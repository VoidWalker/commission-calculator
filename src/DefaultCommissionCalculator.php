<?php
declare(strict_types=1);

namespace Oleksandrsokhan\CommissionCalculator;

use Oleksandrsokhan\CommissionCalculator\Api\BinServiceInterface;
use Oleksandrsokhan\CommissionCalculator\Api\CommissionCalculatorAbstract;
use Oleksandrsokhan\CommissionCalculator\Api\ConfigInterface;
use Oleksandrsokhan\CommissionCalculator\Api\TransactionInterface;

class DefaultCommissionCalculator extends CommissionCalculatorAbstract
{
    public function __construct(
        BinServiceInterface $binService,
        ConfigInterface $config,
        private readonly CurrencyRateService $currencyRateService,
    ) {
        parent::__construct($binService, $config);
    }

    protected function getAmount(TransactionInterface $transaction): float
    {
        $rate = $this->currencyRateService->getRate($transaction->getCurrency());

        return $rate == 0 ? $transaction->getAmount() : $transaction->getAmount() / $rate;
    }
}
