export function FormatDate(dateString: any) {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        timeZone: 'UTC',
    });
}
export function FormatDateShort(dateString: any) {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        timeZone: 'UTC',
    });
}

export const formatDate = (dateString: string) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        timeZone: 'UTC',
    }).format(date);
};

export const formatDateForAPI = (date: Date | string | null | undefined) => {
    if (!date) return '';
    const d = new Date(date);
    if (isNaN(d.getTime())) return '';
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
};

/**
 * Date + time, in the reader's own timezone.
 *
 * Note this deliberately differs from FormatDate/FormatDateShort above, which pin
 * timeZone: 'UTC' and so render a UTC wall clock. That is harmless for a date-only
 * column but wrong for a timestamp: an action taken 17:14 WIB would read "10:14".
 * Audit timestamps must match what the reader's own clock said, and must agree with
 * FormatRelativeTime, which is always computed against the real now.
 */
export const FormatDateTime = (dateString: any) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return '-';
    return date.toLocaleString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

/** Time of day only, e.g. "17:14". Same timezone rule as FormatDateTime. */
export const FormatTime = (dateString: any) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return '-';
    return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
};
