<?php
declare(strict_types=1);

namespace Oleksandrsokhan\CommissionCalculator;

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

    public function getCountryByBin(string $bin): ?string
    {
        $url = str_replace('{bin}', $bin, self::URL_PATTERN);
        $response = file_get_contents($url);
        if ($response === false) {
            throw new \RuntimeException("Failed to get data from $url");
        }

        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException("Failed to decode JSON data");
        }

        return $data['country']['alpha2'] ?? null;
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
