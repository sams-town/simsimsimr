import { useEffect, useState } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { useParams, useNavigate } from 'react-router-dom';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '@/store/themeConfigSlice';
import { useTranslation } from 'react-i18next';
import SelectSearch from 'react-select';
import Flatpickr from 'react-flatpickr';
import 'flatpickr/dist/flatpickr.css';
import {
    useGetAccountPayableByIdQuery,
    useProcessAccountPayablePaymentMutation,
    useCancelInvoicePaymentMutation,
} from '@/store/api/accountPayable/accountPayableApiSlice';
import { useRoleAccess } from '@/hooks/useRoleAccess';
import TimelineButton from '@/components/ActivityTimeline/TimelineButton';
import { AUDIT_MODULE } from '@/components/ActivityTimeline/modules';
import { useGetOptionBankQuery } from '@/store/api/bank/bankApiSlice';
import { FormatNumberRounded } from '@/utils/formatNumber';
import { FormatDate, formatDateForAPI } from '@/utils/formatDate';
import { toastMessage } from '@/utils/toastUtils';
import ConfirmationModal from '@/components/ConfirmationModal';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import {
    ArrowLeft,
    FileText,
    Calendar,
    Banknote,
    Percent,
    CreditCard,
    Receipt,
    CheckCircle2,
    Loader2,
    Calculator,
    Ban
} from 'lucide-react';

interface DPItem {
    dpId: number;
    dpDate: string;
    amount: number;
    note: string;
    paymentMethod: string;
}

interface PaymentHistoryItem {
    paymentDate: string;
    amount: number;
    note: string;
    coaCode: string;
    coaName: string;
}

const PembayaranAPDetail = () => {
    const dispatch = useDispatch();
    const { t } = useTranslation();
    const navigate = useNavigate();
    const isRtl = useSelector((state: any) => state.themeConfig.rtlClass) === 'rtl';
    const { paymentId } = useParams<{ paymentId: string }>();

    // API calls
    const { data: invoiceData, isLoading: isLoadingInvoice, refetch } = useGetAccountPayableByIdQuery(
        Number(paymentId),
        { skip: !paymentId, refetchOnMountOrArgChange: true }
    );
    const { data: bankList, isLoading: isLoadingBank } = useGetOptionBankQuery(
        { search: '', page: -1, pageSize: -1 }, { skip: false }
    );
    const [processPayment, { isLoading: isProcessingPayment }] = useProcessAccountPayablePaymentMutation();

    // Form states for unpaid invoice
    const [paymentDate, setPaymentDate] = useState<Date>(new Date());
    const [selectedBank, setSelectedBank] = useState<any>(null);
    const [searchBank, setSearchBank] = useState<string>('');
    const [paymentRef, setPaymentRef] = useState<string>('');
    const [amountToPay, setAmountToPay] = useState<number>(0);
    const [isManualAmount, setIsManualAmount] = useState<boolean>(false);

    // Discount states
    const [discountPercent, setDiscountPercent] = useState<number>(0);
    const [discountAmount, setDiscountAmount] = useState<number>(0);
    const [discountMode, setDiscountMode] = useState<'percent' | 'amount'>('percent');

    // PPH23 states
    const [pph23Percent, setPph23Percent] = useState<number>(2); // Default PPH23 rate 2%
    const [pph23Amount, setPph23Amount] = useState<number>(0);
    const [pph23Mode, setPph23Mode] = useState<'percent' | 'amount'>('percent');
    const [includePph23, setIncludePph23] = useState<boolean>(false);

    const [showPaymentModal, setShowPaymentModal] = useState(false);

    const invoice = invoiceData?.data;
    const isPaid = invoice?.paymentStatus === 'Paid';

    // Batal pembayaran: hanya Administrator & Manager, dan hanya kalau memang ada
    // pembayaran yang bisa dibalik.
    const { canCancelPayment } = useRoleAccess();
    const [cancelPayment, { isLoading: cancellingPayment }] = useCancelInvoicePaymentMutation();
    const [cancelOpen, setCancelOpen] = useState(false);
    const [cancelReason, setCancelReason] = useState('');
    const adaPembayaran = Number(invoice?.paidAmount ?? 0) > 0;

    const handleCancelPayment = async () => {
        try {
            const res: any = await cancelPayment({
                invoiceExchangeId: paymentId as string,
                reason: cancelReason,
            }).unwrap();
            const noJurnal = res?.data?.cancelJournalNo ?? res?.cancelJournalNo ?? '-';
            toastMessage(`Pembayaran dibatalkan. Jurnal pembalik: ${noJurnal}`, 'success');
            setCancelOpen(false);
            setCancelReason('');
            refetch();
        } catch (err: any) {
            toastMessage(err?.data?.message || 'Gagal membatalkan pembayaran', 'error');
        }
    };

    useEffect(() => {
        dispatch(setPageTitle(t('ap.payment.detail_title') || 'Detail Pembayaran'));
        dispatch(setTitle(t('ap.payment.detail_title') || 'Detail Pembayaran'));
        dispatch(setBreadcrumbTitle(['Dashboard', 'Account Payable', 'Pembayaran', `#${paymentId}`]));
    }, [dispatch, paymentId, t]);

    // Calculate amounts
    const totalTransaction = invoice?.totalTransaction || 0;

    // Calculate discount based on mode
    const calculatedDiscountAmount = discountMode === 'percent'
        ? (totalTransaction * discountPercent) / 100
        : discountAmount;

    const totalAfterDiscount = totalTransaction - calculatedDiscountAmount;

    // Calculate PPH23 based on mode (calculated from DPP = nilai setelah diskon - PPN)
    // NOTE: User requested PPh 23 calculation base = Total - PPN. 
    // Usually it is (Total - PPN - Discount). Here we use totalAfterDiscount which already has discount deducted.
    // So we just need to deduct PPN.
    const ppnAmount = invoice?.ppnAmount || 0;
    const pph23Base = totalAfterDiscount - ppnAmount;

    const calculatedPph23Amount = includePph23
        ? (pph23Mode === 'percent'
            ? (pph23Base * pph23Percent) / 100
            : pph23Amount)
        : 0;

    const totalDP = invoice?.totalDP || 0;
    const dpList: DPItem[] = invoice?.dpList || [];
    const paymentHistory: PaymentHistoryItem[] = invoice?.paymentHistory || [];

    // Outstanding = Total setelah diskon - DP - PPH23
    // Outstanding = Total setelah diskon - DP - PPH23 - Paid Amount (backend logic consistency)
    // Frontend calculation for "Max we can pay now"
    const maxPayableAmount = totalAfterDiscount - totalDP - calculatedPph23Amount - (invoice?.paidAmount || 0);

    // Halaman ini khusus invoice yang berasal dari PO. Invoice non-PO (input manual)
    // punya rumus tagihan berbeda dan halamannya sendiri - dialihkan agar URL lama atau
    // bookmark tidak menampilkan angka dengan rumus yang salah.
    useEffect(() => {
        if (invoice && !invoice.poNumber) {
            navigate(`/account-payable/pembayaran/manual/${paymentId}`, { replace: true });
        }
    }, [invoice, paymentId, navigate]);

    // Initial Data Sync
    useEffect(() => {
        if (!invoice) return;

        // Diskon & PPh23 yang SUDAH tersimpan di invoice - misalnya diisi lewat
        // "Input Manual Tukar Faktur (Non-PO)" - harus selalu mengisi form.
        //
        // Sebelumnya blok ini dijaga `if (invoice.paymentHistory?.length > 0)`, yaitu
        // hanya berjalan kalau invoice PERNAH dibayar. Padahal form pembayaran justru
        // dibuka saat invoice belum dibayar, sehingga diskonnya tidak pernah terisi dan
        // ikut hilang dari perhitungan. Pengecekan riwayat itu urusan mengunci field
        // (lihat isLocked), bukan urusan mengisi nilai awal.
        const savedDiscount = invoice.discountAmount || 0;
        const savedDiscountPercent = invoice.discountPercent || 0;

        setDiscountPercent(savedDiscountPercent);
        setDiscountAmount(savedDiscount);
        // Pindah ke mode nominal hanya bila diskonnya memang tersimpan sebagai nominal.
        // Kalau tidak ada diskon sama sekali, biarkan 'percent' seperti default form.
        setDiscountMode(savedDiscount > 0 && savedDiscountPercent <= 0 ? 'amount' : 'percent');

        const savedPph23 = invoice.pph23Amount || 0;
        const savedPph23Percent = invoice.pph23Percent || 0;

        setIncludePph23(savedPph23 > 0);
        setPph23Percent(savedPph23Percent > 0 ? savedPph23Percent : 2);
        setPph23Amount(savedPph23);
        setPph23Mode(savedPph23 > 0 && savedPph23Percent <= 0 ? 'amount' : 'percent');
    }, [invoice]);

    // Sync Amount to Pay with Max Payable (unless manually edited)
    useEffect(() => {
        if (!isManualAmount) {
            // Ensure we don't pay more than outstanding
            // If this is a partial payment scenario, invoice.outstandingAmount from DB is accurate?
            // But we are recalculating locally based on form inputs.
            // Let's rely on local calculation:
            setAmountToPay(Math.max(0, maxPayableAmount));
        }
    }, [maxPayableAmount, isManualAmount]);

    const hasPaymentHistory = (invoice?.paymentHistory?.length || 0) > 0;
    const isLocked = hasPaymentHistory;

    // Sync discount amount when percent changes
    useEffect(() => {
        if (discountMode === 'percent') {
            setDiscountAmount((totalTransaction * discountPercent) / 100);
        }
    }, [discountPercent, totalTransaction, discountMode]);

    // Sync discount percent when amount changes
    useEffect(() => {
        if (discountMode === 'amount' && totalTransaction > 0) {
            setDiscountPercent((discountAmount / totalTransaction) * 100);
        }
    }, [discountAmount, totalTransaction, discountMode]);

    // Sync PPH23 amount when percent changes
    useEffect(() => {
        if (pph23Mode === 'percent') {
            setPph23Amount((totalAfterDiscount * pph23Percent) / 100);
        }
    }, [pph23Percent, totalAfterDiscount, pph23Mode]);

    // Sync PPH23 percent when amount changes
    useEffect(() => {
        if (pph23Mode === 'amount' && totalAfterDiscount > 0) {
            setPph23Percent((pph23Amount / totalAfterDiscount) * 100);
        }
    }, [pph23Amount, totalAfterDiscount, pph23Mode]);

    const getBankOptions = () => {
        if (!bankList) return [];
        let items: any[] = Array.isArray(bankList) ? bankList : bankList.data || [];
        return items.map((b: any) => ({
            label: b.label || b.bankName || '',
            value: b.value || b.coaCode || b.desc || '',
            desc: b.desc || b.coaCode || '',
        })).filter((i: any) => i.label && i.value);
    };

    const bankOptions = getBankOptions();

    const selectStyles = {
        control: (base: any) => ({
            ...base,
            minHeight: '40px',
            borderColor: '#e2e8f0',
            '&:hover': { borderColor: '#cbd5e1' },
        }),
        menuPortal: (base: any) => ({ ...base, zIndex: 9999 }),
    };

    const handlePayment = async () => {
        if (!selectedBank) {
            toastMessage('Pilih rekening pembayaran', 'error');
            return;
        }

        try {
            await processPayment({
                invoiceIds: [Number(paymentId)],
                paymentDate: formatDateForAPI(paymentDate),
                coaCode: selectedBank.desc || selectedBank.value,
                coaDesc: selectedBank.label,
                paymentRef: paymentRef,

                amount: amountToPay,
                discountAmount: calculatedDiscountAmount,
                discountPercent: discountPercent,
                pph23Amount: calculatedPph23Amount,
                pph23Percent: pph23Percent,
            }).unwrap();

            toastMessage('Pembayaran berhasil diproses', 'success');
            setShowPaymentModal(false);
            refetch();
        } catch (error: any) {
            toastMessage(error?.data?.message || 'Gagal memproses pembayaran', 'error');
        }
    };

    if (isLoadingInvoice) {
        return (
            <div className="flex items-center justify-center h-64">
                <Loader2 className="w-8 h-8 animate-spin text-primary" />
            </div>
        );
    }

    if (!invoice) {
        return (
            <div className="p-6">
                <div className="text-center text-gray-500">
                    Invoice tidak ditemukan
                </div>
                <div className="mt-4 text-center">
                    <Button onClick={() => navigate('/account-payable/pembayaran')}>
                        <ArrowLeft className="w-4 h-4 mr-2" />
                        Kembali
                    </Button>
                </div>
            </div>
        );
    }

    return (
        <div className="space-y-6">
            {/* Header */}
            <div className="flex items-center justify-between">
                <Button
                    variant="outline"
                    className="gap-2"
                    onClick={() => navigate('/account-payable/pembayaran')}
                >
                    <ArrowLeft className="w-4 h-4" />
                    {t('common.back') || 'Kembali'}
                </Button>
                <div className="flex items-center gap-2">
                    {invoice?.invoiceExchangeId && (
                        <TimelineButton
                            module={AUDIT_MODULE.INVOICE_EXCHANGE}
                            refId={invoice.invoiceExchangeId}
                            title={invoice.invoiceNumber}
                        />
                    )}
                    {isPaid && (
                        <Button variant="outline" className="gap-2">
                            <FileText className="w-4 h-4" />
                            {t('common.print') || 'Cetak'}
                        </Button>
                    )}
                    {canCancelPayment && adaPembayaran && (
                        <Button
                            variant="outline"
                            className="gap-2 border-red-200 text-red-600 hover:bg-red-50 hover:text-red-700"
                            onClick={() => setCancelOpen(true)}
                        >
                            <Ban className="w-4 h-4" />
                            Batalkan Pembayaran
                        </Button>
                    )}
                </div>
            </div>

            <ConfirmationModal
                isOpen={cancelOpen}
                onClose={() => setCancelOpen(false)}
                onConfirm={handleCancelPayment}
                title="Batalkan Pembayaran Invoice Ini"
                confirmText="Ya, Batalkan"
                cancelText="Batal"
                variant="danger"
                loading={cancellingPayment}
                keepOpenOnConfirm
                customContent={
                    <div className="space-y-3 text-left">
                        <div className="rounded-md bg-amber-50 border border-amber-100 px-3 py-2 text-sm text-amber-800">
                            Jurnal pembalik akan dibuat bertanggal sama dengan pembayarannya, dan invoice ini kembali
                            ke status belum lunas. <span className="font-semibold">Invoice lain dalam pembayaran yang
                            sama tidak terpengaruh.</span> Data lama tidak dihapus.
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1.5">Alasan pembatalan</label>
                            <textarea
                                rows={3}
                                className="flex w-full rounded-md border border-input bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
                                value={cancelReason}
                                onChange={(e) => setCancelReason(e.target.value)}
                                placeholder="Contoh: salah nominal transfer"
                            />
                        </div>
                    </div>
                }
            />

            {/* Invoice Info Card */}
            <Card>
                <CardHeader className="border-b bg-gradient-to-r from-primary/5 to-transparent">
                    <CardTitle className="flex items-center gap-2 text-lg">
                        <Receipt className="w-5 h-5 text-primary" />
                        {t('ap.payment.info') || 'Informasi Invoice'}
                        <span className={`ml-2 px-2 py-1 rounded-full text-xs font-semibold ${isPaid ? 'bg-success/10 text-success' : 'bg-warning/10 text-warning'}`}>
                            {isPaid ? 'LUNAS' : 'BELUM DIBAYAR'}
                        </span>
                    </CardTitle>
                </CardHeader>
                <CardContent className="p-6">
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div>
                            <p className="text-sm text-gray-500">{t('ap.payment.supplier') || 'Supplier'}</p>
                            <p className="font-semibold text-lg">{invoice.supplierName}</p>
                        </div>
                        <div>
                            <p className="text-sm text-gray-500">{t('ap.payment.invoice_no') || 'No. Faktur'}</p>
                            <p className="font-semibold">{invoice.invoiceNumber || '-'}</p>
                        </div>
                        <div>
                            <p className="text-sm text-gray-500">{t('ap.payment.po_no') || 'No. PO'}</p>
                            <p className="font-semibold">{invoice.poNumber || '-'}</p>
                        </div>
                        <div>
                            <p className="text-sm text-gray-500">{t('ap.payment.due_date') || 'Jatuh Tempo'}</p>
                            <p className="font-semibold flex items-center gap-1">
                                <Calendar className="w-4 h-4 text-gray-400" />
                                {invoice.dueDate ? FormatDate(invoice.dueDate) : '-'}
                            </p>
                        </div>
                        <div>
                            <p className="text-sm text-gray-500">{t('ap.payment.amount') || 'Nilai Faktur'}</p>
                            <p className="font-semibold font-mono text-lg">{FormatNumberRounded(totalTransaction)}</p>
                        </div>
                        <div>
                            <p className="text-sm text-gray-500">{t('ap.payment.aging') || 'Aging'}</p>
                            <p className={`px-2 py-1 inline-block rounded-full text-xs font-semibold 
                                ${invoice.agingBucket === 'Current' ? 'bg-success/10 text-success' :
                                    invoice.agingBucket === '1-30' ? 'bg-warning/10 text-warning' :
                                        invoice.agingBucket === '31-60' ? 'bg-orange-100 text-orange-600' :
                                            'bg-danger/10 text-danger'}`}>
                                {invoice.agingBucket} ({invoice.agingDays} hari)
                            </p>
                        </div>
                        <div>
                            <p className="text-sm text-gray-500">Total PPN</p>
                            <p className="font-semibold font-mono text-lg">{FormatNumberRounded(invoice.ppnAmount || 0)}</p>
                        </div>
                        {invoice.paymentBatchNumber && (
                            <div>
                                <p className="text-sm text-gray-500">Payment Batch</p>
                                <button
                                    className="font-semibold text-primary hover:underline cursor-pointer"
                                    onClick={() => navigate(`/account-payable/pembayaran/batch/${encodeURIComponent(invoice.paymentBatchNumber)}`)}
                                >
                                    {invoice.paymentBatchNumber}
                                </button>
                            </div>
                        )}
                    </div>
                </CardContent>
            </Card>

            {/* Payment Form (for Unpaid) or Payment Info (for Paid).
                Lunas lewat "Mark as Paid" di halaman Purchase Order punya panel sendiri:
                tidak ada no. referensi, rekening bank, maupun jurnal -- yang ada hanya
                catatan, siapa yang menandai, dan kapan. */}
            {isPaid && invoice.manualPaid ? (
                <Card className="border-info/30 bg-info/5">
                    <CardHeader className="border-b border-info/20">
                        <CardTitle className="flex items-center gap-2 text-lg text-info">
                            <CheckCircle2 className="w-5 h-5" />
                            Ditandai Lunas Manual (Mark as Paid)
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="p-6 space-y-6">
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <div>
                                <p className="text-sm text-gray-500">Tanggal Ditandai</p>
                                <p className="font-semibold">{invoice.manualPaidAt ? FormatDate(invoice.manualPaidAt) : '-'}</p>
                            </div>
                            <div>
                                <p className="text-sm text-gray-500">Ditandai Oleh</p>
                                <p className="font-semibold">{invoice.manualPaidByName || '-'}</p>
                            </div>
                            <div>
                                <p className="text-sm text-gray-500">No. PO</p>
                                <p className="font-semibold">{invoice.poNumber || '-'}</p>
                            </div>
                            <div>
                                <p className="text-sm text-gray-500">Nilai Dilunasi</p>
                                <p className="font-semibold font-mono text-lg text-info">{FormatNumberRounded(invoice.paidAmount || 0)}</p>
                            </div>
                        </div>
                        <div>
                            <p className="text-sm text-gray-500">Catatan</p>
                            <p className="font-medium whitespace-pre-wrap">{invoice.manualPaidNote || '-'}</p>
                        </div>
                        <div className="rounded-md border border-info/30 bg-background px-4 py-3 text-sm text-muted-foreground">
                            Faktur ini dilunasi lewat <span className="font-semibold">Mark as Paid</span> di halaman Purchase
                            Order, bukan lewat menu Pembayaran. Tidak ada transaksi kas/bank dan tidak ada jurnal yang
                            diposting, sehingga saldo Utang Supplier di buku besar tidak ikut berkurang. Pembatalan
                            dilakukan dari halaman Purchase Order lewat filter &quot;Sudah Ditandai Paid&quot;.
                        </div>
                    </CardContent>
                </Card>
            ) : isPaid ? (
                <Card>
                    <CardHeader className="border-b">
                        <CardTitle className="flex items-center gap-2 text-lg text-success">
                            <CheckCircle2 className="w-5 h-5" />
                            Informasi Pembayaran
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="p-6">
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <div>
                                <p className="text-sm text-gray-500">Tanggal Bayar</p>
                                <p className="font-semibold">{invoice.paymentDate ? FormatDate(invoice.paymentDate) : '-'}</p>
                            </div>
                            <div>
                                <p className="text-sm text-gray-500">No. Referensi</p>
                                <p className="font-semibold">{invoice.paymentRef || '-'}</p>
                            </div>
                            <div>
                                <p className="text-sm text-gray-500">Rekening Bank</p>
                                <p className="font-semibold">{invoice.paymentCoaDesc || invoice.paymentCoaCode || '-'}</p>
                            </div>
                            <div>
                                <p className="text-sm text-gray-500">Total Dibayar</p>
                                <p className="font-semibold font-mono text-lg text-success">{FormatNumberRounded(invoice.paidAmount || 0)}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            ) : (
                <Card className="border-primary/20 bg-primary/5">
                    <CardHeader className="border-b border-primary/10">
                        <CardTitle className="flex items-center gap-2 text-lg text-primary">
                            <CreditCard className="w-5 h-5" />
                            Form Pembayaran
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="p-6 space-y-6">
                        {/* Row 1: Basic Payment Info */}
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div>
                                <label className="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
                                    <Calendar className="w-4 h-4" />
                                    Tanggal Bayar
                                </label>
                                <Flatpickr
                                    options={{ dateFormat: 'd/m/Y', position: isRtl ? 'auto right' : 'auto left' }}
                                    className="flex h-10 w-full items-center justify-between rounded-md border border-input bg-white px-3 py-2 text-sm"
                                    value={paymentDate}
                                    onChange={(dates: Date[]) => dates[0] && setPaymentDate(dates[0])}
                                />
                            </div>
                            <div>
                                <label className="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
                                    <Banknote className="w-4 h-4" />
                                    Rekening Bank *
                                </label>
                                <SelectSearch
                                    placeholder="Pilih Bank"
                                    options={bankOptions}
                                    isLoading={isLoadingBank}
                                    onInputChange={(v) => setSearchBank(v)}
                                    onChange={(opt: any) => setSelectedBank(opt)}
                                    value={selectedBank}
                                    styles={selectStyles}
                                    menuPortalTarget={document.body}
                                />
                            </div>
                            <div>
                                <label className="text-sm font-medium text-gray-700 mb-2 block">
                                    No. Referensi
                                </label>
                                <input
                                    type="text"
                                    className="flex h-10 w-full items-center justify-between rounded-md border border-input bg-white px-3 py-2 text-sm"
                                    placeholder="No. Giro/Transfer"
                                    value={paymentRef}
                                    onChange={(e) => setPaymentRef(e.target.value)}
                                />
                            </div>

                            {/* Amount To Pay moved to bottom */}
                        </div>

                        {/* Row 2: Discount */}
                        <div className="border-t pt-4">
                            <h4 className="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-3">
                                <Percent className="w-4 h-4 text-success" />
                                Diskon
                            </h4>
                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div>
                                    <label className="text-sm font-medium text-gray-700 mb-2 block">
                                        Diskon (%)
                                    </label>
                                    <input
                                        type="number"
                                        min="0"
                                        max="100"
                                        step="0.1"
                                        className="flex h-10 w-full items-center justify-between rounded-md border border-input bg-white px-3 py-2 text-sm"
                                        placeholder="0"
                                        value={discountPercent || ''}
                                        onChange={(e) => {
                                            setDiscountMode('percent');
                                            setDiscountPercent(Number(e.target.value) || 0);
                                        }}
                                        disabled={isLocked}
                                        title={isLocked ? "Terkunci: Pembayaran parsial telah dilakukan" : ""}
                                    />
                                </div>
                                <div>
                                    <label className="text-sm font-medium text-gray-700 mb-2 block">
                                        Diskon (Rp)
                                    </label>
                                    <input
                                        type="number"
                                        min="0"
                                        step="1000"
                                        className="flex h-10 w-full items-center justify-between rounded-md border border-input bg-white px-3 py-2 text-sm"
                                        placeholder="0"
                                        value={discountAmount || ''}
                                        onChange={(e) => {
                                            setDiscountMode('amount');
                                            setDiscountAmount(Number(e.target.value) || 0);
                                        }}
                                        disabled={isLocked}
                                        title={isLocked ? "Terkunci: Pembayaran parsial telah dilakukan" : ""}
                                    />
                                </div>
                                <div className="flex items-end">
                                    <div className="bg-success/10 text-success px-4 py-2 rounded-md text-sm font-mono w-full text-right">
                                        -{FormatNumberRounded(calculatedDiscountAmount)}
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Row 3: PPH23 */}
                        <div className="border-t pt-4">
                            <div className="flex items-center gap-3 mb-3">
                                <h4 className="flex items-center gap-2 text-sm font-semibold text-gray-700">
                                    <Calculator className="w-4 h-4 text-blue-600" />
                                    PPh 23
                                </h4>
                                <label className="flex items-center gap-2 cursor-pointer">
                                    <input
                                        type="checkbox"
                                        checked={includePph23}
                                        onChange={(e) => setIncludePph23(e.target.checked)}
                                        className="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary"
                                        disabled={isLocked}
                                    />
                                    <span className="text-sm text-gray-600">Potong PPh 23</span>
                                    {isLocked && <span className="text-xs text-warning ml-2">(Terkunci)</span>}
                                </label>
                            </div>

                            {includePph23 && (
                                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <div>
                                        <label className="text-sm font-medium text-gray-700 mb-2 block">
                                            PPh 23 (%)
                                        </label>
                                        <input
                                            type="number"
                                            min="0"
                                            max="100"
                                            step="0.1"
                                            className="flex h-10 w-full items-center justify-between rounded-md border border-input bg-white px-3 py-2 text-sm"
                                            placeholder="2"
                                            value={pph23Percent || ''}
                                            onChange={(e) => {
                                                setPph23Mode('percent');
                                                setPph23Percent(Number(e.target.value) || 0);
                                            }}
                                            disabled={isLocked}
                                        />
                                        <p className="text-xs text-gray-500 mt-1">Umumnya 2% (dengan NPWP)</p>
                                        {isLocked && <p className="text-xs text-warning">Locked by Previous Payment</p>}
                                    </div>
                                    <div>
                                        <label className="text-sm font-medium text-gray-700 mb-2 block">
                                            PPh 23 (Rp)
                                        </label>
                                        <input
                                            type="number"
                                            min="0"
                                            step="1000"
                                            className="flex h-10 w-full items-center justify-between rounded-md border border-input bg-white px-3 py-2 text-sm"
                                            placeholder="0"
                                            value={pph23Amount || ''}
                                            onChange={(e) => {
                                                setPph23Mode('amount');
                                                setPph23Amount(Number(e.target.value) || 0);
                                            }}
                                            disabled={isLocked}
                                        />
                                    </div>
                                    <div className="flex items-end">
                                        <div className="bg-blue-50 text-blue-600 px-4 py-2 rounded-md text-sm font-mono w-full text-right">
                                            -{FormatNumberRounded(calculatedPph23Amount)}
                                        </div>
                                    </div>
                                </div>
                            )}
                        </div>

                        {/* Row 4: Total Payment Input */}
                        <div className="border-t pt-4">
                            <label className="text-sm font-medium text-gray-700 mb-2 block">
                                {t('ap.payment.payment_form.total_pay') || 'Nominal Bayar (Rp)'}
                            </label>
                            <div className="relative">
                                <input
                                    type="number"
                                    className="flex h-10 w-full items-center justify-between rounded-md border border-input bg-white px-3 py-2 text-sm font-bold text-primary"
                                    value={amountToPay || ''}
                                    onChange={(e) => {
                                        const val = Number(e.target.value);
                                        setAmountToPay(val);
                                        setIsManualAmount(true);
                                    }}
                                    max={maxPayableAmount}
                                />
                                {maxPayableAmount > 0 && amountToPay > maxPayableAmount && (
                                    <p className="text-xs text-danger mt-1">
                                        Maksimal pembayaran: {FormatNumberRounded(maxPayableAmount)}
                                    </p>
                                )}
                            </div>
                        </div>
                    </CardContent>
                </Card>
            )}

            {/* Summary Section */}
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {/* Payment Summary */}
                <Card className="mt-6 lg:mt-0">
                    <CardHeader className="border-b">
                        <CardTitle className="flex items-center gap-2 text-lg">
                            <Calculator className="w-5 h-5 text-primary" />
                            Ringkasan Pembayaran
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="p-6">
                        <div className="space-y-3">
                            <div className="flex justify-between items-center">
                                <span className="text-gray-600">Subtotal</span>
                                <span className="font-mono">{FormatNumberRounded(totalTransaction)}</span>
                            </div>
                            <div className="flex justify-between items-center text-success">
                                <span className="flex items-center gap-1">
                                    <Percent className="w-4 h-4" />
                                    Diskon ({isPaid ? (invoice.discountPercent || 0) : discountPercent.toFixed(2)}%)
                                </span>
                                <span className="font-mono">-{FormatNumberRounded(isPaid ? (invoice.discountAmount || 0) : calculatedDiscountAmount)}</span>
                            </div>
                            {(invoice.ppnAmount || 0) > 0 && (
                                <div className="flex justify-between items-center text-blue-500">
                                    <span>Total PPN</span>
                                    <span className="font-mono">{FormatNumberRounded(invoice.ppnAmount || 0)}</span>
                                </div>
                            )}
                            <hr />
                            <div className="flex justify-between items-center font-semibold">
                                <span>Total Setelah Diskon (DPP)</span>
                                <span className="font-mono">{FormatNumberRounded(isPaid ? (invoice.totalAfterDiscount || totalTransaction) : totalAfterDiscount)}</span>
                            </div>
                            {(includePph23 || (isPaid && invoice.pph23Amount > 0)) && (
                                <div className="flex justify-between items-center text-blue-600">
                                    <span className="flex items-center gap-1">
                                        <Calculator className="w-4 h-4" />
                                        PPh 23 ({isPaid ? (invoice.pph23Percent || 0) : pph23Percent.toFixed(2)}%)
                                    </span>
                                    <span className="font-mono">-{FormatNumberRounded(isPaid ? (invoice.pph23Amount || 0) : calculatedPph23Amount)}</span>
                                </div>
                            )}
                            {totalDP > 0 && (
                                <div className="flex justify-between items-center text-warning">
                                    <span>Total DP</span>
                                    <span className="font-mono">-{FormatNumberRounded(totalDP)}</span>
                                </div>
                            )}
                            <hr />
                            <div className="flex justify-between items-center font-bold text-lg">
                                <span>Total Tagihan (Sisa)</span>
                                <span className="font-mono text-primary">{FormatNumberRounded(maxPayableAmount)}</span>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                {/* DP List */}
                <div className="space-y-6">
                    {/* DP List */}
                    <Card>
                        <CardHeader className="border-b">
                            <CardTitle className="flex items-center gap-2 text-lg">
                                <CreditCard className="w-5 h-5 text-warning" />
                                {t('ap.payment.dp_list') || 'Riwayat Down Payment'}
                            </CardTitle>
                        </CardHeader>
                        <CardContent className="p-0">
                            {dpList.length === 0 ? (
                                <div className="p-6 text-center text-gray-500">
                                    {t('ap.payment.no_dp') || 'Belum ada Down Payment'}
                                </div>
                            ) : (
                                <div className="divide-y">
                                    {dpList.map((dp) => (
                                        <div key={dp.dpId} className="p-4 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                            <div className="flex justify-between items-start">
                                                <div>
                                                    <p className="font-medium">{dp.note}</p>
                                                    <p className="text-sm text-gray-500">{dp.paymentMethod}</p>
                                                    <p className="text-xs text-gray-400 flex items-center gap-1 mt-1">
                                                        <Calendar className="w-3 h-3" />
                                                        {FormatDate(dp.dpDate)}
                                                    </p>
                                                </div>
                                                <span className="font-mono font-semibold text-warning">
                                                    {FormatNumberRounded(dp.amount)}
                                                </span>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            )}
                            <div className="p-4 bg-gray-50 dark:bg-gray-800 border-t">
                                <div className="flex justify-between items-center font-bold">
                                    <span>{t('ap.payment.total_dp') || 'Total DP'}</span>
                                    <span className="font-mono text-warning">{FormatNumberRounded(totalDP)}</span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Payment History List from Partial Payment */}
                    <Card>
                        <CardHeader className="border-b">
                            <CardTitle className="flex items-center gap-2 text-lg">
                                <Receipt className="w-5 h-5 text-blue-500" />
                                {t('ap.payment.history_list') || 'Riwayat Pembayaran'}
                            </CardTitle>
                        </CardHeader>
                        <CardContent className="p-0">
                            {paymentHistory.length === 0 ? (
                                <div className="p-6 text-center text-gray-500">
                                    {t('ap.payment.no_history') || 'Belum ada Pembayaran Partial'}
                                </div>
                            ) : (
                                <div className="divide-y">
                                    {paymentHistory.map((hist, idx) => (
                                        <div key={idx} className="p-4 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                            <div className="flex justify-between items-start">
                                                <div>
                                                    <p className="font-medium">{hist.note || 'Pembayaran Partial'}</p>
                                                    <p className="text-sm text-gray-500">{hist.coaName || hist.coaCode}</p>
                                                    <p className="text-xs text-gray-400 flex items-center gap-1 mt-1">
                                                        <Calendar className="w-3 h-3" />
                                                        {FormatDate(hist.paymentDate)}
                                                    </p>
                                                </div>
                                                <span className="font-mono font-semibold text-success">
                                                    {FormatNumberRounded(hist.amount)}
                                                </span>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            )}
                            <div className="p-4 bg-gray-50 dark:bg-gray-800 border-t">
                                <div className="flex justify-between items-center font-bold">
                                    <span>Total Dibayar</span>
                                    <span className="font-mono text-success">{FormatNumberRounded(invoice.paidAmount || 0)}</span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>

            {/* Grand Total / Action */}
            {!isPaid && (
                <Card className="bg-gradient-to-r from-primary to-primary/80 text-white">
                    <CardContent className="p-6">
                        <div className="flex justify-between items-center">
                            <div>
                                <p className="text-white/80 text-sm">Total Pembayaran Saat Ini</p>
                            </div>
                            <div className="flex items-center gap-6">
                                <p className="text-3xl font-bold font-mono">{FormatNumberRounded(amountToPay)}</p>
                                <Button
                                    onClick={() => setShowPaymentModal(true)}
                                    disabled={!selectedBank || isProcessingPayment}
                                    className="bg-white text-primary hover:bg-gray-100 gap-2"
                                >
                                    <CheckCircle2 className="w-4 h-4" />
                                    {isProcessingPayment ? 'Processing...' : 'Proses Pembayaran'}
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            )}

            {isPaid && (
                <Card className="bg-gradient-to-r from-success to-success/80 text-white">
                    <CardContent className="p-6">
                        <div className="flex justify-between items-center">
                            <div>
                                <p className="text-white/80 text-sm">Total Dibayar</p>
                            </div>
                            <div className="text-right">
                                <p className="text-3xl font-bold font-mono">{FormatNumberRounded(invoice.paidAmount || 0)}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            )}

            <ConfirmationModal
                confirmText="Konfirmasi Bayar"
                message={
                    <div className="text-left space-y-2">
                        <p>Anda akan membayar invoice <strong>{invoice?.invoiceNumber || ''}</strong></p>
                        <div className="bg-gray-50 p-3 rounded-md text-sm space-y-1">
                            <div className="flex justify-between">
                                <span>Subtotal:</span>
                                <span className="font-mono">{FormatNumberRounded(totalTransaction)}</span>
                            </div>
                            <div className="flex justify-between text-success">
                                <span>Diskon:</span>
                                <span className="font-mono">-{FormatNumberRounded(calculatedDiscountAmount)}</span>
                            </div>
                            {includePph23 && (
                                <div className="flex justify-between text-blue-600">
                                    <span>PPh 23:</span>
                                    <span className="font-mono">-{FormatNumberRounded(calculatedPph23Amount)}</span>
                                </div>
                            )}
                            {totalDP > 0 && (
                                <div className="flex justify-between text-warning">
                                    <span>DP:</span>
                                    <span className="font-mono">-{FormatNumberRounded(totalDP)}</span>
                                </div>
                            )}
                            <hr />
                            <div className="flex justify-between font-bold">
                                <span>Total Bayar:</span>
                                <span className="font-mono">{FormatNumberRounded(amountToPay)}</span>
                            </div>
                        </div>
                        <p className="text-gray-500">Lanjutkan?</p>
                    </div>
                }
                isOpen={showPaymentModal}
                onClose={() => !isProcessingPayment && setShowPaymentModal(false)}
                onConfirm={handlePayment}
            />
        </div>
    );
};

export default PembayaranAPDetail;
