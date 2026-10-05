import { useDispatch, useSelector } from 'react-redux';
import { useTranslation } from 'react-i18next';
import { useLocation, useNavigate, useParams } from 'react-router-dom';
import { useEffect, useState } from 'react';
import { setPageTitle, setTitle, setBreadcrumbTitle } from '../../../store/themeConfigSlice';
import {
    useGetCostCentersQuery,
    useCreateCostCenterMutation,
    useUpdateCostCenterMutation,
    CostCenterApi,
    useGetCostCenterDetailQuery,
} from '@/store/api/costCenter/costCenterApiSlice';
import { useGetDepartmentsQuery } from '@/store/api/department/departmentApiSlice';
import * as yup from 'yup';
import { useForm, FieldError } from 'react-hook-form';
import { yupResolver } from '@hookform/resolvers/yup';
import { costCenterType } from '@/types';
import { DepartmentType } from '@/types/departmentType';
import { responseCallback } from '@/utils/responseCallback';
import { toastMessage } from '@/utils/toastUtils';

import { Eye, Pencil } from 'lucide-react';
import ConfirmationModal from '@/components/ConfirmationModal';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

const Form = () => {
    const { t } = useTranslation();
    const { id } = useParams();
    const [editMode, setEditMode] = useState(id ? false : true); // default: kalau edit, mulai dari view-only
    const [showConfirmModal, setShowConfirmModal] = useState(false); // ✅ pindah ke dalam komponen

    const user = useSelector((state: any) => state.auth.user);
    const [post, { isLoading: isLoadingPost }] = useCreateCostCenterMutation();
    const [update, { isLoading: isLoadingUpdate }] = useUpdateCostCenterMutation();
    const dispatch = useDispatch();
    const location = useLocation();
    const navigate = useNavigate();
    const pathSegments = location.pathname.split('/');
    const lastSegment = pathSegments[pathSegments.length - 1];
    const type = pathSegments[2];

    const {
        data: detailCostCenter,
        refetch: detailCostCenterRefetch,
    } = id ? useGetCostCenterDetailQuery(id) : { data: null, refetch: () => { } };

    const { data: departmentsList, refetch: departmentsListRefetch } = useGetDepartmentsQuery({});

    const schema = yup
        .object({
            costCenterCode: yup.string().required(t('validation.required')),
            costCenterName: yup.string().required(t('validation.required')),
            departmentId: yup.string().required(t('validation.required'))
        })
        .required();

    const {
        register,
        formState: { errors },
        handleSubmit,
        setValue,
        trigger
    } = useForm<costCenterType>({
        resolver: yupResolver(schema),
        mode: 'all',
    });

    const submitForm = async (data: costCenterType) => {
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
                    navigate('/costCenter');
                },
                null,
            );
        } catch (err: any) {
            toastMessage(err.message, 'error');
        }
    };

    useEffect(() => {
        departmentsListRefetch();
    }, [departmentsListRefetch]);

    useEffect(() => {
        if (id) {
            detailCostCenterRefetch();
        }
    }, [id, detailCostCenterRefetch]);

    useEffect(() => {
        if (detailCostCenter?.data) {
            Object.keys(detailCostCenter.data).forEach((key) => {
                setValue(key as keyof costCenterType, detailCostCenter.data[key]);
            });
        }
    }, [detailCostCenter, setValue, departmentsList]);

    useEffect(() => {
        dispatch(setPageTitle(t('master.cost_center')));
        dispatch(setTitle(t('master.cost_center')));
        if (type === 'create') {
            dispatch(setBreadcrumbTitle(['Dashboard', t('master.cost_center'), t('common.add_new')]));
        } else {
            dispatch(setBreadcrumbTitle(['Dashboard', t('master.cost_center'), t('common.edit'), lastSegment]));
        }
    }, [dispatch, type, lastSegment]);

    return (
        <div className="space-y-6">
            <Card>
                <CardHeader className="flex flex-row items-center justify-between border-b border-gray-100 pb-4">
                    <CardTitle className="text-lg">
                        {id ? t('master.cost_center_page.edit_title') : t('master.cost_center_page.create_title')}
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

                    <form className="space-y-6" onSubmit={handleSubmit(submitForm)}>
                        <div className="grid md:grid-cols-2 gap-4 w-full">
                            <div className="space-y-2">
                                <Label htmlFor="costCenterCode">{t('master.cost_center_page.form.code')}</Label>
                                <Input
                                    id="costCenterCode"
                                    type="text"
                                    placeholder={t('master.cost_center_page.form.enter_code')}
                                    {...register('costCenterCode')}
                                    readOnly={!editMode}
                                />
                                <span className="text-danger text-xs">
                                    {(errors.costCenterCode as FieldError)?.message}
                                </span>
                            </div>
                            <div className="space-y-2">
                                <Label htmlFor="costCenterName">{t('master.cost_center_page.form.name')}</Label>
                                <Input
                                    id="costCenterName"
                                    type="text"
                                    placeholder={t('master.cost_center_page.form.enter_name')}
                                    {...register('costCenterName')}
                                    readOnly={!editMode}
                                />
                                <span className="text-danger text-xs">
                                    {(errors.costCenterName as FieldError)?.message}
                                </span>
                            </div>
                            <div className="space-y-2">
                                <Label htmlFor="departmentId">{t('master.cost_center_page.form.department')}</Label>
                                <select
                                    id="departmentId"
                                    {...register('departmentId')}
                                    className="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                    disabled={!editMode}
                                    defaultValue={detailCostCenter?.data?.departmentId || ''}
                                >
                                    <option value="">{t('master.cost_center_page.form.select_department')}</option>
                                    {departmentsList?.data?.data?.map((d: DepartmentType, i: number) => (
                                        <option key={i} value={d.departmentId}>
                                            {d.departmentName}
                                        </option>
                                    ))}
                                </select>
                                <span className="text-danger text-xs">
                                    {(errors.departmentId as FieldError)?.message}
                                </span>
                            </div>
                        </div>


                        <div className="flex w-full justify-end gap-4">
                            <Button
                                type="button"
                                variant="outline"
                                onClick={() => navigate('/CostCenter')}
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
