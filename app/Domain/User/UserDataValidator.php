<?php

namespace App\Domain\User;

use App\Domain\Cpf\Cpf;
use App\Exceptions\DataValidationException;

class UserDataValidator implements UserDataValidatorInterface
{
    private const ID_MAX_LENGTH = 36;
    private const NAME_MAX_LENGTH = 100;
    private const EMAIL_MAX_LENGTH = 100;

    private const UUID_REGEX = '/[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}/';

    /**
     * @throws DataValidationException
     */
    public function validateId(string $id): void
    {
        if (!preg_match(self::UUID_REGEX, $id)) {

            throw new DataValidationException('The user ID is not a valid UUID');
        }

        if (empty($id) || strlen($id) > self::ID_MAX_LENGTH) {

            throw new DataValidationException('The user ID is not valid');
        }
    }

    /**
     * @throws DataValidationException
     */
    public function validateName(string $name): void
    {
        if (empty($name)) {

            throw new DataValidationException('The user name cannot be empty');
        }

        if (strlen($name) > self::NAME_MAX_LENGTH) {

            throw new DataValidationException('The user name exceeds the max length');
        }
    }

    /**
     * @throws DataValidationException
     */
    public function validateEmail(string $email): void
    {
        if (strlen($email) > self::EMAIL_MAX_LENGTH) {

            throw new DataValidationException('The user email exceeds the max length');
        }

        if (empty($email)) {

            throw new DataValidationException('The user email cannot be empty');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

            throw new DataValidationException('The user email is not valid');
        }
    }

    /**
     * @throws DataValidationException
     */
    public function validateCpf(string $cpf): void
    {
        $trimmedCpf = trim($cpf);

        if (empty($trimmedCpf)) {

            throw new DataValidationException('The user cpf cannot be empty');
        }

        if (!is_numeric($trimmedCpf) || !(new Cpf($trimmedCpf))->isValid()) {

            throw new DataValidationException('The user cpf is not valid');
        }
    }

    /**
     * @throws DataValidationException
     */
    public function validateDateCreation(string $dateCreation): void
    {
        if (empty($dateCreation)) {

            throw new DataValidationException('The user date creation cannot be empty');
        }

        if (!\DateTime::createFromFormat('Y-m-d H:i:s', $dateCreation)) {

            throw new DataValidationException('The user date creation is not in a valid format');
        }
    }

    /**
     * @throws DataValidationException
     */
    public function validateDateEdition(string $dateEdition): void
    {
        if (empty($dateEdition)) {

            throw new DataValidationException('The user date edition cannot be empty');
        }

        if (!\DateTime::createFromFormat('Y-m-d H:i:s', $dateEdition)) {

            throw new DataValidationException('The user date edition is not in a valid format');
        }
    }
}
