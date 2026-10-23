<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexJobApplicationRequest;
use App\Http\Requests\StoreJobApplicationRequest;
use App\Models\CountryMaster;
use App\Models\HrMastJobAply;
use App\Services\ApplicationManagement\JobApplicationService;
use App\Services\Common\TinyUrlService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class JobApplicationManagementController extends Controller
{
    public function __construct(private JobApplicationService $applications, private TinyUrlService $tinyUrlService) {}

    public function show(string $token, string $orguin): View
    {
        return view('application-management.apply-job', [
            'token'     => $token,
            'orguin' => $orguin,
            'countries' => CountryMaster::orderBy('Name')->get(['Admn_Cutr_Mast_UIN', 'Name']),
        ]);
    }

    public function store(string $token, string $orguin, StoreJobApplicationRequest $request): View
    {
        $validated = $request->safe()->except('Bio_Data_Path');

        $application = $this->applications->create($validated, $request->file('Bio_Data_Path'), $token, $orguin);

        return view('application-management.job-application-success', [
            'submittedAt' => $application->CrBy->format('M j, Y \a\t g:i A'),
        ]);
    }

    public function redirect(string $token): RedirectResponse
    {
        $link = $this->tinyUrlService->resolve($token);

        abort_if(!$link, 410, 'This link has expired or does not exist.');

        return match ($link->Link_Type) {
            'organisation' => redirect()->route('job-apply.show', [
                'token' => $link->Link_UIN,
                'orguin' => $link->Link_Foreign_Id_UIN
            ]),
            default        => abort(404),
        };
    }

    public function index(IndexJobApplicationRequest $request): JsonResponse
    {
        return response()->json(
            $this->applications->paginateApplications($request->validated())
        );
    }

    public function bioData(HrMastJobAply $application): StreamedResponse
    {
        abort_unless($application->Bio_Data_Path, 404);

        $disk = $this->applications->bioDataDisk();

        abort_unless(Storage::disk($disk)->exists($application->Bio_Data_Path), 404);

        return Storage::disk($disk)->response(
            $application->Bio_Data_Path,
            'bio-data-' . $application->Aply_UIN . '.pdf',
            ['Content-Type' => 'application/pdf'],
            'inline'
        );
    }
}
