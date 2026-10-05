<template>
  <b-container fluid class="py-4">
      <b-row class="justify-content-center">
          <b-col cols="12" md="10" lg="8">
              <div class="bg-white notification-wrapper p-4 rounded shadow-sm">
                  <b-button
                  variant="primary"
                  size="sm"
                  class="mb-3"
                  to="/"
                  >
                      <b-icon-house-fill class="mr-1" /> Back to Home
                  </b-button>

                  <h4 class="font-weight-bold mb-3">Notifikasi</h4>
              </div>
          </b-col>
      </b-row>
  </b-container>
</template>

<script>
// announce
const codebluebell = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/codebluebell.mp3"))
const codeblue = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/codeblueverpria.mp3"))
//utils sound
const segerake = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/segerake.mp3"))
const space = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/space.mp3"))
// numbers
const satu = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/angka/satu.mp3"))
const dua = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/angka/dua.mp3"))
const tiga = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/angka/tiga.mp3"))
const empat = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/angka/empat.mp3"))
const lima = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/angka/lima.mp3"))
const enam = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/angka/enam.mp3"))
const tujuh = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/angka/tujuh.mp3"))
const delapan = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/angka/delapan.mp3"))
const sembilan = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/angka/sembilan.mp3"))
const sepuluh = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/angka/sepuluh.mp3"))
const sebelas = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/angka/sebelas.mp3"))
const seratus = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/angka/seratus.mp3"))
const belas = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/angka/belas.mp3"))
const puluh = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/angka/puluh.mp3"))
const ratus = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/angka/ratus.mp3"))
//punya kamar dan ruangan
const sakura = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/codeblue-sakura.mp3"))
const gardeniaanak = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/codeblue-gardeniaanak.mp3"))
const jasmine = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/codeblue-jasmine.mp3"))
const bougenville = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/codeblue-bougenville.mp3"))
const tulip = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/codeblue-tulip.mp3"))
const orchid = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/codeblue-orchid.mp3"))
const gardeniakebidanan = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/codeblue-gardeniakebidanan.mp3"))
const ruangjiwa = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/codeblue-ruanganjiwa.mp3"))
const orchidbedah = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/codeblue-orchidbedah.mp3"))
const orchidisolasi = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/codeblue-orchidisolasi.mp3"))
//hanya punya ruangan
const administrasi = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/administrasi.mp3"))
const ambulancejenazah = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/ambulance-jenazah.mp3"))
const lavender = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/lavender.mp3"))
const fisioterapi = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/codeblue-fisioterapi.mp3"))
const radiologi = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/codeblue-radiologi.mp3"))
const laboratorium = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/codeblue-laboratorium.mp3"))
const mcu = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/codeblue-mcu.mp3"))
// const anastesia = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/codeblue-anastesia.mp3"))
const anastesia = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/codeblue-anastesi.mp3"))
const hemodialisa = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/codeblue-hemodialisa.mp3"))
const forensik = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/codeblue-forensik.mp3"))
const gizi = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/codeblue-gizi.mp3"))
const homecare = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/codeblue-homecare.mp3"))
const lobics = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/codeblue-lobics.mp3"));
const lobiutama = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/codeblue-lobiutama.mp3"));

const poliklinikakupuntur = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/poliklinik-akupuntur.mp3"))
const poliklinikanakpaediatric = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/poliklinik-anak-paediatric.mp3"))
const poliklinikanestesi = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/poliklinik-anestesi.mp3"))
const poliklinikbedahanak = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/poliklinik-bedah-anak.mp3"))
const poliklinikbedahdigestif = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/poliklinik-bedah-digestif.mp3"))
const poliklinikbedahorthopedi = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/poliklinik-bedah-orthopedi.mp3"))
const poliklinikbedahplastik = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/poliklinik-bedah-plastik.mp3"))
const poliklinikbedahsyaraf = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/poliklinik-bedah-syaraf.mp3"))
const poliklinikbedahthorakskardiovaskuler = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/poliklinik-bedah-thoraks-kardiovaskuler.mp3"))
const poliklinikbedahumum = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/poliklinik-bedah-umum.mp3"))
const poliklinikbedahurologi = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/poliklinik-bedah-urologi.mp3"))
const poliklinikbedahvaskuler = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/poliklinik-bedah-vaskuler.mp3"))
const poliklinikgigi = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/poliklinik-gigi.mp3"))
const poliklinikjantung = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/poliklinik-jantung.mp3"))
const poliklinikjiwa = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/poliklinik-jiwa.mp3"))
const poliklinikkebidananKandungan = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/poliklinik-kebidanan-kandungan.mp3"))
const poliklinikkulitKelamin = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/poliklinik-kulit-kelamin.mp3"))
const poliklinikmata = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/poliklinik-mata.mp3"))
const poliklinikparu = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/poliklinik-paru.mp3"))
const poliklinikpenyakitdalam = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/poliklinik-penyakit-dalam-interna.mp3"))
const poliklinikrehabilitasimedis = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/poliklinik-rehabilitasi-medis.mp3"))
const polikliniksyaraf = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/poliklinik-syaraf.mp3"))
const polikliniktht = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/poliklinik-tht.mp3"))
const poliklinikumum = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/poliklinik-umum.mp3"))
const poliklinikkecantikan = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/poliklinik-kecantikan.mp3"))

const hcu = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/hcu.mp3"))
const iccu = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/iccu.mp3"))
const icu = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/icu.mp3"))
const icuisolasi = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/icuisolasi.mp3")) 
const nicu = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/nicu.mp3"))
const nicuisolasi = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/nicu-isolasi.mp3"))
const odc = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/odc.mp3"))
const ok = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/ok.mp3"))
const perinatologi = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/perinatologi.mp3"))
const picu = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/picu.mp3"))
const rawatinap = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/rawat-inap.mp3"))
const igd = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/igd.mp3"))

const terimakasih = new Audio(require("@/@core/assets/sound/elvenlabs/verpria/codeblue-terimakasih.mp3"))

//test code blue
const testcodeblue = new Audio(require("@/@core/assets/sound/code_blue/test-codeblue-yetty.mp3"));

import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import io from 'socket.io-client';

export default {
components: {
  ToastificationContent,
},
data() {
  return {
      socket: 
          io(process.env.VUE_APP_SOCKET_IO_NOTIF, {
          path: '/socket.io',
          transports: ['websocket', 'polling', 'flashsocket'],
          withCredentials: true}),
      currentSound: null,
      playbackToken: null,
      facilityItemList:{
          "b56b62a5-9c15-4d57-bbc4-63718155f226": "lobcc",
          "1989029c-2d02-4a3e-94c3-1c77f0e66b33": "lobutama",
          "a2d04be2-9d67-4cdb-b463-9812199c7855": "adm",
          "96bc2426-38d3-4e92-a71f-a692ff2dda99": "adm",
          "d8fc6b9d-5d8f-4f58-b616-975fedc598a2": "adanj",
          "48eff95d-2318-4088-ac41-de3f3c4d1b94": "adanj",
          "70af15e7-7a32-442a-8b46-a3d574b4a661": "hd",
          "68be017e-9aa8-4847-8d89-646323f52d23": "lab",
          "0233f15e-cd67-4c92-a3c1-b23928279d1d": "rad",
          "0a742dd7-3978-4135-b4df-650c66326cf8": "anest",
          "d350e55d-6f8a-4c42-b269-cfda717fbf8b": "fisiot",
          "b4e06ba4-350a-43a6-b39b-dd4acea0214f": "forens",
          "f048cca5-d40f-4529-a4b1-c38e0451a4f3": "gizi",
          "ef69a944-d6fc-4ff7-9eb6-4a1433a19b9e": "hd",
          "9eb15706-0a61-42fa-91a8-e5101b80aa61": "hc",
          "d46e2eb6-85a9-4bd4-b515-a0858cc50c8f": "mcu",
          "cfa60f81-356d-4cbf-85e0-3b8415307a10": "akup",
          "00f4dcd5-8577-4e7f-b7b6-58a2a1f767b3": "anak",
          "a26133de-1618-466d-8c59-781d48bfed91": "polanest",
          "9c89d12e-63c1-4d1e-a082-e37bcadaeec1": "bda",
          "8798e026-a5ad-4b98-8f2b-bfc5521e5144": "bdg",
          "820af6c1-0c58-45ae-afb2-990360f9e132": "bod",
          "4adbb716-9cf3-49fa-93b6-ba4bf2f54736": "bdp",
          "7759aec0-3f21-42d5-9fad-05d04fb82b24": "bds",
          "74087789-8f86-4e45-802b-d3b9710a2a3c": "bdtk",
          "73fd97aa-8cec-4058-9bf3-14cdcd9db4c0": "bdu",
          "6e90b7af-67ae-4a26-a1e2-44c151719080": "bduro",
          "48744dd2-210e-4ee9-bc06-02e333814826": "bdv",
          "38b10a7b-7006-451b-aa30-c11fa98635e0": "bg",
          "219f52b1-ec3a-4cb6-a2cf-5d87a82175d9": "bja",
          "246f50f0-d6b1-40be-8602-af11e105c9c4": "bji",
          "0fd51271-a0e5-4db2-bc6e-12064508e82b": "bkdank",
          "477baf79-5cae-4da7-8d98-cdad9161533a": "bkk",
          "bbdf4d7d-3255-4753-8292-70b836e019f4": "bm",
          "6292975d-65c5-4d7d-b7ca-1ef85b30c286": "bp",
          "358bf387-dced-47ad-97cd-cfaae63851aa": "bdi",
          "5f14b9bf-e9f5-4fdd-8d79-25fceb3f9f4a": "brm",
          "86607c52-5eae-4b17-861a-2fbaf864f06f": "bsr",
          "4bdaf1f5-575a-4c9f-bed9-f17f73802c64": "btht",
          "cbd51a04-eb9b-4ddf-89fc-f5f2e2865144": "bum",
          "13942927-502c-45fd-bea3-02505875b4ef": "bkc",
          "edda56f0-920d-4c6a-b8bd-b084d3c368a7": "boug",
          "c1002e5b-5f1f-4837-af45-122673052259": "ganak",
          "3905d3e8-26a3-4166-965a-a91b5edad463": "gkb",
          "1843843c-af62-4452-b19f-beddd1acd0ec": "hcu",
          "7f2a9f80-c30d-4cbd-9f0d-364cda2ec84c": "iccu",
          "6c7d7e53-ff24-4246-85b2-07ea77ac801f": "icu",
          "ff1f252e-daab-4bdb-b308-e9f4cb11a47d": "ici",
          "2a2b727f-631e-4d46-86b1-410fb8e40445": "jas",
          "15b39ac7-0654-4cff-b109-b702d4626994": "lav",
          "5515af72-6dcb-415d-89e4-2433e7b0149d": "nicu",
          "ed40f235-1c7b-4954-aebf-b3dc70a674b7": "nci",
          "91e067fd-74a6-4b45-9a0b-3d350066e370": "odc",
          "cd72c7f0-920b-4260-b602-541d0a15305a": "ok",
          "5d92766f-f1e8-42cd-9067-df8cc0fca864": "orch",
          "05079cd9-07c6-4cf9-beb0-271eb301b7c1": "perin",
          "e66e6ed4-4ef4-4375-a6d0-0445e3ec3039": "picu",
          "d158a64e-6246-444a-97b7-8d0a2d90e53a": "ri",
          "cb8fea0f-ce57-4596-b901-4671c8a2ef29": "rjiwa",
          "e6f9a05c-3be9-4847-bca8-6983af3fb230": "sak",
          "58f3ae39-1444-4e43-bdf8-0e624f8d7928": "tulip",
          "cca59ec4-d77a-45fd-b51f-aae069da22ea": "igd",
          "4a8a1d01-692c-40a8-8e41-969bbbc090b0": "orchbdh",
      }
  };
},
computed: {},
methods: {
  notification (a,b,c,d) {
    this.$toast({
      component: ToastificationContent,
      position: 'top-right',
      props: { title: a, icon: b, variant: c, text: d}
    })
  },
  async playSoundByPoli(code) {
    const sounds = {
      lobcc: lobics,
      lobutama: lobiutama,

      adm: administrasi,
      adanj: ambulancejenazah,
      hd: hemodialisa,
      lab: laboratorium,
      rad: radiologi,
      anest: anastesia,
      fisiot: fisioterapi,
      forens: forensik,
      gizi: gizi,
      hc: homecare,
      mcu: mcu,

      akup: poliklinikakupuntur,
      anak: poliklinikanakpaediatric,
      polanest: poliklinikanestesi,
      bda: poliklinikbedahanak,
      bdg: poliklinikbedahdigestif,
      bod: poliklinikbedahorthopedi,
      bdp: poliklinikbedahplastik,
      bds: poliklinikbedahsyaraf,
      bdtk: poliklinikbedahthorakskardiovaskuler,
      bdu: poliklinikbedahumum,
      bduro: poliklinikbedahurologi,
      bdv: poliklinikbedahvaskuler,
      bg: poliklinikgigi,
      bja: poliklinikjantung,
      bji: poliklinikjiwa,
      bkdank: poliklinikkebidananKandungan,
      bkk: poliklinikkulitKelamin,
      bm: poliklinikmata,
      bp: poliklinikparu,
      bdi: poliklinikpenyakitdalam,
      brm: poliklinikrehabilitasimedis,
      bsr: polikliniksyaraf,
      btht: polikliniktht,
      bum: poliklinikumum,
      bkc: poliklinikkecantikan,

      boug: bougenville,
      ganak: gardeniaanak,
      gkb: gardeniakebidanan,
      hcu: hcu,
      iccu: iccu,
      icu: icu,
      ici: icuisolasi,
      jas: jasmine,
      lav: lavender,
      nicu: nicu,
      nci: nicuisolasi,
      odc: odc,
      ok: ok,
      orch: orchid,
      orchbdh: orchidbedah,
      perin: perinatologi,
      picu: picu,
      ri: rawatinap,
      rjiwa: ruangjiwa,
      sak: sakura,
      tulip: tulip,
      igd: igd,
    };

    const soundToPlay = sounds[code];
    if (soundToPlay) {
      const newSound = new Audio(soundToPlay.src);
      await this.playSound(newSound)
    }
    return Promise.resolve();
  },
  async playSound(sound) {
    if (this.currentSound) {
      this.currentSound.pause()
      this.currentSound.currentTime = 0
    }

    this.currentSound = sound
    return new Promise((resolve) => {
      sound.play()
      sound.onended = resolve
    })
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
  async getDataSound(data) {
    try {
      const token = Symbol("playback");
      this.playbackToken = token;

      for (let i = 0; i < 2; i++) {
        if(data.health_facility_item_id == "1d4db5d8-84d3-45f6-9e97-a18b615518a9"){
          // console.log('testcodeblue')
          await this.playSound(testcodeblue)
          if (this.playbackToken !== token) break
        } else {
          if (this.playbackToken !== token) break
          for (let i = 0; i < 2; i++) {
            await this.playSound(codebluebell)
            if (this.playbackToken !== token) break
          }

          await this.playSound(segerake)
          if (this.playbackToken !== token) break

          if(data.health_facility_item_room_number > 0){
            for (let i = 0; i < 2; i++) {
              await this.playSoundByPoli(this.facilityItemList[data.health_facility_item_id])
              if (this.playbackToken !== token) break

              await this.playSoundByNumber(data.health_facility_item_room_number)
              if (this.playbackToken !== token) break

              if(i < 1){
                await this.playSound(space)
                if (this.playbackToken !== token) break
              }
            }
          } else {
            for (let i = 0; i < 2; i++) {
              await this.playSoundByPoli(this.facilityItemList[data.health_facility_item_id])
              if (this.playbackToken !== token) break

              if(i < 1){
                await this.playSound(space)
                if (this.playbackToken !== token) break
              }
            }
          }

          await this.playSound(terimakasih)
          if (this.playbackToken !== token) break
        }
        await this.delay(3000, token);
      }
    } catch (error) {
      console.log('inierror', error)
      this.notification('Failed', 'CloseIcon', 'danger', 'Something Wrong!');
    }
  },
  delay(ms,token) {
    return new Promise((resolve) => {
      const interval = 100
      let elapsed = 0

      const timer = setInterval(() => {
        if (this.playbackToken !== token) {
          clearInterval(timer)
          resolve(); 
        } else if (elapsed >= ms) {
          clearInterval(timer)
          resolve()
        } else {
          elapsed += interval
        }
      }, interval)
    });
  },
},
mounted() {
  this.socket.on('MESSAGE', (data) => {
    // console.log('socket notif code blue', data)
    this.getDataSound(data)
  }); 
},
created(){
}
};
</script>

<style scoped>
.notification-wrapper {
  /* max-height: 70vh; */
  overflow-y: hidden;
  overflow-x: hidden
}
</style>
