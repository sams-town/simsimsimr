-- ============================================================================
-- Fitur Batal Settlement Petty Cash.
--
-- Menambah jejak pembatalan di trx_petty_cash_settlement. Pembatalan TIDAK
-- menghapus baris apa pun: settlement lama tetap tersimpan dengan status
-- 'Cancelled', jurnal aslinya tetap ada, dan dibuatkan jurnal pembalik
-- (transaction_type = 'PettyCashSettlementCancel') yang tercatat di
-- cancel_transaction_id. Request terkait dikembalikan ke status 'Posted'
-- supaya bisa di-settle ulang.
--
-- Idempoten, aman dijalankan ulang.
-- ============================================================================

ALTER TABLE public.trx_petty_cash_settlement
    ADD COLUMN IF NOT EXISTS cancelled_by          integer,
    ADD COLUMN IF NOT EXISTS cancelled_date        timestamp without time zone,
    ADD COLUMN IF NOT EXISTS cancel_reason         character varying(500),
    ADD COLUMN IF NOT EXISTS cancel_transaction_id character varying(50);

-- Untuk mencari settlement yang dibatalkan tanpa seq scan.
CREATE INDEX IF NOT EXISTS idx_pc_settlement_status
    ON public.trx_petty_cash_settlement (status);

-- Verifikasi
SELECT column_name, data_type
FROM information_schema.columns
WHERE table_schema = 'public' AND table_name = 'trx_petty_cash_settlement'
  AND column_name IN ('cancelled_by','cancelled_date','cancel_reason','cancel_transaction_id')
ORDER BY column_name;
