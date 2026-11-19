import { createApi, fetchBaseQuery } from "@reduxjs/toolkit/query/react";
import { getAuthenticated, removeUserInfo } from "@/utils/auth";

/**
 * Base URL API.
 *
 * VITE_HOST / VITE_API_URL di-inject saat BUILD (vite). Nilai fallback dibuat eksplisit
 * agar bundle produksi tidak pernah menghasilkan baseUrl kosong/relatif — baseUrl relatif
 * akan mengirim POST ke nginx frontend (https://backoffice.rsthb.id/auth/login) yang
 * menjawab 405/404, bukan ke backend.
 */
const trimTrailingSlashes = (value?: unknown): string =>
  typeof value === "string" ? value.trim().replace(/\/+$/, "") : "";

const ENV_BASE_URL = trimTrailingSlashes(
  import.meta.env.VITE_HOST ||
    import.meta.env.VITE_API_URL ||
    import.meta.env.VITE_APP_API_URL,
);

// Fallback terakhir hanya dipakai jika env build tidak terdefinisi sama sekali.
const FALLBACK_BASE_URL = "https://api.rsthb.id";

export const API_BASE_URL = ENV_BASE_URL || FALLBACK_BASE_URL;

// Guard agar konfigurasi salah tidak lolos diam-diam ke produksi.
if (import.meta.env.PROD && !ENV_BASE_URL) {
  // eslint-disable-next-line no-console
  console.warn(
    `[apiSlice] VITE_HOST/VITE_API_URL tidak terdefinisi saat build. Memakai fallback ${FALLBACK_BASE_URL}.`,
  );
}

export const apiSlice = createApi({
  reducerPath: "api",
  baseQuery: fetchBaseQuery({
    baseUrl: API_BASE_URL,
    prepareHeaders(headers) {
      const token = getAuthenticated();
      if (token) {
        headers.set("Authorization", `Bearer ${token}`);
      }
      // Selalu minta JSON dari backend.
      headers.set("Accept", "application/json");
      return headers;
    },
    fetchFn: async (input: RequestInfo, init?: RequestInit) => {
      const response = await fetch(input, init);
      // Jangan redirect di request login: 401 di sana = password salah, bukan sesi habis.
      // Perbandingan dibuat case-insensitive karena endpoint Laravel/.NET memakai "Auth/login".
      const isLogin = String(input).toLowerCase().includes("auth/login");
      if (!response.ok && response.status === 401 && !isLogin) {
        removeUserInfo();
        window.location.href = '/login';
      }
      return response;
    },
  }),
  tagTypes: ['GeneralJournals', 'VoucherList', 'SettlementList', 'VoucherDetail', 'SettlementDetail', 'ARList', 'ARVoucherList', 'ARCombinedList', 'ARCombinedDetail', 'ARCombinedEligible', 'APVoucherList', 'APVoucherDetail', 'APVoucherEligible', 'BankVoucherList', 'BankVoucherDetail', 'BankVoucherEligible', 'PurchaseOrder', 'PettyCashUnits', 'PettyCashRequests', 'PettyCashSettlements', 'PettyCashTopUps', 'PettyCashDashboard', 'AccountPayable', 'InvoiceExchange', 'AuditTrail', 'AuditTimeline', 'JournalImportJobs'],
  endpoints: (builder) => ({}),
});
