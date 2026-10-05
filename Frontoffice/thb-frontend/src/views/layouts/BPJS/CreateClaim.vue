<template>
    <div>
        <div class="col">
            <section ref="section_1">
                <div class="row c-form">
                    <div class="col-lg-6">
                        <div>
                            <label class="c-form-label">No SEP</label>
                            <b-form-select></b-form-select>
                        </div>
                        <div class="mt-12">
                            <label class="c-form-label">No kartu</label>
                            <b-form-input></b-form-input>
                        </div>
                        <div class="mt-12">
                            <label class="c-form-label">Nama</label>
                            <b-form-input></b-form-input>
                        </div>
                    </div>
                    <div class="col-lg-6 mt-12 mt-lg-0">
                        <div>
                            <label class="c-form-label">No RM</label>
                            <b-form-input type="number"></b-form-input>
                        </div>
                        <div class="mt-12">
                            <label class="c-form-label">Penjamin</label>
                            <b-form-input></b-form-input>
                        </div>
                        <div class="mt-12">
                            <label class="c-form-label">Jenis Kelamin</label>
                            <b-form-select></b-form-select>
                        </div>
                    </div>
                </div>
            </section>
            <section ref="section_2" class="mt-32">
                <div class="card">
                    <div class="card-header bg-indigo-200"></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <div>
                                    <label class="c-form-label">Tanggal Masuk</label>
                                    <b-input-group>
                                        <b-form-input id="example-input" v-model="datePick" type="text"
                                            placeholder="YYYY-MM-DD" autocomplete="off"></b-form-input>
                                        <b-input-group-append>
                                            <b-form-datepicker v-model="datePick" button-only right locale="en-US"
                                                aria-controls="example-input"></b-form-datepicker>
                                        </b-input-group-append>
                                    </b-input-group>
                                </div>
                                <div class="mt-12">
                                    <label class="c-form-label">Jenis Layanan</label>
                                    <b-form-input></b-form-input>
                                </div>
                                <div class="mt-12">
                                    <label class="c-form-label">Spesialistik</label>
                                    <b-form-input></b-form-input>
                                </div>
                                <div class="mt-12">
                                    <label class="c-form-label">Tanggal Pulang</label>
                                    <b-input-group>
                                        <b-form-input id="example-input" v-model="datePick" type="text"
                                            placeholder="YYYY-MM-DD" autocomplete="off"></b-form-input>
                                        <b-input-group-append>
                                            <b-form-datepicker v-model="datePick" button-only right locale="en-US"
                                                aria-controls="example-input"></b-form-datepicker>
                                        </b-input-group-append>
                                    </b-input-group>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mt-12 mt-lg-0">
                                    <label class="c-form-label">Kelas Rawat</label>
                                    <b-form-input></b-form-input>
                                </div>
                                <div class="mt-12">
                                    <label class="c-form-label">Ruang Rawat</label>
                                    <b-form-input></b-form-input>
                                </div>
                                <div class="mt-12">
                                    <label class="c-form-label">Cara Pulang</label>
                                    <b-form-input></b-form-input>
                                </div>
                                <div class="mt-12">
                                    <label class="c-form-label">Kondisi Pulang</label>
                                    <b-form-input></b-form-input>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section ref="section_3" class="mt-32">
                <div class="card">
                    <div class="card-header bg-indigo-200 text-uppercase">Diagnosa</div>
                    <div class="card-body">
                        <b-table-simple fixed bordered small>
                            <b-thead>
                                <b-tr>
                                    <b-th>#</b-th>
                                    <b-th>Diagnosa</b-th>
                                    <b-th>Kode</b-th>
                                </b-tr>
                            </b-thead>
                        </b-table-simple>
                    </div>
                </div>
            </section>
            <section ref="section_4" class="mt-32">
                <div class="card">
                    <div class="card-header bg-indigo-200 text-uppercase">PROCEDURE / TINDAKAN</div>
                    <div class="card-body">
                        <b-table-simple fixed bordered small>
                            <b-thead>
                                <b-tr>
                                    <b-th>Diagnosa</b-th>
                                    <b-th>Kode</b-th>
                                </b-tr>
                            </b-thead>
                        </b-table-simple>
                    </div>
                </div>
            </section>
            <section ref="section_2" class="mt-32">
                <div class="card">
                    <div class="card-header bg-indigo-200"></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <div>
                                    <label class="c-form-label">Tindak Lanjut</label>
                                    <b-form-input></b-form-input>
                                </div>
                                <div class="mt-12">
                                    <label class="c-form-label">DPJP</label>
                                    <b-form-input></b-form-input>
                                </div>
                                <div class="mt-12">
                                    <label class="c-form-label">Dirujuk Ke</label>
                                    <b-form-input></b-form-input>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mt-12 mt-lg-0">
                                    <label class="c-form-label">Kontrol Kembali</label>
                                    <b-form-input></b-form-input>
                                </div>
                                <div class="mt-12">
                                    <label class="c-form-label">Ke Poli</label>
                                    <b-form-input></b-form-input>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <div class="d-flex mt-32">
                <router-link :to="{ name: 'BpjsClaimSubmissionSheetView' }">
                    <b-button variant="outline-secondary" class="mr-2">
                        <feather-icon icon="ChevronLeftIcon" /> Kembali
                    </b-button>
                </router-link>
                <b-button variant="indigo-500">
                    <feather-icon icon="SendIcon" /> Kirim
                </b-button>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            datePick: '',
        }
    },
}
</script>