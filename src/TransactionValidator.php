<?php
declare(strict_types=1);

namespace Oleksandrsokhan\CommissionCalculator;

class TransactionValidator implements Api\TransactionValidatorInterface
{
    public function validate(array $data): bool
    {
        if (empty($data['bin'])) {
            throw new \InvalidArgumentException('Missing bin');
        }

        if (empty($data['amount'])) {
            throw new \InvalidArgumentException('Missing amount');
        }

        if (empty($data['currency'])) {
            throw new \InvalidArgumentException('Missing currency');
        }

        return true;
    }
}
