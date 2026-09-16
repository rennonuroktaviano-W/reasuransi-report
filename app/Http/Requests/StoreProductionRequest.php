<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'policy_number' => trim($this->policy_number ?? ''),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'policy_number' => ['required', 'string', 'max:50', 'unique:reinsurance_productions,policy_number'],
            'insured_name' => ['required', 'string', 'max:150'],
            'birth_date' => ['required', 'date', 'before:today'],
            'sum_insured' => ['required', 'numeric', 'min:0'],
            'retention' => ['required', 'numeric', 'min:0'],
            'ceded_amount' => ['required', 'numeric', 'min:0', 'lte:sum_insured'],
            'reinsurance_type' => ['required', 'string', 'in:Surplus,Quota Share,Fac/Surplus,Facultative,Other'],
            'reinsurance_premium' => ['required', 'numeric', 'min:0'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'birth_date.before' => 'Tanggal Lahir tidak boleh di masa depan.',
            'ceded_amount.lte' => 'UP Direasuransikan (Ceded) tidak boleh lebih besar dari Uang Pertanggungan (UP Utama).',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'policy_number' => 'Nomor Polis',
            'insured_name' => 'Nama Tertanggung',
            'birth_date' => 'Tanggal Lahir',
            'sum_insured' => 'Uang Pertanggungan (UP Utama)',
            'retention' => 'Sendiri (Retention)',
            'ceded_amount' => 'UP Direasuransikan (Ceded)',
            'reinsurance_type' => 'Jenis Reasuransi',
            'reinsurance_premium' => 'Premi Reasuransi',
        ];
    }
}
