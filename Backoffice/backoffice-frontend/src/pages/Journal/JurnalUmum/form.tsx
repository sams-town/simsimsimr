import ConfirmationModal from '@/components/ConfirmationModal';
import ModalCoaCustom from '@/components/ModalCoaCustom';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    useCreateGeneralJournalMutation,
    useGetGeneralJournalDetailQuery,
    useUpdateGeneralJournalMutation,
} from '@/store/api/journal/generalJournalApiSlice';
import { COAType } from '@/types';
import { GeneralJournalType, GeneralJournalUpdateType, JournalEntryDetail } from '@/types/generalJournalType';
import { pembilang } from '@/utils/currencyHelper';
import { responseCallback } from '@/utils/responseCallback';
import { toastMessage } from '@/utils/toastUtils';
import { yupResolver } from '@hookform/resolvers/yup';
import Tippy from '@tippyjs/react';
import { useTranslation } from 'react-i18next';
import 'flatpickr/dist/flatpickr.css';
import { ArrowLeft, Plus, Save, Trash2 } from 'lucide-react';
import React, { useEffect, useState } from 'react';
import Flatpickr from 'react-flatpickr';
import { useFieldArray, useForm } from 'react-hook-form';
import { useDispatch, useSelector } from 'react-redux';
import { useLocation, useNavigate, useParams } from 'react-router-dom';
import 'tippy.js/dist/tippy.css';
import * as yup from 'yup';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '../../../store/themeConfigSlice';
import IconCalendar from '@/components/Icon/IconCalendar';
import { FormatNumber } from '@/utils/formatNumber';

const JurnalUmumForm = () => {
    const dispatch = useDispatch();
    const navigate = useNavigate();
    const { t } = useTranslation();
    const { id } = useParams();
    const location = useLocation();
    const isViewMode = !!id && (location.pathname.includes('/view/') || location.pathname.includes('/detail/'));
    const isUpdateMode = !!id && location.pathname.includes('/update/');
    const isRtl = useSelector((state: any) => state.themeConfig.rtlClass) === 'rtl';

    const { data: detailTrans, refetch: refetchdetailTrans } = id
        ? useGetGeneralJournalDetailQuery<any>(id)
        : { data: null, refetch: () => { } };

    const [createJournal, { isLoading: isCreating }] = useCreateGeneralJournalMutation();
    const [updateJournal, { isLoading: isUpdating }] = useUpdateGeneralJournalMutation();

    const [transactionIdToUpdate, setTransactionIdToUpdate] = useState<string | null>(null);
    const [isShowModalCoa, setIsShowModalCoa] = useState<boolean>(false);
    const [selectedRecords, setSelectedRecords] = useState<COAType[]>([]);
    const [showSelected, setShowSelected] = useState<COAType[]>([]);
    const [isSave, setIsSave] = useState<boolean>(false);
    const [isLocked, setIsLocked] = useState<boolean>(false);
    const [statusMessage, setStatusMessage] = useState<string>('');
    const [showConfirmModal, setShowConfirmModal] = useState(false);

    // Form Schema
    const schema = yup
        .object({
            transactionDate: yup
                .string()
                .required(t('transaction.general_journal_page.validation.date_required'))
                .test('valid-date', t('transaction.general_journal_page.validation.date_invalid'), (value) => {
                    if (!value) return false;
                    const date = new Date(value);
                    return !isNaN(date.getTime());
                }),
            description: yup.string().required(t('transaction.general_journal_page.validation.desc_required')),
            details: yup
                .array()
                .of(
                    yup.object().shape({
                        description: yup.string().required(t('transaction.general_journal_page.validation.line_desc_required')),
                        debit: yup.number().min(0).transform((value) => (isNaN(value) ? 0 : value)),
                        credit: yup.number().min(0).transform((value) => (isNaN(value) ? 0 : value)),
                        coaCode: yup.string().required(),
                    }).test('debit-or-credit', t('transaction.general_journal_page.validation.debit_credit_required'), (obj) => {
                        const debit = obj?.debit || 0;
                        const credit = obj?.credit || 0;
                        return debit > 0 || credit > 0;
                    })
                )

                .required(t('transaction.general_journal_page.validation.details_required'))
                .min(2, t('transaction.general_journal_page.validation.min_lines'))
                .test('balanced', t('transaction.general_journal_page.validation.balanced'), (details) => {
                    if (!details || details.length < 2) return false;
                    const totalDebit = details.reduce((sum, d) => sum + (d.debit || 0), 0);
                    const totalCredit = details.reduce((sum, d) => sum + (d.credit || 0), 0);
                    return Math.abs(totalDebit - totalCredit) < 1; // Tolerance for floating point
                }),
        })
        .required();

    // Extending Form Type to include Debit/Credit mapping helper fields
    type FormType = {
        transactionDate: string;
        transactionNo: string;
        description: string;
        details: {
            journalId?: number;
            coaCode: string;
            coaName: string;
            description: string;
            debit: number;
            credit: number;
        }[]
    };

    const {
        register,
        control,
        formState: { errors },
        handleSubmit,
        setValue,
        watch,
        trigger,
        reset,
    } = useForm<FormType>({
        resolver: yupResolver(schema) as any,
        defaultValues: {
            transactionDate: '',
            description: '',
            transactionNo: '',
            details: [],
        },
    });

    const { fields, append, remove, update } = useFieldArray({
        control,
        name: 'details',
    });

    const [totalDebit, setTotalDebit] = useState(0);
    const [totalCredit, setTotalCredit] = useState(0);

    // Watch for totals calculation using subscription pattern
    useEffect(() => {
        const subscription = watch((value) => {
            const deb = value.details?.reduce((sum, d) => sum + (Number(d?.debit) || 0), 0) || 0;
            const cred = value.details?.reduce((sum, d) => sum + (Number(d?.credit) || 0), 0) || 0;
            setTotalDebit(deb);
            setTotalCredit(cred);
        });
        return () => subscription.unsubscribe();
    }, [watch]);

    // Handle COA Selection from Modal
    useEffect(() => {
        if (isSave && showSelected.length > 0) {
            showSelected.forEach(coa => {
                // Check if this COA is already in the fields list
                const exists = fields.some(f => f.coaCode === coa.coaCode);
                if (!exists) {
                    append({
                        coaCode: coa.coaCode,
                        coaName: coa.coaName,
                        description: '',
                        debit: 0,
                        credit: 0
                    });
                }
            });
            setIsSave(false);
        }
    }, [isSave, showSelected, append, fields]);

    const onSubmit = async (data: FormType) => {
        try {
            // Transformation to API Model
            // First detail becomes the "premier" account (source/destination equivalent for General Journal)
            const apiDetails: JournalEntryDetail[] = data.details.map((d, index) => {
                const isDebit = (d.debit || 0) > 0;
                return {
                    journalId: d.journalId,
                    coaCode: d.coaCode,
                    description: d.description,
                    amount: isDebit ? d.debit : d.credit,
                    position: isDebit ? 'Debit' : 'Credit',
                    isPremier: false,
                };
            });

            // Use first detail's coaCode as the transaction-level coaCode (required by backend)
            const premierCoaCode = data.details.length > 0 ? data.details[0].coaCode : undefined;

            const commonData = {
                transactionDate: data.transactionDate,
                transactionNo: data.transactionNo,
                description: data.description,
                transactionType: 'JournalEntry',
                coaCode: premierCoaCode, // Set premier account code
                details: apiDetails
            };

            let response;
            if (id) {
                const updateData: GeneralJournalUpdateType = {
                    ...commonData,
                    transactionId: transactionIdToUpdate!,
                };
                response = await updateJournal(updateData);
            } else {
                response = await createJournal(commonData);
            }

            responseCallback(
                response,
                () => {
                    navigate('/jurnal-umum');
                },
                null
            );
        } catch (err: any) {
            toastMessage(err.message, 'error');
        }
    };

    useEffect(() => {
        const title = isUpdateMode
            ? t('transaction.general_journal_page.form.update_journal')
            : isViewMode
                ? t('transaction.general_journal_page.form.detail_title')
                : t('transaction.general_journal_page.form.create_title');

        dispatch(setPageTitle(title));
        dispatch(setTitle(title));
        dispatch(setBreadcrumbTitle([t('menu.dashboard'), t('menu.journal'), t('transaction.general_journal_page.title'), isUpdateMode ? t('common.update') : isViewMode ? t('common.view') : t('common.create')]));

        if (id) {
            refetchdetailTrans();
        }
    }, [dispatch, id, refetchdetailTrans, isViewMode, isUpdateMode]);

    useEffect(() => {
        if (detailTrans && detailTrans.data) {
            const data = detailTrans.data;

            if (isUpdateMode && data.status !== 'Pending') {
                setIsLocked(true);
                setStatusMessage(`${t('transaction.general_journal_page.form.read_only_msg')}${data.status}.`);
            } else {
                setIsLocked(false);
                setStatusMessage('');
            }

            setTransactionIdToUpdate(detailTrans.data.transactionId);

            // Map API details to Form details
            const formattedDetails = data.details.map((apiDetail: any) => ({
                journalId: apiDetail.journalId,
                coaCode: apiDetail.coaCode,
                coaName: apiDetail.coaName,
                description: apiDetail.journalDesc,
                debit: apiDetail.debitAmount || 0,
                credit: apiDetail.creditAmount || 0
            })).sort((a: any, b: any) => {
                const aIsDebit = a.debit > 0 ? 0 : 1;
                const bIsDebit = b.debit > 0 ? 0 : 1;
                return aIsDebit - bIsDebit;
            });

            reset({
                transactionNo: data.transactionNo || '',
                transactionDate: data.transactionDate || '',
                description: data.description || '',
                details: formattedDetails,
            });
        }
    }, [detailTrans, reset, isUpdateMode]);

    return (
        <div className="flex flex-col gap-6 max-w-5xl mx-auto">
            {/* Header / Title */}
            <div className="flex items-center justify-between">
                <div>
                    <h1 className="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                        {isUpdateMode ? t('transaction.general_journal_page.form.update_journal') : isViewMode ? t('transaction.general_journal_page.form.detail_title') : t('transaction.general_journal_page.form.create_title')}
                    </h1>
                    <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        {isUpdateMode ? t('transaction.general_journal_page.form.subtitle_edit') : t('transaction.general_journal_page.form.subtitle_create')}
                    </p>
                </div>
                <Button
                    type="button"
                    variant="outline"
                    onClick={() => navigate('/jurnal-umum')}
                    className="gap-2 shadow-sm bg-white dark:bg-black"
                    size="sm"
                >
                    <ArrowLeft className="h-4 w-4" />
                    {t('common.back')}
                </Button>
            </div>

            {isLocked && (
                <div className="bg-amber-50 border-l-4 border-amber-500 text-amber-900 p-4 rounded-md shadow-sm flex items-start gap-3">
                    <div className="mt-0.5">
                        <Tippy content="Info">
                            <span className="bg-amber-100 text-amber-600 rounded-full w-5 h-5 flex items-center justify-center font-bold text-xs">!</span>
                        </Tippy>
                    </div>
                    <div>
                        <p className="font-bold">{t('transaction.general_journal_page.form.read_only')}</p>
                        <p className="text-sm opacity-90">{statusMessage}</p>
                    </div>
                </div>
            )}

            <form className="space-y-6" onSubmit={handleSubmit(onSubmit)}>
                {/* Main Information Card */}
                <Card className="shadow-sm border border-gray-200 dark:border-gray-800">
                    <CardHeader className="bg-gray-50/50 dark:bg-[#1b2e4b] border-b border-gray-100 dark:border-gray-700 px-6 py-4">
                        <CardTitle className="text-base font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                            <span className="bg-primary/10 text-primary w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold">1</span>
                            {t('transaction.general_journal_page.form.journal_info')}
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="p-6">
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {/* Transaction No */}
                            {/* Transaction No */}
                            <div className="space-y-2">
                                <Label htmlFor="transactionNo" className="text-gray-700 dark:text-gray-300 font-medium">{t('transaction.general_journal_page.form.transaction_no')} <span className="text-gray-400 font-normal text-xs ml-1">(Optional)</span></Label>
                                <Input
                                    id="transactionNo"
                                    placeholder={t('transaction.general_journal_page.form.enter_transaction_no')}
                                    {...register('transactionNo')}
                                    disabled={isLocked || isViewMode}
                                    className={isLocked || isViewMode ? 'bg-gray-100 cursor-not-allowed' : ''}
                                />
                            </div>

                            {/* Transaction Date */}
                            <div className="space-y-2">
                                <Label htmlFor="transactionDate" className="text-gray-700 dark:text-gray-300 font-medium">{t('transaction.general_journal_page.form.date')} <span className="text-red-500">*</span></Label>
                                <div className="relative">
                                    <Flatpickr
                                        options={{ enableTime: false, dateFormat: 'd F Y', position: isRtl ? 'auto right' : 'auto left' }}
                                        className={`form-input w-full pl-10 transition-all ${errors.transactionDate ? 'border-red-500 focus:ring-red-200' : 'focus:ring-primary/20'} disabled:bg-gray-100 disabled:cursor-not-allowed`}
                                        onChange={(date: Date[]) => !isViewMode && !isLocked && setValue('transactionDate', date[0].toISOString())}
                                        value={watch('transactionDate')}
                                        disabled={isLocked}
                                        placeholder={t('transaction.general_journal_page.form.select_date')}
                                    />
                                    <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                        <IconCalendar className="w-4 h-4" />
                                    </div>
                                </div>
                                {errors.transactionDate && (
                                    <p className="text-xs text-red-500 flex items-center gap-1 mt-1">
                                        <span className="w-1 h-1 bg-red-500 rounded-full inline-block"></span>
                                        {errors.transactionDate.message}
                                    </p>
                                )}
                            </div>

                            {/* Description */}
                            <div className="space-y-2 md:col-span-2">
                                <Label htmlFor="description" className="text-gray-700 dark:text-gray-300 font-medium">{t('transaction.general_journal_page.form.description')} <span className="text-red-500">*</span></Label>
                                <textarea
                                    rows={3}
                                    className={`form-textarea w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary/20 transition-all ${errors.description ? 'border-red-500' : ''}`}
                                    placeholder={t('transaction.general_journal_page.form.enter_description')}
                                    {...register('description')}
                                    disabled={isLocked}
                                />
                                {errors.description && (
                                    <p className="text-xs text-red-500 flex items-center gap-1 mt-1">
                                        <span className="w-1 h-1 bg-red-500 rounded-full inline-block"></span>
                                        {errors.description.message}
                                    </p>
                                )}
                            </div>
                        </div>
                    </CardContent>
                </Card>

                {/* Details Card */}
                <Card className="shadow-sm border border-gray-200 dark:border-gray-800">
                    <CardHeader className="bg-gray-50/50 dark:bg-[#1b2e4b] border-b border-gray-100 dark:border-gray-700 px-6 py-4 flex flex-row items-center justify-between">
                        <CardTitle className="text-base font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                            <span className="bg-primary/10 text-primary w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold">2</span>
                            {t('transaction.general_journal_page.form.journal_details')}
                        </CardTitle>
                        {!isLocked && (
                            <Button
                                onClick={() => setIsShowModalCoa(true)}
                                type="button"
                                className="bg-primary hover:bg-primary/90 text-white shadow-sm gap-2 h-9"
                                size="sm"
                            >
                                <Plus className="h-4 w-4" />
                                {t('transaction.general_journal_page.form.add_account')}
                            </Button>
                        )}
                    </CardHeader>
                    <CardContent className="p-0">
                        <div className="table-responsive">
                            <table className="w-full text-sm text-left">
                                <thead className="bg-gray-50 text-gray-700 font-semibold border-b border-gray-100">
                                    <tr>
                                        <th className="px-6 py-3 w-1/4">{t('transaction.general_journal_page.form.account')}</th>
                                        <th className="px-6 py-3">{t('transaction.general_journal_page.form.line_description')}</th>
                                        <th className="px-6 py-3 w-40 text-right">{t('transaction.general_journal_page.form.debit')}</th>
                                        <th className="px-6 py-3 w-40 text-right">{t('transaction.general_journal_page.form.credit')}</th>
                                        {!isLocked && <th className="px-6 py-3 w-[50px]"></th>}
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-100">
                                    {fields.map((field, index) => (
                                        <React.Fragment key={field.id}>
                                            <tr className="hover:bg-gray-50/50 group">
                                                <td className="px-6 py-4 align-top">
                                                    <div className="font-medium text-gray-900">{field.coaName}</div>
                                                    <div className="text-xs text-gray-500 mt-0.5">{field.coaCode}</div>
                                                </td>
                                                <td className="px-6 py-4 align-top">
                                                    <Input
                                                        {...register(`details.${index}.description` as const)}
                                                        disabled={isLocked || isViewMode}
                                                        placeholder={t('transaction.general_journal_page.form.enter_line_description')}
                                                        className={`h-9 text-sm ${errors.details?.[index]?.description ? 'border-red-500' : 'border-gray-200'}`}
                                                    />
                                                    {errors.details?.[index]?.description && <p className="text-xs text-red-500 mt-1">{t('validation.required')}</p>}
                                                </td>
                                                <td className="px-6 py-4 align-top">
                                                    <div className="relative">
                                                        <span className="absolute left-3 top-2 text-gray-400 text-xs font-medium">Rp</span>
                                                        <Input
                                                            type="number"
                                                            {...register(`details.${index}.debit` as const, { valueAsNumber: true })}
                                                            disabled={isLocked || isViewMode}
                                                            className="h-9 pl-8 text-right font-mono text-sm border-gray-200"
                                                            placeholder="0"
                                                            onFocus={(e) => e.target.select()}
                                                            onBlur={(e) => {
                                                                const val = parseFloat(e.target.value) || 0;
                                                                if (val > 0) setValue(`details.${index}.credit`, 0);
                                                            }}
                                                        />
                                                    </div>
                                                </td>
                                                <td className="px-6 py-4 align-top">
                                                    <div className="relative">
                                                        <span className="absolute left-3 top-2 text-gray-400 text-xs font-medium">Rp</span>
                                                        <Input
                                                            type="number"
                                                            {...register(`details.${index}.credit` as const, { valueAsNumber: true })}
                                                            disabled={isLocked || isViewMode}
                                                            className="h-9 pl-8 text-right font-mono text-sm border-gray-200"
                                                            placeholder="0"
                                                            onFocus={(e) => e.target.select()}
                                                            onBlur={(e) => {
                                                                const val = parseFloat(e.target.value) || 0;
                                                                if (val > 0) setValue(`details.${index}.debit`, 0);
                                                            }}
                                                        />
                                                    </div>
                                                </td>
                                                {!isLocked && (
                                                    <td className="px-6 py-4 align-top text-center">
                                                        <button
                                                            type="button"
                                                            className="text-gray-400 hover:text-red-500 transition-colors p-1 rounded-md hover:bg-red-50 opacity-0 group-hover:opacity-100"
                                                            onClick={() => remove(index)}
                                                        >
                                                            <Trash2 className="h-4 w-4" />
                                                        </button>
                                                    </td>
                                                )}
                                            </tr>
                                            {/* Show row-level validation error */}
                                            {(errors.details as any)?.[index]?.message && (
                                                <tr>
                                                    <td colSpan={5} className="px-6 py-2 bg-red-50 text-red-600 text-xs">
                                                        <span className="font-medium">{field.coaName}:</span> {(errors.details as any)?.[index]?.message}
                                                    </td>
                                                </tr>
                                            )}
                                        </React.Fragment>
                                    ))}
                                    {fields.length === 0 && (
                                        <tr>
                                            <td colSpan={isLocked ? 4 : 5} className="px-6 py-12 text-center text-gray-500">
                                                <div className="flex flex-col items-center justify-center gap-2">
                                                    <div className={`w-12 h-12 rounded-full flex items-center justify-center ${errors.details ? 'bg-red-100 text-red-400' : 'bg-gray-100 text-gray-400'}`}>
                                                        <Plus className="w-6 h-6" />
                                                    </div>
                                                    <p className={`font-medium ${errors.details ? 'text-red-600' : ''}`}>
                                                        {errors.details?.message || t('transaction.general_journal_page.form.no_details')}
                                                    </p>
                                                    <p className="text-xs">{t('transaction.general_journal_page.form.click_add')}</p>
                                                </div>
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                                <tfoot className="bg-gray-50/50 border-t border-gray-100">
                                    <tr>
                                        <td colSpan={2} className="px-6 py-4 text-right font-bold text-gray-700 align-top">
                                            <span className="text-gray-500 text-sm font-medium mr-2">{t('transaction.general_journal_page.form.total')}</span>
                                        </td>
                                        <td className="px-6 py-4 text-right">
                                            <div className={`text-base font-bold font-mono tracking-tight ${totalDebit !== totalCredit ? 'text-red-500' : 'text-green-600'}`}>
                                                Rp {FormatNumber(totalDebit)}
                                            </div>
                                        </td>
                                        <td className="px-6 py-4 text-right">
                                            <div className={`text-base font-bold font-mono tracking-tight ${totalDebit !== totalCredit ? 'text-red-500' : 'text-green-600'}`}>
                                                Rp {FormatNumber(totalCredit)}
                                            </div>
                                        </td>
                                        {!isLocked && <td></td>}
                                    </tr>
                                    {totalDebit !== totalCredit && (
                                        <tr>
                                            <td colSpan={5} className="px-6 py-3 text-center bg-red-50 border-t border-red-100">
                                                <div className="flex items-center justify-center gap-2 text-red-600">
                                                    <span className="font-semibold text-sm">{t('transaction.general_journal_page.form.unbalanced')}</span>
                                                    <span className="text-xs">{t('transaction.general_journal_page.form.difference')} {FormatNumber(Math.abs(totalDebit - totalCredit))}</span>
                                                </div>
                                            </td>
                                        </tr>
                                    )}
                                </tfoot>
                            </table>
                        </div>
                    </CardContent>
                </Card>

                {/* Form-level validation errors */}
                {Object.keys(errors).length > 0 && (
                    <div className="bg-red-50 border border-red-200 rounded-lg p-4">
                        <p className="text-sm font-semibold text-red-700 mb-2">{t('transaction.general_journal_page.form.fix_errors')}</p>
                        <ul className="list-disc list-inside text-sm text-red-600 space-y-1">
                            {errors.transactionDate && <li>{errors.transactionDate.message}</li>}
                            {errors.description && <li>{errors.description.message}</li>}
                            {errors.details?.message && <li>{errors.details.message}</li>}
                            {errors.details?.root?.message && <li>{errors.details.root.message}</li>}
                        </ul>
                    </div>
                )}

                <div className="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-800">
                    <Button
                        type="button"
                        variant="ghost"
                        onClick={() => navigate('/jurnal-umum')}
                        className="text-gray-600 hover:text-gray-900 hover:bg-gray-100"
                    >
                        {t('common.cancel')}
                    </Button>
                    {!isLocked && (
                        <Button
                            type="button"
                            onClick={async () => {
                                const valid = await trigger();
                                if (valid) setShowConfirmModal(true);
                            }}
                            disabled={isCreating || isUpdating}
                            className="min-w-[140px] shadow-md hover:shadow-lg transition-all"
                        >
                            {(isCreating || isUpdating) ? (
                                <>
                                    <div className="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin mr-2"></div>
                                    Processing...
                                </>
                            ) : (
                                <>
                                    <Save className="mr-2 h-4 w-4" />
                                    {isUpdateMode ? t('transaction.general_journal_page.form.update_journal') : t('transaction.general_journal_page.form.save_journal')}
                                </>
                            )}
                        </Button>
                    )}
                </div>
            </form>

            {!isLocked && (
                <ModalCoaCustom
                    setIsSave={setIsSave}
                    selectedRecords={selectedRecords}
                    setShowSelected={setShowSelected}
                    setSelectedRecords={setSelectedRecords}
                    showModal={isShowModalCoa}
                    setIsShowModal={setIsShowModalCoa}
                    excludeId={null}
                />
            )}

            <ConfirmationModal
                isOpen={showConfirmModal}
                onClose={() => setShowConfirmModal(false)}
                onConfirm={handleSubmit(onSubmit)}
                title={t('transaction.general_journal_page.form.save_confirm_title')}
                message={t('transaction.general_journal_page.form.save_confirm_msg')}
                confirmText={isUpdateMode ? t('common.update') : t('common.save')}
                cancelText={t('common.cancel')}
                loading={isCreating || isUpdating}
            />
        </div>
    );
};

export default JurnalUmumForm;
