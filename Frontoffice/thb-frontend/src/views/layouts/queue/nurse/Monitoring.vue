<template>
  <div>
    <section class="mt-32 px-3">
      <section >
        <div class="card">
          <div class="card-header font-weight-bold bg-primary-200">Filter Data</div>
          <div class="card-body">
              <div class="row">
                <div class="col-lg-4 mt-1">
                    <label class="c-form-label">Instalasi</label>
                    <v-select v-model="params.health_facility_id" :options="OptionsHealthFacility" :reduce="OptionsHealthFacility => OptionsHealthFacility.value" label="title" placeholder="-- Instalasi --"></v-select>
                </div>
                <div class="col-lg-4 mt-1">
                    <label class="c-form-label">Poli/Ruangan</label>
                    <v-select v-model="params.health_facility_item_id" :options="OptionsHealthFacilityItem" :reduce="OptionsHealthFacilityItem => OptionsHealthFacilityItem.value" label="title" placeholder="-- Poliklinik --"></v-select>
                </div>
                <div class="col-lg-4 mt-1">
                    <label class="c-form-label">Dokter</label>
                    <v-select v-model="params.doctor_in_charge_id" :options="OptionsDoctor" :reduce="OptionsDoctor => OptionsDoctor.value" label="title" placeholder="-- Dokter --" />
                </div>
                <div class="col-lg-4 mt-1">
                    <label class="c-form-label">Tanggal Mulai</label>
                    <b-form-input type="date" v-model="params.date_start"></b-form-input>
                </div>
                <div class="col-lg-4 mt-1">
                  <label class="c-form-label">Tanggal Akhir</label>
                  <b-form-input type="date" v-model="params.date_end"></b-form-input>
                </div>
              </div>
              <div class="mt-24">
                <div class="d-flex">
                  <b-button @click="onListFast" :disabled="isDisabledFilter" variant="primary"><feather-icon icon="SearchIcon" class="mr-2" />Cari</b-button>
                  <b-button @click="filterReset" :disabled="isDisabledFilter" variant="outline-primary" class="ml-2">
                      <feather-icon icon="XIcon" /> Clear Filter
                  </b-button>
                </div>
              </div>
          </div>
        </div>
      </section>
      <!-- <section v-show="active.show_actions" class="mt-20">
        <div class="row mx-0">
          <b-button v-for="(item, index) in getDataFiltered" :key="item.key" @click="toggleDoctorTab({ id:item.value, name: item.title }, index)" :variant="active.tab == item.value ? 'primary' : 'outline-primary'" size="sm" class="mr-1">{{ item.title }}</b-button>
        </div>
      </section> -->
      <section class="mt-20">
        <!-- <p>Tab: {{ active.tab }} || Name: {{ getItemData.name }} || Id: {{ getItemData.id }}</p> -->
        <!-- <ModernLabel :label="getItemData.name ? 'Dokter: ' + getItemData.name : 'Dokter: -'" /> -->
        <div class="row mx-0 mt-20 justify-content-between">
          <div class="col-lg-6">
            <div class="row mx-0">
                <b-form-group label-cols="6" label-align="left" label-size="sm" label-for="sortBySelect" class="text-nowrap mb-md-0 mr-1">
                  <span class="text-regular-14">Tampilkan</span>
                  <b-form-select id="per-page-select" v-model="perPage" size="sm" :options="pageOptions"/>
                  <span class="text-regular-14">Entri</span>
                </b-form-group>
            </div>
          </div>
          <div class="col-lg-3">
            <div class="d-flex align-items-center position-relative">
              <b-form-input size="sm" autocomplete="off" placeholder="Cari dengan Nama Pasien" v-model="params.name" @input="onSearch" class="rounded-pill pl-5"></b-form-input>
              <b-icon icon="search" class="position-absolute pl-3 text-neutral-400" />
            </div>
          </div>
        </div>
        <b-table bordered responsive small head-variant="light" :items="ListItems" :fields="NurseTable.fields" :per-page="0" :current-page="currentPage" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="mt-12 text-center">
          <template #cell(name)="row">
            <div class="text-left">
              <span>[{{ row.item.alias }}] </span>
              <span>{{ row.item.name }}</span>
              <!-- <span v-if="row.item.reg_type != 'Appointment'" class="text-danger-500"> [ Waiting List ]</span> -->
            </div>
          </template>
          <template #cell(code)="row">
            <span>{{ row.item.code }} </span><br>
            <span>[{{ row.item.register_date }}] </span>
          </template>
          <template #cell(queue)="row">
            <div>
              <template v-if="row.item.health_facility_name == 'RAWAT INAP' || row.item.health_facility_name == 'IGD'">
                  <span></span>
              </template>
              <template v-else>
                  <span>{{ row.item.code_queue }}-{{ row.item.queue }}</span>
              </template>
            </div>
          </template>
          <template #cell(health_facility_item_name)="row">
            <div>
              <span>{{ row.item.health_facility_item_name }}, </span><br>
              <span>{{ row.item.doctor_in_charge_name }}</span>
            </div>
          </template>
          <template #cell(gender_name)="row">
            <div>
              <span>{{ row.item.sex_name }}, </span>
              <span>{{ parsed_age(row.item.birth_date) }}</span>
            </div>
          </template>
          <template #cell(reg_type)="row">
            <div>
              <span>Walk In</span>
            </div>
          </template>
          <template #cell(is_new)="row">
            <div v-if="row.item.is_new == 'Y'">
              <span>Pasien Baru</span>
            </div>
            <div v-else-if="row.item.is_new == 'N'">
              <span>Pasien Lama</span>
            </div>
          </template>
          <template #cell(status)="row">
            <div>
              <b-badge variant="info-500" class="p-2">{{ row.item.status_antrol  }}</b-badge>
              <b-badge v-if="row.item.sts == 3" variant="info-500" class="p-2">Menunggu TTV [Perawat]</b-badge>
              <b-badge v-else-if="row.item.sts == 4 || row.item.call_nurse_stamp" variant="success-500" class="p-2">Dokter</b-badge>
              <b-badge v-else-if="row.item.sts == 5" variant="success-500" class="p-2">Menunggu Farmasi</b-badge>
              <b-badge v-else-if="row.item.sts == 6" variant="success-500" class="p-2">Layanan Farmasi</b-badge>
              <b-badge v-else-if="row.item.sts == 7" variant="success-500" class="p-2">Pasien Pulang</b-badge>
              <b-badge v-else variant="warning-500" class="p-2">{{ row.item.sts }}</b-badge>
            </div>
          </template>
          <template #cell(totaltask)="row">
            <b-table fixed small bordered responsive :items="row.item.totaltask" head-variant="light" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="text-center">
            </b-table>
          </template>
          <template #cell(encounter)="row">
            <!-- <div v-if="row.item.enc_type == 'Follow Up'"> -->
              <b-button @click="antreanCheck(row.item)" size="sm" variant="pink-500" class="text-white">Detail</b-button>
              <b-button @click="icare(row.item)" size="sm" variant="pink-500" class="text-white">i-Care</b-button>
              <router-link :to="{ path: '/patient/doctor/' + row.item.registration_destination_id }">
                  <b-button size="sm" variant="primary" class="mr-1">
                      <feather-icon icon="FileIcon" />
                  </b-button>
              </router-link>
              <b-button variant="danger-500" size="sm">
                <feather-icon icon="TrashIcon" @click="rowOnDelete(row.item.registration_id)" />
                <b-link v-b-modal.modal-center>
                </b-link>
              </b-button>
            <!-- </div> -->
          </template>
        </b-table>
        <div class="col px-0 mt-20">
            <div class="d-flex">
                <span class="text-regular-14">Menampilkan {{ firstRows }} hingga <template
                        v-if="lastRows < totalRows">{{ lastRows }}</template> <template v-else>{{ totalRows
                        }}</template> dari {{ totalRows }} entri</span>
                <div class="ml-auto">
                    <b-pagination v-model="currentPage" :total-rows="totalRows" :per-page="perPage"
                        align="fill" size="sm" class="my-0"></b-pagination>
                </div>
            </div>
        </div>
      </section>
    </section>
    <b-modal v-model="active.is_taskid" @close="antreanCheck()" title="TASKID" hide-footer>
      <section class="card">
        <div class="card-header rounded border-bottom-0 bg-neutral-100">
          <div class="row">
            <div class="col-lg">
              <div class="row">
                <b-table fixed small bordered responsive :items="taskid.items" :fields="taskid.fields" head-variant="light" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="text-center">
                </b-table>
              </div>
            </div>
          </div>
        </div>
      </section>
    </b-modal>
  </div>
</template>
<script>
import moment from 'moment'
import { mapState } from 'vuex'
import TopQueueMenu from '@/@core/components/top-menu/TopQueueMenu.vue';
import vSelect from 'vue-select'
import ModernLabel from '@/@core/components/label/ModernLabel.vue';
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import axios from 'axios'
import io from 'socket.io-client';
export default {
  components: {
    TopQueueMenu, vSelect, ModernLabel
  },
  data() {
    return {
      user: '',
      isDisabled: false,
      message: '',
      messages: [],
      // socket : io("http://localhost:3001", { transports: ["websocket", "polling", "flashsocket"],withCredentials: true }),
      userData: JSON.parse(localStorage.getItem('userData')), 
      params: {
        offset: 0,
        limit: 50,
        code: '',
        norm: '',
        name: '',
        sex: '',
        how_to_enter_id: '',
        is_new: '',
        health_facility_id: '',
        health_facility_item_id: '',
        doctor_in_charge_id: JSON.parse(localStorage.getItem('userData')).user_group.name === 'Doctor' ? JSON.parse(localStorage.getItem('userData')).employee_id : '',
        date_start: moment(new Date()).format('YYYY-MM-DD'),
        date_end: moment(new Date()).format('YYYY-MM-DD'),
      },
      taskid:{items:[]},
      active: {
        is_taskid:false,
        is_icare:false,
        show_actions: false,
        tab: '',
      },
      searchQuery: "", 
      getDataFiltered: [],
      getItemData: {},
      show: false,
      NurseTable: {
        fields: [
          { key: 'queue', label: 'No Antrian' },
          { key: 'code', label: 'No Pendaftaran' },
          { key: 'norm', label: 'No RM' },
          { key: 'name', label: 'Nama' },
          { key: 'health_facility_item_name', label: 'Poliklinik/Ruangan' },
          { key: 'gender_name', label: 'Kelamin - Umur' }, 
          { key: 'reg_type', label: 'Reg. Tipe' },
          { key: 'insurance_name', label: 'Cara Bayar' },
          { key: 'is_new', label: 'Lama/Baru' },
          { key: 'status', label: 'Status' },
          { key: 'code_booking', label: 'code_booking' },
          { key: 'totaltask', label: 'totaltask' },
          { key: 'encounter', label: 'Aksi' },
          // { key: 'action', label: 'Aksi' },
        ],
        items: [
          // { queue: '1', reg_num: '5', record: '08-00-07-70', name: 'Ghazy Muhammad', waiting_list: true, gender_name: 'Male', age: '10 Years', reg_type: 'Walk In', enc_type: 'Follow Up', status: 'Waiting' },
          // { queue: '1', reg_num: '5', record: '08-00-07-70', name: 'Ghazy Muhammad', waiting_list: true, gender_name: 'Male', age: '10 Years', reg_type: 'Appointment', enc_type: 'New', status: 'Engage' },
          // { queue: '1', reg_num: '5', record: '08-00-07-70', name: 'Ghazy Muhammad', waiting_list: true, gender_name: 'Male', age: '10 Years', reg_type: 'Walk In', enc_type: 'Follow Up', status: 'Completed' },
        ]
      },
      ListItems: [],
      requestCancelToken: null,
      searchTimeout: null,
      currentPage: 1,
      perPage: 20,
      firstRows: 1,
      lastRows: 20,
      totalRows: 0,
      pageOptions: [50, 150, 250],
      isDisabled: false,
      isDisabledFilter: false,
      show: false,

      searchtable:''
    }
  },
  watch: {
    currentPage: {
      handler: function (value) {
        this.firstRows = this.perPage * (value - 1) + 1
        this.lastRows = this.perPage * (value)
        this.params.offset = this.perPage * (value - 1)
        this.params.limit = this.perPage

        this.onListFast()
      }
    },
    perPage: {
      handler: function (value) {
        this.params.limit = this.perPage
        this.onListFast()
      }
    },
    'params.health_facility_id' (val) {
        if(val){
            return this.$store.dispatch('healthfacilityitem/getParam', val)
        }
    },
    'params.health_facility_item_id'(val) {
      if (val) {
        return this.$store.dispatch('employee/getParam', val)
      }
    },
  },
  computed: {
    ...mapState({
      start: state => state.registration.fast.start, 
      OptionsHealthFacility: state => state.healthfacility.options.healthfacility,
      OptionsHealthFacilityItem: state => state.healthfacilityitem.options.healthfacilityitem,
      OptionsDoctor: state => state.employee.options.employee,
    }),
    itemsdata() {
      return this.items.filter((data) => {
      const computedOpItemObj = { ...data}
      return Object.keys(computedOpItemObj)
      .some(key => ('' + computedOpItemObj[key]).toLowerCase().includes(this.searchtable.toLowerCase()))
    })
  },
  
},
  methods: {
    notification(a, b, c, d) {
      this.$toast({
        component: ToastificationContent,
        position: 'top-right',
        props: { title: a, icon: b, variant: c, text: d }
      })
    },
    icare(data) {
      this.$store.dispatch('patient/getIcare', {number_insurance:data.number_insurance,bride_bpjs:data.bride_bpjs})
      .then(res => {
        console.log(res)
        console.log(res.response)
        window.open(res.response.url, '_blank');
      },
      err => {
      })
    },
    antreanCheck(data) {
      this.active.is_taskid = !this.active.is_taskid
      console.log(data)
      this.$store.dispatch('patient/getTaskid', data.code_booking)
      .then(res => {
        console.log(res)
        this.taskid.items = res.response
      },
      err => {
      })
    },
    onSearch(search) {
      if(search.length >= 3) {
        this.$store.dispatch('registration/getAntrol', this.params)
      } else {
        this.params.name = ''
        this.$store.dispatch('registration/getAntrol', this.params)
      }
    },
    parsed_age(input_date) {
      const birthDate = moment(input_date, 'YYYY-MM-DD');
      const currentDate = moment();

      const years = currentDate.diff(birthDate, 'years');
      birthDate.add(years, 'years');
      const months = currentDate.diff(birthDate, 'months');
      birthDate.add(months, 'months');
      const days = currentDate.diff(birthDate, 'days');

      // return `${years} th ${months} bln ${days} hr`;
      return `${years} th`;
    },
    parsed_start_from_date(input_date) {
      return moment(input_date).format('DD-MM-YYYY');
    },
    onNurseFilter () {
        this.show = true
        this.isDisabledFilter = true
        this.$store.dispatch('registration/getAntrol', this.params)
        .then(res => {
            this.show = false
            this.isDisabledFilter = false
        },
        err => {
            this.show = false
            this.isDisabledFilter = false
        })
    },
    filterReset () {
      this.params = {
        offset: 0,
        limit: this.perPage,
        code: '',
        norm: '',
        name: '',
        sex: '',
        type_how_to_enter_id: '',
        is_new: '',
        health_facility_id: '',
        health_facility_item_id: '',
        doctor_in_charge_id: '',
        date_start: moment(new Date()).format('YYYY-MM-DD'),
        date_end: moment(new Date()).format('YYYY-MM-DD'),
      }
        
      this.$store.dispatch('registration/getAntrol',this.params)
    },
    
    rowOnDelete(id) {
    this.$bvModal
    .msgBoxConfirm('Please confirm that you want to cancel antrol.', {
    title: 'Please Confirm', size: 'sm', okVariant: 'primary', okTitle: 'Yes', cancelTitle: 'No', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
    })
    .then(value => {
    if (value === true) {
    this.$store.dispatch('registration/cancelAntrol', id)
    .then(response => {
    if (response.status === 200) {
    this.onListFast()
    this.notification('Success', 'CheckIcon', 'success', 'You have successfully')
    } else {
    this.notification('Failed', 'CloseIcon', 'danger', 'You have unsuccessful')
    }
    },
    error => {
    this.notification('Failed', 'CloseIcon', 'danger', 'You have unsuccessful')
    })
    }
    })
    },

    onListFast() {
    if (this.searchTimeout) {
    clearTimeout(this.searchTimeout);
    }

    this.searchTimeout = setTimeout(() => {
    this.isDisabledFilter = true;

    this.params.norm_like = ""
    this.params.name = ""
    this.params.birth_date = ""

    const input = this.searchQuery.trim();

    if (/^\d{4}-\d{2}-\d{2}$/.test(input)) {
    this.params.birth_date = input
    } else {
    if (/^\d+$/.test(input)) {
    this.params.norm_like = input
    }
    else {
    this.params.name = input
    }
    }

    if (this.requestCancelToken) {
    // console.log("Membatalkan request sebelumnya...")
    this.requestCancelToken.cancel("Request sebelumnya dibatalkan.")
    }

    this.requestCancelToken = axios.CancelToken.source()
    // console.log("Mengirim request baru...", this.params)
    // console.log('this.requestCancelToken: ', this.requestCancelToken)

    this.$store.dispatch("registration/getAntrol", { params: this.params, cancelToken: this.requestCancelToken.token })
    .then((res) => {
    this.ListItems = res.rows
    this.totalRows = res.iTotal
    this.isDisabledFilter = false

    let filterParams = {
    norm_like: this.params.norm_like,
    name: this.params.name,
    birth_date: this.params.birth_date,
    health_facility_item_id: this.params.health_facility_item_id,
    doctor_in_charge_id: this.params.doctor_in_charge_id,
    exit_id:this.params.exit_id,
    date_start: this.params.date_start,
    date_end: this.params.date_end
    }
    localStorage.setItem("checkoutpatientfiltersession", JSON.stringify(filterParams))
    })
    .catch((err) => {
    if (axios.isCancel(err)) {
    console.log("Request dibatalkan:", err.message)
    } else {
    console.error("Error saat request:", err)
    }
    this.isDisabledFilter = false
    })
    }, 300)
    },
  },
  created() {
    this.onListFast()
    this.$store.dispatch('healthfacility/getParam', 'healthfacility')
    // this.$store.dispatch('healthfacilityitem/getParam', '')
  }
}
</script>
<style lang="scss">@import '@core/scss/vue/libs/vue-select.scss';</style>