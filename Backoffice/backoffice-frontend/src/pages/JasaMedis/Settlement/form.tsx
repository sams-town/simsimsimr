import { useState } from 'react';
import { useTranslation } from 'react-i18next';
import { useParams, useNavigate } from 'react-router-dom';
import { DataTable } from 'mantine-datatable';
import { useGetSettlementDetailQuery, useProcessSettlementMutation, useLazyGetSalarySlipQuery } from '@/store/api/medicalFee/medicalFeeApiSlice';
import { useGetOptionBankQuery } from '@/store/api/bank/bankApiSlice';
import Select from 'react-select';
import Flatpickr from 'react-flatpickr';
import 'flatpickr/dist/flatpickr.css';
import { toastMessage } from '@/utils/toastUtils';
import ConfirmationModal from '@/components/ConfirmationModal';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ArrowLeft, CreditCard, Banknote, Calendar, CheckCircle, Printer } from 'lucide-react';
import IconCalendar from '@/components/Icon/IconCalendar';
import { createPortal } from 'react-dom';
import { SalarySlip, SalarySlipData } from './SalarySlip';

interface SettlementDetailItem {
    medicalFeeId: number;
    procedureDate: string;
    procedureName: string;
    payerName: string;
    totalFee: number;
    adjustmentAmount?: number;
    doctorShare: number;
    amountAfterShare: number;
    taxAmount?: number;
    patientName?: string;
    medicalRecordNo?: string;
}

interface TaxTierDetail {
    tierName: string;
    rate: number;
    taxableAmount: number;
    taxValue: number;
}

// Base64 URL-safe decoding
const decodeBase64Url = (str: string): string => {
    try {
        let base64 = str.replace(/-/g, '+').replace(/_/g, '/');
        // Add padding if needed
        while (base64.length % 4) {
            base64 += '=';
        }
        return atob(base64);
    } catch {
        return str;
    }
};

const SettlementForm = () => {
    const { t } = useTranslation();
    const { voucherId } = useParams<{ voucherId: string }>();
    const navigate = useNavigate();
    const [selectedBank, setSelectedBank] = useState<any>(null);
    const [paymentDate, setPaymentDate] = useState<Date>(new Date());
    const [paymentRef, setPaymentRef] = useState('');
    const [showConfirmModal, setShowConfirmModal] = useState(false);
    const [bankKeyword, setBankKeyword] = useState('');

    // Decode for display purposes (API receives base64, backend decodes)
    const decodedVoucherId = voucherId ? decodeBase64Url(voucherId) : '';

    // API calls use base64 encoded voucherId - backend will decode
    const { data: detailData, isLoading } = useGetSettlementDetailQuery(voucherId);
    const { data: bankOptions, isFetching: isBankFetching } = useGetOptionBankQuery({ keyword: bankKeyword });
    const [processSettlement, { isLoading: isProcessing }] = useProcessSettlementMutation();
    const [triggerGetSalarySlip] = useLazyGetSalarySlipQuery();
    const [printData, setPrintData] = useState<SalarySlipData | null>(null);

    const handlePrintSlip = async () => {
        if (!voucherId) return;
        try {
            const result = await triggerGetSalarySlip(voucherId).unwrap();
            if (result && result.data) {
                setPrintData(result.data);
                setTimeout(() => {
                    window.print();
                }, 500);
            }
        } catch (error) {
            console.error("Failed to fetch salary slip", error);
            toastMessage(t('jasa_medis.settlement.form.toast.failed'), 'error');
        }
    };

    const records: SettlementDetailItem[] = detailData?.data?.items || detailData?.data || [];
    const totalNet = detailData?.data?.totalNet || records.reduce((sum, r) => sum + r.amountAfterShare, 0);
    const totalTax = detailData?.data?.totalTax || records.reduce((sum, r) => sum + (r.taxAmount || 0), 0);
    const totalBruto = detailData?.data?.totalAmount || (totalNet + totalTax); // Fallback logic
    const doctorShare = detailData?.data?.doctorSharePercentage || (records.length > 0 ? records[0].doctorShare : 0);
    const voucherDate = detailData?.data?.voucherDate || null;

    // Status and Payment Info
    const isPaid = detailData?.data?.status?.toLowerCase() === 'paid';
    const paymentDateInfo = detailData?.data?.paymentDate || null;
    const paymentRefInfo = detailData?.data?.paymentRef || '-';
    // CoaCode Logic: API might return coaCode or we need to look it up if we had bank list but usually Detail response should have descriptive info
    const coaCodeInfo = detailData?.data?.coaCode || '-';
    // Use coaDesc (PaymentCoaDesc) if available, otherwise fallback to coaCode
    const coaDescInfo = detailData?.data?.coaDesc || coaCodeInfo;

    const taxDetails: TaxTierDetail[] = detailData?.data?.taxDetails || [];
    const previousPeriodIncome = detailData?.data?.previousPeriodIncome || 0;

    // Helper to format date in UTC
    const formatDateUTC = (dateStr: string | null): string => {
        if (!dateStr) return '-';
        const date = new Date(dateStr);
        const day = date.getUTCDate();
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        const month = months[date.getUTCMonth()];
        const year = date.getUTCFullYear();
        return `${day} ${month} ${year}`;
    };

    const formatCurrency = (value: number) => {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
    };

    const bankSelectOptions = (bankOptions?.data || []).map((b: any) => ({
        value: b.coaCode || b.value,
        label: `${b.label}`,
        desc: b.coaCode || b.value,
    }));

    const handleOpenConfirm = () => {
        if (!selectedBank) {
            toastMessage(t('jasa_medis.settlement.form.toast.select_bank'), 'warning');
            return;
        }
        setShowConfirmModal(true);
    };

    const handleSettlement = async () => {
        try {
            const result = await processSettlement({
                paymentVoucher: voucherId,
                coaCode: selectedBank.desc || selectedBank.value,
                coaDesc: selectedBank.label || '', // Send the full label as description
                paymentDate: paymentDate.toISOString(),
                paymentRef: paymentRef,
            }).unwrap();

            toastMessage(t('jasa_medis.settlement.form.toast.success', { count: result.data?.updatedCount, total: formatCurrency(result.data?.totalPaid || 0) }), 'success');
            navigate('/jasa-medis/settlement');
        } catch (error: any) {
            toastMessage(error?.data?.message || t('jasa_medis.settlement.form.toast.failed'), 'error');
        }
    };

    return (
        <div className="flex flex-col gap-6 max-w-5xl mx-auto relative">
            {isPaid && (
                <div className="absolute inset-0 flex items-center justify-center pointer-events-none z-10 overflow-hidden">
                    <span className="text-8xl font-black text-green-600 border-[12px] border-green-600 px-10 py-4 rounded-xl transform -rotate-12 inline-block tracking-widest uppercase opacity-20 select-none">
                        {t('status.paid_watermark')}
                    </span>
                </div>
            )}
            {/* Header / Title */}
            <div className="flex items-center justify-between">
                <div>
                    <h1 className="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                        {isPaid ? t('jasa_medis.settlement.form.title_detail') : t('jasa_medis.settlement.form.title_process')}
                    </h1>
                    <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        {isPaid ? t('jasa_medis.settlement.form.subtitle_detail') : t('jasa_medis.settlement.form.subtitle_process')}
                    </p>
                </div>
                <div className="flex gap-2">
                    {isPaid && (
                        <Button
                            type="button"
                            variant="outline"
                            onClick={handlePrintSlip}
                            className="gap-2 shadow-sm bg-white dark:bg-black"
                            size="sm"
                        >
                            <Printer className="h-4 w-4" />
                            {t('jasa_medis.settlement.form.print_btn', 'Cetak Slip')}
                        </Button>
                    )}
                    <Button
                        type="button"
                        variant="outline"
                        onClick={() => navigate(-1)}
                        className="gap-2 shadow-sm bg-white dark:bg-black"
                        size="sm"
                    >
                        <ArrowLeft className="h-4 w-4" />
                        {t('jasa_medis.settlement.form.back_btn')}
                    </Button>
                </div>
            </div>

            {/* Voucher Info Header */}
            <div className="grid grid-cols-1 md:grid-cols-5 gap-4 p-4 bg-white dark:bg-[#1b2e4b] rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                <div>
                    <p className="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">{t('jasa_medis.settlement.form.voucher_info.number')}</p>
                    <div className="flex items-start flex-col">
                        <p className="text-lg font-semibold text-gray-900 dark:text-white">{decodedVoucherId}</p>
                    </div>
                </div>
                <div>
                    <p className="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">{t('jasa_medis.settlement.form.voucher_info.date')}</p>
                    <p className="text-lg font-semibold text-gray-900 dark:text-white">{formatDateUTC(voucherDate)}</p>
                </div>
                <div>
                    <p className="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">{t('jasa_medis.settlement.form.voucher_info.share')}</p>
                    <p className="text-lg font-semibold text-gray-900 dark:text-white">{doctorShare}%</p>
                </div>
                <div>
                    <p className="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">{t('jasa_medis.settlement.form.voucher_info.total_pay')}</p>
                    <p className="text-lg font-semibold text-primary">{formatCurrency(totalNet)}</p>
                </div>
                <div>
                    <p className="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">{t('jasa_medis.settlement.form.voucher_info.status')}</p>
                    <span className={`px-2 py-0.5 rounded text-sm font-semibold capitalize ${isPaid ? 'bg-success/10 text-success' : 'bg-warning/10 text-warning'}`}>
                        {detailData?.data?.status || 'Unknown'}
                    </span>
                </div>
            </div>

            {/* Detail Items Card */}
            <Card className="shadow-sm border border-gray-200 dark:border-gray-800">
                <CardHeader className="bg-gray-50/50 dark:bg-[#1b2e4b] border-b border-gray-100 dark:border-gray-700 px-6 py-4">
                    <CardTitle className="text-base font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                        <span className="bg-primary/10 text-primary w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold">1</span>
                        {t('jasa_medis.settlement.form.details_title')}
                    </CardTitle>
                </CardHeader>
                <CardContent className="p-0">
                    <DataTable
                        highlightOnHover
                        className="table-hover"
                        records={records}
                        columns={[
                            {
                                accessor: 'procedureDate',
                                title: t('jasa_medis.settlement.form.table.date'),
                                render: (row: SettlementDetailItem) => formatDateUTC(row.procedureDate)
                            },
                            {
                                accessor: 'procedureName',
                                title: t('jasa_medis.settlement.form.table.procedure'),
                                render: (row: SettlementDetailItem) => (
                                    <div className="whitespace-normal" style={{ maxWidth: '200px' }}>
                                        {row.procedureName || '-'}
                                    </div>
                                )
                            },
                            { accessor: 'medicalRecordNo', title: t('jasa_medis.posting.table.mr_no', 'No RM') },
                            { accessor: 'patientName', title: t('jasa_medis.posting.table.patient_name', 'Nama Pasien') },
                            { accessor: 'payerName', title: t('jasa_medis.settlement.form.table.payment_method') },
                            {
                                accessor: 'totalFee',
                                title: t('jasa_medis.settlement.form.table.total_fee'),
                                textAlignment: 'right',
                                render: (row: SettlementDetailItem) => {
                                    const effective = row.adjustmentAmount != null && row.adjustmentAmount !== 0 && row.adjustmentAmount !== row.totalFee
                                        ? row.adjustmentAmount : row.totalFee;
                                    return (
                                        <div className="text-right font-medium">
                                            <div>{formatCurrency(effective)}</div>
                                            {row.adjustmentAmount != null && row.adjustmentAmount !== 0 && row.adjustmentAmount !== row.totalFee && (
                                                <div className="text-xs text-gray-400 line-through">{formatCurrency(row.totalFee)}</div>
                                            )}
                                        </div>
                                    );
                                }
                            },
                            {
                                accessor: 'amountAfterShare',
                                title: t('jasa_medis.settlement.form.table.pay_amount'),
                                textAlignment: 'right',
                                render: (row: SettlementDetailItem) => (
                                    <div className="text-right font-medium text-primary">{formatCurrency(row.amountAfterShare)}</div>
                                )
                            },
                        ]}
                        fetching={isLoading}
                        minHeight={200}
                        noRecordsText="Tidak ada data"
                    />
                </CardContent>
            </Card>

            {/* Payment Form - Show only if NOT PAID */}
            {!isPaid && (
                <Card className="shadow-sm border border-gray-200 dark:border-gray-800">
                    <CardHeader className="bg-gray-50/50 dark:bg-[#1b2e4b] border-b border-gray-100 dark:border-gray-700 px-6 py-4">
                        <CardTitle className="text-base font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                            <span className="bg-primary/10 text-primary w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold">2</span>
                            {t('jasa_medis.settlement.form.payment_info_title')}
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="p-6">
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div className="space-y-2">
                                <Label htmlFor="bank">{t('jasa_medis.settlement.form.payment_form.bank_label')}</Label>
                                <Select
                                    id="bank"
                                    options={bankSelectOptions}
                                    value={selectedBank}
                                    onChange={setSelectedBank}
                                    onInputChange={(value) => setBankKeyword(value)}
                                    isLoading={isBankFetching}
                                    placeholder={t('jasa_medis.settlement.form.payment_form.bank_placeholder')}
                                    className="text-sm"
                                    menuPortalTarget={document.body}
                                    styles={{
                                        menuPortal: (base) => ({ ...base, zIndex: 9999 }),
                                        control: (base) => ({ ...base, minHeight: '42px' })
                                    }}
                                />
                            </div>

                            <div className="space-y-2">
                                <Label>{t('jasa_medis.settlement.form.payment_form.date_label')}</Label>
                                <div className="relative">
                                    <Flatpickr
                                        value={paymentDate}
                                        options={{ dateFormat: 'd/m/Y', enableTime: false }}
                                        className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                        onChange={(date) => setPaymentDate(date[0])}
                                    />
                                    <div className="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-400">
                                        <IconCalendar className="w-4 h-4" />
                                    </div>
                                </div>
                            </div>

                            <div className="space-y-2 md:col-span-2">
                                <Label htmlFor="ref">{t('jasa_medis.settlement.form.payment_form.ref_label')}</Label>
                                <Input
                                    id="ref"
                                    placeholder={t('jasa_medis.settlement.form.payment_form.ref_placeholder')}
                                    value={paymentRef}
                                    onChange={(e) => setPaymentRef(e.target.value)}
                                />
                            </div>
                        </div>

                        {/* Summary */}
                        <div className="mt-6 p-4 bg-green-50 dark:bg-green-900/20 rounded-md border border-green-100 dark:border-green-800">
                            <div className="space-y-3">
                                <div className="flex items-center justify-between text-sm">
                                    <span className="text-gray-600 dark:text-gray-400">Total Jasa Medis (Bruto)</span>
                                    <span className="font-medium text-gray-900 dark:text-gray-200">{formatCurrency(totalBruto)}</span>
                                </div>
                                <div className="flex items-center justify-between text-sm">
                                    <span className="text-gray-600 dark:text-gray-400">Total PPh21</span>
                                    <span className="font-medium text-red-500">{formatCurrency(totalTax)}</span>
                                </div>
                                {detailData?.data && !detailData.data.isPPh21 && (
                                    <div className="flex justify-end">
                                        <span className="px-2 py-0.5 rounded text-xs font-semibold border bg-orange-100 text-orange-800 border-orange-200 dark:bg-orange-900/30 dark:text-orange-400 dark:border-orange-800">
                                            {t('jasa_medis.badge.pph21_inactive')}
                                        </span>
                                    </div>
                                )}

                                {/* PPh21 Tier Breakdown - Dynamic */}
                                {detailData?.data?.isPPh21 && taxDetails.length > 0 && (
                                    <div className="ml-4 pl-4 border-l-2 border-red-100 dark:border-red-900/30 space-y-2 py-1">
                                        <p className="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Rincian Per Tier</p>
                                        {taxDetails.map((tier, idx) => (
                                            <div key={idx} className="flex justify-between items-center text-xs">
                                                <span className="text-gray-500 dark:text-gray-400 italic">
                                                    {tier.tierName} ({formatCurrency(tier.taxableAmount)})
                                                </span>
                                                <span className="font-mono text-gray-600 dark:text-gray-300">

                                                </span>
                                            </div>
                                        ))}
                                    </div>
                                )}

                                <div className="pt-3 border-t border-green-200 dark:border-green-800 flex items-center justify-between">
                                    <div className="flex items-center gap-2">
                                        <Banknote className="h-5 w-5 text-green-600" />
                                        <p className="font-bold text-gray-800 dark:text-gray-200">{t('jasa_medis.settlement.form.payment_form.total_label')}</p>
                                    </div>
                                    <p className="font-black text-xl text-green-600 dark:text-green-400">{formatCurrency(totalNet)}</p>
                                </div>
                            </div>
                        </div>

                        <div className="mt-8 flex justify-end">
                            <Button
                                onClick={handleOpenConfirm}
                                disabled={isProcessing}
                                className="bg-success hover:bg-success/90 min-w-[140px]"
                            >
                                <CreditCard className="mr-2 h-4 w-4" />
                                {isProcessing ? t('jasa_medis.settlement.form.payment_form.processing') : t('jasa_medis.settlement.form.payment_form.process_btn')}
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            )}

            {/* Payment Info ReadOnly - Show if PAID */}
            {isPaid && (
                <Card className="shadow-sm border border-gray-200 dark:border-gray-800">
                    <CardHeader className="bg-gray-50/50 dark:bg-[#1b2e4b] border-b border-gray-100 dark:border-gray-700 px-6 py-4">
                        <CardTitle className="text-base font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                            <span className="bg-success/10 text-success w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold">✓</span>
                            {t('jasa_medis.settlement.form.payment_info_paid_title')}
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="p-6">
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div className="p-4 bg-gray-50 rounded-lg border border-gray-100">
                                <p className="text-sm text-gray-500 mb-1 font-medium">{t('jasa_medis.settlement.form.payment_readonly.bank_label')}</p>
                                <p className="font-semibold text-gray-900 text-lg">
                                    {coaCodeInfo !== '-' ? `[${coaCodeInfo}] ` : ''}{coaDescInfo}
                                </p>
                            </div>
                            <div className="p-4 bg-gray-50 rounded-lg border border-gray-100">
                                <p className="text-sm text-gray-500 mb-1 font-medium">{t('jasa_medis.settlement.form.payment_readonly.date_label')}</p>
                                <p className="font-semibold text-gray-900 text-lg">{formatDateUTC(paymentDateInfo)}</p>
                            </div>
                            <div className="p-4 bg-gray-50 rounded-lg border border-gray-100">
                                <p className="text-sm text-gray-500 mb-1 font-medium">{t('jasa_medis.settlement.form.payment_readonly.ref_label')}</p>
                                <p className="font-semibold text-gray-900 text-lg">{paymentRefInfo}</p>
                            </div>
                        </div>

                        <div className="mt-6 pt-6 border-t border-gray-100 dark:border-gray-700">
                            <h4 className="text-sm font-semibold text-gray-900 dark:text-white mb-4">Rincian Pembayaran</h4>
                            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div className="flex justify-between md:block">
                                    <span className="text-sm text-gray-500 block mb-1">Total Jasa Medis</span>
                                    <span className="font-medium text-lg">{formatCurrency(totalBruto)}</span>
                                </div>
                                <div className="flex justify-between md:block">
                                    <span className="text-sm text-gray-500 block mb-1">Total Pajak (PPh21)</span>
                                    <div className="flex items-center gap-2">
                                        <span className="font-medium text-lg text-red-500">{formatCurrency(totalTax)}</span>
                                        {detailData?.data && !detailData.data.isPPh21 && (
                                            <span className="px-2 py-0.5 rounded text-xs font-semibold border bg-orange-100 text-orange-800 border-orange-200 dark:bg-orange-900/30 dark:text-orange-400 dark:border-orange-800">
                                                {t('jasa_medis.badge.pph21_inactive')}
                                            </span>
                                        )}
                                    </div>

                                    {/* PPh21 Tier Breakdown - ReadOnly */}
                                    {detailData?.data?.isPPh21 && taxDetails.length > 0 && (
                                        <div className="mt-2 ml-4 pl-4 border-l-2 border-red-100 dark:border-red-900/30 space-y-2 py-1">
                                            <p className="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Rincian Per Tier</p>
                                            {taxDetails.map((tier, idx) => (
                                                <div key={idx} className="flex justify-between items-center text-xs">
                                                    <span className="text-gray-500 dark:text-gray-400 italic">
                                                        {tier.tierName} ({formatCurrency(tier.taxableAmount)})
                                                    </span>
                                                    <span className="font-mono text-gray-600 dark:text-gray-300">

                                                    </span>
                                                </div>
                                            ))}
                                        </div>
                                    )}
                                </div>
                                <div className="flex justify-between md:block">
                                    <span className="text-sm text-gray-500 block mb-1">Total Dibayarkan</span>
                                    <span className="font-bold text-lg text-green-600">{formatCurrency(totalNet)}</span>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            )}

            <ConfirmationModal
                isOpen={showConfirmModal}
                onClose={() => setShowConfirmModal(false)}
                onConfirm={handleSettlement}
                title={t('jasa_medis.settlement.form.modal.title')}
                message={t('jasa_medis.settlement.form.modal.message', { voucher: decodedVoucherId, total: formatCurrency(totalNet) })}
                confirmText={t('jasa_medis.settlement.form.modal.yes')}
                cancelText={t('jasa_medis.settlement.form.modal.cancel')}
                loading={isProcessing}
                variant="success"
            />

            {/* Print Portal */}
            {printData && createPortal(
                <div id="printable-slip-container">
                    <style>
                        {`
                        @media print {
                            body > *:not(#printable-slip-container) { display: none !important; }
                            #printable-slip-container { display: block !important; position: absolute; left: 0; top: 0; width: 100%; height: 100%; background: white; z-index: 9999; }
                            @page { size: auto; margin: 0; }
                        }
                        @media screen {
                            #printable-slip-container { display: none; }
                        }
                    `}
                    </style>
                    <SalarySlip data={printData} />
                </div>,
                document.body
            )}
        </div>
    );
};

export default SettlementForm;
