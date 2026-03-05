<?php

namespace App\Http\Requests\Parking;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vehicle_id' => ['required_without:vehicle_data', 'integer', 'exists:vehicles,id'],
            'vehicle_data' => ['required_without:vehicle_id', 'array'],
            'vehicle_data.plate' => ['required_with:vehicle_data', 'string', 'max:10'],
            'vehicle_data.owner_name' => ['required_with:vehicle_data', 'string', 'max:255'],
            'vehicle_data.phone' => ['required_with:vehicle_data', 'string', 'max:20'],
            'vehicle_data.vehicle_type' => ['required_with:vehicle_data', 'string', Rule::in(['car', 'motorcycle', 'truck'])],
            'parking_lot_id' => ['required', 'integer', 'exists:parking_lots,id'],
            'parking_spot_id' => ['required', 'integer', 'exists:parking_spots,id'],
            'entry_time' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'vehicle_id.required_without' => 'Se requiere vehicle_id o vehicle_data.',
            'vehicle_id.exists' => 'El vehículo no existe.',
            'vehicle_data.required_without' => 'Los datos del vehículo son requeridos cuando no se proporciona vehicle_id.',
            'vehicle_data.plate.required_with' => 'La placa es obligatoria dentro de vehicle_data.',
            'vehicle_data.owner_name.required_with' => 'El nombre del propietario es obligatorio.',
            'vehicle_data.phone.required_with' => 'El teléfono es obligatorio.',
            'vehicle_data.vehicle_type.required_with' => 'El tipo de vehículo es obligatorio.',
            'vehicle_data.vehicle_type.in' => 'El tipo de vehículo debe ser: car, motorcycle o truck.',
            'parking_lot_id.required' => 'El estacionamiento es obligatorio.',
            'parking_lot_id.exists' => 'El estacionamiento no existe.',
            'parking_spot_id.required' => 'El espacio es obligatorio.',
            'parking_spot_id.exists' => 'El espacio no existe.',
            'entry_time.date' => 'La fecha de entrada debe ser válida.',
        ];
    }
}

