-- Migration: Propagasi Mark-as-Paid PO ke sisi invoice (Account Payable)
-- Date: 2026-07-27
--
-- Konteks:
--   Sebelumnya "Mark as Paid" di /account-payable/purchase-order hanya menulis
--   flag di thb_purchase_order (lihat 20260722_add_po_manual_paid.sql), sehingga
--   invoice PO yang sama masih tampil Unpaid di:
--     /account-payable/tukar-faktur, /daftar-ap, dan /pembayaran
--   -- ketiganya membaca tabel yang sama: public.trx_invoice_exchange.
--
--   Mulai sekarang Mark as Paid ikut melunasi baris trx_invoice_exchange yang
--   po_number-nya cocok. Kolom di bawah adalah PEMBEDA antara pelunasan lewat
--   Mark as Paid dan pembayaran normal lewat menu Pembayaran:
--
--     Mark as Paid      -> manual_paid = TRUE, payment_ref/payment_coa_* tetap NULL,
--                          tidak ada jurnal, tidak ada payment_batch_number.
--     Menu Pembayaran   -> manual_paid = FALSE, payment_ref & payment_coa_* terisi,
--                          ada jurnal (Debit Utang Supplier / Credit Kas-Bank).
--
--   CATATAN AKUNTANSI: Mark as Paid sengaja TIDAK memposting jurnal, karena
--   pembayarannya terjadi di luar sistem dan umumnya sudah tercatat dari sumber
--   lain (mis. import Khanza lewat COA 204099). Konsekuensinya saldo Utang
--   Supplier di buku besar tidak ikut berkurang -- selisih ini disengaja.
--
-- Idempotent & aman dijalankan ulang.

-- 0) Timezone eksplisit --------------------------------------------------------
-- manual_paid_at bertipe `timestamp` (tanpa zona, mengikuti thb_purchase_order),
-- sedangkan payment_date bertipe timestamptz. Backfill di STEP 3 menyalin antar
-- keduanya, jadi konversinya mengikuti TimeZone session. Aplikasi menulis UTC,
-- jadi kunci ke UTC supaya tidak ada pergeseran jam.
SET TimeZone = 'UTC';

-- 1) Kolom pembeda Mark-as-Paid pada trx_invoice_exchange ----------------------
ALTER TABLE public.trx_invoice_exchange
    ADD COLUMN IF NOT EXISTS manual_paid      BOOLEAN     NOT NULL DEFAULT FALSE,
    ADD COLUMN IF NOT EXISTS manual_paid_note TEXT        DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS manual_paid_by   INTEGER     DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS manual_paid_at   TIMESTAMP   DEFAULT NULL;

COMMENT ON COLUMN public.trx_invoice_exchange.manual_paid      IS 'TRUE bila baris ini dilunasi lewat "Mark as Paid" di halaman Purchase Order, bukan lewat menu Pembayaran. Pembeda utama di list & detail AP.';
COMMENT ON COLUMN public.trx_invoice_exchange.manual_paid_note IS 'Catatan wajib yang diisi saat Mark as Paid (disalin dari thb_purchase_order.manual_paid_note).';
COMMENT ON COLUMN public.trx_invoice_exchange.manual_paid_by   IS 'user_id yang melakukan Mark as Paid (mst_users.user_id).';
COMMENT ON COLUMN public.trx_invoice_exchange.manual_paid_at   IS 'Waktu (UTC) Mark as Paid dilakukan.';

-- 2) Index pendukung ------------------------------------------------------------
-- MarkAsPaid/UnmarkAsPaid mengambil seluruh baris invoice milik satu PO.
CREATE INDEX IF NOT EXISTS idx_invoice_exchange_po_number
    ON public.trx_invoice_exchange (po_number);

-- Filter "hanya yang ditandai manual" pada list AP & Purchase Order.
CREATE INDEX IF NOT EXISTS idx_invoice_exchange_manual_paid
    ON public.trx_invoice_exchange (manual_paid)
    WHERE manual_paid = TRUE;

CREATE INDEX IF NOT EXISTS idx_purchase_order_manual_paid
    ON public.thb_purchase_order (manual_paid)
    WHERE manual_paid = TRUE;

-- 3) Backfill data lama ---------------------------------------------------------
-- PO yang SUDAH ditandai Paid sebelum migrasi ini belum menyentuh sisi invoice.
-- Baris invoice-nya dilunasi di sini dengan aturan yang sama seperti runtime:
-- hanya yang belum pernah dibayar lewat menu Pembayaran (payment_status kosong /
-- 'unpaid' DAN paid_amount kosong/0) yang ikut, supaya riwayat pembayaran asli
-- tidak pernah tertimpa.
--
-- Preview dulu sebelum menjalankan UPDATE di bawahnya:
SELECT po.order_number,
       po.manual_paid_at,
       count(ie.invoice_exchange_id) AS baris_invoice,
       count(*) FILTER (WHERE lower(coalesce(ie.payment_status, 'unpaid')) <> 'unpaid'
                           OR coalesce(ie.paid_amount, 0) <> 0) AS baris_dilewati
FROM public.thb_purchase_order po
JOIN public.trx_invoice_exchange ie ON ie.po_number = po.order_number
WHERE po.manual_paid = TRUE
GROUP BY po.order_number, po.manual_paid_at
ORDER BY po.manual_paid_at;

UPDATE public.trx_invoice_exchange ie
SET manual_paid        = TRUE,
    manual_paid_note   = po.manual_paid_note,
    manual_paid_by     = po.manual_paid_by,
    manual_paid_at     = po.manual_paid_at,
    payment_status     = 'Paid',
    paid_amount        = greatest(coalesce(ie.total_transaction, 0)
                                  - coalesce(ie.discount_amount, 0)
                                  - coalesce(ie.pph23_amount, 0), 0),
    outstanding_amount = 0,
    payment_date       = po.manual_paid_at,
    updated_by         = po.manual_paid_by,
    updated_date       = now() AT TIME ZONE 'UTC'
FROM public.thb_purchase_order po
WHERE ie.po_number = po.order_number
  AND po.manual_paid = TRUE
  AND ie.manual_paid = FALSE
  AND lower(coalesce(ie.payment_status, 'unpaid')) = 'unpaid'
  AND coalesce(ie.paid_amount, 0) = 0;

-- Verifikasi:
--   \d+ public.trx_invoice_exchange
--   SELECT manual_paid, payment_status, count(*)
--   FROM public.trx_invoice_exchange GROUP BY 1, 2 ORDER BY 1, 2;
