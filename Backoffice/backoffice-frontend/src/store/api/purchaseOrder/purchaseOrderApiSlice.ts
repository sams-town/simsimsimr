import { apiSlice } from "../apiSlice";

const buildQueryString = (params: Record<string, unknown>): string => {
    const query = Object.entries(params)
        .filter(([, value]) => value !== undefined && value !== null && value !== "")
        .map(([key, value]) => `${key}=${encodeURIComponent(String(value))}`)
        .join("&");
    return query ? `?${query}` : "";
};

export const purchaseOrderApiSlice = apiSlice.injectEndpoints({
    endpoints: (builder) => ({
        getPurchaseOrders: builder.query({
            query: ({ search, status, supplierType, startDate, endDate, page, pageSize, markedPaid }) => {
                let queryString = `PurchaseOrder?page=${page}&pageSize=${pageSize}`;
                if (search) queryString += `&search=${search}`;
                if (status) queryString += `&status=${status}`;
                if (supplierType) queryString += `&supplierType=${supplierType}`;
                if (startDate) queryString += `&startDate=${startDate}`;
                if (endDate) queryString += `&endDate=${endDate}`;
                // Arsip PO yang sudah ditandai Paid manual -- satu-satunya cara melihat
                // (dan membatalkan) PO tersebut, karena list normal menyembunyikannya.
                if (markedPaid) queryString += `&markedPaid=true`;
                return queryString;
            },
            providesTags: ["PurchaseOrder"],
        }),
        getPurchaseOrderByNumber: builder.query({
            query: (orderNumber) => `PurchaseOrder/${orderNumber}`,
            providesTags: (result, error, arg) => [{ type: "PurchaseOrder", id: arg }],
        }),
        addDownPayment: builder.mutation({
            query: (data) => ({
                url: "PurchaseOrder/DownPayment",
                method: "POST",
                body: data,
            }),
            invalidatesTags: ["PurchaseOrder"],
        }),
        markPurchaseOrderAsPaid: builder.mutation({
            query: ({ orderNumber, note }) => ({
                url: `PurchaseOrder/${encodeURIComponent(orderNumber)}/MarkAsPaid`,
                method: "POST",
                body: { note },
            }),
            // Menandai PO juga melunasi faktur terkait, jadi cache AP & tukar faktur ikut basi.
            invalidatesTags: ["PurchaseOrder", "AccountPayable", "InvoiceExchange"],
        }),
        unmarkPurchaseOrderAsPaid: builder.mutation({
            query: ({ orderNumber, note }) => ({
                url: `PurchaseOrder/${encodeURIComponent(orderNumber)}/UnmarkAsPaid`,
                method: "POST",
                body: { note },
            }),
            invalidatesTags: ["PurchaseOrder", "AccountPayable", "InvoiceExchange"],
        }),
        exportPurchaseOrders: builder.mutation({
            query: (params) => ({
                url: `PurchaseOrder/Export${buildQueryString(params)}`,
                method: "GET",
                responseHandler: (response) => response.blob(),
            }),
            transformResponse: (response: Blob) => response,
        }),
    }),
});

export const {
    useGetPurchaseOrdersQuery,
    useGetPurchaseOrderByNumberQuery,
    useAddDownPaymentMutation,
    useMarkPurchaseOrderAsPaidMutation,
    useUnmarkPurchaseOrderAsPaidMutation,
    useExportPurchaseOrdersMutation,
} = purchaseOrderApiSlice;
