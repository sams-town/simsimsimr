import { useState, useEffect } from 'react';
import { useNavigate, useLocation } from 'react-router-dom';
import { Button } from '@/components/ui/button';
import { ArrowLeft, Printer } from 'lucide-react';
import { FormatNumberRounded } from '@/utils/formatNumber';

interface InvoiceItem {
    invoiceExchangeId: number;
    supplierName: string;
    poNumber: string;
    invoiceNumber: string;
    outstandingAmount: number;
}

const COLOR = '#8B1A1A';

const terbilang = (angka: number): string => {
    const huruf = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
    if (angka < 12) return huruf[angka];
    if (angka < 20) return huruf[angka - 10] + ' Belas';
    if (angka < 100) return huruf[Math.floor(angka / 10)] + ' Puluh ' + (huruf[angka % 10] || '');
    if (angka < 200) return 'Seratus ' + (terbilang(angka - 100) || '');
    if (angka < 1000) return huruf[Math.floor(angka / 100)] + ' Ratus ' + (terbilang(angka % 100) || '');
    if (angka < 2000) return 'Seribu ' + (terbilang(angka - 1000) || '');
    if (angka < 1000000) return terbilang(Math.floor(angka / 1000)) + ' Ribu ' + (terbilang(angka % 1000) || '');
    if (angka < 1000000000) return terbilang(Math.floor(angka / 1000000)) + ' Juta ' + (terbilang(angka % 1000000) || '');
    return terbilang(Math.floor(angka / 1000000000)) + ' Miliar ' + (terbilang(angka % 1000000000) || '');
};

const formatDateSlash = (date: Date): string => {
    const d = String(date.getDate()).padStart(2, '0');
    const m = String(date.getMonth() + 1).padStart(2, '0');
    const y = date.getFullYear();
    return `${d} / ${m} / ${y}`;
};

const generateVoucherNo = (date: Date): string => {
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, '0');
    const d = String(date.getDate()).padStart(2, '0');
    return `FMR-${y}${m}${d}-0001`;
};

const BuktiPengeluaran = () => {
    const navigate = useNavigate();
    const location = useLocation();
    const invoices: InvoiceItem[] = location.state?.selectedInvoices || [];

    const today = new Date();
    const [paymentType, setPaymentType] = useState<'CASH' | 'BANK'>('CASH');
    const [voucherNo, setVoucherNo] = useState(() => generateVoucherNo(today));
    const [printDate] = useState(() => formatDateSlash(today));

    useEffect(() => {
        if (!invoices || invoices.length === 0) {
            navigate('/account-payable/pembayaran');
        }
        document.title = 'Bukti Pengeluaran AP';
    }, []);

    if (!invoices || invoices.length === 0) return null;

    const total = invoices.reduce((sum, inv) => sum + (inv.outstandingAmount || 0), 0);
    const terbilangText = `${terbilang(Math.round(total)).trim()} Rupiah`;
    const supplierName = invoices[0]?.supplierName || '';

    const BLANK_ROWS = Math.max(0, 3 - invoices.length);

    return (
        <div className="min-h-screen bg-gray-100 py-8 print:bg-white print:py-0">
            {/* Action Bar */}
            <div className="max-w-[210mm] mx-auto mb-4 flex items-center justify-between px-4 print:hidden">
                <Button variant="outline" className="gap-2 bg-white" onClick={() => navigate(-1)}>
                    <ArrowLeft className="w-4 h-4" />
                    Kembali
                </Button>
                <div className="flex items-center gap-3">
                    {/* Payment type toggle */}
                    <div className="flex items-center gap-3 text-sm font-semibold border rounded-md px-3 py-1.5 bg-white" style={{ borderColor: COLOR, color: COLOR }}>
                        <label className="flex items-center gap-1.5 cursor-pointer">
                            <input
                                type="radio"
                                name="payType"
                                checked={paymentType === 'CASH'}
                                onChange={() => setPaymentType('CASH')}
                                style={{ accentColor: COLOR }}
                            />
                            CASH
                        </label>
                        <label className="flex items-center gap-1.5 cursor-pointer">
                            <input
                                type="radio"
                                name="payType"
                                checked={paymentType === 'BANK'}
                                onChange={() => setPaymentType('BANK')}
                                style={{ accentColor: COLOR }}
                            />
                            BANK
                        </label>
                    </div>
                    {/* Voucher No editable */}
                    <div className="flex items-center gap-2 border rounded-md px-3 py-1.5 bg-white" style={{ borderColor: COLOR }}>
                        <span className="text-xs font-semibold" style={{ color: COLOR }}>No. Voucher:</span>
                        <input
                            type="text"
                            value={voucherNo}
                            onChange={(e) => setVoucherNo(e.target.value)}
                            className="text-sm font-mono outline-none bg-transparent w-44"
                            style={{ color: COLOR }}
                        />
                    </div>
                    <Button
                        className="gap-2 text-white"
                        style={{ backgroundColor: COLOR }}
                        onClick={() => window.print()}
                    >
                        <Printer className="w-4 h-4" />
                        Cetak
                    </Button>
                </div>
            </div>

            {/* Receipt Paper */}
            <div className="max-w-[210mm] mx-auto bg-white shadow-md print:shadow-none">
                <div className="p-8 print:p-6 font-sans" style={{ color: COLOR }}>

                    {/* ── Header ── */}
                    <div className="flex items-start justify-between mb-4">
                        <div className="flex items-start gap-3">
                            <img
                                src="/assets/images/logo-thb-transparent.png"
                                alt="Logo RS THB"
                                className="w-16 h-16 object-contain"
                            />
                            <div>
                                <p className="text-base font-bold leading-tight">RUMAH SAKIT</p>
                                <p className="text-base font-bold leading-tight">TAMAN HARAPAN BARU</p>
                                <p className="text-[9px] leading-snug mt-1" style={{ color: '#555' }}>
                                    Jl. Kaliabang Tengah No. 2, RT. 004 / RW. 023,<br />
                                    Kel. Pejuang Kec. Medan Satria - Kota Bekasi 17131<br />
                                    Telp. (021) 8898 1055 Fax. (021) 8886 8558
                                </p>
                            </div>
                        </div>

                        <div className="text-right">
                            <h2 className="text-xl font-bold tracking-widest mb-2">BUKTI PENGELUARAN</h2>
                            {/* CASH / BANK — screen: radio inputs; print: checkboxes */}
                            <div className="flex items-center justify-end gap-4 text-xs font-semibold mb-1.5 print:hidden">
                                <label className="flex items-center gap-1 cursor-pointer">
                                    <input type="radio" name="payTypePrint" checked={paymentType === 'CASH'} onChange={() => setPaymentType('CASH')} style={{ accentColor: COLOR }} />
                                    CASH
                                </label>
                                <label className="flex items-center gap-1 cursor-pointer">
                                    <input type="radio" name="payTypePrint" checked={paymentType === 'BANK'} onChange={() => setPaymentType('BANK')} style={{ accentColor: COLOR }} />
                                    BANK
                                </label>
                            </div>
                            <div className="hidden print:flex items-center justify-end gap-3 text-xs font-semibold mb-1.5">
                                <span className="flex items-center gap-1">
                                    <span
                                        className="inline-flex items-center justify-center w-3.5 h-3.5 border-2 rounded-sm text-white text-[8px]"
                                        style={{ borderColor: COLOR, backgroundColor: paymentType === 'CASH' ? COLOR : 'white' }}
                                    >
                                        {paymentType === 'CASH' ? '✓' : ''}
                                    </span>
                                    CASH
                                </span>
                                <span className="flex items-center gap-1">
                                    <span
                                        className="inline-flex items-center justify-center w-3.5 h-3.5 border-2 rounded-sm text-white text-[8px]"
                                        style={{ borderColor: COLOR, backgroundColor: paymentType === 'BANK' ? COLOR : 'white' }}
                                    >
                                        {paymentType === 'BANK' ? '✓' : ''}
                                    </span>
                                    BANK
                                </span>
                            </div>
                            <p className="text-xs font-semibold">
                                TANGGAL : <span className="font-semibold">{printDate}</span>
                            </p>
                        </div>
                    </div>

                    {/* ── Voucher No ── */}
                    <div className="flex items-baseline gap-2 mb-3">
                        <span className="text-sm font-bold">VOUCHER NO. :</span>
                        <span className="text-lg font-semibold border-b flex-1 max-w-xs pl-1 print:block print:visible" style={{ borderColor: COLOR }}>
                            {voucherNo}
                        </span>
                    </div>

                    {/* ── Invoice Table ── */}
                    <table className="w-full border-collapse text-xs" style={{ borderColor: COLOR }}>
                        <thead>
                            <tr>
                                <th className="border p-2 font-bold text-center w-[130px]" style={{ borderColor: COLOR }}>
                                    No.Invoice / Nota
                                </th>
                                <th className="border p-2 font-bold text-center" style={{ borderColor: COLOR }}>
                                    U R A I A N
                                </th>
                                <th className="border p-2 font-bold text-center w-[110px]" style={{ borderColor: COLOR }}>
                                    JUMLAH
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            {invoices.map((inv) => (
                                <tr key={inv.invoiceExchangeId}>
                                    <td className="border p-2" style={{ borderColor: COLOR }}>
                                        {inv.invoiceNumber || '-'}
                                    </td>
                                    <td className="border p-2" style={{ borderColor: COLOR }}>
                                        {inv.supplierName}{inv.poNumber ? ` - ${inv.poNumber}` : ''}
                                    </td>
                                    <td className="border p-2 text-right font-semibold whitespace-nowrap" style={{ borderColor: COLOR }}>
                                        {FormatNumberRounded(inv.outstandingAmount)}
                                    </td>
                                </tr>
                            ))}
                            {Array.from({ length: BLANK_ROWS }).map((_, i) => (
                                <tr key={`pad-${i}`}>
                                    <td className="border" style={{ borderColor: COLOR, height: '32px' }}>&nbsp;</td>
                                    <td className="border" style={{ borderColor: COLOR }}></td>
                                    <td className="border" style={{ borderColor: COLOR }}></td>
                                </tr>
                            ))}
                            <tr>
                                <td
                                    colSpan={2}
                                    className="border p-2 text-center font-bold"
                                    style={{ borderColor: COLOR }}
                                >
                                    TOTAL
                                </td>
                                <td
                                    className="border p-2 text-right font-bold whitespace-nowrap"
                                    style={{ borderColor: COLOR }}
                                >
                                    {FormatNumberRounded(total)}
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    {/* ── Terbilang / Dibayar / Diterima ── */}
                    <div className="border border-t-0 px-3 py-2 text-xs space-y-1.5" style={{ borderColor: COLOR }}>
                        <div className="flex items-start gap-1">
                            <span className="w-28 shrink-0 font-semibold">Terbilang :</span>
                            <span className="italic font-semibold">
                                # {terbilangText} #
                            </span>
                        </div>
                        <div className="flex items-baseline gap-1">
                            <span className="w-28 shrink-0 font-semibold">Dibayar pada :</span>
                            <span className="border-b flex-1" style={{ borderColor: COLOR }}>{printDate}</span>
                        </div>
                        <div className="flex items-baseline gap-1">
                            <span className="w-28 shrink-0 font-semibold">Diterima Oleh :</span>
                            <span className="font-bold">{supplierName}</span>
                        </div>
                    </div>

                    {/* ── Signature Grid ── */}
                    <table className="w-full border-collapse text-[10px] border-t-0" style={{ borderColor: COLOR }}>
                        <thead>
                            <tr>
                                <th className="border p-1.5 font-bold text-center" style={{ borderColor: COLOR }}>Unit</th>
                                <th className="border p-1.5 font-bold text-center" style={{ borderColor: COLOR }}>Accounting</th>
                                <th className="border p-1.5 font-bold text-center" style={{ borderColor: COLOR }}>Finance</th>
                                <th className="border p-1.5 font-bold text-center" style={{ borderColor: COLOR }}>Director</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td className="border p-2 align-top" style={{ borderColor: COLOR, minWidth: '100px' }}>
                                    <p className="text-[9px] text-gray-500 mb-10">Issued By :</p>
                                    <div className="border-b mb-1" style={{ borderColor: COLOR }}></div>
                                    <p className="text-[9px] text-gray-500 mb-8">Approved By :</p>
                                    <div className="border-b" style={{ borderColor: COLOR }}></div>
                                </td>
                                <td className="border p-2 align-top" style={{ borderColor: COLOR }}>
                                    <p className="text-[9px] text-gray-500 mb-10">Coded By :</p>
                                    <div className="border-b mb-1" style={{ borderColor: COLOR }}></div>
                                    <p className="text-[9px] text-gray-500 mb-8">Approved By :</p>
                                    <div className="border-b" style={{ borderColor: COLOR }}></div>
                                </td>
                                <td className="border p-2 align-top" style={{ borderColor: COLOR }}>
                                    <p className="text-[9px] text-gray-500 mb-10">Approved By :</p>
                                    <div className="border-b mb-1" style={{ borderColor: COLOR }}></div>
                                    <p className="text-[9px] text-gray-500 mb-8">Passed for payment</p>
                                    <div className="border-b" style={{ borderColor: COLOR }}></div>
                                </td>
                                <td className="border p-2 align-top" style={{ borderColor: COLOR }}>
                                    <div style={{ height: '96px' }}></div>
                                    <div className="border-b" style={{ borderColor: COLOR }}></div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <p className="text-[9px] italic font-semibold mt-1">
                        HARUS ADA : TANDA TANGAN, NAMA JELAS, TANGGAL
                    </p>
                </div>
            </div>

            <style>{`
                @media print {
                    @page { size: A4 portrait; margin: 10mm; }
                    body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
                }
            `}</style>
        </div>
    );
};

export default BuktiPengeluaran;
