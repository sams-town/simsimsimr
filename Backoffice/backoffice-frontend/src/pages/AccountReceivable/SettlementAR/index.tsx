import { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import { useDispatch, useSelector } from 'react-redux';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '@/store/themeConfigSlice';
import { DataTable } from 'mantine-datatable';
import Select from 'react-select';
import Flatpickr from 'react-flatpickr';
import 'flatpickr/dist/flatpickr.css';
import {
    useGetAccountReceivableVoucherListQuery,
    useGetAccountReceivableInsuranceOptionsQuery,
    useUnfinalizeAccountReceivableVoucherMutation,
} from '@/store/api/accountReceivable/accountReceivableApiSlice';
import { FormatDate } from '@/utils/formatDate';
import { toastMessage } from '@/utils/toastUtils';
import ConfirmationModal from '@/components/ConfirmationModal';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Wallet, DollarSign, Eye, Ban } from 'lucide-react';
import IconCalendar from '@/components/Icon/IconCalendar';
import { useRoleAccess } from '@/hooks/useRoleAccess';

interface VoucherSummary {
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

const SettlementAR = () => {
    const dispatch = useDispatch();
    const navigate = useNavigate();
    const isRtl = useSelector((state: any) => state.themeConfig.rtlClass) === 'rtl';
    const { t } = useTranslation();

    useEffect(() => {
        dispatch(setPageTitle(t('ar.penerimaan_piutang_page.title')));
        dispatch(setTitle(t('ar.penerimaan_piutang_page.title')));
        dispatch(setBreadcrumbTitle(['Dashboard', t('sidebar.piutang_usaha'), t('ar.penerimaan_piutang_page.title')]));
    }, [dispatch, t]);

    const [page, setPage] = useState(1);
    const PAGE_SIZE = 10;
    const [filters, setFilters] = useState({
        payer: null as any,
        payerType: null as any,
        status: { value: 'settlement', label: 'Semua Status' },
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
    const { data: listData, isLoading, refetch } = useGetAccountReceivableVoucherListQuery({
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

    const [unfinalizeVoucher, { isLoading: isCancelling }] = useUnfinalizeAccountReceivableVoucherMutation();
    // Batal voucher hanya Administrator & Manager, sama seperti di halaman detail.
    const { canCancelArVoucher } = useRoleAccess();
    const [cancelTarget, setCancelTarget] = useState<VoucherSummary | null>(null);
    const [showCancelModal, setShowCancelModal] = useState(false);

    const handleCancelVoucher = async () => {
        if (!cancelTarget?.voucherNumber) return;
        try {
            await unfinalizeVoucher({ voucherNumber: cancelTarget.voucherNumber }).unwrap();
            toastMessage('Voucher has been cancelled', 'success');
            setShowCancelModal(false);
            setCancelTarget(null);
            refetch();
        } catch (error: any) {
            toastMessage(error?.data?.message || 'Failed to cancel voucher', 'error');
        }
    };

    const payerOptions = [
        ...(insuranceOptions?.data || insuranceOptions || []).map((p: any) => ({ value: p.value, label: p.label })),
    ];

    const formatCurrency = (value: number) => {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
    };

    const selectStyles = {
        control: (base: any) => ({
            ...base,
            minHeight: '40px',
            borderColor: '#e2e8f0',
            '&:hover': { borderColor: '#cbd5e1' },
            minWidth: '200px',
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
                            <div className="p-2 rounded-lg bg-success/10">
                                <Wallet className="h-5 w-5 text-success" />
                            </div>
                            <div>
                                <CardTitle className="text-lg">{t('ar.penerimaan_piutang_page.title')}</CardTitle>
                                <p className="text-sm text-gray-500 mt-0.5">
                                    {t('ar.penerimaan_piutang_page.subtitle')}
                                </p>
                            </div>
                        </div>

                        {/* Right side - Filters */}
                        <div className="flex flex-col md:flex-row items-end md:items-center gap-3 ml-auto w-full md:w-auto" style={{ position: 'relative', zIndex: 10 }}>
                            {/* Status Filter */}
                            <div className="w-full md:w-[200px]">
                                <Select
                                    options={[
                                        { value: 'settlement', label: t('ar.penerimaan_piutang_page.filter.status_value.all') || '' },
                                        { value: 'final', label: t('ar.penerimaan_piutang_page.filter.status_value.final') || '' },
                                        { value: 'paid', label: t('ar.penerimaan_piutang_page.filter.status_value.paid') || '' }
                                    ]}
                                    value={filters.status}
                                    onChange={(val: any) => {
                                        setPage(1);
                                        setFilters(prev => ({ ...prev, status: val || { value: 'settlement', label: t('ar.penerimaan_piutang_page.filter.status_value.all') || '' } }));
                                    }}
                                    placeholder={t('ar.penerimaan_piutang_page.filter.status')}
                                    className="text-sm"
                                    menuPortalTarget={document.body}
                                    styles={selectStyles}
                                />
                            </div>

                            {/* Payer Filter */}
                            <div className="w-full md:w-[200px]">
                                <Select
                                    options={payerOptions}
                                    value={filters.payer}
                                    onInputChange={(val) => setPayerSearch(val)}
                                    onChange={(val: any) => {
                                        setPage(1);
                                        setFilters(prev => ({ ...prev, payer: val }));
                                    }}
                                    isClearable
                                    isLoading={isLoadingInsurance}
                                    placeholder={t('ar.penerimaan_piutang_page.filter.payer')}
                                    className="text-sm"
                                    menuPortalTarget={document.body}
                                    styles={selectStyles}
                                />
                            </div>

                            {/* Payer Type Filter */}
                            <div className="w-full md:w-[180px]">
                                <Select
                                    options={payerTypeOptions}
                                    value={filters.payerType}
                                    onChange={(val: any) => {
                                        setPage(1);
                                        setFilters(prev => ({ ...prev, payerType: val }));
                                    }}
                                    isClearable
                                    placeholder="Payer Type"
                                    className="text-sm"
                                    menuPortalTarget={document.body}
                                    styles={selectStyles}
                                />
                            </div>

                            {/* Date Range Filter */}
                            <div className="relative w-full md:w-[200px]">
                                <Flatpickr
                                    value={[filters.dateFrom, filters.dateTo].filter(Boolean) as Date[]}
                                    options={{ mode: 'range', dateFormat: 'd/m/Y', enableTime: false }}
                                    className="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 ltr:pl-9 rtl:pr-9"
                                    onChange={(dates) => {
                                        setPage(1);
                                        if (dates.length === 2) {
                                            setFilters(prev => ({ ...prev, dateFrom: dates[0], dateTo: dates[1] }));
                                        } else if (dates.length === 0) {
                                            setFilters(prev => ({ ...prev, dateFrom: null, dateTo: null }));
                                        }
                                    }}
                                    placeholder={t('ar.penerimaan_piutang_page.filter.period')}
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
                                    title: t('ar.penerimaan_piutang_page.table.payer_code'),
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (row: VoucherSummary) => <span className="font-mono">{row.payerId}</span>
                                },
                                {
                                    accessor: 'payerName',
                                    title: t('ar.penerimaan_piutang_page.table.insurance_name'),
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50'
                                },
                                {
                                    accessor: 'voucherNumber',
                                    title: t('ar.penerimaan_piutang_page.table.voucher_no'),
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (row: VoucherSummary) => (
                                        <span className="font-semibold text-primary">{row.voucherNumber || '-'}</span>
                                    )
                                },
                                {
                                    accessor: 'voucherDate',
                                    title: t('ar.penerimaan_piutang_page.table.voucher_date'),
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (row: VoucherSummary) => FormatDate(row.voucherDate)
                                },
                                {
                                    accessor: 'itemCount',
                                    title: t('ar.penerimaan_piutang_page.table.item_count'),
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    textAlignment: 'center',
                                    render: (row: VoucherSummary) => (
                                        <span className="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs font-medium">
                                            {row.itemCount} item
                                        </span>
                                    )
                                },
                                {
                                    accessor: 'totalAmount',
                                    title: t('ar.penerimaan_piutang_page.table.total_amount'),
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50 text-right',
                                    textAlignment: 'right',
                                    render: (row: VoucherSummary) => <div className="font-mono font-bold text-gray-900 text-right">{formatCurrency(row.totalAmount)}</div>
                                },
                                {
                                    accessor: 'status',
                                    title: t('ar.penerimaan_piutang_page.table.status'),
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (row: VoucherSummary) => (
                                        <span className={`px-2 py-1 rounded-full text-xs font-medium ${row.status?.toLowerCase() === 'paid' ? 'bg-success/10 text-success' : 'bg-warning/10 text-warning'
                                            }`}>
                                            {row.status}
                                        </span>
                                    )
                                },
                                {
                                    accessor: 'action',
                                    title: t('ar.penerimaan_piutang_page.table.action'),
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50 text-center',
                                    textAlignment: 'center',
                                    render: (row: VoucherSummary) => (
                                        <div className="flex justify-center gap-1">
                                            {row.status?.toLowerCase() === 'paid' ? (
                                                <Button
                                                    size="sm"
                                                    variant="outline"
                                                    className="h-8 gap-1"
                                                    onClick={() => navigate(`/account-receivable/settlement/${btoa(row.voucherNumber || '')}`)}
                                                >
                                                    <Eye className="w-3.5 h-3.5" /> {t('ar.penerimaan_piutang_page.table.detail')}
                                                </Button>
                                            ) : (
                                                <>
                                                    <Button
                                                        size="sm"
                                                        className="bg-success hover:bg-success/90 h-8 gap-1"
                                                        onClick={() => navigate(`/account-receivable/settlement/${btoa(row.voucherNumber || '')}`)}
                                                    >
                                                        <DollarSign className="w-3.5 h-3.5" /> {t('ar.penerimaan_piutang_page.table.pay')}
                                                    </Button>
                                                    {canCancelArVoucher && (
                                                        <Button
                                                            size="sm"
                                                            variant="destructive"
                                                            className="h-8 w-8 p-0"
                                                            onClick={() => {
                                                                setCancelTarget(row);
                                                                setShowCancelModal(true);
                                                            }}
                                                        >
                                                            <Ban className="w-3.5 h-3.5" />
                                                        </Button>
                                                    )}
                                                </>
                                            )}
                                        </div>
                                    )
                                },
                            ]}
                            totalRecords={total}
                            recordsPerPage={PAGE_SIZE}
                            page={page}
                            onPageChange={setPage}
                            fetching={isLoading}
                            minHeight={200}
                            noRecordsText={t('common.no_records')}
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

            <ConfirmationModal
                isOpen={showCancelModal}
                onClose={() => {
                    if (!isCancelling) {
                        setShowCancelModal(false);
                        setCancelTarget(null);
                    }
                }}
                onConfirm={handleCancelVoucher}
                title="Cancel Voucher"
                message={`Cancel voucher ${cancelTarget?.voucherNumber || ''}? All items will return to "Posting" status. Ensure no items have been paid.`}
                confirmText="Yes, Cancel"
                cancelText="No"
                loading={isCancelling}
                variant="danger"
            />
        </div>
    );
};

export default SettlementAR;
