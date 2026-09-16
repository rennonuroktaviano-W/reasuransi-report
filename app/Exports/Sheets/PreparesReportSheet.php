<?php

namespace App\Exports\Sheets;

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

trait PreparesReportSheet
{
    protected function applyTitle(
        Worksheet $sheet,
        string $title,
        string $range,
        int $lastColumn,
    ): void {
        $sheet->mergeCells($range);

        $style = $sheet->getStyle($range);
        $style->getFont()->setBold(true)->setSize(14)->getColor()->setRGB('FFFFFF');
        $style->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('1F4E79');
        $style->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->getRowDimension(1)->setRowHeight(28);
    }

    protected function applyHeader(Worksheet $sheet, string $lastColumn): void
    {
        $range = 'A3:'.$lastColumn.'3';
        $style = $sheet->getStyle($range);
        $style->getFont()->setBold(true)->setSize(11)->getColor()->setRGB('FFFFFF');
        $style->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('2E74B5');
        $style->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);

        $sheet->getRowDimension(3)->setRowHeight(32);
    }

    protected function applyTableBorders(Worksheet $sheet, string $range): void
    {
        $sheet->getStyle($range)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN)
            ->getColor()->setRGB('A6A6A6');
    }

    protected function applyZebra(Worksheet $sheet, string $firstColumn, string $lastColumn, string $fillColor = 'F2F2F2'): void
    {
        for ($row = 4; $row <= $sheet->getHighestRow(); $row++) {
            if ($row % 2 === 0) {
                $sheet->getStyle("{$firstColumn}{$row}:{$lastColumn}{$row}")
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setRGB($fillColor);
            }
        }
    }

    protected function applyColumnFormats(Worksheet $sheet, array $columns, string $startRow = '4', string $endRow = ''): void
    {
        $lastRow = $endRow ?: (string) $sheet->getHighestRow();

        foreach ($columns as $column => $format) {
            $sheet->getStyle("{$column}{$startRow}:{$column}{$lastRow}")
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

    protected function applyTotalStyle(Worksheet $sheet, string $range, string $fill = 'D9E1F2'): void
    {
        $sheet->getStyle($range)
            ->getFont()
            ->setBold(true);
        $sheet->getStyle($range)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB($fill);
    }

    protected function freezeAndFilter(Worksheet $sheet, string $lastColumn, int $headerRow = 3, int $firstDataRow = 4): void
    {
        $dataLast = $this->dataLastRow ?? null;
        $filterEnd = (string) $sheet->getHighestRow();

        if ($dataLast && $dataLast > $headerRow) {
            $filterEnd = (string) $dataLast;
        }

        $sheet->freezePane("A{$firstDataRow}");
        $sheet->setAutoFilter("A{$headerRow}:{$lastColumn}{$filterEnd}");
    }

    protected function setupPrint(Worksheet $sheet, int $repeatStart = 3, int $repeatEnd = 3): void
    {
        $sheet->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
            ->setFitToWidth(1)
            ->setFitToHeight(0);
        $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd($repeatStart, $repeatEnd);
        $sheet->getPageMargins()
            ->setTop(0.5)
            ->setRight(0.5)
            ->setBottom(0.5)
            ->setLeft(0.5);
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

    protected function setCellFontColor(Worksheet $sheet, string $cell, string $color = 'FFFFFF'): void
    {
        $sheet->getStyle($cell)->getFont()->getColor()->setRGB($color);
    }
}
