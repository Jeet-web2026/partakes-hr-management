<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AgreementCategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tbl_mast_agmt_catg')->insert([

            // Parent Categories
            [
                'Agmt_Catg_UIN' => 1,
                'Agmt_Catg_Name' => 'Vendor Agreements',
                'Agmt_Parn_Catg_UIN' => null,
                'Stau_UIN' => 1,
                'CrBy' => 1,
                'MoBy' => null,
            ],
            [
                'Agmt_Catg_UIN' => 2,
                'Agmt_Catg_Name' => 'Finance Agreements',
                'Agmt_Parn_Catg_UIN' => null,
                'Stau_UIN' => 1,
                'CrBy' => 1,
                'MoBy' => null,
            ],
            [
                'Agmt_Catg_UIN' => 3,
                'Agmt_Catg_Name' => 'Legal Agreements',
                'Agmt_Parn_Catg_UIN' => null,
                'Stau_UIN' => 1,
                'CrBy' => 1,
                'MoBy' => null,
            ],

            // Vendor
            ['Agmt_Catg_UIN'=>4,'Agmt_Catg_Name'=>'Procurement & Supply','Agmt_Parn_Catg_UIN'=>1,'Stau_UIN'=>1,'CrBy'=>1, 'MoBy' => null],
            ['Agmt_Catg_UIN'=>5,'Agmt_Catg_Name'=>'Services','Agmt_Parn_Catg_UIN'=>1,'Stau_UIN'=>1,'CrBy'=>1, 'MoBy' => null],
            ['Agmt_Catg_UIN'=>6,'Agmt_Catg_Name'=>'Logistics','Agmt_Parn_Catg_UIN'=>1,'Stau_UIN'=>1,'CrBy'=>1, 'MoBy' => null],
            ['Agmt_Catg_UIN'=>7,'Agmt_Catg_Name'=>'Outsourcing','Agmt_Parn_Catg_UIN'=>1,'Stau_UIN'=>1,'CrBy'=>1, 'MoBy' => null],

            // Finance
            ['Agmt_Catg_UIN'=>8,'Agmt_Catg_Name'=>'Loans & Borrowings','Agmt_Parn_Catg_UIN'=>2,'Stau_UIN'=>1,'CrBy'=>1, 'MoBy' => null],
            ['Agmt_Catg_UIN'=>9,'Agmt_Catg_Name'=>'Investments','Agmt_Parn_Catg_UIN'=>2,'Stau_UIN'=>1,'CrBy'=>1, 'MoBy' => null],
            ['Agmt_Catg_UIN'=>10,'Agmt_Catg_Name'=>'Security & Guarantees','Agmt_Parn_Catg_UIN'=>2,'Stau_UIN'=>1,'CrBy'=>1, 'MoBy' => null],
            ['Agmt_Catg_UIN'=>11,'Agmt_Catg_Name'=>'Banking','Agmt_Parn_Catg_UIN'=>2,'Stau_UIN'=>1,'CrBy'=>1, 'MoBy' => null],
            ['Agmt_Catg_UIN'=>12,'Agmt_Catg_Name'=>'Leasing','Agmt_Parn_Catg_UIN'=>2,'Stau_UIN'=>1,'CrBy'=>1, 'MoBy' => null],

            // Legal
            ['Agmt_Catg_UIN'=>13,'Agmt_Catg_Name'=>'Confidentiality','Agmt_Parn_Catg_UIN'=>3,'Stau_UIN'=>1,'CrBy'=>1, 'MoBy' => null],
            ['Agmt_Catg_UIN'=>14,'Agmt_Catg_Name'=>'Compliance','Agmt_Parn_Catg_UIN'=>3,'Stau_UIN'=>1,'CrBy'=>1, 'MoBy' => null],
            ['Agmt_Catg_UIN'=>15,'Agmt_Catg_Name'=>'Risk Management','Agmt_Parn_Catg_UIN'=>3,'Stau_UIN'=>1,'CrBy'=>1, 'MoBy' => null],
            ['Agmt_Catg_UIN'=>16,'Agmt_Catg_Name'=>'Dispute Resolution','Agmt_Parn_Catg_UIN'=>3,'Stau_UIN'=>1,'CrBy'=>1, 'MoBy' => null],
        ]);
    }
}