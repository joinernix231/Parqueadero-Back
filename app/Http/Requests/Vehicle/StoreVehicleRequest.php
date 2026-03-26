<?php

namespace App\Http\Requests\Vehicle;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'plate' => [
                'required',
                'string',
                'max:10',
                Rule::unique('vehicles', 'plate'),
                'regex:/^[A-Z0-9-]{3,10}$/i',
            ],
            'owner_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'vehicle_type' => ['required', 'string', Rule::in(['car', 'motorcycle', 'truck'])],
        ];
    }

    public function messages(): array
    {
        return [
            'plate.required' => 'License plate is required.',
            'plate.unique' => 'This license plate is already registered.',
            'plate.regex' => 'License plate format is invalid.',
            'owner_name.required' => 'Owner name is required.',
            'phone.required' => 'Phone is required.',
            'vehicle_type.required' => 'Vehicle type is required.',
            'vehicle_type.in' => 'Vehicle type must be one of: car, motorcycle, truck.',
        ];
    }
}
