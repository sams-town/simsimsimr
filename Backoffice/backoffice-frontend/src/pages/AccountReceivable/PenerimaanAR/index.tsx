import { useEffect, useState } from 'react';
import { useDispatch } from 'react-redux';
import { useTranslation } from 'react-i18next';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '@/store/themeConfigSlice';
import { DataTable } from 'mantine-datatable';
import Select from 'react-select';
import Flatpickr from 'react-flatpickr';
import 'flatpickr/dist/flatpickr.css';
import {
    useGetAccountReceivableVoucherListQuery,
    useGetAccountReceivableInsuranceOptionsQuery,
} from '@/store/api/accountReceivable/accountReceivableApiSlice';
import { FormatNumber } from '@/utils/formatNumber';
import { useNavigate } from 'react-router-dom';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { CreditCard, Eye } from 'lucide-react';
import IconCalendar from '@/components/Icon/IconCalendar';

interface PayerVoucherSummary {
    payerId: string;
    payerName: string;
    voucherNumber?: string;
    voucherDate?: string;
    itemCount: number;
    totalAmount: number;
    status: string;
}

const formatDateForApi = (date: Date | null): string | null => {
    if (!date) return null;
    return new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate())).toISOString();
};

const PaymentVoucherAR = () => {
    const dispatch = useDispatch();
    const navigate = useNavigate();
    const { t } = useTranslation();

    useEffect(() => {
        dispatch(setPageTitle(t('ar.tagihan_piutang_page.title')));
        dispatch(setTitle(t('ar.tagihan_piutang_page.title')));
        dispatch(setBreadcrumbTitle(['Dashboard', t('sidebar.piutang_usaha'), t('ar.tagihan_piutang_page.title')]));
    }, [dispatch, t]);

    // Filter states
    const [page, setPage] = useState(1);
    const PAGE_SIZE = 10;
    const [filters, setFilters] = useState({
        payer: null as any,
        payerType: null as any,
        status: { value: 'posting', label: 'Posting' },
        dateFrom: null as Date | null,
        dateTo: null as Date | null,
    });

    const payerTypeOptions = [
        { value: '', label: 'Semua Payer Type' },
        { value: 'bpjs', label: 'BPJS' },
        { value: 'non bpjs', label: 'Non BPJS' },
    ];

    const [payerSearch, setPayerSearch] = useState('');

    // API
    const { data: insuranceOptions, isLoading: isLoadingInsurance } = useGetAccountReceivableInsuranceOptionsQuery(payerSearch);
    const { data: listData, isLoading } = useGetAccountReceivableVoucherListQuery({
        payerId: filters.payer?.value || '',
        payerType: filters.payerType?.value || '',
        status: filters.status?.value || '',
        startDate: formatDateForApi(filters.dateFrom),
        endDate: formatDateForApi(filters.dateTo),
        page: page,
        pageSize: PAGE_SIZE,
    });

    const records = listData?.data?.data || [];
    const total = listData?.data?.totalData || 0;

    const payerOptions = [
        ...(insuranceOptions?.data || insuranceOptions || []).map((p: any) => ({ value: p.value, label: p.label })),
    ];

    const formatCurrency = (value: number) => {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
    };

    const handleDetail = (payerId: string, voucherNumber?: string) => {
        const queryParams = new URLSearchParams();
        const currentStatus = filters.status?.value;

        if (currentStatus) {
            queryParams.append('status', currentStatus);
        }

        let identifier = payerId;
        // If status is Final or Paid, use Voucher Number (Base64 encoded) as identifier
        if ((currentStatus === 'paid' || currentStatus === 'final') && voucherNumber) {
            // Base64 encode URL safe
            identifier = btoa(voucherNumber).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
        }

        navigate(`/account-receivable/payment-voucher/${encodeURIComponent(identifier)}?${queryParams.toString()}`);
    };

    return (
        <div className="space-y-6">
            <Card>
                <CardHeader className="border-b border-gray-100 pb-4">
                    <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        {/* Left side - Title */}
                        <div className="flex items-center gap-3">
                            <div className="p-2 rounded-lg bg-primary/10">
                                <CreditCard className="h-5 w-5 text-primary" />
                            </div>
                            <div>
                                <CardTitle className="text-lg">{t('ar.tagihan_piutang_page.title')}</CardTitle>
                                <p className="text-sm text-gray-500 mt-0.5">
                                    {t('ar.tagihan_piutang_page.subtitle')}
                                </p>
                            </div>
                        </div>

                        {/* Right side - Filters */}
                        <div className="flex flex-col md:flex-row items-end md:items-center gap-3 ml-auto w-full md:w-auto" style={{ position: 'relative', zIndex: 10 }}>
                            {/* Status Filter */}
                            <div className="w-full md:w-[200px]">
                                <Select
                                    options={[
                                        { value: 'posting', label: t('ar.tagihan_piutang_page.filter.status_value.posting') || '' },
                                        { value: 'final', label: t('ar.tagihan_piutang_page.filter.status_value.final') || '' },
                                        { value: 'paid', label: t('ar.tagihan_piutang_page.filter.status_value.paid') || '' }
                                    ]}
                                    value={filters.status}
                                    onChange={(val) => setFilters({ ...filters, status: val || { value: 'posting', label: t('ar.tagihan_piutang_page.filter.status_value.posting') || '' } })}
                                    placeholder={t('ar.tagihan_piutang_page.filter.status')}
                                    className="text-sm"
                                    menuPortalTarget={document.body}
                                    styles={{ menuPortal: (base) => ({ ...base, zIndex: 9999 }) }}
                                />
                            </div>

                            {/* Payer Filter */}
                            <div className="w-full md:w-[200px]">
                                <Select
                                    options={payerOptions}
                                    value={filters.payer}
                                    onInputChange={(val) => setPayerSearch(val)}
                                    onChange={(val) => setFilters({ ...filters, payer: val })}
                                    isClearable
                                    isLoading={isLoadingInsurance}
                                    placeholder={t('ar.tagihan_piutang_page.filter.payer')}
                                    className="text-sm"
                                    menuPortalTarget={document.body}
                                    styles={{ menuPortal: (base) => ({ ...base, zIndex: 9999 }) }}
                                />
                            </div>

                            {/* Payer Type Filter */}
                            <div className="w-full md:w-[180px]">
                                <Select
                                    options={payerTypeOptions}
                                    value={filters.payerType}
                                    onChange={(val) => setFilters({ ...filters, payerType: val })}
                                    isClearable
                                    placeholder="Payer Type"
                                    className="text-sm"
                                    menuPortalTarget={document.body}
                                    styles={{ menuPortal: (base) => ({ ...base, zIndex: 9999 }) }}
                                />
                            </div>

                            {/* Date Range Filter */}
                            <div className="relative w-full md:w-[200px]">
                                <Flatpickr
                                    value={[filters.dateFrom, filters.dateTo].filter(Boolean) as Date[]}
                                    options={{ mode: 'range', dateFormat: 'd/m/Y', enableTime: false }}
                                    className="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 ltr:pl-9 rtl:pr-9"
                                    onChange={(dates) => {
                                        if (dates.length === 2) {
                                            setFilters({ ...filters, dateFrom: dates[0], dateTo: dates[1] });
                                        } else if (dates.length === 0) {
                                            setFilters({ ...filters, dateFrom: null, dateTo: null });
                                        }
                                    }}
                                    placeholder={t('ar.tagihan_piutang_page.filter.period')}
                                />
                                <div className="absolute inset-y-0 ltr:left-3 rtl:right-3 flex items-center pointer-events-none text-gray-400">
                                    <IconCalendar className="w-4 h-4" />
                                </div>
                            </div>
                        </div>
                    </div>
                </CardHeader>

                <CardContent className="pt-6">
                    {/* Data Table */}
                    <div className="datatables z-0 rounded-lg border border-gray-100 overflow-hidden">
                        <DataTable
                            highlightOnHover
                            className="whitespace-nowrap table-hover"
                            records={records}
                            columns={[
                                {
                                    accessor: 'payerId',
                                    title: t('ar.tagihan_piutang_page.table.payer_code'),
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (row: PayerVoucherSummary) => <span className="font-mono">{row.payerId}</span>
                                },
                                {
                                    accessor: 'payerName',
                                    title: t('ar.tagihan_piutang_page.table.insurance_name'),
                                    sortable: true,
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50'
                                },
                                {
                                    accessor: 'voucherNumber',
                                    title: t('ar.tagihan_piutang_page.table.voucher_number'),
                                    hidden: filters.status?.value !== 'final' && filters.status?.value !== 'paid',
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (row: PayerVoucherSummary) => <span className="font-mono">{row.voucherNumber || '-'}</span>
                                },
                                {
                                    accessor: 'itemCount',
                                    title: t('ar.tagihan_piutang_page.table.item_count'),
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    textAlignment: 'center',
                                    render: (row: PayerVoucherSummary) => (
                                        <span className="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs font-medium">
                                            {row.itemCount} item
                                        </span>
                                    )
                                },
                                {
                                    accessor: 'totalAmount',
                                    title: t('ar.tagihan_piutang_page.table.total_amount'),
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50 text-right',
                                    textAlignment: 'right',
                                    render: (row: PayerVoucherSummary) => <div className="font-mono font-bold text-gray-900 text-right">{formatCurrency(row.totalAmount)}</div>
                                },
                                {
                                    accessor: 'status',
                                    title: t('ar.tagihan_piutang_page.table.status'),
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (row: PayerVoucherSummary) => (
                                        <span className={`px-2 py-1 rounded-full text-xs font-medium ${row.voucherNumber ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'
                                            }`}>
                                            {row.status}
                                        </span>
                                    )
                                },
                                {
                                    accessor: 'action',
                                    title: t('ar.tagihan_piutang_page.table.action'),
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (row: PayerVoucherSummary) => (
                                        <Button variant="outline" size="sm" onClick={() => handleDetail(row.payerId, row.voucherNumber)} className="gap-1">
                                            <Eye className="h-4 w-4" />
                                            {t('ar.tagihan_piutang_page.table.detail')}
                                        </Button>
                                    ),
                                },
                            ]}
                            totalRecords={total}
                            recordsPerPage={PAGE_SIZE}
                            page={page}
                            onPageChange={setPage}
                            fetching={isLoading}
                            minHeight={200}
                            noRecordsText={t('ar.tagihan_piutang_page.table.no_data')}
                            loaderVariant="dots"
                            loaderColor="#4361ee"
                            styles={{
                                root: { border: 'none', borderRadius: '0.75rem', overflow: 'hidden' },
                                pagination: { borderTop: '1px solid #f1f5f9', margin: '1rem' },
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

export default PaymentVoucherAR;
