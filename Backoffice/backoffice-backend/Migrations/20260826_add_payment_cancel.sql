-- ============================================================================
-- Fitur Batal Pembayaran AP (per invoice & per batch).
--
-- Pembatalan TIDAK menghapus data: jurnal pembayaran asli tetap ada, dibuatkan
-- jurnal pembalik (transaction_type = 'PaymentCancel'), dan invoice dikembalikan
-- ke Unpaid/Partial. Kolom di bawah menyimpan jejaknya.
--
-- Idempoten, aman dijalankan ulang.
-- ============================================================================

ALTER TABLE public.trx_invoice_exchange
    ADD COLUMN IF NOT EXISTS payment_cancelled_by          integer,
    ADD COLUMN IF NOT EXISTS payment_cancelled_date        timestamp with time zone,
    ADD COLUMN IF NOT EXISTS payment_cancel_reason         character varying(500),
    ADD COLUMN IF NOT EXISTS payment_cancel_transaction_id character varying(50);

-- Pembatalan batch mencari invoice lewat payment_ref; tanpa index ini jadi seq scan
-- atas seluruh tabel untuk tiap pembatalan.
CREATE INDEX IF NOT EXISTS idx_invoice_exchange_payment_ref
    ON public.trx_invoice_exchange (payment_ref);

SELECT column_name, data_type
FROM information_schema.columns
WHERE table_schema = 'public' AND table_name = 'trx_invoice_exchange'
  AND column_name LIKE 'payment_cancel%'
ORDER BY column_name;
