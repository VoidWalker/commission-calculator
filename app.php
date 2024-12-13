<?php
require_once __DIR__ . '/vendor/autoload.php';

use \Oleksandrsokhan\CommissionCalculator\App;


$config = new \Oleksandrsokhan\CommissionCalculator\Config();
$httpClient = new \GuzzleHttp\Client();
$binService = new \Oleksandrsokhan\CommissionCalculator\BinService($httpClient);

$app = new App(
    new \Oleksandrsokhan\CommissionCalculator\TxtFileReader(
        new \Oleksandrsokhan\CommissionCalculator\TransactionValidator()
    ),
    [
        'EUR' => new \Oleksandrsokhan\CommissionCalculator\EurCommissionCalculator(
            $binService,
            $config
        ),
        'default' => new \Oleksandrsokhan\CommissionCalculator\DefaultCommissionCalculator(
            $binService,
            new \Oleksandrsokhan\CommissionCalculator\CurrencyRateService(
                $httpClient
            ),
            $config
        )
    ]
);
try {
    $commission = $app->run($argv);
    foreach ($commission as $value) {
//        echo number_format($value, 2) . PHP_EOL;
        echo ceil($value * 100) / 100 . PHP_EOL;
    }
} catch (\Exception $e) {
    echo $e->getMessage() . PHP_EOL;
}
