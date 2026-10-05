import { useEffect, useState, useMemo } from 'react';
import { useDispatch } from 'react-redux';
import { setPageTitle, setTitle, setBreadcrumbTitle } from '@/store/themeConfigSlice';
import { useGetDashboardFullQuery, DashboardMetrics, MetricData } from '@/store/api/dashboard/dashboardApiSlice';
import { FormatNumber } from '@/utils/formatNumber';
import { getProfile } from '@/utils/auth';
import Chart from 'react-apexcharts';
import {
  CalendarRange,
  Stethoscope,
  Pill,
  UserCheck,
  BedDouble,
  Siren,
  ScanLine,
  FlaskConical,
  Activity,
  TrendingUp,
  TrendingDown,
  DollarSign,
  Loader2
} from 'lucide-react';
import { useTranslation } from 'react-i18next';

const FinanceDashboard = () => {
  const dispatch = useDispatch();
  const { t } = useTranslation();

  // Check if user is Staff or Petty Cash role and redirect
  useEffect(() => {
    const user = getProfile();
    if (user?.role?.roleID === 4) {
      window.location.href = '/staff-dashboard';
      return;
    }
    if (user?.role?.roleID === 5) {
      window.location.href = '/petty-cash/dashboard';
      return;
    }
  }, []);

  useEffect(() => {
    dispatch(setPageTitle(t('dashboard.title')));
    dispatch(setTitle(t('dashboard.title')));
    dispatch(setBreadcrumbTitle([t('dashboard.breadcrumb.dashboard'), t('dashboard.breadcrumb.overview')]));
  }, [dispatch, t]);

  const today = new Date();
  const firstDay = new Date(today.getFullYear(), 0, 1);
  const [dateRange, setDateRange] = useState<[Date, Date]>([firstDay, today]);

  // Fetch real data from API
  const { data: dashboardData, isLoading, error } = useGetDashboardFullQuery();

  // Default empty metric
  const emptyMetric: MetricData = { today: 0, yesterday: 0, thisMonth: 0, lastMonth: 0 };

  // Extract metrics from API response
  const metrics = useMemo(() => {
    return dashboardData?.data?.metrics || {
      revenue: emptyMetric,
      outpatient: emptyMetric,
      inpatient: emptyMetric,
      igd: emptyMetric,
      polyclinic: emptyMetric,
      pharmacy: emptyMetric,
      laboratory: emptyMetric,
      radiology: emptyMetric,
      physiotherapy: emptyMetric,
    } as DashboardMetrics;
  }, [dashboardData]);

  const monthlyTrend = useMemo(() => {
    return dashboardData?.data?.monthlyTrend || {
      months: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
      revenue: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
      expense: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
    };
  }, [dashboardData]);

  const revenueComposition = useMemo(() => {
    return dashboardData?.data?.revenueComposition || {
      labels: ['Rawat Inap', 'Rawat Jalan', 'IGD', 'Farmasi', 'Lab', 'Radiologi', 'Lainnya'],
      series: [0, 0, 0, 0, 0, 0, 0],
    };
  }, [dashboardData]);

  const topRevenueAccounts = useMemo(() => {
    return dashboardData?.data?.topRevenueAccounts || [];
  }, [dashboardData]);

  // Payment methods from API (BPJS vs Non-BPJS)
  const paymentMethods = useMemo(() => {
    return dashboardData?.data?.paymentMethods || {
      labels: ['BPJS', 'Non-BPJS'],
      series: [50, 50],
    };
  }, [dashboardData]);

  if (isLoading) {
    return (
      <div className="flex items-center justify-center h-96">
        <Loader2 className="h-8 w-8 animate-spin text-primary" />
        <span className="ml-2 text-gray-600">{t('dashboard.loading')}</span>
      </div>
    );
  }

  return (
    <div className="space-y-6 animate-fade-in pb-10">
      {/* ================= HEADER ================= */}
      <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h1 className="text-3xl font-bold text-gray-800 tracking-tight">{t('dashboard.title')}</h1>
          <p className="text-gray-500 mt-1">{t('dashboard.subtitle')}</p>
        </div>

        <div className="flex items-center gap-3 px-4 py-2 rounded-full bg-white shadow-sm border border-gray-100/50 backdrop-blur-sm">
          <CalendarRange size={16} className="text-indigo-400" />
          <span className="text-sm font-semibold text-gray-700">
            {dateRange[0].toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })}
            <span className="mx-2 text-gray-400">{t('dashboard.date_range.to')}</span>
            {dateRange[1].toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })}
          </span>
        </div>
      </div>

      {/* ================= HERO METRICS (FULL BLOCKS) ================= */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {/* BIG REVENUE BLOCK */}
        <div className="lg:col-span-3">
          <LuxuryMetricCard
            title={t('dashboard.metrics.total_revenue')}
            data={metrics.revenue}
            icon={DollarSign}
            gradient="from-indigo-400 to-blue-400"
            variant="solid"
          />
        </div>

        {/* KEY DEPARTMENTS */}
        <LuxuryMetricCard
          title={t('dashboard.metrics.outpatient')}
          data={metrics.outpatient}
          icon={UserCheck}
          gradient="from-emerald-400 to-teal-400"
          variant="solid"
        />
        <LuxuryMetricCard
          title={t('dashboard.metrics.inpatient')}
          data={metrics.inpatient}
          icon={BedDouble}
          gradient="from-violet-400 to-purple-400"
          variant="solid"
        />
        <LuxuryMetricCard
          title={t('dashboard.metrics.igd')}
          data={metrics.igd}
          icon={Siren}
          gradient="from-rose-400 to-orange-400"
          variant="solid"
        />
      </div>

      {/* ================= SECONDARY METRICS (MODERN CARDS) ================= */}
      <div>
        <h3 className="text-lg font-bold text-gray-800 mb-3 flex items-center gap-2">
          <Activity className="text-gray-400" size={18} />
          {t('dashboard.metrics.department_performance')}
        </h3>
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-5">
          <LuxuryMetricCard
            title={t('dashboard.metrics.polyclinic')}
            data={metrics.polyclinic}
            icon={Stethoscope}
            gradient="from-pink-400 to-rose-400"
            variant="default"
          />
          <LuxuryMetricCard
            title={t('dashboard.metrics.pharmacy')}
            data={metrics.pharmacy}
            icon={Pill}
            gradient="from-cyan-400 to-blue-400"
            variant="default"
          />
          <LuxuryMetricCard
            title={t('dashboard.metrics.laboratory')}
            data={metrics.laboratory}
            icon={FlaskConical}
            gradient="from-amber-400 to-orange-400"
            variant="default"
          />
          <LuxuryMetricCard
            title={t('dashboard.metrics.radiology')}
            data={metrics.radiology}
            icon={ScanLine}
            gradient="from-slate-400 to-gray-500"
            variant="default"
          />
          <LuxuryMetricCard
            title={t('dashboard.metrics.physiotherapy')}
            data={metrics.physiotherapy}
            icon={Activity}
            gradient="from-lime-400 to-green-500"
            variant="default"
          />
        </div>
      </div>

      {/* ================= CHARTS SECTION: REVENUE INTELLIGENCE ================= */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* MONTHLY REVENUE LINE CHART */}
        <div className="lg:col-span-2 relative overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100 p-6">
          <div className="flex justify-between items-center mb-6">
            <div>
              <h3 className="text-lg font-bold text-gray-800">{t('dashboard.charts.monthly_revenue_trend')}</h3>
              <p className="text-sm text-gray-400">{t('dashboard.charts.income_vs_expense')}</p>
            </div>
            <div className="flex gap-2">
              <div className="flex items-center gap-1 text-xs font-medium text-gray-500">
                <span className="w-2 h-2 rounded-full bg-indigo-500"></span> {t('dashboard.charts.revenue')}
              </div>
              <div className="flex items-center gap-1 text-xs font-medium text-gray-500">
                <span className="w-2 h-2 rounded-full bg-rose-400"></span> {t('dashboard.charts.expense')}
              </div>
            </div>
          </div>
          <Chart
            options={{
              chart: { toolbar: { show: false }, zoom: { enabled: false } },
              stroke: { curve: 'smooth', width: 3 },
              colors: ['#6366f1', '#fb7185'], // Indigo-500, Rose-400
              fill: { type: 'gradient', gradient: { opacityFrom: 0.5, opacityTo: 0 } },
              xaxis: { categories: monthlyTrend.months, axisBorder: { show: false }, axisTicks: { show: false } },
              yaxis: { labels: { formatter: (val) => `${val}M` } },
              grid: { borderColor: '#f1f5f9' },
              legend: { show: false },
              tooltip: { theme: 'light', y: { formatter: (val) => `Rp ${val} Juta` } }
            }}
            series={[
              { name: t('dashboard.charts.revenue'), data: monthlyTrend.revenue },
              { name: t('dashboard.charts.expense'), data: monthlyTrend.expense }
            ]}
            type="area"
            height={320}
          />
        </div>

        {/* REVENUE COMPOSITION PIE CHART */}
        <div className="relative overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100 p-6">
          <h3 className="text-lg font-bold text-gray-800 mb-1">{t('dashboard.charts.revenue_sources')}</h3>
          <p className="text-sm text-gray-400 mb-6">{t('dashboard.charts.distribution_by_dept')}</p>

          <Chart
            options={{
              chart: { type: 'donut', fontFamily: 'inherit' },
              labels: revenueComposition.labels,
              colors: ['#6366f1', '#10b981', '#f43f5e', '#06b6d4', '#f59e0b', '#8b5cf6', '#94a3b8'],
              legend: { position: 'bottom', horizontalAlign: 'center', fontSize: '13px' },
              plotOptions: { pie: { donut: { size: '65%', labels: { show: true, value: { formatter: (val: string) => `${val}%` }, total: { show: true, showAlways: true, label: t('dashboard.charts.total'), color: '#64748b', fontSize: '14px', fontWeight: '600', formatter: () => '100%' } } } } },
              dataLabels: { enabled: false },
              tooltip: { y: { formatter: (val: number) => `${val}%` } },
              stroke: { width: 0 }
            }}
            series={revenueComposition.series}
            type="donut"
            height={350}
          />
        </div>
      </div>

      {/* ================= TABLE & ANALYTICS: SERVICE INTELLIGENCE ================= */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {/* TOP REVENUE ACCOUNTS TABLE */}
        <div className="lg:col-span-2 relative overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100 p-6">
          <div className="flex justify-between items-center mb-6">
            <div>
              <h3 className="text-lg font-bold text-gray-800">{t('dashboard.tables.top_revenue_accounts')}</h3>
              <p className="text-sm text-gray-400">{t('dashboard.tables.highest_revenue_coa')}</p>
            </div>
            <button className="text-sm font-medium text-indigo-600 hover:text-indigo-800 transition-colors">{t('dashboard.tables.view_all')}</button>
          </div>

          <div className="overflow-x-auto">
            <table className="w-full text-left border-collapse">
              <thead>
                <tr className="text-xs text-gray-500 border-b border-gray-100">
                  <th className="font-semibold py-3 px-4 uppercase tracking-wider">{t('dashboard.tables.coa_code')}</th>
                  <th className="font-semibold py-3 px-4 uppercase tracking-wider">{t('dashboard.tables.account_name')}</th>
                  <th className="font-semibold py-3 px-4 uppercase tracking-wider">{t('dashboard.tables.group')}</th>
                  <th className="font-semibold py-3 px-4 uppercase tracking-wider text-right">{t('dashboard.charts.revenue')}</th>
                </tr>
              </thead>
              <tbody className="text-sm text-gray-700">
                {topRevenueAccounts.map((account, idx) => (
                  <tr key={account.id} className="hover:bg-gray-50/50 transition-colors border-b border-gray-50 last:border-0">
                    <td className="py-3 px-4 font-mono text-xs text-indigo-600">{account.coaCode}</td>
                    <td className="py-3 px-4 font-medium">{account.accountName}</td>
                    <td className="py-3 px-4">
                      <span className={`px-2 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700`}>
                        {account.group}
                      </span>
                    </td>
                    <td className="py-3 px-4 text-right font-bold text-gray-800">{FormatNumber(account.revenue)}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>

        {/* PAYMENT METHODS PIE CHART */}
        <div className="relative overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100 p-6">
          <h3 className="text-lg font-bold text-gray-800 mb-1">{t('dashboard.charts.payment_methods')}</h3>
          <p className="text-sm text-gray-400 mb-6">{t('dashboard.charts.payer_distribution')}</p>

          <Chart
            options={{
              chart: { type: 'pie', fontFamily: 'inherit' },
              labels: paymentMethods.labels,
              colors: ['#22c55e', '#3b82f6', '#f97316', '#a8a29e'], // Green, Blue, Orange, Gray
              legend: { position: 'bottom', horizontalAlign: 'center', fontSize: '13px' },
              dataLabels: { enabled: true, dropShadow: { enabled: false } },
              stroke: { width: 0 },
              responsive: [{ breakpoint: 480, options: { chart: { width: 200 }, legend: { position: 'bottom' } } }]
            }}
            series={paymentMethods.series}
            type="pie"
            height={300}
          />
        </div>

      </div>
    </div>
  );
};

/* ================= COMPONENT ================= */

interface MetricCardProps {
  title: string;
  data: {
    today: number;
    yesterday: number;
    thisMonth: number;
    lastMonth: number;
  };
  icon: any;
  gradient: string;
  variant?: 'default' | 'solid';
}

const LuxuryMetricCard = ({ title, data, icon: Icon, gradient, variant = 'default' }: MetricCardProps) => {
  const isSolid = variant === 'solid';

  // Calculate Deltas
  const dayDelta = data.today - data.yesterday;
  const dayPercent = data.yesterday !== 0 ? (dayDelta / data.yesterday) * 100 : 0;

  const monthDelta = data.thisMonth - data.lastMonth;
  const monthPercent = data.lastMonth !== 0 ? (monthDelta / data.lastMonth) * 100 : 0;

  // Styles - COMPACT & PASTEL ADJUSTMENTS
  const containerClasses = isSolid
    ? `relative overflow-hidden rounded-2xl bg-gradient-to-br ${gradient} text-white shadow-lg shadow-gray-200`
    : `relative overflow-hidden rounded-2xl bg-white text-gray-800 shadow-md border border-gray-50 hover:shadow-lg transition-all duration-300`;

  const iconBgClass = isSolid
    ? `bg-white/20 text-white backdrop-blur-md`
    : `bg-gradient-to-br ${gradient} text-white shadow-sm`;

  const labelClass = isSolid ? `text-white/90` : `text-gray-500`;
  const valueClass = isSolid ? `text-white` : `text-gray-800`;
  const subValueClass = isSolid ? `text-white/80` : `text-gray-400`;
  const borderClass = isSolid ? `border-white/20` : `border-gray-100`;

  const getTrendBadge = (percent: number) => {
    const isUp = percent >= 0;
    if (isSolid) {
      return (
        <div className={`flex items-center text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-white/20 text-white backdrop-blur-md`}>
          {isUp ? <TrendingUp size={10} className="mr-1" /> : <TrendingDown size={10} className="mr-1" />}
          {Math.abs(percent).toFixed(1)}%
        </div>
      )
    }
    return (
      <div className={`flex items-center text-[10px] font-bold px-1.5 py-0.5 rounded-full ${isUp ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600'}`}>
        {isUp ? <TrendingUp size={10} className="mr-1" /> : <TrendingDown size={10} className="mr-1" />}
        {Math.abs(percent).toFixed(1)}%
      </div>
    )
  };

  return (
    <div className={`${containerClasses} group`}>
      {/* Watermark Icon - Reduced Opacity & Scale */}
      <Icon
        size={80}
        className={`absolute -right-4 -bottom-4 opacity-[0.08] transform rotate-12 group-hover:scale-105 transition-all duration-700 ${isSolid ? 'text-white' : 'text-gray-400'}`}
      />

      <div className="p-4 relative z-10 flex flex-col h-full justify-between">
        <div>
          <div className="flex justify-between items-start mb-3">
            <div className={`p-2 rounded-xl ${iconBgClass}`}>
              <Icon size={18} />
            </div>
            {getTrendBadge(dayPercent)}
          </div>
          <p className={`text-xs font-bold tracking-wide uppercase ${labelClass}`}>{title}</p>
        </div>

        <div className="mt-2">
          <div className={`pb-2 border-b ${borderClass}`}>
            <div className="flex items-end gap-2">
              <h3 className={`text-2xl font-bold ${valueClass}`}>{FormatNumber(data.today)}</h3>
              <span className={`text-[10px] mb-1.5 ${subValueClass}`}>/ Hari Ini</span>
            </div>
          </div>
          <div className="pt-2 flex justify-between items-center">
            <div>
              <p className={`text-[10px] ${isSolid ? 'text-white/80' : 'text-gray-400'}`}>Bulan Ini</p>
              <p className={`text-sm font-semibold ${valueClass}`}>{FormatNumber(data.thisMonth)}</p>
            </div>
            <div className={`text-[10px] font-medium text-right ${isSolid ? 'text-white/90' : (monthPercent >= 0 ? 'text-green-600' : 'text-red-600')}`}>
              {monthPercent >= 0 ? '+' : ''}{Math.abs(monthPercent).toFixed(1)}%
              <p className={`font-normal ${subValueClass}`}>vs Bulan Lalu</p>
            </div>
          </div>
        </div>
      </div>

      {/* Remove bottom bar for cleaner look in compact mode */}
      {/* {!isSolid && <div className={`h-1 w-full bg-gradient-to-r ${gradient}`}></div>} */}
    </div>
  );
};

export default FinanceDashboard;
