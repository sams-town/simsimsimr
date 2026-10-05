import { useGetJournalSheetQuery, useExportBalanceSheetMutation } from '@/store/api/journal/journalApiSlice';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '@/store/themeConfigSlice';
import { formatCurrency } from '@/utils/currency';
import { filterTree, FlatReportRow, flattenReportData } from '@/utils/flattenData';
import 'flatpickr/dist/flatpickr.css';
import { DataTable } from 'mantine-datatable';
import { useEffect, useMemo, useState } from 'react';
import Flatpickr from 'react-flatpickr';
import { useDispatch, useSelector } from 'react-redux';
import 'tippy.js/dist/tippy.css';

// Icons & UI
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { CalendarDays, Download, Scale, TrendingUp, TrendingDown, Landmark } from 'lucide-react';
import IconCalendar from '@/components/Icon/IconCalendar';
import Tippy from '@tippyjs/react';
import { useTranslation } from 'react-i18next';

const Index = () => {
  const dispatch = useDispatch();
  const { t } = useTranslation();

  useEffect(() => {
    dispatch(setPageTitle(t('balance_sheet_page.title')));
    dispatch(setTitle(t('balance_sheet_page.title')));
    dispatch(setBreadcrumbTitle([t('balance_sheet_page.breadcrumb.dashboard'), t('balance_sheet_page.breadcrumb.report'), t('balance_sheet_page.breadcrumb.title')]));
  }, [dispatch, t]);

  const isRtl = useSelector((state: any) => state.themeConfig.rtlClass) === 'rtl';

  // Dates
  const today = new Date();
  const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
  const toUtcMidnight = (date: Date) => new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));

  const [startDate, setStartDate] = useState<string>(toUtcMidnight(firstDayOfMonth).toISOString());
  const [endDate, setEndDate] = useState<string>(toUtcMidnight(today).toISOString());

  // API
  const {
    data: sheetList,
    refetch,
    isLoading,
    isFetching,
  } = useGetJournalSheetQuery({
    startDate: startDate ?? '',
    endDate: endDate ?? '',
  });

  // Export mutation
  const [exportBalanceSheet, { isLoading: isExporting }] = useExportBalanceSheetMutation();

  const handleExport = async () => {
    try {
      const blob = await exportBalanceSheet({
        startDate: startDate,
        endDate: endDate,
      }).unwrap();

      const url = window.URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.href = url;
      link.setAttribute('download', `BalanceSheet_${new Date().toISOString().split('T')[0]}.xlsx`);
      document.body.appendChild(link);
      link.click();
      link.remove();
      window.URL.revokeObjectURL(url);
    } catch (error) {
      console.error('Export failed:', error);
    }
  };

  // Summary Totals
  const summaryTotals = useMemo(() => {
    // API now returns { message, data: { summary: { totalAssets... }, data: [...] } }
    // RTK Query returns the payload in `sheetList` (which is the full response object if we tapped into it correctly, 
    // but typically `useQuery` returns the parsed JSON). 
    // Based on Controller: Ok(new { message, data }) -> { message: "...", data: { summary, data: [...] } }

    // Let's inspect `sheetList`.
    // If ApiSlice uses `transformResponse`, it might differ.
    // Assuming default behavior: sheetList = { message, data: { summary, data } }

    const summary = sheetList?.data?.summary;
    return {
      asset: summary?.totalAssets ?? 0,
      hutang: summary?.totalLiabilities ?? 0,
      ekuitas: summary?.totalEquity ?? 0,
    };
  }, [sheetList]);

  // Flatten Data
  const [records, setRecords] = useState<FlatReportRow[]>([]);
  useEffect(() => {
    const nestedData = sheetList?.data?.data || [];
    const flatData = flattenReportData(nestedData);
    setRecords(flatData);
  }, [sheetList]);

  const formatDateDisplay = (isoString: string) => {
    if (!isoString) return '';
    return new Date(isoString).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
  };

  return (
    <div className="space-y-6 animate-fade-in">
      {/* Header Card */}
      <Card className="bg-white border-0 shadow-sm rounded-2xl overflow-hidden relative">
        <div className="absolute top-0 right-0 w-32 h-32 bg-primary/5 rounded-bl-full -mr-10 -mt-10"></div>
        <CardContent className="p-6 relative z-10">
          <div className="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div className="flex items-center gap-5">
              <div className="p-4 rounded-2xl bg-indigo-50 text-indigo-600 shadow-sm">
                <Scale className="h-8 w-8" />
              </div>

              <div>
                <div className="flex items-center gap-2 text-gray-500 mb-1">
                  <Landmark className="h-4 w-4" />
                  <span className="text-xs font-bold uppercase tracking-wider">{t('balance_sheet_page.subtitle')}</span>
                </div>
                <h1 className="text-2xl font-bold text-gray-800">{t('balance_sheet_page.title')}</h1>
                <div className="flex items-center gap-2 mt-2 text-gray-500 text-sm">
                  <CalendarDays className="h-4 w-4 text-indigo-400" />
                  <span className="font-medium text-gray-700">{formatDateDisplay(startDate)}</span>
                  <span className="text-gray-300">|</span>
                  <span className="font-medium text-gray-700">{formatDateDisplay(endDate)}</span>
                </div>
              </div>
            </div>

            <div className="flex flex-col sm:flex-row items-center gap-4">
              <div className="relative w-full sm:w-auto min-w-[250px]">
                <Flatpickr
                  options={{
                    mode: 'range',
                    dateFormat: 'd/m/Y',
                    enableTime: false,
                    position: isRtl ? 'auto right' : 'auto left',
                    defaultDate: [toUtcMidnight(firstDayOfMonth), toUtcMidnight(today)],
                  }}
                  className="form-input pl-10 py-2.5 text-sm bg-gray-50 border-gray-200 text-gray-700 placeholder:text-gray-400 focus:border-indigo-400 focus:ring-0 rounded-xl"
                  onChange={(dates: Date[]) => {
                    if (dates.length === 2) {
                      setStartDate(toUtcMidnight(dates[0]).toISOString());
                      setEndDate(toUtcMidnight(dates[1]).toISOString());
                    }
                  }}
                  placeholder={t('balance_sheet_page.filter_period')}
                />
                <div className="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                  <IconCalendar className="w-4 h-4" />
                </div>
              </div>
              <Tippy content={t('balance_sheet_page.download_excel')}>
                <Button
                  variant="outline"
                  className="w-full sm:w-auto border-gray-200 text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-100 rounded-xl transition-all"
                  onClick={handleExport}
                  disabled={isExporting}
                >
                  <Download className="h-4 w-4 mr-2" />
                  {isExporting ? t('balance_sheet_page.exporting') : t('balance_sheet_page.export')}
                </Button>
              </Tippy>
            </div>
          </div>
        </CardContent>
      </Card>

      {/* Summary KPI Cards */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        <MetricCard
          title={t('balance_sheet_page.metrics.total_assets')}
          value={summaryTotals.asset}
          icon={Landmark}
          gradient="from-emerald-400 to-teal-400"
        />
        <MetricCard
          title={t('balance_sheet_page.metrics.total_liabilities')}
          value={summaryTotals.hutang}
          icon={TrendingDown}
          gradient="from-rose-400 to-orange-400"
        />
        <MetricCard
          title={t('balance_sheet_page.metrics.total_equity')}
          value={summaryTotals.ekuitas}
          icon={TrendingUp}
          gradient="from-indigo-400 to-blue-400"
        />
      </div>

      {/* Detail Table */}
      <Card className="shadow-lg border-0 rounded-2xl overflow-hidden">
        <CardHeader className="border-b border-gray-100 bg-white p-6">
          <div className="flex items-center gap-3">
            <div className="p-2.5 rounded-xl bg-indigo-50 text-indigo-500">
              <Scale className="h-5 w-5" />
            </div>
            <div>
              <CardTitle className="text-lg font-bold text-gray-800">{t('balance_sheet_page.table.title')}</CardTitle>
              <p className="text-sm text-gray-500 mt-0.5">{t('balance_sheet_page.table.subtitle')}</p>
            </div>
          </div>
        </CardHeader>
        <CardContent className="p-0">
          <div className="datatables">
            <div className="table-report-compact">
              <DataTable<FlatReportRow>
                noRecordsText={t('balance_sheet_page.table.no_data')}
                minHeight={200}
                highlightOnHover
                className={`${isRtl ? 'whitespace-nowrap table-hover' : 'whitespace-nowrap table-hover'}`}
                records={records}
                idAccessor="id"
                fetching={isLoading || isFetching}
                loaderVariant="dots"
                loaderColor="#4361ee"
                rowClassName={(record) => {
                  if (record.type === 'header' && record.level === 0) return '!bg-indigo-50 border-b border-indigo-100';
                  if (record.type === 'header' && record.level > 0) return '!bg-gray-50/70';
                  if (record.type === 'total' && record.level === 0) return '!bg-indigo-50/50 border-t-2 border-indigo-200';
                  if (record.type === 'total') return 'border-t border-gray-200';
                  return '';
                }}
                rowStyle={() => ({ fontSize: '12px', padding: 0 })}
                columns={[
                  {
                    accessor: 'name',
                    title: t('balance_sheet_page.table.description'),
                    titleClassName: 'font-bold uppercase text-xs text-gray-500 tracking-wider pl-4 py-3',
                    render: (record) => {
                      const indent = record.type === 'header' ? record.level * 1 : 0.4 + (record.level * 1);
                      const isTopHeader = record.type === 'header' && record.level === 0;
                      const isSubHeader = record.type === 'header' && record.level > 0;
                      const isTopTotal = record.type === 'total' && record.level === 0;
                      const isTotal = record.type === 'total';

                      let cls = 'text-gray-600 text-[13px]';
                      if (isTopHeader) cls = 'font-bold text-indigo-700 text-sm uppercase tracking-wide';
                      else if (isSubHeader) cls = 'font-semibold text-gray-700 text-[13px]';
                      else if (isTopTotal) cls = 'font-bold text-indigo-700 text-sm';
                      else if (isTotal) cls = 'font-semibold text-gray-700 text-[13px] italic';

                      return (
                        <div style={{ paddingLeft: `${indent}rem` }} className="py-0 leading-tight">
                          <span className={cls}>{record.name}</span>
                        </div>
                      );
                    },
                  },
                  {
                    accessor: 'value',
                    title: t('balance_sheet_page.table.amount'),
                    titleClassName: 'font-bold uppercase text-xs text-gray-500 tracking-wider text-right pr-4 py-3',
                    textAlignment: 'right',
                    width: 200,
                    render: (record) => {
                      if (!record.value) return null;
                      const isTopTotal = record.level === 0 && record.type === 'total';
                      const isTotal = record.type === 'total';

                      let cls = 'font-mono text-gray-600 text-[13px]';
                      if (isTopTotal) cls = 'font-mono font-extrabold text-indigo-600 text-sm';
                      else if (isTotal) cls = 'font-mono font-bold text-gray-800 text-[13px]';

                      return <div className={`${cls} pr-3 text-right`}>{record.value}</div>;
                    },
                  },
                ]}
                styles={{
                  root: { border: 'none' },
                  header: {
                    backgroundColor: '#f9fafb',
                    borderBottom: '2px solid #e5e7eb',
                    height: '32px',
                  },
                }}
              />
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  );
};

// --- Components ---

interface MetricCardProps {
  title: string;
  value: number;
  icon: any;
  gradient: string;
}

const MetricCard = ({ title, value, icon: Icon, gradient }: MetricCardProps) => {
  return (
    <div className={`relative overflow-hidden rounded-2xl bg-gradient-to-br ${gradient} p-6 shadow-lg text-white transition-all duration-300 hover:scale-[1.02] hover:shadow-xl group`}>
      <div className="flex justify-between items-start mb-6">
        <div className="p-3 rounded-2xl bg-white/20 backdrop-blur-sm text-white shadow-sm">
          <Icon size={24} className="text-white" />
        </div>
      </div>

      <div className="relative z-10">
        <p className="text-xs font-bold text-white/90 uppercase tracking-wider mb-1">{title}</p>
        <h3 className="text-3xl font-bold text-white">{formatCurrency(value)}</h3>
      </div>

      {/* Decor */}
      <Icon
        size={120}
        className="absolute -right-6 -bottom-6 text-white opacity-[0.15] transform rotate-12 group-hover:scale-110 transition-transform duration-500"
      />
    </div>
  );
};

export default Index;
