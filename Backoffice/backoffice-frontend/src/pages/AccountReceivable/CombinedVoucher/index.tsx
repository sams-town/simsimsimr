import { useEffect, useState } from 'react';
import { useDispatch } from 'react-redux';
import { useNavigate } from 'react-router-dom';
import { DataTable } from 'mantine-datatable';
import Flatpickr from 'react-flatpickr';
import 'flatpickr/dist/flatpickr.css';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '@/store/themeConfigSlice';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Layers, Plus, Eye, Trash2, Search } from 'lucide-react';
import IconCalendar from '@/components/Icon/IconCalendar';
import ConfirmationModal from '@/components/ConfirmationModal';
import { toastMessage } from '@/utils/toastUtils';
import { FormatDate } from '@/utils/formatDate';
import AddPatientModal from './AddPatientModal';
import {
    useGetCombinedVoucherListQuery,
    useCreateCombinedVoucherMutation,
    useDeleteCombinedVoucherMutation,
} from '@/store/api/accountReceivable/combinedVoucherApiSlice';

interface CombinedBatch {
    id: number;
    batchNumber: string;
    batchDate?: string;
    description?: string;
    status: string;
    itemCount: number;
    payerCount: number;
    totalAmount: number;
}

const formatDateForApi = (date: Date | null): string | null => {
    if (!date) return null;
    return new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate())).toISOString();
};

const PAGE_SIZE = 10;

const CombinedVoucherList = () => {
    const dispatch = useDispatch();
    const navigate = useNavigate();

    useEffect(() => {
        dispatch(setPageTitle('AR Payment Voucher Gabungan'));
        dispatch(setTitle('AR Payment Voucher Gabungan'));
        dispatch(setBreadcrumbTitle(['Dashboard', 'Piutang Usaha', 'Payment Voucher Gabungan']));
    }, [dispatch]);

    const [page, setPage] = useState(1);
    const [keyword, setKeyword] = useState('');
    const [debouncedKeyword, setDebouncedKeyword] = useState('');
    const [dateFrom, setDateFrom] = useState<Date | null>(null);
    const [dateTo, setDateTo] = useState<Date | null>(null);

    const [showAddModal, setShowAddModal] = useState(false);
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [deleteTarget, setDeleteTarget] = useState<CombinedBatch | null>(null);

    useEffect(() => {
        const t = setTimeout(() => setDebouncedKeyword(keyword), 400);
        return () => clearTimeout(t);
    }, [keyword]);

    const { data, isLoading, isFetching } = useGetCombinedVoucherListQuery({
        keyword: debouncedKeyword,
        startDate: formatDateForApi(dateFrom),
        endDate: formatDateForApi(dateTo),
        page,
        pageSize: PAGE_SIZE,
    });

    const [createCombined, { isLoading: isCreating }] = useCreateCombinedVoucherMutation();
    const [deleteCombined, { isLoading: isDeleting }] = useDeleteCombinedVoucherMutation();

    const records: CombinedBatch[] = data?.data?.data || [];
    const total = data?.data?.totalData || 0;

    const formatCurrency = (value: number) =>
        new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value || 0);

    const handleCreate = async (arIds: number[]) => {
        try {
            const res = await createCombined({
                batchDate: formatDateForApi(new Date()),
                description: null,
                arIds,
            }).unwrap();

            toastMessage('Combined voucher berhasil dibuat', 'success');
            setShowAddModal(false);

            const newId = res?.data?.id;
            if (newId) navigate(`/account-receivable/combined-voucher/${newId}`);
        } catch (error: any) {
            toastMessage(error?.data?.message || 'Gagal membuat combined voucher', 'error');
        }
    };

    const handleDelete = async () => {
        if (!deleteTarget) return;
        try {
            await deleteCombined(deleteTarget.id).unwrap();
            toastMessage('Combined voucher dihapus', 'success');
            setShowDeleteModal(false);
            setDeleteTarget(null);
        } catch (error: any) {
            toastMessage(error?.data?.message || 'Gagal menghapus', 'error');
        }
    };

    return (
        <div className="space-y-6">
            <Card>
                <CardHeader className="border-b border-gray-100 pb-4">
                    <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div className="flex items-center gap-3">
                            <div className="p-2 rounded-lg bg-primary/10">
                                <Layers className="h-5 w-5 text-primary" />
                            </div>
                            <div>
                                <CardTitle className="text-lg">AR Payment Voucher Gabungan</CardTitle>
                                <p className="text-sm text-gray-500 mt-0.5">Gabungkan beberapa pasien (lintas asuransi) ke dalam satu nomor batch</p>
                            </div>
                        </div>

                        <div className="flex flex-col md:flex-row items-end md:items-center gap-3 ml-auto w-full md:w-auto">
                            <div className="relative w-full md:w-[260px]">
                                <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                                <input
                                    type="text"
                                    value={keyword}
                                    onChange={(e) => { setKeyword(e.target.value); setPage(1); }}
                                    placeholder="Cari no batch / keterangan"
                                    className="flex h-10 w-full rounded-md border border-input bg-background pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
                                />
                            </div>
                            <div className="relative w-full md:w-[200px]">
                                <Flatpickr
                                    value={[dateFrom, dateTo].filter(Boolean) as Date[]}
                                    options={{ mode: 'range', dateFormat: 'd/m/Y' }}
                                    className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ltr:pl-9 focus:outline-none focus:ring-2 focus:ring-ring"
                                    placeholder="Periode batch"
                                    onChange={(dates) => {
                                        setPage(1);
                                        if (dates.length === 2) { setDateFrom(dates[0]); setDateTo(dates[1]); }
                                        else if (dates.length === 0) { setDateFrom(null); setDateTo(null); }
                                    }}
                                />
                                <div className="absolute inset-y-0 left-3 flex items-center pointer-events-none text-gray-400">
                                    <IconCalendar className="w-4 h-4" />
                                </div>
                            </div>
                            <Button onClick={() => setShowAddModal(true)} className="gap-2 w-full md:w-auto">
                                <Plus className="h-4 w-4" />
                                Add
                            </Button>
                        </div>
                    </div>
                </CardHeader>

                <CardContent className="pt-6">
                    <div className="datatables rounded-lg border border-gray-100 overflow-hidden">
                        <DataTable
                            highlightOnHover
                            className="whitespace-nowrap table-hover"
                            records={records}
                            idAccessor="id"
                            columns={[
                                {
                                    accessor: 'batchNumber',
                                    title: 'No Batch',
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (r: CombinedBatch) => <span className="font-mono font-bold text-primary">{r.batchNumber}</span>,
                                },
                                {
                                    accessor: 'batchDate',
                                    title: 'Tanggal',
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (r: CombinedBatch) => (r.batchDate ? FormatDate(r.batchDate) : '-'),
                                },
                                {
                                    accessor: 'itemCount',
                                    title: 'Jml Pasien',
                                    textAlignment: 'center',
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (r: CombinedBatch) => (
                                        <span className="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs font-medium">{r.itemCount} pasien</span>
                                    ),
                                },
                                {
                                    accessor: 'payerCount',
                                    title: 'Jml Payer',
                                    textAlignment: 'center',
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (r: CombinedBatch) => <span className="text-xs">{r.payerCount} payer</span>,
                                },
                                {
                                    accessor: 'totalAmount',
                                    title: 'Total',
                                    textAlignment: 'right',
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50 text-right',
                                    render: (r: CombinedBatch) => <span className="font-mono font-bold">{formatCurrency(r.totalAmount)}</span>,
                                },
                                {
                                    accessor: 'action',
                                    title: 'Aksi',
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (r: CombinedBatch) => (
                                        <div className="flex gap-1">
                                            <Button variant="outline" size="sm" onClick={() => navigate(`/account-receivable/combined-voucher/${r.id}`)} className="gap-1">
                                                <Eye className="h-4 w-4" /> Detail
                                            </Button>
                                            <button
                                                type="button"
                                                onClick={() => { setDeleteTarget(r); setShowDeleteModal(true); }}
                                                className="text-red-500 hover:text-red-700 p-1.5 rounded-md hover:bg-red-50 transition-colors"
                                                title="Hapus batch"
                                            >
                                                <Trash2 className="w-4 h-4" />
                                            </button>
                                        </div>
                                    ),
                                },
                            ]}
                            totalRecords={total}
                            recordsPerPage={PAGE_SIZE}
                            page={page}
                            onPageChange={setPage}
                            fetching={isLoading || isFetching}
                            minHeight={200}
                            noRecordsText="Belum ada combined voucher"
                            loaderVariant="dots"
                            loaderColor="#4361ee"
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

            <AddPatientModal
                isOpen={showAddModal}
                onClose={() => setShowAddModal(false)}
                onAdd={handleCreate}
                loading={isCreating}
            />

            <ConfirmationModal
                isOpen={showDeleteModal}
                onClose={() => { setShowDeleteModal(false); setDeleteTarget(null); }}
                onConfirm={handleDelete}
                title="Hapus Combined Voucher"
                message={`Yakin hapus batch ${deleteTarget?.batchNumber || ''}? Item AR di dalamnya akan dilepas (tidak terhapus).`}
                confirmText="Hapus"
                cancelText="Batal"
                loading={isDeleting}
                variant="danger"
            />
        </div>
    );
};

export default CombinedVoucherList;
