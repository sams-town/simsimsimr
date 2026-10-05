import { useDispatch, useSelector } from 'react-redux';
import { useNavigate } from 'react-router-dom';
import { DataTable, DataTableSortStatus } from 'mantine-datatable';
import { useEffect, useState } from 'react';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '../../../store/themeConfigSlice';
import { useTranslation } from 'react-i18next';
import Tippy from '@tippyjs/react';
import 'tippy.js/dist/tippy.css';
import { journalType } from '@/types';
import { useGetJournalQuery } from '@/store/api/journal/journalApiSlice';
import { useGetOptionBankQuery } from '@/store/api/bank/bankApiSlice';
import SelectSearch from 'react-select';
import Flatpickr from 'react-flatpickr';
import 'flatpickr/dist/flatpickr.css';
import { FormatNumber } from '@/utils/formatNumber';
import { FormatDate } from '@/utils/formatDate';

// ShadCN & UI
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Search, Loader2, FileText, Download } from 'lucide-react';
import IconCalendar from '@/components/Icon/IconCalendar';
import { useExportJournalHistoryMutation } from '@/store/api/journal/journalApiSlice';

const Index = () => {
    const dispatch = useDispatch();
    const navigate = useNavigate();
    const { t } = useTranslation();

    const [exportHistory, { isLoading: isExporting }] = useExportJournalHistoryMutation();

    const handleExport = async () => {
        try {
            const data = await exportHistory({
                keyword: search,
                startDate: startDate,
                endDate: endDate,
                coaCode: selectedCoa?.desc || '',
                orderBy: sortStatus.columnAccessor === 'createddate' ? 'createddate' : sortStatus.columnAccessor,
                orderType: sortStatus.direction,
            }).unwrap();

            const url = window.URL.createObjectURL(data);
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', `JournalHistory_${new Date().toISOString().slice(0, 19).replace(/[-:T]/g, '')}.xlsx`);
            document.body.appendChild(link);
            link.click();
            link.parentNode?.removeChild(link);
        } catch (error) {
            console.error('Export failed', error);
        }
    };

    useEffect(() => {
        dispatch(setPageTitle(t('general_ledger.history.title')));
        dispatch(setTitle(t('general_ledger.history.title')));
        dispatch(setBreadcrumbTitle(['Dashboard', 'General Ledger', 'History']));
    }, [dispatch]);

    const isRtl = useSelector((state: any) => state.themeConfig.rtlClass) === 'rtl';
    const [page, setPage] = useState<number>(1);
    const PAGE_SIZES: number[] = [10, 20, 30, 50, 100];
    const [pageSize, setPageSize] = useState<number>(PAGE_SIZES[0]);

    // Filters
    const [search, setSearch] = useState<string>('');
    const [searchCoa, setSearchCoa] = useState<string>('');
    const [selectedCoa, setSelectedCoa] = useState<any>(null);

    const today = new Date();
    const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
    const toUtcMidnight = (date: Date) => {
        return new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate(), 0, 0, 0));
    };

    const [startDate, setStartDate] = useState<any>(toUtcMidnight(firstDayOfMonth).toISOString());
    const [endDate, setEndDate] = useState<any>(toUtcMidnight(today).toISOString());

    const [sortStatus, setSortStatus] = useState<DataTableSortStatus>({ columnAccessor: 'createdDate', direction: 'desc' });

    // COA Options
    const { data: defaultCoaRes, isLoading: isDefaultLoading } = useGetOptionBankQuery(
        { page: -1, pageSize: -1 },
        { skip: !!searchCoa }
    );
    const { data: searchCoaRes, isLoading: isSearchLoading } = useGetOptionBankQuery(
        { search: searchCoa, page: -1, pageSize: -1 },
        { skip: !searchCoa }
    );
    const coaList = searchCoa ? searchCoaRes?.data ?? [] : defaultCoaRes?.data ?? [];
    const isLoadingCoa = isDefaultLoading || isSearchLoading;


    // API Query
    const {
        data: historyList,
        refetch,
        isLoading,
    } = useGetJournalQuery<any>({
        keyword: search,
        orderBy: sortStatus.columnAccessor === 'createddate' ? 'createddate' : sortStatus.columnAccessor,
        orderType: sortStatus.direction,
        pageSize: pageSize,
        page: page,
        startDate: startDate,
        endDate: endDate,
        coaCode: selectedCoa?.desc || '' // Pass selected COA Code
    });

    useEffect(() => {
        refetch();
    }, [page, pageSize, sortStatus, search, startDate, endDate, selectedCoa]);

    // Calculate Totals for Visible Data
    const totalDebit = historyList?.data?.data?.reduce((acc: number, curr: journalType) => acc + (curr.debitAmount || 0), 0) || 0;
    const totalCredit = historyList?.data?.data?.reduce((acc: number, curr: journalType) => acc + (curr.creditAmount || 0), 0) || 0;

    // Helper function to get detail URL based on source
    const getDetailUrl = (row: journalType): string | null => {
        if (!row.transactionId) return null;

        switch (row.source) {
            case 'Deposit':
                return `/daftar-penerimaan/detail/${row.transactionId}`;
            case 'Payment':
                return `/daftar-pengeluaran/detail/${row.transactionId}`;
            case 'Transfer':
                return `/daftar-transfer/detail/${row.transactionId}`;
            case 'JournalEntry':
            case 'KhanzaJournal':
            default:
                return `/jurnal-umum/detail/${row.transactionId}`;
        }
    };

    return (
        <div className="space-y-6">
            <Card>
                <CardHeader className="border-b border-gray-100 pb-4">
                    <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        {/* Left side - Title */}
                        <div className="flex items-center gap-4">
                            <div className="flex items-center gap-3">
                                <div className="p-2 rounded-lg bg-primary/10">
                                    <FileText className="h-5 w-5 text-primary" />
                                </div>
                                <div>
                                    <CardTitle className="text-lg">{t('general_ledger.history.title')}</CardTitle>
                                    <p className="text-sm text-gray-500 mt-0.5">
                                        {t('general_ledger.history.subtitle')}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {/* Right side - Actions */}
                        <div className="flex flex-col md:flex-row items-end md:items-center gap-3 ml-auto w-full md:w-auto">
                            {/* Account Filter */}
                            <div className="w-[300px]">
                                <SelectSearch
                                    placeholder={t('general_ledger.history.select_account')}
                                    options={coaList}
                                    className="z-10"
                                    isClearable
                                    isLoading={isLoadingCoa}
                                    onInputChange={(e) => setSearchCoa(e)}
                                    onChange={(option: any) => setSelectedCoa(option)}
                                    value={selectedCoa}
                                />
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
                                    placeholder={t('general_ledger.history.select_date_range')}
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
                            <div className="relative flex-1 md:w-[200px]">
                                <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" />
                                <Input
                                    type="text"
                                    placeholder={t('common.search')}
                                    value={search}
                                    onChange={(e) => setSearch(e.target.value)}
                                    className="pl-9"
                                />
                            </div>

                            <Button
                                variant="outline"
                                className="gap-2 border-primary text-primary hover:bg-primary hover:text-white"
                                onClick={handleExport}
                                disabled={isExporting}
                            >
                                {isExporting ? <Loader2 className="h-4 w-4 animate-spin" /> : <Download className="h-4 w-4" />}
                                <span className="hidden sm:inline">{t('common.download')}</span>
                            </Button>
                        </div>
                    </div>
                </CardHeader>

                <CardContent className="pt-6">
                    <div className="datatables z-0 rounded-lg border border-gray-100 overflow-hidden">
                        {/* Summary Header */}
                        <div className="bg-gray-50 dark:bg-gray-800 p-4 border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row justify-end items-center gap-4 sm:gap-8 text-sm">
                            <div className="flex items-center gap-2">
                                <span className="font-bold text-gray-500 uppercase">{t('general_ledger.history.total_debit')}</span>
                                <span className="font-mono font-bold text-gray-800 dark:text-gray-200 text-base">{FormatNumber(totalDebit)}</span>
                            </div>
                            <div className="flex items-center gap-2">
                                <span className="font-bold text-gray-500 uppercase">{t('general_ledger.history.total_credit')}</span>
                                <span className="font-mono font-bold text-gray-800 dark:text-gray-200 text-base">{FormatNumber(totalCredit)}</span>
                            </div>
                        </div>
                        <DataTable
                            highlightOnHover
                            className="whitespace-nowrap table-hover"
                            records={historyList?.data?.data}
                            totalRecords={historyList?.data?.totalData}
                            recordsPerPage={pageSize}
                            page={page}
                            onPageChange={(p) => setPage(p)}
                            recordsPerPageOptions={PAGE_SIZES}
                            onRecordsPerPageChange={setPageSize}
                            sortStatus={sortStatus}
                            onSortStatusChange={setSortStatus}
                            minHeight={200}
                            fetching={isLoading}
                            loaderVariant="dots"
                            loaderColor="#4361ee"
                            columns={[
                                {
                                    accessor: 'createdDate',
                                    title: t('general_ledger.history.table.date'),
                                    sortable: true,
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (row: journalType) => (row.createdDate ? FormatDate(row.createdDate) : '-')
                                },
                                {
                                    accessor: 'transactionDetail.journalNo',
                                    title: t('general_ledger.history.table.journal_no'),
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (row: journalType) => <span className="font-medium text-gray-600">{row.transactionDetail?.journalNo || '-'}</span>
                                },
                                {
                                    accessor: 'transactionDetail.transactionNo',
                                    title: t('general_ledger.history.table.trans_no'),
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (row: journalType) => {
                                        const detailUrl = getDetailUrl(row);
                                        return (
                                            <span
                                                className={`font-medium ${detailUrl ? 'text-primary cursor-pointer hover:underline' : 'text-gray-600'}`}
                                                onClick={() => detailUrl && navigate(detailUrl)}
                                            >
                                                {row.transactionDetail?.transactionNo || '-'}
                                            </span>
                                        );
                                    }
                                },
                                {
                                    accessor: 'coaCode',
                                    title: t('general_ledger.history.table.account'),
                                    sortable: true,
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (row: journalType) => (
                                        <div className="flex flex-col">
                                            <span className="font-bold text-gray-700">{row.coaCode}</span>
                                            <span className="text-xs text-gray-500">{row.coaName}</span>
                                        </div>
                                    )
                                },
                                {
                                    accessor: 'journalDesc',
                                    title: t('general_ledger.history.table.description'),
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    width: 300,
                                    render: (row: journalType) => <div className="truncate max-w-xs" title={row.journalDesc}>{row.journalDesc}</div>,
                                },
                                {
                                    accessor: 'debitAmount',
                                    title: t('general_ledger.history.table.debit'),
                                    sortable: true,
                                    textAlignment: 'right',
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50 text-right',
                                    render: (row: journalType) => <span className="font-mono">{row.debitAmount ? FormatNumber(row.debitAmount) : '-'}</span>,
                                },
                                {
                                    accessor: 'creditAmount',
                                    title: t('general_ledger.history.table.credit'),
                                    sortable: true,
                                    textAlignment: 'right',
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50 text-right',
                                    render: (row: journalType) => <span className="font-mono">{row.creditAmount ? FormatNumber(row.creditAmount) : '-'}</span>,
                                },
                            ] as any}
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
                            paginationText={({ from, to, totalRecords }) => t('common.pagination_text', { from, to, totalRecords })}
                        />

                    </div>
                </CardContent>
            </Card>
        </div>
    );
};

export default Index;
