import { useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { useGetPettyCashRequestReceiptQuery } from '@/store/api/pettyCash/pettyCashApiSlice';
import { Button } from '@/components/ui/button';
import { ArrowLeft, Printer, Loader2 } from 'lucide-react';

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

const formatNumber = (n: number): string => new Intl.NumberFormat('id-ID').format(n || 0);

const formatDate = (d?: string | null): string => {
    if (!d) return '...... / ...... / ............';
    const date = new Date(d);
    // Pakai UTC agar konsisten dengan tampilan detail (FormatDate timeZone:'UTC')
    const day = String(date.getUTCDate()).padStart(2, '0');
    const month = String(date.getUTCMonth() + 1).padStart(2, '0');
    const year = date.getUTCFullYear();
    return `${day} / ${month} / ${year}`;
};

const RED = '#C8102E';

const PettyCashRequestReceipt = () => {
    const { id } = useParams();
    const navigate = useNavigate();
    const { data, isLoading, error } = useGetPettyCashRequestReceiptQuery(id, { skip: !id });

    useEffect(() => {
        document.title = 'Bukti Pengeluaran Petty Cash';
    }, []);

    const handlePrint = () => window.print();

    if (isLoading) {
        return (
            <div className="flex items-center justify-center h-64">
                <Loader2 className="h-8 w-8 animate-spin text-primary" />
            </div>
        );
    }

    if (error || !data?.data) {
        return (
            <div className="p-5 text-center">
                <div className="text-red-500 mb-2">Data Permintaan tidak ditemukan.</div>
                <Button onClick={() => navigate('/petty-cash/request')}>Kembali</Button>
            </div>
        );
    }

    const r = data.data;
    const amount = Number(r.amount) || 0;
    const terbilangText = `${terbilang(Math.round(amount)).trim()} Rupiah`;
    const printDate = r.approvedDate || r.requestDate;

    return (
        <div className="min-h-screen bg-gray-100 py-8 print:bg-white print:py-0">
            {/* Action Bar - hidden on print */}
            <div className="max-w-[210mm] mx-auto mb-4 flex items-center justify-between px-4 print:hidden">
                <Button variant="outline" className="gap-2" onClick={() => navigate(`/petty-cash/request/${id}`)}>
                    <ArrowLeft className="w-4 h-4" />
                    Kembali
                </Button>
                <Button className="gap-2" onClick={handlePrint}>
                    <Printer className="w-4 h-4" />
                    Cetak
                </Button>
            </div>

            {/* Receipt Sheet */}
            <div className="max-w-[210mm] mx-auto bg-white shadow-md print:shadow-none">
                <div className="p-6 print:p-4 font-sans" style={{ color: RED }}>
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
                                <p className="text-[9px] leading-tight mt-1 max-w-[360px]">
                                    Jl. Kaliabang Tengah No. 2, RT. 004 / RW. 023,<br />
                                    Kel. Pejuang Kec. Medan Satria - Kota Bekasi 17131<br />
                                    Telp. (021) 8898 1055 Fax. (021) 8886 8558
                                </p>
                            </div>
                        </div>

                        <div className="text-right">
                            <h2 className="text-xl font-bold tracking-wider mb-2">BUKTI PENGELUARAN</h2>
                            <div className="flex items-center justify-end gap-4 mb-1 text-xs">
                                <label className="flex items-center gap-1">
                                    <span className="inline-block w-4 h-4 border flex items-center justify-center text-[10px] font-bold" style={{ borderColor: RED }}>✓</span>
                                    <span>CASH</span>
                                </label>
                                <label className="flex items-center gap-1">
                                    <span className="inline-block w-4 h-4 border" style={{ borderColor: RED }}></span>
                                    <span>BANK</span>
                                </label>
                            </div>
                            <div className="text-xs flex items-baseline gap-1 justify-end">
                                <span className="font-semibold">TANGGAL :</span>
                                <span className="font-semibold">{formatDate(printDate)}</span>
                            </div>
                        </div>
                    </div>

                    {/* Voucher No */}
                    <div className="flex items-baseline gap-2 mb-1 text-sm">
                        <span className="font-bold">VOUCHER NO. :</span>
                        <span className="font-semibold border-b min-w-[200px] pl-1" style={{ borderColor: RED }}>
                            {r.voucherNo}
                        </span>
                    </div>

                    {/* No.Invoice / Nota — URAIAN — JUMLAH */}
                    <table className="w-full border-collapse text-xs mt-2 border-t-0" style={{ borderColor: RED }}>
                        <thead>
                            <tr>
                                <th className="border p-1 font-bold text-center w-[120px]" style={{ borderColor: RED }}>
                                    No.Invoice / Nota
                                </th>
                                <th className="border p-1 font-bold text-center" style={{ borderColor: RED }}>
                                    U R A I A N
                                </th>
                                <th className="border p-1 font-bold text-center w-[120px]" style={{ borderColor: RED }}>
                                    JUMLAH
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td className="border p-1 align-top" style={{ borderColor: RED }}>&nbsp;</td>
                                <td className="border p-1 align-top font-semibold italic" style={{ borderColor: RED }}>
                                    {r.purpose}
                                    {r.costCenterName && (
                                        <div className="text-[10px] mt-1">({r.costCenterName})</div>
                                    )}
                                </td>
                                <td className="border p-1 align-top text-right font-semibold" style={{ borderColor: RED }}>
                                    {formatNumber(amount)}
                                </td>
                            </tr>
                            {/* Empty rows */}
                            {[1, 2].map((i) => (
                                <tr key={i}>
                                    <td className="border p-1" style={{ borderColor: RED }}>&nbsp;</td>
                                    <td className="border p-1" style={{ borderColor: RED }}></td>
                                    <td className="border p-1" style={{ borderColor: RED }}></td>
                                </tr>
                            ))}
                            <tr>
                                <td colSpan={2} className="border p-1 text-center font-bold" style={{ borderColor: RED }}>
                                    TOTAL
                                </td>
                                <td className="border p-1 text-right font-bold" style={{ borderColor: RED }}>
                                    {formatNumber(amount)}
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    {/* Terbilang / Dibayar / Diterima */}
                    <div className="border border-t-0 p-2 text-xs space-y-1" style={{ borderColor: RED }}>
                        <div className="flex items-baseline">
                            <span className="w-32 shrink-0 font-semibold">Terbilang :</span>
                            <span className="flex-1 font-semibold italic">
                                # {terbilangText} #
                            </span>
                        </div>
                        <div className="flex items-baseline">
                            <span className="w-32 shrink-0 font-semibold">Dibayar pada :</span>
                            <span className="flex-1 border-b min-w-[150px]" style={{ borderColor: RED }}>
                                {formatDate(printDate)}
                            </span>
                        </div>
                        <div className="flex items-baseline">
                            <span className="w-32 shrink-0 font-semibold">Diterima Oleh :</span>
                            <span className="flex-1 italic">
                                ( {r.requesterName || '....................'} ) &nbsp;&nbsp;&nbsp; Tgl : {formatDate(printDate)}
                            </span>
                        </div>
                    </div>

                    {/* Signature Footer */}
                    <table className="w-full border-collapse text-xs mt-0 border-t-0" style={{ borderColor: RED }}>
                        <thead>
                            <tr>
                                <th className="border p-1 font-bold text-center" style={{ borderColor: RED }}>Unit</th>
                                <th className="border p-1 font-bold text-center" style={{ borderColor: RED }}>Accounting</th>
                                <th className="border p-1 font-bold text-center" style={{ borderColor: RED }}>Finance</th>
                                <th className="border p-1 font-bold text-center" style={{ borderColor: RED }}>Director</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td className="border p-1" style={{ borderColor: RED }}>
                                    <div className="text-[10px]">Issued By :</div>
                                    <div className="h-16"></div>
                                    <div className="border-t pt-0.5 text-[10px]" style={{ borderColor: RED }}>
                                        Approved By :
                                    </div>
                                    <div className="h-10"></div>
                                </td>
                                <td className="border p-1" style={{ borderColor: RED }}>
                                    <div className="text-[10px]">Coded By :</div>
                                    <div className="h-16"></div>
                                    <div className="border-t pt-0.5 text-[10px]" style={{ borderColor: RED }}>
                                        Approved By :
                                    </div>
                                    <div className="h-10"></div>
                                </td>
                                <td className="border p-1" style={{ borderColor: RED }}>
                                    <div className="text-[10px]">Approved By :</div>
                                    <div className="h-16"></div>
                                    <div className="border-t pt-0.5 text-[10px]" style={{ borderColor: RED }}>
                                        Passed for payment
                                    </div>
                                    <div className="h-10"></div>
                                </td>
                                <td className="border p-1" style={{ borderColor: RED }}>
                                    <div className="text-[10px]">&nbsp;</div>
                                    <div className="h-16"></div>
                                    <div className="border-t pt-0.5 text-[10px]" style={{ borderColor: RED }}>
                                        &nbsp;
                                    </div>
                                    <div className="h-10"></div>
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
                    @page {
                        size: A4 portrait;
                        margin: 10mm;
                    }
                    body {
                        -webkit-print-color-adjust: exact;
                        print-color-adjust: exact;
                    }
                }
            `}</style>
        </div>
    );
};

export default PettyCashRequestReceipt;
