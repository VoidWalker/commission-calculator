<?php
declare(strict_types=1);

namespace Oleksandrsokhan\CommissionCalculator\Api;

interface CurrencyRateServiceInterface
{
    public function getRate(string $currency): float;
}
