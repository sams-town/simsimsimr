export interface ReleaseNote {
    version: string;
    date: string;
    type: 'major' | 'minor' | 'patch';
    summary?: string;
    features?: string[];
    bugfixes?: string[];
    improvements?: string[];
}

export const APP_VERSION = '1.0.0';

export const releaseNotes: ReleaseNote[] = [
    {
        version: '1.0.0',
        date: '2026-02-27',
        type: 'major',
        summary: 'Rilis awal Backoffice Keuangan — sistem terintegrasi untuk mengelola seluruh proses keuangan rumah sakit.',
        features: [
            // Dashboard
            'Dashboard eksekutif dengan ringkasan keuangan, grafik, dan indikator kinerja',
            'Dashboard staff dengan tampilan ringkas sesuai akses role',

            // Master Data
            'Master Department — kelola data departemen/unit',
            'Master Bank — kelola data rekening bank',
            'Master Cost Center — kelola pusat biaya',
            'Master Contact — kelola data supplier, asuransi, dan kontak lainnya',
            'Master Account Group — kelola grup akun keuangan',
            'Master Account Type — kelola tipe akun',
            'Master COA (Chart of Accounts) — kelola daftar akun keuangan',
            'Master COA Mapping — pemetaan akun dari sistem Khanza ke backoffice',

            // Kas & Bank
            'Daftar Buku Kas — monitoring saldo dan mutasi per rekening',
            'Transfer Antar Bank — pencatatan transfer antar rekening (CRUD + Detail)',

            // Penerimaan & Pengeluaran
            'Daftar Penerimaan — pencatatan penerimaan uang masuk (CRUD + Detail)',
            'Daftar Pengeluaran — pencatatan pengeluaran uang keluar (CRUD + Detail)',

            // Jurnal
            'Jurnal Umum — entri jurnal manual (CRUD + Detail)',
            'Trial Journal — verifikasi dan review jurnal sebelum posting',
            'Journal Report — laporan jurnal dengan filter tanggal',
            'Journal Ledger — buku besar per akun',
            'Journal Khanza — import jurnal dari sistem Khanza',
            //'Deteksi Jurnal Tidak Seimbang — identifikasi jurnal yang debit ≠ kredit',

            // Account Payable
            'Daftar AP (Account Payable) — monitoring hutang ke supplier',
            'Pembayaran AP — pencatatan pembayaran hutang (list + detail)',
            'Laporan Aging AP — analisis umur hutang untuk manajemen cash flow',
            'Tukar Faktur — proses tukar faktur dengan supplier',
            'Purchase Order — monitoring data purchase order dari Khanza',

            // Account Receivable
            'Daftar AR (Account Receivable) — monitoring piutang dari asuransi/pasien',
            'Penerimaan AR — pencatatan penerimaan pembayaran piutang',
            'Settlement AR — proses settlement piutang',
            'Laporan Aging AR — analisis umur piutang',

            // Jasa Medis
            'Posting Jasa Medis — posting data jasa medis dokter dari Khanza',
            'Payment Voucher — generate voucher pembayaran jasa medis per dokter',
            'Settlement Jasa Medis — pencatatan settlement pembayaran jasa medis',

            // Petty Cash
            'Dashboard Petty Cash — ringkasan kas kecil per unit',
            'Unit Kas Kecil — kelola unit-unit pemegang kas kecil',
            'Pengajuan Kas Kecil — form pengajuan dan approval kas kecil',
            'Settlement Kas Kecil — pertanggungjawaban penggunaan kas kecil',
            'Top Up Kas Kecil — pengisian ulang saldo kas kecil',
            'Buku Kas Kecil (Ledger) — catatan mutasi kas kecil per unit',

            // General Ledger
            'General Ledger History — riwayat transaksi buku besar',
            'General Ledger Summary — ringkasan saldo buku besar',

            // Laporan
            'Neraca (Balance Sheet) — laporan posisi keuangan',
            'Laba Rugi (Profit & Loss) — laporan pendapatan dan beban',
            'Arus Kas (Cash Flow) — laporan arus kas aktivitas operasi, investasi, dan pendanaan',
            'Trial Balance — laporan neraca saldo',

            // Settings & User Management
            'Pengaturan Laporan — konfigurasi header dan format laporan',
            'Manajemen User — kelola user, role, dan hak akses (RBAC)',
            'Role-Based Access Control — proteksi halaman dan aksi berdasarkan role',
        ],
    },
];
