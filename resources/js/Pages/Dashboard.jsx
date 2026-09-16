import AppLayout from '@/Layouts/AppLayout';
import { formatRupiah } from '@/utils';
import { Link, Head } from '@inertiajs/react';

export default function Dashboard({ summary, productionsCount, claimsCount, hasData }) {
    const cards = [
        { label: 'Jumlah Polis', value: productionsCount, icon: '📋', color: 'bg-blue-500' },
        { label: 'Jumlah Klaim', value: claimsCount, icon: '📑', color: 'bg-amber-500' },
        { label: 'Premi Gross', value: formatRupiah(summary.total_premi_gross), icon: '💰', color: 'bg-green-500' },
        { label: 'Total Recovery', value: formatRupiah(summary.total_recovery), icon: '🏦', color: 'bg-purple-500' },
    ];

    return (
        <AppLayout>
            <Head title="Dashboard" />
            <div className="space-y-6">
                <div>
                    <h1 className="text-2xl font-bold text-gray-900">Dashboard</h1>
                    <p className="text-sm text-gray-500 mt-1">Ringkasan data laporan reasuransi.</p>
                </div>

                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    {cards.map((card) => (
                        <div key={card.label} className="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                            <div className="flex items-center gap-3 mb-3">
                                <span className={`w-9 h-9 rounded-lg flex items-center justify-center text-white text-lg ${card.color}`}>
                                    {card.icon}
                                </span>
                                <span className="text-sm text-gray-500">{card.label}</span>
                            </div>
                            <p className="text-xl font-bold text-gray-900">{card.value}</p>
                        </div>
                    ))}
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h2 className="font-semibold text-gray-900 mb-4">Ringkasan Keuangan</h2>
                        <dl className="space-y-3">
                            {[
                                ['Premi Gross', summary.total_premi_gross],
                                ['Komisi (10%)', summary.komisi_reasuransi],
                                ['Premi Netto', summary.premi_netto],
                                ['Recovery Klaim', summary.total_recovery],
                                ['Saldo Akhir', summary.saldo_netto_setelah_klaim],
                            ].map(([label, val]) => (
                                <div key={label} className="flex justify-between text-sm">
                                    <dt className="text-gray-500">{label}</dt>
                                    <dd className={`font-medium ${label === 'Saldo Akhir' && val < 0 ? 'text-red-600' : 'text-gray-900'}`}>
                                        {formatRupiah(val)}
                                    </dd>
                                </div>
                            ))}
                        </dl>
                    </div>

                    <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col items-center justify-center gap-4">
                        <span className="text-4xl">📄</span>
                        <p className="text-gray-500 text-sm text-center">
                            Klik tombol di bawah untuk mengunduh workbook Excel tiga worksheet.
                        </p>
                        <div className="flex flex-col gap-3 w-full">
                            <a
                                href="/reports/reinsurance.xlsx"
                                className="block text-center bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-6 rounded-lg transition"
                            >
                                ⬇️ Generate &amp; Download Excel
                            </a>
                            {!hasData && (
                                <p className="text-amber-600 text-xs text-center">
                                    ⚠ Belum ada data produksi. Workbook akan kosong.
                                </p>
                            )}
                            <div className="flex gap-3 justify-center mt-2">
                                <Link
                                    href="/productions"
                                    className="text-sm text-blue-600 hover:underline"
                                >
                                    Kelola Produksi →
                                </Link>
                                <Link
                                    href="/claims"
                                    className="text-sm text-blue-600 hover:underline"
                                >
                                    Kelola Klaim →
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}