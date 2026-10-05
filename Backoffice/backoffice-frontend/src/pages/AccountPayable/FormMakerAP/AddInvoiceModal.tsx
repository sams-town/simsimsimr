import { useState, useEffect, Fragment } from 'react';
import { Dialog, Transition } from '@headlessui/react';
import { DataTable } from 'mantine-datatable';
import Flatpickr from 'react-flatpickr';
import 'flatpickr/dist/flatpickr.css';
import SelectSearch from 'react-select';
import { X, Plus, Search } from 'lucide-react';
import { Button } from '@/components/ui/button';
import IconCalendar from '@/components/Icon/IconCalendar';
import { FormatNumberRounded } from '@/utils/formatNumber';
import { FormatDate } from '@/utils/formatDate';
import { toastMessage } from '@/utils/toastUtils';
import { useGetApVoucherEligibleItemsQuery } from '@/store/api/accountPayable/apPaymentVoucherApiSlice';
import { useGetInvoiceExchangeSupplierOptionQuery } from '@/store/api/invoiceExchange/invoiceExchangeApiSlice';

interface EligibleItem {
    invoiceExchangeId: number;
    invoiceNumber?: string;
    poNumber?: string;
    supplierName?: string;
    receiptDate?: string;
    dueDate?: string;
    totalTransaction: number;
    paidAmount: number;
    outstandingAmount: number;
    paymentStatus?: string;
    agingDays?: number;
    agingBucket?: string;
}

interface AddInvoiceModalProps {
    isOpen: boolean;
    onClose: () => void;
    onAdd: (ids: number[]) => Promise<void> | void;
    loading?: boolean;
    supplierFilter?: string;
    excludeVoucherId?: number;
}

const formatDateForApi = (date: Date | null): string | null => {
    if (!date) return null;
    return new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate())).toISOString();
};

const getAgingColor = (bucket?: string) => {
    switch (bucket) {
        case 'Current': return 'bg-success/10 text-success';
        case '1-30': return 'bg-warning/10 text-warning';
        case '31-60': return 'bg-orange-100 text-orange-600';
        case '61-90': return 'bg-danger/10 text-danger';
        case '>90': return 'bg-danger/20 text-danger';
        default: return 'bg-gray-100 text-gray-500';
    }
};

const selectStyles = {
    control: (base: any) => ({
        ...base,
        minHeight: '36px',
        height: '36px',
        borderColor: '#e2e8f0',
        '&:hover': { borderColor: '#cbd5e1' },
        fontSize: '13px',
    }),
    valueContainer: (base: any) => ({ ...base, padding: '0 8px' }),
    indicatorsContainer: (base: any) => ({ ...base, height: '36px' }),
    menuPortal: (base: any) => ({ ...base, zIndex: 9999 }),
};

const PAGE_SIZE = 10;

const AddInvoiceModal = ({ isOpen, onClose, onAdd, loading, supplierFilter, excludeVoucherId }: AddInvoiceModalProps) => {
    const [page, setPage] = useState(1);
    const [keyword, setKeyword] = useState('');
    const [debouncedKeyword, setDebouncedKeyword] = useState('');
    const [dateFrom, setDateFrom] = useState<Date | null>(null);
    const [dateTo, setDateTo] = useState<Date | null>(null);
    const statusFilter = 'Unpaid'; // eligible items selalu Unpaid (dikunci di backend)
    const [supplierType, setSupplierType] = useState('');
    const [selectedSupplier, setSelectedSupplier] = useState<any>(null);
    const [searchSupplier, setSearchSupplier] = useState('');
    const [selectedRecords, setSelectedRecords] = useState<EligibleItem[]>([]);

    useEffect(() => {
        const t = setTimeout(() => setDebouncedKeyword(keyword), 400);
        return () => clearTimeout(t);
    }, [keyword]);

    useEffect(() => {
        if (isOpen) {
            setPage(1);
            setSelectedRecords([]);
            setKeyword('');
            setDebouncedKeyword('');
        }
    }, [isOpen]);

    useEffect(() => { setPage(1); }, [statusFilter, supplierType, selectedSupplier, dateFrom, dateTo]);

    const { data, isLoading, isFetching } = useGetApVoucherEligibleItemsQuery(
        {
            supplierName: supplierFilter || selectedSupplier?.label || '',
            supplierType,
            paymentStatus: statusFilter,
            keyword: debouncedKeyword,
            startDate: formatDateForApi(dateFrom),
            endDate: formatDateForApi(dateTo),
            page,
            pageSize: PAGE_SIZE,
            excludeVoucherId: excludeVoucherId ?? '',
        },
        { skip: !isOpen }
    );

    const { data: supplierList, isLoading: isLoadingSupplier } = useGetInvoiceExchangeSupplierOptionQuery(
        { keyword: searchSupplier || '', page: 1, pageSize: 20 },
        { skip: !isOpen || !!supplierFilter }
    );

    const supplierOptions = (() => {
        if (!supplierList) return [];
        const items: any[] = Array.isArray(supplierList) ? supplierList : supplierList.data?.data || supplierList.data || [];
        return [
            { label: 'Semua Supplier', value: '' },
            ...items.map((s: any) => ({ label: s.label || s.supplierName || '', value: s.value || s.label || '' })).filter((i: any) => i.label),
        ];
    })();

    const records: EligibleItem[] = data?.data?.data || [];
    const total = data?.data?.totalData || 0;

    // When no supplierFilter is locked (create flow), enforce same-supplier selection
    const lockedSupplier = supplierFilter || (selectedRecords.length > 0 ? selectedRecords[0].supplierName : null);

    const handleSelectionChange = (newRecords: EligibleItem[]) => {
        if (supplierFilter || newRecords.length === 0) {
            setSelectedRecords(newRecords);
            return;
        }
        const firstSupplier = newRecords[0].supplierName;
        const diffSupplier = newRecords.find(r => r.supplierName !== firstSupplier);
        if (diffSupplier) {
            toastMessage('Semua invoice harus dari supplier yang sama', 'error');
            setSelectedRecords(newRecords.filter(r => r.supplierName === (selectedRecords[0]?.supplierName || firstSupplier)));
            return;
        }
        // Check against already-selected supplier
        if (selectedRecords.length > 0 && firstSupplier !== selectedRecords[0].supplierName) {
            toastMessage(`Hanya bisa memilih invoice dari "${selectedRecords[0].supplierName}"`, 'error');
            return;
        }
        setSelectedRecords(newRecords);
    };

    const handleAdd = async () => {
        if (selectedRecords.length === 0) return;
        await onAdd(selectedRecords.map((r) => r.invoiceExchangeId));
        setSelectedRecords([]);
    };

    return (
        <Transition appear show={isOpen} as={Fragment}>
            <Dialog as="div" open={isOpen} onClose={onClose} className="relative z-[999]">
                <Transition.Child
                    as={Fragment}
                    enter="ease-out duration-300" enterFrom="opacity-0" enterTo="opacity-100"
                    leave="ease-in duration-200" leaveFrom="opacity-100" leaveTo="opacity-0"
                >
                    <div className="fixed inset-0 bg-black/50" />
                </Transition.Child>

                <div className="fixed inset-0 overflow-y-auto">
                    <div className="flex items-center justify-center min-h-screen p-4">
                        <Dialog.Panel className="relative bg-white dark:bg-[#0e1726] rounded-lg shadow-xl w-full max-w-5xl max-h-[92vh] flex flex-col">

                            {/* Header */}
                            <div className="flex items-center justify-between px-5 py-3 border-b border-gray-100 dark:border-gray-700">
                                <h3 className="text-base font-bold text-gray-900 dark:text-white">
                                    Tambah Invoice AP
                                    {lockedSupplier && (
                                        <span className="ml-2 text-xs font-normal bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full">
                                            {lockedSupplier}
                                        </span>
                                    )}
                                </h3>
                                <button onClick={onClose} className="text-gray-400 hover:text-gray-600 p-1">
                                    <X className="h-5 w-5" />
                                </button>
                            </div>

                            {/* Filters — same as PembayaranAP */}
                            <div className="px-5 py-2.5 border-b border-gray-100 dark:border-gray-700 flex flex-wrap gap-2 items-center">
                                {/* Keyword */}
                                <div className="relative w-[200px]">
                                    <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                                    <input
                                        type="text"
                                        value={keyword}
                                        onChange={(e) => { setKeyword(e.target.value); setPage(1); }}
                                        placeholder="Cari no invoice / PO..."
                                        className="flex h-9 w-full rounded-md border border-input bg-background pl-9 pr-3 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
                                    />
                                </div>

                                {/* Due Date Range */}
                                <div className="relative w-[200px]">
                                    <Flatpickr
                                        value={[dateFrom, dateTo].filter(Boolean) as Date[]}
                                        options={{ mode: 'range', dateFormat: 'd/m/Y' }}
                                        className="flex h-9 w-full rounded-md border border-input bg-background pl-9 pr-3 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
                                        placeholder="Periode jatuh tempo"
                                        onChange={(dates) => {
                                            if (dates.length === 2) { setDateFrom(dates[0]); setDateTo(dates[1]); }
                                            else if (dates.length === 0) { setDateFrom(null); setDateTo(null); }
                                        }}
                                    />
                                    <div className="absolute inset-y-0 left-3 flex items-center pointer-events-none text-gray-400">
                                        <IconCalendar className="w-4 h-4" />
                                    </div>
                                </div>

                                {/* Payment status filter removed: hanya invoice Unpaid & sudah tukar faktur yang eligible */}

                                {/* Supplier Type */}
                                <select
                                    value={supplierType}
                                    onChange={(e) => setSupplierType(e.target.value)}
                                    className="h-9 rounded-md border border-input bg-background px-3 text-sm focus:outline-none focus:ring-2 focus:ring-ring w-[130px]"
                                >
                                    <option value="">Semua Tipe</option>
                                    <option value="Medis">Medis</option>
                                    <option value="Umum">Umum</option>
                                    <option value="Dapur">Dapur</option>
                                </select>

                                {/* Supplier searchable — hidden when a supplier is already locked */}
                                {!supplierFilter && (
                                    <div className="w-[220px]">
                                        <SelectSearch
                                            placeholder="Pilih supplier"
                                            options={supplierOptions}
                                            isLoading={isLoadingSupplier}
                                            onInputChange={(v) => setSearchSupplier(v)}
                                            onChange={(opt: any) => setSelectedSupplier(opt)}
                                            value={selectedSupplier}
                                            isClearable
                                            styles={selectStyles}
                                            menuPortalTarget={document.body}
                                        />
                                    </div>
                                )}
                            </div>

                            {/* Table */}
                            <div className="px-5 py-3 overflow-auto flex-1">
                                <DataTable
                                    highlightOnHover
                                    className="whitespace-nowrap table-hover text-sm cursor-pointer"
                                    verticalSpacing={4}
                                    fontSize="sm"
                                    records={records}
                                    idAccessor="invoiceExchangeId"
                                    selectedRecords={selectedRecords}
                                    onSelectedRecordsChange={handleSelectionChange}
                                    onRowClick={(record: EligibleItem) => {
                                        if (lockedSupplier && record.supplierName !== lockedSupplier) {
                                            toastMessage(`Hanya bisa memilih invoice dari "${lockedSupplier}"`, 'error');
                                            return;
                                        }
                                        const isSelected = selectedRecords.some(r => r.invoiceExchangeId === record.invoiceExchangeId);
                                        handleSelectionChange(
                                            isSelected
                                                ? selectedRecords.filter(r => r.invoiceExchangeId !== record.invoiceExchangeId)
                                                : [...selectedRecords, record]
                                        );
                                    }}
                                    isRecordSelectable={(record: EligibleItem) =>
                                        !lockedSupplier || record.supplierName === lockedSupplier
                                    }
                                    columns={[
                                        {
                                            accessor: 'supplierName',
                                            title: 'Supplier',
                                            render: (r: EligibleItem) => (
                                                <div className="flex flex-col">
                                                    <span className="font-semibold text-gray-800 dark:text-gray-200">{r.supplierName || '-'}</span>
                                                    <span className="text-xs text-gray-500">{r.poNumber || '-'}</span>
                                                </div>
                                            ),
                                        },
                                        {
                                            accessor: 'invoiceNumber',
                                            title: 'No. Invoice / Jatuh Tempo',
                                            noWrap: true,
                                            render: (r: EligibleItem) => (
                                                <div className="flex flex-col">
                                                    <span className="font-mono text-xs">{r.invoiceNumber || '-'}</span>
                                                    <span className="text-[11px] text-gray-500">{r.dueDate ? FormatDate(r.dueDate) : '-'}</span>
                                                </div>
                                            ),
                                        },
                                        {
                                            accessor: 'totalTransaction',
                                            title: 'Total',
                                            textAlignment: 'right',
                                            noWrap: true,
                                            render: (r: EligibleItem) => <span className="font-mono">{FormatNumberRounded(r.totalTransaction)}</span>,
                                        },
                                        {
                                            accessor: 'paidAmount',
                                            title: 'Dibayar',
                                            textAlignment: 'right',
                                            noWrap: true,
                                            render: (r: EligibleItem) => <span className="font-mono text-success">{FormatNumberRounded(r.paidAmount)}</span>,
                                        },
                                        {
                                            accessor: 'outstandingAmount',
                                            title: 'Outstanding',
                                            textAlignment: 'right',
                                            noWrap: true,
                                            render: (r: EligibleItem) => <span className="font-mono font-bold">{FormatNumberRounded(r.outstandingAmount)}</span>,
                                        },
                                        {
                                            accessor: 'paymentStatus',
                                            title: 'Status / Aging',
                                            textAlignment: 'center',
                                            noWrap: true,
                                            render: (r: EligibleItem) => {
                                                const cls = r.paymentStatus === 'Paid'
                                                    ? 'bg-success/10 text-success'
                                                    : r.paymentStatus === 'Partial'
                                                    ? 'bg-info/10 text-info'
                                                    : 'bg-warning/10 text-warning';
                                                return (
                                                    <div className="flex flex-col items-center gap-0.5">
                                                        <span className={`px-1.5 py-0.5 rounded-full text-[10px] font-semibold ${cls}`}>{r.paymentStatus || 'Unpaid'}</span>
                                                        <span className={`px-1.5 py-0.5 rounded-full text-[10px] font-semibold ${getAgingColor(r.agingBucket)}`}>{r.agingBucket || '-'}</span>
                                                    </div>
                                                );
                                            },
                                        },
                                    ]}
                                    totalRecords={total}
                                    recordsPerPage={PAGE_SIZE}
                                    page={page}
                                    onPageChange={setPage}
                                    fetching={isLoading || isFetching}
                                    minHeight={200}
                                    noRecordsText="Tidak ada invoice yang tersedia"
                                    loaderVariant="dots"
                                    loaderColor="#E8861A"
                                    styles={{
                                        header: {
                                            backgroundColor: '#E8861A',
                                            color: '#ffffff',
                                            fontWeight: 'bold',
                                            fontSize: '11px',
                                            textTransform: 'uppercase',
                                        },
                                    }}
                                />
                            </div>

                            {/* Footer */}
                            <div className="flex items-center justify-between px-5 py-3 border-t border-gray-100 dark:border-gray-700">
                                <span className="text-sm text-gray-600">
                                    {selectedRecords.length > 0
                                        ? `${selectedRecords.length} invoice dipilih · Total: ${FormatNumberRounded(selectedRecords.reduce((s, r) => s + r.outstandingAmount, 0))}`
                                        : 'Belum ada invoice dipilih'}
                                </span>
                                <div className="flex gap-2">
                                    <Button variant="ghost" onClick={onClose}>Batal</Button>
                                    <Button
                                        onClick={handleAdd}
                                        disabled={selectedRecords.length === 0 || loading}
                                        className="gap-2 text-white"
                                        style={{ backgroundColor: '#E8861A' }}
                                    >
                                        <Plus className="h-4 w-4" />
                                        {loading ? 'Memproses...' : `Tambahkan (${selectedRecords.length})`}
                                    </Button>
                                </div>
                            </div>
                        </Dialog.Panel>
                    </div>
                </div>
            </Dialog>
        </Transition>
    );
};

export default AddInvoiceModal;
