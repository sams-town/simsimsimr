import { Eye, Pencil } from 'lucide-react';
import {
  useCreateMasterBankMutation,
  useGetMasterBankDetailQuery,
  useUpdateMasterBankMutation,
} from '@/store/api/bank/master/masterBankApiSlice';
import { MasterBankType } from '@/types/masterBankType';
import { responseCallback } from '@/utils/responseCallback';
import { toastMessage } from '@/utils/toastUtils';
import { yupResolver } from '@hookform/resolvers/yup';
import { useEffect, useState } from 'react';
import { FieldError, useForm } from 'react-hook-form';
import { useDispatch } from 'react-redux';
import { useLocation, useNavigate, useParams } from 'react-router-dom';
import * as yup from 'yup';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '../../../store/themeConfigSlice';
import { useTranslation } from 'react-i18next';

// shadcn/ui components
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { AlertDialog } from '@/components/ui/alert-dialog';

const BankForm = () => {
  const { t } = useTranslation();
  const dispatch = useDispatch();
  const navigate = useNavigate();
  const location = useLocation();
  const { id } = useParams();
  const pathSegments = location.pathname.split('/');
  const lastSegment = pathSegments[pathSegments.length - 1];
  const type = pathSegments[2];

  const { data: detailBank, refetch: refetchDetailBank } = id
    ? useGetMasterBankDetailQuery(id, { refetchOnMountOrArgChange: true })
    : { data: null, refetch: () => { } };

  const [
    create,
    { isLoading: isCreating, isSuccess: isSuccessCreate, isError: isErrorCreate, error },
  ] = useCreateMasterBankMutation();
  const [update, { isLoading: isUpdating }] = useUpdateMasterBankMutation();
  const [editMode, setEditMode] = useState(id ? false : true);
  const [showConfirmModal, setShowConfirmModal] = useState(false);

  const schema = yup.object({
    bankCode: yup.string().required(t('validation.required')),
    bankName: yup.string().required(t('validation.required')),
  });

  const {
    register,
    formState: { errors },
    handleSubmit,
    setValue,
    trigger,
  } = useForm<MasterBankType>({
    resolver: yupResolver(schema),
  });

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

  const onSubmit = async (data: MasterBankType) => {
    try {
      let response: any;
      if (id) {
        response = await update(data);
      } else {
        response = await create(data);
      }
      responseCallback(
        response,
        () => {
          navigate('/bank');
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
    dispatch(setPageTitle(id ? t('master.bank_page.edit_title') : t('master.bank_page.create_title')));
    dispatch(setTitle(t('master.bank_page.title')));
    if (type === 'create') {
      dispatch(setBreadcrumbTitle(['Dashboard', t('master.title'), t('master.bank_page.title'), t('common.add_new')]));
    } else {
      dispatch(setBreadcrumbTitle(['Dashboard', t('master.title'), t('master.bank_page.title'), t('common.edit'), lastSegment]));
    }
  }, [dispatch, id, type, lastSegment]);

  useEffect(() => {
    if (detailBank?.data) {
      Object.keys(detailBank.data).forEach((key) => {
        setValue(key as keyof MasterBankType, detailBank.data[key]);
      });
    }
  }, [detailBank, setValue]);

  return (
    <div>
      <Card>
        <CardHeader className="flex flex-row items-center justify-between border-b border-gray-100 pb-4">
          <CardTitle className="text-lg">
            {id ? t('master.bank_page.edit_title') : t('master.bank_page.create_title')}
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
            <div className="grid md:grid-cols-2 gap-6">
              <div className="space-y-2">
                <Label htmlFor="bankCode">{t('master.bank_page.form.code')}</Label>
                <Input
                  id="bankCode"
                  type="number"
                  placeholder={t('master.bank_page.form.enter_code')}
                  {...register('bankCode')}
                  readOnly={id !== undefined && id !== null}
                />
                {errors.bankCode && (
                  <span className="text-sm text-danger">
                    {(errors.bankCode as FieldError)?.message}
                  </span>
                )}
              </div>

              <div className="space-y-2">
                <Label htmlFor="bankName">{t('master.bank_page.form.name')}</Label>
                <Input
                  id="bankName"
                  type="text"
                  placeholder={t('master.bank_page.form.enter_name')}
                  {...register('bankName')}
                  readOnly={!editMode}
                />
                {errors.bankName && (
                  <span className="text-sm text-danger">
                    {(errors.bankName as FieldError)?.message}
                  </span>
                )}
              </div>
            </div>

            <div className="flex justify-end gap-3 pt-4 border-t border-gray-100">
              <Button
                type="button"
                variant="outline"
                onClick={() => navigate('/bank')}
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
                  loading={isCreating || isUpdating}
                >
                  {id ? t('common.update') : t('common.save')}
                </Button>
              )}
            </div>
          </form>
        </CardContent>
      </Card>

      <AlertDialog
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

export default BankForm;
