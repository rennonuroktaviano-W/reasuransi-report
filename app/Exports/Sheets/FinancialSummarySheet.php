<?php

namespace App\Exports\Sheets;

use App\Models\ReportSetting;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class FinancialSummarySheet implements FromCollection, WithEvents, WithTitle
{
    use PreparesReportSheet;

    public const PREMI_SHEET = "'Borderaux Premi'";

    public const CLAIM_SHEET = "'Borderaux Klaim'";

    public function __construct(
        protected int $productionsCount,
        protected int $claimsCount,
    ) {}

    public function title(): string
    {
        return 'Ringkasan Keuangan';
    }

    public function collection(): Collection
    {
        $premiLast = 3 + $this->productionsCount;
        $claimLast = 3 + $this->claimsCount;

        $premiRange = self::PREMI_SHEET.'!H4:H'.max(4, $premiLast);
        $recoveryRange = self::CLAIM_SHEET.'!H4:H'.max(4, $claimLast);

        $commissionRate = (float) ReportSetting::current()->commission_rate;

        $rows = [
            ['', '', ''],
            ['', '', ''],
            ['Metrik', 'Nilai (Rp)', 'Keterangan'],
            ['Premi Reasuransi Gross', '=SUM('.$premiRange.')', 'Jumlah seluruh premi reasuransi'],
            ['Tarif Komisi Reasuransi', $commissionRate, 'Persentase komisi dari premi gross'],
            ['Komisi Reasuransi', '=B4*B5', 'Premi gross dikali tarif komisi'],
            ['Premi Reasuransi Netto', '=B4-B6', 'Premi gross dikurangi komisi'],
            ['Recovery Klaim', '=SUM('.$recoveryRange.')', 'Jumlah seluruh recovery klaim'],
            ['Saldo Netto Setelah Klaim', '=B7-B8', 'Premi netto dikurangi recovery klaim'],
        ];

        return collect($rows);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                $sheet = $event->sheet->getDelegate();

                $sheet->setCellValue('A1', 'Ringkasan Akun Keuangan Reasuransi');
                $this->applyTitle($sheet, 'Ringkasan Akun Keuangan Reasuransi', 'A1:C1', 3);
                $this->applyHeader($sheet, 'C');
                $this->applyTableBorders($sheet, 'A3:C9');
                $this->applyZebra($sheet, 'A', 'C', 'F2F2F2');
                $this->applyColumnWidths($sheet, [
                    'A' => 32,
                    'B' => 24,
                    'C' => 46,
                ]);

                $this->setColumnNumberFormat($sheet, 'B', '4', '4', '#,##0');
                $this->setColumnNumberFormat($sheet, 'B', '5', '5', '0.0%');
                $this->setColumnNumberFormat($sheet, 'B', '6', '9', '#,##0');

                $this->applyAlignment($sheet, 'B4:B9', Alignment::HORIZONTAL_RIGHT);
                $this->applyAlignment($sheet, 'A4:A9', Alignment::HORIZONTAL_LEFT);
                $this->applyTotalStyle($sheet, 'A9:C9');

                $sheet->getStyle('A3:C9')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            },
        ];
    }
}
