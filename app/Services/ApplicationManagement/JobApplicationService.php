<?php

namespace App\Services\ApplicationManagement;

use App\Models\HrMastJobAply;
use App\Models\ShareLink;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Throwable;

class JobApplicationService
{
    private const UPLOAD_DIRECTORY = 'job-applications/bio-data';

    public function create(array $data, UploadedFile $bioData, string $token, string $orguin): HrMastJobAply
    {
        $disk = $this->disk();
        $path = $bioData->store(self::UPLOAD_DIRECTORY, $disk);

        try {
            $newApplication = HrMastJobAply::create($this->prepare($data) + [
                'Bio_Data_Path' => $path,
                'Link_UIN'    => $token,
                'Orga_UIN' => $orguin,
                'App_Stus' => 'new'
            ]);

            ShareLink::where('Link_UIN', $token)->update([
                'Link_Expy_On' => now()
            ]);

            return $newApplication;
        } catch (Throwable $e) {
            Storage::disk($disk)->delete($path);

            throw $e;
        }
    }

    private function prepare(array $data): array
    {
        $ownMobile = (bool) (int) ($data['Own_Mo'] ?? 0);
        $ownLaptop = (bool) (int) ($data['Own_Lptp'] ?? 0);
        $hasLicence = (bool) (int) ($data['Drvi_Lisc'] ?? 0);
        $relocate  = (bool) (int) ($data['Rlca'] ?? 0);

        if (! $ownMobile) {
            $data['Mo_Type'] = null;
            $data['MO_OS']   = null;
        } elseif (($data['Mo_Type'] ?? null) !== 'Smart Phone') {
            $data['MO_OS'] = null;
        }

        if (! $ownLaptop) {
            $data['Lptp_OS'] = null;
        }

        if (! $hasLicence) {
            $data['Vcle_Type_Lisc'] = null;
        }

        if (! $relocate) {
            $data['Rlca_Area'] = null;
        }

        return [
            'FaNm'           => $data['FaNm'],
            'MiNm'           => $data['MiNm'] ?? null,
            'SuNm'           => $data['SuNm'],
            'Gend'           => $data['Gend'],
            'DOB'            => $data['DOB'],
            'MoTu'           => $data['MoTu'],
            'Cutr_UIN'       => $data['Cutr_UIN'],
            'PinCod_UIN'     => $data['PinCod_UIN'],
            'Mo_No'          => $data['Mo_No'],
            'Emai'           => $data['Emai'],
            'Own_Mo'         => $ownMobile,
            'Mo_Type'        => $data['Mo_Type'],
            'MO_OS'          => $data['MO_OS'],
            'Own_Lptp'       => $ownLaptop,
            'Lptp_OS'        => $data['Lptp_OS'],
            'Drvi_Lisc'      => $hasLicence,
            'Vcle_Type_Lisc' => $data['Vcle_Type_Lisc'],
            'Rlca'           => $relocate,
            'Rlca_Area'      => $data['Rlca_Area'],
            'Job_Intr'       => $data['Job_Intr'],
            'Job_Type'       => $data['Job_Type'],
        ];
    }

    private function disk(): string
    {
        return config('filesystems.job_application_disk', config('filesystems.default'));
    }

    public function paginateApplications(array $filters = []): LengthAwarePaginator
    {
        $search = $filters['search'] ?? null;

        $query = HrMastJobAply::query()
            ->with('country', 'interestedpositionDetails')
            ->when($filters['company_id'] ?? null, fn($q, $companyId) => $q->where('Orga_UIN', $companyId))
            ->when($search, function ($q, $term) {
                $q->where(function ($q) use ($term) {
                    $like = "%{$term}%";
                    $q->where('FaNm', 'like', $like)
                        ->orWhere('MiNm', 'like', $like)
                        ->orWhere('SuNm', 'like', $like)
                        ->orWhere('Emai', 'like', $like)
                        ->orWhere('Mo_No', 'like', $like)
                        ->orWhere('Job_Intr', 'like', $like);
                });
            })
            ->when($filters['job_type'] ?? null, fn($q, $jobType) => $q->where('Job_Type', $jobType))
            ->when($filters['date_from'] ?? null, fn($q, $from) => $q->whereDate('CrBy', '>=', $from))
            ->when($filters['date_to'] ?? null, fn($q, $to) => $q->whereDate('CrBy', '<=', $to))
            ->when(
                $filters['statuses'] ?? null,
                fn($q, $statuses) => $q->whereIn('App_Stus', (array) $statuses)
            )
            ->latest('Aply_UIN');

        $perPage = min((int) ($filters['per_page'] ?? 100), 250);

        return $query->paginate($perPage)->through(function (HrMastJobAply $application) {
            return [
                'id'          => (string) $application->Aply_UIN,
                'fullName'    => trim(implode(' ', array_filter([
                    $application->FaNm,
                    $application->MiNm,
                    $application->SuNm,
                ]))),
                'firstName'   => $application->FaNm,
                'middleName'  => $application->MiNm,
                'surname'     => $application->SuNm,
                'gender'      => $application->Gend,
                'dateOfBirth' => $application->DOB?->format('Y-m-d'),
                'motherTongue' => $application->MoTu,
                'country'     => $application->country->Name,
                'pincode'     => $application->PinCod_UIN,
                'mobile'      => $application->Mo_No,
                'email'       => $application->Emai,

                'ownMobile'   => $application->Own_Mo,
                'mobileType'  => $application->Mo_Type,
                'mobileOs'    => $application->MO_OS,

                'ownLaptop'   => $application->Own_Lptp,
                'laptopOs'    => $application->Lptp_OS,

                'drivingLicence' => $application->Drvi_Lisc,
                'licenceType'    => $application->Vcle_Type_Lisc,

                'relocate'        => $application->Rlca,
                'relocationArea'  => $application->Rlca_Area,

                'jobInterest' => $application->interestedpositionDetails?->Dsig_Name,
                'jobType'     => $application->Job_Type,

                'bioDataUrl' => $application->Bio_Data_Path
                    ? URL::temporarySignedRoute(
                        'job-applications.bio-data',
                        now()->addMinutes(15),
                        ['application' => $application->Aply_UIN]
                    )
                    : null,

                'companyId'  => $application->Orga_UIN,
                'linkId'     => $application->Link_UIN,
                'appliedOn'  => $application->CrBy?->format('Y-m-d H:i'),
            ];
        });
    }

    public function bioDataDisk(): string
    {
        return config('filesystems.job_application_disk', config('filesystems.default'));
    }
}
