import { useEffect, useState } from 'react';
import { useTranslation } from 'react-i18next';
import { useDispatch } from 'react-redux';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '@/store/themeConfigSlice';
import { DataTable } from 'mantine-datatable';
import SelectSearch from 'react-select';
import {
    useGetAccountReceivableAgingReportQuery,
    useGetAccountReceivableInsuranceOptionsQuery,
    useExportAccountReceivableAgingMutation,
} from '@/store/api/accountReceivable/accountReceivableApiSlice';
import { FormatNumber } from '@/utils/formatNumber';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import {
    Clock,
    FileSpreadsheet,
    Calendar
} from 'lucide-react';

interface AgingData {
    voucherDate?: string;
    voucherNumber: string;
    payerName: string;
    dueDate?: string;
    current: number;
    days1To30: number;
    days31To60: number;
    days61To90: number;
    over90Days: number;
    total: number;
}

const LaporanAgingAR = () => {
    const dispatch = useDispatch();
    const { t } = useTranslation();

    useEffect(() => {
        dispatch(setPageTitle(t('account_receivable.laporan_aging.title')));
        dispatch(setTitle(t('account_receivable.laporan_aging.page_title')));
        dispatch(setBreadcrumbTitle(['Dashboard', t('menu.account_receivable'), t('account_receivable.laporan_aging.title')]));
    }, [dispatch, t]);

    // Filter states
    const [selectedPayer, setSelectedPayer] = useState<any>(null);
    const [selectedPayerType, setSelectedPayerType] = useState<any>(null);
    const [payerSearch, setPayerSearch] = useState('');

    const payerTypeOptions = [
        { value: '', label: 'Semua Payer Type' },
        { value: 'bpjs', label: 'BPJS' },
        { value: 'non bpjs', label: 'Non BPJS' },
    ];

    // API calls
    const { data: payerOptions } = useGetAccountReceivableInsuranceOptionsQuery(payerSearch);

    const { data: agingData, isLoading } = useGetAccountReceivableAgingReportQuery({
        payerId: selectedPayer?.value || '',
        payerType: selectedPayerType?.value || '',
    });

    const [exportData, { isLoading: isExporting }] = useExportAccountReceivableAgingMutation();

    const insuranceOptions = [
        { value: '', label: t('account_receivable.laporan_aging.all_insurance') },
        ...(payerOptions?.data || payerOptions || []).map((p: any) => ({ value: p.value, label: p.label })),
    ];

    const handleExport = async () => {
        try {
            const result = await exportData({
                payerId: selectedPayer?.value || '',
                payerType: selectedPayerType?.value || '',
                type: 'xlsx',
            }).unwrap();

            // Create blob and download
            const blob = new Blob([result], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `aging-piutang-${new Date().toISOString().split('T')[0]}.xlsx`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        } catch (error) {
            console.error('Export failed:', error);
        }
    };

    const records: AgingData[] = agingData?.data?.data || agingData?.data || [];

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
            {/* Filter Card */}
            <Card>
                <CardHeader className="border-b border-gray-100 pb-4">
                    <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div className="flex items-center gap-3">
                            <div className="p-2 rounded-lg bg-primary/10">
                                <Clock className="h-5 w-5 text-primary" />
                            </div>
                            <div>
                                <CardTitle className="text-lg">{t('account_receivable.laporan_aging.title')}</CardTitle>
                                <p className="text-sm text-gray-500 mt-0.5">
                                    {t('account_receivable.laporan_aging.subtitle')}
                                </p>
                            </div>
                        </div>

                        <div className="flex flex-col md:flex-row items-end md:items-center gap-3 ml-auto w-full md:w-auto">
                            <div className="w-[300px]">
                                <SelectSearch
                                    placeholder={t('account_receivable.laporan_aging.select_insurance')}
                                    options={insuranceOptions}
                                    onChange={(opt: any) => setSelectedPayer(opt)}
                                    onInputChange={(val) => setPayerSearch(val)}
                                    value={selectedPayer}
                                    isClearable
                                    styles={selectStyles}
                                    menuPortalTarget={document.body}
                                />
                            </div>

                            <div className="w-[180px]">
                                <SelectSearch
                                    placeholder="Payer Type"
                                    options={payerTypeOptions}
                                    onChange={(opt: any) => setSelectedPayerType(opt)}
                                    value={selectedPayerType}
                                    isClearable
                                    styles={selectStyles}
                                    menuPortalTarget={document.body}
                                />
                            </div>

                            <Button
                                variant="outline"
                                className="gap-2"
                                onClick={handleExport}
                                disabled={isExporting}
                            >
                                <FileSpreadsheet className="h-4 w-4" />
                                <span className="hidden sm:inline">{isExporting ? t('account_receivable.laporan_aging.exporting') : t('account_receivable.laporan_aging.export')}</span>
                            </Button>
                        </div>
                    </div>
                </CardHeader>
            </Card>

            {/* Summary Cards */}
            <div className="grid grid-cols-2 md:grid-cols-6 gap-4">
                <Card className="bg-success/5 border-success/20">
                    <CardContent className="pt-4 pb-4">
                        <div className="text-xs text-success font-medium mb-1">{t('account_receivable.laporan_aging.periods.current')}</div>
                        <div className="text-lg font-bold text-success">{FormatNumber(totals.current)}</div>
                    </CardContent>
                </Card>
                <Card className="bg-warning/5 border-warning/20">
                    <CardContent className="pt-4 pb-4">
                        <div className="text-xs text-warning font-medium mb-1">{t('account_receivable.laporan_aging.periods.days_1_30')}</div>
                        <div className="text-lg font-bold text-warning">{FormatNumber(totals.days1To30)}</div>
                    </CardContent>
                </Card>
                <Card className="bg-orange-50 border-orange-200">
                    <CardContent className="pt-4 pb-4">
                        <div className="text-xs text-orange-600 font-medium mb-1">{t('account_receivable.laporan_aging.periods.days_31_60')}</div>
                        <div className="text-lg font-bold text-orange-600">{FormatNumber(totals.days31To60)}</div>
                    </CardContent>
                </Card>
                <Card className="bg-danger/5 border-danger/20">
                    <CardContent className="pt-4 pb-4">
                        <div className="text-xs text-danger font-medium mb-1">{t('account_receivable.laporan_aging.periods.days_61_90')}</div>
                        <div className="text-lg font-bold text-danger">{FormatNumber(totals.days61To90)}</div>
                    </CardContent>
                </Card>
                <Card className="bg-danger/10 border-danger/30">
                    <CardContent className="pt-4 pb-4">
                        <div className="text-xs text-danger font-medium mb-1">{t('account_receivable.laporan_aging.periods.days_over_90')}</div>
                        <div className="text-lg font-bold text-danger">{FormatNumber(totals.over90Days)}</div>
                    </CardContent>
                </Card>
                <Card className="bg-primary/5 border-primary/20">
                    <CardContent className="pt-4 pb-4">
                        <div className="text-xs text-primary font-medium mb-1">{t('account_receivable.laporan_aging.total')}</div>
                        <div className="text-lg font-bold text-primary">{FormatNumber(totals.total)}</div>
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
                                    accessor: 'voucherDate',
                                    title: t('account_receivable.laporan_aging.table.voucher_date'),
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (r: AgingData) => r.voucherDate ? new Date(r.voucherDate).toLocaleDateString('id-ID') : '-'
                                },
                                {
                                    accessor: 'voucherNumber',
                                    title: t('account_receivable.laporan_aging.table.voucher_no'),
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (r: AgingData) => <span className="font-mono font-medium text-primary">{r.voucherNumber}</span>
                                },
                                {
                                    accessor: 'payerName',
                                    title: t('account_receivable.laporan_aging.table.insurance'),
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (r: AgingData) => <span className="text-gray-600">{r.payerName || '-'}</span>
                                },
                                {
                                    accessor: 'dueDate',
                                    title: t('account_receivable.laporan_aging.table.due_date'),
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (r: AgingData) => r.dueDate ? new Date(r.dueDate).toLocaleDateString('id-ID') : '-'
                                },
                                {
                                    accessor: 'current',
                                    title: t('account_receivable.laporan_aging.table.current'),
                                    textAlignment: 'right' as const,
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50 text-right',
                                    render: (r: AgingData) => <div className="font-mono text-success text-right">{FormatNumber(r.current)}</div>
                                },
                                {
                                    accessor: 'days1To30',
                                    title: t('account_receivable.laporan_aging.table.days_1_30'),
                                    textAlignment: 'right' as const,
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50 text-right',
                                    render: (r: AgingData) => <div className="font-mono text-warning text-right">{FormatNumber(r.days1To30)}</div>
                                },
                                {
                                    accessor: 'days31To60',
                                    title: t('account_receivable.laporan_aging.table.days_31_60'),
                                    textAlignment: 'right' as const,
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50 text-right',
                                    render: (r: AgingData) => <div className="font-mono text-orange-600 text-right">{FormatNumber(r.days31To60)}</div>
                                },
                                {
                                    accessor: 'days61To90',
                                    title: t('account_receivable.laporan_aging.table.days_61_90'),
                                    textAlignment: 'right' as const,
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50 text-right',
                                    render: (r: AgingData) => <div className="font-mono text-danger text-right">{FormatNumber(r.days61To90)}</div>
                                },
                                {
                                    accessor: 'over90Days',
                                    title: t('account_receivable.laporan_aging.table.days_over_90'),
                                    textAlignment: 'right' as const,
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50 text-right',
                                    render: (r: AgingData) => <div className="font-mono text-danger font-bold text-right">{FormatNumber(r.over90Days)}</div>
                                },
                                {
                                    accessor: 'total',
                                    title: t('account_receivable.laporan_aging.table.total'),
                                    textAlignment: 'right' as const,
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50 text-right',
                                    render: (r: AgingData) => <div className="font-mono font-bold text-gray-900 text-right">{FormatNumber(r.total)}</div>
                                },
                            ]}
                            minHeight={200}
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
                </CardContent>
            </Card>
        </div>
    );
};

export default LaporanAgingAR;
