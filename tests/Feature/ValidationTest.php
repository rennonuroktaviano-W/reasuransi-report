<?php

namespace Tests\Feature;

use App\Models\ReinsuranceProduction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ValidationTest extends TestCase
{
    use RefreshDatabase;

    private function validProductionPayload(array $overrides = []): array
    {
        return array_merge([
            'policy_number' => 'POL-VALID-001',
            'insured_name' => 'Valid User',
            'birth_date' => '1990-01-01',
            'sum_insured' => 1000000000,
            'retention' => 400000000,
            'ceded_amount' => 600000000,
            'reinsurance_type' => 'Surplus',
            'reinsurance_premium' => 50000000,
        ], $overrides);
    }

    public function test_tanggal_lahir_masa_depan_ditolak(): void
    {
        $this->post(route('productions.store'), $this->validProductionPayload([
            'birth_date' => now()->addDay()->toDateString(),
        ]))->assertSessionHasErrors('birth_date');
    }

    public function test_nilai_negatif_ditolak(): void
    {
        $this->post(route('productions.store'), $this->validProductionPayload([
            'reinsurance_premium' => -50000,
        ]))->assertSessionHasErrors('reinsurance_premium');

        $this->post(route('productions.store'), $this->validProductionPayload([
            'sum_insured' => -1000000,
        ]))->assertSessionHasErrors('sum_insured');
    }

    public function test_ceded_melebihi_up_ditolak(): void
    {
        $this->post(route('productions.store'), $this->validProductionPayload([
            'ceded_amount' => 1500000000,
            'retention' => 100000000,
        ]))->assertSessionHasErrors('ceded_amount');
    }

    public function test_retention_plus_ceded_harus_sama_dengan_up(): void
    {
        $this->post(route('productions.store'), $this->validProductionPayload([
            'sum_insured' => 1000000000,
            'retention' => 500000000,
            'ceded_amount' => 400000000,
        ]))->assertSessionHasErrors('sum_insured');
    }

    public function test_gabungan_retention_ceded_sama_up_berhasil(): void
    {
        $this->post(route('productions.store'), $this->validProductionPayload([
            'sum_insured' => 1000000000,
            'retention' => 500000000,
            'ceded_amount' => 500000000,
        ]))->assertRedirect(route('productions.index'));

        $this->assertDatabaseHas('reinsurance_productions', ['policy_number' => 'POL-VALID-001']);
    }

    public function test_recovery_melebihi_nilai_klaim_ditolak(): void
    {
        $production = ReinsuranceProduction::factory()->create();

        $this->post(route('claims.store'), [
            'claim_number' => 'KLM-VALID',
            'production_id' => $production->id,
            'claim_cause' => 'Penyebab apapun',
            'total_claim_value' => 100000000,
            'reinsurance_recovery' => 150000000,
            'claim_status' => 'Approved',
        ])->assertSessionHasErrors('reinsurance_recovery');
    }

    public function test_status_klaim_tidak_valid_ditolak(): void
    {
        $production = ReinsuranceProduction::factory()->create();

        $this->post(route('claims.store'), [
            'claim_number' => 'KLM-VALID2',
            'production_id' => $production->id,
            'claim_cause' => 'Penyebab apapun',
            'total_claim_value' => 100000000,
            'reinsurance_recovery' => 50000000,
            'claim_status' => 'Lainnya',
        ])->assertSessionHasErrors('claim_status');
    }
}