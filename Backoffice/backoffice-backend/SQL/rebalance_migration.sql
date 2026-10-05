-- ============================================================================
-- RE-BALANCE MIGRATION  ——  SIAP DIJALANKAN, JANGAN dieksekusi tanpa validasi.
-- ============================================================================
-- Tujuan: membangun ulang saldo SEMUA COA (bukan cuma petty cash) dengan model
--   Saldo(COA, branch, asOf) = saldo_awal(@cutoff) + Σ mutasi jurnal (tgl transaksi > cutoff .. asOf)
--   tanda mengikuti normal_position COA.
--
-- PRASYARAT (urut):
--   1) SQL/fix_coa_normal_position.sql sudah dijalankan (normal_position benar).
--   2) SQL/retire_balance_triggers.sql sudah dijalankan (trigger incremental mati,
--      supaya tidak menulis snapshot per-transaksi yang akan double-count).
--   3) Deploy kode JournalService.GetEndingBalance versi ON-READ (checkpoint + mutasi).
--      (Migrasi ini menyiapkan datanya; pembacaan saldo memakai kode tsb.)
--
-- SIFAT: idempotent — aman diulang (membangun ulang dari nol setiap kali).
-- DRY-RUN: ganti COMMIT di akhir dengan ROLLBACK untuk melihat hasil tanpa menyimpan.
-- ============================================================================

BEGIN;

-- ----------------------------------------------------------------------------
-- PARAMETER: cutoff_date = tanggal "AS OF" saldo awal.
--   Mutasi dihitung dari jurnal dgn transaction_date STRICTLY > cutoff_date.
--   * Semua saldo dari jurnal (mis. petty cash) -> cutoff jauh di masa lalu, opening 0.
--   * Ada saldo awal migrasi (mis. bank)        -> cutoff = tgl cut-off go-live,
--                                                  isi _opening_balance per akhir cutoff.
-- ----------------------------------------------------------------------a------
CREATE TEMP TABLE _params (cutoff_date TIMESTAMP) ON COMMIT DROP;
INSERT INTO _params (cutoff_date) VALUES ('2026-01-01 00:00:00');   -- << GANTI bila perlu

-- ----------------------------------------------------------------------------
-- SALDO AWAL (opening) per (COA, branch) @ cutoff_date. Diisi akuntan.
-- Akun yang saldonya MURNI dari jurnal TIDAK perlu baris di sini (opening = 0).
-- ----------------------------------------------------------------------------
CREATE TEMP TABLE _opening_balance (
    coa_code  VARCHAR NOT NULL,
    branch_id INT     NOT NULL,
    opening   DOUBLE PRECISION NOT NULL DEFAULT 0,
    PRIMARY KEY (coa_code, branch_id)
) ON COMMIT DROP;

-- >>> ISI saldo awal di sini bila ada. Contoh:
-- INSERT INTO _opening_balance (coa_code, branch_id, opening) VALUES
--   ('101003001', 1, 50000000),   -- BSI Giro
--   ('101001001', 1, 10000000);   -- Kas Pusat
-- (Petty cash: kosongkan -> opening 0, saldo penuh dari jurnal.)

-- ----------------------------------------------------------------------------
-- (A) Hapus snapshot lama (korup, buatan trigger lama).
--     Untuk rollout BERTAHAP, batasi: ... WHERE coa_code IN ('101004','101004001',...);
-- ----------------------------------------------------------------------------
DELETE FROM public.trx_balance;   -- << seluruh COA; ganti dengan filter bila bertahap

-- ----------------------------------------------------------------------------
-- (B) Tulis 1 checkpoint pembuka per (COA, branch) di cutoff_date (closing = opening).
--     Hanya untuk COA yang punya opening != 0. (opening 0 -> cukup dihitung dari jurnal)
-- ----------------------------------------------------------------------------
INSERT INTO public.trx_balance
    (coa_code, period_date, branch_id, opening_balance, debit_amount, credit_amount, closing_balance, created_date, updated_date)
SELECT ob.coa_code, p.cutoff_date, ob.branch_id, ob.opening, 0, 0, ob.opening,
       NOW() AT TIME ZONE 'UTC', NOW() AT TIME ZONE 'UTC'
FROM _opening_balance ob CROSS JOIN _params p
WHERE ob.opening <> 0;

-- ----------------------------------------------------------------------------
-- (C) Set mst_coa.balance (cache "saldo terkini global") = opening + Σ mutasi jurnal (> cutoff).
--     Tanda mengikuti normal_position (HARUS sudah benar). Catatan: ini agregat
--     lintas-branch (cache). Saldo per-branch dihitung GetEndingBalance dari jurnal.
-- ----------------------------------------------------------------------------
WITH mvt AS (
    SELECT j.coa_code,
           SUM(COALESCE(j.debit_amount, 0))  AS debit,
           SUM(COALESCE(j.credit_amount, 0)) AS credit
    FROM public.trx_journal j
    JOIN public.trx_transaction t ON t.transaction_id = j.transaction_id
    CROSS JOIN _params p
    WHERE j.status <> 'Deleted'
      AND t.transaction_date > p.cutoff_date
    GROUP BY j.coa_code
),
calc AS (
    SELECT c.coa_code,
           COALESCE((SELECT SUM(opening) FROM _opening_balance ob WHERE ob.coa_code = c.coa_code), 0)
           + CASE
               WHEN UPPER(TRIM(COALESCE(c.normal_position, 'D'))) IN ('C','CREDIT','K','KREDIT')
               THEN COALESCE(m.credit, 0) - COALESCE(m.debit, 0)
               ELSE COALESCE(m.debit, 0)  - COALESCE(m.credit, 0)
             END AS new_balance
    FROM master.mst_coa c
    LEFT JOIN mvt m ON m.coa_code = c.coa_code
)
UPDATE master.mst_coa c
SET balance      = calc.new_balance,
    updated_date = NOW() AT TIME ZONE 'UTC'
FROM calc
WHERE calc.coa_code = c.coa_code;

-- ----------------------------------------------------------------------------
-- (D) VERIFIKASI sebelum COMMIT (contoh):
--   -- petty cash induk harus ≈ 2.904.941
--   SELECT coa_code, coa_name, normal_position, balance FROM master.mst_coa WHERE coa_code = '101004001';
--   -- neraca saldo: total debit-normal vs credit-normal (cek kewajaran)
--   SELECT
--     SUM(CASE WHEN UPPER(TRIM(COALESCE(normal_position,'D'))) IN ('C','CREDIT','K','KREDIT') THEN 0 ELSE balance END) AS total_debit_normal,
--     SUM(CASE WHEN UPPER(TRIM(COALESCE(normal_position,'D'))) IN ('C','CREDIT','K','KREDIT') THEN balance ELSE 0 END) AS total_credit_normal
--   FROM master.mst_coa;
-- ----------------------------------------------------------------------------

COMMIT;
-- ROLLBACK;  -- DRY-RUN: jalankan semua di atas lalu ROLLBACK untuk inspeksi tanpa menyimpan.

-- ============================================================================
-- (OPSIONAL, NANTI) Performa: jika volume jurnal besar, buat checkpoint AKHIR BULAN
-- via job batch — bukan trigger per-insert — supaya GetEndingBalance hanya menjumlah
-- mutasi bulan berjalan. Tidak wajib untuk benar; hanya optimasi.
-- ============================================================================
