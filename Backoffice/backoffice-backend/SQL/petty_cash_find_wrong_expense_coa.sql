-- ============================================================
-- Cari SEMUA settlement petty cash yang akun bebannya SALAH:
-- expense_coa_code diarahkan ke COA petty cash unit itu sendiri
-- (jurnal jadi Dr Petty Cash / Cr Piutang, bukan Dr Beban).
-- Catatan schema: entity memakai public.mst_petty_cash_unit.
-- Kalau error "relation not found", ganti ke master.mst_petty_cash_unit.
-- ============================================================


-- ------------------------------------------------------------
-- A. Daftar detail settlement yg expense_coa_code = COA unit-nya sendiri
-- ------------------------------------------------------------
SELECT
    u.branch_id,
    u.unit_code,
    u.unit_name,
    u.coa_code              AS coa_petty_cash,
    s.settlement_no,
    s.settlement_date::date AS tgl,
    s.status,
    d.expense_coa_code,
    d.expense_coa_name,
    d.description,
    d.amount
FROM public.trx_petty_cash_settlement_detail d
JOIN public.trx_petty_cash_settlement s ON s.settlement_id = d.settlement_id
JOIN public.mst_petty_cash_unit u        ON u.unit_id      = s.unit_id
WHERE d.expense_coa_code = u.coa_code
ORDER BY u.branch_id, u.unit_code, s.settlement_date;


-- ------------------------------------------------------------
-- B. Ringkasan dampak per unit (jumlah settlement & total nilai salah)
-- ------------------------------------------------------------
SELECT
    u.branch_id,
    u.unit_code,
    u.unit_name,
    u.coa_code                        AS coa_petty_cash,
    COUNT(DISTINCT s.settlement_id)   AS jml_settlement,
    COUNT(*)                          AS jml_baris_detail,
    SUM(d.amount)                     AS total_nilai_salah
FROM public.trx_petty_cash_settlement_detail d
JOIN public.trx_petty_cash_settlement s ON s.settlement_id = d.settlement_id
JOIN public.mst_petty_cash_unit u        ON u.unit_id      = s.unit_id
WHERE d.expense_coa_code = u.coa_code
GROUP BY u.branch_id, u.unit_code, u.unit_name, u.coa_code
ORDER BY total_nilai_salah DESC;


-- ------------------------------------------------------------
-- C. Jaring lebih lebar: expense_coa_code = COA KAS/BANK apa pun (is_cash_bank),
--    bukan hanya COA unit sendiri (mis. kepilih kas/bank/petty cash unit lain).
-- ------------------------------------------------------------
SELECT
    u.branch_id,
    u.unit_code,
    u.unit_name,
    u.coa_code              AS coa_petty_cash,
    s.settlement_no,
    s.settlement_date::date AS tgl,
    s.status,
    d.expense_coa_code,
    d.expense_coa_name,
    c.is_cash_bank,
    d.amount
FROM public.trx_petty_cash_settlement_detail d
JOIN public.trx_petty_cash_settlement s ON s.settlement_id = d.settlement_id
JOIN public.mst_petty_cash_unit u        ON u.unit_id      = s.unit_id
JOIN master.mst_coa c                    ON c.coa_code = d.expense_coa_code AND c.branch_id = u.branch_id
WHERE c.is_cash_bank = TRUE
ORDER BY u.branch_id, u.unit_code, s.settlement_date;


-- ------------------------------------------------------------
-- D. Cross-check dari JURNAL (independen dari detail):
--    baris DEBIT settlement yang mendarat di COA petty cash unit.
-- ------------------------------------------------------------
SELECT
    j.branch_id,
    u.unit_code,
    u.unit_name,
    t.transaction_ref       AS settlement_no,
    t.transaction_date::date AS tgl,
    j.coa_code,
    j.journal_desc,
    j.debit_amount
FROM public.trx_journal j
JOIN public.trx_transaction t     ON t.transaction_id = j.transaction_id
JOIN public.mst_petty_cash_unit u ON u.coa_code = j.coa_code AND u.branch_id = j.branch_id
WHERE j.source = 'PettyCashSettlement'
  AND j.status = 'Published'
  AND j.debit_amount > 0
ORDER BY j.branch_id, u.unit_code, t.transaction_date;
