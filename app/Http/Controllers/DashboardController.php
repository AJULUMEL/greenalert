<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(private DashboardService $service)
    {
        $this->middleware('auth');
    }

    /**
     * Display the main dashboard
     */
    public function index(): View
    {
        $data = $this->service->getDashboardData();

        return view('dashboard', $data);
    }
}
