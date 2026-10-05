import { useEffect, useMemo, useState } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { useParams, useNavigate } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '@/store/themeConfigSlice';
import SelectSearch from 'react-select';
import Flatpickr from 'react-flatpickr';
import 'flatpickr/dist/flatpickr.css';
import {
    useGetAccountPayableByIdQuery,
    useProcessAccountPayablePaymentMutation,
    useCancelInvoicePaymentMutation,
} from '@/store/api/accountPayable/accountPayableApiSlice';
import { useGetOptionBankQuery } from '@/store/api/bank/bankApiSlice';
import { useRoleAccess } from '@/hooks/useRoleAccess';
import TimelineButton from '@/components/ActivityTimeline/TimelineButton';
import { AUDIT_MODULE } from '@/components/ActivityTimeline/modules';
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
    Ban,
} from 'lucide-react';

/**
 * Halaman pembayaran untuk invoice NON-PO hasil "Input Manual Tukar Faktur".
 *
 * Dipisah dari detail.tsx (jalur PO) HANYA karena rumus tagihannya berbeda dan tidak
 * boleh saling mengubah:
 *
 *   Non-PO (halaman ini) : tagihan = (nilai barang - diskon) + PPN + PPh23
 *   PO     (detail.tsx)  : tagihan = (nilai barang - diskon) - PPh23,
 *                          PPN hanya informasi, basis PPh23 = DPP - PPN
 *
 * Tata letak, warna, dan alur tombolnya sengaja dibuat sama persis dengan halaman PO
 * supaya tidak terasa seperti halaman lain bagi user.
 *
 * Diskon, PPN, dan PPh23 di sini READ-ONLY: nilainya ditetapkan saat invoice dibuat
 * lewat form Input Manual, jadi angka di layar selalu sama dengan yang tersimpan.
 */
const PembayaranAPManualDetail = () => {
    const dispatch = useDispatch();
    const navigate = useNavigate();
    const { t } = useTranslation();
    const isRtl = useSelector((state: any) => state.themeConfig.rtlClass) === 'rtl';
    const { paymentId } = useParams<{ paymentId: string }>();

    const { data: invoiceData, isLoading: isLoadingInvoice, refetch } = useGetAccountPayableByIdQuery(
        Number(paymentId),
        { skip: !paymentId, refetchOnMountOrArgChange: true }
    );
    const { data: bankList, isLoading: isLoadingBank } = useGetOptionBankQuery(
        { search: '', page: -1, pageSize: -1 }, { skip: false }
    );
    const [processPayment, { isLoading: isProcessingPayment }] = useProcessAccountPayablePaymentMutation();

    const invoice = invoiceData?.data;
    const isPaid = invoice?.paymentStatus === 'Paid';

    const { canCancelPayment } = useRoleAccess();
    const [cancelPayment, { isLoading: cancellingPayment }] = useCancelInvoicePaymentMutation();
    const [cancelOpen, setCancelOpen] = useState(false);
    const [cancelReason, setCancelReason] = useState('');

    const [selectedBank, setSelectedBank] = useState<any>(null);
    const [paymentDate, setPaymentDate] = useState<Date>(new Date());
    const [paymentRef, setPaymentRef] = useState<string>('');
    const [amountToPay, setAmountToPay] = useState<number>(0);
    const [isManualAmount, setIsManualAmount] = useState<boolean>(false);
    const [showPaymentModal, setShowPaymentModal] = useState(false);

    useEffect(() => {
        dispatch(setPageTitle('Detail Pembayaran'));
        dispatch(setTitle('Detail Pembayaran'));
        dispatch(setBreadcrumbTitle(['Dashboard', 'Account Payable', 'Pembayaran', 'Detail']));
    }, [dispatch]);

    // Halaman ini khusus non-PO. Invoice ber-PO dikembalikan ke halaman lamanya supaya
    // rumusnya tidak tertukar - termasuk bila URL diketik atau di-bookmark manual.
    useEffect(() => {
        if (invoice?.poNumber) {
            navigate(`/account-payable/pembayaran/${paymentId}`, { replace: true });
        }
    }, [invoice, paymentId, navigate]);

    const totalTransaction = Number(invoice?.totalTransaction || 0);
    const discountAmount = Number(invoice?.discountAmount || 0);
    const discountPercent = Number(invoice?.discountPercent || 0);
    const ppnAmount = Number(invoice?.ppnAmount || 0);
    const ppnPercent = Number(invoice?.ppnPercent || 0);
    const pph23Amount = Number(invoice?.pph23Amount || 0);
    const pph23Percent = Number(invoice?.pph23Percent || 0);
    const paidAmount = Number(invoice?.paidAmount || 0);

    const totalAfterDiscount = Math.max(totalTransaction - discountAmount, 0);
    const totalTagihan = Math.max(totalAfterDiscount + ppnAmount + pph23Amount, 0);
    const maxPayableAmount = Math.max(totalTagihan - paidAmount, 0);

    useEffect(() => {
        if (!isManualAmount) setAmountToPay(maxPayableAmount);
    }, [maxPayableAmount, isManualAmount]);

    // Pemetaan disamakan persis dengan halaman PO supaya daftar rekening yang tampil dan
    // nilai yang dikirim ke backend identik di kedua halaman.
    const bankOptions = useMemo(() => {
        if (!bankList) return [];
        const items: any[] = Array.isArray(bankList) ? bankList : bankList.data || [];
        return items
            .map((b: any) => ({
                label: b.label || b.bankName || '',
                value: b.value || b.coaCode || b.desc || '',
                desc: b.desc || b.coaCode || '',
            }))
            .filter((i: any) => i.label && i.value);
    }, [bankList]);

    const selectStyles = {
        control: (base: any) => ({ ...base, minHeight: '40px', borderColor: '#e2e8f0' }),
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
                // Dikirim apa adanya dari invoice. Backend menghitung selisih terhadap
                // nilai lama, jadi selisihnya 0 dan jurnal pembayaran tidak menggandakan
                // diskon/PPh23 yang sudah melekat pada invoice.
                discountAmount: discountAmount,
                discountPercent: discountPercent,
                pph23Amount: pph23Amount,
                pph23Percent: pph23Percent,
            }).unwrap();

            toastMessage('Pembayaran berhasil diproses', 'success');
            setShowPaymentModal(false);
            refetch();
        } catch (error: any) {
            toastMessage(error?.data?.message || 'Gagal memproses pembayaran', 'error');
        }
    };

    const handleCancelPayment = async () => {
        try {
            const res: any = await cancelPayment({ invoiceExchangeId: paymentId as string, reason: cancelReason }).unwrap();
            const noJurnal = res?.data?.cancelJournalNo ?? res?.cancelJournalNo ?? '-';
            toastMessage(`Pembayaran dibatalkan. Jurnal pembalik: ${noJurnal}`, 'success');
            setCancelOpen(false);
            setCancelReason('');
            refetch();
        } catch (err: any) {
            toastMessage(err?.data?.message || 'Gagal membatalkan pembayaran', 'error');
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
                <div className="text-center text-gray-500">Invoice tidak ditemukan</div>
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
                    {canCancelPayment && paidAmount > 0 && (
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

            {/* Invoice Info */}
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
                            <p className="font-semibold font-mono text-lg">{FormatNumberRounded(ppnAmount)}</p>
                        </div>
                        {paidAmount > 0 && (
                            <div>
                                <p className="text-sm text-gray-500">Sudah Dibayar</p>
                                <p className="font-semibold font-mono text-lg text-success">{FormatNumberRounded(paidAmount)}</p>
                            </div>
                        )}
                    </div>
                </CardContent>
            </Card>

            {/* Payment Form */}
            {!isPaid && (
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
                        </div>

                        {/* Row 2: Diskon & Pajak - nilainya sudah ditetapkan saat invoice dibuat */}
                        <div className="border-t pt-4">
                            <h4 className="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-3">
                                <Percent className="w-4 h-4 text-success" />
                                Diskon &amp; Pajak
                            </h4>
                            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label className="text-sm font-medium text-gray-700 mb-2 block">
                                        Diskon ({discountPercent.toFixed(2)}%)
                                    </label>
                                    <div className="flex h-10 w-full items-center justify-end rounded-md border border-input bg-gray-50 px-3 py-2 text-sm font-mono text-success">
                                        -{FormatNumberRounded(discountAmount)}
                                    </div>
                                </div>
                                <div>
                                    <label className="text-sm font-medium text-gray-700 mb-2 block">
                                        PPN ({ppnPercent.toFixed(2)}%)
                                    </label>
                                    <div className="flex h-10 w-full items-center justify-end rounded-md border border-input bg-gray-50 px-3 py-2 text-sm font-mono text-blue-600">
                                        +{FormatNumberRounded(ppnAmount)}
                                    </div>
                                </div>
                                <div>
                                    <label className="text-sm font-medium text-gray-700 mb-2 block">
                                        PPh 23 ({pph23Percent.toFixed(2)}%)
                                    </label>
                                    <div className="flex h-10 w-full items-center justify-end rounded-md border border-input bg-gray-50 px-3 py-2 text-sm font-mono text-blue-600">
                                        +{FormatNumberRounded(pph23Amount)}
                                    </div>
                                </div>
                            </div>
                            <p className="text-xs text-gray-500 mt-2">
                                Nilai diambil dari data invoice dan tidak diubah di halaman ini.
                            </p>
                        </div>

                        {/* Row 3: Total Payment Input */}
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
                                        setAmountToPay(Number(e.target.value));
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

            {/* Summary */}
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
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
                                    Diskon ({discountPercent.toFixed(2)}%)
                                </span>
                                <span className="font-mono">-{FormatNumberRounded(discountAmount)}</span>
                            </div>
                            <hr />
                            <div className="flex justify-between items-center font-semibold">
                                <span>Total Setelah Diskon (DPP)</span>
                                <span className="font-mono">{FormatNumberRounded(totalAfterDiscount)}</span>
                            </div>
                            {ppnAmount > 0 && (
                                <div className="flex justify-between items-center text-blue-500">
                                    <span>PPN ({ppnPercent.toFixed(2)}%)</span>
                                    <span className="font-mono">+{FormatNumberRounded(ppnAmount)}</span>
                                </div>
                            )}
                            {pph23Amount > 0 && (
                                <div className="flex justify-between items-center text-blue-600">
                                    <span className="flex items-center gap-1">
                                        <Calculator className="w-4 h-4" />
                                        PPh 23 ({pph23Percent.toFixed(2)}%)
                                    </span>
                                    <span className="font-mono">+{FormatNumberRounded(pph23Amount)}</span>
                                </div>
                            )}
                            {paidAmount > 0 && (
                                <div className="flex justify-between items-center text-gray-500">
                                    <span>Sudah Dibayar</span>
                                    <span className="font-mono">-{FormatNumberRounded(paidAmount)}</span>
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

                {/* Riwayat Pembayaran */}
                <Card className="mt-6 lg:mt-0">
                    <CardHeader className="border-b">
                        <CardTitle className="flex items-center gap-2 text-lg">
                            <CreditCard className="w-5 h-5 text-success" />
                            Riwayat Pembayaran
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="p-0">
                        {(invoice.paymentHistory?.length || 0) === 0 ? (
                            <div className="p-6 text-center text-sm text-gray-500">Belum ada pembayaran</div>
                        ) : (
                            <>
                                {invoice.paymentHistory.map((hist: any, i: number) => (
                                    <div key={i} className="flex items-center justify-between px-6 py-3 border-b last:border-0">
                                        <div>
                                            <p className="font-semibold text-sm">{hist.paymentRef || '-'}</p>
                                            <p className="text-sm text-gray-500">{hist.coaName || hist.coaCode}</p>
                                            <p className="text-xs text-gray-400">
                                                {hist.paymentDate ? FormatDate(hist.paymentDate) : '-'}
                                            </p>
                                        </div>
                                        <span className="font-mono font-semibold">{FormatNumberRounded(hist.amount || 0)}</span>
                                    </div>
                                ))}
                                <div className="p-4 bg-gray-50 dark:bg-gray-800 border-t">
                                    <div className="flex justify-between items-center font-bold">
                                        <span>Total Dibayar</span>
                                        <span className="font-mono text-success">{FormatNumberRounded(paidAmount)}</span>
                                    </div>
                                </div>
                            </>
                        )}
                    </CardContent>
                </Card>
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
                                <p className="text-3xl font-bold font-mono">{FormatNumberRounded(paidAmount)}</p>
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
                                <span className="font-mono">-{FormatNumberRounded(discountAmount)}</span>
                            </div>
                            {ppnAmount > 0 && (
                                <div className="flex justify-between text-blue-500">
                                    <span>PPN:</span>
                                    <span className="font-mono">+{FormatNumberRounded(ppnAmount)}</span>
                                </div>
                            )}
                            {pph23Amount > 0 && (
                                <div className="flex justify-between text-blue-600">
                                    <span>PPh 23:</span>
                                    <span className="font-mono">+{FormatNumberRounded(pph23Amount)}</span>
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
                loading={isProcessingPayment}
                keepOpenOnConfirm
            />

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
                            Jurnal pembalik akan dibuat bertanggal sama dengan pembayarannya, dan invoice kembali ke
                            status belum lunas. Data lama tidak dihapus.
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1.5">Alasan pembatalan</label>
                            <textarea
                                rows={3}
                                className="flex w-full rounded-md border border-input bg-white px-3 py-2 text-sm"
                                value={cancelReason}
                                onChange={(e) => setCancelReason(e.target.value)}
                                placeholder="Contoh: salah nominal transfer"
                            />
                        </div>
                    </div>
                }
            />
        </div>
    );
};

export default PembayaranAPManualDetail;
