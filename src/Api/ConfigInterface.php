<?php
declare(strict_types=1);

namespace Oleksandrsokhan\CommissionCalculator\Api;

interface ConfigInterface
{
    public function getBaseCurrencyCommission(): float;

    public function getForeignCurrencyCommission(): float;
}
