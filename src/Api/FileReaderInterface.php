<?php
declare(strict_types=1);

namespace Oleksandrsokhan\CommissionCalculator\Api;

interface FileReaderInterface
{
    public function read(string $filename): array;
}
