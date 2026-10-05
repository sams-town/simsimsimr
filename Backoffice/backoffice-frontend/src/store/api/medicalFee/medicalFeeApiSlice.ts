import { apiSlice } from '../apiSlice';

export const medicalFeeApi = apiSlice.injectEndpoints({
    endpoints: (builder) => ({
        getMedicalFeeList: builder.query({
            query: (params) => {
                const cleanParams: Record<string, string> = {};
                Object.keys(params).forEach((key) => {
                    const value = params[key];
                    if (value !== null && value !== undefined && value !== '') {
                        cleanParams[key] = String(value);
                    }
                });
                const queryString = new URLSearchParams(cleanParams).toString();
                return `/MedicalFee/List${queryString ? `?${queryString}` : ''}`;
            },
        }),
        processMedicalFeePayment: builder.mutation({
            query: (body) => ({
                url: '/MedicalFee/Payment',
                method: 'POST',
                body: body,
            }),
        }),
        exportMedicalFee: builder.mutation({
            query: (params) => {
                const cleanParams: Record<string, string> = {};
                Object.keys(params).forEach((key) => {
                    const value = params[key];
                    if (value !== null && value !== undefined && value !== '') {
                        cleanParams[key] = String(value);
                    }
                });
                const queryString = new URLSearchParams(cleanParams).toString();
                return {
                    url: `/MedicalFee/Export${queryString ? `?${queryString}` : ''}`,
                    method: 'GET',
                    responseHandler: (response) => response.blob(),
                };
            },
            transformResponse: (response: Blob) => response,
        }),
        // ============ Posting ============
        postingBatch: builder.mutation({
            query: (body) => ({
                url: '/MedicalFee/Posting',
                method: 'POST',
                body: body,
            }),
            invalidatesTags: ['VoucherList'],
        }),
        cancelPosting: builder.mutation({
            query: (id) => ({
                url: `/MedicalFee/Posting/Cancel/${id}`,
                method: 'POST',
            }),
            invalidatesTags: ['VoucherList', 'VoucherDetail'],
        }),
        cancelPostingBatch: builder.mutation({
            query: (body) => ({
                url: `/MedicalFee/Posting/CancelBatch`,
                method: 'POST',
                body,
            }),
            invalidatesTags: ['VoucherList', 'VoucherDetail'],
        }),
        // ============ Voucher ============
        getVoucherList: builder.query({
            query: (params) => {
                const cleanParams: Record<string, string> = {};
                Object.keys(params).forEach((key) => {
                    const value = params[key];
                    if (value !== null && value !== undefined && value !== '') {
                        cleanParams[key] = String(value);
                    }
                });
                const queryString = new URLSearchParams(cleanParams).toString();
                return `/MedicalFee/Voucher/List${queryString ? `?${queryString}` : ''}`;
            },
            providesTags: ['VoucherList'],
        }),
        getVoucherDetail: builder.query({
            query: ({ doctorCode, status }) => `/MedicalFee/Voucher/Detail/${doctorCode}${status ? `?status=${status}` : ''}`,
            providesTags: ['VoucherDetail'],
        }),
        generateVoucher: builder.mutation({
            query: (body) => ({
                url: '/MedicalFee/Voucher/Generate',
                method: 'POST',
                body: body,
            }),
            invalidatesTags: ['VoucherList'],
        }),
        uploadBpjsAdjustment: builder.mutation({
            query: (formData) => ({
                url: '/MedicalFee/Upload/BpjsAdjustment',
                method: 'POST',
                body: formData,
            }),
            invalidatesTags: ['VoucherDetail', 'VoucherList'],
        }),
        exportVoucherDetail: builder.mutation({
            query: ({ doctorCode, status }) => ({
                url: `/MedicalFee/VoucherDetail/Export?doctorCode=${doctorCode}&status=${status}`,
                method: 'GET',
                responseHandler: (response) => response.blob(),
            }),
            transformResponse: (response: Blob) => response,
        }),
        // ============ Settlement ============
        getSettlementList: builder.query({
            query: (params) => {
                const cleanParams: Record<string, string> = {};
                Object.keys(params).forEach((key) => {
                    const value = params[key];
                    if (value !== null && value !== undefined && value !== '') {
                        cleanParams[key] = String(value);
                    }
                });
                const queryString = new URLSearchParams(cleanParams).toString();
                return `/MedicalFee/Settlement/List${queryString ? `?${queryString}` : ''}`;
            },
            providesTags: ['SettlementList'],
        }),
        getSettlementDetail: builder.query({
            query: (paymentVoucher) => `/MedicalFee/Settlement/Detail/${encodeURIComponent(paymentVoucher)}`,
            providesTags: ['SettlementDetail'],
        }),
        getSalarySlip: builder.query({
            query: (paymentVoucher) => `/MedicalFee/Settlement/${encodeURIComponent(paymentVoucher)}/Print`,
        }),
        processSettlement: builder.mutation({
            query: (body) => ({
                url: '/MedicalFee/Settlement',
                method: 'POST',
                body: body,
            }),
            invalidatesTags: ['SettlementList', 'SettlementDetail'],
        }),
        // ============ Lookup ============
        getDoctorOptions: builder.query({
            query: (keyword) => `/MedicalFee/Lookup/Doctors${keyword ? `?keyword=${keyword}` : ''}`,
        }),
        getPayerOptions: builder.query({
            query: (keyword) => `/MedicalFee/Lookup/Payers${keyword ? `?keyword=${keyword}` : ''}`,
        }),
        getPatientOptions: builder.query({
            query: (keyword) => `/MedicalFee/Lookup/Patients${keyword ? `?keyword=${keyword}` : ''}`,
        }),
        getVisitNoOptions: builder.query({
            query: (medicalRecordNo) => `/MedicalFee/Lookup/VisitNos?medicalRecordNo=${encodeURIComponent(medicalRecordNo)}`,
        }),
        getSepNoOptions: builder.query({
            query: (medicalRecordNo) => `/MedicalFee/Lookup/SepNos?medicalRecordNo=${encodeURIComponent(medicalRecordNo)}`,
        }),
        getPaymentMethodOptions: builder.query({
            query: (keyword) => `/MedicalFee/Lookup/PaymentMethods${keyword ? `?keyword=${keyword}` : ''}`,
        }),
        // ============ Settlement Export ============
        exportSettlement: builder.mutation({
            query: (params) => {
                const cleanParams: Record<string, string> = {};
                Object.keys(params).forEach((key) => {
                    const value = params[key];
                    if (value !== null && value !== undefined && value !== '') {
                        cleanParams[key] = String(value);
                    }
                });
                const queryString = new URLSearchParams(cleanParams).toString();
                return {
                    url: `/MedicalFee/Settlement/Export${queryString ? `?${queryString}` : ''}`,
                    method: 'GET',
                    responseHandler: (response) => response.blob(),
                };
            },
            transformResponse: (response: Blob) => response,
            invalidatesTags: ['VoucherDetail', 'VoucherList'],
        }),
        // ============ Manual Entry ============
        createManualMedicalFee: builder.mutation({
            query: (body) => ({
                url: '/MedicalFee/Manual',
                method: 'POST',
                body,
            }),
        }),
        uploadBulkManual: builder.mutation({
            query: (formData) => ({
                url: '/MedicalFee/Upload/BulkManual',
                method: 'POST',
                body: formData,
            }),
        }),
        downloadBulkManualTemplate: builder.mutation({
            query: () => ({
                url: '/MedicalFee/Template/BulkManual',
                method: 'GET',
                responseHandler: (response) => response.blob(),
            }),
            transformResponse: (response: Blob) => response,
        }),
        exportSettlementMaker: builder.mutation({
            query: (params) => {
                const cleanParams: Record<string, string> = {};
                Object.keys(params).forEach((key) => {
                    const value = params[key];
                    if (value !== null && value !== undefined && value !== '') {
                        cleanParams[key] = String(value);
                    }
                });
                const queryString = new URLSearchParams(cleanParams).toString();
                return {
                    url: `/MedicalFee/Settlement/ExportMaker${queryString ? `?${queryString}` : ''}`,
                    method: 'GET',
                    responseHandler: (response) => response.blob(),
                };
            },
            transformResponse: (response: Blob) => response,
        }),


    }),
});

export const {
    useGetMedicalFeeListQuery,
    useProcessMedicalFeePaymentMutation,
    useExportMedicalFeeMutation,
    // Posting
    usePostingBatchMutation,
    useCancelPostingMutation,
    useCancelPostingBatchMutation,
    // Voucher
    useGetVoucherListQuery,
    useGetVoucherDetailQuery,
    useGenerateVoucherMutation,
    useUploadBpjsAdjustmentMutation,
    // Settlement
    useGetSettlementListQuery,
    useGetSettlementDetailQuery,
    useGetSalarySlipQuery,
    useLazyGetSalarySlipQuery,
    useProcessSettlementMutation,
    useExportSettlementMutation,
    useExportSettlementMakerMutation,
    useExportVoucherDetailMutation,
    // Lookup
    useGetDoctorOptionsQuery,
    useGetPayerOptionsQuery,
    useGetPatientOptionsQuery,
    useGetVisitNoOptionsQuery,
    useGetSepNoOptionsQuery,
    useGetPaymentMethodOptionsQuery,
    // Manual Entry
    useCreateManualMedicalFeeMutation,
    useUploadBulkManualMutation,
    useDownloadBulkManualTemplateMutation,
} = medicalFeeApi;

