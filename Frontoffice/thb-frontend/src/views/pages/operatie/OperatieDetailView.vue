<template>
    <div>
        <Layout
            :layout_breadcrumb_items="[
                { text: 'Home', link: '/' },
                { text: 'OK', link: '/operatie-kamer' },
                { text: `${regData.name}` }
            ]"
        >
            <section class="">
                <section class="">
                    <div class="card">
                        <div class="card-header bg-indigo-200">
                            <span class="text-bold-18">Data Dasar</span>
                        </div>
                        <div class="card-body">
                            <div class="row" style="font-size: 14px">
                                <div class="col-4">
                                    <div class="font-weight-bold">Nama</div>
                                    <div>{{ regData.name }} ({{ parsed_age(regData.birth_date) }})</div>
                                </div>
                                <div class="col-4">
                                    <div class="font-weight-bold">Tanggal Lahir</div>
                                    <div>{{ localDate(regData.birth_date) }}</div>
                                </div>
                                <div class="col-4">
                                    <div class="font-weight-bold">No RM</div>
                                    <div>{{ regData.norm }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="mt-24">
                    <div class="card">
                        <div class="card-body">
                            <section class="d-flex flex-wrap custom-tab">
                                <li class="my-2 mx-1">
                                    <a @click="goToTab('menu1')" size="sm" class="px-16 py-8 rounded-pill" :class="{ active: active.current_tab === 'menu1' }">
                                        <span class="text-semi-14-on">Detail Operasi</span>
                                    </a>
                                </li>
                                <li class="my-2 mx-1">
                                    <a @click="goToTab('menu2')" class="px-16 py-8 rounded-pill" :class="{ active: active.current_tab === 'menu2' }">
                                        <span class="text-semi-14-on">Pra-Operasi</span>
                                    </a>
                                </li>
                                <li class="my-2 mx-1">
                                    <a @click="goToTab('menu3')" class="px-16 py-8 rounded-pill" :class="{ active: active.current_tab === 'menu3' }">
                                        <span class="text-semi-14-on">Keperawatan Operasi</span>
                                    </a>
                                </li>
                                <li class="my-2 mx-1">
                                    <a @click="goToTab('menu4')" class="px-16 py-8 rounded-pill" :class="{ active: active.current_tab === 'menu4' }">
                                        <span class="text-semi-14-on">Keamanan Operasi</span>
                                    </a>
                                </li>
                                <li class="my-2 mx-1">
                                    <a @click="goToTab('menu5')" class="px-16 py-8 rounded-pill" :class="{ active: active.current_tab === 'menu5' }">
                                        <span class="text-semi-14-on">Catatan Anestesi</span>
                                    </a>
                                </li>
                                <li class="my-2 mx-1">
                                    <a @click="goToTab('menu6')" class="px-16 py-8 rounded-pill" :class="{ active: active.current_tab === 'menu6' }">
                                        <span class="text-semi-14-on">Post Operasi</span>
                                    </a>
                                </li>
                                <li class="my-2 mx-1">
                                    <a @click="goToTab('menu7')" class="px-16 py-8 rounded-pill" :class="{ active: active.current_tab === 'menu7' }">
                                        <span class="text-semi-14-on">Laporan Operasi</span>
                                    </a>
                                </li>
                            </section>
                        </div>
                    </div>
                </section>

                    <!-- Current_tab = menu1 -->
                <section v-if="active.current_tab === 'menu1'" class="mt-24">

                    <section class="card mt-32">
                        <div class="card-header font-weight-bold bg-primary-200">Informasi Operator</div>
                        <div class="card-body">
                            <b-button @click="openModalOperator" size="sm" variant="outline-primary" pill class="px-3">
                                <feather-icon icon="PlusIcon" /> Tambah Operator
                            </b-button>
                            
                            <b-table :fields="operatorFields" responsive bordered :items="operator_list" class="mt-16" style="font-size: 13px">
                                <template #cell(no)="row">
                                    <div class="text-center">{{ row.index+1 }}</div>
                                </template>
                                <template #cell(operator_role_type_name)="row">
                                    <div class="text-center">
                                        <span v-if="row.item.operator_role_type_id == 1">Operator</span>
                                        <span v-if="row.item.operator_role_type_id == 2">Co-operator</span>
                                        <span v-if="row.item.operator_role_type_id == 3">Instrumen</span>
                                        <span v-if="row.item.operator_role_type_id == 4">Circular</span>
                                        <span v-if="row.item.operator_role_type_id == 5">Anastesi</span>
                                        <span v-if="row.item.operator_role_type_id == 6">Bidan</span>
                                        <span v-if="row.item.operator_role_type_id == 7">DPJP</span>
                                        <span v-if="row.item.operator_role_type_id == 8">Dokter Umum</span>
                                    </div>
                                </template>
                                <template #cell(action)="row">
                                    <div class="d-flex justify-content-center">
                                        <b-button @click="deleteOperator(row.item)" variant="danger-600" size="sm" v-b-popover.hover.top="'Hapus'">
                                            <feather-icon icon="TrashIcon" />
                                        </b-button>
                                    </div>
                                </template>
                            </b-table>
                            <b-modal v-model="active.modal_operator" size="lg" title="Tambah Operator" no-close-on-backdrop>
                                <section class="">
                                    <section class="card">
                                        <div class="card-body">
                                            <div class="row justify-content-center align-items-end">
                                                <div class="col-lg-5">
                                                    <label class="c-form-label">Operator <span class="text-danger">*</span></label>
                                                    <v-select v-model="input_operator.employee" :options="doctor_list" :reduce="doctor_list => doctor_list" label="title" placeholder="-- Operator --"></v-select>
                                                </div>
                                                <div class="col-lg-5 mt-12 mt-lg-0">
                                                    <label class="c-form-label">Peran <span class="text-danger">*</span></label>
                                                    <v-select v-model="input_operator.role" :options="OptionsRole" :reduce="OptionsRole => OptionsRole" label="title" placeholder="-- Peran --"></v-select>
                                                </div>
                                                <div class="px-3 mt-12 mt-lg-0">
                                                    <b-button variant="outline-primary" @click="onAddOperator">
                                                        <feather-icon icon="PlusIcon" /> Tambah
                                                    </b-button>                            
                                                </div>
                                            </div>
                                        </div>
                                    </section>

                                    <b-table :fields="operatorFields" responsive bordered :items="form_operator.operators" class="mt-16" style="font-size: 13px">
                                        <template #cell(no)="row">
                                            <div class="text-center">{{ row.index+1 }}</div>
                                        </template>
                                        <template #cell(action)="row">
                                            <div class="d-flex justify-content-center">
                                                <b-button variant="danger-600" size="sm" @click="removeOperator(row.index)" v-b-popover.hover.top="'Hapus'">
                                                    <feather-icon icon="TrashIcon" />
                                                </b-button>
                                            </div>
                                        </template>
                                    </b-table>
                                </section>

                                <template #modal-footer>
                                    <div class="w-100 d-flex justify-content-end">
                                        <b-button @click="validationOperator" :disabled="disabled.operator" variant="primary" class="px-3">
                                            <feather-icon icon="SaveIcon" /> Simpan
                                        </b-button>
                                    </div>
                                </template>
                            </b-modal>

                        </div>
                    </section>

                    <!-- <section class="mt-32">
                        <div class="card">
                            <div class="card-header bg-primary-200">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-bold-16">List Pemakaian Obat dan Alat Medis</span>
                                    <b-button variant="indigo-500" :disabled="is_submit"><feather-icon icon="SaveIcon" /></b-button>
                                    <b-button variant="indigo-500" :disabled="is_submit" @click.prevent="validationFormTransaction"><feather-icon icon="SaveIcon" /></b-button>
                                </div>
                            </div>
                            <div class="card-body">   
                                <section class="col-lg-12">
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <label>Lokasi Order Obat (APOTIK)</label>
                                        </div>
                                        <div class="col-lg-9">
                                            <v-select v-model="form_transaction.pharmacy_depo_id" :options="OptionsPharmacyDepo" :reduce="(OptionsPharmacyDepo) => OptionsPharmacyDepo.value" label="title" placeholder="-- Lokasi Order Obat (APOTIK) --"></v-select>
                                        </div>
                                        <div class="col-lg-3" style="display:none;">
                                            <label>Poliklinik/Ruangan</label>
                                        </div>
                                        <div class="col-lg-9" style="display:none;">
                                            <v-select v-model="form_transaction.health_facility_item_id" :options="OptionsHealthFacilityItem" :reduce="(OptionsHealthFacilityItem) => OptionsHealthFacilityItem.value" label="title" placeholder="-- Poliklinik/Ruangan --"></v-select>
                                        </div>
                                        <div class="col-lg-3" style="display:none;">
                                            <label>Dokter</label>
                                        </div>
                                        <div class="col-lg-9" style="display:none;">
                                            <v-select v-model="form_transaction.soap_p_id" :options="OptionsSoapPlanDoctor" :reduce="(OptionsSoapPlanDoctor) => OptionsSoapPlanDoctor.value" label="title" placeholder="-- Dokter --"></v-select>
                                        </div>
                                        <div class="col-lg-3">
                                            <label>Jenis Resep</label>
                                        </div>
                                        <div class="col-lg-9">
                                            <v-select v-model="form_transaction.type_prescription_id" :options="PrescriptionTypeOptions" :reduce="PrescriptionTypeOptions => PrescriptionTypeOptions.value" label="title" placeholder="Instalasi"></v-select>
                                        </div>
                                    </div>
                                    <div class="row mt-16">
                                        <table class="table">
                                            <thead class="text-center text-bold-14">
                                                <tr>
                                                    <th rowspan="2">
                                                        <b-form-checkbox />
                                                    </th>
                                                    <th rowspan="2" style="min-width: 300px;">NAMA BARANG</th>
                                                    <th colspan="5">ESTIMASI ORDER</th>
                                                </tr>
                                                <tr>
                                                    <th style="min-width: 50px;">KETERANGAN&ATURAN PAKAI</th>
                                                    <th style="min-width: 50px;">SEDIAAN OBAT</th>
                                                    <th style="min-width: 50px;">DOSIS DOKTER</th>
                                                    <th style="min-width: 50px;">JUMLAH</th>
                                                    <th style="min-width: 140px;">KEMASAN</th>
                                                </tr>
                                            </thead>
                                            <template v-for="(item_p, index_p) in form_transaction.form_prescription_parent">
                                                <tr>
                                                    <td colspan="2">
                                                        <label><b>{{ item_p.prescription }}</b></label>
                                                    </td>
                                                    <td colspan="2" v-if="item_p.id !== 1">
                                                        <b-input-group append="Jumlah">
                                                            <b-form-input style="width:150px" type="number" v-model="item_p.value" />
                                                        </b-input-group>
                                                        <b-input-group append="Aturan Pakai">
                                                            <b-form-input style="width:150px" type="text" v-model="item_p.used" />
                                                        </b-input-group>
                                                    </td>
                                                </tr>
                                                <template v-for="(item, index) in form_transaction.form_prescription"> 
                                                    <tr class="item" v-if="item_p.id === item.prescription_type_id">
                                                        <td>
                                                            <b-button variant="danger-600" size="sm" @click="deletePRES(index)" class="mr-2">
                                                                <feather-icon icon="TrashIcon" />
                                                            </b-button>
                                                        </td>
                                                        <td>
                                                            {{ item.name }}
                                                        </td>
                                                        <td>
                                                            <b-form-input type="number" v-model="item.value_package" />
                                                        </td>
                                                        <td>
                                                            <v-select v-model="item.package_id" :options="PackageOptions" :reduce="PackageOptions => PackageOptions.value" label="title" placeholder="Kemasan"></v-select>
                                                        </td>
                                                    </tr>
                                                </template>
                                            </template>
                                            <tr>
                                                <td></td>
                                                <td>
                                                    <v-select v-model="prescription" @input="onAddPRES($event)" :options="PrescriptionOptions" :reduce="PrescriptionOptions => PrescriptionOptions" label="title" placeholder="-- Pilih --"></v-select>
                                                    <v-select v-model="catalog" @input="onAddCAT($event)" :options="CatalogOptions" :reduce="CatalogOptions => CatalogOptions" label="title" placeholder="-- Pilih Katalog --"></v-select>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </section>

                                <div class="card-body mt-32">
                                    <div class="mt-16 mt-lg-24">
                                        <b-table-simple bordered hover resposive>
                                            <b-thead>
                                                <b-tr>
                                                    <b-th>Tanggal & Jam Input</b-th>
                                                    <b-th>No Resep/Order</b-th>
                                                    <b-th>Lokasi Order Obat (APOTIK)</b-th>
                                                    <b-th>Dokter</b-th>
                                                    <b-th>Status</b-th>
                                                    <b-th>Aksi</b-th>
                                                </b-tr>
                                            </b-thead>
                                            <b-tbody>
                                                <template v-for="(itemsResult,i) in prescriptionitems" >
                                                    <b-tr>
                                                        <b-td>{{ itemsResult.date_added }}</b-td>
                                                        <b-td>{{ itemsResult.no_order }}</b-td>
                                                        <b-td>{{ itemsResult.depo_name }}</b-td>
                                                        <b-td>{{ itemsResult.doctor_in_charge_name }}</b-td>
                                                        <b-td>{{ itemsResult.status }}</b-td>
                                                        <b-td>
                                                            <b-button @click="openPrescription(itemsResult)" size="sm" variant="warning-500" class="mr-2">
                                                                <feather-icon icon="EyeIcon" />
                                                            </b-button>
                                                        </b-td>
                                                    </b-tr>
                                                </template>
                                            </b-tbody>
                                        </b-table-simple>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </section> -->

                </section>

                    <!-- Current_tab = menu2 -->
                <section v-if="active.current_tab === 'menu2'" class="mt-24">
                    <section class="card">
                        <div class="card-header font-weight-bold bg-primary-200">Serah Terima Pra-Operasi</div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-4 py-3 mt-2 general-box">
                                    <div>
                                        <label class="c-form-label font-weight-bold">LAPOR KE DOKTER OPERASI</label>
                                        <b-form-radio-group v-model="pre_op_handover_form.report_to_operating_doctor" stacked size="sm">
                                            <b-form-radio value="1">SUDAH</b-form-radio>
                                            <b-form-radio value="2">BELUM</b-form-radio>
                                        </b-form-radio-group>
                                    </div>
                                </div>
                                <div class="col-lg-4 py-3 mt-2 mid-box">
                                    <div>
                                        <label class="c-form-label font-weight-bold">LAPOR KE KAMAR OPERASI</label>
                                        <b-form-radio-group v-model="pre_op_handover_form.report_to_operating_room" stacked size="sm">
                                            <b-form-radio value="1">SUDAH</b-form-radio>
                                            <b-form-radio value="2">BELUM</b-form-radio>
                                        </b-form-radio-group>
                                    </div>
                                </div>
                                <div class="col-lg-4 py-3 mt-2 general-box">
                                    <div>
                                        <label class="c-form-label font-weight-bold">MENGISI INFORMED CONSENT</label>
                                        <b-form-radio-group v-model="pre_op_handover_form.filling_informed_consent" stacked size="sm">
                                            <b-form-radio value="1">SUDAH</b-form-radio>
                                            <b-form-radio value="2">BELUM</b-form-radio>
                                        </b-form-radio-group>
                                    </div>
                                </div>
                            </div>
                            <hr class="d-none d-lg-block">
                            <div class="row">
                                <div class="col-lg-4 py-3 mt-2 general-box">
                                    <div>
                                        <label class="c-form-label font-weight-bold">MENCUKUR DAERAH OPERASI</label>
                                        <b-form-radio-group v-model="pre_op_handover_form.shaving_operating_area" stacked size="sm">
                                            <b-form-radio value="1">SUDAH</b-form-radio>
                                            <b-form-radio value="2">BELUM</b-form-radio>
                                        </b-form-radio-group>
                                    </div>
                                </div>
                                <div class="col-lg-4 py-3 mt-2 mid-box">
                                    <div>
                                        <label class="c-form-label font-weight-bold">PASANG INFUS</label>
                                        <b-form-radio-group v-model="pre_op_handover_form.install_infusion" stacked size="sm">
                                            <b-form-radio value="1">SUDAH</b-form-radio>
                                            <b-form-radio value="2">BELUM</b-form-radio>
                                        </b-form-radio-group>
                                    </div>
                                </div>
                                <div class="col-lg-4 py-3 mt-2 general-box">
                                    <div>
                                        <label class="c-form-label font-weight-bold">TRANSFUSI</label>
                                        <b-form-radio-group v-model="pre_op_handover_form.transfusion" stacked size="sm">
                                            <b-form-radio value="1">SUDAH</b-form-radio>
                                            <b-form-radio value="2">BELUM</b-form-radio>
                                        </b-form-radio-group>
                                    </div>
                                </div>
                            </div>
                            <hr class="d-none d-lg-block">
                            <div class="row">
                                <div class="col-lg-4 py-3 mt-2 general-box">
                                    <div>
                                        <label class="c-form-label font-weight-bold">PASANG CATHETER</label>
                                        <b-form-radio-group v-model="pre_op_handover_form.install_catheter" stacked size="sm">
                                            <b-form-radio value="1">SUDAH</b-form-radio>
                                            <b-form-radio value="2">BELUM</b-form-radio>
                                        </b-form-radio-group>
                                    </div>
                                </div>
                                <div class="col-lg-4 py-3 mt-2 mid-box">
                                    <div>
                                        <label class="c-form-label font-weight-bold">PASANG NGT</label>
                                        <b-form-radio-group v-model="pre_op_handover_form.install_ngt" stacked size="sm">
                                            <b-form-radio value="1">SUDAH</b-form-radio>
                                            <b-form-radio value="2">BELUM</b-form-radio>
                                        </b-form-radio-group>
                                    </div>
                                </div>
                                <div class="col-lg-4 py-3 mt-2 general-box">
                                    <div>
                                        <label class="c-form-label font-weight-bold">CLISMA</label>
                                        <b-form-radio-group v-model="pre_op_handover_form.clisma" stacked size="sm">
                                            <b-form-radio value="1">SUDAH</b-form-radio>
                                            <b-form-radio value="2">BELUM</b-form-radio>
                                        </b-form-radio-group>
                                    </div>
                                </div>
                            </div>
                            <hr class="d-none d-lg-block">
                            <div class="row">
                                <div class="col-lg-4 py-3 mt-2 general-box">
                                    <div>
                                        <label class="c-form-label font-weight-bold">ECG</label>
                                        <b-form-radio-group v-model="pre_op_handover_form.ecg" stacked size="sm">
                                            <b-form-radio value="1">SUDAH</b-form-radio>
                                            <b-form-radio value="2">BELUM</b-form-radio>
                                        </b-form-radio-group>
                                    </div>
                                </div>
                                <div class="col-lg-4 py-3 mt-2 mid-box">
                                    <div>
                                        <label class="c-form-label font-weight-bold">USG</label>
                                        <b-form-radio-group v-model="pre_op_handover_form.usg" stacked size="sm">
                                            <b-form-radio value="1">SUDAH</b-form-radio>
                                            <b-form-radio value="2">BELUM</b-form-radio>
                                        </b-form-radio-group>
                                    </div>
                                </div>
                                <div class="col-lg-4 py-3 mt-2 general-box">
                                    <div>
                                        <label class="c-form-label font-weight-bold">MENGHAPUS KOSMETIK DAN CAT KUKU</label>
                                        <b-form-radio-group v-model="pre_op_handover_form.remove_cosmetics" stacked size="sm">
                                            <b-form-radio value="1">SUDAH</b-form-radio>
                                            <b-form-radio value="2">BELUM</b-form-radio>
                                        </b-form-radio-group>
                                    </div>
                                </div>
                            </div>
                            <hr class="d-none d-lg-block">
                            <div class="row">
                                <div class="col-lg-4 py-3 mt-2 general-box">
                                    <div>
                                        <label class="c-form-label font-weight-bold">MELEPAS SOFLENS, PERHIASAN, GIGI PALSU</label>
                                        <b-form-radio-group v-model="pre_op_handover_form.remove_softlens" stacked size="sm">
                                            <b-form-radio value="1">SUDAH</b-form-radio>
                                            <b-form-radio value="2">BELUM</b-form-radio>
                                        </b-form-radio-group>
                                    </div>
                                </div>
                                <div class="col-lg-4 py-3 mt-2 mid-box">
                                    <div>
                                        <label class="c-form-label font-weight-bold">CEK GELANG PASIEN</label>
                                        <b-form-radio-group v-model="pre_op_handover_form.check_bracelet" stacked size="sm">
                                            <b-form-radio value="1">SUDAH</b-form-radio>
                                            <b-form-radio value="2">BELUM</b-form-radio>
                                        </b-form-radio-group>
                                    </div>
                                </div>
                                <div class="col-lg-4 py-3 mt-2 general-box">
                                    <div>
                                        <label class="c-form-label font-weight-bold">PUASA</label>
                                        <b-form-radio-group v-model="pre_op_handover_form.fasting" stacked size="sm">
                                            <b-form-radio value="1">SUDAH</b-form-radio>
                                            <b-form-radio value="2">BELUM</b-form-radio>
                                        </b-form-radio-group>
                                    </div>
                                </div>
                            </div>
                            <hr class="d-none d-lg-block">
                            <div class="row">
                                <div class="col-lg-4 py-3 mt-2 general-box">
                                    <div>
                                        <label class="c-form-label font-weight-bold">PROFILAKSIS</label>
                                        <b-form-radio-group v-model="pre_op_handover_form.prophilaxis" stacked size="sm">
                                            <b-form-radio value="1">SUDAH</b-form-radio>
                                            <b-form-radio value="2">BELUM</b-form-radio>
                                        </b-form-radio-group>
                                    </div>
                                </div>
                                <div class="col-lg-4 py-3 mt-2 mid-box">
                                    <div>
                                        <label class="c-form-label font-weight-bold">PREMEDIKASI</label>
                                        <b-form-radio-group v-model="pre_op_handover_form.premedication" stacked size="sm">
                                            <b-form-radio value="1">SUDAH</b-form-radio>
                                            <b-form-radio value="2">BELUM</b-form-radio>
                                        </b-form-radio-group>
                                    </div>
                                </div>
                                <div class="col-lg-4 py-3 mt-2 general-box">
                                    <div>
                                        <label class="c-form-label font-weight-bold">LAIN-LAIN</label>
                                        <b-form-radio-group v-model="pre_op_handover_form.others" stacked size="sm">
                                            <b-form-radio value="1">SUDAH</b-form-radio>
                                            <b-form-radio value="2">BELUM</b-form-radio>
                                        </b-form-radio-group>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-12">
                                <label class="c-form-label">RONTGEN</label>
                                <b-textarea rows="2" v-model="pre_op_handover_form.rontgen" />
                            </div>
                            <div class="mt-12">
                                <label class="c-form-label">TERAPI YANG TELAH DIBERIKAN</label>
                                <b-textarea rows="2" v-model="pre_op_handover_form.theraphy" />
                            </div>
                            <div class="mt-12">
                                <label class="c-form-label">CATATAN PASANG INFUS</label>
                                <b-textarea rows="2" v-model="pre_op_handover_form.infusion_install_notes" />
                            </div>
                            <div class="mt-12">
                                <label class="c-form-label">CATATAN TRANSFUSI</label>
                                <b-textarea rows="2" v-model="pre_op_handover_form.infusion_notes" />
                            </div>
                            <div class="mt-12">
                                <label class="c-form-label">CATATAN PASANG NGT</label>
                                <b-textarea rows="2" v-model="pre_op_handover_form.ngt_install_notes" />
                            </div>
                            <div class="mt-12">
                                <label class="c-form-label">PENANDAAN LOKASI OPERASI</label>
                                <b-textarea rows="2" v-model="pre_op_handover_form.location_mark" />
                            </div>
                            <div class="mt-12">
                                <label class="c-form-label">LAINNYA</label>
                                <b-textarea rows="2" v-model="pre_op_handover_form.other_notes" />
                            </div>
                            <div class="d-flex justify-content-end mt-24">
                                <b-button variant="primary" @click="submit_pre_op_handover_form()">
                                    <feather-icon icon="SaveIcon" class="mr-2" /> Simpan
                                </b-button>
                            </div>
                        </div>
                    </section>

                    <div ref="Muskuloskeletal Laki" class="mt-32" v-if="regData.sex === 1">
                        <ModernLabel label="Pemerikasaan Muskuloskeletal" />
                        <div class="mt-20">
                            <div v-if="editable.musLaki == true" class="w-100">
                                <MuskuloskeletalLakiView :drawing="pre_op_handover_form.muskuloskeletal_laki" @save="saveMusLaki"/>
                            </div>
                            <div v-else class="w-100 mx-auto">
                                <div class="card">
                                    <div class="card-header bg-primary-500 text-semi-18 text-white">Muskuloskeletal Laki-laki</div>
                                    <div class="card-body d-flex bg-white">
                                        <div class="mx-auto col-lg-8">
                                            <img src="./../../../@core/assets/images/canvas/Muskuloskeletal-laki.jpg" alt="" class="w-100" style="aspect-ratio: 16/9; object-fit: cover;">
                                            <b-button @click="editable.musLaki = !editable.musLaki" size="sm" variant="primary-200" class="w-100">
                                                <b-icon icon="pencil-square" scale="0.7" />
                                                <span class="text-bold-16">Start Drawing</span>
                                            </b-button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div ref="Muskuloskeletal pr" class="mt-72" v-if="regData.sex === 0">
                        <ModernLabel label="Pemerikasaan Muskuloskeletal Perempuan" />
                        <div class="mt-20">
                            <div v-if="editable.musPr == true" class="w-100">
                                <MuskuloskeletalPrView :drawing="pre_op_handover_form.muskuloskeletal_pr" @save="saveMusPr"/>
                            </div>
                            <div v-else class="w-100 mx-auto">
                                <div class="card">
                                    <div class="card-header bg-primary-500 text-semi-18 text-white">Muskuloskeletal Perempuan</div>
                                    <div class="card-body d-flex bg-white">
                                        <div class="mx-auto col-lg-8">
                                            <img src="./../../../@core/assets/images/canvas/Muskuloskeletal-pr.jpg" alt="" class="w-100" style="aspect-ratio: 16/9; object-fit: cover;">
                                            <b-button @click="editable.musPr = !editable.musPr" size="sm" variant="primary-200" class="w-100">
                                                <b-icon icon="pencil-square" scale="0.7" />
                                                <span class="text-bold-16">Start Drawing</span>
                                            </b-button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-column mt-32">
                        <ModernLabel label="Pemeriksaan Kepala" />
                        <div class="mt-20">
                            <div v-if="editable.kepala == true" class="w-100">
                                <KepalaView :drawing="pre_op_handover_form.kepala" @save="saveKepala"/>
                            </div>
                            <div v-else class="w-100 mx-auto">
                                <div class="card">
                                    <div class="card-header bg-primary-500 text-semi-18 text-white">Kepala</div>
                                    <div class="card-body d-flex bg-white">
                                        <div class="mx-auto col-lg-8">
                                            <img src="./../../../@core/assets/images/canvas/Kepala.jpg" alt="" class="w-100" style="aspect-ratio: 16/9; object-fit: cover;">
                                            <b-button @click="editable.kepala = !editable.kepala" size="sm" variant="primary-200" class="w-100">
                                                <b-icon icon="pencil-square" scale="0.7" />
                                                <span class="text-bold-16">Start Drawing</span>
                                            </b-button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div ref="Dada Lk" class="mt-32">
                        <ModernLabel label="Ekstremitas Atas" />
                        <div class="mt-20">
                            <div v-if="editable.ekstrimAtas == true" class="w-100">
                                <EkstrimAtasView :drawing="pre_op_handover_form.ekstremitas_atas" @save="saveekstrimAtas"/>
                            </div>
                            <div v-else class="w-100 mx-auto">
                                <div class="card">
                                    <div class="card-header bg-primary-500 text-semi-18 text-white">Ekstremitas Atas</div>
                                    <div class="card-body d-flex bg-white">
                                        <div class="mx-auto col-lg-8">
                                            <img src="./../../../@core/assets/images/canvas/Ekstremitas Atas.jpg" alt="" class="w-100" style="aspect-ratio: 16/9; object-fit: cover;">
                                            <b-button @click="editable.ekstrimAtas = !editable.ekstrimAtas" size="sm" variant="primary-200" class="w-100">
                                                <b-icon icon="pencil-square" scale="0.7" />
                                                <span class="text-bold-16">Start Drawing</span>
                                            </b-button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div ref="Dada Pr" class="mt-32">
                        <ModernLabel label="Ekstremitas Bawah" />
                        <div class="mt-20">
                            <div v-if="editable.ekstrimBawah == true" class="w-100">
                                <EkstrimBawahView :drawing="pre_op_handover_form.ekstremitas_bawah" @save="saveekstrimBawah"/>
                            </div>
                            <div v-else class="w-100 mx-auto">
                                <div class="card">
                                    <div class="card-header bg-primary-500 text-semi-18 text-white">Ekstremitas Bawah</div>
                                    <div class="card-body d-flex bg-white">
                                        <div class="mx-auto col-lg-8">
                                            <img src="./../../../@core/assets/images/canvas/Ekstremitas Bawah.jpg" alt="" class="w-100" style="aspect-ratio: 16/9; object-fit: cover;">
                                            <b-button @click="editable.ekstrimBawah = !editable.ekstrimBawah" size="sm" variant="primary-200" class="w-100">
                                                <b-icon icon="pencil-square" scale="0.7" />
                                                <span class="text-bold-16">Start Drawing</span>
                                            </b-button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </section>

                    <!-- Current_tab = menu3 -->
                <section v-if="active.current_tab === 'menu3'" class="mt-24">
                    <b-card no-body>
                        <b-tabs card>
                            <b-tab active>
                                <template #title>
                                    <strong>Pra Operatif</strong>
                                </template>

                                <section>
                                <div class="card-header bg-primary text-white" style="margin: 0 -20px; font-size: 14px;">CATATAN KEPERAWATAN PRA OPERASI</div>
                                <div>
                                    <div class="">
                                    <ModernLabel label="Tanda-Tanda Vital" class="mt-20" />
                                    <div class="row">
                                        <div class="col-3 mt-12">
                                        <label class="c-form-label">Suhu</label>
                                        <b-input-group append="ᵒC">
                                            <b-form-input v-model="form3_pre.ttv_temperature" type="number" autocomplete="off" />
                                        </b-input-group>
                                        </div>
                                        <div class="col-3 mt-12">
                                        <label class="c-form-label">Nadi</label>
                                        <b-input-group append="x/mnt">
                                            <b-form-input v-model="form3_pre.ttv_pulse" type="number" autocomplete="off" />
                                        </b-input-group>
                                        </div>
                                        <div class="col-3 mt-12">
                                        <label class="c-form-label">RR</label>
                                        <!-- FIX: ditambahkan type="number" agar konsisten & sesuai kolom decimal(10,2) -->
                                        <b-form-input v-model="form3_pre.ttv_rr" type="number" autocomplete="off" />
                                        </div>
                                        <div class="col-3 mt-12">
                                        <label class="c-form-label">Systolic</label>
                                        <b-input-group append="mmHg">
                                            <b-form-input v-model="form3_pre.ttv_systolic" type="number" autocomplete="off" />
                                        </b-input-group>
                                        </div>
                                        <div class="col-3 mt-12">
                                        <label class="c-form-label">Diastolic</label>
                                        <b-input-group append="mmHg">
                                            <b-form-input v-model="form3_pre.ttv_diastolic" type="number" autocomplete="off" />
                                        </b-input-group>
                                        </div>
                                        <div class="col-3 mt-12">
                                        <label class="c-form-label">Skor Nyeri</label>
                                        <b-form-input v-model="form3_pre.ttv_sore_score" type="number" autocomplete="off" />
                                        </div>
                                        <div class="col-lg-3 mt-12">
                                        <label class="c-form-label">Berat Badan</label>
                                        <b-input-group append="kg">
                                            <b-form-input v-model="form3_pre.ttv_weight" type="number" />
                                        </b-input-group>
                                        </div>
                                        <div class="col-lg-3 mt-12">
                                        <label class="c-form-label">Tinggi Badan</label>
                                        <b-input-group append="cm">
                                            <b-form-input v-model="form3_pre.ttv_height" type="number" />
                                        </b-input-group>
                                        </div>
                                    </div>
                                    </div>
                                    <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mt-20">
                                        <ModernLabel label="Status Mental" />
                                        <b-form-radio-group v-model="form3_pre.mental_status_type_id" class="mt-10">
                                            <b-form-radio class="my-1" value="1">Sadar Penuh</b-form-radio>
                                            <b-form-radio class="my-1" value="2">Bingung</b-form-radio>
                                            <b-form-radio class="my-1" value="3">Agitasi</b-form-radio>
                                            <b-form-radio class="my-1" value="4">Mengantuk</b-form-radio>
                                            <b-form-radio class="my-1" value="5">Koma</b-form-radio>
                                        </b-form-radio-group>
                                        </div>
                                        <div class="mt-20">
                                        <ModernLabel label="Riwayat Penyakit" />
                                        <b-form-checkbox-group v-model="form3_pre.medical_history_arry" size="sm" class="mt-10">
                                            <b-form-checkbox value="a" class="my-1">Hipertensi</b-form-checkbox>
                                            <b-form-checkbox value="b" class="my-1">Diabetes</b-form-checkbox>
                                            <b-form-checkbox value="c" class="my-1">Hepatitis</b-form-checkbox>
                                            <b-form-checkbox value="d" class="my-1">Lain-lain</b-form-checkbox>
                                        </b-form-checkbox-group>
                                        <!-- FIX: tambah maxlength karena kolom DB varchar(150) -->
                                        <b-form-input
                                            v-model="form3_pre.medical_history"
                                            v-if="form3_pre.medical_history_arry.indexOf('d') !== -1"
                                            maxlength="150"
                                            class="mt-10"
                                        />
                                        </div>
                                        <div class="mt-20">
                                        <ModernLabel label="Pengobatan Saat Ini" />
                                        <b-textarea v-model="form3_pre.current_treatment" class="mt-10" />
                                        </div>
                                        <div class="mt-20">
                                        <ModernLabel label="Alat bantu yang digunakan" />
                                        <b-textarea v-model="form3_pre.tools_used" class="mt-10" />
                                        </div>
                                        <div class="mt-20">
                                        <ModernLabel label="Batuk / Flu / Demam" />
                                        <b-form-radio-group v-model="form3_pre.cough_flu_fever_type_id" class="mt-10">
                                            <b-form-radio class="my-1" value="0">Tidak</b-form-radio>
                                            <b-form-radio class="my-1" value="1">Ya</b-form-radio>
                                        </b-form-radio-group>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mt-12 mt-lg-0">
                                        <div class="mt-20">
                                        <ModernLabel label="Operasi Sebelumnya" />
                                        <div class="">
                                            <div class="mt-10">
                                            <label class="c-form-label">Jenis Operasi</label>
                                            <!--
                                                FIX: prev_operations_type_id di DB = tinyint(1), berarti seharusnya
                                                ID pilihan (mis. dari master jenis operasi), BUKAN teks bebas.
                                                Ganti ke b-form-select / b-form-radio-group begitu daftar opsinya
                                                sudah dikonfirmasi ke BE. Sementara ini dibiarkan text input dan
                                                DIBERI CATATAN agar tidak lupa saat integrasi BE.
                                            -->
                                            <b-form-input v-model="form3_pre.prev_operations_type_id" autocomplete="off" />
                                            <small class="text-muted">*Sementara text bebas — perlu diganti jadi pilihan ID (tinyint) sesuai master data BE</small>
                                            </div>
                                            <div class="mt-12">
                                            <label class="c-form-label">Kapan</label>
                                            <b-form-input v-model="form3_pre.prev_operations_date" type="date" autocomplete="off" />
                                            </div>
                                            <div class="mt-12">
                                            <label class="c-form-label">Di</label>
                                            <b-form-input v-model="form3_pre.prev_operations_place" maxlength="150" autocomplete="off" />
                                            </div>
                                        </div>
                                        </div>
                                        <div class="mt-20">
                                        <ModernLabel label="Alergi" />
                                        <b-form-radio-group v-model="form3_pre.allergy_type_id" class="mt-10">
                                            <b-form-radio class="my-1" value="1">Tidak ada</b-form-radio>
                                            <b-form-radio class="my-1" value="2">Tidak diketahui</b-form-radio>
                                            <b-form-radio class="my-1" value="3">Ada, Jelaskan</b-form-radio>
                                        </b-form-radio-group>
                                        <!-- FIX: tambah maxlength karena kolom DB varchar(150) -->
                                        <b-textarea
                                            v-model="form3_pre.allergy"
                                            v-if="form3_pre.allergy_type_id == 3"
                                            maxlength="150"
                                            class="mt-10"
                                        />
                                        </div>
                                        <div class="mt-20">
                                        <ModernLabel label="Bila pasien perempuan, apakah sedang haid / menstruasi" />
                                        <b-form-radio-group v-model="form3_pre.period_type_id" class="mt-10">
                                            <b-form-radio class="my-1" value="0">Tidak</b-form-radio>
                                            <b-form-radio class="my-1" value="1">Ya</b-form-radio>
                                        </b-form-radio-group>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                                </section>

                                <section class="mt-32">
                                <div class="card-header bg-primary text-white" style="margin: 0 -20px; font-size: 14px;">CEKLIST PERSIAPAN PRA OPERASI</div>

                                <section class="mt-20">
                                    <ModernLabel label="I. VERIFIKASI PASIEN" />
                                    <b-table-simple responsive bordered small class="mt-10" style="font-size: 13px">
                                    <b-thead>
                                        <b-tr>
                                        <b-th class="align-middle text-center" style="width: 50px">No</b-th>
                                        <b-th class="align-middle">Item</b-th>
                                        <b-th class="align-middle text-center" style="width: 120px">Perawat Pengirim</b-th>
                                        <b-th class="align-middle text-center" style="width: 120px">Perawat Penerima</b-th>
                                        <b-th class="align-middle text-center" style="width: 30%">Keterangan</b-th>
                                        </b-tr>
                                    </b-thead>
                                    <b-tbody>
                                        <b-tr v-for="(item, idx) in patientVerificationItems" :key="item.key">
                                        <b-td class="text-center">{{ idx + 1 }}</b-td>
                                        <b-td>{{ item.label }}</b-td>
                                        <b-td class="text-center">
                                            <b-form-checkbox-group v-model="form3_pre.patient_verification[item.key + '_arry']" size="lg">
                                            <b-form-checkbox value="a" class="m-0"></b-form-checkbox>
                                            </b-form-checkbox-group>
                                        </b-td>
                                        <b-td class="text-center">
                                            <b-form-checkbox-group v-model="form3_pre.patient_verification[item.key + '_arry']" size="lg">
                                            <b-form-checkbox value="b" class="m-0"></b-form-checkbox>
                                            </b-form-checkbox-group>
                                        </b-td>
                                        <b-td>
                                            <b-textarea v-model="form3_pre.patient_verification[item.key]" />
                                        </b-td>
                                        </b-tr>
                                    </b-tbody>
                                    </b-table-simple>
                                </section>

                                <section class="mt-32">
                                    <ModernLabel label="II. PERSIAPAN FISIK PASIEN" />
                                    <b-table-simple responsive bordered small class="mt-10" style="font-size: 13px">
                                    <b-thead>
                                        <b-tr>
                                        <b-th class="align-middle text-center" style="width: 50px">No</b-th>
                                        <b-th class="align-middle">Item</b-th>
                                        <b-th class="align-middle text-center" style="width: 120px">Perawat Pengirim</b-th>
                                        <b-th class="align-middle text-center" style="width: 120px">Perawat Penerima</b-th>
                                        <b-th class="align-middle text-center" style="width: 30%">Keterangan</b-th>
                                        </b-tr>
                                    </b-thead>
                                    <b-tbody>
                                        <b-tr v-for="(item, idx) in physicalPreparationItems" :key="item.key">
                                        <b-td class="text-center">{{ idx + 1 }}</b-td>
                                        <b-td>{{ item.label }}</b-td>
                                        <b-td class="text-center">
                                            <b-form-checkbox-group v-model="form3_pre.physical_preparation[item.key + '_arry']" size="lg">
                                            <b-form-checkbox value="a" class="m-0"></b-form-checkbox>
                                            </b-form-checkbox-group>
                                        </b-td>
                                        <b-td class="text-center">
                                            <b-form-checkbox-group v-model="form3_pre.physical_preparation[item.key + '_arry']" size="lg">
                                            <b-form-checkbox value="b" class="m-0"></b-form-checkbox>
                                            </b-form-checkbox-group>
                                        </b-td>
                                        <b-td>
                                            <b-textarea v-model="form3_pre.physical_preparation[item.key]" />
                                        </b-td>
                                        </b-tr>
                                    </b-tbody>
                                    </b-table-simple>

                                    <div>
                                    <label class="c-form-label">Diperiksa oleh</label>

                                    <div class="row">
                                        <div class="col-lg-6 input-group mb-3">
                                        <span class="input-group-text rounded-0">Perawat ruangan</span>
                                        <div class="col-lg-6">
                                        <v-select v-model="form3_pre.checked_room_nurse_id" :options="doctor_list" :reduce="doctor_list => doctor_list.value" label="title" placeholder="-- Perawat ruangan --"></v-select>
                                        </div>
                                        <span class="input-group-text rounded-0">Jam</span>
                                        <!--
                                            FIX: kolom DB adalah TIME, bukan DATETIME.
                                            Ganti input dari type="datetime-local" menjadi type="time"
                                            supaya tidak menyimpan komponen tanggal yang tidak perlu / tidak
                                            sesuai kolom.
                                        -->
                                        <input v-model="form3_pre.checked_room_nurse_hours" type="time" class="form-control" />
                                        </div>
                                        <div class="col-lg-6 input-group mb-3">
                                        <span class="input-group-text rounded-left">Perawat UKO</span>
                                        <div class="col-lg-6">
                                            <v-select v-model="form3_pre.checked_uko_nurse_id" :options="doctor_list" :reduce="doctor_list => doctor_list.value" label="title" placeholder="-- Perawat UKO --"></v-select>
                                        </div>
                                        <span class="input-group-text rounded-0">Jam</span>
                                        <input v-model="form3_pre.checked_uko_nurse_hours" type="time" class="form-control" />
                                        </div>
                                    </div>
                                    </div>
                                </section>
                                </section>

                                <div class="card-footer" style="margin: 20px -20px -20px -20px">
                                    <div class="d-flex justify-content-end">
                                        <b-button variant="primary" class="px-3" @click="submit_preoperative_nursing_form()">
                                        <feather-icon icon="SaveIcon" /> Simpan
                                        </b-button>
                                    </div>
                                </div>
                            </b-tab>
                            
                            <b-tab>
                                <template #title>
                                    <strong>Intra Operatif</strong>
                                </template>
                                
                                <section class="text-uppercase">
                                    <div class="row">
                                        <div class="col-lg-4 py-3 general-box">
                                            <label class="c-form-label font-weight-bold">Time Out</label>
                                            <b-form-radio-group v-model="form3_intra.timeout_type_id" class="">
                                                <b-form-radio class="my-1" value="0">Tidak</b-form-radio>
                                                <b-form-radio class="my-1" value="1">Ya</b-form-radio>
                                            </b-form-radio-group>
                                            <b-form-input v-model="form3_intra.timeout" v-if="form3_intra.timeout_type_id == 1" class="mt-12" />
                                        </div>
                                    </div>
                                    <hr class="d-none d-lg-block">

                                    <ModernLabel label="Cek ketersediaan peralatan & fungsinya" />
                                    <div class="row">
                                        <div class="col-lg-4 py-3 general-box">
                                            <label class="c-form-label font-weight-bold">Instrument</label>
                                            <b-form-radio-group v-model="form3_intra.instrument_type_id" size="sm" stacked class="">
                                                <b-form-radio class="my-1" value="0">Tidak</b-form-radio>
                                                <b-form-radio class="my-1" value="1">Ya</b-form-radio>
                                            </b-form-radio-group>
                                            <b-form-input v-model="form3_intra.instrument" v-if="form3_intra.instrument_type_id == 1" class="mt-12" />
                                        </div>
                                        <div class="col-lg-4 py-3">
                                            <div>
                                                <label class="c-form-label font-weight-bold">Prostheses / Implant</label>
                                                <b-form-radio-group v-model="form3_intra.prosthesis_implant_type_id" size="sm" stacked class="">
                                                    <b-form-radio class="my-1" value="0">Tidak</b-form-radio>
                                                    <b-form-radio class="my-1" value="1">Ya</b-form-radio>
                                                </b-form-radio-group>
                                                <b-form-input v-model="form3_intra.prosthesis_implant" v-if="form3_intra.prosthesis_implant_type_id == 1" class="mt-12" />
                                            </div>
                                        </div>
                                        <div class="col-lg-4 py-3 general-box">
                                            <div class="">
                                                <label class="c-form-label font-weight-bold">Mulai Operasi</label>
                                                <b-form-input v-model="form3_intra.operation_start_time" type="time" />
                                            </div>
                                            <div class="mt-12">
                                                <label class="c-form-label font-weight-bold">Selesai Operasi</label>
                                                <b-form-input v-model="form3_intra.operation_finish_time" type="time" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-4 py-3 general-box">
                                            <div class="">
                                                <label class="c-form-label font-weight-bold">Mulai Anestesi</label>
                                                <b-form-input v-model="form3_intra.anesthesia_start_time" type="time" />
                                            </div>
                                            <div class="mt-12">
                                                <label class="c-form-label font-weight-bold">Selesai Anestesi</label>
                                                <b-form-input v-model="form3_intra.anesthesia_finish_time" type="time" />
                                            </div>
                                        </div>
                                        <div class="col-lg-4 py-3">
                                            <div class="">
                                                <label class="c-form-label font-weight-bold">Dilakukan operasi / jenis operasi</label>
                                                <b-form-input v-model="form3_intra.operation_performed"/>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 py-3 general-box">
                                            <div>
                                                <label class="c-form-label font-weight-bold">Tipe operasi</label>
                                                <b-form-radio-group v-model="form3_intra.operation_type_id" size="sm" stacked>
                                                    <b-form-radio class="my-1" value="1">Efektif</b-form-radio>
                                                    <b-form-radio class="my-1" value="2">Darurat</b-form-radio>
                                                    <b-form-radio class="my-1" value="3">Operasi ODS</b-form-radio>
                                                </b-form-radio-group>
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="d-none d-lg-block">

                                    <div class="row">
                                        <div class="col-lg-4 py-3 general-box">
                                            <div class="">
                                                <label class="c-form-label font-weight-bold">Tipe pembiusan</label>
                                                <b-form-radio-group v-model="form3_intra.anesthesia_type_id" size="sm" stacked>
                                                    <b-form-radio class="my-1" value="1">Umum</b-form-radio>
                                                    <b-form-radio class="my-1" value="2">Lokal</b-form-radio>
                                                    <b-form-radio class="my-1" value="3">Regional</b-form-radio>
                                                    <b-form-radio class="my-1" value="4">Blok perifer</b-form-radio>
                                                </b-form-radio-group>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 py-3 mid-box">
                                            <div class="">
                                                <label class="c-form-label font-weight-bold">Tingkat kesadaran waktu masuk kamar operasi</label>
                                                <b-form-radio-group v-model="form3_intra.level_of_awareness_type_id" size="sm" stacked class="w-100">
                                                    <b-form-radio class="my-1 w-100" value="1">
                                                        <span>E</span>
                                                        <b-form-input v-model="form3_intra.level_of_awareness_e" v-if="form3_intra.level_of_awareness_type_id == 1" class="mt-10" />
                                                    </b-form-radio>
                                                    <b-form-radio class="my-1 w-100" value="2">
                                                        <span>M</span>
                                                        <b-form-input v-model="form3_intra.level_of_awareness_m" v-if="form3_intra.level_of_awareness_type_id == 2" class="mt-10" />
                                                    </b-form-radio>
                                                    <b-form-radio class="my-1 w-100" value="3">
                                                        <div>V</div>
                                                        <b-form-input v-model="form3_intra.level_of_awareness_v" v-if="form3_intra.level_of_awareness_type_id == 3" class="mt-10" />
                                                    </b-form-radio>
                                                </b-form-radio-group>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 py-3 general-box">
                                            <div class="">
                                                <label class="c-form-label font-weight-bold">Status emosi waktu masuk kamar operasi</label>
                                                <b-form-radio-group v-model="form3_intra.emotional_status_type_id" size="sm" stacked>
                                                    <b-form-radio class="my-1" value="1">Rileks</b-form-radio>
                                                    <b-form-radio class="my-1" value="2">Gelisah</b-form-radio>
                                                    <b-form-radio class="my-1" value="3">Tidak ada respon</b-form-radio>
                                                </b-form-radio-group>
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="d-none d-lg-block">

                                    <div class="row">
                                        <div class="col-lg-4 py-3 general-box">
                                            <div class="">
                                                <label class="c-form-label font-weight-bold">Posisi kanula intra vena</label>
                                                <b-form-radio-group v-model="form3_intra.cannula_position_type_id" size="sm" stacked>
                                                    <b-form-radio class="my-1" value="1">Tangan kanan / kiri </b-form-radio>
                                                    <b-form-radio class="my-1" value="2">Kaki kanan / kiri </b-form-radio>
                                                    <b-form-radio class="my-1" value="3">Artline line</b-form-radio>
                                                    <b-form-radio class="my-1" value="4">CVP</b-form-radio>
                                                    <b-form-radio class="my-1" value="5">Lain-lain</b-form-radio>
                                                </b-form-radio-group>
                                                <b-form-input v-model="form3_intra.cannula_position" v-if="form3_intra.cannula_position_type_id == '5'" class="mt-10" />
                                            </div>
                                        </div>
                                        <div class="col-lg-4 py-3 mid-box">
                                            <div class="">
                                                <label class="c-form-label font-weight-bold">Operasi diawasi oleh</label>
                                                <v-select v-model="form3_intra.operation_supervised_by_id" :options="doctor_list" :reduce="doctor_list => doctor_list.value" label="title" placeholder="-- pilih --"></v-select>
                                            </div>
                                            <div class="mt-12">
                                                <label class="c-form-label font-weight-bold">Posisi Operasi</label>
                                                <b-form-radio-group v-model="form3_intra.operation_position_type_id" size="sm" stacked>
                                                    <b-form-radio class="my-1" value="1">Telentang</b-form-radio>
                                                    <b-form-radio class="my-1" value="2">Lithotomy</b-form-radio>
                                                    <b-form-radio class="my-1" value="3">Tengkurap</b-form-radio>
                                                    <b-form-radio class="my-1" value="4">Lateral Ka/Ki</b-form-radio>
                                                    <b-form-radio class="my-1" value="5">Lain-lain</b-form-radio>
                                                </b-form-radio-group>
                                                <b-form-input v-model="form3_intra.operation_position" v-if="form3_intra.operation_position_type_id == '5'" class="mt-10" />
                                            </div>
                                        </div>
                                        <div class="col-lg-4 py-3 general-box">
                                            <div class="">
                                                <label class="c-form-label font-weight-bold">Posisi Lengan</label>
                                                <b-form-radio-group v-model="form3_intra.arm_position_type_id" size="sm" stacked>
                                                    <b-form-radio class="my-1" value="1">Lengan terentang Ka/Ki</b-form-radio>
                                                    <b-form-radio class="my-1" value="2">Lengan terlipat Ka/Ki</b-form-radio>
                                                    <b-form-radio class="my-1" value="3">Lain-lain</b-form-radio>
                                                </b-form-radio-group>
                                                <b-form-input v-model="form3_intra.arm_position" v-if="form3_intra.arm_position_type_id == '3'" class="mt-10" />
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="d-none d-lg-block">

                                    <div class="row">
                                        <div class="col-lg-4 py-3 general-box">
                                            <div class="">
                                                <label class="c-form-label font-weight-bold">Posisi alat bantu</label>
                                                <b-form-checkbox-group v-model="form3_intra.aids_position_type_id" size="sm" stacked >
                                                    <b-form-checkbox value="a" class="my-1">Papan lengan penyanggah</b-form-checkbox>
                                                    <b-form-checkbox value="b" class="my-1">Lain-lain</b-form-checkbox>
                                                </b-form-checkbox-group>
                                                <b-form-input v-model="form3_intra.aids_position" v-if="form3_intra.aids_position_type_id.indexOf('b') !== -1" class="mt-10" />
                                            </div>
                                        </div>
                                        <div class="col-lg-4 py-3 mid-box">
                                            <div class="">
                                                <label class="c-form-label font-weight-bold">Memakai kateter urin</label>
                                                <b-form-radio-group v-model="form3_intra.urinary_catheter_type_id" size="sm" stacked>
                                                    <b-form-radio class="my-1" value="1">Tidak</b-form-radio>
                                                    <b-form-radio class="my-1" value="2">Dalam kamar operasi</b-form-radio>
                                                    <b-form-radio class="my-1" value="3">Di ruangan</b-form-radio>
                                                </b-form-radio-group>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 py-3 general-box">
                                            <div class="">
                                                <label class="c-form-label font-weight-bold">Persiapan Kulit</label>
                                                <b-form-radio-group v-model="form3_intra.skin_preparation_type_id" size="sm" stacked>
                                                    <b-form-radio class="my-1" value="1">Ohlorhexidine / 70%</b-form-radio>
                                                    <b-form-radio class="my-1" value="2">Povidone-lodine</b-form-radio>
                                                    <b-form-radio class="my-1" value="3">Hibiscrub</b-form-radio>
                                                    <b-form-radio class="my-1" value="4">Lain-lain</b-form-radio>
                                                </b-form-radio-group>
                                                <b-form-input v-model="form3_intra.skin_preparation" v-if="form3_intra.skin_preparation_type_id == '4'" class="mt-10" />
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="d-none d-lg-block">

                                    <ModernLabel label="Pemakaian diathermy" />
                                    <div class="row">
                                        <div class="col-lg-4 py-3 general-box">
                                            <div class="">
                                                <label class="c-form-label font-weight-bold">Pemakaian diathermy</label>
                                                <b-form-radio-group v-model="form3_intra.diathermy_type_id" size="sm" stacked>
                                                    <b-form-radio class="my-1" value="1">Tidak</b-form-radio>
                                                    <b-form-radio class="my-1" value="2">Monopolar</b-form-radio>
                                                    <b-form-radio class="my-1" value="3">Bipolar</b-form-radio>
                                                </b-form-radio-group>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 py-3 mt-2">
                                            <div class="">
                                                <label class="c-form-label font-weight-bold">Lokasi dari dipersive elektrode</label>
                                                <b-form-radio-group v-model="form3_intra.dispersive_electrode_type_id" size="sm" stacked>
                                                    <b-form-radio class="my-1" value="1">Bokong Ka/Ki</b-form-radio>
                                                    <b-form-radio class="my-1" value="2">Paha Ka/Ki</b-form-radio>
                                                    <b-form-radio class="my-1" value="3">Lain-lain</b-form-radio>
                                                </b-form-radio-group>
                                                <b-form-input v-model="form3_intra.dispersive_electrode" v-if="form3_intra.dispersive_electrode_type_id == '3'" class="mt-10" />
                                            </div>
                                        </div>
                                        <div class="col-lg-4 py-3 general-box">
                                            <div class="">
                                                <label class="c-form-label font-weight-bold">Pemeriksaan kondisi kulit sebelum operasi</label>
                                                <b-form-radio-group v-model="form3_intra.skin_condition_before_type_id" size="sm" stacked>
                                                    <b-form-radio class="my-1" value="1">Utuh</b-form-radio>
                                                    <b-form-radio class="my-1" value="2">Menggelembung</b-form-radio>
                                                    <b-form-radio class="my-1" value="3">Lain-lain</b-form-radio>
                                                </b-form-radio-group>
                                                <b-form-input v-model="form3_intra.skin_condition_before" v-if="form3_intra.skin_condition_before_type_id == '3'" class="mt-10" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-4 py-3 general-box">
                                            <div class="">
                                                <label class="c-form-label font-weight-bold">Pemeriksaan kondisi kulit sesudah operasi</label>
                                                <b-form-radio-group v-model="form3_intra.skin_condition_after_type_id" size="sm" stacked>
                                                    <b-form-radio class="my-1" value="1">Utuh</b-form-radio>
                                                    <b-form-radio class="my-1" value="2">Menggelembung</b-form-radio>
                                                    <b-form-radio class="my-1" value="3">Lain-lain</b-form-radio>
                                                </b-form-radio-group>
                                                <b-form-input v-model="form3_intra.skin_condition_after" v-if="form3_intra.skin_condition_after_type_id == '3'" class="mt-10" />
                                            </div>
                                        </div>
                                        <!-- <div class="col-lg-4 py-3 mid-box">
                                            <div class="">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 py-3 general-box">
                                            <div class="">
                                            </div>
                                        </div> -->
                                    </div>
                                    <hr class="d-none d-lg-block">
                                    
                                    <ModernLabel label="Unit pemanas / pendingin operasi" />
                                    <div class="row">
                                        <div class="col-lg-4 py-3 general-box">
                                            <div class="">
                                                <label class="c-form-label font-weight-bold">Unit pemanas / pendingin operasi</label>
                                                <b-form-radio-group v-model="form3_intra.heating_cooling_unit_type_id" size="sm" stacked>
                                                    <b-form-radio class="my-1" value="0">Tidak</b-form-radio>
                                                    <b-form-radio class="my-1" value="1">Ya</b-form-radio>
                                                </b-form-radio-group>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 py-3" v-if="form3_intra.heating_cooling_unit_type_id == '1'">
                                            <div class="">
                                                <label class="c-form-label font-weight-bold">Pengaturan temperatur</label>
                                                <b-input-group append="ᵒC">
                                                    <b-form-input v-model="form3_intra.temperature"/>
                                                </b-input-group>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 py-3 general-box" v-if="form3_intra.heating_cooling_unit_type_id == '1'">
                                            <div class="">
                                                <div class="">
                                                    <label class="c-form-label font-weight-bold">Jam mulai</label>
                                                    <b-form-input type="time" v-model="form3_intra.temperature_start_time"/>
                                                </div>
                                                <div class="mt-12">
                                                    <label class="c-form-label font-weight-bold">Jam selesai</label>
                                                    <b-form-input type="time" v-model="form3_intra.temperature_finish_time"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="d-none d-lg-block">
                                    
                                    <div class="row">
                                        <div class="p-3 general-box">
                                            <div class="">
                                                <ModernLabel label="Pemakaian torniquet" />
                                                <b-table-simple fixed bordered class="mt-12">
                                                    <b-thead>
                                                        <b-tr>
                                                            <b-th>Lokasi</b-th>
                                                            <b-th>Waktu Mulai</b-th>
                                                            <b-th>Waktu Selesai</b-th>
                                                            <b-th>Tekanan</b-th>
                                                        </b-tr>
                                                    </b-thead>
                                                    <b-tbody>
                                                        <b-tr>
                                                            <b-td>Lengan Kanan</b-td>
                                                            <b-td><b-form-input type="time" v-model="form3_intra.tourniquet.tourniquet_right_arm_start_time"/></b-td>
                                                            <b-td><b-form-input type="time" v-model="form3_intra.tourniquet.tourniquet_right_arm_finish_time"/></b-td>
                                                            <b-td><b-form-input v-model="form3_intra.tourniquet.tourniquet_right_arm_pressure"/></b-td>
                                                        </b-tr>
                                                        <b-tr>
                                                            <b-td>Kaki Kanan</b-td>
                                                            <b-td><b-form-input type="time" v-model="form3_intra.tourniquet.tourniquet_left_arm_start_time"/></b-td>
                                                            <b-td><b-form-input type="time" v-model="form3_intra.tourniquet.tourniquet_left_arm_finish_time"/></b-td>
                                                            <b-td><b-form-input v-model="form3_intra.tourniquet.tourniquet_left_arm_pressure"/></b-td>
                                                        </b-tr>
                                                        <b-tr>
                                                            <b-td>Lengan Kiri</b-td>
                                                            <b-td><b-form-input type="time" v-model="form3_intra.tourniquet.tourniquet_right_foot_start_time"/></b-td>
                                                            <b-td><b-form-input type="time" v-model="form3_intra.tourniquet.tourniquet_right_foot_finish_time"/></b-td>
                                                            <b-td><b-form-input v-model="form3_intra.tourniquet.tourniquet_right_foot_pressure"/></b-td>
                                                        </b-tr>
                                                        <b-tr>
                                                            <b-td>Kaki Kiri</b-td>
                                                            <b-td><b-form-input type="time" v-model="form3_intra.tourniquet.tourniquet_left_foot_start_time"/></b-td>
                                                            <b-td><b-form-input type="time" v-model="form3_intra.tourniquet.tourniquet_left_foot_finish_time"/></b-td>
                                                            <b-td><b-form-input v-model="form3_intra.tourniquet.tourniquet_left_foot_pressure"/></b-td>
                                                        </b-tr>
                                                    </b-tbody>
                                                </b-table-simple>
                                            </div>
                                            <div class="mt-12">
                                                <label class="c-form-label font-weight-bold">Pemakaian torniquet diawasi oleh</label>
                                                <v-select v-model="form3_intra.tourniquet_supervised_by_id" :options="doctor_list" :reduce="doctor_list => doctor_list.value" label="title" placeholder="-- pilih --"></v-select>
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="d-none d-lg-block">
                                    
                                    <ModernLabel label="Pemakaian Laser" />
                                    <div class="row">
                                        <div class="col-lg-4 py-3 general-box">
                                            <div class="">
                                                <label class="c-form-label font-weight-bold">Kode model</label>
                                                <b-form-input v-model="form3_intra.use_of_laser_code"/>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 py-3">
                                            <div class="">
                                                <label class="c-form-label font-weight-bold">Pemakaian laser diawasi oleh</label>
                                                <v-select v-model="form3_intra.laser_supervised_by_id" :options="doctor_list" :reduce="doctor_list => doctor_list.value" label="title" placeholder="-- pilih --"></v-select>
                                            </div>
                                        </div>
                                        <!-- <div class="col-lg-4 py-3 general-box">
                                            <div class="">

                                            </div>
                                        </div> -->
                                    </div>
                                    <hr class="d-none d-lg-block">
                                    
                                    <ModernLabel label="Pemakaian Implant" />
                                    <div class="row">
                                        <div class="col-lg-4 py-3 general-box">
                                            <div class="">
                                                <label class="c-form-label">Pemakaian implant</label>
                                                <b-form-radio-group v-model="form3_intra.use_of_implants_type_id" size="sm" stacked>
                                                    <b-form-radio class="my-1" value="0">Tidak</b-form-radio>
                                                    <b-form-radio class="my-1" value="1">Ya</b-form-radio>
                                                </b-form-radio-group>
                                            </div>
                                            <div v-if="form3_intra.use_of_implants_type_id == '1'" class="mt-12">
                                                <label class="c-form-label">Kadaluarsa</label>
                                                <b-form-radio-group v-model="form3_intra.implants_expired_date" size="sm" stacked>
                                                    <b-form-radio class="my-1" value="0">Tidak</b-form-radio>
                                                    <b-form-radio class="my-1" value="1">Ya</b-form-radio>
                                                </b-form-radio-group>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 py-3">
                                            <div v-if="form3_intra.use_of_implants_type_id == '1'" class="">
                                                <label class="c-form-label">Pabrik</label>
                                                <b-form-input v-model="form3_intra.implants_factory" />
                                            </div>
                                            <div v-if="form3_intra.use_of_implants_type_id == '1'" class="mt-12">
                                                <label class="c-form-label">Type</label>
                                                <b-form-input v-model="form3_intra.implants_type" />
                                            </div>
                                        </div>
                                        <div class="col-lg-4 py-3 general-box">
                                            <div v-if="form3_intra.use_of_implants_type_id == '1'" class="">
                                                <label class="c-form-label">Size</label>
                                                <b-form-input v-model="form3_intra.implants_size" />
                                            </div>
                                            <div v-if="form3_intra.use_of_implants_type_id == '1'" class="mt-12">
                                                <label class="c-form-label">No Seri</label>
                                                <b-form-input v-model="form3_intra.implants_no" />
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="d-none d-lg-block">

                                    <div class="row">
                                        <div class="p-3 general-box">
                                            <div class="">
                                                <ModernLabel label="Pemakaian Drain" />
                                                <b-table :fields="DrainFields" :items="form3_intra.drain_usage" bordered fixed class="mt-12">
                                                    <template #cell(action)="row">
                                                        <b-button variant="danger-600" size="sm" @click="removeDrain(row.index)">
                                                            <feather-icon icon="TrashIcon" />
                                                        </b-button>
                                                    </template>
                                                </b-table>
                                            </div>
                                            <div>
                                                <div class="row">
                                                    <div class="col-lg-3 mt-12">
                                                        <label class="c-form-label">Tipe drain</label>
                                                        <b-form-input v-model="input_drain.drain_type" />
                                                    </div>
                                                    <div class="col-lg-3 mt-12">
                                                        <label class="c-form-label">Jenis drain</label>
                                                        <b-form-input v-model="input_drain.drain_class"/>
                                                    </div>
                                                    <div class="col-lg-3 mt-12">
                                                        <label class="c-form-label">Ukuran</label>
                                                        <b-form-input v-model="input_drain.drain_size"/>
                                                    </div>
                                                    <div class="col-lg-3 mt-12">
                                                        <label class="c-form-label">Keterangan</label>
                                                        <b-form-input v-model="input_drain.drain_notes"/>
                                                    </div>
                                                </div>
                                                <b-button @click="addDrain" variant="primary" class="mt-12">Tambah</b-button>
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="d-none d-lg-block">

                                    <div class="row">
                                        <div class="col-lg-4 py-3 general-box">
                                            <div class="">
                                                <label class="c-form-label">Irigasi Luka</label>
                                                <b-form-checkbox-group v-model="form3_intra.wound_irrigation_arry" size="sm" stacked >
                                                    <b-form-checkbox value="a" class="my-1">Sodium Chloride 0,9%</b-form-checkbox>
                                                    <b-form-checkbox value="b" class="my-1">Antibiotik Spray</b-form-checkbox>
                                                    <b-form-checkbox value="c" class="my-1">Antibiotik</b-form-checkbox>
                                                    <b-form-checkbox value="d" class="my-1">H2O2</b-form-checkbox>
                                                    <b-form-checkbox value="e" class="my-1">Lain-lain</b-form-checkbox>
                                                </b-form-checkbox-group>
                                                <b-form-input v-model="form3_intra.wound_irrigation" v-if="form3_intra.wound_irrigation_arry.indexOf('e') !== -1" class="mt-10" />
                                            </div>
                                        </div>
                                        <div class="col-lg-4 py-3 mid-box">
                                            <div class="">
                                                <label class="c-form-label">Pemakaian Cairan</label>
                                                <b-form-checkbox-group v-model="form3_intra.fluid_usage_arry" size="sm" stacked >
                                                    <b-form-checkbox value="a" class="my-1">
                                                        <div>Gylsin</div>
                                                        <b-input-group append="Liter" v-if="form3_intra.fluid_usage_arry.indexOf('a') !== -1">
                                                            <b-form-input v-model="form3_intra.fluid_usage_glycine"/>
                                                        </b-input-group>
                                                    </b-form-checkbox>
                                                    <b-form-checkbox value="b" class="my-1">
                                                        <div>Sterile water</div>
                                                        <b-input-group append="Liter" v-if="form3_intra.fluid_usage_arry.indexOf('b') !== -1">
                                                            <b-form-input v-model="form3_intra.fluid_usage_sterile_water"/>
                                                        </b-input-group>
                                                    </b-form-checkbox>
                                                    <b-form-checkbox value="c" class="my-1">
                                                        <div>Air untuk irigasi</div>
                                                        <b-input-group append="Liter" v-if="form3_intra.fluid_usage_arry.indexOf('c') !== -1">
                                                            <b-form-input v-model="form3_intra.fluid_usage_water_irrigation"/>
                                                        </b-input-group>
                                                    </b-form-checkbox>
                                                    <b-form-checkbox value="d" class="my-1">
                                                        <div>Sodium Chloride 0,9%</div>
                                                        <b-input-group append="Liter" v-if="form3_intra.fluid_usage_arry.indexOf('d') !== -1">
                                                            <b-form-input v-model="form3_intra.fluid_usage_sodium_chloride"/>
                                                        </b-input-group>
                                                    </b-form-checkbox>
                                                    <b-form-checkbox value="e" class="my-1">
                                                        <div>Lain-lain</div>
                                                        <b-input-group append="Liter" v-if="form3_intra.fluid_usage_arry.indexOf('e') !== -1">
                                                            <b-form-input v-model="form3_intra.fluid_usage_other"/>
                                                        </b-input-group>
                                                    </b-form-checkbox>
                                                </b-form-checkbox-group>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 py-3 general-box">
                                            <div class="">
                                                <label class="c-form-label">Alat-alat terbungkus</label>
                                                <b-form-radio-group v-model="form3_intra.tools_wrapped_type_id" size="sm" stacked>
                                                    <b-form-radio class="my-1" value="0">Tidak</b-form-radio>
                                                    <b-form-radio class="my-1" value="1">Ya</b-form-radio>
                                                </b-form-radio-group>
                                            </div>
                                            <template v-if="form3_intra.tools_wrapped_type_id == '1'">
                                                <div class="mt-12">
                                                    <label class="c-form-label">Jenis</label>
                                                    <b-form-input v-model="form3_intra.tools_wrapped_type"/>
                                                </div>
                                                <div class="mt-12">
                                                    <label class="c-form-label">Lain-lain</label>
                                                    <b-form-input v-model="form3_intra.tools_wrapped_other"/>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                    <hr class="d-none d-lg-block">

                                    <div class="row">
                                        <div class="col-lg-4 py-3 general-box">
                                            <div class="">
                                                <label class="c-form-label">Balutan</label>
                                                <b-form-radio-group v-model="form3_intra.wrapping_type_id" size="sm" stacked>
                                                    <b-form-radio class="my-1" value="0">Tidak ada</b-form-radio>
                                                    <b-form-radio class="my-1" value="1">Pressure</b-form-radio>
                                                </b-form-radio-group>
                                                <b-form-input v-model="form3_intra.wrapping_type" v-if="form3_intra.wrapping_type_id == '1'" class="mt-10" />
                                            </div>
                                        </div>
                                        <div class="col-lg-4 py-3 mid-box">
                                            <div class="">
                                                <label class="c-form-label">Spesimen</label>
                                                <b-form-radio-group v-model="form3_intra.specimen_type_id" size="sm" stacked>
                                                    <b-form-radio class="my-1" value="1">
                                                        <div>Histologi</div>
                                                        <b-form-input v-model="form3_intra.specimen_histology" v-if="form3_intra.specimen_type_id == '1'" placeholder="Jenis Histologi" class="mt-10" />
                                                    </b-form-radio>
                                                    <b-form-radio class="my-1" value="2">
                                                        <div>Kultur</div>
                                                        <b-form-input v-model="form3_intra.specimen_culture" v-if="form3_intra.specimen_type_id == '2'" placeholder="Jenis Kultur" class="mt-10" />
                                                    </b-form-radio>
                                                    <b-form-radio class="my-1" value="3">Frozen Section</b-form-radio>
                                                    <b-form-radio class="my-1" value="4">
                                                        <div>Sitologi</div>
                                                        <b-form-input v-model="form3_intra.specimen_cytology" v-if="form3_intra.specimen_type_id == '4'" placeholder="Jenis Sitologi" class="mt-10" />
                                                    </b-form-radio>
                                                    <b-form-radio class="my-1" value="5">
                                                        <div>Lain-lain</div>
                                                        <template v-if="form3_intra.specimen_type_id == '5'">
                                                            <b-form-input v-model="form3_intra.specimen_other" class="mt-10" />
                                                            <b-form-input v-model="form3_intra.specimen_other_type" placeholder="Jenis" class="mt-10" />
                                                        </template>
                                                    </b-form-radio>
                                                </b-form-radio-group>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 py-3 general-box">
                                            <div class="">
                                                <label class="c-form-label"><b>Jumlah Total Jaringan</b> Cairan pemeriksaan</label>
                                                <b-form-input v-model="form3_intra.amount_of_fluid_examination"/>
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="d-none d-lg-block">
                                                                
                                </section>

                                <div class="card-footer" style="margin: 20px -20px -20px -20px">
                                    <div class="d-flex justify-content-end">
                                        <b-button variant="primary" class="px-3" @click="submit_intraoperative_nursing_form()">
                                            <feather-icon icon="SaveIcon" /> Simpan
                                        </b-button>
                                    </div>
                                </div>
                            </b-tab>
                            
                            <b-tab>
                                <template #title>
                                    <strong>Pasca Operatif</strong>
                                </template>

                                <section>                        
                                    <div class="row">
                                        <div class="col-lg-4 py-3 general-box">
                                            <div class="">
                                                <label class="c-form-label font-weight-bold">Rawat pasca operasi</label>
                                                <b-form-radio-group v-model="form3_post.postoperative_care_type_id" size="sm" stacked>
                                                    <b-form-radio class="my-1" value="1">Ruang rawat / ODS</b-form-radio>
                                                    <b-form-radio class="my-1" value="2">ICU / PICU</b-form-radio>
                                                    <b-form-radio class="my-1" value="3">Peristi level III</b-form-radio>
                                                </b-form-radio-group>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 py-3 mid-box">
                                            <div class="">
                                                <label class="c-form-label">Transport</label>
                                                <b-form-radio-group v-model="form3_post.transport_type_id" size="sm" stacked>
                                                    <b-form-radio class="my-1" value="1">Stretcher</b-form-radio>
                                                    <b-form-radio class="my-1" value="2">Kursi roda</b-form-radio>
                                                    <b-form-radio class="my-1" value="3">tempat tidur</b-form-radio>
                                                </b-form-radio-group>
                                            </div>
                                        </div>
                                        <!-- <div class="col-lg-4 py-3 general-box">
                                            <div class="">
                                            </div>
                                        </div> -->
                                    </div>
                                    <hr class="d-none d-lg-block">
                                    
                                    <ModernLabel label="Pengkajian pasca operasi" />
                                    <div class="row">
                                        <div class="col-lg-4 py-3 general-box">
                                            <div class="">
                                                <label class="c-form-label">Keadaan umum</label>
                                                <b-form-radio-group v-model="form3_post.general_condition_type_id" size="sm" stacked>
                                                    <b-form-radio class="my-1" value="1">Baik</b-form-radio>
                                                    <b-form-radio class="my-1" value="2">Sedang</b-form-radio>
                                                    <b-form-radio class="my-1" value="3">Berat</b-form-radio>
                                                </b-form-radio-group>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 py-3 mid-box">
                                            <div class="">
                                                <label class="c-form-label">GCS</label>
                                                <b-form-radio-group v-model="form3_post.gcs_type_id" size="sm" stacked>
                                                    <b-form-radio class="my-1" value="1">
                                                        <div>E</div>
                                                        <b-form-input v-model="form3_post.gcs_e" v-if="form3_post.gcs_type_id == '1'" class="mt-10" />
                                                    </b-form-radio>
                                                    <b-form-radio class="my-1" value="2">
                                                        <div>M</div>
                                                        <b-form-input v-model="form3_post.gcs_m" v-if="form3_post.gcs_type_id == '2'" class="mt-10" />
                                                    </b-form-radio>
                                                    <b-form-radio class="my-1" value="3">
                                                        <div>V</div>
                                                        <b-form-input v-model="form3_post.gcs_v" v-if="form3_post.gcs_type_id == '3'" class="mt-10" />
                                                    </b-form-radio>
                                                </b-form-radio-group>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 py-3 general-box">
                                            <div class="">
                                                <label class="c-form-label">Jalan nafas</label>
                                                <b-form-radio-group v-model="form3_post.airway_type_id" size="sm" stacked>
                                                    <b-form-radio class="my-1" value="0">Tidak ada masalah</b-form-radio>
                                                    <b-form-radio class="my-1" value="1">Ada masalah</b-form-radio>
                                                </b-form-radio-group>
                                                <b-form-input v-model="form3_post.airway_problem" v-if="form3_post.airway_type_id == '1'" class="mt-10" />
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="d-none d-lg-block">

                                    <div class="row">
                                        <div class="col-lg-4 py-3 general-box">
                                            <div class="">
                                                <label class="c-form-label">Pernafasan</label>
                                                <b-form-radio-group v-model="form3_post.breathing_type_id" size="sm" stacked>
                                                    <b-form-radio class="my-1" value="1">Spontan</b-form-radio>
                                                    <b-form-radio class="my-1" value="2">Dibantu</b-form-radio>
                                                </b-form-radio-group>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 py-3 mid-box">
                                            <div class="">
                                                <label class="c-form-label">Terapi Oksigen</label>
                                                <b-form-radio-group v-model="form3_post.oxygen_therapy_type_id" size="sm" stacked>
                                                    <b-form-radio class="my-1" value="1">Tidak</b-form-radio>
                                                    <b-form-radio class="my-1" value="2">O2 nasal / NRM</b-form-radio>
                                                    <b-form-radio class="my-1" value="3">Lainnya</b-form-radio>
                                                </b-form-radio-group>
                                                <b-form-input v-model="form3_post.oxygen_therapy_other" v-if="form3_post.oxygen_therapy_type_id == '3'" class="mt-10" />
                                            </div>
                                        </div>
                                        <div class="col-lg-4 py-3 general-box">
                                            <div class="">
                                                <label class="c-form-label">Kulit datang</label>
                                                <b-form-radio-group v-model="form3_post.skin_arrive_type_id" size="sm" stacked>
                                                    <b-form-radio class="my-1" value="1">Kering / Lembap</b-form-radio>
                                                    <b-form-radio class="my-1" value="2">Merah muda / biru</b-form-radio>
                                                    <b-form-radio class="my-1" value="3">Hangat / dingin</b-form-radio>
                                                    <b-form-radio class="my-1" value="4">Lainnya</b-form-radio>
                                                </b-form-radio-group>
                                                <b-form-input v-model="form3_post.skin_arrive_other" v-if="form3_post.skin_arrive_type_id == '4'" class="mt-10" />
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="d-none d-lg-block">

                                    <div class="row">
                                        <div class="col-lg-4 py-3 general-box">
                                            <div class="">
                                                <label class="c-form-label">Kulit keluar</label>
                                                <b-form-radio-group v-model="form3_post.skin_exit_type_id" size="sm" stacked>
                                                    <b-form-radio class="my-1" value="1">Kering / Lembap</b-form-radio>
                                                    <b-form-radio class="my-1" value="2">Merah muda / biru</b-form-radio>
                                                    <b-form-radio class="my-1" value="2">Hangat / dingin</b-form-radio>
                                                    <b-form-radio class="my-1" value="3">Lainnya</b-form-radio>
                                                </b-form-radio-group>
                                                <b-form-input v-model="form3_post.skin_exit_other" v-if="form3_post.skin_exit_type_id == '3'" class="mt-10" />
                                            </div>
                                        </div>
                                        <div class="col-lg-4 py-3 mid-box">
                                            <div class="">
                                                <label class="c-form-label">Sirkulasi</label>
                                                <b-form-radio-group v-model="form3_post.circulation_type_id" size="sm" stacked>
                                                    <b-form-radio class="my-1" value="1">Merah muda</b-form-radio>
                                                    <b-form-radio class="my-1" value="2">kebiru-biruan</b-form-radio>
                                                </b-form-radio-group>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 py-3 general-box">
                                            <div class="">
                                                <label class="c-form-label">Posisi</label>
                                                <b-form-radio-group v-model="form3_post.position_type_id" size="sm" stacked>
                                                    <b-form-radio class="my-1" value="1">Lateral Ka/Ki</b-form-radio>
                                                    <b-form-radio class="my-1" value="2">Tersanggah keatas</b-form-radio>
                                                    <b-form-radio class="my-1" value="3">Lainnya</b-form-radio>
                                                </b-form-radio-group>
                                                <b-form-input v-model="form3_post.position_other" v-if="form3_post.position_type_id == '3'" class="mt-10" />
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="d-none d-lg-block">

                                    <!-- <div class="row">
                                        <div class="col-lg-4 py-3 general-box">
                                            <div class="">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 py-3 mid-box">
                                            <div class="">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 py-3 general-box">
                                            <div class="">
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="d-none d-lg-block"> -->
                                    
                                    <div class="mt-12">
                                        <label class="c-form-label">Pupil</label>
                                        <b-form-radio-group v-model="form3_post.pupil_type_id" class="">
                                            <b-form-radio class="my-1" value="1">Isokor</b-form-radio>
                                            <b-form-radio class="my-1" value="2">Anisokor</b-form-radio>
                                        </b-form-radio-group>
                                    </div>
                                </section>

                                <div class="card-footer" style="margin: 20px -20px -20px -20px">
                                    <div class="d-flex justify-content-end">
                                        <b-button variant="primary" class="px-3" @click="submit_postoperative_nursing_form()">
                                            <feather-icon icon="SaveIcon" /> Simpan
                                        </b-button>
                                    </div>
                                </div>
                            </b-tab>
                        </b-tabs>
                    </b-card>
                </section>

                    <!-- Current_tab = operational_nursing -->
                <section v-if="active.current_tab === 'menu4'" class="mt-24">
                    <b-card no-body>
                        <b-tabs card>
                            <b-tab active>
                                <template #title>
                                    <strong>SIGN IN</strong>
                                </template>
                                
                                <section>
                                    <div class="row">
                                        <div class="col-lg-4 py-3 mt-2 general-box">
                                            <label class="c-form-label font-weight-bold text-uppercase">KONFRIRMASI / VERIFIKASI</label>
                                            <b-form-checkbox-group v-model="form4_signin.confirmation_arry" size="sm" stacked>
                                                <b-form-checkbox class="mr-0" value="a">IDENTIFIKASI DAN CEK GELANG PASIEN</b-form-checkbox>
                                                <b-form-checkbox class="mr-0" value="b">INFORMED CONSENT</b-form-checkbox>
                                            </b-form-checkbox-group>
                                        </div>
                                        <div class="col-lg-4 py-3 mt-2 mid-box">
                                            <div>
                                                <label class="c-form-label font-weight-bold text-uppercase">NAMA OPERATOR</label>
                                                <v-select v-model="form4_signin.operator_id" :options="doctor_list" :reduce="doctor_list => doctor_list.value" label="title" placeholder="-- Operator --"></v-select>
                                            </div>
                                            <div class="mt-12">
                                                <label class="c-form-label font-weight-bold text-uppercase">LOKASI OPERASI</label>
                                                <b-form-input v-model="form4_signin.location_operation" />
                                            </div>
                                        </div>
                                        <div class="col-lg-4 py-3 mt-2 general-box">
                                            <div>
                                                <label class="c-form-label font-weight-bold text-uppercase">PROSEDUR OPERASI</label>
                                                <b-textarea v-model="form4_signin.operating_procedure" />
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="d-none d-lg-block">
                                    <div class="row">
                                        <div class="col-lg-4 py-3 mt-2 general-box">
                                            <div>
                                                <label class="c-form-label font-weight-bold text-uppercase">TANDA DAERAH OPERASI</label>
                                                <b-form-radio-group v-model="form4_signin.operating_area_type_id" stacked size="sm">
                                                    <b-form-radio value="0">TIDAK DAPAT DITERAPKAN</b-form-radio>
                                                    <b-form-radio value="1">YA</b-form-radio>
                                                </b-form-radio-group>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 py-3 mt-2">
                                            <div>
                                                <b-form-checkbox :value="1" :unchecked-value="0" size="sm" class="mt-12" v-model="form4_signin.is_check_completeness">PERIKSA KELENGKAPAN ANESTESI (IV LINE, OBAT-OABATAN, DLL)</b-form-checkbox>
                                                <b-form-checkbox :value="1" :unchecked-value="0" size="sm" class="mt-12" v-model="form4_signin.is_check_anesthesia">PENEGECEKAN MESIN ANESTESI</b-form-checkbox>
                                                <b-form-checkbox :value="1" :unchecked-value="0" size="sm" class="mt-12" v-model="form4_signin.is_check_tools">PENGECEKAN ALAT / INSTRUMENT</b-form-checkbox>
                                                <b-form-checkbox :value="1" :unchecked-value="0" size="sm" class="mt-12" v-model="form4_signin.is_check_sterility">PENGECEKAN KESTERILAN INSTRUMEN</b-form-checkbox>
                                                <b-form-checkbox :value="1" :unchecked-value="0" size="sm" class="mt-12" v-model="form4_signin.is_fasting">PUASA MULAI PUKUL</b-form-checkbox>
                                                <b-form-input v-if="form4_signin.is_fasting == 1" v-model="form4_signin.fasting_hours" type="time" class="mt-10" />
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="d-none d-lg-block">
                                    <div class="row">
                                        <div class="col-lg-4 py-3 mt-2 general-box">
                                            <div>
                                                <label class="c-form-label font-weight-bold text-uppercase">RIWAYAT ASTMA / RISIKO ASPIRASI / KESULITAN JALAN NAFAS</label>
                                                <b-form-radio-group v-model="form4_signin.history_asthma_type_id" stacked size="sm">
                                                    <b-form-radio value="0">TIDAK ADA</b-form-radio>
                                                    <b-form-radio value="1">ADA, PERSIAPAN BANTUAN TERSEDIA</b-form-radio>
                                                </b-form-radio-group>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 py-3 mt-2 mid-box">
                                            <div>
                                                <label class="c-form-label font-weight-bold text-uppercase">RIWAYAT ALERGI</label>
                                                <div class="">
                                                    <b-form-radio-group v-model="form4_signin.allergy_history_type_id" stacked size="sm">
                                                        <b-form-radio value="0">TIDAK</b-form-radio>
                                                        <b-form-radio value="1">
                                                            <div>ADA, NAMA OBAT</div>
                                                        </b-form-radio>
                                                    </b-form-radio-group>
                                                    <b-form-input v-if="form4_signin.allergy_history_type_id == 1" v-model="form4_signin.allergy_history_name" placeholder="nama obat" class="mt-10" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 py-3 mt-2 general-box">
                                            <div>
                                                <label class="c-form-label font-weight-bold text-uppercase">PERALATAN KHUSUS YANG DIPERLUKAN (IMPLAN, DLL)</label>
                                                <b-form-radio-group v-model="form4_signin.special_equipment_type_id" stacked size="sm">
                                                    <b-form-radio value="0">TIDAK</b-form-radio>
                                                    <b-form-radio value="1">
                                                        <div>ADA, NAMA OBAT</div>
                                                    </b-form-radio>
                                                </b-form-radio-group>
                                                <b-form-input v-if="form4_signin.special_equipment_type_id == 1" v-model="form4_signin.special_equipment_name" placeholder="nama obat" class="mt-10" />
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="d-none d-lg-block">
                                    <div class="row">
                                        <div class="col-lg-4 py-3 mt-2 general-box">
                                            <div>
                                                <label class="c-form-label font-weight-bold text-uppercase">DOKUMEN HASIL PENUNJANG</label>
                                                <b-form-checkbox-group v-model="form4_signin.supporting_results_arry" size="sm" stacked class="">
                                                    <b-form-checkbox value="a">LABORATORIUM</b-form-checkbox>
                                                    <b-form-checkbox value="b">RADIOLOGI</b-form-checkbox>
                                                    <b-form-checkbox value="c">APAKAH IMAGING C-ARM DIPERLUKAN</b-form-checkbox>
                                                </b-form-checkbox-group>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 py-3 mt-2 mid-box">
                                            <div>
                                                <label class="c-form-label font-weight-bold text-uppercase">RISIKO KEHILANGAN DARAH > 500 cc ATAU 7 ml/Kg BB (ANAK)</label>
                                                <b-form-radio-group v-model="form4_signin.blood_loss_type_id" stacked size="sm">
                                                    <b-form-radio value="0">TIDAK</b-form-radio>
                                                    <b-form-radio value="1">YA</b-form-radio>
                                                </b-form-radio-group>
                                            </div>
                                            <template v-if="form4_signin.blood_loss_type_id == 1">
                                                <div class="mt-12">
                                                    <label class="c-form-label font-weight-bold text-uppercase">AKSES IV NO</label>
                                                    <b-form-input v-model="form4_signin.access_iv_no" />
                                                </div>
                                                <div class="mt-12">
                                                    <label class="c-form-label font-weight-bold text-uppercase">PERSIAPAN DARAH</label>
                                                    <b-input-group append="ml">
                                                        <b-form-input type="number" v-model="form4_signin.blood_preparation" />
                                                    </b-input-group>
                                                </div>
                                                <div class="mt-12">
                                                    <label class="c-form-label font-weight-bold text-uppercase">JENIS</label>
                                                    <b-form-input v-model="form4_signin.blood_type" />
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </section>
                                <div class="card-footer" style="margin: 20px -20px -20px -20px">
                                    <div class="d-flex justify-content-end">
                                        <b-button variant="primary" class="px-3" @click="submit_signin_form()">
                                            <feather-icon icon="SaveIcon" /> Simpan
                                        </b-button>
                                    </div>
                                </div>
                            </b-tab>
                            <b-tab>
                                <template #title>
                                    <strong>TIME OUT</strong>
                                </template>
                                
                                <section>
                                    <div class="row">
                                        <div class="col-lg-4 py-3 mt-2 general-box">
                                            <div>
                                                <label class="c-form-label font-weight-bold text-uppercase">KELENGKAPAN TIM OPERASI DAN PERANNYA</label>
                                                <b-form-radio-group v-model="form4_timeout.completeness_team_type_id" stacked size="sm">
                                                    <b-form-radio value="0">TIDAK LENGKAP</b-form-radio>
                                                    <b-form-radio value="1">LENGKAP</b-form-radio>
                                                </b-form-radio-group>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 py-3 mt-2 mid-box">
                                            <label class="c-form-label font-weight-bold text-uppercase">KONFIRMASI DENGAN MEMBACAKAN SECARA VERBAL</label>
                                            <b-form-checkbox-group v-model="form4_timeout.reading_verbally_arry" stacked size="sm">
                                                <b-form-checkbox class="my-1" value="a">IDENTIFIKASI PASIEN</b-form-checkbox>
                                                <b-form-checkbox class="my-1" value="b">TANGGAL OPERASI</b-form-checkbox>
                                                <b-form-checkbox class="my-1" value="c">LOKASI OPERASI</b-form-checkbox>
                                                <b-form-checkbox class="my-1" value="d">NAMA DAN TIM OPERASI</b-form-checkbox>
                                                <b-form-checkbox class="my-1" value="e">PROSEDUR OPERASI</b-form-checkbox>
                                                <b-form-checkbox class="my-1" value="f">INFORMED CONSENT</b-form-checkbox>
                                            </b-form-checkbox-group>
                                        </div>
                                        <div class="col-lg-4 py-3 mt-2 general-box">
                                            <div class="">
                                                <label class="c-form-label font-weight-bold text-uppercase">PREMEDIKASI</label>
                                                <b-form-input v-model="form4_timeout.premedication"/>
                                            </div>
                                            <div class="mt-12">
                                                <label class="c-form-label font-weight-bold text-uppercase">DIBERIKAN JAM</label>
                                                <b-form-input type="time" v-model="form4_timeout.given_hours"/>
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="d-none d-lg-block">
                                    <div class="row">
                                        <div class="col-lg-4 py-3 mt-2 general-box">
                                            <div class="">
                                                <label class="c-form-label font-weight-bold text-uppercase">ANTIBIOTIK PROHILAXIX 60' SEBELUM OPERASI</label>
                                                <b-form-input v-model="form4_timeout.prohilaxix" />
                                            </div>
                                            <div class="mt-12">
                                                <label class="c-form-label font-weight-bold text-uppercase">DIBERIKASAN JAM</label>
                                                <b-form-input v-model="form4_timeout.prohilaxix_hours" type="time"/>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 py-3 mt-2 mid-box">
                                            <div class="">
                                                <label class="c-form-label font-weight-bold text-uppercase">PENGECEKAN ULANG STERILISASI ALAT DAN LINEN</label>
                                                <b-form-input v-model="form4_timeout.rechecking_sterilization"/>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 py-3 mt-2 general-box">
                                            <div>
                                                <label class="c-form-label font-weight-bold text-uppercase">FOTO RADIOLOGI YANG DIPERLUKAN </label>
                                                <b-form-radio-group v-model="form4_timeout.radiology_photo_type_id" stacked size="sm">
                                                    <b-form-radio value="0">TIDAK DIPASANG</b-form-radio>
                                                    <b-form-radio value="1">DIPASANG</b-form-radio>
                                                </b-form-radio-group>
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="d-none d-lg-block">
                                    <div class="row">
                                        <div class="col-lg-4 py-3 mt-2 general-box">
                                            <div class="">
                                                <label class="c-form-label font-weight-bold text-uppercase">PENGECEKAN</label>
                                                <b-textarea v-model="form4_timeout.checking"/>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 py-3 mt-2 mid-box">
                                            <div class="">
                                                <label class="c-form-label font-weight-bold text-uppercase">HAL YANG HARUS DIPERHATIKAN</label>
                                                <b-textarea v-model="form4_timeout.things_to_note"/>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                                <div class="card-footer" style="margin: 20px -20px -20px -20px">
                                    <div class="d-flex justify-content-end">
                                        <b-button variant="primary" class="px-3" @click="submit_timeout_form()">
                                            <feather-icon icon="SaveIcon" /> Simpan
                                        </b-button>
                                    </div>
                                </div>
                            </b-tab>
                            <b-tab>
                                <template #title>
                                    <strong>SIGN OUT</strong>
                                </template>
                                
                                <section class="">
                                    <div class="row">
                                        <div class="col-lg-4 py-3 mt-2 general-box">
                                            <div>
                                                <b-form-checkbox :value="1" :unchecked-value="0" size="sm" v-model="form4_signout.is_read_verbally" >MEMBACAKAN SECARA VERBAL TINDAKAN YANG TELAH DILAKUKAN</b-form-checkbox>
                                                <b-form-checkbox :value="1" :unchecked-value="0" size="sm" class="mt-12" v-model="form4_signout.is_read_amount" >MEMBACAKAN JUMLAH KASA DAN JARUM</b-form-checkbox>
                                                <b-form-checkbox :value="1" :unchecked-value="0" size="sm" class="mt-12" v-model="form4_signout.is_read_completeness" >MAMBACAKAN KELENGKAPAN INSTRUMEN</b-form-checkbox>
                                                <b-form-checkbox :value="1" :unchecked-value="0" size="sm" class="mt-12" v-model="form4_signout.is_read_completeness_document">MEMASTIKAN KELENGKAPAN DOKUMEN OPERASI SEBELUM KE RUANG PEMULIHAN</b-form-checkbox>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 py-3 mt-2 mid-box">
                                            <div>
                                                <label class="c-form-label font-weight-bold text-uppercase">THROAT PACK</label>
                                                <b-form-radio-group v-model="form4_signout.throat_pack_type_id" stacked size="sm">
                                                    <b-form-radio value="0">TIDAK ADA</b-form-radio>
                                                    <b-form-radio value="1">ADA</b-form-radio>
                                                </b-form-radio-group>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 py-3 mt-2 general-box">
                                            <div>
                                                <label class="c-form-label font-weight-bold text-uppercase">PENGANGKATAN JARINGAN </label>
                                                <b-form-radio-group v-model="form4_signout.network_lift_type_id" stacked size="sm">
                                                    <b-form-radio value="0">TIDAK ADA</b-form-radio>
                                                    <b-form-radio value="1">ADA</b-form-radio>
                                                </b-form-radio-group>
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="d-none d-lg-block">
                                    <div class="row">
                                        <div class="col-lg-4 py-3 mt-2 general-box">
                                            <div>
                                                <label class="c-form-label font-weight-bold text-uppercase">PEMASANGAN IMPLANT </label>
                                                <b-form-radio-group v-model="form4_signout.implant_installation_type_id" stacked size="sm">
                                                    <b-form-radio value="0">TIDAK</b-form-radio>
                                                    <b-form-radio value="1">ADA</b-form-radio>
                                                </b-form-radio-group>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 py-3 mt-2 mid-box">
                                            <div>
                                                <label class="c-form-label font-weight-bold text-uppercase">KERUSAKAN ALAT / INSTRUMEN</label>
                                                <b-form-radio-group v-model="form4_signout.tool_damaged_type_id" stacked size="sm">
                                                    <b-form-radio value="0">TIDAK ADA</b-form-radio>
                                                    <b-form-radio value="1">ADA</b-form-radio>
                                                </b-form-radio-group>
                                                <b-form-input v-if="form4_signout.tool_damaged_type_id == 1" v-model="form4_signout.tool_damaged"/>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 py-3 mt-2 general-box">
                                            <div>
                                                <label class="c-form-label font-weight-bold text-uppercase">KETERANGAN</label>
                                                <b-textarea v-model="form4_signout.information" />
                                            </div>
                                        </div>
                                    </div>
                                </section>
                                <div class="card-footer" style="margin: 20px -20px -20px -20px">
                                    <div class="d-flex justify-content-end">
                                        <b-button variant="primary" class="px-3" @click="submit_signout_form()">
                                            <feather-icon icon="SaveIcon" /> Simpan
                                        </b-button>
                                    </div>
                                </div>
                            </b-tab>
                        </b-tabs>
                    </b-card>
                </section>

                    <!-- Current_tab = menu5 -->
                <section v-if="active.current_tab === 'menu5'" class="mt-24">
                    <b-card no-body>
                        <b-tabs card>
                            <b-tab active>
                                <template #title>
                                    <strong>Evaluasi Pra-Anestesi</strong>
                                </template>
                                
                                <section>
                                    <div class="row">
                                        <div class="col-3 mt-12">
                                            <label class="c-form-label">Kesadaran</label>
                                            <v-select v-model="form5_eval.awareness" :options="OptionsAwareness" :reduce="OptionsAwareness => OptionsAwareness.value" label="title" placeholder="-- pilih --"></v-select>
                                        </div>
                                        <div class="col-lg-3 mt-12">
                                            <label>Berat Badan</label>
                                            <b-input-group append="kg">
                                                <b-form-input v-model="form5_eval.weight" type="number" />
                                            </b-input-group>
                                        </div>
                                        <div class="col-lg-3 mt-12">
                                            <label>Tinggi Badan</label>
                                            <b-input-group append="cm">
                                                <b-form-input v-model="form5_eval.height" type="number" />
                                            </b-input-group>
                                        </div>
                                        <div class="col-3 mt-12">
                                            <label>Systolic</label>
                                            <b-input-group append="mmHg">
                                                <b-form-input v-model="form5_eval.systolic" type="number" autocomplete="off" />
                                            </b-input-group>
                                        </div>
                                        <div class="col-3 mt-12">
                                            <label>Diastolic</label>
                                            <b-input-group append="mmHg">
                                                <b-form-input v-model="form5_eval.diastolic" type="number" autocomplete="off" />
                                            </b-input-group>
                                        </div>
                                        <div class="col-3 mt-12">
                                            <label class="c-form-label">Nadi</label>
                                            <b-input-group append="x/mnt">
                                                <b-form-input v-model="form5_eval.pulse" type="number" autocomplete="off" />
                                            </b-input-group>
                                        </div>
                                        <div class="col-3 mt-12">
                                            <label class="c-form-label">RR</label>
                                            <b-form-input v-model="form5_eval.respiratory_rate" autocomplete="off" />
                                        </div>
                                        <div class="col-3 mt-12">
                                            <label class="c-form-label">Suhu</label>
                                            <b-input-group append="ᵒC">
                                                <b-form-input v-model="form5_eval.temperature" type="number" autocomplete="off" />
                                            </b-input-group>
                                        </div>
                                        <div class="col-3 mt-12">
                                            <label class="c-form-label">CVP Sp02, dll</label>
                                            <b-form-input v-model="form5_eval.cvp_sp02" type="number" autocomplete="off" />
                                        </div>                            
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Riwayat komplikasi anestesi pada pasien / keluarga</label>
                                        <b-form-radio-group v-model="form5_eval.history_complications_type_id" size="sm">
                                            <b-form-radio value="0">TIDAK ADA</b-form-radio>
                                            <b-form-radio value="1">ADA</b-form-radio>
                                        </b-form-radio-group>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-3 mt-12">
                                            <label class="c-form-label">Gigi</label>
                                            <v-select v-model="form5_eval.tooth_type_id" :options="OptionsTooth" :reduce="OptionsTooth => OptionsTooth.value" label="title" placeholder="-- pilih --"></v-select>
                                        </div>
                                        <!-- kl gigi 'gigi palsu' -->
                                        <div class="col-lg-3 mt-12">
                                            <label class="c-form-label">Gigi palsu</label>
                                            <v-select v-model="form5_eval.dentures_type_id" :options="OptionsDentures" :reduce="OptionsDentures => OptionsDentures.value" label="title" placeholder="-- pilih --"></v-select>
                                        </div>
                                        <!-- kl gigi 'gigi palsu' -->
                                        <div class="col-lg-3 mt-12">
                                            <label class="c-form-label">Posisi</label>
                                            <v-select v-model="form5_eval.dentures_position_type_id" :options="OptionsDenturesPosition" :reduce="OptionsDenturesPosition => OptionsDenturesPosition.value" label="title" placeholder="-- pilih --"></v-select>
                                        </div>
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Jalan Nafas</label>
                                        <b-form-checkbox-group v-model="form5_eval.respiratory_path_type_id" size="sm" class="mt-12">
                                            <b-form-checkbox value="a">Tidak ada masalah yang terlihat</b-form-checkbox>
                                        </b-form-checkbox-group>
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Skor Mallampati</label>
                                        <b-form-radio-group v-model="form5_eval.mallampati_score_type_id" size="sm">
                                            <b-form-radio value="1">Class I</b-form-radio>
                                            <b-form-radio value="2">Class II</b-form-radio>
                                            <b-form-radio value="3">Class III</b-form-radio>
                                            <b-form-radio value="4">Class IV</b-form-radio>
                                        </b-form-radio-group>
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Respirasi</label>
                                        <b-form-checkbox-group v-model="form5_eval.respiration_arry" size="sm" class="mt-12">
                                            <b-form-checkbox value="a">Asthma</b-form-checkbox>
                                            <b-form-checkbox value="b">PPOK</b-form-checkbox>
                                            <b-form-checkbox value="c">ISPA</b-form-checkbox>
                                            <b-form-checkbox value="d">Riwayat ISPA</b-form-checkbox>
                                            <b-form-checkbox value="e">Sleep Apnea</b-form-checkbox>
                                            <b-form-checkbox value="f">Merokok</b-form-checkbox>
                                            <b-form-checkbox value="g">Riwayat Merokok</b-form-checkbox>
                                            <b-form-checkbox value="h">Lain-lain</b-form-checkbox>
                                        </b-form-checkbox-group>
                                    </div>
                                    <!-- kl respirasi 'merokok' / ' riwayat merokok' -->
                                    <div class="row">
                                        <div class="col-lg-3 mt-12">
                                            <label class="c-form-label">Konsumsi rokok per hari</label>
                                            <b-input-group append="batang">
                                                <b-form-input v-model="form5_eval.respiration_cigarrete_amount" type="number" autocomplete="off" />
                                            </b-input-group>
                                        </div>
                                        <div class="col-lg-3 mt-12">
                                            <label class="c-form-label">Selama</label>
                                            <b-input-group append="tahun">
                                                <b-form-input v-model="form5_eval.respiration_cigarrete_long" type="number" autocomplete="off" />
                                            </b-input-group>
                                        </div>
                                        <div class="col-lg-3 mt-12">
                                            <label class="c-form-label">Asthma</label>
                                            <b-input-group append="Asthma Berhenti">
                                                <b-form-input v-model="form5_eval.respiration_asthma" autocomplete="off" />
                                            </b-input-group>
                                        </div>
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Kardiovaskular</label>
                                        <b-form-checkbox-group v-model="form5_eval.cardiovascular_arry" size="sm" class="mt-12">
                                            <b-form-checkbox value="a">Normal</b-form-checkbox>
                                            <b-form-checkbox value="b">CAD</b-form-checkbox>
                                            <b-form-checkbox value="c">CHF</b-form-checkbox>
                                            <b-form-checkbox value="d">Disritmia</b-form-checkbox>
                                            <b-form-checkbox value="e">Hipertensi</b-form-checkbox>
                                            <b-form-checkbox value="f">Dislipidemia</b-form-checkbox>
                                            <b-form-checkbox value="g">MCI</b-form-checkbox>
                                            <b-form-checkbox value="h">Pacemaker</b-form-checkbox>
                                            <b-form-checkbox value="i">Penyakit Katup</b-form-checkbox>
                                            <b-form-checkbox value="j">Angina</b-form-checkbox>
                                            <b-form-checkbox value="k">
                                                <div>Lain-lain</div>
                                                <b-form-input v-model="form5_eval.cardiovascular_other" autocomplete="off" />
                                            </b-form-checkbox>
                                        </b-form-checkbox-group>
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Sistem pencernaan</label>
                                        <b-form-checkbox-group v-model="form5_eval.digestive_system_arry" size="sm" class="mt-12">
                                            <b-form-checkbox value="a">Normal</b-form-checkbox>
                                            <b-form-checkbox value="b">Mual / muntah</b-form-checkbox>
                                            <b-form-checkbox value="c">Drug / alcohol abuse</b-form-checkbox>
                                            <b-form-checkbox value="d">Penyakit Liver</b-form-checkbox>
                                            <b-form-checkbox value="e">
                                                <div>Lain-lain</div>
                                                <b-form-input v-model="form5_eval.digestive_system_other" autocomplete="off" />
                                            </b-form-checkbox>
                                        </b-form-checkbox-group>
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Neuro / Musculoskeletal</label>
                                        <b-form-checkbox-group v-model="form5_eval.neuromusculoskeletal_arry" size="sm" class="mt-12">
                                            <b-form-checkbox value="a">Normal</b-form-checkbox>
                                            <b-form-checkbox value="b">Artritis</b-form-checkbox>
                                            <b-form-checkbox value="c">Kejang</b-form-checkbox>
                                            <b-form-checkbox value="d">CVA / TIA</b-form-checkbox>
                                            <b-form-checkbox value="e">Kelumpuhan</b-form-checkbox>
                                            <b-form-checkbox value="f">Penyakit Neurimuskular</b-form-checkbox>
                                            <b-form-checkbox value="g">
                                                <div>Lain-lain</div>
                                                <b-form-input v-model="form5_eval.neuromusculoskeletal_other" autocomplete="off" />
                                            </b-form-checkbox>
                                        </b-form-checkbox-group>
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Ginjal</label>
                                        <b-form-checkbox-group v-model="form5_eval.kidney_arry" size="sm" class="mt-12">
                                            <b-form-checkbox value="a">Normal</b-form-checkbox>
                                            <b-form-checkbox value="b">Gagal ginjal / dialisis</b-form-checkbox>
                                            <b-form-checkbox value="c">DM</b-form-checkbox>
                                            <b-form-checkbox value="d">Riwayat Steroid</b-form-checkbox>
                                            <b-form-checkbox value="e">Riwayat Tiroid</b-form-checkbox>
                                        </b-form-checkbox-group>
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Lain-lain</label>
                                        <b-form-checkbox-group v-model="form5_eval.other_arry" size="sm" class="mt-12">
                                            <b-form-checkbox value="a">Koagulopati</b-form-checkbox>
                                            <b-form-checkbox value="b">Obesitas</b-form-checkbox>
                                            <b-form-checkbox value="c">Kehamilan</b-form-checkbox>
                                            <b-form-checkbox value="d">Riwayat Psikiatri</b-form-checkbox>
                                        </b-form-checkbox-group>
                                    </div>
                                    <div class="mt-20">
                                        <div>
                                            <ModernLabel label="Diagnosis (ICD10)" />
                                            <b-table-simple bordered responsive class="mt-12" style="font-size: 14px">
                                                <b-tr>
                                                    <b-th style="width: 60px;">No</b-th>
                                                    <b-th>ICD10</b-th>
                                                    <b-th style="width: 100px;">Aksi</b-th>
                                                </b-tr>
                                                <template v-for="(item,index) in form5_eval.icd10"> 
                                                    <b-tr class="item">
                                                        <b-td>{{ index+1 }}</b-td>
                                                        <b-td>
                                                            <div>{{ item.title }}</div>
                                                        </b-td>
                                                        <b-td>
                                                            <b-button variant="danger-600" size="sm" @click="deleteEval10(index)" class="mr-2">
                                                                <feather-icon icon="TrashIcon" />
                                                            </b-button>
                                                        </b-td>
                                                    </b-tr>
                                                </template>
                                            </b-table-simple>
                                            <div class="mt-10">
                                                <label class="text-semi-14">Diagnosa</label>
                                                <v-select v-model="evalIcd10_id" aria-readonly="true" @input="onAddEval10($event)" @search="onSearchEvalIcd10" :options="OptionsEvalIcd10" :reduce="OptionsEvalIcd10 => OptionsEvalIcd10" label="title" placeholder="-- Diagnosa --"></v-select>
                                            </div>
                                        </div>

                                        <b-textarea rows="2" type="text" v-model="form5_eval.icd10_notes" autocomplete="off" placeholder="Catatan ICD10" class="mt-12"></b-textarea>

                                        <div class="mt-24" >
                                            <ModernLabel label="Diagnosis Non-ICD10" />
                                            <b-table-simple bordered responsive class="mt-12" style="font-size: 14px">
                                                <b-tr>
                                                    <b-th style="width: 60px;">No</b-th>
                                                    <b-th>Non-ICD10</b-th>
                                                    <b-th style="width: 100px;">Aksi</b-th>
                                                </b-tr>
                                                <template v-for="(item,index) in form5_eval.non_icd10">
                                                    <b-tr class="item">
                                                        <b-td>{{ form5_eval.icd10.length+index+1 }}</b-td>
                                                        <b-td>
                                                            <div>{{ item }}</div>
                                                        </b-td>
                                                        <b-td>
                                                            <b-button variant="danger-600" size="sm" @click="removeEvalOther10(index)" class="mr-2">
                                                                <feather-icon icon="TrashIcon" />
                                                            </b-button>
                                                        </b-td>
                                                    </b-tr>
                                                </template> 
                                            </b-table-simple>
                                            <div class="mt-10">
                                                <label class="text-semi-14">Non-ICD10</label>
                                                <b-form-input v-model="input_other10" />
                                                <b-button @click="addEvalOther10" size="sm" variant="outline-primary" class="mt-12">Tambah</b-button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-20">
                                        <ModernLabel label="Tindakan (ICD9)" />
                                        <b-table :fields="ICD9TableFields" :items="form5_eval.icd9cm" responsive bordered class="mt-8" style="font-size: 13px;">
                                            <template #cell(no)="row">
                                                <div class="text-center">{{ row.index+1 }}</div>
                                            </template>
                                            <template #cell(action)="row">
                                                <div class="d-flex justify-content-center">
                                                    <b-button variant="danger-600" size="sm" @click="deleteEval9cm(row.index)">
                                                        <feather-icon icon="TrashIcon" />
                                                    </b-button>
                                                </div>
                                            </template>
                                        </b-table>
                                        <v-select v-model="evalIcd9cm_id" @input="onAddEval9cm($event)" aria-readonly="true" @search="onSearchEvalIcd9cm" :options="OptionsEvalIcd9cm" :reduce="OptionsEvalIcd9cm => OptionsEvalIcd9cm" label="title" placeholder="-- Pilih ICD9CM --" class="mt-12"></v-select>

                                    <b-textarea rows="2" type="text" v-model="form5_eval.therapy_plan" autocomplete="off" placeholder="Catatan ICD9CM" class="mt-12"></b-textarea>
                                </div>
                                <div class="mt-12">
                                    <div>
                                        <label class="c-form-label font-weight-bold">Obat yang sedang di konsumsi</label>
                                        <b-textarea v-model="form5_eval.medications_currently" />
                                    </div>
                                </div>
                                <div class="mt-12">
                                    <div>
                                        <label class="c-form-label font-weight-bold">Alergi obat / makanan</label>
                                        <b-textarea v-model="form5_eval.allergy" />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-3 mt-12">
                                        <label class="c-form-label font-weight-bold">Makan terakhir pukul</label>
                                        <b-form-input v-model="form5_eval.last_meal_hours" type="time" />
                                    </div>
                                    <div class="col-lg-3 mt-12">
                                        <label class="c-form-label font-weight-bold">Minum terakhir pukul</label>
                                        <b-form-input v-model="form5_eval.last_drink_hours" type="time" />
                                    </div>
                                </div>
                                <div class="">
                                    <label class="c-form-label">Rencana Anestesi</label>
                                    <b-form-checkbox-group v-model="form5_eval.anesthesia_plan_arry" size="sm">
                                        <b-form-checkbox value="a">ASA</b-form-checkbox>
                                        <b-form-checkbox value="b">Rencana</b-form-checkbox>
                                    </b-form-checkbox-group>
                                </div>
                                <!-- kl rencana anestesi ASA -->
                                <div class="mt-12">
                                    <label class="c-form-label">ASA</label>
                                    <b-form-radio-group v-model="form5_eval.anesthesia_asa_type_id" size="sm">
                                        <b-form-radio value="1">1</b-form-radio>
                                        <b-form-radio value="2">2</b-form-radio>
                                        <b-form-radio value="3">3</b-form-radio>
                                        <b-form-radio value="4">4</b-form-radio>
                                        <b-form-radio value="5">5</b-form-radio>
                                        <b-form-radio value="6">6</b-form-radio>
                                        <b-form-radio value="7">E</b-form-radio>
                                    </b-form-radio-group>
                                </div>
                                <!-- kl rencana anestesi 'rencanan' -->
                                <div class="mt-12">
                                    <label class="c-form-label">Rencana</label>
                                    <b-form-radio-group v-model="form5_eval.anesthesia_plan_type_id" size="sm">
                                        <b-form-radio value="1">GA</b-form-radio>
                                        <b-form-radio value="2">REG</b-form-radio>
                                        <b-form-radio value="3">IV Sed</b-form-radio>
                                        <b-form-radio value="4">L/MAC</b-form-radio>
                                    </b-form-radio-group>
                                </div>
                                <div class="mt-12">
                                    <label class="c-form-label">Premidikasi</label>
                                    <b-table :fields="premedicationFields" :items="form5_eval.premedication">
                                        <template #cell(no)="row">{{ row.index+1 }}</template>
                                        <template #cell(action)="row">
                                            <div class="d-flex justify-content-center">
                                                <b-button variant="danger-600"  @click="removePremedicationEval()" size="sm" >
                                                    <feather-icon icon="TrashIcon" />
                                                </b-button>
                                            </div>
                                        </template>
                                    </b-table>
                                    <div class="row">
                                        <div class="col-3 mt-12">
                                            <label class="c-form-label">Obat</label>
                                            <b-form-input v-model="input_premedication.drug" autocomplete="off"></b-form-input>
                                        </div>
                                        <div class="col-3 mt-12">
                                            <label class="c-form-label">Waktu</label>
                                            <b-form-input v-model="input_premedication.time" type="datetime-local" autocomplete="off"></b-form-input>
                                        </div>
                                    </div>
                                    <b-button @click="addPremedicationEval()" variant="primary" class="mt-8">Tambah</b-button>
                                </div>
                            </section>
                            <div class="card-footer" style="margin: 20px -20px -20px -20px">
                                <div class="d-flex justify-content-end">
                                    <b-button variant="primary" class="px-3" @click="submit_eval_form()">
                                        <feather-icon icon="SaveIcon" /> Simpan
                                    </b-button>
                                </div>
                            </div>
                            </b-tab>
                            <b-tab>
                                <template #title>
                                    <strong>Catatan Anestesi</strong>
                                </template>
                                <section>
                                    <div class="">
                                        <label class="c-form-label">Prioritas</label>
                                        <b-form-radio-group v-model="form5_notes.priority_type_id" size="sm">
                                            <b-form-radio value="1">Elektif</b-form-radio>
                                            <b-form-radio value="2">Emergensi</b-form-radio>
                                            <b-form-radio value="4">Elektif Tambahan</b-form-radio>
                                        </b-form-radio-group>
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Tanggal / jam pemeriksaan</label>
                                        <b-form-input v-model="form5_notes.examination_date" type="datetime-local" />
                                    </div>
                                    <div class="mt-12">                        
                                        <label class="c-form-label">Rencana Tindakan (ICD9)</label>
                                        <b-table :fields="ICD9TableFields" :items="form5_eval.icd9cm" responsive bordered class="mt-8" style="font-size: 13px;">
                                            <template #cell(no)="row">
                                                <div class="text-center">{{ row.index+1 }}</div>
                                            </template>
                                            <template #cell(action)="row">
                                                <div class="d-flex justify-content-center">
                                                    <b-button variant="danger-600" size="sm" @click="deleteEval9cm(row.index)">
                                                        <feather-icon icon="TrashIcon" />
                                                    </b-button>
                                                </div>
                                            </template>
                                        </b-table>
                                        <v-select v-model="evalIcd9cm_id" @input="onAddEval9cm($event)" aria-readonly="true" @search="onSearchEvalIcd9cm" :options="OptionsEvalIcd9cm" :reduce="OptionsEvalIcd9cm => OptionsEvalIcd9cm" label="title" placeholder="-- Pilih ICD9CM --" class="mt-12"></v-select>
                                        <b-textarea rows="2" type="text" v-model="form5_notes.therapy_plan" autocomplete="off" placeholder="Catatan ICD9CM" class="mt-12"></b-textarea>
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Tanggal tindakan</label>
                                        <b-form-input type="date" v-model="form5_notes.action_date"/>
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Operator</label>
                                        <v-select v-model="form5_notes.operator_id" :options="doctor_list" :reduce="doctor_list => doctor_list.value" label="title" placeholder="-- Operator --"></v-select>
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Spesialis anestesi</label>
                                        <v-select v-model="form5_notes.anesthesia_specialist" :options="doctor_list" :reduce="doctor_list => doctor_list.value" label="title" placeholder="-- Operator --"></v-select>
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Klasifikasi Mallampati</label>
                                        <b-form-radio-group v-model="form5_notes.mallampati_classification_type_id" size="sm">
                                            <b-form-radio value="1">Class I</b-form-radio>
                                            <b-form-radio value="2">Class II</b-form-radio>
                                            <b-form-radio value="3">Class III</b-form-radio>
                                            <b-form-radio value="4">Class IV</b-form-radio>
                                        </b-form-radio-group>
                                    </div>
                                    <div class="mt-20">
                                        <b-table-simple bordered fixed>
                                            <b-thead>
                                                <b-tr>
                                                    <b-th>Pemeriksaan Fisik</b-th>
                                                    <b-th>DBN</b-th>
                                                    <b-th>Keterangan</b-th>                                        
                                                </b-tr>
                                            </b-thead>
                                            <b-tbody>
                                                <b-tr>
                                                    <b-td>
                                                        <div class="">
                                                            <label class="c-form-label">Respirasi</label>
                                                            <b-form-checkbox-group v-model="form5_notes.respiration_arry" size="sm" stacked>
                                                                <b-form-checkbox value="a">Asma</b-form-checkbox>
                                                                <b-form-checkbox value="b">Bronkitis</b-form-checkbox>
                                                                <b-form-checkbox value="c">Pneumonia</b-form-checkbox>
                                                                <b-form-checkbox value="d">Tuberkulosis</b-form-checkbox>
                                                            </b-form-checkbox-group>
                                                        </div>
                                                    </b-td>
                                                    <b-td>
                                                        <b-form-checkbox v-model="form5_notes.respiration_dbn" :value="1" :unchecked-value="0" size="sm">DBN</b-form-checkbox>
                                                    </b-td>
                                                    <b-td>
                                                        <b-textarea v-model="form5_notes.respiration_notes"/>
                                                    </b-td>
                                                </b-tr>
                                                <b-tr>
                                                    <b-td>
                                                        <div class="">
                                                            <label class="c-form-label">cardiovascular</label>
                                                            <b-form-checkbox-group v-model="form5_notes.cardiovascular_arry" size="sm" stacked>
                                                                <b-form-checkbox value="a">EKG</b-form-checkbox>
                                                                <b-form-checkbox value="b">Angina</b-form-checkbox>
                                                                <b-form-checkbox value="c">CHF</b-form-checkbox>
                                                                <b-form-checkbox value="d">Hipertensi</b-form-checkbox>
                                                                <b-form-checkbox value="e">Murmur</b-form-checkbox>
                                                                <b-form-checkbox value="f">Pacemaker</b-form-checkbox>
                                                                <b-form-checkbox value="g">Penyakit Katup</b-form-checkbox>
                                                            </b-form-checkbox-group>
                                                        </div>
                                                    </b-td>
                                                    <b-td>
                                                        <b-form-checkbox v-model="form5_notes.cardiovascular_dbn" :value="1" :unchecked-value="0" size="sm">DBN</b-form-checkbox>
                                                    </b-td>
                                                    <b-td>
                                                        <b-textarea v-model="form5_notes.cardiovascular_notes"/>
                                                    </b-td>
                                                </b-tr>
                                                <b-tr>
                                                    <b-td>
                                                        <div class="">
                                                            <label class="c-form-label">Hepato / Gastrointestinala</label>
                                                            <b-form-checkbox-group v-model="form5_notes.hepato_arry" size="sm" stacked>
                                                                <b-form-checkbox value="a">Bowel obstruction</b-form-checkbox>
                                                                <b-form-checkbox value="b">Sirosis</b-form-checkbox>
                                                                <b-form-checkbox value="c">Jaundice</b-form-checkbox>
                                                                <b-form-checkbox value="d">Nausea & Vomiting</b-form-checkbox>
                                                            </b-form-checkbox-group>
                                                        </div>
                                                    </b-td>
                                                    <b-td>
                                                        <b-form-checkbox v-model="form5_notes.hepato_dbn" :value="1" :unchecked-value="0" size="sm">DBN</b-form-checkbox>
                                                    </b-td>
                                                    <b-td>
                                                        <b-textarea v-model="form5_notes.hepato_notes"/>
                                                    </b-td>
                                                </b-tr>
                                                <b-tr>
                                                    <b-td>
                                                        <div class="">
                                                            <label class="c-form-label">Neuro / Musculoskeletal</label>
                                                            <b-form-checkbox-group v-model="form5_notes.neuro_arry" size="sm" stacked>
                                                                <b-form-checkbox value="a">CVA / Stroke / TIA</b-form-checkbox>
                                                                <b-form-checkbox value="b">Sakit Kepala</b-form-checkbox>
                                                                <b-form-checkbox value="c">Penurunan kesadaran</b-form-checkbox>
                                                                <b-form-checkbox value="d">Paralisis paraestesia</b-form-checkbox>
                                                                <b-form-checkbox value="e">Hemiparese</b-form-checkbox>
                                                                <b-form-checkbox value="f">Plegi paraparese / Plegi</b-form-checkbox>
                                                                <b-form-checkbox value="g">Kejang</b-form-checkbox>
                                                                <b-form-checkbox value="h">Neuromuscular</b-form-checkbox>
                                                                <b-form-checkbox value="i">Distrofi</b-form-checkbox>
                                                            </b-form-checkbox-group>
                                                        </div>
                                                    </b-td>
                                                    <b-td>
                                                        <b-form-checkbox v-model="form5_notes.neuro_dbn" :value="1" :unchecked-value="0" size="sm">DBN</b-form-checkbox>
                                                    </b-td>
                                                    <b-td>
                                                        <b-textarea v-model="form5_notes.neuro_notes"/>
                                                    </b-td>
                                                </b-tr>
                                                <b-tr>
                                                    <b-td>
                                                        <div class="">
                                                            <label class="c-form-label">Renal / Endokrin</label>
                                                            <b-form-checkbox-group v-model="form5_notes.renal_endocrine_arry" size="sm" stacked>
                                                                <b-form-checkbox value="a">DM</b-form-checkbox>
                                                                <b-form-checkbox value="b">Gagal ginjal</b-form-checkbox>
                                                                <b-form-checkbox value="c">Thyroid</b-form-checkbox>
                                                            </b-form-checkbox-group>
                                                        </div>
                                                    </b-td>
                                                    <b-td>
                                                        <b-form-checkbox v-model="form5_notes.renal_endocrine_dbn" :value="1" :unchecked-value="0" size="sm">DBN</b-form-checkbox>
                                                    </b-td>
                                                    <b-td>
                                                        <b-textarea v-model="form5_notes.renal_endocrine_notes"/>
                                                    </b-td>
                                                </b-tr>
                                                <b-tr>
                                                    <b-td>
                                                        <div class="">
                                                            <label class="c-form-label">Other</label>
                                                            <b-form-checkbox-group v-model="form5_notes.other_arry"size="sm" stacked>
                                                                <b-form-checkbox value="a">Anemia</b-form-checkbox>
                                                                <b-form-checkbox value="b">Kanker</b-form-checkbox>
                                                                <b-form-checkbox value="c">Kemoterapi</b-form-checkbox>
                                                                <b-form-checkbox value="d">Dehidrasi</b-form-checkbox>
                                                                <b-form-checkbox value="e">Hemofilia</b-form-checkbox>
                                                                <b-form-checkbox value="f">Harnis</b-form-checkbox>
                                                            </b-form-checkbox-group>
                                                        </div>
                                                    </b-td>
                                                    <b-td>
                                                        <b-form-checkbox v-model="form5_notes.other_dbn" :value="1" :unchecked-value="0" size="sm">DBN</b-form-checkbox>
                                                    </b-td>
                                                    <b-td>
                                                        <b-textarea v-model="form5_notes.other_notes"/>
                                                    </b-td>
                                                </b-tr>
                                            </b-tbody>
                                        </b-table-simple>
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Alergi Obat</label>
                                        <b-textarea v-model="form5_notes.drug_allergy"/>
                                    </div>
                                    <div class="">
                                        <label class="font-weight-bold">Tanda-tanda vital sebelum tindakan</label>
                                        <div class="row">
                                            <div class="col-3 mt-12">
                                                <label>Systolic</label>
                                                <b-input-group append="mmHg">
                                                    <b-form-input v-model="form5_notes.systolic" type="number" autocomplete="off"></b-form-input>
                                                </b-input-group>
                                            </div>
                                            <div class="col-3 mt-12">
                                                <label>Diastolic</label>
                                                <b-input-group append="mmHg">
                                                    <b-form-input v-model="form5_notes.diastolic" type="number" autocomplete="off"></b-form-input>
                                                </b-input-group>
                                            </div>
                                            <div class="col-lg-3 mt-12">
                                                <label>Berat Badan</label>
                                                <b-input-group append="kg">
                                                    <b-form-input v-model="form5_notes.weight" type="number"></b-form-input>
                                                </b-input-group>
                                            </div>
                                            <div class="col-lg-3 mt-12">
                                                <label>Tinggi Badan</label>
                                                <b-input-group append="cm">
                                                    <b-form-input v-model="form5_notes.height" type="number"></b-form-input>
                                                </b-input-group>
                                            </div>
                                            <div class="col-3 mt-12">
                                                <label class="c-form-label">Nadi</label>
                                                <b-input-group append="x/mnt">
                                                    <b-form-input v-model="form5_notes.pulse_frequency" type="number" autocomplete="off"></b-form-input>
                                                </b-input-group>
                                            </div>
                                            <div class="col-3 mt-12">
                                                <label class="c-form-label">RR</label>
                                                <b-form-input v-model="form5_notes.breath_frequency" autocomplete="off"></b-form-input>
                                            </div>
                                            <div class="col-3 mt-12">
                                                <label class="c-form-label">Suhu</label>
                                                <b-input-group append="ᵒC">
                                                    <b-form-input v-model="form5_notes.temperature" type="number" autocomplete="off"></b-form-input>
                                                </b-input-group>
                                            </div>                                
                                        </div>
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Obat yang sedang dikonsumsi</label>
                                        <b-form-radio-group v-model="form5_notes.medications_currently_type_id" size="sm">
                                            <b-form-radio value="0">Tidak ada</b-form-radio>
                                            <b-form-radio value="1">Ada</b-form-radio>
                                        </b-form-radio-group>
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Riwayat Anestesi</label>
                                        <b-textarea v-model="form5_notes.history_anesthesia_type_id" />
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Anamnesa dari</label>
                                        <b-form-radio-group v-model="form5_notes.anamnesa_from_type_id" size="sm">
                                            <b-form-radio value="1">Pasien</b-form-radio>
                                            <b-form-radio value="2">Keluarga</b-form-radio>
                                            <b-form-radio value="3">Wali / orang tua</b-form-radio>
                                        </b-form-radio-group>
                                    </div>
                                    <div>
                                        <label class="font-weight-bold">Puasa</label>
                                        <div class="row">
                                            <div class="col-3 mt-12">
                                                <label class="c-form-label">Makanan padat</label>
                                                <b-form-input v-model="form5_notes.fasting_solid_food" autocomplete="off" />
                                            </div>
                                            <div class="col-3 mt-12">
                                                <label class="c-form-label">Makanan cair</label>
                                                <b-form-input v-model="form5_notes.fasting_liquid_food" autocomplete="off" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Obat-obat diberikan terus</label>
                                        <b-form-radio-group v-model="form5_notes.drugs_given_type_id" size="sm">
                                            <b-form-radio value="0">Tidak</b-form-radio>
                                            <b-form-radio value="1">Ya</b-form-radio>
                                        </b-form-radio-group>
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Obat yang dihentikan</label>
                                        <b-textarea />
                                    </div>
                                    <div class="">
                                        <label class="c-form-label">Rencana Anestesi</label>
                                        <b-form-checkbox-group v-model="form5_notes.anesthesia_plan_arry" size="sm">
                                            <b-form-checkbox value="a">ASA</b-form-checkbox>
                                            <b-form-checkbox value="b">Rencana</b-form-checkbox>
                                        </b-form-checkbox-group>
                                    </div>
                                    <!-- kl rencana anestesi ASA -->
                                    <div class="mt-12">
                                        <label class="c-form-label">ASA</label>
                                        <b-form-radio-group v-model="form5_notes.anesthesia_asa_type_id" size="sm">
                                            <b-form-radio value="1">1</b-form-radio>
                                            <b-form-radio value="2">2</b-form-radio>
                                            <b-form-radio value="3">3</b-form-radio>
                                            <b-form-radio value="4">4</b-form-radio>
                                            <b-form-radio value="5">5</b-form-radio>
                                            <b-form-radio value="6">6</b-form-radio>
                                            <b-form-radio value="7">E</b-form-radio>
                                        </b-form-radio-group>
                                    </div>
                                    <!-- kl rencana anestesi 'rencanan' -->
                                    <div class="mt-12">
                                        <label class="c-form-label">Rencana</label>
                                        <b-form-radio-group v-model="form5_notes.anesthesia_plan_type_id" size="sm">
                                            <b-form-radio value="1">GA</b-form-radio>
                                            <b-form-radio value="2">REG</b-form-radio>
                                            <b-form-radio value="3">IV Sed</b-form-radio>
                                            <b-form-radio value="4">L/MAC</b-form-radio>
                                        </b-form-radio-group>
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label">Premidikasi</label>
                                        <b-table :fields="premedicationFields" :items="form5_notes.premedication">
                                            <template #cell(no)="row">{{ row.index+1 }}</template>
                                            <template #cell(action)="row">
                                                <div class="d-flex justify-content-center">
                                                    <b-button @click="removePremedication(row.index)" variant="danger-600" size="sm" >
                                                        <feather-icon icon="TrashIcon" />
                                                    </b-button>
                                                </div>
                                            </template>
                                        </b-table>
                                        <div class="row">
                                            <div class="col-3 mt-12">
                                                <label class="c-form-label">Obat</label>
                                                <b-form-input v-model="input_premedication.drug" autocomplete="off"></b-form-input>
                                            </div>
                                            <div class="col-3 mt-12">
                                                <label class="c-form-label">Waktu</label>
                                                <b-form-input v-model="input_premedication.time" type="datetime-local" autocomplete="off"></b-form-input>
                                            </div>
                                        </div>
                                        <b-button @click="addPremedication()" type="submit" variant="primary" class="mt-8">Tambah</b-button>
                                    </div>
                                </section>
                                <div class="card-footer" style="margin: 20px -20px -20px -20px">
                                    <div class="d-flex justify-content-end">
                                        <b-button variant="primary" class="px-3" @click="submit_notes_form()">
                                            <feather-icon icon="SaveIcon" /> Simpan
                                        </b-button>
                                    </div>
                                </div>
                            </b-tab>
                            <b-tab>
                                <template #title>
                                    <strong>Pemantauan Pembedahan dengan Anastesi Lokal (Pra Bedah)</strong>
                                </template>
                                
                                <section>
                                    <div class="">
                                        <div class="">
                                            <label class="c-form-label">Riwayat penyakit dahulu</label>
                                            <b-textarea v-model="form5_monitor.past_medical_history"/>
                                        </div>
                                        <div class="mt-12">
                                            <label class="c-form-label">Riwayat Alergi</label>
                                            <b-textarea v-model="form5_monitor.allergy_history"/>
                                        </div>
                                    </div>
                                    <div class="mt-20">
                                        <ModernLabel label="pemantauan Status Fisiologis" />
                                        <b-table bordered responsive :fields="fisioFields" :items="form5_monitor.physiological_status_monitoring" class="mt-20">
                                            <template #cell(no)="row">{{ row.index+1 }}</template>
                                            <template #cell(awareness)="row">
                                                <template v-if="row.item.awareness == 1">Eye</template>
                                                <template v-if="row.item.awareness == 2">Motoric</template>
                                                <template v-if="row.item.awareness == 3">Verbal</template>
                                            </template>
                                            <template #cell(blood_pressure)="row">{{ row.item.systolic_blood_pressure }} / {{ row.item.dyastolic_blood_pressure }} mmHg</template>
                                            <template #cell(action)="row">
                                                <div class="d-flex justify-content-center">
                                                    <b-button @click="removeFisio(row.index)" variant="danger-600" size="sm" >
                                                        <feather-icon icon="TrashIcon" />
                                                    </b-button>
                                                </div>
                                            </template>
                                        </b-table>
                                        <div class="row mt-32">
                                            <div class="col-lg-3 mt-12">
                                                <label class="c-form-label">Waktu Pemantauan</label>
                                                <b-form-input type="datetime-local" v-model="input_fisio.monitoring_time" />
                                            </div>
                                            <div class="col-lg-3 mt-12">
                                                <label class="c-form-label">Kesadaran</label>
                                                <v-select v-model="input_fisio.awareness" :options="OptionsAwareness" :reduce="OptionsAwareness => OptionsAwareness.value" label="title" placeholder="-- pilih --"></v-select>
                                            </div>
                                            <div class="col-lg-3 mt-12">
                                                <label class="c-form-label">Systolic</label>
                                                <b-input-group append="mmHg">
                                                    <b-form-input type="number" v-model="input_fisio.systolic_blood_pressure" />
                                                </b-input-group>
                                            </div>
                                            <div class="col-lg-3 mt-12">
                                                <label class="c-form-label">Diastolic</label>
                                                <b-input-group append="mmHg">
                                                    <b-form-input type="number" v-model="input_fisio.dyastolic_blood_pressure" />
                                                </b-input-group>
                                            </div>
                                            <div class="col-lg-3 mt-12">
                                                <label class="c-form-label">Frekuensi Nadi</label>
                                                <b-input-group append="x/mnt">
                                                    <b-form-input type="number" v-model="input_fisio.pulse_frequency" />
                                                </b-input-group>
                                            </div>
                                            <div class="col-lg-3 mt-12">
                                                <label class="c-form-label">Frekuensi Nafas</label>
                                                <b-input-group append="x/mnt">
                                                    <b-form-input type="number" v-model="input_fisio.breath_frequency" />
                                                </b-input-group>
                                            </div>
                                            <div class="col-lg-3 mt-12">
                                                <label class="c-form-label">Suhu</label>
                                                <b-input-group append="ᵒC">
                                                    <b-form-input v-model="input_fisio.temperature" type="number" autocomplete="off"></b-form-input>
                                                </b-input-group>
                                            </div>
                                            <div class="col-lg-3 mt-12">
                                                <label class="c-form-label">Cairan</label>
                                                <b-form-input v-model="input_fisio.liquid" autocomplete="off"></b-form-input>
                                            </div>
                                            <div class="col-lg-3 mt-12">
                                                <label class="c-form-label">Obat-obatan</label>
                                                <b-textarea v-model="input_fisio.prescription" rows="2"></b-textarea>
                                            </div>
                                            <div class="col-lg-3 mt-12">
                                                <label class="c-form-label">Keterangan</label>
                                                <b-textarea v-model="input_fisio.notes" rows="2"></b-textarea>
                                            </div>
                                        </div>
                                        <div class="mt-16">
                                            <b-button @click="addFisio" variant="primary" class="px-3">Tambah</b-button>
                                        </div>
                                    </div>
                                    <div class="mt-32">
                                        <ModernLabel label="Pasca Bedah" />
                                        <div class="row">
                                            <div class="col-lg-3 mt-12">
                                                <label class="c-form-label">Kesadaran</label>
                                                <v-select v-model="form5_monitor.post_surgery_awareness" :options="OptionsAwareness" :reduce="OptionsAwareness => OptionsAwareness.value" label="title" placeholder="-- pilih --"></v-select>
                                            </div>
                                            <div class="col-lg-3 mt-12">
                                                <label class="c-form-label">Systolic</label>
                                                <b-input-group append="mmHg">
                                                    <b-form-input type="number" v-model="form5_monitor.post_surgery_systolic" />
                                                </b-input-group>
                                            </div>
                                            <div class="col-lg-3 mt-12">
                                                <label class="c-form-label">Diastolic</label>
                                                <b-input-group append="mmHg">
                                                    <b-form-input type="number" v-model="form5_monitor.post_surgery_diastolic" />
                                                </b-input-group>
                                            </div>
                                            <div class="col-lg-3 mt-12">
                                                <label class="c-form-label">Frekuensi Nadi</label>
                                                <b-input-group append="x/mnt">
                                                    <b-form-input type="number" v-model="form5_monitor.post_surgery_pulse" />
                                                </b-input-group>
                                            </div>
                                            <div class="col-lg-3 mt-12">
                                                <label class="c-form-label">Frekuensi Nafas</label>
                                                <b-input-group append="x/mnt">
                                                    <b-form-input type="number" v-model="form5_monitor.post_surgery_respiratory_rate" />
                                                </b-input-group>
                                            </div>
                                            <div class="col-lg-3 mt-12">
                                                <label class="c-form-label">Suhu</label>
                                                <b-input-group append="ᵒC">
                                                    <b-form-input type="number" autocomplete="off" v-model="form5_monitor.post_surgery_temperature" />
                                                </b-input-group>
                                            </div>
                                            <div class="col-lg-3 mt-12">
                                                <label class="c-form-label">Reaksi Alergi</label>
                                                <b-textarea rows="2" v-model="form5_monitor.post_surgery_allergic_reaction" />
                                            </div>
                                            <div class="col-lg-3 mt-12">
                                                <label class="c-form-label">Komplikasi Lain</label>
                                                <b-textarea rows="2" v-model="form5_monitor.post_surgery_other_complications" />
                                            </div>
                                        </div>
                                    </div>
                                </section>
                                <div class="card-footer" style="margin: 20px -20px -20px -20px">
                                    <div class="d-flex justify-content-end">
                                        <b-button variant="primary" class="px-3" @click="submit_monitor_form()">
                                            <feather-icon icon="SaveIcon" /> Simpan
                                        </b-button>
                                    </div>
                                </div>
                            </b-tab>
                        </b-tabs>
                    </b-card>
                </section>

                    <!-- Current_tab = menu6 -->
                <section v-if="active.current_tab === 'menu6'" class="mt-24">
                    <b-card no-body>
                        <b-tabs card>
                            <b-tab active>
                                <template #title>
                                    <strong>BROMAGE SCORE</strong>
                                </template>
                                <section>
                                    <b-row class="mb-1">
                                        <b-col md="3">
                                            <b-form-group label="Jam masuk RR">
                                                <b-form-input type="time" v-model="form6_bromage.check_in_rr_time" :disabled="!!form6_bromage.check_in_rr_time_locked" />
                                            </b-form-group>
                                        </b-col>
                                        <b-col md="3">
                                            <b-form-group label="Jam pindah ruang perawatan">
                                                <b-form-input type="time" v-model="form6_bromage.transfer_time" :disabled="!bromage_can_transfer" />
                                            </b-form-group>
                                        </b-col>
                                    </b-row>
                                    <b-table-simple small bordered class="text-center" style="font-size:13px;">
                                        <b-thead class="text-bold-14">
                                            <b-tr>
                                                <b-th colspan="2" class="text-left">Kriteria</b-th>
                                                <b-th v-for="slot in bromageTimeSlots" :key="'head_'+slot.key">{{ slot.label }}</b-th>
                                            </b-tr>
                                        </b-thead>
                                        <b-tbody>
                                            <b-tr>
                                                <b-td class="text-left">Gerakan penuh dari tungkai</b-td>
                                                <b-td style="width:100px">0</b-td>
                                                <b-td v-for="slot in bromageTimeSlots" :key="'m0_'+slot.key">
                                                    <b-form-radio
                                                        :name="'bromage_slot_'+slot.key"
                                                        v-model="form6_bromage.slot_score[slot.key]"
                                                        :value="0"
                                                        :disabled="isSlotLocked(slot.key)"
                                                    />
                                                </b-td>
                                            </b-tr>

                                            <b-tr>
                                                <b-td class="text-left">Tak mampu ekstensi tungkai</b-td>
                                                <b-td>1</b-td>
                                                <b-td v-for="slot in bromageTimeSlots" :key="'m1_'+slot.key">
                                                    <b-form-radio
                                                        :name="'bromage_slot_'+slot.key"
                                                        v-model="form6_bromage.slot_score[slot.key]"
                                                        :value="1"
                                                        :disabled="isSlotLocked(slot.key)"
                                                    />
                                                </b-td>
                                            </b-tr>

                                            <b-tr>
                                                <b-td class="text-left">Tak mampu fleksi lutut</b-td>
                                                <b-td>2</b-td>
                                                <b-td v-for="slot in bromageTimeSlots" :key="'m2_'+slot.key">
                                                    <b-form-radio
                                                        :name="'bromage_slot_'+slot.key"
                                                        v-model="form6_bromage.slot_score[slot.key]"
                                                        :value="2"
                                                        :disabled="isSlotLocked(slot.key)"
                                                    />
                                                </b-td>
                                            </b-tr>

                                            <b-tr>
                                                <b-td class="text-left">Tak mampu fleksi pergelangan kaki</b-td>
                                                <b-td>3</b-td>
                                                <b-td v-for="slot in bromageTimeSlots" :key="'m3_'+slot.key">
                                                    <b-form-radio
                                                        :name="'bromage_slot_'+slot.key"
                                                        v-model="form6_bromage.slot_score[slot.key]"
                                                        :value="3"
                                                        :disabled="isSlotLocked(slot.key)"
                                                    />
                                                </b-td>
                                            </b-tr>

                                            <b-tr>
                                                <b-th colspan="2">TOTAL</b-th>
                                                <b-td v-for="slot in bromageTimeSlots" :key="'total_'+slot.key">
                                                    {{ form6_bromage.slot_score[slot.key] !== null ? form6_bromage.slot_score[slot.key] : '-' }}
                                                </b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-th colspan="9" class="text-left">
                                                    Boleh pindah ruang jika skor akhir &lt; 2 <span v-if="bromage_final_score !== null"> (Skor akhir saat ini: <strong>{{ bromage_final_score }}</strong>) </span>
                                                </b-th>
                                            </b-tr>
                                        </b-tbody>
                                    </b-table-simple>
                                </section>

                                <div class="card-footer" style="margin: 20px -20px -20px -20px">
                                    <div class="d-flex justify-content-end">
                                        <b-button variant="primary" class="px-3" @click="submit_bromage_form" :disabled="isLoading">
                                            <feather-icon icon="SaveIcon" /> Simpan
                                        </b-button>
                                    </div>
                                </div>
                            </b-tab>
                            <b-tab>
                                <template #title>
                                    <strong>PADSS</strong>
                                </template>

                                <section>
                                    <b-row class="mb-1">
                                        <b-col md="3">
                                            <b-form-group label="Jam Pasca Anestesi">
                                                <b-form-input type="time" v-model="form6_padss.post_anesthesia_time" :disabled="!!form6_padss.post_anesthesia_time_locked" />
                                            </b-form-group>
                                        </b-col>
                                        <b-col md="3">
                                            <b-form-group label="Jam pindah ruang perawatan">
                                                <b-form-input type="time" v-model="form6_padss.transfer_time" :disabled="!padss_can_transfer || !!form6_padss.transfer_time_locked" />
                                            </b-form-group>
                                        </b-col>
                                    </b-row>

                                    <b-table-simple small bordered class="text-center" style="font-size:13px;">
                                        <b-thead class="text-bold-14">
                                            <b-tr>
                                                <b-th colspan="3" class="text-left">Post-Anestesi Vital Sign / Source</b-th>
                                                <b-th v-for="slot in padssTimeSlots" :key="'head_'+slot.key">{{ slot.label }}</b-th>
                                            </b-tr>
                                        </b-thead>
                                        <b-tbody>

                                            <!-- TEKANAN DARAH -->
                                            <b-tr>
                                                <b-td rowspan="3" class="align-top text-left" style="width:150px">Tekanan Darah</b-td>
                                                <b-td class="text-left">TD & Nadi &ne; 20% dari pra anaestesi</b-td>
                                                <b-td style="width:60px">2</b-td>
                                                <b-td v-for="slot in padssTimeSlots" :key="'bp2_'+slot.key">
                                                    <b-form-radio
                                                        :name="'padss_blood_pressure_'+slot.key"
                                                        v-model="form6_padss.category_score.blood_pressure[slot.key]"
                                                        :value="2"
                                                        :disabled="isPadssSlotLocked('blood_pressure', slot.key)"
                                                    />
                                                </b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-td class="text-left">TD & Nadi &ne; 20% - 40% dari pra anaestesi</b-td>
                                                <b-td>1</b-td>
                                                <b-td v-for="slot in padssTimeSlots" :key="'bp1_'+slot.key">
                                                    <b-form-radio
                                                        :name="'padss_blood_pressure_'+slot.key"
                                                        v-model="form6_padss.category_score.blood_pressure[slot.key]"
                                                        :value="1"
                                                        :disabled="isPadssSlotLocked('blood_pressure', slot.key)"
                                                    />
                                                </b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-td class="text-left">TD & Nadi &ne; 40% dari pra anaestesi</b-td>
                                                <b-td>0</b-td>
                                                <b-td v-for="slot in padssTimeSlots" :key="'bp0_'+slot.key">
                                                    <b-form-radio
                                                        :name="'padss_blood_pressure_'+slot.key"
                                                        v-model="form6_padss.category_score.blood_pressure[slot.key]"
                                                        :value="0"
                                                        :disabled="isPadssSlotLocked('blood_pressure', slot.key)"
                                                    />
                                                </b-td>
                                            </b-tr>

                                            <!-- AKTIVITAS -->
                                            <b-tr>
                                                <b-td rowspan="3" class="align-top text-left" style="width:150px">Aktivitas</b-td>
                                                <b-td class="text-left">Mampu jalan tidak pusing</b-td>
                                                <b-td>2</b-td>
                                                <b-td v-for="slot in padssTimeSlots" :key="'act2_'+slot.key">
                                                    <b-form-radio
                                                        :name="'padss_activities_'+slot.key"
                                                        v-model="form6_padss.category_score.activities[slot.key]"
                                                        :value="2"
                                                        :disabled="isPadssSlotLocked('activities', slot.key)"
                                                    />
                                                </b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-td class="text-left">Perlu bantuan</b-td>
                                                <b-td>1</b-td>
                                                <b-td v-for="slot in padssTimeSlots" :key="'act1_'+slot.key">
                                                    <b-form-radio
                                                        :name="'padss_activities_'+slot.key"
                                                        v-model="form6_padss.category_score.activities[slot.key]"
                                                        :value="1"
                                                        :disabled="isPadssSlotLocked('activities', slot.key)"
                                                    />
                                                </b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-td class="text-left">Tidak mampu berjalan</b-td>
                                                <b-td>0</b-td>
                                                <b-td v-for="slot in padssTimeSlots" :key="'act0_'+slot.key">
                                                    <b-form-radio
                                                        :name="'padss_activities_'+slot.key"
                                                        v-model="form6_padss.category_score.activities[slot.key]"
                                                        :value="0"
                                                        :disabled="isPadssSlotLocked('activities', slot.key)"
                                                    />
                                                </b-td>
                                            </b-tr>

                                            <!-- MUAL DAN MUNTAH -->
                                            <b-tr>
                                                <b-td rowspan="3" class="align-top text-left" style="width:150px">Mual dan Muntah</b-td>
                                                <b-td class="text-left">Minimal (membaik dengan obat PO)</b-td>
                                                <b-td>2</b-td>
                                                <b-td v-for="slot in padssTimeSlots" :key="'nv2_'+slot.key">
                                                    <b-form-radio
                                                        :name="'padss_nausea_vomiting_'+slot.key"
                                                        v-model="form6_padss.category_score.nausea_vomiting[slot.key]"
                                                        :value="2"
                                                        :disabled="isPadssSlotLocked('nausea_vomiting', slot.key)"
                                                    />
                                                </b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-td class="text-left">Sedang (membaik dengan obat IM)</b-td>
                                                <b-td>1</b-td>
                                                <b-td v-for="slot in padssTimeSlots" :key="'nv1_'+slot.key">
                                                    <b-form-radio
                                                        :name="'padss_nausea_vomiting_'+slot.key"
                                                        v-model="form6_padss.category_score.nausea_vomiting[slot.key]"
                                                        :value="1"
                                                        :disabled="isPadssSlotLocked('nausea_vomiting', slot.key)"
                                                    />
                                                </b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-td class="text-left">Berat (tetap dengan terapi berulang)</b-td>
                                                <b-td>0</b-td>
                                                <b-td v-for="slot in padssTimeSlots" :key="'nv0_'+slot.key">
                                                    <b-form-radio
                                                        :name="'padss_nausea_vomiting_'+slot.key"
                                                        v-model="form6_padss.category_score.nausea_vomiting[slot.key]"
                                                        :value="0"
                                                        :disabled="isPadssSlotLocked('nausea_vomiting', slot.key)"
                                                    />
                                                </b-td>
                                            </b-tr>

                                            <!-- NYERI -->
                                            <b-tr>
                                                <b-td rowspan="2" class="align-top text-left" style="width:150px">Nyeri</b-td>
                                                <b-td class="text-left">Ya</b-td>
                                                <b-td>2</b-td>
                                                <b-td v-for="slot in padssTimeSlots" :key="'pain2_'+slot.key">
                                                    <b-form-radio
                                                        :name="'padss_painful_'+slot.key"
                                                        v-model="form6_padss.category_score.painful[slot.key]"
                                                        :value="2"
                                                        :disabled="isPadssSlotLocked('painful', slot.key)"
                                                    />
                                                </b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-td class="text-left">Tidak</b-td>
                                                <b-td>1</b-td>
                                                <b-td v-for="slot in padssTimeSlots" :key="'pain1_'+slot.key">
                                                    <b-form-radio
                                                        :name="'padss_painful_'+slot.key"
                                                        v-model="form6_padss.category_score.painful[slot.key]"
                                                        :value="1"
                                                        :disabled="isPadssSlotLocked('painful', slot.key)"
                                                    />
                                                </b-td>
                                            </b-tr>

                                            <!-- PERDARAHAN -->
                                            <b-tr>
                                                <b-td rowspan="3" class="align-top text-left" style="width:150px">Perdarahan</b-td>
                                                <b-td class="text-left">Minimal (tidak perlu ganti perban)</b-td>
                                                <b-td>2</b-td>
                                                <b-td v-for="slot in padssTimeSlots" :key="'bl2_'+slot.key">
                                                    <b-form-radio
                                                        :name="'padss_bleeding_'+slot.key"
                                                        v-model="form6_padss.category_score.bleeding[slot.key]"
                                                        :value="2"
                                                        :disabled="isPadssSlotLocked('bleeding', slot.key)"
                                                    />
                                                </b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-td class="text-left">Sedang (ganti perban 2 kali)</b-td>
                                                <b-td>1</b-td>
                                                <b-td v-for="slot in padssTimeSlots" :key="'bl1_'+slot.key">
                                                    <b-form-radio
                                                        :name="'padss_bleeding_'+slot.key"
                                                        v-model="form6_padss.category_score.bleeding[slot.key]"
                                                        :value="1"
                                                        :disabled="isPadssSlotLocked('bleeding', slot.key)"
                                                    />
                                                </b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-td class="text-left">Berat (ganti perban &gt; 3 kali)</b-td>
                                                <b-td>0</b-td>
                                                <b-td v-for="slot in padssTimeSlots" :key="'bl0_'+slot.key">
                                                    <b-form-radio
                                                        :name="'padss_bleeding_'+slot.key"
                                                        v-model="form6_padss.category_score.bleeding[slot.key]"
                                                        :value="0"
                                                        :disabled="isPadssSlotLocked('bleeding', slot.key)"
                                                    />
                                                </b-td>
                                            </b-tr>

                                            <b-tr>
                                                <b-th colspan="3">TOTAL</b-th>
                                                <b-td v-for="slot in padssTimeSlots" :key="'total_'+slot.key">
                                                    {{ padssSlotTotal[slot.key] !== null ? padssSlotTotal[slot.key] : '-' }}
                                                </b-td>
                                            </b-tr>
                                            <b-tr>
                                                <b-th colspan="10" class="text-left">
                                                    Boleh pindah ruang jika skor &ge; 9 <span v-if="padss_final_score !== null"> (Skor akhir saat ini: <strong>{{ padss_final_score }}</strong>) </span>
                                                </b-th>
                                            </b-tr>
                                        </b-tbody>
                                    </b-table-simple>
                                </section>

                                <div class="card-footer" style="margin: 20px -20px -20px -20px">
                                    <div class="d-flex justify-content-end">
                                        <b-button variant="primary" class="px-3" @click="submit_padss_form" :disabled="isLoading">
                                            <feather-icon icon="SaveIcon" /> Simpan
                                        </b-button>
                                    </div>
                                </div>
                            </b-tab>
                            <b-tab>
                                <template #title><strong>Aldrete Score</strong></template>

                                <section>
                                    <b-row class="mb-1">
                                        <b-col md="3">
                                            <b-form-group label="Jam Pasca Anestesi">
                                                <b-form-input type="time" v-model="form6_aldrete.post_anesthesia_time" :disabled="!!form6_aldrete.post_anesthesia_time_locked"/>
                                            </b-form-group>
                                        </b-col>

                                        <b-col md="3">
                                            <b-form-group label="Jam Pindah Ruang Perawatan">
                                                <b-form-input type="time" v-model="form6_aldrete.transfer_time" :disabled="!aldrete_can_transfer || !!form6_aldrete.transfer_time_locked"/>
                                            </b-form-group>
                                        </b-col>
                                    </b-row>

                                    <b-table-simple small bordered class="text-center" style="font-size:13px;">
                                        <b-thead class="text-bold-14">
                                            <b-tr>
                                                <b-th colspan="3" class="text-left">Aldrete Score</b-th>
                                                <b-th v-for="slot in padssTimeSlots" :key="'head_'+slot.key">{{ slot.label }}</b-th>
                                            </b-tr>
                                        </b-thead>

                                        <b-tbody>

                                            <!-- AKTIVITAS MOTORIK -->
                                            <b-tr>
                                                <b-td rowspan="3" class="align-top text-left" style="width:170px">Aktivitas Motorik</b-td>
                                                <b-td class="text-left">Mampu menggerakkan seluruh ekstremitas sesuai perintah</b-td>
                                                <b-td style="width:60px">2</b-td>
                                                <b-td v-for="slot in padssTimeSlots" :key="'motor2_'+slot.key">
                                                    <b-form-radio :name="'aldrete_motor_activities_'+slot.key"
                                                                v-model="form6_aldrete.category_score.motor_activities[slot.key]"
                                                                :value="2"
                                                                :disabled="isAldreteSlotLocked('motor_activities',slot.key)"/>
                                                </b-td>
                                            </b-tr>

                                            <b-tr>
                                                <b-td class="text-left">Mampu menggerakkan dua ekstremitas sesuai perintah</b-td>
                                                <b-td>1</b-td>
                                                <b-td v-for="slot in padssTimeSlots" :key="'motor1_'+slot.key">
                                                    <b-form-radio :name="'aldrete_motor_activities_'+slot.key"
                                                                v-model="form6_aldrete.category_score.motor_activities[slot.key]"
                                                                :value="1"
                                                                :disabled="isAldreteSlotLocked('motor_activities',slot.key)"/>
                                                </b-td>
                                            </b-tr>

                                            <b-tr>
                                                <b-td class="text-left">Tidak mampu menggerakkan semua ekstremitas</b-td>
                                                <b-td>0</b-td>
                                                <b-td v-for="slot in padssTimeSlots" :key="'motor0_'+slot.key">
                                                    <b-form-radio :name="'aldrete_motor_activities_'+slot.key"
                                                                v-model="form6_aldrete.category_score.motor_activities[slot.key]"
                                                                :value="0"
                                                                :disabled="isAldreteSlotLocked('motor_activities',slot.key)"/>
                                                </b-td>
                                            </b-tr>

                                            <b-tr>
                                                <b-td rowspan="3" class="align-top text-left" style="width:170px">Respirasi</b-td>
                                                <b-td class="text-left">Nafas adekuat dan dapat batuk</b-td>
                                                <b-td>2</b-td>
                                                <b-td v-for="slot in padssTimeSlots" :key="'resp2_'+slot.key">
                                                    <b-form-radio :name="'aldrete_respiration_'+slot.key"
                                                                v-model="form6_aldrete.category_score.respiration[slot.key]"
                                                                :value="2"
                                                                :disabled="isAldreteSlotLocked('respiration',slot.key)"/>
                                                </b-td>
                                            </b-tr>

                                            <b-tr>
                                                <b-td class="text-left">Nafas kurang adekuat / hipoventilasi / usaha nafas</b-td>
                                                <b-td>1</b-td>
                                                <b-td v-for="slot in padssTimeSlots" :key="'resp1_'+slot.key">
                                                    <b-form-radio :name="'aldrete_respiration_'+slot.key"
                                                                v-model="form6_aldrete.category_score.respiration[slot.key]"
                                                                :value="1"
                                                                :disabled="isAldreteSlotLocked('respiration',slot.key)"/>
                                                </b-td>
                                            </b-tr>

                                            <b-tr>
                                                <b-td class="text-left">Apneu</b-td>
                                                <b-td>0</b-td>
                                                <b-td v-for="slot in padssTimeSlots" :key="'resp0_'+slot.key">
                                                    <b-form-radio :name="'aldrete_respiration_'+slot.key"
                                                                v-model="form6_aldrete.category_score.respiration[slot.key]"
                                                                :value="0"
                                                                :disabled="isAldreteSlotLocked('respiration',slot.key)"/>
                                                </b-td>
                                            </b-tr>

                                            <!-- SIRKULASI -->
                                            <b-tr>
                                                <b-td rowspan="3" class="align-top text-left" style="width:170px">Sirkulasi</b-td>
                                                <b-td class="text-left">TD berbeda ±20% dari pre anestesi</b-td>
                                                <b-td>2</b-td>
                                                <b-td v-for="slot in padssTimeSlots" :key="'circ2_'+slot.key">
                                                    <b-form-radio :name="'aldrete_circulation_'+slot.key"
                                                                v-model="form6_aldrete.category_score.circulation[slot.key]"
                                                                :value="2"
                                                                :disabled="isAldreteSlotLocked('circulation',slot.key)"/>
                                                </b-td>
                                            </b-tr>

                                            <b-tr>
                                                <b-td class="text-left">TD berbeda ±20% - 50% dari pre anestesi</b-td>
                                                <b-td>1</b-td>
                                                <b-td v-for="slot in padssTimeSlots" :key="'circ1_'+slot.key">
                                                    <b-form-radio :name="'aldrete_circulation_'+slot.key"
                                                                v-model="form6_aldrete.category_score.circulation[slot.key]"
                                                                :value="1"
                                                                :disabled="isAldreteSlotLocked('circulation',slot.key)"/>
                                                </b-td>
                                            </b-tr>

                                            <b-tr>
                                                <b-td class="text-left">TD berbeda >50% dari pre anestesi</b-td>
                                                <b-td>0</b-td>
                                                <b-td v-for="slot in padssTimeSlots" :key="'circ0_'+slot.key">
                                                    <b-form-radio :name="'aldrete_circulation_'+slot.key"
                                                                v-model="form6_aldrete.category_score.circulation[slot.key]"
                                                                :value="0"
                                                                :disabled="isAldreteSlotLocked('circulation',slot.key)"/>
                                                </b-td>
                                            </b-tr>
                                            <!-- KESADARAN -->
                                            <b-tr>
                                                <b-td rowspan="3" class="align-top text-left" style="width:170px">Kesadaran</b-td>
                                                <b-td class="text-left">Sadar penuh</b-td>
                                                <b-td>2</b-td>
                                                <b-td v-for="slot in padssTimeSlots" :key="'cons2_'+slot.key">
                                                    <b-form-radio :name="'aldrete_consciousness_'+slot.key"
                                                                v-model="form6_aldrete.category_score.consciousness[slot.key]"
                                                                :value="2"
                                                                :disabled="isAldreteSlotLocked('consciousness',slot.key)"/>
                                                </b-td>
                                            </b-tr>

                                            <b-tr>
                                                <b-td class="text-left">Bangun jika dipanggil</b-td>
                                                <b-td>1</b-td>
                                                <b-td v-for="slot in padssTimeSlots" :key="'cons1_'+slot.key">
                                                    <b-form-radio :name="'aldrete_consciousness_'+slot.key"
                                                                v-model="form6_aldrete.category_score.consciousness[slot.key]"
                                                                :value="1"
                                                                :disabled="isAldreteSlotLocked('consciousness',slot.key)"/>
                                                </b-td>
                                            </b-tr>

                                            <b-tr>
                                                <b-td class="text-left">Tidak ada respon / belum sadar</b-td>
                                                <b-td>0</b-td>
                                                <b-td v-for="slot in padssTimeSlots" :key="'cons0_'+slot.key">
                                                    <b-form-radio :name="'aldrete_consciousness_'+slot.key"
                                                                v-model="form6_aldrete.category_score.consciousness[slot.key]"
                                                                :value="0"
                                                                :disabled="isAldreteSlotLocked('consciousness',slot.key)"/>
                                                </b-td>
                                            </b-tr>

                                            <!-- WARNA KULIT -->
                                            <b-tr>
                                                <b-td rowspan="3" class="align-top text-left" style="width:170px">Warna Kulit</b-td>
                                                <b-td class="text-left">Kemerahan</b-td>
                                                <b-td>2</b-td>
                                                <b-td v-for="slot in padssTimeSlots" :key="'skin2_'+slot.key">
                                                    <b-form-radio :name="'aldrete_skin_color_'+slot.key"
                                                                v-model="form6_aldrete.category_score.skin_color[slot.key]"
                                                                :value="2"
                                                                :disabled="isAldreteSlotLocked('skin_color',slot.key)"/>
                                                </b-td>
                                            </b-tr>

                                            <b-tr>
                                                <b-td class="text-left">Pucat</b-td>
                                                <b-td>1</b-td>
                                                <b-td v-for="slot in padssTimeSlots" :key="'skin1_'+slot.key">
                                                    <b-form-radio :name="'aldrete_skin_color_'+slot.key"
                                                                v-model="form6_aldrete.category_score.skin_color[slot.key]"
                                                                :value="1"
                                                                :disabled="isAldreteSlotLocked('skin_color',slot.key)"/>
                                                </b-td>
                                            </b-tr>

                                            <b-tr>
                                                <b-td class="text-left">Sianosis</b-td>
                                                <b-td>0</b-td>
                                                <b-td v-for="slot in padssTimeSlots" :key="'skin0_'+slot.key">
                                                    <b-form-radio :name="'aldrete_skin_color_'+slot.key"
                                                                v-model="form6_aldrete.category_score.skin_color[slot.key]"
                                                                :value="0"
                                                                :disabled="isAldreteSlotLocked('skin_color',slot.key)"/>
                                                </b-td>
                                            </b-tr>

                                            <!-- TOTAL -->
                                            <b-tr>
                                                <b-th colspan="3">TOTAL</b-th>
                                                <b-td v-for="slot in padssTimeSlots" :key="'aldrete_total_'+slot.key">
                                                    {{ aldreteSlotTotal[slot.key] !== null ? aldreteSlotTotal[slot.key] : '-' }}
                                                </b-td>
                                            </b-tr>

                                            <b-tr>
                                                <b-th colspan="10" class="text-left">
                                                    Boleh pindah ruang jika skor ≥ 8
                                                    <span v-if="aldrete_final_score !== null">
                                                        (Skor akhir saat ini: <strong>{{ aldrete_final_score }}</strong>)
                                                    </span>
                                                </b-th>
                                            </b-tr>

                                        </b-tbody>
                                    </b-table-simple>
                                </section>

                                <div class="card-footer" style="margin:20px -20px -20px -20px">
                                    <div class="d-flex justify-content-end">
                                        <b-button variant="primary" class="px-3" @click="submit_aldrete_form" :disabled="isLoading">
                                            <feather-icon icon="SaveIcon"/> Simpan
                                        </b-button>
                                    </div>
                                </div>
                            </b-tab>
                            <b-tab>
                                <template #title><strong>Steward Score</strong></template>
                                <section>
                                    <b-row class="mb-1">
                                        <b-col md="3">
                                            <b-form-group label="Jam Pasca Anestesi">
                                                <b-form-input
                                                    type="time"
                                                    v-model="form6_steward.post_anesthesia_time"
                                                    :disabled="!!form6_steward.post_anesthesia_time_locked"/>
                                            </b-form-group>
                                        </b-col>

                                        <b-col md="3">
                                            <b-form-group label="Jam Pindah Ruang Perawatan">
                                                <b-form-input
                                                    type="time"
                                                    v-model="form6_steward.transfer_time"
                                                    :disabled="!steward_can_transfer || !!form6_steward.transfer_time_locked"/>
                                            </b-form-group>
                                        </b-col>
                                    </b-row>

                                    <b-table-simple small bordered class="text-center">
                                        <b-thead>
                                            <b-tr>
                                                <b-th colspan="3" class="text-left">Steward Score</b-th>
                                                <b-th
                                                    v-for="slot in padssTimeSlots"
                                                    :key="'steward_head_'+slot.key">
                                                    {{ slot.label }}
                                                </b-th>
                                            </b-tr>
                                        </b-thead>

                                        <b-tbody>

                                            <!-- KESADARAN -->
                                            <b-tr>
                                                <b-td rowspan="3" class="text-left">Kesadaran</b-td>
                                                <b-td class="text-left">Bangun</b-td>
                                                <b-td>2</b-td>
                                                <b-td v-for="slot in padssTimeSlots" :key="'cons2_'+slot.key">
                                                    <b-form-radio
                                                        :name="'steward_consciousness_'+slot.key"
                                                        v-model="form6_steward.category_score.consciousness[slot.key]"
                                                        :value="2"
                                                        :disabled="isStewardSlotLocked('consciousness',slot.key)"/>
                                                </b-td>
                                            </b-tr>

                                            <b-tr>
                                                <b-td class="text-left">Ada respon terhadap rangsang</b-td>
                                                <b-td>1</b-td>
                                                <b-td v-for="slot in padssTimeSlots" :key="'cons1_'+slot.key">
                                                    <b-form-radio
                                                        :name="'steward_consciousness_'+slot.key"
                                                        v-model="form6_steward.category_score.consciousness[slot.key]"
                                                        :value="1"
                                                        :disabled="isStewardSlotLocked('consciousness',slot.key)"/>
                                                </b-td>
                                            </b-tr>

                                            <b-tr>
                                                <b-td class="text-left">Tidak ada respon</b-td>
                                                <b-td>0</b-td>
                                                <b-td v-for="slot in padssTimeSlots" :key="'cons0_'+slot.key">
                                                    <b-form-radio
                                                        :name="'steward_consciousness_'+slot.key"
                                                        v-model="form6_steward.category_score.consciousness[slot.key]"
                                                        :value="0"
                                                        :disabled="isStewardSlotLocked('consciousness',slot.key)"/>
                                                </b-td>
                                            </b-tr>

                                            <!-- RESPIRASI -->
                                            <b-tr>
                                                <b-td rowspan="3" class="text-left">Respirasi</b-td>
                                                <b-td class="text-left">Batuk / menangis</b-td>
                                                <b-td>2</b-td>
                                                <b-td v-for="slot in padssTimeSlots" :key="'resp2_'+slot.key">
                                                    <b-form-radio
                                                        :name="'steward_respiration_'+slot.key"
                                                        v-model="form6_steward.category_score.respiration[slot.key]"
                                                        :value="2"
                                                        :disabled="isStewardSlotLocked('respiration',slot.key)"/>
                                                </b-td>
                                            </b-tr>

                                            <b-tr>
                                                <b-td class="text-left">Perlu bantuan bernafas</b-td>
                                                <b-td>1</b-td>
                                                <b-td v-for="slot in padssTimeSlots" :key="'resp1_'+slot.key">
                                                    <b-form-radio
                                                        :name="'steward_respiration_'+slot.key"
                                                        v-model="form6_steward.category_score.respiration[slot.key]"
                                                        :value="1"
                                                        :disabled="isStewardSlotLocked('respiration',slot.key)"/>
                                                </b-td>
                                            </b-tr>

                                            <b-tr>
                                                <b-td class="text-left">Apneu</b-td>
                                                <b-td>0</b-td>
                                                <b-td v-for="slot in padssTimeSlots" :key="'resp0_'+slot.key">
                                                    <b-form-radio
                                                        :name="'steward_respiration_'+slot.key"
                                                        v-model="form6_steward.category_score.respiration[slot.key]"
                                                        :value="0"
                                                        :disabled="isStewardSlotLocked('respiration',slot.key)"/>
                                                </b-td>
                                            </b-tr>

                                            <!-- AKTIVITAS MOTORIK -->
                                            <b-tr>
                                                <b-td rowspan="3" class="text-left">Aktivitas Motorik</b-td>
                                                <b-td class="text-left">Gerakan bertujuan</b-td>
                                                <b-td>2</b-td>
                                                <b-td v-for="slot in padssTimeSlots" :key="'motor2_'+slot.key">
                                                    <b-form-radio
                                                        :name="'steward_motor_activities_'+slot.key"
                                                        v-model="form6_steward.category_score.motor_activities[slot.key]"
                                                        :value="2"
                                                        :disabled="isStewardSlotLocked('motor_activities',slot.key)"/>
                                                </b-td>
                                            </b-tr>

                                            <b-tr>
                                                <b-td class="text-left">Gerakan tanpa tujuan</b-td>
                                                <b-td>1</b-td>
                                                <b-td v-for="slot in padssTimeSlots" :key="'motor1_'+slot.key">
                                                    <b-form-radio
                                                        :name="'steward_motor_activities_'+slot.key"
                                                        v-model="form6_steward.category_score.motor_activities[slot.key]"
                                                        :value="1"
                                                        :disabled="isStewardSlotLocked('motor_activities',slot.key)"/>
                                                </b-td>
                                            </b-tr>

                                            <b-tr>
                                                <b-td class="text-left">Tidak bergerak</b-td>
                                                <b-td>0</b-td>
                                                <b-td v-for="slot in padssTimeSlots" :key="'motor0_'+slot.key">
                                                    <b-form-radio
                                                        :name="'steward_motor_activities_'+slot.key"
                                                        v-model="form6_steward.category_score.motor_activities[slot.key]"
                                                        :value="0"
                                                        :disabled="isStewardSlotLocked('motor_activities',slot.key)"/>
                                                </b-td>
                                            </b-tr>

                                            <b-tr>
                                                <b-th colspan="3">TOTAL</b-th>
                                                <b-td
                                                    v-for="slot in padssTimeSlots"
                                                    :key="'steward_total_'+slot.key">
                                                    {{ stewardSlotTotal[slot.key] !== null ? stewardSlotTotal[slot.key] : '-' }}
                                                </b-td>
                                            </b-tr>

                                            <b-tr>
                                                <b-th colspan="10" class="text-left">
                                                    Boleh pindah ruang jika skor ≥ 5
                                                    <span v-if="steward_final_score !== null">
                                                        (Skor akhir saat ini:
                                                        <strong>{{ steward_final_score }}</strong>)
                                                    </span>
                                                </b-th>
                                            </b-tr>

                                        </b-tbody>
                                    </b-table-simple>
                                </section>

                                <div class="card-footer" style="margin:20px -20px -20px -20px">
                                    <div class="d-flex justify-content-end">
                                        <b-button
                                            variant="primary"
                                            class="px-3"
                                            @click="submit_steward_form"
                                            :disabled="isLoading">
                                            <feather-icon icon="SaveIcon"/>
                                            Simpan
                                        </b-button>
                                    </div>
                                </div>
                            </b-tab>
                            <b-tab>
                                <template #title>
                                    <strong>Tindakan RR</strong>
                                </template>
                                
                                <section class="">
                                    <div>
                                        <label class="c-form-label">INSTRUKSI/TINDAKAN DI RUANG PULIH SADAR (RR)</label>
                                        <b-textarea rows="3" v-model="rr_action_form.instruction"></b-textarea>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6 mt-12">
                                            <label class="c-form-label">JAM MASUK RUANG RR</label>
                                            <b-form-input type="time" v-model="rr_action_form.time_in"></b-form-input>
                                        </div>
                                        <div class="col-lg-6 mt-12">
                                            <label class="c-form-label">JAM KELUAR RUANG RR</label>
                                            <b-form-input type="time" v-model="rr_action_form.time_out"></b-form-input>
                                        </div>
                                    </div>
                                </section>

                                <div class="card-footer" style="margin: 20px -20px -20px -20px">
                                    <div class="d-flex justify-content-end">
                                        <b-button variant="primary" class="px-3" @click="submit_rr_action_form">
                                            <feather-icon icon="SaveIcon" /> Simpan
                                        </b-button>
                                    </div>
                                </div>
                            </b-tab>
                            <b-tab>
                                <template #title>
                                    <strong>Serah Terima Post-Operasi</strong>
                                </template>
                                
                                <section class="">
                                    <div class="row">
                                        <div class="col-lg-4 py-3 mt-2 general-box">
                                            <div>
                                                <label class="c-form-label font-weight-bold text-uppercase">STATUS PASIEN</label>
                                                <b-form-radio-group v-model="post_op_handover_form.patient_status" stacked size="sm">
                                                    <b-form-radio value="1">SUDAH</b-form-radio>
                                                    <b-form-radio value="2">BELUM</b-form-radio>
                                                </b-form-radio-group>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 py-3 mt-2 mid-box">
                                            <div>
                                                <label class="c-form-label font-weight-bold text-uppercase">FOTO RONTGEN</label>
                                                <b-form-radio-group v-model="post_op_handover_form.rontgen_photo" stacked size="sm">
                                                    <b-form-radio value="1">SUDAH</b-form-radio>
                                                    <b-form-radio value="2">BELUM</b-form-radio>
                                                </b-form-radio-group>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 py-3 mt-2 general-box">
                                            <div>
                                                <label class="c-form-label font-weight-bold text-uppercase">INSTRUKSI POST OPERASI</label>
                                                <b-form-radio-group v-model="post_op_handover_form.post_instruction" stacked size="sm">
                                                    <b-form-radio value="1">SUDAH</b-form-radio>
                                                    <b-form-radio value="2">BELUM</b-form-radio>
                                                </b-form-radio-group>
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="d-none d-lg-block">
                                    <div class="row">
                                        <div class="col-lg-4 py-3 mt-2 general-box">
                                            <div>
                                                <label class="c-form-label font-weight-bold text-uppercase">TERPASANG DRAIN</label>
                                                <b-form-radio-group v-model="post_op_handover_form.drain_installed" stacked size="sm">
                                                    <b-form-radio value="1">SUDAH</b-form-radio>
                                                    <b-form-radio value="2">BELUM</b-form-radio>
                                                </b-form-radio-group>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 py-3 mt-2 mid-box">
                                            <div>
                                                <label class="c-form-label font-weight-bold text-uppercase">TAMPON</label>
                                                <b-form-radio-group v-model="post_op_handover_form.tampon" stacked size="sm">
                                                    <b-form-radio value="1">SUDAH</b-form-radio>
                                                    <b-form-radio value="2">BELUM</b-form-radio>
                                                </b-form-radio-group>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 py-3 mt-2 general-box">
                                            <div>
                                                <label class="c-form-label font-weight-bold text-uppercase">INFUS</label>
                                                <b-form-radio-group v-model="post_op_handover_form.infusion" stacked size="sm">
                                                    <b-form-radio value="1">SUDAH</b-form-radio>
                                                    <b-form-radio value="2">BELUM</b-form-radio>
                                                </b-form-radio-group>
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="d-none d-lg-block">
                                    <div class="row">
                                        <div class="col-lg-4 py-3 mt-2 general-box">
                                            <div>
                                                <label class="c-form-label font-weight-bold text-uppercase">TRANSFUSI</label>
                                                <b-form-radio-group v-model="post_op_handover_form.tranfusion" stacked size="sm">
                                                    <b-form-radio value="1">SUDAH</b-form-radio>
                                                    <b-form-radio value="2">BELUM</b-form-radio>
                                                </b-form-radio-group>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 py-3 mt-2 mid-box">
                                            <div>
                                                <label class="c-form-label font-weight-bold text-uppercase">PASANG NGT</label>
                                                <b-form-radio-group v-model="post_op_handover_form.install_ngt" stacked size="sm">
                                                    <b-form-radio value="1">SUDAH</b-form-radio>
                                                    <b-form-radio value="2">BELUM</b-form-radio>
                                                </b-form-radio-group>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 py-3 mt-2 general-box">
                                            <div>
                                                <label class="c-form-label font-weight-bold text-uppercase">ETT/label</label>
                                                <b-form-radio-group v-model="post_op_handover_form.ett_label" stacked size="sm">
                                                    <b-form-radio value="1">SUDAH</b-form-radio>
                                                    <b-form-radio value="2">BELUM</b-form-radio>
                                                </b-form-radio-group>
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="d-none d-lg-block">
                                    <div class="row">
                                        <div class="col-lg-4 py-3 mt-2 general-box">
                                            <div>
                                                <label class="c-form-label font-weight-bold text-uppercase">CATHETER</label>
                                                <b-form-radio-group v-model="post_op_handover_form.catheter" stacked size="sm">
                                                    <b-form-radio value="1">SUDAH</b-form-radio>
                                                    <b-form-radio value="2">BELUM</b-form-radio>
                                                </b-form-radio-group>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 py-3 mt-2 mid-box">
                                            <div>
                                                <label class="c-form-label font-weight-bold text-uppercase">LAIN-LAIN</label>
                                                <b-form-radio-group v-model="post_op_handover_form.others" stacked size="sm">
                                                    <b-form-radio value="1">SUDAH</b-form-radio>
                                                    <b-form-radio value="2">BELUM</b-form-radio>
                                                </b-form-radio-group>
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="d-none d-lg-block">
                                    <div class="mt-12">
                                        <label class="c-form-label text-uppercase">CATATAN</label>
                                        <b-textarea rows="2" v-model="post_op_handover_form.notes" />
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label text-uppercase">CATATAN PASANG DRAIN</label>
                                        <b-textarea rows="2" v-model="post_op_handover_form.drain_install_notes" />
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label text-uppercase">CATATAN PASANG INFUS</label>
                                        <b-textarea rows="2" v-model="post_op_handover_form.infusion_install_notes" />
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label text-uppercase">CATATAN TRANSFUSI</label>
                                        <b-textarea rows="2" v-model="post_op_handover_form.infusion_notes" />
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label text-uppercase">CATATAN PASANG NGT</label>
                                        <b-textarea rows="2" v-model="post_op_handover_form.ngt_install_notes" />
                                    </div>
                                    <div class="mt-12">
                                        <label class="c-form-label text-uppercase">LAINNYA</label>
                                        <b-textarea rows="2" v-model="post_op_handover_form.other_notes" />
                                    </div>
                                </section>

                                <div class="card-footer" style="margin: 20px -20px -20px -20px">
                                    <div class="d-flex justify-content-end">
                                        <b-button variant="primary" class="px-3" @click="submit_post_op_handover_form">
                                            <feather-icon icon="SaveIcon" /> Simpan
                                        </b-button>
                                    </div>
                                </div>
                            </b-tab>
                        </b-tabs>
                    </b-card>
                    
                </section>

                <!-- Current_tab = menu7 -->
                <section v-if="active.current_tab === 'menu7'" class="card p-2 mt-24">
                    <div v-if="Object.keys(operativeReportData).length !== 0" class="fs-13 mb-3 d-flex justify-content-start"> 
                        <b-button
                        :to="`/operatie-kamer/print/${this.ok_id}/${this.operativeReportData.id}`"   
                        target="_blank"
                        size="sm" variant="w3green">
                            <b-icon icon="printer" class="mr-1" /> Print Laporan Operasi
                        </b-button>
                    </div>

                    <section class="card">
                        <div class="card-header font-weight-bold bg-primary-200">Laporan Pembedahan / Tindakan</div>
                        <div class="card-body">
                            <div class="">
                                <div>
                                    <ModernLabel label="Diagnosis (ICD10) Pra Prosedur" />
                                    <b-table-simple bordered responsive class="mt-12" style="font-size: 14px">
                                        <b-tr>
                                            <b-th style="width: 60px;">No</b-th>
                                            <b-th>ICD10</b-th>
                                            <b-th style="width: 100px;">Aksi</b-th>
                                        </b-tr>
                                        
                                        <template v-for="(item,index) in form7_eval.pre_icd10">
                                            <b-tr class="item">
                                                <b-td>{{ index+1 }}</b-td>
                                                <b-td>
                                                    <div>{{ item.title }}</div>
                                                </b-td>
                                                <b-td>
                                                    <b-button variant="danger-600" size="sm" @click="deleteTab7PraEval10(index)" class="mr-2">
                                                        <feather-icon icon="TrashIcon" />
                                                    </b-button>
                                                </b-td>
                                            </b-tr>
                                        </template> 
                                    </b-table-simple>
                                    <div class="mt-10">
                                        <label class="text-semi-14">Diagnosa</label>
                                        <v-select v-model="evalTab7PraIcd10_id" aria-readonly="true" @input="onAddTab7PraEval10($event)" @search="onSearchTab7EvalPraIcd10" :options="OptionsTab7EvalPraIcd10" :reduce="OptionsTab7EvalPraIcd10 => OptionsTab7EvalPraIcd10" label="title" placeholder="-- Diagnosa --"></v-select>
                                    </div>
                                </div>

                                <!-- <b-textarea rows="2" type="text" v-model="form7_eval.pra_icd10_notes" autocomplete="off" placeholder="Catatan Pra - ICD10" class="mt-12"></b-textarea> -->

                                <div class="mt-24" >
                                    <ModernLabel label="Diagnosis Non-ICD10 Pra Prosedur" />
                                    <b-table-simple bordered responsive class="mt-12" style="font-size: 14px">
                                        <b-tr>
                                            <b-th style="width: 60px;">No</b-th>
                                            <b-th>Non-ICD10</b-th>
                                            <b-th style="width: 100px;">Aksi</b-th>
                                        </b-tr>
                                        
                                        <template v-for="(item,index) in form7_eval.pre_non_icd10">
                                            <b-tr class="item">
                                                <b-td>{{ form7_eval.pre_icd10.length+index+1 }}</b-td>
                                                <b-td>
                                                    <div>{{ item }}</div>
                                                </b-td>
                                                <b-td>
                                                    <b-button variant="danger-600" size="sm" @click="removeTab7EvalPraOther10(index)" class="mr-2">
                                                        <feather-icon icon="TrashIcon" />
                                                    </b-button>
                                                </b-td>
                                            </b-tr>
                                        </template> 
                                    </b-table-simple>
                                    <div class="mt-10">
                                        <label class="text-semi-14">Non-ICD10</label>
                                        <b-form-input v-model="input_pra_other10_tab7" />
                                        <b-button @click="addTab7EvalPraOther10" size="sm" variant="outline-primary" class="mt-12">Tambah</b-button>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-20">
                                <div>
                                    <ModernLabel label="Diagnosis (ICD10) Pasca Prosedur" />
                                    <b-table-simple bordered responsive class="mt-12" style="font-size: 14px">
                                        <b-tr>
                                            <b-th style="width: 60px;">No</b-th>
                                            <b-th>ICD10</b-th>
                                            <b-th style="width: 100px;">Aksi</b-th>
                                        </b-tr>
                                        <template v-for="(item,index) in form7_eval.post_icd10">
                                            <b-tr class="item">
                                                <b-td>{{ index+1 }}</b-td>
                                                <b-td>
                                                    <div>{{ item.title }}</div>
                                                </b-td>
                                                <b-td>
                                                    <b-button variant="danger-600" size="sm" @click="deleteTab7PostEval10(index)" class="mr-2">
                                                        <feather-icon icon="TrashIcon" />
                                                    </b-button>
                                                </b-td>
                                            </b-tr>
                                        </template> 
                                    </b-table-simple>
                                    <div class="mt-10">
                                        <label class="text-semi-14">Diagnosa</label>
                                        <v-select v-model="evalTab7PostIcd10_id" aria-readonly="true" @input="onAddTab7PostEval10($event)" @search="onSearchTab7EvalPostIcd10" :options="OptionsTab7EvalPostIcd10" :reduce="OptionsTab7EvalPostIcd10 => OptionsTab7EvalPostIcd10" label="title" placeholder="-- Diagnosa --"></v-select>
                                    </div>
                                </div>

                                <!-- <b-textarea rows="2" type="text" v-model="form7_eval.post_icd10_notes" autocomplete="off" placeholder="Catatan Pasca - ICD10" class="mt-12"></b-textarea> -->

                                <div class="mt-24" >
                                    <ModernLabel label="Diagnosis Non-ICD10 Pasca Prosedur" />
                                    <b-table-simple bordered responsive class="mt-12" style="font-size: 14px">
                                        <b-tr>
                                            <b-th style="width: 60px;">No</b-th>
                                            <b-th>Non-ICD10</b-th>
                                            <b-th style="width: 100px;">Aksi</b-th>
                                        </b-tr>
                                        <template v-for="(item,index) in form7_eval.post_non_icd10">
                                            <b-tr class="item">
                                                <b-td>{{ form7_eval.post_icd10.length+index+1 }}</b-td>
                                                <b-td>
                                                    <div>{{ item }}</div>
                                                </b-td>
                                                <b-td>
                                                    <b-button variant="danger-600" size="sm" @click="removeTab7EvalPostOther10(index)" class="mr-2">
                                                        <feather-icon icon="TrashIcon" />
                                                    </b-button>
                                                </b-td>
                                            </b-tr>
                                        </template> 
                                    </b-table-simple>
                                    <div class="mt-10">
                                        <label class="text-semi-14">Non-ICD10</label>
                                        <b-form-input v-model="input_post_other10_tab7" />
                                        <b-button @click="addTab7EvalPostOther10" size="sm" variant="outline-primary" class="mt-12">Tambah</b-button>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-12">
                                <label class="c-form-label">Jenis operasi / tindakan</label>
                                <b-textarea rows="2" v-model="form7_eval.therapy_plan"/>
                            </div>
                            <div class="row">
                                <div class="col-6 col-lg-3 mt-12">
                                    <label class="c-form-label">Jam mulai</label>
                                    <b-form-input type="time" v-model="form7_eval.start_time"/>
                                </div>
                                <div class="col-6 col-lg-3 mt-12">
                                    <label class="c-form-label">Jam selesai</label>
                                    <b-form-input type="time" v-model="form7_eval.finish_time" />
                                </div>
                            </div>
                            <!-- <div class="row">
                                <div class="col-lg-3 mt-12">
                                    <label class="c-form-label">Dokter operator</label>
                                    <v-select disabled v-model="doctor_operator" :options="doctor_list" :reduce="doctor_list => doctor_list.value" label="title" placeholder="-- Operator --"></v-select>
                                </div>
                                <div class="col-lg-3 mt-12">
                                    <label class="c-form-label">Asisten</label>
                                    <v-select disabled v-model="assistant_doctor" :options="doctor_list" :reduce="doctor_list => doctor_list.value" label="title" placeholder="-- Asisten --"></v-select>
                                </div>
                                <div class="col-lg-3 mt-12">
                                    <label class="c-form-label">Perawat Instrumen</label>
                                    <v-select disabled v-model="instrument_nurse" :options="nurse_list" :reduce="nurse_list => nurse_list.value" label="title" placeholder="-- Perawat Instrumen --"></v-select>
                                </div>
                                <div class="col-lg-3 mt-12">
                                    <label class="c-form-label">Dokter Anestesi</label>
                                    <v-select disabled v-model="anesthesiologist" :options="anesthesiologist_list" :reduce="anesthesiologist_list => anesthesiologist_list.value" label="title" placeholder="-- Dokter Anestesi --"></v-select>
                                </div>
                            </div> -->
                            <div class="mt-32">
                                <ModernLabel label="Informasi Operator" />
                                <div class="mt-20">
                                    <b-button @click="openModalOperator" size="sm" variant="outline-primary" pill class="px-3">
                                        <feather-icon icon="PlusIcon" /> Tambah Operator
                                    </b-button>
                                    
                                    <b-table :fields="operatorFields" responsive bordered :items="operator_list" class="mt-16" style="font-size: 13px">
                                        <template #cell(no)="row">
                                            <div class="text-center">{{ row.index+1 }}</div>
                                        </template>
                                        <template #cell(operator_role_type_name)="row">
                                            <div class="text-center">
                                                <span v-if="row.item.operator_role_type_id == 1">Operator</span>
                                                <span v-if="row.item.operator_role_type_id == 2">Co-operator</span>
                                                <span v-if="row.item.operator_role_type_id == 3">Instrumen</span>
                                                <span v-if="row.item.operator_role_type_id == 4">Circular</span>
                                                <span v-if="row.item.operator_role_type_id == 5">Anastesi</span>
                                                <span v-if="row.item.operator_role_type_id == 6">Bidan</span>
                                                <span v-if="row.item.operator_role_type_id == 7">DPJP</span>
                                                <span v-if="row.item.operator_role_type_id == 8">Dokter Umum</span>
                                            </div>
                                        </template>
                                        <template #cell(action)="row">
                                            <div class="d-flex justify-content-center">
                                                <b-button @click="deleteOperator(row.item)" variant="danger-600" size="sm" v-b-popover.hover.top="'Hapus'">
                                                    <feather-icon icon="TrashIcon" />
                                                </b-button>
                                            </div>
                                        </template>
                                    </b-table>
                                    <b-modal v-model="active.modal_operator" size="lg" title="Tambah Operator" no-close-on-backdrop>
                                        <section class="">
                                            <section class="card">
                                                <div class="card-body">
                                                    <div class="row justify-content-center align-items-end">
                                                        <div class="col-lg-5">
                                                            <label class="c-form-label">Operator <span class="text-danger">*</span></label>
                                                            <v-select v-model="input_operator.employee" :options="doctor_list" :reduce="doctor_list => doctor_list" label="title" placeholder="-- Operator --"></v-select>
                                                        </div>
                                                        <div class="col-lg-5 mt-12 mt-lg-0">
                                                            <label class="c-form-label">Peran <span class="text-danger">*</span></label>
                                                            <v-select v-model="input_operator.role" :options="OptionsRole" :reduce="OptionsRole => OptionsRole" label="title" placeholder="-- Peran --"></v-select>
                                                        </div>
                                                        <div class="px-3 mt-12 mt-lg-0">
                                                            <b-button variant="outline-primary" @click="onAddOperator">
                                                                <feather-icon icon="PlusIcon" /> Tambah
                                                            </b-button>                            
                                                        </div>
                                                    </div>
                                                </div>
                                            </section>

                                            <b-table :fields="operatorFields" responsive bordered :items="form_operator.operators" class="mt-16" style="font-size: 13px">
                                                <template #cell(no)="row">
                                                    <div class="text-center">{{ row.index+1 }}</div>
                                                </template>
                                                <template #cell(action)="row">
                                                    <div class="d-flex justify-content-center">
                                                        <b-button variant="danger-600" size="sm" @click="removeOperator(row.index)" v-b-popover.hover.top="'Hapus'">
                                                            <feather-icon icon="TrashIcon" />
                                                        </b-button>
                                                    </div>
                                                </template>
                                            </b-table>
                                        </section>

                                        <template #modal-footer>
                                            <div class="w-100 d-flex justify-content-end">
                                                <b-button @click="validationOperator" :disabled="disabled.operator" variant="primary" class="px-3">
                                                    <feather-icon icon="SaveIcon" /> Simpan
                                                </b-button>
                                            </div>
                                        </template>
                                    </b-modal>
                                </div>
                            </div>
                            <div class="mt-12">
                                <label class="c-form-label">Jenis Anestesi</label>
                                <b-textarea rows="2" v-model="form7_eval.anesthesia_type" />
                            </div>
                            <div class="row">
                                <div class="col-6 col-lg-3 mt-12">
                                    <label class="c-form-label">Jam mulai anestesi</label>
                                    <b-form-input type="time" v-model="form7_eval.start_anesthesia_time"/>
                                </div>
                                <div class="col-6 col-lg-3 mt-12">
                                    <label class="c-form-label">Jam selesai anestesi</label>
                                    <b-form-input type="time" v-model="form7_eval.finish_anesthesia_time" />
                                </div>
                            </div>
                            <div class="mt-12">
                                <label class="c-form-label">Khusus Operasi, Jenis</label>
                                <b-form-radio-group size="sm" v-model="form7_eval.special_operation_type_id">
                                    <b-form-radio value="1">Bersih</b-form-radio>
                                    <b-form-radio value="2">Bersih Terkontaminasi</b-form-radio>
                                    <b-form-radio value="3">Terkontaminasi</b-form-radio>
                                    <b-form-radio value="4">Kotor</b-form-radio>
                                </b-form-radio-group>
                            </div>
                            <div class="mt-12">
                                <label class="c-form-label">Golongan Operasi</label>
                                <b-form-radio-group size="sm" v-model="form7_eval.operation_group_type_id">
                                    <b-form-radio value="1">Sedang</b-form-radio>
                                    <b-form-radio value="2">Besar</b-form-radio>
                                    <b-form-radio value="3">Khusus</b-form-radio>
                                </b-form-radio-group>
                            </div>
                            <div class="row">
                                <div class="col-6 col-lg-3 mt-12">
                                    <label class="c-form-label">Pendarahan</label>
                                    <b-input-group append="mL">
                                        <b-form-input type="number" autocomplete="off" v-model="form7_eval.bleeding"></b-form-input>
                                    </b-input-group>
                                </div>
                                <div class="col-6 col-lg-3 mt-12">
                                    <label class="c-form-label" >Drain</label>
                                    <b-form-input v-model="form7_eval.drain"/>
                                </div>
                            </div>
                            <div class="mt-12">
                                <label class="c-form-label">Laporan pembedahan / tindakan</label>
                                <b-textarea rows="5" v-model="form7_eval.surgical_report"/>
                            </div>
                            <div class="mt-12">
                                <label class="c-form-label">Implant</label>
                                <b-form-radio-group size="sm" v-model="form7_eval.implant_type_id">
                                    <b-form-radio value="1">Ya</b-form-radio>
                                    <b-form-radio value="0">Tidak</b-form-radio>
                                </b-form-radio-group>
                                <template v-if="form7_eval.implant_type_id == 1">
                                    <label class="c-form-label">Kode Implant</label>
                                    <b-form-input v-model="form7_eval.implant_code" class="mt-10" />                                
                                </template>
                            </div>
                            <div class="mt-12">
                                <label class="c-form-label">Penyulit/komplikasi pada prosedur ini</label>
                                <b-form-radio-group size="sm" v-model="form7_eval_temp.complications_type_id">
                                    <b-form-radio value="1">Ya</b-form-radio>
                                    <b-form-radio value="0">Tidak</b-form-radio>
                                </b-form-radio-group>
                                <template v-if="form7_eval_temp.complications_type_id == 1">
                                    <b-form-input v-model="form7_eval.complications" class="mt-10" />                                
                                </template>
                            </div>
                            <div class="mt-12">
                                <label class="c-form-label">Terdapat jaringan / organ yang diangkat</label>
                                <b-form-radio-group size="sm" v-model="form7_eval_temp.tissues_organs_removed_type_id">
                                    <b-form-radio value="1">Ya</b-form-radio>
                                    <b-form-radio value="0">Tidak</b-form-radio>
                                </b-form-radio-group>
                                <template v-if="form7_eval_temp.tissues_organs_removed_type_id == 1">
                                    <b-form-input v-model="form7_eval.tissues_organs_removed" class="mt-10" />                                
                                </template>
                            </div>
                            <div class="mt-12">
                                <label class="c-form-label">Sample dikirim ke Patologi Anatomi</label>
                                <b-form-radio-group size="sm" v-model="form7_eval.samples_sent">
                                    <b-form-radio value="1">Ya</b-form-radio>
                                    <b-form-radio value="0">Tidak</b-form-radio>
                                </b-form-radio-group>
                            </div>  
                            <div class="mt-12">
                                <label class="c-form-label">Instruksi penatalaksanaan pasca bedah/tindakan</label>
                                <b-textarea rows="5" v-model="form7_eval.treatment_management_instructions"/>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex justify-content-end">
                                <b-button @click="validationOperativeReport" :disabled="disabled.operative_report" variant="primary" class="px-3">
                                    <feather-icon icon="SaveIcon" /> Simpan
                                </b-button>
                            </div>
                        </div>
                    </section>
                </section>
            </section>
        </Layout>
    </div>
</template>
<script>
import { mapState } from 'vuex'
import patientMenu from '@/@core/components/tab-menu/patientMenu.vue';
import administration from '@/views/layouts/patient/doctor/administration.vue';
import ModernLabel from '@/@core/components/label/ModernLabel.vue';
import vSelect from 'vue-select'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import moment from 'moment'
import MuskuloskeletalLakiView from '@/views/pages/canvasing/jsView/MuskuloskeletalLakiView.vue'
import MuskuloskeletalPrView from '@/views/pages/canvasing/jsView/MuskuloskeletalPrView.vue'
import KepalaView from '@/views/pages/canvasing/jsView/KepalaView.vue'
import EkstrimAtasView from '@/views/pages/canvasing/jsView/EkstrimAtasView.vue'
import EkstrimBawahView from '@/views/pages/canvasing/jsView/EkstrimBawahView.vue'


export default {
    name: 'OperatieDetail',
    components: {
        patientMenu, administration, ModernLabel, vSelect, MuskuloskeletalLakiView, MuskuloskeletalPrView, KepalaView, EkstrimAtasView, EkstrimBawahView
    },
    data() {
        return {
            userData: JSON.parse(localStorage.getItem('userData')),
            reg_des_id: '',
            ok_id: '',
            active: {
                modal_operator: false,
                current_tab: 'menu1',
                is_operation_detail_disabled: true,
            },
            disabled: {
                operator: false,
                operative_report: false,
            },
            regData: {},
            doctor_list: [],
            nurse_list:[],
            operatingData: {},
            operativeReportData:{},
            params: {
                limit: 20,
                offset: 0,
                norm: '',
                name: '',
                date_start: moment(new Date()).format('YYYY-MM-DD'),
                date_end: moment(new Date()).format('YYYY-MM-DD'),
            },
            isDisabledFilter: false,

            OptionsRole: [
                { value: 1, title: 'Operator' },
                { value: 2, title: 'Co-operator' },
                { value: 3, title: 'Instrumen' },
                { value: 4, title: 'Circular' },
                { value: 5, title: 'Anastesi' },
                { value: 6, title: 'Bidan' },
                { value: 7, title: 'DPJP' },
                { value: 8, title: 'Dokter Umum' },
            ],

            OptionsTooth: [
                { value: 1, title: 'Tampak Normal' },
                { value: 2, title: 'Goyah' },
                { value: 3, title: 'Gigi Palsu' },
            ],

            OptionsAwareness: [
                { value: 1, title: 'Eye' },
                { value: 2, title: 'Motoric' },
                { value: 3, title: 'Verbal' },
            ],

            OptionsDentures: [
                { value: 1, title: 'Semua' },
                { value: 2, title: 'Sebagian' },
            ],

            OptionsDenturesPosition: [
                { value: 1, title: 'Atas' },
                { value: 2, title: 'Bawah' },
            ],

            OptionsDummy: [
                { value: '1', title: 'Option 1' },
                { value: '2', title: 'Option 2' },
                { value: '3', title: 'Option 3' },
                { value: '4', title: 'Option 4' },
            ],

            ICD9TableFields: [
                { key: 'no', label: 'No', thStyle: 'width: 50px; text-align: center' },
                { key: 'title', label: 'ICD9CM' },
                { key: 'action', label: 'Aksi', thStyle: 'width: 100px; text-align: center' },
            ],

            // Section Tab 1: Detail Operasi
            operator_list: [],
            operatorFields: [
                { key: 'no', label: 'No.', thStyle: { width: '50px' }},
                { key: 'operator_name', label: 'Nama' },
                { key: 'operator_role_type_name', label: 'Peran', },
                { key: 'action', label: 'Action', thStyle: { width: '100px' } },
            ],
            form_operator: {
                operating_room_id: '',
                operators: []
            },
            input_operator: {
                employee: '',
                role: '',
            },

            PrescriptionOptions:[
                { value: 1, title: 'BHP SATUAN' },
            ],
            PrescriptionTypeOptions:[
                { value: 3, title: 'BHP' },
            ],
            form_transaction: {
                id:'',
                view:'add',
                type_prescription_id:3,
                soap_p_id:'',
                registration_destination_id:'',
                pharmacy_depo_id:'',
                date_added: moment(new Date()).format('YYYY-MM-DD'),
                norm:'',
                health_facility_item_id:'',
                form_prescription_parent:[],
                form_prescription:[]
            },
            catalog:'',
            prescription:'',
            prescription_name:'OBAT SATUAN',
            is_submit:false,

            // Section Tab 2: Pra Operasi
            editable: {
				ekstrimAtas: false,
				ekstrimBawah: false,
				kepala: false,
				musLaki: false,
				musPr: false,
			},


            // Section Tab 3: Keperawatan Operasi
            patientVerificationItems: [
                { key: 'check_identity', label: 'Periksa identitas pasien' },
                { key: 'check_bracelet', label: 'Periksa gelang identitas / gelang alergi' },
                { key: 'operation_scheduling', label: 'Formular penjadwalan operasi / PBO' },
                { key: 'type_and_location', label: 'Jenis dan lokasi pembedahan dipastikan bersama pasien' },
                { key: 'languages_issue', label: 'Masalah bahasa dan komunikasi' },
                { key: 'check_surgical_consent', label: 'Periksa kelengkapan persetujuan pembedahan (surat ijin operasi)' },
                { key: 'check_anesthesia_consent', label: 'Periksa kelengkapan persetujuan anestesi' },
                { key: 'icu_availability', label: 'Surat ketersediaan ICU bila dibutuhkan' },
                { key: 'check_status', label: 'Periksa kelengkapan status rawat inap / rawat jalan' },
                { key: 'check_examination', label: 'Periksa kelengkapan Xray / CT-Scan / MRI / EKG Angiografi / Echo*' },
                { key: 'pre_anesthetic_evaluation', label: 'Evaluasi pra anestesi' },
                { key: 'patient_questionnaire', label: 'Kuesioner pasien sebelum tindakan' }
            ],
            // Daftar item untuk tabel II. PERSIAPAN FISIK PASIEN
            physicalPreparationItems: [
                { key: 'last_fast', label: 'Puasa / makan dan minum terakhir' },
                { key: 'external_prosthesis', label: 'Prothesa luar dilepaskan (gigi palsu, lensa kontak)' },
                { key: 'internal_prosthesis', label: 'Menggunakan prothesa dalam (pacemaker, implant, prothesis panggul / bahu, VP Shunt)' },
                { key: 'accessories', label: 'Penjepit rambut / cat kuku / perhiasan dilepaskan' },
                { key: 'skin_preparation', label: 'Persiapan kulit / cukur' },
                { key: 'bladder_emptying', label: 'Pengosongan kandung kemih / clysma' },
                { key: 'blood_supply', label: 'Memastikan persediaan darah' },
                { key: 'aids', label: 'Alat bantu (kaca mata, alat bantu dengar) disimpan' },
                { key: 'included_medications', label: 'Obat yang disertakan' },
                { key: 'last_medications', label: 'Obat terakhir yang diberikan' },
                // FIX: key diseragamkan jadi "vascular_access" (sebelumnya ada typo "vasvular_access")
                { key: 'vascular_access', label: 'Vaskuler akses (cimino) dll' }
            ],

            // FIX: options nurse ini seharusnya di-fetch dari BE (mis. endpoint /employees?role=nurse)
            // dan diisi di created()/mounted(), bukan hard-code.
            nurseOptions: [
                { value: null, text: '-- Pilih Perawat --' }
                // { value: 'uuid-employee-1', text: 'Nama Perawat 1' }, dst dari API
            ],

            form3_pre: {
                operating_room_id: '',
                ttv_temperature: '',
                ttv_pulse: '',
                ttv_rr: '',
                ttv_systolic: '',
                ttv_diastolic: '',
                ttv_sore_score: '',
                ttv_weight: '',
                ttv_height: '',
                mental_status_type_id: '',
                medical_history_arry: [],
                medical_history: '',
                current_treatment: '',
                tools_used: '',
                prev_operations_type_id: '',
                prev_operations_date: '',
                prev_operations_place: '',
                allergy_type_id: '',
                allergy: '',
                cough_flu_fever_type_id: '',
                period_type_id: '',
                patient_verification: {
                    check_identity_arry: [],
                    check_identity: '',
                    check_bracelet_arry: [],
                    check_bracelet: '',
                    operation_scheduling_arry: [],
                    operation_scheduling: '',
                    type_and_location_arry: [],
                    type_and_location: '',
                    languages_issue_arry: [],
                    languages_issue: '',
                    check_surgical_consent_arry: [],
                    check_surgical_consent: '',
                    check_anesthesia_consent_arry: [],
                    check_anesthesia_consent: '',
                    icu_availability_arry: [],
                    icu_availability: '',
                    check_status_arry: [],
                    check_status: '',
                    check_examination_arry: [],
                    check_examination: '',
                    pre_anesthetic_evaluation_arry: [],
                    pre_anesthetic_evaluation: '',
                    patient_questionnaire_arry: [],
                    patient_questionnaire: ''
                },
                physical_preparation: {
                    last_fast_arry: [],
                    last_fast: '',
                    external_prosthesis_arry: [],
                    external_prosthesis: '',
                    internal_prosthesis_arry: [],
                    internal_prosthesis: '',
                    accessories_arry: [],
                    accessories: '',
                    skin_preparation_arry: [],
                    skin_preparation: '',
                    bladder_emptying_arry: [],
                    bladder_emptying: '',
                    blood_supply_arry: [],
                    blood_supply: '',
                    aids_arry: [],
                    aids: '',
                    included_medications_arry: [],
                    included_medications: '',
                    last_medications_arry: [],
                    last_medications: '',
                    // FIX: typo "vasvular_access" -> "vascular_access", disamakan dengan v-model di template
                    vascular_access_arry: [],
                    vascular_access: ''
                },
                checked_room_nurse_id: '',
                checked_room_nurse_hours: '',
                checked_uko_nurse_id: '',
                checked_uko_nurse_hours: ''
            },

            form3_intra: {
                operating_room_id: "",
                timeout_type_id: 0,
                timeout: "",
                instrument_type_id: 0,
                instrument: "",
                prosthesis_implant_type_id: 0,
                prosthesis_implant: "",
                operation_start_time: "",
                operation_finish_time: "",
                anesthesia_start_time: "",
                anesthesia_finish_time: "",
                operation_performed: "",
                operation_type_id: 0,
                anesthesia_type_id: 0,
                level_of_awareness_type_id: 0,
                level_of_awareness_e: "",
                level_of_awareness_m: "",
                level_of_awareness_v: "",
                emotional_status_type_id: 0,
                cannula_position_type_id: 0,
                cannula_position: "",
                operation_position_type_id: 0,
                operation_position: "",
                operation_supervised_by_id: "",
                arm_position_type_id: 0,
                arm_position: "",
                aids_position_type_id: [], //harusnya arry, di be nya masih string
                aids_position: "",
                urinary_catheter_type_id: 0,
                skin_preparation_type_id: 0,
                skin_preparation: "",
                diathermy_type_id: 0,
                dispersive_electrode_type_id: 0,
                dispersive_electrode: "",
                skin_condition_before_type_id: 0,
                skin_condition_before: "",
                skin_condition_after_type_id: 0,
                skin_condition_after: "",
                heating_cooling_unit_type_id: 0,
                temperature: "",
                temperature_start_time: "",
                temperature_finish_time: "",
                tourniquet_supervised_by_id: "",
                tourniquet: {
                    tourniquet_right_arm_start_time: "",
                    tourniquet_right_arm_finish_time: "",
                    tourniquet_right_arm_pressure: "",
                    tourniquet_left_arm_start_time: "",
                    tourniquet_left_arm_finish_time: "",
                    tourniquet_left_arm_pressure: "",
                    tourniquet_right_foot_start_time: "",
                    tourniquet_right_foot_finish_time: "",
                    tourniquet_right_foot_pressure: "",
                    tourniquet_left_foot_start_time: "",
                    tourniquet_left_foot_finish_time: "",
                    tourniquet_left_foot_pressure: ""
                },
                use_of_laser_code: "",
                laser_supervised_by_id: "",
                use_of_implants_type_id: 0,
                implants_expired_date: "",
                implants_factory: "",
                implants_type: "",
                implants_size: "",
                implants_no: "",
                drain_usage: [], // di BE masih string, harusnya arry, nerima 4 inputan
                wound_irrigation_arry: [],
                wound_irrigation: "",
                fluid_usage_arry: [],
                fluid_usage_glycine: "",
                fluid_usage_sterile_water: "",
                fluid_usage_water_irrigation: "",
                fluid_usage_sodium_chloride: "",
                fluid_usage_other: "",
                tools_wrapped_type_id: 0,
                tools_wrapped_type: "",
                tools_wrapped_other: "",
                wrapping_type_id: 0,
                wrapping_type: "",
                wrapping_other: "", // ga dipake
                specimen_type_id: 0,
                specimen_histology: "",
                specimen_cytology: "",
                specimen_culture: "",
                specimen_other: "",
                specimen_other_type: '', // tambahin, belom ada di postman
                amount_of_fluid_examination: "",
            },
            input_drain: {
                drain_type: '',
                drain_class: '',
                drain_size: '',
                drain_notes: '',
            },
            DrainFields: [
                { key: 'drain_type', label: 'Tipe Drain' },
                { key: 'drain_class', label: 'Jenis Drain' },
                { key: 'drain_size', label: 'Ukuran' },
                { key: 'drain_notes', label: 'Keterangan' },
                { key: 'action', label: 'Aksi' },
            ],

            form3_post: {
                operating_room_id: "",
                postoperative_care_type_id: 0,
                transport_type_id: 0,
                general_condition_type_id: 0,
                gcs_type_id: 0,
                gcs_e: "",
                gcs_m: "",
                gcs_v: "",
                airway_type_id: 0,
                airway_problem: "",
                breathing_type_id: 0,
                oxygen_therapy_type_id: 0,
                oxygen_therapy_other: "",
                skin_arrive_type_id: 0,
                skin_arrive_other: "",
                skin_exit_type_id: 0,
                skin_exit_other: "",
                circulation_type_id: 0,
                position_type_id: 0,
                position_other: "",
                pupil_type_id: 0 
            },

            // Section tab 4: Keamanan Operasi 
            form4_signin: {
                confirmation_arry: [],
                operator_id: "",
                location_operation: "",
                operating_procedure: "",
                operating_area_type_id: 0,
                is_check_completeness: 0,
                is_check_anesthesia: 0,
                is_check_tools: 0,
                is_check_sterility: 0,
                is_fasting: 0,
                fasting_hours: "",
                history_asthma_type_id: 0,
                allergy_history_type_id: 0,
                allergy_history_name: "",
                special_equipment_type_id: 0,
                special_equipment_name: "",
                supporting_results_arry: [],
                blood_loss_type_id: 0,
                access_iv_no: "",
                blood_preparation: "",
                blood_type: "",
            },

            form4_timeout: {
                completeness_team_type_id: 0,
                reading_verbally_arry: [],
                premedication: "",
                given_hours: "",
                prohilaxix: "",
                prohilaxix_hours: "",
                rechecking_sterilization: "",
                radiology_photo_type_id: 0,
                checking: "",
                things_to_note: "",
            },

            form4_signout: {
                is_read_verbally: 0,
                is_read_amount: 0,
                is_read_completeness: 0,
                is_read_completeness_document: 0,
                throat_pack_type_id: 0,
                network_lift_type_id: 0,
                implant_installation_type_id: 0,
                tool_damaged_type_id: 0,
                tool_damaged: "",
                information: "",
            },

            // Section tab 5: Catatan Anestesi 
            form5_eval: {
                icd10: [], // belom ada di postman
                icd10_notes: '', // belom ada di postman
                non_icd10: [], // belom ada di postman
                icd9cm: [], // belom ada di postman
                therapy_plan: '', // belom ada di postman

                operating_room_id: "",
                awareness: "",
                weight: "",
                height: "",
                systolic: "",
                diastolic: "",
                pulse: "",
                respiratory_rate: "",
                temperature: "",
                cvp_sp02: "",
                history_complications_type_id: 0,
                tooth_type_id: 0,
                dentures_type_id: 0,
                dentures_position_type_id: 0,
                respiratory_path_type_id: [], // harusnya array
                mallampati_score_type_id: 0,
                respiration_arry: [],
                respiration_cigarrete_amount: "",
                respiration_cigarrete_long: "",
                respiration_asthma: "",
                cardiovascular_arry: [],
                cardiovascular_other: "",
                digestive_system_arry: [],
                digestive_system_other: "",
                neuromusculoskeletal_arry: [],
                neuromusculoskeletal_other: "",
                kidney_arry: [],
                other_arry: [],
                medications_currently: "",
                allergy: "",
                last_meal_hours: "",
                last_drink_hours: "",
                anesthesia_plan_arry: [],
                anesthesia_asa_type_id: 0,
                anesthesia_plan_type_id: 0,
                premedication: [],
            },
            evalIcd10_id: '',
            OptionsEvalIcd10: [],
            input_other10: '',
            evalIcd9cm_id: '',
            OptionsEvalIcd9cm: [],


            form5_notes: {
                operating_room_id: "",
                anesthesia_specialist: "",
                operator_id: "",
                action_date: "",
                therapy_plan: "",
                priority_type_id: 0,
                examination_date: "",
                mallampati_classification_type_id: 0,
                respiration_arry: [],
                respiration_dbn: 0,
                respiration_notes: "",
                cardiovascular_arry: [],
                cardiovascular_dbn: 0,
                cardiovascular_notes: "",
                hepato_arry: [],
                hepato_dbn: 0,
                hepato_notes: "",
                neuro_arry: [],
                neuro_dbn: 0,
                neuro_notes: "",
                renal_endocrine_arry: [],
                renal_endocrine_dbn: 0,
                renal_endocrine_notes: "",
                other_arry: [],
                other_dbn: 0,
                other_notes: "",
                drug_allergy: "",
                systolic: "",
                diastolic: "",
                weight: "",
                height: "",
                pulse_frequency: "",
                breath_frequency: "",
                temperature: "",
                medications_currently_type_id: 0,
                history_anesthesia_type_id: 0,
                anamnesa_from_type_id: 0, // tambahin, belom ada di postman
                fasting_solid_food: "",
                fasting_liquid_food: "",
                drugs_given_type_id: 0,
                anesthesia_plan_arry: [],
                anesthesia_asa_type_id: 0,
                anesthesia_plan_type_id: 0,
                premedication: []
            },
            premedicationFields: [
                { key: 'no', label: 'No', thStyle: 'width: 50px' },
                { key: 'drug', label: 'Obat' },
                { key: 'time', label: 'Waktu' },
                { key: 'action', label: 'Aksi' },
            ],
            input_premedication: {
                drug: '',
                time: null,
            },

            
            form5_monitor: {
                operating_room_id: "",
                past_medical_history: "",
                allergy_history: "",
                physiological_status_monitoring: [],
                post_surgery_awareness: "",
                post_surgery_systolic: "",
                post_surgery_diastolic: "",
                post_surgery_pulse: "",
                post_surgery_respiratory_rate: "",
                post_surgery_temperature: "",
                post_surgery_allergic_reaction: "",
                post_surgery_other_complications: "",
                // physiological_status_monitoring: [
                //     {
                //         monitoring_time: "",
                //         awareness: "",
                //         systolic: "",
                //         diastolic: "",
                //         pulse_frequency: "",
                //         breath_frequency: "",
                //         temperature: "",
                //         fluid: "",
                //         drugs: "",
                //         information: ""
                //     }
                // ]
            },
            input_fisio: {
                monitoring_time: null,
                awareness: '',
                systolic_blood_pressure: '',
                dyastolic_blood_pressure: '',
                pulse_frequency: '',
                breath_frequency: '',
                temperature: '',
                prescription: '',
                liquid: '',
                notes: '',
            },
            fisioFields: [
                { key: 'no', label: 'No', thStyle: 'width: 50px' },
                { key: 'monitoring_time', label: 'Tanggal / jam' },
                { key: 'awareness', label: 'Kesadaran' },
                { key: 'blood_pressure', label: 'Tekanan Darah' },
                { key: 'pulse_frequency', label: 'Nadi' },
                { key: 'breath_frequency', label: 'Respirasi' },
                { key: 'temperature', label: 'Suhu' },
                { key: 'prescription', label: 'Obat-obatan' },
                { key: 'liquid', label: 'Cairan' },
                { key: 'notes', label: 'Keterangan' },
                { key: 'action', label: 'Aksi' },
            ],      

            // Section tab 6: Post Operasi
            form6_bromage: {
                check_in_rr_time: null,
                check_in_rr_time_locked: false,
                transfer_time: null,
                slot_score: {
                    '15': null, '30': null, '45': null,
                    '60': null, '120': null, '180': null, '240': null
                },
                history: [] // [{monitoring_label, score, date_added}] -> SATU-SATUNYA sumber kebenaran
            },

            bromageTimeSlots: [
                { key: '15',  label: '15 mnt' },
                { key: '30',  label: '30 mnt' },
                { key: '45',  label: '45 mnt' },
                { key: '60',  label: '1 jam'  },
                { key: '120', label: '2 jam'  },
                { key: '180', label: '3 jam'  },
                { key: '240', label: '4 jam'  }
            ],

            form6_padss: {
                post_anesthesia_time: null,
                post_anesthesia_time_locked: false,
                transfer_time: null,
                transfer_time_locked: false,

                category_score: {
                    blood_pressure:  { '15': null, '30': null, '45': null, '60': null, '120': null, '180': null, '240': null },
                    activities:      { '15': null, '30': null, '45': null, '60': null, '120': null, '180': null, '240': null },
                    nausea_vomiting: { '15': null, '30': null, '45': null, '60': null, '120': null, '180': null, '240': null },
                    painful:         { '15': null, '30': null, '45': null, '60': null, '120': null, '180': null, '240': null },
                    bleeding:        { '15': null, '30': null, '45': null, '60': null, '120': null, '180': null, '240': null }
                },

                history: [] // [{category, monitoring_label, score, date_added}] -> SATU-SATUNYA sumber kebenaran
            },

            padssTimeSlots: [
                { key: '15',  label: '15 mnt' },
                { key: '30',  label: '30 mnt' },
                { key: '45',  label: '45 mnt' },
                { key: '60',  label: '1 jam'  },
                { key: '120', label: '2 jam'  },
                { key: '180', label: '3 jam'  },
                { key: '240', label: '4 jam'  }
            ],

            form6_aldrete: {
                post_anesthesia_time: null,
                post_anesthesia_time_locked: false,

                transfer_time: null,
                transfer_time_locked: false,

                category_score: {
                    motor_activities:{'15':null,'30':null,'45':null,'60':null,'120':null,'180':null,'240':null},
                    respiration:{'15':null,'30':null,'45':null,'60':null,'120':null,'180':null,'240':null},
                    circulation:{'15':null,'30':null,'45':null,'60':null,'120':null,'180':null,'240':null},
                    consciousness:{'15':null,'30':null,'45':null,'60':null,'120':null,'180':null,'240':null},
                    skin_color:{'15':null,'30':null,'45':null,'60':null,'120':null,'180':null,'240':null}
                },

                history:[]
            },

            form6_steward: {
                post_anesthesia_time: null,
                post_anesthesia_time_locked: false,
                transfer_time: null,
                transfer_time_locked: false,
                category_score: {
                    consciousness: {'15': null,'30': null,'45': null,'60': null,'120': null,'180': null,'240': null},
                    respiration: {'15': null,'30': null,'45': null,'60': null,'120': null,'180': null,'240': null},
                    motor_activities: {'15': null,'30': null,'45': null,'60': null,'120': null,'180': null,'240': null}
                },
                history: []
            },
            
            // Section Tab Serah Terima Pre Operasi
            pre_op_handover_form: {
                report_to_operating_doctor: '',
                report_to_operating_room: '',
                filling_informed_consent: '',
                shaving_operating_area: '',
                install_infusion: '',
                transfusion: '',
                install_catheter: '',
                install_ngt: '',
                clisma: '',
                ecg: '',
                usg: '',
                remove_cosmetics: '',
                remove_softlens: '',
                check_bracelet: '',
                fasting: '',
                prophilaxis: '',
                premedication: '',
                others: '',
                rontgen: '',
                theraphy: '',
                infusion_install_notes: '',
                infusion_notes: '',
                ngt_install_notes: '',
                location_mark: '',
                other_notes: '',
                muskuloskeletal_laki: null,
                muskuloskeletal_pr: null,
                kepala: null, 
                ekstremitas_atas: null,
                ekstremitas_bawah: null
            },
            
            // Section Tab Serah Terima Post-Operasi
            post_op_handover_form: {
                patient_status: '',
                rontgen_photo: '',
                post_instruction: '',
                drain_installed: '',
                tampon: '',
                infusion: '',
                tranfusion: '',
                install_ngt: '',
                ett_label: '',
                catheter: '',
                others: '',
                notes: '',
                drain_install_notes: '',
                infusion_install_notes: '',
                infusion_notes: '',
                ngt_install_notes: '',
                other_notes: '',
            },
            isLoadingPostOp: false,
            rr_action_form: {
                instruction: '',
                time_in: '',
                time_out: '',
            },
            isLoadingRR: false,
            // Section tab 7: Laporan Operasi
            form7_eval: {
                post_icd10:[], 
                post_non_icd10:[],
                // post_icd10_notes: '',
                pre_icd10:[], 
                // pra_icd10_notes: '', 
                pre_non_icd10:[], 

                operating_room_id: '',
                therapy_plan: '',
                start_time:'',
                finish_time:'',
                anesthesia_type:'',
                start_anesthesia_time:'',
                finish_anesthesia_time:'',
                special_operation_type_id:'',
                operation_group_type_id:'',
                bleeding:'',
                drain:'',
                surgical_report:'',

                implant_type_id:'',
                implant_code:'',
                complications:'',
                tissues_organs_removed:'',
                samples_sent:'',
                treatment_management_instructions:'',
            },
            form7_eval_temp: {
                complications_type_id:'',
                tissues_organs_removed_type_id:'',
            },
            evalTab7PraIcd10_id: '',
            evalTab7PostIcd10_id: '',
            OptionsTab7EvalPraIcd10:[],
            OptionsTab7EvalPostIcd10:[],
            input_pra_other10_tab7: '',
            input_post_other10_tab7: '',

            doctor_operator:'', 
            assistant_doctor:'', 
            instrument_nurse:'', 
            anesthesiologist:'', 
        }
    },
    computed: {
        ...mapState({ 
            TypeRoomOptions: state => state.helper.options.type_room,
            OptionsRoom: state => state.room.options.room,
            OptionsBed: state => state.room.options.bed,
            OptionsPharmacyDepo: state => state.depo.options.depo,
            OptionsHealthFacilityItem: state => state.healthfacilityitem.options.healthfacilityitem,
            OptionsSoapPlanDoctor: state => state.soap.options.plandoctor,
            CatalogOptions: state => state.catalog.options.catalog,
            prescriptionitems: state => state.soap.prescription.items, 
            PackageOptions: state => state.packages.options.packages
         
        }),
        anesthesiologist_list() {
            const anesthesiologists = this.doctor_list.filter(item => {
                const cleanName = item?.title?.replace(/\s+/g, ' ').replace(/\s*,\s*/g, ',').trim(); 
                if(cleanName){
                    const parts = cleanName.split(',Sp.');
                    if (parts.length < 2) return false;

                    const specialization = parts[1].trim().toLowerCase();
                    return specialization.includes('an');
                }
            });

            return anesthesiologists
        },
        // skor akhir = skor dari submit TERAKHIR (elemen terakhir di history), bukan slot terbesar
        bromage_final_score() {
            if (!this.form6_bromage.history.length) return null
            return this.form6_bromage.history[this.form6_bromage.history.length - 1].score
        },
        bromage_can_transfer() {
            return this.bromage_final_score !== null && this.bromage_final_score < 2
        },
        // dihitung dari history, TIDAK disimpan sebagai state terpisah -> tidak mungkin desync
        bromageSubmittedSlotKeys() {
            return this.form6_bromage.history.map(h => h.monitoring_label)
        },
        // final_score = akumulasi skor terakhir tiap kategori (dari history), bukan dihitung ulang manual di FE
        // total skor per slot = jumlah skor terpilih dari tiap kategori di slot tsb
        padssSlotTotal() {
            const cat = this.form6_padss.category_score
            const catKeys = Object.keys(cat)
            const totals = {}
            this.padssTimeSlots.forEach(slot => {
                const values = catKeys.map(k => cat[k][slot.key])
                const allFilled = values.every(v => v !== null && v !== undefined)
                totals[slot.key] = allFilled ? values.reduce((sum, v) => sum + v, 0) : null
            })
            return totals
        },
        // skor akhir = total dari submit TERAKHIR yang bikin sebuah slot lengkap (5 kategori terisi)
        padss_final_score() {
            if (!this.form6_padss.history.length) return null
            const last = this.form6_padss.history[this.form6_padss.history.length - 1]
            return last.slot_total !== undefined ? last.slot_total : null
        },
        padss_can_transfer() {
            return this.padss_final_score !== null && this.padss_final_score >= 9
        },
        aldreteSlotTotal() {
            const cat = this.form6_aldrete.category_score
            const keys = Object.keys(cat)

            const totals = {}

            this.padssTimeSlots.forEach(slot => {
                const values = keys.map(k => cat[k][slot.key])

                totals[slot.key] =
                    values.every(v => v !== null && v !== undefined)
                        ? values.reduce((a,b)=>a+b,0)
                        : null
            })

            return totals
        },
        aldrete_final_score() {
            if (!this.form6_aldrete.history.length)
                return null

            const last = this.form6_aldrete.history[this.form6_aldrete.history.length-1]

            return last.slot_total ?? null
        },
        aldrete_can_transfer() {
            return this.aldrete_final_score !== null && this.aldrete_final_score >= 8
        },
        stewardSlotTotal() {
            const cat = this.form6_steward.category_score
            const keys = Object.keys(cat)
            const totals = {}
            this.padssTimeSlots.forEach(slot => {
                const values = keys.map(k => cat[k][slot.key])
                totals[slot.key] =  values.every(v => v !== null && v !== undefined) ? values.reduce((a,b) => a + b, 0) : null
            })
            return totals
        },
        steward_final_score() {
            if (!this.form6_steward.history.length) return null
            const last = this.form6_steward.history[this.form6_steward.history.length - 1]
            return last.slot_total ?? null
        },
        steward_can_transfer() {
            return this.steward_final_score !== null && this.steward_final_score >= 5
        },
    },
    watch : {
        // FORM 3 - PRA OPERATIF
        "form3_pre.medical_history_arry"(newValues) {
            if (newValues && !newValues.includes("d")) {
                this.form3_pre.medical_history = ""
            }
        },
        "form3_pre.allergy_type_id"(newValue) {
            if (newValue != "3") {
                this.form3_pre.allergy = ""
            }
        },
        
        // FORM 3 - INTRA OPERATIF
        "form3_intra.timeout_type_id"(newValue) {
            if (newValue != "1") {
                this.form3_intra.timeout = ""
            }
        },
        "form3_intra.instrument_type_id"(newValue) {
            if (newValue != "1") {
                this.form3_intra.instrument = ""
            }
        },
        "form3_intra.prosthesis_implant_type_id"(newValue) {
            if (newValue != "1") {
                this.form3_intra.prosthesis_implant = ""
            }
        },
        "form3_intra.level_of_awareness_type_id"(newValue) {
            if (newValue != "1") {
                // this.form3_intra.level_of_awareness_m = ""
                // this.form3_intra.level_of_awareness_v = ""
            } else if (newValue != "2") {
                // this.form3_intra.level_of_awareness_e = ""
                // this.form3_intra.level_of_awareness_v = ""
            } else if (newValue != "3") {
                // this.form3_intra.level_of_awareness_e = ""
                // this.form3_intra.level_of_awareness_m = ""
            }
        },
        "form3_intra.cannula_position_type_id"(newValue) {
            if (newValue != "5") {
                this.form3_intra.cannula_position = ""
            }
        },
        "form3_intra.operation_position_type_id"(newValue) {
            if (newValue != "5") {
                this.form3_intra.operation_position = ""
            }
        },
        "form3_intra.arm_position_type_id"(newValue) {
            if (newValue != "3") {
                this.form3_intra.arm_position = ""
            }
        },
        "form3_intra.aids_position_type_id"(newValues) {
            if (newValues && !newValues.includes("b")) {
                this.form3_intra.aids_position = ""
            }
        },
        "form3_intra.skin_preparation_type_id"(newValue) {
            if (newValue != "4") {
                this.form3_intra.skin_preparation = ""
            }
        },
        "form3_intra.dispersive_electrode_type_id"(newValue) {
            if (newValue != "3") {
                this.form3_intra.dispersive_electrode = ""
            }
        },
        "form3_intra.skin_condition_before_type_id"(newValue) {
            if (newValue != "3") {
                this.form3_intra.skin_condition_before = ""
            }
        },
        "form3_intra.skin_condition_after_type_id"(newValue) {
            if (newValue != "3") {
                this.form3_intra.skin_condition_after = ""
            }
        },
        "form3_intra.heating_cooling_unit_type_id"(newValue) {
            if (newValue != "1") {
                this.form3_intra.temperature = ""
                this.form3_intra.temperature_start_time = ""
                this.form3_intra.temperature_finish_time = ""
            }
        },
        "form3_intra.use_of_implants_type_id"(newValue) {
            if (newValue != "1") {
                this.form3_intra.implants_expired_date = ""
                this.form3_intra.implants_factory = ""
                this.form3_intra.implants_type = ""
                this.form3_intra.implants_size = ""
                this.form3_intra.implants_no = ""
            }
        },
        "form3_intra.wound_irrigation_arry"(newValues) {
            if (newValues && !newValues.includes("e")) {
                this.form3_intra.wound_irrigation = ""
            }
        },
        "form3_intra.fluid_usage_arry"(newValues) {
            if (newValues && !newValues.includes("a")) {
                this.form3_intra.fluid_usage_glycine = ""
            }
            if (newValues && !newValues.includes("b")) {
                this.form3_intra.fluid_usage_sterile_water = ""
            }
            if (newValues && !newValues.includes("c")) {
                this.form3_intra.fluid_usage_water_irrigation = ""
            }
            if (newValues && !newValues.includes("d")) {
                this.form3_intra.fluid_usage_sodium_chloride = ""
            }
            if (newValues && !newValues.includes("e")) {
                this.form3_intra.fluid_usage_other = ""
            }
        },
        "form3_intra.tools_wrapped_type_id"(newValue) {
            if (newValue != "1") {
                this.form3_intra.tools_wrapped_type = ""
                this.form3_intra.tools_wrapped_other = ""
            }
        },
        "form3_intra.wrapping_type_id"(newValue) {
            if (newValue != "1") {
                this.form3_intra.wrapping_type = ""
            }
        },
        "form3_intra.specimen_type_id"(newValue) {
            if (newValue != "1") {
                this.form3_intra.specimen_cytology = ""
                this.form3_intra.specimen_culture = ""
                this.form3_intra.specimen_other = ""
                this.form3_intra.specimen_other_type = ""
            } else if (newValue != "2") {
                this.form3_intra.specimen_histology = ""
                this.form3_intra.specimen_culture = ""
                this.form3_intra.specimen_other = ""
                this.form3_intra.specimen_other_type = ""
            } else if (newValue != "4") {
                this.form3_intra.specimen_histology = ""
                this.form3_intra.specimen_cytology = ""
                this.form3_intra.specimen_other = ""
                this.form3_intra.specimen_other_type = ""
            } else if (newValue != "5") {
                this.form3_intra.specimen_histology = ""
                this.form3_intra.specimen_cytology = ""
                this.form3_intra.specimen_culture = ""
            }
        },

        // FORM 3 - PASCA OPERATIF
        "form3_post.gcs_type_id"(newValue) {
            if (newValue != "1") {
                this.form3_post.gcs_m = ""
                this.form3_post.gcs_v = ""
            } else if (newValue != "2") {
                this.form3_post.gcs_e = ""
                this.form3_post.gcs_v = ""
            } else if (newValue != "3") {
                this.form3_post.gcs_e = ""
                this.form3_post.gcs_m = ""
            }
        },
        "form3_post.airway_type_id"(newValue) {
            if (newValue != "1") {
                this.form3_post.airway_problem = ""
            }
        },
        "form3_post.oxygen_therapy_type_id"(newValue) {
            if (newValue != "3") {
                this.form3_post.oxygen_therapy_other = ""
            }
        },
        "form3_post.skin_arrive_type_id"(newValue) {
            if (newValue != "4") {
                this.form3_post.skin_arrive_other = ""
            }
        },
        "form3_post.skin_exit_type_id"(newValue) {
            if (newValue != "3") {
                this.form3_post.skin_exit_other = ""
            }
        },
        "form3_post.position_type_id"(newValue) {
            if (newValue != "3") {
                this.form3_post.position_other = ""
            }
        },

        // FORM 4 - SIGN IN
        "form4_signin.allergy_history_type_id"(newValue) {
            if (newValue !== "1") {
                this.form4_signin.allergy_history_name = ""
            }
        },
        "form4_signin.special_equipment_type_id"(newValue) {
            if (newValue !== "1") {
                this.form4_signin.special_equipment_name = ""
            }
        },
        "form4_signin.is_fasting"(newValue) {
            if (newValue !== "1") {
                this.form4_signin.fasting_hours = ""
            }
        },
        "form4_signin.blood_loss_type_id"(newValue) {
            if (newValue !== "1") {
                this.form4_signin.access_iv_no = ""
                this.form4_signin.blood_preparation = ""
                this.form4_signin.blood_type = ""
            }
        },

        // FORM 4 - SIGN OUT
        "form4_signout.tool_damaged_type_id"(newValue) {
            if (newValue !== "1") {
                this.form4_signout.tool_damaged = ""
            }
        },


        // "form4_signin.is_fasting"(newValues) {
        //     if (newValues && !newValues.includes("1")) {
        //         this.form4_signin.fasting_hours = ""
        //     }
        // },

        // FORM 7 - EVAL
        "form7_eval.implant_type_id"(newValue) {
            if (newValue != "1") {
                this.form7_eval.implant_code = ""
            }
        },
        "form7_eval_temp.complications_type_id"(newValue) {
            if (newValue != "1") {
                this.form7_eval.complications = ""
            }
        },
        "form7_eval_temp.tissues_organs_removed_type_id"(newValue) {
            if (newValue != "1") {
                this.form7_eval.tissues_organs_removed = ""
            }
        },
    },
    methods: {
        onSubmit () {

        },
        // GENERAL
        refresh () {
            router.go(router.currentRoute)
        },
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                employee: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },
        parsed_age(input_date) {
            const birthDate = moment(input_date, 'YYYY-MM-DD');
            const currentDate = moment();

            const years = currentDate.diff(birthDate, 'years');
            birthDate.add(years, 'years');
            const months = currentDate.diff(birthDate, 'months');
            birthDate.add(months, 'months');
            const days = currentDate.diff(birthDate, 'days');

            return `${years} th ${months} bln ${days} hr`;
        },
        localDate(input_date) {
            return moment(input_date).locale("id").format('DD MMMM YYYY')
        },
        async getData (id) {
			this.$store.dispatch('registration/getById', id).then(data => {
                this.regData = data[0]

                this.form_transaction.norm = data[0].norm
                this.form_transaction.registration_destination_id = id
                this.form_transaction.doctor_in_charge_id = data[0].doctor_in_charge_id
                this.form_transaction.health_facility_item_id = data[0].health_facility_item_id
            })

            this.$store.dispatch('employee/getDoctorOptions', '').then((response) => {
                this.doctor_list = response
            })

            this.$store.dispatch('employee/getNurseList').then((response) => {
                this.nurse_list = response
            })

            await this.$store.dispatch('operatie/getDetailOperativeReportById', { ok_id: this.ok_id })
            .then(async (response) => {
                console.log('getDetailOperativeReportById result: ', response);
                // untuk laporan operasi autofill dokter/perawat
                this.fillOperatorInReportOK()

                if(Array.isArray(response) && response.length > 0){
                    this.operativeReportData = response[0]
                    this.fillEvalForm7(this.operativeReportData)
                } else {
                    this.fillEvalForm7(this.operatingData)
                }
            })          
        },

        async getOperatingDetail(ok_id) {
            try {
                await this.$store.dispatch('operatie/getDetailById', { ok_id: ok_id } ).then(data => {
                    if(data?.length > 0) {
                        this.operatingData = data[0]
                        // console.log('this.operatingData: ', this.operatingData);                
                        this.getData(this.operatingData?.registration_destination_id)
                        this.reg_des_id = this.operatingData?.registration_destination_id
                        
                        const res_data_pre_op_handover = data[0].pre_op_handover;
                        const res_data_preoperative_nursing = data[0].preoperative_nursing;
                        const res_data_intraoperative_nursing = data[0].dt_intraoperative_nursing;
                        const res_data_postoperative_nursing = data[0].postoperative_nursing;
                        const res_data_operational_safety_sign_in = data[0].operational_safety_sign_in;
                        const res_data_operational_safety_sign_out = data[0].operational_safety_sign_out;
                        const res_data_operational_safety_time_out = data[0].operational_safety_time_out;
                        const res_data_preanesthesia_evaluation = data[0].preanesthesia_evaluation;
                        const res_data_anesthesia_note = data[0].anesthesia_note;
                        const res_data_monitored_anesthesia = data[0].monitored_anesthesia;
                        const res_data_bromage_score = data[0].bromage_score;
                        const res_data_padss_score = data[0].padss_score;
                        const res_data_aldrete_score = data[0].aldrete_score;
                        const res_data_steward_score = data[0].steward_score;
                        const res_data_rr_action = data[0].rr_action;
                        const res_data_post_op_handover = data[0].post_op_handover;
                        Object.keys(this.pre_op_handover_form).forEach(key => {
                            if (res_data_pre_op_handover[key] !== undefined && res_data_pre_op_handover[key] !== null) {
                                this.$set(this.pre_op_handover_form, key, res_data_pre_op_handover[key]);
                            }
                            if (this.pre_op_handover_form.muskuloskeletal_laki) {
                                this.editable.musLaki = true
                            }
                            if (this.pre_op_handover_form.muskuloskeletal_pr) {
                                this.editable.musPr = true
                            }
                            if (this.pre_op_handover_form.kepala) {
                                this.editable.kepala = true
                            }
                            if (this.pre_op_handover_form.ekstremitas_atas) {
                                this.editable.ekstrimAtas = true
                            }
                            if (this.pre_op_handover_form.ekstremitas_bawah) {
                                this.editable.ekstrimBawah = true
                            }
                        });
                        Object.keys(this.form3_pre).forEach(key => {
                            if (res_data_preoperative_nursing[key] !== undefined && res_data_preoperative_nursing[key] !== null) {
                            let value = res_data_preoperative_nursing[key]

                            // FIX: gunakan safeParseJSON, bukan JSON.parse() langsung,
                            // supaya kalau data lama sudah kadung double-encoded, tetap bisa dipulihkan.
                            if (key === 'patient_verification' || key === 'physical_preparation') {
                                value = this.safeParseJSON(value, this.form3_pre[key]) // fallback ke struktur default kalau gagal total
                            }
                            if (key === 'medical_history_arry') {
                                value = this.safeParseJSON(value, [])
                            }

                            this.$set(this.form3_pre, key, value)
                            }
                        })
                        // Mapping balik response BE ke form3_intra,
                        // dengan parse ulang field yang di-stringify
                        Object.keys(this.form3_intra).forEach(key => {
                            if (res_data_intraoperative_nursing[key] !== undefined && res_data_intraoperative_nursing[key] !== null) {
                                let value = res_data_intraoperative_nursing[key]

                                // Field array (checkbox-group) → parse balik ke array
                                if (
                                    key === 'aids_position_type_id' ||
                                    key === 'wound_irrigation_arry' ||
                                    key === 'fluid_usage_arry'
                                ) {
                                    value = this.safeParseJSON(value, [])
                                }

                                // Field array of objects (drain_usage) → parse balik ke array
                                if (key === 'drain_usage') {
                                    value = this.safeParseJSON(value, [])
                                }

                                // Field nested object (tourniquet) → parse balik ke object
                                if (key === 'tourniquet') {
                                    value = this.safeParseJSON(value, this.form3_intra.tourniquet)
                                }

                                this.$set(this.form3_intra, key, value)
                            }
                        })
                        // Mapping balik response BE ke form3_post0
                        // Tidak ada field yang perlu di-parse karena semua primitive
                        Object.keys(this.form3_post).forEach(key => {
                            if (res_data_postoperative_nursing[key] !== undefined && res_data_postoperative_nursing[key] !== null) {
                                this.$set(this.form3_post, key, res_data_postoperative_nursing[key])
                            }
                        })
                        Object.keys(this.form4_signin).forEach(key => {
                            if (res_data_operational_safety_sign_in[key] !== undefined && res_data_operational_safety_sign_in[key] !== null) {
                                let value = res_data_operational_safety_sign_in[key]

                                if (key === 'confirmation_arry' || key === 'supporting_results_arry') {
                                    value = this.safeParseJSON(value, [])
                                }

                                this.$set(this.form4_signin, key, value)
                            }
                        })
                        Object.keys(this.form4_timeout).forEach(key => {
                            if (res_data_operational_safety_time_out[key] !== undefined && res_data_operational_safety_time_out[key] !== null) {
                                let value = res_data_operational_safety_time_out[key]

                                if (key === 'reading_verbally_arry') {
                                    value = this.safeParseJSON(value, [])
                                }

                                this.$set(this.form4_timeout, key, value)
                            }
                        })
                        Object.keys(this.form4_signout).forEach(key => {
                            if (res_data_operational_safety_sign_out[key] !== undefined && res_data_operational_safety_sign_out[key] !== null) {
                                this.$set(this.form4_signout, key, res_data_operational_safety_sign_out[key])
                            }
                        })
                        const arrayFieldsform5_eval = [
                            'respiratory_path_type_id',
                            'respiration_arry',
                            'cardiovascular_arry',
                            'digestive_system_arry',
                            'neuromusculoskeletal_arry',
                            'kidney_arry',
                            'other_arry',
                            'anesthesia_plan_arry',
                            'icd10',
                            'non_icd10',
                            'icd9cm',
                            'premedication'
                        ]
                        Object.keys(this.form5_eval).forEach(key => {
                            if (res_data_preanesthesia_evaluation[key] !== undefined && res_data_preanesthesia_evaluation[key] !== null) {
                                let value = res_data_preanesthesia_evaluation[key]
                                if (arrayFieldsform5_eval.includes(key)) {
                                    value = this.safeParseJSON(value, [])
                                }
                                this.$set(this.form5_eval, key, value)
                            }
                        })
                        const arrayFieldsform5_notes = [
                            'respiration_arry', 'cardiovascular_arry', 'hepato_arry',
                            'neuro_arry', 'renal_endocrine_arry', 'other_arry',
                            'anesthesia_plan_arry', 'premedication'
                        ]
                        Object.keys(this.form5_notes).forEach(key => {
                            if (res_data_anesthesia_note[key] !== undefined && res_data_anesthesia_note[key] !== null) {
                                let value = res_data_anesthesia_note[key]
                                if (arrayFieldsform5_notes.includes(key)) {
                                    value = this.safeParseJSON(value, [])
                                }
                                this.$set(this.form5_notes, key, value)
                            }
                        })
                        Object.keys(this.form5_monitor).forEach(key => {
                            if (res_data_monitored_anesthesia[key] !== undefined && res_data_monitored_anesthesia[key] !== null) {
                                let value = res_data_monitored_anesthesia[key]
                                if (key === 'physiological_status_monitoring') {
                                    value = this.safeParseJSON(value, [])
                                }
                                this.$set(this.form5_monitor, key, value)
                            }
                        })
                        Object.keys(this.form6_bromage).forEach(key => {
                            if (res_data_bromage_score[key] !== undefined && res_data_bromage_score[key] !== null) {
                                this.$set(this.form6_bromage, key, res_data_bromage_score[key])
                            }
                        })
                        this.applyBromageResponse(res_data_bromage_score)
                        this.applyPadssResponse(res_data_padss_score)
                        this.applyAldreteResponse(res_data_aldrete_score)
                        this.applyStewardResponse(res_data_steward_score)
                        Object.keys(this.rr_action_form).forEach(key => {
                            if (res_data_rr_action[key] !== undefined && res_data_rr_action[key] !== null) {
                                this.$set(this.rr_action_form, key, res_data_rr_action[key])
                            }
                        })
                        Object.keys(this.post_op_handover_form).forEach(key => {
                            if (res_data_post_op_handover[key] !== undefined && res_data_post_op_handover[key] !== null) {
                                this.$set(this.post_op_handover_form, key, res_data_post_op_handover[key])
                            }
                        })
                    }

                })
            } catch (error) {
                console.log('Error while GET DATA at: ', error);                
            }
        },

        fillOperatorInReportOK() {
            this.resetOperatorInReportOK()
            if (this.operator_list && Array.isArray(this.operator_list)) {
                if(this.operator_list.length > 0){
                    this.operator_list.forEach(item => { 
                        if(item.operator_role_type_id == 1){
                            this.doctor_operator = item.operator_id
                        }
                        if(item.operator_role_type_id == 2){
                            this.assistant_doctor = item.operator_id
                        }
                        if(item.operator_role_type_id == 3){
                            this.instrument_nurse = item.operator_id
                        }
                        if(item.operator_role_type_id == 5){
                            this.anesthesiologist = item.operator_id
                        }
                        if(item.operator_role_type_id == 6){
                            this.anesthesiologist = item.operator_id
                        }
                        if(item.operator_role_type_id == 7){
                            this.anesthesiologist = item.operator_id
                        }
                        if(item.operator_role_type_id == 8){
                            this.anesthesiologist = item.operator_id
                        }
                    })
                }
            }
        },

        resetOperatorInReportOK(){
            this.doctor_operator = ''
            this.assistant_doctor = ''
            this.instrument_nurse = ''
            this.anesthesiologist = ''
        },
        
        goToTab(tab) {
            this.active.current_tab = tab;
            console.log(this.active.current_tab);
        },
        
        // Tab 1: DETAIL OPERASI
        openModalOperator() {
            this.active.modal_operator = true
            this.form_operator.operators = []
            this.resetFormOperator()
        },
        resetFormOperator() {
            this.input_operator.employee = ''
            this.input_operator.role = ''
        },
        onAddOperator () {
            if (this.input_operator.employee && this.input_operator.role) {
                let temp = {
                    operator_id: this.input_operator.employee.value,
                    operator_name: this.input_operator.employee.title,
                    operator_role_type_id: this.input_operator.role.value,
                    operator_role_type_name: this.input_operator.role.title,
                }
                
                if ((temp.operator_role_type_id == '1' && this.operator_list.some(item => item.operator_role_type_id == '1')) || (temp.operator_role_type_id == '1' && this.form_operator.operators.some(item => item.operator_role_type_id == '1'))) {
                    this.notification('Warning','CloseIcon','warning','Tidak dapat menambahkan peran OPERATOR lebih dari satu')
                } else {
                    if ((this.operator_list.some(item => item.operator_id === temp.operator_id)) || (this.form_operator.operators.some(item => item.operator_id === temp.operator_id))) {
                        this.notification('Warning','CloseIcon','warning','Tidak dapat menambahkan petugas yang sama')
                    } else {
                        this.form_operator.operators.push(temp)
                        this.resetFormOperator()
                    }
                }
                
            } else {
                this.notification('Warning','CloseIcon','warning','Operator dan Peran wajib terisi')
            }
        },
        removeOperator (index) {
            this.form_operator.operators.splice(index, 1)
        },
        validationOperator() {
            this.form_operator.operating_room_id = this.ok_id

            if (this.form_operator && this.form_operator.operators.length > 0) {
                try {
                    this.disabled.operator = true
                    this.$store.dispatch('operatie/postAddOperator', this.form_operator).then((response) => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active.modal_operator = false
                        this.getOperatorList()
                        this.resetFormOperator()    
                        this.form_operator.operators = []
                        this.disabled.operator = false
                    })
    
                } catch (error) {
                    this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                    this.disabled.operator = false
                    console.log('Error at: ', error);
                }
            } else {
                this.notification('Warning','CloseIcon','warning','Tambahkan pertugas dan peran terlebih dahulu')
            }
        },
        async getOperatorList(){
            try {
                await this.$store.dispatch('operatie/getOperatorByOkId', { ok_id: this.ok_id }).then(response => {
                    this.operator_list = response
                    // console.log('this.operator_list', this.operator_list);
                    this.fillOperatorInReportOK()
                })
            } catch (error) {
                console.log('Error while GET DATA at: ', error);
            }
        },
        deleteOperator(obj){
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus ${obj.operator_name} ?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'primary', okTitle: 'Yes', cancelTitle: 'No', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    try {
                        this.$store.dispatch('operatie/delRemoveOperator', obj.id) .then(response => {
                            if (response.status === 200 || response.status === 201) {
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.getOperatorList()
                            } else {
                                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                            }
                        }) 
                        
                    } catch (error) {
                        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                        console.log('Error at: ', error);
                    }
                }
            })
        },
        deletePRES (index) {
            this.form_transaction.form_prescription.splice(index, 1)
        },
        onAddPRES (event) {
            this.prescription_type = event
            let index = this.form_transaction.form_prescription_parent.findIndex((item) => item.id === event.value);

            if (index === -1) {
                this.form_transaction.form_prescription_parent.push({
                    id:event.value,
                    prescription:event.title,
                    used:'',
                    value:0
                }) 
            } else {
                console.log("Object already exists");
            }
        },
        onAddCAT (event) {
            this.catalog = ''
            this.collation = ''
            if (this.prescription === '') {
                alert("Pilih Racikan!");
            } else {
                this.form_transaction.form_prescription.push(
                    { 
                        code:event.data_catalog.code,
                        name:event.data_catalog.name,
                        pharmacy_catalog_id:event.data_catalog.id, 
                        package_id:event.data_catalog.package_id,
                        dose:event.data_catalog.dose,
                        sum_dose:event.data_catalog.dose+' '+event.data_catalog.dose_name, 
                        package_name:event.data_catalog.package_name,
                        prescription_type_id:this.prescription_type.value,
                        prescription_type_name:this.prescription_type.title,
                        value_doctor:0,
                        value_package:0,
                        used:'',
                        desc:'',
                        sort:this.form_transaction.form_prescription.length, 
                    }
                )
            }
        },
        openPrescription (data) {
            this.is_submit=true
            console.log(data)
            this.form_transaction.id = data.id
            this.form_transaction.norm = data.norm
            let prescription_type = {value:1,title:'OBAT SATUAN'}
            this.prescription = prescription_type
            this.prescription_type = prescription_type
            this.form_transaction.form_prescription_parent.push({
                id:1,
                prescription:'BHP SATUAN',
                used:'',
                value:0
            }) 
            for (let index = 0; index < data.form_prescription.length; index++) {
                this.form_transaction.form_prescription.push(
                    { 
                        code:data.form_prescription[index].pharmacy_catalog_code,
                        name:data.form_prescription[index].pharmacy_catalog_name,
                        pharmacy_catalog_id:data.form_prescription[index].id, 
                        package_id:data.form_prescription[index].package_id,
                        dose:data.form_prescription[index].dose,
                        sum_dose:data.form_prescription[index].dose+' '+data.form_prescription[index].dose_name, 
                        package_name:data.form_prescription[index].package_name,
                        prescription_type_id:this.prescription_type.value,
                        prescription_type_name:this.prescription_type.title,
                        value_doctor:data.form_prescription[index].qty_doctor,
                        value_package:data.form_prescription[index].qty,
                        used:data.form_prescription[index].used,
                        desc:data.form_prescription[index].desc,
                        sort:data.form_prescription[index].sort, 
                    }
                )
            }
            this.form_transaction.health_facility_item_id = data.health_facility_item_id
            this.form_transaction.pharmacy_depo_id = data.pharmacy_depo_id
            this.form_transaction.soap_p_id = data.soap_p_id
            this.form_transaction.view = 'edit'
        },
        validationFormTransaction (id) {
            this.is_submit=true
            const formData = this.form_transaction
            this.$store.dispatch('soap/formPostPutPrescription', formData)
            .then(res => {
                this.is_submit = false
                this.notification('Success','CheckIcon','success','You have successfully')
                this.$store.dispatch('soap/getSoapDoctorById', this.$route.params.id)
                this.$store.dispatch('soap/getSoapPrescriptionById', this.$route.params.id)
                this.form_transaction.id = ''
                this.form_transaction.view = 'add'
                this.form_transaction.type_prescription_id = 3
                this.form_transaction.soap_p_id = ''
                this.form_transaction.registration_destination_id = ''
                this.form_transaction.pharmacy_depo_id = '297c44f0-12ef-40e6-b9a7-c831065b3372'
                this.form_transaction.date_added =  moment(new Date()).format('YYYY-MM-DD')
                this.form_transaction.norm = ''
                this.form_transaction.health_facility_item_id = ''
                this.form_transaction.form_prescription_parent = []
                this.form_transaction.form_prescription = []
            }, 
            err => {
                this.is_submit = false
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
            })
        },
        // Tab 3: Keperawatan Operasi
        addDrain(){
            let temp = {
                drain_type: this.input_drain.drain_type,
                drain_class: this.input_drain.drain_class,
                drain_size: this.input_drain.drain_size,
                drain_notes: this.input_drain.drain_notes,
            }

            this.form3_intra.drain_usage.push(temp)

            this.input_drain.drain_type = ''
            this.input_drain.drain_class = ''
            this.input_drain.drain_size = ''
            this.input_drain.drain_notes = ''
        },
        removeDrain (index) {
            this.form3_intra.drain_usage.splice(index, 1)
        },
        // Tab 5: Catatan anestesi
        onAddEval10 (event) {
            this.evalIcd10_id = ''
            if (this.form5_eval.icd10.some(item => item.icd10_id === event.value)) {
                this.notification('Warning','CloseIcon','warning','Tidak dapat menambahkan list ICD10 yang sama')
            } else {
                this.form5_eval.icd10.push({ icd10_id:event.value, title:event.title, value:event.value, action: 'action'})
            }
        },
        deleteEval10 (index) {
            this.form5_eval.icd10.splice(index, 1)
        },
        onSearchEvalIcd10(search, loading) {
            if(search.length >= 3) {
                loading(true);
                this.$store.dispatch('icd10/getParam', search).then(data => {
                    this.OptionsEvalIcd10 = []
                    for (let i = 0; i < data.length; i++) {
                        const option = []
                        for (const key in data[i]) {
                            if (key === 'id') { option['value'] = data[i][key] } else if (key === 'name') { option['title'] = data[i]['code']+' '+data[i][key] } else if (key === 'id') { option['oid'] = data[i][key] }
                        }
                        this.OptionsEvalIcd10.push(Object.assign({}, option))
                    }
                    loading(false)
                })
            }
        },
        addEvalOther10() {
            let input = ''
            if (this.input_other10) {
                input = this.input_other10
                this.form5_eval.non_icd10.push(input)
                this.input_other10 = ''
            }
        },
        removeEvalOther10 (index) {
            this.form5_eval.non_icd10.splice(index, 1)
        },
        onAddEval9cm (event) {
            this.evalIcd9cm_id = ''
            if (this.form5_eval.icd9cm.some(item => item.icd9cm_id === event.value)) {
                this.notification('Warning','CloseIcon','warning','Tidak dapat menambahkan list ICD9CM yang sama')
            } else {
                this.form5_eval.icd9cm.push({ icd9cm_id:event.value, title:event.title, value:event.value, action: 'action'})
            }
        },
        deleteEval9cm (index) {
            this.form5_eval.icd9cm.splice(index, 1)
        },
        onSearchEvalIcd9cm(search, loading) {
            if(search.length >= 2) {
                loading(true);
                this.$store.dispatch('icd9cm/getParam', search).then(data => {
                    this.OptionsEvalIcd9cm = []
                    for (let i = 0; i < data.length; i++) {
                        const option = []
                        for (const key in data[i]) {
                            if (key === 'id') { option['value'] = data[i][key] } else if (key === 'name') { option['title'] = data[i]['code']+' '+data[i][key] } else if (key === 'id') { option['oid'] = data[i][key] }
                        }
                        this.OptionsEvalIcd9cm.push(Object.assign({}, option))
                    }
                    loading(false)
                })
            }
        },
        
        addFisio() {
            let temp = {
                monitoring_time: this.input_fisio.monitoring_time,
                awareness: this.input_fisio.awareness,
                systolic_blood_pressure: this.input_fisio.systolic_blood_pressure,
                dyastolic_blood_pressure: this.input_fisio.dyastolic_blood_pressure,
                pulse_frequency: this.input_fisio.pulse_frequency,
                breath_frequency: this.input_fisio.breath_frequency,
                temperature: this.input_fisio.temperature,
                prescription: this.input_fisio.prescription,
                liquid: this.input_fisio.liquid,
                notes: this.input_fisio.notes,
            }
            this.form5_monitor.physiological_status_monitoring.push(temp)

            this.input_fisio.monitoring_time = null
            this.input_fisio.awareness = ''
            this.input_fisio.systolic_blood_pressure = ''
            this.input_fisio.dyastolic_blood_pressure = ''
            this.input_fisio.pulse_frequency = ''
            this.input_fisio.breath_frequency = ''
            this.input_fisio.temperature = ''
            this.input_fisio.prescription = ''
            this.input_fisio.liquid = ''
            this.input_fisio.notes = ''
        },
        removeFisio (index) {
            this.form5_monitor.physiological_status_monitoring.splice(index, 1)
        },

        addPremedication(){
            let temp = {
                drug: this.input_premedication.drug,
                time: this.input_premedication.time,
            }

            this.form5_notes.premedication.push(temp)
            this.input_premedication.drug = ''
            this.input_premedication.time = null
        },
        removePremedication(index){
            this.form5_notes.premedication.splice(index, 1)
        },

        addPremedicationEval(){
            let temp = {
                drug: this.input_premedication.drug,
                time: this.input_premedication.time,
            }

            this.form5_eval.premedication.push(temp)
            this.input_premedication.drug = ''
            this.input_premedication.time = null
        },
        removePremedicationEval(index){
            this.form5_eval.premedication.splice(index, 1)
        },
        // Tab 7: Laporan Operasi
        deleteTab7PraEval10(index) {
            this.form7_eval.pre_icd10.splice(index, 1)
        },
        deleteTab7PostEval10(index) {
            this.form7_eval.post_icd10.splice(index, 1)
        },
        onAddTab7PraEval10 (event) {
            this.evalTab7PraIcd10_id = ''
            if (this.form7_eval.pre_icd10.some(item => item.icd10_id === event.value)) {
                this.notification('Warning','CloseIcon','warning','Tidak dapat menambahkan list Pra ICD10 yang sama')
            } else {
                this.form7_eval.pre_icd10.push({ icd10_id:event.value, title:event.title, value:event.value, action: 'action'})
            }
        },
        onAddTab7PostEval10 (event) {
            this.evalTab7PostIcd10_id = ''
            if (this.form7_eval.post_icd10.some(item => item.icd10_id === event.value)) {
                this.notification('Warning','CloseIcon','warning','Tidak dapat menambahkan list Post ICD10 yang sama')
            } else {
                this.form7_eval.post_icd10.push({ icd10_id:event.value, title:event.title, value:event.value, action: 'action'})
            }
        },
        onSearchTab7EvalPraIcd10(search, loading) {
            if(search.length >= 3) {
                loading(true);
                this.$store.dispatch('icd10/getParam', search).then(data => {
                    this.OptionsTab7EvalPraIcd10 = []
                    for (let i = 0; i < data.length; i++) {
                        const option = []
                        for (const key in data[i]) {
                            if (key === 'id') { option['value'] = data[i][key] } else if (key === 'name') { option['title'] = data[i]['code']+' '+data[i][key] } else if (key === 'id') { option['oid'] = data[i][key] }
                        }
                        this.OptionsTab7EvalPraIcd10.push(Object.assign({}, option))
                    }
                    loading(false)
                })
            }
        },
        onSearchTab7EvalPostIcd10(search, loading) {
            if(search.length >= 3) {
                loading(true);
                this.$store.dispatch('icd10/getParam', search).then(data => {
                    this.OptionsTab7EvalPostIcd10 = []
                    for (let i = 0; i < data.length; i++) {
                        const option = []
                        for (const key in data[i]) {
                            if (key === 'id') { option['value'] = data[i][key] } else if (key === 'name') { option['title'] = data[i]['code']+' '+data[i][key] } else if (key === 'id') { option['oid'] = data[i][key] }
                        }
                        this.OptionsTab7EvalPostIcd10.push(Object.assign({}, option))
                    }
                    loading(false)
                })
            }
        },

        addTab7EvalPraOther10() {
            let input = ''
            if (this.input_pra_other10_tab7) {
                input = this.input_pra_other10_tab7
                this.form7_eval.pre_non_icd10.push(input)
                this.input_pra_other10_tab7 = ''
            }
        },
        addTab7EvalPostOther10() {
            let input = ''
            if (this.input_post_other10_tab7) {
                input = this.input_post_other10_tab7 
                this.form7_eval.post_non_icd10.push(input)
                this.input_post_other10_tab7 = ''
            }
        },
        removeTab7EvalPraOther10(index) {
            this.form7_eval.pre_non_icd10.splice(index, 1)
        },
        removeTab7EvalPostOther10(index) {
            this.form7_eval.post_non_icd10.splice(index, 1)
        },
        async validationOperativeReport() {
            this.form7_eval.operating_room_id = this.ok_id

            // console.log(this.form7_eval, 'thisformeval')
            this.disabled.operative_report = true

            try {
                if(Object.keys(this.operativeReportData).length === 0){
                    await this.$store.dispatch('operatie/postAddOperativeReport', this.form7_eval);
                } else {
                    const formData = {
                        form: this.form7_eval,
                        id: this.operativeReportData.id
                    };
                    await this.$store.dispatch('operatie/postPutOperativeReport', formData);
                }

                this.notification('Success', 'CheckIcon', 'success', 'You have successfully')

                const data = await this.$store.dispatch('operatie/getDetailOperativeReportById', { ok_id: this.ok_id });
                console.log('getDetailOperativeReportById result: ', data);

                if (Array.isArray(data) && data.length > 0) {
                    this.operativeReportData = data[0];
                    this.fillEvalForm7(this.operativeReportData);
                }

            } catch (error) {
                this.notification('Failed', 'CloseIcon', 'danger', 'Operation unsuccessful')
                console.error('Error occurred: ', error);
            } finally {
                this.disabled.operative_report = false;
            }
        },

        // async fetchOperativeReportById(ok_id) {
        //     try {
        //         const data = await this.$store.dispatch('operatie/getDetailOperativeReportById', { ok_id });
        //         return data;
        //     } catch (error) {
        //         console.error('Failed to fetch operative report by ID:', error);
        //         throw error;
        //     }
        // },

        async fillEvalForm7(obj) {
            await this.$nextTick();
            this.resetEvalForm7();
            if (obj) {   
                console.log(obj,'objfillevalform7')
                this.form7_eval.operating_room_id = obj.operating_room_id || this.ok_id

                if (obj?.post_icd10 && Array.isArray(obj?.post_icd10)) {
                    this.form7_eval.post_icd10 = obj.post_icd10.map(item => {
                        return {
                            icd10_id: item.icd10_id ,
                            value: item.id,
                            title: `${item.icd10_code} ${item.icd10_name}`, 
                            action: "action"
                        };
                    });
                }

                if(obj?.post_non_icd10){
                    this.form7_eval.post_non_icd10 = obj.post_non_icd10
                }

                if (obj?.pre_icd10 && Array.isArray(obj?.pre_icd10)) {
                    this.form7_eval.pre_icd10 = obj.pre_icd10.map(item => {
                        return {
                            icd10_id: item.icd10_id ,
                            value: item.id,
                            title: `${item.icd10_code} ${item.icd10_name}`, 
                            action: "action"
                        };
                    });
                }

                if(obj?.pre_non_icd10){
                    this.form7_eval.pre_non_icd10 = obj.pre_non_icd10   
                }

                if(obj?.therapy_plan){
                    this.form7_eval.therapy_plan = obj.therapy_plan
                }

                if(obj?.start_time){
                    this.form7_eval.start_time = obj.start_time
                }

                if(obj?.finish_time){
                    this.form7_eval.finish_time = obj.finish_time
                }

                if(obj?.anesthesia_type){
                    this.form7_eval.anesthesia_type = obj.anesthesia_type
                }

                if(obj?.start_anesthesia_time){
                    this.form7_eval.start_anesthesia_time = obj.start_anesthesia_time
                }

                if(obj?.finish_anesthesia_time){
                    this.form7_eval.finish_anesthesia_time = obj.finish_anesthesia_time
                }

                if(obj?.operation_group_type_id){
                    this.form7_eval.operation_group_type_id = obj.operation_group_type_id
                }

                if(obj?.special_operation_type_id){
                    this.form7_eval.special_operation_type_id = obj.special_operation_type_id
                }

                if(obj?.bleeding){
                    this.form7_eval.bleeding = obj.bleeding
                }

                if(obj?.drain){
                    this.form7_eval.drain = obj.drain
                }

                if(obj?.surgical_report){
                    this.form7_eval.surgical_report = obj.surgical_report
                }

                if(obj?.implant_type_id != undefined){
                    this.form7_eval.implant_type_id = obj.implant_type_id
                }
                if(obj?.implant_code){
                    this.form7_eval.implant_code = obj.implant_code
                }

                if(obj?.complications){
                    this.form7_eval_temp.complications_type_id = 1
                } else {
                    this.form7_eval_temp.complications_type_id = 0
                }
                if(obj?.complications){
                    this.form7_eval.complications = obj.complications
                }

                if(obj?.tissues_organs_removed){
                    this.form7_eval_temp.tissues_organs_removed_type_id = 1
                } else {
                    this.form7_eval_temp.tissues_organs_removed_type_id = 0
                }
                if(obj?.tissues_organs_removed){
                    this.form7_eval.tissues_organs_removed = obj.tissues_organs_removed
                }

                if(obj?.samples_sent != undefined){
                    this.form7_eval.samples_sent = obj.samples_sent
                }

                if(obj?.treatment_management_instructions){
                    this.form7_eval.treatment_management_instructions = obj.treatment_management_instructions
                }
            } 
        },   

        resetEvalForm7(){
            this.form7_eval.post_icd10 = []
            this.form7_eval.post_non_icd10 = []
            this.form7_eval.pre_icd10 = []
            this.form7_eval.pre_non_icd10 = []
            this.form7_eval.operating_room_id = ''
            this.form7_eval.therapy_plan = ''
            this.form7_eval.start_time = ''
            this.form7_eval.finish_time = ''
            this.form7_eval.anesthesia_type = ''
            this.form7_eval.start_anesthesia_time = ''
            this.form7_eval.finish_anesthesia_time = ''
            this.form7_eval.special_operation_type_id = ''
            this.form7_eval.operation_group_type_id = ''
            this.form7_eval.bleeding = ''
            this.form7_eval.drain = ''
            this.form7_eval.surgical_report = ''

            this.form7_eval.implant_type_id = ''
            this.form7_eval.implant_code = ''
            this.form7_eval_temp.complications_type_id = ''
            this.form7_eval.complications = ''
            this.form7_eval_temp.tissues_organs_removed_type_id = ''
            this.form7_eval.tissues_organs_removed = ''
            this.form7_eval.samples_sent = ''
            this.form7_eval.treatment_management_instructions = ''
        },

        
        async submit_pre_op_handover_form() {
            // const requiredFields = {
            //     report_to_operating_doctor: 'Lapor ke Dokter Operasi',
            //     report_to_operating_room: 'Lapor ke Kamar Operasi',
            //     filling_informed_consent: 'Mengisi Informed Consent',
            //     shaving_operating_area: 'Mencukur Daerah Operasi',
            //     install_infusion: 'Pasang Infus',
            //     transfusion: 'Transfusi',
            //     install_catheter: 'Pasang Catheter',
            //     install_ngt: 'Pasang NGT',
            //     clisma: 'Clisma',
            //     ecg: 'ECG',
            //     usg: 'USG',
            //     remove_cosmetics: 'Menghapus Kosmetik dan Cat Kuku',
            //     remove_softlens: 'Melepas Soflens / Perhiasan / Gigi Palsu',
            //     check_bracelet: 'Cek Gelang Pasien',
            //     fasting: 'Puasa',
            //     prophilaxis: 'Profilaksis',
            //     premedication: 'Premedikasi',
            //     others: 'Lain-lain'
            // };

            // for (const key in requiredFields) {
            //     if (!this.pre_op_handover_form[key]) {
            //         this.$bvToast.toast(`${requiredFields[key]} wajib diisi.`, {
            //             title: 'Validasi',
            //             variant: 'danger',
            //             solid: true
            //         });
            //         return;
            //     }
            // }


            try {
                this.isLoading = true;

                await this.$store.dispatch('operatie/post_pre_op_handover_form', {form:this.pre_op_handover_form,id:this.ok_id})
                .then(
                    async res => {
                       this.$bvToast.toast('Data berhasil disimpan.', {
                            title: 'Berhasil',
                            variant: 'success',
                            solid: true
                        });
                        const res_data = res.data.data;
                        Object.keys(this.pre_op_handover_form).forEach(key => {
                            if (res_data[key] !== undefined && res_data[key] !== null) {
                                this.$set(this.pre_op_handover_form, key, res_data[key]);
                            }
                            if (this.pre_op_handover_form.muskuloskeletal_laki) {
                                this.editable.musLaki = true
                            }
                            if (this.pre_op_handover_form.muskuloskeletal_pr) {
                                this.editable.musPr = true
                            }
                            if (this.pre_op_handover_form.kepala) {
                                this.editable.kepala = true
                            }
                            if (this.pre_op_handover_form.ekstremitas_atas) {
                                this.editable.ekstrimAtas = true
                            }
                            if (this.pre_op_handover_form.ekstremitas_bawah) {
                                this.editable.ekstrimBawah = true
                            }
                        });
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })

            } catch (error) {

                this.$bvToast.toast(
                    error.response?.data?.message || 'Terjadi kesalahan.',
                    {
                        title: 'Error',
                        variant: 'danger',
                        solid: true
                    }
                );

            } finally {

                this.isLoading = false;
            }
        },
        async saveMusLaki(drawing){
            try{
                this.isLoading = true;
                this.pre_op_handover_form.muskuloskeletal_laki = JSON.stringify(drawing);
                await this.$store.dispatch('operatie/post_pre_op_handover_form',{
                        form:this.pre_op_handover_form,
                        id:this.ok_id
                    }
                );
                this.$bvToast.toast(
                    'Data berhasil disimpan.',
                    {
                        title:'Berhasil',
                        variant:'success',
                        solid:true
                    }
                );
                this.editable.musLaki = true;
            }catch(error){
                this.$bvToast.toast(
                    error.response?.data?.message || 'Terjadi kesalahan.',
                    {
                        title:'Error',
                        variant:'danger',
                        solid:true
                    }
                );
            }finally{
                this.isLoading = false;
            }
        },
        async saveMusPr(drawing){
            try{
                this.isLoading = true;
                this.pre_op_handover_form.muskuloskeletal_pr = JSON.stringify(drawing);
                await this.$store.dispatch('operatie/post_pre_op_handover_form',{
                        form:this.pre_op_handover_form,
                        id:this.ok_id
                    }
                );
                this.$bvToast.toast(
                    'Data berhasil disimpan.',
                    {
                        title:'Berhasil',
                        variant:'success',
                        solid:true
                    }
                );
                this.editable.musPr = true;
            }catch(error){
                this.$bvToast.toast(
                    error.response?.data?.message || 'Terjadi kesalahan.',
                    {
                        title:'Error',
                        variant:'danger',
                        solid:true
                    }
                );
            }finally{
                this.isLoading = false;
            }
        },
        async saveKepala(drawing){
            try{
                this.isLoading = true;
                this.pre_op_handover_form.kepala = JSON.stringify(drawing);
                await this.$store.dispatch('operatie/post_pre_op_handover_form',{
                        form:this.pre_op_handover_form,
                        id:this.ok_id
                    }
                );
                this.$bvToast.toast(
                    'Data berhasil disimpan.',
                    {
                        title:'Berhasil',
                        variant:'success',
                        solid:true
                    }
                );
                this.editable.kepala = true;
            }catch(error){
                this.$bvToast.toast(
                    error.response?.data?.message || 'Terjadi kesalahan.',
                    {
                        title:'Error',
                        variant:'danger',
                        solid:true
                    }
                );
            }finally{
                this.isLoading = false;
            }
        },
        async saveekstrimAtas(drawing){
            try{
                this.isLoading = true;
                this.pre_op_handover_form.ekstremitas_atas = JSON.stringify(drawing);
                await this.$store.dispatch('operatie/post_pre_op_handover_form',{
                        form:this.pre_op_handover_form,
                        id:this.ok_id
                    }
                );
                this.$bvToast.toast(
                    'Data berhasil disimpan.',
                    {
                        title:'Berhasil',
                        variant:'success',
                        solid:true
                    }
                );
                this.editable.ekstrimAtas = true;
            }catch(error){
                this.$bvToast.toast(
                    error.response?.data?.message || 'Terjadi kesalahan.',
                    {
                        title:'Error',
                        variant:'danger',
                        solid:true
                    }
                );
            }finally{
                this.isLoading = false;
            }
        },
        async saveekstrimBawah(drawing){
            try{
                this.isLoading = true;
                this.pre_op_handover_form.ekstremitas_bawah = JSON.stringify(drawing);
                await this.$store.dispatch('operatie/post_pre_op_handover_form',{
                        form:this.pre_op_handover_form,
                        id:this.ok_id
                    }
                );
                this.$bvToast.toast(
                    'Data berhasil disimpan.',
                    {
                        title:'Berhasil',
                        variant:'success',
                        solid:true
                    }
                );
                this.editable.ekstrimBawah = true;
            }catch(error){
                this.$bvToast.toast(
                    error.response?.data?.message || 'Terjadi kesalahan.',
                    {
                        title:'Error',
                        variant:'danger',
                        solid:true
                    }
                );
            }finally{
                this.isLoading = false;
            }
        },
        // Helper terpusat, dipakai di submit maupun load, supaya konsisten
        safeParseJSON(value, fallback) {
            if (value === null || value === undefined || value === '') return fallback
            if (typeof value === 'object') return value // sudah object, jangan diapa-apakan

            let parsed = value
            // FIX: parse berulang selama hasilnya masih string (menangani data lama yang sudah kadung double-encoded)
            let attempts = 0
            while (typeof parsed === 'string' && attempts < 5) {
            try {
                const next = JSON.parse(parsed)
                if (next === parsed) break // tidak berubah lagi, berhenti
                parsed = next
            } catch (e) {
                break // bukan JSON valid, hentikan, pakai apa adanya / fallback
            }
            attempts++
            }

            return typeof parsed === 'object' && parsed !== null ? parsed : fallback
        },

        async submit_preoperative_nursing_form() {
            // Validasi field wajib bisa diaktifkan di sini kalau diperlukan, contoh:
            // const requiredFields = {
            //     ttv_temperature: 'Suhu',
            //     ttv_pulse: 'Nadi',
            //     mental_status_type_id: 'Status Mental'
            // };
            // for (const key in requiredFields) {
            //     if (!this.form3_pre[key]) {
            //         this.$bvToast.toast(`${requiredFields[key]} wajib diisi.`, {
            //             title: 'Validasi',
            //             variant: 'danger',
            //             solid: true
            //         });
            //         return;
            //     }
            // }

            // FIX: patient_verification, physical_preparation, & medical_history_arry
            // disimpan sebagai longtext/varchar di DB → harus di-JSON.stringify()
            // sebelum dikirim ke BE.
            const payload = {
                ...this.form3_pre,
                patient_verification: JSON.stringify(this.form3_pre.patient_verification),
                physical_preparation: JSON.stringify(this.form3_pre.physical_preparation),
                medical_history_arry: JSON.stringify(this.form3_pre.medical_history_arry)
            }

            try {
                this.isLoading = true

                await this.$store
                .dispatch('operatie/post_preoperative_nursing_form', {
                    form: payload,
                    id: this.ok_id
                })
                .then(async res => {
                    this.$bvToast.toast('Data berhasil disimpan.', {
                    title: 'Berhasil',
                    variant: 'success',
                    solid: true
                    })

                    const res_data = res.data.data

                    // FIX: mapping balik response BE ke form3_pre, dengan parse ulang
                    // field yang di-stringify (patient_verification, physical_preparation,
                    // medical_history_arry) supaya tetap berbentuk object/array di FE.

                    Object.keys(this.form3_pre).forEach(key => {
                        if (res_data[key] !== undefined && res_data[key] !== null) {
                        let value = res_data[key]

                        // FIX: gunakan safeParseJSON, bukan JSON.parse() langsung,
                        // supaya kalau data lama sudah kadung double-encoded, tetap bisa dipulihkan.
                        if (key === 'patient_verification' || key === 'physical_preparation') {
                            value = this.safeParseJSON(value, this.form3_pre[key]) // fallback ke struktur default kalau gagal total
                        }
                        if (key === 'medical_history_arry') {
                            value = this.safeParseJSON(value, [])
                        }

                        this.$set(this.form3_pre, key, value)
                        }
                    })
                })
                .catch(({ message, debug, response }) => {
                    this.notification(`Failed ${debug}`, 'CloseIcon', 'danger', `${message}`)
                })
            } catch (error) {
                this.$bvToast.toast(error.response?.data?.message || 'Terjadi kesalahan.', {
                title: 'Error',
                variant: 'danger',
                solid: true
                })
            } finally {
                this.isLoading = false
            }
        },

        async submit_intraoperative_nursing_form() {
            // Field yang perlu di-stringify karena disimpan sebagai longtext/varchar di DB
            // - aids_position_type_id  → array checkbox
            // - drain_usage            → array of objects
            // - wound_irrigation_arry  → array checkbox
            // - fluid_usage_arry       → array checkbox
            // - tourniquet             → object nested

            const payload = {
                ...this.form3_intra,
                aids_position_type_id:  JSON.stringify(this.form3_intra.aids_position_type_id),
                drain_usage:             JSON.stringify(this.form3_intra.drain_usage),
                wound_irrigation_arry:   JSON.stringify(this.form3_intra.wound_irrigation_arry),
                fluid_usage_arry:        JSON.stringify(this.form3_intra.fluid_usage_arry),
                tourniquet:              JSON.stringify(this.form3_intra.tourniquet),
            }

            try {
                this.isLoading = true

                await this.$store
                    .dispatch('operatie/post_intraoperative_nursing_form', {
                        form: payload,
                        id: this.ok_id
                    })
                    .then(async res => {
                        this.$bvToast.toast('Data berhasil disimpan.', {
                            title: 'Berhasil',
                            variant: 'success',
                            solid: true
                        })

                        const res_data = res.data.data

                        // Mapping balik response BE ke form3_intra,
                        // dengan parse ulang field yang di-stringify
                        Object.keys(this.form3_intra).forEach(key => {
                            if (res_data[key] !== undefined && res_data[key] !== null) {
                                let value = res_data[key]

                                // Field array (checkbox-group) → parse balik ke array
                                if (
                                    key === 'aids_position_type_id' ||
                                    key === 'wound_irrigation_arry' ||
                                    key === 'fluid_usage_arry'
                                ) {
                                    value = this.safeParseJSON(value, [])
                                }

                                // Field array of objects (drain_usage) → parse balik ke array
                                if (key === 'drain_usage') {
                                    value = this.safeParseJSON(value, [])
                                }

                                // Field nested object (tourniquet) → parse balik ke object
                                if (key === 'tourniquet') {
                                    value = this.safeParseJSON(value, this.form3_intra.tourniquet)
                                }

                                this.$set(this.form3_intra, key, value)
                            }
                        })
                    })
                    .catch(({ message, debug, response }) => {
                        this.notification(`Failed ${debug}`, 'CloseIcon', 'danger', `${message}`)
                    })

            } catch (error) {
                this.$bvToast.toast(error.response?.data?.message || 'Terjadi kesalahan.', {
                    title: 'Error',
                    variant: 'danger',
                    solid: true
                })
            } finally {
                this.isLoading = false
            }
        },

        async submit_postoperative_nursing_form() {
            // Tidak ada field array/object di form ini,
            // jadi payload langsung spread tanpa stringify
            const payload = { ...this.form3_post }

            try {
                this.isLoading = true

                await this.$store
                    .dispatch('operatie/post_postoperative_nursing_form', {
                        form: payload,
                        id: this.ok_id
                    })
                    .then(async res => {
                        this.$bvToast.toast('Data berhasil disimpan.', {
                            title: 'Berhasil',
                            variant: 'success',
                            solid: true
                        })

                        const res_data = res.data.data

                        // Mapping balik response BE ke form3_post
                        // Tidak ada field yang perlu di-parse karena semua primitive
                        Object.keys(this.form3_post).forEach(key => {
                            if (res_data[key] !== undefined && res_data[key] !== null) {
                                this.$set(this.form3_post, key, res_data[key])
                            }
                        })
                    })
                    .catch(({ message, debug, response }) => {
                        this.notification(`Failed ${debug}`, 'CloseIcon', 'danger', `${message}`)
                    })

            } catch (error) {
                this.$bvToast.toast(error.response?.data?.message || 'Terjadi kesalahan.', {
                    title: 'Error',
                    variant: 'danger',
                    solid: true
                })
            } finally {
                this.isLoading = false
            }
        },

        async submit_signin_form() {
            // Field array → stringify karena disimpan varchar di DB
            const payload = {
                ...this.form4_signin,
                confirmation_arry:      JSON.stringify(this.form4_signin.confirmation_arry),
                supporting_results_arry: JSON.stringify(this.form4_signin.supporting_results_arry),
            }

            try {
                this.isLoading = true

                await this.$store
                    .dispatch('operatie/post_operational_safety_sign_in', {
                        form: payload,
                        id: this.ok_id
                    })
                    .then(async res => {
                        this.$bvToast.toast('Data Sign In berhasil disimpan.', {
                            title: 'Berhasil',
                            variant: 'success',
                            solid: true
                        })

                        const res_data = res.data.data

                        Object.keys(this.form4_signin).forEach(key => {
                            if (res_data[key] !== undefined && res_data[key] !== null) {
                                let value = res_data[key]

                                if (key === 'confirmation_arry' || key === 'supporting_results_arry') {
                                    value = this.safeParseJSON(value, [])
                                }

                                this.$set(this.form4_signin, key, value)
                            }
                        })
                    })
                    .catch(({ message, debug }) => {
                        this.notification(`Failed ${debug}`, 'CloseIcon', 'danger', `${message}`)
                    })

            } catch (error) {
                this.$bvToast.toast(error.response?.data?.message || 'Terjadi kesalahan.', {
                    title: 'Error',
                    variant: 'danger',
                    solid: true
                })
            } finally {
                this.isLoading = false
            }
        },

        async submit_timeout_form() {
            // reading_verbally_arry → array, stringify karena varchar di DB
            const payload = {
                ...this.form4_timeout,
                reading_verbally_arry: JSON.stringify(this.form4_timeout.reading_verbally_arry),
            }

            try {
                this.isLoading = true

                await this.$store
                    .dispatch('operatie/post_operational_safety_time_out', {
                        form: payload,
                        id: this.ok_id
                    })
                    .then(async res => {
                        this.$bvToast.toast('Data Time Out berhasil disimpan.', {
                            title: 'Berhasil',
                            variant: 'success',
                            solid: true
                        })

                        const res_data = res.data.data

                        Object.keys(this.form4_timeout).forEach(key => {
                            if (res_data[key] !== undefined && res_data[key] !== null) {
                                let value = res_data[key]

                                if (key === 'reading_verbally_arry') {
                                    value = this.safeParseJSON(value, [])
                                }

                                this.$set(this.form4_timeout, key, value)
                            }
                        })
                    })
                    .catch(({ message, debug }) => {
                        this.notification(`Failed ${debug}`, 'CloseIcon', 'danger', `${message}`)
                    })

            } catch (error) {
                this.$bvToast.toast(error.response?.data?.message || 'Terjadi kesalahan.', {
                    title: 'Error',
                    variant: 'danger',
                    solid: true
                })
            } finally {
                this.isLoading = false
            }
        },

        async submit_signout_form() {
            // Tidak ada field array/object → langsung spread
            const payload = { ...this.form4_signout }

            try {
                this.isLoading = true

                await this.$store
                    .dispatch('operatie/post_operational_safety_sign_out', {
                        form: payload,
                        id: this.ok_id
                    })
                    .then(async res => {
                        this.$bvToast.toast('Data Sign Out berhasil disimpan.', {
                            title: 'Berhasil',
                            variant: 'success',
                            solid: true
                        })

                        const res_data = res.data.data

                        Object.keys(this.form4_signout).forEach(key => {
                            if (res_data[key] !== undefined && res_data[key] !== null) {
                                this.$set(this.form4_signout, key, res_data[key])
                            }
                        })
                    })
                    .catch(({ message, debug }) => {
                        this.notification(`Failed ${debug}`, 'CloseIcon', 'danger', `${message}`)
                    })

            } catch (error) {
                this.$bvToast.toast(error.response?.data?.message || 'Terjadi kesalahan.', {
                    title: 'Error',
                    variant: 'danger',
                    solid: true
                })
            } finally {
                this.isLoading = false
            }
        },

        async submit_eval_form() {
            // Field array → stringify karena disimpan varchar/longtext di DB
            const payload = {
                ...this.form5_eval,
                respiratory_path_type_id:   JSON.stringify(this.form5_eval.respiratory_path_type_id),
                respiration_arry:           JSON.stringify(this.form5_eval.respiration_arry),
                cardiovascular_arry:        JSON.stringify(this.form5_eval.cardiovascular_arry),
                digestive_system_arry:      JSON.stringify(this.form5_eval.digestive_system_arry),
                neuromusculoskeletal_arry:  JSON.stringify(this.form5_eval.neuromusculoskeletal_arry),
                kidney_arry:                JSON.stringify(this.form5_eval.kidney_arry),
                other_arry:                 JSON.stringify(this.form5_eval.other_arry),
                anesthesia_plan_arry:       JSON.stringify(this.form5_eval.anesthesia_plan_arry),
                icd10:                      JSON.stringify(this.form5_eval.icd10),
                non_icd10:                  JSON.stringify(this.form5_eval.non_icd10),
                icd9cm:                     JSON.stringify(this.form5_eval.icd9cm),
                premedication:              JSON.stringify(this.form5_eval.premedication),
            }

            try {
                this.isLoading = true

                await this.$store
                    .dispatch('operatie/post_preanesthesia_evaluation', {
                        form: payload,
                        id: this.ok_id
                    })
                    .then(async res => {
                        this.$bvToast.toast('Data Evaluasi Pra-Anestesi berhasil disimpan.', {
                            title: 'Berhasil',
                            variant: 'success',
                            solid: true
                        })

                        const res_data = res.data.data

                        const arrayFields = [
                            'respiratory_path_type_id',
                            'respiration_arry',
                            'cardiovascular_arry',
                            'digestive_system_arry',
                            'neuromusculoskeletal_arry',
                            'kidney_arry',
                            'other_arry',
                            'anesthesia_plan_arry',
                            'icd10',
                            'non_icd10',
                            'icd9cm',
                            'premedication'
                        ]

                        Object.keys(this.form5_eval).forEach(key => {
                            if (res_data[key] !== undefined && res_data[key] !== null) {
                                let value = res_data[key]

                                if (arrayFields.includes(key)) {
                                    value = this.safeParseJSON(value, [])
                                }

                                this.$set(this.form5_eval, key, value)
                            }
                        })
                    })
                    .catch(({ message, debug }) => {
                        this.notification(`Failed ${debug}`, 'CloseIcon', 'danger', `${message}`)
                    })

            } catch (error) {
                this.$bvToast.toast(error.response?.data?.message || 'Terjadi kesalahan.', {
                    title: 'Error',
                    variant: 'danger',
                    solid: true
                })
            } finally {
                this.isLoading = false
            }
        },

        async submit_notes_form() {
            const payload = {
                ...this.form5_notes,
                respiration_arry:     JSON.stringify(this.form5_notes.respiration_arry),
                cardiovascular_arry:  JSON.stringify(this.form5_notes.cardiovascular_arry),
                hepato_arry:          JSON.stringify(this.form5_notes.hepato_arry),
                neuro_arry:           JSON.stringify(this.form5_notes.neuro_arry),
                renal_endocrine_arry: JSON.stringify(this.form5_notes.renal_endocrine_arry),
                other_arry:           JSON.stringify(this.form5_notes.other_arry),
                anesthesia_plan_arry: JSON.stringify(this.form5_notes.anesthesia_plan_arry),
                premedication:        JSON.stringify(this.form5_notes.premedication),
            }

            try {
                this.isLoading = true

                await this.$store
                    .dispatch('operatie/post_anesthesia_note', {
                        form: payload,
                        id: this.ok_id
                    })
                    .then(async res => {
                        this.$bvToast.toast('Data Catatan Anestesi berhasil disimpan.', {
                            title: 'Berhasil', variant: 'success', solid: true
                        })

                        const res_data = res.data.data
                        const arrayFields = [
                            'respiration_arry', 'cardiovascular_arry', 'hepato_arry',
                            'neuro_arry', 'renal_endocrine_arry', 'other_arry',
                            'anesthesia_plan_arry', 'premedication'
                        ]

                        Object.keys(this.form5_notes).forEach(key => {
                            if (res_data[key] !== undefined && res_data[key] !== null) {
                                let value = res_data[key]
                                if (arrayFields.includes(key)) {
                                    value = this.safeParseJSON(value, [])
                                }
                                this.$set(this.form5_notes, key, value)
                            }
                        })
                    })
                    .catch(({ message, debug }) => {
                        this.notification(`Failed ${debug}`, 'CloseIcon', 'danger', `${message}`)
                    })

            } catch (error) {
                this.$bvToast.toast(error.response?.data?.message || 'Terjadi kesalahan.', {
                    title: 'Error', variant: 'danger', solid: true
                })
            } finally {
                this.isLoading = false
            }
        },

        async submit_monitor_form() {
            const payload = {
                ...this.form5_monitor,
                physiological_status_monitoring: JSON.stringify(this.form5_monitor.physiological_status_monitoring),
            }

            try {
                this.isLoading = true

                await this.$store
                    .dispatch('operatie/post_monitored_anesthesia', {
                        form: payload,
                        id: this.ok_id
                    })
                    .then(async res => {
                        this.$bvToast.toast('Data Pemantauan berhasil disimpan.', {
                            title: 'Berhasil', variant: 'success', solid: true
                        })

                        const res_data = res.data.data

                        Object.keys(this.form5_monitor).forEach(key => {
                            if (res_data[key] !== undefined && res_data[key] !== null) {
                                let value = res_data[key]
                                if (key === 'physiological_status_monitoring') {
                                    value = this.safeParseJSON(value, [])
                                }
                                this.$set(this.form5_monitor, key, value)
                            }
                        })
                    })
                    .catch(({ message, debug }) => {
                        this.notification(`Failed ${debug}`, 'CloseIcon', 'danger', `${message}`)
                    })

            } catch (error) {
                this.$bvToast.toast(error.response?.data?.message || 'Terjadi kesalahan.', {
                    title: 'Error', variant: 'danger', solid: true
                })
            } finally {
                this.isLoading = false
            }
        },

        isSlotLocked(key) {
            return this.bromageSubmittedSlotKeys.includes(key)
        },

        // async loadBromageScore() {
        //     try {
        //         const res = await this.$store.dispatch('operatie/get_bromage_score', this.ok_id)
        //         this.applyBromageResponse(res.data.data)
        //     } catch (error) {
        //         // biarkan default kosong kalau data belum ada
        //     }
        // },

        applyBromageResponse(data) {
            if (!data) return
            this.form6_bromage.check_in_rr_time        = data.check_in_rr_time || null
            this.form6_bromage.check_in_rr_time_locked  = !!data.check_in_rr_time
            this.form6_bromage.transfer_time            = data.transfer_time || null
            this.form6_bromage.slot_score               = { ...this.form6_bromage.slot_score, ...(data.slot_score || {}) }
            this.form6_bromage.history                  = data.history || []
        },

        async submit_bromage_form() {
            if (!this.form6_bromage.check_in_rr_time) {
                this.$bvToast.toast('Isi jam masuk RR terlebih dahulu.', {
                    title: 'Peringatan', variant: 'warning', solid: true
                })
                return
            }

            const pendingSlot = this.bromageTimeSlots.find(slot =>
                !this.isSlotLocked(slot.key) &&
                this.form6_bromage.slot_score[slot.key] !== null &&
                this.form6_bromage.slot_score[slot.key] !== undefined
            )

            if (!pendingSlot) {
                this.$bvToast.toast('Pilih skor pemantauan pada salah satu kolom waktu terlebih dahulu.', {
                    title: 'Peringatan', variant: 'warning', solid: true
                })
                return
            }

            const payload = {
                monitoring_label: pendingSlot.key,
                score: this.form6_bromage.slot_score[pendingSlot.key]
            }

            // check_in_rr_time cuma dikirim kalau memang belum pernah terkunci
            if (!this.form6_bromage.check_in_rr_time_locked) {
                payload.check_in_rr_time = this.form6_bromage.check_in_rr_time
            }

            // transfer_time cuma dikirim kalau user memang mengisinya & memang boleh
            if (this.form6_bromage.transfer_time && this.bromage_can_transfer) {
                payload.transfer_time = this.form6_bromage.transfer_time
            }

            try {
                this.isLoading = true

                const res = await this.$store.dispatch('operatie/post_bromage_score', {
                    form: payload,
                    id: this.ok_id
                })

                // TIMPA seluruh state dari response server -> tidak ada push manual lagi
                this.applyBromageResponse(res.data.data)

                this.$bvToast.toast('Data Bromage Score berhasil disimpan.', {
                    title: 'Berhasil', variant: 'success', solid: true
                })

            } catch (error) {
                this.$bvToast.toast(error.response?.data?.message || 'Terjadi kesalahan.', {
                    title: 'Error', variant: 'danger', solid: true
                })
            } finally {
                this.isLoading = false
            }
        },
        
        applyPadssResponse(data) {
            if (!data) return
            this.form6_padss.post_anesthesia_time        = data.post_anesthesia_time || null
            this.form6_padss.post_anesthesia_time_locked = !!data.post_anesthesia_time
            this.form6_padss.transfer_time               = data.transfer_time || null
            this.form6_padss.transfer_time_locked        = !!data.transfer_time

            const catScore = data.category_score || {}
            Object.keys(this.form6_padss.category_score).forEach(catKey => {
                this.form6_padss.category_score[catKey] = {
                    ...this.form6_padss.category_score[catKey],
                    ...(catScore[catKey] || {})
                }
            })

            this.form6_padss.history = data.history || []
        },

        // slot terkunci per kategori kalau sudah ada di history untuk kategori+slot itu
        isPadssSlotLocked(category, slotKey) {
            return this.form6_padss.history.some(h => h.category === category && h.monitoring_label === slotKey)
        },

        async submit_padss_form() {
            if (!this.form6_padss.post_anesthesia_time) {
                this.$bvToast.toast('Isi jam pasca anestesi terlebih dahulu.', {
                    title: 'Peringatan', variant: 'warning', solid: true
                })
                return
            }

            // kumpulkan SEMUA kriteria yang sudah dipilih & belum terkunci -> kirim sekaligus
            const items = []
            Object.keys(this.form6_padss.category_score).forEach(catKey => {
                this.padssTimeSlots.forEach(slot => {
                    if (this.isPadssSlotLocked(catKey, slot.key)) return
                    const val = this.form6_padss.category_score[catKey][slot.key]
                    if (val !== null && val !== undefined) {
                        items.push({ category: catKey, monitoring_label: slot.key, score: val })
                    }
                })
            })

            if (!items.length) {
                this.$bvToast.toast('Pilih skor pada kriteria dan kolom waktu terlebih dahulu.', {
                    title: 'Peringatan', variant: 'warning', solid: true
                })
                return
            }

            const payload = { items }

            if (!this.form6_padss.post_anesthesia_time_locked) {
                payload.post_anesthesia_time = this.form6_padss.post_anesthesia_time
            }

            if (this.form6_padss.transfer_time && this.padss_can_transfer) {
                payload.transfer_time = this.form6_padss.transfer_time
            }

            try {
                this.isLoading = true

                const res = await this.$store.dispatch('operatie/post_padss_score', {
                    form: payload,
                    id: this.ok_id
                })

                // TIMPA seluruh state dari response server -> tidak ada push manual lagi
                this.applyPadssResponse(res.data.data)

                this.$bvToast.toast('Data PADSS berhasil disimpan.', {
                    title: 'Berhasil', variant: 'success', solid: true
                })

            } catch (error) {
                this.$bvToast.toast(error.response?.data?.message || 'Terjadi kesalahan.', {
                    title: 'Error', variant: 'danger', solid: true
                })
            } finally {
                this.isLoading = false
            }
        },

        applyAldreteResponse(data){
            if(!data) return
            this.form6_aldrete.post_anesthesia_time = data.post_anesthesia_time || null
            this.form6_aldrete.post_anesthesia_time_locked = !!data.post_anesthesia_time
            this.form6_aldrete.transfer_time = data.transfer_time || null
            this.form6_aldrete.transfer_time_locked = !!data.transfer_time
            const catScore = data.category_score || {}
            Object.keys(this.form6_aldrete.category_score).forEach(cat=>{
                this.form6_aldrete.category_score[cat] = {
                    ...this.form6_aldrete.category_score[cat],
                    ...(catScore[cat] || {})
                }
            })
            this.form6_aldrete.history = data.history || []
        },

        isAldreteSlotLocked(category,slot){
            return this.form6_aldrete.history.some(h=>
                h.category===category &&
                h.monitoring_label===slot
            )
        },

        async submit_aldrete_form(){
            if (!this.form6_aldrete.post_anesthesia_time){
                this.$bvToast.toast('Isi jam pasca anestesi terlebih dahulu.',{
                    title:'Peringatan',
                    variant:'warning',
                    solid:true
                })
                return
            }

            const items = []
            Object.keys(this.form6_aldrete.category_score).forEach(cat=>{
                this.padssTimeSlots.forEach(slot=>{
                    if (this.isAldreteSlotLocked(cat,slot.key))
                        return
                    const val=this.form6_aldrete.category_score[cat][slot.key]
                    if (val !== null && val !== undefined){
                        items.push({
                            category:cat,
                            monitoring_label:slot.key,
                            score:val
                        })
                    }
                })
            })

            if (!items.length){
                this.$bvToast.toast('Belum ada data yang dipilih.',{
                    title:'Peringatan',
                    variant:'warning',
                    solid:true
                })
                return
            }

            const payload = {items}

            if (!this.form6_aldrete.post_anesthesia_time_locked){
                payload.post_anesthesia_time = this.form6_aldrete.post_anesthesia_time
            }

            if (this.form6_aldrete.transfer_time && this.aldrete_can_transfer) {
                payload.transfer_time = this.form6_aldrete.transfer_time
            } try {
                this.isLoading = true
                const res = await this.$store.dispatch('operatie/post_aldrete_score',{form:payload,id:this.ok_id })
                this.applyAldreteResponse(res.data.data)
                this.$bvToast.toast('Data Aldrete berhasil disimpan.',{
                    title:'Berhasil',
                    variant:'success',
                    solid:true
                })
            } catch(e) {
                this.$bvToast.toast(e.response?.data?.message || 'Terjadi kesalahan.',{
                    title:'Error',
                    variant:'danger',
                    solid:true
                })
            } finally {
                this.isLoading=false
            }
        },

        applyStewardResponse(data) {
            if (!data) return
            this.form6_steward.post_anesthesia_time = data.post_anesthesia_time || null
            this.form6_steward.post_anesthesia_time_locked = !!data.post_anesthesia_time
            this.form6_steward.transfer_time = data.transfer_time || null
            this.form6_steward.transfer_time_locked = !!data.transfer_time
            const catScore = data.category_score || {}
            Object.keys(this.form6_steward.category_score).forEach(cat => {
                this.form6_steward.category_score[cat] = {
                    ...this.form6_steward.category_score[cat],
                    ...(catScore[cat] || {})
                }
            })
            this.form6_steward.history = data.history || []
        },

        isStewardSlotLocked(category, slot) {
            return this.form6_steward.history.some(h => h.category === category && h.monitoring_label === slot )
        },

        async submit_steward_form() {
            if (!this.form6_steward.post_anesthesia_time) {
                this.$bvToast.toast('Isi jam pasca anestesi terlebih dahulu.', {
                    title: 'Peringatan',
                    variant: 'warning',
                    solid: true
                })
                return
            }

            const items = []

            Object.keys(this.form6_steward.category_score).forEach(cat => {
                this.padssTimeSlots.forEach(slot => {
                    if (this.isStewardSlotLocked(cat, slot.key)) return

                    const val = this.form6_steward.category_score[cat][slot.key]

                    if (val !== null && val !== undefined) {
                        items.push({
                            category: cat,
                            monitoring_label: slot.key,
                            score: val
                        })
                    }
                })
            })

            if (!items.length) {
                this.$bvToast.toast('Belum ada data yang dipilih.', {
                    title: 'Peringatan',
                    variant: 'warning',
                    solid: true
                })
                return
            }

            const payload = { items }

            if (!this.form6_steward.post_anesthesia_time_locked) {
                payload.post_anesthesia_time = this.form6_steward.post_anesthesia_time
            }

            if (this.form6_steward.transfer_time && this.steward_can_transfer) {
                payload.transfer_time = this.form6_steward.transfer_time
            }

            try {
                this.isLoading = true

                const res = await this.$store.dispatch('operatie/post_steward_score', {
                    form: payload,
                    id: this.ok_id
                })

                this.applyStewardResponse(res.data.data)

                this.$bvToast.toast('Data Steward berhasil disimpan.', {
                    title: 'Berhasil',
                    variant: 'success',
                    solid: true
                })

            } catch (e) {
                this.$bvToast.toast(
                    e.response?.data?.message || 'Terjadi kesalahan.',
                    {
                        title: 'Error',
                        variant: 'danger',
                        solid: true
                    }
                )
            } finally {
                this.isLoading = false
            }
        },

        async submit_post_op_handover_form() {
            const payload = { ...this.post_op_handover_form }

            try {
                this.isLoadingPostOp = true

                await this.$store
                    .dispatch('operatie/post_post_op_handover', {
                        form: payload,
                        id: this.ok_id
                    })
                    .then(res => {
                        this.$bvToast.toast('Data Serah Terima Post-Operasi berhasil disimpan.', {
                            title: 'Berhasil', variant: 'success', solid: true
                        })

                        const res_data = res.data.data
                        Object.keys(this.post_op_handover_form).forEach(key => {
                            if (res_data[key] !== undefined && res_data[key] !== null) {
                                this.$set(this.post_op_handover_form, key, res_data[key])
                            }
                        })
                    })
                    .catch(({ message, debug }) => {
                        this.notification(`Failed ${debug}`, 'CloseIcon', 'danger', `${message}`)
                    })

            } catch (error) {
                this.$bvToast.toast(error.response?.data?.message || 'Terjadi kesalahan.', {
                    title: 'Error', variant: 'danger', solid: true
                })
            } finally {
                this.isLoadingPostOp = false
            }
        },

        async submit_rr_action_form() {
            const payload = { ...this.rr_action_form }

            try {
                this.isLoadingRR = true

                await this.$store
                    .dispatch('operatie/post_rr_action', {
                        form: payload,
                        id: this.ok_id
                    })
                    .then(res => {
                        this.$bvToast.toast('Data Tindakan RR berhasil disimpan.', {
                            title: 'Berhasil', variant: 'success', solid: true
                        })

                        const res_data = res.data.data
                        Object.keys(this.rr_action_form).forEach(key => {
                            if (res_data[key] !== undefined && res_data[key] !== null) {
                                this.$set(this.rr_action_form, key, res_data[key])
                            }
                        })
                    })
                    .catch(({ message, debug }) => {
                        this.notification(`Failed ${debug}`, 'CloseIcon', 'danger', `${message}`)
                    })

            } catch (error) {
                this.$bvToast.toast(error.response?.data?.message || 'Terjadi kesalahan.', {
                    title: 'Error', variant: 'danger', solid: true
                })
            } finally {
                this.isLoadingRR = false
            }
        }

    },

    beforeRouteEnter(to, from, next) {
        next(vm => {
            const fromPath = localStorage.getItem('fromPath')
            if (fromPath) {
                if (fromPath === '/patient/doctor') {
                    vm.active.current_tab = 'menu7'
                }
                localStorage.removeItem('fromPath')
            }
        });
    },
    created() {
        
        if (this.$route.params.ok_id) {
            this.ok_id = this.$route.params.ok_id
            this.getOperatorList()
            this.getOperatingDetail(this.$route.params.ok_id)
            // this.$store.dispatch('soap/getSoapDoctorById', this.$route.params.id)
            // this.$store.dispatch('soap/getSoapPrescriptionById', this.$route.params.id)
        }

        // this.$store.dispatch('helper/getParam', 'type_room')
        // this.$store.dispatch('depo/getParam','depo')
        // this.$store.dispatch('healthfacilityitem/getParamAlls')
        // this.$store.dispatch('catalog/getParam')
        // this.$store.dispatch('packages/getParam')
    },

}
</script>
<style scoped>
.label-head::before {
    content: '';
    border-left: 6px solid #648DE1;
}
.c-img {
    aspect-ratio: 1/1;
    object-fit: cover;
    max-width: 150px;
    max-height: 150px;
    border: 10px solid white;
}
.custom-tab li {
    list-style-type: none;
}
.custom-tab li a {
    list-style-type: none;
    color: #78838C;
}
.custom-tab li a:hover {
    text-decoration: none;
    color: #101725;
    background-color: rgb(245, 239, 254);
    cursor: pointer;
}
.custom-tab li a.active {
    background-color: rgba(132, 66, 243, 0.05);
    color: indigo;
    border: 1px solid indigo;
    color: #1B1B1B;
}
.custom-tab li a.active:hover {
    border: 1px solid indigo;
    background-color: rgb(245, 239, 254);
    color: indigo;
    cursor: pointer;
}

</style>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>