-- ============================================================
-- DATABASE INDEX OPTIMIZATION
-- Date: 2026-02-24
-- Purpose: Add missing indexes to improve query performance
-- ============================================================

-- ============================================================
-- 1. trx_journal (649K rows) - Tabel transaksi terbesar
-- ============================================================

-- Status filter (dipakai di hampir semua query list journal)
CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_trx_journal_status
ON public.trx_journal (status);

-- payer_code (dipakai untuk special cash flow query COA 401101004)
CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_trx_journal_payer_code
ON public.trx_journal (payer_code);

-- coa_parent_code (dipakai untuk ledger/report grouping)
CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_trx_journal_coa_parent_code
ON public.trx_journal (coa_parent_code);

-- costcenter_id (FK, dipakai join di report)
CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_trx_journal_costcenter_id
ON public.trx_journal (costcenter_id);

-- Composite: status + coa_code (filter status + coa di report)
CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_trx_journal_status_coa
ON public.trx_journal (status, coa_code);

-- ============================================================
-- 2. trx_transaction (146K rows) - Tabel parent transaksi
-- ============================================================

-- Status filter (dipakai semua list: Pending, Published, Deleted, Void)
CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_trx_transaction_status
ON public.trx_transaction (status);

-- transaction_type (filter: JournalEntry, Deposit, Payment, Transfer, dll)
CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_trx_transaction_type
ON public.trx_transaction (transaction_type);

-- journal_no (search by journal number)
CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_trx_transaction_journal_no
ON public.trx_transaction (journal_no);

-- transaction_no (search by transaction number)
CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_trx_transaction_no
ON public.trx_transaction (transaction_no);

-- created_date (sorting default di list)
CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_trx_transaction_created_date
ON public.trx_transaction (created_date DESC);

-- contact_id (FK, join ke mst_contact)
CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_trx_transaction_contact_id
ON public.trx_transaction (contact_id);

-- Composite: status + transaction_type + branch_id (common filter combo)
CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_trx_transaction_status_type_branch
ON public.trx_transaction (status, transaction_type, branch_id);

-- Composite: status + transaction_date (filter list by status + date range)
CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_trx_transaction_status_date
ON public.trx_transaction (status, transaction_date);

-- ============================================================
-- 3. mst_contact (205K rows) - Hanya 16.7% index usage!
-- ============================================================

-- branch_id (filter per cabang)
CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_mst_contact_branch_id
ON master.mst_contact (branch_id);

-- status (filter Active/InActive)
CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_mst_contact_status
ON master.mst_contact (status);

-- contact_name (search by name, text pattern support)
CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_mst_contact_name
ON master.mst_contact (contact_name varchar_pattern_ops);

-- contact_category_id (FK, filter by category)
CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_mst_contact_category_id
ON master.mst_contact (contact_category_id);

-- ref_code (lookup by reference code from external system)
CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_mst_contact_ref_code
ON master.mst_contact (ref_code);

-- ============================================================
-- 4. trx_balance (2.2M rows) - Tabel terbesar
-- ============================================================

-- branch_id (filter per cabang)
CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_trx_balance_branch_id
ON public.trx_balance (branch_id);

-- Composite: branch_id + coa_code + period_date (common query combo)
CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_trx_balance_branch_coa_period
ON public.trx_balance (branch_id, coa_code, period_date DESC);

-- ============================================================
-- 5. trx_invoice_exchange (5K rows) - 0.5% index usage!
-- ============================================================

-- status
CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_trx_invoice_exchange_status
ON public.trx_invoice_exchange (status);

-- NOTE: branch_id dan contact_id tidak ada di tabel ini

-- ============================================================
-- 6. trx_medical_fee (72K rows) - 25.5% index usage
-- ============================================================

-- branch_id
CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_trx_medical_fee_branch_id
ON public.trx_medical_fee (branch_id);

-- NOTE: transaction_id tidak ada di tabel ini

-- ============================================================
-- 7. mst_coa_mapping (baru, akan digunakan CRUD)
-- ============================================================

-- coa_source UNIQUE (1 source -> 1 dest)
CREATE UNIQUE INDEX CONCURRENTLY IF NOT EXISTS idx_mst_coa_mapping_source_unique
ON master.mst_coa_mapping (coa_source);

-- coa_dest (lookup semua source yang map ke dest tertentu)
CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_mst_coa_mapping_dest
ON master.mst_coa_mapping (coa_dest);

-- ============================================================
-- 8. thb_purchase_order (6.7K rows) - No indexes
-- ============================================================

-- status (filter PO by status)
CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_thb_purchase_order_status
ON public.thb_purchase_order (status);

-- date (filter/sort by date)
CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_thb_purchase_order_date
ON public.thb_purchase_order (date);

-- supplier_code (lookup by supplier)
CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_thb_purchase_order_supplier
ON public.thb_purchase_order (supplier_code);

-- ============================================================
-- 9. thb_detail_order (13.8K rows) - No indexes besides PK
-- ============================================================

-- no_invoice (FK ke thb_purchase_order.order_number)
CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_thb_detail_order_invoice
ON public.thb_detail_order (no_invoice);

-- ============================================================
-- 10. mst_cost_center (183 rows) - No indexes
-- ============================================================

-- branch_id (filter per cabang)
CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_mst_cost_center_branch_id
ON master.mst_cost_center (branch_id);

-- ============================================================
-- 11. cfg_options - lookup by name
-- ============================================================

-- option_name (lookup config by name)
CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_cfg_options_name
ON public.cfg_options (option_name);

-- ============================================================
-- ANALYZE setelah index dibuat (update statistics)
-- ============================================================

ANALYZE public.trx_journal;
ANALYZE public.trx_transaction;
ANALYZE master.mst_contact;
ANALYZE public.trx_balance;
ANALYZE public.trx_invoice_exchange;
ANALYZE public.trx_medical_fee;
ANALYZE master.mst_coa_mapping;
ANALYZE public.thb_purchase_order;
ANALYZE public.thb_detail_order;
