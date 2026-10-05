import { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { DataTable } from 'mantine-datatable';
import { ArrowLeft, Plus, Printer, Trash2, Layers, StickyNote } from 'lucide-react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import ConfirmationModal from '@/components/ConfirmationModal';
import { toastMessage } from '@/utils/toastUtils';
import { FormatNumber } from '@/utils/formatNumber';
import { FormatDate } from '@/utils/formatDate';
import { useGetAccountReceivablePrintConfigQuery } from '@/store/api/accountReceivable/accountReceivableApiSlice';
import AddPatientModal from './AddPatientModal';
import {
    useGetCombinedVoucherDetailQuery,
    useAddCombinedVoucherItemsMutation,
    useRemoveCombinedVoucherItemsMutation,
} from '@/store/api/accountReceivable/combinedVoucherApiSlice';

interface CombinedItem {
    arId: number;
    arNumber?: string;
    patientName?: string;
    medicalRecordNo?: string;
    sepNo?: string;
    payerId?: string;
    payerName?: string;
    payerType?: string;
    serviceType?: string;
    voucherNumber?: string;
    voucherDate?: string;
    transactionDate?: string;
    dueDate?: string;
    totalAmount: number;
    adjustmentAmount?: number;
    effectiveAmount: number;
    notes?: string;
    noteHistory?: string;
}

const parseNoteHistory = (historyJson?: string): any[] => {
    if (!historyJson) return [];
    try {
        const parsed = JSON.parse(historyJson);
        return Array.isArray(parsed) ? parsed : [];
    } catch {
        return [];
    }
};

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

const CombinedVoucherDetail = () => {
    const { id } = useParams<{ id: string }>();
    const navigate = useNavigate();

    const [showAddModal, setShowAddModal] = useState(false);
    const [showRemoveModal, setShowRemoveModal] = useState(false);
    const [removeTarget, setRemoveTarget] = useState<CombinedItem | null>(null);

    const { data, isLoading } = useGetCombinedVoucherDetailQuery(id, { skip: !id });
    const [addItems, { isLoading: isAdding }] = useAddCombinedVoucherItemsMutation();
    const [removeItems, { isLoading: isRemoving }] = useRemoveCombinedVoucherItemsMutation();

    const { data: printConfigData } = useGetAccountReceivablePrintConfigQuery(undefined);
    const printConfig = {
        leftName: printConfigData?.data?.PRINT_SIGNER_LEFT_NAME || 'Naurah Nazhifah Salim',
        leftTitle: printConfigData?.data?.PRINT_SIGNER_LEFT_TITLE || 'Bagian Penagihan',
        rightName: printConfigData?.data?.PRINT_SIGNER_RIGHT_NAME || 'Herlan Mars, S.Ak',
        rightTitle: printConfigData?.data?.PRINT_SIGNER_RIGHT_TITLE || 'Manajer Keu & Akuntansi',
    };

    const batch = data?.data;
    const items: CombinedItem[] = batch?.items || batch?.Items || [];
    const totalAmount = items.reduce((sum, r) => sum + (r.effectiveAmount || 0), 0);

    const formatCurrency = (value: number) =>
        new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value || 0);

    const handleAdd = async (arIds: number[]) => {
        if (!id) return;
        try {
            await addItems({ id, arIds }).unwrap();
            toastMessage('Pasien ditambahkan', 'success');
            setShowAddModal(false);
        } catch (error: any) {
            toastMessage(error?.data?.message || 'Gagal menambahkan pasien', 'error');
        }
    };

    const handleRemove = async () => {
        if (!id || !removeTarget) return;
        try {
            await removeItems({ id, arIds: [removeTarget.arId] }).unwrap();
            toastMessage('Pasien dihapus dari batch', 'success');
            setShowRemoveModal(false);
            setRemoveTarget(null);
        } catch (error: any) {
            toastMessage(error?.data?.message || 'Gagal menghapus pasien', 'error');
        }
    };

    return (
        <>
            <div className="flex flex-col gap-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 print:hidden">
                {/* Header */}
                <div className="flex items-center justify-between mt-4">
                    <div className="flex items-center gap-3">
                        <div className="p-2 rounded-lg bg-primary/10">
                            <Layers className="h-5 w-5 text-primary" />
                        </div>
                        <div>
                            <h1 className="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                                {batch?.batchNumber || 'Combined Voucher'}
                            </h1>
                            <p className="text-sm text-gray-500 mt-1">Detail combined / gabungan AR payment voucher</p>
                        </div>
                    </div>
                    <div className="flex gap-2">
                        <Button variant="outline" onClick={() => navigate(-1)} size="sm" className="gap-2">
                            <ArrowLeft className="h-4 w-4" /> Kembali
                        </Button>
                        <Button variant="outline" onClick={() => window.print()} size="sm" className="gap-2">
                            <Printer className="h-4 w-4" /> Cetak
                        </Button>
                        <Button onClick={() => setShowAddModal(true)} size="sm" className="gap-2">
                            <Plus className="h-4 w-4" /> Tambah Pasien
                        </Button>
                    </div>
                </div>

                {/* Info */}
                <div className="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-[#1b2e4b] shadow-sm overflow-hidden">
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 divide-y md:divide-y-0 md:divide-x border-gray-200 dark:border-gray-700">
                        <div className="p-5">
                            <p className="text-xs text-gray-500 uppercase tracking-wide font-medium mb-1.5">No Batch</p>
                            <p className="text-lg font-bold text-primary font-mono">{batch?.batchNumber || '-'}</p>
                        </div>
                        <div className="p-5">
                            <p className="text-xs text-gray-500 uppercase tracking-wide font-medium mb-1.5">Tanggal</p>
                            <p className="text-lg font-bold text-gray-900 dark:text-white">{batch?.batchDate ? FormatDate(batch.batchDate) : '-'}</p>
                        </div>
                        <div className="p-5">
                            <p className="text-xs text-gray-500 uppercase tracking-wide font-medium mb-1.5">Jumlah Pasien</p>
                            <p className="text-lg font-bold text-gray-900 dark:text-white">{items.length} pasien · {batch?.payerCount ?? 0} payer</p>
                        </div>
                        <div className="p-5 bg-primary/5 dark:bg-primary/10">
                            <p className="text-xs text-gray-500 uppercase tracking-wide font-medium mb-1.5">Total</p>
                            <p className="text-xl font-bold text-primary">{formatCurrency(totalAmount)}</p>
                        </div>
                    </div>
                </div>

                {/* Items */}
                <Card className="shadow-sm border border-gray-200 dark:border-gray-800">
                    <CardHeader className="bg-gray-50/50 dark:bg-[#1b2e4b] border-b border-gray-100 dark:border-gray-700 px-6 py-4">
                        <CardTitle className="text-base font-semibold flex items-center gap-2">Daftar Pasien</CardTitle>
                    </CardHeader>
                    <CardContent className="p-0">
                        <DataTable
                            highlightOnHover
                            className="table-hover whitespace-nowrap"
                            records={items}
                            idAccessor="arId"
                            columns={[
                                { accessor: 'no', title: 'No.', width: 50, textAlignment: 'center', render: (_r: CombinedItem, i: number) => i + 1 },
                                { accessor: 'voucherNumber', title: 'No Voucher', render: (r: CombinedItem) => <span className="font-mono text-xs">{r.voucherNumber || '-'}</span> },
                                { accessor: 'sepNo', title: 'No SEP', render: (r: CombinedItem) => <span className="font-mono text-xs">{r.sepNo || '-'}</span> },
                                {
                                    accessor: 'patientName', title: 'Pasien', render: (r: CombinedItem) => (
                                        <div className="flex flex-col">
                                            <span className="font-medium text-gray-900">{r.patientName || '-'}</span>
                                            <span className="text-xs text-gray-500">RM: {r.medicalRecordNo || '-'}</span>
                                        </div>
                                    )
                                },
                                { accessor: 'payerName', title: 'Payer', render: (r: CombinedItem) => <span className="text-xs">{r.payerName || '-'}</span> },
                                {
                                    accessor: 'serviceType', title: 'Layanan', render: (r: CombinedItem) => (
                                        <span className="text-xs uppercase">
                                            {r.serviceType?.toLowerCase() === 'rajal' || r.serviceType?.toLowerCase() === 'ralan' ? 'Rawat Jalan' :
                                                r.serviceType?.toLowerCase() === 'ranap' ? 'Rawat Inap' : (r.serviceType || '-')}
                                        </span>
                                    )
                                },
                                { accessor: 'effectiveAmount', title: 'Jumlah', textAlignment: 'right', render: (r: CombinedItem) => <span className="font-mono font-medium">{FormatNumber(r.effectiveAmount)}</span> },
                                {
                                    accessor: 'notes', title: 'Catatan', width: 200, render: (r: CombinedItem) => {
                                        const history = parseNoteHistory(r.noteHistory);
                                        if (!r.notes && history.length === 0) return <span className="text-xs text-gray-300">-</span>;
                                        return (
                                            <div className="flex items-start gap-1.5" title={r.notes || ''}>
                                                <StickyNote className="w-3.5 h-3.5 text-amber-500 mt-0.5 shrink-0" />
                                                <div className="flex flex-col">
                                                    <span className="text-xs text-gray-700 whitespace-normal leading-tight line-clamp-2">{r.notes || '-'}</span>
                                                    {history.length > 1 && <span className="text-[10px] text-gray-400">+{history.length - 1} catatan lain</span>}
                                                </div>
                                            </div>
                                        );
                                    }
                                },
                                {
                                    accessor: 'action', title: 'Aksi', textAlignment: 'center', render: (r: CombinedItem) => (
                                        <button
                                            type="button"
                                            onClick={() => { setRemoveTarget(r); setShowRemoveModal(true); }}
                                            className="text-red-500 hover:text-red-700 p-1.5 rounded-md hover:bg-red-50 transition-colors"
                                            title="Hapus dari batch"
                                        >
                                            <Trash2 className="w-4 h-4" />
                                        </button>
                                    )
                                },
                            ]}
                            fetching={isLoading}
                            minHeight={200}
                            noRecordsText="Belum ada pasien. Klik 'Tambah Pasien'."
                        />
                    </CardContent>
                </Card>
            </div>

            {/* Print layout */}
            <div className="hidden print:block font-sans text-black bg-white p-4 absolute top-0 left-0 w-full min-h-screen z-[9999]">
                <div className="flex items-start gap-4 mb-4 border-b-2 border-black pb-2">
                    <img src="/assets/images/logo-thb-transparent.png" alt="Logo" className="w-16 h-16 object-contain" />
                    <div className="flex-1 text-center pr-16">
                        <h1 className="text-xl font-bold">RS Taman Harapan Baru</h1>
                        <p className="text-xs">Jl. Kaliabang Tengah No.2, RT 004/023 Pejuang, Medan Satria, Bekasi, Jawa Barat</p>
                        <p className="text-xs">021-8898 1055</p>
                        <p className="text-xs">E-mail : rs_thb@yahoo.com</p>
                    </div>
                </div>

                <div className="text-center mb-6">
                    <h2 className="text-lg font-bold border-b border-black inline-block px-4">INVOICE GABUNGAN</h2>
                </div>

                <div className="flex justify-between mb-4 text-xs">
                    <div className="space-y-1">
                        <div className="grid grid-cols-[90px_1fr]"><span>No. Batch</span><span className="font-bold">: {batch?.batchNumber || '-'}</span></div>
                        <div className="grid grid-cols-[90px_1fr]"><span>Jumlah Pasien</span><span>: {items.length}</span></div>
                    </div>
                    <div className="space-y-1 w-64">
                        <div className="grid grid-cols-[100px_1fr]"><span>Tanggal</span><span>: {batch?.batchDate ? FormatDate(batch.batchDate) : FormatDate(new Date().toISOString())}</span></div>
                    </div>
                </div>

                <table className="w-full border-collapse mb-4 table-fixed border border-black [&_th]:!text-xs [&_td]:!text-xs">
                    <thead>
                        <tr className="border-b border-black bg-gray-50">
                            <th className="py-1 px-2 text-left border-x border-black w-[5%]">No.</th>
                            <th className="py-1 px-2 text-left border-x border-black w-[13%]">No.Rawat</th>
                            <th className="py-1 px-2 text-left border-x border-black w-[22%]">Nama Pasien</th>
                            <th className="py-1 px-2 text-left border-x border-black w-[22%]">Payer</th>
                            <th className="py-1 px-2 text-left border-x border-black w-[18%]">No.Peserta</th>
                            <th className="py-1 px-2 text-right border-x border-black w-[15%]">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        {items.map((item, idx) => (
                            <tr key={item.arId || idx} className="border-b border-black">
                                <td className="py-1 px-2 border-x border-black text-center">{idx + 1}</td>
                                <td className="py-1 px-2 border-x border-black truncate">{item.medicalRecordNo || '-'}</td>
                                <td className="py-1 px-2 border-x border-black font-medium break-words whitespace-normal">{item.patientName || '-'}</td>
                                <td className="py-1 px-2 border-x border-black break-words whitespace-normal">{item.payerName || '-'}</td>
                                <td className="py-1 px-2 border-x border-black truncate">{item.sepNo || '-'}</td>
                                <td className="py-1 px-2 border-x border-black text-right font-mono">{FormatNumber(item.effectiveAmount)}</td>
                            </tr>
                        ))}
                    </tbody>
                </table>

                <div className="flex justify-between items-start text-[10px] mb-8">
                    <div className="flex-1 italic">
                        <span>Terbilang : </span>
                        <span className="font-medium lowercase first-letter:uppercase">{terbilang(Math.round(totalAmount))} rupiah</span>
                    </div>
                    <div className="w-64 border-t border-black pt-1">
                        <div className="flex justify-between font-bold text-xs">
                            <span>Total :</span>
                            <span>{FormatNumber(totalAmount)}</span>
                        </div>
                    </div>
                </div>

                <div className="grid grid-cols-2 gap-4 text-[10px] mt-auto">
                    <div className="flex flex-col">
                        <p className="font-bold mb-1">Transfer :</p>
                        <div className="flex gap-8">
                            <div>
                                <p>Bank Syariah Indonesia</p>
                                <p>PT. Permata Bunda Abadi</p>
                                <p>No.Rek. 6666665005</p>
                            </div>
                            <div>
                                <p>Bank Mandiri</p>
                                <p>PT Permata Bunda Abadi</p>
                                <p>No. Rek. 15600770077-08</p>
                            </div>
                        </div>
                        <div className="mt-4 flex flex-col items-center w-fit">
                            <p className="mb-1 w-full text-left">Dengan Hormat,</p>
                            <img src={`https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=Signed by ${encodeURIComponent(printConfig.leftName)}`} alt="QR Signature" className="w-16 h-16 mb-2 border p-0.5" />
                            <p className="font-bold underline">{printConfig.leftName}</p>
                            <p>{printConfig.leftTitle}</p>
                        </div>
                    </div>
                    <div className="flex flex-col items-end justify-end">
                        <div className="flex flex-col items-center w-fit">
                            <p className="mb-1 w-full text-left">Kepala Bagian Keuangan</p>
                            <img src={`https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=Approved by ${encodeURIComponent(printConfig.rightName)}`} alt="QR Approval" className="w-16 h-16 mb-2 border p-0.5" />
                            <p className="font-bold underline">{printConfig.rightName}</p>
                            <p>{printConfig.rightTitle}</p>
                        </div>
                    </div>
                </div>
            </div>

            <AddPatientModal
                isOpen={showAddModal}
                onClose={() => setShowAddModal(false)}
                onAdd={handleAdd}
                loading={isAdding}
            />

            <ConfirmationModal
                isOpen={showRemoveModal}
                onClose={() => { setShowRemoveModal(false); setRemoveTarget(null); }}
                onConfirm={handleRemove}
                title="Hapus Pasien dari Batch"
                message={`Hapus ${removeTarget?.patientName || 'pasien ini'} dari combined voucher? Item AR-nya tidak terhapus, hanya dilepas dari batch.`}
                confirmText="Hapus"
                cancelText="Batal"
                loading={isRemoving}
                variant="danger"
            />
        </>
    );
};

export default CombinedVoucherDetail;
