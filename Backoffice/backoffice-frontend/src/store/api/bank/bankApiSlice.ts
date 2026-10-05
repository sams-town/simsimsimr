import { BankType } from '@/types/bankType';
import { apiSlice } from '../apiSlice';

interface GetBanksQueryParams {
  orderBy?: string;
  orderType?: string;
  page?: number;
  pageSize?: number;
}

export const bankApi = apiSlice.injectEndpoints({
  endpoints: (builder) => ({
    getBanks: builder.query<{ data: BankType[] }, GetBanksQueryParams>({
      query: ({ orderBy = 'coacode', orderType = 'asc', page = 1, pageSize = 10 } = {}) =>
        `/coa/option?parent=${952}&orderBy=${orderBy}&orderType=${orderType}&page=${page}&pageSize=${pageSize}`,
    }),
    getOptionBank: builder.query({
      query: (params) => `/coa/option?${new URLSearchParams(params).toString()}`,
    }),
    getOptionBankList: builder.query({
      query: (params) => `/Bank/option?${new URLSearchParams(params).toString()}`,
    }),
    getDynamicOptionCOA: builder.query({
      query: (params) => ({
        url: '/COA/option',
        params: params,
      }),
    }),
  }),
});

export const {
  useGetBanksQuery,
  useGetOptionBankQuery,
  useGetOptionBankListQuery,
  useGetDynamicOptionCOAQuery,
} = bankApi;
