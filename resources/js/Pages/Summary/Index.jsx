import AppLayout from '@/Layouts/AppLayout';
import { Head } from '@inertiajs/react';
import { formatRupiah } from '@/utils';

export default function Index({ summary, hasData }) {
    const rows = [
        {
            key: 'gross',
            label: 'Premi Reasuransi Gross',
            value: summary.total_premi_gross,
            note: 'Jumlah seluruh premi reasuransi dari data produksi.',
        },
        {
            key: 'komisi',
            label: 'Komisi Reasuransi',
            value: summary.komisi_reasuransi,
            note: `${(summary.commission_rate * 100).toFixed(1).replace('.', ',')}% dari premi gross (tarif komisi).`,
        },
        {
            key: 'netto',
            label: 'Premi Reasuransi Netto',
            value: summary.premi_netto,
            note: 'Premi gross dikurangi komisi reasuransi.',
        },
        {
            key: 'recovery',
            label: 'Recovery Klaim',
            value: summary.total_recovery,
            note: 'Jumlah seluruh porsi klaim reasuransi (recovery).',
        },
        {
            key: 'saldo',
            label: 'Saldo Netto Setelah Klaim',
            value: summary.saldo_netto_setelah_klaim,
            note: 'Premi netto dikurangi recovery klaim.',
            last: true,
        },
    ];

    return (
        <AppLayout>
            <Head title="Ringkasan" />
            <div className="max-w-3xl space-y-6">
                <div>
                    <h1 className="text-2xl font-bold text-gray-900">Ringkasan Akun Keuangan</h1>
                    <p className="text-sm text-gray-500 mt-1">
                        Nilai dihitung dari data saat ini dan sama dengan yang ada di workbook Excel.
                    </p>
                </div>

                {!hasData && (
                    <div className="rounded-lg bg-amber-50 border border-amber-200 text-amber-700 text-sm px-4 py-3">
                        ⚠ Belum ada data produksi. Tambahkan data terlebih dahulu agar ringkasan terisi.
                    </div>
                )}

                <div className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <table className="min-w-full divide-y divide-gray-200">
                        <thead className="bg-gray-50">
                            <tr>
                                <th className="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Metrik</th>
                                <th className="px-5 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Nilai</th>
                                <th className="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody className="bg-white divide-y divide-gray-200">
                            {rows.map((row) => (
                                <tr key={row.key} className={row.last ? 'bg-blue-50/60 font-semibold' : 'hover:bg-gray-50'}>
                                    <td className="px-5 py-4 text-sm font-medium text-gray-900">{row.label}</td>
                                    <td className={`px-5 py-4 text-sm text-right ${row.last ? 'font-bold text-gray-900' : 'text-gray-700'} ${row.last && row.value < 0 ? 'text-red-600' : ''}`}>
                                        {formatRupiah(row.value)}
                                    </td>
                                    <td className="px-5 py-4 text-sm text-gray-500">{row.note}</td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>

                <div className="flex justify-end">
                    <a
                        href="/reports/reinsurance.xlsx"
                        className="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-6 rounded-lg transition"
                    >
                        ⬇️ Generate &amp; Download Excel
                    </a>
                </div>
            </div>
        </AppLayout>
    );
}