import { apiSlice } from '../apiSlice';

export const departmentApi = apiSlice.injectEndpoints({
    endpoints: (builder) => ({
        getDepartments: builder.query({
            query: ({ keyword = '', orderBy = 'departmentId', orderType = 'asc', page = 1, pageSize = 10 }) =>
                `/department?keyword=${keyword}&orderBy=${orderBy}&orderType=${orderType}&page=${page}&pageSize=${pageSize}`,
        }),
        deleteDepartment: builder.mutation({
            query: (id) => ({
                url: `/department/${id}`,
                method: 'DELETE',
            }),
        }),
        createDepartment: builder.mutation({
            query: (body) => ({
                url: '/department',
                method: 'POST',
                body: body,
            }),
        }),
        updateDepartment: builder.mutation({
            query: (body) => ({
                url: '/department',
                method: 'PUT',
                body: body,
            }),
        }),
        getDepartmentDetail: builder.query({
            query: (id) => `/department/${id}`,
        }),
        exportDepartment: builder.mutation({
            query: ({ type, keyword, orderBy, orderType }) => ({
                url: `department/export?type=${type}&keyword=${keyword}&orderBy=${orderBy}&orderType=${orderType}`,
                method: 'GET',
                responseHandler: (response) => response.blob(),
            }),
            transformResponse: (response: Blob) => response,
        }),
        getOptiondepartmentDetail: builder.query({
            query: ({ keyword = '', orderBy = 'departmentId', orderType = 'asc', page = 1, pageSize = 10 }) =>
                `/department/option?keyword=${keyword}&orderBy=${orderBy}&orderType=${orderType}&page=${page}&pageSize=${pageSize}`,
        
        }),
    }),
});


export const {
    useGetDepartmentsQuery,
    useDeleteDepartmentMutation,
    useCreateDepartmentMutation,
    useUpdateDepartmentMutation,
    useGetDepartmentDetailQuery,
    useExportDepartmentMutation,
    useGetOptiondepartmentDetailQuery,
} = departmentApi;

