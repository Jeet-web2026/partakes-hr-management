<?php

namespace App\Services\Agreement;

use App\Http\Requests\Agreement\RenewAgreementRequest;
use App\Http\Requests\Agreement\StoreAgreementrequest;
use App\Http\Resources\AgmtDraftResource;
use App\Http\Resources\AgmtNameResource;
use App\Models\AgmtDraft;
use App\Models\Agreement;
use App\Models\AgreementCategories;
use App\Models\AgreementName;
use App\Models\AgreementType;
use App\Models\OrganisationDetails;
use App\Models\TranAgmtRemk;
use App\Models\UserOrgaRela;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Throwable;
use Illuminate\Support\Str;
use App\Models\ShareLink;
use App\Services\Common\FileStorageService;

class AgreementService
{

    public function __construct(private readonly FileStorageService $fileStorageService) {}

    public function fetchAgreementCategories(): ?Collection
    {
        return AgreementCategories::with(['children' => function ($query) {
            $query->orderBy('Agmt_Catg_Name', 'asc');
        }])
            ->select('Agmt_Catg_UIN', 'Agmt_Catg_Name')
            ->get();
    }

    public function getAgreementTypes(int $categoryId): ?Collection
    {
        return AgreementType::where('Agmt_Catg_UIN', $categoryId)
            ->select('Agmt_Type_UIN', 'Agmt_Type_Name')
            ->orderBy('Agmt_Type_Name', 'asc')
            ->get();
    }

    public function store(StoreAgreementrequest $request): Agreement
    {
        return DB::transaction(function () use ($request) {

            $userData = Session::get('partaker_user_data');

            $attachment = null;
            $attachmentSizeKb = null;

            $refNo = $request->ref_no ?? $this->generateAgreementRefNo($request->company_id);

            $agreement = Agreement::create([
                'Orga_UIN'       => $request->company_id,
                'User_UIN'       => $userData['User_UIN'] ?? 1,

                'Agmt_Catg_UIN'  => $request->agreement_category,
                'Agmt_Type_UIN'  => $request->agreement_type,
                'Agmt_Name'      => $request->agreement_name,

                'Agmt_File_no'        => $request->file_no,

                'Agmt_Strt_Date' => $request->agreement_date,
                'Agmt_Expy_Date' => $request->expiry_date,
                'Agmt_Sign_Date' => $request->signing_date,
                'Agmt_Ntry_Date' => $request->notary_date,

                'Agmt_Vson'      => $request->agreement_version,
                'Agmt_Drft_By'   => $request->drafted_by,
                'Agmt_Judn'      => $request->jurisdiction,
                'Agmt_Atch'      => $attachment,
                'Agmt_Size_KB'  => $attachmentSizeKb,

                'Agmt_Parn_UIN'  => null,
                'Agmt_Prty_UIN'  => null,

                'CrBy'           => $userData['User_UIN'] ?? 1,
                'MoBy'           => $userData['User_UIN'] ?? 1,

                'Stau_UIN'       => $this->determineStatusUin($request->expiry_date),
                'Agmt_Ref_No' => $refNo,
                'Agmt_Depst_Amt' => $request->deposit_amount ?? null,
                'Agmt_Signed' => $request->agreement_signed,
                'Signed_Recv_Date' => $request->signed_document_received_date,
                'Agmt_Recv_Type' => $request->agreement_received_type
            ]);

            $remarks = trim($request->remarks);

            if (
                $remarks !== '<p><br></p>' &&
                !empty(trim(strip_tags($remarks)))
            ) {
                TranAgmtRemk::create([
                    'agmt_uin'       => $agreement->Agmt_UIN,
                    'agmt_remk_date' => now()->toDateString(),
                    'agmt_remk'      => $remarks,
                    'stau_uin'       => 1,
                    'crby'           => $userData['User_Name'] ?? 1,
                    'moby'           => $userData['User_Name'] ?? 1,
                ]);
            }

            $primaryPartyUin = null;

            foreach ($request->input('parties', []) as $index => $partyData) {

                $party = $agreement->parties()->create([
                    'Agmt_Prty_No'       => $partyData['role'] ?? null,
                    'Agmt_Prty_Org_Name' => $partyData['org_name'] ?? null,
                    'Agmt_Athr_Sgnt'     => $partyData['signatory'] ?? null,
                    'CrBy'               => $userData['User_UIN'] ?? 1,
                    'MoBy'               => $userData['User_UIN'] ?? 1,
                ]);

                if (isset($partyData['is_primary'])) {
                    $primaryPartyUin = $party->Agmt_Prty_UIN;
                }
            }

            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $attachment =  $this->storeAgreementAttachment($file, $agreement->Agmt_UIN, $agreement->Orga_UIN, $userData['User_UIN'], 'agreement');
                $attachmentSizeKb = $this->getFileSizeInKb($file);
            }

            if ($primaryPartyUin !== null) {
                $agreement->Agmt_Prty_UIN = $primaryPartyUin;
                $agreement->Agmt_Atch = $attachment;
                $agreement->Agmt_Size_KB = $attachmentSizeKb;
                $agreement->saveQuietly();
            }

            return $agreement->load('parties');
        });
    }

    public function getAgreements(
        ?string $search = null,
        ?string $dateFrom = null,
        ?string $dateTo = null,
        ?string $dateFilterType = 'agreement_date',
        int $perPage = 5,
        ?int $companyId = null,
        array $statuses = []
    ) {
        $userData = Session::get('partaker_user_data');
        $query = Agreement::query()
            ->select([
                'Agmt_UIN',
                'Agmt_Name',
                'Agmt_Strt_Date',
                'Agmt_Expy_Date',
                'Agmt_Sign_Date',
                'Agmt_Ntry_Date',
                'Agmt_Vson',
                'Agmt_Drft_By',
                'Agmt_Judn',
                'Agmt_Remk',
                'Agmt_File_no',
                'Stau_UIN',
                'Orga_UIN',
                'Agmt_Catg_UIN',
                'Agmt_Type_UIN',
                'Agmt_Parn_UIN',
                'User_UIN',
                'Agmt_Ref_No',
                'Agmt_Depst_Amt',
                'Agmt_Signed',
                'Signed_Recv_Date',
                'Agmt_Recv_Type'
            ])
            ->with([
                'category:Agmt_Catg_UIN,Agmt_Catg_Name',
                'type:Agmt_Type_UIN,Agmt_Type_Name',
                'parties:Agmt_UIN,Agmt_Prty_No,Agmt_Prty_Org_Name,Agmt_Athr_Sgnt',
                'parentAgreement:Agmt_UIN,Agmt_Name,Agmt_File_no',
                'remarks' => function ($q) {
                    $q->select('agmt_remk_uin', 'agmt_uin', 'agmt_remk', 'agmt_remk_date')
                        ->orderBy('agmt_remk_date');
                },
            ])
            ->where('User_UIN', $userData['User_UIN'])
            ->withCount('childAgreements')
            ->whereIn('Stau_UIN', ['active', 'renewed', 'expired']);

        if ($companyId) {
            $query->where('Orga_UIN', $companyId);
        }

        if (! empty($statuses)) {
            $query->where(function ($q) use ($statuses) {
                foreach ($statuses as $status) {
                    match ($status) {
                        'expired' => $q->orWhere('Stau_UIN', 'expired'),
                        'renew' => $q->orWhere('Stau_UIN', 'renewed'),
                        'new' => $q->orWhere(function ($sub) {
                            $sub->whereMonth('created_at', now()->month)
                                ->whereYear('created_at', now()->year);
                        }),
                        'sign_pending' => $q->orWhere('Stau_UIN', 'sign_pending'),
                        'received' => $q->orWhere('Stau_UIN', 'received'),
                        default => null,
                    };
                }
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('Agmt_Name', 'like', "%{$search}%")
                    ->orWhere('Agmt_File_no', 'like', "%{$search}%")
                    ->orWhere('Agmt_Ref_No', 'like', "%{$search}%")
                    ->orWhereHas('type', function ($q2) use ($search) {
                        $q2->where('Agmt_Type_Name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('category', function ($q2) use ($search) {
                        $q2->where('Agmt_Catg_Name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('parties', function ($q2) use ($search) {
                        $q2->where('Agmt_Prty_Org_Name', 'like', "%{$search}%");
                    });
            });
        }

        if ($dateFrom && $dateTo) {
            $dateColumn = $dateFilterType === 'expiry_date' ? 'Agmt_Expy_Date' : 'Agmt_Strt_Date';
            $query->whereBetween($dateColumn, [
                $dateFrom . ' 00:00:00',
                $dateTo . ' 23:59:59',
            ]);
        }

        $agreements = $query->latest('Agmt_UIN')->paginate($perPage);

        $agreements->getCollection()->transform(function ($agreement) {
            $expiryDate = $agreement->Agmt_Expy_Date;

            return [
                'id' => (string) $agreement->Agmt_UIN,
                'category' => optional($agreement->category)->Agmt_Catg_Name,
                'type' => optional($agreement->type)->Agmt_Type_Name,
                'name' => ucfirst($agreement->Agmt_Name),

                'parties' => $agreement->parties->map(function ($party) {
                    return [
                        'role'      => $party->Agmt_Prty_No,
                        'org'       => $party->Agmt_Prty_Org_Name,
                        'signatory' => $party->Agmt_Athr_Sgnt,
                    ];
                })->values(),

                'date' => optional($agreement->Agmt_Strt_Date)?->format('Y-m-d') ?? '-',
                'expiry' => optional($agreement->Agmt_Expy_Date)?->format('Y-m-d') ?? '-',
                'signingDate' => optional($agreement->Agmt_Sign_Date)?->format('Y-m-d') ?? '-',
                'notaryDate' => optional($agreement->Agmt_Ntry_Date)?->format('Y-m-d') ?? '-',

                'version' => $agreement->Agmt_Vson ?? '-',
                'drafted' => $agreement->Agmt_Drft_By ?? '-',

                'parentAgreement' => $agreement->parentAgreement ? [
                    'id'   => $agreement->parentAgreement->Agmt_UIN ?? '-',
                    'name' => $agreement->parentAgreement->Agmt_Name ?? '-',
                    'file_no' => $agreement->parentAgreement->Agmt_File_no ?? '-',
                ] : null,

                'daysLeft' => $expiryDate ? (int) now()->diffInDays($expiryDate, false) : null,

                'categoryLabel' => optional($agreement->category)->Agmt_Catg_Name ?? '-',
                'jurisdiction' => $agreement->Agmt_Judn ?? '-',
                'remarks' => $agreement->remarks->pluck('agmt_remk')->implode(', ') ?? '-',
                'fileNo' => $agreement->Agmt_File_no ?? '-',
                'refNo' => $agreement->Agmt_Ref_No,
                'depositAmount' => $agreement->Agmt_Depst_Amt,
                'agmtSigned' => $agreement->Agmt_Signed,
                'agmtRecDate' => $agreement->Signed_Recv_Date,
                'agmtRecType' => ucfirst(str_replace('_', ' ', $agreement->Agmt_Recv_Type))
            ];
        });

        return $agreements;
    }

    public function getAgreementById(int $agreementId): Agreement
    {
        return Agreement::with([
            'category',
            'type',
            'parties',
            'remarks',
            'user',
            'organisationDetails'
        ])->findOrFail($agreementId);
    }

    public function updateAgreement(int $agreementId, Request $request): Agreement
    {
        return DB::transaction(function () use ($agreementId, $request) {

            $agreement = Agreement::findOrFail($agreementId);
            $attachment = $agreement->Agmt_Atch;
            $attachmentSizeKb = $agreement->Agmt_Size_KB;
            $userData = Session::get('partaker_user_data');

            $refNo = $request->ref_no ?? $this->generateAgreementRefNo($request->company_id);

            $updatePayload = [
                'Agmt_Catg_UIN'  => $request->category,
                'Agmt_Type_UIN'  => $request->type,
                'Agmt_Name'      => $request->name,
                'Agmt_File_no'        => $request->file_no,
                'Agmt_Strt_Date' => $request->agreement_date,
                'Agmt_Expy_Date' => $request->expiry_date,
                'Agmt_Sign_Date' => $request->signing_date,
                'Agmt_Ntry_Date' => $request->notary_date,
                'Agmt_Vson'      => $request->version,
                'Agmt_Drft_By'   => $request->drafted_by,
                'Agmt_Judn'      => $request->jurisdiction,
                'MoBy'           => $userData['User_UIN'] ?? 1,
                'Agmt_Atch'      => $attachment,
                'Agmt_Size_KB'  => $attachmentSizeKb,
                'Stau_UIN'       => $request->expiry_date ? $this->determineStatusUin($request->expiry_date) : $this->determineStatusUin($agreement->expiry_date),
                'Agmt_Ref_No' => $refNo,
                'Agmt_Depst_Amt' => $request->deposit_amount ?? null,
                'Agmt_Signed' => $request->agreement_signed,
                'Signed_Recv_Date' => $request->signed_document_received_date,
                'Agmt_Recv_Type' => $request->agreement_received_type
            ];

            $remarks = trim($request->remarks);

            if (
                $remarks !== '<p><br></p>' &&
                !empty(trim(strip_tags($remarks)))
            ) {
                TranAgmtRemk::create([
                    'agmt_uin'       => $agreement->Agmt_UIN,
                    'agmt_remk_date' => now()->toDateString(),
                    'agmt_remk'      => $request->remarks,
                    'stau_uin'       => 1,
                    'crby'           => $userData['User_Name'] ?? 1,
                    'moby'           => $userData['User_Name'] ?? 1,
                ]);
            }

            $agreement->update($updatePayload);

            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $attachment = $this->storeAgreementAttachment($file, $agreement->Agmt_UIN, $agreement->Orga_UIN, $userData['User_UIN'], 'agreement');;
                $attachmentSizeKb = $this->getFileSizeInKb($file);

                if ($agreement->Agmt_Atch) {
                    Storage::disk('public')->delete($agreement->Agmt_Atch);
                }
            }

            $agreement->update(['Agmt_Atch' => $attachment, 'Agmt_Size_KB' => $attachmentSizeKb]);

            $agreement->increment('update_count');

            $submittedPartyIds = [];
            $primaryPartyUin = null;

            foreach ($request->input('parties', []) as $index => $partyData) {

                $partyId = $partyData['id'] ?? null;

                $attributes = [
                    'Agmt_Prty_No'       => $partyData['role'] ?? null,
                    'Agmt_Prty_Org_Name' => $partyData['org_name'] ?? null,
                    'Agmt_Athr_Sgnt'     => $partyData['signatory'] ?? null,
                    'MoBy'               => $userData['User_UIN'] ?? 1,
                ];

                if ($partyId) {
                    $party = $agreement->parties()->where('Agmt_Prty_UIN', $partyId)->first();

                    if ($party) {
                        $party->update($attributes);
                        $submittedPartyIds[] = $party->Agmt_Prty_UIN;
                    }
                } else {
                    $party = $agreement->parties()->create(array_merge($attributes, [
                        'CrBy' => $userData['User_UIN'] ?? 1,
                        'MoBy' => $userData['User_UIN'] ?? 1,
                    ]));
                    $submittedPartyIds[] = $party->Agmt_Prty_UIN;
                }

                // Checkbox only submits when checked
                if (isset($partyData['is_primary']) && isset($party)) {
                    $primaryPartyUin = $party->Agmt_Prty_UIN;
                }
            }

            // Remove parties that existed before but were deleted in the form
            $agreement->parties()
                ->whereNotIn('Agmt_Prty_UIN', $submittedPartyIds)
                ->delete();

            // Update primary party reference
            if ($primaryPartyUin !== null) {
                $agreement->update(['Agmt_Prty_UIN' => $primaryPartyUin]);
            }

            return $agreement->fresh(['parties']);
            /**
             * Attachment update
             * We'll implement after file upload integration.
             */
        });
    }

    public function renewAgreement(int $agreementId, RenewAgreementRequest $request): Agreement
    {
        return DB::transaction(function () use ($agreementId, $request) {

            $oldAgreement = Agreement::with('parties')->findOrFail($agreementId);
            $userData = Session::get('partaker_user_data');

            if ($oldAgreement->childAgreements()->exists()) {
                throw new Exception('This agreement has already been renewed.');
            }

            // 1. Mark the old agreement inactive
            $oldAgreement->update([
                'Stau_UIN' => 'renewed',
                'MoBy'     => $userData['User_UIN'] ?? 1,
                'RenwOn' => now()
            ]);

            // 2. Handle new attachment if uploaded, otherwise carry over the old one
            $attachment = $oldAgreement->Agmt_Atch;
            $attachmentSizeKb = $oldAgreement->Agmt_Size_KB;

            $refNo = $request->ref_no ?? $this->generateAgreementRefNo($request->company_id);

            // 3. Create the new agreement as a copy of the old one, overriding only expiry (+ optional remarks/attachment)
            $newAgreement = Agreement::create([
                'Orga_UIN'       => $oldAgreement->Orga_UIN,
                'User_UIN'       => $oldAgreement->User_UIN,

                'Agmt_Catg_UIN'  => $request->agreement_category,
                'Agmt_Type_UIN'  => $request->agreement_type,
                'Agmt_Name'      => $request->agreement_name,

                'Agmt_File_no'        => $request->file_no,

                'Agmt_Strt_Date' => $request->agreement_date,
                'Agmt_Expy_Date' => $request->expiry_date, // ← the only date field that changes
                'Agmt_Sign_Date' => $request->signing_date,
                'Agmt_Ntry_Date' => $request->notary_date,

                'Agmt_Vson'      => $request->agreement_version,
                'Agmt_Drft_By'   => $request->drafted_by,
                'Agmt_Judn'      => $request->jurisdiction,
                'Agmt_Atch'      => $attachment,
                'Agmt_Size_KB'  => $attachmentSizeKb,

                'Agmt_Parn_UIN'  => $oldAgreement->Agmt_UIN, // link back to the original agreement
                'Agmt_Prty_UIN'  => null, // set after parties are copied

                'CrBy'           => $userData['User_UIN'] ?? 1,
                'MoBy'           => $userData['User_UIN'] ?? 1,

                'Stau_UIN'       => $request->expiry_date ? $this->determineStatusUin($request->expiry_date) : $this->determineStatusUin($oldAgreement->expiry_date),
                'Agmt_Ref_No' => $refNo,
                'Agmt_Depst_Amt' => $request->deposit_amount ?? null,
                'Agmt_Signed' => $request->agreement_signed,
                'Signed_Recv_Date' => $request->signed_document_received_date,
                'Agmt_Recv_Type' => $request->agreement_received_type
            ]);

            $remarks = trim($request->remarks);

            if (
                $remarks !== '<p><br></p>' &&
                !empty(trim(strip_tags($remarks)))
            ) {
                TranAgmtRemk::create([
                    'agmt_uin'       => $newAgreement->Agmt_UIN,
                    'agmt_remk_date' => now()->toDateString(),
                    'agmt_remk'      => $request->remarks,
                    'stau_uin'       => 1,
                    'crby'           => $userData['User_Name'] ?? 1,
                    'moby'           => $userData['User_Name'] ?? 1,
                ]);
            }

            // 4. Copy parties over to the new agreement
            $primaryPartyUin = null;

            foreach ($request->input('parties', []) as $index => $partyData) {

                $party = $newAgreement->parties()->create([
                    'Agmt_Prty_No'       => $partyData['role'] ?? null,
                    'Agmt_Prty_Org_Name' => $partyData['org_name'] ?? null,
                    'Agmt_Athr_Sgnt'     => $partyData['signatory'] ?? null,
                    'CrBy'               => $userData['User_UIN'] ?? 1,
                    'MoBy'               => $userData['User_UIN'] ?? 1,
                ]);

                if (isset($partyData['is_primary'])) {
                    $primaryPartyUin = $party->Agmt_Prty_UIN;
                }
            }

            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $attachment =  $this->storeAgreementAttachment($file, $newAgreement->Agmt_UIN, $newAgreement->Orga_UIN, $userData['User_UIN'], 'agreement');
                $attachmentSizeKb = $this->getFileSizeInKb($file);
            }

            if ($primaryPartyUin !== null) {
                $newAgreement->update(['Agmt_Prty_UIN' => $primaryPartyUin, 'Agmt_Atch' => $attachment, 'Agmt_Size_KB' => $attachmentSizeKb]);
            }

            return $newAgreement->load('parties');
        });
    }

    public function getHistory(int $agreementId)
    {
        $agreement = Agreement::findOrFail($agreementId);

        return $agreement->getRenewalHistory()->map(function ($item) {
            return [
                'id'          => $item->Agmt_UIN,
                'name'        => $item->Agmt_Name,
                'status'      => $item->Stau_UIN,
                'agmt_date'   => optional($item->Agmt_Sign_Date)->format('d M Y'),
                'expiry_date' => optional($item->Agmt_Expy_Date)->format('d M Y'),
                'created_date' => optional($item->created_at)->format('d M Y'),
                'file_no'     => $item->Agmt_File_no,
            ];
        });
    }

    private function determineStatusUin(?string $expiryDate): string
    {
        if (!$expiryDate) {
            return 'active';
        }

        $expiry = Carbon::parse($expiryDate)->startOfDay();
        $today  = Carbon::today();

        return $expiry->lt($today) ? 'expired' : 'active';
    }

    private function storeAgreementAttachment($file, $primaryKey, $orgUin, $userUin, string $storageType): string
    {
        return $this->fileStorageService->store(
            file: $file,
            folder: "attachments/{$orgUin}",
            primaryKey: $primaryKey,
            orgUin: $orgUin,
            userUin: $userUin,
            storageType: $storageType
        );
    }

    private function getFileSizeInKb(UploadedFile $file): int
    {
        return (int) round($file->getSize() / 1024);
    }

    public function getCompanyInformation($userUin)
    {
        $organisationUIN = UserOrgaRela::where('User_UIN', $userUin)
            ->pluck('Orga_UIN')
            ->values();

        return OrganisationDetails::whereIn('Orga_UIN', $organisationUIN)->get();
    }

    public function fetchUserData(string $token): ?array
    {
        try {
            $response = Http::timeout(10)
                ->post('https://api.partakers.in/User/getUserData', [
                    'token' => $token,
                ]);

            if ($response->failed()) {
                Log::warning('Partakers getUserData failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return null;
            }

            return $response->json();
        } catch (Throwable $e) {
            Log::error('Partakers getUserData exception: ' . $e->getMessage());

            return null;
        }
    }

    public function storeDraftAgreement(
        array $validated,
        ?string $accessToken,
        ?UploadedFile $draftFile,
        ?UploadedFile $checklistFile,
    ): AgmtDraft {
        $userData = Session::get('partaker_user_data');

        $draftFileName = null;
        $checklistFileName = null;

        $draft =  AgmtDraft::create([
            'Orga_UIN'              => $validated['company_id'],
            'User_UIN'              => $userData['User_UIN'] ?? null ?? null,

            'Agmt_Catg_UIN'         => $validated['agreement_category'],
            'Agmt_Type_UIN'         => $validated['agreement_type'],
            'Agmt_Name'             => $validated['agreement_name'],

            'Agmt_Vson'             => $validated['draft_version_no'] ?? null,

            'Agmt_Aprv_By'          => $validated['approved_by'] ?? null,
            'Agmt_Aprv_Date'        => $validated['approved_date'] ?? null,

            'Agmt_Drft_File_Name'   => $draftFileName,
            'Agmt_Chklst_File_Name' => $checklistFileName,
            'CrOn' => now()
        ]);

        $draftFileName = $draftFile
            ? $this->storeAgreementAttachment($draftFile, $draft->id, $validated['company_id'], $userData['User_UIN'] ?? null, 'draft')
            : null;

        $checklistFileName = $checklistFile
            ? $this->storeAgreementAttachment($checklistFile, $draft->id, $validated['company_id'], $userData['User_UIN'] ?? null, 'draft')
            : null;

        $draft->update([
            'Agmt_Drft_File_Name'   => $draftFileName,
            'Agmt_Chklst_File_Name' => $checklistFileName,
        ]);

        return $draft->refresh();
    }

    public function getDraftAgreements(
        ?string $search,
        ?string $dateFrom,
        ?string $dateTo,
        int $perPage,
        int|string $companyId,
    ) {
        $paginated = AgmtDraft::query()
            ->where('Orga_UIN', $companyId)
            ->when($search, function ($query, $search) {
                $query->where('Agmt_Name', 'like', "%{$search}%");
            })
            ->when($dateFrom, function ($query, $dateFrom) {
                $query->whereDate('Agmt_Aprv_Date', '>=', $dateFrom);
            })
            ->when($dateTo, function ($query, $dateTo) {
                $query->whereDate('Agmt_Aprv_Date', '<=', $dateTo);
            })
            ->latest()
            ->paginate($perPage);

        $paginated->getCollection()->transform(
            fn($item) => (new AgmtDraftResource($item))->toArray(request())
        );

        return $paginated->toArray();
    }

    public function generateAgreementRefNo(int|string $orgaUin): string
    {
        $company = OrganisationDetails::where('Orga_UIN', $orgaUin)->first();
        $companyCode = $company->Orga_UIN ?? 'ORG';

        $today = now()->format('Ymd');

        return DB::transaction(function () use ($companyCode, $orgaUin, $today) {
            $todaysCount = Agreement::where('Orga_UIN', $orgaUin)
                ->whereDate('created_at', now()->toDateString())
                ->lockForUpdate()
                ->count();

            $sequence = str_pad((string) ($todaysCount + 1), 3, '0', STR_PAD_LEFT);

            do {
                $randomSegment = strtoupper(Str::random(4));
                $refNo = "{$companyCode}/{$today}#{$sequence}#{$randomSegment}";
            } while (
                Agreement::where('Orga_UIN', $orgaUin)
                ->where('Agmt_Ref_No', $refNo)
                ->exists()
            );

            return $refNo;
        });
    }

    public function create(array $data): AgreementName
    {
        $userUin = Session::get('partaker_user_uin');

        return AgreementName::create([
            'Orga_UIN'      => $data['company_id'],
            'Agmt_Catg_UIN' => $data['agreement_category'],
            'Agmt_Type_UIN' => $data['agreement_type'],
            'Agmt_Name'     => $data['agmt_Name'],
            'Stau_UIN'      => $data['status'] ?? 1,
            'CrBy'          => $userUin,
            'MoBy'          => $userUin,
        ]);
    }

    public function getAgreementNames(
        ?string $search,
        ?string $dateFrom,
        ?string $dateTo,
        int $perPage,
        int|string $companyId,
    ) {
        $paginated = AgreementName::query()
            ->where('Orga_UIN', $companyId)
            ->when($search, function ($query, $search) {
                $query->where('Agmt_Name', 'like', "%{$search}%");
            })
            ->when($dateFrom, function ($query, $dateFrom) {
                $query->whereDate('CrOn', '>=', $dateFrom);
            })
            ->when($dateTo, function ($query, $dateTo) {
                $query->whereDate('CrOn', '<=', $dateTo);
            })
            ->withCount('drafts')
            ->latest()
            ->paginate($perPage);

        $paginated->getCollection()->transform(
            fn($item) => (new AgmtNameResource($item))->toArray(request())
        );

        return $paginated->toArray();
    }

    public function deleteAgreementName(int|string $id): bool
    {
        $agreementName = AgreementName::query()
            ->withCount('drafts')
            ->findOrFail($id);

        if ($agreementName->drafts_count > 0) {
            return false;
        }

        return $agreementName->delete();
    }

    public function generateViewLink(Agreement $agreement): string
    {
        $userUin = Session::get('partaker_user_uin');

        $link = ShareLink::create([
            'Link_Foreign_Id_UIN' => $agreement->getKey(),
            'Link_Type'           => $agreement->getMorphClass(),
            'Link_Token'          => Str::random(40),
            'Link_Expy_On'        => now()->addHours(24),
            'CrBy'                => $userUin,
            'MoBy'                => $userUin,
        ]);

        return route('agreements.public-view', ['token' => $link->Link_Token, 'timeStamp' => $link->Link_Expy_On]);
    }

    public function generateDraftViewLink(AgmtDraft $draft): string
    {
        $userUin = Session::get('partaker_user_uin');

        $link = ShareLink::create([
            'Link_Foreign_Id_UIN' => $draft->getKey(),
            'Link_Type'           => $draft->getMorphClass(),
            'Link_Token'          => Str::random(40),
            'Link_Expy_On'        => now()->addHours(24),
            'CrBy'                => $userUin,
            'MoBy'                => $userUin,
        ]);

        return route('agreements.public-view', ['token' => $link->Link_Token, 'timeStamp' => $link->Link_Expy_On]);
    }

    public function resolveShareLink(string $token): ShareLink
    {
        $link = ShareLink::where('Link_Token', $token)->first();

        if (is_null($link)) {
            abort(404);
        }

        if ($link->isExpired()) {
            abort(410, 'This link has expired.');
        }

        if (is_null($link->foreign)) {
            abort(404);
        }

        return $link;
    }

    public function getCategoriesWithChildren()
    {
        return AgreementCategories::with('children')->get();
    }

    public function getByOrgaCategoryAndType(
        string|int $orgaUin,
        string|int $categoryUin,
        string|int $typeUin,
    ): Collection {
        return AgreementName::query()
            ->where('Orga_UIN', $orgaUin)
            ->where('Agmt_Catg_UIN', $categoryUin)
            ->where('Agmt_Type_UIN', $typeUin)
            ->orderBy('Agmt_Name')
            ->get(['Agmt_Name', 'Agmt_Name_UIN']);
    }
}
