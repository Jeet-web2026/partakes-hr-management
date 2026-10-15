<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAgreementNameRequest;
use App\Models\OrganisationDetails;
use App\Services\Agreement\AgreementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;

class AgreementNameController extends Controller
{
    public function __construct(
        protected AgreementService $agreementService
    ) {}

    public function index(Request $request): View
    {
        $agreementCategories = $this->agreementService->fetchAgreementCategories();
        $userData = $this->agreementService->fetchUserData(Session::get('partaker_access_token'));
        $companyInformation =  OrganisationDetails::where('Orga_UIN', $request->query('company'))->first();
        return view('agreements.add-agreement-name', compact('agreementCategories', 'userData', 'companyInformation'));
    }

    public function store(StoreAgreementNameRequest $request): RedirectResponse
    {
        $this->agreementService->create($request->validated());

        return redirect('/settings/settings')
            ->with('success', 'Agreement name created successfully.');
    }

    public function destroy(int|string $id)
    {
        $response = $this->agreementService->deleteAgreementName($id);

        if (!$response) {
            return redirect()
                ->back()
                ->with('error', 'Agreement can not be deletd.');
        }

        return redirect()
            ->back()
            ->with('success', 'Agreement name deleted successfully.');
    }

    public function fetchAgreementName(Request $request): JsonResponse
    {

        $request->validate([
            'orga_uin'  => ['required'],
            'category'  => ['required'],
            'type'      => ['required'],
        ]);

        $names = $this->agreementService->getByOrgaCategoryAndType(
            $request->input('orga_uin'),
            $request->input('category'),
            $request->input('type'),
        );

        return response()->json($names);
    }
}
