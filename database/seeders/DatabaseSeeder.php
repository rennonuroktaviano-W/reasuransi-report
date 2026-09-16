<?php

namespace Database\Seeders;

use App\Models\ReinsuranceClaim;
use App\Models\ReinsuranceProduction;
use App\Models\ReportSetting;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        ReportSetting::query()->firstOrCreate(
            ['id' => 1],
            [
                'commission_rate' => 0.10,
                'company_name' => 'PT Asuransi Contoh',
                'report_title' => 'Laporan Reasuransi',
            ]
        );

        $productionSurplus = ReinsuranceProduction::create([
            'policy_number' => 'POL-0001',
            'insured_name' => 'Budi Santoso',
            'birth_date' => '1985-03-12',
            'sum_insured' => 1_000_000_000.00,
            'retention' => 400_000_000.00,
            'ceded_amount' => 600_000_000.00,
            'reinsurance_type' => 'Surplus',
            'reinsurance_premium' => 30_000_000.00,
        ]);

        $productionQuotaShare = ReinsuranceProduction::create([
            'policy_number' => 'POL-0002',
            'insured_name' => 'Siti Rahmawati',
            'birth_date' => '1990-07-25',
            'sum_insured' => 500_000_000.00,
            'retention' => 100_000_000.00,
            'ceded_amount' => 400_000_000.00,
            'reinsurance_type' => 'Quota Share',
            'reinsurance_premium' => 15_000_000.00,
        ]);

        ReinsuranceProduction::create([
            'policy_number' => 'POL-0003',
            'insured_name' => 'Ahmad Fauzi',
            'birth_date' => '1978-11-02',
            'sum_insured' => 750_000_000.00,
            'retention' => 350_000_000.00,
            'ceded_amount' => 400_000_000.00,
            'reinsurance_type' => 'Fac/Surplus',
            'reinsurance_premium' => 15_000_000.00,
        ]);

        ReinsuranceClaim::create([
            'claim_number' => 'KLM-0001',
            'production_id' => $productionSurplus->id,
            'claim_cause' => 'Meninggal dunia akibat kecelakaan lalu lintas',
            'total_claim_value' => 100_000_000.00,
            'reinsurance_recovery' => 20_000_000.00,
            'claim_status' => 'Approved',
        ]);

        ReinsuranceClaim::create([
            'claim_number' => 'KLM-0002',
            'production_id' => $productionQuotaShare->id,
            'claim_cause' => 'Diagnosis penyakit kritis (jantung)',
            'total_claim_value' => 80_000_000.00,
            'reinsurance_recovery' => 15_000_000.00,
            'claim_status' => 'Under Investigation',
        ]);
    }
}
