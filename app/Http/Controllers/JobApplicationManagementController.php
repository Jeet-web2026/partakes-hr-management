<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexJobApplicationRequest;
use App\Http\Requests\StoreJobApplicationRequest;
use App\Models\CountryMaster;
use App\Models\HrMastJobAply;
use App\Models\MastIntvStep;
use App\Services\ApplicationManagement\JobApplicationService;
use App\Services\ApplicationManagement\MastDsigService;
use App\Services\Common\TinyUrlService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class JobApplicationManagementController extends Controller
{
    public function __construct(private JobApplicationService $applications, private TinyUrlService $tinyUrlService, private readonly MastDsigService $dsgService) {}

    public function show(string $token, string $orguin): View
    {
        return view('application-management.apply-job', [
            'token'     => $token,
            'orguin' => $orguin,
            'countries' => CountryMaster::orderBy('Name')->get(['Admn_Cutr_Mast_UIN', 'Name']),
            'designations' => $this->dsgService->getActiveByOrganisation($orguin)
        ]);
    }

    public function store(string $token, string $orguin, StoreJobApplicationRequest $request): RedirectResponse
    {
        try {
            $validated = $request->safe()->except('Bio_Data_Path');

            $application = $this->applications->create($validated, $request->file('Bio_Data_Path'), $token, $orguin);

            return redirect()
                ->route('job-application.success', [$application->CrBy->format('M j, Y \a\t g:i A')])
                ->with('sucesss', "Application sumitted successfully!");
        } catch (NotFoundHttpException $th) {
            throw $th;
        }
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

    public function success(string $submitTime): View|RedirectResponse
    {
        return view('application-management.job-application-success', [
            'submittedAt' => $submitTime,
        ]);
    }

    public function view(string $orguin, int $uin): View
    {
        return view('application-management.candidate-view', [
            ...$this->applications->getForView($orguin, $uin),
            'questions' => $this->applications->getActiveQuestionsByOrganisation($orguin),
        ]);
    }

    // Streams the PDF so the iframe works without exposing the storage path
    public function resume(string $orguin, int $uin)
    {
        $application = $this->applications->findForOrganisation($orguin, $uin);

        abort_unless($application->Resume_Path && Storage::exists($application->Resume_Path), 404);

        return Storage::response($application->Resume_Path, null, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function updateQuery(Request $request, string $orguin, int $uin): RedirectResponse
    {
        $data = $request->validate([
            'stage'    => ['required', 'integer'],
            'step'     => ['required', 'integer'],
            'flags'    => ['nullable', 'array'],
            'flags.*'  => ['uuid'],
            'question' => ['required', 'uuid'],
            'answer'   => ['required', 'string', 'max:5000'],
            'marks'    => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $userUin = Session::get('partaker_user_uin');
        abort_if(blank($userUin), 403);

        $this->applications->updateQuery($orguin, $uin, $data, (string) $userUin);

        return back()->with('success', 'Query updated.');
    }


    public function stageBysteps(int $stage, string $organisation): JsonResponse
    {
        $steps = MastIntvStep::query()
            ->forOrganisation($organisation)
            ->active()
            ->where('Intv_Stage', $stage)
            ->orderBy('Intv_Step_Name')
            ->get(['Intv_Step_UIN', 'Intv_Step_Name']);

        return response()->json($steps);
    }
}
