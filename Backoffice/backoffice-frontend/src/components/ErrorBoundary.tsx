import { Component, ErrorInfo, ReactNode } from 'react';

interface Props {
    children: ReactNode;
}

interface State {
    error: Error | null;
    info: ErrorInfo | null;
}

/**
 * ErrorBoundary global.
 *
 * Tanpa boundary, satu exception saat render (mis. permission undefined, response API
 * berbentuk lain) membuat React melepas seluruh tree -> layar putih / "force close".
 * Dengan boundary, user melihat pesan yang bisa dibaca + tombol reload, dan error asli
 * tetap tercatat di console untuk diagnosis.
 */
class ErrorBoundary extends Component<Props, State> {
    state: State = { error: null, info: null };

    static getDerivedStateFromError(error: Error): Partial<State> {
        return { error };
    }

    componentDidCatch(error: Error, info: ErrorInfo) {
        this.setState({ info });
        // eslint-disable-next-line no-console
        console.error('[ErrorBoundary]', error, info?.componentStack);
    }

    handleReload = () => {
        window.location.reload();
    };

    handleBackToDashboard = () => {
        this.setState({ error: null, info: null });
        window.location.href = '/';
    };

    render() {
        const { error, info } = this.state;
        if (!error) return this.props.children;

        return (
            <div className="min-h-[60vh] flex items-center justify-center p-6">
                <div className="w-full max-w-2xl rounded-xl border border-red-200 bg-white shadow-sm">
                    <div className="border-b border-red-100 bg-red-50 px-6 py-4 rounded-t-xl">
                        <h2 className="text-lg font-semibold text-red-700">Terjadi kesalahan saat menampilkan halaman</h2>
                        <p className="mt-1 text-sm text-red-600">
                            Halaman tidak dapat dirender. Silakan coba muat ulang; jika berulang, laporkan pesan di bawah ke administrator.
                        </p>
                    </div>
                    <div className="px-6 py-4 space-y-3">
                        <p className="text-sm font-medium text-gray-800">{error.name}: {error.message}</p>
                        {info?.componentStack && (
                            <pre className="max-h-48 overflow-auto whitespace-pre-wrap rounded bg-gray-50 p-3 text-xs text-gray-600">
                                {info.componentStack.trim()}
                            </pre>
                        )}
                        <div className="flex gap-3">
                            <button
                                type="button"
                                onClick={this.handleReload}
                                className="rounded-md bg-primary px-4 py-2 text-sm font-semibold text-white hover:opacity-90"
                            >
                                Muat ulang halaman
                            </button>
                            <button
                                type="button"
                                onClick={this.handleBackToDashboard}
                                className="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                            >
                                Kembali ke Dashboard
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}

export default ErrorBoundary;
