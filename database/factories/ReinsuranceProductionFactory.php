<?php

namespace Database\Factories;

use App\Models\ReinsuranceProduction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ReinsuranceProduction>
 */
class ReinsuranceProductionFactory extends Factory
{
    public function definition(): array
    {
        $sumInsured = fake()->randomFloat(2, 100_000_000, 2_000_000_000);
        $cededAmount = fake()->randomFloat(2, $sumInsured * 0.5, $sumInsured);
        $retention = round($sumInsured - $cededAmount, 2);

        return [
            'policy_number' => 'POL-'.strtoupper(fake()->unique()->bothify('####??')),
            'insured_name' => fake()->name(),
            'birth_date' => fake()->date('Y-m-d', '-18 years'),
            'sum_insured' => $sumInsured,
            'retention' => $retention,
            'ceded_amount' => $cededAmount,
            'reinsurance_type' => fake()->randomElement([
                'Surplus',
                'Quota Share',
                'Fac/Surplus',
                'Facultative',
                'Other',
            ]),
            'reinsurance_premium' => fake()->randomFloat(2, 1_000_000, 50_000_000),
        ];
    }
}
