-- ============================================================
-- Rekonsiliasi Buku Kas Kecil (Ledger) vs Jurnal
-- Tujuan:
--   1) Pastikan halaman ledger (baru, berbasis jurnal) = data jurnal
--   2) Diagnosa settlement yang men-DEBIT penuh ke COA petty cash
--      (indikasi baris expense salah ter-posting ke COA petty cash)
--
-- GANTI dua nilai ini sesuai unit yang dicek:
--   COA    = '101004002'   (Patty Cash Kasir)
--   BRANCH = 1
-- ============================================================


-- ------------------------------------------------------------
-- Q0. Info unit + saldo tersimpan + checkpoint saldo
-- ------------------------------------------------------------
SELECT unit_id, unit_code, unit_name, coa_code, branch_id, current_balance
FROM public.mst_petty_cash_unit
WHERE coa_code = '101004002' AND branch_id = 1;

-- Checkpoint saldo (kalau ada). Saldo = closing_balance terakhir + mutasi jurnal setelah period_date.
-- Kalau kosong, saldo murni = SUM(debit) - SUM(credit) seluruh jurnal (lihat Q2 baris TOTAL).
SELECT period_date, opening_balance, debit_amount, credit_amount, closing_balance, created_date
FROM public.trx_balance
WHERE coa_code = '101004002' AND branch_id = 1
ORDER BY period_date DESC, created_date DESC
LIMIT 5;


-- ------------------------------------------------------------
-- Q1. BUKU BESAR COA petty cash = persis yang tampil di halaman ledger baru
--     (debit = MASUK, credit = KELUAR)  +  penanda ASAL (dalam/luar sistem)
-- ------------------------------------------------------------
SELECT
    t.transaction_date::date  AS tanggal,
    j.journal_ref             AS no_transaksi,
    t.journal_no              AS no_jurnal,
    j.source,
    CASE
      WHEN j.source IS NULL
        OR j.source NOT IN ('PettyCashTopUp','PettyCashRequest','PettyCashSettlement')
      THEN 'LUAR SISTEM'
      ELSE 'petty cash'
    END                       AS asal,
    j.journal_desc            AS keterangan,
    j.debit_amount            AS masuk,
    j.credit_amount           AS keluar
FROM public.trx_journal j
JOIN public.trx_transaction t ON t.transaction_id = j.transaction_id
WHERE j.coa_code = '101004002'
  AND j.branch_id = 1
  AND j.status = 'Published'
ORDER BY t.transaction_date, j.journal_id;


-- ------------------------------------------------------------
-- Q2. Ringkasan per SUMBER + baris TOTAL (ROLLUP)
--     Net (TOTAL) harus = Net Flow di halaman = Saldo (bila tanpa checkpoint)
-- ------------------------------------------------------------
SELECT
    COALESCE(j.source, '(kosong)')                AS sumber,
    COUNT(*)                                      AS jml_baris,
    SUM(j.debit_amount)                           AS total_masuk,
    SUM(j.credit_amount)                          AS total_keluar,
    SUM(j.debit_amount) - SUM(j.credit_amount)    AS net
FROM public.trx_journal j
JOIN public.trx_transaction t ON t.transaction_id = j.transaction_id
WHERE j.coa_code = '101004002'
  AND j.branch_id = 1
  AND j.status = 'Published'
GROUP BY ROLLUP (j.source)
ORDER BY sumber NULLS LAST;


-- ------------------------------------------------------------
-- Q3. DETAIL LENGKAP jurnal tiap SETTLEMENT (SEMUA COA per transaksi)
--     Untuk lihat pasangan double-entry: apakah baris beban masuk ke
--     COA beban (5xxxx) atau justru ke COA petty cash (101004002)?
-- ------------------------------------------------------------
SELECT
    t.transaction_ref  AS no_settlement,
    t.transaction_date::date AS tanggal,
    t.journal_no,
    j.coa_code,
    j.coa_name,
    j.journal_desc,
    j.debit_amount     AS debit,
    j.credit_amount    AS kredit
FROM public.trx_transaction t
JOIN public.trx_journal j ON j.transaction_id = t.transaction_id
WHERE t.transaction_id IN (
    SELECT DISTINCT j2.transaction_id
    FROM public.trx_journal j2
    WHERE j2.source = 'PettyCashSettlement'
      AND j2.coa_code = '101004002'
      AND j2.branch_id = 1
)
ORDER BY t.transaction_date, t.transaction_ref, j.journal_id;


-- ------------------------------------------------------------
-- Q4. Data ENTITY settlement + request terkait (posting yang SEHARUSNYA)
--     selisih_vs_approved > 0 => kurang bayar ; return_amount > 0 => lebih bayar
-- ------------------------------------------------------------
SELECT
    s.settlement_no,
    s.settlement_date::date AS tgl,
    s.status                AS settlement_status,
    s.request_id,
    s.total_amount,
    s.return_amount,
    r.request_no,
    r.amount                AS request_amount,
    r.approved_amount,
    (s.total_amount - COALESCE(r.approved_amount, r.amount)) AS selisih_vs_approved
FROM public.trx_petty_cash_settlement s
LEFT JOIN public.trx_petty_cash_request r ON r.request_id = s.request_id
WHERE s.unit_id = (SELECT unit_id FROM public.mst_petty_cash_unit
                   WHERE coa_code = '101004002' AND branch_id = 1 LIMIT 1)
ORDER BY s.settlement_date;


-- ------------------------------------------------------------
-- Q5. Rincian beban settlement (COA beban yang benar menurut input)
-- ------------------------------------------------------------
SELECT
    s.settlement_no,
    d.expense_coa_code,
    d.expense_coa_name,
    d.description,
    d.amount
FROM public.trx_petty_cash_settlement s
JOIN public.trx_petty_cash_settlement_detail d ON d.settlement_id = s.settlement_id
WHERE s.unit_id = (SELECT unit_id FROM public.mst_petty_cash_unit
                   WHERE coa_code = '101004002' AND branch_id = 1 LIMIT 1)
ORDER BY s.settlement_no;


-- ------------------------------------------------------------
-- Q6. Baris jurnal LUAR SISTEM di COA petty cash
--     (jurnal manual / scheduler / modul lain — bukan modul petty cash)
-- ------------------------------------------------------------
SELECT
    t.transaction_date::date AS tanggal,
    t.transaction_no,
    t.journal_no,
    j.source,
    j.journal_desc,
    j.debit_amount  AS masuk,
    j.credit_amount AS keluar,
    j.created_by,
    j.created_date
FROM public.trx_journal j
JOIN public.trx_transaction t ON t.transaction_id = j.transaction_id
WHERE j.coa_code = '101004002'
  AND j.branch_id = 1
  AND j.status = 'Published'
  AND (j.source IS NULL
       OR j.source NOT IN ('PettyCashTopUp','PettyCashRequest','PettyCashSettlement'))
ORDER BY t.transaction_date;


-- ------------------------------------------------------------
-- Q7. BUKTI (kalau ada) settlement men-DEBIT COA petty cash penuh
--     dibandingkan COA beban yang seharusnya dari detail.
--     Kalau coa_baris_jurnal = 101004002 tapi coa_beban_seharusnya = 5xxxx
--     dgn keterangan/nilai sama => beban salah ter-posting ke petty cash.
-- ------------------------------------------------------------
SELECT
    t.transaction_ref  AS no_settlement,
    j.coa_code         AS coa_baris_jurnal,
    j.debit_amount     AS debit_ke_petty_cash,
    j.journal_desc,
    d.expense_coa_code AS coa_beban_seharusnya,
    d.expense_coa_name,
    d.amount           AS nilai_beban_detail
FROM public.trx_journal j
JOIN public.trx_transaction t ON t.transaction_id = j.transaction_id
JOIN public.trx_petty_cash_settlement s ON s.transaction_id = j.transaction_id
LEFT JOIN public.trx_petty_cash_settlement_detail d ON d.settlement_id = s.settlement_id
WHERE j.source = 'PettyCashSettlement'
  AND j.coa_code = '101004002'
  AND j.branch_id = 1
  AND j.debit_amount > 0
ORDER BY t.transaction_date;
