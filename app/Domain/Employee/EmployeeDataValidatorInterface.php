<?php

namespace App\Domain\Employee;

interface EmployeeDataValidatorInterface
{
    public function validateEmployeeExists(?Employee $user): void;
}
