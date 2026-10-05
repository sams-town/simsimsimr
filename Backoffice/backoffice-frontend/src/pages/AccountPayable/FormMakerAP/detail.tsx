import { useEffect, useState } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { DataTable } from 'mantine-datatable';
import Flatpickr from 'react-flatpickr';
import 'flatpickr/dist/flatpickr.css';
import { ArrowLeft, Plus, Printer, Trash2, FileText, CalendarDays } from 'lucide-react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import ConfirmationModal from '@/components/ConfirmationModal';
import { toastMessage } from '@/utils/toastUtils';
import { FormatNumberRounded } from '@/utils/formatNumber';
import { FormatDate } from '@/utils/formatDate';
import { isVoucherLocked } from '@/utils/editWindow';
import AddInvoiceModal from './AddInvoiceModal';
import {
    useGetApVoucherDetailQuery,
    useUpdateApVoucherMutation,
    useAddApVoucherItemsMutation,
    useRemoveApVoucherItemsMutation,
} from '@/store/api/accountPayable/apPaymentVoucherApiSlice';

interface VoucherItem {
    invoiceExchangeId: number;
    invoiceNumber?: string;
    poNumber?: string;
    supplierName?: string;
    dueDate?: string;
    totalTransaction: number;
    paidAmount: number;
    outstandingAmount: number;
    paymentStatus?: string;
}

interface VoucherDetail {
    id: number;
    voucherNumber: string;
    voucherDate?: string;
    paymentType: string;
    status: string;
    supplierName?: string;
    supplierBankInfo?: string;
    createdDate?: string;
    items: VoucherItem[];
}

const terbilang = (angka: number): string => {
    const huruf = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
    if (angka < 12) return huruf[angka];
    if (angka < 20) return huruf[angka - 10] + ' Belas';
    if (angka < 100) return (huruf[Math.floor(angka / 10)] + ' Puluh' + (angka % 10 !== 0 ? ' ' + huruf[angka % 10] : '')).trim();
    if (angka < 200) return ('Seratus' + (angka - 100 !== 0 ? ' ' + terbilang(angka - 100) : '')).trim();
    if (angka < 1000) return (huruf[Math.floor(angka / 100)] + ' Ratus' + (angka % 100 !== 0 ? ' ' + terbilang(angka % 100) : '')).trim();
    if (angka < 2000) return ('Seribu' + (angka - 1000 !== 0 ? ' ' + terbilang(angka - 1000) : '')).trim();
    if (angka < 1000000) return (terbilang(Math.floor(angka / 1000)) + ' Ribu' + (angka % 1000 !== 0 ? ' ' + terbilang(angka % 1000) : '')).trim();
    if (angka < 1000000000) return (terbilang(Math.floor(angka / 1000000)) + ' Juta' + (angka % 1000000 !== 0 ? ' ' + terbilang(angka % 1000000) : '')).trim();
    return (terbilang(Math.floor(angka / 1000000000)) + ' Miliar' + (angka % 1000000000 !== 0 ? ' ' + terbilang(angka % 1000000000) : '')).trim();
};

const MAROON = '#8B1A1A';

const formatPrintDate = (date?: string | Date | null): string => {
    const d = date ? new Date(date) : new Date();
    const dd = String(d.getDate()).padStart(2, '0');
    const mm = String(d.getMonth() + 1).padStart(2, '0');
    const yyyy = d.getFullYear();
    return `${dd} / ${mm} / ${yyyy}`;
};

const toUtcIso = (date: Date): string =>
    new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate())).toISOString();

const FormMakerAPDetail = () => {
    const { id } = useParams<{ id: string }>();
    const navigate = useNavigate();

    const [showAddModal, setShowAddModal] = useState(false);
    const [showRemoveModal, setShowRemoveModal] = useState(false);
    const [removeTarget, setRemoveTarget] = useState<VoucherItem | null>(null);
    const [printPaymentType, setPrintPaymentType] = useState<'CASH' | 'BANK'>('CASH');
    const [voucherDate, setVoucherDate] = useState<Date | null>(null);

    const { data, isLoading } = useGetApVoucherDetailQuery(id, { skip: !id });
    const [updateVoucher, { isLoading: isUpdatingDate }] = useUpdateApVoucherMutation();
    const [addItems, { isLoading: isAdding }] = useAddApVoucherItemsMutation();
    const [removeItems, { isLoading: isRemoving }] = useRemoveApVoucherItemsMutation();

    const voucher: VoucherDetail | undefined = data?.data;
    const items: VoucherItem[] = voucher?.items || [];
    const totalAmount = items.reduce((sum, r) => sum + (r.outstandingAmount || 0), 0);
    const supplierName = voucher?.supplierName || items[0]?.supplierName || '-';
    const locked = isVoucherLocked(voucher?.createdDate);

    // Sync editable date with the loaded voucher
    useEffect(() => {
        if (voucher?.voucherDate) setVoucherDate(new Date(voucher.voucherDate));
    }, [voucher?.voucherDate]);

    const handleDateChange = async (newDate: Date) => {
        setVoucherDate(newDate);
        if (!id) return;
        try {
            await updateVoucher({ id, voucherDate: toUtcIso(newDate) }).unwrap();
            toastMessage('Tanggal voucher diperbarui', 'success');
        } catch (error: any) {
            toastMessage(error?.data?.message || 'Gagal memperbarui tanggal', 'error');
        }
    };

    const handleAdd = async (invoiceExchangeIds: number[]) => {
        if (!id) return;
        try {
            await addItems({ id, invoiceExchangeIds }).unwrap();
            toastMessage('Invoice ditambahkan', 'success');
            setShowAddModal(false);
        } catch (error: any) {
            toastMessage(error?.data?.message || 'Gagal menambahkan invoice', 'error');
        }
    };

    const handleRemove = async () => {
        if (!id || !removeTarget) return;
        try {
            await removeItems({ id, invoiceExchangeIds: [removeTarget.invoiceExchangeId] }).unwrap();
            toastMessage('Invoice dihapus dari voucher', 'success');
            setShowRemoveModal(false);
            setRemoveTarget(null);
        } catch (error: any) {
            toastMessage(error?.data?.message || 'Gagal menghapus invoice', 'error');
        }
    };

    const voucherDateStr = voucherDate ? FormatDate(voucherDate.toISOString()) : FormatDate(new Date().toISOString());

    return (
        <>
            {/* Screen layout */}
            <div className="flex flex-col gap-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 print:hidden">
                <div className="flex items-center justify-between mt-4">
                    <div className="flex items-center gap-3">
                        <div className="p-2 rounded-lg" style={{ backgroundColor: '#E8861A20' }}>
                            <FileText className="h-5 w-5" style={{ color: '#E8861A' }} />
                        </div>
                        <div>
                            <h1 className="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                                {voucher?.voucherNumber || 'Form Maker AP'}
                            </h1>
                            <p className="text-sm text-gray-500 mt-1">Detail Bukti Pengeluaran Account Payable</p>
                        </div>
                    </div>
                    <div className="flex gap-2 items-center">
                        <Button variant="outline" onClick={() => navigate(-1)} size="sm" className="gap-2">
                            <ArrowLeft className="h-4 w-4" /> Kembali
                        </Button>

                        {/* Editable voucher date */}
                        <div className="relative w-[150px]">
                            <Flatpickr
                                value={voucherDate || undefined}
                                options={{ dateFormat: 'd/m/Y', allowInput: false }}
                                className="flex h-9 w-full rounded-md border border-gray-200 bg-background pl-8 pr-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-ring disabled:opacity-60"
                                placeholder="Tanggal voucher"
                                disabled={isUpdatingDate || locked}
                                onChange={(dates) => { if (dates.length) handleDateChange(dates[0]); }}
                            />
                            <CalendarDays className="absolute left-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
                        </div>

                        {/* CASH / BANK toggle */}
                        <div className="flex rounded-md border border-gray-200 overflow-hidden text-sm">
                            <button
                                type="button"
                                className={`px-3 py-1.5 font-semibold transition-colors ${printPaymentType === 'CASH' ? 'bg-green-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50'}`}
                                onClick={() => setPrintPaymentType('CASH')}
                            >
                                CASH
                            </button>
                            <button
                                type="button"
                                className={`px-3 py-1.5 font-semibold transition-colors ${printPaymentType === 'BANK' ? 'bg-blue-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50'}`}
                                onClick={() => setPrintPaymentType('BANK')}
                            >
                                BANK
                            </button>
                        </div>

                        <Button variant="outline" onClick={() => window.print()} size="sm" className="gap-2">
                            <Printer className="h-4 w-4" /> Cetak
                        </Button>
                        {locked ? (
                            <span className="text-xs text-gray-400 italic px-2" title="Bukti hanya dapat diubah dalam 3 hari sejak dibuat">
                                Terkunci (lebih dari 3 hari)
                            </span>
                        ) : (
                            <Button onClick={() => setShowAddModal(true)} size="sm" className="gap-2 text-white" style={{ backgroundColor: '#E8861A' }}>
                                <Plus className="h-4 w-4" /> Tambah Invoice
                            </Button>
                        )}
                    </div>
                </div>

                {/* Info cards */}
                <div className="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-[#1b2e4b] shadow-sm overflow-hidden">
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 divide-y md:divide-y-0 md:divide-x border-gray-200 dark:border-gray-700">
                        <div className="p-5">
                            <p className="text-xs text-gray-500 uppercase tracking-wide font-medium mb-1.5">No. Voucher</p>
                            <p className="text-lg font-bold font-mono" style={{ color: '#E8861A' }}>{voucher?.voucherNumber || '-'}</p>
                        </div>
                        <div className="p-5">
                            <p className="text-xs text-gray-500 uppercase tracking-wide font-medium mb-1.5">Tanggal</p>
                            <p className="text-lg font-bold text-gray-900 dark:text-white">{voucherDateStr}</p>
                        </div>
                        <div className="p-5">
                            <p className="text-xs text-gray-500 uppercase tracking-wide font-medium mb-1.5">Supplier</p>
                            <p className="text-base font-semibold text-gray-900 dark:text-white truncate">{supplierName}</p>
                        </div>
                        <div className="p-5" style={{ backgroundColor: '#E8861A15' }}>
                            <p className="text-xs text-gray-500 uppercase tracking-wide font-medium mb-1.5">Total Outstanding</p>
                            <p className="text-xl font-bold" style={{ color: '#E8861A' }}>{FormatNumberRounded(totalAmount)}</p>
                        </div>
                    </div>
                </div>

                {/* Invoice items */}
                <Card className="shadow-sm border border-gray-200 dark:border-gray-800">
                    <CardHeader className="bg-gray-50/50 dark:bg-[#1b2e4b] border-b border-gray-100 dark:border-gray-700 px-6 py-4">
                        <CardTitle className="text-base font-semibold flex items-center gap-2">Daftar Invoice</CardTitle>
                    </CardHeader>
                    <CardContent className="p-0">
                        <DataTable
                            highlightOnHover
                            className="table-hover whitespace-nowrap"
                            records={items}
                            idAccessor="invoiceExchangeId"
                            columns={[
                                { accessor: 'no', title: 'No.', width: 50, textAlignment: 'center', render: (_r: VoucherItem, i: number) => i + 1 },
                                {
                                    accessor: 'invoiceNumber', title: 'No. Invoice',
                                    render: (r: VoucherItem) => <span className="font-mono text-xs">{r.invoiceNumber || '-'}</span>
                                },
                                {
                                    accessor: 'poNumber', title: 'No. PO',
                                    render: (r: VoucherItem) => <span className="text-xs">{r.poNumber || '-'}</span>
                                },
                                {
                                    accessor: 'supplierName', title: 'Supplier',
                                    render: (r: VoucherItem) => <span className="font-medium">{r.supplierName || '-'}</span>
                                },
                                {
                                    accessor: 'dueDate', title: 'Jatuh Tempo', noWrap: true,
                                    render: (r: VoucherItem) => <span className="text-xs">{r.dueDate ? FormatDate(r.dueDate) : '-'}</span>
                                },
                                {
                                    accessor: 'totalTransaction', title: 'Total Invoice', textAlignment: 'right',
                                    render: (r: VoucherItem) => <span className="font-mono text-xs">{FormatNumberRounded(r.totalTransaction)}</span>
                                },
                                {
                                    accessor: 'paidAmount', title: 'Dibayar', textAlignment: 'right',
                                    render: (r: VoucherItem) => <span className="font-mono text-xs">{FormatNumberRounded(r.paidAmount)}</span>
                                },
                                {
                                    accessor: 'outstandingAmount', title: 'Outstanding', textAlignment: 'right',
                                    render: (r: VoucherItem) => <span className="font-mono font-bold">{FormatNumberRounded(r.outstandingAmount)}</span>
                                },
                                {
                                    accessor: 'paymentStatus', title: 'Status', textAlignment: 'center',
                                    render: (r: VoucherItem) => (
                                        <span className={`px-1.5 py-0.5 rounded text-[10px] font-semibold ${r.paymentStatus === 'Partial' ? 'bg-info/10 text-info' : 'bg-warning/10 text-warning'}`}>
                                            {r.paymentStatus || 'Unpaid'}
                                        </span>
                                    )
                                },
                                {
                                    accessor: 'action', title: 'Aksi', textAlignment: 'center',
                                    render: (r: VoucherItem) => (
                                        locked ? (
                                            <span className="text-gray-300" title="Terkunci (lebih dari 3 hari)">—</span>
                                        ) : (
                                            <button
                                                type="button"
                                                onClick={() => { setRemoveTarget(r); setShowRemoveModal(true); }}
                                                className="text-red-500 hover:text-red-700 p-1.5 rounded-md hover:bg-red-50 transition-colors"
                                                title="Hapus dari voucher"
                                            >
                                                <Trash2 className="w-4 h-4" />
                                            </button>
                                        )
                                    )
                                },
                            ]}
                            fetching={isLoading}
                            minHeight={200}
                            noRecordsText="Belum ada invoice. Klik 'Tambah Invoice'."
                        />
                    </CardContent>
                </Card>
            </div>

            {/* ===== PRINT LAYOUT — Bukti Pengeluaran ===== */}
            <div className="fmr-print hidden print:block font-sans text-black bg-white p-[10mm] absolute top-0 left-0 w-full min-h-screen z-[9999]" style={{ WebkitPrintColorAdjust: 'exact', printColorAdjust: 'exact' } as any}>
                <style>{`
                    @media print {
                        @page { size: A4 portrait; margin: 10mm; }
                        body { margin: 0; }
                        * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
                        /* Override global black-border print rule (tailwind.css) -> maroon */
                        .fmr-print table,
                        .fmr-print table thead tr,
                        .fmr-print table thead tr th,
                        .fmr-print table tbody tr,
                        .fmr-print table tbody tr td {
                            border-color: ${MAROON} !important;
                        }
                    }
                `}</style>

                {/* Header: logo+name left | title+cashbank+date right */}
                <div className="flex items-start justify-between pb-3 mb-3" style={{ borderBottom: `2px solid ${MAROON}` }}>
                    {/* Left: logo + hospital info */}
                    <div className="flex items-start gap-3">
                        <img src="/assets/images/logo-thb-transparent.png" alt="Logo" className="w-[55px] h-[55px] object-contain" />
                        <div>
                            <p className="text-[11px] font-bold leading-tight" style={{ color: MAROON }}>RUMAH SAKIT</p>
                            <p className="text-[14px] font-extrabold leading-tight" style={{ color: MAROON }}>TAMAN HARAPAN BARU</p>
                            <p className="text-[8px] mt-1 leading-snug" style={{ color: MAROON }}>Jl. Kaliabang Tengah No. 2, RT. 004 / RW. 023,</p>
                            <p className="text-[8px] leading-snug" style={{ color: MAROON }}>Kel. Pejuang Kec. Medan Satria - Kota Bekasi 17131</p>
                            <p className="text-[8px] leading-snug" style={{ color: MAROON }}>Telp. (021) 8898 1055 Fax. (021) 8886 8558</p>
                        </div>
                    </div>
                    {/* Right: title + cash/bank + date */}
                    <div className="text-right">
                        <h1 className="text-[20px] font-extrabold tracking-wide" style={{ color: MAROON }}>BUKTI PENGELUARAN</h1>
                        <div className="flex items-center justify-end gap-4 mt-1 text-[10px] font-semibold" style={{ color: MAROON }}>
                            <label className="flex items-center gap-1">
                                <span style={{
                                    display: 'inline-block', width: 12, height: 12,
                                    border: `1.5px solid ${MAROON}`, textAlign: 'center', lineHeight: '10px',
                                    fontSize: 10, color: MAROON, fontWeight: 'bold'
                                }}>
                                    {printPaymentType === 'CASH' ? '✓' : ''}
                                </span>
                                CASH
                            </label>
                            <label className="flex items-center gap-1">
                                <span style={{
                                    display: 'inline-block', width: 12, height: 12,
                                    border: `1.5px solid ${MAROON}`, textAlign: 'center', lineHeight: '10px',
                                    fontSize: 10, color: MAROON, fontWeight: 'bold'
                                }}>
                                    {printPaymentType === 'BANK' ? '✓' : ''}
                                </span>
                                BANK
                            </label>
                        </div>
                        <p className="text-[10px] font-semibold mt-1" style={{ color: MAROON }}>TANGGAL : {formatPrintDate(voucherDate)}</p>
                    </div>
                </div>

                {/* Voucher number */}
                <p className="text-[11px] font-bold mb-4" style={{ color: '#000' }}>
                    <span style={{ color: MAROON }}>VOUCHER NO. : </span>
                    <span style={{ borderBottom: `1.5px solid #000`, paddingBottom: 1, fontSize: 14 }}>
                        {voucher?.voucherNumber || '-'}
                    </span>
                </p>

                {/* Invoice table */}
                <table className="w-full mb-0 text-[10px]" style={{ borderCollapse: 'collapse', border: `1px solid ${MAROON}` }}>
                    <thead>
                        <tr style={{ color: MAROON }}>
                            <th style={{ border: `1px solid ${MAROON}`, padding: '6px 8px', textAlign: 'center', width: '20%', fontWeight: 'bold' }}>
                                No.Invoice /<br />Nota
                            </th>
                            <th style={{ border: `1px solid ${MAROON}`, padding: '6px 8px', textAlign: 'center', fontWeight: 'bold', letterSpacing: '0.15em' }}>
                                U R A I A N
                            </th>
                            <th style={{ border: `1px solid ${MAROON}`, padding: '6px 8px', textAlign: 'center', width: '18%', fontWeight: 'bold', letterSpacing: '0.05em' }}>
                                JUMLAH
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        {items.map((item) => (
                            <tr key={item.invoiceExchangeId}>
                                <td style={{ border: `1px solid ${MAROON}`, padding: '5px 8px', fontFamily: 'monospace' }}>
                                    {item.invoiceNumber || '-'}
                                </td>
                                <td style={{ border: `1px solid ${MAROON}`, padding: '5px 8px' }}>
                                    {item.supplierName || '-'}{item.poNumber ? ` - ${item.poNumber}` : ''}
                                </td>
                                <td style={{ border: `1px solid ${MAROON}`, padding: '5px 8px', textAlign: 'right', fontFamily: 'monospace' }}>
                                    {FormatNumberRounded(item.outstandingAmount)}
                                </td>
                            </tr>
                        ))}
                        <tr style={{ fontWeight: 'bold' }}>
                            <td colSpan={2} style={{ border: `1px solid ${MAROON}`, padding: '6px 8px', textAlign: 'center', letterSpacing: '0.1em', color: MAROON }}>
                                TOTAL
                            </td>
                            <td style={{ border: `1px solid ${MAROON}`, padding: '6px 8px', textAlign: 'right', fontFamily: 'monospace' }}>
                                {FormatNumberRounded(totalAmount)}
                            </td>
                        </tr>
                    </tbody>
                </table>

                {/* Terbilang + Dibayar + Diterima */}
                <table className="w-full mb-4 text-[10px]" style={{ borderCollapse: 'collapse' }}>
                    <tbody>
                        <tr>
                            <td style={{ border: `1px solid ${MAROON}`, padding: '4px 8px', width: '18%', fontWeight: 'bold', color: MAROON, verticalAlign: 'top' }}>Terbilang :</td>
                            <td style={{ border: `1px solid ${MAROON}`, padding: '4px 8px', fontStyle: 'italic', fontWeight: 'bold', color: MAROON }}>
                                # {terbilang(Math.round(totalAmount))} Rupiah #
                            </td>
                        </tr>
                        <tr>
                            <td style={{ border: `1px solid ${MAROON}`, padding: '4px 8px', fontWeight: 'bold', color: MAROON }}>Dibayar pada :</td>
                            <td style={{ border: `1px solid ${MAROON}`, padding: '4px 8px', color: MAROON }}>{formatPrintDate(voucherDate)}</td>
                        </tr>
                        <tr>
                            <td style={{ border: `1px solid ${MAROON}`, padding: '4px 8px', fontWeight: 'bold', color: MAROON, verticalAlign: 'middle' }}>Diterima Oleh :</td>
                            <td style={{ border: `1px solid ${MAROON}`, padding: '4px 8px', fontWeight: 'bold', fontSize: 14 }}>
                                {supplierName}{voucher?.supplierBankInfo ? ` - ${voucher.supplierBankInfo}` : ''}
                            </td>
                        </tr>
                    </tbody>
                </table>

                {/* Signature block: Submitted + Finance + Director joined, aligned right */}
                <div className="flex justify-end mt-2 text-[10px]">
                    <table style={{ width: '70%', borderCollapse: 'collapse', border: `1px solid ${MAROON}` }}>
                        <thead>
                            <tr>
                                <th style={{ border: `1px solid ${MAROON}`, padding: '5px 8px', textAlign: 'center', fontWeight: 'bold', color: MAROON, width: '33.33%' }}>
                                    Submitted
                                </th>
                                <th style={{ border: `1px solid ${MAROON}`, padding: '5px 8px', textAlign: 'center', fontWeight: 'bold', color: MAROON, width: '33.33%' }}>
                                    Finance
                                </th>
                                <th style={{ border: `1px solid ${MAROON}`, padding: '5px 8px', textAlign: 'center', fontWeight: 'bold', color: MAROON, width: '33.33%' }}>
                                    Director
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style={{ height: 90 }}>
                                <td style={{ border: `1px solid ${MAROON}`, padding: '6px 10px', verticalAlign: 'top' }}>
                                    <p className="mb-0">Issued By :</p>
                                    <div style={{ borderBottom: `1px solid #000`, marginTop: 48, marginBottom: 2 }}></div>
                                </td>
                                <td style={{ border: `1px solid ${MAROON}`, padding: '6px 10px', verticalAlign: 'top' }}>
                                    <p className="mb-0">Acknowledged By:</p>
                                    <div style={{ borderBottom: `1px solid #000`, marginTop: 48, marginBottom: 2 }}></div>
                                </td>
                                <td style={{ border: `1px solid ${MAROON}`, padding: '6px 10px', verticalAlign: 'top' }}>
                                    <p className="mb-0">Approved By:</p>
                                    <div style={{ borderBottom: `1px solid #000`, marginTop: 48, marginBottom: 2 }}></div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <p className="text-[8px] italic mt-2" style={{ color: MAROON }}>
                    HARUS ADA : TANDA TANGAN, NAMA JELAS, TANGGAL
                </p>
            </div>

            <AddInvoiceModal
                isOpen={showAddModal}
                onClose={() => setShowAddModal(false)}
                onAdd={handleAdd}
                loading={isAdding}
                supplierFilter={supplierName !== '-' ? supplierName : undefined}
                excludeVoucherId={id ? Number(id) : undefined}
            />

            <ConfirmationModal
                isOpen={showRemoveModal}
                onClose={() => { setShowRemoveModal(false); setRemoveTarget(null); }}
                onConfirm={handleRemove}
                title="Hapus Invoice dari Voucher"
                message={`Hapus invoice ${removeTarget?.invoiceNumber || 'ini'} dari bukti pengeluaran? Invoice tidak terhapus, hanya dilepas dari voucher ini.`}
                confirmText="Hapus"
                cancelText="Batal"
                loading={isRemoving}
                variant="danger"
            />
        </>
    );
};

export default FormMakerAPDetail;
