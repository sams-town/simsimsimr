import { useDispatch, useSelector } from 'react-redux';
import { useTranslation } from 'react-i18next';
import { useLocation, useNavigate, useParams } from 'react-router-dom';
import { useEffect, useState } from 'react';
import { setPageTitle, setTitle, setBreadcrumbTitle } from '../../store/themeConfigSlice';
import {
    useGetDetailUsersQuery,
    usePostUsersMutation,
    useUpdateUsersMutation,
} from '@/store/api/users/usersApiSlice';
import { useGetRolesQuery } from '@/store/api/roles/rolesApiSlice';
import * as yup from 'yup';
import { useForm, FieldError } from 'react-hook-form';
import { yupResolver } from '@hookform/resolvers/yup';
import { usersType } from '@/types';
import { rolesType } from '@/types/rolesType';
import { responseCallback } from '@/utils/responseCallback';
import { toastMessage } from '@/utils/toastUtils';
import ConfirmationModal from '@/components/ConfirmationModal';

// New Imports
import { Eye, Pencil } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardTitle } from '@/components/ui/card';

const Form = () => {
    const { t } = useTranslation();
    const { id } = useParams();
    const [editMode, setEditMode] = useState(id ? false : true); // default: kalau edit, mulai dari view-only
    const [showConfirmModal, setShowConfirmModal] = useState(false); // ✅ pindah ke dalam komponen

    const user = useSelector((state: any) => state.auth.user);
    const [post, { isLoading: isLoadingPost }] = usePostUsersMutation();
    const [update, { isLoading: isLoadingUpdate }] = useUpdateUsersMutation();
    const dispatch = useDispatch();
    const location = useLocation();
    const navigate = useNavigate();
    const pathSegments = location.pathname.split('/');
    const lastSegment = pathSegments[pathSegments.length - 1];
    const type = pathSegments[2];

    const {
        data: detailUsers,
        refetch: detailUsersRefetch,
    } = id ? useGetDetailUsersQuery(id) : { data: null, refetch: () => { } };

    const { data: rolesList, refetch: rolesListRefetch } = useGetRolesQuery({});

    const schema = yup
        .object({
            email: yup.string().email(t('validation.email_invalid')).required(t('validation.required')),
            password: id ? yup.string() : yup.string().required(t('validation.required')),
            displayName: yup.string().required(t('validation.required')),
            roleID: yup.string().required(t('validation.required')),
            status: yup.string().required(t('validation.required')),
        })
        .required();

    const {
        register,
        formState: { errors },
        handleSubmit,
        setValue,
        trigger,
    } = useForm<usersType>({
        resolver: yupResolver(schema),
        mode: 'all',
        defaultValues: {
            status: 'Active',
        },
    });

    const submitForm = async (data: usersType) => {
        console.log("🚀 Data ready to submit:", data);
        try {
            let response: any;
            if (id) {
                response = await update(data);
            } else {
                response = await post(data);
            }
            responseCallback(
                response,
                () => {
                    navigate('/users');
                },
                null,
            );
        } catch (err: any) {
            toastMessage(err.message, 'error');
        }
    };

    useEffect(() => {
        rolesListRefetch();
    }, [rolesListRefetch]);

    useEffect(() => {
        if (id) {
            detailUsersRefetch();
        }
    }, [id, detailUsersRefetch]);

    useEffect(() => {
        if (detailUsers?.data) {
            Object.keys(detailUsers.data).forEach((key) => {
                setValue(key as keyof usersType, detailUsers.data[key]);
            });
        }
    }, [detailUsers, setValue, rolesList]);

    useEffect(() => {
        dispatch(setPageTitle(t('users_page.title')));
        dispatch(setTitle(t('users_page.title')));
        if (type === 'create') {
            dispatch(setBreadcrumbTitle(['Dashboard', t('users_page.title'), t('common.add_new')]));
        } else {
            dispatch(setBreadcrumbTitle(['Dashboard', t('users_page.title'), t('common.edit'), lastSegment]));
        }
    }, [dispatch, type, lastSegment]);

    return (
        <div>
            <Card className="relative">
                <CardContent className="pt-6">
                    <CardTitle className="text-lg">
                        {id ? t('users_page.edit_title') : t('users_page.create_title')}
                    </CardTitle>
                    {id && (
                        <div className="absolute top-6 right-6 z-20">
                            <Button
                                variant="outline"
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
                        </div>
                    )}

                    <form className="py-8 flex gap-6 flex-col" onSubmit={handleSubmit(submitForm)}>
                        <div className="grid md:grid-cols-2 gap-4 w-full">
                            <div className="space-y-2">
                                <Label htmlFor="displayName">{t('users_page.form.name')}</Label>
                                <Input
                                    id="displayName"
                                    type="text"
                                    placeholder={t('users_page.form.enter_name')}
                                    {...register('displayName')}
                                    readOnly={!editMode}
                                />
                                <span className="text-danger text-xs">
                                    {(errors.displayName as FieldError)?.message}
                                </span>
                            </div>
                            <div className="space-y-2">
                                <Label htmlFor="Email">{t('users_page.form.email')}</Label>
                                <Input
                                    id="Email"
                                    type="email"
                                    placeholder={t('users_page.form.enter_email')}
                                    {...register('email')}
                                    readOnly={!editMode}
                                />
                                <span className="text-danger text-xs">
                                    {(errors.email as FieldError)?.message}
                                </span>
                            </div>
                            <div className="space-y-2">
                                <Label htmlFor="Password">{t('users_page.form.password')}</Label>
                                <Input
                                    id="Password"
                                    type="password"
                                    placeholder={t('users_page.form.enter_password')}
                                    {...register('password')}
                                    readOnly={!editMode}
                                />
                                <span className="text-danger text-xs">
                                    {(errors.password as FieldError)?.message}
                                </span>
                            </div>
                            <div className="space-y-2">
                                <Label htmlFor="roleID">{t('users_page.form.role')}</Label>
                                <select
                                    id="roleID"
                                    {...register('roleID')}
                                    className="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                    disabled={!editMode}
                                    defaultValue={detailUsers?.data?.roleID || ''}
                                >
                                    <option value="">{t('users_page.form.select_role')}</option>
                                    {rolesList?.data?.data?.map((d: rolesType, i: number) => (
                                        <option key={i} value={d.roleID}>
                                            {d.roleName}
                                        </option>
                                    ))}
                                </select>
                                <span className="text-danger text-xs">
                                    {(errors.roleID as FieldError)?.message}
                                </span>
                            </div>
                            <div className="space-y-2">
                                <Label>{t('users_page.form.status')}</Label>
                                <div className="flex space-x-4">
                                    <label className="flex items-center">
                                        <input
                                            type="radio"
                                            value="Active"
                                            {...register('status')}
                                            className="form-radio"
                                            disabled={!editMode}
                                        />
                                        <span className="ml-2">{t('users_page.active')}</span>
                                    </label>
                                    <label className="flex items-center">
                                        <input
                                            type="radio"
                                            value="InActive"
                                            {...register('status')}
                                            className="form-radio"
                                            disabled={!editMode}
                                        />
                                        <span className="ml-2">{t('users_page.inactive')}</span>
                                    </label>
                                </div>
                                <span className="text-danger text-xs">
                                    {(errors.status as FieldError)?.message}
                                </span>
                            </div>
                        </div>

                        <div className="flex w-full justify-end gap-4 mt-4">
                            <Button
                                type="button"
                                variant="outline"
                                onClick={() => navigate('/users')}
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
                                    {isLoadingPost || isLoadingUpdate
                                        ? t('common.loading')
                                        : id
                                            ? t('common.update')
                                            : t('common.save')}
                                </Button>
                            )}
                        </div>
                    </form>
                </CardContent>
            </Card>

            <ConfirmationModal
                isOpen={showConfirmModal}
                onClose={() => (!isLoadingPost && !isLoadingUpdate) && setShowConfirmModal(false)}
                onConfirm={handleSubmit(submitForm)}
                title={t('messages.confirmation')}
                message={id ? t('messages.update_confirm') : t('messages.save_confirm')}
                confirmText={id ? t('common.update') : t('common.save')}
                cancelText={t('common.cancel')}
                loading={isLoadingPost || isLoadingUpdate}
            />
        </div>
    );
};

export default Form;
