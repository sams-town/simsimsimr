-- 1. Remove duplicates, keeping the one with the generated ID (lowest or highest, doesn't matter much if data is same)
-- Using CTID (physical location) to distinguish rows is a common PostgreSQL pattern for removing duplicates

DELETE FROM master.mst_role_page_access a
USING master.mst_role_page_access b
WHERE a.ctid < b.ctid
  AND a.role_id = b.role_id
  AND a.page_code = b.page_code;

-- 2. Add Unique Constraint to prevent future duplicates
ALTER TABLE master.mst_role_page_access
ADD CONSTRAINT result_page_access_unique UNIQUE (role_id, page_code);
