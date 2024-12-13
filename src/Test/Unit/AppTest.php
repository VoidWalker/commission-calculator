<?php
declare(strict_types=1);

namespace Oleksandrsokhan\CommissionCalculator\Test\Unit;

use Oleksandrsokhan\CommissionCalculator\Api\CommissionCalculatorInterface;
use Oleksandrsokhan\CommissionCalculator\Api\FileReaderInterface;
use Oleksandrsokhan\CommissionCalculator\App;
use Oleksandrsokhan\CommissionCalculator\Transaction;
use PHPUnit\Framework\TestCase;

class AppTest extends TestCase
{
    public function testRunProcessesTransactions(): void
    {
        $mockFileReader = $this->createMock(FileReaderInterface::class);
        $mockFileReader->method('readTransactions')
            ->with('path/to/file')
            ->willReturn($this->createTransactionGenerator([
                ['EUR', 100.00, '45717360'],
                ['USD', 50.00, '516793'],
            ]));

        $mockEurCalculator = $this->createMock(CommissionCalculatorInterface::class);
        $mockEurCalculator->method('calculateCommission')->willReturn(1.0);

        $mockDefaultCalculator = $this->createMock(CommissionCalculatorInterface::class);
        $mockDefaultCalculator->method('calculateCommission')->willReturn(2.0);

        $app = new App(
            $mockFileReader,
            [
                'EUR' => $mockEurCalculator,
                'default' => $mockDefaultCalculator,
            ]
        );

        $result = $app->run(['script.php', 'path/to/file']);

        $this->assertSame([1.0, 2.0], $result);
    }

    public function testRunThrowsExceptionForMissingFilePath(): void
    {
        $mockFileReader = $this->createMock(FileReaderInterface::class);
        $app = new App($mockFileReader, []);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Please provide file path as a parameter');
        $app->run(['script.php']);
    }

    public function testRunFallsBackToDefaultCalculator(): void
    {
        $mockFileReader = $this->createMock(FileReaderInterface::class);
        $mockFileReader->method('readTransactions')
            ->with('path/to/file')
            ->willReturn(
                $this->createTransactionGenerator([['JPY', 10000.00, '45417360']]),
            );

        $mockDefaultCalculator = $this->createMock(CommissionCalculatorInterface::class);
        $mockDefaultCalculator->method('calculateCommission')->willReturn(200.0);

        $app = new App(
            $mockFileReader,
            [
                'default' => $mockDefaultCalculator,
            ]
        );

        $result = $app->run(['script.php', 'path/to/file']);

        $this->assertSame([200.0], $result);
    }

    public function testRunHandlesFileReaderFailure(): void
    {
        $mockFileReader = $this->createMock(FileReaderInterface::class);
        $mockFileReader->method('readTransactions')
            ->willThrowException(new \RuntimeException('File read error'));

        $app = new App($mockFileReader, []);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('File read error');
        $app->run(['script.php', 'path/to/file']);
    }

    private function createTransaction(string $currency, float $amount, string $bin): Transaction
    {
        $transaction = $this->createMock(Transaction::class);
        $transaction->method('getCurrency')->willReturn($currency);
        $transaction->method('getAmount')->willReturn($amount);
        $transaction->method('getBin')->willReturn($bin);

        return $transaction;
    }

    private function createTransactionGenerator(array $transactions): \Generator
    {
        foreach ($transactions as $transaction) {
            yield $this->createTransaction(...$transaction);
        }
    }
}
