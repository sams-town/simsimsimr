<template>
    <div>
        <b-button @click="modalShow = !modalShow" size="sm" variant="pink-500" class="text-white">Calon Pasien</b-button>
        <b-modal v-model="modalShow" size="xl" title="Calon Pasien" hide-footer>
            <div class="card">
                <div class="card-header bg-indigo">
                    <p class="my-0 fw-600 c-text-16-18 text-light">Pencarian Pasien</p>
                </div>
                <div class="card-body">
                    <b-form action="index.html" class="c-form">
                        <div class="col px-0">
                            <div class="row mx-0">
                                <div class="col mt-1">
                                    <label>Nama Pendaftaran</label>
                                    <b-form-input type="text"></b-form-input>
                                </div>
                                <div class="col mt-1">
                                    <label>Cara Masuk Sub</label>
                                    <b-form-select v-model="selected" :options="options"></b-form-select>
                                </div>
                            </div>
                            <div class="row mx-0 mt-lg-12">
                                <div class="col mt-1">
                                    <label>Asal Rujukan</label>
                                    <b-form-select v-model="selected" :options="options"></b-form-select>
                                </div>
                                <div class="col mt-1">
                                    <label>Waktu Daftar</label>
                                    <div class="row mx-0">
                                        <b-form-input type="date" class="col mr-1"></b-form-input>
                                        <b-form-input type="time" class="col ml-1"></b-form-input>
                                    </div>
                                </div>
                            </div>
                            <div class="col d-flex justify-content-end mt-lg-24">
                                <div class="col col-lg-3 px-0">
                                    <div class="row mx-0">
                                        <b-button pill variant="outline-indigo" class="col mr-1">
                                            <b-icon icon="x-circle" scale="0.8"></b-icon>
                                            <span class="text-regular-14 ml-1">Clear filter</span>
                                        </b-button>
                                        <b-button pill variant="indigo" class="col" style="font-size: 14px;">Cari</b-button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </b-form>
                </div>
            </div>
            <div class="card mt-16">
                <div class="card-body">
                    <div class="mb-3 text-regular-16">
                        Tampilkan
                        <select name="" id="">
                            <option value="">10</option>
                            <option value="">15</option>
                            <option value="">20</option>
                        </select>
                        entri
                    </div>
                    <div class="col px-0">
                        <b-table small striped hover responsive :items="candidate_items" :fields="candidate_fields">
                            <template #cell(aksi)>
                                <div class="row mx-0">
                                    <b-button variant="warning" class="my-0 mr-1 c-btn-xs-ico">
                                        <b-icon icon="pencil-square"></b-icon>
                                    </b-button>
                                    <b-button variant="danger" class="m-0 c-btn-xs-ico">
                                        <b-icon icon="trash-fill"></b-icon>
                                    </b-button>
                                </div>
                            </template>
                        </b-table>

                    </div>
                </div>
            </div>
        </b-modal>
    </div>
</template>

<script>
export default {
    data() {
        return {
            modalShow: false,
            selected: null,
            options: [
                { value: null, text: '-- Pilih --' },
                { value: 'a', text: 'This is option 1' },
                { value: 'b', text: 'This is option 2' },
                { value: 'c', text: 'This is option 3' },
                { value: 'd', text: 'This is option 4' }
            ],
            candidate_fields: [
                { key: 'pendaftaran', label: 'Nama Pendaftaran' },
                { key: 'catatan', label: 'Catatan' },
                { key: 'masuk', label: 'Cara Masuk Sub' },
                { key: 'rujukan', label: 'Rujukan' },
                { key: 'date', label: 'Date Added' },
                { key: 'aksi', label: 'Aksi' },
            ],
            candidate_items: [
                { pendaftaran: '-', catatan: '-', masuk: '-', rujukan: '-', date: '-' },
                { pendaftaran: '-', catatan: '-', masuk: '-', rujukan: '-', date: '-' },
                { pendaftaran: '-', catatan: '-', masuk: '-', rujukan: '-', date: '-' },
                { pendaftaran: '-', catatan: '-', masuk: '-', rujukan: '-', date: '-' },
            ],
        }
    },
}
</script>