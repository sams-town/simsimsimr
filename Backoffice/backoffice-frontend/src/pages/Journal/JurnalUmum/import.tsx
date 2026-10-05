import { useEffect, useMemo, useRef, useState } from 'react';
import { useDispatch } from 'react-redux';
import { useNavigate } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import { DataTable } from 'mantine-datatable';
import {
    ArrowLeft,
    CheckCircle2,
    Clock,
    Download,
    Eye,
    FileSpreadsheet,
    Loader2,
    RefreshCw,
    Trash2,
    Upload,
    XCircle,
} from 'lucide-react';

import { setBreadcrumbTitle, setPageTitle, setTitle } from '@/store/themeConfigSlice';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { toastMessage } from '@/utils/toastUtils';
import { FormatDateTime } from '@/utils/formatDate';
import {
    useCancelJournalImportJobMutation,
    useDownloadJournalImportFileMutation,
    useEnqueueJournalImportMutation,
    useGetJournalImportJobDetailQuery,
    useGetJournalImportJobsQuery,
    useGetJournalImportQueueStatusQuery,
} from '@/store/api/journalImport/journalImportApiSlice';

interface ImportJobRow {
    id: number;
    fileName: string;
    fileSize?: number | null;
    importType: string;
    status: string;
    requestedByName?: string | null;
    requestedAt?: string | null;
    startedAt?: string | null;
    finishedAt?: string | null;
    totalRows?: number | null;
    journalCount?: number | null;
    errorCount?: number | null;
    errorMessage?: string | null;
    durationSeconds?: number | null;
}

interface ImportError {
    row: number;
    field: string;
    message: string;
}

const IMPORT_TYPES = [
    { value: 'standard', label: 'Template Standar' },
    { value: 'thb', label: 'Format THB' },
];

/** Poll cadence while the queue still has work. */
const POLL_INTERVAL_MS = 10000;

const formatBytes = (bytes?: number | null) => {
    if (!bytes) return '-';
    if (bytes < 1024) return `${bytes} B`;
    if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(0)} KB`;
    return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
};

const formatDuration = (seconds?: number | null) => {
    if (seconds === null || seconds === undefined) return '-';
    if (seconds < 60) return `${seconds} dtk`;
    const m = Math.floor(seconds / 60);
    const s = seconds % 60;
    return `${m}m ${s}s`;
};

const statusBadge = (status: string) => {
    const base = 'inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded border';
    switch (status) {
        case 'pending':
            return (
                <span className={`${base} bg-gray-50 text-gray-600 border-gray-200`}>
                    <Clock className="h-3 w-3" /> Menunggu
                </span>
            );
        case 'running':
            return (
                <span className={`${base} bg-sky-50 text-sky-700 border-sky-200`}>
                    <Loader2 className="h-3 w-3 animate-spin" /> Diproses
                </span>
            );
        case 'success':
            return (
                <span className={`${base} bg-emerald-50 text-emerald-700 border-emerald-200`}>
                    <CheckCircle2 className="h-3 w-3" /> Berhasil
                </span>
            );
        case 'failed':
            return (
                <span className={`${base} bg-red-50 text-red-700 border-red-200`}>
                    <XCircle className="h-3 w-3" /> Gagal
                </span>
            );
        default:
            return <span className={`${base} bg-gray-50 text-gray-600 border-gray-200`}>{status}</span>;
    }
};

const ImportJurnalUmum = () => {
    const dispatch = useDispatch();
    const navigate = useNavigate();
    const { t } = useTranslation();

    const fileInputRef = useRef<HTMLInputElement>(null);
    const [selectedFiles, setSelectedFiles] = useState<File[]>([]);
    const [importType, setImportType] = useState<string>('standard');
    const [page, setPage] = useState(1);
    const [pageSize, setPageSize] = useState(10);
    const [detailId, setDetailId] = useState<number | null>(null);

    const [enqueue, { isLoading: isUploading }] = useEnqueueJournalImportMutation();
    const [cancelJob, { isLoading: isCancelling }] = useCancelJournalImportJobMutation();
    const [downloadFile] = useDownloadJournalImportFileMutation();

    useEffect(() => {
        const title = t('journal_import.title', 'Import Jurnal Umum');
        dispatch(setPageTitle(title));
        dispatch(setTitle(title));
        dispatch(setBreadcrumbTitle(['Dashboard', 'Jurnal Umum', title]));
    }, [dispatch, t]);

    // Status endpoint is cheap, so it polls unconditionally and decides whether the heavier
    // list query needs to keep polling too. Same shape as MigrationJobTab.
    const { data: statusData } = useGetJournalImportQueueStatusQuery(undefined, {
        pollingInterval: POLL_INTERVAL_MS,
    });
    const hasActiveJob: boolean = statusData?.data?.hasActiveJob ?? false;

    const listParams = useMemo(
        () => ({ page, pageSize, orderBy: 'requestedAt', orderType: 'desc' }),
        [page, pageSize]
    );

    const {
        data: listData,
        isLoading,
        isFetching,
        refetch,
    } = useGetJournalImportJobsQuery(listParams, {
        pollingInterval: hasActiveJob ? POLL_INTERVAL_MS : 0,
    });

    const { data: detailData, isFetching: isFetchingDetail } = useGetJournalImportJobDetailQuery(
        detailId as number,
        { skip: detailId === null }
    );

    const records: ImportJobRow[] = listData?.data ?? [];
    const totalRecords: number = listData?.totalData ?? 0;
    const detail = detailData?.data;
    const detailErrors: ImportError[] = detail?.errors ?? [];

    const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const files = Array.from(e.target.files ?? []);
        if (files.length === 0) return;
        setSelectedFiles((prev) => [...prev, ...files]);
        if (fileInputRef.current) fileInputRef.current.value = '';
    };

    const removeSelected = (index: number) => {
        setSelectedFiles((prev) => prev.filter((_, i) => i !== index));
    };

    const handleUpload = async () => {
        if (selectedFiles.length === 0) return;

        try {
            const response: any = await enqueue({ files: selectedFiles, importType }).unwrap();
            const result = response?.data;
            const queued = result?.queued?.length ?? 0;
            const rejected = result?.rejected ?? [];

            if (queued > 0) {
                toastMessage(
                    t('journal_import.toast.queued', { count: queued, defaultValue: `${queued} file masuk antrian` }),
                    'success'
                );
            }
            // Partial rejects must not be swallowed by the success toast.
            rejected.forEach((r: { fileName: string; reason: string }) =>
                toastMessage(`${r.fileName}: ${r.reason}`, 'error')
            );

            setSelectedFiles([]);
            refetch();
        } catch (err: any) {
            toastMessage(err?.data?.message ?? t('journal_import.toast.upload_failed', 'Upload gagal'), 'error');
        }
    };

    const handleCancel = async (id: number) => {
        try {
            await cancelJob(id).unwrap();
            toastMessage(t('journal_import.toast.cancelled', 'Job dibatalkan'), 'success');
            refetch();
        } catch (err: any) {
            toastMessage(err?.data?.message ?? t('journal_import.toast.cancel_failed', 'Gagal membatalkan'), 'error');
        }
    };

    const handleDownload = async (row: ImportJobRow) => {
        try {
            const blob = await downloadFile(row.id).unwrap();
            const url = window.URL.createObjectURL(blob as Blob);
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', row.fileName);
            document.body.appendChild(link);
            link.click();
            link.remove();
            window.URL.revokeObjectURL(url);
        } catch {
            toastMessage(t('journal_import.toast.download_failed', 'Gagal mengunduh file'), 'error');
        }
    };

    const handleDownloadTemplate = () => {
        const baseUrl = import.meta.env.VITE_HOST || '';
        window.open(`${baseUrl}/Journal/general-journal-template`, '_blank');
    };

    return (
        <div className="space-y-6">
            <div className="flex items-center gap-2">
                <Button variant="ghost" size="sm" onClick={() => navigate('/jurnal-umum')} className="gap-1">
                    <ArrowLeft className="h-4 w-4" />
                    {t('common.back', 'Kembali')}
                </Button>
            </div>

            {/* Upload */}
            <Card>
                <CardHeader className="pb-3">
                    <CardTitle className="flex items-center gap-2">
                        <Upload className="h-4 w-4 text-primary" />
                        {t('journal_import.upload.title', 'Upload File Import')}
                    </CardTitle>
                    <p className="mt-1 text-sm text-gray-500">
                        {t(
                            'journal_import.upload.subtitle',
                            'Bisa pilih beberapa file sekaligus. File diproses di server secara berurutan — halaman ini akan memperbarui statusnya sendiri.'
                        )}
                    </p>
                </CardHeader>

                <CardContent className="space-y-4">
                    <div className="flex flex-wrap items-end gap-3">
                        <div>
                            <label className="mb-1 block text-sm font-medium text-gray-700">
                                {t('journal_import.upload.type', 'Jenis File')}
                            </label>
                            <select
                                value={importType}
                                onChange={(e) => setImportType(e.target.value)}
                                className="h-9 rounded-md border border-gray-200 bg-white px-2 text-sm text-gray-700 focus:border-primary focus:outline-none"
                            >
                                {IMPORT_TYPES.map((o) => (
                                    <option key={o.value} value={o.value}>
                                        {o.label}
                                    </option>
                                ))}
                            </select>
                        </div>

                        <input
                            ref={fileInputRef}
                            type="file"
                            accept=".xlsx"
                            multiple
                            onChange={handleFileChange}
                            className="hidden"
                        />

                        <Button variant="outline" onClick={() => fileInputRef.current?.click()} className="gap-2">
                            <FileSpreadsheet className="h-4 w-4" />
                            {t('journal_import.upload.choose', 'Pilih File')}
                        </Button>

                        {importType === 'standard' && (
                            <Button variant="ghost" onClick={handleDownloadTemplate} className="gap-2">
                                <Download className="h-4 w-4" />
                                {t('journal_import.upload.template', 'Download Template')}
                            </Button>
                        )}

                        <Button
                            onClick={handleUpload}
                            disabled={selectedFiles.length === 0 || isUploading}
                            className="gap-2"
                        >
                            {isUploading ? <Loader2 className="h-4 w-4 animate-spin" /> : <Upload className="h-4 w-4" />}
                            {t('journal_import.upload.submit', { count: selectedFiles.length, defaultValue: `Upload (${selectedFiles.length})` })}
                        </Button>
                    </div>

                    {selectedFiles.length > 0 && (
                        <ul className="divide-y divide-gray-100 rounded-lg border border-gray-100">
                            {selectedFiles.map((file, index) => (
                                <li key={`${file.name}-${index}`} className="flex items-center justify-between gap-2 px-3 py-2">
                                    <div className="flex min-w-0 items-center gap-2">
                                        <FileSpreadsheet className="h-4 w-4 shrink-0 text-emerald-600" />
                                        <span className="truncate text-sm text-gray-800">{file.name}</span>
                                        <span className="shrink-0 text-xs text-gray-400">{formatBytes(file.size)}</span>
                                    </div>
                                    <button
                                        type="button"
                                        onClick={() => removeSelected(index)}
                                        className="shrink-0 rounded p-1 text-gray-400 hover:bg-gray-100 hover:text-red-600"
                                        aria-label={t('common.delete', 'Hapus')}
                                    >
                                        <Trash2 className="h-4 w-4" />
                                    </button>
                                </li>
                            ))}
                        </ul>
                    )}

                    <p className="text-xs text-gray-400">
                        {t('journal_import.upload.hint', 'Hanya file .xlsx, maksimal 20 MB per file.')}
                    </p>
                </CardContent>
            </Card>

            {/* History */}
            <Card>
                <CardHeader className="pb-3">
                    <div className="flex items-center justify-between gap-2">
                        <div>
                            <CardTitle>{t('journal_import.history.title', 'Riwayat Upload')}</CardTitle>
                            {hasActiveJob && (
                                <p className="mt-1 flex items-center gap-1.5 text-sm text-sky-600">
                                    <Loader2 className="h-3 w-3 animate-spin" />
                                    {t('journal_import.history.processing', 'Ada file sedang diproses — status diperbarui otomatis.')}
                                </p>
                            )}
                        </div>
                        <Button variant="outline" size="sm" onClick={() => refetch()} disabled={isFetching} className="gap-2">
                            {isFetching ? <Loader2 className="h-4 w-4 animate-spin" /> : <RefreshCw className="h-4 w-4" />}
                        </Button>
                    </div>
                </CardHeader>

                <CardContent className="p-0">
                    <div className="datatables">
                        <DataTable
                            highlightOnHover
                            className="whitespace-nowrap"
                            records={records}
                            columns={[
                                {
                                    accessor: 'fileName',
                                    title: t('journal_import.history.col_file', 'Nama File'),
                                    render: (r: ImportJobRow) => (
                                        <div>
                                            <p className="text-sm font-medium text-gray-900">{r.fileName}</p>
                                            <p className="text-[11px] text-gray-400">
                                                {formatBytes(r.fileSize)} ·{' '}
                                                {r.importType === 'thb' ? 'THB' : 'Standar'}
                                            </p>
                                        </div>
                                    ),
                                },
                                {
                                    accessor: 'status',
                                    title: t('journal_import.history.col_status', 'Status'),
                                    render: (r: ImportJobRow) => statusBadge(r.status),
                                },
                                {
                                    accessor: 'summary',
                                    title: t('journal_import.history.col_summary', 'Ringkasan'),
                                    render: (r: ImportJobRow) => {
                                        if (r.status === 'pending') return <span className="text-gray-400">-</span>;
                                        return (
                                            <div className="text-xs">
                                                <span className="text-gray-500">
                                                    {t('journal_import.history.rows', 'Baris')}: {r.totalRows ?? 0}
                                                </span>
                                                {r.status === 'success' && (
                                                    <span className="ml-2 text-emerald-600">
                                                        {r.journalCount ?? 0} {t('journal_import.history.journals', 'jurnal')}
                                                    </span>
                                                )}
                                                {(r.errorCount ?? 0) > 0 && (
                                                    <span className="ml-2 text-red-600">
                                                        {r.errorCount} {t('journal_import.history.errors', 'error')}
                                                    </span>
                                                )}
                                            </div>
                                        );
                                    },
                                },
                                {
                                    accessor: 'requestedByName',
                                    title: t('journal_import.history.col_user', 'Diupload Oleh'),
                                    render: (r: ImportJobRow) => r.requestedByName ?? '-',
                                },
                                {
                                    accessor: 'requestedAt',
                                    title: t('journal_import.history.col_time', 'Waktu Upload'),
                                    render: (r: ImportJobRow) => FormatDateTime(r.requestedAt),
                                },
                                {
                                    accessor: 'durationSeconds',
                                    title: t('journal_import.history.col_duration', 'Durasi'),
                                    render: (r: ImportJobRow) => formatDuration(r.durationSeconds),
                                },
                                {
                                    accessor: 'actions',
                                    title: t('common.actions', 'Aksi'),
                                    width: 130,
                                    render: (r: ImportJobRow) => (
                                        <div className="flex items-center gap-1">
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                className="h-8 w-8 p-0"
                                                aria-label={t('journal_import.history.detail', 'Detail')}
                                                onClick={() => setDetailId(r.id)}
                                            >
                                                <Eye className="h-4 w-4" />
                                            </Button>
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                className="h-8 w-8 p-0"
                                                aria-label={t('journal_import.history.download', 'Unduh file')}
                                                onClick={() => handleDownload(r)}
                                            >
                                                <Download className="h-4 w-4" />
                                            </Button>
                                            {r.status === 'pending' && (
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    className="h-8 w-8 p-0 text-red-600"
                                                    aria-label={t('journal_import.history.cancel', 'Batalkan')}
                                                    disabled={isCancelling}
                                                    onClick={() => handleCancel(r.id)}
                                                >
                                                    <Trash2 className="h-4 w-4" />
                                                </Button>
                                            )}
                                        </div>
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
                            noRecordsText={t('journal_import.history.empty', 'Belum ada riwayat upload.')}
                            paginationText={({ from, to, totalRecords }) =>
                                t('common.pagination_text', { from, to, totalRecords })
                            }
                            loadingText={t('common.loading', 'Memuat...')}
                            fetching={isLoading}
                            loaderVariant="dots"
                            loaderColor="#4361ee"
                        />
                    </div>
                </CardContent>
            </Card>

            {/* Detail */}
            <Dialog open={detailId !== null} onOpenChange={(open) => !open && setDetailId(null)}>
                <DialogContent className="max-w-3xl">
                    <DialogHeader>
                        <DialogTitle>
                            {t('journal_import.detail.title', 'Detail Import')}
                            {detail ? ` — ${detail.fileName}` : ''}
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
                                    <span className="text-gray-500">Status:</span> {statusBadge(detail.status)}
                                </div>
                                <div>
                                    <span className="text-gray-500">
                                        {t('journal_import.history.col_duration', 'Durasi')}:
                                    </span>{' '}
                                    {formatDuration(detail.durationSeconds)}
                                </div>
                                <div>
                                    <span className="text-gray-500">{t('journal_import.history.rows', 'Baris')}:</span>{' '}
                                    {detail.totalRows ?? 0}
                                </div>
                                <div>
                                    <span className="text-gray-500">
                                        {t('journal_import.history.journals', 'jurnal')}:
                                    </span>{' '}
                                    {detail.journalCount ?? 0}
                                </div>
                                <div>
                                    <span className="text-gray-500">
                                        {t('journal_import.history.col_user', 'Diupload Oleh')}:
                                    </span>{' '}
                                    {detail.requestedByName ?? '-'}
                                </div>
                                <div>
                                    <span className="text-gray-500">
                                        {t('journal_import.history.col_time', 'Waktu Upload')}:
                                    </span>{' '}
                                    {FormatDateTime(detail.requestedAt)}
                                </div>
                            </div>

                            {detail.errorMessage && (
                                <div className="rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                                    {detail.errorMessage}
                                </div>
                            )}

                            {detailErrors.length > 0 && (
                                <div>
                                    <p className="mb-2 text-sm font-medium text-gray-700">
                                        {t('journal_import.detail.errors', 'Daftar Error')} ({detailErrors.length})
                                    </p>
                                    <div className="max-h-64 overflow-y-auto rounded-lg border border-gray-100">
                                        <table className="w-full text-xs">
                                            <thead className="sticky top-0 bg-gray-50">
                                                <tr className="text-left text-gray-600">
                                                    <th className="px-3 py-2 font-medium">
                                                        {t('journal_import.detail.row', 'Baris')}
                                                    </th>
                                                    <th className="px-3 py-2 font-medium">
                                                        {t('journal_import.detail.field', 'Kolom')}
                                                    </th>
                                                    <th className="px-3 py-2 font-medium">
                                                        {t('journal_import.detail.message', 'Pesan')}
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody className="divide-y divide-gray-100">
                                                {detailErrors.map((err, i) => (
                                                    <tr key={i}>
                                                        <td className="px-3 py-2 font-mono text-gray-500">{err.row}</td>
                                                        <td className="px-3 py-2 text-gray-700">{err.field}</td>
                                                        <td className="px-3 py-2 text-gray-700">{err.message}</td>
                                                    </tr>
                                                ))}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            )}

                            {detail.status === 'success' && detailErrors.length === 0 && (
                                <p className="text-sm text-emerald-600">
                                    {t('journal_import.detail.all_ok', 'Semua baris berhasil diimport.')}
                                </p>
                            )}
                        </div>
                    ) : null}
                </DialogContent>
            </Dialog>
        </div>
    );
};

export default ImportJurnalUmum;
