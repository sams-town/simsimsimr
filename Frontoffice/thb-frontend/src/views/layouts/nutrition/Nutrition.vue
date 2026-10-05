<template>
    <div>
        <!-- <TopNutritionMenu selectedMenu="menu1" /> -->
        <section class="card">
            <div class="card-header text-bold-18 bg-primary-200">Filter Data</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-3">
                        <label class="c-form-label">Nama Pasien</label>
                        <b-form-input v-model="params.patient_name"/>
                    </div>
                    <div class="col-lg-3 mt-10 mt-lg-0">
                        <label class="c-form-label">No RM</label>
                        <b-form-input v-model="params.norm"/>
                    </div>
                    <div class="col-lg-3 mt-10 mt-lg-0">
                        <label class="c-form-label">Tanggal</label>
                        <b-form-input type="date" v-model="params.date" />
                    </div>
                    <div class="col-lg-3 mt-10 mt-lg-0">
                        <label class="c-form-label">Diet</label>
                        <v-select v-model="params.diet_id" :options="optionsDiet" :reduce="item => item.value" label="title" placeholder="-- Diet --" />
                    </div>
                    <div class="col-lg-3 mt-10">
                        <label class="c-form-label">Tipe Ruangan</label>
                        <v-select v-model="params.type_room_id" :options="TypeRoomOptions" :reduce="(TypeRoomOptions) => TypeRoomOptions.value" label="title" placeholder="-- Pilih Tipe Ruangan --"></v-select>
                    </div>

                    <div class="col-lg-3 mt-10">
                        <label class="c-form-label">Ruangan</label>
                        <v-select v-model="params.room_id" :options="OptionsRoom" :reduce="(OptionsRoom) => OptionsRoom.value" label="title" placeholder="-- Ruangan --"></v-select>
                    </div>

                    <div class="col-lg-3 mt-10">
                        <label class="c-form-label">Waktu</label>
                        <v-select v-model="params.time_type_id" :options="optionsTime" :reduce="item => item.value" label="title" placeholder="-- pilih --" />
                    </div>

                </div>
                <div class="d-flex mt-40">
                    <b-button @click="getNutritionList" variant="primary" :disabled="isDisabledFilter" class="mr-2">
                        <feather-icon icon="SearchIcon" /> Filter
                    </b-button>
                    <b-button @click="filterReset" variant="outline-primary" :disabled="isDisabledFilter" class="mr-2">
                        <feather-icon icon="XIcon" /> Clear Filter
                    </b-button>
                    <b-button @click="printAllEticket" :disabled="disabled_print_all" variant="outline-w3green" class="mr-2">
                        <feather-icon icon="PrinterIcon" /> Cetak Semua
                    </b-button>
                </div>
            </div>
        </section>
        <section class="mt-24">
            <div class="d-flex justify-content-between align-items-center">
                <b-form-group label-cols="6" label-align="left" label-size="sm" label-for="sortBySelect" class="text-nowrap mb-md-0">
                    <span class="text-regular-14">Tampilkan</span>
                    <b-form-select v-model="perPage" size="sm" :options="pageOptions" class="mx-1"/>
                    <span class="text-regular-14">Entri</span>
                </b-form-group>
                <!-- <div>
                    <b-button @click="openModal" size="sm" pill variant="primary" class="d-block px-3">
                        <feather-icon icon="PlusIcon" class="mr-1" /> Tambah Order
                    </b-button>
                </div> -->
            </div>
            <b-table fixed responsive bordered head-variant="light" :items="nutrition_list" :fields="nutrition_fields" class="mt-12" style="font-size: 13px;">
                <template #cell(patient)="row">
                    <div>{{ row.item.norm }}</div>
                    <div>{{ row.item.patient_name }}</div>
                </template>
                <template #cell(date)="row">
                    <div>{{ row.value }}, {{ getTimeName(row.item.time_type_id) }}</div>
                </template>
                <template #cell(employee_approved_name)="row">
                    {{ row.value ? row.value : '-' }}
                </template>
                <template #cell(action)="row">
                    <div class="d-flex">
                        <div>
                            <b-button @click="printOneEticket(row.item)" :disabled="disabled_one_print" size="sm" variant="w3green" class="mr-1">
                                <feather-icon icon="PrinterIcon" />
                            </b-button>
                        </div>
                        <template v-if="row.item.status == 0">
                            <div>
                                <b-button @click="approveNutrition(row.item)" size="sm" variant="orange-500" class="mr-1">
                                    <feather-icon icon="CheckIcon" />
                                </b-button>
                            </div>
                            <div>
                                <b-button @click="onEditNutrition(row.item)" size="sm" variant="warning-500" class="mr-1">
                                    <feather-icon icon="EditIcon" />
                                </b-button>
                            </div>
                            <div>
                                <b-button @click="removeNutrition(row.item)" size="sm" variant="danger-500" class="mr-1">
                                    <feather-icon icon="TrashIcon" />
                                </b-button>
                            </div>
                        </template>
                    </div>
                </template>
                <template #cell(status)="row">
                    {{ row.value == 1 ? 'Telah dikirim' : '-' }}
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
                            align="fill" size="sm" class="my-0">
                        </b-pagination>
                    </div>
                </div>
            </div>
        </section>

        <b-modal v-model="active.modal_edit" title="Edit Order" no-close-on-backdrop>
            <div class="row">
                <div class="col-lg-4">
                    <label class="c-form-label">Tanggal</label>
                    <b-form-input type="date" v-model="form_nutrition.date" />
                </div>
                <div class="col-lg-4">
                    <label class="c-form-label">Waktu</label>
                    <v-select v-model="form_nutrition.time_type_id" :options="optionsTime" :reduce="item => item.value" label="title" placeholder="-- pilih --" />
                </div>
                <div class="col-lg-4">
                    <label class="c-form-label">Diet</label>
                    <v-select v-model="form_nutrition.diet_id" :options="optionsDiet" :reduce="item => item.value" label="title" placeholder="-- pilih --" />
                </div>
                <div class="col-12 mt-10">
                    <label class="c-form-label">Catatan</label>
                    <b-form-textarea rows="4" v-model="form_nutrition.notes" autocomplete="off" />
                </div>
            </div>

            <template #modal-footer>
                <div class="d-flex justify-content-end w-100">
                    <b-button @click="validationForm" :disabled="active.disabled_main" variant="primary-500">Simpan</b-button>
                </div>
            </template>
        </b-modal>
    </div>
</template>

<script>
// import TopNutritionMenu from '@/@core/components/top-menu/TopNutritionMenu.vue';
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import axios from 'axios'
import moment from 'moment'
import vSelect from 'vue-select'
import { mapState } from 'vuex'

export default {
    components: { vSelect },
    data() {
        return {
            showFilter: false,
            nutrition_list: [],
            disabled_one_print: false,
            disabled_print_all: false,

            pageOptions: [20, 50, 100],
            currentPage:1,
            perPage: 20,
            firstRows: 1,
            lastRows: 20,
            isDisabledFilter: false,
            totalRows: 0,
            params: {
                offset: 0,
                limit: 20,
                patient_name: '',
                norm: '',
                date: moment(new Date()).format('YYYY-MM-DD'),
                diet_id: '',
                type_room_id: '',
                room_id: '',
                time_type_id: '',
            },

            active: {
                modal_edit: false,
                disabled_main: false,
            },

            nutrition_fields: [
                { key: 'patient', label: 'Pasien' },
                { key: 'room_name', label: 'Ruangan' },
                { key: 'date', label: 'Tanggal Order' },
                { key: 'diet_name', label: 'Diet' },
                { key: 'notes', label: 'Catatan' },
                { key: 'employee_nurse_name', label: 'Diorder oleh' },
                // { key: 'employee_approved_name', label: 'Disetujui oleh' },
                { key: 'action', label: 'Aksi', thStyle: {width: '170px'} },
                { key: 'status', label: 'Status' },
            ],

            optionsTime: [
                { value : 1, title: 'Pagi' },
                { value : 2, title: 'Siang' },
                { value : 3, title: 'Sore' },
            ],
            // selected: []
            form_nutrition: {
                id: '',
                registration_destination_id: '',
                health_facility_item_id: '',
                employee_nurse_id: '',
                room_id: '',
                diet_id: '',
                date: moment(new Date()).format('YYYY-MM-DD'),
                delivery_time: '',
                time_arr: [],
                time_type_id: 0,
                notes: '',
                status: 0,
            },
        }
    },

    computed: {
        ...mapState({
            optionsDiet: state => state.nutrition.optionsDiet,
            TypeRoomOptions: state => state.helper.options.type_room,
            OptionsRoom: state => state.room.options.room,
        })
    },

    watch: {
        currentPage: {
            handler: function(value) {
                this.firstRows = this.perPage*(value-1)+1
                this.lastRows = this.perPage*(value)+1
                this.params.offset = this.perPage*(value-1)
                this.params.limit = this.perPage
                this.getNutritionList()
            }
        },
        perPage: {
            handler: function(value) {
                this.params.limit = this.perPage
                this.getNutritionList()
            }
        },
        'params.type_room_id'(value) {
            this.params.room_id = ''
            if (value) {
                return this.$store.dispatch('room/getParamRoom', value)
            } else {
                this.$store.dispatch('room/getParamRoom', '')
            }
        },
    },

    methods: {
        // onRowSelected(items) {
        //     this.selected = items
        // },
        onEditNutrition(obj) {
            this.resetForm()
            this.active.modal_edit = true

            this.form_nutrition.id = obj?.id
            this.form_nutrition.registration_destination_id = obj?.registration_destination_id
            this.form_nutrition.health_facility_item_id = obj?.health_facility_item_id
            this.form_nutrition.employee_nurse_id = obj?.employee_nurse_id
            this.form_nutrition.room_id = obj?.room_id
            this.form_nutrition.diet_id = obj?.diet_id
            this.form_nutrition.date = obj?.date
            this.form_nutrition.time_type_id = obj?.time_type_id
            this.form_nutrition.notes = obj?.notes
            this.form_nutrition.status = obj?.status
            this.form_nutrition.delivery_time = obj?.delivery_time || ''
        },

        resetForm() {
            this.form_nutrition.id = ''
            this.form_nutrition.registration_destination_id = ''
            this.form_nutrition.health_facility_item_id = ''
            this.form_nutrition.employee_nurse_id = ''
            this.form_nutrition.room_id = ''
            this.form_nutrition.diet_id = ''
            this.form_nutrition.date = moment(new Date()).format('YYYY-MM-DD')
            this.form_nutrition.time_type_id = ''
            this.form_nutrition.time_arr = []
            this.form_nutrition.notes = ''
            this.form_nutrition.status = 0
            this.form_nutrition.delivery_time = ''
        },

        validationForm() {
            try {
                this.active.disabled_main = true
                this.$store.dispatch('nutrition/postPutNutrition', this.form_nutrition)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active.modal_edit = false
                        this.active.modal_edit = false
                        this.resetForm()
                        this.getNutritionList()
                    }
                )
                .catch(({error, response}) => {
                    this.notification('Failed','CloseIcon','danger',`${error}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)
            } finally{
                this.active.disabled_main = false
            }
        },

        removeNutrition(obj) {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus ${obj.patient_name} (${obj.room_name}) tanggal ${obj.date} ${this.getTimeName(obj.time_type_id)}?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger', okTitle: 'Ya, Hapus', cancelTitle: 'Batal', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    this.$store.dispatch('nutrition/deleteNutrition', obj.id)
                    .then(
                        response => {
                            if (response.status === 200 || response.status === 201) {
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.getNutritionList()
                            } else {
                                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                            }
                        },
                        error => {
                            this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                            console.error('Error While Delete Using nutrition/deleteNutritionl at: ', error)
                        }
                    )
                    .catch(({error, response}) => {
                        this.notification('Failed','CloseIcon','danger',`${error}`)
                    })
                }
            })
        },

        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                employee: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },
        getNutritionList() {
            this.isDisabledFilter = true
            try {
                this.$store.dispatch('nutrition/getListNutrition', this.params).then((response) => {
                    this.nutrition_list = response.rows
                    this.totalRows = response.iTotal
                }, error => {
                    console.error('Error while GET nutrition/getListNutrition at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET nutrition/getListNutrition at: ', error);
            } finally {
                this.isDisabledFilter = false
            }
        },

        filterReset () {
            this.params.offset = 0
            this.params.limit = this.perPage
            this.params.patient_name = ''
            this.params.norm = ''
            this.params.date = moment(new Date()).format('YYYY-MM-DD')
            this.params.diet_id = ''
            this.params.type_room_id = ''
            this.params.room_id = ''
            this.params.time_type_id = ''
            this.getNutritionList()
        },

        approveNutrition(obj) {
            let formData = {
                id: obj?.id,
                registration_destination_id: obj?.registration_destination_id,
                health_facility_item_id: obj?.health_facility_item_id,
                employee_nurse_id: obj?.employee_nurse_id,
                room_id: obj?.room_id,
                diet_id: obj?.diet_id,
                date: obj?.date,
                time_type_id: obj?.time_type_id,
                delivery_time: moment(new Date()).format('HH:mm'),
                notes: obj?.notes,
                time_type_id: obj?.time_type_id,
                status: 1
            }
            this.$bvModal
            .msgBoxConfirm(`Apakah anda ingin konfirmasi ${obj.patient_name} (${obj.room_name}) tanggal ${obj.date} ${this.getTimeName(obj.time_type_id)}?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'primary', okTitle: 'Ya, Konfirmasi', cancelTitle: 'Batal', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    try {
                        this.$store.dispatch('nutrition/postPutNutrition', formData)
                        .then(
                            res => {
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.getNutritionList()
                            }
                        )
                        .catch(({error, response}) => {
                            this.notification('Failed','CloseIcon','danger',`${error}`)
                        })
                    } catch (error) {
                        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                        console.error('Error catch at: ', error)
                    }
                }
            })
        },

        getTimeName(time_id) {
            if (time_id == 1) {
                return 'Pagi'
            } else if (time_id == 2) {
                return 'Siang'
            } else if (time_id == 3) {
                return 'Sore'
            } else {
                return '-'
            }
        },

        printOneEticket(obj) {
            this.disabled_one_print = true

            let temp_arry = []
            let print_norm = obj?.norm || '-'
            let print_name = obj?.patient_name || '-'
            let print_birthdate = obj?.birth_date || '-'
            let print_diet = obj?.diet_name || '-'
            let print_time = this.getTimeName(obj?.time_type_id).toUpperCase() || '-'
            let print_hfi = obj?.health_facility_item_name || '-'
            // let print_room = obj?.room_name || '-'
            // let print_date = obj?.date || '-'

            const version = `
                ^XA

                ^CF0,20
                ^FO240,15^FDUNIT GIZI^FS
                ^FO170,38^FDRS Taman Harapan Baru Bekasi^FS

                ^CF0,16
                ^FO124,65^FDJl. Kaliabang Tengah No. 2 RT.004 RW.023, Bekasi, Kota Bekasi^FS
                ^FO0,90^GB1300,3,2^FS

                ^CF0,20
                ^FO150,105^FDETICKET MAKAN PASIEN ${print_time}^FS

                ^CF0,20
                ^FO10,137^FDNo RM^FS
                ^FO140,137^FD:^FS
                ^FO155,137^FD${print_norm}^FS

                ^CF0,20
                ^FO10,164^FDNama^FS
                ^FO140,164^FD:^FS
                ^FO155,164^FD${print_name}^FS

                ^CF0,20
                ^FO10,191^FDTanggal Lahir^FS
                ^FO140,191^FD:^FS
                ^FO155,191^FD${print_birthdate}^FS

                ^CF0,20
                ^FO10,218^FDRuangan^FS
                ^FO140,218^FD:^FS
                ^FO155,218^FD${print_hfi}^FS

                ^CF0,20
                ^FO10,245^FDDiet^FS
                ^FO140,245^FD:^FS
                ^FO155,245^FD${print_diet}^FS

                ^CF0,19
                ^FO450,137^FD[  ] Ahli Gizi^FS
                ^FO450,164^FD[  ] Perawat^FS

                ^CF0,17
                ^FO90,290^FDSebaiknya dikonsumsi dalam 1 jam setelah pemberian^FS

                ^XZ
            `

            temp_arry.push(version)

            const headers = {
                "Accept": "application/pdf",
                "Content-Type": "application/x-www-form-urlencoded",
            }
            axios.post("http://api.labelary.com/v1/printers/8dpmm/labels/2.7559069999999997x1.5748039999999999/", temp_arry, {
                responseType: "arraybuffer",
                headers,
            })
            .then((res) => {
                const blob = new Blob([res.data], { type: "application/pdf" });
                const url = window.URL.createObjectURL(blob);

                window.open(url, "_blank");
                this.disabled_one_print = false
            })
            .catch((error) => {
                console.error("Error:", error);
                this.disabled_one_print = false
            });
        },

        printAllEticket() {
            this.disabled_print_all = true
            let temp_arry = []

            for (let i = 0; i < this.nutrition_list.length; i++) {
                const el = this.nutrition_list[i];

                let print_norm = el?.norm || '-'
                let print_name = el?.patient_name || '-'
                let print_birthdate = el?.birth_date || '-'
                let print_diet = el?.diet_name || '-'
                let print_time = this.getTimeName(el?.time_type_id).toUpperCase() || '-'
                let print_hfi = el?.health_facility_item_name || '-'
                // let print_room = el?.room_name || '-'
                // let print_date = el?.date || '-'

                const version = `
                    ^XA

                    ^CF0,20
                    ^FO240,15^FDUNIT GIZI^FS
                    ^FO170,38^FDRS Taman Harapan Baru Bekasi^FS

                    ^CF0,16
                    ^FO124,65^FDJl. Kaliabang Tengah No. 2 RT.004 RW.023, Bekasi, Kota Bekasi^FS
                    ^FO0,90^GB1300,3,2^FS

                    ^CF0,20
                    ^FO150,105^FDETICKET MAKAN PASIEN ${print_time}^FS

                    ^CF0,20
                    ^FO10,137^FDNo RM^FS
                    ^FO140,137^FD:^FS
                    ^FO155,137^FD${print_norm}^FS

                    ^CF0,20
                    ^FO10,164^FDNama^FS
                    ^FO140,164^FD:^FS
                    ^FO155,164^FD${print_name}^FS

                    ^CF0,20
                    ^FO10,191^FDTanggal Lahir^FS
                    ^FO140,191^FD:^FS
                    ^FO155,191^FD${print_birthdate}^FS

                    ^CF0,20
                    ^FO10,218^FDRuangan^FS
                    ^FO140,218^FD:^FS
                    ^FO155,218^FD${print_hfi}^FS

                    ^CF0,20
                    ^FO10,245^FDDiet^FS
                    ^FO140,245^FD:^FS
                    ^FO155,245^FD${print_diet}^FS

                    ^CF0,19
                    ^FO450,137^FD[  ] Ahli Gizi^FS
                    ^FO450,164^FD[  ] Perawat^FS

                    ^CF0,17
                    ^FO90,290^FDSebaiknya dikonsumsi dalam 1 jam setelah pemberian^FS

                    ^XZ
                `;
                temp_arry.push(version)
            }
            const headers = {
                "Accept": "application/pdf",
                "Content-Type": "application/x-www-form-urlencoded",
            }
            axios.post("http://api.labelary.com/v1/printers/8dpmm/labels/2.7559069999999997x1.5748039999999999/", temp_arry, {
                responseType: "arraybuffer",
                headers,
            })
            .then((res) => {
                console.log(res);
                const blob = new Blob([res.data], { type: "application/pdf" });
                const url = window.URL.createObjectURL(blob);

                window.open(url, "_blank");
                this.disabled_print_all = false
            })
            .catch((error) => {
                console.error("Error:", error);
                this.disabled_print_all = false
            });
        },
    },

    created() {
        this.$store.dispatch('nutrition/getParamDiet')
        this.$store.dispatch('helper/getParam', 'type_room')
        this.getNutritionList()
    },
}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>