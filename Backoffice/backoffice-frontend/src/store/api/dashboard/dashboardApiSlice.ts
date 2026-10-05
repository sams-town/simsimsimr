import { apiSlice } from '../apiSlice';

export interface MetricData {
    today: number;
    yesterday: number;
    thisMonth: number;
    lastMonth: number;
}

export interface DashboardMetrics {
    revenue: MetricData;
    outpatient: MetricData;
    inpatient: MetricData;
    igd: MetricData;
    polyclinic: MetricData;
    pharmacy: MetricData;
    laboratory: MetricData;
    radiology: MetricData;
    physiotherapy: MetricData;
}

export interface MonthlyTrend {
    months: string[];
    revenue: number[];
    expense: number[];
}

export interface RevenueComposition {
    labels: string[];
    series: number[];
}

export interface TopRevenueAccount {
    id: number;
    coaCode: string;
    accountName: string;
    group: string;
    revenue: number;
}

export interface PaymentMethods {
    labels: string[];
    series: number[];
}

export interface DashboardFullResponse {
    metrics: DashboardMetrics;
    monthlyTrend: MonthlyTrend;
    revenueComposition: RevenueComposition;
    topRevenueAccounts: TopRevenueAccount[];
    paymentMethods: PaymentMethods;
}

export const dashboardApiSlice = apiSlice.injectEndpoints({
    endpoints: (builder) => ({
        getDashboardFull: builder.query<{ data: DashboardFullResponse }, void>({
            query: () => '/Dashboard/full',
        }),
        getDashboardSummary: builder.query<any, { startDate: string; endDate: string }>({
            query: ({ startDate, endDate }) => ({
                url: `/Dashboard/summary`,
                params: { startDate, endDate },
            }),
        }),
    }),
});

export const {
    useGetDashboardFullQuery,
    useGetDashboardSummaryQuery,
} = dashboardApiSlice;