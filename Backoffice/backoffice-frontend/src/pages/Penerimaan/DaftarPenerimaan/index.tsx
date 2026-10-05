import ConfirmationModal from '@/components/ConfirmationModal';
import Dropdown from '@/components/Dropdown';
import IconHorizontalDots from '@/components/Icon/IconHorizontalDots';
import {
  useDeleteTransactionJournalMutation,
  useVoidTransactionJournalMutation,
  useExportTransactionListMutation,
  useExportTransactionListFlatMutation,
} from '@/store/api/daftarTransfer/daftarTransferApiSlice';
import { useGetBookBanksQuery } from '@/store/api/bank/bookBank/bookBankApiSlice';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '@/store/themeConfigSlice';
import { BookBankType } from '@/types/bookBankType';
import { FormatDate } from '@/utils/formatDate';
import { FormatNumber } from '@/utils/formatNumber';
import { responseCallback } from '@/utils/responseCallback';
import { toastMessage } from '@/utils/toastUtils';
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
import { Input } from '@/components/ui/input';
import { Search, Plus, Eye, Trash2, X, FileText, Download, ChevronDown } from 'lucide-react';
import IconCalendar from '@/components/Icon/IconCalendar';

const DaftarPenerimaanIndex = () => {
  const dispatch = useDispatch();
  const navigate = useNavigate();
  const { t } = useTranslation();

  useEffect(() => {
    dispatch(setPageTitle(t('penerimaan.list_page.title')));
    dispatch(setTitle(t('penerimaan.list_page.title')));
    dispatch(setBreadcrumbTitle([t('menu.dashboard'), t('menu.penerimaan'), t('common.list')]));
  }, [dispatch, t]);

  const isRtl = useSelector((state: any) => state.themeConfig.rtlClass) === 'rtl';
  const [page, setPage] = useState<number>(1);
  const PAGE_SIZES: number[] = [10, 20, 30, 50, 100];
  const [pageSize, setPageSize] = useState<number>(PAGE_SIZES[0]);
  const [search, setSearch] = useState<string>('');
  const [status, setStatus] = useState<string>('');
  const [sortStatus, setSortStatus] = useState<DataTableSortStatus>({
    columnAccessor: 'createddate',
    direction: 'desc',
  });

  const [deleteTransactionJournal, { isLoading: isDeleting }] = useDeleteTransactionJournalMutation();
  const [voidTransactionJournal, { isLoading: isVoiding }] = useVoidTransactionJournalMutation();
  const [exportTransactionJournal, { isLoading: isExporting }] = useExportTransactionListMutation();
  const [exportTransactionFlat, { isLoading: isExportingFlat }] = useExportTransactionListFlatMutation();
  const [showDeleteModal, setShowDeleteModal] = useState<boolean>(false);
  const [deleteId, setDeleteId] = useState<string>('');
  const [confirmAction, setConfirmAction] = useState<'delete' | 'void' | null>(null);

  const today = new Date();
  const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);

  const toUtcMidnight = (date: Date) => {
    return new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate(), 0, 0, 0));
  };

  const [startDate, setStartDate] = useState<any>(toUtcMidnight(firstDayOfMonth).toISOString());
  const [endDate, setEndDate] = useState<any>(toUtcMidnight(today).toISOString());

  const {
    data: bookBankList,
    refetch,
    isLoading,
  } = useGetBookBanksQuery({
    orderBy: sortStatus.columnAccessor,
    orderType: sortStatus.direction,
    page: page,
    pageSize: pageSize,
    keyword: search,
    status: status,
    transactionType: 'Deposit',
    startDate: startDate,
    endDate: endDate,
  });

  const handleDelete = async (id: string) => {
    try {
      const response = await deleteTransactionJournal(id);
      responseCallback(response, null, null);
      refetch();
    } catch (err) {
      console.error('Error deleting Transaction Journal:', err);
      toastMessage('Failed to delete Transaction Journal.', 'error');
    }
  };

  const handleVoid = async (id: string) => {
    try {
      const response = await voidTransactionJournal(id);
      responseCallback(response, null, null);
      refetch();
    } catch (err) {
      console.error('Error voiding Transaction Journal:', err);
      toastMessage('Failed to void Transaction Journal.', 'error');
    }
  };

  const handleExport = async () => {
    try {
      const response = await exportTransactionJournal({
        keyword: search,
        status: status,
        startDate: startDate,
        endDate: endDate,
        transactionType: 'Deposit', // Backend aliases 'Deposit' -> 'Revenue' logic
      }).unwrap();
      const url = window.URL.createObjectURL(response);
      const link = document.createElement('a');
      link.href = url;
      link.setAttribute('download', `DaftarPenerimaan_${new Date().toISOString()}.xlsx`);
      document.body.appendChild(link);
      link.click();
      link.remove();
    } catch (err) {
      console.error('Failed to export:', err);
      toastMessage('Failed to export data', 'error');
    }
  };

  const handleExportFlat = async () => {
    try {
      const response = await exportTransactionFlat({
        keyword: search,
        status: status,
        startDate: startDate,
        endDate: endDate,
        transactionType: 'Deposit',
      }).unwrap();
      const url = window.URL.createObjectURL(response);
      const link = document.createElement('a');
      link.href = url;
      link.setAttribute('download', `DaftarPenerimaan_Flat_${new Date().toISOString()}.xlsx`);
      document.body.appendChild(link);
      link.click();
      link.remove();
    } catch (err) {
      console.error('Failed to export flat:', err);
      toastMessage('Failed to export flat data', 'error');
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
          <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
            {/* Left side - Title */}
            <div className="flex items-center gap-4">
              <div className="flex items-center gap-3">
                <div className="p-2 rounded-lg bg-primary/10">
                  <FileText className="h-5 w-5 text-primary" />
                </div>
                <div>
                  <CardTitle className="text-lg">{t('penerimaan.list_page.title')}</CardTitle>
                  <p className="text-sm text-gray-500 mt-0.5">
                    {t('penerimaan.list_page.subtitle')}
                  </p>
                </div>
              </div>
            </div>

            {/* Right side - Actions */}
            <div className="flex flex-col md:flex-row items-end md:items-center gap-3 ml-auto w-full md:w-auto">
              {/* Filter Status */}
              <div className="w-[150px]">
                <select
                  className="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                  onChange={(e) => setStatus(e.target.value)}
                  value={status}
                >
                  <option value={''}>{t('penerimaan.list_page.filter.all_status')}</option>
                  <option value={'published'}>{t('penerimaan.list_page.filter.publish')}</option>
                  <option value={'pending'}>{t('penerimaan.list_page.filter.pending')}</option>
                  <option value={'void'}>{t('penerimaan.list_page.filter.void')}</option>
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

              <div className="relative flex-1 md:w-[200px]">
                <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" />
                <Input
                  type="text"
                  placeholder={t('penerimaan.list_page.search_placeholder')}
                  value={search}
                  onChange={(e) => setSearch(e.target.value)}
                  className="pl-9"
                />
              </div>

              <div className="flex items-center gap-2">
                <div className="dropdown">
                  <Dropdown
                    offset={[0, 5]}
                    placement="bottom-end"
                    btnClassName="w-full"
                    button={
                      <Button variant="outline" className="gap-2 w-full" disabled={isExporting || isExportingFlat}>
                        <Download className="h-4 w-4" />
                        <span className="hidden sm:inline">{t('penerimaan.list_page.export')}</span>
                        <ChevronDown className="h-4 w-4" />
                      </Button>
                    }
                  >
                    <ul className="text-sm font-medium">
                      <li>
                        <button
                          type="button"
                          className="w-full text-left px-4 py-2 hover:bg-gray-50 dark:hover:bg-gray-800"
                          onClick={handleExport}
                          disabled={isExporting}
                        >
                          {isExporting ? t('penerimaan.list_page.exporting') : t('penerimaan.list_page.export_excel')}
                        </button>
                      </li>
                      <li>
                        <button
                          type="button"
                          className="w-full text-left px-4 py-2 hover:bg-gray-50 dark:hover:bg-gray-800"
                          onClick={handleExportFlat}
                          disabled={isExportingFlat}
                        >
                          {isExportingFlat ? t('penerimaan.list_page.exporting') : t('penerimaan.list_page.export_flat')}
                        </button>
                      </li>
                    </ul>
                  </Dropdown>
                </div>
                <Button onClick={() => navigate(`/daftar-penerimaan/create`)} className="gap-2">
                  <Plus className="h-4 w-4" />
                  <span className="hidden sm:inline">{t('penerimaan.list_page.add_new')}</span>
                </Button>
              </div>
            </div>
          </div>
        </CardHeader>

        <CardContent className="pt-6">
          <div className="datatables z-0 rounded-lg border border-gray-100">
            <DataTable
              highlightOnHover
              className={`${isRtl ? 'whitespace-nowrap table-hover' : 'whitespace-nowrap table-hover'}`}
              records={bookBankList?.data?.data}
              columns={[
                {
                  accessor: 'transactionNo',
                  title: t('penerimaan.list_page.table.transaction_no'),
                  sortable: true,
                  titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                  render: (row: BookBankType) => (
                    <div className="font-semibold text-primary hover:underline cursor-pointer" onClick={() => navigate(`/daftar-penerimaan/detail/${row.transactionId}`)}>
                      {row.transactionNo}
                    </div>
                  )
                },
                {
                  accessor: 'journalNo',
                  title: t('penerimaan.list_page.table.journal_no'),
                  sortable: true,
                  titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                  render: (row) => <span className="font-medium text-gray-600">{row.journalNo || '-'}</span>
                },
                {
                  accessor: 'transactionDate',
                  title: t('penerimaan.list_page.table.date'),
                  sortable: true,
                  titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                  render: (row: BookBankType) =>
                    <span className="text-gray-600">
                      {row.transactionDate ? FormatDate(row.transactionDate) : '-'}
                    </span>
                },
                {
                  accessor: 'coaName',
                  title: t('penerimaan.list_page.table.account'),
                  sortable: true,
                  titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                  render: (row) => (
                    <div className="max-w-xs truncate font-medium text-gray-700" title={row.coaName}>
                      {row.coaName}
                    </div>
                  )
                },
                {
                  accessor: 'amount',
                  title: t('penerimaan.list_page.table.amount'),
                  sortable: true,
                  textAlignment: 'right',
                  titleClassName: 'font-bold text-gray-800 bg-gray-50/50 text-right',
                  render: (row: BookBankType) => (
                    <div className="font-mono font-bold text-gray-900 text-right">
                      {FormatNumber(row.amount)}
                    </div>
                  ),
                },
                {
                  accessor: 'status',
                  title: t('penerimaan.list_page.table.status'),
                  sortable: true,
                  textAlignment: 'center',
                  titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                  render: (row: BookBankType) => {
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
                  title: t('penerimaan.list_page.table.action'),
                  titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                  render: (row: BookBankType) => (
                    <div className="flex items-center gap-1 justify-center">
                      <Tippy content={t('penerimaan.list_page.actions.view')}>
                        <Button
                          variant="ghost"
                          size="sm"
                          onClick={() => navigate(`/daftar-penerimaan/detail/${row.transactionId}`)}
                          className="h-8 w-8 p-0 text-gray-500 hover:text-primary hover:bg-primary/10 rounded-lg"
                        >
                          <Eye className="h-4 w-4" />
                        </Button>
                      </Tippy>

                      {row.status !== 'Published' && row.status !== 'Void' && (
                        <div className="dropdown">
                          <Dropdown
                            offset={[0, 5]}
                            placement="auto-end"
                            strategy="fixed"
                            button={<IconHorizontalDots className="opacity-70 cursor-pointer hover:opacity-100 text-gray-500" />}
                          >
                            <ul className="text-sm font-medium">
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
                                  {t('penerimaan.list_page.actions.void')}
                                </button>
                              </li>
                            </ul>
                          </Dropdown>
                        </div>
                      )}
                    </div>
                  ),
                },
              ]}
              totalRecords={bookBankList?.data?.totalData}
              recordsPerPage={pageSize}
              page={page}
              onPageChange={(p) => setPage(p)}
              recordsPerPageOptions={PAGE_SIZES}
              onRecordsPerPageChange={setPageSize}
              sortStatus={sortStatus}
              onSortStatusChange={setSortStatus}
              minHeight={400}
              paginationText={({ from, to, totalRecords }) => t('common.pagination_text', { from, to, totalRecords })}
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
        title={confirmAction === 'delete' ? t('penerimaan.list_page.modal.delete_title') : t('penerimaan.list_page.modal.void_title')}
        message={confirmAction === 'delete' ? t('penerimaan.list_page.modal.delete_message') : t('penerimaan.list_page.modal.void_message')}
        confirmText={confirmAction === 'delete' ? t('penerimaan.list_page.actions.delete') : t('penerimaan.list_page.actions.void')}
        variant={confirmAction === 'delete' ? 'danger' : 'primary'}
        cancelText={t('common.cancel')}
      />
    </div>
  );
};

export default DaftarPenerimaanIndex;
