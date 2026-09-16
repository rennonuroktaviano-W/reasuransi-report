<?php

namespace App\Exports\Sheets;

use App\Models\ReportSetting;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

trait PreparesReportSheet
{
    protected function applyCompanyHeader(Worksheet $sheet, string $lastColumn, string $title): void
    {
        $companyName = (string) (ReportSetting::current()->company_name ?: ReportDesign::COMPANY_NAME);

        $sheet->setCellValue('A1', mb_strtoupper($companyName));
        $sheet->mergeCells('A1:'.$lastColumn.'1');
        $sheet->getStyle('A1')->getFont()
            ->setBold(true)->setSize(16)->getColor()->setRGB(ReportDesign::NAVY);
        $sheet->getStyle('A1')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_LEFT)
            ->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(32);

        $sheet->getRowDimension(2)->setRowHeight(7);

        $sheet->setCellValue('A'.ReportDesign::TITLE_ROW, $title);
        $sheet->mergeCells('A'.ReportDesign::TITLE_ROW.':'.$lastColumn.ReportDesign::TITLE_ROW);
        $sheet->getStyle('A'.ReportDesign::TITLE_ROW.':'.$lastColumn.ReportDesign::TITLE_ROW)->getFont()
            ->setBold(true)->setSize(13)->getColor()->setRGB(ReportDesign::NAVY);
        $sheet->getStyle('A'.ReportDesign::TITLE_ROW.':'.$lastColumn.ReportDesign::TITLE_ROW)->getFill()
            ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(ReportDesign::BAND_FILL);
        $sheet->getStyle('A'.ReportDesign::TITLE_ROW.':'.$lastColumn.ReportDesign::TITLE_ROW)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(3)->setRowHeight(28);

        $sheet->setCellValue('A'.ReportDesign::META_ROW, sprintf(
            'No. Dok : %s    |    Periode : %s    |    Tanggal Cetak : %s',
            ReportDesign::documentNumber(),
            ReportDesign::period(),
            now()->format('d/m/Y H:i')
        ));
        $sheet->mergeCells('A'.ReportDesign::META_ROW.':'.$lastColumn.ReportDesign::META_ROW);
        $sheet->getStyle('A'.ReportDesign::META_ROW.':'.$lastColumn.ReportDesign::META_ROW)->getFont()
            ->setSize(9)->getColor()->setRGB(ReportDesign::MUTED);
        $sheet->getStyle('A'.ReportDesign::META_ROW.':'.$lastColumn.ReportDesign::META_ROW)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(4)->setRowHeight(18);
    }

    /**
     * @param  array<string, string>  $groups  peta rentang => label, contoh ['A:C' => 'INFORMASI']
     */
    protected function applyGroupBands(Worksheet $sheet, array $groups): void
    {
        foreach ($groups as $range => $label) {
            [$from, $to] = explode(':', $range);
            $startCell = $from.ReportDesign::GROUP_BAND_ROW;
            $merge = $from.ReportDesign::GROUP_BAND_ROW.':'.$to.ReportDesign::GROUP_BAND_ROW;

            $sheet->setCellValue($startCell, $label);
            $sheet->mergeCells($merge);
            $sheet->getStyle($merge)->getFont()
                ->setBold(true)->setSize(9)->getColor()->setRGB(ReportDesign::WHITE);
            $sheet->getStyle($merge)->getFill()
                ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(ReportDesign::NAVY);
            $sheet->getStyle($merge)->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
        }

        $sheet->getRowDimension(ReportDesign::GROUP_BAND_ROW)->setRowHeight(22);
    }

    protected function applyHeader(Worksheet $sheet, string $lastColumn): void
    {
        $range = 'A'.ReportDesign::HEADER_ROW.':'.$lastColumn.ReportDesign::HEADER_ROW;
        $style = $sheet->getStyle($range);
        $style->getFont()->setBold(true)->setSize(10)->getColor()->setRGB(ReportDesign::WHITE);
        $style->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(ReportDesign::HEADER_BLUE);
        $style->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);

        $sheet->getRowDimension(ReportDesign::HEADER_ROW)->setRowHeight(34);
    }

    protected function applyTableBorders(Worksheet $sheet, string $range): void
    {
        $sheet->getStyle($range)->getBorders()->getInside()
            ->setBorderStyle(Border::BORDER_THIN)
            ->getColor()->setRGB(ReportDesign::GRID);
        $sheet->getStyle($range)->getBorders()->getOutline()
            ->setBorderStyle(Border::BORDER_MEDIUM)
            ->getColor()->setRGB(ReportDesign::NAVY);
    }

    protected function applyZebra(Worksheet $sheet, string $firstColumn, string $lastColumn, string $fillColor = ReportDesign::ZEBRA): void
    {
        $lastRow = min($sheet->getHighestRow(), $this->dataLastRow ?? ReportDesign::HEADER_ROW);

        for ($row = ReportDesign::DATA_START_ROW; $row <= $lastRow; $row++) {
            if ($row % 2 === 0) {
                $sheet->getStyle("{$firstColumn}{$row}:{$lastColumn}{$row}")
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setRGB($fillColor);
            }
        }
    }

    protected function applyTotalRow(Worksheet $sheet, string $range): void
    {
        $sheet->getStyle($range)->getFont()
            ->setBold(true)->setSize(10.5)->getColor()->setRGB(ReportDesign::NAVY);
        $sheet->getStyle($range)->getFill()
            ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(ReportDesign::TOTAL_FILL);
        $sheet->getStyle($range)->getBorders()->getTop()
            ->setBorderStyle(Border::BORDER_MEDIUM)
            ->getColor()->setRGB(ReportDesign::ACCENT);
        $sheet->getStyle($range)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    }

    protected function applyColumnFormats(Worksheet $sheet, array $columns, string $startRow = '', string $endRow = ''): void
    {
        $start = $startRow ?: (string) ReportDesign::DATA_START_ROW;
        $lastRow = $endRow ?: (string) $sheet->getHighestRow();

        foreach ($columns as $column => $format) {
            $sheet->getStyle("{$column}{$start}:{$column}{$lastRow}")
                ->getNumberFormat()
                ->setFormatCode($format);
        }
    }

    /**
     * @param  array<string, int>  $widths  peta kolom => lebar
     */
    protected function applyColumnWidths(Worksheet $sheet, array $widths): void
    {
        foreach ($widths as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }
    }

    protected function applyAlignment(Worksheet $sheet, string $range, string $horizontal): void
    {
        $sheet->getStyle($range)->getAlignment()->setHorizontal($horizontal);
    }

    protected function applySignatureFooter(Worksheet $sheet, string $lastColumn, int $startRow): void
    {
        [$leftCol, $rightCol] = $this->splitColumns($lastColumn);

        $sheet->setCellValue('A'.$startRow, 'Dibuat oleh,');
        $sheet->mergeCells('A'.$startRow.':'.$leftCol.$startRow);
        $sheet->getStyle('A'.$startRow)->getFont()->setSize(9)->setBold(true);
        $sheet->getStyle('A'.$startRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue($rightCol.$startRow, 'Disetujui oleh,');
        $sheet->mergeCells($rightCol.$startRow.':'.$lastColumn.$startRow);
        $sheet->getStyle($rightCol.$startRow)->getFont()->setSize(9)->setBold(true);
        $sheet->getStyle($rightCol.$startRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $emptyRow = $startRow + 1;
        $sheet->getRowDimension($emptyRow)->setRowHeight(34);

        $labelRow = $startRow + 2;
        $sheet->setCellValue('A'.$labelRow, '( Nama & Tanda Tangan )');
        $sheet->mergeCells('A'.$labelRow.':'.$leftCol.$labelRow);
        $sheet->getStyle('A'.$labelRow)->getFont()->setSize(8)->getColor()->setRGB(ReportDesign::MUTED);
        $sheet->getStyle('A'.$labelRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue($rightCol.$labelRow, '( Nama & Tanda Tangan )');
        $sheet->mergeCells($rightCol.$labelRow.':'.$lastColumn.$labelRow);
        $sheet->getStyle($rightCol.$labelRow)->getFont()->setSize(8)->getColor()->setRGB(ReportDesign::MUTED);
        $sheet->getStyle($rightCol.$labelRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }

    protected function applyDisclaimer(Worksheet $sheet, string $lastColumn, int $row): void
    {
        $sheet->setCellValue('A'.$row, sprintf(
            'Dokumen ini dibuat otomatis oleh Sistem Laporan Reasuransi pada %s. Data disajikan sesuai input yang tercatat di sistem.',
            now()->format('d/m/Y H:i')
        ));
        $sheet->mergeCells('A'.$row.':'.$lastColumn.$row);
        $sheet->getStyle('A'.$row.':'.$lastColumn.$row)->getFont()
            ->setSize(8)->setItalic(true)->getColor()->setRGB(ReportDesign::MUTED);
    }

    protected function freezeAndFilter(Worksheet $sheet, string $lastColumn): void
    {
        $sheet->freezePane('A'.ReportDesign::DATA_START_ROW);
        $sheet->setAutoFilter('A'.ReportDesign::HEADER_ROW.':'.$lastColumn.($this->dataLastRow ?? ReportDesign::HEADER_ROW));
    }

    protected function setupPrint(Worksheet $sheet, ?string $companyName = null): void
    {
        $sheet->setShowGridlines(false);

        $sheet->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
            ->setFitToWidth(1)
            ->setFitToHeight(0);
        $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, ReportDesign::HEADER_ROW);
        $sheet->getPageMargins()
            ->setTop(0.6)
            ->setRight(0.5)
            ->setBottom(0.7)
            ->setLeft(0.5);

        $companyName ??= (string) (ReportSetting::current()->company_name ?: ReportDesign::COMPANY_NAME);

        $sheet->getHeaderFooter()->setOddFooter(
            '&L'.mb_strtoupper($companyName)
            .'&C'.'Halaman &P dari &N'
            .'&R'.'Dicetak: &D &T'
        );
    }

    protected function setColumnNumberFormat(Worksheet $sheet, string $column, string $start, string $end, string $format): void
    {
        $sheet->getStyle("{$column}{$start}:{$column}{$end}")
            ->getNumberFormat()
            ->setFormatCode($format);
    }

    protected function setCellFill(Worksheet $sheet, string $cell, string $color): void
    {
        $sheet->getStyle($cell)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB($color);
    }

    protected function setCellFontColor(Worksheet $sheet, string $cell, string $color = ReportDesign::WHITE): void
    {
        $sheet->getStyle($cell)->getFont()->getColor()->setRGB($color);
    }

    /**
     * @return array{0: string, 1: string} kolom tengah kiri dan kanan
     */
    private function splitColumns(string $lastColumn): array
    {
        $count = Coordinate::columnIndexFromString($lastColumn);
        $mid = (int) floor($count / 2);

        return [
            Coordinate::stringFromColumnIndex($mid),
            Coordinate::stringFromColumnIndex($mid + 1),
        ];
    }
}
