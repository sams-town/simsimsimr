<template>
    <div>
        <TopBPJSeMenu selectedMenu="menu5" />
        <div class="col mt-32">
            <div class="card">
                <div class="card-header font-weight-bold bg-indigo-200">Filter Data</div>
                <div class="card-body">
                    <div class="d-flex justify-content-center">
                        <div class="col-lg-5">
                            <div>
                                <label class="c-form-label">Tanggal SEP</label>
                                <b-form-input type="date" v-model="filter_form.sep_data"></b-form-input>
                            </div>
                            <div class="mt-12">
                                <label class="c-form-label">Jenis Pelayanan</label>
                                <v-select v-model="filter_form.health_facility" aria-readonly="true" :options="OptionsHealthFacility" label="title" placeholder="-- Pelayanan --"></v-select>
                            </div>
                            <div class="d-flex justify-content-lg-end mt-40">
                                <div class="col-lg-3 px-0">
                                    <b-button pill class="w-100" variant="primary">Cari</b-button>
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
import vSelect from 'vue-select'

export default {
    components: {
        TopBPJSeMenu, vSelect
    },
    data() {
        return {
            filter_form: {
                sep_data: '',
                health_facility: '',
            },
            OptionsHealthFacility: [
                { value: 1, title: 'Rawat Inap' },
                { value: 2, title: 'Rawat Jalan' },
            ],
        }
    },
}
</script>
<style lang="scss">
    @import '@core/scss/vue/libs/vue-select.scss';
</style>