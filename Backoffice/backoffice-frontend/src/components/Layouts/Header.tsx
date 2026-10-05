import { useEffect } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { Link, useLocation } from 'react-router-dom';
import { toggleSidebar } from '../../store/themeConfigSlice';
import { useTranslation } from 'react-i18next';
import Dropdown from '../Dropdown';
import { logOut } from '../../store/api/auth/authSlice';
import { removeUserInfo, getProfile } from "@/utils/auth";
import { Menu, LogOut, ChevronDown } from 'lucide-react';
import { apiSlice } from '@/store/api/apiSlice';

const Header = () => {
    const location = useLocation();
    const dispatch = useDispatch();
    // Get user from localStorage directly to ensure fresh data on every render
    const user = getProfile();
    const isRtl = useSelector((state: any) => state.themeConfig.rtlClass) === 'rtl';
    const themeConfig = useSelector((state: any) => state.themeConfig);
    const { t } = useTranslation();

    const isDev = window.location.hostname === 'localhost' || window.location.hostname === 'app-dev.example.com';
    const headerGradient = isDev
        ? 'bg-gradient-to-r from-orange-500 via-orange-500 to-orange-600'
        : 'bg-gradient-to-r from-primary via-primary to-[#4a7bd4]';

    const handleLogout = () => {
        // Clear localStorage
        removeUserInfo();
        // Reset Redux auth state
        dispatch(logOut());
        // Reset RTK Query cache to prevent stale data
        dispatch(apiSlice.util.resetApiState());
        // Use window.location.href to force full page reload
        // This ensures Redux re-initializes with empty localStorage
        window.location.href = '/login';
    };

    useEffect(() => {
        const selector = document.querySelector('ul.horizontal-menu a[href="' + window.location.pathname + '"]');
        if (selector) {
            selector.classList.add('active');
            const all: any = document.querySelectorAll('ul.horizontal-menu .nav-link.active');
            for (let i = 0; i < all.length; i++) {
                all[0]?.classList.remove('active');
            }
            const ul: any = selector.closest('ul.sub-menu');
            if (ul) {
                let ele: any = ul.closest('li.menu').querySelectorAll('.nav-link');
                if (ele) {
                    ele = ele[0];
                    setTimeout(() => {
                        ele?.classList.add('active');
                    });
                }
            }
        }
    }, [location]);

    const userInitials = user?.displayName
        ? user.displayName.split(' ').map((n: string) => n[0]).join('').slice(0, 2).toUpperCase()
        : '?';

    return (
        <header className={`z-40 ${themeConfig.semidark && themeConfig.menu === 'horizontal' ? 'dark' : ''}`}>
            <div className="shadow-md">
                <div className={`relative ${headerGradient} flex w-full items-center px-5 py-2.5 dark:bg-black`}>
                    {/* Sidebar Toggle */}
                    <button
                        type="button"
                        className="p-2 rounded-lg bg-white/10 hover:bg-white/20 text-white transition-all duration-200 ltr:mr-3 rtl:ml-3"
                        onClick={() => dispatch(toggleSidebar())}
                    >
                        <Menu className="w-5 h-5" />
                    </button>

                    {/* Mobile Logo */}
                    <div className="horizontal-logo flex lg:hidden items-center ltr:mr-2 rtl:ml-2">
                        <Link to="/" className="main-logo flex items-center shrink-0">
                            <div className="flex items-center gap-2">
                                <img className="w-8" src="/assets/images/logo-thb-transparent.png" alt="logo" />
                                <span className="text-xl font-semibold text-white">{t('RS THB')}</span>
                            </div>
                        </Link>
                    </div>

                    {/* Right Side */}
                    <div className="flex-1 flex items-center justify-end gap-3">
                        <div className="dropdown shrink-0">
                            <Dropdown
                                offset={[0, 8]}
                                placement={`${isRtl ? 'bottom-start' : 'bottom-end'}`}
                                btnClassName="relative group block"
                                button={
                                    <div className="flex items-center gap-2 p-1.5 pr-3 rounded-full bg-white/10 hover:bg-white/20 transition-all cursor-pointer">
                                        <div className="w-8 h-8 flex items-center justify-center rounded-full bg-white text-primary font-bold text-sm overflow-hidden">
                                            {user?.profilePicture ? (
                                                <img src={user.profilePicture} alt="Profile" className="w-full h-full object-cover" />
                                            ) : (
                                                userInitials
                                            )}
                                        </div>
                                        <span className="hidden sm:block text-sm font-medium text-white truncate max-w-[100px]">
                                            {user?.displayName || 'User'}
                                        </span>
                                        <ChevronDown className="w-4 h-4 text-white/70" />
                                    </div>
                                }
                            >
                                <ul className="text-dark dark:text-white-dark !py-0 w-[230px] font-semibold dark:text-white-light/90">
                                    <li>
                                        <div className="flex items-center px-4 py-4">

                                            <div className="ltr:pl-4 rtl:pr-4 truncate">
                                                <h4 className="text-base">{user?.displayName || ''}</h4>
                                                <button type="button" className="text-black/60 hover:text-primary dark:text-dark-light/60 dark:hover:text-white text-sm">
                                                    {user?.email}
                                                </button>
                                            </div>
                                        </div>
                                    </li>
                                    <li className="border-t border-white-light dark:border-white-light/10">
                                        <Link to={location.pathname} onClick={handleLogout} className="text-danger !py-3 flex items-center gap-2">
                                            <LogOut className="w-4 h-4" />
                                            Sign Out
                                        </Link>
                                    </li>
                                </ul>
                            </Dropdown>
                        </div>
                    </div>
                </div>
            </div>
        </header>
    );
};

export default Header;
