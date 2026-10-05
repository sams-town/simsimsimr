import { useEffect, useState } from 'react';
import { useTranslation } from 'react-i18next';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import {
    ChevronDown, ChevronUp, Loader2, Info, Save,
    LayoutDashboard, FileText, Receipt, Stethoscope, Wallet, Settings2,
} from 'lucide-react';
import { toastMessage } from '@/utils/toastUtils';
import { useGetSettingOptionsByTypeMutation, useGetSettingOptionMuMutation, usePutBulkSettingOptionMutation } from '@/store/api/setting/settingApiSlice';
import { useGetDynamicOptionCOAQuery } from '@/store/api/bank/bankApiSlice';
import SelectSearch from 'react-select';

// --- COA Select Component ---
interface CoaSelectProps {
    value: string;
    onChange: (value: string) => void;
    placeholder?: string;
}

const CoaSelect: React.FC<CoaSelectProps> = ({ value, onChange, placeholder }) => {
    const { t } = useTranslation();
    const { data: listResponse, isLoading } = useGetDynamicOptionCOAQuery({ page: -1, pageSize: -1 });

    const optionsList = listResponse?.data?.map((item: any) => ({
        ...item,
        label: `[${item.desc}] ${item.label}`,
    })) || [];

    // If value exists but not found in dropdown, show it as a manual entry
    let selectedOption = optionsList.find((opt: any) => opt.desc === value) || null;
    if (!selectedOption && value) {
        selectedOption = { desc: value, label: `[${value}]`, value: value };
    }

    return (
        <SelectSearch
            options={optionsList}
            onChange={(selected: any) => onChange(selected ? selected.desc : '')}
            value={selectedOption}
            isLoading={isLoading}
            isClearable
            placeholder={placeholder || t('common.placeholder.select')}
            className="w-full"
        />
    );
};

// --- Field definitions ---
type FieldType = 'coa' | 'text';

interface FieldConfig {
    optionName: string;
    labelKey: string;
    descKey: string;
    type: FieldType;
    defaultValue: string;
}

interface SectionConfig {
    titleKey: string;
    icon: any;
    iconColor: string;
    fields: FieldConfig[];
}

const SECTIONS: SectionConfig[] = [
    {
        titleKey: 'settings_page.default_accounts.sections.dashboard',
        icon: LayoutDashboard,
        iconColor: 'text-blue-500',
        fields: [
            { optionName: 'COA_PREFIX_REVENUE', labelKey: 'settings_page.default_accounts.fields.coa_prefix_revenue', descKey: 'settings_page.default_accounts.descs.coa_prefix_revenue', type: 'text', defaultValue: '4' },
            { optionName: 'COA_PREFIX_EXPENSE', labelKey: 'settings_page.default_accounts.fields.coa_prefix_expense', descKey: 'settings_page.default_accounts.descs.coa_prefix_expense', type: 'text', defaultValue: '5,6' },
            { optionName: 'COA_PREFIX_REVENUE_ALT', labelKey: 'settings_page.default_accounts.fields.coa_prefix_revenue_alt', descKey: 'settings_page.default_accounts.descs.coa_prefix_revenue_alt', type: 'text', defaultValue: '7' },
            { optionName: 'COA_PREFIX_EXPENSE_ALT', labelKey: 'settings_page.default_accounts.fields.coa_prefix_expense_alt', descKey: 'settings_page.default_accounts.descs.coa_prefix_expense_alt', type: 'text', defaultValue: '8' },
            { optionName: 'COA_PREFIX_REVENUE_OPD', labelKey: 'settings_page.default_accounts.fields.coa_prefix_opd', descKey: 'settings_page.default_accounts.descs.coa_prefix_opd', type: 'text', defaultValue: '401201' },
            { optionName: 'COA_PREFIX_REVENUE_IPD', labelKey: 'settings_page.default_accounts.fields.coa_prefix_ipd', descKey: 'settings_page.default_accounts.descs.coa_prefix_ipd', type: 'text', defaultValue: '401101' },
            { optionName: 'ACCOUNT_CASH', labelKey: 'settings_page.default_accounts.fields.account_cash', descKey: 'settings_page.default_accounts.descs.account_cash', type: 'text', defaultValue: '' },
            { optionName: 'ACCOUNT_BANK', labelKey: 'settings_page.default_accounts.fields.account_bank', descKey: 'settings_page.default_accounts.descs.account_bank', type: 'text', defaultValue: '' },
            { optionName: 'ACCOUNT_RECEIVABLE', labelKey: 'settings_page.default_accounts.fields.account_receivable', descKey: 'settings_page.default_accounts.descs.account_receivable', type: 'text', defaultValue: '' },
            { optionName: 'ACCOUNT_PAYABLE', labelKey: 'settings_page.default_accounts.fields.account_payable', descKey: 'settings_page.default_accounts.descs.account_payable', type: 'text', defaultValue: '' },
            { optionName: 'CLINIC_CODE_IGD', labelKey: 'settings_page.default_accounts.fields.clinic_code_igd', descKey: 'settings_page.default_accounts.descs.clinic_code_igd', type: 'text', defaultValue: 'IGDK' },
            { optionName: 'CLINIC_CODE_FISIO', labelKey: 'settings_page.default_accounts.fields.clinic_code_fisio', descKey: 'settings_page.default_accounts.descs.clinic_code_fisio', type: 'text', defaultValue: 'FISIO' },
            { optionName: 'PAYER_CODE_BPJS', labelKey: 'settings_page.default_accounts.fields.payer_code_bpjs', descKey: 'settings_page.default_accounts.descs.payer_code_bpjs', type: 'text', defaultValue: 'BPJ' },
            { optionName: 'PAYER_CODE_PRIVATE', labelKey: 'settings_page.default_accounts.fields.payer_code_private', descKey: 'settings_page.default_accounts.descs.payer_code_private', type: 'text', defaultValue: 'Prb' },
            { optionName: 'COA_CASHFLOW_SPECIAL', labelKey: 'settings_page.default_accounts.fields.coa_cashflow_special', descKey: 'settings_page.default_accounts.descs.coa_cashflow_special', type: 'coa', defaultValue: '401101004' },
        ],
    },
    {
        titleKey: 'settings_page.default_accounts.sections.ar',
        icon: FileText,
        iconColor: 'text-green-500',
        fields: [
            { optionName: 'ACCOUNT_PIUTANG_USAHA_RANAP_BPJS', labelKey: 'settings_page.default_accounts.fields.piutang_ranap_bpjs', descKey: 'settings_page.default_accounts.descs.piutang_ranap_bpjs', type: 'coa', defaultValue: '103001001' },
            { optionName: 'ACCOUNT_PIUTANG_USAHA_RAJAL_BPJS', labelKey: 'settings_page.default_accounts.fields.piutang_rajal_bpjs', descKey: 'settings_page.default_accounts.descs.piutang_rajal_bpjs', type: 'coa', defaultValue: '103002001' },
            { optionName: 'ACCOUNT_PIUTANG_USAHA_RANAP_NON_BPJS', labelKey: 'settings_page.default_accounts.fields.piutang_ranap_non_bpjs', descKey: 'settings_page.default_accounts.descs.piutang_ranap_non_bpjs', type: 'coa', defaultValue: '103001002' },
            { optionName: 'ACCOUNT_PIUTANG_USAHA_RAJAL_NON_BPJS', labelKey: 'settings_page.default_accounts.fields.piutang_rajal_non_bpjs', descKey: 'settings_page.default_accounts.descs.piutang_rajal_non_bpjs', type: 'coa', defaultValue: '103002002' },
            { optionName: 'ACCOUNT_DISCOUNT_AR', labelKey: 'settings_page.default_accounts.fields.discount_ar', descKey: 'settings_page.default_accounts.descs.discount_ar', type: 'coa', defaultValue: '414000000' },
        ],
    },
    {
        titleKey: 'settings_page.default_accounts.sections.ap',
        icon: Receipt,
        iconColor: 'text-red-500',
        fields: [
            { optionName: 'ACCOUNT_PPN_MASUKAN', labelKey: 'settings_page.default_accounts.fields.ppn_masukan', descKey: 'settings_page.default_accounts.descs.ppn_masukan', type: 'coa', defaultValue: '108006' },
            { optionName: 'ACCOUNT_UTANG_SUPPLIER', labelKey: 'settings_page.default_accounts.fields.utang_supplier', descKey: 'settings_page.default_accounts.descs.utang_supplier', type: 'coa', defaultValue: '201001' },
            { optionName: 'ACCOUNT_AP_DISCOUNT', labelKey: 'settings_page.default_accounts.fields.ap_discount', descKey: 'settings_page.default_accounts.descs.ap_discount', type: 'coa', defaultValue: '404001' },
            { optionName: 'ACCOUNT_AP_PPH23', labelKey: 'settings_page.default_accounts.fields.ap_pph23', descKey: 'settings_page.default_accounts.descs.ap_pph23', type: 'coa', defaultValue: '206002' },
            { optionName: 'ACCOUNT_TUKAR_FAKTUR_CREDIT', labelKey: 'settings_page.default_accounts.fields.tukar_faktur_credit', descKey: 'settings_page.default_accounts.descs.tukar_faktur_credit', type: 'coa', defaultValue: '201001' },
            { optionName: 'ACCOUNT_TUKAR_FAKTUR_DEBIT', labelKey: 'settings_page.default_accounts.fields.tukar_faktur_debit', descKey: 'settings_page.default_accounts.descs.tukar_faktur_debit', type: 'coa', defaultValue: '204099' },
        ],
    },
    {
        titleKey: 'settings_page.default_accounts.sections.medical_fee',
        icon: Stethoscope,
        iconColor: 'text-purple-500',
        fields: [
            { optionName: 'ACCOUNT_JASA_MEDIS_PRIBADI', labelKey: 'settings_page.default_accounts.fields.jasa_medis_pribadi', descKey: 'settings_page.default_accounts.descs.jasa_medis_pribadi', type: 'coa', defaultValue: '201004' },
            { optionName: 'ACCOUNT_JASA_MEDIS_ASURANSI', labelKey: 'settings_page.default_accounts.fields.jasa_medis_asuransi', descKey: 'settings_page.default_accounts.descs.jasa_medis_asuransi', type: 'coa', defaultValue: '201005' },
            { optionName: 'ACCOUNT_JASA_MEDIS_SELISIH', labelKey: 'settings_page.default_accounts.fields.jasa_medis_selisih', descKey: 'settings_page.default_accounts.descs.jasa_medis_selisih', type: 'coa', defaultValue: '722098' },
        ],
    },
    {
        titleKey: 'settings_page.default_accounts.sections.petty_cash',
        icon: Wallet,
        iconColor: 'text-orange-500',
        fields: [
            { optionName: 'OPTION_PETTY_CASH_TOPUP_COA', labelKey: 'settings_page.default_accounts.fields.petty_cash_topup', descKey: 'settings_page.default_accounts.descs.petty_cash_topup', type: 'coa', defaultValue: '101004001' },
            { optionName: 'OPTION_PETTY_CASH_RECEIVABLE_COA', labelKey: 'settings_page.default_accounts.fields.petty_cash_receivable', descKey: 'settings_page.default_accounts.descs.petty_cash_receivable', type: 'coa', defaultValue: '104003' },
        ],
    },
    {
        titleKey: 'settings_page.default_accounts.sections.general',
        icon: Settings2,
        iconColor: 'text-gray-500',
        fields: [
            { optionName: 'ACCOUNT_ACCOUNT_CASH_BANK', labelKey: 'settings_page.default_accounts.fields.prefix_cash_bank', descKey: 'settings_page.default_accounts.descs.prefix_cash_bank', type: 'text', defaultValue: '101' },
        ],
    },
];

// Build default values map from SECTIONS config
const DEFAULT_VALUES: Record<string, string> = {};
SECTIONS.forEach((s) => s.fields.forEach((f) => {
    DEFAULT_VALUES[f.optionName] = f.defaultValue;
}));

const DefaultAccountsTab = () => {
    const { t } = useTranslation();
    const [isSaving, setIsSaving] = useState(false);
    const [isLoading, setIsLoading] = useState(true);
    const [values, setValues] = useState<Record<string, string>>({ ...DEFAULT_VALUES });
    const [openSections, setOpenSections] = useState<Record<number, boolean>>({ 0: true });

    const [getOptionsByType] = useGetSettingOptionsByTypeMutation();
    const [getSettingOption] = useGetSettingOptionMuMutation();
    const [putBulkOption] = usePutBulkSettingOptionMutation();

    const allOptionNames = Object.keys(DEFAULT_VALUES);

    useEffect(() => {
        fetchOptions();
    }, []);

    const fetchOptions = async () => {
        setIsLoading(true);
        try {
            // Start with default values
            const mapped: Record<string, string> = { ...DEFAULT_VALUES };

            // 1. Try bulk fetch by type first
            try {
                const res = await getOptionsByType('DEFAULT_ACCOUNT').unwrap();
                const data = res.data || res;
                if (Array.isArray(data)) {
                    data.forEach((opt: any) => {
                        if (opt.optionName && opt.optionName in mapped) {
                            mapped[opt.optionName] = opt.optionValue ?? '';
                        }
                    });
                }
            } catch {
                // Type query might fail if no options have DEFAULT_ACCOUNT type yet
            }

            // 2. For any options still at default, try fetching individually
            const toFetch = allOptionNames.filter((name) => mapped[name] === DEFAULT_VALUES[name]);
            if (toFetch.length > 0) {
                const results = await Promise.allSettled(
                    toFetch.map((name) => getSettingOption(name).unwrap())
                );
                results.forEach((result, idx) => {
                    if (result.status === 'fulfilled') {
                        const opt = result.value?.data || result.value;
                        if (opt?.optionValue !== undefined && opt.optionValue !== null) {
                            mapped[toFetch[idx]] = opt.optionValue;
                        }
                    }
                });
            }

            setValues(mapped);
        } catch (error) {
            console.error('Failed to fetch default account options', error);
        } finally {
            setIsLoading(false);
        }
    };

    const handleChange = (optionName: string, value: string) => {
        setValues((prev) => ({ ...prev, [optionName]: value }));
    };

    const toggleSection = (index: number) => {
        setOpenSections((prev) => ({ ...prev, [index]: !prev[index] }));
    };

    const handleSave = async () => {
        setIsSaving(true);
        try {
            const payload = SECTIONS.flatMap((section) =>
                section.fields.map((field) => ({
                    optionName: field.optionName,
                    optionValue: values[field.optionName] || '',
                    optionType: 'DEFAULT_ACCOUNT',
                    optionLabel: t(field.labelKey),
                    optionDesc: t(field.descKey),
                }))
            );
            await putBulkOption(payload).unwrap();
            toastMessage(t('messages.save_success'), 'success');
        } catch (error) {
            console.error(error);
            toastMessage(t('messages.save_error'), 'error');
        } finally {
            setIsSaving(false);
        }
    };

    if (isLoading) {
        return (
            <div className="flex items-center justify-center py-16">
                <Loader2 className="h-8 w-8 animate-spin text-primary" />
            </div>
        );
    }

    return (
        <div className="space-y-4">
            {/* Info Banner */}
            <div className="bg-blue-50 border border-blue-100 rounded-xl p-4 flex items-start gap-3">
                <Info className="h-5 w-5 text-blue-500 mt-0.5 shrink-0" />
                <div>
                    <p className="text-sm text-blue-800 font-medium">{t('settings_page.default_accounts.info_title')}</p>
                    <p className="text-sm text-blue-600 mt-1">{t('settings_page.default_accounts.info_desc')}</p>
                </div>
            </div>

            {/* Sections */}
            {SECTIONS.map((section, sectionIdx) => {
                const isOpen = openSections[sectionIdx] ?? false;
                const SectionIcon = section.icon;
                return (
                    <Card key={sectionIdx} className="border border-gray-200 shadow-sm rounded-xl overflow-hidden">
                        <CardHeader
                            className="border-b border-gray-100 bg-gray-50/50 py-3 px-5 cursor-pointer select-none"
                            onClick={() => toggleSection(sectionIdx)}
                        >
                            <div className="flex items-center justify-between">
                                <CardTitle className="text-sm font-semibold flex items-center gap-2">
                                    <SectionIcon className={`h-5 w-5 ${section.iconColor}`} />
                                    {t(section.titleKey)}
                                    <span className="text-xs text-gray-400 font-normal ml-1">
                                        ({section.fields.length})
                                    </span>
                                </CardTitle>
                                {isOpen ? (
                                    <ChevronUp className="h-4 w-4 text-gray-400" />
                                ) : (
                                    <ChevronDown className="h-4 w-4 text-gray-400" />
                                )}
                            </div>
                        </CardHeader>
                        {isOpen && (
                            <CardContent className="p-5">
                                <div className="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                    {section.fields.map((field) => (
                                        <div key={field.optionName} className="space-y-1.5">
                                            <label className="text-xs font-medium text-gray-700">
                                                {t(field.labelKey)}
                                            </label>
                                            {field.type === 'coa' ? (
                                                <CoaSelect
                                                    value={values[field.optionName] || ''}
                                                    onChange={(val) => handleChange(field.optionName, val)}
                                                    placeholder={t(field.labelKey)}
                                                />
                                            ) : (
                                                <input
                                                    type="text"
                                                    className="form-input w-full rounded-lg border-gray-200 text-sm"
                                                    value={values[field.optionName] || ''}
                                                    onChange={(e) => handleChange(field.optionName, e.target.value)}
                                                    placeholder={t(field.descKey)}
                                                />
                                            )}
                                            <p className="text-[11px] text-gray-400">{t(field.descKey)}</p>
                                        </div>
                                    ))}
                                </div>
                            </CardContent>
                        )}
                    </Card>
                );
            })}

            {/* Save Button */}
            <Button
                onClick={handleSave}
                disabled={isSaving}
                className="w-full lg:w-auto bg-primary hover:bg-primary/90"
            >
                {isSaving ? (
                    <>
                        <Loader2 className="h-4 w-4 mr-2 animate-spin" />
                        {t('common.loading')}
                    </>
                ) : (
                    <>
                        <Save className="h-4 w-4 mr-2" />
                        {t('common.save')}
                    </>
                )}
            </Button>
        </div>
    );
};

export default DefaultAccountsTab;
