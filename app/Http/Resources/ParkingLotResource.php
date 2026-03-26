<?php

namespace App\Http\Resources;

use App\Domain\Entities\ParkingLot;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ParkingLotResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var ParkingLot $lot */
        $lot = $this->resource instanceof ParkingLot ? $this->resource : null;

        if (! $lot) {
            return [];
        }

        return [
            'id' => $lot->getId(),
            'name' => $lot->getName(),
            'address' => $lot->getAddress(),
            'total_spots' => $lot->getTotalSpots(),
            'hourly_rate_day' => $lot->getHourlyRateDay(),
            'hourly_rate_night' => $lot->getHourlyRateNight(),
            'day_start_time' => $lot->getDayStartTime(),
            'day_end_time' => $lot->getDayEndTime(),
            'is_active' => $lot->isActive(),
            'created_at' => $lot->getCreatedAt(),
        ];
    }
}
