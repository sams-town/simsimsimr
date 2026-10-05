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
    useGetPettyCashRequestsQuery,
    useGetPettyCashUnitOptionsQuery,
    useSubmitPettyCashRequestMutation,
    useApprovePettyCashRequestMutation,
    useRejectPettyCashRequestMutation,
    useDeletePettyCashRequestMutation,
    useExportPettyCashRequestsMutation,
    useGetPettyCashMyUnitQuery,
} from '@/store/api/pettyCash/pettyCashApiSlice';
import { FormatNumber } from '@/utils/formatNumber';
import { FormatDate } from '@/utils/formatDate';
import { toastMessage } from '@/utils/toastUtils';
import { FileText, Plus, Send, Check, X, Trash2, Download, Eye, Loader2, Search } from 'lucide-react';
import ConfirmationModal from '@/components/ConfirmationModal';

interface RequestData {
    requestId: number;
    requestNo: string;
    requestDate: string;
    unitId: number;
    unitCode: string;
    unitName: string;
    requesterName: string;
    purpose: string;
    amount: number;
    expenseCoaCode: string;
    status: string;
    approvedAmount: number | null;
    approvedDate: string | null;
    notes: string | null;
    createdDate: string;
    journalNo?: string;
}

const PettyCashRequest = () => {
    const dispatch = useDispatch();
    const navigate = useNavigate();
    const { t } = useTranslation();
    const user = useSelector((state: any) => state.auth.user);
    const isPettyCashOfficer = user?.role?.roleID === 5;
    const { data: myUnit } = useGetPettyCashMyUnitQuery(undefined, { skip: !isPettyCashOfficer });

    useEffect(() => {
        dispatch(setPageTitle(t('petty_cash.request.title', 'Permintaan Kas Kecil')));
        dispatch(setTitle(t('petty_cash.request.title', 'Permintaan Kas Kecil')));
        dispatch(setBreadcrumbTitle(['Dashboard', t('sidebar.petty_cash', 'Petty Cash'), t('petty_cash.request.title', 'Permintaan')]));
    }, [dispatch, t]);

    // Filter states
    const [selectedUnit, setSelectedUnit] = useState<string>('');
    const [startDate, setStartDate] = useState<Date | null>(null);
    const [endDate, setEndDate] = useState<Date | null>(null);
    const [keyword, setKeyword] = useState<string>('');

    useEffect(() => {
        if (isPettyCashOfficer && myUnit?.data) {
            setSelectedUnit(String(myUnit.data.unitId));
        }
    }, [isPettyCashOfficer, myUnit]);

    // Pagination
    const [page, setPage] = useState(1);
    const [pageSize, setPageSize] = useState(10);
    const PAGE_SIZES = [10, 20, 30, 50];

    // Modal states
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [selectedRequest, setSelectedRequest] = useState<RequestData | null>(null);

    // API
    const { data: unitOptions } = useGetPettyCashUnitOptionsQuery({});

    const formatDateForAPI = (date: Date | null): string | null => {
        if (!date) return null;
        return new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate())).toISOString();
    };

    const { data: listData, isLoading, refetch } = useGetPettyCashRequestsQuery({
        unitId: selectedUnit || undefined,
        startDate: formatDateForAPI(startDate),
        endDate: formatDateForAPI(endDate),
        keyword: keyword || undefined,
        page,
        pageSize,
    });

    const [submitRequest, { isLoading: isSubmitting }] = useSubmitPettyCashRequestMutation();

    const [deleteRequest, { isLoading: isDeleting }] = useDeletePettyCashRequestMutation();
    const [exportRequests, { isLoading: isExporting }] = useExportPettyCashRequestsMutation();

    const records: RequestData[] = listData?.data?.data || [];
    const totalRecords = listData?.data?.totalData || 0;

    const unitOptionsFormatted = isPettyCashOfficer
        ? (myUnit?.data ? [{ value: String(myUnit.data.unitId), label: myUnit.data.unitName }] : [])
        : [
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

    const handleSubmit = async (request: RequestData) => {
        try {
            await submitRequest(request.requestId).unwrap();
            toastMessage('Permintaan berhasil diajukan', 'success');
            refetch();
        } catch (error: any) {
            toastMessage(error?.data?.message || 'Gagal mengajukan permintaan', 'error');
        }
    };



    const handleDelete = (request: RequestData) => {
        setSelectedRequest(request);
        setShowDeleteModal(true);
    };



    const handleConfirmDelete = async () => {
        if (!selectedRequest) return;
        try {
            await deleteRequest(selectedRequest.requestId).unwrap();
            toastMessage('Permintaan berhasil dihapus', 'success');
            setShowDeleteModal(false);
            refetch();
        } catch (error: any) {
            toastMessage(error?.data?.message || 'Gagal menghapus permintaan', 'error');
        }
    };

    const handleExport = async () => {
        try {
            const blob = await exportRequests({
                unitId: selectedUnit || undefined,
                startDate: formatDateForAPI(startDate),
                endDate: formatDateForAPI(endDate),
            }).unwrap();

            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', `permintaan_kas_kecil_${new Date().toISOString().split('T')[0]}.xlsx`);
            document.body.appendChild(link);
            link.click();
            link.remove();
            window.URL.revokeObjectURL(url);
        } catch {
            toastMessage('Gagal export data', 'error');
        }
    };



    const columns: DataTableColumn<RequestData>[] = [
        {
            accessor: 'requestNo',
            title: 'No. Permintaan',
            width: 180,
        },
        {
            accessor: 'requestDate',
            title: 'Tanggal',
            width: 120,
            render: (row) => FormatDate(row.requestDate),
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
            accessor: 'requesterName',
            title: 'Pemohon',
        },
        {
            accessor: 'purpose',
            title: 'Keperluan',
            width: 250,
            render: (row) => (
                <span className="truncate block max-w-[250px]" title={row.purpose}>
                    {row.purpose}
                </span>
            ),
        },
        {
            accessor: 'amount',
            title: 'Jumlah',
            textAlignment: 'right' as const,
            render: (row) => <span className="font-mono">{FormatNumber(row.amount)}</span>,
        },
        {
            accessor: 'journalNo',
            title: 'No. Jurnal',
            render: (row) => row.journalNo || '-',
        },
        {
            accessor: 'actions',
            title: 'Aksi',
            width: 150,
            render: (row) => (
                <div className="flex items-center gap-1">
                    <Button
                        variant="ghost"
                        size="sm"
                        onClick={() => navigate(`/petty-cash/request/${row.requestId}`)}
                    >
                        <Eye className="h-4 w-4" />
                    </Button>
                    {row.status === 'Draft' && (
                        <>
                            <Button
                                variant="ghost"
                                size="sm"
                                className="text-blue-600"
                                onClick={() => handleSubmit(row)}
                                disabled={isSubmitting}
                            >
                                <Send className="h-4 w-4" />
                            </Button>
                            <Button
                                variant="ghost"
                                size="sm"
                                className="text-red-600"
                                onClick={() => handleDelete(row)}
                            >
                                <Trash2 className="h-4 w-4" />
                            </Button>
                        </>
                    )}
                    <TimelineButton
                        module={AUDIT_MODULE.PETTY_CASH_REQUEST}
                        refId={row.requestId}
                        title={row.requestNo}
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
                                    <FileText className="h-5 w-5 text-primary" />
                                </div>
                                <div>
                                    <CardTitle className="text-lg">{t('petty_cash.request.title', 'Permintaan Kas Kecil')}</CardTitle>
                                    <p className="text-sm text-gray-500 mt-0.5">Kelola permintaan dana kas kecil</p>
                                </div>
                            </div>
                            <Button className="gap-2" onClick={() => navigate('/petty-cash/request/create')}>
                                <Plus className="h-4 w-4" />
                                Buat Permintaan
                            </Button>
                        </div>

                        <div className="flex flex-wrap items-center gap-3">
                            <div className="relative w-48">
                                <input
                                    type="text"
                                    placeholder="Cari..."
                                    className="form-input w-full h-10 pl-9 border-slate-200"
                                    value={keyword}
                                    onChange={(e) => { setKeyword(e.target.value); setPage(1); }}
                                />
                                <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <Search className="h-4 w-4 text-gray-400" />
                                </div>
                            </div>

                            <div className="w-48">
                                <SelectSearch
                                    placeholder="Unit"
                                    options={unitOptionsFormatted}
                                    onChange={(opt: any) => { setSelectedUnit(opt?.value || ''); setPage(1); }}
                                    value={unitOptionsFormatted.find((o) => o.value === selectedUnit) || null}
                                    isDisabled={isPettyCashOfficer}
                                    isClearable={!isPettyCashOfficer}
                                    styles={{
                                        control: (base) => ({ ...base, minHeight: '40px', borderColor: '#e2e8f0', backgroundColor: isPettyCashOfficer ? '#f1f5f9' : '#ffffff' }),
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



            {/* Delete Modal */}
            <ConfirmationModal
                isOpen={showDeleteModal}
                onClose={() => setShowDeleteModal(false)}
                onConfirm={handleConfirmDelete}
                title="Hapus Permintaan"
                message={`Apakah Anda yakin ingin menghapus permintaan "${selectedRequest?.requestNo}"?`}
                confirmText="Hapus"
                loading={isDeleting}
                variant="danger"
            />
        </div>
    );
};

export default PettyCashRequest;
