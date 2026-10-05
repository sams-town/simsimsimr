import { apiSlice } from '../apiSlice';

export const invoiceExchangeApi = apiSlice.injectEndpoints({
  endpoints: (builder) => ({
    getInvoiceExchangeSupplierOption: builder.query({
      query: (params) => `/InvoiceExchange/Option/Supplier?${new URLSearchParams(params).toString()}`,
    }),
    getInvoiceExchangeTransaction: builder.query({
      query: (params) => {
        // Filter out null, undefined, and empty string values
        const cleanParams: Record<string, string> = {};
        Object.keys(params).forEach((key) => {
          const value = params[key];
          if (value !== null && value !== undefined && value !== '') {
            cleanParams[key] = String(value);
          }
        });
        const queryString = new URLSearchParams(cleanParams).toString();
        return `/InvoiceExchange/Transaction${queryString ? `?${queryString}` : ''}`;
      },
      providesTags: ['InvoiceExchange'],
    }),
    getInvoiceExchangeTransactionDetail: builder.query({
      query: (params) => `/InvoiceExchange/Transaction/Detail?${new URLSearchParams(params).toString()}`,
      providesTags: ['InvoiceExchange'],
    }),
    updateInvoiceExchangeStatus: builder.mutation({
      query: (body) => ({
        url: '/InvoiceExchange/UpdateStatus',
        method: 'PUT',
        body: body,
      }),
      invalidatesTags: ['AccountPayable', 'InvoiceExchange'],
    }),
    createManualInvoiceExchange: builder.mutation({
      query: (body) => ({
        url: '/InvoiceExchange/CreateManual',
        method: 'POST',
        body,
      }),
      invalidatesTags: ['AccountPayable', 'InvoiceExchange'],
    }),
    exportInvoiceExchange: builder.mutation({
      query: (params) => {
        // Filter out null, undefined, and empty string values
        const cleanParams: Record<string, string> = {};
        Object.keys(params).forEach((key) => {
          const value = params[key];
          if (value !== null && value !== undefined && value !== '') {
            cleanParams[key] = String(value);
          }
        });
        const queryString = new URLSearchParams(cleanParams).toString();
        return {
          url: `/InvoiceExchange/export${queryString ? `?${queryString}` : ''}`,
          method: 'GET',
          responseHandler: (response) => response.blob(),
        };
      },
      transformResponse: (response: Blob) => response,
    }),
    getPrintResi: builder.query({
      query: (params) => {
        const cleanParams: Record<string, string> = {};
        Object.keys(params).forEach((key) => {
          const value = params[key];
          if (value !== null && value !== undefined && value !== '') {
            cleanParams[key] = String(value);
          }
        });
        const queryString = new URLSearchParams(cleanParams).toString();
        return `/InvoiceExchange/PrintResi${queryString ? `?${queryString}` : ''}`;
      },
    }),
    getPrintResiById: builder.query({
      query: (invoiceExchangeId: number) => `/InvoiceExchange/PrintResiById?invoiceExchangeId=${invoiceExchangeId}`,
    }),
  }),
});

export const {
  useGetInvoiceExchangeSupplierOptionQuery,
  useGetInvoiceExchangeTransactionQuery,
  useGetInvoiceExchangeTransactionDetailQuery,
  useLazyGetInvoiceExchangeTransactionDetailQuery,
  useUpdateInvoiceExchangeStatusMutation,
  useCreateManualInvoiceExchangeMutation,
  useExportInvoiceExchangeMutation,
  useLazyGetPrintResiQuery,
  useLazyGetPrintResiByIdQuery,
} = invoiceExchangeApi;

