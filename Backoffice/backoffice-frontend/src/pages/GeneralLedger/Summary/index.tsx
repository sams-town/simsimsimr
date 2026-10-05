import React, { useEffect, useState } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { useNavigate } from 'react-router-dom';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '@/store/themeConfigSlice';
import { useTranslation } from 'react-i18next';
import { DataTable, DataTableSortStatus } from 'mantine-datatable';
import Flatpickr from 'react-flatpickr';
import SelectSearch from 'react-select';
import 'flatpickr/dist/flatpickr.css';
import 'tippy.js/dist/tippy.css';

// UI Components
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { FileText } from 'lucide-react';
import IconCalendar from '@/components/Icon/IconCalendar';
import { FormatNumber } from '@/utils/formatNumber';
import IconDownload from '@/components/Icon/IconDownload';
import { useGetOptionBankQuery } from '@/store/api/bank/bankApiSlice';
import { useGetLedgerSummaryQuery, useExportLedgerSummaryMutation } from '@/store/api/journal/journalApiSlice';

const GeneralLedgerSummaryIndex = () => {
    const dispatch = useDispatch();
    const navigate = useNavigate();
    const { t } = useTranslation();
    const isRtl = useSelector((state: any) => state.themeConfig.rtlClass) === 'rtl';

    useEffect(() => {
        dispatch(setPageTitle(t('general_ledger.summary.title')));
        dispatch(setTitle(t('general_ledger.summary.title')));
        dispatch(setBreadcrumbTitle(['Dashboard', 'General Ledger', 'Summary']));
    }, [dispatch]);

    // --- State ---
    const today = new Date();
    const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);

    const toUtcMidnight = (date: Date) => {
        return new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));
    };

    const toUtcEndOfDay = (date: Date) => {
        return new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate(), 23, 59, 59, 999));
    };

    const [startDate, setStartDate] = useState<string>(toUtcMidnight(firstDayOfMonth).toISOString());
    const [endDate, setEndDate] = useState<string>(today.toISOString());

    // Filters
    const [page, setPage] = useState(1);
    const PAGE_SIZES = [10, 20, 30, 50, 100];
    const [pageSize, setPageSize] = useState(PAGE_SIZES[0]);
    const [sortStatus, setSortStatus] = useState<DataTableSortStatus>({ columnAccessor: 'coaCode', direction: 'asc' });

    // Account Filter
    const [searchCoa, setSearchCoa] = useState('');
    const [selectedCoa, setSelectedCoa] = useState<any>(null);

    // Fetch Options (Reusing OptionBank as per previous pattern, or COA Option if distinct)
    // Assuming useGetOptionBankQuery is valid or we should use useGetOptionCOAQuery if it exists.
    // Checking previous file imports, it used `useGetOptionBankQuery` in History.
    // Fetch Options (Matches JournalLedger logic)
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

    // Main Data Query
    const { data: summaryData, isLoading, isFetching, refetch } = useGetLedgerSummaryQuery({
        page,
        pageSize,
        startDate: startDate,
        endDate: endDate,
        coaCode: selectedCoa?.desc || '',
    });

    // Export Mutation
    const [exportLedgerSummary, { isLoading: isExporting }] = useExportLedgerSummaryMutation();

    const handleExport = async () => {
        try {
            const blob = await exportLedgerSummary({
                startDate: startDate,
                endDate: endDate,
                coaCode: selectedCoa?.desc || '',
            }).unwrap();

            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', `LedgerSummary_${new Date().getTime()}.xlsx`);
            document.body.appendChild(link);
            link.click();
            link.parentNode?.removeChild(link);
        } catch (error) {
            console.error('Failed to export ledger summary', error);
        }
    };

    // Reset page on filter change
    useEffect(() => {
        setPage(1);
    }, [selectedCoa, startDate, endDate]);

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
                                    <CardTitle className="text-lg">{t('general_ledger.summary.title')}</CardTitle>
                                    <p className="text-sm text-gray-500 mt-0.5">
                                        {t('general_ledger.summary.subtitle')}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {/* Right side - Actions */}
                        <div className="flex flex-col md:flex-row items-end md:items-center gap-3 ml-auto w-full md:w-auto">
                            {/* Account Filter */}
                            <div className="w-full md:w-[300px]">
                                <SelectSearch
                                    className="text-white-dark z-10 w-full"
                                    onInputChange={(value) => setSearchCoa(value)}
                                    onChange={(selectedOption: any) => {
                                        setSelectedCoa(selectedOption);
                                    }}
                                    value={selectedCoa ?? null}
                                    options={coaList}
                                    placeholder={t('general_ledger.summary.filter_account')}
                                    isClearable={true}
                                    isLoading={isLoadingCoa}
                                    styles={{
                                        menuPortal: (base) => ({ ...base, zIndex: 9999 }),
                                    }}
                                    menuPortalTarget={document.body}
                                />
                            </div>

                            {/* Date Filter */}
                            <div className="relative w-full md:w-[240px]">
                                <Flatpickr
                                    options={{
                                        mode: 'range',
                                        dateFormat: 'd/m/Y',
                                        defaultDate: [toUtcMidnight(firstDayOfMonth), today],
                                    }}
                                    className="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 ltr:pl-9 rtl:pr-9"
                                    placeholder={t('general_ledger.history.select_date_range')}
                                    onChange={(dates: Date[]) => {
                                        if (dates.length === 2) {
                                            setStartDate(toUtcMidnight(dates[0]).toISOString());
                                            setEndDate(toUtcEndOfDay(dates[1]).toISOString());
                                        }
                                    }}
                                />
                                <div className="absolute inset-y-0 ltr:left-3 rtl:right-3 flex items-center pointer-events-none text-gray-400">
                                    <IconCalendar className="w-4 h-4" />
                                </div>
                            </div>

                            {/* Export Button */}
                            <button
                                type="button"
                                className="btn btn-primary w-full md:w-auto gap-2"
                                onClick={handleExport}
                                disabled={isExporting}
                            >
                                <IconDownload className="h-4 w-4" />
                                {isExporting ? t('common.loading') : t('common.download')}
                            </button>
                        </div>
                    </div>
                </CardHeader>

                <CardContent className="pt-6">
                    <div className="datatables z-0 rounded-lg border border-gray-100 overflow-hidden">
                        <DataTable
                            highlightOnHover
                            className={`${isRtl ? 'whitespace-nowrap table-hover' : 'whitespace-nowrap table-hover'}`}
                            records={summaryData?.data?.data}
                            totalRecords={summaryData?.data?.totalData}
                            recordsPerPage={pageSize}
                            page={page}
                            onPageChange={(p) => setPage(p)}
                            recordsPerPageOptions={PAGE_SIZES}
                            onRecordsPerPageChange={setPageSize}
                            sortStatus={sortStatus}
                            onSortStatusChange={setSortStatus}
                            minHeight={200}
                            fetching={isLoading || isFetching}
                            loaderVariant="dots"
                            loaderColor="#4361ee"
                            columns={[
                                {
                                    accessor: 'coaCode',
                                    title: t('general_ledger.summary.table.account'),
                                    sortable: true,
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (row: any) => (
                                        <div className="flex flex-col">
                                            <span className="font-bold text-gray-800">{row.coaCode}</span>
                                            <span className="text-xs text-gray-500">{row.coaName}</span>
                                        </div>
                                    ),
                                },
                                {
                                    accessor: 'beginningBalance',
                                    title: t('general_ledger.summary.table.beginning_balance'),
                                    sortable: false,
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50 text-right',
                                    textAlignment: 'right',
                                    render: (row: any) => <span className="font-mono text-gray-600 block text-right">{FormatNumber(row.beginningBalance)}</span>
                                },
                                {
                                    accessor: 'debit',
                                    title: t('general_ledger.summary.table.debit'),
                                    sortable: false,
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50 text-right',
                                    textAlignment: 'right',
                                    render: (row: any) => <span className="font-mono text-gray-600 block text-right">{FormatNumber(row.debit)}</span>
                                },
                                {
                                    accessor: 'credit',
                                    title: t('general_ledger.summary.table.credit'),
                                    sortable: false,
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50 text-right',
                                    textAlignment: 'right',
                                    render: (row: any) => <span className="font-mono text-gray-600 block text-right">{FormatNumber(row.credit)}</span>
                                },
                                {
                                    accessor: 'netChange',
                                    title: t('general_ledger.summary.table.net_change'),
                                    sortable: false,
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50 text-right',
                                    textAlignment: 'right',
                                    render: (row: any) => (
                                        <span className={`font-mono font-bold block text-right ${row.netChange < 0 ? 'text-red-500' : 'text-green-600'}`}>
                                            {FormatNumber(row.netChange)}
                                        </span>
                                    )
                                },
                                {
                                    accessor: 'endingBalance',
                                    title: t('general_ledger.summary.table.ending_balance'),
                                    sortable: false,
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50 text-right',
                                    textAlignment: 'right',
                                    render: (row: any) => <span className="font-mono font-bold text-primary block text-right">{FormatNumber(row.endingBalance)}</span>
                                },
                            ]}
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

export default GeneralLedgerSummaryIndex;
