<template>
  <div>
    <!-- <b-badge class="float-right" variant="primary-700" @click="openFullScreen">
      <feather-icon icon="Maximize2Icon" />
    </b-badge> -->
    
    <template>
      <div class="row fullscreen-row">
         <section class="col-lg-10 col-md-7 col-12 d-flex align-items-center justify-content-center" style="height: 100vh;">
            <video
              :key="currentVideoIndex"
              :src="videos[currentVideoIndex]"
              style="background:#000; object-fit:contain; width:100%; height: auto; max-height: 100vh;"
              autoplay
              muted
              @ended="playNextVideo"
              controls
            ></video>
          </section>
        <section class="col-lg-2 col-md-5 col-12 loket-column" style="height: 100vh; overflow-y: auto;">
          <template v-for="(d, index) in display">
            <div class="card text-center mb-3 loket-card">
              <div class="card-header font-weight-bold text-neutral-100 bg-primary">
                <h3><b>LOKET {{d.name}}</b></h3>
              </div>
              <div class="card-body">
                <section class="card">
                  <div class="card-body">
                    <template v-if="call.loket == d.name">
                      <h1><b>{{call.code}}-{{call.number}}</b></h1>
                    </template>
                    <template v-else>
                      <h1><b>{{d.queue}}</b></h1>
                    </template>
                  </div>
                </section>
              </div>
            </div>
          </template>
        </section>
      </div>
    </template>
  </div>
</template>
<script>
const bell = new Audio(require("@/@core/assets/sound/Airport_Bell.mp3"));
const bellclose = new Audio(require("@/@core/assets/sound/bell-bandara.mp3"));
const out = new Audio(require("@/@core/assets/sound/out.wav"));
const nomorantrian = new Audio(require("@/@core/assets/sound/nomor-antrian.mp3"));
const loket = new Audio(require("@/@core/assets/sound/loket.mp3"));
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
const penyakitdalam = new Audio(require("@/@core/assets/sound/ke-poli-penyakit-dalam.mp3"));
const a = new Audio(require("@/@core/assets/sound/a.mp3"));
const b = new Audio(require("@/@core/assets/sound/b.mp3"));
const c = new Audio(require("@/@core/assets/sound/c.mp3"));
const d = new Audio(require("@/@core/assets/sound/d.mp3"));
const e = new Audio(require("@/@core/assets/sound/e.mp3"));
const f = new Audio(require("@/@core/assets/sound/f.mp3"));
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import moment from 'moment'
import { mapState } from 'vuex'
import ModernLabel from '@/@core/components/label/ModernLabel.vue';
import io from 'socket.io-client';
export default {
  components: {
    ModernLabel
  },
  data() {
    return {
      videos: [],
      currentVideoIndex: 0,
      print:false,
      isDisabled:false,
      user: '',
      message: '',
      messages: [],
      display: [],
      call: {},
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
    shuffleVideos() {
      // Fisher-Yates shuffle
      for (let i = this.videos.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [this.videos[i], this.videos[j]] = [this.videos[j], this.videos[i]];
      }
    },
    playNextVideo() {
      this.currentVideoIndex = (this.currentVideoIndex + 1) % this.videos.length;
      // If want to reshuffle after every full cycle
      if (this.currentVideoIndex === 0) this.shuffleVideos();
    },
    notification (a,b,c,d) {
      this.$toast({
        component: ToastificationContent,
        employee: 'top-right',
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
    getData () {
      this.$store.dispatch('queue/getDisplay').then(data => {
        this.display = data
      })
      this.$store.dispatch('queue/getAds').then(data => {
        for (let i = 0; i < data.length; i++) {
          this.videos.push(process.env.VUE_APP_IMAGE+data[i].file)
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

    playSoundByCode(code) {
      const sounds = {
        'A': a,
        'B': b,
        'C': c,
        'D': d,
        'E': e,
        'F': f,
      };
      const soundToPlay = sounds[code];
      if (soundToPlay) {
        return this.playSound(soundToPlay);
      }
      return Promise.resolve();
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

    playSoundByLoket(loket) {
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
      const soundToPlay = sounds[loket];
      if (soundToPlay) {
        return this.playSound(soundToPlay);
      }
      return Promise.resolve();
    },

    async getDataSound(data) {
      try {
        if (data.action === 'call') {
          const res = await this.$store.dispatch('queue/getDisplayById', data.id);
          if (res) {
            this.call = res
            // await this.playSound(bell)
            await this.playSound(nomorantrian)
            
            await this.playSoundByCode(res.code)
            
            await this.playSoundByNumber(res.number)
            
            await this.playSound(loket)
            await this.playSoundByLoket(res.loket)
            
            // await this.playSound(bell)
          }
        } else {
          console.log('error going here');
        }
      } catch (error) {
        this.notification('Failed', 'CloseIcon', 'danger', 'Something Wrong!');
      }
    },
  },
  
  mounted() {
    this.getData()
    this.socket.on('GET_DISPLAY', (data) => {
      console.log('data emit: ', data)
      this.getData()
      this.getDataSound(data)
    });
  }
}
</script>
<style>
@import './../../../../@core/css/main/main.min.css';
html, body, #app {
  height: 100%;
  margin: 0;
  padding: 0;
}
.fullscreen-row {
  min-height: 100vh;
  height: 100vh;
  display: flex;
}
.loket-column {
  height: 100vh;
  overflow-y: hidden;
  display: flex;
  flex-direction: column;
  gap: 12px; /* Remove gap if you want cards to touch each other */
}

.loket-column,
.fullscreen-row > section {
  padding-left: 0 !important;
  padding-right: 0 !important;
}

.loket-card {
  flex: 1 1 0;
  margin-bottom: 0 !important; /* Remove bottom margin for seamless stacking */
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.loket-card,
.loket-card .card,
.loket-card .card-header,
.loket-card .card-body {
  border-radius: 0 !important;
  /* border: none !important; */
}
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
