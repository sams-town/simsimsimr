import React from 'react';
import { FormatNumberRounded } from '@/utils/formatNumber';
import { FormatDate } from '@/utils/formatDate';

export interface APListItem {
    invoiceExchangeId: number;
    supplierName: string;
    poNumber: string;
    invoiceNumber: string;
    receiptDate?: string;
    dueDate?: string;
    totalTransaction?: number;
    paidAmount?: number;
    outstandingAmount?: number;
    status?: string;
    paymentStatus?: string;
    agingDays?: number;
    agingBucket?: string;
}

interface APListPrintProps {
    data: APListItem[];
    filters: {
        supplierName?: string;
        paymentStatus?: string;
        startDate?: Date | null;
        endDate?: Date | null;
    };
    printedBy: string;
}

export const APListPrint = React.forwardRef<HTMLDivElement, APListPrintProps>(({ data, filters, printedBy }, ref) => {
    const totalTrx = data.reduce((sum, item) => sum + (item.totalTransaction || 0), 0);
    const totalPaid = data.reduce((sum, item) => sum + (item.paidAmount || 0), 0);
    const totalOutstanding = data.reduce((sum, item) => sum + (item.outstandingAmount || 0), 0);

    const periodStr = filters.startDate && filters.endDate
        ? `${FormatDate(filters.startDate.toISOString()).split(' ')[0]} - ${FormatDate(filters.endDate.toISOString()).split(' ')[0]}`
        : filters.startDate
            ? `>= ${FormatDate(filters.startDate.toISOString()).split(' ')[0]}`
            : filters.endDate
                ? `<= ${FormatDate(filters.endDate.toISOString()).split(' ')[0]}`
                : 'Semua Periode';

    return (
        <div ref={ref} className="p-8 bg-white text-black font-sans w-full text-[10px] print:p-4">
            {/* Header */}
            <div className="text-center mb-6 border-b-2 border-black pb-4">
                <h1 className="text-xl font-bold uppercase tracking-wider">DAFTAR ACCOUNT PAYABLE (HUTANG USAHA)</h1>
                <p className="text-sm text-gray-600 mt-1 uppercase">RS THB</p>
            </div>

            {/* Info */}
            <div className="mb-4 flex justify-between items-end">
                <div className="space-y-1">
                    <div className="flex gap-2">
                        <span className="font-semibold w-24">Periode JT</span>
                        <span>: {periodStr}</span>
                    </div>
                    {filters.supplierName && (
                        <div className="flex gap-2">
                            <span className="font-semibold w-24">Supplier</span>
                            <span>: {filters.supplierName}</span>
                        </div>
                    )}
                    {filters.paymentStatus && (
                        <div className="flex gap-2">
                            <span className="font-semibold w-24">Status</span>
                            <span>: {filters.paymentStatus}</span>
                        </div>
                    )}
                </div>
                <div className="text-right text-[8px] text-gray-500">
                    <div>Dicetak pada: {new Date().toLocaleString('id-ID')}</div>
                    <div>Oleh: {printedBy}</div>
                </div>
            </div>

            {/* Table */}
            <table className="w-full border-collapse border border-gray-400">
                <thead>
                    <tr className="bg-gray-100 uppercase text-[8px]">
                        <th className="border border-gray-400 px-1 py-1 text-center w-6">No</th>
                        <th className="border border-gray-400 px-1 py-1 text-left">Supplier</th>
                        <th className="border border-gray-400 px-1 py-1 text-left">No. PO</th>
                        <th className="border border-gray-400 px-1 py-1 text-left">No. Faktur</th>
                        <th className="border border-gray-400 px-1 py-1 text-center">Tgl Terima</th>
                        <th className="border border-gray-400 px-1 py-1 text-center">JT</th>
                        <th className="border border-gray-400 px-1 py-1 text-right">Total Trx</th>
                        <th className="border border-gray-400 px-1 py-1 text-right">Terbayar</th>
                        <th className="border border-gray-400 px-1 py-1 text-right">Outstanding</th>
                        <th className="border border-gray-400 px-1 py-1 text-center">Status Bayar</th>
                        <th className="border border-gray-400 px-1 py-1 text-center">Aging</th>
                    </tr>
                </thead>
                <tbody>
                    {data.map((item, index) => (
                        <tr key={item.invoiceExchangeId || index} className="even:bg-gray-50">
                            <td className="border border-gray-400 px-1 py-0.5 text-center">{index + 1}</td>
                            <td className="border border-gray-400 px-1 py-0.5 max-w-[120px] truncate">{item.supplierName}</td>
                            <td className="border border-gray-400 px-1 py-0.5">{item.poNumber || '-'}</td>
                            <td className="border border-gray-400 px-1 py-0.5">{item.invoiceNumber || '-'}</td>
                            <td className="border border-gray-400 px-1 py-0.5 text-center">{item.receiptDate ? FormatDate(item.receiptDate).split(' ')[0] : '-'}</td>
                            <td className="border border-gray-400 px-1 py-0.5 text-center font-semibold">{item.dueDate ? FormatDate(item.dueDate).split(' ')[0] : '-'}</td>
                            <td className="border border-gray-400 px-1 py-0.5 text-right whitespace-nowrap">{FormatNumberRounded(item.totalTransaction || 0)}</td>
                            <td className="border border-gray-400 px-1 py-0.5 text-right whitespace-nowrap">{FormatNumberRounded(item.paidAmount || 0)}</td>
                            <td className="border border-gray-400 px-1 py-0.5 text-right whitespace-nowrap font-semibold">{FormatNumberRounded(item.outstandingAmount || 0)}</td>
                            <td className="border border-gray-400 px-1 py-0.5 text-center text-[7px]">{item.paymentStatus}</td>
                            <td className="border border-gray-400 px-1 py-0.5 text-center whitespace-nowrap">
                                {item.agingDays && item.agingDays > 0 ? `${item.agingDays} h (${item.agingBucket})` : '-'}
                            </td>
                        </tr>
                    ))}
                </tbody>
                <tfoot>
                    <tr className="bg-gray-100 font-bold">
                        <td colSpan={6} className="border border-gray-400 px-1 py-1 text-right uppercase">TOTAL</td>
                        <td className="border border-gray-400 px-1 py-1 text-right whitespace-nowrap">{FormatNumberRounded(totalTrx)}</td>
                        <td className="border border-gray-400 px-1 py-1 text-right whitespace-nowrap">{FormatNumberRounded(totalPaid)}</td>
                        <td className="border border-gray-400 px-1 py-1 text-right whitespace-nowrap">{FormatNumberRounded(totalOutstanding)}</td>
                        <td colSpan={2} className="border border-gray-400 px-1 py-1"></td>
                    </tr>
                </tfoot>
            </table>

            {/* Footer Signatures */}
            <div className="mt-12 flex justify-between text-center px-12">
                <div className="w-1/3">
                    <p className="mb-16">Mengetahui,</p>
                    <div className="border-b border-black w-32 mx-auto"></div>
                    <p className="mt-1">Manager Keuangan</p>
                </div>
                <div className="w-1/3">
                    <p className="mb-16">Dibuat oleh,</p>
                    <div className="border-b border-black w-32 mx-auto"></div>
                    <p className="mt-1">Bagian Hutang</p>
                </div>
            </div>
        </div>
    );
});

APListPrint.displayName = 'APListPrint';
