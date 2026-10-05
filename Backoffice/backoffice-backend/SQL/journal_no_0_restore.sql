-- ============================================================================
-- PEMULIHAN - kembalikan journal_no ke nilai sebelum renumber.
--
--   \i backoffice-backend/SQL/journal_no_0_restore.sql
--
-- Kenapa perlu: renumber sebelumnya memakai LPAD(no_baru::text, 4, '0') untuk
-- menjaga format 4 digit. Di PostgreSQL, LPAD MEMOTONG string yang lebih
-- panjang dari panjang target - jadi 'JRN/2026/02/12092' menjadi
-- 'JRN/2026/02/1209'. Semua 186.200 nomor baru ikut terpotong.
--
-- Pemulihan bersifat eksak: journal_no_renumber_log menyimpan journal_no_lama
-- untuk setiap transaction_id yang tersentuh. Tidak ada baris yang dihapus
-- kapan pun - operasi sebelumnya murni UPDATE satu kolom.
-- ============================================================================

SET lock_timeout = '60s';
SET statement_timeout = '15min';

\echo '== A. Kelayakan pemulihan (baris_log = trx_unik, lama_kosong = 0) ======'
SELECT COUNT(*)                       AS baris_log,
       COUNT(DISTINCT transaction_id) AS trx_unik,
       COUNT(*) FILTER (WHERE journal_no_lama IS NULL OR journal_no_lama = '') AS lama_kosong
FROM public.journal_no_renumber_log;

\echo '== B. PULIHKAN (harusnya UPDATE 186200) ================================'
UPDATE public.trx_transaction t
   SET journal_no = l.journal_no_lama
  FROM public.journal_no_renumber_log l
 WHERE t.transaction_id = l.transaction_id
   AND t.journal_no IS DISTINCT FROM l.journal_no_lama;

\echo '== C. Harus 0: tidak ada lagi baris yang menyimpang dari nilai lama ===='
SELECT COUNT(*) AS belum_pulih
FROM public.trx_transaction t
JOIN public.journal_no_renumber_log l ON l.transaction_id = t.transaction_id
WHERE t.journal_no <> l.journal_no_lama;

\echo '== D. Harus persis sama dengan snapshot awal ==========================='
-- Patokan sebelum renumber:
--   periode | total | unik  | duplikat | max_no
--   2026/01 | 36180 | 36180 |        0 |  60120
--   2026/02 | 38086 | 12069 |    26017 |  12091
--   2026/03 | 38703 | 38703 |        0 |  89799
--   2026/04 | 48163 | 10196 |    37967 |  11348
--   2026/05 | 45843 | 11058 |    34785 |  11916
--   2026/06 | 43902 |  9806 |    34096 |  10619
--   2026/07 | 48135 |  9371 |    38764 |  10261
--   2026/08 | 24008 |  9437 |    14571 |  10347
SELECT SUBSTRING(journal_no FROM 5 FOR 7)        AS periode,
       COUNT(*)                                  AS total_baris,
       COUNT(DISTINCT journal_no)                AS nomor_unik,
       COUNT(*) - COUNT(DISTINCT journal_no)     AS duplikat,
       MAX(SUBSTRING(journal_no FROM 13)::bigint) AS max_no
FROM public.trx_transaction
WHERE journal_no ~ '^JRN/[0-9]{4}/[0-9]{2}/[0-9]+$'
GROUP BY 1 ORDER BY 1;

\echo '== E. Jumlah baris utuh (kedua selisih harus 0) ========================'
SELECT (SELECT COUNT(*) FROM public.trx_transaction) - 323020  AS selisih_trx_transaction,
       (SELECT COUNT(*) FROM public.trx_journal)     - 1428187 AS selisih_trx_journal;

\echo '== F. Kosongkan log HANYA setelah C..E semuanya cocok =================='
-- Log ini adalah satu-satunya jalan pulang. Jangan dikosongkan sebelum
-- verifikasi di atas benar-benar sesuai. Jalankan manual:
--
--   TRUNCATE public.journal_no_renumber_log;
--
-- Setelah itu baru boleh menjalankan journal_no_2_renumber.sql versi perbaikan.
