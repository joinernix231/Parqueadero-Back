<?php

namespace App\Domain\Services;

class VehicleValidationService
{
    /**
     * Valida el formato de una placa
     * Acepta múltiples formatos comunes de placas:
     * - 3 letras + 3 números (ABC123)
     * - 3 letras + 2 números + 1 letra (ABC12D) - Formato colombiano
     * - 3 números + 3 letras (123ABC)
     * - 2 letras + 3 números + 1 letra (AB123C)
     * - Formatos con guiones
     */
    public function validatePlateFormat(string $plate): bool
    {
        $normalizedPlate = strtoupper(str_replace([' ', '-'], '', trim($plate)));

        // Validar que tenga entre 5 y 7 caracteres
        if (strlen($normalizedPlate) < 5 || strlen($normalizedPlate) > 7) {
            return false;
        }

        // Validar que contenga solo letras y números
        if (!preg_match('/^[A-Z0-9]+$/', $normalizedPlate)) {
            return false;
        }

        // Formato básico: 3 letras + 3 números (ABC123)
        if (preg_match('/^[A-Z]{3}[0-9]{3}$/', $normalizedPlate)) {
            return true;
        }

        // Formato colombiano: 3 letras + 2 números + 1 letra (ABC12D)
        if (preg_match('/^[A-Z]{3}[0-9]{2}[A-Z]$/', $normalizedPlate)) {
            return true;
        }

        // Formato alternativo: 3 números + 3 letras (123ABC)
        if (preg_match('/^[0-9]{3}[A-Z]{3}$/', $normalizedPlate)) {
            return true;
        }

        // Formato: 2 letras + 3 números + 1 letra (AB123C)
        if (preg_match('/^[A-Z]{2}[0-9]{3}[A-Z]$/', $normalizedPlate)) {
            return true;
        }

        // Formato: 3 letras + 3 números + 1 letra (ABC123D)
        if (preg_match('/^[A-Z]{3}[0-9]{3}[A-Z]$/', $normalizedPlate)) {
            return true;
        }

        // Formato: 1 letra + 3 números + 3 letras (A123BCD)
        if (preg_match('/^[A-Z][0-9]{3}[A-Z]{3}$/', $normalizedPlate)) {
            return true;
        }

        // Formato flexible: al menos 2 letras y 2 números en cualquier orden
        // pero con longitud válida (5-7 caracteres)
        $letterCount = preg_match_all('/[A-Z]/', $normalizedPlate);
        $numberCount = preg_match_all('/[0-9]/', $normalizedPlate);
        
        if ($letterCount >= 2 && $numberCount >= 2) {
            return true;
        }

        return false;
    }

    /**
     * Normaliza el formato de una placa
     */
    public function normalizePlate(string $plate): string
    {
        return strtoupper(str_replace([' ', '-'], '', trim($plate)));
    }
}

