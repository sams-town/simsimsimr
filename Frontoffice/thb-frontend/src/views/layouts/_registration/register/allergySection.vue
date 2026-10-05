<template>
    <div class="card border-0">
        <div class="card-header rounded bg-indigo">
            <span class="fw-bold text-white">Alergi</span>
        </div>
        <div class="card-body">
            <div>
                <b-table small hover responsive :items="allergyTable.items" :fields="allergyTable.fields" head-variant="light"
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
            <div class="col px-0 d-flex mt-32">
                <b-button pill size="sm" variant="indigo-200" @click="alergiForm = !alergiForm" :disabled="alergiForm"
                    class="ml-auto px-3">
                    <img src="../../../../@core/assets/icons/plusPurp.svg" class="mr-2" />
                    <span class="text-indigo-700 fw-medium">Tambah Alergi</span>
                </b-button>
            </div>
            <div v-show="alergiForm" class="col px-0 mt-32">
                <form class="c-form">
                    <div class="row mx-0">
                        <div class="col-12 col-lg px-0 px-lg-3">
                            <label>Alergi</label>
                            <b-form-input type="text"></b-form-input>
                        </div>
                        <div class="col-12 col-lg px-0 px-lg-3">
                            <label>Jenis Alergi</label>
                            <b-form-select v-model="selected" :options="options"></b-form-select>
                        </div>
                        <div class="col-12 col-lg px-0 px-lg-3">
                            <label>Reaksi</label>
                            <b-form-input type="text"></b-form-input>
                        </div>
                    </div>
                    <div class="row mx-0 mt-0 mt-lg-12">
                        <div class="col-12 col-lg px-0 px-lg-3">
                            <label>Tingkat Keparahan</label>
                            <b-form-input type="text"></b-form-input>
                        </div>
                        <div class="col-12 col-lg px-0 px-lg-3">
                            <label>Efek Samping</label>
                            <b-form-input type="text"></b-form-input>
                        </div>
                        <div class="col-12 col-lg px-0 px-lg-3">
                            <label>Tanggal Kejadian</label>
                            <b-form-input type="date"></b-form-input>
                        </div>
                    </div>
                    <div class="col mt-16 mt-lg-24 d-flex">
                        <div class="ml-auto row mx-0">
                            <b-button pill variant="outline-indigo" class="px-3 mr-2"
                                @click="alergiForm = !alergiForm">Batal</b-button>
                            <b-button pill variant="indigo" class="px-3">Simpan</b-button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
<script>

export default {
    data() {
        return {
            alergiForm: false,
            // table
            allergyTable: {

                fields: [
                    { key: 'No' },
                    { key: 'Alergi' },
                    { key: 'Jenis', label: 'Jenis Alergi' },
                    { key: 'Reaksi' },
                    { key: 'Parah', label: 'Tingkat Keparahan' },
                    { key: 'Efek', label: 'Efek Samping' },
                    { key: 'Tanggal', label: 'Tgl Kejadian' },
                    { key: 'Aksi' },
                ],
                items: [
                    { No: 1, Alergi: 'Paracetamol', Jenis: 'Obat', Reaksi: 'Gatal', Parah: 'Sedang', Efek: 'Merah', Tanggal: '01-12-2022 16:33' },
                    { No: 2, Alergi: 'Paracetamol', Jenis: 'Obat', Reaksi: 'Gatal', Parah: 'Sedang', Efek: 'Merah', Tanggal: '01-12-2022 16:33' },
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
}
</script>
<style scoped>
@import '../../../../@core/css/utility.css';
</style>