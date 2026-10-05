<template>
  <div>
    <!-- <div class="label-head my-16 my-lg-24">
      <span class="px-16 text-bold-24 text-neutral-700">BPJS</span>
    </div> -->
    <!-- ////////  -->
    <div class="my-16 my-lg-24">
      <span class="px-16 text-bold-24 text-neutral-700">Rujukan (SPRI/SPRJ)</span>
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
                                        <feather-icon icon="FolderPlusIcon" class="mr-2" /> {{ pesertaData.hakKelas && pesertaData.hakKelas.keterangan ? pesertaData.hakKelas.keterangan : '-' }}
                                    </b-list-group-item>
                                    <b-list-group-item style="border-bottom: 2px solid rgb(231, 231, 231);">
                                        <feather-icon icon="BriefcaseIcon" class="mr-2" /> {{  pesertaData.provUmum && pesertaData.provUmum.nmProvider ? pesertaData.provUmum.nmProvider : '-' }} ( {{pesertaData.provUmum && pesertaData.provUmum.kdProvider ? pesertaData.provUmum.kdProvider : '-'}} )
                                    </b-list-group-item>
                                    <b-list-group-item style="border-bottom: 2px solid rgb(231, 231, 231);">
                                        <feather-icon icon="CalendarIcon" class="mr-2" /> {{ pesertaData.tglTMT }} s.d {{pesertaData.tglTAT}}
                                    </b-list-group-item>
                                    <b-list-group-item style="border-bottom: 2px solid rgb(231, 231, 231);">
                                        <feather-icon icon="CalendarIcon" class="mr-2" /> {{ pesertaData.jenisPeserta && pesertaData.jenisPeserta.keterangan ? pesertaData.jenisPeserta.keterangan : '-' }}
                                    </b-list-group-item>
                                </b-list-group>
                            </section>
                            <section class="col-lg-9 mt-32 mt-lg-0">
                                <div class="label-head ml-10">
                                  <span class="px-16 text-bold-24 text-neutral-700">Rencana Kontrol</span>
                                </div>
                                <div class="d-flex justify-content-center">
                                  <div class="col-lg-12">
                                    <div class="row">
                                      <div class="col-lg-12">
                                        <div>
                                          <label class="c-form-label">Pelayanan <span class="text-danger-500">*</span></label>
                                          <v-select v-model="form.jnsPelayanan" :options="OptionsServe" :reduce="OptionsServe => OptionsServe.value" label="title" placeholder="-- Pelayanan --"></v-select>
                                        </div>
                                      </div>
                                      <template v-if="form.jnsPelayanan == 2">
                                        <div class="col-lg-12">
                                          <div class="mt-12">
                                            <label class="c-form-label">Tgl.Rencana Kontrol.</label>
                                            <b-form-input v-model="form.tglRencanaKontrol" :max="this.asalTglRujukan" type="date"></b-form-input>
                                          </div>
                                          <label class="c-form-label" v-if="form.jnsPelayanan == 2">Rujukan s/d : <br>{{ asalTglRujukanDate }}</label> 
                                        </div>
                                      </template>
                                      <template v-if="form.jnsPelayanan == 1">
                                        <div class="col-lg-12">
                                          <div class="mt-12">
                                            <label class="c-form-label">Tgl.Rencana Inap</label>
                                            <b-form-input v-model="form.tglRencanaKontrol" type="date"></b-form-input>
                                          </div>
                                        </div>
                                      </template>
                                      <div class="col-lg-12">
                                        <div class="mt-12">
                                          <label class="c-form-label">Spesialis/SubSpesialis <span class="text-danger-500">*</span></label>
                                          <b-input-group>
                                            <b-form-input v-model="form.poliKontrol" readonly></b-form-input>
                                            <b-input-group-prepend>
                                              <b-button @click="onSpecialistModal" variant="info-600">Cari</b-button>
                                            </b-input-group-prepend>
                                          </b-input-group>
                                        </div>
                                      </div>
                                      <div class="col-lg-12">
                                        <div class="mt-12">
                                          <label class="c-form-label">Nama DPJP <span class="text-danger-500">*</span></label>
                                          <b-form-input v-model="form.doctor_in_charge_name" ></b-form-input>
                                          <!-- <v-select v-model="form.kodeDokter" aria-readonly="true" :options="OptionsDoctor" :reduce="OptionsDoctor => OptionsDoctor.value" label="title" placeholder="-- Dokter --"></v-select> -->
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
                              <template v-if="!regControl">
                                <b-button variant="primary-600" :disabled="isDisabled" @click.prevent="validationForm" class="m-1">Simpan</b-button>
                              </template>
                              <template v-else>
                                <router-link :to="{ path: '/prints/control/'+ regControl.registration_destination_id + '/' + regControl.no_control}">
                                  <b-button variant="warning-500" class="m-1">
                                    <feather-icon icon="PrinterIcon" class="m-1" /> Cetak
                                  </b-button>
                                </router-link>
                                <b-button variant="danger-600" :disabled="isDisabled" @click.prevent="validationFormDelete" class="m-1">Hapus</b-button>
                              </template>
                              <b-button variant="outline-primary-500" class="m-1">Kembali</b-button>
                              <router-link v-if="form.jnsPelayanan == 2" :to="{ path: '/bpjs/refer/'+ this.$route.params.id + '/' + form.noSep}">
                                <b-button variant="warning-500" class="m-1">
                                  <feather-icon icon="SendIcon" class="m-1" /> Rujuk Balik
                                </b-button>
                              </router-link>
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
      isDisabled:false,
      pesertaData: {},
      regData: {},
      asalTglRujukan: '',
      asalTglRujukanDate:'',
      form: {
        registration_destination_id:'',
        registration_id:'',
        noKartu:'',
        noSep:'',
        jnsPelayanan:2,
        kodeDokter:'',
        poliKontrol: '',
        asalTglRujukan: '',
        tglRencanaKontrol: moment(new Date()).format('YYYY-MM-DD'),
        noSuratKontrol: '',
        user:JSON.parse(localStorage.getItem('userData')).employee_name,
        doctor_in_charge_name: ''
      },
      regControl:{},
      OptionsIcd10:[],
      OptionsServe:[
        { value: 1, title: 'SPRI' },
        { value: 2, title: 'SPRJ' },
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
    validationFormDelete () {
      const formData = this.form 
      console.log('fromdata: ', formData);
      this.isDisabled = true
      this.$store.dispatch('patient/formDeleteKontrol', formData)
      .then(res => {
        // this.notification('Success','CheckIcon','success','You have successfully')
        if (res.status == 200 || res.status == 201) {
          location.reload()
          this.notification('Success','CheckIcon','success',res.message)
        } else if (res.status == 404) {
          this.notification('Warning','AlertCircleIcon','warning','Error')
        } else if (res.status == 422) {
          this.notification('Warning','AlertCircleIcon','warning',"Error")
        }
        // location.reload()
        this.isDisabled = false
      }, 
      err => {
        this.isDisabled = false
        this.notification('Failed','CloseIcon','danger',err)
      })
    },
    fillForm(row){
      this.active.specialist_modal = false
      this.form.poliKontrol = row.item.kodePoli
      this.form.kodeDokter = row.item.kodeDokter
      this.form.doctor_in_charge_name = row.item.namaDokter

      console.log('kode dokter', this.form.kodeDokter );
    },
    toggleRefDoctor(row){
      row.toggleDetails();
      // console.log('kodepoli:', row.item.kodePoli);
      this.$store.dispatch('patient/getRefDokter', {serve: this.form.jnsPelayanan, kodePoli:row.item.kodePoli,tanggal: this.form.tglRencanaKontrol} ).then(data => {

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
      this.$store.dispatch('patient/formPostPutControl', formData)
      .then(res => {
        console.log('response: ', res);
        // this.notification('Success','CheckIcon','success','You have successfully')
        if (res.status == 200) {
          this.notification('Success','CheckIcon','success',res.message)
          // location.reload()
        } else if (res.status == 404) {
            this.notification('Warning','AlertCircleIcon','warning','Error')
        } else if (res.data.code == 202 || res.status == 201) {
          this.notification('Failed','CloseIcon','danger',res.message)
        }
        this.getData(this.$route.params.id)
        this.getRegInsurance(this.$route.params.id)
        this.getRegControl(this.$route.params.id)
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
        serve: this.form.jnsPelayanan,
        number_insurance: this.regInsurances.number_insurance,
        sep: this.regInsurances.sep,
        tanggal: this.form.tglRencanaKontrol
      }
      // console.log('object',object);
      this.$store.dispatch('patient/getRefPoli', object ).then(data => {
        this.refPoliData = data.response.list
        // console.log('dah di hit:', this.refPoliData);
      })
    },
    async getRegInsurance(id) {
      try {
        const response = await this.$store.dispatch('registration/getByInsuranceByNoka', {registration_destination_id:id,number_insurance:this.$route.params.no})
        this.regInsurances = response[0]
        this.form.noSep = response[0].sep
        this.form.registration_destination_id = id
        this.form.registration_id = response[0].registration_id
        this.$store.dispatch('patient/bpjs', {url:'Sep',idx:this.regInsurances.sep})
        .then(response_sep => {
          if (response_sep.data) {
            if (response_sep.data.response.noRujukan) {
              this.$store.dispatch('patient/bpjs', {url:'Rujukan',idx:response_sep.data.response.noRujukan})
              .then(response_rujukan => {
                if(response_rujukan.data){
                  if(response_rujukan.data[0]){
                    const addedDate = moment(response_rujukan.data[0].tglKunjungan).add(89, 'days');
                    this.asalTglRujukan = addedDate.format('YYYY-MM-DD');
                    this.asalTglRujukanDate = addedDate.format('DD-MM-YYYY');
                  }
                }
              })
            }
          }
        })
      } catch (error) {
        // this.notification('Failed', 'CloseIcon', 'danger', 'Something Wrong!');
      }
    },
    async getRegControl(id) {
      try {
        const response = await this.$store.dispatch('registration/getByControlByNoka', {registration_destination_id:id,number_insurance:this.$route.params.no})
        this.regControl = response[0]
        this.form.noSuratKontrol = response[0].no_control
        // console.log(this.regControl)
      } catch (error) {
        // this.notification('Failed', 'CloseIcon', 'danger', 'Something Wrong!');
      }
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
        this.form.noKartu = id
      } catch (error) {
        // this.notification('Failed', 'CloseIcon', 'danger', 'Something Wrong!');
      }
    },
    async getData(id) {
      try {
        const response = await this.$store.dispatch('registration/getById', id)
        this.regData = response[0]
        this.form.poliKontrol = response[0].health_facility_item_name
        this.form.norm = response[0].norm
        this.form.kodeDokter = response[0].doctor_in_charge_id
        this.getDataBpjs(this.$route.params.no)
      } catch (error) {
        // this.notification('Failed', 'CloseIcon', 'danger', 'Something Wrong!');
      }
    }
  },
  mounted() {
    if (this.$route.params.id) {
      // this.show = true
      this.id = this.$route.params.id
      this.getData(this.$route.params.id)
      this.getRegInsurance(this.$route.params.id)
      this.getRegControl(this.$route.params.id)
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
