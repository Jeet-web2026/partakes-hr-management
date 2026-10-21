<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobApplicationRequest;
use App\Models\CountryMaster;
use App\Services\ApplicationManagement\JobApplicationService;
use App\Services\Common\TinyUrlService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

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

    public function store(string $token, string $orguin, StoreJobApplicationRequest $request): RedirectResponse
    {
        $validated = $request->safe()->except('Bio_Data_Path');

        $this->applications->create($validated, $request->file('Bio_Data_Path'), $token, $orguin);

        return redirect()
            ->back()
            ->with('success', 'Your application has been submitted.');
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
}
