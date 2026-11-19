<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| ROLE POLICY — guard POST/PUT/DELETE per modul (tahap 1 deployment readiness)
|--------------------------------------------------------------------------
| Mengikuti pola yang SUDAH ADA di FO: application/config/delete_policy.php
|   'delete_policy_roles'          => nama KEBIJAKAN => nama group nyata di `group`
|   'delete_policy_allowed_roles'  => daftar kebijakan yang boleh
|   resolusi: user.id -> user_group -> group (TRIM + case-insensitive,
|             difilter institute_branch_id = branch actor) di Delete_policy::_resolve_actor()
|
| Konfigurasi ini memakai pola yang sama supaya hanya ada SATU cara memetakan role.
|
| REALITA GROUP (dibaca dari DB):
|   - "Super User" = group administrator (satu-satunya admin di produksi).
|   - "Admisi"     = petugas pendaftaran.
|   - "Apoteker", "Kasir", "Dokter" BELUM ADA -> dibuat oleh
|     artifacts/deploy1/seed_groups.sql (idempoten, per cabang).
|
| ADMIN: 'role_policy_admin_groups' memberi SEMUA capability sekaligus
|   (admin = apoteker + kasir + dokter + registrasi). Tidak perlu mendaftarkan
|   "Super User" di setiap capability, dan admin tidak pernah terkunci.
|
| TODO: expand branch filter saat multi-cabang
|--------------------------------------------------------------------------
*/

// TRUE = guard aktif untuk endpoint yang terdaftar di role_policy.
$config['role_policy_enforce'] = TRUE;

// Endpoint ("<module>/<controller>", lowercase, tanpa prefix versi) => capability.
$config['role_policy'] = array(
    'pharmacy/prescription'           => 'apoteker',
    'pharmacy/receipt'                => 'apoteker',
    'pharmacy/mutation'               => 'apoteker',
    'finance/piutang'                 => 'kasir_admin',
    'health_facility/surgery'         => 'dokter_operasi',
    'health_facility/operating_room'  => 'dokter_operasi',
    'mr/registration'                 => 'registrasi',
);

// Capability => nama group nyata di tabel `group` (yang mengerjakan fungsinya).
$config['role_policy_groups'] = array(
    'apoteker'       => array('Apoteker'),
    'kasir_admin'    => array('Kasir'),
    'dokter_operasi' => array('Dokter'),
    'registrasi'     => array('Admisi'),
);

// Group administrator: berhak atas SEMUA capability di atas (admin = 4 role).
$config['role_policy_admin_groups'] = array('Super User');

// Method yang DIPERIKSA. GET selalu bebas (single cabang: satu cabang = semua data).
$config['role_policy_methods'] = array('post', 'put', 'delete', 'patch');
