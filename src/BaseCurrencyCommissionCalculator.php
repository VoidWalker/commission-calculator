<?php
declare(strict_types=1);

namespace Oleksandrsokhan\CommissionCalculator;

use Oleksandrsokhan\CommissionCalculator\Api\CommissionCalculatorAbstract;
use Oleksandrsokhan\CommissionCalculator\Api\TransactionInterface;

class BaseCurrencyCommissionCalculator extends CommissionCalculatorAbstract
{
    protected function getAmount(TransactionInterface $transaction): float
    {
        return $transaction->getAmount();
    }
}
