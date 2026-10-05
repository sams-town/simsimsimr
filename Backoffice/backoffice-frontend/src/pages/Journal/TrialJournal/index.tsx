import React, { useState, useEffect } from 'react';
import { DataTableSortStatus } from 'mantine-datatable';
import { Pagination } from '@mantine/core';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '../../../store/themeConfigSlice';
import { useTranslation } from 'react-i18next';
import 'tippy.js/dist/tippy.css';
import IconPlus from '@/components/Icon/IconPlus';
import { useDispatch, useSelector } from 'react-redux';
import { useNavigate } from 'react-router-dom';
import { responseCallback } from '@/utils/responseCallback';
import { toastMessage } from '@/utils/toastUtils';
import { useGetTransactionJournalQuery, useDeleteTransactionJournalMutation, useUpdateStatusTransactionJournalMutation, useExportTransactionJournalMutation, useExportTransactionJournalFlatMutation } from '@/store/api/daftarTransfer/daftarTransferApiSlice';
import { TransactionJournalType, TransactionDetail } from '@/types/transactionJournalType';
import Flatpickr from 'react-flatpickr';
import 'flatpickr/dist/flatpickr.css';
import './index.css'
import IconDownload from '@/components/Icon/IconDownload';
import { FormatNumber } from '@/utils/formatNumber';
import { FormatDateShort } from '@/utils/formatDate';
import ConfirmationModal from '@/components/ConfirmationModal';
import IconCalendar from '@/components/Icon/IconCalendar';
import IconSearch from '@/components/Icon/IconSearch';

const Index = () => {
    const dispatch = useDispatch();
    const navigate = useNavigate();
    const user = useSelector((state: any) => state.auth.user);

    const { t } = useTranslation();

    useEffect(() => {
        dispatch(setPageTitle(t('journal.trial_journal.title')));
        dispatch(setTitle(t('journal.trial_journal.title')));
        dispatch(setBreadcrumbTitle(["Dashboard", t('journal.trial_journal.title'), t('journal.trial_journal.list_title')]));
    }, [dispatch, t]);

    const isRtl = useSelector((state: any) => state.themeConfig.rtlClass) === 'rtl';
    const [page, setPage] = useState<number>(1);
    const PAGE_SIZES: number[] = [10, 20, 30, 50, 100];
    const [pageSize, setPageSize] = useState<number>(PAGE_SIZES[0]);
    const [search, setSearch] = useState<string>('');
    const [status, setStatus] = useState<string>('Pending');
    const [sortStatus, setSortStatus] = useState<DataTableSortStatus>({ columnAccessor: 'createdDate', direction: 'desc' });
    const [showConfirmModal, setShowConfirmModal] = useState(false);
    const [selectedData, setSelecteData] = useState<TransactionJournalType>();
    const today = new Date();
    const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);

    const toUtcMidnight = (date: Date) =>
        new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate(), 0, 0, 0));

    const [startDate, setStartDate] = useState<any>(toUtcMidnight(firstDayOfMonth).toISOString());
    const [endDate, setEndDate] = useState<any>(toUtcMidnight(today).toISOString());

    const {
        data: bookBankList,
        refetch,
        isLoading,
    } = useGetTransactionJournalQuery({
        orderBy: sortStatus.columnAccessor,
        orderType: sortStatus.direction,
        page,
        pageSize,
        keyword: search,
        status,
        startDate,
        endDate,
    });

    const [updateStatusTransactionJournalMutation] = useUpdateStatusTransactionJournalMutation();
    const [exportTransactionJournal, { isLoading: isExportingBlock }] = useExportTransactionJournalMutation();
    const [exportTransactionJournalFlat, { isLoading: isExportingFlat }] = useExportTransactionJournalFlatMutation();

    const handleUpdateStatus = async () => {
        try {
            let request = { transactionId: selectedData?.transactionId, status: "Published" }
            const response = await updateStatusTransactionJournalMutation(request);
            responseCallback(response, null, null);
            setPage(1);
            refetch();
        } catch (err) {
            toastMessage(t('journal.trial_journal.toast.update_failed'), 'error');
        }
    };



    useEffect(() => {
        setPage(1);
    }, [sortStatus, search, pageSize, startDate, endDate]);

    return (
        <div className=''>
            <div className="panel">
                <div className="flex md:items-center md:flex-row flex-col mb-2 gap-5">
                    <div className="rtl:ml-auto rtl:mr-auto w-full md:w-auto">
                        <div className="grid grid-cols-1 md:grid-cols-4 gap-2 w-full">
                            <div className="relative">
                                <input
                                    type="text"
                                    className="form-input ltr:pl-4 rtl:pr-4 ltr:pr-9 rtl:pl-9 peer sm:bg-transparent bg-gray-100 placeholder:tracking-widest"
                                    placeholder={t('journal.trial_journal.search_placeholder')}
                                    value={search}
                                    onChange={(e) => setSearch(e.target.value)}
                                />
                                <button
                                    type="button"
                                    className="absolute inset-y-0 right-0 w-9 h-9 flex items-center justify-center peer-focus:text-primary"
                                >
                                    <IconSearch className="mx-auto" />
                                </button>
                            </div>
                            <div className="relative w-[230px]">
                                <Flatpickr
                                    options={{
                                        mode: "range",
                                        dateFormat: "d/m/Y",
                                        enableTime: false,
                                        position: isRtl ? "auto right" : "auto left",
                                        defaultDate: [toUtcMidnight(firstDayOfMonth), toUtcMidnight(today)],
                                    }}
                                    className="form-input font-normal w-full pr-10" // kasih padding kanan biar teks tidak ketimpa icon
                                    onChange={(dates: Date[]) => {
                                        if (dates.length === 2) {
                                            setStartDate(toUtcMidnight(dates[0]).toISOString());
                                            setEndDate(toUtcMidnight(dates[1]).toISOString());
                                        }
                                    }}
                                    placeholder={t('journal.trial_journal.select_period')}
                                />
                                <IconCalendar className="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-500 pointer-events-none" />

                            </div>
                            <div className="flex items-center gap-2">
                                <button
                                    type="button"
                                    className="btn btn-primary flex items-center whitespace-nowrap"
                                    disabled={isExportingBlock}
                                    onClick={async () => {
                                        try {
                                            const response: any = await exportTransactionJournal({
                                                keyword: search,
                                                status: status === 'All' ? '' : status,
                                                startDate: startDate,
                                                endDate: endDate
                                            });

                                            if (response.data) {
                                                const url = window.URL.createObjectURL(new Blob([response.data]));
                                                const link = document.createElement('a');
                                                link.href = url;
                                                link.setAttribute('download', 'TrialJournal.xlsx');
                                                document.body.appendChild(link);
                                                link.click();
                                                link.remove();
                                            }
                                        } catch (e) {
                                            toastMessage(t('journal.trial_journal.toast.export_failed'), 'error');
                                        }
                                    }}
                                >
                                    {isExportingBlock ? <svg className="animate-spin w-4 h-4 ltr:mr-1.5 rtl:ml-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle><path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> : <IconDownload className="w-4 h-4 ltr:mr-1.5 rtl:ml-1.5" />}
                                    {isExportingBlock ? t('common.loading') || 'Loading...' : t('journal.trial_journal.export_block')}
                                </button>
                                <button
                                    type="button"
                                    className="btn btn-primary flex items-center whitespace-nowrap"
                                    disabled={isExportingFlat}
                                    onClick={async () => {
                                        try {
                                            const response: any = await exportTransactionJournalFlat({
                                                keyword: search,
                                                status: status === 'All' ? '' : status,
                                                startDate: startDate,
                                                endDate: endDate
                                            });

                                            if (response.data) {
                                                const url = window.URL.createObjectURL(new Blob([response.data]));
                                                const link = document.createElement('a');
                                                link.href = url;
                                                link.setAttribute('download', 'TrialJournalFlat.xlsx');
                                                document.body.appendChild(link);
                                                link.click();
                                                link.remove();
                                            }
                                        } catch (e) {
                                            toastMessage(t('journal.trial_journal.toast.export_failed'), 'error');
                                        }
                                    }}
                                >
                                    {isExportingFlat ? <svg className="animate-spin w-4 h-4 ltr:mr-1.5 rtl:ml-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle><path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> : <IconDownload className="w-4 h-4 ltr:mr-1.5 rtl:ml-1.5" />}
                                    {isExportingFlat ? t('common.loading') || 'Loading...' : t('journal.trial_journal.export_list')}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {/* === Table custom tetap === */}
                <div className="datatables">
                    {bookBankList?.data?.data?.map((item: TransactionJournalType, index: number) => (
                        <div key={index} className="mb-6 border rounded-lg shadow-sm overflow-hidden bg-white dark:bg-[#1b2e4b] dark:border-[#191e3a]">
                            {/* Group Header: Transaction Info */}
                            {/* Group Header: Transaction Info */}
                            <div className="bg-gray-100 dark:bg-gray-800 p-4 border-b dark:border-[#191e3a] grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <div className="flex items-center gap-2 mb-1">
                                        <span className="text-primary font-bold text-lg">{item.journalNo}</span>
                                    </div>
                                    <div className="text-gray-500 text-sm">{FormatDateShort(item.transactionDate)}</div>
                                </div>
                                <div className="md:text-right flex flex-col justify-between">
                                    <div className="mb-2">
                                        <span className={`badge ${item.status === 'Published' ? 'bg-success' : 'bg-warning'}`}>
                                            {item.status === 'Published' ? t('journal.trial_journal.status.published') : (item.status === 'Pending' ? t('journal.trial_journal.status.pending') : item.status)}
                                        </span>
                                    </div>
                                    <div>
                                        <h4 className="font-semibold text-gray-800 dark:text-gray-200">{item.description}</h4>
                                        <div className="text-xs text-gray-500 mt-1">{t('journal.trial_journal.ref')} {item.transactionNo}</div>
                                    </div>
                                </div>
                            </div>

                            {/* Detail Table */}
                            <div className="table-responsive">
                                <table className="table-hover w-full text-left text-sm">
                                    <thead className="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 border-b dark:border-[#191e3a]">
                                        <tr>
                                            <th className="px-4 py-2 font-semibold">{t('journal.trial_journal.table.account_code')}</th>
                                            <th className="px-4 py-2 font-semibold">{t('journal.trial_journal.table.account_name')}</th>
                                            <th className="px-4 py-2 font-semibold">{t('journal.trial_journal.table.description')}</th>
                                            <th className="px-4 py-2 font-semibold text-right">{t('journal.trial_journal.table.debit')}</th>
                                            <th className="px-4 py-2 font-semibold text-right">{t('journal.trial_journal.table.credit')}</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y dark:divide-[#191e3a]">
                                        {item.details.map((details: TransactionDetail, key: number) => (
                                            <tr key={key} className="">
                                                <td className="px-4 py-2 font-medium">{details.coaCode}</td>
                                                <td className="px-4 py-2">{details.coaName}</td>
                                                <td className="px-4 py-2 text-gray-500">{details.journalDesc}</td>
                                                <td className="px-4 py-2 text-right font-mono">{details.debitAmount != null && details.debitAmount !== 0 ? FormatNumber(details.debitAmount) : '-'}</td>
                                                <td className="px-4 py-2 text-right font-mono">{details.creditAmount != null && details.creditAmount !== 0 ? FormatNumber(details.creditAmount) : '-'}</td>
                                            </tr>
                                        ))}
                                    </tbody>
                                    {/* Footer: Totals */}
                                    <tfoot className="bg-gray-50 dark:bg-gray-800 font-bold text-gray-700 dark:text-gray-200 border-t dark:border-[#191e3a]">
                                        <tr>
                                            <td colSpan={3} className="px-4 py-2 text-right uppercase">{t('journal.trial_journal.total')}</td>
                                            <td className="px-4 py-2 text-right font-mono">
                                                {FormatNumber(item.details.reduce((sum, d) => sum + (d.debitAmount ?? 0), 0))}
                                            </td>
                                            <td className="px-4 py-2 text-right font-mono">
                                                {FormatNumber(item.details.reduce((sum, d) => sum + (d.creditAmount ?? 0), 0))}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            {/* Actions (Posting Button) */}
                            {item.status !== 'Published' && user?.role?.roleName !== 'Staff' && (
                                <div className="p-3 bg-gray-50 dark:bg-gray-800 border-t dark:border-[#191e3a] flex justify-end">
                                    <button
                                        type="button"
                                        onClick={() => {
                                            setSelecteData(item);
                                            setShowConfirmModal(true);
                                        }}
                                        className="btn btn-primary btn-sm"
                                    >
                                        {t('journal.trial_journal.posting')}
                                    </button>
                                </div>
                            )}
                        </div>
                    ))}

                    {bookBankList?.data?.data?.length < 1 && (
                        <div className="flex justify-center items-center h-40 border rounded bg-gray-50">
                            <span className="text-gray-500 font-semibold">{t('journal.trial_journal.no_data')}</span>
                        </div>
                    )}
                </div>
                <div className="flex justify-end items-center mt-4">
                    <Pagination
                        // value={page}
                        onChange={setPage}
                        total={Math.ceil(bookBankList?.data?.totalData / 10) || 1}
                        size="sm"
                    />
                </div>
            </div >
            <ConfirmationModal
                isOpen={showConfirmModal}
                onClose={() => (!isLoading) && setShowConfirmModal(false)}
                onConfirm={async () => {
                    await handleUpdateStatus();
                }}
                title={t('journal.trial_journal.modal.confirm_title')}
                message={t('journal.trial_journal.modal.posting_confirm')}
                confirmText={t('journal.trial_journal.modal.posting_btn')}
                cancelText={t('journal.trial_journal.modal.cancel_btn')}
            />
        </div >
    );
};

export default Index;
