import { usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';

export default function FlashMessage() {
    const { flash } = usePage().props;
    const [visible, setVisible] = useState(false);
    const [message, setMessage] = useState('');
    const [type, setType] = useState('success');

    useEffect(() => {
        if (flash?.success) {
            setMessage(flash.success);
            setType('success');
            setVisible(true);
        } else if (flash?.error) {
            setMessage(flash.error);
            setType('error');
            setVisible(true);
        }
    }, [flash]);

    useEffect(() => {
        if (visible) {
            const timer = setTimeout(() => setVisible(false), 4000);
            return () => clearTimeout(timer);
        }
    }, [visible]);

    if (!visible) return null;

    return (
        <div className="fixed top-4 right-4 z-50 max-w-sm">
            <div
                className={`rounded-lg px-4 py-3 shadow-lg border ${
                    type === 'success'
                        ? 'bg-green-50 border-green-200 text-green-800'
                        : 'bg-red-50 border-red-200 text-red-800'
                }`}
            >
                <div className="flex items-center justify-between gap-3">
                    <span className="text-sm font-medium">{message}</span>
                    <button
                        onClick={() => setVisible(false)}
                        className="text-current opacity-60 hover:opacity-100"
                    >
                        &times;
                    </button>
                </div>
            </div>
        </div>
    );
}