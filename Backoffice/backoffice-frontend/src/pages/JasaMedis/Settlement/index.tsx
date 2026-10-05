import { useState, useEffect } from 'react';
import { useTranslation } from 'react-i18next';
import { useDispatch } from 'react-redux';
import { setPageTitle } from '../../../store/themeConfigSlice';
import { DataTable } from 'mantine-datatable';
import { useGetSettlementListQuery, useGetDoctorOptionsQuery, useExportVoucherDetailMutation, useExportSettlementMutation, useExportSettlementMakerMutation } from '@/store/api/medicalFee/medicalFeeApiSlice';
import { useNavigate } from 'react-router-dom';
import Select from 'react-select';
import Flatpickr from 'react-flatpickr';
import 'flatpickr/dist/flatpickr.css';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Wallet, DollarSign, Search, CheckCircle, Clock, Eye, AlertCircle, Download } from 'lucide-react';
import IconCalendar from '@/components/Icon/IconCalendar';

interface SettlementSummary {
    doctorId: number;
    doctorName: string;
    paymentVoucher: string;
    voucherDate: string;
    doctorShare: number;
    totalFee: number;
    totalAfterShare: number;
    itemCount: number;
    status: string;
}

// Helper to format date for API (YYYY-MM-DD)
const formatDateForApi = (date: Date | null): string | null => {
    if (!date) return null;
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
};

// Helper to format date for display
const formatDateDisplay = (dateStr: string | null): string => {
    if (!dateStr) return '-';
    const date = new Date(dateStr);
    const day = date.getUTCDate();
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    const month = months[date.getUTCMonth()];
    const year = date.getUTCFullYear();
    return `${day} ${month} ${year}`;
};



const SettlementList = () => {
    const [page, setPage] = useState(1);
    const PAGE_SIZE = 10;
    const [filters, setFilters] = useState({
        doctor: null as any,
        voucherNo: '',
        status: { value: 'settlement', label: 'Semua Status' },
        dateFrom: null as Date | null,
        dateTo: null as Date | null,
    });
    const navigate = useNavigate();
    const { t } = useTranslation();
    const dispatch = useDispatch();
    const [doctorSearch, setDoctorSearch] = useState('');

    // Print State


    useEffect(() => {
        dispatch(setPageTitle(t('jasa_medis.settlement.title')));
    }, [dispatch, t]);

    const { data: doctorOptions } = useGetDoctorOptionsQuery(doctorSearch);
    const { data: listData, isLoading } = useGetSettlementListQuery({
        doctorId: filters.doctor?.desc || '',
        paymentVoucher: filters.voucherNo,
        status: filters.status?.value || 'settlement', // Default to 'settlement' if empty/null
        voucherDateFrom: formatDateForApi(filters.dateFrom),
        voucherDateTo: formatDateForApi(filters.dateTo),
        page: page,
        pageSize: PAGE_SIZE,
    });

    const records = listData?.data?.data || [];
    const total = listData?.data?.totalData || listData?.data?.total || 0;

    const formatCurrency = (value: number) => {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
    };

    // Base64 URL-safe encoding
    const encodeBase64Url = (str: string): string => {
        return btoa(str).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
    };

    const handleDetail = (voucherId: string) => {
        navigate(`/jasa-medis/settlement/${encodeBase64Url(voucherId)}`);
    };

    const [exportVoucherDetail] = useExportVoucherDetailMutation();

    const handleDownload = async (voucherId: string) => {
        try {
            // Reusing the same endpoint logic: pass voucherId as doctorCode and set status to 'paid'
            // This triggers the backend to look up by PaymentVoucher
            const response = await exportVoucherDetail({ doctorCode: voucherId, status: 'paid' }).unwrap();
            const url = window.URL.createObjectURL(response);
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', `VoucherDetail_${voucherId}.xlsx`);
            document.body.appendChild(link);
            link.click();
            link.parentNode?.removeChild(link);
        } catch (error) {
            console.error('Download failed', error);
        }
    };

    const [exportSettlement, { isLoading: isExporting }] = useExportSettlementMutation();
    const [exportSettlementMaker, { isLoading: isExportingMaker }] = useExportSettlementMakerMutation();

    const handleExportList = async () => {
        try {
            const params = {
                doctorId: filters.doctor?.desc || '',
                paymentVoucher: filters.voucherNo,
                status: filters.status?.value || 'settlement',
                voucherDateFrom: formatDateForApi(filters.dateFrom),
                voucherDateTo: formatDateForApi(filters.dateTo),
                type: 'xlsx'
            };

            const response = await exportSettlement(params).unwrap();
            const url = window.URL.createObjectURL(response);
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', `Settlement_${new Date().toISOString().slice(0, 10)}.xlsx`);
            document.body.appendChild(link);
            link.click();
            link.parentNode?.removeChild(link);
        } catch (error) {
            console.error('Export failed', error);
        }
    };

    const handleExportMaker = async () => {
        try {
            const params = {
                doctorId: filters.doctor?.desc || '',
                paymentVoucher: filters.voucherNo,
                status: filters.status?.value || 'settlement',
                voucherDateFrom: formatDateForApi(filters.dateFrom),
                voucherDateTo: formatDateForApi(filters.dateTo),
            };

            const response = await exportSettlementMaker(params).unwrap();
            const url = window.URL.createObjectURL(response);
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', `Settlement_Maker_${new Date().toISOString().slice(0, 10)}.xlsx`);
            document.body.appendChild(link);
            link.click();
            link.parentNode?.removeChild(link);
        } catch (error) {
            console.error('Export Maker failed', error);
        }
    };



    return (
        <div className="space-y-6">
            <Card>
                <CardHeader className="border-b border-gray-100 pb-4">
                    <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        {/* Left side - Title */}
                        <div className="flex items-center gap-3 shrink-0">
                            <div className="p-2 rounded-lg bg-success/10">
                                <Wallet className="h-5 w-5 text-success" />
                            </div>
                            <div>
                                <CardTitle className="text-lg whitespace-nowrap">{t('jasa_medis.settlement.title')}</CardTitle>
                                <p className="text-sm text-gray-500 mt-0.5">
                                    {t('jasa_medis.settlement.subtitle')}
                                </p>
                            </div>
                        </div>

                        {/* Right side - Filters */}
                        <div className="flex flex-wrap items-center justify-end gap-3 w-full" style={{ position: 'relative', zIndex: 10 }}>
                            <Button
                                variant="outline"
                                onClick={handleExportList}
                                disabled={isExporting}
                                className="whitespace-nowrap gap-2 bg-white dark:bg-black"
                                title="Download Excel"
                            >
                                <Download className="h-4 w-4" />
                                <span className="hidden sm:inline">Export</span>
                            </Button>

                            <Button
                                variant="outline"
                                onClick={handleExportMaker}
                                disabled={isExportingMaker}
                                className="whitespace-nowrap gap-2 bg-white dark:bg-black border-success text-success hover:bg-success hover:text-white"
                                title="Download Excel for Bank Upload (Maker)"
                            >
                                <Download className="h-4 w-4" />
                                <span className="hidden sm:inline">Export Maker</span>
                            </Button>

                            {/* Doctor Filter */}
                            <div className="w-full md:w-[180px]">
                                <Select
                                    options={doctorOptions?.data || []}
                                    value={filters.doctor}
                                    onInputChange={(val) => setDoctorSearch(val)}
                                    onChange={(val) => setFilters({ ...filters, doctor: val })}
                                    isClearable
                                    placeholder={t('jasa_medis.settlement.filter_doctor')}
                                    className="text-sm"
                                    menuPortalTarget={document.body}
                                    styles={{ menuPortal: (base) => ({ ...base, zIndex: 9999 }) }}
                                />
                            </div>

                            {/* Status Filter */}
                            <div className="w-full md:w-[150px]">
                                <Select
                                    options={[
                                        { value: 'settlement', label: t('ar.penerimaan_piutang_page.filter.status_value.all') || 'Semua Status' },
                                        { value: 'final', label: t('ar.penerimaan_piutang_page.filter.status_value.final') || 'Final' },
                                        { value: 'paid', label: t('ar.penerimaan_piutang_page.filter.status_value.paid') || 'Paid' }
                                    ]}
                                    value={filters.status}
                                    onChange={(val: any) => {
                                        setPage(1);
                                        setFilters(prev => ({ ...prev, status: val || { value: 'settlement', label: t('ar.penerimaan_piutang_page.filter.status_value.all') || 'Semua Status' } }));
                                    }}
                                    placeholder={t('ar.penerimaan_piutang_page.filter.status')}
                                    className="text-sm"
                                    isClearable={false}
                                    menuPortalTarget={document.body}
                                    styles={{ menuPortal: (base) => ({ ...base, zIndex: 9999 }) }}
                                />
                            </div>

                            {/* Voucher Search */}
                            <div className="relative w-full md:w-[180px]">
                                <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" />
                                <Input
                                    type="text"
                                    placeholder={t('jasa_medis.settlement.filter_voucher')}
                                    value={filters.voucherNo}
                                    onChange={(e) => setFilters({ ...filters, voucherNo: e.target.value })}
                                    className="pl-9"
                                />
                            </div>

                            {/* Date Range Filter */}
                            <div className="relative w-full md:w-[200px]">
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
                                    placeholder={t('jasa_medis.settlement.filter_period')}
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
                                { accessor: 'doctorName', title: t('jasa_medis.settlement.table.doctor_name'), titleClassName: 'font-bold text-gray-800 bg-gray-50/50' },
                                {
                                    accessor: 'paymentVoucher',
                                    title: t('jasa_medis.settlement.table.voucher_no'),
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (row: SettlementSummary) => <span className="font-semibold text-primary">{row.paymentVoucher}</span>
                                },
                                {
                                    accessor: 'voucherDate',
                                    title: t('jasa_medis.settlement.table.voucher_date'),
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (row: SettlementSummary) => formatDateDisplay(row.voucherDate)
                                },
                                { accessor: 'doctorShare', title: t('jasa_medis.settlement.table.share'), titleClassName: 'font-bold text-gray-800 bg-gray-50/50', render: (row: SettlementSummary) => `${row.doctorShare}%` },
                                { accessor: 'itemCount', title: t('jasa_medis.settlement.table.item_count'), titleClassName: 'font-bold text-gray-800 bg-gray-50/50' },
                                {
                                    accessor: 'totalFee',
                                    title: t('jasa_medis.settlement.table.total_fee'),
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50 text-right',
                                    textAlignment: 'right',
                                    render: (row: SettlementSummary) => <div className="font-mono text-gray-600 text-right">{formatCurrency(row.totalFee)}</div>
                                },
                                {
                                    accessor: 'status',
                                    title: t('jasa_medis.settlement.table.status'),
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50',
                                    render: (row: SettlementSummary) => (
                                        <span className={`px-2 py-1 rounded-full text-xs font-semibold capitalize ${row.status?.toLowerCase() === 'paid' ? 'bg-success/10 text-success' : 'bg-warning/10 text-warning'}`}>
                                            {row.status || 'Unknown'}
                                        </span>
                                    )
                                },
                                {
                                    accessor: 'totalAfterShare',
                                    title: t('jasa_medis.settlement.table.total_payment'),
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50 text-right',
                                    textAlignment: 'right',
                                    render: (row: SettlementSummary) => <div className="font-mono font-bold text-primary text-right">{formatCurrency(row.totalAfterShare)}</div>
                                },
                                {
                                    accessor: 'action',
                                    title: t('jasa_medis.settlement.table.action'),
                                    titleClassName: 'font-bold text-gray-800 bg-gray-50/50 text-center',
                                    textAlignment: 'center',
                                    render: (row: SettlementSummary) => (
                                        <div className="flex justify-center">
                                            {row.status?.toLowerCase() === 'paid' ? (
                                                <Button size="sm" variant="outline" className="h-8 gap-1" onClick={() => handleDetail(row.paymentVoucher)}>
                                                    <Eye className="w-3.5 h-3.5" /> {t('jasa_medis.settlement.detail_btn')}
                                                </Button>
                                            ) : (
                                                <Button size="sm" className="bg-success hover:bg-success/90 h-8 gap-1" onClick={() => handleDetail(row.paymentVoucher)}>
                                                    <DollarSign className="w-3.5 h-3.5" /> {t('jasa_medis.settlement.pay_btn')}
                                                </Button>
                                            )}
                                            {/* Download Button for Valid Vouchers */}
                                            {row.paymentVoucher && (
                                                <Button
                                                    size="sm"
                                                    variant="outline"
                                                    className="h-8 gap-1 ml-1"
                                                    onClick={() => handleDownload(row.paymentVoucher)}
                                                    title={t('jasa_medis.payment_voucher.download_tooltip') || 'Download Excel'}
                                                >
                                                    <Download className="w-3.5 h-3.5" />
                                                </Button>
                                            )}
                                        </div>
                                    )
                                },
                            ]}
                            totalRecords={total}
                            recordsPerPage={PAGE_SIZE}
                            page={page}
                            onPageChange={setPage}
                            fetching={isLoading}
                            minHeight={200}
                            noRecordsText={t('jasa_medis.settlement.no_data')}
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

export default SettlementList;


