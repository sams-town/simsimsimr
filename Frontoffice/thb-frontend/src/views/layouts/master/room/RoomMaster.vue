<template>
    <div>
        <section class="d-flex">
            <b-button pill @click="toggleMainTab('list')"
                :variant="active.main_tab === 'list' ? 'primary-500' : 'outline-primary-500'" class="mr-2">List
                Kamar</b-button>
            <b-button pill @click="toggleMainTab('add')"
                :variant="active.main_tab === 'add' ? 'primary-500' : 'outline-primary-500'">Tambah Kamar</b-button>
        </section>
        <section class="mt-24">
            <div v-if="active.main_tab === 'list'">
                <section class="card">
                    <div class="card-header font-weight-bold bg-primary-200">Filter Data</div>
                    <div class="card-body">
                        <div class="d-flex justify-content-center">
                            <div class="col-lg-4">
                                <div class="">
                                    <label class="c-form-label">Nama Ruangan</label>
                                    <b-form-input v-model="params.name" />
                                </div>
                                <div class="mt-12">
                                    <label class="c-form-label">Tipe Ruangan</label>
                                    <v-select v-model="params.type_room_id" :options="TypeRoomOptions" :reduce="TypeRoomOptions => TypeRoomOptions.value" label="title" placeholder="-- Pilih Tipe Ruangan --"></v-select>
                                </div>
                                <div class="d-flex mt-24">
                                    <b-button @click.prevent="filterData" :disabled="isDisabledFilter" variant="primary">
                                        <feather-icon icon="SearchIcon" /> Filter
                                    </b-button>
                                    <b-button @click.prevent="filterReset" :disabled="isDisabledFilter" variant="outline-primary" class="ml-2">
                                        <feather-icon icon="XIcon" /> Clear Filter
                                    </b-button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section class="mt-32">
                    <div class="row mx-0">
                        <b-form-group label-cols="6" label-align="left" label-size="sm" label-for="sortBySelect" class="text-nowrap mb-md-0 mr-1">
                            <span class="text-regular-14">Tampilkan</span>
                            <b-form-select id="per-page-select" v-model="perPage" size="sm" :options="pageOptions"/>
                            <span class="text-regular-14">Entri</span>
                        </b-form-group>
                    </div>
                    <b-table bordered responsive hover small head-variant="light" :items="items" :fields="fields" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="mt-12 text-center">
                        <template #cell(is_delete)="data">
                            <b-badge :variant="status[1][data.value]">
                            {{ status[0][data.value] }}
                            </b-badge>
                        </template>
                        <template #cell(action)="row">
                            <div class="row mx-0">
                                <b-button @click="editData(row.item)" variant="warning-500" size="sm" class="mr-1">
                                    <feather-icon icon="EditIcon" />
                                </b-button>
                                <b-button @click="rowOnDelete(row.item.id)" variant="danger-500" size="sm">
                                    <feather-icon icon="TrashIcon" />
                                    <b-link v-b-modal.modal-center>
                                    </b-link>
                                </b-button>
                            </div>
                        </template>
                    </b-table>
                    <div class="col px-0 mt-20">
                        <div class="d-flex">
                            <span class="text-regular-14">Menampilkan {{ firstRows }} hingga <template v-if="lastRows < totalRows">{{ lastRows }}</template> <template v-else>{{ totalRows }}</template> dari {{ totalRows }} entri</span>
                            <div class="ml-auto">
                                <b-pagination 
                                    v-model="currentPage" 
                                    :total-rows="totalRows"
                                    :per-page="perPage" 
                                    align="fill" size="sm" class="my-0"></b-pagination>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="col px-0 mt-20">
                        <div class="d-flex">
                            <span class="text-regular-14">Menampilkan {{ firstRow }} hingga {{ lastRow }} dari {{
                                totalRows }} entri</span>
                            <div class="ml-auto">
                                <b-pagination v-model="currentPage" :total-rows="totalRows"
                                    :per-page="perPage" :current-page="currentPage"
                                    align="fill" size="sm" class="my-0"></b-pagination>
                            </div>
                        </div>
                    </div> -->
                </section>
            </div>
            <div v-if="active.main_tab === 'add'">
                <div class="card">
                    <div class="card-header font-weight-bold bg-primary-200">Tambah Data</div>
                    <b-overlay
                        :show="show"
                        spinner-variant="primary"
                        spinner-type="grow"
                        spinner-small
                        rounded="sm"
                        >
                    <div class="card-body">
                        <div class="d-flex justify-content-center">
                            <div class="col-lg-10">
                                <div class="row">
                                    <div class="col-lg-6 mt-12 d-none">
                                        <div>
                                            <label class="c-form-label">Instalasi</label>
                                            <v-select v-model="form.health_facility_id" aria-readonly="true" :options="OptionsHealthFacility" :reduce="OptionsHealthFacility => OptionsHealthFacility.value" label="title" placeholder="--Instalasi --"></v-select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mt-12">
                                        <label class="c-form-label">Instalasi Sub</label>
                                        <v-select v-model="form.health_facility_item_id" aria-readonly="true" :options="OptionsHealthFacilityItem" :reduce="OptionsHealthFacilityItem => OptionsHealthFacilityItem.value" label="title" placeholder="--Instalasi Sub --"></v-select>
                                    </div>
                                    <div class="col-lg-6 mt-12">
                                        <label class="c-form-label">Nama Kamar</label>
                                        <b-form-input v-model="form.name" autocomplete="off"/>
                                    </div>
                                    <div class="col-lg-6 mt-12">
                                        <label class="c-form-label">Tipe Ruangan</label>
                                        <v-select v-model="form.type_room_id" :options="TypeRoomOptions" :reduce="TypeRoomOptions => TypeRoomOptions.value" label="title" placeholder="-- Pilih Tipe Ruangan --"></v-select>
                                    </div>
                                    <div class="col-lg-6 mt-12">
                                        <label class="c-form-label">Kelas Layanan</label>
                                        <v-select v-model="form.service_class_id" :options="OptionsServiceClass" :reduce="OptionsServiceClass => OptionsServiceClass.value" label="title" placeholder="-- Pilih Kelas Layanan --"></v-select>
                                    </div>
                                    <div class="col-lg-6 mt-12">
                                        <label class="c-form-label">Tarif</label>
                                        <v-select v-model="form.costing_id" :options="CostingOptions" :reduce="CostingOptions => CostingOptions.value" label="title" placeholder="-- Pilih Tarif --"></v-select>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="mt-20">
                                            <label class="c-form-label">Status</label>
                                            <table class="table">
                                                <tr>
                                                    <th>Tempat Tidur</th>
                                                    <th>Status Ketersediaan</th>
                                                    <th>Aksi</th>
                                                    <th>
                                                        <b-button type="submit" variant="info" @click.prevent="onAddSC()"><feather-icon icon="PlusIcon" /></b-button>
                                                    </th>
                                                </tr>
                                                <tr class="item" v-for="(item, index) in form.beds">
                                                    <td>
                                                        <b-form-input type="text" :disabled="item.id ? item.type_status_bed_id != 1 : false" v-model="item.name" aria-readonly="true" />
                                                    </td>
                                                    <td>
                                                        <v-select v-model="item.type_status_bed_id" :options="TypeStatusBedOptions" :reduce="TypeStatusBedOptions => TypeStatusBedOptions.value" label="title" :disabled="item.id ? item.type_status_bed_id != 1 : true" placeholder="-- Pilih Status Tempat Tidur --"></v-select>
                                                    </td>
                                                    <td colspan="2">
                                                        <b-button v-if="item.id ? item.type_status_bed_id == 1 : true" variant="danger-600" size="sm" @click="deleteSC(item, index)" class="mr-2">
                                                            <feather-icon icon="TrashIcon" />
                                                        </b-button>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <b-col cols="12" class="px-0 mt-24">
                                    <b-button type="submit" variant="primary" class="mr-1" @click.prevent="validationForm"> Submit </b-button>
                                    <b-button type="reset" variant="outline-secondary"> Reset </b-button>
                                </b-col>
                            </div>
                        </div>
                    </div>
                    </b-overlay>
                </div>
            </div>
        </section>
    </div>
</template>
<script>
import { mapState } from 'vuex'
import vSelect from 'vue-select'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
export default {
    components: { vSelect },
    name: 'RoomMaster',
    data() {
        return {
            params: {
                limit: 20,
                offset: 0,
                name: '',
                type_room_id: '',
            },
            isDisabledFilter: false,
            // perPage: 20,
            currentPage: 1,
            firstRows: 1,
            lastRows: 20,
            show: false,
            active: {
                main_tab: 'list',
            },
            RoomMasterTable: {
                field: [
                    { key: 'room_name', label: 'Nama Kamar' },
                    { key: 'service_class', label: 'Kelas Layanan' },
                    { key: 'rates', label: 'Tarif' },
                    { key: 'mapping_code', label: 'Kode Mapping' },
                    { key: 'user_added', label: 'User added' },
                    { key: 'date_added', label: 'Date added' },
                    { key: 'user_updated', label: 'User updated' },
                    { key: 'date_updated', label: 'Date updated' },
                    { key: 'action', label: 'Aksi' },
                ],
                item: [
                    {
                        room_name: 'Magnolia 306-C',
                        service_class: 'NON KELAS',
                        rates: 'KELAS I',
                        mapping_code: 'LV02',
                        user_added: 'Giga, Adm',
                        date_added: '2019-08-26 14:05:07',
                        user_updated: 'Giga, Adm',
                        date_updated: '2021-05-11 10:42:44',
                    },
                ],
                currentPage: 1,
                totalRows: 1,
                perPage: 5,
            },
            form: {
                id: null,
                health_facility_id:'40f06534-07ec-4221-ad9d-dea08196079f',
                health_facility_item_id:'',
                service_class_id:'',
                type_room_id:'',
                costing_id:'',
                name:'',
                beds:[],
                beds_deleted: []
            },
            status: [{
                0: 'Active', 1: 'Deleted'
            },
            {
                0: 'primary', 1: 'danger'
            }],
            beds:'',
            tableData: [
                { rates: '', },
            ],
            newRow: {
                rates: '',
            }
        }
    },
    watch: {
        // 'items': function( val) {
        //     console.log(val)
        // },
        currentPage: {
            handler: function(value) {
                this.firstRows = this.perPage*(value-1)+1
                this.lastRows = this.perPage*(value)+1
                this.params.offset = this.perPage*(value-1)
                this.params.limit = this.perPage
                this.$store.dispatch('room/getAllList', this.params)
            }
        },
        perPage: {
            handler: function(value) {
                this.params.limit = this.perPage
                this.$store.dispatch('room/getAllList', this.params)
            }
        },
        // 'form.health_facility_id' (obj) {
        //     return this.$store.dispatch('healthfacilityitem/getParam', obj)
        // }
    },
    computed: {
        ...mapState({ 
            totalRows: state => state.room.totalRows, 
            fields: state => state.room.fields, 
            items: state => state.room.items, 
            perPage: state => state.room.perPage, 
            pageOptions: state => state.room.pageOptions, 
            firstRow: state => state.room.firstRow, 
            lastRow: state => state.room.lastRow, 
            OptionsHealthFacility: state => state.healthfacility.options.healthfacility,
            OptionsHealthFacilityItem: state => state.healthfacilityitem.options.healthfacilityitem,
            OptionsServiceClass: state => state.helper.options.service_class,
            TypeStatusBedOptions: state => state.helper.options.type_status_bed,
            TypeRoomOptions: state => state.helper.options.type_room,
            CostingOptions: state => state.costing.options.serviceData
            // CostingOptions: state => state.costing.options.costing
        }),
        // sortOptions() {
        //     return this.fields.filter(f => f.sortable).map(f => ({ text: f.label, value: f.key }))
        // },
        // dateFormat () {
        //     return create_at => this.$options.filters.formatDate(create_at)
        // },
        // direction () {
        //     if (store.state.appConfig.isRTL) {
        //         this.dir = true
        //         return this.dir
        //     }
        //     this.dir = false
        //     return this.dir
        // }
    },
    methods: {
        filterData () {
            this.show = true
            this.isDisabledFilter = true
            this.$store.dispatch('room/getAllList', this.params)
            .then(res => {
                this.show = false
                this.isDisabledFilter = false
            },
            err => {
                this.show = false
                this.isDisabledFilter = false
            })
        },
        filterReset () {
            this.params.limit = this.perPage
            this.params.offset = 0
            this.params.name = ''
            this.params.type_room_id = ''
            this.$store.dispatch('room/getAllList',this.params)
        },
        onAddSC () {
            this.beds = ''
            this.collation = ''
            this.form.beds.push({ id:'', name:'', type_status_bed_id: 1, action: 'action'})
        },
        deleteSC (data, index) {
            if (data.id) {
                this.form.beds_deleted.push(data)
            }
            this.form.beds.splice(index, 1)
            // console.log('deleted: ', this.form);

        },
        rowOnDelete (id) {
            this.$bvModal
                .msgBoxConfirm('Please confirm that you want to delete this data.', {
                title: 'Please Confirm', size: 'sm', okVariant: 'primary', okTitle: 'Yes', cancelTitle: 'No', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                this.$store.dispatch('room/remove', id)
                .then(response => {
                    if (response.status === 200) {
                    this.$store.dispatch('room/getAllList', this.params)
                    this.notification('Success','CheckIcon','success','You have successfully')
                    } else {
                    this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                    }
                },
                error => {
                    this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                }) 
                }
            })
        },
        toggleMainTab(tab) {
            this.active.main_tab = tab;
            this.form.id = ''
            this.form.health_facility_id = '40f06534-07ec-4221-ad9d-dea08196079f'
            this.form.health_facility_item_id = ''
            this.form.service_class_id = ''
            this.form.type_room_id = ''
            this.form.costing_id = ''
            this.form.name = ''
            this.form.beds = []
            this.form.beds_deleted = []
        },
        validationForm () {
            this.show = true
            // const formData = this.form

            const fields = {
                health_facility_item_id: 'Instalasi sub',
                name: 'Nama kamar',
                type_room_id: 'Tipe ruangan',
                service_class_id: 'Kelas Layanan',
                costing_id: 'Tarif',
            };

            let isFormEmpty = false;
            for (const [key, message] of Object.entries(fields)) {
                if (this.form[key] === '') {
                    this.notification('Warning','CloseIcon','warning', `${message} wajib diisi`)
                    isFormEmpty = true;
                }
            }

            let isBedNameEmpty = false;
            if (this.form.beds.length > 0) {
                for (let index = 0; index < this.form.beds.length; index++) {
                    const element = this.form.beds[index];
                    if (element.name === '') {
                        this.notification('Warning','CloseIcon','warning', `Nama Bed ke-${index+1} wajib diisi`)
                        isBedNameEmpty = true;
                    }
                    
                }
            }

            if (!isFormEmpty && !isBedNameEmpty) {
                this.$store.dispatch('room/formPostPut', this.form)
                .then(res => {
                    this.notification('Success','CheckIcon','success','You have successfully')
                    this.$store.dispatch('room/getAllList', this.params)
                    this.active.main_tab = 'list'
                    // this.form.id = ''
                    this.form.health_facility_id = '40f06534-07ec-4221-ad9d-dea08196079f'
                    this.form.health_facility_item_id = ''
                    this.form.service_class_id = ''
                    this.form.type_room_id = ''
                    this.form.costing_id = ''
                    this.form.name = ''
                    this.form.beds = []
                    this.form.beds_deleted = []
                    this.show = false
                }, 
                err => {
                    this.show = false
                    console.error('ERR GOING HERE', err)
                    this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                })
            }
            this.show = false
        },
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                position: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },
        editData (obj) {
            this.active.main_tab = 'add'
            // console.log(obj)
            this.form.id = obj.id
            this.form.costing_id = obj.costing_id
            this.form.health_facility_id = '40f06534-07ec-4221-ad9d-dea08196079f'
            this.form.health_facility_item_id = obj.health_facility_item_id
            this.form.service_class_id = obj.service_class_id
            this.form.type_room_id = obj.type_room_id
            this.form.name = obj.name
            let beds = {}
            for (let i = 0; i < obj.beds.length; i++) {
                beds = { 
                    id:obj.beds[i].id, 
                    name:obj.beds[i].name, 
                    type_status_bed_id:obj.beds[i].type_status_bed_id, 
                    action: 'action'
                }
                this.form.beds.push(beds)
                // if (this.form.type_status_bed_id == 2) {
                //     btn_bed.classList.add('d-none')
                // }
            }
            this.show = false
        }
    },
    mounted() {
        this.$store.state.totalRows = this.items.length
    },
    created () {
        this.$store.dispatch('room/getAllList', this.params)
        this.$store.dispatch('healthfacility/getParam', 'healthfacility')
        this.$store.dispatch('costing/getParamByTypeCostingId', 7) // harusnya 7 = bed, ni 8 cuman buat cobain
        this.$store.dispatch('healthfacilityitem/getParam', '40f06534-07ec-4221-ad9d-dea08196079f')
        this.$store.dispatch('helper/getParam', 'type_status_bed')
        this.$store.dispatch('helper/getParam', 'type_room')
    }
}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>