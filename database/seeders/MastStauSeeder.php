<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MastStauSeeder extends Seeder
{
    public function run(): void
    {
        $org = 101;

        $rows = [
            [2,      'Blank',      null,                                    103],
            [101,    'System Own', 'System Keep the owner',                 102],
            [100201, 'Active',     'General User can Select',               103],
            [100202, 'Inactive',   'General User can Select',               103],
            [100203, 'Update',     'General User can only Update',          103],
            [100204, 'Pending',    'General User can use',                  103],
            [100205, 'Close',      'General User can View',                 103],
            [100206, 'Not Verify', 'General User can use',                  103],
            [100207, 'Verified',   'General User can use',                  103],
            [100208, 'Audited',    'General User can use',                  103],
            [100209, 'Delete',     'General User can not use',              103],
            [100210, 'Hide',       null,                                    103],
        ];

        $data = array_map(fn($r) => [
            'Stau_UIN'  => $r[0],
            'Orga_UIN'  => $org,
            'Stau_Name' => $r[1],
            'Stau_Desp' => $r[2],
            'CrBy'      => $r[3],
            'MoBy'      => $r[3],
            'MoOn'      => null,
            'VfOn'      => null,
            'VfBy'      => $r[3],
        ], $rows);

        DB::table('tbl_mast_stau')->upsert(
            $data,
            ['Stau_UIN'],
            ['Orga_UIN', 'Stau_Name', 'Stau_Desp', 'CrBy', 'MoBy', 'MoOn', 'VfOn', 'VfBy']
        );
    }
}
