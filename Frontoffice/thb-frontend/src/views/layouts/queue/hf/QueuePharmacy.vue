<template>
  <div class="text-white font-inter-bold" style="font-size: 55px;">
      <div class="position-relative">
          <div class="ax-header bg-grad-primary text-center position-relative">
              <div style="font-size: 1em;">INFORMASI ANTRIAN FARMASI</div>
              <div style="font-size: 0.63em;">Berdasarkan Waktu Input Dokter</div>
          </div>
          <div class="d-flex align-items-start justify-content-center position-absolute h-100 px-20" style="left: 64px; top: 32px;">
              <b-img src="../../../../@core/assets/images/logo-thb.jpg" alt="logo-thb-white" />
          </div>
          <div class="d-flex align-items-start justify-content-center position-absolute h-100 px-20" style="right: 64px; top: 32px;">
              <!-- <b-img src="../../../../@core/assets/images/apexa-white.png" width="200px" alt="logo-apexa-white" /> -->
          </div>
      </div>
      <div class="row mx-0 px-80" style="margin-top: -48px;">
          <template v-if="display && display.length > 0">
              <section v-for="(item, index_i) in display" :key="index_i" class="col-lg-4 px-40">
                  <template v-if="index_i < 3">
                      <div class="text-center h-100">
                          <div class="queue card-header font-weight-bold text-white py-24" :class="getHeaderVariant(item.status)" style="font-size: 0.72em;">{{ item.name }}</div>
                          <div class="card-body px-0">
                              <section class="row" style="margin-left: -8px; margin-right: -8px;">
                                  <!-- <template v-if="item.data && item.data.length > 0"> -->
                                      <section v-for="(list, index_j) in item.data" :key="index_j" class="col-lg-4 px-8" :class="index_j > 2 ? 'mt-24' : ''">
                                          <b-aspect class="d-flex align-items-center justify-content-center shadow" :class="getBoxVariant(item.status)" style="border-radius: 20px;">
                                              <div style="font-size: 0.81em;">{{ list.registration_destination_queue }}</div>
                                              <!-- <div style="font-size: 0.81em;">{{ list.queue }}</div> -->
                                          </b-aspect>
                                      </section>
                                      <!-- <section v-for="(n, index_j) in 11" :key="index_j" class="col-lg-4 px-8" :class="index_j > 2 ? 'mt-24' : ''">
                                          <b-aspect class="d-flex align-items-center justify-content-center shadow" :class="getBoxVariant(item.status)" style="border-radius: 20px;">
                                              <div style="font-size: 0.81em;">{{ n*10 }}</div>
                                          </b-aspect>
                                      </section> -->
                                  <!-- </template> -->
                                  <!-- <span v-else class="w-100 text-center">-</span> -->
                              </section>
                          </div>
                      </div>
                  </template>
              </section>
          </template>
      </div>
      <div class="position-fixed" style="left: 0; top: 0; width: 100%; height: 100vh;">
          <div class="position-fixed text-center w-100 text-neutral-900 bg-white px-24" style="bottom: 0; font-size: 0.45em;">
              <span id="live-date" class="text-right mr-2"></span>
              <span id="live-clock" class="text-left"></span>
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
const jiwa = new Audio(require("@/@core/assets/sound/ke-poli-jiwa.mp3"));
const bedahorthopedi = new Audio(require("@/@core/assets/sound/ke-poli-bedah-orthopedi.mp3"));
const bedahurologi = new Audio(require("@/@core/assets/sound/ke-poli-bedah-urologi.mp3"));
const tht = new Audio(require("@/@core/assets/sound/ke-poli-THT.mp3"));
const syaraf = new Audio(require("@/@core/assets/sound/ke-poli-syaraf.mp3"));
const jantung = new Audio(require("@/@core/assets/sound/ke-poli-jantung.mp3"));
const mata = new Audio(require("@/@core/assets/sound/ke-poli-mata.mp3"));
const kulitkelamin = new Audio(require("@/@core/assets/sound/ke-poli-kulit-dan-kelamin.mp3"));
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

// import { mapState } from 'vuex'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue';
import moment from 'moment';
import 'moment/locale/id';
import io from 'socket.io-client';
moment.locale('id');

export default {
    components: { ToastificationContent },

    data() {
      return {
        socket : io(process.env.VUE_APP_SOCKET_IO, { transports: ["websocket", "polling", "flashsocket"],withCredentials: true }),
        display: [],
        active: {
            tooltip: 'Maximize',
        },
        date: moment(new Date()).format('YYYY-MM-DD'),
      }
    },

    methods: {
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

        updateDate() {
            const dateElement = document.getElementById('live-date');
            if (dateElement) {
                dateElement.innerHTML = moment().locale("id").format('dddd, DD MMMM YYYY');
            }
        },

        updateClock() {
            const clockElement = document.getElementById('live-clock');
            if (clockElement) {
                let time = moment().locale("id").format('HH:mm:ss');
                clockElement.innerHTML = time + ' WIB';
            }
        },

        getHeaderVariant(status) {
            if (status == 1) {
                return 'bg-grad-dove-100'
            } else if (status == 2) {
                return 'bg-grad-warning-100'
            } else if (status == 3) {
                return 'bg-grad-success-100'
            } else {
                return ''
            }
        },

        getBoxVariant(status) {
            if (status == 1) {
                return 'bg-grad-dove-200'
            } else if (status == 2) {
                return 'bg-grad-warning-200'
            } else if (status == 3) {
                return 'bg-grad-success-200'
            } else {
                return ''
            }
        },

        notification (a,b,c,d) {
          this.$toast({
            component: ToastificationContent,
            position: 'top-right',
            props: { title: a, icon: b, variant: c, text: d}
          })
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
          };
          for (let i = 0; i < code.length; i++) {
            const char = code.charAt(i);
            const soundToPlay = sounds[char]

            if (soundToPlay) {
              await this.playSound(soundToPlay)
            }
          }
        },

        getData () {
          this.$store.dispatch('queue/getDisplayPresc', this.date).then(data => {
            this.display = data
            // console.log('this.display: ', this.display);
          })
        },

        delay(ms) {
          return new Promise((resolve) => setTimeout(resolve, ms));
        },

        async getDataSound(code,queue) {
          this.getData()

          if(queue && code) {
            await this.playSound(nomorantrian)
            await this.playSoundByCode(code)
            await this.playSoundByNumber(queue)
          }
        },

        playSound(sound) {
          return new Promise((resolve) => {
            sound.play();
            sound.onended = resolve;
          });
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
    },

    created() {
        this.getData()
    },

    mounted() {
        this.socket.on('GET_DISPLAY_PRS', (obj) => {
          if (obj) {
              this.getDataSound(obj.code_queue_poli,obj.queue_poli)
              console.log(obj)
          } else {
            this.getData()
          }
        });

        this.updateDate();
        setInterval(this.updateDate, 1000)

        this.updateClock();
        setInterval(this.updateClock, 1000)
    },

}
</script>

<style scoped>
@import './../../../../@core/css/main/main.min.css';

.ax-header {
  padding: 40px 64px 83px 54px;
  border-bottom-left-radius: 100px;
  border-bottom-right-radius: 100px;
}

.queue.card {
  border-radius: 30px;
}

.queue.card-header {
  border-top-left-radius: 30px;
  border-top-right-radius: 30px;
}

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