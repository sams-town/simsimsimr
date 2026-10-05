import { useState, useEffect, useRef } from 'react';
import { useTranslation } from 'react-i18next';
import { useParams, useNavigate, useSearchParams } from 'react-router-dom';
import { DataTable } from 'mantine-datatable';
import { useGetVoucherDetailQuery, useGenerateVoucherMutation, useCancelPostingMutation, medicalFeeApi } from '@/store/api/medicalFee/medicalFeeApiSlice';
import { toastMessage } from '@/utils/toastUtils';
import ConfirmationModal from '@/components/ConfirmationModal';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ArrowLeft, FileText, Trash2 } from 'lucide-react';

interface VoucherDetailItem {
    medicalFeeId: number;
    period: string;
    procedureDate: string;
    procedureName: string;
    payerCode: string;
    payerName: string;
    totalFee: number;
    postingDate: string;
    paymentVoucher?: string;
    patientName?: string;
    medicalRecordNo?: string;
    visitNo?: string;
    sepNo?: string;
    adjustmentAmount?: number;
}

interface TaxTierDetail {
    tierName: string;
    rate: number;
    taxableAmount: number;
    taxValue: number;
}

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

// PPh21 Calculation Helper (5 Tiers)
const calculateProgressiveTax = (dpp: number): number => {
    let tax = 0;
    let remaining = dpp;

    // Tier 1: 0 - 60M (5%)
    if (remaining > 0) {
        const taxable = Math.min(remaining, 60000000);
        tax += taxable * 0.05;
        remaining -= taxable;
    }

    // Tier 2: 60M - 250M (15%)
    if (remaining > 0) {
        const taxable = Math.min(remaining, 190000000); // 250M - 60M
        tax += taxable * 0.15;
        remaining -= taxable;
    }

    // Tier 3: 250M - 500M (25%)
    if (remaining > 0) {
        const taxable = Math.min(remaining, 250000000); // 500M - 250M
        tax += taxable * 0.25;
        remaining -= taxable;
    }

    // Tier 4: 500M - 5B (30%)
    if (remaining > 0) {
        const taxable = Math.min(remaining, 4500000000); // 5B - 500M
        tax += taxable * 0.30;
        remaining -= taxable;
    }

    // Tier 5: > 5B (35%)
    if (remaining > 0) {
        tax += remaining * 0.35;
    }

    return Math.floor(tax);
};

const calculateProgressiveTaxDetailed = (previousDpp: number, currentDpp: number): TaxTierDetail[] => {
    const tiers = [
        { name: 'Tier 1 (5%)', limit: 60000000, rate: 0.05 },
        { name: 'Tier 2 (15%)', limit: 190000000, rate: 0.15 },
        { name: 'Tier 3 (25%)', limit: 250000000, rate: 0.25 },
        { name: 'Tier 4 (30%)', limit: 4500000000, rate: 0.30 },
        { name: 'Tier 5 (35%)', limit: Infinity, rate: 0.35 }
    ];

    const result: TaxTierDetail[] = [];
    let remainingPrevious = previousDpp;
    let remainingCurrent = currentDpp;

    for (const tier of tiers) {
        if (remainingCurrent <= 0) break;

        const tierCapacity = tier.limit;
        const usedInThisTier = Math.min(remainingPrevious, tierCapacity);
        remainingPrevious -= usedInThisTier;

        const availableInThisTier = tierCapacity - usedInThisTier;

        if (availableInThisTier > 0) {
            const taxableInThisTier = Math.min(remainingCurrent, availableInThisTier);
            if (taxableInThisTier > 0) {
                result.push({
                    tierName: tier.name,
                    rate: tier.rate * 100,
                    taxableAmount: taxableInThisTier,
                    taxValue: Math.round(taxableInThisTier * tier.rate)
                });
            }
            remainingCurrent -= taxableInThisTier;
        }
    }
    return result;
};

const PaymentVoucherForm = () => {
    const { doctorCode: paramDoctorCode } = useParams<{ doctorCode: string }>();
    const navigate = useNavigate();
    const [searchParams] = useSearchParams();
    const statusParam = searchParams.get('status') || '';
    const isPaid = statusParam === 'paid';

    const [doctorSharePercent, setDoctorSharePercent] = useState<string>('');
    const [doctorShareAmount, setDoctorShareAmount] = useState<string>(''); // New state for amount
    const [showConfirmModal, setShowConfirmModal] = useState(false);
    const { t } = useTranslation();

    // Delete/Cancel Posting State
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [selectedDeleteId, setSelectedDeleteId] = useState<number | null>(null);

    // paramDoctorCode is already base64-encoded for paid status - backend will decode it
    const { data: detailData, isLoading, refetch } = useGetVoucherDetailQuery({
        doctorCode: paramDoctorCode,
        status: statusParam
    });
    const [generateVoucher, { isLoading: isGenerating }] = useGenerateVoucherMutation();
    const [cancelPosting, { isLoading: isDeleting }] = useCancelPostingMutation();

    const records: VoucherDetailItem[] = detailData?.data?.items || [];
    const totalFee = detailData?.data?.totalFee || 0;
    const apiDoctorShareAmount = detailData?.data?.doctorShareAmount || 0;
    const defaultDoctorShare = detailData?.data?.defaultDoctorShare || 0;

    // Export & Upload Hooks
    const [uploadBpjsAdjustment, { isLoading: isUploading }] = medicalFeeApi.useUploadBpjsAdjustmentMutation();
    const [exportVoucherDetail, { isLoading: isExporting }] = medicalFeeApi.useExportVoucherDetailMutation();



    const handleDownloadRincian = async () => {
        try {
            const blob = await exportVoucherDetail({
                doctorCode: paramDoctorCode, // Use param (base64) or detailData.doctorCode? API expects string. 
                // paramDoctorCode is what we used for getVoucherDetail, so reuse it.
                // status is 'paid' or empty
                status: statusParam
            }).unwrap();

            // Create download link
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `Rincian_JasaMedis_${doctorName}_${new Date().toISOString().split('T')[0]}.xlsx`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        } catch (error) {
            toastMessage(t('jasa_medis.toast.export_failed'), 'error');
        }
    };

    const handleUploadAdjustment = async (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('file', file);

        try {
            await uploadBpjsAdjustment(formData).unwrap();
            toastMessage(t('jasa_medis.toast.upload_success', 'Upload Berhasil'), 'success');
            refetch(); // Refresh data
        } catch (error: any) {
            toastMessage(error?.data?.message || t('jasa_medis.toast.upload_failed', 'Gagal Upload'), 'error');
        } finally {
            e.target.value = ''; // Reset input
        }
    };
    const apiDoctorSharePercentage = detailData?.data?.doctorSharePercentage || 0;
    const doctorName = detailData?.data?.doctorName || 'Dokter';
    const doctorCode = detailData?.data?.doctorCode || '-';
    // PPh21 Data
    const previousPeriodIncome = detailData?.data?.previousPeriodIncome || 0;
    const isPPh21 = detailData?.data?.isPPh21 || false;
    const apiTaxAmount = detailData?.data?.taxAmount || 0;
    const apiTaxDetails: TaxTierDetail[] = detailData?.data?.taxDetails || [];

    // Calculated State
    const [calculatedTax, setCalculatedTax] = useState(0);

    // Check if any item has payment voucher
    const hasPaymentVoucher = records.some(r => r.paymentVoucher);

    const hasPrefilled = useRef(false);

    useEffect(() => {
        if (!hasPrefilled.current && defaultDoctorShare > 0 && !hasPaymentVoucher && !isPaid && totalFee > 0) {
            setDoctorSharePercent(defaultDoctorShare.toString());
            const amt = (totalFee * defaultDoctorShare) / 100;
            setDoctorShareAmount(Math.round(amt).toString());
            hasPrefilled.current = true;
        }
    }, [defaultDoctorShare, hasPaymentVoucher, isPaid, totalFee]);

    // Derived values for display/calculation
    const numericShareAmount = hasPaymentVoucher
        ? apiDoctorShareAmount
        : (doctorShareAmount !== '' ? parseFloat(doctorShareAmount) : 0);

    const displayPercent = hasPaymentVoucher ? apiDoctorSharePercentage : (doctorSharePercent || 0);

    const formatCurrency = (value: number) => {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(value);
    };

    // PPh21 Calculation Effect
    useState(() => {
        // We use an effect-like approach, but inside render or explicit effect?
        // Let's use useEffect to avoid too many re-renders
    });

    const calculateTax = () => {
        if (!isPPh21) return 0;

        if (hasPaymentVoucher || isPaid) {
            return apiTaxAmount;
        }

        const currentIncome = numericShareAmount;
        const previousIncome = previousPeriodIncome;
        const totalAnnual = previousIncome + currentIncome;

        const dppTotal = totalAnnual * 0.5;
        const dppPrevious = previousIncome * 0.5;

        const taxTotal = calculateProgressiveTax(dppTotal);
        const taxPrevious = calculateProgressiveTax(dppPrevious);

        let taxVoucher = taxTotal - taxPrevious;
        if (taxVoucher < 0) taxVoucher = 0;

        return taxVoucher;
    };

    const taxValue = calculateTax();
    const afterTaxValue = numericShareAmount - taxValue;

    const taxDetails = (hasPaymentVoucher || isPaid)
        ? apiTaxDetails
        : (isPPh21 ? calculateProgressiveTaxDetailed(previousPeriodIncome * 0.5, numericShareAmount * 0.5) : []);

    const handleGenerate = async () => {
        const doctorCodeToUse = detailData?.data?.doctorCode || doctorCode;

        if (!doctorCodeToUse) {
            toastMessage(t('jasa_medis.payment_voucher.form.toast.invalid_doctor'), 'error');
            return;
        }

        try {
            const result = await generateVoucher({
                doctorId: detailData?.data?.doctorId || 0,
                doctorCode: doctorCodeToUse,
                doctorSharePercent: parseFloat(doctorSharePercent),
            }).unwrap();

            toastMessage(t('jasa_medis.payment_voucher.form.toast.generate_success', { voucher: result.data?.paymentVoucher, count: result.data?.updatedCount }), 'success');
            navigate('/jasa-medis/payment-voucher');
        } catch (error: any) {
            toastMessage(error?.data?.message || t('jasa_medis.payment_voucher.form.toast.generate_failed'), 'error');
        }
    };

    // ... (keep middle code, e.g. handleDelete) ...

    const handleDelete = async () => {
        if (!selectedDeleteId) return;

        try {
            await cancelPosting(selectedDeleteId).unwrap();
            toastMessage(t('jasa_medis.payment_voucher.form.toast.delete_success'), 'success');
            setShowDeleteModal(false);
            setSelectedDeleteId(null);
            // Optional: refetch is automatic via tags, but we can allow it to happen
        } catch (error: any) {
            toastMessage(error?.data?.message || t('jasa_medis.payment_voucher.form.toast.delete_failed'), 'error');
        }
    };

    return (
        <div className="flex flex-col gap-6 max-w-5xl mx-auto relative">
            {/* ... (watermark, header, doctor info, detail items card - skipped for brevity) ... */}
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
                        {isPaid ? t('jasa_medis.payment_voucher.form.title_paid') : t('jasa_medis.payment_voucher.form.title_generate')}
                    </h1>
                    <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        {isPaid ? t('jasa_medis.payment_voucher.form.subtitle_paid') : t('jasa_medis.payment_voucher.form.subtitle_generate')}
                    </p>
                </div>
                <Button
                    type="button"
                    variant="outline"
                    onClick={() => navigate(-1)}
                    className="gap-2 shadow-sm bg-white dark:bg-black"
                    size="sm"
                >
                    <ArrowLeft className="h-4 w-4" />
                    {t('jasa_medis.payment_voucher.form.back_btn')}
                </Button>
            </div>

            {/* Doctor Info Header */}
            <div className="grid grid-cols-1 md:grid-cols-4 gap-4 p-4 bg-white dark:bg-[#1b2e4b] rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                <div>
                    <p className="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">{t('jasa_medis.payment_voucher.form.doctor_info.code')}</p>
                    <p className="text-lg font-semibold text-gray-900 dark:text-white">{doctorCode}</p>
                    {records[0]?.paymentVoucher && (
                        <p className="text-xs text-primary font-mono mt-1">{records[0].paymentVoucher}</p>
                    )}
                </div>
                <div>
                    <p className="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">{t('jasa_medis.payment_voucher.form.doctor_info.name')}</p>
                    <div className="flex items-center gap-2">
                        <p className="text-lg font-semibold text-gray-900 dark:text-white">{doctorName}</p>
                    </div>
                </div>
                <div>
                    <p className="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">{t('jasa_medis.payment_voucher.form.doctor_info.item_count')}</p>
                    <p className="text-lg font-semibold text-gray-900 dark:text-white">{records.length} item</p>
                </div>
                <div>
                    <p className="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">{t('jasa_medis.payment_voucher.form.doctor_info.total_fee')}</p>
                    <p className="text-lg font-semibold text-primary">{formatCurrency(totalFee)}</p>
                </div>
            </div>

            {/* Detail Items Card */}
            <Card className="shadow-sm border border-gray-200 dark:border-gray-800">
                <CardHeader className="bg-gray-50/50 dark:bg-[#1b2e4b] border-b border-gray-100 dark:border-gray-700 px-6 py-4">
                    <CardTitle className="text-base font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                        <span className="bg-primary/10 text-primary w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold">1</span>
                        {t('jasa_medis.payment_voucher.form.details_title')}
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
                                title: t('jasa_medis.payment_voucher.form.table.date'),
                                width: 130,
                                render: (row: VoucherDetailItem) => formatDateUTC(row.procedureDate)
                            },
                            {
                                accessor: 'procedureName',
                                title: t('jasa_medis.payment_voucher.form.table.procedure'),
                                render: (row: VoucherDetailItem) => (
                                    <div className="whitespace-normal" style={{ maxWidth: '200px' }}>
                                        {row.procedureName || '-'}
                                    </div>
                                )
                            },
                            {
                                accessor: 'patientInfo',
                                title: t('jasa_medis.posting.table.patient_name', 'Pasien'),
                                width: 200,
                                render: (row: VoucherDetailItem) => (
                                    <div className="flex flex-col gap-1">
                                        <span className="font-semibold text-gray-900 dark:text-white">{row.patientName || '-'}</span>
                                        <span className="text-xs text-gray-500 bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded w-fit">
                                            RM: {row.medicalRecordNo || '-'}
                                        </span>
                                    </div>
                                )
                            },
                            {
                                accessor: 'payerName',
                                title: t('jasa_medis.payment_voucher.form.table.payment_method'),
                                width: 180,
                                render: (row: VoucherDetailItem) => (
                                    <div className="flex flex-col gap-1">
                                        <span className="text-sm">{row.payerName || '-'}</span>
                                        {row.sepNo && (
                                            <span className="text-xs bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 px-1.5 py-0.5 rounded w-fit">
                                                SEP: {row.sepNo}
                                            </span>
                                        )}
                                        {!row.sepNo && row.visitNo && (
                                            <span className="text-xs bg-gray-50 dark:bg-gray-800 text-gray-500 px-1.5 py-0.5 rounded w-fit">
                                                Visit: {row.visitNo}
                                            </span>
                                        )}
                                    </div>
                                )
                            },
                            {
                                accessor: 'postingDate',
                                title: t('jasa_medis.payment_voucher.form.table.posting_date'),
                                width: 130,
                                render: (row: VoucherDetailItem) => formatDateUTC(row.postingDate)
                            },
                            {
                                accessor: 'totalFee',
                                title: t('jasa_medis.payment_voucher.form.table.total_fee'),
                                textAlignment: 'right',
                                width: 140,
                                render: (row: VoucherDetailItem) => {
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
                                accessor: 'action',
                                title: t('jasa_medis.payment_voucher.form.table.action'),
                                width: 80,
                                textAlignment: 'center',
                                hidden: hasPaymentVoucher || isPaid,
                                render: (row: VoucherDetailItem) => (
                                    <div className="flex justify-center">
                                        <button
                                            type="button"
                                            onClick={() => {
                                                setSelectedDeleteId(row.medicalFeeId);
                                                setShowDeleteModal(true);
                                            }}
                                            className="text-red-500 hover:text-red-700 p-1 rounded-full hover:bg-red-50 transition-colors"
                                            title={t('jasa_medis.payment_voucher.form.delete_tooltip')}
                                        >
                                            <Trash2 className="w-4 h-4" />
                                        </button>
                                    </div>
                                )
                            }
                        ]}
                        fetching={isLoading}
                        minHeight={200}
                        noRecordsText={t('jasa_medis.posting.no_data')}
                    />
                </CardContent>
            </Card>

            {/* Share Calculation Card */}
            <Card className="shadow-sm border border-gray-200 dark:border-gray-800">
                <CardHeader className="bg-gray-50/50 dark:bg-[#1b2e4b] border-b border-gray-100 dark:border-gray-700 px-6 py-4">
                    <CardTitle className="text-base font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                        <span className="bg-primary/10 text-primary w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold">2</span>
                        {t('jasa_medis.payment_voucher.form.calc_title')}
                    </CardTitle>
                </CardHeader>
                <CardContent className="p-6">
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div className="space-y-2">
                            <Label htmlFor="doctorShare" className="text-gray-700 dark:text-gray-300 font-medium">
                                {t('jasa_medis.payment_voucher.form.share_percent')} <span className="text-red-500">*</span>
                            </Label>
                            {!hasPaymentVoucher ? (
                                <>
                                    <Input
                                        id="doctorShare"
                                        type="number"
                                        placeholder={t('jasa_medis.payment_voucher.form.share_placeholder')}
                                        value={doctorSharePercent}
                                        onChange={(e) => {
                                            const val = e.target.value;
                                            setDoctorSharePercent(val);

                                            // Bi-directional update for Amount
                                            if (val === '') {
                                                setDoctorShareAmount('');
                                            } else {
                                                const pct = parseFloat(val);
                                                if (!isNaN(pct) && totalFee > 0) {
                                                    const amt = (totalFee * pct) / 100;
                                                    setDoctorShareAmount(Math.round(amt).toString());
                                                }
                                            }
                                        }}
                                        min={0}
                                        max={100}
                                        className="transition-all focus:ring-primary/20 h-[58px] text-lg"
                                    />
                                    {doctorSharePercent === '' && (
                                        <p className="text-xs text-amber-600 dark:text-amber-400 flex items-center gap-1 mt-1">
                                            <span className="w-1 h-1 bg-amber-500 rounded-full inline-block"></span>
                                            {t('jasa_medis.payment_voucher.form.share_required')}
                                        </p>
                                    )}
                                </>
                            ) : (
                                <div className="p-4 bg-gray-50 dark:bg-gray-800 rounded-md border border-gray-200 dark:border-gray-700 h-[58px] flex items-center">
                                    <p className="font-semibold text-lg text-gray-900 dark:text-white">{apiDoctorSharePercentage}%</p>
                                </div>
                            )}
                        </div>
                        <div className="space-y-2">
                            <Label className="text-gray-700 dark:text-gray-300 font-medium">{t('jasa_medis.payment_voucher.form.table.total_fee')}</Label>
                            <div className="p-4 bg-gray-50 dark:bg-gray-800 rounded-md border border-gray-200 dark:border-gray-700 h-[58px] flex items-center">
                                <p className="font-semibold text-lg text-gray-900 dark:text-white">{formatCurrency(totalFee)}</p>
                            </div>
                        </div>
                        <div className="space-y-2">

                            <Label className="text-gray-700 dark:text-gray-300 font-medium">{t('jasa_medis.payment_voucher.form.doctor_revenue')} ({displayPercent}%)</Label>
                            {!hasPaymentVoucher ? (
                                <Input
                                    type="number"
                                    placeholder="0"
                                    value={doctorShareAmount}
                                    onChange={(e) => {
                                        const val = e.target.value;
                                        setDoctorShareAmount(val);

                                        // Bi-directional update for Percent
                                        if (val === '') {
                                            setDoctorSharePercent('');
                                        } else {
                                            const amount = parseFloat(val);
                                            if (!isNaN(amount) && totalFee > 0) {
                                                const percent = (amount / totalFee) * 100;
                                                const limitedPercent = Math.min(100, Math.max(0, percent));
                                                // Smart rounding: 10.0000 -> 10, 10.0001 -> 10.0001
                                                setDoctorSharePercent(parseFloat(limitedPercent.toFixed(4)).toString());
                                            }
                                        }
                                    }}
                                    className="transition-all focus:ring-primary/20 h-[58px] text-lg"
                                />
                            ) : (
                                <div className="p-4 bg-primary/5 dark:bg-primary/10 rounded-md border border-primary/20 h-[58px] flex items-center">
                                    <p className="font-bold text-xl text-primary">{formatCurrency(numericShareAmount)}</p>
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Tax Breakdown (Consolidated) */}
                    <div className="mt-6 border-t border-gray-100 dark:border-gray-700 pt-4">
                        <div className="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-4 space-y-3">
                            <div className="flex justify-between items-center text-sm">
                                <span className="text-gray-600 dark:text-gray-400">Total Pendapatan YTD (Sebelumnya)</span>
                                <span className="font-mono text-gray-900 dark:text-gray-200">{formatCurrency(previousPeriodIncome)}</span>
                            </div>
                            <div className="flex justify-between items-center text-sm">
                                <span className="text-gray-600 dark:text-gray-400">Pendapatan Bruto Voucher Ini</span>
                                <span className="font-mono font-medium text-primary">{formatCurrency(numericShareAmount)}</span>
                            </div>
                            <div className="flex justify-between items-center text-sm border-b border-gray-200 dark:border-gray-700 pb-2">
                                <span className="text-gray-600 dark:text-gray-400">DPP (50% x Total YTD + Current)</span>
                                <span className="font-mono text-gray-900 dark:text-gray-200">
                                    {formatCurrency((previousPeriodIncome + numericShareAmount) * 0.5)}
                                </span>
                            </div>

                            <div className="flex justify-between items-center bg-white dark:bg-gray-800 p-3 rounded border border-gray-200 dark:border-gray-700">
                                <span className="font-medium text-gray-700 dark:text-gray-300">PPh21 Terutang</span>
                                <span className="font-mono font-bold text-red-500">{formatCurrency(taxValue)}</span>
                            </div>

                            {/* PPh21 Tier Breakdown */}
                            {isPPh21 && taxDetails.length > 0 && (
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

                            <div className="flex justify-between items-center pt-1">
                                <span className="font-bold text-lg text-gray-800 dark:text-white">Netto Diterima Dokter</span>
                                <span className="font-mono text-xl font-black text-green-600 dark:text-green-400">
                                    {formatCurrency(afterTaxValue)}
                                </span>
                            </div>
                            {!isPPh21 && (
                                <div className="mt-2 text-right">
                                    <span className="px-2 py-0.5 rounded text-xs font-semibold border bg-orange-100 text-orange-800 border-orange-200 dark:bg-orange-900/30 dark:text-orange-400 dark:border-orange-800">
                                        {t('jasa_medis.badge.pph21_inactive')}
                                    </span>
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Terbilang */}
                    <div className="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-md border border-blue-100 dark:border-blue-800">
                        <p className="text-sm text-gray-600 dark:text-gray-400 mb-1">{t('jasa_medis.payment_voucher.form.terbilang')}:</p>
                        <p className="italic text-gray-800 dark:text-gray-200 font-medium">
                            {terbilang(Math.round(numericShareAmount)).trim() || t('jasa_medis.payment_voucher.form.zero')} {t('jasa_medis.payment_voucher.form.currency_name')}
                        </p>
                    </div>
                </CardContent>
            </Card>

            {/* Action Buttons */}
            <div className="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-800">
                <Button
                    type="button"
                    variant="ghost"
                    onClick={() => navigate(-1)}
                    className="text-gray-600 hover:text-gray-900 hover:bg-gray-100"
                >
                    {hasPaymentVoucher || isPaid ? t('jasa_medis.payment_voucher.form.back_btn') : t('jasa_medis.payment_voucher.form.modal.cancel')}
                </Button>
                {!hasPaymentVoucher && !isPaid && (
                    <>
                        <Button
                            type="button"
                            variant="outline"
                            onClick={handleDownloadRincian}
                            disabled={records.length === 0 || isExporting}
                            className="bg-white dark:bg-black gap-2"
                        >
                            {isExporting ? (
                                <div className="w-4 h-4 border-2 border-primary/30 border-t-primary rounded-full animate-spin"></div>
                            ) : (
                                <FileText className="h-4 w-4" />
                            )}
                            Download Rincian
                        </Button>
                        <div className="relative">
                            <input
                                type="file"
                                accept=".xlsx,.xls" // Only Excel
                                onChange={handleUploadAdjustment}
                                className="absolute inset-0 w-full h-full opacity-0 cursor-pointer disabled:cursor-not-allowed"
                                disabled={isUploading}
                            />
                            <Button
                                type="button"
                                variant="outline"
                                disabled={isUploading}
                                className="bg-white dark:bg-black gap-2"
                            >
                                {isUploading ? (
                                    <div className="w-4 h-4 border-2 border-primary/30 border-t-primary rounded-full animate-spin"></div>
                                ) : (
                                    <FileText className="h-4 w-4" />
                                )}
                                Upload BPJS Adj
                            </Button>
                        </div>
                    </>
                )}
                {!hasPaymentVoucher && !isPaid && (
                    <Button
                        type="button"
                        onClick={() => setShowConfirmModal(true)}
                        disabled={records.length === 0 || isGenerating || doctorSharePercent === ''}
                        className="min-w-[160px] shadow-md hover:shadow-lg transition-all gap-2"
                    >
                        {isGenerating ? (
                            <>
                                <div className="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                                {t('jasa_medis.posting.processing')}
                            </>
                        ) : (
                            <>
                                <FileText className="h-4 w-4" />
                                {t('jasa_medis.payment_voucher.form.generate_btn')}
                            </>
                        )}
                    </Button>
                )}
            </div>

            <ConfirmationModal
                isOpen={showConfirmModal}
                onClose={() => setShowConfirmModal(false)}
                onConfirm={handleGenerate}
                title={t('jasa_medis.payment_voucher.form.modal.confirm_generate_title')}
                message={t('jasa_medis.payment_voucher.form.modal.confirm_generate_message', { name: doctorName, amount: formatCurrency(numericShareAmount), percent: doctorSharePercent })}
                confirmText={t('jasa_medis.payment_voucher.form.modal.yes_generate')}
                cancelText={t('jasa_medis.payment_voucher.form.modal.cancel')}
                loading={isGenerating}
                variant="primary"
            />

            <ConfirmationModal
                isOpen={showDeleteModal}
                onClose={() => {
                    setShowDeleteModal(false);
                    setSelectedDeleteId(null);
                }}
                onConfirm={handleDelete}
                title={t('jasa_medis.payment_voucher.form.modal.confirm_delete_title')}
                message={t('jasa_medis.payment_voucher.form.modal.confirm_delete_message')}
                confirmText={t('jasa_medis.payment_voucher.form.modal.yes_delete')}
                cancelText={t('jasa_medis.payment_voucher.form.modal.cancel')}
                loading={isDeleting}
                variant="danger"
            />
        </div>
    );
};
export default PaymentVoucherForm;
