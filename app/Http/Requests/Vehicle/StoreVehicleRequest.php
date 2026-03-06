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
            'plate.required' => 'La placa es obligatoria.',
            'plate.unique' => 'La placa ya está registrada.',
            'plate.regex' => 'El formato de la placa no es válido.',
            'owner_name.required' => 'El nombre del propietario es obligatorio.',
            'phone.required' => 'El teléfono es obligatorio.',
            'vehicle_type.required' => 'El tipo de vehículo es obligatorio.',
            'vehicle_type.in' => 'El tipo de vehículo debe ser: car, motorcycle o truck.',
        ];
    }
}





