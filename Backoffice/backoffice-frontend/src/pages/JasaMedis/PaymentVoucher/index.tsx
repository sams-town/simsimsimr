import { useState, useEffect } from 'react';
import { useTranslation } from 'react-i18next';
import { useDispatch } from 'react-redux';
import { setPageTitle } from '../../../store/themeConfigSlice';
import { DataTable } from 'mantine-datatable';
import { useGetVoucherListQuery, useGetDoctorOptionsQuery, useExportVoucherDetailMutation } from '@/store/api/medicalFee/medicalFeeApiSlice';
import { useNavigate } from 'react-router-dom';
import Select from 'react-select';
import Flatpickr from 'react-flatpickr';
import 'flatpickr/dist/flatpickr.css';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { CreditCard, Eye, Download } from 'lucide-react';
import IconCalendar from '@/components/Icon/IconCalendar';

interface DoctorVoucherSummary {
    doctorId: number;
    doctorCode: string;
    doctorName: string;
    period: string;
    itemCount: number;
    totalFee: number;
}

const PaymentVoucherList = () => {
    const [page, setPage] = useState(1);
    const PAGE_SIZE = 10;
    const [filters, setFilters] = useState({
        doctor: null as any,
        status: { value: 'posting', label: 'Posting (Unpaid)' }, // Default to Posting
        dateFrom: null as Date | null,
        dateTo: null as Date | null,
    });
    const { t } = useTranslation();
    const dispatch = useDispatch();
    const navigate = useNavigate();
    const [doctorSearch, setDoctorSearch] = useState('');

    useEffect(() => {
        dispatch(setPageTitle(t('jasa_medis.payment_voucher.title')));
    }, [dispatch, t]);

    const { data: doctorOptions } = useGetDoctorOptionsQuery(doctorSearch);
    const { data: listData, isLoading } = useGetVoucherListQuery({
        doctorCode: filters.doctor?.value || '', // Changed to doctorCode (value from option)
        status: filters.status?.value || '',
        page: page,
        pageSize: PAGE_SIZE,
    });

    // Export Mutation
    const [exportVoucherDetail, { isLoading: isExporting }] = useExportVoucherDetailMutation();

    const records = listData?.data?.data || [];
    const total = listData?.data?.totalData || listData?.data?.total || 0;

    const formatCurrency = (value: number) => {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
    };

    // Base64 URL-safe encode helper
    const base64UrlEncode = (str: string): string => {
        return btoa(str)
            .replace(/\+/g, '-')
            .replace(/\//g, '_')
            .replace(/=+$/, '');
    };

    const handleDetail = (doctorCode: string, paymentVoucher?: string) => {
        const queryParams = new URLSearchParams();
        if (filters.status?.value) {
            queryParams.append('status', filters.status.value);
        }
        // For paid status, use base64 encoded paymentVoucher as identifier; otherwise use doctorCode
        let identifier: string;
        if ((filters.status?.value === 'paid' || filters.status?.value === 'final') && paymentVoucher) {
            identifier = base64UrlEncode(paymentVoucher);
        } else {
            identifier = doctorCode;
        }
        navigate(`/jasa-medis/payment-voucher/${encodeURIComponent(identifier)}?${queryParams.toString()}`);
    };

    const handleDownload = async (doctorCode: string, paymentVoucher: string) => {
        try {
            const status = filters.status?.value || 'posting';

            // If status is paid/final, paymentVoucher (row.period) is the ID.
            // If posting, doctorCode is the ID.
            const identifier = (status === 'paid' || status === 'final') ? paymentVoucher : doctorCode;

            const blob = await exportVoucherDetail({ doctorCode: identifier, status }).unwrap();

            // Create download link
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `Voucher_${status}_${identifier}.xlsx`; // Default name, backend also provides filename
            document.body.appendChild(a);
            a.click();
            a.remove();
        } catch (error) {
            console.error('Export failed', error);
        }
    };

    return (
        <div className="space-y-6">
            <Card>
                <CardHeader className="border-b border-gray-100 pb-4">
                    <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        {/* Left side - Title */}
                        <div className="flex items-center gap-3">
                            <div className="p-2 rounded-lg bg-primary/10">
                                <CreditCard className="h-5 w-5 text-primary" />
                            </div>
                            <div>
                                <CardTitle className="text-lg">{t('jasa_medis.payment_voucher.title')}</CardTitle>
                                <p className="text-sm text-gray-500 mt-0.5">
                                    {t('jasa_medis.payment_voucher.subtitle')}
                                </p>
                            </div>
                        </div>

                        {/* Right side - Filters */}
                        <div className="flex flex-col md:flex-row items-end md:items-center gap-3 ml-auto w-full md:w-auto" style={{ position: 'relative', zIndex: 10 }}>
                            {/* Status Filter */}
                            <div className="w-full md:w-[200px]">
                                <Select
                                    options={[
                                        { value: 'posting', label: t('jasa_medis.payment_voucher.status_posting') || '' },
                                        { value: 'final', label: t('jasa_medis.payment_voucher.status_final') || '' },
                                        { value: 'paid', label: t('jasa_medis.payment_voucher.status_paid') || '' }
                                    ]}
                                    value={filters.status}
                                    onChange={(val) => setFilters({ ...filters, status: val || { value: 'posting', label: t('jasa_medis.payment_voucher.status_posting') || '' } })}
                                    placeholder={t('jasa_medis.payment_voucher.filter_status')}
                                    className="text-sm"
                                    menuPortalTarget={document.body}
                                    styles={{ menuPortal: (base) => ({ ...base, zIndex: 9999 }) }}
                                />
                            </div>

                            {/* Doctor Filter */}
                            <div className="w-full md:w-[200px]">
                                <Select
                                    options={doctorOptions?.data || []}
                                    value={filters.doctor}
                                    onInputChange={(val) => setDoctorSearch(val)}
                                    onChange={(val) => setFilters({ ...filters, doctor: val })}
                                    isClearable
                                    placeholder={t('jasa_medis.payment_voucher.filter_doctor')}
                                    className="text-sm"
                                    menuPortalTarget={document.body}
                                    styles={{ menuPortal: (base) => ({ ...base, zIndex: 9999 }) }}
                                />
                            </div>

                            {/* Date Range Filter */}
                            <div className="relative w-full md:w-[220px]">
                                <Flatpickr
                                    value={[filters.dateFrom, filters.dateTo].filter(Boolean) as Date[]}
                                    options={{ mode: 'range', dateFormat: 'd/m/Y', enableTime: false }}
                                    className="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 ltr:pl-9 rtl:pr-9"
                                    onChange={(dates) => {
                                        if (dates.length === 2) {
                                            setFilters({ ...filters, dateFrom: dates[0], dateTo: dates[1] });
                                        } else if (dates.length === 0) {
                                            setFilters({ ...filters, dateFrom: null, dateTo: null });
                                        }
                                    }}
                                    placeholder={t('jasa_medis.payment_voucher.filter_period')}
                                />
                                <div className="absolute inset-y-0 ltr:left-3 rtl:right-3 flex items-center pointer-events-none text-gray-400">
                                    <IconCalendar className="w-4 h-4" />
                                </div>
                            </div>
                        </div>
                    </div>
                </CardHeader>

                <CardContent className="pt-6">
                    {/* Data Table */}
                    <div className="datatables z-0 rounded-lg border border-gray-100 overflow-hidden">
                        <DataTable
                            highlightOnHover
                            className="whitespace-nowrap table-hover"
                            records={records}
                            columns={[
                                {
                                    accessor: 'period',
                                    title: (filters.status?.value === 'paid' || filters.status?.value === 'final')
                                        ? t('jasa_medis.payment_voucher.table.voucher_number')
                                        : t('jasa_medis.payment_voucher.table.period'),
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (row: DoctorVoucherSummary) => (
                                        <span className="font-mono">{row.period}</span>
                                    )
                                },
                                { accessor: 'doctorCode', title: t('jasa_medis.payment_voucher.table.doctor_code'), titleClassName: 'font-bold text-gray-800 bg-gray-50/50' },
                                { accessor: 'doctorName', title: t('jasa_medis.payment_voucher.table.doctor_name'), sortable: true, titleClassName: 'font-bold text-gray-800 bg-gray-50/50' },
                                { accessor: 'itemCount', title: t('jasa_medis.payment_voucher.table.item_count'), titleClassName: 'font-bold text-gray-800 bg-gray-50/50' },
                                {
                                    accessor: 'totalFee',
                                    title: t('jasa_medis.payment_voucher.table.total_fee'),
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50 text-right',
                                    textAlignment: 'right',
                                    render: (row: DoctorVoucherSummary) => <div className="font-mono font-bold text-gray-900 text-right">{formatCurrency(row.totalFee)}</div>
                                },
                                {
                                    accessor: 'action',
                                    title: t('jasa_medis.payment_voucher.table.action'),
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (row: DoctorVoucherSummary) => (
                                        <div className="flex gap-2">
                                            <Button variant="outline" size="sm" onClick={() => handleDetail(row.doctorCode, row.period)} className="gap-1">
                                                <Eye className="h-4 w-4" />
                                                {t('jasa_medis.payment_voucher.detail_btn')}
                                            </Button>
                                            <Button
                                                variant="outline"
                                                size="sm"
                                                onClick={() => handleDownload(row.doctorCode, row.period)}
                                                className="gap-1"
                                                disabled={isExporting}
                                                title="Download Voucher"
                                            >
                                                <Download className="h-4 w-4" />
                                            </Button>
                                        </div>
                                    ),
                                },
                            ]}
                            totalRecords={total}
                            recordsPerPage={PAGE_SIZE}
                            page={page}
                            onPageChange={setPage}
                            fetching={isLoading}
                            minHeight={200}
                            noRecordsText={t('jasa_medis.payment_voucher.no_data')}
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
        </div>
    );
};

export default PaymentVoucherList;


