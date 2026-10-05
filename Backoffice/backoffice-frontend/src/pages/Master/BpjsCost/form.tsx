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
import { BpjsCostType } from '@/types';
import {
    useCreateBpjsCostMutation,
    useUpdateBpjsCostMutation,
    useGetBpjsCostDetailQuery,
} from '@/store/api/bpjsCost/bpjsCostApiSlice';
import { Eye, Pencil } from 'lucide-react';
import ConfirmationModal from '@/components/ConfirmationModal';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

const bpjsCostForm = () => {
    const { t } = useTranslation();
    const dispatch = useDispatch();
    const navigate = useNavigate();
    const location = useLocation();
    const { id } = useParams();
    const pathSegments = location.pathname.split('/');
    const lastSegment = pathSegments[pathSegments.length - 1];
    const type = pathSegments[2];

    const { data: detailBpjsCost } = id
        ? useGetBpjsCostDetailQuery(id, { refetchOnMountOrArgChange: true })
        : { data: null };

    const [createBpjsCost, { isLoading: isCreating }] = useCreateBpjsCostMutation();
    const [updateBpjsCost, { isLoading: isUpdating }] = useUpdateBpjsCostMutation();

    const [editMode, setEditMode] = useState(id ? false : true);
    const [showConfirmModal, setShowConfirmModal] = useState(false);

    const schema = yup.object({
        procedure: yup.string().required(t('validation.required')),
        cost: yup
            .number()
            .typeError(t('validation.number'))
            .required(t('validation.required'))
            .min(0, t('validation.min', { min: 0 })),
    });

    const {
        register,
        formState: { errors },
        handleSubmit,
        setValue,
        trigger,
    } = useForm<BpjsCostType>({
        resolver: yupResolver(schema),
    });

    const onSubmit = async (data: BpjsCostType) => {
        try {
            let response: any;
            if (id) {
                response = await updateBpjsCost(data);
            } else {
                response = await createBpjsCost(data);
            }
            responseCallback(response, () => {
                navigate('/bpjs-cost');
            }, null);
        } catch (err: any) {
            toastMessage(err.message, 'error');
        }
    };

    useEffect(() => {
        dispatch(setPageTitle(t('master.bpjs_cost_page.title')));
        dispatch(setTitle(t('master.bpjs_cost_page.title')));
        if (type === 'create') {
            dispatch(setBreadcrumbTitle(['Dashboard', t('master.title'), t('master.bpjs_cost_page.title'), t('common.add_new')]));
        } else {
            dispatch(setBreadcrumbTitle(['Dashboard', t('master.title'), t('master.bpjs_cost_page.title'), t('common.edit'), lastSegment]));
        }
    }, [dispatch, id, type, lastSegment]);

    useEffect(() => {
        if (detailBpjsCost?.data) {
            Object.keys(detailBpjsCost.data).forEach((key) => {
                setValue(key as keyof BpjsCostType, detailBpjsCost.data[key]);
            });
        }
    }, [detailBpjsCost, setValue]);

    return (
        <div className="space-y-6">
            <Card>
                <CardHeader className="flex flex-row items-center justify-between border-b border-gray-100 pb-4">
                    <CardTitle className="text-lg">
                        {id ? t('master.bpjs_cost_page.edit_title') : t('master.bpjs_cost_page.create_title')}
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
                                <Label htmlFor="procedure">{t('master.bpjs_cost_page.form.procedure')}</Label>
                                <Input
                                    id="procedure"
                                    type="text"
                                    placeholder={t('master.bpjs_cost_page.form.enter_procedure')}
                                    {...register('procedure')}
                                    readOnly={!editMode}
                                />
                                <span className="text-danger text-xs">
                                    {(errors.procedure as FieldError)?.message}
                                </span>
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="cost">{t('master.bpjs_cost_page.form.cost')}</Label>
                                <Input
                                    id="cost"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    placeholder={t('master.bpjs_cost_page.form.enter_cost')}
                                    {...register('cost')}
                                    readOnly={!editMode}
                                />
                                <span className="text-danger text-xs">
                                    {(errors.cost as FieldError)?.message}
                                </span>
                            </div>
                        </div>

                        <div className="flex w-full justify-end gap-4">
                            <Button
                                type="button"
                                variant="outline"
                                onClick={() => navigate('/bpjs-cost')}
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

export default bpjsCostForm;
