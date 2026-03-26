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
            'ticket_id.required_without' => 'Either ticket_id or plate is required.',
            'ticket_id.exists' => 'The ticket does not exist.',
            'plate.required_without' => 'Either ticket_id or plate is required.',
            'exit_time.date' => 'Exit time must be a valid date.',
        ];
    }
}
