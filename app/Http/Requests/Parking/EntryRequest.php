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
            'vehicle_id.required_without' => 'Either vehicle_id or vehicle_data is required.',
            'vehicle_id.exists' => 'The vehicle does not exist.',
            'vehicle_data.required_without' => 'Vehicle data is required when vehicle_id is not provided.',
            'vehicle_data.plate.required_with' => 'License plate is required inside vehicle_data.',
            'vehicle_data.owner_name.required_with' => 'Owner name is required.',
            'vehicle_data.phone.required_with' => 'Phone is required.',
            'vehicle_data.vehicle_type.required_with' => 'Vehicle type is required.',
            'vehicle_data.vehicle_type.in' => 'Vehicle type must be one of: car, motorcycle, truck.',
            'parking_lot_id.required' => 'Parking lot is required.',
            'parking_lot_id.exists' => 'The parking lot does not exist.',
            'parking_spot_id.required' => 'Parking spot is required.',
            'parking_spot_id.exists' => 'The parking spot does not exist.',
            'entry_time.date' => 'Entry time must be a valid date.',
        ];
    }
}
