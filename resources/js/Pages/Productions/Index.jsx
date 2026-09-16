import AppLayout from '@/Layouts/AppLayout';
import { Link, Head, router } from '@inertiajs/react';
import { useState } from 'react';
import Pagination from '@/Components/Pagination';
import ConfirmDialog from '@/Components/ConfirmDialog';
import { formatRupiah, formatDate } from '@/utils';

export default function Index({ productions, search }) {
    const [searchInput, setSearchInput] = useState(search || '');
    const [deleteTarget, setDeleteTarget] = useState(null);

    const handleSearch = (e) => {
        e.preventDefault();
        router.get('/productions', { search: searchInput }, { preserveState: true, replace: true });
    };

    const confirmDelete = () => {
        if (!deleteTarget) return;
        router.delete(`/productions/${deleteTarget.id}`, { preserveScroll: true });
        setDeleteTarget(null);
    };

    return (
        <AppLayout>
            <Head title="Produksi & Premi" />
            <div className="space-y-6">
                <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h1 className="text-2xl font-bold text-gray-900">Produksi &amp; Premi</h1>
                        <p className="text-sm text-gray-500 mt-1">Kelola data polis dan premi reasuransi.</p>
                    </div>
                    <Link
                        href="/productions/create"
                        className="text-center bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-5 rounded-lg transition"
                    >
                        + Tambah Polis
                    </Link>
                </div>

                <form onSubmit={handleSearch} className="flex gap-2">
                    <input
                        type="search"
                        value={searchInput}
                        onChange={(e) => setSearchInput(e.target.value)}
                        placeholder="Cari nomor polis atau nama tertanggung..."
                        className="flex-1 rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none"
                    />
                    <button
                        type="submit"
                        className="bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition"
                    >
                        Cari
                    </button>
                </form>

                <div className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="min-w-full divide-y divide-gray-200">
                            <thead className="bg-gray-50">
                                <tr>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No Polis</th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Tertanggung</th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal Lahir</th>
                                    <th className="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">UP Utama</th>
                                    <th className="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Retention</th>
                                    <th className="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Ceded</th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Jenis</th>
                                    <th className="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Premi</th>
                                    <th className="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody className="bg-white divide-y divide-gray-200">
                                {productions.data.length === 0 && (
                                    <tr>
                                        <td colSpan={9} className="px-4 py-10 text-center text-gray-400 text-sm">
                                            Belum ada data polis.
                                        </td>
                                    </tr>
                                )}
                                {productions.data.map((production) => (
                                    <tr key={production.id} className="hover:bg-gray-50">
                                        <td className="px-4 py-3 text-sm font-medium text-gray-900">{production.policy_number}</td>
                                        <td className="px-4 py-3 text-sm text-gray-700">{production.insured_name}</td>
                                        <td className="px-4 py-3 text-sm text-gray-600">{formatDate(production.birth_date)}</td>
                                        <td className="px-4 py-3 text-sm text-gray-700 text-right">{formatRupiah(production.sum_insured)}</td>
                                        <td className="px-4 py-3 text-sm text-gray-700 text-right">{formatRupiah(production.retention)}</td>
                                        <td className="px-4 py-3 text-sm text-gray-700 text-right">{formatRupiah(production.ceded_amount)}</td>
                                        <td className="px-4 py-3 text-sm text-gray-600">{production.reinsurance_type}</td>
                                        <td className="px-4 py-3 text-sm text-gray-900 font-medium text-right">{formatRupiah(production.reinsurance_premium)}</td>
                                        <td className="px-4 py-3 text-sm text-center whitespace-nowrap">
                                            <div className="inline-flex gap-1.5">
                                                <Link
                                                    href={`/productions/${production.id}/edit`}
                                                    className="text-xs bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 font-medium px-2.5 py-1 rounded transition"
                                                >
                                                    Edit
                                                </Link>
                                                <button
                                                    onClick={() => setDeleteTarget(production)}
                                                    className="text-xs bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 font-medium px-2.5 py-1 rounded transition"
                                                >
                                                    Hapus
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                    <div className="px-4 py-3 border-t border-gray-100">
                        <Pagination data={productions} />
                    </div>
                </div>

                <ConfirmDialog
                    open={deleteTarget !== null}
                    title="Hapus Data Polis"
                    message={
                        deleteTarget
                            ? `Apakah Anda yakin ingin menghapus polis ${deleteTarget.policy_number}?`
                            : ''
                    }
                    onConfirm={confirmDelete}
                    onCancel={() => setDeleteTarget(null)}
                />
            </div>
        </AppLayout>
    );
}