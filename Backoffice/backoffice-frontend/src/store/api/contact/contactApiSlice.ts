import { ContactType } from '@/types/contactType';
import { OptionType } from '@/types/OptionType';
import { ParamsType } from '@/types/paramsType';
import { ResponseDataType, ResponseType } from '@/types/responseType';
import { apiSlice } from '../apiSlice';

export const receivableApi = apiSlice.injectEndpoints({
  endpoints: (builder) => ({
    getContact: builder.query({
      query: (params) => `/Contact?${new URLSearchParams(params).toString()}`,
    }),
    createContact: builder.mutation({
      query: (data: ContactType) => ({
        url: '/Contact',
        method: 'POST',
        body: data,
      }),
    }),
    updateContact: builder.mutation({
      query: (data: ContactType) => ({
        url: `/Contact`,
        method: 'PUT',
        body: data,
      }),
    }),
    deleteContact: builder.mutation({
      query: (id: number) => ({
        url: `/Contact/${id}`,
        method: 'DELETE',
      }),
    }),
    getContactDetail: builder.query({
      query: (id) => `/Contact/${id}`,
    }),
    getContactCategoryOption: builder.query({
      query: (params) => `/ContactCategory/option?${new URLSearchParams(params).toString()}`,
    }),
    exportContact: builder.mutation({
      query: ({ type, keyword, orderBy, orderType }) => ({
        url: `Contact/export?type=${type}&keyword=${keyword}&orderBy=${orderBy}&orderType=${orderType}`,
        method: 'GET',
        responseHandler: (response) => response.blob(),
      }),
      transformResponse: (response: Blob) => response,
    }),
  }),
});

export const {
  useGetContactQuery,
  useCreateContactMutation,
  useUpdateContactMutation,
  useDeleteContactMutation,
  useGetContactDetailQuery,
  useGetContactCategoryOptionQuery,
  useExportContactMutation,
} = receivableApi;
