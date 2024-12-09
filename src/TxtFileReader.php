<?php
declare(strict_types=1);

namespace Oleksandrsokhan\CommissionCalculator;

use Oleksandrsokhan\CommissionCalculator\Api\FileReaderInterface;

class TxtFileReader implements FileReaderInterface
{
    /**
     * Read file
     * TODO: Read data by batches or line by line using generator
     * @param string $filename
     * @return array
     **/
    public function read(string $filename): array
    {
        if (!file_exists($filename)) {
            throw new \RuntimeException("File not found: $filename");
        }

        $fileData = file_get_contents($filename);
        if ($fileData === false) {
            throw new \RuntimeException("Failed to read file: $filename");
        }

        $rows = explode("\n", $fileData);
        $rows = array_filter(array_map('trim', $rows));

        return $rows;
    }
}
