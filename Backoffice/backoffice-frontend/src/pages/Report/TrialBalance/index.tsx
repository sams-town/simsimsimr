import { useGetLedgerSummaryQuery, useExportLedgerSummaryMutation } from '@/store/api/journal/journalApiSlice';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '@/store/themeConfigSlice';
import { formatCurrency } from '@/utils/currency';
import { FormatNumber } from '@/utils/formatNumber';
import 'flatpickr/dist/flatpickr.css';
import { DataTable } from 'mantine-datatable';
import { useEffect, useMemo, useState } from 'react';
import Flatpickr from 'react-flatpickr';
import { useDispatch, useSelector } from 'react-redux';
import { useTranslation } from 'react-i18next';
import 'tippy.js/dist/tippy.css';

// Icons & UI
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { CalendarDays, Download, BookOpen, TrendingUp, TrendingDown, Coins, Landmark } from 'lucide-react';
import IconCalendar from '@/components/Icon/IconCalendar';
import Tippy from '@tippyjs/react';
import { toastMessage } from '@/utils/toastUtils';

interface LedgerSummaryData {
    coaCode: string;
    coaName: string;
    beginningBalance: number;
    debit: number;
    credit: number;
    endingBalance: number;
}

const TrialBalanceIndex = () => {
    const dispatch = useDispatch();
    const { t } = useTranslation();
    useEffect(() => {
        dispatch(setPageTitle(t('trial_balance_page.title')));
        dispatch(setTitle(t('trial_balance_page.title')));
        dispatch(setBreadcrumbTitle([t('menu.dashboard'), 'Report', t('trial_balance_page.title')]));
    }, [dispatch, t]);

    const isRtl = useSelector((state: any) => state.themeConfig.rtlClass) === 'rtl';

    // Dates
    const today = new Date();
    const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
    const toUtcMidnight = (date: Date) => new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));

    const [startDate, setStartDate] = useState<string>(toUtcMidnight(firstDayOfMonth).toISOString());
    const [endDate, setEndDate] = useState<string>(toUtcMidnight(today).toISOString());

    // Table state
    const [page, setPage] = useState(1);
    const PAGE_SIZES = [20, 50, 100, 200];
    const [pageSize, setPageSize] = useState(PAGE_SIZES[0]);

    // API
    const {
        data: summaryData,
        refetch,
        isLoading,
        isFetching,
    } = useGetLedgerSummaryQuery({
        startDate: startDate ?? '',
        endDate: endDate ?? '',
        page: -1,
        pageSize: -1,
    });

    const [exportLedgerSummary, { isLoading: isExporting }] = useExportLedgerSummaryMutation();

    const handleExport = async () => {
        try {
            const blob = await exportLedgerSummary({
                startDate: startDate,
                endDate: endDate,
            }).unwrap();

            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', `TrialBalance_${new Date().toISOString().split('T')[0]}.xlsx`);
            document.body.appendChild(link);
            link.click();
            link.remove();
            window.URL.revokeObjectURL(url);
        } catch (error) {
            toastMessage(t('trial_balance_page.toast.export_failed'), 'error');
        }
    };

    // Process data
    const records: LedgerSummaryData[] = useMemo(() => {
        const data = summaryData?.data?.data || summaryData?.data || [];
        return data;
    }, [summaryData]);

    // Paginate locally
    const paginatedRecords = useMemo(() => {
        if (page === -1 || pageSize === -1) return records;
        const start = (page - 1) * pageSize;
        return records.slice(start, start + pageSize);
    }, [records, page, pageSize]);

    // Summary Totals
    const totals = useMemo(() => {
        return records.reduce((acc, r) => ({
            beginningBalance: acc.beginningBalance + (r.beginningBalance || 0),
            debit: acc.debit + (r.debit || 0),
            credit: acc.credit + (r.credit || 0),
            endingBalance: acc.endingBalance + (r.endingBalance || 0),
        }), { beginningBalance: 0, debit: 0, credit: 0, endingBalance: 0 });
    }, [records]);

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
                                <BookOpen className="h-8 w-8" />
                            </div>

                            <div>
                                <div className="flex items-center gap-2 text-gray-500 mb-1">
                                    <Landmark className="h-4 w-4" />
                                    <span className="text-xs font-bold uppercase tracking-wider">{t('trial_balance_page.subtitle')}</span>
                                </div>
                                <h1 className="text-2xl font-bold text-gray-800">{t('trial_balance_page.title')}</h1>
                                <div className="flex items-center gap-2 mt-2 text-gray-500 text-sm">
                                    <CalendarDays className="h-4 w-4 text-indigo-400" />
                                    <span className="font-medium text-gray-700">{formatDateDisplay(startDate)}</span>
                                    <span className="text-gray-300">{t('trial_balance_page.date_separator')}</span>
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
                                            setPage(1);
                                        }
                                    }}
                                    placeholder={t('trial_balance_page.filter_period')}
                                />
                                <div className="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                    <IconCalendar className="w-4 h-4" />
                                </div>
                            </div>
                            <Tippy content={t('trial_balance_page.export')}>
                                <Button
                                    variant="outline"
                                    className="w-full sm:w-auto border-gray-200 text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-100 rounded-xl transition-all"
                                    onClick={handleExport}
                                    disabled={isExporting}
                                >
                                    <Download className="h-4 w-4 mr-2" />
                                    {isExporting ? t('trial_balance_page.exporting') : t('trial_balance_page.export')}
                                </Button>
                            </Tippy>
                        </div>
                    </div>
                </CardContent>
            </Card>

            {/* Summary KPI Cards */}
            <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
                <MetricCard
                    title={t('trial_balance_page.metrics.opening_balance')}
                    value={totals.beginningBalance}
                    icon={Coins}
                    gradient="from-slate-400 to-gray-400"
                />
                <MetricCard
                    title={t('trial_balance_page.metrics.total_debit')}
                    value={totals.debit}
                    icon={TrendingUp}
                    gradient="from-emerald-400 to-teal-400"
                />
                <MetricCard
                    title={t('trial_balance_page.metrics.total_credit')}
                    value={totals.credit}
                    icon={TrendingDown}
                    gradient="from-rose-400 to-orange-400"
                />
                <MetricCard
                    title={t('trial_balance_page.metrics.ending_balance')}
                    value={totals.endingBalance}
                    icon={BookOpen}
                    gradient="from-indigo-400 to-blue-400"
                />
            </div>

            {/* Detail Table */}
            <Card className="shadow-lg border-0 rounded-2xl overflow-hidden">
                <CardHeader className="border-b border-gray-100 bg-white p-6">
                    <div className="flex items-center justify-between">
                        <div className="flex items-center gap-3">
                            <div className="p-2.5 rounded-xl bg-indigo-50 text-indigo-500">
                                <BookOpen className="h-5 w-5" />
                            </div>
                        </div>
                        <div>
                            <CardTitle className="text-lg font-bold text-gray-800">{t('trial_balance_page.detail_title')}</CardTitle>
                            <p className="text-sm text-gray-500 mt-0.5">{t('trial_balance_page.records_found', { count: records.length })}</p>
                        </div>
                    </div>
                </CardHeader>
                <CardContent className="p-0">
                    <div className="datatables">
                        <DataTable<LedgerSummaryData>
                            noRecordsText={t('trial_balance_page.no_data')}
                            minHeight={200}
                            highlightOnHover
                            className="whitespace-nowrap table-hover"
                            records={paginatedRecords}
                            fetching={isLoading || isFetching}
                            loaderVariant="dots"
                            loaderColor="#4361ee"
                            totalRecords={records.length}
                            recordsPerPage={pageSize}
                            page={page}
                            onPageChange={setPage}
                            recordsPerPageOptions={PAGE_SIZES}
                            onRecordsPerPageChange={setPageSize}
                            paginationText={({ from, to, totalRecords }) => `${from} - ${to} ${t('trial_balance_page.pagination', { total: totalRecords })}`}
                            columns={[
                                {
                                    accessor: 'coaCode',
                                    title: t('trial_balance_page.table.account_code'),
                                    titleClassName: 'font-bold uppercase text-xs text-gray-500 tracking-wider pl-6 py-4',
                                    render: (record) => (
                                        <div className="font-mono text-primary font-medium pl-4">{record.coaCode}</div>
                                    ),
                                },
                                {
                                    accessor: 'coaName',
                                    title: t('trial_balance_page.table.account_name'),
                                    titleClassName: 'font-bold uppercase text-xs text-gray-500 tracking-wider',
                                    render: (record) => (
                                        <div className="text-gray-700 font-medium">{record.coaName}</div>
                                    ),
                                },
                                {
                                    accessor: 'beginningBalance',
                                    title: t('trial_balance_page.table.opening_balance'),
                                    titleClassName: 'font-bold uppercase text-xs text-gray-500 tracking-wider text-right pr-6',
                                    textAlignment: 'right',
                                    render: (record) => (
                                        <div className="font-mono text-gray-600 pr-4">{FormatNumber(record.beginningBalance)}</div>
                                    ),
                                },
                                {
                                    accessor: 'debit',
                                    title: t('trial_balance_page.table.debit'),
                                    titleClassName: 'font-bold uppercase text-xs text-gray-500 tracking-wider text-right pr-6',
                                    textAlignment: 'right',
                                    render: (record) => (
                                        <div className="font-mono text-emerald-600 font-medium pr-4">{FormatNumber(record.debit)}</div>
                                    ),
                                },
                                {
                                    accessor: 'credit',
                                    title: t('trial_balance_page.table.credit'),
                                    titleClassName: 'font-bold uppercase text-xs text-gray-500 tracking-wider text-right pr-6',
                                    textAlignment: 'right',
                                    render: (record) => (
                                        <div className="font-mono text-rose-600 font-medium pr-4">{FormatNumber(record.credit)}</div>
                                    ),
                                },
                                {
                                    accessor: 'endingBalance',
                                    title: t('trial_balance_page.table.ending_balance'),
                                    titleClassName: 'font-bold uppercase text-xs text-gray-500 tracking-wider text-right pr-6',
                                    textAlignment: 'right',
                                    render: (record) => (
                                        <div className="font-mono font-bold text-indigo-600 pr-4">{FormatNumber(record.endingBalance)}</div>
                                    ),
                                },
                            ]}
                            styles={{
                                root: {
                                    border: 'none',
                                },
                                header: {
                                    backgroundColor: '#f9fafb',
                                    borderBottom: '1px solid #f1f5f9',
                                    height: '50px',
                                },
                                pagination: {
                                    borderTop: '1px solid #f1f5f9',
                                    padding: '1rem',
                                },
                            }}
                        />
                    </div>

                    {/* Totals Footer */}
                    {records.length > 0 && (
                        <div className="bg-indigo-50 border-t border-indigo-100 p-4 grid grid-cols-6 gap-4 font-bold text-sm">
                            <div className="col-span-2 text-indigo-700 uppercase flex items-center pl-4">{t('trial_balance_page.grand_total')}</div>
                            <div className="text-right font-mono text-gray-700 pr-4">{FormatNumber(totals.beginningBalance)}</div>
                            <div className="text-right font-mono text-emerald-600 pr-4">{FormatNumber(totals.debit)}</div>
                            <div className="text-right font-mono text-rose-600 pr-4">{FormatNumber(totals.credit)}</div>
                            <div className="text-right font-mono text-indigo-700 pr-4">{FormatNumber(totals.endingBalance)}</div>
                        </div>
                    )}
                </CardContent>
            </Card>
        </div >
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
                <h3 className="text-2xl font-bold text-white">{formatCurrency(value)}</h3>
            </div>

            {/* Decor */}
            <Icon
                size={120}
                className="absolute -right-6 -bottom-6 text-white opacity-[0.15] transform rotate-12 group-hover:scale-110 transition-transform duration-500"
            />
        </div>
    );
};

export default TrialBalanceIndex;
