import React, { useEffect, useState } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { useNavigate, useParams } from 'react-router-dom';
import { DataTable, DataTableSortStatus } from 'mantine-datatable';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '../../../store/themeConfigSlice';
import { useGetDetailCoaWithCoaQuery, useExportDetailCoaMutation } from '@/store/api/coa/coaApiSlice';
import { FormatDate } from '@/utils/formatDate';
import { FormatNumber } from '@/utils/formatNumber';
import DateRangePicker from '@/components/DateRangePicker';
import { useTranslation } from 'react-i18next';
import { Journal } from '@/types';
import './index.css';

// UI Components
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { ArrowLeft, Wallet, CalendarDays, History, Download, Loader2 } from 'lucide-react';
import Tippy from '@tippyjs/react';
import 'tippy.js/dist/tippy.css';
import moment from 'moment';

const Index = () => {
    const dispatch = useDispatch();
    const navigate = useNavigate();
    const { t } = useTranslation();
    const { coa } = useParams();
    const coaFix = coa?.replace(/\-/g, '.');

    useEffect(() => {
        dispatch(setPageTitle(t('buku_kas.detail_page.title')));
        dispatch(setTitle(t('buku_kas.detail_page.title')));
        dispatch(setBreadcrumbTitle([t('menu.dashboard'), t('menu.buku_kas'), t('buku_kas.list_page.title'), coa || '']));
    }, [dispatch, coa, t]);

    const isRtl = useSelector((state: any) => state.themeConfig.rtlClass) === 'rtl';
    const [page, setPage] = useState<number>(1);
    const PAGE_SIZES: number[] = [10, 20, 30, 50, 100];
    const [pageSize, setPageSize] = useState<number>(PAGE_SIZES[0]);

    // Date Logic
    const dateNow = new Date();
    const dateFirst = new Date(dateNow.getFullYear(), dateNow.getMonth(), 1);
    const formatDateState = (date: Date): string => {
        const year = date.getUTCFullYear();
        const month = String(date.getUTCMonth() + 1).padStart(2, '0');
        const day = String(date.getUTCDate()).padStart(2, '0');
        const hours = String(date.getUTCHours()).padStart(2, '0');
        const minutes = String(date.getUTCMinutes()).padStart(2, '0');
        const seconds = String(date.getUTCSeconds()).padStart(2, '0');
        const milliseconds = String(date.getUTCMilliseconds()).padStart(3, '0');
        return `${year}-${month}-${day}T${hours}:${minutes}:${seconds}.${milliseconds}Z`;
    };

    const [startDate, setStartDate] = useState<any>(formatDateState(dateFirst));
    const [endDate, setEndDate] = useState<any>(formatDateState(dateNow));

    const { data, refetch, isLoading } = useGetDetailCoaWithCoaQuery<any>({
        'coa': coaFix,
        'startDate': startDate,
        'endDate': endDate,
        'page': page,
        'pageSize': pageSize
    });

    // Export Mutation
    const [exportDetailCoa, { isLoading: isExporting }] = useExportDetailCoaMutation();

    const handleExport = async () => {
        try {
            const res = await exportDetailCoa({
                coa: coaFix,
                startDate: startDate,
                endDate: endDate
            }).unwrap();
            const url = window.URL.createObjectURL(new Blob([res as BlobPart]));
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', `Detail_Buku_Kas_${coa}_${moment().format('YYYYMMDD_HHmmss')}.xlsx`);
            document.body.appendChild(link);
            link.click();
            link.remove();
        } catch (err) {
            console.error("Export failed", err);
        }
    };

    useEffect(() => {
        refetch();
    }, [startDate, endDate, page, pageSize]);

    const handleFilterDate = ({ startDate, endDate }: { startDate: Date | null; endDate: Date | null }) => {
        if (startDate) setStartDate(formatDateState(startDate));
        if (endDate) setEndDate(formatDateState(endDate));
    };

    return (
        <div className="space-y-6">
            {/* Header & Stats */}
            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                <Card className="md:col-span-2 bg-gradient-to-r from-primary to-primary/80 text-white border-0 shadow-lg">
                    <CardHeader className="pb-2">
                        <div className="flex items-center justify-between">
                            <div className="flex items-center gap-2 text-white/80">
                                <Wallet className="h-5 w-5" />
                                <span className="text-sm font-medium uppercase tracking-wider">{t('menu.cash_account')}</span>
                            </div>
                            <Button
                                variant="ghost"
                                size="sm"
                                className="text-white hover:bg-white/20 hover:text-white -mr-3"
                                onClick={() => navigate('/daftar-buku-kas')}
                            >
                                <ArrowLeft className="h-4 w-4 mr-2" />
                                {t('common.back')}
                            </Button>
                        </div>
                        <CardTitle className="text-2xl font-bold mt-2 text-white">
                            {data?.data?.coa?.coaName || '-'}
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div className="flex items-center gap-2 mt-2 text-white/90 bg-white/10 w-fit px-3 py-1 rounded-full text-sm">
                            <CalendarDays className="h-4 w-4" />
                            <span>{FormatDate(startDate)}</span>
                            <span>-</span>
                            <span>{FormatDate(endDate)}</span>
                        </div>
                    </CardContent>
                </Card>

                <Card className="border-l-4 border-l-primary shadow-md">
                    <CardHeader className="pb-2">
                        <div className="text-sm font-medium text-muted-foreground uppercase tracking-wider">
                            {t('menu.ending_balance')}
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div className="text-3xl font-bold text-primary">
                            {data?.data?.coa?.currencyName ? data?.data?.coa?.currencyName : 'Rp.'} {FormatNumber(data?.data?.coa?.balance)}
                        </div>
                        <p className="text-xs text-muted-foreground mt-1">
                            {t('buku_kas.detail_page.balance_as_of')} {FormatDate(endDate)}
                        </p>
                    </CardContent>
                </Card>
            </div>

            {/* Transaction History */}
            <Card>
                <CardHeader className="border-b border-gray-100 pb-4">
                    <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div className="flex items-center gap-3">
                            <div className="p-2 rounded-lg bg-primary/10">
                                <History className="h-5 w-5 text-primary" />
                            </div>
                            <div>
                                <CardTitle className="text-lg">{t('buku_kas.detail_page.transaction_history')}</CardTitle>
                                <p className="text-sm text-gray-500 mt-0.5">
                                    {t('buku_kas.detail_page.debit_credit_list')}
                                </p>
                            </div>
                        </div>
                        <div className="flex items-center gap-2 w-full md:w-auto">
                            <Tippy content={t('buku_kas.list_page.download_excel')}>
                                <Button variant="outline" size="icon" onClick={handleExport} disabled={isExporting} className="h-10 w-10 border-gray-200 text-green-600 hover:bg-green-50 hover:border-green-200">
                                    {isExporting ? <Loader2 className="h-4 w-4 animate-spin" /> : <Download className="h-4 w-4" />}
                                </Button>
                            </Tippy>
                            <div className='w-full'>
                                <DateRangePicker onFilter={handleFilterDate} />
                            </div>
                        </div>
                    </div>
                </CardHeader>

                <CardContent className="pt-6">
                    <div className="datatables z-0 rounded-lg border border-gray-100 overflow-hidden">
                        <DataTable
                            highlightOnHover
                            className={`${isRtl ? 'whitespace-nowrap table-hover' : 'whitespace-nowrap table-hover'}`}
                            records={data?.data?.journals?.data}
                            columns={[
                                {
                                    accessor: 'transactionDate',
                                    title: t('buku_kas.detail_page.table.date'),
                                    sortable: true,
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (row: Journal) => (
                                        <span className="text-gray-600 font-medium">
                                            {row?.createdDate ? FormatDate(row?.createdDate) : '-'}
                                        </span>
                                    )
                                },
                                {
                                    accessor: 'transactionNo',
                                    title: t('buku_kas.detail_page.table.transaction_no'),
                                    sortable: true,
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (row: Journal) => (
                                        <div className="font-semibold text-primary hover:underline cursor-pointer">
                                            {row?.transactionDetail?.transactionNo || '-'}
                                        </div>
                                    )
                                },
                                {
                                    accessor: 'journalNo',
                                    title: t('buku_kas.detail_page.table.journal_no'),
                                    sortable: true,
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (row: Journal) => (
                                        <span className="text-gray-600 font-medium">
                                            {row?.transactionDetail?.journalNo || '-'}
                                        </span>
                                    )
                                },
                                {
                                    accessor: 'description',
                                    title: t('buku_kas.detail_page.table.description'),
                                    sortable: true,
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (row: Journal) => (
                                        <span className="text-gray-700 max-w-xs truncate block" title={row.journalDesc}>
                                            {row.journalDesc}
                                        </span>
                                    )
                                },
                                {
                                    accessor: 'debit',
                                    title: t('buku_kas.detail_page.table.debit'),
                                    sortable: true,
                                    textAlignment: 'right',
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50 text-right',
                                    render: (row: Journal) => (
                                        <div className="font-mono text-gray-900 text-right">
                                            {row.debitAmount ? FormatNumber(row.debitAmount) : '0'}
                                        </div>
                                    )
                                },
                                {
                                    accessor: 'credit',
                                    title: t('buku_kas.detail_page.table.credit'),
                                    sortable: true,
                                    textAlignment: 'right',
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50 text-right',
                                    render: (row: Journal) => (
                                        <div className="font-mono text-gray-900 text-right">
                                            {row.creditAmount ? FormatNumber(row.creditAmount) : '0'}
                                        </div>
                                    )
                                },
                                {
                                    accessor: 'amount',
                                    title: t('buku_kas.detail_page.table.balance'),
                                    sortable: true,
                                    textAlignment: 'right',
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50 text-right',
                                    render: (row: Journal) => (
                                        <div className="font-mono font-bold text-primary text-right">
                                            {row.balance ? FormatNumber(row.balance) : '0'}
                                        </div>
                                    )
                                },
                            ]}
                            horizontalSpacing="xs"
                            verticalSpacing="xs"
                            fetching={isLoading}
                            minHeight={200}
                            loaderVariant="dots"
                            loaderColor="#4361ee"

                            // Pagination
                            totalRecords={data?.data?.journals?.totalRecords || 0}
                            recordsPerPage={pageSize}
                            page={page}
                            onPageChange={(p) => setPage(p)}
                            recordsPerPageOptions={PAGE_SIZES}
                            onRecordsPerPageChange={setPageSize}

                            styles={{
                                root: {
                                    border: 'none',
                                    borderRadius: '0.75rem',
                                    overflow: 'hidden',
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
        </div>
    );
};

export default Index;
