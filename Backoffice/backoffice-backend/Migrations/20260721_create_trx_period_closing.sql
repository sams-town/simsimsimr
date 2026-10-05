-- ============================================================================
-- Item 7 — Modul Tutup Buku (Period Closing)
-- Tabel catatan tutup buku periode (bulanan/tahunan) + lock periode + riwayat.
-- Idempotent.
-- ============================================================================

CREATE TABLE IF NOT EXISTS public.trx_period_closing (
    id                        SERIAL PRIMARY KEY,
    period_type               varchar(10)  NOT NULL DEFAULT 'Monthly',  -- 'Monthly' | 'Yearly'
    year                      int          NOT NULL,
    month                     int          NULL,                         -- NULL utk 'Yearly'
    branch_id                 int          NULL,                         -- NULL = semua branch
    status                    varchar(10)  NOT NULL DEFAULT 'Closed',    -- 'Closed' | 'Reopened'
    generate_closing_entries  boolean      NOT NULL DEFAULT false,
    closing_transaction_id    varchar      NULL,
    note                      text         NULL,
    closed_by                 int          NULL,
    closed_date               timestamptz  NULL,
    reopened_by               int          NULL,
    reopened_date             timestamptz  NULL
);

-- Satu baris per (type, year, month, branch). COALESCE agar NULL month/branch unik.
CREATE UNIQUE INDEX IF NOT EXISTS uq_period_closing_key
    ON public.trx_period_closing (period_type, year, COALESCE(month, 0), COALESCE(branch_id, 0));

CREATE INDEX IF NOT EXISTS idx_period_closing_lookup
    ON public.trx_period_closing (year, month, branch_id, status);

-- ----------------------------------------------------------------------------
-- Konfigurasi akun jurnal penutup (dipakai HANYA bila opsi 'Buat jurnal penutup' dipilih).
-- Placeholder kosong: GANTI option_value dengan kode COA riil (Laba Ditahan & Ikhtisar L/R)
-- sebelum memakai fitur jurnal penutup. Di-seed per branch_id = 1 (branch aktif on-prem).
-- ----------------------------------------------------------------------------
INSERT INTO public.cfg_options (option_name, option_value, option_type, option_label, option_desc, branch_id)
VALUES
('COA_RETAINED_EARNINGS', '', 'DEFAULT_ACCOUNT', 'COA Laba Ditahan', 'Kode COA Laba Ditahan tujuan jurnal penutup tahunan', 1),
('COA_INCOME_SUMMARY',    '', 'DEFAULT_ACCOUNT', 'COA Ikhtisar Laba Rugi', 'Kode COA Ikhtisar L/R (opsional) untuk jurnal penutup', 1)
ON CONFLICT (option_name, branch_id) DO NOTHING;
