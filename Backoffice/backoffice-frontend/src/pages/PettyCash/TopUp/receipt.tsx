import { useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { useGetPettyCashTopUpReceiptQuery } from '@/store/api/pettyCash/pettyCashApiSlice';
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
    if (!d) return '...../...../..........';
    const date = new Date(d);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    return `${day} / ${month} / ${year}`;
};

const PettyCashTopUpReceipt = () => {
    const { id } = useParams();
    const navigate = useNavigate();
    const { data, isLoading, error } = useGetPettyCashTopUpReceiptQuery(id, { skip: !id });

    useEffect(() => {
        document.title = 'Tanda Terima Top Up Petty Cash';
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
                <div className="text-red-500 mb-2">Data Top Up tidak ditemukan.</div>
                <Button onClick={() => navigate('/petty-cash/topup')}>Kembali</Button>
            </div>
        );
    }

    const r = data.data;
    const amount = Number(r.amount) || 0;
    const terbilangText = `${terbilang(Math.round(amount)).trim()} Rupiah`;
    const printDate = r.approvedDate || r.topUpDate;

    return (
        <div className="min-h-screen bg-gray-100 py-8 print:bg-white print:py-0">
            {/* Action Bar - hidden on print */}
            <div className="max-w-[210mm] mx-auto mb-4 flex items-center justify-between px-4 print:hidden">
                <Button variant="outline" className="gap-2" onClick={() => navigate(`/petty-cash/topup/${id}`)}>
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
                <div className="p-8 print:p-6 text-[#003399] font-sans">
                    {/* Header */}
                    <div className="flex items-start justify-between mb-4">
                        <div className="flex items-start gap-3">
                            <img
                                src="/assets/images/logo-thb-transparent.png"
                                alt="Logo"
                                className="w-16 h-16 object-contain"
                            />
                            <div>
                                <h1 className="text-base font-bold leading-tight">
                                    RUMAH SAKIT
                                </h1>
                                <h1 className="text-base font-bold leading-tight">
                                    TAMAN HARAPAN BARU
                                </h1>
                                <p className="text-[9px] leading-tight mt-1 max-w-[360px]">
                                    Jl. Kaliabang Tengah No. 2, RT. 004 / RW. 023,<br />
                                    Kel. Pejuang Kec. Medan Satria - Kota Bekasi 17131<br />
                                    Tel. (021) 8898 1055 Fax. (021) 8886 8558
                                </p>
                            </div>
                        </div>
                        <div className="text-xs space-y-1 text-right pt-1">
                            <div className="flex items-baseline gap-1">
                                <span>No.Ref</span>
                                <span>:</span>
                                <span className="font-semibold border-b border-[#003399] min-w-[120px] inline-block text-left pl-1">
                                    {r.topUpNo}
                                </span>
                            </div>
                            <div className="flex items-baseline gap-1">
                                <span>Tanggal</span>
                                <span>:</span>
                                <span className="font-semibold border-b border-[#003399] min-w-[120px] inline-block text-left pl-1">
                                    {formatDate(printDate)}
                                </span>
                            </div>
                        </div>
                    </div>

                    <hr className="border-t-2 border-[#003399] mb-6" />

                    {/* Title */}
                    <div className="text-center mb-8">
                        <h2 className="text-2xl font-bold tracking-[0.5em] inline-block">
                            T A N D A &nbsp; T E R I M A
                        </h2>
                    </div>

                    {/* Body */}
                    <div className="space-y-4 text-sm mb-12">
                        <div className="flex items-baseline">
                            <span className="w-40 shrink-0">Sudah terima dari</span>
                            <span className="mr-2">:</span>
                            <span className="flex-1 border-b border-[#003399] pb-0.5 font-semibold italic">
                                {r.handedByName || '-'} (Finance)
                            </span>
                        </div>

                        <div className="flex items-baseline">
                            <span className="w-40 shrink-0">No Invoice</span>
                            <span className="mr-2">:</span>
                            <span className="flex-1 border-b border-[#003399] pb-0.5 font-semibold italic">
                                {r.description} Rp {formatNumber(amount)} ({terbilangText})
                            </span>
                        </div>

                        {/* Empty lines for additional notes (matches paper form) */}
                        <div className="flex items-baseline">
                            <span className="w-40 shrink-0"></span>
                            <span className="mr-2 invisible">:</span>
                            <span className="flex-1 border-b border-[#003399] pb-3"></span>
                        </div>
                        <div className="flex items-baseline">
                            <span className="w-40 shrink-0"></span>
                            <span className="mr-2 invisible">:</span>
                            <span className="flex-1 border-b border-[#003399] pb-3"></span>
                        </div>
                        <div className="flex items-baseline">
                            <span className="w-40 shrink-0"></span>
                            <span className="mr-2 invisible">:</span>
                            <span className="flex-1 border-b border-[#003399] pb-3"></span>
                        </div>
                    </div>

                    {/* Signatures */}
                    <div className="grid grid-cols-2 gap-8 mt-12">
                        <div className="border border-[#003399] p-3">
                            <p className="text-sm font-semibold mb-1">Di Serahkan Oleh :</p>
                            <div className="h-24"></div>
                            <div className="border-t border-[#003399] pt-2 flex justify-between text-xs">
                                <div>
                                    <p>Tanggal</p>
                                    <p className="font-semibold mt-1">{formatDate(printDate)}</p>
                                </div>
                                <div className="text-right">
                                    <p>Tanda Tangan</p>
                                    <p className="font-semibold mt-1">({r.handedByName || '...........'})</p>
                                </div>
                            </div>
                        </div>

                        <div className="border border-[#003399] p-3">
                            <p className="text-sm font-semibold mb-1">Di Terima Oleh :</p>
                            <div className="h-24"></div>
                            <div className="border-t border-[#003399] pt-2 flex justify-between text-xs">
                                <div>
                                    <p>Tanggal</p>
                                    <p className="font-semibold mt-1">{formatDate(printDate)}</p>
                                </div>
                                <div className="text-right">
                                    <p>Tanda Tangan</p>
                                    <p className="font-semibold mt-1">({r.receivedByName || '...........'})</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <style>{`
                @media print {
                    @page {
                        size: A5 landscape;
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

export default PettyCashTopUpReceipt;
