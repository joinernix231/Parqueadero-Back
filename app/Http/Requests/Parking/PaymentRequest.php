<?php

namespace App\Http\Requests\Parking;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ticket_id' => ['required', 'integer', 'exists:parking_tickets,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['required', 'string', Rule::in(['cash', 'card', 'transfer'])],
        ];
    }

    public function messages(): array
    {
        return [
            'ticket_id.required' => 'Ticket is required.',
            'ticket_id.exists' => 'The ticket does not exist.',
            'amount.required' => 'Amount is required.',
            'amount.numeric' => 'Amount must be numeric.',
            'amount.min' => 'Amount cannot be negative.',
            'payment_method.required' => 'Payment method is required.',
            'payment_method.in' => 'Payment method must be one of: cash, card, transfer.',
        ];
    }
}
