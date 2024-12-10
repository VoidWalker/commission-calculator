<?php
declare(strict_types=1);

namespace Oleksandrsokhan\CommissionCalculator\Test\Unit;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use Oleksandrsokhan\CommissionCalculator\BinService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class BinServiceTest extends TestCase
{
    private ClientInterface $httpClientMock;
    private BinService $binService;

    public static function getCountryByBinDataProvider()
    {
        return [
            ['45717360', 'DK'],
            ['516793', 'LT'],
            ['4745030', 'GBP'],
            ['123321', null],
        ];
    }

    public function setUp(): void
    {
        $this->httpClientMock = $this->createMock(ClientInterface::class);
        $this->binService = new BinService(
            $this->httpClientMock
        );
    }

    #[DataProvider('getCountryByBinDataProvider')]
    public function testGetCountryByBin(
        string $bin,
        ?string $expectedCountry
    ) {
        $this->httpClientMock
            ->expects($this->atLeastOnce())
            ->method('request')
            ->willReturn(
                new Response(200, [], json_encode(['country' => ['alpha2' => $expectedCountry]]))
            );

        $country = $this->binService->getCountryByBin($bin);
        $this->assertEquals($expectedCountry, $country);
    }

    public function testGetCountryByBinException() {
        $this->httpClientMock
            ->expects($this->once())
            ->method('request')
            ->willThrowException(
                new \Exception('Server error')
            );

        $this->expectException(\RuntimeException::class);
        $this->binService->getCountryByBin('123321');
    }

    public function testIsEuCountryCardReturnsTrueForEuCountry(): void
    {
        $binServiceMock = $this->getMockBuilder(BinService::class)
            ->onlyMethods(['getCountryByBin'])
            ->setConstructorArgs([$this->httpClientMock])
            ->getMock();

        $binServiceMock->expects($this->once())
            ->method('getCountryByBin')
            ->with('45717360')
            ->willReturn('FR');

        $result = $binServiceMock->isEuCountryCard('45717360');
        $this->assertTrue($result);
    }

    public function testIsEuCountryCardReturnsFalseForNonEuCountry(): void
    {
        $binServiceMock = $this->getMockBuilder(BinService::class)
            ->onlyMethods(['getCountryByBin'])
            ->setConstructorArgs([$this->httpClientMock])
            ->getMock();

        $binServiceMock->expects($this->once())
            ->method('getCountryByBin')
            ->with('516793')
            ->willReturn('US');

        $result = $binServiceMock->isEuCountryCard('516793');
        $this->assertFalse($result);
    }

    public function testIsEuCountryCardThrowsExceptionIfCountryNotFound(): void
    {
        $binServiceMock = $this->getMockBuilder(BinService::class)
            ->onlyMethods(['getCountryByBin'])
            ->setConstructorArgs([$this->httpClientMock])
            ->getMock();

        $binServiceMock->expects($this->once())
            ->method('getCountryByBin')
            ->with('000000')
            ->willReturn(null);

        $this->expectException(\RuntimeException::class);

        $binServiceMock->isEuCountryCard('000000');
    }
}
