import IconPencil from '@/components/Icon/IconPencil';
import IconX from '@/components/Icon/IconX';
import { useGetOptionBankQuery } from '@/store/api/bank/bankApiSlice';
import { useGetDetailJournalQuery } from '@/store/api/journal/journalApiSlice';
import { formatCurrency } from '@/utils/currency';
import { pembilang } from '@/utils/currencyHelper';
import { formatDate } from '@/utils/formatDate';
import { useEffect, useState } from 'react';
import { useTranslation } from 'react-i18next';
import { useDispatch, useSelector } from 'react-redux';
import { useNavigate, useParams } from 'react-router-dom';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '../../../store/themeConfigSlice';

interface BankOption {
  desc: string;
  label: string;
}

interface DetailItem {
  description: string;
  amount: number;
  isPremier: boolean;
  coaCode: string;
  coaName: string;
  journalDesc: string;
  creditAmount: number;
  debitAmount: number;
}

const DaftarPengeluaranView = () => {
  const dispatch = useDispatch();
  const navigate = useNavigate();
  const { id } = useParams();
  const { t } = useTranslation();

  const { data: detailTrans, refetch: refetchdetailTrans } = useGetDetailJournalQuery<any>(
    id || ''
  );
  const { data: bankResponse } = useGetOptionBankQuery({ parent: 952 });
  const bankList: BankOption[] = bankResponse?.data ?? [];

  const [detailsData, setDetailsData] = useState<DetailItem[]>([]);

  useEffect(() => {
    dispatch(setPageTitle(t('pengeluaran.view_page.title')));
    dispatch(setTitle(t('pengeluaran.view_page.title')));
    dispatch(setBreadcrumbTitle(['Dashboard', 'Pengeluaran', 'Detail']));

    if (id) {
      refetchdetailTrans();
    }
  }, [dispatch, id, refetchdetailTrans]);

  useEffect(() => {
    if (detailTrans && detailTrans.data) {
      const data = detailTrans.data;
      // Set details dari API
      if (data.details && data.details.length > 0) {
        setDetailsData([...data.details].reverse());
      }
    }
  }, [detailTrans]);

  const getBankName = (coaCode: string) => {
    const bank = bankList.find((bank) => bank.desc === coaCode);
    return bank ? bank.label : coaCode;
  };

  const calculateTotal = () => {
    return detailsData.reduce(
      (sum, detail) => sum + (Math.max(detail.creditAmount, detail.debitAmount) || 0),
      0
    );
  };

  const total = calculateTotal();

  return (
    <div className="space-y-6">
      <div className="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
          <h1 className="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{t('pengeluaran.view_page.title')}</h1>
          <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">{t('pengeluaran.view_page.subtitle', { transactionNo: detailTrans?.data?.transactionNo || '...' })}</p>
        </div>
        <div className="flex gap-2">
          <button
            type="button"
            className="btn btn-outline-dark shadow-sm bg-white dark:bg-black"
            onClick={() => navigate('/daftar-Pengeluaran')}
          >
            <IconX className="w-4 h-4 ltr:mr-2 rtl:ml-2" />
            {t('pengeluaran.view_page.back')}
          </button>
          {['pending', 'Pending'].includes(detailTrans?.data?.status) && (
            <button
              type="button"
              className="btn btn-primary shadow-sm"
              onClick={() => navigate(`/daftar-Pengeluaran/update/${id}`)}
            >
              <IconPencil className="w-4 h-4 ltr:mr-2 rtl:ml-2" />
              {t('pengeluaran.view_page.edit')}
            </button>
          )}
        </div>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Left Column: Main Info */}
        <div className="lg:col-span-2 space-y-6">
          <div className="panel border-gray-200 dark:border-gray-800 shadow-sm">
            <div className="flex items-center justify-between mb-5">
              <h5 className="font-semibold text-lg dark:text-white-light">{t('pengeluaran.view_page.general_info')}</h5>
              <span className={`px-3 py-1 rounded-full text-sm font-medium ${detailTrans?.data?.status === 'Published' ? 'bg-success/10 text-success border border-success/20' :
                detailTrans?.data?.status === 'Void' ? 'bg-danger/10 text-danger border border-danger/20' :
                  'bg-warning/10 text-warning border border-warning/20'
                }`}>
                {detailTrans?.data?.status || 'Unknown'}
              </span>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label className="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-1">{t('pengeluaran.view_page.transaction_no')}</label>
                <div className="text-gray-900 dark:text-gray-200 font-medium text-base">
                  {detailTrans?.data?.transactionNo || '-'}
                </div>
              </div>
              <div>
                <label className="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-1">{t('pengeluaran.view_page.journal_no')}</label>
                <div className="text-gray-900 dark:text-gray-200 font-medium text-base">
                  {detailTrans?.data?.journalNo || '-'}
                </div>
              </div>
              <div>
                <label className="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-1">{t('pengeluaran.view_page.transaction_date')}</label>
                <div className="text-gray-900 dark:text-gray-200 font-medium text-base">
                  {detailTrans?.data?.transactionDate ? formatDate(detailTrans?.data?.transactionDate) : '-'}
                </div>
              </div>
              <div>
                <label className="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-1">{t('pengeluaran.view_page.source_account')}</label>
                <div className="text-gray-900 dark:text-gray-200 font-medium text-base">
                  {detailTrans?.data?.coaName || '-'}
                  <span className="text-xs text-gray-400 block font-normal">{detailTrans?.data?.coaCode}</span>
                </div>
              </div>
            </div>

            <div className="mt-6 pt-6 border-t border-gray-100 dark:border-gray-700">
              <label className="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-2 block">{t('pengeluaran.view_page.description')}</label>
              <p className="text-gray-700 dark:text-gray-300 leading-relaxed bg-gray-50 dark:bg-gray-900 p-4 rounded-lg text-sm">
                {detailTrans?.data?.description || t('pengeluaran.view_page.no_description')}
              </p>
            </div>
          </div>

          <div className="panel border-gray-200 dark:border-gray-800 shadow-sm p-0 overflow-hidden">
            <div className="p-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-[#1b2e4b]">
              <h5 className="font-semibold text-lg dark:text-white-light">{t('pengeluaran.view_page.detail_info')}</h5>
            </div>
            <div className="table-responsive">
              <table className="table-hover w-full text-left text-sm">
                <thead className="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                  <tr>
                    <th className="px-5 py-3 font-semibold">{t('pengeluaran.view_page.account_name')}</th>
                    <th className="px-5 py-3 font-semibold">{t('pengeluaran.view_page.item_description')}</th>
                    <th className="px-5 py-3 font-semibold text-right">{t('pengeluaran.view_page.amount')}</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-gray-100 dark:divide-gray-700">
                  {detailsData.length > 0 ? (
                    detailsData.map((detail, index) => (
                      <tr key={index} className="hover:bg-gray-50/50 dark:hover:bg-gray-800/50">
                        <td className="px-5 py-3">
                          <div className="font-medium text-gray-900 dark:text-gray-100">{detail.coaName || 'N/A'}</div>
                          <div className="text-xs text-gray-500">{detail.coaCode}</div>
                        </td>
                        <td className="px-5 py-3 text-gray-600 dark:text-gray-400">{detail.journalDesc || '-'}</td>
                        <td className="px-5 py-3 text-right font-mono font-medium text-gray-900 dark:text-gray-100">
                          {formatCurrency(Math.max(detail.creditAmount, detail.debitAmount) || 0)}
                        </td>
                      </tr>
                    ))
                  ) : (
                    <tr>
                      <td colSpan={3} className="px-5 py-8 text-center text-gray-500">
                        {t('pengeluaran.view_page.no_data')}
                      </td>
                    </tr>
                  )}
                </tbody>
                <tfoot className="bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
                  <tr>
                    <td colSpan={2} className="px-5 py-3 text-right font-bold text-gray-700 dark:text-gray-200 uppercase text-xs tracking-wider">{t('pengeluaran.view_page.total')}</td>
                    <td className="px-5 py-3 text-right font-bold font-mono text-lg text-primary">
                      {formatCurrency(total)}
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
            <h5 className="font-semibold text-lg text-primary mb-4">{t('pengeluaran.view_page.total_summary')}</h5>
            <div className="flex flex-col gap-1">
              <span className="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">
                {formatCurrency(total)}
              </span>
              <span className="text-sm text-gray-500 dark:text-gray-400 italic mt-2">
                "{pembilang(total)} rupiah"
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default DaftarPengeluaranView;
