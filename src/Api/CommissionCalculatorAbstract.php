<?php
declare(strict_types=1);

namespace Oleksandrsokhan\CommissionCalculator\Api;

abstract class CommissionCalculatorAbstract implements CommissionCalculatorInterface
{
    public function __construct(
        private readonly BinServiceInterface $binService,
        private readonly ConfigInterface $config,
    ) {
    }

    public function calculateCommission(TransactionInterface $transaction): float
    {
        $isEu = $this->binService->isEuCountryCard($transaction->getBin());
        $amount = $this->getAmount($transaction);

        return $isEu ? $amount * $this->config->getCommissionRateEu() : $amount * $this->config->getCommissionRateNonEu();
    }

    abstract protected function getAmount(TransactionInterface $transaction): float;
}
