<?php

namespace App\Infra\File\Csv;

use App\Domain\File\Csv\CsvInterface;
use App\Domain\File\File;

class Csv extends File implements CsvInterface
{
    private array $associativeContent = [];
    private array $expectedHeaders = [];

    public function buildAssociativeArrayFromContent(): array
    {
        $rows = explode(PHP_EOL, $this->getContent());

        $headers = str_getcsv(array_shift($rows));

        $data = [];

        foreach ($rows as $row) {

            if (empty($row)) {

                continue;
            }

            $data[] = array_combine($headers, str_getcsv($row));
        }

        return $data;
    }

    public function setAssociativeContent(array $associativeContent): CsvInterface
    {
        $this->associativeContent = $associativeContent;

        return $this;
    }

    public function getAssociativeContent(): array
    {
        return $this->associativeContent;
    }

    public function setExpectedHeaders(array $expectedHeaders): CsvInterface
    {
        $this->expectedHeaders = $expectedHeaders;

        return $this;
    }

    public function buildFromAssociativeContent(): void
    {
        $content = implode(',', $this->expectedHeaders) . PHP_EOL;

        foreach ($this->associativeContent as $row) {

            $content .= implode(',', $row) . PHP_EOL;
        }

        $this->setContent($content);
    }
}
