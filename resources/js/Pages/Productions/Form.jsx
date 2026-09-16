import AppLayout from '@/Layouts/AppLayout';
import { Head, Link, router, useForm } from '@inertiajs/react';
import CurrencyInput from '@/Components/CurrencyInput';
import { formatRupiah, upMismatchInfo } from '@/utils';

const REINSURANCE_TYPES = ['Surplus', 'Quota Share', 'Fac/Surplus', 'Facultative', 'Other'];

export default function Form({ production }) {
    const isEdit = production !== null;

    const { data, setData, post, put, errors, processing } = useForm({
        policy_number: production?.policy_number ?? '',
        insured_name: production?.insured_name ?? '',
        birth_date: production?.birth_date?.slice(0, 10) ?? '',
        sum_insured: production?.sum_insured ?? '',
        retention: production?.retention ?? '',
        ceded_amount: production?.ceded_amount ?? '',
        reinsurance_type: production?.reinsurance_type ?? 'Surplus',
        reinsurance_premium: production?.reinsurance_premium ?? '',
    });

    const submit = (e) => {
        e.preventDefault();
        const common = {
            onSuccess: () => router.visit('/productions'),
        };

        if (isEdit) {
            put(`/productions/${production.id}`, {
                ...common,
                transform: transformNumbers,
            });
        } else {
            post('/productions', {
                ...common,
                transform: transformNumbers,
            });
        }
    };

    const transformNumbers = (payload) => ({
        ...payload,
        sum_insured: payload.sum_insured === '' || payload.sum_insured === null ? null : Number(payload.sum_insured),
        retention: payload.retention === '' || payload.retention === null ? null : Number(payload.retention),
        ceded_amount: payload.ceded_amount === '' || payload.ceded_amount === null ? null : Number(payload.ceded_amount),
        reinsurance_premium: payload.reinsurance_premium === '' || payload.reinsurance_premium === null ? null : Number(payload.reinsurance_premium),
    });

    const mismatch = upMismatchInfo(data.sum_insured, data.retention, data.ceded_amount);

    const inputClass = (hasError) =>
        `w-full rounded-lg border px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500 ${
            hasError ? 'border-red-400' : 'border-gray-300'
        }`;

    return (
        <AppLayout>
            <Head title={isEdit ? 'Edit Polis' : 'Tambah Polis'} />
            <div className="max-w-2xl space-y-6">
                <div>
                    <h1 className="text-2xl font-bold text-gray-900">
                        {isEdit ? 'Edit Data Polis' : 'Tambah Data Polis'}
                    </h1>
                    <p className="text-sm text-gray-500 mt-1">
                        Isi data polis dan premi reasuransi dengan lengkap. Field bertanda * wajib diisi.
                    </p>
                    <p className="text-sm text-blue-600 mt-2">
                        Petunjuk: Uang Pertanggungan (UP Utama) seharusnya terbagi menjadi Sendiri (Retention) + UP Direasuransikan (Ceded).
                    </p>
                </div>

                <form onSubmit={submit} className="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-5">
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">
                                Nomor Polis *
                            </label>
                            <input
                                type="text"
                                value={data.policy_number}
                                onChange={(e) => setData('policy_number', e.target.value)}
                                className={inputClass(errors.policy_number)}
                                placeholder="CON-0001"
                            />
                            {errors.policy_number && <p className="mt-1 text-xs text-red-600">{errors.policy_number}</p>}
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">
                                Nama Tertanggung *
                            </label>
                            <input
                                type="text"
                                value={data.insured_name}
                                onChange={(e) => setData('insured_name', e.target.value)}
                                className={inputClass(errors.insured_name)}
                                placeholder="Nama lengkap"
                            />
                            {errors.insured_name && <p className="mt-1 text-xs text-red-600">{errors.insured_name}</p>}
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">
                                Tanggal Lahir *
                            </label>
                            <input
                                type="date"
                                value={data.birth_date}
                                onChange={(e) => setData('birth_date', e.target.value)}
                                className={inputClass(errors.birth_date)}
                            />
                            {errors.birth_date && <p className="mt-1 text-xs text-red-600">{errors.birth_date}</p>}
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">
                                Uang Pertanggungan (UP Utama) *
                            </label>
                            <CurrencyInput
                                value={data.sum_insured}
                                onChange={(v) => setData('sum_insured', v)}
                                className={inputClass(errors.sum_insured)}
                                placeholder="0"
                            />
                            {errors.sum_insured && <p className="mt-1 text-xs text-red-600">{errors.sum_insured}</p>}
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">
                                Sendiri (Retention) *
                            </label>
                            <CurrencyInput
                                value={data.retention}
                                onChange={(v) => setData('retention', v)}
                                className={inputClass(errors.retention)}
                                placeholder="0"
                            />
                            {errors.retention && <p className="mt-1 text-xs text-red-600">{errors.retention}</p>}
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">
                                UP Direasuransikan (Ceded) *
                            </label>
                            <CurrencyInput
                                value={data.ceded_amount}
                                onChange={(v) => setData('ceded_amount', v)}
                                className={inputClass(errors.ceded_amount)}
                                placeholder="0"
                            />
                            {errors.ceded_amount && <p className="mt-1 text-xs text-red-600">{errors.ceded_amount}</p>}
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">
                                Jenis Reasuransi *
                            </label>
                            <select
                                value={data.reinsurance_type}
                                onChange={(e) => setData('reinsurance_type', e.target.value)}
                                className={inputClass(errors.reinsurance_type)}
                            >
                                {REINSURANCE_TYPES.map((type) => (
                                    <option key={type} value={type}>{type}</option>
                                ))}
                            </select>
                            {errors.reinsurance_type && <p className="mt-1 text-xs text-red-600">{errors.reinsurance_type}</p>}
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">
                                Premi Reasuransi *
                            </label>
                            <CurrencyInput
                                value={data.reinsurance_premium}
                                onChange={(v) => setData('reinsurance_premium', v)}
                                className={inputClass(errors.reinsurance_premium)}
                                placeholder="0"
                            />
                            {errors.reinsurance_premium && <p className="mt-1 text-xs text-red-600">{errors.reinsurance_premium}</p>}
                        </div>
                    </div>

                    {mismatch && (
                        <div className="rounded-lg bg-amber-50 border border-amber-200 px-4 py-3">
                            <p className="text-sm text-amber-800">
                                Perhatian: Uang Pertanggungan (UP Utama) sebesar {formatRupiah(mismatch.sum)} belum
                                sesuai dengan jumlah Sendiri (Retention) + UP Direasuransikan (Ceded) sebesar{' '}
                                {formatRupiah(mismatch.total)}. Data tetap bisa disimpan, tapi mohon periksa kembali.
                            </p>
                        </div>
                    )}

                    <div className="flex items-center justify-end gap-3 border-t border-gray-100 pt-5">
                        <Link
                            href="/productions"
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