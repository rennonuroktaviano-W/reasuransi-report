<?php

namespace App\Http\Controllers;

use App\Services\ReinsuranceReportService;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        protected ReinsuranceReportService $reportService,
    ) {}

    public function index(): Response
    {
        $dataCounts = $this->reportService->dataCounts();

        return Inertia::render('Dashboard', [
            'summary' => $this->reportService->summary(),
            'productionsCount' => $dataCounts['productions_count'],
            'claimsCount' => $dataCounts['claims_count'],
            'hasData' => $this->reportService->hasData(),
        ]);
    }
}