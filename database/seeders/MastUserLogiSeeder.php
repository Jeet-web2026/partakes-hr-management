<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MastUserLogiSeeder extends Seeder
{
    public function run(): void
    {
        // updateOrInsert keeps the seeder safe to re-run (no duplicate-key error)
        DB::table('tbl_mast_user_logi')->updateOrInsert(
            ['User_UIN' => '1500000001'],
            [
                'User_Name'       => 'Jit Nath',
                'User_Logo'       => '',
                'Prmy_Emai'       => 'jeetnath2110@gmail.com',
                'Prmy_Emai_Vefi'  => null,
                'Altn_Emai'       => '',
                'Altn_Emai_Vefi'  => null,

                // NOT NULL integer columns: 0 instead of null / ''
                'Cutr_UIN'        => 0,
                'ISD_UIN'         => 0,
                'Prmy_MoNo'       => 0,
                'Prmy_MoNo_Vefi'  => 0,

                'OTP_Prmy_MoNo'   => null,
                'Altn_MoNo'       => null,
                'Altn_MoNo_Vefi'  => null,
                'OTP_Altn_MoNo'   => null,
                'User_pasw'       => '',
                'Pasw_Self_Gntd'  => null,
                'Pasw_Rest_By'    => null,
                'Pasw_Rest_On'    => null,
                'CrBy'            => null,
                'Stau_UIN'        => null,
            ]
        );
    }
}
