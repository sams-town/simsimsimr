-- ============================================================
-- KOREKSI 3 settlement yang salah COA beban (unit Patty Cash Kasir)
-- Masalah: expense_coa_code = '101004002' (COA petty cash) →
--          jurnal jadi Dr Petty Cash / Cr Piutang (bukan Dr Beban).
-- Solusi:  pindahkan baris DEBIT dari 101004002 ke COA beban yang benar,
--          lalu sinkronkan detail settlement. (Sisi Cr Piutang 104003 SUDAH benar.)
--
-- ISI dulu placeholder ini:
--   :BEBAN_BENSIN   = COA beban BBM/kendaraan (mis. '50xxxxxxx')
--   :BEBAN_TRANSFUSI= COA beban medis/laboratorium (mis. '50xxxxxxx')
--   branch_id       = 1
--
-- LANGKAH 0 (WAJIB cek dulu): checkpoint saldo. Kalau ada baris trx_balance untuk
--   COA 101004002 / COA beban dengan period_date >= 2026-07-01, closing_balance-nya
--   jadi basi setelah koreksi → lihat LANGKAH 3.
-- ============================================================

BEGIN;   -- verifikasi dulu dengan Q1/Q2 sebelum COMMIT; kalau ragu: ROLLBACK;

-- ------------------------------------------------------------
-- LANGKAH 1 — Bensin: PCS-20260723-0002 & PCS-20260729-0002
-- ------------------------------------------------------------
UPDATE public.trx_journal j
SET coa_code        = c.coa_code,
    coa_name        = c.coa_name,
    coa_parent_code = c.parent_code,
    coa_parent_name = pc.coa_name
FROM master.mst_coa c
LEFT JOIN master.mst_coa pc ON pc.coa_code = c.parent_code AND pc.branch_id = c.branch_id
WHERE c.coa_code = ':BEBAN_BENSIN' AND c.branch_id = 1
  AND j.source = 'PettyCashSettlement'
  AND j.coa_code = '101004002'
  AND j.debit_amount > 0
  AND j.transaction_id IN (
      SELECT transaction_id FROM public.trx_petty_cash_settlement
      WHERE settlement_no IN ('PCS-20260723-0002','PCS-20260729-0002')
  );

UPDATE public.trx_petty_cash_settlement_detail d
SET expense_coa_code = ':BEBAN_BENSIN',
    expense_coa_name = (SELECT coa_name FROM master.mst_coa WHERE coa_code = ':BEBAN_BENSIN' AND branch_id = 1)
FROM public.trx_petty_cash_settlement s
WHERE s.settlement_id = d.settlement_id
  AND s.settlement_no IN ('PCS-20260723-0002','PCS-20260729-0002')
  AND d.expense_coa_code = '101004002';

-- ------------------------------------------------------------
-- LANGKAH 2 — Transfusi: PCS-20260804-0002
-- ------------------------------------------------------------
UPDATE public.trx_journal j
SET coa_code        = c.coa_code,
    coa_name        = c.coa_name,
    coa_parent_code = c.parent_code,
    coa_parent_name = pc.coa_name
FROM master.mst_coa c
LEFT JOIN master.mst_coa pc ON pc.coa_code = c.parent_code AND pc.branch_id = c.branch_id
WHERE c.coa_code = ':BEBAN_TRANSFUSI' AND c.branch_id = 1
  AND j.source = 'PettyCashSettlement'
  AND j.coa_code = '101004002'
  AND j.debit_amount > 0
  AND j.transaction_id IN (
      SELECT transaction_id FROM public.trx_petty_cash_settlement
      WHERE settlement_no = 'PCS-20260804-0002'
  );

UPDATE public.trx_petty_cash_settlement_detail d
SET expense_coa_code = ':BEBAN_TRANSFUSI',
    expense_coa_name = (SELECT coa_name FROM master.mst_coa WHERE coa_code = ':BEBAN_TRANSFUSI' AND branch_id = 1)
FROM public.trx_petty_cash_settlement s
WHERE s.settlement_id = d.settlement_id
  AND s.settlement_no = 'PCS-20260804-0002'
  AND d.expense_coa_code = '101004002';

-- ------------------------------------------------------------
-- LANGKAH 3 — Checkpoint saldo (jalankan HANYA jika LANGKAH 0 menemukan checkpoint basi)
-- Hapus checkpoint COA terdampak agar saldo dihitung ulang penuh dari jurnal (on-read).
-- ------------------------------------------------------------
-- DELETE FROM public.trx_balance
-- WHERE branch_id = 1
--   AND coa_code IN ('101004002', ':BEBAN_BENSIN', ':BEBAN_TRANSFUSI')
--   AND period_date >= DATE '2026-07-01';

-- ------------------------------------------------------------
-- VERIFIKASI (sebelum COMMIT): jalankan Q2 dari petty_cash_ledger_reconcile.sql.
-- Harapan: net TOTAL COA 101004002 turun jadi 1.798.500, PettyCashSettlement net = 0.
-- ------------------------------------------------------------

-- COMMIT;   -- buka kalau hasil verifikasi sudah benar
-- ROLLBACK; -- kalau ada yang meleset
