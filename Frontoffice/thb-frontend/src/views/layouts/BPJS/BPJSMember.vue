<template>
    <div>
        <TopBPJSeMenu selectedMenu="menu1" />
        <div class="col mt-32">
            <div class="card border-0">
                <div class="card-body bg-neutral-100 rounded">
                    <div class="d-flex justify-content-between">
                        <p class="mb-0 text-bold-24">Cek Peserta BPJS</p>
                        <div class="col-6 col-lg-3 d-flex align-items-center position-relative">
                            <b-form-input autocomplete="off" placeholder="Search" class="rounded-pill pl-5"></b-form-input>
                            <b-icon icon="search" class="position-absolute pl-3 text-neutral-400" />
                        </div>
                    </div>
                    <hr class="col p-0 my-32">
                    <div class="row">
                        <div class="col-lg-6 px-lg-4">
                            <div ref="section-1">
                                <p class="text-bold-18">COB</p>
                                <div class="row mt-16">
                                    <div class="col-lg-6">
                                        <label class="c-form-label">No Asuransi</label>
                                        <b-form-input type="number"></b-form-input>
                                    </div>
                                    <div class="col-lg-6 mt-12 mt-lg-0">
                                        <label class="c-form-label">Asuransi</label>
                                        <b-form-datepicker></b-form-datepicker>
                                    </div>
                                </div>
                                <div class="row mt-lg-16">
                                    <div class="col-lg-6 mt-12 mt-lg-0">
                                        <label class="c-form-label">Tanggal TAT</label>
                                        <b-form-datepicker></b-form-datepicker>
                                    </div>
                                    <div class="col-lg-6 mt-12 mt-lg-0">
                                        <label class="c-form-label">Tanggal TMT</label>
                                        <b-form-datepicker></b-form-datepicker>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-40" ref="section-2">
                                <p class="text-bold-18">Jenis Peserta</p>
                                <div class="row mt-16">
                                    <div class="col-lg-6">
                                        <label class="c-form-label">Kode</label>
                                        <b-form-input></b-form-input>
                                    </div>
                                    <div class="col-lg-6 mt-12 mt-lg-0">
                                        <label class="c-form-label">Keterangan</label>
                                        <b-form-input></b-form-input>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-40" ref="section-3">
                                <p class="text-bold-18">Informasi</p>
                                <div class="row mt-16">
                                    <div class="col-lg-6">
                                        <label class="c-form-label">Nama</label>
                                        <b-form-input></b-form-input>
                                    </div>
                                    <div class="col-lg-6 mt-12 mt-lg-0">
                                        <label class="c-form-label">NIK</label>
                                        <b-form-input type="number"></b-form-input>
                                    </div>
                                </div>
                                <div class="row mt-lg-16">
                                    <div class="col-lg-6 mt-12 mt-lg-0">
                                        <label class="c-form-label">No Kartu</label>
                                        <b-form-input type="number"></b-form-input>
                                    </div>
                                    <div class="col-lg-6 mt-12 mt-lg-0">
                                        <label class="c-form-label">Pisa</label>
                                        <b-form-input></b-form-input>
                                    </div>
                                </div>
                                <div class="row mt-lg-16">
                                    <div class="col-lg-6 mt-12 mt-lg-0">
                                        <label class="c-form-label">Kode Provider</label>
                                        <b-form-input type="number"></b-form-input>
                                    </div>
                                    <div class="col-lg-6 mt-12 mt-lg-0">
                                        <label class="c-form-label">Nama Provider</label>
                                        <b-form-input></b-form-input>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 px-lg-4">
                            <div class="mt-40 mt-lg-0" ref="section-4">
                                <p class="text-bold-18">Hak Kelas</p>
                                <div class="row mt-16">
                                    <div class="col-lg-6">
                                        <label class="c-form-label">Kode</label>
                                        <b-form-input></b-form-input>
                                    </div>
                                    <div class="col-lg-6 mt-12 mt-lg-0">
                                        <label class="c-form-label">Keterangan</label>
                                        <b-form-input></b-form-input>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-40" ref="section-5">
                                <p class="text-bold-18">Informasi</p>
                                <div class="row mt-16">
                                    <div class="col-lg-6">
                                        <label class="c-form-label">Dinsos</label>
                                        <b-form-input></b-form-input>
                                    </div>
                                    <div class="col-lg-6 mt-12 mt-lg-0">
                                        <label class="c-form-label">No SKTM</label>
                                        <b-form-input type="number"></b-form-input>
                                    </div>
                                </div>
                                <div class="row mt-lg-16">
                                    <div class="col-lg-6 mt-12 mt-lg-0">
                                        <label class="c-form-label">Prolanis PRB</label>
                                        <b-form-input></b-form-input>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-40" ref="section-6">
                                <p class="text-bold-18">Mr</p>
                                <div class="row mt-16">
                                    <div class="col-lg-6">
                                        <label class="c-form-label">No MR</label>
                                        <b-form-input type="number"></b-form-input>
                                    </div>
                                    <div class="col-lg-6 mt-12 mt-lg-0">
                                        <label class="c-form-label">No Telepon</label>
                                        <b-form-input type="number"></b-form-input>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-40" ref="section-7">
                                <p class="text-bold-18">Umur</p>
                                <div class="row mt-16">
                                    <div class="col-lg-6">
                                        <label class="c-form-label">Umur Saat Pelayanan</label>
                                        <b-form-input type="number"></b-form-input>
                                    </div>
                                    <div class="col-lg-6 mt-12 mt-lg-0">
                                        <label class="c-form-label">Umur Sekarang</label>
                                        <b-form-input type="number"></b-form-input>
                                    </div>
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
import TopBPJSeMenu from '@/@core/components/top-menu/TopBPJSMenu.vue';

export default {
    components: {
        TopBPJSeMenu
    }
}

</script>