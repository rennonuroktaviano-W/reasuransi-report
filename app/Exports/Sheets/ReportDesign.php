<?php

namespace App\Exports\Sheets;

final class ReportDesign
{
    public const COMPANY_ROW = 1;

    public const TITLE_ROW = 3;

    public const META_ROW = 4;

    public const GROUP_BAND_ROW = 5;

    public const HEADER_ROW = 6;

    public const DATA_START_ROW = 7;

    public const COMPANY_NAME = 'DESWA INVISCO MULTITAMA';

    public const NAVY = '1F3864';

    public const HEADER_BLUE = '1F4E79';

    public const ACCENT = '2E74B5';

    public const BAND_FILL = 'DDEBF7';

    public const ZEBRA = 'F2F7FC';

    public const TOTAL_FILL = 'BDD7EE';

    public const GRID = 'C8CDD2';

    public const TEXT = '212121';

    public const MUTED = '7F7F7F';

    public const WHITE = 'FFFFFF';

    public static function documentNumber(): string
    {
        return 'RR-'.now()->format('Y-m');
    }

    public static function period(): string
    {
        return now()->translatedFormat('F Y');
    }
}
