import { GeneralJournalType, GeneralJournalUpdateType } from '@/types/generalJournalType';
import { apiSlice } from '../apiSlice';

export const generalJournalApi = apiSlice.injectEndpoints({
    endpoints: (builder) => ({
        getGeneralJournals: builder.query({
            query: (params) =>
                `/Transaction?transactionType=JournalEntry&${new URLSearchParams(params).toString()}`,
            providesTags: ['GeneralJournals'],
        }),
        getGeneralJournalDetail: builder.query({
            query: (id: string) => `/Transaction/${id}`,
            providesTags: (result, error, id) => [{ type: 'GeneralJournals', id }],
        }),
        createGeneralJournal: builder.mutation({
            query: (data: GeneralJournalType) => ({
                url: '/Transaction',
                method: 'POST',
                body: data,
            }),
            invalidatesTags: ['GeneralJournals'],
        }),
        updateGeneralJournal: builder.mutation({
            query: (data: GeneralJournalUpdateType) => ({
                url: `/Transaction`,
                method: 'PUT',
                body: data,
            }),
            invalidatesTags: ['GeneralJournals'],
        }),
        deleteGeneralJournal: builder.mutation({
            query: (id: string) => ({
                url: `/Transaction/${id}`,
                method: 'DELETE',
            }),
            invalidatesTags: ['GeneralJournals'],
        }),
        voidGeneralJournal: builder.mutation({
            query: (id: string) => ({
                url: `/Transaction/void/${id}`,
                method: 'DELETE',
            }),
            invalidatesTags: ['GeneralJournals'],
        }),
        importGeneralJournal: builder.mutation({
            query: (file: File) => {
                const formData = new FormData();
                formData.append('file', file);
                return {
                    url: '/Journal/import-general-journal',
                    method: 'POST',
                    body: formData,
                };
            },
            invalidatesTags: ['GeneralJournals'],
        }),
        importGeneralJournalTHB: builder.mutation({
            query: (file: File) => {
                const formData = new FormData();
                formData.append('file', file);
                return {
                    url: '/Journal/import-general-journal-thb',
                    method: 'POST',
                    body: formData,
                };
            },
            invalidatesTags: ['GeneralJournals'],
        }),
        exportUnbalancedJournal: builder.mutation({
            query: (params) => ({
                url: `/Journal/unbalanced/export?${new URLSearchParams(params).toString()}`,
                method: 'GET',
                responseHandler: (response) => response.blob(),
            }),
        }),
        getUnbalancedJournals: builder.query({
            query: (params) =>
                `/Journal/unbalanced?${new URLSearchParams(params).toString()}`,
            providesTags: ['GeneralJournals'],
        }),
    }),
});

export const {
    useGetGeneralJournalsQuery,
    useGetGeneralJournalDetailQuery,
    useCreateGeneralJournalMutation,
    useUpdateGeneralJournalMutation,
    useDeleteGeneralJournalMutation,
    useVoidGeneralJournalMutation,
    useImportGeneralJournalMutation,
    useImportGeneralJournalTHBMutation,
    useGetUnbalancedJournalsQuery,
    useExportUnbalancedJournalMutation,
} = generalJournalApi;

