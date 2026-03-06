<?php

namespace App\Http\Requests\ParkingLot;

use Illuminate\Foundation\Http\FormRequest;

class UpdateParkingLotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'address' => ['sometimes', 'string', 'max:500'],
            'total_spots' => ['sometimes', 'integer', 'min:1'],
            'hourly_rate_day' => ['sometimes', 'numeric', 'min:0'],
            'hourly_rate_night' => ['sometimes', 'numeric', 'min:0'],
            'day_start_time' => ['sometimes', 'string', 'regex:/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/'],
            'day_end_time' => ['sometimes', 'string', 'regex:/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.max' => 'El nombre no puede exceder 255 caracteres.',
            'address.max' => 'La dirección no puede exceder 500 caracteres.',
            'total_spots.min' => 'Debe haber al menos 1 espacio.',
            'hourly_rate_day.min' => 'La tarifa horaria diurna debe ser mayor o igual a 0.',
            'hourly_rate_night.min' => 'La tarifa horaria nocturna debe ser mayor o igual a 0.',
            'day_start_time.regex' => 'El formato de hora no es válido (HH:MM).',
            'day_end_time.regex' => 'El formato de hora no es válido (HH:MM).',
        ];
    }
}

