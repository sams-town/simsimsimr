import { useEffect, useState } from 'react';
import { useDispatch } from 'react-redux';
import { useNavigate } from 'react-router-dom';
import { DataTable } from 'mantine-datatable';
import Flatpickr from 'react-flatpickr';
import 'flatpickr/dist/flatpickr.css';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '@/store/themeConfigSlice';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { FileText, Plus, Eye, Trash2, Search } from 'lucide-react';
import IconCalendar from '@/components/Icon/IconCalendar';
import ConfirmationModal from '@/components/ConfirmationModal';
import { toastMessage } from '@/utils/toastUtils';
import { FormatDate } from '@/utils/formatDate';
import { FormatNumber } from '@/utils/formatNumber';
import { BankFormMakerConfig } from './config';
import {
    useGetBankVoucherListQuery,
    useDeleteBankVoucherMutation,
} from '@/store/api/bankFormVoucher/bankFormVoucherApiSlice';

interface BankVoucher {
    id: number;
    voucherNumber: string;
    voucherDate?: string;
    paymentType: string;
    status: string;
    personName?: string;
    coaName?: string;
    amount: number;
    totalAmount: number;
}

const formatDateForApi = (date: Date | null): string | null => {
    if (!date) return null;
    return new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate())).toISOString();
};

const PAGE_SIZE = 10;

const BankFormMakerList = ({ config }: { config: BankFormMakerConfig }) => {
    const dispatch = useDispatch();
    const navigate = useNavigate();

    useEffect(() => {
        dispatch(setPageTitle(config.pageTitle));
        dispatch(setTitle(config.pageTitle));
        dispatch(setBreadcrumbTitle(['Dashboard', config.breadcrumbParent, 'Form Maker']));
    }, [dispatch, config]);

    const [page, setPage] = useState(1);
    const [keyword, setKeyword] = useState('');
    const [debouncedKeyword, setDebouncedKeyword] = useState('');
    const [dateFrom, setDateFrom] = useState<Date | null>(null);
    const [dateTo, setDateTo] = useState<Date | null>(null);

    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [deleteTarget, setDeleteTarget] = useState<BankVoucher | null>(null);

    useEffect(() => {
        const t = setTimeout(() => setDebouncedKeyword(keyword), 400);
        return () => clearTimeout(t);
    }, [keyword]);

    const { data, isLoading, isFetching } = useGetBankVoucherListQuery({
        voucherType: config.voucherType,
        keyword: debouncedKeyword,
        startDate: formatDateForApi(dateFrom),
        endDate: formatDateForApi(dateTo),
        page,
        pageSize: PAGE_SIZE,
    });

    const [deleteVoucher, { isLoading: isDeleting }] = useDeleteBankVoucherMutation();

    const records: BankVoucher[] = data?.data?.data || [];
    const total = data?.data?.totalData || 0;

    const handleDelete = async () => {
        if (!deleteTarget) return;
        try {
            await deleteVoucher(deleteTarget.id).unwrap();
            toastMessage('Bukti dihapus', 'success');
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
                            <div className="p-2 rounded-lg" style={{ backgroundColor: `${config.accent}20` }}>
                                <FileText className="h-5 w-5" style={{ color: config.accent }} />
                            </div>
                            <div>
                                <CardTitle className="text-lg">{config.pageTitle}</CardTitle>
                                <p className="text-sm text-gray-500 mt-0.5">{config.subtitle}</p>
                            </div>
                        </div>

                        <div className="flex flex-col md:flex-row items-end md:items-center gap-2 ml-auto w-full md:w-auto flex-wrap">
                            <div className="relative w-full md:w-[220px]">
                                <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                                <input
                                    type="text"
                                    value={keyword}
                                    onChange={(e) => { setKeyword(e.target.value); setPage(1); }}
                                    placeholder="Cari no voucher / nama..."
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
                            <Button
                                onClick={() => navigate(`${config.basePath}/create`)}
                                className="gap-2 w-full md:w-auto text-white"
                                style={{ backgroundColor: config.accent }}
                            >
                                <Plus className="h-4 w-4" />
                                {config.createLabel}
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
                                    render: (r: BankVoucher) => (
                                        <span className="font-mono font-bold" style={{ color: config.accent }}>{r.voucherNumber}</span>
                                    ),
                                },
                                {
                                    accessor: 'voucherDate',
                                    title: 'Tanggal',
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (r: BankVoucher) => r.voucherDate ? FormatDate(r.voucherDate) : '-',
                                },
                                {
                                    accessor: 'personName',
                                    title: config.personLabel,
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (r: BankVoucher) => <span className="font-medium">{r.personName || '-'}</span>,
                                },
                                {
                                    accessor: 'coaName',
                                    title: 'Akun',
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (r: BankVoucher) => <span className="text-sm text-gray-600">{r.coaName || '-'}</span>,
                                },
                                {
                                    accessor: 'paymentType',
                                    title: 'Tipe',
                                    textAlignment: 'center',
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (r: BankVoucher) => (
                                        <span className={`px-2 py-1 rounded-full text-xs font-semibold ${r.paymentType === 'CASH' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700'}`}>
                                            {r.paymentType}
                                        </span>
                                    ),
                                },
                                {
                                    accessor: 'totalAmount',
                                    title: 'Total',
                                    textAlignment: 'right',
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50 text-right',
                                    render: (r: BankVoucher) => <span className="font-mono font-bold">{FormatNumber(r.totalAmount)}</span>,
                                },
                                {
                                    accessor: 'action',
                                    title: 'Aksi',
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (r: BankVoucher) => (
                                        <div className="flex gap-1">
                                            <Button variant="outline" size="sm" onClick={() => navigate(`${config.basePath}/${r.id}`)} className="gap-1">
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
                            noRecordsText="Belum ada bukti"
                            loaderVariant="dots"
                            loaderColor={config.accent}
                        />
                    </div>
                </CardContent>
            </Card>

            <ConfirmationModal
                isOpen={showDeleteModal}
                onClose={() => { setShowDeleteModal(false); setDeleteTarget(null); }}
                onConfirm={handleDelete}
                title="Hapus Bukti"
                message={`Yakin hapus voucher ${deleteTarget?.voucherNumber || ''}?`}
                confirmText="Hapus"
                cancelText="Batal"
                loading={isDeleting}
                variant="danger"
            />
        </div>
    );
};

export default BankFormMakerList;
