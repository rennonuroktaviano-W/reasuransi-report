<?php

namespace Tests\Feature;

use App\Models\ReinsuranceProduction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndonesianMessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_pesan_wajib_diisi_berbahasa_indonesia(): void
    {
        $this->post(route('productions.store'), [])
            ->assertSessionHasErrors(['policy_number', 'insured_name']);

        $errors = session('errors');
        $this->assertStringContainsString('Kolom Nomor Polis wajib diisi.', $errors->first('policy_number'));
        $this->assertStringContainsString('Kolom Nama Tertanggung wajib diisi.', $errors->first('insured_name'));
    }

    public function test_pesan_format_tanggal_berbahasa_indonesia(): void
    {
        $this->post(route('productions.store'), [
            'policy_number' => 'POL-001',
            'insured_name' => 'Test',
            'birth_date' => 'bukan-tanggal',
            'sum_insured' => 100,
            'retention' => 50,
            'ceded_amount' => 50,
            'reinsurance_type' => 'Surplus',
            'reinsurance_premium' => 10,
        ])->assertSessionHasErrors('birth_date');

        $errors = session('errors');
        $this->assertStringContainsString('Format tanggal', $errors->first('birth_date'));
    }

    public function test_pesan_tanggal_masa_depan_tidak_mengandung_kata_english(): void
    {
        $this->post(route('productions.store'), [
            'policy_number' => 'POL-001',
            'insured_name' => 'Test',
            'birth_date' => now()->addDay()->toDateString(),
            'sum_insured' => 100,
            'retention' => 50,
            'ceded_amount' => 50,
            'reinsurance_type' => 'Surplus',
            'reinsurance_premium' => 10,
        ])->assertSessionHasErrors('birth_date');

        $message = session('errors')->first('birth_date');
        $this->assertStringNotContainsStringIgnoringCase('today', $message);
        $this->assertStringContainsString('Tanggal Lahir tidak boleh di masa depan.', $message);
    }

    public function test_pesan_duplikat_nomor_polis_berbahasa_indonesia(): void
    {
        $existing = ReinsuranceProduction::factory()->create();

        $this->post(route('productions.store'), [
            'policy_number' => $existing->policy_number,
            'insured_name' => 'Test',
            'birth_date' => '1990-01-01',
            'sum_insured' => 100,
            'retention' => 50,
            'ceded_amount' => 50,
            'reinsurance_type' => 'Surplus',
            'reinsurance_premium' => 10,
        ])->assertSessionHasErrors('policy_number');

        $message = session('errors')->first('policy_number');
        $this->assertStringContainsString('sudah digunakan', $message);
    }
}
