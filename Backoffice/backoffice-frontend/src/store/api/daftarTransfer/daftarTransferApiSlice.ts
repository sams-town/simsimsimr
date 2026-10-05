import { apiSlice } from '../apiSlice';
import { DepositType } from '@/types/depositType';

export const transactionJournalApi = apiSlice.injectEndpoints({
    endpoints: (builder) => ({
        getTransactionJournal: builder.query({
            query: (params) => `/Transaction/Journal?${new URLSearchParams(params).toString()}`,
        }),

        createTransactionJournal: builder.mutation({
            query: (body: DepositType) => ({
                url: '/Transaction',
                method: 'POST',
                body: body,
            }),
        }),

        deleteTransactionJournal: builder.mutation({
            query: (id: string) => ({
                url: `/Transaction/${id}`,
                method: 'DELETE',
            }),
        }),
        voidTransactionJournal: builder.mutation({
            query: (id: string) => ({
                url: `/Transaction/void/${id}`,
                method: 'DELETE',
            }),
        }),
        updateStatusTransactionJournal: builder.mutation({
            query: (body: any) => ({
                url: '/Transaction/updateStatus',
                method: 'PUT',
                body: body,
            }),
        }),
        // For journal-report and trial-journal pages
        exportTransactionJournal: builder.mutation({
            query: (params) => ({
                url: `/Transaction/journal/export?${new URLSearchParams(params).toString()}`,
                method: 'GET',
                responseHandler: (response) => response.blob(),
            }),
        }),
        // For other transaction list pages (Penerimaan, Pengeluaran, Transfer)
        exportTransactionList: builder.mutation({
            query: (params) => ({
                url: `/Transaction/export?${new URLSearchParams(params).toString()}`,
                method: 'GET',
                responseHandler: (response) => response.blob(),
            }),
        }),
        // For journal-report and trial-journal pages - flat list format
        exportTransactionJournalFlat: builder.mutation({
            query: (params) => ({
                url: `/Transaction/journal/export-flat?${new URLSearchParams(params).toString()}`,
                method: 'GET',
                responseHandler: (response) => response.blob(),
            }),
        }),
        // For other transaction list pages (Penerimaan, Pengeluaran, Transfer) - flat list format
        exportTransactionListFlat: builder.mutation({
            query: (params) => ({
                url: `/Transaction/export-flat?${new URLSearchParams(params).toString()}`,
                method: 'GET',
                responseHandler: (response) => response.blob(),
            }),
        }),
    }),
});

export const {
    useGetTransactionJournalQuery,
    useCreateTransactionJournalMutation,
    useDeleteTransactionJournalMutation,
    useVoidTransactionJournalMutation,
    useUpdateStatusTransactionJournalMutation,
    useExportTransactionJournalMutation,
    useExportTransactionListMutation,
    useExportTransactionJournalFlatMutation,
    useExportTransactionListFlatMutation,
} = transactionJournalApi;

