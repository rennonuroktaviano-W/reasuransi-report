import { router } from '@inertiajs/react';

export default function Pagination({ data }) {
    if (!data || data.last_page <= 1) return null;

    const goTo = (url) => {
        if (url) router.get(url, {}, { preserveState: true });
    };

    return (
        <nav className="mt-4 flex items-center justify-between">
            <span className="text-sm text-gray-600">
                Menampilkan {data.from}–{data.to} dari {data.total} data
            </span>
            <div className="flex items-center gap-1">
                {data.links.map((link, i) => {
                    const isDots = link.label === '...';
                    return isDots ? (
                        <span key={i} className="px-3 py-1.5 text-sm text-gray-400">…</span>
                    ) : (
                        <button
                            key={i}
                            onClick={() => goTo(link.url)}
                            disabled={link.active}
                            className={`px-3 py-1.5 text-sm rounded-md border transition ${
                                link.active
                                    ? 'bg-blue-600 text-white border-blue-600 cursor-default'
                                    : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                            }`}
                            dangerouslySetInnerHTML={{ __html: link.label }}
                        />
                    );
                })}
            </div>
        </nav>
    );
}