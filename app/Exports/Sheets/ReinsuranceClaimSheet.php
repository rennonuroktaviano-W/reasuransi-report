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

class ReinsuranceClaimSheet implements FromCollection, WithEvents, WithTitle
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

        $dataLast = ReportDesign::DATA_START_ROW + count($data) - 1;
        $this->dataLastRow = $dataLast;

        $safeStart = ReportDesign::DATA_START_ROW;
        $safeEnd = $dataLast >= $safeStart ? $dataLast : $safeStart - 1;
        $sumTotal = '=SUM(F'.$safeStart.':F'.$safeEnd.')';
        $sumRecovery = '=SUM(H'.$safeStart.':H'.$safeEnd.')';

        $rows = [
            array_fill(0, 10, ''),
            array_fill(0, 10, ''),
            array_fill(0, 10, ''),
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
                $dataLast = $this->dataLastRow ?? ReportDesign::HEADER_ROW;
                $totalRow = $dataLast + 1;

                $this->applyCompanyHeader(
                    $sheet,
                    self::LAST_COLUMN,
                    'LAPORAN KLAIM REASURANSI — BORDERAUX KLAIM'
                );
                $this->applyGroupBands($sheet, [
                    'A:B' => 'KODE',
                    'C:E' => 'DATA TERTANGGUNG',
                    'F:I' => 'NILAI KLAIM (Rp)',
                    'J:J' => 'STATUS',
                ]);
                $this->applyHeader($sheet, self::LAST_COLUMN);
                $this->applyTableBorders($sheet, self::FIRST_COLUMN.ReportDesign::HEADER_ROW.':'.self::LAST_COLUMN.$totalRow);
                $this->applyZebra($sheet, 'A', 'J');
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

                if ($dataLast >= ReportDesign::DATA_START_ROW) {
                    $this->setColumnNumberFormat($sheet, 'E', (string) ReportDesign::DATA_START_ROW, (string) $dataLast, 'dd/mm/yyyy');
                    foreach (['F', 'G', 'H', 'I'] as $column) {
                        $this->setColumnNumberFormat($sheet, $column, (string) ReportDesign::DATA_START_ROW, (string) $dataLast, '#,##0');
                    }

                    $this->applyAlignment($sheet, 'A'.ReportDesign::DATA_START_ROW.':B'.$dataLast, Alignment::HORIZONTAL_CENTER);
                    $this->applyAlignment($sheet, 'E'.ReportDesign::DATA_START_ROW.':E'.$dataLast, Alignment::HORIZONTAL_CENTER);
                    $this->applyAlignment($sheet, 'J'.ReportDesign::DATA_START_ROW.':J'.$dataLast, Alignment::HORIZONTAL_CENTER);
                    $this->applyAlignment($sheet, 'F'.ReportDesign::DATA_START_ROW.':I'.$dataLast, Alignment::HORIZONTAL_RIGHT);

                    $sheet->getStyle('D'.ReportDesign::DATA_START_ROW.':D'.$dataLast)->getAlignment()->setWrapText(true);
                    $sheet->getStyle('J'.ReportDesign::DATA_START_ROW.':J'.$dataLast)->getAlignment()->setWrapText(true);
                }

                $sheet->mergeCells('A'.$totalRow.':E'.$totalRow);
                $this->applyTotalRow($sheet, 'A'.$totalRow.':'.self::LAST_COLUMN.$totalRow);
                foreach (['F', 'H'] as $column) {
                    $this->setColumnNumberFormat($sheet, $column, (string) ReportDesign::DATA_START_ROW, (string) $totalRow, '#,##0');
                }
                $this->applyAlignment($sheet, 'A'.$totalRow.':E'.$totalRow, Alignment::HORIZONTAL_CENTER);

                $this->freezeAndFilter($sheet, self::LAST_COLUMN);
                $this->applySignatureFooter($sheet, self::LAST_COLUMN, $totalRow + 2);
                $this->applyDisclaimer($sheet, self::LAST_COLUMN, $totalRow + 6);
                $this->setupPrint($sheet);
            },
        ];
    }
}
