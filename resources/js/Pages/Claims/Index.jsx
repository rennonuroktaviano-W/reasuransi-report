import AppLayout from '@/Layouts/AppLayout';
import { Link, Head, router } from '@inertiajs/react';
import { useState } from 'react';
import Pagination from '@/Components/Pagination';
import ConfirmDialog from '@/Components/ConfirmDialog';
import { formatRupiah } from '@/utils';

export default function Index({ claims, search, status, statuses }) {
    const [searchInput, setSearchInput] = useState(search || '');
    const [statusInput, setStatusInput] = useState(status || '');
    const [deleteTarget, setDeleteTarget] = useState(null);

    const handleFilter = (e) => {
        e.preventDefault();
        router.get('/claims', {
            search: searchInput,
            status: statusInput,
        }, { preserveState: true, replace: true });
    };

    const confirmDelete = () => {
        if (!deleteTarget) return;
        router.delete(`/claims/${deleteTarget.id}`, { preserveScroll: true });
        setDeleteTarget(null);
    };

    const statusBadge = (value) => {
        const map = {
            Approved: 'bg-green-100 text-green-700 border-green-200',
            'Under Investigation': 'bg-amber-100 text-amber-700 border-amber-200',
            Paid: 'bg-blue-100 text-blue-700 border-blue-200',
            Rejected: 'bg-red-100 text-red-700 border-red-200',
        };
        return map[value] || 'bg-gray-100 text-gray-700 border-gray-200';
    };

    return (
        <AppLayout>
            <Head title="Klaim Reasuransi" />
            <div className="space-y-6">
                <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h1 className="text-2xl font-bold text-gray-900">Klaim Reasuransi</h1>
                        <p className="text-sm text-gray-500 mt-1">Kelola data klaim reasuransi.</p>
                    </div>
                    <Link
                        href="/claims/create"
                        className="text-center bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-5 rounded-lg transition"
                    >
                        + Tambah Klaim
                    </Link>
                </div>

                <form onSubmit={handleFilter} className="flex flex-col sm:flex-row gap-2">
                    <input
                        type="search"
                        value={searchInput}
                        onChange={(e) => setSearchInput(e.target.value)}
                        placeholder="Cari nomor klaim atau nomor polis..."
                        className="flex-1 rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none"
                    />
                    <select
                        value={statusInput}
                        onChange={(e) => setStatusInput(e.target.value)}
                        className="sm:w-56 rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none"
                    >
                        <option value="">Semua Status</option>
                        {statuses.map((s) => (
                            <option key={s} value={s}>{s}</option>
                        ))}
                    </select>
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
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No Klaim</th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No Polis</th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tertanggung</th>
                                    <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Penyebab</th>
                                    <th className="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Nilai Klaim</th>
                                    <th className="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Recovery</th>
                                    <th className="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                    <th className="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody className="bg-white divide-y divide-gray-200">
                                {claims.data.length === 0 && (
                                    <tr>
                                        <td colSpan={8} className="px-4 py-10 text-center text-gray-400 text-sm">
                                            Belum ada data klaim.
                                        </td>
                                    </tr>
                                )}
                                {claims.data.map((claim) => (
                                    <tr key={claim.id} className="hover:bg-gray-50">
                                        <td className="px-4 py-3 text-sm font-medium text-gray-900">{claim.claim_number}</td>
                                        <td className="px-4 py-3 text-sm text-gray-700">{claim.production?.policy_number ?? '-'}</td>
                                        <td className="px-4 py-3 text-sm text-gray-700">{claim.production?.insured_name ?? '-'}</td>
                                        <td className="px-4 py-3 text-sm text-gray-600 max-w-xs">{claim.claim_cause}</td>
                                        <td className="px-4 py-3 text-sm text-gray-700 text-right">{formatRupiah(claim.total_claim_value)}</td>
                                        <td className="px-4 py-3 text-sm text-gray-700 text-right">{formatRupiah(claim.reinsurance_recovery)}</td>
                                        <td className="px-4 py-3 text-center">
                                            <span className={`inline-block px-2.5 py-1 rounded-full text-xs font-medium border ${statusBadge(claim.claim_status)}`}>
                                                {claim.claim_status}
                                            </span>
                                        </td>
                                        <td className="px-4 py-3 text-sm text-center whitespace-nowrap">
                                            <div className="inline-flex gap-1.5">
                                                <Link
                                                    href={`/claims/${claim.id}/edit`}
                                                    className="text-xs bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 font-medium px-2.5 py-1 rounded transition"
                                                >
                                                    Edit
                                                </Link>
                                                <button
                                                    onClick={() => setDeleteTarget(claim)}
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
                        <Pagination data={claims} />
                    </div>
                </div>

                <ConfirmDialog
                    open={deleteTarget !== null}
                    title="Hapus Data Klaim"
                    message={
                        deleteTarget
                            ? `Apakah Anda yakin ingin menghapus klaim ${deleteTarget.claim_number}?`
                            : ''
                    }
                    onConfirm={confirmDelete}
                    onCancel={() => setDeleteTarget(null)}
                />
            </div>
        </AppLayout>
    );
}