<?php

namespace Tests\Feature;

use App\Models\ReinsuranceProduction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductionCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_daftar_roduksi_dapat_ditampilkan(): void
    {
        ReinsuranceProduction::factory()->count(12)->create();

        $response = $this->get(route('productions.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page->component('Productions/Index'));
    }

    public function test_halaman_tambah_polis_dapat_dibuka(): void
    {
        $this->get(route('productions.create'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Productions/Form'));
    }

    public function test_admin_dapat_menambah_polis_baru(): void
    {
        $payload = [
            'policy_number' => 'POL-TEST-001',
            'insured_name' => 'Test User',
            'birth_date' => '1990-01-01',
            'sum_insured' => 1000000000,
            'retention' => 400000000,
            'ceded_amount' => 600000000,
            'reinsurance_type' => 'Surplus',
            'reinsurance_premium' => 50000000,
        ];

        $this->post(route('productions.store'), $payload)
            ->assertRedirect(route('productions.index'));

        $this->assertDatabaseHas('reinsurance_productions', [
            'policy_number' => 'POL-TEST-001',
            'insured_name' => 'Test User',
        ]);
    }

    public function test_admin_dapat_mengedit_polis(): void
    {
        $production = ReinsuranceProduction::factory()->create(['insured_name' => 'Nama Awal']);

        $response = $this->put(route('productions.update', $production), [
            'policy_number' => $production->policy_number,
            'insured_name' => 'Nama Diperbarui',
            'birth_date' => '1988-05-05',
            'sum_insured' => $production->sum_insured,
            'retention' => $production->retention,
            'ceded_amount' => $production->ceded_amount,
            'reinsurance_type' => $production->reinsurance_type,
            'reinsurance_premium' => 99999999,
        ]);

        $response->assertRedirect(route('productions.index'));
        $this->assertDatabaseHas('reinsurance_productions', [
            'id' => $production->id,
            'insured_name' => 'Nama Diperbarui',
        ]);
    }

    public function test_admin_dapat_menghapus_polis_tanpa_klaim(): void
    {
        $production = ReinsuranceProduction::factory()->create();

        $this->delete(route('productions.destroy', $production))
            ->assertRedirect(route('productions.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('reinsurance_productions', ['id' => $production->id]);
    }

    public function test_polis_yang_memiliki_klaim_tidak_dapat_dihapus(): void
    {
        $production = ReinsuranceProduction::factory()->create();
        \App\Models\ReinsuranceClaim::factory()->create(['production_id' => $production->id]);

        $this->delete(route('productions.destroy', $production))
            ->assertRedirect(route('productions.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('reinsurance_productions', ['id' => $production->id]);
    }

    public function test_nomor_polis_duplikat_ditolak(): void
    {
        ReinsuranceProduction::factory()->create(['policy_number' => 'POL-DUP']);

        $this->post(route('productions.store'), [
            'policy_number' => 'POL-DUP',
            'insured_name' => 'Orang Lain',
            'birth_date' => '1990-01-01',
            'sum_insured' => 1000000000,
            'retention' => 400000000,
            'ceded_amount' => 600000000,
            'reinsurance_type' => 'Quota Share',
            'reinsurance_premium' => 10000000,
        ])->assertSessionHasErrors('policy_number');

        $this->assertDatabaseCount('reinsurance_productions', 1);
    }

    public function test_nomor_polis_dinormalisasi_trim(): void
    {
        $this->post(route('productions.store'), [
            'policy_number' => '  POL-TRIM-001  ',
            'insured_name' => 'Trim User',
            'birth_date' => '1990-01-01',
            'sum_insured' => 1000000000,
            'retention' => 400000000,
            'ceded_amount' => 600000000,
            'reinsurance_type' => 'Surplus',
            'reinsurance_premium' => 50000000,
        ])->assertRedirect(route('productions.index'));

        $this->assertDatabaseHas('reinsurance_productions', ['policy_number' => 'POL-TRIM-001']);
    }
}