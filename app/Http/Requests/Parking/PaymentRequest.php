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
            'ticket_id.required' => 'El ticket es obligatorio.',
            'ticket_id.exists' => 'El ticket no existe.',
            'amount.required' => 'El monto es obligatorio.',
            'amount.numeric' => 'El monto debe ser numérico.',
            'amount.min' => 'El monto no puede ser negativo.',
            'payment_method.required' => 'El método de pago es obligatorio.',
            'payment_method.in' => 'El método de pago debe ser: cash, card o transfer.',
        ];
    }
}




