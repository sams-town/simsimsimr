import { useEffect, useState } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { ArrowLeft, Printer, FileText, Pencil } from 'lucide-react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { FormatNumber } from '@/utils/formatNumber';
import { FormatDate } from '@/utils/formatDate';
import { BankFormMakerConfig } from './config';
import { isVoucherLocked } from '@/utils/editWindow';
import { useGetBankVoucherDetailQuery } from '@/store/api/bankFormVoucher/bankFormVoucherApiSlice';

interface VoucherDetail {
    id: number;
    voucherNumber: string;
    voucherDate?: string;
    voucherType: string;
    paymentType: string;
    status: string;
    personName?: string;
    coaCode?: string;
    coaName?: string;
    amount: number;
    discountPercent: number;
    discountAmount: number;
    pph23Enabled: boolean;
    pph23Percent: number;
    pph23Amount: number;
    purpose?: string;
    totalAmount: number;
    notes?: string;
    recipientBankName?: string;
    recipientAccountName?: string;
    recipientAccountNumber?: string;
    createdDate?: string;
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

const formatPrintDate = (date?: string | Date | null): string => {
    const d = date ? new Date(date) : new Date();
    const dd = String(d.getDate()).padStart(2, '0');
    const mm = String(d.getMonth() + 1).padStart(2, '0');
    const yyyy = d.getFullYear();
    return `${dd} / ${mm} / ${yyyy}`;
};

const BankFormMakerDetail = ({ config }: { config: BankFormMakerConfig }) => {
    const { id } = useParams<{ id: string }>();
    const navigate = useNavigate();
    const [printPaymentType, setPrintPaymentType] = useState<'CASH' | 'BANK'>('CASH');
    const PRINT_COLOR = config.printColor;

    const { data, isLoading } = useGetBankVoucherDetailQuery(id, { skip: !id });
    const voucher: VoucherDetail | undefined = data?.data;

    useEffect(() => {
        if (voucher?.paymentType === 'BANK' || voucher?.paymentType === 'CASH') setPrintPaymentType(voucher.paymentType);
    }, [voucher?.paymentType]);

    const amount = voucher?.amount || 0;
    const discount = voucher?.discountAmount || 0;
    const pph = voucher?.pph23Enabled ? (voucher?.pph23Amount || 0) : 0;
    const total = voucher?.totalAmount || 0;
    const personName = voucher?.personName || '';
    const voucherDateStr = voucher?.voucherDate ? FormatDate(voucher.voucherDate) : '-';
    const locked = isVoucherLocked(voucher?.createdDate);

    return (
        <>
            {/* Screen layout */}
            <div className="flex flex-col gap-6 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 print:hidden">
                <div className="flex items-center justify-between mt-4">
                    <div className="flex items-center gap-3">
                        <div className="p-2 rounded-lg" style={{ backgroundColor: `${config.accent}20` }}>
                            <FileText className="h-5 w-5" style={{ color: config.accent }} />
                        </div>
                        <div>
                            <h1 className="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                                {voucher?.voucherNumber || config.pageTitle}
                            </h1>
                            <p className="text-sm text-gray-500 mt-1">Detail {config.docTitle.replace('BUKTI', 'Bukti')}</p>
                        </div>
                    </div>
                    <div className="flex gap-2 items-center">
                        <Button variant="outline" onClick={() => navigate(config.basePath)} size="sm" className="gap-2">
                            <ArrowLeft className="h-4 w-4" /> Kembali
                        </Button>

                        {/* CASH / BANK toggle for print */}
                        <div className="flex rounded-md border border-gray-200 overflow-hidden text-sm">
                            <button type="button" className={`px-3 py-1.5 font-semibold transition-colors ${printPaymentType === 'CASH' ? 'bg-green-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50'}`} onClick={() => setPrintPaymentType('CASH')}>CASH</button>
                            <button type="button" className={`px-3 py-1.5 font-semibold transition-colors ${printPaymentType === 'BANK' ? 'bg-blue-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50'}`} onClick={() => setPrintPaymentType('BANK')}>BANK</button>
                        </div>

                        {locked ? (
                            <span className="text-xs text-gray-400 italic px-2" title="Bukti hanya dapat diubah dalam 3 hari sejak dibuat">
                                Terkunci (lebih dari 3 hari)
                            </span>
                        ) : (
                            <Button variant="outline" onClick={() => navigate(`${config.basePath}/${id}/edit`)} size="sm" className="gap-2">
                                <Pencil className="h-4 w-4" /> Ubah
                            </Button>
                        )}
                        <Button variant="outline" onClick={() => window.print()} size="sm" className="gap-2">
                            <Printer className="h-4 w-4" /> Cetak
                        </Button>
                    </div>
                </div>

                {/* Info cards */}
                <div className="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-[#1b2e4b] shadow-sm overflow-hidden">
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 divide-y md:divide-y-0 md:divide-x border-gray-200 dark:border-gray-700">
                        <div className="p-5">
                            <p className="text-xs text-gray-500 uppercase tracking-wide font-medium mb-1.5">No. Voucher</p>
                            <p className="text-lg font-bold font-mono" style={{ color: config.accent }}>{voucher?.voucherNumber || '-'}</p>
                        </div>
                        <div className="p-5">
                            <p className="text-xs text-gray-500 uppercase tracking-wide font-medium mb-1.5">Tanggal</p>
                            <p className="text-lg font-bold text-gray-900 dark:text-white">{voucherDateStr}</p>
                        </div>
                        <div className="p-5">
                            <p className="text-xs text-gray-500 uppercase tracking-wide font-medium mb-1.5">{config.personLabel}</p>
                            <p className="text-base font-semibold text-gray-900 dark:text-white truncate">{personName || '-'}</p>
                        </div>
                        <div className="p-5" style={{ backgroundColor: `${config.accent}15` }}>
                            <p className="text-xs text-gray-500 uppercase tracking-wide font-medium mb-1.5">Total</p>
                            <p className="text-xl font-bold" style={{ color: config.accent }}>{FormatNumber(total)}</p>
                        </div>
                    </div>
                </div>

                {/* Detail card */}
                <Card className="shadow-sm border border-gray-200 dark:border-gray-800">
                    <CardHeader className="bg-gray-50/50 dark:bg-[#1b2e4b] border-b border-gray-100 dark:border-gray-700 px-6 py-4">
                        <CardTitle className="text-base font-semibold">Rincian</CardTitle>
                    </CardHeader>
                    <CardContent className="p-6">
                        {isLoading ? (
                            <p className="text-gray-500 text-sm">Memuat...</p>
                        ) : (
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                                <Field label={config.accountLabel} value={voucher?.coaName || '-'} />
                                <Field label={config.amountLabel} value={`Rp ${FormatNumber(amount)}`} mono />
                                {config.showTax && (
                                    <>
                                        <Field label={`Diskon (${voucher?.discountPercent || 0}%)`} value={`Rp ${FormatNumber(discount)}`} mono />
                                        <Field label={`PPh 23 ${voucher?.pph23Enabled ? `(${voucher?.pph23Percent || 0}%)` : '(tidak dipotong)'}`} value={`Rp ${FormatNumber(pph)}`} mono />
                                    </>
                                )}
                                <Field label="Total" value={`Rp ${FormatNumber(total)}`} mono accent={config.accent} />
                                <Field label="Tipe" value={voucher?.paymentType || '-'} />
                                {config.showRecipientAccount && (
                                    <div className="md:col-span-2">
                                        <Field
                                            label="Rekening Penerima"
                                            value={
                                                [voucher?.recipientBankName, voucher?.recipientAccountName, voucher?.recipientAccountNumber]
                                                    .filter(Boolean).join(' - ') || '-'
                                            }
                                        />
                                    </div>
                                )}
                                <div className="md:col-span-2">
                                    <Field label="Keperluan" value={voucher?.purpose || '-'} />
                                </div>
                                {voucher?.notes && (
                                    <div className="md:col-span-2">
                                        <Field label="Catatan" value={voucher.notes} />
                                    </div>
                                )}
                            </div>
                        )}
                    </CardContent>
                </Card>
            </div>

            {/* ===== PRINT LAYOUT ===== */}
            <div className="fmr-print hidden print:block font-sans text-black bg-white p-[10mm] absolute top-0 left-0 w-full min-h-screen z-[9999]" style={{ WebkitPrintColorAdjust: 'exact', printColorAdjust: 'exact' } as any}>
                <style>{`
                    @media print {
                        @page { size: A4 portrait; margin: 10mm; }
                        body { margin: 0; }
                        * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
                        .fmr-print table, .fmr-print table thead tr, .fmr-print table thead tr th,
                        .fmr-print table tbody tr, .fmr-print table tbody tr td { border-color: ${PRINT_COLOR} !important; }
                    }
                `}</style>

                {/* Header */}
                <div className="flex items-start justify-between pb-3 mb-3" style={{ borderBottom: `2px solid ${PRINT_COLOR}` }}>
                    <div className="flex items-start gap-3">
                        <img src="/assets/images/logo-thb-transparent.png" alt="Logo" className="w-[55px] h-[55px] object-contain" />
                        <div>
                            <p className="text-[11px] font-bold leading-tight" style={{ color: PRINT_COLOR }}>RUMAH SAKIT</p>
                            <p className="text-[14px] font-extrabold leading-tight" style={{ color: PRINT_COLOR }}>TAMAN HARAPAN BARU</p>
                            <p className="text-[8px] mt-1 leading-snug" style={{ color: PRINT_COLOR }}>Jl. Kaliabang Tengah No. 2, RT. 004 / RW. 023,</p>
                            <p className="text-[8px] leading-snug" style={{ color: PRINT_COLOR }}>Kel. Pejuang Kec. Medan Satria - Kota Bekasi 17131</p>
                            <p className="text-[8px] leading-snug" style={{ color: PRINT_COLOR }}>Telp. (021) 8898 1055 Fax. (021) 8886 8558</p>
                        </div>
                    </div>
                    <div className="text-right">
                        <h1 className="text-[20px] font-extrabold tracking-wide" style={{ color: PRINT_COLOR }}>{config.docTitle}</h1>
                        <div className="flex items-center justify-end gap-4 mt-1 text-[10px] font-semibold" style={{ color: PRINT_COLOR }}>
                            <label className="flex items-center gap-1">
                                <span style={{ display: 'inline-block', width: 12, height: 12, border: `1.5px solid ${PRINT_COLOR}`, textAlign: 'center', lineHeight: '10px', fontSize: 10, color: PRINT_COLOR, fontWeight: 'bold' }}>{printPaymentType === 'CASH' ? '✓' : ''}</span>
                                CASH
                            </label>
                            <label className="flex items-center gap-1">
                                <span style={{ display: 'inline-block', width: 12, height: 12, border: `1.5px solid ${PRINT_COLOR}`, textAlign: 'center', lineHeight: '10px', fontSize: 10, color: PRINT_COLOR, fontWeight: 'bold' }}>{printPaymentType === 'BANK' ? '✓' : ''}</span>
                                BANK
                            </label>
                        </div>
                        <p className="text-[10px] font-semibold mt-1" style={{ color: PRINT_COLOR }}>TANGGAL : {formatPrintDate(voucher?.voucherDate)}</p>
                    </div>
                </div>

                {/* Voucher number */}
                <p className="text-[11px] font-bold mb-4" style={{ color: '#000' }}>
                    <span style={{ color: PRINT_COLOR }}>VOUCHER NO. : </span>
                    <span style={{ borderBottom: `1.5px solid #000`, paddingBottom: 1, fontSize: 14 }}>{voucher?.voucherNumber || '-'}</span>
                </p>

                {/* Table */}
                <table className="w-full mb-0 text-[10px]" style={{ borderCollapse: 'collapse', border: `1px solid ${PRINT_COLOR}` }}>
                    <thead>
                        <tr style={{ color: PRINT_COLOR }}>
                            <th style={{ border: `1px solid ${PRINT_COLOR}`, padding: '6px 8px', textAlign: 'center', width: '28%', fontWeight: 'bold' }}>AKUN</th>
                            <th style={{ border: `1px solid ${PRINT_COLOR}`, padding: '6px 8px', textAlign: 'center', fontWeight: 'bold', letterSpacing: '0.15em' }}>U R A I A N</th>
                            <th style={{ border: `1px solid ${PRINT_COLOR}`, padding: '6px 8px', textAlign: 'center', width: '20%', fontWeight: 'bold' }}>JUMLAH</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style={{ border: `1px solid ${PRINT_COLOR}`, padding: '5px 8px' }}>{voucher?.coaName || '-'}</td>
                            <td style={{ border: `1px solid ${PRINT_COLOR}`, padding: '5px 8px' }}>{voucher?.purpose || '-'}</td>
                            <td style={{ border: `1px solid ${PRINT_COLOR}`, padding: '5px 8px', textAlign: 'right', fontFamily: 'monospace' }}>{FormatNumber(amount)}</td>
                        </tr>
                        {config.showTax && discount > 0 && (
                            <tr>
                                <td colSpan={2} style={{ border: `1px solid ${PRINT_COLOR}`, padding: '5px 8px', textAlign: 'right', fontStyle: 'italic' }}>Diskon ({voucher?.discountPercent || 0}%)</td>
                                <td style={{ border: `1px solid ${PRINT_COLOR}`, padding: '5px 8px', textAlign: 'right', fontFamily: 'monospace' }}>- {FormatNumber(discount)}</td>
                            </tr>
                        )}
                        {config.showTax && pph > 0 && (
                            <tr>
                                <td colSpan={2} style={{ border: `1px solid ${PRINT_COLOR}`, padding: '5px 8px', textAlign: 'right', fontStyle: 'italic' }}>PPh 23 ({voucher?.pph23Percent || 0}%)</td>
                                <td style={{ border: `1px solid ${PRINT_COLOR}`, padding: '5px 8px', textAlign: 'right', fontFamily: 'monospace' }}>- {FormatNumber(pph)}</td>
                            </tr>
                        )}
                        <tr style={{ fontWeight: 'bold' }}>
                            <td colSpan={2} style={{ border: `1px solid ${PRINT_COLOR}`, padding: '6px 8px', textAlign: 'center', letterSpacing: '0.1em', color: PRINT_COLOR }}>TOTAL</td>
                            <td style={{ border: `1px solid ${PRINT_COLOR}`, padding: '6px 8px', textAlign: 'right', fontFamily: 'monospace' }}>{FormatNumber(total)}</td>
                        </tr>
                    </tbody>
                </table>

                {/* Terbilang + Diterima/Dibayar */}
                <table className="w-full mb-4 text-[10px]" style={{ borderCollapse: 'collapse' }}>
                    <tbody>
                        <tr>
                            <td style={{ border: `1px solid ${PRINT_COLOR}`, padding: '4px 8px', width: '18%', fontWeight: 'bold', color: PRINT_COLOR, verticalAlign: 'top' }}>Terbilang :</td>
                            <td style={{ border: `1px solid ${PRINT_COLOR}`, padding: '4px 8px', fontStyle: 'italic', fontWeight: 'bold', color: PRINT_COLOR }}># {terbilang(Math.round(total))} Rupiah #</td>
                        </tr>
                        <tr>
                            <td style={{ border: `1px solid ${PRINT_COLOR}`, padding: '4px 8px', fontWeight: 'bold', color: PRINT_COLOR }}>Tanggal :</td>
                            <td style={{ border: `1px solid ${PRINT_COLOR}`, padding: '4px 8px', color: PRINT_COLOR }}>{formatPrintDate(voucher?.voucherDate)}</td>
                        </tr>
                        <tr>
                            <td style={{ border: `1px solid ${PRINT_COLOR}`, padding: '4px 8px', fontWeight: 'bold', color: PRINT_COLOR, verticalAlign: 'middle' }}>{config.receivedLabel}</td>
                            <td style={{ border: `1px solid ${PRINT_COLOR}`, padding: '4px 8px', fontWeight: 'bold', fontSize: 14, height: 26 }}>
                                {personName}
                                {config.showRecipientAccount && (voucher?.recipientBankName || voucher?.recipientAccountName || voucher?.recipientAccountNumber) && (
                                    <span style={{ fontWeight: 'normal', fontSize: 10, fontStyle: 'italic' }}>
                                        {'  '}(Rek: {[voucher?.recipientBankName, voucher?.recipientAccountName, voucher?.recipientAccountNumber].filter(Boolean).join(' - ')})
                                    </span>
                                )}
                            </td>
                        </tr>
                    </tbody>
                </table>

                {/* Signature block */}
                <div className="flex justify-end mt-2 text-[10px]">
                    <table style={{ width: '70%', borderCollapse: 'collapse', border: `1px solid ${PRINT_COLOR}` }}>
                        <thead>
                            <tr>
                                <th style={{ border: `1px solid ${PRINT_COLOR}`, padding: '5px 8px', textAlign: 'center', fontWeight: 'bold', color: PRINT_COLOR, width: '33.33%' }}>Submitted</th>
                                <th style={{ border: `1px solid ${PRINT_COLOR}`, padding: '5px 8px', textAlign: 'center', fontWeight: 'bold', color: PRINT_COLOR, width: '33.33%' }}>Finance</th>
                                <th style={{ border: `1px solid ${PRINT_COLOR}`, padding: '5px 8px', textAlign: 'center', fontWeight: 'bold', color: PRINT_COLOR, width: '33.33%' }}>Director</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style={{ height: 90 }}>
                                <td style={{ border: `1px solid ${PRINT_COLOR}`, padding: '6px 10px', verticalAlign: 'top' }}>
                                    <p className="mb-0">Issued By :</p>
                                    <div style={{ borderBottom: `1px solid #000`, marginTop: 48, marginBottom: 2 }}></div>
                                </td>
                                <td style={{ border: `1px solid ${PRINT_COLOR}`, padding: '6px 10px', verticalAlign: 'top' }}>
                                    <p className="mb-0">Acknowledged By:</p>
                                    <div style={{ borderBottom: `1px solid #000`, marginTop: 48, marginBottom: 2 }}></div>
                                </td>
                                <td style={{ border: `1px solid ${PRINT_COLOR}`, padding: '6px 10px', verticalAlign: 'top' }}>
                                    <p className="mb-0">Approved By:</p>
                                    <div style={{ borderBottom: `1px solid #000`, marginTop: 48, marginBottom: 2 }}></div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <p className="text-[8px] italic mt-2" style={{ color: PRINT_COLOR }}>HARUS ADA : TANDA TANGAN, NAMA JELAS, TANGGAL</p>
            </div>
        </>
    );
};

const Field = ({ label, value, mono, accent }: { label: string; value: string; mono?: boolean; accent?: string }) => (
    <div>
        <p className="text-xs text-gray-500 uppercase tracking-wide font-medium mb-1">{label}</p>
        <p className={`${mono ? 'font-mono' : ''} font-semibold text-gray-900 dark:text-white`} style={accent ? { color: accent } : undefined}>{value}</p>
    </div>
);

export default BankFormMakerDetail;
