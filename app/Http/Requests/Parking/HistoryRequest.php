<?php

namespace App\Http\Requests\Parking;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HistoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'plate' => ['nullable', 'string', 'max:10'],
            'parking_lot_id' => ['nullable', 'integer', 'exists:parking_lots,id'],
            'status' => ['nullable', 'string', Rule::in(['active', 'completed'])],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'date_from.date' => 'La fecha desde debe ser válida.',
            'date_to.date' => 'La fecha hasta debe ser válida.',
            'date_to.after_or_equal' => 'La fecha hasta debe ser posterior o igual a la fecha desde.',
            'parking_lot_id.exists' => 'El estacionamiento no existe.',
            'status.in' => 'El estado debe ser: active o completed.',
        ];
    }
}




