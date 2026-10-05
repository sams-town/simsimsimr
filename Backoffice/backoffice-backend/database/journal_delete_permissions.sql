-- Pisahkan hak Void dari hak Delete pada halaman TrxTransaction.
--
-- Latar belakang: endpoint DELETE /Transaction/{id} (hapus) dan
-- DELETE /Transaction/void/{id} (void) sebelumnya sama-sama dijaga
-- Action.Delete, jadi mustahil membatasi hapus tanpa ikut mencabut void.
--
-- Kebijakan setelah skrip ini:
--   Delete -> Administrator (1) + Manager (2) saja.
--   Void   -> Administrator (1), Manager (2), Supervisor (3).
--   Staff (4) & Patty Cash (5) tidak berubah (tidak punya keduanya).
--
-- Hapus tetap ditolak backend bila transaksi sudah Published atau periodenya
-- sudah tutup buku (TransactionController.Delete), terlepas dari role.
--
-- CAKUPAN: skrip ini HANYA menyentuh baris page_code = 'TrxTransaction' untuk
-- role 1, 2, dan 3. Tidak ada TRUNCATE. Role lain, halaman lain, dan role kustom
-- (role_id di luar 1-3) tidak tersentuh sama sekali.
--
-- Idempoten: aman dijalankan berulang.
-- Catatan: AuthorizeAttribute mem-split kolom action dengan ',' TANPA trim,
-- jadi jangan pernah menyisipkan spasi di daftar action.

BEGIN;

-- =====================================================================
-- STEP 1: Preview — catat kondisi sebelum perubahan
-- =====================================================================
SELECT a.role_id, r.role_name, a."action"
FROM master.mst_role_page_access a
JOIN master.mst_role r ON r.role_id = a.role_id
WHERE a.page_code = 'TrxTransaction'
ORDER BY a.role_id;

-- =====================================================================
-- STEP 2: Daftarkan action 'Void' pada definisi halaman
--
-- Baris tunggal ber-primary key (page_code); UPDATE, bukan DELETE+INSERT,
-- karena mst_role_page adalah induk FK dari mst_role_page_access —
-- menghapusnya akan menyeret baris akses milik role lain.
-- =====================================================================
UPDATE master.mst_role_page
SET "action" = 'Create,Read,Update,Delete,Void,Export,Approve'
WHERE page_code = 'TrxTransaction';

-- =====================================================================
-- STEP 3: Tulis ulang akses TrxTransaction untuk role 1, 2, 3
--
-- DELETE dibatasi page_code + role_id, jadi tidak ada baris lain yang hilang.
-- =====================================================================
DELETE FROM master.mst_role_page_access
WHERE page_code = 'TrxTransaction'
  AND role_id IN (1, 2, 3);

INSERT INTO master.mst_role_page_access (page_code, "action", role_id) VALUES
-- Administrator: hapus + void
('TrxTransaction', 'Create,Read,Update,Delete,Void,Export,Approve', 1),
-- Manager: hapus + void
('TrxTransaction', 'Create,Read,Update,Delete,Void,Export,Approve', 2),
-- Supervisor: void saja, Delete dicabut
('TrxTransaction', 'Create,Read,Update,Void,Export,Approve', 3);

-- =====================================================================
-- STEP 4: Verifikasi — pastikan hasilnya sesuai kebijakan
-- =====================================================================
SELECT a.role_id,
       r.role_name,
       a."action",
       ('Delete' = ANY (string_to_array(a."action", ','))) AS bisa_hapus,
       ('Void'   = ANY (string_to_array(a."action", ','))) AS bisa_void
FROM master.mst_role_page_access a
JOIN master.mst_role r ON r.role_id = a.role_id
WHERE a.page_code = 'TrxTransaction'
ORDER BY a.role_id;

-- Harapan:
--   1 Administrator -> bisa_hapus = true,  bisa_void = true
--   2 Manager       -> bisa_hapus = true,  bisa_void = true
--   3 Supervisor    -> bisa_hapus = false, bisa_void = true
--   4 Staff         -> bisa_hapus = false, bisa_void = false

COMMIT;

-- =====================================================================
-- ROLLBACK (jalankan terpisah bila perlu balik ke kondisi semula)
-- =====================================================================
-- BEGIN;
-- UPDATE master.mst_role_page
-- SET "action" = 'Create,Read,Update,Delete,Export,Approve'
-- WHERE page_code = 'TrxTransaction';
--
-- DELETE FROM master.mst_role_page_access
-- WHERE page_code = 'TrxTransaction' AND role_id IN (1, 2, 3);
--
-- INSERT INTO master.mst_role_page_access (page_code, "action", role_id) VALUES
-- ('TrxTransaction', 'Create,Read,Update,Delete,Export,Approve', 1),
-- ('TrxTransaction', 'Create,Read,Update,Delete,Export,Approve', 2),
-- ('TrxTransaction', 'Create,Read,Update,Delete,Export,Approve', 3);
-- COMMIT;
