<template>
  <div>
    <div class="px-3">
      <div class="card">
        <div class="card-header font-weight-bold bg-primary-200">Filter Data</div>
        <div class="card-header">
          <div class="row">
            <div class="col-lg-3">
                <label class="c-form-label">Tanggal Mulai</label>
                <b-form-input type="date" v-model="params.date_start"></b-form-input>
            </div>
            <div class="col-lg-3">
                <label class="c-form-label">Tanggal Akhir</label>
                <b-form-input type="date" v-model="params.date_end"></b-form-input>
            </div>
          </div>
          <div class="d-flex mt-24">
            <b-button @click="onDoctorFilter" :disabled="isDisabledFilter" variant="primary"><feather-icon icon="SearchIcon" /> Cari</b-button>
            <b-button @click="filterReset" :disabled="isDisabledFilter" variant="outline-primary" class="ml-2">
                <feather-icon icon="XIcon" /> Clear Filter
            </b-button>
          </div>
        </div>
      </div>
      <ModernLabel label="Today's Worklist" class="mt-20" />
      <section class="mt-20">
        <div class="row mx-0 justify-content-between">
            <b-form-group label-cols="6" label-align="left" label-size="sm" label-for="sortBySelect" class="text-nowrap mb-md-0 mr-1">
                <span class="text-regular-14">Tampilkan</span>
                <b-form-select id="per-page-select" v-model="perPage" size="sm" :options="pageOptions"/>
                <span class="text-regular-14">Entri</span>
            </b-form-group>
            <div v-if="active.tab !== 'tab2'"class="col-12 col-lg-3 d-flex align-items-center position-relative">
                <b-form-input size="sm" autocomplete="off" placeholder="Cari dengan Nama Pasien" v-model="params.name" @input="onSearch" class="rounded-pill pl-5"></b-form-input>
                <b-icon icon="search" class="position-absolute pl-3 text-neutral-400" />
          </div>
        </div>
        <b-table bordered responsive small head-variant="light" :items="items" :fields="NurseTable.fields_2" :per-page="0" :current-page="currentPage" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="mt-12 text-center">
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
          <template #cell(insurance_name)="row">
            <div>
                {{ row.item.how_to_pay }} | {{ row.item.insurance_name }}
                <b-badge v-if="row.item.is_cob == 1" class="rounded text-neutral-500 text-uppercase border border-warning-500 px-10 py-1" variant="warning-200">COB</b-badge>
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
              <b-badge v-if="row.item.sts == 3" variant="info-500" class="p-2">Menunggu TTV [Perawat]</b-badge>
              <b-badge v-else-if="row.item.sts == 4" variant="success-500" class="p-2">Dokter</b-badge>
              <b-badge v-else-if="row.item.sts == 5" variant="success-500" class="p-2">Menunggu Farmasi</b-badge>
              <b-badge v-else-if="row.item.sts == 6" variant="success-500" class="p-2">Layanan Farmasi</b-badge>
              <b-badge v-else-if="row.item.sts == 7" variant="success-500" class="p-2">Pasien Pulang</b-badge>
              <b-badge v-else variant="warning-500" class="p-2">{{ row.item.sts }}</b-badge>
            </div>
          </template>
          <template #cell(encounter)="row">
              <router-link :to="{ path: '/patient/detail/'+ row.item.registration_destination_id}">
                <b-button size="sm" variant="outline-primary"><feather-icon icon="FileIcon" /></b-button>
              </router-link>
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
        tab: 'tab3',
      },
      searchtable:'',
      params: {
        offset: 0,
        limit: 20,
        code: '',
        norm: '',
        name: '',
        sex: '',
        how_to_enter_id: '',
        is_new: '',
        health_facility_id: '444b3134-5b75-4b67-af72-a558d46716d7',
        health_facility_item_id: '',
        doctor_in_charge_id: JSON.parse(localStorage.getItem('userData')).user_group.name === 'Doctor' ? JSON.parse(localStorage.getItem('userData')).employee_id : '',
        date_start: moment(new Date()).format('YYYY-MM-DD'),
        date_end: moment(new Date()).format('YYYY-MM-DD'),
      },
      currentPage:1,
      perPage: 20,
      firstRows: 1,
      lastRows: 20,
      isDisabled: false,
      isDisabledFilter: false,
      show: false,
      NurseTable: {
        fields_2: [
          { key: 'code', label: 'No Pendaftaran' },
          { key: 'norm', label: 'No RM' },
          { key: 'name', label: 'Nama' },
          { key: 'gender_name', label: 'Kelamin - Umur' },
          { key: 'health_facility_item_name', label: 'Poliklinik/Ruangan' },
          { key: 'insurance_name', label: 'Cara Bayar' },
          { key: 'is_new', label: 'Lama/Baru' },
          { key: 'status', label: 'Status' },
          { key: 'encounter', label: 'Encounter' },
        ],
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
        pageOptions: state => state.registration.pageOptions,
    }),
    // itemsdata() {
    //   return this.items.filter((data) => {
    //     const computedOpItemObj = { ...data}
    //     return Object.keys(computedOpItemObj)
    //     .some(key => ('' + computedOpItemObj[key]).toLowerCase().includes(this.searchtable.toLowerCase()))
    //   })
    // }
  },
  methods: {
    onSearch(search) {
      if(search.length >= 3) {
        this.$store.dispatch('registration/getAllFast', this.params)
      } else {
        this.params.name = ''
        this.$store.dispatch('registration/getAllFast', this.params)
      }
    },
    onDoctorFilter () {
      this.show = true
      this.isDisabledFilter = true
      
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
        this.params = {
            offset: 0,
            limit: this.perPage,
            code: '',
            norm: '',
            name: '',
            sex: '',
            type_how_to_enter_id: '',
            is_new: '',
            health_facility_id: '444b3134-5b75-4b67-af72-a558d46716d7',
            health_facility_item_id: '',
            doctor_in_charge_id: JSON.parse(localStorage.getItem('userData')).user_group.name === 'Doctor' ? JSON.parse(localStorage.getItem('userData')).employee_id : '',
            date_start: moment(new Date()).format('YYYY-MM-DD'),
            date_end: moment(new Date()).format('YYYY-MM-DD'),
        }
        
        this.$store.dispatch('registration/getAllFast',this.params)

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
  },
  mounted() {
    this.socket.on('GET_WORKORDER', (data) => {
      this.$store.dispatch('registration/getAllFast', this.params)
    });
  },
  created() {
    this.$store.dispatch('registration/getAllFast', this.params)
  }
}
</script>