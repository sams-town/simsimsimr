<template>
  <div class="container-fluid custom-text">
    <div class="row h-100">
      <!-- Left Column -->
      <div class="col-6 left-panel">
        <!-- Header -->
        <div class="row no-gutters">
          <div class="col-5">
            <h1 class="fs-50 font-weight-bold">NO ANTRIAN</h1>
          </div>
          <div class="col-7">
            <h1 class="fs-50 font-weight-bold">POLIKLINIK</h1>
          </div>
        </div>
        <div class="white-line mt-4 mb-5"></div>

        <!-- Queue Item -->
        <template v-for="(d, index) in display">
          <div class="queue-item">
            <div class="row">
              <div class="col-3 font-weight-bold">
                <div class="fs-80">
                  {{d.queue}}
                </div>
              </div>
              <!-- Middle: Clinic and Doctor info -->
              <div class="col">
                <div class="mb-2 fs-50 lh-60 font-weight-medium">
                  <div>{{ formatTitle(d.health_facility_item_name) }}</div>
                </div>
                <div class="fs-25">
                  {{ d.doctor_name }}
                </div>
              </div>
              <div class="doctor-photo-container">
                <img v-if="d?.photo_sm" :src="urlbe + d.photo_sm" alt="Doctor Photo" class="doctor-photo">
                <img v-else src="@/@core/assets/images/doctor/drNotFoundkecil.png" alt="Doctor Photo" class="doctor-photo">
                <div class="jam-praktik-badge">
                  <div class="fs-20">Jam praktik</div>
                  <div class="fs-22 font-weight-bold">{{trimTime(d.hour_start)}} - {{trimTime(d.hour_end)}}</div>
                </div>
              </div>
            </div>
          </div>
        </template>
      </div>

      <!-- Right Column -->
      <div class="col-6 right-panel">
        <!-- Doctor Info -->
        <div class="d-flex flex-column align-items-center justify-content-center">
          <div class="logo-container">
            <img src="@/@core/assets/images/logo-thb.jpg" alt="Logo" class="logo">
          </div>

          <div class="doctor-photo-container-right mb-5 text-center">
            <img v-if="incoming_message?.photo_md" :src="urlbe + incoming_message.photo_md" alt="Doctor Photo" class="doctor-photo-right">
            <img v-else src="@/@core/assets/images/doctor/drNotFoundbesar.png" alt="Doctor Photo" class="doctor-photo-right">

            <div class="jam-praktik-badge-right">
              <div class="fs-30">Jam praktik</div>
              <div class="fs-30 font-weight-bold">{{trimTime(incoming_message.hour_start)}} - {{trimTime(incoming_message.hour_end)}}</div>
            </div>
          </div>
          <div class="doctor-info-inline">
            <span class="doctor-specialty">Spesialis {{trimPoliklinik(incoming_message.health_facility_item_name)}}</span>
            <span class="doctor-divider"></span>
            <span class="doctor-name">{{ incoming_message.doctor_name }}</span>
          </div>
        </div>

        <!-- Queue Info -->
        <div class="queue-block">
          <div class="queue-label fs-30">No Antrian</div>
          <div class="queue-number">{{ incoming_message.queue ? incoming_message.queue : '-' }}</div>
          <div class="queue-instruction">Silahkan Menuju Poliklinik</div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
// announce
const bell = new Audio(require("@/@core/assets/sound/Airport_Bell.mp3"));
const bellclose = new Audio(require("@/@core/assets/sound/bell-bandara.mp3"));
const nomorantrian = new Audio(require("@/@core/assets/sound/nomor-antrian.mp3"));
const loket = new Audio(require("@/@core/assets/sound/loket.mp3"));
// numbers
const satu = new Audio(require("@/@core/assets/sound/1.mp3"));
const dua = new Audio(require("@/@core/assets/sound/2.mp3"));
const tiga = new Audio(require("@/@core/assets/sound/3.mp3"));
const empat = new Audio(require("@/@core/assets/sound/4.mp3"));
const lima = new Audio(require("@/@core/assets/sound/5.mp3"));
const enam = new Audio(require("@/@core/assets/sound/6.mp3"));
const tujuh = new Audio(require("@/@core/assets/sound/7.mp3"));
const delapan = new Audio(require("@/@core/assets/sound/8.mp3"));
const sembilan = new Audio(require("@/@core/assets/sound/9.mp3"));
const sepuluh = new Audio(require("@/@core/assets/sound/sepuluh.mp3"));
const sebelas = new Audio(require("@/@core/assets/sound/sebelas.mp3"));
const seratus = new Audio(require("@/@core/assets/sound/seratus.mp3"));
const belas = new Audio(require("@/@core/assets/sound/belas.mp3"));
const puluh = new Audio(require("@/@core/assets/sound/puluh.mp3"));
const ratus = new Audio(require("@/@core/assets/sound/ratus.mp3"));
// destinations
const penyakitdalam = new Audio(require("@/@core/assets/sound/ke-poli-penyakit-dalam.mp3"));
const anakpediatric = new Audio(require("@/@core/assets/sound/ke-poli-anak-pediatric.mp3"));
const paru = new Audio(require("@/@core/assets/sound/ke-poli-paru.mp3"));
const rehabilitasimedis = new Audio(require("@/@core/assets/sound/ke-poli-rehabilitasi-medis.mp3"));
const kebidanankandungan = new Audio(require("@/@core/assets/sound/ke-poli-kebidanan-dan-kandungan.mp3"));
const bedahplastik = new Audio(require("@/@core/assets/sound/ke-poli-bedah-plastik.mp3"));
const gigi = new Audio(require("@/@core/assets/sound/ke-poli-gigi.mp3"));
const bedahumum = new Audio(require("@/@core/assets/sound/ke-poli-bedah-umum.mp3"));
const bedahanak = new Audio(require("@/@core/assets/sound/ke-poli-bedah-anak.mp3"));
const jiwa = new Audio(require("@/@core/assets/sound/ke-poli-jiwa.mp3"));
const bedahorthopedi = new Audio(require("@/@core/assets/sound/ke-poli-bedah-orthopedi.mp3"));
const bedahurologi = new Audio(require("@/@core/assets/sound/ke-poli-bedah-urologi.mp3"));
const tht = new Audio(require("@/@core/assets/sound/ke-poli-THT.mp3"));
const syaraf = new Audio(require("@/@core/assets/sound/ke-poli-syaraf.mp3"));
const jantung = new Audio(require("@/@core/assets/sound/ke-poli-jantung.mp3"));
const mata = new Audio(require("@/@core/assets/sound/ke-poli-mata.mp3"));
const kulitkelamin = new Audio(require("@/@core/assets/sound/ke-poli-kulit-dan-kelamin.mp3"));
const btkv = new Audio(require("@/@core/assets/sound/ke-poli-btkv.mp3"));
const bedahdigestif = new Audio(require("@/@core/assets/sound/ke-poli-bedah-digestif.mp3"));
// codes
const a = new Audio(require("@/@core/assets/sound/a.mp3"));
const b = new Audio(require("@/@core/assets/sound/b.mp3"));
const x = new Audio(require("@/@core/assets/sound/b.mp3"));
const c = new Audio(require("@/@core/assets/sound/c.mp3"));
const d = new Audio(require("@/@core/assets/sound/d.mp3"));
const e = new Audio(require("@/@core/assets/sound/e.mp3"));
const f = new Audio(require("@/@core/assets/sound/f.mp3"));
const t = new Audio(require("@/@core/assets/sound/t.mp3"));
const l = new Audio(require("@/@core/assets/sound/l.mp3"));
const g = new Audio(require("@/@core/assets/sound/g.mp3"));
import TopQueueMenu from '@/@core/components/top-menu/TopQueueMenu.vue';
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue';
import io from 'socket.io-client';
import { mapState } from 'vuex';
export default {
  components: {
    TopQueueMenu,ToastificationContent
  },
  data() {
    return {
      print:false,
      isDisabled:false,
      user: '',
      message: '',
      messages: [],
      display: [],
      socket : io(process.env.VUE_APP_SOCKET_IO, { transports: ["websocket", "polling", "flashsocket"],withCredentials: true }),
      userData: JSON.parse(localStorage.getItem('userData')),
      queue_code:'',
      params: {
        limit: 20,
        offset: 0,
        code: '',
        norm: ''
      },
      incoming_message:{},
      urlbe:process.env.VUE_APP_IMAGE,
    }
  },
  computed: {
    ...mapState({
      items: state => state.registration.items,
    }),
  },
  methods: {
    notification (a,b,c,d) {
      this.$toast({
        component: ToastificationContent,
        position: 'top-right',
        props: { title: a, icon: b, variant: c, text: d}
      })
    },
    openFullScreen() {
      const element = document.documentElement;
      if (element.requestFullscreen) {
        element.requestFullscreen();
      } else if (element.mozRequestFullScreen) {
        element.mozRequestFullScreen();
      } else if (element.webkitRequestFullscreen) {
        element.webkitRequestFullscreen();
      } else if (element.msRequestFullscreen) {
        element.msRequestFullscreen();
      }
    },
    getData (id) {
			this.$store.dispatch('queue/getDisplayPoli', id).then(data => {
        this.display = data
        if (this.incoming_message && this.display.length > 0) {
          let tempDisplay = [...this.display]; // clone to avoid mutating while iterating
          let tempIncoming_message = null;

          this.display.forEach((item, index) => {
            if (
              item.code === this.incoming_message.code &&
              item.number === this.incoming_message.number
            ) {
              tempDisplay.splice(index, 1); // remove item from temp copy
              tempIncoming_message = item;  // save the removed item
            }
          });

          if (tempIncoming_message) {
            this.incoming_message = tempIncoming_message;
          }
          this.display = tempDisplay;
        }
      })
    },

    delay(ms) {
      return new Promise((resolve) => setTimeout(resolve, ms));
    },

    playSound(sound) {
      return new Promise((resolve) => {
        sound.play();
        sound.onended = resolve;
      });
    },


    async playSoundByCode(code) {
      const sounds = {
        'A': a,
        'B': b,
        'X': x,
        'C': c,
        'D': d,
        'E': e,
        'F': f,
        'L': l,
        'T': t,
        'G': g,
      };
      for (let i = 0; i < code.length; i++) {
        const char = code.charAt(i);
        const soundToPlay = sounds[char]

        if (soundToPlay) {
          await this.playSound(soundToPlay)
        }
      }
    },

    async playSoundByNumber(number) {
      if (number <= 11) {
        // Handle numbers 1-11 directly
        const sounds = {
          1: satu,
          2: dua,
          3: tiga,
          4: empat,
          5: lima,
          6: enam,
          7: tujuh,
          8: delapan,
          9: sembilan,
          10: sepuluh,
          11: sebelas,
        };
        const soundToPlay = sounds[number];

        if (soundToPlay) {
          await this.playSound(soundToPlay);
        }

      } else if (number < 20) {
        // Handle numbers 12-19
        const unit = number - 10;
        await this.playSoundByNumber(unit);
        await this.playSound(belas);

      } else if (number < 100) {
        // Handle numbers 20-99
        const tens = Math.floor(number / 10);
        const unit = number % 10;

        await this.playSound(this.getTensSound(tens));
        await this.playSound(puluh);

        if (unit > 0) {
          await this.playSoundByNumber(unit);
        }


      } else if (number <200) {
        const hundreds = Math.floor(number / 100)
        const remain = number % 100

        if (hundreds === 1) {
          await this.playSound(seratus);
        }

        if (remain > 0){
          await this.playSoundByNumber(remain);
        }

      } else if (number < 1000) {
        const hundreds = Math.floor(number / 100)
        const remain = number % 100

        await this.playSound(this.getHundredsSound(hundreds));
        await this.playSound(ratus);

        if (remain > 0) {
          await this.playSoundByNumber(remain);
        }
      }

    },

    getHundredsSound(hundreds) {
      const sounds = {
        1: seratus,
        2: dua,
        3: tiga,
        4: empat,
        5: lima,
        6: enam,
        7: tujuh,
        8: delapan,
        9: sembilan,
      };
      return hundreds === 1 ? sounds[1] : sounds[hundreds];
    },

    getTensSound(tens) {
      const sounds = {
        2: dua,
        3: tiga,
        4: empat,
        5: lima,
        6: enam,
        7: tujuh,
        8: delapan,
        9: sembilan,
      };
      return sounds[tens];
    },

    playSoundByPoli(code) {
      const sounds = {
        'AB': anakpediatric,
        'AC': penyakitdalam,
        'AD': kebidanankandungan,
        'AE': anakpediatric,
        'AF': bedahorthopedi,
        'LD': bedahorthopedi,
        'BA': bedahanak,
        'BB': anakpediatric,
        'BC': paru,
        'DT': paru,
        'BD': bedahumum,
        'BX': bedahumum,
        'BE': syaraf,
        'BF': kebidanankandungan,
        'CA': syaraf,
        'CB': mata,
        'CD': bedahplastik,
        'CE': kulitkelamin,
        'CF': kulitkelamin,
        'DA': penyakitdalam,
        'DB': kebidanankandungan,
        'DC': penyakitdalam,
        'DD': bedahurologi,
        'DE': tht,
        'DF': jantung,
        'EB': rehabilitasimedis,
        'EC': bedahumum,
        'ED': gigi,
        'EE': rehabilitasimedis,
        'EF': anakpediatric,
        'FA': jiwa,
        'FB': penyakitdalam,
        'FC': penyakitdalam,
        'BT': btkv,
        'TB': btkv,
        'DG': bedahdigestif,
      };
      const soundToPlay = sounds[code];
      if (soundToPlay) {
        return this.playSound(soundToPlay);
      }
      return Promise.resolve();
    },

    async getDataSound(id, data) {
      try {
        if (data.status === 201) {
          const res = await this.$store.dispatch('queue/getDisplayPoliById', {display_id:id,id:data.data.id});
          if (res) {
            console.log(res)
            // await this.playSound(bell)

            if (res.code) {
              await this.playSound(nomorantrian)

              await this.playSoundByCode(res.code)

              await this.playSoundByNumber(res.number)

              await this.playSoundByPoli(res.code)
            }

            // await this.playSound(bell)
          }
        } else {
          console.log(data.status);
        }
      } catch (error) {
        this.notification('Failed', 'CloseIcon', 'danger', 'Something Wrong!');
      }
    },
    // async getDataSound(id, data) {
    //   console.log('id: ', id)
    //   console.log('data: ', data)
    //   try {
    //     if (data.status === 201) {
    //       this.$store.dispatch('queue/getDisplayPoliById',{display_id:id,id:data.data.id}).then(res => {
    //         if (res) {
    //           setTimeout(() => { bell.play();}, 100);
    //           setTimeout(() => { nomorantrian.play();}, 9000);
    //           if (res.code == 'DA') {
    //             setTimeout(() => { d.play();}, 10000);
    //             setTimeout(() => { a.play();}, 10500);
    //           } else if (res.code == 'FC') {
    //             setTimeout(() => { f.play();}, 10000);
    //             setTimeout(() => { c.play();}, 10500);
    //           } else if (res.code == 'C') {
    //             setTimeout(() => { c.play();}, 10500);
    //           } else if (res.code == 'D') {
    //             setTimeout(() => { d.play();}, 10500);
    //           } else if (res.code == 'E') {
    //             setTimeout(() => { e.play();}, 10500);
    //           } else if (res.code == 'F') {
    //             setTimeout(() => { f.play();}, 10500);
    //           }
    //           if (res.number == 1) {
    //             setTimeout(() => { satu.play();}, 11500);
    //           } else if (res.number == 2) {
    //             setTimeout(() => { dua.play();}, 11500);
    //           } else if (res.number == 3) {
    //             setTimeout(() => { tiga.play();}, 11500);
    //           } else if (res.number == 4) {
    //             setTimeout(() => { empat.play();}, 11500);
    //           } else if (res.number == 5) {
    //             setTimeout(() => { lima.play();}, 11500);
    //           } else if (res.number == 6) {
    //             setTimeout(() => { enam.play();}, 11500);
    //           } else if (res.number == 7) {
    //             setTimeout(() => { tujuh.play();}, 11500);
    //           } else if (res.number == 8) {
    //             setTimeout(() => { delapan.play();}, 11500);
    //           } else if (res.number == 9) {
    //             setTimeout(() => { sembilan.play();}, 11500);
    //           } else if (res.number == 10) {
    //             setTimeout(() => { sepuluh.play();}, 11500);
    //           } else if (res.number == 11) {
    //             setTimeout(() => { sebelas.play();}, 11500);
    //           } else if (res.number == 12) {
    //             setTimeout(() => { dua.play();}, 11500);
    //             setTimeout(() => { belas.play();}, 12500);
    //           }
    //           setTimeout(() => { penyakitdalam.play();}, 13500);
    //           setTimeout(() => { bellclose.play();}, 15000);
    //         }
    //       })
    //     } else{
    //       console.log('err going here')
    //     }
    //   } catch (error) {
    //     this.notification('Failed', 'CloseIcon', 'danger', 'Something Wrong!');
    //   }
    // }
    formatTitle(str) {
      if (!str) return '';
      return str
        .toLowerCase()
        .replace(/\b\w/g, c => c.toUpperCase());
    },
    trimPoliklinik(text) {
      if (!text) return '';
      return this.formatTitle(text.replace(/^POLIKLINIK\s*/i, '').trim());
    },
    trimTime(text){
      if (!text) return '';
      return text.slice(0, 5);
    }
  },
  mounted() {
    if (this.$route.params.id){
      this.getData(this.$route.params.id)
      this.socket.on('GET_DISPLAY_POLI', async (data) => {
        // console.log(data)
        if (data.status === 201) {
          const res = await this.$store.dispatch('queue/getDisplayPoliById', {display_id:this.$route.params.id,id:data.data.id});
          if (res) {
            this.incoming_message = res
          }
        } else {
          console.log(data.status);
        }
        this.getData(this.$route.params.id)
        this.getDataSound(this.$route.params.id, data)
      });
    }
  }
}
</script>
<style scoped>
.left-panel {
  background-color: #2ea7c6;
  color: white;
  padding: 30px 30px;
  height: 100vh;
  /* overflow-y: scroll;
  overflow-x: hidden; */

  overflow: visible !important; /* allow overflow */
  position: relative; /* for z-index stacking */
}

.queue-item {
  background-color: rgba(217,217,217,0.15);
  border-radius: 30px;
  padding: 15px;
  margin-bottom: 20px;
}

.queue-item .row {
  display: flex;
  align-items: center;
  flex-wrap: nowrap;
}

.white-line {
  height: 2px;
  background-color: white;
  width: 90%;
}

.doctor-photo-container {
  position: relative;
  display: inline-block;
  z-index: 10;
}

.doctor-photo {
  object-fit: cover;
  position: relative;
  transform: translate(-15%, -10%);
  width: 180px;
}

.jam-praktik-badge {
  position: absolute;
  bottom: 0;
  right: 0;
  transform: translate(-30%, 35%);
  background-color: #2ABEAF;
  color: white;
  border-radius: 5px;
  padding: 4px 8px;
  text-align: center;
  line-height: 1.2;
}

.right-panel {
  background-color: #f7fdfd;
  background-image: url('../../../../@core/assets/images/backapexa.png');
  background-repeat: no-repeat;
  background-size: contain;
  background-position: center right;
  height: 100vh;
  padding: 120px 0;

  flex-direction: column;
  position: relative;
}

.logo-container {
  position: absolute;
  top: 40px;
  right: 20px;
}

.logo {
  height: 75px;
}

.doctor-photo-container-right {
  position: relative;
  z-index: 10;
}

.doctor-photo-right {
  object-fit: cover;
  position: relative;
  width: 404px;
}

.jam-praktik-badge-right {
  position: absolute;
  bottom: 0;
  right: 0;
  transform: translate(40%, 20%);
  background-color: #2ABEAF;
  color: white;
  border-radius: 5px;
  padding: 4px 8px;
  text-align: center;
  line-height: 1.2;
  width: 233px;
  height: 83px;
}


.doctor-info-inline {
  display: flex;
  align-items: center;
  font-size: 35px; /* same as fs-35 */
  color: #2ea7c6;
  width: 75%;
}

.doctor-divider {
  display: inline-block;
  width: 1px;
  height: 1em; /* match font height */
  background-color: #2ea7c6;
  margin: 40px 40px;
}

.doctor-specialty {
  font-weight: 400;
  text-align: right;
}

.doctor-name {
  font-weight: bold;
}


.queue-block {
  margin-top: 50px;
  text-align: center;
}

.queue-label {
  font-size: 50px;
  color: #409EB7;
}

.queue-number {
  font-size: 150px;
  font-weight: bold;
  color: #409EB7;
  line-height: 125%;
}

.queue-instruction {
  font-style: italic;
  color: #409EB7;
  font-size:45px;
}

.ellipsis {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* auxiliary  */
@font-face {
  font-family: 'InterRegular';
  src: url('@/@core/assets/fonts/inter/Inter_24pt-Regular.woff2') format('woff2');
  font-weight: normal;
  font-style: normal;
}

.custom-text {
  font-family: 'InterRegular', sans-serif;
}

.fs-50 {
  font-size:50px;
}

.fs-80 {
  font-size:80px;
}

.lh-60{
  line-height: 60px;
}

.font-weight-medium{
  font-weight: 500;
}

/* For WebKit browsers (Chrome, Edge, Safari) */
.left-panel::-webkit-scrollbar {
  width: 6px;
}

.left-panel::-webkit-scrollbar-track {
  background: transparent;
}

.left-panel::-webkit-scrollbar-thumb {
  background-color: #ffffff66;
  border-radius: 3px;
}
</style>