<?php

namespace App\Http\Controllers;

use App\Http\Requests\Agreement\RenewAgreementRequest;
use App\Models\AgreementCategories;
use App\Models\AgreementType;
use App\Services\Agreement\AgreementService;
use Exception;
use Illuminate\Support\Facades\Session;

class RenewalAgreementController extends Controller
{
    protected AgreementService $agreementService;

    public function __construct(AgreementService $agreementService)
    {
        $this->agreementService = $agreementService;
    }

    public function renewalAgreement(int $agreementId)
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

        return view('agreements.renew-agreement', compact('agreement', 'agreementCategories', 'types', 'userData'));
    }

    public function renewAgreement(RenewAgreementRequest $request, int $agreementId)
    {
        try {
            $this->agreementService->renewAgreement($agreementId, $request);

            return redirect('/dashboard/agreements')
                ->with('success', 'Agreement renewed successfully.');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }
}
