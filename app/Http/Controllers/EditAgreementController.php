<?php

namespace App\Http\Controllers;

use App\Http\Requests\Agreement\EditAgreementRequest;
use App\Models\AgreementCategories;
use App\Models\AgreementType;
use App\Services\Agreement\AgreementService;
use Illuminate\Support\Facades\Session;

class EditAgreementController extends Controller
{
    protected AgreementService $agreementService;

    public function __construct(AgreementService $agreementService)
    {
        $this->agreementService = $agreementService;
    }

    public function editAgreement(int $agreementId)
    {
        $agreement = $this->agreementService->getAgreementById($agreementId);
        $userData = $this->agreementService->fetchUserData(Session::get('partaker_access_token'));

        $agreementCategories = AgreementCategories::with(['children' => function ($query) {
            $query->orderBy('Agmt_Catg_Name', 'asc');
        }])
            ->select('Agmt_Catg_UIN', 'Agmt_Catg_Name')
            ->get();

        $types = AgreementType::select('Agmt_Type_UIN', 'Agmt_Type_Name')
            ->orderBy('Agmt_Type_Name', 'asc')
            ->get();

        return view('agreements.edit-agreement', compact('agreement', 'agreementCategories', 'types', 'userData'));
    }

    public function updateAgreement(EditAgreementRequest $request, int $agreementId)
    {
        $this->agreementService->updateAgreement($agreementId, $request);

        return redirect('/dashboard/agreements')
            ->with('success', 'Agreement updated successfully.');
    }
}
