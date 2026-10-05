-- ============================================================================
-- Tambah kolom PPN di trx_invoice_exchange.
--
-- Dipakai oleh Input Manual Tukar Faktur (Non-PO). Untuk invoice via PO, PPN
-- tetap dibaca dari jurnal PO (COA ACCOUNT_PPN_MASUKAN) seperti sebelumnya -
-- kolom ini hanya terisi untuk invoice non-PO yang diinput manual.
--
-- Idempoten, aman dijalankan ulang.
-- ============================================================================

ALTER TABLE public.trx_invoice_exchange
    ADD COLUMN IF NOT EXISTS ppn_percent double precision DEFAULT 0,
    ADD COLUMN IF NOT EXISTS ppn_amount  double precision DEFAULT 0;

-- Baris lama: samakan dengan default supaya tidak ada NULL yang menjalar ke
-- perhitungan outstanding.
UPDATE public.trx_invoice_exchange
   SET ppn_percent = COALESCE(ppn_percent, 0),
       ppn_amount  = COALESCE(ppn_amount, 0)
 WHERE ppn_percent IS NULL OR ppn_amount IS NULL;

-- Verifikasi
SELECT column_name, data_type, column_default
FROM information_schema.columns
WHERE table_schema = 'public' AND table_name = 'trx_invoice_exchange'
  AND column_name IN ('ppn_percent','ppn_amount','pph23_percent','pph23_amount')
ORDER BY column_name;
