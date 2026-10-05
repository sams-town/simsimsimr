-- ============================================================================
-- Index pendukung perhitungan saldo ON-READ (GetEndingBalance / GetLedgers).
-- Tanpa ini, query saldo per-COA (WHERE coa_code = ...) tidak punya index yang
-- diawali coa_code (index trx_journal yang ada: branch_id, created_date,
-- (transaction_id, coa_code)) -> cenderung scan saat tabel membesar.
--
-- Aman & idempotent. Untuk tabel besar, pertimbangkan CONCURRENTLY (di luar transaksi):
--   CREATE INDEX CONCURRENTLY IF NOT EXISTS ...
-- ============================================================================

CREATE INDEX IF NOT EXISTS idx_trx_journal_coa_branch
    ON public.trx_journal (coa_code, branch_id);

-- Verifikasi:
--   SELECT indexname, indexdef FROM pg_indexes WHERE tablename = 'trx_journal';
