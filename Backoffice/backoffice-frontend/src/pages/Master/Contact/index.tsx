import { useNavigate } from 'react-router-dom';
import { useDispatch, useSelector } from 'react-redux';
import { DataTable, DataTableSortStatus } from 'mantine-datatable';
import React, { Fragment, useEffect, useState } from 'react';
import { useTranslation } from 'react-i18next';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '../../../store/themeConfigSlice';
import { useGetContactQuery, useDeleteContactMutation, useExportContactMutation } from '@/store/api/contact/contactApiSlice';
import { ContactType } from '@/types/contactType';
import { useDownloader } from '@/utils/fileDownload';
import { responseCallback } from '@/utils/responseCallback';
import { toastMessage } from '@/utils/toastUtils';
import Tippy from '@tippyjs/react';
import 'tippy.js/dist/tippy.css';
import ConfirmationModal from '@/components/ConfirmationModal';

// New Imports
import { Search, Download, Plus, Eye, Trash2, Loader2, Users } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';


const Index = () => {
  const dispatch = useDispatch();
  const { t } = useTranslation();
  useEffect(() => {
    dispatch(setPageTitle(t('master.contact_page.title')));
    dispatch(setTitle(t('master.contact_page.title')));
    dispatch(setBreadcrumbTitle(['Dashboard', t('master.title'), t('master.contact_page.title'), t('master.contact_page.list')]));
  }, [dispatch]);

  const isRtl = useSelector((state: any) => state.themeConfig.rtlClass) === 'rtl';
  const [page, setPage] = useState<number>(1);
  const PAGE_SIZES: number[] = [10, 20, 30, 50, 100];
  const [pageSize, setPageSize] = useState<number>(PAGE_SIZES[0]);
  const [search, setSearch] = useState<string>('');
  const [sortStatus, setSortStatus] = useState<DataTableSortStatus>({
    columnAccessor: 'contactId',
    direction: 'desc',
  });
  const [debouncedSearch, setDebouncedSearch] = useState<string>('');
  const [confirmAction, setConfirmAction] = useState<'delete' | null>(null);
  const { downloadFile, isLoading: isLoadingDownload } = useDownloader();

  const {
    data: contactList,
    refetch,
    error,
    isLoading,
  } = useGetContactQuery({
    keyword: debouncedSearch,
    page: page,
    pageSize: pageSize,
    orderBy: sortStatus.columnAccessor === 'contactId' ? 'contactId' : sortStatus.columnAccessor,
    orderType: sortStatus.direction,
  });

  const [deleteContact, { isLoading: isLoadingDelete }] = useDeleteContactMutation();
  const [showDeleteModal, setShowDeleteModal] = useState<boolean>(false);
  const [deleteId, setDeleteId] = useState<number>(0);
  const navigate = useNavigate();

  const handleDelete = async (id: number) => {
    try {
      const response = await deleteContact(id);
      responseCallback(response, null, null);
      refetch();
    } catch (err) {
      console.error('Error deleting Contact:', err);
      toastMessage('Failed to delete Contact.', 'error');
    }
  };

  const handleExportClick = () => {
    const exportArgs = {
      endpoint: 'Contact/Export',
      baseFilename: `contacts_${new Date().toISOString().split('T')[0]}.xlsx`,
      params: {
        orderBy: 'createddate',
        orderType: 'desc',
        type: 'xlsx',
        keyword: search,
      },
    };
    console.log('params', exportArgs);

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
            {/* Left side - Title */}
            <div className="flex items-center gap-4">
              <div className="flex items-center gap-3">
                <div className="p-2 rounded-lg bg-primary/10">
                  <Users className="h-5 w-5 text-primary" />
                </div>
                <div>
                  <CardTitle className="text-lg">{t('master.contact_page.list_title')}</CardTitle>
                  <p className="text-sm text-gray-500 mt-0.5">
                    {t('master.contact_page.subtitle')}
                  </p>
                </div>
              </div>
            </div>

            {/* Right side - Actions */}
            <div className="flex flex-col md:flex-row items-end md:items-center gap-3 ml-auto w-full md:w-auto">
              <div className="relative flex-1 md:w-[250px]">
                <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" />
                <Input
                  type="text"
                  placeholder={t('common.search')}
                  value={search}
                  onChange={(e) => setSearch(e.target.value)}
                  className="pl-9"
                />
              </div>

              <div className="flex items-center gap-2">
                <Tippy content="Download">
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

                <Button onClick={() => navigate(`/contact/create`)} className="gap-2">
                  <Plus className="h-4 w-4" />
                  <span className="hidden sm:inline">{t('master.contact_page.add_new')}</span>
                </Button>
              </div>
            </div>
          </div>
        </CardHeader>
        <CardContent className="pt-6">
          <div className="datatables z-0 rounded-lg border border-gray-100 overflow-hidden">
            <DataTable
              highlightOnHover
              className={`${isRtl ? 'whitespace-nowrap table-hover' : 'whitespace-nowrap table-hover'
                }`}
              records={contactList?.data?.data}
              columns={[
                { accessor: 'refCode', title: t('master.contact_page.table.ref_code'), sortable: true },
                { accessor: 'contactName', title: t('master.contact_page.table.name'), sortable: true },
                { accessor: 'email', title: t('master.contact_page.table.email'), sortable: true },
                { accessor: 'phone', title: t('master.contact_page.table.phone'), sortable: true },
                {
                  accessor: 'contactCategory',
                  title: t('master.contact_page.table.category'),
                  sortable: true,
                  render: (s: ContactType) => (
                    <span className="px-2 py-1 rounded-full bg-gray-100 text-xs font-medium">
                      {s.contactCategory?.contactCategoryName}
                    </span>
                  ),
                },
                {
                  accessor: '',
                  title: t('common.actions'),
                  width: 100,
                  render: (s: ContactType) => (
                    <div className="flex items-center gap-1">
                      <Tippy content="View">
                        <Button
                          variant="ghost"
                          size="sm"
                          onClick={() => navigate(`/contact/update/${s.contactId}`)}
                          className="h-8 w-8 p-0 text-gray-500 hover:text-primary hover:bg-primary/10 rounded-lg"
                        >
                          <Eye className="h-4 w-4" />
                        </Button>
                      </Tippy>
                      <Tippy content="Delete">
                        <Button
                          variant="ghost"
                          size="sm"
                          onClick={async () => {
                            setConfirmAction('delete');
                            setDeleteId(s.contactId);
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
              totalRecords={contactList?.data?.totalData}
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

      <div className="mb-5">
        <ConfirmationModal
          confirmText={t('common.delete')}
          message={t('messages.delete_warning')}
          isOpen={showDeleteModal}
          onClose={() => {
            setShowDeleteModal(false);
          }}
          onConfirm={async () => {
            if (!deleteId) return;
            if (confirmAction === 'delete') {
              await handleDelete(deleteId);
            }
          }}
        />
      </div>
    </div>
  );
};

export default Index;
