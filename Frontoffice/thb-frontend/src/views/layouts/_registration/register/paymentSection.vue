<template>
    <div class="card border-0">
        <div class="card-header bg-indigo rounded">
            <span class="fw-bold text-white">Metode Pembayaran</span>
        </div>
        <div class="card-body">
            <div>
                <b-table small hover responsive :items="paymentTable.items" :fields="paymentTable.fields" head-variant="light"
                    style="white-space: nowrap;">
                    <template #cell(Aksi)>
                        <b-button variant="warning" size="sm" class="mr-2">
                            <img src="../../../../@core/assets/icons/edit.svg" alt="">
                        </b-button>
                        <b-button variant="danger" size="sm" class="mr-2">
                            <img src="../../../../@core/assets/icons/trash.svg" alt="">
                        </b-button>
                    </template>

                </b-table>
            </div>
            <div class="col d-flex mt-32">
                <b-button pill size="sm" variant="indigo-200" @click="showBPJS" :disabled="BPJSmethod"
                    class="ml-lg-auto px-3 mr-2">
                    <img src="../../../../@core/assets/icons/plusPurp.svg" class="mr-2" />
                    <span class="text-indigo-700 fw-medium">Penjamin BPJS</span>
                </b-button>
                <b-button pill size="sm" variant="indigo-200" @click="showPenjamin" :disabled="penjamin" class="px-3">
                    <img src="../../../../@core/assets/icons/plusPurp.svg" class="mr-2" />
                    <span class="text-indigo-700 fw-medium">Penjamin</span>
                </b-button>
            </div>
            <div v-if="BPJSmethod" class="col px-0 mt-2 mt-lg-0">
                <div class="col px-0">
                    <p class="title">Penjamin BPJS</p>
                    <hr style="border: 1px solid indigo;">
                </div>
                <b-form class="c-form">
                    <div class="col px-0">
                        <div class="row mx-0">
                            <div class="col-12 col-lg-4 px-0 px-lg-3 mt-1">
                                <label>No Anggota *</label>
                                <b-form-input type="text"></b-form-input>
                            </div>
                            <div class="col-12 col-lg-4 px-0 px-lg-3 mt-1">
                                <label>Nama Terdaftar *</label>
                                <b-form-input type="text"></b-form-input>
                            </div>
                            <div class="col-12 col-lg-4 px-0 px-lg-3 mt-1">
                                <label>No KTP *</label>
                                <b-form-input type="number"></b-form-input>
                            </div>
                        </div>
                        <div class="row mx-0 mt-lg-10">
                            <div class="col-12 col-lg-4 px-0 px-lg-3 mt-1">
                                <label>No RM *</label>
                                <b-form-input type="number"></b-form-input>
                            </div>
                            <div class="col-12 col-lg-4 px-0 px-lg-3 mt-1">
                                <label>Tanggal Lahir *</label>
                                <b-form-input type="date"></b-form-input>
                            </div>
                        </div>
                        <div class="row mx-0 mt-lg-10">
                            <div class="col-12 col-lg-3 px-0 px-lg-3 mt-1">
                                <label>Hak Kelas *</label>
                                <b-form-select v-model="selected" :options="options"></b-form-select>
                            </div>
                            <div class="col-12 col-lg-3 px-0 px-lg-3 mt-1">
                                <label>Jenis Kepesertaan *</label>
                                <b-form-input type="text"></b-form-input>
                            </div>
                            <div class="col-12 col-lg-3 px-0 px-lg-3 mt-1">
                                <label>Tanggal Aktif *</label>
                                <b-form-input type="date"></b-form-input>
                            </div>
                            <div class="col-12 col-lg-3 px-0 px-lg-3 mt-1">
                                <label>Tanggal Akhir *</label>
                                <b-form-input type="date"></b-form-input>
                            </div>
                        </div>
                        <div class="row mx-0 mt-lg-10">
                            <div class="col-12 col-lg-3 px-0 px-lg-3 mt-1">
                                <label>Nomor Rujukan *</label>
                                <b-form-input type="number"></b-form-input>
                            </div>
                            <div class="col-12 col-lg-3 px-0 px-lg-3 mt-1">
                                <label>Tanggal Rujukan *</label>
                                <b-form-input type="date"></b-form-input>
                            </div>
                            <div class="col-12 col-lg-3 px-0 px-lg-3 mt-1">
                                <label>Asal Rujukan *</label>
                                <b-form-input type="text"></b-form-input>
                            </div>
                            <div class="col-12 col-lg-3 px-0 px-lg-3 mt-1">
                                <label>Kode PPK Rujukan *</label>
                                <b-form-input type="text"></b-form-input>
                            </div>
                        </div>
                        <div class="row mx-0 mt-lg-10">
                            <div class="col-12 col-lg-3 px-0 px-lg-3 mt-1">
                                <label>Operasi Katarak *</label>
                                <b-form-select v-model="selected" :options="options"></b-form-select>
                            </div>
                            <div class="col-12 col-lg-3 px-0 px-lg-3 mt-1">
                                <label>No Kontrol/RJ *</label>
                                <b-form-input type="number"></b-form-input>
                            </div>
                            <div class="col-12 col-lg-3 px-0 px-lg-3 mt-1">
                                <label>Status Kepesertaan *</label>
                                <b-form-input type="text"></b-form-input>
                            </div>
                            <div class="col-12 col-lg-3 px-0 px-lg-3 mt-1">
                                <label>Tanggal SEP *</label>
                                <b-form-input type="date"></b-form-input>
                            </div>
                        </div>
                        <div class="row mx-0 mt-lg-10">
                            <div class="col-12 col-lg-4 px-0 px-lg-3 mt-1">
                                <label>Poli *</label>
                                <b-form-select v-model="selected" :options="options"></b-form-select>
                            </div>
                            <div class="col-12 col-lg-4 px-0 px-lg-3 mt-1">
                                <label>Dokter *</label>
                                <b-form-select v-model="selected" :options="options"></b-form-select>
                            </div>
                            <div class="col-12 col-lg-4 px-0 px-lg-3 mt-1">
                                <label>COB *</label>
                                <b-form-select v-model="selected" :options="options"></b-form-select>
                            </div>
                        </div>
                        <div class="row mx-0 mt-lg-10">
                            <div class="col-12 col-lg-3 px-0 px-lg-3 mt-1">
                                <label>Tujuan Kunjungan *</label>
                                <b-form-select v-model="selected" :options="options"></b-form-select>
                            </div>
                            <div class="col-12 col-lg-3 px-0 px-lg-3 mt-1">
                                <label>Procedure</label>
                                <b-form-input type="text"></b-form-input>
                            </div>
                            <div class="col-12 col-lg-3 px-0 px-lg-3 mt-1">
                                <label>Penunjang</label>
                                <b-form-input type="text"></b-form-input>
                            </div>
                            <div class="col-12 col-lg-3 px-0 px-lg-3 mt-1">
                                <label>Asesmen Pelayanan</label>
                                <b-form-input type="text"></b-form-input>
                            </div>
                        </div>
                        <div class="row mx-0 mt-lg-10">
                            <div class="col-12 px-0 px-lg-3 mt-1">
                                <label>Catatan</label>
                                <b-form-input type="text"></b-form-input>
                            </div>
                        </div>
                        <div class="row mx-0 mt-lg-10">
                            <div class="col-12 col-lg-4 px-0 px-lg-3 mt-1">
                                <label>Laka Lintas *</label>
                                <b-form-select v-model="selected" :options="options"></b-form-select>
                            </div>
                            <div class="col d-flex">
                                <div class="d-flex ml-auto mt-auto">
                                    <b-button pill size="sm" variant="indigo-200" class="px-3 text-indigo-600"
                                        @click="no_sep = !no_sep">Buat SEP Manual</b-button>
                                </div>
                            </div>
                        </div>
                        <div v-if="no_sep" class="row mx-0 mt-lg-10">
                            <div class="col-12 px-0 px-lg-3 mt-1">
                                <label>No SEP</label>
                                <b-form-input type="text"></b-form-input>
                            </div>
                        </div>
                        <!-- TANDA TANGAN -->
                        <!-- <div class="d-flex mt-10 mt-lg-24">
                            <div class="mx-auto col-12 col-lg-4">
                                <div id="signature-screen" class="signature-input bg-white"></div>
                                <b-button size="sm" variant="indigo" id="signature-editor-clear" class="col">Clear</b-button>
                            </div>
                        </div> -->
                    </div>
                    <div class="col mt-3 d-flex justify-content-end align-items-center">
                        <div class="col mt-16 mt-lg-24 px-0 d-flex">
                            <div class="ml-auto row mx-0">
                                <b-button pill variant="outline-indigo" class="px-3 mr-2" @click="showBPJS">Batal</b-button>
                                <b-button pill variant="indigo" class="px-3">Simpan</b-button>
                            </div>
                        </div>
                    </div>
                </b-form>
            </div>
            <div v-if="penjamin" class="mt-2 mt-lg-0">
                <div class="col px-0">
                    <p class="title">Penjamin</p>
                    <hr style="border: 1px solid indigo;">
                </div>
                <b-form class="c-form">
                    <div class="col px-0">
                        <div class="row mx-0">
                            <div class="col-12 col-lg px-0 px-lg-3 mt-1">
                                <label>Nama Penjamin *</label>
                                <b-form-input type="text"></b-form-input>
                            </div>
                        </div>
                        <div class="row mx-0 mt-lg-10">
                            <div class="col-12 col-lg-4 px-0 px-lg-3 mt-1">
                                <label>No. Anggota *</label>
                                <b-form-input type="number"></b-form-input>
                            </div>
                            <div class="col-12 col-lg px-0 px-lg-3 mt-1">
                                <label>Nama Terdaftar *</label>
                                <b-form-input type="text"></b-form-input>
                            </div>
                        </div>
                        <div class="row mx-0 mt-lg-10">
                            <div class="col-12 col-lg px-0 px-lg-3 mt-1">
                                <label>Hak Kelas *</label>
                                <b-form-select v-model="selected" :options="options"></b-form-select>
                            </div>
                            <div class="col-12 col-lg px-0 px-lg-3 mt-1">
                                <label>Jenis Kepesertaan *</label>
                                <b-form-select v-model="selected" :options="options"></b-form-select>
                            </div>
                            <div class="col-12 col-lg px-0 px-lg-3 mt-1">
                                <label>Status Kepesertaan *</label>
                                <b-form-select v-model="selected" :options="options"></b-form-select>
                            </div>
                        </div>
                        <div class="row mx-0 mt-lg-10">
                            <div class="col-12 col-lg px-0 px-lg-3 mt-1">
                                <label>Kecelakaan Kerja *</label>
                                <b-form-select v-model="selected" :options="options"></b-form-select>
                            </div>
                        </div>
                        <div class="row mx-0 mt-lg-10">
                            <div class="col-12 col-lg px-0 px-lg-3 mt-1">
                                <label>Tanggal Akhir *</label>
                                <b-form-input type="date"></b-form-input>
                            </div>
                            <div class="col-12 col-lg px-0 px-lg-3 mt-1">
                                <label>Tanggal Akhir *</label>
                                <b-form-input type="date"></b-form-input>
                            </div>
                            <div class="col-12 col-lg px-0 px-lg-3 mt-1">
                                <label>No. SEP *</label>
                                <b-form-input type="number"></b-form-input>
                            </div>
                        </div>
                        <div class="row mx-0 mt-lg-10">
                            <div class="col-12 col-lg px-0 px-lg-3 mt-1">
                                <label>Catatan *</label>
                                <b-form-input type="text"></b-form-input>
                            </div>
                        </div>
                    </div>
                    <div class="col mt-16 mt-lg-24 d-flex">
                        <div class="ml-auto row mx-0">
                            <b-button pill variant="outline-indigo" class="px-3 mr-2" @click="showPenjamin">Batal</b-button>
                            <b-button pill variant="indigo" class="px-3">Simpan</b-button>
                        </div>
                    </div>
                </b-form>
            </div>
        </div>
    </div>
</template>
<script>
import $ from 'jquery'
import '@/views/pages/canvasing/jsCanvas/szsignaturecanvas.js'
export default {
    data() {
        return {
            activeBtn: 'aktif',
            BPJSmethod: false,
            penjamin: false,
            no_sep: false,

            paymentTable: {
                fields: [
                    { key: 'penjamin', label: 'Jenis Penjamin' },
                    { key: 'anggota', label: 'No Anggota' },
                    { key: 'kelas', label: 'Kelas Tanggungan' },
                    { key: 'Aksi' },
                ],
                items: [
                    { penjamin: 'Tunai', anggota: '-', kelas: '-' },
                    { penjamin: 'Tunai', anggota: '-', kelas: '-' },
                ],
            },
            // option
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
        showBPJS() {
            this.BPJSmethod = !this.BPJSmethod
            this.penjamin = false
        },
        showPenjamin() {
            this.penjamin = !this.penjamin
            this.BPJSmethod = false
        },
    },
    // updated() {
    //     var $signature = $('#signature-screen.signature-input').szSignatureCanvas({
    //         responsive: true,
    //         canDraw: true,
    //     });
    //     $('#signature-editor-clear').click(function () {
    //         $signature.szSignatureCanvas('clearCanvas');
    //         confirm('Bersihkan tanda tangan?');
    //         return false;
    //     });
    // },
}
</script>
<style scoped>
@import '../../../../@core/css/utility.css';
</style>