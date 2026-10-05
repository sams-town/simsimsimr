-- ============================================================================
-- Halaman "Sinkronisasi Data Khanza" (CfgKhanzaSync).
--
-- Modul untuk membandingkan data hasil tarikan scheduler (AP, AR, PO, Jasa Medis)
-- dengan data terbaru langsung dari database Khanza, lalu menerapkan field yang
-- dipilih. Hanya membaca Khanza - tidak pernah menulis ke Khanza.
--
-- Tidak ada tabel baru: jejak perubahan dicatat di logs.audit_logs (before/after).
-- Idempoten, aman dijalankan ulang.
-- ============================================================================

INSERT INTO master.mst_role_page (page_code, page, category, "action")
VALUES ('CfgKhanzaSync', 'Sinkronisasi Data Khanza', 'Settings', 'Read,Update')
ON CONFLICT (page_code) DO UPDATE
    SET page = EXCLUDED.page,
        category = EXCLUDED.category,
        "action" = EXCLUDED."action";

-- Administrator (1) dan Manager (2). Service juga menegakkan daftar role yang sama.
INSERT INTO master.mst_role_page_access (page_code, "action", role_id)
VALUES ('CfgKhanzaSync', 'Read,Update', 1),
       ('CfgKhanzaSync', 'Read,Update', 2)
ON CONFLICT (page_code, "action", role_id) DO NOTHING;

-- Index visit_no untuk jasa medis. Modul sinkron mencari, memuat, dan meng-UPDATE per kunjungan;
-- tanpa index ini setiap operasi melakukan seq scan atas seluruh tabel (±270 ribu baris).
-- Sengaja TANPA CONCURRENTLY supaya aman dijalankan dari DBeaver/psql apa pun (CONCURRENTLY
-- tidak boleh di dalam transaction block). Kunci tabel hanya selama index dibangun, hitungan detik.
CREATE INDEX IF NOT EXISTS idx_medical_fee_visit_no
    ON public.trx_medical_fee (visit_no);

SELECT p.page_code, p.page, a.role_id, a."action"
FROM master.mst_role_page p
JOIN master.mst_role_page_access a ON a.page_code = p.page_code
WHERE p.page_code = 'CfgKhanzaSync'
ORDER BY a.role_id;
