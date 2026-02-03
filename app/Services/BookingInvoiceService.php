<?php

namespace App\Services;

use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Exception;

class BookingInvoiceService
{
    /**
     * Generate invoice PDF for a booking
     */
    public function generateInvoice(Booking $booking): string
    {
        try {
            // Ensure we have fresh data
            $booking->load('client', 'photographer', 'quote', 'inquiry');

            // Create a unique invoice filename
            $invoiceNumber = 'INV-' . date('Ym') . '-' . str_pad($booking->id, 5, '0', STR_PAD_LEFT);
            $fileName = 'booking_' . $booking->id . '_' . Str::random(8) . '.pdf';
            $path = 'invoices/' . $fileName;

            // Generate PDF
            $pdf = Pdf::loadView('invoices.booking-invoice', [
                'booking' => $booking,
                'invoiceNumber' => $invoiceNumber,
                'invoiceDate' => now()->format('M d, Y'),
                'dueDate' => $booking->event_date->format('M d, Y'),
                'companyName' => config('app.name', 'Photographar'),
                'companyAddress' => 'Bangladesh',
                'companyPhone' => config('app.phone'),
                'companyEmail' => config('mail.from.address'),
            ])
            ->setPaper('a4')
            ->setOption('margin-top', 10)
            ->setOption('margin-bottom', 10)
            ->setOption('margin-left', 10)
            ->setOption('margin-right', 10);

            // Store PDF in storage/invoices/
            Storage::put($path, $pdf->output());

            // Log invoice generation
            activity()
                ->performedOn($booking)
                ->causedBy(auth()->user() ?? $booking->client)
                ->event('invoice_generated')
                ->withProperties([
                    'invoice_number' => $invoiceNumber,
                    'file_path' => $path,
                    'amount' => $booking->total_amount,
                ])
                ->log('Invoice generated for booking');

            return $path;
        } catch (Exception $e) {
            throw new Exception('Failed to generate invoice: ' . $e->getMessage());
        }
    }

    /**
     * Generate all pending invoices for bookings
     */
    public function generateAllPendingInvoices()
    {
        $bookings = Booking::where('payment_status', 'pending')
            ->where('status', 'confirmed')
            ->whereNull('invoice_generated_at')
            ->limit(50)
            ->get();

        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        foreach ($bookings as $booking) {
            try {
                $this->generateInvoice($booking);
                $booking->update(['invoice_generated_at' => now()]);
                $results['success']++;
            } catch (Exception $e) {
                $results['failed']++;
                $results['errors'][] = [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * Get invoice download URL for a booking
     */
    public function getDownloadUrl(Booking $booking): ?string
    {
        // Check if invoice exists in storage
        $invoices = Storage::files('invoices/');
        $bookingInvoice = collect($invoices)->first(function ($file) use ($booking) {
            return strpos($file, 'booking_' . $booking->id . '_') === 7; // Remove 'invoices/'
        });

        if ($bookingInvoice) {
            return Storage::url($bookingInvoice);
        }

        return null;
    }

    /**
     * Delete invoice file
     */
    public function deleteInvoice(string $filePath): bool
    {
        if (Storage::exists($filePath)) {
            Storage::delete($filePath);
            return true;
        }

        return false;
    }

    /**
     * Get invoice file path for a booking
     */
    public function getInvoiceFilePath(Booking $booking): ?string
    {
        $invoices = Storage::files('invoices/');
        $bookingInvoice = collect($invoices)->first(function ($file) use ($booking) {
            return strpos($file, 'booking_' . $booking->id . '_') === 7;
        });

        return $bookingInvoice;
    }

    /**
     * Calculate invoice totals with tax and fees
     */
    public function calculateTotals(Booking $booking): array
    {
        $subtotal = $booking->total_amount;
        $taxRate = config('invoicing.tax_rate', 0.15); // 15% VAT for Bangladesh
        $tax = round($subtotal * $taxRate, 2);
        $total = $subtotal + $tax;

        return [
            'subtotal' => $subtotal,
            'tax_rate' => $taxRate * 100,
            'tax' => $tax,
            'total' => $total,
            'platformFee' => round($subtotal * 0.10, 2), // 10% platform fee
            'photographerReceives' => $subtotal - round($subtotal * 0.10, 2),
        ];
    }

    /**
     * Email invoice to booking parties
     */
    public function emailInvoice(Booking $booking, string $filePath): bool
    {
        try {
            $booking->load('client', 'photographer');

            // Send to client
            if ($booking->client?->email) {
                \Mail::send('emails.invoice-sent', [
                    'booking' => $booking,
                    'recipientName' => $booking->client->name,
                ], function ($message) use ($booking, $filePath) {
                    $message->to($booking->client->email)
                        ->subject('Your Photography Service Invoice - ' . $booking->uuid)
                        ->attach(Storage::path($filePath), [
                            'as' => 'invoice_' . $booking->id . '.pdf',
                            'mime' => 'application/pdf',
                        ]);
                });
            }

            // Send to photographer
            if ($booking->photographer?->user?->email) {
                \Mail::send('emails.invoice-sent', [
                    'booking' => $booking,
                    'recipientName' => $booking->photographer->user->name,
                    'isPrintable' => true,
                ], function ($message) use ($booking, $filePath) {
                    $message->to($booking->photographer->user->email)
                        ->subject('Photography Service Invoice - ' . $booking->uuid)
                        ->attach(Storage::path($filePath), [
                            'as' => 'invoice_' . $booking->id . '.pdf',
                            'mime' => 'application/pdf',
                        ]);
                });
            }

            return true;
        } catch (Exception $e) {
            \Log::error('Failed to email invoice: ' . $e->getMessage());
            return false;
        }
    }
}
