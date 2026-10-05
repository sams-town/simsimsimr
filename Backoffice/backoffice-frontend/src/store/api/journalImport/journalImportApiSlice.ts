import { apiSlice } from '../apiSlice';

const createQueryString = (params: Record<string, unknown>): string => {
    const cleanParams: Record<string, string> = {};
    Object.keys(params).forEach((key) => {
        const value = params[key];
        if (value !== null && value !== undefined && value !== '') {
            cleanParams[key] = String(value);
        }
    });
    const queryString = new URLSearchParams(cleanParams).toString();
    return queryString ? `?${queryString}` : '';
};

export const journalImportApi = apiSlice.injectEndpoints({
    endpoints: (builder) => ({
        // Queue one or more files. The server saves them and returns the queued rows.
        enqueueJournalImport: builder.mutation({
            query: ({ files, importType }: { files: File[]; importType: string }) => {
                const formData = new FormData();
                files.forEach((file) => formData.append('files', file));
                formData.append('importType', importType);
                return {
                    url: '/JournalImport',
                    method: 'POST',
                    body: formData,
                };
            },
            invalidatesTags: ['JournalImportJobs'],
        }),

        getJournalImportJobs: builder.query({
            query: (params: Record<string, unknown>) => `/JournalImport${createQueryString(params)}`,
            providesTags: ['JournalImportJobs'],
        }),

        // Cheap poll target: tells us whether anything is still pending/running.
        getJournalImportQueueStatus: builder.query({
            query: () => '/JournalImport/status',
            providesTags: ['JournalImportJobs'],
        }),

        getJournalImportJobDetail: builder.query({
            query: (id: number) => `/JournalImport/${id}`,
        }),

        cancelJournalImportJob: builder.mutation({
            query: (id: number) => ({
                url: `/JournalImport/${id}`,
                method: 'DELETE',
            }),
            invalidatesTags: ['JournalImportJobs'],
        }),

        // Import files live outside wwwroot, so the original file can only be fetched through
        // the API. responseHandler returns the raw blob rather than trying to parse JSON.
        downloadJournalImportFile: builder.mutation({
            query: (id: number) => ({
                url: `/JournalImport/${id}/file`,
                method: 'GET',
                responseHandler: (response: Response) => response.blob(),
            }),
        }),
    }),
});

export const {
    useEnqueueJournalImportMutation,
    useGetJournalImportJobsQuery,
    useGetJournalImportQueueStatusQuery,
    useGetJournalImportJobDetailQuery,
    useCancelJournalImportJobMutation,
    useDownloadJournalImportFileMutation,
} = journalImportApi;
