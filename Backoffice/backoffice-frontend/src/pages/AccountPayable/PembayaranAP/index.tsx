import { useEffect, useState } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { useNavigate } from 'react-router-dom';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '@/store/themeConfigSlice';
import { DataTable } from 'mantine-datatable';
import Flatpickr from 'react-flatpickr';
import 'flatpickr/dist/flatpickr.css';
import SelectSearch from 'react-select';
import { useTranslation } from 'react-i18next';
import {
    useGetAccountPayableListQuery,
    useExportAccountPayableMutation,
} from '@/store/api/accountPayable/accountPayableApiSlice';
import { useGetInvoiceExchangeSupplierOptionQuery } from '@/store/api/invoiceExchange/invoiceExchangeApiSlice';
import { FormatNumberRounded } from '@/utils/formatNumber';
import { FormatDate } from '@/utils/formatDate';
import { toastMessage } from '@/utils/toastUtils';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import IconCalendar from '@/components/Icon/IconCalendar';
import {
    CreditCard,
    Eye,
    Banknote,
    Search,
    CheckSquare,
    Download,
    Loader2,
    FileText,
} from 'lucide-react';

interface AccountPayableData {
    invoiceExchangeId: number;
    supplierName: string;
    poNumber: string;
    invoiceNumber: string;
    receiptDate: string;
    dueDate: string;
    totalTransaction: number;
    paidAmount: number;
    outstandingAmount: number;
    status: string;
    paymentStatus: string;
    agingDays: number;
    agingBucket: string;
    paymentRef?: string;
    paymentDate?: string;
    paymentCoaCode?: string;
    paymentCoaDesc?: string;
    // true = lunas lewat "Mark as Paid" di halaman Purchase Order (tanpa transaksi bank
    // & tanpa jurnal), bukan lewat menu Pembayaran ini.
    manualPaid?: boolean;
    manualPaidNote?: string | null;
    manualPaidAt?: string | null;
}

const PembayaranAP = () => {
    const dispatch = useDispatch();
    const navigate = useNavigate();
    const { t } = useTranslation();

    useEffect(() => {
        dispatch(setPageTitle(t('account_payable.pembayaran.title')));
        dispatch(setTitle(t('account_payable.pembayaran.title')));
        dispatch(setBreadcrumbTitle([t('menu.dashboard'), t('menu.account_payable'), t('account_payable.pembayaran.title')]));
    }, [dispatch, t]);

    const isRtl = useSelector((state: any) => state.themeConfig.rtlClass) === 'rtl';

    // Filter states
    const [selectedSupplier, setSelectedSupplier] = useState<any>(null);
    const [searchSupplier, setSearchSupplier] = useState<string>('');
    const [statusFilter, setStatusFilter] = useState<string>('Unpaid');
    const [keyword, setKeyword] = useState<string>('');
    const [supplierType, setSupplierType] = useState<string>('');
    const [startDate, setStartDate] = useState<Date | null>(null);
    const [endDate, setEndDate] = useState<Date | null>(null);

    // Table states
    const [page, setPage] = useState<number>(1);
    const PAGE_SIZES: number[] = [10, 20, 30, 50, 100];
    const [pageSize, setPageSize] = useState<number>(PAGE_SIZES[0]);

    // Selection states for batch payment
    const [selectedRecords, setSelectedRecords] = useState<AccountPayableData[]>([]);


    // API calls
    const { data: supplierList, isLoading: isLoadingSupplier } = useGetInvoiceExchangeSupplierOptionQuery(
        { keyword: searchSupplier || '', page: 1, pageSize: 20 }, { skip: false }
    );

    const [exportAP, { isLoading: isExporting }] = useExportAccountPayableMutation();

    // Format date as UTC ISO string (e.g. "2026-03-11T00:00:00.000Z")
    // Uses Date.UTC to preserve local date values and prevent timezone shift
    const formatDateForAPI = (date: Date | null): string => {
        if (!date) return '';
        return new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate())).toISOString();
    };

    // Only send dates when full range is selected (both start and end)
    const hasFullRange = startDate !== null && endDate !== null;

    const { data: apList, isLoading } = useGetAccountPayableListQuery({
        supplierName: selectedSupplier?.label || '',
        paymentStatus: statusFilter,
        supplierType: supplierType,
        keyword: keyword || '',
        startDate: hasFullRange ? formatDateForAPI(startDate) : '',
        endDate: hasFullRange ? formatDateForAPI(endDate) : '',
        page,
        pageSize,
        orderBy: 'duedate',
        orderType: 'asc',
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

    const supplierOptions = [{ label: t('account_payable.pembayaran.all_suppliers'), value: '' }, ...getSupplierOptions()];

    useEffect(() => {
        setPage(1);
        setSelectedRecords([]);
    }, [selectedSupplier, pageSize, statusFilter, keyword, startDate, endDate, supplierType]);

    const records: AccountPayableData[] = apList?.data?.data || apList?.data || [];
    const totalRecords = apList?.data?.totalData || records.length;

    const getAgingColor = (bucket: string) => {
        switch (bucket) {
            case 'Current': return 'bg-success/10 text-success';
            case '1-30': return 'bg-warning/10 text-warning';
            case '31-60': return 'bg-orange-100 text-orange-600';
            case '61-90': return 'bg-danger/10 text-danger';
            case '>90': return 'bg-danger/20 text-danger';
            default: return 'bg-gray-100 text-gray-500';
        }
    };

    const selectStyles = {
        control: (base: any) => ({
            ...base,
            minHeight: '40px',
            borderColor: '#e2e8f0',
            '&:hover': { borderColor: '#cbd5e1' },
        }),
        menuPortal: (base: any) => ({ ...base, zIndex: 9999 }),
    };

    const handleExport = async () => {
        try {
            const blob = await exportAP({
                supplierName: selectedSupplier?.label || '',
                paymentStatus: statusFilter,
                supplierType: supplierType,
                keyword: keyword || '',
                startDate: hasFullRange ? formatDateForAPI(startDate) : '',
                endDate: hasFullRange ? formatDateForAPI(endDate) : '',
                type: 'xlsx',
            }).unwrap();

            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', `pembayaran_ap_${new Date().toISOString().split('T')[0]}.xlsx`);
            document.body.appendChild(link);
            link.click();
            link.remove();
            window.URL.revokeObjectURL(url);
        } catch (error: any) {
            toastMessage(error?.data?.message || 'Gagal export data', 'error');
        }
    };

    // Invoice non-PO (input manual) punya rumus tagihan sendiri, jadi diarahkan ke
    // halaman pembayarannya sendiri. Jalur PO tetap ke halaman lama tanpa perubahan.
    const detailPathFor = (row: any) =>
        row?.poNumber
            ? `/account-payable/pembayaran/${row.invoiceExchangeId}`
            : `/account-payable/pembayaran/manual/${row.invoiceExchangeId}`;

    // Navigate to payment detail page
    const handlePayAction = (row: any) => {
        navigate(detailPathFor(row));
    };

    // Navigate to view payment details for paid invoices
    const handleViewDetail = (row: any) => {
        navigate(detailPathFor(row));
    };

    return (
        <div className="space-y-6">
            {/* Filter Card */}
            <Card>
                <CardHeader className="border-b border-gray-100 pb-4">
                    <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div className="flex items-center gap-3">
                            <div className="p-2 rounded-lg bg-primary/10">
                                <CreditCard className="h-5 w-5 text-primary" />
                            </div>
                            <div>
                                <CardTitle className="text-lg">{t('account_payable.pembayaran.title')}</CardTitle>
                                <p className="text-sm text-gray-500 mt-0.5">
                                    {t('account_payable.pembayaran.subtitle')}
                                </p>
                            </div>
                        </div>

                        <div className="flex flex-col md:flex-row items-end md:items-center gap-3 ml-auto w-full md:w-auto flex-wrap">
                            {/* Keyword Filter */}
                            <div className="relative w-[220px]">
                                <input
                                    type="text"
                                    className="flex h-10 w-full items-center rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 ltr:pl-9 rtl:pr-9"
                                    placeholder={t('account_payable.pembayaran.filter.keyword')}
                                    value={keyword}
                                    onChange={(e) => setKeyword(e.target.value)}
                                />
                                <div className="absolute inset-y-0 ltr:left-3 rtl:right-3 flex items-center pointer-events-none text-gray-400">
                                    <Search className="w-4 h-4" />
                                </div>
                            </div>

                            {/* Due Date Period Filter */}
                            <div className="relative w-[240px]">
                                <Flatpickr
                                    options={{
                                        mode: 'range',
                                        dateFormat: 'd/m/Y',
                                        enableTime: false,
                                        position: isRtl ? 'auto right' : 'auto left',
                                    }}
                                    className="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 ltr:pl-9 rtl:pr-9"
                                    onChange={(dates: Date[]) => {
                                        if (dates.length === 2) {
                                            setStartDate(dates[0]);
                                            setEndDate(dates[1]);
                                        } else if (dates.length === 0) {
                                            setStartDate(null);
                                            setEndDate(null);
                                        }
                                    }}
                                    placeholder={t('account_payable.pembayaran.filter.due_date_period')}
                                />
                                <div className="absolute inset-y-0 ltr:left-3 rtl:right-3 flex items-center pointer-events-none text-gray-400">
                                    <IconCalendar className="w-4 h-4" />
                                </div>
                            </div>

                            {/* Status Filter */}
                            <div className="w-[150px]">
                                <select
                                    className="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                    onChange={(e) => setStatusFilter(e.target.value)}
                                    value={statusFilter}
                                >
                                    <option value={'Unpaid'}>{t('account_payable.pembayaran.filter.unpaid')}</option>
                                    <option value={'Partial'}>{t('account_payable.pembayaran.filter.partial')}</option>
                                    <option value={'Paid'}>{t('account_payable.pembayaran.filter.paid')}</option>
                                    <option value={''}>{t('common.all')}</option>
                                </select>
                            </div>

                            {/* Supplier Type Filter */}
                            <div className="w-[150px]">
                                <select
                                    className="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                                    value={supplierType}
                                    onChange={(e) => setSupplierType(e.target.value)}
                                >
                                    <option value="">Semua Tipe</option>
                                    <option value="Medis">Medis</option>
                                    <option value="Umum">Umum</option>
                                    <option value="Dapur">Dapur</option>
                                </select>
                            </div>

                            {/* Supplier Filter */}
                            <div className="w-[220px]">
                                <SelectSearch
                                    placeholder={t('account_payable.pembayaran.select_supplier')}
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

                            {/* Export Button */}
                            <Button
                                variant="outline"
                                className="gap-2"
                                onClick={handleExport}
                                disabled={isExporting || records.length === 0}
                            >
                                {isExporting ? <Loader2 className="h-4 w-4 animate-spin" /> : <Download className="h-4 w-4" />}
                                <span className="hidden sm:inline">{isExporting ? 'Exporting...' : 'Export Excel'}</span>
                            </Button>
                        </div>
                    </div>
                </CardHeader>
            </Card>

            {/* Data Card */}
            <Card>
                <CardContent className="pt-6">
                    {/* Batch action bar - shown when any record is selected */}
                    {selectedRecords.length >= 1 && (
                        <div className="mb-4 flex items-center justify-between">
                            <span className="text-sm text-gray-600">
                                {selectedRecords.length} invoice dipilih
                            </span>
                            <div className="flex items-center gap-2">
                                {new Set(selectedRecords.map(r => r.supplierName)).size === 1 && (
                                    <Button
                                        className="gap-2 text-white"
                                        style={{ backgroundColor: '#E8861A' }}
                                        onClick={() => navigate('/account-payable/form-maker')}
                                    >
                                        <FileText className="w-4 h-4" />
                                        Form Maker
                                    </Button>
                                )}
                                {selectedRecords.length > 1 && (
                                    <Button
                                        className="gap-2 bg-success hover:bg-success/90"
                                        onClick={() => navigate('/account-payable/pembayaran/batch', {
                                            state: { selectedInvoices: selectedRecords }
                                        })}
                                    >
                                        <CheckSquare className="w-4 h-4" />
                                        Bayar {selectedRecords.length} Invoice
                                    </Button>
                                )}
                            </div>
                        </div>
                    )}
                    <div className="datatables z-0 rounded-lg border border-gray-100 overflow-hidden">
                        <DataTable
                            highlightOnHover
                            className="whitespace-nowrap table-hover"
                            records={records}
                            idAccessor="invoiceExchangeId"
                            selectedRecords={selectedRecords}
                            onSelectedRecordsChange={setSelectedRecords}
                            isRecordSelectable={(record: AccountPayableData) => record.paymentStatus !== 'Paid'}
                            columns={[
                                {
                                    accessor: 'supplierName',
                                    title: t('account_payable.pembayaran.table.supplier'),
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (r: AccountPayableData) => (
                                        <div className="flex flex-col">
                                            <span className="font-semibold text-gray-800">{r.supplierName}</span>
                                            <span className="text-xs text-gray-500">{r.poNumber || '-'}</span>
                                        </div>
                                    )
                                },
                                {
                                    accessor: 'invoiceNumber',
                                    title: t('account_payable.pembayaran.table.invoice_no'),
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (r: AccountPayableData) => <span className="text-gray-600">{r.invoiceNumber || '-'}</span>
                                },
                                {
                                    accessor: 'dueDate',
                                    title: t('account_payable.pembayaran.table.due_date'),
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (r: AccountPayableData) => <span className="text-gray-600">{r.dueDate ? FormatDate(r.dueDate) : '-'}</span>
                                },
                                {
                                    accessor: 'totalTransaction',
                                    title: t('account_payable.pembayaran.table.total'),
                                    textAlignment: 'right' as const,
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50 text-right',
                                    cellsStyle: { textAlign: 'right' },
                                    render: (r: AccountPayableData) => <div className="font-mono font-medium text-gray-900 text-right">{FormatNumberRounded(r.totalTransaction)}</div>
                                },
                                {
                                    accessor: 'paidAmount',
                                    title: t('account_payable.pembayaran.table.paid'),
                                    textAlignment: 'right' as const,
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50 text-right',
                                    cellsStyle: { textAlign: 'right' },
                                    render: (r: AccountPayableData) => <div className="font-mono text-success text-right">{FormatNumberRounded(r.paidAmount)}</div>
                                },
                                {
                                    accessor: 'outstandingAmount',
                                    title: t('account_payable.pembayaran.table.outstanding'),
                                    textAlignment: 'right' as const,
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50 text-right',
                                    cellsStyle: { textAlign: 'right' },
                                    render: (r: AccountPayableData) => <div className="font-mono font-bold text-gray-900 text-right">{FormatNumberRounded(r.outstandingAmount)}</div>
                                },
                                {
                                    accessor: 'paymentStatus',
                                    title: t('account_payable.pembayaran.table.status'),
                                    textAlignment: 'center' as const,
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (r: AccountPayableData) => {
                                        let badgeClass = 'bg-gray-100 text-gray-500';
                                        let label = r.paymentStatus || 'Unpaid';

                                        if (r.paymentStatus === 'Paid') badgeClass = 'bg-success/10 text-success';
                                        else if (r.paymentStatus === 'Partial') badgeClass = 'bg-info/10 text-info';
                                        else badgeClass = 'bg-warning/10 text-warning'; // Unpaid

                                        // Lunas via Mark as Paid: warna & label dibedakan supaya tidak
                                        // tertukar dengan pembayaran yang benar-benar keluar dari kas/bank.
                                        if (r.paymentStatus === 'Paid' && r.manualPaid) {
                                            badgeClass = 'bg-info/10 text-info';
                                            label = t('account_payable.pembayaran.paid_manual', 'Paid (Manual)');
                                        }

                                        return (
                                            <span
                                                className={`px-2 py-1 rounded-full text-xs font-semibold ${badgeClass}`}
                                                title={r.manualPaid ? r.manualPaidNote || undefined : undefined}
                                            >
                                                {label}
                                            </span>
                                        );
                                    }
                                },
                                {
                                    accessor: 'agingBucket',
                                    title: t('account_payable.pembayaran.table.aging'),
                                    textAlignment: 'center' as const,
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (r: AccountPayableData) => (
                                        <span className={`px-2 py-1 rounded-full text-xs font-semibold ${getAgingColor(r.agingBucket)}`}>
                                            {r.agingBucket}
                                        </span>
                                    )
                                },
                                {
                                    accessor: 'action',
                                    title: t('common.action'),
                                    textAlignment: 'center' as const,
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    width: 120,
                                    render: (r: AccountPayableData) => {
                                        if (r.paymentStatus === 'Paid') {
                                            return (
                                                <div className="flex justify-center">
                                                    <Button
                                                        size="sm"
                                                        variant="outline"
                                                        className="gap-1.5 h-8"
                                                        onClick={() => handleViewDetail(r)}
                                                    >
                                                        <Eye className="w-3.5 h-3.5" />
                                                        Detail
                                                    </Button>
                                                </div>
                                            );
                                        }
                                        return (
                                            <div className="flex justify-center">
                                                <Button
                                                    size="sm"
                                                    className="gap-1.5 h-8 bg-success hover:bg-success/90"
                                                    onClick={() => handlePayAction(r)}
                                                >
                                                    <Banknote className="w-3.5 h-3.5" />
                                                    Bayar
                                                </Button>
                                            </div>
                                        );
                                    }
                                },
                            ]}
                            totalRecords={totalRecords}
                            recordsPerPage={pageSize}
                            page={page}
                            onPageChange={setPage}
                            recordsPerPageOptions={PAGE_SIZES}
                            onRecordsPerPageChange={setPageSize}
                            minHeight={200}
                            paginationText={({ from, to, totalRecords }) =>
                                `Showing  ${from} to ${to} of ${totalRecords} entries`
                            }
                            fetching={isLoading}
                            loaderVariant="dots"
                            loaderColor="#4361ee"
                            styles={{
                                root: {
                                    border: 'none',
                                    borderRadius: '0.75rem',
                                    overflow: 'hidden',
                                },
                                pagination: {
                                    borderTop: '1px solid #f1f5f9',
                                    margin: '1rem',
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

export default PembayaranAP;
