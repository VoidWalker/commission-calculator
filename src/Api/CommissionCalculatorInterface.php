<?php
declare(strict_types=1);

namespace Oleksandrsokhan\CommissionCalculator\Api;

interface CommissionCalculatorInterface
{
    public function calculateCommission(TransactionInterface $transaction): float;
}
