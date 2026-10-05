import { useEffect, useMemo, useState } from 'react';
import { useDispatch } from 'react-redux';
import { DataTable } from 'mantine-datatable';
import {
    RefreshCw,
    Search,
    Lock,
    ArrowLeftRight,
    CheckCircle2,
    XCircle,
    AlertTriangle,
    Loader2,
    Info,
} from 'lucide-react';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '@/store/themeConfigSlice';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import ConfirmationModal from '@/components/ConfirmationModal';
import { toastMessage } from '@/utils/toastUtils';
import { FormatNumberRounded } from '@/utils/formatNumber';
import { FormatDate } from '@/utils/formatDate';
import {
    useGetKhanzaSyncModulesQuery,
    useGetKhanzaSyncRecordsQuery,
    useCompareKhanzaSyncMutation,
    useApplyKhanzaSyncMutation,
} from '@/store/api/khanzaSync/khanzaSyncApiSlice';
import type {
    KhanzaSyncApplyItem,
    KhanzaSyncApplyResult,
    KhanzaSyncCompareField,
    KhanzaSyncCompareRecord,
    KhanzaSyncListItem,
    KhanzaSyncLockState,
    KhanzaSyncModule,
    KhanzaSyncModuleCode,
} from '@/types/khanzaSyncType';

const PAGE_SIZES = [20, 50, 100];
const MAX_SELECTION = 50;

const LOCK_FILTERS: Array<{ value: KhanzaSyncLockState; label: string }> = [
    { value: 'editable', label: 'Bisa diubah' },
    { value: 'locked', label: 'Terkunci' },
    { value: 'all', label: 'Semua' },
];

const formatValue = (field: KhanzaSyncCompareField, value: string | null) => {
    if (value === null || value === '') return <span className="text-gray-400">—</span>;
    switch (field.type) {
        case 'Number':
        case 'Integer':
            return <span className="font-mono">{Number(value).toLocaleString('id-ID', { maximumFractionDigits: 2 })}</span>;
        case 'Date':
            return FormatDate(value);
        default:
            return value;
    }
};

// Field yang boleh dicentang: berbeda, record bisa diubah, dan data Khanza-nya ditemukan.
const selectableFields = (record: KhanzaSyncCompareRecord) =>
    record.editable && record.foundInKhanza ? record.fields.filter((f) => !f.equal).map((f) => f.column) : [];

const KhanzaSyncPage = () => {
    const dispatch = useDispatch();

    useEffect(() => {
        dispatch(setPageTitle('Sinkronisasi Data Khanza'));
        dispatch(setTitle('Sinkronisasi Data Khanza'));
        dispatch(setBreadcrumbTitle(['Dashboard', 'Pengaturan', 'Sinkronisasi Khanza']));
    }, [dispatch]);

    const [moduleCode, setModuleCode] = useState<KhanzaSyncModuleCode>('ap');
    const [lockState, setLockState] = useState<KhanzaSyncLockState>('editable');
    const [searchInput, setSearchInput] = useState('');
    const [search, setSearch] = useState('');
    const [page, setPage] = useState(1);
    const [pageSize, setPageSize] = useState(PAGE_SIZES[0]);
    const [selectedRecords, setSelectedRecords] = useState<KhanzaSyncListItem[]>([]);

    const [compareResults, setCompareResults] = useState<KhanzaSyncCompareRecord[] | null>(null);
    const [checked, setChecked] = useState<Record<string, string[]>>({});
    const [applyResults, setApplyResults] = useState<Record<string, KhanzaSyncApplyResult>>({});
    const [confirmOpen, setConfirmOpen] = useState(false);

    const { data: modulesRes, isLoading: loadingModules } = useGetKhanzaSyncModulesQuery(undefined);
    const modules: KhanzaSyncModule[] = modulesRes?.data ?? [];
    const activeModule = modules.find((m) => m.code === moduleCode);

    const {
        data: recordsRes,
        isFetching: fetchingRecords,
        refetch: refetchRecords,
        error: recordsError,
    } = useGetKhanzaSyncRecordsQuery(
        { module: moduleCode, search, lockState, page, pageSize },
        { refetchOnMountOrArgChange: true }
    );
    const records: KhanzaSyncListItem[] = recordsRes?.data?.data ?? [];
    const totalRecords: number = recordsRes?.data?.totalData ?? 0;

    const [compare, { isLoading: comparing }] = useCompareKhanzaSyncMutation();
    const [apply, { isLoading: applying }] = useApplyKhanzaSyncMutation();

    // Cari dengan jeda supaya tidak menembak API setiap ketikan.
    useEffect(() => {
        const handle = setTimeout(() => {
            setSearch(searchInput.trim());
            setPage(1);
        }, 400);
        return () => clearTimeout(handle);
    }, [searchInput]);

    // Ganti modul/filter = konteks baru; buang pilihan dan hasil perbandingan lama.
    useEffect(() => {
        setSelectedRecords([]);
        setCompareResults(null);
        setChecked({});
        setApplyResults({});
        setPage(1);
    }, [moduleCode, lockState]);

    const handleSelection = (rows: KhanzaSyncListItem[]) => {
        if (rows.length > MAX_SELECTION) {
            toastMessage(`Maksimal ${MAX_SELECTION} data per perbandingan`, 'error');
            setSelectedRecords(rows.slice(0, MAX_SELECTION));
            return;
        }
        setSelectedRecords(rows);
    };

    const runCompare = async (keys: string[]) => {
        try {
            const res: any = await compare({ module: moduleCode, keys }).unwrap();
            const list: KhanzaSyncCompareRecord[] = res?.data ?? [];
            setCompareResults(list);
            // Default: centang semua field yang berubah pada record yang boleh diubah.
            setChecked(Object.fromEntries(list.map((r) => [r.key, selectableFields(r)])));
            return list;
        } catch (e: any) {
            toastMessage(e?.data?.message || 'Gagal membandingkan dengan Khanza', 'error');
            return null;
        }
    };

    const handleCompare = async () => {
        if (selectedRecords.length === 0) {
            toastMessage('Pilih data yang ingin dibandingkan', 'error');
            return;
        }
        setApplyResults({});
        await runCompare(selectedRecords.map((r) => r.key));
    };

    const toggleField = (key: string, column: string) =>
        setChecked((prev) => {
            const current = prev[key] ?? [];
            return { ...prev, [key]: current.includes(column) ? current.filter((c) => c !== column) : [...current, column] };
        });

    const toggleAllFields = (record: KhanzaSyncCompareRecord) =>
        setChecked((prev) => {
            const all = selectableFields(record);
            const current = prev[record.key] ?? [];
            return { ...prev, [record.key]: current.length === all.length ? [] : all };
        });

    const pendingItems: KhanzaSyncApplyItem[] = useMemo(
        () =>
            (compareResults ?? [])
                .filter((r) => r.editable && (checked[r.key]?.length ?? 0) > 0)
                .map((r) => {
                    const cols = checked[r.key];
                    return {
                        key: r.key,
                        fields: cols,
                        expectedAppValues: Object.fromEntries(
                            r.fields.filter((f) => cols.includes(f.column)).map((f) => [f.column, f.appValue])
                        ),
                    };
                }),
        [compareResults, checked]
    );
    const pendingFieldCount = pendingItems.reduce((n, i) => n + i.fields.length, 0);

    const handleApply = async () => {
        try {
            const res: any = await apply({ module: moduleCode, items: pendingItems }).unwrap();
            const results: KhanzaSyncApplyResult[] = res?.data ?? [];
            const ok = results.filter((r) => r.success).length;
            toastMessage(res?.message || `${ok} dari ${results.length} data diterapkan`, ok === results.length ? 'success' : 'error');
            setConfirmOpen(false);

            // Bandingkan ulang supaya layar menampilkan kondisi terbaru setelah diterapkan.
            const keys = (compareResults ?? []).map((r) => r.key);
            await runCompare(keys);
            setApplyResults(Object.fromEntries(results.map((r) => [r.key, r])));
            refetchRecords();
        } catch (e: any) {
            toastMessage(e?.data?.message || 'Gagal menerapkan perubahan', 'error');
        }
    };

    return (
        <div className="space-y-6">
            <Card>
                <CardHeader className="border-b bg-gradient-to-r from-primary/5 to-transparent">
                    <CardTitle className="flex items-center gap-2 text-lg">
                        <RefreshCw className="w-5 h-5 text-primary" />
                        Sinkronisasi Data Khanza
                    </CardTitle>
                </CardHeader>
                <CardContent className="p-6 space-y-4">
                    <p className="text-sm text-gray-600">
                        Bandingkan data yang sudah ditarik scheduler dengan data terbaru langsung dari Khanza, lalu pilih field mana yang
                        ingin diperbarui. Khanza hanya dibaca, dan data yang sudah diproses lebih lanjut tidak bisa diubah.
                    </p>

                    {/* Modul */}
                    <div className="flex flex-wrap gap-2">
                        {loadingModules && <Loader2 className="w-4 h-4 animate-spin text-gray-400" />}
                        {modules.map((m) => (
                            <button
                                key={m.code}
                                type="button"
                                onClick={() => setModuleCode(m.code)}
                                className={`px-4 py-2 rounded-md text-sm font-medium border transition-colors ${
                                    m.code === moduleCode
                                        ? 'bg-primary text-white border-primary'
                                        : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50'
                                }`}
                            >
                                {m.label}
                            </button>
                        ))}
                    </div>

                    {activeModule && (
                        <div className="flex items-start gap-2 rounded-md bg-gray-50 border border-gray-100 px-3 py-2 text-xs text-gray-600">
                            <Info className="w-4 h-4 mt-0.5 shrink-0 text-gray-400" />
                            <span>
                                {activeModule.lockRule}
                                {moduleCode === 'mf' && !search && (
                                    <strong className="block mt-1 text-gray-700">
                                        Tanpa pencarian hanya menampilkan tindakan 31 hari terakhir. Cari no. rawat, pasien, dokter, atau tindakan
                                        untuk data yang lebih lama.
                                    </strong>
                                )}
                            </span>
                        </div>
                    )}

                    {/* Filter */}
                    <div className="flex flex-col md:flex-row md:items-center gap-3">
                        <div className="flex rounded-md border border-gray-200 overflow-hidden w-fit">
                            {LOCK_FILTERS.map((f) => (
                                <button
                                    key={f.value}
                                    type="button"
                                    onClick={() => setLockState(f.value)}
                                    className={`px-3 py-2 text-sm ${
                                        f.value === lockState ? 'bg-primary/10 text-primary font-semibold' : 'bg-white text-gray-600 hover:bg-gray-50'
                                    }`}
                                >
                                    {f.label}
                                </button>
                            ))}
                        </div>
                        <div className="relative flex-1 max-w-md">
                            <Search className="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                            <input
                                type="text"
                                className="flex h-10 w-full rounded-md border border-input bg-white pl-9 pr-3 py-2 text-sm"
                                placeholder={`Cari ${activeModule?.keyLabel ?? 'data'}, nama, supplier/pasien…`}
                                value={searchInput}
                                onChange={(e) => setSearchInput(e.target.value)}
                            />
                        </div>
                        <Button
                            onClick={handleCompare}
                            disabled={selectedRecords.length === 0 || comparing}
                            className="gap-2 md:ml-auto"
                        >
                            {comparing ? <Loader2 className="w-4 h-4 animate-spin" /> : <ArrowLeftRight className="w-4 h-4" />}
                            Bandingkan dengan Khanza ({selectedRecords.length})
                        </Button>
                    </div>
                </CardContent>
            </Card>

            {/* Daftar */}
            <Card>
                <CardContent className="p-0">
                    {recordsError && (
                        <div className="m-4 rounded-md bg-red-50 border border-red-100 px-3 py-2 text-sm text-red-700">
                            {(recordsError as any)?.data?.message || 'Gagal memuat data'}
                        </div>
                    )}
                    <DataTable
                        highlightOnHover
                        className="whitespace-nowrap table-hover"
                        minHeight={200}
                        fetching={fetchingRecords}
                        records={records}
                        idAccessor="key"
                        selectedRecords={selectedRecords}
                        onSelectedRecordsChange={handleSelection}
                        isRecordSelectable={(r: KhanzaSyncListItem) => r.editable}
                        totalRecords={totalRecords}
                        recordsPerPage={pageSize}
                        page={page}
                        onPageChange={setPage}
                        recordsPerPageOptions={PAGE_SIZES}
                        onRecordsPerPageChange={(s: number) => {
                            setPageSize(s);
                            setPage(1);
                        }}
                        noRecordsText="Tidak ada data"
                        paginationText={({ from, to, totalRecords: t }) => `Menampilkan ${from}–${to} dari ${t} data`}
                        columns={[
                            {
                                accessor: 'title',
                                title: activeModule?.keyLabel ?? 'Data',
                                render: (r: KhanzaSyncListItem) => (
                                    <div className="flex flex-col">
                                        <span className="font-semibold text-gray-800">{r.title}</span>
                                        <span className="text-xs text-gray-500">{r.subtitle || '-'}</span>
                                    </div>
                                ),
                            },
                            {
                                accessor: 'info',
                                title: 'Keterangan',
                                render: (r: KhanzaSyncListItem) => <span className="text-gray-600">{r.info || '-'}</span>,
                            },
                            {
                                accessor: 'date',
                                title: 'Tanggal',
                                render: (r: KhanzaSyncListItem) => <span className="text-gray-600">{r.date ? FormatDate(r.date) : '-'}</span>,
                            },
                            {
                                accessor: 'amount',
                                title: 'Nilai',
                                textAlignment: 'right' as const,
                                render: (r: KhanzaSyncListItem) => (
                                    <span className="font-mono">{r.amount != null ? FormatNumberRounded(r.amount) : '-'}</span>
                                ),
                            },
                            {
                                accessor: 'editable',
                                title: 'Status',
                                render: (r: KhanzaSyncListItem) => (
                                    <div className="flex flex-col gap-1">
                                        {r.editable ? (
                                            <span className="px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 w-fit">
                                                Bisa diubah
                                            </span>
                                        ) : (
                                            <span className="flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 w-fit">
                                                <Lock className="w-3 h-3" />
                                                {r.lockReason || 'Terkunci'}
                                            </span>
                                        )}
                                        {r.rowCount > 1 && (
                                            <span className="px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700 w-fit">
                                                {r.rowCount} baris kembar{r.valuesDiffer ? ' · nilai tidak seragam' : ''}
                                            </span>
                                        )}
                                    </div>
                                ),
                            },
                        ]}
                    />
                </CardContent>
            </Card>

            {/* Hasil perbandingan */}
            {compareResults && (
                <Card className="border-primary/20">
                    <CardHeader className="border-b border-primary/10 bg-primary/5">
                        <div className="flex flex-col md:flex-row md:items-center justify-between gap-3">
                            <CardTitle className="flex items-center gap-2 text-lg text-primary">
                                <ArrowLeftRight className="w-5 h-5" />
                                Perbandingan Aplikasi vs Khanza
                            </CardTitle>
                            <div className="flex items-center gap-3 text-xs">
                                <span className="flex items-center gap-1">
                                    <span className="w-3 h-3 rounded bg-green-100 border border-green-300" /> Sama
                                </span>
                                <span className="flex items-center gap-1">
                                    <span className="w-3 h-3 rounded bg-red-100 border border-red-300" /> Berubah
                                </span>
                                <Button onClick={() => setConfirmOpen(true)} disabled={pendingItems.length === 0 || applying} className="gap-2">
                                    {applying ? <Loader2 className="w-4 h-4 animate-spin" /> : <CheckCircle2 className="w-4 h-4" />}
                                    Terapkan {pendingFieldCount} field
                                </Button>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent className="p-4 space-y-4">
                        {compareResults.map((record) => {
                            const selectable = selectableFields(record);
                            const recordChecked = checked[record.key] ?? [];
                            const result = applyResults[record.key];
                            return (
                                <div key={record.key} className="rounded-lg border border-gray-200 overflow-hidden">
                                    <div className="flex flex-col md:flex-row md:items-center justify-between gap-2 px-4 py-3 bg-gray-50 border-b">
                                        <div>
                                            <p className="font-semibold text-gray-800">{record.title}</p>
                                            <p className="text-xs text-gray-500">{record.subtitle || '-'}</p>
                                        </div>
                                        <div className="flex flex-wrap items-center gap-2 text-xs">
                                            {!record.foundInKhanza ? (
                                                <span className="flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-100 text-amber-700">
                                                    <AlertTriangle className="w-3 h-3" /> Tidak ditemukan di Khanza
                                                </span>
                                            ) : record.changedCount > 0 ? (
                                                <span className="px-2 py-0.5 rounded-full bg-red-100 text-red-700">
                                                    {record.changedCount} field berubah
                                                </span>
                                            ) : (
                                                <span className="px-2 py-0.5 rounded-full bg-green-100 text-green-700">Sama semua</span>
                                            )}
                                            {!record.editable && record.lockReason && (
                                                <span className="flex items-center gap-1 px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">
                                                    <Lock className="w-3 h-3" /> {record.lockReason}
                                                </span>
                                            )}
                                            {selectable.length > 0 && (
                                                <button type="button" className="text-primary hover:underline" onClick={() => toggleAllFields(record)}>
                                                    {recordChecked.length === selectable.length ? 'Batal pilih semua' : 'Pilih semua yang berubah'}
                                                </button>
                                            )}
                                        </div>
                                    </div>

                                    {(record.note || record.rowCount > 1 || record.appValuesDiffer) && (
                                        <div className="px-4 py-2 text-xs space-y-1 border-b bg-white">
                                            {record.note && <p className="text-amber-700">{record.note}</p>}
                                            {record.rowCount > 1 && (
                                                <p className="text-amber-700">
                                                    Data ini tercatat {record.rowCount} baris kembar di aplikasi; perubahan diterapkan ke semuanya.
                                                    {record.appValuesDiffer && ' Nilai antar baris tidak seragam — yang ditampilkan adalah baris pertama.'}
                                                </p>
                                            )}
                                        </div>
                                    )}

                                    <div className="overflow-x-auto">
                                        <table className="w-full text-sm">
                                            <thead>
                                                <tr className="text-left text-gray-500 border-b">
                                                    <th className="w-10 px-4 py-2" />
                                                    <th className="px-4 py-2 font-medium">Field</th>
                                                    <th className="px-4 py-2 font-medium">Aplikasi</th>
                                                    <th className="px-4 py-2 font-medium">Khanza (terbaru)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {record.fields.map((f) => {
                                                    const canPick = selectable.includes(f.column);
                                                    const rowClass = !record.foundInKhanza
                                                        ? 'bg-white'
                                                        : f.equal
                                                          ? 'bg-green-50/60'
                                                          : 'bg-red-50';
                                                    const valueClass = !record.foundInKhanza ? 'text-gray-600' : f.equal ? 'text-green-800' : 'text-red-700 font-medium';
                                                    return (
                                                        <tr key={f.column} className={`border-b last:border-0 ${rowClass}`}>
                                                            <td className="px-4 py-2">
                                                                {canPick && (
                                                                    <input
                                                                        type="checkbox"
                                                                        className="form-checkbox"
                                                                        checked={recordChecked.includes(f.column)}
                                                                        onChange={() => toggleField(record.key, f.column)}
                                                                    />
                                                                )}
                                                            </td>
                                                            <td className="px-4 py-2 text-gray-700">{f.label}</td>
                                                            <td className={`px-4 py-2 ${valueClass}`}>{formatValue(f, f.appValue)}</td>
                                                            <td className={`px-4 py-2 ${valueClass}`}>
                                                                {record.foundInKhanza ? formatValue(f, f.khanzaValue) : <span className="text-gray-400">—</span>}
                                                            </td>
                                                        </tr>
                                                    );
                                                })}
                                            </tbody>
                                        </table>
                                    </div>

                                    {result && (
                                        <div
                                            className={`flex items-center gap-2 px-4 py-2 text-xs border-t ${
                                                result.success ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'
                                            }`}
                                        >
                                            {result.success ? <CheckCircle2 className="w-4 h-4" /> : <XCircle className="w-4 h-4" />}
                                            {result.message}
                                        </div>
                                    )}
                                </div>
                            );
                        })}
                    </CardContent>
                </Card>
            )}

            <ConfirmationModal
                isOpen={confirmOpen}
                onClose={() => !applying && setConfirmOpen(false)}
                onConfirm={handleApply}
                title="Terapkan Data dari Khanza"
                confirmText="Ya, Terapkan"
                cancelText="Batal"
                loading={applying}
                keepOpenOnConfirm
                message={
                    <div className="text-left space-y-2 text-sm">
                        <p>
                            <strong>{pendingFieldCount} field</strong> pada <strong>{pendingItems.length} data</strong> akan ditimpa dengan nilai terbaru
                            dari Khanza.
                        </p>
                        <p className="text-gray-500">
                            Nilai Khanza diambil ulang saat diterapkan, status kunci diperiksa ulang, dan data yang sudah berubah sejak dibandingkan
                            akan ditolak. Setiap perubahan tercatat di audit trail.
                        </p>
                    </div>
                }
            />
        </div>
    );
};

export default KhanzaSyncPage;
