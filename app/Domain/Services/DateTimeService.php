<?php

namespace App\Domain\Services;

use Carbon\Carbon;

class DateTimeService
{
    /**
     * Obtiene la zona horaria configurada en la aplicación
     */
    public function getTimezone(): string
    {
        return config('app.timezone', 'America/Bogota');
    }

    /**
     * Normaliza una fecha UTC del frontend a la zona horaria local
     * 
     * @param string|null $dateTime Fecha en formato ISO 8601 UTC (ej: "2026-03-05T21:26:10.970Z") o null
     * @return string Fecha normalizada en formato 'Y-m-d H:i:s' en la zona horaria local
     */
    public function normalizeFromUtc(?string $dateTime): string
    {
        if (empty($dateTime)) {
            return now($this->getTimezone())->format('Y-m-d H:i:s');
        }

        try {
            $carbon = Carbon::parse($dateTime);

            // Fechas con zona UTC explícita se convierten; sin zona horaria se tratan como locales.
            if (str_ends_with($dateTime, 'Z') || str_contains($dateTime, '+00:00')) {
                $carbon = $carbon->setTimezone($this->getTimezone());
            }

            return $carbon->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return now($this->getTimezone())->format('Y-m-d H:i:s');
        }
    }

    /**
     * Convierte una fecha local a formato para guardar en la base de datos
     * 
     * @param string|null $dateTime Fecha en cualquier formato
     * @return string Fecha en formato 'Y-m-d H:i:s' en la zona horaria local
     */
    public function normalizeToLocal(?string $dateTime): string
    {
        if (empty($dateTime)) {
            return now($this->getTimezone())->format('Y-m-d H:i:s');
        }

        try {
            $carbon = Carbon::parse($dateTime, $this->getTimezone());
            return $carbon->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return now($this->getTimezone())->format('Y-m-d H:i:s');
        }
    }

    /**
     * Crea una instancia Carbon desde una fecha string en la zona horaria local
     * 
     * @param string $dateTime Fecha en formato 'Y-m-d H:i:s'
     * @return Carbon Instancia de Carbon en la zona horaria local
     */
    public function createCarbonFromLocal(string $dateTime): Carbon
    {
        return Carbon::parse($dateTime, $this->getTimezone());
    }

    /**
     * Obtiene la fecha y hora actual en la zona horaria local
     * 
     * @return string Fecha actual en formato 'Y-m-d H:i:s'
     */
    public function now(): string
    {
        return now($this->getTimezone())->format('Y-m-d H:i:s');
    }

    /**
     * Compara dos fechas y retorna true si la primera es anterior a la segunda
     * 
     * @param string $dateTime1 Primera fecha
     * @param string $dateTime2 Segunda fecha
     * @return bool True si dateTime1 < dateTime2
     */
    public function isBefore(string $dateTime1, string $dateTime2): bool
    {
        $carbon1 = $this->createCarbonFromLocal($dateTime1);
        $carbon2 = $this->createCarbonFromLocal($dateTime2);
        
        return $carbon1->isBefore($carbon2);
    }

    /**
     * Calcula la diferencia en horas entre dos fechas
     * 
     * @param string $dateTime1 Primera fecha
     * @param string $dateTime2 Segunda fecha
     * @return float Diferencia en horas
     */
    public function diffInHours(string $dateTime1, string $dateTime2): float
    {
        $carbon1 = $this->createCarbonFromLocal($dateTime1);
        $carbon2 = $this->createCarbonFromLocal($dateTime2);
        
        return abs($carbon1->diffInHours($carbon2, true));
    }
}


