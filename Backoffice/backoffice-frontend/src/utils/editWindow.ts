// Form Maker vouchers can only be edited within this many calendar days of creation.
// H (created), H+1 and H+2 are editable; from H+3 onward editing is locked.
export const EDIT_WINDOW_DAYS = 3;

/**
 * Returns true when a voucher created on `createdDate` can no longer be edited
 * (i.e. it is H+3 or older). Compares calendar days, ignoring time of day.
 */
export const isVoucherLocked = (createdDate?: string | Date | null): boolean => {
    if (!createdDate) return false;
    const c = new Date(createdDate);
    if (isNaN(c.getTime())) return false;
    c.setHours(0, 0, 0, 0);
    const now = new Date();
    now.setHours(0, 0, 0, 0);
    const diffDays = Math.floor((now.getTime() - c.getTime()) / 86400000);
    return diffDays >= EDIT_WINDOW_DAYS;
};
