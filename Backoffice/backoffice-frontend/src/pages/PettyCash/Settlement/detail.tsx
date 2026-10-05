import { useEffect, useState, Fragment } from 'react';
import { useDispatch } from 'react-redux';
import { useTranslation } from 'react-i18next';
import { useNavigate, useParams } from 'react-router-dom';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '@/store/themeConfigSlice';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Dialog, Transition } from '@headlessui/react';
import {
    useGetPettyCashSettlementByIdQuery,
    useCancelPettyCashSettlementMutation,
    useGetPettyCashSettlementHistoryQuery,
} from '@/store/api/pettyCash/pettyCashApiSlice';
import TimelineButton from '@/components/ActivityTimeline/TimelineButton';
import { AUDIT_MODULE } from '@/components/ActivityTimeline/modules';
import { useRoleAccess } from '@/hooks/useRoleAccess';
import { toastMessage } from '@/utils/toastUtils';
import { FormatNumber } from '@/utils/formatNumber';
import { FormatDate } from '@/utils/formatDate';
import { ArrowLeft, FileText, CheckCircle, Clock, Receipt, Printer, Ban, XCircle } from 'lucide-react';
import { Loader2 } from 'lucide-react';

const PettyCashSettlementDetail = () => {
    const dispatch = useDispatch();
    const navigate = useNavigate();
    const { t } = useTranslation();
    const { id } = useParams();

    useEffect(() => {
        dispatch(setPageTitle('Detail Settlement'));
        dispatch(setTitle('Detail Settlement'));
        dispatch(setBreadcrumbTitle(['Dashboard', t('sidebar.petty_cash', 'Petty Cash'), 'Settlement', 'Detail']));
    }, [dispatch, t]);

    const { data: settlementRes, isLoading, refetch } = useGetPettyCashSettlementByIdQuery(id);
    const settlement = settlementRes?.data;

    const { canCancelSettlement } = useRoleAccess();
    const [cancelSettlement, { isLoading: cancelling }] = useCancelPettyCashSettlementMutation();
    const [cancelOpen, setCancelOpen] = useState(false);
    const [cancelReason, setCancelReason] = useState('');

    // Riwayat semua percobaan settlement untuk request yang sama (settle -> batal -> settle lagi).
    const requestIdForHistory = settlementRes?.data?.requestId;
    const { data: historyRes } = useGetPettyCashSettlementHistoryQuery(requestIdForHistory, {
        skip: !requestIdForHistory,
    });
    const history: any[] = historyRes?.data?.data || [];

    const handleCancel = async () => {
        try {
            const res: any = await cancelSettlement({ id: id as string, reason: cancelReason }).unwrap();
            toastMessage(
                `Settlement dibatalkan. Jurnal pembalik: ${res?.data?.cancelJournalNo ?? res?.cancelJournalNo ?? '-'}`,
                'success',
            );
            setCancelOpen(false);
            setCancelReason('');
            refetch();
        } catch (err: any) {
            toastMessage(err?.data?.message || 'Gagal membatalkan settlement', 'error');
        }
    };

    if (isLoading) {
        return (
            <div className="flex items-center justify-center h-64">
                <Loader2 className="h-8 w-8 animate-spin text-primary" />
            </div>
        );
    }

    if (!settlement) {
        return (
            <div className="p-5 text-center">
                <div className="text-red-500 mb-2">Settlement tidak ditemukan.</div>
                <Button onClick={() => navigate('/petty-cash/settlement')}>Kembali</Button>
            </div>
        );
    }

    const getStatusBadge = (status: string) => {
        const config: any = {
            Posted: { bg: 'bg-green-100', text: 'text-green-700', icon: CheckCircle, label: 'Posted' },
            Pending: { bg: 'bg-yellow-100', text: 'text-yellow-700', icon: Clock, label: 'Pending' },
            Void: { bg: 'bg-red-100', text: 'text-red-700', icon: Clock, label: 'Void' },
            Cancelled: { bg: 'bg-red-100', text: 'text-red-700', icon: XCircle, label: 'Dibatalkan' },
        };
        const c = config[status] || config.Pending;
        const Icon = c.icon;

        return (
            <span className={`flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium ${c.bg} ${c.text}`}>
                <Icon className="w-4 h-4" />
                {c.label}
            </span>
        );
    };

    // Kurang bayar (talangan) & lebih bayar (sisa) untuk tombol cetak
    const approvedAmount = Number(settlement.request?.approvedAmount ?? settlement.request?.amount ?? 0);
    const shortfall = settlement.request && settlement.totalAmount > approvedAmount ? settlement.totalAmount - approvedAmount : 0;
    const returnAmount = Number(settlement.returnAmount) || 0;

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
                <div className="flex items-center gap-2">
                    {shortfall > 0 && (
                        <Button
                            variant="outline"
                            className="gap-2"
                            onClick={() => window.open(`/petty-cash/settlement/${id}/receipt?type=kurang`, '_blank')}
                        >
                            <Printer className="w-4 h-4" />
                            Cetak Kurang Bayar
                        </Button>
                    )}
                    {returnAmount > 0 && (
                        <Button
                            variant="outline"
                            className="gap-2"
                            onClick={() => window.open(`/petty-cash/settlement/${id}/receipt?type=lebih`, '_blank')}
                        >
                            <Printer className="w-4 h-4" />
                            Cetak Lebih Bayar
                        </Button>
                    )}
                    <TimelineButton
                        module={AUDIT_MODULE.PETTY_CASH_SETTLEMENT}
                        refId={settlement.settlementId}
                        title={settlement.settlementNo}
                    />
                    {canCancelSettlement && settlement.status === 'Posted' && (
                        <Button
                            variant="outline"
                            className="gap-2 border-red-200 text-red-600 hover:bg-red-50 hover:text-red-700"
                            onClick={() => setCancelOpen(true)}
                        >
                            <Ban className="w-4 h-4" />
                            Batalkan Settlement
                        </Button>
                    )}
                </div>
            </div>

            {settlement.status === 'Cancelled' && (
                <div className="rounded-lg border border-red-200 bg-red-50 px-4 py-3">
                    <div className="flex items-start gap-2">
                        <XCircle className="w-5 h-5 text-red-600 mt-0.5 shrink-0" />
                        <div className="text-sm">
                            <p className="font-semibold text-red-800">Settlement ini sudah dibatalkan</p>
                            <p className="text-red-700 mt-0.5">
                                Jurnal pembalik{settlement.cancelJournalNo ? ` ${settlement.cancelJournalNo}` : ''} sudah dibuat, dan
                                request terkait dikembalikan agar bisa di-settle ulang.
                                {settlement.cancelReason ? ` Alasan: ${settlement.cancelReason}` : ''}
                            </p>
                            {settlement.cancelledDate && (
                                <p className="text-red-600/80 text-xs mt-1">Dibatalkan pada {FormatDate(settlement.cancelledDate)}</p>
                            )}
                        </div>
                    </div>
                </div>
            )}

            {/* Main Content */}
            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {/* Left Column: Details */}
                <div className="lg:col-span-2 space-y-6">
                    <Card>
                        <CardHeader className="border-b">
                            <div className="flex justify-between items-start">
                                <div>
                                    <CardTitle className="text-lg flex items-center gap-2">
                                        <Receipt className="w-5 h-5 text-primary" />
                                        Informasi Settlement
                                    </CardTitle>
                                    <p className="text-sm text-gray-500 mt-1">No. {settlement.settlementNo}</p>
                                </div>
                                {getStatusBadge(settlement.status)}
                            </div>
                        </CardHeader>
                        <CardContent className="p-6">
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <p className="text-sm text-gray-500 mb-1">Tanggal Settlement</p>
                                    <p className="font-semibold">{FormatDate(settlement.settlementDate)}</p>
                                </div>
                                <div>
                                    <p className="text-sm text-gray-500 mb-1">Unit</p>
                                    <p className="font-semibold">{settlement.unitName}</p>
                                    <p className="text-xs text-gray-400">{settlement.unitCode}</p>
                                </div>
                                <div className="md:col-span-2">
                                    <p className="text-sm text-gray-500 mb-1">Catatan</p>
                                    <p className="font-medium bg-gray-50 p-3 rounded-md border border-gray-100">
                                        {settlement.notes || '-'}
                                    </p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Expense Details */}
                    <Card>
                        <CardHeader className="border-b">
                            <CardTitle className="text-base flex items-center gap-2">
                                <FileText className="w-5 h-5 text-primary" />
                                Rincian Pengeluaran
                            </CardTitle>
                        </CardHeader>
                        <CardContent className="p-0">
                            <div className="overflow-x-auto">
                                <table className="w-full text-sm">
                                    <thead>
                                        <tr className="bg-gray-50 border-b">
                                            <th className="px-4 py-3 text-left font-medium text-gray-500">Akun Biaya</th>
                                            <th className="px-4 py-3 text-left font-medium text-gray-500">Keterangan</th>
                                            <th className="px-4 py-3 text-right font-medium text-gray-500">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y">
                                        {settlement.details?.map((detail: any, index: number) => (
                                            <tr key={index}>
                                                <td className="px-4 py-3">
                                                    <p className="font-medium">{detail.expenseCoaName}</p>
                                                    <p className="text-xs text-gray-400 font-mono">{detail.expenseCoaCode}</p>
                                                </td>
                                                <td className="px-4 py-3 text-gray-600">{detail.description}</td>
                                                <td className="px-4 py-3 text-right font-mono font-medium">
                                                    {FormatNumber(detail.amount)}
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                    <tfoot className="bg-gray-50 font-bold border-t">
                                        <tr>
                                            <td colSpan={2} className="px-4 py-3 text-right">TOTAL PENGELUARAN</td>
                                            <td className="px-4 py-3 text-right font-mono text-primary">
                                                Rp {FormatNumber(settlement.totalAmount)}
                                            </td>
                                        </tr>
                                        {settlement.returnAmount > 0 && (
                                            <tr>
                                                <td colSpan={2} className="px-4 py-3 text-right text-green-600">PENGEMBALIAN DANA (SISA)</td>
                                                <td className="px-4 py-3 text-right font-mono text-green-600">
                                                    Rp {FormatNumber(settlement.returnAmount)}
                                                </td>
                                            </tr>
                                        )}
                                    </tfoot>
                                </table>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Journal Info */}
                    {(settlement.transactionId || settlement.journalNo) && (
                        <Card>
                            <CardHeader className="border-b bg-gray-50/50">
                                <CardTitle className="text-base">Informasi Jurnal</CardTitle>
                            </CardHeader>
                            <CardContent className="p-6">
                                <div className="grid grid-cols-2 gap-4">
                                    <div>
                                        <p className="text-sm text-gray-500">No. Jurnal</p>
                                        <p className="font-mono font-medium">{settlement.journalNo || '-'}</p>
                                    </div>
                                    <div>
                                        <p className="text-sm text-gray-500">Transaction ID</p>
                                        <p className="font-mono text-xs text-gray-600 truncate" title={settlement.transactionId}>
                                            {settlement.transactionId || '-'}
                                        </p>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    )}
                </div>

                {/* Right Column: Original Request Info if any */}
                <div className="space-y-6">
                    {settlement.request && (
                        <Card className="bg-blue-50 border-blue-200">
                            <CardHeader className="pb-2">
                                <CardTitle className="text-blue-800 text-base flex items-center gap-2">
                                    <FileText className="w-5 h-5" />
                                    Permintaan Asal
                                </CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <div>
                                    <span className="text-xs text-gray-500 block">No. Permintaan</span>
                                    <span className="font-semibold text-sm">{settlement.request.requestNo}</span>
                                </div>
                                <div>
                                    <span className="text-xs text-gray-500 block">Pemohon</span>
                                    <span className="font-semibold text-sm">{settlement.request.requesterName}</span>
                                </div>
                                <div>
                                    <span className="text-xs text-gray-500 block">Keperluan</span>
                                    <p className="text-xs mt-1 text-gray-700">{settlement.request.purpose}</p>
                                </div>
                                <div className="pt-3 border-t border-blue-200">
                                    <span className="text-xs text-gray-500 block">Jumlah Disetujui</span>
                                    <span className="font-mono font-bold text-blue-700">Rp {FormatNumber(settlement.request.approvedAmount || settlement.request.amount)}</span>
                                </div>
                            </CardContent>
                        </Card>
                    )}
                </div>
            </div>

            {history.length > 1 && (
                <Card>
                    <CardHeader className="border-b border-gray-100 pb-4">
                        <CardTitle className="flex items-center gap-2 text-base">
                            <Receipt className="w-4 h-4" />
                            Riwayat Settlement Request Ini
                            <span className="text-xs font-normal text-gray-500">({history.length} percobaan)</span>
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="pt-4">
                        <div className="overflow-x-auto">
                            <table className="w-full text-sm">
                                <thead>
                                    <tr className="text-left text-gray-500 border-b border-gray-100">
                                        <th className="py-2 pr-3 font-medium">#</th>
                                        <th className="py-2 pr-3 font-medium">No. Settlement</th>
                                        <th className="py-2 pr-3 font-medium">Tanggal</th>
                                        <th className="py-2 pr-3 font-medium text-right">Total</th>
                                        <th className="py-2 pr-3 font-medium">Jurnal</th>
                                        <th className="py-2 pr-3 font-medium">Status</th>
                                        <th className="py-2 font-medium">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {history.map((h: any) => {
                                        const isCancelled = h.status === 'Cancelled';
                                        const isCurrent = String(h.settlementId) === String(id);
                                        return (
                                            <tr
                                                key={h.settlementId}
                                                className={`border-b border-gray-50 last:border-0 ${isCurrent ? 'bg-blue-50/60' : ''}`}
                                            >
                                                <td className="py-2 pr-3 text-gray-400">{h.urutan}</td>
                                                <td className="py-2 pr-3 font-mono">
                                                    {isCurrent ? (
                                                        <span className="font-semibold">{h.settlementNo}</span>
                                                    ) : (
                                                        <button
                                                            className="text-blue-600 hover:underline"
                                                            onClick={() => navigate(`/petty-cash/settlement/${h.settlementId}`)}
                                                        >
                                                            {h.settlementNo}
                                                        </button>
                                                    )}
                                                </td>
                                                <td className="py-2 pr-3">{FormatDate(h.settlementDate)}</td>
                                                <td className="py-2 pr-3 text-right font-mono">{FormatNumber(h.totalAmount)}</td>
                                                <td className="py-2 pr-3 font-mono text-xs">
                                                    <div>{h.journalNo || '-'}</div>
                                                    {h.cancelJournalNo && (
                                                        <div className="text-red-600">balik: {h.cancelJournalNo}</div>
                                                    )}
                                                </td>
                                                <td className="py-2 pr-3">
                                                    <span
                                                        className={`px-2 py-0.5 rounded-full text-xs font-medium ${
                                                            isCancelled ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'
                                                        }`}
                                                    >
                                                        {isCancelled ? 'Dibatalkan' : h.status}
                                                    </span>
                                                </td>
                                                <td className="py-2 text-xs text-gray-500">
                                                    {isCancelled ? (
                                                        <>
                                                            {h.cancelReason || 'Tanpa alasan'}
                                                            {h.cancelledByName ? ` — ${h.cancelledByName}` : ''}
                                                        </>
                                                    ) : (
                                                        h.createdByName || '-'
                                                    )}
                                                </td>
                                            </tr>
                                        );
                                    })}
                                </tbody>
                            </table>
                        </div>
                    </CardContent>
                </Card>
            )}

            <Transition appear show={cancelOpen} as={Fragment}>
                <Dialog as="div" open={cancelOpen} onClose={() => !cancelling && setCancelOpen(false)} className="relative z-[999]">
                    <Transition.Child as={Fragment} enter="ease-out duration-300" enterFrom="opacity-0" enterTo="opacity-100" leave="ease-in duration-200" leaveFrom="opacity-100" leaveTo="opacity-0">
                        <div className="fixed inset-0 bg-black/50" />
                    </Transition.Child>
                    <div className="fixed inset-0 overflow-y-auto">
                        <div className="flex items-center justify-center min-h-screen p-4">
                            <Dialog.Panel className="relative bg-white dark:bg-[#0e1726] rounded-lg shadow-xl w-full max-w-lg">
                                <div className="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                                    <h3 className="text-base font-bold text-gray-900 dark:text-white">Batalkan Settlement</h3>
                                    <p className="text-xs text-gray-500 mt-0.5">{settlement.settlementNo}</p>
                                </div>
                                <div className="px-5 py-4 space-y-3">
                                    <div className="rounded-md bg-amber-50 border border-amber-100 px-3 py-2 text-sm text-amber-800">
                                        Sistem akan membuat <span className="font-semibold">jurnal pembalik</span> bertanggal sama dengan
                                        settlement ini, lalu mengembalikan request agar bisa di-settle ulang. Data lama tidak dihapus.
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-1.5">Alasan pembatalan</label>
                                        <textarea
                                            rows={3}
                                            className="flex w-full rounded-md border border-input bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
                                            value={cancelReason}
                                            onChange={(e) => setCancelReason(e.target.value)}
                                            placeholder="Contoh: salah pilih akun beban"
                                        />
                                    </div>
                                </div>
                                <div className="flex items-center justify-end gap-2 px-5 py-3 border-t border-gray-100 dark:border-gray-700">
                                    <Button variant="ghost" onClick={() => setCancelOpen(false)} disabled={cancelling}>Batal</Button>
                                    <Button
                                        onClick={handleCancel}
                                        disabled={cancelling}
                                        className="gap-2 bg-red-600 hover:bg-red-700 text-white"
                                    >
                                        {cancelling ? <Loader2 className="h-4 w-4 animate-spin" /> : <Ban className="h-4 w-4" />}
                                        Ya, Batalkan
                                    </Button>
                                </div>
                            </Dialog.Panel>
                        </div>
                    </div>
                </Dialog>
            </Transition>
        </div>
    );
};

export default PettyCashSettlementDetail;
