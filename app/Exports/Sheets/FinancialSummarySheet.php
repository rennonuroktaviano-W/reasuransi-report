<?php

namespace App\Exports\Sheets;

use App\Models\ReportSetting;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

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
        $premiLast = ReportDesign::DATA_START_ROW + $this->productionsCount - 1;
        $claimLast = ReportDesign::DATA_START_ROW + $this->claimsCount - 1;

        $premiRange = self::PREMI_SHEET.'!H'.ReportDesign::DATA_START_ROW.':H'.$premiLast;
        $recoveryRange = self::CLAIM_SHEET.'!H'.ReportDesign::DATA_START_ROW.':H'.$claimLast;

        $commissionRate = (float) ReportSetting::current()->commission_rate;

        $rows = [
            ['', '', ''],
            ['', '', ''],
            ['', '', ''],
            ['', '', ''],
            ['', '', ''],
            ['Metrik', 'Nilai (Rp)', 'Keterangan'],
            ['Premi Reasuransi Gross', '=SUM('.$premiRange.')', 'Jumlah seluruh premi reasuransi'],
            ['Tarif Komisi Reasuransi', $commissionRate, 'Persentase komisi dari premi gross'],
            ['Komisi Reasuransi', '=B7*B8', 'Premi gross dikali tarif komisi'],
            ['Premi Reasuransi Netto', '=B7-B9', 'Premi gross dikurangi komisi'],
            ['Recovery Klaim', '=SUM('.$recoveryRange.')', 'Jumlah seluruh recovery klaim'],
            ['Saldo Netto Setelah Klaim', '=B10-B11', 'Premi netto dikurangi recovery klaim'],
        ];

        return collect($rows);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                $sheet = $event->sheet->getDelegate();

                $this->applyCompanyHeader(
                    $sheet,
                    'C',
                    'RINGKASAN AKUN KEUANGAN REASURANSI'
                );
                $this->applyGroupBands($sheet, [
                    'A:C' => 'RINGKASAN AKUN KEUANGAN',
                ]);
                $this->applyHeader($sheet, 'C');
                $this->applyTableBorders($sheet, 'A6:C12');
                $this->applyColumnWidths($sheet, [
                    'A' => 32,
                    'B' => 24,
                    'C' => 52,
                ]);

                $this->setColumnNumberFormat($sheet, 'B', '7', '7', '#,##0');
                $this->setColumnNumberFormat($sheet, 'B', '8', '8', '0.0%');
                $this->setColumnNumberFormat($sheet, 'B', '9', '12', '#,##0');

                $this->applyAlignment($sheet, 'B7:B12', Alignment::HORIZONTAL_RIGHT);
                $this->applyAlignment($sheet, 'A7:A12', Alignment::HORIZONTAL_LEFT);
                $this->applyAlignment($sheet, 'C7:C12', Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('A6:C12')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                $sheet->getStyle('A12:C12')->getFont()
                    ->setBold(true)->setSize(10.5)->getColor()->setRGB(ReportDesign::WHITE);
                $sheet->getStyle('A12:C12')->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(ReportDesign::NAVY);
                $sheet->getStyle('A12:C12')->getBorders()->getTop()
                    ->setBorderStyle(Border::BORDER_MEDIUM)
                    ->getColor()->setRGB(ReportDesign::ACCENT);

                $sheet->freezePane('A7');
                $this->applySignatureFooter($sheet, 'C', 14);
                $this->applyDisclaimer($sheet, 'C', 18);
                $this->setupPrint($sheet);
            },
        ];
    }
}
