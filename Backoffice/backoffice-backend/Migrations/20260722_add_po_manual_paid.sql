-- Migration: Mark-as-Paid manual untuk Purchase Order
-- Date: 2026-07-22
--
-- Konteks:
--   Halaman /account-payable/purchase-order menampilkan PO yang MASIH outstanding.
--   Sebuah PO disembunyikan dari list & hasil download bila:
--     (a) sudah dibayar via Account Payable (trx_invoice_exchange.payment_status = 'Paid'),
--     (b) sudah ada jurnal Persediaan Obat: trx_journal.coa_code = '204099'
--         DAN trx_journal.thb_po_number = thb_purchase_order.order_number, atau
--     (c) ditandai manual "Mark as Paid" (kolom baru di bawah) -- untuk PO yang dibayar
--         lewat jalur lain yang tidak bisa ditrack sistem.
--
-- Migrasi ini menambah kolom pendukung (c) + index pendukung (a) & (b).
-- Idempotent & aman dijalankan ulang.

-- 1) Kolom Mark-as-Paid pada thb_purchase_order --------------------------------
ALTER TABLE public.thb_purchase_order
    ADD COLUMN IF NOT EXISTS manual_paid      BOOLEAN     NOT NULL DEFAULT FALSE,
    ADD COLUMN IF NOT EXISTS manual_paid_note TEXT        DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS manual_paid_by   INTEGER     DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS manual_paid_at   TIMESTAMP   DEFAULT NULL;

COMMENT ON COLUMN public.thb_purchase_order.manual_paid      IS 'TRUE bila PO ditandai lunas manual (dibayar lewat jalur di luar account-payable/pembayaran). Menyembunyikan PO dari list & download.';
COMMENT ON COLUMN public.thb_purchase_order.manual_paid_note IS 'Catatan wajib saat Mark as Paid (alasan / referensi pembayaran).';
COMMENT ON COLUMN public.thb_purchase_order.manual_paid_by   IS 'user_id yang melakukan Mark as Paid (mst_users.user_id).';
COMMENT ON COLUMN public.thb_purchase_order.manual_paid_at   IS 'Waktu (UTC) Mark as Paid dilakukan.';

-- 2) Index pendukung filter penyembunyian --------------------------------------
-- (b) subquery EXISTS di trx_journal: WHERE coa_code = '204099' AND thb_po_number = <order_number>
CREATE INDEX IF NOT EXISTS idx_trx_journal_coa_po
    ON public.trx_journal (coa_code, thb_po_number);

-- (a) subquery EXISTS di trx_invoice_exchange: WHERE po_number = <order_number> AND payment_status = 'Paid'
CREATE INDEX IF NOT EXISTS idx_invoice_exchange_po_paystatus
    ON public.trx_invoice_exchange (po_number, payment_status);

-- Verifikasi:
--   \d+ public.thb_purchase_order
--   SELECT indexname, indexdef FROM pg_indexes WHERE tablename IN ('trx_journal','trx_invoice_exchange');
