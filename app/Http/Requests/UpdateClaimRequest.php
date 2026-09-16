<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClaimRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'claim_number' => trim($this->claim_number ?? ''),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'claim_number' => [
                'required',
                'string',
                'max:50',
                'unique:reinsurance_claims,claim_number,'.$this->route('claim')?->id,
            ],
            'production_id' => ['required', 'integer', 'exists:reinsurance_productions,id'],
            'claim_cause' => ['required', 'string'],
            'total_claim_value' => ['required', 'numeric', 'min:0'],
            'reinsurance_recovery' => ['required', 'numeric', 'min:0', 'lte:total_claim_value'],
            'claim_status' => ['required', 'string', 'in:Approved,Under Investigation,Paid,Rejected'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'claim_number' => 'Nomor Klaim',
            'production_id' => 'Polis',
            'claim_cause' => 'Penyebab Meninggal/Klaim',
            'total_claim_value' => 'Total Nilai Klaim',
            'reinsurance_recovery' => 'Porsi Klaim Reasuransi (Recovery)',
            'claim_status' => 'Status Klaim',
        ];
    }
}
