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

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $sum = (float) $this->input('sum_insured');
            $retention = (float) $this->input('retention');
            $ceded = (float) $this->input('ceded_amount');

            if (abs(($retention + $ceded) - $sum) > 0.01) {
                $validator->errors()->add(
                    'sum_insured',
                    'Retention ditambah Ceded Amount (Sisa UP Direasuransikan) harus sama dengan Uang Pertanggungan (UP Utama).'
                );
            }
        });
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