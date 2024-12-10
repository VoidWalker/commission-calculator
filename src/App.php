<?php
declare(strict_types=1);

namespace Oleksandrsokhan\CommissionCalculator;

use Oleksandrsokhan\CommissionCalculator\Api\CommissionCalculatorInterface;
use Oleksandrsokhan\CommissionCalculator\Api\FileReaderInterface;

class App
{
    /**
     * @param FileReaderInterface $fileReader
     * @param CommissionCalculatorInterface[] $commissionCalculators
     */
    public function __construct(
        private readonly FileReaderInterface $fileReader,
        private readonly array $commissionCalculators
    ) {
    }

    public function run($argv): array
    {
        $result = [];
        $filePath = $argv[1] ?? null;

        if (!$filePath) {
            throw new \InvalidArgumentException('Please provide file path as a parameter');
        }

        /** @var Transaction $transaction */
        foreach ($this->fileReader->readTransactions($filePath) as $transaction) {
            $result[] = number_format($this->calculateCommission($transaction), 2);
        }

        return $result;
    }

    private function calculateCommission(Transaction $transaction): float
    {
        $calculator = $this->comissionCalculators[$transaction->getCurrency()] ?? $this->commissionCalculators['default'];
        return $calculator->calculateCommission($transaction);
    }
}
