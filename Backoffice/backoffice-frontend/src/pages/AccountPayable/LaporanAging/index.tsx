import { useEffect, useState } from 'react';
import { useTranslation } from 'react-i18next';
import { useDispatch, useSelector } from 'react-redux';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '@/store/themeConfigSlice';
import { DataTable } from 'mantine-datatable';
import Flatpickr from 'react-flatpickr';
import 'flatpickr/dist/flatpickr.css';
import SelectSearch from 'react-select';
import {
    useGetAccountPayableAgingReportQuery,
    useExportAccountPayableAgingMutation,
} from '@/store/api/accountPayable/accountPayableApiSlice';
import { useGetInvoiceExchangeSupplierOptionQuery } from '@/store/api/invoiceExchange/invoiceExchangeApiSlice';
import { FormatNumberRounded } from '@/utils/formatNumber';
import { FormatDateShort } from '@/utils/formatDate';
import { toastMessage } from '@/utils/toastUtils';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import IconCalendar from '@/components/Icon/IconCalendar';
import {
    BarChart3,
    Download,
    Filter,
    Search,
} from 'lucide-react';

interface AgingData {
    batchNumber?: string | null;
    supplierName: string;
    poNumber?: string | null;
    invoiceNumber?: string | null;
    dueDate?: string | null;
    current: number;
    days1To30: number;
    days31To60: number;
    days61To90: number;
    over90Days: number;
    total: number;
}

const LaporanAgingAP = () => {
    const dispatch = useDispatch();
    const isRtl = useSelector((state: any) => state.themeConfig.rtlClass) === 'rtl';

    const { t } = useTranslation();

    useEffect(() => {
        dispatch(setPageTitle(t('account_payable.laporan_aging.title')));
        dispatch(setTitle(t('account_payable.laporan_aging.page_title')));
        dispatch(setBreadcrumbTitle(['Dashboard', t('menu.account_payable'), t('account_payable.laporan_aging.title')]));
    }, [dispatch, t]);

    // Filter states
    const [selectedSupplier, setSelectedSupplier] = useState<any>(null);
    const [searchSupplier, setSearchSupplier] = useState<string>('');
    const [supplierType, setSupplierType] = useState<string>('');
    const [dueStatus, setDueStatus] = useState<string>('');
    const [asOfDate, setAsOfDate] = useState<Date>(new Date());
    const [keyword, setKeyword] = useState<string>('');
    const [debouncedKeyword, setDebouncedKeyword] = useState<string>('');

    useEffect(() => {
        const handler = setTimeout(() => setDebouncedKeyword(keyword.trim()), 400);
        return () => clearTimeout(handler);
    }, [keyword]);



    // API calls
    const { data: supplierList, isLoading: isLoadingSupplier } = useGetInvoiceExchangeSupplierOptionQuery(
        { keyword: searchSupplier || '', page: 1, pageSize: 20 }, { skip: false }
    );

    const [exportAging, { isLoading: isExporting }] = useExportAccountPayableAgingMutation();

    const formatDateForAPI = (date: Date): string => {
        return new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate())).toISOString();
    };

    const { data: agingData, isLoading, refetch } = useGetAccountPayableAgingReportQuery({
        supplierName: selectedSupplier?.label || '',
        supplierType: supplierType,
        asOfDate: formatDateForAPI(asOfDate),
        dueStatus: dueStatus,
        keyword: debouncedKeyword,
    });

    const getSupplierOptions = () => {
        if (!supplierList) return [];
        let items: any[] = Array.isArray(supplierList) ? supplierList :
            supplierList.data?.data || supplierList.data || [];
        return items.map((s: any) => ({
            label: s.label || s.supplierName || '',
            value: s.value || s.label || '',
        })).filter((i: any) => i.label);
    };

    const supplierOptions = [{ label: t('account_payable.laporan_aging.all_suppliers'), value: '' }, ...getSupplierOptions()];

    useEffect(() => {
        setTimeout(() => refetch(), 0);
    }, [asOfDate, selectedSupplier, supplierType, dueStatus, debouncedKeyword]);

    const handleExport = async () => {
        try {
            const blob = await exportAging({
                supplierName: selectedSupplier?.label || '',
                supplierType: supplierType,
                asOfDate: formatDateForAPI(asOfDate),
                dueStatus: dueStatus,
                keyword: debouncedKeyword,
                type: 'xlsx',
            }).unwrap();

            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', `aging_report_${new Date().toISOString().split('T')[0]}.xlsx`);
            document.body.appendChild(link);
            link.click();
            link.remove();
            window.URL.revokeObjectURL(url);
        } catch (error: any) {
            toastMessage(error?.data?.message || 'Gagal export data', 'error');
        }
    };

    const records: AgingData[] = agingData?.data || agingData || [];

    // Calculate totals
    const totals = records.reduce((acc, r) => ({
        current: acc.current + (r.current || 0),
        days1To30: acc.days1To30 + (r.days1To30 || 0),
        days31To60: acc.days31To60 + (r.days31To60 || 0),
        days61To90: acc.days61To90 + (r.days61To90 || 0),
        over90Days: acc.over90Days + (r.over90Days || 0),
        total: acc.total + (r.total || 0),
    }), { current: 0, days1To30: 0, days31To60: 0, days61To90: 0, over90Days: 0, total: 0 });

    const selectStyles = {
        control: (base: any) => ({
            ...base,
            minHeight: '40px',
            borderColor: '#e2e8f0',
            '&:hover': { borderColor: '#cbd5e1' },
        }),
        menuPortal: (base: any) => ({ ...base, zIndex: 9999 }),
    };

    return (
        <div className="space-y-6">
            <Card>
                <CardHeader className="border-b border-gray-100 pb-4">
                    <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        {/* Left side - Title */}
                        <div className="flex items-center gap-3">
                            <div className="p-2 rounded-lg bg-primary/10">
                                <BarChart3 className="h-5 w-5 text-primary" />
                            </div>
                            <div>
                                <CardTitle className="text-lg">{t('account_payable.laporan_aging.page_title')}</CardTitle>
                                <p className="text-sm text-gray-500 mt-0.5">
                                    {t('account_payable.laporan_aging.subtitle')}
                                </p>
                            </div>
                        </div>

                        {/* Right side - Filters */}
                        <div className="flex flex-col md:flex-row items-end md:items-center flex-wrap gap-3 ml-auto w-full md:w-auto">
                            {/* Keyword Filter */}
                            <div className="relative w-full md:w-[220px]">
                                <Search className="h-4 w-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" />
                                <Input
                                    type="text"
                                    className="pl-9 h-10"
                                    placeholder={t('account_payable.laporan_aging.keyword_placeholder')}
                                    value={keyword}
                                    onChange={(e) => setKeyword(e.target.value)}
                                />
                            </div>

                            {/* Supplier Type Filter */}
                            <div className="w-[150px]">
                                <select
                                    className="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                                    value={supplierType}
                                    onChange={(e) => setSupplierType(e.target.value)}
                                >
                                    <option value="">Semua Tipe</option>
                                    <option value="Medis">Medis</option>
                                    <option value="Umum">Umum</option>
                                    <option value="Dapur">Dapur</option>
                                </select>
                            </div>

                            {/* Status Jatuh Tempo Filter */}
                            <div className="w-[180px]">
                                <select
                                    className="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                                    value={dueStatus}
                                    onChange={(e) => setDueStatus(e.target.value)}
                                >
                                    <option value="">Semua Status</option>
                                    <option value="overdue">Sudah Jatuh Tempo</option>
                                    <option value="not_overdue">Belum Jatuh Tempo</option>
                                </select>
                            </div>

                            {/* Supplier Filter */}
                            <div className="w-[220px]">
                                <SelectSearch
                                    placeholder={t('account_payable.laporan_aging.select_supplier')}
                                    options={supplierOptions}
                                    isLoading={isLoadingSupplier}
                                    onInputChange={(v) => setSearchSupplier(v)}
                                    onChange={(opt: any) => setSelectedSupplier(opt)}
                                    value={selectedSupplier}
                                    isClearable
                                    styles={selectStyles}
                                    menuPortalTarget={document.body}
                                />
                            </div>

                            {/* Date Filter */}
                            <div className="relative w-[180px]">
                                <Flatpickr
                                    options={{ dateFormat: 'd/m/Y', position: isRtl ? 'auto right' : 'auto left' }}
                                    className="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ltr:pl-9 rtl:pr-9"
                                    value={asOfDate}
                                    onChange={(dates: Date[]) => dates[0] && setAsOfDate(dates[0])}
                                />
                                <div className="absolute inset-y-0 ltr:left-3 rtl:right-3 flex items-center pointer-events-none text-gray-400">
                                    <IconCalendar className="w-4 h-4" />
                                </div>
                            </div>


                            <Button
                                variant="outline"
                                className="gap-2"
                                onClick={handleExport}
                                disabled={isExporting || records.length === 0}
                            >
                                <Download className="h-4 w-4" />
                                <span className="hidden sm:inline">{isExporting ? t('account_payable.laporan_aging.exporting') : t('account_payable.laporan_aging.export')}</span>
                            </Button>
                        </div>
                    </div>
                </CardHeader>
            </Card>

            {/* Summary Cards */}
            <div className="grid grid-cols-2 md:grid-cols-6 gap-4">
                <Card className="bg-success/5 border-success/20">
                    <CardContent className="pt-4 pb-4">
                        <div className="text-xs text-success font-medium mb-1">{t('account_payable.laporan_aging.periods.current')}</div>
                        <div className="text-lg font-bold text-success">{FormatNumberRounded(totals.current)}</div>
                    </CardContent>
                </Card>
                <Card className="bg-warning/5 border-warning/20">
                    <CardContent className="pt-4 pb-4">
                        <div className="text-xs text-warning font-medium mb-1">{t('account_payable.laporan_aging.periods.days_1_30')}</div>
                        <div className="text-lg font-bold text-warning">{FormatNumberRounded(totals.days1To30)}</div>
                    </CardContent>
                </Card>
                <Card className="bg-orange-50 border-orange-200">
                    <CardContent className="pt-4 pb-4">
                        <div className="text-xs text-orange-600 font-medium mb-1">{t('account_payable.laporan_aging.periods.days_31_60')}</div>
                        <div className="text-lg font-bold text-orange-600">{FormatNumberRounded(totals.days31To60)}</div>
                    </CardContent>
                </Card>
                <Card className="bg-danger/5 border-danger/20">
                    <CardContent className="pt-4 pb-4">
                        <div className="text-xs text-danger font-medium mb-1">{t('account_payable.laporan_aging.periods.days_61_90')}</div>
                        <div className="text-lg font-bold text-danger">{FormatNumberRounded(totals.days61To90)}</div>
                    </CardContent>
                </Card>
                <Card className="bg-danger/10 border-danger/30">
                    <CardContent className="pt-4 pb-4">
                        <div className="text-xs text-danger font-medium mb-1">{t('account_payable.laporan_aging.periods.days_over_90')}</div>
                        <div className="text-lg font-bold text-danger">{FormatNumberRounded(totals.over90Days)}</div>
                    </CardContent>
                </Card>
                <Card className="bg-primary/5 border-primary/20">
                    <CardContent className="pt-4 pb-4">
                        <div className="text-xs text-primary font-medium mb-1">{t('account_payable.laporan_aging.total')}</div>
                        <div className="text-lg font-bold text-primary">{FormatNumberRounded(totals.total)}</div>
                    </CardContent>
                </Card>
            </div>

            {/* Data Card */}
            <Card>
                <CardContent className="pt-6">
                    <div className="datatables z-0 rounded-lg border border-gray-100 overflow-hidden">
                        <DataTable
                            highlightOnHover
                            className="whitespace-nowrap table-hover"
                            records={records}
                            columns={[
                                {
                                    accessor: 'batchNumber',
                                    title: t('account_payable.laporan_aging.table.batch_number'),
                                    width: 160,
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (r: AgingData) => <span className="font-mono text-gray-700">{r.batchNumber || '-'}</span>
                                },
                                {
                                    accessor: 'supplierName',
                                    title: t('account_payable.laporan_aging.supplier'),
                                    width: 260,
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    cellsClassName: 'whitespace-normal break-words align-top',
                                    render: (r: AgingData) => <span className="font-medium text-gray-700 whitespace-normal break-words">{r.supplierName || '-'}</span>
                                },
                                {
                                    accessor: 'poInvoice',
                                    title: t('account_payable.laporan_aging.table.po_invoice'),
                                    width: 220,
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (r: AgingData) => (
                                        <div className="flex flex-col">
                                            <span className="font-mono text-gray-700">{r.poNumber || '-'}</span>
                                            <span className="font-mono text-xs text-gray-500">{r.invoiceNumber || '-'}</span>
                                        </div>
                                    )
                                },
                                {
                                    accessor: 'dueDate',
                                    title: t('account_payable.laporan_aging.table.due_date'),
                                    width: 140,
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (r: AgingData) => <span className="text-gray-700">{r.dueDate ? FormatDateShort(r.dueDate) : '-'}</span>
                                },
                                {
                                    accessor: 'current',
                                    title: t('account_payable.laporan_aging.periods.current'),
                                    textAlignment: 'right',
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50 text-right',
                                    cellsStyle: { textAlign: 'right' },
                                    render: (r: AgingData) => <span className="font-mono text-success font-medium text-right block">{FormatNumberRounded(r.current)}</span>
                                },
                                {
                                    accessor: 'days1To30',
                                    title: t('account_payable.laporan_aging.periods.days_1_30'),
                                    textAlignment: 'right',
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50 text-right',
                                    cellsStyle: { textAlign: 'right' },
                                    render: (r: AgingData) => <span className="font-mono text-warning font-medium text-right block">{FormatNumberRounded(r.days1To30)}</span>
                                },
                                {
                                    accessor: 'days31To60',
                                    title: t('account_payable.laporan_aging.periods.days_31_60'),
                                    textAlignment: 'right',
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50 text-right',
                                    cellsStyle: { textAlign: 'right' },
                                    render: (r: AgingData) => <span className="font-mono text-orange-600 font-medium text-right block">{FormatNumberRounded(r.days31To60)}</span>
                                },
                                {
                                    accessor: 'days61To90',
                                    title: t('account_payable.laporan_aging.periods.days_61_90'),
                                    textAlignment: 'right',
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50 text-right',
                                    cellsStyle: { textAlign: 'right' },
                                    render: (r: AgingData) => <span className="font-mono text-danger font-medium text-right block">{FormatNumberRounded(r.days61To90)}</span>
                                },
                                {
                                    accessor: 'over90Days',
                                    title: t('account_payable.laporan_aging.periods.days_over_90'),
                                    textAlignment: 'right',
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50 text-right',
                                    cellsStyle: { textAlign: 'right' },
                                    render: (r: AgingData) => <span className="font-mono text-danger font-bold text-right block">{FormatNumberRounded(r.over90Days)}</span>
                                },
                                {
                                    accessor: 'total',
                                    title: t('account_payable.laporan_aging.total'),
                                    textAlignment: 'right',
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50 text-right',
                                    cellsStyle: { textAlign: 'right' },
                                    render: (r: AgingData) => <span className="font-mono font-bold text-gray-900 text-right block">{FormatNumberRounded(r.total)}</span>
                                },
                            ]}
                            minHeight={200}
                            noRecordsText={t('account_payable.laporan_aging.no_data')}
                            fetching={isLoading}
                            loaderVariant="dots"
                            loaderColor="#4361ee"
                            styles={{
                                root: {
                                    border: 'none',
                                    borderRadius: '0.75rem',
                                    overflow: 'hidden',
                                },
                                header: {
                                    backgroundColor: '#4361ee',
                                    color: '#ffffff',
                                    fontWeight: 'bold',
                                    textTransform: 'uppercase',
                                    letterSpacing: '0.05em',
                                },
                            }}
                        />
                    </div>

                    {/* Totals Row */}
                    {records.length > 0 && (
                        <div className="bg-gray-50 border border-gray-200 rounded-lg mt-4 p-4 grid grid-cols-10 gap-2 font-bold text-sm">
                            <div className="col-span-4 text-gray-700">{t('account_payable.laporan_aging.total')}</div>
                            <div className="text-right font-mono text-success">{FormatNumberRounded(totals.current)}</div>
                            <div className="text-right font-mono text-warning">{FormatNumberRounded(totals.days1To30)}</div>
                            <div className="text-right font-mono text-orange-600">{FormatNumberRounded(totals.days31To60)}</div>
                            <div className="text-right font-mono text-danger">{FormatNumberRounded(totals.days61To90)}</div>
                            <div className="text-right font-mono text-danger">{FormatNumberRounded(totals.over90Days)}</div>
                            <div className="text-right font-mono text-primary">{FormatNumberRounded(totals.total)}</div>
                        </div>
                    )}
                </CardContent>
            </Card>
        </div >
    );
};

export default LaporanAgingAP;
