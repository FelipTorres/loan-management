<?php

namespace App\Domain\File\Csv;

use App\Domain\File\FileDataValidatorInterface;
use App\Exceptions\DataValidationException;
use App\Http\Helpers\CustomHelper;

class CsvDataValidator implements FileDataValidatorInterface
{
    private const VALID_MIME_TYPE = 'text/csv';
    private const MAX_SIZE_IN_BYTES = 1000000;
    private const MIN_SIZE_IN_BYTES = 1;

    /**
     * @throws DataValidationException
     */
    public function validateMimeType(string $mimeType): void
    {
        $mimeType = CustomHelper::applyTrim($mimeType);

        if (empty($mimeType)) {

            throw new DataValidationException('The file mimeType cannot be empty');
        }

        if ($mimeType !== self::VALID_MIME_TYPE) {

            throw new DataValidationException('The file type is not valid');
        }
    }

    /**
     * @throws DataValidationException
     */
    public function validateContent(string $content): void
    {
        $content = CustomHelper::applyTrim($content);

        if (empty($content)) {

            throw new DataValidationException('The file Content cannot be empty');
        }
    }

    /**
     * @throws DataValidationException
     */
    public function validateSizeInBytes(int $sizeInBytes): void
    {
        if ($sizeInBytes < self::MIN_SIZE_IN_BYTES || $sizeInBytes > self::MAX_SIZE_IN_BYTES) {

            throw new DataValidationException('The file size is not valid');
        }
    }
}
