import { apiSlice } from '../apiSlice';

export const coaMappingApi = apiSlice.injectEndpoints({
    endpoints: (builder: any) => ({
        getCoaMappings: builder.query({
            query: ({ orderBy = 'Id', orderType = 'asc', page = 1, pageSize = 10, keyword = '' }: any) =>
                `/CoaMapping?orderBy=${orderBy}&orderType=${orderType}&page=${page}&pageSize=${pageSize}&keyword=${keyword}`,
        }),
        getCoaMappingDetail: builder.query({
            query: (id: number) => `/CoaMapping/${id}`,
        }),
        createCoaMapping: builder.mutation({
            query: (body: any) => ({
                url: '/CoaMapping',
                method: 'POST',
                body: body,
            }),
        }),
        updateCoaMapping: builder.mutation({
            query: (body: any) => ({
                url: '/CoaMapping',
                method: 'PUT',
                body: body,
            }),
        }),
        deleteCoaMapping: builder.mutation({
            query: (id: number) => ({
                url: `/CoaMapping/${id}`,
                method: 'DELETE',
            }),
        }),
    }),
});

export const {
    useGetCoaMappingsQuery,
    useGetCoaMappingDetailQuery,
    useCreateCoaMappingMutation,
    useUpdateCoaMappingMutation,
    useDeleteCoaMappingMutation,
} = coaMappingApi;
