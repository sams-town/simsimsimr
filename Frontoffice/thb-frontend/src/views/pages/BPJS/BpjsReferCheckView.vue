<template>
    <div>
        <Layout
            :layout_breadcrumb_items="[
                { text: 'Home', link: '/' },
                { text: 'BPJS' },
                { text: 'Check Rujukan/Surat Kontrol' }
            ]"
        >
            <section class="">
                <div class="d-flex justify-content-between">
                    <div class="row w-100 align-items-end">
                        <div class="col-lg-4">
                            <label class="c-form-label">Faskes</label>
                            <div class="d-flex align-items-center position-relative">
                                <v-select v-model="type_search" aria-readonly="true" :options="OptionsTypeSearch" :reduce="OptionsTypeSearch => OptionsTypeSearch.value" label="title" placeholder="-- Asal Faskes --"></v-select>
                            </div>
                            <label class="c-form-label">NIK</label>
                            <div class="d-flex align-items-center position-relative">
                                <b-input-group>
                                    <b-form-input v-model="nik"  @keyup.enter="getByNoka('nik')"></b-form-input>
                                    <b-input-group-append>
                                        <b-button variant="primary-500" @click="getByNoka('nik')"><feather-icon icon="SearchIcon" /></b-button>
                                    </b-input-group-append>
                                </b-input-group>
                            </div>
                            <label class="c-form-label">Nomor Kartu/Anggota</label>
                            <div class="d-flex align-items-center position-relative">
                                <b-input-group>
                                    <b-form-input v-model="number_insurance"  @keyup.enter="getByNoka()"></b-form-input>
                                    <b-input-group-append>
                                        <b-button variant="primary-500" @click="getByNoka()"><feather-icon icon="SearchIcon" /></b-button>
                                    </b-input-group-append>
                                </b-input-group>
                            </div>
                            <label class="c-form-label">Nomor Rujukan</label>
                            <div class="d-flex align-items-center position-relative">
                                <b-input-group>
                                    <b-form-input v-model="number_reference"  @keyup.enter="getByNoru()"></b-form-input>
                                    <b-input-group-append>
                                        <b-button variant="primary-500" @click="getByNoru()"><feather-icon icon="SearchIcon" /></b-button>
                                    </b-input-group-append>
                                </b-input-group> 
                            </div>
                        </div>
                    </div>
                </div>
                <b-modal v-model="active.refer_bpjs" scrollable size="lg" title="Rujukan BPJS" hide-footer>
                    <section class="card">
                        <div class="card-header rounded border-bottom-0 bg-neutral-100">
                            <div class="row">
                                <div class="col-lg">
                                    <div class="row">
                                        <b-table bordered responsive small head-variant="light" style="height: 200px" :items="refer_bpjs_data" :fields="ReferBpjsTable.fields" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="mt-12 text-center">
                                            <template #cell(tglKunjunganExp)="row">
                                                {{ moment(row.item.tglKunjungan).add(89, 'days').format('YYYY-MM-DD') }}
                                            </template>
                                            <template #cell(action)="row">
                                                <b-button size="sm" variant="primary-500" @click="pickControlTable(row.item)">Pilih</b-button>
                                            </template>
                                        </b-table>
                                        <b-table bordered responsive small head-variant="light" style="height: 200px" :items="refer_control_data" :fields="ReferControlTable.fields" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="mt-12 text-center">
                                            <template #cell(action)="row">
                                                <div>
                                                <b-button size="sm" variant="primary-500" @click="pickNoControl(row.item)">Pilih</b-button>
                                                </div>
                                            </template>
                                        </b-table>
                                        <!-- <b-table bordered responsive small head-variant="light" :items="refer_internal_data" :fields="ReferInternalTable.fields" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="mt-12 text-center">
                                            <template #cell(action)="row">
                                                <div>
                                                <b-button size="sm" variant="primary-500" @click="pickNoInternal(row.item)">Pilih</b-button>
                                                </div>
                                            </template>
                                        </b-table> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </b-modal>
            </section>
        </Layout>
    </div>
</template>
<script>
import moment from 'moment'
import { mapState } from 'vuex'

export default {

    data() {
        return {
            type_search:'KL',
            userData: JSON.parse(localStorage.getItem('userData')),
            ReferControlTable: {
                fields: [
                { key: 'tglRencanaKontrol', label: 'Tgl.Rencana Kontrol' },
                { key: 'noSuratKontrol', label: 'No Surat' },
                { key: 'namaJnsKontrol', label: 'Jenis Kontrol' },
                { key: 'nama', label: 'Nama' },
                { key: 'namaPoliTujuan', label: 'Sub/Specialist' },
                { key: 'namaDokter', label: 'Dokter' },
                ],
            },
            ReferBpjsTable: {
                fields: [
                    { key: 'tglKunjungan', label: 'Tgl. Rujukan' },
                    { key: 'tglKunjunganExp', label: 'Tgl. Rujukan Habis' },
                    { key: 'noKunjungan', label: 'No.Rujukan' },
                    { key: 'keteranganstatusPeserta', label: 'Status.Kartu' },
                    { key: 'nama', label: 'Nama' },
                    { key: 'nmProviderPerujuk', label: 'PPK Perujuk' },
                    { key: 'nama_poli', label: 'Sub/Specialist' },
                    { key: 'nama_poli', label: 'Sub/Specialist' },
                ],
            },
            ReferInternalTable: {
                fields: [
                { key: 'no_refer', label: 'Nomer Rujukan' },
                { key: 'consultation_date', label: 'Tgl Rujukan' },
                { key: 'health_facility_item_name', label: 'Poliklinik' },
                { key: 'doctor_name', label: 'Dokter' },
                ],
            },
            nik:'',
            number_insurance:'',
            number_reference:'',
            dataPeserta:{},
            refer_internal_data:{},
            refer_bpjs_data:{},
            refer_control_data:{},
            active: {
                refer_bpjs:false,
                is_bpjs:false
            },
            OptionsTypeSearch:[
                {'title':'Faskes Tingkat 1', 'value':'KL'},
                {'title':'Faskes Tingkat 2', 'value':'RS'},
            ]
        }
    },
    computed: {
        ...mapState({ 
            // OptionsHealthFacilityItem: state => state.healthfacilityitem.options.healthfacilityitem,
        }),
    },
    watch: {
    },
    methods: {
        moment,
        getByNoru () {
            this.$store.dispatch('patient/bpjs', {url:'Rujukan',idx:this.number_reference,'type_search':this.type_search})
            .then(response => {
                this.number_insurance = response.data[0].noKartu
                if (response.data[0].code === '200') {
                    alert(' No.Rujukan: '+response.data[0].noKunjungan+' Tgl.Rujukan: '+response.data[0].tglKunjungan)
                } else {
                    alert('Surat rujukan ini masa berlaku habis, maksimal 3(tiga) bulan dari tanggal rujukan. silahkan ke faskes perujuk untuk perbarui rujukan')
                }
            })
        },
        getByNoka (by) {
            let nokanik = 'noka'
            if (by) {
                nokanik = 'nik'
            }
            this.$store.dispatch('patient/bpjs', {url:nokanik,idx:by ? this.nik : this.number_insurance,'type_search':this.type_search})
            .then(response => {
                if (response.metaData.code === "200") {
                    alert(response.response.peserta.statusPeserta.keterangan);
                    this.dataPeserta = response.response
                    console.log(this.dataPeserta)
                } else {
                    alert(response.metaData.message);
                }
                this.refer_bpjs_data = []
                this.refer_control_data = []
                this.$store.dispatch('patient/bpjs', {url:'RujukanKontrolList',idx:response.response.peserta.noKartu})
                .then(response_refer => {
                    if (response_refer.status == 201) {
                    alert(response_refer.message)
                    } else {
                    this.refer_control_data = response_refer.data.response.list
                    }
                })
                this.refer_internal_data = []
                this.$store.dispatch('registration/getReferInternalByNorm', response.response.peserta.mr.noMR)
                .then(response_refer => {
                    console.log(response_refer)
                    if (response_refer) {
                        this.refer_internal_data = response_refer
                    }
                })
                this.$store.dispatch('patient/bpjs', {url:'RujukanList',idx:this.number_insurance,'type_search':this.type_search})
                .then(response_refer => {
                    console.log(response_refer)
                    if (response_refer.status == 201) {
                        alert(response_refer.message)
                    } else {
                        this.refer_bpjs_data = response_refer.data
                    }
                })
                this.active.refer_bpjs = true
            })
        },
    },
    mounted() {
    },
    created() {
    }

}
</script>