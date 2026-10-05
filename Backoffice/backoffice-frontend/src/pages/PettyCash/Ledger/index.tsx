import { useEffect, useState } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { useNavigate } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '@/store/themeConfigSlice';
import { DataTable, DataTableColumn } from 'mantine-datatable';
import Flatpickr from 'react-flatpickr';
import 'flatpickr/dist/flatpickr.css';
import SelectSearch from 'react-select';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import IconCalendar from '@/components/Icon/IconCalendar';
import {
    useGetPettyCashLedgerQuery,
    useGetPettyCashUnitOptionsQuery,
    useGetPettyCashMyUnitQuery,
    useExportPettyCashLedgerMutation,
} from '@/store/api/pettyCash/pettyCashApiSlice';
import { FormatNumber } from '@/utils/formatNumber';
import { FormatDate } from '@/utils/formatDate';
import { toastMessage } from '@/utils/toastUtils';
import { BookOpen, Download, Loader2, TrendingUp, TrendingDown, Equal, Table, List } from 'lucide-react';

interface LedgerEntry {
    transactionId: string;
    date: string;
    type: string;
    transactionNo: string;
    description: string;
    debit: number;
    credit: number;
    journalNo?: string;
    // Kategori & sumber baris (dari jurnal) — untuk klasifikasi & badge
    category?: string;
    source?: string;
    // true = jurnal diinput dari luar modul petty cash (jurnal manual / scheduler / modul lain)
    isExternal?: boolean;
    // ID sumber untuk navigasi ke modul masing-masing
    settlementId?: number;
    requestId?: number;
    topUpId?: number;
    // Ref. permintaan asal (baris lebih/kurang bayar)
    requestNo?: string;
    // Realisasi belanja untuk baris Pengeluaran (Out) yang sudah di-settle
    realizedAmount?: number;
}

interface LedgerUnitInfo {
    currentBalance: number;
    openingBalance?: number;
    closingBalance?: number;
    totalIn: number;
    totalOut: number;
    totalTopUp?: number;
    totalRequest?: number;
    totalSettlementIn?: number;
    totalSettlementOut?: number;
    totalExternalIn?: number;
    totalExternalOut?: number;
}

const PettyCashLedger = () => {
    const dispatch = useDispatch();
    const navigate = useNavigate();
    const { t } = useTranslation();
    const user = useSelector((state: any) => state.auth.user);

    useEffect(() => {
        dispatch(setPageTitle(t('petty_cash.ledger.title', 'Buku Kas Kecil')));
        dispatch(setTitle(t('petty_cash.ledger.title', 'Buku Kas Kecil')));
        dispatch(setBreadcrumbTitle(['Dashboard', t('sidebar.petty_cash', 'Petty Cash'), t('petty_cash.ledger.title', 'Buku Kas')]));
    }, [dispatch, t]);

    // Filter states
    const [selectedUnit, setSelectedUnit] = useState<string>('');
    const [startDate, setStartDate] = useState<Date | null>(null);
    const [endDate, setEndDate] = useState<Date | null>(null);

    // Pagination
    const [page, setPage] = useState(1);
    const [pageSize, setPageSize] = useState(20);
    const PAGE_SIZES = [20, 50, 100];

    // Mode tampilan: 'table' (style lama 7 kolom) atau 'list' (ringkas 2 baris)
    const [viewMode, setViewMode] = useState<'table' | 'list'>('table');

    // API
    const isPettyCashOfficer = user?.role?.roleID === 5;

    const { data: unitOptions, isLoading: isLoadingUnits } = useGetPettyCashUnitOptionsQuery({}, { skip: isPettyCashOfficer });
    const { data: myUnitData } = useGetPettyCashMyUnitQuery(undefined, { skip: !isPettyCashOfficer });

    // Auto-select unit for Petty Cash Officer
    useEffect(() => {
        if (isPettyCashOfficer && myUnitData?.data) {
            setSelectedUnit(String(myUnitData.data.unitId));
        }
    }, [isPettyCashOfficer, myUnitData]);

    const formatDateForAPI = (date: Date | null): string | null => {
        if (!date) return null;
        return new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate())).toISOString();
    };

    const { data: ledgerData, isLoading, isFetching } = useGetPettyCashLedgerQuery(
        {
            unitId: selectedUnit,
            startDate: formatDateForAPI(startDate),
            endDate: formatDateForAPI(endDate),
            page,
            pageSize,
        },
        { skip: !selectedUnit }
    );

    const [exportLedger, { isLoading: isExporting }] = useExportPettyCashLedgerMutation();

    const records: LedgerEntry[] = ledgerData?.data?.data || [];
    const totalRecords = ledgerData?.data?.totalData || 0;
    const unitInfo: LedgerUnitInfo = ledgerData?.data?.unitInfo || { currentBalance: 0, totalIn: 0, totalOut: 0 };
    const hasPeriod = !!(startDate || endDate);

    const unitOptionsFormatted = isPettyCashOfficer
        ? (myUnitData?.data ? [{ value: String(myUnitData.data.unitId), label: myUnitData.data.unitName }] : [])
        : (unitOptions?.data || []).map((u: any) => ({
            value: String(u.value),
            label: u.label,
        }));

    const handleDateChange = (dates: Date[]) => {
        if (dates.length >= 1) setStartDate(dates[0]);
        if (dates.length >= 2) setEndDate(dates[1]);
        if (dates.length === 0) {
            setStartDate(null);
            setEndDate(null);
        }
    };

    const handleExport = async () => {
        if (!selectedUnit) {
            toastMessage('Pilih unit terlebih dahulu', 'error');
            return;
        }
        try {
            const blob = await exportLedger({
                unitId: selectedUnit,
                startDate: formatDateForAPI(startDate),
                endDate: formatDateForAPI(endDate),
            }).unwrap();

            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', `buku_kas_kecil_${new Date().toISOString().split('T')[0]}.xlsx`);
            document.body.appendChild(link);
            link.click();
            link.remove();
            window.URL.revokeObjectURL(url);
        } catch {
            toastMessage('Gagal export data', 'error');
        }
    };

    const getTransactionTypeIcon = (type: string) => {
        if (type.includes('(In)')) {
            return <TrendingUp className="h-4 w-4 text-green-600" />;
        }
        if (type.includes('(Out)')) {
            return <TrendingDown className="h-4 w-4 text-red-600" />;
        }
        return <Equal className="h-4 w-4 text-gray-600" />;
    };

    // Navigasi ke modul sumber transaksi (settlement / request / topup)
    const hasSource = (row: LedgerEntry) => !!(row.settlementId || row.requestId || row.topUpId);
    const goToSource = (row: LedgerEntry) => {
        if (row.settlementId) navigate(`/petty-cash/settlement/${row.settlementId}`);
        else if (row.requestId) navigate(`/petty-cash/request/${row.requestId}`);
        else if (row.topUpId) navigate(`/petty-cash/topup/${row.topUpId}`);
    };
    const goToJournal = (row: LedgerEntry) => {
        if (row.transactionId) navigate(`/jurnal-umum/detail/${row.transactionId}`);
    };
    const goToRequest = (requestId?: number) => {
        if (requestId) navigate(`/petty-cash/request/${requestId}`);
    };

    const linkClass = 'text-primary hover:underline font-medium cursor-pointer';

    // Label dasar untuk baris lebih/kurang bayar bila user tidak mengisi keterangan
    const fallbackLabel = (type: string) =>
        type === 'Settlement (In)' ? 'Lebih Bayar' : type === 'Settlement (Out)' ? 'Kurang Bayar' : '';

    // Keterangan + "- ref [link permintaan]" (lebih/kurang bayar) + "Realisasi" (baris pengeluaran yg sudah settled)
    const renderDescription = (row: LedgerEntry) => {
        const desc = (row.description || '').trim() || fallbackLabel(row.type);
        return (
            <span className="whitespace-normal break-words">
                {desc}
                {row.requestNo && (
                    <>
                        {desc ? ' - ' : ''}ref{' '}
                        <button
                            type="button"
                            className="text-amber-600 hover:underline font-medium cursor-pointer"
                            onClick={() => goToRequest(row.requestId)}
                        >
                            {row.requestNo}
                        </button>
                    </>
                )}
                {row.realizedAmount != null && (
                    <span className="text-gray-500"> · Realisasi Rp {FormatNumber(row.realizedAmount)}</span>
                )}
            </span>
        );
    };

    // === Tampilan TABEL (style lama 7 kolom) — No. Transaksi & No. Jurnal jadi link;
    // Keterangan menampilkan Ref. Permintaan untuk baris Kurang Bayar ===
    const tableColumns: DataTableColumn<LedgerEntry>[] = [
        {
            accessor: 'date',
            title: 'Tanggal',
            width: 120,
            render: (row) => FormatDate(row.date),
        },
        {
            accessor: 'type',
            title: 'Tipe',
            width: 200,
            render: (row) => (
                <div className="flex items-center gap-2">
                    {getTransactionTypeIcon(row.type)}
                    <span>{row.type}</span>
                    {row.isExternal && (
                        <span className="px-1.5 py-0.5 rounded bg-amber-100 text-amber-700 text-[10px] font-semibold uppercase whitespace-nowrap">
                            Luar Sistem
                        </span>
                    )}
                </div>
            ),
        },
        {
            accessor: 'transactionNo',
            title: 'No. Transaksi',
            width: 180,
            render: (row) =>
                row.transactionNo
                    ? (hasSource(row) ? (
                        <button type="button" className={linkClass} onClick={() => goToSource(row)}>
                            {row.transactionNo}
                        </button>
                    ) : (
                        <span>{row.transactionNo}</span>
                    ))
                    : '-',
        },
        {
            accessor: 'journalNo',
            title: 'No. Jurnal',
            width: 180,
            render: (row) =>
                row.journalNo ? (
                    <button type="button" className={linkClass} onClick={() => goToJournal(row)}>
                        {row.journalNo}
                    </button>
                ) : (
                    '-'
                ),
        },
        {
            accessor: 'description',
            title: 'Keterangan',
            render: (row) => renderDescription(row),
        },
        {
            accessor: 'debit',
            title: 'Debit',
            textAlignment: 'right',
            render: (row) => (
                <div className="text-right w-full">
                    {row.debit > 0 ? (
                        <span className="font-mono text-green-600">{FormatNumber(row.debit)}</span>
                    ) : '-'}
                </div>
            ),
        },
        {
            accessor: 'credit',
            title: 'Kredit',
            textAlignment: 'right' as const,
            render: (row) => (
                <div className="text-right w-full">
                    {row.credit > 0 ? (
                        <span className="font-mono text-red-600">{FormatNumber(row.credit)}</span>
                    ) : '-'}
                </div>
            ),
        },
    ];

    // === Tampilan LIST (2 baris per transaksi, ringkas, tanpa scroll horizontal) ===
    const listColumns: DataTableColumn<LedgerEntry>[] = [
        {
            accessor: 'description',
            title: 'Transaksi',
            render: (row) => (
                <div className="py-1.5">
                    {/* Baris 1: tipe + keterangan (+ ref permintaan & realisasi) */}
                    <div className="flex items-center gap-2 font-medium text-gray-800">
                        {getTransactionTypeIcon(row.type)}
                        {renderDescription(row)}
                    </div>
                    {/* Baris 2: tanggal, no. transaksi (link), no. jurnal (link) */}
                    <div className="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1 text-xs text-gray-500">
                        <span>{FormatDate(row.date)}</span>
                        <span className="px-1.5 py-0.5 rounded bg-gray-100 text-gray-600">{row.type}</span>
                        {row.isExternal && (
                            <span className="px-1.5 py-0.5 rounded bg-amber-100 text-amber-700 font-semibold uppercase">Luar Sistem</span>
                        )}
                        {row.transactionNo && (
                            <span>
                                No. Transaksi:{' '}
                                {hasSource(row) ? (
                                    <button type="button" className={linkClass} onClick={() => goToSource(row)}>
                                        {row.transactionNo}
                                    </button>
                                ) : (
                                    <span className="font-medium text-gray-700">{row.transactionNo}</span>
                                )}
                            </span>
                        )}
                        {row.journalNo && (
                            <span>
                                No. Jurnal:{' '}
                                <button type="button" className={linkClass} onClick={() => goToJournal(row)}>
                                    {row.journalNo}
                                </button>
                            </span>
                        )}
                    </div>
                </div>
            ),
        },
        {
            accessor: 'debit',
            title: 'Debit',
            textAlignment: 'right',
            width: 130,
            render: (row) => (
                <span className="font-mono text-green-600">{row.debit > 0 ? FormatNumber(row.debit) : '-'}</span>
            ),
        },
        {
            accessor: 'credit',
            title: 'Kredit',
            textAlignment: 'right' as const,
            width: 130,
            render: (row) => (
                <span className="font-mono text-red-600">{row.credit > 0 ? FormatNumber(row.credit) : '-'}</span>
            ),
        },
    ];

    return (
        <div className="space-y-6">
            <Card>
                <CardHeader className="border-b border-gray-100 pb-4">
                    <div className="flex flex-col gap-4">
                        <div className="flex items-center gap-3">
                            <div className="p-2 rounded-lg bg-primary/10">
                                <BookOpen className="h-5 w-5 text-primary" />
                            </div>
                            <div>
                                <CardTitle className="text-lg">{t('petty_cash.ledger.title', 'Buku Kas Kecil')}</CardTitle>
                                <p className="text-sm text-gray-500 mt-0.5">Riwayat mutasi kas kecil per unit</p>
                            </div>
                        </div>

                        <div className="flex flex-wrap items-center gap-3">
                            <div className="w-64">
                                <SelectSearch
                                    placeholder="Pilih Unit..."
                                    options={unitOptionsFormatted}
                                    onChange={(opt: any) => { setSelectedUnit(opt?.value || ''); setPage(1); }}
                                    value={unitOptionsFormatted.find((o: any) => o.value === selectedUnit) || null}
                                    isLoading={isLoadingUnits}
                                    isDisabled={isPettyCashOfficer}
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

                            <div className="ml-auto flex items-center gap-3">
                                {/* Toggle tampilan: Tabel / List */}
                                <div className="flex items-center h-10 rounded-lg border border-gray-200 overflow-hidden">
                                    <button
                                        type="button"
                                        onClick={() => setViewMode('table')}
                                        className={`flex items-center gap-1.5 px-3 h-full text-sm transition-colors ${viewMode === 'table' ? 'bg-primary text-white' : 'bg-white text-gray-600 hover:bg-gray-50'}`}
                                    >
                                        <Table className="h-4 w-4" /> Tabel
                                    </button>
                                    <button
                                        type="button"
                                        onClick={() => setViewMode('list')}
                                        className={`flex items-center gap-1.5 px-3 h-full text-sm transition-colors border-l border-gray-200 ${viewMode === 'list' ? 'bg-primary text-white' : 'bg-white text-gray-600 hover:bg-gray-50'}`}
                                    >
                                        <List className="h-4 w-4" /> List
                                    </button>
                                </div>

                                <Button
                                    variant="outline"
                                    className="gap-2 h-10"
                                    onClick={handleExport}
                                    disabled={isExporting || !selectedUnit}
                                >
                                    {isExporting ? <Loader2 className="h-4 w-4 animate-spin" /> : <Download className="h-4 w-4" />}
                                    Export
                                </Button>
                            </div>
                        </div>
                    </div>
                </CardHeader>

                {selectedUnit && (
                    <>
                        {/* Summary Cards */}
                        <div className="grid grid-cols-1 md:grid-cols-4 gap-4 p-4">
                            <div className="bg-gray-50 p-4 rounded-lg">
                                <p className="text-sm text-gray-500">Saldo Unit (Saat Ini)</p>
                                <p className="text-lg font-bold font-mono text-gray-700">{FormatNumber(unitInfo.currentBalance)}</p>
                            </div>
                            <div className="bg-green-50 p-4 rounded-lg">
                                <p className="text-sm text-green-600">Total Masuk (Periode)</p>
                                <p className="text-lg font-bold font-mono text-green-700">{FormatNumber(unitInfo.totalIn)}</p>
                                <p className="text-[11px] text-green-600/80 mt-1">
                                    Top Up {FormatNumber(unitInfo.totalTopUp || 0)}
                                    {(unitInfo.totalSettlementIn || 0) > 0 && ` · Realisasi/Lebih ${FormatNumber(unitInfo.totalSettlementIn || 0)}`}
                                    {(unitInfo.totalExternalIn || 0) > 0 && ` · Luar Sistem ${FormatNumber(unitInfo.totalExternalIn || 0)}`}
                                </p>
                            </div>
                            <div className="bg-red-50 p-4 rounded-lg">
                                <p className="text-sm text-red-600">Total Keluar (Periode)</p>
                                <p className="text-lg font-bold font-mono text-red-700">{FormatNumber(unitInfo.totalOut)}</p>
                                <p className="text-[11px] text-red-600/80 mt-1">
                                    Pengeluaran {FormatNumber(unitInfo.totalRequest || 0)}
                                    {(unitInfo.totalSettlementOut || 0) > 0 && ` · Kurang Bayar ${FormatNumber(unitInfo.totalSettlementOut || 0)}`}
                                    {(unitInfo.totalExternalOut || 0) > 0 && ` · Luar Sistem ${FormatNumber(unitInfo.totalExternalOut || 0)}`}
                                </p>
                            </div>
                            <div className="bg-blue-50 p-4 rounded-lg">
                                <p className="text-sm text-blue-600">Net Flow (Periode)</p>
                                <p className="text-lg font-bold font-mono text-blue-700">{FormatNumber(unitInfo.totalIn - unitInfo.totalOut)}</p>
                            </div>
                        </div>

                        {/* Rekonsiliasi periode: Saldo Awal + Masuk - Keluar = Saldo Akhir */}
                        {hasPeriod && (
                            <div className="px-4 pb-4 border-b">
                                <div className="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm bg-slate-50 rounded-lg p-3 font-mono">
                                    <span className="text-gray-500">Saldo Awal</span>
                                    <span className="font-semibold text-gray-700">{FormatNumber(unitInfo.openingBalance || 0)}</span>
                                    <span className="text-green-600">+ {FormatNumber(unitInfo.totalIn)}</span>
                                    <span className="text-red-600">− {FormatNumber(unitInfo.totalOut)}</span>
                                    <span className="text-gray-400">=</span>
                                    <span className="text-gray-500">Saldo Akhir</span>
                                    <span className="font-bold text-blue-700">{FormatNumber(unitInfo.closingBalance ?? (unitInfo.openingBalance || 0) + unitInfo.totalIn - unitInfo.totalOut)}</span>
                                </div>
                            </div>
                        )}
                        {!hasPeriod && <div className="border-b" />}

                        <CardContent className="p-0">
                            <div className="datatables">
                                <DataTable
                                    highlightOnHover
                                    className={viewMode === 'table' ? 'whitespace-nowrap table-hover' : 'table-hover'}
                                    records={records}
                                    columns={viewMode === 'table' ? tableColumns : listColumns}
                                    totalRecords={totalRecords}
                                    recordsPerPage={pageSize}
                                    page={page}
                                    onPageChange={setPage}
                                    recordsPerPageOptions={PAGE_SIZES}
                                    onRecordsPerPageChange={setPageSize}
                                    minHeight={300}
                                    fetching={isLoading || isFetching}
                                    loaderVariant="dots"
                                    loaderColor="#4361ee"
                                    noRecordsText="Tidak ada data transaksi"
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
                    </>
                )}

                {!selectedUnit && (
                    <CardContent>
                        <div className="py-12 text-center text-gray-500">
                            <BookOpen className="h-12 w-12 mx-auto mb-3 opacity-50" />
                            <p className="text-lg">Pilih unit untuk melihat buku kas</p>
                            <p className="text-sm mt-1">Silakan pilih unit kas kecil dari dropdown di atas</p>
                        </div>
                    </CardContent>
                )}
            </Card>
        </div>
    );
};

export default PettyCashLedger;
