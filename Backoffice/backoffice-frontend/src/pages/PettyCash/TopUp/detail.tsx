import { useEffect, useState } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { useTranslation } from 'react-i18next';
import { useNavigate, useParams } from 'react-router-dom';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '@/store/themeConfigSlice';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import {
    useGetPettyCashTopUpByIdQuery,
    useApprovePettyCashTopUpMutation,
    useRejectPettyCashTopUpMutation,
} from '@/store/api/pettyCash/pettyCashApiSlice';
import { FormatNumber } from '@/utils/formatNumber';
import { toastMessage } from '@/utils/toastUtils';
import ConfirmationModal from '@/components/ConfirmationModal';
import {
    ArrowLeft,
    Check,
    X,
    Ban,
    Loader2,
    Calendar,
    Wallet,
    FileText,
    CreditCard,
    Info,
    Printer
} from 'lucide-react';


const PettyCashTopUpDetail = () => {
    const dispatch = useDispatch();
    const navigate = useNavigate();
    const { t } = useTranslation();
    const { id } = useParams();
    const isRtl = useSelector((state: any) => state.themeConfig.rtlClass) === 'rtl';
    const user = useSelector((state: any) => state.auth.user);

    // API
    const { data: topUpData, isLoading, error, refetch } = useGetPettyCashTopUpByIdQuery(id, {
        refetchOnMountOrArgChange: true,
        skip: !id,
    });

    const [approveTopUp, { isLoading: isApproving }] = useApprovePettyCashTopUpMutation();
    const [rejectTopUp, { isLoading: isRejecting }] = useRejectPettyCashTopUpMutation();

    const [showApproveModal, setShowApproveModal] = useState(false);
    const [showRejectModal, setShowRejectModal] = useState(false);
    const [rejectReason, setRejectReason] = useState('');

    const topUp = topUpData?.data || {};

    // Action States
    const [activeActionTab, setActiveActionTab] = useState<'approve' | 'reject'>('approve');
    const [approveAmount, setApproveAmount] = useState<number>(0);
    const [approveAmountDisplay, setApproveAmountDisplay] = useState('');
    const [approveNotes, setApproveNotes] = useState('');

    useEffect(() => {
        dispatch(setPageTitle('Detail Top Up'));
        dispatch(setTitle('Detail Top Up'));
        dispatch(setBreadcrumbTitle(['Dashboard', t('sidebar.petty_cash', 'Petty Cash'), 'Top Up', 'Detail']));
    }, [dispatch, t]);

    // Initialize approval amount when data is loaded
    useEffect(() => {
        if (topUp?.amount && approveAmount === 0) {
            setApproveAmount(topUp.amount);
            setApproveAmountDisplay(new Intl.NumberFormat('id-ID').format(topUp.amount));
        }
    }, [topUp]);

    const handleAmountChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const value = e.target.value.replace(/\D/g, ''); // Remove non-digits
        if (value === '') {
            setApproveAmount(0);
            setApproveAmountDisplay('');
            return;
        }
        const numberValue = parseInt(value, 10);
        setApproveAmount(numberValue);
        setApproveAmountDisplay(new Intl.NumberFormat('id-ID').format(numberValue));
    };

    const handleOpenApproveModal = () => {
        setShowApproveModal(true);
    };

    const handleConfirmApprove = async () => {
        if (approveAmount <= 0) {
            toastMessage('Jumlah yang disetujui harus lebih dari 0', 'error');
            return;
        }

        try {
            await approveTopUp({
                id: topUp.topUpId,
                approvedAmount: approveAmount,
                notes: approveNotes
            }).unwrap();

            toastMessage('Top Up berhasil disetujui', 'success');
            refetch();
            setShowApproveModal(false);
        } catch (error: any) {
            toastMessage(error?.data?.message || 'Gagal menyetujui Top Up', 'error');
        }
    };

    const handleReject = () => {
        setShowRejectModal(true);
    };

    const handleConfirmReject = async () => {
        if (!rejectReason.trim()) {
            toastMessage('Alasan penolakan harus diisi', 'error');
            return;
        }

        try {
            await rejectTopUp({ id: topUp.topUpId, reason: rejectReason }).unwrap();
            toastMessage('Top Up berhasil ditolak', 'success');
            setShowRejectModal(false);
            refetch();
        } catch (error: any) {
            toastMessage(error?.data?.message || 'Gagal menolak Top Up', 'error');
        }
    };



    if (isLoading) {
        return (
            <div className="flex items-center justify-center h-64">
                <Loader2 className="h-8 w-8 animate-spin text-primary" />
            </div>
        );
    }

    if (error || !topUpData) {
        return (
            <div className="p-5 text-center">
                <div className="text-red-500 mb-2">Data Top Up tidak ditemukan.</div>
                <Button onClick={() => navigate('/petty-cash/topup')}>Kembali</Button>
            </div>
        )
    }

    const getStatusBadge = (status: string) => {
        switch (status) {
            case 'Pending':
                return <span className="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs font-semibold">Menunggu Persetujuan</span>;
            case 'Posted':
                return <span className="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-semibold">Posted</span>;
            case 'Rejected':
                return <span className="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-semibold">Rejected</span>;
            case 'Void':
                return <span className="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs font-semibold">Void</span>;
            default:
                return <span className="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs font-semibold">{status}</span>;
        }
    };

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

                {topUp.status === 'Posted' && (
                    <Button
                        variant="outline"
                        className="gap-2"
                        onClick={() => window.open(`/petty-cash/topup/${topUp.topUpId}/receipt`, '_blank')}
                    >
                        <Printer className="w-4 h-4" />
                        Cetak Tanda Terima
                    </Button>
                )}
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {/* Main Info */}
                <Card className="lg:col-span-2">
                    <CardHeader className="border-b">
                        <CardTitle className="flex items-center justify-between">
                            <span className="flex items-center gap-2">
                                <FileText className="w-5 h-5 text-primary" />
                                Informasi Top Up
                            </span>
                            {getStatusBadge(topUp.status)}
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="p-6 space-y-6">
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <p className="text-sm text-gray-500 mb-1">No. Top Up</p>
                                <p className="font-semibold text-lg">{topUp.topUpNo}</p>
                            </div>
                            <div>
                                <p className="text-sm text-gray-500 mb-1">Tanggal</p>
                                <div className="flex items-center gap-2 font-medium">
                                    <Calendar className="w-4 h-4 text-gray-400" />
                                    {topUp.topUpDate && new Date(topUp.topUpDate).toLocaleDateString('id-ID', {
                                        day: 'numeric',
                                        month: 'long',
                                        year: 'numeric'
                                    })}
                                </div>
                            </div>
                            <div>
                                <p className="text-sm text-gray-500 mb-1">Unit Penerima</p>
                                <div className="flex items-center gap-2 font-medium">
                                    <Wallet className="w-4 h-4 text-gray-400" />
                                    {topUp.unitCode} - {topUp.unitName}
                                </div>
                            </div>
                            <div>
                                <p className="text-sm text-gray-500 mb-1">Sumber Dana</p>
                                <div className="flex items-center gap-2 font-medium">
                                    <CreditCard className="w-4 h-4 text-gray-400" />
                                    {topUp.fromCoaCode} - {topUp.fromCoaName}
                                </div>
                            </div>
                        </div>

                        <div className="p-4 bg-gray-50 rounded-lg border border-gray-100">
                            <p className="text-sm text-gray-500 mb-2">Jumlah Top Up (Permintaan)</p>
                            <p className="text-3xl font-bold text-gray-800 font-mono">
                                Rp {FormatNumber(topUp.amount)}
                            </p>
                            {topUp.status === 'Posted' && (
                                <div className="mt-4 pt-4 border-t border-gray-200">
                                    <p className="text-sm text-green-600 font-semibold mb-1">Jumlah Disetujui</p>
                                    <p className="text-2xl font-bold text-green-600 font-mono">
                                        Rp {FormatNumber(topUp.approvedAmount || 0)}
                                    </p>
                                </div>
                            )}
                        </div>

                        <div>
                            <p className="text-sm text-gray-500 mb-2">Keterangan / Alasan</p>
                            <p className="text-gray-700 whitespace-pre-wrap bg-white p-3 rounded border">
                                {topUp.description || '-'}
                            </p>
                        </div>

                        {topUp.notes && (
                            <div>
                                <p className="text-sm text-gray-500 mb-2">Catatan Tambahan (Request)</p>
                                <p className="text-gray-700 whitespace-pre-wrap bg-white p-3 rounded border">
                                    {topUp.notes}
                                </p>
                            </div>
                        )}

                        {(topUp.status === 'Posted' || topUp.approvedNotes) && (
                            <div>
                                <p className="text-sm text-gray-500 mb-2">Catatan Approval</p>
                                <p className="text-gray-700 whitespace-pre-wrap bg-white p-3 rounded border border-green-200 bg-green-50">
                                    {topUp.approvedNotes || '-'}
                                </p>
                            </div>
                        )}

                        {topUp.rejectedReason && (
                            <div className="bg-red-50 border border-red-100 p-4 rounded-lg">
                                <p className="text-sm text-red-600 font-semibold mb-1">Alasan Penolakan</p>
                                <p className="text-red-700">{topUp.rejectedReason}</p>
                            </div>
                        )}
                    </CardContent>
                </Card>

                {/* Meta Info */}
                <div className="space-y-6">
                    <Card>
                        <CardHeader className="border-b">
                            <CardTitle className="flex items-center gap-2 text-base">
                                <Info className="w-4 h-4" />
                                Log Aktivitas
                            </CardTitle>
                        </CardHeader>
                        <CardContent className="p-4 space-y-4">
                            <div className="text-sm">
                                <p className="text-gray-500">Dibuat Oleh</p>
                                <p className="font-medium">{topUp.createdByName || '-'}</p>
                                <p className="text-xs text-gray-400">
                                    {topUp.createdDate && new Date(topUp.createdDate).toLocaleString('id-ID')}
                                </p>
                            </div>

                            {topUp.status === 'Approved' && topUp.approvedBy && (
                                <div className="text-sm pt-4 border-t">
                                    <p className="text-gray-500">Disetujui Oleh</p>
                                    <p className="font-medium">{topUp.approvedByName || '-'}</p>
                                    <p className="text-xs text-gray-400">
                                        {topUp.approvedDate && new Date(topUp.approvedDate).toLocaleString('id-ID')}
                                    </p>
                                </div>
                            )}

                            {topUp.status === 'Rejected' && (
                                <div className="text-sm pt-4 border-t">
                                    <p className="text-gray-500">Ditolak Oleh</p>
                                    <p className="font-medium">{topUp.rejectedByName || '-'}</p>
                                    <p className="text-xs text-gray-400">
                                        {topUp.rejectedDate && new Date(topUp.rejectedDate).toLocaleString('id-ID')}
                                    </p>
                                    {topUp.rejectedReason && (
                                        <div className="mt-2 p-2 bg-red-50 rounded border border-red-100">
                                            <p className="text-xs text-gray-500 mb-1">Alasan:</p>
                                            <p className="text-red-700">{topUp.rejectedReason}</p>
                                        </div>
                                    )}
                                </div>
                            )}

                            {topUp.status === 'Posted' && topUp.approvedBy && (
                                <div className="text-sm pt-4 border-t">
                                    <p className="text-gray-500">Disetujui Oleh</p>
                                    <p className="font-medium">{topUp.approvedByName || '-'}</p>
                                    <p className="text-xs text-gray-400">
                                        {topUp.approvedDate && new Date(topUp.approvedDate).toLocaleString('id-ID')}
                                    </p>
                                </div>
                            )}

                            {topUp.transactionId && (
                                <div className="text-sm pt-4 border-t">
                                    <p className="text-gray-500">Jurnal Transaksi</p>
                                    <p className="font-medium text-primary hover:underline cursor-pointer">
                                        {topUp.journalNo}
                                    </p>
                                </div>
                            )}
                        </CardContent>
                    </Card>
                </div>
            </div>

            {/* Action Card for Pending Status */}
            {topUp.status === 'Pending' && user?.role?.roleID !== 5 && (
                <Card className="border-t-4 border-t-primary">
                    <CardHeader className="border-b bg-gray-50/50">
                        <div className="flex items-center gap-4">
                            <div className="inline-flex bg-gray-100 p-1 rounded-lg">
                                <button
                                    className={`px-4 py-2 text-sm font-medium rounded-md transition-all ${activeActionTab === 'approve'
                                        ? 'bg-white text-green-700 shadow-sm ring-1 ring-black/5'
                                        : 'text-gray-500 hover:text-gray-900 hover:bg-gray-200/50'
                                        }`}
                                    onClick={() => setActiveActionTab('approve')}
                                >
                                    <div className="flex items-center gap-2">
                                        <Check className="w-4 h-4" />
                                        Persetujuan (Approve)
                                    </div>
                                </button>
                                <button
                                    className={`px-4 py-2 text-sm font-medium rounded-md transition-all ${activeActionTab === 'reject'
                                        ? 'bg-white text-red-600 shadow-sm ring-1 ring-black/5'
                                        : 'text-gray-500 hover:text-gray-900 hover:bg-gray-200/50'
                                        }`}
                                    onClick={() => setActiveActionTab('reject')}
                                >
                                    <div className="flex items-center gap-2">
                                        <X className="w-4 h-4" />
                                        Penolakan (Reject)
                                    </div>
                                </button>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent className="p-6">
                        {activeActionTab === 'approve' ? (
                            <div className="space-y-6">
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label htmlFor="approveAmount" className="block text-sm font-medium text-gray-700 mb-1">
                                            Jumlah yang Disetujui <span className="text-red-500">*</span>
                                        </label>
                                        <div className="relative">
                                            <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span className="text-gray-500 sm:text-sm">Rp</span>
                                            </div>
                                            <input
                                                type="text"
                                                name="approveAmount"
                                                id="approveAmount"
                                                className="form-input pl-10 block w-full sm:text-sm rounded-md border-gray-300 focus:ring-primary focus:border-primary"
                                                placeholder="0"
                                                value={approveAmountDisplay}
                                                onChange={handleAmountChange}
                                            />
                                        </div>
                                        <p className="mt-1 text-xs text-gray-500">
                                            Default sesuai jumlah permintaan: Rp {FormatNumber(topUp.amount)}
                                        </p>
                                    </div>
                                    <div>
                                        <label htmlFor="approveNotes" className="block text-sm font-medium text-gray-700 mb-1">
                                            Catatan Approval (Opsional)
                                        </label>
                                        <textarea
                                            id="approveNotes"
                                            className="form-textarea w-full rounded-md border-gray-300 focus:ring-primary focus:border-primary"
                                            rows={3}
                                            placeholder="Tambahkan catatan approval jika perlu..."
                                            value={approveNotes}
                                            onChange={(e) => setApproveNotes(e.target.value)}
                                        />
                                    </div>
                                </div>
                                <div className="flex justify-end pt-4 border-t">
                                    <Button
                                        className="bg-green-600 hover:bg-green-700 text-white gap-2 min-w-[150px]"
                                        onClick={handleOpenApproveModal}
                                        disabled={isApproving}
                                    >
                                        <Check className="w-4 h-4" />
                                        {isApproving ? 'Memproses...' : 'Proses Approve'}
                                    </Button>
                                </div>
                            </div>
                        ) : (
                            <div className="space-y-6">
                                <div>
                                    <label htmlFor="rejectReason" className="block text-sm font-medium text-gray-700 mb-1">
                                        Alasan Penolakan <span className="text-red-500">*</span>
                                    </label>
                                    <textarea
                                        id="rejectReason"
                                        className="form-textarea w-full rounded-md border-gray-300 focus:ring-red-500 focus:border-red-500"
                                        rows={3}
                                        placeholder="Berikan alasan mengapa permintaan ini ditolak..."
                                        value={rejectReason}
                                        onChange={(e) => setRejectReason(e.target.value)}
                                    />
                                </div>
                                <div className="flex justify-end pt-4 border-t">
                                    <Button
                                        variant="destructive"
                                        className="gap-2 min-w-[150px]"
                                        onClick={handleReject}
                                        disabled={isRejecting}
                                    >
                                        <X className="w-4 h-4" />
                                        {isRejecting ? 'Memproses...' : 'Proses Reject'}
                                    </Button>
                                </div>
                            </div>
                        )}
                    </CardContent>
                </Card>
            )}

            <ConfirmationModal
                isOpen={showApproveModal}
                onClose={() => setShowApproveModal(false)}
                onConfirm={handleConfirmApprove}
                title="Setujui Top Up"
                message=""
                customContent={
                    <div className="space-y-4">
                        <p>Apakah anda yakin ingin menyetujui top up ini? Silakan periksa kembali data di bawah ini.</p>

                        <div className="bg-gray-50 p-4 rounded-md space-y-2">
                            <div className="flex justify-between">
                                <span className="text-gray-600">Jumlah Disetujui:</span>
                                <span className="font-bold">Rp {approveAmountDisplay}</span>
                            </div>
                            {approveNotes && (
                                <div className="flex justify-between">
                                    <span className="text-gray-600">Catatan:</span>
                                    <span className="font-medium text-right max-w-[200px] truncate">{approveNotes}</span>
                                </div>
                            )}
                        </div>
                    </div>
                }
                confirmText={isApproving ? 'Memproses...' : 'Ya, Setujui'}
                cancelText="Batal"
                loading={isApproving}
            />

            <ConfirmationModal
                isOpen={showRejectModal}
                onClose={() => setShowRejectModal(false)}
                onConfirm={handleConfirmReject}
                title="Tolak Top Up"
                message=""
                customContent={
                    <div className="space-y-4">
                        <p>Apakah anda yakin ingin menolak top up ini?</p>
                        <div className="bg-red-50 p-4 rounded-md">
                            <span className="text-gray-600 block mb-1">Alasan Penolakan:</span>
                            <span className="font-medium text-red-700">{rejectReason}</span>
                        </div>
                    </div>
                }
                confirmText={isRejecting ? 'Memproses...' : 'Ya, Tolak'}
                cancelText="Batal"
                loading={isRejecting}
                variant="danger"
            />


        </div>
    );
};

export default PettyCashTopUpDetail;
