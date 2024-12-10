<?php
declare(strict_types=1);

namespace Oleksandrsokhan\CommissionCalculator;

use Oleksandrsokhan\CommissionCalculator\Api\BinServiceInterface;
use Oleksandrsokhan\CommissionCalculator\Api\CommissionCalculatorInterface;
use Oleksandrsokhan\CommissionCalculator\Api\TransactionInterface;

class EurCommissionCalculator implements CommissionCalculatorInterface
{
    public function __construct(
        private readonly BinServiceInterface $binService,
    ) {
    }

    public function calculateCommission(TransactionInterface $transaction): float
    {
        $isEu = $this->binService->isEuCountryCard($transaction->getBin());
        $amount = $transaction->getAmount();

        return $isEu ? $amount * 0.01 : $amount * 0.02;
    }
}
