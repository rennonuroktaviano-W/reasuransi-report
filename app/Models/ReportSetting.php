<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportSetting extends Model
{
    protected $fillable = [
        'commission_rate',
        'company_name',
        'report_title',
    ];

    protected function casts(): array
    {
        return [
            'commission_rate' => 'decimal:4',
        ];
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate(
            ['id' => 1],
            [
                'commission_rate' => 0.10,
                'company_name' => 'Deswa Invisco Multitama',
                'report_title' => 'Laporan Reasuransi',
            ]
        );
    }
}
