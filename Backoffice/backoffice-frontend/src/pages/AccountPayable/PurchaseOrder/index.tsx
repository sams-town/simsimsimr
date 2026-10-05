import { useEffect, useState } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '@/store/themeConfigSlice';
import { DataTable, DataTableSortStatus } from 'mantine-datatable';
import Flatpickr from 'react-flatpickr';
import 'flatpickr/dist/flatpickr.css';
import { useTranslation } from 'react-i18next';
import {
    useGetPurchaseOrdersQuery,
    useMarkPurchaseOrderAsPaidMutation,
    useUnmarkPurchaseOrderAsPaidMutation,
    useExportPurchaseOrdersMutation,
} from '@/store/api/purchaseOrder/purchaseOrderApiSlice';
import { FormatNumberRounded } from '@/utils/formatNumber';
import { FormatDate } from '@/utils/formatDate';
import { toastMessage } from '@/utils/toastUtils';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import ConfirmationModal from '@/components/ConfirmationModal';
import IconCalendar from '@/components/Icon/IconCalendar';
import { ShoppingCart, Search, Banknote, CheckCircle2, Download, Loader2, Undo2 } from 'lucide-react';
import PayDPModal from './PayDPModal';

// Roles allowed to Mark as Paid: Administrator(1), Manager(2), Supervisor(3).
// Staff(4) and Petty Cash(5) are blocked (mirrors the backend gating).
const MARK_AS_PAID_ROLES = [1, 2, 3];

interface PurchaseOrder {
    orderNumber: string;
    date: string;
    supplierName: string;
    grandTotal?: number;
    total: number;
    downPayment: number;
    status: string;
    manualPaid?: boolean;
    manualPaidNote?: string | null;
    manualPaidAt?: string | null;
}

const PurchaseOrderList = () => {
    const dispatch = useDispatch();
    const { t } = useTranslation();
    const isRtl = useSelector((state: any) => state.themeConfig.rtlClass) === 'rtl';

    useEffect(() => {
        dispatch(setPageTitle('Purchase Order'));
        dispatch(setTitle('Purchase Order'));
        dispatch(setBreadcrumbTitle(['Dashboard', 'Account Payable', 'Purchase Order']));
    }, [dispatch]);

    // Filter states
    const [search, setSearch] = useState<string>('');
    const [startDate, setStartDate] = useState<Date | null>(null);
    const [endDate, setEndDate] = useState<Date | null>(null);
    const [status, setStatus] = useState<string>('Proses Pesan');
    const [supplierType, setSupplierType] = useState<string>('');
    // Arsip: PO yang sudah ditandai Paid manual hilang dari list normal, jadi view ini
    // adalah satu-satunya cara meninjau & membatalkannya.
    const [markedPaidView, setMarkedPaidView] = useState<boolean>(false);

    // Pagination
    const [page, setPage] = useState<number>(1);
    const PAGE_SIZES = [10, 20, 50, 100];
    const [pageSize, setPageSize] = useState<number>(PAGE_SIZES[0]);

    // Modal State
    const [showDPModal, setShowDPModal] = useState(false);
    const [selectedPO, setSelectedPO] = useState<any>(null);

    // Role gating for "Mark as Paid"
    const user = useSelector((state: any) => state.auth.user);
    const canMarkAsPaid = MARK_AS_PAID_ROLES.includes(user?.role?.roleID);

    // Mark as Paid state
    const [showMarkModal, setShowMarkModal] = useState(false);
    const [markPO, setMarkPO] = useState<any>(null);
    const [markNote, setMarkNote] = useState('');
    const [markAsPaid, { isLoading: isMarking }] = useMarkPurchaseOrderAsPaidMutation();
    const [exportPurchaseOrders, { isLoading: isExporting }] = useExportPurchaseOrdersMutation();

    // Batal Mark as Paid
    const [showUnmarkModal, setShowUnmarkModal] = useState(false);
    const [unmarkPO, setUnmarkPO] = useState<any>(null);
    const [unmarkNote, setUnmarkNote] = useState('');
    const [unmarkAsPaid, { isLoading: isUnmarking }] = useUnmarkPurchaseOrderAsPaidMutation();

    const formatDateForAPI = (date: Date | null): string => {
        if (!date) return '';
        return new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate())).toISOString();
    };

    const { data: poList, isLoading, refetch } = useGetPurchaseOrdersQuery({
        search,
        status,
        supplierType,
        startDate: formatDateForAPI(startDate),
        endDate: formatDateForAPI(endDate),
        page,
        pageSize,
        markedPaid: markedPaidView
    });

    useEffect(() => {
        setPage(1);
    }, [markedPaidView, status, supplierType, search, startDate, endDate]);

    const records = poList?.data || [];
    const totalRecords = poList?.total || 0;

    const handlePayDP = (po: any) => {
        setSelectedPO(po);
        setShowDPModal(true);
    };

    const openMarkModal = (po: any) => {
        setMarkPO(po);
        setMarkNote('');
        setShowMarkModal(true);
    };

    const confirmMarkAsPaid = async () => {
        if (!markPO) return;
        if (!markNote.trim()) {
            toastMessage('Catatan (note) wajib diisi.', 'error');
            return;
        }
        try {
            const res = await markAsPaid({ orderNumber: markPO.orderNumber, note: markNote.trim() }).unwrap();
            toastMessage(res?.message || `PO ${markPO.orderNumber} berhasil ditandai sebagai Paid.`, 'success');
            setShowMarkModal(false);
            setMarkPO(null);
            setMarkNote('');
            refetch();
        } catch (err: any) {
            toastMessage(err?.data?.message || 'Gagal menandai PO sebagai Paid.', 'error');
        }
    };

    const openUnmarkModal = (po: any) => {
        setUnmarkPO(po);
        setUnmarkNote('');
        setShowUnmarkModal(true);
    };

    const confirmUnmarkAsPaid = async () => {
        if (!unmarkPO) return;
        if (!unmarkNote.trim()) {
            toastMessage('Alasan pembatalan wajib diisi.', 'error');
            return;
        }
        try {
            const res = await unmarkAsPaid({ orderNumber: unmarkPO.orderNumber, note: unmarkNote.trim() }).unwrap();
            toastMessage(res?.message || `Tanda Paid PO ${unmarkPO.orderNumber} dibatalkan.`, 'success');
            setShowUnmarkModal(false);
            setUnmarkPO(null);
            setUnmarkNote('');
            refetch();
        } catch (err: any) {
            toastMessage(err?.data?.message || 'Gagal membatalkan tanda Paid.', 'error');
        }
    };

    const handleExport = async () => {
        try {
            const blob = await exportPurchaseOrders({
                search,
                status,
                supplierType,
                startDate: formatDateForAPI(startDate),
                endDate: formatDateForAPI(endDate),
                markedPaid: markedPaidView || undefined,
            }).unwrap();

            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', `purchase_order_${new Date().toISOString().split('T')[0]}.xlsx`);
            document.body.appendChild(link);
            link.click();
            link.remove();
            window.URL.revokeObjectURL(url);
        } catch {
            toastMessage('Gagal export data', 'error');
        }
    };

    return (
        <div className="space-y-6">
            <Card>
                <CardHeader className="border-b border-border pb-4">
                    <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div className="flex items-center gap-4">
                            <div className="p-2 rounded-lg bg-primary/10">
                                <ShoppingCart className="h-5 w-5 text-primary" />
                            </div>
                            <div>
                                <CardTitle className="text-lg">{t('account_payable.purchase_order.list_title')}</CardTitle>
                                <p className="text-sm text-muted-foreground mt-0.5">
                                    {t('account_payable.purchase_order.subtitle')}
                                </p>
                            </div>
                        </div>

                        <div className="flex flex-wrap items-center gap-3">
                            {/* Search */}
                            <div className="relative">
                                <input
                                    type="text"
                                    className="flex h-10 w-full sm:w-[200px] items-center rounded-md border border-input bg-background pl-9 pr-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                                    placeholder={t('account_payable.purchase_order.search_placeholder')}
                                    value={search}
                                    onChange={(e) => setSearch(e.target.value)}
                                />
                                <div className="absolute inset-y-0 left-3 flex items-center pointer-events-none text-muted-foreground">
                                    <Search className="w-4 h-4" />
                                </div>
                            </div>

                            {/* Status Filter */}
                            <div className="relative">
                                <select
                                    className="flex h-10 w-[150px] items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                                    value={status}
                                    onChange={(e) => setStatus(e.target.value)}
                                >
                                    <option value="">{t('account_payable.purchase_order.filter.all_status')}</option>
                                    <option value="Proses Pesan">Proses Pesan</option>
                                    <option value="Sudah Datang">Sudah Datang</option>
                                </select>
                            </div>

                            {/* View: outstanding vs arsip yang sudah ditandai Paid */}
                            <div className="relative">
                                <select
                                    className="flex h-10 w-[190px] items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                                    value={markedPaidView ? 'marked' : 'outstanding'}
                                    onChange={(e) => setMarkedPaidView(e.target.value === 'marked')}
                                >
                                    <option value="outstanding">
                                        {t('account_payable.purchase_order.filter.outstanding', 'PO Outstanding')}
                                    </option>
                                    <option value="marked">
                                        {t('account_payable.purchase_order.filter.marked_paid', 'Sudah Ditandai Paid')}
                                    </option>
                                </select>
                            </div>

                            {/* Supplier Type Filter */}
                            <div className="relative">
                                <select
                                    className="flex h-10 w-[150px] items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                                    value={supplierType}
                                    onChange={(e) => setSupplierType(e.target.value)}
                                >
                                    <option value="">Semua Tipe</option>
                                    <option value="Medis">Medis</option>
                                    <option value="Umum">Umum</option>
                                    <option value="Dapur">Dapur</option>
                                </select>
                            </div>

                            {/* Date Filter */}
                            <div className="relative w-[220px]">
                                <Flatpickr
                                    options={{
                                        mode: 'range',
                                        dateFormat: 'd/m/Y',
                                        enableTime: false,
                                    }}
                                    className="flex h-10 w-full items-center rounded-md border border-input bg-background pl-9 pr-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                                    onChange={(dates) => {
                                        setStartDate(dates[0] || null);
                                        setEndDate(dates[1] || null);
                                    }}
                                    placeholder={t('account_payable.purchase_order.date_placeholder')}
                                />
                                <div className="absolute inset-y-0 left-3 flex items-center pointer-events-none text-muted-foreground">
                                    <IconCalendar className="w-4 h-4" />
                                </div>
                            </div>

                            {/* Export */}
                            <Button
                                variant="outline"
                                className="gap-2 h-10"
                                onClick={handleExport}
                                disabled={isExporting}
                            >
                                {isExporting ? <Loader2 className="w-4 h-4 animate-spin" /> : <Download className="w-4 h-4" />}
                                {t('common.download', 'Download')}
                            </Button>
                        </div>
                    </div>
                </CardHeader>

                <CardContent className="pt-6">
                    <div className="datatables z-0 rounded-lg border border-border overflow-hidden">
                        <DataTable
                            highlightOnHover
                            className="whitespace-nowrap table-hover"
                            records={records}
                            columns={[
                                {
                                    accessor: 'orderNumber',
                                    title: t('account_payable.purchase_order.table.po_no'),
                                    titleClassName: 'font-bold text-foreground',
                                    render: (r: PurchaseOrder) => <span className="font-medium text-primary">{r.orderNumber}</span>
                                },
                                {
                                    accessor: 'date',
                                    title: t('account_payable.purchase_order.table.date'),
                                    titleClassName: 'font-bold text-foreground',
                                    render: (r: PurchaseOrder) => <span className="text-muted-foreground">{FormatDate(r.date)}</span>
                                },
                                {
                                    accessor: 'supplierName',
                                    title: t('account_payable.purchase_order.table.supplier'),
                                    titleClassName: 'font-bold text-foreground',
                                    render: (r: PurchaseOrder) => <span className="font-semibold text-foreground">{r.supplierName}</span>
                                },
                                {
                                    accessor: 'grandTotal',
                                    title: t('account_payable.purchase_order.table.total'),
                                    textAlignment: 'right',
                                    titleClassName: 'font-bold text-foreground text-right',
                                    render: (r: PurchaseOrder) => <div className="text-right font-mono">{FormatNumberRounded(r.grandTotal || r.total)}</div>
                                },
                                {
                                    accessor: 'downPayment',
                                    title: t('account_payable.purchase_order.table.total_dp'),
                                    textAlignment: 'right',
                                    titleClassName: 'font-bold text-foreground text-right',
                                    render: (r: PurchaseOrder) => <div className="text-right font-mono font-bold text-success">{FormatNumberRounded(r.downPayment || 0)}</div>
                                },
                                {
                                    accessor: 'status',
                                    title: t('account_payable.purchase_order.table.status'),
                                    textAlignment: 'center',
                                    titleClassName: 'font-bold text-foreground text-center',
                                    render: (r: PurchaseOrder) => (
                                        <span className={`px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-muted-foreground`}>
                                            {r.status || 'Draft'}
                                        </span>
                                    )
                                },
                                // Hanya relevan pada view arsip: catatan & waktu penandaan.
                                ...(markedPaidView
                                    ? [{
                                        accessor: 'manualPaidNote',
                                        title: t('account_payable.purchase_order.table.marked_paid_info', 'Ditandai Paid'),
                                        titleClassName: 'font-bold text-foreground',
                                        render: (r: PurchaseOrder) => (
                                            <div className="max-w-xs">
                                                <div className="text-xs text-muted-foreground">
                                                    {r.manualPaidAt ? FormatDate(r.manualPaidAt) : '-'}
                                                </div>
                                                <div className="truncate text-sm" title={r.manualPaidNote || ''}>
                                                    {r.manualPaidNote || '-'}
                                                </div>
                                            </div>
                                        )
                                    }]
                                    : []),
                                {
                                    accessor: 'action',
                                    title: t('account_payable.purchase_order.table.action'),
                                    textAlignment: 'center',
                                    titleClassName: 'font-bold text-foreground text-center',
                                    render: (r: PurchaseOrder) => (
                                        <div className="flex justify-center gap-2">
                                            {!markedPaidView && r.status === 'Proses Pesan' && (
                                                <Button
                                                    size="sm"
                                                    variant="outline"
                                                    className="gap-2 h-8"
                                                    onClick={() => handlePayDP(r)}
                                                >
                                                    <Banknote className="w-3 h-3" />
                                                    {t('account_payable.purchase_order.action.pay_dp')}
                                                </Button>
                                            )}
                                            {canMarkAsPaid && !markedPaidView && (
                                                <Button
                                                    size="sm"
                                                    variant="success"
                                                    className="gap-2 h-8"
                                                    onClick={() => openMarkModal(r)}
                                                >
                                                    <CheckCircle2 className="w-3 h-3" />
                                                    {t('account_payable.purchase_order.action.mark_as_paid', 'Mark as Paid')}
                                                </Button>
                                            )}
                                            {canMarkAsPaid && markedPaidView && (
                                                <Button
                                                    size="sm"
                                                    variant="outline"
                                                    className="gap-2 h-8"
                                                    onClick={() => openUnmarkModal(r)}
                                                >
                                                    <Undo2 className="w-3 h-3" />
                                                    {t('account_payable.purchase_order.action.unmark_as_paid', 'Batal Tandai Paid')}
                                                </Button>
                                            )}
                                        </div>
                                    )
                                }
                            ]}
                            totalRecords={totalRecords}
                            recordsPerPage={pageSize}
                            page={page}
                            onPageChange={setPage}
                            recordsPerPageOptions={PAGE_SIZES}
                            onRecordsPerPageChange={setPageSize}
                            minHeight={200}
                            fetching={isLoading}
                            loaderVariant="dots"
                            loaderColor="#4361ee"
                        />
                    </div>
                </CardContent>
            </Card>

            <PayDPModal
                isOpen={showDPModal}
                onClose={() => setShowDPModal(false)}
                poData={selectedPO}
                refetch={refetch}
            />

            <ConfirmationModal
                isOpen={showMarkModal}
                onClose={() => {
                    if (!isMarking) {
                        setShowMarkModal(false);
                        setMarkPO(null);
                        setMarkNote('');
                    }
                }}
                onConfirm={confirmMarkAsPaid}
                title={t('account_payable.purchase_order.mark_as_paid.title', 'Tandai sebagai Paid')}
                confirmText={t('account_payable.purchase_order.mark_as_paid.confirm', 'Ya, Tandai Paid')}
                cancelText={t('common.cancel', 'Batal')}
                variant="success"
                loading={isMarking}
                keepOpenOnConfirm
                customContent={
                    <div className="space-y-4">
                        <p className="text-sm text-muted-foreground">
                            {t(
                                'account_payable.purchase_order.mark_as_paid.description',
                                'PO ini akan ditandai sudah dibayar dan hilang dari daftar outstanding serta hasil download. Semua faktur dengan No. PO ini ikut menjadi Paid di Tukar Faktur, Daftar AP, dan Pembayaran.'
                            )}
                        </p>
                        <p className="text-sm text-muted-foreground">
                            {t(
                                'account_payable.purchase_order.mark_as_paid.no_journal_warning',
                                'Tidak ada jurnal yang diposting, jadi saldo Utang Supplier di buku besar tidak berubah. Gunakan hanya untuk pembayaran yang pencatatan akuntansinya sudah masuk dari sumber lain. Bisa dibatalkan lewat filter "Sudah Ditandai Paid".'
                            )}
                        </p>
                        {markPO && (
                            <div className="rounded-md border border-border bg-muted/40 px-4 py-3 text-sm space-y-1">
                                <div className="flex justify-between gap-4">
                                    <span className="text-muted-foreground">No. PO</span>
                                    <span className="font-semibold">{markPO.orderNumber}</span>
                                </div>
                                <div className="flex justify-between gap-4">
                                    <span className="text-muted-foreground">Supplier</span>
                                    <span className="font-semibold">{markPO.supplierName}</span>
                                </div>
                                <div className="flex justify-between gap-4">
                                    <span className="text-muted-foreground">Total</span>
                                    <span className="font-mono font-semibold">
                                        {FormatNumberRounded(markPO.grandTotal || markPO.total)}
                                    </span>
                                </div>
                            </div>
                        )}
                        <div className="space-y-1">
                            <label className="text-sm font-medium">
                                {t('account_payable.purchase_order.mark_as_paid.note_label', 'Catatan')}{' '}
                                <span className="text-danger">*</span>
                            </label>
                            <textarea
                                className="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                                placeholder={t(
                                    'account_payable.purchase_order.mark_as_paid.note_placeholder',
                                    'Alasan / referensi pembayaran (wajib diisi)'
                                )}
                                value={markNote}
                                onChange={(e) => setMarkNote(e.target.value)}
                                disabled={isMarking}
                            />
                        </div>
                    </div>
                }
            />

            <ConfirmationModal
                isOpen={showUnmarkModal}
                onClose={() => {
                    if (!isUnmarking) {
                        setShowUnmarkModal(false);
                        setUnmarkPO(null);
                        setUnmarkNote('');
                    }
                }}
                onConfirm={confirmUnmarkAsPaid}
                title={t('account_payable.purchase_order.unmark_as_paid.title', 'Batalkan Tanda Paid')}
                confirmText={t('account_payable.purchase_order.unmark_as_paid.confirm', 'Ya, Batalkan')}
                cancelText={t('common.cancel', 'Batal')}
                variant="danger"
                loading={isUnmarking}
                keepOpenOnConfirm
                customContent={
                    <div className="space-y-4">
                        <p className="text-sm text-muted-foreground">
                            {t(
                                'account_payable.purchase_order.unmark_as_paid.description',
                                'PO ini kembali ke daftar outstanding, dan semua faktur yang dilunasi lewat Mark as Paid dikembalikan ke status Unpaid. Faktur yang dibayar lewat menu Pembayaran tidak tersentuh.'
                            )}
                        </p>
                        {unmarkPO && (
                            <div className="rounded-md border border-border bg-muted/40 px-4 py-3 text-sm space-y-1">
                                <div className="flex justify-between gap-4">
                                    <span className="text-muted-foreground">No. PO</span>
                                    <span className="font-semibold">{unmarkPO.orderNumber}</span>
                                </div>
                                <div className="flex justify-between gap-4">
                                    <span className="text-muted-foreground">Supplier</span>
                                    <span className="font-semibold">{unmarkPO.supplierName}</span>
                                </div>
                                <div className="flex justify-between gap-4">
                                    <span className="text-muted-foreground">Catatan saat ditandai</span>
                                    <span className="font-semibold text-right">{unmarkPO.manualPaidNote || '-'}</span>
                                </div>
                            </div>
                        )}
                        <div className="space-y-1">
                            <label className="text-sm font-medium">
                                {t('account_payable.purchase_order.unmark_as_paid.note_label', 'Alasan pembatalan')}{' '}
                                <span className="text-danger">*</span>
                            </label>
                            <textarea
                                className="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                                placeholder={t(
                                    'account_payable.purchase_order.unmark_as_paid.note_placeholder',
                                    'Alasan pembatalan (wajib diisi)'
                                )}
                                value={unmarkNote}
                                onChange={(e) => setUnmarkNote(e.target.value)}
                                disabled={isUnmarking}
                            />
                        </div>
                    </div>
                }
            />
        </div>
    );
};

export default PurchaseOrderList;
