import { apiSlice } from '../apiSlice';

export const agingSchedulePatientApi = apiSlice.injectEndpoints({
    endpoints: (builder) => ({
        getAgingSchedulePatient: builder.query({
            query: (params) => `/Receivable/agingschedule?${new URLSearchParams(params).toString()}`,
        }),
    }),
});

export const {
    useGetAgingSchedulePatientQuery,
} = agingSchedulePatientApi;
