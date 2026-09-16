<?php

namespace App\Exports;

use App\Exports\Sheets\FinancialSummarySheet;
use App\Exports\Sheets\ProductionPremiumSheet;
use App\Exports\Sheets\ReinsuranceClaimSheet;
use App\Models\ReinsuranceClaim;
use App\Models\ReinsuranceProduction;
use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ReinsuranceWorkbookExport implements Export, WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new ProductionPremiumSheet,
            new ReinsuranceClaimSheet,
            new FinancialSummarySheet(
                (int) ReinsuranceProduction::query()->count(),
                (int) ReinsuranceClaim::query()->count(),
            ),
        ];
    }
}
