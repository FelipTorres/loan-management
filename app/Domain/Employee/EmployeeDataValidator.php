<?php

namespace App\Domain\Employee;

use App\Exceptions\UserNotFoundException;

class EmployeeDataValidator implements EmployeeDataValidatorInterface
{
    /**
     * @throws UserNotFoundException
     */
    public function validateEmployeeExists(?Employee $user): void
    {
        if (!$user) {

            throw new UserNotFoundException('Employee not active in any partner company.');
        }
    }
}
