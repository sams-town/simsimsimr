<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| COA MAPPING — akun untuk jurnal otomatis
|--------------------------------------------------------------------------
| Setiap tipe peristiwa punya daftar baris jurnal: role + sisi (debit/credit).
| Pemanggil hanya mengirim NILAI per role; akun diambil dari sini.
| Baris dengan nilai 0 otomatis dilewati.
|
| Akun harus ada di `b_coa.account` (varchar 15). Kalau tidak ada, jurnal
| di-skip dengan alasan ACCOUNT_MISSING (tidak menggagalkan transaksi bisnis),
| dan dicatat di log_activity supaya kelihatan.
|
| Akun master: artifacts/deploy1/seed_deploy1.sql (8 akun) +
| artifacts/e2e_gap/seed_coa.sql (5 akun: 1-1110, 1-1120, 1-1300, 4-1300, 4-1400).
|--------------------------------------------------------------------------
*/

$config['coa_mapping'] = array(

    // Piutang dibuat: tagihan tumbuh, pendapatan diakui.
    'PIUTANG_CREATE' => array(
        'description' => 'Piutang pasien dibuat',
        'lines' => array(
            array('role' => 'receivable', 'side' => 'debit',  'account' => '1-1200'),
            array('role' => 'revenue',    'side' => 'credit', 'account' => '4-1200'),
        ),
    ),

    // Piutang dibayar: kas bertambah, piutang berkurang, diskon jadi beban.
    'PIUTANG_PAYMENT' => array(
        'description' => 'Pembayaran piutang pasien',
        'lines' => array(
            array('role' => 'cash',       'side' => 'debit',  'account' => '1-1100'),
            array('role' => 'discount',   'side' => 'debit',  'account' => '5-1200'),
            array('role' => 'receivable', 'side' => 'credit', 'account' => '1-1200'),
        ),
    ),

    // Jasa dokter bedah/anestesi dicatat sebagai hak dokter.
    'SURGERY_FEE' => array(
        'description' => 'Jasa medik operasi',
        'lines' => array(
            array('role' => 'receivable', 'side' => 'debit',  'account' => '1-1400'),
            array('role' => 'revenue',    'side' => 'credit', 'account' => '4-1100'),
        ),
    ),

    // Farmasi menyerahkan obat: HPP diakui, persediaan berkurang.
    'PHARMACY_DISPENSE' => array(
        'description' => 'Penyerahan obat (HPP)',
        'lines' => array(
            array('role' => 'hpp',     'side' => 'debit',  'account' => '5-1100'),
            array('role' => 'stock',   'side' => 'credit', 'account' => '1-1500'),
        ),
    ),

    /* ================= E-5: JURNAL STOK ================= */

    // Penerimaan barang dari supplier: persediaan bertambah, hutang dagang timbul.
    'STOCK_RECEIPT' => array(
        'description' => 'Penerimaan barang dari supplier',
        'lines' => array(
            array('role' => 'stock',   'side' => 'debit',  'account' => '1-1500'),
            array('role' => 'payable', 'side' => 'credit', 'account' => '1-1300'),
        ),
    ),

    // Opname: stok fisik lebih kecil dari administrasi => selisih jadi beban.
    'STOCK_OPNAME_LOSS' => array(
        'description' => 'Selisih kurang opname persediaan',
        'lines' => array(
            array('role' => 'hpp',   'side' => 'debit',  'account' => '5-1100'),
            array('role' => 'stock', 'side' => 'credit', 'account' => '1-1500'),
        ),
    ),

    // Opname: stok fisik lebih besar => koreksi mengurangi beban.
    'STOCK_OPNAME_GAIN' => array(
        'description' => 'Selisih lebih opname persediaan',
        'lines' => array(
            array('role' => 'stock', 'side' => 'debit',  'account' => '1-1500'),
            array('role' => 'hpp',   'side' => 'credit', 'account' => '5-1100'),
        ),
    ),

    // Mutasi antar depo: perpindahan internal, nilai pindah antar persediaan
    // (net nol terhadap akun persediaan, tetapi jejaknya tetap tercatat).
    'STOCK_MUTATION' => array(
        'description' => 'Mutasi persediaan antar depo',
        'lines' => array(
            array('role' => 'stock_in',  'side' => 'debit',  'account' => '1-1500'),
            array('role' => 'stock_out', 'side' => 'credit', 'account' => '1-1500'),
        ),
    ),

    // Retur obat dari pasien kembali ke stok: membalik HPP penyerahan.
    // CATATAN: brief menyebut "credit hutang dagang"; dipakai credit HPP karena
    // retur pasien tidak menimbulkan kewajiban ke supplier. Hutang dagang hanya
    // timbul dari penerimaan barang (STOCK_RECEIPT).
    'STOCK_RETURN' => array(
        'description' => 'Retur obat pasien kembali ke persediaan',
        'lines' => array(
            array('role' => 'stock', 'side' => 'debit',  'account' => '1-1500'),
            array('role' => 'hpp',   'side' => 'credit', 'account' => '5-1100'),
        ),
    ),

    /* ================= E-7: JURNAL KASIR ================= */

    // Pembayaran kasir. Akun debit dipilih dari cara bayar:
    //   cash (1-1100) / transfer (1-1110) / edc (1-1120).
    // Hanya satu role yang diisi pemanggil, sisanya 0 => dilewati.
    //
    // Sisi kredit = piutang pasien, BUKAN pendapatan: pendapatan sudah diakui
    // saat invoice dibuat (LAB_REVENUE / RAD_REVENUE / PIUTANG_CREATE). Kalau
    // di sini juga credit pendapatan, pendapatan akan dihitung dua kali.
    'CASHIER_PAYMENT' => array(
        'description' => 'Pembayaran kasir',
        'lines' => array(
            array('role' => 'cash',       'side' => 'debit',  'account' => '1-1100'),
            array('role' => 'bank',       'side' => 'debit',  'account' => '1-1110'),
            array('role' => 'edc',        'side' => 'debit',  'account' => '1-1120'),
            array('role' => 'receivable', 'side' => 'credit', 'account' => '1-1200'),
        ),
    ),

    /* ================= F3: JURNAL PENAGIHAN ================= */

    // Pembayaran penagihan (dunning) diterima: debit kas/bank/EDC + diskon,
    // credit piutang pasien. Prinsip sama dengan E-7 supaya pendapatan tidak
    // diakui dua kali (sudah diakui saat invoice dibuat).
    'PENAGIHAN_PAYMENT' => array(
        'description' => 'Pembayaran penagihan piutang',
        'lines' => array(
            array('role' => 'cash',       'side' => 'debit',  'account' => '1-1100'),
            array('role' => 'bank',       'side' => 'debit',  'account' => '1-1110'),
            array('role' => 'edc',        'side' => 'debit',  'account' => '1-1120'),
            array('role' => 'discount',   'side' => 'debit',  'account' => '5-1200'),
            array('role' => 'receivable', 'side' => 'credit', 'account' => '1-1200'),
        ),
    ),

    /* ================= F3+: REVERSE / VOID PENAGIHAN ================= */

    // Void pembayaran: KEBALIKAN dari PENAGIHAN_PAYMENT.
    // Piutang dibuka kembali (debit), kas/bank/EDC yang dipakai saat bayar dikembalikan
    // (credit). Akun kas di-override pemanggil dengan kd_rek_bayar asli supaya void
    // selalu membalik akun yang benar, bukan menebak dari cara bayar.
    'PENAGIHAN_VOID' => array(
        'description' => 'Void pembayaran penagihan piutang',
        'lines' => array(
            array('role' => 'receivable', 'side' => 'debit',  'account' => '1-1200'),
            array('role' => 'cash',       'side' => 'credit', 'account' => '1-1100'),
            array('role' => 'bank',       'side' => 'credit', 'account' => '1-1110'),
            array('role' => 'edc',        'side' => 'credit', 'account' => '1-1120'),
            array('role' => 'discount',   'side' => 'credit', 'account' => '5-1200'),
        ),
    ),

    /* ================= E-8: JURNAL LAB / RADIOLOGI ================= */

    // Tagihan lab/radiologi masuk billing: piutang pasien naik, pendapatan diakui
    // per jenis layanan (akun berbeda untuk lab dan radiologi).
    'LAB_REVENUE' => array(
        'description' => 'Pendapatan laboratorium',
        'lines' => array(
            array('role' => 'receivable', 'side' => 'debit',  'account' => '1-1200'),
            array('role' => 'revenue',    'side' => 'credit', 'account' => '4-1300'),
        ),
    ),

    'RAD_REVENUE' => array(
        'description' => 'Pendapatan radiologi',
        'lines' => array(
            array('role' => 'receivable', 'side' => 'debit',  'account' => '1-1200'),
            array('role' => 'revenue',    'side' => 'credit', 'account' => '4-1400'),
        ),
    ),
);

/*
|--------------------------------------------------------------------------
| E-7: akun debit pembayaran kasir per tipe cara bayar
|--------------------------------------------------------------------------
| Tabel `payment_method` punya kolom `type_id` + `coa`. Selama `coa` terisi dan
| akunnya ada di b_coa, nilai itu yang dipakai. Kalau kosong (tabel saat ini
| masih 0 baris), dipakai peta tipe di bawah.
|
| TODO: konfirmasi arti type_id ke pemilik proses saat data payment_method diisi.
|--------------------------------------------------------------------------
*/
$config['payment_method_accounts'] = array(
    '1'       => '1-1100',   // tunai
    '2'       => '1-1110',   // transfer / bank
    '3'       => '1-1120',   // kartu / EDC
    'default' => '1-1100',
);
