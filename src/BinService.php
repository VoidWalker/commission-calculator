<?php
declare(strict_types=1);

namespace Oleksandrsokhan\CommissionCalculator;

use GuzzleHttp\Exception\GuzzleException;
use Oleksandrsokhan\CommissionCalculator\Api\BinServiceInterface;

class BinService implements BinServiceInterface
{
    private const string URL_PATTERN = "https://lookup.binlist.net/{bin}";
    private const array EU_COUNTRIES = [
        'AT',
        'BE',
        'BG',
        'CY',
        'CZ',
        'DE',
        'DK',
        'EE',
        'ES',
        'FI',
        'FR',
        'GR',
        'HR',
        'HU',
        'IE',
        'IT',
        'LT',
        'LU',
        'LV',
        'MT',
        'NL',
        'PO',
        'PT',
        'RO',
        'SE',
        'SI',
        'SK'
    ];

    public function __construct(
        private readonly \GuzzleHttp\ClientInterface $httpClient
    ) {
    }

    public function getCountryByBin(string $bin): ?string
    {
        $url = str_replace('{bin}', $bin, self::URL_PATTERN);
        try {
            $response = $this->httpClient->request('GET', $url);

            if ($response->getStatusCode() !== 200) {
                throw new \RuntimeException('Invalid response code');
            }

            $data = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

            return $data['country']['alpha2'] ?? null;
        } catch (\JsonException $e) {
            throw new \RuntimeException("Error parsing BIN data: " . $e->getMessage());
        } catch (\Exception|GuzzleException $e) {
            throw new \RuntimeException("Error fetching BIN data: " . $e->getMessage());
        }
    }

    public function isEuCountryCard(string $bin): bool
    {
        $country = $this->getCountryByBin($bin);
        if (!$country) {
            throw new \RuntimeException("Failed to get country by bin $bin");
        }

        return in_array($country, self::EU_COUNTRIES);
    }
}
