<?php
declare(strict_types=1);

namespace Oleksandrsokhan\CommissionCalculator;

use Oleksandrsokhan\CommissionCalculator\Api\BinServiceInterface;
use Oleksandrsokhan\CommissionCalculator\Api\TransactionInterface;

class DefaultCommissionCalculator implements Api\CommissionCalculatorInterface
{
    public function __construct(
        private readonly BinServiceInterface $binService,
        private readonly CurrencyRateService $currencyRateService
    ) {
    }

    public function calculateCommission(TransactionInterface $transaction): float
    {
        $isEu = $this->binService->isEuCountryCard($transaction->getBin());
        $rate = $this->currencyRateService->getRate($transaction->getCurrency());

        $amount = $rate == 0 ? $transaction->getAmount() : $transaction->getAmount() / $rate;

        return $isEu ? $amount * self::COMMISSION_RATE_EU : $amount * self::COMMISSION_RATE_NON_EU;
    }
}
