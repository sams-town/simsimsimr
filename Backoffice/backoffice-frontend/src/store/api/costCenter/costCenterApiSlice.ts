import { apiSlice } from '../apiSlice';

export const CostCenterApi = apiSlice.injectEndpoints({
    endpoints: (builder) => ({
        getCostCenters: builder.query({
            query: ({ keyword = '',departmentId = null, orderBy = 'CostCenterId', orderType = 'asc', page = 1, pageSize = 10 }) =>
                `/CostCenter?keyword=${keyword}&departmentId=${departmentId}&orderBy=${orderBy}&orderType=${orderType}&page=${page}&pageSize=${pageSize}`,
        }),
        deleteCostCenter: builder.mutation({
            query: (id) => ({
                url: `/CostCenter/${id}`,
                method: 'DELETE',
            }),
        }),
        createCostCenter: builder.mutation({
            query: (body) => ({
                url: '/CostCenter',
                method: 'POST',
                body: body,
            }),
        }),
        updateCostCenter: builder.mutation({
            query: (body) => ({
                url: '/CostCenter',
                method: 'PUT',
                body: body,
            }),
        }),
        getCostCenterDetail: builder.query({
            query: (id) => `/CostCenter/${id}`,
        }),
        exportCostCenter: builder.mutation({
            query: ({ type, keyword,departmentId, orderBy, orderType }) => ({
                url: `CostCenter/export?type=${type}&keyword=${keyword}&departmentId=${departmentId}&orderBy=${orderBy}&orderType=${orderType}`,
                method: 'GET',
                responseHandler: (response) => response.blob(),
            }),
            transformResponse: (response: Blob) => response,
        }),
        getOptionCostCenterDetail: builder.query({
            query: ({ keyword = '',departmentId = null, orderBy = 'CostCenterId', orderType = 'asc', page = 1, pageSize = 10 }) =>
                `/CostCenter/option?keyword=${keyword}&departmentId=${departmentId}&orderBy=${orderBy}&orderType=${orderType}&page=${page}&pageSize=${pageSize}`,
        
        }),
    }),
});


export const {
    useGetCostCentersQuery,
    useDeleteCostCenterMutation,
    useCreateCostCenterMutation,
    useUpdateCostCenterMutation,
    useGetCostCenterDetailQuery,
    useExportCostCenterMutation,
    useGetOptionCostCenterDetailQuery,
} = CostCenterApi;

