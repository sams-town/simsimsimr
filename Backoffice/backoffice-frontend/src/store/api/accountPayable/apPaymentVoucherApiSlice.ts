import { apiSlice } from '../apiSlice';

const buildQuery = (params: Record<string, any>) => {
    const clean: Record<string, string> = {};
    Object.keys(params).forEach((key) => {
        const value = params[key];
        if (value !== null && value !== undefined && value !== '') {
            clean[key] = String(value);
        }
    });
    const qs = new URLSearchParams(clean).toString();
    return qs ? `?${qs}` : '';
};

export const apPaymentVoucherApi = apiSlice.injectEndpoints({
    endpoints: (builder) => ({
        getApVoucherEligibleItems: builder.query({
            query: (params: {
                supplierName?: string;
                supplierType?: string;
                paymentStatus?: string;
                keyword?: string;
                startDate?: string | null;
                endDate?: string | null;
                page?: number;
                pageSize?: number;
                excludeVoucherId?: number | string;
            }) => `/AccountPayable/PaymentVoucher/EligibleItems${buildQuery(params)}`,
            providesTags: ['APVoucherEligible'],
        }),

        getApVoucherList: builder.query({
            query: (params: {
                supplierName?: string;
                keyword?: string;
                startDate?: string | null;
                endDate?: string | null;
                page?: number;
                pageSize?: number;
            }) => `/AccountPayable/PaymentVoucher/List${buildQuery(params)}`,
            providesTags: ['APVoucherList'],
        }),

        getApVoucherDetail: builder.query({
            query: (id) => `/AccountPayable/PaymentVoucher/${id}`,
            providesTags: (result, error, id) => [{ type: 'APVoucherDetail', id }],
        }),

        createApVoucher: builder.mutation({
            query: (body) => ({
                url: '/AccountPayable/PaymentVoucher/Create',
                method: 'POST',
                body,
            }),
            invalidatesTags: ['APVoucherList', 'APVoucherEligible'],
        }),

        updateApVoucher: builder.mutation({
            query: ({ id, voucherDate, paymentType }) => ({
                url: `/AccountPayable/PaymentVoucher/${id}/Update`,
                method: 'POST',
                body: { voucherDate, paymentType },
            }),
            invalidatesTags: (result, error, { id }) => [{ type: 'APVoucherDetail', id }, 'APVoucherList'],
        }),

        addApVoucherItems: builder.mutation({
            query: ({ id, invoiceExchangeIds }) => ({
                url: `/AccountPayable/PaymentVoucher/${id}/AddItems`,
                method: 'POST',
                body: { invoiceExchangeIds },
            }),
            invalidatesTags: (result, error, { id }) => [{ type: 'APVoucherDetail', id }, 'APVoucherList', 'APVoucherEligible'],
        }),

        removeApVoucherItems: builder.mutation({
            query: ({ id, invoiceExchangeIds }) => ({
                url: `/AccountPayable/PaymentVoucher/${id}/RemoveItems`,
                method: 'POST',
                body: { invoiceExchangeIds },
            }),
            invalidatesTags: (result, error, { id }) => [{ type: 'APVoucherDetail', id }, 'APVoucherList', 'APVoucherEligible'],
        }),

        deleteApVoucher: builder.mutation({
            query: (id) => ({
                url: `/AccountPayable/PaymentVoucher/${id}`,
                method: 'DELETE',
            }),
            invalidatesTags: ['APVoucherList', 'APVoucherEligible'],
        }),
    }),
});

export const {
    useGetApVoucherEligibleItemsQuery,
    useGetApVoucherListQuery,
    useGetApVoucherDetailQuery,
    useCreateApVoucherMutation,
    useUpdateApVoucherMutation,
    useAddApVoucherItemsMutation,
    useRemoveApVoucherItemsMutation,
    useDeleteApVoucherMutation,
} = apPaymentVoucherApi;
