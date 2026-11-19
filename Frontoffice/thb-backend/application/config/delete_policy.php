<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| DELETE / CANCEL CAPABILITY MAP  (P1-b)
|--------------------------------------------------------------------------
| Kunci = "<module>/<controller>" lowercase, tanpa prefix versi.
| Endpoint yang TIDAK terdaftar di sini => 422 CAPABILITY_NOT_CONFIGURED.
| Keberadaan kolom is_delete TIDAK dipakai sebagai dasar izin.
|
| operations : nama handler => jenis operasi
|              soft_delete | cancel | correction | none
| permission : daftar module.url yang salah satunya wajib dimiliki user
| scope      : direct  -> kolom branch ada di tabel objek
|              chain   -> branch dicapai lewat relasi (hops)
|              none    -> branch tidak dapat ditentukan (hanya untuk DENY)
| status_guard : kolom + nilai yang masih boleh diubah (selain itu 409)
| columns    : kolom yang WAJIB ada di tabel; kalau tidak ada => 422
| restricted : data klinis / ada side effect => selalu DENY
|
| Sumber kewenangan: user.id -> user_group -> group -> group_module -> module.url
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| KEBIJAKAN ROLE DELETE/CANCEL  (P3)
|--------------------------------------------------------------------------
| Hanya role Superadmin dan Administrator yang boleh menjalankan delete /
| soft delete / cancel. Role lain SELALU 403 DELETE_NOT_AUTHORIZED, tanpa
| menyentuh database dan tanpa side effect eksternal.
|
| 'delete_policy_roles' memetakan NAMA KEBIJAKAN -> nama group nyata di tabel
| `group`. Pemisahan ini perlu karena nama group di database berbeda dari nama
| kebijakan (lihat catatan di bawah). Pencocokan memakai TRIM + case-insensitive.
|
| CATATAN DATA PRODUKSI (dibaca read-only, 2026-11-15):
|   - group "Super User" ADA (16 user, 9 baris karena per cabang).
|   - group "Administrator" TIDAK ADA. group "Admisi" (8 user) adalah petugas
|     pendaftaran, bukan administrator.
|   - "Superadmin" TIDAK ADA sebagai nama group.
|
| KEPUTUSAN PEMILIK PROSES (2026-11-15, disetujui):
|   - "Super User" di produksi = role Superadmin. Itu satu-satunya role yang boleh.
|   - Tidak ada group Administrator saat ini, dan mapping-nya sengaja DIBIARKAN
|     KOSONG (array()). Kalau nanti ada group bernama "Administrator" dibuat,
|     group itu TIDAK otomatis berhak — perlu keputusan + perubahan config.
|   - "Admisi" tidak boleh dipakai sebagai Administrator.
|   - Semua role selain Super User ditolak.
|
| 'delete_policy_global_scope' = FALSE (dikonfirmasi pemilik): Super User tetap
| terikat cabang. Tidak ada konfigurasi scope global di database (tidak ada kolom
| global/all_branch/scope di `group`, `user`, maupun `setting`).
|--------------------------------------------------------------------------
*/

$config['delete_policy_allowed_roles'] = array('Superadmin', 'Administrator');

$config['delete_policy_roles'] = array(
    'Superadmin'    => array('Super User'),   // nama group nyata di produksi
    'Administrator' => array(),               // kosong: belum ada group-nya, jangan ditebak
);

$config['delete_policy_global_scope'] = FALSE;

$config['delete_policy'] = array(

    /* ================= ALLOW (3 endpoint) ================= */

    'health_facility/laboratory_parameter' => array(
        'table'      => 'laboratory_parameter',
        'operations' => array('delete_delete' => 'soft_delete'),
        'permission' => array('LaboratoryListView'),
        'scope'      => array('type' => 'direct', 'column' => 'institute_branch_id'),
        'status_guard' => null,
        'columns'    => array('is_delete'),
        'restricted' => false,
        'approval_required' => false,
    ),

    'mr/letter' => array(
        'table'      => 'letter',
        'operations' => array('delete_delete' => 'soft_delete'),
        'permission' => array('MasterLetterView'),
        'scope'      => array(
            'type' => 'chain',
            'hops' => array(
                array('from' => 'registration_destination_id', 'table' => 'registration_destination', 'match' => 'id', 'select' => 'health_facility_item_id'),
                array('from' => 'health_facility_item_id',     'table' => 'health_facility_item',     'match' => 'id', 'select' => 'health_facility_id'),
                array('from' => 'health_facility_id',          'table' => 'health_facility',          'match' => 'id', 'select' => 'institute_branch_id'),
            ),
        ),
        'status_guard' => null,
        'columns'    => array('is_delete', 'user_deleted', 'date_deleted'),
        'restricted' => false,
        'approval_required' => false,
    ),

    /* CANCEL — bukan delete. Tidak pernah menulis is_delete. */
    'mr/registration' => array(
        'table'      => 'registration',
        'operations' => array('cancel_put' => 'cancel'),
        'permission' => array('PatientListView'),
        'scope'      => array(
            'type' => 'chain',
            'hops' => array(
                array('from' => 'id',                      'table' => 'registration_destination', 'match' => 'registration_id', 'select' => 'health_facility_item_id'),
                array('from' => 'health_facility_item_id', 'table' => 'health_facility_item',     'match' => 'id',              'select' => 'health_facility_id'),
                array('from' => 'health_facility_id',      'table' => 'health_facility',          'match' => 'id',              'select' => 'institute_branch_id'),
            ),
        ),
        'status_guard' => array('column' => 'is_cancel', 'allowed' => array('0')),
        'columns'    => array('is_cancel'),
        'restricted' => false,
        'approval_required' => false,
    ),

    /* ================= DENY — data klinis / pelayanan (18) ================= */
    /* Aturan pemilik proses belum tersedia => DENY sementara. */

    'health_facility/laboratory_request' => array(
        'table' => 'laboratory_request', 'restricted' => true,
        'reason' => 'data pelayanan laboratorium: wajib cancel/koreksi sesuai SOP, bukan delete',
    ),
    'health_facility/laboratory_request_item' => array(
        'table' => 'laboratory_request_item', 'restricted' => true,
        'reason' => 'item permintaan laboratorium ikut jejak pelayanan',
    ),
    'health_facility/radiology_request' => array(
        'table' => 'radiology_request', 'restricted' => true,
        'reason' => 'data pelayanan radiologi: wajib cancel/koreksi sesuai SOP',
    ),
    'health_facility/radiology_request_item' => array(
        'table' => 'radiology_request_item', 'restricted' => true,
        'reason' => 'item permintaan radiologi ikut jejak pelayanan',
    ),
    'health_facility/radiology_request_item_image' => array(
        'table' => 'radiology_request_item_image', 'restricted' => true,
        'reason' => 'gambar radiologi adalah bagian rekam medis',
    ),
    'pharmacy/prescription' => array(
        'table' => 'prescription', 'restricted' => true,
        'reason' => 'resep adalah dokumen farmasi pelayanan',
    ),
    'pharmacy/receipt' => array(
        'table' => 'receipt', 'restricted' => true,
        'reason' => 'penerimaan farmasi mengubah stok; harus koreksi, bukan delete',
    ),
    'mr/soap_examination' => array(
        'table' => 'soap_examination', 'restricted' => true,
        'reason' => 'SOAP/penunjang adalah rekam medis',
    ),
    'mr/cppt' => array(
        'table' => 'cppt', 'restricted' => true,
        'reason' => 'CPPT adalah rekam medis',
    ),
    'mr/sbar' => array(
        'table' => 'sbar', 'restricted' => true,
        'reason' => 'SBAR adalah rekam medis',
    ),
    'mr/nurse' => array(
        'table' => 'nurse', 'restricted' => true,
        'reason' => 'asuhan keperawatan adalah rekam medis',
    ),
    'tr/transaction_doctor' => array(
        'table' => 'transaction_doctor', 'restricted' => true,
        'reason' => 'tindakan dokter adalah jejak pelayanan',
    ),
    'tr/transaction_alkes' => array(
        'table' => 'transaction_alkes', 'restricted' => true,
        'reason' => 'pemakaian alkes adalah jejak pelayanan',
    ),
    'tr/transaction_bhp' => array(
        'table' => 'transaction_bhp', 'restricted' => true,
        'reason' => 'pemakaian BHP adalah jejak pelayanan',
    ),
    'tr/transaction_nurse' => array(
        'table' => 'transaction_nurse', 'restricted' => true,
        'reason' => 'tindakan perawat adalah jejak pelayanan',
    ),
    'tr/transaction_bed' => array(
        'table' => 'transaction_bed', 'restricted' => true,
        'reason' => 'mutasi bed adalah jejak pelayanan rawat inap',
    ),
    'health_facility/bed' => array(
        'table' => 'bed', 'restricted' => true,
        'reason' => 'status bed terikat rawat inap',
    ),
    'health_facility/operating_room' => array(
        'table' => 'operating_room', 'restricted' => true,
        'reason' => 'data kamar operasi adalah jejak pelayanan',
    ),
    'finance/penagihan' => array(
        'table' => 'penagihan_piutang', 'restricted' => true,
        'reason' => 'penagihan yang sudah terbit adalah dokumen tagihan: batalkan lewat status Nol, bukan delete',
    ),
    'health_facility/surgery' => array(
        'table' => 'operating_room_consumable', 'restricted' => true,
        'reason' => 'pemakaian obat/alat operasi adalah data tagihan: wajib koreksi (void) dengan alasan, bukan delete',
    ),

    /* ================= DENY — ada side effect eksternal ================= */

    'pharmacy/purchase_order' => array(
        'table' => 'purchase_order', 'restricted' => true,
        'reason' => 'delete memicu panggilan API Accurate; semantik side effect belum didefinisikan',
    ),
);
