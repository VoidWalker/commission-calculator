<?php
declare(strict_types=1);

namespace Oleksandrsokhan\CommissionCalculator;

use Oleksandrsokhan\CommissionCalculator\Api\FileReaderInterface;
use Oleksandrsokhan\CommissionCalculator\Api\TransactionValidatorInterface;

class TxtFileReader implements FileReaderInterface
{
    public function __construct(
        private readonly TransactionValidatorInterface $transactionValidator
    ) {
    }

    public function readTransactions(string $filename): \Generator
    {
        if (!file_exists($filename)) {
            throw new \RuntimeException("File not found: $filename");
        }

        $file = fopen($filename, 'r');
        if ($file === false) {
            throw new \RuntimeException("Failed to read file: $filename");
        }

        try {
            while (($row = fgets($file)) !== false) {
                $row = trim($row);
                if (empty($row)) {
                    continue;
                }
                $transaction = $this->parseRow($row);
                yield $transaction;
            }
        } catch (\JsonException $e) {
            throw new \RuntimeException("Failed to parse JSON data: " . $e->getMessage());
        } catch (\Throwable $e) {
            throw new \RuntimeException("Failed to read file: " . $e->getMessage());
        } finally {
            fclose($file);
        }
    }

    private function parseRow(string $rowJson): Transaction
    {
        $data = json_decode($rowJson, true, 512, JSON_THROW_ON_ERROR);
        $this->transactionValidator->validate($data);
        return new Transaction($data['bin'], (float)$data['amount'], $data['currency']);
    }
}
