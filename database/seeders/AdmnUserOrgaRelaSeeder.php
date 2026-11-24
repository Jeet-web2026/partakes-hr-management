<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdmnUserOrgaRelaSeeder extends Seeder
{
    private const ACTIVE = 100201;

    /** Column defaults for admn_orga_mast. */
    private const ORGA_DEFAULTS = [
        'Orga_Type_UIN' => 1500000001,
        'orga_Logo' => '',
        'Dt_of_Incp' => 0,
        'Fnal_Year_Strt_Dt' => 0,
        'Actn_Strt_Dt' => 0,
        'Web_Addr' => '',
        'Cnta_Pers' => 0,
        'Admt_Pers_1st' => 0,
        'Admt_Pers_2nd' => 0,
        'Admt_Pers_3rd' => 0,
        'Admt_Pers_4th' => 0,
        'Assc_Part_Code' => 0,
        'Sytm_Admt_Pers' => 0,
        'Eft_Dt' => 0,
        'Renw_Dt' => 0,
        'Expy_Dt' => 0,
        'Orga_Del_Dt' => 0,
        'Stau_UIN' => 0,
        'CrBy' => null,
        'CrOn' => null,
        'MoBy' => null,
        'MoOn' => null,
        'VfBy' => null,
        'VfOn' => null,
        'Stat_UIN' => 0,
        'pin_UIN' => '',
        'Prmy_Emai' => '',
        'Prmy_Emai_Vefi' => 0,
        'mpos_key' => 0,
    ];

    public function run(): void
    {
        DB::transaction(function () {
            $this->seedOrganisations();
            $this->ensureUsersExist();
            $this->seedUserOrganisationLinks();
        });
    }

    /* ------------------------------------------------------------------ */
    /* 1. admn_orga_mast                                                   */
    /* ------------------------------------------------------------------ */
    private function seedOrganisations(): void
    {
        $organisations = [
            [
                'Orga_UIN' => 1500000001,
                'Orga_Name' => 'D S Computer Center',
                'Admt_Pers_1st' => 1500000001,
                'Admt_Pers_2nd' => 1677851592
            ],
            [
                'Orga_UIN' => 1500000002,
                'Orga_Name' => 'Marcus Org',
                'Admt_Pers_1st' => 1500000001
            ],
            [
                'Orga_UIN' => 1715097168,
                'Orga_Type_UIN' => 1500000033,
                'Orga_Name' => 'Ekatrap',
                'Admt_Pers_1st' => 1500000001
            ],
            [
                'Orga_UIN' => 1787331830,
                'Orga_Type_UIN' => 1500000009,
                'Orga_Name' => 'Rajdeep Trading Company',
                'Admt_Pers_1st' => 1500000001
            ],
            [
                'Orga_UIN' => 1787331948,
                'Orga_Type_UIN' => 1500000012,
                'Orga_Name' => 'Partakers Organization LLP',
                'Admt_Pers_1st' => 1500000001
            ],
            [
                'Orga_UIN' => 1787331949,
                'Orga_Name' => 'wbc',
                'Admt_Pers_1st' => 6857873701,
                'Eft_Dt' => 1787592627,
                'Stau_UIN' => 100201,
                'CrBy' => '6857873701',
                'CrOn' => '1970-01-01 00:33:46',
                'Stat_UIN' => 102034,
                'pin_UIN' => '221108',
                'Prmy_Emai' => 'ydivyanshuya22@gmail.com',
                'Prmy_Emai_Vefi' => 100207,
                'mpos_key' => 1
            ],
            [
                'Orga_UIN' => 1787592628,
                'Orga_Name' => 'wbc',
                'Dt_of_Incp' => 1787592813,
                'Fnal_Year_Strt_Dt' => 1787592813,
                'Actn_Strt_Dt' => 1787592813,
                'Cnta_Pers' => 6857873701,
                'Admt_Pers_1st' => 6857873701,
                'Sytm_Admt_Pers' => 6857873701,
                'Eft_Dt' => 1787592813,
                'Renw_Dt' => 1787592813,
                'Expy_Dt' => 1819128813,
                'Stau_UIN' => 100201,
                'CrBy' => '6857873701',
                'CrOn' => '1970-01-01 00:33:46',
                'MoBy' => '6857873701',
                'MoOn' => '1970-01-01 00:33:46',
                'Stat_UIN' => 100201,
                'Prmy_Emai' => 'ydivyanshuya22@gmail.com'
            ],
            [
                'Orga_UIN' => 1787592629,
                'Orga_Name' => 'potion',
                'Admt_Pers_1st' => 6857873702,
                'Eft_Dt' => 1788089433,
                'Stau_UIN' => 100201,
                'CrBy' => '6857873702',
                'CrOn' => '1970-01-01 00:33:46',
                'Stat_UIN' => 102034,
                'pin_UIN' => '221108',
                'Prmy_Emai' => 'divyanshuyadav297@gmail.com',
                'Prmy_Emai_Vefi' => 100207,
                'mpos_key' => 2
            ],
            [
                'Orga_UIN' => 1788089434,
                'Orga_Name' => 'potion',
                'Dt_of_Incp' => 1788089546,
                'Fnal_Year_Strt_Dt' => 1788089546,
                'Actn_Strt_Dt' => 1788089546,
                'Cnta_Pers' => 6857873702,
                'Admt_Pers_1st' => 6857873702,
                'Sytm_Admt_Pers' => 6857873702,
                'Eft_Dt' => 1788089546,
                'Renw_Dt' => 1788089546,
                'Expy_Dt' => 1819625546,
                'Stau_UIN' => 100201,
                'CrBy' => '6857873702',
                'CrOn' => '1970-01-01 00:33:46',
                'MoBy' => '6857873702',
                'MoOn' => '1970-01-01 00:33:46',
                'Stat_UIN' => 100201,
                'Prmy_Emai' => 'divyanshuyadav297@gmail.com'
            ],
            [
                'Orga_UIN' => 1788089435,
                'Orga_Name' => 'techy',
                'Admt_Pers_1st' => 6857873703,
                'Eft_Dt' => 1788540697,
                'Stau_UIN' => 100201,
                'CrBy' => '6857873703',
                'CrOn' => '1970-01-01 00:33:46',
                'Stat_UIN' => 102034,
                'pin_UIN' => '221108',
                'Prmy_Emai' => 'ketad97069@hebase.com',
                'Prmy_Emai_Vefi' => 100207,
                'mpos_key' => 3
            ],
            [
                'Orga_UIN' => 1788540697,
                'Orga_Name' => 'techy',
                'Dt_of_Incp' => 1788540754,
                'Fnal_Year_Strt_Dt' => 1788540754,
                'Actn_Strt_Dt' => 1788540754,
                'Cnta_Pers' => 6857873703,
                'Admt_Pers_1st' => 6857873703,
                'Sytm_Admt_Pers' => 6857873703,
                'Eft_Dt' => 1788540754,
                'Renw_Dt' => 1788540754,
                'Expy_Dt' => 1820076754,
                'Stau_UIN' => 100201,
                'CrBy' => '6857873703',
                'CrOn' => '1970-01-01 00:33:46',
                'MoBy' => '6857873703',
                'MoOn' => '1970-01-01 00:33:46',
                'Stat_UIN' => 100201,
                'Prmy_Emai' => 'ketad97069@hebase.com'
            ],
        ];

        foreach ($organisations as $org) {
            $row = array_merge(self::ORGA_DEFAULTS, $org);
            $uin = $row['Orga_UIN'];
            unset($row['Orga_UIN']);

            DB::table('admn_orga_mast')->updateOrInsert(['Orga_UIN' => $uin], $row);
        }
    }

    /* ------------------------------------------------------------------ */
    /* 2. admn_user_logi_mast: check only (no dump yet)                    */
    /* ------------------------------------------------------------------ */
    private function ensureUsersExist(): void
    {
        $required = collect($this->linkRows())->pluck(3)->unique()->values();

        $existing = DB::table('admn_user_logi_mast')
            ->whereIn('User_UIN', $required)
            ->pluck('User_UIN');

        $missing = $required->diff($existing);

        if ($missing->isNotEmpty()) {
            throw new \RuntimeException(
                'admn_user_logi_mast is missing these User_UIN values: ' . $missing->implode(', ')
                    . '. Seed those users first (add them in this method), then re-run.'
            );
        }
    }

    /* ------------------------------------------------------------------ */
    /* 3. admn_user_orga_rela                                              */
    /* ------------------------------------------------------------------ */

    /** [User_Assc_UIN, Orga_User_Assc_UIN, Orga_UIN, User_UIN, CrOn] */
    private function linkRows(): array
    {
        return [
            [1,          100101,     1787331949, 6857873701, '2026-08-24 17:30:27'],
            [1540893581, 1946530666, 1500000001, 1500000001, '2026-08-25 05:45:12'],
            [1546522162, 1546040583, 1500000002, 1500000001, '2026-08-25 05:45:12'],
            [1548199972, 1547763322, 1715097168, 1500000001, '2026-08-25 05:45:12'],
            [1549786312, 1549371768, 1787331830, 1500000001, '2026-08-25 05:45:12'],
            [1551510117, 1550887593, 1787331948, 1500000001, '2026-08-25 05:45:12'],
            [1569892849, 1569380538, 1788089434, 6857873702, '2026-08-30 11:32:26'],
            [1757946193, 1757510371, 1787592628, 6857873701, '2026-08-24 17:33:33'],
            [1757946194, 100101,     1787592629, 6857873702, '2026-08-30 11:30:33'],
            [1996835985, 1946530666, 1500000001, 1768039919, '2026-08-30 14:48:38'],
            [1999018541, 1546040583, 1500000002, 1768039919, '2026-08-30 14:48:38'],
            [2000087273, 1547763322, 1715097168, 1768039919, '2026-08-30 14:48:38'],
            [2001191410, 1549371768, 1787331830, 1768039919, '2026-08-30 14:48:38'],
            [2002245568, 1550887593, 1787331948, 1768039919, '2026-08-30 14:48:38'],
            [2003314161, 1804878109, 1787331949, 1768039919, '2026-08-30 14:48:38'],
            [2004761838, 1757510371, 1787592628, 1768039919, '2026-08-30 14:48:38'],
            [2006181205, 1575432792, 1787592629, 1768039919, '2026-08-30 14:48:38'],
            [2007568399, 1569380538, 1788089434, 1768039919, '2026-08-30 14:48:38'],
            [2007568400, 100101,     1788089435, 6857873703, '2026-09-04 16:51:37'],
            [2293779869, 1946530666, 1500000001, 6857873677, '2026-09-21 14:27:37'],
            [2295032523, 2294570871, 1788540697, 6857873703, '2026-09-04 16:52:34'],
            [2295899273, 1550887593, 1787331948, 6857873677, '2026-09-21 14:27:37'],
        ];
    }

    private function seedUserOrganisationLinks(): void
    {
        foreach ($this->linkRows() as [$userAsscUin, $orgaUserAsscUin, $orgaUin, $userUin, $createdOn]) {
            DB::table('admn_user_orga_rela')->updateOrInsert(
                ['User_Assc_UIN' => $userAsscUin],
                [
                    'Orga_User_Assc_UIN' => $orgaUserAsscUin,
                    'Orga_UIN'           => $orgaUin,
                    'User_UIN'           => $userUin,
                    'Expy'               => null,
                    'Stau_UIN'           => self::ACTIVE,
                    'CrOn'               => $createdOn,
                    'CrBy'               => (string) $userUin,
                    'MoOn'               => null,
                    'MoBy'               => null,
                ]
            );
        }
    }
}
