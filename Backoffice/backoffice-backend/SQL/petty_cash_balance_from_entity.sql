-- ============================================================
-- Saldo Unit Petty Cash dari ENTITY TABLES saja (tanpa journal)
-- COA 101004001 / Petty Cash Induk
-- ============================================================
-- Formula:
--   Calc Balance = TopUp(+) - Request(-) + Return(+) - Shortfall(-) - Standalone(-)
--   Diff = DB Stored Balance - Calc Balance
--   Jika Diff ≠ 0, ada transaksi di luar entity tables (direct journal)
-- ============================================================

WITH unit AS (
    SELECT unit_id, unit_code, unit_name, coa_code, current_balance
    FROM master.mst_petty_cash_unit
    WHERE coa_code = '101004001'
),
topup_in AS (
    SELECT 
        unit_id,
        SUM(CASE WHEN COALESCE(approved_amount, 0) > 0 THEN approved_amount ELSE amount END) AS total
    FROM public.trx_petty_cash_topup
    WHERE status = 'Posted'
    GROUP BY unit_id
),
request_out AS (
    SELECT 
        unit_id,
        SUM(COALESCE(approved_amount, amount)) AS total
    FROM public.trx_petty_cash_request
    WHERE status IN ('Posted', 'Settled')
    GROUP BY unit_id
),
settlement_in AS (
    SELECT 
        unit_id,
        SUM(COALESCE(return_amount, 0)) AS total_return
    FROM public.trx_petty_cash_settlement
    WHERE status = 'Posted'
    GROUP BY unit_id
),
shortfall_out AS (
    SELECT 
        s.unit_id,
        SUM(s.total_amount - COALESCE(r.approved_amount, r.amount)) AS total
    FROM public.trx_petty_cash_settlement s
    JOIN public.trx_petty_cash_request r ON s.request_id = r.request_id
    WHERE s.status = 'Posted'
      AND s.total_amount > COALESCE(r.approved_amount, r.amount)
    GROUP BY s.unit_id
),
standalone_out AS (
    SELECT 
        unit_id,
        SUM(total_amount - COALESCE(return_amount, 0)) AS total
    FROM public.trx_petty_cash_settlement
    WHERE status = 'Posted'
      AND request_id IS NULL
    GROUP BY unit_id
)
SELECT 
    u.unit_code,
    u.unit_name,
    u.coa_code,
    u.current_balance                    AS "DB Stored Balance",
    COALESCE(ti.total, 0)                AS "TopUp (+)",
    COALESCE(ro.total, 0)                AS "Request (-)",
    COALESCE(si.total_return, 0)         AS "Return (+)",
    COALESCE(sf.total, 0)                AS "Shortfall (-)",
    COALESCE(so.total, 0)                AS "Standalone (-)",
    COALESCE(ti.total, 0) 
      - COALESCE(ro.total, 0) 
      + COALESCE(si.total_return, 0) 
      - COALESCE(sf.total, 0) 
      - COALESCE(so.total, 0)            AS "Calc Balance (Entity)",
    u.current_balance - (
        COALESCE(ti.total, 0) 
        - COALESCE(ro.total, 0) 
        + COALESCE(si.total_return, 0) 
        - COALESCE(sf.total, 0) 
        - COALESCE(so.total, 0)
    )                                     AS "Diff (DB - Calc)"
FROM unit u
LEFT JOIN topup_in ti    ON u.unit_id = ti.unit_id
LEFT JOIN request_out ro ON u.unit_id = ro.unit_id
LEFT JOIN settlement_in si ON u.unit_id = si.unit_id
LEFT JOIN shortfall_out sf ON u.unit_id = sf.unit_id
LEFT JOIN standalone_out so ON u.unit_id = so.unit_id;
