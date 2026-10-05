import { useEffect } from 'react';
import { useParams, useNavigate, useSearchParams } from 'react-router-dom';
import { useGetPettyCashSettlementByIdQuery } from '@/store/api/pettyCash/pettyCashApiSlice';
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

const BLUE = '#003399'; // lebih bayar / penerimaan
const RED = '#C8102E';  // kurang bayar / pengembalian

const PettyCashSettlementReceipt = () => {
    const { id } = useParams();
    const navigate = useNavigate();
    const [searchParams] = useSearchParams();
    const type = (searchParams.get('type') || 'lebih').toLowerCase(); // 'kurang' | 'lebih'
    const isKurang = type === 'kurang';

    const { data, isLoading, error } = useGetPettyCashSettlementByIdQuery(id, { skip: !id });

    useEffect(() => {
        document.title = isKurang ? 'Bukti Pengembalian Settlement' : 'Bukti Penerimaan Settlement';
    }, [isKurang]);

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
                <div className="text-red-500 mb-2">Data Settlement tidak ditemukan.</div>
                <Button onClick={() => navigate('/petty-cash/settlement')}>Kembali</Button>
            </div>
        );
    }

    const s: any = data.data;
    const approved = Number(s.request?.approvedAmount ?? s.request?.amount ?? 0);
    const total = Number(s.totalAmount) || 0;
    const shortfall = s.request && total > approved ? total - approved : 0;
    const returnAmount = Number(s.returnAmount) || 0;
    const printDate = s.settlementDate;
    const purpose = s.request?.purpose || s.notes || '-';
    const requester = s.request?.requesterName || '';
    const reqNo = s.request?.requestNo || '';

    const amount = isKurang ? shortfall : returnAmount;
    const color = isKurang ? RED : BLUE;
    const title = isKurang ? 'BUKTI PENGELUARAN' : 'BUKTI PENERIMAAN';
    const subtitle = isKurang ? '(Kurang Bayar / Talangan Settlement)' : '(Lebih Bayar / Sisa Dana Settlement)';
    const uraian = isKurang
        ? `Pengembalian/talangan kurang bayar settlement ${s.settlementNo} - ${purpose}`
        : `Penerimaan sisa dana (lebih bayar) settlement ${s.settlementNo} - ${purpose}`;
    const dateLabel = isKurang ? 'Dibayar pada :' : 'Diterima pada :';
    // Yang menerima uang: kurang bayar -> pemohon; lebih bayar -> petty cash
    const receivedByName = isKurang ? (requester || '....................') : s.unitName;
    const handedByName = isKurang ? s.unitName : (requester || '....................');
    const col1Label = isKurang ? 'Diserahkan (Petty Cash)' : 'Diserahkan (Pemohon)';
    const col2Label = isKurang ? 'Diterima (Pemohon)' : 'Diterima (Petty Cash)';

    const ActionBar = () => (
        <div className="max-w-[210mm] mx-auto mb-4 flex items-center justify-between px-4 print:hidden">
            <Button variant="outline" className="gap-2" onClick={() => navigate(`/petty-cash/settlement/${id}`)}>
                <ArrowLeft className="w-4 h-4" />
                Kembali
            </Button>
            <Button className="gap-2" style={{ backgroundColor: color }} onClick={handlePrint}>
                <Printer className="w-4 h-4" />
                Cetak
            </Button>
        </div>
    );

    if (amount <= 0) {
        return (
            <div className="min-h-screen bg-gray-100 py-8">
                <ActionBar />
                <div className="max-w-[210mm] mx-auto bg-white p-10 text-center text-gray-500 shadow-md">
                    Settlement {s.settlementNo} tidak memiliki {isKurang ? 'kurang bayar (talangan)' : 'lebih bayar (penerimaan sisa dana)'}.
                </div>
            </div>
        );
    }

    const terbilangText = `${terbilang(Math.round(amount)).trim()} Rupiah`;

    return (
        <div className="min-h-screen bg-gray-100 py-8 print:bg-white print:py-0">
            <ActionBar />
            <div className="max-w-[210mm] mx-auto bg-white shadow-md print:shadow-none">
                <div className="p-6 print:p-4 font-sans" style={{ color }}>
                    {/* Header */}
                    <div className="flex items-start justify-between mb-3">
                        <div className="flex items-start gap-3">
                            <img src="/assets/images/logo-thb-transparent.png" alt="Logo" className="w-16 h-16 object-contain" />
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
                            <h2 className="text-xl font-bold tracking-wider mb-1">{title}</h2>
                            <div className="text-[10px] font-semibold mb-1">{subtitle}</div>
                            <div className="text-xs flex items-baseline gap-1 justify-end">
                                <span className="font-semibold">TANGGAL :</span>
                                <span className="font-semibold">{formatDate(printDate)}</span>
                            </div>
                        </div>
                    </div>

                    {/* Voucher No */}
                    <div className="flex items-baseline gap-2 mb-1 text-sm">
                        <span className="font-bold">VOUCHER NO. :</span>
                        <span className="font-semibold border-b min-w-[200px] pl-1" style={{ borderColor: color }}>{s.settlementNo}</span>
                    </div>

                    {/* Table */}
                    <table className="w-full border-collapse text-xs mt-2" style={{ borderColor: color }}>
                        <thead>
                            <tr>
                                <th className="border p-1 font-bold text-center w-[120px]" style={{ borderColor: color }}>No.Ref Permintaan</th>
                                <th className="border p-1 font-bold text-center" style={{ borderColor: color }}>U R A I A N</th>
                                <th className="border p-1 font-bold text-center w-[120px]" style={{ borderColor: color }}>JUMLAH</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td className="border p-1 align-top" style={{ borderColor: color }}>{reqNo || '-'}</td>
                                <td className="border p-1 align-top font-semibold italic" style={{ borderColor: color }}>
                                    {uraian}
                                    {requester && <div className="text-[10px] mt-1">({isKurang ? 'untuk' : 'dari'}: {requester})</div>}
                                </td>
                                <td className="border p-1 align-top text-right font-semibold" style={{ borderColor: color }}>{formatNumber(amount)}</td>
                            </tr>
                            {[1, 2].map((i) => (
                                <tr key={i}>
                                    <td className="border p-1" style={{ borderColor: color }}>&nbsp;</td>
                                    <td className="border p-1" style={{ borderColor: color }}></td>
                                    <td className="border p-1" style={{ borderColor: color }}></td>
                                </tr>
                            ))}
                            <tr>
                                <td colSpan={2} className="border p-1 text-center font-bold" style={{ borderColor: color }}>TOTAL</td>
                                <td className="border p-1 text-right font-bold" style={{ borderColor: color }}>{formatNumber(amount)}</td>
                            </tr>
                        </tbody>
                    </table>

                    {/* Terbilang / Tanggal / Diterima */}
                    <div className="border border-t-0 p-2 text-xs space-y-1" style={{ borderColor: color }}>
                        <div className="flex items-baseline">
                            <span className="w-32 shrink-0 font-semibold">Terbilang :</span>
                            <span className="flex-1 font-semibold italic"># {terbilangText} #</span>
                        </div>
                        <div className="flex items-baseline">
                            <span className="w-32 shrink-0 font-semibold">{dateLabel}</span>
                            <span className="flex-1 border-b min-w-[150px]" style={{ borderColor: color }}>{formatDate(printDate)}</span>
                        </div>
                        <div className="flex items-baseline">
                            <span className="w-32 shrink-0 font-semibold">Diterima Oleh :</span>
                            <span className="flex-1 italic">( {receivedByName} ) &nbsp;&nbsp;&nbsp; Tgl : {formatDate(printDate)}</span>
                        </div>
                    </div>

                    {/* Signature Footer */}
                    <table className="w-full border-collapse text-xs border-t-0" style={{ borderColor: color }}>
                        <thead>
                            <tr>
                                <th className="border p-1 font-bold text-center" style={{ borderColor: color }}>{col1Label}</th>
                                <th className="border p-1 font-bold text-center" style={{ borderColor: color }}>{col2Label}</th>
                                <th className="border p-1 font-bold text-center" style={{ borderColor: color }}>Accounting</th>
                                <th className="border p-1 font-bold text-center" style={{ borderColor: color }}>Finance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td className="border p-1 align-top" style={{ borderColor: color }}>
                                    <div className="h-16"></div>
                                    <div className="border-t pt-0.5 text-[10px] text-center" style={{ borderColor: color }}>( {handedByName} )</div>
                                </td>
                                <td className="border p-1 align-top" style={{ borderColor: color }}>
                                    <div className="h-16"></div>
                                    <div className="border-t pt-0.5 text-[10px] text-center" style={{ borderColor: color }}>( {receivedByName} )</div>
                                </td>
                                <td className="border p-1 align-top" style={{ borderColor: color }}>
                                    <div className="h-16"></div>
                                    <div className="border-t pt-0.5 text-[10px] text-center" style={{ borderColor: color }}>(.................)</div>
                                </td>
                                <td className="border p-1 align-top" style={{ borderColor: color }}>
                                    <div className="h-16"></div>
                                    <div className="border-t pt-0.5 text-[10px] text-center" style={{ borderColor: color }}>(.................)</div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div className="text-[9px] italic mt-1 font-semibold">HARUS ADA : TANDA TANGAN, NAMA JELAS, TANGGAL</div>
                </div>
            </div>
            <style>{`@media print { @page { size: A4 portrait; margin: 10mm; } body { -webkit-print-color-adjust: exact; print-color-adjust: exact; } }`}</style>
        </div>
    );
};

export default PettyCashSettlementReceipt;
