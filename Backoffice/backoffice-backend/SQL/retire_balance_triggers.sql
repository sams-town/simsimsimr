-- ============================================================================
-- PENSIUNKAN trigger saldo incremental.
--
-- Alasan: saldo kini dihitung ON-READ (checkpoint + Σ mutasi jurnal) di
-- JournalService.GetEndingBalance & GetLedgers — membaca langsung dari tabel
-- jurnal, sehingga:
--   * benar untuk semua sumber insert (scheduler insert langsung ke DB & aplikasi),
--   * kebal urutan posting (backdate),
--   * tidak butuh update incremental per-insert.
--
-- Trigger lama (process_journal_insert / perform_balance_update) JUSTRU sumber bug:
--   * opening diambil dari mst_coa.balance global lalu distempel di period_date = tgl transaksi
--     -> tidak konsisten kronologis,
--   * snapshot per-transaksi -> double-count bila dipadukan dengan model checkpoint+mutasi.
--
-- >>> JALANKAN BERSAMAAN dengan rebalance_migration.sql DAN deploy kode
--     GetEndingBalance versi on-read. Setelah ini, snapshot trx_balance hanya
--     berisi checkpoint pembuka (dari migrasi) + (opsional) checkpoint bulanan
--     dari job batch — BUKAN per-transaksi.
-- ============================================================================

DROP TRIGGER IF EXISTS trg_journal_insert ON public.trx_journal;
DROP TRIGGER IF EXISTS trg_trx_publish   ON public.trx_transaction;

-- Fungsi dibiarkan ada untuk referensi/rollback. Drop hanya jika yakin tak dipakai:
-- DROP FUNCTION IF EXISTS public.process_journal_insert();
-- DROP FUNCTION IF EXISTS public.process_transaction_publish();
-- DROP FUNCTION IF EXISTS public.perform_balance_update(VARCHAR, TIMESTAMP, DOUBLE PRECISION, DOUBLE PRECISION, INT);
-- DROP FUNCTION IF EXISTS public.update_trx_balance();

-- ROLLBACK (pasang lagi trigger) bila perlu: jalankan ulang SQL/triggers.sql
