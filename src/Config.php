<?php
declare(strict_types=1);

namespace Oleksandrsokhan\CommissionCalculator;

class Config implements Api\ConfigInterface
{
    public function getBaseCurrencyCommission(): float
    {
        return 0.01;
    }

    public function getForeignCurrencyCommission(): float
    {
        return 0.02;
    }
}
