import { useEffect, useState } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { useTranslation } from 'react-i18next';
import { useNavigate } from 'react-router-dom';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '@/store/themeConfigSlice';
import { DataTable, DataTableColumn } from 'mantine-datatable';
import Flatpickr from 'react-flatpickr';
import 'flatpickr/dist/flatpickr.css';
import SelectSearch from 'react-select';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import IconCalendar from '@/components/Icon/IconCalendar';
import {
    useGetPettyCashRequestsQuery,
    useGetPettyCashUnitOptionsQuery,
    useGetPettyCashMyUnitQuery,
} from '@/store/api/pettyCash/pettyCashApiSlice';
import { FormatNumber } from '@/utils/formatNumber';
import { FormatDate } from '@/utils/formatDate';
import { Receipt, FileText, CheckCircle, Clock, Download, Loader2 } from 'lucide-react';

interface RequestData {
    requestId: number;
    requestNo: string;
    requestDate: string;
    unitId: number;
    unitCode: string;
    unitName: string;
    requesterName: string;
    purpose: string;
    amount: number;
    approvedAmount: number | null;
    expenseCoaCode: string;
    expenseCoaName: string | null;
    costCenterCode: string | null;
    costCenterName: string | null;
    status: string;
    settlementId: number | null;
    notes: string | null;
    createdDate: string;
}

const settlementStatusOptions = [
    { value: '', label: 'Semua' },
    { value: 'not_settled', label: 'Belum Settlement' },
    { value: 'settled', label: 'Sudah Settlement' },
];

const PettyCashSettlement = () => {
    const dispatch = useDispatch();
    const navigate = useNavigate();
    const { t } = useTranslation();
    const user = useSelector((state: any) => state.auth.user);
    const isPettyCashOfficer = user?.role?.roleID === 5;
    const { data: myUnit } = useGetPettyCashMyUnitQuery(undefined, { skip: !isPettyCashOfficer });

    useEffect(() => {
        dispatch(setPageTitle(t('petty_cash.settlement.title', 'Settlement Kas Kecil')));
        dispatch(setTitle(t('petty_cash.settlement.title', 'Settlement Kas Kecil')));
        dispatch(setBreadcrumbTitle(['Dashboard', t('sidebar.petty_cash', 'Petty Cash'), t('petty_cash.settlement.title', 'Settlement')]));
    }, [dispatch, t]);

    // Filter states
    const [selectedUnit, setSelectedUnit] = useState<string>('');
    const [settlementStatus, setSettlementStatus] = useState<string>('');
    const [startDate, setStartDate] = useState<Date | null>(null);
    const [endDate, setEndDate] = useState<Date | null>(null);

    useEffect(() => {
        if (isPettyCashOfficer && myUnit?.data) {
            setSelectedUnit(String(myUnit.data.unitId));
        }
    }, [isPettyCashOfficer, myUnit]);

    // Pagination
    const [page, setPage] = useState(1);
    const [pageSize, setPageSize] = useState(10);
    const PAGE_SIZES = [10, 20, 30, 50];

    // API
    const { data: unitOptions } = useGetPettyCashUnitOptionsQuery({});

    const formatDateForAPI = (date: Date | null): string | null => {
        if (!date) return null;
        return new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate())).toISOString();
    };

    // Fetch requests for settlement workflow (Posted & Settled)
    const { data: listData, isLoading } = useGetPettyCashRequestsQuery({
        unitId: selectedUnit || undefined,
        status: 'settlement', // Includes both Posted and Settled
        settlementStatus: settlementStatus || undefined, // 'settled' or 'not_settled'
        startDate: formatDateForAPI(startDate),
        endDate: formatDateForAPI(endDate),
        page,
        pageSize,
    });

    const records: RequestData[] = listData?.data?.data || [];
    const totalRecords = listData?.data?.totalData || 0;

    const unitOptionsFormatted = isPettyCashOfficer
        ? (myUnit?.data ? [{ value: String(myUnit.data.unitId), label: myUnit.data.unitName }] : [])
        : [
            { value: '', label: 'Semua Unit' },
            ...(unitOptions?.data || []).map((u: any) => ({ value: String(u.value), label: u.label })),
        ];

    const handleDateChange = (dates: Date[]) => {
        if (dates.length >= 1) setStartDate(dates[0]);
        if (dates.length >= 2) setEndDate(dates[1]);
        if (dates.length === 0) {
            setStartDate(null);
            setEndDate(null);
        }
    };

    const handleSettle = (request: RequestData) => {
        // Navigate to settlement form with request ID
        navigate(`/petty-cash/settlement/create?request_id=${request.requestId}`);
    };

    const handleViewSettlement = (request: RequestData) => {
        if (request.settlementId) {
            navigate(`/petty-cash/settlement/${request.settlementId}`);
        }
    };

    const getSettlementStatusBadge = (row: RequestData) => {
        if (row.settlementId) {
            return (
                <span className="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 flex items-center gap-1 w-fit">
                    <CheckCircle className="h-3 w-3" />
                    Sudah Settlement
                </span>
            );
        }
        return (
            <span className="px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700 flex items-center gap-1 w-fit">
                <Clock className="h-3 w-3" />
                Belum Settlement
            </span>
        );
    };

    const columns: DataTableColumn<RequestData>[] = [
        {
            accessor: 'requestNo',
            title: 'No. Permintaan',
            width: 180,
        },
        {
            accessor: 'requestDate',
            title: 'Tanggal',
            width: 120,
            render: (row) => FormatDate(row.requestDate),
        },
        {
            accessor: 'unitName',
            title: 'Unit',
            render: (row) => (
                <div className="flex flex-col">
                    <span className="font-medium">{row.unitName}</span>
                    <span className="text-xs text-gray-500">{row.unitCode}</span>
                </div>
            ),
        },
        {
            accessor: 'requesterName',
            title: 'Pemohon',
            width: 140,
        },
        {
            accessor: 'purpose',
            title: 'Keperluan',
            render: (row) => (
                <div className="max-w-[180px] truncate" title={row.purpose}>
                    {row.purpose}
                </div>
            ),
        },
        {
            accessor: 'approvedAmount',
            title: 'Jumlah',
            textAlignment: 'right' as const,
            render: (row) => (
                <span className="font-mono font-medium">
                    {FormatNumber(row.approvedAmount || row.amount)}
                </span>
            ),
        },
        {
            accessor: 'settlementId',
            title: 'Status Settlement',
            render: (row) => getSettlementStatusBadge(row),
        },
        {
            accessor: 'actions',
            title: 'Aksi',
            width: 140,
            render: (row) => (
                <div className="flex items-center gap-1">
                    {row.settlementId ? (
                        <Button
                            variant="outline"
                            size="sm"
                            className="gap-1"
                            onClick={() => handleViewSettlement(row)}
                        >
                            <FileText className="h-4 w-4" />
                            Lihat
                        </Button>
                    ) : (
                        <Button
                            variant="default"
                            size="sm"
                            className="gap-1"
                            onClick={() => handleSettle(row)}
                        >
                            <FileText className="h-4 w-4" />
                            Settlement
                        </Button>
                    )}
                </div>
            ),
        },
    ];

    return (
        <div className="space-y-6">
            <Card>
                <CardHeader className="border-b border-gray-100 pb-4">
                    <div className="flex flex-col gap-4">
                        <div className="flex items-center justify-between">
                            <div className="flex items-center gap-3">
                                <div className="p-2 rounded-lg bg-primary/10">
                                    <Receipt className="h-5 w-5 text-primary" />
                                </div>
                                <div>
                                    <CardTitle className="text-lg">{t('petty_cash.settlement.title', 'Settlement Kas Kecil')}</CardTitle>
                                    <p className="text-sm text-gray-500 mt-0.5">Daftar permintaan yang disetujui untuk di-settlement</p>
                                </div>
                            </div>
                        </div>

                        <div className="flex flex-wrap items-center gap-3">
                            <div className="w-48">
                                <SelectSearch
                                    placeholder="Unit"
                                    options={unitOptionsFormatted}
                                    onChange={(opt: any) => { setSelectedUnit(opt?.value || ''); setPage(1); }}
                                    value={unitOptionsFormatted.find((o) => o.value === selectedUnit) || null}
                                    isClearable={!isPettyCashOfficer}
                                    isDisabled={isPettyCashOfficer}
                                    styles={{
                                        control: (base) => ({
                                            ...base,
                                            minHeight: '40px',
                                            borderColor: '#e2e8f0',
                                            backgroundColor: isPettyCashOfficer ? '#f3f4f6' : '#ffffff'
                                        }),
                                        menuPortal: (base) => ({ ...base, zIndex: 9999 }),
                                    }}
                                    menuPortalTarget={document.body}
                                />
                            </div>

                            <div className="w-48">
                                <SelectSearch
                                    placeholder="Status Settlement"
                                    options={settlementStatusOptions}
                                    onChange={(opt: any) => { setSettlementStatus(opt?.value || ''); setPage(1); }}
                                    value={settlementStatusOptions.find((o) => o.value === settlementStatus) || null}
                                    isClearable
                                    styles={{
                                        control: (base) => ({
                                            ...base,
                                            minHeight: '40px',
                                            borderColor: '#e2e8f0',
                                        }),
                                        menuPortal: (base) => ({ ...base, zIndex: 9999 }),
                                    }}
                                    menuPortalTarget={document.body}
                                />
                            </div>

                            <div className="relative w-48">
                                <Flatpickr
                                    options={{ mode: 'range', dateFormat: 'd/m/Y' }}
                                    className="form-input w-full h-10 pl-9 border-slate-200"
                                    onChange={handleDateChange}
                                    placeholder="Periode"
                                />
                                <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <IconCalendar className="h-4 w-4 text-gray-400" />
                                </div>
                            </div>
                        </div>
                    </div>
                </CardHeader>

                <CardContent className="p-0">
                    <div className="datatables">
                        <DataTable
                            highlightOnHover
                            className="whitespace-nowrap table-hover"
                            records={records}
                            columns={columns}
                            totalRecords={totalRecords}
                            recordsPerPage={pageSize}
                            page={page}
                            onPageChange={setPage}
                            recordsPerPageOptions={PAGE_SIZES}
                            onRecordsPerPageChange={setPageSize}
                            minHeight={300}
                            fetching={isLoading}
                            loaderVariant="dots"
                            loaderColor="#4361ee"
                            noRecordsText="Tidak ada permintaan yang disetujui"
                            paginationText={({ from, to, totalRecords }) =>
                                `Menampilkan ${from} - ${to} dari ${totalRecords} data`
                            }
                            styles={{
                                root: { border: 'none', borderRadius: '0.75rem', overflow: 'hidden' },
                                pagination: { borderTop: '1px solid #f1f5f9', margin: '1rem' },
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
        </div>
    );
};

export default PettyCashSettlement;
