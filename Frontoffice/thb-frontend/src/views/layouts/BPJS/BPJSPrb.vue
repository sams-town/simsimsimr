<template>
    <div>
      <!-- <div class="label-head my-16 my-lg-24">
        <span class="px-16 text-bold-24 text-neutral-700">BPJS</span>
      </div> -->
      <!-- ////////  -->
      <div class="my-16 my-lg-24">
        <span class="px-16 text-bold-24 text-neutral-700">Perserta Rujuk Balik (PRB)</span>
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
                                          <feather-icon icon="UserIcon" class="mr-2" /> {{ pesertaData.nama }} ({{ pesertaData.sex }})
                                      </b-list-group-item>
                                      <b-list-group-item style="border-bottom: 2px solid rgb(231, 231, 231);">
                                          <feather-icon icon="ClipboardIcon" class="mr-2" /> {{ pesertaData.nik }}
                                      </b-list-group-item>
                                      <b-list-group-item style="border-bottom: 2px solid rgb(231, 231, 231);">
                                          <feather-icon icon="CalendarIcon" class="mr-2" /> {{ pesertaData.tglLahir }}
                                      </b-list-group-item>
                                      <b-list-group-item style="border-bottom: 2px solid rgb(231, 231, 231);">
                                          <feather-icon icon="FolderPlusIcon" class="mr-2" /> {{ pesertaData.hakKelas.keterangan }}
                                      </b-list-group-item>
                                      <b-list-group-item style="border-bottom: 2px solid rgb(231, 231, 231);">
                                          <feather-icon icon="BriefcaseIcon" class="mr-2" /> {{  pesertaData.provUmum.nmProvider }} ( {{pesertaData.provUmum.kdProvider}} )
                                      </b-list-group-item>
                                      <b-list-group-item style="border-bottom: 2px solid rgb(231, 231, 231);">
                                          <feather-icon icon="CalendarIcon" class="mr-2" /> {{ pesertaData.tglTMT }} s.d {{pesertaData.tglTAT}}
                                      </b-list-group-item>
                                      <b-list-group-item style="border-bottom: 2px solid rgb(231, 231, 231);">
                                          <feather-icon icon="CalendarIcon" class="mr-2" /> {{  pesertaData.jenisPeserta.keterangan }}
                                      </b-list-group-item>
                                  </b-list-group>
                              </section>
                              <section class="col-lg-9 mt-32 mt-lg-0">
                                  <div class="label-head ml-10">
                                    <span class="px-16 text-bold-24 text-neutral-700">Form Perserta Rujuk Balik (PRB)</span>
                                  </div>
                                  <div class="d-flex justify-content-center">
                                    <div class="col-lg-12">
                                      <div class="row">
                                        <div class="col-lg-12">
                                          <div>
                                            <label class="c-form-label">Tgl.Rujuk Balik</label>
                                            <b-form-input v-model="form.tglRujukBalik" type="date"></b-form-input>
                                          </div>
                                        </div>
                                        <div class="col-lg-12">
                                          <div class="mt-12">
                                            <label class="c-form-label">Alamat Peserta</label>
                                            <b-textarea v-model="form.address"></b-textarea>
                                          </div>
                                        </div>
                                        <div class="col-lg-12">
                                          <div class="mt-12">
                                            <label class="c-form-label">Kotak/Email Peserta/Keluarga <span class="text-danger-500">*</span></label>
                                            <b-form-input v-model="form.familycontact" type="text"></b-form-input>
                                          </div>
                                        </div>
                                        <div class="col-lg-12">
                                          <div class="mt-12">
                                            <label class="c-form-label">Program PRB <span class="text-danger-500">*</span></label>
                                            <v-select v-model="form.program" :options="OptionsProgram" :reduce="OptionsProgram => OptionsProgram.value" label="title" placeholder="-- Program PRB --"></v-select>
                                          </div>
                                        </div>
                                        <div class="col-lg-12">
                                          <div class="mt-12">
                                            <label class="c-form-label">DPJP Pemberi Pelyanan <span class="text-danger-500">*</span></label>
                                            <v-select v-model="form.doctor_in_charge_id" aria-readonly="true" :options="OptionsDoctor" :reduce="OptionsDoctor => OptionsDoctor.value" label="title" placeholder="-- Dokter --"></v-select>
                                          </div>
                                        </div>
                                        <div class="col-lg-12">
                                          <div class="mt-12">
                                            <label class="c-form-label">Keterangan</label>
                                            <b-textarea v-model="form.notes"></b-textarea>
                                          </div>
                                        </div>
                                        <div class="col-lg-12">
                                          <div class="mt-12">
                                            <label class="c-form-label">Saran</label>
                                            <b-textarea v-model="form.advice"></b-textarea>
                                          </div>
                                        </div>
                                        <div class="col-lg-12">
                                          <div class="mt-12">
                                            <label class="c-form-label">Nama Obat <span class="text-danger-500">*</span></label>
                                            <v-select v-model="form.serve" :options="OptionsServe" :reduce="OptionsServe => OptionsServe.value" label="title" placeholder="-- Pelayanan --"></v-select>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                              </section>
                              <section class="mt-32 row mx-0">
                                <b-button variant="info-600" class="m-1">Simpan</b-button>
                                <router-link :to="{ name: 'PrintsBpjsMemberView' }">
                                  <b-button variant="warning-500" class="m-1">
                                    <feather-icon icon="PrinterIcon" class="m-1" /> Cetak
                                  </b-button>
                                </router-link>
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
    name: 'BPJSLetter',
    components: {
      patientMenu, administration, vSelect, ToastificationContent
    },
    data() {
      return {
        pesertaData: {},
        regData: {},
        form: {
          serve:1,
          program:'',
          notes:'',
          advice:'',
          familycontact:'',
          address:'',
          doctor_in_charge_id:'',
          icd10_id:'',
          poli_destination: '',
          reference_origin: '',
          ppk_origin: '',
          reference_date: '',
          reference_number: '',
          sep_date: moment(new Date()).format('YYYY-MM-DD'),
          norm: '',
          control: '',
          diagnosis: '',
          phone_number: '',
          note: '',
          accident: '',
          tujuanKunj:1,
          flagProcedure:'',
          assesmentPel:''
        },
        OptionsIcd10:[],
        OptionsProgram:[
          {
            value: "01",
            title: "Diabetes Mellitus"
          },
          {
            value: "02",
            title: "Hypertensi"
          },
          {
            value: "03",
            title: "Asthma"
          },
          {
            value: "04",
            title: "Penyakit Jantung"
          },
          {
            value: "05",
            title: "PPOK (Penyakit Paru Obstruktif Kronik)"
          },
          {
            value: "06",
            title: "Schizophrenia"
          },
          {
            value: "07",
            title: "Stroke"
          },
          {
            value: "08",
            title: "Epilepsi"
          },
          {
            value: "09",
            title: "Systemic Lupus Erythematosus"
          }
        ],
        OptionsPurpose: [
          { value: 1, title: 'Normal' },
          { value: 2, title: 'Prosedur' },
          { value: 3, title: 'Konsul Dokter' }
        ],
        OptionsProcedure: [
          { value: 1, title: 'Tidak Berkelanjutan' },
          { value: 2, title: 'Prosedur dan Terapi Berkelanjutan' },
        ],
        OptionsProcedureCon: [
          {value: "1", title: "Radioterapi"},
          {value: "2", title: "Kemoterapi"},
          {value: "3", title: "Rehabilitasi Medik"},
          {value: "4", title: "Rehabilitasi Psikososial"},
          {value: "5", title: "Transfusi Darah"},
          {value: "6", title: "Pelayanan Gigi"}
        ],
        OptionsProcedureNot: [
          {value: "7", title: "Laboratorium"},
          {value: "8", title: "USG"},
          {value: "9", title: "Farmasi"},
          {value: "10", title: "Lain-Lain"},
          {value: "11", title: "MRI"},
          {value: "12", title: "HEMODIALISA"}
        ],
        OptionsConsuleDoctor: [
          {value: 1, title: "Poli spesialis tidak tersedia pada hari sebelumnya"},
          {value: 2, title: "Jam Poli telah berakhir pada hari sebelumnya"},
          {value: 3, title: "Dokter Spesialis yang dimaksud tidak praktek pada hari sebelumnya"},
          {value: 4, title: "Atas Instruksi RS"},
          {value: 5, title: "Tujuan Kontrol"}
        ],
        OptionsKLL: [
          {value: "0", title: "Bukan Kecelakaan lalu lintas"},
          {value: "1", title: "KLL dan bukan kecelakaan Kerja"},
          {value: "2", title: "KLL dan KK"},
        ],
        // OptionsFamilyRelation: [
        //   { value: 1, title: 'Ayah' },
        //   { value: 2, title: 'Ibu' },
        //   { value: 3, title: 'Kakak' },
        //   { value: 4, title: 'Adik' },
        // ],
      }
    },
    // watch: {
    //   'form.health_facility_id'(obj) {
    //     if (obj) {
    //       this.$store.dispatch('healthfacilityitem/getParam', obj.value)
    //     }
    //   },
    //   'form.health_facility_item_id'(obj) {
    //     if (obj) {
    //       return this.$store.dispatch('employee/getParamSpecialist', obj.value)
    //     }
    //   },
    //   'form.type_room_id'(value) {
    //     if (value) {
    //       return this.$store.dispatch('room/getParamRoom', value)
    //     }
    //   },
    //   'form.room_id'(value) {
    //     if (value) {
    //       return this.$store.dispatch('room/getParamBed', value)
    //     }
    //   },
    //   'form.dpjp_doctor_in_charge_id'(value) {
    //     if (value == 1) {
    //       this.form.doctor_in_charge_utama = this.form.doctor_in_charge_id_1
    //     } else if (value == 2) {
    //       this.form.doctor_in_charge_utama = this.form.doctor_in_charge_id_2
    //     }
    //   },
    // },
    methods: {
      inputKLL() {
        let text = "Apakah ini merupakan kasus kecelakaan baru ?";
        if (confirm(text) == true) {
          this.inputKLLYes()
        } else {
          this.inputKLLTidak()
        }
      },
      inputKLLYes() {
        let text = "1. Apakah kasus berhubungan dengan pekerjaan ?\n2. Berangkat dari rumah menuju tempat kerja ?\n3. Pulang dari tempat kerja menuju ke rumah ?";
        if (confirm(text) == true) {
        } else {
        }
      },
      inputKLLYes() {
      },
      notification(a, b, c, d) {
        this.$toast({
          component: ToastificationContent,
          position: 'top-right',
          props: { title: a, icon: b, variant: c, text: d }
        })
      },
      async getDataBpjs(id) {
        try {
          const response = await this.$store.dispatch('patient/bpjs', {url:'noka',idx:id})
          this.pesertaData = response.response.peserta
        } catch (error) {
          this.notification('Failed', 'CloseIcon', 'danger', 'Something Wrong!');
        }
      },
      async getData(id) {
        try {
          const response = await this.$store.dispatch('registration/getById', id)
          this.regData = response[0]
          this.form.poli_destination = response[0].health_facility_item_name
          this.form.norm = response[0].norm
          this.form.doctor_in_charge_id = response[0].doctor_in_charge_id
          this.getDataBpjs(this.$route.params.no)
          // this.form.ppk_origin = response[0].
          // this.form.reference_date = response[0].
          // this.form.reference_number = response[0].
          // this.form.sep_date = response[0].
          // this.form.control = response[0].
          // this.form.diagnosis = response[0].
          // this.form.phone_number = response[0].
          // this.form.note = response[0].
          // this.form.accident = response[0].
        } catch (error) {
          this.notification('Failed', 'CloseIcon', 'danger', 'Something Wrong!');
        }
      },
      resetTransferForm() {
        this.form = {
          // soap_s_id: '',
          // soap_a_id: '',
        }
      },
      onSearchIcd10(search, loading) {
        if(search.length >= 3) {
          loading(true);
          this.$store.dispatch('icd10/getParam', search).then(data => {
            this.OptionsIcd10 = []
            for (let i = 0; i < data.length; i++) {
              const option = []
              for (const key in data[i]) {
                if (key === 'id') { option['value'] = data[i][key] } else if (key === 'name') { option['title'] = data[i]['code']+' '+data[i][key] } else if (key === 'id') { option['oid'] = data[i][key] }
              }
              this.OptionsIcd10.push(Object.assign({}, option))
            }
            loading(false)
          })
        }
      }
    },
    mounted() {
      if (this.$route.params.id) {
        // this.show = true
        this.id = this.$route.params.id
        this.getData(this.$route.params.id)
      }
    },
    computed: {
      ...mapState({
        // OptionsInpatient: state => state.healthfacility.options.inpatientoption,
        // OptionsHealthFacility: state => state.healthfacility.options.healthfacility,
        // OptionsHealthFacilityItem: state => state.healthfacilityitem.options.healthfacilityitem,
        OptionsDoctor: state => state.employee.options.employee,
        // TypeRoomOptions: state => state.helper.options.type_room,
        // OptionsRoom: state => state.room.options.room,
        // OptionsBed: state => state.room.options.bed,
        // OptionsInsurance: state => state.helper.options.insurance,
        // Options3rdCompany: state => state.helper.options.third_party_company,
        // OptionsServiceClass: state => state.helper.options.service_class,
      })
    },
    created() {
      this.$store.dispatch('employee/getParamSpecialist')
      // this.$store.dispatch('healthfacility/getParam', 'healthfacility')
      // this.$store.dispatch('healthfacility/getInpatientOption', 'inpatientOption')
      // this.$store.dispatch('helper/getParam', 'type_room')
      // this.$store.dispatch('helper/getParam', 'insurance')
      // this.$store.dispatch('helper/getParamThirdPartyCompany', 'third_party_company')
      // this.$store.dispatch('helper/getParamServiceClass', 'service_class')
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
  