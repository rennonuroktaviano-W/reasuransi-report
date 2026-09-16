<?php

namespace Tests\Feature;

use App\Models\ReinsuranceClaim;
use App\Models\ReinsuranceProduction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClaimCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_daftar_klaim_dapat_ditampilkan(): void
    {
        $production = ReinsuranceProduction::factory()->create();
        ReinsuranceClaim::factory()->count(12)->create(['production_id' => $production->id]);

        $this->get(route('claims.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Claims/Index'));
    }

    public function test_halaman_tambah_klaim_dapat_dibuka(): void
    {
        $this->get(route('claims.create'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Claims/Form'));
    }

    public function test_admin_dapat_menambah_klaim(): void
    {
        $production = ReinsuranceProduction::factory()->create();

        $this->post(route('claims.store'), [
            'claim_number' => 'KLM-TEST-001',
            'production_id' => $production->id,
            'claim_cause' => 'Meninggal akibat sakit',
            'total_claim_value' => 100000000,
            'reinsurance_recovery' => 25000000,
            'claim_status' => 'Approved',
        ])->assertRedirect(route('claims.index'));

        $this->assertDatabaseHas('reinsurance_claims', [
            'claim_number' => 'KLM-TEST-001',
            'production_id' => $production->id,
        ]);
    }

    public function test_admin_dapat_mengedit_klaim(): void
    {
        $production = ReinsuranceProduction::factory()->create();
        $claim = ReinsuranceClaim::factory()->create(['production_id' => $production->id, 'claim_status' => 'Approved']);

        $this->put(route('claims.update', $claim), [
            'claim_number' => $claim->claim_number,
            'production_id' => $production->id,
            'claim_cause' => $claim->claim_cause,
            'total_claim_value' => $claim->total_claim_value,
            'reinsurance_recovery' => $claim->reinsurance_recovery,
            'claim_status' => 'Paid',
        ])->assertRedirect(route('claims.index'));

        $this->assertDatabaseHas('reinsurance_claims', [
            'id' => $claim->id,
            'claim_status' => 'Paid',
        ]);
    }

    public function test_admin_dapat_menghapus_klaim(): void
    {
        $production = ReinsuranceProduction::factory()->create();
        $claim = ReinsuranceClaim::factory()->create(['production_id' => $production->id]);

        $this->delete(route('claims.destroy', $claim))
            ->assertRedirect(route('claims.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('reinsurance_claims', ['id' => $claim->id]);
    }

    public function test_nomor_klaim_duplikat_ditolak(): void
    {
        $production = ReinsuranceProduction::factory()->create();
        ReinsuranceClaim::factory()->create(['production_id' => $production->id, 'claim_number' => 'KLM-DUP']);

        $this->post(route('claims.store'), [
            'claim_number' => 'KLM-DUP',
            'production_id' => $production->id,
            'claim_cause' => 'Penyebab apapun',
            'total_claim_value' => 10000000,
            'reinsurance_recovery' => 5000000,
            'claim_status' => 'Approved',
        ])->assertSessionHasErrors('claim_number');

        $this->assertDatabaseCount('reinsurance_claims', 1);
    }

    public function test_klaim_dengan_polis_tidak_ada_ditolak(): void
    {
        $this->post(route('claims.store'), [
            'claim_number' => 'KLM-NOPOLIS',
            'production_id' => 999,
            'claim_cause' => 'Penyebab apapun',
            'total_claim_value' => 10000000,
            'reinsurance_recovery' => 5000000,
            'claim_status' => 'Approved',
        ])->assertSessionHasErrors('production_id');
    }
}