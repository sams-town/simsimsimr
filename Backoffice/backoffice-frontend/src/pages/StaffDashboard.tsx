import { useEffect } from 'react';
import { useDispatch } from 'react-redux';
import { setPageTitle, setTitle, setBreadcrumbTitle } from '@/store/themeConfigSlice';
import { getProfile } from '@/utils/auth';
import {
    ArrowDownCircle,
    ArrowUpCircle,
    FileText,
    ClipboardList,
    Wallet,
    RefreshCw
} from 'lucide-react';
import { useTranslation } from 'react-i18next';
import { Card, CardContent } from '@/components/ui/card';
import { Link } from 'react-router-dom';

/**
 * Staff Dashboard
 * 
 * Simplified dashboard for Staff role.
 * Shows only quick actions - NO financial metrics.
 */
const StaffDashboard = () => {
    const dispatch = useDispatch();
    const { t } = useTranslation();
    const user = getProfile();

    useEffect(() => {
        dispatch(setPageTitle(t('dashboard.staff_title', 'Dashboard Staff')));
        dispatch(setTitle(t('dashboard.staff_title', 'Dashboard Staff')));
        dispatch(setBreadcrumbTitle([t('dashboard.breadcrumb.dashboard', 'Dashboard')]));
    }, [dispatch, t]);

    return (
        <div className="space-y-8 animate-fade-in pb-10">
            {/* Welcome Header */}
            <div className="bg-gradient-to-r from-primary to-indigo-600 rounded-2xl p-8 text-white shadow-lg">
                <h1 className="text-3xl font-bold tracking-tight">
                    Selamat Datang, {user?.displayName || 'Staff'}! 👋
                </h1>
                <p className="text-white/80 mt-2 text-lg">
                    Pilih menu di bawah untuk memulai pekerjaan Anda hari ini
                </p>
            </div>

            {/* Main Quick Actions Grid */}
            <div className="grid grid-cols-2 lg:grid-cols-4 gap-6">
                {/* Input Penerimaan */}
                <Link
                    to="/daftar-penerimaan/create"
                    className="group"
                >
                    <Card className="h-full bg-white hover:shadow-xl transition-all duration-300 border-0 shadow-md hover:border-emerald-200 hover:-translate-y-1">
                        <CardContent className="p-6 flex flex-col items-center text-center">
                            <div className="p-4 rounded-2xl bg-emerald-100 text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                                <ArrowDownCircle className="h-10 w-10" />
                            </div>
                            <h3 className="mt-4 font-bold text-gray-800 text-lg">
                                Input Penerimaan
                            </h3>
                            <p className="mt-1 text-sm text-gray-500">
                                Catat penerimaan baru
                            </p>
                        </CardContent>
                    </Card>
                </Link>

                {/* Input Pengeluaran */}
                <Link
                    to="/daftar-pengeluaran/create"
                    className="group"
                >
                    <Card className="h-full bg-white hover:shadow-xl transition-all duration-300 border-0 shadow-md hover:border-rose-200 hover:-translate-y-1">
                        <CardContent className="p-6 flex flex-col items-center text-center">
                            <div className="p-4 rounded-2xl bg-rose-100 text-rose-600 group-hover:bg-rose-600 group-hover:text-white transition-colors">
                                <ArrowUpCircle className="h-10 w-10" />
                            </div>
                            <h3 className="mt-4 font-bold text-gray-800 text-lg">
                                Input Pengeluaran
                            </h3>
                            <p className="mt-1 text-sm text-gray-500">
                                Catat pengeluaran baru
                            </p>
                        </CardContent>
                    </Card>
                </Link>

                {/* Daftar Penerimaan */}
                <Link
                    to="/daftar-penerimaan"
                    className="group"
                >
                    <Card className="h-full bg-white hover:shadow-xl transition-all duration-300 border-0 shadow-md hover:border-blue-200 hover:-translate-y-1">
                        <CardContent className="p-6 flex flex-col items-center text-center">
                            <div className="p-4 rounded-2xl bg-blue-100 text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                <FileText className="h-10 w-10" />
                            </div>
                            <h3 className="mt-4 font-bold text-gray-800 text-lg">
                                Daftar Penerimaan
                            </h3>
                            <p className="mt-1 text-sm text-gray-500">
                                Lihat riwayat penerimaan
                            </p>
                        </CardContent>
                    </Card>
                </Link>

                {/* Daftar Pengeluaran */}
                <Link
                    to="/daftar-pengeluaran"
                    className="group"
                >
                    <Card className="h-full bg-white hover:shadow-xl transition-all duration-300 border-0 shadow-md hover:border-amber-200 hover:-translate-y-1">
                        <CardContent className="p-6 flex flex-col items-center text-center">
                            <div className="p-4 rounded-2xl bg-amber-100 text-amber-600 group-hover:bg-amber-600 group-hover:text-white transition-colors">
                                <ClipboardList className="h-10 w-10" />
                            </div>
                            <h3 className="mt-4 font-bold text-gray-800 text-lg">
                                Daftar Pengeluaran
                            </h3>
                            <p className="mt-1 text-sm text-gray-500">
                                Lihat riwayat pengeluaran
                            </p>
                        </CardContent>
                    </Card>
                </Link>
            </div>

            {/* Secondary Actions */}
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                {/* Buku Kas */}
                <Link to="/daftar-buku-kas" className="group">
                    <Card className="bg-white hover:shadow-lg transition-all duration-300 border-0 shadow-md hover:-translate-y-1">
                        <CardContent className="p-5 flex items-center gap-4">
                            <div className="p-3 rounded-xl bg-violet-100 text-violet-600 group-hover:bg-violet-600 group-hover:text-white transition-colors">
                                <Wallet className="h-6 w-6" />
                            </div>
                            <div>
                                <h3 className="font-bold text-gray-800">Buku Kas</h3>
                                <p className="text-sm text-gray-500">Lihat daftar buku kas</p>
                            </div>
                        </CardContent>
                    </Card>
                </Link>

                {/* Transfer */}
                <Link to="/daftar-transfer" className="group">
                    <Card className="bg-white hover:shadow-lg transition-all duration-300 border-0 shadow-md hover:-translate-y-1">
                        <CardContent className="p-5 flex items-center gap-4">
                            <div className="p-3 rounded-xl bg-cyan-100 text-cyan-600 group-hover:bg-cyan-600 group-hover:text-white transition-colors">
                                <RefreshCw className="h-6 w-6" />
                            </div>
                            <div>
                                <h3 className="font-bold text-gray-800">Daftar Transfer</h3>
                                <p className="text-sm text-gray-500">Lihat daftar transfer antar kas</p>
                            </div>
                        </CardContent>
                    </Card>
                </Link>
            </div>
        </div>
    );
};

export default StaffDashboard;
