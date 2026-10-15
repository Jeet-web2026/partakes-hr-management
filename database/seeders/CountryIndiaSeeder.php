<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountryIndiaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tbl_mast_cutr')->updateOrInsert(
            ['Code' => 'IN'],
            [
                'Name'         => 'India',
                'Code'         => 'IN',
                'Phon_Code'    => '+91',
                'Flag_Emoj'    => '🇮🇳',
                'MoNo_Digt'    => 10,
                'Stau_UIN'     => 100201,
                'CrBy'         => 'system',
                'CrOn'         => now(),
            ]
        );
    }
}
