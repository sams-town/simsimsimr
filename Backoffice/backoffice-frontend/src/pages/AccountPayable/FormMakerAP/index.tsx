import { useEffect, useState } from 'react';
import { useDispatch } from 'react-redux';
import { useNavigate } from 'react-router-dom';
import { DataTable } from 'mantine-datatable';
import Flatpickr from 'react-flatpickr';
import 'flatpickr/dist/flatpickr.css';
import SelectSearch from 'react-select';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '@/store/themeConfigSlice';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { FileText, Plus, Eye, Trash2, Search } from 'lucide-react';
import IconCalendar from '@/components/Icon/IconCalendar';
import ConfirmationModal from '@/components/ConfirmationModal';
import { toastMessage } from '@/utils/toastUtils';
import { FormatDate } from '@/utils/formatDate';
import { FormatNumberRounded } from '@/utils/formatNumber';
import AddInvoiceModal from './AddInvoiceModal';
import {
    useGetApVoucherListQuery,
    useCreateApVoucherMutation,
    useDeleteApVoucherMutation,
} from '@/store/api/accountPayable/apPaymentVoucherApiSlice';
import { useGetInvoiceExchangeSupplierOptionQuery } from '@/store/api/invoiceExchange/invoiceExchangeApiSlice';

interface ApVoucher {
    id: number;
    voucherNumber: string;
    voucherDate?: string;
    paymentType: string;
    status: string;
    itemCount: number;
    totalAmount: number;
    supplierName?: string;
}

const formatDateForApi = (date: Date | null): string | null => {
    if (!date) return null;
    return new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate())).toISOString();
};

const PAGE_SIZE = 10;

const FormMakerAPList = () => {
    const dispatch = useDispatch();
    const navigate = useNavigate();

    useEffect(() => {
        dispatch(setPageTitle('Form Maker AP'));
        dispatch(setTitle('Form Maker AP'));
        dispatch(setBreadcrumbTitle(['Dashboard', 'Account Payable', 'Form Maker']));
    }, [dispatch]);

    const [page, setPage] = useState(1);
    const [keyword, setKeyword] = useState('');
    const [debouncedKeyword, setDebouncedKeyword] = useState('');
    const [dateFrom, setDateFrom] = useState<Date | null>(null);
    const [dateTo, setDateTo] = useState<Date | null>(null);
    const [selectedSupplier, setSelectedSupplier] = useState<any>(null);
    const [searchSupplier, setSearchSupplier] = useState('');

    const [showAddModal, setShowAddModal] = useState(false);
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [deleteTarget, setDeleteTarget] = useState<ApVoucher | null>(null);

    useEffect(() => {
        const t = setTimeout(() => setDebouncedKeyword(keyword), 400);
        return () => clearTimeout(t);
    }, [keyword]);

    const { data, isLoading, isFetching } = useGetApVoucherListQuery({
        supplierName: selectedSupplier?.label || '',
        keyword: debouncedKeyword,
        startDate: formatDateForApi(dateFrom),
        endDate: formatDateForApi(dateTo),
        page,
        pageSize: PAGE_SIZE,
    });

    const { data: supplierList, isLoading: isLoadingSupplier } = useGetInvoiceExchangeSupplierOptionQuery(
        { keyword: searchSupplier || '', page: 1, pageSize: 20 }
    );

    const supplierOptions = (() => {
        if (!supplierList) return [];
        const items: any[] = Array.isArray(supplierList) ? supplierList : supplierList.data?.data || supplierList.data || [];
        return [
            { label: 'Semua Supplier', value: '' },
            ...items.map((s: any) => ({ label: s.label || s.supplierName || '', value: s.value || s.label || '' })).filter((i: any) => i.label),
        ];
    })();

    const [createVoucher, { isLoading: isCreating }] = useCreateApVoucherMutation();
    const [deleteVoucher, { isLoading: isDeleting }] = useDeleteApVoucherMutation();

    const records: ApVoucher[] = data?.data?.data || [];
    const total = data?.data?.totalData || 0;

    const handleCreate = async (invoiceExchangeIds: number[]) => {
        try {
            const res = await createVoucher({
                voucherDate: formatDateForApi(new Date()),
                paymentType: 'CASH',
                invoiceExchangeIds,
            }).unwrap();

            toastMessage('Bukti pengeluaran berhasil dibuat', 'success');
            setShowAddModal(false);

            const newId = res?.data?.id;
            if (newId) navigate(`/account-payable/form-maker/${newId}`);
        } catch (error: any) {
            toastMessage(error?.data?.message || 'Gagal membuat bukti pengeluaran', 'error');
        }
    };

    const handleDelete = async () => {
        if (!deleteTarget) return;
        try {
            await deleteVoucher(deleteTarget.id).unwrap();
            toastMessage('Bukti pengeluaran dihapus', 'success');
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
                            <div className="p-2 rounded-lg" style={{ backgroundColor: '#E8861A20' }}>
                                <FileText className="h-5 w-5" style={{ color: '#E8861A' }} />
                            </div>
                            <div>
                                <CardTitle className="text-lg">Form Maker AP</CardTitle>
                                <p className="text-sm text-gray-500 mt-0.5">Buat Bukti Pengeluaran untuk pembayaran invoice AP</p>
                            </div>
                        </div>

                        <div className="flex flex-col md:flex-row items-end md:items-center gap-2 ml-auto w-full md:w-auto flex-wrap">
                            <div className="relative w-full md:w-[220px]">
                                <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                                <input
                                    type="text"
                                    value={keyword}
                                    onChange={(e) => { setKeyword(e.target.value); setPage(1); }}
                                    placeholder="Cari no voucher..."
                                    className="flex h-10 w-full rounded-md border border-input bg-background pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
                                />
                            </div>
                            <div className="relative w-full md:w-[190px]">
                                <Flatpickr
                                    value={[dateFrom, dateTo].filter(Boolean) as Date[]}
                                    options={{ mode: 'range', dateFormat: 'd/m/Y' }}
                                    className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ltr:pl-9 focus:outline-none focus:ring-2 focus:ring-ring"
                                    placeholder="Periode voucher"
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
                            <div className="w-full md:w-[220px]">
                                <SelectSearch
                                    placeholder="Pilih supplier"
                                    options={supplierOptions}
                                    isLoading={isLoadingSupplier}
                                    onInputChange={(v) => setSearchSupplier(v)}
                                    onChange={(opt: any) => { setSelectedSupplier(opt); setPage(1); }}
                                    value={selectedSupplier}
                                    isClearable
                                    styles={{
                                        control: (base: any) => ({
                                            ...base,
                                            minHeight: '40px',
                                            borderColor: '#e2e8f0',
                                            '&:hover': { borderColor: '#cbd5e1' },
                                        }),
                                        menuPortal: (base: any) => ({ ...base, zIndex: 9999 }),
                                    }}
                                    menuPortalTarget={document.body}
                                />
                            </div>
                            <Button
                                onClick={() => setShowAddModal(true)}
                                className="gap-2 w-full md:w-auto text-white"
                                style={{ backgroundColor: '#E8861A' }}
                            >
                                <Plus className="h-4 w-4" />
                                Buat Bukti
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
                                    accessor: 'voucherNumber',
                                    title: 'No. Voucher',
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (r: ApVoucher) => (
                                        <span className="font-mono font-bold" style={{ color: '#E8861A' }}>{r.voucherNumber}</span>
                                    ),
                                },
                                {
                                    accessor: 'voucherDate',
                                    title: 'Tanggal',
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (r: ApVoucher) => r.voucherDate ? FormatDate(r.voucherDate) : '-',
                                },
                                {
                                    accessor: 'supplierName',
                                    title: 'Supplier',
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (r: ApVoucher) => <span className="font-medium">{r.supplierName || '-'}</span>,
                                },
                                {
                                    accessor: 'paymentType',
                                    title: 'Tipe',
                                    textAlignment: 'center',
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (r: ApVoucher) => (
                                        <span className={`px-2 py-1 rounded-full text-xs font-semibold ${r.paymentType === 'CASH' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700'}`}>
                                            {r.paymentType}
                                        </span>
                                    ),
                                },
                                {
                                    accessor: 'itemCount',
                                    title: 'Jml Invoice',
                                    textAlignment: 'center',
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (r: ApVoucher) => (
                                        <span className="bg-orange-100 text-orange-700 px-2 py-1 rounded-full text-xs font-medium">{r.itemCount} invoice</span>
                                    ),
                                },
                                {
                                    accessor: 'totalAmount',
                                    title: 'Total',
                                    textAlignment: 'right',
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50 text-right',
                                    render: (r: ApVoucher) => <span className="font-mono font-bold">{FormatNumberRounded(r.totalAmount)}</span>,
                                },
                                {
                                    accessor: 'action',
                                    title: 'Aksi',
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (r: ApVoucher) => (
                                        <div className="flex gap-1">
                                            <Button variant="outline" size="sm" onClick={() => navigate(`/account-payable/form-maker/${r.id}`)} className="gap-1">
                                                <Eye className="h-4 w-4" /> Detail
                                            </Button>
                                            <button
                                                type="button"
                                                onClick={() => { setDeleteTarget(r); setShowDeleteModal(true); }}
                                                className="text-red-500 hover:text-red-700 p-1.5 rounded-md hover:bg-red-50 transition-colors"
                                                title="Hapus voucher"
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
                            noRecordsText="Belum ada bukti pengeluaran"
                            loaderVariant="dots"
                            loaderColor="#E8861A"
                        />
                    </div>
                </CardContent>
            </Card>

            <AddInvoiceModal
                isOpen={showAddModal}
                onClose={() => setShowAddModal(false)}
                onAdd={handleCreate}
                loading={isCreating}
            />

            <ConfirmationModal
                isOpen={showDeleteModal}
                onClose={() => { setShowDeleteModal(false); setDeleteTarget(null); }}
                onConfirm={handleDelete}
                title="Hapus Bukti Pengeluaran"
                message={`Yakin hapus voucher ${deleteTarget?.voucherNumber || ''}? Invoice di dalamnya akan dilepas (tidak terhapus).`}
                confirmText="Hapus"
                cancelText="Batal"
                loading={isDeleting}
                variant="danger"
            />
        </div>
    );
};

export default FormMakerAPList;
