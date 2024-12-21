<?php

namespace App\Domain\Employee;

use App\Exceptions\UserNotFoundException;
use Carbon\Carbon;

class Employee
{
    private string $id;
    private string $user_id;
    private string $company_id;
    private string $hire_date;
    private int $is_active;
    private string $date_creation;
    private EmployeeDataValidatorInterface $dataValidator;
    private EmployeePersistenceInterface $persistence;

    public function __construct(EmployeePersistenceInterface $persistence)
    {
        $this->persistence = $persistence;
    }

    public function setDataValidator(EmployeeDataValidatorInterface $dataValidator): Employee
    {
        $this->dataValidator = $dataValidator;

        return $this;
    }

    public function getDataValidator(): EmployeeDataValidator
    {
        return $this->dataValidator;
    }


    public function setId(string $id): Employee
    {
        $this->id = $id;

        return $this;
    }

    public function setUserId(string $userId): Employee
    {
        $this->user_id = $userId;

        return $this;
    }

    public function setCompanyId(string $companyId): Employee
    {
        $this->company_id = $companyId;

        return $this;
    }

    public function setHireDate(string $hireDate): Employee
    {
        $this->hire_date = $hireDate;

        return $this;
    }

    public function setIsActive(int $is_active): Employee
    {
        $this->is_active = $is_active;

        return $this;
    }

    public function setDateCreation(string $date_creation): Employee
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    /**
     * @throws UserNotFoundException
     */
    public function findByUserId(string $userId): Employee
    {
        $this->setDataValidator(new EmployeeDataValidator());

        $employee = $this->persistence->findByUserId($userId);

        $this->getDataValidator()->validateEmployeeExists($employee);

        return $employee;
    }

    public function checkIfEmployeeGetConsignedCredit(): array
    {
        $hireDate = $this->hire_date;

        $monthsSinceHire = Carbon::now()->diffInMonths($hireDate);

        if ($monthsSinceHire < 6) {

            return $this->buildEmployeeResponse(false, 'Employee does not have sufficient time in the job (minimum of 6 months).');
        }

        if ($monthsSinceHire > 120) {

            return $this->buildEmployeeResponse(false, 'Employee exceeds the permitted time limit for admission (10 years).');
        }

        return $this->buildEmployeeResponse(true, 'Employee is eligible for consigned credit.');
    }

    public function buildEmployeeResponse(bool $eligible, string $message): array
    {
        return [
            'eligible' => $eligible,
            'message' => $message
        ];
    }
}
