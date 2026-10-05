import { DepositType, DepositUpdateType } from '@/types/depositType';
import { apiSlice } from '../../apiSlice';

export const depositApi = apiSlice.injectEndpoints({
    endpoints: (builder) => ({
        getDeposits: builder.query({
            query: (params) =>
                `/Bank/deposit?${new URLSearchParams(params).toString()}`,
        }),
        getDepositDetail: builder.query({
            query: (id: string) => `/Bank/deposit/${id}`,
        }),
        createDeposit: builder.mutation({
            query: (data: DepositType) => ({
                url: '/Transaction',
                method: 'POST',
                body: data,
            }),
        }),
        updateDeposit: builder.mutation({
            query: (data: DepositUpdateType) => ({
                url: `/Transaction`,
                method: 'PUT',
                body: data,
            }),
        }),
        deleteDeposit: builder.mutation({
            query: (id: number) => ({
                url: `/Bank/deposit/${id}`,
                method: 'DELETE',
            }),
        }),
    }),
});

export const {
    useGetDepositsQuery,
    useGetDepositDetailQuery,
    useCreateDepositMutation,
    useUpdateDepositMutation,
    useDeleteDepositMutation,
} = depositApi;
