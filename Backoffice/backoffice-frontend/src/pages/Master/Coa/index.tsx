import { useNavigate } from 'react-router-dom';
import { useDispatch, useSelector } from 'react-redux';
import { useTranslation } from 'react-i18next';
import { DataTable, DataTableSortStatus } from 'mantine-datatable';
import React, { Fragment, useEffect, useState, useRef } from 'react';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '../../../store/themeConfigSlice';
import { useGetCOAQuery, useDeleteCOAMutation, useGetOptionCOAQuery, useCOAUploadMutation, useExportCOAMutation } from '@/store/api/coa/coaApiSlice';
import { useGetRolesQuery } from '@/store/api/roles/rolesApiSlice';
import { COAType, usersType, OptionType } from '@/types';
import { responseCallback } from '@/utils/responseCallback';
import { toastMessage } from '@/utils/toastUtils';
import SelectSearch from 'react-select';
import Tippy from '@tippyjs/react';
import 'tippy.js/dist/tippy.css';
import { Dialog, Transition } from '@headlessui/react';
import IconX from '@/components/Icon/IconX';
import '@/pages/Master/Coa/index.css';

// New Imports
import { Search, Download, Plus, Eye, Trash2, FileText, Upload, Loader2, Filter, AlertTriangle, RefreshCw, SkipForward } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

const Index = () => {
    const dispatch = useDispatch();
    const { t } = useTranslation();
    useEffect(() => {
        dispatch(setPageTitle(t('master.coa.title')));
        dispatch(setTitle(t('master.coa.title')));
        dispatch(setBreadcrumbTitle(['Dashboard', 'Master', 'COA', t('master.coa.list')]));
    });
    const [exportCOA, { isLoading: isExporting }] = useExportCOAMutation();
    const [isLoadingUpload, setIsLoadingUpload] = useState<boolean>(false);
    const isRtl = useSelector((state: any) => state.themeConfig.rtlClass) === 'rtl' ? true : false;
    const [page, setPage] = useState<number>(1);
    const PAGE_SIZES: number[] = [10, 20, 30, 50, 100];
    const [pageSize, setPageSize] = useState<number>(PAGE_SIZES[0]);
    const [search, setSearch] = useState<string>('');
    const [searchType, setSearchType] = useState<string>('');
    const [status, setStatus] = useState<string>('');
    const [COALevel, setCOALevel] = useState<string>('');
    const [role, setRole] = useState<string>('');
    const [showFilter, setShowFilter] = useState<boolean>(false);
    const [sortStatus, setSortStatus] = useState<DataTableSortStatus>({ columnAccessor: 'coaCode', direction: 'asc' });
    const [debouncedSearch, setDebouncedSearch] = useState<string>('');
    const [showImportModal, setShowImportModal] = useState<boolean>(false);
    const [selectedFile, setSelectedFile] = useState<File | null>(null);
    const [importMode, setImportMode] = useState<string>('upsert');
    const {
        data: CoAList,
        refetch,
        error,
        isLoading,
    } = useGetCOAQuery<any>({
        keyword: debouncedSearch,
        orderBy: sortStatus.columnAccessor === 'coaCode' ? 'coaCode' : sortStatus.columnAccessor,
        orderType: sortStatus.direction,
        page: page,
        pageSize: pageSize,
        status,
        parent: COALevel,
    });
    const {
        data: CoAListOption,
        refetch: refetchCoaOption,
    } = useGetOptionCOAQuery<any>({
        orderBy: sortStatus.columnAccessor === 'coaCode' ? 'coaCode' : sortStatus.columnAccessor,
        orderType: sortStatus.direction,
        pageSize: 20,
        status,
        level: 4,
        accounttype: 1,
        keyword: searchType
    });

    const [CoaUpload, { isLoading: isLoadingError, isError: isErrorUpload }] = useCOAUploadMutation();


    let TypeListOption: any[] = [];
    TypeListOption.push({
        value: "",
        label: t('master.coa.table.all_account_groups'),
        level: "",
    });

    if (CoAListOption?.data) {
        CoAListOption.data.forEach((option: any) => {
            TypeListOption.push({
                value: option.value,
                label: option.label,
                level: option.level ? option.level : '',
            });
        });
    }
    const { data: rolesList, refetch: rolesListRefetch } = useGetRolesQuery({});
    const [deleted, { isError }] = useDeleteCOAMutation();
    const [showDeleteModal, setShowDeleteModal] = useState<boolean>(false);
    const [deleteId, setDeleteId] = useState<number>(0);
    const [selectedRecords, setSelectedRecords] = useState<any>([]);
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

    const handleExport = async () => {
        try {
            const type = 'xlsx';
            const keyword = search;
            const orderBy = sortStatus.columnAccessor;
            const orderType = sortStatus.direction;
            const statusValue = status;
            const accountType = '';
            const parent = COALevel;

            const res = await exportCOA({ type, keyword, orderBy, orderType, status: statusValue, accountType, parent }).unwrap();
            const url = window.URL.createObjectURL(new Blob([res as any], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' }));
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', `CoA_${new Date().toISOString()}.xlsx`);
            document.body.appendChild(link);
            link.click();
            link.parentNode?.removeChild(link);
        } catch (error: any) {
            console.error('Failed to download CoA', error);
            toastMessage(error.message, 'error');
        }
    };

    useEffect(() => {
        setTimeout(() => {
            refetchCoaOption();
            TypeListOption = [];
            TypeListOption.push({
                value: "",
                label: t('master.coa.table.all_type'),
                level: "",
            });

            if (CoAListOption?.data) {
                CoAListOption.data.forEach((option: any) => {
                    TypeListOption.push({
                        value: option.value,
                        label: option.label,
                        level: option.level ? option.level : '',
                    });
                });
            }
        }, 3000);
    }, [searchType]);

    useEffect(() => {
        refetch();
    }, [COALevel]);


    useEffect(() => {
        refetch();
        rolesListRefetch();
    }, [page]);

    useEffect(() => {
        setPage(1);
    }, [sortStatus, search, pageSize, role]);

    useEffect(() => {
        const handler = setTimeout(() => {
            setDebouncedSearch(search);
        }, 500); // delay 500ms

        return () => {
            clearTimeout(handler);
        };
    }, [search]);

    const colorStatus = (status: string) => {
        return status === 'InActive' ? 'primary' : 'success';
    };

    const fileInputRef = useRef<HTMLInputElement>(null);
    const handleUploadClick = () => {
        if (fileInputRef.current) {
            fileInputRef.current.click();
        }
    };

    const handleFileChange = async (e: any) => {
        const file = e.target.files[0];
        if (file) {
            setSelectedFile(file);
            setImportMode('upsert');
            setShowImportModal(true);
        }
        if (fileInputRef.current) {
            fileInputRef.current.value = '';
        }
    };

    const handleImportConfirm = async () => {
        if (!selectedFile) return;
        try {
            setIsLoadingUpload(true);
            setShowImportModal(false);
            const response = await CoaUpload({ file: selectedFile, importMode });
            if ('data' in response) {
                responseCallback(response, null, null);
                refetch();
            } else if ('error' in response) {
                responseCallback(response, (data: any) => {
                    navigate('/coa');
                }, null);
            }
        } catch (error: any) {
            console.log(error);
            toastMessage(error.message, 'error');
        } finally {
            setIsLoadingUpload(false);
            setSelectedFile(null);
        }
    };

    return (
        <div className='coa space-y-6'>
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
                                    <CardTitle className="text-lg">{t('master.coa.title')}</CardTitle>
                                    <p className="text-sm text-gray-500 mt-0.5">
                                        {t('master.coa.subtitle')}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {/* Right side - Actions */}
                        <div className="flex flex-col md:flex-row items-end md:items-center gap-3 ml-auto w-full md:w-auto">
                            {/* Filters Group */}
                            <div className="flex items-center gap-2 w-full md:w-auto">
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
                                <div className="w-[200px] z-20">
                                    <SelectSearch
                                        placeholder={t('master.coa.table.all_account_groups')}
                                        options={TypeListOption}
                                        className="text-sm"
                                        onInputChange={(e) => setSearchType(e)}
                                        onChange={(dt: any) => { setCOALevel(dt.value) }}
                                        styles={{
                                            control: (base) => ({
                                                ...base,
                                                borderColor: '#e2e8f0',
                                                borderRadius: '0.375rem',
                                                minHeight: '2.5rem',
                                                height: '2.5rem',
                                                boxShadow: 'none',
                                                '&:hover': {
                                                    borderColor: '#cbd5e1'
                                                }
                                            }),
                                            menu: (base) => ({ ...base, zIndex: 9999 })
                                        }}
                                    />
                                </div>
                            </div>

                            {/* Buttons Group */}
                            <div className="flex items-center gap-2">
                                {/* Upload Button */}
                                <Tippy content="Import File">
                                    <Button
                                        variant="outline"

                                        onClick={handleUploadClick}
                                        disabled={isLoadingUpload}
                                        className="gap-2"
                                    >
                                        {isLoadingUpload ? <Loader2 className="h-4 w-4 animate-spin" /> : <Upload className="h-4 w-4" />}
                                        <span className="hidden sm:inline">{t('common.import')}</span>
                                        <input
                                            type="file"
                                            className="hidden"
                                            onChange={handleFileChange}
                                            ref={fileInputRef}
                                        />
                                    </Button>
                                </Tippy>

                                {/* Export Button */}
                                <Tippy content="Download CoA">
                                    <Button
                                        variant="outline"
                                        size="icon"
                                        onClick={handleExport}
                                        disabled={isExporting}
                                    >
                                        {isExporting ? <Loader2 className="h-4 w-4 animate-spin" /> : <Download className="h-4 w-4" />}
                                    </Button>
                                </Tippy>

                                {/* Add Button */}
                                <Button onClick={() => navigate(`/coa/create`)} className="gap-2">
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
                            records={CoAList?.data?.data}
                            columns={[
                                {

                                    accessor: 'coaCode',
                                    title: t('master.coa.table.account_no'),
                                    sortable: true,
                                    render: (row: COAType, index: number) => (
                                        <span style={{ fontWeight: row.accountTypeName === "Header" ? 'bold' : 'normal' }}>
                                            {row.coaCode}
                                        </span>
                                    )
                                },
                                {

                                    accessor: 'coaName',
                                    title: t('master.coa.table.account_name'),
                                    sortable: true,
                                    render: (row: COAType, index: number) => (
                                        <div className="flex items-center gap-2">
                                            {row.accountTypeName === "Header" && <FileText className="w-4 h-4 text-primary opacity-70" />}
                                            <span style={{ fontWeight: row.accountTypeName === "Header" ? 'bold' : 'normal', paddingLeft: (row.coaLevel > 4) ? `${(row.coaLevel - 3) * 0.5}rem` : '0rem' }}>
                                                {row.coaName}
                                            </span>
                                        </div>
                                    )
                                },
                                {

                                    accessor: 'accountGroup',
                                    title: t('master.coa.table.account_group'),
                                    sortable: true,
                                    render: (row: COAType) => (
                                        <span>{row.accountGroupName}</span>
                                    )
                                },
                                {

                                    accessor: 'accountType',
                                    title: t('master.coa.table.account_type'),
                                    sortable: true,
                                    render: (row: COAType) => (
                                        <span className={`px-2 py-1 rounded text-xs font-semibold ${row.accountTypeName === "Detail" ? 'bg-success/10 text-success' : 'bg-primary/10 text-primary'}`}>
                                            {row.accountTypeName}
                                        </span>
                                    )
                                },
                                {
                                    accessor: 'normalPosition',
                                    title: t('master.coa.table.normal_position'),
                                    render: (row: COAType) => {
                                        const isCredit = ['C', 'CREDIT', 'K', 'KREDIT'].includes((row.normalPosition || '').trim().toUpperCase());
                                        return (
                                            <span className={`px-2 py-1 rounded text-xs font-semibold ${isCredit ? 'bg-primary/10 text-primary' : 'bg-warning/10 text-warning'}`}>
                                                {isCredit ? t('master.coa.form.normal_credit') : t('master.coa.form.normal_debit')}
                                            </span>
                                        );
                                    }
                                },
                                {

                                    accessor: '',
                                    title: t('common.action'),
                                    render: (s: COAType) => (
                                        <div className="flex items-center gap-1">
                                            <Tippy content="View">
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    onClick={() => navigate(`/CoA/update/${s.coaId}`)}
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
                                                        setDeleteId(s.coaId as number);
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
                            horizontalSpacing={`xs`}
                            verticalSpacing={`xs`}
                            totalRecords={CoAList?.data?.totalData}
                            rowStyle={{ padding: '0 8px' }}
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
                                    fontWeight: 'bold',
                                    textTransform: 'uppercase',
                                    letterSpacing: '0.05em',
                                    fontSize: '12px',
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

            {/* Import Confirmation Modal */}
            <div className="mb-5">
                <Transition appear show={showImportModal} as={Fragment}>
                    <Dialog as="div" open={showImportModal} onClose={() => setShowImportModal(false)}>
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
                        <div id="import_modal" className="fixed inset-0 bg-[black]/60 z-[999] overflow-y-auto">
                            <div className="flex items-start justify-center min-h-screen px-4">
                                <Dialog.Panel className="panel border-0 p-0 rounded-lg overflow-hidden w-full max-w-md my-8 text-black dark:text-white-dark animate__animated animate__fadeIn">
                                    <div className="flex bg-[#fbfbfb] dark:bg-[#121c2c] items-center justify-between px-5 py-3">
                                        <h5 className="font-bold text-lg flex items-center gap-2">
                                            <Upload className="w-5 h-5 text-primary" />
                                            Import COA
                                        </h5>
                                        <button onClick={() => setShowImportModal(false)} type="button" className="text-white-dark hover:text-dark">
                                            <IconX />
                                        </button>
                                    </div>
                                    <div className="p-5 space-y-5">
                                        {/* File Info */}
                                        <div className="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-3 flex items-center gap-3">
                                            <FileText className="w-8 h-8 text-primary shrink-0" />
                                            <div className="min-w-0">
                                                <p className="text-sm font-medium truncate">{selectedFile?.name}</p>
                                                <p className="text-xs text-gray-500">
                                                    {selectedFile ? `${(selectedFile.size / 1024).toFixed(1)} KB` : ''}
                                                </p>
                                            </div>
                                        </div>

                                        {/* Import Mode Selection */}
                                        <div>
                                            <p className="text-sm font-semibold mb-3">Pilih Mode Import:</p>
                                            <div className="space-y-2">
                                                {/* Mode 1: Truncate */}
                                                <label
                                                    className={`flex items-start gap-3 p-3 rounded-lg border-2 cursor-pointer transition-all ${importMode === 'truncate'
                                                            ? 'border-danger bg-danger/5'
                                                            : 'border-gray-200 dark:border-gray-700 hover:border-gray-300'
                                                        }`}
                                                >
                                                    <input
                                                        type="radio"
                                                        name="importMode"
                                                        value="truncate"
                                                        checked={importMode === 'truncate'}
                                                        onChange={(e) => setImportMode(e.target.value)}
                                                        className="mt-1 text-danger"
                                                    />
                                                    <div>
                                                        <div className="flex items-center gap-2">
                                                            <AlertTriangle className="w-4 h-4 text-danger" />
                                                            <span className="font-medium text-sm">Truncate + Insert</span>
                                                        </div>
                                                        <p className="text-xs text-gray-500 mt-1">
                                                            Hapus semua data COA yang ada, lalu insert data baru dari file.
                                                        </p>
                                                    </div>
                                                </label>

                                                {/* Mode 2: Skip */}
                                                <label
                                                    className={`flex items-start gap-3 p-3 rounded-lg border-2 cursor-pointer transition-all ${importMode === 'skip'
                                                            ? 'border-info bg-info/5'
                                                            : 'border-gray-200 dark:border-gray-700 hover:border-gray-300'
                                                        }`}
                                                >
                                                    <input
                                                        type="radio"
                                                        name="importMode"
                                                        value="skip"
                                                        checked={importMode === 'skip'}
                                                        onChange={(e) => setImportMode(e.target.value)}
                                                        className="mt-1 text-info"
                                                    />
                                                    <div>
                                                        <div className="flex items-center gap-2">
                                                            <SkipForward className="w-4 h-4 text-info" />
                                                            <span className="font-medium text-sm">Insert Baru & Skip Existing</span>
                                                        </div>
                                                        <p className="text-xs text-gray-500 mt-1">
                                                            Hanya tambahkan COA baru, lewati yang sudah ada.
                                                        </p>
                                                    </div>
                                                </label>

                                                {/* Mode 3: Upsert */}
                                                <label
                                                    className={`flex items-start gap-3 p-3 rounded-lg border-2 cursor-pointer transition-all ${importMode === 'upsert'
                                                            ? 'border-success bg-success/5'
                                                            : 'border-gray-200 dark:border-gray-700 hover:border-gray-300'
                                                        }`}
                                                >
                                                    <input
                                                        type="radio"
                                                        name="importMode"
                                                        value="upsert"
                                                        checked={importMode === 'upsert'}
                                                        onChange={(e) => setImportMode(e.target.value)}
                                                        className="mt-1 text-success"
                                                    />
                                                    <div>
                                                        <div className="flex items-center gap-2">
                                                            <RefreshCw className="w-4 h-4 text-success" />
                                                            <span className="font-medium text-sm">Insert Baru & Update Existing</span>
                                                        </div>
                                                        <p className="text-xs text-gray-500 mt-1">
                                                            Tambahkan COA baru dan update data COA yang sudah ada.
                                                        </p>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>

                                        {/* Warning for Truncate */}
                                        {importMode === 'truncate' && (
                                            <div className="bg-danger/10 border border-danger/20 rounded-lg p-3 flex items-start gap-2">
                                                <AlertTriangle className="w-4 h-4 text-danger mt-0.5 shrink-0" />
                                                <p className="text-xs text-danger">
                                                    <strong>Perhatian!</strong> Mode ini akan menghapus SEMUA data COA yang ada sebelum import.
                                                    Pastikan file berisi data COA yang lengkap.
                                                </p>
                                            </div>
                                        )}

                                        {/* Actions */}
                                        <div className="flex justify-end items-center gap-3 pt-2">
                                            <button onClick={() => setShowImportModal(false)} type="button" className="btn btn-outline-dark">
                                                Batal
                                            </button>
                                            <button onClick={handleImportConfirm} type="button" className="btn btn-primary">
                                                <Upload className="w-4 h-4 mr-2" />
                                                Import
                                            </button>
                                        </div>
                                    </div>
                                </Dialog.Panel>
                            </div>
                        </div>
                    </Dialog>
                </Transition>
            </div>

            {/* Loading Overlay */}
            {isLoadingUpload && (
                <div className="fixed inset-0 bg-black/50 z-[9999] flex items-center justify-center">
                    <div className="bg-white dark:bg-gray-800 rounded-xl p-8 flex flex-col items-center gap-4 shadow-2xl">
                        <Loader2 className="w-10 h-10 text-primary animate-spin" />
                        <div className="text-center">
                            <p className="font-semibold text-gray-800 dark:text-white">Mengimport data COA...</p>
                            <p className="text-sm text-gray-500 mt-1">Mohon tunggu, proses ini mungkin memakan waktu.</p>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default Index;
