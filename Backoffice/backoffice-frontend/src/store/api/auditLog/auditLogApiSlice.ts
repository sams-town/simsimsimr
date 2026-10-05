import { apiSlice } from '../apiSlice';

const createQueryString = (params: Record<string, unknown>): string => {
    const cleanParams: Record<string, string> = {};
    Object.keys(params).forEach((key) => {
        const value = params[key];
        if (value !== null && value !== undefined && value !== '') {
            cleanParams[key] = String(value);
        }
    });
    const queryString = new URLSearchParams(cleanParams).toString();
    return queryString ? `?${queryString}` : '';
};

export const auditLogApi = apiSlice.injectEndpoints({
    endpoints: (builder) => ({
        // Riwayat drawer: every event for one record.
        getAuditTimeline: builder.query({
            query: ({ module, refId }: { module: string; refId: string | number }) =>
                `/AuditLog/Timeline${createQueryString({ module, refId })}`,
            providesTags: ['AuditTimeline'],
        }),

        // Audit Trail tab listing.
        getAuditLogList: builder.query({
            query: (params: Record<string, unknown>) => `/AuditLog/List${createQueryString(params)}`,
            providesTags: ['AuditTrail'],
        }),

        getAuditLogDetail: builder.query({
            query: (id: number) => `/AuditLog/Detail/${id}`,
        }),

        getAuditLogOptions: builder.query({
            query: () => '/AuditLog/Options',
            providesTags: ['AuditTrail'],
        }),
    }),
});

export const {
    useGetAuditTimelineQuery,
    useGetAuditLogListQuery,
    useGetAuditLogDetailQuery,
    useGetAuditLogOptionsQuery,
} = auditLogApi;
