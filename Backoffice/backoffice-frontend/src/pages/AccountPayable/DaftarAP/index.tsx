import { useEffect, useState } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '@/store/themeConfigSlice';
import { DataTable, DataTableSortStatus } from 'mantine-datatable';
import Flatpickr from 'react-flatpickr';
import 'flatpickr/dist/flatpickr.css';
import SelectSearch from 'react-select';
import { useGetAccountPayableListQuery, useLazyGetAccountPayableListQuery, useExportAccountPayableMutation, useCancelInvoiceExchangeMutation, useExportAccountPayableResiMutation } from '@/store/api/accountPayable/accountPayableApiSlice';
import { APListPrint } from './APListPrint';
import { useTranslation } from 'react-i18next';
import { useGetInvoiceExchangeSupplierOptionQuery, useLazyGetPrintResiByIdQuery } from '@/store/api/invoiceExchange/invoiceExchangeApiSlice';
import { FormatNumberRounded } from '@/utils/formatNumber';
import { FormatDate, formatDateForAPI } from '@/utils/formatDate';
import { toastMessage } from '@/utils/toastUtils';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import TimelineButton from '@/components/ActivityTimeline/TimelineButton';
import { AUDIT_MODULE } from '@/components/ActivityTimeline/modules';
import IconCalendar from '@/components/Icon/IconCalendar';
import ConfirmationModal from '@/components/ConfirmationModal';
import {
    FileText,
    Download,
    Filter,
    CreditCard,
    XCircle,
    Printer,
    FileSpreadsheet,
    Loader2,
    Search,
    Plus
} from 'lucide-react';
import { createPortal } from 'react-dom';
import { ResiTukarFaktur, ResiTukarFakturData } from '@/pages/settings/invoiceExchange/ResiTukarFaktur';
import ManualInvoiceModal from './ManualInvoiceModal';

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
    // true = lunas lewat "Mark as Paid" di halaman Purchase Order (tanpa transaksi bank
    // & tanpa jurnal), bukan lewat menu Pembayaran.
    manualPaid?: boolean;
    manualPaidNote?: string | null;
    manualPaidAt?: string | null;
}

const DaftarAP = () => {
    const dispatch = useDispatch();
    const { t } = useTranslation();
    const isRtl = useSelector((state: any) => state.themeConfig.rtlClass) === 'rtl';
    const user = useSelector((state: any) => state.auth.user);

    useEffect(() => {
        dispatch(setPageTitle(t('ap.list.title')));
        dispatch(setTitle(t('ap.list.title')));
        dispatch(setBreadcrumbTitle(['Dashboard', 'Account Payable', 'Daftar AP']));
    }, [dispatch]);

    // Filter states
    const [keyword, setKeyword] = useState<string>('');
    const [debouncedKeyword, setDebouncedKeyword] = useState<string>('');
    const [selectedSupplier, setSelectedSupplier] = useState<any>(null);
    const [searchSupplier, setSearchSupplier] = useState<string>('');
    const [startDate, setStartDate] = useState<Date | null>(null);
    const [endDate, setEndDate] = useState<Date | null>(null);
    const [paymentStatus, setPaymentStatus] = useState<string>('Unpaid');
    const [supplierType, setSupplierType] = useState<string>('');

    useEffect(() => {
        const timer = setTimeout(() => setDebouncedKeyword(keyword), 500);
        return () => clearTimeout(timer);
    }, [keyword]);

    // Table states
    const [page, setPage] = useState<number>(1);
    const PAGE_SIZES: number[] = [10, 20, 30, 50, 100];
    const [pageSize, setPageSize] = useState<number>(PAGE_SIZES[0]);
    const [sortStatus, setSortStatus] = useState<DataTableSortStatus>({
        columnAccessor: 'dueDate',
        direction: 'asc',
    });

    // API calls
    const { data: supplierList, isLoading: isLoadingSupplier } = useGetInvoiceExchangeSupplierOptionQuery(
        { keyword: searchSupplier || '', page: 1, pageSize: 20 }, { skip: false }
    );

    const [exportAP, { isLoading: isExporting }] = useExportAccountPayableMutation();
    const [cancelInvoiceExchange, { isLoading: isCancelling }] = useCancelInvoiceExchangeMutation();

    // Cancel modal state
    const [showCancelModal, setShowCancelModal] = useState(false);
    const [cancelInvoiceId, setCancelInvoiceId] = useState<number | null>(null);
    const [showManualModal, setShowManualModal] = useState(false);

    const formatDateForAPI = (date: Date | null): string => {
        if (!date) return '';
        return new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate())).toISOString();
    };

    const formattedStartDate = formatDateForAPI(startDate);
    const formattedEndDate = formatDateForAPI(endDate);
    const supplierLabel = selectedSupplier?.label || '';

    const { data: apList, isLoading, refetch } = useGetAccountPayableListQuery({
        keyword: debouncedKeyword,
        supplierName: supplierLabel,
        paymentStatus: paymentStatus,
        supplierType: supplierType,
        startDate: formattedStartDate,
        endDate: formattedEndDate,
        page,
        pageSize,
        orderBy: sortStatus.columnAccessor,
        orderType: sortStatus.direction,
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

    const supplierOptions = [{ label: t('ap.list.filter.all_suppliers'), value: '' }, ...getSupplierOptions()];

    const handleExport = async () => {
        try {
            const blob = await exportAP({
                supplierName: selectedSupplier?.label || '',
                paymentStatus: paymentStatus,
                supplierType: supplierType,
                startDate: formatDateForAPI(startDate),
                endDate: formatDateForAPI(endDate),
                type: 'xlsx',
            }).unwrap();

            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', `daftar_ap_${new Date().toISOString().split('T')[0]}.xlsx`);
            document.body.appendChild(link);
            link.click();
            link.remove();
            window.URL.revokeObjectURL(url);
        } catch (error: any) {
            toastMessage(error?.data?.message || 'Gagal export data', 'error');
        }
    };

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

    const records: AccountPayableData[] = apList?.data?.data || apList?.data || [];
    const totalRecords = apList?.data?.totalData || records.length;

    useEffect(() => {
        setPage(1);
    }, [sortStatus, pageSize, paymentStatus, selectedSupplier, startDate, endDate, debouncedKeyword, supplierType]);

    const handleCancelClick = (invoiceId: number) => {
        setCancelInvoiceId(invoiceId);
        setShowCancelModal(true);
    };

    const handleConfirmCancel = async () => {
        if (!cancelInvoiceId) return;
        try {
            await cancelInvoiceExchange({ invoiceIds: [cancelInvoiceId] }).unwrap();
            toastMessage(t('ap.list.cancel_success') || 'Invoice berhasil dibatalkan', 'success');
            refetch();
        } catch (error: any) {
            toastMessage(error?.data?.message || t('ap.list.cancel_error') || 'Gagal membatalkan invoice', 'error');
        } finally {
            setShowCancelModal(false);
            setCancelInvoiceId(null);
        }
    };

    const [exportAccountPayableExcelResi, { isLoading: isExportingResi }] = useExportAccountPayableResiMutation();

    const handleExportExcelResi = async (id: number, invoiceNo: string) => {
        try {
            const blob = await exportAccountPayableExcelResi(id).unwrap();
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', `Resi_AP_${invoiceNo}_${new Date().toISOString().split('T')[0]}.xlsx`);
            document.body.appendChild(link);
            link.click();
            link.remove();
            window.URL.revokeObjectURL(url);
        } catch (error: any) {
            toastMessage(error?.data?.message || 'Gagal export data resi', 'error');
        }
    };

    const [triggerFetchAllAP, { isFetching: isFetchingAll }] = useLazyGetAccountPayableListQuery();
    const [allRecordsToPrint, setAllRecordsToPrint] = useState<any[]>([]);

    const handlePrintList = async () => {
        try {
            const result = await triggerFetchAllAP({
                supplierName: selectedSupplier?.label || '',
                paymentStatus: paymentStatus,
                supplierType: supplierType,
                startDate: formattedStartDate,
                endDate: formattedEndDate,
                page: -1,
                pageSize: -1, // Fetch all
                orderBy: sortStatus.columnAccessor,
                orderType: sortStatus.direction,
            }).unwrap();

            const data = result?.data?.data || result?.data || [];
            if (data.length > 0) {
                setAllRecordsToPrint(data);
                // Clear other print data to avoid conflicts
                setPrintData(null);

                setTimeout(() => {
                    window.print();
                    // Optional: clear after print to keep DOM clean
                    // setAllRecordsToPrint([]);
                }, 500);
            } else {
                toastMessage('Tidak ada data untuk dicetak', 'warning');
            }
        } catch (error) {
            console.error('Failed to fetch full AP list for printing', error);
            toastMessage('Gagal mengambil data cetak', 'error');
        }
    };

    // Print state
    const [triggerGetPrintResiById] = useLazyGetPrintResiByIdQuery();
    const [printData, setPrintData] = useState<ResiTukarFakturData | null>(null);

    // Clean up print portals after printing
    useEffect(() => {
        const handleAfterPrint = () => {
            setPrintData(null);
            setAllRecordsToPrint([]);
        };
        window.addEventListener('afterprint', handleAfterPrint);
        return () => window.removeEventListener('afterprint', handleAfterPrint);
    }, []);

    const handlePrintResi = async (invoiceId: number, supplierName: string) => {
        try {
            const result = await triggerGetPrintResiById(invoiceId).unwrap();
            if (result && result.data) {
                setAllRecordsToPrint([]); // Clear list portal to avoid conflict
                setPrintData(result.data);
                setTimeout(() => {
                    window.print();
                }, 500);
            }
        } catch (error) {
            console.error('Failed to fetch print resi data', error);
            toastMessage(t('ap.list.print_error') || 'Gagal mengambil data cetak', 'error');
        }
    };

    return (
        <div className="space-y-6">
            <Card>
                <CardHeader className="border-b border-border pb-4 no-print">
                    {/* Row 1: Title */}
                    <div className="flex items-center gap-3 mb-4">
                        <div className="p-2 rounded-lg bg-primary/10">
                            <FileText className="h-5 w-5 text-primary" />
                        </div>
                        <div>
                            <CardTitle className="text-lg">{t('ap.list.title')}</CardTitle>
                            <p className="text-sm text-muted-foreground mt-0.5">
                                {t('ap.list.subtitle')}
                            </p>
                        </div>
                    </div>

                    {/* Row 2: Filters & Actions */}
                    <div className="flex flex-wrap items-center gap-3">
                        {/* Keyword Filter */}
                        <div className="relative">
                            <input
                                type="text"
                                className="flex h-10 w-[220px] rounded-lg border border-gray-200 bg-white pl-9 pr-3 py-2 text-sm text-gray-900 placeholder:text-gray-400 hover:border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none"
                                placeholder="No Invoice / No PO"
                                value={keyword}
                                onChange={(e) => setKeyword(e.target.value)}
                            />
                            <div className="absolute inset-y-0 left-3 flex items-center pointer-events-none text-gray-400">
                                <Search className="w-4 h-4" />
                            </div>
                        </div>

                        {/* Filter Status */}
                        <select
                            className="flex h-10 w-[150px] rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 hover:border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none"
                            value={paymentStatus}
                            onChange={(e) => setPaymentStatus(e.target.value)}
                        >
                            <option value="">{t('ap.list.filter.all_status')}</option>
                            <option value="Unpaid">{t('ap.list.filter.unpaid')}</option>
                            <option value="Paid">{t('ap.list.filter.paid')}</option>
                        </select>

                        {/* Supplier Type Filter */}
                        <select
                            className="flex h-10 w-[150px] rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 hover:border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none"
                            value={supplierType}
                            onChange={(e) => setSupplierType(e.target.value)}
                        >
                            <option value="">Semua Tipe</option>
                            <option value="Medis">Medis</option>
                            <option value="Umum">Umum</option>
                            <option value="Dapur">Dapur</option>
                        </select>

                        {/* Supplier Filter */}
                        <div className="w-[200px]">
                            <SelectSearch
                                placeholder={t('ap.list.filter.select_supplier')}
                                options={supplierOptions}
                                isLoading={isLoadingSupplier}
                                onInputChange={(v) => setSearchSupplier(v)}
                                onChange={(opt: any) => setSelectedSupplier(opt)}
                                value={selectedSupplier}
                                isClearable
                                styles={{
                                    control: (base, state) => ({
                                        ...base,
                                        minHeight: '40px',
                                        borderColor: state.isFocused ? '#648de1' : '#e5e7eb',
                                        backgroundColor: '#ffffff',
                                        borderRadius: '0.5rem',
                                        boxShadow: state.isFocused ? '0 0 0 2px rgba(100,141,225,0.2)' : 'none',
                                        '&:hover': { borderColor: '#d1d5db' },
                                    }),
                                    placeholder: (base) => ({ ...base, color: '#9ca3af' }),
                                    singleValue: (base) => ({ ...base, color: '#111827' }),
                                    input: (base) => ({ ...base, color: '#111827' }),
                                    menu: (base) => ({ ...base, backgroundColor: '#ffffff', border: '1px solid #e5e7eb' }),
                                    option: (base, state) => ({ ...base, backgroundColor: state.isFocused ? '#f3f4f6' : 'transparent', color: '#111827' }),
                                    menuPortal: (base) => ({ ...base, zIndex: 9999 }),
                                }}
                                menuPortalTarget={document.body}
                            />
                        </div>

                        {/* Date Filter */}
                        <div className="relative">
                            <Flatpickr
                                options={{
                                    mode: 'range',
                                    dateFormat: 'd/m/Y',
                                    enableTime: false,
                                    position: isRtl ? 'auto right' : 'auto left',
                                }}
                                className="flex h-10 w-[220px] rounded-lg border border-gray-200 bg-white pl-9 pr-3 py-2 text-sm text-gray-900 placeholder:text-gray-400 hover:border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none"
                                onChange={(dates: Date[]) => {
                                    setStartDate(dates.length > 0 ? dates[0] : null);
                                    setEndDate(dates.length > 1 ? dates[1] : null);
                                }}
                                placeholder={t('ap.list.filter.due_date')}
                            />
                            <div className="absolute inset-y-0 left-3 flex items-center pointer-events-none text-gray-400">
                                <IconCalendar className="w-4 h-4" />
                            </div>
                        </div>

                        <div className="flex items-center gap-3 ml-auto">
                            <Button
                                className="gap-2 text-white"
                                onClick={() => setShowManualModal(true)}
                            >
                                <Plus className="h-4 w-4" />
                                <span className="hidden sm:inline">Input Manual</span>
                            </Button>

                            <Button
                                variant="outline"
                                className="gap-2"
                                onClick={handlePrintList}
                                disabled={isFetchingAll || records.length === 0}
                            >
                                {isFetchingAll ? <Loader2 className="h-4 w-4 animate-spin" /> : <Printer className="h-4 w-4" />}
                                <span className="hidden sm:inline">Cetak PDF</span>
                            </Button>

                            <Button
                                variant="outline"
                                className="gap-2"
                                onClick={handleExport}
                                disabled={isExporting || records.length === 0}
                            >
                                {isExporting ? <Loader2 className="h-4 w-4 animate-spin" /> : <Download className="h-4 w-4" />}
                                <span className="hidden sm:inline">{isExporting ? t('common.loading') : 'Export Excel'}</span>
                            </Button>
                        </div>
                    </div>
                </CardHeader>

                <CardContent className="pt-6">
                    <div className="datatables z-0 rounded-lg border border-gray-100 overflow-hidden">
                        <DataTable
                            highlightOnHover
                            className="whitespace-nowrap table-hover"
                            records={records}
                            columns={[
                                {
                                    accessor: 'supplierName',
                                    title: t('ap.list.table.supplier'),
                                    sortable: true,
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (r: AccountPayableData) => <span className="font-medium text-gray-700">{r.supplierName}</span>
                                },
                                {
                                    accessor: 'poNumber',
                                    title: t('ap.list.table.po_no'),
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (r: AccountPayableData) => <span className="text-gray-600">{r.poNumber || '-'}</span>
                                },
                                {
                                    accessor: 'invoiceNumber',
                                    title: t('ap.list.table.invoice_no'),
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (r: AccountPayableData) => <span className="text-gray-600">{r.invoiceNumber || '-'}</span>
                                },
                                {
                                    accessor: 'dueDate',
                                    title: t('ap.list.table.due_date'),
                                    sortable: true,
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (r: AccountPayableData) => <span className="text-gray-600">{r.dueDate ? FormatDate(r.dueDate) : '-'}</span>
                                },
                                {
                                    accessor: 'totalTransaction',
                                    title: t('ap.list.table.total'),
                                    sortable: true,
                                    textAlignment: 'right' as const,
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50 text-right',
                                    cellsStyle: { textAlign: 'right' },
                                    render: (r: AccountPayableData) => <div className="font-mono text-gray-900 text-right">{FormatNumberRounded(r.totalTransaction)}</div>
                                },
                                {
                                    accessor: 'outstandingAmount',
                                    title: t('ap.list.table.outstanding'),
                                    textAlignment: 'right' as const,
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50 text-right',
                                    cellsStyle: { textAlign: 'right' },
                                    render: (r: AccountPayableData) => <div className="font-mono font-bold text-gray-900 text-right">{FormatNumberRounded(r.outstandingAmount)}</div>
                                },
                                {
                                    accessor: 'agingBucket',
                                    title: t('ap.list.table.aging'),
                                    textAlignment: 'center' as const,
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (r: AccountPayableData) => (
                                        <span className={`px-2 py-1 rounded-full text-xs font-semibold ${getAgingColor(r.agingBucket)}`}>
                                            {r.agingBucket} {r.agingDays > 0 ? `(${r.agingDays}d)` : ''}
                                        </span>
                                    )
                                },
                                {
                                    accessor: 'paymentStatus',
                                    title: t('ap.list.table.status'),
                                    textAlignment: 'center' as const,
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (r: AccountPayableData) => {
                                        const isPaid = r.paymentStatus === 'Paid';
                                        // Paid manual dibedakan warnanya + tooltip catatan, supaya tidak
                                        // tertukar dengan pembayaran yang benar-benar lewat kas/bank.
                                        const badgeClass = !isPaid
                                            ? 'bg-warning/10 text-warning'
                                            : r.manualPaid
                                                ? 'bg-info/10 text-info'
                                                : 'bg-success/10 text-success';
                                        const label = !isPaid
                                            ? t('ap.list.filter.unpaid')
                                            : r.manualPaid
                                                ? t('ap.list.paid_manual', 'Paid (Manual)')
                                                : t('ap.list.filter.paid');

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
                                    accessor: 'actions',
                                    title: t('common.actions') || 'Aksi',
                                    textAlignment: 'center' as const,
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50 no-print',
                                    cellsClassName: 'no-print',
                                    render: (r: AccountPayableData) => (
                                        <div className="flex items-center justify-center gap-1">
                                            <Button
                                                size="sm"
                                                variant="outline"
                                                className="h-7 px-2 gap-1"
                                                onClick={() => handlePrintResi(r.invoiceExchangeId, r.supplierName)}
                                            >
                                                <Printer className="w-3 h-3" />
                                                <span className="hidden xl:inline text-xs">Cetak</span>
                                            </Button>
                                            <Button
                                                size="sm"
                                                variant="outline"
                                                className="h-7 px-2 gap-1 text-success border-success/30 hover:bg-success/10"
                                                onClick={() => handleExportExcelResi(r.invoiceExchangeId, r.invoiceNumber)}
                                                disabled={isExportingResi}
                                            >
                                                {isExportingResi ? <Loader2 className="w-3 h-3 animate-spin" /> : <FileSpreadsheet className="w-3 h-3" />}
                                                <span className="hidden xl:inline text-xs">Excel</span>
                                            </Button>
                                            {(r.paidAmount === 0 || !r.paidAmount) && (
                                                <Button
                                                    size="sm"
                                                    variant="destructive"
                                                    className="h-7 px-2 gap-1"
                                                    onClick={() => handleCancelClick(r.invoiceExchangeId)}
                                                    disabled={isCancelling}
                                                >
                                                    <XCircle className="w-3 h-3" />
                                                    <span className="hidden xl:inline text-xs">Batal</span>
                                                </Button>
                                            )}
                                            <TimelineButton
                                                module={AUDIT_MODULE.INVOICE_EXCHANGE}
                                                refId={r.invoiceExchangeId}
                                                title={r.invoiceNumber}
                                            />
                                        </div>
                                    )
                                },
                            ]}
                            totalRecords={totalRecords}
                            recordsPerPage={pageSize}
                            page={page}
                            onPageChange={setPage}
                            recordsPerPageOptions={PAGE_SIZES}
                            onRecordsPerPageChange={setPageSize}
                            sortStatus={sortStatus}
                            onSortStatusChange={setSortStatus}
                            minHeight={200}
                            paginationText={({ from, to, totalRecords }) => t('common.pagination_text', { from, to, totalRecords })}
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

            {/* Cancel Confirmation Modal */}
            <ConfirmationModal
                isOpen={showCancelModal}
                onClose={() => {
                    setShowCancelModal(false);
                    setCancelInvoiceId(null);
                }}
                onConfirm={handleConfirmCancel}
                title={t('ap.list.cancel_title') || 'Konfirmasi Pembatalan'}
                message={t('ap.list.cancel_message') || 'Apakah Anda yakin ingin membatalkan tukar faktur ini? Status akan kembali ke "not sent".'}
                confirmText={t('ap.list.cancel_confirm') || 'Ya, Batalkan'}
                cancelText={t('common.cancel') || 'Batal'}
            />

            {/* Manual (non-PO) invoice entry */}
            <ManualInvoiceModal isOpen={showManualModal} onClose={() => setShowManualModal(false)} />

            {/* List Print Portal */}
            {allRecordsToPrint.length > 0 && createPortal(
                <div id="printable-list-container">
                    <style>
                        {`
                        @media print {
                            body > *:not(#printable-list-container) { display: none !important; }
                            #printable-list-container { display: block !important; position: absolute; left: 0; top: 0; width: 100%; height: 100%; background: white; z-index: 9999; }
                            @page { size: landscape; margin: 10mm; }
                        }
                        @media screen {
                            #printable-list-container { display: none; }
                        }
                    `}
                    </style>
                    <APListPrint
                        data={allRecordsToPrint}
                        filters={{
                            supplierName: selectedSupplier?.label,
                            paymentStatus: paymentStatus,
                            startDate: startDate,
                            endDate: endDate
                        }}
                        printedBy={user?.displayName || 'Admin'}
                    />
                </div>,
                document.body
            )}

            {/* Print Resi Portal */}
            {printData && createPortal(
                <div id="printable-resi-container">
                    <style>
                        {`
                        @media print {
                            body > *:not(#printable-resi-container) { display: none !important; }
                            #printable-resi-container { display: block !important; position: absolute; left: 0; top: 0; width: 100%; height: 100%; background: white; z-index: 9999; }
                            @page { size: auto; margin: 0; }
                        }
                        @media screen {
                            #printable-resi-container { display: none; }
                        }
                    `}
                    </style>
                    <ResiTukarFaktur data={printData} />
                </div>,
                document.body
            )}
        </div >
    );
};

export default DaftarAP;
