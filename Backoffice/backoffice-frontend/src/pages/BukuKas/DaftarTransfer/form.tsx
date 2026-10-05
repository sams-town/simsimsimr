import ConfirmationModal from '@/components/ConfirmationModal';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
  useCreateDepositMutation,
  useUpdateDepositMutation,
} from '@/store/api/bank/deposit/depositApiSlice';
import { useGetOptionCOAQuery } from '@/store/api/coa/coaApiSlice';
import { useGetDetailJournalQuery } from '@/store/api/journal/journalApiSlice';
import { OptionType } from '@/types';
import { DepositType, DepositUpdateType } from '@/types/depositType';
import { pembilang } from '@/utils/currencyHelper';
import { responseCallback } from '@/utils/responseCallback';
import { toastMessage } from '@/utils/toastUtils';
import { yupResolver } from '@hookform/resolvers/yup';
import Tippy from '@tippyjs/react';
import 'flatpickr/dist/flatpickr.css';
import { ArrowLeft, ArrowRightLeft, Save, Info } from 'lucide-react';
import { useEffect, useState } from 'react';
import Flatpickr from 'react-flatpickr';
import { FieldError, useFieldArray, useForm } from 'react-hook-form';
import { useTranslation } from 'react-i18next';
import { useDispatch, useSelector } from 'react-redux';
import { useLocation, useNavigate, useParams } from 'react-router-dom';
import SelectSearch from 'react-select';
import 'tippy.js/dist/tippy.css';
import * as yup from 'yup';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '../../../store/themeConfigSlice';

const Form = () => {
  const dispatch = useDispatch();
  const location = useLocation();
  const navigate = useNavigate();
  const { id } = useParams();
  const isViewMode = !!id && location.pathname.includes('/view/');
  const isUpdateMode = !!id && location.pathname.includes('/update/');
  const pathSegments = location.pathname.split('/');
  const lastSegment = pathSegments[pathSegments.length - 1];
  const type = pathSegments[2];
  const { t } = useTranslation();
  const isRtl = useSelector((state: any) => state.themeConfig.rtlClass) === 'rtl';
  const [account, setAccount] = useState<OptionType>({
    label: t('buku_kas.transfer_form.select_account'),
    value: '',
  });
  const [accountTo, setAccountTo] = useState<OptionType>({
    label: t('buku_kas.transfer_form.select_account'),
    value: '',
  });
  const [searchAccount, setSearchAccount] = useState<string>('');
  const [searchAccountTo, setSearchAccountTo] = useState<string>('');
  const [transactionIdToUpdate, setTransactionIdToUpdate] = useState<string | null>(null);
  const [isLocked, setIsLocked] = useState<boolean>(false);
  const [statusMessage, setStatusMessage] = useState<string>('');
  const [post, { isLoading: isLoadingPost }] = useCreateDepositMutation();
  const [update, { isLoading: isLoadingUpdate }] = useUpdateDepositMutation();

  const [showConfirmModal, setShowConfirmModal] = useState(false);

  const { data: detailDaftarTransfer, refetch: refetchDaftarTransfer } = id
    ? useGetDetailJournalQuery<any>(id)
    : { data: null, refetch: () => { } };
  const { data: AccountList, refetch: AccountListRefetch } = useGetOptionCOAQuery<any>({
    orderBy: 'coaCode',
    orderType: 'asc',
    pageSize: 20,
    status: 'Active',
    keyword: searchAccount,
  });
  const { data: AccountToList, refetch: AccountToListRefetch } = useGetOptionCOAQuery<any>({
    orderBy: 'coaCode',
    orderType: 'asc',
    pageSize: 20,
    status: 'Active',
    keyword: searchAccountTo,
  });

  const AccountListOption =
    AccountList?.data?.map((option: any) => ({
      value: option.desc,
      label: option.label,
      level: option.level || '',
      desc: option.desc || '',
    })) || [];

  const AccountToListOption =
    AccountToList?.data?.map((option: any) => ({
      value: option.desc,
      label: option.label,
      level: option.level || '',
      desc: option.desc || '',
    })) || [];

  const schema = yup
    .object({
      coaCode: yup.string().required('Account is Required'),
      transactionDate: yup.date().required('Transaction Date is Required'),
      description: yup.string().required('Description is Required'),
      amount: yup.number().required('Amount is Required').positive('Amount must be positive'),
      details: yup
        .array()
        .of(
          yup.object().shape({
            coaCode: yup.string().required('Account is Required'),
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
  } = useForm<DepositType>({
    resolver: yupResolver(schema),
    defaultValues: {
      transactionDate: '',
      coaCode: '',
      description: '',
      transactionNo: '',
      transactionType: 'Transfer',
      transactionName: '',
      transactionRef: '',
      contactId: 0,
      details: [{ coaCode: '', description: '', amount: 0, isPremier: false }],
    },
  });

  const amount = watch('amount') || 0;

  const { fields, append, remove } = useFieldArray({
    control,
    name: 'details',
  });

  const submitForm = async (formData: DepositType) => {
    try {
      let response;
      if (id) {
        const updateData: DepositUpdateType = {
          ...formData,
          coaCode: account?.desc ?? '',
          details: [
            {
              coaCode: accountTo?.desc,
              amount: formData.amount,
              description: formData.description,
              isPremier: false,
            },
          ],
          transactionId: transactionIdToUpdate!,
          journalId: parseInt(id),
        };

        response = await update(updateData);
      } else {
        const data: DepositType = {
          ...formData,
          coaCode: account?.desc ?? '',
          details: [
            {
              coaCode: accountTo?.desc,
              amount: formData.amount,
              description: formData.description,
              isPremier: false,
            },
          ],
        };
        response = await post(data);
      }
      responseCallback(
        response,
        (data: any) => {
          navigate('/daftar-transfer');
        },
        null
      );
    } catch (err: any) {
      toastMessage(err.message, 'error');
    }
  };

  useEffect(() => {
    const pageTitle = id ? t('buku_kas.transfer_form.update_title') : t('buku_kas.transfer_form.create_title');
    dispatch(setPageTitle(pageTitle));
    dispatch(setTitle(pageTitle));
    if (type == 'create') {
      dispatch(setBreadcrumbTitle([t('menu.dashboard'), t('menu.buku_kas'), t('buku_kas.transfer_page.title'), type]));
    } else {
      dispatch(setBreadcrumbTitle([t('menu.dashboard'), t('menu.buku_kas'), t('buku_kas.transfer_page.title'), type, lastSegment]));
    }
    AccountListRefetch();
  }, [dispatch, t]);

  useEffect(() => {
    if (id) {
      refetchDaftarTransfer();
    }
  }, [id, refetchDaftarTransfer]);

  useEffect(() => {
    if (detailDaftarTransfer && detailDaftarTransfer?.data) {
      const data = detailDaftarTransfer?.data;

      if (isUpdateMode && data.status !== 'Pending') {
        setIsLocked(true);
        setStatusMessage(`${t('buku_kas.transfer_form.read_only_msg')} ${data.status}.`);
      } else {
        setIsLocked(false);
        setStatusMessage('');
      }

      setTransactionIdToUpdate(data.transactionId);

      Object.keys(data).forEach((key) => {
        setValue(key as keyof DepositType, data[key]);
      });

      setAccount({
        label: data.coaName,
        desc: data.coaCode,
      });
      setAccountTo({
        label: data.details[0].coaName,
        desc: data.details[0]?.coaCode,
      });
    }
  }, [detailDaftarTransfer, setValue]);

  return (
    <div className="flex flex-col gap-6 max-w-5xl mx-auto">
      {/* Header / Title */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
            {isUpdateMode ? t('buku_kas.transfer_form.update_title') : isViewMode ? t('buku_kas.transfer_form.detail_title') : t('buku_kas.transfer_form.create_title')}
          </h1>
          <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
            {isUpdateMode ? t('buku_kas.transfer_form.update_subtitle') : t('buku_kas.transfer_form.create_subtitle')}
          </p>
        </div>
        <Button
          type="button"
          variant="outline"
          onClick={() => navigate('/daftar-transfer')}
          className="gap-2 shadow-sm bg-white dark:bg-black"
          size="sm"
        >
          <ArrowLeft className="h-4 w-4" />
          {t('buku_kas.transfer_form.back')}
        </Button>
      </div>

      {isLocked && (
        <div className="bg-amber-50 border-l-4 border-amber-500 text-amber-900 p-4 rounded-md shadow-sm flex items-start gap-3" role="alert">
          <div className="mt-0.5">
            <Info className="h-5 w-5 text-amber-600" />
          </div>
          <div>
            <p className="font-bold">{t('buku_kas.transfer_form.read_only')}</p>
            <p className="text-sm opacity-90">{statusMessage}</p>
          </div>
        </div>
      )}

      <form className="space-y-6" onSubmit={handleSubmit(submitForm)}>
        <Card className="shadow-sm border border-gray-200 dark:border-gray-800">
          <CardHeader className="bg-gray-50/50 dark:bg-[#1b2e4b] border-b border-gray-100 dark:border-gray-700 px-6 py-4">
            <CardTitle className="text-base font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
              <span className="bg-primary/10 text-primary w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold">1</span>
              {t('buku_kas.transfer_form.transaction_info')}
            </CardTitle>
          </CardHeader>

          <CardContent className="p-6">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              {/* Transaction No */}
              <div className="space-y-2">
                <Label htmlFor="transactionNo">{t('buku_kas.transfer_form.transaction_no')} <span className="text-gray-400 font-normal text-xs ml-1">({t('buku_kas.transfer_form.optional')})</span></Label>
                <Input
                  id="transactionNo"
                  placeholder={t('buku_kas.transfer_form.transaction_no_hint')}
                  {...register('transactionNo')}
                  disabled={isLocked}
                  className={`transition-all ${errors.transactionNo ? 'border-red-500 focus:ring-red-200' : 'focus:ring-primary/20'}`}
                />
                {errors.transactionNo && (
                  <p className="text-xs text-red-500 mt-1">{(errors.transactionNo as FieldError)?.message}</p>
                )}
              </div>

              {/* Transaction Date */}
              <div className="space-y-2">
                <Label htmlFor="transactionDate">{t('buku_kas.transfer_form.transaction_date')} <span className="text-red-500">*</span></Label>
                <div className="relative">
                  <Flatpickr
                    options={{
                      enableTime: false,
                      dateFormat: 'd F Y',
                      position: isRtl ? 'auto right' : 'auto left',
                    }}
                    className={`form-input w-full pl-10 transition-all ${errors.transactionDate ? 'border-red-500 focus:ring-red-200' : 'focus:ring-primary/20'} disabled:bg-gray-100 disabled:cursor-not-allowed`}
                    onChange={(date: Date[]) => {
                      setValue('transactionDate', date[0].toISOString());
                    }}
                    placeholder={t('buku_kas.transfer_form.select_date')}
                    value={detailDaftarTransfer?.data?.transactionDate}
                    disabled={isLocked}
                  />
                  <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <i className="calendar-icon">📅</i>
                  </div>
                </div>
                {errors.transactionDate && (
                  <p className="text-xs text-red-500 mt-1">{t('buku_kas.transfer_form.validation.date_required')}</p>
                )}
              </div>

              {/* From Account */}
              <div className="space-y-2">
                <Label htmlFor="coaCode">{t('buku_kas.transfer_form.from_account')} <span className="text-red-500">*</span></Label>
                <SelectSearch
                  options={AccountListOption}
                  onInputChange={(e) => setSearchAccount(e)}
                  onChange={(e: any) => {
                    setAccount(e);
                    setValue('coaCode', e.desc);
                  }}
                  value={account ?? null}
                  placeholder={t('buku_kas.transfer_form.select_from_account')}
                  isDisabled={isLocked}
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
                  menuPortalTarget={document.body}
                />
                {errors.coaCode && <p className="text-xs text-red-500 mt-1">{t('buku_kas.transfer_form.validation.from_required')}</p>}
              </div>

              {/* To Account */}
              <div className="space-y-2">
                <Label htmlFor="toAccount">{t('buku_kas.transfer_form.to_account')} <span className="text-red-500">*</span></Label>
                <SelectSearch
                  options={AccountToListOption}
                  onInputChange={(e) => setSearchAccountTo(e)}
                  onChange={(e: any) => {
                    setAccountTo(e);
                    setValue('details.0.coaCode', e.desc);
                  }}
                  value={accountTo}
                  placeholder={t('buku_kas.transfer_form.select_to_account')}
                  isDisabled={isLocked}
                  styles={{
                    control: (base, state) => ({
                      ...base,
                      borderColor: errors.details?.[0]?.coaCode ? '#ef4444' : state.isFocused ? '#4361ee' : '#e2e8f0',
                      boxShadow: state.isFocused ? '0 0 0 2px rgba(67, 97, 238, 0.2)' : 'none',
                      paddingTop: '2px',
                      paddingBottom: '2px',
                      '&:hover': { borderColor: '#cbd5e1' }
                    }),
                    menuPortal: (base) => ({ ...base, zIndex: 9999 }),
                  }}
                  menuPortalTarget={document.body}
                />
                {errors.details && errors.details[0] && (errors.details[0].coaCode as FieldError) && (
                  <p className="text-xs text-red-500 mt-1">{t('buku_kas.transfer_form.validation.to_required')}</p>
                )}
              </div>

              {/* Amount */}
              <div className="space-y-2">
                <Label htmlFor="amount">{t('buku_kas.transfer_form.amount')} <span className="text-red-500">*</span></Label>
                <div className="relative">
                  <span className="absolute left-3 top-2.5 text-gray-500 font-medium">Rp</span>
                  <Input
                    id="amount"
                    type="number"
                    placeholder="0"
                    {...register('amount')}
                    disabled={isLocked}
                    className={`pl-10 font-mono text-lg font-semibold ${errors.amount ? 'border-red-500 focus:ring-red-200' : 'focus:ring-primary/20'}`}
                  />
                </div>
                {errors.amount && (
                  <p className="text-xs text-red-500 mt-1">{t('buku_kas.transfer_form.validation.amount_required')}</p>
                )}
                <div className="bg-gray-50 p-3 rounded-md border border-gray-100 mt-2">
                  <p className="text-xs text-gray-500 mb-1">{t('buku_kas.transfer_form.in_words')}</p>
                  <p className="font-medium text-gray-700 italic">{pembilang(amount)} {t('buku_kas.transfer_form.rupiah')}</p>
                </div>
              </div>

              {/* Description */}
              <div className="space-y-2">
                <Label htmlFor="description">{t('buku_kas.transfer_form.description')} <span className="text-red-500">*</span></Label>
                <textarea
                  id="description"
                  rows={4}
                  className={`form-textarea w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary/20 transition-all ${errors.description ? 'border-red-500' : ''}`}
                  placeholder={t('buku_kas.transfer_form.description_hint')}
                  {...register('description')}
                  disabled={isLocked}
                ></textarea>
                {errors.description && (
                  <p className="text-xs text-red-500 mt-1">{t('buku_kas.transfer_form.validation.description_required')}</p>
                )}
              </div>
            </div>
          </CardContent>
        </Card>

        {/* Action Buttons */}
        <div className="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-800">
          <Button
            type="button"
            variant="ghost"
            onClick={() => navigate('/daftar-transfer')}
            className="text-gray-600 hover:text-gray-900 hover:bg-gray-100"
          >
            {isLocked ? t('buku_kas.transfer_form.back') : t('buku_kas.transfer_form.cancel')}
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
              disabled={isLoadingPost || isLoadingUpdate}
              className="min-w-[140px] shadow-md hover:shadow-lg transition-all gap-2"
            >
              {isLoadingPost || isLoadingUpdate ? (
                <>
                  <div className="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin mr-2"></div>
                  {t('buku_kas.transfer_form.processing')}
                </>
              ) : (
                <>
                  <Save className="h-4 w-4" />
                  {id ? t('buku_kas.transfer_form.update_transfer') : t('buku_kas.transfer_form.save_transfer')}
                </>
              )}
            </Button>
          )}
        </div>
      </form>

      <ConfirmationModal
        isOpen={showConfirmModal}
        onClose={() => !isLoadingPost && !isLoadingUpdate && setShowConfirmModal(false)}
        onConfirm={handleSubmit(submitForm)}
        title={t('buku_kas.transfer_form.modal.confirm_title')}
        message={id ? t('buku_kas.transfer_form.modal.update_message') : t('buku_kas.transfer_form.modal.save_message')}
        confirmText={id ? t('common.update') : t('common.save')}
        cancelText={t('common.cancel')}
        loading={isLoadingPost || isLoadingUpdate}
      />
    </div>
  );
};

export default Form;
