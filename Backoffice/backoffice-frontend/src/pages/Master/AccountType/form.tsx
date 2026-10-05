import { useDispatch } from 'react-redux';
import { useTranslation } from 'react-i18next';
import { useLocation, useNavigate, useParams } from 'react-router-dom';
import { useEffect, useState } from 'react';
import { setPageTitle, setTitle, setBreadcrumbTitle } from '../../../store/themeConfigSlice';
import { useForm, FieldError } from 'react-hook-form';
import * as yup from 'yup';
import { yupResolver } from '@hookform/resolvers/yup';
import {
    useUpdateAccountTypeMutation,
    useCreateAccountTypeMutation,
    useGetAccountTypeDetailQuery,
} from '@/store/api/accountType/accountTypeApiSlice';
import { responseCallback } from '@/utils/responseCallback';
import { toastMessage } from '@/utils/toastUtils';
import { AccountType } from '@/types';
import { Eye, Pencil } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import ConfirmationModal from '@/components/ConfirmationModal';

const Form = () => {
    const dispatch = useDispatch();
    const { t } = useTranslation();
    const navigate = useNavigate();
    const location = useLocation();
    const { id } = useParams();

    const pathSegments = location.pathname.split('/');
    const lastSegment = pathSegments[pathSegments.length - 1];
    const type = pathSegments[2];

    const [editMode, setEditMode] = useState(id ? false : true); // default: kalau edit, mulai view-only
    const [showConfirmModal, setShowConfirmModal] = useState(false);

    const { data: detailAccountType, refetch: refetchDetailAccountType } = id
        ? useGetAccountTypeDetailQuery<any>(id, { refetchOnMountOrArgChange: true })
        : { data: null, refetch: () => { } };

    const [createAccountType, { isLoading: isCreating }] = useCreateAccountTypeMutation();
    const [updateAccountType, { isLoading: isUpdating }] = useUpdateAccountTypeMutation();

    const schema = yup
        .object({
            accountTypeName: yup.string().required(t('validation.required')),
        })
        .required();

    const {
        register,
        formState: { errors },
        handleSubmit,
        setValue,
        trigger,
    } = useForm<AccountType>({
        resolver: yupResolver(schema),
    });

    const submitForm = async (data: AccountType) => {
        try {
            let response: any;
            if (id) {
                response = await updateAccountType(data);
            } else {
                response = await createAccountType(data);
            }
            responseCallback(response, () => {
                navigate('/accountType');
            }, null);
        } catch (err: any) {
            toastMessage(err.message, 'error');
        }
    };

    useEffect(() => {
        dispatch(setPageTitle(t('master.account_type_page.title')));
        dispatch(setTitle(t('master.account_type_page.title')));

        if (type === 'create') {
            dispatch(setBreadcrumbTitle(['Dashboard', 'Master', t('master.account_type_page.title'), type]));
        } else {
            dispatch(setBreadcrumbTitle(['Dashboard', 'Master', t('master.account_type_page.title'), type, lastSegment]));
        }

        if (id) {
            refetchDetailAccountType();
        }
    }, [dispatch, id, refetchDetailAccountType]);

    useEffect(() => {
        if (detailAccountType && 'data' in detailAccountType) {
            if (detailAccountType?.data) {
                Object.keys(detailAccountType.data).forEach((key) => {
                    setValue(key as keyof AccountType, detailAccountType.data[key]);
                });
            }
        }
    }, [detailAccountType, setValue]);

    return (
        <div>
            <Card>
                <CardHeader className="flex flex-row items-center justify-between border-b border-gray-100 pb-4">
                    <CardTitle className="text-lg">
                        {id ? t('master.account_type_page.edit_title') : t('master.account_type_page.create_title')}
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
                                    <span>{t('master.account_type_page.list')}</span>
                                </>
                            ) : (
                                <>
                                    <Pencil className="h-4 w-4" />
                                    <span>{t('common.edit')}</span>
                                </>
                            )}
                        </Button>
                    )}
                </CardHeader>

                <CardContent className="pt-6">
                    <form className="space-y-6" onSubmit={handleSubmit(submitForm)}>
                        <div className="grid md:grid-cols-2 gap-6">
                            <div className="space-y-2">
                                <Label htmlFor="accountTypeName">{t('master.account_type_page.form.name')}</Label>
                                <Input
                                    id="accountTypeName"
                                    type="text"
                                    placeholder={t('master.account_type_page.form.enter_name')}
                                    {...register('accountTypeName')}
                                    readOnly={!editMode}
                                />
                                {errors.accountTypeName && (
                                    <span className="text-danger text-xs">
                                        {(errors.accountTypeName as FieldError)?.message}
                                    </span>
                                )}
                            </div>
                        </div>

                        <div className="flex w-full justify-end gap-4">
                            <Button
                                type="button"
                                variant="outline"
                                onClick={() => navigate('/accountType')}
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

            {/* ✅ Confirmation Modal */}
            <ConfirmationModal
                isOpen={showConfirmModal}
                onClose={() => !isCreating && !isUpdating && setShowConfirmModal(false)}
                onConfirm={handleSubmit(submitForm)}
                title={t('messages.confirmation')}
                message={id ? t('messages.update_confirm') : t('messages.save_confirm')}
                confirmText={id ? t('common.save') : t('common.save')}
                cancelText={t('common.cancel')}
                loading={isCreating || isUpdating}
            />
        </div>
    );
};

export default Form;
