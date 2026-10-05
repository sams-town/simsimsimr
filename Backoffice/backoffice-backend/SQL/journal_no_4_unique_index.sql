-- ============================================================================
-- LANGKAH 4/4 - UNIQUE INDEX. Jalankan SETELAH kode baru ter-deploy.
--
--   \i backoffice-backend/SQL/journal_no_4_unique_index.sql
--
-- Dipasang paling akhir dengan sengaja. Kode LAMA masih menomori lewat MAX
-- teks dan akan langsung melanggar index ini, sehingga setiap pembuatan jurnal
-- gagal. Pasang hanya setelah semua instance memakai counter.
--
-- CATATAN psql: CREATE INDEX CONCURRENTLY tidak boleh jalan di dalam
-- transaction block. psql sudah auto-commit, jadi file ini aman dijalankan
-- apa adanya - ASAL tanpa flag -1 / --single-transaction.
-- ============================================================================

\echo '== 4a. Duplikat baru yang lolos di sela renumber & deploy? ============='
-- HARUS 0 baris. Kalau ada isinya, jalankan ulang journal_no_2_renumber.sql
-- lebih dulu, baru lanjut ke 4b.
SELECT journal_no, COUNT(*) AS jml
FROM public.trx_transaction
WHERE journal_no IS NOT NULL AND journal_no <> ''
GROUP BY journal_no
HAVING COUNT(*) > 1
ORDER BY jml DESC
LIMIT 20;


\echo '== 4b. Sisa index INVALID dari percobaan sebelumnya? ==================='
-- CREATE INDEX CONCURRENTLY yang gagal meninggalkan index INVALID: tidak
-- dipakai planner, tetap membebani INSERT/UPDATE, dan IF NOT EXISTS akan
-- menganggapnya sudah ada sehingga percobaan ulang di-skip diam-diam.
SELECT c.relname AS nama_index, i.indisvalid AS valid
FROM pg_class c
JOIN pg_index i ON i.indexrelid = c.oid
WHERE c.relname = 'uq_trx_transaction_journal_no';

-- Kalau valid = false:
--   DROP INDEX CONCURRENTLY public.uq_trx_transaction_journal_no;


\echo '== 4c. Pasang unique index ============================================'
CREATE UNIQUE INDEX CONCURRENTLY IF NOT EXISTS uq_trx_transaction_journal_no
    ON public.trx_transaction (journal_no)
    WHERE journal_no IS NOT NULL AND journal_no <> '';


\echo '== 4d. Pastikan index terpasang dan VALID ============================='
SELECT c.relname AS nama_index, i.indisvalid AS valid, pg_size_pretty(pg_relation_size(c.oid)) AS ukuran
FROM pg_class c
JOIN pg_index i ON i.indexrelid = c.oid
WHERE c.relname = 'uq_trx_transaction_journal_no';
