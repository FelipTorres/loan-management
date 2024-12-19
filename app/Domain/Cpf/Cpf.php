<?php

namespace App\Domain\Cpf;

class Cpf
{
    private const MAX_LENGTH = 11;

    private string $cpf;

    public function __construct(string $cpf)
    {
        $this->cpf = $cpf;
    }

    public function isValid(): bool
    {
        $isValidLength = $this->isValidLength();
        $isValidNumber = $this->isValidNumber();

        return $isValidLength && $isValidNumber;
    }

    private function isValidLength(): bool
    {
        return strlen($this->cpf) === self::MAX_LENGTH;
    }

    private function isValidNumber(): bool
    {
        $cpf = $this->cpf;

        $cpf = preg_replace('/[^0-9]/', '', $cpf);

        if (preg_match('/(\d)\1{10}/', $cpf)) {

            return false;
        }

        for ($position = 9; $position < 11; $position++) {

            $sum = 0;

            for ($index = 0; $index < $position; $index++) {

                $sum += $this->cpf[$index] * (($position + 1) - $index);
            }

            $digit = ((10 * $sum) % 11) % 10;

            if ($this->cpf[$index] != $digit) {

                return false;
            }
        }

        return true;
    }
}
