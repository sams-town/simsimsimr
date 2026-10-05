import { journalType, usersType } from '@/types';
import { apiSlice } from '../apiSlice';
import { khanzaType } from '@/types/khanzaType';
import { use } from 'i18next';

export const journalRefApi = apiSlice.injectEndpoints({
  endpoints: (builder) => ({
    getJournal: builder.query({
      query: (params) => `/Journal?${new URLSearchParams(params).toString()}`,
    }),
    getJournalLedgers: builder.query({
      query: (params) => `/Journal/ledgers?${new URLSearchParams(params).toString()}`,
    }),
    getJournalLedgerV2: builder.mutation({
      query: (params) => `/Journal/ledgers?${new URLSearchParams(params).toString()}`,
    }),
    getJournalSheet: builder.query({
      query: (params) => `/Journal/balance-sheet?${new URLSearchParams(params).toString()}`,
    }),
    getProfitLossSheet: builder.query({
      query: (params) => `/Journal/profit-loss?${new URLSearchParams(params).toString()}`,
    }),
    getCashFlowSheet: builder.query({
      query: (params) => `/Journal/cash-flow?${new URLSearchParams(params).toString()}`,
    }),
    getLedgerSummary: builder.query({
      query: (params) => `/Journal/summary?${new URLSearchParams(params).toString()}`,
    }),


    deleteJournal: builder.mutation({
      query: (id?: number) => {
        return {
          url: `/Transaction/${id}`,
          method: 'DELETE',
        };
      },
    }),
    postJournal: builder.mutation({
      query: (body: journalType) => {
        return {
          url: '/Transaction',
          method: 'POST',
          body: body,
        };
      },
    }),
    postJournalKhanza: builder.mutation({
      query: (body: khanzaType) => {
        return {
          url: '/Journal/Khanza',
          method: 'POST',
          body: body,
        };
      },
    }),
    updateJournal: builder.mutation({
      query: (body: usersType) => {
        return {
          url: '/Journal',
          method: 'PUT',
          body: body,
        };
      },
    }),
    getDetailJournal: builder.query({
      query: (id?: string) => `/Transaction/${id}`,
    }),
    exportJournalHistory: builder.mutation({
      query: (params) => ({
        url: `/Journal/history/export?${new URLSearchParams(params).toString()}`,
        method: 'GET',
        responseHandler: (response) => response.blob(),
      }),
    }),
    exportLedgerSummary: builder.mutation({
      query: (params) => ({
        url: `/Journal/summary/export?${new URLSearchParams(params).toString()}`,
        method: 'GET',
        responseHandler: (response) => response.blob(),
      }),
    }),
    exportBalanceSheet: builder.mutation({
      query: (params) => ({
        url: `/Journal/balance-sheet/export?${new URLSearchParams(params).toString()}`,
        method: 'GET',
        responseHandler: (response) => response.blob(),
      }),
    }),
    exportProfitLoss: builder.mutation({
      query: (params) => ({
        url: `/Journal/profit-loss/export?${new URLSearchParams(params).toString()}`,
        method: 'GET',
        responseHandler: (response) => response.blob(),
      }),
    }),
    exportCashFlow: builder.mutation({
      query: (params) => ({
        url: `/Journal/cash-flow/export?${new URLSearchParams(params).toString()}`,
        method: 'GET',
        responseHandler: (response) => response.blob(),
      }),
    }),
  }),


});

export const {
  useGetJournalQuery,
  useGetJournalLedgersQuery,
  useGetJournalSheetQuery,
  useDeleteJournalMutation,
  usePostJournalMutation,
  useUpdateJournalMutation,
  useGetDetailJournalQuery,
  useGetProfitLossSheetQuery,
  useGetCashFlowSheetQuery,
  usePostJournalKhanzaMutation,
  useGetJournalLedgerV2Mutation,
  useGetLedgerSummaryQuery,
  useExportJournalHistoryMutation,
  useExportLedgerSummaryMutation,
  useExportBalanceSheetMutation,
  useExportProfitLossMutation,
  useExportCashFlowMutation,
} = journalRefApi;
