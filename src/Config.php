<?php
declare(strict_types=1);

namespace Oleksandrsokhan\CommissionCalculator;

class Config implements Api\ConfigInterface
{
    public function getCommissionRateEu(): float
    {
        return 0.01;
    }

    public function getCommissionRateNonEu(): float
    {
        return 0.02;
    }

    public function getBaseCurrency(): string
    {
        return 'EUR';
    }
}
