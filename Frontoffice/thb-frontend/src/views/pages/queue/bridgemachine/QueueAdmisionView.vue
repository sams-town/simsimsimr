<template>
  <div>
    <Layout
      :layout_breadcrumb_items="[
        { text: 'Home', link: '/' },
        { text: 'Pendaftaran' },
        { text: 'Antrean Admisi' }
      ]">
      <section class="row">
        <section class="col-lg-12">
          <div class="card text-center">
            <div class="card-header font-weight-bold text-neutral-100 bg-primary">
              PANGGIL ANTREAN
              <router-link :to="{ path: '/display-admisi/'}" target="_blank">
                <b-button variant="warning-500" size="sm" class="mr-1">
                  <feather-icon icon="MonitorIcon" />
                </b-button>
              </router-link>
            </div>
            <div class="card-body">
              <!-- <section class="row mt-12">
                <section class="col-lg-4">
                  <div class="card">
                    <div class="card-body">
                      <div class="font-weight-bold">NO ANTRIAN TERAKHIR</div>
                      <div class="text-regular-18 text-neutral-400">18:09:48</div>
                    </div>
                  </div>
                </section>
                <section class="col-lg-4">
                  <div class="card">
                    <div class="card-body">
                      <div class="font-weight-bold">SISA ANTRIAN</div>
                      <div class="text-regular-18 text-neutral-400">18:09:48</div>
                    </div>
                  </div>
                </section>
                <section class="col-lg-4 ">
                  <div class="card mt-12 mt-lg-0">
                    <div class="card-body">
                      <div class="font-weight-bold">TOTAL ANTRIAN</div>
                      <div class="text-regular-18 text-neutral-400">18:12:18</div>
                    </div>
                  </div>
                </section>
              </section> -->
            </div>
          </div>
        </section>
      </section>
      <section class="row">
        <div class="col-lg-8">
          <div class="row">
            <div class="col-lg-6">
              <label class="c-form-label">Status</label>
              <v-select v-model="params.flag" :options="OptionsFlag" @input="onAddLoket($event)" :reduce="OptionsFlag => OptionsFlag.value" label="title" placeholder="-- Status --"></v-select>
            </div>
            <div class="col-lg">
              <label class="c-form-label">Loket</label>
              <v-select v-model="params.loket" :options="OptionsLoket" @input="onAddLoket($event)" :reduce="OptionsLoket => OptionsLoket.value" label="title" placeholder="-- Loket --"></v-select>
            </div>
          </div>
        </div>
      </section>
      <section v-show="active.show_actions" class="mt-20">
        <div class="row mx-0">
          <b-button v-for="(item, index) in getDataFiltered" :key="item.key" @click="toggleDoctorTab({ id:item.value, name: item.title }, index)" :variant="active.tab == item.value ? 'primary' : 'outline-primary'" size="sm" class="mr-1">{{ item.title }}</b-button>
        </div>
      </section>
      <section class="mt-20">
        <!-- <p>Tab: {{ active.tab }} || Name: {{ getItemData.name }} || Id: {{ getItemData.id }}</p> -->
        <ModernLabel :label="getItemData.name ? 'LOKET: ' + getItemData.name : 'LOKET: -'" />
        <b-table bordered responsive small head-variant="light" :items="items" :fields="Table.fields" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="mt-12 text-center">
          <!-- <template #cell(health_facility_item_name)="row">
            <div>
              <span>{{ row.item.health_facility_item_name }}, </span><br>
              <span>{{ row.item.doctor_in_charge_name }}</span>
            </div>
          </template> -->
          <!-- <template #cell(gender_name)="row">
            <div>
              <span>{{ row.item.sex_name }}, </span>
              <span>{{ parsed_age(row.item.birth_date) }}</span>
            </div>
          </template> -->
          <template #cell(queue)="row">
            <div>
              <span>{{row.item.code}}-{{row.item.queue}}</span>
            </div>
          </template>
          <!-- <template #cell(is_new)="row">
            <div v-if="row.item.is_new == 'Y'">
              <span>Pasien Baru</span>
            </div>
            <div v-else-if="row.item.is_new == 'N'">
              <span>Pasien Lama</span>
            </div>
          </template> -->
          <template #cell(flag)="row">
            <div>
              <b-badge v-if="row.item.flag == 1" variant="warning-500" class="p-2">Belum dipanggil</b-badge>
              <b-badge v-else-if="row.item.flag == 2" variant="success-500" class="p-2">Sudah dipanggil</b-badge>
              <b-badge v-else-if="row.item.flag == 3" variant="success-500" class="p-2">Panggil ulang</b-badge>
              <b-badge v-else-if="row.item.flag == 4" variant="success-500" class="p-2">cancel</b-badge>
            </div>
          </template>
          <!-- <template #cell(encounter)="row"> -->
            <!-- <div v-if="row.item.enc_type == 'Follow Up'"> -->
              <!-- <router-link :to="{ path: '/patient/nurse/'+ row.item.registration_destination_id}"> -->
                <!-- <b-button size="sm" @click="playSound()" variant="outline-primary"><feather-icon icon="FileIcon" /></b-button> -->
              <!-- </router-link> -->
            <!-- </div> -->
          <!-- </template> -->
          <template #cell(action)="row">
            <div v-if="row.item.flag === 1 && row.item.loket_id === null" class="d-flex">
              <b-button size="sm" variant="primary" :disabled="isDisabled" @click.prevent="action(row.item, 'call')" class="mr-1"><feather-icon icon="Volume2Icon" /></b-button>
              <b-button size="sm" variant="primary" @click.prevent="regist(row.item.code_booking)" class="mr-1"><feather-icon icon="PlusIcon" /> Daftarkan Pasien</b-button>
              <b-button size="sm" variant="primary" :disabled="isDisabled" @click.prevent="action(row.item, 'ok')" class="mr-1"><feather-icon icon="CheckIcon" /></b-button>
              <b-button size="sm" variant="primary" :disabled="isDisabled" @click.prevent="action(row.item, 'close')" class="mr-1"><feather-icon icon="XIcon" /></b-button>
            </div>
            <div v-if="row.item.flag === 1 && row.item.loket_id !== null" class="d-flex">
              <template v-if="params.loket === row.item.loket_id">
                <b-button size="sm" variant="primary" :disabled="isDisabled" @click.prevent="action(row.item, 'call')" class="mr-1"><feather-icon icon="Volume2Icon" /></b-button>
              </template>
              <b-button size="sm" variant="primary" @click.prevent="regist(row.item.code_booking)" class="mr-1"><feather-icon icon="PlusIcon" /> Daftarkan Pasien</b-button>
              <b-button size="sm" variant="primary" :disabled="isDisabled" @click.prevent="action(row.item, 'ok')" class="mr-1"><feather-icon icon="CheckIcon" /></b-button>
              <b-button size="sm" variant="primary" :disabled="isDisabled" @click.prevent="action(row.item, 'close')" class="mr-1"><feather-icon icon="XIcon" /></b-button>
            </div>
            <div v-else class="d-flex">
            </div>
            <!-- <div v-else class="d-flex"> -->
            <!-- </div> -->
          </template>
        </b-table>
      </section>
    </Layout>
  </div>
</template>
<script>
import moment from 'moment'
import { mapState } from 'vuex'
import TopQueueMenu from '@/@core/components/top-menu/TopQueueMenu.vue';
import vSelect from 'vue-select'
import ModernLabel from '@/@core/components/label/ModernLabel.vue';
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import io from 'socket.io-client';
export default {
  components: {
    TopQueueMenu, vSelect, ModernLabel
  },
  data() {
    return {
      isDisabled:false,
      user: '',
      loket: '4e644d9a-c254-4b74-951e-ab78e82ec057',
      message: '',
      messages: [],
      socket: io(process.env.VUE_APP_SOCKET_IO, {
        transports: ["websocket", "polling"],
        withCredentials: true
    }),
      // socket : io(process.env.VUE_APP_SOCKET_IO, { transports: ["websocket", "polling", "flashsocket"],withCredentials: true }),
      userData: JSON.parse(localStorage.getItem('userData')), 
      params: {
        limit: 100,
        offset: 0,
        flag: 1,
        loket: '4e644d9a-c254-4b74-951e-ab78e82ec057',
        date_start: moment(new Date()).format('YYYY-MM-DD'),
        date_end: moment(new Date()).format('YYYY-MM-DD'),
      },
      active: {
        show_actions: false,
        tab: '',
      },
      filter: {
        health_facility_id: '',
        health_facility_item_id: '',
        doctor_in_charge_group: [],
      },
      getDataFiltered: [],
      getItemData: {},
      isDisabledFilter: false,
      show: false,
      OptionsFlag:[
          { title: 'Belum dipanggil', value: 1 },
          { title: 'Sudah dipanggil', value: 2 },
          { title: 'Panggil ulang', value: 3 },
          { title: 'cancel', value: 4 },
      ],
      Table: {
        fields: [
          { key: 'queue', label: 'No Antrian' },
          { key: 'flag', label: 'Status' },
          // { key: 'name', label: 'Nama' },
          // { key: 'norm', label: 'No RM' },
          { key: 'action', label: 'Aksi' },
        ],
        items: [
          // { queue: '1', reg_num: '5', record: '08-00-07-70', name: 'Ghazy Muhammad', waiting_list: true, gender_name: 'Male', age: '10 Years', reg_type: 'Walk In', enc_type: 'Follow Up', status: 'Waiting' },
          // { queue: '1', reg_num: '5', record: '08-00-07-70', name: 'Ghazy Muhammad', waiting_list: true, gender_name: 'Male', age: '10 Years', reg_type: 'Appointment', enc_type: 'New', status: 'Engage' },
          // { queue: '1', reg_num: '5', record: '08-00-07-70', name: 'Ghazy Muhammad', waiting_list: true, gender_name: 'Male', age: '10 Years', reg_type: 'Walk In', enc_type: 'Follow Up', status: 'Completed' },
        ]
      }
    }
  },
  watch: {
    // 'filter.health_facility_id'(obj) {
    //   if (obj) {
    //     return this.$store.dispatch('healthfacilityitem/getParam', obj.value)
    //   }
    // },
    'filter.health_facility_item_id'(obj) {
      if (obj) {
        return this.$store.dispatch('employee/getParam', obj.value)
      }
    },
  },
  computed: {
    ...mapState({
      items: state => state.queue.items,
      OptionsHealthFacilityItem: state => state.healthfacilityitem.options.healthfacilityitem,
      OptionsDoctor: state => state.employee.options.employee,
      OptionsLoket: state => state.helper.options.loket
    }),
  },
  methods: {
    onAddLoket() {
      this.$store.dispatch('queue/getAll', this.params)
    },
    regist (code_booking) {
      const routeData = this.$router.resolve({ path: '/registration/regist-patient-new/' });
      window.open(routeData.href + code_booking, '_blank');
    },
    action (data,action) {
      if(action == 'call'){
        this.isDisabled = true
      }
      const formData = {id:data.id,loket_id:this.params.loket,action:action} 
      this.$store.dispatch('queue/formPostPutAction', formData)
      .then(res => {
        // console.log('response: ', res);
        this.$store.dispatch('queue/getAll', this.params)
        this.socket.emit('SEND_LOKET', formData);
        if(action == 'call'){
          setTimeout(() => { this.isDisabled = false }, 10000);
        }
      }, 
      err => {
        this.isDisabled = false
        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
      })
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
    onDoctorFilter() {
      // this.socket.emit('SEND_MESSAGE', {
      //   user: 'userasu',
      //   message: 'asu'
      // });
      // this.message = ''
      this.active.show_actions = this.filter.doctor_in_charge_group.length > 0 ? true : false

      this.getDataFiltered = []
      this.params.health_facility_item_id = this.filter.health_facility_item_id.value
      if (this.filter.doctor_in_charge_group.length > 0) {
        this.filter.doctor_in_charge_group.forEach((item) => {
          console.log(item)
          this.params.doctor_in_charge_id = item.value
          this.getDataFiltered.push(item)
        })
      } else {
        this.getDataFiltered = []
        this.getItemData = {}  
      }
      this.show = true
      this.isDisabledFilter = true
    },
    toggleDoctorTab(item, index) {
      this.getItemData = {}
      this.getItemData = {
        id: item.id,
        name: item.name,
      }
      this.active.tab = this.getItemData.id
    },
    notification (a,b,c,d) {
      this.$toast({
        component: ToastificationContent,
        employee: 'top-right',
        props: { title: a, icon: b, variant: c, text: d}
      })
    }
  },
  mounted() {
    this.socket.on('GET_LOKET', (data) => {
      console.log(data)
      this.$store.dispatch('queue/getAll', this.params)
    });
  },
  created() {
    this.$store.dispatch('queue/getAll', this.params)
    this.$store.dispatch('healthfacilityitem/getParam', '')
    this.$store.dispatch('helper/getParamLoket', 'Loket')
    console.log("SOCKET URL:", process.env.VUE_APP_SOCKET_IO);
  }
}
</script>
<style lang="scss">@import '@core/scss/vue/libs/vue-select.scss';</style>