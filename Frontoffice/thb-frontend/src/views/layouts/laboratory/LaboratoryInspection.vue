<template>
    <div>
        <TopLaboratoryMenu selectedMenu="menu2" />
        <div class="col mt-32">
            <div class="card">
                <div @click="showFilter = !showFilter" class="card-header d-flex justify-content-between bg-primary-200">
                    <span class="text-bold-18">Filter Data</span>
                    <b-icon v-if="showFilter" icon="chevron-up" />
                    <b-icon v-else icon="chevron-down" />
                </div>
                <div v-if="showFilter" class="card-body">
                    <div class="row">
                        <div class="col-lg-6 px-lg-5">
                            <div>
                                <label class="c-form-label">Jaminan</label>
                                <b-form-select></b-form-select>
                            </div>
                            <div class="mt-12">
                                <label class="c-form-label">Pemeriksaan</label>
                                <b-form-select></b-form-select>
                            </div>
                        </div>
                        <div class="col-lg-6 px-lg-5">
                            <div class="mt-12 mt-lg-0">
                                <label class="c-form-label">Perusahaan</label>
                                <b-form-select></b-form-select>
                            </div>
                            <div class="mt-12">
                                <label class="c-form-label">Tanggal Request</label>
                                <div class="row mx-0">
                                    <b-form-datepicker class="col"></b-form-datepicker>
                                    <span class="px-3">-</span>
                                    <b-form-datepicker class="col"></b-form-datepicker>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-lg-end mt-40">
                        <div class="col-lg-4">
                            <div class="row">
                                <div class="col-6">
                                    <b-button pill class="btn-block" variant="outline-primary">
                                        <b-icon icon="x" /> Clear Filters
                                    </b-button>
                                </div>
                                <div class="col-6">
                                    <b-button pill class="btn-block" variant="primary">Filter Data</b-button>
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
import TopLaboratoryMenu from '@/@core/components/top-menu/TopLaboratoryMenu.vue';

export default {
    name: 'LaboratoryListView',
    components: {
        TopLaboratoryMenu,
    },
    data() {
        return {
            showFilter: true,
        };
    },
}
</script>