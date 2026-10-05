import { useEffect, useState } from 'react';
import { useDispatch } from 'react-redux';
import { useParams, useNavigate } from 'react-router-dom';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '@/store/themeConfigSlice';
import { useTranslation } from 'react-i18next';
import {
    useGetBatchPaymentDetailQuery,
    useCancelBatchPaymentMutation,
} from '@/store/api/accountPayable/accountPayableApiSlice';
import { useRoleAccess } from '@/hooks/useRoleAccess';
import { toastMessage } from '@/utils/toastUtils';
import ConfirmationModal from '@/components/ConfirmationModal';
import { FormatNumberRounded } from '@/utils/formatNumber';
import { FormatDate } from '@/utils/formatDate';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import {
    ArrowLeft,
    Loader2,
    CheckCircle2,
    Calendar,
    CreditCard,
    Receipt,
    Users,
    FileText,
    Percent,
    Calculator,
    Eye,
    Ban,
} from 'lucide-react';

const BatchPaymentDetailAP = () => {
    const dispatch = useDispatch();
    const { t } = useTranslation();
    const navigate = useNavigate();
    const { batchNumber } = useParams<{ batchNumber: string }>();

    const { canCancelPayment } = useRoleAccess();
    const [cancelBatch, { isLoading: cancellingBatch }] = useCancelBatchPaymentMutation();
    const [cancelOpen, setCancelOpen] = useState(false);
    const [cancelReason, setCancelReason] = useState('');

    const { data: batchData, isLoading, refetch } = useGetBatchPaymentDetailQuery(
        batchNumber || '',
        { skip: !batchNumber, refetchOnMountOrArgChange: true }
    );

    const batch = batchData?.data;

    useEffect(() => {
        dispatch(setPageTitle('Detail Batch Payment'));
        dispatch(setTitle('Detail Batch Payment'));
        dispatch(setBreadcrumbTitle(['Dashboard', 'Account Payable', 'Pembayaran', `Batch ${batchNumber || ''}`]));
    }, [dispatch, batchNumber]);

    if (isLoading) {
        return (
            <div className="flex items-center justify-center h-64">
                <Loader2 className="w-8 h-8 animate-spin text-primary" />
            </div>
        );
    }

    if (!batch) {
        return (
            <div className="p-6">
                <div className="text-center text-gray-500">Batch payment tidak ditemukan</div>
                <div className="mt-4 text-center">
                    <Button onClick={() => navigate('/account-payable/pembayaran')}>
                        <ArrowLeft className="w-4 h-4 mr-2" />
                        Kembali
                    </Button>
                </div>
            </div>
        );
    }

    const handleCancelBatch = async () => {
        try {
            const res: any = await cancelBatch({ paymentRef: batch.paymentRef, reason: cancelReason }).unwrap();
            const noJurnal = res?.data?.cancelJournalNo ?? res?.cancelJournalNo ?? '-';
            const jml = res?.data?.invoiceCount ?? res?.invoiceCount ?? batch.invoiceCount;
            toastMessage(`${jml} invoice dibatalkan pembayarannya. Jurnal pembalik: ${noJurnal}`, 'success');
            setCancelOpen(false);
            setCancelReason('');
            refetch();
        } catch (err: any) {
            toastMessage(err?.data?.message || 'Gagal membatalkan pembayaran', 'error');
        }
    };

    // Group invoices by supplier
    const supplierGroups: Record<string, typeof batch.invoices> = {};
    batch.invoices.forEach((inv: any) => {
        const key = inv.supplierName || 'Unknown';
        if (!supplierGroups[key]) supplierGroups[key] = [];
        supplierGroups[key].push(inv);
    });

    return (
        <div className="space-y-6">
            {/* Header */}
            <div className="flex items-center justify-between">
                <Button
                    variant="outline"
                    className="gap-2"
                    onClick={() => navigate('/account-payable/pembayaran')}
                >
                    <ArrowLeft className="w-4 h-4" />
                    Kembali
                </Button>
                {canCancelPayment && batch.paymentRef && (
                    <Button
                        variant="outline"
                        className="gap-2 border-red-200 text-red-600 hover:bg-red-50 hover:text-red-700"
                        onClick={() => setCancelOpen(true)}
                    >
                        <Ban className="w-4 h-4" />
                        Batalkan Seluruh Pembayaran
                    </Button>
                )}
            </div>

            <ConfirmationModal
                isOpen={cancelOpen}
                onClose={() => setCancelOpen(false)}
                onConfirm={handleCancelBatch}
                title="Batalkan Seluruh Pembayaran"
                confirmText="Ya, Batalkan Semua"
                cancelText="Batal"
                variant="danger"
                loading={cancellingBatch}
                keepOpenOnConfirm
                customContent={
                    <div className="space-y-3 text-left">
                        <div className="rounded-md bg-red-50 border border-red-200 px-3 py-2 text-sm text-red-800">
                            <span className="font-semibold">{batch.invoiceCount} invoice</span> senilai{' '}
                            <span className="font-mono font-semibold">{FormatNumberRounded(batch.totalPaid)}</span> akan
                            kembali ke status belum lunas, dan jurnal pembalik dibuat untuk seluruhnya. Data lama tidak
                            dihapus.
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1.5">Alasan pembatalan</label>
                            <textarea
                                rows={3}
                                className="flex w-full rounded-md border border-input bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
                                value={cancelReason}
                                onChange={(e) => setCancelReason(e.target.value)}
                                placeholder="Contoh: salah rekening bank tujuan"
                            />
                        </div>
                    </div>
                }
            />

            {/* Batch Info Card */}
            <Card>
                <CardHeader className="border-b bg-gradient-to-r from-success/5 to-transparent">
                    <CardTitle className="flex items-center gap-2 text-lg">
                        <Receipt className="w-5 h-5 text-success" />
                        Informasi Batch Payment
                        <span className="ml-2 px-2 py-1 rounded-full text-xs font-semibold bg-success/10 text-success">
                            LUNAS
                        </span>
                    </CardTitle>
                </CardHeader>
                <CardContent className="p-6">
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div>
                            <p className="text-sm text-gray-500">Batch Number</p>
                            <p className="font-semibold text-lg text-primary">{batch.paymentBatchNumber}</p>
                        </div>
                        <div>
                            <p className="text-sm text-gray-500">Tanggal Bayar</p>
                            <p className="font-semibold flex items-center gap-1">
                                <Calendar className="w-4 h-4 text-gray-400" />
                                {batch.paymentDate ? FormatDate(batch.paymentDate) : '-'}
                            </p>
                        </div>
                        <div>
                            <p className="text-sm text-gray-500">No. Referensi</p>
                            <p className="font-semibold">{batch.paymentRef || '-'}</p>
                        </div>
                        <div>
                            <p className="text-sm text-gray-500">Rekening Bank</p>
                            <p className="font-semibold">{batch.paymentCoaDesc || batch.paymentCoaCode || '-'}</p>
                        </div>
                        <div>
                            <p className="text-sm text-gray-500">Jumlah Invoice</p>
                            <p className="font-semibold text-lg">{batch.invoiceCount}</p>
                        </div>
                        <div>
                            <p className="text-sm text-gray-500">Total Dibayar</p>
                            <p className="font-semibold font-mono text-lg text-success">{FormatNumberRounded(batch.totalPaid)}</p>
                        </div>
                        <div>
                            <p className="text-sm text-gray-500">Total Diskon</p>
                            <p className="font-semibold font-mono text-lg text-success">{FormatNumberRounded(batch.totalDiscount)}</p>
                        </div>
                        <div>
                            <p className="text-sm text-gray-500">Total PPh 23</p>
                            <p className="font-semibold font-mono text-lg text-blue-600">{FormatNumberRounded(batch.totalPph23)}</p>
                        </div>
                    </div>
                </CardContent>
            </Card>

            {/* Invoice List Grouped by Supplier */}
            {Object.entries(supplierGroups).map(([supplier, invoices]) => {
                const groupPaid = invoices.reduce((sum: number, inv: any) => sum + (inv.paidAmount || 0), 0);
                const groupDiscount = invoices.reduce((sum: number, inv: any) => sum + (inv.discountAmount || 0), 0);
                const groupPph23 = invoices.reduce((sum: number, inv: any) => sum + (inv.pph23Amount || 0), 0);

                return (
                    <Card key={supplier}>
                        <CardHeader className="border-b bg-gradient-to-r from-gray-50 to-transparent">
                            <CardTitle className="flex items-center gap-2 text-lg">
                                <Users className="w-5 h-5 text-primary" />
                                {supplier}
                                <span className="text-sm font-normal text-gray-500">
                                    ({invoices.length} invoice)
                                </span>
                            </CardTitle>
                        </CardHeader>
                        <CardContent className="p-0">
                            <div className="overflow-x-auto">
                                <table className="w-full text-sm">
                                    <thead className="bg-gray-50 border-b">
                                        <tr>
                                            <th className="px-4 py-3 text-left font-semibold text-gray-700">No. Faktur</th>
                                            <th className="px-4 py-3 text-left font-semibold text-gray-700">No. PO</th>
                                            <th className="px-4 py-3 text-left font-semibold text-gray-700">Jatuh Tempo</th>
                                            <th className="px-4 py-3 text-right font-semibold text-gray-700">Total Transaksi</th>
                                            <th className="px-4 py-3 text-right font-semibold text-gray-700">Diskon</th>
                                            <th className="px-4 py-3 text-right font-semibold text-gray-700">PPh 23</th>
                                            <th className="px-4 py-3 text-right font-semibold text-gray-700">Dibayar</th>
                                            <th className="px-4 py-3 text-center font-semibold text-gray-700">Status</th>
                                            <th className="px-4 py-3 text-center font-semibold text-gray-700">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y">
                                        {invoices.map((inv: any) => (
                                            <tr key={inv.invoiceExchangeId} className="hover:bg-gray-50">
                                                <td className="px-4 py-3">
                                                    <div className="flex items-center gap-1.5">
                                                        <FileText className="w-3.5 h-3.5 text-gray-400" />
                                                        <span className="font-medium">{inv.invoiceNumber || '-'}</span>
                                                    </div>
                                                </td>
                                                <td className="px-4 py-3 text-gray-600">{inv.poNumber || '-'}</td>
                                                <td className="px-4 py-3 text-gray-600">{inv.dueDate ? FormatDate(inv.dueDate) : '-'}</td>
                                                <td className="px-4 py-3 text-right font-mono">{FormatNumberRounded(inv.totalTransaction || 0)}</td>
                                                <td className="px-4 py-3 text-right font-mono text-success">
                                                    {(inv.discountAmount || 0) > 0 ? `-${FormatNumberRounded(inv.discountAmount)}` : '-'}
                                                </td>
                                                <td className="px-4 py-3 text-right font-mono text-blue-600">
                                                    {(inv.pph23Amount || 0) > 0 ? `-${FormatNumberRounded(inv.pph23Amount)}` : '-'}
                                                </td>
                                                <td className="px-4 py-3 text-right font-mono font-semibold text-success">
                                                    {FormatNumberRounded(inv.paidAmount || 0)}
                                                </td>
                                                <td className="px-4 py-3 text-center">
                                                    <span className="px-2 py-1 rounded-full text-xs font-semibold bg-success/10 text-success">
                                                        {inv.paymentStatus}
                                                    </span>
                                                </td>
                                                <td className="px-4 py-3 text-center">
                                                    <Button
                                                        size="sm"
                                                        variant="outline"
                                                        className="gap-1.5 h-8"
                                                        onClick={() => navigate(`/account-payable/pembayaran/${inv.invoiceExchangeId}`)}
                                                    >
                                                        <Eye className="w-3.5 h-3.5" />
                                                        Detail
                                                    </Button>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>

                            {/* Supplier subtotal */}
                            <div className="px-4 py-3 bg-gray-50 border-t flex flex-wrap items-center justify-end gap-6 text-sm">
                                {groupDiscount > 0 && (
                                    <div className="text-success">
                                        <span>Diskon: </span>
                                        <span className="font-mono">-{FormatNumberRounded(groupDiscount)}</span>
                                    </div>
                                )}
                                {groupPph23 > 0 && (
                                    <div className="text-blue-600">
                                        <span>PPh 23: </span>
                                        <span className="font-mono">-{FormatNumberRounded(groupPph23)}</span>
                                    </div>
                                )}
                                <div className="font-bold text-success">
                                    <span>Dibayar: </span>
                                    <span className="font-mono">{FormatNumberRounded(groupPaid)}</span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                );
            })}

            {/* Grand Summary */}
            <Card>
                <CardHeader className="border-b">
                    <CardTitle className="flex items-center gap-2 text-lg">
                        <Calculator className="w-5 h-5 text-primary" />
                        Ringkasan Batch Payment
                    </CardTitle>
                </CardHeader>
                <CardContent className="p-6">
                    <div className="space-y-3 max-w-md ml-auto">
                        <div className="flex justify-between items-center">
                            <span className="text-gray-600">Total Transaksi</span>
                            <span className="font-mono">
                                {FormatNumberRounded(batch.invoices.reduce((sum: number, inv: any) => sum + (inv.totalTransaction || 0), 0))}
                            </span>
                        </div>
                        {batch.totalDiscount > 0 && (
                            <div className="flex justify-between items-center text-success">
                                <span className="flex items-center gap-1">
                                    <Percent className="w-4 h-4" />
                                    Total Diskon
                                </span>
                                <span className="font-mono">-{FormatNumberRounded(batch.totalDiscount)}</span>
                            </div>
                        )}
                        {batch.totalPph23 > 0 && (
                            <div className="flex justify-between items-center text-blue-600">
                                <span className="flex items-center gap-1">
                                    <Calculator className="w-4 h-4" />
                                    Total PPh 23
                                </span>
                                <span className="font-mono">-{FormatNumberRounded(batch.totalPph23)}</span>
                            </div>
                        )}
                        <hr />
                        <div className="flex justify-between items-center font-bold text-lg">
                            <span>Total Dibayar</span>
                            <span className="font-mono text-success">{FormatNumberRounded(batch.totalPaid)}</span>
                        </div>
                    </div>
                </CardContent>
            </Card>

            {/* Grand Total Bar */}
            <Card className="bg-gradient-to-r from-success to-success/80 text-white">
                <CardContent className="p-6">
                    <div className="flex justify-between items-center">
                        <div>
                            <p className="text-white/80 text-sm">Total Dibayar ({batch.invoiceCount} Invoice)</p>
                            <p className="text-sm text-white/60">{batch.paymentBatchNumber}</p>
                        </div>
                        <div className="text-right">
                            <p className="text-3xl font-bold font-mono">{FormatNumberRounded(batch.totalPaid)}</p>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    );
};

export default BatchPaymentDetailAP;
