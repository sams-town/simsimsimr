# Rencana Isolasi Branch (Multi-Cabang) — DOKUMEN RENCANA, BELUM DIKERJAKAN

> Status: **rencana saja**. Tidak ada perubahan kode yang dilakukan untuk item ini.
> Tujuan: memastikan seluruh modul terisolasi per-branch (tidak tercampur antar cabang)
> ketika multi-cabang benar-benar dipakai. Saat ini sistem praktis single-branch (on-prem,
> `branch_id = 1`), jadi ini disiapkan sebagai peta kerja, bukan eksekusi.

## 1. Kondisi saat ini (hasil audit kode)

**Sudah per-branch (benar):**
- Tabel inti punya `branch_id`: `trx_journal`, `trx_transaction`, `trx_balance`, `master.mst_coa`, `cfg_options`.
- `JournalService.GetEndingBalance(coaCode, endDate, branchId)` — filter `BranchId == branchId` ✅
- `JournalService.GenerateMonthlyCheckpoint(year, month, branchId)` — group per `(coa, branch)` ✅
- `OpeningBalanceService` & `PeriodClosingService` (modul baru) — menyimpan & memfilter per `branch_id`, dengan opsi "semua branch" eksplisit ✅
- Sebagian besar controller memakai `userSession.BranchId` untuk scoping.

**Belum per-branch (GAP yang harus diperbaiki):**
1. `JournalService.GetLedgers(...)` — query jurnal & `TrxBalances` **tidak** memfilter `branch_id`
   (mis. `.Where(tb => tb.CoaCode == coa.CoaCode && tb.PeriodDate <= startDate)` tanpa branch).
   → Buku Besar menjumlahkan lintas-branch.
2. `JournalService.GetLedgerSummary(...)` — sama seperti di atas (Neraca Saldo lintas-branch).
3. `JournalService.GetBulkBalancesAsOf(...)` — raw SQL `SELECT DISTINCT ON (coa_code) ... FROM trx_balance`
   **tanpa** `branch_id`; movement scan juga tanpa branch. Dipakai Neraca & Arus Kas.
4. `JournalService.GetBulkMovements(start,end)` — movement P&L **tanpa** filter branch. Dipakai Laba Rugi & Arus Kas.
5. `master.mst_coa.balance` — cache "saldo terkini" bersifat **agregat lintas-branch**
   (diisi `rebalance_migration.sql` step C). Tidak boleh dipakai sebagai saldo per-branch.
6. `mst_coa` sendiri sudah punya `branch_id`, tetapi COA "sama" bisa terduplikasi antar-branch;
   perlu kepastian keunikan `(coa_code, branch_id)` dan lookup selalu menyertakan branch.

## 2. Risiko bila dibiarkan

- Laporan **Neraca, Laba Rugi, Arus Kas, Buku Besar, Neraca Saldo** akan menjumlahkan angka
  dari semua cabang begitu ada >1 cabang → laporan per-cabang salah.
- `mst_coa.balance` menyesatkan bila dipakai untuk menampilkan saldo cabang tertentu.

## 3. Rencana bertahap (usulan urutan eksekusi)

**Fase 0 — Audit menyeluruh (read-only)**
- Telusuri semua query di `JournalService`, `COAService`, `Dashboard`, laporan, dan export untuk
  memastikan setiap agregasi saldo/mutasi menerima & memakai `branchId`.
- Inventaris pemakaian `mst_coa.balance` (grep) dan klasifikasikan: mana yang butuh per-branch.

**Fase 1 — Tambah scoping branch di layer perhitungan**
- Tambahkan parameter `int? branchId` (null = semua) ke: `GetLedgers`, `GetLedgerSummary`,
  `GetBulkBalancesAsOf`, `GetBulkMovements`, dan helper terkait; sisipkan filter `branch_id`.
- Update controller/route agar meneruskan `userSession.BranchId` (atau pilihan cabang) ke method di atas.
- `GetBulkBalancesAsOf`: ubah raw SQL menjadi `DISTINCT ON (coa_code, branch_id)` + filter `branch_id`.

**Fase 2 — Pensiunkan/atur ulang `mst_coa.balance`**
- Hentikan pemakaian `mst_coa.balance` sebagai sumber saldo. Ganti seluruh pembaca dengan
  `GetEndingBalance(coa, asOf, branchId)`.
- Bila cache tetap diinginkan untuk performa, buat tabel/kolom cache **per-branch**
  (mis. `mst_coa_balance(coa_code, branch_id, balance)`), diperbarui oleh job checkpoint.

**Fase 3 — Integritas data & konstrain**
- Pastikan unique `(coa_code, branch_id)` di `mst_coa`; audit data existing yang melanggar.
- Pastikan setiap insert jurnal (aplikasi, import, scheduler Khanza) selalu menuliskan `branch_id` benar.

**Fase 4 — Uji regresi**
- Buat 2 cabang dummy + jurnal terpisah; verifikasi Neraca/Laba Rugi/Arus Kas/Buku Besar/Neraca Saldo
  per-branch tidak saling tercampur; verifikasi "semua branch" menjumlahkan benar.
- Verifikasi tutup buku & saldo awal per-branch (sudah didesain per-branch) tetap konsisten.

## 4. Catatan integrasi dengan pekerjaan yang baru selesai

- Filter saldo **Published-only + transaction_date** (Item 1/3) sudah diterapkan; saat menambah
  scoping branch, pola filter `j.status == Posted` harus tetap dipertahankan.
- Modul **Saldo Awal** & **Tutup Buku** sudah per-branch dengan opsi "semua branch",
  jadi keduanya sudah selaras dengan target isolasi ini.
- Lock tutup buku memakai aturan: `branch_id NULL` = mengunci semua cabang; `branch_id = X`
  = mengunci cabang X saja. Pertahankan semantik ini saat isolasi diberlakukan penuh.
