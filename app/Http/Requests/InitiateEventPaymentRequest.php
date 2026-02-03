<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class InitiateEventPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'event_id' => 'required|exists:events,id',
            'ticket_id' => 'required|exists:event_tickets,id',
            'quantity' => 'required|integer|min:1',
        ];
    }
}
