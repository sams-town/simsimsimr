-- ============================================================================
-- LANGKAH 3/4 - COUNTER PENOMORAN. Jalankan SEBELUM deploy kode baru.
--
--   \i backoffice-backend/SQL/journal_no_3_counter.sql
--
-- Kode versi baru (JournalService.GenerateJournalNo dan run_journal.py)
-- mengambil nomor dari tabel ini, BUKAN dari MAX(journal_no). Dua alasan:
--
--   1. journal_no bertipe teks. Cara lama memakai ORDER BY journal_no DESC,
--      yang salah begitu nomor tembus 4 digit ('JRN/2026/08/9999' lebih besar
--      daripada 'JRN/2026/08/10050' secara teks). Itulah sumber 186.200 nomor
--      kembar sepanjang 2026.
--   2. Baca-MAX-lalu-tambah-satu tidak aman terhadap race. INSERT .. ON
--      CONFLICT DO UPDATE mengunci baris counter, jadi penomoran
--      terserialisasi antara aplikasi dan scheduler.
--
-- URUTAN PENTING: seed harus diambil dari data yang SUDAH direnumber, dan
-- tabel ini harus ADA sebelum kode baru di-deploy. Kalau dibalik, penomoran
-- akan error atau bentrok lagi.
-- ============================================================================

CREATE TABLE IF NOT EXISTS public.mst_journal_counter (
    prefix  text   PRIMARY KEY,
    last_no bigint NOT NULL
);

\echo '== 3a. Seed dari data terkini. Idempoten - aman diulang. ==============='
-- GREATEST menjaga counter tidak pernah mundur kalau dijalankan ulang.
INSERT INTO public.mst_journal_counter (prefix, last_no)
SELECT SUBSTRING(journal_no FROM 1 FOR 12),
       MAX(SUBSTRING(journal_no FROM 13)::bigint)
FROM public.trx_transaction
WHERE journal_no ~ '^JRN/[0-9]{4}/[0-9]{2}/[0-9]+$'
GROUP BY 1
ON CONFLICT (prefix) DO UPDATE
    SET last_no = GREATEST(public.mst_journal_counter.last_no, EXCLUDED.last_no);


\echo '== 3b. Isi counter - last_no harus >= max_no tiap periode =============='
SELECT c.prefix,
       c.last_no,
       m.max_no,
       CASE WHEN c.last_no >= m.max_no THEN 'OK' ELSE 'BAHAYA - counter di bawah data' END AS status
FROM public.mst_journal_counter c
LEFT JOIN (
    SELECT SUBSTRING(journal_no FROM 1 FOR 12)    AS prefix,
           MAX(SUBSTRING(journal_no FROM 13)::bigint) AS max_no
    FROM public.trx_transaction
    WHERE journal_no ~ '^JRN/[0-9]{4}/[0-9]{2}/[0-9]+$'
    GROUP BY 1
) m ON m.prefix = c.prefix
ORDER BY c.prefix;


\echo '== 3c. Simulasi: nomor berikutnya yang akan diberikan kode baru ========'
-- Hanya membaca. Nomor sebenarnya baru dialokasikan saat kode dijalankan.
SELECT prefix, last_no + 1 AS nomor_berikutnya
FROM public.mst_journal_counter
ORDER BY prefix;
