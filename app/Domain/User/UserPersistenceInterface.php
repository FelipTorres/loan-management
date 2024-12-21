<?php

namespace App\Domain\User;

interface UserPersistenceInterface
{
    public function create(User $user): void;
    public function isCpfAlreadyCreated(User $user): bool;
    public function isEmailAlreadyCreated(User $user): bool;
    public function findAll(User $user): array;
    public function isExistentId(User $user): bool;
    public function editName(User $user): void;
    public function findById(string $uuid): ?User;
    public function deleteById(User $user): void;
    public function updateById(User $user, array $data): void;
}
