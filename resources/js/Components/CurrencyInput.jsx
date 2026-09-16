export default function CurrencyInput({ value, onChange, ...props }) {
    const display = value !== null && value !== undefined && value !== ''
        ? new Intl.NumberFormat('id-ID').format(Number(value))
        : '';

    const handleChange = (e) => {
        const raw = e.target.value.replace(/[^\d]/g, '');
        onChange(raw === '' ? '' : parseFloat(raw));
    };

    return (
        <input
            inputMode="numeric"
            value={value === null || value === undefined || value === '' ? '' : display}
            onChange={handleChange}
            {...props}
        />
    );
}