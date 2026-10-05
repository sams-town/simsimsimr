import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { useGetDetailJournalQuery } from '@/store/api/journal/journalApiSlice';
import { formatCurrency } from '@/utils/currency';
import { pembilang } from '@/utils/currencyHelper';
import { formatDate } from '@/utils/formatDate';
import { ArrowLeft, Pencil } from 'lucide-react';
import { useEffect } from 'react';
import { useTranslation } from 'react-i18next';
import { useDispatch, useSelector } from 'react-redux';
import { useLocation, useNavigate, useParams } from 'react-router-dom';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '../../../store/themeConfigSlice';

const DaftarTransferView = () => {
  const dispatch = useDispatch();
  const location = useLocation();
  const navigate = useNavigate();
  const { id } = useParams();
  const pathSegments = location.pathname.split('/');
  const lastSegment = pathSegments[pathSegments.length - 1];
  const { t } = useTranslation();
  const isRtl = useSelector((state: any) => state.themeConfig.rtlClass) === 'rtl';

  const { data: detailDaftarTransfer, refetch: refetchDaftarTransfer } =
    useGetDetailJournalQuery<any>(id || '');

  useEffect(() => {
    dispatch(setPageTitle('Detail Daftar Transfer'));
    dispatch(setTitle('Detail Daftar Transfer'));
    dispatch(
      setBreadcrumbTitle(['Dashboard', t('menu.account_book'), t('buku_kas.title'), t('buku_kas.view_page.title'), lastSegment])
    );

    if (id) {
      refetchDaftarTransfer();
    }
  }, [dispatch, id, refetchDaftarTransfer, lastSegment]);

  const statusClass: any = {
    Void: 'bg-red-100 text-red-700',
    Published: 'bg-green-100 text-green-700',
    Pending: 'bg-gray-100 text-gray-700',
  };

  return (
    <div className="flex flex-col gap-6 max-w-5xl mx-auto">
      {/* Header / Title */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
            {t('buku_kas.view_page.title')}
          </h1>
          <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
            {t('buku_kas.view_page.subtitle')}
          </p>
        </div>
        <div className="flex items-center gap-2">
          <Button
            type="button"
            variant="outline"
            onClick={() => navigate('/daftar-transfer')}
            className="gap-2 shadow-sm bg-white dark:bg-black"
            size="sm"
          >
            <ArrowLeft className="h-4 w-4" />
            {t('buku_kas.view_page.back')}
          </Button>
          {['pending', 'Pending'].includes(detailDaftarTransfer?.data?.status) && (
            <Button
              type="button"
              onClick={() => navigate(`/daftar-transfer/update/${id}`)}
              className="gap-2"
              size="sm"
            >
              <Pencil className="h-4 w-4" />
              {t('buku_kas.view_page.edit')}
            </Button>
          )}
        </div>
      </div>

      <Card className="shadow-sm border border-gray-200 dark:border-gray-800">
        <CardHeader className="bg-gray-50/50 dark:bg-[#1b2e4b] border-b border-gray-100 dark:border-gray-700 px-6 py-4">
          <CardTitle className="text-base font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
            <span className="bg-primary/10 text-primary w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold">1</span>
            {t('buku_kas.view_page.transaction_info')}
          </CardTitle>
        </CardHeader>
        <CardContent className="p-6">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">

            {/* Status */}
            <div className="md:col-span-2">
              <span className={`px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider ${statusClass[detailDaftarTransfer?.data?.status] || 'bg-gray-100 text-gray-800'}`}>
                {detailDaftarTransfer?.data?.status || 'Unknown'}
              </span>
            </div>

            {/* Transaction No */}
            <div className="space-y-1">
              <p className="text-sm font-medium text-gray-500">{t('buku_kas.view_page.transaction_no')}</p>
              <p className="text-base font-semibold text-gray-900">{detailDaftarTransfer?.data?.transactionNo || '-'}</p>
            </div>

            {/* Transaction Date */}
            <div className="space-y-1">
              <p className="text-sm font-medium text-gray-500">{t('buku_kas.view_page.transaction_date')}</p>
              <p className="text-base font-semibold text-gray-900">{formatDate(detailDaftarTransfer?.data?.transactionDate)}</p>
            </div>

            <div className="border-t border-gray-100 md:col-span-2 my-2"></div>

            {/* From Account */}
            <div className="space-y-1">
              <p className="text-sm font-medium text-gray-500">{t('buku_kas.view_page.from_account')}</p>
              <p className="text-base font-semibold text-gray-900">{detailDaftarTransfer?.data?.coaName || '-'}</p>
            </div>

            {/* To Account */}
            <div className="space-y-1">
              <p className="text-sm font-medium text-gray-500">{t('buku_kas.view_page.to_account')}</p>
              <p className="text-base font-semibold text-gray-900">{detailDaftarTransfer?.data?.details?.[0]?.coaName || '-'}</p>
            </div>

            <div className="border-t border-gray-100 md:col-span-2 my-2"></div>

            {/* Amount */}
            <div className="space-y-1">
              <p className="text-sm font-medium text-gray-500">{t('buku_kas.view_page.amount')}</p>
              <p className="text-xl font-bold text-gray-900 font-mono">{formatCurrency(detailDaftarTransfer?.data?.amount)}</p>
              <p className="text-sm text-gray-500 italic mt-1 bg-gray-50 p-2 rounded-md inline-block">
                {pembilang(detailDaftarTransfer?.data?.amount || 0)} rupiah
              </p>
            </div>

            {/* Description */}
            <div className="space-y-1">
              <p className="text-sm font-medium text-gray-500">{t('buku_kas.view_page.description')}</p>
              <p className="text-base text-gray-900">{detailDaftarTransfer?.data?.description || '-'}</p>
            </div>

          </div>
        </CardContent>
      </Card>
    </div>
  );
};

export default DaftarTransferView;
