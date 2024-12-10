<?php
declare(strict_types=1);

namespace Oleksandrsokhan\CommissionCalculator;

use GuzzleHttp\Exception\GuzzleException;
use Oleksandrsokhan\CommissionCalculator\Api\CurrencyRateServiceInterface;

class CurrencyRateService implements CurrencyRateServiceInterface
{
    const string URL = 'https://api.exchangeratesapi.io/latest';

    public function __construct(
        private readonly \GuzzleHttp\ClientInterface $httpClient
    ) {
    }

    public function getRate(string $currency): float
    {
        try {
            $response = $this->httpClient->request('GET', self::URL);

            if ($response->getStatusCode() !== 200) {
                throw new \RuntimeException('Invalid response code');
            }

            $data = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new \RuntimeException("Error parsing rate data: " . $e->getMessage());
        } catch (\Exception|GuzzleException $e) {
            throw new \RuntimeException("Error fetching rate data: " . $e->getMessage());
        }

        if (empty($data['rates'][$currency])) {
            throw new \RuntimeException("Currency rate for $currency not found");
        }

        return (float)$data['rates'][$currency];
    }
}
