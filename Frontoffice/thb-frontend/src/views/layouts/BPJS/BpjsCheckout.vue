<template>
    <div>
      <!-- <div class="label-head my-16 my-lg-24">
        <span class="px-16 text-bold-24 text-neutral-700">BPJS</span>
      </div> -->
      <!-- ////////  -->
      <div class="my-16 my-lg-24">
        <span class="px-16 text-bold-24 text-neutral-700">Update Tanggal Pulang</span>
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
                                          <feather-icon icon="UserIcon" class="mr-2" /> {{ pesertaData?.nama }} ({{ pesertaData?.sex }})
                                      </b-list-group-item>
                                      <b-list-group-item style="border-bottom: 2px solid rgb(231, 231, 231);">
                                          <feather-icon icon="ClipboardIcon" class="mr-2" /> {{ pesertaData?.nik }}
                                      </b-list-group-item>
                                      <b-list-group-item style="border-bottom: 2px solid rgb(231, 231, 231);">
                                          <feather-icon icon="CalendarIcon" class="mr-2" /> {{ pesertaData?.tglLahir }}
                                      </b-list-group-item>
                                      <b-list-group-item style="border-bottom: 2px solid rgb(231, 231, 231);">
                                          <feather-icon icon="FolderPlusIcon" class="mr-2" /> {{ pesertaData?.hakKelas?.keterangan }}
                                      </b-list-group-item>
                                      <b-list-group-item style="border-bottom: 2px solid rgb(231, 231, 231);">
                                          <feather-icon icon="BriefcaseIcon" class="mr-2" /> {{  pesertaData?.provUmum?.nmProvider }} ( {{pesertaData?.provUmum?.kdProvider}} )
                                      </b-list-group-item>
                                      <b-list-group-item style="border-bottom: 2px solid rgb(231, 231, 231);">
                                          <feather-icon icon="CalendarIcon" class="mr-2" /> {{ pesertaData?.tglTMT }} s.d {{pesertaData?.tglTAT}}
                                      </b-list-group-item>
                                      <b-list-group-item style="border-bottom: 2px solid rgb(231, 231, 231);">
                                          <feather-icon icon="CalendarIcon" class="mr-2" /> {{  pesertaData?.jenisPeserta?.keterangan }}
                                      </b-list-group-item>
                                  </b-list-group>
                              </section>
                              <section class="col-lg-9 mt-32 mt-lg-0">
                                  <div class="label-head ml-10">
                                    <span class="px-16 text-bold-24 text-neutral-700">Update Tanggal Pulang SEP : {{ this.pesertaSep.noSep }}</span>
                                  </div>
                                  <div class="d-flex justify-content-center">
                                    <div class="col-lg-12">
                                      <div class="row">
                                        <div class="col-lg-12">
                                          <div>
                                            <label class="c-form-label">Status Pulang <span class="text-danger-500">*</span></label>
                                            <v-select v-model="form.statusPulang" :options="OptionsPulang" :reduce="OptionsPulang => OptionsPulang.value" label="title" placeholder="-- Status Pulang --"></v-select>
                                          </div>
                                        </div>
                                        <template v-if="form.statusPulang == '4'">
                                          <div class="col-lg-12">
                                            <div class="mt-12">
                                              <label class="c-form-label">Tgl.Meningal</label>
                                              <b-form-input v-model="form.tglMeninggal" type="date"></b-form-input>
                                            </div>
                                          </div>
                                          <div class="col-lg-12">
                                            <div class="mt-12">
                                              <label class="c-form-label">No. Surat Meninggal</label>
                                              <b-form-input v-model="form.noSuratMeninggal"></b-form-input>
                                            </div>
                                          </div>
                                        </template>
                                        <div class="col-lg-12">
                                          <div class="mt-12">
                                            <label class="c-form-label">Tgl.Pulang</label>
                                            <b-form-input v-model="form.tglPulang" @input="validateCheckout" type="date"></b-form-input>
                                          </div>
                                        </div>
                                        <div class="col-lg-12">
                                          <div class="mt-12">
                                            <label class="c-form-label">No Laporan Manual <span class="text-danger-500">*</span></label>
                                            <b-form-input v-model="form.noLPManual" type="text"></b-form-input>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                              </section>

                              <b-modal v-model="active.specialist_modal" title="Initial Title" size="lg" scrollable hide-footer>

                                <b-table :fields="fieldsPoli" :items="refPoliData" responsive>
                                  <template #cell(kodePoli)="row">
                                    <b-button size="sm" @click="toggleRefDoctor(row)">{{ row.value }}</b-button>
                                  </template>

                                  <template #row-details="row">
                                    <b-card>

                                      <b-table :fields="fieldsDokter" :items="row.item.refDokter" responsive >
                                        <template #cell(action)="row">
                                          <b-button variant="success" size="sm" @click="fillForm(row)">
                                            <b-icon icon="check-lg"></b-icon>
                                          </b-button>
                                        </template>
                                      </b-table>
                                        
                                    </b-card>
                                  </template>
                                </b-table>
                              </b-modal>
                              <section class="mt-32 row mx-0">
                                <template v-if="!regData?.registration_insurance?.tglPulang">
                                  <b-button variant="primary-600" :disabled="isDisabled" @click.prevent="validationForm" class="m-1">Simpan</b-button>
                                </template>
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
  import { mapState } from 'vuex';
  
  import patientMenu from '@/@core/components/tab-menu/patientMenu.vue';
  import administration from '@/views/layouts/patient/doctor/administration.vue';
  import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
  import vSelect from 'vue-select'
  import router from '@/router/index.js'
  
  // import ModernLabel from '@/@core/components/label/ModernLabel.vue';
  import moment from 'moment'
  
  export default {
    name: 'BPJSCheckout',
    components: {
      patientMenu, administration, vSelect, ToastificationContent
    },
    data() {
      return {
        isDisabled:false,
        pesertaData: {},
        pesertaSep: {},
        regData: {},
        form: {
          registration_destination_id:'',
          registration_id:'',
          noKartu:'',
          noSep:'',
          noLPManual:'',
          statusPulang:'',
          noSuratMeninggal:'',
          tglMeninggal:'',
          tglPulang: '',
          user:JSON.parse(localStorage.getItem('userData')).employee_name,
        },
        regControl:{},
        OptionsIcd10:[],
        OptionsPulang:[
          { value: '1', title: 'Atas Persetujuan Dokter' },
          { value: '3', title: 'Atas Permintaan Sendiri' },
          { value: '4', title: 'Meninggal' },
          { value: '5', title: 'Lain-lain' },
        ],
        active: {
          specialist_modal: false,
        },
        regInsurances: {},
        refPoliData: [],
        fieldsPoli: [
          { key: 'kodePoli', label: 'Kode Poli' },
          { key: 'namaPoli', label: 'Nama Poli' },
          { key: 'kapasitas', label: 'Kapasitas' },
          { key: 'jmlRencanaKontroldanRujukan', label: 'Jml Rencana Kontroldan Rujukan' },
          { key: 'persentase', label: 'Persentase' },
        ],
        fieldsDokter: [
          { key: 'kodeDokter', label: 'Kode Dokter' },
          { key: 'namaDokter', label: 'Nama Dokter' },
          { key: 'jadwalPraktek', label: 'Jadwal Praktek' },
          { key: 'kapasitas', label: 'Kapasitas' },
          { key: 'action', label: '#' },
        ]
        // OptionsFamilyRelation: [
        //   { value: 1, title: 'Ayah' },
        //   { value: 2, title: 'Ibu' },
        //   { value: 3, title: 'Kakak' },
        //   { value: 4, title: 'Adik' },
        // ],
      }
    },
    methods: {
      validateCheckout() {
        // alert('set Tgl.Pulang Berhasil')
        // alert('Tanggal Pulang Tidak sesuai dengan tgl Hari ini')
        // alert('Tanggal Pulang Tidak boleh lebih kecil dari tanggal SEP')
      },
      fillForm(row){
        this.active.specialist_modal = false
        this.form.poliKontrol = row.item.kodePoli
        this.form.kodeDokter = row.item.kodeDokter
        this.form.doctor_in_charge_name = row.item.namaDokter

        // console.log('kode dokter', this.form.kodeDokter );
      },
      toggleRefDoctor(row){
        row.toggleDetails();
        // console.log('kodepoli:', row.item.kodePoli);
        this.$store.dispatch('patient/getRefDokter', {serve: this.form.statusPulang, kodePoli:row.item.kodePoli} ).then(data => {

          let refDokter = data.response.list
          refDokter = refDokter.map(dokter => ({
            ...dokter,
            kodePoli: row.item.kodePoli
          }));

          const itemIndex = this.refPoliData.findIndex(item => item.kodePoli === row.item.kodePoli);
          if (itemIndex !== -1) {
            this.$set(this.refPoliData, itemIndex, {
                ...this.refPoliData[itemIndex],
                refDokter,
            });
          }
          // console.log('updated:', this.refPoliData);
        })

      },
      validationForm () {
        const formData = this.form 
        console.log('fromdata: ', formData);
        this.isDisabled = true
        this.$store.dispatch('patient/formPostPutCheckout', formData)
        .then(res => {
          console.log('response: ', res);
          // this.notification('Success','CheckIcon','success','You have successfully')
          if (res.status == 200 || res.status == 201) {
            this.notification('Success','CheckIcon','success',res.message)
            // location.reload()
          } else if (res.status == 404) {
              this.notification('Warning','AlertCircleIcon','warning','Error')
          } else if (res.status == 422) {
              this.notification('Warning','AlertCircleIcon','warning',"Error")
          }
          this.isDisabled = false
        }, 
        err => {
          this.isDisabled = false
          console.log(err)
          this.notification('Failed','CloseIcon','danger',err)
        })
      },
      onSpecialistModal(){
        this.active.specialist_modal = true
        const object = {
          serve: this.form.statusPulang,
          number_insurance: this.regInsurances.number_insurance,
          sep: this.regInsurances.sep
        }
        // console.log('object',object);
        this.$store.dispatch('patient/getRefPoli', object ).then(data => {
          this.refPoliData = data.response.list
          // console.log('dah di hit:', this.refPoliData);
        })
      },
      notification(a, b, c, d) {
        this.$toast({
          component: ToastificationContent,
          position: 'top-right',
          props: { title: a, icon: b, variant: c, text: d }
        })
      },
      async getDataSep(id) {
        try {
          const response = await this.$store.dispatch('patient/bpjs', {url:'Sep',idx:id})
          this.pesertaSep = response.data.response
          this.getDataBpjs(this.pesertaSep.peserta.noKartu)
          this.form.noSep = id
        } catch (error) {
          this.notification('Failed', 'CloseIcon', 'danger', 'Something Wrong!');
        }
      },
      async getDataBpjs(id) {
        try {
          const response = await this.$store.dispatch('patient/bpjs', {url:'noka',idx:id})
          this.pesertaData = response.response.peserta
          this.form.noKartu = id
        } catch (error) {
          this.notification('Failed', 'CloseIcon', 'danger', 'Something Wrong!');
        }
      },
      async getDieLetter(id) {
        try {
          const response = await this.$store.dispatch('letter/getAllNew', {registration_destination_id:id,letter_type_id:'e9432634-dd6b-41cd-b57d-7c34daba9efc'})
          this.form.noSuratMeninggal = response?.rows[0]?.no
        } catch (error) {
          this.notification('Failed', 'CloseIcon', 'danger', 'Something Wrong!');
        }

      },
      async getData(id) {
        try {
          const response = await this.$store.dispatch('registration/getById', id)
          this.regData = response[0]
          this.form.registration_destination_id = id
          this.form.poliKontrol = response[0].health_facility_item_name
          this.form.norm = response[0].norm
          this.form.kodeDokter = response[0].doctor_in_charge_id
          this.noLPManual = response[0].noLPManual
          this.statusPulang = response[0].statusPulang
          this.tglMeninggal = response[0].tglMeninggal
          this.form.tglPulang = moment(new Date(response[0].checkout_date)).format('YYYY-MM-DD')
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
          this.getDieLetter(this.$route.params.id)
          this.getDataSep(this.$route.params.no)
        }
    },
    computed: {
      ...mapState({
        // OptionsInpatient: state => state.healthfacility.options.inpatientoption,
      })
    },
  }
  </script>
  <style lang="scss">
  @import "@core/scss/vue/libs/vue-select.scss";
  </style>
  <style scoped>
  .label-head::before {
    content: "";
    border-left: 6px solid #648DE1;
  }
  
  .c-img {
    aspect-ratio: 1/1;
    object-fit: cover;
    max-width: 150px;
    max-height: 150px;
    border: 10px solid white;
  }
  </style>
  