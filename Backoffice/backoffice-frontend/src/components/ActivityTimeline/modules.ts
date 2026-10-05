/**
 * Module names the backend writes into logs.audit_logs.module.
 *
 * These strings must match Models/AuditLogModels.cs `AuditModule` exactly -- the timeline
 * endpoint filters by equality, so a typo silently yields an empty drawer rather than an error.
 */
export const AUDIT_MODULE = {
    MEDICAL_FEE: 'Jasa Medis',
    ACCOUNT_RECEIVABLE: 'Piutang AR',
    ACCOUNT_PAYABLE: 'Hutang AP',
    INVOICE_EXCHANGE: 'Tukar Faktur',
    PETTY_CASH_UNIT: 'Petty Cash Unit',
    PETTY_CASH_REQUEST: 'Petty Cash Request',
    PETTY_CASH_SETTLEMENT: 'Petty Cash Settlement',
    PETTY_CASH_TOPUP: 'Petty Cash TopUp',
    JOURNAL: 'Journal',
    TRANSACTION: 'Transaction',
    OPENING_BALANCE: 'OpeningBalance',
    PERIOD_CLOSING: 'PeriodClosing',
} as const;

export type AuditModule = (typeof AUDIT_MODULE)[keyof typeof AUDIT_MODULE];
