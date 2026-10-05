import { useEffect, useState } from 'react';
import { useTranslation } from 'react-i18next';
import { useDispatch, useSelector } from 'react-redux';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '@/store/themeConfigSlice';
import { DataTable } from 'mantine-datatable';
import Flatpickr from 'react-flatpickr';
import 'flatpickr/dist/flatpickr.css';
import SelectSearch from 'react-select';
import {
    useGetMedicalFeeListQuery,
    useProcessMedicalFeePaymentMutation,
    useExportMedicalFeeMutation,
} from '@/store/api/medicalFee/medicalFeeApiSlice';
import { useGetOptionBankQuery } from '@/store/api/bank/bankApiSlice';
import { FormatNumberRounded } from '@/utils/formatNumber';
import { toastMessage } from '@/utils/toastUtils';
import ConfirmationModal from '@/components/ConfirmationModal';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import IconCalendar from '@/components/Icon/IconCalendar';
import {
    User,
    Download,
    Filter,
    CreditCard,
    CheckCircle2,
    Banknote,
    Stethoscope
} from 'lucide-react';

interface MedicalFeeData {
    medicalFeeId: number;
    doctorId: number;
    doctorName: string;
    period: string;
    periodMonth: number;
    periodYear: number;
    patientType: string;
    totalFee: number;
    adjustmentAmount?: number;
    paidAmount: number;
    outstandingAmount: number;
    status: string;
    paymentDate: string;
    paymentRef: string;
}

const JasaMedis = () => {
    const dispatch = useDispatch();
    const { t } = useTranslation();
    const isRtl = useSelector((state: any) => state.themeConfig.rtlClass) === 'rtl';

    useEffect(() => {
        dispatch(setPageTitle(t('jasa_medis.payment.title')));
        dispatch(setTitle(t('jasa_medis.payment.title')));
        dispatch(setBreadcrumbTitle(['Dashboard', 'Account Payable', t('jasa_medis.payment.title')]));
    }, [dispatch]);

    // Filter states
    const [selectedPatientType, setSelectedPatientType] = useState<string>('');
    const [selectedStatus, setSelectedStatus] = useState<string>('Pending');
    const [periodMonth, setPeriodMonth] = useState<number | null>(null);
    const [periodYear, setPeriodYear] = useState<number | null>(new Date().getFullYear());

    const [isFilterApplied, setIsFilterApplied] = useState<boolean>(true);
    const [appliedPatientType, setAppliedPatientType] = useState<string>('');
    const [appliedStatus, setAppliedStatus] = useState<string>('Pending');
    const [appliedMonth, setAppliedMonth] = useState<number | null>(null);
    const [appliedYear, setAppliedYear] = useState<number | null>(new Date().getFullYear());

    // Payment form states
    const [selectedRows, setSelectedRows] = useState<Set<number>>(new Set());
    const [paymentDate, setPaymentDate] = useState<Date>(new Date());
    const [selectedBank, setSelectedBank] = useState<any>(null);
    const [searchBank, setSearchBank] = useState<string>('');
    const [paymentRef, setPaymentRef] = useState<string>('');
    const [showPaymentModal, setShowPaymentModal] = useState(false);

    // Table states
    const [page, setPage] = useState<number>(1);
    const PAGE_SIZES: number[] = [10, 20, 30, 50, 100];
    const [pageSize, setPageSize] = useState<number>(PAGE_SIZES[0]);

    // API calls
    const { data: bankList, isLoading: isLoadingBank } = useGetOptionBankQuery(
        { search: searchBank || '', page: -1, pageSize: -1 }, { skip: false }
    );

    const [processPayment, { isLoading: isProcessingPayment }] = useProcessMedicalFeePaymentMutation();
    const [exportFees, { isLoading: isExporting }] = useExportMedicalFeeMutation();

    const { data: feeList, isLoading, refetch } = useGetMedicalFeeListQuery({
        patientType: appliedPatientType,
        status: appliedStatus,
        periodMonth: appliedMonth,
        periodYear: appliedYear,
        page,
        pageSize,
        orderBy: 'doctorname',
        orderType: 'asc',
    }, { skip: !isFilterApplied });

    const getBankOptions = () => {
        if (!bankList) return [];
        let items: any[] = Array.isArray(bankList) ? bankList : bankList.data || [];
        return items.map((b: any) => ({
            label: b.label || b.bankName || '',
            value: b.value || b.coaCode || b.desc || '',
            desc: b.desc || b.coaCode || '',
        })).filter((i: any) => i.label && i.value);
    };

    const bankOptions = getBankOptions();

    const handleFilter = () => {
        setAppliedPatientType(selectedPatientType);
        setAppliedStatus(selectedStatus);
        setAppliedMonth(periodMonth);
        setAppliedYear(periodYear);
        setIsFilterApplied(true);
        setPage(1);
        setSelectedRows(new Set());
        setTimeout(() => refetch(), 0);
    };

    const handleExport = async () => {
        try {
            const blob = await exportFees({
                patientType: appliedPatientType,
                status: appliedStatus,
                periodMonth: appliedMonth,
                periodYear: appliedYear,
                type: 'xlsx',
            }).unwrap();

            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', `jasa_medis_${new Date().toISOString().split('T')[0]}.xlsx`);
            document.body.appendChild(link);
            link.click();
            link.remove();
            window.URL.revokeObjectURL(url);
        } catch (error: any) {
            toastMessage(error?.data?.message || 'Gagal export data', 'error');
        }
    };

    const records: MedicalFeeData[] = feeList?.data?.data || feeList?.data || [];
    const totalRecords = feeList?.data?.totalData || records.length;

    const selectedAmount = records
        .filter(r => selectedRows.has(r.medicalFeeId))
        .reduce((sum, r) => sum + (r.outstandingAmount || 0), 0);

    const handleRowSelect = (id: number, checked: boolean) => {
        setSelectedRows((prev) => {
            const next = new Set(prev);
            checked ? next.add(id) : next.delete(id);
            return next;
        });
    };

    const handleSelectAll = (checked: boolean) => {
        const selectableRecords = records.filter(r => r.status?.toLowerCase() !== 'paid');
        setSelectedRows(checked ? new Set(selectableRecords.map(r => r.medicalFeeId)) : new Set());
    };

    const handlePayment = async () => {
        if (selectedRows.size === 0) {
            toastMessage('Pilih minimal satu jasa medis', 'error');
            return;
        }
        if (!selectedBank) {
            toastMessage('Pilih rekening pembayaran', 'error');
            return;
        }

        try {
            const result = await processPayment({
                feeIds: Array.from(selectedRows),
                paymentDate: paymentDate.toISOString(),
                coaCode: selectedBank.desc || selectedBank.value,
                paymentRef: paymentRef,
            }).unwrap();

            toastMessage(`${result.data?.updatedCount || selectedRows.size} jasa medis berhasil dibayar`, 'success');
            setSelectedRows(new Set());
            setPaymentRef('');
            setShowPaymentModal(false);
            refetch();
        } catch (error: any) {
            toastMessage(error?.data?.message || 'Gagal memproses pembayaran', 'error');
        }
    };

    const getStatusColor = (status: string) => {
        switch (status?.toLowerCase()) {
            case 'paid': return 'bg-success/10 text-success';
            case 'partial': return 'bg-warning/10 text-warning';
            default: return 'bg-danger/10 text-danger';
        }
    };

    const months = [
        { label: 'Semua Bulan', value: null },
        { label: 'Januari', value: 1 },
        { label: 'Februari', value: 2 },
        { label: 'Maret', value: 3 },
        { label: 'April', value: 4 },
        { label: 'Mei', value: 5 },
        { label: 'Juni', value: 6 },
        { label: 'Juli', value: 7 },
        { label: 'Agustus', value: 8 },
        { label: 'September', value: 9 },
        { label: 'Oktober', value: 10 },
        { label: 'November', value: 11 },
        { label: 'Desember', value: 12 },
    ];

    const selectStyles = {
        control: (base: any) => ({
            ...base,
            minHeight: '40px',
            borderColor: '#e2e8f0',
            '&:hover': { borderColor: '#cbd5e1' },
        }),
        menuPortal: (base: any) => ({ ...base, zIndex: 9999 }),
    };

    return (
        <div className="space-y-6">
            {/* Filter Card */}
            <Card>
                <CardHeader className="border-b border-gray-100 pb-4">
                    <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div className="flex items-center gap-3">
                            <div className="p-2 rounded-lg bg-primary/10">
                                <Stethoscope className="h-5 w-5 text-primary" />
                            </div>
                            <div>
                                <CardTitle className="text-lg">{t('jasa_medis.payment.title')}</CardTitle>
                                <p className="text-sm text-gray-500 mt-0.5">
                                    {t('jasa_medis.payment.subtitle')}
                                </p>
                            </div>
                        </div>

                        <div className="flex flex-col md:flex-row items-end md:items-center gap-3 ml-auto w-full md:w-auto flex-wrap">
                            {/* Patient Type */}
                            <div className="w-[130px]">
                                <select
                                    className="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm"
                                    value={selectedPatientType}
                                    onChange={(e) => setSelectedPatientType(e.target.value)}
                                >
                                    <option value="">{t('jasa_medis.payment.filter.all_types')}</option>
                                    <option value="Tunai">Tunai</option>
                                    <option value="BPJS">BPJS</option>
                                    <option value="Asuransi">Asuransi</option>
                                </select>
                            </div>

                            {/* Month */}
                            <div className="w-[140px]">
                                <select
                                    className="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm"
                                    value={periodMonth ?? ''}
                                    onChange={(e) => setPeriodMonth(e.target.value ? Number(e.target.value) : null)}
                                >
                                    {months.map(m => <option key={m.value ?? 'all'} value={m.value ?? ''}>{m.label}</option>)}
                                </select>
                            </div>

                            {/* Year */}
                            <div className="w-[100px]">
                                <input
                                    type="number"
                                    className="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm"
                                    value={periodYear ?? ''}
                                    onChange={(e) => setPeriodYear(e.target.value ? Number(e.target.value) : null)}
                                    placeholder="Tahun"
                                />
                            </div>

                            {/* Status */}
                            <div className="w-[130px]">
                                <select
                                    className="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm"
                                    value={selectedStatus}
                                    onChange={(e) => setSelectedStatus(e.target.value)}
                                >
                                    <option value="">{t('jasa_medis.payment.filter.all_status')}</option>
                                    <option value="Pending">{t('jasa_medis.payment.filter.pending')}</option>
                                    <option value="Paid">{t('jasa_medis.payment.filter.paid')}</option>
                                </select>
                            </div>

                            <div className="flex items-center gap-2">
                                <Button variant="default" className="gap-2" onClick={handleFilter}>
                                    <Filter className="h-4 w-4" />
                                    <span className="hidden sm:inline">{t('jasa_medis.payment.filter.filter_btn')}</span>
                                </Button>
                                <Button
                                    variant="outline"
                                    className="gap-2"
                                    onClick={handleExport}
                                    disabled={isExporting || records.length === 0}
                                >
                                    <Download className="h-4 w-4" />
                                    <span className="hidden sm:inline">{isExporting ? 'Exporting...' : 'Export'}</span>
                                </Button>
                            </div>
                        </div>
                    </div>
                </CardHeader>
            </Card>

            {/* Payment Form */}
            {selectedRows.size > 0 && (
                <Card className="border-primary/20 bg-primary/5">
                    <CardHeader className="pb-4">
                        <CardTitle className="text-lg flex items-center gap-2 text-primary">
                            <CreditCard className="w-5 h-5" />
                            {t('jasa_medis.payment.payment_form.title')} ({t('jasa_medis.payment.payment_form.selected_count', { count: selectedRows.size })})
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label className="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
                                    <IconCalendar className="w-4 h-4" />{t('jasa_medis.payment.payment_form.date')}
                                </label>
                                <Flatpickr
                                    options={{ dateFormat: 'd/m/Y', position: isRtl ? 'auto right' : 'auto left' }}
                                    className="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm"
                                    value={paymentDate}
                                    onChange={(dates: Date[]) => dates[0] && setPaymentDate(dates[0])}
                                />
                            </div>
                            <div>
                                <label className="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
                                    <Banknote className="w-4 h-4" />{t('jasa_medis.payment.payment_form.bank')}
                                </label>
                                <SelectSearch
                                    placeholder={t('jasa_medis.payment.payment_form.select_bank')}
                                    options={bankOptions}
                                    isLoading={isLoadingBank}
                                    onInputChange={(v) => setSearchBank(v)}
                                    onChange={(opt: any) => setSelectedBank(opt)}
                                    value={selectedBank}
                                    styles={selectStyles}
                                    menuPortalTarget={document.body}
                                />
                            </div>
                            <div>
                                <label className="text-sm font-medium text-gray-700 mb-2 block">No. Referensi</label>
                                <input
                                    type="text"
                                    className="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm"
                                    placeholder={t('jasa_medis.payment.payment_form.ref_placeholder')}
                                    value={paymentRef}
                                    onChange={(e) => setPaymentRef(e.target.value)}
                                />
                            </div>
                            <div>
                                <label className="text-sm font-medium text-gray-700 mb-2 block">{t('jasa_medis.payment.payment_form.total_pay')}</label>
                                <div className="flex h-10 w-full items-center rounded-md border border-input bg-gray-100 px-3 py-2 text-sm font-mono font-bold text-lg">
                                    {FormatNumberRounded(selectedAmount)}
                                </div>
                            </div>
                        </div>
                        <div className="mt-4 flex justify-end">
                            <Button
                                onClick={() => setShowPaymentModal(true)}
                                disabled={!selectedBank || isProcessingPayment}
                                className="gap-2 bg-success hover:bg-success/90"
                            >
                                <CheckCircle2 className="w-4 h-4" />
                                {isProcessingPayment ? t('jasa_medis.payment.payment_form.processing') : t('jasa_medis.payment.payment_form.process_btn')}
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            )}

            {/* Data Card */}
            <Card>
                <CardContent className="pt-6">
                    <div className="datatables z-0 rounded-lg border border-gray-100 overflow-hidden">
                        <DataTable
                            highlightOnHover
                            className="whitespace-nowrap table-hover cursor-pointer"
                            records={records}
                            onRowClick={(record: MedicalFeeData) => {
                                if (record.status?.toLowerCase() !== 'paid') {
                                    handleRowSelect(record.medicalFeeId, !selectedRows.has(record.medicalFeeId));
                                }
                            }}
                            columns={[
                                ...(appliedStatus !== 'Paid' ? [{
                                    accessor: 'checkbox',
                                    title: (
                                        <input
                                            type="checkbox"
                                            className="form-checkbox"
                                            checked={records.length > 0 && records.filter(r => r.status?.toLowerCase() !== 'paid').every(r => selectedRows.has(r.medicalFeeId))}
                                            onChange={(e) => handleSelectAll(e.target.checked)}
                                            onClick={(e) => e.stopPropagation()}
                                        />
                                    ),
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (r: MedicalFeeData) => r.status?.toLowerCase() !== 'paid' ? (
                                        <input
                                            type="checkbox"
                                            className="form-checkbox"
                                            checked={selectedRows.has(r.medicalFeeId)}
                                            onChange={(e) => handleRowSelect(r.medicalFeeId, e.target.checked)}
                                            onClick={(e) => e.stopPropagation()}
                                        />
                                    ) : null,
                                    width: 50,
                                }] : []),
                                {
                                    accessor: 'doctorName',
                                    title: t('jasa_medis.payment.table.doctor'),
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (r: MedicalFeeData) => (
                                        <span className="flex items-center gap-2 font-medium text-gray-700">
                                            <User className="w-4 h-4 text-gray-400" />{r.doctorName}
                                        </span>
                                    )
                                },
                                {
                                    accessor: 'period',
                                    title: t('jasa_medis.payment.table.period'),
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (r: MedicalFeeData) => <span className="text-gray-600">{r.period}</span>
                                },
                                {
                                    accessor: 'patientType',
                                    title: t('jasa_medis.payment.table.patient_type'),
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (r: MedicalFeeData) => <span className="text-gray-600">{r.patientType}</span>
                                },
                                {
                                    accessor: 'totalFee',
                                    title: t('jasa_medis.payment.table.total_fee'),
                                    textAlignment: 'right' as const,
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50 text-right',
                                    cellsStyle: { textAlign: 'right' },
                                    render: (r: MedicalFeeData) => (
                                        <div className="text-right">
                                            <div className="font-mono text-gray-900">
                                                {FormatNumberRounded(r.adjustmentAmount != null && r.adjustmentAmount !== 0 && r.adjustmentAmount !== r.totalFee
                                                    ? r.adjustmentAmount : r.totalFee)}
                                            </div>
                                            {r.adjustmentAmount != null && r.adjustmentAmount !== 0 && r.adjustmentAmount !== r.totalFee && (
                                                <div className="font-mono text-xs text-gray-400 line-through">{FormatNumberRounded(r.totalFee)}</div>
                                            )}
                                        </div>
                                    )
                                },
                                {
                                    accessor: 'outstandingAmount',
                                    title: t('jasa_medis.payment.table.outstanding'),
                                    textAlignment: 'right' as const,
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50 text-right',
                                    cellsStyle: { textAlign: 'right' },
                                    render: (r: MedicalFeeData) => <div className="font-mono font-bold text-gray-900 text-right">{FormatNumberRounded(r.outstandingAmount)}</div>
                                },
                                {
                                    accessor: 'status',
                                    title: t('jasa_medis.payment.table.status'),
                                    textAlignment: 'center' as const,
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (r: MedicalFeeData) => (
                                        <span className={`px-2 py-1 rounded-full text-xs font-semibold ${getStatusColor(r.status)}`}>
                                            {r.status}
                                        </span>
                                    )
                                },
                            ]}
                            totalRecords={totalRecords}
                            recordsPerPage={pageSize}
                            page={page}
                            onPageChange={setPage}
                            recordsPerPageOptions={PAGE_SIZES}
                            onRecordsPerPageChange={setPageSize}
                            minHeight={200}
                            paginationText={({ from, to, totalRecords }) =>
                                `Showing  ${from} to ${to} of ${totalRecords} entries`
                            }
                            fetching={isLoading}
                            loaderVariant="dots"
                            loaderColor="#4361ee"
                            styles={{
                                root: {
                                    border: 'none',
                                    borderRadius: '0.75rem',
                                    overflow: 'hidden',
                                },
                                pagination: {
                                    borderTop: '1px solid #f1f5f9',
                                    margin: '1rem',
                                },
                                header: {
                                    backgroundColor: '#4361ee',
                                    color: '#ffffff',
                                    fontWeight: 'bold',
                                    textTransform: 'uppercase',
                                    letterSpacing: '0.05em',
                                },
                            }}
                        />
                    </div>
                </CardContent>
            </Card>

            <ConfirmationModal
                confirmText={t('jasa_medis.payment.modal.confirm_pay_btn')}
                message={t('jasa_medis.payment.modal.confirm_msg', {
                    count: selectedRows.size,
                    amount: FormatNumberRounded(selectedAmount)
                })}
                isOpen={showPaymentModal}
                onClose={() => !isProcessingPayment && setShowPaymentModal(false)}
                onConfirm={handlePayment}
            />
        </div>
    );
};

export default JasaMedis;
