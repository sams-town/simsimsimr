import { useEffect, useState } from 'react';
import { useTranslation } from 'react-i18next';
import { useDispatch } from 'react-redux';
import { DataTable } from 'mantine-datatable';
import Tippy from '@tippyjs/react';
import 'tippy.js/dist/tippy.css';
import { Lock, LockOpen, BookLock, Info } from 'lucide-react';

import { setBreadcrumbTitle, setPageTitle, setTitle } from '@/store/themeConfigSlice';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Dialog, DialogContent, DialogHeader, DialogFooter, DialogTitle, DialogDescription,
} from '@/components/ui/dialog';
import TimelineButton from '@/components/ActivityTimeline/TimelineButton';
import { AUDIT_MODULE } from '@/components/ActivityTimeline/modules';
import { FormatDateTime } from '@/utils/formatDate';
import { responseCallback } from '@/utils/responseCallback';
import { toastMessage } from '@/utils/toastUtils';
import {
    useGetPeriodClosingsQuery,
    useClosePeriodMutation,
    useReopenPeriodMutation,
} from '@/store/api/periodClosing/periodClosingApiSlice';

const MONTHS = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
const now = new Date();

const TutupBukuIndex = () => {
    const dispatch = useDispatch();
    const { t } = useTranslation();

    const [page, setPage] = useState(1);
    const PAGE_SIZES = [10, 20, 30, 50];
    const [pageSize, setPageSize] = useState(PAGE_SIZES[0]);
    const [filterYear, setFilterYear] = useState<string>('');
    const [filterStatus, setFilterStatus] = useState<string>('');

    const [showClose, setShowClose] = useState(false);
    const [form, setForm] = useState({
        periodType: 'Monthly',
        year: now.getUTCFullYear(),
        month: now.getUTCMonth() + 1,
        allBranch: false,
        generateClosingEntries: false,
        note: '',
    });

    const [showReopen, setShowReopen] = useState(false);
    const [reopenRow, setReopenRow] = useState<any>(null);
    const [reopenNote, setReopenNote] = useState('');

    const { data: list, refetch, isLoading } = useGetPeriodClosingsQuery<any>({ year: filterYear, status: filterStatus, page, pageSize });
    const [closePeriod, { isLoading: isClosing }] = useClosePeriodMutation();
    const [reopenPeriod, { isLoading: isReopening }] = useReopenPeriodMutation();

    useEffect(() => {
        dispatch(setPageTitle(t('period_closing.title', 'Tutup Buku')));
        dispatch(setTitle(t('period_closing.title', 'Tutup Buku')));
        dispatch(setBreadcrumbTitle([t('menu.dashboard', 'Dashboard'), t('menu.report', 'Laporan'), t('period_closing.title', 'Tutup Buku')]));
    }, [dispatch]);

    useEffect(() => { setPage(1); }, [filterYear, filterStatus, pageSize]);

    const submitClose = async () => {
        try {
            const body = {
                periodType: form.periodType,
                year: Number(form.year),
                month: form.periodType === 'Monthly' ? Number(form.month) : null,
                allBranch: form.allBranch,
                generateClosingEntries: form.generateClosingEntries,
                note: form.note,
            };
            const response = await closePeriod(body);
            responseCallback(response, () => { setShowClose(false); refetch(); }, null);
        } catch (err: any) {
            toastMessage(err?.message || 'Error', 'error');
        }
    };

    const submitReopen = async () => {
        if (!reopenRow) return;
        try {
            const response = await reopenPeriod({ id: reopenRow.id, note: reopenNote });
            responseCallback(response, () => { setShowReopen(false); setReopenNote(''); refetch(); }, null);
        } catch (err: any) {
            toastMessage(err?.message || 'Error', 'error');
        }
    };

    const periodLabel = (r: any) => r.periodType === 'Yearly' ? `${r.year}` : `${MONTHS[(r.month || 1) - 1]} ${r.year}`;

    return (
        <div className="space-y-6">
            <Card>
                <CardHeader className="border-b border-gray-100 pb-4">
                    <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
                        <div className="flex items-center gap-3">
                            <div className="p-2 rounded-lg bg-primary/10"><BookLock className="h-5 w-5 text-primary" /></div>
                            <div>
                                <CardTitle className="text-lg">{t('period_closing.title', 'Tutup Buku')}</CardTitle>
                                <p className="text-sm text-gray-500 mt-0.5">{t('period_closing.subtitle', 'Tutup buku periode (bulanan/tahunan). Periode yang ditutup terkunci dari perubahan jurnal.')}</p>
                            </div>
                        </div>
                        <Button onClick={() => setShowClose(true)} className="gap-2">
                            <Lock className="h-4 w-4" />
                            <span className="hidden sm:inline">{t('period_closing.close_action', 'Tutup Buku')}</span>
                        </Button>
                    </div>

                    <div className="flex flex-wrap items-center gap-3">
                        <Input type="number" placeholder={t('period_closing.filter_year', 'Tahun')} className="w-[120px]" value={filterYear}
                            onChange={(e) => setFilterYear(e.target.value)} />
                        <select className="flex h-10 w-[160px] rounded-md border border-input bg-background px-3 py-2 text-sm"
                            value={filterStatus} onChange={(e) => setFilterStatus(e.target.value)}>
                            <option value="">{t('period_closing.filter_status', 'Semua Status')}</option>
                            <option value="Closed">{t('period_closing.status_closed', 'Ditutup')}</option>
                            <option value="Reopened">{t('period_closing.status_reopened', 'Dibuka Kembali')}</option>
                        </select>
                    </div>
                </CardHeader>

                <CardContent className="pt-6">
                    <div className="datatables z-0 rounded-lg border border-gray-100">
                        <DataTable
                            highlightOnHover
                            className="whitespace-nowrap table-hover"
                            records={list?.data || []}
                            columns={[
                                { accessor: 'periodType', title: t('period_closing.table.type', 'Tipe'), render: (r: any) => r.periodType === 'Yearly' ? t('period_closing.yearly', 'Tahunan') : t('period_closing.monthly', 'Bulanan') },
                                { accessor: 'period', title: t('period_closing.table.period', 'Periode'), render: (r: any) => <span className="font-semibold">{periodLabel(r)}</span> },
                                { accessor: 'branchName', title: t('period_closing.table.branch', 'Cabang'), render: (r: any) => r.branchId ? (r.branchName || r.branchId) : t('period_closing.all_branch', 'Semua Cabang') },
                                { accessor: 'generateClosingEntries', title: t('period_closing.table.closing_entry', 'Jurnal Penutup'), render: (r: any) => r.generateClosingEntries ? <span className="text-success text-xs font-semibold">Ya</span> : <span className="text-gray-400 text-xs">Tidak</span> },
                                {
                                    accessor: 'status', title: t('period_closing.table.status', 'Status'), textAlignment: 'center',
                                    render: (r: any) => (
                                        <span className={`px-2 py-1 rounded-full text-xs font-semibold ${r.status === 'Closed' ? 'bg-danger/10 text-danger' : 'bg-warning/10 text-warning'}`}>
                                            {r.status === 'Closed' ? t('period_closing.status_closed', 'Ditutup') : t('period_closing.status_reopened', 'Dibuka Kembali')}
                                        </span>
                                    ),
                                },
                                { accessor: 'closedName', title: t('period_closing.table.closed_by', 'Ditutup Oleh'), render: (r: any) => (<div><div>{r.closedName || '-'}</div><div className="text-xs text-gray-400">{r.closedDate ? FormatDateTime(r.closedDate) : ''}</div></div>) },
                                { accessor: 'note', title: t('period_closing.table.note', 'Catatan'), render: (r: any) => (<div className="max-w-xs truncate text-gray-600" title={r.note}>{r.note || '-'}</div>) },
                                {
                                    accessor: 'action', title: t('common.action', 'Aksi'), textAlignment: 'center',
                                    render: (row: any) => (
                                        <div className="flex items-center gap-1 justify-center">
                                            {row.status === 'Closed' && (
                                                <Tippy content={t('period_closing.reopen_action', 'Buka Kembali')}>
                                                    <Button variant="ghost" size="sm" className="h-8 w-8 p-0 text-warning" onClick={() => { setReopenRow(row); setReopenNote(''); setShowReopen(true); }}>
                                                        <LockOpen className="h-4 w-4" />
                                                    </Button>
                                                </Tippy>
                                            )}
                                            <TimelineButton module={AUDIT_MODULE.PERIOD_CLOSING} refId={`${row.periodType}|${row.year}|${row.month ?? '-'}|${row.branchId ?? 'ALL'}`} title={periodLabel(row)} />
                                        </div>
                                    ),
                                },
                            ]}
                            totalRecords={list?.totalData || 0}
                            recordsPerPage={pageSize}
                            page={page}
                            onPageChange={setPage}
                            recordsPerPageOptions={PAGE_SIZES}
                            onRecordsPerPageChange={setPageSize}
                            minHeight={200}
                            fetching={isLoading}
                            loaderVariant="dots"
                            loaderColor="#4361ee"
                            paginationText={({ from, to, totalRecords }) => t('common.pagination_text', { from, to, totalRecords })}
                        />
                    </div>
                </CardContent>
            </Card>

            {/* Tutup Buku Form */}
            <Dialog open={showClose} onOpenChange={(o) => !o && setShowClose(false)}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>{t('period_closing.close_action', 'Tutup Buku')}</DialogTitle>
                        <DialogDescription>{t('period_closing.close_hint', 'Membuat checkpoint saldo periode & mengunci periode dari perubahan jurnal. Bisa dibuka kembali.')}</DialogDescription>
                    </DialogHeader>

                    <div className="space-y-4">
                        <div className="space-y-2">
                            <Label>{t('period_closing.type', 'Tipe Periode')}</Label>
                            <div className="flex gap-4">
                                <label className="flex items-center gap-2">
                                    <input type="radio" checked={form.periodType === 'Monthly'} onChange={() => setForm((f) => ({ ...f, periodType: 'Monthly' }))} />
                                    {t('period_closing.monthly', 'Bulanan')}
                                </label>
                                <label className="flex items-center gap-2">
                                    <input type="radio" checked={form.periodType === 'Yearly'} onChange={() => setForm((f) => ({ ...f, periodType: 'Yearly' }))} />
                                    {t('period_closing.yearly', 'Tahunan')}
                                </label>
                            </div>
                        </div>

                        <div className="grid grid-cols-2 gap-4">
                            <div className="space-y-2">
                                <Label>{t('period_closing.year', 'Tahun')}</Label>
                                <Input type="number" value={form.year} onChange={(e) => setForm((f) => ({ ...f, year: Number(e.target.value) }))} />
                            </div>
                            {form.periodType === 'Monthly' && (
                                <div className="space-y-2">
                                    <Label>{t('period_closing.month', 'Bulan')}</Label>
                                    <select className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                                        value={form.month} onChange={(e) => setForm((f) => ({ ...f, month: Number(e.target.value) }))}>
                                        {MONTHS.map((m, i) => <option key={i} value={i + 1}>{m}</option>)}
                                    </select>
                                </div>
                            )}
                        </div>

                        {/* Opsi "Semua Cabang" disembunyikan: saat ini hanya 1 cabang, default pakai cabang sesi (allBranch=false). */}

                        {/* Opsi Jurnal Penutup + deskripsi fungsi & dampak */}
                        <div className="rounded-lg border border-gray-200 p-3 space-y-2 bg-gray-50/60">
                            <label className="flex items-center gap-2 font-medium">
                                <input type="checkbox" checked={form.generateClosingEntries} onChange={(e) => setForm((f) => ({ ...f, generateClosingEntries: e.target.checked }))} />
                                {t('period_closing.generate_closing', 'Buat Jurnal Penutup')}
                            </label>
                            <div className="flex gap-2 text-xs text-gray-600">
                                <Info className="h-4 w-4 shrink-0 text-primary mt-0.5" />
                                <div className="space-y-1">
                                    <p><b>{t('period_closing.if_on', 'Jika dicentang')}:</b> {t('period_closing.closing_on_desc', 'Sistem membuat jurnal penutup yang menolkan akun pendapatan & beban periode dan memindahkan laba/rugi bersih ke akun Laba Ditahan. Dampak: laporan Laba Rugi periode tsb menjadi nol setelah ditutup (laba sudah pindah ke ekuitas). Sesuai kaidah tutup buku formal. Butuh konfigurasi COA_RETAINED_EARNINGS.')}</p>
                                    <p><b>{t('period_closing.if_off', 'Jika tidak dicentang')}:</b> {t('period_closing.closing_off_desc', 'Hanya membuat checkpoint saldo & mengunci periode. Laba tahun berjalan tetap dihitung otomatis di Neraca tanpa jurnal penutup. Direkomendasikan untuk tutup buku bulanan.')}</p>
                                </div>
                            </div>
                        </div>

                        <div className="space-y-2">
                            <Label>{t('period_closing.table.note', 'Catatan')}</Label>
                            <Input type="text" value={form.note} onChange={(e) => setForm((f) => ({ ...f, note: e.target.value }))} />
                        </div>
                    </div>

                    <DialogFooter>
                        <Button variant="outline" onClick={() => setShowClose(false)}>{t('common.cancel', 'Batal')}</Button>
                        <Button onClick={submitClose} disabled={isClosing}>{isClosing ? t('common.loading', 'Memproses...') : t('period_closing.close_action', 'Tutup Buku')}</Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>

            {/* Reopen */}
            <Dialog open={showReopen} onOpenChange={(o) => !o && setShowReopen(false)}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>{t('period_closing.reopen_action', 'Buka Kembali Periode')}</DialogTitle>
                        <DialogDescription>{t('period_closing.reopen_hint', 'Periode akan dibuka kembali; jurnal penutup (bila ada) akan di-void dan checkpoint dihitung ulang.')}</DialogDescription>
                    </DialogHeader>
                    <div className="space-y-2">
                        <Label>{t('period_closing.reopen_note', 'Alasan / Catatan')}</Label>
                        <Input type="text" value={reopenNote} onChange={(e) => setReopenNote(e.target.value)} />
                    </div>
                    <DialogFooter>
                        <Button variant="outline" onClick={() => setShowReopen(false)}>{t('common.cancel', 'Batal')}</Button>
                        <Button onClick={submitReopen} disabled={isReopening}>{isReopening ? t('common.loading', 'Memproses...') : t('period_closing.reopen_action', 'Buka Kembali')}</Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </div>
    );
};

export default TutupBukuIndex;
