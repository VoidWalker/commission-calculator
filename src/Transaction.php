<?php
declare(strict_types=1);

namespace Oleksandrsokhan\CommissionCalculator;

class Transaction
{
    public string $bin;

    public float $amount;

    public string $currency;

    public static function fromJsonString(string $jsonString): self
    {
        $data = json_decode($jsonString, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Invalid JSON format');
        }
        self::validate($data);

        $transaction = new self();
        $transaction->bin = $data['bin'];
        $transaction->amount = (float) $data['amount'];
        $transaction->currency = $data['currency'];

        return $transaction;
    }

    private static function validate(mixed $data): void
    {
        if (!is_array($data)) {
            throw new \InvalidArgumentException('Invalid data format');
        }

        if (!isset($data['bin'])) {
            throw new \InvalidArgumentException('Missing bin');
        }

        if (!isset($data['amount'])) {
            throw new \InvalidArgumentException('Missing amount');
        }

        if (!isset($data['currency'])) {
            throw new \InvalidArgumentException('Missing currency');
        }
    }
}
