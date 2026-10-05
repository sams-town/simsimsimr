import { useState, useEffect, Fragment, useMemo } from 'react';
import { Dialog, Transition } from '@headlessui/react';
import CreatableSelect from 'react-select/creatable';
import Flatpickr from 'react-flatpickr';
import 'flatpickr/dist/flatpickr.css';
import { X, Save, Loader2 } from 'lucide-react';
import { Button } from '@/components/ui/button';
import IconCalendar from '@/components/Icon/IconCalendar';
import { FormatNumberRounded } from '@/utils/formatNumber';
import { toastMessage } from '@/utils/toastUtils';
import { useGetInvoiceExchangeSupplierOptionQuery, useCreateManualInvoiceExchangeMutation } from '@/store/api/invoiceExchange/invoiceExchangeApiSlice';

interface ManualInvoiceModalProps {
    isOpen: boolean;
    onClose: () => void;
}

const toApiDate = (date: Date | null): string | null => {
    if (!date) return null;
    return new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate())).toISOString();
};

const round = (n: number) => Math.round((n + Number.EPSILON) * 100) / 100;

const selectStyles = {
    control: (base: any) => ({ ...base, minHeight: '40px', borderColor: '#e2e8f0', '&:hover': { borderColor: '#cbd5e1' } }),
    menuPortal: (base: any) => ({ ...base, zIndex: 99999 }),
};

const ACCENT = '#4361ee';

const ManualInvoiceModal = ({ isOpen, onClose }: ManualInvoiceModalProps) => {
    const [supplier, setSupplier] = useState<any>(null);
    const [supplierCode, setSupplierCode] = useState('');
    const [invoiceNumber, setInvoiceNumber] = useState('');
    const [receiptDate, setReceiptDate] = useState<Date | null>(new Date());
    const [dueDate, setDueDate] = useState<Date | null>(null);
    const [amount, setAmount] = useState<number>(0);
    const [discountPercent, setDiscountPercent] = useState<number>(0);
    const [discountAmount, setDiscountAmount] = useState<number>(0);
    const [pph23Percent, setPph23Percent] = useState<number>(0);
    const [pph23Amount, setPph23Amount] = useState<number>(0);
    const [ppnPercent, setPpnPercent] = useState<number>(0);
    const [ppnAmount, setPpnAmount] = useState<number>(0);

    const [searchSupplier, setSearchSupplier] = useState('');

    useEffect(() => {
        if (isOpen) {
            setSupplier(null); setSupplierCode(''); setInvoiceNumber('');
            setReceiptDate(new Date()); setDueDate(null); setAmount(0);
            setDiscountPercent(0); setDiscountAmount(0); setPph23Percent(0); setPph23Amount(0);
            setPpnPercent(0); setPpnAmount(0);
            setSearchSupplier('');
        }
    }, [isOpen]);

    const { data: supplierList, isLoading: isLoadingSupplier } = useGetInvoiceExchangeSupplierOptionQuery(
        { keyword: searchSupplier || '', page: 1, pageSize: 20 },
        { skip: !isOpen }
    );

    const supplierOptions = useMemo(() => {
        if (!supplierList) return [];
        const items: any[] = Array.isArray(supplierList) ? supplierList : supplierList.data?.data || supplierList.data || [];
        return items
            .map((s: any) => ({ label: s.label || s.supplierName || s.value || '', value: s.value || s.label || '' }))
            .filter((i: any) => i.label);
    }, [supplierList]);

    const [createManual, { isLoading: saving }] = useCreateManualInvoiceExchangeMutation();

    const dpp = Math.max(amount - discountAmount, 0);
    // Hutang = (nilai barang - diskon) + PPN + PPh23. PPN dan PPh23 MENAMBAH tagihan
    // untuk invoice non-PO - berbeda dari jalur PO, di mana PPh23 dipotong dan PPN
    // hanya informasi.
    const outstanding = Math.max(dpp + ppnAmount + pph23Amount, 0);

    // Pajak selalu dihitung ulang dari DPP terbaru, supaya nilai persen dan rupiah
    // tidak saling bertentangan setelah jumlah atau diskon diubah.
    const applyTaxesFor = (base: number) => {
        if (ppnPercent > 0) setPpnAmount(round(base * ppnPercent / 100));
        if (pph23Percent > 0) setPph23Amount(round(base * pph23Percent / 100));
    };

    const onAmountChange = (val: number) => {
        setAmount(val);
        const d = discountPercent > 0 ? round(val * discountPercent / 100) : discountAmount;
        setDiscountAmount(d);
        applyTaxesFor(Math.max(val - d, 0));
    };
    const onDiscountPercentChange = (pct: number) => {
        setDiscountPercent(pct);
        const d = round(amount * pct / 100);
        setDiscountAmount(d);
        applyTaxesFor(Math.max(amount - d, 0));
    };
    const onDiscountAmountChange = (rp: number) => {
        setDiscountAmount(rp);
        setDiscountPercent(amount > 0 ? round(rp / amount * 100) : 0);
        applyTaxesFor(Math.max(amount - rp, 0));
    };
    const onPpnPercentChange = (pct: number) => {
        setPpnPercent(pct);
        setPpnAmount(round(dpp * pct / 100));
    };
    const onPph23PercentChange = (pct: number) => {
        setPph23Percent(pct);
        setPph23Amount(round(dpp * pct / 100));
    };

    const handleSubmit = async () => {
        const supplierName = (supplier?.label || '').trim();
        if (!supplierName) { toastMessage('Nama supplier wajib diisi', 'error'); return; }
        if (!invoiceNumber.trim()) { toastMessage('No. invoice wajib diisi', 'error'); return; }
        if (!dueDate) { toastMessage('Jatuh tempo wajib diisi', 'error'); return; }
        if (!amount || amount <= 0) { toastMessage('Jumlah harus lebih dari 0', 'error'); return; }

        try {
            await createManual({
                supplierName,
                supplierCode: supplierCode.trim() || null,
                invoiceNumber: invoiceNumber.trim(),
                receiptDate: toApiDate(receiptDate),
                dueDate: toApiDate(dueDate),
                totalTransaction: amount,
                discountPercent,
                discountAmount,
                pph23Percent,
                pph23Amount,
                ppnPercent,
                ppnAmount,
            }).unwrap();
            toastMessage('Data invoice berhasil ditambahkan', 'success');
            onClose();
        } catch (error: any) {
            toastMessage(error?.data?.message || 'Gagal menambahkan data', 'error');
        }
    };

    const inputCls = 'flex h-10 w-full rounded-md border border-input bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring';

    return (
        <Transition appear show={isOpen} as={Fragment}>
            <Dialog as="div" open={isOpen} onClose={onClose} className="relative z-[999]">
                <Transition.Child as={Fragment} enter="ease-out duration-300" enterFrom="opacity-0" enterTo="opacity-100" leave="ease-in duration-200" leaveFrom="opacity-100" leaveTo="opacity-0">
                    <div className="fixed inset-0 bg-black/50" />
                </Transition.Child>

                <div className="fixed inset-0 overflow-y-auto">
                    <div className="flex items-center justify-center min-h-screen p-4">
                        <Dialog.Panel className="relative bg-white dark:bg-[#0e1726] rounded-lg shadow-xl w-full max-w-3xl max-h-[92vh] flex flex-col">
                            {/* Header */}
                            <div className="flex items-center justify-between px-5 py-3 border-b border-gray-100 dark:border-gray-700">
                                <div>
                                    <h3 className="text-base font-bold text-gray-900 dark:text-white">Input Manual Tukar Faktur (Non-PO)</h3>
                                    <p className="text-xs text-gray-500">Untuk data AP yang masuk tanpa melalui PO</p>
                                </div>
                                <button onClick={onClose} className="text-gray-400 hover:text-gray-600 p-1"><X className="h-5 w-5" /></button>
                            </div>

                            {/* Body */}
                            <div className="px-5 py-4 overflow-auto flex-1 space-y-4">
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-1.5">Supplier <span className="text-red-500">*</span></label>
                                        <CreatableSelect
                                            options={supplierOptions}
                                            value={supplier}
                                            isLoading={isLoadingSupplier}
                                            onInputChange={(v) => setSearchSupplier(v)}
                                            onChange={(opt: any) => setSupplier(opt)}
                                            placeholder="Pilih atau ketik supplier baru..."
                                            formatCreateLabel={(v) => `Buat supplier baru: "${v}"`}
                                            isClearable
                                            styles={selectStyles}
                                            menuPortalTarget={document.body}
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-1.5">Kode Supplier <span className="text-gray-400 text-xs">(opsional)</span></label>
                                        <input type="text" className={inputCls} value={supplierCode} onChange={(e) => setSupplierCode(e.target.value)} placeholder="cth: NM001" />
                                    </div>
                                </div>

                                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-1.5">No. Invoice <span className="text-red-500">*</span></label>
                                        <input type="text" className={inputCls} value={invoiceNumber} onChange={(e) => setInvoiceNumber(e.target.value)} placeholder="No. invoice / nota" />
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Terima</label>
                                        <div className="relative">
                                            <Flatpickr value={receiptDate || undefined} options={{ dateFormat: 'd/m/Y' }} className={`${inputCls} pl-9`} placeholder="Tanggal terima" onChange={(d) => setReceiptDate(d[0] || null)} />
                                            <IconCalendar className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
                                        </div>
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-1.5">Jatuh Tempo <span className="text-red-500">*</span></label>
                                        <div className="relative">
                                            <Flatpickr value={dueDate || undefined} options={{ dateFormat: 'd/m/Y' }} className={`${inputCls} pl-9`} placeholder="Jatuh tempo" onChange={(d) => setDueDate(d[0] || null)} />
                                            <IconCalendar className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1.5">Jumlah (Total Transaksi) <span className="text-red-500">*</span></label>
                                    <input type="number" min="0" className={`${inputCls} text-right font-medium`} value={amount || ''} onChange={(e) => onAmountChange(parseFloat(e.target.value) || 0)} placeholder="0" />
                                </div>

                                {/* Diskon */}
                                <div className="border-t border-gray-100 pt-4">
                                    <p className="text-sm font-semibold text-gray-700 mb-2">Diskon</p>
                                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4 items-start">
                                        <div>
                                            <label className="block text-xs font-medium mb-1 text-gray-500">Diskon (%)</label>
                                            <input type="number" min="0" className={`${inputCls} text-right`} value={discountPercent || ''} onChange={(e) => onDiscountPercentChange(parseFloat(e.target.value) || 0)} placeholder="0" />
                                        </div>
                                        <div>
                                            <label className="block text-xs font-medium mb-1 text-gray-500">Diskon (Rp)</label>
                                            <input type="number" min="0" className={`${inputCls} text-right`} value={discountAmount || ''} onChange={(e) => onDiscountAmountChange(parseFloat(e.target.value) || 0)} placeholder="0" />
                                        </div>
                                        <div>
                                            <label className="block text-xs font-medium mb-1 invisible select-none" aria-hidden="true">.</label>
                                            <div className="rounded-md bg-emerald-50 border border-emerald-100 h-10 flex items-center justify-end px-3 text-sm font-mono text-emerald-700">- {FormatNumberRounded(discountAmount)}</div>
                                        </div>
                                    </div>
                                </div>

                                {/* PPh23 */}
                                <div className="border-t border-gray-100 pt-4">
                                    <p className="text-sm font-semibold text-gray-700 mb-2">PPh 23</p>
                                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4 items-start">
                                        <div>
                                            <label className="block text-xs font-medium mb-1 text-gray-500">PPh 23 (%)</label>
                                            <input type="number" min="0" className={`${inputCls} text-right`} value={pph23Percent || ''} onChange={(e) => onPph23PercentChange(parseFloat(e.target.value) || 0)} placeholder="0" />
                                        </div>
                                        <div>
                                            <label className="block text-xs font-medium mb-1 text-gray-500">PPh 23 (Rp)</label>
                                            <input type="number" min="0" className={`${inputCls} text-right`} value={pph23Amount || ''} onChange={(e) => setPph23Amount(parseFloat(e.target.value) || 0)} placeholder="0" />
                                        </div>
                                        <div>
                                            <label className="block text-xs font-medium mb-1 invisible select-none" aria-hidden="true">.</label>
                                            <div className="rounded-md bg-amber-50 border border-amber-100 h-10 flex items-center justify-end px-3 text-sm font-mono text-amber-700">+ {FormatNumberRounded(pph23Amount)}</div>
                                        </div>
                                    </div>
                                    <p className="text-[11px] text-gray-400 mt-1">Umumnya 2% (dengan NPWP)</p>
                                </div>

                                {/* PPN */}
                                <div className="border-t border-gray-100 pt-4">
                                    <p className="text-sm font-semibold text-gray-700 mb-2">PPN</p>
                                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4 items-start">
                                        <div>
                                            <label className="block text-xs font-medium mb-1 text-gray-500">PPN (%)</label>
                                            <input type="number" min="0" className={`${inputCls} text-right`} value={ppnPercent || ''} onChange={(e) => onPpnPercentChange(parseFloat(e.target.value) || 0)} placeholder="0" />
                                        </div>
                                        <div>
                                            <label className="block text-xs font-medium mb-1 text-gray-500">PPN (Rp)</label>
                                            <input type="number" min="0" className={`${inputCls} text-right`} value={ppnAmount || ''} onChange={(e) => setPpnAmount(parseFloat(e.target.value) || 0)} placeholder="0" />
                                        </div>
                                        <div>
                                            <label className="block text-xs font-medium mb-1 invisible select-none" aria-hidden="true">.</label>
                                            <div className="rounded-md bg-sky-50 border border-sky-100 h-10 flex items-center justify-end px-3 text-sm font-mono text-sky-700">+ {FormatNumberRounded(ppnAmount)}</div>
                                        </div>
                                    </div>
                                    <p className="text-[11px] text-gray-400 mt-1">Umumnya 11%. Dihitung dari DPP (jumlah setelah diskon)</p>
                                </div>
                            </div>

                            {/* Footer */}
                            <div className="flex items-center justify-between px-5 py-3 border-t border-gray-100 dark:border-gray-700">
                                <div className="text-sm">
                                    <div className="text-[11px] text-gray-400 font-mono leading-tight">
                                        DPP {FormatNumberRounded(dpp)} + PPN {FormatNumberRounded(ppnAmount)} + PPh23 {FormatNumberRounded(pph23Amount)}
                                    </div>
                                    <span className="text-gray-500">Outstanding: </span>
                                    <span className="font-mono font-bold text-gray-900 dark:text-white">Rp {FormatNumberRounded(outstanding)}</span>
                                </div>
                                <div className="flex gap-2">
                                    <Button variant="ghost" onClick={onClose}>Batal</Button>
                                    <Button onClick={handleSubmit} disabled={saving} className="gap-2 text-white" style={{ backgroundColor: ACCENT }}>
                                        {saving ? <Loader2 className="h-4 w-4 animate-spin" /> : <Save className="h-4 w-4" />}
                                        Simpan
                                    </Button>
                                </div>
                            </div>
                        </Dialog.Panel>
                    </div>
                </div>
            </Dialog>
        </Transition>
    );
};

export default ManualInvoiceModal;
