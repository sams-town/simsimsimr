<template>
  <div>
    <TopQueueMenu selectedMenu="menu4" />
    <div class="mt-32 px-3">
      <section class="card">
        <div class="card-header font-weight-bold bg-primary-200">Filter Data</div>
        <div class="card-body">
          <div class="row">
            <div class="col-lg-3">
                <label class="c-form-label">Nama Pasien</label>
                <b-form-input type="text" v-model="params.name"></b-form-input>
            </div>
          </div>
          <div class="d-flex mt-24">
            <b-button @click="getListFast" :disabled="isDisabledFilter" variant="primary"><feather-icon icon="SearchIcon" /> Cari</b-button>
            <b-button @click="filterReset" :disabled="isDisabledFilter" variant="outline-primary" class="ml-2">
                <feather-icon icon="XIcon" /> Clear Filter
            </b-button>
          </div>
        </div>
      </section>

      <ModernLabel label="Today's Worklist" class="mt-20" />

      <div class="row">
          <b-form-group label-cols="6" label-align="left" label-size="sm" label-for="sortBySelect" class="text-nowrap mb-md-0 mr-1">
              <span class="text-regular-14">Tampilkan</span>
              <b-form-select id="per-page-select" v-model="perPage" size="sm" :options="pageOptions"/>
              <span class="text-regular-14">Entri</span>
          </b-form-group>
      </div>

      <section class="mt-20">
        <b-table bordered responsive small head-variant="light" :items="itemsdata" :fields="NurseTable"  sort-by="queue" :sort-asc="true" :per-page="0" :current-page="currentPage" thead-class="text-bold-14" :tbody-tr-class="rowClass" class="mt-12 text-center">
          <template #cell(name)="row">
            <div class="text-left">
              <span>[{{ row.item.alias }}] </span>
              <span>{{ row.item.name }}</span>
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
      pageOptions: [20, 50, 100],
      params: {
        offset: 0,
        limit: 50,
        is_checkout: 1,
        name: '',
        health_facility_id: '40f06534-07ec-4221-ad9d-dea08196079f', // default value rawt inap
        doctor_in_charge_id: '',
        // doctor_in_charge_id: JSON.parse(localStorage.getItem('userData')).user_group?.name == 'Doctor' ? JSON.parse(localStorage.getItem('userData')).position_id !== 'd93bd445-df1e-451a-bf95-5fc10546c133' ? JSON.parse(localStorage.getItem('userData')).employee_id : '' : '',
      },
      currentPage:1,
      perPage: 50,
      firstRows: 1,
      lastRows: 50,
      isDisabled: false,
      isDisabledFilter: false,
      NurseTable: [
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

    }
  },
  watch: {
    currentPage: {
        handler: function(value) {
            this.firstRows = this.perPage * (value - 1) + 1
            this.lastRows = this.perPage * (value)
            this.params.offset = this.perPage * (value - 1)
            this.params.limit = this.perPage
            this.$store.dispatch('registration/getAllFast',this.params)
        }
    },
    perPage: {
        handler: function(value) {
            this.params.limit = this.perPage
            this.$store.dispatch('registration/getAllFast',this.params)
        }
    },
    
  },
  computed: {
    ...mapState({
      items: state => state.registration.fast.items,
      start: state => state.registration.fast.start, 
      totalRows: state => state.registration.fast.totalRows, 
    }),

    itemsdata() {
      return this.items.filter((data) => {
      const computedOpItemObj = { ...data}
      return Object.keys(computedOpItemObj)
        .some(key => ('' + computedOpItemObj[key]).toLowerCase())
      })
    },
    
    // itemsdata() {
    //   return this.items.filter((data) => {
    //   const computedOpItemObj = { ...data}
    //   return Object.keys(computedOpItemObj)
    //     .some(key => ('' + computedOpItemObj[key]).toLowerCase().includes(this.searchtable.toLowerCase()))
    //   })
    // }
  },
  methods: {
    getDoctorId() {
      const userLogin = localStorage.getItem('userData')
      if (userLogin) {
          const parsed = JSON.parse(userLogin);
          return (parsed.user_group.name === 'Doctor' && parsed.position_id !== 'd93bd445-df1e-451a-bf95-5fc10546c133') ? parsed.employee_id : '';
      }
      return ''
    },

    // inpatientFilter(val){
    //   this.isDisabledFilter = true

    //   let poli_id = ''
    //   let doctor_in_charge_id = ''

    //     poli_id = '40f06534-07ec-4221-ad9d-dea08196079f' // rawat inap
    //     doctor_in_charge_id = JSON.parse(localStorage.getItem('userData')).user_group.name === 'Doctor' ? (JSON.parse(localStorage.getItem('userData')).position_id !== 'd93bd445-df1e-451a-bf95-5fc10546c133' ? JSON.parse(localStorage.getItem('userData')).employee_id : '' ) : ''
    //     start_date_param = ''
    //     end_date_param = ''

    //   const inpatient_params = {
    //     offset: 0,
    //     limit: this.perPage,
    //     is_checkout:1,
    //     doctor_in_charge_id:doctor_in_charge_id,
    //     health_facility_id: poli_id,
    //   }
    //   this.$store.dispatch('registration/getAllFast', inpatient_params)
    //   .then(res => {
    //       this.isDisabledFilter = false
    //   },
    //   err => {
    //       this.isDisabledFilter = false
    //   })
    // },
    // onDoctorFilter () {
    //   this.isDisabledFilter = true

    //     this.params.health_facility_id = '40f06534-07ec-4221-ad9d-dea08196079f'
    //     this.params.doctor_in_charge_id = JSON.parse(localStorage.getItem('userData')).user_group.name === 'Doctor' ? (JSON.parse(localStorage.getItem('userData')).position_id !== 'd93bd445-df1e-451a-bf95-5fc10546c133' ? JSON.parse(localStorage.getItem('userData')).employee_id : '' ) : ''
      
    //   this.$store.dispatch('registration/getAllFast', this.params)
    //   .then(res => {
    //       this.isDisabledFilter = false
    //   },
    //   err => {
    //       this.isDisabledFilter = false
    //   })
    // },
    filterReset () {
      let poli_id = ''
      let param_start_date = ''
      let param_end_date = ''
      let param_name = ''

        poli_id = '40f06534-07ec-4221-ad9d-dea08196079f'
        param_start_date = ''
        param_end_date = ''
        param_name = ''


        this.params = {
            offset: 0,
            limit: this.perPage,
            is_checkout:1,
            name: param_name,
            health_facility_id: poli_id,
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

    getListFast() {
      this.isDisabledFilter = true
      try {
        this.$store.dispatch('registration/getAllFast', this.params)
      } catch (error) {
        console.error('error at while GET registration/getAllFast at', error)
      } finally {
        this.isDisabledFilter = false
      }
    },

    rowClass(item, role) {
        if (!item || role !== 'row') return 'text-regular-12'

        let status = Number(item.ews_score)
        if (status >= 1 && status <= 4) return 'table-info-300 text-regular-12'
        if (status >= 5 && status <= 6) return 'table-warning-300 text-regular-12'
        if (status >= 7) return 'table-danger-300 text-regular-12'
        return 'text-regular-12'
    },
  },
  mounted() {
    this.params.doctor_in_charge_id = this.getDoctorId()
    this.socket.on('GET_WORKORDER', (data) => {
      this.getListFast()
    });
  },
  created() {
    this.params.doctor_in_charge_id = this.getDoctorId()
    this.getListFast()
  }
}
</script>