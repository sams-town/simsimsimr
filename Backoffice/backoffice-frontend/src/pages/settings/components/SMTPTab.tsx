import { useEffect, useState } from 'react';
import { useTranslation } from 'react-i18next';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Mail, Server, Lock, User, Hash, Loader2, TestTube, Info } from 'lucide-react';
import { toastMessage } from '@/utils/toastUtils';
import { useGetSettingOptionMuMutation, usePutSettingOptionMutation, useTestSmtpEmailMutation } from '@/store/api/setting/settingApiSlice';

const SMTPTab = () => {
    const { t } = useTranslation();
    const [isSaving, setIsSaving] = useState(false);
    const [isTesting, setIsTesting] = useState(false);

    const [smtp, setSmtp] = useState({
        host: '',
        port: '587',
        username: '',
        password: '',
        fromEmail: '',
        fromName: '',
        encryption: 'tls',
    });

    const [getSettingOption] = useGetSettingOptionMuMutation();
    const [putSettingOption] = usePutSettingOptionMutation();
    const [testSmtpEmail] = useTestSmtpEmailMutation();

    useEffect(() => {
        fetchSmtpSettings();
    }, []);

    const fetchSmtpSettings = async () => {
        try {
            console.log("Fetching SMTP Config...");
            const res = await getSettingOption('SMTP_CONFIG').unwrap();
            console.log("SMTP Config Response:", res);

            const data = res.data || res; // Handle wrapped or unwrapped response

            if (data && data.optionValue) {
                console.log("Parsing optionValue:", data.optionValue);
                setSmtp(JSON.parse(data.optionValue));
            } else {
                console.warn("No optionValue found in response data");
            }
        } catch (error) {
            console.error('Failed to fetch SMTP settings', error);
        }
    };

    const handleChange = (field: string, value: string) => {
        setSmtp((prev) => ({ ...prev, [field]: value }));
    };

    const handleSave = async () => {
        setIsSaving(true);
        try {
            await putSettingOption({
                optionName: 'SMTP_CONFIG',
                optionValue: JSON.stringify(smtp),
                optionLabel: 'SMTP Configuration',
                optionType: 'json',
                optionDesc: 'SMTP Server Configuration'
            }).unwrap();
            toastMessage(t('messages.save_success'), 'success');
        } catch (error) {
            console.error(error);
            toastMessage(t('messages.save_error'), 'error');
        } finally {
            setIsSaving(false);
        }
    };

    const handleTestEmail = async () => {
        if (!smtp.host || !smtp.username || !smtp.fromEmail) {
            toastMessage(t('settings_page.smtp.toast.incomplete'), 'error');
            return;
        }

        setIsTesting(true);
        try {
            await testSmtpEmail(smtp).unwrap();
            toastMessage(t('settings_page.smtp.toast.success'), 'success');
        } catch (error) {
            toastMessage(t('settings_page.smtp.toast.failed'), 'error');
        } finally {
            setIsTesting(false);
        }
    };

    return (
        <div className="space-y-6">
            {/* Info Banner */}
            <div className="bg-amber-50 border border-amber-100 rounded-xl p-4 flex items-start gap-3">
                <Info className="h-5 w-5 text-amber-500 mt-0.5 shrink-0" />
                <div>
                    <p className="text-sm text-amber-800 font-medium">{t('settings_page.smtp.info_title')}</p>
                    <p className="text-sm text-amber-600 mt-1">
                        {t('settings_page.smtp.info_desc')}
                    </p>
                </div>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {/* SMTP Host */}
                <Card className="border border-gray-100 shadow-sm rounded-xl">
                    <CardHeader className="border-b border-gray-100 bg-gray-50/50 py-3">
                        <CardTitle className="text-sm font-semibold flex items-center gap-2">
                            <Server className="h-4 w-4 text-blue-500" />
                            {t('settings_page.smtp.host')}
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="p-4">
                        <input
                            type="text"
                            className="form-input w-full rounded-lg border-gray-200"
                            placeholder="smtp.gmail.com"
                            value={smtp.host}
                            onChange={(e) => handleChange('host', e.target.value)}
                        />
                    </CardContent>
                </Card>

                {/* Port */}
                <Card className="border border-gray-100 shadow-sm rounded-xl">
                    <CardHeader className="border-b border-gray-100 bg-gray-50/50 py-3">
                        <CardTitle className="text-sm font-semibold flex items-center gap-2">
                            <Hash className="h-4 w-4 text-purple-500" />
                            {t('settings_page.smtp.port')}
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="p-4">
                        <select
                            className="form-select w-full rounded-lg border-gray-200"
                            value={smtp.port}
                            onChange={(e) => handleChange('port', e.target.value)}
                        >
                            <option value="25">25 (SMTP)</option>
                            <option value="465">465 (SSL)</option>
                            <option value="587">587 (TLS)</option>
                            <option value="2525">2525 (Alternative)</option>
                        </select>
                    </CardContent>
                </Card>

                {/* Username */}
                <Card className="border border-gray-100 shadow-sm rounded-xl">
                    <CardHeader className="border-b border-gray-100 bg-gray-50/50 py-3">
                        <CardTitle className="text-sm font-semibold flex items-center gap-2">
                            <User className="h-4 w-4 text-green-500" />
                            {t('settings_page.smtp.username')}
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="p-4">
                        <input
                            type="text"
                            className="form-input w-full rounded-lg border-gray-200"
                            placeholder="user@gmail.com"
                            value={smtp.username}
                            onChange={(e) => handleChange('username', e.target.value)}
                        />
                    </CardContent>
                </Card>

                {/* Password */}
                <Card className="border border-gray-100 shadow-sm rounded-xl">
                    <CardHeader className="border-b border-gray-100 bg-gray-50/50 py-3">
                        <CardTitle className="text-sm font-semibold flex items-center gap-2">
                            <Lock className="h-4 w-4 text-red-500" />
                            {t('settings_page.smtp.password')}
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="p-4">
                        <input
                            type="password"
                            className="form-input w-full rounded-lg border-gray-200"
                            placeholder="••••••••"
                            value={smtp.password}
                            onChange={(e) => handleChange('password', e.target.value)}
                        />
                    </CardContent>
                </Card>

                {/* From Email */}
                <Card className="border border-gray-100 shadow-sm rounded-xl">
                    <CardHeader className="border-b border-gray-100 bg-gray-50/50 py-3">
                        <CardTitle className="text-sm font-semibold flex items-center gap-2">
                            <Mail className="h-4 w-4 text-orange-500" />
                            {t('settings_page.smtp.from_email')}
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="p-4">
                        <input
                            type="email"
                            className="form-input w-full rounded-lg border-gray-200"
                            placeholder="noreply@company.com"
                            value={smtp.fromEmail}
                            onChange={(e) => handleChange('fromEmail', e.target.value)}
                        />
                    </CardContent>
                </Card>

                {/* From Name */}
                <Card className="border border-gray-100 shadow-sm rounded-xl">
                    <CardHeader className="border-b border-gray-100 bg-gray-50/50 py-3">
                        <CardTitle className="text-sm font-semibold flex items-center gap-2">
                            <User className="h-4 w-4 text-cyan-500" />
                            {t('settings_page.smtp.from_name')}
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="p-4">
                        <input
                            type="text"
                            className="form-input w-full rounded-lg border-gray-200"
                            placeholder="Backoffice System"
                            value={smtp.fromName}
                            onChange={(e) => handleChange('fromName', e.target.value)}
                        />
                    </CardContent>
                </Card>
            </div>

            {/* Action Buttons */}
            <div className="flex flex-col sm:flex-row gap-3">
                <Button
                    onClick={handleTestEmail}
                    disabled={isTesting}
                    variant="outline"
                    className="border-blue-200 text-blue-600 hover:bg-blue-50"
                >
                    {isTesting ? (
                        <>
                            <Loader2 className="h-4 w-4 mr-2 animate-spin" />
                            {t('settings_page.smtp.testing')}
                        </>
                    ) : (
                        <>
                            <TestTube className="h-4 w-4 mr-2" />
                            {t('settings_page.smtp.test_button')}
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
                            {t('common.loading')}
                        </>
                    ) : (
                        t('common.save')
                    )}
                </Button>
            </div>
        </div>
    );
};

export default SMTPTab;
