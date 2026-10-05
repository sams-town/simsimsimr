import { useEffect, useState } from 'react';
import AnimateHeight from 'react-animate-height';
import { useTranslation } from 'react-i18next';
import PerfectScrollbar from 'react-perfect-scrollbar';
import { useDispatch, useSelector } from 'react-redux';
import { NavLink, useLocation } from 'react-router-dom';
import { toggleSidebar } from '../../store/themeConfigSlice';
import { checkAccess } from '../../store/api/auth/authSlice';
import {
  LayoutDashboard,
  Database,
  Wallet,
  ArrowDownCircle,
  BookOpen,
  BarChart3,
  Settings,
  ChevronDown,
  Coins,
  RefreshCw
} from 'lucide-react';

const Sidebar = () => {
  const user = useSelector((state: any) => state.auth.user);
  const [currentMenu, setCurrentMenu] = useState<string>('');
  const [errorSubMenu, setErrorSubMenu] = useState(false);
  const themeConfig = useSelector((state: any) => state.themeConfig);
  const semidark = useSelector((state: any) => state.themeConfig.semidark);
  const location = useLocation();
  const dispatch = useDispatch();
  const { t } = useTranslation();

  // Role-based access helper
  const hasAccess = (pageCode: string, action?: string): boolean => {
    return checkAccess(user, pageCode, action);
  };

  // Get role ID for menu visibility
  const roleID = user?.role?.roleID;

  // Master Data menu: only Admin (1) and Manager (2)
  const hasMasterAccess = roleID === 1 || roleID === 2;

  // Report menu: exclude Staff (4) and Petty Cash (5)
  const hasReportAccess = hasAccess('Report') && roleID !== 4 && roleID !== 5;


  const toggleMenu = (value: string) => {
    setCurrentMenu((oldValue) => {
      return oldValue === value ? '' : value;
    });
  };


  useEffect(() => {
    const selector = document.querySelector(
      '.sidebar ul a[href="' + window.location.pathname + '"]'
    );
    if (selector) {
      selector.classList.add('active');
      const ul: any = selector.closest('ul.sub-menu');
      if (ul) {
        let ele: any = ul.closest('li.menu').querySelectorAll('.nav-link') || [];
        if (ele.length) {
          ele = ele[0];
          setTimeout(() => {
            ele.click();
          });
        }
      }
    }
  }, []);

  useEffect(() => {
    if (window.innerWidth < 1024 && themeConfig.sidebar) {
      dispatch(toggleSidebar());
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [location]);

  return (
    <div className={semidark ? 'dark' : ''}>
      <nav
        className={`sidebar fixed min-h-screen h-full top-0 bottom-0 w-[260px] shadow-[0_0_30px_0_rgba(100,141,225,0.12)] z-50 transition-all duration-300 ${semidark ? 'text-white-dark' : ''}`}
      >
        <div className="bg-gradient-to-b from-white to-gray-50/50 h-full">
          {/* Sidebar Header */}
          <div className="flex justify-between items-center px-4 py-3 border-b border-gray-100">
            <div className="flex items-center gap-3">
              <div className="p-1.5 bg-primary/10 rounded-lg">
                <img className="w-7 h-7" src="/assets/images/logo-thb-transparent.png" alt="logo" />
              </div>
              <div>
                <span className="text-lg font-bold text-gray-800 dark:text-white-light block">{t('auth.system_name')}</span>
                <span className="text-[10px] text-gray-400 uppercase tracking-wider">Backoffice</span>
              </div>
            </div>

            <button
              type="button"
              className="collapse-icon w-7 h-7 rounded-lg flex items-center justify-center hover:bg-primary/10 text-gray-400 hover:text-primary transition-all duration-200 rtl:rotate-180"
              onClick={() => dispatch(toggleSidebar())}
            >
              <ChevronDown className="w-4 h-4 -rotate-90" />
            </button>
          </div>
          <PerfectScrollbar className="h-[calc(100vh-72px)] relative">
            <ul className="relative font-semibold space-y-0.5 p-3 py-2">
              <li className="menu nav-item">
                <NavLink to="/">
                  <button
                    type="button"
                    className={`${currentMenu === 'dashboard' ? 'active' : ''
                      } nav-link group w-full`}
                    onClick={() => toggleMenu('dashboard')}
                  >
                    <div className="flex items-center">
                      <LayoutDashboard className="w-5 h-5 group-hover:!text-primary shrink-0" />
                      <span className="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">
                        {t('menu.dashboard')}
                      </span>
                    </div>
                  </button>
                </NavLink>
              </li>
              {/* Master Menu - Hidden for Supervisor and Staff */}
              {hasMasterAccess && (
                <li className="menu nav-item">
                  <button
                    type="button"
                    className={`${currentMenu === 'master' ? 'active' : ''} nav-link group w-full`}
                    onClick={() => toggleMenu('master')}
                  >
                    <div className="flex items-center">
                      <Database className="w-5 h-5 group-hover:!text-primary shrink-0" />
                      <span className="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">
                        {t('menu.master')}
                      </span>
                    </div>

                    <div className={currentMenu !== 'master' ? 'rtl:rotate-90 -rotate-90' : ''}>
                      <ChevronDown className="w-4 h-4" />
                    </div>
                  </button>

                  <AnimateHeight duration={300} height={currentMenu === 'master' ? 'auto' : 0}>
                    <ul className="sub-menu text-black ">
                      {/* Users - Only visible to Admin (1) */}
                      {roleID === 1 && hasAccess('MasterUser') && (
                        <li>
                          <NavLink to="/users">{t('menu.users')}</NavLink>
                        </li>
                      )}
                      {hasAccess('MasterDepartment') && (
                        <li>
                          <NavLink to="/department">{t('menu.department')}</NavLink>
                        </li>
                      )}
                      {hasAccess('MasterBpjsCost') && (
                        <li>
                          <NavLink to="/bpjs-cost">{t('menu.bpjs_cost')}</NavLink>
                        </li>
                      )}
                      {hasAccess('MasterCostCenter') && (
                        <li>
                          <NavLink to="/costCenter">{t('menu.cost_center')}</NavLink>
                        </li>
                      )}
                      {hasAccess('MasterAccountGroup') && (
                        <li>
                          <NavLink to="/accountGroup">{t('menu.account_group')}</NavLink>
                        </li>
                      )}
                      {hasAccess('MasterAccountType') && (
                        <li>
                          <NavLink to="/accountType">{t('menu.account_type')}</NavLink>
                        </li>
                      )}
                      {hasAccess('MasterCOA') && (
                        <li>
                          <NavLink to="/coa">{t('menu.coa')}</NavLink>
                        </li>
                      )}
                      {hasAccess('MasterCOA') && (
                        <li>
                          <NavLink to="/coa-mapping">{t('menu.coa_mapping')}</NavLink>
                        </li>
                      )}
                      {hasAccess('MasterBank') && (
                        <li>
                          <NavLink to="/bank">{t('menu.bank')}</NavLink>
                        </li>
                      )}
                      {hasAccess('MasterContact') && (
                        <li>
                          <NavLink to="/contact">{t('menu.contact')}</NavLink>
                        </li>
                      )}
                    </ul>
                  </AnimateHeight>
                </li>
              )}
              {/* Buku Kas - Protected (hidden for Petty Cash) */}
              {hasAccess('TrxBank') && roleID !== 5 && (
                <li className="menu nav-item">
                  <button
                    type="button"
                    className={`${currentMenu === 'bukukas' ? 'active' : ''} nav-link group w-full`}
                    onClick={() => toggleMenu('bukukas')}
                  >
                    <div className="flex items-center">
                      <Wallet className="w-5 h-5 group-hover:!text-primary shrink-0" />
                      <span className="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">
                        {t('menu.buku_kas')}
                      </span>
                    </div>

                    <div className={currentMenu !== 'bukukas' ? 'rtl:rotate-90 -rotate-90' : ''}>
                      <ChevronDown className="w-4 h-4" />
                    </div>
                  </button>

                  <AnimateHeight duration={300} height={currentMenu === 'bukukas' ? 'auto' : 0}>
                    <ul className="sub-menu text-black ">
                      <li>
                        <NavLink to="/daftar-buku-kas">{t('menu.buku_kas_list')}</NavLink>
                      </li>
                      <li>
                        <NavLink to="/daftar-transfer">{t('menu.transfer_list')}</NavLink>
                      </li>
                      <li>
                        <NavLink to="/saldo-awal">{t('menu.opening_balance', 'Saldo Awal')}</NavLink>
                      </li>
                    </ul>
                  </AnimateHeight>
                </li>
              )}

              {/* <li className={`menu nav-item`}>
                                <button type="button" className={`nav-link group w-full`} onClick={() => toggleMenu('User Management')}>
                                    <div className="flex items-center">
                                        <svg className="group-hover:!text-primary shrink-0" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                opacity="0.5"
                                                d="M13 15.4C13 13.3258 13 12.2887 13.659 11.6444C14.318 11 15.3787 11 17.5 11C19.6213 11 20.682 11 21.341 11.6444C22 12.2887 22 13.3258 22 15.4V17.6C22 19.6742 22 20.7113 21.341 21.3556C20.682 22 19.6213 22 17.5 22C15.3787 22 14.318 22 13.659 21.3556C13 20.7113 13 19.6742 13 17.6V15.4Z"
                                                fill={`${currentMenu === "User Management" ? "white" : 'black'}`}
                                            />
                                            <path
                                                d="M2 8.6C2 10.6742 2 11.7113 2.65901 12.3556C3.31802 13 4.37868 13 6.5 13C8.62132 13 9.68198 13 10.341 12.3556C11 11.7113 11 10.6742 11 8.6V6.4C11 4.32582 11 3.28873 10.341 2.64437C9.68198 2 8.62132 2 6.5 2C4.37868 2 3.31802 2 2.65901 2.64437C2 3.28873 2 4.32582 2 6.4V8.6Z"
                                                fill={`${currentMenu === "User Management" ? "white" : 'black'}`}
                                            />
                                            <path
                                                d="M13 5.5C13 4.4128 13 3.8692 13.1713 3.44041C13.3996 2.86867 13.8376 2.41443 14.389 2.17761C14.8024 2 15.3266 2 16.375 2H18.625C19.6734 2 20.1976 2 20.611 2.17761C21.1624 2.41443 21.6004 2.86867 21.8287 3.44041C22 3.8692 22 4.4128 22 5.5C22 6.5872 22 7.1308 21.8287 7.55959C21.6004 8.13133 21.1624 8.58557 20.611 8.82239C20.1976 9 19.6734 9 18.625 9H16.375C15.3266 9 14.8024 9 14.389 8.82239C13.8376 8.58557 13.3996 8.13133 13.1713 7.55959C13 7.1308 13 6.5872 13 5.5Z"
                                                fill={`${currentMenu === "User Management" ? "white" : 'black'}`}
                                            />
                                            <path
                                                opacity="0.5"
                                                d="M2 18.5C2 19.5872 2 20.1308 2.17127 20.5596C2.39963 21.1313 2.83765 21.5856 3.38896 21.8224C3.80245 22 4.32663 22 5.375 22H7.625C8.67337 22 9.19755 22 9.61104 21.8224C10.1624 21.5856 10.6004 21.1313 10.8287 20.5596C11 20.1308 11 19.5872 11 18.5C11 17.4128 11 16.8692 10.8287 16.4404C10.6004 15.8687 10.1624 15.4144 9.61104 15.1776C9.19755 15 8.67337 15 7.625 15H5.375C4.32663 15 3.80245 15 3.38896 15.1776C2.83765 15.4144 2.39963 15.8687 2.17127 16.4404C2 16.8692 2 17.4128 2 18.5Z"
                                                fill={`${currentMenu === "User Management" ? "white" : 'black'}`}
                                            />
                                        </svg>
                                        <span className={`ltr:pl-3 rtl:pr-3 ${currentMenu === 'User Management' ? 'text-white' : 'text-black'}`}>{t('User Management')}</span>
                                    </div>
                                    <div className={currentMenu !== 'User Management' ? 'rtl:rotate-90 -rotate-90' : ''}>
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M19 9L12 15L5 9" stroke={`${currentMenu === 'User Management' ? 'white' : 'black'}`} strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"></path>
                                        </svg>
                                    </div>
                                </button>
                                <AnimateHeight duration={300} height={currentMenu === 'User Management' ? 'auto' : 0}>
                                    <ul className={`sub-menu text-black bg-white`}>
                                        <li>
                                            <NavLink to="/users">
                                                Users
                                            </NavLink>
                                        </li>
                                        <li>
                                            <NavLink to="/roles">
                                                Roles
                                            </NavLink>
                                        </li>

                                    </ul>
                                </AnimateHeight>
                            </li> */}

              {hasAccess('TrxTransaction') && roleID !== 5 && (
                <li className="menu nav-item">
                  <button
                    type="button"
                    className={`${currentMenu === 'penerimaan' ? 'active' : ''
                      } nav-link group w-full`}
                    onClick={() => toggleMenu('penerimaan')}
                  >
                    <div className="flex items-center">
                      <ArrowDownCircle className="w-5 h-5 group-hover:!text-primary shrink-0" />
                      <span className="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">
                        {t('menu.penerimaan')}
                      </span>
                    </div>

                    <div className={currentMenu !== 'users' ? 'rtl:rotate-90 -rotate-90' : ''}>
                      <ChevronDown className="w-4 h-4" />
                    </div>
                  </button>
                  <AnimateHeight duration={300} height={currentMenu === 'penerimaan' ? 'auto' : 0}>
                    <ul className={`sub-menu text-black bg-white`}>
                      <li>
                        <NavLink to="/daftar-penerimaan">{t('menu.revenue_list')}</NavLink>
                      </li>
                      <li>
                        <NavLink to="/daftar-penerimaan/form-maker">Form Maker Penerimaan</NavLink>
                      </li>
                    </ul>
                  </AnimateHeight>
                </li>
              )}

              {hasAccess('TrxTransaction') && roleID !== 5 && (
                <li className="menu nav-item">
                  <button
                    type="button"
                    className={`${currentMenu === 'pengeluaran' ? 'active' : ''
                      } nav-link group w-full`}
                    onClick={() => toggleMenu('pengeluaran')}
                  >
                    <div className="flex items-center">
                      <ArrowDownCircle className="w-5 h-5 group-hover:!text-primary shrink-0" />
                      <span className="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">
                        {t('menu.pengeluaran')}
                      </span>
                    </div>

                    <div className={currentMenu !== 'pengeluaran' ? 'rtl:rotate-90 -rotate-90' : ''}>
                      <ChevronDown className="w-4 h-4" />
                    </div>
                  </button>
                  <AnimateHeight duration={300} height={currentMenu === 'pengeluaran' ? 'auto' : 0}>
                    <ul className={`sub-menu text-black bg-white`}>
                      <li>
                        <NavLink to="/daftar-pengeluaran">{t('menu.expense_list')}</NavLink>
                      </li>
                      <li>
                        <NavLink to="/daftar-pengeluaran/form-maker">Form Maker Pengeluaran</NavLink>
                      </li>
                      {/* <li>
                                              <NavLink to="/daftar-biaya">
                                                  {t('Daftar Biaya')}
                                              </NavLink>
                                          </li>
                                          <li>
                                              <NavLink to="/daftar-biaya-rutin">
                                                  {t('Daftar Biaya Rutin')}
                                              </NavLink>
                                          </li> */}
                    </ul>
                  </AnimateHeight>
                </li>
              )}

              {/* <li className="menu nav-item">
                                <button type="button" className={`${currentMenu === 'receiveble' ? 'active' : ''} nav-link group w-full`} onClick={() => toggleMenu('receiveble')}>
                                    <div className="flex items-center">
                                        <ArrowDownCircle className="w-5 h-5 group-hover:!text-primary shrink-0" />
                                        <span className="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">{t('Receiveble')}</span>
                                    </div>

                                    <div className={currentMenu !== 'receivable' ? 'rtl:rotate-90 -rotate-90' : ''}>
                                        <ChevronDown className="w-4 h-4" />
                                    </div>
                                </button>
                                <AnimateHeight duration={300} height={currentMenu === 'receivable' ? 'auto' : 0}>
                                    <ul className={`sub-menu text-black bg-white`}>
                                        <li>
                                            <NavLink to="/aging-schedule">
                                                {t('Aging Schedule')}
                                            </NavLink>
                                        </li>
                                        <li>
                                            <NavLink to="/payment-period">
                                                {t('Payment Period')}
                                            </NavLink>
                                        </li>
                                    </ul>
                                </AnimateHeight>
                            </li> */}
              {/* <li className={`menu nav-item ${currentMenu === 'bank' ? 'bg-[#fa6e15]' : ''} hover:bg-purple-300`}>
                                <button type="button" className={`nav-link group w-full`} onClick={() => toggleMenu('bank')}>
                                    <div className="flex items-center">
                                        <svg className="group-hover:!text-primary shrink-0" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                opacity="0.5"
                                                d="M13 15.4C13 13.3258 13 12.2887 13.659 11.6444C14.318 11 15.3787 11 17.5 11C19.6213 11 20.682 11 21.341 11.6444C22 12.2887 22 13.3258 22 15.4V17.6C22 19.6742 22 20.7113 21.341 21.3556C20.682 22 19.6213 22 17.5 22C15.3787 22 14.318 22 13.659 21.3556C13 20.7113 13 19.6742 13 17.6V15.4Z"
                                                fill={`${currentMenu === "bank" ? "white" : 'black'}`}
                                            />
                                            <path
                                                d="M2 8.6C2 10.6742 2 11.7113 2.65901 12.3556C3.31802 13 4.37868 13 6.5 13C8.62132 13 9.68198 13 10.341 12.3556C11 11.7113 11 10.6742 11 8.6V6.4C11 4.32582 11 3.28873 10.341 2.64437C9.68198 2 8.62132 2 6.5 2C4.37868 2 3.31802 2 2.65901 2.64437C2 3.28873 2 4.32582 2 6.4V8.6Z"
                                                fill={`${currentMenu === "bank" ? "white" : 'black'}`}
                                            />
                                            <path
                                                d="M13 5.5C13 4.4128 13 3.8692 13.1713 3.44041C13.3996 2.86867 13.8376 2.41443 14.389 2.17761C14.8024 2 15.3266 2 16.375 2H18.625C19.6734 2 20.1976 2 20.611 2.17761C21.1624 2.41443 21.6004 2.86867 21.8287 3.44041C22 3.8692 22 4.4128 22 5.5C22 6.5872 22 7.1308 21.8287 7.55959C21.6004 8.13133 21.1624 8.58557 20.611 8.82239C20.1976 9 19.6734 9 18.625 9H16.375C15.3266 9 14.8024 9 14.389 8.82239C13.8376 8.58557 13.3996 8.13133 13.1713 7.55959C13 7.1308 13 6.5872 13 5.5Z"
                                                fill={`${currentMenu === "bank" ? "white" : 'black'}`}
                                            />
                                            <path
                                                opacity="0.5"
                                                d="M2 18.5C2 19.5872 2 20.1308 2.17127 20.5596C2.39963 21.1313 2.83765 21.5856 3.38896 21.8224C3.80245 22 4.32663 22 5.375 22H7.625C8.67337 22 9.19755 22 9.61104 21.8224C10.1624 21.5856 10.6004 21.1313 10.8287 20.5596C11 20.1308 11 19.5872 11 18.5C11 17.4128 11 16.8692 10.8287 16.4404C10.6004 15.8687 10.1624 15.4144 9.61104 15.1776C9.19755 15 8.67337 15 7.625 15H5.375C4.32663 15 3.80245 15 3.38896 15.1776C2.83765 15.4144 2.39963 15.8687 2.17127 16.4404C2 16.8692 2 17.4128 2 18.5Z"
                                                fill={`${currentMenu === "bank" ? "white" : 'black'}`}
                                            />
                                        </svg>
                                        <span className={`ltr:pl-3 rtl:pr-3 ${currentMenu === 'bank' ? 'text-white' : 'text-black'}`}>{t('Bank')}</span>
                                    </div>
                                    <div className={currentMenu !== 'bank' ? 'rtl:rotate-90 -rotate-90' : ''}>
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M19 9L12 15L5 9" stroke={`${currentMenu === 'receivable' ? 'white' : 'black'}`} strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"></path>
                                        </svg>
                                    </div>
                                </button>
                                <AnimateHeight duration={300} height={currentMenu === 'bank' ? 'auto' : 0}>
                                    <ul className={`sub-menu text-black bg-white`}>
                                        <li>
                                            <NavLink to="/bookBank">
                                                Book Bank
                                            </NavLink>
                                        </li>
                                        <li>
                                            <NavLink to="/deposit">
                                                Deposit
                                            </NavLink>
                                        </li>
                                        <li>
                                            <NavLink to="/payment">
                                                Payment
                                            </NavLink>
                                        </li>
                                        <li>
                                            <NavLink to="/reconcile">
                                                Reconcile
                                            </NavLink>
                                        </li>
                                    </ul>
                                </AnimateHeight>
                            </li> */}
              {hasAccess('TrxJournal') && roleID !== 5 && (
                <li className="menu nav-item">
                  <button
                    type="button"
                    className={`${currentMenu === 'general-ledger' ? 'active' : ''
                      } nav-link group w-full`}
                    onClick={() => toggleMenu('general-ledger')}
                  >
                    <div className="flex items-center">
                      <BookOpen className="w-5 h-5 group-hover:!text-primary shrink-0" />
                      <span className="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">
                        {t('menu.general_ledger')}
                      </span>
                    </div>

                    <div
                      className={currentMenu !== 'general-ledger' ? 'rtl:rotate-90 -rotate-90' : ''}
                    >
                      <ChevronDown className="w-4 h-4" />
                    </div>
                  </button>
                  <AnimateHeight
                    duration={300}
                    height={currentMenu === 'general-ledger' ? 'auto' : 0}
                  >
                    <ul className={`sub-menu text-black bg-white`}>
                      <li>
                        <NavLink to="/general-ledger/history">{t('menu.history')}</NavLink>
                      </li>
                      <li>
                        <NavLink to="/general-ledger/summary">{t('menu.summary')}</NavLink>
                      </li>
                      <li>
                        <NavLink to="/report/tutup-buku">{t('menu.period_closing', 'Tutup Buku')}</NavLink>
                      </li>
                    </ul>
                  </AnimateHeight>
                </li>
              )}

              {/* Account Payable Menu */}
              {/* Account Payable Menu */}
              {hasAccess('TrxAccountPayable') && roleID !== 5 && (
                <li className="menu nav-item">
                  <button
                    type="button"
                    className={`${currentMenu === 'account-payable' ? 'active' : ''} nav-link group w-full`}
                    onClick={() => toggleMenu('account-payable')}
                  >
                    <div className="flex items-center">
                      <Database className="w-5 h-5 group-hover:!text-primary shrink-0" />
                      <span className="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">
                        {t('menu.account_payable')}
                      </span>
                    </div>

                    <div className={currentMenu !== 'account-payable' ? 'rtl:rotate-90 -rotate-90' : ''}>
                      <ChevronDown className="w-4 h-4" />
                    </div>
                  </button>
                  <AnimateHeight duration={300} height={currentMenu === 'account-payable' ? 'auto' : 0}>
                    <ul className={`sub-menu text-black bg-white`}>
                      {hasAccess('TrxPurchaseOrder') && (
                        <li>
                          <NavLink to="/account-payable/purchase-order">{t('menu.purchase_order') || 'Purchase Order'}</NavLink>
                        </li>
                      )}
                      {hasAccess('TrxInvoiceExchange') && (
                        <li>
                          <NavLink to="/account-payable/tukar-faktur">{t('menu.invoice_exchange')}</NavLink>
                        </li>
                      )}
                      <li>
                        <NavLink to="/account-payable/daftar-ap">{t('menu.ap_list')}</NavLink>
                      </li>
                      <li>
                        <NavLink to="/account-payable/pembayaran">{t('menu.ap_payment')}</NavLink>
                      </li>
                      <li>
                        <NavLink to="/account-payable/form-maker">Form Maker</NavLink>
                      </li>
                      <li>
                        <NavLink to="/account-payable/laporan-aging">{t('menu.aging_report')}</NavLink>
                      </li>
                    </ul>
                  </AnimateHeight>
                </li>
              )}

              {/* Jasa Medis - New Top Level Menu */}
              {/* Jasa Medis - New Top Level Menu */}
              {hasAccess('TrxMedicalFee') && roleID !== 5 && (
                <li className="menu nav-item">
                  <button
                    type="button"
                    className={`${currentMenu === 'jasa-medis' ? 'active' : ''} nav-link group w-full`}
                    onClick={() => toggleMenu('jasa-medis')}
                  >
                    <div className="flex items-center">
                      <Database className="w-5 h-5 group-hover:!text-primary shrink-0" />
                      <span className="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">
                        {t('menu.jasa_medis')}
                      </span>
                    </div>

                    <div className={currentMenu !== 'jasa-medis' ? 'rtl:rotate-90 -rotate-90' : ''}>
                      <ChevronDown className="w-4 h-4" />
                    </div>
                  </button>
                  <AnimateHeight duration={300} height={currentMenu === 'jasa-medis' ? 'auto' : 0}>
                    <ul className={`sub-menu text-black bg-white`}>
                      <li>
                        <NavLink to="/jasa-medis/posting">{t('menu.posting_jasa_medis')}</NavLink>
                      </li>
                      <li>
                        <NavLink to="/jasa-medis/payment-voucher">{t('menu.payment_voucher')}</NavLink>
                      </li>
                      <li>
                        <NavLink to="/jasa-medis/settlement">{t('menu.settlement')}</NavLink>
                      </li>
                    </ul>
                  </AnimateHeight>
                </li>
              )}

              {hasAccess('TrxAccountReceivable') && roleID !== 5 && (
                <li className="menu nav-item">
                  <button
                    type="button"
                    className={`${currentMenu === 'account-receivable' ? 'active' : ''} nav-link group w-full`}
                    onClick={() => toggleMenu('account-receivable')}
                  >
                    <div className="flex items-center">
                      <Database className="w-5 h-5 group-hover:!text-primary shrink-0" />
                      <span className="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">
                        {t('menu.piutang_usaha')}
                      </span>
                    </div>

                    <div className={currentMenu !== 'account-receivable' ? 'rtl:rotate-90 -rotate-90' : ''}>
                      <ChevronDown className="w-4 h-4" />
                    </div>
                  </button>
                  <AnimateHeight duration={300} height={currentMenu === 'account-receivable' ? 'auto' : 0}>
                    <ul className={`sub-menu text-black bg-white`}>
                      <li>
                        <NavLink to="/account-receivable/daftar-ar">{t('menu.posting_ar')}</NavLink>
                      </li>
                      <li>
                        <NavLink to="/account-receivable/payment-voucher">{t('menu.payment_voucher_ar')}</NavLink>
                      </li>
                      <li>
                        <NavLink to="/account-receivable/combined-voucher">{t('menu.combined_voucher_ar')}</NavLink>
                      </li>
                      <li>
                        <NavLink to="/account-receivable/settlement">{t('menu.settlement_ar')}</NavLink>
                      </li>
                      <li>
                        <NavLink to="/account-receivable/laporan-aging">{t('menu.aging_report')}</NavLink>
                      </li>
                    </ul>
                  </AnimateHeight>
                </li>
              )}

              {hasAccess('TrxPettyCash') && (
                <li className="menu nav-item">
                  <button
                    type="button"
                    className={`${currentMenu === 'petty-cash' ? 'active' : ''} nav-link group w-full`}
                    onClick={() => toggleMenu('petty-cash')}
                  >
                    <div className="flex items-center">
                      <Coins className="w-5 h-5 group-hover:!text-primary shrink-0" />
                      <span className="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">
                        {t('menu.petty_cash')}
                      </span>
                    </div>

                    <div className={currentMenu !== 'petty-cash' ? 'rtl:rotate-90 -rotate-90' : ''}>
                      <ChevronDown className="w-4 h-4" />
                    </div>
                  </button>
                  <AnimateHeight duration={300} height={currentMenu === 'petty-cash' ? 'auto' : 0}>
                    <ul className={`sub-menu text-black bg-white`}>
                      <li>
                        <NavLink to="/petty-cash/dashboard">{t('menu.petty_cash_dashboard')}</NavLink>
                      </li>
                      {roleID !== 5 && (
                        <li>
                          <NavLink to="/petty-cash/unit">{t('menu.petty_cash_unit')}</NavLink>
                        </li>
                      )}
                      <li>
                        <NavLink to="/petty-cash/request">{t('menu.petty_cash_request')}</NavLink>
                      </li>
                      <li>
                        <NavLink to="/petty-cash/settlement">{t('menu.petty_cash_settlement')}</NavLink>
                      </li>
                      <li>
                        <NavLink to="/petty-cash/topup">{t('menu.petty_cash_topup')}</NavLink>
                      </li>
                      <li>
                        <NavLink to="/petty-cash/ledger">{t('menu.petty_cash_ledger')}</NavLink>
                      </li>
                    </ul>
                  </AnimateHeight>
                </li>
              )}

              {hasAccess('TrxJournal') && roleID !== 5 && (
                <li className="menu nav-item">
                  <button
                    type="button"
                    className={`${currentMenu === 'journal' ? 'active' : ''} nav-link group w-full`}
                    onClick={() => toggleMenu('journal')}
                  >
                    <div className="flex items-center">
                      <BookOpen className="w-5 h-5 group-hover:!text-primary shrink-0" />
                      <span className="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">
                        {t('menu.journal')}
                      </span>
                    </div>

                    <div className={currentMenu !== 'journal' ? 'rtl:rotate-90 -rotate-90' : ''}>
                      <ChevronDown className="w-4 h-4" />
                    </div>
                  </button>
                  <AnimateHeight duration={300} height={currentMenu === 'journal' ? 'auto' : 0}>
                    <ul className={`sub-menu text-black bg-white`}>
                      <li>
                        <NavLink to="/journal/trial-journal">{t('menu.trial_journal')}</NavLink>
                      </li>
                      <li>
                        <NavLink to="/jurnal-umum">{t('menu.general_journal')}</NavLink>
                      </li>
                    </ul>
                  </AnimateHeight>
                </li>
              )}

              {/* Report Menu - Hidden for Staff */}
              {hasReportAccess && (
                <li className="menu nav-item">
                  <button
                    type="button"
                    className={`${currentMenu === 'report' ? 'active' : ''} nav-link group w-full`}
                    onClick={() => toggleMenu('report')}
                  >
                    <div className="flex items-center">
                      <Database className="w-5 h-5 group-hover:!text-primary shrink-0" />
                      <span className="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">
                        {t('menu.report')}
                      </span>
                    </div>

                    <div className={currentMenu !== 'report' ? 'rtl:rotate-90 -rotate-90' : ''}>
                      <ChevronDown className="w-4 h-4" />
                    </div>
                  </button>
                  <AnimateHeight duration={300} height={currentMenu === 'report' ? 'auto' : 0}>
                    <ul className={`sub-menu text-black bg-white`}>
                      <li>
                        <NavLink to="/journal/journal-report">{t('menu.journal_report')}</NavLink>
                      </li>
                      <li>
                        <NavLink to="/ledger">{t('menu.ledger_report')}</NavLink>
                      </li>
                      <li>
                        <NavLink to="/report/balance-sheet">{t('menu.balance_sheet')}</NavLink>
                      </li>
                      <li>
                        <NavLink to="/report/profit-loss">{t('menu.profit_loss')}</NavLink>
                      </li>
                      <li>
                        <NavLink to="/report/cash-flow">{t('menu.cash_flow', 'Arus Kas')}</NavLink>
                      </li>
                      <li>
                        <NavLink to="/report/trial-balance">{t('menu.trial_balance')}</NavLink>
                      </li>
                      <li>
                        <NavLink to="/journal/khanza">{t('menu.khanza_downloader')}</NavLink>
                      </li>
                    </ul>
                  </AnimateHeight>
                </li>
              )}
              {hasAccess('CfgKhanzaSync') && (
                <li className="menu nav-item">
                  <NavLink to="/settings/khanza-sync" className="nav-link group w-full">
                    <div className="flex items-center">
                      <RefreshCw className="w-5 h-5 group-hover:!text-primary shrink-0" />
                      <span className="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">
                        {t('menu.khanza_sync', 'Sinkronisasi Khanza')}
                      </span>
                    </div>
                  </NavLink>
                </li>
              )}
              <li className="menu nav-item">
                <NavLink
                  to="/settings"
                  className={`${currentMenu === 'setting' ? 'active' : ''} nav-link group w-full`}
                >
                  <div className="flex items-center">
                    <Settings className="w-5 h-5 group-hover:!text-primary shrink-0" />
                    <span className="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">
                      {t('menu.settings')}
                    </span>
                  </div>
                </NavLink>
              </li>
            </ul>
          </PerfectScrollbar>
        </div>
      </nav>
    </div>
  );
};

export default Sidebar;
