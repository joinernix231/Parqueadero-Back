<?php

namespace App\Domain\Services;

use App\Domain\Entities\ParkingLot;
use App\Domain\Entities\ParkingTicket;
use Carbon\Carbon;

class PricingService
{
    public function __construct(private DateTimeService $dateTimeService) {}

    public function calculateTotalHours(ParkingTicket $ticket, ?string $atTime = null): float
    {
        $tz = $this->dateTimeService->getTimezone();
        $exit = $atTime ?? $ticket->getExitTime() ?? Carbon::now($tz)->format('Y-m-d H:i:s');

        $entry = Carbon::parse($ticket->getEntryTime(), $tz);
        $exitCarbon = Carbon::parse($exit, $tz);

        if ($exitCarbon->isBefore($entry)) {
            throw new \InvalidArgumentException('Exit time cannot be before entry time');
        }

        return round($entry->diffInHours($exitCarbon, true), 2);
    }

    public function calculatePrice(ParkingTicket $ticket, ParkingLot $lot): float
    {
        if ($ticket->getExitTime() === null) {
            throw new \RuntimeException('Cannot calculate price without exit time');
        }

        $tz = $this->dateTimeService->getTimezone();
        $entry = Carbon::parse($ticket->getEntryTime(), $tz);
        $exit = Carbon::parse($ticket->getExitTime(), $tz);

        if ($exit->isBefore($entry)) {
            throw new \InvalidArgumentException('Exit time cannot be before entry time');
        }

        $price = 0.0;
        $current = $entry->copy();

        while ($current->isBefore($exit)) {
            $isDayTime = $lot->isDayTime($current->format('H:i'));
            $nextChange = $this->getNextRateChange($current, $lot, $tz);
            $segmentEnd = $nextChange->isBefore($exit) ? $nextChange : $exit;

            $rate = $isDayTime ? $lot->getHourlyRateDay() : $lot->getHourlyRateNight();
            $price += $current->diffInHours($segmentEnd, true) * $rate;

            $current = $segmentEnd;
        }

        return round($price, 2);
    }

    private function getNextRateChange(Carbon $current, ParkingLot $lot, string $tz): Carbon
    {
        $date = $current->format('Y-m-d');
        $dayStart = Carbon::parse("{$date} {$lot->getDayStartTime()}", $tz);
        $dayEnd = Carbon::parse("{$date} {$lot->getDayEndTime()}", $tz);

        if ($dayEnd->isBefore($dayStart)) {
            $dayEnd->addDay();
        }

        if ($lot->isDayTime($current->format('H:i'))) {
            return $dayEnd;
        }

        // Night: next change is start of next day period
        return Carbon::parse("{$date} {$lot->getDayStartTime()}", $tz)->addDay();
    }
}
