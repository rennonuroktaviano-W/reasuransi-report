export function formatRupiah(value) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(value);
}

export function formatDate(date) {
    if (!date) return '-';
    const d = new Date(date);
    const day = String(d.getDate()).padStart(2, '0');
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const year = d.getFullYear();
    return `${day}/${month}/${year}`;
}

export function upMismatchInfo(sumInsured, retention, cededAmount) {
    const empty = (v) => v === '' || v === null || v === undefined;
    if (empty(sumInsured) || empty(retention) || empty(cededAmount)) return null;

    const sum = Number(sumInsured);
    const total = Number(retention) + Number(cededAmount);
    if (!Number.isFinite(sum) || !Number.isFinite(total) || sum <= 0) return null;

    const tolerance = Math.max(sum * 0.01, 10000);
    if (Math.abs(total - sum) <= tolerance) return null;

    return { sum, total };
}