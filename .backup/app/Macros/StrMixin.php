<?php

namespace App\Macros;

use Closure;
use NumberFormatter;

class StrMixin
{
    public function money(): Closure
    {
        return function (float $value, int $decimals = 0, string $currency = 'EUR'): string {
            $formatter = new NumberFormatter(app()->getLocale(), NumberFormatter::CURRENCY);
            $formatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, $decimals);

            return $formatter->formatCurrency($value, $currency);
        };
    }
}
