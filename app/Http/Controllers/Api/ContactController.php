<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ContactMessageRequest;
use App\Http\Requests\SponsorInquiryRequest;
use App\Models\ContactMessage;
use Illuminate\Http\JsonResponse;

class ContactController extends Controller
{
    public function sponsorInquiry(SponsorInquiryRequest $request): JsonResponse
    {
        $validated = $request->validated();

        ContactMessage::create([
            'type' => 'sponsorship',
            'name' => $validated['contact_person'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'subject' => 'Sponsorship Inquiry from ' . $validated['company_name'],
            'message' => "Company: {$validated['company_name']}\n\n{$validated['message']}",
            'status' => 'pending',
            'user_id' => auth()->id(),
        ]);

        return response()->json(['message' => 'Inquiry submitted successfully']);
    }

    public function contact(ContactMessageRequest $request): JsonResponse
    {
        $validated = $request->validated();

        ContactMessage::create([
            'type' => 'contact',
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'status' => 'pending',
            'user_id' => auth()->id(),
        ]);

        return response()->json(['message' => 'Message sent successfully']);
    }
}
