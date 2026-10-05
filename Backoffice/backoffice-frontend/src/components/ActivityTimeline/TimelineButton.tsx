import { useState } from 'react';
import { useTranslation } from 'react-i18next';
import Tippy from '@tippyjs/react';
import 'tippy.js/dist/tippy.css';
import { History } from 'lucide-react';

import ActivityTimeline from './index';
import { useRoleAccess } from '@/hooks/useRoleAccess';

interface TimelineButtonProps {
    module: string;
    refId: string | number | null | undefined;
    /** Record label for the drawer subtitle, e.g. an invoice or voucher number. */
    title?: string;
}

/**
 * Riwayat trigger for a table row. Renders nothing for roles below Supervisor, so pages can
 * drop it into an actions column unconditionally.
 *
 * The gate lives here rather than on the route because at least one page
 * (settings/invoiceExchange) is also reachable via an unguarded route (routes.tsx `/ap/invoice-exchange`).
 * The server enforces the same rule independently.
 */
const TimelineButton = ({ module, refId, title }: TimelineButtonProps) => {
    const { t } = useTranslation();
    const { canViewTimeline } = useRoleAccess();
    const [isOpen, setIsOpen] = useState(false);

    if (!canViewTimeline || refId === null || refId === undefined || refId === '') return null;

    return (
        <>
            <Tippy content={t('common.riwayat.title', 'Riwayat')}>
                <button
                    type="button"
                    aria-label={t('common.riwayat.title', 'Riwayat')}
                    onClick={(e) => {
                        // Several of these tables select the record on row click.
                        e.stopPropagation();
                        setIsOpen(true);
                    }}
                    className="rounded p-1 text-gray-400 transition-colors hover:bg-gray-100 hover:text-primary"
                >
                    <History className="h-4 w-4" />
                </button>
            </Tippy>

            {/* Mounted only while open so each row does not hold a Radix portal. */}
            {isOpen && (
                <ActivityTimeline
                    module={module}
                    refId={refId}
                    title={title}
                    isOpen={isOpen}
                    onClose={() => setIsOpen(false)}
                />
            )}
        </>
    );
};

export default TimelineButton;
