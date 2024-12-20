<?php

namespace App\Infra\Db;

use App\Domain\User\User;
use App\Domain\User\UserDataValidator;
use App\Domain\User\UserPersistenceInterface;
use Illuminate\Support\Facades\DB;
use stdClass;

class UserDb implements UserPersistenceInterface
{
    private const TABLE_NAME = 'users';
    private const COLUMN_UUID = 'uuid';
    private const COLUMN_EMAIL = 'email';
    private const COLUMN_CPF = 'cpf';
    private const COLUMN_NAME = 'name';
    private const COLUMN_CREATED_AT = 'created_at';
    private const COLUMN_UPDATED_AT = 'updated_at';
    private const COLUMN_DELETED_AT = 'deleted_at';

    public function create(User $user): void
    {
        DB::table(self::TABLE_NAME)->insert([
            self::COLUMN_UUID => $user->getId(),
            self::COLUMN_NAME => $user->getName(),
            self::COLUMN_EMAIL => $user->getEmail(),
            self::COLUMN_CPF => $user->getCpf(),
            self::COLUMN_CREATED_AT => $user->getDateCreation(),
        ]);
    }

    public function isCpfAlreadyCreated(User $user): bool
    {
        return DB::table(self::TABLE_NAME)
            ->where(self::COLUMN_CPF, $user->getCpf())
            ->whereNull(self::COLUMN_DELETED_AT)
            ->exists();
    }

    public function isEmailAlreadyCreated(User $user): bool
    {
        return DB::table(self::TABLE_NAME)
            ->where(self::COLUMN_EMAIL, $user->getEmail())
            ->whereNull(self::COLUMN_DELETED_AT)
            ->exists();
    }

    public function findAll(User $user): array
    {
        $users = [];

        $records = DB::table(self::TABLE_NAME)
            ->select([
                self::COLUMN_UUID,
                self::COLUMN_NAME,
                self::COLUMN_EMAIL,
                self::COLUMN_CPF,
            ])
            ->where([
                self::COLUMN_DELETED_AT => null
            ])
            ->get();

        foreach ($records as $record) {

            $users[] = (new User(new UserDb()))
                ->setDataValidator(new UserDataValidator())
                ->setId($record->uuid)
                ->setName($record->name)
                ->setCpf($record->cpf)
                ->setEmail($record->email);
        }

        return $users;
    }

    public function isExistentId(User $user): bool
    {
        return DB::table(self::TABLE_NAME)
            ->where(self::COLUMN_UUID, $user->getId())
            ->whereNull(self::COLUMN_DELETED_AT)
            ->exists();
    }

    public function editName(User $user): void
    {
        DB::table(self::TABLE_NAME)
            ->where(self::COLUMN_UUID, $user->getId())
            ->update([self::COLUMN_NAME => $user->getName()]);
    }

    public function findById(string $uuid): ?stdClass
    {
        return DB::table(self::TABLE_NAME)
            ->select([
                self::COLUMN_UUID,
                self::COLUMN_NAME,
                self::COLUMN_EMAIL,
                self::COLUMN_CPF,
            ])
            ->where(self::COLUMN_UUID, $uuid)
            ->whereNull(self::COLUMN_DELETED_AT)
            ->first();
    }
}
