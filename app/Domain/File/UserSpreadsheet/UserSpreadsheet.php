<?php

namespace App\Domain\File\UserSpreadsheet;

use app\Domain\File\Csv\CsvInterface;
use App\Domain\User\User;
use App\Domain\User\UserPersistenceInterface;
use App\Domain\Uuid\UuidGeneratorInterface;
use App\Exceptions\DuplicatedDataException;
use App\Exceptions\UserSpreadsheetException;
use App\Http\Helpers\CustomHelper;
use App\Infra\Db\UserDb;
use App\Infra\File\Csv\Csv;
use App\Infra\Uuid\UuidGenerator;

class UserSpreadsheet
{
    public const SPREADSHEET_LINE_NUMBER = 'line_number';
    public const NAME_HEADER = 'name';
    public const CPF_HEADER = 'cpf';
    public const EMAIL_HEADER = 'email';
    public const HEADERS = [
        self::NAME_HEADER,
        self::CPF_HEADER,
        self::EMAIL_HEADER,
    ];
    private array $users;
    private UuidGeneratorInterface $uuidGenerator;
    private UserPersistenceInterface $userPersistence;
    private CsvInterface $csv;

    public function setItemsUserSpreadSheet(Csv $csv): void
    {
        $this->setUuidGenerator(new UuidGenerator())
            ->setUserPersistence(new UserDb())
            ->setCsv($csv);
    }

    public function setUuidGenerator(UuidGeneratorInterface $uuidGenerator): self
    {
        $this->uuidGenerator = $uuidGenerator;

        return $this;
    }

    public function setUserPersistence(UserPersistenceInterface $userPersistence): self
    {
        $this->userPersistence = $userPersistence;

        return $this;
    }

    public function setUsers(array $users): self
    {
        $this->users = $users;

        return $this;
    }

    public function setCsv(CsvInterface $csv): self
    {
        $this->csv = $csv;

        return $this;
    }

    /**
     * @throws UserSpreadsheetException
     */
    public function buildUsersFromContent(): array
    {
        $this->csv->setExpectedHeaders(self::HEADERS)
            ->buildAssociativeArrayFromContent();

        return $this->createUserSpreadSheet();
    }

    private function trimArrayKeys(array $array): array
    {
        $item = CustomHelper::cleanValues(array_keys($array));

        return array_combine($item, $array);
    }

    /**
     * @throws UserSpreadsheetException
     */
    private function createUserSpreadSheet(): array
    {
        $users = [];

        foreach ($this->csv->getAssociativeContent() as $userData) {

            $userData = $this->trimArrayKeys($userData);

            $users[] = $this->buildUserFromUserData($userData);
        }

        return $users;
    }

    /**
     * @throws UserSpreadsheetException
     */
    private function buildUserFromUserData(array $userData): User
    {
        try {
            $user = new User($this->userPersistence);

            $name = utf8_decode($userData[self::NAME_HEADER]);
            $cpf = $userData[self::CPF_HEADER];
            $email = $userData[self::EMAIL_HEADER];

            $user->setItemsUser($this->uuidGenerator, $name, $cpf, $email);

            $user->verifyAlreadyCreatedCpf();
            $user->verifyAlreadyCreatedEmail();

            return $user;

        } catch (DuplicatedDataException $e) {

            throw new UserSpreadsheetException("Spreadsheet error: line {$userData[self::SPREADSHEET_LINE_NUMBER]} | {$e->getMessage()}");
        }
    }
}
