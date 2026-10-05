import { useEffect, useRef, useState } from 'react';
import { useDispatch } from 'react-redux';
import { useTranslation } from 'react-i18next';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '../../../store/themeConfigSlice';
import { DataTable, DataTableColumn } from 'mantine-datatable';
import Flatpickr from 'react-flatpickr';
import 'flatpickr/dist/flatpickr.css';
import SelectSearch from 'react-select';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import IconCalendar from '@/components/Icon/IconCalendar';
import TimelineButton from '@/components/ActivityTimeline/TimelineButton';
import { AUDIT_MODULE } from '@/components/ActivityTimeline/modules';
import {
    useGetAccountReceivableListQuery,
    useGetAccountReceivableInsuranceOptionsQuery,
    usePostAccountReceivableMutation,
    useUnpostAccountReceivableMutation,
    useExportAccountReceivableMutation,
    useVoidAccountReceivableMutation,
    useMarkAsPrintedAccountReceivableMutation,
    useBulkPostAccountReceivableMutation,
    useUploadAdjustmentMutation,
    useGetPayerOptionsQuery,
    useChangePayerAccountReceivableMutation,
    useAdjustAccountReceivableMutation,
    useAddNoteAccountReceivableMutation,
} from '@/store/api/accountReceivable/accountReceivableApiSlice';
import { FormatNumber } from '@/utils/formatNumber';
import { FormatDate } from '@/utils/formatDate';
import { toastMessage } from '@/utils/toastUtils';
import { Download, Filter, FileText, Clock, CheckCircle2, Send, Undo2, Loader2, FileSpreadsheet, Printer, Ban, Search, UploadCloud, RefreshCw, Pencil, StickyNote } from 'lucide-react';
import ConfirmationModal from '@/components/ConfirmationModal';

interface ReceivableData {
    arId: number;
    arNumber: string;
    journalId: string;
    payerType: string;
    payerTypeName: string;
    payerId: string;
    payerName: string;
    claimNumber: string;
    serviceType: string;
    coaCode: string;
    transactionDate: string;
    dueDate: string;
    totalAmount: number;
    paidAmount: number;
    outstandingAmount: number;
    paymentStatus: string;
    paymentDate: string;
    agingDays: number;
    agingBucket: string;
    notes: string;
    noteHistory?: string;
    voucherNumber?: string;
    patientName?: string;
    medicalRecordNo?: string;
    invoiceNo?: string;
    sepNo?: string;
    isPrinted?: boolean;
    status?: string;
    adjustmentAmount?: number;
    adjustmentDate?: string;
    adjustmentHistory?: string;
    payerChangeHistory?: string;
}

const DaftarAR = () => {
    const dispatch = useDispatch();
    const { t } = useTranslation();

    useEffect(() => {
        dispatch(setPageTitle(t('ar.daftar_piutang_page.title')));
        dispatch(setTitle(t('ar.daftar_piutang_page.title')));
        dispatch(setBreadcrumbTitle(['Dashboard', t('sidebar.piutang_usaha'), t('ar.daftar_piutang_page.title')]));
    }, [dispatch, t]);

    // Filter states
    const [selectedInsurance, setSelectedInsurance] = useState<any[]>([]);
    const [selectedPayerType, setSelectedPayerType] = useState<string>('');
    const [selectedServiceType, setSelectedServiceType] = useState<string>('');
    const [selectedStatus, setSelectedStatus] = useState<string>('Pending');
    const [startDate, setStartDate] = useState<Date | null>(null);
    const [endDate, setEndDate] = useState<Date | null>(null);

    const [hasVoucher, setHasVoucher] = useState<string>('');
    const [keyword, setKeyword] = useState<string>('');

    // Selection states
    const [selectedRows, setSelectedRows] = useState<Set<number>>(new Set());
    const [showPostModal, setShowPostModal] = useState(false);
    const [showUnpostModal, setShowUnpostModal] = useState(false);
    const [showVoidModal, setShowVoidModal] = useState(false);
    const [showBulkPostModal, setShowBulkPostModal] = useState(false);
    const [bulkPostFile, setBulkPostFile] = useState<File | null>(null);

    // Pagination
    const [page, setPage] = useState(1);
    const PAGE_SIZES = [10, 20, 30, 50];
    const [pageSize, setPageSize] = useState(PAGE_SIZES[0]);

    // Applied filters (managed directly via state updates)
    const getDefaultFilters = () => ({
        paymentStatus: 'Pending',
        payerId: '',
        serviceType: '',
        startDate: null as string | null,
        endDate: null as string | null
    });

    const [payerSearch, setPayerSearch] = useState('');

    // API
    const { data: insuranceOptions, isLoading: isLoadingInsurance } = useGetAccountReceivableInsuranceOptionsQuery(payerSearch);

    const formatDateForAPI = (date: Date | null): string | null => {
        if (!date) return null;
        return new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate())).toISOString();
    };

    const { data: listData, isLoading, refetch } = useGetAccountReceivableListQuery(
        {
            payerId: selectedInsurance.map((i: any) => i.value).join(','),
            payerType: selectedPayerType,
            serviceType: selectedServiceType,
            paymentStatus: selectedStatus,
            startDate: formatDateForAPI(startDate),
            endDate: formatDateForAPI(endDate),
            keyword: keyword,

            page,
            pageSize
        }
    );

    const [exportData, { isLoading: isExporting }] = useExportAccountReceivableMutation();
    const [postAR, { isLoading: isPosting }] = usePostAccountReceivableMutation();
    const [unpostAR, { isLoading: isUnposting }] = useUnpostAccountReceivableMutation();
    const [voidAR, { isLoading: isVoiding }] = useVoidAccountReceivableMutation();
    const [markAsPrinted, { isLoading: isMarking }] = useMarkAsPrintedAccountReceivableMutation();
    const [bulkPostAR, { isLoading: isBulkPosting }] = useBulkPostAccountReceivableMutation();
    const [uploadAdjustment, { isLoading: isUploadingAdjustment }] = useUploadAdjustmentMutation();
    const inputFileAdjustment = useRef<HTMLInputElement>(null);

    // Change Payer
    const [changePayerSearch, setChangePayerSearch] = useState('');
    const { data: payerOptionsData, isLoading: isLoadingPayerOptions } = useGetPayerOptionsQuery(changePayerSearch);
    const [changePayer, { isLoading: isChangingPayer }] = useChangePayerAccountReceivableMutation();
    const [showChangePayerModal, setShowChangePayerModal] = useState(false);
    const [changePayerTarget, setChangePayerTarget] = useState<ReceivableData | null>(null);
    const [selectedNewPayer, setSelectedNewPayer] = useState<any>(null);
    const [changePayerReason, setChangePayerReason] = useState('');

    // Adjust Amount
    const [adjustAr, { isLoading: isAdjusting }] = useAdjustAccountReceivableMutation();
    const [showAdjustModal, setShowAdjustModal] = useState(false);
    const [adjustTarget, setAdjustTarget] = useState<ReceivableData | null>(null);
    const [adjustAmountInput, setAdjustAmountInput] = useState<string>('');
    const [adjustReason, setAdjustReason] = useState('');

    // Add Note
    const [addNote, { isLoading: isAddingNote }] = useAddNoteAccountReceivableMutation();
    const [showNoteModal, setShowNoteModal] = useState(false);
    const [noteTarget, setNoteTarget] = useState<ReceivableData | null>(null);
    const [noteInput, setNoteInput] = useState('');

    // Options
    const insuranceOptionsFormatted = [
        { value: '', label: t('ar.daftar_piutang_page.filter.all_insurance') },
        ...(insuranceOptions?.data || insuranceOptions || []).map((p: any) => ({ value: p.value, label: p.label })),
    ];

    const serviceTypeOptions = [
        { value: '', label: t('ar.daftar_piutang_page.filter.all_service') },
        { value: 'ralan', label: 'Rawat Jalan' },
        { value: 'ranap', label: 'Rawat Inap' },
    ];

    const statusOptions = [
        { value: '', label: t('ar.daftar_piutang_page.filter.all_status') },
        { value: 'Pending', label: 'Pending' },
        { value: 'Posting', label: 'Posting' },
        { value: 'Paid', label: 'Paid' },
    ];

    const payerTypeOptions = [
        { value: '', label: 'Semua Payer Type' },
        { value: 'bpjs', label: 'BPJS' },
        { value: 'non bpjs', label: 'Non BPJS' },
    ];



    const resetSelection = () => {
        setPage(1);
        setSelectedRows(new Set());
    }

    const handleInsuranceChange = (vals: any[]) => {
        setSelectedInsurance(vals || []);
        resetSelection();
    };

    const handleServiceTypeChange = (val: string) => {
        setSelectedServiceType(val);
        resetSelection();
    };

    const handleKeywordChange = (val: string) => {
        setKeyword(val);
        setPage(1);
    };



    const handleBulkPostFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        if (e.target.files && e.target.files.length > 0) {
            setBulkPostFile(e.target.files[0]);
        }
    };

    const handleBulkPostSubmit = async () => {
        if (!bulkPostFile) return;
        const formData = new FormData();
        formData.append('file', bulkPostFile);
        try {
            await bulkPostAR(formData).unwrap();
            toastMessage(t('ar.daftar_piutang_page.toast.bulk_post_success', 'Bulk Post successful'), 'success');
            setShowBulkPostModal(false);
            setBulkPostFile(null);
            refetch();
        } catch (error: any) {
            toastMessage(error?.data?.message || t('ar.daftar_piutang_page.toast.bulk_post_failed', 'Bulk Post failed'), 'error');
        }
    };

    const handleVoid = () => {
        const arIds = Array.from(selectedRows);
        if (arIds.length === 0) return;
        setShowVoidModal(true);
    };

    const handleConfirmVoid = async () => {
        const arIds = Array.from(selectedRows);
        try {
            await voidAR({ arIds }).unwrap();
            toastMessage(t('ar.daftar_piutang_page.toast.void_success', 'Void successful'), 'success');
            setSelectedRows(new Set());
            setShowVoidModal(false);
            refetch();
        } catch (e: any) {
            toastMessage(e?.data?.message || 'Void failed', 'error');
        }
    };

    const handlePrint = async () => {
        const arIds = Array.from(selectedRows);
        if (arIds.length === 0) return;

        try {
            await markAsPrinted({ arIds }).unwrap();
            toastMessage(t('ar.daftar_piutang_page.toast.print_success', 'Marked as printed'), 'success');
            // Optionally open print view here? API creates PDF? 
            // Requirement says "nota tagihan dapat dicetak... setelah cetak ada penanda".
            // We marked it. Assuming print happens via separate flow or client side generation.
            // For now just marking it.
            setSelectedRows(new Set());
            refetch();
        } catch (e: any) {
            toastMessage(e?.data?.message || 'Print failed', 'error');
        }
    };



    const handleStatusChange = (val: string) => {
        setSelectedStatus(val);
        resetSelection();
    };

    const handleDateChange = (dates: Date[]) => {
        if (dates.length >= 1) setStartDate(dates[0]);
        if (dates.length >= 2) setEndDate(dates[1]);
        if (dates.length === 0) {
            setStartDate(null);
            setEndDate(null);
        }
        if (dates.length !== 1) resetSelection(); // Reset only on complete range or clear
    };

    const handleExport = async () => {
        try {
            const blob = await exportData({
                payerId: selectedInsurance.map((i: any) => i.value).join(','),
                payerType: selectedPayerType,
                serviceType: selectedServiceType,
                paymentStatus: selectedStatus,
                startDate: formatDateForAPI(startDate),
                endDate: formatDateForAPI(endDate),
                type: 'xlsx'
            }).unwrap();

            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', `daftar_ar_${new Date().toISOString().split('T')[0]}.xlsx`);
            document.body.appendChild(link);
            link.click();
            link.remove();
            window.URL.revokeObjectURL(url);
        } catch {
            toastMessage(t('ar.daftar_piutang_page.toast.export_failed'), 'error');
        }
    };

    const handleRowSelect = (id: number, checked: boolean) => {
        setSelectedRows((prev) => {
            const next = new Set(prev);
            checked ? next.add(id) : next.delete(id);
            return next;
        });
    };

    const handleSelectAll = (checked: boolean) => {
        if (checked) {
            const ids = records.filter(r => canSelect(r)).map(r => r.arId);
            setSelectedRows(new Set(ids));
        } else {
            setSelectedRows(new Set());
        }
    };

    const handleAdjustmentUpload = async (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('file', file);

        try {
            const result = await uploadAdjustment(formData).unwrap();
            toastMessage(`Adjustment berhasil: ${result?.data?.updatedCount || 0} data diperbarui`, 'success');
            if (inputFileAdjustment.current) inputFileAdjustment.current.value = '';
            refetch();
        } catch (error: any) {
            toastMessage(error?.data?.message || 'Gagal upload file adjustment', 'error');
        }
    };

    const payerOptionsFormatted = (payerOptionsData?.data || payerOptionsData || []).map((p: any) => ({
        value: p.value,
        label: p.label,
        refCode: p.refCode,
    }));

    const handleOpenChangePayer = (row: ReceivableData) => {
        setChangePayerTarget(row);
        setSelectedNewPayer(null);
        setChangePayerReason('');
        setShowChangePayerModal(true);
    };

    const handleConfirmChangePayer = async () => {
        if (!changePayerTarget || !selectedNewPayer) return;
        try {
            await changePayer({
                arId: changePayerTarget.arId,
                newPayerId: selectedNewPayer.value,
                newPayerName: selectedNewPayer.label,
                reason: changePayerReason,
            }).unwrap();
            toastMessage(t('ar.daftar_piutang_page.toast.change_payer_success', 'Payer berhasil diubah'), 'success');
            setShowChangePayerModal(false);
            setChangePayerTarget(null);
            setSelectedNewPayer(null);
            setChangePayerReason('');
        } catch (error: any) {
            toastMessage(error?.data?.message || t('ar.daftar_piutang_page.toast.change_payer_failed', 'Gagal mengubah payer'), 'error');
        }
    };

    const parsePayerHistory = (historyJson?: string) => {
        if (!historyJson) return [];
        try {
            return JSON.parse(historyJson);
        } catch {
            return [];
        }
    };

    const parseAdjustmentHistory = (historyJson?: string): any[] => {
        if (!historyJson) return [];
        try {
            const parsed = JSON.parse(historyJson);
            return Array.isArray(parsed) ? parsed : [];
        } catch {
            return [];
        }
    };

    const handleOpenAdjust = (row: ReceivableData) => {
        setAdjustTarget(row);
        setAdjustAmountInput(
            row.adjustmentAmount != null && row.adjustmentAmount !== 0
                ? String(row.adjustmentAmount)
                : String(row.totalAmount ?? '')
        );
        setAdjustReason('');
        setShowAdjustModal(true);
    };

    const handleConfirmAdjust = async () => {
        if (!adjustTarget) return;
        const amount = Number(adjustAmountInput);
        if (!amount || amount <= 0) {
            toastMessage(t('ar.daftar_piutang_page.adjust.invalid_amount', 'Masukkan nominal adjustment yang valid'), 'error');
            return;
        }
        if (!adjustReason.trim()) {
            toastMessage(t('ar.daftar_piutang_page.adjust.reason_required', 'Alasan wajib diisi'), 'error');
            return;
        }
        try {
            await adjustAr({
                arId: adjustTarget.arId,
                adjustmentAmount: amount,
                reason: adjustReason.trim(),
            }).unwrap();
            toastMessage(t('ar.daftar_piutang_page.adjust.toast_success', 'Adjustment berhasil disimpan'), 'success');
            setShowAdjustModal(false);
            setAdjustTarget(null);
            setAdjustAmountInput('');
            setAdjustReason('');
            refetch();
        } catch (error: any) {
            toastMessage(error?.data?.message || t('ar.daftar_piutang_page.adjust.toast_failed', 'Gagal menyimpan adjustment'), 'error');
        }
    };

    const parseNoteHistory = (historyJson?: string): any[] => {
        if (!historyJson) return [];
        try {
            const parsed = JSON.parse(historyJson);
            return Array.isArray(parsed) ? parsed : [];
        } catch {
            return [];
        }
    };

    const handleOpenNote = (row: ReceivableData) => {
        setNoteTarget(row);
        setNoteInput('');
        setShowNoteModal(true);
    };

    const handleConfirmAddNote = async () => {
        if (!noteTarget) return;
        if (!noteInput.trim()) {
            toastMessage(t('ar.daftar_piutang_page.note.reason_required', 'Catatan wajib diisi'), 'error');
            return;
        }
        try {
            await addNote({
                arId: noteTarget.arId,
                note: noteInput.trim(),
            }).unwrap();
            toastMessage(t('ar.daftar_piutang_page.note.toast_success', 'Catatan berhasil disimpan'), 'success');
            setShowNoteModal(false);
            setNoteTarget(null);
            setNoteInput('');
            refetch();
        } catch (error: any) {
            toastMessage(error?.data?.message || t('ar.daftar_piutang_page.note.toast_failed', 'Gagal menyimpan catatan'), 'error');
        }
    };

    const canSelect = (row: ReceivableData) => {
        // Can select if Pending (for posting) or Posting without voucher (for unposting)
        return row.paymentStatus === 'Pending' || (row.paymentStatus === 'Posting' && !row.voucherNumber);
    };

    const canPost = (row: ReceivableData) => row.paymentStatus === 'Pending';
    const canUnpost = (row: ReceivableData) => row.paymentStatus === 'Posting' && !row.voucherNumber;

    const handlePost = async () => {
        const arIds = Array.from(selectedRows).filter(id => {
            const row = records.find(r => r.arId === id);
            return row && canPost(row);
        });

        if (arIds.length === 0) {
            toastMessage(t('ar.daftar_piutang_page.toast.no_post_item'), 'error');
            return;
        }

        try {
            await postAR({ arIds }).unwrap();
            toastMessage(t('ar.daftar_piutang_page.toast.post_success', { count: arIds.length }), 'success');
            setSelectedRows(new Set());
            setShowPostModal(false);
            refetch();
        } catch (error: any) {
            toastMessage(error?.data?.message || t('ar.daftar_piutang_page.toast.post_failed'), 'error');
        }
    };

    const handleUnpost = async () => {
        const arIds = Array.from(selectedRows).filter(id => {
            const row = records.find(r => r.arId === id);
            return row && canUnpost(row);
        });

        if (arIds.length === 0) {
            toastMessage(t('ar.daftar_piutang_page.toast.no_unpost_item'), 'error');
            return;
        }

        try {
            await unpostAR({ arIds }).unwrap();
            toastMessage(t('ar.daftar_piutang_page.toast.unpost_success', { count: arIds.length }), 'success');
            setSelectedRows(new Set());
            setShowUnpostModal(false);
            refetch();
        } catch (error: any) {
            toastMessage(error?.data?.message || t('ar.daftar_piutang_page.toast.unpost_failed'), 'error');
        }
    };

    const records: ReceivableData[] = listData?.data?.data || listData?.data || [];
    const totalRecords = listData?.data?.totalData || listData?.totalData || 0;

    const selectedPendingCount = Array.from(selectedRows).filter(id => {
        const row = records.find(r => r.arId === id);
        return row && canPost(row);
    }).length;

    const selectedPostingCount = Array.from(selectedRows).filter(id => {
        const row = records.find(r => r.arId === id);
        return row && canUnpost(row);
    }).length;

    const columns: DataTableColumn<ReceivableData>[] = [
        {
            accessor: 'checkbox',
            title: (
                <input
                    type="checkbox"
                    className="form-checkbox"
                    onChange={(e) => handleSelectAll(e.target.checked)}
                    checked={records.filter(r => canSelect(r)).length > 0 &&
                        records.filter(r => canSelect(r)).every(r => selectedRows.has(r.arId))}
                />
            ),
            render: (row) => canSelect(row) ? (
                <input
                    type="checkbox"
                    className="form-checkbox"
                    checked={selectedRows.has(row.arId)}
                    onChange={(e) => handleRowSelect(row.arId, e.target.checked)}
                />
            ) : null,
            width: 50,
        },
        {
            accessor: 'transactionDate',
            title: t('ar.daftar_piutang_page.table.date'),
            render: (row) => row.transactionDate ? FormatDate(row.transactionDate) : '-',
            width: 120,
        },
        {
            accessor: 'patientName',
            title: t('jasa_medis.posting.table.patient_name', 'Pasien'),
            width: 280,
            render: (row) => (
                <div className="flex flex-col">
                    <span className="font-semibold text-gray-800 whitespace-normal leading-tight">{row.patientName || '-'}</span>
                    <span className="text-xs text-gray-500">{row.medicalRecordNo || '-'}</span>
                </div>
            )
        },
        {
            accessor: 'invoiceNo',
            title: 'Invoice No / Service Type',
            width: 200,
            render: (row) => (
                <div className="flex flex-col">
                    <span className="font-medium text-gray-800">{row.invoiceNo || '-'}</span>
                    <span className="text-xs text-gray-500">
                        {row.serviceType === 'rajal' ? 'Rawat Jalan' :
                            row.serviceType === 'ranap' ? 'Rawat Inap' : row.serviceType || '-'}
                    </span>
                </div>
            ),
        },
        {
            accessor: 'payerName',
            title: t('ar.daftar_piutang_page.table.insurance'),
            width: 250,
            render: (row) => (
                <div className="flex flex-col">
                    <span className="whitespace-normal leading-tight font-medium">{row.payerName}</span>
                    {row.sepNo && <span className="text-xs text-gray-500 mt-0.5">SEP: {row.sepNo}</span>}
                </div>
            )
        },
        {
            accessor: 'totalAmount',
            title: t('ar.daftar_piutang_page.table.amount'),
            textAlignment: 'right' as const,
            cellsStyle: { textAlign: 'right' },
            render: (row) => (
                <div className="flex flex-col items-end">
                    <span className="font-mono">
                        {FormatNumber(row.adjustmentAmount && row.adjustmentAmount !== row.totalAmount
                            ? row.adjustmentAmount : row.totalAmount)}
                    </span>
                    {row.adjustmentAmount != null && row.adjustmentAmount !== row.totalAmount && (
                        <span className="text-xs text-gray-400 line-through">
                            {FormatNumber(row.totalAmount)}
                        </span>
                    )}
                </div>
            ),
        },
        {
            accessor: 'paymentStatus',
            title: t('ar.daftar_piutang_page.table.status'),
            render: (row) => (
                <div className="flex items-center">
                    <span className={`px-2 py-1 rounded-full text-xs font-medium flex items-center gap-1 w-fit ${row.paymentStatus === 'Paid' ? 'bg-green-100 text-green-700' :
                        row.paymentStatus === 'Posting' ? 'bg-blue-100 text-blue-700' :
                            'bg-gray-100 text-gray-700'
                        }`}>
                        {row.paymentStatus === 'Paid' ? <CheckCircle2 className="w-3 h-3" /> :
                            row.status === 'VOID' ? <Ban className="w-3 h-3" /> :
                                <Clock className="w-3 h-3" />}
                        {row.status === 'VOID' ? 'VOID' : row.paymentStatus}
                        {row.voucherNumber && <span className="ml-1 text-[10px]">({row.voucherNumber})</span>}
                    </span>

                </div>
            ),
        },
        {
            accessor: 'notes',
            title: t('ar.daftar_piutang_page.table.notes', 'Catatan'),
            width: 220,
            render: (row) => {
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
                                <span className="text-[10px] text-gray-400">+{history.length - 1} {t('ar.daftar_piutang_page.note.more', 'catatan lain')}</span>
                            )}
                        </div>
                    </div>
                );
            },
        },
        {
            accessor: 'action',
            title: t('ar.daftar_piutang_page.table.action', 'Action'),
            width: 220,
            render: (row) => (
                <div className="flex items-center gap-1.5">
                    <button
                        type="button"
                        className="btn btn-sm btn-outline-primary p-1.5 flex items-center gap-1"
                        onClick={() => handleOpenChangePayer(row)}
                        title={t('ar.daftar_piutang_page.change_payer.title', 'Change Payer')}
                    >
                        <RefreshCw className="w-3.5 h-3.5" />
                        <span className="text-xs">{t('ar.daftar_piutang_page.change_payer.btn_change', 'Payer')}</span>
                    </button>
                    <button
                        type="button"
                        className="btn btn-sm btn-outline-warning p-1.5 flex items-center gap-1"
                        onClick={() => handleOpenAdjust(row)}
                        title={t('ar.daftar_piutang_page.adjust.title', 'Adjust Amount')}
                    >
                        <Pencil className="w-3.5 h-3.5" />
                        <span className="text-xs">{t('ar.daftar_piutang_page.adjust.btn_adjust', 'Adjust')}</span>
                    </button>
                    <button
                        type="button"
                        className="btn btn-sm btn-outline-info p-1.5 flex items-center gap-1"
                        onClick={() => handleOpenNote(row)}
                        title={t('ar.daftar_piutang_page.note.title', 'Add Note')}
                    >
                        <StickyNote className="w-3.5 h-3.5" />
                        <span className="text-xs">{t('ar.daftar_piutang_page.note.btn_add', 'Note')}</span>
                    </button>
                    <TimelineButton
                        module={AUDIT_MODULE.ACCOUNT_RECEIVABLE}
                        refId={row.arId}
                        title={row.arNumber}
                    />
                </div>
            ),
        },
    ];

    return (
        <div className="space-y-6">
            <Card>
                <CardHeader className="border-b border-gray-100 pb-4">
                    <div className="flex flex-col gap-6">
                        <div className="flex items-center gap-3">
                            <div className="p-2 rounded-lg bg-primary/10">
                                <FileText className="h-5 w-5 text-primary" />
                            </div>
                            <div>
                                <CardTitle className="text-lg">{t('ar.daftar_piutang_page.title')}</CardTitle>
                                <p className="text-sm text-gray-500 mt-0.5">{t('ar.daftar_piutang_page.subtitle')}</p>
                            </div>
                        </div>

                        <div className="flex flex-wrap items-center gap-3 w-full">
                            <div className="w-full md:w-[200px]">
                                <div className="relative">
                                    <input
                                        type="text"
                                        placeholder={t('Search...', 'Search...')}
                                        className="form-input w-full h-10 pl-9 border-slate-200"
                                        value={keyword}
                                        onChange={(e) => handleKeywordChange(e.target.value)}
                                    />
                                    <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <Search className="h-4 w-4 text-gray-400" />
                                    </div>
                                </div>
                            </div>
                            <div className="w-[210px]">
                                <SelectSearch
                                    placeholder={t('ar.daftar_piutang_page.filter.insurance')}
                                    options={insuranceOptionsFormatted}
                                    onChange={(opts: any) => handleInsuranceChange(opts)}
                                    onInputChange={(val) => setPayerSearch(val)}
                                    value={selectedInsurance}
                                    isMulti
                                    isClearable
                                    isLoading={isLoadingInsurance}
                                    styles={{
                                        control: (base) => ({ ...base, minHeight: '40px', borderColor: '#e2e8f0' }),
                                        menuPortal: (base) => ({ ...base, zIndex: 9999 }),
                                    }}
                                    menuPortalTarget={document.body}
                                />
                            </div>

                            <div className="w-[180px]">
                                <SelectSearch
                                    placeholder="Payer Type"
                                    options={payerTypeOptions}
                                    onChange={(opt: any) => { setSelectedPayerType(opt?.value || ''); resetSelection(); }}
                                    value={payerTypeOptions.find((o) => o.value === selectedPayerType) || null}
                                    isClearable
                                    styles={{
                                        control: (base) => ({ ...base, minHeight: '40px', borderColor: '#e2e8f0' }),
                                        menuPortal: (base) => ({ ...base, zIndex: 9999 }),
                                    }}
                                    menuPortalTarget={document.body}
                                />
                            </div>

                            <div className="w-[210px]">
                                <SelectSearch
                                    placeholder={t('ar.daftar_piutang_page.filter.service')}
                                    options={serviceTypeOptions}
                                    onChange={(opt: any) => handleServiceTypeChange(opt?.value || '')}
                                    value={serviceTypeOptions.find((o) => o.value === selectedServiceType) || null}
                                    isClearable
                                    styles={{
                                        control: (base) => ({ ...base, minHeight: '40px', borderColor: '#e2e8f0' }),
                                        menuPortal: (base) => ({ ...base, zIndex: 9999 }),
                                    }}
                                    menuPortalTarget={document.body}
                                />
                            </div>

                            <div className="w-[160px]">
                                <SelectSearch
                                    placeholder={t('ar.daftar_piutang_page.filter.status')}
                                    options={statusOptions}
                                    onChange={(opt: any) => handleStatusChange(opt?.value || '')}
                                    value={statusOptions.find((o) => o.value === selectedStatus) || null}
                                    isClearable
                                    styles={{
                                        control: (base) => ({ ...base, minHeight: '40px', borderColor: '#e2e8f0' }),
                                        menuPortal: (base) => ({ ...base, zIndex: 9999 }),
                                    }}
                                    menuPortalTarget={document.body}
                                />
                            </div>

                            <div className="relative w-[200px]">
                                <Flatpickr
                                    options={{ mode: 'range', dateFormat: 'd/m/Y' }}
                                    className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ltr:pl-9 rtl:pr-9"
                                    onChange={handleDateChange}
                                    placeholder={t('ar.daftar_piutang_page.filter.period')}
                                />
                                <div className="absolute inset-y-0 ltr:left-3 rtl:right-3 flex items-center pointer-events-none text-gray-400">
                                    <IconCalendar className="w-4 h-4" />
                                </div>
                            </div>

                            <Button
                                variant="outline"
                                className="gap-2 h-10 ml-auto md:ml-0"
                                onClick={handleExport}
                                disabled={isExporting}
                            >
                                {isExporting ? <Loader2 className="h-4 w-4 animate-spin" /> : <Download className="h-4 w-4" />}
                                <span className="hidden sm:inline">Export</span>
                            </Button>

                            <Button
                                variant="outline"
                                className="gap-2 h-10"
                                onClick={() => setShowBulkPostModal(true)}
                            >
                                <FileSpreadsheet className="h-4 w-4" />
                                <span className="hidden sm:inline">Bulk Post</span>
                            </Button>

                            <input
                                type="file"
                                ref={inputFileAdjustment}
                                style={{ display: 'none' }}
                                onChange={handleAdjustmentUpload}
                                accept=".xlsx,.xls,.txt,.csv"
                            />
                            <Button
                                variant="outline"
                                className="gap-2 h-10"
                                onClick={() => inputFileAdjustment.current?.click()}
                                disabled={isUploadingAdjustment}
                            >
                                {isUploadingAdjustment ? <Loader2 className="h-4 w-4 animate-spin" /> : <UploadCloud className="h-4 w-4" />}
                                <span className="hidden sm:inline">Upload Adjustment</span>
                            </Button>
                        </div>
                    </div>
                </CardHeader>

                {/* Action Bar */}
                {selectedRows.size > 0 && (
                    <div className="px-4 py-3 bg-blue-50 border-b flex items-center justify-between">
                        <span className="text-sm text-blue-700 font-medium">
                            {selectedRows.size} {t('ar.daftar_piutang_page.action.selected')}
                        </span>
                        <div className="flex items-center gap-2">
                            {selectedPendingCount > 0 && (
                                <Button
                                    variant="default"
                                    size="sm"
                                    className="gap-2 bg-green-600 hover:bg-green-700"
                                    onClick={() => setShowPostModal(true)}
                                    disabled={isPosting}
                                >
                                    {isPosting ? <Loader2 className="h-4 w-4 animate-spin" /> : <Send className="h-4 w-4" />}
                                    {t('ar.daftar_piutang_page.action.post')} ({selectedPendingCount})
                                </Button>
                            )}
                            {selectedPostingCount > 0 && (
                                <Button
                                    variant="outline"
                                    size="sm"
                                    className="gap-2"
                                    onClick={() => setShowUnpostModal(true)}
                                    disabled={isUnposting}
                                >
                                    {isUnposting ? <Loader2 className="h-4 w-4 animate-spin" /> : <Undo2 className="h-4 w-4" />}
                                    {t('ar.daftar_piutang_page.action.unpost')} ({selectedPostingCount})
                                </Button>
                            )}
                            <Button
                                variant="outline"
                                size="sm"
                                className="gap-2 text-red-600 border-red-200 hover:bg-red-50"
                                onClick={handleVoid}
                                disabled={isVoiding}
                            >
                                {isVoiding ? <Loader2 className="h-4 w-4 animate-spin" /> : <Ban className="h-4 w-4" />}
                                Void
                            </Button>

                        </div>
                    </div>
                )}

                <CardContent className="p-0">
                    <div className="datatables">
                        <DataTable
                            highlightOnHover
                            className="whitespace-nowrap table-hover"
                            records={records}
                            columns={columns}
                            totalRecords={totalRecords}
                            recordsPerPage={pageSize}
                            page={page}
                            onPageChange={setPage}
                            recordsPerPageOptions={PAGE_SIZES}
                            onRecordsPerPageChange={setPageSize}
                            minHeight={300}
                            fetching={isLoading}
                            loaderVariant="dots"
                            loaderColor="#4361ee"
                            noRecordsText={t('common.no_records')}
                            paginationText={({ from, to, totalRecords }) =>
                                t('common.pagination', { from, to, total: totalRecords })
                            }
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

            {/* Post Confirmation Modal */}
            <ConfirmationModal
                isOpen={showPostModal}
                onClose={() => setShowPostModal(false)}
                onConfirm={handlePost}
                title={t('ar.daftar_piutang_page.modal.post_title')}
                message={t('ar.daftar_piutang_page.modal.post_message', { count: selectedPendingCount })}
                confirmText={t('ar.daftar_piutang_page.modal.post_btn')}
                loading={isPosting}
            />

            {/* Unpost Confirmation Modal */}
            <ConfirmationModal
                isOpen={showUnpostModal}
                onClose={() => setShowUnpostModal(false)}
                onConfirm={handleUnpost}
                title={t('ar.daftar_piutang_page.modal.unpost_title')}
                message={t('ar.daftar_piutang_page.modal.unpost_message', { count: selectedPostingCount })}
                confirmText={t('ar.daftar_piutang_page.modal.unpost_btn')}
                loading={isUnposting}
            />

            {/* Void Confirmation Modal */}
            <ConfirmationModal
                isOpen={showVoidModal}
                onClose={() => setShowVoidModal(false)}
                onConfirm={handleConfirmVoid}
                title={t('ar.daftar_piutang_page.modal.void_title', 'Void Payment')}
                message={t('ar.daftar_piutang_page.modal.void_message', 'Are you sure you want to void these items? This action cannot be undone.')}
                confirmText={t('ar.daftar_piutang_page.modal.void_btn', 'Void')}
                loading={isVoiding}
                variant="danger"
            />



            {/* Bulk Post Modal */}
            <ConfirmationModal
                isOpen={showBulkPostModal}
                onClose={() => { setShowBulkPostModal(false); setBulkPostFile(null); }}
                onConfirm={handleBulkPostSubmit}
                title={t('ar.daftar_piutang_page.modal.bulk_post_title', 'Bulk Post via Excel')}
                confirmText={t('ar.daftar_piutang_page.modal.bulk_post_btn', 'Process Status Update')}
                loading={isBulkPosting}
                customContent={
                    <div className="mt-4">
                        <p className="text-sm text-gray-500 mb-2">
                            1. Download Export Data first.<br />
                            2. Changes Status column to 'Posting' for items you want to post.<br />
                            3. Upload the file here.
                        </p>
                        <input
                            type="file"
                            accept=".xlsx, .xls"
                            onChange={handleBulkPostFileChange}
                            className="block w-full text-sm text-slate-500
                              file:mr-4 file:py-2 file:px-4
                              file:rounded-full file:border-0
                              file:text-sm file:font-semibold
                              file:bg-violet-50 file:text-violet-700
                              hover:file:bg-violet-100
                            "
                        />
                        {bulkPostFile && <p className="mt-2 text-sm text-green-600 font-medium">Selected: {bulkPostFile.name}</p>}
                    </div>
                }
            />

            {/* Change Payer Modal */}
            <ConfirmationModal
                isOpen={showChangePayerModal}
                onClose={() => { setShowChangePayerModal(false); setChangePayerTarget(null); }}
                onConfirm={handleConfirmChangePayer}
                title={t('ar.daftar_piutang_page.change_payer.title', 'Ganti Payer')}
                confirmText={t('ar.daftar_piutang_page.change_payer.save', 'Simpan')}
                loading={isChangingPayer}
                keepOpenOnConfirm={true}
                customContent={
                    <div className="mt-2 space-y-4">
                        {/* Current Payer Info */}
                        {changePayerTarget && (
                            <div className="bg-gray-50 rounded-lg p-3">
                                <p className="text-xs text-gray-500 mb-1">{t('ar.daftar_piutang_page.change_payer.current_payer', 'Payer Saat Ini')}</p>
                                <p className="font-semibold text-gray-800">{changePayerTarget.payerName || '-'}</p>
                                <p className="text-xs text-gray-500">ID: {changePayerTarget.payerId || '-'}</p>
                            </div>
                        )}

                        {/* New Payer Dropdown */}
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">{t('ar.daftar_piutang_page.change_payer.new_payer', 'Payer Baru')} <span className="text-red-500">*</span></label>
                            <SelectSearch
                                placeholder={t('ar.daftar_piutang_page.change_payer.new_payer_placeholder', 'Pilih payer baru...')}
                                options={payerOptionsFormatted}
                                onChange={(opt: any) => setSelectedNewPayer(opt)}
                                onInputChange={(val: string) => setChangePayerSearch(val)}
                                value={selectedNewPayer}
                                isClearable
                                isLoading={isLoadingPayerOptions}
                                styles={{
                                    control: (base: any) => ({ ...base, minHeight: '40px', borderColor: '#e2e8f0' }),
                                    menuPortal: (base: any) => ({ ...base, zIndex: 99999 }),
                                }}
                                menuPortalTarget={document.body}
                            />
                        </div>

                        {/* Reason */}
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">{t('ar.daftar_piutang_page.change_payer.reason', 'Alasan Perubahan')} <span className="text-red-500">*</span></label>
                            <textarea
                                className="form-textarea w-full rounded-md border-gray-300"
                                rows={3}
                                placeholder={t('ar.daftar_piutang_page.change_payer.reason_placeholder', 'Masukkan alasan perubahan payer...')}
                                value={changePayerReason}
                                onChange={(e) => setChangePayerReason(e.target.value)}
                            />
                        </div>

                        {/* History */}
                        {changePayerTarget && parsePayerHistory(changePayerTarget.payerChangeHistory).length > 0 && (
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">{t('ar.daftar_piutang_page.change_payer.history', 'Riwayat Perubahan')}</label>
                                <div className="max-h-48 overflow-y-auto space-y-2">
                                    {parsePayerHistory(changePayerTarget.payerChangeHistory).map((h: any, idx: number) => (
                                        <div key={idx} className="bg-blue-50 border border-blue-100 rounded-lg p-3 text-sm">
                                            <div className="flex justify-between items-start mb-1">
                                                <span className="font-medium text-blue-800">{t('ar.daftar_piutang_page.change_payer.change_number', 'Perubahan #{{number}}', { number: idx + 1 })}</span>
                                                <span className="text-xs text-gray-500">{h.changedDate}</span>
                                            </div>
                                            <div className="text-gray-600">
                                                <span className="text-red-600 line-through">{h.payerBefore?.name || h.payerBefore?.id || '-'}</span>
                                                {' → '}
                                                <span className="text-green-700 font-medium">{h.payerAfter?.name || h.payerAfter?.id || '-'}</span>
                                            </div>
                                            <p className="text-xs text-gray-500 mt-1">{t('ar.daftar_piutang_page.change_payer.reason_label', 'Alasan')}: {h.reason}</p>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        )}
                    </div>
                }
            />

            {/* Adjust Amount Modal */}
            <ConfirmationModal
                isOpen={showAdjustModal}
                onClose={() => { setShowAdjustModal(false); setAdjustTarget(null); }}
                onConfirm={handleConfirmAdjust}
                title={t('ar.daftar_piutang_page.adjust.title', 'Adjust Amount')}
                confirmText={t('ar.daftar_piutang_page.adjust.save', 'Simpan')}
                loading={isAdjusting}
                keepOpenOnConfirm={true}
                customContent={
                    <div className="mt-2 space-y-4">
                        {/* Current Info */}
                        {adjustTarget && (
                            <div className="bg-gray-50 rounded-lg p-3 space-y-1">
                                <div className="flex justify-between items-center">
                                    <span className="text-xs text-gray-500">{t('ar.daftar_piutang_page.adjust.patient', 'Pasien')}</span>
                                    <span className="text-sm font-medium text-gray-800">{adjustTarget.patientName || '-'}</span>
                                </div>
                                <div className="flex justify-between items-center">
                                    <span className="text-xs text-gray-500">Invoice</span>
                                    <span className="text-sm font-medium text-gray-800">{adjustTarget.invoiceNo || adjustTarget.arNumber || '-'}</span>
                                </div>
                                <div className="flex justify-between items-center pt-1 border-t border-gray-200">
                                    <span className="text-xs text-gray-500">{t('ar.daftar_piutang_page.adjust.original_amount', 'Total Klaim')}</span>
                                    <span className="text-sm font-mono text-gray-800">{FormatNumber(adjustTarget.totalAmount || 0)}</span>
                                </div>
                                <div className="flex justify-between items-center">
                                    <span className="text-xs text-gray-500">{t('ar.daftar_piutang_page.adjust.current_adjustment', 'Adjustment Saat Ini')}</span>
                                    <span className="text-sm font-mono font-semibold text-gray-800">
                                        {adjustTarget.adjustmentAmount != null && adjustTarget.adjustmentAmount !== 0
                                            ? FormatNumber(adjustTarget.adjustmentAmount)
                                            : '-'}
                                    </span>
                                </div>
                            </div>
                        )}

                        {/* New Adjustment Amount */}
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">
                                {t('ar.daftar_piutang_page.adjust.new_amount', 'Nominal Adjustment')} <span className="text-red-500">*</span>
                            </label>
                            <input
                                type="number"
                                inputMode="decimal"
                                min="0"
                                step="0.01"
                                className="form-input w-full rounded-md border-gray-300"
                                placeholder={t('ar.daftar_piutang_page.adjust.new_amount_placeholder', 'Masukkan nominal disetujui...')}
                                value={adjustAmountInput}
                                onChange={(e) => setAdjustAmountInput(e.target.value)}
                            />
                        </div>

                        {/* Reason */}
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">
                                {t('ar.daftar_piutang_page.adjust.reason', 'Alasan Adjustment')} <span className="text-red-500">*</span>
                            </label>
                            <textarea
                                className="form-textarea w-full rounded-md border-gray-300"
                                rows={3}
                                placeholder={t('ar.daftar_piutang_page.adjust.reason_placeholder', 'Masukkan alasan adjustment...')}
                                value={adjustReason}
                                onChange={(e) => setAdjustReason(e.target.value)}
                            />
                        </div>

                        {/* History */}
                        {adjustTarget && parseAdjustmentHistory(adjustTarget.adjustmentHistory).length > 0 && (
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    {t('ar.daftar_piutang_page.adjust.history', 'Riwayat Adjustment')}
                                </label>
                                <div className="max-h-48 overflow-y-auto space-y-2">
                                    {parseAdjustmentHistory(adjustTarget.adjustmentHistory).map((h: any, idx: number) => (
                                        <div key={idx} className="bg-amber-50 border border-amber-100 rounded-lg p-3 text-sm">
                                            <div className="flex justify-between items-start mb-1">
                                                <span className="font-medium text-amber-800">
                                                    {t('ar.daftar_piutang_page.adjust.change_number', 'Adjustment #{{number}}', { number: idx + 1 })}
                                                </span>
                                                <span className="text-xs text-gray-500">{h.changedDate}</span>
                                            </div>
                                            <div className="text-gray-600 font-mono text-xs">
                                                <span className="text-red-600 line-through">
                                                    {h.amountBefore != null && h.amountBefore !== 0 ? FormatNumber(h.amountBefore) : '-'}
                                                </span>
                                                {' → '}
                                                <span className="text-green-700 font-semibold">{FormatNumber(h.amountAfter || 0)}</span>
                                            </div>
                                            <p className="text-xs text-gray-500 mt-1">
                                                {t('ar.daftar_piutang_page.adjust.reason_label', 'Alasan')}: {h.reason || '-'}
                                            </p>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        )}
                    </div>
                }
            />

            {/* Add Note Modal */}
            <ConfirmationModal
                isOpen={showNoteModal}
                onClose={() => { setShowNoteModal(false); setNoteTarget(null); setNoteInput(''); }}
                onConfirm={handleConfirmAddNote}
                title={t('ar.daftar_piutang_page.note.title', 'Tambah Catatan')}
                confirmText={t('ar.daftar_piutang_page.note.save', 'Simpan')}
                loading={isAddingNote}
                keepOpenOnConfirm={true}
                customContent={
                    <div className="mt-2 space-y-4">
                        {/* Current Info */}
                        {noteTarget && (
                            <div className="bg-gray-50 rounded-lg p-3 space-y-1">
                                <div className="flex justify-between items-center">
                                    <span className="text-xs text-gray-500">{t('ar.daftar_piutang_page.note.patient', 'Pasien')}</span>
                                    <span className="text-sm font-medium text-gray-800">{noteTarget.patientName || '-'}</span>
                                </div>
                                <div className="flex justify-between items-center">
                                    <span className="text-xs text-gray-500">Invoice</span>
                                    <span className="text-sm font-medium text-gray-800">{noteTarget.invoiceNo || noteTarget.arNumber || '-'}</span>
                                </div>
                            </div>
                        )}

                        {/* Note Input */}
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">
                                {t('ar.daftar_piutang_page.note.label', 'Catatan')} <span className="text-red-500">*</span>
                            </label>
                            <textarea
                                className="form-textarea w-full rounded-md border-gray-300"
                                rows={3}
                                placeholder={t('ar.daftar_piutang_page.note.placeholder', 'Masukkan catatan...')}
                                value={noteInput}
                                onChange={(e) => setNoteInput(e.target.value)}
                            />
                        </div>

                        {/* History */}
                        {noteTarget && parseNoteHistory(noteTarget.noteHistory).length > 0 && (
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    {t('ar.daftar_piutang_page.note.history', 'Riwayat Catatan')}
                                </label>
                                <div className="max-h-48 overflow-y-auto space-y-2">
                                    {[...parseNoteHistory(noteTarget.noteHistory)].reverse().map((h: any, idx: number) => (
                                        <div key={idx} className="bg-sky-50 border border-sky-100 rounded-lg p-3 text-sm">
                                            <div className="flex justify-between items-start mb-1">
                                                <span className="font-medium text-sky-800">
                                                    {t('ar.daftar_piutang_page.note.entry_number', 'Catatan #{{number}}', { number: parseNoteHistory(noteTarget.noteHistory).length - idx })}
                                                </span>
                                                <span className="text-xs text-gray-500">{h.createdDate}</span>
                                            </div>
                                            <p className="text-gray-700 whitespace-pre-wrap break-words">{h.note || '-'}</p>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        )}
                    </div>
                }
            />
        </div>
    );
};

export default DaftarAR;
