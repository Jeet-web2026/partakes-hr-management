<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            OrganizationSeeder::class,
            UserOrgaRelaSeeder::class,
            TblMastPinCodeSeeder::class,
            CountryIndiaSeeder::class,
            AgreementCategorySeeder::class,
            AgreementTypeSeeder::class,
            MasterVerticalPageNameSeeder::class,
            MastUserLogiSeeder::class,
            MastStauSeeder::class,
            MastPageSeeder::class
        ]);
    }
}
