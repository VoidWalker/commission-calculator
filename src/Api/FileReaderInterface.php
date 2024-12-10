<?php
declare(strict_types=1);

namespace Oleksandrsokhan\CommissionCalculator\Api;

use Generator;

interface FileReaderInterface
{
    public function readTransactions(string $filename): Generator;
}
