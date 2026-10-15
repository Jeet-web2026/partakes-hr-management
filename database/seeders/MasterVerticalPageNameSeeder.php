<?php

namespace Database\Seeders;

use App\Models\MasterVerticalPageName;
use Illuminate\Database\Seeder;

class MasterVerticalPageNameSeeder extends Seeder
{
    public function run(): void
    {
        MasterVerticalPageName::create([
            'Page_name' => 'Feedback',
            'Page_Desp' => 'Feedback page',

            'Dvlp_name' => 0,

            'Data_Hit' => 1,
            'Walt_Chag_Pont' => 1,
            'Rate_Pont' => 1,

            'Data_Add' => 1,
            'Data_Updt' => 1,
            'Data_Del' => 1,
            'Data_VfBy' => 1,
            'Data_Adit' => 1,
            'Data_Prnt' => 1,
            'Data_Expt' => 1,
            'Data_Prnt_Scrn' => 1,
            'Data_Sele_Copy' => 1,
            'Data_Pste' => 1,
            'Data_Link' => 1,

            'CrBy' => 1,
            'CrOn' => now()->timestamp,

            'MoBy' => 1,
            'MoOn' => now()->timestamp,

            'VfBy' => 1,
            'VfOn' => now()->timestamp,
        ]);
    }
}