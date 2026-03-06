<?php

namespace App\Http\Requests\ParkingLot;

use Illuminate\Foundation\Http\FormRequest;

class StoreParkingLotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'total_spots' => ['required', 'integer', 'min:1'],
            'hourly_rate_day' => ['required', 'numeric', 'min:0'],
            'hourly_rate_night' => ['required', 'numeric', 'min:0'],
            'day_start_time' => ['required', 'string', 'regex:/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/'],
            'day_end_time' => ['required', 'string', 'regex:/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'address.required' => 'La dirección es obligatoria.',
            'total_spots.required' => 'El número total de espacios es obligatorio.',
            'total_spots.min' => 'Debe haber al menos 1 espacio.',
            'hourly_rate_day.required' => 'La tarifa horaria diurna es obligatoria.',
            'hourly_rate_day.min' => 'La tarifa horaria diurna debe ser mayor o igual a 0.',
            'hourly_rate_night.required' => 'La tarifa horaria nocturna es obligatoria.',
            'hourly_rate_night.min' => 'La tarifa horaria nocturna debe ser mayor o igual a 0.',
            'day_start_time.required' => 'La hora de inicio del día es obligatoria.',
            'day_start_time.regex' => 'El formato de hora no es válido (HH:MM).',
            'day_end_time.required' => 'La hora de fin del día es obligatoria.',
            'day_end_time.regex' => 'El formato de hora no es válido (HH:MM).',
        ];
    }
}

