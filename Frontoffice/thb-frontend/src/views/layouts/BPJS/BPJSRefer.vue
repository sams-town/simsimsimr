<template>
  <div>
    <!-- <div class="label-head my-16 my-lg-24">
      <span class="px-16 text-bold-24 text-neutral-700">BPJS</span>
    </div> -->
    <!-- ////////  -->
    <div class="my-16 my-lg-24">
      <span class="px-16 text-bold-24 text-neutral-700">Rujukan</span>
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
                                <span class="px-16 text-bold-24 text-neutral-700">SEP</span>
                              </div>
                              <b-list-group class="mt-16 text-uppercase">
                                <b-list-group-item style="border-bottom: 2px solid rgb(231, 231, 231);">
                                  <feather-icon icon="UserIcon" class="mr-2" /> {{ pesertaDataSep?.noSep }}
                                </b-list-group-item>
                                <b-list-group-item style="border-bottom: 2px solid rgb(231, 231, 231);">
                                  <feather-icon icon="ClipboardIcon" class="mr-2" /> {{ pesertaDataSep.tglSep }}
                                </b-list-group-item>
                                <b-list-group-item style="border-bottom: 2px solid rgb(231, 231, 231);">
                                  <feather-icon icon="UserIcon" class="mr-2" /> {{ sepjson?.response?.sep?.diagnosa }}
                                </b-list-group-item>
                              </b-list-group>
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
                                  <feather-icon icon="BriefcaseIcon" class="mr-2" /> {{  pesertaData.provUmum && pesertaData.provUmum.nmProvider ? pesertaData.provUmum.nmProvider : '-' }} ( {{pesertaData.provUmum && pesertaData.provUmum.kdProvider? pesertaData.provUmum.kdProvider : '-'}} )
                                </b-list-group-item>
                                <b-list-group-item style="border-bottom: 2px solid rgb(231, 231, 231);">
                                  <feather-icon icon="CalendarIcon" class="mr-2" /> {{ pesertaData.tglTMT }} s.d {{pesertaData.tglTAT}}
                                </b-list-group-item>
                                <b-list-group-item style="border-bottom: 2px solid rgb(231, 231, 231);">
                                  <feather-icon icon="CalendarIcon" class="mr-2" /> {{  pesertaData.jenisPeserta && pesertaData.jenisPeserta.keterangan ? pesertaData.jenisPeserta.keterangan : '-' }}
                                </b-list-group-item>
                              </b-list-group>
                            </section>
                            <section class="col-lg-9 mt-32 mt-lg-0">
                                <div class="label-head ml-10">
                                  <span class="px-16 text-bold-24 text-neutral-700">Form Entry Rujukan</span>
                                </div>
                                <div class="d-flex justify-content-center">
                                  <div class="col-lg-12">
                                    <div class="row">
                                      <div class="col-lg-12">
                                        <div>
                                          <label class="c-form-label">Tgl.Rencana Kunjungan</label>
                                          <b-form-input v-model="form.tglRencanaKunjungan" type="date"></b-form-input>
                                        </div>
                                      </div>
                                      <div class="col-lg-12">
                                        <div class="mt-12">
                                          <label class="c-form-label">Pelayanan <span class="text-danger-500">*</span></label>
                                          <v-select v-model="form.jnsPelayanan" :options="OptionsServe" :reduce="OptionsServe => OptionsServe.value" label="title" placeholder="-- Pelayanan --"></v-select>
                                        </div>
                                      </div>
                                      <div class="col-lg-12">
                                        <div class="mt-12">
                                          <label class="c-form-label">Tipe <span class="text-danger-500">*</span></label>
                                          <div class="col">
                                            <div class="row">
                                              <b-form-radio @input="changetiperujukan" v-model="form.tipeRujukan" value="0">Penuh &nbsp;</b-form-radio>
                                              <b-form-radio @input="changetiperujukan" v-model="form.tipeRujukan" value="1">Partial &nbsp;</b-form-radio>
                                              <b-form-radio @input="changetiperujukan" v-model="form.tipeRujukan" value="2">Rujuk Balik &nbsp;</b-form-radio>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="col-lg-12">
                                        <div class="mt-12">
                                          <label class="c-form-label">Diagnosa <span class="text-danger-500">*</span></label>
                                          <v-select v-model="form.diagRujukan" @search="onSearchIcd10" :options="OptionsIcd10" :reduce="OptionsIcd10 => OptionsIcd10.value" label="title" placeholder="-- Diagnosa --"></v-select>
                                        </div>
                                      </div>
                                      <div class="col-lg-12" v-if="form.tipeRujukan === '2'">
                                        <div class="mt-12">
                                          <label class="c-form-label">Di Rujuk ke <span class="text-danger-500">*</span></label>
                                          <b-form-input v-model="form.namatujuanRujukan" readonly></b-form-input>
                                        </div>
                                      </div>
                                      <div class="col-lg-12" v-if="form.tipeRujukan !== '2'">
                                        <div class="mt-12">
                                          <label class="c-form-label">Di Rujuk ke <span class="text-danger-500">*</span></label>
                                          <b-input-group>
                                            <b-form-input v-model="form.namatujuanRujukan" readonly></b-form-input>
                                            <b-input-group-prepend>
                                              <b-button @click="active.reference_modal = true" variant="info-600">Cari</b-button>
                                            </b-input-group-prepend>
                                          </b-input-group>
                                        </div>
                                      </div>
                                      <div class="col-lg-12" v-if="form.tipeRujukan !== '2'">
                                        <div class="mt-12">
                                          <label class="c-form-label">Spesialis/SubSpesialis <span class="text-danger-500">*</span></label>
                                          <b-form-input v-model="form.namaSpesialis" readonly></b-form-input>
                                        </div>
                                      </div>
                                      <div class="col-lg-12">
                                        <div class="mt-12">
                                          <label class="c-form-label">Catatan</label>
                                          <b-textarea v-model="form.note"></b-textarea>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                            </section>

                            <b-modal v-model="active.reference_modal" title="Faskes" size="lg" scrollable hide-footer>
                              <section>
                                <div class="row">
                                  <div class="col-lg-3">Di Rujuk Ke</div>
                                  <div class="col-lg-9">
                                    <v-select v-model="form_reference.faskes" @input="handleSelectFaskes($event)" @search="onSearchFaskes" :options="OptionsFaskes" :reduce="OptionsFaskes => OptionsFaskes" label="title" placeholder="-- ketik kode atau nama ppk minimal 3 karakter --"></v-select>
                                  </div>
                                </div>
                                <div class="row mt-12">
                                  <div class="col-lg-3">Tgl.Rencana Kunjungan</div>
                                  <div class="col-lg-9">
                                    <b-form-input v-model="form_reference.visit_date" @input="handleSelectFaskes($event)" type="date"></b-form-input>
                                  </div>
                                </div>
                              </section>

                              <section class="mt-32">
                                <b-table :fields="fieldsReference" :items="itemsReference" >
                                  <template #cell(no)="row">
                                    <div>{{ row.index + 1}}</div>
                                  </template>
                                  <template #cell(action)="row">
                                    <b-button @click="fillForm(row.item)" variant="success" size="sm">
                                      <b-icon icon="check-lg"></b-icon>
                                    </b-button>
                                  </template>
                                </b-table>
                              </section>
                            </b-modal>
                            <section class="mt-32 row mx-0">
                              <template v-if="!regRefer">
                                <b-button variant="primary-600" @click.prevent="validationForm" class="m-1">Simpan</b-button>
                              </template>
                              <template v-else>
                                <router-link :to="{ path: '/prints/rujukan/'+ regRefer.registration_destination_id + '/' + regRefer.noSep}">
                                  <b-button variant="warning-500" class="m-1">
                                    <feather-icon icon="PrinterIcon" class="m-1" /> Cetak
                                  </b-button>
                                </router-link>
                                <b-button variant="danger-600" @click.prevent="validationFormDelete" class="m-1">Hapus</b-button>
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
  name: 'BPJSLetter',
  components: {
    patientMenu, administration, vSelect, ToastificationContent
  },
  data() {
    return {
      isDisabled:false,
      pesertaData: {},
      pesertaDataSep: {},
      regRefer:{},
      regData: {},
      form: {
        registration_destination_id:'',
        registration_id:'',
        noRujukan:'',
        noSep:'',
        tglRujukan:'',
        tglRencanaKunjungan: moment(new Date()).format('YYYY-MM-DD'),
        ppkDirujuk: '',
        jnsPelayanan:'2',
        catatan: '',
        diagRujukan: '',
        tipeRujukan:'0',
        poliRujukan: '',
        user:JSON.parse(localStorage.getItem('userData')).employee_name,
      },
      sepjson:{},
      OptionsIcd10:[],
      OptionsServe:[
        { value: '1', title: 'Rawat Inap' },
        { value: '2', title: 'Rawat Jalan' },
      ],
      active: {
        reference_modal: false,
      },
      form_reference: {
        faskes: '',
        visit_date: moment(new Date()).format('YYYY-MM-DD'),
      },
      fieldsReference: [
        { key: 'no', label: 'No' },
        { key: 'namaSpesialis', label: 'Nama Spesialis / Sub' },
        { key: 'kapasitas', label: 'Kapasitas' },
        { key: 'jumlahRujukan', label: 'Jumlah Rujukan' },
        { key: 'persentase', label: 'Persentase' },
        { key: 'action', label: '#' },
      ],
      OptionsFaskes:[],
      itemsReference: []
    }
  },
  methods: {
    changetiperujukan () {
      if (this.form.tipeRujukan === '2') {
        this.form.ppkDirujuk = this.pesertaData.provUmum.kdProvider
        this.form.namatujuanRujukan = this.pesertaData.provUmum.nmProvider
      } else {
        this.form.ppkDirujuk = ''
        this.form.namatujuanRujukan = ''
      }
    },
    validationForm () {
      const formData = this.form 
      this.isDisabled = true
      this.$store.dispatch('patient/formPostPutRefer', formData)
      .then(res => {
        // this.notification('Success','CheckIcon','success','You have successfully')
        location.reload()
        if (res.status == 200 || res.status == 201) {
          this.notification('Success','CheckIcon','success',res.data.metaData.message)
          location.reload()
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
        this.notification('Failed','CloseIcon','danger',err)
      })
    },
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
        this.getRegInsurance()
      } catch (error) {
        this.notification('Failed', 'CloseIcon', 'danger', 'Something Wrong!');
      }
    },
    async getRegInsurance(id) {
      try {
        const response = await this.$store.dispatch('registration/getByInsuranceByNoka', {registration_destination_id:this.$route.params.id,number_insurance:this.form.noKartu})
        this.regInsurances = response[0]
        this.form.noSep = response[0].sep
        this.form.registration_destination_id = this.$route.params.id
        this.form.registration_id = response[0].registration_id
        this.sepjson = JSON.parse(response[0].sep_json)
      } catch (error) {
        // this.notification('Failed', 'CloseIcon', 'danger', 'Something Wrong!');
      }
    },
    async getRegRefer(id) {
      try {
        const response = await this.$store.dispatch('registration/getByReferBySep', {registration_destination_id:id,sep:this.$route.params.no})
        this.regRefer = response[0]
        this.form.noRujukan = response[0].no_refer
      } catch (error) {
        // this.notification('Failed', 'CloseIcon', 'danger', 'Something Wrong!');
      }
    },
    validationFormDelete () {
      const formData = this.form 
      console.log('fromdata: ', formData);
      this.isDisabled = true
      this.$store.dispatch('patient/formDeleteRujukan', formData)
      .then(res => {
        // this.notification('Success','CheckIcon','success','You have successfully')
        if (res.status == 200 || res.status == 201) {
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
    async getDataSep() {
      try {
        const response = await this.$store.dispatch('patient/bpjs', {url:'Sep',idx:this.$route.params.no})
        this.form.noSep = this.$route.params.no
        this.pesertaDataSep = response.data.response
        this.form.tglRujukan = response.data.response.tglSep
        this.getDataBpjs(this.pesertaDataSep.peserta.noKartu)
        // console.log(this.pesertaDataSep)

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
        this.getDataSep()
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
              if (key === 'code') { option['value'] = data[i][key] } else if (key === 'name') { option['title'] = data[i]['code']+' '+data[i][key] } else if (key === 'id') { option['oid'] = data[i][key] }
            }
            this.OptionsIcd10.push(Object.assign({}, option))
          }
          loading(false)
        })
      }
    },
    onSearchFaskes(search, loading) {
      if(search.length >= 3) {
        loading(true);
        this.$store.dispatch('patient/getFaskesBPJS', search).then(data => {
          this.OptionsFaskes = []
          for (let i = 0; i < data.length; i++) {
            const option = []
            for (const key in data[i]) {
              if (key === 'kodetujuanRujukan') { option['value'] = data[i][key] } else if (key === 'namatujuanRujukan') { option['title'] = data[i][key] } else if (key === 'kodetujuanRujukan') { option['oid'] = data[i][key] }
            }
            this.OptionsFaskes.push(Object.assign({}, option))
            // console.log('options:', this.OptionsFaskes);
          }
          loading(false)
        })
      }
    },
    handleSelectFaskes(event) {
      this.itemsReference = []
      
      try {
        if (this.form_reference.faskes && this.form_reference.visit_date) {
          this.$store.dispatch('patient/getPPKspesialis', this.form_reference).then(data => {
            let ppkData = data
            ppkData = ppkData.map(item => ({
              ...item,
              kodetujuanRujukan: this.form_reference.faskes.value,
              namatujuanRujukan: this.form_reference.faskes.title,
            }));
            this.itemsReference = ppkData
            // console.log('this.itemsReference:', this.itemsReference);
          })
        } else {
          this.notification('Warning', 'CloseIcon', 'warning', 'Tujuan Rujukan dan Tgl.Rencana Kunjungan Tidak Boleh Kosong');
        }
        
      } catch (error) {
        console.error('Error at: ', error)
      }
    },
    fillForm(row){
      this.active.reference_modal = false
      // console.log('selected: ', row);
      this.form.poliRujukan = row.kodeSpesialis
      this.form.ppkDirujuk = row.kodetujuanRujukan
      this.form.namatujuanRujukan = row.namatujuanRujukan 

      this.form.namaSpesialis = row.namaSpesialis 

      // console.log('form: ', this.form);
    },
  },
  mounted() {
    if (this.$route.params.id) {
      // this.show = true
      this.id = this.$route.params.id
      this.getData(this.$route.params.id)
      this.getRegRefer(this.$route.params.id)
    }
  },
  computed: {
    ...mapState({
      OptionsDoctor: state => state.employee.options.employee,
    })
  },
  created() {
    this.$store.dispatch('employee/getParamSpecialist')
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
