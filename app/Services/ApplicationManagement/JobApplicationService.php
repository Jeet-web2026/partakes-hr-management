<?php

namespace App\Services\ApplicationManagement;

use App\Models\HrMastJobAply;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Throwable;

class JobApplicationService
{
    private const UPLOAD_DIRECTORY = 'job-applications/bio-data';

    public function create(array $data, UploadedFile $bioData, string $token, string $orguin): HrMastJobAply
    {
        $disk = $this->disk();
        $path = $bioData->store(self::UPLOAD_DIRECTORY, $disk);

        try {
            return HrMastJobAply::create($this->prepare($data) + [
                'Bio_Data_Path' => $path,
                'Link_UIN'    => $token,
                'Orga_UIN' => $orguin
            ]);
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
}
