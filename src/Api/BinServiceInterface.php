<?php
declare(strict_types=1);

namespace Oleksandrsokhan\CommissionCalculator\Api;

interface BinServiceInterface
{
    public function getCountryByBin(string $bin): ?string;

    public function isEuCountryCard(string $bin): bool;
}
