<?php

namespace Tests\Feature;

use App\Exports\ReinsuranceWorkbookExport;
use App\Models\ReinsuranceClaim;
use App\Models\ReinsuranceProduction;
use App\Models\ReportSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class ReportExportTest extends TestCase
{
    use RefreshDatabase;

    private function seedDataset(): void
    {
        ReportSetting::query()->create([
            'commission_rate' => 0.10,
            'company_name' => 'PT Asuransi Contoh',
            'report_title' => 'Laporan Reasuransi',
        ]);

        $a = ReinsuranceProduction::create([
            'policy_number' => 'POL-0001',
            'insured_name' => 'User A',
            'birth_date' => '1985-03-12',
            'sum_insured' => 1000000000,
            'retention' => 400000000,
            'ceded_amount' => 600000000,
            'reinsurance_type' => 'Surplus',
            'reinsurance_premium' => 30000000,
        ]);

        ReinsuranceProduction::create([
            'policy_number' => 'POL-0002',
            'insured_name' => 'User B',
            'birth_date' => '1990-07-25',
            'sum_insured' => 500000000,
            'retention' => 100000000,
            'ceded_amount' => 400000000,
            'reinsurance_type' => 'Quota Share',
            'reinsurance_premium' => 15000000,
        ]);

        ReinsuranceClaim::create([
            'claim_number' => 'KLM-0001',
            'production_id' => $a->id,
            'claim_cause' => 'Meninggal dunia',
            'total_claim_value' => 100000000,
            'reinsurance_recovery' => 20000000,
            'claim_status' => 'Approved',
        ]);
    }

    public function test_endpoint_export_mengunduh_file_xlsx(): void
    {
        $this->seedDataset();

        $response = $this->get(route('reports.reinsurance'));

        $response->assertOk();
        $this->assertStringContainsString(
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            $response->headers->get('content-type')
        );
        $this->assertMatchesRegularExpression(
            '/laporan_reasuransi_\d{4}-\d{2}-\d{2}_\d{6}\.xlsx/',
            $response->headers->get('content-disposition') ?? ''
        );
    }

    public function test_workbook_memiliki_tiga_sheet_dengan_urutan_benar(): void
    {
        $this->seedDataset();

        Excel::store(new ReinsuranceWorkbookExport, 'test_workbook.xlsx', 'local');
        $path = Storage::disk('local')->path('test_workbook.xlsx');

        $spreadsheet = IOFactory::load($path);

        $this->assertSame(
            ['Borderaux Premi', 'Borderaux Klaim', 'Ringkasan Keuangan'],
            $spreadsheet->getSheetNames()
        );
    }

    public function test_sheet_pertama_memiliki_header_block_band_judul_dan_total(): void
    {
        $this->seedDataset();

        Excel::store(new ReinsuranceWorkbookExport, 'test_sheet1.xlsx', 'local');
        $path = Storage::disk('local')->path('test_sheet1.xlsx');

        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getSheet(0);

        $this->assertSame('PT ASURANSI CONTOH', $sheet->getCell('A1')->getValue());
        $this->assertSame('LAPORAN PRODUKSI & PREMI REASURANSI — BORDERAUX PREMI', $sheet->getCell('A3')->getValue());
        $this->assertSame('INFORMASI POLIS', $sheet->getCell('A5')->getValue());
        $this->assertSame('UANG PERTANGGUNGAN (Rp)', $sheet->getCell('D5')->getValue());
        $this->assertSame('REASURANSI', $sheet->getCell('G5')->getValue());
        $this->assertSame('No Polis', $sheet->getCell('A6')->getValue());
        $this->assertSame('Premi Reasuransi', $sheet->getCell('H6')->getValue());
        $this->assertSame('TOTAL', $sheet->getCell('A9')->getValue());
        $this->assertSame('=SUM(H7:H8)', $sheet->getCell('H9')->getValue());
        $this->assertSame('A7', $sheet->getFreezePane());
    }

    public function test_sheet_korporat_memiliki_footer_dan_print_pengaturan(): void
    {
        $this->seedDataset();

        Excel::store(new ReinsuranceWorkbookExport, 'test_sheet_korporat.xlsx', 'local');
        $path = Storage::disk('local')->path('test_sheet_korporat.xlsx');

        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getSheet(0);

        $this->assertFalse($sheet->getShowGridlines());
        $this->assertSame('Dibuat oleh,', $sheet->getCell('A11')->getValue());
        $this->assertSame('Disetujui oleh,', $sheet->getCell('E11')->getValue());
        $this->assertStringContainsString('Dokumen ini dibuat otomatis', $sheet->getCell('A15')->getValue());
        $this->assertStringContainsString('Halaman &P dari &N', (string) $sheet->getHeaderFooter()->getOddFooter());
    }

    public function test_sheet_ketiga_memuat_formula_lintas_sheet(): void
    {
        $this->seedDataset();

        Excel::store(new ReinsuranceWorkbookExport, 'test_sheet3.xlsx', 'local');
        $path = Storage::disk('local')->path('test_sheet3.xlsx');

        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getSheet(2);
        $this->assertSame('PT ASURANSI CONTOH', $sheet->getCell('A1')->getValue());
        $this->assertSame('RINGKASAN AKUN KEUANGAN REASURANSI', $sheet->getCell('A3')->getValue());
        $this->assertSame('Metrik', $sheet->getCell('A6')->getValue());
        $this->assertSame("=SUM('Borderaux Premi'!H7:H8)", $sheet->getCell('B7')->getValue());
        $this->assertSame('=B7*B8', $sheet->getCell('B9')->getValue());
        $this->assertSame('=B10-B11', $sheet->getCell('B12')->getValue());
    }

    public function test_ekspor_tetap_berhasil_saat_database_kosong(): void
    {
        $response = $this->get(route('reports.reinsurance'));

        $response->assertOk();
    }
}
