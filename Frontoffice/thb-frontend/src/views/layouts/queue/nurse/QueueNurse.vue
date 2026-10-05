<template>
  <div>
    <TopQueueMenu selectedMenu="menu2" />
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
                  <b-button @click="onNurseFilter" :disabled="isDisabledFilter" variant="primary"><feather-icon icon="SearchIcon" class="mr-2" />Cari</b-button>
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
        <b-table bordered responsive small head-variant="light" :items="itemsdata" :fields="NurseTable.fields" :per-page="0" :current-page="currentPage" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="mt-12 text-center">
          <template #cell(name)="row">
            <div class="text-left">
              <span>[{{ row.item.alias_name }}] </span>
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
          <template #cell(insurance_name)="row">
              <div v-for="insurance in row.item.registration_insurance">
                  {{ insurance.insurance_name }} | {{ insurance.company_name }}
                  <b-badge v-if="insurance.is_cob == 1"class="rounded text-neutral-500 text-uppercase border border-warning-500 px-10 py-1" variant="warning-200">COB</b-badge>
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
              <b-badge v-if="row.item.sts == 3 && !row.item.call_nurse_stamp" variant="info-500" class="p-2">Menunggu TTV [Perawat]</b-badge>
              <b-badge v-else-if="row.item.sts == 4 || row.item.call_nurse_stamp" variant="success-500" class="p-2">Dokter</b-badge>
              <b-badge v-else-if="row.item.sts == 5" variant="success-500" class="p-2">Menunggu Farmasi</b-badge>
              <b-badge v-else-if="row.item.sts == 6" variant="success-500" class="p-2">Layanan Farmasi</b-badge>
              <b-badge v-else-if="row.item.sts == 7" variant="success-500" class="p-2">Pasien Pulang</b-badge>
              <b-badge v-else variant="warning-500" class="p-2">{{ row.item.sts }}</b-badge>
            </div>
          </template>
          <template #cell(encounter)="row">
            <!-- <div v-if="row.item.enc_type == 'Follow Up'"> -->
              <router-link :to="{ path: '/patient/nurse/'+ row.item.registration_destination_id}">
                <b-button size="sm" variant="outline-primary"><feather-icon icon="FileIcon" /></b-button>
              </router-link>
              <router-link :to="{ path: '/prints/label/'+ row.item.registration_destination_id}">
                <b-badge size="sm" variant="success-500" class="p-2 font-weight-normal">
                  <feather-icon icon="TagIcon" />
                </b-badge>
              </router-link>
            <!-- </div> -->
          </template>
          <!-- <template #cell(action)="row">
            <div v-if="row.item.enc_type == 'Follow Up'" class="d-flex">
              <b-button size="sm" variant="primary"><feather-icon icon="PlayIcon" /></b-button>
            </div>
            <div v-else class="d-flex">
              <b-button size="sm" variant="primary" class="mr-1"><feather-icon icon="Volume2Icon" /></b-button>
              <b-button size="sm" variant="primary" class="mr-1"><feather-icon icon="PauseIcon" /></b-button>
              <b-button size="sm" variant="primary" class="mr-1"><feather-icon icon="PlayIcon" /></b-button>
              <b-button size="sm" variant="primary" class="mr-1"><feather-icon icon="XIcon" /></b-button>
              <b-button size="sm" variant="primary" class="mr-1"><feather-icon icon="ArrowUpIcon" /></b-button>
              <b-button size="sm" variant="primary" class="mr-1"><feather-icon icon="ArrowDownIcon" /></b-button>
            </div>
          </template> -->
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
  </div>
</template>
<script>
import moment from 'moment'
import { mapState } from 'vuex'
import TopQueueMenu from '@/@core/components/top-menu/TopQueueMenu.vue';
import vSelect from 'vue-select'
import ModernLabel from '@/@core/components/label/ModernLabel.vue';
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
        limit: 20,
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
      active: {
        show_actions: false,
        tab: '',
      },
      // filter: {
      //   health_facility_id: '',
      //   health_facility_item_id: '',
      //   doctor_in_charge_group: [],
      // },
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
          { key: 'encounter', label: 'Encounter' },
          // { key: 'action', label: 'Aksi' },
        ],
        items: [
          // { queue: '1', reg_num: '5', record: '08-00-07-70', name: 'Ghazy Muhammad', waiting_list: true, gender_name: 'Male', age: '10 Years', reg_type: 'Walk In', enc_type: 'Follow Up', status: 'Waiting' },
          // { queue: '1', reg_num: '5', record: '08-00-07-70', name: 'Ghazy Muhammad', waiting_list: true, gender_name: 'Male', age: '10 Years', reg_type: 'Appointment', enc_type: 'New', status: 'Engage' },
          // { queue: '1', reg_num: '5', record: '08-00-07-70', name: 'Ghazy Muhammad', waiting_list: true, gender_name: 'Male', age: '10 Years', reg_type: 'Walk In', enc_type: 'Follow Up', status: 'Completed' },
        ]
      },
      currentPage:1,
      perPage: 20,
      firstRows: 1,
      lastRows: 20,
      isDisabled: false,
      isDisabledFilter: false,
      show: false,

      searchtable:''
    }
  },
  watch: {
    currentPage: {
        handler: function(value) {
            this.firstRows = this.perPage * (value - 1) + 1
            this.lastRows = this.perPage * (value)
            this.params.offset = this.perPage * (value - 1)
            this.params.limit = this.perPage
            this.$store.dispatch('registration/getAll',this.params)
        }
    },
    perPage: {
        handler: function(value) {
            this.params.limit = this.perPage
            this.$store.dispatch('registration/getAll',this.params)
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
      items: state => state.registration.items,
      pageOptions: state => state.registration.pageOptions,
      start: state => state.registration.start, 
      totalRows: state => state.registration.totalRows, 
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
    onSearch(search) {
      if(search.length >= 3) {
        this.$store.dispatch('registration/getAll', this.params)
      } else {
        this.params.name = ''
        this.$store.dispatch('registration/getAll', this.params)
      }
    },
    // sendMessage(e) {
    //   e.preventDefault();

    //   this.socket.emit('SEND_MESSAGE', {
    //     user: this.user,
    //     message: this.message
    //   });
    //   this.message = ''
    // },
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
    // onNurseFilter() {
    //   console.log('filter: ', this.params)
    //   this.active.show_actions = this.filter.doctor_in_charge_group.length > 0 ? true : false

    //   this.getDataFiltered = []
    //   this.params.health_facility_item_id = this.filter.health_facility_item_id.value
    //   this.params.health_facility_id = this.filter.health_facility_id.value

    //   if (this.filter.doctor_in_charge_group.length > 0) {
    //     this.filter.doctor_in_charge_group.forEach((item) => {
    //       console.log(item)
    //       this.params.doctor_in_charge_id = item.value
    //       this.getDataFiltered.push(item)
    //     })
    //   } else {
    //     this.getDataFiltered = []
    //     this.getItemData = {}  
    //   }
    //   this.show = true
    //   this.isDisabledFilter = true
    //   this.$store.dispatch('registration/getAll', this.params)
    //   .then(res => {
    //     this.show = false
    //     this.isDisabledFilter = false
    //   },
    //   err => {
    //     this.show = false
    //     this.isDisabledFilter = false
    //   })
    // },
    onNurseFilter () {
        this.show = true
        this.isDisabledFilter = true
        this.$store.dispatch('registration/getAll', this.params)
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
        
      this.$store.dispatch('registration/getAll',this.params)
    },
    // toggleDoctorTab(item, index) {
    //   this.getItemData = {}
    //   this.getItemData = {
    //     id: item.id,
    //     name: item.name,
    //   }

    //   this.active.tab = this.getItemData.id
    // },
  },
  mounted() {
    // this.socket.on('MESSAGE', (data) => {
    //   this.messages = [...this.messages, data];
    //   console.log(this.messages)
    //   // you can also do this.messages.push(data)
    // });
  },
  created() {
    this.$store.dispatch('registration/getAll', this.params)
    this.$store.dispatch('healthfacility/getParam', 'healthfacility')
    // this.$store.dispatch('healthfacilityitem/getParam', '')
  }
}
</script>
<style lang="scss">@import '@core/scss/vue/libs/vue-select.scss';</style>