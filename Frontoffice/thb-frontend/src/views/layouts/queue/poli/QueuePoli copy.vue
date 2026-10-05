<template>
  <div>
    <!-- <TopQueueMenu selectedMenu="menu1" /> -->
    <div class="row mt-32">
      <section class="col-lg-5">
        <div class="card text-center">
          <div class="card-header font-weight-bold text-neutral-100 bg-primary">
            NOMOR ANTREAN
            <b-badge class="float-right" variant="primary-700" @click="openFullScreen">
              <feather-icon icon="Maximize2Icon" />
            </b-badge>
          </div>
          <div class="card-body">
            <section class="card">
              <div class="card-body text-bold-72">A4</div>
            </section>
            <section class="card mt-12">
              <div class="card-body">
                <div class="font-weight-bold">Dokter/Bidan</div>
                <div class="text-regular-18 text-neutral-400">dr. Arief Budiman</div>
              </div>
            </section>
            <section class="row mt-12">
              <section class="col-lg-6">
                <div class="card">
                  <div class="card-body">
                    <div class="font-weight-bold">Poli Tujuan</div>
                    <div class="text-regular-18 text-neutral-400">health_facility_item</div>
                  </div>
                </div>
              </section>
              <section class="col-lg-6 ">
                <div class="card mt-12 mt-lg-0">
                  <div class="card-body">
                    <div class="font-weight-bold">Instalasi</div>
                    <div class="text-regular-18 text-neutral-400">health_facility_name</div>
                  </div>
                </div>
              </section>
            </section>
            <section class="row mt-12">
              <section class="col-lg-6">
                <div class="card">
                  <div class="card-body">
                    <div class="font-weight-bold">Waktu Daftar</div>
                    <div class="text-regular-18 text-neutral-400">18:09:48</div>
                  </div>
                </div>
              </section>
              <section class="col-lg-6 ">
                <div class="card mt-12 mt-lg-0">
                  <div class="card-body">
                    <div class="font-weight-bold">Waktu Panggil</div>
                    <div class="text-regular-18 text-neutral-400">18:12:18</div>
                  </div>
                </div>
              </section>
            </section>
          </div>
        </div>
      </section>
      <section class="col-lg">
        <section class="card">
          <div class="card-body">
            <section class="row">
              <div class="col-lg-6">
                <b-form-select value="0">
                  <b-form-select-option value="0">Umum</b-form-select-option>
                </b-form-select>
              </div>
              <div class="col-lg-6">
                <b-form-select value="0">
                  <b-form-select-option value="0">Pilih dokter</b-form-select-option>
                </b-form-select>
              </div>
            </section>
            <section class="row mt-24">
              <div class="col-lg-3">
                <b-button variant="success-500" class="btn-block">
                  <feather-icon icon="XIcon" /> Panggil Next
                </b-button>
              </div>
              <div class="col-lg-3">
                <b-button variant="warning-500" class="btn-block">
                  <feather-icon icon="XIcon" /> Panggil Ulang
                </b-button>
              </div>
              <div class="col-lg-3">
                <b-button variant="outline-neutral-500" class="btn-block">
                  <feather-icon icon="XIcon" /> Selesai
                </b-button>
              </div>
              <div class="col-lg-3">
                <b-button variant="danger-500" class="btn-block">
                  <feather-icon icon="XIcon" /> Tidak Hadir
                </b-button>
              </div>
            </section>
          </div>
        </section>
        <section class="card mt-24">
          <div class="card-body">
            <b-list-group>
              <b-list-group-item style="border-bottom: 2px solid rgb(231, 231, 231);" class="d-flex justify-content-between align-items-center">
                Umum
                <div class="text-right">Sisa 0 / 4 Antrean</div>
              </b-list-group-item>

              <b-list-group-item style="border-bottom: 2px solid rgb(231, 231, 231);" class="d-flex justify-content-between align-items-center">
                Gigi
                <div class="text-right">Sisa 0 / 0 Antrean</div>
              </b-list-group-item>

              <b-list-group-item style="border-bottom: 2px solid rgb(231, 231, 231);" class="d-flex justify-content-between align-items-center">
                UGD
                <div class="text-right">Sisa 0 / 4 Antrean</div>
              </b-list-group-item>

              <b-list-group-item style="border-bottom: 2px solid rgb(231, 231, 231);" class="d-flex justify-content-between align-items-center">
                KB
                <div class="text-right">Sisa 0 / 0 Antrean</div>
              </b-list-group-item>

              <b-list-group-item style="border-bottom: 2px solid rgb(231, 231, 231);" class="d-flex justify-content-between align-items-center">
                Penyakit Dalam
                <div class="text-right">Sisa 0 / 4 Antrean</div>
              </b-list-group-item>

              <b-list-group-item style="border-bottom: 2px solid rgb(231, 231, 231);" class="d-flex justify-content-between align-items-center">
                Mata
                <div class="text-right">Sisa 0 / 4 Antrean</div>
              </b-list-group-item>

              <b-list-group-item style="border-bottom: 2px solid rgb(231, 231, 231);" class="d-flex justify-content-between align-items-center">
                Kandungan
                <div class="text-right">Sisa 0 / 4 Antrean</div>
              </b-list-group-item>

            </b-list-group>
          </div>
        </section>
      </section>
    </div>
  </div>
</template>
<script>
import { mapState } from 'vuex'
import TopQueueMenu from '@/@core/components/top-menu/TopQueueMenu.vue';

export default {
  components: {
    TopQueueMenu
  },
  computed: {
    ...mapState({ 
      items: state => state.registration.items, 
    }),
  },
  methods: {
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
    }
  },
  created () {
      this.$store.dispatch('registration/getDetail')
  }
}
</script>