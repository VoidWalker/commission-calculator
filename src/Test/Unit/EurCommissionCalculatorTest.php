<?php
declare(strict_types=1);

namespace Oleksandrsokhan\CommissionCalculator\Test\Unit;

use Oleksandrsokhan\CommissionCalculator\Api\BinServiceInterface;
use Oleksandrsokhan\CommissionCalculator\Api\ConfigInterface;
use Oleksandrsokhan\CommissionCalculator\Api\TransactionInterface;
use Oleksandrsokhan\CommissionCalculator\EurCommissionCalculator;
use PHPUnit\Framework\TestCase;

class EurCommissionCalculatorTest extends TestCase
{
    private BinServiceInterface $binService;
    private EurCommissionCalculator $commissionCalculator;

    public function setUp(): void
    {
        $this->binService = $this->createMock(BinServiceInterface::class);
        $config = $this->createMock(ConfigInterface::class);
        $config->method('getBaseCurrencyCommission')->willReturn(0.01);
        $config->method('getForeignCurrencyCommission')->willReturn(0.02);

        $this->commissionCalculator = new EurCommissionCalculator(
            $this->binService,
            $config,
        );
    }

    public function testCalculateCommissionEuCountry(): void
    {
        $this->binService->method('isEuCountryCard')
            ->with('45717360')
            ->willReturn(true);

        $mockTransaction = $this->createMock(TransactionInterface::class);
        $mockTransaction->method('getBin')->willReturn('45717360');
        $mockTransaction->method('getAmount')->willReturn(100.00);

        $this->assertSame(1.0, $this->commissionCalculator->calculateCommission($mockTransaction));
    }

    public function testCalculateCommissionNonEuCountry(): void
    {
        $this->binService->method('isEuCountryCard')
            ->with('516793')
            ->willReturn(false);

        $mockTransaction = $this->createMock(TransactionInterface::class);
        $mockTransaction->method('getBin')->willReturn('516793');
        $mockTransaction->method('getAmount')->willReturn(50.00);


        $this->assertSame(1.0, $this->commissionCalculator->calculateCommission($mockTransaction));
    }

    public function testCalculateCommissionThrowsExceptionOnBinServiceFailure(): void
    {
        $this->binService->method('isEuCountryCard')
            ->willThrowException(new \RuntimeException("Bin service failed"));

        $mockTransaction = $this->createMock(TransactionInterface::class);
        $mockTransaction->method('getBin')->willReturn('45717360');
        $mockTransaction->method('getAmount')->willReturn(100.00);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Bin service failed');
        $this->commissionCalculator->calculateCommission($mockTransaction);
    }
}
