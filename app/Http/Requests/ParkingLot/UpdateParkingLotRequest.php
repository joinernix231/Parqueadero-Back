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
            'name.max' => 'Name may not exceed 255 characters.',
            'address.max' => 'Address may not exceed 500 characters.',
            'total_spots.min' => 'There must be at least one spot.',
            'hourly_rate_day.min' => 'Day hourly rate must be greater than or equal to 0.',
            'hourly_rate_night.min' => 'Night hourly rate must be greater than or equal to 0.',
            'day_start_time.regex' => 'Invalid time format (use HH:MM).',
            'day_end_time.regex' => 'Invalid time format (use HH:MM).',
        ];
    }
}
