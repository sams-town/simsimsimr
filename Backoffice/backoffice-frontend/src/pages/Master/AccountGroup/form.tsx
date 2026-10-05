import { useState, useEffect } from 'react';
import { useTranslation } from 'react-i18next';
import { useForm, FieldError } from 'react-hook-form';
import * as yup from 'yup';
import { yupResolver } from '@hookform/resolvers/yup';
import { useNavigate, useParams, useLocation } from 'react-router-dom';
import { useDispatch } from 'react-redux';
import { setPageTitle, setTitle, setBreadcrumbTitle } from '../../../store/themeConfigSlice';
import { responseCallback } from '@/utils/responseCallback';
import { toastMessage } from '@/utils/toastUtils';
import { AccountGroupType } from '@/types';
import {
    useCreateAccountGroupMutation,
    useUpdateAccountGroupMutation,
    useGetAccountGroupDetailQuery,
} from '@/store/api/accountGroup/accountGroupApiSlice';
import { Eye, Pencil } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import ConfirmationModal from '@/components/ConfirmationModal';

const AccountGroupForm = () => {
    const dispatch = useDispatch();
    const { t } = useTranslation();
    const navigate = useNavigate();
    const location = useLocation();
    const { id } = useParams();
    const pathSegments = location.pathname.split('/');
    const lastSegment = pathSegments[pathSegments.length - 1];
    const type = pathSegments[2];

    const { data: detailAccountGroup, refetch: refetchDetailAccountGroup } = id
        ? useGetAccountGroupDetailQuery(id, { refetchOnMountOrArgChange: true })
        : { data: null, refetch: () => { } };

    const [createAccountGroup, { isLoading: isCreating }] = useCreateAccountGroupMutation();
    const [updateAccountGroup, { isLoading: isUpdating }] = useUpdateAccountGroupMutation();

    // ✅ tambahan state
    const [editMode, setEditMode] = useState(id ? false : true);
    const [showConfirmModal, setShowConfirmModal] = useState(false);

    const schema = yup.object({
        accountGroupName: yup.string().required(t('validation.required')),
    });

    const {
        register,
        formState: { errors },
        handleSubmit,
        setValue,
        trigger,
    } = useForm<AccountGroupType>({
        resolver: yupResolver(schema),
    });

    const onSubmit = async (data: AccountGroupType) => {
        try {
            let response: any;
            if (id) {
                response = await updateAccountGroup(data);
            } else {
                response = await createAccountGroup(data);
            }
            responseCallback(response, () => {
                navigate('/accountGroup');
            }, null);
        } catch (err: any) {
            toastMessage(err.message, 'error');
        }
    };

    useEffect(() => {
        dispatch(setPageTitle(t('master.account_group_page.title')));
        dispatch(setTitle(t('master.account_group_page.title')));
        if (type === 'create') {
            dispatch(setBreadcrumbTitle(['Dashboard', 'Master', t('master.account_group_page.title'), type]));
        } else {
            dispatch(setBreadcrumbTitle(['Dashboard', 'Master', t('master.account_group_page.title'), type, lastSegment]));
        }
    }, [dispatch, id, type, lastSegment]);

    useEffect(() => {
        if (detailAccountGroup?.data) {
            Object.keys(detailAccountGroup.data).forEach((key) => {
                setValue(key as keyof AccountGroupType, detailAccountGroup.data[key]);
            });
        }
    }, [detailAccountGroup, setValue]);

    return (
        <div>
            <Card>
                <CardHeader className="flex flex-row items-center justify-between border-b border-gray-100 pb-4">
                    <CardTitle className="text-lg">
                        {id ? t('master.account_group_page.edit_title') : t('master.account_group_page.create_title')}
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
                                    <span>{t('master.account_group_page.list')}</span>
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
                    <form className="space-y-6" onSubmit={handleSubmit(onSubmit)}>
                        <div className="grid md:grid-cols-2 gap-6">
                            <div className="space-y-2">
                                <Label htmlFor="accountGroupName">{t('master.account_group_page.form.name')}</Label>
                                <Input
                                    id="accountGroupName"
                                    type="text"
                                    placeholder={t('master.account_group_page.form.enter_name')}
                                    {...register('accountGroupName')}
                                    readOnly={!editMode}
                                />
                                {errors.accountGroupName && (
                                    <span className="text-danger text-xs">
                                        {(errors.accountGroupName as FieldError)?.message}
                                    </span>
                                )}
                            </div>
                        </div>

                        <div className="flex w-full justify-end gap-4">
                            <Button
                                type="button"
                                variant="outline"
                                onClick={() => navigate('/accountGroup')}
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
                onClose={() => (!isCreating && !isUpdating) && setShowConfirmModal(false)}
                onConfirm={handleSubmit(onSubmit)}
                title={t('messages.confirmation')}
                message={id ? t('messages.update_confirm') : t('messages.save_confirm')}
                confirmText={id ? t('common.save') : t('common.save')}
                cancelText={t('common.cancel')}
                loading={isCreating || isUpdating}
            />
        </div>
    );
};

export default AccountGroupForm;
