-- Migration: Add parent_code column to mst_coa
-- Run this BEFORE deploying the new backend

-- 1. Add parent_code column
ALTER TABLE master.mst_coa ADD COLUMN IF NOT EXISTS parent_code VARCHAR(50) NULL;

-- 2. Populate parent_code from existing parent_id data
UPDATE master.mst_coa c SET parent_code = p.coa_code
FROM master.mst_coa p WHERE c.parent_id = p.coa_id AND c.parent_id IS NOT NULL;
