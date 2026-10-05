import { OptionType } from '@/types/OptionType';
import { ParamsType } from '@/types/paramsType';
import { ResponseDataType, ResponseType } from '@/types/responseType';
import { apiSlice } from '../apiSlice';

export const cityApi = apiSlice.injectEndpoints({
  endpoints: (builder) => ({
    getCities: builder.query({
      query: (params) => {
        const queryString = new URLSearchParams(params).toString();
        return `City?${queryString}`;
      },
    }),
    getCityOptionMu: builder.mutation<ResponseType<ResponseDataType<OptionType[]>>, ParamsType>({
      query: (params: any) => {
        return {
          url: `/City/option?${new URLSearchParams(params).toString()}`,
          method: 'GET',
        };
      },
    }),
    getDetailsCities: builder.query({
      query: (id?: string) => `/City/option?${id}`,
    }),
    getDetailCity: builder.mutation({
      query: (id?: any) => {
        return {
          url: `/City/${id}`,
          method: 'GET',
        };
      },
    }),
    getCityOption: builder.query({
      query: (params) => `/City/option?${new URLSearchParams(params).toString()}`,
    }),
  }),
});

export const {
  useGetCitiesQuery,
  useGetCityOptionMuMutation,
  useGetDetailsCitiesQuery,
  useGetDetailCityMutation,
  useGetCityOptionQuery,
} = cityApi;
