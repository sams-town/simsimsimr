import { CreateMigrationJobPayload } from '@/types/migrationJobType';
import { apiSlice } from '../apiSlice';

export const migrationJobApiSlice = apiSlice.injectEndpoints({
  endpoints: (builder) => ({
    getMigrationJobs: builder.query({
      query: (params: Record<string, any>) =>
        `/MigrationJob?${new URLSearchParams(
          Object.entries(params).reduce<Record<string, string>>((acc, [k, v]) => {
            if (v !== undefined && v !== null && v !== '') acc[k] = String(v);
            return acc;
          }, {})
        ).toString()}`,
    }),
    getMigrationJobDetail: builder.query({
      query: (id: number) => `/MigrationJob/${id}`,
    }),
    getMigrationJobStatus: builder.query({
      query: () => `/MigrationJob/status`,
    }),
    getMigrationJobScripts: builder.query({
      query: () => `/MigrationJob/scripts`,
    }),
    createMigrationJob: builder.mutation({
      query: (data: CreateMigrationJobPayload) => ({
        url: '/MigrationJob',
        method: 'POST',
        body: data,
      }),
    }),
  }),
});

export const {
  useGetMigrationJobsQuery,
  useGetMigrationJobDetailQuery,
  useGetMigrationJobStatusQuery,
  useGetMigrationJobScriptsQuery,
  useCreateMigrationJobMutation,
} = migrationJobApiSlice;
