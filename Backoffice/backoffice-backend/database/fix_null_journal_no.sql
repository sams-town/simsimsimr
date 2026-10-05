-- ============================================================
-- Fix NULL / Duplicate journal_no di trx_transaction & trx_journal
-- Pattern: JRN/{yyyy}/{MM}/{sequence per bulan}
--
-- Jalankan step by step, SELALU jalankan DIAGNOSTIC dulu
-- sebelum menjalankan UPDATE.
-- ============================================================

-- ============================================================
-- STEP 0: DIAGNOSTIC - Jalankan ini dulu untuk cek kondisi data
-- ============================================================

-- Case 1: trx_transaction journal_no NULL
SELECT 'Case 1: trx_transaction journal_no NULL' AS case_name, count(*) AS cnt
FROM trx_transaction WHERE journal_no IS NULL OR journal_no = '';

-- Case 2: trx_journal journal_no NULL, tapi trx_transaction sudah punya
SELECT 'Case 2: trx_journal NULL, trx_transaction has value' AS case_name, count(*) AS cnt
FROM trx_journal j
JOIN trx_transaction t ON j.transaction_id = t.transaction_id
WHERE (j.journal_no IS NULL OR j.journal_no = '')
  AND t.journal_no IS NOT NULL AND t.journal_no != '';

-- Case 3: Keduanya NULL (trx_transaction & trx_journal)
SELECT 'Case 3: Both NULL' AS case_name, count(*) AS cnt
FROM trx_journal j
JOIN trx_transaction t ON j.transaction_id = t.transaction_id
WHERE (j.journal_no IS NULL OR j.journal_no = '')
  AND (t.journal_no IS NULL OR t.journal_no = '');

-- Case 4: Duplicate journal_no di trx_transaction
SELECT 'Case 4: Duplicate journal_no in trx_transaction' AS case_name, count(*) AS cnt FROM (
    SELECT journal_no FROM trx_transaction
    WHERE journal_no IS NOT NULL AND journal_no != ''
    GROUP BY journal_no HAVING COUNT(*) > 1
) d;

-- Case 5: trx_journal orphan (tidak punya transaction)
SELECT 'Case 5: trx_journal NULL, no matching transaction' AS case_name, count(*) AS cnt
FROM trx_journal j
LEFT JOIN trx_transaction t ON j.transaction_id = t.transaction_id
WHERE (j.journal_no IS NULL OR j.journal_no = '')
  AND t.transaction_id IS NULL;

-- Case 6: Mismatch journal_no antara trx_journal dan trx_transaction
SELECT 'Case 6: journal_no mismatch between tables' AS case_name, count(*) AS cnt
FROM trx_journal j
JOIN trx_transaction t ON j.transaction_id = t.transaction_id
WHERE j.journal_no IS NOT NULL AND j.journal_no != ''
  AND t.journal_no IS NOT NULL AND t.journal_no != ''
  AND j.journal_no != t.journal_no;


-- ============================================================
-- STEP 1: Fix trx_transaction journal_no NULL
--         Generate journal_no baru dengan sequence per bulan
-- ============================================================

-- Preview dulu:
-- SELECT t.transaction_id, t.transaction_no, t.transaction_type, t.created_date::date,
--     'JRN/' || n.yr || '/' || n.mn || '/' || (COALESCE(m.current_max, 0) + n.rn)::text AS new_journal_no
-- FROM (
--     SELECT transaction_id,
--         TO_CHAR(created_date, 'YYYY/MM') AS ym,
--         TO_CHAR(created_date, 'YYYY') AS yr,
--         TO_CHAR(created_date, 'MM') AS mn,
--         ROW_NUMBER() OVER (PARTITION BY TO_CHAR(created_date, 'YYYY/MM') ORDER BY created_date, transaction_no) AS rn
--     FROM trx_transaction WHERE journal_no IS NULL OR journal_no = ''
-- ) n
-- JOIN trx_transaction t ON t.transaction_id = n.transaction_id
-- LEFT JOIN (
--     SELECT TO_CHAR(created_date, 'YYYY/MM') AS ym,
--         COALESCE(MAX(CAST(SUBSTRING(journal_no FROM 'JRN/\d{4}/\d{2}/(\d+)') AS INTEGER)), 0) AS current_max
--     FROM trx_transaction WHERE journal_no IS NOT NULL AND journal_no ~ '^JRN/\d{4}/\d{2}/\d+$'
--     GROUP BY TO_CHAR(created_date, 'YYYY/MM')
-- ) m ON m.ym = n.ym
-- ORDER BY t.created_date, t.transaction_no;

-- Execute:
WITH month_maxes AS (
    SELECT
        TO_CHAR(created_date, 'YYYY/MM') AS ym,
        COALESCE(MAX(CAST(SUBSTRING(journal_no FROM 'JRN/\d{4}/\d{2}/(\d+)') AS INTEGER)), 0) AS current_max
    FROM trx_transaction
    WHERE journal_no IS NOT NULL
      AND journal_no ~ '^JRN/\d{4}/\d{2}/\d+$'
    GROUP BY TO_CHAR(created_date, 'YYYY/MM')
),
null_rows AS (
    SELECT
        transaction_id,
        TO_CHAR(created_date, 'YYYY/MM') AS ym,
        TO_CHAR(created_date, 'YYYY') AS yr,
        TO_CHAR(created_date, 'MM') AS mn,
        ROW_NUMBER() OVER (
            PARTITION BY TO_CHAR(created_date, 'YYYY/MM')
            ORDER BY created_date, transaction_no
        ) AS rn
    FROM trx_transaction
    WHERE journal_no IS NULL OR journal_no = ''
)
UPDATE trx_transaction t
SET journal_no = 'JRN/' || n.yr || '/' || n.mn || '/' || (COALESCE(m.current_max, 0) + n.rn)::text
FROM null_rows n
LEFT JOIN month_maxes m ON m.ym = n.ym
WHERE t.transaction_id = n.transaction_id;


-- ============================================================
-- STEP 2: Fix duplicate journal_no di trx_transaction
--         Record pertama (by created_date) tetap, sisanya di-renumber
-- ============================================================

-- Preview dulu:
-- SELECT d.transaction_id, d.journal_no AS old_journal_no, d.rn,
--     CASE WHEN d.rn = 1 THEN d.journal_no
--          ELSE m.prefix || (m.max_seq + d.rn - 1)::text
--     END AS new_journal_no
-- FROM (
--     SELECT transaction_id, journal_no, created_date,
--         ROW_NUMBER() OVER (PARTITION BY journal_no ORDER BY created_date) AS rn
--     FROM trx_transaction
--     WHERE journal_no IN (
--         SELECT journal_no FROM trx_transaction
--         WHERE journal_no IS NOT NULL AND journal_no != ''
--         GROUP BY journal_no HAVING COUNT(*) > 1
--     )
-- ) d
-- CROSS JOIN LATERAL (
--     SELECT
--         SUBSTRING(d.journal_no FROM '^(JRN/\d{4}/\d{2}/)') AS prefix,
--         (SELECT MAX(CAST(SUBSTRING(journal_no FROM 'JRN/\d{4}/\d{2}/(\d+)') AS INTEGER))
--          FROM trx_transaction
--          WHERE journal_no LIKE SUBSTRING(d.journal_no FROM '^(JRN/\d{4}/\d{2}/)') || '%') AS max_seq
-- ) m
-- ORDER BY d.journal_no, d.rn;

-- Execute:
WITH dup_journal_nos AS (
    SELECT journal_no
    FROM trx_transaction
    WHERE journal_no IS NOT NULL AND journal_no != ''
    GROUP BY journal_no
    HAVING COUNT(*) > 1
),
dupes AS (
    SELECT
        transaction_id,
        journal_no,
        created_date,
        SUBSTRING(journal_no FROM '^(JRN/\d{4}/\d{2}/)') AS prefix,
        ROW_NUMBER() OVER (PARTITION BY journal_no ORDER BY created_date) AS rn
    FROM trx_transaction
    WHERE journal_no IN (SELECT journal_no FROM dup_journal_nos)
),
month_max AS (
    SELECT
        SUBSTRING(journal_no FROM '^(JRN/\d{4}/\d{2}/)') AS prefix,
        MAX(CAST(SUBSTRING(journal_no FROM 'JRN/\d{4}/\d{2}/(\d+)') AS INTEGER)) AS max_seq
    FROM trx_transaction
    WHERE journal_no IS NOT NULL AND journal_no ~ '^JRN/\d{4}/\d{2}/\d+$'
    GROUP BY SUBSTRING(journal_no FROM '^(JRN/\d{4}/\d{2}/)')
)
UPDATE trx_transaction t
SET journal_no = d.prefix || (m.max_seq + d.rn - 1)::text
FROM dupes d
JOIN month_max m ON m.prefix = d.prefix
WHERE t.transaction_id = d.transaction_id
  AND d.rn > 1;


-- ============================================================
-- STEP 3: Sync journal_no dari trx_transaction ke trx_journal
--         (Case 2: trx_journal NULL, trx_transaction sudah punya)
-- ============================================================

-- Preview dulu:
-- SELECT j.journal_id, j.journal_no AS old_jno, t.journal_no AS new_jno, j.source
-- FROM trx_journal j
-- JOIN trx_transaction t ON j.transaction_id = t.transaction_id
-- WHERE (j.journal_no IS NULL OR j.journal_no = '')
--   AND t.journal_no IS NOT NULL AND t.journal_no != ''
-- ORDER BY j.journal_id;

-- Execute:
UPDATE trx_journal j
SET journal_no = t.journal_no
FROM trx_transaction t
WHERE j.transaction_id = t.transaction_id
  AND (j.journal_no IS NULL OR j.journal_no = '')
  AND t.journal_no IS NOT NULL AND t.journal_no != '';


-- ============================================================
-- STEP 4: Fix trx_journal orphan yang tidak punya transaction
--         Generate journal_no baru berdasarkan created_date journal
-- ============================================================

-- Preview dulu:
-- SELECT j.journal_id, j.source, j.created_date::date
-- FROM trx_journal j
-- LEFT JOIN trx_transaction t ON j.transaction_id = t.transaction_id
-- WHERE (j.journal_no IS NULL OR j.journal_no = '')
--   AND t.transaction_id IS NULL;

-- Execute:
WITH month_maxes AS (
    SELECT
        TO_CHAR(created_date, 'YYYY/MM') AS ym,
        COALESCE(MAX(CAST(SUBSTRING(journal_no FROM 'JRN/\d{4}/\d{2}/(\d+)') AS INTEGER)), 0) AS current_max
    FROM trx_transaction
    WHERE journal_no IS NOT NULL AND journal_no ~ '^JRN/\d{4}/\d{2}/\d+$'
    GROUP BY TO_CHAR(created_date, 'YYYY/MM')
),
orphan_rows AS (
    SELECT
        j.journal_id,
        TO_CHAR(j.created_date, 'YYYY/MM') AS ym,
        TO_CHAR(j.created_date, 'YYYY') AS yr,
        TO_CHAR(j.created_date, 'MM') AS mn,
        ROW_NUMBER() OVER (
            PARTITION BY TO_CHAR(j.created_date, 'YYYY/MM')
            ORDER BY j.created_date, j.journal_id
        ) AS rn
    FROM trx_journal j
    LEFT JOIN trx_transaction t ON j.transaction_id = t.transaction_id
    WHERE (j.journal_no IS NULL OR j.journal_no = '')
      AND t.transaction_id IS NULL
)
UPDATE trx_journal j
SET journal_no = 'JRN/' || o.yr || '/' || o.mn || '/' || (COALESCE(m.current_max, 0) + o.rn)::text
FROM orphan_rows o
LEFT JOIN month_maxes m ON m.ym = o.ym
WHERE j.journal_id = o.journal_id;


-- ============================================================
-- STEP 5: Fix mismatch journal_no (trx_journal != trx_transaction)
--         Sync journal_no trx_journal = trx_transaction (source of truth)
-- ============================================================

-- Preview dulu:
-- SELECT j.journal_id, j.journal_no AS journal_jno, t.journal_no AS transaction_jno
-- FROM trx_journal j
-- JOIN trx_transaction t ON j.transaction_id = t.transaction_id
-- WHERE j.journal_no IS NOT NULL AND j.journal_no != ''
--   AND t.journal_no IS NOT NULL AND t.journal_no != ''
--   AND j.journal_no != t.journal_no;

-- Execute:
UPDATE trx_journal j
SET journal_no = t.journal_no
FROM trx_transaction t
WHERE j.transaction_id = t.transaction_id
  AND j.journal_no IS NOT NULL AND j.journal_no != ''
  AND t.journal_no IS NOT NULL AND t.journal_no != ''
  AND j.journal_no != t.journal_no;


-- ============================================================
-- STEP 6: VERIFIKASI AKHIR - Jalankan setelah semua step selesai
-- ============================================================

SELECT 'trx_transaction NULL' AS check_name, count(*) AS cnt
FROM trx_transaction WHERE journal_no IS NULL OR journal_no = ''
UNION ALL
SELECT 'trx_journal NULL', count(*)
FROM trx_journal WHERE journal_no IS NULL OR journal_no = ''
UNION ALL
SELECT 'trx_transaction DUPLICATE', count(*) FROM (
    SELECT journal_no FROM trx_transaction
    WHERE journal_no IS NOT NULL AND journal_no != ''
    GROUP BY journal_no HAVING COUNT(*) > 1
) d
UNION ALL
SELECT 'Mismatch journal_no', count(*)
FROM trx_journal j
JOIN trx_transaction t ON j.transaction_id = t.transaction_id
WHERE j.journal_no IS NOT NULL AND j.journal_no != ''
  AND t.journal_no IS NOT NULL AND t.journal_no != ''
  AND j.journal_no != t.journal_no;
