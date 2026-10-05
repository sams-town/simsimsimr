<template>
    <div>
        <topMenu selectedMenu="menu8" class="mt-2" />
        <div class="mt-28 p-16 bg-white shadow-sm">
            <div class="col px-0">
                <p class="mb-0 text-bold-24">Pendaftaran Eksternal</p>
            </div>
            <div class="row mx-0 mt-12 mt-lg-20">
                <div class="col-12 col-lg-3 px-0">
                    <b-form-input class="rounded-pill" placeholder="cari nomor rekam medis"></b-form-input>
                </div>
                <div class="col-12 col-lg mt-16 mt-lg-0">
                    <div class="d-flex flex-column flex-lg-row justify-content-start justify-content-lg-end">
                        <b-button pill variant="outline-primary" size="sm" @click="modal.search = !modal.search">
                            <b-icon icon="arrow-left" /> Cari Pasien
                        </b-button>
                        <b-button pill variant="outline-primary" size="sm" class="mx-lg-20 mt-8 mt-lg-0"
                            @click="modal.external = !modal.external">
                            <b-icon icon="arrow-left" /> Cari Pasien Eksternal
                        </b-button>
                        <b-button pill variant="outline-primary" size="sm" @click="modal.import = !modal.import"
                            class="mt-8 mt-lg-0">
                            <b-icon icon="arrow-left" /> Import Pasien Eksternal
                        </b-button>
                    </div>
                </div>
            </div>
        </div>
        <div class="px-32 py-16 bg-primary-300 mt-24 rounded">
            <p class="mb-0 text-semi-16">Pendaftaran Pasien Eksternal</p>
        </div>
        <div class="col mt-20">
            <div class="card">
                <div class="card-header bg-primary-100 pointer" @click="expand.entry = !expand.entry">
                    <div class="row mx-0">
                        <p class="text-semi-16 mb-0 mr-auto">Cara Masuk</p>
                        <b-icon icon="chevron-down" />
                    </div>
                </div>
                <div class="card-body" v-if="expand.entry == true">
                    <label class="c-form-label">Pilih Cara Masuk</label>
                    <b-form-select></b-form-select>
                </div>
            </div>
            <div class="card mt-16">
                <div class="card-header bg-primary-100 pointer" @click="expand.payment = !expand.payment">
                    <div class="row mx-0">
                        <p class="text-semi-16 mb-0 mr-auto">Cara Bayar</p>
                        <b-icon icon="chevron-down" />
                    </div>
                </div>
                <div class="card-body" v-if="expand.payment == true">
                    <div>
                        <label class="c-form-label">Pilih Cara Bayar</label>
                        <b-form-select :options="option_payment" v-model="option_payment.value"></b-form-select>
                    </div>
                    <div v-if="option_payment.value === 'BPJS' || option_payment.value === 'Asuransi'">
                        <b-form-select :options="option_bpjs.company" v-model="selected_bpjs.company"
                            class="mt-12"></b-form-select>
                        <b-form-select :options="option_bpjs.sub" v-model="selected_bpjs.sub" class="mt-12"></b-form-select>
                        <b-form-input type="number" class="mt-12" placeholder="No Peserta"></b-form-input>
                        <b-form-input type="text" class="mt-12" placeholder="Nama Peserta"></b-form-input>
                        <b-form-input type="text" class="mt-12" placeholder="Golongan Peserta"></b-form-input>
                        <b-form-select :options="option_bpjs.class" v-model="selected_bpjs.class"
                            class="mt-12"></b-form-select>
                    </div>
                    <div v-if="option_payment.value === 'Perusahaan'">
                        <b-form-select :options="option_company.company" v-model="selected_company.company"
                            class="mt-12"></b-form-select>
                        <b-form-select :options="option_company.sub" v-model="selected_company.sub"
                            class="mt-12"></b-form-select>
                    </div>
                </div>
            </div>
            <div class="card mt-16">
                <div class="card-header bg-primary-100 pointer" @click="expand.poli = !expand.poli">
                    <div class="row mx-0">
                        <p class="text-semi-16 mb-0 mr-auto">Poli & Dokter</p>
                        <b-icon icon="chevron-down" />
                    </div>
                </div>
                <div class="card-body" v-if="expand.poli == true">
                    <div class="row">
                        <div class="col-lg-5">
                            <label class="c-form-label">Pilih Poli</label>
                            <b-form-select v-model="poli.poli"></b-form-select>
                        </div>
                        <div class="col-lg-5">
                            <label class="c-form-label">Pilih Dokter</label>
                            <b-form-select v-model="poli.doctor"></b-form-select>
                        </div>
                        <div v-if="poli.poli && poli.doctor" class="d-flex flex-column justify-content-end">
                            <b-button size="sm" class="bg-neutral-200">
                                <b-icon icon="x-square-fill" style="color:#EB5757" class="bg-white" />
                            </b-button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mt-16">
                <div class="card-header bg-primary-100 pointer" @click="expand.rs = !expand.rs">
                    <div class="row mx-0">
                        <p class="text-semi-16 mb-0 mr-auto">Paket RS</p>
                        <b-icon icon="chevron-down" />
                    </div>
                </div>
                <div class="card-body" v-if="expand.rs == true">
                    <div>
                        <label class="c-form-label">Pilih Paket RS</label>
                        <b-form-select></b-form-select>
                    </div>
                </div>
            </div>
            <div class="card mt-16">
                <div class="card-header bg-primary-100 pointer" @click="expand.patient = !expand.patient">
                    <div class="row mx-0">
                        <p class="text-semi-16 mb-0 mr-auto">Data Pasien</p>
                        <b-icon icon="chevron-down" />
                    </div>
                </div>
                <div class="card-body" v-if="expand.patient == true">
                    <div class="row">
                        <div class="col-12 col-lg-4">
                            <label class="c-form-label">No RM</label>
                            <b-form-input type="text"></b-form-input>
                        </div>
                        <div class="col-12 col-lg-4">
                            <label class="c-form-label">Jenis Kelamin</label>
                            <b-form-select></b-form-select>
                        </div>
                        <div class="col-12 col-lg-4">
                            <label class="c-form-label">Satus Kawin</label>
                            <b-form-select></b-form-select>
                        </div>
                    </div>
                    <div class="row mt-12">
                        <div class="col-12 col-lg-4">
                            <label class="c-form-label">No Identitas</label>
                            <b-form-input type="number"></b-form-input>
                        </div>
                        <div class="col-12 col-lg-4">
                            <label class="c-form-label">Tempat Lahir</label>
                            <b-form-input type="text"></b-form-input>
                        </div>
                        <div class="col-12 col-lg-4">
                            <label class="c-form-label">No Telepon</label>
                            <b-form-input type="number"></b-form-input>
                        </div>
                    </div>
                    <div class="row mt-12">
                        <div class="col-12 col-lg-4">
                            <label class="c-form-label">Nama</label>
                            <b-form-input type="text"></b-form-input>
                        </div>
                        <div class="col-12 col-lg-4">
                            <label class="c-form-label">Tanggal Lahir</label>
                            <b-form-input type="date"></b-form-input>
                        </div>
                        <div class="col-12 col-lg-4">
                            <label class="c-form-label">Alamat</label>
                            <b-form-input type="text"></b-form-input>
                        </div>
                    </div>
                    <div class="d-flex mt-20">
                        <b-button variant="primary" pill class="ml-auto">
                            <img src="@/@core/assets/icons/registration/save.svg" alt="">
                            Simpan
                        </b-button>
                    </div>
                </div>
            </div>
        </div>
        <b-modal v-model="modal.search" size="xl" title="Cari Pasien" hide-footer>
            <div class="row">
                <div class="col-12 col-lg">
                    <p class="mb-0 text-bold-18">Pencarian Calon Pasien External</p>
                </div>
                <div class="col-12 col-lg-3 d-flex mt-12 mt-lg-0">
                    <b-button pill size="sm" variant="outline-primary" class="ml-auto">Clear Filter</b-button>
                </div>
            </div>
            <div class="mt-24">
                <div class="row">
                    <div class="col-12 col-lg-4">
                        <label class="c-form-label">No Rekam Medis</label>
                        <b-form-input type="number" placeholder="No Rekam Medis"></b-form-input>
                    </div>
                    <div class="col-12 col-lg-4">
                        <label class="c-form-label">Nama</label>
                        <b-form-input type="text" placeholder="Nama"></b-form-input>
                    </div>
                    <div class="col-12 col-lg-4">
                        <label class="c-form-label">Identitas</label>
                        <b-form-input type="text" placeholder="Nomor Identitas"></b-form-input>
                    </div>
                </div>
                <div class="row mt-10">
                    <div class="col-12 col-lg-4">
                        <label class="c-form-label">Tempat Lahir</label>
                        <b-form-input type="text" placeholder="Tempat Lahir"></b-form-input>
                    </div>
                    <div class="col-12 col-lg-4">
                        <label class="c-form-label">Tanggal Lahir</label>
                        <b-form-input type="date"></b-form-input>
                    </div>
                </div>
                <div class="row mt-10">
                    <div class="col-12 col-lg-4">
                        <label class="c-form-label">Alamat</label>
                        <b-form-input type="text" placeholder="Nama Jalan"></b-form-input>
                    </div>
                    <div class="col-12 col-lg-4">
                        <label class="c-form-label">RT/RW</label>
                        <b-form-input type="number" placeholder="RT/RW"></b-form-input>
                    </div>
                    <div class="col-12 col-lg-4">
                        <label class="c-form-label">Kelurahan</label>
                        <b-form-input type="text" placeholder="Kelurahan"></b-form-input>
                    </div>
                </div>
                <div class="row mt-10">
                    <div class="col-12 col-lg-4">
                        <label class="c-form-label">Kecamatan</label>
                        <b-form-input type="text" placeholder="Kecamatan"></b-form-input>
                    </div>
                    <div class="col-12 col-lg-4">
                        <label class="c-form-label">Kotamadya</label>
                        <b-form-input type="text" placeholder="Kotamadya"></b-form-input>
                    </div>
                    <div class="col-12 col-lg-4">
                        <label class="c-form-label">Provinsi</label>
                        <b-form-input type="text" placeholder="Provinsi"></b-form-input>
                    </div>
                </div>
            </div>
            <div class="mt-32 mt-lg-40">
                <div class="row mx-0">
                    <span class="text-regular-14">Tampilkan</span>
                    <select id="per-page-select" v-model="search.perPage" class="px-8 border-left-0 border-right-0 border-top-0 mx-2">
                        <option value="5" selected>5</option>
                        <option value="10" selected>10</option>
                        <option value="15" selected>15</option>
                    </select>
                    <span class="text-regular-14">Entri</span>
                </div>
                <b-table hover :fields="search.field" :items="search.item" :current-page="search.currentPage" @filtered="SearchFiltered" :per-page="search.perPage" class="mt-12">
                    <template #cell(action)>
                        <b-button size="sm" variant="info-400">Daftarkan</b-button>
                    </template>
                </b-table>
                <div class="col px-0 mt-20">
                    <div class="d-flex">
                        <span class="text-regular-14">Menampilkan 1 hingga 10 dari 200 entri</span>
                        <div class="ml-auto">
                            <b-pagination v-model="search.currentPage" :total-rows="search.totalRows" :per-page="search.perPage" :current-page="search.currentPage"  align="fill"
                                size="sm" class="my-0"></b-pagination>
                        </div>
                    </div>
                </div>
            </div>
        </b-modal>
        <b-modal v-model="modal.external" size="xl" title="Cari Pasien Eksternal" hide-footer>
            <div>
                <p class="mb-0 text-bold-18">Pencarian Calon Pasien External</p>
            </div>
            <div class="mt-24">
                <div class="row">
                    <div class="col-12 col-lg-4">
                        <label class="c-form-label">Nama Pasien</label>
                        <b-form-input type="text" placeholder="Nama Pasien"></b-form-input>
                    </div>
                    <div class="col-12 col-lg-4">
                        <label class="c-form-label">No Identitas</label>
                        <b-form-input type="number" placeholder="No Identitas"></b-form-input>
                    </div>
                </div>
                <div class="row mt-10">
                    <div class="col-12 col-lg-4">
                        <label class="c-form-label">Tempat Lahir</label>
                        <b-form-input type="number" placeholder="Tempat Lahir"></b-form-input>
                    </div>
                    <div class="col-12 col-lg-4">
                        <label class="c-form-label">Tanggal Lahir</label>
                        <b-form-input type="date"></b-form-input>
                    </div>
                </div>
                <div class="row mt-10">
                    <div class="col-12 col-lg-4">
                        <label class="c-form-label">Alamat</label>
                        <b-form-input type="text" placeholder="Nama Jalan"></b-form-input>
                    </div>
                    <div class="col-12 col-lg-4">
                        <label class="c-form-label">RT/RW</label>
                        <b-form-input type="number" placeholder="RT/RW"></b-form-input>
                    </div>
                    <div class="col-12 col-lg-4">
                        <label class="c-form-label">Kelurahan</label>
                        <b-form-input type="text" placeholder="Kelurahan"></b-form-input>
                    </div>
                </div>
                <div class="row mt-10">
                    <div class="col-12 col-lg-4">
                        <label class="c-form-label">Kecamatan</label>
                        <b-form-input type="text" placeholder="Kecamatan"></b-form-input>
                    </div>
                    <div class="col-12 col-lg-4">
                        <label class="c-form-label">Kotamadya</label>
                        <b-form-input type="text" placeholder="Kotamadya"></b-form-input>
                    </div>
                    <div class="col-12 col-lg-4">
                        <label class="c-form-label">Provinsi</label>
                        <b-form-input type="text" placeholder="Provinsi"></b-form-input>
                    </div>
                </div>
            </div>
            <div class="mt-32 mt-lg-40">
                <div class="row mx-0">
                    <span class="text-regular-14">Tampilkan</span>
                    <select id="per-page-select" v-model="external.perPage" class="px-8 border-left-0 border-right-0 border-top-0 mx-2">
                        <option value="5" selected>5</option>
                        <option value="10" selected>10</option>
                        <option value="15" selected>15</option>
                    </select>
                    <span class="text-regular-14">Entri</span>
                </div>
                <b-table responsive hover :fields="external.field" :items="external.item" :current-page="external.currentPage" @filtered="ExternalFiltered" :per-page="external.perPage"
                    class="mt-12">
                    <template #cell(action)>
                        <b-button size="sm" variant="info-400"> Daftarkan</b-button>
                    </template>
                </b-table>
                <div class="col px-0 mt-20">
                    <div class="d-flex">
                        <span class="text-regular-14">Menampilkan 1 hingga 10 dari 200 entri</span>
                        <div class="ml-auto">
                            <b-pagination v-model="external.currentPage" :total-rows="external.totalRows" :per-page="external.perPage" :current-page="external.currentPage"  align="fill"
                                size="sm" class="my-0"></b-pagination>
                        </div>
                    </div>
                </div>
            </div>
        </b-modal>
        <b-modal v-model="modal.import" size="xl" title="Calon Pasien Eksternal" hide-footer>
            <div class="row mx-0">
                <div class="col-12 col-lg px-0">
                    <p class="mb-0 text-bold-18">Pencarian Calon Pasien External</p>
                </div>
                <div class="col-12 col-lg-4 px-0">
                    <div class="d-flex justify-content-end mt-12 mt-lg-0s">
                        <b-button pill variant="outline-primary" size="sm" class="mr-2 col-lg-4">
                            <b-icon icon="bootstrap-fill" /> Import Data
                        </b-button>
                        <b-button pill variant="outline-primary" size="sm" class="col-lg-4">Clear Data</b-button>
                    </div>
                </div>
            </div>
            <div class="row mt-24">
                <div class="col-12 col-lg-4">
                    <label class="c-form-label">Nama Pendaftaran / Keterangan</label>
                    <b-form-input type="text" placeholder="Nama Pendaftaran / Keterangan"></b-form-input>
                </div>
                <div class="col-12 col-lg-4">
                    <label class="c-form-label">Waktu Daftar</label>
                    <b-form-input type="date"></b-form-input>
                </div>
                <div class="col-12 col-lg-4">
                    <label class="c-form-label">Sd</label>
                    <b-form-input type="date"></b-form-input>
                </div>
            </div>
            <div class="mt-32 mt-lg-40">
                <div class="row mx-0">
                    <span class="text-regular-14">Tampilkan</span>
                    <select id="per-page-select" v-model="impor.perPage" class="px-8 border-left-0 border-right-0 border-top-0 mx-2">
                        <option value="5" selected>5</option>
                        <option value="10" selected>10</option>
                        <option value="15" selected>15</option>
                    </select>
                    <span class="text-regular-14">Entri</span>
                </div>
                <b-table hover :fields="impor.field" :items="impor.item" :current-page="impor.currentPage" @filtered="ImporFiltered" :per-page="impor.perPage" class="mt-12"></b-table>
                <div class="col px-0 mt-20">
                    <div class="d-flex">
                        <span class="text-regular-14">Menampilkan 1 hingga 10 dari 200 entri</span>
                        <div class="ml-auto">
                            <b-pagination v-model="impor.currentPage" :total-rows="impor.totalRows" :per-page="impor.perPage" :current-page="impor.currentPage"  align="fill"
                                size="sm" class="my-0"></b-pagination>
                        </div>
                    </div>
                </div>
            </div>
        </b-modal>
    </div>
</template>

<script>
import topMenu from '@/@core/components/top-menu/topMenu.vue'

export default {
    components: { topMenu },
    data() {
        return {
            option_payment: [
                { text: 'BPJS', value: 'BPJS' },
                { text: 'Perusahaan', value: 'Perusahaan' },
                { text: 'Asuransi', value: 'Asuransi' },
            ],
            option_bpjs: {
                company: [
                    { text: 'Silakan pilih perusahaan', value: null },
                ],
                sub: [
                    { text: 'Silakan pilih cara bayar sub', value: null },
                ],
                class: [
                    { text: 'Kelas 1 Utama', value: 'kelas 1' },
                ],
            },
            selected_bpjs: {
                company: null,
                sub: null,
                class: 'kelas 1',
            },
            option_company: {
                company: [
                    { text: 'Silakan pilih perusahaan', value: null },
                ],
                sub: [
                    { text: 'Silakan pilih cara bayar sub', value: null },
                ],
            },
            selected_company: {
                company: null,
                sub: null,
            },
            poli: {
                poli: '',
                doctor: '',
            },
            impor: {
                field: [
                    { key: 'file_name', label: 'Nama File', sortable: true },
                    { key: 'date_register', label: 'Tanggal Daftar', sortable: true },
                    { key: 'date_added', label: 'Date Added', sortable: true },
                    { key: 'action', label: 'Aksi', sortable: true },
                ],
                item: [
                    { file_name: '1', date_register: '-', date_added: '-', action: '-' },
                    { file_name: '2', date_register: '-', date_added: '-', action: '-' },
                    { file_name: '3', date_register: '-', date_added: '-', action: '-' },
                    { file_name: '4', date_register: '-', date_added: '-', action: '-' },
                    { file_name: '5', date_register: '-', date_added: '-', action: '-' },
                    { file_name: '6', date_register: '-', date_added: '-', action: '-' },
                ],
                currentPage: 1,
                totalRows: 1,
                perPage: 5,
            },
            external: {
                field: [
                    { key: 'no_rm', label: 'No RM', sortable: true },
                    { key: 'name', label: 'Nama', sortable: true },
                    { key: 'birth', label: 'Tanggal Lahir', sortable: true },
                    { key: 'ktp', label: 'No KTP', sortable: true },
                    { key: 'address', label: 'Alamat', sortable: true },
                    { key: 'update', label: 'Last Update', sortable: true },
                    { key: 'action', label: 'Aksi' },
                ],
                item: [
                    { no_rm: '1', name: '-', birth: '-', ktp: '-', address: '-', update: '-' },
                    { no_rm: '2', name: '-', birth: '-', ktp: '-', address: '-', update: '-' },
                    { no_rm: '3', name: '-', birth: '-', ktp: '-', address: '-', update: '-' },
                    { no_rm: '4', name: '-', birth: '-', ktp: '-', address: '-', update: '-' },
                    { no_rm: '5', name: '-', birth: '-', ktp: '-', address: '-', update: '-' },
                    { no_rm: '6', name: '-', birth: '-', ktp: '-', address: '-', update: '-' },
                ],
                currentPage: 1,
                totalRows: 1,
                perPage: 5,
            },
            search: {
                field: [
                    { key: 'no_rm', label: 'No RM', sortable: true },
                    { key: 'name', label: 'Nama', sortable: true },
                    { key: 'birth', label: 'Tanggal Lahir', sortable: true },
                    { key: 'place', label: 'Tempat Lahir', sortable: true },
                    { key: 'ktp', label: 'No KTP', sortable: true },
                    { key: 'address', label: 'Alamat', sortable: true },
                    { key: 'update', label: 'Last Update', sortable: true },
                    { key: 'action', label: 'Aksi' },
                ],
                item: [
                    { no_rm: '1', name: '-', birth: '-', place: '-', ktp: '-', address: '-', update: '-' },
                    { no_rm: '2', name: '-', birth: '-', place: '-', ktp: '-', address: '-', update: '-' },
                    { no_rm: '3', name: '-', birth: '-', place: '-', ktp: '-', address: '-', update: '-' },
                    { no_rm: '4', name: '-', birth: '-', place: '-', ktp: '-', address: '-', update: '-' },
                    { no_rm: '5', name: '-', birth: '-', place: '-', ktp: '-', address: '-', update: '-' },
                    { no_rm: '6', name: '-', birth: '-', place: '-', ktp: '-', address: '-', update: '-' },
                ],
                currentPage: 1,
                totalRows: 1,
                perPage: 5,
            },
            expand: {
                entry: true,
                payment: true,
                poli: true,
                rs: true,
                patient: true,
            },
            modal: {
                search: false,
                external: false,
                import: false,
            }
        }
    },
    mounted() {
        this.external.totalRows = this.external.item.length
        this.impor.totalRows = this.impor.item.length
        this.search.totalRows = this.search.item.length
    },
    methods: {
        ExternalFiltered(externalItems) {
            this.external.totalRows = externalItems.length
            this.external.currentPage = 1
        },
        ImporFiltered(imporItems) {
            this.impor.totalRows = imporItems.length
            this.impor.currentPage = 1
        },
        SearchFiltered(searchItems) {
            this.search.totalRows = searchItems.length
            this.search.currentPage = 1
        }
    },

}
</script>