import ConfirmationModal from '@/components/ConfirmationModal';
import ModalCoaCustom from '@/components/ModalCoaCustom';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useGetOptionBankQuery } from '@/store/api/bank/bankApiSlice';
import {
  useCreateDepositMutation,
  useGetDepositDetailQuery,
  useUpdateDepositMutation,
} from '@/store/api/bank/deposit/depositApiSlice';
import { useGetDetailJournalQuery } from '@/store/api/journal/journalApiSlice';
import { COAType, OptionType } from '@/types';
import { DepositType, DepositUpdateType } from '@/types/depositType';
import { pembilang } from '@/utils/currencyHelper';
import { responseCallback } from '@/utils/responseCallback';
import { toastMessage } from '@/utils/toastUtils';
import { yupResolver } from '@hookform/resolvers/yup';
import Tippy from '@tippyjs/react';
import 'flatpickr/dist/flatpickr.css';
import { ArrowLeft, Plus, Save, Trash2 } from 'lucide-react';
import { useEffect, useState } from 'react';
import { useDebounce } from '@/hooks/useDebounce';
import Flatpickr from 'react-flatpickr';
import { FieldError, useFieldArray, useForm } from 'react-hook-form';
import { useTranslation } from 'react-i18next';
import { useDispatch, useSelector } from 'react-redux';
import { useLocation, useNavigate, useParams } from 'react-router-dom';
import SelectSearch from 'react-select';
import 'tippy.js/dist/tippy.css';
import * as yup from 'yup';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '../../../store/themeConfigSlice';

interface DetailItem {
  description: string;
  amount: number;
  isPremier: boolean;
  coaCode: string;
  coaName: string;
}

const DaftarPengeluaranForm = () => {
  const dispatch = useDispatch();
  const navigate = useNavigate();
  const { id } = useParams();
  const location = useLocation();
  const isViewMode = !!id && location.pathname.includes('/view/');
  const isUpdateMode = !!id && location.pathname.includes('/update/');
  const isRtl = useSelector((state: any) => state.themeConfig.rtlClass) === 'rtl';
  const { t } = useTranslation();

  const { data: detailTrans, refetch: refetchdetailTrans } = id
    ? useGetDetailJournalQuery<any>(id)
    : { data: null, refetch: () => { } };

  const [createDeposit, { isLoading: isCreating }] = useCreateDepositMutation();
  const [updateDeposit, { isLoading: isUpdating }] = useUpdateDepositMutation();

  const [transactionIdToUpdate, setTransactionIdToUpdate] = useState<string | null>(null);
  const [isShowModalCoa, setIsShowModalCoa] = useState<boolean>(false);
  const [selectedRecords, setSelectedRecords] = useState<COAType[]>([]);
  const [showSelected, setShowSelected] = useState<COAType[]>([]);
  const [excludeId, setExcludeId] = useState<any>('');
  const [isSave, setIsSave] = useState<boolean>(false);
  const [isLocked, setIsLocked] = useState<boolean>(false);
  const [statusMessage, setStatusMessage] = useState<string>('');
  const [showConfirmModal, setShowConfirmModal] = useState(false);
  const [detailsData, setDetailsData] = useState<DetailItem[]>([]);
  const [searchAccount, setSearchAccount] = useState<string>('');
  const [account, setAccount] = useState<OptionType>({
    label: t('pengeluaran.form_page.select_account'),
    value: '',
  });

  const schema = yup
    .object({
      coaCode: yup.string().required('Account is Required'),
      transactionDate: yup.date().required('Transaction Date is Required'),
      details: yup
        .array()
        .of(
          yup.object().shape({
            description: yup.string().required('Memo is Required'),
            amount: yup.number().required('Amount is Required').positive('Amount must be positive'),
          })
        )
        .required()
        .min(1, 'At least one detail entry is required'),
    })
    .required();

  const {
    register,
    control,
    formState: { errors },
    handleSubmit,
    setValue,
    watch,
    trigger,
    getValues,
    reset,
  } = useForm<DepositType>({
    resolver: yupResolver(schema),
    defaultValues: {
      transactionDate: '',
      coaCode: '',
      description: '',
      transactionNo: '',
      transactionType: 'Payment',
      transactionName: '',
      transactionRef: '',
      contactId: 0,
      details: [],
    },
  });

  const { fields, append, remove } = useFieldArray({
    control,
    name: 'details',
  });

  const [debouncedSearchAccount] = useDebounce(searchAccount, 500);

  // Consolidated Query: Fetch based on searchAccount or default (no params)
  const { data: bankResponse, isLoading } = useGetOptionBankQuery(
    debouncedSearchAccount ? { keyword: debouncedSearchAccount } : {},
    { refetchOnMountOrArgChange: true }
  );

  const bankList = bankResponse?.data?.data || bankResponse?.data || [];

  const deleteItem = (indexToDelete: number) => {
    if (isViewMode) return;
    remove(indexToDelete);
    const newItems = showSelected.filter((_, index) => index !== indexToDelete);
    setShowSelected(newItems);
    setSelectedRecords(newItems);
  };

  const [total, setTotal] = useState(0);

  const onSubmit = async (data: DepositType) => {
    try {
      let response;
      if (id) {
        const updateData: DepositUpdateType = {
          ...data,
          transactionType: 'Payment', // Ensure Payment type
          details: data.details.map((detail, index: number) => ({
            ...detail,
            coaCode: showSelected[index]?.coaCode || '',
            isPremier: false,
          })),
          transactionId: transactionIdToUpdate!,
          journalId: parseInt(id),
        };

        response = await updateDeposit(updateData);
      } else {
        const postData: DepositType = {
          ...data,
          transactionType: 'Payment', // Ensure Payment type
          details: data.details.map((detail, index: number) => ({
            ...detail,
            coaCode: showSelected[index]?.coaCode || '',
            isPremier: false,
          })),
        };
        response = await createDeposit(postData);
      }

      responseCallback(
        response,
        () => {
          navigate('/daftar-Pengeluaran');
        },
        null
      );
    } catch (err: any) {
      toastMessage(err.message, 'error');
    }
  };

  useEffect(() => {
    const title = isUpdateMode
      ? 'Update Daftar Pengeluaran'
      : isViewMode
        ? 'View Daftar Pengeluaran'
        : 'Tambah Daftar Pengeluaran';

    const breadcrumb = isUpdateMode ? 'Update' : isViewMode ? 'View' : 'Create';

    dispatch(setPageTitle(title));
    dispatch(setTitle(title));
    dispatch(setBreadcrumbTitle(['Dashboard', 'Pengeluaran', 'Daftar Pengeluaran', breadcrumb]));

    if (id) {
      refetchdetailTrans();
    }
  }, [dispatch, id, refetchdetailTrans, isViewMode, isUpdateMode]);

  useEffect(() => {
    if (detailTrans && detailTrans.data) {
      const data = detailTrans.data;

      if (isUpdateMode && data.status !== 'Pending') {
        setIsLocked(true);
        setStatusMessage(`Data tidak bisa diubah karena statusnya: ${data.status}.`);
      } else {
        setIsLocked(false);
        setStatusMessage('');
      }

      setTransactionIdToUpdate(detailTrans.data.transactionId);

      setAccount({
        label: `${data.coaCode} - ${data.coaName}`,
        value: data.coaCode,
      });

      const formattedDetails = data.details.map((apiDetail: any) => ({
        journalId: apiDetail.journalId,
        description: apiDetail.journalDesc,
        amount: apiDetail.debitAmount, // Use debitAmount for Expense/Payment
        coaCode: apiDetail.coaCode,
        isPremier: apiDetail.isPremier,
      }));

      const formDataToSet = {
        transactionNo: data.transactionNo || '',
        coaCode: data.coaCode || '',
        transactionDate: data.transactionDate || '',
        description: data.description || '',
        details: formattedDetails,
      };

      reset(formDataToSet);

      if (data.details && data.details.length > 0) {
        setDetailsData(data.details);
        const detailsWithCoa = data.details.map((detail: any) => ({
          coaCode: detail.coaCode,
          coaName: detail.coaName || 'N/A',
        }));
        setShowSelected(detailsWithCoa);
        setSelectedRecords(detailsWithCoa);
        setIsSave(true);
      }
    }
  }, [detailTrans, reset, isUpdateMode]);

  useEffect(() => {
    const subscription = watch((value) => {
      const totalDetails =
        value.details?.reduce((sum, detail) => {
          const detailAmount = parseFloat(detail?.amount?.toString() || '0') || 0;
          return sum + detailAmount;
        }, 0) || 0;

      setTotal(totalDetails);
    });
    return () => subscription.unsubscribe();
  }, [watch]);

  return (
    <div className="flex flex-col gap-6 max-w-5xl mx-auto">
      {/* Header / Title */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
            {isUpdateMode ? t('pengeluaran.form_page.update_title') : isViewMode ? t('pengeluaran.form_page.detail_title') : t('pengeluaran.form_page.create_title')}
          </h1>
          <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
            {isUpdateMode ? t('pengeluaran.form_page.update_subtitle') : t('pengeluaran.form_page.create_subtitle')}
          </p>
        </div>
        <Button
          type="button"
          variant="outline"
          onClick={() => navigate('/daftar-Pengeluaran')}
          className="gap-2 shadow-sm bg-white dark:bg-black"
          size="sm"
        >
          <ArrowLeft className="h-4 w-4" />
          {t('pengeluaran.form_page.back')}
        </Button>
      </div>

      {isLocked && (
        <div className="bg-amber-50 border-l-4 border-amber-500 text-amber-900 p-4 rounded-md shadow-sm flex items-start gap-3" role="alert">
          <div className="mt-0.5">
            <Tippy content="Info">
              <span className="bg-amber-100 text-amber-600 rounded-full w-5 h-5 flex items-center justify-center font-bold text-xs">!</span>
            </Tippy>
          </div>
          <div>
            <p className="font-bold">{t('pengeluaran.form_page.readonly_mode')}</p>
            <p className="text-sm opacity-90">{statusMessage}</p>
          </div>
        </div>
      )}

      <form className="space-y-6" onSubmit={handleSubmit(onSubmit)}>
        {/* Main Information Card */}
        <Card className="shadow-sm border border-gray-200 dark:border-gray-800">
          <CardHeader className="bg-gray-50/50 dark:bg-[#1b2e4b] border-b border-gray-100 dark:border-gray-700 px-6 py-4">
            <CardTitle className="text-base font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
              <span className="bg-primary/10 text-primary w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold">1</span>
              {t('pengeluaran.form_page.transaction_info')}
            </CardTitle>
          </CardHeader>
          <CardContent className="p-6">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              {/* Transaction No */}
              <div className="space-y-2">
                <Label htmlFor="transactionNo" className="text-gray-700 dark:text-gray-300 font-medium">{t('pengeluaran.form_page.transaction_no')} <span className="text-gray-400 font-normal text-xs ml-1">{t('pengeluaran.form_page.transaction_no_optional')}</span></Label>
                <Input
                  id="transactionNo"
                  placeholder={t('pengeluaran.form_page.transaction_no_hint')}
                  {...register('transactionNo')}
                  disabled={isLocked}
                  className={`transition-all ${errors.transactionNo ? 'border-red-500 focus:ring-red-200' : 'focus:ring-primary/20'}`}
                />
                {errors.transactionNo && (
                  <p className="text-xs text-red-500 flex items-center gap-1 mt-1">
                    <span className="w-1 h-1 bg-red-500 rounded-full inline-block"></span>
                    {(errors.transactionNo as FieldError)?.message}
                  </p>
                )}
              </div>

              {/* Transaction Date */}
              <div className="space-y-2">
                <Label htmlFor="transactionDate" className="text-gray-700 dark:text-gray-300 font-medium">{t('pengeluaran.form_page.transaction_date')} <span className="text-red-500">*</span></Label>
                <div className="relative">
                  <Flatpickr
                    options={{
                      enableTime: false,
                      dateFormat: 'd F Y',
                      position: isRtl ? 'auto right' : 'auto left',
                    }}
                    className={`form-input w-full pl-10 transition-all ${errors.transactionDate ? 'border-red-500 focus:ring-red-200' : 'focus:ring-primary/20'} disabled:bg-gray-100 disabled:cursor-not-allowed`}
                    onChange={(date: Date[]) => {
                      if (!isViewMode && !isLocked) {
                        setValue('transactionDate', date[0].toISOString());
                      }
                    }}
                    placeholder={t('pengeluaran.form_page.select_date')}
                    value={detailTrans?.data?.transactionDate}
                    disabled={isLocked}
                  />
                  <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <i className="calendar-icon">📅</i>
                  </div>
                </div>
                {errors.transactionDate && (
                  <p className="text-xs text-red-500 flex items-center gap-1 mt-1">
                    <span className="w-1 h-1 bg-red-500 rounded-full inline-block"></span>
                    {t('pengeluaran.form_page.validation.date_required')}
                  </p>
                )}
              </div>

              {/* Account Selection */}
              <div className="space-y-2 md:col-span-2">
                <Label htmlFor="coaCode" className="text-gray-700 dark:text-gray-300 font-medium">{t('pengeluaran.form_page.source_account')} <span className="text-red-500">*</span></Label>
                <SelectSearch
                  options={bankList}
                  onInputChange={(value) => setSearchAccount(value)}
                  onChange={(record: any) => {
                    setAccount(record);
                    setValue('coaCode', record.desc);
                    setExcludeId(record?.desc);
                  }}
                  className="text-sm"
                  menuPortalTarget={document.body}
                  styles={{
                    control: (base, state) => ({
                      ...base,
                      borderColor: errors.coaCode ? '#ef4444' : state.isFocused ? '#4361ee' : '#e2e8f0',
                      boxShadow: state.isFocused ? '0 0 0 2px rgba(67, 97, 238, 0.2)' : 'none',
                      paddingTop: '2px',
                      paddingBottom: '2px',
                      '&:hover': { borderColor: '#cbd5e1' }
                    }),
                    menuPortal: (base) => ({ ...base, zIndex: 9999 }),
                  }}
                  value={account.value ? account : null}
                  isDisabled={isLocked}
                  isLoading={isLoading}
                  placeholder={isLoading ? t('pengeluaran.form_page.loading_account') : t('pengeluaran.form_page.select_account')}
                />
                {errors.coaCode && (
                  <p className="text-xs text-red-500 flex items-center gap-1 mt-1">
                    <span className="w-1 h-1 bg-red-500 rounded-full inline-block"></span>
                    {t('pengeluaran.form_page.validation.account_required')}
                  </p>
                )}
              </div>

              {/* Description */}
              <div className="space-y-2 md:col-span-2">
                <Label htmlFor="description" className="text-gray-700 dark:text-gray-300 font-medium">{t('pengeluaran.form_page.description')} <span className="text-red-500">*</span></Label>
                <textarea
                  id="description"
                  rows={3}
                  className={`form-textarea w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary/20 transition-all ${errors.description ? 'border-red-500' : ''}`}
                  placeholder={t('pengeluaran.form_page.description_hint')}
                  {...register('description')}
                  disabled={isLocked}
                />
                {errors.description && (
                  <p className="text-xs text-red-500 flex items-center gap-1 mt-1">
                    <span className="w-1 h-1 bg-red-500 rounded-full inline-block"></span>
                    {t('pengeluaran.form_page.validation.description_required')}
                  </p>
                )}
              </div>
            </div>
          </CardContent>
        </Card>

        {/* Details Card */}
        <Card className="shadow-sm border border-gray-200 dark:border-gray-800">
          <CardHeader className="bg-gray-50/50 dark:bg-[#1b2e4b] border-b border-gray-100 dark:border-gray-700 px-6 py-4 flex flex-row items-center justify-between">
            <CardTitle className="text-base font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
              <span className="bg-primary/10 text-primary w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold">2</span>
              {t('pengeluaran.form_page.detail_info')}
            </CardTitle>
            {!isLocked && (
              <Button
                onClick={() => setIsShowModalCoa(true)}
                type="button"
                className="bg-primary hover:bg-primary/90 text-white shadow-sm gap-2 h-9"
                size="sm"
              >
                <Plus className="h-4 w-4" />
                {t('pengeluaran.form_page.add_detail')}
              </Button>
            )}
          </CardHeader>
          <CardContent className="p-0">
            <div className="table-responsive">
              <table className="w-full text-sm text-left">
                <thead className="bg-gray-50 text-gray-700 font-semibold border-b border-gray-100">
                  <tr>
                    <th className="px-6 py-3 w-1/3">{t('pengeluaran.form_page.expense_account')}</th>
                    <th className="px-6 py-3">{t('pengeluaran.form_page.item_description')}</th>
                    <th className="px-6 py-3 w-48 text-right">{t('pengeluaran.form_page.amount')}</th>
                    {!isLocked && <th className="px-6 py-3 w-[50px]"></th>}
                  </tr>
                </thead>
                <tbody className="divide-y divide-gray-100">
                  {isSave && showSelected.length > 0 ? (
                    showSelected.map((record: COAType, index: number) => (
                      <tr key={index} className="hover:bg-gray-50/50 group">
                        <td className="px-6 py-4 align-top">
                          <div className="font-medium text-gray-900">{record.coaName}</div>
                          <div className="text-xs text-gray-500 mt-0.5">{record.coaCode}</div>
                        </td>
                        <td className="px-6 py-4 align-top">
                          <Input
                            id={`details.${index}.description`}
                            {...register(`details.${index}.description` as const)}
                            placeholder="Deskripsi item..."
                            disabled={isLocked}
                            className={`h-9 text-sm ${errors.details?.[index]?.description ? 'border-red-500' : 'border-gray-200'}`}
                          />
                        </td>
                        <td className="px-6 py-4 align-top">
                          <div className="relative">
                            <span className="absolute left-3 top-2 text-gray-400 text-xs font-medium">Rp</span>
                            <Input
                              type="number"
                              id={`details.${index}.amount`}
                              {...register(`details.${index}.amount` as const)}
                              placeholder="0"
                              disabled={isLocked}
                              className={`h-9 pl-8 text-right font-mono text-sm ${errors.details?.[index]?.amount ? 'border-red-500' : 'border-gray-200'}`}
                            />
                          </div>
                        </td>
                        {!isLocked && (
                          <td className="px-6 py-4 align-top text-center">
                            <button
                              type="button"
                              className="text-gray-400 hover:text-red-500 transition-colors p-1 rounded-md hover:bg-red-50 opacity-0 group-hover:opacity-100"
                              title="Hapus baris"
                              onClick={() => deleteItem(index)}
                            >
                              <Trash2 className="h-4 w-4" />
                            </button>
                          </td>
                        )}
                      </tr>
                    ))
                  ) : (
                    <tr>
                      <td colSpan={isLocked ? 3 : 4} className="px-6 py-12 text-center text-gray-500">
                        <div className="flex flex-col items-center justify-center gap-2">
                          <div className="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center text-gray-400">
                            <Plus className="w-6 h-6" />
                          </div>
                          <p className="font-medium">{t('pengeluaran.form_page.no_items')}</p>
                          <p className="text-xs">{t('pengeluaran.form_page.no_items_hint')}</p>
                        </div>
                      </td>
                    </tr>
                  )}
                </tbody>
                {/* Footer Summary */}
                {showSelected.length > 0 && (
                  <tfoot className="bg-gray-50/50 border-t border-gray-100">
                    <tr>
                      <td colSpan={2} className="px-6 py-4 text-right align-top">
                        <span className="text-gray-500 text-sm font-medium mr-2">{t('pengeluaran.form_page.total_expense')}:</span>
                      </td>
                      <td className="px-6 py-4 text-right">
                        <div className="text-xl font-bold text-primary tracking-tight">
                          Rp {Number(total).toLocaleString('id-ID')}
                        </div>
                        <div className="text-xs text-gray-500 italic mt-1">
                          {pembilang(total)} rupiah
                        </div>
                      </td>
                      {!isLocked && <td></td>}
                    </tr>
                  </tfoot>
                )}
              </table>
            </div>
          </CardContent>
        </Card>

        {/* Action Buttons */}
        <div className="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-800">
          <Button
            type="button"
            variant="ghost"
            onClick={() => navigate('/daftar-Pengeluaran')}
            className="text-gray-600 hover:text-gray-900 hover:bg-gray-100"
          >
            {t('pengeluaran.form_page.cancel')}
          </Button>

          {!isLocked && (
            <Button
              type="button"
              onClick={async () => {
                const valid = await trigger();
                if (valid) {
                  setShowConfirmModal(true);
                }
              }}
              disabled={isCreating || isUpdating}
              className="min-w-[140px] shadow-md hover:shadow-lg transition-all"
            >
              {isCreating || isUpdating ? (
                <>
                  <div className="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin mr-2"></div>
                  Processing...
                </>
              ) : (
                <>
                  <Save className="mr-2 h-4 w-4" />
                  {isUpdateMode ? t('pengeluaran.form_page.update') : t('pengeluaran.form_page.save')}
                </>
              )}
            </Button>
          )}
        </div>
      </form>

      {!isLocked && (
        <ModalCoaCustom
          setIsSave={setIsSave}
          selectedRecords={selectedRecords}
          setShowSelected={setShowSelected}
          setSelectedRecords={setSelectedRecords}
          showModal={isShowModalCoa}
          setIsShowModal={setIsShowModalCoa}
          excludeId={excludeId}
        />
      )}

      <ConfirmationModal
        isOpen={showConfirmModal}
        onClose={() => !isCreating && !isUpdating && setShowConfirmModal(false)}
        onConfirm={handleSubmit(onSubmit)}
        title="Confirmation"
        message={`Are you sure you want to ${id ? 'update' : 'save'} this data?`}
        confirmText={id ? 'Update' : 'Save'}
        cancelText="Cancel"
        loading={isCreating || isCreating}
      />
    </div>
  );
};

export default DaftarPengeluaranForm;
