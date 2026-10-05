<template>
    <div>
        <TopPaymentMenu selectedMenu="menu2" class="mt-2" />
        <div v-if="active.section === 'section1'" class="mt-32">
            <div class="card">
                <div @click="showFilter = !showFilter" class="card-header d-flex justify-content-between bg-primary-200">
                    <span class="text-bold-18">Filter Data</span>
                    <b-icon v-if="showFilter" icon="chevron-up" />
                    <b-icon v-else icon="chevron-down" />
                </div>
                <div v-if="showFilter" class="card-body">
                    <div class="col">
                        <div class="row c-form">
                            <div class="col-lg-6 px-lg-5">
                                <div class="mt-12 mt-lg-0">
                                    <label>No Pendaftaran</label>
                                    <b-form-input></b-form-input>
                                </div>
                                <div class="mt-12">
                                    <label>No RM</label>
                                    <b-form-input></b-form-input>
                                </div>
                                <div class="mt-12">
                                    <label>Status</label>
                                    <b-form-select></b-form-select>
                                </div>
                                <div class="mt-12">
                                    <label>Dokter</label>
                                    <b-form-select></b-form-select>
                                </div>
                                <div class="mt-12">
                                    <label>Instalasi</label>
                                    <b-form-select></b-form-select>
                                </div>
                                <div class="mt-12">
                                    <label>Instalasi Sub</label>
                                    <b-form-select></b-form-select>
                                </div>
                            </div>
                            <div class="col-lg-6 px-lg-5">
                                <div class="mt-12 mt-lg-0">
                                    <label>Nama Pasien</label>
                                    <b-form-select></b-form-select>
                                </div>
                                <div class="mt-12">
                                    <label>Jenis Pasien</label>
                                    <b-form-select></b-form-select>
                                </div>
                                <div class="mt-12">
                                    <label>Kelamin</label>
                                    <b-form-select></b-form-select>
                                </div>
                                <div class="mt-12">
                                    <label>Cara Masuk</label>
                                    <b-form-select></b-form-select>
                                </div>
                                <div class="mt-12">
                                    <label>Cara Bayar</label>
                                    <b-form-select></b-form-select>
                                </div>
                                <div class="mt-12">
                                    <label>Tanggal Pendaftaran</label>
                                    <div class="row mx-0">
                                        <b-form-datepicker class="col-lg"></b-form-datepicker>
                                        <span class="px-3">-</span>
                                        <b-form-datepicker class="col-lg"></b-form-datepicker>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-32 d-flex justify-content-end px-lg-4">
                            <b-button pill variant="outline-indigo">
                                <b-icon icon="x" /> Clear Filter
                            </b-button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-32 mt-lg-40">
                <p class="text-bold-24">List Data</p>
                <div class="row mx-0 mt-8">
                    <span class="text-regular-14">Tampilkan</span>
                    <select id="per-page-select" v-model="patient.perPage"
                        class="px-8 border-left-0 border-right-0 border-top-0 mx-2">
                        <option value="5" selected>5</option>
                        <option value="10" selected>10</option>
                        <option value="15" selected>15</option>
                    </select>
                    <span class="text-regular-14">Entri</span>
                </div>
                <b-table bordered responsive hover small head-variant="light" :fields="patient.field" :items="patient.item"
                    :current-page="patient.currentPage" @filtered="patientFiltered" :per-page="patient.perPage"
                    class="mt-12 text-center">
                    <template #cell(code)="data">
                        <div style="width: 150px;">
                            <b-badge variant="info-600">{{ data.value }}</b-badge>
                        </div>
                    </template>
                    <template #cell(status)="data">
                        <div style="width: 140px;">
                            <b-badge v-if="data.value === 'Belum Melakukan Pelunasan'" variant="warning-600"
                                class="text-white d-flex" style="white-space:pre-wrap; font-weight: 400;">
                                <b-icon icon="exclamation-triangle-fill" /> {{ data.value }}
                            </b-badge>
                            <b-badge v-else variant="success-500" class="text-white d-flex"
                                style="white-space:pre-wrap; font-weight: 400;">
                                <b-icon icon="check-square-fill" /> {{ data.value }}
                            </b-badge>
                            <b-badge v-else variant="success-600">{{ data.value }}</b-badge>
                        </div>
                    </template>
                    <template #cell(dateReg)="data">
                        <div style="width: 140px;">
                            <span class="text-medium-14">{{ data.value }}</span>
                        </div>
                    </template>
                    <template #cell(payment)="data">
                        <div style="width: 140px;">
                            <span class="text-medium-14">{{ data.value }}</span>
                        </div>
                    </template>
                    <template #cell(entry)="data">
                        <div style="width: 140px;">
                            <span class="text-medium-14">{{ data.value }}</span>
                        </div>
                    </template>
                    <template #cell(no_rm)="data">
                        <div style="width: 140px;">
                            <span class="text-medium-14">{{ data.value }}</span>
                        </div>
                    </template>
                    <template #cell(name)="data">
                        <div style="width: 160px;">
                            <span class="text-medium-14 text-uppercase">{{ data.value }}</span>
                        </div>
                    </template>
                    <template #cell(birth)="data">
                        <div style="width: 140px;">
                            <span class="text-medium-14">{{ data.value }}</span>
                        </div>
                    </template>
                    <template #cell(age)="data">
                        <div style="width: 140px;">
                            <span class="text-medium-14">{{ data.value }}</span>
                        </div>
                    </template>
                    <template #cell(gender)="data">
                        <div style="width: 140px;">
                            <span class="text-medium-14">{{ data.value }}</span>
                        </div>
                    </template>
                    <template #cell(instalation)="data">
                        <div style="width: 140px;">
                            <span class="text-medium-14">{{ data.value }}</span>
                        </div>
                    </template>
                    <template #cell(poli)="data">
                        <div style="width: 140px;">
                            <span class="text-medium-14">{{ data.value }}</span>
                        </div>
                    </template>
                    <template #cell(doctor)="data">
                        <div style="width: 140px;">
                            <span class="text-medium-14">{{ data.value }}</span>
                        </div>
                    </template>
                    <template #cell(serial)="data">
                        <div style="width: 100px;">
                            <span class="text-medium-14">{{ data.value }}</span>
                        </div>
                    </template>
                    <template #cell(action)>
                        <div style="width: 200px;">
                            <b-button @click="changeSection('section2')" size="sm" variant="info-600"><b-icon
                                    icon="file-earmark" /> Detail Pelayanan
                                Pasien</b-button>
                        </div>
                    </template>
                </b-table>
                <div class="col px-0 mt-20">
                    <div class="d-flex">
                        <span class="text-regular-14">Menampilkan 1 hingga 10 dari 200 entri</span>
                        <div class="ml-auto">
                            <b-pagination v-model="patient.currentPage" :total-rows="patient.totalRows"
                                :per-page="patient.perPage" :current-page="patient.currentPage" align="fill" size="sm"
                                class="my-0"></b-pagination>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-else>
            <div class="card border-0 bg-neutral-100 mt-32">
                <div class="card-body">
                    <p class="text-bold-18">Data Rincian Tagihan Pasien</p>
                    <div class="row mt-24">
                        <div class="col col-lg-6 px-0 px-lg-4">
                            <b-table-simple class="text-bold-14">
                                <b-tr>
                                    <b-td class="border-0" style="width: 30%;">No Reg / RM</b-td>
                                    <b-td class="border-0 text-center" style="width: 10%;">:</b-td>
                                    <b-td class="border-0">202212231200002</b-td>
                                </b-tr>
                                <b-tr>
                                    <b-td class="border-0" style="width: 30%;">Cara Bayar</b-td>
                                    <b-td class="border-0 text-center" style="width: 10%;">:</b-td>
                                    <b-td class="border-0">PT. Nama Asuransi by Asuransi</b-td>
                                </b-tr>
                                <b-tr>
                                    <b-td class="border-0" style="width: 30%;">Nama Pasien</b-td>
                                    <b-td class="border-0 text-center" style="width: 10%;">:</b-td>
                                    <b-td class="border-0">Fajar Surya Pratikno [Tn]</b-td>
                                </b-tr>
                                <b-tr>
                                    <b-td class="border-0" style="width: 30%;">Kelamin</b-td>
                                    <b-td class="border-0 text-center" style="width: 10%;">:</b-td>
                                    <b-td class="border-0">Laki-laki</b-td>
                                </b-tr>
                                <b-tr>
                                    <b-td class="border-0" style="width: 30%;">Alamat</b-td>
                                    <b-td class="border-0 text-center" style="width: 10%;">:</b-td>
                                    <b-td class="border-0">JL. Pesing Poglar RT 05/05 No 24, Jakarta Barat</b-td>
                                </b-tr>

                            </b-table-simple>
                        </div>
                        <div class="col col-lg-6 px-0 px-lg-4">
                            <b-table-simple class="text-semi-14">
                                <b-tr>
                                    <b-td class="border-0" style="width: 30%;">Tgl pendaftaran</b-td>
                                    <b-td class="border-0 text-center" style="width: 10%;">:</b-td>
                                    <b-td class="border-0">23-12-2022 03:25:44</b-td>
                                </b-tr>
                                <b-tr>
                                    <b-td class="border-0" style="width: 30%;">Cara Masuk</b-td>
                                    <b-td class="border-0 text-center" style="width: 10%;">:</b-td>
                                    <b-td class="border-0">Langsung</b-td>
                                </b-tr>
                                <b-tr>
                                    <b-td class="border-0" style="width: 30%;">Tanggal Lahir</b-td>
                                    <b-td class="border-0 text-center" style="width: 10%;">:</b-td>
                                    <b-td class="border-0">25-08-1991</b-td>
                                </b-tr>
                                <b-tr>
                                    <b-td class="border-0" style="width: 30%;">Umur</b-td>
                                    <b-td class="border-0 text-center" style="width: 10%;">:</b-td>
                                    <b-td class="border-0">31 Tahun 4 Bulan 9 Hari</b-td>
                                </b-tr>
                                <b-tr>
                                    <b-td class="border-0" style="width: 30%;">No HP</b-td>
                                    <b-td class="border-0 text-center" style="width: 10%;">:</b-td>
                                    <b-td class="border-0">087734132884</b-td>
                                </b-tr>
                            </b-table-simple>
                        </div>
                    </div>
                </div>
            </div>
            <!-- here button menu -->
            <div class="col-lg-4 mt-40">
                <div class="row">
                    <div class="col px-1 px-lg-2">
                        <b-button @click="changeTab('tab1')" pill :variant="active.tab === 'tab1' ? 'indigo' : 'outline-indigo'"
                        class="w-100">Rincian</b-button>
                    </div>
                    <div class="col px-1 px-lg-2">
                        <b-button @click="changeTab('tab2')" pill :variant="active.tab === 'tab2' ? 'indigo' : 'outline-indigo'"
                        class="w-100">Pembayaran</b-button>
                    </div>
                    <div class="col px-1 px-lg-2">
                        <b-button @click="changeTab('tab3')" pill :variant="active.tab === 'tab3' ? 'indigo' : 'outline-indigo'"
                        class="w-100">Uang Muka</b-button>
                    </div>
                </div>
            </div>
            <!-- here inside menu -->
            <div class="mt-20">
                <div v-if="active.tab === 'tab1'">
                    <b-table-simple responsive bordered>
                        <b-thead class="bg-neutral-100 text-bold-14 text-center">
                            <b-tr>
                                <b-th style="min-width: 120px;">Tanggal & Jam</b-th>
                                <b-th style="min-width: 400px;">Nama Rincian</b-th>
                                <b-th style="min-width: 120px;">Harga</b-th>
                                <b-th style="min-width: 5px;">Qty</b-th>
                                <b-th style="min-width: 120px;">Diskon</b-th>
                                <b-th style="min-width: 120px;">Total</b-th>
                                <b-th style="min-width: 120px;">Buat Tagihan <b-form-checkbox class="ml-1 d-inline" /></b-th>
                            </b-tr>
                        </b-thead>
                        <b-tbody class="text-right text-medium-14">
                            <b-tr>
                                <b-td>23/12/2022 10:25</b-td>
                                <b-td class="text-left">
                                    <div class="row">
                                        <div class="col-lg">Adm Pasien Lama Rawat Jalan</div>
                                        <div class="col-lg">
                                            <b-form-select></b-form-select>
                                        </div>
                                    </div>
                                </b-td>
                                <b-td>15,000.00</b-td>
                                <b-td class="text-center">1</b-td>
                                <b-td>0,00</b-td>
                                <b-td>15,000.00</b-td>
                                <b-td class="text-center"><b-form-checkbox /></b-td>
                            </b-tr>
                            <b-tr>
                                <b-td colspan="7">
                                    <div class="row">
                                        <b-button @click="modalShow = !modalShow" variant="info-600" size="sm" class="mx-2">
                                            <b-icon icon="plus" /> Tambah Rincian Lainnya
                                        </b-button>
                                        <b-button variant="info-600" size="sm" class="mx-2">
                                            <b-icon icon="arrow-repeat" /> Update Harga Obat
                                        </b-button>
                                    </div>
                                </b-td>
                            </b-tr>
                            <b-tr>
                                <b-td colspan="5">Total Tagihan</b-td>
                                <b-td colspan="2">15,000.00</b-td>
                            </b-tr>
                            <b-tr>
                                <b-td colspan="5">Total Item yang Dipilih</b-td>
                                <b-td colspan="2">0,00</b-td>
                            </b-tr>
                            <b-tr>
                                <b-td colspan="5" class="text-left">
                                    <div class="col-3 ml-auto">
                                        <small class="text-muted">Pembayaran Partner:</small>
                                        <p class="text-regular-14">PT. Nama Asuransi</p>
                                        <div class="d-flex">
                                            <p>Dijamin Sebagian</p>
                                            <b-form-checkbox size="lg" switch class="ml-auto"></b-form-checkbox>
                                        </div>
                                    </div>
                                </b-td>
                                <b-td colspan="2" class="position-relative p-0 text-center">
                                    <div class="col-2 bg-success-500 position-absolute d-flex justify-content-center align-items-center" style="height: 100%; left:0;">
                                        <span class="text-white fw-bold">Rp</span>
                                    </div>
                                    <div class="col-2 bg-neutral-100 position-absolute d-flex justify-content-center align-items-center" style="height: 100%; right:0;">
                                        <span class="text-dark fw-bold">%</span>
                                    </div>
                                    <div class="p-3 mx-auto" style="width: 60%;">10,000.00</div>
                                </b-td>
                            </b-tr>
                            <b-tr>
                                <b-td colspan="5">Ekses/Sisa Tagihan</b-td>
                                <b-td colspan="2">5,000.00</b-td>
                            </b-tr>
                            <b-tr>
                                <b-td colspan="5">Diskon Item</b-td>
                                <b-td colspan="2">0,00</b-td>
                            </b-tr>
                            <b-tr>
                                <b-td colspan="5">Diskon</b-td>
                                <b-td colspan="2">
                                    <b-form-select></b-form-select>
                                </b-td>
                            </b-tr>
                            <b-tr>
                                <b-td colspan="5">Nilai Diskon</b-td>
                                <b-td colspan="2">0,00</b-td>
                            </b-tr>
                            <b-tr>
                                <b-td colspan="5">Voucher</b-td>
                                <b-td colspan="2">0,00</b-td>
                            </b-tr>
                            <b-tr>
                                <b-td colspan="5">Total</b-td>
                                <b-td colspan="2">0,00</b-td>
                            </b-tr>

                        </b-tbody>
                    </b-table-simple>
                    <div class="d-flex justify-content-lg-end mt-32">
                        <div class="col-lg-5">
                            <div class="row">
                                <div class="col-lg px-lg-2 my-1 my-lg-0">
                                    <b-button pill variant="outline-indigo" size="sm" class="w-100">
                                        <b-icon icon="file-earmark" /> E-Klaim
                                    </b-button>
                                </div>
                                <div class="col-lg px-lg-2 my-1 my-lg-0">
                                    <b-button pill variant="success-600" size="sm" class="w-100">
                                        <b-icon icon="eye" /> Preview Tagihan
                                    </b-button>
                                </div>
                                <div class="col-lg px-lg-2 my-1 my-lg-0">
                                    <b-button pill variant="primary" size="sm" class="w-100">Buat Invoice</b-button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <b-modal v-model="modalShow" size="md" title="Tambah Rincian">
                        <!-- here -->
                        <div class="row">
                            <b-button pill size="sm" variant="outline-indigo" class="m-1">Rincian Dokter</b-button>
                            <b-button pill size="sm" variant="outline-indigo" class="m-1">Rincian Perawat</b-button>
                            <b-button pill size="sm" variant="outline-indigo" class="m-1">Rincian Laboratorium</b-button>
                            <b-button pill size="sm" variant="outline-indigo" class="m-1">Rincian Radologi</b-button>
                            <b-button pill size="sm" variant="outline-indigo" class="m-1">Rincian Pemakaian Alat</b-button>
                            <b-button pill size="sm" variant="outline-indigo" class="m-1">Rincian APD</b-button>
                            <b-button pill size="sm" variant="outline-indigo" class="m-1">Rincian Lainnya</b-button>
                            <b-button pill size="sm" variant="outline-indigo" class="m-1">Rincian OK</b-button>
                        </div>
                    </b-modal>
                </div>
                <div v-else-if="active.tab === 'tab2'">
                    <b-table small bordered responsive head-variant="light" :fields="Payment.fields"
                        :items="Payment.items" class="mt-20 text-center" style="vertical-align: middle;">
                        <template #cell(no)="data">
                            <div class="px-3">
                                <span class="text-regular-14">{{ data.value }}</span>
                            </div>
                        </template>
                        <template #cell(bill)="data">
                            <div class="px-3" style="min-width: 200px;">
                                <p class="text-regular-14 text-right">{{ data.value }}</p>
                            </div>
                        </template>
                        <template #cell(paid)="data">
                            <div class="px-3" style="min-width: 200px;">
                                <p class="text-regular-14 text-right">{{ data.value }}</p>
                            </div>
                        </template>
                        <template #cell(remain)="data">
                            <div class="px-3" style="min-width: 200px;">
                                <p class="text-regular-14 text-right">{{ data.value }}</p>
                            </div>
                        </template>
                        <template #cell(time_pay)="data">
                            <div class="px-3" style="min-width: 200px;">
                                <span class="text-regular-14">{{ data.value }}</span>
                            </div>
                        </template>
                        <template #cell(action_pay)>
                            <div class="px-3 text-left" style="min-width: 280px;">
                                <b-button size="sm" variant="indigo" class="m-1"><b-icon icon="eye" /> Lihat</b-button>
                                <b-button size="sm" variant="orange-500" class="m-1"><b-icon icon="pencil" /> Pelunasan</b-button>
                                <b-button size="sm" variant="danger-500" class="m-1"><b-icon icon="x" /> Batal Invoice</b-button>
                                <b-button size="sm" variant="success-600" class="m-1"><b-icon icon="check" /> Verifikasi</b-button>
                                <b-button size="sm" variant="info-800" class="m-1"><b-icon icon="printer" /> Cetak Invoice Kop</b-button>
                                <b-button size="sm" variant="info-800" class="m-1"><b-icon icon="printer" /> Cetak Invoice</b-button>
                                <b-button size="sm" variant="success-600" class="m-1"><b-icon icon="journal-text" /> Export Excel</b-button>
                            </div>
                        </template>
                    </b-table>
                </div>
                <div v-else>
                    <div class="d-flex justify-content-lg-end">
                        <b-button size="sm" variant="info-600" class="px-3">
                            <b-icon icon="plus" /> tambah Uang Muka
                        </b-button>
                    </div>
                    <b-table small bordered responsive head-variant="light" :fields="DownPayment.fields"
                        :items="DownPayment.items" class="mt-20 text-center" style="vertical-align: middle;">
                        <template #cell(deposit)="data">
                            <div class="px-3" style="min-width: 200px;">
                                <p class="text-regular-14 text-right">{{ data.value }}</p>
                            </div>
                        </template>
                        <template #cell(time)="data">
                            <div class="px-3" style="min-width: 200px;">
                                <span class="text-regular-14">{{ data.value }}</span>
                            </div>
                        </template>
                        <template #cell(date)="data">
                            <div class="px-3" style="min-width: 200px;">
                                <span class="text-regular-14">{{ data.value }}</span>
                            </div>
                        </template>
                        <template #cell(action)>
                            <div class="px-3" style="min-width: 280px;">
                                <span class="text-regular-14">actions</span>
                            </div>
                        </template>
                        <template #cell(no)="data">
                            <div class="px-3">
                                <span class="text-regular-14">{{ data.value }}</span>
                            </div>
                        </template>
                    </b-table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import TopPaymentMenu from '@/@core/components/top-menu/TopPaymentMenu.vue';

export default {
    name: 'PatientPayment',
    components: {
        TopPaymentMenu
    },
    data() {
        return {
            active: {
                section: 'section1',
                tab: 'tab1'
            },
            modalShow: false,
            showFilter: true,
            patient: {
                field: [
                    { key: 'code', label: 'Kode Pendaftaran' },
                    { key: 'status', label: 'Status' },
                    { key: 'dateReg', label: 'Tanggal Pendaftaran' },
                    { key: 'payment', label: 'Cara Bayar' },
                    { key: 'entry', label: 'Cara Masuk' },
                    { key: 'no_rm', label: 'No RM' },
                    { key: 'name', label: 'Nama Pasien [A/n]' },
                    { key: 'birth', label: 'Tanggal Lahir' },
                    { key: 'age', label: 'Umur' },
                    { key: 'gender', label: 'Kelamin Pasien' },
                    { key: 'instalation', label: 'Instalasi Asal' },
                    { key: 'poli', label: 'Poli/Ruangan Asal' },
                    { key: 'doctor', label: 'Dokter' },
                    { key: 'serial', label: 'No Urut' },
                    { key: 'action', label: 'Aksi' },
                ],
                item: [
                    {
                        code: '202302100200001',
                        status: 'Belum Melakukan Pelunasan',
                        dateReg: '10-02-2023 07:02:35',
                        payment: 'BPJS',
                        entry: 'Lansung',
                        no_rm: '00013599',
                        name: 'Rio Riswanto [Tn]',
                        birth: '1992-08-16',
                        age: '30 Tahun 6 Bulan 0 Hari',
                        gender: 'Laki-laki',
                        instalation: 'Instalasi Rawat Jalan',
                        poli: 'Poli Nyeri',
                        doctor: 'dr. Mahardika Mardjikoen, Sp. An',
                        serial: '1',
                    },
                    {
                        code: '202302100200001',
                        status: 'Sudah Melakukan Pelunasan',
                        dateReg: '10-02-2023 07:02:35',
                        payment: 'BPJS',
                        entry: 'Lansung',
                        no_rm: '00013599',
                        name: 'Rio Riswanto [Tn]',
                        birth: '1992-08-16',
                        age: '30 Tahun 6 Bulan 0 Hari',
                        gender: 'Laki-laki',
                        instalation: 'Instalasi Rawat Jalan',
                        poli: 'Poli Nyeri',
                        doctor: 'dr. Mahardika Mardjikoen, Sp. An',
                        serial: '1',
                    },
                    {
                        code: '202302100200001',
                        status: 'Belum Melakukan Pelunasan',
                        dateReg: '10-02-2023 07:02:35',
                        payment: 'BPJS',
                        entry: 'Lansung',
                        no_rm: '00013599',
                        name: 'Rio Riswanto [Tn]',
                        birth: '1992-08-16',
                        age: '30 Tahun 6 Bulan 0 Hari',
                        gender: 'Laki-laki',
                        instalation: 'Instalasi Rawat Jalan',
                        poli: 'Poli Nyeri',
                        doctor: 'dr. Mahardika Mardjikoen, Sp. An',
                        serial: '1',
                    },
                    {
                        code: '202302100200001',
                        status: 'Sudah Melakukan Pelunasan',
                        dateReg: '10-02-2023 07:02:35',
                        payment: 'BPJS',
                        entry: 'Lansung',
                        no_rm: '00013599',
                        name: 'Rio Riswanto [Tn]',
                        birth: '1992-08-16',
                        age: '30 Tahun 6 Bulan 0 Hari',
                        gender: 'Laki-laki',
                        instalation: 'Instalasi Rawat Jalan',
                        poli: 'Poli Nyeri',
                        doctor: 'dr. Mahardika Mardjikoen, Sp. An',
                        serial: '1',
                    },
                    {
                        code: '202302100200001',
                        status: 'Belum Melakukan Pelunasan',
                        dateReg: '10-02-2023 07:02:35',
                        payment: 'BPJS',
                        entry: 'Lansung',
                        no_rm: '00013599',
                        name: 'Rio Riswanto [Tn]',
                        birth: '1992-08-16',
                        age: '30 Tahun 6 Bulan 0 Hari',
                        gender: 'Laki-laki',
                        instalation: 'Instalasi Rawat Jalan',
                        poli: 'Poli Nyeri',
                        doctor: 'dr. Mahardika Mardjikoen, Sp. An',
                        serial: '1',
                    },
                    {
                        code: '202302100200001',
                        status: 'Sudah Melakukan Pelunasan',
                        dateReg: '10-02-2023 07:02:35',
                        payment: 'BPJS',
                        entry: 'Lansung',
                        no_rm: '00013599',
                        name: 'Rio Riswanto [Tn]',
                        birth: '1992-08-16',
                        age: '30 Tahun 6 Bulan 0 Hari',
                        gender: 'Laki-laki',
                        instalation: 'Instalasi Rawat Jalan',
                        poli: 'Poli Nyeri',
                        doctor: 'dr. Mahardika Mardjikoen, Sp. An',
                        serial: '1',
                    },

                ],
                currentPage: 1,
                totalRows: 1,
                perPage: 5,
            },
            DownPayment: {
                fields: [
                    { key: 'no', label: 'No' },
                    { key: 'date', label: 'Tanggal' },
                    { key: 'time', label: 'Waktu' },
                    { key: 'deposit', label: 'Setoran' },
                    { key: 'action', label: 'Aksi' },
                ],
                items: [
                    {
                        no: '1',
                        date: '10-02-2023',
                        time: '02-01-2023 04:58:23',
                        deposit: 'Rp. 100.000',
                    },
                    {
                        no: '2',
                        date: '10-02-2023',
                        time: '02-01-2023 04:58:23',
                        deposit: 'Rp. 100.000',
                    },
                    {
                        no: '3',
                        date: '10-02-2023',
                        time: '02-01-2023 04:58:23',
                        deposit: 'Rp. 100.000',
                    },
                    {
                        no: '4',
                        date: '10-02-2023',
                        time: '02-01-2023 04:58:23',
                        deposit: 'Rp. 100.000',
                    },
                    {
                        no: '5',
                        date: '10-02-2023',
                        time: '02-01-2023 04:58:23',
                        deposit: 'Rp. 100.000',
                    },
                    {
                        no: '6',
                        date: '10-02-2023',
                        time: '02-01-2023 04:58:23',
                        deposit: 'Rp. 100.000',
                    },
                ],
            },
            Payment: {
                fields: [
                    { key: 'no', label: 'No' },
                    { key: 'bill', label: 'Tagihan Pasien' },
                    { key: 'paid', label: 'Jumlah Terbayar' },
                    { key: 'remain', label: 'Sisa' },
                    { key: 'time_pay', label: 'Waktu' },
                    { key: 'action_pay', label: 'Aksi' },
                ],
                items:[
                    {
                        no: '1',
                        bill: 'Rp. 100.000',
                        paid: 'Rp. 100.000',
                        remain: 'Rp. 0',
                        time_pay: '02-01-2023 04:58:23',
                    },
                    {
                        no: '2',
                        bill: 'Rp. 100.000',
                        paid: 'Rp. 100.000',
                        remain: 'Rp. 0',
                        time_pay: '02-01-2023 04:58:23',
                    },
                    {
                        no: '3',
                        bill: 'Rp. 100.000',
                        paid: 'Rp. 100.000',
                        remain: 'Rp. 0',
                        time_pay: '02-01-2023 04:58:23',
                    },
                    {
                        no: '4',
                        bill: 'Rp. 100.000',
                        paid: 'Rp. 100.000',
                        remain: 'Rp. 0',
                        time_pay: '02-01-2023 04:58:23',
                    },
                    {
                        no: '5',
                        bill: 'Rp. 100.000',
                        paid: 'Rp. 100.000',
                        remain: 'Rp. 0',
                        time_pay: '02-01-2023 04:58:23',
                    },
                    {
                        no: '6',
                        bill: 'Rp. 100.000',
                        paid: 'Rp. 100.000',
                        remain: 'Rp. 0',
                        time_pay: '02-01-2023 04:58:23',
                    },
                ],                
            }
        }
    },
    mounted() {
        this.patient.totalRows = this.patient.item.length
    },
    methods: {
        patientFiltered(patientItems) {
            this.patient.totalRows = patientItems.length
            this.patient.currentPage = 1
        },
        changeSection(section) {
            this.active.section = section
        },
        changeTab(tab) {
            this.active.tab = tab
        },
    },
}
</script>
<style scoped>
.card-header:hover {
    cursor: pointer;
}
</style>