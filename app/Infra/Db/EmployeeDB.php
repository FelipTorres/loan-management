<?php

namespace App\Infra\Db;

use App\Domain\Employee\Employee;
use App\Domain\Employee\EmployeePersistenceInterface;
use Illuminate\Support\Facades\DB;

class EmployeeDB implements EmployeePersistenceInterface
{
    private const TABLE_NAME = 'employees';
    private const COLUMN_USER_ID = 'user_id';
    private const COLUMN_IS_ACTIVE = 'is_active';
    private const COLUMN_DELETED_AT = 'deleted_at';

    public function findByUserId(string $userUuid): ?Employee
    {
        $employeeStd = DB::table(self::TABLE_NAME)
            ->where(self::COLUMN_USER_ID, $userUuid)
            ->where(self::COLUMN_IS_ACTIVE, true)
            ->whereNull(self::COLUMN_DELETED_AT)
            ->first();

        return $employeeStd ? (new Employee(new EmployeeDB()))
            ->setId($employeeStd->uuid)
            ->setUserId($employeeStd->user_id)
            ->setCompanyId($employeeStd->company_id)
            ->setHireDate($employeeStd->hire_date)
            ->setIsActive($employeeStd->is_active)
            ->setDateCreation($employeeStd->created_at) : null;
    }
}
