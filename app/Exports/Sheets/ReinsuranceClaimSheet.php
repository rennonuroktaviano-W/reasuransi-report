<?php

namespace App\Exports\Sheets;

use App\Models\ReinsuranceClaim;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ReinsuranceClaimSheet implements FromCollection, WithTitle, WithEvents
{
    use PreparesReportSheet;

    protected ?int $dataLastRow = null;

    protected const FIRST_COLUMN = 'A';

    protected const LAST_COLUMN = 'J';

    public function title(): string
    {
        return 'Borderaux Klaim';
    }

    public function collection(): Collection
    {
        $claims = ReinsuranceClaim::query()
            ->with('production:id,policy_number,insured_name,birth_date,sum_insured,ceded_amount')
            ->orderBy('id')
            ->get();

        $headers = [
            'No Klaim',
            'No Polis',
            'Nama Tertanggung',
            'Penyebab Meninggal/Klaim',
            'Tanggal Lahir',
            'Total Nilai Klaim',
            'Uang Pertanggungan (UP Utama)',
            'Porsi Klaim Reasuransi (Recovery)',
            'UP direasuransikan (Ceded)',
            'Status Klaim',
        ];

        $data = $claims->map(function (ReinsuranceClaim $claim) {
            $production = $claim->production;

            return [
                $claim->claim_number,
                $production?->policy_number ?? '-',
                $production?->insured_name ?? '-',
                $claim->claim_cause,
                $production ? Date::PHPToExcel($production->birth_date->getTimestamp()) : '',
                (float) $claim->total_claim_value,
                $production ? (float) $production->sum_insured : 0,
                (float) $claim->reinsurance_recovery,
                $production ? (float) $production->ceded_amount : 0,
                $claim->claim_status,
            ];
        })->toArray();

        $dataLast = 3 + count($data);
        $this->dataLastRow = $dataLast;

        $sumTotal = $dataLast >= 4
            ? '=SUM(F4:F' . $dataLast . ')'
            : '=SUM(F4:F3)';
        $sumRecovery = $dataLast >= 4
            ? '=SUM(H4:H' . $dataLast . ')'
            : '=SUM(H4:H3)';

        $rows = [
            array_fill(0, 10, ''),
            array_fill(0, 10, ''),
            $headers,
        ];

        $rows = array_merge($rows, $data);

        $rows[] = [
            'TOTAL',
            '',
            '',
            '',
            '',
            $sumTotal,
            '',
            $sumRecovery,
            '',
            '',
        ];

        return collect($rows);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                $sheet = $event->sheet->getDelegate();
                $dataLast = $this->dataLastRow ?? 3;
                $totalRow = 'A' . ($dataLast + 1) . ':' . self::LAST_COLUMN . ($dataLast + 1);

                $sheet->setCellValue('A1', 'Laporan Klaim Reasuransi (Borderaux Klaim)');
                $this->applyTitle($sheet, 'Laporan Klaim Reasuransi (Borderaux Klaim)', 'A1:J1', 10);
                $this->applyHeader($sheet, self::LAST_COLUMN);
                $this->applyTableBorders($sheet, self::FIRST_COLUMN . '3:' . self::LAST_COLUMN . ($dataLast + 1));
                $this->applyZebra($sheet, 'A', 'J', 'F2F2F2');
                $this->applyColumnWidths($sheet, [
                    'A' => 14,
                    'B' => 16,
                    'C' => 26,
                    'D' => 42,
                    'E' => 14,
                    'F' => 22,
                    'G' => 24,
                    'H' => 24,
                    'I' => 24,
                    'J' => 20,
                ]);

                if ($dataLast >= 4) {
                    $this->setColumnNumberFormat($sheet, 'E', '4', (string) $dataLast, 'dd/mm/yyyy');
                    foreach (['F', 'G', 'H', 'I'] as $column) {
                        $this->setColumnNumberFormat($sheet, $column, '4', (string) $dataLast, '#,##0');
                    }

                    $this->applyAlignment($sheet, 'A4:B' . $dataLast, Alignment::HORIZONTAL_CENTER);
                    $this->applyAlignment($sheet, 'E4:E' . $dataLast, Alignment::HORIZONTAL_CENTER);
                    $this->applyAlignment($sheet, 'J4:J' . $dataLast, Alignment::HORIZONTAL_CENTER);
                    $this->applyAlignment($sheet, 'F4:I' . $dataLast, Alignment::HORIZONTAL_RIGHT);

                    $sheet->getStyle('D4:D' . $dataLast)->getAlignment()->setWrapText(true);
                    $sheet->getStyle('J4:J' . $dataLast)->getAlignment()->setWrapText(true);
                }

                $sheet->mergeCells('A' . ($dataLast + 1) . ':E' . ($dataLast + 1));
                $this->applyTotalStyle($sheet, $totalRow);
                foreach (['F', 'H'] as $column) {
                    $this->setColumnNumberFormat($sheet, $column, '4', (string) ($dataLast + 1), '#,##0');
                }

                $this->freezeAndFilter($sheet, self::LAST_COLUMN);
                $this->setupPrint($sheet);
            },
        ];
    }
}