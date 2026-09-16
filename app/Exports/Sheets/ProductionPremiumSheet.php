<?php

namespace App\Exports\Sheets;

use App\Models\ReinsuranceProduction;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ProductionPremiumSheet implements FromCollection, WithTitle, WithEvents
{
    use PreparesReportSheet;

    protected ?int $dataLastRow = null;

    protected const FIRST_COLUMN = 'A';

    protected const LAST_COLUMN = 'H';

    public function title(): string
    {
        return 'Borderaux Premi';
    }

    public function collection(): Collection
    {
        $productions = ReinsuranceProduction::query()
            ->orderBy('id')
            ->get(['policy_number', 'insured_name', 'birth_date', 'sum_insured', 'retention', 'ceded_amount', 'reinsurance_type', 'reinsurance_premium']);

        $headers = [
            'No Polis',
            'Nama Tertanggung',
            'Tanggal Lahir',
            'Uang Pertanggungan (UP Utama)',
            'Sendiri (Retention)',
            'UP direasuransikan (Ceded)',
            'Jenis Reasuransi',
            'Premi Reasuransi',
        ];

        $data = $productions->map(function (ReinsuranceProduction $production) {
            return [
                $production->policy_number,
                $production->insured_name,
                Date::PHPToExcel($production->birth_date->getTimestamp()),
                (float) $production->sum_insured,
                (float) $production->retention,
                (float) $production->ceded_amount,
                $production->reinsurance_type,
                (float) $production->reinsurance_premium,
            ];
        })->toArray();

        $dataLast = 3 + count($data);
        $this->dataLastRow = $dataLast;

        $sumFormula = $dataLast >= 4
            ? '=SUM(H4:H' . $dataLast . ')'
            : '=SUM(H4:H3)';

        $rows = [
            array_fill(0, 8, ''),
            array_fill(0, 8, ''),
            $headers,
        ];

        $rows = array_merge($rows, $data);

        $rows[] = [
            'TOTAL',
            '',
            '',
            '',
            '',
            '',
            '',
            $sumFormula,
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

                $sheet->setCellValue('A1', 'Laporan Produksi & Premi Reasuransi (Borderaux Premi)');
                $this->applyTitle($sheet, 'Laporan Produksi & Premi Reasuransi (Borderaux Premi)', 'A1:H1', 8);
                $this->applyHeader($sheet, self::LAST_COLUMN);
                $this->applyTableBorders($sheet, self::FIRST_COLUMN . '3:' . self::LAST_COLUMN . ($dataLast + 1));
                $this->applyZebra($sheet, 'A', 'H', 'F2F2F2');
                $this->applyColumnWidths($sheet, [
                    'A' => 16,
                    'B' => 30,
                    'C' => 14,
                    'D' => 22,
                    'E' => 20,
                    'F' => 24,
                    'G' => 18,
                    'H' => 22,
                ]);

                if ($dataLast >= 4) {
                    $this->setColumnNumberFormat($sheet, 'C', '4', (string) $dataLast, 'dd/mm/yyyy');
                    foreach (['D', 'E', 'F', 'H'] as $column) {
                        $this->setColumnNumberFormat($sheet, $column, '4', (string) $dataLast, '#,##0');
                    }

                    $this->applyAlignment($sheet, 'A4:A' . $dataLast, Alignment::HORIZONTAL_CENTER);
                    $this->applyAlignment($sheet, 'C4:C' . $dataLast, Alignment::HORIZONTAL_CENTER);
                    $this->applyAlignment($sheet, 'G4:G' . $dataLast, Alignment::HORIZONTAL_CENTER);
                    $this->applyAlignment($sheet, 'D4:H' . $dataLast, Alignment::HORIZONTAL_RIGHT);
                }

                $sheet->mergeCells('A' . ($dataLast + 1) . ':G' . ($dataLast + 1));
                $this->applyTotalStyle($sheet, $totalRow);
                $this->setColumnNumberFormat($sheet, 'H', '4', (string) ($dataLast + 1), '#,##0');

                $this->freezeAndFilter($sheet, self::LAST_COLUMN);
                $this->setupPrint($sheet);
            },
        ];
    }
}