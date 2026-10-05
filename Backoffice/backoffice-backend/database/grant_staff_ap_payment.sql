-- ============================================================================
-- Beri akses STAFF (role_id = 4) untuk PEMBAYARAN AP (settlement hutang).
--
-- Endpoint pembayaran AP butuh:
--   POST /AccountPayable/Payment       -> Action.Create
--   POST /AccountPayable/BatchPayment  -> Action.Create
--   PUT  /AccountPayable/Cancel        -> Action.Update  (batalkan pembayaran)
--
-- Saat ini Staff hanya 'Read,Export' -> tambah 'Create'.
-- (Kalau Staff juga perlu MEMBATALKAN pembayaran, pakai versi + Update di bawah.)
--
-- Permission aktual ada di master.mst_role_page_access (DB ini sumber kebenaran).
-- Cek dulu: SELECT "action" FROM master.mst_role_page_access
--           WHERE page_code='TrxAccountPayable' AND role_id=4;
-- ============================================================================

BEGIN;

-- Versi inti: bisa membuat/melakukan pembayaran (Payment + BatchPayment)
UPDATE master.mst_role_page_access
SET "action" = 'Create,Read,Export'
WHERE page_code = 'TrxAccountPayable' AND role_id = 4;

-- Versi + Cancel (aktifkan kalau Staff juga boleh membatalkan pembayaran):
-- UPDATE master.mst_role_page_access
-- SET "action" = 'Create,Read,Update,Export'
-- WHERE page_code = 'TrxAccountPayable' AND role_id = 4;

COMMIT;
-- ROLLBACK;  -- untuk uji-coba tanpa menyimpan

-- Verifikasi:
--   SELECT page_code, "action", role_id FROM master.mst_role_page_access
--   WHERE page_code='TrxAccountPayable' AND role_id=4;
