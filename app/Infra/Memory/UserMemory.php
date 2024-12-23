<?php

namespace App\Infra\Memory;

use App\Domain\User\User;
use App\Domain\User\UserPersistenceInterface;

class UserMemory implements UserPersistenceInterface
{
    private array $users = [];

    public function create(User $user): void
    {
        $this->users[$user->getId()] = $user;
    }

    public function isCpfAlreadyCreated(User $user): bool
    {
        foreach ($this->users as $storedUser) {

            if ($storedUser->getCpf() === $user->getCpf()) {

                return true;
            }
        }

        return false;
    }

    public function isEmailAlreadyCreated(User $user): bool
    {
        foreach ($this->users as $storedUser) {

            if ($storedUser->getEmail() === $user->getEmail()) {

                return true;
            }
        }

        return false;
    }

    public function findAll(): array
    {
        return array_values($this->users);
    }

    public function isExistentId(User $user): bool
    {
        return isset($this->users[$user->getId()]);
    }

    public function editName(User $user): void
    {
        if (isset($this->users[$user->getId()])) {

            $this->users[$user->getId()]->setName($user->getName());
        }
    }

    public function findById(string $uuid): ?User
    {
        return $this->users[$uuid] ?? null;
    }

    public function deleteById(User $user): void
    {
        unset($this->users[$user->getId()]);
    }

    public function updateById(User $user, array $data): void
    {
        if (isset($this->users[$user->getId()])) {

            $storedUser = $this->users[$user->getId()];

            if (isset($data['name'])) {

                $storedUser->setName($data['name']);
            }

            if (isset($data['email'])) {

                $storedUser->setEmail($data['email']);
            }

            if (isset($data['cpf'])) {

                $storedUser->setCpf($data['cpf']);
            }
        }
    }
}
