-- Grant STAFF role (role_id = 4) access to all Form Makers built in this project.
--
-- Form Makers are gated by existing page codes (they have no dedicated page code):
--   * AP Form Maker (Bukti Pengeluaran AP)      -> TrxAccountPayable
--   * Form Maker Penerimaan (Bukti Penerimaan)  -> TrxTransaction
--   * Form Maker Pengeluaran (Bukti Pengeluaran)-> TrxTransaction
--
-- Actions needed by the form makers: Read (list/detail), Create (buat),
-- Update (ubah), Delete (hapus). Export is included to match the existing
-- convention for these transaction pages (non-reducing).
--
-- NOTE: Because these page codes are shared with their parent modules
-- (Pembayaran AP, Daftar Penerimaan, Daftar Pengeluaran), this also grants
-- Staff access to those parent pages. That is inherent to the permission model.
--
-- Idempotent: DELETE then INSERT (avoids ON CONFLICT issues), safe to re-run.
-- If Staff is not role_id 4 in your database, change the value below.

BEGIN;

DELETE FROM master.mst_role_page_access
WHERE role_id = 4
  AND page_code IN ('TrxAccountPayable', 'TrxTransaction');

INSERT INTO master.mst_role_page_access (page_code, "action", role_id) VALUES
('TrxAccountPayable', 'Create,Read,Update,Delete,Export', 4),
('TrxTransaction',    'Create,Read,Update,Delete,Export', 4);

COMMIT;
