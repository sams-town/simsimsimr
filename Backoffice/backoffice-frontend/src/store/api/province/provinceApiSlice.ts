import { ProvincesType } from '@/types/provincesType';
import { ResponseDataType, ResponseType } from '@/types/responseType';
import { apiSlice } from '../apiSlice';

export const provincesApi = apiSlice.injectEndpoints({
  endpoints: (builder) => ({
    getProvinces: builder.query({
      query: (params) => {
        const queryString = new URLSearchParams(params).toString();
        return `/Province?${queryString}`;
      },
    }),
    getDetailsProvinces: builder.query<ResponseType<ProvincesType>, string>({
      query: (id?: string) => `/Province/${id}`,
    }),
    getDetailProvince: builder.mutation({
      query: (id?: any) => {
        return {
          url: `/Province/${id}`,
          method: 'GET',
        };
      },
    }),
    getProvinceOption: builder.query({
      query: (params) => `/Province/option?${new URLSearchParams(params).toString()}`,
    }),
  }),
});

export const {
  useGetProvincesQuery,
  useGetDetailsProvincesQuery,
  useGetDetailProvinceMutation,
  useGetProvinceOptionQuery,
} = provincesApi;
