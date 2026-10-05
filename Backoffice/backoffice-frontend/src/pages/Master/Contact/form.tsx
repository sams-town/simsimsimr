import ConfirmationModal from '@/components/ConfirmationModal';
import { Eye, Pencil, Plus, Trash2 } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { useGetOptionBankListQuery, useGetOptionBankQuery } from '@/store/api/bank/bankApiSlice';
import { useGetCityOptionMuMutation } from '@/store/api/city/cityApiSlice';
import {
  useCreateContactMutation,
  useGetContactCategoryOptionQuery,
  useGetContactDetailQuery,
  useUpdateContactMutation,
} from '@/store/api/contact/contactApiSlice';
import { useGetProvinceOptionQuery } from '@/store/api/province/provinceApiSlice';
import { OptionType } from '@/types';
import { AccountDetail, ContactType } from '@/types/contactType';
import { responseCallback } from '@/utils/responseCallback';
import { toastMessage } from '@/utils/toastUtils';
import { yupResolver } from '@hookform/resolvers/yup';
import Tippy from '@tippyjs/react';
import { useEffect, useState } from 'react';
import { Controller, FieldError, useFieldArray, useForm } from 'react-hook-form';
import { useTranslation } from 'react-i18next';
import { useDispatch } from 'react-redux';
import { useLocation, useNavigate, useParams } from 'react-router-dom';
import SelectSearch from 'react-select';
import 'tippy.js/dist/tippy.css';
import * as yup from 'yup';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '../../../store/themeConfigSlice';

import { reactSelectCustomStyles } from '@/utils/reactSelectCustomStyle';

const Form = () => {
  const { t } = useTranslation();
  const dispatch = useDispatch();
  const navigate = useNavigate();
  const location = useLocation();
  const { id } = useParams();
  const pathSegments = location.pathname.split('/');
  const lastSegment = pathSegments[pathSegments.length - 1];

  const type = pathSegments[2];

  const { data: detailContact, refetch: refetchDetailContact } = id
    ? useGetContactDetailQuery(id, { refetchOnMountOrArgChange: true })
    : { data: null, refetch: () => { } };
  const { data: bankOptionList, isLoading: isBankOptionListLoading } = useGetOptionBankListQuery(
    {}
  );

  const [
    create,
    { isLoading: isCreating, isSuccess: isSuccessCreate, isError: isErrorCreate, error },
  ] = useCreateContactMutation();
  const [update, { isLoading: isUpdating }] = useUpdateContactMutation();
  const [editMode, setEditMode] = useState(id ? false : true);
  const [showConfirmModal, setShowConfirmModal] = useState(false);
  const [isBankOptionLoading, setIsBankOptionLoading] = useState(false);
  const [searchBank, setSearchBank] = useState<any>();
  const [selectedCategory, setSelectedCategory] = useState<OptionType>({
    label: 'Select Category',
    value: '',
  });
  const [selectedProvince, setSelectedProvince] = useState<OptionType>({
    label: 'Select Province',
    value: '',
  });
  const [selectedCity, setSelectedCity] = useState<OptionType>({
    label: 'Select City',
    value: '',
  });
  const [searchProvince] = useState<any>();
  const [searchCity] = useState<any>();
  const [optionCity, setOptionCity] = useState<any>();
  const {
    data: optionProvince,
    refetch: refetchOptionProvince,
    isLoading: isLoadingProvince,
  } = useGetProvinceOptionQuery({
    page: -1,
    pageSize: -1,
    orderBy: 'provinceId',
    keyword: searchProvince ?? '',
  });
  const [city, { isLoading: isLoadingCity }] = useGetCityOptionMuMutation();
  const { data: optionCategory, isLoading: isLoadingCategory } = useGetContactCategoryOptionQuery({
    page: -1,
    pageSize: -1,
  });

  const schema = yup.object({
    contactName: yup.string().required(t('validation.required')),
    email: yup.string().email(t('validation.email_invalid')).required(t('validation.required')),
    phone: yup
      .string()
      .required(t('validation.required'))
      .matches(/^[0-9]+$/, t('validation.number_only'))
      .min(10, t('validation.min_length', { count: 10 })),
    address: yup.string().required(t('validation.required')),
    provinceId: yup.number().required(t('validation.required')).typeError(t('validation.required')),
    cityId: yup.string().required(t('validation.required')).typeError(t('validation.required')),
    contactCategoryId: yup
      .string()
      .required(t('validation.required'))
      .typeError(t('validation.required')),
    npwp: yup.string().nullable(),
    pph21: yup.boolean().nullable(),
    doctorShare: yup.number().nullable().transform((value, originalValue) => originalValue === '' ? null : value),
    accountDetail: yup
      .array()
      .of(
        yup.object().shape({
          bankCode: yup.string(), // Made optional
          accountName: yup.string(), // Made optional
          accountNo: yup
            .string()
            .matches(/^[0-9]*$/, t('validation.number_only')), // Optional match
          bankDetail: yup.object().shape({
            bankCode: yup.string(),
            bankName: yup.string(),
          }),
        })
      )
      .notRequired(), // Made optional
  });

  const {
    register,
    formState: { errors },
    handleSubmit,
    setValue,
    trigger,
    watch,
    control,
  } = useForm<ContactType>({
    resolver: yupResolver(schema),
    mode: 'onChange',
    defaultValues: {
      accountDetail: [],
    },
  });

  const provinceId = watch('provinceId');
  const { fields, append, remove } = useFieldArray({
    control,
    name: 'accountDetail',
  });

  const addAccount = () => {
    append({
      accountId: 0,
      accountName: '',
      bankCode: '',
      bankName: '',
      accountNo: '',
      contactId: Number(id) || 0,
      bankDetail: {
        bankCode: '',
        bankName: '',
      },
    });
  };

  useEffect(() => {
    if (isSuccessCreate) {
      navigate('/bank');
    }

    if (isErrorCreate && error) {
      let errorMessage = t('messages.something_went_wrong');
      if ('status' in error) {
        errorMessage = (error as any).data as string;
      } else if ('message' in error) {
        errorMessage = (error as any).message as string;
      }
      toastMessage(errorMessage, 'error');
    }
  }, [isSuccessCreate, isErrorCreate, navigate]);

  const getApiCityOption = async (provinceId: any) => {
    try {
      const response = await city({
        keyword: searchCity ?? '',
        page: 1,
        pageSize: 10,
        provinceid: provinceId,
      });

      if ('data' in response) {
        setOptionCity(response?.data);
      }
    } catch (e) {
      console.log(e);
    }
  };

  const onSubmit = async (data: ContactType) => {
    try {
      let response: any;
      if (id) {
        console.log('update', data);
        response = await update(data);
      } else {
        console.log('create', data);
        response = await create(data);
      }
      responseCallback(
        response,
        () => {
          navigate('/contact');
        },
        null
      );
    } catch (err: any) {
      if (err.response && err.response.status === 400) {
        toastMessage(err.response.data, 'error');
      } else {
        toastMessage(t('messages.something_went_wrong'), 'error');
      }
    }
  };

  useEffect(() => {
    dispatch(setPageTitle(id ? t('master.contact_page.edit_title') : t('master.contact_page.create_title')));
    dispatch(setTitle(id ? t('master.contact_page.edit_title') : t('master.contact_page.create_title')));
    if (type === 'create') {
      dispatch(setBreadcrumbTitle(['Dashboard', t('master.title'), t('master.contact_page.title'), t('common.add_new')]));
    } else {
      dispatch(setBreadcrumbTitle(['Dashboard', t('master.title'), t('master.contact_page.title'), t('common.edit'), lastSegment]));
    }
  }, [dispatch, id, type, lastSegment]);

  useEffect(() => {
    if (provinceId) {
      getApiCityOption(provinceId);
    }
  }, [provinceId, searchCity]);

  useEffect(() => {
    if (
      detailContact?.data &&
      optionCategory?.data &&
      optionProvince?.data &&
      bankOptionList?.data
    ) {
      const data = detailContact?.data;
      console.log('populated', data);

      Object.keys(detailContact.data).forEach((key) => {
        if (key !== 'accountDetail') {
          setValue(key as keyof ContactType, data[key as keyof ContactType]);
        }
      });

      if (data?.accountDetail && Array.isArray(data?.accountDetail)) {
        const allBanks = bankOptionList.data;

        const enrichedAccountDetails = data?.accountDetail.map((accountItem: AccountDetail) => {
          const matchingBank = allBanks.find((bank: any) => bank.value == accountItem.bankCode);
          return {
            ...accountItem,
            bankName: matchingBank ? matchingBank.label : '',
            bankDetail: {
              bankCode: accountItem.bankCode,
              bankName: matchingBank ? matchingBank.label : '',
            },
          };
        });

        setValue('accountDetail', enrichedAccountDetails);
      }

      const fullCategoryOption = optionCategory.data.find(
        (option: OptionType) => option.value == data.contactCategoryId
      );

      if (fullCategoryOption) {
        setSelectedCategory(fullCategoryOption);
      }

      const fullProvinceOption = optionProvince.data.find(
        (option: OptionType) => option.value == data.provinceId
      );

      if (fullProvinceOption) {
        setSelectedProvince(fullProvinceOption);
      }

      setSelectedCity({
        label: data.cityName,
        value: data.cityId,
      });

      if (data.accountDetail && Array.isArray(data.accountDetail)) {
        setValue('accountDetail', data.accountDetail);
      }
    }
  }, [detailContact, setValue, optionCategory, optionProvince]);

  return (
    <div className="space-y-6">
      <Card>
        <CardHeader className="flex flex-row items-center justify-between border-b border-gray-100 pb-4">
          <CardTitle className="text-lg">
            {id ? t('master.contact_page.edit_title') : t('master.contact_page.create_title')}
          </CardTitle>
          {id && (
            <Button
              variant="ghost"
              size="sm"
              onClick={() => setEditMode((prev) => !prev)}
              className="gap-2"
            >
              {editMode ? (
                <>
                  <Eye className="h-4 w-4" />
                  <span>{t('common.view_mode')}</span>
                </>
              ) : (
                <>
                  <Pencil className="h-4 w-4" />
                  <span>{t('common.edit_mode')}</span>
                </>
              )}
            </Button>
          )}
        </CardHeader>

        <CardContent className="pt-6">
          <form className="space-y-6" onSubmit={handleSubmit(onSubmit)}>
            <div className="grid md:grid-cols-2 gap-4 w-full">

              <div className="space-y-2">
                <Label htmlFor="contactCategoryId"> {t('master.contact_page.form.category')} </Label>
                <div className="relative w-full">
                  <SelectSearch
                    options={optionCategory?.data?.map((data: any) => ({
                      value: data.value,
                      label: data.label,
                    }))}
                    {...register('contactCategoryId')}
                    onChange={(data: any) => {
                      setValue('contactCategoryId', parseInt(data?.value));
                      setSelectedCategory(data);
                    }}
                    defaultValue={selectedCategory}
                    value={selectedCategory ?? null}
                    className="w-full font-normal text-black"
                    isDisabled={!editMode}
                    isLoading={isLoadingCategory}
                    styles={reactSelectCustomStyles}
                  />
                </div>
                <span className="text-danger text-xs">
                  {(errors.contactCategoryId as FieldError)?.message}
                </span>
              </div>
              <div className="space-y-2">
                <Label htmlFor="refCode"> {watch('contactCategoryId') === 3 ? 'NIK' : t('master.contact_page.form.ref_code')} </Label>
                <Input
                  id="refCode"
                  type="text"
                  placeholder={watch('contactCategoryId') === 3 ? 'Enter NIK' : t('master.contact_page.form.enter_ref_code')}
                  {...register('refCode')}
                  readOnly={!editMode}
                />
                <span className="text-danger text-xs">{(errors.refCode as FieldError)?.message}</span>
              </div>
            </div>

            {/* Conditional Fields for Category 3 (Dokter) */}
            {watch('contactCategoryId') === 3 && (
              <div className="grid md:grid-cols-2 gap-4 w-full">
                <div className="space-y-2">
                  <Label htmlFor="npwp"> NPWP </Label>
                  <Input
                    id="npwp"
                    type="text"
                    placeholder="Enter NPWP"
                    {...register('npwp')}
                    readOnly={!editMode}
                  />
                  <span className="text-danger text-xs">{(errors.npwp as FieldError)?.message}</span>
                </div>
                <div className="space-y-2">
                  <Label htmlFor="pph21" className="block mb-2"> PPh21 </Label>
                  <div className="flex items-center gap-2">
                    <input
                      id="pph21"
                      type="checkbox"
                      className="form-checkbox w-5 h-5 text-primary rounded border-gray-300"
                      {...register('pph21')}
                      disabled={!editMode}
                    />
                    <label htmlFor="pph21" className="text-sm text-gray-700 cursor-pointer select-none">
                      Active
                    </label>
                  </div>
                  <span className="text-danger text-xs">{(errors.pph21 as FieldError)?.message}</span>
                </div>
                <div className="space-y-2">
                  <Label htmlFor="doctorShare"> Doctor Share (%) </Label>
                  <Input
                    id="doctorShare"
                    type="number"
                    placeholder="Enter Doctor Share (Optional)"
                    {...register('doctorShare', { valueAsNumber: true })}
                    readOnly={!editMode}
                  />
                  <span className="text-danger text-xs">{(errors.doctorShare as FieldError)?.message}</span>
                </div>
              </div>
            )}

            {/* Conditional Due Date for Category 5 (Asuransi) or 6 (Mitra) */}
            {/* {(watch('contactCategoryId') === 5 || watch('contactCategoryId') === 6) && (
              <div className="grid md:grid-cols-2 gap-4 w-full">
                <div className="space-y-2">
                  <Label htmlFor="dueDate"> {t('master.contact_page.form.due_date')} (Days) </Label>
                  <Input
                    id="dueDate"
                    type="number"
                    placeholder="Enter Due Date (Optional)"
                    {...register('dueDate', { valueAsNumber: true })}
                    readOnly={!editMode}
                  />
                  <span className="text-danger text-xs">{(errors.dueDate as FieldError)?.message}</span>
                </div>
              </div>
            )} */}

            <div className="grid md:grid-cols-2 gap-4 w-full">
              <div className="space-y-2">
                <Label htmlFor="contactName"> {t('master.contact_page.form.name')} </Label>
                <Input
                  id="contactName"
                  type="text"
                  placeholder={t('master.contact_page.form.enter_name')}
                  {...register('contactName')}
                  readOnly={!editMode}
                />
                <span className="text-danger text-xs">
                  {(errors.contactName as FieldError)?.message}
                </span>
              </div>
              <div className="space-y-2">
                <Label htmlFor="email"> {t('master.contact_page.form.email')} </Label>
                <Input
                  id="email"
                  type="email"
                  placeholder={t('master.contact_page.form.enter_email')}
                  {...register('email')}
                  readOnly={!editMode}
                />
                <span className="text-danger text-xs">{(errors.email as FieldError)?.message}</span>
              </div>
            </div>
            <div className="grid md:grid-cols-2 gap-4 w-full">
              <div className="space-y-2">
                <Label htmlFor="phone"> {t('master.contact_page.form.phone')} </Label>
                <Input
                  id="phone"
                  type="tel"
                  placeholder={t('master.contact_page.form.enter_phone')}
                  {...register('phone')}
                  onKeyDown={(e) => {
                    if (
                      !/[0-9]/.test(e.key) &&
                      !['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab'].includes(e.key)
                    ) {
                      e.preventDefault();
                    }
                  }}
                  readOnly={!editMode}
                />
                <span className="text-danger text-xs">{(errors.phone as FieldError)?.message}</span>
              </div>
              <div className="space-y-2">
                <Label htmlFor="address"> {t('master.contact_page.form.address')}</Label>
                <Input
                  id="address"
                  type="text"
                  placeholder={t('master.contact_page.form.enter_address')}
                  {...register('address')}
                  readOnly={!editMode}
                />
                <span className="text-danger text-xs">{(errors.address as FieldError)?.message}</span>
              </div>
            </div>
            <div className="grid md:grid-cols-2 gap-4 w-full">
              <div className="space-y-2">
                <Label htmlFor="provinceId"> {t('master.contact_page.form.province')} </Label>
                <div className="relative w-full">
                  <SelectSearch
                    options={optionProvince?.data?.map((data: any) => ({
                      value: data.value,
                      label: data.label,
                    }))}
                    {...register('provinceId')}
                    onChange={(data: any) => {
                      setValue('provinceId', parseInt(data?.value));
                      setSelectedProvince(data);
                      setValue('cityId', 0);
                      setSelectedCity(data?.label);
                    }}
                    defaultValue={selectedProvince}
                    value={selectedProvince ?? null}
                    className="w-full font-normal text-black"
                    isDisabled={!editMode}
                    isLoading={isLoadingProvince}
                    styles={reactSelectCustomStyles}
                  />
                </div>
                <span className="text-danger text-xs">
                  {(errors.provinceId as FieldError)?.message}
                </span>
              </div>
              <div className="space-y-2">
                <Label htmlFor="cityId"> {t('master.contact_page.form.city')} </Label>
                <div className="relative w-full">
                  <SelectSearch
                    options={optionCity?.data ?? []}
                    {...register('cityId')}
                    onChange={(data: any) => {
                      setValue('cityId', parseInt(data?.value));
                      setSelectedCity(data);
                    }}
                    defaultValue={selectedCity}
                    value={selectedCity ?? null}
                    className="w-full font-normal text-black"
                    isDisabled={!editMode}
                    isLoading={isLoadingCity}
                    styles={reactSelectCustomStyles}
                  />
                </div>
                <span className="text-danger text-xs">
                  {(errors.provinceId as FieldError)?.message}
                </span>
              </div>
            </div>
            <div className="space-y-4">
              <div className="flex flex-row justify-between items-center">
                <h1 className="font-semibold text-lg"> {t('master.contact_page.form.bank_info')} </h1>
                <Tippy content={t('master.contact_page.form.add_bank')}>
                  <Button
                    type="button"
                    variant="outline"
                    onClick={addAccount}
                    className="gap-2"
                    disabled={!editMode}
                  >
                    <Plus className="w-4 h-4" />
                    <span> {t('master.contact_page.form.add_bank')} </span>
                  </Button>
                </Tippy>
              </div>
              <div className="rounded-md border border-gray-100">
                <table className="w-full text-sm text-left">
                  <thead className="bg-gray-50 text-gray-700 uppercase">
                    <tr>
                      <th className="px-4 py-3 font-medium">{t('master.contact_page.form.bank_table.bank_name')}</th>
                      <th className="px-4 py-3 font-medium">{t('master.contact_page.form.bank_table.account_name')}</th>
                      <th className="px-4 py-3 font-medium">{t('master.contact_page.form.bank_table.account_no')}</th>
                      {editMode && <th className="px-4 py-3 font-medium text-center">{t('master.contact_page.form.bank_table.action')}</th>}
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-gray-100">
                    {fields.map((field, index) => (
                      <tr key={field.id} className="hover:bg-gray-50/50">
                        <td className="p-2">
                          <Controller
                            name={`accountDetail.${index}.bankCode`}
                            control={control}
                            render={({ field }) => {
                              const options =
                                bankOptionList?.data?.map((option: any) => ({
                                  value: option.value,
                                  label: option.label,
                                })) || [];

                              const currentValue = options.find(
                                (option: any) => option.value === field.value
                              );

                              return (
                                <SelectSearch
                                  placeholder={t('master.contact_page.form.bank_table.select_bank')}
                                  options={options}
                                  onInputChange={(value) => setSearchBank(value)}
                                  value={currentValue || null}
                                  onChange={(selectedOption: any) => {
                                    if (selectedOption) {
                                      field.onChange(selectedOption.value);
                                      setValue(`accountDetail.${index}.bankName`, selectedOption.label);
                                      setValue(`accountDetail.${index}.bankDetail`, {
                                        bankCode: selectedOption.value,
                                        bankName: selectedOption.label,
                                      });
                                    } else {
                                      field.onChange(null);
                                      setValue(`accountDetail.${index}.bankDetail`, {
                                        bankCode: '',
                                        bankName: '',
                                      });
                                      setValue(`accountDetail.${index}.bankName`, '');
                                    }
                                  }}
                                  className="w-full font-normal"
                                  isLoading={isBankOptionLoading}
                                  isDisabled={!editMode}
                                  styles={reactSelectCustomStyles}
                                  menuPortalTarget={document.body}
                                />
                              );
                            }}
                          />
                          <span className="text-danger text-xs">
                            {errors.accountDetail?.[index]?.bankCode?.message}
                          </span>
                        </td>
                        <td className="p-2">
                          <Input
                            type="text"
                            {...register(`accountDetail.${index}.accountName`)}
                            placeholder={t('master.contact_page.form.bank_table.enter_account_name')}
                            readOnly={!editMode}
                          />
                          <span className="text-danger text-xs">
                            {errors.accountDetail?.[index]?.accountName?.message}
                          </span>
                        </td>
                        <td className="p-2">
                          <Input
                            type="text"
                            {...register(`accountDetail.${index}.accountNo`)}
                            placeholder={t('master.contact_page.form.bank_table.enter_account_no')}
                            readOnly={!editMode}
                            onKeyPress={(event) => {
                              if (!/[0-9]/.test(event.key)) {
                                event.preventDefault();
                              }
                            }}
                          />
                          <span className="text-danger text-xs">
                            {errors.accountDetail?.[index]?.accountNo?.message}
                          </span>
                        </td>
                        {editMode && (
                          <td className="p-2 text-center">
                            <Tippy content="Hapus Rekening">
                              <Button
                                type="button"
                                variant="ghost"
                                size="sm"
                                onClick={() => remove(index)}
                                className="h-8 w-8 p-0 text-gray-500 hover:text-danger hover:bg-danger/10 rounded-lg"
                              >
                                <Trash2 className="w-4 h-4" />
                              </Button>
                            </Tippy>
                          </td>
                        )}
                      </tr>
                    ))}
                    {fields.length === 0 && (
                      <tr>
                        <td colSpan={4} className="text-center py-8 text-gray-500">
                          <div className="flex flex-col items-center justify-center gap-2">
                            <div className="p-2 rounded-full bg-gray-100">
                              <Plus className="h-6 w-6 text-gray-400" />
                            </div>
                            <span className="text-sm">{t('master.contact_page.form.no_bank_data')}</span>
                            <Button variant="link" size="sm" onClick={addAccount} disabled={!editMode}>
                              {t('master.contact_page.form.add_bank')}
                            </Button>
                          </div>
                        </td>
                      </tr>
                    )}
                  </tbody>
                </table>
              </div>
              <span className="text-danger text-xs">
                {(errors.accountDetail as any)?.message}
              </span>
            </div>

            <div className="flex w-full justify-end gap-4">
              <Button
                type="button"
                variant="outline"
                onClick={() => navigate('/contact')}
              >
                {t('common.back')}
              </Button>
              {editMode && (
                <Button
                  type="button"
                  onClick={async () => {
                    const valid = await trigger();
                    if (valid) {
                      setShowConfirmModal(true);
                    }
                  }}
                >
                  {isCreating || isUpdating ? t('common.loading') : id ? t('common.update') : t('common.save')}
                </Button>
              )}
            </div>
          </form>
        </CardContent>
      </Card>

      <ConfirmationModal
        isOpen={showConfirmModal}
        onClose={() => !isCreating && !isUpdating && setShowConfirmModal(false)}
        onConfirm={handleSubmit(onSubmit)}
        title={t('messages.confirmation')}
        message={id ? t('messages.update_confirm') : t('messages.save_confirm')}
        confirmText={id ? t('common.update') : t('common.save')}
        cancelText={t('common.cancel')}
        loading={isCreating || isUpdating}
      />
    </div>
  );
};

export default Form;
