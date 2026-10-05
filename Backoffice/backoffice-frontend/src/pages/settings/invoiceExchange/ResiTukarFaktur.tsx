import React from 'react';
import { FormatNumberRounded } from '@/utils/formatNumber';

export interface ResiTukarFakturItem {
    invoiceExchangeId: number;
    poNumber: string;
    invoiceNumber: string;
    receiptDate: string;
    dueDate: string;
    totalTransaction: number;
}

export interface ResiTukarFakturData {
    supplierName: string;
    batchNumber: string;
    exchangeDate: string;
    items: ResiTukarFakturItem[];
    totalAmount: number;
    printedDate: string;
    printedBy: string;
}

interface ResiTukarFakturProps {
    data: ResiTukarFakturData;
}

export const ResiTukarFaktur = React.forwardRef<HTMLDivElement, ResiTukarFakturProps>(({ data }, ref) => {
    return (
        <div ref={ref} className="p-6 bg-white text-[#003399] font-sans max-w-[210mm] mx-auto text-xs min-h-[148mm]">
            {/* Header */}
            <div className="flex justify-between items-start mb-2">
                <div className="flex items-center gap-3">
                    <img src="/assets/images/logo-thb-transparent.png" alt="Logo" className="w-14 h-14 object-contain" />
                    <div>
                        <h1 className="text-sm font-bold leading-none mb-1">RUMAH SAKIT TAMAN HARAPAN BARU</h1>
                        <p className="text-[8px] leading-tight max-w-[400px]">
                            Jl. Kaliabang Tengah No. 2 RT. 004 / RW. 023 Kel. Pejuang Kec. Medan Satria Kota Bekasi 17131<br />
                            Telp. (021) 8898 1055 Fax. (021) 8886 8558
                        </p>
                    </div>
                </div>
                <div className="text-[10px] font-medium italic">Lembar - 1 Supplier</div>
            </div>

            <div className="text-center mb-4">
                <h2 className="text-base font-bold underline tracking-wider">TANDA TERIMA DOKUMEN PENAGIHAN</h2>
            </div>

            {/* Info Grid */}
            <div className="border border-[#003399] divide-y divide-[#003399] mb-0">
                <div className="flex">
                    <div className="w-[150px] p-1 border-r border-[#003399]">Diterima dari</div>
                    <div className="flex-1 p-1 font-bold">: {data.supplierName}</div>
                </div>
                <div className="flex">
                    <div className="w-[150px] p-1 border-r border-[#003399]">Jenis Supplier</div>
                    <div className="flex-1 p-1">: - </div>
                </div>
                <div className="flex">
                    <div className="w-[150px] p-1 border-r border-[#003399]">Nilai Total</div>
                    <div className="flex-1 p-1 font-bold">: Rp. {FormatNumberRounded(data.totalAmount)}</div>
                </div>
                <div className="flex h-12">
                    <div className="w-[150px] p-1 border-r border-[#003399]">Keterangan</div>
                    <div className="flex-1 p-1">: {data.batchNumber ? `Batch ${data.batchNumber}` : '-'}</div>
                </div>
            </div>

            {/* Table Area */}
            <div className="border-x border-b border-[#003399]">
                <div className="text-center font-bold p-0.5 border-b border-[#003399] bg-[#003399]/5">Nomor Dokument</div>
                <div className="grid grid-cols-3 divide-x divide-[#003399] text-center font-medium border-b border-[#003399]">
                    <div className="p-1">Nomor Invoice / Faktur</div>
                    <div className="p-1">Nomor PO</div>
                    <div className="p-1">Total Tagihan</div>
                </div>
                <div className="min-h-[60px] divide-y divide-[#003399]/30">
                    {data.items.slice(0, 10).map((item, idx) => (
                        <div key={idx} className="grid grid-cols-3 divide-x divide-[#003399]/30 text-center">
                            <div className="p-0.5">{item.invoiceNumber || '-'}</div>
                            <div className="p-0.5">{item.poNumber || '-'}</div>
                            <div className="p-0.5">{FormatNumberRounded(item.totalTransaction)}</div>
                        </div>
                    ))}
                    {data.items.length === 0 && (
                        <div className="grid grid-cols-3 h-full divide-x divide-[#003399]">
                            <div>&nbsp;</div><div>&nbsp;</div><div>&nbsp;</div>
                        </div>
                    )}
                </div>
            </div>

            {/* Note Area */}
            <div className="mt-2 text-center text-[9px] font-bold italic">
                Tukar Faktur hanya dilakukan pada : <span className="underline">HARI KAMIS DAN SELASA</span>
            </div>

            {/* Footer Area */}
            <div className="mt-4 grid grid-cols-2 gap-20">
                <div className="flex flex-col">
                    <div className="mb-1">Bekasi, .....................................................</div>
                    <div className="mb-1">Diterima oleh :</div>
                    <div className="mt-12 border-t border-dashed border-[#003399] w-48 text-center pt-1 font-medium italic">
                        tanda tangan & nama jelas
                    </div>
                </div>
                <div className="flex flex-col items-end">
                    <div className="w-48">
                        <div className="mb-1 text-left">Diserahkan oleh :</div>
                        <div className="mt-14 border-t border-dashed border-[#003399] w-full text-center pt-1 font-medium italic">
                            tanda tangan & nama jelas
                        </div>
                    </div>
                </div>
            </div>

            <div className="mt-4 text-[7px] italic text-[#003399]/60 flex justify-between no-print">
                <span>Printed on: {new Date().toLocaleString('id-ID')} | {data.printedBy}</span>
            </div>
        </div>
    );
});

ResiTukarFaktur.displayName = 'ResiTukarFaktur';
