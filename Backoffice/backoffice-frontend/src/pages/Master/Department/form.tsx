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
import { DepartmentType } from '@/types';
import {
    useCreateDepartmentMutation,
    useUpdateDepartmentMutation,
    useGetDepartmentDetailQuery,
} from '@/store/api/department/departmentApiSlice';
import { Eye, Pencil } from 'lucide-react';
import ConfirmationModal from '@/components/ConfirmationModal';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

const departmentForm = () => {
    const { t } = useTranslation();
    const dispatch = useDispatch();
    const navigate = useNavigate();
    const location = useLocation();
    const { id } = useParams();
    const pathSegments = location.pathname.split('/');
    const lastSegment = pathSegments[pathSegments.length - 1];
    const type = pathSegments[2];

    const { data: detailDepartment, refetch: refetchDetaildepartment } = id
        ? useGetDepartmentDetailQuery(id, { refetchOnMountOrArgChange: true })
        : { data: null, refetch: () => { } };

    const [createdepartment, { isLoading: isCreating }] = useCreateDepartmentMutation();
    const [updatedepartment, { isLoading: isUpdating }] = useUpdateDepartmentMutation();

    const [editMode, setEditMode] = useState(id ? false : true);
    const [showConfirmModal, setShowConfirmModal] = useState(false);

    const schema = yup.object({
        departmentName: yup.string().required(t('validation.required')),
    });

    const {
        register,
        formState: { errors },
        handleSubmit,
        setValue,
        trigger
    } = useForm<DepartmentType>({
        resolver: yupResolver(schema),
    });

    const onSubmit = async (data: DepartmentType) => {
        try {
            let response: any;
            if (id) {
                response = await updatedepartment(data);
            } else {
                response = await createdepartment(data);
            }
            responseCallback(response, () => {
                navigate('/department');
            }, null);
        } catch (err: any) {
            toastMessage(err.message, 'error');
        }
    };

    useEffect(() => {
        dispatch(setPageTitle(t('master.department_page.title')));
        dispatch(setTitle(t('master.department_page.title')));
        if (type === 'create') {
            dispatch(setBreadcrumbTitle(['Dashboard', t('master.title'), t('master.department_page.title'), t('common.add_new')]));
        } else {
            dispatch(setBreadcrumbTitle(['Dashboard', t('master.title'), t('master.department_page.title'), t('common.edit'), lastSegment]));
        }
    }, [dispatch, id, type, lastSegment]);

    useEffect(() => {
        if (detailDepartment?.data) {
            Object.keys(detailDepartment.data).forEach((key) => {
                setValue(key as keyof DepartmentType, detailDepartment.data[key]);
            });
        }
    }, [detailDepartment, setValue]);

    return (
        <div className="space-y-6">
            <Card>
                <CardHeader className="flex flex-row items-center justify-between border-b border-gray-100 pb-4">
                    <CardTitle className="text-lg">
                        {id ? t('master.department_page.edit_title') : t('master.department_page.create_title')}
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
                                <Label htmlFor="departmentName">{t('master.department_page.form.name')}</Label>
                                <Input
                                    id="departmentName"
                                    type="text"
                                    placeholder={t('master.department_page.form.enter_name')}
                                    {...register('departmentName')}
                                    readOnly={!editMode}
                                />
                                <span className="text-danger text-xs">
                                    {(errors.departmentName as FieldError)?.message}
                                </span>
                            </div>
                        </div>

                        <div className="flex w-full justify-end gap-4">
                            <Button
                                type="button"
                                variant="outline"
                                onClick={() => navigate('/department')}
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
                onClose={() => (!isCreating && !isUpdating) && setShowConfirmModal(false)}
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

export default departmentForm;
