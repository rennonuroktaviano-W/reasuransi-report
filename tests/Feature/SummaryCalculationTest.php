<?php

namespace Tests\Feature;

use App\Models\ReinsuranceClaim;
use App\Models\ReinsuranceProduction;
use App\Models\ReportSetting;
use App\Services\FinancialSummaryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SummaryCalculationTest extends TestCase
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

        $b = ReinsuranceProduction::create([
            'policy_number' => 'POL-0002',
            'insured_name' => 'User B',
            'birth_date' => '1990-07-25',
            'sum_insured' => 500000000,
            'retention' => 100000000,
            'ceded_amount' => 400000000,
            'reinsurance_type' => 'Quota Share',
            'reinsurance_premium' => 15000000,
        ]);

        ReinsuranceProduction::create([
            'policy_number' => 'POL-0003',
            'insured_name' => 'User C',
            'birth_date' => '1978-11-02',
            'sum_insured' => 750000000,
            'retention' => 350000000,
            'ceded_amount' => 400000000,
            'reinsurance_type' => 'Fac/Surplus',
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

        ReinsuranceClaim::create([
            'claim_number' => 'KLM-0002',
            'production_id' => $b->id,
            'claim_cause' => 'Penyakit kritis',
            'total_claim_value' => 80000000,
            'reinsurance_recovery' => 15000000,
            'claim_status' => 'Under Investigation',
        ]);
    }

    public function test_perhitungan_ringkasan_sesuai_seed_data(): void
    {
        $this->seedDataset();

        $summary = app(FinancialSummaryService::class)->summary();

        $this->assertSame(60000000.0, $summary['total_premi_gross']);
        $this->assertSame(6000000.0, $summary['komisi_reasuransi']);
        $this->assertSame(54000000.0, $summary['premi_netto']);
        $this->assertSame(35000000.0, $summary['total_recovery']);
        $this->assertSame(19000000.0, $summary['saldo_netto_setelah_klaim']);
    }

    public function test_ringkasan_kosong_saat_tidak_ada_data(): void
    {
        $summary = app(FinancialSummaryService::class)->summary();

        $this->assertSame(0.0, $summary['total_premi_gross']);
        $this->assertSame(0.0, $summary['komisi_reasuransi']);
        $this->assertSame(0.0, $summary['total_recovery']);
        $this->assertSame(0.0, $summary['saldo_netto_setelah_klaim']);
    }

    public function test_komisi_menggunakan_commission_rate_dari_settings(): void
    {
        ReportSetting::query()->create([
            'commission_rate' => 0.15,
            'company_name' => null,
            'report_title' => 'Laporan Reasuransi',
        ]);

        ReinsuranceProduction::factory()->create([
            'reinsurance_premium' => 100000000,
        ]);

        $summary = app(FinancialSummaryService::class)->summary();

        $this->assertSame(15000000.0, $summary['komisi_reasuransi']);
        $this->assertSame(85000000.0, $summary['premi_netto']);
    }

    public function test_halaman_ringkasan_menampilkan_nilai_yang_sama(): void
    {
        $this->seedDataset();

        $this->get(route('summary'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Summary/Index')
                ->has('summary')
                ->where('summary.total_premi_gross', 60000000));
    }
}