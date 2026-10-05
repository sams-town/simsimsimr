import { useEffect, useState } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { useTranslation } from 'react-i18next';
import { useNavigate } from 'react-router-dom';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '@/store/themeConfigSlice';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import SelectSearch from 'react-select';
import Flatpickr from 'react-flatpickr';
import 'flatpickr/dist/flatpickr.css';
import {
    useAddPettyCashTopUpMutation,
    useGetPettyCashMyUnitQuery,
    useGetPettyCashTopUpDefaultCOAQuery,
} from '@/store/api/pettyCash/pettyCashApiSlice';
import { useGetOptionCOAQuery } from '@/store/api/coa/coaApiSlice';
import { FormatNumber } from '@/utils/formatNumber';
import { toastMessage } from '@/utils/toastUtils';
import ConfirmationModal from '@/components/ConfirmationModal';
import {
    ArrowLeft,
    Save,
    Loader2,
    TrendingUp,
    CreditCard,
    Calendar,
    Wallet,
    FileText,
} from 'lucide-react';

interface FormData {
    unitId: number;
    topUpDate: Date;
    amount: number;
    fromCoaCode: string;
    description: string;
    notes: string;
}

const defaultFormData: FormData = {
    unitId: 0,
    topUpDate: new Date(),
    amount: 0,
    fromCoaCode: '',
    description: '',
    notes: '',
};

const PettyCashTopUpForm = () => {
    const dispatch = useDispatch();
    const navigate = useNavigate();
    const { t } = useTranslation();
    const isRtl = useSelector((state: any) => state.themeConfig.rtlClass) === 'rtl';
    const user = useSelector((state: any) => state.auth.user);
    const roleID = user?.role?.roleID;

    useEffect(() => {
        dispatch(setPageTitle('Buat Top Up'));
        dispatch(setTitle('Buat Top Up'));
        dispatch(setBreadcrumbTitle(['Dashboard', t('sidebar.petty_cash', 'Petty Cash'), 'Top Up', 'Buat']));
    }, [dispatch, t]);



    const [formData, setFormData] = useState<FormData>(defaultFormData);
    const [searchCOA, setSearchCOA] = useState('');
    const [showConfirmModal, setShowConfirmModal] = useState(false);

    // API
    const { data: myUnitData, isLoading: isLoadingUnit, error: unitError } = useGetPettyCashMyUnitQuery(undefined, {
        refetchOnMountOrArgChange: true
    });


    // Server-side search: pass `keyword` (backend param) and `startswith` '101' as requested
    const { data: coaOptions } = useGetOptionCOAQuery({ startswith: '101', keyword: searchCOA, pageSize: 100 });
    const [addTopUp, { isLoading: isCreating }] = useAddPettyCashTopUpMutation();

    const myUnit = myUnitData?.data;
    const currentBalance = myUnit?.currentBalance || 0;
    const newBalance = currentBalance + (formData.amount || 0);

    const coaOptionsFormatted = (((coaOptions as any)?.data as any[]) || []).map((c: any) => ({
        value: c.coaCode || c.value,
        label: c.coaName || c.label || c.coaCode || c.value,
    }));

    const handleConfirm = (e: React.FormEvent) => {
        e.preventDefault();

        if (!myUnit?.unitId) {
            toastMessage('Anda tidak memiliki akses ke unit kas kecil', 'error');
            return;
        }

        if (formData.amount <= 0) {
            toastMessage('Jumlah harus lebih dari 0', 'error');
            return;
        }
        // Source COA is only required if NOT Role 5 (Petty Cash Officer)
        if (!formData.fromCoaCode && roleID !== 5) {
            toastMessage('Pilih akun sumber dana', 'error');
            return;
        }
        setShowConfirmModal(true);
    };

    const handleSave = async () => {
        const selectedCoa = coaOptionsFormatted.find(o => o.value === formData.fromCoaCode);

        const payload = {
            unitId: myUnit?.unitId,
            topUpDate: formData.topUpDate.toISOString(),
            amount: formData.amount,
            fromCoaCode: formData.fromCoaCode,
            fromCoaName: selectedCoa?.label?.split(' - ')[1] || selectedCoa?.label, // Try to strip code if format is "Code - Name"
            description: formData.description,
            notes: formData.notes,
        };

        try {
            await addTopUp(payload).unwrap();
            toastMessage('Permintaan top up berhasil dibuat', 'success');
            navigate('/petty-cash/topup');
        } catch (error: any) {
            toastMessage(error?.data?.message || 'Gagal membuat permintaan top up', 'error');
        } finally {
            setShowConfirmModal(false);
        }
    };

    const selectStyles = {
        control: (base: any) => ({
            ...base,
            minHeight: '40px',
            borderColor: '#e2e8f0',
            '&:hover': { borderColor: '#cbd5e1' },
        }),
        menuPortal: (base: any) => ({ ...base, zIndex: 9999 }),
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
                <Button onClick={() => navigate('/petty-cash/topup')}>Kembali</Button>
            </div>
        )
    }

    return (
        <div className="space-y-6">
            {/* Header */}
            <div className="flex items-center justify-between">
                <Button
                    variant="outline"
                    className="gap-2"
                    onClick={() => navigate('/petty-cash/topup')}
                >
                    <ArrowLeft className="w-4 h-4" />
                    {t('common.back') || 'Kembali'}
                </Button>
            </div>

            {/* Unit Info Card */}
            <Card>
                <CardHeader className="border-b bg-gradient-to-r from-green-50 to-transparent">
                    <CardTitle className="flex items-center gap-2 text-lg text-green-700">
                        <Wallet className="w-5 h-5" />
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

            {/* Top Up Form */}
            <Card className="border-primary/20 bg-primary/5">
                <CardHeader className="border-b border-primary/10">
                    <CardTitle className="flex items-center gap-2 text-lg text-primary">
                        <TrendingUp className="w-5 h-5" />
                        Form Permintaan Top Up
                    </CardTitle>
                </CardHeader>
                <CardContent className="p-6 space-y-6">
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        {/* Date */}
                        <div>
                            <label className="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
                                <Calendar className="w-4 h-4" />
                                Tanggal Top Up
                            </label>
                            <Flatpickr
                                options={{ dateFormat: 'd/m/Y', position: isRtl ? 'auto right' : 'auto left' }}
                                className="flex h-10 w-full items-center justify-between rounded-md border border-input bg-white px-3 py-2 text-sm"
                                value={formData.topUpDate}
                                onChange={(dates: Date[]) => dates[0] && setFormData({ ...formData, topUpDate: dates[0] })}
                            />
                        </div>

                        {/* Amount */}
                        <div>
                            <label className="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
                                <CreditCard className="w-4 h-4" />
                                Nominal Top Up (Rp) *
                            </label>
                            <input
                                type="number"
                                className="flex h-10 w-full items-center justify-between rounded-md border border-input bg-white px-3 py-2 text-sm font-bold text-primary"
                                placeholder="0"
                                min="0"
                                value={formData.amount || ''}
                                onChange={(e) => setFormData({ ...formData, amount: parseFloat(e.target.value) || 0 })}
                            />
                        </div>

                        {/* Source Account */}
                        {roleID !== 5 && (
                            <div>
                                <label className="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
                                    <Wallet className="w-4 h-4" />
                                    Sumber Dana *
                                </label>
                                <SelectSearch
                                    placeholder="Pilih akun..."
                                    options={coaOptionsFormatted}
                                    value={coaOptionsFormatted.find((o) => o.value === formData.fromCoaCode) || null}
                                    onChange={(opt: any) => setFormData({ ...formData, fromCoaCode: opt?.value || '' })}
                                    onInputChange={(val) => setSearchCOA(val)}
                                    filterOption={null} // Disable client-side filtering to show server results
                                    styles={selectStyles}
                                    menuPortalTarget={document.body}
                                    isClearable={roleID !== 5}
                                    isSearchable
                                    isDisabled={roleID === 5}
                                />
                            </div>
                        )}
                    </div>

                    {/* Description & Notes */}
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label className="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
                                <FileText className="w-4 h-4" />
                                Alasan Top Up
                            </label>
                            <textarea
                                className="flex min-h-[80px] w-full rounded-md border border-input bg-white px-3 py-2 text-sm"
                                placeholder="Jelaskan alasan permintaan top up..."
                                value={formData.description}
                                onChange={(e) => setFormData({ ...formData, description: e.target.value })}
                            />
                        </div>
                        <div>
                            <label className="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
                                <FileText className="w-4 h-4" />
                                Catatan Tambahan
                            </label>
                            <textarea
                                className="flex min-h-[80px] w-full rounded-md border border-input bg-white px-3 py-2 text-sm"
                                placeholder="Catatan tambahan (opsional)"
                                value={formData.notes}
                                onChange={(e) => setFormData({ ...formData, notes: e.target.value })}
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
                            <p className="text-white/80 text-sm">Estimasi Saldo Akhir</p>
                            <p className="text-sm font-light mt-1 text-white/60">
                                {FormatNumber(currentBalance)} + {FormatNumber(formData.amount)}
                            </p>
                        </div>
                        <div className="flex items-center gap-6">
                            <p className="text-3xl font-bold font-mono">
                                Rp {FormatNumber(newBalance)}
                            </p>
                            <Button
                                onClick={handleConfirm}
                                disabled={isCreating}
                                className="bg-white text-primary hover:bg-gray-100 gap-2 h-12 px-6"
                            >
                                {isCreating ? (
                                    <Loader2 className="w-5 h-5 animate-spin" />
                                ) : (
                                    <Save className="w-5 h-5" />
                                )}
                                {isCreating ? 'Processing...' : 'Ajukan Top Up'}
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <ConfirmationModal
                isOpen={showConfirmModal}
                onClose={() => setShowConfirmModal(false)}
                onConfirm={handleSave}
                title="Konfirmasi Top Up"
                message={
                    <div>
                        <p>Apakah anda yakin ingin mengajukan top up sebesar <span className="font-bold">Rp {FormatNumber(formData.amount)}</span>?</p>
                        <p className="text-sm text-gray-500 mt-2">Pastikan data yang diinput sudah benar.</p>
                    </div>
                }
                confirmText={isCreating ? 'Memproses...' : 'Ya, Ajukan'}
                cancelText="Batal"
                loading={isCreating}
            />
        </div>
    );
};

export default PettyCashTopUpForm;
