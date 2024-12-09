<?php
declare(strict_types=1);

namespace Oleksandrsokhan\CommissionCalculator;

use Oleksandrsokhan\CommissionCalculator\Api\CurrencyRateServiceInterface;

class CurrencyRateService implements CurrencyRateServiceInterface
{
    const string URL = 'https://api.exchangeratesapi.io/latest';

    /**
     * @inheritDoc
     */
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
        /*
        $data = [
            'rates' => [
                'EUR' => 1,
                'USD' => 1.1497,
                'JPY' => 129.53,
                'BGN' => 1.9558,
                'CZK' => 25.855,
                'DKK' => 7.4646,
                'GBP' => 0.88308,
                'HUF' => 322.92,
                'PLN' => 4.2983,
                'RON' => 4.6558,
                'SEK' => 10.6465,
                'CHF' => 1.1354,
                'ISK' => 137.7,
                'NOK' => 9.7558,
                'HRK' => 7.4325,
                'RUB' => 75.835,
                'TRY' => 6.3175,
                'AUD' => 1.6145,
                'BRL' => 4.2825,
                'CAD' => 1.5073,
                'CNY' => 7.8045,
                'HKD' => 8.9923,
                'IDR' => 16195.68,
                'ILS' => 4.094,
                'INR' => 78.115,
                'KRW' => 1301.68,
                'MXN' => 22.363,
                'MYR' => 4.712,
                'NZD' => 1.726,
                'PHP' => 59.841,
                'SGD' => 1.5443,
                'THB' => 35.91,
                'ZAR' => 16.422,
            ]
        ];
        */
        if (!isset($data['rates'][$currency])) {
            throw new \RuntimeException("Currency rate for $currency not found");
        }

        return (float)$data['rates'][$currency];
    }
}
