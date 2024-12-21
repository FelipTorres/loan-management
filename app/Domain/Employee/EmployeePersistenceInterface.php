<?php

namespace App\Domain\Employee;

interface EmployeePersistenceInterface
{
    public function findByUserId(string $userUuid): ?Employee;
}
