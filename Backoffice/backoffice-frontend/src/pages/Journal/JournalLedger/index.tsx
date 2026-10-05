import IconCalendar from '@/components/Icon/IconCalendar';
import IconDownload from '@/components/Icon/IconDownload';
import IconLoader from '@/components/Icon/IconLoader';
import { useGetOptionBankQuery } from '@/store/api/bank/bankApiSlice';
import { useGetJournalLedgerV2Mutation } from '@/store/api/journal/journalApiSlice';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '@/store/themeConfigSlice';
import { formatCurrency } from '@/utils/currency';
import { useDownloader } from '@/utils/fileDownload';
import { FormatDateShort } from '@/utils/formatDate';
import Tippy from '@tippyjs/react';
import 'flatpickr/dist/flatpickr.css';
import { DataTable } from 'mantine-datatable';
import { useEffect, useState } from 'react';
import Flatpickr from 'react-flatpickr';
import { useDispatch, useSelector } from 'react-redux';
import SelectSearch from 'react-select';
import { toastMessage } from '@/utils/toastUtils';
import 'tippy.js/dist/tippy.css';
import { FileText, ChevronLeft, ChevronRight, ChevronsLeft, ChevronsRight } from 'lucide-react';
import { JournalLedgerType, TransactionsType } from '@/types';
import { useTranslation } from 'react-i18next';

// Child Component for Individual Account Ledger
const AccountLedgerCard = ({
  initialData,
  startDate,
  endDate
}: {
  initialData: JournalLedgerType,
  startDate: string,
  endDate: string
}) => {
  const [page, setPage] = useState<number>(1);
  const PAGE_SIZES = [10, 20, 30, 50];
  const [pageSize, setPageSize] = useState<number>(10);
  const [data, setData] = useState<JournalLedgerType>(initialData);
  const { t } = useTranslation();

  // API for this specific card's pagination
  const [fetchData, { data: apiData, isSuccess, isLoading }] = useGetJournalLedgerV2Mutation();

  // Sync when props change (e.g. parent filter update)
  useEffect(() => {
    setData(initialData);
    setPage(1);
  }, [initialData]);

  // Handle Page Change
  const handlePageChange = (p: number) => {
    setPage(p);
    fetchFetch(p, pageSize);
  };

  const handlePageSizeChange = (ps: number) => {
    setPageSize(ps);
    setPage(1);
    fetchFetch(1, ps);
  };

  const fetchFetch = (p: number, ps: number) => {
    fetchData({
      coaCode: initialData.coaCode,
      startDate, endDate, page: p, pageSize: ps
    });
  }

  // Update local data from API
  useEffect(() => {
    if (isSuccess && apiData?.data?.data?.[0]) {
      setData(apiData.data.data[0]);
    }
  }, [apiData, isSuccess]);

  return (
    <div className="bg-white dark:bg-[#1b2e4b] border dark:border-[#191e3a] rounded-lg shadow-sm overflow-hidden mb-6 animate-fade-in">
      {/* Card Header (Matches TrialJournal style) */}
      <div className="bg-gray-50 dark:bg-gray-800 p-4 border-b dark:border-[#191e3a] flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
          <h2 className="text-lg font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
            <FileText className="w-5 h-5 text-primary" />
            {data.coaName}
          </h2>
          <p className="text-xs text-gray-500 font-mono mt-1">{data.coaCode}</p>
        </div>
        <div className="flex gap-6 text-right">
          <div>
            <p className="text-[10px] text-gray-500 uppercase font-semibold">{t('journal_ledger.labels.opening')}</p>
            <p className={`text-md font-bold font-mono ${data.openingBalance < 0 ? 'text-red-500' : 'text-gray-800 dark:text-gray-200'}`}>
              {formatCurrency(data.openingBalance)}
            </p>
          </div>
          <div>
            <p className="text-[10px] text-gray-500 uppercase font-semibold">{t('journal_ledger.labels.closing')}</p>
            <p className={`text-md font-bold font-mono ${data.closingBalance < 0 ? 'text-red-500' : 'text-gray-800 dark:text-gray-200'}`}>
              {formatCurrency(data.closingBalance)}
            </p>
          </div>
        </div>
      </div>

      {/* Transactions Table */}
      <div className="datatables">
        <DataTable
          highlightOnHover
          className="whitespace-nowrap table-hover"
          records={data.transactions}
          fetching={isLoading}
          columns={[
            { accessor: 'date', title: t('journal_ledger.table.date'), render: (r: TransactionsType) => FormatDateShort(r.date) },
            { accessor: 'journalNo', title: t('journal_ledger.table.journal_no'), render: (r: TransactionsType) => <span className="font-semibold text-primary">{r.journalNo}</span> },
            { accessor: 'transactionNo', title: t('journal_ledger.table.ref_no') },
            { accessor: 'description', title: t('journal_ledger.table.description'), width: '30%', render: (r: TransactionsType) => <span className="whitespace-normal line-clamp-2 block" title={r.description}>{r.description}</span> },
            { accessor: 'debit', title: t('journal_ledger.table.debit'), textAlignment: 'right', render: (r: TransactionsType) => <span className="font-mono">{r.debit > 0 ? formatCurrency(r.debit) : '-'}</span> },
            { accessor: 'credit', title: t('journal_ledger.table.credit'), textAlignment: 'right', render: (r: TransactionsType) => <span className="font-mono">{r.credit > 0 ? formatCurrency(r.credit) : '-'}</span> },
            {
              accessor: 'runningBalance', title: t('journal_ledger.table.balance'), textAlignment: 'right',
              render: (r: TransactionsType) => <span className={`font-mono font-bold ${r.runningBalance < 0 ? 'text-red-500' : ''}`}>
                {r.runningBalance < 0 ? `(${formatCurrency(Math.abs(r.runningBalance))})` : formatCurrency(r.runningBalance)}
              </span>
            },
          ]}
          // Ensure Total Transactions is used. If initialData (List Mode) has limited transactions, totalTransactions property should tell the truth.
          totalRecords={data.totalTransactions || data.transactions.length}
          recordsPerPage={pageSize}
          page={page}
          onPageChange={handlePageChange}
          recordsPerPageOptions={PAGE_SIZES}
          onRecordsPerPageChange={handlePageSizeChange}
          styles={{
            root: {
              border: 'none',
              borderRadius: '0.75rem',
            },
            header: {
              backgroundColor: '#4361ee',
              color: '#ffffff',
              fontWeight: 'bold',
              textTransform: 'uppercase',
              letterSpacing: '0.05em',
            },
            pagination: {
              borderTop: '1px solid #f1f5f9',
              margin: '1rem',
            }
          }}
          paginationText={({ from, to, totalRecords }) => t('journal_ledger.pagination.showing_entries', { from, to, totalRecords })}
        />
      </div>
    </div>
  );
};


const Index = () => {
  const dispatch = useDispatch();
  const { t } = useTranslation();
  useEffect(() => {
    dispatch(setPageTitle(t('journal_ledger.title')));
    dispatch(setTitle(t('journal_ledger.title')));
    dispatch(setBreadcrumbTitle([t('journal_ledger.breadcrumb.dashboard'), t('journal_ledger.breadcrumb.title'), t('journal_ledger.breadcrumb.list')]));
  }, [dispatch, t]);

  const isRtl = useSelector((state: any) => state.themeConfig.rtlClass) === 'rtl';
  const [page, setPage] = useState<number>(1);
  const [isFirstLoad, setIsFirstLoad] = useState<boolean>(true);
  const PAGE_SIZES: number[] = [5, 10, 20]; // Fewer COAs per page because expanded
  const [pageSize, setPageSize] = useState<number>(5);

  // Search State
  const [selectedCoaCode, setSelectedCoaCode] = useState<{
    value: string;
    label: string;
    desc: string;
  } | null>(null);

  const today = new Date();
  const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
  const toUtcMidnight = (date: Date) => {
    return new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate(), 0, 0, 0));
  };

  const [startDate, setStartDate] = useState<any>(toUtcMidnight(firstDayOfMonth).toISOString());
  const [endDate, setEndDate] = useState<any>(toUtcMidnight(today).toISOString());

  const [searchCoa, setSearchCoa] = useState<string>('');

  // API
  const [fetchJournal, { data: journalList, isLoading }] = useGetJournalLedgerV2Mutation();
  const { downloadFile, isLoading: isLoadingDownload } = useDownloader();

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

  const handleExportClick = () => {

    const exportArgs = {
      endpoint: 'Journal/ledgers/export',
      baseFilename: `ledger_${selectedCoaCode?.desc ?? "all"}_${startDate.substring(0, 10)}.xlsx`,
      params: {
        orderBy: 'createddate',
        orderType: 'desc',
        type: 'xlsx',
        coaCode: selectedCoaCode?.desc ?? '',
        startDate: startDate ?? '',
        endDate: endDate ?? '',
      },
    };
    downloadFile(exportArgs);

  };

  const refetch = () => {
    fetchJournal({
      coaCode: selectedCoaCode?.desc ?? '',
      startDate: startDate ?? '',
      endDate: endDate ?? '',
      page: page, // Controls COA Page
      pageSize: pageSize // Controls COA Page Size
    });
    setIsFirstLoad(false);
  };

  useEffect(() => {
    refetch();
  }, [page, pageSize, selectedCoaCode]);

  // Reset page when switching modes or filters
  useEffect(() => {
    setPage(1);
  }, [selectedCoaCode, startDate, endDate]);

  // Outer Pagination Helpers
  const totalOuterRecords = journalList?.data?.totalData || 0;
  const totalOuterPages = Math.ceil(totalOuterRecords / pageSize);

  return (
    <div>
      <div className="panel">
        {/* Header Controls */}
        <div className="flex flex-col gap-4 mb-5">
          <div className="flex md:items-center md:flex-row flex-col gap-5 justify-between">

            <div className="flex items-center gap-3 w-full md:w-auto">
              {/* Date Picker */}
              <div className="relative w-[230px]">
                <Flatpickr
                  options={{
                    mode: "range",
                    dateFormat: "d/m/Y",
                    enableTime: false,
                    position: isRtl ? "auto right" : "auto left",
                    defaultDate: [toUtcMidnight(firstDayOfMonth), toUtcMidnight(today)],
                  }}
                  className="form-input font-normal w-full pr-10"
                  onChange={(dates: Date[]) => {
                    if (dates.length === 2) {
                      setStartDate(toUtcMidnight(dates[0]).toISOString());
                      setEndDate(toUtcMidnight(dates[1]).toISOString());
                    }
                  }}
                  placeholder={t('journal_ledger.placeholder.period')}
                />
                <IconCalendar className="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-500 pointer-events-none" />
              </div>

              {/* COA Search */}
              <div className="w-[300px]">
                <SelectSearch
                  className="text-white-dark z-10 w-full"
                  onInputChange={(value) => setSearchCoa(value)}
                  onChange={(selectedOption: any) => {
                    setSelectedCoaCode(selectedOption);
                  }}
                  defaultValue={selectedCoaCode}
                  value={selectedCoaCode ?? null}
                  options={coaList}
                  placeholder={t('journal_ledger.placeholder.select_account')}
                  isClearable={true}
                  isLoading={isLoadingCoa}
                  styles={{
                    menuPortal: (base) => ({ ...base, zIndex: 9999 }),
                  }}
                  menuPortalTarget={document.body}
                />
              </div>
            </div>

            {/* Actions */}
            <div className="flex items-center gap-2">
              <button
                onClick={refetch}
                className="btn btn-primary gap-2"
              >
                <IconLoader className={`w-4 h-4 ${isLoading ? 'animate-spin' : ''}`} />
                {t('journal_ledger.button.refresh')}
              </button>
              <Tippy content={t('journal_ledger.tooltip.download')}>
                <button
                  onClick={handleExportClick}
                  disabled={isLoadingDownload}
                  type="button"
                  className={`btn ${!selectedCoaCode ? 'btn-outline-secondary opacity-50' : 'btn-outline-success'}`}
                >
                  {isLoadingDownload ? <IconLoader className="animate-spin" /> : <IconDownload />}
                </button>
              </Tippy>
            </div>
          </div>
        </div>

        {/* Content: List of Account Cards */}
        {isLoading && isFirstLoad ? (
          <div className="flex justify-center items-center h-48">
            <span className="animate-spin border-4 border-primary border-l-transparent rounded-full w-10 h-10 inline-block"></span>
          </div>
        ) : (
          <div className="animate-fade-in relative">
            {journalList?.data?.data?.map((ledger: JournalLedgerType) => (
              <AccountLedgerCard
                key={ledger.coaId}
                initialData={ledger}
                startDate={startDate}
                endDate={endDate}
              />
            ))}

            {!journalList?.data?.data?.length && (
              <div className="text-center p-10 text-gray-500 bg-gray-50 rounded border border-dashed border-gray-300">
                {t('journal_ledger.empty_state')}
              </div>
            )}

            {/* Custom Outer Pagination (Controls Account List) */}
            {totalOuterRecords > 0 && (
              <div className="flex justify-between items-center mt-6 p-4 bg-gray-50 border rounded-lg">
                <div className="text-sm text-gray-500">
                  {t('journal_ledger.pagination.showing_accounts', { start: (page - 1) * pageSize + 1, end: Math.min(page * pageSize, totalOuterRecords), total: totalOuterRecords })}
                </div>
                <div className="flex items-center gap-2">
                  {/* Page Size Selector */}
                  <select
                    className="form-select w-20 h-9 py-1"
                    value={pageSize}
                    onChange={(e) => { setPageSize(Number(e.target.value)); setPage(1); }}
                  >
                    {PAGE_SIZES.map(s => <option key={s} value={s}>{s}</option>)}
                  </select>

                  <div className="flex gap-1">
                    <button
                      disabled={page === 1}
                      onClick={() => setPage(1)}
                      className="btn btn-sm btn-outline-primary p-2 disabled:opacity-50"
                    >
                      <ChevronsLeft className="w-4 h-4" />
                    </button>
                    <button
                      disabled={page === 1}
                      onClick={() => setPage(p => Math.max(1, p - 1))}
                      className="btn btn-sm btn-outline-primary p-2 disabled:opacity-50"
                    >
                      <ChevronLeft className="w-4 h-4" />
                    </button>
                    <span className="flex items-center justify-center px-4 font-semibold text-gray-700">
                      {t('journal_ledger.pagination.page_of', { page: page, pages: totalOuterPages })}
                    </span>
                    <button
                      disabled={page >= totalOuterPages}
                      onClick={() => setPage(p => Math.min(totalOuterPages, p + 1))}
                      className="btn btn-sm btn-outline-primary p-2 disabled:opacity-50"
                    >
                      <ChevronRight className="w-4 h-4" />
                    </button>
                    <button
                      disabled={page >= totalOuterPages}
                      onClick={() => setPage(totalOuterPages)}
                      className="btn btn-sm btn-outline-primary p-2 disabled:opacity-50"
                    >
                      <ChevronsRight className="w-4 h-4" />
                    </button>
                  </div>
                </div>
              </div>
            )}
          </div>
        )}
      </div>
    </div>
  );
};

export default Index;
