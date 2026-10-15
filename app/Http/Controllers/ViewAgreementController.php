<?php

namespace App\Http\Controllers;

use App\Models\Agreement;
use App\Models\AgmtDraft;
use App\Models\AgreementCategories;
use App\Models\AgreementParty;
use App\Models\AgreementType;
use App\Models\TranAgmtRemk;
use App\Services\Agreement\AgreementService;
use App\Services\Common\FileStorageService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class ViewAgreementController extends Controller
{
    protected AgreementService $agreementService;

    public function __construct(AgreementService $agreementService, private readonly FileStorageService $fileStorageService)
    {
        $this->agreementService = $agreementService;
    }

    public function index(int $agreementId)
    {
        $agreement = $this->agreementService->getAgreementById($agreementId);

        $agreementCategories = AgreementCategories::with('children')
            ->get();

        $types = AgreementType::all();
        $userData = $this->agreementService->fetchUserData(Session::get('partaker_access_token'));

        return view('agreements.view-agreement', compact('agreement', 'agreementCategories', 'types', 'userData'));
    }

    public function history(int $agreementId)
    {
        $agreement = $this->agreementService->getHistory($agreementId);

        return response()->json(['data' => $agreement]);
    }

    public function destroy(Agreement $agreement)
    {
        $filePath = $agreement->file_path;
        DB::transaction(function () use ($agreement) {
            Agreement::where('Agmt_Parn_UIN', $agreement->Agmt_UIN)
                ->update(['Agmt_Parn_UIN' => null]);

            TranAgmtRemk::where('agmt_uin', $agreement->Agmt_UIN)->delete();
            AgreementParty::where('Agmt_UIN', $agreement->Agmt_UIN)->delete();

            $agreement->delete();
        });

        if ($filePath && Storage::exists($filePath)) {
            Storage::delete($filePath);
        }

        return redirect()->back()->with('success', 'Agreement deleted successfully.');
    }

    public function generateViewLink(Agreement $agreement)
    {
        $url = $this->agreementService->generateViewLink($agreement);

        return response()->json([
            'url' => $url,
        ]);
    }

    public function generateDraftViewLink(AgmtDraft $draft)
    {
        $url = $this->agreementService->generateDraftViewLink($draft);

        return response()->json([
            'url' => $url,
        ]);
    }

    public function publicView(string $token)
    {
        $link = $this->agreementService->resolveShareLink($token);

        return match ($link->Link_Type) {
            'agreement' => view('agreements.view-agreement', [
                'agreement'           => $link->foreign,
                'agreementCategories' => $this->agreementService->getCategoriesWithChildren(),
                'public'              => true,
            ]),
            'draft' => view('agreements.view-draft', [
                'draft'   => $link->foreign,
                'agreementCategories' => $this->agreementService->getCategoriesWithChildren(),
                'public'  => true,
            ]),
            default => abort(404),
        };
    }

    public function download(Agreement $agreement)
    {
        return $this->fileStorageService
            ->downloadAs($agreement->Agmt_Atch);
    }

    public function draftdownload(AgmtDraft $draft, string $columnName)
    {
        return $this->fileStorageService
            ->downloadAs($draft->$columnName);
    }
}
