<?php

if (!function_exists('format_amount')) {
    /**
     * Formate un montant en FCFA.
     */
    function format_amount(int|float|null $amount, bool $withCurrency = true): string
    {
        $formatted = number_format($amount ?? 0, 0, ',', ' ');
        return $withCurrency ? "{$formatted} FCFA" : $formatted;
    }
}
