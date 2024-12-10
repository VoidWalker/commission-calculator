<?php
declare(strict_types=1);

namespace Oleksandrsokhan\CommissionCalculator\Api;

interface TransactionInterface
{
    public function getBin(): string;

    public function getCurrency(): string;

    public function getAmount(): float;
}
