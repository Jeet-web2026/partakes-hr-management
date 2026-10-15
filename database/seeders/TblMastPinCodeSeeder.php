<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TblMastPinCodeSeeder extends Seeder
{
    /**
     * Number of rows inserted per DB::table()->insert() call.
     */
    private const CHUNK_SIZE = 500;

    /**
     * Run the database seeds.
     *
     * Loads data/tbl_mast_pin_code.csv (21,161 rows exported from the
     * original SQL dump) and bulk-inserts it in chunks to avoid
     * building one giant query or one huge in-memory array at once.
     */
    public function run(): void
    {
        $path = __DIR__ . '/data/tbl_mast_pin_code.csv';

        if (! file_exists($path)) {
            $this->command->error("Seed file not found: {$path}");
            return;
        }

        $handle = fopen($path, 'r');
        $header = fgetcsv($handle); // ['Pin_code_UIN', 'pin_code_No', ...]

        $buffer = [];
        $total = 0;

        // Truncate first so re-running the seeder is idempotent.
        DB::table('tbl_mast_pin_code')->truncate();

        while (($row = fgetcsv($handle)) !== false) {
            $buffer[] = array_combine($header, $row);

            if (count($buffer) >= self::CHUNK_SIZE) {
                DB::table('tbl_mast_pin_code')->insertOrIgnore($buffer);
                $total += count($buffer);
                $buffer = [];
            }
        }

        // Insert any remaining rows.
        if (! empty($buffer)) {
            DB::table('tbl_mast_pin_code')->insertOrIgnore($buffer);
            $total += count($buffer);
        }

        fclose($handle);

        $this->command->info("Seeded {$total} rows into tbl_mast_pin_code.");
    }
}