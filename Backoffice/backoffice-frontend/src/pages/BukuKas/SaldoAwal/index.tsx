import { useEffect, useMemo, useState } from 'react';
import { useTranslation } from 'react-i18next';
import { useDispatch, useSelector } from 'react-redux';
import { DataTable, DataTableSortStatus } from 'mantine-datatable';
import Flatpickr from 'react-flatpickr';
import 'flatpickr/dist/flatpickr.css';
import SelectSearch from 'react-select';
import Tippy from '@tippyjs/react';
import 'tippy.js/dist/tippy.css';
import { Plus, Eye, Trash2, Pencil, Wallet } from 'lucide-react';

import { setBreadcrumbTitle, setPageTitle, setTitle } from '@/store/themeConfigSlice';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Dialog, DialogContent, DialogHeader, DialogFooter, DialogTitle, DialogDescription,
} from '@/components/ui/dialog';
import ConfirmationModal from '@/components/ConfirmationModal';
import TimelineButton from '@/components/ActivityTimeline/TimelineButton';
import { AUDIT_MODULE } from '@/components/ActivityTimeline/modules';
import IconCalendar from '@/components/Icon/IconCalendar';
import { reactSelectCustomStyles } from '@/utils/reactSelectCustomStyle';
import { FormatDate } from '@/utils/formatDate';
import { FormatNumber } from '@/utils/formatNumber';
import { responseCallback } from '@/utils/responseCallback';
import { toastMessage } from '@/utils/toastUtils';
import {
    useGetOpeningBalancesQuery,
    useCreateOpeningBalanceMutation,
    useUpdateOpeningBalanceMutation,
    useDeleteOpeningBalanceMutation,
} from '@/store/api/openingBalance/openingBalanceApiSlice';
import { useGetOptionCOAQuery } from '@/store/api/coa/coaApiSlice';

const toUtcMidnight = (date: Date) => new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate(), 0, 0, 0));
const yyyymmdd = (d: string | Date) => {
    const dt = new Date(d);
    return `${dt.getUTCFullYear()}${String(dt.getUTCMonth() + 1).padStart(2, '0')}${String(dt.getUTCDate()).padStart(2, '0')}`;
};

const emptyForm = { id: null as number | null, coaCode: '', asOfDate: toUtcMidnight(new Date()).toISOString(), amount: '' as string, note: '' };

const SaldoAwalIndex = () => {
    const dispatch = useDispatch();
    const { t } = useTranslation();
    const isRtl = useSelector((state: any) => state.themeConfig.rtlClass) === 'rtl';

    const [page, setPage] = useState(1);
    const PAGE_SIZES = [10, 20, 30, 50, 100];
    const [pageSize, setPageSize] = useState(PAGE_SIZES[0]);
    const [coaFilter, setCoaFilter] = useState<string>('');
    const [coaSearch, setCoaSearch] = useState<string>('');
    const [startDate, setStartDate] = useState<any>('');
    const [endDate, setEndDate] = useState<any>('');
    const [sortStatus, setSortStatus] = useState<DataTableSortStatus>({ columnAccessor: 'periodDate', direction: 'desc' });

    const [showForm, setShowForm] = useState(false);
    const [form, setForm] = useState({ ...emptyForm });
    const [isView, setIsView] = useState(false);
    const [showDelete, setShowDelete] = useState(false);
    const [deleteId, setDeleteId] = useState<number | null>(null);

    const { data: list, refetch, isLoading } = useGetOpeningBalancesQuery<any>({
        coaCode: coaFilter, startDate, endDate, page, pageSize,
    });
    const { data: coaOptions } = useGetOptionCOAQuery<any>({ orderBy: 'coaCode', orderType: 'asc', pageSize: 30, status: 'Active', keyword: coaSearch });

    const [createOB, { isLoading: isCreating }] = useCreateOpeningBalanceMutation();
    const [updateOB, { isLoading: isUpdating }] = useUpdateOpeningBalanceMutation();
    const [deleteOB, { isLoading: isDeleting }] = useDeleteOpeningBalanceMutation();

    useEffect(() => {
        dispatch(setPageTitle(t('opening_balance.title', 'Saldo Awal')));
        dispatch(setTitle(t('opening_balance.title', 'Saldo Awal')));
        dispatch(setBreadcrumbTitle([t('menu.dashboard', 'Dashboard'), t('menu.buku_kas', 'Buku Kas'), t('opening_balance.title', 'Saldo Awal')]));
    }, [dispatch]);

    useEffect(() => { setPage(1); }, [coaFilter, startDate, endDate, pageSize]);

    const coaSelectOptions = useMemo(() => {
        const opts: any[] = [];
        (coaOptions?.data || []).forEach((o: any) => opts.push({ value: o.value, label: o.label }));
        return opts;
    }, [coaOptions]);

    const openCreate = () => { setForm({ ...emptyForm }); setIsView(false); setShowForm(true); };
    const openEdit = (row: any, view = false) => {
        setForm({ id: row.balanceId, coaCode: row.coaCode, asOfDate: new Date(row.periodDate).toISOString(), amount: row.openingBalance != null ? String(row.openingBalance) : '', note: row.note || '' });
        setIsView(view);
        setShowForm(true);
    };

    const submit = async () => {
        if (!form.coaCode) { toastMessage(t('opening_balance.coa_required', 'Akun (COA) wajib dipilih'), 'error'); return; }
        try {
            const body = { id: form.id, coaCode: form.coaCode, asOfDate: form.asOfDate, amount: Number(form.amount) || 0, note: form.note };
            const response = form.id ? await updateOB(body) : await createOB(body);
            responseCallback(response, () => { setShowForm(false); refetch(); }, null);
        } catch (err: any) {
            toastMessage(err?.message || 'Error', 'error');
        }
    };

    const confirmDelete = async () => {
        if (!deleteId) return;
        try {
            const response = await deleteOB(deleteId);
            responseCallback(response, () => { setShowDelete(false); refetch(); }, null);
        } catch (err: any) {
            toastMessage(err?.message || 'Error', 'error');
        }
    };

    return (
        <div className="space-y-6">
            <Card>
                <CardHeader className="border-b border-gray-100 pb-4">
                    <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
                        <div className="flex items-center gap-3">
                            <div className="p-2 rounded-lg bg-primary/10"><Wallet className="h-5 w-5 text-primary" /></div>
                            <div>
                                <CardTitle className="text-lg">{t('opening_balance.title', 'Saldo Awal')}</CardTitle>
                                <p className="text-sm text-gray-500 mt-0.5">{t('opening_balance.subtitle', 'Input saldo awal (opening balance) per akun sebagai titik awal perhitungan saldo.')}</p>
                            </div>
                        </div>
                        <Button onClick={openCreate} className="gap-2">
                            <Plus className="h-4 w-4" />
                            <span className="hidden sm:inline">{t('opening_balance.add_new', 'Tambah Saldo Awal')}</span>
                        </Button>
                    </div>

                    <div className="flex flex-wrap items-center gap-3">
                        <div className="w-[280px]">
                            <SelectSearch
                                placeholder={t('opening_balance.filter_coa', 'Filter Akun (COA)')}
                                options={[{ value: '', label: t('common.all', 'Semua') }, ...coaSelectOptions]}
                                onInputChange={(v) => setCoaSearch(v || '')}
                                onChange={(opt: any) => setCoaFilter(opt?.value || '')}
                                isClearable
                                styles={reactSelectCustomStyles}
                            />
                        </div>
                        <div className="relative w-[240px]">
                            <Flatpickr
                                options={{ mode: 'range', dateFormat: 'd/m/Y', position: isRtl ? 'auto right' : 'auto left' }}
                                className="flex h-10 w-full items-center rounded-md border border-input bg-background px-3 py-2 text-sm ltr:pl-9 rtl:pr-9"
                                placeholder={t('opening_balance.filter_date', 'Filter Tanggal Cut-off')}
                                onChange={(dates: Date[]) => {
                                    if (dates.length === 2) {
                                        setStartDate(toUtcMidnight(dates[0]).toISOString());
                                        setEndDate(toUtcMidnight(dates[1]).toISOString());
                                    } else if (dates.length === 0) {
                                        setStartDate(''); setEndDate('');
                                    }
                                }}
                            />
                            <div className="absolute inset-y-0 ltr:left-3 rtl:right-3 flex items-center pointer-events-none text-gray-400">
                                <IconCalendar className="w-4 h-4" />
                            </div>
                        </div>
                    </div>
                </CardHeader>

                <CardContent className="pt-6">
                    <div className="datatables z-0 rounded-lg border border-gray-100">
                        <DataTable
                            highlightOnHover
                            className="whitespace-nowrap table-hover"
                            records={list?.data || []}
                            columns={[
                                { accessor: 'coaCode', title: t('opening_balance.table.coa', 'Akun'), render: (r: any) => (<div><div className="font-semibold text-primary">{r.coaCode}</div><div className="text-xs text-gray-500">{r.coaName}</div></div>) },
                                { accessor: 'normalPosition', title: t('opening_balance.table.normal', 'Posisi'), render: (r: any) => (['C', 'CREDIT', 'K', 'KREDIT'].includes((r.normalPosition || '').toUpperCase()) ? 'Kredit' : 'Debit') },
                                { accessor: 'periodDate', title: t('opening_balance.table.as_of', 'Tgl Cut-off'), sortable: true, render: (r: any) => FormatDate(r.periodDate) },
                                { accessor: 'openingBalance', title: t('opening_balance.table.amount', 'Nominal'), textAlignment: 'right', render: (r: any) => <span className="font-mono font-bold">{FormatNumber(r.openingBalance)}</span> },
                                { accessor: 'note', title: t('opening_balance.table.note', 'Catatan'), render: (r: any) => (<div className="max-w-xs truncate text-gray-600" title={r.note}>{r.note || '-'}</div>) },
                                { accessor: 'createdName', title: t('opening_balance.table.created_by', 'Dibuat Oleh'), render: (r: any) => (<div><div>{r.createdName || '-'}</div><div className="text-xs text-gray-400">{r.createdDate ? FormatDate(r.createdDate) : ''}</div></div>) },
                                {
                                    accessor: 'action', title: t('common.action', 'Aksi'), textAlignment: 'center',
                                    render: (row: any) => (
                                        <div className="flex items-center gap-1 justify-center">
                                            <Tippy content={t('common.view', 'Lihat')}><Button variant="ghost" size="sm" className="h-8 w-8 p-0" onClick={() => openEdit(row, true)}><Eye className="h-4 w-4" /></Button></Tippy>
                                            <Tippy content={t('common.edit', 'Ubah')}><Button variant="ghost" size="sm" className="h-8 w-8 p-0" onClick={() => openEdit(row, false)}><Pencil className="h-4 w-4" /></Button></Tippy>
                                            <Tippy content={t('common.delete', 'Hapus')}><Button variant="ghost" size="sm" className="h-8 w-8 p-0 text-danger" onClick={() => { setDeleteId(row.balanceId); setShowDelete(true); }}><Trash2 className="h-4 w-4" /></Button></Tippy>
                                            <TimelineButton module={AUDIT_MODULE.OPENING_BALANCE} refId={`${row.coaCode}|${row.branchId}|${yyyymmdd(row.periodDate)}`} title={`${row.coaCode} - ${row.coaName || ''}`} />
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
                            sortStatus={sortStatus}
                            onSortStatusChange={setSortStatus}
                            minHeight={200}
                            fetching={isLoading}
                            loaderVariant="dots"
                            loaderColor="#4361ee"
                            paginationText={({ from, to, totalRecords }) => t('common.pagination_text', { from, to, totalRecords })}
                        />
                    </div>
                </CardContent>
            </Card>

            {/* Form Modal */}
            <Dialog open={showForm} onOpenChange={(o) => !o && setShowForm(false)}>
                <DialogContent
                    // Cegah dialog menutup saat berinteraksi dgn kalender Flatpickr / menu react-select
                    // yang dirender di portal luar DialogContent.
                    onInteractOutside={(e) => {
                        const target = (((e as any).detail?.originalEvent?.target) ?? (e.target as any)) as HTMLElement | null;
                        if (target?.closest?.('.flatpickr-calendar')) e.preventDefault();
                    }}
                >
                    <DialogHeader>
                        <DialogTitle>{isView ? t('opening_balance.view_title', 'Detail Saldo Awal') : form.id ? t('opening_balance.edit_title', 'Ubah Saldo Awal') : t('opening_balance.create_title', 'Tambah Saldo Awal')}</DialogTitle>
                        <DialogDescription>{t('opening_balance.form_hint', 'Saldo awal ditulis sebagai titik awal (checkpoint) perhitungan saldo akun. Tanda nilai mengikuti posisi normal akun.')}</DialogDescription>
                    </DialogHeader>

                    <div className="space-y-4">
                        <div className="space-y-2">
                            <Label>{t('opening_balance.table.coa', 'Akun')}</Label>
                            <SelectSearch
                                placeholder={t('opening_balance.select_coa', 'Pilih Akun (COA)')}
                                options={coaSelectOptions}
                                value={coaSelectOptions.find((o) => o.value === form.coaCode) || (form.coaCode ? { value: form.coaCode, label: form.coaCode } : null)}
                                onInputChange={(v) => setCoaSearch(v || '')}
                                onChange={(opt: any) => setForm((f) => ({ ...f, coaCode: opt?.value || '' }))}
                                isDisabled={isView || !!form.id}
                                styles={reactSelectCustomStyles}
                            />
                        </div>
                        <div className="space-y-2">
                            <Label>{t('opening_balance.table.as_of', 'Tgl Cut-off')}</Label>
                            <Flatpickr
                                // static: render kalender INLINE di dalam DOM dialog (bukan portal ke body),
                                // agar tidak direbut focus-trap Radix Dialog & tidak menutup saat pilih bulan/tahun.
                                options={{ dateFormat: 'd/m/Y', static: true }}
                                value={form.asOfDate ? new Date(form.asOfDate) : undefined}
                                className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm disabled:opacity-50"
                                disabled={isView}
                                onChange={(dates: Date[]) => dates[0] && setForm((f) => ({ ...f, asOfDate: toUtcMidnight(dates[0]).toISOString() }))}
                            />
                        </div>
                        <div className="space-y-2">
                            <Label>{t('opening_balance.table.amount', 'Nominal')}</Label>
                            <Input type="number" step="any" inputMode="decimal" placeholder="0" value={form.amount} disabled={isView}
                                onChange={(e) => setForm((f) => ({ ...f, amount: e.target.value }))} />
                        </div>
                        <div className="space-y-2">
                            <Label>{t('opening_balance.table.note', 'Catatan')}</Label>
                            <Input type="text" value={form.note} disabled={isView}
                                onChange={(e) => setForm((f) => ({ ...f, note: e.target.value }))} />
                        </div>
                    </div>

                    <DialogFooter>
                        <Button variant="outline" onClick={() => setShowForm(false)}>{t('common.close', 'Tutup')}</Button>
                        {!isView && (
                            <Button onClick={submit} disabled={isCreating || isUpdating}>
                                {isCreating || isUpdating ? t('common.loading', 'Menyimpan...') : t('common.save', 'Simpan')}
                            </Button>
                        )}
                    </DialogFooter>
                </DialogContent>
            </Dialog>

            <ConfirmationModal
                isOpen={showDelete}
                onClose={() => !isDeleting && setShowDelete(false)}
                onConfirm={confirmDelete}
                title={t('opening_balance.delete_title', 'Hapus Saldo Awal')}
                message={t('opening_balance.delete_confirm', 'Yakin ingin menghapus saldo awal ini? Checkpoint saldo terkait akan dihitung ulang.')}
                confirmText={t('common.delete', 'Hapus')}
                variant="danger"
                cancelText={t('common.cancel', 'Batal')}
                loading={isDeleting}
            />
        </div>
    );
};

export default SaldoAwalIndex;
