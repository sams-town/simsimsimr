import { useSelector } from 'react-redux';

/**
 * Role ids as seeded in master.mst_role (database/new_roles.sql).
 */
export const ROLE = {
    ADMINISTRATOR: 1,
    MANAGER: 2,
    SUPERVISOR: 3,
    STAFF: 4,
    PETTY_CASH: 5,
} as const;

/** Who may see the Riwayat timeline: above Staff only. */
export const TIMELINE_ROLES: number[] = [ROLE.ADMINISTRATOR, ROLE.MANAGER, ROLE.SUPERVISOR];

/** Siapa yang boleh membatalkan settlement petty cash. Backend menegakkan hal yang sama. */
export const CANCEL_SETTLEMENT_ROLES: number[] = [ROLE.ADMINISTRATOR, ROLE.MANAGER, ROLE.SUPERVISOR];

/** Siapa yang boleh membatalkan pembayaran AP. Lebih ketat dari cancel settlement:
 *  tanpa Supervisor. Backend menegakkan daftar yang sama. */
export const CANCEL_PAYMENT_ROLES: number[] = [ROLE.ADMINISTRATOR, ROLE.MANAGER];

/** Siapa yang boleh membatalkan (unfinalize) voucher AR. Sengaja terpisah dari
 *  CANCEL_PAYMENT_ROLES supaya kebijakan keduanya bisa berbeda tanpa saling menyeret. */
export const CANCEL_AR_VOUCHER_ROLES: number[] = [ROLE.ADMINISTRATOR, ROLE.MANAGER];

/**
 * Role checks for the current user.
 *
 * Allow-lists rather than deny-lists on purpose: `role` is optional on UserProfile, so a
 * missing role must fail closed (hidden) rather than fall through to visible.
 */
export const useRoleAccess = () => {
    const user = useSelector((state: any) => state.auth.user);
    const roleID: number | undefined = user?.role?.roleID;

    const hasRole = (allowed: number[]) => roleID !== undefined && allowed.includes(roleID);

    return {
        user,
        roleID,
        /** Riwayat timeline drawer: Administrator, Manager, Supervisor. */
        canViewTimeline: hasRole(TIMELINE_ROLES),
        /** Audit Trail settings tab and its endpoints: Administrator only. */
        isAdministrator: roleID === ROLE.ADMINISTRATOR,
        /** Batalkan settlement petty cash: Administrator, Manager, Supervisor. */
        canCancelSettlement: hasRole(CANCEL_SETTLEMENT_ROLES),
        /** Batalkan pembayaran AP: Administrator & Manager saja. */
        canCancelPayment: hasRole(CANCEL_PAYMENT_ROLES),
        /** Batalkan voucher AR: Administrator & Manager saja. */
        canCancelArVoucher: hasRole(CANCEL_AR_VOUCHER_ROLES),
        hasRole,
    };
};
