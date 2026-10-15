<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AgreementTypeSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tbl_mast_agmt_type')->insert([

            // Procurement & Supply
            ['Agmt_Catg_UIN'=>4,'Agmt_Type_Name'=>'Vendor Agreement','Stau_UIN'=>1,'CrBy'=>1],
            ['Agmt_Catg_UIN'=>4,'Agmt_Type_Name'=>'Supplier Agreement','Stau_UIN'=>1,'CrBy'=>1],
            ['Agmt_Catg_UIN'=>4,'Agmt_Type_Name'=>'Purchase Agreement','Stau_UIN'=>1,'CrBy'=>1],
            ['Agmt_Catg_UIN'=>4,'Agmt_Type_Name'=>'Procurement Agreement','Stau_UIN'=>1,'CrBy'=>1],
            ['Agmt_Catg_UIN'=>4,'Agmt_Type_Name'=>'Material Supply Agreement','Stau_UIN'=>1,'CrBy'=>1],
            ['Agmt_Catg_UIN'=>4,'Agmt_Type_Name'=>'Rate Contract Agreement','Stau_UIN'=>1,'CrBy'=>1],

            // Services
            ['Agmt_Catg_UIN'=>5,'Agmt_Type_Name'=>'Service Agreement','Stau_UIN'=>1,'CrBy'=>1],
            ['Agmt_Catg_UIN'=>5,'Agmt_Type_Name'=>'Annual Maintenance Contract (AMC)','Stau_UIN'=>1,'CrBy'=>1],
            ['Agmt_Catg_UIN'=>5,'Agmt_Type_Name'=>'Facility Management Agreement','Stau_UIN'=>1,'CrBy'=>1],
            ['Agmt_Catg_UIN'=>5,'Agmt_Type_Name'=>'Security Service Agreement','Stau_UIN'=>1,'CrBy'=>1],
            ['Agmt_Catg_UIN'=>5,'Agmt_Type_Name'=>'Housekeeping Agreement','Stau_UIN'=>1,'CrBy'=>1],
            ['Agmt_Catg_UIN'=>5,'Agmt_Type_Name'=>'IT Support Agreement','Stau_UIN'=>1,'CrBy'=>1],

            // Logistics
            ['Agmt_Catg_UIN'=>6,'Agmt_Type_Name'=>'Transportation Agreement','Stau_UIN'=>1,'CrBy'=>1],
            ['Agmt_Catg_UIN'=>6,'Agmt_Type_Name'=>'Courier Agreement','Stau_UIN'=>1,'CrBy'=>1],
            ['Agmt_Catg_UIN'=>6,'Agmt_Type_Name'=>'Warehouse Agreement','Stau_UIN'=>1,'CrBy'=>1],
            ['Agmt_Catg_UIN'=>6,'Agmt_Type_Name'=>'Freight Agreement','Stau_UIN'=>1,'CrBy'=>1],

            // Outsourcing
            ['Agmt_Catg_UIN'=>7,'Agmt_Type_Name'=>'Business Process Outsourcing (BPO) Agreement','Stau_UIN'=>1,'CrBy'=>1],
            ['Agmt_Catg_UIN'=>7,'Agmt_Type_Name'=>'Staffing Agreement','Stau_UIN'=>1,'CrBy'=>1],
            ['Agmt_Catg_UIN'=>7,'Agmt_Type_Name'=>'Manpower Supply Agreement','Stau_UIN'=>1,'CrBy'=>1],

            // Loans
            ['Agmt_Catg_UIN'=>8,'Agmt_Type_Name'=>'Loan Agreement','Stau_UIN'=>1,'CrBy'=>1],
            ['Agmt_Catg_UIN'=>8,'Agmt_Type_Name'=>'Working Capital Agreement','Stau_UIN'=>1,'CrBy'=>1],
            ['Agmt_Catg_UIN'=>8,'Agmt_Type_Name'=>'Overdraft Agreement','Stau_UIN'=>1,'CrBy'=>1],
            ['Agmt_Catg_UIN'=>8,'Agmt_Type_Name'=>'Credit Facility Agreement','Stau_UIN'=>1,'CrBy'=>1],

            // Investments
            ['Agmt_Catg_UIN'=>9,'Agmt_Type_Name'=>'Investment Agreement','Stau_UIN'=>1,'CrBy'=>1],
            ['Agmt_Catg_UIN'=>9,'Agmt_Type_Name'=>'Share Subscription Agreement','Stau_UIN'=>1,'CrBy'=>1],
            ['Agmt_Catg_UIN'=>9,'Agmt_Type_Name'=>'Shareholders Agreement','Stau_UIN'=>1,'CrBy'=>1],
            ['Agmt_Catg_UIN'=>9,'Agmt_Type_Name'=>'Venture Capital Agreement','Stau_UIN'=>1,'CrBy'=>1],

            // Security
            ['Agmt_Catg_UIN'=>10,'Agmt_Type_Name'=>'Corporate Guarantee Agreement','Stau_UIN'=>1,'CrBy'=>1],
            ['Agmt_Catg_UIN'=>10,'Agmt_Type_Name'=>'Personal Guarantee Agreement','Stau_UIN'=>1,'CrBy'=>1],
            ['Agmt_Catg_UIN'=>10,'Agmt_Type_Name'=>'Mortgage Agreement','Stau_UIN'=>1,'CrBy'=>1],
            ['Agmt_Catg_UIN'=>10,'Agmt_Type_Name'=>'Pledge Agreement','Stau_UIN'=>1,'CrBy'=>1],

            // Banking
            ['Agmt_Catg_UIN'=>11,'Agmt_Type_Name'=>'Banking Service Agreement','Stau_UIN'=>1,'CrBy'=>1],
            ['Agmt_Catg_UIN'=>11,'Agmt_Type_Name'=>'Escrow Agreement','Stau_UIN'=>1,'CrBy'=>1],
            ['Agmt_Catg_UIN'=>11,'Agmt_Type_Name'=>'Payment Gateway Agreement','Stau_UIN'=>1,'CrBy'=>1],

            // Leasing
            ['Agmt_Catg_UIN'=>12,'Agmt_Type_Name'=>'Equipment Finance Agreement','Stau_UIN'=>1,'CrBy'=>1],
            ['Agmt_Catg_UIN'=>12,'Agmt_Type_Name'=>'Asset Leasing Agreement','Stau_UIN'=>1,'CrBy'=>1],
            ['Agmt_Catg_UIN'=>12,'Agmt_Type_Name'=>'Vehicle Finance Agreement','Stau_UIN'=>1,'CrBy'=>1],

            // Confidentiality
            ['Agmt_Catg_UIN'=>13,'Agmt_Type_Name'=>'Non-Disclosure Agreement (NDA)','Stau_UIN'=>1,'CrBy'=>1],
            ['Agmt_Catg_UIN'=>13,'Agmt_Type_Name'=>'Mutual NDA','Stau_UIN'=>1,'CrBy'=>1],
            ['Agmt_Catg_UIN'=>13,'Agmt_Type_Name'=>'Employee NDA','Stau_UIN'=>1,'CrBy'=>1],

            // Compliance
            ['Agmt_Catg_UIN'=>14,'Agmt_Type_Name'=>'Data Processing Agreement (DPA)','Stau_UIN'=>1,'CrBy'=>1],
            ['Agmt_Catg_UIN'=>14,'Agmt_Type_Name'=>'GDPR Compliance Agreement','Stau_UIN'=>1,'CrBy'=>1],
            ['Agmt_Catg_UIN'=>14,'Agmt_Type_Name'=>'Regulatory Compliance Agreement','Stau_UIN'=>1,'CrBy'=>1],

            // Risk
            ['Agmt_Catg_UIN'=>15,'Agmt_Type_Name'=>'Indemnity Agreement','Stau_UIN'=>1,'CrBy'=>1],
            ['Agmt_Catg_UIN'=>15,'Agmt_Type_Name'=>'Hold Harmless Agreement','Stau_UIN'=>1,'CrBy'=>1],
            ['Agmt_Catg_UIN'=>15,'Agmt_Type_Name'=>'Liability Waiver Agreement','Stau_UIN'=>1,'CrBy'=>1],

            // Dispute
            ['Agmt_Catg_UIN'=>16,'Agmt_Type_Name'=>'Settlement Agreement','Stau_UIN'=>1,'CrBy'=>1],
            ['Agmt_Catg_UIN'=>16,'Agmt_Type_Name'=>'Arbitration Agreement','Stau_UIN'=>1,'CrBy'=>1],
            ['Agmt_Catg_UIN'=>16,'Agmt_Type_Name'=>'Mediation Agreement','Stau_UIN'=>1,'CrBy'=>1],
        ]);
    }
}