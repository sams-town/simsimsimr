<template>
  <div class="">
    <section class="text-white">
      <div class="position-relative text-center bg-primary p-16">
        <div class="text-bold-18">Informasi Jadwal Operasi RS Taman Harapan Baru</div>
        <div class="text-regular-14" id="clock"></div>

        <div class="d-flex align-items-center justify-content-center position-absolute h-100 px-20 bg-white" style="left: 0; top: 0;">
          <b-img src="../../../../@core/assets/images/apexa.png" width="120px" class="mr-24"/>
        </div>

        <div class="d-flex align-items-center justify-content-center position-absolute h-100 px-20" style="right: 0; top: 0;">
          <b-button @click="fullScreenMode()" v-b-tooltip.hover.left :title="active.tooltip" variant="primary-500">
            <feather-icon :icon="active.tooltip == 'Maximize' ? 'MaximizeIcon' : 'MinimizeIcon'"/>
          </b-button>
        </div>
      </div>
      <div class="bg-w3green text-regular-14 p-8 text-right overflow-hidden">
        <div class="slide-left" style="margin-right: -390px;"></div>
      </div>
    </section>
    <section class="row mx-0 mt-20">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-header bg-primary"></div>
          <b-table bordered responsive :fields="RoomFields" :items="ok_list" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="text-center">
            <template #cell(no)="data">{{ data.index+1 }}</template>
            <template #cell(birth_date)="row">
                <div>
                  {{ parsed_age(row.item.birth_date) }}
                </div>
            </template>
            <template #cell(status)="row">
                <div>
                  {{ row.item.status === 2 ? 'Menunggu' : '' }}
                </div>
            </template>
          </b-table>
        </div>
      </div>
    </section>
  </div>
</template>
<script>
import moment from 'moment';
import 'moment/locale/id';
moment.locale('id');

export default {
  data() {
    return {
      date_now: moment(new Date()).locale("id").format('dddd, Do MMMM YYYY'),
      monitoring_list: [],
      ok_list: [],
      RoomFields: [
        { key: 'no_order', label: 'No.Operasi', sortable: true },
        { key: 'birth_date', label: 'Umur', sortable: true },
        { key: 'sex_name', label: 'Kelamin', sortable: true },
        { key: 'operation_date', label: 'Waktu Operasi', sortable: true },
        { key: 'status', label: 'Status', sortable: true },
        // { key: 'last_updated', label: 'Operator', sortable: true },
        // { key: 'last_updated', label: 'Ruang Operasi', sortable: true }
      ],

      params: {
        status: 2,
        limit: 20,
        offset: 0,
      },
      totalRows: 0,
      active: {
        tooltip: 'Maximize',
      }
    }
  },
  methods: {
    parsed_age(input_date) {
        const birthDate = moment(input_date, 'YYYY-MM-DD');
        const currentDate = moment();

        const years = currentDate.diff(birthDate, 'years');
        birthDate.add(years, 'years');
        const months = currentDate.diff(birthDate, 'months');
        birthDate.add(months, 'months');
        const days = currentDate.diff(birthDate, 'days');

        return `${years} th ${months} bln ${days} hr`;
    },
    getRoomList() {
      if (this.totalRows === 0 || this.params.offset >= this.totalRows) {
        this.params.offset = 0
        this.params.limit = 20
      }
      try {
        this.$store.dispatch('operatie/getAllList', this.params).then(response => {
          this.ok_list = response.rows
          this.totalRows = response.iTotal

          if (this.params.offset + this.params.limit < this.totalRows) {
            this.params.offset += this.params.limit
          } else {
            this.params.offset = 0
          }
        });
      } catch (error) {
        console.log('Error while GET DATA at: ', error);
      }
    },
    getMonitoringClass() {
      try {
        this.$store.dispatch('room/getMonitoringClass').then(response => {
          this.monitoring_list = response
        })
      } catch (error) {
        console.error('Error while GET data from room/getMonitoringClass at: ', error);
      }
    },
    updateClock() {
      const clockElement = document.getElementById('clock');
      if (clockElement) {
        clockElement.innerHTML = moment().locale("id").format('dddd, Do MMMM YYYY H:mm:ss');
      }
    },
    fullScreenMode(){
      if (!document.fullscreenElement &&
        !document.mozFullScreenElement &&
        !document.webkitFullscreenElement &&
        !document.msFullscreenElement) {
        if (document.documentElement.requestFullscreen) {
          document.documentElement.requestFullscreen();
        } else if (document.documentElement.mozRequestFullScreen) {
          document.documentElement.mozRequestFullScreen();
        } else if (document.documentElement.webkitRequestFullscreen) {
          document.documentElement.webkitRequestFullscreen();
        } else if (document.documentElement.msRequestFullscreen) {
          document.documentElement.msRequestFullscreen();
        }
        this.active.tooltip = 'Minimize'
      } else {
        if (document.exitFullscreen) {
          document.exitFullscreen();
        } else if (document.mozCancelFullScreen) {
          document.mozCancelFullScreen();
        } else if (document.webkitExitFullscreen) {
          document.webkitExitFullscreen();
        } else if (document.msExitFullscreen) {
          document.msExitFullscreen();
        }
        this.active.tooltip = 'Maximize'
      }
    },
  },
  created() {
    this.$store.dispatch('helper/getParam', 'type_room')
    this.getMonitoringClass()
    this.getRoomList()
  },
  mounted() {
    this.updateClock();
    setInterval(this.updateClock, 1000)
    this.intervalId = setInterval(() => {
    this.getRoomList()
    this.getMonitoringClass()
    }, 5000);
  },
  beforeDestroy() {
    clearInterval(this.intervalId);
  }
}
</script>

<style scoped>
@import './../../../../@core/css/main/main.min.css';

.slide-left {
  -webkit-animation: slide-left 10s linear ;
  animation: slide-left 10s linear ;
  animation-iteration-count: infinite;
}

@-webkit-keyframes slide-left {
0% {
  -webkit-transform: translateX(0);
  transform: translateX(0);
}
100% {
  -webkit-transform: translateX(-100%);
  transform: translateX(-100%);
}
}
@keyframes slide-left {
0% {
  -webkit-transform: translateX(0);
  transform: translateX(0);
}
100% {
  -webkit-transform: translateX(-100%);
  transform: translateX(-100%);
}
}


</style>