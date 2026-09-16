import { Link, usePage } from '@inertiajs/react';
import FlashMessage from '@/Components/FlashMessage';

const NAV_ITEMS = [
    { label: 'Dashboard', href: '/', icon: '📊' },
    { label: 'Produksi & Premi', href: '/productions', icon: '📋' },
    { label: 'Klaim Reasuransi', href: '/claims', icon: '📑' },
    { label: 'Ringkasan', href: '/summary', icon: '📈' },
];

export default function AppLayout({ children }) {
    const { url } = usePage();

    const isActive = (href) => {
        if (href === '/') return url === '/';
        return url.startsWith(href);
    };

    return (
        <div className="min-h-screen flex">
            <aside className="w-64 bg-gray-800 text-white flex flex-col fixed h-full shrink-0">
                <div className="px-5 py-5 border-b border-gray-700">
                    <h1 className="text-lg font-bold tracking-tight leading-tight">
                        Reasuransi Report
                    </h1>
                    <p className="text-xs text-gray-400 mt-1">Sistem Laporan Reasuransi</p>
                </div>
                <nav className="flex-1 px-3 py-4 space-y-1">
                    {NAV_ITEMS.map((item) => (
                        <Link
                            key={item.href}
                            href={item.href}
                            className={`flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition ${
                                isActive(item.href)
                                    ? 'bg-blue-600 text-white'
                                    : 'text-gray-300 hover:bg-gray-700 hover:text-white'
                            }`}
                        >
                            <span className="text-lg">{item.icon}</span>
                            {item.label}
                        </Link>
                    ))}
                </nav>
                <div className="px-3 pb-4">
                    <a
                        href="/reports/reinsurance.xlsx"
                        className="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium bg-green-600 hover:bg-green-700 text-white transition"
                    >
                        <span className="text-lg">⬇️</span>
                        Generate Excel
                    </a>
                </div>
            </aside>

            <main className="flex-1 ml-64 min-h-screen">
                <div className="p-6">{children}</div>
            </main>

            <FlashMessage />
        </div>
    );
}