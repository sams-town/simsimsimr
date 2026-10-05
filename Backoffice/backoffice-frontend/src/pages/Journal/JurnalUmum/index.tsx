import ConfirmationModal from '@/components/ConfirmationModal';
import Dropdown from '@/components/Dropdown';
import IconHorizontalDots from '@/components/Icon/IconHorizontalDots';
import {
    useDeleteGeneralJournalMutation,
    useGetGeneralJournalsQuery,
    useVoidGeneralJournalMutation,
} from '@/store/api/journal/generalJournalApiSlice';
import { checkAccess } from '@/store/api/auth/authSlice';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '@/store/themeConfigSlice';
import { FormatDate } from '@/utils/formatDate';
import { FormatNumber } from '@/utils/formatNumber';
import { responseCallback } from '@/utils/responseCallback';
import { toastMessage } from '@/utils/toastUtils';
import Tippy from '@tippyjs/react';
import 'flatpickr/dist/flatpickr.css';
import { DataTable, DataTableSortStatus } from 'mantine-datatable';
import React, { useEffect, useState, useRef } from 'react';
import Flatpickr from 'react-flatpickr';
import { useTranslation } from 'react-i18next';
import { useDispatch, useSelector } from 'react-redux';
import { useNavigate } from 'react-router-dom';
import 'tippy.js/dist/tippy.css';

// UI Components
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import TimelineButton from '@/components/ActivityTimeline/TimelineButton';
import { AUDIT_MODULE } from '@/components/ActivityTimeline/modules';
import { Input } from '@/components/ui/input';
import { Search, Plus, Eye, Trash2, X, FileText, Download, Upload } from 'lucide-react';
import IconCalendar from '@/components/Icon/IconCalendar';

const JurnalUmumIndex = () => {
    const dispatch = useDispatch();
    const navigate = useNavigate();
    const { t } = useTranslation();

    useEffect(() => {
        dispatch(setPageTitle(t('transaction.general_journal_page.title')));
        dispatch(setTitle(t('transaction.general_journal_page.title')));
        dispatch(setBreadcrumbTitle([t('menu.dashboard'), t('menu.journal'), t('transaction.general_journal_page.title')]));
    }, [dispatch]);

    const isRtl = useSelector((state: any) => state.themeConfig.rtlClass) === 'rtl';

    // Hapus dibatasi Administrator/Manager lewat grant 'Delete' pada TrxTransaction;
    // Void tetap terbuka untuk role di bawahnya. Backend tetap menjadi penentu akhir —
    // gating di sini hanya menyembunyikan aksi yang pasti ditolak.
    const user = useSelector((state: any) => state.auth.user);
    const canDelete = checkAccess(user, 'TrxTransaction', 'Delete');
    const canVoid = canDelete || checkAccess(user, 'TrxTransaction', 'Void');
    const [page, setPage] = useState<number>(1);
    const PAGE_SIZES: number[] = [10, 20, 30, 50, 100];
    const [pageSize, setPageSize] = useState<number>(PAGE_SIZES[0]);
    const [search, setSearch] = useState<string>('');
    const [status, setStatus] = useState<string>('');
    const [sortStatus, setSortStatus] = useState<DataTableSortStatus>({
        columnAccessor: 'createddate',
        direction: 'desc',
    });

    const [deleteGeneralJournal, { isLoading: isDeleting }] = useDeleteGeneralJournalMutation();
    const [voidGeneralJournal, { isLoading: isVoiding }] = useVoidGeneralJournalMutation();
    const [showDeleteModal, setShowDeleteModal] = useState<boolean>(false);
    const [deleteId, setDeleteId] = useState<string>('');
    const [confirmAction, setConfirmAction] = useState<'delete' | 'void' | null>(null);

    const handleDownloadTemplate = () => {
        const baseUrl = import.meta.env.VITE_HOST || '';
        window.open(`${baseUrl}/Journal/general-journal-template`, '_blank');
    };

    const today = new Date();
    const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);

    const toUtcMidnight = (date: Date) => {
        return new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate(), 0, 0, 0));
    };

    const [startDate, setStartDate] = useState<any>(toUtcMidnight(firstDayOfMonth).toISOString());
    const [endDate, setEndDate] = useState<any>(toUtcMidnight(today).toISOString());

    const {
        data: journalsList,
        refetch,
        isLoading,
    } = useGetGeneralJournalsQuery({
        orderBy: sortStatus.columnAccessor,
        orderType: sortStatus.direction,
        page: page,
        pageSize: pageSize,
        keyword: search,
        status: status,
        startDate: startDate,
        endDate: endDate,
    });

    const handleDelete = async (id: string) => {
        try {
            const response = await deleteGeneralJournal(id);
            responseCallback(response, null, null);
            refetch();
        } catch (err) {
            console.error('Error deleting Journal:', err);
            toastMessage('Failed to delete Journal.', 'error');
        }
    };

    const handleVoid = async (id: string) => {
        try {
            const response = await voidGeneralJournal(id);
            responseCallback(response, null, null);
            refetch();
        } catch (err) {
            console.error('Error voiding Journal:', err);
            toastMessage('Failed to void Journal.', 'error');
        }
    };

    useEffect(() => {
        refetch();
    }, [page, refetch]);

    useEffect(() => {
        setPage(1);
    }, [sortStatus, search, pageSize, startDate, endDate, status]);


    return (
        <div className="space-y-6">
            <Card>
                <CardHeader className="border-b border-gray-100 pb-4">
                    {/* First Row - Title and Action Buttons */}
                    <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
                        {/* Left side - Title */}
                        <div className="flex items-center gap-4">
                            <div className="flex items-center gap-3">
                                <div className="p-2 rounded-lg bg-primary/10">
                                    <FileText className="h-5 w-5 text-primary" />
                                </div>
                                <div>
                                    <CardTitle className="text-lg">{t('transaction.general_journal_page.title')}</CardTitle>
                                    <p className="text-sm text-gray-500 mt-0.5">
                                        {t('transaction.general_journal_page.subtitle')}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {/* Right side - Action Buttons */}
                        <div className="flex items-center gap-2">
                            <Tippy content={t('transaction.general_journal_page.download_template')}>
                                <Button variant="outline" onClick={handleDownloadTemplate} className="gap-2">
                                    <Download className="h-4 w-4" />
                                    <span className="hidden sm:inline">{t('transaction.general_journal_page.download_template')}</span>
                                </Button>
                            </Tippy>
                            {/* Import moved to its own page: files are queued and processed in the
                                background, so there is nothing to wait for here. */}
                            <Tippy content={t('journal_import.title', 'Import Jurnal Umum')}>
                                <Button variant="outline" onClick={() => navigate('/jurnal-umum/import')} className="gap-2">
                                    <Upload className="h-4 w-4" />
                                    <span className="hidden sm:inline">
                                        {t('transaction.general_journal_page.import_excel')}
                                    </span>
                                </Button>
                            </Tippy>
                            <Button onClick={() => navigate(`/jurnal-umum/create`)} className="gap-2">
                                <Plus className="h-4 w-4" />
                                <span className="hidden sm:inline">{t('transaction.general_journal_page.add_new')}</span>
                            </Button>
                        </div>
                    </div>

                    {/* Second Row - Filters */}
                    <div className="flex flex-wrap items-center gap-3">
                        {/* Filter Status */}
                        <div className="w-[150px]">
                            <select
                                className="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                onChange={(e) => setStatus(e.target.value)}
                                value={status}
                            >
                                <option value={''}>{t('transaction.general_journal_page.filter_status')}</option>
                                <option value={'published'}>{t('transaction.general_journal_page.status_publish')}</option>
                                <option value={'pending'}>{t('transaction.general_journal_page.status_pending')}</option>
                                <option value={'void'}>{t('transaction.general_journal_page.status_void')}</option>
                            </select>
                        </div>

                        {/* Date Filter */}
                        <div className="relative w-[240px]">
                            <Flatpickr
                                options={{
                                    mode: 'range',
                                    dateFormat: 'd/m/Y',
                                    enableTime: false,
                                    position: isRtl ? 'auto right' : 'auto left',
                                    defaultDate: [toUtcMidnight(firstDayOfMonth), toUtcMidnight(today)],
                                }}
                                className="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 ltr:pl-9 rtl:pr-9"
                                onChange={(dates: Date[]) => {
                                    if (dates.length === 2) {
                                        setStartDate(toUtcMidnight(dates[0]).toISOString());
                                        setEndDate(toUtcMidnight(dates[1]).toISOString());
                                    }
                                }}
                            />
                            <div className="absolute inset-y-0 ltr:left-3 rtl:right-3 flex items-center pointer-events-none text-gray-400">
                                <IconCalendar className="w-4 h-4" />
                            </div>
                        </div>

                        {/* Search */}
                        <div className="relative w-[200px]">
                            <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" />
                            <Input
                                type="text"
                                placeholder={t('common.search')}
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                className="pl-9"
                            />
                        </div>
                    </div>
                </CardHeader>

                <CardContent className="pt-6">
                    <div className="datatables z-0 rounded-lg border border-gray-100">
                        <DataTable
                            highlightOnHover
                            className={`${isRtl ? 'whitespace-nowrap table-hover' : 'whitespace-nowrap table-hover'}`}
                            records={journalsList?.data?.data}
                            columns={[
                                {
                                    accessor: 'transactionNo',
                                    title: t('transaction.general_journal_page.table.transaction_no'),
                                    sortable: true,
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (row: any) => (
                                        <div className="font-semibold text-primary hover:underline cursor-pointer" onClick={() => navigate(`/jurnal-umum/detail/${row.transactionId}`)}>
                                            {row.transactionNo}
                                        </div>
                                    )
                                },
                                {
                                    accessor: 'transactionDate',
                                    title: t('transaction.general_journal_page.table.date'),
                                    sortable: true,
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (row: any) =>
                                        <span className="text-gray-600">
                                            {row.transactionDate ? FormatDate(row.transactionDate) : '-'}
                                        </span>
                                },
                                {
                                    accessor: 'description',
                                    title: t('transaction.general_journal_page.table.description'),
                                    sortable: true,
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (row) => (
                                        <div className="max-w-xs truncate font-medium text-gray-700" title={row.description}>
                                            {row.description}
                                        </div>
                                    )
                                },
                                {
                                    accessor: 'amount',
                                    title: t('transaction.general_journal_page.table.total'),
                                    sortable: true,
                                    textAlignment: 'right',
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50 text-right',
                                    render: (row: any) => (
                                        <div className="font-mono font-bold text-gray-900 text-right">
                                            {FormatNumber(row.amount)}
                                        </div>
                                    ),
                                },
                                {
                                    accessor: 'status',
                                    title: t('transaction.general_journal_page.table.status'),
                                    sortable: true,
                                    textAlignment: 'center',
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (row: any) => {
                                        let badgeClass = 'bg-gray-100 text-gray-500';
                                        if (row.status === 'Published') badgeClass = 'bg-success/10 text-success';
                                        if (row.status === 'Pending') badgeClass = 'bg-warning/10 text-warning';
                                        if (row.status === 'Void') badgeClass = 'bg-danger/10 text-danger';

                                        return (
                                            <span className={`px-2 py-1 rounded-full text-xs font-semibold ${badgeClass}`}>
                                                {row.status}
                                            </span>
                                        );
                                    },
                                },
                                {
                                    accessor: 'action',
                                    title: t('transaction.general_journal_page.table.action'),
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (row: any) => (
                                        <div className="flex items-center gap-1 justify-center">
                                            <Tippy content="View">
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    onClick={() => navigate(`/jurnal-umum/detail/${row.transactionId}`)}
                                                    className="h-8 w-8 p-0 text-gray-500 hover:text-primary hover:bg-primary/10 rounded-lg"
                                                >
                                                    <Eye className="h-4 w-4" />
                                                </Button>
                                            </Tippy>

                                            {/* Published & Void tidak bisa diubah lagi: Published sudah masuk
                                                perhitungan saldo, Void sudah dibatalkan. */}
                                            {row.status !== 'Published' && row.status !== 'Void' && (canVoid || canDelete) && (
                                                <div className="dropdown">
                                                    <Dropdown
                                                        offset={[0, 5]}
                                                        placement="auto-end"
                                                        strategy="fixed"
                                                        button={<IconHorizontalDots className="opacity-70 cursor-pointer hover:opacity-100 text-gray-500" />}
                                                    >
                                                        <ul className="text-sm font-medium">
                                                            {canVoid && (
                                                                <li>
                                                                    <button
                                                                        type="button"
                                                                        className="text-orange-500 flex items-center gap-2 w-full px-4 py-2 hover:bg-gray-50 dark:hover:bg-gray-800"
                                                                        onClick={(e) => {
                                                                            e.stopPropagation();
                                                                            setDeleteId(row.transactionId);
                                                                            setConfirmAction('void');
                                                                            setShowDeleteModal(true);
                                                                        }}
                                                                    >
                                                                        <X className="w-4 h-4" />
                                                                        {t('transaction.general_journal_page.status_void')}
                                                                    </button>
                                                                </li>
                                                            )}
                                                            {canDelete && (
                                                                <li>
                                                                    <button
                                                                        type="button"
                                                                        className="text-danger flex items-center gap-2 w-full px-4 py-2 hover:bg-gray-50 dark:hover:bg-gray-800"
                                                                        onClick={(e) => {
                                                                            e.stopPropagation();
                                                                            setDeleteId(row.transactionId);
                                                                            setConfirmAction('delete');
                                                                            setShowDeleteModal(true);
                                                                        }}
                                                                    >
                                                                        <Trash2 className="w-4 h-4" />
                                                                        {t('common.delete')}
                                                                    </button>
                                                                </li>
                                                            )}
                                                        </ul>
                                                    </Dropdown>
                                                </div>
                                            )}

                                            <TimelineButton
                                                module={AUDIT_MODULE.TRANSACTION}
                                                refId={row.transactionId}
                                                title={row.transactionNo || row.journalNo}
                                            />
                                        </div>
                                    ),
                                },
                            ]}
                            totalRecords={journalsList?.data?.totalData}
                            recordsPerPage={pageSize}
                            page={page}
                            onPageChange={(p) => setPage(p)}
                            recordsPerPageOptions={PAGE_SIZES}
                            onRecordsPerPageChange={setPageSize}
                            sortStatus={sortStatus}
                            onSortStatusChange={setSortStatus}
                            minHeight={200}
                            paginationText={({ from, to, totalRecords }) =>
                                t('common.pagination_text', { from, to, totalRecords })
                            }
                            fetching={isLoading}
                            loaderVariant="dots"
                            loaderColor="#4361ee"
                            styles={{
                                root: {
                                    border: 'none',
                                    borderRadius: '0.75rem',
                                    overflow: 'hidden',
                                },
                                pagination: {
                                    borderTop: '1px solid #f1f5f9',
                                    margin: '1rem',
                                },
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
            </Card>

            <ConfirmationModal
                isOpen={showDeleteModal}
                onClose={() => !isDeleting && !isVoiding && setShowDeleteModal(false)}
                onConfirm={async () => {
                    if (!deleteId) return;
                    if (confirmAction === 'delete') {
                        await handleDelete(deleteId);
                    }
                    if (confirmAction === 'void') {
                        await handleVoid(deleteId);
                    }
                    setShowDeleteModal(false);
                }}
                title={confirmAction === 'delete' ? t('transaction.general_journal_page.delete_title') : t('transaction.general_journal_page.void_title')}
                message={confirmAction === 'delete' ? t('transaction.general_journal_page.delete_confirm') : t('transaction.general_journal_page.void_confirm')}
                confirmText={confirmAction === 'delete' ? t('common.delete') : t('transaction.general_journal_page.status_void')}
                variant={confirmAction === 'delete' ? 'danger' : 'primary'}
                cancelText={t('common.cancel')}
            />

        </div>
    );
};

export default JurnalUmumIndex;
