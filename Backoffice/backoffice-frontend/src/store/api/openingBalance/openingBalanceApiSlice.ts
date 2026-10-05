import { apiSlice } from '../apiSlice';

// Modul Saldo Awal (Opening Balance). Backend menulis baris 'Opening' ke trx_balance.
export const openingBalanceApi = apiSlice.injectEndpoints({
    endpoints: (builder: any) => ({
        getOpeningBalances: builder.query({
            query: ({ coaCode = '', startDate = '', endDate = '', page = 1, pageSize = 10 }: any) => {
                const params = new URLSearchParams();
                if (coaCode) params.set('coaCode', coaCode);
                if (startDate) params.set('startDate', startDate);
                if (endDate) params.set('endDate', endDate);
                params.set('page', String(page));
                params.set('pageSize', String(pageSize));
                return `/OpeningBalance?${params.toString()}`;
            },
        }),
        getOpeningBalanceDetail: builder.query({
            query: (id: number) => `/OpeningBalance/${id}`,
        }),
        createOpeningBalance: builder.mutation({
            query: (body: any) => ({
                url: '/OpeningBalance',
                method: 'POST',
                body,
            }),
        }),
        updateOpeningBalance: builder.mutation({
            query: (body: any) => ({
                url: '/OpeningBalance',
                method: 'PUT',
                body,
            }),
        }),
        deleteOpeningBalance: builder.mutation({
            query: (id: number) => ({
                url: `/OpeningBalance/${id}`,
                method: 'DELETE',
            }),
        }),
    }),
});

export const {
    useGetOpeningBalancesQuery,
    useGetOpeningBalanceDetailQuery,
    useCreateOpeningBalanceMutation,
    useUpdateOpeningBalanceMutation,
    useDeleteOpeningBalanceMutation,
} = openingBalanceApi;
