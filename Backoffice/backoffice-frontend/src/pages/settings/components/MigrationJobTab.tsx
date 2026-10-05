import { useEffect, useMemo, useState } from 'react';
import { useTranslation } from 'react-i18next';
import { DataTable } from 'mantine-datatable';
import { Play, Loader2, Terminal, Eye, RefreshCw, CheckCircle2, XCircle, Clock, AlertTriangle, Info } from 'lucide-react';

import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { toastMessage } from '@/utils/toastUtils';
import {
    useCreateMigrationJobMutation,
    useGetMigrationJobDetailQuery,
    useGetMigrationJobStatusQuery,
    useGetMigrationJobsQuery,
} from '@/store/api/migrationJob/migrationJobApiSlice';
import type {
    MigrationJobDetail,
    MigrationJobListItem,
    MigrationJobStatus,
    ScriptRunningStatus,
} from '@/types/migrationJobType';

const SCRIPT_OPTIONS: Array<{ value: string; label: string }> = [
    { value: 'run_ar', label: 'run_ar (Account Receivable)' },
    { value: 'run_faktur', label: 'run_faktur (Tukar Faktur)' },
    { value: 'run_medical_fee', label: 'run_medical_fee (Jasa Medis)' },
    { value: 'run_purchase_order', label: 'run_purchase_order (Purchase Order)' },
    { value: 'run_journal', label: 'run_journal (Journal)' },
];

const todayYmd = () => {
    const d = new Date();
    const mm = String(d.getMonth() + 1).padStart(2, '0');
    const dd = String(d.getDate()).padStart(2, '0');
    return `${d.getFullYear()}-${mm}-${dd}`;
};

const formatDateTime = (iso: string | null | undefined) => {
    if (!iso) return '-';
    const d = new Date(iso);
    if (Number.isNaN(d.getTime())) return '-';
    return d.toLocaleString('id-ID', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
    });
};

const formatDateOnly = (iso: string | null | undefined) => {
    if (!iso) return '-';
    const d = new Date(iso);
    if (Number.isNaN(d.getTime())) return '-';
    return d.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' });
};

const formatDuration = (seconds: number | null | undefined) => {
    if (seconds === null || seconds === undefined) return '-';
    if (seconds < 60) return `${seconds}s`;
    const m = Math.floor(seconds / 60);
    const s = seconds % 60;
    if (m < 60) return `${m}m ${s}s`;
    const h = Math.floor(m / 60);
    return `${h}h ${m % 60}m`;
};

const statusBadge = (status: MigrationJobStatus) => {
    const map: Record<MigrationJobStatus, { cls: string; icon: JSX.Element; label: string }> = {
        pending: {
            cls: 'bg-amber-50 text-amber-700 border-amber-200',
            icon: <Clock className="h-3 w-3" />,
            label: 'Pending',
        },
        running: {
            cls: 'bg-blue-50 text-blue-700 border-blue-200',
            icon: <Loader2 className="h-3 w-3 animate-spin" />,
            label: 'Running',
        },
        success: {
            cls: 'bg-green-50 text-green-700 border-green-200',
            icon: <CheckCircle2 className="h-3 w-3" />,
            label: 'Success',
        },
        failed: {
            cls: 'bg-red-50 text-red-700 border-red-200',
            icon: <XCircle className="h-3 w-3" />,
            label: 'Failed',
        },
    };
    const info = map[status] ?? map.pending;
    return (
        <span className={`inline-flex items-center gap-1 rounded-full border px-2 py-0.5 text-xs font-medium ${info.cls}`}>
            {info.icon}
            {info.label}
        </span>
    );
};

const MigrationJobTab = () => {
    const { t } = useTranslation();

    // --- Form state ---
    const [scriptName, setScriptName] = useState<string>(SCRIPT_OPTIONS[0].value);
    const [targetDate, setTargetDate] = useState<string>(todayYmd());
    const [updateAllColumns, setUpdateAllColumns] = useState<boolean>(false);

    // --- Table state ---
    const [page, setPage] = useState<number>(1);
    const [pageSize, setPageSize] = useState<number>(10);
    const [filterScript, setFilterScript] = useState<string>('');
    const [filterStatus, setFilterStatus] = useState<string>('');

    // --- Detail modal state ---
    const [detailId, setDetailId] = useState<number | null>(null);

    // --- Queries ---
    const {
        data: statusResp,
        refetch: refetchStatus,
    } = useGetMigrationJobStatusQuery(undefined, {
        pollingInterval: 5000,
    });
    const runningStatus: ScriptRunningStatus[] = statusResp?.data ?? [];
    const hasActiveJob = runningStatus.some((s) => s.isRunning);

    const {
        data: listResp,
        isFetching: isFetchingList,
        refetch: refetchList,
    } = useGetMigrationJobsQuery(
        {
            scriptName: filterScript,
            status: filterStatus,
            page,
            pageSize,
            orderBy: 'requestedAt',
            orderType: 'desc',
        },
        { pollingInterval: hasActiveJob ? 5000 : 0 },
    );

    const jobs: MigrationJobListItem[] = listResp?.data?.data ?? [];
    const totalJobs: number = listResp?.data?.totalData ?? 0;

    const {
        data: detailResp,
        isFetching: isFetchingDetail,
        refetch: refetchDetail,
    } = useGetMigrationJobDetailQuery(detailId as number, {
        skip: detailId === null,
        // Force a fresh fetch each time the modal opens so we never serve a
        // 60s-old "running" snapshot when the worker has already finished.
        refetchOnMountOrArgChange: true,
    });
    const detail: MigrationJobDetail | undefined = detailResp?.data;
    const detailIsTerminal = detail?.status === 'success' || detail?.status === 'failed';

    // Poll detail every 3s only while the job is still active. Stop once it
    // reaches success/failed — DB row won't change after that.
    useEffect(() => {
        if (detailId === null) return;
        if (detailIsTerminal) return;
        const t = setInterval(() => { refetchDetail(); }, 3000);
        return () => clearInterval(t);
    }, [detailId, detailIsTerminal, refetchDetail]);

    const [createJob, { isLoading: isSubmitting }] = useCreateMigrationJobMutation();

    const currentScriptStatus = useMemo(
        () => runningStatus.find((s) => s.scriptName === scriptName),
        [runningStatus, scriptName],
    );

    const handleRun = async () => {
        if (!scriptName) {
            toastMessage(t('migration_job.toast.select_script', 'Pilih script terlebih dahulu'), 'error');
            return;
        }
        if (!targetDate) {
            toastMessage(t('migration_job.toast.select_date', 'Pilih tanggal terlebih dahulu'), 'error');
            return;
        }
        try {
            const res: any = await createJob({
                scriptName,
                targetDate,
                updateAllColumns,
            });
            if (res?.error) {
                const msg = res.error?.data?.message ?? t('migration_job.toast.run_failed', 'Gagal menjalankan script');
                toastMessage(typeof msg === 'string' ? msg : JSON.stringify(msg), 'error');
                return;
            }
            toastMessage(t('migration_job.toast.queued', 'Script berhasil di-antrikan'), 'success');
            refetchStatus();
            refetchList();
        } catch (e: any) {
            toastMessage(e?.message ?? t('migration_job.toast.run_failed', 'Gagal menjalankan script'), 'error');
        }
    };

    useEffect(() => {
        setPage(1);
    }, [filterScript, filterStatus, pageSize]);

    return (
        <div className="space-y-6">
            {/* Info banner */}
            <div className="bg-blue-50 border border-blue-100 rounded-xl p-4 flex items-start gap-3">
                <Info className="h-5 w-5 text-blue-500 mt-0.5 shrink-0" />
                <div>
                    <p className="text-sm text-blue-800 font-medium">
                        {t('migration_job.info_title', 'Migrasi Data Khanza (Manual)')}
                    </p>
                    <p className="text-sm text-blue-600 mt-1">
                        {t(
                            'migration_job.info_desc',
                            'Menjalankan script tarik data backdate yang tidak ter-capture scheduler otomatis. Gunakan saat scheduler melewatkan data karena sumber tidak punya last_update. Hanya 1 job per script yang bisa berjalan bersamaan.',
                        )}
                    </p>
                </div>
            </div>

            {/* Run form */}
            <Card className="border border-gray-100 shadow-sm rounded-xl">
                <CardHeader className="border-b border-gray-100 bg-gray-50/50 py-3">
                    <CardTitle className="text-sm font-semibold flex items-center gap-2">
                        <Terminal className="h-4 w-4 text-primary" />
                        {t('migration_job.run.title', 'Jalankan Script')}
                    </CardTitle>
                </CardHeader>
                <CardContent className="p-4 space-y-4">
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                        {/* Script */}
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">
                                {t('migration_job.run.script', 'Script')}
                            </label>
                            <select
                                className="form-select w-full rounded-lg border-gray-200"
                                value={scriptName}
                                onChange={(e) => setScriptName(e.target.value)}
                                disabled={isSubmitting}
                            >
                                {SCRIPT_OPTIONS.map((o) => {
                                    const st = runningStatus.find((s) => s.scriptName === o.value);
                                    const suffix = st?.isRunning ? ' — sedang berjalan' : '';
                                    return (
                                        <option key={o.value} value={o.value}>
                                            {o.label}{suffix}
                                        </option>
                                    );
                                })}
                            </select>
                            {currentScriptStatus?.isRunning && (
                                <p className="mt-1 text-xs text-amber-600 flex items-center gap-1">
                                    <AlertTriangle className="h-3 w-3" />
                                    {t(
                                        'migration_job.run.script_busy',
                                        'Script ini masih berjalan (job #{{id}}). Tunggu sampai selesai.',
                                        { id: currentScriptStatus.activeJobId },
                                    )}
                                </p>
                            )}
                        </div>

                        {/* Date */}
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">
                                {t('migration_job.run.target_date', 'Tanggal Target')}
                            </label>
                            <input
                                type="date"
                                className="form-input w-full rounded-lg border-gray-200"
                                value={targetDate}
                                onChange={(e) => setTargetDate(e.target.value)}
                                disabled={isSubmitting}
                            />
                            <p className="mt-1 text-xs text-gray-500">
                                {t('migration_job.run.target_date_hint', 'Diteruskan ke argumen --date')}
                            </p>
                        </div>

                        {/* Mode */}
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">
                                {t('migration_job.run.mode', 'Mode')}
                            </label>
                            <div className="flex flex-col gap-2 pt-1">
                                <label className="inline-flex items-center gap-2 text-sm text-gray-700">
                                    <input
                                        type="radio"
                                        name="migration-mode"
                                        className="form-radio"
                                        checked={!updateAllColumns}
                                        onChange={() => setUpdateAllColumns(false)}
                                        disabled={isSubmitting}
                                    />
                                    {t('migration_job.run.mode_skip', 'Skip existing (default)')}
                                </label>
                                <label className="inline-flex items-center gap-2 text-sm text-gray-700">
                                    <input
                                        type="radio"
                                        name="migration-mode"
                                        className="form-radio"
                                        checked={updateAllColumns}
                                        onChange={() => setUpdateAllColumns(true)}
                                        disabled={isSubmitting}
                                    />
                                    {t('migration_job.run.mode_update', 'Update data (--update-columns=all)')}
                                </label>
                            </div>
                        </div>
                    </div>

                    <div className="flex items-center justify-between gap-3 pt-2 border-t border-gray-100">
                        <div className="text-xs text-gray-500 font-mono truncate">
                            {`python script/${scriptName}.py --date ${targetDate}${updateAllColumns ? ' --update-columns=all' : ''}`}
                        </div>
                        <Button
                            onClick={handleRun}
                            disabled={isSubmitting || !!currentScriptStatus?.isRunning}
                            className="bg-primary hover:bg-primary/90 shrink-0"
                        >
                            {isSubmitting ? (
                                <>
                                    <Loader2 className="h-4 w-4 mr-2 animate-spin" />
                                    {t('migration_job.run.submitting', 'Mengantrikan...')}
                                </>
                            ) : (
                                <>
                                    <Play className="h-4 w-4 mr-2" />
                                    {t('migration_job.run.submit', 'Jalankan')}
                                </>
                            )}
                        </Button>
                    </div>
                </CardContent>
            </Card>

            {/* History */}
            <Card className="border border-gray-100 shadow-sm rounded-xl">
                <CardHeader className="border-b border-gray-100 bg-gray-50/50 py-3">
                    <div className="flex items-center justify-between flex-wrap gap-3">
                        <CardTitle className="text-sm font-semibold flex items-center gap-2">
                            <Clock className="h-4 w-4 text-primary" />
                            {t('migration_job.history.title', 'Riwayat Eksekusi')}
                        </CardTitle>
                        <div className="flex items-center gap-2">
                            <select
                                className="form-select text-sm rounded-lg border-gray-200"
                                value={filterScript}
                                onChange={(e) => setFilterScript(e.target.value)}
                            >
                                <option value="">{t('migration_job.history.filter_all_scripts', 'Semua script')}</option>
                                {SCRIPT_OPTIONS.map((o) => (
                                    <option key={o.value} value={o.value}>{o.value}</option>
                                ))}
                            </select>
                            <select
                                className="form-select text-sm rounded-lg border-gray-200"
                                value={filterStatus}
                                onChange={(e) => setFilterStatus(e.target.value)}
                            >
                                <option value="">{t('migration_job.history.filter_all_status', 'Semua status')}</option>
                                <option value="pending">Pending</option>
                                <option value="running">Running</option>
                                <option value="success">Success</option>
                                <option value="failed">Failed</option>
                            </select>
                            <Button
                                variant="outline"
                                size="icon"
                                onClick={() => { refetchList(); refetchStatus(); }}
                                disabled={isFetchingList}
                            >
                                {isFetchingList ? (
                                    <Loader2 className="h-4 w-4 animate-spin" />
                                ) : (
                                    <RefreshCw className="h-4 w-4" />
                                )}
                            </Button>
                        </div>
                    </div>
                </CardHeader>
                <CardContent className="p-0">
                    <div className="datatables">
                        <DataTable
                            highlightOnHover
                            className="whitespace-nowrap"
                            records={jobs}
                            columns={[
                                {
                                    accessor: 'id',
                                    title: 'ID',
                                    width: 70,
                                    render: (r: MigrationJobListItem) => <span className="font-mono text-xs text-gray-600">#{r.id}</span>,
                                },
                                {
                                    accessor: 'scriptName',
                                    title: t('migration_job.history.col.script', 'Script'),
                                    render: (r: MigrationJobListItem) => (
                                        <span className="font-mono text-sm text-gray-900">{r.scriptName}</span>
                                    ),
                                },
                                {
                                    accessor: 'targetDate',
                                    title: t('migration_job.history.col.target_date', 'Tanggal Target'),
                                    render: (r: MigrationJobListItem) => formatDateOnly(r.targetDate),
                                },
                                {
                                    accessor: 'updateColumns',
                                    title: t('migration_job.history.col.mode', 'Mode'),
                                    render: (r: MigrationJobListItem) =>
                                        r.updateColumns === 'all' ? (
                                            <span className="text-xs px-2 py-0.5 rounded bg-purple-50 text-purple-700 border border-purple-200">
                                                update
                                            </span>
                                        ) : (
                                            <span className="text-xs px-2 py-0.5 rounded bg-gray-50 text-gray-600 border border-gray-200">
                                                skip
                                            </span>
                                        ),
                                },
                                {
                                    accessor: 'status',
                                    title: 'Status',
                                    render: (r: MigrationJobListItem) => statusBadge(r.status),
                                },
                                {
                                    accessor: 'requestedByName',
                                    title: t('migration_job.history.col.requested_by', 'Pemicu'),
                                    render: (r: MigrationJobListItem) => r.requestedByName ?? '-',
                                },
                                {
                                    accessor: 'requestedAt',
                                    title: t('migration_job.history.col.requested_at', 'Dipicu'),
                                    render: (r: MigrationJobListItem) => formatDateTime(r.requestedAt),
                                },
                                {
                                    accessor: 'startedAt',
                                    title: t('migration_job.history.col.started_at', 'Mulai'),
                                    render: (r: MigrationJobListItem) => formatDateTime(r.startedAt),
                                },
                                {
                                    accessor: 'finishedAt',
                                    title: t('migration_job.history.col.finished_at', 'Selesai'),
                                    render: (r: MigrationJobListItem) => formatDateTime(r.finishedAt),
                                },
                                {
                                    accessor: 'durationSeconds',
                                    title: t('migration_job.history.col.duration', 'Durasi'),
                                    render: (r: MigrationJobListItem) => formatDuration(r.durationSeconds),
                                },
                                {
                                    accessor: 'actions',
                                    title: t('common.actions', 'Aksi'),
                                    width: 80,
                                    render: (r: MigrationJobListItem) => (
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            className="h-8 w-8 p-0"
                                            onClick={() => setDetailId(r.id)}
                                        >
                                            <Eye className="h-4 w-4" />
                                        </Button>
                                    ),
                                },
                            ]}
                            totalRecords={totalJobs}
                            recordsPerPage={pageSize}
                            page={page}
                            onPageChange={setPage}
                            recordsPerPageOptions={[10, 20, 30, 50]}
                            onRecordsPerPageChange={setPageSize}
                            minHeight={200}
                            paginationText={({ from, to, totalRecords }) =>
                                t('common.pagination_text', { from, to, totalRecords })
                            }
                            loadingText={t('common.loading', 'Memuat...')}
                            fetching={isFetchingList}
                            loaderVariant="dots"
                            loaderColor="#648de1"
                        />
                    </div>
                </CardContent>
            </Card>

            {/* Detail modal */}
            <Dialog open={detailId !== null} onOpenChange={(open) => { if (!open) setDetailId(null); }}>
                <DialogContent className="max-w-3xl">
                    <DialogHeader>
                        <div className="flex items-center justify-between gap-2 pr-8">
                            <DialogTitle>
                                {t('migration_job.detail.title', 'Detail Job')}{detail ? ` #${detail.id}` : ''}
                            </DialogTitle>
                            <Button
                                variant="outline"
                                size="sm"
                                onClick={() => refetchDetail()}
                                disabled={isFetchingDetail}
                                className="gap-2"
                            >
                                {isFetchingDetail ? (
                                    <Loader2 className="h-4 w-4 animate-spin" />
                                ) : (
                                    <RefreshCw className="h-4 w-4" />
                                )}
                                {t('common.refresh', 'Segarkan')}
                            </Button>
                        </div>
                    </DialogHeader>

                    {isFetchingDetail && !detail ? (
                        <div className="py-10 flex items-center justify-center">
                            <Loader2 className="h-6 w-6 animate-spin text-primary" />
                        </div>
                    ) : detail ? (
                        <div className="space-y-4 max-h-[70vh] overflow-y-auto pr-2">
                            <div className="grid grid-cols-2 gap-3 text-sm">
                                <div><span className="text-gray-500">Script:</span> <span className="font-mono">{detail.scriptName}</span></div>
                                <div><span className="text-gray-500">Status:</span> {statusBadge(detail.status)}</div>
                                <div><span className="text-gray-500">Tanggal Target:</span> {formatDateOnly(detail.targetDate)}</div>
                                <div><span className="text-gray-500">Mode:</span> {detail.updateColumns === 'all' ? 'update (--update-columns=all)' : 'skip existing'}</div>
                                <div><span className="text-gray-500">Pemicu:</span> {detail.requestedByName ?? '-'}</div>
                                <div><span className="text-gray-500">Dipicu:</span> {formatDateTime(detail.requestedAt)}</div>
                                <div><span className="text-gray-500">Mulai:</span> {formatDateTime(detail.startedAt)}</div>
                                <div><span className="text-gray-500">Selesai:</span> {formatDateTime(detail.finishedAt)}</div>
                                <div><span className="text-gray-500">Durasi:</span> {formatDuration(detail.durationSeconds)}</div>
                                <div><span className="text-gray-500">Exit code:</span> {detail.exitCode ?? '-'}</div>
                                <div><span className="text-gray-500">Worker:</span> {detail.workerHost ?? '-'}{detail.workerPid ? ` (pid ${detail.workerPid})` : ''}</div>
                            </div>

                            {detail.command && (
                                <div>
                                    <p className="text-xs font-semibold text-gray-500 uppercase mb-1">Command</p>
                                    <pre className="bg-gray-900 text-gray-100 text-xs p-3 rounded-lg overflow-x-auto whitespace-pre-wrap break-all">{detail.command}</pre>
                                </div>
                            )}

                            {detail.errorMessage && (
                                <div>
                                    <p className="text-xs font-semibold text-red-600 uppercase mb-1">Error</p>
                                    <pre className="bg-red-50 border border-red-100 text-red-800 text-xs p-3 rounded-lg overflow-x-auto whitespace-pre-wrap break-all">{detail.errorMessage}</pre>
                                </div>
                            )}

                            {detail.stdoutLog && (
                                <div>
                                    <p className="text-xs font-semibold text-gray-500 uppercase mb-1">stdout</p>
                                    <pre className="bg-gray-50 border border-gray-100 text-gray-800 text-xs p-3 rounded-lg overflow-auto whitespace-pre-wrap break-all max-h-80">{detail.stdoutLog}</pre>
                                </div>
                            )}

                            {detail.stderrLog && (
                                <div>
                                    <p className="text-xs font-semibold text-gray-500 uppercase mb-1">stderr</p>
                                    <pre className="bg-gray-50 border border-gray-100 text-gray-800 text-xs p-3 rounded-lg overflow-auto whitespace-pre-wrap break-all max-h-80">{detail.stderrLog}</pre>
                                </div>
                            )}

                            {!detail.stdoutLog && !detail.stderrLog && detail.status === 'running' && (
                                <p className="text-sm text-gray-500 italic">
                                    {t('migration_job.detail.still_running', 'Script masih berjalan — log akan muncul setelah selesai.')}
                                </p>
                            )}
                        </div>
                    ) : (
                        <p className="text-sm text-gray-500">{t('migration_job.detail.not_found', 'Job tidak ditemukan.')}</p>
                    )}
                </DialogContent>
            </Dialog>
        </div>
    );
};

export default MigrationJobTab;
