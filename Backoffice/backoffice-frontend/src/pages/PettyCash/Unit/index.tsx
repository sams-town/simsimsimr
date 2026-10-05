import { useEffect, useState } from 'react';
import { useDispatch } from 'react-redux';
import { useTranslation } from 'react-i18next';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '@/store/themeConfigSlice';
import { DataTable, DataTableColumn } from 'mantine-datatable';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import {
    useGetPettyCashUnitsQuery,
    useAddPettyCashUnitMutation,
    useUpdatePettyCashUnitMutation,
    useDeletePettyCashUnitMutation,
} from '@/store/api/pettyCash/pettyCashApiSlice';
import { useGetOptionCOAQuery } from '@/store/api/coa/coaApiSlice';
import { useGetUsersQuery } from '@/store/api/users/usersApiSlice';
import { FormatNumber } from '@/utils/formatNumber';
import { FormatDate } from '@/utils/formatDate';
import { toastMessage } from '@/utils/toastUtils';
import { Building2, Plus, Edit, Trash2, Search, Loader2 } from 'lucide-react';
import ConfirmationModal from '@/components/ConfirmationModal';
import SelectSearch from 'react-select';
import { useDebounce } from '@/hooks/useDebounce';

interface AssignedUser {
    userId: number;
    displayName: string | null;
}

interface UnitData {
    unitId: number;
    unitCode: string;
    unitName: string;
    coaCode: string;
    coaName: string;
    assignedUsers: AssignedUser[];
    maxLimitPerTransaction: number | null;
    maxLimitPerPeriod: number | null;
    periodType: string;
    currentBalance: number;
    isActive: boolean;
    createdDate: string;
}

interface FormData {
    unitId: number;
    unitCode: string;
    unitName: string;
    coaCode: string;
    assignedUserIds: number[];
    maxLimitPerTransaction: number | null;
    maxLimitPerPeriod: number | null;
    periodType: string;
    currentBalance: number;
    isActive: boolean;
}

const defaultFormData: FormData = {
    unitId: 0,
    unitCode: '',
    unitName: '',
    coaCode: '',
    assignedUserIds: [],
    maxLimitPerTransaction: null,
    maxLimitPerPeriod: null,
    periodType: 'Monthly',
    currentBalance: 0,
    isActive: true,
};

const PettyCashUnit = () => {
    const dispatch = useDispatch();
    const { t } = useTranslation();

    useEffect(() => {
        dispatch(setPageTitle(t('petty_cash.unit.title', 'Unit Kas Kecil')));
        dispatch(setTitle(t('petty_cash.unit.title', 'Unit Kas Kecil')));
        dispatch(setBreadcrumbTitle(['Dashboard', t('sidebar.petty_cash', 'Petty Cash'), t('petty_cash.unit.title', 'Unit Kas Kecil')]));
    }, [dispatch, t]);

    // States
    const [keyword, setKeyword] = useState('');
    const [page, setPage] = useState(1);
    const [pageSize, setPageSize] = useState(10);
    const PAGE_SIZES = [10, 20, 30, 50];

    // Modal states
    const [showFormModal, setShowFormModal] = useState(false);
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [selectedUnit, setSelectedUnit] = useState<UnitData | null>(null);
    const [formData, setFormData] = useState<FormData>(defaultFormData);

    // COA Search
    const [coaSearch, setCoaSearch] = useState('');
    const [debouncedCoaSearch] = useDebounce(coaSearch, 500);

    // API
    const { data: listData, isLoading, refetch } = useGetPettyCashUnitsQuery({ keyword, page, pageSize });
    const { data: coaOptions } = useGetOptionCOAQuery({ startswith: '101004', keyword: debouncedCoaSearch });
    // Load ALL active users at once (page: -1) so the Petugas dropdown shows options without searching.
    const { data: usersData } = useGetUsersQuery({ page: -1, status: 'Active' });

    const [addUnit, { isLoading: isAdding }] = useAddPettyCashUnitMutation();
    const [updateUnit, { isLoading: isUpdating }] = useUpdatePettyCashUnitMutation();
    const [deleteUnit, { isLoading: isDeleting }] = useDeletePettyCashUnitMutation();

    const records: UnitData[] = listData?.data?.data || [];
    const totalRecords = listData?.data?.totalData || 0;

    const coaOptionsFormatted = (((coaOptions as any)?.data as any[]) || []).map((c: any) => ({
        value: c.coaCode || c.value,
        label: c.coaName || c.label,
    }));

    const userOptions = (usersData?.data?.data || []).map((u: any) => ({
        value: u.userID || u.userId,
        label: `${u.displayName} (${u.email})`,
    }));

    const periodOptions = [
        { value: 'Daily', label: 'Harian' },
        { value: 'Weekly', label: 'Mingguan' },
        { value: 'Monthly', label: 'Bulanan' },
        { value: 'Yearly', label: 'Tahunan' },
    ];

    const handleAdd = () => {
        setFormData(defaultFormData);
        setSelectedUnit(null);
        setShowFormModal(true);
    };

    const handleEdit = (unit: UnitData) => {
        setFormData({
            unitId: unit.unitId,
            unitCode: unit.unitCode,
            unitName: unit.unitName,
            coaCode: unit.coaCode,
            assignedUserIds: (unit.assignedUsers || []).map(u => u.userId),
            maxLimitPerTransaction: unit.maxLimitPerTransaction,
            maxLimitPerPeriod: unit.maxLimitPerPeriod,
            periodType: unit.periodType || 'Monthly',
            currentBalance: unit.currentBalance,
            isActive: unit.isActive,
        });
        setSelectedUnit(unit);
        setShowFormModal(true);
    };

    const handleDelete = (unit: UnitData) => {
        setSelectedUnit(unit);
        setShowDeleteModal(true);
    };

    const handleSubmit = async () => {
        if (!formData.unitCode || !formData.unitName || !formData.coaCode) {
            toastMessage('Mohon lengkapi data yang diperlukan', 'error');
            return;
        }

        try {
            const payload = { ...formData };
            if (formData.unitId === 0) {
                delete (payload as any).unitId;
                await addUnit(payload).unwrap();
                toastMessage('Unit berhasil ditambahkan', 'success');
            } else {
                await updateUnit(payload).unwrap();
                toastMessage('Unit berhasil diperbarui', 'success');
            }
            setShowFormModal(false);
            refetch();
        } catch (error: any) {
            toastMessage(error?.data?.message || 'Gagal menyimpan data', 'error');
        }
    };

    const handleConfirmDelete = async () => {
        if (!selectedUnit) return;
        try {
            await deleteUnit(selectedUnit.unitId).unwrap();
            toastMessage('Unit berhasil dihapus', 'success');
            setShowDeleteModal(false);
            refetch();
        } catch (error: any) {
            toastMessage(error?.data?.message || 'Gagal menghapus unit', 'error');
        }
    };

    const columns: DataTableColumn<UnitData>[] = [
        {
            accessor: 'unitCode',
            title: 'Kode Unit',
            width: 120,
        },
        {
            accessor: 'unitName',
            title: 'Nama Unit',
        },
        {
            accessor: 'coaCode',
            title: 'Akun',
            render: (row) => (
                <div className="flex flex-col">
                    <span className="font-medium">{row.coaCode}</span>
                    <span className="text-xs text-gray-500">{row.coaName}</span>
                </div>
            ),
        },
        {
            accessor: 'currentBalance',
            title: 'Saldo',
            textAlignment: 'right' as const,
            render: (row) => (
                <span className={`font-mono ${row.currentBalance < 0 ? 'text-red-600' : 'text-green-600'}`}>
                    {FormatNumber(row.currentBalance)}
                </span>
            ),
        },
        {
            accessor: 'maxLimitPerTransaction',
            title: 'Limit/Transaksi',
            textAlignment: 'right' as const,
            render: (row) => row.maxLimitPerTransaction ? FormatNumber(row.maxLimitPerTransaction) : '-',
        },
        {
            accessor: 'assignedUsers',
            title: 'Petugas',
            render: (row) => {
                const users = row.assignedUsers || [];
                if (users.length === 0) return <span className="text-gray-400 text-xs">-</span>;
                return (
                    <div className="flex flex-wrap gap-1">
                        {users.slice(0, 2).map(u => (
                            <span key={u.userId} className="px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                {u.displayName || `User #${u.userId}`}
                            </span>
                        ))}
                        {users.length > 2 && (
                            <span className="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600" title={users.slice(2).map(u => u.displayName).join(', ')}>
                                +{users.length - 2}
                            </span>
                        )}
                    </div>
                );
            },
        },
        {
            accessor: 'isActive',
            title: 'Status',
            render: (row) => (
                <span className={`px-2 py-1 rounded-full text-xs font-medium ${row.isActive ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'}`}>
                    {row.isActive ? 'Aktif' : 'Nonaktif'}
                </span>
            ),
        },
        {
            accessor: 'actions',
            title: 'Aksi',
            width: 100,
            render: (row) => (
                <div className="flex items-center gap-2">
                    <Button variant="ghost" size="sm" onClick={() => handleEdit(row)}>
                        <Edit className="h-4 w-4" />
                    </Button>
                    <Button variant="ghost" size="sm" className="text-red-600" onClick={() => handleDelete(row)}>
                        <Trash2 className="h-4 w-4" />
                    </Button>
                </div>
            ),
        },
    ];

    return (
        <div className="space-y-6">
            <Card>
                <CardHeader className="border-b border-gray-100 pb-4">
                    <div className="flex items-center justify-between gap-4">
                        <div className="flex items-center gap-3">
                            <div className="p-2 rounded-lg bg-primary/10">
                                <Building2 className="h-5 w-5 text-primary" />
                            </div>
                            <div>
                                <CardTitle className="text-lg">{t('petty_cash.unit.title', 'Unit Kas Kecil')}</CardTitle>
                                <p className="text-sm text-gray-500 mt-0.5">Kelola unit kas kecil perusahaan</p>
                            </div>
                        </div>

                        <div className="flex items-center gap-3">
                            <div className="relative w-64">
                                <input
                                    type="text"
                                    placeholder="Cari unit..."
                                    className="form-input w-full h-10 pl-9 border-slate-200"
                                    value={keyword}
                                    onChange={(e) => { setKeyword(e.target.value); setPage(1); }}
                                />
                                <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <Search className="h-4 w-4 text-gray-400" />
                                </div>
                            </div>
                            <Button className="gap-2" onClick={handleAdd}>
                                <Plus className="h-4 w-4" />
                                Tambah Unit
                            </Button>
                        </div>
                    </div>
                </CardHeader>

                <CardContent className="p-0">
                    <div className="datatables">
                        <DataTable
                            highlightOnHover
                            className="whitespace-nowrap table-hover"
                            records={records}
                            columns={columns}
                            totalRecords={totalRecords}
                            recordsPerPage={pageSize}
                            page={page}
                            onPageChange={setPage}
                            recordsPerPageOptions={PAGE_SIZES}
                            onRecordsPerPageChange={setPageSize}
                            minHeight={300}
                            fetching={isLoading}
                            loaderVariant="dots"
                            loaderColor="#4361ee"
                            noRecordsText="Tidak ada data"
                            paginationText={({ from, to, totalRecords }) =>
                                `Menampilkan ${from} - ${to} dari ${totalRecords} data`
                            }
                            styles={{
                                root: { border: 'none', borderRadius: '0.75rem', overflow: 'hidden' },
                                pagination: { borderTop: '1px solid #f1f5f9', margin: '1rem' },
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

            {/* Form Modal */}
            {showFormModal && (
                <div className="fixed inset-0 z-50 flex items-center justify-center">
                    <div className="fixed inset-0 bg-black/50" onClick={() => setShowFormModal(false)} />
                    <div className="relative bg-white rounded-lg shadow-xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
                        <div className="p-6 border-b">
                            <h2 className="text-lg font-semibold">
                                {formData.unitId > 0 ? 'Edit Unit' : 'Tambah Unit Baru'}
                            </h2>
                        </div>
                        <div className="p-6 space-y-4">
                            <div>
                                <label className="block text-sm font-medium mb-1">Kode Unit *</label>
                                <input
                                    type="text"
                                    className="form-input w-full"
                                    value={formData.unitCode}
                                    onChange={(e) => setFormData({ ...formData, unitCode: e.target.value.toUpperCase() })}
                                    placeholder="contoh: PC-001"
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-medium mb-1">Nama Unit *</label>
                                <input
                                    type="text"
                                    className="form-input w-full"
                                    value={formData.unitName}
                                    onChange={(e) => setFormData({ ...formData, unitName: e.target.value })}
                                    placeholder="contoh: Kas Kecil Operasional"
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-medium mb-1">Akun (COA) *</label>
                                <SelectSearch
                                    options={coaOptionsFormatted}
                                    value={coaOptionsFormatted.find((o: any) => o.value === formData.coaCode) || null}
                                    onChange={(opt: any) => setFormData({ ...formData, coaCode: opt?.value || '' })}
                                    onInputChange={(value) => setCoaSearch(value)}
                                    placeholder="Pilih akun..."
                                    isClearable
                                    isSearchable
                                    menuPortalTarget={document.body}
                                    styles={{ menuPortal: (base) => ({ ...base, zIndex: 9999 }) }}
                                />
                            </div>

                            {/* Assigned Users Field */}
                            <div>
                                <label className="block text-sm font-medium mb-1">Petugas (Assigned Users)</label>
                                <SelectSearch
                                    options={userOptions}
                                    value={userOptions.filter((o: any) => formData.assignedUserIds.includes(o.value))}
                                    onChange={(opts: any) => setFormData({ ...formData, assignedUserIds: (opts || []).map((o: any) => o.value) })}
                                    placeholder="Pilih user..."
                                    isClearable
                                    isSearchable
                                    isMulti
                                    menuPortalTarget={document.body}
                                    styles={{ menuPortal: (base) => ({ ...base, zIndex: 9999 }) }}
                                />
                            </div>

                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-sm font-medium mb-1">Limit per Transaksi</label>
                                    <input
                                        type="number"
                                        className="form-input w-full"
                                        value={formData.maxLimitPerTransaction || ''}
                                        onChange={(e) => setFormData({ ...formData, maxLimitPerTransaction: e.target.value ? parseFloat(e.target.value) : null })}
                                        placeholder="0"
                                    />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium mb-1">Limit per Periode</label>
                                    <input
                                        type="number"
                                        className="form-input w-full"
                                        value={formData.maxLimitPerPeriod || ''}
                                        onChange={(e) => setFormData({ ...formData, maxLimitPerPeriod: e.target.value ? parseFloat(e.target.value) : null })}
                                        placeholder="0"
                                    />
                                </div>
                            </div>
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-sm font-medium mb-1">Tipe Periode</label>
                                    <SelectSearch
                                        options={periodOptions}
                                        value={periodOptions.find(o => o.value === formData.periodType) || null}
                                        onChange={(opt: any) => setFormData({ ...formData, periodType: opt?.value || 'Monthly' })}
                                    />
                                </div>
                            </div>
                            <div className="flex items-center gap-2">
                                <input
                                    type="checkbox"
                                    id="isActive"
                                    className="form-checkbox"
                                    checked={formData.isActive}
                                    onChange={(e) => setFormData({ ...formData, isActive: e.target.checked })}
                                />
                                <label htmlFor="isActive" className="text-sm">Aktif</label>
                            </div>
                        </div>
                        <div className="p-6 border-t flex justify-end gap-3">
                            <Button variant="outline" onClick={() => setShowFormModal(false)}>
                                Batal
                            </Button>
                            <Button onClick={handleSubmit} disabled={isAdding || isUpdating}>
                                {(isAdding || isUpdating) && <Loader2 className="h-4 w-4 mr-2 animate-spin" />}
                                {formData.unitId > 0 ? 'Simpan Perubahan' : 'Tambah Unit'}
                            </Button>
                        </div>
                    </div>
                </div>
            )}

            {/* Delete Modal */}
            <ConfirmationModal
                isOpen={showDeleteModal}
                onClose={() => setShowDeleteModal(false)}
                onConfirm={handleConfirmDelete}
                title="Hapus Unit"
                message={`Apakah Anda yakin ingin menghapus unit "${selectedUnit?.unitName}"?`}
                confirmText="Hapus"
                loading={isDeleting}
                variant="danger"
            />
        </div>
    );
};

export default PettyCashUnit;
