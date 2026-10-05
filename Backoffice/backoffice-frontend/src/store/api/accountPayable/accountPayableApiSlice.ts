import { apiSlice } from '../apiSlice';

export const accountPayableApi = apiSlice.injectEndpoints({
    endpoints: (builder) => ({
        getAccountPayableList: builder.query({
            query: (params) => {
                const cleanParams: Record<string, string> = {};
                Object.keys(params).forEach((key) => {
                    const value = params[key];
                    if (value !== null && value !== undefined && value !== '') {
                        cleanParams[key] = String(value);
                    }
                });
                const queryString = new URLSearchParams(cleanParams).toString();
                return `/AccountPayable/List${queryString ? `?${queryString}` : ''}`;
            },
            providesTags: ['AccountPayable'],
        }),
        getAccountPayableAgingReport: builder.query({
            query: (params) => {
                const cleanParams: Record<string, string> = {};
                Object.keys(params).forEach((key) => {
                    const value = params[key];
                    if (value !== null && value !== undefined && value !== '') {
                        cleanParams[key] = String(value);
                    }
                });
                const queryString = new URLSearchParams(cleanParams).toString();
                return `/AccountPayable/AgingReport${queryString ? `?${queryString}` : ''}`;
            },
        }),
        processAccountPayablePayment: builder.mutation({
            query: (body) => ({
                url: '/AccountPayable/Payment',
                method: 'POST',
                body: body,
            }),
            invalidatesTags: ['AccountPayable'],
        }),
        processAccountPayableBatchPayment: builder.mutation({
            query: (body) => ({
                url: '/AccountPayable/BatchPayment',
                method: 'POST',
                body: body,
            }),
            invalidatesTags: ['AccountPayable'],
        }),
        exportAccountPayable: builder.mutation({
            query: (params) => {
                const cleanParams: Record<string, string> = {};
                Object.keys(params).forEach((key) => {
                    const value = params[key];
                    if (value !== null && value !== undefined && value !== '') {
                        cleanParams[key] = String(value);
                    }
                });
                const queryString = new URLSearchParams(cleanParams).toString();
                return {
                    url: `/AccountPayable/Export${queryString ? `?${queryString}` : ''}`,
                    method: 'GET',
                    responseHandler: (response) => response.blob(),
                };
            },
            transformResponse: (response: Blob) => response,
        }),
        cancelInvoicePayment: builder.mutation({
            query: ({ invoiceExchangeId, reason }: { invoiceExchangeId: number | string; reason?: string }) => ({
                url: `/AccountPayable/Payment/${invoiceExchangeId}/Cancel`,
                method: 'POST',
                body: { reason: reason || null },
            }),
            invalidatesTags: ['AccountPayable'],
        }),
        cancelBatchPayment: builder.mutation({
            query: ({ paymentRef, reason }: { paymentRef: string; reason?: string }) => ({
                url: `/AccountPayable/BatchPayment/Cancel?paymentRef=${encodeURIComponent(paymentRef)}`,
                method: 'POST',
                body: { reason: reason || null },
            }),
            invalidatesTags: ['AccountPayable'],
        }),
        exportAccountPayableAging: builder.mutation({
            query: (params) => {
                const cleanParams: Record<string, string> = {};
                Object.keys(params).forEach((key) => {
                    const value = params[key];
                    if (value !== null && value !== undefined && value !== '') {
                        cleanParams[key] = String(value);
                    }
                });
                const queryString = new URLSearchParams(cleanParams).toString();
                return {
                    url: `/AccountPayable/AgingReport/Export${queryString ? `?${queryString}` : ''}`,
                    method: 'GET',
                    responseHandler: (response) => response.blob(),
                };
            },
            transformResponse: (response: Blob) => response,
        }),
        cancelInvoiceExchange: builder.mutation({
            query: (body) => ({
                url: '/AccountPayable/Cancel',
                method: 'PUT',
                body: body,
            }),
        }),
        getAccountPayableById: builder.query({
            query: (id: number) => `/AccountPayable/${id}`,
        }),
        getBatchPaymentDetail: builder.query({
            query: (batchNumber: string) => `/AccountPayable/BatchPayment/${encodeURIComponent(batchNumber)}`,
        }),
        getAccountPayableBatchPpn: builder.query({
            query: (body) => ({
                url: '/AccountPayable/BatchPpn',
                method: 'POST',
                body: body,
            }),
        }),
        exportAccountPayableResi: builder.mutation({
            query: (id: number) => ({
                url: `/AccountPayable/ExportResi/${id}`,
                method: 'GET',
                responseHandler: (response) => response.blob(),
            }),
            transformResponse: (response: Blob) => response,
        }),
    }),
});

export const {
    useGetAccountPayableListQuery,
    useLazyGetAccountPayableListQuery,
    useGetAccountPayableAgingReportQuery,
    useProcessAccountPayablePaymentMutation,
    useProcessAccountPayableBatchPaymentMutation,
    useExportAccountPayableMutation,
    useExportAccountPayableAgingMutation,
    useCancelInvoiceExchangeMutation,
    useCancelInvoicePaymentMutation,
    useCancelBatchPaymentMutation,
    useGetAccountPayableByIdQuery,
    useGetBatchPaymentDetailQuery,
    useExportAccountPayableResiMutation,
    useGetAccountPayableBatchPpnQuery,
} = accountPayableApi;

