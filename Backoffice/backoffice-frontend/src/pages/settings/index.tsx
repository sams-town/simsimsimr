import { useEffect, useState } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { useTranslation } from 'react-i18next';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '@/store/themeConfigSlice';
import { checkAccess } from '@/store/api/auth/authSlice';
import { Card, CardContent } from '@/components/ui/card';
import { Settings, User, Sliders, Landmark, FileText, Mail, Database, Terminal, ShieldCheck } from 'lucide-react';
import ProfileTab from './components/ProfileTab';
import PreferencesTab from './components/PreferencesTab';
import DefaultAccountsTab from './components/DefaultAccountsTab';
import ReportTab from './components/ReportTab';
import SMTPTab from './components/SMTPTab';
import KhanzaConfigTab from './components/KhanzaConfigTab';
import MigrationJobTab from './components/MigrationJobTab';
import AuditTrailTab from './components/AuditTrailTab';
import PrintSignaturesTab from './components/PrintSignaturesTab';

type TabType = 'profile' | 'preferences' | 'default_accounts' | 'smtp' | 'report' | 'khanza' | 'migration' | 'audit_trail' | 'print_signatures';

interface Tab {
    id: TabType;
    label: string;
    icon: React.ReactNode;
}

// User settings tabs - visible to ALL roles
const userTabs: Tab[] = [
    { id: 'profile', label: 'settings_page.tabs.profile', icon: <User className="h-4 w-4" /> },
    { id: 'preferences', label: 'settings_page.tabs.preferences', icon: <Sliders className="h-4 w-4" /> },
];

// Global settings tabs - visible only to Admin/Manager (those with CfgGlobalSettings access)
// Global settings tabs - visible to Admin/Manager (those with CfgGlobalSettings access)
const commonGlobalTabs: Tab[] = [
    { id: 'default_accounts', label: 'settings_page.tabs.default_accounts', icon: <Landmark className="h-4 w-4" /> },
    { id: 'print_signatures', label: 'settings_page.tabs.print_signatures', icon: <FileText className="h-4 w-4" /> },
    { id: 'report', label: 'settings_page.tabs.report_config', icon: <FileText className="h-4 w-4" /> },
];

// Sensitive global settings - visible only to Admin (those with MasterUser access as proxy)
const sensitiveGlobalTabs: Tab[] = [
    { id: 'smtp', label: 'settings_page.tabs.smtp', icon: <Mail className="h-4 w-4" /> },
    { id: 'khanza', label: 'settings_page.tabs.khanza_config', icon: <Database className="h-4 w-4" /> },
    { id: 'migration', label: 'settings_page.tabs.migration', icon: <Terminal className="h-4 w-4" /> },
    { id: 'audit_trail', label: 'settings_page.tabs.audit_trail', icon: <ShieldCheck className="h-4 w-4" /> },
];

const SettingsPage = () => {
    const dispatch = useDispatch();
    const { t } = useTranslation();
    const user = useSelector((state: any) => state.auth.user);
    const [activeTab, setActiveTab] = useState<TabType>('profile');

    const roleID = user?.role?.roleID;

    // Global settings tabs (Default Accounts, Report Config): only Admin (1) and Manager (2)
    const hasGlobalSettingsAccess = (roleID === 1 || roleID === 2) && checkAccess(user, 'CfgGlobalSettings');

    // Sensitive settings tabs (SMTP, Khanza Config): only Admin (1)
    const hasSensitiveSettingsAccess = roleID === 1 && checkAccess(user, 'MasterUser');

    // Combine tabs based on access
    const tabs = [
        ...userTabs,
        ...(hasGlobalSettingsAccess ? commonGlobalTabs : []),
        ...(hasSensitiveSettingsAccess ? sensitiveGlobalTabs : [])
    ];

    useEffect(() => {
        dispatch(setPageTitle(t('settings_page.title')));
        dispatch(setTitle(t('settings_page.title')));
        dispatch(setBreadcrumbTitle([t('menu.dashboard'), t('settings_page.title')]));
    }, [dispatch, t]);

    const renderTabContent = () => {
        switch (activeTab) {
            case 'profile':
                return <ProfileTab />;
            case 'preferences':
                return <PreferencesTab />;
            case 'default_accounts':
                return hasGlobalSettingsAccess ? <DefaultAccountsTab /> : <ProfileTab />;
            case 'smtp':
                return hasSensitiveSettingsAccess ? <SMTPTab /> : <ProfileTab />;
            case 'report':
                return hasGlobalSettingsAccess ? <ReportTab /> : <ProfileTab />;
            case 'print_signatures':
                return hasGlobalSettingsAccess ? <PrintSignaturesTab /> : <ProfileTab />;
            case 'khanza':
                return hasSensitiveSettingsAccess ? <KhanzaConfigTab /> : <ProfileTab />;
            case 'migration':
                return hasSensitiveSettingsAccess ? <MigrationJobTab /> : <ProfileTab />;
            case 'audit_trail':
                return hasSensitiveSettingsAccess ? <AuditTrailTab /> : <ProfileTab />;
            default:
                return <ProfileTab />;
        }
    };

    return (
        <div className="space-y-6 animate-fade-in">
            {/* Header */}
            <Card className="bg-white border-0 shadow-sm rounded-2xl overflow-hidden relative">
                <div className="absolute top-0 right-0 w-32 h-32 bg-primary/5 rounded-bl-full -mr-10 -mt-10"></div>
                <CardContent className="p-6 relative z-10">
                    <div className="flex items-center gap-5">
                        <div className="p-4 rounded-2xl bg-primary/10 text-primary shadow-sm">
                            <Settings className="h-8 w-8" />
                        </div>
                        <div>
                            <h1 className="text-2xl font-bold text-gray-800">{t('settings_page.title')}</h1>
                            <p className="text-gray-500 text-sm mt-1">
                                {t('settings_page.subtitle')}
                            </p>
                        </div>
                    </div>
                </CardContent>
            </Card>

            {/* Tabs Container */}
            <Card className="bg-white border-0 shadow-sm rounded-2xl overflow-hidden">
                <CardContent className="p-0">
                    {/* Tab Navigation */}
                    <div className="border-b border-gray-100 overflow-x-auto">
                        <nav className="flex px-6 -mb-px" aria-label="Tabs">
                            {tabs.map((tab) => (
                                <button
                                    key={tab.id}
                                    onClick={() => setActiveTab(tab.id)}
                                    className={`
                    flex items-center gap-2 py-4 px-4 border-b-2 font-medium text-sm transition-all whitespace-nowrap
                    ${activeTab === tab.id
                                            ? 'border-primary text-primary'
                                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                        }
                  `}
                                >
                                    {tab.icon}
                                    <span>{t(tab.label)}</span>
                                </button>
                            ))}
                        </nav>
                    </div>

                    {/* Tab Content */}
                    <div className="p-6">
                        {renderTabContent()}
                    </div>
                </CardContent>
            </Card>
        </div>
    );
};

export default SettingsPage;

