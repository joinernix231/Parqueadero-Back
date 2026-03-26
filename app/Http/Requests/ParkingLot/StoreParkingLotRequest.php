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
            'name.required' => 'Name is required.',
            'address.required' => 'Address is required.',
            'total_spots.required' => 'Total number of spots is required.',
            'total_spots.min' => 'There must be at least one spot.',
            'hourly_rate_day.required' => 'Day hourly rate is required.',
            'hourly_rate_day.min' => 'Day hourly rate must be greater than or equal to 0.',
            'hourly_rate_night.required' => 'Night hourly rate is required.',
            'hourly_rate_night.min' => 'Night hourly rate must be greater than or equal to 0.',
            'day_start_time.required' => 'Day start time is required.',
            'day_start_time.regex' => 'Invalid time format (use HH:MM).',
            'day_end_time.required' => 'Day end time is required.',
            'day_end_time.regex' => 'Invalid time format (use HH:MM).',
        ];
    }
}
