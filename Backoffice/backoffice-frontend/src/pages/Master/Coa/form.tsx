import { useDispatch } from 'react-redux';
import { Link, useLocation, useNavigate, useParams } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import { useEffect, useRef, useState, useMemo } from 'react';
import { setPageTitle, setBreadcrumbTitle, setTitle } from '../../../store/themeConfigSlice';
import { useForm, FieldError } from 'react-hook-form';
import * as yup from 'yup';
import { yupResolver } from '@hookform/resolvers/yup';
import { COAType, OptionType } from '@/types';
import { useGetDetailCOAQuery, usePostCOAMutation, useUpdateCOAMutation, useGetOptionCOAQuery } from '@/store/api/coa/coaApiSlice';
import { useGetOptionAccountTypeOptionQuery } from '@/store/api/accountType/accountTypeApiSlice';
import { useGetOptionAccountGroupDetailQuery } from '@/store/api/accountGroup/accountGroupApiSlice';
import { useGetOptionCurrencyQuery } from '@/store/api/currency/currencyApiSlice';
import { toastMessage } from '@/utils/toastUtils';
import { responseCallback } from '@/utils/responseCallback';
import SelectSearch from 'react-select';
import ConfirmationModal from '@/components/ConfirmationModal';
import { Eye, Pencil } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { reactSelectCustomStyles } from '@/utils/reactSelectCustomStyle';

const Form = () => {
    const accountTypeRef = useRef<HTMLSelectElement>(null);
    const accountGroupRef = useRef<HTMLSelectElement>(null);
    const currencyRef = useRef<HTMLSelectElement>(null);
    const [parentId, setParentId] = useState<string>('');
    const [searchParent, setSearchParent] = useState<string>('');
    const [post, { isLoading: isLoadingPost }] = usePostCOAMutation();
    const [update, { isLoading: isLoadingUpdate }] = useUpdateCOAMutation();
    const dispatch = useDispatch();
    const { t } = useTranslation();
    const location = useLocation();
    const navigate = useNavigate();
    const pathSegments = location.pathname.split('/');
    const lastSegment = pathSegments[pathSegments.length - 1];
    const type = pathSegments[2];
    const { id } = useParams();

    // 🔹 state tambahan
    const [selectedParent, setSelectedParent] = useState<any>(null);
    const [injectedOptions, setInjectedOptions] = useState<any[]>([]);
    const [editMode, setEditMode] = useState(id ? false : true);
    const [showConfirmModal, setShowConfirmModal] = useState(false);

    const status = 'Active';
    const { data: detailCOA, refetch: detailCOARefetch } = id ? useGetDetailCOAQuery<any>(id, { refetchOnMountOrArgChange: true }) : { data: null, refetch: () => { } };
    const { data: accountTypeList } = useGetOptionAccountTypeOptionQuery<any>(
        { orderBy: 'accountTypeId', orderType: 'asc', pageSize: 20, status },
        { skip: !status }
    );
    const { data: accountGroupList } = useGetOptionAccountGroupDetailQuery(
        { orderBy: 'accountGroupId', orderType: 'asc', pageSize: 20, status },
        { skip: !status }
    );

    const { data: CurrencyList } = useGetOptionCurrencyQuery({
        orderBy: 'currencyName',
        orderType: 'asc',
        pageSize: 20,
    });

    const { data: ParentList, refetch: ParentListRefetch } = useGetOptionCOAQuery<any>({
        orderBy: 'coaCode',
        orderType: 'asc',
        pageSize: 20,
        status: 'Active',
        keyword: searchParent,
    });

    const parentOptions = useMemo(() => {
        const opts: any[] = [{ value: '', label: t('master.coa.form.none'), level: '' }];
        if (ParentList?.data && Array.isArray(ParentList.data)) {
            ParentList.data.forEach((option: any) => {
                opts.push({
                    value: option.value,
                    label: option.label,
                    level: option.level ?? '',
                });
            });
        }
        return [...opts, ...injectedOptions];
    }, [ParentList, injectedOptions]);

    useEffect(() => {
        if (detailCOA?.data?.parentId) {
            let opt = parentOptions.find((p) => String(p.value) === String(detailCOA.data.parentId));
            if (!opt) {
                opt = {
                    value: detailCOA.data.parentId,
                    label: detailCOA.data.parentName || `Parent #${detailCOA.data.parentId}`,
                };
                setInjectedOptions((prev) => [...prev, opt]);
            }
            setSelectedParent(opt);
            setParentId(opt.value);
        }
    }, [detailCOA, parentOptions]);

    useEffect(() => {
        const t = setTimeout(() => {
            ParentListRefetch();
        }, 300);
        return () => clearTimeout(t);
    }, [searchParent, ParentListRefetch]);

    const schema = yup
        .object({
            coaCode: yup.string().required(t('validation.required')),
            coaName: yup.string().required(t('validation.required')),
            accountTypeId: yup.number().required(t('validation.required')),
            accountGroupId: yup.number().required(t('validation.required')),
            status: yup.string().required(t('validation.required')),
        })
        .required();

    const {
        register,
        formState: { errors },
        handleSubmit,
        setValue,
        trigger,
    } = useForm<COAType>({
        resolver: yupResolver(schema),
        mode: 'all',
        defaultValues: {
            status: 'Active',
            normalPosition: 'C',
        },
    });

    const submitForm = async (data: COAType) => {
        try {
            let response: any;
            if (accountTypeRef.current) {
                data.accountTypeName = accountTypeRef.current.options[data.accountTypeId].text;
            }
            if (accountGroupRef.current) {
                data.accountGroupName = accountGroupRef.current.options[data.accountGroupId].text;
            }
            if (currencyRef.current) {
                data.currencyName = currencyRef.current.options[data.currencyId].text;
            }
            if (parentId !== '' && parentId !== 'None') {
                data.parentId = Number(parentId);
            }

            if (id) {
                response = await update(data);
            } else {
                // normalPosition kini diambil dari form (default 'C'); jangan di-hardcode lagi.
                data.normalPosition = data.normalPosition || 'C';
                data.balance = 0;
                data.isCashFlow = false;
                data.isCashBank = false;
                data.journals = [];
                response = await post(data);
            }

            responseCallback(response, () => {
                navigate('/coa');
            }, null);
        } catch (err: any) {
            toastMessage(err.message, 'error');
        }
    };

    useEffect(() => {
        dispatch(setPageTitle(t('master.coa.title')));
        dispatch(setTitle(t('master.coa.title')));
        if (type === 'create') {
            dispatch(setBreadcrumbTitle(['Dashboard', 'Master', 'COA', type]));
        } else {
            dispatch(setBreadcrumbTitle(['Dashboard', 'Master', 'COA', type, lastSegment]));
        }
        ParentListRefetch();
    }, [dispatch]);

    useEffect(() => {
        if (id) detailCOARefetch();
    }, [id]);

    useEffect(() => {
        if (detailCOA?.data) {
            Object.keys(detailCOA.data).forEach((key) => {
                setValue(key as keyof COAType, detailCOA.data[key]);
            });
        }
    }, [detailCOA, accountTypeList, accountGroupList, setValue]);

    return (
        <div>
            <Card>
                <CardHeader className="flex flex-row items-center justify-between border-b border-gray-100 pb-4">
                    <CardTitle className="text-lg">
                        {id ? t('master.coa.edit_title') : t('master.coa.create_title')}
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
                                    <span>{t('master.coa.list')}</span>
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
                                <Label htmlFor="coaCode">{t('master.coa.form.account_no')}</Label>
                                <Input
                                    id="coaCode"
                                    type="text"
                                    placeholder={t('master.coa.form.enter_account_no')}
                                    {...register('coaCode')}
                                    disabled={!editMode}
                                    className={!editMode ? "text-gray-500" : ""}
                                />
                                {errors.coaCode && <span className="text-danger text-xs">{(errors.coaCode as FieldError)?.message}</span>}
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="coaName">{t('master.coa.form.account_name')}</Label>
                                <Input
                                    id="coaName"
                                    type="text"
                                    placeholder={t('master.coa.form.enter_account_name')}
                                    {...register('coaName')}
                                    disabled={!editMode}
                                    className={!editMode ? "text-gray-500" : ""}
                                />
                                {errors.coaName && <span className="text-danger text-xs">{(errors.coaName as FieldError)?.message}</span>}
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="accountTypeId">{t('master.coa.form.account_type')}</Label>
                                <select
                                    id="accountTypeId"
                                    {...register('accountTypeId', { setValueAs: (v) => (v === '' ? undefined : Number(v)) })}
                                    className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                    disabled={!editMode}
                                >
                                    <option value="">{t('master.coa.form.enter_account_type')}</option>
                                    {accountTypeList?.data?.map((d: OptionType, i: number) => (
                                        <option key={i} value={d?.value}>
                                            {d?.label}
                                        </option>
                                    ))}
                                </select>
                                {errors.accountTypeId && <span className="text-danger text-xs">{(errors.accountTypeId as FieldError)?.message}</span>}
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="accountGroupId">{t('master.coa.form.account_group')}</Label>
                                <select
                                    id="accountGroupId"
                                    {...register('accountGroupId', { setValueAs: (v) => (v === '' ? undefined : Number(v)) })}
                                    className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                    disabled={!editMode}
                                >
                                    <option value="">{t('master.coa.form.enter_account_group')}</option>
                                    {accountGroupList?.data?.map((d: OptionType, i: number) => (
                                        <option key={i} value={d?.value}>
                                            {d?.label}
                                        </option>
                                    ))}
                                </select>
                                {errors.accountGroupId && <span className="text-danger text-xs">{(errors.accountGroupId as FieldError)?.message}</span>}
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="parentId">{t('master.coa.form.parent_account')}</Label>
                                <SelectSearch
                                    id="parentId"
                                    placeholder={t('master.coa.form.enter_parent')}
                                    options={parentOptions}
                                    onInputChange={(inputValue) => setSearchParent(inputValue || '')}
                                    onChange={(opt: any) => {
                                        setSelectedParent(opt);
                                        setParentId(opt?.value || '');
                                    }}
                                    value={selectedParent}
                                    isDisabled={!editMode}
                                    styles={reactSelectCustomStyles}
                                />
                                {errors.parentId && <span className="text-danger text-xs">{(errors.parentId as FieldError)?.message}</span>}
                            </div>

                            <div className="space-y-2">
                                <Label>{t('master.coa.form.status')}</Label>
                                <div className="flex space-x-4">
                                    <label className="flex items-center">
                                        <input type="radio" value="Active" {...register('status')} className="form-radio" disabled={!editMode} />
                                        <span className="ml-2">{t('master.coa.form.active')}</span>
                                    </label>
                                    <label className="flex items-center">
                                        <input type="radio" value="InActive" {...register('status')} className="form-radio" disabled={!editMode} />
                                        <span className="ml-2">{t('master.coa.form.inactive')}</span>
                                    </label>
                                </div>
                                {errors.status && <span className="text-danger text-xs">{(errors.status as FieldError)?.message}</span>}
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="normalPosition">{t('master.coa.form.normal_position')}</Label>
                                <select
                                    id="normalPosition"
                                    {...register('normalPosition')}
                                    className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                    disabled={!editMode}
                                >
                                    <option value="C">{t('master.coa.form.normal_credit')}</option>
                                    <option value="D">{t('master.coa.form.normal_debit')}</option>
                                </select>
                                <p className="text-xs text-muted-foreground">{t('master.coa.form.normal_position_hint')}</p>
                                {errors.normalPosition && <span className="text-danger text-xs">{(errors.normalPosition as FieldError)?.message}</span>}
                            </div>
                        </div>

                        <div className="flex w-full justify-end gap-4">
                            <Button
                                type="button"
                                variant="outline"
                                onClick={() => navigate('/coa')}
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
                                    {isLoadingPost || isLoadingUpdate ? t('common.loading') : id ? t('common.save') : t('common.save')}
                                </Button>
                            )}
                        </div>
                    </form>
                </CardContent>
            </Card>

            {/* 🔹 Confirmation Modal */}
            <ConfirmationModal
                isOpen={showConfirmModal}
                onClose={() => (!isLoadingPost && !isLoadingUpdate) && setShowConfirmModal(false)}
                onConfirm={handleSubmit(submitForm)}
                title={t('messages.confirmation')}
                message={id ? t('messages.update_confirm') : t('messages.save_confirm')}
                confirmText={id ? t('common.save') : t('common.save')}
                cancelText={t('common.cancel')}
                loading={isLoadingPost || isLoadingUpdate}
            />
        </div>
    );
};

export default Form;
