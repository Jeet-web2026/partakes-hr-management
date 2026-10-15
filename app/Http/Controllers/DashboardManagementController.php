<?php

namespace App\Http\Controllers;

use App\Services\Dashboard\DashboardManagementService;
use Illuminate\Http\Request;

class DashboardManagementController extends Controller
{
    public function __construct(private readonly DashboardManagementService $service) {}

    public function index(Request $request)
    {
        $orgaUin = $request->selected_orga_uin;

        $year = (int) $request->year ?? now()->year;
        $month = (int) $request->month ?? now()->month;

        return response()->json(
            $this->service->getCounts($orgaUin, $year, $month)
        );
    }
}
