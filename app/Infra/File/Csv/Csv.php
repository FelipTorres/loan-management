<?php

namespace App\Infra\File\Csv;

use App\Domain\File\Csv\CsvDataValidator;
use App\Domain\File\Csv\CsvInterface;
use App\Domain\File\File;
use App\Exceptions\CsvEmptyContentException;
use App\Exceptions\CsvHeadersValidation;
use App\Http\Helpers\CustomHelper;
use Illuminate\Http\UploadedFile;

class Csv extends File implements CsvInterface
{
    private const INVALID_CSV_FILE_MESSAGE = 'The csv file is not valid';
    private const CSV_EMPTY_CONTENT_MESSAGE = 'There is no data to create the csv';
    private const LINE_NUMBER = 'line_number';
    private const COMMA_SEPARATOR = ',';
    private const EMPTY_ROW_SIZE = 0;
    private array $headers;
    private array $rows;
    private array $associativeContent;
    private array $expectedHeaders;

    public function setItemsCSV(UploadedFile $uploadedFile): void
    {
        $this->setDataValidator(new CsvDataValidator())
            ->setSizeInBytes($uploadedFile->getSize())
            ->setMimeType($uploadedFile->getClientMimeType())
            ->setContent($uploadedFile->getContent());
    }

    /**
     * @throws CsvHeadersValidation
     */
    public function buildAssociativeArrayFromContent(): array
    {
        $this->extractHeadersAndRows();
        $this->generateAssociativeArray();

        return $this->associativeContent;
    }

    public function setAssociativeContent(array $associativeContent): Csv
    {
        $this->associativeContent = $associativeContent;

        return $this;
    }

    public function getAssociativeContent(): array
    {
        return $this->associativeContent;
    }

    public function setExpectedHeaders(array $expectedHeaders): Csv
    {
        $this->expectedHeaders = $expectedHeaders;

        return $this;
    }

    /**
     * @throws CsvHeadersValidation
     */
    private function extractHeadersAndRows(): void
    {
        $explodedContent = explode(PHP_EOL, $this->getContent());

        $headersUnitedByComma = $explodedContent[0];

        $this->defineHeaders($headersUnitedByComma);

        $this->defineRows($explodedContent);
    }

    /**
     * @throws CsvHeadersValidation
     */
    private function defineHeaders(string $headersUnitedByComma): void
    {
        $headers = $this->explodeRow($headersUnitedByComma);

        $this->validateHeaders($headers);

        $this->headers = $headers;
    }

    private function explodeRow(string $row): array
    {
        return explode(self::COMMA_SEPARATOR, $row);
    }

    /**
     * @throws CsvHeadersValidation
     */
    private function validateHeaders(array $headers): void
    {
        $expectedHeaders = $this->expectedHeaders;

        $headers = CustomHelper::cleanValues($headers);

        if (count($expectedHeaders) !== count($headers)) {

            throw new CsvHeadersValidation(self::INVALID_CSV_FILE_MESSAGE);
        }

        foreach ($expectedHeaders as $expectedHeader) {

            $headerExists = in_array($expectedHeader, $headers);

            if (!$headerExists) {

                throw new CsvHeadersValidation(self::INVALID_CSV_FILE_MESSAGE);
            }
        }
    }

    private function defineRows(array $explodedContent): void
    {
        array_shift($explodedContent);

        $explodedContent = CustomHelper::cleanValues($explodedContent);

        $this->rows = $explodedContent;
    }

    private function generateAssociativeArray(): void
    {
        foreach ($this->rows as $index => $row) {

            $this->mapHeadersToRowContent($row, $index);
        }
    }

    private function mapHeadersToRowContent(string $rowContent, int $rowNumber): void
    {
        if ($this->isValidRow($rowContent)) {

            $explodedRowContent = $this->explodeRow($rowContent);

            $this->associativeContent[] = $this->createAssociativeRow($explodedRowContent, $rowNumber);
        }
    }

    private function isValidRow(string $row): bool
    {
        $trimmedRow = trim($row);

        return mb_strlen($trimmedRow) > self::EMPTY_ROW_SIZE;
    }

    private function createAssociativeRow(array $explodedRowContent, int $rowNumber): array
    {
        $associativeLine = [];

        foreach ($this->headers as $index => $header) {

            $associativeLine[$header] = $explodedRowContent[$index];
        }

        $associativeLine[self::LINE_NUMBER] = $rowNumber + 2;

        return $associativeLine;
    }

    /**
     * @throws CsvEmptyContentException
     */
    public function buildFromAssociativeContent(): void
    {
        $this->checkContent();

        $headers = $this->createHeaders();
        $rows = $this->createRows();

        $this->setContent($headers . $rows);
    }

    /**
     * @throws CsvEmptyContentException
     */
    private function checkContent(): void
    {
        if (count($this->associativeContent) === 0) {

            throw new CsvEmptyContentException(self::CSV_EMPTY_CONTENT_MESSAGE);
        }
    }

    private function createHeaders(): string
    {
        $headers = implode(self::COMMA_SEPARATOR, $this->expectedHeaders);
        $headers .= PHP_EOL;

        return $headers;
    }

    private function createRows(): string
    {
        $rows = "";

        foreach($this->associativeContent as $row) {
            $rowDataArray = array_values($row);
            $rowDataUnitedByComma = implode(self::COMMA_SEPARATOR, $rowDataArray);
            $rowDataUnitedByComma .= PHP_EOL;

            $rows .= $rowDataUnitedByComma;
        }

        return $rows;
    }
}
