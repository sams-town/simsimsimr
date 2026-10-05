import { Dialog, Transition } from '@headlessui/react';
import { Fragment, useEffect, useState } from 'react';
import { useSelector } from 'react-redux';
import { DataTable, DataTableSortStatus } from 'mantine-datatable';
import { useGetCOAQuery } from '@/store/api/coa/coaApiSlice';
import '@/pages/Master/Coa/index.css';
import { COAType } from '@/types';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Search, X } from 'lucide-react';
import { useDebounce } from '@/hooks/useDebounce';
import './index.css';

const ModalCoaCustom = ({
    setIsSave,
    selectedRecords,
    setSelectedRecords,
    showModal,
    setShowSelected,
    setIsShowModal,
    excludeId
}: {
    setIsSave: any,
    selectedRecords: any,
    setSelectedRecords: any,
    setShowSelected: any,
    showModal: boolean,
    setIsShowModal: any,
    excludeId: any
}) => {
    const isRtl = useSelector((state: any) => state.themeConfig.rtlClass) === 'rtl';
    const [page, setPage] = useState<number>(1);
    const PAGE_SIZES: number[] = [10, 20, 30, 50, 100];
    const [pageSize, setPageSize] = useState<number>(PAGE_SIZES[0]);

    // Search & Debounce
    const [search, setSearch] = useState<string>('');
    const [debouncedSearch] = useDebounce(search, 500); // 500ms delay

    const [sortStatus, setSortStatus] = useState<DataTableSortStatus>({ columnAccessor: 'coaCode', direction: 'asc' });

    const {
        data: CoAList,
        refetch,
        isLoading,
    } = useGetCOAQuery<any>({
        keyword: debouncedSearch,
        orderBy: sortStatus.columnAccessor === 'coaCode' ? 'coaCode' : sortStatus.columnAccessor,
        orderType: sortStatus.direction,
        page: page,
        pageSize: pageSize,
    });

    useEffect(() => {
        refetch();
    }, [page, sortStatus, debouncedSearch, pageSize, refetch]);

    // Reset page to 1 when search terms change
    useEffect(() => {
        setPage(1);
    }, [debouncedSearch, pageSize, sortStatus]);

    const handleButtonSave = () => {
        setShowSelected(selectedRecords);
        setIsSave(true);
        setIsShowModal(false);
    };

    const handleRowClick = (record: COAType) => {
        // Prevent selection of Header types or excluded IDs
        if (record.accountTypeName === "Header" || record.coaCode === excludeId) return;

        const isSelected = selectedRecords.some((r: COAType) => r.coaCode === record.coaCode);
        let newSelectedRecords;

        if (isSelected) {
            newSelectedRecords = selectedRecords.filter((r: COAType) => r.coaCode !== record.coaCode);
        } else {
            // Add to selection
            newSelectedRecords = [...selectedRecords, record];
        }
        setSelectedRecords(newSelectedRecords);
    };

    return (
        <Transition appear show={showModal} as={Fragment}>
            <Dialog as="div" open={showModal} onClose={() => setIsShowModal(false)}>
                <Transition.Child
                    as={Fragment}
                    enter="ease-out duration-300"
                    enterFrom="opacity-0"
                    enterTo="opacity-100"
                    leave="ease-in duration-200"
                    leaveFrom="opacity-100"
                    leaveTo="opacity-0"
                >
                    <div className="fixed inset-0 bg-black/60 z-[999]" />
                </Transition.Child>

                <div className="fixed inset-0 z-[999] overflow-y-auto">
                    <div className="flex items-center justify-center min-h-screen px-4">
                        <Dialog.Panel className="panel border-0 p-0 rounded-lg overflow-hidden w-full max-w-5xl my-8 text-black dark:text-white-dark bg-white dark:bg-[#1b2e4b] shadow-2xl animate__animated animate__fadeIn">
                            {/* Header */}
                            <div className="flex bg-[#fbfbfb] dark:bg-[#121c2c] items-center justify-between px-5 py-3 border-b border-gray-100 dark:border-gray-700">
                                <h5 className="font-bold text-lg">Chart Of Account</h5>
                                <button onClick={() => setIsShowModal(false)} type="button" className="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                    <X className="h-5 w-5" />
                                </button>
                            </div>

                            <div className="p-5">
                                {/* Search */}
                                <div className="mb-5 relative max-w-sm">
                                    <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" />
                                    <Input
                                        type="text"
                                        className="pl-9"
                                        placeholder="Cari akun..."
                                        value={search}
                                        onChange={(e) => setSearch(e.target.value)}
                                        autoFocus
                                    />
                                </div>

                                {/* Table */}
                                <div className="datatables z-0 rounded-lg border border-gray-100 overflow-hidden">
                                    <DataTable
                                        idAccessor="coaCode" // Explicitly set ID accessor
                                        highlightOnHover
                                        className={`${isRtl ? 'whitespace-nowrap table-hover' : 'whitespace-nowrap table-hover'} cursor-pointer`}
                                        records={CoAList?.data?.data}
                                        columns={[
                                            {
                                                accessor: 'coaCode',
                                                title: 'KODE',
                                                sortable: true,
                                                render: (row: COAType) => (
                                                    <span className={row.accountTypeName === "Header" ? 'font-bold' : ''}>
                                                        {row.coaCode}
                                                    </span>
                                                )
                                            },
                                            {
                                                accessor: 'coaName',
                                                title: 'NAMA AKUN',
                                                sortable: true,
                                                render: (row: COAType) => (
                                                    <span className={row.accountTypeName === "Header" ? 'font-bold' : ''}>
                                                        {row.coaName}
                                                    </span>
                                                )
                                            },
                                            {
                                                accessor: 'accountTypeName',
                                                title: 'KATEGORI',
                                                sortable: true,
                                                render: (row: COAType) => (
                                                    <span className={`px-2 py-1 rounded-full text-xs font-semibold ${row.accountTypeName === 'Header'
                                                        ? 'bg-blue-50 text-blue-600'
                                                        : 'bg-gray-100 text-gray-500'
                                                        }`}>
                                                        {row.accountTypeName}
                                                    </span>
                                                )
                                            },
                                        ]}
                                        recordsPerPage={pageSize}
                                        page={page}
                                        onPageChange={(p) => setPage(p)}
                                        recordsPerPageOptions={PAGE_SIZES}
                                        onRecordsPerPageChange={setPageSize}
                                        sortStatus={sortStatus}
                                        onSortStatusChange={setSortStatus}
                                        selectedRecords={selectedRecords}
                                        onSelectedRecordsChange={setSelectedRecords}
                                        onRowClick={(record) => handleRowClick(record)}
                                        totalRecords={CoAList?.data?.totalData}
                                        isRecordSelectable={(record) => record.accountTypeName !== "Header" && record.coaCode !== excludeId}
                                        fetching={isLoading}
                                        loaderVariant="dots"
                                        loaderColor="#648de1"
                                        minHeight={200}
                                        paginationText={({ from, to, totalRecords }) => `Showing ${from} to ${to} of ${totalRecords} entries`}
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

                                {/* Footer Actions */}
                                <div className="flex justify-end items-center mt-6 gap-3">
                                    <Button
                                        variant="outline"
                                        onClick={() => setIsShowModal(false)}
                                    >
                                        Cancel
                                    </Button>
                                    <Button
                                        onClick={handleButtonSave}
                                        className="bg-primary text-white"
                                    >
                                        Select ({selectedRecords.length})
                                    </Button>
                                </div>
                            </div>
                        </Dialog.Panel>
                    </div>
                </div>
            </Dialog>
        </Transition>
    );
};

export default ModalCoaCustom;