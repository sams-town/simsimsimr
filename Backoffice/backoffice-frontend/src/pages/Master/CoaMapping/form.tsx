import { useDispatch } from 'react-redux';
import { useTranslation } from 'react-i18next';
import { useLocation, useNavigate, useParams } from 'react-router-dom';
import { useEffect, useState } from 'react';
import { setPageTitle, setTitle, setBreadcrumbTitle } from '../../../store/themeConfigSlice';
import { useForm, FieldError } from 'react-hook-form';
import * as yup from 'yup';
import { yupResolver } from '@hookform/resolvers/yup';
import {
    useUpdateCoaMappingMutation,
    useCreateCoaMappingMutation,
    useGetCoaMappingDetailQuery,
} from '@/store/api/coaMapping/coaMappingApiSlice';
import { responseCallback } from '@/utils/responseCallback';
import { toastMessage } from '@/utils/toastUtils';
import { CoaMappingType } from '@/types/coaMappingType';
import { COAType } from '@/types';
import { Eye, Pencil, Search } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import ConfirmationModal from '@/components/ConfirmationModal';
import ModalCoaCustom from '@/components/ModalCoaCustom';

const Form = () => {
    const dispatch = useDispatch();
    const { t } = useTranslation();
    const navigate = useNavigate();
    const location = useLocation();
    const { id } = useParams();

    const pathSegments = location.pathname.split('/');
    const type = pathSegments[2];

    const [editMode, setEditMode] = useState(id ? false : true);
    const [showConfirmModal, setShowConfirmModal] = useState(false);
    const [showCoaModal, setShowCoaModal] = useState(false);
    const [selectedRecords, setSelectedRecords] = useState<COAType[]>([]);
    const [isSave, setIsSave] = useState(false);
    const [coaDestDisplay, setCoaDestDisplay] = useState<string>('');

    const { data: detailCoaMapping, refetch: refetchDetail } = id
        ? useGetCoaMappingDetailQuery<any>(Number(id), { refetchOnMountOrArgChange: true })
        : { data: null, refetch: () => {} };

    const [createCoaMapping, { isLoading: isCreating }] = useCreateCoaMappingMutation();
    const [updateCoaMapping, { isLoading: isUpdating }] = useUpdateCoaMappingMutation();

    const schema = yup
        .object({
            coaSource: yup.string().required(t('validation.required')),
            coaSourceName: yup.string().nullable(),
            coaDest: yup.string().required(t('validation.required')),
        })
        .required();

    const {
        register,
        formState: { errors },
        handleSubmit,
        setValue,
        trigger,
        getValues,
    } = useForm<CoaMappingType>({
        resolver: yupResolver(schema),
    });

    const submitForm = async (data: CoaMappingType) => {
        try {
            let response: any;
            if (id) {
                response = await updateCoaMapping({ ...data, id: Number(id) });
            } else {
                response = await createCoaMapping(data);
            }
            responseCallback(response, () => {
                navigate('/coa-mapping');
            }, null);
        } catch (err: any) {
            toastMessage(err.message, 'error');
        }
    };

    useEffect(() => {
        dispatch(setPageTitle(t('master.coa_mapping_page.title')));
        dispatch(setTitle(t('master.coa_mapping_page.title')));

        if (type === 'create') {
            dispatch(setBreadcrumbTitle(['Dashboard', 'Master', t('master.coa_mapping_page.title'), type]));
        } else {
            dispatch(setBreadcrumbTitle(['Dashboard', 'Master', t('master.coa_mapping_page.title'), type, id || '']));
        }

        if (id) {
            refetchDetail();
        }
    }, [dispatch, id, refetchDetail]);

    useEffect(() => {
        if (detailCoaMapping && 'data' in detailCoaMapping) {
            if (detailCoaMapping?.data) {
                const d = detailCoaMapping.data;
                setValue('coaSource', d.coaSource);
                setValue('coaSourceName', d.coaSourceName);
                setValue('coaDest', d.coaDest);
                setCoaDestDisplay(d.coaDestName ? `${d.coaDest} - ${d.coaDestName}` : d.coaDest || '');
            }
        }
    }, [detailCoaMapping, setValue]);

    // Handle COA picker save
    useEffect(() => {
        if (isSave && selectedRecords.length > 0) {
            const coa = selectedRecords[selectedRecords.length - 1];
            setValue('coaDest', coa.coaCode);
            setCoaDestDisplay(`${coa.coaCode} - ${coa.coaName}`);
            setIsSave(false);
            setSelectedRecords([]);
        }
    }, [isSave, selectedRecords, setValue]);

    return (
        <div>
            <Card>
                <CardHeader className="flex flex-row items-center justify-between border-b border-gray-100 pb-4">
                    <CardTitle className="text-lg">
                        {id ? t('master.coa_mapping_page.edit_title') : t('master.coa_mapping_page.create_title')}
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
                                    <span>{t('common.view')}</span>
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
                            {/* COA Source */}
                            <div className="space-y-2">
                                <Label htmlFor="coaSource">{t('master.coa_mapping_page.form.coa_source')}</Label>
                                <Input
                                    id="coaSource"
                                    type="text"
                                    placeholder={t('master.coa_mapping_page.form.enter_coa_source')}
                                    {...register('coaSource')}
                                    readOnly={!editMode}
                                />
                                {errors.coaSource && (
                                    <span className="text-danger text-xs">
                                        {(errors.coaSource as FieldError)?.message}
                                    </span>
                                )}
                            </div>

                            {/* COA Source Name */}
                            <div className="space-y-2">
                                <Label htmlFor="coaSourceName">{t('master.coa_mapping_page.form.coa_source_name')}</Label>
                                <Input
                                    id="coaSourceName"
                                    type="text"
                                    placeholder={t('master.coa_mapping_page.form.enter_coa_source_name')}
                                    {...register('coaSourceName')}
                                    readOnly={!editMode}
                                />
                            </div>

                            {/* COA Dest with picker */}
                            <div className="space-y-2">
                                <Label htmlFor="coaDest">{t('master.coa_mapping_page.form.coa_dest')}</Label>
                                <div className="flex gap-2">
                                    <Input
                                        id="coaDest"
                                        type="text"
                                        value={coaDestDisplay}
                                        placeholder={t('master.coa_mapping_page.form.select_coa_dest')}
                                        readOnly
                                        className="flex-1"
                                    />
                                    <input type="hidden" {...register('coaDest')} />
                                    {editMode && (
                                        <Button
                                            type="button"
                                            variant="outline"
                                            onClick={() => setShowCoaModal(true)}
                                        >
                                            <Search className="h-4 w-4" />
                                        </Button>
                                    )}
                                </div>
                                {errors.coaDest && (
                                    <span className="text-danger text-xs">
                                        {(errors.coaDest as FieldError)?.message}
                                    </span>
                                )}
                            </div>
                        </div>

                        <div className="flex w-full justify-end gap-4">
                            <Button
                                type="button"
                                variant="outline"
                                onClick={() => navigate('/coa-mapping')}
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

            {/* COA Picker Modal */}
            <ModalCoaCustom
                setIsSave={setIsSave}
                selectedRecords={selectedRecords}
                setSelectedRecords={setSelectedRecords}
                showModal={showCoaModal}
                setShowSelected={setSelectedRecords}
                setIsShowModal={setShowCoaModal}
                excludeId={null}
            />

            {/* Confirmation Modal */}
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
