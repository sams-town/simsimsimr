import { Search, Plus, Eye, Trash2, ArrowRightLeft, Loader2 } from 'lucide-react';
import { DataTable, DataTableSortStatus } from 'mantine-datatable';
import React, { Fragment, useEffect, useState } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { useTranslation } from 'react-i18next';
import { useNavigate } from 'react-router-dom';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '../../../store/themeConfigSlice';
import { useDeleteCoaMappingMutation, useGetCoaMappingsQuery } from '@/store/api/coaMapping/coaMappingApiSlice';
import { CoaMappingType } from '@/types/coaMappingType';
import { responseCallback } from '@/utils/responseCallback';
import { toastMessage } from '@/utils/toastUtils';
import Tippy from '@tippyjs/react';
import 'tippy.js/dist/tippy.css';
import { Dialog, Transition } from '@headlessui/react';
import IconX from '@/components/Icon/IconX';

import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

const Index = () => {
    const dispatch = useDispatch();
    const { t } = useTranslation();
    useEffect(() => {
        dispatch(setPageTitle(t('master.coa_mapping_page.title')));
        dispatch(setTitle(t('master.coa_mapping_page.title')));
        dispatch(setBreadcrumbTitle(['Dashboard', 'Master', t('master.coa_mapping_page.title'), t('master.coa_mapping_page.list')]));
    }, [dispatch]);

    const isRtl = useSelector((state: any) => state.themeConfig.rtlClass) === 'rtl';
    const [page, setPage] = useState<number>(1);
    const PAGE_SIZES: number[] = [10, 20, 30, 50, 100];
    const [pageSize, setPageSize] = useState<number>(PAGE_SIZES[0]);
    const [search, setSearch] = useState<string>('');
    const [sortStatus, setSortStatus] = useState<DataTableSortStatus>({ columnAccessor: 'id', direction: 'asc' });
    const [debouncedSearch, setDebouncedSearch] = useState<string>('');

    const {
        data: coaMappingList,
        refetch,
        error,
        isLoading,
    } = useGetCoaMappingsQuery<any>({
        keyword: debouncedSearch,
        page: page,
        pageSize: pageSize,
        orderBy: sortStatus.columnAccessor,
        orderType: sortStatus.direction,
    });

    const [deleted] = useDeleteCoaMappingMutation();
    const [showDeleteModal, setShowDeleteModal] = useState<boolean>(false);
    const [deleteId, setDeleteId] = useState<number>(0);
    const navigate = useNavigate();

    const handleDelete = async () => {
        try {
            const response: any = await deleted(deleteId);
            setDeleteId(0);
            setShowDeleteModal(false);
            responseCallback(response, null, null);
            setPage(1);
            refetch();
        } catch (err: any) {
            toastMessage(err.message, 'error');
        }
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
                        <div className="flex items-center gap-4">
                            <div className="flex items-center gap-3">
                                <div className="p-2 rounded-lg bg-primary/10">
                                    <ArrowRightLeft className="h-5 w-5 text-primary" />
                                </div>
                                <div>
                                    <CardTitle className="text-lg">{t('master.coa_mapping_page.title')}</CardTitle>
                                    <p className="text-sm text-gray-500 mt-0.5">
                                        {t('master.coa_mapping_page.subtitle')}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div className="flex items-center gap-3">
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

                            <Button onClick={() => navigate(`/coa-mapping/create`)} className="gap-2">
                                <Plus className="h-4 w-4" />
                                <span className="hidden sm:inline">{t('common.add_new')}</span>
                            </Button>
                        </div>
                    </div>
                </CardHeader>
                <CardContent className="pt-6">
                    <div className="datatables rounded-lg border border-gray-100 overflow-hidden">
                        <DataTable
                            highlightOnHover
                            className={`${isRtl ? 'whitespace-nowrap table-hover' : 'whitespace-nowrap table-hover'}`}
                            records={coaMappingList?.data?.data}
                            columns={[
                                {
                                    accessor: 'coaSource',
                                    title: t('master.coa_mapping_page.table.coa_source'),
                                    sortable: true,
                                    render: (row: CoaMappingType) => (
                                        <span className="font-semibold text-gray-900">{row.coaSource}</span>
                                    ),
                                },
                                {
                                    accessor: 'coaSourceName',
                                    title: t('master.coa_mapping_page.table.coa_source_name'),
                                    sortable: true,
                                    render: (row: CoaMappingType) => (
                                        <span className="text-gray-600">{row.coaSourceName || '-'}</span>
                                    ),
                                },
                                {
                                    accessor: 'coaDest',
                                    title: t('master.coa_mapping_page.table.coa_dest'),
                                    sortable: true,
                                    render: (row: CoaMappingType) => (
                                        <span className="font-semibold text-gray-900">{row.coaDest}</span>
                                    ),
                                },
                                {
                                    accessor: 'coaDestName',
                                    title: t('master.coa_mapping_page.table.coa_dest_name'),
                                    render: (row: CoaMappingType) => (
                                        <span className="text-gray-600">{row.coaDestName || '-'}</span>
                                    ),
                                },
                                {
                                    accessor: '',
                                    title: t('common.action'),
                                    width: 100,
                                    render: (s: CoaMappingType) => (
                                        <div className="flex items-center gap-1">
                                            <Tippy content="View">
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    onClick={() => navigate(`/coa-mapping/update/${s.id}`)}
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
                                                        setDeleteId(s.id as number);
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
                            totalRecords={coaMappingList?.data?.totalData}
                            recordsPerPage={pageSize}
                            page={page}
                            onPageChange={(p) => setPage(p)}
                            recordsPerPageOptions={PAGE_SIZES}
                            onRecordsPerPageChange={setPageSize}
                            sortStatus={sortStatus}
                            onSortStatusChange={setSortStatus}
                            minHeight={200}
                            paginationText={({ from, to, totalRecords }) => t('common.pagination_text', { from, to, totalRecords })}
                            fetching={isLoading}
                            loaderVariant="dots"
                            loaderColor="#648de1"
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
                <Transition appear show={showDeleteModal} as={Fragment}>
                    <Dialog as="div" open={showDeleteModal} onClose={() => setShowDeleteModal(false)}>
                        <Transition.Child
                            as={Fragment}
                            enter="ease-out duration-300"
                            enterFrom="opacity-0"
                            enterTo="opacity-100"
                            leave="ease-in duration-200"
                            leaveFrom="opacity-100"
                            leaveTo="opacity-0"
                        >
                            <div className="fixed inset-0" />
                        </Transition.Child>
                        <div id="fadein_modal" className="fixed inset-0 bg-[black]/60 z-[999] overflow-y-auto">
                            <div className="flex items-start justify-center min-h-screen px-4">
                                <Dialog.Panel className="panel border-0 p-0 rounded-lg overflow-hidden w-full max-w-lg my-8 text-black dark:text-white-dark animate__animated animate__fadeIn">
                                    <div className="flex bg-[#fbfbfb] dark:bg-[#121c2c] items-center justify-between px-5 py-3">
                                        <h5 className="font-bold text-lg">{t('messages.confirmation')}</h5>
                                        <button onClick={() => setShowDeleteModal(false)} type="button" className="text-white-dark hover:text-dark">
                                            <IconX />
                                        </button>
                                    </div>
                                    <div className="p-5">
                                        <p>
                                            {t('messages.delete_warning')}
                                        </p>
                                        <div className="flex justify-end items-center mt-8">
                                            <button onClick={() => setShowDeleteModal(false)} type="button" className="btn btn-outline-dark">
                                                {t('common.cancel')}
                                            </button>
                                            <button onClick={handleDelete} type="button" className="btn btn-outline-danger ltr:ml-4 rtl:mr-4">
                                                {t('common.delete')}
                                            </button>
                                        </div>
                                    </div>
                                </Dialog.Panel>
                            </div>
                        </div>
                    </Dialog>
                </Transition>
            </div>
        </div>
    );
};

export default Index;
