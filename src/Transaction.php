<?php
declare(strict_types=1);

namespace Oleksandrsokhan\CommissionCalculator;

class Transaction implements Api\TransactionInterface
{
    public function __construct(
        private readonly string $bin,
        private readonly float $amount,
        private readonly string $currency
    ) {
    }

    public function getBin(): string
    {
        return $this->bin;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }
}
