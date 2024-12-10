<?php
declare(strict_types=1);

namespace Oleksandrsokhan\CommissionCalculator\Api;

interface TransactionValidatorInterface
{
    public function validate(array $data): bool;
}
