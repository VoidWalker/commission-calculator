<?php
require_once __DIR__ . '/vendor/autoload.php';

use \Oleksandrsokhan\CommissionCalculator\App;


$app = new App(
    new \Oleksandrsokhan\CommissionCalculator\TxtFileReader(
        new \Oleksandrsokhan\CommissionCalculator\TransactionValidator()
    ),
    [
        'EUR' => new \Oleksandrsokhan\CommissionCalculator\EurCommissionCalculator(
            new \Oleksandrsokhan\CommissionCalculator\BinService(),
        ),
        'default' => new \Oleksandrsokhan\CommissionCalculator\DefaultCommissionCalculator(
            new \Oleksandrsokhan\CommissionCalculator\BinService(),
            new \Oleksandrsokhan\CommissionCalculator\CurrencyRateService()
        )
    ]
);
try {
    $commission = $app->run($argv);
    foreach ($commission as $value) {
        echo number_format($value, 2) . PHP_EOL;
    }
} catch (\Exception $e) {
    echo $e->getMessage() . PHP_EOL;
}
