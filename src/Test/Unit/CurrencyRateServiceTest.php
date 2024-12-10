<?php
declare(strict_types=1);

namespace Oleksandrsokhan\CommissionCalculator\Test\Unit;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Oleksandrsokhan\CommissionCalculator\CurrencyRateService;
use PHPUnit\Framework\TestCase;

class CurrencyRateServiceTest extends TestCase
{
    private ClientInterface $httpClientMock;
    private CurrencyRateService $currencyRateService;

    public function setUp(): void
    {
        $this->httpClientMock = $this->createMock(ClientInterface::class);
        $this->currencyRateService = new CurrencyRateService(
            $this->httpClientMock
        );
    }

    public function testGetRateSuccess(): void
    {
        $this->httpClientMock->method('request')
            ->willReturn(new Response(200, [], json_encode(['rates' => ['USD' => 1.2]])));

        $rate = $this->currencyRateService->getRate('USD');

        $this->assertSame(1.2, $rate);
    }

    public function testGetRateCurrencyNotFound(): void
    {
        $this->httpClientMock->method('request')
            ->willReturn(new Response(200, [], json_encode(['rates' => ['EUR' => 0.85]])));


        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Currency rate for USD not found');
        $this->currencyRateService->getRate('USD');
    }

    public function testGetRateHttpRequestFailure(): void
    {
        $this->httpClientMock->method('request')
            ->willThrowException(new RequestException(
                'Connection error',
                new Request('GET', 'mock-url')
            ));


        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Error fetching rate data: Connection error');
        $this->currencyRateService->getRate('USD');
    }

    public function testGetRateMalformedJson(): void
    {
        $this->httpClientMock->method('request')
            ->willReturn(new Response(200, [], 'invalid-json'));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Error parsing rate data: Syntax error');
        $this->currencyRateService->getRate('USD');
    }
}
