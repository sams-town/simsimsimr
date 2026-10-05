import { apiSlice } from '../apiSlice';

export const bpjsCostApi = apiSlice.injectEndpoints({
    endpoints: (builder) => ({
        getBpjsCosts: builder.query({
            query: ({ keyword = '', orderBy = 'costId', orderType = 'asc', page = 1, pageSize = 10 }) =>
                `/bpjscost?keyword=${keyword}&orderBy=${orderBy}&orderType=${orderType}&page=${page}&pageSize=${pageSize}`,
        }),
        deleteBpjsCost: builder.mutation({
            query: (id) => ({
                url: `/bpjscost/${id}`,
                method: 'DELETE',
            }),
        }),
        createBpjsCost: builder.mutation({
            query: (body) => ({
                url: '/bpjscost',
                method: 'POST',
                body: body,
            }),
        }),
        updateBpjsCost: builder.mutation({
            query: (body) => ({
                url: '/bpjscost',
                method: 'PUT',
                body: body,
            }),
        }),
        getBpjsCostDetail: builder.query({
            query: (id) => `/bpjscost/${id}`,
        }),
        exportBpjsCost: builder.mutation({
            query: ({ type, keyword, orderBy, orderType }) => ({
                url: `bpjscost/export?type=${type}&keyword=${keyword}&orderBy=${orderBy}&orderType=${orderType}`,
                method: 'GET',
                responseHandler: (response) => response.blob(),
            }),
            transformResponse: (response: Blob) => response,
        }),
        getOptionBpjsCostDetail: builder.query({
            query: ({ keyword = '', orderBy = 'costId', orderType = 'asc', page = 1, pageSize = 10 }) =>
                `/bpjscost/option?keyword=${keyword}&orderBy=${orderBy}&orderType=${orderType}&page=${page}&pageSize=${pageSize}`,
        }),
    }),
});

export const {
    useGetBpjsCostsQuery,
    useDeleteBpjsCostMutation,
    useCreateBpjsCostMutation,
    useUpdateBpjsCostMutation,
    useGetBpjsCostDetailQuery,
    useExportBpjsCostMutation,
    useGetOptionBpjsCostDetailQuery,
} = bpjsCostApi;
