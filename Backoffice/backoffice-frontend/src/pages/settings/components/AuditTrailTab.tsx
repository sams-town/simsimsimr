import { useEffect, useMemo, useState } from 'react';
import { useTranslation } from 'react-i18next';
import { DataTable } from 'mantine-datatable';
import Flatpickr from 'react-flatpickr';
import 'flatpickr/dist/flatpickr.css';
import { Eye, Loader2, RefreshCw, Search, ShieldCheck } from 'lucide-react';

import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import {
    useGetAuditLogDetailQuery,
    useGetAuditLogListQuery,
    useGetAuditLogOptionsQuery,
} from '@/store/api/auditLog/auditLogApiSlice';
import { FormatDateTime, formatDateForAPI } from '@/utils/formatDate';
import { useDebounce } from '@/hooks/useDebounce';

interface AuditLogRow {
    id: number;
    module?: string;
    refId?: string;
    operation?: string;
    userId?: number | null;
    userName?: string | null;
    roleName?: string | null;
    branchId?: number | null;
    branchName?: string | null;
    ipAddress?: string | null;
    status?: string | null;
    createdAt?: string | null;
}

const DESTRUCTIVE_OPERATIONS = ['Batal Posting', 'Batal Tukar Faktur', 'Void', 'Ditolak', 'Dihapus', 'Dinonaktifkan'];

const statusBadge = (status?: string | null) => {
    const base = 'text-xs px-2 py-0.5 rounded border';
    if (status === 'Failed') return <span className={`${base} bg-red-50 text-red-700 border-red-200`}>{status}</span>;
    if (status === 'Backfill') return <span className={`${base} bg-gray-50 text-gray-600 border-gray-200`}>{status}</span>;
    if (!status) return <span className="text-gray-400">-</span>;
    return <span className={`${base} bg-emerald-50 text-emerald-700 border-emerald-200`}>{status}</span>;
};

const operationBadge = (operation?: string) => {
    if (!operation) return <span className="text-gray-400">-</span>;
    const destructive = DESTRUCTIVE_OPERATIONS.includes(operation);
    return (
        <span
            className={`text-xs px-2 py-0.5 rounded border ${
                destructive
                    ? 'bg-red-50 text-red-700 border-red-200'
                    : 'bg-sky-50 text-sky-700 border-sky-200'
            }`}
        >
            {operation}
        </span>
    );
};

/** Pretty-print a jsonb payload that arrives as a JSON string. */
const formatPayload = (raw?: string | null): string | null => {
    if (!raw) return null;
    try {
        const parsed = typeof raw === 'string' ? JSON.parse(raw) : raw;
        return JSON.stringify(parsed, null, 2);
    } catch {
        return String(raw);
    }
};

const AuditTrailTab = () => {
    const { t } = useTranslation();

    const [page, setPage] = useState(1);
    const [pageSize, setPageSize] = useState(10);
    const [search, setSearch] = useState('');
    const [filterModule, setFilterModule] = useState('');
    const [filterOperation, setFilterOperation] = useState('');
    const [filterUserId, setFilterUserId] = useState('');
    const [dateRange, setDateRange] = useState<Date[]>([]);
    const [detailId, setDetailId] = useState<number | null>(null);

    const [debouncedSearch] = useDebounce(search, 400);

    const { data: optionsData } = useGetAuditLogOptionsQuery(undefined);
    const options = optionsData?.data ?? {};
    const moduleOptions: string[] = options.modules ?? [];
    const operationOptions: string[] = options.operations ?? [];
    const userOptions: { userId: number; displayName: string }[] = options.users ?? [];

    const queryParams = useMemo(
        () => ({
            keyword: debouncedSearch,
            module: filterModule,
            operation: filterOperation,
            userId: filterUserId,
            from: dateRange[0] ? formatDateForAPI(dateRange[0]) : '',
            to: dateRange[1] ? formatDateForAPI(dateRange[1]) : '',
            page,
            pageSize,
            orderBy: 'created_at',
            orderType: 'desc',
        }),
        [debouncedSearch, filterModule, filterOperation, filterUserId, dateRange, page, pageSize]
    );

    const {
        data: listData,
        isLoading,
        isFetching,
        isError,
        refetch,
    } = useGetAuditLogListQuery(queryParams);

    const { data: detailData, isFetching: isFetchingDetail } = useGetAuditLogDetailQuery(detailId as number, {
        skip: detailId === null,
    });

    const records: AuditLogRow[] = listData?.data ?? [];
    const totalRecords: number = listData?.totalData ?? 0;
    const detail = detailData?.data;

    // Any filter change invalidates the current page number.
    useEffect(() => {
        setPage(1);
    }, [debouncedSearch, filterModule, filterOperation, filterUserId, dateRange, pageSize]);

    const resetFilters = () => {
        setSearch('');
        setFilterModule('');
        setFilterOperation('');
        setFilterUserId('');
        setDateRange([]);
    };

    const hasActiveFilter =
        !!search || !!filterModule || !!filterOperation || !!filterUserId || dateRange.length > 0;

    const selectClass =
        'h-9 w-full rounded-md border border-gray-200 bg-white px-2 text-sm text-gray-700 focus:border-primary focus:outline-none';

    return (
        <div className="space-y-4">
            <Card>
                <CardHeader className="pb-3">
                    <div className="flex items-center justify-between gap-2">
                        <div>
                            <CardTitle className="flex items-center gap-2">
                                <ShieldCheck className="h-4 w-4 text-primary" />
                                {t('audit_trail.title', 'Audit Trail')}
                            </CardTitle>
                            <p className="mt-1 text-sm text-gray-500">
                                {t('audit_trail.subtitle', 'Seluruh aktivitas transaksi yang tercatat di sistem.')}
                            </p>
                        </div>
                        <Button
                            variant="outline"
                            size="sm"
                            onClick={() => refetch()}
                            disabled={isFetching}
                            className="gap-2"
                        >
                            {isFetching ? <Loader2 className="h-4 w-4 animate-spin" /> : <RefreshCw className="h-4 w-4" />}
                        </Button>
                    </div>
                </CardHeader>

                <CardContent className="space-y-3">
                    <div className="grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-5">
                        <div className="relative lg:col-span-2">
                            <Search className="absolute left-2.5 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                            <Input
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                placeholder={t('audit_trail.search_placeholder', 'Cari modul, no. referensi, atau aksi...')}
                                className="pl-8"
                            />
                        </div>

                        <select
                            value={filterModule}
                            onChange={(e) => setFilterModule(e.target.value)}
                            className={selectClass}
                            aria-label={t('audit_trail.filter_module', 'Jenis Transaksi')}
                        >
                            <option value="">{t('audit_trail.all_modules', 'Semua Jenis')}</option>
                            {moduleOptions.map((m) => (
                                <option key={m} value={m}>
                                    {m}
                                </option>
                            ))}
                        </select>

                        <select
                            value={filterOperation}
                            onChange={(e) => setFilterOperation(e.target.value)}
                            className={selectClass}
                            aria-label={t('audit_trail.filter_operation', 'Aksi')}
                        >
                            <option value="">{t('audit_trail.all_operations', 'Semua Aksi')}</option>
                            {operationOptions.map((o) => (
                                <option key={o} value={o}>
                                    {o}
                                </option>
                            ))}
                        </select>

                        <select
                            value={filterUserId}
                            onChange={(e) => setFilterUserId(e.target.value)}
                            className={selectClass}
                            aria-label={t('audit_trail.filter_user', 'User')}
                        >
                            <option value="">{t('audit_trail.all_users', 'Semua User')}</option>
                            {userOptions.map((u) => (
                                <option key={u.userId} value={u.userId}>
                                    {u.displayName}
                                </option>
                            ))}
                        </select>
                    </div>

                    <div className="flex flex-wrap items-center gap-3">
                        <div className="w-full md:w-72">
                            <Flatpickr
                                value={dateRange}
                                options={{ mode: 'range', dateFormat: 'd M Y' }}
                                className="h-9 w-full rounded-md border border-gray-200 bg-white px-3 text-sm text-gray-700 focus:border-primary focus:outline-none"
                                placeholder={`${t('audit_trail.filter_date_from', 'Dari Tanggal')} - ${t(
                                    'audit_trail.filter_date_to',
                                    'Sampai Tanggal'
                                )}`}
                                onChange={(dates: Date[]) => setDateRange(dates)}
                            />
                        </div>

                        {hasActiveFilter && (
                            <Button variant="ghost" size="sm" onClick={resetFilters}>
                                {t('audit_trail.reset_filter', 'Reset Filter')}
                            </Button>
                        )}
                    </div>
                </CardContent>

                <CardContent className="p-0">
                    {isError ? (
                        <p className="py-10 text-center text-sm text-red-500">
                            {t('audit_trail.error', 'Gagal memuat audit trail.')}
                        </p>
                    ) : (
                        <div className="datatables">
                            <DataTable
                                highlightOnHover
                                className="whitespace-nowrap"
                                records={records}
                                columns={[
                                    {
                                        accessor: 'createdAt',
                                        title: t('audit_trail.col_time', 'Waktu'),
                                        width: 170,
                                        render: (r: AuditLogRow) => (
                                            <span className="text-sm text-gray-700">{FormatDateTime(r.createdAt)}</span>
                                        ),
                                    },
                                    {
                                        accessor: 'module',
                                        title: t('audit_trail.col_module', 'Jenis Transaksi'),
                                        render: (r: AuditLogRow) => r.module ?? '-',
                                    },
                                    {
                                        accessor: 'refId',
                                        title: t('audit_trail.col_ref', 'No. Referensi'),
                                        render: (r: AuditLogRow) => (
                                            <span className="font-mono text-xs text-gray-600">{r.refId ?? '-'}</span>
                                        ),
                                    },
                                    {
                                        accessor: 'operation',
                                        title: t('audit_trail.col_operation', 'Aksi'),
                                        render: (r: AuditLogRow) => operationBadge(r.operation),
                                    },
                                    {
                                        accessor: 'userName',
                                        title: t('audit_trail.col_user', 'User'),
                                        render: (r: AuditLogRow) => (
                                            <div>
                                                <p className="text-sm text-gray-900">{r.userName ?? '-'}</p>
                                                {r.roleName && <p className="text-[11px] text-gray-400">{r.roleName}</p>}
                                            </div>
                                        ),
                                    },
                                    {
                                        accessor: 'branchName',
                                        title: t('audit_trail.col_branch', 'Cabang'),
                                        render: (r: AuditLogRow) => r.branchName ?? '-',
                                    },
                                    {
                                        accessor: 'status',
                                        title: t('audit_trail.col_status', 'Status'),
                                        render: (r: AuditLogRow) => statusBadge(r.status),
                                    },
                                    {
                                        accessor: 'actions',
                                        title: t('common.actions', 'Aksi'),
                                        width: 80,
                                        render: (r: AuditLogRow) => (
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                className="h-8 w-8 p-0"
                                                aria-label={t('audit_trail.view_detail', 'Lihat Detail')}
                                                onClick={() => setDetailId(r.id)}
                                            >
                                                <Eye className="h-4 w-4" />
                                            </Button>
                                        ),
                                    },
                                ]}
                                totalRecords={totalRecords}
                                recordsPerPage={pageSize}
                                page={page}
                                onPageChange={setPage}
                                recordsPerPageOptions={[10, 20, 30, 50]}
                                onRecordsPerPageChange={setPageSize}
                                minHeight={200}
                                noRecordsText={t('audit_trail.empty', 'Tidak ada data audit trail.')}
                                paginationText={({ from, to, totalRecords }) =>
                                    t('common.pagination_text', { from, to, totalRecords })
                                }
                                loadingText={t('common.loading', 'Memuat...')}
                                fetching={isLoading || isFetching}
                                loaderVariant="dots"
                                loaderColor="#648de1"
                            />
                        </div>
                    )}
                </CardContent>
            </Card>

            {/* Detail popup */}
            <Dialog open={detailId !== null} onOpenChange={(open) => !open && setDetailId(null)}>
                <DialogContent className="max-w-3xl">
                    <DialogHeader>
                        <DialogTitle>
                            {t('audit_trail.detail_title', 'Detail Audit Trail')}
                            {detail ? ` #${detail.id}` : ''}
                        </DialogTitle>
                    </DialogHeader>

                    {isFetchingDetail && !detail ? (
                        <div className="flex items-center justify-center py-10">
                            <Loader2 className="h-6 w-6 animate-spin text-primary" />
                        </div>
                    ) : detail ? (
                        <div className="max-h-[70vh] space-y-4 overflow-y-auto pr-2">
                            <div className="grid grid-cols-2 gap-3 text-sm">
                                <div>
                                    <span className="text-gray-500">{t('audit_trail.col_module', 'Jenis Transaksi')}:</span>{' '}
                                    {detail.module ?? '-'}
                                </div>
                                <div>
                                    <span className="text-gray-500">{t('audit_trail.col_ref', 'No. Referensi')}:</span>{' '}
                                    <span className="font-mono">{detail.refId ?? '-'}</span>
                                </div>
                                <div>
                                    <span className="text-gray-500">{t('audit_trail.col_operation', 'Aksi')}:</span>{' '}
                                    {operationBadge(detail.operation)}
                                </div>
                                <div>
                                    <span className="text-gray-500">{t('audit_trail.col_status', 'Status')}:</span>{' '}
                                    {statusBadge(detail.status)}
                                </div>
                                <div>
                                    <span className="text-gray-500">{t('audit_trail.col_user', 'User')}:</span>{' '}
                                    {detail.userName ?? '-'}
                                    {detail.roleName ? ` (${detail.roleName})` : ''}
                                </div>
                                <div>
                                    <span className="text-gray-500">{t('audit_trail.col_time', 'Waktu')}:</span>{' '}
                                    {FormatDateTime(detail.createdAt)}
                                </div>
                                <div>
                                    <span className="text-gray-500">{t('audit_trail.col_branch', 'Cabang')}:</span>{' '}
                                    {detail.branchName ?? '-'}
                                </div>
                                <div>
                                    <span className="text-gray-500">IP:</span>{' '}
                                    <span className="font-mono text-xs">{detail.ipAddress ?? '-'}</span>
                                </div>
                            </div>

                            {formatPayload(detail.beforeData) && (
                                <div>
                                    <p className="mb-1 text-sm font-medium text-gray-700">
                                        {t('audit_trail.detail_before', 'Data Sebelum')}
                                    </p>
                                    <pre className="max-h-64 overflow-auto rounded-lg border border-gray-100 bg-gray-50 p-3 text-xs text-gray-700">
                                        {formatPayload(detail.beforeData)}
                                    </pre>
                                </div>
                            )}

                            {formatPayload(detail.afterData) ? (
                                <div>
                                    <p className="mb-1 text-sm font-medium text-gray-700">
                                        {t('audit_trail.detail_after', 'Detail Aksi')}
                                    </p>
                                    <pre className="max-h-64 overflow-auto rounded-lg border border-gray-100 bg-gray-50 p-3 text-xs text-gray-700">
                                        {formatPayload(detail.afterData)}
                                    </pre>
                                </div>
                            ) : (
                                !formatPayload(detail.beforeData) && (
                                    <p className="text-sm text-gray-400">
                                        {t('audit_trail.detail_no_payload', 'Tidak ada detail tambahan.')}
                                    </p>
                                )
                            )}
                        </div>
                    ) : null}
                </DialogContent>
            </Dialog>
        </div>
    );
};

export default AuditTrailTab;
