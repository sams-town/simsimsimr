import { useEffect } from 'react';
import { useDispatch } from 'react-redux';
import { useTranslation } from 'react-i18next';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '@/store/themeConfigSlice';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { useGetPettyCashDashboardQuery } from '@/store/api/pettyCash/pettyCashApiSlice';
import { FormatNumber } from '@/utils/formatNumber';
import { Wallet, Building2, FileText, TrendingUp, TrendingDown, AlertCircle, RefreshCw } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Link } from 'react-router-dom';

interface UnitBalance {
    unitId: number;
    unitCode: string;
    unitName: string;
    currentBalance: number;
    maxLimitPerPeriod: number;
    usagePercentage: number;
}

const PettyCashDashboard = () => {
    const dispatch = useDispatch();
    const { t } = useTranslation();

    useEffect(() => {
        dispatch(setPageTitle(t('petty_cash.dashboard.title', 'Petty Cash Dashboard')));
        dispatch(setTitle(t('petty_cash.dashboard.title', 'Petty Cash Dashboard')));
        dispatch(setBreadcrumbTitle(['Dashboard', t('sidebar.petty_cash', 'Petty Cash'), t('petty_cash.dashboard.title', 'Dashboard')]));
    }, [dispatch, t]);

    const { data: dashboardData, isLoading, refetch } = useGetPettyCashDashboardQuery({});

    const data = dashboardData?.data || {};

    const stats = [
        {
            title: t('petty_cash.dashboard.total_balance', 'Total Balance'),
            value: FormatNumber(data.totalBalance || 0),
            icon: Wallet,
            color: 'bg-blue-500',
            textColor: 'text-blue-600',
            bgColor: 'bg-blue-50',
        },
        {
            title: t('petty_cash.dashboard.unit_count', 'Active Units'),
            value: data.unitCount || 0,
            icon: Building2,
            color: 'bg-green-500',
            textColor: 'text-green-600',
            bgColor: 'bg-green-50',
        },
        {
            title: t('petty_cash.dashboard.pending_requests', 'Pending Requests'),
            value: data.pendingRequests || 0,
            icon: FileText,
            color: 'bg-yellow-500',
            textColor: 'text-yellow-600',
            bgColor: 'bg-yellow-50',
            link: '/petty-cash/request',
        },
        {
            title: t('petty_cash.dashboard.pending_topups', 'Pending Top-Ups'),
            value: data.pendingTopUps || 0,
            icon: AlertCircle,
            color: 'bg-purple-500',
            textColor: 'text-purple-600',
            bgColor: 'bg-purple-50',
            link: '/petty-cash/topup',
        },
    ];

    const monthlyStats = [
        {
            title: t('petty_cash.dashboard.monthly_settlements', 'Pengeluaran Bulan Ini'),
            value: FormatNumber(data.monthlySettlements || 0),
            icon: TrendingDown,
            color: 'text-red-500',
        },
        {
            title: t('petty_cash.dashboard.monthly_topups', 'Top-Up Bulan Ini'),
            value: FormatNumber(data.monthlyTopUps || 0),
            icon: TrendingUp,
            color: 'text-green-500',
        },
    ];

    return (
        <div className="space-y-6">
            {/* Header */}
            <div className="flex items-center justify-between">
                <div className="flex items-center gap-3">
                    <div className="p-2 rounded-lg bg-primary/10">
                        <Wallet className="h-6 w-6 text-primary" />
                    </div>
                    <div>
                        <h1 className="text-xl font-semibold">{t('petty_cash.dashboard.title', 'Petty Cash Dashboard')}</h1>
                        <p className="text-sm text-gray-500">{t('petty_cash.dashboard.subtitle', 'Manage your petty cash operations')}</p>
                    </div>
                </div>
                <Button variant="outline" onClick={() => refetch()} disabled={isLoading}>
                    <RefreshCw className={`h-4 w-4 mr-2 ${isLoading ? 'animate-spin' : ''}`} />
                    Refresh
                </Button>
            </div>

            {/* Summary Stats */}
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                {stats.map((stat, index) => (
                    <Card key={index} className="hover:shadow-md transition-shadow">
                        <CardContent className="p-6">
                            <div className="flex items-center justify-between">
                                <div>
                                    <p className="text-sm text-gray-500 mb-1">{stat.title}</p>
                                    <p className={`text-2xl font-bold ${stat.textColor}`}>
                                        {stat.value}
                                    </p>
                                </div>
                                <div className={`p-3 rounded-full ${stat.bgColor}`}>
                                    <stat.icon className={`h-6 w-6 ${stat.textColor}`} />
                                </div>
                            </div>
                            {stat.link && (
                                <Link to={stat.link} className="text-sm text-primary mt-2 inline-block hover:underline">
                                    View Details →
                                </Link>
                            )}
                        </CardContent>
                    </Card>
                ))}
            </div>

            {/* Monthly Stats */}
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                {monthlyStats.map((stat, index) => (
                    <Card key={index}>
                        <CardContent className="p-6">
                            <div className="flex items-center gap-4">
                                <stat.icon className={`h-8 w-8 ${stat.color}`} />
                                <div>
                                    <p className="text-sm text-gray-500">{stat.title}</p>
                                    <p className="text-xl font-bold">{stat.value}</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                ))}
            </div>

            {/* Unit Balances */}
            <Card>
                <CardHeader className="border-b">
                    <CardTitle className="text-lg flex items-center gap-2">
                        <Building2 className="h-5 w-5" />
                        {t('petty_cash.dashboard.unit_balances', 'Saldo Per Unit')}
                    </CardTitle>
                </CardHeader>
                <CardContent className="p-0">
                    {isLoading ? (
                        <div className="p-8 text-center text-gray-500">Loading...</div>
                    ) : (data.unitBalances || []).length === 0 ? (
                        <div className="p-8 text-center text-gray-500">
                            <Building2 className="h-12 w-12 mx-auto mb-2 opacity-50" />
                            <p>{t('petty_cash.dashboard.no_units', 'Belum ada unit kas kecil')}</p>
                            <Link to="/petty-cash/unit" className="text-primary hover:underline mt-2 inline-block">
                                Tambah Unit Pertama →
                            </Link>
                        </div>
                    ) : (
                        <div className="overflow-x-auto">
                            <table className="w-full">
                                <thead className="bg-gray-50">
                                    <tr>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Unit
                                        </th>
                                        <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Saldo Saat Ini
                                        </th>
                                        <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Limit per Periode
                                        </th>
                                        <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Penggunaan
                                        </th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-200">
                                    {(data.unitBalances || []).map((unit: UnitBalance) => {
                                        const hasLimit = unit.maxLimitPerPeriod > 0;
                                        const lowBalance = hasLimit && unit.currentBalance < unit.maxLimitPerPeriod * 0.2;
                                        return (
                                            <tr key={unit.unitId} className="hover:bg-gray-50">
                                                <td className="px-6 py-4">
                                                    <div className="flex flex-col">
                                                        <span className="font-medium text-gray-900">{unit.unitName}</span>
                                                        <span className="text-sm text-gray-500">{unit.unitCode}</span>
                                                    </div>
                                                </td>
                                                <td className="px-6 py-4 text-right">
                                                    <span className={`font-mono font-medium ${unit.currentBalance <= 0 ? 'text-gray-400' : lowBalance ? 'text-red-600' : 'text-green-600'}`}>
                                                        Rp {FormatNumber(unit.currentBalance)}
                                                    </span>
                                                </td>
                                                <td className="px-6 py-4 text-right">
                                                    {hasLimit ? (
                                                        <span className="font-mono text-gray-600">Rp {FormatNumber(unit.maxLimitPerPeriod)}</span>
                                                    ) : (
                                                        <span className="text-sm text-gray-400 italic">Belum diatur</span>
                                                    )}
                                                </td>
                                                <td className="px-6 py-4">
                                                    {hasLimit ? (
                                                        <div className="flex items-center justify-center gap-2">
                                                            <div className="w-24 bg-gray-200 rounded-full h-2">
                                                                <div
                                                                    className={`h-2 rounded-full ${unit.usagePercentage > 80 ? 'bg-red-500' :
                                                                            unit.usagePercentage > 50 ? 'bg-yellow-500' : 'bg-green-500'
                                                                        }`}
                                                                    style={{ width: `${Math.min(unit.usagePercentage, 100)}%` }}
                                                                />
                                                            </div>
                                                            <span className="text-sm text-gray-600 w-12 text-right">
                                                                {unit.usagePercentage.toFixed(0)}%
                                                            </span>
                                                        </div>
                                                    ) : (
                                                        <div className="text-center text-sm text-gray-400 italic">Tanpa limit</div>
                                                    )}
                                                </td>
                                            </tr>
                                        );
                                    })}
                                </tbody>
                            </table>
                        </div>
                    )}
                </CardContent>
            </Card>

            {/* Quick Actions */}
            <Card>
                <CardHeader className="border-b">
                    <CardTitle className="text-lg">Quick Actions</CardTitle>
                </CardHeader>
                <CardContent className="p-6">
                    <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <Link to="/petty-cash/request/create">
                            <Button variant="outline" className="w-full h-20 flex flex-col gap-2">
                                <FileText className="h-6 w-6" />
                                <span>Permintaan Baru</span>
                            </Button>
                        </Link>
                        <Link to="/petty-cash/settlement/create">
                            <Button variant="outline" className="w-full h-20 flex flex-col gap-2">
                                <TrendingDown className="h-6 w-6" />
                                <span>Settlement</span>
                            </Button>
                        </Link>
                        <Link to="/petty-cash/topup/create">
                            <Button variant="outline" className="w-full h-20 flex flex-col gap-2">
                                <TrendingUp className="h-6 w-6" />
                                <span>Top Up</span>
                            </Button>
                        </Link>
                        <Link to="/petty-cash/ledger">
                            <Button variant="outline" className="w-full h-20 flex flex-col gap-2">
                                <Wallet className="h-6 w-6" />
                                <span>Buku Kas</span>
                            </Button>
                        </Link>
                    </div>
                </CardContent>
            </Card>
        </div>
    );
};

export default PettyCashDashboard;
