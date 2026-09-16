import AppLayout from '@/Layouts/AppLayout';
import { Head, Link, router, useForm } from '@inertiajs/react';
import CurrencyInput from '@/Components/CurrencyInput';

const STATUSES = ['Approved', 'Under Investigation', 'Paid', 'Rejected'];

export default function Form({ claim, productions }) {
    const isEdit = claim !== null;

    const { data, setData, post, put, errors, processing } = useForm({
        claim_number: claim?.claim_number ?? '',
        production_id: claim?.production_id ? String(claim.production_id) : '',
        claim_cause: claim?.claim_cause ?? '',
        total_claim_value: claim?.total_claim_value ?? '',
        reinsurance_recovery: claim?.reinsurance_recovery ?? '',
        claim_status: claim?.claim_status ?? 'Approved',
    });

    const transformNumbers = (payload) => ({
        ...payload,
        total_claim_value: payload.total_claim_value === '' || payload.total_claim_value === null ? null : Number(payload.total_claim_value),
        reinsurance_recovery: payload.reinsurance_recovery === '' || payload.reinsurance_recovery === null ? null : Number(payload.reinsurance_recovery),
    });

    const submit = (e) => {
        e.preventDefault();

        if (isEdit) {
            put(`/claims/${claim.id}`, { transform: transformNumbers, onSuccess: () => router.visit('/claims') });
        } else {
            post('/claims', { transform: transformNumbers, onSuccess: () => router.visit('/claims') });
        }
    };

    const inputClass = (hasError) =>
        `w-full rounded-lg border px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500 ${
            hasError ? 'border-red-400' : 'border-gray-300'
        }`;

    return (
        <AppLayout>
            <Head title={isEdit ? 'Edit Klaim' : 'Tambah Klaim'} />
            <div className="max-w-2xl space-y-6">
                <div>
                    <h1 className="text-2xl font-bold text-gray-900">
                        {isEdit ? 'Edit Data Klaim' : 'Tambah Data Klaim'}
                    </h1>
                    <p className="text-sm text-gray-500 mt-1">
                        Pilih polis dan lengkapi data klaim reasuransi. Field bertanda * wajib diisi.
                    </p>
                </div>

                <form onSubmit={submit} className="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-5">
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">
                                Nomor Klaim *
                            </label>
                            <input
                                type="text"
                                value={data.claim_number}
                                onChange={(e) => setData('claim_number', e.target.value)}
                                className={inputClass(errors.claim_number)}
                                placeholder="KLM-0001"
                            />
                            {errors.claim_number && <p className="mt-1 text-xs text-red-600">{errors.claim_number}</p>}
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">
                                Polis Terkait *
                            </label>
                            <select
                                value={data.production_id}
                                onChange={(e) => setData('production_id', e.target.value)}
                                className={inputClass(errors.production_id)}
                            >
                                <option value="">Pilih Polis...</option>
                                {productions.map((production) => (
                                    <option key={production.id} value={production.id}>
                                        {production.policy_number} - {production.insured_name}
                                    </option>
                                ))}
                            </select>
                            {errors.production_id && <p className="mt-1 text-xs text-red-600">{errors.production_id}</p>}
                        </div>
                        <div className="sm:col-span-2">
                            <label className="block text-sm font-medium text-gray-700 mb-1">
                                Penyebab Meninggal/Klaim *
                            </label>
                            <textarea
                                value={data.claim_cause}
                                onChange={(e) => setData('claim_cause', e.target.value)}
                                rows={3}
                                className={inputClass(errors.claim_cause)}
                                placeholder="Deskripsi penyebab klaim..."
                            />
                            {errors.claim_cause && <p className="mt-1 text-xs text-red-600">{errors.claim_cause}</p>}
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">
                                Total Nilai Klaim *
                            </label>
                            <CurrencyInput
                                value={data.total_claim_value}
                                onChange={(v) => setData('total_claim_value', v)}
                                className={inputClass(errors.total_claim_value)}
                                placeholder="0"
                            />
                            {errors.total_claim_value && <p className="mt-1 text-xs text-red-600">{errors.total_claim_value}</p>}
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">
                                Porsi Klaim Reasuransi (Recovery) *
                            </label>
                            <CurrencyInput
                                value={data.reinsurance_recovery}
                                onChange={(v) => setData('reinsurance_recovery', v)}
                                className={inputClass(errors.reinsurance_recovery)}
                                placeholder="0"
                            />
                            {errors.reinsurance_recovery && <p className="mt-1 text-xs text-red-600">{errors.reinsurance_recovery}</p>}
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">
                                Status Klaim *
                            </label>
                            <select
                                value={data.claim_status}
                                onChange={(e) => setData('claim_status', e.target.value)}
                                className={inputClass(errors.claim_status)}
                            >
                                {STATUSES.map((status) => (
                                    <option key={status} value={status}>{status}</option>
                                ))}
                            </select>
                            {errors.claim_status && <p className="mt-1 text-xs text-red-600">{errors.claim_status}</p>}
                        </div>
                    </div>

                    <div className="flex items-center justify-end gap-3 border-t border-gray-100 pt-5">
                        <Link
                            href="/claims"
                            className="text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 px-5 py-2.5 rounded-lg hover:bg-gray-200 transition"
                        >
                            Batal
                        </Link>
                        <button
                            type="submit"
                            disabled={processing}
                            className="bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white text-sm font-medium px-6 py-2.5 rounded-lg transition"
                        >
                            {isEdit ? 'Simpan Perubahan' : 'Simpan'}
                        </button>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}