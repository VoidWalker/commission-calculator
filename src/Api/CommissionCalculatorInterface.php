<?php
declare(strict_types=1);

namespace Oleksandrsokhan\CommissionCalculator\Api;

interface CommissionCalculatorInterface
{
    const float COMMISSION_RATE_EU = 0.01;
    const float COMMISSION_RATE_NON_EU = 0.02;
    public function calculateCommission(TransactionInterface $transaction): float;
}
