<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tbl_mast_orga')->insert([
            [
                'Orga_UIN' => 1724774817,
                'Orga_Type_UIN' => 1724774817,

                'Orga_Name' => 'ABC Technologies',
                'orga_Logo' => 'abc-logo.png',

                'Dt_of_Incp' => strtotime('2020-01-15'),
                'Fnal_Year_Strt_Dt' => strtotime('2025-04-01'),
                'Actn_Strt_Dt' => strtotime('2020-02-01'),

                'Web_Addr' => 'abc.com',

                'Regi_Addr' => 'Salt Lake, Kolkata, West Bengal',

                'Cnta_Pers' => 101,

                'Admt_Pers_1st' => 1,
                'Admt_Pers_2nd' => 2,
                'Admt_Pers_3rd' => 3,
                'Admt_Pers_4th' => 4,

                'Assc_Part_Code' => 1001,

                'Sytm_Admt_Pers' => 1,

                'Eft_Dt' => Carbon::now(),

                'Renw_Dt' => strtotime('2026-01-15'),
                'Expy_Dt' => strtotime('2027-01-15'),
                'Orga_Del_Dt' => 0,

                'Stau_UIN' => 1,

                'CrBy' => 1,
                'CrOn' => time(),

                'MoBy' => null,
                'MoOn' => null,

                'VfOn' => null,
                'VfBy' => null,

                'Stat_UIN' => 1,

                'pin_UIN' => '700001',

                'Prmy_Emai' => 'info@abc.com',

                'Prmy_Emai_Vefi' => 1,

                'mpos_key' => 123456,
            ],

            [
                'Orga_UIN' => 1500000046,
                'Orga_Type_UIN' => 1500000001,

                'Orga_Name' => 'XYZ Solutions',
                'orga_Logo' => 'xyz-logo.png',

                'Dt_of_Incp' => strtotime('2018-05-20'),
                'Fnal_Year_Strt_Dt' => strtotime('2025-04-01'),
                'Actn_Strt_Dt' => strtotime('2018-06-01'),

                'Web_Addr' => 'xyz.com',

                'Regi_Addr' => 'New Town, Kolkata, West Bengal',

                'Cnta_Pers' => 102,

                'Admt_Pers_1st' => 5,
                'Admt_Pers_2nd' => 6,
                'Admt_Pers_3rd' => 7,
                'Admt_Pers_4th' => 8,

                'Assc_Part_Code' => 1002,

                'Sytm_Admt_Pers' => 1,

                'Eft_Dt' => Carbon::now(),

                'Renw_Dt' => strtotime('2026-05-20'),
                'Expy_Dt' => strtotime('2027-05-20'),
                'Orga_Del_Dt' => 0,

                'Stau_UIN' => 1,

                'CrBy' => 1,
                'CrOn' => time(),

                'MoBy' => null,
                'MoOn' => null,

                'VfOn' => null,
                'VfBy' => null,

                'Stat_UIN' => 1,

                'pin_UIN' => '700156',

                'Prmy_Emai' => 'contact@xyz.com',

                'Prmy_Emai_Vefi' => 1,

                'mpos_key' => 654321,
            ]
        ]);
    }
}