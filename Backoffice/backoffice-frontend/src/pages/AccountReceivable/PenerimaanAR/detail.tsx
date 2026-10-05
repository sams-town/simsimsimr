import { useState, useCallback, useEffect } from 'react';
import { useTranslation } from 'react-i18next';
import { useParams, useNavigate, useSearchParams } from 'react-router-dom';
import { DataTable } from 'mantine-datatable';
import {
    useGetAccountReceivableListQuery,
    useLazyGetAccountReceivableListQuery,
    useFinalizeAccountReceivableVoucherMutation,
    useUnpostAccountReceivableMutation,
    useVoidAccountReceivableMutation,
    useMarkAsPrintedAccountReceivableMutation,
    useExportAccountReceivableMutation,
    useBulkRevisionAccountReceivableMutation,
    useGetAccountReceivablePrintConfigQuery,
    useUnfinalizeAccountReceivableVoucherMutation,
} from '@/store/api/accountReceivable/accountReceivableApiSlice';
import { toastMessage } from '@/utils/toastUtils';
import ConfirmationModal from '@/components/ConfirmationModal';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { ArrowLeft, FileText, Trash2, Printer, Ban, Upload, Download, FileSpreadsheet, StickyNote } from 'lucide-react';
import { FormatNumber } from '@/utils/formatNumber';
import { FormatDate } from '@/utils/formatDate';
import Flatpickr from 'react-flatpickr';
import 'flatpickr/dist/flatpickr.css';
import IconCalendar from '@/components/Icon/IconCalendar';
import { useRoleAccess } from '@/hooks/useRoleAccess';

interface ARDetailItem {
    arId: number;
    arNumber: string;
    transactionDate: string;
    postingDate?: string;
    serviceType: string;
    totalAmount: number;
    adjustmentAmount?: number;
    paymentStatus: string;
    voucherNumber?: string;
    updatedDate?: string;
    payerId?: string;
    payerName?: string;
    patientName?: string;
    medicalRecordNo?: string;
    invoiceNo?: string;
    isPrinted?: boolean;
    sepNo?: string;
    notes?: string;
    noteHistory?: string;
}

const getEffectiveAmount = (row: ARDetailItem) =>
    row.adjustmentAmount != null && row.adjustmentAmount !== 0 && row.adjustmentAmount !== row.totalAmount
        ? row.adjustmentAmount : row.totalAmount;

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

const PaymentVoucherARDetail = () => {
    const { t } = useTranslation();
    const { payerId: paramPayerId } = useParams<{ payerId: string }>();
    const navigate = useNavigate();
    const [searchParams] = useSearchParams();
    const statusParam = searchParams.get('status') || 'posting';
    const isFinal = statusParam === 'final';
    const isPaid = statusParam === 'paid';

    const [showConfirmModal, setShowConfirmModal] = useState(false);
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [showVoidModal, setShowVoidModal] = useState(false);
    const [showCancelVoucherModal, setShowCancelVoucherModal] = useState(false);
    const [selectedDeleteId, setSelectedDeleteId] = useState<number | null>(null);
    const [selectedVoidId, setSelectedVoidId] = useState<number | null>(null);

    // Revision State
    const [showRevisionModal, setShowRevisionModal] = useState(false);
    const [revisionFile, setRevisionFile] = useState<File | null>(null);

    // Print state
    const [printRecords, setPrintRecords] = useState<ARDetailItem[]>([]);
    const [printMode, setPrintMode] = useState<'all' | 'single'>('all');
    const [singlePrintItem, setSinglePrintItem] = useState<ARDetailItem | null>(null);

    // Selection state
    const [selectedRecords, setSelectedRecords] = useState<ARDetailItem[]>([]);

    // Default due date: today + 14 days
    const defaultDueDate = new Date();
    defaultDueDate.setDate(defaultDueDate.getDate() + 30);
    const [dueDate, setDueDate] = useState<Date>(defaultDueDate);

    // Pagination
    const [page, setPage] = useState(1);
    const PAGE_SIZE = 20;

    const paymentStatusParam = isPaid ? 'paid' : (isFinal ? 'vouchered' : 'Posting');

    const { data: listData, isLoading, refetch } = useGetAccountReceivableListQuery({
        payerId: paramPayerId,
        paymentStatus: paymentStatusParam,
        page,
        pageSize: PAGE_SIZE,
    });

    // Lazy query for fetching ALL records for print
    const [fetchAllRecords] = useLazyGetAccountReceivableListQuery();

    const [finalizeVoucher, { isLoading: isGenerating }] = useFinalizeAccountReceivableVoucherMutation();
    const [unpostAR, { isLoading: isDeleting }] = useUnpostAccountReceivableMutation();
    const [voidAR, { isLoading: isVoiding }] = useVoidAccountReceivableMutation();
    const [markAsPrinted] = useMarkAsPrintedAccountReceivableMutation();
    const [exportAccountReceivable, { isLoading: isExporting }] = useExportAccountReceivableMutation();
    const [bulkRevision, { isLoading: isRevising }] = useBulkRevisionAccountReceivableMutation();
    const [unfinalizeVoucher, { isLoading: isCancelling }] = useUnfinalizeAccountReceivableVoucherMutation();
    // Batal voucher hanya Administrator & Manager. Backend menegakkan hal yang sama;
    // ini semata agar tombolnya tidak terlihat oleh role yang pasti ditolak.
    const { canCancelArVoucher } = useRoleAccess();

    const { data: printConfigData } = useGetAccountReceivablePrintConfigQuery(undefined);
    const printConfig = {
        leftName: printConfigData?.data?.PRINT_SIGNER_LEFT_NAME || 'Naurah Nazhifah Salim',
        leftTitle: printConfigData?.data?.PRINT_SIGNER_LEFT_TITLE || 'Bagian Penagihan',
        rightName: printConfigData?.data?.PRINT_SIGNER_RIGHT_NAME || 'Herlan Mars, S.Ak',
        rightTitle: printConfigData?.data?.PRINT_SIGNER_RIGHT_TITLE || 'Manajer Keu & Akuntansi',
    };

    const records: ARDetailItem[] = listData?.data?.data || listData?.data || [];
    const totalRecords = listData?.data?.totalData || records.length || 0;
    const totalAmount = records.reduce((sum, r) => sum + getEffectiveAmount(r), 0);

    const payerName = listData?.data?.data?.[0]?.payerName || (!isFinal && !isPaid ? paramPayerId : '-');

    // Auto-select all records by default when data loads
    const recordIds = records.map(r => r.arId).join(',');
    useEffect(() => {
        if (records.length > 0) setSelectedRecords(records);
    }, [recordIds]);

    const selectedTotalAmount = selectedRecords.reduce((sum, r) => sum + getEffectiveAmount(r), 0);

    // Check if any item has voucher number
    const hasVoucherNumber = records.some(r => r.voucherNumber);

    const formatCurrency = (value: number) => {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
    };

    // Compute print data and totals
    const printItems = printMode === 'single' && singlePrintItem ? [singlePrintItem] : printRecords;
    const printTotalAmount = printItems.reduce((sum, r) => sum + getEffectiveAmount(r), 0);

    const handleGenerate = async () => {
        if (!paramPayerId) {
            toastMessage(t('account_receivable.payment_voucher_detail.toast.invalid_payer'), 'error');
            return;
        }
        if (selectedRecords.length === 0) {
            toastMessage('Pilih minimal satu item', 'error');
            return;
        }

        try {
            await finalizeVoucher({
                payerId: paramPayerId,
                voucherDate: new Date().toISOString(),
                dueDate: dueDate ? new Date(Date.UTC(dueDate.getFullYear(), dueDate.getMonth(), dueDate.getDate())).toISOString() : null,
                arIds: selectedRecords.map(r => r.arId),
            }).unwrap();

            toastMessage(t('account_receivable.payment_voucher_detail.toast.generate_success', { count: selectedRecords.length }), 'success');
            setShowConfirmModal(false);
            navigate(-1);
        } catch (error: any) {
            toastMessage(error?.data?.message || t('account_receivable.payment_voucher_detail.toast.generate_failed'), 'error');
        }
    };

    const voucherNumber = records?.[0]?.voucherNumber || '';

    const handleCancelVoucher = async () => {
        if (!voucherNumber) {
            toastMessage('No voucher number found', 'error');
            return;
        }
        try {
            await unfinalizeVoucher({ voucherNumber }).unwrap();
            toastMessage(t('account_receivable.payment_voucher_detail.toast.cancel_success'), 'success');
            setShowCancelVoucherModal(false);
            navigate(-1);
        } catch (error: any) {
            toastMessage(error?.data?.message || t('account_receivable.payment_voucher_detail.toast.cancel_failed'), 'error');
        }
    };

    const handleDelete = async () => {
        if (!selectedDeleteId) return;

        try {
            await unpostAR({ arIds: [selectedDeleteId] }).unwrap();
            toastMessage(t('account_receivable.payment_voucher_detail.toast.delete_success', 'Item unposted successfully'), 'success');
            setShowDeleteModal(false);
            setSelectedDeleteId(null);
            refetch();
        } catch (error: any) {
            toastMessage(error?.data?.message || t('account_receivable.payment_voucher_detail.toast.delete_failed', 'Failed to unpost item'), 'error');
        }
    };

    const handleVoid = async () => {
        if (!selectedVoidId) return;

        try {
            await voidAR({ arIds: [selectedVoidId] }).unwrap();
            toastMessage(t('account_receivable.payment_voucher_detail.toast.void_success', 'Item voided successfully'), 'success');
            setShowVoidModal(false);
            setSelectedVoidId(null);
            refetch();
        } catch (error: any) {
            toastMessage(error?.data?.message || t('account_receivable.payment_voucher_detail.toast.void_failed', 'Failed to void item'), 'error');
        }
    };

    const handlePrint = useCallback(async () => {
        if (selectedRecords.length === 0) {
            toastMessage('Pilih minimal satu item untuk print', 'error');
            return;
        }

        setPrintRecords(selectedRecords);
        setPrintMode('all');

        // Mark selected as printed
        const ids = selectedRecords.map(r => r.arId);
        if (ids.length > 0) {
            try {
                await markAsPrinted({ arIds: ids }).unwrap();
                refetch();
            } catch (e) { console.error('Failed to mark printed', e) }
        }

        setTimeout(() => window.print(), 300);
    }, [selectedRecords, markAsPrinted, refetch]);

    const handlePrintSingle = useCallback(async (item: ARDetailItem) => {
        setSinglePrintItem(item);
        setPrintMode('single');

        // Mark single item as printed
        try {
            await markAsPrinted({ arIds: [item.arId] }).unwrap();
            refetch();
        } catch (e) { console.error('Failed to mark printed', e) }

        setTimeout(() => window.print(), 300);
    }, [markAsPrinted, refetch]);

    const handleExport = async () => {
        try {
            const blob = await exportAccountReceivable({
                payerId: paramPayerId,
                paymentStatus: paymentStatusParam,
            }).unwrap();

            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `Voucher_Detail_${paramPayerId}_${isFinal ? 'Final' : 'Draft'}.xlsx`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
        } catch (error) {
            toastMessage(t('account_receivable.toast.export_failed'), 'error');
        }
    };

    const handleRevisionUpload = async () => {
        if (!revisionFile) return;

        const formData = new FormData();
        formData.append('file', revisionFile);

        try {
            await bulkRevision(formData).unwrap();
            toastMessage('Revision processed successfully', 'success');
            setShowRevisionModal(false);
            setRevisionFile(null);
            refetch();
        } catch (error: any) {
            toastMessage(error?.data?.message || 'Revision failed', 'error');
        }
    };

    // Render print layout (reusable for both all & single)
    const renderPrintLayout = (items: ARDetailItem[], itemTotalAmount: number) => (
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
                        <span className="font-bold">: {payerName || '-'}</span>
                    </div>
                    <div className="grid grid-cols-[80px_1fr]">
                        <span>ID</span>
                        <span>: {items[0]?.payerId || paramPayerId || '-'}</span>
                    </div>
                </div>
                <div className="space-y-1 w-64">
                    <div className="grid grid-cols-[100px_1fr]">
                        <span>Tanggal</span>
                        <span>: {FormatDate(new Date().toISOString())}</span>
                    </div>
                    <div className="grid grid-cols-[100px_1fr]">
                        <span>No. Invoice</span>
                        <span className="font-bold">: {items[0]?.voucherNumber || '-'}</span>
                    </div>
                    <div className="grid grid-cols-[100px_1fr]">
                        <span>Jatuh Tempo</span>
                        <span>: {dueDate ? FormatDate(dueDate.toISOString()) : '-'}</span>
                    </div>
                    <div className="grid grid-cols-[100px_1fr]">
                        <span>Tempo Bayar</span>
                        <span>: {dueDate
                            ? `${Math.max(0, Math.round((new Date(dueDate).getTime() - new Date().getTime()) / (1000 * 3600 * 24)))} Hari`
                            : '30 Hari'}</span>
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
                    {items.map((item, idx) => (
                        <tr key={item.arId || idx} className="border-b border-black">
                            <td className="py-1 px-2 border-x border-black text-center">{idx + 1}</td>
                            <td className="py-1 px-2 border-x border-black truncate">{item.medicalRecordNo || '-'}</td>
                            <td className="py-1 px-2 border-x border-black font-medium break-words whitespace-normal align-middle">{item.patientName || '-'}</td>
                            <td className="py-1 px-2 border-x border-black break-words whitespace-normal align-top">{payerName || '-'}</td>
                            <td className="py-1 px-2 border-x border-black truncate">{item.sepNo || '-'}</td>
                            <td className="py-1 px-2 border-x border-black text-right font-mono">{FormatNumber(getEffectiveAmount(item))}</td>
                        </tr>
                    ))}
                </tbody>
            </table>

            {/* Footer Section */}
            <div className="flex justify-between items-start text-[10px] mb-8">
                <div className="flex-1 italic">
                    <span>Terbilang : </span>
                    <span className="font-medium lowercase first-letter:uppercase">{terbilang(Math.round(itemTotalAmount))} rupiah</span>
                </div>
                <div className="w-64 border-t border-black pt-1">
                    <div className="flex justify-between font-bold text-xs">
                        <span>Total :</span>
                        <span>{FormatNumber(itemTotalAmount)}</span>
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
    );

    return (
        <>
            <div className="flex flex-col gap-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative print:hidden">
                {/* LUNAS Watermark for Paid Items */}
                {isPaid && (
                    <div className="absolute inset-0 flex items-center justify-center pointer-events-none z-10 overflow-hidden">
                        <span className="text-8xl font-black text-green-600 border-[12px] border-green-600 px-10 py-4 rounded-xl transform -rotate-12 inline-block tracking-widest uppercase opacity-20 select-none">
                            {t('status.paid_watermark')}
                        </span>
                    </div>
                )}
                {/* Header / Title */}
                <div className="flex items-center justify-between mt-4">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                            {isPaid ? t('account_receivable.payment_voucher_detail.title_paid') : (isFinal ? t('account_receivable.payment_voucher_detail.title_detail') : t('account_receivable.payment_voucher_detail.title_generate'))}
                        </h1>
                        <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            {isPaid ? t('account_receivable.payment_voucher_detail.subtitle_paid') : (isFinal ? t('account_receivable.payment_voucher_detail.subtitle_detail') : t('account_receivable.payment_voucher_detail.subtitle_generate'))}
                        </p>
                    </div>
                    <Button
                        type="button"
                        variant="outline"
                        onClick={() => navigate(-1)}
                        className="gap-2 shadow-sm bg-white dark:bg-black"
                        size="sm"
                    >
                        <ArrowLeft className="h-4 w-4" />
                        {t('account_receivable.payment_voucher_detail.back_btn')}
                    </Button>

                    {/* Revision Button for Draft/Posting */}
                    {!isFinal && !isPaid && (
                        <div className="flex gap-2">
                            <Button
                                type="button"
                                variant="outline"
                                onClick={handleExport}
                                className="gap-2 shadow-sm bg-white dark:bg-black"
                                size="sm"
                                disabled={isExporting}
                            >
                                <Download className="h-4 w-4" />
                                Download
                            </Button>
                            <Button
                                type="button"
                                variant="outline"
                                onClick={() => setShowRevisionModal(true)}
                                className="gap-2 shadow-sm bg-white dark:bg-black text-orange-600 border-orange-200 hover:bg-orange-50"
                                size="sm"
                            >
                                <Upload className="h-4 w-4" />
                                Upload Revision
                            </Button>
                        </div>
                    )}
                </div>

                {/* Already Printed Warning */}
                {records.some(r => r.isPrinted) && (
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

                {/* Payer Info Header - Refined Grid Layout */}
                <div className="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-[#1b2e4b] shadow-sm overflow-hidden">
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 divide-y md:divide-y-0 md:divide-x border-gray-200 dark:border-gray-700">
                        <div className="p-5">
                            <p className="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-medium mb-1.5 flex items-center gap-2">
                                {t('account_receivable.payment_voucher_detail.payer_info.code')}
                            </p>
                            <p className="text-lg font-bold text-gray-900 dark:text-white font-mono">
                                {records[0]?.payerId || (!isFinal && !isPaid ? paramPayerId : '-')}
                            </p>
                            {records[0]?.voucherNumber && (
                                <p className="text-xs text-primary font-mono mt-1 font-bold">
                                    {records[0].voucherNumber}
                                </p>
                            )}
                        </div>
                        <div className="p-5">
                            <p className="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-medium mb-1.5">
                                {t('account_receivable.payment_voucher_detail.payer_info.name')}
                            </p>
                            <p className="text-lg font-bold text-gray-900 dark:text-white truncate" title={payerName}>
                                {payerName}
                            </p>
                        </div>
                        <div className="p-5">
                            <p className="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-medium mb-1.5">
                                {t('account_receivable.payment_voucher_detail.payer_info.item_count')}
                            </p>
                            <p className="text-lg font-bold text-gray-900 dark:text-white">{totalRecords} item</p>
                        </div>
                        <div className="p-5 bg-primary/5 dark:bg-primary/10">
                            <p className="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-medium mb-1.5">
                                {t('account_receivable.payment_voucher_detail.payer_info.total_amount')}
                            </p>
                            <p className="text-xl font-bold text-primary">{formatCurrency(selectedTotalAmount)}</p>
                            {selectedRecords.length < records.length && (
                                <p className="text-xs text-gray-500 mt-0.5">{selectedRecords.length} / {records.length} item dipilih</p>
                            )}
                        </div>
                    </div>
                </div>

                {/* Detail Items Card */}
                <Card className="shadow-sm border border-gray-200 dark:border-gray-800">
                    <CardHeader className="bg-gray-50/50 dark:bg-[#1b2e4b] border-b border-gray-100 dark:border-gray-700 px-6 py-4">
                        <CardTitle className="text-base font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                            <span className="bg-primary/10 text-primary w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold">1</span>
                            {t('account_receivable.payment_voucher_detail.details_title')}
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="p-0">
                        <DataTable
                            highlightOnHover
                            className="table-hover"
                            records={records}
                            idAccessor="arId"
                            selectedRecords={selectedRecords}
                            onSelectedRecordsChange={setSelectedRecords}
                            columns={[
                                {
                                    accessor: 'rowNumber',
                                    title: 'No.',
                                    width: 60,
                                    textAlignment: 'center',
                                    render: (_row: ARDetailItem, index: number) => (page - 1) * PAGE_SIZE + index + 1
                                },
                                {
                                    accessor: 'transactionDate',
                                    title: t('account_receivable.payment_voucher_detail.table.trans_date'),
                                    width: 140,
                                    render: (row: ARDetailItem) => FormatDate(row.transactionDate)
                                },
                                {
                                    accessor: 'updatedDate',
                                    title: t('account_receivable.payment_voucher_detail.table.post_date'),
                                    width: 140,
                                    render: (row: ARDetailItem) => FormatDate(row.updatedDate || row.transactionDate)
                                },
                                {
                                    accessor: 'sepNo',
                                    title: 'SEP No',
                                    width: 140,
                                    render: (row: ARDetailItem) => <span className="font-mono text-xs">{row.sepNo || '-'}</span>
                                },
                                {
                                    accessor: 'patientInfo',
                                    title: t('jasa_medis.posting.table.patient_name', 'Patient Info'),
                                    width: 150,
                                    render: (row: ARDetailItem) => (
                                        <div className="flex flex-col">
                                            <span className="font-medium text-gray-900">{row.patientName || '-'}</span>
                                            <span className="text-xs text-gray-500">RM: {row.medicalRecordNo || '-'}</span>
                                        </div>
                                    )
                                },
                                {
                                    accessor: 'invoiceInfo',
                                    title: 'Invoice / Service',
                                    width: 150,
                                    render: (row: ARDetailItem) => (
                                        <div className="flex flex-col">
                                            <span className="font-medium text-gray-900">{row.invoiceNo || '-'}</span>
                                            <span className="text-xs text-gray-500 uppercase">
                                                {row.serviceType === 'rajal' ? 'Rawat Jalan' :
                                                    row.serviceType === 'ranap' ? 'Rawat Inap' : row.serviceType || '-'}
                                            </span>
                                        </div>
                                    )
                                },
                                {
                                    accessor: 'totalAmount',
                                    title: t('account_receivable.payment_voucher_detail.table.amount'),
                                    textAlignment: 'right',
                                    width: 160,
                                    render: (row: ARDetailItem) => (
                                        <div className="text-right font-medium font-mono">
                                            <div>{FormatNumber(getEffectiveAmount(row))}</div>
                                            {row.adjustmentAmount != null && row.adjustmentAmount !== 0 && row.adjustmentAmount !== row.totalAmount && (
                                                <div className="text-xs text-gray-400 line-through">{FormatNumber(row.totalAmount)}</div>
                                            )}
                                        </div>
                                    )
                                },
                                {
                                    accessor: 'notes',
                                    title: t('account_receivable.payment_voucher_detail.table.notes', 'Catatan'),
                                    width: 200,
                                    render: (row: ARDetailItem) => <NotesCell row={row} />
                                },
                                {
                                    accessor: 'action',
                                    title: t('account_receivable.payment_voucher_detail.table.action'),
                                    width: 120,
                                    textAlignment: 'center',
                                    render: (row: ARDetailItem) => (
                                        <div className="flex justify-center gap-1">
                                            <button
                                                type="button"
                                                onClick={() => handlePrintSingle(row)}
                                                className="text-blue-500 hover:text-blue-700 p-1.5 rounded-md hover:bg-blue-50 transition-colors"
                                                title="Cetak Invoice Pasien"
                                            >
                                                <Printer className="w-4 h-4" />
                                            </button>
                                            {!hasVoucherNumber && !isFinal && !isPaid && (
                                                <>
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
                                                </>
                                            )}
                                        </div>
                                    )
                                }
                            ]}
                            totalRecords={totalRecords}
                            recordsPerPage={PAGE_SIZE}
                            page={page}
                            onPageChange={setPage}
                            fetching={isLoading}
                            minHeight={200}
                            noRecordsText="Tidak ada data"
                            styles={{
                                root: { border: 'none', borderRadius: '0.75rem', overflow: 'hidden' },
                                pagination: { borderTop: '1px solid #f1f5f9', margin: '1rem' },
                                header: {
                                    backgroundColor: '#f8fafc',
                                    color: '#475569',
                                    fontWeight: 600,
                                    fontSize: '0.75rem',
                                    textTransform: 'uppercase',
                                    letterSpacing: '0.05em',
                                    height: '48px'
                                }
                            }}
                        />
                    </CardContent>
                </Card>

                {/* Summary Card - Updated with Due Date Picker */}
                <Card className="shadow-sm border border-gray-200 dark:border-gray-800">
                    <CardHeader className="bg-gray-50/50 dark:bg-[#1b2e4b] border-b border-gray-100 dark:border-gray-700 px-6 py-4">
                        <CardTitle className="text-base font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                            <span className="bg-primary/10 text-primary w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold">2</span>
                            {t('account_receivable.payment_voucher_detail.summary_title')}
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="p-6">
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div className="space-y-2">
                                <p className="text-gray-700 dark:text-gray-300 font-medium text-sm">{t('account_receivable.payment_voucher_detail.summary_form.total_label')}</p>
                                <div className="px-4 py-3 bg-primary/5 dark:bg-primary/10 rounded-md border border-primary/20 flex items-center h-[46px]">
                                    <p className="font-bold text-xl text-primary">{formatCurrency(selectedTotalAmount)}</p>
                                </div>
                            </div>
                            <div className="space-y-2">
                                <label className="text-gray-700 dark:text-gray-300 font-medium text-sm block">
                                    {t('account_receivable.payment_voucher_detail.summary_form.due_date_label')}
                                </label>
                                <div className="relative">
                                    <Flatpickr
                                        value={dueDate}
                                        onChange={(dates: Date[]) => dates[0] && setDueDate(dates[0])}
                                        options={{
                                            dateFormat: 'd/m/Y',
                                            minDate: isFinal ? undefined : 'today',
                                        }}
                                        disabled={hasVoucherNumber || isFinal || isPaid}
                                        className="px-4 py-3 w-full bg-white dark:bg-gray-800 rounded-md border border-gray-200 dark:border-gray-700 font-medium text-gray-900 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all disabled:bg-gray-100 disabled:text-gray-500 h-[46px]"
                                    />
                                    <div className="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                        <IconCalendar className="w-5 h-5" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Terbilang */}
                        <div className="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-md border border-blue-100 dark:border-blue-800">
                            <p className="text-sm text-gray-600 dark:text-gray-400 mb-1">{t('account_receivable.payment_voucher_detail.summary_form.terbilang_label')}:</p>
                            <p className="italic text-gray-800 dark:text-gray-200 font-medium text-lg">
                                {terbilang(Math.round(selectedTotalAmount)).trim() || t('account_receivable.payment_voucher_detail.zero')} {t('account_receivable.payment_voucher_detail.currency_name')}
                            </p>
                        </div>
                    </CardContent>
                </Card>

                {/* Action Buttons */}
                <div className="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-800 mb-8">
                    <Button
                        type="button"
                        variant="ghost"
                        onClick={() => navigate(-1)}
                        className="text-gray-600 hover:text-gray-900 hover:bg-gray-100"
                    >
                        {t('account_receivable.payment_voucher_detail.btn.cancel')}
                    </Button>

                    {!isFinal && !isPaid && (
                        <Button
                            type="button"
                            onClick={() => setShowConfirmModal(true)}
                            disabled={selectedRecords.length === 0 || isGenerating || hasVoucherNumber}
                            className="min-w-[160px] shadow-md hover:shadow-lg transition-all gap-2"
                        >
                            {isGenerating ? (
                                <>
                                    <div className="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                                    Processing...
                                </>
                            ) : (
                                <>
                                    <FileText className="h-4 w-4" />
                                    {t('account_receivable.payment_voucher_detail.btn.generate')}
                                </>
                            )}
                        </Button>
                    )}

                    {isFinal && !isPaid && canCancelArVoucher && (
                        <Button
                            type="button"
                            variant="destructive"
                            onClick={() => setShowCancelVoucherModal(true)}
                            disabled={isCancelling}
                            className="min-w-[160px] shadow-md hover:shadow-lg transition-all gap-2"
                        >
                            {isCancelling ? (
                                <>
                                    <div className="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                                    Processing...
                                </>
                            ) : (
                                <>
                                    <Ban className="h-4 w-4" />
                                    Cancel Voucher
                                </>
                            )}
                        </Button>
                    )}

                    <Button
                        type="button"
                        variant="outline"
                        onClick={handlePrint}
                        className="gap-2 shadow-sm"
                    >
                        <Printer className="h-4 w-4" />
                        {t('common.print', 'Print Semua')}
                    </Button>
                </div>

                <ConfirmationModal
                    isOpen={showConfirmModal}
                    onClose={() => setShowConfirmModal(false)}
                    onConfirm={handleGenerate}
                    title={t('account_receivable.payment_voucher_detail.modal.generate_title')}
                    message={t('account_receivable.payment_voucher_detail.modal.generate_message', { name: paramPayerId, amount: formatCurrency(selectedTotalAmount) })}
                    confirmText={t('account_receivable.payment_voucher_detail.modal.generate_yes')}
                    cancelText={t('account_receivable.payment_voucher_detail.modal.cancel')}
                    loading={isGenerating}
                    variant="primary"
                />

                <ConfirmationModal
                    isOpen={showCancelVoucherModal}
                    onClose={() => setShowCancelVoucherModal(false)}
                    onConfirm={handleCancelVoucher}
                    title="Cancel Voucher"
                    message={`Are you sure you want to cancel voucher ${voucherNumber}? All items will return to "Posting" status and the voucher number will be removed. This cannot be undone if items have been paid.`}
                    confirmText="Yes, Cancel Voucher"
                    cancelText="No, Keep It"
                    loading={isCancelling}
                    variant="danger"
                />

                <ConfirmationModal
                    isOpen={showDeleteModal}
                    onClose={() => {
                        setShowDeleteModal(false);
                        setSelectedDeleteId(null);
                    }}
                    onConfirm={handleDelete}
                    title={t('account_receivable.payment_voucher_detail.modal.delete_title')}
                    message={t('account_receivable.payment_voucher_detail.modal.delete_message')}
                    confirmText={t('account_receivable.payment_voucher_detail.modal.delete_yes')}
                    cancelText={t('account_receivable.payment_voucher_detail.modal.cancel')}
                    loading={isDeleting}
                    variant="danger"
                />

                <ConfirmationModal
                    isOpen={showVoidModal}
                    onClose={() => {
                        setShowVoidModal(false);
                        setSelectedVoidId(null);
                    }}
                    onConfirm={handleVoid}
                    title={t('account_receivable.payment_voucher_detail.modal.void_title', 'Void Item')}
                    message={t('account_receivable.payment_voucher_detail.modal.void_message', 'Are you sure you want to VOID this item? This action cannot be undone.')}
                    confirmText={t('account_receivable.payment_voucher_detail.modal.void_yes', 'Confirm Void')}
                    cancelText={t('account_receivable.payment_voucher_detail.modal.cancel')}
                    loading={isVoiding}
                    variant="danger"
                />
                <ConfirmationModal
                    isOpen={showRevisionModal}
                    onClose={() => {
                        setShowRevisionModal(false);
                        setRevisionFile(null);
                    }}
                    onConfirm={handleRevisionUpload}
                    title="Upload Revision (Bulk)"
                    confirmText="Process Revision"
                    cancelText="Cancel"
                    loading={isRevising}
                    variant="primary"
                    customContent={
                        <div className="mt-4">
                            <p className="text-sm text-gray-600 mb-4">
                                Upload Excel file to revise statuses. Ensure column 'Status' has 'Pending' (Unpost) or 'Void' values.
                            </p>
                            <label className="block text-sm font-medium text-gray-700 mb-2">
                                Select Excel File
                            </label>
                            <div className="flex items-center justify-center w-full">
                                <label className="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                    <div className="flex flex-col items-center justify-center pt-5 pb-6">
                                        <FileSpreadsheet className="w-8 h-8 mb-3 text-gray-400" />
                                        <p className="mb-2 text-sm text-gray-500">
                                            <span className="font-semibold">Click to upload</span>
                                        </p>
                                        <p className="text-xs text-gray-500">XLSX, XLS</p>
                                    </div>
                                    <input
                                        type="file"
                                        className="hidden"
                                        accept=".xlsx, .xls"
                                        onChange={(e) => setRevisionFile(e.target.files?.[0] || null)}
                                    />
                                </label>
                            </div>
                            {revisionFile && (
                                <p className="mt-2 text-sm text-green-600 font-medium flex items-center gap-2">
                                    <FileSpreadsheet className="w-4 h-4" />
                                    {revisionFile.name}
                                </p>
                            )}
                        </div>
                    }
                />
            </div>

            {/* Print Only Layout */}
            {renderPrintLayout(printItems, printTotalAmount)}
        </>
    );
};
export default PaymentVoucherARDetail;
