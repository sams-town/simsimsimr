<template>
    <div>
        <patientMenu selectedMenu="menu12" />
        <div class="label-head my-16">
            <span class="px-16 text-bold-24 text-neutral-700">Ruang / Kamar Perawatan Pasien</span>
        </div>
        <PatientBanner />
        <div class="mt-24">
            <div class="d-flex">
                <b-button @click="openChangeRoom" variant="primary" class="mr-2">Pindah Kamar</b-button>
                <!-- <b-button @click="openEditRoom" variant="warning" class="mr-2">Edit Kamar</b-button>
                <b-button @click="openCheckoutRoom" variant="danger" class="mr-2">Checkout</b-button> -->
            </div>
            <b-table hover fixed responsive bordered :fields="RoomFields" :items="treatment_list" class="mt-24" style="font-size: 13px;">
                <template #cell(no)="row">{{ row.index+1 }}</template>
                <template #cell(doctor)="row">
                    <div>
                        <div>Dokter DPJP : {{ row.item.doctor_in_charge_name ? row.item.doctor_in_charge_name : '-' }}</div>
                        <div>Dokter 2 : {{ row.item.doctor_in_charge_name_2 ? row.item.doctor_in_charge_name_2 : '-' }}</div>
                        <div>Dokter 3 : {{ row.item.doctor_in_charge_name_3 ? row.item.doctor_in_charge_name_3 : '-' }}</div>
                        <div>Dokter 4 : {{ row.item.doctor_in_charge_name_4 ? row.item.doctor_in_charge_name_4 : '-' }}</div>
                        <div>Dokter 5 : {{ row.item.doctor_in_charge_name_5 ? row.item.doctor_in_charge_name_5 : '-' }}</div>
                    </div>
                </template>
                <template #cell(room)="row">
                    <div>
                        <div>Ruangan: {{ row.item.room_name }}</div>
                        <div>Bed: {{ row.item.bed_name }}</div>
                    </div>
                </template>
                <template #cell(status)="row">
                    {{ row.value == 1 ? 'Active' : 'Checkout' }}
                </template>
            </b-table>
        </div>

        <b-modal v-model="active.modal_change" size="lg" title="Pindah Ruangan Perawatan" no-close-on-backdrop>
            <div class="card">
                <div class="card-header font-weight-bold">Dokter</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <label class="c-form-label">Dokter 1 (DPJP)</label>
                            <v-select v-model="form_transfer.doctor_in_charge_id" @input="changeDoctor1" :options="doctor_list" :reduce="doctor_list => doctor_list.value" label="title" placeholder="-- Dokter --" />
                        </div>
                        <div class="col-lg-6 mt-12 mt-lg-0">
                            <label class="c-form-label">Dokter 2</label>
                            <v-select v-model="form_transfer.doctor_in_charge_id_2" @input="changeDoctor2" :options="doctor_list" :reduce="doctor_list => doctor_list.value" label="title" placeholder="-- Dokter --" />
                        </div>
                        <div class="col-lg-6 mt-12">
                            <label class="c-form-label">Dokter 3</label>
                            <v-select v-model="form_transfer.doctor_in_charge_id_3" @input="changeDoctor3" :options="doctor_list" :reduce="doctor_list => doctor_list.value" label="title" placeholder="-- Dokter --" />
                        </div>
                        <div class="col-lg-6 mt-12">
                            <label class="c-form-label">Dokter 4</label>
                            <v-select v-model="form_transfer.doctor_in_charge_id_4" @input="changeDoctor4" :options="doctor_list" :reduce="doctor_list => doctor_list.value" label="title" placeholder="-- Dokter --" />
                        </div>
                        <div class="col-lg-6 mt-12">
                            <label class="c-form-label">Dokter 5</label>
                            <v-select v-model="form_transfer.doctor_in_charge_id_5" @input="changeDoctor5" :options="doctor_list" :reduce="doctor_list => doctor_list.value" label="title" placeholder="-- Dokter --" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mt-20">
                <div class="card-header">Ruangan</div>
                <div class="card-body">
                    <div>
                        <label class="c-form-label">Keterangan Pindah</label>
                        <b-textarea v-model="form_transfer.notes" rows="3" />
                    </div>
                    <div class="row mt-12">
                        <div class="col-lg-4 mt-12">
                            <label class="c-form-label">Tipe Ruangan</label>
                            <v-select v-model="form_transfer.type_room_id" :options="TypeRoomOptions" :reduce="(TypeRoomOptions) => TypeRoomOptions.value" label="title" placeholder="-- Pilih Tipe Ruangan --"></v-select>
                        </div>

                        <div class="col-lg-4 mt-12">
                            <label class="c-form-label">Ruangan</label>
                            <v-select v-model="form_transfer.room_id" :options="OptionsRoom" :reduce="(OptionsRoom) => OptionsRoom.value" label="title" placeholder="-- Ruangan --"></v-select>
                        </div>

                        <div class="col-lg-4 mt-12">
                            <label class="c-form-label">Bed</label>
                            <v-select v-model="form_transfer.bed_id" :options="OptionsBed" :reduce="(OptionsBed) => OptionsBed.value" label="title" placeholder="-- Bed --"></v-select>
                        </div>
                    </div>
                </div>
            </div>
            <template #modal-footer>
                <div class="d-flex justify-content-end">
                    <b-button @click="validationTransfer" :disabled="disabled.change" variant="primary" class="px-24">Pindah</b-button>
                </div>
            </template>  
        </b-modal>

        <!-- <b-modal v-model="active.modal_edit" size="lg" title="Edit Data Ruang Perawatan">
            <div class="card">
                <div class="card-header">Instalasi</div>
                <div class="card-body">
                    <v-select aria-readonly="true" :options="OptionsDummy" :reduce="OptionsDummy => OptionsDummy" label="title" placeholder="-- Pilih --"></v-select>
                </div>
            </div>
            <div class="card mt-20">
                <div class="card-header">Ruangan</div>
                <div class="card-body">
                    <b-textarea rows="3" placeholder="Keterangan pindah" />
                    <div class="row mt-12">
                        <div class="col">
                            <label class="c-form-label">Ruangan</label>
                            <v-select aria-readonly="true" :options="OptionsDummy" :reduce="OptionsDummy => OptionsDummy" label="title" placeholder="-- Pilih --"></v-select>
                        </div>
                        <div class="col">
                            <label class="c-form-label">Kamar</label>
                            <v-select aria-readonly="true" :options="OptionsDummy" :reduce="OptionsDummy => OptionsDummy" label="title" placeholder="-- Pilih --"></v-select>
                        </div>
                        <div class="col">
                            <label class="c-form-label">Bed</label>
                            <v-select aria-readonly="true" :options="OptionsDummy" :reduce="OptionsDummy => OptionsDummy" label="title" placeholder="-- Pilih --"></v-select>
                        </div>
                    </div>
                </div>
            </div>
            <template #modal-footer>
                <div class="d-flex justify-content-end">
                    <b-button variant="warning" class="px-3">Edit</b-button>
                </div>
            </template>
        </b-modal> -->
        <!-- <b-modal v-model="active.modal_checkout" size="lg" title="Checkout Ruang Perawatan">
            <div class="card">
                <div class="card-header">Checkout / Pulangkan Pasien</div>
                <div class="card-body">
                    <div>
                        <label class="c-form-label">Waktu Checkout</label>
                        <b-form-input type="date" />
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Kondisi Pasien Saat Pulang</label>
                        <b-form-radio-group>
                            <b-form-radio class="my-1" value="1">Sembuh</b-form-radio>
                            <b-form-radio class="my-1" value="2">Atas permintaan pasien / keluarga</b-form-radio>
                            <b-form-radio class="my-1" value="3">Dirujuk: fasilitas/rumah sakit</b-form-radio>
                            <b-form-radio class="my-1" value="4">Meninggal</b-form-radio>
                        </b-form-radio-group>
                    </div>
                </div>
            </div>
            <template #modal-footer>
                <div class="d-flex justify-content-end">
                    <b-button variant="danger" class="px-3">Checkout</b-button>
                </div>
            </template>
        </b-modal> -->
    </div>
</template>
<script>
import { mapState } from 'vuex'
import patientMenu from '@/@core/components/tab-menu/patientMenu.vue';
import administration from '@/views/layouts/patient/doctor/administration.vue';
import vSelect from 'vue-select'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import { computed } from 'vue';

export default {
    name: 'DoctorStudies',
    components: {
        patientMenu, administration, vSelect
    },
    data() {
        return {
            active: {
                modal_change: false,
                modal_edit: false,
                modal_checkout: false,
            },
            disabled: {
                change: false,
            },
            RoomFields: [
                { key: 'no', label: 'No', thStyle: 'width: 60px'},
                { key: 'doctor', label: 'Dokter', },
                { key: 'room', label: 'Info Ruang Perawatan', },
                { key: 'notes', label: 'Keterangan Pindah/Edit', },
                { key: 'status', label: 'Status', },
            ],
            userData: JSON.parse(localStorage.getItem('userData')),
            regData: {},
            treatment_list: [],
            OptionsDummy: [
                { value: 1, title: 'option 1' },
                { value: 2, title: 'option 2' },
                { value: 3, title: 'option 3' },
            ],
            form_transfer: {
                version: 'v20241122114',
                registration_destination_id: "",
                bed_id: "",
                doctor_in_charge_id: "",
                doctor_in_charge_id_2: "",
                doctor_in_charge_id_3: "",
                doctor_in_charge_id_4: "",
                doctor_in_charge_id_5: "",
                notes: "",

                type_room_id: '',
                room_id: '',
            },
            doctor_list: []
        }
    },
    computed: {
        ...mapState({ 
            OptionsSpecialistDoctor: state => state.employee.options.specialist,                   
            TypeRoomOptions: state => state.helper.options.type_room,
            OptionsRoom: state => state.room.options.room,
            OptionsBed: state => state.room.options.bed,
        })
    },
    watch: {
        'form_transfer.type_room_id'(value) {
            this.form_transfer.room_id = ''
            this.form_transfer.bed_id = ''
            if (value) {
                return this.$store.dispatch('room/getParamRoom', value)
            } else {
                this.$store.dispatch('room/getParamRoom', '')
                this.$store.dispatch('room/getParamBed', '')
            }
        },
        'form_transfer.room_id'(value) {
            this.form_transfer.bed_id = ''
            if (value) {
                return this.$store.dispatch('room/getParamBed', value)
            } else {
                this.$store.dispatch('room/getParamBed', '')
            }
        },
    },
    methods: {
        refresh () {
            router.go(router.currentRoute)
        },
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                employee: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },
        getData (reg_des_id) {
			this.$store.dispatch('registration/getById', reg_des_id).then(data => {
                this.regData = data[0]
                // console.log(this.regData);

                this.form_transfer.registration_destination_id = this.regData.registration_destination_id
                this.form_transfer.doctor_in_charge_id = this.regData.doctor_in_charge_id
            })

            this.$store.dispatch('employee/getDoctorOptions', '').then((response) => {
                this.doctor_list = response
            })
        },

        getTreatmentList (reg_des_id) {
            try {
                this.$store.dispatch('treatment/getTreatmentList', { registration_destination_id: reg_des_id }).then(data => {
                    this.treatment_list = data.rows
                    // console.log('this.treatment_list: ', this.treatment_list);
                })
                
            } catch (error) {
                console.log('Error while GET DATA at: ', error);
            }
        },

        changeDoctor1() {
            const { doctor_in_charge_id, doctor_in_charge_id_2, doctor_in_charge_id_3, doctor_in_charge_id_4, doctor_in_charge_id_5 } = this.form_transfer;
            
            if (
                doctor_in_charge_id &&
                (doctor_in_charge_id === doctor_in_charge_id_2 || 
                doctor_in_charge_id === doctor_in_charge_id_3 || 
                doctor_in_charge_id === doctor_in_charge_id_4 || 
                doctor_in_charge_id === doctor_in_charge_id_5)
            ) {
                this.notification('Warning', 'CloseIcon', 'warning', 'Tidak dapat memilih dokter yang sama');
                this.form_transfer.doctor_in_charge_id= '';
            }
        },

        changeDoctor2() {
            const { doctor_in_charge_id, doctor_in_charge_id_2, doctor_in_charge_id_3, doctor_in_charge_id_4, doctor_in_charge_id_5 } = this.form_transfer;
            
            if (
                doctor_in_charge_id_2 &&
                (doctor_in_charge_id_2 === doctor_in_charge_id || 
                doctor_in_charge_id_2 === doctor_in_charge_id_3 || 
                doctor_in_charge_id_2 === doctor_in_charge_id_4 || 
                doctor_in_charge_id_2 === doctor_in_charge_id_5)
            ) {
                this.notification('Warning', 'CloseIcon', 'warning', 'Tidak dapat memilih dokter yang sama');
                this.form_transfer.doctor_in_charge_id_2 = '';
            }
        },

        changeDoctor3() {
            const { doctor_in_charge_id, doctor_in_charge_id_2, doctor_in_charge_id_3, doctor_in_charge_id_4, doctor_in_charge_id_5 } = this.form_transfer;
            
            if (
                doctor_in_charge_id_3 &&
                (doctor_in_charge_id_3 === doctor_in_charge_id || 
                doctor_in_charge_id_3 === doctor_in_charge_id_2 || 
                doctor_in_charge_id_3 === doctor_in_charge_id_4 || 
                doctor_in_charge_id_3 === doctor_in_charge_id_5)
            ) {
                this.notification('Warning', 'CloseIcon', 'warning', 'Tidak dapat memilih dokter yang sama');
                this.form_transfer.doctor_in_charge_id_3 = '';
            }
        },

        changeDoctor4() {
            const { doctor_in_charge_id, doctor_in_charge_id_2, doctor_in_charge_id_3, doctor_in_charge_id_4, doctor_in_charge_id_5 } = this.form_transfer;
            
            if (
                doctor_in_charge_id_4 &&
                (doctor_in_charge_id_4 === doctor_in_charge_id || 
                doctor_in_charge_id_4 === doctor_in_charge_id_2 || 
                doctor_in_charge_id_4 === doctor_in_charge_id_3 || 
                doctor_in_charge_id_4 === doctor_in_charge_id_5)
            ) {
                this.notification('Warning', 'CloseIcon', 'warning', 'Tidak dapat memilih dokter yang sama');
                this.form_transfer.doctor_in_charge_id_4 = '';
            }
        },

        changeDoctor5() {
            const { doctor_in_charge_id, doctor_in_charge_id_2, doctor_in_charge_id_3, doctor_in_charge_id_4, doctor_in_charge_id_5 } = this.form_transfer;
            
            if (
                doctor_in_charge_id_5 &&
                (doctor_in_charge_id_5 === doctor_in_charge_id || 
                doctor_in_charge_id_5 === doctor_in_charge_id_2 || 
                doctor_in_charge_id_5 === doctor_in_charge_id_3 || 
                doctor_in_charge_id_5 === doctor_in_charge_id_4)
            ) {
                this.notification('Warning', 'CloseIcon', 'warning', 'Tidak dapat memilih dokter yang sama');
                this.form_transfer.doctor_in_charge_id_5 = '';
            }
        },

        openChangeRoom(){
            this.active.modal_change = true
        },
        openEditRoom(){
            this.active.modal_edit = true
        },
        openCheckoutRoom(){
            this.active.modal_checkout = true
        },

        validationTransfer(){
            // console.log(this.form_transfer);
            this.disabled.change = true

            try {
                this.$store.dispatch('treatment/postTransfer', this.form_transfer).then((response) => {
                    this.notification('Success','CheckIcon','success','You have successfully')
                    this.active.modal_change = false
                    this.getTreatmentList(this.reg_des_id)
                    this.resetChangeForm()
                    this.disabled.change = false
                })
                
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                this.disabled.change = false
                console.log('Error at: ', error);
            }
        }
    },
    resetChangeForm() {
        // this.form_transfer.registration_destination_id = ""
        // this.form_transfer.doctor_in_charge_id = ""
        this.form_transfer.doctor_in_charge_id_2 = ""
        this.form_transfer.doctor_in_charge_id_3 = ""
        this.form_transfer.doctor_in_charge_id_4 = ""
        this.form_transfer.doctor_in_charge_id_5 = ""
        this.form_transfer.notes = ""
        this.form_transfer.bed_id = ""

        this.form_transfer.type_room_id = ''
        this.form_transfer.room_id = ''
    },
    created () {
        this.$store.dispatch('employee/getParamAllSpecialist')
        this.$store.dispatch('helper/getParam', 'type_room')

        if (this.$route.params.id) {
            this.reg_des_id = this.$route.params.id
            this.getData(this.$route.params.id)
            this.getTreatmentList(this.$route.params.id)
        }
    },
}
</script>
<style scoped>
@import '../../../../@core/css/utility.css';

.label-head::before {
    content: '';
    border-left: 6px solid #648DE1;
}

.c-img {
    aspect-ratio: 1/1;
    object-fit: cover;
    max-width: 150px;
    max-height: 150px;
    border: 10px solid white;
}
</style>