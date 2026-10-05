<template>
  <div>
    <template v-if="print === false">
      <!-- <TopQueueMenu selectedMenu="menu1" /> -->
      <div class="text-right" >
        <b-img src="../../../../@core/assets/images/apexa.png"/>
      </div>
      <div class="text-center">
        <b-img src="../../../../@core/assets/images/logo-thb.jpg"/>
      </div>
      <div class="d-flex justify-content-center">
        <div class="row mt-32">
          <section class="card">
            <div class="card-body">
              <section class="row">
                <div class="col-lg-4">
                  <!-- <b-button style="height:200px" :disabled="isDisabled" @click.prevent="onClick(1)" variant="success-200" class="btn-block">
                    <h1>Pasien BPJS</h1>
                  </b-button> -->
                  <b-img @click.prevent="onClick(1)" src="../../../../@core/assets/images/opsibpjs.png"/>
                </div>
                <div class="col-lg-4">
                  <!-- <b-button style="height:200px" :disabled="isDisabled" @click.prevent="onClick(2)" variant="info-300" class="btn-block">
                    <h1>Pasien Umum / Asuransi</h1>
                  </b-button> -->
                  <b-img @click.prevent="onClick(2)" src="../../../../@core/assets/images/opsiumum.png"/>
                </div>
                <div class="col-lg-4">
                  <!-- <b-button style="height:200px" :disabled="isDisabled" @click.prevent="onClick(3)" variant="warning-300" class="btn-block">
                    <h1>Mobile JKN</h1>
                  </b-button> -->
                  <b-img @click.prevent="onClick(3)" src="../../../../@core/assets/images/opsijkn.png"/>
                </div>
              </section>
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
            <p style="font-size:62px">Tiket Antrian</p>
            <p style="font-size:62px">RS Taman Harapan Baru Bekasi</p>
          </section>
          <h1 style="font-size:72px" class="text-center"><b>{{queue_code}}</b></h1>
          <p style="font-size:62px" class="text-regular-14 text-center">( {{ dateNow }} )</p>
        </section>
      </div>
    </template>
  </div>
</template>
<script>
import ModernLabel from '@/@core/components/label/ModernLabel.vue';
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue';
import moment from 'moment';
import io from 'socket.io-client';
import { mapState } from 'vuex';
export default {
  components: {
    ModernLabel
  },
  data() {
    return {
      print:false,
      isDisabled:false,
      user: '',
      message: '',
      messages: [],
      socket : io(process.env.VUE_APP_SOCKET_IO, { transports: ["websocket", "polling", "flashsocket"],withCredentials: true }),
      userData: JSON.parse(localStorage.getItem('userData')),
      queue_code:'',
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
    // printCanvas() {
    //   const element = document.querySelector('canvas');
    //   // const originalContent = document.body.innerHTML;

    //   element.style.height= calc
    //   setTimeout(() => { window.print();}, 100);
    //   setTimeout(() => { this.print = false;}, 200);
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
    onClick(index) {
      this.print = true
      const formData = {type_loket_id:index}
      this.isDisabled = true
      this.$store.dispatch('helper/formPostQueueLoket', formData)
      .then(res => {
        console.log('response: ', res);
        // this.notification('Success','CheckIcon','success','You have successfully')
        if (res.status == 200 || res.status == 201) {
          this.socket.emit('SEND_ANJUNGAN', this.queue_code);
          this.queue_code = res.data.queue
          // const style = document.createElement('style');
          //   style.type = 'text/css';
          //   style.innerHTML = `
          //       @page{
          //         size: 80mm 170mm;
          //         margin: 0;
          //         padding: 0;
          //       }
          //   `;
          // document.head.appendChild(style);
          setTimeout(() => { window.print();}, 100);
          setTimeout(() => { this.print = false;}, 200);
          this.isDisabled = false
        }
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
    // this.$store.dispatch('healthfacility/getParam', 'healthfacility')
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
 @media print {
    @page{
      size: 80mm 170mm;
      margin: 0;
      padding: 0;
    }
    body{
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      /* align-items: center; */
    }
    p {
      font-size: 40px;
    }
    h1 {
      font-size: 44px;
    }

 }
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