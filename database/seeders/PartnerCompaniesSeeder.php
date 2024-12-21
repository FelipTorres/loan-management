<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PartnerCompaniesSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        DB::table('partner_companies')->insert([
            [
                'uuid' => Str::uuid(),
                'name' => 'Empresa A',
                'cnpj' => '12345678000199',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'Empresa B',
                'cnpj' => '98765432000188',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
