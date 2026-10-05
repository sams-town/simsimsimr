import { Search, Download, Plus, Eye, Trash2, Loader2, Building2 } from 'lucide-react';
import {
  useDeleteMasterBankMutation,
  useGetMasterBankQuery,
} from '@/store/api/bank/master/masterBankApiSlice';
import { MasterBankType } from '@/types/masterBankType';
import { useDownloader } from '@/utils/fileDownload';
import { responseCallback } from '@/utils/responseCallback';
import { toastMessage } from '@/utils/toastUtils';
import Tippy from '@tippyjs/react';
import { DataTable, DataTableSortStatus } from 'mantine-datatable';
import React, { useEffect, useState } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { useNavigate } from 'react-router-dom';
import 'tippy.js/dist/tippy.css';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '../../../store/themeConfigSlice';
import { useTranslation } from 'react-i18next';

// shadcn/ui components
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { AlertDialog } from '@/components/ui/alert-dialog';

const Index = () => {
  const dispatch = useDispatch();
  const { t } = useTranslation();
  useEffect(() => {
    dispatch(setPageTitle(t('master.bank_page.title')));
    dispatch(setTitle(t('master.bank_page.title')));
    dispatch(setBreadcrumbTitle(['Dashboard', t('master.title'), t('master.bank_page.title'), t('master.bank_page.list')]));
  }, [dispatch]);

  const isRtl = useSelector((state: any) => state.themeConfig.rtlClass) === 'rtl';
  const [page, setPage] = useState<number>(1);
  const PAGE_SIZES: number[] = [10, 20, 30, 50, 100];
  const [pageSize, setPageSize] = useState<number>(PAGE_SIZES[0]);
  const [search, setSearch] = useState<string>('');
  const [sortStatus, setSortStatus] = useState<DataTableSortStatus>({
    columnAccessor: 'bankCode',
    direction: 'asc',
  });
  const [debouncedSearch, setDebouncedSearch] = useState<string>('');
  const [confirmAction, setConfirmAction] = useState<'delete' | null>(null);
  const { downloadFile, isLoading: isLoadingDownload } = useDownloader();

  const {
    data: bankList,
    refetch,
    error,
    isLoading,
  } = useGetMasterBankQuery({
    keyword: debouncedSearch,
    page: page,
    pageSize: pageSize,
    orderBy: sortStatus.columnAccessor === 'bankName' ? 'bankName' : sortStatus.columnAccessor,
    orderType: sortStatus.direction,
  });

  const [deleteBank, { isLoading: isLoadingDelete }] = useDeleteMasterBankMutation();
  const [showDeleteModal, setShowDeleteModal] = useState<boolean>(false);
  const [deleteId, setDeleteId] = useState<string>('');
  const navigate = useNavigate();

  const handleDelete = async (id: string) => {
    try {
      const response = await deleteBank(id);
      responseCallback(response, null, null);
      refetch();
    } catch (err) {
      console.error('Error deleting Bank:', err);
      toastMessage('Failed to delete Bank.', 'error');
    }
  };

  const handleExportClick = () => {
    const exportArgs = {
      endpoint: 'Bank/Export',
      baseFilename: `bank_${new Date().toISOString().split('T')[0]}.xlsx`,
      params: {
        orderBy: 'createddate',
        orderType: 'desc',
        type: 'xlsx',
        keyword: search,
      },
    };
    downloadFile(exportArgs);
  };

  useEffect(() => {
    refetch();
  }, [page, search, sortStatus, pageSize, refetch]);

  useEffect(() => {
    setPage(1);
  }, [sortStatus, search, pageSize]);

  useEffect(() => {
    const handler = setTimeout(() => {
      setDebouncedSearch(search);
    }, 500);

    return () => {
      clearTimeout(handler);
    };
  }, [search]);

  return (
    <div className="space-y-6">
      <Card>
        <CardHeader className="border-b border-gray-100 pb-4">
          <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
            {/* Left side - Title and Search */}
            <div className="flex items-center gap-4">
              <div className="flex items-center gap-3">
                <div className="p-2 rounded-lg bg-primary/10">
                  <Building2 className="h-5 w-5 text-primary" />
                </div>
                <div>
                  <CardTitle className="text-lg">{t('master.bank_page.title')}</CardTitle>
                  <p className="text-sm text-gray-500 mt-0.5">
                    {t('master.bank_page.subtitle')}
                  </p>
                </div>
              </div>
            </div>

            {/* Right side - Actions */}
            <div className="flex items-center gap-3">
              {/* Search */}
              <div className="relative">
                <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" />
                <Input
                  type="text"
                  placeholder={t('common.search')}
                  value={search}
                  onChange={(e) => setSearch(e.target.value)}
                  className="pl-9 w-[200px] md:w-[280px]"
                />
              </div>

              {/* Export Button */}
              <Tippy content="Download Bank">
                <Button
                  variant="outline"
                  size="icon"
                  onClick={handleExportClick}
                  disabled={isLoadingDownload}
                >
                  {isLoadingDownload ? (
                    <Loader2 className="h-4 w-4 animate-spin" />
                  ) : (
                    <Download className="h-4 w-4" />
                  )}
                </Button>
              </Tippy>

              {/* Add Button */}
              <Button onClick={() => navigate(`/bank/create`)} className="gap-2">
                <Plus className="h-4 w-4" />
                <span className="hidden sm:inline">{t('master.bank_page.add_new')}</span>
              </Button>
            </div>
          </div>
        </CardHeader>

        <CardContent className="pt-6">
          <div className="datatables rounded-lg border border-gray-100 overflow-hidden">
            <DataTable
              highlightOnHover
              className={`${isRtl ? 'whitespace-nowrap table-hover' : 'whitespace-nowrap table-hover'}`}
              records={bankList?.data?.data}
              columns={[
                {
                  accessor: 'bankCode',
                  title: t('master.bank_page.table.code'),
                  sortable: true,
                  render: (row: MasterBankType) => (
                    <div className="flex items-center gap-2">
                      <div className="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center">
                        <Building2 className="w-4 h-4 text-primary" />
                      </div>
                      <span className="font-semibold text-gray-900">{row.bankCode}</span>
                    </div>
                  )
                },
                {
                  accessor: 'bankName',
                  title: t('master.bank_page.table.name'),
                  sortable: true,
                  render: (row: MasterBankType) => (
                    <span className="text-gray-700 font-medium">{row.bankName}</span>
                  )
                },
                {
                  accessor: '',
                  title: t('common.actions'),
                  width: 120,
                  render: (s: MasterBankType) => (
                    <div className="flex items-center gap-1">
                      <Tippy content="View Details">
                        <Button
                          variant="ghost"
                          size="sm"
                          onClick={() => navigate(`/bank/update/${s.bankCode}`)}
                          className="h-8 w-8 p-0 text-gray-500 hover:text-primary hover:bg-primary/10 rounded-lg"
                        >
                          <Eye className="h-4 w-4" />
                        </Button>
                      </Tippy>
                      <Tippy content="Delete">
                        <Button
                          variant="ghost"
                          size="sm"
                          onClick={() => {
                            setConfirmAction('delete');
                            setDeleteId(s.bankCode);
                            setShowDeleteModal(true);
                          }}
                          className="h-8 w-8 p-0 text-gray-500 hover:text-danger hover:bg-danger/10 rounded-lg"
                        >
                          <Trash2 className="h-4 w-4" />
                        </Button>
                      </Tippy>
                    </div>
                  ),
                },
              ]}
              totalRecords={bankList?.data?.totalData}
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
              loadingText={t('common.loading')}
              fetching={isLoading}
              loaderVariant="dots"
              loaderColor="#648de1"
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
                pagination: {
                  borderTop: '1px solid #f1f5f9',
                  margin: '1rem',
                },
              }}
            />
          </div>
        </CardContent>
      </Card>

      <AlertDialog
        isOpen={showDeleteModal}
        onClose={() => setShowDeleteModal(false)}
        onConfirm={async () => {
          if (!deleteId) return;
          if (confirmAction === 'delete') {
            await handleDelete(deleteId);
          }
        }}
        title={t('master.bank_page.delete_title')}
        message={t('messages.delete_warning')}
        confirmText={t('common.delete')}
        cancelText={t('common.cancel')}
        variant="danger"
        loading={isLoadingDelete}
      />
    </div>
  );
};

export default Index;
