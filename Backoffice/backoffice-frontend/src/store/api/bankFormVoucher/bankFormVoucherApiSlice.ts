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

export const bankFormVoucherApi = apiSlice.injectEndpoints({
    endpoints: (builder) => ({
        getBankVoucherList: builder.query({
            query: (params: {
                voucherType: string;
                keyword?: string;
                startDate?: string | null;
                endDate?: string | null;
                page?: number;
                pageSize?: number;
            }) => `/BankFormVoucher/List${buildQuery(params)}`,
            providesTags: ['BankVoucherList'],
        }),

        getBankVoucherDetail: builder.query({
            query: (id) => `/BankFormVoucher/${id}`,
            providesTags: (result, error, id) => [{ type: 'BankVoucherDetail', id }],
        }),

        createBankVoucher: builder.mutation({
            query: (body) => ({
                url: '/BankFormVoucher/Create',
                method: 'POST',
                body,
            }),
            invalidatesTags: ['BankVoucherList'],
        }),

        updateBankVoucher: builder.mutation({
            query: ({ id, ...body }) => ({
                url: `/BankFormVoucher/${id}/Update`,
                method: 'POST',
                body,
            }),
            invalidatesTags: (result, error, { id }) => [{ type: 'BankVoucherDetail', id }, 'BankVoucherList'],
        }),

        deleteBankVoucher: builder.mutation({
            query: (id) => ({
                url: `/BankFormVoucher/${id}`,
                method: 'DELETE',
            }),
            invalidatesTags: ['BankVoucherList'],
        }),
    }),
});

export const {
    useGetBankVoucherListQuery,
    useGetBankVoucherDetailQuery,
    useCreateBankVoucherMutation,
    useUpdateBankVoucherMutation,
    useDeleteBankVoucherMutation,
} = bankFormVoucherApi;
