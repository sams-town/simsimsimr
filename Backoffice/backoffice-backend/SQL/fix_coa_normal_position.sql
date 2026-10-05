-- ============================================================================
-- FIX normal_position COA aset/kas/bank yang salah ter-set 'C' (Credit)
-- Akun ASET (kode 1xx) & BEBAN (5xx/6xx) seharusnya 'D' (Debit).
-- Trigger/perhitungan saldo membaca normal_position untuk menentukan tanda:
--   Debit-normal  -> net = debit  - credit
--   Credit-normal -> net = credit - debit   (IN ('C','CREDIT','K','KREDIT'))
--
-- >>> JALANKAN SETELAH divalidasi akuntan. <<<
-- Cek kandidat yang salah lebih dulu:
--   SELECT coa_code, coa_name, normal_position FROM master.mst_coa
--   WHERE normal_position = 'C' AND (coa_code LIKE '1%' OR coa_code LIKE '5%' OR coa_code LIKE '6%')
--   ORDER BY coa_code;
-- HATI-HATI: KECUALIKAN kontra-aset yang SAH ber-'C', mis:
--   - Akumulasi Penyusutan (1xx)
--   - Penyisihan / Cadangan Kerugian Piutang (1xx)
-- ============================================================================

BEGIN;

-- Kas & Bank + Petty Cash yang terkonfirmasi salah (semua akun ASET => Debit):
UPDATE master.mst_coa
SET normal_position = 'D',
    updated_date    = NOW() AT TIME ZONE 'UTC'
WHERE normal_position = 'C'
  AND coa_code IN (
        -- Bank
        '101003005',  -- BCA
        '101003006',  -- OCBC
        '101003007',  -- BSI Gesek
        '101003008',  -- Bank Bukopin
        '101003009',  -- Bank 9
        '101003010',  -- Bank 10
        -- Petty Cash
        '101004',     -- Patty Cash (parent)
        '101004001',  -- Patty Cash Induk
        '101004002',  -- Patty Cash Kasir
        '101004003'   -- Patty Cash Rumah Duka
      );

-- ----------------------------------------------------------------------------
-- HASIL VALIDASI thd referensi/coa_thb_202606091105.csv (per 2026-06-09):
--   * Aset (1*) ber-'C' = 32 akun:
--       - 10 akun kas/bank SALAH  -> sudah masuk daftar UPDATE di atas.
--       - 22 akun kontra-aset BENAR ber-'C' (Akumulasi Penyusutan/Amortisasi:
--         141*, 142*, 143*, 144*) -> JANGAN diubah.
--   * Beban (5*/6*) ber-'C' = 1 akun: '501021' (STOK KELUAR BARANG DAPUR)
--     -> KONFIRMASI AKUNTAN dulu (kliring/offset yang sengaja kredit, atau salah?).
--        Bila memang seharusnya Debit, tambahkan ke daftar UPDATE:
--          -- '501021'
--   * Anomali lain (3->D x2, 4->D x3, 7 campur, 9 campur) kemungkinan SAH
--     (kontra-revenue/akun lain-lain). Tinjau bila perlu, tapi BUKAN bagian bug ini.
-- ----------------------------------------------------------------------------

-- Verifikasi sebelum commit:
--   SELECT coa_code, coa_name, normal_position FROM master.mst_coa
--   WHERE coa_code LIKE '10100%' ORDER BY coa_code;

COMMIT;
-- ROLLBACK;  -- gunakan untuk uji-coba tanpa menyimpan
