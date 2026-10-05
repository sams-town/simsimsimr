import { useState, useEffect } from 'react';
import { useTranslation } from 'react-i18next';
import { useParams, useNavigate } from 'react-router-dom';
import { useDispatch, useSelector } from 'react-redux';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '@/store/themeConfigSlice';
import {
    useGetAccountReceivableVoucherListQuery,
    useGetAccountReceivableListQuery,
    useProcessSettlementMutation,
    useMarkAsPrintedAccountReceivableMutation,
    useVoidAccountReceivableMutation,
    useUnpostAccountReceivableMutation,
    useExportAccountReceivableMutation,
    useBulkRevisionAccountReceivableMutation,
    useGetAccountReceivablePrintConfigQuery,
} from '@/store/api/accountReceivable/accountReceivableApiSlice';
import { useGetOptionBankQuery } from '@/store/api/bank/bankApiSlice';
import { toastMessage } from '@/utils/toastUtils';
import Select from 'react-select';
import Flatpickr from 'react-flatpickr';
import 'flatpickr/dist/flatpickr.css';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { ArrowLeft, Wallet, Save, Printer, Download, Upload, Trash2, Ban, Percent, Receipt, Calendar, StickyNote } from 'lucide-react';
import IconCalendar from '@/components/Icon/IconCalendar';
import { DataTable } from 'mantine-datatable';
import { AlertDialog } from "@/components/ui/alert-dialog";
import { Label } from '@/components/ui/label'; // Added Label import
import ConfirmationModal from '@/components/ConfirmationModal'; // Added ConfirmationModal import
import { FormatDate } from '@/utils/formatDate';
import { FormatNumber } from '@/utils/formatNumber';

const parseNoteHistory = (historyJson?: string): any[] => {
    if (!historyJson) return [];
    try {
        const parsed = JSON.parse(historyJson);
        return Array.isArray(parsed) ? parsed : [];
    } catch {
        return [];
    }
};

const NotesCell = ({ row }: { row: { notes?: string; noteHistory?: string } }) => {
    const history = parseNoteHistory(row.noteHistory);
    if (!row.notes && history.length === 0) {
        return <span className="text-xs text-gray-300">-</span>;
    }
    return (
        <div className="flex items-start gap-1.5" title={row.notes || ''}>
            <StickyNote className="w-3.5 h-3.5 text-amber-500 mt-0.5 shrink-0" />
            <div className="flex flex-col">
                <span className="text-xs text-gray-700 whitespace-normal leading-tight line-clamp-2">{row.notes || '-'}</span>
                {history.length > 1 && (
                    <span className="text-[10px] text-gray-400">+{history.length - 1} catatan lain</span>
                )}
            </div>
        </div>
    );
};

const terbilang = (angka: number): string => {
    const huruf = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];

    if (angka < 12) return huruf[angka];
    if (angka < 20) return huruf[angka - 10] + ' Belas';
    if (angka < 100) return huruf[Math.floor(angka / 10)] + ' Puluh ' + huruf[angka % 10];
    if (angka < 200) return 'Seratus ' + terbilang(angka - 100);
    if (angka < 1000) return huruf[Math.floor(angka / 100)] + ' Ratus ' + terbilang(angka % 100);
    if (angka < 2000) return 'Seribu ' + terbilang(angka - 1000);
    if (angka < 1000000) return terbilang(Math.floor(angka / 1000)) + ' Ribu ' + terbilang(angka % 1000);
    if (angka < 1000000000) return terbilang(Math.floor(angka / 1000000)) + ' Juta ' + terbilang(angka % 1000000);
    return terbilang(Math.floor(angka / 1000000000)) + ' Miliar ' + terbilang(angka % 1000000000);
};

const SettlementARForm = () => {
    const { t } = useTranslation();
    const { voucherNumber: paramVoucherNumber } = useParams<{ voucherNumber: string }>();
    const navigate = useNavigate();
    const dispatch = useDispatch();
    const isRtl = useSelector((state: any) => state.themeConfig.rtlClass) === 'rtl';

    let decodedVoucher = paramVoucherNumber || '';
    try {
        if (!decodedVoucher.startsWith('PVR')) {
            decodedVoucher = atob(decodedVoucher);
        }
    } catch (e) {
        decodedVoucher = paramVoucherNumber || '';
    }

    const [paymentDate, setPaymentDate] = useState<Date>(new Date());
    const [selectedBank, setSelectedBank] = useState<any>(null);
    const [searchBank, setSearchBank] = useState<string>('');
    const [paymentRef, setPaymentRef] = useState<string>('');

    // Discount States (Dual Mode)
    const [discountPercent, setDiscountPercent] = useState<number>(0);
    const [discountAmount, setDiscountAmount] = useState<number>(0);
    const [discountMode, setDiscountMode] = useState<'percent' | 'amount'>('percent');

    const [amountToPay, setAmountToPay] = useState<string>(''); // Partial Payment Amount (Input String)
    const [openConfirm, setOpenConfirm] = useState(false);
    const [showConfirmModal, setShowConfirmModal] = useState(false);

    // Fetch AR items that are part of this voucher
    // We use the GetList endpoint which returns individual AR items.
    // We filter by 'vouchered' status, which in backend effectively means Posting + VoucherNumber set.
    // However, the backend GetList doesn't have a direct 'voucherNumber' filter exposed nicely in the standard params commonly used,
    // BUT looking at the backend code we just modified GetVoucherList to support voucherNumber.
    // Wait, GetList returns individual items, GetVoucherList returns grouped vouchers.
    // To show the *details* (list of items), we need GetList (individual items) filtered by VoucherNumber.
    // Let's modify the frontend API slice to support passing voucherNumber to GetList if possible, or key off existing params.
    // Actually, earlier we modified GetList in backend to support 'vouchered' but that filtered for ANY voucher number.
    // We need to filter for specific voucher number.
    // The GetVoucherList (grouped) is good for the header info, but doesn't give the items.
    // We need to call GetList (individual items) with a filter for VoucherNumber.
    // The GetList method in Service doesn't explicitly have `voucherNumber` parameter either...
    // Let's check GetList implementation again. 
    // It filters by payerId, status, etc.
    // We probably need to add `voucherNumber` param to GetList as well to be clean.

    // BUT, for now let's assume we can fetch the summary from GetVoucherList (we added voucherNumber support there)
    // AND we probably need a new hook or update GetList to support voucherNumber.
    // Let's assume we update the slice to accept additional params.

    // Let's implement fetching Summary from GetVoucherList (using our new backend param)
    // AND fetching Items from GetList (we need to add support there too, or filter client side).

    // Wait, if I cannot modify backend GetList right now easily without another step...
    // I can fetch GetList with current filters... but it doesn't support specific VoucherNumber.
    // However, GetList DOES return the VoucherNumber in the response.
    // So if I fetch "vouchered" items (PaymentStatus=Posting & VoucherNumber!=null), I will get ALL vouchered items for ALL payers if I don't filter by payer.
    // That could be huge.
    // Maybe I can filter by PayerId if I know it? I can get PayerId from the Header info first.

    // Strategy:
    // 1. Fetch Voucher Summary using GetVoucherList with voucherNumber param (we just added this support).
    // 2. Use the PayerId from that to fetch GetList with status='vouchered' and payerId=...
    // 3. Client-side filter GetList results to only show items matching the voucherNumber.

    // Wait, useGetAccountReceivableVoucherListQuery in slice needs to be updated to accept voucherNumber?
    // It takes `params`. 

    // Let's try to update backend GetList to support voucherNumber as well, it's safer.
    // But I'll do that in next step. For now let's implement the UI assuming we have data.

    const { data: voucherData, isLoading: isLoadingVoucher } = useGetAccountReceivableVoucherListQuery({
        voucherNumber: decodedVoucher
    });

    const voucher = voucherData?.data?.data?.[0]; // Expecting one result since we filtered by unique voucher number

    // We also need the details (individual items)
    // For now, let's just display the Header info and Payment form until we fix GetList.
    // Actually the user ASKED for details ("ada rinciannya").
    // So I MUST display the list of items.

    // Let's assume we'll use useGetAccountReceivableListQuery with a generic filter and client-side filter for now
    // until we optimize.
    // But we need PayerId for that to be efficient.

    // We also need the details (individual items)
    // Now we use the backend's new ability to filter by voucherNumber directly.
    const { data: detailData, isLoading: isLoadingList, refetch } = useGetAccountReceivableListQuery({
        voucherNumber: decodedVoucher,
        page: 1,
        pageSize: 1000 // Get all items for this voucher
    }, {
        skip: !decodedVoucher
    });

    const detailItems = detailData?.data?.data || [];
    const items = detailItems; // Alias for clarity with the instruction

    useEffect(() => {
        if (voucher) {
            dispatch(setPageTitle(t('account_receivable.settlement_form.page_title', { voucherNumber: voucher?.voucherNumber })));
            dispatch(setTitle(t('account_receivable.settlement_form.page_title', { voucherNumber: voucher?.voucherNumber })));
            dispatch(setBreadcrumbTitle([t('menu.dashboard'), t('account_receivable.title'), t('account_receivable.settlement_form.breadcrumb_settlement'), voucher?.voucherNumber]));

            // Initialize Amount to Pay with Outstanding Amount (or Total if none)
            const outstanding = voucher?.outstandingAmount !== undefined ? voucher?.outstandingAmount : ((voucher?.totalAmount || 0) - (voucher?.paidAmount || 0) - (voucher?.discountAmount || 0));
            setAmountToPay(outstanding > 0 ? outstanding.toString() : '0');
        }
    }, [dispatch, voucher, t]);

    // Sync discount amount when percent changes
    useEffect(() => {
        const isDiscountLocked = (voucher?.paidAmount || 0) > 0;
        if (discountMode === 'percent' && !isDiscountLocked) {
            const baseAmount = voucher?.outstandingAmount !== undefined ? voucher?.outstandingAmount : ((voucher?.totalAmount || 0) - (voucher?.paidAmount || 0) - (voucher?.discountAmount || 0));
            setDiscountAmount(Math.round((baseAmount * discountPercent) / 100));
        } else if (isDiscountLocked) {
            setDiscountAmount(0);
            setDiscountPercent(0);
        }
    }, [discountPercent, discountMode, voucher]);

    // Sync discount percent when amount changes
    useEffect(() => {
        const isDiscountLocked = (voucher?.paidAmount || 0) > 0;
        if (discountMode === 'amount' && !isDiscountLocked) {
            const baseAmount = voucher?.outstandingAmount !== undefined ? voucher?.outstandingAmount : ((voucher?.totalAmount || 0) - (voucher?.paidAmount || 0) - (voucher?.discountAmount || 0));
            if (baseAmount > 0) {
                setDiscountPercent((discountAmount / baseAmount) * 100);
            } else {
                setDiscountPercent(0);
            }
        }
    }, [discountAmount, discountMode, voucher]);

    const { data: bankList, isLoading: isLoadingBank } = useGetOptionBankQuery(
        { search: searchBank || '', page: -1, pageSize: -1 }, { skip: false }
    );

    const [processSettlement, { isLoading: isProcessingPayment }] = useProcessSettlementMutation();
    const [markAsPrinted] = useMarkAsPrintedAccountReceivableMutation();
    const [unpostAccountReceivable, { isLoading: isUnposting }] = useUnpostAccountReceivableMutation();
    const [voidAccountReceivable, { isLoading: isVoiding }] = useVoidAccountReceivableMutation();
    const [exportAccountReceivable, { isLoading: isExporting }] = useExportAccountReceivableMutation();
    const [bulkRevisionAccountReceivable, { isLoading: isRevising }] = useBulkRevisionAccountReceivableMutation();

    const { data: printConfigData } = useGetAccountReceivablePrintConfigQuery(undefined);
    const printConfig = {
        leftName: printConfigData?.data?.PRINT_SIGNER_LEFT_NAME || 'Naurah Nazhifah Salim',
        leftTitle: printConfigData?.data?.PRINT_SIGNER_LEFT_TITLE || 'Bagian Penagihan',
        rightName: printConfigData?.data?.PRINT_SIGNER_RIGHT_NAME || 'Herlan Mars, S.Ak',
        rightTitle: printConfigData?.data?.PRINT_SIGNER_RIGHT_TITLE || 'Manajer Keu & Akuntansi',
    };

    const isProcessing = isProcessingPayment; // Alias for clarity with the instruction

    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [showVoidModal, setShowVoidModal] = useState(false);
    const [showRevisionModal, setShowRevisionModal] = useState(false);
    const [selectedDeleteId, setSelectedDeleteId] = useState<number | null>(null);
    const [selectedVoidId, setSelectedVoidId] = useState<number | null>(null);

    const getBankOptions = () => {
        if (!bankList) return [];
        let items: any[] = Array.isArray(bankList) ? bankList : bankList.data || [];
        return items.map((b: any) => ({
            label: b.label || b.bankName || '',
            value: b.value || b.coaCode || b.desc || '', // Assuming value holds the COA Code
            desc: b.desc || b.coaCode || '',
        })).filter((i: any) => i.label && i.value);
    };

    const bankOptions = getBankOptions();
    const bankSelectOptions = bankOptions; // Alias for clarity with the instruction
    const setBankKeyword = setSearchBank; // Alias for clarity with the instruction

    const formatCurrency = (value: number) => {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
    };

    const formatDateUTC = (dateString: string | undefined) => {
        if (!dateString) return '-';
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' });
    };

    const handleConfirm = () => {
        if (!voucher || !selectedBank || !amountToPay) {
            toastMessage(t('account_receivable.settlement_form.toast.incomplete'), 'warning');
            return;
        }

        const payAmount = parseFloat(amountToPay) || 0;
        const outstanding = voucher?.outstandingAmount !== undefined ? voucher?.outstandingAmount : ((voucher?.totalAmount || 0) - (voucher?.paidAmount || 0) - (voucher?.discountAmount || 0));

        if (payAmount > outstanding + 100) { // Tolerance
            toastMessage("Amount to pay cannot exceed outstanding amount", 'warning');
            return;
        }
        if (discountPercent > 100) {
            toastMessage("Discount cannot exceed 100%", 'warning');
            return;
        }
        if (payAmount < (discountAmount || 0)) {
            toastMessage("Total settlement cannot be less than discount amount", 'warning');
            return;
        }
        setShowConfirmModal(true);
    };

    const handleSave = async () => { // Renamed from confirmSave in the instruction
        setShowConfirmModal(false);
        try {
            const payAmount = parseFloat(amountToPay) || 0;
            const history = voucher?.paymentHistory ? JSON.parse(voucher?.paymentHistory || '[]') : [];
            const isDiscountLocked = (voucher?.paidAmount > 0) || (history && history.length > 0);

            await processSettlement({
                paymentVoucher: voucher?.voucherNumber,
                coaCode: selectedBank.value,
                coaDesc: selectedBank.label,
                paidAmount: Math.max(0, payAmount - (discountAmount || 0)), // Send actual CASH amount
                discount: isDiscountLocked ? 0 : (discountAmount || 0),
                paymentDate: paymentDate.toISOString(),
                paymentRef: paymentRef,
            }).unwrap();

            toastMessage(t('account_receivable.settlement_form.toast.success', { name: voucher?.voucherNumber }), 'success');
            navigate('/account-receivable/settlement'); // Changed from navigate(-1) to specific path
        } catch (error: any) {
            toastMessage(error?.data?.message || t('account_receivable.settlement_form.toast.failed'), 'error');
        }
    };
    const handlePrint = async () => {
        // Mark as printed
        if (items && items.length > 0) {
            const ids = items.map((r: any) => r.arId);
            try {
                await markAsPrinted({ arIds: ids }).unwrap();
            } catch (e) { console.error('Failed to mark printed', e) }
        }
        window.print();
    };

    const handleExport = async () => {
        try {
            const blob = await exportAccountReceivable({
                voucherNumber: voucher?.voucherNumber,
                // We can also pass status if needed, but mainly voucherNumber defines this view
            }).unwrap();

            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', `Reimbursement_${voucher?.voucherNumber}_${new Date().toISOString().slice(0, 10)}.xlsx`);
            document.body.appendChild(link);
            link.click();
            link.parentNode?.removeChild(link);
        } catch (error) {
            console.error('Export failed', error);
            toastMessage(t('account_receivable.payment_voucher_detail.toast.export_failed', 'Export failed'), 'error');
        }
    };

    const handleUnpost = async () => {
        if (!selectedDeleteId) return;
        try {
            await unpostAccountReceivable({ arIds: [selectedDeleteId] }).unwrap();
            toastMessage(t('account_receivable.payment_voucher_detail.toast.unpost_success', 'Item unposted successfully'), 'success');
            setShowDeleteModal(false);
            setSelectedDeleteId(null);
            refetch();
        } catch (error) {
            toastMessage(t('account_receivable.payment_voucher_detail.toast.unpost_failed', 'Failed to unpost item'), 'error');
        }
    };

    const handleVoid = async () => {
        if (!selectedVoidId) return;
        try {
            await voidAccountReceivable({ arIds: [selectedVoidId] }).unwrap();
            toastMessage(t('account_receivable.payment_voucher_detail.toast.void_success', 'Item voided successfully'), 'success');
            setShowVoidModal(false);
            setSelectedVoidId(null);
            refetch();
        } catch (error) {
            toastMessage(t('account_receivable.payment_voucher_detail.toast.void_failed', 'Failed to void item'), 'error');
        }
    };

    const handleBulkRevision = async (file: File) => {
        const formData = new FormData();
        formData.append('file', file);

        try {
            await bulkRevisionAccountReceivable(formData).unwrap();
            toastMessage(t('account_receivable.payment_voucher_detail.toast.revision_success', 'Revision uploaded successfully'), 'success');
            setShowRevisionModal(false);
            refetch();
        } catch (error: any) {
            toastMessage(error?.data?.message || t('account_receivable.payment_voucher_detail.toast.revision_failed', 'Failed to upload revision'), 'error');
        }
    };

    if (isLoadingVoucher) {
        return <div className="p-6">Loading...</div>;
    }

    if (!voucher) {
        return (
            <div className="p-6">
                <div className="flex flex-col items-center justify-center min-h-[400px]">
                    <p className="text-gray-500 mb-4">{t('account_receivable.settlement_form.not_found.message')}</p>
                    <Button onClick={() => navigate(-1)}>{t('account_receivable.settlement_form.not_found.back')}</Button>
                </div>
            </div>
        );
    }

    const selectStyles = {
        control: (base: any) => ({
            ...base,
            minHeight: '46px',
            borderColor: '#e2e8f0',
            '&:hover': { borderColor: '#cbd5e1' },
            borderRadius: '0.375rem',
        }),
        menuPortal: (base: any) => ({ ...base, zIndex: 9999 }),
    };

    const isPaid = voucher?.status?.toLowerCase() === 'paid'; // Added for clarity with instruction

    // Calculate final amount for footer
    const finalAmount = (parseFloat(amountToPay) || 0) - discountAmount;

    return (
        <div className="settlement-page-wrapper space-y-6">
            <div className="mx-auto space-y-6 relative print:hidden">
                {/* Already Printed Warning */}
                {items.some((r: any) => r.isPrinted) && (
                    <div className="bg-orange-50 border-l-4 border-orange-500 p-4 rounded-r shadow-sm flex items-start gap-3">
                        <Printer className="w-5 h-5 text-orange-600 mt-0.5" />
                        <div>
                            <p className="font-bold text-orange-800 text-sm">SUDAH DICETAK / ALREADY PRINTED</p>
                            <p className="text-sm text-orange-700 mt-1">
                                Dokumen ini sudah pernah dicetak sebelumnya. Mencetak ulang akan membuat salinan baru.
                            </p>
                        </div>
                    </div>
                )}

                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                            {t('account_receivable.settlement_form.title')}
                        </h1>
                        <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            {t('account_receivable.settlement_form.subtitle')}
                        </p>
                    </div>
                    <div className="flex items-center gap-2">
                        <Button
                            type="button"
                            variant="outline"
                            onClick={() => navigate(-1)}
                            className="gap-2 shadow-sm bg-white dark:bg-black"
                            size="sm"
                        >
                            <ArrowLeft className="h-4 w-4" />
                            {t('account_receivable.settlement_form.back_btn')}
                        </Button>

                        {/* Revision Button for Draft/Posting */}
                        {!isPaid && (
                            <>
                                <Button
                                    type="button"
                                    variant="outline"
                                    onClick={handleExport}
                                    className="gap-2 shadow-sm bg-white dark:bg-black"
                                    size="sm"
                                    disabled={isExporting}
                                >
                                    <Download className="h-4 w-4" />
                                    {t('common.download', 'Download')}
                                </Button>
                                <Button
                                    type="button"
                                    variant="outline"
                                    onClick={() => setShowRevisionModal(true)}
                                    className="gap-2 shadow-sm bg-white dark:bg-black text-orange-600 border-orange-200 hover:bg-orange-50"
                                    size="sm"
                                >
                                    <Upload className="h-4 w-4" />
                                    {t('account_receivable.payment_voucher_detail.modal.revision_title', 'Upload Revision')}
                                </Button>
                            </>
                        )}

                        {/* Print Button */}
                        <Button
                            type="button"
                            variant="outline"
                            onClick={handlePrint}
                            className="gap-2 shadow-sm bg-white dark:bg-black"
                            size="sm"
                        >
                            <Printer className="h-4 w-4" />
                            {t('common.print', 'Print')}
                        </Button>
                    </div>
                </div>
            </div>

            {/* Voucher Info Card */}
            <Card className="mb-6">
                <CardHeader className="border-b bg-gradient-to-r from-primary/5 to-transparent">
                    <CardTitle className="flex items-center gap-2 text-lg">
                        <Receipt className="w-5 h-5 text-primary" />
                        {t('account_receivable.settlement_form.voucher_info.title')}
                        <span className={`ml-2 px-2 py-1 rounded-full text-xs font-semibold ${isPaid ? 'bg-green-100 text-green-600' : ((voucher?.paidAmount || 0) > 0 ? 'bg-blue-100 text-blue-600' : 'bg-orange-100 text-orange-600')}`}>
                            {isPaid ? t('account_receivable.settlement_form.voucher_info.status_paid') : ((voucher?.paidAmount || 0) > 0 ? t('account_receivable.settlement_form.voucher_info.status_partial') : t('account_receivable.settlement_form.voucher_info.status_outstanding'))}
                        </span>
                    </CardTitle>
                </CardHeader>
                <CardContent className="p-6">
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                        <div>
                            <p className="text-sm text-gray-500">{t('account_receivable.settlement_form.voucher_info.number')}</p>
                            <p className="font-semibold text-lg font-mono">{voucher?.voucherNumber}</p>
                        </div>
                        <div>
                            <p className="text-sm text-gray-500">{t('account_receivable.settlement_form.voucher_info.payer')}</p>
                            <p className="font-semibold truncate" title={voucher?.payerName}>{voucher?.payerName}</p>
                        </div>
                        <div>
                            <p className="text-sm text-gray-500">{t('account_receivable.settlement_form.voucher_info.date')}</p>
                            <p className="font-semibold flex items-center gap-1">
                                <Calendar className="w-4 h-4 text-gray-400" />
                                {voucher?.voucherDate ? FormatDate(voucher?.voucherDate).split(' ')[0] : '-'}
                            </p>
                        </div>
                        <div>
                            <p className="text-sm text-gray-500">{t('account_receivable.settlement_form.voucher_info.due_date')}</p>
                            <p className={`font-semibold flex items-center gap-1 ${voucher?.dueDate && new Date(voucher?.dueDate) < new Date() ? 'text-red-500' : ''}`}>
                                <Calendar className="w-4 h-4 text-gray-400" />
                                {voucher?.dueDate ? FormatDate(voucher?.dueDate).split(' ')[0] : '-'}
                            </p>
                        </div>
                        <div>
                            <p className="text-sm text-gray-500">{t('account_receivable.settlement_form.voucher_info.total')}</p>
                            <p className="font-semibold font-mono text-lg text-primary">{formatCurrency(voucher?.totalAmount || 0)}</p>
                        </div>
                    </div>
                </CardContent>
            </Card>

            {/* List Table */}
            < Card className="shadow-sm border border-gray-200 dark:border-gray-800" >
                <CardHeader className="bg-gray-50/50 dark:bg-[#1b2e4b] border-b border-gray-100 dark:border-gray-700 px-6 py-4">
                    <CardTitle className="text-base font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                        <span className="bg-primary/10 text-primary w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold">1</span>
                        {t('account_receivable.settlement_form.details_title')}
                    </CardTitle>
                </CardHeader>
                <CardContent className="p-0">
                    <DataTable
                        highlightOnHover
                        className="table-hover"
                        records={items}
                        columns={[
                            {
                                accessor: 'transactionDate',
                                title: t('account_receivable.settlement_form.table.date_bill'),
                                render: (row: any) => row.transactionDate ? FormatDate(row.transactionDate) : '-'
                            },
                            {
                                accessor: 'updatedDate',
                                title: t('account_receivable.settlement_form.table.date_post'),
                                render: (row: any) => row.updatedDate ? FormatDate(row.updatedDate) : '-'
                            },
                            {
                                accessor: 'invoiceInfo',
                                title: t('account_receivable.settlement_form.table.invoice_service'),
                                width: 200,
                                render: (row: any) => (
                                    <div className="flex flex-col">
                                        <span className="font-semibold text-gray-900">{row.invoiceNo || row.arNumber || '-'}</span>
                                        <span className="text-xs text-gray-500 uppercase">
                                            {row.serviceType === 'rajal' ? 'Rawat Jalan' :
                                                row.serviceType === 'ranap' ? 'Rawat Inap' : row.serviceType || '-'}
                                        </span>
                                    </div>
                                )
                            },
                            {
                                accessor: 'sepNo',
                                title: t('account_receivable.settlement_form.table.sep_no'),
                                render: (row: any) => <span className="font-mono text-xs">{row.sepNo || '-'}</span>
                            },
                            {
                                accessor: 'patientInfo',
                                title: t('account_receivable.settlement_form.table.patient_info'),
                                render: (row: any) => (
                                    <div className="flex flex-col">
                                        <span className="font-medium text-gray-900">{row.patientName || '-'}</span>
                                        <span className="text-xs text-gray-500">RM: {row.medicalRecordNo || '-'}</span>
                                    </div>
                                )
                            },
                            {
                                accessor: 'totalAmount',
                                title: t('account_receivable.settlement_form.table.total'),
                                textAlignment: 'right',
                                render: (row: any) => {
                                    const effective = row.adjustmentAmount != null && row.adjustmentAmount !== 0 && row.adjustmentAmount !== row.totalAmount
                                        ? row.adjustmentAmount : row.totalAmount;
                                    return (
                                        <div className="text-right font-medium">
                                            <div>{formatCurrency(effective)}</div>
                                            {row.adjustmentAmount != null && row.adjustmentAmount !== 0 && row.adjustmentAmount !== row.totalAmount && (
                                                <div className="text-xs text-gray-400 line-through">{formatCurrency(row.totalAmount)}</div>
                                            )}
                                        </div>
                                    );
                                }
                            },
                            {
                                accessor: 'notes',
                                title: t('account_receivable.settlement_form.table.notes', 'Catatan'),
                                width: 200,
                                render: (row: any) => <NotesCell row={row} />
                            },
                            {
                                accessor: 'action',
                                title: t('account_receivable.payment_voucher_detail.table.action', 'Action'),
                                width: 80,
                                textAlignment: 'center',
                                hidden: isPaid || (voucher?.paidAmount > 0),
                                render: (row: any) => (
                                    <div className="flex justify-center gap-2">
                                        <button
                                            type="button"
                                            onClick={() => {
                                                setSelectedDeleteId(row.arId);
                                                setShowDeleteModal(true);
                                            }}
                                            className="text-red-500 hover:text-red-700 p-1.5 rounded-md hover:bg-red-50 transition-colors"
                                            title={t('account_receivable.payment_voucher_detail.tooltip.delete', 'Unpost')}
                                        >
                                            <Trash2 className="w-4 h-4" />
                                        </button>
                                        <button
                                            type="button"
                                            onClick={() => {
                                                setSelectedVoidId(row.arId);
                                                setShowVoidModal(true);
                                            }}
                                            className="text-orange-500 hover:text-orange-700 p-1.5 rounded-md hover:bg-orange-50 transition-colors"
                                            title={t('account_receivable.payment_voucher_detail.tooltip.void', 'Void')}
                                        >
                                            <Ban className="w-4 h-4" />
                                        </button>
                                    </div>
                                )
                            }
                        ]}
                        fetching={isLoadingList}
                        minHeight={items.length === 0 ? 150 : undefined}
                        noRecordsText={t('account_receivable.settlement_form.no_items')}
                    />
                </CardContent>
            </Card >

            {/* PAID VIEW */}
            {isPaid && (
                <Card className="shadow-sm border border-gray-200 dark:border-gray-800 mt-6">
                    <CardHeader className="bg-gray-50/50 dark:bg-[#1b2e4b] border-b border-gray-100 dark:border-gray-700 px-6 py-4">
                        <div className="flex items-center justify-between">
                            <CardTitle className="text-base font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                                <span className="bg-primary/10 text-primary w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold">2</span>
                                {t('account_receivable.settlement_form.payment_title')}
                            </CardTitle>
                            <span className="bg-success/10 text-success px-3 py-1 rounded-full text-xs font-bold border border-success/20">
                                {t('account_receivable.settlement_form.status_paid')}
                            </span>
                        </div>
                    </CardHeader>
                    <CardContent className="p-6">
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div className="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-100 dark:border-gray-700">
                                <p className="text-sm text-gray-500 dark:text-gray-400 mb-1 font-medium">{t('account_receivable.settlement_form.paid_info.bank')}</p>
                                <p className="font-semibold text-gray-900 dark:text-gray-100 text-lg break-all">
                                    {voucher?.paymentCoaCode ? `[${voucher?.paymentCoaCode}] ` : ''}{voucher?.paymentCoaName || '-'}
                                </p>
                            </div>
                            <div className="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-100 dark:border-gray-700">
                                <p className="text-sm text-gray-500 dark:text-gray-400 mb-1 font-medium">{t('account_receivable.settlement_form.paid_info.date')}</p>
                                <p className="font-semibold text-gray-900 dark:text-gray-100 text-lg">
                                    {formatDateUTC(voucher?.paymentDate)}
                                </p>
                            </div>
                            <div className="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-100 dark:border-gray-700">
                                <p className="text-sm text-gray-500 dark:text-gray-400 mb-1 font-medium">{t('account_receivable.settlement_form.paid_info.ref')}</p>
                                <p className="font-semibold text-gray-900 dark:text-gray-100 text-lg font-mono">
                                    {voucher?.paymentRef || '-'}
                                </p>
                            </div>
                        </div>
                        {/* Payment Summary */}
                        <div className="mt-6 border-t pt-6 space-y-3">
                            <div className="flex justify-between items-center text-sm">
                                <span className="text-gray-500">{t('account_receivable.settlement_form.simulation.subtotal')}</span>
                                <span className="font-mono font-medium text-gray-900 dark:text-gray-100">{FormatNumber(voucher?.totalAmount || 0)}</span>
                            </div>
                            {(voucher?.discountAmount || 0) > 0 && (
                                <div className="flex justify-between items-center text-sm">
                                    <span className="text-gray-500">{t('account_receivable.settlement_form.simulation.discount', 'Diskon')}</span>
                                    <span className="font-mono font-medium text-red-500">-{FormatNumber(voucher?.discountAmount || 0)}</span>
                                </div>
                            )}
                            <div className="flex justify-between items-center text-sm">
                                <span className="text-gray-500">{t('account_receivable.settlement_form.simulation.paid')}</span>
                                <span className="font-mono font-medium text-green-600">{FormatNumber(voucher?.paidAmount || 0)}</span>
                            </div>
                            <div className="border-t pt-2 flex justify-between items-center text-sm font-bold">
                                <span className="text-gray-700">{t('account_receivable.settlement_form.simulation.outstanding_before', 'Outstanding')}</span>
                                <span className="font-mono text-gray-900">{FormatNumber(voucher?.outstandingAmount || 0)}</span>
                            </div>
                        </div>

                        {/* Payment History */}
                        {(() => {
                            let history: any[] = [];
                            try {
                                history = typeof voucher?.paymentHistory === 'string' ? JSON.parse(voucher?.paymentHistory) : (voucher?.paymentHistory || []);
                            } catch (e) { }
                            if (history && history.length > 0) {
                                return (
                                    <div className="mt-6 border-t pt-6">
                                        <h4 className="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-3">
                                            <Receipt className="w-4 h-4 text-blue-500" />
                                            {t('account_receivable.settlement_form.history_title', 'Riwayat Pembayaran')}
                                        </h4>
                                        <div className="divide-y divide-gray-100 dark:divide-gray-700 border rounded-lg">
                                            {history.map((h: any, idx: number) => (
                                                <div key={idx} className="p-3 flex justify-between items-start">
                                                    <div>
                                                        <p className="font-medium text-gray-900 text-sm">{h.Note || 'Settlement'}</p>
                                                        <p className="text-xs text-gray-500">{h.CoaName || h.CoaCode || '-'}</p>
                                                        <p className="text-xs text-gray-400 mt-0.5">{FormatDate(h.PaymentDate)}{h.PaymentRef ? ` • Ref: ${h.PaymentRef}` : ''}</p>
                                                    </div>
                                                    <span className="font-mono font-semibold text-green-600 text-sm">{FormatNumber(h.Amount)}</span>
                                                </div>
                                            ))}
                                        </div>
                                    </div>
                                );
                            }
                            return null;
                        })()}

                        <div className="mt-8 flex justify-end">
                            <Button
                                type="button"
                                variant="outline"
                                onClick={handlePrint}
                                className="h-[46px] text-base gap-2 px-6 bg-white dark:bg-black"
                            >
                                <Printer className="w-4 h-4" />
                                {t('common.print', 'Print')}
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            )}

            {/* UNPAID VIEW - NEW LAYOUT */}
            {!isPaid && (
                <div className="mt-6 space-y-6 pb-24">
                    {/* 1. Payment Form Card */}
                    <Card className="shadow-sm border border-gray-200 dark:border-gray-800">
                        <CardHeader className="bg-gray-50/50 dark:bg-[#1b2e4b] border-b border-gray-100 dark:border-gray-700 px-6 py-4">
                            <CardTitle className="flex items-center gap-2 text-base font-semibold text-gray-800 dark:text-gray-100">
                                <Wallet className="w-5 h-5 text-primary" />
                                {t('account_receivable.settlement_form.payment_form.title') || 'Form Pembayaran'}
                            </CardTitle>
                        </CardHeader>
                        <CardContent className="p-6">
                            {/* Row 1: Date, Bank, Ref */}
                            <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                                <div className="space-y-2">
                                    <Label>{t('account_receivable.settlement_form.payment_form.date_label')}</Label>
                                    <div className="relative">
                                        <Flatpickr
                                            value={paymentDate}
                                            options={{ dateFormat: 'd/m/Y', enableTime: false }}
                                            className="flex h-[46px] w-full items-center justify-between rounded-md border border-gray-200 bg-white px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/20 dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                                            onChange={(dates: Date[]) => dates[0] && setPaymentDate(dates[0])}
                                        />
                                        <div className="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                            <IconCalendar className="w-5 h-5" />
                                        </div>
                                    </div>
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="bank">{t('account_receivable.settlement_form.payment_form.bank_label')} <span className="text-red-500">*</span></Label>
                                    <Select
                                        id="bank"
                                        placeholder={t('account_receivable.settlement_form.payment_form.bank_placeholder')}
                                        options={bankSelectOptions}
                                        isLoading={isLoadingBank}
                                        onInputChange={(v) => setBankKeyword(v)}
                                        onChange={(opt: any) => setSelectedBank(opt)}
                                        value={selectedBank}
                                        styles={selectStyles}
                                        menuPortalTarget={document.body}
                                        className="text-sm"
                                    />
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="ref">{t('account_receivable.settlement_form.payment_form.ref_label')}</Label>
                                    <Input
                                        id="ref"
                                        value={paymentRef}
                                        onChange={(e) => setPaymentRef(e.target.value)}
                                        placeholder={t('account_receivable.settlement_form.payment_form.ref_placeholder')}
                                        className="h-[46px]"
                                    />
                                </div>
                            </div>

                            {/* Row 2: Discount */}
                            <div className="border-t pt-6 mb-6">
                                <h4 className="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-4">
                                    <Percent className="w-4 h-4 text-green-600" />
                                    {t('account_receivable.settlement_form.discount_form.title')}
                                </h4>
                                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <div>
                                        <Label className="text-sm font-medium text-gray-700 mb-2 block">
                                            {t('account_receivable.settlement_form.discount_form.percent')}
                                        </Label>
                                        <Input
                                            type="number"
                                            min="0"
                                            max="100"
                                            step="0.1"
                                            className={`flex h-10 w-full items-center justify-between rounded-md border border-input bg-white px-3 py-2 text-sm ${((voucher?.paidAmount || 0) > 0) ? 'bg-gray-100 cursor-not-allowed' : ''}`}
                                            placeholder="0"
                                            value={((voucher?.paidAmount || 0) > 0) ? 0 : (discountPercent || '')}
                                            disabled={(voucher?.paidAmount || 0) > 0}
                                            onChange={(e) => {
                                                setDiscountMode('percent');
                                                setDiscountPercent(Number(e.target.value) || 0);
                                            }}
                                        />
                                    </div>
                                    <div>
                                        <Label className="text-sm font-medium text-gray-700 mb-2 block">
                                            {t('account_receivable.settlement_form.discount_form.amount')}
                                        </Label>
                                        <Input
                                            type="number"
                                            min="0"
                                            step="1000"
                                            className={`flex h-10 w-full items-center justify-between rounded-md border border-input bg-white px-3 py-2 text-sm ${((voucher?.paidAmount || 0) > 0) ? 'bg-gray-100 cursor-not-allowed' : ''}`}
                                            placeholder="0"
                                            value={((voucher?.paidAmount || 0) > 0) ? 0 : (discountAmount || '')}
                                            disabled={(voucher?.paidAmount || 0) > 0}
                                            onChange={(e) => {
                                                setDiscountMode('amount');
                                                setDiscountAmount(Number(e.target.value) || 0);
                                            }}
                                        />
                                    </div>
                                    <div className="flex items-end">
                                        <div className="bg-green-50 text-green-600 px-4 py-2 rounded-md text-sm font-mono w-full text-right h-10 flex items-center justify-end">
                                            -{FormatNumber(((voucher?.paidAmount || 0) > 0) ? 0 : discountAmount)}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {/* Row 3: Amount */}
                            <div className="border-t pt-6">
                                <Label className="text-sm font-medium text-gray-700 mb-2 block">
                                    {t('account_receivable.settlement_form.payment_form.amount')}
                                </Label>
                                <div className="relative">
                                    <Input
                                        type="number"
                                        className="flex h-12 w-full items-center justify-between rounded-md border border-blue-200 bg-blue-50/30 px-4 py-2 text-lg font-bold text-primary focus:border-blue-500 transition-all shadow-sm"
                                        value={amountToPay || ''}
                                        onChange={(e) => {
                                            const val = e.target.value;
                                            setAmountToPay(val);
                                        }}
                                    />
                                    <div className="absolute right-3 top-1/2 -translate-y-1/2">
                                        <button type="button" className="text-xs font-semibold px-2 py-1 bg-blue-100 text-blue-600 rounded hover:bg-blue-200 transition-colors" onClick={() => setAmountToPay((voucher?.outstandingAmount !== undefined ? voucher?.outstandingAmount : ((voucher?.totalAmount || 0) - (voucher?.paidAmount || 0) - (voucher?.discountAmount || 0))).toString())}>
                                            {t('account_receivable.settlement_form.payment_form.pay_full')}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    {/* 2. Split Summary & History */}
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {/* Left: Summary */}
                        <Card className="shadow-sm border border-gray-200 dark:border-gray-800 h-full">
                            <CardHeader className="bg-gray-50/50 dark:bg-[#1b2e4b] border-b border-gray-100 dark:border-gray-700 px-6 py-4">
                                <CardTitle className="flex items-center gap-2 text-base font-semibold text-gray-800 dark:text-gray-100">
                                    <Wallet className="w-5 h-5 text-orange-500" />
                                    {t('account_receivable.settlement_form.simulation.title')}
                                </CardTitle>
                            </CardHeader>
                            <CardContent className="p-6">
                                <div className="space-y-4">
                                    <div className="flex justify-between items-center text-sm">
                                        <span className="text-gray-500">{t('account_receivable.settlement_form.simulation.subtotal')}</span>
                                        <span className="font-mono font-medium text-gray-900 dark:text-gray-100">{FormatNumber(voucher?.totalAmount || 0)}</span>
                                    </div>
                                    <div className="flex justify-between items-center text-sm">
                                        <span className="text-gray-500">{t('account_receivable.settlement_form.simulation.paid')}</span>
                                        <span className="font-mono font-medium text-green-600">-{FormatNumber(voucher?.paidAmount || 0)}</span>
                                    </div>
                                    <div className="border-t border-dashed my-2"></div>
                                    <div className="flex justify-between items-center text-sm">
                                        <span className="text-gray-500">{t('account_receivable.settlement_form.simulation.outstanding_before')}</span>
                                        <span className="font-mono font-bold text-gray-900 dark:text-white">
                                            {FormatNumber(voucher?.outstandingAmount !== undefined ? voucher?.outstandingAmount : ((voucher?.totalAmount || 0) - (voucher?.paidAmount || 0) - (voucher?.discountAmount || 0)))}
                                        </span>
                                    </div>

                                    <div className="bg-blue-50 dark:bg-blue-900/10 p-4 rounded-lg border border-blue-100 dark:border-blue-800 mt-4 space-y-2">
                                        <p className="text-xs font-bold uppercase text-blue-500 mb-2">{t('account_receivable.settlement_form.simulation.current_title')}</p>
                                        <div className="flex justify-between items-center text-sm">
                                            <span className="text-blue-700/70 dark:text-blue-300/70">{t('account_receivable.settlement_form.simulation.target_settlement')}</span>
                                            <span className="font-medium text-blue-700 dark:text-blue-300">{FormatNumber(parseFloat(amountToPay) || 0)}</span>
                                        </div>
                                        <div className="flex justify-between items-center text-sm">
                                            <span className="text-blue-700/70 dark:text-blue-300/70">{t('account_receivable.settlement_form.simulation.discount')} ({discountPercent.toFixed(2)}%)</span>
                                            <span className="font-medium text-red-500">- {FormatNumber(discountAmount)}</span>
                                        </div>
                                        <div className="border-t border-blue-200 dark:border-blue-700 pt-2 flex justify-between items-center">
                                            <span className="text-base font-bold text-blue-900 dark:text-white">{t('account_receivable.settlement_form.simulation.total_cash')}</span>
                                            <span className="text-xl font-bold text-primary">{FormatNumber((parseFloat(amountToPay) || 0) - discountAmount)}</span>
                                        </div>

                                        {/* Remaining after this payment */}
                                        <div className="mt-4 pt-2 border-t border-blue-200 dark:border-blue-700 text-xs flex justify-between text-gray-500">
                                            <span>{t('account_receivable.settlement_form.simulation.remaining_after')}:</span>
                                            <span>
                                                {FormatNumber(
                                                    Math.max(0,
                                                        (voucher?.outstandingAmount !== undefined ? voucher?.outstandingAmount : ((voucher?.totalAmount || 0) - (voucher?.paidAmount || 0) - (voucher?.discountAmount || 0)))
                                                        - (parseFloat(amountToPay) || 0)
                                                    )
                                                )}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        {/* Right: History - Container is managed by the loop below */}
                        {(() => {
                            let history = [];
                            try {
                                history = typeof voucher?.paymentHistory === 'string' ? JSON.parse(voucher?.paymentHistory) : voucher?.paymentHistory;
                            } catch (e) { }

                            if (history && history.length > 0) {
                                return (
                                    <Card className="shadow-sm border border-gray-200 dark:border-gray-800 h-full">
                                        <CardHeader className="bg-gray-50/50 dark:bg-[#1b2e4b] border-b border-gray-100 dark:border-gray-700 px-6 py-4">
                                            <CardTitle className="flex items-center gap-2 text-base font-semibold text-gray-800 dark:text-gray-100">
                                                <Receipt className="w-5 h-5 text-blue-500" />
                                                {t('account_receivable.settlement_form.history_title')}
                                            </CardTitle>
                                        </CardHeader>
                                        <CardContent className="p-0">
                                            <div className="divide-y divide-gray-100 dark:divide-gray-700">
                                                {history.map((h: any, idx: number) => (
                                                    <div key={idx} className="p-4 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                                        <div className="flex justify-between items-start">
                                                            <div>
                                                                <p className="font-medium text-gray-900 dark:text-gray-100">{h.Note || t('account_receivable.settlement_form.history_partial_note')}</p>
                                                                <p className="text-sm text-gray-500">{h.CoaName || h.CoaCode || '-'}</p>
                                                                <p className="text-xs text-gray-400 flex items-center gap-1 mt-1">
                                                                    <Calendar className="w-3 h-3" />
                                                                    {FormatDate(h.PaymentDate)}
                                                                </p>
                                                                {h.PaymentRef && <p className="text-xs text-gray-400 mt-0.5">Ref: {h.PaymentRef}</p>}
                                                            </div>
                                                            <div className="text-right">
                                                                <span className="font-mono font-semibold text-green-600 block">
                                                                    {FormatNumber(h.Amount)}
                                                                </span>
                                                                <span className="text-xs text-gray-400 block mt-1">
                                                                    {h.PaidBy}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                ))}
                                            </div>
                                        </CardContent>
                                    </Card>
                                );
                            }
                            return (
                                <Card className="shadow-sm border border-gray-200 dark:border-gray-800 h-full">
                                    <CardHeader className="bg-gray-50/50 dark:bg-[#1b2e4b] border-b border-gray-100 dark:border-gray-700 px-6 py-4">
                                        <CardTitle className="flex items-center gap-2 text-base font-semibold text-gray-800 dark:text-gray-100">
                                            <Receipt className="w-5 h-5 text-blue-500" />
                                            Riwayat Pembayaran
                                        </CardTitle>
                                    </CardHeader>
                                    <CardContent className="p-12 flex flex-col items-center justify-center text-gray-400">
                                        <Receipt className="w-12 h-12 mb-3 opacity-20" />
                                        <p className="text-sm">{t('account_receivable.settlement_form.history_empty')}</p>
                                    </CardContent>
                                </Card>
                            );
                        })()}
                    </div>
                </div>
            )}


            {/* Action Bar (Static) */}
            {!isPaid && (
                <Card className="bg-gradient-to-r from-primary to-primary/80 text-white mt-6">
                    <CardContent className="p-6">
                        <div className="flex justify-between items-center">
                            <div>
                                <p className="text-white/80 text-sm uppercase tracking-wider font-semibold">{t('account_receivable.settlement_form.current_total_label')}</p>
                            </div>
                            <div className="flex items-center gap-6">
                                <p className="text-3xl font-bold font-mono">{FormatNumber(finalAmount > 0 ? finalAmount : 0)}</p>
                                <Button
                                    onClick={handleConfirm}
                                    disabled={!selectedBank || isProcessing}
                                    className="bg-white text-primary hover:bg-gray-100 gap-2 font-bold px-8 h-12 rounded-full border-none"
                                >
                                    {isProcessing ? (
                                        <>Processing...</>
                                    ) : (
                                        <>
                                            <Save className="w-5 h-5 mr-2" />
                                            {t('account_receivable.settlement_form.payment_form.save_btn') || 'Proses Pembayaran'}
                                        </>
                                    )}
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            )}

            <ConfirmationModal
                isOpen={showConfirmModal}
                onClose={() => setShowConfirmModal(false)}
                onConfirm={handleSave}
                title={t('account_receivable.settlement_form.modal.title')}
                message={
                    <div className="space-y-3 text-sm">
                        <p className="text-gray-600">{t('account_receivable.settlement_form.modal.message', { voucher: voucher?.voucherNumber, amount: formatCurrency(voucher?.totalAmount || 0) })}</p>
                        <div className="bg-gray-50 rounded-lg p-4 space-y-2 border">
                            <div className="flex justify-between">
                                <span className="text-gray-500">Voucher</span>
                                <span className="font-semibold font-mono">{voucher?.voucherNumber}</span>
                            </div>
                            <div className="flex justify-between">
                                <span className="text-gray-500">Bank (COA)</span>
                                <span className="font-semibold">{selectedBank?.label || '-'}</span>
                            </div>
                            <div className="flex justify-between">
                                <span className="text-gray-500">Tgl Bayar</span>
                                <span className="font-semibold">{paymentDate ? FormatDate(paymentDate.toISOString()) : '-'}</span>
                            </div>
                            {paymentRef && (
                                <div className="flex justify-between">
                                    <span className="text-gray-500">Referensi</span>
                                    <span className="font-semibold font-mono">{paymentRef}</span>
                                </div>
                            )}
                            <div className="border-t pt-2 mt-2 space-y-1">
                                <div className="flex justify-between">
                                    <span className="text-gray-500">Total Tagihan</span>
                                    <span className="font-mono">{FormatNumber(voucher?.totalAmount || 0)}</span>
                                </div>
                                {discountAmount > 0 && (
                                    <div className="flex justify-between text-red-500">
                                        <span>Diskon ({discountPercent.toFixed(2)}%)</span>
                                        <span className="font-mono">-{FormatNumber(discountAmount)}</span>
                                    </div>
                                )}
                                <div className="flex justify-between">
                                    <span className="text-gray-500">Jumlah Bayar</span>
                                    <span className="font-mono">{FormatNumber(parseFloat(amountToPay) || 0)}</span>
                                </div>
                                <div className="border-t pt-1 flex justify-between font-bold text-primary">
                                    <span>Total Kas Masuk</span>
                                    <span className="font-mono">{FormatNumber(finalAmount > 0 ? finalAmount : 0)}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                }
                confirmText={t('account_receivable.settlement_form.modal.yes')}
                cancelText={t('account_receivable.settlement_form.modal.cancel')}
                loading={isProcessing}
                variant="primary"
            />

            {/* Unpost (Delete) Confirmation Modal */}
            <ConfirmationModal
                isOpen={showDeleteModal}
                onClose={() => setShowDeleteModal(false)}
                onConfirm={handleUnpost}
                title={t('account_receivable.payment_voucher_detail.modal.unpost_title', 'Unpost Item')}
                message={t('account_receivable.payment_voucher_detail.modal.unpost_message', 'Are you sure you want to unpost this item? It will return to Pending status.')}
                confirmText={t('account_receivable.payment_voucher_detail.modal.unpost_confirm', 'Yes, Unpost')}
                cancelText={t('common.cancel', 'Cancel')}
                loading={isUnposting}
                variant="danger"
            />

            {/* Void Confirmation Modal */}
            <ConfirmationModal
                isOpen={showVoidModal}
                onClose={() => setShowVoidModal(false)}
                onConfirm={handleVoid}
                title={t('account_receivable.payment_voucher_detail.modal.void_title', 'Void Item')}
                message={t('account_receivable.payment_voucher_detail.modal.void_message', 'Are you sure you want to void this item? It will be marked as Void.')}
                confirmText={t('account_receivable.payment_voucher_detail.modal.void_confirm', 'Yes, Void')}
                cancelText={t('common.cancel', 'Cancel')}
                loading={isVoiding}
                variant="danger"
            />

            {/* Bulk Revision Modal */}
            {
                showRevisionModal && (
                    <div className="fixed inset-0 z-[9999] overflow-y-auto bg-black/50 flex items-center justify-center p-4 backdrop-blur-sm">
                        <div className="bg-white dark:bg-[#1b2e4b] rounded-lg shadow-xl w-full max-w-md border border-gray-200 dark:border-gray-700 animate-in fade-in zoom-in duration-200">
                            <div className="p-6">
                                <h3 className="text-lg font-bold text-gray-900 dark:text-white mb-2">
                                    {t('account_receivable.settlement_form.revision.title')}
                                </h3>
                                <p className="text-sm text-gray-500 dark:text-gray-400 mb-6">
                                    {t('account_receivable.settlement_form.revision.subtitle')}
                                </p>

                                <div className="space-y-4">
                                    <div className="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-8 text-center hover:border-primary transition-colors cursor-pointer bg-gray-50 dark:bg-[#0e1726]"
                                        onDragOver={(e) => e.preventDefault()}
                                        onDrop={(e) => {
                                            e.preventDefault();
                                            const file = e.dataTransfer.files[0];
                                            if (file) handleBulkRevision(file);
                                        }}
                                        onClick={() => document.getElementById('revision-file-input')?.click()}
                                    >
                                        <Upload className="mx-auto h-10 w-10 text-gray-400 mb-3" />
                                        <p className="text-sm font-medium text-gray-700 dark:text-gray-300">
                                            {t('account_receivable.settlement_form.revision.dropzone_title')}
                                        </p>
                                        <p className="text-xs text-gray-500 mt-1">{t('account_receivable.settlement_form.revision.dropzone_hint')}</p>
                                        <input
                                            id="revision-file-input"
                                            type="file"
                                            accept=".xlsx, .xls"
                                            className="hidden"
                                            onChange={(e) => {
                                                const file = e.target.files?.[0];
                                                if (file) handleBulkRevision(file);
                                            }}
                                        />
                                    </div>
                                </div>

                                <div className="flex justify-end gap-3 mt-6">
                                    <Button
                                        variant="outline"
                                        onClick={() => setShowRevisionModal(false)}
                                        disabled={isRevising}
                                    >
                                        {t('common.cancel', 'Cancel')}
                                    </Button>
                                </div>
                            </div>
                        </div>
                    </div>
                )
            }
            {/* Print Only Layout */}
            <div className="hidden print:block font-sans text-black bg-white p-4 absolute top-0 left-0 w-full min-h-screen z-[9999]">
                {/* Letterhead */}
                <div className="flex items-start gap-4 mb-4 border-b-2 border-black pb-2">
                    <img src="/assets/images/logo-thb-transparent.png" alt="Logo" className="w-16 h-16 object-contain" />
                    <div className="flex-1 text-center pr-16">
                        <h1 className="text-xl font-bold">RS Taman Harapan Baru</h1>
                        <p className="text-xs">Jl. Kaliabang Tengah No.2, RT 004/023 Pejuang, Medan Satria, Bekasi, Jawa Barat</p>
                        <p className="text-xs">021-8898 1055</p>
                        <p className="text-xs">E-mail : rs_thb@yahoo.com</p>
                    </div>
                </div>

                <div className="text-center mb-6">
                    <h2 className="text-lg font-bold border-b border-black inline-block px-4">INVOICE</h2>
                </div>

                {/* Metadata */}
                <div className="flex justify-between mb-4 text-xs">
                    <div className="space-y-1">
                        <div className="grid grid-cols-[80px_1fr]">
                            <span>Payer</span>
                            <span className="font-bold">: {voucher?.payerName || '-'}</span>
                        </div>
                        <div className="grid grid-cols-[80px_1fr]">
                            <span>ID</span>
                            <span>: {voucher?.payerId || '-'}</span>
                        </div>
                    </div>
                    <div className="space-y-1 w-64">
                        <div className="grid grid-cols-[100px_1fr]">
                            <span>Tanggal</span>
                            <span>: {FormatDate(new Date().toISOString())}</span>
                        </div>
                        <div className="grid grid-cols-[100px_1fr]">
                            <span>No. Invoice</span>
                            <span className="font-bold">: {voucher?.voucherNumber || '-'}</span>
                        </div>
                        <div className="grid grid-cols-[100px_1fr]">
                            <span>Jatuh Tempo</span>
                            <span>: {voucher?.dueDate ? FormatDate(voucher?.dueDate) : '-'}</span>
                        </div>
                        <div className="grid grid-cols-[100px_1fr]">
                            <span>Tempo Bayar</span>
                            <span>: {voucher?.dueDate && voucher?.voucherDate ?
                                Math.round((new Date(voucher.dueDate).getTime() - new Date(voucher.voucherDate).getTime()) / (1000 * 3600 * 24)) : '30'}</span>
                        </div>
                    </div>
                </div>

                {/* Table */}
                <table className="w-full border-collapse mb-4 table-fixed border border-black [&_th]:!text-xs [&_td]:!text-xs">
                    <thead>
                        <tr className="border-b border-black bg-gray-50">
                            <th className="py-1 px-2 text-left border-x border-black w-[5%]">No.</th>
                            <th className="py-1 px-2 text-left border-x border-black w-[15%]">No.Rawat</th>
                            <th className="py-1 px-2 text-left border-x border-black w-[25%]">Nama Pasien</th>
                            <th className="py-1 px-2 text-left border-x border-black w-[20%]">Nama Perusahaan</th>
                            <th className="py-1 px-2 text-left border-x border-black w-[20%]">No.Peserta</th>
                            <th className="py-1 px-2 text-right border-x border-black w-[15%]">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        {items?.map((item: any, idx: number) => (
                            <tr key={idx} className="border-b border-black">
                                <td className="py-1 px-2 border-x border-black text-center">{idx + 1}</td>
                                <td className="py-1 px-2 border-x border-black truncate">{item.medicalRecordNo || '-'}</td>
                                <td className="py-1 px-2 border-x border-black font-medium break-words whitespace-normal align-middle">{item.patientName || '-'}</td>
                                <td className="py-1 px-2 border-x border-black break-words whitespace-normal align-top">{voucher?.payerName || '-'}</td>
                                <td className="py-1 px-2 border-x border-black truncate">{item.sepNo || '-'}</td>
                                <td className="py-1 px-2 border-x border-black text-right font-mono">{FormatNumber(
                                    item.adjustmentAmount != null && item.adjustmentAmount !== 0 && item.adjustmentAmount !== item.totalAmount
                                        ? item.adjustmentAmount : item.totalAmount
                                )}</td>
                            </tr>
                        ))}
                    </tbody>
                </table>

                {/* Footer Section */}
                <div className="flex justify-between items-start text-[10px] mb-8">
                    <div className="flex-1 italic">
                        <span>Terbilang : </span>
                        <span className="font-medium lowercase first-letter:uppercase">{terbilang(voucher?.totalAmount || 0)} rupiah</span>
                    </div>
                    <div className="w-64 border-t border-black pt-1">
                        <div className="flex justify-between font-bold text-xs">
                            <span>Total :</span>
                            <span>{FormatNumber(voucher?.totalAmount || 0)}</span>
                        </div>
                    </div>
                </div>

                {/* Transfer Info and Signatures */}
                <div className="grid grid-cols-2 gap-4 text-[10px] mt-auto">
                    <div className="flex flex-col h-full">
                        <p className="font-bold mb-1">Transfer :</p>
                        <div className="flex gap-8">
                            <div>
                                <p>Bank Syariah Indonesia</p>
                                <p>PT. Permata Bunda Abadi</p>
                                <p>No.Rek. 6666665005</p>
                            </div>
                            <div>
                                <p>Bank Mandiri</p>
                                <p>PT Permata Bunda Abadi</p>
                                <p>No. Rek. 15600770077-08</p>
                            </div>
                        </div>
                        <p className="mt-2">Email keuangan&nbsp; rsthb_keu@yahoo.com</p>

                        <div className="mt-4 flex flex-col items-center w-fit">
                            <p className="mb-1 w-full text-left">Dengan Hormat,</p>
                            <img
                                src={`https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=Signed by ${encodeURIComponent(printConfig.leftName)}`}
                                alt="QR Signature"
                                className="w-16 h-16 mb-2 border p-0.5"
                            />
                            <p className="font-bold underline">{printConfig.leftName}</p>
                            <p>{printConfig.leftTitle}</p>
                        </div>
                    </div>
                    <div className="flex flex-col items-end justify-end h-full">
                        <div className="flex flex-col items-center w-fit">
                            <p className="mb-1 w-full text-left">Kepala Bagian Keuangan</p>
                            <img
                                src={`https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=Approved by ${encodeURIComponent(printConfig.rightName)}`}
                                alt="QR Approval"
                                className="w-16 h-16 mb-2 border p-0.5"
                            />
                            <p className="font-bold underline">{printConfig.rightName}</p>
                            <p>{printConfig.rightTitle}</p>
                        </div>
                    </div>
                </div>
            </div>

        </div >


    );
};
export default SettlementARForm;
