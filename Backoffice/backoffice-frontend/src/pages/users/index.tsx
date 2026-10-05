import { useDispatch, useSelector } from 'react-redux';
import { DataTable, DataTableSortStatus } from 'mantine-datatable';
import { Fragment, useEffect, useState } from 'react';
import { useTranslation } from 'react-i18next';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '../../store/themeConfigSlice';
import { useDeleteUsersMutation, useGetUsersQuery, useExportUsersMutation } from '@/store/api/users/usersApiSlice';
import { responseCallback } from '@/utils/responseCallback';
import { toastMessage } from '@/utils/toastUtils';
import Tippy from '@tippyjs/react';
import 'tippy.js/dist/tippy.css';
import { Dialog, Transition } from '@headlessui/react';
import { useNavigate } from 'react-router-dom';
import { usersType } from '@/types';
import { useGetRolesQuery } from '@/store/api/roles/rolesApiSlice';
import { rolesType } from '@/types/rolesType';
import ConfirmationModal from '@/components/ConfirmationModal';

// New Imports
import { Search, Download, Plus, Eye, Trash2, Users, Filter, X, Loader2 } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';


const Index = () => {
    const dispatch = useDispatch();
    const { t } = useTranslation();
    useEffect(() => {
        dispatch(setPageTitle(t('users_page.title')));
        dispatch(setTitle(t('users_page.title')));
        dispatch(setBreadcrumbTitle(['Dashboard', t('users_page.title'), t('users_page.list')]))
    });
    const isRtl = useSelector((state: any) => state.themeConfig.rtlClass) === 'rtl' ? true : false;
    const [page, setPage] = useState<number>(1);
    const PAGE_SIZES: number[] = [10, 20, 30, 50, 100];
    const [pageSize, setPageSize] = useState<number>(PAGE_SIZES[0]);
    const [search, setSearch] = useState<string>('');
    const [status, setStatus] = useState<string>('');
    const [role, setRole] = useState<string>('');
    const [showFilter, setShowFilter] = useState<boolean>(false);
    const [sortStatus, setSortStatus] = useState<DataTableSortStatus>({ columnAccessor: 'id', direction: 'asc' });
    const [debouncedSearch, setDebouncedSearch] = useState<string>('');
    const {
        data: usersList,
        refetch,
        error,
        isLoading,
    } = useGetUsersQuery({
        keyword: debouncedSearch,
        page: page,
        role: role,
        pageSize: pageSize,
        orderBy: sortStatus.columnAccessor === 'email' ? 'email' : sortStatus.columnAccessor,
        orderType: sortStatus.direction,
        status,
    });
    const { data: rolesList, refetch: rolesListRefetch } = useGetRolesQuery({});
    const [exportUsers, { isLoading: isExporting }] = useExportUsersMutation();
    const [deleted, { isLoading: isDeleting, isError }] = useDeleteUsersMutation();
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

    const handleExport = async () => {
        try {
            const type = 'xlsx';
            const keyword = search;
            const orderBy = sortStatus.columnAccessor;
            const orderType = sortStatus.direction;
            const statusValue = status;
            const roleValue = role;

            const res = await exportUsers({ type, keyword, orderBy, orderType, status: statusValue, role: roleValue }).unwrap();
            const url = window.URL.createObjectURL(new Blob([res], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' }));
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', `Users_${new Date().toISOString()}.xlsx`);
            document.body.appendChild(link);
            link.click();
            link.parentNode?.removeChild(link);
        } catch (error: any) {
            console.error('Failed to download users', error);
            toastMessage(error.message, 'error');
        }
    };

    useEffect(() => {
        refetch();
        rolesListRefetch();
    }, [page]);

    useEffect(() => {
        setPage(1);
    }, [sortStatus, search, pageSize, role]);

    const colorStatus = (status: string) => {
        return status === 'InActive' ? 'primary' : 'success';
    };

    useEffect(() => {
        const handler = setTimeout(() => {
            setDebouncedSearch(search);
        }, 500); // delay 500ms

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
                                    <CardTitle className="text-lg">{t('users_page.title')}</CardTitle>
                                    <p className="text-sm text-gray-500 mt-0.5">
                                        {t('users_page.subtitle')}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {/* Right side - Actions */}
                        <div className="flex flex-col md:flex-row items-end md:items-center gap-3 ml-auto w-full md:w-auto">
                            {/* Filter Role */}
                            <div className="w-[150px]">
                                <select
                                    className="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                    onChange={(e) => setRole(e.target.value)}
                                >
                                    <option value={''}>{t('users_page.filter_role')}</option>
                                    {rolesList?.data?.data?.map((d: rolesType, i: number) => {
                                        return <option key={i} value={d.roleID}>{d.roleName}</option>;
                                    })}
                                </select>
                            </div>

                            {/* Filter Status */}
                            <div className="w-[150px]">
                                <select
                                    className="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                    onChange={(e) => setStatus(e.target.value)}
                                >
                                    <option value={''}>{t('users_page.filter_status')}</option>
                                    <option value={'active'}>{t('users_page.active')}</option>
                                    <option value={'inactive'}>{t('users_page.inactive')}</option>
                                </select>
                            </div>

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

                                <Button onClick={() => navigate(`/users/create`)} className="gap-2">
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
                            records={usersList?.data?.data}
                            columns={[
                                {
                                    accessor: 'displayName',
                                    title: t('users_page.table.name'),
                                    sortable: true,
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (s: usersType) => (
                                        <div className="flex items-center gap-2">
                                            <div className="avatar w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary font-semibold text-xs">
                                                {s.displayName?.slice(0, 2).toUpperCase()}
                                            </div>
                                            <span className="font-medium">{s.displayName}</span>
                                        </div>
                                    )
                                },
                                {
                                    accessor: 'email',
                                    title: t('users_page.table.email'),
                                    sortable: true,
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                },
                                {
                                    accessor: 'roleName',
                                    title: t('users_page.table.role'),
                                    sortable: true,
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (s: usersType) => (
                                        <span className="font-medium text-gray-600">{s.roleName}</span>
                                    )
                                },
                                {
                                    accessor: 'status',
                                    title: t('users_page.table.status'),
                                    sortable: true,
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (s: usersType) => (
                                        <span className={`px-2 py-1 rounded-full text-xs font-semibold ${s.status === 'Active'
                                            ? 'bg-success/10 text-success'
                                            : 'bg-gray-100 text-gray-500'
                                            }`}>
                                            {s.status === 'Active' ? t('users_page.active') : t('users_page.inactive')}
                                        </span>
                                    ),
                                },
                                {
                                    accessor: '',
                                    title: t('common.actions'),
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (s: usersType) => (
                                        <div className="flex items-center gap-1">
                                            <Tippy content="View">
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    onClick={() => navigate(`/users/update/${s.userID}`)}
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
                                                        setDeleteId(s.userID as number);
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
                            totalRecords={usersList?.data?.totalData}
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
                <ConfirmationModal
                    isOpen={showDeleteModal}
                    onClose={() => (!isDeleting) && setShowDeleteModal(false)}
                    onConfirm={handleDelete}
                    title={t('messages.confirmation')}
                    message={t('messages.delete_warning')}
                    confirmText={t('common.delete')}
                    cancelText={t('common.cancel')}
                    variant="danger"
                    loading={isDeleting}
                />
            </div>
        </div >
    );
};

export default Index;
