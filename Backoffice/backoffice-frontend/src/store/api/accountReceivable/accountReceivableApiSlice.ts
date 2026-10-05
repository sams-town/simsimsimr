import { apiSlice } from '../apiSlice';

export const accountReceivableApi = apiSlice.injectEndpoints({
    endpoints: (builder) => ({
        getAccountReceivablePayerTypes: builder.query({
            query: () => '/AccountReceivable/PayerTypes',
        }),
        getAccountReceivableInsuranceOptions: builder.query({
            query: (keyword) => `/AccountReceivable/InsuranceOptions${keyword ? `?keyword=${keyword}` : ''}`,
        }),
        getAccountReceivableList: builder.query({
            query: (params) => {
                const cleanParams: Record<string, string> = {};
                Object.keys(params).forEach((key) => {
                    const value = params[key];
                    if (value !== null && value !== undefined && value !== '') {
                        cleanParams[key] = String(value);
                    }
                });
                const queryString = new URLSearchParams(cleanParams).toString();
                return `/AccountReceivable/List${queryString ? `?${queryString}` : ''}`;
            },
            providesTags: ['ARList'],
        }),
        getAccountReceivableById: builder.query({
            query: (id) => `/AccountReceivable/${id}`,
        }),
        getAccountReceivableAgingReport: builder.query({
            query: (params) => {
                const cleanParams: Record<string, string> = {};
                Object.keys(params).forEach((key) => {
                    const value = params[key];
                    if (value !== null && value !== undefined && value !== '') {
                        cleanParams[key] = String(value);
                    }
                });
                const queryString = new URLSearchParams(cleanParams).toString();
                return `/AccountReceivable/AgingReport${queryString ? `?${queryString}` : ''}`;
            },
        }),
        getAccountReceivableVoucherList: builder.query({
            query: (params) => {
                const cleanParams: Record<string, string> = {};
                Object.keys(params).forEach((key) => {
                    const value = params[key];
                    if (value !== null && value !== undefined && value !== '') {
                        cleanParams[key] = String(value);
                    }
                });
                const queryString = new URLSearchParams(cleanParams).toString();
                return `/AccountReceivable/VoucherList${queryString ? `?${queryString}` : ''}`;
            },
            providesTags: ['ARVoucherList'],
        }),
        postAccountReceivable: builder.mutation({
            query: (body) => ({
                url: '/AccountReceivable/Post',
                method: 'POST',
                body: body,
            }),
            invalidatesTags: ['ARList', 'ARVoucherList'],
        }),
        unpostAccountReceivable: builder.mutation({
            query: (body) => ({
                url: '/AccountReceivable/Unpost',
                method: 'POST',
                body: body,
            }),
            invalidatesTags: ['ARList', 'ARVoucherList'],
        }),
        finalizeAccountReceivableVoucher: builder.mutation({
            query: (body) => ({
                url: '/AccountReceivable/FinalizeVoucher',
                method: 'POST',
                body: body,
            }),
            invalidatesTags: ['ARVoucherList', 'ARList'],
        }),
        processAccountReceivablePayment: builder.mutation({
            query: (body) => ({
                url: '/AccountReceivable/Payment',
                method: 'POST',
                body: body,
            }),
            invalidatesTags: ['ARVoucherList', 'ARList'],
        }),
        exportAccountReceivable: builder.mutation({
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
                    url: `/AccountReceivable/Export${queryString ? `?${queryString}` : ''}`,
                    method: 'GET',
                    responseHandler: (response) => response.blob(),
                };
            },
            transformResponse: (response: Blob) => response,
        }),
        exportAccountReceivableAging: builder.mutation({
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
                    url: `/AccountReceivable/ExportAging${queryString ? `?${queryString}` : ''}`,
                    method: 'GET',
                    responseHandler: (response) => response.blob(),
                };
            },
            transformResponse: (response: Blob) => response,
        }),
        processSettlement: builder.mutation({
            query: (body) => ({
                url: '/AccountReceivable/Settlement',
                method: 'POST',
                body: body,
            }),
            invalidatesTags: ['ARVoucherList', 'ARList', 'SettlementList'],
        }),
        importAccountReceivable: builder.mutation({
            query: (formData) => ({
                url: '/AccountReceivable/Import',
                method: 'POST',
                body: formData,
            }),
            invalidatesTags: ['ARList'],
        }),
        voidAccountReceivable: builder.mutation({
            query: (body) => ({
                url: '/AccountReceivable/Void',
                method: 'POST',
                body: body,
            }),
            invalidatesTags: ['ARList'],
        }),
        markAsPrintedAccountReceivable: builder.mutation({
            query: (body) => ({
                url: '/AccountReceivable/Print',
                method: 'POST',
                body: body,
            }),
            invalidatesTags: ['ARList'],
        }),
        bulkPostAccountReceivable: builder.mutation({
            query: (formData) => ({
                url: '/AccountReceivable/BulkPost',
                method: 'POST',
                body: formData,
            }),
            invalidatesTags: ['ARList'],
        }),
        bulkRevisionAccountReceivable: builder.mutation({
            query: (formData) => ({
                url: '/AccountReceivable/BulkRevision',
                method: 'POST',
                body: formData,
            }),
            invalidatesTags: ['ARList', 'ARVoucherList'],
        }),
        uploadAdjustment: builder.mutation({
            query: (formData) => ({
                url: '/AccountReceivable/Upload/Adjustment',
                method: 'POST',
                body: formData,
            }),
            invalidatesTags: ['ARList', 'ARVoucherList'],
        }),
        getPayerOptions: builder.query({
            query: (keyword) => `/AccountReceivable/PayerOptions${keyword ? `?keyword=${keyword}` : ''}`,
        }),
        changePayerAccountReceivable: builder.mutation({
            query: (body) => ({
                url: '/AccountReceivable/ChangePayer',
                method: 'POST',
                body: body,
            }),
            invalidatesTags: ['ARList'],
        }),
        adjustAccountReceivable: builder.mutation({
            query: (body) => ({
                url: '/AccountReceivable/Adjust',
                method: 'POST',
                body: body,
            }),
            invalidatesTags: ['ARList', 'ARVoucherList'],
        }),
        addNoteAccountReceivable: builder.mutation({
            query: (body) => ({
                url: '/AccountReceivable/AddNote',
                method: 'POST',
                body: body,
            }),
            invalidatesTags: ['ARList', 'ARVoucherList'],
        }),
        getAccountReceivablePrintConfig: builder.query({
            query: () => '/AccountReceivable/PrintConfig',
        }),
        unfinalizeAccountReceivableVoucher: builder.mutation({
            query: (body) => ({
                url: '/AccountReceivable/UnfinalizeVoucher',
                method: 'POST',
                body: body,
            }),
            invalidatesTags: ['ARList', 'ARVoucherList'],
        }),
    }),
});

export const {
    useGetAccountReceivablePayerTypesQuery,
    useGetAccountReceivableInsuranceOptionsQuery,
    useGetAccountReceivableListQuery,
    useLazyGetAccountReceivableListQuery,
    useGetAccountReceivableByIdQuery,
    useGetAccountReceivableAgingReportQuery,
    useGetAccountReceivableVoucherListQuery,
    usePostAccountReceivableMutation,
    useUnpostAccountReceivableMutation,
    useFinalizeAccountReceivableVoucherMutation,
    useProcessAccountReceivablePaymentMutation,
    useExportAccountReceivableMutation,
    useProcessSettlementMutation,
    useExportAccountReceivableAgingMutation,
    useImportAccountReceivableMutation,
    useVoidAccountReceivableMutation,
    useMarkAsPrintedAccountReceivableMutation,
    useBulkPostAccountReceivableMutation,
    useBulkRevisionAccountReceivableMutation,
    useUploadAdjustmentMutation,
    useGetPayerOptionsQuery,
    useChangePayerAccountReceivableMutation,
    useAdjustAccountReceivableMutation,
    useAddNoteAccountReceivableMutation,
    useGetAccountReceivablePrintConfigQuery,
    useUnfinalizeAccountReceivableVoucherMutation,
} = accountReceivableApi;
