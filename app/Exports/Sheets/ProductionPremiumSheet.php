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

class ProductionPremiumSheet implements FromCollection, WithEvents, WithTitle
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

        $dataLast = ReportDesign::DATA_START_ROW + count($data) - 1;
        $this->dataLastRow = $dataLast;

        $sumFormula = $dataLast >= ReportDesign::DATA_START_ROW
            ? '=SUM(H'.ReportDesign::DATA_START_ROW.':H'.$dataLast.')'
            : '=SUM(H'.ReportDesign::DATA_START_ROW.':H'.(ReportDesign::DATA_START_ROW - 1).')';

        $rows = [
            array_fill(0, 8, ''),
            array_fill(0, 8, ''),
            array_fill(0, 8, ''),
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
                $dataLast = $this->dataLastRow ?? ReportDesign::HEADER_ROW;
                $totalRow = $dataLast + 1;

                $this->applyCompanyHeader(
                    $sheet,
                    self::LAST_COLUMN,
                    'LAPORAN PRODUKSI & PREMI REASURANSI — BORDERAUX PREMI'
                );
                $this->applyGroupBands($sheet, [
                    'A:C' => 'INFORMASI POLIS',
                    'D:F' => 'UANG PERTANGGUNGAN (Rp)',
                    'G:H' => 'REASURANSI',
                ]);
                $this->applyHeader($sheet, self::LAST_COLUMN);
                $this->applyTableBorders($sheet, self::FIRST_COLUMN.ReportDesign::HEADER_ROW.':'.self::LAST_COLUMN.$totalRow);
                $this->applyZebra($sheet, 'A', 'H');
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

                if ($dataLast >= ReportDesign::DATA_START_ROW) {
                    $this->setColumnNumberFormat($sheet, 'C', (string) ReportDesign::DATA_START_ROW, (string) $dataLast, 'dd/mm/yyyy');
                    foreach (['D', 'E', 'F', 'H'] as $column) {
                        $this->setColumnNumberFormat($sheet, $column, (string) ReportDesign::DATA_START_ROW, (string) $dataLast, '#,##0');
                    }

                    $this->applyAlignment($sheet, 'A'.ReportDesign::DATA_START_ROW.':A'.$dataLast, Alignment::HORIZONTAL_CENTER);
                    $this->applyAlignment($sheet, 'C'.ReportDesign::DATA_START_ROW.':C'.$dataLast, Alignment::HORIZONTAL_CENTER);
                    $this->applyAlignment($sheet, 'G'.ReportDesign::DATA_START_ROW.':G'.$dataLast, Alignment::HORIZONTAL_CENTER);
                    $this->applyAlignment($sheet, 'D'.ReportDesign::DATA_START_ROW.':H'.$dataLast, Alignment::HORIZONTAL_RIGHT);

                    $sheet->getStyle('B'.ReportDesign::DATA_START_ROW.':B'.$dataLast)->getAlignment()->setWrapText(true);
                }

                $sheet->mergeCells('A'.$totalRow.':G'.$totalRow);
                $this->applyTotalRow($sheet, 'A'.$totalRow.':'.self::LAST_COLUMN.$totalRow);
                $this->setColumnNumberFormat($sheet, 'H', (string) ReportDesign::DATA_START_ROW, (string) $totalRow, '#,##0');
                $this->applyAlignment($sheet, 'A'.$totalRow.':G'.$totalRow, Alignment::HORIZONTAL_CENTER);

                $this->freezeAndFilter($sheet, self::LAST_COLUMN);
                $this->applySignatureFooter($sheet, self::LAST_COLUMN, $totalRow + 2);
                $this->applyDisclaimer($sheet, self::LAST_COLUMN, $totalRow + 6);
                $this->setupPrint($sheet);
            },
        ];
    }
}
