<?php
declare(strict_types=1);

namespace Oleksandrsokhan\CommissionCalculator\Api;

interface ConfigInterface
{
    public function getCommissionRateEu(): float;

    public function getCommissionRateNonEu(): float;

    public function getBaseCurrency(): string;
}
