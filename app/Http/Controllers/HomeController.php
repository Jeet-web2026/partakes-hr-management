<?php

namespace App\Http\Controllers;

use App\Filament\Dashboard\Pages\Dashboard;
use App\Models\UserOrgaRela;
use App\Services\Agreement\AgreementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    public function __construct(
        protected AgreementService $agreementService
    ) {}

    public function index(Request $request)
    {
        $token = $request->query('token');

        if ($token) {
            $userData = $this->agreementService->fetchUserData($token);

            if (! $userData || empty($userData['data'][0]['User_UIN'])) {
                abort(Response::HTTP_UNAUTHORIZED, 'Unable to verify user with Partakers API.');
            }

            $user = $userData['data'][0];

            Session::put('partaker_user_uin', $user['User_UIN']);
            Session::put('partaker_access_token', $token);
            Session::put('partaker_user_data', $user);

            Session::forget('selected_orga_uin');
        }

        $userUin = Session::get('partaker_user_uin');

        if (! $userUin) {
            abort(Response::HTTP_UNAUTHORIZED, 'No session token found. Please re-open the link with a valid token.');
        }

        $companyInformation = $this->agreementService->getCompanyInformation($userUin);

        if ($companyInformation->isEmpty()) {
            abort(Response::HTTP_FORBIDDEN, 'No organisations found for this user.');
        }

        if ($companyInformation->count() === 1) {
            Session::put('selected_orga_uin', $companyInformation->first()->Orga_UIN);

            return redirect()->route('all-agreements');
        }


        return view('organisation-select', compact('companyInformation'));
    }

    public function selectOrganisation(Request $request)
    {
        $request->validate([
            'Orga_UIN' => 'required',
        ]);

        $userUin = Session::get('partaker_user_uin');

        if (! $userUin) {
            abort(Response::HTTP_UNAUTHORIZED, 'Session expired. Please re-open the link with a valid token.');
        }

        $isValidOrg = $this->agreementService->getCompanyInformation($userUin)
            ->contains('Orga_UIN', $request->input('Orga_UIN'));

        if (! $isValidOrg) {
            abort(Response::HTTP_FORBIDDEN, 'You do not have access to that organisation.');
        }

        Session::put('selected_orga_uin', $request->input('Orga_UIN'));

        return redirect()->route('all-agreements');
    }

    public function allAgreements()
    {
        $userUin = Session::get('partaker_user_uin');
        $orgaUin = Session::get('selected_orga_uin');
        $userData = Session::get('partaker_user_data');

        if (! $userUin || ! $orgaUin || ! $userData) {
            return redirect()->route('home');
        }

        $companyInformation = UserOrgaRela::where('User_UIN', $userUin)
            ->with(['organisation' => function ($query) {
                $query->select('Orga_UIN', 'Orga_Name');
            }])
            ->get();

        if (! $companyInformation) {
            abort(Response::HTTP_FORBIDDEN, 'Organisation data not found.');
        }

        Session::put('company_information', $companyInformation);
        Session::put('user_data', $userData);

        return redirect()->to(Dashboard::getUrl());
    }

    public function agreementsData(Request $request): JsonResponse
    {
        $userUin = Session::get('partaker_user_uin');
        $orgaUin = $request->company_id;

        if (! $userUin || ! $orgaUin) {
            return response()->json(['message' => 'No organisation selected.'], Response::HTTP_UNAUTHORIZED);
        }

        $validated = $request->validate([
            'search'           => 'nullable|string|max:255',
            'date_from'        => 'nullable|date',
            'date_to'          => 'nullable|date|after_or_equal:date_from',
            'date_filter_type' => 'nullable|string|in:agreement_date,expiry_date',
            'per_page'         => 'nullable|integer|min:1|max:100',
            'page'             => 'nullable|integer|min:1',
            'statuses'         => 'nullable|array',
            'statuses.*'       => 'string|in:new,renew,expired,sign_pending,received',
        ]);

        $agreements = $this->agreementService->getAgreements(
            search: $validated['search'] ?? null,
            dateFrom: $validated['date_from'] ?? null,
            dateTo: $validated['date_to'] ?? null,
            dateFilterType: $validated['date_filter_type'] ?? 'agreement_date',
            perPage: $validated['per_page'] ?? 100,
            companyId: $orgaUin,
             statuses: $validated['statuses'] ?? []
        );

        return response()->json($agreements);
    }

    public function draftAgreementsData(Request $request): JsonResponse
    {
        $userUin = Session::get('partaker_user_uin');
        $orgaUin = $request->company_id;

        if (! $userUin || ! $orgaUin) {
            return response()->json(['message' => 'No organisation selected.'], Response::HTTP_UNAUTHORIZED);
        }

        $validated = $request->validate([
            'search'    => 'nullable|string|max:255',
            'date_from' => 'nullable|date',
            'date_to'   => 'nullable|date|after_or_equal:date_from',
            'per_page'  => 'nullable|integer|min:1|max:100',
            'page'      => 'nullable|integer|min:1',
        ]);

        $draftAgreements = $this->agreementService->getDraftAgreements(
            search: $validated['search'] ?? null,
            dateFrom: $validated['date_from'] ?? null,
            dateTo: $validated['date_to'] ?? null,
            perPage: $validated['per_page'] ?? 100,
            companyId: $orgaUin,
        );

        return response()->json($draftAgreements);
    }

    public function agreementNamesData(Request $request): JsonResponse
    {
        $userUin = Session::get('partaker_user_uin');
        $orgaUin = $request->company_id;

        if (! $userUin || ! $orgaUin) {
            return response()->json(['message' => 'No organisation selected.'], Response::HTTP_UNAUTHORIZED);
        }

        $validated = $request->validate([
            'search'    => 'nullable|string|max:255',
            'date_from' => 'nullable|date',
            'date_to'   => 'nullable|date|after_or_equal:date_from',
            'per_page'  => 'nullable|integer|min:1|max:100',
            'page'      => 'nullable|integer|min:1',
        ]);

        $draftAgreements = $this->agreementService->getAgreementNames(
            search: $validated['search'] ?? null,
            dateFrom: $validated['date_from'] ?? null,
            dateTo: $validated['date_to'] ?? null,
            perPage: $validated['per_page'] ?? 100,
            companyId: $orgaUin,
        );

        return response()->json($draftAgreements);
    }
}
