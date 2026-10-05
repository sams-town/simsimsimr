import {
    useGetUnbalancedJournalsQuery,
    useExportUnbalancedJournalMutation,
} from '@/store/api/journal/generalJournalApiSlice';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '@/store/themeConfigSlice';
import { FormatDate } from '@/utils/formatDate';
import { FormatNumber } from '@/utils/formatNumber';
import Tippy from '@tippyjs/react';
import 'flatpickr/dist/flatpickr.css';
import { DataTable, DataTableSortStatus } from 'mantine-datatable';
import React, { useEffect, useState } from 'react';
import Flatpickr from 'react-flatpickr';
import { useTranslation } from 'react-i18next';
import { useDispatch, useSelector } from 'react-redux';
import { useNavigate } from 'react-router-dom';
import 'tippy.js/dist/tippy.css';

// UI Components
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Download, AlertCircle, Eye } from 'lucide-react';
import IconCalendar from '@/components/Icon/IconCalendar';

const UnbalancedJournalIndex = () => {
    const dispatch = useDispatch();
    const navigate = useNavigate();
    const { t } = useTranslation();

    const isRtl = useSelector((state: any) => state.themeConfig.rtlClass) === 'rtl';
    const [page, setPage] = useState<number>(1);
    const PAGE_SIZES: number[] = [10, 20, 30, 50, 100];
    const [pageSize, setPageSize] = useState<number>(PAGE_SIZES[0]);

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
    } = useGetUnbalancedJournalsQuery({
        page: page,
        pageSize: pageSize,
        startDate: startDate,
        endDate: endDate,
    });

    const [exportUnbalancedJournal, { isLoading: isExporting }] = useExportUnbalancedJournalMutation();

    const handleDownload = async () => {
        try {
            const response: any = await exportUnbalancedJournal({
                startDate: startDate,
                endDate: endDate,
            });
            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', `UnbalancedJournals_${new Date().getTime()}.xlsx`);
            document.body.appendChild(link);
            link.click();
            link.parentNode?.removeChild(link);
        } catch (error) {
            console.error('Failed to export:', error);
        }
    };

    useEffect(() => {
        dispatch(setPageTitle('Jurnal Tidak Balance'));
        dispatch(setTitle('Jurnal Tidak Balance'));
        dispatch(setBreadcrumbTitle(['Dashboard', 'Jurnal', 'Jurnal Tidak Balance']));
    }, [dispatch]);

    return (
        <div className="space-y-6">
            <Card>
                <CardHeader className="border-b border-gray-100 pb-4">
                    <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
                        <div className="flex items-center gap-4">
                            <div className="flex items-center gap-3">
                                <div className="p-2 rounded-lg bg-red-100">
                                    <AlertCircle className="h-5 w-5 text-red-600" />
                                </div>
                                <div>
                                    <CardTitle className="text-lg text-red-600">Jurnal Tidak Balance</CardTitle>
                                    <p className="text-sm text-gray-500 mt-0.5">
                                        Daftar transaksi dengan jurnal tidak seimbang (Debit ≠ Kredit)
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div className="flex items-center gap-2">
                            <Button variant="outline" onClick={handleDownload} disabled={isExporting} className="gap-2">
                                <Download className="h-4 w-4" />
                                <span className="hidden sm:inline">
                                    {isExporting ? 'Downloading...' : 'Download Excel'}
                                </span>
                            </Button>
                        </div>
                    </div>

                    <div className="flex flex-wrap items-center gap-3">
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
                                    title: 'No. Transaksi',
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (row: any) => (
                                        <div className="font-semibold text-primary hover:underline cursor-pointer" onClick={() => navigate(`/jurnal-umum/detail/${row.transactionId}`)}>
                                            {row.transactionNo}
                                        </div>
                                    )
                                },
                                {
                                    accessor: 'transactionDate',
                                    title: 'Tanggal',
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (row: any) =>
                                        <span className="text-gray-600">
                                            {row.transactionDate ? FormatDate(row.transactionDate) : '-'}
                                        </span>
                                },
                                {
                                    accessor: 'description',
                                    title: 'Deskripsi',
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (row) => (
                                        <div className="max-w-xs truncate font-medium text-gray-700" title={row.description}>
                                            {row.description}
                                        </div>
                                    )
                                },
                                {
                                    accessor: 'totalDebit',
                                    title: 'Total Debit',
                                    textAlignment: 'right',
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50 text-right',
                                    render: (row: any) => (
                                        <div className="font-mono text-gray-900 text-right">
                                            {FormatNumber(row.totalDebit)}
                                        </div>
                                    ),
                                },
                                {
                                    accessor: 'totalCredit',
                                    title: 'Total Credit',
                                    textAlignment: 'right',
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50 text-right',
                                    render: (row: any) => (
                                        <div className="font-mono text-gray-900 text-right">
                                            {FormatNumber(row.totalCredit)}
                                        </div>
                                    ),
                                },
                                {
                                    accessor: 'difference',
                                    title: 'Selisih',
                                    textAlignment: 'right',
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50 text-right',
                                    render: (row: any) => (
                                        <div className="font-mono font-bold text-red-600 text-right">
                                            {FormatNumber(row.difference)}
                                        </div>
                                    ),
                                },
                                {
                                    accessor: 'action',
                                    title: 'Action',
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
                                        </div>
                                    ),
                                },
                            ]}
                            totalRecords={journalsList?.data?.totalData || 0}
                            recordsPerPage={pageSize}
                            page={page}
                            onPageChange={(p) => setPage(p)}
                            recordsPerPageOptions={PAGE_SIZES}
                            onRecordsPerPageChange={setPageSize}
                            minHeight={200}
                            paginationText={({ from, to, totalRecords }) =>
                                `Showing ${from} to ${to} of ${totalRecords} entries`
                            }
                            fetching={isLoading}
                            loaderVariant="dots"
                            loaderColor="#eb4034"
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
                                    backgroundColor: '#eb4034', // Red header for alert
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
        </div>
    );
};

export default UnbalancedJournalIndex;
