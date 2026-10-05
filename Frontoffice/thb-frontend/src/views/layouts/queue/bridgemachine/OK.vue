<template>
    <div>
        <section class="d-flex">
            <b-button pill @click="toggleMainTab('list')"
                :variant="active.main_tab === 'list' ? 'primary-500' : 'outline-primary-500'" class="mr-2">List
            </b-button>
            <b-button pill @click="toggleMainTab('add')"
                :variant="active.main_tab === 'add' ? 'primary-500' : 'outline-primary-500'">Tambah Data</b-button>
        </section>
        <section class="mt-24">
            <div v-if="active.main_tab === 'list'">
                <div class="row mx-0">
                    <b-form-group label-cols="6" label-align="left" label-size="sm" label-for="sortBySelect" class="text-nowrap mb-md-0 mr-1">
                        <span class="text-regular-14">Tampilkan</span>
                        <b-form-select id="per-page-select" v-model="perPage" size="sm" :options="pageOptions"/>
                        <span class="text-regular-14">Entri</span>
                    </b-form-group>
                </div>
                <b-overlay :show="show" spinner-variant="primary" spinner-type="grow" spinner-small rounded="sm">
                    <b-table :fields="OkTable.fields" :items="OkTable.items" :per-page="0" :current-page="currentPage" bordered responsive class="mt-16" style="font-size: 14px;">
                        <template #cell(date_added)="data">
                            <div>{{ parseDate(data.value) }}</div>
                        </template>
                        <template #cell(action)="row">
                            <div class="d-flex">
                                <div>
                                    <b-button @click="editData(row.item)" variant="warning-500" size="sm" class="mr-1">
                                        <feather-icon icon="EditIcon" />
                                    </b-button>
                                </div>
                                <div>
                                    <b-button @click="rowOnDelete(row.item.id)" variant="danger-500" size="sm">
                                        <feather-icon icon="TrashIcon" />
                                    </b-button>
                                </div>
                            </div>
                        </template>
                    </b-table>
                </b-overlay>
                <div class="col px-0 mt-20">
                    <div class="d-flex">
                        <span class="text-regular-14">Menampilkan {{ firstRows }} hingga <template
                                v-if="lastRows < totalRows">{{ lastRows }}</template> <template v-else>{{ totalRows
                                }}</template> dari {{ totalRows }} entri</span>
                        <div class="ml-auto">
                            <b-pagination v-model="currentPage" :total-rows="totalRows" :per-page="perPage"
                                align="fill" size="sm" class="my-0"></b-pagination>
                        </div>
                    </div>
                </div>

            </div>
            <div v-if="active.main_tab === 'add'">
                <div class="card">
                    <div class="card-header font-weight-bold bg-primary text-white">Tambah Data</div>
                    <div class="card-body">
                        <div class="d-flex justify-content-center">
                            <div class="col-lg-8">
                                <div class="row">
                                    <div class="col-lg-6 mt-12">
                                        <label class="c-form-label">Nomor Kartu</label>
                                        <b-input-group>
                                            <b-form-input v-model="form.card_number" type="number"></b-form-input>
                                            
                                            <b-input-group-append>
                                                <b-button variant="primary"><b-icon icon="search" /></b-button>
                                            </b-input-group-append>
                                        </b-input-group>
                                    </div>
                                    <div class="col-lg-6 mt-12">
                                        <label class="c-form-label">Nama Peserta</label>
                                        <b-form-input v-model="form.name"></b-form-input>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6 mt-12">
                                        <label for="" class="c-form-label">Poli</label>
                                        <v-select v-model="form.health_facility_item_id" aria-readonly="true" :options="OptionsHealthFacilityItem" :reduce="OptionsHealthFacilityItem => OptionsHealthFacilityItem.value" label="title" placeholder="-- Poliklinik --"></v-select>
                                    </div>
                                    <div class="col-lg-6 mt-12">
                                        <label for="" class="c-form-label">Tindakan Operasi</label>
                                        <v-select v-model="form.surgery" aria-readonly="true" :options="OptionsSurgery" :reduce="OptionsSurgery => OptionsSurgery.value" label="title" placeholder="-- Tindakan --" ></v-select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6 mt-12">
                                        <label for="" class="c-form-label">Tanggal Operasi</label>
                                        <b-form-input v-model="form.surgery_date" type="date"></b-form-input>
                                    </div>
                                    <div class="col-lg-6 mt-12">
                                        <label for="" class="c-form-label">Status</label>
                                        <v-select v-model="form.status" aria-readonly="true" :options="OptionsStatus" :reduce="OptionsStatus => OptionsStatus.value" label="title" placeholder="-- Status--" ></v-select>
                                    </div>
                                </div>
                                <div class="mt-24 d-flex justify-content-end">
                                    <b-button :disabled="isDisabled" @click.prevent="cancelForm" variant="outline-primary" >Batal</b-button>
                                    <b-button :disabled="isDisabled" @click.prevent="validationForm" variant="primary" class="ml-2">Simpan</b-button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
<script>
import { mapState } from 'vuex'
import moment from 'moment'
import vSelect from 'vue-select'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'

export default {
    name: 'OK',
    components: {
        vSelect
    },
    data() {
        return {
            active: {
                main_tab: 'list',
            },
            OkTable: {
                fields: [
                    { key: 'no', label: 'No' },
                    { key: 'booking_code', label: 'Kode Booking' },
                    { key: 'date_added', label: 'Tanggal' },
                    { key: 'card_number', label: 'Nomor Kartu' },
                    { key: 'name', label: 'Nama' },
                    { key: 'status', label: 'Status' },
                    { key: 'action', label: 'Aksi' },
                ],
                items: [
                    { no: '1', booking_code: '100200300400', date_added: '2024-09-01 20:34:12', card_number: '100200300400', name: 'John Doe', status: 'Terlaksana' },
                    { no: '2', booking_code: '100200300400', date_added: '2024-09-02 20:34:12', card_number: '100200300400', name: 'Vinsmoke Sanji', status: 'Belum Terlaksana' },
                    // { no: '', booking_code: '', date_added: '', card_number: '', name: '', status: '' },
                ]
            },
            perPage: 20,
            params: {
                offset: 0,
                limit: 20,
            },
            pageOptions: [20, 50, 100],
            firstRows: 1,
            lastRows: 20,
            currentPage: 1,
            show: false,
            isDisabled: false,

            form: {
                id: null,
                card_number: '',
                name: '',
                health_facility_item_id: '',
                surgery: '',
                surgeru_date: '',
                status: '',
            },
            OptionsSurgery: [
                { value: '1', title: 'One'},
                { value: '2', title: 'Two'},
                { value: '3', title: 'Three'},
                { value: '4', title: 'Four'},
            ],
            OptionsStatus: [
                { value: '1', title: 'One'},
                { value: '2', title: 'Two'},
                { value: '3', title: 'Three'},
                { value: '4', title: 'Four'},
            ]
        }
    },
    // watch: {
    //     'items': function( val) {
    //         console.log(val)
    //     },
    //     currentPage: {
    //         handler: function (value) {
    //             console.log(value)
    //             this.firstRows = this.perPage * (value - 1) + 1
    //             this.lastRows = this.perPage * (value) + 1
    //             this.params.offset = this.perPage * (value - 1)
    //             this.params.limit = this.perPage
    //             // this.$store.dispatch('ok/getAll', this.params)
    //         }
    //     },
    //     perPage: {
    //         handler: function (value) {
    //             this.params.limit = this.perPage
    //             // this.$store.dispatch('ok/getAll', this.params)
    //         }
    //     }
    // },
    computed: {
        ...mapState({
            OptionsHealthFacility: state => state.healthfacility.options.healthfacility,
            OptionsHealthFacilityItem: state => state.healthfacilityitem.options.healthfacilityitem,
            OptionsDoctor: state => state.employee.options.employee,


            // items: state => state.ok.items,
            // pageOptions: state => state.ok.pageOptions,
            // totalRows: state => state.ok.totalRows,
        }),
    },
    methods: {
        toggleMainTab(tab) {
            this.active.main_tab = tab

            this.form.id = null
            this.form.name = ''
            this.form.card_number = ''
            this.form.health_facility_item_id = ''
            this.form.surgery = ''
            this.form.surgery_date = null
            this.form.status = ''
        },
        parseDate(input_date) {
            return moment(input_date).format('DD/MM/YYYY')
        },
        // cancelForm() {
        //     this.form.id = null
        //     this.form.name = ''
        //     this.form.card_number = ''
        //     this.form.health_facility_item_id = ''
        //     this.form.surgery = ''
        //     this.form.surgery_date = null
        //     this.form.status = ''

        //     this.active.main_tab = 'list'
        // },
        // validationForm() {
        //     this.show = true
        //     this.isDisabled = true
        //     const formData = this.form
        //     this.$store.dispatch('ok/formPostPut', formData)
        //     .then(res => {
        //         this.notification('Success', 'CheckIcon', 'success', 'You have successfully')
        //         this.$store.dispatch('ok/getAll', this.params)
        //         this.active.main_tab = 'list'

        //         this.form.id = null
        //         this.form.name = ''
        //         this.form.card_number = ''
        //         this.form.health_facility_item_id = ''
        //         this.form.surgery = ''
        //         this.form.surgery_date = null
        //         this.form.status = ''

        //         this.show = false
        //         this.isDisabled = false
        //     },
        //     err => {
        //         this.show = false
        //         this.isDisabled = false
        //         this.notification('Failed', 'CloseIcon', 'danger', 'You have unsuccessful')
        //     })
        // },
        // notification(a, b, c, d) {
        //     this.$toast({
        //         component: ToastificationContent,
        //         position: 'top-right',
        //         props: { title: a, icon: b, variant: c, text: d }
        //     })
        // },
        // editData(obj) {
        //     this.active.main_tab = 'add'
        //     this.form = obj
        // },
        // rowOnDelete (id) {
        //     this.$bvModal
        //         .msgBoxConfirm('Please confirm that you want to delete everything.', {
        //         title: 'Please Confirm', size: 'sm', okVariant: 'primary', okTitle: 'Yes', cancelTitle: 'No', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
        //     })
        //     .then(value => {
        //         if (value === true){
        //         this.$store.dispatch('ok/remove', id)
        //         .then(response => {
        //             if (response.status === 200) {
        //                 this.$store.dispatch('ok/getAll', this.params)
        //                 this.notification('Success','CheckIcon','success','You have successfully')
        //             } else {
        //                 this.notification('Failed','CloseIcon','danger','You have unsuccessful')
        //             }
        //         },
        //         error => {
        //             this.notification('Failed','CloseIcon','danger','You have unsuccessful')
        //         }) 
        //         }
        //     })
        // }
    },
    mounted() {
        // this.$store.state.totalRows = this.items.length
        this.show = false
    },
    created() {
        this.$store.dispatch('healthfacilityitem/getParam', '3cb626f5-9148-4fb1-8663-3d7f028b01a6')
        // this.$store.dispatch('ok/getAll', this.params)
        this.show = false
    }
}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>