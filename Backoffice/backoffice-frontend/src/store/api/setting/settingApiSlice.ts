import { OptionDataUpdateType } from '@/types';
import { apiSlice } from '../apiSlice';

export const settingApi = apiSlice.injectEndpoints({
  endpoints: (builder) => ({
    getSettingOption: builder.query({
      query: (params) => `/Option?${new URLSearchParams(params).toString()}`,
    }),
    getSettingOptions: builder.query({
      query: () => `/Option/CFG_REPORT_BALANCESHEET`,
    }),
    getSettingOptionMu: builder.mutation({
      query: (option?: string) => {
        return {
          url: `/Option/${option}`,
        };
      },
    }),
    getSettingOptionsByType: builder.mutation({
      query: (type: string) => ({
        url: `/Option/type/${type}`,
      }),
    }),
    putSettingOption: builder.mutation({
      query: (data: OptionDataUpdateType) => ({
        url: 'Option',
        method: 'PUT',
        body: data,
      }),
    }),
    putBulkSettingOption: builder.mutation({
      query: (data: OptionDataUpdateType[]) => ({
        url: 'Option/bulk',
        method: 'PUT',
        body: data,
      }),
    }),
    testSmtpEmail: builder.mutation({
      query: (data: any) => ({
        url: 'Option/TestSmtp',
        method: 'POST',
        body: data,
      }),
    }),
  }),
});

export const {
  useGetSettingOptionQuery,
  useGetSettingOptionMuMutation,
  useGetSettingOptionsByTypeMutation,
  usePutSettingOptionMutation,
  usePutBulkSettingOptionMutation,
  useTestSmtpEmailMutation,
} = settingApi;
