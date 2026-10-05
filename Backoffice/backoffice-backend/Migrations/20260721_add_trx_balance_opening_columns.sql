-- ============================================================================
-- Item 6 — Modul Saldo Awal (Opening Balance)
-- Tambah kolom di public.trx_balance untuk menandai & mencatat input saldo awal manual.
--   entry_type : 'Opening' (input manual saldo awal) | 'Checkpoint'/NULL (job/tutup buku)
--   note       : catatan input saldo awal
--   created_by : user yang menginput
--
-- Idempotent (IF NOT EXISTS). Aman dijalankan berulang.
-- Baris lama diperlakukan sebagai 'Checkpoint' (entry_type dibiarkan NULL = checkpoint).
-- ============================================================================

ALTER TABLE public.trx_balance
    ADD COLUMN IF NOT EXISTS entry_type varchar(20) NULL,
    ADD COLUMN IF NOT EXISTS note       text        NULL,
    ADD COLUMN IF NOT EXISTS created_by int         NULL;

-- Indeks bantu untuk listing saldo awal (filter entry_type = 'Opening').
CREATE INDEX IF NOT EXISTS idx_trx_balance_entry_type
    ON public.trx_balance (entry_type);
