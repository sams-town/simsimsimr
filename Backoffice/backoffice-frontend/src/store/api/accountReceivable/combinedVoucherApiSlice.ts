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

export const combinedVoucherApi = apiSlice.injectEndpoints({
    endpoints: (builder) => ({
        // Eligible AR items for the "Add patient" popup
        getCombinedEligibleItems: builder.query({
            query: (params) => `/AccountReceivable/Combined/EligibleItems${buildQuery(params)}`,
            providesTags: ['ARCombinedEligible'],
        }),

        // List of combined batches
        getCombinedVoucherList: builder.query({
            query: (params) => `/AccountReceivable/Combined/List${buildQuery(params)}`,
            providesTags: ['ARCombinedList'],
        }),

        // Detail of a single batch
        getCombinedVoucherDetail: builder.query({
            query: (id) => `/AccountReceivable/Combined/${id}`,
            providesTags: (result, error, id) => [{ type: 'ARCombinedDetail', id }],
        }),

        createCombinedVoucher: builder.mutation({
            query: (body) => ({
                url: '/AccountReceivable/Combined/Create',
                method: 'POST',
                body,
            }),
            invalidatesTags: ['ARCombinedList', 'ARCombinedEligible'],
        }),

        addCombinedVoucherItems: builder.mutation({
            query: ({ id, arIds }) => ({
                url: `/AccountReceivable/Combined/${id}/AddItems`,
                method: 'POST',
                body: { arIds },
            }),
            invalidatesTags: (result, error, { id }) => [{ type: 'ARCombinedDetail', id }, 'ARCombinedList', 'ARCombinedEligible'],
        }),

        removeCombinedVoucherItems: builder.mutation({
            query: ({ id, arIds }) => ({
                url: `/AccountReceivable/Combined/${id}/RemoveItems`,
                method: 'POST',
                body: { arIds },
            }),
            invalidatesTags: (result, error, { id }) => [{ type: 'ARCombinedDetail', id }, 'ARCombinedList', 'ARCombinedEligible'],
        }),

        deleteCombinedVoucher: builder.mutation({
            query: (id) => ({
                url: `/AccountReceivable/Combined/${id}`,
                method: 'DELETE',
            }),
            invalidatesTags: ['ARCombinedList', 'ARCombinedEligible'],
        }),
    }),
});

export const {
    useGetCombinedEligibleItemsQuery,
    useLazyGetCombinedEligibleItemsQuery,
    useGetCombinedVoucherListQuery,
    useGetCombinedVoucherDetailQuery,
    useCreateCombinedVoucherMutation,
    useAddCombinedVoucherItemsMutation,
    useRemoveCombinedVoucherItemsMutation,
    useDeleteCombinedVoucherMutation,
} = combinedVoucherApi;
