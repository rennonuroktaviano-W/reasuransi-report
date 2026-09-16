<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReinsuranceClaim extends Model
{
    use HasFactory;

    protected $fillable = [
        'claim_number',
        'production_id',
        'claim_cause',
        'total_claim_value',
        'reinsurance_recovery',
        'claim_status',
    ];

    protected function casts(): array
    {
        return [
            'total_claim_value' => 'decimal:2',
            'reinsurance_recovery' => 'decimal:2',
        ];
    }

    /**
     * @return BelongsTo<ReinsuranceProduction, $this>
     */
    public function production(): BelongsTo
    {
        return $this->belongsTo(ReinsuranceProduction::class);
    }
}