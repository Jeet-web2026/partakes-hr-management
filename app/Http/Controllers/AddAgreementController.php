<?php

namespace App\Http\Controllers;

use App\Http\Requests\Agreement\StoreAgreementrequest;
use App\Models\OrganisationDetails;
use App\Services\Agreement\AgreementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class AddAgreementController extends Controller
{
    public function __construct(private readonly AgreementService $agreementService) {}

    public function index(Request $request): View
    {
        $agreementCategories = $this->agreementService->fetchAgreementCategories();
        $userData = $this->agreementService->fetchUserData(Session::get('partaker_access_token'));
        $companyInformation =  OrganisationDetails::where('Orga_UIN', $request->query('company'))->first();
        return view('agreements.add-agreement', compact('agreementCategories', 'userData', 'companyInformation'));
    }

    public function getAgreementTypes(int $categoryId): JsonResponse
    {
        return response()->json($this->agreementService->getAgreementTypes($categoryId));
    }

    public function store(StoreAgreementrequest $request)
    {
        $this->agreementService->store($request);

        return redirect()
            ->back()
            ->with('success', 'Agreement created successfully.');
    }
}
