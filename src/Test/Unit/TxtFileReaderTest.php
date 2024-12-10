<?php
declare(strict_types=1);

namespace Oleksandrsokhan\CommissionCalculator\Test\Unit;

use Oleksandrsokhan\CommissionCalculator\Api\TransactionValidatorInterface;
use Oleksandrsokhan\CommissionCalculator\Transaction;
use Oleksandrsokhan\CommissionCalculator\TxtFileReader;
use PHPUnit\Framework\TestCase;

class TxtFileReaderTest extends TestCase
{
    private string $filePath = '';

    public function testReadTransactionsReturnsTransactions(): void
    {
        $mockValidator = $this->createMock(TransactionValidatorInterface::class);
        $mockValidator->expects($this->exactly(2))
            ->method('validate')
            ->willReturn(true);

        // Creating a temporary file with mock content
        $fileContent = '{"bin":"45717360","amount":"100.00","currency":"EUR"}' . PHP_EOL .
            '{"bin":"516793","amount":"50.00","currency":"USD"}';
        $this->filePath = __DIR__ . '/test-file.txt';
        file_put_contents($this->filePath, $fileContent);

        $fileReader = new TxtFileReader($mockValidator);

        // Test generator functionality
        $transactions = iterator_to_array($fileReader->readTransactions($this->filePath), false);

        $this->assertCount(2, $transactions);
        $this->assertInstanceOf(Transaction::class, $transactions[0]);
        $this->assertSame('45717360', $transactions[0]->getBin());
        $this->assertSame(100.00, $transactions[0]->getAmount());
        $this->assertSame('EUR', $transactions[0]->getCurrency());
    }

    public function testReadTransactionsThrowsExceptionOnFileNotFound(): void
    {
        $mockValidator = $this->createMock(TransactionValidatorInterface::class);

        $fileReader = new TxtFileReader($mockValidator);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('File not found: non-existent-file.txt');

        iterator_to_array($fileReader->readTransactions('non-existent-file.txt'), true);

    }

    public function testReadTransactionsThrowsExceptionOnInvalidJson(): void
    {
        $mockValidator = $this->createMock(TransactionValidatorInterface::class);

        $fileContent = '{"bin":"45717360","amount":"100.00","currency":"EUR"}' . PHP_EOL .
            'invalid-json-line' . PHP_EOL;
        $this->filePath = __DIR__ . '/invalid-json-file.txt';
        file_put_contents($this->filePath, $fileContent);

        $fileReader = new TxtFileReader($mockValidator);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Failed to parse JSON data: Syntax error');

        iterator_to_array($fileReader->readTransactions($this->filePath), false);
    }

    protected function tearDown(): void
    {
        // Cleanup the test file after the test has run
        if (file_exists($this->filePath)) {
            unlink($this->filePath);
        }
        parent::tearDown(); // Always call parent tearDown()
    }
}
