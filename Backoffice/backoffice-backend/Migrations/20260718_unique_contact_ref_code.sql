-- Migration: Enforce uniqueness of master.mst_contact.ref_code
-- Date: 2026-07-18
-- Purpose: ref_code has always been expected to be unique -- ContactService.Add:181 rejects a
--          duplicate -- but nothing in the database enforced it. That check is also read-then-
--          insert with no constraint behind it, so two concurrent inserts can both pass it.
--          The THB journal import now generates ref_code for the contacts it auto-creates, so
--          the guarantee needs to be real rather than hoped for.
--
-- RUN THIS SEPARATELY from 20260718_create_journal_import_job.sql, and only after the
-- pre-check below comes back empty. It is deliberately not bundled with the queue migration:
-- the import feature works without it, and this one can legitimately fail on existing data.
--
-- ============================================================
-- STEP 1 -- PRE-CHECK. Run this FIRST, on its own.
-- ============================================================
-- Existing rows may already hold duplicate ref_codes. If this returns any row, the index in
-- STEP 2 will fail. Resolve those duplicates by hand first -- they are real master-data
-- records and which one to keep or re-code is a business decision, not something a migration
-- should guess:
--
--   SELECT ref_code, count(*) AS jumlah, array_agg(contact_id) AS contact_ids,
--          array_agg(contact_name) AS nama
--     FROM master.mst_contact
--    WHERE ref_code IS NOT NULL AND btrim(ref_code) <> ''
--    GROUP BY ref_code
--   HAVING count(*) > 1
--    ORDER BY jumlah DESC;
--
-- ============================================================
-- STEP 2 -- The index. Run once STEP 1 is empty.
-- ============================================================
-- Partial: ref_code is nullable and most existing contacts have none. A plain UNIQUE would
-- treat NULLs as distinct in Postgres anyway, but the partial form also skips blank strings,
-- which are not a meaningful code.
CREATE UNIQUE INDEX IF NOT EXISTS uidx_contact_ref_code
    ON master.mst_contact(ref_code)
    WHERE ref_code IS NOT NULL AND btrim(ref_code) <> '';

COMMENT ON INDEX master.uidx_contact_ref_code IS 'ref_code must be unique across all contacts. Enforced here because ContactService.Add only read-checks it, which races.';
