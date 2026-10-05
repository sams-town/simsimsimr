import IconPencil from '@/components/Icon/IconPencil';
import IconX from '@/components/Icon/IconX';
import { useGetGeneralJournalDetailQuery } from '@/store/api/journal/generalJournalApiSlice';
import { formatCurrency } from '@/utils/currency';
import { pembilang } from '@/utils/currencyHelper';
import { formatDate } from '@/utils/formatDate';
import { useTranslation } from 'react-i18next';
import { useEffect, useState } from 'react';
import { useDispatch } from 'react-redux';
import { useNavigate, useParams } from 'react-router-dom';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '../../../store/themeConfigSlice';

interface DetailItem {
    journalId: number;
    coaCode: string;
    coaName: string;
    journalDesc: string;
    debitAmount: number;
    creditAmount: number;
}

const JurnalUmumView = () => {
    const dispatch = useDispatch();
    const navigate = useNavigate();
    const { t } = useTranslation();
    const { id } = useParams();

    const { data: detailTrans, refetch: refetchdetailTrans } = useGetGeneralJournalDetailQuery<any>(
        id || ''
    );

    const [detailsData, setDetailsData] = useState<DetailItem[]>([]);

    useEffect(() => {
        dispatch(setPageTitle(t('transaction.general_journal_page.view.title')));
        dispatch(setTitle(t('transaction.general_journal_page.view.title')));
        dispatch(setBreadcrumbTitle([t('menu.dashboard'), t('menu.journal'), t('transaction.general_journal_page.title'), t('common.view')]));

        if (id) {
            refetchdetailTrans();
        }
    }, [dispatch, id, refetchdetailTrans]);

    useEffect(() => {
        if (detailTrans && detailTrans.data) {
            const data = detailTrans.data;
            if (data.details && data.details.length > 0) {
                setDetailsData(data.details);
            }
        }
    }, [detailTrans]);

    const calculateTotals = () => {
        const totalDebit = detailsData.reduce((sum, detail) => sum + (detail.debitAmount || 0), 0);
        const totalCredit = detailsData.reduce((sum, detail) => sum + (detail.creditAmount || 0), 0);
        return { totalDebit, totalCredit };
    };

    const { totalDebit, totalCredit } = calculateTotals();

    return (
        <div className="space-y-6">
            <div className="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">

                <div>
                    <h1 className="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{t('transaction.general_journal_page.view.title')}</h1>
                    <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">{t('transaction.general_journal_page.view.subtitle', { no: detailTrans?.data?.transactionNo || '...' })}</p>
                </div>
                <div className="flex gap-2">
                    <button
                        type="button"
                        className="btn btn-outline-dark shadow-sm bg-white dark:bg-black"
                        onClick={() => navigate('/jurnal-umum')}
                    >
                        <IconX className="w-4 h-4 ltr:mr-2 rtl:ml-2" />
                        {t('common.back')}
                    </button>
                    {['pending', 'Pending'].includes(detailTrans?.data?.status) && (
                        <button
                            type="button"
                            className="btn btn-primary shadow-sm"
                            onClick={() => navigate(`/jurnal-umum/update/${id}`)}
                        >
                            <IconPencil className="w-4 h-4 ltr:mr-2 rtl:ml-2" />
                            {t('transaction.general_journal_page.form.update_journal')}
                        </button>
                    )}
                </div>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {/* Left Column: Main Info */}
                <div className="lg:col-span-2 space-y-6">
                    <div className="panel border-gray-200 dark:border-gray-800 shadow-sm">
                        <div className="flex items-center justify-between mb-5">
                            <h5 className="font-semibold text-lg dark:text-white-light">{t('transaction.general_journal_page.view.general_info')}</h5>
                            <span className={`px-3 py-1 rounded-full text-sm font-medium ${detailTrans?.data?.status === 'Published' ? 'bg-success/10 text-success border border-success/20' :
                                detailTrans?.data?.status === 'Void' ? 'bg-danger/10 text-danger border border-danger/20' :
                                    'bg-warning/10 text-warning border border-warning/20'
                                }`}>
                                {detailTrans?.data?.status}
                            </span>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label className="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-1">{t('transaction.general_journal_page.view.transaction_no')}</label>
                                <div className="text-gray-900 dark:text-gray-200 font-medium text-base">
                                    {detailTrans?.data?.transactionNo || '-'}
                                </div>
                            </div>
                            <div>
                                <label className="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-1">{t('transaction.general_journal_page.view.journal_no')}</label>
                                <div className="text-gray-900 dark:text-gray-200 font-medium text-base">
                                    {detailTrans?.data?.journalNo || '-'}
                                </div>
                            </div>
                            <div>
                                <label className="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-1">{t('transaction.general_journal_page.view.date')}</label>
                                <div className="text-gray-900 dark:text-gray-200 font-medium text-base">
                                    {detailTrans?.data?.transactionDate ? formatDate(detailTrans?.data?.transactionDate) : '-'}
                                </div>
                            </div>
                            <div>
                                <label className="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-1">{t('transaction.general_journal_page.view.type')}</label>
                                <div className="text-gray-900 dark:text-gray-200 font-medium text-base">
                                    {t('menu.general_journal')}
                                </div>
                            </div>
                        </div>

                        <div className="mt-6 pt-6 border-t border-gray-100 dark:border-gray-700">
                            <label className="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-2 block">{t('transaction.general_journal_page.view.description')}</label>
                            <p className="text-gray-700 dark:text-gray-300 leading-relaxed bg-gray-50 dark:bg-gray-900 p-4 rounded-lg text-sm">
                                {detailTrans?.data?.description || t('transaction.general_journal_page.view.no_description')}
                            </p>
                        </div>
                    </div>

                    <div className="panel border-gray-200 dark:border-gray-800 shadow-sm p-0 overflow-hidden">
                        <div className="p-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-[#1b2e4b]">
                            <h5 className="font-semibold text-lg dark:text-white-light">{t('transaction.general_journal_page.view.journal_details')}</h5>
                        </div>
                        <div className="table-responsive">
                            <table className="table-hover w-full text-left text-sm">
                                <thead className="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                                    <tr>
                                        <th className="px-5 py-3 font-semibold">{t('transaction.general_journal_page.view.account_name')}</th>
                                        <th className="px-5 py-3 font-semibold">{t('transaction.general_journal_page.view.line_desc')}</th>
                                        <th className="px-5 py-3 font-semibold text-right">{t('transaction.general_journal_page.view.debit')}</th>
                                        <th className="px-5 py-3 font-semibold text-right">{t('transaction.general_journal_page.view.credit')}</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-100 dark:divide-gray-700">
                                    {detailsData.length > 0 ? (
                                        [...detailsData].sort((a, b) => {
                                            const aIsDebit = a.debitAmount > 0 ? 0 : 1;
                                            const bIsDebit = b.debitAmount > 0 ? 0 : 1;
                                            return aIsDebit - bIsDebit;
                                        }).map((detail, index) => (
                                            <tr key={index} className="hover:bg-gray-50/50 dark:hover:bg-gray-800/50">
                                                <td className="px-5 py-3">
                                                    <div className="font-medium text-gray-900 dark:text-gray-100">{detail.coaName || 'N/A'}</div>
                                                    <div className="text-xs text-gray-500">{detail.coaCode}</div>
                                                </td>
                                                <td className="px-5 py-3 text-gray-600 dark:text-gray-400">{detail.journalDesc || '-'}</td>
                                                <td className="px-5 py-3 text-right font-mono font-medium text-gray-900 dark:text-gray-100">
                                                    {detail.debitAmount > 0 ? formatCurrency(detail.debitAmount) : '-'}
                                                </td>
                                                <td className="px-5 py-3 text-right font-mono font-medium text-gray-900 dark:text-gray-100">
                                                    {detail.creditAmount > 0 ? formatCurrency(detail.creditAmount) : '-'}
                                                </td>
                                            </tr>
                                        ))
                                    ) : (
                                        <tr>
                                            <td colSpan={4} className="px-5 py-8 text-center text-gray-500">
                                                {t('common.no_data_available')}
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                                <tfoot className="bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
                                    <tr>
                                        <td colSpan={2} className="px-5 py-3 text-right font-bold text-gray-700 dark:text-gray-200 uppercase text-xs tracking-wider">{t('transaction.general_journal_page.view.total')}</td>
                                        <td className="px-5 py-3 text-right font-bold font-mono text-lg text-primary">
                                            {formatCurrency(totalDebit)}
                                        </td>
                                        <td className="px-5 py-3 text-right font-bold font-mono text-lg text-primary">
                                            {formatCurrency(totalCredit)}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                {/* Right Column: Summary / Metadata */}
                <div className="lg:col-span-1 space-y-6">
                    <div className="panel border-gray-200 dark:border-gray-800 shadow-sm bg-gradient-to-br from-primary/5 to-transparent">
                        <h5 className="font-semibold text-lg text-primary mb-4">{t('transaction.general_journal_page.view.summary')}</h5>
                        <div className="flex flex-col gap-4">
                            <div>
                                <span className="text-xs text-gray-500 uppercase tracking-wide">{t('transaction.general_journal_page.view.total_debit')}</span>
                                <span className="text-2xl font-bold text-gray-900 dark:text-white tracking-tight block">
                                    {formatCurrency(totalDebit)}
                                </span>
                            </div>
                            <div>
                                <span className="text-xs text-gray-500 uppercase tracking-wide">{t('transaction.general_journal_page.view.total_credit')}</span>
                                <span className="text-2xl font-bold text-gray-900 dark:text-white tracking-tight block">
                                    {formatCurrency(totalCredit)}
                                </span>
                            </div>
                            <div className="pt-4 border-t border-gray-100 dark:border-gray-700">
                                <span className={`text-sm font-medium ${totalDebit === totalCredit ? 'text-success' : 'text-danger'}`}>
                                    {totalDebit === totalCredit ? t('transaction.general_journal_page.view.balanced') : t('transaction.general_journal_page.view.unbalanced')}
                                </span>
                            </div>
                            <div className="pt-2">
                                <span className="text-sm text-gray-500 dark:text-gray-400 italic">
                                    "{pembilang(totalDebit)} rupiah"
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};


export default JurnalUmumView;
