<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReinsuranceProduction extends Model
{
    use HasFactory;

    protected $fillable = [
        'policy_number',
        'insured_name',
        'birth_date',
        'sum_insured',
        'retention',
        'ceded_amount',
        'reinsurance_type',
        'reinsurance_premium',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'sum_insured' => 'decimal:2',
            'retention' => 'decimal:2',
            'ceded_amount' => 'decimal:2',
            'reinsurance_premium' => 'decimal:2',
        ];
    }

    /**
     * @return HasMany<ReinsuranceClaim, $this>
     */
    public function claims(): HasMany
    {
        return $this->hasMany(ReinsuranceClaim::class, 'production_id');
    }
}
