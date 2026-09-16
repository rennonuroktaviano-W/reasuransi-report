<?php

namespace App\Http\Controllers;

use App\Exports\ReinsuranceWorkbookExport;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    public function download(): BinaryFileResponse
    {
        $filename = 'laporan_reasuransi_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(new ReinsuranceWorkbookExport(), $filename);
    }
}