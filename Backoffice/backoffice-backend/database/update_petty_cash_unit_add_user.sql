-- Add assigned_user_id column to mst_petty_cash_unit
ALTER TABLE "mst_petty_cash_unit" ADD COLUMN "assigned_user_id" INT NULL;

-- Add foreign key constraint
ALTER TABLE "mst_petty_cash_unit" 
ADD CONSTRAINT "FK_mst_petty_cash_unit_mst_users_assigned_user_id" 
FOREIGN KEY ("assigned_user_id") 
REFERENCES "master"."mst_users" ("user_id");

-- Add unique index to ensure 1-to-1 relationship
CREATE UNIQUE INDEX "IX_mst_petty_cash_unit_assigned_user_id" 
ON "mst_petty_cash_unit" ("assigned_user_id")
WHERE "assigned_user_id" IS NOT NULL;
