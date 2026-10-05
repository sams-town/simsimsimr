<template>
  <div>
    <TopQueueMenu selectedMenu="menu4" />
    <div class="mt-32 px-3">
      <section class="card">
        <div class="card-header font-weight-bold bg-primary-200">Filter Data</div>
        <div class="card-body">
          <div class="row">
            <template v-if="active.tab !== 'tab2'">
              <div class="col-lg-3">
                  <label class="c-form-label">Tanggal Mulai</label>
                  <b-form-input type="date" v-model="params.date_start"></b-form-input>
              </div>
              <div class="col-lg-3">
                  <label class="c-form-label">Tanggal Akhir</label>
                  <b-form-input type="date" v-model="params.date_end"></b-form-input>
              </div>
            </template>
            <template v-if="active.tab === 'tab2'" class="row">
              <div class="col-lg-3">
                  <label class="c-form-label">Nama Pasien</label>
                  <b-form-input type="text" v-model="params.name"></b-form-input>
              </div>
            </template>
          </div>
          <div class="d-flex mt-24">
            <b-button @click="onDoctorFilter" :disabled="isDisabledFilter" variant="primary"><feather-icon icon="SearchIcon" /> Cari</b-button>
            <b-button @click="filterReset" :disabled="isDisabledFilter" variant="outline-primary" class="ml-2">
                <feather-icon icon="XIcon" /> Clear Filter
            </b-button>
          </div>
        </div>
      </section>

      <ModernLabel label="Today's Worklist" class="mt-20" />
      <section class="mt-20">
        <div class="row mx-0">
          <!-- <b-button @click="goToTab('tab1')" size="sm" :variant="active.tab == 'tab1'? 'success' : 'outline-success'" class="mr-2">Rawat Jalan</b-button> -->
          <b-button @click="goToTab('tab2')" size="sm" :variant="active.tab == 'tab2'? 'success' : 'outline-success'" class="mr-2">Rawat Inap</b-button>
          <!-- <b-button @click="goToTab('tab3')" size="sm" :variant="active.tab == 'tab3'? 'success' : 'outline-success'" class="mr-2">IGD</b-button> -->
        </div>
      </section>

      <section class="mt-20">
        <div class="row mx-0 justify-content-between">
            <b-form-group label-cols="6" label-align="left" label-size="sm" label-for="sortBySelect" class="text-nowrap mb-md-0 mr-1">
                <span class="text-regular-14">Tampilkan</span>
                <b-form-select id="per-page-select" v-model="perPage" size="sm" :options="pageOptions"/>
                <span class="text-regular-14">Entri</span>
            </b-form-group>
            <div v-if="active.tab !== 'tab2'"class="col-12 col-lg-3 d-flex align-items-center position-relative">
                <b-form-input size="sm" autocomplete="off" placeholder="Cari Nama Pasien" v-model="searchtable" class="rounded-pill pl-5"></b-form-input>
                <b-icon icon="search" class="position-absolute pl-3 text-neutral-400" />
          </div>
        </div>
        <b-table bordered responsive small head-variant="light" :items="itemsdata" :fields="this.active.tab === 'tab1' ? NurseTable.fields : NurseTable.fields_2"  sort-by="queue" :sort-asc="true" :per-page="0" :current-page="currentPage" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="mt-12 text-center">
          <template #cell(name)="row">
            <div class="text-left">
              <span>[{{ row.item.alias }}] </span>
              <span>{{ row.item.name }}</span>
              <!-- <span v-if="row.item.reg_type != 'Appointment'" class="text-danger-500"> [ Waiting List ]</span> -->
            </div>
          </template>
          <template #cell(gender_name)="row">
            <div>
              <span>{{ row.item.sex_name }}, </span>
              <span>{{ parsed_age(row.item.birth_date) }}</span>
            </div>
          </template>
          <template #cell(health_facility_item_name)="row">
            <div>
              <span v-if="row.item.bed_name">{{ row.item.room_name }} - {{ row.item.bed_name }} </span>
              <span v-else>{{ row.item.health_facility_item_name }} </span>
            </div>
          </template>
          <template #cell(queue)="row">
            <div>
              <template v-if="row.item.health_facility_name == 'RAWAT INAP' || row.item.health_facility_name == 'IGD'">
                  <span></span>
              </template>
              <template v-else>
              <!-- <template v-else-if ="row.item.queue !== null || row.item.queue !== '' || row.item.queue !== 0"> -->
                  <span>{{ row.item.code_queue }}-{{ row.item.queue }}</span><br>
                  <span>{{ row.item.hour_start }}-{{ row.item.hour_end }}</span>
              </template>
            </div>
          </template>
          <template #cell(reg_type)="row">
            <div>
              <template v-if="row.item.date_task_1 || row.item.health_facility_name !== 'RAWAT JALAN'">
                <span>Walk In</span>
              </template>
              <template v-else>
                <span>Mobile JKN</span>
              </template>
            </div>
          </template>
          <template #cell(how_to_pay)="row">
            {{ row.item.how_to_pay }} {{ row.item.insurance_name ? '| '+row.item.insurance_name : '' }}
            <b-badge v-if="row.item.is_cob == 1"class="rounded text-neutral-500 text-uppercase border border-warning-500 px-10 py-1" variant="warning-200">COB</b-badge>
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
              <b-badge v-if="row.item.sts == 3 && !row.item.call_nurse_stamp" variant="info-500" class="p-2">Menunggu TTV [Perawat]</b-badge>
              <b-badge v-else-if="row.item.sts == 4 || row.item.call_nurse_stamp" variant="success-500" class="p-2">Dokter</b-badge>
              <b-badge v-else-if="row.item.sts == 5" variant="success-500" class="p-2">Menunggu Farmasi</b-badge>
              <b-badge v-else-if="row.item.sts == 6" variant="success-500" class="p-2">Layanan Farmasi</b-badge>
              <b-badge v-else-if="row.item.sts == 7" variant="success-500" class="p-2">Pasien Pulang</b-badge>
              <b-badge v-else variant="warning-500" class="p-2">{{ row.item.sts }}</b-badge>
            </div>
          </template>
          <template #cell(encounter)="row">
            <router-link :to="{ path: '/patient/doctor/'+ row.item.registration_destination_id}" v-if="row.item.health_facility_id === '444b3134-5b75-4b67-af72-a558d46716d7' || row.item.health_facility_id === '40f06534-07ec-4221-ad9d-dea08196079f' || row.item.sts == 4 || row.item.sts == 5 || row.item.sts == 6 || row.item.sts == 7">
              <b-button size="sm" variant="outline-primary"><feather-icon icon="FileIcon" /></b-button>
            </router-link>
            <!-- <router-link :to="{ path: '/prints/doctor-studies-print/'+ row.item.id}" target="_blank">
              <b-button size="sm" variant="outline-success-500" class="px-16 mr-1">
                <feather-icon icon="PrinterIcon" size="12" class="mr-1" /> 
                <span class="fs-12">ASESMEN</span>
              </b-button>
            </router-link> -->
          </template>
          <template #cell(action)="row">
            <div v-if="row.item.enc_type == 'Follow Up'" class="d-flex">
              <b-button size="sm" variant="primary"><feather-icon icon="PlayIcon" /></b-button>
            </div>
            <div v-else class="d-flex">
              <b-button size="sm" variant="primary" :disabled="isDisabled" @click.prevent="action(row.item, 'call')" class="mr-1"><feather-icon icon="Volume2Icon" /></b-button>
            </div>
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
    </div>
  </div>
</template>
<script>
import moment from 'moment'
import { mapState } from 'vuex'
import TopQueueMenu from '@/@core/components/top-menu/TopQueueMenu.vue';
import ModernLabel from '@/@core/components/label/ModernLabel.vue';
import io from 'socket.io-client';

export default {
  components: {
    TopQueueMenu, ModernLabel
  },
  data() {
    return {
      userData: JSON.parse(localStorage.getItem('userData')), 
      socket : io(process.env.VUE_APP_SOCKET_IO, { transports: ["websocket", "polling", "flashsocket"],withCredentials: true }),
      active: {
        tab: 'tab2',
      },
      searchtable:'',
      params: {
        offset: 0,
        limit: 50,
        is_checkout: 1,
        code: '',
        norm: '',
        name: '',
        sex: '',
        how_to_enter_id: '',
        is_new: '',
        health_facility_id: '3cb626f5-9148-4fb1-8663-3d7f028b01a6', // default value rawt jalan
        health_facility_item_id: '',
        // doctor_in_charge_id: JSON.parse(localStorage.getItem('userData')).user_group.name === 'Doctor' ? JSON.parse(localStorage.getItem('userData')).employee_id : '',
        doctor_in_charge_id: this.userData?.user_group?.name == 'Doctor' ? this.userData?.employee_id : '',
        date_start: moment(new Date()).format('YYYY-MM-DD'),
        date_end: moment(new Date()).format('YYYY-MM-DD'),
      },
      currentPage:1,
      perPage: 50,
      firstRows: 1,
      lastRows: 50,
      isDisabled: false,
      isDisabledFilter: false,
      show: false,
      NurseTable: {
        fields: [
          { sortable: true, key: 'queue', label: 'No Antrian' },
          { key: 'code', label: 'No Pendaftaran' },
          { key: 'norm', label: 'No RM' },
          { key: 'name', label: 'Nama' },
          { key: 'gender_name', label: 'Kelamin - Umur' },
          { key: 'reg_type', label: 'Reg. Tipe' },
          { key: 'health_facility_item_name', label: 'Poliklinik/Ruangan' },
          { key: 'how_to_pay', label: 'Cara Bayar' },
          { key: 'is_new', label: 'Lama/Baru' },
          { key: 'status', label: 'Status' },
          { key: 'encounter', label: 'Encounter' },
          { key: 'action', label: 'Aksi' },
        ],
        fields_2: [
          { key: 'code', label: 'No Pendaftaran' },
          { key: 'norm', label: 'No RM' },
          { key: 'name', label: 'Nama' },
          { key: 'gender_name', label: 'Kelamin - Umur' },
          { key: 'reg_type', label: 'Reg. Tipe' },
          { key: 'health_facility_item_name', label: 'Poliklinik/Ruangan' },
          { key: 'how_to_pay', label: 'Cara Bayar' },
          { key: 'is_new', label: 'Lama/Baru' },
          { key: 'status', label: 'Status' },
          { key: 'encounter', label: 'Encounter' },
          { key: 'action', label: 'Aksi' },
        ],
        // items: [
        //   { plasma_id: '1', queue: '5', record: '08-00-07-70', name: 'Ghazy Muhammad', waiting_list: true, gender_name: 'Male', age: '10 Years', reg_type: 'Walk In', enc_type: 'Follow Up', status: 'Waiting' },
        //   { plasma_id: '1', queue: '5', record: '08-00-07-70', name: 'Ghazy Muhammad', waiting_list: true, gender_name: 'Male', age: '10 Years', reg_type: 'Appointment', enc_type: 'New', status: 'Engage' },
        //   { plasma_id: '1', queue: '5', record: '08-00-07-70', name: 'Ghazy Muhammad', waiting_list: true, gender_name: 'Male', age: '10 Years', reg_type: 'Walk In', enc_type: 'Follow Up', status: 'Completed' },
        // ]
      }

    }
  },
  watch: {
    currentPage: {
        handler: function(value) {
            this.firstRows = this.perPage * (value - 1) + 1
            this.lastRows = this.perPage * (value)
            this.params.offset = this.perPage * (value - 1)
            this.params.limit = this.perPage

            if (this.active.tab === 'tab1') {
              this.params.health_facility_id = '3cb626f5-9148-4fb1-8663-3d7f028b01a6'  
              this.params.doctor_in_charge_id = JSON.parse(localStorage.getItem('userData')).user_group.name === 'Doctor' ? JSON.parse(localStorage.getItem('userData')).employee_id : '',
              // this.params.date_start = moment(new Date()).format('YYYY-MM-DD')
              // this.params.date_end = moment(new Date()).format('YYYY-MM-DD')
              this.params.name = ''

            } else if (this.active.tab === 'tab2') {
              this.params.health_facility_id = '40f06534-07ec-4221-ad9d-dea08196079f'  
              this.params.doctor_in_charge_id = JSON.parse(localStorage.getItem('userData')).user_group.name === 'Doctor' ? (JSON.parse(localStorage.getItem('userData')).position_id !== 'd93bd445-df1e-451a-bf95-5fc10546c133' ? JSON.parse(localStorage.getItem('userData')).employee_id : '' ) : ''
              // this.params.date_start = ''
              // this.params.date_end = ''
              
            } else if (this.active.tab === 'tab3') {
              this.params.health_facility_id = '444b3134-5b75-4b67-af72-a558d46716d7'  
              this.params.doctor_in_charge_id = JSON.parse(localStorage.getItem('userData')).user_group.name === 'Doctor' ? JSON.parse(localStorage.getItem('userData')).employee_id : '',
              // this.params.date_start = moment(new Date()).format('YYYY-MM-DD')
              // this.params.date_end = moment(new Date()).format('YYYY-MM-DD')
              this.params.name = ''
              
            } else {
              this.params.health_facility_id = '3cb626f5-9148-4fb1-8663-3d7f028b01a6'  
              this.params.doctor_in_charge_id = JSON.parse(localStorage.getItem('userData')).user_group.name === 'Doctor' ? JSON.parse(localStorage.getItem('userData')).employee_id : '',
              // this.params.date_start = moment(new Date()).format('YYYY-MM-DD')
              // this.params.date_end = moment(new Date()).format('YYYY-MM-DD')
              this.params.name = ''
            }

            this.$store.dispatch('registration/getAllFast',this.params)
        }
    },
    perPage: {
        handler: function(value) {
            this.params.limit = this.perPage

            if (this.active.tab === 'tab1') {
              this.params.health_facility_id = '3cb626f5-9148-4fb1-8663-3d7f028b01a6'  
              this.params.doctor_in_charge_id = JSON.parse(localStorage.getItem('userData')).user_group.name === 'Doctor' ? JSON.parse(localStorage.getItem('userData')).employee_id : '',
              // this.params.date_start = moment(new Date()).format('YYYY-MM-DD')
              // this.params.date_end = moment(new Date()).format('YYYY-MM-DD')
              this.params.name = ''

            } else if (this.active.tab === 'tab2') {
              this.params.health_facility_id = '40f06534-07ec-4221-ad9d-dea08196079f'  
              this.params.doctor_in_charge_id = JSON.parse(localStorage.getItem('userData')).user_group.name === 'Doctor' ? (JSON.parse(localStorage.getItem('userData')).position_id !== 'd93bd445-df1e-451a-bf95-5fc10546c133' ? JSON.parse(localStorage.getItem('userData')).employee_id : '' ) : ''
              this.params.date_start = ''
              this.params.date_end = ''
              
            } else if (this.active.tab === 'tab3') {
              this.params.health_facility_id = '444b3134-5b75-4b67-af72-a558d46716d7'  
              this.params.doctor_in_charge_id = JSON.parse(localStorage.getItem('userData')).user_group.name === 'Doctor' ? JSON.parse(localStorage.getItem('userData')).employee_id : '',
              // this.params.date_start = moment(new Date()).format('YYYY-MM-DD')
              // this.params.date_end = moment(new Date()).format('YYYY-MM-DD')
              this.params.name = ''
              
            } else {
              this.params.health_facility_id = '3cb626f5-9148-4fb1-8663-3d7f028b01a6'  
              this.params.doctor_in_charge_id = JSON.parse(localStorage.getItem('userData')).user_group.name === 'Doctor' ? JSON.parse(localStorage.getItem('userData')).employee_id : '',
              // this.params.date_start = moment(new Date()).format('YYYY-MM-DD')
              // this.params.date_end = moment(new Date()).format('YYYY-MM-DD')
              this.params.name = ''

            }
            this.$store.dispatch('registration/getAllFast',this.params)
        }
    },
  },
  computed: {
    ...mapState({
      items: state => state.registration.fast.items,
      pageOptions: state => state.registration.pageOptions,
      start: state => state.registration.fast.start, 
      totalRows: state => state.registration.fast.totalRows, 
    }),
    itemsdata() {
      return this.items.filter((data) => {
      const computedOpItemObj = { ...data}
      return Object.keys(computedOpItemObj)
      .some(key => ('' + computedOpItemObj[key]).toLowerCase().includes(this.searchtable.toLowerCase()))
    })
    }
  },
  methods: {
    goToTab(tab) {
      this.params.date_start = moment(new Date()).format('YYYY-MM-DD')
      this.params.date_end = moment(new Date()).format('YYYY-MM-DD')
      this.params.name = ''
      this.searchtable = ''

      this.active.tab = tab
      if (this.active.tab !== 'tab0') {
        this.inpatientFilter(this.active.tab)
      } else {
        this.filterReset()
      }
      
    },
    inpatientFilter(val){
      this.show = true
      this.isDisabledFilter = true

      let poli_id = ''
      let start_date_param = ''
      let end_date_param = ''
      let doctor_in_charge_id = ''

      if (val === 'tab1') {
        poli_id = '3cb626f5-9148-4fb1-8663-3d7f028b01a6' // rawat jalan
        start_date_param = moment(new Date()).format('YYYY-MM-DD')
        end_date_param = moment(new Date()).format('YYYY-MM-DD')
        doctor_in_charge_id = JSON.parse(localStorage.getItem('userData')).user_group.name === 'Doctor' ? JSON.parse(localStorage.getItem('userData')).employee_id : ''
      } else if (val === 'tab2') {
        poli_id = '40f06534-07ec-4221-ad9d-dea08196079f' // rawat inap
        doctor_in_charge_id = JSON.parse(localStorage.getItem('userData')).user_group.name === 'Doctor' ? (JSON.parse(localStorage.getItem('userData')).position_id !== 'd93bd445-df1e-451a-bf95-5fc10546c133' ? JSON.parse(localStorage.getItem('userData')).employee_id : '' ) : ''
        start_date_param = ''
        end_date_param = ''
      } else if (val === 'tab3') {
        poli_id = '444b3134-5b75-4b67-af72-a558d46716d7' // IGD
        doctor_in_charge_id = JSON.parse(localStorage.getItem('userData')).user_group.name === 'Doctor' ? JSON.parse(localStorage.getItem('userData')).employee_id : ''
        start_date_param = moment(new Date()).format('YYYY-MM-DD')
        end_date_param = moment(new Date()).format('YYYY-MM-DD')
      } else {
        poli_id = '3cb626f5-9148-4fb1-8663-3d7f028b01a6'
        doctor_in_charge_id = JSON.parse(localStorage.getItem('userData')).user_group.name === 'Doctor' ? JSON.parse(localStorage.getItem('userData')).employee_id : ''
        start_date_param = moment(new Date()).format('YYYY-MM-DD')
        end_date_param = moment(new Date()).format('YYYY-MM-DD')
      }

      const inpatient_params = {
        offset: 0,
        limit: this.perPage,
        is_checkout:1,
        doctor_in_charge_id:doctor_in_charge_id,
        health_facility_id: poli_id,
        date_start: start_date_param,
        date_end: end_date_param,
      }
      this.$store.dispatch('registration/getAllFast', inpatient_params)
      .then(res => {
          this.show = false
          this.isDisabledFilter = false
      },
      err => {
          this.show = false
          this.isDisabledFilter = false
      })
      console.log('this function is active');
    },
    onDoctorFilter () {
      this.show = true
      this.isDisabledFilter = true

      if (this.active.tab === 'tab1') {
        this.params.health_facility_id = '3cb626f5-9148-4fb1-8663-3d7f028b01a6'
        this.params.doctor_in_charge_id = JSON.parse(localStorage.getItem('userData')).user_group.name === 'Doctor' ? JSON.parse(localStorage.getItem('userData')).employee_id : ''
        this.params.name = ''

      } else if (this.active.tab === 'tab2') {
        this.params.health_facility_id = '40f06534-07ec-4221-ad9d-dea08196079f'
        this.params.doctor_in_charge_id = JSON.parse(localStorage.getItem('userData')).user_group.name === 'Doctor' ? (JSON.parse(localStorage.getItem('userData')).position_id !== 'd93bd445-df1e-451a-bf95-5fc10546c133' ? JSON.parse(localStorage.getItem('userData')).employee_id : '' ) : ''
        this.params.date_start = ''
        this.params.date_end = ''

      } else if (this.active.tab === 'tab3') {
        this.params.health_facility_id = '444b3134-5b75-4b67-af72-a558d46716d7'
        this.params.doctor_in_charge_id = JSON.parse(localStorage.getItem('userData')).user_group.name === 'Doctor' ? JSON.parse(localStorage.getItem('userData')).employee_id : ''
        this.params.name = ''

      } else {
        this.params.health_facility_id = '3cb626f5-9148-4fb1-8663-3d7f028b01a6' // default
        this.params.name = ''
      }
      
      this.$store.dispatch('registration/getAllFast', this.params)
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
      let poli_id = ''
      let param_start_date = ''
      let param_end_date = ''
      let param_name = ''

      if (this.active.tab === 'tab1') {
        poli_id = '3cb626f5-9148-4fb1-8663-3d7f028b01a6'
        param_start_date = moment(new Date()).format('YYYY-MM-DD')
        param_end_date = moment(new Date()).format('YYYY-MM-DD')
        param_name = ''

      } else if (this.active.tab === 'tab2') {
        poli_id = '40f06534-07ec-4221-ad9d-dea08196079f'
        param_start_date = ''
        param_end_date = ''
        param_name = ''

      } else if (this.active.tab === 'tab3') {
        poli_id = '444b3134-5b75-4b67-af72-a558d46716d7'
        param_start_date = moment(new Date()).format('YYYY-MM-DD')
        param_end_date = moment(new Date()).format('YYYY-MM-DD')
        param_name = ''

      } else {
        poli_id = '3cb626f5-9148-4fb1-8663-3d7f028b01a6'
        param_start_date = moment(new Date()).format('YYYY-MM-DD')
        param_end_date = moment(new Date()).format('YYYY-MM-DD')
        param_name = ''
      }

        this.params = {
            offset: 0,
            limit: this.perPage,
            is_checkout:1,
            code: '',
            norm: '',
            name: param_name,
            sex: '',
            type_how_to_enter_id: '',
            is_new: '',
            health_facility_id: poli_id,
            health_facility_item_id: '',
            date_start: param_start_date,
            date_end: param_end_date,
        }
        
        this.$store.dispatch('registration/getAllFast',this.params)

    },
    action (data,action) {
      const formData = {id:data.registration_destination_id,action:action,code_booking:data.code_booking} 
      this.$store.dispatch('queue/formPostPutActionPoli', formData)
      .then(res => {
        console.log('response: ', res);
        this.$store.dispatch('queue/getAll', this.params)
        this.$store.dispatch('registration/getAllFast',this.params)
        this.socket.emit('SEND_WORKORDER', res);
      }, 
      err => {
        this.isDisabled = false
        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
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

      // return `${years} th ${months} bln ${days} hr`;
      return `${years} th`;
    },
    parsed_start_from_date(input_date) {
      return moment(input_date).format('DD-MM-YYYY');
    },
    toggleDoctorTab(item, index) {
      this.getItemData = {}
      this.getItemData = {
        id: item.id,
        name: item.name,
      }

      this.active.tab = this.getItemData.id
    },
  },
  mounted() {
    this.socket.on('GET_WORKORDER', (data) => {
      this.$store.dispatch('registration/getAllFast', this.params)
    });
  },
  created() {
    this.goToTab('tab2');
    // this.$store.dispatch('registration/getAllFast', this.params)
    // this.$store.dispatch('healthfacility/getParam', 'healthfacility')
    // this.$store.dispatch('healthfacilityitem/getParam', '')
  }
}
</script>