<template>
    <div>
        <b-button v-b-modal.modal-scrollable class="col" variant="blue-500" size="sm">Input Data BPJS</b-button>
        <b-modal id="modal-scrollable" scrollable size="xl" title="Data BPJS Pasien" hide-footer>
            <b-form class="c-form">
                <div class="row mx-0">
                    <!-- LEFT-SIDE -->
                    <div class="col-12 col-lg-6 px-0 px-lg-3">
                        <div>
                            <p class="text-bold-14-on px-0 px-lg-2">Informasi</p>
                            <div class="row mx-0">
                                <div class="col-12 col-lg-6 px-0 px-lg-2 mt-1">
                                    <label>No. Kartu *</label>
                                    <b-form-input type="number"></b-form-input>
                                </div>
                                <div class="col-12 col-lg-6 px-0 px-lg-2 mt-1">
                                    <label>NIK *</label>
                                    <b-form-input type="number"></b-form-input>
                                </div>
                            </div>
                            <div class="row mx-0 mt-lg-10">
                                <div class="col-12 col-lg-6 px-0 px-lg-2 mt-1">
                                    <label>Nama *</label>
                                    <b-form-input type="text"></b-form-input>
                                </div>
                                <div class="col-12 col-lg-6 px-0 px-lg-2 mt-1">
                                    <label>Jenis Kelamin *</label>
                                    <b-form-select v-model="selected" :options="options"
                                    ></b-form-select>
                                </div>
                            </div>
                            <div class="row mx-0 mt-lg-10">
                                <div class="col-12 col-lg-6 px-0 px-lg-2 mt-1">
                                    <label>Tanggal Lahir *</label>
                                    <b-form-input type="date"></b-form-input>
                                </div>
                                <div class="col-12 col-lg-6 px-0 px-lg-2 mt-1">
                                    <label>Umur *</label>
                                    <b-form-input type="number"></b-form-input>
                                </div>
                            </div>
                            <div class="row mx-0 mt-lg-10">
                                <div class="col-12 col-lg-6 px-0 px-lg-2 mt-1">
                                    <label>Pisa *</label>
                                    <b-form-input type="text"></b-form-input>
                                </div>
                                <div class="col-12 col-lg-6 px-0 px-lg-2 mt-1">
                                    <label>Kode Provider *</label>
                                    <b-form-input type="text"></b-form-input>
                                </div>
                            </div>
                            <div class="row mx-0 mt-lg-10">
                                <div class="col-12 col-lg-6 px-0 px-lg-2 mt-1">
                                    <label>Provider *</label>
                                    <b-form-input type="text"></b-form-input>
                                </div>
                            </div>
                        </div>
                        <div class="mt-10 mt-lg-32">
                            <p class="text-bold-14-on px-0 px-lg-2">Informasi</p>
                            <div class="row mx-0">
                                <div class="col-12 col-lg-6 px-0 px-lg-2 mt-1">
                                    <label>Prolanis PRb *</label>
                                    <b-form-input type="text"></b-form-input>
                                </div>
                                <div class="col-12 col-lg-6 px-0 px-lg-2 mt-1">
                                    <label>No. SKTM *</label>
                                    <b-form-input type="number"></b-form-input>
                                </div>
                            </div>
                            <div class="row mx-0 mt-lg-10">
                                <div class="col-12 col-lg-6 px-0 px-lg-2 mt-1">
                                    <label>Tanggal TAT *</label>
                                    <b-form-input type="date"></b-form-input>
                                </div>
                                <div class="col-12 col-lg-6 px-0 px-lg-2 mt-1">
                                    <label>Tanggal TMT *</label>
                                    <b-form-input type="date"></b-form-input>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT-SIDE -->
                    <div class="col-12 col-lg-6 px-0 px-lg-3">
                        <div>
                            <p class="text-bold-14-on px-0 px-lg-2">Jenis Peserta</p>
                            <div class="row mx-0">
                                <div class="col-12 col-lg-6 px-0 px-lg-2 mt-1">
                                    <label>Kode *</label>
                                    <b-form-input type="text"></b-form-input>
                                </div>
                                <div class="col-12 col-lg-6 px-0 px-lg-2 mt-1">
                                    <label>Keterangan *</label>
                                    <b-form-input type="text"></b-form-input>
                                </div>
                            </div>
                        </div>
                        <div class="mt-10 mt-lg-32">
                            <p class="text-bold-14-on px-0 px-lg-2">Hak Peserta</p>
                            <div class="row mx-0">
                                <div class="col-12 col-lg-6 px-0 px-lg-2 mt-1">
                                    <label>Kode *</label>
                                    <b-form-input type="text"></b-form-input>
                                </div>
                                <div class="col-12 col-lg-6 px-0 px-lg-2 mt-1">
                                    <label>Keterangan *</label>
                                    <b-form-input type="text"></b-form-input>
                                </div>
                            </div>
                        </div>
                        <div class="mt-10 mt-lg-32">
                            <p class="text-bold-14-on px-0 px-lg-2">Status Peserta</p>
                            <div class="row mx-0">
                                <div class="col-12 col-lg-6 px-0 px-lg-2 mt-1">
                                    <label>Keterangan *</label>
                                    <b-form-input type="text"></b-form-input>
                                </div>
                                <div class="col-12 col-lg-6 px-0 px-lg-2 mt-1">
                                    <label>Tanggal Cetak *</label>
                                    <b-form-input type="date"></b-form-input>
                                </div>
                            </div>
                            <div class="row mx-0 mt-lg-10">
                                <div class="col-12 col-lg-6 px-0 px-lg-2 mt-1">
                                    <label>Tanggal TAT *</label>
                                    <b-form-input type="date"></b-form-input>
                                </div>
                                <div class="col-12 col-lg-6 px-0 px-lg-2 mt-1">
                                    <label>Tanggal TMT *</label>
                                    <b-form-input type="date"></b-form-input>
                                </div>
                            </div>
                        </div>
                        <div class="mt-10 mt-lg-24">
                            <p class="text-bold-14-on px-0 px-lg-2">Status Peserta</p>
                            <div class="row mx-0">
                                <div class="col-12 col-lg-6 px-0 px-lg-2 mt-1">
                                    <label>Keterangan *</label>
                                    <b-form-input type="text"></b-form-input>
                                </div>
                                <div class="col-12 col-lg-6 px-0 px-lg-2 mt-1">
                                    <label>Tanggal Cetak *</label>
                                    <b-form-input type="date"></b-form-input>
                                </div>
                            </div>
                            <div class="row mx-0 mt-lg-10">
                                <div class="col-12 col-lg-6 px-0 px-lg-2 mt-1">
                                    <label>Tanggal TAT *</label>
                                    <b-form-input type="date"></b-form-input>
                                </div>
                                <div class="col-12 col-lg-6 px-0 px-lg-2 mt-1">
                                    <label>Tanggal TMT *</label>
                                    <b-form-input type="date"></b-form-input>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-show="sepNumber" class="col px-0 px-lg-24 mt-lg-10 c-form">
                    <label>No. SEP *</label>
                    <b-form-input type="number"></b-form-input>
                </div>
                <div class="col mt-16 px-0 px-lg-24">
                    <b-button size="sm" variant="blue-500" @click="showSepNumber" ref="refNumber" class="col">No SEP Manual</b-button>
                </div>
                <div class="d-flex mt-16 mt-lg-32 px-0 px-lg-24">
                    <b-button pill type="reset" variant="outline-indigo" class="ml-lg-auto col col-lg-2">Reset</b-button>
                    <b-button pill type="submit" variant="pink-500" class="ml-2 col col-lg-2 text-white">Simpan</b-button>
                </div>
            </b-form>
        </b-modal>
    </div>
</template>

<script>

export default {
    data() {
        return {
            sepNumber: false,
            selected: null,
            options: [
                { value: null, text: '-- Pilih --' },
                { value: 'a', text: 'This is option 1' },
                { value: 'b', text: 'This is option 2' },
                { value: 'c', text: 'This is option 3' },
                { value: 'd', text: 'This is option 4' }
            ],
        }
    },
    methods: {
        showSepNumber() {
            this.$refs.refNumber.style.display = 'none';
            this.sepNumber = true;
        }
    },
}
</script>