-- ============================================================================
-- LANGKAH 2/4 - RENUMBER. INI MENGUBAH DATA.
--
--   \i backoffice-backend/SQL/journal_no_2_renumber.sql
--
-- SENGAJA TANPA BEGIN/COMMIT. Seluruh renumber adalah SATU statement
-- (WITH .. UPDATE .. INSERT), jadi Postgres sudah menjalankannya atomik dalam
-- transaksi implisit: berhasil semua atau tidak sama sekali. Menambahkan BEGIN
-- manual justru yang bikin file ini tidak bisa dijalankan dengan \i - transaksi
-- menggantung dan statement berikutnya ikut terseret.
--
-- Aturan penomoran:
--   * baris TERTUA (created_date terkecil) MEMPERTAHANKAN nomor aslinya,
--   * baris sisanya dapat nomor mulai dari MAX bulan itu + 1, menaik.
-- Nomor baru selalu di atas MAX periode, jadi tidak mungkin bentrok.
--
-- trx_journal.journal_no SENGAJA TIDAK disentuh - kolom itu tidak dipakai.
-- ============================================================================

\echo '== 2a. Renumber (perhatikan angka INSERT 0 <n>) ========================'
WITH parsed AS (
    SELECT t.transaction_id,
           t.journal_no,
           SUBSTRING(t.journal_no FROM 1 FOR 12)   AS prefix,
           SUBSTRING(t.journal_no FROM 13)::bigint AS no_num,
           t.created_date
    FROM public.trx_transaction t
    WHERE t.journal_no ~ '^JRN/[0-9]{4}/[0-9]{2}/[0-9]+$'
),
max_per_prefix AS (
    SELECT prefix, MAX(no_num) AS max_no FROM parsed GROUP BY prefix
),
ranked AS (
    SELECT parsed.*,
           ROW_NUMBER() OVER (PARTITION BY journal_no
                              ORDER BY created_date NULLS LAST, transaction_id) AS dup_rank
    FROM parsed
),
to_fix AS (
    SELECT r.transaction_id,
           r.prefix,
           r.journal_no AS journal_no_lama,
           m.max_no + ROW_NUMBER() OVER (PARTITION BY r.prefix
                                         ORDER BY r.no_num, r.transaction_id) AS no_baru
    FROM ranked r
    JOIN max_per_prefix m ON m.prefix = r.prefix
    WHERE r.dup_rank > 1
),
upd AS (
    UPDATE public.trx_transaction t
       -- JANGAN pakai LPAD(x, 4, '0') di sini. Di PostgreSQL LPAD MEMOTONG
       -- string yang lebih panjang dari panjang target, sehingga nomor 5 digit
       -- seperti 12092 berubah jadi '1209'. Padding hanya berlaku untuk nomor
       -- di bawah 10000; sisanya dibiarkan apa adanya.
       SET journal_no = f.prefix || CASE WHEN f.no_baru < 10000
                                         THEN LPAD(f.no_baru::text, 4, '0')
                                         ELSE f.no_baru::text END
      FROM to_fix f
     WHERE t.transaction_id = f.transaction_id
    RETURNING t.transaction_id, f.prefix, f.journal_no_lama,
              t.journal_no AS journal_no_baru
)
INSERT INTO public.journal_no_renumber_log
       (transaction_id, periode, journal_no_lama, journal_no_baru)
SELECT transaction_id, SUBSTRING(prefix FROM 5 FOR 7),
       journal_no_lama, journal_no_baru
FROM upd;


\echo '== 2b. VERIFIKASI - harus 0 baris ====================================='
SELECT journal_no, COUNT(*) AS jml
FROM public.trx_transaction
WHERE journal_no ~ '^JRN/[0-9]{4}/[0-9]{2}/[0-9]+$'
GROUP BY journal_no
HAVING COUNT(*) > 1;


\echo '== 2c. VERIFIKASI - harus 0 ==========================================='
SELECT COUNT(*) AS journal_no_kosong
FROM public.trx_transaction
WHERE journal_no IS NULL OR journal_no = '';


\echo '== 2d. Ringkasan yang tercatat di log ================================='
SELECT periode, COUNT(*) AS jml_direnumber
FROM public.journal_no_renumber_log
GROUP BY periode ORDER BY periode;


\echo '== 2e. Rapikan statistik & bloat ======================================'
VACUUM ANALYZE public.trx_transaction;
