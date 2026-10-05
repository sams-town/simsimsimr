import React, { Fragment, useEffect, useState, useRef } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { useNavigate } from 'react-router-dom';
import { DataTable, DataTableSortStatus } from 'mantine-datatable';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '../../../store/themeConfigSlice';
import { useGetCOAQuery, useDeleteCOAMutation, useGetOptionCOAQuery, useDownloadCoaMutation, useCOAUploadMutation, useExportCOAMutation } from '@/store/api/coa/coaApiSlice';
import { useTranslation } from 'react-i18next';
import { responseCallback } from '@/utils/responseCallback';
import { toastMessage } from '@/utils/toastUtils';
import { COAType } from '@/types';
import Tippy from '@tippyjs/react';
import 'tippy.js/dist/tippy.css';
import moment from "moment";
import { Dialog, Transition } from '@headlessui/react';
import './index.css';

// UI Components & Icons
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Search, Eye, Trash2, X, FileText, Upload, Download, Plus } from 'lucide-react';

const Index = () => {
    const dispatch = useDispatch();
    const navigate = useNavigate();
    const { t } = useTranslation();

    useEffect(() => {
        dispatch(setPageTitle(t('buku_kas.list_page.title')));
        dispatch(setTitle(t('buku_kas.list_page.title')));
        dispatch(setBreadcrumbTitle([t('menu.dashboard'), t('menu.buku_kas'), t('buku_kas.list_page.title'), t('common.list')]));
    }, [dispatch, t]);

    const isRtl = useSelector((state: any) => state.themeConfig.rtlClass) === 'rtl';
    const [page, setPage] = useState<number>(1);
    const PAGE_SIZES: number[] = [10, 20, 30, 50, 100];
    const [pageSize, setPageSize] = useState<number>(PAGE_SIZES[0]);
    const [search, setSearch] = useState<string>('');
    const [status, setStatus] = useState<string>('');
    const [COALevel, setCOALevel] = useState<string>('');
    const [sortStatus, setSortStatus] = useState<DataTableSortStatus>({ columnAccessor: 'coaCode', direction: 'asc' });

    const {
        data: CoAList,
        refetch,
        isLoading,
    } = useGetCOAQuery<any>({
        keyword: search,
        orderBy: sortStatus.columnAccessor === 'coaCode' ? 'coaCode' : sortStatus.columnAccessor,
        orderType: sortStatus.direction,
        page: page,
        pageSize: pageSize,
        status,
        parent: COALevel,
        iscashbank: true,
    });

    const [CoaUpload, { isLoading: isLoadingUpload }] = useCOAUploadMutation();
    const [downloadTemplateCOA] = useDownloadCoaMutation();
    const [exportCOA] = useExportCOAMutation();
    const [deleted] = useDeleteCOAMutation();
    const [showDeleteModal, setShowDeleteModal] = useState<boolean>(false);
    const [deleteId, setDeleteId] = useState<number>(0);

    const handleDelete = async () => {
        try {
            const response: any = await deleted(deleteId);
            setDeleteId(0);
            setShowDeleteModal(false);
            responseCallback(response, null, null);
            refetch();
        } catch (err: any) {
            toastMessage(err.message, 'error');
        }
    };

    useEffect(() => {
        refetch();
    }, [page, COALevel]);

    useEffect(() => {
        setPage(1);
    }, [sortStatus, search, pageSize]);

    const fileInputRef = useRef<HTMLInputElement>(null);
    const handleUploadClick = () => {
        if (fileInputRef.current) {
            fileInputRef.current.click();
        }
    };

    const handleFileChange = async (e: any) => {
        try {
            const file = e.target.files[0];
            const response = await CoaUpload(file);
            responseCallback(response, () => {
                navigate('/coa');
            }, null);
        } catch (error: any) {
            toastMessage(error.message, 'error');
        } finally {
            if (fileInputRef.current) {
                fileInputRef.current.value = '';
            }
        }
    };

    const handleDownload = async () => {
        try {
            const res = await downloadTemplateCOA({}).unwrap();
            const link = document.createElement('a');
            link.href = res as string;
            link.setAttribute('download', `COA_TEMPLATE_${moment().format("yyyyMMDD_Hms")}.xlsx`);
            document.body.appendChild(link);
            link.click();
            link.parentNode?.removeChild(link);
        } catch (error: any) {
            console.error('Failed to download template', error);
            toastMessage(error.message, 'error');
        }
    };

    return (
        <div className="space-y-6">
            <Card>
                <CardHeader className="border-b border-gray-100 pb-4">
                    <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        {/* Title Section */}
                        <div className="flex items-center gap-4">
                            <div className="flex items-center gap-3">
                                <div className="p-2 rounded-lg bg-primary/10">
                                    <FileText className="h-5 w-5 text-primary" />
                                </div>
                                <div>
                                    <CardTitle className="text-lg">{t('buku_kas.list_page.title')}</CardTitle>
                                    <p className="text-sm text-gray-500 mt-0.5">
                                        {t('buku_kas.list_page.subtitle')}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {/* Actions & Search */}
                        <div className="flex flex-col md:flex-row items-end md:items-center gap-3 ml-auto w-full md:w-auto">
                            <div className="relative flex-1 md:w-[250px]">
                                <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" />
                                <Input
                                    type="text"
                                    placeholder={t('buku_kas.list_page.search_placeholder')}
                                    value={search}
                                    onChange={(e) => setSearch(e.target.value)}
                                    className="pl-9"
                                />
                            </div>

                            <div className="flex items-center gap-2">
                                <Tippy content={t('buku_kas.list_page.download_excel')}>
                                    <Button
                                        variant="outline"
                                        size="icon"
                                        onClick={async () => {
                                            try {
                                                const res = await exportCOA({
                                                    keyword: search,
                                                    status,
                                                    level: COALevel,
                                                    orderBy: sortStatus.columnAccessor === 'coaCode' ? 'coaCode' : sortStatus.columnAccessor,
                                                    orderType: sortStatus.direction,
                                                    iscashbankexport: true, // Trigger special export mode
                                                    type: 'xlsx'
                                                }).unwrap();
                                                const url = window.URL.createObjectURL(new Blob([res as unknown as BlobPart]));
                                                const link = document.createElement('a');
                                                link.href = url;
                                                link.setAttribute('download', `DaftarBukuKas_${moment().format("YYYYMMDD_HHmmss")}.xlsx`);
                                                document.body.appendChild(link);
                                                link.click();
                                                link.parentNode?.removeChild(link);
                                            } catch (error: any) {
                                                console.error('Failed to export', error);
                                                toastMessage(error.message || 'Export failed', 'error');
                                            }
                                        }}
                                    >
                                        <Download className="h-4 w-4" />
                                    </Button>
                                </Tippy>
                            </div>

                            {/* Hidden Upload Input */}
                            <input
                                type="file"
                                ref={fileInputRef}
                                style={{ display: 'none' }}
                                accept=".xlsx, .xls"
                                onChange={handleFileChange}
                            />
                        </div>
                    </div>
                </CardHeader>

                <CardContent className="pt-6">
                    <div className="datatables z-0 rounded-lg border border-gray-100 overflow-hidden">
                        <DataTable
                            highlightOnHover
                            className={`${isRtl ? 'whitespace-nowrap table-hover' : 'whitespace-nowrap table-hover'}`}
                            records={CoAList?.data?.data}
                            columns={[
                                {
                                    accessor: 'coaCode',
                                    title: t('buku_kas.list_page.table.account_code'),
                                    sortable: true,
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (row: COAType) => (
                                        <div className="font-semibold text-primary">
                                            {row.coaCode}
                                        </div>
                                    )
                                },
                                {
                                    accessor: 'accountTypeName',
                                    title: t('buku_kas.list_page.table.type'),
                                    sortable: true,
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (row: COAType) => (
                                        <span className="text-gray-600 font-medium text-xs bg-gray-100 px-2 py-1 rounded">
                                            {row.accountTypeName}
                                        </span>
                                    )
                                },
                                {
                                    accessor: 'coaName',
                                    title: t('buku_kas.list_page.table.account_name'),
                                    sortable: true,
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (row: COAType) => (
                                        <div className="flex items-center">
                                            <span style={{ paddingLeft: (row.coaLevel > 4) ? `${(row.coaLevel - 4) * 0.5}rem` : '0rem' }}></span>
                                            {row.accountTypeName !== "Header" && (
                                                <span className="w-2 h-2 rounded-full bg-yellow-400 mr-2"></span>
                                            )}
                                            <span
                                                className={`text-gray-700 ${row.accountTypeName === "Header" ? 'font-bold text-gray-900' : ''}`}
                                            >
                                                {row.coaName}
                                            </span>
                                        </div>
                                    )
                                },
                                {
                                    accessor: 'balance',
                                    title: t('buku_kas.list_page.table.balance'),
                                    sortable: true,
                                    textAlignment: 'right',
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50 text-right',
                                    render: (row: COAType) => (
                                        <div className="font-montserrat text-gray-900 font-bold text-right">
                                            {row.accountTypeName === 'Header' ? '-' : row.balance?.toLocaleString('id-ID')}
                                        </div>
                                    )
                                },
                                {
                                    accessor: 'action',
                                    title: t('buku_kas.list_page.table.action'),
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (row: COAType) => (
                                        row.accountTypeName !== 'Header' ? (
                                            <div className="flex items-center justify-center">
                                                <Tippy content={t('buku_kas.list_page.view_detail')}>
                                                    <Button
                                                        variant="ghost"
                                                        size="sm"
                                                        onClick={() => navigate(`/daftar-buku-kas/detail/${row.coaCode.replace(/\./g, '-')}`)}
                                                        className="h-8 w-8 p-0 text-gray-500 hover:text-primary hover:bg-primary/10 rounded-lg"
                                                    >
                                                        <Eye className="h-4 w-4" />
                                                    </Button>
                                                </Tippy>
                                            </div>
                                        ) : (
                                            <div className="text-center text-gray-400">-</div>
                                        )
                                    )
                                },
                            ]}
                            totalRecords={CoAList?.data?.totalData}
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
                                        <X />
                                    </button>
                                </div>
                                <div className="p-5">
                                    <p>{t('messages.delete_confirm')}</p>
                                    <div className="flex justify-end items-center mt-8">
                                        <Button variant="outline" onClick={() => setShowDeleteModal(false)} className="mr-3">
                                            {t('common.cancel')}
                                        </Button>
                                        <Button variant="destructive" onClick={handleDelete}>
                                            {t('common.delete')}
                                        </Button>
                                    </div>
                                </div>
                            </Dialog.Panel>
                        </div>
                    </div>
                </Dialog>
            </Transition>
        </div >
    );
};

export default Index;
