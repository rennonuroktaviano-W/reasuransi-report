<?php

namespace App\Services;

use App\Models\ReinsuranceClaim;
use App\Models\ReinsuranceProduction;
use App\Models\ReportSetting;

class FinancialSummaryService
{
    public const KEYS = [
        'total_premi_gross',
        'commission_rate',
        'komisi_reasuransi',
        'premi_netto',
        'total_recovery',
        'saldo_netto_setelah_klaim',
    ];

    /**
     * Sumber kebenaran tunggal perhitungan ringkasan (frontend maupun export).
     *
     * @return array<string, float>
     */
    public function summary(): array
    {
        $totalPremiGross = (float) ReinsuranceProduction::query()->sum('reinsurance_premium');
        $totalRecovery = (float) ReinsuranceClaim::query()->sum('reinsurance_recovery');
        $commissionRate = (float) ReportSetting::current()->commission_rate;

        $komisi = round($totalPremiGross * $commissionRate, 2);
        $premiNetto = round($totalPremiGross - $komisi, 2);
        $saldo = round($premiNetto - $totalRecovery, 2);

        return [
            'total_premi_gross' => round($totalPremiGross, 2),
            'commission_rate' => $commissionRate,
            'komisi_reasuransi' => $komisi,
            'premi_netto' => $premiNetto,
            'total_recovery' => round($totalRecovery, 2),
            'saldo_netto_setelah_klaim' => $saldo,
        ];
    }
}