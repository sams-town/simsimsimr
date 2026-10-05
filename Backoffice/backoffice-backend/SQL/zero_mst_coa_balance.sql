-- ============================================================================
-- NOL-KAN semua master.mst_coa.balance
--
-- AMAN setelah perubahan berikut (sudah di kode):
--   * Daftar Buku Kas (GET /COA?iscashbank=true) -> saldo dihitung ON-READ (GetEndingBalance),
--     tidak lagi membaca kolom balance. (COAController.GetAll)
--   * Export Buku Kas (GET /COA/export?iscashbankexport=true) -> ON-READ. (COAController.Export)
--   * Detail Buku Kas (GET /code/{coa}) -> sudah ON-READ (GetEndingBalance). (COAController)
--   * Semua laporan (Neraca/Laba Rugi/Arus Kas/Buku Besar/Neraca Saldo) -> ON-READ dari
--     trx_journal (Published) + trx_balance (checkpoint 'Opening' dari menu Saldo Awal).
--
-- Prasyarat: PASTIKAN trigger lama sudah di-drop (SQL/retire_balance_triggers.sql), supaya
-- balance tidak terisi lagi otomatis saat ada insert jurnal.
--
-- Idempotent. DRY-RUN: ganti COMMIT dengan ROLLBACK untuk melihat tanpa menyimpan.
-- ============================================================================

BEGIN;

-- (opsional) lihat dulu yang tidak nol:
-- SELECT coa_code, coa_name, balance FROM master.mst_coa WHERE COALESCE(balance,0) <> 0 ORDER BY coa_code;

UPDATE master.mst_coa
SET balance = 0,
    updated_date = NOW() AT TIME ZONE 'UTC'
WHERE COALESCE(balance, 0) <> 0;

-- verifikasi: harus 0 baris
-- SELECT COUNT(*) AS masih_ada_saldo FROM master.mst_coa WHERE COALESCE(balance,0) <> 0;

COMMIT;
-- ROLLBACK;
