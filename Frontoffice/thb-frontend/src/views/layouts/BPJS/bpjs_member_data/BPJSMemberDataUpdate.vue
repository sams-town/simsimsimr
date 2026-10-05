<template>
    <div>
        <div class="my-16 my-lg-24">
          <span class="px-16 text-bold-24 text-neutral-700">Surat Eligibilitas Peserta</span>
          <!-- <span class="px-16 text-bold-24 text-neutral-700">Surat Eligibilitas Peserta : 0301R01690444513 (Rawat Jalan)</span> -->
        </div>
        <div class="card-body shadow-sm rounded">
            <div class="row mx-0">
                <div class="col-12 col-lg">
                    <div class="row">
                        <div class="col">
                            <!-- <div class="text-medium-18">Surat Eligibilitas Peserta : 0301R01690444513 (Rawat Jalan)</div> -->
                            <div class="row mt-10">
                                <section class="col-lg-3">
                                    <div class="label-head">
                                        <span class="px-16 text-bold-24 text-neutral-700">Data Peserta</span>
                                    </div>
                                    <b-list-group class="mt-16 text-uppercase">
                                        <b-list-group-item style="border-bottom: 2px solid rgb(231, 231, 231);">
                                            <feather-icon icon="UserIcon" class="mr-2" /> BINTANG (L)
                                        </b-list-group-item>
                                        <b-list-group-item style="border-bottom: 2px solid rgb(231, 231, 231);">
                                            <feather-icon icon="ClipboardIcon" class="mr-2" /> 3604026111870001
                                        </b-list-group-item>
                                        <b-list-group-item style="border-bottom: 2px solid rgb(231, 231, 231);">
                                            <feather-icon icon="CalendarIcon" class="mr-2" /> 1989-11-15
                                        </b-list-group-item>
                                        <b-list-group-item style="border-bottom: 2px solid rgb(231, 231, 231);">
                                            <feather-icon icon="FolderPlusIcon" class="mr-2" /> KELAS III
                                        </b-list-group-item>
                                        <b-list-group-item style="border-bottom: 2px solid rgb(231, 231, 231);">
                                            <feather-icon icon="BriefcaseIcon" class="mr-2" /> BOJONG ( 10070801 )
                                        </b-list-group-item>
                                        <b-list-group-item style="border-bottom: 2px solid rgb(231, 231, 231);">
                                            <feather-icon icon="CalendarIcon" class="mr-2" /> 2023-01-19 s.d 2050-01-01
                                        </b-list-group-item>
                                        <b-list-group-item style="border-bottom: 2px solid rgb(231, 231, 231);">
                                            <feather-icon icon="CalendarIcon" class="mr-2" /> PEKERJA MANDIRI
                                        </b-list-group-item>
                                    </b-list-group>
                                </section>
                                <section class="col-lg-9 mt-32 mt-lg-0">
                                    <div class="label-head ml-10">
                                        <span class="px-16 text-bold-24 text-neutral-700">Data Peserta</span>
                                    </div>
                                    <div class="d-flex justify-content-center">
                                        <div class="col-lg-12">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div>
                                                        <label class="c-form-label">Spesialis/SubSpesialis <span class="text-danger-500">*</span></label>
                                                        <b-input-group>
                                                            <b-input-group-prepend is-text>
                                                                <b-form-checkbox readonly>
                                                                    <span>Eksekutif</span>
                                                                </b-form-checkbox>
                                                            </b-input-group-prepend>
                                                            <b-form-input v-model="form.poli_destination" readonly></b-form-input>
                                                        </b-input-group>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="mt-12">
                                                        <label class="c-form-label">Tanggal SEP</label>
                                                        <b-form-input v-model="form.sep_date" type="date"></b-form-input>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="mt-12">
                                                        <label class="c-form-label">No MR <span class="text-danger-500">*</span></label>
                                                        <b-input-group>
                                                            <b-form-input v-model="form.norm"></b-form-input>
                                                            <b-input-group-append is-text>
                                                                <b-form-checkbox>
                                                                    <span>Peserta COB</span>
                                                                </b-form-checkbox>
                                                            </b-input-group-append>
                                                        </b-input-group>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12" style="display:none">
                                                    <div>
                                                        <label class="c-form-label">Asal Rujukan</label>
                                                        <b-form-select v-model="form.reference_origin"></b-form-select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12" style="display:none">
                                                    <div class="mt-12">
                                                        <label class="c-form-label">Tanggal Rujukan</label>
                                                        <b-form-input v-model="form.reference_date" type="date"></b-form-input>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12" style="display:none">
                                                    <div class="mt-12">
                                                        <label class="c-form-label">No Rujukan <span class="text-danger-500">*</span></label>
                                                        <b-form-input v-model="form.reference_number"></b-form-input>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12" style="display:none">
                                                    <div class="mt-12">
                                                        <label class="c-form-label">PPK Asal Rujukan <span class="text-danger-500">*</span></label>
                                                        <b-form-input v-model="form.ppk_origin"></b-form-input>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12" style="display:none">
                                                    <div class="mt-12">
                                                        <label class="c-form-label">Kontrol</label>
                                                        <b-form-checkbox v-model="form.control">Ya</b-form-checkbox>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="mt-12">
                                                        <label class="c-form-label">DPJP <span class="text-danger-500">*</span></label>
                                                        <b-form-select v-model="form.diagnosis"></b-form-select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="mt-12">
                                                        <label class="c-form-label">Diagnosa <span class="text-danger-500">*</span></label>
                                                        <b-form-select v-model="form.diagnosis"></b-form-select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="mt-12">
                                                        <label class="c-form-label">No Telepon <span class="text-danger-500">*</span></label>
                                                        <b-form-input v-model="form.phone_number"></b-form-input>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="mt-12">
                                                        <label class="c-form-label">Catatan</label>
                                                        <b-textarea v-model="form.note"></b-textarea>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="mt-12">
                                                        <label class="c-form-label">Tujuan Kunjungan <span class="text-danger-500">*</span></label>
                                                        <b-form-select v-model="form.diagnosis"></b-form-select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="mt-12">
                                                        <label class="c-form-label">Jaminan <span class="text-danger-500">*</span></label>
                                                        <b-form-select v-model="form.diagnosis"></b-form-select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                                <section class="mt-32 row mx-0">
                                    <b-button variant="info-600" class="m-1">Edit</b-button>
                                    <b-button variant="info-600" class="m-1">Edit Tanggal Pulang</b-button>
                                    <b-button variant="danger-600" class="m-1">Hapus</b-button>
                                    <!-- <router-link :to="{ name: 'PrintsBpjsMemberView' }"> -->
                                        <b-button variant="warning-500" class="m-1">
                                            <feather-icon icon="PrinterIcon" class="m-1" /> Cetak
                                        </b-button>
                                    <!-- </router-link> -->
                                    <b-button variant="warning-500" class="m-1">
                                        <feather-icon icon="PrinterIcon" class="m-1" /> Cetakan ke 1
                                    </b-button>
                                    <b-button variant="outline-primary-500" class="m-1">Kembali</b-button>
                                </section>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import vSelect from 'vue-select'
export default {
    components: {
      vSelect, ToastificationContent
    },
    data() {
        return {
            regData:[],
            form: {
                poli_destination: '',
                reference_origin: '',
                ppk_origin: '',
                reference_date: '',
                reference_number: '',
                sep_date: '',
                norm: '',
                control: '',
                diagnosis: '',
                phone_number: '',
                note: '',
                accident: '',
            }
        }
    },
    methods: {
        notification(a, b, c, d) {
            this.$toast({
                component: ToastificationContent,
                position: 'top-right',
                props: { title: a, icon: b, variant: c, text: d }
            })
        },
        async getData(id) {
            try {
                const response = await this.$store.dispatch('registration/getById', id)
                this.regData = response[0]
                console.log(response[0])
                thus.form.poli_destination = response[0].health_facility_item_name
                thus.form.norm = response[0].norm
                // thus.form.ppk_origin = response[0].
                // thus.form.reference_date = response[0].
                // thus.form.reference_number = response[0].
                // thus.form.sep_date = response[0].
                // thus.form.control = response[0].
                // thus.form.diagnosis = response[0].
                // thus.form.phone_number = response[0].
                // thus.form.note = response[0].
                // thus.form.accident = response[0].
            } catch (error) {
                this.notification('Failed', 'CloseIcon', 'danger', 'Something Wrong!');
            }
        }
    },
    mounted() {
        if (this.$route.params.id) {
            // this.show = true
            this.id = this.$route.params.id
            this.getData(this.$route.params.id)
        }
    }
}
</script>
<style lang="scss">
@import "@core/scss/vue/libs/vue-select.scss";
</style>
<style>
.label-head::before {
    content: '';
    border-left: 6px solid #648DE1;
}
</style>