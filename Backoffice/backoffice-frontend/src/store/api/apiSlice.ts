import { createApi, fetchBaseQuery } from "@reduxjs/toolkit/query/react";
import { getAuthenticated, removeUserInfo } from "@/utils/auth";
export const apiSlice = createApi({
  reducerPath: "api",
  baseQuery: fetchBaseQuery({
    baseUrl: import.meta.env.VITE_HOST,
    prepareHeaders(headers, { getState }) {
      const token = getAuthenticated();
      if (token) {
        headers.set("Authorization", `Bearer ${token}`);
      }
      return headers;
    },
    fetchFn: async (input: RequestInfo, init?: RequestInit) => {
      try {
        const response = await fetch(input, init);
        if (!response.ok) {
          if (response.status === 401) {
            removeUserInfo();
            window.location.href = '/login';
          }
        }
        return response
      } catch (error) {
        throw error;
      }
    },
  }),
  tagTypes: ['GeneralJournals', 'VoucherList', 'SettlementList', 'VoucherDetail', 'SettlementDetail', 'ARList', 'ARVoucherList', 'ARCombinedList', 'ARCombinedDetail', 'ARCombinedEligible', 'APVoucherList', 'APVoucherDetail', 'APVoucherEligible', 'BankVoucherList', 'BankVoucherDetail', 'BankVoucherEligible', 'PurchaseOrder', 'PettyCashUnits', 'PettyCashRequests', 'PettyCashSettlements', 'PettyCashTopUps', 'PettyCashDashboard', 'AccountPayable', 'InvoiceExchange', 'AuditTrail', 'AuditTimeline', 'JournalImportJobs'],
  endpoints: (builder) => ({}),
});
