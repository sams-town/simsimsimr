-- Migration: Backfill logs.audit_logs from existing audit columns
-- Date: 2026-07-17
-- Purpose: Give records created before the activity log existed a baseline timeline.
--          Every row is written with status='Backfill' so it is distinguishable from a
--          live event -- the UI badges these as "Data lama".
--
-- PREREQUISITE: run 20260717_extend_audit_logs_timeline.sql first (before_data/after_data
--               must already be jsonb).
--
-- SAFE TO RE-RUN: every statement excludes rows already backfilled for the same
--                 (module, ref_id, operation). Live events are never touched.
--
-- WHAT THIS CANNOT RECOVER: actions whose audit columns were nulled on cancel
--                           (trx_medical_fee.posting_by, trx_invoice_exchange.exchange_by).
--                           Those events are gone from the database; only actions taken
--                           from now on are captured for them.

BEGIN;

-- Helper predicate used throughout: skip rows we already have.
-- (Written inline per statement rather than as a function to keep this file self-contained.)

-- =====================================================
-- Jasa Medis (trx_medical_fee) -> ref_id = medical_fee_id
-- =====================================================
INSERT INTO logs.audit_logs (module, ref_id, operation, after_data, user_id, branch_id, status, created_at)
SELECT 'Jasa Medis', m.medical_fee_id::text, 'Dibuat',
       jsonb_build_object('DoctorName', m.doctor_name, 'PatientName', m.patient_name, 'TotalFee', m.total_fee),
       m.created_by, m.branch_id, 'Backfill', m.created_date
FROM public.trx_medical_fee m
WHERE m.created_date IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM logs.audit_logs l
                  WHERE l.module = 'Jasa Medis' AND l.ref_id = m.medical_fee_id::text AND l.operation = 'Dibuat');

INSERT INTO logs.audit_logs (module, ref_id, operation, after_data, user_id, branch_id, status, created_at)
SELECT 'Jasa Medis', m.medical_fee_id::text, 'Posting',
       jsonb_build_object('BatchNumber', m.batch_number),
       m.posting_by, m.branch_id, 'Backfill', m.posting_date
FROM public.trx_medical_fee m
WHERE m.posting_date IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM logs.audit_logs l
                  WHERE l.module = 'Jasa Medis' AND l.ref_id = m.medical_fee_id::text AND l.operation = 'Posting');

INSERT INTO logs.audit_logs (module, ref_id, operation, after_data, user_id, branch_id, status, created_at)
SELECT 'Jasa Medis', m.medical_fee_id::text, 'Generate Voucher',
       jsonb_build_object('PaymentVoucher', m.payment_voucher, 'DoctorSharePercent', m.doctor_share),
       m.voucher_by, m.branch_id, 'Backfill', m.voucher_date
FROM public.trx_medical_fee m
WHERE m.voucher_date IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM logs.audit_logs l
                  WHERE l.module = 'Jasa Medis' AND l.ref_id = m.medical_fee_id::text AND l.operation = 'Generate Voucher');

-- No payment_by column on this table -- updated_by is the closest actor on record.
INSERT INTO logs.audit_logs (module, ref_id, operation, after_data, user_id, branch_id, status, created_at)
SELECT 'Jasa Medis', m.medical_fee_id::text, 'Pembayaran',
       jsonb_build_object('PaymentRef', m.payment_ref, 'PaymentCoaCode', m.payment_coa_code, 'PaidAmount', m.paid_amount),
       m.updated_by, m.branch_id, 'Backfill', m.payment_date
FROM public.trx_medical_fee m
WHERE m.payment_date IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM logs.audit_logs l
                  WHERE l.module = 'Jasa Medis' AND l.ref_id = m.medical_fee_id::text AND l.operation = 'Pembayaran');

INSERT INTO logs.audit_logs (module, ref_id, operation, after_data, user_id, branch_id, status, created_at)
SELECT 'Jasa Medis', m.medical_fee_id::text, 'Adjustment BPJS',
       jsonb_build_object('AmountAfter', m.adjustment_amount, 'TotalFee', m.total_fee),
       m.updated_by, m.branch_id, 'Backfill', m.adjustment_date
FROM public.trx_medical_fee m
WHERE m.adjustment_date IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM logs.audit_logs l
                  WHERE l.module = 'Jasa Medis' AND l.ref_id = m.medical_fee_id::text AND l.operation = 'Adjustment BPJS');

-- =====================================================
-- Piutang AR (trx_account_receivable) -> ref_id = ar_id
-- =====================================================
INSERT INTO logs.audit_logs (module, ref_id, operation, after_data, user_id, branch_id, status, created_at)
SELECT 'Piutang AR', a.ar_id::text, 'Dibuat',
       jsonb_build_object('ArNumber', a.ar_number, 'PayerName', a.payer_name, 'TotalAmount', a.total_amount),
       a.created_by, a.branch_id, 'Backfill', a.created_date
FROM public.trx_account_receivable a
WHERE a.created_date IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM logs.audit_logs l
                  WHERE l.module = 'Piutang AR' AND l.ref_id = a.ar_id::text AND l.operation = 'Dibuat');

-- payment_by exists but ProcessPayment never populated it; fall back to updated_by.
INSERT INTO logs.audit_logs (module, ref_id, operation, after_data, user_id, branch_id, status, created_at)
SELECT 'Piutang AR', a.ar_id::text, 'Pembayaran',
       jsonb_build_object('ArNumber', a.ar_number, 'PaymentRef', a.payment_ref,
                          'PaymentCoaCode', a.payment_coa_code, 'PaidAmount', a.paid_amount),
       COALESCE(a.payment_by, a.updated_by), a.branch_id, 'Backfill', a.payment_date
FROM public.trx_account_receivable a
WHERE a.payment_date IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM logs.audit_logs l
                  WHERE l.module = 'Piutang AR' AND l.ref_id = a.ar_id::text AND l.operation = 'Pembayaran');

INSERT INTO logs.audit_logs (module, ref_id, operation, after_data, user_id, branch_id, status, created_at)
SELECT 'Piutang AR', a.ar_id::text, 'Finalize Voucher',
       jsonb_build_object('VoucherNumber', a.voucher_number, 'ArNumber', a.ar_number, 'DueDate', a.due_date),
       a.updated_by, a.branch_id, 'Backfill', a.voucher_date
FROM public.trx_account_receivable a
WHERE a.voucher_date IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM logs.audit_logs l
                  WHERE l.module = 'Piutang AR' AND l.ref_id = a.ar_id::text AND l.operation = 'Finalize Voucher');

INSERT INTO logs.audit_logs (module, ref_id, operation, after_data, user_id, branch_id, status, created_at)
SELECT 'Piutang AR', a.ar_id::text, 'Rekonsiliasi',
       jsonb_build_object('ReconcileStatus', a.reconcile_status, 'ReconcileBpjsAmount', a.reconcile_bpjs_amount,
                          'ReconcileDifference', a.reconcile_difference),
       a.updated_by, a.branch_id, 'Backfill', a.reconcile_date
FROM public.trx_account_receivable a
WHERE a.reconcile_date IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM logs.audit_logs l
                  WHERE l.module = 'Piutang AR' AND l.ref_id = a.ar_id::text AND l.operation = 'Rekonsiliasi');

-- =====================================================
-- Tukar Faktur (trx_invoice_exchange) -> ref_id = invoice_exchange_id
-- =====================================================
INSERT INTO logs.audit_logs (module, ref_id, operation, after_data, user_id, branch_id, status, created_at)
SELECT 'Tukar Faktur', i.invoice_exchange_id::text, 'Dibuat',
       jsonb_build_object('InvoiceNumber', i.invoice_number, 'SupplierName', i.supplier_name,
                          'TotalTransaction', i.total_transaction),
       NULL, NULL, 'Backfill', i.inserted_date
FROM public.trx_invoice_exchange i
WHERE i.inserted_date IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM logs.audit_logs l
                  WHERE l.module = 'Tukar Faktur' AND l.ref_id = i.invoice_exchange_id::text AND l.operation = 'Dibuat');

INSERT INTO logs.audit_logs (module, ref_id, operation, after_data, user_id, branch_id, status, created_at)
SELECT 'Tukar Faktur', i.invoice_exchange_id::text, 'Tukar Faktur',
       jsonb_build_object('BatchNumber', i.batch_number, 'InvoiceNumber', i.invoice_number,
                          'SupplierName', i.supplier_name, 'DueDate', i.due_date),
       i.exchange_by, NULL, 'Backfill', i.exchange_date
FROM public.trx_invoice_exchange i
WHERE i.exchange_date IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM logs.audit_logs l
                  WHERE l.module = 'Tukar Faktur' AND l.ref_id = i.invoice_exchange_id::text AND l.operation = 'Tukar Faktur');

INSERT INTO logs.audit_logs (module, ref_id, operation, after_data, user_id, branch_id, status, created_at)
SELECT 'Tukar Faktur', i.invoice_exchange_id::text, 'Pembayaran',
       jsonb_build_object('InvoiceNumber', i.invoice_number, 'PaymentRef', i.payment_ref,
                          'PaidAmount', i.paid_amount, 'PaymentStatus', i.payment_status),
       COALESCE(i.payment_by, i.updated_by), NULL, 'Backfill', i.payment_date
FROM public.trx_invoice_exchange i
WHERE i.payment_date IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM logs.audit_logs l
                  WHERE l.module = 'Tukar Faktur' AND l.ref_id = i.invoice_exchange_id::text AND l.operation = 'Pembayaran');

-- =====================================================
-- Petty Cash Request (trx_petty_cash_request) -> ref_id = request_id
-- =====================================================
INSERT INTO logs.audit_logs (module, ref_id, operation, after_data, user_id, branch_id, status, created_at)
SELECT 'Petty Cash Request', r.request_id::text, 'Dibuat',
       jsonb_build_object('RequestNo', r.request_no, 'RequesterName', r.requester_name,
                          'Amount', r.amount, 'Purpose', r.purpose),
       r.created_by, r.branch_id, 'Backfill', r.created_date
FROM public.trx_petty_cash_request r
WHERE r.created_date IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM logs.audit_logs l
                  WHERE l.module = 'Petty Cash Request' AND l.ref_id = r.request_id::text AND l.operation = 'Dibuat');

INSERT INTO logs.audit_logs (module, ref_id, operation, after_data, user_id, branch_id, status, created_at)
SELECT 'Petty Cash Request', r.request_id::text, 'Disetujui',
       jsonb_build_object('RequestNo', r.request_no, 'RequestedAmount', r.amount, 'ApprovedAmount', r.approved_amount),
       r.approved_by, r.branch_id, 'Backfill', r.approved_date
FROM public.trx_petty_cash_request r
WHERE r.approved_date IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM logs.audit_logs l
                  WHERE l.module = 'Petty Cash Request' AND l.ref_id = r.request_id::text AND l.operation = 'Disetujui');

-- Rejections have no rejected_by/rejected_date on this table -- only a reason. The actor
-- and the moment are unrecoverable; updated_date is the best available approximation.
INSERT INTO logs.audit_logs (module, ref_id, operation, after_data, user_id, branch_id, status, created_at)
SELECT 'Petty Cash Request', r.request_id::text, 'Ditolak',
       jsonb_build_object('RequestNo', r.request_no, 'Amount', r.amount, 'Reason', r.rejected_reason,
                          'Note', 'Aktor & waktu penolakan tidak tercatat di data lama; memakai updated_date.'),
       r.updated_by, r.branch_id, 'Backfill', r.updated_date
FROM public.trx_petty_cash_request r
WHERE r.status = 'Rejected'
  AND NOT EXISTS (SELECT 1 FROM logs.audit_logs l
                  WHERE l.module = 'Petty Cash Request' AND l.ref_id = r.request_id::text AND l.operation = 'Ditolak');

-- =====================================================
-- Petty Cash TopUp (trx_petty_cash_topup) -> ref_id = topup_id
-- =====================================================
INSERT INTO logs.audit_logs (module, ref_id, operation, after_data, user_id, branch_id, status, created_at)
SELECT 'Petty Cash TopUp', t.topup_id::text, 'Dibuat',
       jsonb_build_object('TopUpNo', t.topup_no, 'Amount', t.amount, 'FromCoaCode', t.from_coa_code),
       t.created_by, t.branch_id, 'Backfill', t.created_date
FROM public.trx_petty_cash_topup t
WHERE t.created_date IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM logs.audit_logs l
                  WHERE l.module = 'Petty Cash TopUp' AND l.ref_id = t.topup_id::text AND l.operation = 'Dibuat');

INSERT INTO logs.audit_logs (module, ref_id, operation, after_data, user_id, branch_id, status, created_at)
SELECT 'Petty Cash TopUp', t.topup_id::text, 'Disetujui',
       jsonb_build_object('TopUpNo', t.topup_no, 'RequestedAmount', t.amount,
                          'ApprovedAmount', t.approved_amount, 'Notes', t.approved_notes),
       t.approved_by, t.branch_id, 'Backfill', t.approved_date
FROM public.trx_petty_cash_topup t
WHERE t.approved_date IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM logs.audit_logs l
                  WHERE l.module = 'Petty Cash TopUp' AND l.ref_id = t.topup_id::text AND l.operation = 'Disetujui');

INSERT INTO logs.audit_logs (module, ref_id, operation, after_data, user_id, branch_id, status, created_at)
SELECT 'Petty Cash TopUp', t.topup_id::text, 'Ditolak',
       jsonb_build_object('TopUpNo', t.topup_no, 'Amount', t.amount, 'Reason', t.rejected_reason),
       t.rejected_by, t.branch_id, 'Backfill', t.rejected_date
FROM public.trx_petty_cash_topup t
WHERE t.rejected_date IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM logs.audit_logs l
                  WHERE l.module = 'Petty Cash TopUp' AND l.ref_id = t.topup_id::text AND l.operation = 'Ditolak');

-- =====================================================
-- Petty Cash Settlement (trx_petty_cash_settlement) -> ref_id = settlement_id
-- =====================================================
INSERT INTO logs.audit_logs (module, ref_id, operation, after_data, user_id, branch_id, status, created_at)
SELECT 'Petty Cash Settlement', s.settlement_id::text, 'Dibuat',
       jsonb_build_object('SettlementNo', s.settlement_no, 'TotalAmount', s.total_amount, 'RequestId', s.request_id),
       s.created_by, s.branch_id, 'Backfill', s.created_date
FROM public.trx_petty_cash_settlement s
WHERE s.created_date IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM logs.audit_logs l
                  WHERE l.module = 'Petty Cash Settlement' AND l.ref_id = s.settlement_id::text AND l.operation = 'Dibuat');

-- =====================================================
-- Journal (trx_journal) -> ref_id = journal_id
-- =====================================================
INSERT INTO logs.audit_logs (module, ref_id, operation, after_data, user_id, branch_id, status, created_at)
SELECT 'Journal', j.journal_id::text, 'Dibuat',
       jsonb_build_object('JournalDesc', j.journal_desc, 'CoaCode', j.coa_code,
                          'DebitAmount', j.debit_amount, 'CreditAmount', j.credit_amount, 'Source', j.source),
       j.created_by, j.branch_id, 'Backfill', j.created_date
FROM public.trx_journal j
WHERE j.created_date IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM logs.audit_logs l
                  WHERE l.module = 'Journal' AND l.ref_id = j.journal_id::text AND l.operation = 'Dibuat');

INSERT INTO logs.audit_logs (module, ref_id, operation, after_data, user_id, branch_id, status, created_at)
SELECT 'Journal', j.journal_id::text, 'Rekonsiliasi',
       jsonb_build_object('CoaCode', j.coa_code, 'JournalRef', j.journal_ref),
       j.reconciled_by, j.branch_id, 'Backfill', j.reconciled_date
FROM public.trx_journal j
WHERE j.reconciled_date IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM logs.audit_logs l
                  WHERE l.module = 'Journal' AND l.ref_id = j.journal_id::text AND l.operation = 'Rekonsiliasi');

-- =====================================================
-- Transaction (trx_transaction) -> ref_id = transaction_id
-- =====================================================
-- NOTE: TransactionController already writes live rows under module='Transaction' with
-- ref_id = transaction_id, so the NOT EXISTS guard also prevents duplicating those.
INSERT INTO logs.audit_logs (module, ref_id, operation, after_data, user_id, branch_id, status, created_at)
SELECT 'Transaction', t.transaction_id, 'Dibuat',
       jsonb_build_object('TransactionNo', t.transaction_no, 'TransactionType', t.transaction_type,
                          'Description', t.description, 'Amount', t.amount),
       t.created_by, t.branch_id, 'Backfill', t.created_date
FROM public.trx_transaction t
WHERE t.created_date IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM logs.audit_logs l
                  WHERE l.module = 'Transaction' AND l.ref_id = t.transaction_id AND l.operation = 'Dibuat');

INSERT INTO logs.audit_logs (module, ref_id, operation, after_data, user_id, branch_id, status, created_at)
SELECT 'Transaction', t.transaction_id, 'Posting',
       jsonb_build_object('TransactionNo', t.transaction_no, 'JournalNo', t.journal_no),
       t.updated_by, t.branch_id, 'Backfill', t.posted_date
FROM public.trx_transaction t
WHERE t.posted_date IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM logs.audit_logs l
                  WHERE l.module = 'Transaction' AND l.ref_id = t.transaction_id AND l.operation = 'Posting');

-- =====================================================
-- Piutang AR JSONB histories -> one log row per history entry
-- =====================================================
-- These three columns are append-only arrays that already carry an actor and a reason per
-- entry, so they map onto the timeline almost 1:1 and are richer than the scalar columns.
--
-- TIMESTAMPS: the writers use DateTime.Now (not UtcNow) and store a naive
-- "yyyy-MM-dd HH:mm:ss" string, while every sibling column is UTC. No offset is applied
-- here because the app containers set no TZ (see Dockerfile/docker-compose -- none define
-- TZ), so DateTime.Now == DateTime.UtcNow in every deployed environment. If any of this
-- data was instead produced by a backend run on a developer machine set to WIB, those
-- specific entries will read 7 hours early. Verify before trusting the oldest rows:
--
--   SELECT a.ar_id, e->>'changedDate' AS json_ts, a.updated_date AS column_ts_utc
--     FROM public.trx_account_receivable a,
--          LATERAL jsonb_array_elements(a.payer_change_history::jsonb) e
--    WHERE a.payer_change_history IS NOT NULL
--    ORDER BY a.updated_date DESC LIMIT 5;
--
-- On the newest row the two should agree to within seconds. If json_ts is ~7h behind,
-- add `- interval '7 hours'` to the created_at expressions in this section.

-- payer_change_history: [{payerBefore:{id,name}, payerAfter:{id,name}, reason, changedBy, changedDate}]
INSERT INTO logs.audit_logs (module, ref_id, operation, after_data, user_id, branch_id, status, created_at)
SELECT 'Piutang AR', a.ar_id::text, 'Ganti Payer',
       jsonb_build_object('PayerBefore', e->'payerBefore', 'PayerAfter', e->'payerAfter', 'Reason', e->>'reason'),
       (e->>'changedBy')::int, a.branch_id, 'Backfill',
       (e->>'changedDate')::timestamp
FROM public.trx_account_receivable a,
     LATERAL jsonb_array_elements(a.payer_change_history::jsonb) e
WHERE a.payer_change_history IS NOT NULL
  AND jsonb_typeof(a.payer_change_history::jsonb) = 'array'
  AND e->>'changedDate' IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM logs.audit_logs l
                  WHERE l.module = 'Piutang AR' AND l.ref_id = a.ar_id::text
                    AND l.operation = 'Ganti Payer'
                    AND l.created_at = (e->>'changedDate')::timestamp);

-- adjustment_history: [{amountBefore, amountAfter, totalAmount, reason, changedBy, changedDate}]
INSERT INTO logs.audit_logs (module, ref_id, operation, after_data, user_id, branch_id, status, created_at)
SELECT 'Piutang AR', a.ar_id::text, 'Adjustment',
       jsonb_build_object('AmountBefore', e->'amountBefore', 'AmountAfter', e->'amountAfter',
                          'TotalAmount', e->'totalAmount', 'Reason', e->>'reason'),
       (e->>'changedBy')::int, a.branch_id, 'Backfill',
       (e->>'changedDate')::timestamp
FROM public.trx_account_receivable a,
     LATERAL jsonb_array_elements(a.adjustment_history::jsonb) e
WHERE a.adjustment_history IS NOT NULL
  AND jsonb_typeof(a.adjustment_history::jsonb) = 'array'
  AND e->>'changedDate' IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM logs.audit_logs l
                  WHERE l.module = 'Piutang AR' AND l.ref_id = a.ar_id::text
                    AND l.operation = 'Adjustment'
                    AND l.created_at = (e->>'changedDate')::timestamp);

-- note_history: [{note, createdBy, createdDate}]
INSERT INTO logs.audit_logs (module, ref_id, operation, after_data, user_id, branch_id, status, created_at)
SELECT 'Piutang AR', a.ar_id::text, 'Tambah Catatan',
       jsonb_build_object('Note', e->>'note'),
       (e->>'createdBy')::int, a.branch_id, 'Backfill',
       (e->>'createdDate')::timestamp
FROM public.trx_account_receivable a,
     LATERAL jsonb_array_elements(a.note_history::jsonb) e
WHERE a.note_history IS NOT NULL
  AND jsonb_typeof(a.note_history::jsonb) = 'array'
  AND e->>'createdDate' IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM logs.audit_logs l
                  WHERE l.module = 'Piutang AR' AND l.ref_id = a.ar_id::text
                    AND l.operation = 'Tambah Catatan'
                    AND l.created_at = (e->>'createdDate')::timestamp);

COMMIT;

-- =====================================================
-- Verification
-- =====================================================
-- Backfilled rows per module/operation:
--   SELECT module, operation, count(*) FROM logs.audit_logs
--    WHERE status = 'Backfill' GROUP BY 1, 2 ORDER BY 1, 2;
--
-- Spot-check a couple against the source, e.g.:
--   SELECT count(*) FROM public.trx_medical_fee WHERE posting_date IS NOT NULL;
--   SELECT count(*) FROM logs.audit_logs WHERE module='Jasa Medis' AND operation='Posting' AND status='Backfill';
--
-- To undo this entire migration (live events are untouched):
--   DELETE FROM logs.audit_logs WHERE status = 'Backfill';
