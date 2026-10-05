import { useEffect, useState, useMemo } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { useLocation, useNavigate } from 'react-router-dom';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '@/store/themeConfigSlice';
import { useTranslation } from 'react-i18next';
import SelectSearch from 'react-select';
import Flatpickr from 'react-flatpickr';
import 'flatpickr/dist/flatpickr.css';
import {
    useProcessAccountPayableBatchPaymentMutation,
    useGetAccountPayableBatchPpnQuery,
} from '@/store/api/accountPayable/accountPayableApiSlice';
import { useGetOptionBankQuery } from '@/store/api/bank/bankApiSlice';
import { FormatNumberRounded } from '@/utils/formatNumber';
import { FormatDate, formatDateForAPI } from '@/utils/formatDate';
import { toastMessage } from '@/utils/toastUtils';
import ConfirmationModal from '@/components/ConfirmationModal';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import {
    ArrowLeft,
    Banknote,
    Percent,
    CreditCard,
    CheckCircle2,
    Loader2,
    Calculator,
    Calendar,
    Users,
    FileText,
} from 'lucide-react';

interface SelectedInvoice {
    invoiceExchangeId: number;
    supplierName: string;
    poNumber: string;
    invoiceNumber: string;
    receiptDate: string;
    dueDate: string;
    totalTransaction: number;
    paidAmount: number;
    outstandingAmount: number;
    paymentStatus: string;
    agingDays: number;
    agingBucket: string;
}

interface InvoiceAdjustment {
    discountPercent: number;
    discountAmount: number;
    discountMode: 'percent' | 'amount';
    pph23Percent: number;
    pph23Amount: number;
    pph23Mode: 'percent' | 'amount';
    pph23Include: boolean;
}

const BatchPaymentAP = () => {
    const dispatch = useDispatch();
    const { t } = useTranslation();
    const navigate = useNavigate();
    const location = useLocation();
    const isRtl = useSelector((state: any) => state.themeConfig.rtlClass) === 'rtl';

    const selectedInvoices: SelectedInvoice[] = location.state?.selectedInvoices || [];

    // Redirect if no invoices selected
    useEffect(() => {
        if (!selectedInvoices || selectedInvoices.length === 0) {
            navigate('/account-payable/pembayaran');
        }
    }, [selectedInvoices, navigate]);

    useEffect(() => {
        dispatch(setPageTitle('Batch Pembayaran AP'));
        dispatch(setTitle('Batch Pembayaran AP'));
        dispatch(setBreadcrumbTitle(['Dashboard', 'Account Payable', 'Pembayaran', 'Batch Payment']));
    }, [dispatch]);

    // API calls
    const { data: bankList, isLoading: isLoadingBank } = useGetOptionBankQuery(
        { search: '', page: -1, pageSize: -1 }, { skip: false }
    );
    const [processBatchPayment, { isLoading: isProcessing }] = useProcessAccountPayableBatchPaymentMutation();
    const { data: batchPpnData } = useGetAccountPayableBatchPpnQuery(
        { invoiceExchangeIds: selectedInvoices.map((inv) => inv.invoiceExchangeId) },
        { skip: selectedInvoices.length === 0 }
    );

    // PPN lookup by invoiceExchangeId
    const ppnLookup = useMemo(() => {
        const lookup: Record<number, number> = {};
        const items = batchPpnData?.data || batchPpnData || [];
        if (Array.isArray(items)) {
            items.forEach((item: any) => {
                lookup[item.invoiceExchangeId] = item.ppnAmount || 0;
            });
        }
        return lookup;
    }, [batchPpnData]);

    // Shared payment form states
    const [paymentDate, setPaymentDate] = useState<Date>(new Date());
    const [selectedBank, setSelectedBank] = useState<any>(null);
    const [paymentRef, setPaymentRef] = useState<string>('');
    const [showPaymentModal, setShowPaymentModal] = useState(false);

    // Per-invoice adjustment state (discount + PPH23)
    const [invoiceAdjustments, setInvoiceAdjustments] = useState<Record<number, InvoiceAdjustment>>({});

    // Group invoices by supplier
    const supplierGroups = useMemo(() => {
        const groups: Record<string, SelectedInvoice[]> = {};
        selectedInvoices.forEach((inv) => {
            const key = inv.supplierName || 'Unknown';
            if (!groups[key]) groups[key] = [];
            groups[key].push(inv);
        });
        return groups;
    }, [selectedInvoices]);

    const defaultAdjustment: InvoiceAdjustment = {
        discountPercent: 0, discountAmount: 0, discountMode: 'percent',
        pph23Percent: 0, pph23Amount: 0, pph23Mode: 'percent', pph23Include: false,
    };

    // Get adjustment for an invoice
    const getAdj = (invoiceId: number): InvoiceAdjustment => {
        return invoiceAdjustments[invoiceId] || defaultAdjustment;
    };

    // Update adjustment for an invoice
    const updateAdj = (invoiceId: number, updates: Partial<InvoiceAdjustment>) => {
        setInvoiceAdjustments((prev) => ({
            ...prev,
            [invoiceId]: { ...getAdj(invoiceId), ...updates },
        }));
    };

    // Update discount for an invoice
    const updateDiscount = (invoiceId: number, field: 'percent' | 'amount', value: number, outstanding: number) => {
        if (field === 'percent') {
            updateAdj(invoiceId, {
                discountPercent: value,
                discountAmount: (outstanding * value) / 100,
                discountMode: 'percent',
            });
        } else {
            updateAdj(invoiceId, {
                discountPercent: outstanding > 0 ? (value / outstanding) * 100 : 0,
                discountAmount: value,
                discountMode: 'amount',
            });
        }
    };

    // Update PPH23 for an invoice
    const updatePph23 = (invoiceId: number, field: 'percent' | 'amount', value: number, afterDiscount: number) => {
        if (field === 'percent') {
            updateAdj(invoiceId, {
                pph23Percent: value,
                pph23Amount: (afterDiscount * value) / 100,
                pph23Mode: 'percent',
            });
        } else {
            updateAdj(invoiceId, {
                pph23Percent: afterDiscount > 0 ? (value / afterDiscount) * 100 : 0,
                pph23Amount: value,
                pph23Mode: 'amount',
            });
        }
    };

    // Computed discount amount for an invoice
    const getComputedDiscountAmount = (invoiceId: number, outstanding: number): number => {
        const adj = getAdj(invoiceId);
        if (adj.discountMode === 'percent') return (outstanding * adj.discountPercent) / 100;
        return adj.discountAmount;
    };

    // Computed PPH23 amount for an invoice
    const getComputedPph23Amount = (invoiceId: number, afterDiscount: number): number => {
        const adj = getAdj(invoiceId);
        if (!adj.pph23Include) return 0;
        if (adj.pph23Mode === 'percent') return (afterDiscount * adj.pph23Percent) / 100;
        return adj.pph23Amount;
    };

    // Computed values per supplier group
    const supplierSummaries = useMemo(() => {
        const summaries: Record<string, {
            totalOutstanding: number;
            totalDiscount: number;
            totalPph23: number;
            totalPpn: number;
            netPayable: number;
        }> = {};

        Object.entries(supplierGroups).forEach(([supplier, invoices]) => {
            let totalOutstanding = 0;
            let totalDiscount = 0;
            let totalPph23 = 0;
            let totalPpn = 0;

            invoices.forEach((inv) => {
                const outstanding = inv.outstandingAmount;
                const discountAmt = getComputedDiscountAmount(inv.invoiceExchangeId, outstanding);
                const afterDiscount = outstanding - discountAmt;
                const pph23Amt = getComputedPph23Amount(inv.invoiceExchangeId, afterDiscount);
                const ppnAmt = ppnLookup[inv.invoiceExchangeId] || 0;
                totalOutstanding += outstanding;
                totalDiscount += discountAmt;
                totalPph23 += pph23Amt;
                totalPpn += ppnAmt;
            });

            summaries[supplier] = {
                totalOutstanding,
                totalDiscount,
                totalPph23,
                totalPpn,
                netPayable: totalOutstanding - totalDiscount - totalPph23,
            };
        });

        return summaries;
    }, [supplierGroups, invoiceAdjustments, ppnLookup]);

    // Grand totals
    const grandTotals = useMemo(() => {
        let totalOutstanding = 0;
        let totalDiscount = 0;
        let totalPph23 = 0;
        let totalPpn = 0;
        let totalToPay = 0;

        Object.values(supplierSummaries).forEach((s) => {
            totalOutstanding += s.totalOutstanding;
            totalDiscount += s.totalDiscount;
            totalPph23 += s.totalPph23;
            totalPpn += s.totalPpn;
            totalToPay += s.netPayable;
        });

        return { totalOutstanding, totalDiscount, totalPph23, totalPpn, totalToPay };
    }, [supplierSummaries]);

    // Bank options
    const getBankOptions = () => {
        if (!bankList) return [];
        let items: any[] = Array.isArray(bankList) ? bankList : bankList.data || [];
        return items
            .map((b: any) => ({
                label: b.label || b.bankName || '',
                value: b.value || b.coaCode || b.desc || '',
                desc: b.desc || b.coaCode || '',
            }))
            .filter((i: any) => i.label && i.value);
    };

    const bankOptions = getBankOptions();

    const selectStyles = {
        control: (base: any) => ({
            ...base,
            minHeight: '40px',
            borderColor: '#e2e8f0',
            '&:hover': { borderColor: '#cbd5e1' },
        }),
        menuPortal: (base: any) => ({ ...base, zIndex: 9999 }),
    };

    const handleBatchPayment = async () => {
        if (!selectedBank) {
            toastMessage('Pilih rekening pembayaran', 'error');
            return;
        }

        try {
            const payload = {
                paymentDate: formatDateForAPI(paymentDate),
                coaCode: selectedBank.desc || selectedBank.value,
                coaDesc: selectedBank.label,
                paymentRef: paymentRef,
                totalAmount: grandTotals.totalToPay,
                invoices: selectedInvoices.map((inv) => {
                    const adj = getAdj(inv.invoiceExchangeId);
                    const outstanding = inv.outstandingAmount;
                    const discountAmt = getComputedDiscountAmount(inv.invoiceExchangeId, outstanding);
                    const afterDiscount = outstanding - discountAmt;
                    const pph23Amt = getComputedPph23Amount(inv.invoiceExchangeId, afterDiscount);
                    return {
                        invoiceExchangeId: inv.invoiceExchangeId,
                        discountPercent: adj.discountPercent || 0,
                        discountAmount: discountAmt,
                        pph23Percent: adj.pph23Include ? (adj.pph23Percent || 0) : 0,
                        pph23Amount: pph23Amt,
                    };
                }),
            };

            await processBatchPayment(payload).unwrap();
            toastMessage(`Batch pembayaran ${selectedInvoices.length} invoice berhasil diproses`, 'success');
            setShowPaymentModal(false);
            navigate('/account-payable/pembayaran');
        } catch (error: any) {
            toastMessage(error?.data?.message || 'Gagal memproses batch pembayaran', 'error');
        }
    };

    if (!selectedInvoices || selectedInvoices.length === 0) {
        return (
            <div className="flex items-center justify-center h-64">
                <Loader2 className="w-8 h-8 animate-spin text-primary" />
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
                    onClick={() => navigate('/account-payable/pembayaran')}
                >
                    <ArrowLeft className="w-4 h-4" />
                    Kembali
                </Button>
                <span className="px-3 py-1.5 rounded-full text-sm font-semibold bg-primary/10 text-primary">
                    {selectedInvoices.length} Invoice Dipilih
                </span>
            </div>

            {/* Shared Payment Form */}
            <Card className="border-primary/20 bg-primary/5">
                <CardHeader className="border-b border-primary/10">
                    <CardTitle className="flex items-center gap-2 text-lg text-primary">
                        <CreditCard className="w-5 h-5" />
                        Form Pembayaran Batch
                    </CardTitle>
                </CardHeader>
                <CardContent className="p-6">
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <label className="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
                                <Calendar className="w-4 h-4" />
                                Tanggal Bayar
                            </label>
                            <Flatpickr
                                options={{ dateFormat: 'd/m/Y', position: isRtl ? 'auto right' : 'auto left' }}
                                className="flex h-10 w-full items-center justify-between rounded-md border border-input bg-white px-3 py-2 text-sm"
                                value={paymentDate}
                                onChange={(dates: Date[]) => dates[0] && setPaymentDate(dates[0])}
                            />
                        </div>
                        <div>
                            <label className="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
                                <Banknote className="w-4 h-4" />
                                Rekening Bank *
                            </label>
                            <SelectSearch
                                placeholder="Pilih Bank"
                                options={bankOptions}
                                isLoading={isLoadingBank}
                                onChange={(opt: any) => setSelectedBank(opt)}
                                value={selectedBank}
                                styles={selectStyles}
                                menuPortalTarget={document.body}
                            />
                        </div>
                        <div>
                            <label className="text-sm font-medium text-gray-700 mb-2 block">
                                No. Referensi
                            </label>
                            <input
                                type="text"
                                className="flex h-10 w-full items-center justify-between rounded-md border border-input bg-white px-3 py-2 text-sm"
                                placeholder="No. Giro/Transfer"
                                value={paymentRef}
                                onChange={(e) => setPaymentRef(e.target.value)}
                            />
                        </div>
                    </div>
                </CardContent>
            </Card>

            {/* Supplier Groups */}
            {Object.entries(supplierGroups).map(([supplier, invoices]) => {
                const summary = supplierSummaries[supplier];

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
                            {/* Invoice table */}
                            <div className="overflow-x-auto">
                                <table className="w-full text-sm">
                                    <thead className="bg-gray-50 border-b">
                                        <tr>
                                            <th className="px-4 py-3 text-left font-semibold text-gray-700">No. Faktur</th>
                                            <th className="px-4 py-3 text-left font-semibold text-gray-700">No. PO</th>
                                            <th className="px-4 py-3 text-left font-semibold text-gray-700">Jatuh Tempo</th>
                                            <th className="px-4 py-3 text-right font-semibold text-gray-700">Outstanding</th>
                                            <th className="px-4 py-3 text-right font-semibold text-gray-700">PPN</th>
                                            <th className="px-4 py-3 text-center font-semibold text-gray-700">Diskon (%)</th>
                                            <th className="px-4 py-3 text-center font-semibold text-gray-700">Diskon (Rp)</th>
                                            <th className="px-4 py-3 text-center font-semibold text-gray-700">PPh23</th>
                                            <th className="px-4 py-3 text-center font-semibold text-gray-700">PPh23 (%)</th>
                                            <th className="px-4 py-3 text-center font-semibold text-gray-700">PPh23 (Rp)</th>
                                            <th className="px-4 py-3 text-right font-semibold text-gray-700">Net</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y">
                                        {invoices.map((inv) => {
                                            const outstanding = inv.outstandingAmount;
                                            const adj = getAdj(inv.invoiceExchangeId);
                                            const discountAmt = getComputedDiscountAmount(inv.invoiceExchangeId, outstanding);
                                            const afterDiscount = outstanding - discountAmt;
                                            const pph23Amt = getComputedPph23Amount(inv.invoiceExchangeId, afterDiscount);
                                            const net = afterDiscount - pph23Amt;

                                            return (
                                                <tr key={inv.invoiceExchangeId} className="hover:bg-gray-50">
                                                    <td className="px-4 py-3">
                                                        <div className="flex items-center gap-1.5">
                                                            <FileText className="w-3.5 h-3.5 text-gray-400" />
                                                            <span className="font-medium">{inv.invoiceNumber || '-'}</span>
                                                        </div>
                                                    </td>
                                                    <td className="px-4 py-3 text-gray-600">{inv.poNumber || '-'}</td>
                                                    <td className="px-4 py-3 text-gray-600">{inv.dueDate ? FormatDate(inv.dueDate) : '-'}</td>
                                                    <td className="px-4 py-3 text-right font-mono font-medium">{FormatNumberRounded(outstanding)}</td>
                                                    <td className="px-4 py-3 text-right font-mono text-gray-600">
                                                        {FormatNumberRounded(ppnLookup[inv.invoiceExchangeId] || 0)}
                                                    </td>
                                                    <td className="px-4 py-3">
                                                        <input
                                                            type="number"
                                                            min="0"
                                                            max="100"
                                                            step="0.1"
                                                            className="w-20 h-8 rounded-md border border-input bg-white px-2 text-sm text-center mx-auto block"
                                                            placeholder="0"
                                                            value={adj.discountPercent || ''}
                                                            onChange={(e) =>
                                                                updateDiscount(inv.invoiceExchangeId, 'percent', Number(e.target.value) || 0, outstanding)
                                                            }
                                                        />
                                                    </td>
                                                    <td className="px-4 py-3">
                                                        <input
                                                            type="number"
                                                            min="0"
                                                            step="1000"
                                                            className="w-28 h-8 rounded-md border border-input bg-white px-2 text-sm text-right mx-auto block"
                                                            placeholder="0"
                                                            value={adj.discountAmount || ''}
                                                            onChange={(e) =>
                                                                updateDiscount(inv.invoiceExchangeId, 'amount', Number(e.target.value) || 0, outstanding)
                                                            }
                                                        />
                                                    </td>
                                                    <td className="px-4 py-3 text-center">
                                                        <input
                                                            type="checkbox"
                                                            checked={adj.pph23Include}
                                                            onChange={(e) => {
                                                                const include = e.target.checked;
                                                                if (include) {
                                                                    // Default 2% when enabling
                                                                    updateAdj(inv.invoiceExchangeId, {
                                                                        pph23Include: true,
                                                                        pph23Percent: adj.pph23Percent || 2,
                                                                        pph23Amount: (afterDiscount * (adj.pph23Percent || 2)) / 100,
                                                                        pph23Mode: 'percent',
                                                                    });
                                                                } else {
                                                                    updateAdj(inv.invoiceExchangeId, { pph23Include: false });
                                                                }
                                                            }}
                                                            className="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary"
                                                        />
                                                    </td>
                                                    <td className="px-4 py-3">
                                                        {adj.pph23Include && (
                                                            <input
                                                                type="number"
                                                                min="0"
                                                                max="100"
                                                                step="0.1"
                                                                className="w-20 h-8 rounded-md border border-input bg-white px-2 text-sm text-center mx-auto block"
                                                                placeholder="2"
                                                                value={adj.pph23Percent || ''}
                                                                onChange={(e) =>
                                                                    updatePph23(inv.invoiceExchangeId, 'percent', Number(e.target.value) || 0, afterDiscount)
                                                                }
                                                            />
                                                        )}
                                                    </td>
                                                    <td className="px-4 py-3">
                                                        {adj.pph23Include && (
                                                            <input
                                                                type="number"
                                                                min="0"
                                                                step="1000"
                                                                className="w-28 h-8 rounded-md border border-input bg-white px-2 text-sm text-right mx-auto block"
                                                                placeholder="0"
                                                                value={adj.pph23Amount || ''}
                                                                onChange={(e) =>
                                                                    updatePph23(inv.invoiceExchangeId, 'amount', Number(e.target.value) || 0, afterDiscount)
                                                                }
                                                            />
                                                        )}
                                                    </td>
                                                    <td className="px-4 py-3 text-right font-mono font-semibold text-primary">
                                                        {FormatNumberRounded(net)}
                                                    </td>
                                                </tr>
                                            );
                                        })}
                                    </tbody>
                                </table>
                            </div>

                            {/* Supplier subtotal */}
                            <div className="px-4 py-3 bg-gray-50 border-t flex flex-wrap items-center justify-end gap-6 text-sm">
                                <div>
                                    <span className="text-gray-500">Outstanding: </span>
                                    <span className="font-mono font-medium">{FormatNumberRounded(summary?.totalOutstanding || 0)}</span>
                                </div>
                                {(summary?.totalPpn || 0) > 0 && (
                                    <div className="text-orange-600">
                                        <span>PPN: </span>
                                        <span className="font-mono">{FormatNumberRounded(summary?.totalPpn || 0)}</span>
                                    </div>
                                )}
                                <div className="text-success">
                                    <span>Diskon: </span>
                                    <span className="font-mono">-{FormatNumberRounded(summary?.totalDiscount || 0)}</span>
                                </div>
                                {(summary?.totalPph23 || 0) > 0 && (
                                    <div className="text-blue-600">
                                        <span>PPh 23: </span>
                                        <span className="font-mono">-{FormatNumberRounded(summary?.totalPph23 || 0)}</span>
                                    </div>
                                )}
                                <div className="font-bold text-primary">
                                    <span>Net: </span>
                                    <span className="font-mono">{FormatNumberRounded(summary?.netPayable || 0)}</span>
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
                        Ringkasan Pembayaran Batch
                    </CardTitle>
                </CardHeader>
                <CardContent className="p-6">
                    <div className="space-y-3 max-w-md ml-auto">
                        <div className="flex justify-between items-center">
                            <span className="text-gray-600">Total Outstanding</span>
                            <span className="font-mono">{FormatNumberRounded(grandTotals.totalOutstanding)}</span>
                        </div>
                        {grandTotals.totalPpn > 0 && (
                            <div className="flex justify-between items-center text-orange-600">
                                <span className="flex items-center gap-1">
                                    <Calculator className="w-4 h-4" />
                                    Total PPN
                                </span>
                                <span className="font-mono">{FormatNumberRounded(grandTotals.totalPpn)}</span>
                            </div>
                        )}
                        <div className="flex justify-between items-center text-success">
                            <span className="flex items-center gap-1">
                                <Percent className="w-4 h-4" />
                                Total Diskon
                            </span>
                            <span className="font-mono">-{FormatNumberRounded(grandTotals.totalDiscount)}</span>
                        </div>
                        {grandTotals.totalPph23 > 0 && (
                            <div className="flex justify-between items-center text-blue-600">
                                <span className="flex items-center gap-1">
                                    <Calculator className="w-4 h-4" />
                                    Total PPh 23
                                </span>
                                <span className="font-mono">-{FormatNumberRounded(grandTotals.totalPph23)}</span>
                            </div>
                        )}
                        <hr />
                        <div className="flex justify-between items-center font-bold text-lg">
                            <span>Total Bayar</span>
                            <span className="font-mono text-primary">{FormatNumberRounded(grandTotals.totalToPay)}</span>
                        </div>
                    </div>
                </CardContent>
            </Card>

            {/* Action Bar */}
            <Card className="bg-gradient-to-r from-primary to-primary/80 text-white">
                <CardContent className="p-6">
                    <div className="flex justify-between items-center">
                        <div>
                            <p className="text-white/80 text-sm">Total Pembayaran Batch ({selectedInvoices.length} Invoice)</p>
                        </div>
                        <div className="flex items-center gap-6">
                            <p className="text-3xl font-bold font-mono">{FormatNumberRounded(grandTotals.totalToPay)}</p>
                            <Button
                                onClick={() => setShowPaymentModal(true)}
                                disabled={!selectedBank || isProcessing || grandTotals.totalToPay <= 0}
                                className="bg-white text-primary hover:bg-gray-100 gap-2"
                            >
                                <CheckCircle2 className="w-4 h-4" />
                                {isProcessing ? 'Processing...' : 'Proses Pembayaran Batch'}
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>

            {/* Confirmation Modal */}
            <ConfirmationModal
                confirmText="Konfirmasi Bayar Batch"
                message={
                    <div className="text-left space-y-2">
                        <p>Anda akan membayar <strong>{selectedInvoices.length} invoice</strong> sekaligus:</p>
                        <div className="bg-gray-50 p-3 rounded-md text-sm space-y-1 max-h-40 overflow-y-auto">
                            {selectedInvoices.map((inv) => (
                                <div key={inv.invoiceExchangeId} className="flex justify-between">
                                    <span>{inv.invoiceNumber} ({inv.supplierName})</span>
                                    <span className="font-mono">{FormatNumberRounded(inv.outstandingAmount)}</span>
                                </div>
                            ))}
                        </div>
                        <div className="bg-gray-50 p-3 rounded-md text-sm space-y-1 mt-2">
                            <div className="flex justify-between">
                                <span>Total Outstanding:</span>
                                <span className="font-mono">{FormatNumberRounded(grandTotals.totalOutstanding)}</span>
                            </div>
                            {grandTotals.totalPpn > 0 && (
                                <div className="flex justify-between text-orange-600">
                                    <span>PPN:</span>
                                    <span className="font-mono">{FormatNumberRounded(grandTotals.totalPpn)}</span>
                                </div>
                            )}
                            <div className="flex justify-between text-success">
                                <span>Diskon:</span>
                                <span className="font-mono">-{FormatNumberRounded(grandTotals.totalDiscount)}</span>
                            </div>
                            {grandTotals.totalPph23 > 0 && (
                                <div className="flex justify-between text-blue-600">
                                    <span>PPh 23:</span>
                                    <span className="font-mono">-{FormatNumberRounded(grandTotals.totalPph23)}</span>
                                </div>
                            )}
                            <hr />
                            <div className="flex justify-between font-bold">
                                <span>Total Bayar:</span>
                                <span className="font-mono">{FormatNumberRounded(grandTotals.totalToPay)}</span>
                            </div>
                        </div>
                        <p className="text-gray-500">Lanjutkan?</p>
                    </div>
                }
                isOpen={showPaymentModal}
                onClose={() => !isProcessing && setShowPaymentModal(false)}
                onConfirm={handleBatchPayment}
            />
        </div>
    );
};

export default BatchPaymentAP;
