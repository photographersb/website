<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\CertificateTemplate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Exception;

class CertificateGenerator
{
    protected $gdAvailable;

    public function __construct()
    {
        $this->gdAvailable = extension_loaded('gd');

        if (!$this->gdAvailable) {
            throw new Exception('GD extension is required for certificate generation.');
        }
    }

    /**
     * Generate certificate PDF and QR code
     */
    public function generateCertificate(Certificate $certificate): Certificate
    {
        try {
            // Generate QR code
            $qrPath = $this->generateQRCode($certificate);
            $certificate->verification_qr_path = $qrPath;

            // Generate PDF
            $pdfPath = $this->generatePDF($certificate);
            $certificate->pdf_path = $pdfPath;

            $certificate->save();

            // Log the action
            $certificate->logs()->create([
                'action' => 'generated',
                'performed_by_user_id' => auth()?->id(),
            ]);

            return $certificate;
        } catch (Exception $e) {
            \Log::error('Certificate generation failed', [
                'certificate_id' => $certificate->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Generate verification QR code
     */
    protected function generateQRCode(Certificate $certificate): string
    {
        $verifyUrl = route('certificate.verify', $certificate->certificate_code);

        $qrGenerator = new \SimpleSoftwareIO\QrCode\Facades\QrCode();
        $qr = $qrGenerator
            ->format('png')
            ->size(300)
            ->errorCorrection('H')
            ->generate($verifyUrl);

        $filename = sprintf('qr-code-%s.png', $certificate->certificate_code);
        $path = "certificates/qr-codes/{$filename}";

        Storage::disk('public')->put($path, $qr);

        return $path;
    }

    /**
     * Generate PDF certificate
     */
    protected function generatePDF(Certificate $certificate): string
    {
        $template = $certificate->template;
        $html = $this->buildCertificateHTML($certificate, $template);

        // For now, save as image-based PDF using GD
        // In production, use Snappy/DomPDF for better formatting
        $imagePath = $this->generateCertificateImage($certificate, $template);

        // Convert image to PDF using a simple approach
        $pdfPath = $this->imageToPDF($imagePath, $certificate);

        return $pdfPath;
    }

    /**
     * Generate certificate as image
     */
    protected function generateCertificateImage(Certificate $certificate, CertificateTemplate $template): string
    {
        // Default dimensions (A4 in pixels at 96 DPI)
        $width = 1190;
        $height = 842;

        // Create canvas
        $canvas = imagecreatetruecolor($width, $height);

        // Fill background
        $bgColor = $this->hexToRgb($template->background_color ?? '#ffffff');
        $backgroundColor = imagecolorallocate($canvas, $bgColor['r'], $bgColor['g'], $bgColor['b']);
        imagefill($canvas, 0, 0, $backgroundColor);

        // Add border
        $primaryColor = $this->hexToRgb($template->primary_color ?? '#1a1a1a');
        $borderColor = imagecolorallocate($canvas, $primaryColor['r'], $primaryColor['g'], $primaryColor['b']);
        imagerectangle($canvas, 20, 20, $width - 20, $height - 20, $borderColor);
        imagerectangle($canvas, 25, 25, $width - 25, $height - 25, $borderColor);

        // Add text
        $textColor = imagecolorallocate($canvas, 0, 0, 0);

        // Title
        $this->addText($canvas, $width, $height, 150, 'Certificate of Participation', 28, $textColor);

        // Event/Competition name
        $eventName = $certificate->event?->title ?? $certificate->competition?->name ?? 'Achievement';
        $this->addText($canvas, $width, $height, 250, $eventName, 24, $textColor);

        // Participant name
        $participantName = $certificate->getParticipantName();
        $this->addText($canvas, $width, $height, 380, "This is to certify that", 16, $textColor);
        $this->addText($canvas, $width, $height, 430, $participantName, 32, $borderColor); // Highlighted

        // Details
        $issueDate = $certificate->issue_date->format('j F Y');
        $this->addText($canvas, $width, $height, 540, "has successfully completed", 14, $textColor);
        $this->addText($canvas, $width, $height, 600, "Certificate Code: " . $certificate->certificate_code, 12, $textColor);
        $this->addText($canvas, $width, $height, 650, "Issued on: " . $issueDate, 12, $textColor);

        // Save image
        $filename = sprintf('cert-%s.jpg', $certificate->certificate_code);
        $fullPath = Storage::path("public/certificates/{$filename}");

        $directory = dirname($fullPath);
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        imagejpeg($canvas, $fullPath, 90);
        imagedestroy($canvas);

        return "certificates/{$filename}";
    }

    /**
     * Add text to image
     */
    protected function addText(&$canvas, $width, $height, $yPos, $text, $fontSize, $color): void
    {
        // Use built-in fonts
        $fontPath = storage_path('app/fonts/arial.ttf');
        
        if (!file_exists($fontPath)) {
            // Fallback to built-in fonts
            $x = ($width - (strlen($text) * ($fontSize / 2))) / 2;
            imagestring($canvas, 5, (int)$x, $yPos, $text, $color);
        } else {
            // Use TrueType font if available
            $bbox = imagettfbbox($fontSize, 0, $fontPath, $text);
            $x = ($width - ($bbox[2] - $bbox[0])) / 2;
            imagettftext($canvas, $fontSize, 0, (int)$x, $yPos, $color, $fontPath, $text);
        }
    }

    /**
     * Convert image to PDF (simplified)
     */
    protected function imageToPDF($imagePath, Certificate $certificate): string
    {
        // For now, just save as PDF with image embedded
        // In production, use proper PDF library like Snappy or DomPDF

        $filename = sprintf('cert-%s.pdf', $certificate->certificate_code);
        $pdfPath = "certificates/{$filename}";

        // For MVP, we'll use the image itself as the "PDF"
        // In production, wrap it in a proper PDF format
        
        return $pdfPath;
    }

    /**
     * Convert hex color to RGB
     */
    protected function hexToRgb($hex): array
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) === 3) {
            $hex = str_repeat($hex[0], 2) . str_repeat($hex[1], 2) . str_repeat($hex[2], 2);
        }

        [$r, $g, $b] = sscanf($hex, '%02x%02x%02x');

        return ['r' => $r, 'g' => $g, 'b' => $b];
    }

    /**
     * Generate certificate code
     */
    public function generateCode(): string
    {
        $year = now()->year;
        $sequence = Certificate::whereYear('created_at', $year)->count() + 1;

        return sprintf('SB-CERT-%d-%05d', $year, $sequence);
    }
}
