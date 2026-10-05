import { lazy } from 'react';
import ProtectedRoute from '../components/ProtectedRoute';

const Index = lazy(() => import('../pages/Index'));
const StaffDashboard = lazy(() => import('../pages/StaffDashboard'));
// User
const UserIndex = lazy(() => import('../pages/users'));
const UserCreate = lazy(() => import('../pages/users/form'));
const UserUpdate = lazy(() => import('../pages/users/form'));
const LoginBoxed = lazy(() => import('../pages/Auth/LoginBoxed'));
const ForgotPasswordBoxed = lazy(() => import('../pages/Auth/ForgotPasswordBoxed'));
const ResetPasswordBoxed = lazy(() => import('../pages/Auth/ResetPasswordBoxed'));

// Department
const DepartmentIndex = lazy(() => import('../pages/Master/Department'));
const DepartmentCreate = lazy(() => import('../pages/Master/Department/form'));
const DepartmentUpdate = lazy(() => import('../pages/Master/Department/form'));

// BpjsCost
const BpjsCostIndex = lazy(() => import('../pages/Master/BpjsCost'));
const BpjsCostCreate = lazy(() => import('../pages/Master/BpjsCost/form'));
const BpjsCostUpdate = lazy(() => import('../pages/Master/BpjsCost/form'));

// Master Bank
const BankIndex = lazy(() => import('../pages/Master/Bank'));
const BankCreate = lazy(() => import('../pages/Master/Bank/form'));
const BankUpdate = lazy(() => import('../pages/Master/Bank/form'));

// CostCenter
const CostCenterIndex = lazy(() => import('../pages/Master/CostCenter'));
const CostCenterCreate = lazy(() => import('../pages/Master/CostCenter/form'));
const CostCenterUpdate = lazy(() => import('../pages/Master/CostCenter/form'));

/// Contact
const ContactIndex = lazy(() => import('../pages/Master/Contact'));
const ContactCreate = lazy(() => import('../pages/Master/Contact/form'));
const ContactUpdate = lazy(() => import('../pages/Master/Contact/form'));

// AccountGroup
const AccountGroupIndex = lazy(() => import('../pages/Master/AccountGroup'));
const AccountGroupCreate = lazy(() => import('../pages/Master/AccountGroup/form'));
const AccountGroupUpdate = lazy(() => import('../pages/Master/AccountGroup/form'));

// AccountType
const AccountTypeIndex = lazy(() => import('../pages/Master/AccountType'));
const AccountTypeCreate = lazy(() => import('../pages/Master/AccountType/form'));
const AccountTypeUpdate = lazy(() => import('../pages/Master/AccountType/form'));

// Buku Besar
const CoAIndex = lazy(() => import('../pages/Master/Coa'));
const CoACreate = lazy(() => import('../pages/Master/Coa/form'));
const CoAUpdate = lazy(() => import('../pages/Master/Coa/form'));
const CoaMappingIndex = lazy(() => import('../pages/Master/CoaMapping'));
const CoaMappingForm = lazy(() => import('../pages/Master/CoaMapping/form'));

/// Balance Sheet
const BalanceSheetIndex = lazy(() => import('../pages/Report/BalanceSheet'));
const BalanceSheetCreate = lazy(() => import('../pages/Report/BalanceSheet/form'));

/// Profit Loss
const ProfitLossIndex = lazy(() => import('../pages/Report/ProfitLoss'));

/// Cash Flow
const CashFlowIndex = lazy(() => import('../pages/Report/CashFlow'));

/// Trial Balance
const TrialBalanceIndex = lazy(() => import('../pages/Report/TrialBalance'));
const TutupBukuIndex = lazy(() => import('../pages/Report/TutupBuku'));

// General Ledger History
const GeneralLedgerHistoryIndex = lazy(() => import('../pages/GeneralLedger/History'));

// General Ledger Summary
const GeneralLedgerSummaryIndex = lazy(() => import('../pages/GeneralLedger/Summary'));

// Kas Bank
const DaftarBukuKasIndex = lazy(() => import('../pages/BukuKas/DaftarBukuKas'));
const DaftarBukuKasCreate = lazy(() => import('../pages/BukuKas/DaftarBukuKas/form'));
const DaftarBukuKasDetail = lazy(() => import('../pages/BukuKas/DaftarBukuKas/detail'));
const SaldoAwalIndex = lazy(() => import('../pages/BukuKas/SaldoAwal'));

const DaftarTransferIndex = lazy(() => import('../pages/BukuKas/DaftarTransfer'));
const DaftarTransferCreate = lazy(() => import('../pages/BukuKas/DaftarTransfer/form'));
const DaftarTransferUpdate = lazy(() => import('../pages/BukuKas/DaftarTransfer/form'));
const DaftarTransferDetail = lazy(() => import('../pages/BukuKas/DaftarTransfer/view'));

// Penerimaan
const DaftarPenerimaanIndex = lazy(() => import('../pages/Penerimaan/DaftarPenerimaan'));
const DaftarPenerimaanCreate = lazy(() => import('../pages/Penerimaan/DaftarPenerimaan/form'));
const DaftarPenerimaanUpdate = lazy(() => import('../pages/Penerimaan/DaftarPenerimaan/form'));
const DaftarPenerimaanView = lazy(() => import('../pages/Penerimaan/DaftarPenerimaan/view'));
const PenerimaanFormMakerList = lazy(() => import('../pages/Penerimaan/FormMaker'));
const PenerimaanFormMakerDetail = lazy(() => import('../pages/Penerimaan/FormMaker/detail'));
const PenerimaanFormMakerForm = lazy(() => import('../pages/Penerimaan/FormMaker/form'));

// Pengeluaran
const DaftarPengeluaranIndex = lazy(() => import('../pages/Pengeluaran/DaftarPengeluaran'));
const DaftarPengeluaranCreate = lazy(() => import('../pages/Pengeluaran/DaftarPengeluaran/form'));
const DaftarPengeluaranUpdate = lazy(() => import('../pages/Pengeluaran/DaftarPengeluaran/form'));
const DaftarPengeluaranView = lazy(() => import('../pages/Pengeluaran/DaftarPengeluaran/view'));
const PengeluaranFormMakerList = lazy(() => import('../pages/Pengeluaran/FormMaker'));
const PengeluaranFormMakerDetail = lazy(() => import('../pages/Pengeluaran/FormMaker/detail'));
const PengeluaranFormMakerForm = lazy(() => import('../pages/Pengeluaran/FormMaker/form'));

const TrialJournalIndex = lazy(() => import('../pages/Journal/TrialJournal'));
const JournalReportIndex = lazy(() => import('../pages/Journal/JournalReport'));
const JournalLedgerIndex = lazy(() => import('../pages/Journal/JournalLedger'));

/// Settings
const SettingsPage = lazy(() => import('../pages/settings'));
const ReportPageSettings = lazy(() => import('../pages/settings/report/report'));
const InvoiceExchange = lazy(() => import('../pages/settings/invoiceExchange'));
const AccountPayableList = lazy(() => import('../pages/settings/accountPayableList'));
const AccountPayablePayment = lazy(() => import('../pages/settings/accountPayablePayment'));
const KhanzaSyncPage = lazy(() => import('../pages/settings/khanzaSync'));
/// Settings

// Account Payable
const DaftarAP = lazy(() => import('../pages/AccountPayable/DaftarAP'));
const PembayaranAP = lazy(() => import('../pages/AccountPayable/PembayaranAP'));
const PembayaranAPDetail = lazy(() => import('../pages/AccountPayable/PembayaranAP/detail'));
const PembayaranAPManualDetail = lazy(() => import('../pages/AccountPayable/PembayaranAP/detail-manual'));
const BatchPaymentAP = lazy(() => import('../pages/AccountPayable/PembayaranAP/batch-payment'));
const BatchPaymentDetailAP = lazy(() => import('../pages/AccountPayable/PembayaranAP/batch-detail'));
const BuktiPengeluaranAP = lazy(() => import('../pages/AccountPayable/PembayaranAP/BuktiPengeluaran'));
const FormMakerAPList = lazy(() => import('../pages/AccountPayable/FormMakerAP'));
const FormMakerAPDetail = lazy(() => import('../pages/AccountPayable/FormMakerAP/detail'));
const LaporanAgingAP = lazy(() => import('../pages/AccountPayable/LaporanAging'));
const JasaMedisLegacy = lazy(() => import('../pages/AccountPayable/JasaMedis'));
const JournalKhanza = lazy(() => import('../pages/Journal/JournalKhanza'));
const PurchaseOrderList = lazy(() => import('../pages/AccountPayable/PurchaseOrder'));

// Jasa Medis - New Module
const PostingJasaMedis = lazy(() => import('../pages/JasaMedis/Posting'));
const ManualMedicalFeeForm = lazy(() => import('../pages/JasaMedis/Posting/form'));
const PaymentVoucherList = lazy(() => import('../pages/JasaMedis/PaymentVoucher'));
const PaymentVoucherForm = lazy(() => import('../pages/JasaMedis/PaymentVoucher/form'));
const SettlementList = lazy(() => import('../pages/JasaMedis/Settlement'));
const SettlementForm = lazy(() => import('../pages/JasaMedis/Settlement/form'));

const JurnalUmumIndex = lazy(() => import('../pages/Journal/JurnalUmum'));
const JurnalUmumForm = lazy(() => import('../pages/Journal/JurnalUmum/form'));
const JurnalUmumView = lazy(() => import('../pages/Journal/JurnalUmum/view'));
const JurnalUmumImport = lazy(() => import('../pages/Journal/JurnalUmum/import'));
const UnbalancedJournalIndex = lazy(() => import('../pages/Journal/UnbalancedJournal'));

// Account Receivable
const DaftarARPage = lazy(() => import('../pages/AccountReceivable/DaftarAR'));
const PenerimaanARPage = lazy(() => import('../pages/AccountReceivable/PenerimaanAR'));
const PenerimaanARDetailPage = lazy(() => import('../pages/AccountReceivable/PenerimaanAR/detail'));
const SettlementARPage = lazy(() => import('../pages/AccountReceivable/SettlementAR'));
const SettlementARForm = lazy(() => import('../pages/AccountReceivable/SettlementAR/form'));
const LaporanAgingARPage = lazy(() => import('../pages/AccountReceivable/LaporanAgingAR'));
const CombinedVoucherARPage = lazy(() => import('../pages/AccountReceivable/CombinedVoucher'));
const CombinedVoucherARDetailPage = lazy(() => import('../pages/AccountReceivable/CombinedVoucher/detail'));

// Petty Cash
const PettyCashDashboard = lazy(() => import('../pages/PettyCash/Dashboard'));
const PettyCashUnit = lazy(() => import('../pages/PettyCash/Unit'));
const PettyCashRequest = lazy(() => import('../pages/PettyCash/Request'));
const PettyCashRequestForm = lazy(() => import('../pages/PettyCash/Request/form'));
const PettyCashRequestDetail = lazy(() => import('../pages/PettyCash/Request/detail'));
const PettyCashRequestReceipt = lazy(() => import('../pages/PettyCash/Request/receipt'));
const PettyCashSettlement = lazy(() => import('../pages/PettyCash/Settlement'));
const PettyCashSettlementForm = lazy(() => import('../pages/PettyCash/Settlement/form'));
const PettyCashSettlementDetail = lazy(() => import('../pages/PettyCash/Settlement/detail'));
const PettyCashSettlementReceipt = lazy(() => import('../pages/PettyCash/Settlement/receipt'));
const PettyCashTopUp = lazy(() => import('../pages/PettyCash/TopUp'));
const PettyCashTopUpForm = lazy(() => import('../pages/PettyCash/TopUp/form'));
const PettyCashTopUpDetail = lazy(() => import('../pages/PettyCash/TopUp/detail'));
const PettyCashTopUpReceipt = lazy(() => import('../pages/PettyCash/TopUp/receipt'));
const PettyCashLedger = lazy(() => import('../pages/PettyCash/Ledger'));

// Release Notes
const ReleaseNotesPage = lazy(() => import('../pages/ReleaseNotes'));

const routes = [
  // dashboard
  {
    path: '/',
    element: <Index />,
    layout: 'default',
  },
  // Staff Dashboard - for Staff role
  {
    path: '/staff-dashboard',
    element: <StaffDashboard />,
    layout: 'default',
  },
  // User management - Protected, Admin only
  {
    path: '/users',
    element: (
      <ProtectedRoute pageCode="MasterUser" action="Read">
        <UserIndex />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/users/create',
    element: (
      <ProtectedRoute pageCode="MasterUser" action="Create">
        <UserCreate />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/users/update/:id',
    element: (
      <ProtectedRoute pageCode="MasterUser" action="Update">
        <UserUpdate />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/login',
    element: <LoginBoxed />,
    layout: 'blank',
  },
  {
    path: '/auth/forgot-password',
    element: <ForgotPasswordBoxed />,
    layout: 'blank',
  },
  {
    path: '/auth/reset-password',
    element: <ResetPasswordBoxed />,
    layout: 'blank',
  },
  {
    path: 'accountGroup',
    element: (
      <ProtectedRoute pageCode="MasterAccountGroup" action="Read">
        <AccountGroupIndex />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: 'accountGroup/create',
    element: (
      <ProtectedRoute pageCode="MasterAccountGroup" action="Create">
        <AccountGroupCreate />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: 'accountGroup/update/:id',
    element: (
      <ProtectedRoute pageCode="MasterAccountGroup" action="Update">
        <AccountGroupUpdate />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/bank',
    element: (
      <ProtectedRoute pageCode="MasterBank" action="Read">
        <BankIndex />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/bank/create',
    element: (
      <ProtectedRoute pageCode="MasterBank" action="Create">
        <BankCreate />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/bank/update/:id',
    element: (
      <ProtectedRoute pageCode="MasterBank" action="Update">
        <BankUpdate />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: 'department',
    element: (
      <ProtectedRoute pageCode="MasterDepartment" action="Read">
        <DepartmentIndex />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: 'department/create',
    element: (
      <ProtectedRoute pageCode="MasterDepartment" action="Create">
        <DepartmentCreate />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: 'department/update/:id',
    element: (
      <ProtectedRoute pageCode="MasterDepartment" action="Update">
        <DepartmentUpdate />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: 'bpjs-cost',
    element: (
      <ProtectedRoute pageCode="MasterBpjsCost" action="Read">
        <BpjsCostIndex />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: 'bpjs-cost/create',
    element: (
      <ProtectedRoute pageCode="MasterBpjsCost" action="Create">
        <BpjsCostCreate />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: 'bpjs-cost/update/:id',
    element: (
      <ProtectedRoute pageCode="MasterBpjsCost" action="Update">
        <BpjsCostUpdate />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: 'costCenter',
    element: (
      <ProtectedRoute pageCode="MasterCostCenter" action="Read">
        <CostCenterIndex />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: 'costCenter/create',
    element: (
      <ProtectedRoute pageCode="MasterCostCenter" action="Create">
        <CostCenterCreate />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: 'costCenter/update/:id',
    element: (
      <ProtectedRoute pageCode="MasterCostCenter" action="Update">
        <CostCenterUpdate />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: 'contact',
    element: (
      <ProtectedRoute pageCode="MasterContact" action="Read">
        <ContactIndex />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: 'contact/create',
    element: (
      <ProtectedRoute pageCode="MasterContact" action="Create">
        <ContactCreate />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: 'contact/update/:id',
    element: (
      <ProtectedRoute pageCode="MasterContact" action="Update">
        <ContactUpdate />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: 'accountType',
    element: (
      <ProtectedRoute pageCode="MasterAccountType" action="Read">
        <AccountTypeIndex />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: 'accountType/create',
    element: (
      <ProtectedRoute pageCode="MasterAccountType" action="Create">
        <AccountTypeCreate />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: 'accountType/update/:id',
    element: (
      <ProtectedRoute pageCode="MasterAccountType" action="Update">
        <AccountTypeUpdate />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: 'coa',
    element: (
      <ProtectedRoute pageCode="MasterCOA" action="Read">
        <CoAIndex />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: 'coa/create',
    element: (
      <ProtectedRoute pageCode="MasterCOA" action="Create">
        <CoACreate />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: 'coa/update/:id',
    element: (
      <ProtectedRoute pageCode="MasterCOA" action="Update">
        <CoAUpdate />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: 'coa-mapping',
    element: (
      <ProtectedRoute pageCode="MasterCOA" action="Read">
        <CoaMappingIndex />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: 'coa-mapping/create',
    element: (
      <ProtectedRoute pageCode="MasterCOA" action="Create">
        <CoaMappingForm />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: 'coa-mapping/update/:id',
    element: (
      <ProtectedRoute pageCode="MasterCOA" action="Update">
        <CoaMappingForm />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/report/balance-sheet',
    element: (
      <ProtectedRoute pageCode="Report" action="Read">
        <BalanceSheetIndex />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/report/profit-loss',
    element: (
      <ProtectedRoute pageCode="Report" action="Read">
        <ProfitLossIndex />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/report/cash-flow',
    element: (
      <ProtectedRoute pageCode="Report" action="Read">
        <CashFlowIndex />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/report/trial-balance',
    element: (
      <ProtectedRoute pageCode="Report" action="Read">
        <TrialBalanceIndex />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/report/tutup-buku',
    element: (
      <ProtectedRoute pageCode="TrxJournal" action="Update">
        <TutupBukuIndex />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/report/balance-sheet/create',
    element: (
      <ProtectedRoute pageCode="Report" action="Create">
        <BalanceSheetCreate />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/general-ledger/History',
    element: (
      <ProtectedRoute pageCode="TrxJournal" action="Read">
        <GeneralLedgerHistoryIndex />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/general-ledger/Summary',
    element: (
      <ProtectedRoute pageCode="TrxJournal" action="Read">
        <GeneralLedgerSummaryIndex />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/daftar-buku-kas',
    element: (
      <ProtectedRoute pageCode="TrxBank" action="Read">
        <DaftarBukuKasIndex />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/saldo-awal',
    element: (
      <ProtectedRoute pageCode="TrxBank" action="Read">
        <SaldoAwalIndex />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/daftar-buku-kas/detail/:coa',
    element: (
      <ProtectedRoute pageCode="TrxBank" action="Read">
        <DaftarBukuKasDetail />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/daftar-buku-kas/create',
    element: (
      <ProtectedRoute pageCode="TrxBank" action="Create">
        <DaftarBukuKasCreate />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/daftar-transfer',
    element: (
      <ProtectedRoute pageCode="TrxBank" action="Read">
        <DaftarTransferIndex />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/daftar-transfer/create',
    element: (
      <ProtectedRoute pageCode="TrxBank" action="Create">
        <DaftarTransferCreate />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/daftar-transfer/update/:id',
    element: (
      <ProtectedRoute pageCode="TrxBank" action="Update">
        <DaftarTransferUpdate />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/daftar-transfer/detail/:id',
    element: (
      <ProtectedRoute pageCode="TrxBank" action="Read">
        <DaftarTransferDetail />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/daftar-penerimaan',
    element: (
      <ProtectedRoute pageCode="TrxTransaction" action="Read">
        <DaftarPenerimaanIndex />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/daftar-penerimaan/create',
    element: (
      <ProtectedRoute pageCode="TrxTransaction" action="Create">
        <DaftarPenerimaanCreate />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/daftar-penerimaan/update/:id',
    element: (
      <ProtectedRoute pageCode="TrxTransaction" action="Update">
        <DaftarPenerimaanUpdate />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/daftar-penerimaan/detail/:id',
    element: (
      <ProtectedRoute pageCode="TrxTransaction" action="Read">
        <DaftarPenerimaanView />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/daftar-penerimaan/form-maker',
    element: (
      <ProtectedRoute pageCode="TrxTransaction" action="Read">
        <PenerimaanFormMakerList />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/daftar-penerimaan/form-maker/create',
    element: (
      <ProtectedRoute pageCode="TrxTransaction" action="Create">
        <PenerimaanFormMakerForm />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/daftar-penerimaan/form-maker/:id/edit',
    element: (
      <ProtectedRoute pageCode="TrxTransaction" action="Update">
        <PenerimaanFormMakerForm />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/daftar-penerimaan/form-maker/:id',
    element: (
      <ProtectedRoute pageCode="TrxTransaction" action="Read">
        <PenerimaanFormMakerDetail />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/daftar-pengeluaran',
    element: (
      <ProtectedRoute pageCode="TrxTransaction" action="Read">
        <DaftarPengeluaranIndex />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/daftar-pengeluaran/detail/:id',
    element: (
      <ProtectedRoute pageCode="TrxTransaction" action="Read">
        <DaftarPengeluaranView />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/daftar-pengeluaran/create',
    element: (
      <ProtectedRoute pageCode="TrxTransaction" action="Create">
        <DaftarPengeluaranCreate />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/daftar-pengeluaran/update/:id',
    element: (
      <ProtectedRoute pageCode="TrxTransaction" action="Update">
        <DaftarPengeluaranUpdate />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/daftar-pengeluaran/form-maker',
    element: (
      <ProtectedRoute pageCode="TrxTransaction" action="Read">
        <PengeluaranFormMakerList />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/daftar-pengeluaran/form-maker/create',
    element: (
      <ProtectedRoute pageCode="TrxTransaction" action="Create">
        <PengeluaranFormMakerForm />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/daftar-pengeluaran/form-maker/:id/edit',
    element: (
      <ProtectedRoute pageCode="TrxTransaction" action="Update">
        <PengeluaranFormMakerForm />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/daftar-pengeluaran/form-maker/:id',
    element: (
      <ProtectedRoute pageCode="TrxTransaction" action="Read">
        <PengeluaranFormMakerDetail />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/journal/trial-journal',
    element: (
      <ProtectedRoute pageCode="TrxJournal" action="Read">
        <TrialJournalIndex />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/journal/journal-report',
    element: (
      <ProtectedRoute pageCode="Report" action="Read">
        <JournalReportIndex />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/ledger',
    element: (
      <ProtectedRoute pageCode="Report" action="Read">
        <JournalLedgerIndex />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/settings',
    element: <SettingsPage />,
    layout: 'default',
  },
  {
    path: '/settings/report',
    element: <ReportPageSettings />,
    layout: 'default',
  },
  {
    path: '/settings/khanza-sync',
    element: (
      <ProtectedRoute pageCode="CfgKhanzaSync" action="Read">
        <KhanzaSyncPage />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/ap/invoice-exchange',
    element: <InvoiceExchange />,
    layout: 'default',
  },
  {
    path: '/account-payable/tukar-faktur',
    element: (
      <ProtectedRoute pageCode="TrxInvoiceExchange" action="Read">
        <InvoiceExchange />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/account-payable/daftar-ap',
    element: (
      <ProtectedRoute pageCode="TrxAccountPayable" action="Read">
        <DaftarAP />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/account-payable/pembayaran',
    element: (
      <ProtectedRoute pageCode="TrxAccountPayable" action="Read">
        <PembayaranAP />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/account-payable/pembayaran/batch',
    element: (
      <ProtectedRoute pageCode="TrxAccountPayable" action="Create">
        <BatchPaymentAP />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/account-payable/pembayaran/batch/:batchNumber',
    element: (
      <ProtectedRoute pageCode="TrxAccountPayable" action="Read">
        <BatchPaymentDetailAP />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/account-payable/pembayaran/form-maker',
    element: (
      <ProtectedRoute pageCode="TrxAccountPayable" action="Read">
        <BuktiPengeluaranAP />
      </ProtectedRoute>
    ),
    layout: 'blank',
  },
  {
    // Didaftarkan SEBELUM ':paymentId' supaya segmen 'manual' tidak tertangkap
    // sebagai nilai paymentId oleh route dinamis di bawahnya.
    path: '/account-payable/pembayaran/manual/:paymentId',
    element: (
      <ProtectedRoute pageCode="TrxAccountPayable" action="Read">
        <PembayaranAPManualDetail />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/account-payable/pembayaran/:paymentId',
    element: (
      <ProtectedRoute pageCode="TrxAccountPayable" action="Read">
        <PembayaranAPDetail />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/account-payable/form-maker',
    element: (
      <ProtectedRoute pageCode="TrxAccountPayable" action="Read">
        <FormMakerAPList />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/account-payable/form-maker/:id',
    element: (
      <ProtectedRoute pageCode="TrxAccountPayable" action="Read">
        <FormMakerAPDetail />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/account-payable/laporan-aging',
    element: (
      <ProtectedRoute pageCode="TrxAccountPayable" action="Export">
        <LaporanAgingAP />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/account-payable/purchase-order',
    element: (
      <ProtectedRoute pageCode="TrxPurchaseOrder" action="Read">
        <PurchaseOrderList />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/account-payable/jasa-medis',
    element: (
      <ProtectedRoute pageCode="TrxMedicalFee" action="Read">
        <JasaMedisLegacy />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  // Jasa Medis - New Module Routes
  {
    path: '/jasa-medis/posting/create',
    element: (
      <ProtectedRoute pageCode="TrxMedicalFee" action="Create">
        <ManualMedicalFeeForm />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/jasa-medis/posting',
    element: (
      <ProtectedRoute pageCode="TrxMedicalFee" action="Create">
        <PostingJasaMedis />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/jasa-medis/payment-voucher',
    element: (
      <ProtectedRoute pageCode="TrxMedicalFee" action="Read">
        <PaymentVoucherList />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/jasa-medis/payment-voucher/:doctorCode',
    element: (
      <ProtectedRoute pageCode="TrxMedicalFee" action="Create">
        <PaymentVoucherForm />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/jasa-medis/settlement',
    element: (
      <ProtectedRoute pageCode="TrxMedicalFee" action="Read">
        <SettlementList />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/jasa-medis/settlement/:voucherId',
    element: (
      <ProtectedRoute pageCode="TrxMedicalFee" action="Update">
        <SettlementForm />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/ap/account-payable-list',
    element: <AccountPayableList />,
    layout: 'default',
  },
  {
    path: '/ap/account-payable-payment',
    element: <AccountPayablePayment />,
    layout: 'default',
  },
  {
    path: '/journal/khanza',
    element: <JournalKhanza />,
    layout: 'default',
  },
  // Jurnal Umum
  {
    path: '/jurnal-umum',
    element: (
      <ProtectedRoute pageCode="TrxJournal" action="Read">
        <JurnalUmumIndex />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/jurnal-umum/create',
    element: (
      <ProtectedRoute pageCode="TrxJournal" action="Create">
        <JurnalUmumForm />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/jurnal-umum/update/:id',
    element: (
      <ProtectedRoute pageCode="TrxJournal" action="Update">
        <JurnalUmumForm />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/jurnal-umum/detail/:id',
    element: (
      <ProtectedRoute pageCode="TrxJournal" action="Read">
        <JurnalUmumView />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/jurnal-umum/import',
    element: (
      <ProtectedRoute pageCode="TrxJournal" action="Create">
        <JurnalUmumImport />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/jurnal-tidak-balance',
    element: (
      <ProtectedRoute pageCode="TrxJournal" action="Read">
        <UnbalancedJournalIndex />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  // Account Receivable
  {
    path: '/account-receivable/daftar-ar',
    element: (
      <ProtectedRoute pageCode="TrxAccountReceivable" action="Read">
        <DaftarARPage />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/account-receivable/payment-voucher',
    element: (
      <ProtectedRoute pageCode="TrxAccountReceivable" action="Read">
        <PenerimaanARPage />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/account-receivable/payment-voucher/:payerId',
    element: (
      <ProtectedRoute pageCode="TrxAccountReceivable" action="Read">
        <PenerimaanARDetailPage />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/account-receivable/combined-voucher',
    element: (
      <ProtectedRoute pageCode="TrxAccountReceivable" action="Read">
        <CombinedVoucherARPage />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/account-receivable/combined-voucher/:id',
    element: (
      <ProtectedRoute pageCode="TrxAccountReceivable" action="Read">
        <CombinedVoucherARDetailPage />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/account-receivable/settlement',
    element: (
      <ProtectedRoute pageCode="TrxAccountReceivable" action="Read">
        <SettlementARPage />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/account-receivable/settlement/:voucherNumber',
    element: (
      <ProtectedRoute pageCode="TrxAccountReceivable" action="Update">
        <SettlementARForm />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/account-receivable/laporan-aging',
    element: (
      <ProtectedRoute pageCode="TrxAccountReceivable" action="Export">
        <LaporanAgingARPage />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  // Petty Cash Routes
  {
    path: '/petty-cash',
    element: (
      <ProtectedRoute pageCode="TrxPettyCash" action="Read">
        <PettyCashDashboard />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/petty-cash/dashboard',
    element: (
      <ProtectedRoute pageCode="TrxPettyCash" action="Read">
        <PettyCashDashboard />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/petty-cash/unit',
    element: (
      <ProtectedRoute pageCode="TrxPettyCashUnit" action="Read">
        <PettyCashUnit />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/petty-cash/request',
    element: (
      <ProtectedRoute pageCode="TrxPettyCashRequest" action="Read">
        <PettyCashRequest />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/petty-cash/request/create',
    element: (
      <ProtectedRoute pageCode="TrxPettyCashRequest" action="Create">
        <PettyCashRequestForm />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/petty-cash/request/:id',
    element: (
      <ProtectedRoute pageCode="TrxPettyCashRequest" action="Read">
        <PettyCashRequestDetail />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/petty-cash/request/:id/receipt',
    element: (
      <ProtectedRoute pageCode="TrxPettyCashRequest" action="Read">
        <PettyCashRequestReceipt />
      </ProtectedRoute>
    ),
    layout: 'blank',
  },
  {
    path: '/petty-cash/request/edit/:id',
    element: (
      <ProtectedRoute pageCode="TrxPettyCashRequest" action="Update">
        <PettyCashRequestForm />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/petty-cash/settlement',
    element: (
      <ProtectedRoute pageCode="TrxPettyCashSettlement" action="Read">
        <PettyCashSettlement />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/petty-cash/settlement/create',
    element: (
      <ProtectedRoute pageCode="TrxPettyCashSettlement" action="Create">
        <PettyCashSettlementForm />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/petty-cash/settlement/:id',
    element: (
      <ProtectedRoute pageCode="TrxPettyCashSettlement" action="Read">
        <PettyCashSettlementDetail />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/petty-cash/settlement/:id/receipt',
    element: (
      <ProtectedRoute pageCode="TrxPettyCashSettlement" action="Read">
        <PettyCashSettlementReceipt />
      </ProtectedRoute>
    ),
    layout: 'blank',
  },
  {
    path: '/petty-cash/topup',
    element: (
      <ProtectedRoute pageCode="TrxPettyCashTopUp" action="Read">
        <PettyCashTopUp />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/petty-cash/topup/create',
    element: (
      <ProtectedRoute pageCode="TrxPettyCashTopUp" action="Create">
        <PettyCashTopUpForm />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/petty-cash/topup/:id',
    element: (
      <ProtectedRoute pageCode="TrxPettyCashTopUp" action="Read">
        <PettyCashTopUpDetail />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  {
    path: '/petty-cash/topup/:id/receipt',
    element: (
      <ProtectedRoute pageCode="TrxPettyCashTopUp" action="Read">
        <PettyCashTopUpReceipt />
      </ProtectedRoute>
    ),
    layout: 'blank',
  },
  {
    path: '/petty-cash/ledger',
    element: (
      <ProtectedRoute pageCode="TrxPettyCash" action="Read">
        <PettyCashLedger />
      </ProtectedRoute>
    ),
    layout: 'default',
  },
  // Release Notes
  {
    path: '/release-notes',
    element: <ReleaseNotesPage />,
    layout: 'default',
  },
];

export { routes };
