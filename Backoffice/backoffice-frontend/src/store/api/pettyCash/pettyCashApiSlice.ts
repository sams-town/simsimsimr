import { apiSlice } from '../apiSlice';

// Helper function to create clean query params
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

export const pettyCashApi = apiSlice.injectEndpoints({
    endpoints: (builder) => ({
        // Dashboard
        getPettyCashDashboard: builder.query({
            query: () => '/PettyCash/Dashboard',
            providesTags: ['PettyCashDashboard'],
        }),

        // Unit Management
        getPettyCashUnits: builder.query({
            query: (params) => `/PettyCash/Unit${createQueryString(params)}`,
            providesTags: ['PettyCashUnits'],
        }),
        getPettyCashUnitOptions: builder.query({
            query: () => '/PettyCash/Unit/Options',
            providesTags: ['PettyCashUnits'],
        }),
        getPettyCashMyUnit: builder.query({
            query: () => '/PettyCash/Unit/MyUnit',
            providesTags: ['PettyCashUnits', 'PettyCashRequests', 'PettyCashSettlements', 'PettyCashTopUps'],
        }),
        getPettyCashUnitById: builder.query({
            query: (id) => `/PettyCash/Unit/${id}`,
        }),
        addPettyCashUnit: builder.mutation({
            query: (body) => ({
                url: '/PettyCash/Unit',
                method: 'POST',
                body,
            }),
            invalidatesTags: ['PettyCashUnits', 'PettyCashDashboard'],
        }),
        updatePettyCashUnit: builder.mutation({
            query: (body) => ({
                url: '/PettyCash/Unit',
                method: 'PUT',
                body,
            }),
            invalidatesTags: ['PettyCashUnits', 'PettyCashDashboard'],
        }),
        deletePettyCashUnit: builder.mutation({
            query: (id) => ({
                url: `/PettyCash/Unit/${id}`,
                method: 'DELETE',
            }),
            invalidatesTags: ['PettyCashUnits', 'PettyCashDashboard'],
        }),

        // Request Management
        getPettyCashRequests: builder.query({
            query: (params) => `/PettyCash/Request${createQueryString(params)}`,
            providesTags: ['PettyCashRequests'],
        }),
        getPettyCashRequestById: builder.query({
            query: (id) => `/PettyCash/Request/${id}`,
        }),
        getPettyCashRequestReceipt: builder.query({
            query: (id) => `/PettyCash/Request/${id}/Receipt`,
        }),
        addPettyCashRequest: builder.mutation({
            query: (body) => ({
                url: '/PettyCash/Request',
                method: 'POST',
                body,
            }),
            invalidatesTags: ['PettyCashRequests', 'PettyCashDashboard'],
        }),
        updatePettyCashRequest: builder.mutation({
            query: (body) => ({
                url: '/PettyCash/Request',
                method: 'PUT',
                body,
            }),
            invalidatesTags: ['PettyCashRequests'],
        }),
        deletePettyCashRequest: builder.mutation({
            query: (id) => ({
                url: `/PettyCash/Request/${id}`,
                method: 'DELETE',
            }),
            invalidatesTags: ['PettyCashRequests', 'PettyCashDashboard'],
        }),
        submitPettyCashRequest: builder.mutation({
            query: (id) => ({
                url: `/PettyCash/Request/${id}/Submit`,
                method: 'POST',
            }),
            invalidatesTags: ['PettyCashRequests', 'PettyCashDashboard'],
        }),
        approvePettyCashRequest: builder.mutation({
            query: ({ id, approvedAmount }) => ({
                url: `/PettyCash/Request/${id}/Approve`,
                method: 'POST',
                body: { approvedAmount },
            }),
            invalidatesTags: ['PettyCashRequests', 'PettyCashDashboard'],
        }),
        rejectPettyCashRequest: builder.mutation({
            query: ({ id, reason }) => ({
                url: `/PettyCash/Request/${id}/Reject`,
                method: 'POST',
                body: { reason },
            }),
            invalidatesTags: ['PettyCashRequests', 'PettyCashDashboard'],
        }),
        exportPettyCashRequests: builder.mutation({
            query: (params) => ({
                url: `/PettyCash/Request/Export${createQueryString(params)}`,
                method: 'GET',
                responseHandler: (response) => response.blob(),
            }),
            transformResponse: (response: Blob) => response,
        }),

        // Settlement Management
        getPettyCashSettlements: builder.query({
            query: (params) => `/PettyCash/Settlement${createQueryString(params)}`,
            providesTags: ['PettyCashSettlements'],
        }),
        getPettyCashSettlementById: builder.query({
            query: (id) => `/PettyCash/Settlement/${id}`,
        }),
        createPettyCashSettlement: builder.mutation({
            query: (body) => ({
                url: '/PettyCash/Settlement',
                method: 'POST',
                body,
            }),
            invalidatesTags: ['PettyCashSettlements', 'PettyCashRequests', 'PettyCashDashboard'],
        }),
        postPettyCashSettlement: builder.mutation({
            query: (id) => ({
                url: `/PettyCash/Settlement/${id}/Post`,
                method: 'POST',
            }),
            invalidatesTags: ['PettyCashSettlements', 'PettyCashUnits', 'PettyCashDashboard', 'GeneralJournals'],
        }),
        voidPettyCashSettlement: builder.mutation({
            query: (id) => ({
                url: `/PettyCash/Settlement/${id}/Void`,
                method: 'POST',
            }),
            invalidatesTags: ['PettyCashSettlements', 'PettyCashDashboard'],
        }),
        getPettyCashSettlementHistory: builder.query({
            query: (requestId: number | string) => `/PettyCash/Settlement/History/${requestId}`,
            providesTags: ['PettyCashSettlements'],
        }),
        cancelPettyCashSettlement: builder.mutation({
            query: ({ id, reason }: { id: number | string; reason?: string }) => ({
                url: `/PettyCash/Settlement/${id}/Cancel`,
                method: 'POST',
                body: { reason: reason || null },
            }),
            invalidatesTags: ['PettyCashSettlements', 'PettyCashRequests', 'PettyCashUnits', 'PettyCashDashboard'],
        }),
        exportPettyCashSettlements: builder.mutation({
            query: (params) => ({
                url: `/PettyCash/Settlement/Export${createQueryString(params)}`,
                method: 'GET',
                responseHandler: (response) => response.blob(),
            }),
            transformResponse: (response: Blob) => response,
        }),

        // TopUp Management
        getPettyCashTopUps: builder.query({
            query: (params) => `/PettyCash/TopUp${createQueryString(params)}`,
            providesTags: ['PettyCashTopUps'],
        }),
        getPettyCashTopUpDefaultCOA: builder.query({
            query: () => '/PettyCash/TopUp/DefaultCOA',
        }),
        getPettyCashTopUpById: builder.query({
            query: (id) => `/PettyCash/TopUp/${id}`,
        }),
        getPettyCashTopUpReceipt: builder.query({
            query: (id) => `/PettyCash/TopUp/${id}/Receipt`,
        }),
        addPettyCashTopUp: builder.mutation({
            query: (body) => ({
                url: '/PettyCash/TopUp',
                method: 'POST',
                body,
            }),
            invalidatesTags: ['PettyCashTopUps', 'PettyCashDashboard'],
        }),
        approvePettyCashTopUp: builder.mutation({
            query: ({ id, approvedAmount, notes }) => ({
                url: `/PettyCash/TopUp/${id}/Approve`,
                method: 'POST',
                body: { approvedAmount, notes },
            }),
            invalidatesTags: ['PettyCashTopUps', 'PettyCashUnits', 'PettyCashDashboard', 'GeneralJournals'],
        }),
        rejectPettyCashTopUp: builder.mutation({
            query: ({ id, reason }) => ({
                url: `/PettyCash/TopUp/${id}/Reject`,
                method: 'POST',
                body: { reason },
            }),
            invalidatesTags: ['PettyCashTopUps', 'PettyCashDashboard'],
        }),
        voidPettyCashTopUp: builder.mutation({
            query: (id) => ({
                url: `/PettyCash/TopUp/${id}/Void`,
                method: 'POST',
            }),
            invalidatesTags: ['PettyCashTopUps', 'PettyCashDashboard'],
        }),
        exportPettyCashTopUps: builder.mutation({
            query: (params) => ({
                url: `/PettyCash/TopUp/Export${createQueryString(params)}`,
                method: 'GET',
                responseHandler: (response) => response.blob(),
            }),
            transformResponse: (response: Blob) => response,
        }),

        // Ledger
        getPettyCashLedger: builder.query({
            query: ({ unitId, ...params }) => `/PettyCash/Ledger/${unitId}${createQueryString(params)}`,
        }),
        exportPettyCashLedger: builder.mutation({
            query: ({ unitId, ...params }) => ({
                url: `/PettyCash/Ledger/${unitId}/Export${createQueryString(params)}`,
                method: 'GET',
                responseHandler: (response) => response.blob(),
            }),
            transformResponse: (response: Blob) => response,
        }),
    }),
});

export const {
    // Dashboard
    useGetPettyCashDashboardQuery,

    // Units
    useGetPettyCashUnitsQuery,
    useGetPettyCashUnitOptionsQuery,
    useGetPettyCashMyUnitQuery,
    useGetPettyCashUnitByIdQuery,
    useAddPettyCashUnitMutation,
    useUpdatePettyCashUnitMutation,
    useDeletePettyCashUnitMutation,

    // Requests
    useGetPettyCashRequestsQuery,
    useGetPettyCashRequestByIdQuery,
    useGetPettyCashRequestReceiptQuery,
    useAddPettyCashRequestMutation,
    useUpdatePettyCashRequestMutation,
    useDeletePettyCashRequestMutation,
    useSubmitPettyCashRequestMutation,
    useApprovePettyCashRequestMutation,
    useRejectPettyCashRequestMutation,
    useExportPettyCashRequestsMutation,

    // Settlements
    useGetPettyCashSettlementsQuery,
    useGetPettyCashSettlementByIdQuery,
    useCreatePettyCashSettlementMutation,
    usePostPettyCashSettlementMutation,
    useVoidPettyCashSettlementMutation,
    useCancelPettyCashSettlementMutation,
    useGetPettyCashSettlementHistoryQuery,
    useExportPettyCashSettlementsMutation,

    // TopUps
    useGetPettyCashTopUpsQuery,
    useGetPettyCashTopUpDefaultCOAQuery,
    useGetPettyCashTopUpByIdQuery,
    useGetPettyCashTopUpReceiptQuery,
    useAddPettyCashTopUpMutation,
    useApprovePettyCashTopUpMutation,
    useRejectPettyCashTopUpMutation,
    useVoidPettyCashTopUpMutation,
    useExportPettyCashTopUpsMutation,

    // Ledger
    useGetPettyCashLedgerQuery,
    useExportPettyCashLedgerMutation,
} = pettyCashApi;
