<?php

namespace App\Http\Requests\Parking;

use Illuminate\Foundation\Http\FormRequest;

class ExitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ticket_id' => ['required_without:plate', 'integer', 'exists:parking_tickets,id'],
            'plate' => ['required_without:ticket_id', 'string', 'max:10'],
            'exit_time' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'ticket_id.required_without' => 'Se requiere ticket_id o plate.',
            'ticket_id.exists' => 'El ticket no existe.',
            'plate.required_without' => 'Se requiere ticket_id o plate.',
            'exit_time.date' => 'La fecha de salida debe ser válida.',
        ];
    }
}




