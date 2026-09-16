<?php

namespace Database\Factories;

use App\Models\ReinsuranceClaim;
use App\Models\ReinsuranceProduction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ReinsuranceClaim>
 */
class ReinsuranceClaimFactory extends Factory
{
    public function definition(): array
    {
        $totalClaimValue = fake()->randomFloat(2, 10_000_000, 500_000_000);
        $recovery = fake()->randomFloat(2, $totalClaimValue * 0.2, $totalClaimValue);

        return [
            'claim_number' => 'KLM-'.strtoupper(fake()->unique()->bothify('####??')),
            'production_id' => ReinsuranceProduction::factory(),
            'claim_cause' => fake()->sentence(6),
            'total_claim_value' => $totalClaimValue,
            'reinsurance_recovery' => $recovery,
            'claim_status' => fake()->randomElement([
                'Approved',
                'Under Investigation',
                'Paid',
                'Rejected',
            ]),
        ];
    }
}
