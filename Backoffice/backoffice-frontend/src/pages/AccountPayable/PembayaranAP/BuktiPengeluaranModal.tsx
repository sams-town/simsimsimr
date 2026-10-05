import { useState, useRef } from 'react';
import { X, Printer } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { FormatNumberRounded } from '@/utils/formatNumber';

interface InvoiceItem {
    invoiceExchangeId: number;
    supplierName: string;
    poNumber: string;
    invoiceNumber: string;
    outstandingAmount: number;
}

interface BuktiPengeluaranModalProps {
    isOpen: boolean;
    onClose: () => void;
    selectedInvoices: InvoiceItem[];
}

const COLOR = '#8B1A1A';

const terbilang = (angka: number): string => {
    const huruf = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
    if (angka < 12) return huruf[angka];
    if (angka < 20) return huruf[angka - 10] + ' Belas';
    if (angka < 100) return huruf[Math.floor(angka / 10)] + ' Puluh ' + huruf[angka % 10];
    if (angka < 200) return 'Seratus ' + terbilang(angka - 100);
    if (angka < 1000) return huruf[Math.floor(angka / 100)] + ' Ratus ' + terbilang(angka % 100);
    if (angka < 2000) return 'Seribu ' + terbilang(angka - 1000);
    if (angka < 1000000) return terbilang(Math.floor(angka / 1000)) + ' Ribu ' + terbilang(angka % 1000);
    if (angka < 1000000000) return terbilang(Math.floor(angka / 1000000)) + ' Juta ' + terbilang(angka % 1000000);
    return terbilang(Math.floor(angka / 1000000000)) + ' Miliar ' + terbilang(angka % 1000000000);
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

const BuktiPengeluaranModal = ({ isOpen, onClose, selectedInvoices }: BuktiPengeluaranModalProps) => {
    const today = new Date();
    const [paymentType, setPaymentType] = useState<'CASH' | 'BANK'>('CASH');
    const [voucherNo, setVoucherNo] = useState(() => generateVoucherNo(today));
    const [printDate, setPrintDate] = useState(() => formatDateSlash(today));

    const printRef = useRef<HTMLDivElement>(null);

    if (!isOpen) return null;

    const total = selectedInvoices.reduce((sum, inv) => sum + (inv.outstandingAmount || 0), 0);
    const terbilangText = `${terbilang(Math.round(total)).trim()} Rupiah`;
    const supplierName = selectedInvoices[0]?.supplierName || '';

    const handlePrint = () => window.print();

    return (
        <div className="fixed inset-0 z-50 flex items-start justify-center bg-black/50 overflow-y-auto py-8 print:bg-white print:py-0 print:block print:overflow-visible">
            {/* Action Bar - hidden on print */}
            <div className="w-full max-w-[210mm] mx-auto mb-4 flex items-center justify-between px-2 print:hidden">
                <Button variant="outline" className="gap-2 bg-white" onClick={onClose}>
                    <X className="w-4 h-4" />
                    Tutup
                </Button>
                <Button
                    className="gap-2 text-white"
                    style={{ backgroundColor: COLOR }}
                    onClick={handlePrint}
                >
                    <Printer className="w-4 h-4" />
                    Cetak
                </Button>
            </div>

            {/* Form Preview */}
            <div ref={printRef} className="w-full max-w-[210mm] mx-auto bg-white shadow-lg print:shadow-none">
                <div className="p-6 print:p-4 font-sans" style={{ color: COLOR }}>

                    {/* Header */}
                    <div className="flex items-start justify-between mb-3">
                        <div className="flex items-start gap-3">
                            <img
                                src="/assets/images/logo-thb-transparent.png"
                                alt="Logo"
                                className="w-16 h-16 object-contain"
                            />
                            <div>
                                <h1 className="text-base font-bold leading-tight">RUMAH SAKIT</h1>
                                <h1 className="text-base font-bold leading-tight">TAMAN HARAPAN BARU</h1>
                                <p className="text-[9px] leading-tight mt-1 max-w-[340px]" style={{ color: '#333' }}>
                                    Jl. Kaliabang Tengah No. 2, RT. 004 / RW. 023,<br />
                                    Kel. Pejuang Kec. Medan Satria - Kota Bekasi 17131<br />
                                    Telp. (021) 8898 1055 Fax. (021) 8886 8558
                                </p>
                            </div>
                        </div>
                        <div className="text-right">
                            <h2 className="text-xl font-bold tracking-wider mb-2">BUKTI PENGELUARAN</h2>
                            {/* CASH / BANK selection */}
                            <div className="flex items-center justify-end gap-4 text-xs font-semibold mb-1 print:hidden">
                                <label className="flex items-center gap-1 cursor-pointer">
                                    <input
                                        type="radio"
                                        name="paymentType"
                                        checked={paymentType === 'CASH'}
                                        onChange={() => setPaymentType('CASH')}
                                        style={{ accentColor: COLOR }}
                                    />
                                    CASH
                                </label>
                                <label className="flex items-center gap-1 cursor-pointer">
                                    <input
                                        type="radio"
                                        name="paymentType"
                                        checked={paymentType === 'BANK'}
                                        onChange={() => setPaymentType('BANK')}
                                        style={{ accentColor: COLOR }}
                                    />
                                    BANK
                                </label>
                            </div>
                            {/* Print-only CASH/BANK display */}
                            <div className="hidden print:flex items-center justify-end gap-4 text-xs font-semibold mb-1">
                                <span className="flex items-center gap-1">
                                    <span className="inline-block w-3 h-3 border rounded-sm" style={{ backgroundColor: paymentType === 'CASH' ? COLOR : 'transparent', borderColor: COLOR }}></span>
                                    CASH
                                </span>
                                <span className="flex items-center gap-1">
                                    <span className="inline-block w-3 h-3 border rounded-sm" style={{ backgroundColor: paymentType === 'BANK' ? COLOR : 'transparent', borderColor: COLOR }}></span>
                                    BANK
                                </span>
                            </div>
                            <div className="text-xs font-semibold">
                                <span>TANGGAL : </span>
                                <span className="print:hidden">
                                    <input
                                        type="text"
                                        value={printDate}
                                        onChange={(e) => setPrintDate(e.target.value)}
                                        className="border-b text-xs w-32 outline-none text-right bg-transparent"
                                        style={{ borderColor: COLOR, color: COLOR }}
                                    />
                                </span>
                                <span className="hidden print:inline">{printDate}</span>
                            </div>
                        </div>
                    </div>

                    {/* Voucher No */}
                    <div className="flex items-baseline gap-2 mb-2 text-sm">
                        <span className="font-bold">VOUCHER NO. :</span>
                        <span className="print:hidden flex-1">
                            <input
                                type="text"
                                value={voucherNo}
                                onChange={(e) => setVoucherNo(e.target.value)}
                                className="border-b outline-none font-semibold text-lg bg-transparent w-full max-w-xs"
                                style={{ borderColor: COLOR, color: COLOR }}
                            />
                        </span>
                        <span className="hidden print:inline font-semibold text-lg border-b min-w-[200px] pl-1" style={{ borderColor: COLOR }}>
                            {voucherNo}
                        </span>
                    </div>

                    {/* Invoice Table */}
                    <table className="w-full border-collapse text-xs mt-2" style={{ borderColor: COLOR }}>
                        <thead>
                            <tr>
                                <th
                                    className="border p-1 font-bold text-center w-[130px]"
                                    style={{ borderColor: COLOR }}
                                >
                                    No.Invoice / Nota
                                </th>
                                <th
                                    className="border p-1 font-bold text-center"
                                    style={{ borderColor: COLOR }}
                                >
                                    U R A I A N
                                </th>
                                <th
                                    className="border p-1 font-bold text-center w-[110px]"
                                    style={{ borderColor: COLOR }}
                                >
                                    JUMLAH
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            {selectedInvoices.map((inv) => (
                                <tr key={inv.invoiceExchangeId}>
                                    <td className="border p-1.5" style={{ borderColor: COLOR }}>
                                        {inv.invoiceNumber || '-'}
                                    </td>
                                    <td className="border p-1.5" style={{ borderColor: COLOR }}>
                                        {inv.supplierName}
                                        {inv.poNumber ? ` - ${inv.poNumber}` : ''}
                                    </td>
                                    <td className="border p-1.5 text-right font-semibold" style={{ borderColor: COLOR }}>
                                        {FormatNumberRounded(inv.outstandingAmount)}
                                    </td>
                                </tr>
                            ))}
                            {/* Blank rows to pad to at least 5 visible rows */}
                            {selectedInvoices.length < 3 && Array.from({ length: 3 - selectedInvoices.length }).map((_, i) => (
                                <tr key={`blank-${i}`}>
                                    <td className="border p-3" style={{ borderColor: COLOR }}>&nbsp;</td>
                                    <td className="border p-3" style={{ borderColor: COLOR }}></td>
                                    <td className="border p-3" style={{ borderColor: COLOR }}></td>
                                </tr>
                            ))}
                            {/* Total row */}
                            <tr>
                                <td
                                    colSpan={2}
                                    className="border p-1.5 text-center font-bold"
                                    style={{ borderColor: COLOR }}
                                >
                                    TOTAL
                                </td>
                                <td
                                    className="border p-1.5 text-right font-bold"
                                    style={{ borderColor: COLOR }}
                                >
                                    {FormatNumberRounded(total)}
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    {/* Terbilang / Tanggal / Diterima Oleh */}
                    <div className="border border-t-0 p-2 text-xs space-y-1.5" style={{ borderColor: COLOR }}>
                        <div className="flex items-baseline">
                            <span className="w-28 shrink-0 font-semibold">Terbilang :</span>
                            <span className="flex-1 font-semibold italic">
                                # {terbilangText} #
                            </span>
                        </div>
                        <div className="flex items-baseline">
                            <span className="w-28 shrink-0 font-semibold">Dibayar pada :</span>
                            <span className="flex-1 border-b" style={{ borderColor: COLOR }}>
                                {printDate}
                            </span>
                        </div>
                        <div className="flex items-baseline">
                            <span className="w-28 shrink-0 font-semibold">Diterima Oleh :</span>
                            <span className="flex-1 font-bold text-sm">{supplierName}</span>
                        </div>
                    </div>

                    {/* Signature Section */}
                    <table className="w-full border-collapse text-[10px] border-t-0" style={{ borderColor: COLOR }}>
                        <thead>
                            <tr>
                                <th className="border p-1 font-bold text-center" style={{ borderColor: COLOR }}>Unit</th>
                                <th className="border p-1 font-bold text-center" style={{ borderColor: COLOR }}>Accounting</th>
                                <th className="border p-1 font-bold text-center" style={{ borderColor: COLOR }}>Finance</th>
                                <th className="border p-1 font-bold text-center" style={{ borderColor: COLOR }}>Director</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                {/* Unit */}
                                <td className="border p-1 align-top" style={{ borderColor: COLOR, minHeight: '90px' }}>
                                    <div className="text-[9px] text-gray-600">Issued By :</div>
                                    <div className="h-14"></div>
                                    <div className="border-t pt-0.5" style={{ borderColor: COLOR }}></div>
                                    <div className="text-[9px] text-gray-600 mt-3">Approved By :</div>
                                    <div className="h-10"></div>
                                    <div className="border-t" style={{ borderColor: COLOR }}></div>
                                </td>
                                {/* Accounting */}
                                <td className="border p-1 align-top" style={{ borderColor: COLOR }}>
                                    <div className="text-[9px] text-gray-600">Coded By :</div>
                                    <div className="h-14"></div>
                                    <div className="border-t pt-0.5" style={{ borderColor: COLOR }}></div>
                                    <div className="text-[9px] text-gray-600 mt-3">Approved By :</div>
                                    <div className="h-10"></div>
                                    <div className="border-t" style={{ borderColor: COLOR }}></div>
                                </td>
                                {/* Finance */}
                                <td className="border p-1 align-top" style={{ borderColor: COLOR }}>
                                    <div className="text-[9px] text-gray-600">Approved By :</div>
                                    <div className="h-14"></div>
                                    <div className="border-t pt-0.5" style={{ borderColor: COLOR }}></div>
                                    <div className="text-[9px] text-gray-600 mt-3">Passed for payment</div>
                                    <div className="h-10"></div>
                                    <div className="border-t" style={{ borderColor: COLOR }}></div>
                                </td>
                                {/* Director */}
                                <td className="border p-1 align-top" style={{ borderColor: COLOR }}>
                                    <div className="h-28"></div>
                                    <div className="border-t pt-0.5" style={{ borderColor: COLOR }}></div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div className="text-[9px] italic mt-1 font-semibold">
                        HARUS ADA : TANDA TANGAN, NAMA JELAS, TANGGAL
                    </div>
                </div>
            </div>

            <style>{`
                @media print {
                    body * { visibility: hidden; }
                    .fixed { position: static !important; overflow: visible !important; background: white !important; padding: 0 !important; }
                    .fixed > div:first-child { display: none !important; }
                    .fixed > div:last-child,
                    .fixed > div:last-child * { visibility: visible !important; }
                    @page { size: A4 portrait; margin: 10mm; }
                    body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
                }
            `}</style>
        </div>
    );
};

export default BuktiPengeluaranModal;
