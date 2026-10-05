export const financeDashboardMock = {
  /* ================= KPI ================= */
  revenueThisMonth: 1850000000,
  expenseThisMonth: 1325000000,
  netProfitThisMonth: 525000000,

  /* ================= TOP EXPENSE ================= */
  topExpenses: [
    { name: 'Gaji', amount: 550000000 },
    { name: 'Obat & Alkes', amount: 420000000 },
    { name: 'Listrik & Air', amount: 85000000 },
    { name: 'Maintenance', amount: 120000000 },
  ],

  /* ================= CASHFLOW ================= */
  cashFlowTrend: [
    { month: 'Jan', inflow: 1200000000, outflow: 950000000 },
    { month: 'Feb', inflow: 1350000000, outflow: 1020000000 },
    { month: 'Mar', inflow: 1600000000, outflow: 1150000000 },
    { month: 'Apr', inflow: 1700000000, outflow: 1300000000 },
  ],

  /* ================= DERIVED (HELPER) ================= */
  months: ['Jan', 'Feb', 'Mar', 'Apr'],

  cashflow: {
    inflow: [1200000000, 1350000000, 1600000000, 1700000000],
    outflow: [950000000, 1020000000, 1150000000, 1300000000],
  },

  revenue: [
    1200000000,
    1350000000,
    1600000000,
    1700000000,
  ],

  expense: [
    950000000,
    1020000000,
    1150000000,
    1300000000,
  ],
  /* ================= DETAILED METRICS ================= */
  detailedMetrics: {
    revenue: {
      today: 15_000_000,
      yesterday: 12_500_000,
      thisMonth: 450_000_000,
      lastMonth: 410_000_000
    },
    polyclinic: {
      today: 5_200_000,
      yesterday: 4_800_000,
      thisMonth: 150_000_000,
      lastMonth: 145_000_000
    },
    pharmacy: {
      today: 4_500_000,
      yesterday: 3_900_000,
      thisMonth: 120_000_000,
      lastMonth: 110_000_000
    },
    outpatient: {
      today: 2_800_000,
      yesterday: 3_100_000,
      thisMonth: 90_000_000,
      lastMonth: 95_000_000
    },
    inpatient: {
      today: 8_500_000,
      yesterday: 7_200_000,
      thisMonth: 210_000_000,
      lastMonth: 180_000_000
    },
    igd: {
      today: 3_500_000,
      yesterday: 1_200_000,
      thisMonth: 85_000_000,
      lastMonth: 60_000_000
    },
    radiology: {
      today: 1_800_000,
      yesterday: 2_100_000,
      thisMonth: 55_000_000,
      lastMonth: 58_000_000
    },
    laboratory: {
      today: 2_200_000,
      yesterday: 2_500_000,
      thisMonth: 65_000_000,
      lastMonth: 62_000_000
    },
    physiotherapy: {
      today: 950_000,
      yesterday: 800_000,
      thisMonth: 25_000_000,
      lastMonth: 22_000_000
    }
  },

  /* ================= CHART DATA ================= */
  chartData: {
    revenueComposition: {
      labels: ['Rawat Inap', 'Rawat Jalan', 'IGD', 'Farmasi', 'Lab', 'Radiologi', 'Lainnya'],
      series: [35, 20, 15, 10, 8, 7, 5]
    },
    monthlyTrend: {
      months: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
      revenue: [120, 135, 125, 145, 160, 155, 170, 180, 190, 210, 220, 240], // in Millions
      expense: [100, 110, 105, 115, 120, 118, 125, 130, 140, 145, 150, 160]
    },
    paymentMethods: {
      labels: ['BPJS', 'Asuransi Swasta', 'Tunai / Pribadi', 'Tanggungan Perusahaan'],
      series: [55, 20, 15, 10]
    }
  },

  /* ================= TABLE DATA ================= */
  tableData: {
    topRevenueAccounts: [
      { id: 1, coaCode: '4-100-01', accountName: 'Pendapatan Rawat Jalan - Umum', group: 'Pendapatan Usaha', revenue: 45000000 },
      { id: 2, coaCode: '4-200-03', accountName: 'Pendapatan Rawat Inap - VIP', group: 'Pendapatan Usaha', revenue: 62000000 },
      { id: 3, coaCode: '4-300-01', accountName: 'Pendapatan Tindakan Medis', group: 'Pendapatan Jasa', revenue: 85000000 },
      { id: 4, coaCode: '4-400-02', accountName: 'Pendapatan Farmasi - Obat Resep', group: 'Pendapatan Penjualan', revenue: 38000000 },
      { id: 5, coaCode: '4-500-01', accountName: 'Pendapatan Laboratorium', group: 'Pendapatan Penunjang', revenue: 28000000 },
    ]
  }
};
