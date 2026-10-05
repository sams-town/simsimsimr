import { useTranslation } from 'react-i18next';
import { History, Loader2, User as UserIcon } from 'lucide-react';

import { Sheet, SheetBody, SheetContent, SheetDescription, SheetHeader, SheetTitle } from '@/components/ui/sheet';
import { useGetAuditTimelineQuery } from '@/store/api/auditLog/auditLogApiSlice';
import { useRoleAccess } from '@/hooks/useRoleAccess';
import { FormatDateTime } from '@/utils/formatDate';
import { FormatRelativeTime } from '@/utils/formatRelativeTime';
import { AUDIT_MODULE } from './modules';

export interface TimelineEvent {
    id: number;
    operation?: string;
    userId?: number | null;
    userName?: string | null;
    roleName?: string | null;
    status?: string | null;
    createdAt?: string | null;
    /** Action payload as a JSON string (audit_logs.after_data). */
    detail?: string | null;
}

interface ActivityTimelineProps {
    /** Must match the `module` value the backend logs under -- see AUDIT_MODULE. */
    module: string;
    refId: string | number | null;
    isOpen: boolean;
    onClose: () => void;
    /** Optional record label for the drawer subtitle, e.g. an invoice number. */
    title?: string;
}

/** Operations that undo something -- shown in red so they stand out on the timeline. */
const DESTRUCTIVE_OPERATIONS = ['Batal Posting', 'Batal Tukar Faktur', 'Void', 'Ditolak', 'Dihapus', 'Dinonaktifkan'];

const dotClass = (operation?: string, status?: string | null) => {
    if (status === 'Failed') return 'bg-red-500';
    if (operation && DESTRUCTIVE_OPERATIONS.includes(operation)) return 'bg-red-500';
    if (status === 'Backfill') return 'bg-gray-300';
    return 'bg-primary';
};

/** after_data is arbitrary per action, so render whatever keys it has rather than a fixed shape. */
const parseDetail = (detail?: string | null): Record<string, any> | null => {
    if (!detail) return null;
    try {
        const parsed = typeof detail === 'string' ? JSON.parse(detail) : detail;
        return parsed && typeof parsed === 'object' && !Array.isArray(parsed) ? parsed : null;
    } catch {
        return null;
    }
};

const humanizeKey = (key: string) =>
    key.replace(/([A-Z])/g, ' $1').replace(/^./, (c) => c.toUpperCase()).trim();

const renderValue = (value: any): string => {
    if (value === null || value === undefined || value === '') return '-';
    if (typeof value === 'boolean') return value ? 'Ya' : 'Tidak';
    if (typeof value === 'number') return value.toLocaleString('id-ID');
    if (typeof value === 'object') return JSON.stringify(value);
    // ISO timestamps inside the payload should read like the rest of the drawer.
    if (typeof value === 'string' && /^\d{4}-\d{2}-\d{2}T/.test(value)) return FormatDateTime(value);
    return String(value);
};

const ActivityTimeline = ({ module, refId, isOpen, onClose, title }: ActivityTimelineProps) => {
    const { t } = useTranslation();
    const { canViewTimeline } = useRoleAccess();

    const { data, isLoading, isError } = useGetAuditTimelineQuery(
        { module, refId: refId ?? '' },
        // Load lazily, and never call the endpoint for a role that may not read it.
        { skip: !isOpen || !refId || !canViewTimeline }
    );

    const events: TimelineEvent[] = data?.data ?? [];

    return (
        <Sheet open={isOpen} onOpenChange={(open) => !open && onClose()}>
            <SheetContent side="right">
                <SheetHeader>
                    <SheetTitle className="flex items-center gap-2">
                        <History className="h-4 w-4 text-primary" />
                        {t('common.riwayat.title', 'Riwayat')}
                    </SheetTitle>
                    <SheetDescription>
                        {title ? `${module} · ${title}` : module}
                    </SheetDescription>
                </SheetHeader>

                <SheetBody>
                    {isLoading && (
                        <div className="flex items-center justify-center py-12 text-gray-400">
                            <Loader2 className="h-5 w-5 animate-spin" />
                        </div>
                    )}

                    {!isLoading && isError && (
                        <p className="py-12 text-center text-sm text-red-500">
                            {t('common.riwayat.error', 'Gagal memuat riwayat.')}
                        </p>
                    )}

                    {!isLoading && !isError && events.length === 0 && (
                        <p className="py-12 text-center text-sm text-gray-400">
                            {t('common.riwayat.empty', 'Belum ada riwayat untuk transaksi ini.')}
                        </p>
                    )}

                    {!isLoading && !isError && events.length > 0 && (
                        <ol className="relative space-y-6">
                            {events.map((event, index) => {
                                const detail = parseDetail(event.detail);
                                const isLast = index === events.length - 1;

                                return (
                                    <li key={event.id} className="relative pl-6">
                                        {!isLast && (
                                            <span
                                                className="absolute left-[5px] top-4 h-full w-px bg-gray-200"
                                                aria-hidden="true"
                                            />
                                        )}
                                        <span
                                            className={`absolute left-0 top-1 h-[11px] w-[11px] rounded-full ring-4 ring-white ${dotClass(
                                                event.operation,
                                                event.status
                                            )}`}
                                            aria-hidden="true"
                                        />

                                        <div className="flex items-baseline justify-between gap-2">
                                            <p className="text-sm font-semibold text-gray-900">
                                                {event.operation || '-'}
                                            </p>
                                            {event.status === 'Backfill' && (
                                                <span className="shrink-0 rounded bg-gray-100 px-1.5 py-0.5 text-[10px] font-medium text-gray-500">
                                                    {t('common.riwayat.backfill', 'Data lama')}
                                                </span>
                                            )}
                                        </div>

                                        <p className="mt-0.5 flex items-center gap-1.5 text-xs text-gray-500">
                                            <UserIcon className="h-3 w-3 shrink-0" />
                                            <span className="font-medium text-gray-700">
                                                {event.userName || t('common.riwayat.unknown_user', 'Tidak diketahui')}
                                            </span>
                                            {event.roleName && <span className="text-gray-400">· {event.roleName}</span>}
                                        </p>

                                        <p className="mt-0.5 text-xs text-gray-400">
                                            {FormatDateTime(event.createdAt)} · {FormatRelativeTime(event.createdAt)}
                                        </p>

                                        {detail && Object.keys(detail).length > 0 && (
                                            <dl className="mt-2 space-y-1 rounded-lg border border-gray-100 bg-gray-50/70 p-2.5">
                                                {Object.entries(detail).map(([key, value]) => (
                                                    <div key={key} className="flex justify-between gap-3 text-[11px]">
                                                        <dt className="shrink-0 text-gray-500">{humanizeKey(key)}</dt>
                                                        <dd className="break-all text-right font-medium text-gray-700">
                                                            {renderValue(value)}
                                                        </dd>
                                                    </div>
                                                ))}
                                            </dl>
                                        )}
                                    </li>
                                );
                            })}
                        </ol>
                    )}
                </SheetBody>
            </SheetContent>
        </Sheet>
    );
};

export { AUDIT_MODULE };
export default ActivityTimeline;
