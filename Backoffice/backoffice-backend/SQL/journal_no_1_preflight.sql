-- ============================================================================
-- LANGKAH 1/4 - PRE-FLIGHT + DRY RUN. Tidak mengubah data sama sekali.
-- Aman dijalankan dengan \i.
--
--   \i backoffice-backend/SQL/journal_no_1_preflight.sql
--
-- Baca hasilnya, lalu lanjut ke journal_no_2_renumber.sql.
-- ============================================================================

\echo '== 1a. Trigger aktif (harus 0 baris) =================================='
-- trg_trx_publish (BEFORE UPDATE) hanya bereaksi saat status berubah
-- Pending -> Published. Renumber cuma menyentuh journal_no, jadi saldo tidak
-- ikut berubah. Tetap pastikan tidak ada trigger lain yang tak terduga.
SELECT tgname,
       tgrelid::regclass AS tabel,
       CASE tgenabled WHEN 'O' THEN 'aktif' WHEN 'D' THEN 'disabled'
                      ELSE tgenabled::text END AS status
FROM pg_trigger
WHERE NOT tgisinternal
  AND tgrelid IN ('public.trx_transaction'::regclass, 'public.trx_journal'::regclass);


\echo '== 1b. Kondisi per periode ============================================'
SELECT SUBSTRING(journal_no FROM 5 FOR 7)        AS periode,
       COUNT(*)                                  AS total_baris,
       COUNT(DISTINCT journal_no)                AS nomor_unik,
       COUNT(*) - COUNT(DISTINCT journal_no)     AS akan_direnumber,
       MAX(SUBSTRING(journal_no FROM 13)::bigint) AS max_sekarang
FROM public.trx_transaction
WHERE journal_no ~ '^JRN/[0-9]{4}/[0-9]{2}/[0-9]+$'
GROUP BY 1
ORDER BY 1;


\echo '== 1c. Tabel log ====================================================='
-- Satu-satunya jejak nomor lama -> nomor baru. Dibuat di luar transaksi
-- supaya tetap ada meski langkah berikutnya diulang.
CREATE TABLE IF NOT EXISTS public.journal_no_renumber_log (
    id              bigserial   PRIMARY KEY,
    transaction_id  text        NOT NULL,
    periode         text        NOT NULL,
    journal_no_lama text        NOT NULL,
    journal_no_baru text        NOT NULL,
    renumbered_at   timestamptz NOT NULL DEFAULT now()
);
CREATE INDEX IF NOT EXISTS idx_jrn_renumber_log_trx
    ON public.journal_no_renumber_log (transaction_id);
CREATE INDEX IF NOT EXISTS idx_jrn_renumber_log_lama
    ON public.journal_no_renumber_log (journal_no_lama);


\echo '== 1d. DRY RUN - persis yang akan dikerjakan langkah 2 ================='
-- Angka di kolom jml_diubah HARUS sama dengan yang dilaporkan langkah 2.
WITH parsed AS (
    SELECT t.transaction_id,
           t.transaction_type,
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
    -- WHERE dievaluasi sebelum window function, jadi ROW_NUMBER hanya menomori
    -- baris yang benar-benar akan diubah - tidak ada lompatan nomor.
    SELECT r.transaction_id,
           r.transaction_type,
           r.prefix,
           m.max_no + ROW_NUMBER() OVER (PARTITION BY r.prefix
                                         ORDER BY r.no_num, r.transaction_id) AS no_baru
    FROM ranked r
    JOIN max_per_prefix m ON m.prefix = r.prefix
    WHERE r.dup_rank > 1
)
SELECT SUBSTRING(prefix FROM 5 FOR 7) AS periode,
       COUNT(*)                       AS jml_diubah,
       MIN(no_baru)                   AS nomor_baru_dari,
       MAX(no_baru)                   AS nomor_baru_sampai,
       COUNT(*) FILTER (WHERE transaction_type <> 'KhanzaJournal') AS dari_aplikasi
FROM to_fix
GROUP BY 1
ORDER BY 1;


\echo '== 1e. CONTOH NILAI JADI - periksa bentuk STRING-nya, bukan angkanya ==='
-- Pemeriksaan ini wajib. Ringkasan di 1d hanya menampilkan no_baru sebagai
-- ANGKA, sehingga cacat pada perakitan string tidak terlihat. Persis begitulah
-- bug LPAD(x, 4, '0') lolos: LPAD memotong string yang lebih panjang dari
-- panjang target, jadi 12092 tersimpan sebagai '1209'.
--
-- panjang_baru HARUS 17 untuk nomor 5 digit (12 prefix + 5). Kalau muncul 16,
-- berarti nomor terpotong - HENTIKAN, jangan lanjut ke langkah 2.
WITH parsed AS (
    SELECT t.transaction_id, t.journal_no,
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
    SELECT r.journal_no AS journal_no_lama,
           r.prefix,
           m.max_no + ROW_NUMBER() OVER (PARTITION BY r.prefix
                                         ORDER BY r.no_num, r.transaction_id) AS no_baru
    FROM ranked r
    JOIN max_per_prefix m ON m.prefix = r.prefix
    WHERE r.dup_rank > 1
),
jadi AS (
    SELECT journal_no_lama,
           prefix || CASE WHEN no_baru < 10000
                          THEN LPAD(no_baru::text, 4, '0')
                          ELSE no_baru::text END AS journal_no_baru
    FROM to_fix
)
SELECT LENGTH(journal_no_baru) AS panjang_baru,
       COUNT(*)                AS jml,
       MIN(journal_no_lama)    AS contoh_lama,
       MIN(journal_no_baru)    AS contoh_baru,
       MAX(journal_no_baru)    AS contoh_baru_terbesar
FROM jadi
GROUP BY 1
ORDER BY 1;
