import { MasterBankType } from '@/types/masterBankType';
import { apiSlice } from '../../apiSlice';

export const masterBankApiSlice = apiSlice.injectEndpoints({
  endpoints: (builder) => ({
    getMasterBank: builder.query({
      query: (params) => `/Bank?${new URLSearchParams(params).toString()}`,
    }),
    createMasterBank: builder.mutation({
      query: (data: MasterBankType) => ({
        url: '/Bank',
        method: 'POST',
        body: data,
      }),
    }),
    updateMasterBank: builder.mutation({
      query: (data: MasterBankType) => ({
        url: `/Bank`,
        method: 'PUT',
        body: data,
      }),
    }),
    deleteMasterBank: builder.mutation({
      query: (id: string) => ({
        url: `/Bank/${id}`,
        method: 'DELETE',
      }),
    }),
    getMasterBankDetail: builder.query({
      query: (id) => `/Bank/${id}`,
    }),
  }),
});

export const {
  useGetMasterBankQuery,
  useCreateMasterBankMutation,
  useUpdateMasterBankMutation,
  useDeleteMasterBankMutation,
  useGetMasterBankDetailQuery,
} = masterBankApiSlice;
