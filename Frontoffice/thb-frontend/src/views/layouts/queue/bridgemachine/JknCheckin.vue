<template>
  <div>
    <template v-if="print === false">
      <div class="row mt-32"><br></div>
      <div class="d-flex justify-content-center">
        <div class="row mt-32">
          <section class="row">
            <div>
              <b-img width="1000%" style="margin-top: -100px;" src="../../../../@core/assets/images/headjkn.png"/>
              <!-- <center>
                <h1>Pendaftaran Online</h1>
                <h1>Pelayanan Rawat Jalan</h1>
                <h4>Masukkan Kode Booking Anda</h4>
              </center><br> --><br><br><br>
              <label><b>Kode Booking</b></label>
              <b-form-input autocomplete="off" style="height:55px;font-size:36px"
                type="number"
                v-model="demo1.value"
              />
              <hr>
              <b-button @click.prevent="onFinger()" variant="primary-500" class="btn-block">
                <h3><b><feather-icon icon="RssIcon" /> Fingerprint BPJS</b></h3>
              </b-button>
              <hr>
              <center>
              <VueNumericKeypad style="position:relative"
                :value.sync="demo1.value"
                :show.sync="demo1.show"
                :options="demo1.options"
              />
              </center><br>
              <template v-if="finger === true">
                <b-button variant="primary-500" @click.prevent="onClick()" class="btn-block">
                  <h3><b><feather-icon icon="SendIcon" /> Selesai</b></h3>
                </b-button>
              </template>
            </div>
          </section>
        </div>
      </div>
    </template>
    <template v-if="print === true">
      <div class="canvas-wrapper">
        <section id="canvas" ref="canvas" class="mt-16 p-40" style="position: relative;">
          <section class="d-flex flex-column justify-content-center text-regular-14 text-center mt-40">
            <!-- <p class="text-uppercase">{{ regData.health_facility_name }}</p> -->
            <h1><p>Tiket Booking</p></h1>
            <h1><p>RS Taman Harapan Baru Bekasi</p></h1>
          </section>
          <p style="font-size:62px" class="text-center">No.RM <b>{{checkin_data.norm}}</b></p>
          <p style="font-size:62px" class="text-center">SEP. <b>{{checkin_data.sep}}</b></p>
          <p style="font-size:62px" class="text-center">Nama <b>{{checkin_data.name}}</b></p>
          <p style="font-size:62px" class="text-center">Dokter <b>{{checkin_data.dpjp}}</b></p>
          <p style="font-size:62px" class="text-center">Poli <b>{{checkin_data.destination}}</b></p>
          <h1 style="font-size:72px" class="text-center"><b>{{checkin_data.queue}}</b></h1>
          <p style="font-size:62px" class="text-regular-14 text-center">( {{ dateNow }} )</p>
        </section>
      </div>
    </template>
  </div>
</template>
<script>
import  VueNumericKeypad  from "vue-numeric-keypad";
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import moment from 'moment'
import { mapState } from 'vuex'
import ModernLabel from '@/@core/components/label/ModernLabel.vue';
import io from 'socket.io-client';
export default {
  components: {
    ModernLabel,VueNumericKeypad
  },
  data() {
    return {
      finger:false,
			demo1: {
				value: "",
				show: 1,
				options: {
					keyRandomize: false,
					randomizeWhenClick: false,
					fixDeleteKey: true,
					fixBlankKey: true,
					pseudoClick: false,
					pseudoClickDeleteKey: false,
					pseudoClickBlankKey: false,
					stopPropagation: true,
					vibrate: false,
					activeButtonDelay: 300,
					vibratePattern: 200,
					keyArray: [1, 2, 3, 4, 5, 6, 7, 8, 9, '', 0, -1],
					deleteKeyText: 'del',
					clearKeyText: 'clr',
				},
			},
      print:false,
      isDisabled:false,
      user: '',
      message: '',
      messages: [],
      socket : io(process.env.VUE_APP_SOCKET_IO, { transports: ["websocket", "polling", "flashsocket"],withCredentials: true }),
      userData: JSON.parse(localStorage.getItem('userData')),
      checkin_data:'',
      params: {
        limit: 20,
        offset: 0,
        code: '',
        norm: ''
      },
    }
  },
  computed: {
    dateNow() {
      const date = new Date();
      const year = date.getFullYear();
      const month = date.getMonth() + 1;
      const day = date.getDate();
      const hour = date.getHours();
      const minute = date.getMinutes();
      const second = date.getSeconds();
      return `${day}-${month}-${year} ${hour}:${minute}:${second}`;
    },
    ...mapState({
      // totalRows: state => state.catalog.totalRows,
    })
  },
  methods: {
    onFinger() {
      this.finger = true
    },
    // printCanvas() {
    // },
    // sendMessage(e) {
    //   e.preventDefault();

    //   this.socket.emit('SEND_MESSAGE', {
    //     user: this.user,
    //     message: this.message
    //   });
    //   this.message = ''
    // },
    notification (a,b,c,d) {
      this.$toast({
        component: ToastificationContent,
        employee: 'top-right',
        props: { title: a, icon: b, variant: c, text: d}
      })
    },
    onClick() {
      const formData = {code_booking:this.demo1.value}
      this.isDisabled = true
      this.$store.dispatch('queue/formPostCheckin', formData)
      .then(res => {
        console.log('response: ', res);
        // this.notification('Success','CheckIcon','success','You have successfully')
        if (res.status == 200 || res.status == 201) {
          this.print = true
          this.checkin_data = res.data
          this.socket.emit('SEND_ANJUNGAN_CHECKIN', this.checkin_data);
          setTimeout(() => { window.print();}, 100);
          setTimeout(() => { this.print = false;}, 200);
        }
        this.isDisabled = false
      },
      err => {
        this.isDisabled = false
        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
      })
    },
  },
  mounted() {
    // const buttons = this.$refs.buttons;
    // buttons.classList.add('d-none');
    // this.socket.on('MESSAGE', (data) => {
    //   this.messages = [...this.messages, data];
    //   console.log(this.messages)
    //   // you can also do this.messages.push(data)
    // });
  },
  created() {
    document.addEventListener('click', function () {
      this.show = 0;
    }.bind(this));
  }
}
</script>
<style>
@import './../../../../@core/css/main/main.min.css';

/* table,
th,
td {
    border: 1px solid black;
} */
td {
    padding: 2px 0;
    vertical-align: text-top;
}

p {
    margin: 0;
    padding: 0;
}

.main-content {
    margin-top: 32px;
    padding-bottom: 32px;
}

.canvas-wrapper {
    display: flex;
    justify-content: center;
}

#canvas {
    width: 21.2cm !important;
    /* height: 29.7cm !important; */
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
}

.canvas-header {
    width: 100%;
}

.canvas-header img:first-child {
    width: 80px;
}

.canvas-header img:last-child {
    width: 100%;
    height: 100%;
    display: block;
    margin-top: -30px;
}

.w-40 {
    width: 40%;
}

.canvas-footer {
    position: absolute;
    bottom: 0;
    /* right: 0; */
}
</style>