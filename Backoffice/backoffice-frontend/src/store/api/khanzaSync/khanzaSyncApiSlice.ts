import type { KhanzaSyncApplyItem, KhanzaSyncModuleCode } from '@/types/khanzaSyncType';
import { apiSlice } from '../apiSlice';

const toQuery = (params: Record<string, any>) =>
  new URLSearchParams(
    Object.entries(params).reduce<Record<string, string>>((acc, [k, v]) => {
      if (v !== undefined && v !== null && v !== '') acc[k] = String(v);
      return acc;
    }, {})
  ).toString();

export const khanzaSyncApiSlice = apiSlice.injectEndpoints({
  endpoints: (builder) => ({
    getKhanzaSyncModules: builder.query({
      query: () => `/KhanzaSync/modules`,
    }),
    getKhanzaSyncRecords: builder.query({
      query: ({ module, ...params }: { module: KhanzaSyncModuleCode } & Record<string, any>) =>
        `/KhanzaSync/${module}/records?${toQuery(params)}`,
    }),
    // POST karena daftar kunci bisa panjang; hasilnya selalu data terbaru, tidak di-cache.
    compareKhanzaSync: builder.mutation({
      query: ({ module, keys }: { module: KhanzaSyncModuleCode; keys: string[] }) => ({
        url: `/KhanzaSync/${module}/compare`,
        method: 'POST',
        body: { keys },
      }),
    }),
    applyKhanzaSync: builder.mutation({
      query: ({ module, items }: { module: KhanzaSyncModuleCode; items: KhanzaSyncApplyItem[] }) => ({
        url: `/KhanzaSync/${module}/apply`,
        method: 'POST',
        body: { items },
      }),
    }),
  }),
});

export const {
  useGetKhanzaSyncModulesQuery,
  useGetKhanzaSyncRecordsQuery,
  useCompareKhanzaSyncMutation,
  useApplyKhanzaSyncMutation,
} = khanzaSyncApiSlice;
