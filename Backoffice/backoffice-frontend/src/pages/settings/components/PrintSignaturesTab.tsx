import { useEffect, useState } from 'react';
import { useTranslation } from 'react-i18next';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Loader2, Save, PenTool } from 'lucide-react';
import { toastMessage } from '@/utils/toastUtils';
import { useGetSettingOptionsByTypeMutation, usePutBulkSettingOptionMutation } from '@/store/api/setting/settingApiSlice';

const SECTION_TITLE_KEY = 'settings_page.print_signatures.title';
const SECTION_DESC_KEY = 'settings_page.print_signatures.desc';
const OPTION_TYPE = 'PRINT_SIGNATURES';

interface FieldDef {
    optionName: string;
    labelKey: string;
    descKey: string;
    defaultValue: string;
}

const FIELDS: FieldDef[] = [
    {
        optionName: 'PRINT_SIGNER_LEFT_NAME',
        labelKey: 'settings_page.print_signatures.fields.left_name',
        descKey: 'settings_page.print_signatures.descs.left_name',
        defaultValue: 'Naurah Nazhifah Salim',
    },
    {
        optionName: 'PRINT_SIGNER_LEFT_TITLE',
        labelKey: 'settings_page.print_signatures.fields.left_title',
        descKey: 'settings_page.print_signatures.descs.left_title',
        defaultValue: 'Bagian Penagihan',
    },
    {
        optionName: 'PRINT_SIGNER_RIGHT_NAME',
        labelKey: 'settings_page.print_signatures.fields.right_name',
        descKey: 'settings_page.print_signatures.descs.right_name',
        defaultValue: 'Herlan Mars, S.Ak',
    },
    {
        optionName: 'PRINT_SIGNER_RIGHT_TITLE',
        labelKey: 'settings_page.print_signatures.fields.right_title',
        descKey: 'settings_page.print_signatures.descs.right_title',
        defaultValue: 'Manajer Keu & Akuntansi',
    },
];

const DEFAULTS: Record<string, string> = {};
FIELDS.forEach((f) => { DEFAULTS[f.optionName] = f.defaultValue; });

const PrintSignaturesTab = () => {
    const { t } = useTranslation();
    const [isSaving, setIsSaving] = useState(false);
    const [isLoading, setIsLoading] = useState(true);
    const [values, setValues] = useState<Record<string, string>>({ ...DEFAULTS });

    const [getOptionsByType] = useGetSettingOptionsByTypeMutation();
    const [putBulkOption] = usePutBulkSettingOptionMutation();

    useEffect(() => {
        (async () => {
            try {
                const res: any = await getOptionsByType(OPTION_TYPE).unwrap();
                const list = res?.data || res || [];
                const map: Record<string, string> = { ...DEFAULTS };
                list.forEach((o: any) => {
                    if (o.optionName && o.optionValue !== undefined) {
                        map[o.optionName] = o.optionValue;
                    }
                });
                setValues(map);
            } catch {
                setValues({ ...DEFAULTS });
            } finally {
                setIsLoading(false);
            }
        })();
    }, []);

    const handleChange = (name: string, val: string) => {
        setValues((prev) => ({ ...prev, [name]: val }));
    };

    const handleSave = async () => {
        setIsSaving(true);
        try {
            const payload = FIELDS.map((f) => ({
                optionName: f.optionName,
                optionValue: values[f.optionName] || f.defaultValue,
                optionType: OPTION_TYPE,
                optionLabel: t(f.labelKey),
                optionDesc: t(f.descKey),
            }));
            await putBulkOption(payload).unwrap();
            toastMessage(t('common.saved_successfully'), 'success');
        } catch (err: any) {
            toastMessage(err?.data?.message || err?.message || t('common.save_error'), 'error');
        } finally {
            setIsSaving(false);
        }
    };

    if (isLoading) {
        return (
            <div className="flex items-center justify-center py-12">
                <Loader2 className="h-6 w-6 animate-spin text-primary" />
            </div>
        );
    }

    return (
        <div className="space-y-6">
            <Card>
                <CardHeader className="border-b border-gray-100 pb-4">
                    <div className="flex items-center gap-3">
                        <div className="p-2 rounded-lg bg-primary/10">
                            <PenTool className="h-5 w-5 text-primary" />
                        </div>
                        <div>
                            <CardTitle className="text-lg">{t(SECTION_TITLE_KEY)}</CardTitle>
                            <p className="text-sm text-gray-500 mt-0.5">{t(SECTION_DESC_KEY)}</p>
                        </div>
                    </div>
                </CardHeader>
                <CardContent className="pt-6">
                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        {FIELDS.map((field) => (
                            <div key={field.optionName} className="space-y-1.5">
                                <Label htmlFor={field.optionName}>{t(field.labelKey)}</Label>
                                <Input
                                    id={field.optionName}
                                    type="text"
                                    value={values[field.optionName] || ''}
                                    onChange={(e) => handleChange(field.optionName, e.target.value)}
                                    placeholder={t(field.labelKey)}
                                />
                                <p className="text-[11px] text-gray-400">{t(field.descKey)}</p>
                            </div>
                        ))}
                    </div>

                    {/* QR Preview */}
                    <div className="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <p className="text-xs font-medium text-gray-500 mb-3 uppercase tracking-wider">
                            {t('settings_page.print_signatures.preview')}
                        </p>
                        <div className="flex items-center justify-center gap-16">
                            <div className="text-center">
                                <img
                                    src={`https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=Signed by ${values['PRINT_SIGNER_LEFT_NAME'] || DEFAULTS['PRINT_SIGNER_LEFT_NAME']}`}
                                    alt="QR Left"
                                    className="w-20 h-20 mx-auto mb-2 border p-0.5 rounded"
                                />
                                <p className="text-xs font-bold underline">
                                    {values['PRINT_SIGNER_LEFT_NAME'] || DEFAULTS['PRINT_SIGNER_LEFT_NAME']}
                                </p>
                                <p className="text-[10px] text-gray-400">
                                    {values['PRINT_SIGNER_LEFT_TITLE'] || DEFAULTS['PRINT_SIGNER_LEFT_TITLE']}
                                </p>
                            </div>
                            <div className="text-center">
                                <img
                                    src={`https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=Approved by ${values['PRINT_SIGNER_RIGHT_NAME'] || DEFAULTS['PRINT_SIGNER_RIGHT_NAME']}`}
                                    alt="QR Right"
                                    className="w-20 h-20 mx-auto mb-2 border p-0.5 rounded"
                                />
                                <p className="text-xs font-bold underline">
                                    {values['PRINT_SIGNER_RIGHT_NAME'] || DEFAULTS['PRINT_SIGNER_RIGHT_NAME']}
                                </p>
                                <p className="text-[10px] text-gray-400">
                                    {values['PRINT_SIGNER_RIGHT_TITLE'] || DEFAULTS['PRINT_SIGNER_RIGHT_TITLE']}
                                </p>
                            </div>
                        </div>
                    </div>

                    <Button
                        onClick={handleSave}
                        disabled={isSaving}
                        className="mt-6 w-full lg:w-auto bg-primary hover:bg-primary/90"
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
                </CardContent>
            </Card>
        </div>
    );
};

export default PrintSignaturesTab;
