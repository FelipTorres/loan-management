<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EmployeesSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('employees')->insert([
            [
                'uuid' => Str::uuid(),
                'user_id' => 'c970314e-adb1-3def-b3b0-5d438a4e06d2',
                'company_id' => 'bf7eed73-f204-464f-8201-e4776766112b',
                'hire_date' => $now->subMonths(12),
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'uuid' => Str::uuid(),
                'user_id' => '4ee0c242-3748-3c0d-937f-26c2a375fed7',
                'company_id' => 'd542e322-711c-4fb8-8d7f-1441fa511260',
                'hire_date' => $now->subMonths(8),
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'uuid' => Str::uuid(),
                'user_id' => '21b5e10f-b9d1-33ac-b646-19f60f37be26',
                'company_id' => 'bf7eed73-f204-464f-8201-e4776766112b',
                'hire_date' => $now->subYears(2),
                'is_active' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'uuid' => Str::uuid(),
                'user_id' => '26b593b5-c356-3b56-8468-6e1469bd3715',
                'company_id' => 'd542e322-711c-4fb8-8d7f-1441fa511260',
                'hire_date' => $now->subMonths(5),
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'uuid' => Str::uuid(),
                'user_id' => '52eaf214-cda2-3472-a670-4d06bd5149fa',
                'company_id' => 'bf7eed73-f204-464f-8201-e4776766112b',
                'hire_date' => $now->subYears(10),
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'uuid' => Str::uuid(),
                'user_id' => '1417e15b-baad-3608-8e58-dd51704d4225',
                'company_id' => 'd542e322-711c-4fb8-8d7f-1441fa511260',
                'hire_date' => $now->subYears(7),
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'uuid' => Str::uuid(),
                'user_id' => '4f216027-fdb2-361b-b2bc-71232af39a17',
                'company_id' => 'bf7eed73-f204-464f-8201-e4776766112b',
                'hire_date' => $now->subYears(15),
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
