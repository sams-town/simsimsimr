import { useEffect, useState } from 'react';
import { useDispatch } from 'react-redux';
import { useTranslation } from 'react-i18next';
import { useNavigate, useSearchParams } from 'react-router-dom';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '@/store/themeConfigSlice';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import SelectSearch from 'react-select';
import Flatpickr from 'react-flatpickr';
import 'flatpickr/dist/flatpickr.css';
import {
    useGetPettyCashUnitOptionsQuery,
    useCreatePettyCashSettlementMutation,
    useGetPettyCashMyUnitQuery,
    useGetPettyCashRequestByIdQuery,
    useGetPettyCashSettlementHistoryQuery,
} from '@/store/api/pettyCash/pettyCashApiSlice';
import { useGetOptionCOAQuery } from '@/store/api/coa/coaApiSlice';
import { FormatNumber } from '@/utils/formatNumber';
import { toastMessage } from '@/utils/toastUtils';
import { ArrowLeft, Save, Loader2, Plus, Trash2, FileText, Receipt } from 'lucide-react';
import IconCalendar from '@/components/Icon/IconCalendar';
import ConfirmationModal from '@/components/ConfirmationModal';

interface DetailItem {
    description: string;
    amount: number;
    expenseCoaCode: string;
    expenseCoaName: string;
}

interface FormData {
    unitId: number;
    settlementDate: Date;
    description: string;
    notes: string;
    details: DetailItem[];
}

const defaultFormData: FormData = {
    unitId: 0,
    settlementDate: new Date(),
    description: '',
    notes: '',
    details: [],
};

const PettyCashSettlementForm = () => {
    const dispatch = useDispatch();
    const navigate = useNavigate();
    const { t } = useTranslation();
    const [searchParams] = useSearchParams();
    const queryRequestId = searchParams.get('request_id');

    useEffect(() => {
        dispatch(setPageTitle('Buat Settlement'));
        dispatch(setTitle('Buat Settlement'));
        dispatch(setBreadcrumbTitle(['Dashboard', t('sidebar.petty_cash', 'Petty Cash'), 'Settlement', 'Buat']));
    }, [dispatch, t]);

    const [formData, setFormData] = useState<FormData>(defaultFormData);

    // Percobaan settlement sebelumnya untuk request ini (kalau ada yang pernah dibatalkan).
    const { data: historyRes } = useGetPettyCashSettlementHistoryQuery(queryRequestId ?? '', {
        skip: !queryRequestId,
    });
    const cancelledAttempts: any[] = (historyRes?.data?.data || []).filter((h: any) => h.status === 'Cancelled');
    const [showConfirmModal, setShowConfirmModal] = useState(false);

    // API
    const { data: myUnitData } = useGetPettyCashMyUnitQuery(undefined, { refetchOnMountOrArgChange: true });
    const { data: unitOptions } = useGetPettyCashUnitOptionsQuery({});

    // Request Data
    const { data: requestData } = useGetPettyCashRequestByIdQuery(queryRequestId, { skip: !queryRequestId });
    const request = requestData?.data;

    // COA Options
    const [searchCOA, setSearchCOA] = useState('');
    const { data: coaOptions } = useGetOptionCOAQuery({ type: 'expense', keyword: searchCOA, pageSize: 100 });
    // API returns: { label: "[1] A S E T", value: "1", desc: "1" } - label contains full name
    // We parse: desc = COA code, extract COA name from label
    const coaOptionsFormatted = (((coaOptions as any)?.data as any[]) || []).map((c: any) => {
        // Extract only the COA name - remove category prefix like "[1] "
        const labelParts = c.label?.split('] ');
        const coaName = labelParts && labelParts.length > 1 ? labelParts[1].trim() : c.label;
        return {
            value: c.desc, // COA code
            label: `${c.desc} - ${coaName}`, // Display: "code - name"
            name: coaName
        };
    });

    const [createSettlement, { isLoading: isCreating }] = useCreatePettyCashSettlementMutation();

    const myUnit = myUnitData?.data;
    const isPettyCashOfficer = !!myUnit;

    useEffect(() => {
        // Only set unitId from myUnit if there's no request_id (direct create)
        if (myUnit && !queryRequestId) {
            setFormData(prev => ({ ...prev, unitId: myUnit.unitId }));
        }
    }, [myUnit, queryRequestId]);

    useEffect(() => {
        if (request) {
            setFormData(prev => ({
                ...prev,
                unitId: request.unitId,
                description: `Settlement - ${request.purpose}`,
                details: prev.details.length === 0 || (prev.details.length === 1 && !prev.details[0].amount) ? [{
                    description: request.purpose || '',
                    amount: request.approvedAmount || request.amount || 0,
                    expenseCoaCode: '',
                    expenseCoaName: ''
                }] : prev.details
            }));
        }
    }, [request]);

    const unitOptionsFormatted = ((unitOptions?.data as any[]) || []).map((u: any) => ({
        value: u.value,
        label: u.label,
    }));

    const handleAddDetail = () => {
        setFormData({
            ...formData,
            details: [...formData.details, {
                description: '',
                amount: 0,
                expenseCoaCode: '',
                expenseCoaName: ''
            }],
        });
    };

    const handleRemoveDetail = (index: number) => {
        const newDetails = [...formData.details];
        newDetails.splice(index, 1);
        setFormData({ ...formData, details: newDetails });
    };

    const handleDetailChange = (index: number, field: keyof DetailItem, value: any) => {
        const newDetails = [...formData.details];
        newDetails[index] = { ...newDetails[index], [field]: value };
        setFormData({ ...formData, details: newDetails });
    };

    // Removed handleSelectRequest and request availability logic

    // Hanya item dengan jumlah > 0 yang dihitung sebagai pengeluaran nyata.
    // Baris kosong / jumlah 0 diabaikan agar kasus "dana tidak jadi dipakai" bisa disettle.
    const spentDetails = formData.details.filter((d) => (d.amount || 0) > 0);
    const totalSpent = spentDetails.reduce((sum, d) => sum + (d.amount || 0), 0);
    const approvedAmount = request?.approvedAmount || request?.amount || 0;
    // Pengembalian penuh: ada dana yang dicairkan (dari request) tapi tidak ada pengeluaran.
    const isFullReturn = spentDetails.length === 0 && approvedAmount > 0;
    // Semua dihitung otomatis dari selisih pengeluaran vs dana disetujui — tanpa input manual.
    // Kurang bayar: pengeluaran > dana disetujui (kas kecil menalangi selisihnya).
    const shortfall = approvedAmount > 0 && totalSpent > approvedAmount ? totalSpent - approvedAmount : 0;
    // Lebih bayar / sisa dana: pengeluaran < dana disetujui (sisa otomatis dikembalikan ke kas).
    const returnAmount = approvedAmount > 0 && totalSpent < approvedAmount ? approvedAmount - totalSpent : 0;
    // Pengurangan saldo kas kecil = kas yang benar-benar keluar untuk transaksi ini.
    // Karena sisa selalu dikembalikan penuh, nilainya selalu = total pengeluaran.
    const netAmount = approvedAmount > 0 ? approvedAmount - returnAmount + shortfall : totalSpent;

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();

        if (!formData.unitId) {
            toastMessage('Pilih unit kas kecil', 'error');
            return;
        }

        if (spentDetails.length === 0) {
            // Tidak ada pengeluaran. Hanya boleh disettle sebagai pengembalian penuh
            // jika memang ada dana yang dicairkan dari permintaan.
            if (approvedAmount <= 0) {
                toastMessage('Tambahkan minimal satu item pengeluaran', 'error');
                return;
            }
            // else: dana tidak jadi dipakai — dikembalikan seluruhnya.
        } else {
            // Ada pengeluaran nyata — setiap item harus lengkap.
            if (spentDetails.some(d => !d.expenseCoaCode)) {
                toastMessage('Pilih akun biaya untuk semua item', 'error');
                return;
            }
            if (spentDetails.some(d => !d.description.trim())) {
                toastMessage('Lengkapi keterangan untuk semua item pengeluaran', 'error');
                return;
            }
        }

        setShowConfirmModal(true);
    };

    const handleConfirmSubmit = async () => {
        const payload = {
            unitId: formData.unitId,
            requestId: request?.requestId || null, // Request ID in header
            settlementDate: formData.settlementDate.toISOString(),
            description: formData.description,
            returnAmount: returnAmount,
            notes: formData.notes,
            details: spentDetails.map(d => ({
                expenseCoaCode: d.expenseCoaCode,
                expenseCoaName: d.expenseCoaName,
                description: d.description,
                amount: d.amount,
            })),
        };

        try {
            await createSettlement(payload).unwrap();
            toastMessage('Settlement berhasil dibuat', 'success');
            navigate('/petty-cash/settlement');
        } catch (error: any) {
            toastMessage(error?.data?.message || 'Gagal membuat settlement', 'error');
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

    return (
        <div className="space-y-6">
            {/* Header */}
            <div className="flex items-center justify-between">
                <Button
                    variant="outline"
                    className="gap-2"
                    onClick={() => navigate('/petty-cash/settlement')}
                >
                    <ArrowLeft className="w-4 h-4" />
                    {t('common.back') || 'Kembali'}
                </Button>
            </div>

            {/* Unit Info Card - Exactly matching Request Header Style */}
            {/* Request Info Card */}
            {request && (
                <Card className="bg-blue-50 border-blue-200">
                    <CardHeader className="pb-2">
                        <CardTitle className="text-blue-800 flex items-center gap-2">
                            <FileText className="w-5 h-5" />
                            Informasi Permintaan Asal
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div>
                            <span className="text-gray-500 block">No. Permintaan</span>
                            <span className="font-semibold">{request.requestNo}</span>
                        </div>
                        <div>
                            <span className="text-gray-500 block">Pemohon</span>
                            <span className="font-semibold">{request.requesterName}</span>
                        </div>
                        <div>
                            <span className="text-gray-500 block">Keperluan</span>
                            <span className="font-semibold">{request.purpose}</span>
                        </div>
                        <div>
                            <span className="text-gray-500 block">Jumlah Disetujui</span>
                            <span className="font-mono font-bold text-green-700">Rp {FormatNumber(request.approvedAmount || request.amount)}</span>
                        </div>
                    </CardContent>
                </Card>
            )}

            {cancelledAttempts.length > 0 && (
                <div className="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3">
                    <p className="text-sm font-semibold text-amber-900">
                        Request ini pernah di-settle dan dibatalkan ({cancelledAttempts.length}x)
                    </p>
                    <ul className="mt-1.5 space-y-1">
                        {cancelledAttempts.map((h: any) => (
                            <li key={h.settlementId} className="text-xs text-amber-800">
                                <span className="font-mono font-medium">{h.settlementNo}</span>
                                {' · '}Rp {FormatNumber(h.totalAmount)}
                                {h.cancelJournalNo ? ` · jurnal balik ${h.cancelJournalNo}` : ''}
                                {h.cancelReason ? ` · ${h.cancelReason}` : ''}
                            </li>
                        ))}
                    </ul>
                </div>
            )}

            {/* Settlement Form */}
            <Card>
                <CardHeader className="border-b bg-gradient-to-r from-blue-50 to-transparent">
                    <CardTitle className="flex items-center gap-2 text-lg text-blue-700">
                        <FileText className="w-5 h-5" />
                        Informasi Unit Kas Kecil
                        {/* Only show badge if explicitly active, mimicking Request logic if needed, but for now just consistency */}
                        {isPettyCashOfficer && (
                            <span className="ml-2 px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                ACTIVE
                            </span>
                        )}
                    </CardTitle>
                </CardHeader>
                <CardContent className="p-6">
                    {/* Role 5: Read Only View */}
                    {isPettyCashOfficer ? (
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <div>
                                <p className="text-sm text-gray-500">Kode Unit</p>
                                <p className="font-semibold text-lg">{myUnit?.unitCode || '-'}</p>
                            </div>
                            <div className="lg:col-span-2">
                                <p className="text-sm text-gray-500">Nama Unit</p>
                                <p className="font-semibold">{myUnit?.unitName || '-'}</p>
                            </div>
                            <div>
                                <p className="text-sm text-gray-500">Saldo Saat Ini</p>
                                <p className="font-semibold font-mono text-lg text-green-600">
                                    Rp {FormatNumber(myUnit?.currentBalance || 0)}
                                </p>
                            </div>
                        </div>
                    ) : (
                        /* Admin: Select View */
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label className="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
                                    <FileText className="w-4 h-4" />
                                    Unit Kas Kecil *
                                </label>
                                <SelectSearch
                                    options={unitOptionsFormatted}
                                    value={unitOptionsFormatted.find((o) => o.value === formData.unitId) || null}
                                    onChange={(opt: any) => setFormData({ ...formData, unitId: opt?.value || 0, details: [] })}
                                    placeholder="Pilih unit..."
                                    isClearable
                                    styles={selectStyles}
                                    menuPortalTarget={document.body}
                                />
                            </div>
                        </div>
                    )}
                </CardContent>
            </Card>

            {/* Main Form Card - Matching Request Form Style */}
            <Card className="border-primary/20 bg-primary/5">
                <CardHeader className="border-b border-primary/10">
                    <CardTitle className="flex items-center gap-2 text-lg text-primary">
                        <Receipt className="w-5 h-5" />
                        Buat Settlement Baru
                    </CardTitle>
                </CardHeader>
                <CardContent className="p-6 space-y-6">
                    {/* Date & Desc */}
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label className="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
                                <IconCalendar className="w-4 h-4" />
                                Tanggal Settlement *
                            </label>
                            <Flatpickr
                                options={{ dateFormat: 'd/m/Y' }}
                                className="flex h-10 w-full items-center justify-between rounded-md border border-input bg-white px-3 py-2 text-sm"
                                value={formData.settlementDate}
                                onChange={(dates) => {
                                    if (dates[0]) setFormData({ ...formData, settlementDate: dates[0] });
                                }}
                                placeholder="Pilih tanggal"
                            />
                        </div>

                        <div>
                            <label className="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
                                <FileText className="w-4 h-4" />
                                Deskripsi
                            </label>
                            <input
                                type="text"
                                className="flex h-10 w-full items-center justify-between rounded-md border border-input bg-white px-3 py-2 text-sm"
                                value={formData.description}
                                onChange={(e) => setFormData({ ...formData, description: e.target.value })}
                                placeholder="Deskripsi settlement (opsional)"
                            />
                        </div>
                    </div>

                    {/* Detail Items Section */}
                    <div className="border-t border-gray-200 pt-6">
                        <div className="flex items-center justify-between mb-4">
                            <h3 className="font-medium text-lg flex items-center gap-2">
                                <FileText className="w-5 h-5 text-gray-500" />
                                Rincian Pengeluaran
                            </h3>
                            <Button type="button" variant="outline" size="sm" onClick={handleAddDetail}>
                                <Plus className="h-4 w-4 mr-1" />
                                Tambah Item
                            </Button>
                        </div>

                        {formData.details.length === 0 ? (
                            <div className="text-center py-8 bg-white rounded-lg border-2 border-dashed border-gray-300">
                                <p className="text-gray-500">Belum ada item pengeluaran</p>
                                <p className="text-sm text-gray-400">Klik "Tambah Item" untuk menambahkan</p>
                                {approvedAmount > 0 && (
                                    <p className="text-sm text-amber-600 mt-3 font-medium">
                                        Dana tidak jadi dipakai? Biarkan kosong dan klik "Buat Settlement" untuk mengembalikan seluruh dana Rp {FormatNumber(approvedAmount)}.
                                    </p>
                                )}
                            </div>
                        ) : (
                            <div className="space-y-4">
                                {formData.details.map((detail, index) => (
                                    <div key={index} className="grid grid-cols-12 gap-3 items-start p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                                        <div className="col-span-4">
                                            <label className="block text-xs font-medium mb-1 text-gray-500">Akun Biaya *</label>
                                            <SelectSearch
                                                options={coaOptionsFormatted}
                                                value={detail.expenseCoaCode ? (coaOptionsFormatted.find((c: any) => c.value === detail.expenseCoaCode) || { value: detail.expenseCoaCode, label: `${detail.expenseCoaCode} - ${detail.expenseCoaName}`, name: detail.expenseCoaName }) : null}
                                                onChange={(opt: any) => {
                                                    const newDetails = [...formData.details];
                                                    newDetails[index] = {
                                                        ...newDetails[index],
                                                        expenseCoaCode: opt?.value || '',
                                                        expenseCoaName: opt?.name || ''
                                                    };
                                                    setFormData({ ...formData, details: newDetails });
                                                }}
                                                placeholder="Pilih Akun Biaya"
                                                styles={selectStyles}
                                                menuPortalTarget={document.body}
                                                onInputChange={(val, { action }) => { if (action === 'input-change') setSearchCOA(val); }}
                                                isSearchable
                                            />
                                        </div>
                                        <div className="col-span-4">
                                            <label className="block text-xs font-medium mb-1 text-gray-500">Keterangan *</label>
                                            <input
                                                type="text"
                                                className="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                                                value={detail.description}
                                                onChange={(e) => handleDetailChange(index, 'description', e.target.value)}
                                                placeholder="Keterangan pengeluaran"
                                            />
                                        </div>
                                        <div className="col-span-3">
                                            <label className="block text-xs font-medium mb-1 text-gray-500">Jumlah (Rp) *</label>
                                            <input
                                                type="number"
                                                className="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm text-right font-medium"
                                                value={detail.amount || ''}
                                                onChange={(e) => handleDetailChange(index, 'amount', parseFloat(e.target.value) || 0)}
                                                placeholder="0"
                                                min="0"
                                            />
                                        </div>
                                        <div className="col-span-1 flex flex-col">
                                            {/* Spacer agar tombol sejajar dengan kotak input, bukan dengan label */}
                                            <label className="block text-xs font-medium mb-1 invisible select-none" aria-hidden="true">Aksi</label>
                                            <div className="flex items-center justify-center h-9">
                                                <Button
                                                    type="button"
                                                    variant="ghost"
                                                    size="sm"
                                                    className="text-red-500 hover:text-red-700 h-9 w-9 p-0"
                                                    onClick={() => handleRemoveDetail(index)}
                                                >
                                                    <Trash2 className="h-4 w-4" />
                                                </Button>
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        )}
                    </div>

                    {/* Notes */}
                    <div className="border-t border-gray-200 pt-6">
                        <label className="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
                            <FileText className="w-4 h-4" />
                            Catatan
                        </label>
                        <textarea
                            className="flex min-h-[80px] w-full rounded-md border border-input bg-white px-3 py-2 text-sm"
                            rows={3}
                            value={formData.notes}
                            onChange={(e) => setFormData({ ...formData, notes: e.target.value })}
                            placeholder="Catatan tambahan (opsional)"
                        />
                        {approvedAmount > 0 && (
                            <p className="text-xs text-gray-500 mt-2">
                                Kurang/lebih bayar dihitung otomatis dari selisih total pengeluaran dengan dana disetujui — sisa dana otomatis dikembalikan ke kas kecil.
                            </p>
                        )}
                    </div>
                </CardContent>
            </Card>

            {/* Summary & Action Card - Matches Request Form */}
            <Card className="bg-gradient-to-r from-primary to-primary/80 text-white">
                <CardContent className="p-6">
                    <div className="flex items-center justify-between gap-6">
                        <div className="flex items-center gap-6 divide-x divide-white/20 flex-1 min-w-0">
                            {approvedAmount > 0 && (
                                <div className="text-center px-4 first:pl-0">
                                    <p className="text-white/70 text-xs uppercase tracking-wide">Dana Disetujui</p>
                                    <p className="text-lg font-bold font-mono mt-1">Rp {FormatNumber(approvedAmount)}</p>
                                </div>
                            )}
                            <div className="text-center px-4 first:pl-0">
                                <p className="text-white/70 text-xs uppercase tracking-wide">Total Pengeluaran</p>
                                <p className="text-xl font-bold font-mono mt-1">Rp {FormatNumber(totalSpent)}</p>
                            </div>
                            {shortfall > 0 && (
                                <div className="text-center px-4">
                                    <p className="text-white/70 text-xs uppercase tracking-wide">Kurang Bayar</p>
                                    <p className="text-lg font-bold font-mono mt-1 text-red-300">Rp {FormatNumber(shortfall)}</p>
                                </div>
                            )}
                            {returnAmount > 0 && shortfall === 0 && (
                                <div className="text-center px-4">
                                    <p className="text-white/70 text-xs uppercase tracking-wide">Lebih Bayar (Dikembalikan)</p>
                                    <p className="text-lg font-bold font-mono mt-1 text-yellow-200">Rp {FormatNumber(returnAmount)}</p>
                                </div>
                            )}
                            <div className="text-center px-4">
                                <p className="text-white/70 text-xs uppercase tracking-wide">Pengurangan Saldo</p>
                                <p className={`text-xl font-bold font-mono mt-1 ${netAmount < 0 ? 'text-red-300' : 'text-green-200'}`}>Rp {FormatNumber(netAmount)}</p>
                            </div>
                        </div>
                        <Button
                            onClick={handleSubmit}
                            disabled={isCreating}
                            className="bg-white text-primary hover:bg-gray-100 gap-2 h-12 px-6 shrink-0"
                        >
                            {isCreating ? (
                                <Loader2 className="w-5 h-5 animate-spin" />
                            ) : (
                                <Save className="w-5 h-5" />
                            )}
                            Buat Settlement
                        </Button>
                    </div>
                </CardContent>
            </Card>

            <ConfirmationModal
                isOpen={showConfirmModal}
                onClose={() => setShowConfirmModal(false)}
                onConfirm={handleConfirmSubmit}
                title="Buat Settlement"
                message={
                    isFullReturn ? (
                        <div>
                            <p>Dana tidak digunakan. Seluruh dana <span className="font-bold">Rp {FormatNumber(approvedAmount)}</span> akan dikembalikan ke kas kecil.</p>
                            <p className="text-sm text-gray-500 mt-2">Settlement dibuat tanpa item pengeluaran (pengembalian penuh).</p>
                        </div>
                    ) : (
                        <div>
                            <p>Apakah anda yakin ingin membuat settlement senilai <span className="font-bold">Rp {FormatNumber(totalSpent)}</span>?</p>
                            <p className="text-sm text-gray-500 mt-2">Pastikan semua rincian pengeluaran sudah benar.</p>
                        </div>
                    )
                }
                confirmText={isCreating ? 'Menyimpan...' : 'Ya, Simpan'}
                cancelText="Batal"
                loading={isCreating}
            />
        </div>
    );
};

export default PettyCashSettlementForm;
