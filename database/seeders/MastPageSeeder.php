<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MastPageSeeder extends Seeder
{
    private const DEVELOPER = 1500000005;

    private const PERM_COLUMNS = [
        'Data_Add',
        'Data_Updt',
        'Data_Del',
        'Data_VfBy',
        'Data_Adit',
        'Data_Prnt',
        'Data_Expt',
        'Data_Prnt_Scrn',
        'Data_Sele_Copy',
        'Data_Pste',
        'Data_Link',
    ];

    public function run(): void
    {
        // [uin, name, description, hit, wallet points, rate points, permission values (same order as PERM_COLUMNS)]
        $pageA = [100211, 100211, 100211, 100211, 100213, 100213, 100211, 100213, 100213, 100211, 100213];
        $pageB = [100211, 100213, 100213, 100213, 100213, 100213, 100211, 100211, 100211, 100211, 100211];
        $pageC = [100213, 100213, 100213, 100213, 100211, 100211, 100213, 100213, 100213, 100213, 100213];
        $pageD = [100211, 100213, 100213, 100213, 100213, 100213, 100213, 100213, 100213, 100213, 100213];

        $pages = [
            [10000001, 'Login',          'Login Screen',                       55,   1, 0, $pageA],
            [10000002, 'Myprofile',      'Can Select various options',         222,  1, 0, $pageA],
            [10000003, 'SelfAccounting', 'Accounting Dashboard',               432,  1, 0, $pageA],
            [10000004, 'GroupAcounting', 'Project Master',                     22,   1, 0, $pageA],
            [10000005, 'SelfAlbum',      'Department Master',                  18,   1, 0, $pageA],
            [10000006, 'GroupAlbum',     'Location Master',                    16,   2, 0, $pageB],
            [10000007, 'Project',        'Show the ledger transactional daata', 2341, 1, 0, $pageC],
            [10000008, 'Department',     'Ledger Group data',                  564,  0, 0, $pageD],
            [10000009, 'Location',       'Location data',                      564,  0, 0, $pageD],
            [10000010, 'Ledger',         'Ledger data',                        564,  0, 0, $pageD],
            [10000011, 'Ledger Group',   'Ledger Group data',                  564,  0, 0, $pageD],
        ];

        $rows = array_map(function (array $p) {
            [$uin, $name, $desp, $hit, $wallet, $rate, $perms] = $p;

            return array_merge(
                [
                    'Page_UIN'       => $uin,
                    'Page_name'      => $name,
                    'Page_Desp'      => $desp,
                    'Dvlp_name'      => self::DEVELOPER,
                    'Data_Hit'       => $hit,
                    'Walt_Chag_Pont' => $wallet,
                    'Rate_Pont'      => $rate,
                ],
                array_combine(self::PERM_COLUMNS, $perms),
                [
                    'CrBy' => 103,
                    'CrOn' => 0,
                    'MoBy' => 102,
                    'MoOn' => 0,
                    'VfBy' => 102,
                    'VfOn' => 0,
                ]
            );
        }, $pages);

        DB::table('tbl_mast_page')->upsert(
            $rows,
            ['Page_UIN'],
            array_keys($rows[0] + []) // update every column on conflict
        );
    }
}
