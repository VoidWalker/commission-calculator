<?php
declare(strict_types=1);

namespace Oleksandrsokhan\CommissionCalculator;

use Oleksandrsokhan\CommissionCalculator\Api\BinServiceInterface;
use Oleksandrsokhan\CommissionCalculator\Api\CurrencyRateServiceInterface;
use Oleksandrsokhan\CommissionCalculator\Api\FileReaderInterface;

class App
{
    public function __construct(
        private readonly FileReaderInterface $fileReader,
        private readonly BinServiceInterface $binService,
        private readonly CurrencyRateServiceInterface $currencyRateService
    ) {

    }

    public function run($argv): array
    {
        $result = [];
        $filePath = $argv[1] ?? null;

        if (!$filePath) {
            throw new \InvalidArgumentException('Please provide file path as a parameter');
        }

        $rows = $this->fileReader->read($filePath);

        foreach ($rows as $row) {
            $transaction = Transaction::fromJsonString($row);
            $isEu = $this->binService->isEuCountryCard($transaction->bin);
            $rate = $this->currencyRateService->getRate($transaction->currency);

            $result[] = number_format($this->calculateCommission($transaction, $isEu, $rate), 2);
        }

        return $result;
    }

    private function calculateCommission(Transaction $transaction, bool $isEu, float $rate): float
    {
        if ($transaction->currency === 'EUR' || $rate == 0) {
            $amount = $transaction->amount;
        } else {
            $amount = $transaction->amount / $rate;
        }

        return $isEu ? $amount * 0.01 : $amount * 0.02;
    }
}
