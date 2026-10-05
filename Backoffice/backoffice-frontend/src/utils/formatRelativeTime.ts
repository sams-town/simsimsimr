import { formatDistanceToNow } from 'date-fns';
import { id } from 'date-fns/locale';

/**
 * "2 jam lalu", "3 hari lalu". Computed against the real now, so it only lines up with
 * FormatDateTime (which renders in the reader's timezone) and not with FormatDate,
 * which pins UTC. Pair it with FormatDateTime, never with FormatDate.
 */
export const FormatRelativeTime = (dateString: any): string => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return '-';

    return formatDistanceToNow(date, { addSuffix: true, locale: id });
};
