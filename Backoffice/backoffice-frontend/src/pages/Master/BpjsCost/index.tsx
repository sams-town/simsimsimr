import { useNavigate } from 'react-router-dom';
import { useDispatch, useSelector } from 'react-redux';
import { DataTable, DataTableSortStatus } from 'mantine-datatable';
import { Fragment, useEffect, useState } from 'react';
import { useTranslation } from 'react-i18next';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '../../../store/themeConfigSlice';
import { useDeleteBpjsCostMutation, useGetBpjsCostsQuery, useExportBpjsCostMutation } from '@/store/api/bpjsCost/bpjsCostApiSlice';
import { responseCallback } from '@/utils/responseCallback';
import { toastMessage } from '@/utils/toastUtils';
import Tippy from '@tippyjs/react';
import 'tippy.js/dist/tippy.css';
import { BpjsCostType } from '@/types';
import { Dialog, Transition } from '@headlessui/react';

import { Search, Download, Plus, Eye, Trash2, Loader2, Banknote, X } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

const Index = () => {
    const dispatch = useDispatch();
    const { t } = useTranslation();
    useEffect(() => {
        dispatch(setPageTitle(t('master.bpjs_cost_page.title')));
        dispatch(setTitle(t('master.bpjs_cost_page.title')));
        dispatch(setBreadcrumbTitle(['Dashboard', t('master.title'), t('master.bpjs_cost_page.title'), t('master.bpjs_cost_page.list')]));
    }, [dispatch]);

    const isRtl = useSelector((state: any) => state.themeConfig.rtlClass) === 'rtl';
    const [page, setPage] = useState<number>(1);
    const PAGE_SIZES: number[] = [10, 20, 30, 50, 100];
    const [pageSize, setPageSize] = useState<number>(PAGE_SIZES[0]);
    const [search, setSearch] = useState<string>('');
    const [sortStatus, setSortStatus] = useState<DataTableSortStatus>({ columnAccessor: 'costId', direction: 'asc' });
    const [debouncedSearch, setDebouncedSearch] = useState<string>('');

    const {
        data: bpjsCostList,
        refetch,
        isLoading,
    } = useGetBpjsCostsQuery({
        keyword: debouncedSearch,
        page: page,
        pageSize: pageSize,
        orderBy: sortStatus.columnAccessor === 'procedure' ? 'procedure' : sortStatus.columnAccessor === 'cost' ? 'cost' : 'costId',
        orderType: sortStatus.direction,
    });

    const [deleteBpjsCost] = useDeleteBpjsCostMutation();
    const [showDeleteModal, setShowDeleteModal] = useState<boolean>(false);
    const [deleteId, setDeleteId] = useState<number>(0);
    const [exportBpjsCost, { isLoading: isExporting }] = useExportBpjsCostMutation();
    const navigate = useNavigate();

    const handleDelete = async () => {
        try {
            const response: any = await deleteBpjsCost(deleteId);
            setDeleteId(0);
            setShowDeleteModal(false);
            responseCallback(response, null, null);
            setPage(1);
            refetch();
        } catch (err: any) {
            toastMessage(err.message, 'error');
        }
    };

    const handleExport = async () => {
        try {
            const type = 'xlsx';
            const keyword = search;
            const orderBy = sortStatus.columnAccessor;
            const orderType = sortStatus.direction;

            const res = await exportBpjsCost({ type, keyword, orderBy, orderType }).unwrap();
            const url = window.URL.createObjectURL(res);
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', `bpjs_cost_${new Date().toISOString()}.xlsx`);
            document.body.appendChild(link);
            link.click();
            link.parentNode?.removeChild(link);
        } catch (error: any) {
            console.error('Failed to download BPJS Cost', error);
            toastMessage(error.message, 'error');
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

    const formatCurrency = (value: number) => {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(value);
    };

    return (
        <div className="space-y-6">
            <Card>
                <CardHeader className="border-b border-gray-100 pb-4">
                    <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div className="flex items-center gap-4">
                            <div className="flex items-center gap-3">
                                <div className="p-2 rounded-lg bg-primary/10">
                                    <Banknote className="h-5 w-5 text-primary" />
                                </div>
                                <div>
                                    <CardTitle className="text-lg">{t('master.bpjs_cost_page.title')}</CardTitle>
                                    <p className="text-sm text-gray-500 mt-0.5">
                                        {t('master.bpjs_cost_page.subtitle')}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div className="flex flex-col md:flex-row items-end md:items-center gap-3 ml-auto w-full md:w-auto">
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

                            <div className="flex items-center gap-2">
                                <Tippy content="Download">
                                    <Button
                                        variant="outline"
                                        size="icon"
                                        onClick={handleExport}
                                        disabled={isExporting}
                                    >
                                        {isExporting ? <Loader2 className="h-4 w-4 animate-spin" /> : <Download className="h-4 w-4" />}
                                    </Button>
                                </Tippy>

                                <Button onClick={() => navigate(`/bpjs-cost/create`)} className="gap-2">
                                    <Plus className="h-4 w-4" />
                                    <span className="hidden sm:inline">{t('common.add_new')}</span>
                                </Button>
                            </div>
                        </div>
                    </div>
                </CardHeader>
                <CardContent className="pt-6">
                    <div className="datatables z-0 rounded-lg border border-gray-100 overflow-hidden">
                        <DataTable
                            highlightOnHover
                            className={`${isRtl ? 'whitespace-nowrap table-hover' : 'whitespace-nowrap table-hover'}`}
                            records={bpjsCostList?.data?.data}
                            columns={[
                                { accessor: 'procedure', title: t('master.bpjs_cost_page.table.procedure'), sortable: true },
                                {
                                    accessor: 'cost',
                                    title: t('master.bpjs_cost_page.table.cost'),
                                    sortable: true,
                                    render: (s: BpjsCostType) => formatCurrency(s.cost),
                                },
                                {
                                    accessor: '',
                                    title: t('common.actions'),
                                    width: 100,
                                    render: (s: BpjsCostType) => (
                                        <div className="flex items-center gap-1">
                                            <Tippy content="View">
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    onClick={() => navigate(`/bpjs-cost/update/${s.costId}`)}
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
                                                        setDeleteId(s.costId as number);
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
                            totalRecords={bpjsCostList?.data?.totalData}
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
                                            <X className="h-5 w-5" />
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
