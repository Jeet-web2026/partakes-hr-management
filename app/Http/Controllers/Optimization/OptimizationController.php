<?php

namespace App\Http\Controllers\Optimization;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;

class OptimizationController extends Controller
{
    public function cache()
    {
        Artisan::call('optimize');
        Artisan::call('route:cache');

        return back()->with('success', 'Application optimized (config, route, view, event caches built).');
    }

    public function clear()
    {
        Artisan::call('optimize:clear');
        Artisan::call('route:clear');

        return back()->with('success', 'All caches cleared.');
    }
}
