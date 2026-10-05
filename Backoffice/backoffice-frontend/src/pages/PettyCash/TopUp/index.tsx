import { useEffect, useState } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { useTranslation } from 'react-i18next';
import { useNavigate } from 'react-router-dom';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '@/store/themeConfigSlice';
import { DataTable, DataTableColumn } from 'mantine-datatable';
import Flatpickr from 'react-flatpickr';
import 'flatpickr/dist/flatpickr.css';
import SelectSearch from 'react-select';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import TimelineButton from '@/components/ActivityTimeline/TimelineButton';
import { AUDIT_MODULE } from '@/components/ActivityTimeline/modules';
import IconCalendar from '@/components/Icon/IconCalendar';
import {
    useGetPettyCashTopUpsQuery,
    useGetPettyCashUnitOptionsQuery,
    useApprovePettyCashTopUpMutation,
    useRejectPettyCashTopUpMutation,
    useVoidPettyCashTopUpMutation,
    useExportPettyCashTopUpsMutation,
} from '@/store/api/pettyCash/pettyCashApiSlice';
import { FormatNumber } from '@/utils/formatNumber';
import { FormatDate } from '@/utils/formatDate';
import { toastMessage } from '@/utils/toastUtils';
import { TrendingUp, Plus, Check, X, Ban, Download, Eye, Loader2, CheckCircle } from 'lucide-react';
import ConfirmationModal from '@/components/ConfirmationModal';

interface TopUpData {
    topUpId: number;
    topUpNo: string;
    topUpDate: string;
    unitId: number;
    unitCode: string;
    unitName: string;
    fromCoaCode: string;
    fromCoaName: string;
    amount: number;
    status: string;
    transactionId: number | null;
    journalNo: string | null;
    notes: string | null;
    approvedDate: string | null;
    createdDate: string;
}

const statusOptions = [
    { value: '', label: 'Semua Status' },
    { value: 'Pending', label: 'Menunggu Persetujuan' },
    { value: 'Posted', label: 'Disetujui (Posted)' },
    { value: 'Rejected', label: 'Ditolak' },
];

const PettyCashTopUp = () => {
    const dispatch = useDispatch();
    const user = useSelector((state: any) => state.auth.user);
    const navigate = useNavigate();
    const { t } = useTranslation();

    useEffect(() => {
        dispatch(setPageTitle(t('petty_cash.topup.title', 'Top Up Kas Kecil')));
        dispatch(setTitle(t('petty_cash.topup.title', 'Top Up Kas Kecil')));
        dispatch(setBreadcrumbTitle(['Dashboard', t('sidebar.petty_cash', 'Petty Cash'), t('petty_cash.topup.title', 'Top Up')]));
    }, [dispatch, t]);

    // Filter states
    const [selectedUnit, setSelectedUnit] = useState<string>('');
    const [selectedStatus, setSelectedStatus] = useState<string>('');
    const [startDate, setStartDate] = useState<Date | null>(null);
    const [endDate, setEndDate] = useState<Date | null>(null);

    // Pagination
    const [page, setPage] = useState(1);
    const [pageSize, setPageSize] = useState(10);
    const PAGE_SIZES = [10, 20, 30, 50];

    // Modal states
    const [showApproveModal, setShowApproveModal] = useState(false);
    const [showRejectModal, setShowRejectModal] = useState(false);
    const [showVoidModal, setShowVoidModal] = useState(false);
    const [selectedTopUp, setSelectedTopUp] = useState<TopUpData | null>(null);
    const [rejectReason, setRejectReason] = useState<string>('');

    // API
    const { data: unitOptions } = useGetPettyCashUnitOptionsQuery({});

    const formatDateForAPI = (date: Date | null): string | null => {
        if (!date) return null;
        return new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate())).toISOString();
    };

    const { data: listData, isLoading, refetch } = useGetPettyCashTopUpsQuery({
        unitId: selectedUnit || undefined,
        status: selectedStatus || undefined,
        startDate: formatDateForAPI(startDate),
        endDate: formatDateForAPI(endDate),
        page,
        pageSize,
    });

    const [approveTopUp, { isLoading: isApproving }] = useApprovePettyCashTopUpMutation();
    const [rejectTopUp, { isLoading: isRejecting }] = useRejectPettyCashTopUpMutation();
    const [voidTopUp, { isLoading: isVoiding }] = useVoidPettyCashTopUpMutation();
    const [exportTopUps, { isLoading: isExporting }] = useExportPettyCashTopUpsMutation();

    const records: TopUpData[] = listData?.data?.data || [];
    const totalRecords = listData?.data?.totalData || 0;

    const unitOptionsFormatted = [
        { value: '', label: 'Semua Unit' },
        ...(unitOptions?.data || []).map((u: any) => ({ value: String(u.value), label: u.label })),
    ];

    const handleDateChange = (dates: Date[]) => {
        if (dates.length >= 1) setStartDate(dates[0]);
        if (dates.length >= 2) setEndDate(dates[1]);
        if (dates.length === 0) {
            setStartDate(null);
            setEndDate(null);
        }
    };

    const handleApprove = (topUp: TopUpData) => {
        setSelectedTopUp(topUp);
        setShowApproveModal(true);
    };

    const handleReject = (topUp: TopUpData) => {
        setSelectedTopUp(topUp);
        setRejectReason('');
        setShowRejectModal(true);
    };

    const handleVoid = (topUp: TopUpData) => {
        setSelectedTopUp(topUp);
        setShowVoidModal(true);
    };

    const handleConfirmApprove = async () => {
        if (!selectedTopUp) return;
        try {
            await approveTopUp(selectedTopUp.topUpId).unwrap();
            toastMessage('Top up berhasil disetujui dan jurnal dibuat', 'success');
            setShowApproveModal(false);
            refetch();
        } catch (error: any) {
            toastMessage(error?.data?.message || 'Gagal menyetujui top up', 'error');
        }
    };

    const handleConfirmReject = async () => {
        if (!selectedTopUp) return;
        try {
            await rejectTopUp({ id: selectedTopUp.topUpId, reason: rejectReason }).unwrap();
            toastMessage('Top up berhasil ditolak', 'success');
            setShowRejectModal(false);
            refetch();
        } catch (error: any) {
            toastMessage(error?.data?.message || 'Gagal menolak top up', 'error');
        }
    };

    const handleConfirmVoid = async () => {
        if (!selectedTopUp) return;
        try {
            await voidTopUp(selectedTopUp.topUpId).unwrap();
            toastMessage('Top up berhasil di-void', 'success');
            setShowVoidModal(false);
            refetch();
        } catch (error: any) {
            toastMessage(error?.data?.message || 'Gagal void top up', 'error');
        }
    };

    const handleExport = async () => {
        try {
            const blob = await exportTopUps({
                unitId: selectedUnit || undefined,
                startDate: formatDateForAPI(startDate),
                endDate: formatDateForAPI(endDate),
            }).unwrap();

            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', `topup_kas_kecil_${new Date().toISOString().split('T')[0]}.xlsx`);
            document.body.appendChild(link);
            link.click();
            link.remove();
            window.URL.revokeObjectURL(url);
        } catch {
            toastMessage('Gagal export data', 'error');
        }
    };

    const getStatusBadge = (status: string) => {
        const statusConfig: Record<string, { bg: string; text: string; label: string }> = {
            Pending: { bg: 'bg-yellow-100', text: 'text-yellow-700', label: 'Menunggu Persetujuan' },
            Approved: { bg: 'bg-green-100', text: 'text-green-700', label: 'Disetujui' },
            Posted: { bg: 'bg-green-100', text: 'text-green-700', label: 'Disetujui (Posted)' },
            Rejected: { bg: 'bg-red-100', text: 'text-red-700', label: 'Ditolak' },
            Void: { bg: 'bg-gray-100', text: 'text-gray-700', label: 'Dibatalkan' },
        };
        const config = statusConfig[status] || { bg: 'bg-gray-100', text: 'text-gray-700', label: status };
        return (
            <span className={`px-2 py-1 rounded-full text-xs font-medium ${config.bg} ${config.text}`}>
                {config.label}
            </span>
        );
    };

    const columns: DataTableColumn<TopUpData>[] = [
        {
            accessor: 'topUpNo',
            title: 'No. Top Up',
            width: 180,
        },
        {
            accessor: 'topUpDate',
            title: 'Tanggal',
            width: 120,
            render: (row) => FormatDate(row.topUpDate),
        },
        {
            accessor: 'unitName',
            title: 'Unit',
            render: (row) => (
                <div className="flex flex-col">
                    <span className="font-medium">{row.unitName}</span>
                    <span className="text-xs text-gray-500">{row.unitCode}</span>
                </div>
            ),
        },
        {
            accessor: 'fromCoaName',
            title: 'Sumber Dana',
            render: (row) => (
                <div className="flex flex-col">
                    <span className="font-medium">{row.fromCoaCode}</span>
                    <span className="text-xs text-gray-500">{row.fromCoaName}</span>
                </div>
            ),
        },
        {
            accessor: 'amount',
            title: 'Jumlah',
            textAlignment: 'right' as const,
            render: (row) => <span className="font-mono font-medium text-green-600">{FormatNumber(row.amount)}</span>,
        },
        {
            accessor: 'journalNo',
            title: 'No. Jurnal',
            width: 180,
            render: (row) => row.journalNo ? (
                <span className="text-gray-700 font-medium">
                    {row.journalNo}
                </span>
            ) : '-',
        },
        {
            accessor: 'status',
            title: 'Status',
            render: (row) => getStatusBadge(row.status),
        },
        {
            accessor: 'actions',
            title: 'Aksi',
            width: 130,
            render: (row) => (
                <div className="flex items-center gap-1">
                    <Button
                        variant="ghost"
                        size="sm"
                        onClick={() => navigate(`/petty-cash/topup/${row.topUpId}`)}
                    >
                        <Eye className="h-4 w-4" />
                    </Button>
                    <TimelineButton
                        module={AUDIT_MODULE.PETTY_CASH_TOPUP}
                        refId={row.topUpId}
                        title={row.topUpNo}
                    />
                </div>
            ),
        },
    ];

    return (
        <div className="space-y-6">
            <Card>
                <CardHeader className="border-b border-gray-100 pb-4">
                    <div className="flex flex-col gap-4">
                        <div className="flex items-center justify-between">
                            <div className="flex items-center gap-3">
                                <div className="p-2 rounded-lg bg-primary/10">
                                    <TrendingUp className="h-5 w-5 text-primary" />
                                </div>
                                <div>
                                    <CardTitle className="text-lg">{t('petty_cash.topup.title', 'Top Up Kas Kecil')}</CardTitle>
                                    <p className="text-sm text-gray-500 mt-0.5">Pengisian ulang kas kecil dari kas induk</p>
                                </div>
                            </div>
                            <Button className="gap-2" onClick={() => navigate('/petty-cash/topup/create')}>
                                <Plus className="h-4 w-4" />
                                Buat Top Up
                            </Button>
                        </div>

                        <div className="flex flex-wrap items-center gap-3">
                            {user?.role?.roleID !== 5 && (
                                <div className="w-48">
                                    <SelectSearch
                                        placeholder="Unit"
                                        options={unitOptionsFormatted}
                                        onChange={(opt: any) => { setSelectedUnit(opt?.value || ''); setPage(1); }}
                                        value={unitOptionsFormatted.find((o) => o.value === selectedUnit) || null}
                                        isClearable
                                        styles={{
                                            control: (base) => ({ ...base, minHeight: '40px', borderColor: '#e2e8f0' }),
                                            menuPortal: (base) => ({ ...base, zIndex: 9999 }),
                                        }}
                                        menuPortalTarget={document.body}
                                    />
                                </div>
                            )}

                            <div className="w-40">
                                <SelectSearch
                                    placeholder="Status"
                                    options={statusOptions}
                                    onChange={(opt: any) => { setSelectedStatus(opt?.value || ''); setPage(1); }}
                                    value={statusOptions.find((o) => o.value === selectedStatus) || null}
                                    isClearable
                                    styles={{
                                        control: (base) => ({ ...base, minHeight: '40px', borderColor: '#e2e8f0' }),
                                        menuPortal: (base) => ({ ...base, zIndex: 9999 }),
                                    }}
                                    menuPortalTarget={document.body}
                                />
                            </div>

                            <div className="relative w-48">
                                <Flatpickr
                                    options={{ mode: 'range', dateFormat: 'd/m/Y' }}
                                    className="form-input w-full h-10 pl-9 border-slate-200"
                                    onChange={handleDateChange}
                                    placeholder="Periode"
                                />
                                <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <IconCalendar className="h-4 w-4 text-gray-400" />
                                </div>
                            </div>

                            <Button
                                variant="outline"
                                className="gap-2 h-10 ml-auto"
                                onClick={handleExport}
                                disabled={isExporting}
                            >
                                {isExporting ? <Loader2 className="h-4 w-4 animate-spin" /> : <Download className="h-4 w-4" />}
                                Export
                            </Button>
                        </div>
                    </div>
                </CardHeader>

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
                            noRecordsText="Tidak ada data"
                            paginationText={({ from, to, totalRecords }) =>
                                `Menampilkan ${from} - ${to} dari ${totalRecords} data`
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

            {/* Approve Modal */}
            <ConfirmationModal
                isOpen={showApproveModal}
                onClose={() => setShowApproveModal(false)}
                onConfirm={handleConfirmApprove}
                title="Setujui Top Up"
                message={`Apakah Anda yakin ingin menyetujui top up "${selectedTopUp?.topUpNo}"? Ini akan mentransfer dana ${FormatNumber(selectedTopUp?.amount || 0)} ke unit kas kecil dan membuat jurnal otomatis.`}
                confirmText="Setujui"
                loading={isApproving}
            />

            {/* Reject Modal */}
            {showRejectModal && (
                <div className="fixed inset-0 z-50 flex items-center justify-center">
                    <div className="fixed inset-0 bg-black/50" onClick={() => setShowRejectModal(false)} />
                    <div className="relative bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
                        <div className="p-6 border-b">
                            <h2 className="text-lg font-semibold">Tolak Top Up</h2>
                        </div>
                        <div className="p-6 space-y-4">
                            <p className="text-gray-600">
                                No. Top Up: <strong>{selectedTopUp?.topUpNo}</strong>
                            </p>
                            <div>
                                <label className="block text-sm font-medium mb-1">Alasan Penolakan</label>
                                <textarea
                                    className="form-input w-full"
                                    rows={3}
                                    value={rejectReason}
                                    onChange={(e) => setRejectReason(e.target.value)}
                                    placeholder="Masukkan alasan penolakan..."
                                />
                            </div>
                        </div>
                        <div className="p-6 border-t flex justify-end gap-3">
                            <Button variant="outline" onClick={() => setShowRejectModal(false)}>
                                Batal
                            </Button>
                            <Button variant="destructive" onClick={handleConfirmReject} disabled={isRejecting}>
                                {isRejecting && <Loader2 className="h-4 w-4 mr-2 animate-spin" />}
                                Tolak
                            </Button>
                        </div>
                    </div>
                </div>
            )}

            {/* Void Modal */}
            <ConfirmationModal
                isOpen={showVoidModal}
                onClose={() => setShowVoidModal(false)}
                onConfirm={handleConfirmVoid}
                title="Void Top Up"
                message={`Apakah Anda yakin ingin void top up "${selectedTopUp?.topUpNo}"?`}
                confirmText="Void"
                loading={isVoiding}
                variant="danger"
            />
        </div>
    );
};

export default PettyCashTopUp;
