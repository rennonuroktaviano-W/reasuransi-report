<?php

namespace App\Http\Controllers;

use App\Services\ReinsuranceReportService;
use Inertia\Inertia;
use Inertia\Response;

class SummaryController extends Controller
{
    public function __construct(
        protected ReinsuranceReportService $reportService,
    ) {}

    public function index(): Response
    {
        return Inertia::render('Summary/Index', [
            'summary' => $this->reportService->summary(),
            'hasData' => $this->reportService->hasData(),
        ]);
    }
}
