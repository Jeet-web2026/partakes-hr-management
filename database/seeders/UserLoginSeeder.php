<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserLoginSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tbl_mast_user_logi')->insert([
            [
                'User_UIN' => 1500000001,
                'User_Name'          => 'Admin User',
                'User_Logo'          => 'default-user.png',

                'Prmy_Emai'          => 'admin@example.com',
                'Prmy_Emai_Vefi'     => 1,

                'Altn_Emai'          => 'admin2@example.com',
                'Altn_Emai_Vefi'     => 1,

                'Cutr_UIN'           => 101,
                'ISD_UIN'            => 91,

                'Prmy_MoNo'          => 9876543210,
                'Prmy_MoNo_Vefi'     => 1,
                'OTP_Prmy_MoNo'      => 123456,

                'Altn_MoNo'          => 9123456789,
                'Altn_MoNo_Vefi'     => 1,
                'OTP_Altn_MoNo'      => 654321,

                'User_pasw'          => Hash::make('password'),

                'Pasw_Self_Gntd'     => 1,
                'Pasw_Rest_By'       => null,
                'Pasw_Rest_On'       => null,

                'CrBy'               => 1,
                'Stau_UIN'           => 1,
            ]
        ]);
    }
}
