<?php

namespace App\Http\Requests\Parking;

use Illuminate\Foundation\Http\FormRequest;

class CurrentTicketsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Convertir string "true"/"false" a booleanos para query params
        if ($this->has('paginate')) {
            $paginate = $this->input('paginate');
            if (is_string($paginate)) {
                $this->merge([
                    'paginate' => filter_var($paginate, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false
                ]);
            }
        }
    }

    public function rules(): array
    {
        return [
            'parking_lot_id' => ['nullable', 'integer', 'exists:parking_lots,id'],
            'search' => ['nullable', 'string', 'max:100'],
            'filters' => ['nullable', 'string'],
            'paginate' => ['nullable', 'boolean'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'parking_lot_id.exists' => 'El estacionamiento no existe.',
            'search.max' => 'La búsqueda no puede superar los 100 caracteres.',
        ];
    }

    public function getParkingLotId(): ?int
    {
        $parkingLotId = $this->input('parking_lot_id');

        return $parkingLotId !== null ? (int) $parkingLotId : null;
    }

    public function getSearch(): ?string
    {
        $search = trim((string) $this->input('search', ''));

        return $search !== '' ? $search : null;
    }

    public function getFilters(): array|string|null
    {
        return $this->input('filters');
    }

    public function getPerPage(): int
    {
        return (int) $this->input('per_page', 15);
    }

    public function shouldPaginate(): bool
    {
        return $this->boolean('paginate') || $this->filled('page') || $this->filled('per_page');
    }
}

