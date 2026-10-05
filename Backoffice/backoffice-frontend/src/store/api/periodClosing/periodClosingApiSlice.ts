import { apiSlice } from '../apiSlice';

// Modul Tutup Buku (Period Closing).
export const periodClosingApi = apiSlice.injectEndpoints({
    endpoints: (builder: any) => ({
        getPeriodClosings: builder.query({
            query: ({ periodType = '', year = '', status = '', page = 1, pageSize = 10 }: any) => {
                const params = new URLSearchParams();
                if (periodType) params.set('periodType', periodType);
                if (year) params.set('year', String(year));
                if (status) params.set('status', status);
                params.set('page', String(page));
                params.set('pageSize', String(pageSize));
                return `/PeriodClosing?${params.toString()}`;
            },
        }),
        getPeriodClosingStatus: builder.query({
            query: (date: string) => `/PeriodClosing/Status?date=${date}`,
        }),
        closePeriod: builder.mutation({
            query: (body: any) => ({
                url: '/PeriodClosing/Close',
                method: 'POST',
                body,
            }),
        }),
        reopenPeriod: builder.mutation({
            query: (body: any) => ({
                url: '/PeriodClosing/Reopen',
                method: 'POST',
                body,
            }),
        }),
    }),
});

export const {
    useGetPeriodClosingsQuery,
    useGetPeriodClosingStatusQuery,
    useClosePeriodMutation,
    useReopenPeriodMutation,
} = periodClosingApi;
