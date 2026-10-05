import { useEffect, useState } from 'react';
import { useTranslation } from 'react-i18next';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Database, Server, Hash, User, Lock, Loader2, Info, CheckCircle2, XCircle } from 'lucide-react';
import { toastMessage } from '@/utils/toastUtils';
import { useGetSettingOptionMuMutation, usePutSettingOptionMutation } from '@/store/api/setting/settingApiSlice';

interface KhanzaConfig {
    host: string;
    port: string;
    database: string;
    username: string;
    password: string;
}

const KhanzaConfigTab = () => {
    const { t } = useTranslation();
    const [isSaving, setIsSaving] = useState(false);
    const [isTesting, setIsTesting] = useState(false);
    const [testResult, setTestResult] = useState<'success' | 'error' | null>(null);

    const [config, setConfig] = useState<KhanzaConfig>({
        host: '',
        port: '3306',
        database: '',
        username: '',
        password: '',
    });

    const [getSettingOption] = useGetSettingOptionMuMutation();
    const [putSettingOption] = usePutSettingOptionMutation();

    useEffect(() => {
        fetchConfigs();
    }, []);

    const fetchConfigs = async () => {
        try {
            const fields = ['KHANZA_DB_HOST', 'KHANZA_DB_PORT', 'KHANZA_DB_NAME', 'KHANZA_DB_USER', 'KHANZA_DB_PASS'];
            const newConfig: KhanzaConfig = { ...config };

            for (const field of fields) {
                try {
                    const res = await getSettingOption(field).unwrap();
                    const data = res.data || res;
                    if (data?.optionValue) {
                        switch (field) {
                            case 'KHANZA_DB_HOST':
                                newConfig.host = data.optionValue;
                                break;
                            case 'KHANZA_DB_PORT':
                                newConfig.port = data.optionValue;
                                break;
                            case 'KHANZA_DB_NAME':
                                newConfig.database = data.optionValue;
                                break;
                            case 'KHANZA_DB_USER':
                                newConfig.username = data.optionValue;
                                break;
                            case 'KHANZA_DB_PASS':
                                newConfig.password = data.optionValue;
                                break;
                        }
                    }
                } catch (error) {
                    // Option might not exist yet, that's ok
                    console.log(`Option ${field} not found, using default`);
                }
            }

            setConfig(newConfig);
        } catch (error) {
            console.error('Failed to fetch Khanza config', error);
        }
    };

    const handleChange = (field: keyof KhanzaConfig, value: string) => {
        setConfig((prev) => ({ ...prev, [field]: value }));
        setTestResult(null); // Reset test result when config changes
    };

    const handleSave = async () => {
        if (!config.host || !config.database || !config.username) {
            if (!config.host || !config.database || !config.username) {
                toastMessage(t('settings_page.khanza.toast.incomplete'), 'error');
                return;
            }
            return;
        }

        setIsSaving(true);
        try {
            const options = [
                { name: 'KHANZA_DB_HOST', value: config.host, label: 'Host Database Khanza' },
                { name: 'KHANZA_DB_PORT', value: config.port || '3306', label: 'Port Database Khanza' },
                { name: 'KHANZA_DB_NAME', value: config.database, label: 'Nama Database Khanza' },
                { name: 'KHANZA_DB_USER', value: config.username, label: 'Username Database Khanza' },
                { name: 'KHANZA_DB_PASS', value: config.password, label: 'Password Database Khanza' },
            ];

            for (const opt of options) {
                await putSettingOption({
                    optionName: opt.name,
                    optionValue: opt.value,
                    optionLabel: opt.label,
                    optionType: 'KHANZA',
                    optionDesc: opt.label
                }).unwrap();
            }

            toastMessage(t('messages.save_success'), 'success');
        } catch (error) {
            console.error(error);
            toastMessage(t('messages.save_error'), 'error');
        } finally {
            setIsSaving(false);
        }
    };

    const handleTestConnection = async () => {
        if (!config.host || !config.database || !config.username) {
            if (!config.host || !config.database || !config.username) {
                toastMessage(t('settings_page.khanza.toast.incomplete_db'), 'error');
                return;
            }
            return;
        }

        setIsTesting(true);
        setTestResult(null);

        try {
            // Test by calling khanza-sync with a small date range
            const today = new Date().toISOString().split('T')[0];
            const response = await fetch(
                `${import.meta.env.VITE_HOST}/Journal/khanza-sync?startDate=${today}&endDate=${today}&page=1&pageSize=1`,
                {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem(`${import.meta.env.VITE_BASE_APP_NAME}-token`)}`,
                    }
                }
            );

            if (response.ok) {
                setTestResult('success');
                toastMessage(t('settings_page.khanza.toast.success'), 'success');
            } else {
                const data = await response.json();
                setTestResult('error');
                toastMessage(data.message || t('settings_page.khanza.toast.failed'), 'error');
            }
        } catch (error) {
            setTestResult('error');
            toastMessage(t('settings_page.khanza.toast.failed_khanza'), 'error');
        } finally {
            setIsTesting(false);
        }
    };

    return (
        <div className="space-y-6">
            {/* Info Banner */}
            <div className="bg-blue-50 border border-blue-100 rounded-xl p-4 flex items-start gap-3">
                <Info className="h-5 w-5 text-blue-500 mt-0.5 shrink-0" />
                <div>
                    <p className="text-sm text-blue-800 font-medium">{t('settings_page.khanza.info_title')}</p>
                    <p className="text-sm text-blue-600 mt-1">
                        {t('settings_page.khanza.info_desc')}
                    </p>
                </div>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {/* Host */}
                <Card className="border border-gray-100 shadow-sm rounded-xl">
                    <CardHeader className="border-b border-gray-100 bg-gray-50/50 py-3">
                        <CardTitle className="text-sm font-semibold flex items-center gap-2">
                            <Server className="h-4 w-4 text-blue-500" />
                            {t('settings_page.khanza.host')}
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="p-4">
                        <input
                            type="text"
                            className="form-input w-full rounded-lg border-gray-200"
                            placeholder={t('settings_page.khanza.placeholder.host')}
                            value={config.host}
                            onChange={(e) => handleChange('host', e.target.value)}
                        />
                    </CardContent>
                </Card>

                {/* Port */}
                <Card className="border border-gray-100 shadow-sm rounded-xl">
                    <CardHeader className="border-b border-gray-100 bg-gray-50/50 py-3">
                        <CardTitle className="text-sm font-semibold flex items-center gap-2">
                            <Hash className="h-4 w-4 text-purple-500" />
                            {t('settings_page.khanza.port')}
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="p-4">
                        <input
                            type="text"
                            className="form-input w-full rounded-lg border-gray-200"
                            placeholder="3306"
                            value={config.port}
                            onChange={(e) => handleChange('port', e.target.value)}
                        />
                        <p className="text-xs text-gray-500 mt-1">{t('settings_page.khanza.helper.port')}</p>
                    </CardContent>
                </Card>

                {/* Database Name */}
                <Card className="border border-gray-100 shadow-sm rounded-xl">
                    <CardHeader className="border-b border-gray-100 bg-gray-50/50 py-3">
                        <CardTitle className="text-sm font-semibold flex items-center gap-2">
                            <Database className="h-4 w-4 text-green-500" />
                            {t('settings_page.khanza.database')}
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="p-4">
                        <input
                            type="text"
                            className="form-input w-full rounded-lg border-gray-200"
                            placeholder={t('settings_page.khanza.placeholder.database')}
                            value={config.database}
                            onChange={(e) => handleChange('database', e.target.value)}
                        />
                        <p className="text-xs text-gray-500 mt-1">{t('settings_page.khanza.helper.database')}</p>
                    </CardContent>
                </Card>

                {/* Username */}
                <Card className="border border-gray-100 shadow-sm rounded-xl">
                    <CardHeader className="border-b border-gray-100 bg-gray-50/50 py-3">
                        <CardTitle className="text-sm font-semibold flex items-center gap-2">
                            <User className="h-4 w-4 text-orange-500" />
                            {t('settings_page.khanza.username')}
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="p-4">
                        <input
                            type="text"
                            className="form-input w-full rounded-lg border-gray-200"
                            placeholder={t('settings_page.khanza.placeholder.username')}
                            value={config.username}
                            onChange={(e) => handleChange('username', e.target.value)}
                        />
                    </CardContent>
                </Card>

                {/* Password */}
                <Card className="border border-gray-100 shadow-sm rounded-xl lg:col-span-2">
                    <CardHeader className="border-b border-gray-100 bg-gray-50/50 py-3">
                        <CardTitle className="text-sm font-semibold flex items-center gap-2">
                            <Lock className="h-4 w-4 text-red-500" />
                            {t('settings_page.khanza.password')}
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="p-4">
                        <input
                            type="password"
                            className="form-input w-full rounded-lg border-gray-200"
                            placeholder="••••••••"
                            value={config.password}
                            onChange={(e) => handleChange('password', e.target.value)}
                        />
                    </CardContent>
                </Card>
            </div>

            {/* Test Result */}
            {testResult && (
                <div className={`rounded-xl p-4 flex items-center gap-3 ${testResult === 'success'
                    ? 'bg-green-50 border border-green-100'
                    : 'bg-red-50 border border-red-100'
                    }`}>
                    {testResult === 'success' ? (
                        <>
                            <CheckCircle2 className="h-5 w-5 text-green-500" />
                            <span className="text-sm text-green-700 font-medium">
                                {t('settings_page.khanza.test_result.success')}
                            </span>
                        </>
                    ) : (
                        <>
                            <XCircle className="h-5 w-5 text-red-500" />
                            <span className="text-sm text-red-700 font-medium">
                                {t('settings_page.khanza.test_result.failed')}
                            </span>
                        </>
                    )}
                </div>
            )}

            {/* Action Buttons */}
            <div className="flex flex-col sm:flex-row gap-3">
                <Button
                    onClick={handleTestConnection}
                    disabled={isTesting}
                    variant="outline"
                    className="border-blue-200 text-blue-600 hover:bg-blue-50"
                >
                    {isTesting ? (
                        <>
                            <Loader2 className="h-4 w-4 mr-2 animate-spin" />
                            {t('settings_page.khanza.testing')}
                        </>
                    ) : (
                        <>
                            <Database className="h-4 w-4 mr-2" />
                            {t('settings_page.khanza.test_button')}
                        </>
                    )}
                </Button>

                <Button
                    onClick={handleSave}
                    disabled={isSaving}
                    className="bg-primary hover:bg-primary/90"
                >
                    {isSaving ? (
                        <>
                            <Loader2 className="h-4 w-4 mr-2 animate-spin" />
                            {t('common.loading', 'Menyimpan...')}
                        </>
                    ) : (
                        t('common.save', 'Simpan')
                    )}
                </Button>
            </div>
        </div>
    );
};

export default KhanzaConfigTab;
