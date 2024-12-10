<?php
declare(strict_types=1);

namespace Oleksandrsokhan\CommissionCalculator;

use Oleksandrsokhan\CommissionCalculator\Api\CurrencyRateServiceInterface;

class CurrencyRateService implements CurrencyRateServiceInterface
{
    const string URL = 'https://api.exchangeratesapi.io/latest';

    public function getRate(string $currency): float
    {
        $response = file_get_contents(self::URL);
        if ($response === false) {
            throw new \RuntimeException("Failed to get data from " . self::URL);
        }

        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException("Failed to decode JSON data");
        }

        if (!isset($data['rates'][$currency])) {
            throw new \RuntimeException("Currency rate for $currency not found");
        }

        return (float)$data['rates'][$currency];
    }
}
