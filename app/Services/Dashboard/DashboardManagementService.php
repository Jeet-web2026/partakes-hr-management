<?php

namespace App\Services\Dashboard;

use App\Models\AgmtDraft;
use App\Models\Agreement;
use Carbon\Carbon;

class DashboardManagementService
{
    public function getTiles(string $orgaUin): array
    {
        $definitions = [
            'available'       => ['label' => 'Agreement Available',              'icon' => 'heroicon-o-document-text',    'color' => 'primary'],
            'draft_available' => ['label' => 'Draft Available',                  'icon' => 'heroicon-o-document',         'color' => 'gray'],
            'expired'         => ['label' => 'Expired Agreement',                'icon' => 'heroicon-o-x-circle',         'color' => 'danger'],
            'new_draft'       => ['label' => 'New Draft Added',                  'icon' => 'heroicon-o-document-plus',    'color' => 'info'],
            'active'          => ['label' => 'Active Agreement',                 'icon' => 'heroicon-o-check-circle',     'color' => 'success'],
            'sign_pending'    => ['label' => 'Agreement Sign Pending',           'icon' => 'heroicon-o-pencil-square',    'color' => 'warning'],
            'new'             => ['label' => 'New Agreement',                    'icon' => 'heroicon-o-sparkles',         'color' => 'info'],
            'received_hard'   => ['label' => 'Agreement Received (Hard Copy)',   'icon' => 'heroicon-o-inbox-arrow-down', 'color' => 'success'],
            'renew'           => ['label' => 'Renew Agreement',                  'icon' => 'heroicon-o-arrow-path',       'color' => 'warning'],
            'received_soft'   => ['label' => 'Agreement Received (Soft Copy)',   'icon' => 'heroicon-o-inbox-arrow-down', 'color' => 'success'],
        ];

        $tiles = [];
        foreach ($definitions as $key => $meta) {
            $tiles[] = array_merge($meta, [
                'key'   => $key,
                'count' => 0,
            ]);
        }

        return $tiles;
    }

    public function getCounts(string $orgaUin, int $year, int $month): array
    {
        $base = Agreement::query()->where('Orga_UIN', $orgaUin);
        $draftBase = AgmtDraft::query()->where('Orga_UIN', $orgaUin);
        $monthStart = Carbon::create($year, $month, 1)->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();

        return [
            'available'       => (clone $base)->count(),
            'draft_available' => (clone $draftBase)->count(),
            'expired'         => (clone $base)->where('Stau_UIN', 'expired')
                ->whereBetween('ExpOn', [$monthStart, $monthEnd])->count(),
            'new_draft'       => (clone $draftBase)
                ->whereBetween('CrOn', [$monthStart, $monthEnd])->count(),
            'active'          => (clone $base)
                ->where('Stau_UIN', 'active')
                ->where('Agmt_Strt_Date', '<=', $monthEnd)
                ->where('Agmt_Expy_Date', '>=', $monthStart)->count(),
            'sign_pending'    => (clone $base)->where('Agmt_Signed', 'no')->count(),
            'new'             => (clone $base)
                ->whereYear('Agmt_Strt_Date', $year)
                ->whereMonth('Agmt_Strt_Date', $month)->count(),
            'received_hard'   => (clone $base)->where('Agmt_Recv_Type', 'original_hard_copy')->count(),
            'renew'           => (clone $base)
                ->where('Stau_UIN', 'renewed')
                ->whereBetween('RenwOn', [$monthStart, $monthEnd])->count(),
            'received_soft'   => (clone $base)->where('Agmt_Recv_Type', 'pdf_copy')->count(),
        ];
    }

    public function getTileDetails(): array
    {
        return [
            'available'       => 'All agreements currently on file across every status, regardless of month.',
            'draft_available' => 'Drafts saved but not yet converted into a live agreement.',
            'expired'         => 'Agreements past their end date with no renewal recorded yet.',
            'new_draft'       => 'Drafts added within the selected calendar month.',
            'active'          => 'Agreements currently in force and within their valid date range.',
            'sign_pending'    => 'Agreements sent out and awaiting a counter-party signature.',
            'new'             => 'Agreements created within the selected calendar month.',
            'received_hard'   => 'Signed agreements received back as a physical hard copy.',
            'renew'           => 'Agreements flagged for renewal in the selected month.',
            'received_soft'   => 'Signed agreements received back as a scanned/soft copy.',
        ];
    }
}
