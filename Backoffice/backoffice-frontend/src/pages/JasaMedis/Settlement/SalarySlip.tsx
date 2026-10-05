import React from 'react';

export interface SalarySlipItem {
    registrationNo: string;
    invoiceNo: string;
    medicalRecordNo: string;
    patientName: string;
    polyclinic: string;
    serviceName: string;
    paymentType: string;
    doctorShare: number;
}

export interface SalarySlipData {
    hospitalName: string;
    doctorName: string;
    period: string;
    items: SalarySlipItem[];
    subTotal: number;
    taxAmount: number;
    taxRate: number;
    afterTaxAmount: number;
    zakatAmount: number;
    zakatRate: number;
    netAmount: number;
    printedDate: string;
    printedBy: string;
}

interface SalarySlipProps {
    data: SalarySlipData;
}

export const SalarySlip = React.forwardRef<HTMLDivElement, SalarySlipProps>(({ data }, ref) => {
    const formatCurrency = (value: number) => {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
    };

    return (
        <div ref={ref} className="p-8 bg-white text-black font-sans max-w-[210mm] mx-auto text-xs">
            {/* Header */}
            <div className="text-center mb-6 border-b-2 border-black pb-4">
                <h1 className="text-xl font-bold uppercase tracking-wider">{data.hospitalName}</h1>
                <h2 className="text-lg font-semibold mt-1">SLIP JASA MEDIS DOKTER</h2>
            </div>

            {/* Info */}
            <div className="mb-4 flex justify-between items-start">
                <table className="text-sm">
                    <tbody>
                        <tr>
                            <td className="font-semibold pr-4 py-0.5">Nama Dokter</td>
                            <td>: {data.doctorName}</td>
                        </tr>
                        <tr>
                            <td className="font-semibold pr-4 py-0.5">Periode</td>
                            <td>: {data.period}</td>
                        </tr>
                    </tbody>
                </table>
                <div className="text-xs text-right text-gray-600">
                    <div>Dicetak pada: {new Date(data.printedDate).toLocaleString('id-ID')}</div>
                    <div>Oleh: {data.printedBy}</div>
                </div>
            </div>

            {/* Items Table */}
            <div className="mb-6">
                <table className="w-full text-[10px] border-collapse border border-gray-400">
                    <thead>
                        <tr className="bg-gray-100">
                            <th className="border border-gray-400 px-1 py-1 text-center w-8">No</th>
                            <th className="border border-gray-400 px-1 py-1 text-left">No Invoice</th>
                            <th className="border border-gray-400 px-1 py-1 text-left">No RM</th>
                            <th className="border border-gray-400 px-1 py-1 text-left">Pasien</th>
                            {/* <th className="border border-gray-400 px-1 py-1 text-left">Poliklinik</th> */}
                            <th className="border border-gray-400 px-1 py-1 text-left">Layanan</th>
                            <th className="border border-gray-400 px-1 py-1 text-center">Tipe</th>
                            <th className="border border-gray-400 px-1 py-1 text-right">Jasa Dokter</th>
                        </tr>
                    </thead>
                    <tbody>
                        {data.items.map((item, index) => (
                            <tr key={index}>
                                <td className="border border-gray-400 px-1 py-0.5 text-center">{index + 1}</td>
                                <td className="border border-gray-400 px-1 py-0.5">{item.invoiceNo}</td>
                                <td className="border border-gray-400 px-1 py-0.5">{item.medicalRecordNo}</td>
                                <td className="border border-gray-400 px-1 py-0.5">{item.patientName}</td>
                                {/* <td className="border border-gray-400 px-1 py-0.5">{item.polyclinic}</td> */}
                                <td className="border border-gray-400 px-1 py-0.5">{item.serviceName}</td>
                                <td className="border border-gray-400 px-1 py-0.5 text-center">{item.paymentType}</td>
                                <td className="border border-gray-400 px-1 py-0.5 text-right">{formatCurrency(item.doctorShare)}</td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>

            {/* Totals */}
            <div className="flex justify-end mb-12">
                <div className="w-1/2">
                    <div className="flex justify-between py-1 border-b border-dotted border-gray-400">
                        <span className="font-semibold">Sub Total</span>
                        <span className="font-mono">{formatCurrency(data.subTotal)}</span>
                    </div>
                    <div className="flex justify-between py-1 border-b border-dotted border-gray-400 text-red-600">
                        <span>PPH 21 ({data.taxRate}%)</span>
                        <span className="font-mono">({formatCurrency(data.taxAmount)})</span>
                    </div>
                    <div className="flex justify-between py-1 border-b border-dotted border-gray-400">
                        <span className="font-semibold">Total Setelah Pajak</span>
                        <span className="font-mono">{formatCurrency(data.afterTaxAmount)}</span>
                    </div>
                    {/* <div className="flex justify-between py-1 border-b border-dotted border-gray-400 text-red-600">
                        <span>Zakat ({data.zakatRate}%)</span>
                        <span className="font-mono">({formatCurrency(data.zakatAmount)})</span>
                    </div> */}
                    <div className="flex justify-between py-2 border-b-2 border-black font-bold text-lg mt-2">
                        <span>Total Penerimaan</span>
                        <span className="font-mono">{formatCurrency(data.netAmount)}</span>
                    </div>
                </div>
            </div>

            {/* Signature Area */}
            <div className="flex justify-between text-center text-sm px-8">
                <div className="w-1/3">
                    <p className="mb-16">Penerima,</p>
                    <p className="font-semibold border-b border-black inline-block min-w-[150px]">{data.doctorName}</p>
                </div>
                <div className="w-1/3">
                    <p className="mb-16">Bagian Keuangan,</p>
                    <p className="font-semibold border-b border-black inline-block min-w-[150px]">&nbsp;</p>
                </div>
            </div>
        </div>
    );
});

SalarySlip.displayName = 'SalarySlip';
