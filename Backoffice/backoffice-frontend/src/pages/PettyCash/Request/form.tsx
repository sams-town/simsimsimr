import { useEffect, useState } from 'react';
import { useDispatch } from 'react-redux';
import { useTranslation } from 'react-i18next';
import { useNavigate, useParams } from 'react-router-dom';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '@/store/themeConfigSlice';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import SelectSearch from 'react-select';
import Flatpickr from 'react-flatpickr';
import 'flatpickr/dist/flatpickr.css';
import {
    useAddPettyCashRequestMutation,
    useUpdatePettyCashRequestMutation,
    useGetPettyCashRequestByIdQuery,
    useGetPettyCashMyUnitQuery,
} from '@/store/api/pettyCash/pettyCashApiSlice';
import { useGetOptionCOAQuery } from '@/store/api/coa/coaApiSlice';
import { useGetOptionCostCenterDetailQuery } from '@/store/api/costCenter/costCenterApiSlice';
import { toastMessage } from '@/utils/toastUtils';
import { FormatNumber } from '@/utils/formatNumber';
import { ArrowLeft, Save, Loader2, FileText } from 'lucide-react';
import IconCalendar from '@/components/Icon/IconCalendar';
import ConfirmationModal from '@/components/ConfirmationModal';

interface FormData {
    requestId: number;
    unitId: number;
    requestDate: Date;
    requesterName: string;
    purpose: string;
    amount: number;
    expenseCoaCode: string;
    costCenterCode: string;
    costCenterName: string;
    notes: string;
}

const defaultFormData: FormData = {
    requestId: 0,
    unitId: 0,
    requestDate: new Date(),
    requesterName: '',
    purpose: '',
    amount: 0,
    expenseCoaCode: '',
    costCenterCode: '',
    costCenterName: '',
    notes: '',
};

const PettyCashRequestForm = () => {
    const dispatch = useDispatch();
    const navigate = useNavigate();
    const { t } = useTranslation();
    const { id } = useParams();
    const isEdit = !!id;

    useEffect(() => {
        const title = isEdit ? 'Edit Permintaan' : 'Buat Permintaan';
        dispatch(setPageTitle(title));
        dispatch(setTitle(title));
        dispatch(setBreadcrumbTitle(['Dashboard', t('sidebar.petty_cash', 'Petty Cash'), 'Permintaan', title]));
    }, [dispatch, t, isEdit]);

    const [formData, setFormData] = useState<FormData>(defaultFormData);
    const [searchCOA, setSearchCOA] = useState('');
    const [searchCostCenter, setSearchCostCenter] = useState('');
    const [showConfirmModal, setShowConfirmModal] = useState(false);

    const selectStyles = {
        control: (base: any) => ({
            ...base,
            minHeight: '40px',
            borderColor: '#e2e8f0',
            '&:hover': { borderColor: '#cbd5e1' },
        }),
        menuPortal: (base: any) => ({ ...base, zIndex: 9999 }),
    };

    // API
    const { data: myUnitData, isLoading: isLoadingUnit, error: unitError } = useGetPettyCashMyUnitQuery(undefined, {
        refetchOnMountOrArgChange: true
    });
    const { data: coaOptions } = useGetOptionCOAQuery({ type: 'expense', keyword: searchCOA, pageSize: 100 });
    const { data: costCenterOptions } = useGetOptionCostCenterDetailQuery({ keyword: searchCostCenter, departmentId: '3', pageSize: 100 });
    const { data: requestData, isLoading: isLoadingRequest } = useGetPettyCashRequestByIdQuery(id, { skip: !isEdit });
    const [addRequest, { isLoading: isAdding }] = useAddPettyCashRequestMutation();
    const [updateRequest, { isLoading: isUpdating }] = useUpdatePettyCashRequestMutation();

    const myUnit = myUnitData?.data || {};
    const currentBalance = myUnit.currentBalance || 0;
    const newBalance = currentBalance - (formData.amount || 0);

    // Load existing data for edit
    useEffect(() => {
        if (isEdit && requestData?.data) {
            const data = requestData.data;
            setFormData({
                requestId: data.requestId,
                unitId: data.unitId,
                requestDate: new Date(data.requestDate),
                requesterName: data.requesterName || '',
                purpose: data.purpose || '',
                amount: data.amount || 0,
                expenseCoaCode: data.expenseCoaCode || '',
                costCenterCode: data.costCenterCode || '',
                costCenterName: data.costCenterName || '',
                notes: data.notes || '',
            });
        }
    }, [isEdit, requestData]);

    const coaOptionsFormatted = (((coaOptions as any)?.data as any[]) || []).map((c: any) => ({
        value: c.coaCode || c.value,
        label: c.coaName || c.label || c.coaCode || c.value,
    }));

    const costCenterOptionsFormatted = (((costCenterOptions as any)?.data as any[]) || []).map((c: any) => ({
        value: c.label, // Use Code as value for selection
        label: `${c.label} - ${c.desc || ''}`,
        name: c.desc
    }));

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();

        if (!myUnit?.unitId && !isEdit) {
            toastMessage('Anda tidak memiliki akses ke unit kas kecil', 'error');
            return;
        }

        if (!formData.requesterName.trim()) {
            toastMessage('Nama pemohon harus diisi', 'error');
            return;
        }
        if (!formData.purpose.trim()) {
            toastMessage('Keperluan harus diisi', 'error');
            return;
        }
        if (formData.amount <= 0) {
            toastMessage('Jumlah harus lebih dari 0', 'error');
            return;
        }

        // Check balance limit if available
        if (currentBalance < formData.amount) {
            toastMessage(`Saldo tidak mencukupi. Sisa saldo: ${new Intl.NumberFormat('id-ID').format(currentBalance)}`, 'error');
            return;
        }

        // if (!formData.expenseCoaCode) {
        //     toastMessage('Pilih akun biaya', 'error');
        //     return;
        // }

        setShowConfirmModal(true);
    };

    const handleConfirmSubmit = async () => {
        const d = formData.requestDate;
        const payload = {
            ...formData,
            unitId: myUnit?.unitId || 0,
            // Kirim tanggal yang dipilih sebagai UTC-midnight (tanpa geser zona),
            // supaya tidak mundur sehari (mis. 1 Juni 00:00 GMT+7 -> 31 Mei 17:00 UTC).
            requestDate: new Date(Date.UTC(d.getFullYear(), d.getMonth(), d.getDate())).toISOString(),
        };

        try {
            if (isEdit) {
                await updateRequest(payload).unwrap();
                toastMessage('Permintaan berhasil diperbarui', 'success');
            } else {
                await addRequest(payload).unwrap();
                toastMessage('Permintaan berhasil dibuat', 'success');
            }
            navigate('/petty-cash/request');
        } catch (error: any) {
            toastMessage(error?.data?.message || 'Gagal menyimpan permintaan', 'error');
        } finally {
            setShowConfirmModal(false);
        }
    };

    if (isLoadingUnit) {
        return (
            <div className="flex items-center justify-center h-64">
                <Loader2 className="h-8 w-8 animate-spin text-primary" />
            </div>
        );
    }

    if (unitError || !myUnitData) {
        return (
            <div className="p-5 text-center">
                <div className="text-red-500 mb-2">Anda tidak memiliki akses sebagai Petugas Kas Kecil.</div>
                <Button onClick={() => navigate('/petty-cash/request')}>Kembali</Button>
            </div>
        )
    }

    if (isEdit && isLoadingRequest) {
        return (
            <div className="flex items-center justify-center h-64">
                <Loader2 className="h-8 w-8 animate-spin text-primary" />
            </div>
        );
    }

    return (
        <div className="space-y-6">
            {/* Header */}
            <div className="flex items-center justify-between">
                <Button
                    variant="outline"
                    className="gap-2"
                    onClick={() => navigate('/petty-cash/request')}
                >
                    <ArrowLeft className="w-4 h-4" />
                    {t('common.back') || 'Kembali'}
                </Button>
            </div>

            {/* Unit Info Card */}
            <Card>
                <CardHeader className="border-b bg-gradient-to-r from-blue-50 to-transparent">
                    <CardTitle className="flex items-center gap-2 text-lg text-blue-700">
                        <FileText className="w-5 h-5" />
                        Informasi Unit Kas Kecil
                        <span className="ml-2 px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                            ACTIVE
                        </span>
                    </CardTitle>
                </CardHeader>
                <CardContent className="p-6">
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div>
                            <p className="text-sm text-gray-500">Kode Unit</p>
                            <p className="font-semibold text-lg">{myUnit.unitCode}</p>
                        </div>
                        <div className="lg:col-span-2">
                            <p className="text-sm text-gray-500">Nama Unit</p>
                            <p className="font-semibold">{myUnit.unitName}</p>
                        </div>
                        <div>
                            <p className="text-sm text-gray-500">Saldo Saat Ini</p>
                            <p className="font-semibold font-mono text-lg text-green-600">
                                Rp {FormatNumber(currentBalance)}
                            </p>
                        </div>
                    </div>
                </CardContent>
            </Card>

            {/* Request Form */}
            <Card className="border-primary/20 bg-primary/5">
                <CardHeader className="border-b border-primary/10">
                    <CardTitle className="flex items-center gap-2 text-lg text-primary">
                        <FileText className="w-5 h-5" />
                        {isEdit ? 'Form Edit Permintaan' : 'Form Permintaan Baru'}
                    </CardTitle>
                </CardHeader>
                <CardContent className="p-6 space-y-6">
                    {/* Row 1: Date & Requester */}
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {/* Date */}
                        <div>
                            <label className="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
                                <IconCalendar className="w-4 h-4" />
                                Tanggal Permintaan *
                            </label>
                            <Flatpickr
                                options={{ dateFormat: 'd/m/Y' }}
                                className="flex h-10 w-full items-center justify-between rounded-md border border-input bg-white px-3 py-2 text-sm"
                                value={formData.requestDate}
                                onChange={(dates) => {
                                    if (dates[0]) setFormData({ ...formData, requestDate: dates[0] });
                                }}
                            />
                        </div>

                        {/* Requester Name */}
                        <div>
                            <label className="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
                                <FileText className="w-4 h-4" />
                                Nama Pemohon *
                            </label>
                            <input
                                type="text"
                                className="flex h-10 w-full items-center justify-between rounded-md border border-input bg-white px-3 py-2 text-sm"
                                value={formData.requesterName}
                                onChange={(e) => setFormData({ ...formData, requesterName: e.target.value })}
                                placeholder="Masukkan nama pemohon"
                            />
                        </div>
                    </div>

                    {/* Row 2: Cost Center & Amount */}
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {/* Cost Center */}
                        <div>
                            <label className="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
                                <FileText className="w-4 h-4" />
                                Cost Center (Opsional)
                            </label>
                            <SelectSearch
                                options={costCenterOptionsFormatted}
                                value={costCenterOptionsFormatted.find((o) => o.value === formData.costCenterCode) || null}
                                onChange={(opt: any) => setFormData({
                                    ...formData,
                                    costCenterCode: opt?.value || '',
                                    costCenterName: opt?.name || ''
                                })}
                                placeholder="Pilih cost center..."
                                isClearable
                                onInputChange={(val) => setSearchCostCenter(val)}
                                filterOption={null}
                                styles={selectStyles}
                                menuPortalTarget={document.body}
                                isSearchable
                            />
                        </div>

                        {/* Amount */}
                        <div>
                            <label className="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
                                <FileText className="w-4 h-4" />
                                Jumlah Permintaan (Rp) *
                            </label>
                            <input
                                type="number"
                                className="flex h-10 w-full items-center justify-between rounded-md border border-input bg-white px-3 py-2 text-sm font-bold"
                                value={formData.amount || ''}
                                onChange={(e) => setFormData({ ...formData, amount: parseFloat(e.target.value) || 0 })}
                                placeholder="0"
                                min="0"
                            />
                        </div>
                    </div>

                    {/* Row 3: Purpose & Notes */}
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {/* Purpose */}
                        <div>
                            <label className="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
                                <FileText className="w-4 h-4" />
                                Keperluan *
                            </label>
                            <textarea
                                className="flex min-h-[80px] w-full rounded-md border border-input bg-white px-3 py-2 text-sm"
                                rows={3}
                                value={formData.purpose}
                                onChange={(e) => setFormData({ ...formData, purpose: e.target.value })}
                                placeholder="Jelaskan keperluan..."
                            />
                        </div>

                        {/* Notes */}
                        <div>
                            <label className="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
                                <FileText className="w-4 h-4" />
                                Catatan (Opsional)
                            </label>
                            <textarea
                                className="flex min-h-[80px] w-full rounded-md border border-input bg-white px-3 py-2 text-sm"
                                rows={3}
                                value={formData.notes}
                                onChange={(e) => setFormData({ ...formData, notes: e.target.value })}
                                placeholder="Catatan tambahan..."
                            />
                        </div>
                    </div>
                </CardContent>
            </Card>

            {/* Summary & Action */}
            <Card className="bg-gradient-to-r from-primary to-primary/80 text-white">
                <CardContent className="p-6">
                    <div className="flex justify-between items-center">
                        <div>
                            <p className="text-white/80 text-sm">Estimasi Sisa Saldo</p>
                            <p className="text-2xl font-bold font-mono mt-1">
                                Rp {FormatNumber(newBalance)}
                            </p>
                            <p className="text-xs text-white/60 mt-1">
                                (Saldo Awal: {FormatNumber(currentBalance)})
                            </p>
                        </div>
                        <div className="flex items-center gap-6">
                            <div className="text-right">
                                <p className="text-white/80 text-sm">Total Permintaan</p>
                                <p className="text-3xl font-bold font-mono">
                                    Rp {FormatNumber(formData.amount)}
                                </p>
                            </div>
                            <Button
                                onClick={handleSubmit}
                                disabled={isAdding || isUpdating}
                                className="bg-white text-primary hover:bg-gray-100 gap-2 h-12 px-6"
                            >
                                {(isAdding || isUpdating) ? (
                                    <Loader2 className="w-5 h-5 animate-spin" />
                                ) : (
                                    <Save className="w-5 h-5" />
                                )}
                                {isEdit ? 'Simpan Perubahan' : 'Simpan Permintaan'}
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <ConfirmationModal
                isOpen={showConfirmModal}
                onClose={() => setShowConfirmModal(false)}
                onConfirm={handleConfirmSubmit}
                title={isEdit ? 'Update Permintaan' : 'Buat Permintaan'}
                message={
                    <div>
                        <p>Apakah anda yakin ingin menyimpan permintaan sebesar <span className="font-bold">Rp {FormatNumber(formData.amount)}</span>?</p>
                        <p className="text-sm text-gray-500 mt-2">Pastikan data yang diinput sudah benar.</p>
                    </div>
                }
                confirmText={isAdding || isUpdating ? 'Menyimpan...' : 'Ya, Simpan'}
                cancelText="Batal"
                loading={isAdding || isUpdating}
            />
        </div>
    );
};

export default PettyCashRequestForm;
