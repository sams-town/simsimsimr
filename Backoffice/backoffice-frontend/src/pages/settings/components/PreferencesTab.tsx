import { useState } from 'react';
import { useTranslation } from 'react-i18next';
import { useDispatch, useSelector } from 'react-redux';
import { toggleLocale } from '@/store/themeConfigSlice';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Globe, Loader2 } from 'lucide-react';
import { toastMessage } from '@/utils/toastUtils';

const PreferencesTab = () => {
    const { t } = useTranslation();
    const dispatch = useDispatch();
    const currentLocale = useSelector((state: any) => state.themeConfig.locale);
    const [isSaving, setIsSaving] = useState(false);

    const [preferences, setPreferences] = useState({
        language: currentLocale || 'id',
    });

    const languages = [
        { value: 'id', label: t('preferences_settings.language_id') },
        { value: 'en', label: t('preferences_settings.language_en') },
    ];

    const handleChange = (value: string) => {
        setPreferences({ language: value });
        dispatch(toggleLocale(value));
    };

    const handleSave = async () => {
        setIsSaving(true);
        try {
            localStorage.setItem('i18nextLng', preferences.language);
            localStorage.setItem('preferences', JSON.stringify(preferences));
            toastMessage(t('messages.save_success'), 'success');
        } catch (error) {
            toastMessage(t('messages.save_error'), 'error');
        } finally {
            setIsSaving(false);
        }
    };

    return (
        <div className="max-w-2xl space-y-6">
            {/* Language */}
            <Card className="border border-gray-100 shadow-sm rounded-xl">
                <CardHeader className="border-b border-gray-100 bg-gray-50/50 py-4">
                    <CardTitle className="text-base font-semibold flex items-center gap-2">
                        <Globe className="h-5 w-5 text-blue-500" />
                        {t('preferences_settings.language')}
                    </CardTitle>
                </CardHeader>
                <CardContent className="p-5">
                    <div className="grid grid-cols-2 gap-3">
                        {languages.map((lang) => (
                            <button
                                key={lang.value}
                                onClick={() => handleChange(lang.value)}
                                className={`
                  p-4 rounded-xl border-2 text-left transition-all
                  ${preferences.language === lang.value
                                        ? 'border-primary bg-primary/5'
                                        : 'border-gray-100 hover:border-gray-200'
                                    }
                `}
                            >
                                <div className="flex items-center gap-3">
                                    <div className={`w-10 h-10 rounded-lg flex items-center justify-center text-lg font-bold ${preferences.language === lang.value ? 'bg-primary text-white' : 'bg-gray-100 text-gray-600'
                                        }`}>
                                        {lang.value.toUpperCase()}
                                    </div>
                                    <span className="font-medium text-gray-800">{lang.label}</span>
                                </div>
                            </button>
                        ))}
                    </div>
                </CardContent>
            </Card>

            {/* Save Button */}
            <Button
                onClick={handleSave}
                disabled={isSaving}
                className="w-full bg-primary hover:bg-primary/90"
            >
                {isSaving ? (
                    <>
                        <Loader2 className="h-4 w-4 mr-2 animate-spin" />
                        {t('common.loading')}
                    </>
                ) : (
                    t('common.save')
                )}
            </Button>
        </div>
    );
};

export default PreferencesTab;
