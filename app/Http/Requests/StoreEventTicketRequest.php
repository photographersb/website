<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreEventTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'event_id' => 'required|exists:events,id',
            'title' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'sales_start_datetime' => 'nullable|date',
            'sales_end_datetime' => 'nullable|date|after_or_equal:sales_start_datetime',
            'is_active' => 'boolean',
        ];
    }
}
