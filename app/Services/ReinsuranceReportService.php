<?php

namespace App\Services;

use App\Models\ReinsuranceClaim;
use App\Models\ReinsuranceProduction;

class ReinsuranceReportService
{
    public function __construct(
        protected FinancialSummaryService $summaryService,
    ) {}

    /**
     * @return array<string, float>
     */
    public function summary(): array
    {
        return $this->summaryService->summary();
    }

    public function hasData(): bool
    {
        return ReinsuranceProduction::query()->exists();
    }

    public function dataCounts(): array
    {
        return [
            'productions_count' => ReinsuranceProduction::query()->count(),
            'claims_count' => ReinsuranceClaim::query()->count(),
        ];
    }
}
