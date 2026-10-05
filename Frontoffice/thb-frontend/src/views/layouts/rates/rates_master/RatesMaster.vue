<template>
    <div>
        <TopRatesMenu selectedMenu="menu1" />
        <div class="col mt-32">
            <div class="mt-32">
                <div class="card">
                    <div class="card-header font-weight-bold">Tambah Data</div>
                    <div class="card-body">
                        <div class="d-flex justify-content-center">
                            <div class="col-lg-10">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div>
                                            <label class="c-form-label">Kode Internal</label>
                                            <b-form-input />
                                        </div>
                                        <div class="mt-12">
                                            <label class="c-form-label">Nama Tarif</label>
                                            <b-form-input />
                                        </div>
                                        <div class="mt-12">
                                            <label class="c-form-label">Keterangan</label>
                                            <b-form-input />
                                        </div>
                                        <div class="mt-12">
                                            <label class="c-form-label">Jenis Tarif</label>
                                            <b-input-group>
                                                <b-form-select></b-form-select>
                                                <b-input-group-append>
                                                    <b-button><feather-icon icon="PlusIcon" /></b-button>
                                                </b-input-group-append>
                                            </b-input-group>
                                        </div>
                                        <div class="mt-12">
                                            <label class="c-form-label">Sub Jenis Tarif</label>
                                            <b-input-group>
                                                <b-form-select></b-form-select>
                                                <b-input-group-append>
                                                    <b-button><feather-icon icon="PlusIcon" /></b-button>
                                                </b-input-group-append>
                                            </b-input-group>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mt-12 mt-lg-0">
                                            <label class="c-form-label">Kelas</label>
                                            <b-input-group>
                                                <b-form-select></b-form-select>
                                                <b-input-group-append>
                                                    <b-button><feather-icon icon="PlusIcon" /></b-button>
                                                </b-input-group-append>
                                            </b-input-group>
                                        </div>
                                        <div class="mt-12">
                                            <label class="c-form-label">Harga</label>
                                            <b-input-group prepend="Rp" append=".00">
                                                <b-form-input></b-form-input>
                                            </b-input-group>
                                        </div>
                                        <div class="mt-12">
                                            <label class="c-form-label">Share Dokter</label>
                                            <b-input-group prepend="Rp" append=".00">
                                                <b-form-input></b-form-input>
                                            </b-input-group>
                                        </div>
                                        <div class="mt-12">
                                            <label class="c-form-label">Share RS</label>
                                            <b-input-group prepend="Rp" append=".00">
                                                <b-form-input></b-form-input>
                                            </b-input-group>
                                        </div>
                                        <div class="mt-12">
                                            <label class="c-form-label">Share Perawat</label>
                                            <b-input-group prepend="Rp" append=".00">
                                                <b-form-input></b-form-input>
                                            </b-input-group>
                                        </div>
                                        <div class="mt-12">
                                            <label class="c-form-label">Biaya Administrasi</label>
                                            <b-form-radio-group>
                                                <b-form-radio>Ya</b-form-radio>
                                                <b-form-radio>Tidak</b-form-radio>
                                            </b-form-radio-group>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-lg-end mt-24">
                                    <b-button variant="success-600" class="mr-2">Tambah</b-button>
                                    <b-button variant="warning-500" class="mr-2">Reset</b-button>
                                    <b-button variant="info-600">Excel</b-button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import TopRatesMenu from '@/@core/components/top-menu/TopRatesMenu.vue';

export default {
    components: {
        TopRatesMenu
    },

} 
</script>