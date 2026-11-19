<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| BRANCH — MODE SINGLE CABANG (keputusan owner: Opsi C)
|--------------------------------------------------------------------------
| RS hanya punya 1 cabang. Tidak ada filter branch masif ke 1.200+ endpoint,
| tetapi mekanismenya disiapkan supaya siap multi-cabang tanpa ubah kode lagi.
|
| Catatan skema: `institute_branch.id` bertipe char(36) (UUID), BUKAN integer.
| Karena itu "id = 1" diwujudkan sebagai UUID default
| 00000000-0000-4000-8000-000000000001 (kode BR001) — nilai tetap & mudah dikenali.
|
| TODO: expand branch filter saat multi-cabang
|   -> set branch_single_mode = FALSE, lalu aktifkan filter per-endpoint
|      (pola sudah ada di pharmacy/Prescription, finance/Piutang,
|       health_facility/Operating_room, health_facility/Surgery).
|--------------------------------------------------------------------------
*/

// TRUE = semua transaksi jatuh ke default_branch_id bila token tidak membawa branch.
$config['branch_single_mode'] = TRUE;

// Branch default (dipakai saat mode single & branch token kosong).
$config['default_branch_id']  = '00000000-0000-4000-8000-000000000001';
$config['default_branch_code'] = 'BR001';

// Data lama (sebelum fase branch scope) punya institute_branch_id NULL.
// TRUE = baris NULL tetap boleh dibaca (GET), tapi TIDAK boleh ditulis ulang tanpa branch.
$config['allow_legacy_null_branch'] = TRUE;
