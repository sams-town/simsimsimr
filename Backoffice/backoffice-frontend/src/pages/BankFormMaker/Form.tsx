import { useEffect, useMemo, useState } from 'react';
import { useDispatch } from 'react-redux';
import { useNavigate, useParams } from 'react-router-dom';
import SelectSearch from 'react-select';
import Flatpickr from 'react-flatpickr';
import 'flatpickr/dist/flatpickr.css';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '@/store/themeConfigSlice';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { ArrowLeft, Save, Loader2, FileText, Receipt, Percent, Calculator } from 'lucide-react';
import IconCalendar from '@/components/Icon/IconCalendar';
import { FormatNumber } from '@/utils/formatNumber';
import { toastMessage } from '@/utils/toastUtils';
import { useGetOptionBankQuery } from '@/store/api/bank/bankApiSlice';
import {
    useCreateBankVoucherMutation,
    useUpdateBankVoucherMutation,
    useGetBankVoucherDetailQuery,
} from '@/store/api/bankFormVoucher/bankFormVoucherApiSlice';
import { BankFormMakerConfig } from './config';
import { isVoucherLocked } from '@/utils/editWindow';

const toApiDate = (date: Date): string =>
    new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate())).toISOString();

const round = (n: number) => Math.round((n + Number.EPSILON) * 100) / 100;

const selectStyles = {
    control: (base: any) => ({ ...base, minHeight: '40px', borderColor: '#e2e8f0', '&:hover': { borderColor: '#cbd5e1' } }),
    menuPortal: (base: any) => ({ ...base, zIndex: 9999 }),
};

const BankFormMakerForm = ({ config }: { config: BankFormMakerConfig }) => {
    const dispatch = useDispatch();
    const navigate = useNavigate();
    const { id } = useParams<{ id: string }>();
    const isEdit = !!id;

    useEffect(() => {
        dispatch(setPageTitle(config.formTitle));
        dispatch(setTitle(config.formTitle));
        dispatch(setBreadcrumbTitle(['Dashboard', config.breadcrumbParent, 'Form Maker', isEdit ? 'Ubah' : 'Baru']));
    }, [dispatch, config, isEdit]);

    // Form state
    const [voucherDate, setVoucherDate] = useState<Date>(new Date());
    const [paymentType, setPaymentType] = useState<'CASH' | 'BANK'>('CASH');
    const [personName, setPersonName] = useState('');
    const [account, setAccount] = useState<any>(null);
    const [amount, setAmount] = useState<number>(0);
    const [discountPercent, setDiscountPercent] = useState<number>(0);
    const [discountAmount, setDiscountAmount] = useState<number>(0);
    const [pph23Enabled, setPph23Enabled] = useState<boolean>(false);
    const [pph23Percent, setPph23Percent] = useState<number>(2);
    const [pph23Amount, setPph23Amount] = useState<number>(0);
    const [purpose, setPurpose] = useState('');
    const [notes, setNotes] = useState('');
    const [recipientBankName, setRecipientBankName] = useState('');
    const [recipientAccountName, setRecipientAccountName] = useState('');
    const [recipientAccountNumber, setRecipientAccountNumber] = useState('');

    // Account (Kas/Bank) options
    const [searchAccount, setSearchAccount] = useState('');
    const [debouncedSearch, setDebouncedSearch] = useState('');
    useEffect(() => {
        const t = setTimeout(() => setDebouncedSearch(searchAccount), 400);
        return () => clearTimeout(t);
    }, [searchAccount]);

    const { data: bankResponse, isLoading: isLoadingAccounts } = useGetOptionBankQuery(
        debouncedSearch ? { keyword: debouncedSearch } : {},
        { refetchOnMountOrArgChange: true }
    );

    const accountOptions = useMemo(() => {
        const list: any[] = bankResponse?.data?.data || bankResponse?.data || [];
        return list.map((a: any) => {
            const code = a.desc ?? a.value ?? a.coaCode ?? '';
            const label = a.label ?? a.coaName ?? code;
            return { value: code, label, coaCode: code, coaName: label };
        });
    }, [bankResponse]);

    // Edit: load existing voucher
    const { data: detailData } = useGetBankVoucherDetailQuery(id, { skip: !id });
    useEffect(() => {
        const v = detailData?.data;
        if (!v) return;
        // Editing is only allowed within 3 days of creation.
        if (isEdit && isVoucherLocked(v.createdDate)) {
            toastMessage('Bukti tidak dapat diubah karena sudah lebih dari 3 hari sejak dibuat.', 'error');
            navigate(`${config.basePath}/${id}`);
            return;
        }
        if (v.voucherDate) setVoucherDate(new Date(v.voucherDate));
        setPaymentType(v.paymentType === 'BANK' ? 'BANK' : 'CASH');
        setPersonName(v.personName || '');
        if (v.coaCode) setAccount({ value: v.coaCode, label: v.coaName || v.coaCode, coaCode: v.coaCode, coaName: v.coaName || '' });
        setAmount(Number(v.amount) || 0);
        setDiscountPercent(Number(v.discountPercent) || 0);
        setDiscountAmount(Number(v.discountAmount) || 0);
        setPph23Enabled(!!v.pph23Enabled);
        setPph23Percent(Number(v.pph23Percent) || 2);
        setPph23Amount(Number(v.pph23Amount) || 0);
        setPurpose(v.purpose || '');
        setNotes(v.notes || '');
        setRecipientBankName(v.recipientBankName || '');
        setRecipientAccountName(v.recipientAccountName || '');
        setRecipientAccountNumber(v.recipientAccountNumber || '');
    }, [detailData]);

    const [createVoucher, { isLoading: isCreating }] = useCreateBankVoucherMutation();
    const [updateVoucher, { isLoading: isUpdating }] = useUpdateBankVoucherMutation();
    const saving = isCreating || isUpdating;

    // ---- Derived amounts ----
    const dpp = Math.max(amount - discountAmount, 0); // base after discount
    const total = config.showTax
        ? Math.max(amount - discountAmount - (pph23Enabled ? pph23Amount : 0), 0)
        : amount;

    // Handlers keep percent <-> Rp in sync
    const onAmountChange = (val: number) => {
        setAmount(val);
        if (config.showTax) {
            const d = round(val * discountPercent / 100);
            setDiscountAmount(discountPercent > 0 ? d : discountAmount);
            const base = Math.max(val - (discountPercent > 0 ? d : discountAmount), 0);
            if (pph23Enabled) setPph23Amount(round(base * pph23Percent / 100));
        }
    };
    const onDiscountPercentChange = (pct: number) => {
        setDiscountPercent(pct);
        const d = round(amount * pct / 100);
        setDiscountAmount(d);
        if (pph23Enabled) setPph23Amount(round(Math.max(amount - d, 0) * pph23Percent / 100));
    };
    const onDiscountAmountChange = (rp: number) => {
        setDiscountAmount(rp);
        setDiscountPercent(amount > 0 ? round(rp / amount * 100) : 0);
        if (pph23Enabled) setPph23Amount(round(Math.max(amount - rp, 0) * pph23Percent / 100));
    };
    const onPph23PercentChange = (pct: number) => {
        setPph23Percent(pct);
        setPph23Amount(round(dpp * pct / 100));
    };
    const onPph23Toggle = (checked: boolean) => {
        setPph23Enabled(checked);
        setPph23Amount(checked ? round(dpp * pph23Percent / 100) : 0);
    };

    const handleSubmit = async () => {
        if (!account?.coaCode) { toastMessage(`Pilih ${config.accountLabel}`, 'error'); return; }
        if (!amount || amount <= 0) { toastMessage(`${config.amountLabel} harus lebih dari 0`, 'error'); return; }
        if (!purpose.trim()) { toastMessage('Keperluan wajib diisi', 'error'); return; }

        const body = {
            voucherType: config.voucherType,
            voucherDate: toApiDate(voucherDate),
            paymentType,
            personName: personName.trim(),
            coaCode: account.coaCode,
            coaName: account.coaName,
            amount,
            discountPercent: config.showTax ? discountPercent : 0,
            discountAmount: config.showTax ? discountAmount : 0,
            pph23Enabled: config.showTax ? pph23Enabled : false,
            pph23Percent: config.showTax ? pph23Percent : 0,
            pph23Amount: config.showTax ? pph23Amount : 0,
            purpose: purpose.trim(),
            notes: config.showNotes ? notes.trim() : '',
            recipientBankName: config.showRecipientAccount ? recipientBankName.trim() : '',
            recipientAccountName: config.showRecipientAccount ? recipientAccountName.trim() : '',
            recipientAccountNumber: config.showRecipientAccount ? recipientAccountNumber.trim() : '',
        };

        try {
            if (isEdit) {
                await updateVoucher({ id, ...body }).unwrap();
                toastMessage('Bukti berhasil diperbarui', 'success');
                navigate(`${config.basePath}/${id}`);
            } else {
                const res = await createVoucher(body).unwrap();
                toastMessage('Bukti berhasil dibuat', 'success');
                const newId = res?.data?.id;
                navigate(newId ? `${config.basePath}/${newId}` : config.basePath);
            }
        } catch (error: any) {
            toastMessage(error?.data?.message || 'Gagal menyimpan bukti', 'error');
        }
    };

    const inputCls = 'flex h-10 w-full rounded-md border border-input bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring';

    return (
        <div className="space-y-6">
            <div className="flex items-center justify-between">
                <Button variant="outline" className="gap-2" onClick={() => navigate(config.basePath)}>
                    <ArrowLeft className="w-4 h-4" /> Kembali
                </Button>
            </div>

            <Card>
                <CardHeader className="pb-2">
                    <CardTitle className="text-lg">{config.formTitle}</CardTitle>
                    <p className="text-sm text-gray-500 mt-1">{config.formSubtitle}</p>
                </CardHeader>
            </Card>

            <Card className="border-primary/20" style={{ borderColor: `${config.accent}30` }}>
                <CardHeader className="border-b" style={{ background: `linear-gradient(to right, ${config.accent}12, transparent)` }}>
                    <CardTitle className="flex items-center gap-2 text-lg" style={{ color: config.accent }}>
                        <Receipt className="w-5 h-5" /> {config.formTitle}
                    </CardTitle>
                </CardHeader>
                <CardContent className="p-6 space-y-6">
                    {/* Row 1 */}
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label className="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
                                <IconCalendar className="w-4 h-4" /> {config.dateLabel} <span className="text-red-500">*</span>
                            </label>
                            <Flatpickr
                                options={{ dateFormat: 'd/m/Y' }}
                                className={inputCls}
                                value={voucherDate}
                                onChange={(dates) => { if (dates[0]) setVoucherDate(dates[0]); }}
                                placeholder="Pilih tanggal"
                            />
                        </div>
                        <div>
                            <label className="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
                                <FileText className="w-4 h-4" /> {config.personLabel}
                            </label>
                            <input
                                type="text"
                                className={inputCls}
                                value={personName}
                                onChange={(e) => setPersonName(e.target.value)}
                                placeholder={config.voucherType === 'PENERIMAAN' ? 'Masukkan nama penyerah' : 'Masukkan nama penerima'}
                            />
                        </div>
                    </div>

                    {/* Row 2 */}
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label className="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
                                <FileText className="w-4 h-4" /> {config.accountLabel} <span className="text-red-500">*</span>
                            </label>
                            <SelectSearch
                                options={accountOptions}
                                value={account}
                                isLoading={isLoadingAccounts}
                                onInputChange={(v) => setSearchAccount(v)}
                                onChange={(opt: any) => setAccount(opt)}
                                placeholder="Pilih akun kas/bank..."
                                isClearable
                                styles={selectStyles}
                                menuPortalTarget={document.body}
                            />
                        </div>
                        <div>
                            <label className="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
                                <Calculator className="w-4 h-4" /> {config.amountLabel} <span className="text-red-500">*</span>
                            </label>
                            <input
                                type="number"
                                min="0"
                                className={`${inputCls} text-right font-medium`}
                                value={amount || ''}
                                onChange={(e) => onAmountChange(parseFloat(e.target.value) || 0)}
                                placeholder="0"
                            />
                        </div>
                    </div>

                    {/* Diskon + PPh23 (Pengeluaran only) */}
                    {config.showTax && (
                        <>
                            <div className="border-t border-gray-100 pt-5">
                                <p className="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-3">
                                    <Percent className="w-4 h-4 text-emerald-600" /> Diskon
                                </p>
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
                                        <div className="rounded-md bg-emerald-50 border border-emerald-100 h-10 flex items-center justify-end px-3 text-sm font-mono text-emerald-700">
                                            - {FormatNumber(discountAmount)}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div className="border-t border-gray-100 pt-5">
                                <label className="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-3 cursor-pointer">
                                    <input type="checkbox" className="w-4 h-4 rounded border-gray-300" checked={pph23Enabled} onChange={(e) => onPph23Toggle(e.target.checked)} />
                                    Potong PPh 23
                                </label>
                                <div className={`grid grid-cols-1 md:grid-cols-3 gap-4 items-start ${pph23Enabled ? '' : 'opacity-50 pointer-events-none'}`}>
                                    <div>
                                        <label className="block text-xs font-medium mb-1 text-gray-500">PPh 23 (%)</label>
                                        <input type="number" min="0" className={`${inputCls} text-right`} value={pph23Percent || ''} onChange={(e) => onPph23PercentChange(parseFloat(e.target.value) || 0)} placeholder="2" />
                                    </div>
                                    <div>
                                        <label className="block text-xs font-medium mb-1 text-gray-500">PPh 23 (Rp)</label>
                                        <input type="number" min="0" className={`${inputCls} text-right`} value={pph23Amount || ''} onChange={(e) => setPph23Amount(parseFloat(e.target.value) || 0)} placeholder="0" />
                                    </div>
                                    <div>
                                        <label className="block text-xs font-medium mb-1 invisible select-none" aria-hidden="true">.</label>
                                        <div className="rounded-md bg-amber-50 border border-amber-100 h-10 flex items-center justify-end px-3 text-sm font-mono text-amber-700">
                                            - {FormatNumber(pph23Enabled ? pph23Amount : 0)}
                                        </div>
                                    </div>
                                </div>
                                <p className="text-[11px] text-gray-400 mt-1">Umumnya 2% (dengan NPWP)</p>
                            </div>
                        </>
                    )}

                    {/* Rekening Penerima (Pengeluaran only) */}
                    {config.showRecipientAccount && (
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-4 border-t border-gray-100 pt-5">
                            <div>
                                <label className="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
                                    <FileText className="w-4 h-4" /> Nama Bank Penerima
                                </label>
                                <input
                                    type="text"
                                    className={inputCls}
                                    value={recipientBankName}
                                    onChange={(e) => setRecipientBankName(e.target.value)}
                                    placeholder="cth: BCA, Mandiri, BNI"
                                />
                            </div>
                            <div>
                                <label className="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
                                    <FileText className="w-4 h-4" /> Nama Rekening Penerima
                                </label>
                                <input
                                    type="text"
                                    className={inputCls}
                                    value={recipientAccountName}
                                    onChange={(e) => setRecipientAccountName(e.target.value)}
                                    placeholder="Nama pemilik rekening"
                                />
                            </div>
                            <div>
                                <label className="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
                                    <FileText className="w-4 h-4" /> No Rekening Penerima
                                </label>
                                <input
                                    type="text"
                                    className={inputCls}
                                    value={recipientAccountNumber}
                                    onChange={(e) => setRecipientAccountNumber(e.target.value)}
                                    placeholder="Nomor rekening"
                                />
                            </div>
                        </div>
                    )}

                    {/* Keperluan + Catatan */}
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4 border-t border-gray-100 pt-5">
                        <div className={config.showNotes ? '' : 'md:col-span-2'}>
                            <label className="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
                                <FileText className="w-4 h-4" /> Keperluan <span className="text-red-500">*</span>
                            </label>
                            <textarea className="flex min-h-[90px] w-full rounded-md border border-input bg-white px-3 py-2 text-sm" value={purpose} onChange={(e) => setPurpose(e.target.value)} placeholder="Jelaskan keperluan..." />
                        </div>
                        {config.showNotes && (
                            <div>
                                <label className="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
                                    <FileText className="w-4 h-4" /> Catatan (Opsional)
                                </label>
                                <textarea className="flex min-h-[90px] w-full rounded-md border border-input bg-white px-3 py-2 text-sm" value={notes} onChange={(e) => setNotes(e.target.value)} placeholder="Catatan tambahan..." />
                            </div>
                        )}
                    </div>
                </CardContent>
            </Card>

            {/* Summary bar */}
            <Card className="text-white" style={{ background: `linear-gradient(to right, ${config.accent}, ${config.accent}cc)` }}>
                <CardContent className="p-6">
                    <div className="flex items-center justify-between gap-6 flex-wrap">
                        <div className="flex items-center gap-6 divide-x divide-white/20">
                            <div className="px-4 first:pl-0">
                                <p className="text-white/70 text-xs uppercase tracking-wide">{config.amountLabel}</p>
                                <p className="text-lg font-bold font-mono mt-1">Rp {FormatNumber(amount)}</p>
                            </div>
                            {config.showTax && (
                                <div className="px-4">
                                    <p className="text-white/70 text-xs uppercase tracking-wide">Potongan</p>
                                    <p className="text-lg font-bold font-mono mt-1">Rp {FormatNumber(discountAmount + (pph23Enabled ? pph23Amount : 0))}</p>
                                </div>
                            )}
                            <div className="px-4">
                                <p className="text-white/70 text-xs uppercase tracking-wide">Total</p>
                                <p className="text-2xl font-bold font-mono mt-1">Rp {FormatNumber(total)}</p>
                            </div>
                        </div>
                        <Button onClick={handleSubmit} disabled={saving} className="bg-white hover:bg-gray-100 gap-2 h-12 px-6" style={{ color: config.accent }}>
                            {saving ? <Loader2 className="w-5 h-5 animate-spin" /> : <Save className="w-5 h-5" />}
                            {isEdit ? 'Simpan Perubahan' : 'Simpan Bukti'}
                        </Button>
                    </div>
                </CardContent>
            </Card>
        </div>
    );
};

export default BankFormMakerForm;
