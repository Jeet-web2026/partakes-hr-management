<?php

namespace Database\Seeders;

use App\Models\MastStat;
use Illuminate\Database\Seeder;

class MastStatSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            // placeholder rows from the original data
            [
                'Stat_UIN' => 2,
                'Cutr_UIN' => 2,
                'Stat_Comp_Name' => 'Blank',
                'Stat_Type' => 'Blank',
                'Stat_Shrt_Name' => 'Short',
                'City_UIN' => 0,
                'Stau_UIN' => 101,
                'CrBy' => 102,
                'MoBy' => 102,
                'MoOn' => null,
                'VfOn' => null,
                'VfBy' => 102,
            ],
            [
                'Stat_UIN' => 4,
                'Cutr_UIN' => 4,
                'Stat_Comp_Name' => 'Not Mentioned',
                'Stat_Type' => 'Blank',
                'Stat_Shrt_Name' => 'Short',
                'City_UIN' => 0,
                'Stau_UIN' => 101,
                'CrBy' => 102,
                'MoBy' => 102,
                'MoOn' => null,
                'VfOn' => null,
                'VfBy' => 102,
            ],
        ];

        $states = [
            102001 => ['Andaman & Nicobar Is', 'Union Territory'],
            102002 => ['Andhra Pradesh', 'State'],
            102003 => ['Arunachal Pradesh', 'State'],
            102004 => ['Assam', 'State'],
            102005 => ['Bihar', 'State'],
            102006 => ['Chandigarh', 'State'],
            102007 => ['Chattisgarh', 'State'],
            102008 => ['Dadra & Nagar Haveli', 'Union Territory'],
            102009 => ['Daman & Diu', 'Union Territory'],
            102010 => ['Delhi', 'Union Territory'],
            102011 => ['Goa', 'State'],
            102012 => ['Gujarat', 'State'],
            102013 => ['Haryana', 'State'],
            102014 => ['Himachal Pradesh', 'State'],
            102015 => ['Jammu & Kashmir', 'State'],
            102016 => ['Jharkhand', 'State'],
            102017 => ['Karnataka', 'State'],
            102018 => ['Kerala', 'State'],
            102019 => ['Lakshadweep', 'Union Territory'],
            102020 => ['Madhya Pradesh', 'State'],
            102021 => ['Maharashtra', 'State'],
            102022 => ['Manipur', 'State'],
            102023 => ['Meghalaya', 'State'],
            102024 => ['Mizoram', 'State'],
            102025 => ['Nagaland', 'State'],
            102026 => ['Odisha', 'State'],
            102027 => ['Pondicherry', 'State'],
            102028 => ['Punjab', 'State'],
            102029 => ['Rajasthan', 'State'],
            102030 => ['Sikkim', 'State'],
            102031 => ['Tamil Nadu', 'State'],
            102032 => ['Telangana', 'State'],
            102033 => ['Tripura', 'State'],
            102034 => ['Uttar Pradesh', 'State'],
            102035 => ['Uttarakhand', 'State'],
            102036 => ['West Bengal', 'State'],
        ];

        foreach ($states as $uin => [$name, $type]) {
            $rows[] = [
                'Stat_UIN' => $uin,
                'Cutr_UIN' => 101107,
                'Stat_Comp_Name' => $name,
                'Stat_Type' => $type,
                'Stat_Shrt_Name' => 'Short',
                'City_UIN' => 0,
                'Stau_UIN' => 100201,
                'CrBy' => 103,
                'MoBy' => 2,
                'MoOn' => null,
                'VfOn' => null,
                'VfBy' => 2,
            ];
        }

        MastStat::upsert(
            $rows,
            ['Stat_UIN'],
            array_diff(array_keys($rows[0]), ['Stat_UIN'])
        );
    }
}
