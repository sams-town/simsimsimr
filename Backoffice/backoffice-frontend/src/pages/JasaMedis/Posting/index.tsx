import { useState, useEffect, useRef } from 'react';
import { useNavigate } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import { useDispatch } from 'react-redux';
import { setPageTitle } from '../../../store/themeConfigSlice';
import { DataTable, DataTableSortStatus } from 'mantine-datatable';
import { useGetMedicalFeeListQuery, usePostingBatchMutation, useGetDoctorOptionsQuery, useGetPaymentMethodOptionsQuery, useCancelPostingBatchMutation, useExportMedicalFeeMutation, useUploadBpjsAdjustmentMutation, useUploadBulkManualMutation, useDownloadBulkManualTemplateMutation } from '@/store/api/medicalFee/medicalFeeApiSlice';
import Select from 'react-select';
import Flatpickr from 'react-flatpickr';
import 'flatpickr/dist/flatpickr.css';
import { toastMessage } from '@/utils/toastUtils';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import TimelineButton from '@/components/ActivityTimeline/TimelineButton';
import { AUDIT_MODULE } from '@/components/ActivityTimeline/modules';
import { FileText, Send, XCircle, Download, Trash2, UploadCloud, FilePlus, FileSpreadsheet } from 'lucide-react';
import IconCalendar from '@/components/Icon/IconCalendar';
import ConfirmationModal from '@/components/ConfirmationModal';

interface MedicalFeeData {
    medicalFeeId: number;
    doctorId: number;
    doctorCode: string;
    doctorName: string;
    period: string;
    patientType: string;
    payerCode: string;
    payerName: string;
    totalFee: number;
    status: string;
    paymentVoucher?: string | null; // Added
    procedureDate: string;
    procedureName: string;
    batchNumber: string;
    patientName?: string;
    medicalRecordNo?: string;
    sepNo?: string;
    medicalProcedure?: string;
    adjustmentAmount?: number;
}

const PostingJasaMedis = () => {
    const navigate = useNavigate();
    const [page, setPage] = useState(1);
    const PAGE_SIZE = 10;
    const [selectedRows, setSelectedRows] = useState<Set<number>>(new Set());
    const fileInputRef = useState<any>(null); // Use Ref properly later
    const [uploadBpjsAdjustment, { isLoading: isUploading }] = useUploadBpjsAdjustmentMutation();
    const [uploadBulkManual, { isLoading: isUploadingBulk }] = useUploadBulkManualMutation();
    const [downloadTemplate, { isLoading: isDownloadingTemplate }] = useDownloadBulkManualTemplateMutation();
    const inputFile = useRef<HTMLInputElement>(null);
    const inputBulkFile = useRef<HTMLInputElement>(null);
    // ... existing filters and state ...
    const [filters, setFilters] = useState({
        doctorId: null as any,
        payerCode: [] as any, // Changed to array for multi-select
        status: { value: 'Pending', label: 'Pending' } as any,
        procedureDateFrom: null as Date | null,
        procedureDateTo: null as Date | null,
        keyword: '' as string, // Search: Tindakan, No Registrasi, No SEP
        medicalProcedure: { value: '', label: 'Semua' } as any, // Rawat Jalan / Rawat Inap
    });
    // ... existing sortStatus state ...
    const [sortStatus, setSortStatus] = useState<DataTableSortStatus>({
        columnAccessor: 'createdDate',
        direction: 'desc',
    });
    // ... existing modals and hooks ...
    const [showConfirmModal, setShowConfirmModal] = useState(false);
    const { t } = useTranslation();
    const dispatch = useDispatch();

    useEffect(() => {
        dispatch(setPageTitle(t('jasa_medis.posting.title')));
    }, [dispatch, t]);

    // ... existing cancel/posting/export logic hooks ...
    const [showCancelModal, setShowCancelModal] = useState(false);
    const [cancelPostingBatch, { isLoading: isCancelling }] = useCancelPostingBatchMutation();
    const [exportMedicalFee, { isLoading: isExporting }] = useExportMedicalFeeMutation();
    const [postingBatch, { isLoading: isPosting }] = usePostingBatchMutation();

    // ... existing formatDateForApi helper ...
    // Helper to format date as YYYY-MM-DD (local date, no timezone conversion)
    const formatDateForApi = (date: Date | null): string => {
        if (!date) return '';
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    };

    const handleUpload = async (e: any) => {
        const file = e.target.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('file', file);

        try {
            await uploadBpjsAdjustment(formData).unwrap();
            toastMessage('File Adjustment berhasil diupload', 'success');
            if (inputFile.current) inputFile.current.value = '';
        } catch (error: any) {
            toastMessage(error?.data?.message || 'Gagal upload file', 'error');
        }
    };

    const handleBulkUpload = async (e: any) => {
        const file = e.target.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('file', file);

        try {
            const result = await uploadBulkManual(formData).unwrap();
            const data = result?.data;
            const inserted = data?.insertedCount || 0;
            const skipped = data?.skippedCount || 0;

            if (data?.hasErrors && data?.errorFile) {
                // Auto-download error Excel file
                const byteCharacters = atob(data.errorFile);
                const byteNumbers = new Array(byteCharacters.length);
                for (let i = 0; i < byteCharacters.length; i++) {
                    byteNumbers[i] = byteCharacters.charCodeAt(i);
                }
                const byteArray = new Uint8Array(byteNumbers);
                const blob = new Blob([byteArray], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                const url = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', data.errorFileName || 'Error_BulkJasaMedis.xlsx');
                document.body.appendChild(link);
                link.click();
                link.remove();

                toastMessage(`Import selesai: ${inserted} berhasil, ${skipped} gagal. File error ter-download otomatis.`, inserted > 0 ? 'success' : 'warning');
            } else {
                toastMessage(`Import selesai: ${inserted} data berhasil diimport`, 'success');
            }

            if (inputBulkFile.current) inputBulkFile.current.value = '';
            if (inserted > 0) refetch();
        } catch (error: any) {
            toastMessage(error?.data?.message || 'Gagal upload file bulk', 'error');
            if (inputBulkFile.current) inputBulkFile.current.value = '';
        }
    };

    const handleDownloadTemplate = async () => {
        try {
            const blob = await downloadTemplate({}).unwrap();
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', 'Template_Bulk_JasaMedis.xlsx');
            document.body.appendChild(link);
            link.click();
            link.remove();
        } catch (error: any) {
            toastMessage(error?.data?.message || 'Gagal download template', 'error');
        }
    };

    const [doctorSearch, setDoctorSearch] = useState('');
    const [paymentMethodSearch, setPaymentMethodSearch] = useState('');

    const isPending = filters.status?.value === 'Pending';
    const isPostingStatus = filters.status?.value === 'Posting';

    // ... existing useGetMedicalFeeListQuery ...
    const { data: listData, isLoading, refetch } = useGetMedicalFeeListQuery({
        status: filters.status?.value || '',
        doctorId: filters.doctorId?.desc || '',
        payerCode: Array.isArray(filters.payerCode) ? filters.payerCode.map((p: any) => p.value).join(',') : '',
        procedureDateFrom: formatDateForApi(filters.procedureDateFrom),
        procedureDateTo: formatDateForApi(filters.procedureDateTo),
        keyword: filters.keyword || '', // Single search for Tindakan/NoRegist/NoSEP
        medicalProcedure: filters.medicalProcedure?.value || '',
        page: page,
        pageSize: PAGE_SIZE,
        orderBy: sortStatus.columnAccessor,
        orderType: sortStatus.direction,
    });

    const { data: doctorOptions } = useGetDoctorOptionsQuery(doctorSearch);
    const { data: paymentMethodOptions } = useGetPaymentMethodOptionsQuery(paymentMethodSearch);

    const records = listData?.data?.data || [];
    const total = listData?.data?.totalData || listData?.data?.total || 0;

    // ... existing selection handlers ...
    const handleSelectRow = (id: number, checked: boolean) => {
        const newSelected = new Set(selectedRows);
        if (checked) {
            newSelected.add(id);
        } else {
            newSelected.delete(id);
        }
        setSelectedRows(newSelected);
    };

    const handleSelectAll = (checked: boolean) => {
        if (checked) {
            // Select all visible records that match the current status filter (Pending or Posting)
            const allIds = records
                .filter((r: MedicalFeeData) => {
                    const status = r.status?.toLowerCase();
                    return (isPending && status === 'pending') || (isPostingStatus && (status === 'posting' || status === 'unpaid'));
                })
                .map((r: MedicalFeeData) => r.medicalFeeId);
            setSelectedRows(new Set(allIds));
        } else {
            setSelectedRows(new Set());
        }
    };

    // ... existing action handlers (handlePosting, handleCancel, handleExport) ...
    const handlePosting = async () => {
        if (selectedRows.size === 0) {
            toastMessage(t('jasa_medis.posting.toast.select_item_posting'), 'warning');
            return;
        }

        try {
            const result = await postingBatch({
                feeIds: Array.from(selectedRows),
            }).unwrap();

            toastMessage(t('jasa_medis.posting.toast.posting_success', { count: result.data?.postedCount || selectedRows.size, batch: result.data?.batchNumber }), 'success');
            setSelectedRows(new Set());
            refetch();
        } catch (error: any) {
            toastMessage(error?.data?.message || t('jasa_medis.posting.toast.posting_failed'), 'error');
        }
    };

    const handleCancelPosting = async () => {
        if (selectedRows.size === 0) return;

        try {
            await cancelPostingBatch({ feeIds: Array.from(selectedRows) }).unwrap();
            toastMessage(t('jasa_medis.posting.toast.cancel_success'), 'success');
            setShowCancelModal(false);
            setSelectedRows(new Set());
            refetch();
        } catch (error: any) {
            toastMessage(error?.data?.message || t('jasa_medis.posting.toast.cancel_failed'), 'error');
        }
    };

    const handleExport = async () => {
        try {
            const blob = await exportMedicalFee({
                status: filters.status?.value || '',
                doctorId: filters.doctorId?.desc || '',
                payerCode: Array.isArray(filters.payerCode) ? filters.payerCode.map((p: any) => p.value).join(',') : '',
                procedureDateFrom: formatDateForApi(filters.procedureDateFrom),
                procedureDateTo: formatDateForApi(filters.procedureDateTo),
                keyword: filters.keyword || '',
                medicalProcedure: filters.medicalProcedure?.value || '',
                orderBy: sortStatus.columnAccessor,
                orderType: sortStatus.direction,
            }).unwrap();

            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', `JasaMedis_Posting_${new Date().getTime()}.xlsx`);
            document.body.appendChild(link);
            link.click();
            link.remove();
        } catch (error) {
            toastMessage(t('jasa_medis.posting.toast.export_failed'), 'error');
        }
    };

    const formatCurrency = (value: number) => {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
    };

    return (
        <div className="space-y-6">
            <Card className="shadow-sm border-gray-200">
                <CardHeader className="bg-gray-50/50 pb-4 border-b border-gray-100">
                    <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div className="flex items-center gap-3">
                            <div className="p-2 bg-primary/10 rounded-lg">
                                <FileText className="w-6 h-6 text-primary" />
                            </div>
                            <div>
                                <CardTitle className="text-xl font-bold text-gray-800">{t('jasa_medis.posting.title')}</CardTitle>
                                <p className="text-sm text-gray-500 mt-1">{t('jasa_medis.posting.subtitle')}</p>
                            </div>
                        </div>

                        {/* Right side - Filters & Actions */}
                        <div className="flex flex-wrap items-center gap-2 ml-auto w-full lg:w-auto" style={{ position: 'relative', zIndex: 10 }}>
                            {/* Row 1: Dropdowns */}
                            <div className="flex flex-wrap items-center gap-2 w-full lg:w-auto">
                                {/* Status Filter */}
                                <div className="w-[110px]">
                                    <Select
                                        options={[
                                            { value: 'Pending', label: 'Pending' },
                                            { value: 'Posting', label: 'Posting' },
                                            { value: 'Paid', label: 'Paid' },
                                        ]}
                                        value={filters.status}
                                        onChange={(val) => {
                                            setFilters({ ...filters, status: val });
                                            setSelectedRows(new Set()); // Clear selection on filter change
                                        }}
                                        isClearable={false}
                                        placeholder="Status"
                                        className="text-sm"
                                        menuPortalTarget={document.body}
                                        styles={{ menuPortal: (base) => ({ ...base, zIndex: 9999 }) }}
                                    />
                                </div>

                                {/* Doctor Filter */}
                                <div className="w-[140px]">
                                    <Select
                                        options={doctorOptions?.data || []}
                                        value={filters.doctorId}
                                        onInputChange={(val) => setDoctorSearch(val)}
                                        onChange={(val) => setFilters({ ...filters, doctorId: val })}
                                        isClearable
                                        placeholder="Dokter..."
                                        className="text-sm"
                                        menuPortalTarget={document.body}
                                        styles={{ menuPortal: (base) => ({ ...base, zIndex: 9999 }) }}
                                    />
                                </div>

                                {/* Cara Bayar Filter */}
                                <div className="w-[180px]">
                                    <Select
                                        options={paymentMethodOptions?.data || []}
                                        value={filters.payerCode}
                                        onInputChange={(val) => setPaymentMethodSearch(val)}
                                        onChange={(val: any) => setFilters({ ...filters, payerCode: val || [] })}
                                        isMulti
                                        isClearable
                                        placeholder="Cara Bayar..."
                                        className="text-sm"
                                        menuPortalTarget={document.body}
                                        styles={{ menuPortal: (base) => ({ ...base, zIndex: 9999 }) }}
                                    />
                                </div>

                                {/* Jenis Pelayanan Filter - Rawat Jalan/Rawat Inap */}
                                <div className="w-[120px]">
                                    <Select
                                        options={[
                                            { value: '', label: 'Semua' },
                                            { value: 'Rawat Jalan', label: 'Rawat Jalan' },
                                            { value: 'Rawat Inap', label: 'Rawat Inap' },
                                        ]}
                                        value={filters.medicalProcedure}
                                        onChange={(val) => setFilters({ ...filters, medicalProcedure: val || { value: '', label: 'Semua' } })}
                                        isClearable={false}
                                        placeholder="Pelayanan..."
                                        className="text-sm"
                                        menuPortalTarget={document.body}
                                        styles={{ menuPortal: (base) => ({ ...base, zIndex: 9999 }) }}
                                    />
                                </div>

                                {/* Keyword Search: Tindakan, No Registrasi, No SEP */}
                                <div className="w-[180px]">
                                    <input
                                        type="text"
                                        value={filters.keyword}
                                        onChange={(e) => setFilters({ ...filters, keyword: e.target.value })}
                                        placeholder="Cari Tindakan/SEP"
                                        className="flex h-[38px] w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                                    />
                                </div>

                                {/* Date Range Filter */}
                                <div className="relative w-[160px]">
                                    <Flatpickr
                                        value={[filters.procedureDateFrom, filters.procedureDateTo].filter(Boolean) as Date[]}
                                        options={{ mode: 'range', dateFormat: 'd/m/Y', enableTime: false }}
                                        className="flex h-[38px] w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-primary ltr:pl-8 rtl:pr-8"
                                        onChange={(dates) => {
                                            if (dates.length === 2) {
                                                setFilters({ ...filters, procedureDateFrom: dates[0], procedureDateTo: dates[1] });
                                            } else if (dates.length === 0) {
                                                setFilters({ ...filters, procedureDateFrom: null, procedureDateTo: null });
                                            }
                                        }}
                                        placeholder="Periode..."
                                    />
                                    <div className="absolute inset-y-0 ltr:left-2.5 rtl:right-2.5 flex items-center pointer-events-none text-gray-400">
                                        <IconCalendar className="w-4 h-4" />
                                    </div>
                                </div>
                            </div>

                            {/* Actions: Export + Posting/Cancel */}
                            <div className="flex items-center gap-2">
                                {/* Input Manual Button */}
                                <Button
                                    variant="outline"
                                    size="sm"
                                    onClick={() => navigate('/jasa-medis/posting/create')}
                                    className="gap-2"
                                >
                                    <FilePlus className="h-4 w-4" />
                                    Input Manual
                                </Button>

                                {/* Download Template Button */}
                                <Button
                                    variant="outline"
                                    size="sm"
                                    onClick={handleDownloadTemplate}
                                    disabled={isDownloadingTemplate}
                                    title="Download Template Bulk Jasa Medis"
                                    className="gap-2"
                                >
                                    <FileSpreadsheet className="h-4 w-4" />
                                    Template
                                </Button>

                                {/* Upload Bulk Manual Button */}
                                <input
                                    type="file"
                                    ref={inputBulkFile}
                                    style={{ display: 'none' }}
                                    onChange={handleBulkUpload}
                                    accept=".csv,.xlsx"
                                />
                                <Button
                                    variant="outline"
                                    size="sm"
                                    onClick={() => inputBulkFile.current?.click()}
                                    disabled={isUploadingBulk}
                                    title="Upload Bulk Jasa Medis"
                                    className="gap-2"
                                >
                                    <UploadCloud className="h-4 w-4" />
                                    {isUploadingBulk ? 'Uploading...' : 'Upload Bulk'}
                                </Button>

                                {/* Export Button */}
                                <Button
                                    variant="outline"
                                    size="sm"
                                    onClick={handleExport}
                                    disabled={isExporting}
                                    title={t('jasa_medis.posting.export_btn')}
                                >
                                    <Download className="h-4 w-4 ltr:mr-2 rtl:ml-2" />
                                    Export
                                </Button>

                                {/* Upload Button */}
                                <input
                                    type="file"
                                    ref={inputFile}
                                    style={{ display: 'none' }}
                                    onChange={handleUpload}
                                    accept=".xlsx,.xls,.txt"
                                />
                                <Button
                                    variant="outline"
                                    size="sm"
                                    onClick={() => inputFile.current?.click()}
                                    disabled={isUploading}
                                    title="Upload BPJS Adjustment"
                                >
                                    <UploadCloud className="h-4 w-4 ltr:mr-2 rtl:ml-2" />
                                    Upload BPJS Adjustment
                                </Button>

                                {/* ACTION BUTTON: Posting or Cancel Posting */}
                                {isPending && (
                                    <Button onClick={() => {
                                        if (selectedRows.size === 0) {
                                            toastMessage(t('jasa_medis.posting.toast.select_item_posting'), 'warning');
                                            return;
                                        }
                                        setShowConfirmModal(true);
                                    }} disabled={selectedRows.size === 0 || isPosting} className="gap-2">
                                        <Send className="h-4 w-4" />
                                        <span className="hidden sm:inline">{isPosting ? t('jasa_medis.posting.processing') : `${t('jasa_medis.posting.posting_btn')} (${selectedRows.size})`}</span>
                                    </Button>
                                )}

                                {isPostingStatus && (
                                    <Button
                                        onClick={() => {
                                            if (selectedRows.size === 0) {
                                                toastMessage(t('jasa_medis.posting.toast.select_item_cancel'), 'warning');
                                                return;
                                            }
                                            setShowCancelModal(true);
                                        }}
                                        disabled={selectedRows.size === 0 || isCancelling}
                                        variant="destructive"
                                        className="gap-2"
                                    >
                                        <XCircle className="h-4 w-4" />
                                        <span className="hidden sm:inline">{isCancelling ? t('jasa_medis.posting.processing') : `${t('jasa_medis.posting.cancel_posting_btn')} (${selectedRows.size})`}</span>
                                    </Button>
                                )}
                            </div>
                        </div>
                    </div>
                </CardHeader>
                <CardContent className="p-0">
                    {/* Data Table */}
                    <div className="datatables z-0 rounded-lg border border-gray-100 overflow-hidden">
                        <DataTable
                            highlightOnHover
                            className="table-hover"
                            records={records}
                            columns={[
                                {
                                    accessor: 'checkbox',
                                    hidden: !isPending && !isPostingStatus,
                                    title: (
                                        <input
                                            type="checkbox"
                                            checked={
                                                records.length > 0 &&
                                                records.filter((r: MedicalFeeData) => (isPending && r.status?.toLowerCase() === 'pending') || (isPostingStatus && (r.status?.toLowerCase() === 'posting' || (r.status?.toLowerCase() === 'unpaid' && !r.paymentVoucher)))).length === selectedRows.size && selectedRows.size > 0
                                            }
                                            onChange={(e) => handleSelectAll(e.target.checked)}
                                            className="rounded border-gray-300"
                                        />
                                    ),
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (row: MedicalFeeData) => (
                                        <input
                                            type="checkbox"
                                            checked={selectedRows.has(row.medicalFeeId)}
                                            onChange={(e) => handleSelectRow(row.medicalFeeId, e.target.checked)}
                                            disabled={(!isPending && !isPostingStatus) || (row.paymentVoucher !== null && row.paymentVoucher !== "" && row.paymentVoucher !== undefined)}
                                            className="rounded border-gray-300"
                                        />
                                    ),
                                    width: 50,
                                },
                                {
                                    accessor: 'doctorInfo',
                                    title: t('jasa_medis.posting.table.doctor_name', 'Dokter'),
                                    sortable: true,
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (row: MedicalFeeData) => (
                                        <div>
                                            <div className="font-semibold text-gray-900">{row.doctorName || '-'}</div>
                                            <div className="text-xs text-gray-500">{row.doctorCode || '-'}</div>
                                        </div>
                                    )
                                },
                                {
                                    accessor: 'sepNo',
                                    title: 'No SEP',
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    width: 160,
                                    render: (row: MedicalFeeData) => (
                                        <span className="text-gray-700 font-mono text-xs">{row.sepNo || '-'}</span>
                                    )
                                },
                                {
                                    accessor: 'procedureInfo',
                                    title: t('jasa_medis.posting.table.procedure_name', 'Tindakan'),
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (row: MedicalFeeData) => {
                                        let dateStr = '-';
                                        if (row.procedureDate) {
                                            const date = new Date(row.procedureDate);
                                            const day = date.getUTCDate();
                                            const month = date.toLocaleDateString('id-ID', { month: 'short', timeZone: 'UTC' });
                                            const year = date.getUTCFullYear();
                                            dateStr = `${day} ${month} ${year}`;
                                        }
                                        return (
                                            <div>
                                                <div className="font-medium text-gray-900 whitespace-normal" style={{ maxWidth: '200px' }}>{row.procedureName || '-'}</div>
                                                <div className="text-xs text-gray-500">{dateStr}</div>
                                            </div>
                                        );
                                    }
                                },
                                {
                                    accessor: 'patientInfo',
                                    title: t('jasa_medis.posting.table.patient_name', 'Pasien'),
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    width: 180,
                                    render: (row: MedicalFeeData) => (
                                        <div>
                                            <div className="font-medium text-gray-900">{row.patientName || '-'}</div>
                                            <div className="text-xs text-gray-500">RM: {row.medicalRecordNo || '-'}</div>
                                        </div>
                                    )
                                },
                                {
                                    accessor: 'payerInfo',
                                    title: t('jasa_medis.posting.table.payment_method', 'Cara Bayar'),
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (row: MedicalFeeData) => (
                                        <div>
                                            <div className="font-medium text-gray-900">{row.payerName || '-'}</div>
                                        </div>
                                    )
                                },
                                {
                                    accessor: 'medicalProcedure',
                                    title: 'Jenis Pelayanan',
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    width: 130,
                                    render: (row: MedicalFeeData) => (
                                        <span className={`px-2 py-1 rounded-full text-xs font-semibold ${row.medicalProcedure === 'Rawat Inap' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700'}`}>
                                            {row.medicalProcedure || '-'}
                                        </span>
                                    )
                                },
                                {
                                    accessor: 'totalFee',
                                    title: t('jasa_medis.posting.table.total_fee'),
                                    sortable: true,
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50 text-right',
                                    textAlignment: 'right',
                                    render: (row: MedicalFeeData) => (
                                        <div className="text-right">
                                            <div className="font-mono font-bold text-gray-900">
                                                {formatCurrency(row.adjustmentAmount != null && row.adjustmentAmount !== 0 && row.adjustmentAmount !== row.totalFee
                                                    ? row.adjustmentAmount : row.totalFee)}
                                            </div>
                                            {row.adjustmentAmount != null && row.adjustmentAmount !== 0 && row.adjustmentAmount !== row.totalFee && (
                                                <div className="font-mono text-xs text-gray-400 line-through">
                                                    {formatCurrency(row.totalFee)}
                                                </div>
                                            )}
                                        </div>
                                    )
                                },
                                {
                                    accessor: 'status',
                                    title: t('jasa_medis.posting.table.status'),
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (row: MedicalFeeData) => (
                                        <span className={`px-2 py-1 rounded-full text-xs font-semibold ${row.status === 'Pending' ? 'bg-warning/10 text-warning' :
                                            row.status === 'Posting' ? 'bg-info/10 text-info' :
                                                'bg-success/10 text-success'
                                            }`}>
                                            {row.status}
                                        </span>
                                    )
                                },
                                {
                                    accessor: 'action',
                                    title: t('common.actions', 'Aksi'),
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    width: 70,
                                    render: (row: MedicalFeeData) => (
                                        <TimelineButton
                                            module={AUDIT_MODULE.MEDICAL_FEE}
                                            refId={row.medicalFeeId}
                                            title={row.patientName || row.doctorName}
                                        />
                                    )
                                },
                            ]}
                            totalRecords={total}
                            recordsPerPage={PAGE_SIZE}
                            page={page}
                            onPageChange={setPage}
                            sortStatus={sortStatus}
                            onSortStatusChange={setSortStatus}
                            fetching={isLoading}
                            minHeight={200}
                            noRecordsText={t('jasa_medis.posting.no_data')}
                            loaderVariant="dots"
                            loaderColor="#4361ee"
                            styles={{
                                root: { border: 'none', borderRadius: '0.75rem', overflow: 'hidden' },
                                pagination: { borderTop: '1px solid #f1f5f9', margin: '1rem' },
                            }}
                        />
                    </div>
                </CardContent>
            </Card>

            <ConfirmationModal
                isOpen={showConfirmModal}
                onClose={() => setShowConfirmModal(false)}
                onConfirm={handlePosting}
                title={t('jasa_medis.posting.modal.confirm_posting_title')}
                message={t('jasa_medis.posting.modal.confirm_posting_message', { count: selectedRows.size })}
                confirmText={t('jasa_medis.posting.modal.yes_posting')}
                cancelText={t('jasa_medis.posting.modal.cancel')}
                loading={isPosting}
                variant="primary"
            />

            <ConfirmationModal
                isOpen={showCancelModal}
                onClose={() => {
                    setShowCancelModal(false);
                }}
                onConfirm={handleCancelPosting}
                title={t('jasa_medis.posting.modal.confirm_cancel_title')}
                message={t('jasa_medis.posting.modal.confirm_cancel_message', { count: selectedRows.size })}
                confirmText={t('jasa_medis.posting.modal.yes_cancel')}
                cancelText={t('jasa_medis.posting.modal.cancel')}
                loading={isCancelling}
                variant="danger"
            />
        </div>
    );
};

export default PostingJasaMedis;
