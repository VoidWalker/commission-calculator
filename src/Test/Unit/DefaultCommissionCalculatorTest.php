<?php
declare(strict_types=1);

namespace Oleksandrsokhan\CommissionCalculator\Test\Unit;

use Oleksandrsokhan\CommissionCalculator\Api\BinServiceInterface;
use Oleksandrsokhan\CommissionCalculator\Api\ConfigInterface;
use Oleksandrsokhan\CommissionCalculator\Api\TransactionInterface;
use Oleksandrsokhan\CommissionCalculator\CurrencyRateService;
use Oleksandrsokhan\CommissionCalculator\DefaultCommissionCalculator;
use PHPUnit\Framework\TestCase;

class DefaultCommissionCalculatorTest extends TestCase
{

    private BinServiceInterface $binService;
    private CurrencyRateService $currencyRateService;
    private DefaultCommissionCalculator $commissionCalculator;

    public function setUp(): void
    {
        $this->binService = $this->createMock(BinServiceInterface::class);
        $this->currencyRateService = $this->createMock(CurrencyRateService::class);
        $config = $this->createMock(ConfigInterface::class);
        $config->method('getCommissionRateEu')->willReturn(0.01);
        $config->method('getCommissionRateNonEu')->willReturn(0.02);

        $this->commissionCalculator = new DefaultCommissionCalculator(
            $this->binService,
            $config,
            $this->currencyRateService,
        );
    }

    public function testCalculateCommissionEuCountry(): void
    {
        $this->binService->expects($this->once())
            ->method('isEuCountryCard')
            ->with('45717360')
            ->willReturn(true);

        $this->currencyRateService->expects($this->once())
            ->method('getRate')
            ->with('EUR')
            ->willReturn(1.0);

        $mockTransaction = $this->createMock(TransactionInterface::class);
        $mockTransaction->method('getBin')->willReturn('45717360');
        $mockTransaction->method('getAmount')->willReturn(100.00);
        $mockTransaction->method('getCurrency')->willReturn('EUR');


        $this->assertSame(
            1.0,
            $this->commissionCalculator->calculateCommission($mockTransaction)
        );
    }

    public function testCalculateCommissionNonEuCountry(): void
    {
        $this->binService->expects($this->once())
            ->method('isEuCountryCard')
            ->with('516793')
            ->willReturn(false);

        $this->currencyRateService->expects($this->once())
            ->method('getRate')
            ->with('USD')
            ->willReturn(1.2);

        $mockTransaction = $this->createMock(TransactionInterface::class);
        $mockTransaction->method('getBin')->willReturn('516793');
        $mockTransaction->method('getAmount')->willReturn(50.00);
        $mockTransaction->method('getCurrency')->willReturn('USD');

        $this->assertSame(
            0.8333333333333335,
            $this->commissionCalculator->calculateCommission($mockTransaction)
        );
    }

    public function testCalculateCommissionZeroRate(): void
    {
        $this->binService->expects($this->once())
            ->method('isEuCountryCard')
            ->with('45417360')
            ->willReturn(true);

        $this->currencyRateService->expects($this->once())
            ->method('getRate')
            ->with('JPY')
            ->willReturn(0.0);

        $mockTransaction = $this->createMock(TransactionInterface::class);
        $mockTransaction->method('getBin')->willReturn('45417360');
        $mockTransaction->method('getAmount')->willReturn(10000.00);
        $mockTransaction->method('getCurrency')->willReturn('JPY');

        $this->assertSame(
            100.0,
            $this->commissionCalculator->calculateCommission($mockTransaction)
        );
    }

    public function testCalculateCommissionThrowsExceptionOnDependencyFailure(): void
    {
        $this->binService->method('isEuCountryCard')
            ->willThrowException(new \RuntimeException("Bin service failed"));

        $mockTransaction = $this->createMock(TransactionInterface::class);
        $mockTransaction->method('getBin')->willReturn('45717360');
        $mockTransaction->method('getAmount')->willReturn(100.00);
        $mockTransaction->method('getCurrency')->willReturn('EUR');


        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Bin service failed');
        $this->commissionCalculator->calculateCommission($mockTransaction);
    }
}
