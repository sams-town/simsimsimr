import { useState, useEffect, Fragment } from 'react';
import { Dialog, Transition } from '@headlessui/react';
import { DataTable } from 'mantine-datatable';
import Select from 'react-select';
import Flatpickr from 'react-flatpickr';
import 'flatpickr/dist/flatpickr.css';
import { X, Plus, Search } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { FormatNumber } from '@/utils/formatNumber';
import { useGetCombinedEligibleItemsQuery } from '@/store/api/accountReceivable/combinedVoucherApiSlice';
import { useGetAccountReceivableInsuranceOptionsQuery } from '@/store/api/accountReceivable/accountReceivableApiSlice';

interface EligibleItem {
    arId: number;
    arNumber?: string;
    patientName?: string;
    medicalRecordNo?: string;
    sepNo?: string;
    payerId?: string;
    payerName?: string;
    payerType?: string;
    serviceType?: string;
    voucherNumber?: string;
    voucherDate?: string;
    transactionDate?: string;
    totalAmount: number;
    adjustmentAmount?: number;
    effectiveAmount: number;
}

interface AddPatientModalProps {
    isOpen: boolean;
    onClose: () => void;
    onAdd: (arIds: number[]) => Promise<void> | void;
    loading?: boolean;
}

const payerTypeOptions = [
    { value: '', label: 'Semua Payer Type' },
    { value: 'bpjs', label: 'BPJS' },
    { value: 'non bpjs', label: 'Non BPJS' },
];

const selectStyles = {
    menuPortal: (base: any) => ({ ...base, zIndex: 9999 }),
    control: (base: any) => ({ ...base, minHeight: 36, height: 36 }),
    valueContainer: (base: any) => ({ ...base, padding: '0 8px' }),
    indicatorsContainer: (base: any) => ({ ...base, height: 36 }),
};

const formatDateForApi = (date: Date | null): string | null => {
    if (!date) return null;
    return new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate())).toISOString();
};

// Compact date for the table (dd/mm/yyyy) so the Tgl column stays narrow
const formatShortDate = (iso?: string): string => {
    if (!iso) return '-';
    const d = new Date(iso);
    if (isNaN(d.getTime())) return '-';
    const pad = (n: number) => String(n).padStart(2, '0');
    return `${pad(d.getDate())}/${pad(d.getMonth() + 1)}/${d.getFullYear()}`;
};

const PAGE_SIZE = 8;

const AddPatientModal = ({ isOpen, onClose, onAdd, loading }: AddPatientModalProps) => {
    const [page, setPage] = useState(1);
    const [keyword, setKeyword] = useState('');
    const [debouncedKeyword, setDebouncedKeyword] = useState('');
    const [payerType, setPayerType] = useState<any>(payerTypeOptions[0]);
    const [payer, setPayer] = useState<any>(null);
    const [payerSearch, setPayerSearch] = useState('');
    const [dateFrom, setDateFrom] = useState<Date | null>(null);
    const [dateTo, setDateTo] = useState<Date | null>(null);
    const [selectedRecords, setSelectedRecords] = useState<EligibleItem[]>([]);

    // Payer (insurance) options — pre-filtered by the selected payer type
    const { data: insuranceOptions, isLoading: isLoadingInsurance } = useGetAccountReceivableInsuranceOptionsQuery(payerSearch);
    const allPayerOptions = (insuranceOptions?.data || insuranceOptions || []).map((p: any) => ({
        value: p.value,
        label: p.label,
        payerType: p.payerType,
    }));
    const payerOptions = (() => {
        const pt = (payerType?.value || '').toLowerCase();
        if (!pt) return allPayerOptions;
        // Data sudah punya field payerType eksplisit ("bpjs" / "non bpjs").
        // Cocokkan PERSIS (exact match) — pakai includes akan keliru karena "non bpjs" juga mengandung "bpjs".
        // Filter lokal: opsi insurance sudah ter-load semua di awal.
        return allPayerOptions.filter((o: any) => (o.payerType || '').toLowerCase() === pt);
    })();

    // Debounce keyword
    useEffect(() => {
        const t = setTimeout(() => setDebouncedKeyword(keyword), 400);
        return () => clearTimeout(t);
    }, [keyword]);

    // Reset state when opened/closed
    useEffect(() => {
        if (isOpen) {
            setPage(1);
            setSelectedRecords([]);
            setPayer(null);
        }
    }, [isOpen]);

    const { data, isLoading, isFetching } = useGetCombinedEligibleItemsQuery(
        {
            payerType: payerType?.value || '',
            payerId: payer?.value || '',
            keyword: debouncedKeyword,
            startDate: formatDateForApi(dateFrom),
            endDate: formatDateForApi(dateTo),
            page,
            pageSize: PAGE_SIZE,
        },
        { skip: !isOpen }
    );

    const records: EligibleItem[] = data?.data?.data || [];
    const total = data?.data?.totalData || 0;

    const handleAdd = async () => {
        if (selectedRecords.length === 0) return;
        await onAdd(selectedRecords.map((r) => r.arId));
        setSelectedRecords([]);
    };

    // Klik baris untuk toggle pilih (checkbox sudah stopPropagation, jadi tidak double-toggle)
    const toggleRecord = (record: EligibleItem) => {
        setSelectedRecords((prev) =>
            prev.some((r) => r.arId === record.arId)
                ? prev.filter((r) => r.arId !== record.arId)
                : [...prev, record]
        );
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
                        <Dialog.Panel className="relative bg-white dark:bg-[#0e1726] rounded-lg shadow-xl w-full max-w-5xl max-h-[90vh] flex flex-col">
                {/* Header — 1 baris */}
                <div className="flex items-center justify-between px-5 py-3 border-b border-gray-100 dark:border-gray-700">
                    <h3 className="text-base font-bold text-gray-900 dark:text-white">
                        Tambah Pasien
                        <span className="ml-2 text-xs font-normal text-gray-400">item ber-voucher & belum lunas</span>
                    </h3>
                    <button onClick={onClose} className="text-gray-400 hover:text-gray-600 p-1">
                        <X className="h-5 w-5" />
                    </button>
                </div>

                {/* Filters — 1 baris ringkas */}
                <div className="px-5 py-2.5 border-b border-gray-100 dark:border-gray-700 flex flex-col md:flex-row gap-2">
                    <div className="relative flex-1">
                        <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                        <input
                            type="text"
                            value={keyword}
                            onChange={(e) => { setKeyword(e.target.value); setPage(1); }}
                            placeholder="Cari pasien / RM / voucher / SEP"
                            className="flex h-9 w-full rounded-md border border-input bg-background pl-9 pr-3 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
                        />
                    </div>
                    <div className="w-full md:w-[150px]">
                        <Select
                            options={payerTypeOptions}
                            value={payerType}
                            onChange={(val) => { setPayerType(val); setPayer(null); setPage(1); }}
                            className="text-sm"
                            menuPortalTarget={document.body}
                            styles={selectStyles}
                        />
                    </div>
                    <div className="w-full md:w-[180px]">
                        <Select
                            options={payerOptions}
                            value={payer}
                            onInputChange={(val) => setPayerSearch(val)}
                            onChange={(val) => { setPayer(val); setPage(1); }}
                            isClearable
                            isLoading={isLoadingInsurance}
                            placeholder="Payer"
                            className="text-sm"
                            menuPortalTarget={document.body}
                            styles={selectStyles}
                        />
                    </div>
                    <div className="w-full md:w-[160px]">
                        <Flatpickr
                            value={[dateFrom, dateTo].filter(Boolean) as Date[]}
                            options={{ mode: 'range', dateFormat: 'd/m/Y' }}
                            className="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
                            placeholder="Periode"
                            onChange={(dates) => {
                                setPage(1);
                                if (dates.length === 2) { setDateFrom(dates[0]); setDateTo(dates[1]); }
                                else if (dates.length === 0) { setDateFrom(null); setDateTo(null); }
                            }}
                        />
                    </div>
                </div>

                {/* Table */}
                <div className="px-6 py-3 overflow-auto flex-1">
                    <style>{`
                        .combined-eligible-table .mantine-Checkbox-input {
                            border: 2px solid #94a3b8;
                            cursor: pointer;
                        }
                        .combined-eligible-table .mantine-Checkbox-input:checked {
                            border-color: #4361ee;
                            background-color: #4361ee;
                        }
                        .combined-eligible-table tbody tr { cursor: pointer; }
                    `}</style>
                    <DataTable
                        highlightOnHover
                        className="text-sm combined-eligible-table"
                        verticalSpacing={4}
                        fontSize="sm"
                        records={records}
                        idAccessor="arId"
                        selectedRecords={selectedRecords}
                        onSelectedRecordsChange={setSelectedRecords}
                        onRowClick={(record: EligibleItem) => toggleRecord(record)}
                        columns={[
                            { accessor: 'voucherNumber', title: 'Voucher', width: 130, noWrap: true, render: (r: EligibleItem) => <span className="font-mono text-xs">{r.voucherNumber || '-'}</span> },
                            { accessor: 'sepNo', title: 'SEP', width: 175, ellipsis: true, render: (r: EligibleItem) => <span className="font-mono text-xs">{r.sepNo || '-'}</span> },
                            {
                                accessor: 'patientName', title: 'Pasien', render: (r: EligibleItem) => (
                                    <div className="leading-tight">
                                        <div className="font-medium truncate">{r.patientName || '-'}</div>
                                        <div className="text-[11px] text-gray-500">RM: {r.medicalRecordNo || '-'}</div>
                                    </div>
                                )
                            },
                            { accessor: 'payerName', title: 'Payer', width: 150, ellipsis: true, render: (r: EligibleItem) => <span className="text-xs">{r.payerName || '-'}</span> },
                            { accessor: 'voucherDate', title: 'Tgl', width: 100, noWrap: true, render: (r: EligibleItem) => <span className="text-xs">{formatShortDate(r.voucherDate)}</span> },
                            { accessor: 'effectiveAmount', title: 'Jumlah', width: 120, textAlignment: 'right', noWrap: true, render: (r: EligibleItem) => <span className="font-mono">{FormatNumber(r.effectiveAmount)}</span> },
                        ]}
                        totalRecords={total}
                        recordsPerPage={PAGE_SIZE}
                        page={page}
                        onPageChange={setPage}
                        fetching={isLoading || isFetching}
                        minHeight={200}
                        noRecordsText="Tidak ada item yang bisa digabungkan"
                    />
                </div>

                {/* Footer */}
                <div className="flex items-center justify-between px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                    <span className="text-sm text-gray-600">
                        {selectedRecords.length > 0 ? `${selectedRecords.length} item dipilih` : 'Belum ada item dipilih'}
                    </span>
                    <div className="flex gap-2">
                        <Button variant="ghost" onClick={onClose}>Batal</Button>
                        <Button onClick={handleAdd} disabled={selectedRecords.length === 0 || loading} className="gap-2">
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

export default AddPatientModal;
