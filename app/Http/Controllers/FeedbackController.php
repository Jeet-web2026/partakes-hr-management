<?php

namespace App\Http\Controllers;

use App\Http\Requests\General\StoreFeedbackRequest;
use App\Services\Common\FeedbackService;
use Illuminate\Support\Facades\Session;

class FeedbackController extends Controller
{
    public function __construct(protected FeedbackService $feedbackService) {}

    public function store(StoreFeedbackRequest $request)
    {
        $userUin = Session::get('partaker_user_uin');

        $this->feedbackService->store([
            ...$request->validated(),
            'created_by' => $userUin,
        ]);

        return back()->with('success', 'Thank you for sharing your valuable feedback. Your input is greatly appreciated.');
    }
}
