<template>
    <div>
        <div class="col px-0 card">
            <div @click="showFilter = !showFilter" class="card-header d-flex pointer bg-indigo-200">
                <span class="text-semi-16">Filter Data</span>
                <b-icon icon="chevron-down" class="ml-auto" />
            </div>
            <div v-show="showFilter" class="card-body">
                <b-form action="index.html">
                    <div class="row mx-0">
                        <div class="col-lg-6">
                            <div class="mt-10 mt-lg-0 c-form">
                                <label>No Pendaftaran</label>
                                <b-form-input type="number"></b-form-input>
                            </div>
                            <div class="mt-10 c-form">
                                <label>No RM</label>
                                <b-form-input type="number"></b-form-input>
                            </div>
                            <div class="mt-10">
                                <label class="c-form-label">Jenis Pasien</label>
                                <b-form-radio-group>
                                    <b-form-radio>Pasien Lama</b-form-radio>
                                    <b-form-radio>Pasien baru</b-form-radio>
                                </b-form-radio-group>
                            </div>
                            <div class="mt-10">
                                <label class="c-form-label">Jenis Kelamin</label>
                                <b-form-radio-group>
                                    <b-form-radio>Laki-laki</b-form-radio>
                                    <b-form-radio>Perempuan</b-form-radio>
                                </b-form-radio-group>
                            </div>
                            <div v-if="formType == 'default'" class="mt-10 c-form">
                                <label>Instalasi</label>
                                <b-form-select v-model="selected" :options="options"></b-form-select>
                            </div>
                        </div>
                        <div class="col-lg-6 c-form">
                            <div v-if="formType == 'cancel'" class="mt-10 mt-lg-0">
                                <label>Instalasi</label>
                                <b-form-input type="text"></b-form-input>
                            </div>
                            <div v-if="formType == 'default'" class="mt-10 mt-lg-0">
                                <label>Instalasi Sub</label>
                                <b-form-input type="text"></b-form-input>
                            </div>
                            <div v-if="formType == 'default'" class="mt-10">
                                <label>Dokter</label>
                                <b-form-select v-model="selected" :options="options"
                                ></b-form-select>
                            </div>
                            <div class="mt-10">
                                <label>Kota</label>
                                <b-form-select v-model="selected" :options="options"
                                ></b-form-select>
                            </div>
                            <div v-if="formType == 'default'" class="mt-10">
                                <label>Status Pasien</label>
                                <b-form-select v-model="selected" :options="options"
                                ></b-form-select>
                            </div>
                            <div v-if="formType == 'default'" class="mt-10">
                                <label>Cara Pulang</label>
                                <b-form-select v-model="selected" :options="options"
                                ></b-form-select>
                            </div>
                            <div class="col px-0 mt-10">
                                <label>Tanggal Pendaftaran</label>
                                <div class="row mx-0">
                                    <b-form-input type="date" class="col"></b-form-input>
                                    <span class="mx-1">-</span>
                                    <b-form-input type="time" class="col"></b-form-input>
                                </div>
                            </div>
                        </div>
                    </div>
                </b-form>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            showFilter: false,
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
    props: {
        formType: {
            type: String,
            default: 'default'
        }
    }
}

</script>
<style>
@import '../../css/utility.css';

.c-box {
    background: #F8F9FA;
    box-shadow: 0px 2px 2px rgba(128, 109, 92, 0.4);
    border-radius: 4px;
}
</style>