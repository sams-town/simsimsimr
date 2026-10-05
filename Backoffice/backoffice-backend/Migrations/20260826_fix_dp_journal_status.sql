-- ============================================================================
-- Koreksi status jurnal Down Payment yang tersimpan NULL.
--
-- Penyebab: PurchaseOrderService.AddDownPayment membuat Transaction dengan
-- status "Published" tetapi TIDAK menyetel status pada kedua baris jurnalnya.
-- Journal.status nullable tanpa default, jadi tersimpan NULL. Seluruh
-- perhitungan saldo menyaring status = 'Published', sehingga jurnal DP sama
-- sekali tidak masuk buku besar: bank tidak berkurang dan Uang Muka Pembelian
-- (107002) tidak muncul, padahal uangnya sudah keluar.
--
-- Kode sudah diperbaiki (status = transaction.status). Script ini membereskan
-- baris yang telanjur tersimpan.
--
-- Hanya menyentuh baris DOWN_PAYMENT ber-status NULL. Sudah diverifikasi bahwa
-- itu satu-satunya tipe transaksi yang terdampak.
-- ============================================================================

\echo '== 1. SEBELUM: baris yang akan diubah =================================='
SELECT t.transaction_type, t.status AS status_transaksi,
       j.coa_code, j.coa_name,
       COUNT(*) AS baris,
       SUM(j.debit_amount)::numeric(18,2)  AS debit,
       SUM(j.credit_amount)::numeric(18,2) AS kredit
FROM public.trx_journal j
JOIN public.trx_transaction t ON t.transaction_id = j.transaction_id
WHERE (j.status IS NULL OR j.status = '')
  AND t.transaction_type = 'DOWN_PAYMENT'
GROUP BY 1,2,3,4
ORDER BY 3;

\echo '== 2. Saldo 107002 SEBELUM (harusnya 0 baris terhitung) ================'
SELECT COUNT(*) AS baris_published,
       COALESCE(SUM(debit_amount - credit_amount),0)::numeric(18,2) AS saldo
FROM public.trx_journal
WHERE coa_code = '107002' AND status = 'Published';

\echo '== 3. KOREKSI - status jurnal disamakan dengan transaksi induknya ======'
-- Satu statement, atomik. Mengambil status dari transaksi induk (bukan
-- hardcode 'Published') supaya kalau ada DP yang sengaja Void/Pending, nilainya
-- tetap ikut induknya.
UPDATE public.trx_journal j
   SET status = t.status
  FROM public.trx_transaction t
 WHERE t.transaction_id = j.transaction_id
   AND t.transaction_type = 'DOWN_PAYMENT'
   AND (j.status IS NULL OR j.status = '')
   AND t.status IS NOT NULL;

\echo '== 4. SESUDAH: tidak boleh ada sisa NULL ==============================='
SELECT COUNT(*) AS sisa_null
FROM public.trx_journal j
JOIN public.trx_transaction t ON t.transaction_id = j.transaction_id
WHERE (j.status IS NULL OR j.status = '')
  AND t.transaction_type = 'DOWN_PAYMENT';

\echo '== 5. Saldo 107002 SESUDAH (harus muncul nilainya) ====================='
SELECT COUNT(*) AS baris_published,
       COALESCE(SUM(debit_amount - credit_amount),0)::numeric(18,2) AS saldo_uang_muka
FROM public.trx_journal
WHERE coa_code = '107002' AND status = 'Published';

\echo '== 6. Jurnal DP tetap seimbang ========================================='
SELECT t.journal_no,
       SUM(j.debit_amount)::numeric(18,2)  AS debit,
       SUM(j.credit_amount)::numeric(18,2) AS kredit,
       (SUM(j.debit_amount) - SUM(j.credit_amount))::numeric(18,2) AS selisih
FROM public.trx_journal j
JOIN public.trx_transaction t ON t.transaction_id = j.transaction_id
WHERE t.transaction_type = 'DOWN_PAYMENT'
GROUP BY 1 ORDER BY 1;
