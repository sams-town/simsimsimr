<template>
    <div>
        <patientMenu selectedMenu="menu22" />
        <section class="card mt-32">
            <div class="card-header bg-indigo-200">
                <span class="text-bold-18">Data Dasar</span>
            </div>
            <div class="card-body">
                <div class="row" style="font-size: 14px">
                    <div class="col-lg-4">
                        <div class="font-weight-bold">Nama</div>
                        <div>{{ regData.name }} ({{ parsed_age(regData.birth_date) }})</div>
                        <div>{{ regData.room_name }} ({{ regData.room_name }})</div>
                    </div>
                    <div class="col-lg-4">
                        <div class="font-weight-bold">Tanggal Lahir</div>
                        <div>{{ localDate(regData.birth_date) }}</div>
                    </div>
                    <div class="col-lg-4">
                        <div class="font-weight-bold">No RM</div>
                        <div>{{ regData.norm }}</div>
                    </div>
                </div>
            </div>
        </section>
        <section class="mt-32">
            <div class="d-flex justify-content-end">
                <b-button @click="openModalTransfusion" size="sm" variant="primary">
                    <feather-icon icon="PlusIcon" class="mr-1"/> Tambah
                </b-button>
            </div>
        </section>

        <template v-if="transfusion_list && transfusion_list.length > 0">
            <template v-for="(item, index) in transfusion_list" >
                <section class="card mt-32">
                    <div class="card-body shadow-sm">
                        <ModernLabel :label="`No. Kantong: ${item.bag_number} (ke-${item.bag})`" :small="`Added on ${item.date_added} Oleh ${item.employee_nurse_name}`" />
                        <div class="d-flex mt-12">
                            <b-button pill size="sm" variant="primary" class="px-16 mr-2" @click="onEditTransfusion(item)">
                                <feather-icon icon="EditIcon" class="mr-1" /> Edit
                            </b-button>
                            <b-button pill size="sm" variant="danger" class="px-16 mr-2" @click="removeTransfusion(item)">
                                <feather-icon icon="TrashIcon" class="mr-1" /> Hapus
                            </b-button>
                            <b-button @click="openModalItem(item.id)" pill variant="outline-success" size="sm" class="px-16">
                                <feather-icon icon="PlusIcon" class="mr-1" />Monitoring
                            </b-button>
                        </div>

                        <div class="row mt-16">
                            <div class="col-lg-6">
                                <div class="text-uppercase text-bold-14 text-primary-500">Transfusi Darah :</div>
                                <div class="mt-8" style="font-size: 13px;">
                                    <b>Tetes</b> = {{ item.bag_drops ? item.bag_drops : '-' }} tetes/mnt,
                                    <b>Jns Darah</b> = {{ item.blood_group ? item.blood_group : '-' }},
                                    <b>Kecepatan</b> = {{ item.blood_velocity ? item.blood_velocity : '-' }},
                                    <b>Gol Darah</b> = {{ item.blood_type ? item.blood_type : '-' }},
                                    <b>Jumlah CC</b> = {{ item.blood_amount ? item.blood_amount : '-' }} cc,
                                    <b>Kadaluarsa</b> = {{ item.expired_date ? item.expired_date : '-' }},
                                    <b>Tgl/Jam Mulai</b> = {{ item.start_datetime ? item.start_datetime : '-' }}
                                    <b>Tgl/Jam Selesai</b> = {{ item.finish_datetime ? item.finish_datetime : '-' }},
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="text-uppercase text-bold-14 text-primary-500">Stiker Pasien :</div>
                                <div class="mt-8" style="font-size: 13px;">
                                    <span>
                                        <b>Reaksi</b> = 
                                        <template v-if="item.reaction_arry">
                                            <span v-if="item.reaction_arry.includes('a')">Menggigil, </span>
                                            <span v-if="item.reaction_arry.includes('b')">Sakit Kepala, </span>
                                            <span v-if="item.reaction_arry.includes('c')">Kulit Kemerahan, </span>
                                            <span v-if="item.reaction_arry.includes('d')">Urtikaria, </span>
                                            <span v-if="item.reaction_arry.includes('e')">Sesak nafas, </span>
                                            <span v-if="item.reaction_arry.includes('f')">Suhu &gt; 37,8ᵒC, </span>
                                            <span v-if="item.reaction_arry.includes('g')">Lainnya, </span>
                                        </template>
                                        <span v-else>-</span>
                                    </span>
                                    <b>Reaksi lainnya</b> = {{ item.reaction ? item.reaction : '-' }},
                                    <b>Waktu Reaksi</b> = {{ item.reaction_time ? item.reaction_time : '-' }},
                                    <span>
                                        <b>Tranfusi</b> = 
                                        <span v-if="item.transfusion_process_type_id">{{ item.transfusion_process_type_id == '1' ? 'Stop' : 'Dilanjutkan' }}</span>
                                        <span v-else>-</span>, 
                                    </span>
                                    <b>Waktu Tranfusi</b> = {{ item.transfusion_process_time ? item.transfusion_process_time : '-' }},
                                    <b>Intake</b> = {{ item.intake ? item.intake : '-' }} ml,
                                    <b>Output</b> = {{ item.output ? item.output : '-' }} ml,
                                </div>
                            </div>
                        </div>
                        
                        <div v-if="item.blood_transfusion_items && item.blood_transfusion_items.length > 0" class="mt-16 mt-lg-28" style="font-size: 13px">
                            <b-table bordered fixed :fields="transfusionItemFields" :items="item.blood_transfusion_items" style="font-size: 13px;">
                                <template #cell(temperature)="row">{{ row.value }} ᵒC</template>
                                <template #cell(blood_pressure)="row">{{ row.item.systolic }}/{{ row.item.diastolic }} mmHg</template>
                                <template #cell(pulse)="row">{{ row.value }} x/mnt</template>
                                <template #cell(respiratory_rate)="row">{{ row.value }} x/mnt</template>
                                <template #cell(action)="row">
                                    <b-button @click="onEditItem(row.item)" size="sm" variant="warning-500" class="mr-1">
                                        <feather-icon icon="EditIcon" />
                                    </b-button>
                                    <b-button @click="removeItem(row.item)" size="sm" variant="danger-500">
                                        <feather-icon icon="TrashIcon" />
                                    </b-button>
                                </template>
                            </b-table>
                        </div>
                    </div>
                </section>
            </template>
        </template>

        <b-modal v-model="active.modal_main" size="lg" title="Tambah Kantong" no-close-on-backdrop>
            <div>
                <ModernLabel label="Monitoring Tranfusi darah" />
                <div class="row">
                    <div class="col-lg-4 mt-10">
                        <label class="c-form-label">Kantong Ke</label>
                        <b-form-input v-model="form_transfusion.bag" type="number" />
                    </div>
                    <div class="col-lg-4 mt-10">
                        <label class="c-form-label">Tetes</label>
                        <b-input-group append="tetes/mnt">
                            <b-form-input v-model="form_transfusion.bag_drops" type="number" />
                        </b-input-group>
                    </div>
                    <div class="col-lg-4 mt-10">
                        <label class="c-form-label">Jenis Darah</label>
                        <v-select v-model="form_transfusion.blood_group":options="OptionsBloodGroup" :reduce="item => item.value" label="title" placeholder="-- Jenis darah --"></v-select>
                    </div>
                    <div class="col-lg-4 mt-10">
                        <label class="c-form-label">Kecepatan</label>
                        <b-input-group append="ml">
                            <b-form-input v-model="form_transfusion.blood_velocity" type="number" />
                        </b-input-group>
                    </div>
                    <div class="col-lg-4 mt-10">
                        <label class="c-form-label">Golongan Darah</label>
                        <v-select v-model="form_transfusion.blood_type":options="OptionsBloodType" :reduce="item => item.value" label="title" placeholder="-- Jenis darah --"></v-select>
                    </div>
                    <div class="col-lg-4 mt-10">
                        <label class="c-form-label">Jumlah CC</label>
                        <b-input-group append="cc">
                            <b-form-input v-model="form_transfusion.blood_amount" type="number" />
                        </b-input-group>
                    </div>
                    <div class="col-lg-4 mt-10">
                        <label class="c-form-label">Nomor Kantong</label>
                        <b-form-input v-model="form_transfusion.bag_number" type="number" />
                    </div>
                    <div class="col-lg-4 mt-10">
                        <label class="c-form-label">Tanggal Kadaluarsa</label>
                        <b-form-input v-model="form_transfusion.expired_date" type="date" />
                    </div>
                    <div class="col-lg-4 mt-10">
                        <label class="c-form-label">Tgl/Jam Mulai</label>
                        <b-form-input v-model="form_transfusion.start_datetime" type="datetime-local" />
                    </div>
                    <div class="col-lg-4 mt-10">
                        <label class="c-form-label">Tgl/Jam Selesai</label>
                        <b-form-input v-model="form_transfusion.finish_datetime" type="datetime-local" />
                    </div>
                </div>
            </div>
            <div class="mt-24">
                <ModernLabel label="Stiker Pasien" />
                <div class="row">
                    <div class="col-lg-4 mt-10">
                        <label class="c-form-label">Reaksi</label>
                        <b-form-checkbox-group size="sm"v-model="form_transfusion.reaction_arry">
                            <b-form-checkbox value="a" class="col-6 mr-0 my-1">Menggigil</b-form-checkbox>
                            <b-form-checkbox value="b" class="col-6 mr-0 my-1">Sakit Kepala</b-form-checkbox>
                            <b-form-checkbox value="c" class="col-6 mr-0 my-1">Kulit Kemerahan</b-form-checkbox>
                            <b-form-checkbox value="d" class="col-6 mr-0 my-1">Urtikaria</b-form-checkbox>
                            <b-form-checkbox value="e" class="col-6 mr-0 my-1">Sesak nafas</b-form-checkbox>
                            <b-form-checkbox value="f" class="col-6 mr-0 my-1">Suhu &gt; 37,8ᵒC</b-form-checkbox>
                            <b-form-checkbox value="g" class="col-6 mr-0 my-1">Lainnya</b-form-checkbox>
                        </b-form-checkbox-group>
                        <b-form-input v-model="form_transfusion.reaction" class="mt-10" v-if="form_transfusion.reaction_arry.indexOf('g') !== -1" />
                    </div>
                    <div class="col-lg-4 mt-10">
                        <label class="c-form-label">Waktu Reaksi</label>
                        <b-form-input v-model="form_transfusion.reaction_time" type="time" />
                    </div>
                    <div class="col-lg-4 mt-10">
                        <label class="c-form-label">Tranfusi</label>
                        <v-select v-model="form_transfusion.transfusion_process_type_id" :options="OptionsTransfusion" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                    </div>
                    <div class="col-lg-4 mt-10">
                        <label class="c-form-label">Waktu Tranfusi</label>
                        <b-form-input v-model="form_transfusion.transfusion_process_time" type="time" />
                    </div>
                    <div class="col-lg-4 mt-10">
                        <label class="c-form-label">Balance Cairan (Intake)</label>
                        <b-input-group append="ml">
                            <b-form-input v-model="form_transfusion.intake" type="number" />
                        </b-input-group>
                    </div>
                    <div class="col-lg-4 mt-10">
                        <label class="c-form-label">Balance Cairan (Output)</label>
                        <b-input-group append="ml">
                            <b-form-input v-model="form_transfusion.output" type="number" />
                        </b-input-group>
                    </div>
                </div>
            </div>

            <template #modal-footer>
                <div class="d-flex justify-content-end">
                    <b-button @click="validationTransfusion" :disabled="active.disabled_main" variant="primary">Simpan</b-button>
                </div>
            </template>
        </b-modal>

        <b-modal v-model="active.modal_item" size="lg" title="Tambah Kantong" no-close-on-backdrop>
            <div class="row">
                <div class="col-lg-4">
                    <label class="c-form-label">Waktu</label>
                    <b-form-input v-model="form_item.time" type="time" />
                </div>
                <div class="col-lg-4 mt-10 mt-lg-0">
                    <label class="c-form-label">Systolic</label>
                    <b-input-group append="mmHg">
                        <b-form-input type="number" autocomplete="off" v-model="form_item.systolic" />
                    </b-input-group>
                </div>
                <div class="col-lg-4 mt-10 mt-lg-0">
                    <label class="c-form-label">Diastolic</label>
                    <b-input-group append="mmHg">
                        <b-form-input type="number" autocomplete="off" v-model="form_item.diastolic" />
                    </b-input-group>
                </div>
                <div class="col-lg-4 mt-10">
                    <label class="c-form-label">Kesadaran</label>
                    <b-form-input v-model="form_item.consciousness" />
                </div>
                <div class="col-lg-4 mt-10">
                    <label class="c-form-label">Nadi</label>
                    <b-input-group append="x/mnt">
                        <b-form-input type="number" autocomplete="off" v-model="form_item.pulse" />
                    </b-input-group>
                </div>
                <div class="col-lg-4 mt-10">
                    <label class="c-form-label">Pernafasan</label>
                    <b-input-group append="x/mnt">
                        <b-form-input type="number" autocomplete="off" v-model="form_item.respiratory_rate" />
                    </b-input-group>
                </div>
                <div class="col-lg-4 mt-10">
                    <label class="c-form-label">Suhu</label>
                    <b-input-group append="ᵒC">
                        <b-form-input type="number" autocomplete="off" v-model="form_item.temperature" />
                    </b-input-group>
                </div>
                <div class="col-lg-4 mt-10">
                    <label class="c-form-label">Nyeri</label>
                    <b-form-input v-model="form_item.sore" />
                </div>
                <div class="col-lg-4 mt-10">
                    <label class="c-form-label">Kondisi Akses Intravena</label>
                    <b-form-input v-model="form_item.intravenous_access_conditions" />
                </div>
            </div>

            <template #modal-footer>
                <div class="d-flex justify-content-end">
                    <b-button @click="validationItem" :disabled="active.disabled_item" variant="primary">Simpan</b-button>
                </div>
            </template>
        </b-modal>

    </div>
</template>
<script>
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import patientMenu from '@/@core/components/tab-menu/patientMenu.vue';
import ModernLabel from '@/@core/components/label/ModernLabel.vue';
import vSelect from 'vue-select'
import moment from 'moment'

export default {
    name: 'BloodTransfusion',
    components: {
        patientMenu, ModernLabel, vSelect
    },
    data() {
        return {
            userData: JSON.parse(localStorage.getItem('userData')),
            reg_des_id: '',
            regData: {},
            active: {
                modal_main: false,
                disabled_main: false,
                modal_item: false,
                disabled_item: false,
            },
            params: {
                // limit: 0,
                // offset: 20,
                registration_destination_id: ''
            },
            OptionsBloodGroup: [
                { value: 'WB', title: 'WB' },
                { value: 'PRC', title: 'PRC' },
                { value: 'TC', title: 'BTC' },
            ],
            OptionsBloodType: [
                { value: 'A+', title: 'A+' },
                { value: 'A-', title: 'A-' },
                { value: 'B+', title: 'B+' },
                { value: 'B-', title: 'B-' },
                { value: 'AB+', title: 'AB+' },
                { value: 'AB-', title: 'AB-' },
                { value: 'O+', title: 'AB-' },
                { value: 'O-', title: 'AB-' },
            ],
            OptionsTransfusion: [
                { value: 1, title: 'Stop' },
                { value: 2, title: 'Dilanjutkan' },
            ],
            transfusion_list: [],
            form_transfusion: {
                id:'',
                registration_destination_id: '',
                employee_nurse_id: '',
                bag: '',
                bag_drops: '',
                blood_group: '',
                blood_velocity: '',
                blood_type: '',
                blood_amount: '',
                bag_number: '',
                expired_date: '',
                start_datetime: moment(new Date()).format('YYYY-MM-DD HH:mm'),
                finish_datetime: moment(new Date()).format('YYYY-MM-DD HH:mm'),
                reaction_arry: [],
                reaction: '',
                reaction_time: moment(new Date()).format('HH:mm'),
                transfusion_process_type_id: '',
                transfusion_process_time: moment(new Date()).format('HH:mm'),
                intake: '',
                output: ''
            },

            transfusionItemFields: [
                { key: 'time', label: 'Waktu' },
                { key: 'consciousness', label: 'Kesadaran' },
                { key: 'blood_pressure', label: 'Tekanan Darah' },
                { key: 'pulse', label: 'Nadi' },
                { key: 'respiratory_rate', label: 'Pernafasan' },
                { key: 'temperature', label: 'Suhu' },
                { key: 'sore', label: 'Nyeri' },
                { key: 'intravenous_access_conditions', label: 'Kondisi Akses Intravena' },
                { key: 'action', label: 'Aksi', thStyle: 'width: 100px' },
            ],

            form_item: {
                id: '',
                blood_transfusion_id: '',
                time: moment(new Date()).format('HH:mm'),
                consciousness: '',
                blood_pressure: '',
                systolic: '',
                diastolic: '',
                pulse: '',
                respiratory_rate: '',
                temperature: '',
                sore: '',
                intravenous_access_conditions: ''
            }
        }
    },

    watch: {
        "form_transfusion.reaction_arry"(newValues) {
            if (newValues && !newValues.includes("g")) {
                this.form_transfusion.reaction = ""
            }
        },
    },

    methods: {
        openModalTransfusion() {
            this.clearFormTransfusion()
            this.active.modal_main = true
        },

        clearFormTransfusion() {
            this.form_transfusion.id = ''
            this.form_transfusion.registration_destination_id = ''
            this.form_transfusion.employee_nurse_id = ''
            this.form_transfusion.bag = ''
            this.form_transfusion.bag_drops = ''
            this.form_transfusion.blood_group = ''
            this.form_transfusion.blood_velocity = ''
            this.form_transfusion.blood_type = ''
            this.form_transfusion.blood_amount = ''
            this.form_transfusion.bag_number = ''
            this.form_transfusion.expired_date = ''
            this.form_transfusion.start_datetime = moment(new Date()).format('YYYY-MM-DD HH:mm')
            this.form_transfusion.finish_datetime = moment(new Date()).format('YYYY-MM-DD HH:mm')
            this.form_transfusion.reaction_arry = []
            this.form_transfusion.reaction = ''
            this.form_transfusion.reaction_time = moment(new Date()).format('HH:mm')
            this.form_transfusion.transfusion_process_type_id = ''
            this.form_transfusion.transfusion_process_time = moment(new Date()).format('HH:mm')
            this.form_transfusion.intake = ''
            this.form_transfusion.output = ''
        },

        validationTransfusion() {
            // console.log('this.form_transfusion: ', this.form_transfusion)
            if (this.form_transfusion.id == '') {
                this.form_transfusion.registration_destination_id = this.reg_des_id
                this.form_transfusion.employee_nurse_id = this.userData.employee_id
            }

            try {
                this.active.disabled_main = true
                this.$store.dispatch('bloodtransfusion/postPutTransfusionMonitoring', this.form_transfusion)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active.modal_main = false
                        this.active.disabled_main = false
                        this.clearFormTransfusion()
                        this.getTransfusion()
                    } 
                )
                .catch(({error, response}) => {
                    this.notification('Failed','CloseIcon','danger',`${error}`)
                    this.active.disabled_main = false
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
                this.active.disabled_main = false
            }
        },

        getTransfusion() {
            this.params.registration_destination_id = this.reg_des_id
            try {                
                this.$store.dispatch('bloodtransfusion/getListTransfusionMonitoring', this.params).then((response) => {
                    this.transfusion_list = response.rows
                    // this.totalRows = response.iTotal
                }, error => {
                    console.error('Error while GET bloodtransfusion/getListTransfusionMonitoring at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET bloodtransfusion/getListTransfusionMonitoring at: ', error);
            }
        },

        onEditTransfusion(obj) {
            this.openModalTransfusion()
            
            this.form_transfusion.id = obj.id
            this.form_transfusion.registration_destination_id = obj.registration_destination_id
            this.form_transfusion.employee_nurse_id = obj.employee_nurse_id
            this.form_transfusion.bag = obj.bag
            this.form_transfusion.bag_drops = obj.bag_drops
            this.form_transfusion.blood_group = obj.blood_group
            this.form_transfusion.blood_velocity = obj.blood_velocity
            this.form_transfusion.blood_type = obj.blood_type
            this.form_transfusion.blood_amount = obj.blood_amount
            this.form_transfusion.bag_number = obj.bag_number
            this.form_transfusion.expired_date = obj.expired_date
            this.form_transfusion.start_datetime = obj.start_datetime
            this.form_transfusion.finish_datetime = obj.finish_datetime
            this.form_transfusion.reaction_arry = obj.reaction_arry
            this.form_transfusion.reaction = obj.reaction
            this.form_transfusion.reaction_time = obj.reaction_time
            this.form_transfusion.transfusion_process_type_id = obj.transfusion_process_type_id
            this.form_transfusion.transfusion_process_time = obj.transfusion_process_time
            this.form_transfusion.intake = obj.intake
            this.form_transfusion.output = obj.output
        },

        removeTransfusion(obj) {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus monitoring ${obj.employee_nurse_name} pada kantong nomor-${obj.bag_number}?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger', okTitle: 'Ya, Hapus', cancelTitle: 'Batal', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    this.$store.dispatch('bloodtransfusion/deleteTransfusionMonitoring', obj.id)
                    .then(
                        response => {
                            if (response.status === 200 || response.status === 201) {
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.getTransfusion()
                            } else {
                                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                            }
                        },
                        error => {
                            this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                            console.error('Error While Delete Using bloodtransfusion/deleteTransfusionMonitoring at: ', error)
                        }
                    )
                    .catch(({error, response}) => {
                        this.notification('Failed','CloseIcon','danger',`${error}`)
                    })
                }
            })
        },

        openModalItem(transfusion_id) {
            this.clearFormItem()
            this.active.modal_item = true
            this.form_item.blood_transfusion_id = transfusion_id
        },

        clearFormItem() {
            this.form_item.id = ''
            this.form_item.blood_transfusion_id = ''
            this.form_item.time = moment(new Date()).format('HH:mm')
            this.form_item.consciousness = ''
            this.form_item.systolic = ''
            this.form_item.diastolic = ''
            this.form_item.pulse = ''
            this.form_item.respiratory_rate = ''
            this.form_item.temperature = ''
            this.form_item.sore = ''
            this.form_item.intravenous_access_conditions = ''
        },

        validationItem() {
            if (this.form_item.id == '') {
                this.form_item.registration_destination_id = this.reg_des_id
                this.form_item.employee_nurse_id = this.userData.employee_id
            }

            try {
                this.active.disabled_main = true
                this.$store.dispatch('bloodtransfusion/postPutTransfusionItem', this.form_item)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active.modal_item = false
                        this.active.disabled_item = false
                        this.clearFormItem()
                        this.getTransfusion()
                    } 
                )
                .catch(({error, response}) => {
                    this.notification('Failed','CloseIcon','danger',`${error}`)
                    this.active.disabled_item = false
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
                this.active.disabled_item = false
            }
        },

        onEditItem(obj) {
            this.clearFormItem()
            this.active.modal_item = true

            this.form_item.id = obj.id
            this.form_item.blood_transfusion_id = obj.blood_transfusion_id
            this.form_item.time = obj.time
            this.form_item.consciousness = obj.consciousness
            this.form_item.systolic = obj.systolic
            this.form_item.diastolic = obj.diastolic
            this.form_item.pulse = obj.pulse
            this.form_item.respiratory_rate = obj.respiratory_rate
            this.form_item.temperature = obj.temperature
            this.form_item.sore = obj.sore
            this.form_item.intravenous_access_conditions = obj.intravenous_access_conditions
        },

        removeItem(obj) {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus monitoring jam ${obj.time} ?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger', okTitle: 'Ya, Hapus', cancelTitle: 'Batal', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    this.$store.dispatch('bloodtransfusion/deleteTransfusionItem', obj.id)
                    .then(
                        response => {
                            if (response.status === 200 || response.status === 201) {
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.getTransfusion()
                            } else {
                                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                            }
                        },
                        error => {
                            this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                            console.error('Error While Delete Using bloodtransfusion/deleteTransfusionItem at: ', error)
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
                position: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },

        localDate(input_date) {
            if (!input_date || input_date === "0000-00-00") {
                return "";
            }

            const parsed = moment(input_date);
            if (!parsed.isValid()) {
                return "";
            }

            return moment(input_date).locale("id").format('DD MMMM YYYY')
        },

        parsed_age(input_date) {
            if (!input_date || input_date === "0000-00-00") {
                return "";
            }

            const parsed = moment(input_date);
            if (!parsed.isValid()) {
                return "";
            }

            const birthDate = moment(input_date, 'YYYY-MM-DD');
            const currentDate = moment();

            const years = currentDate.diff(birthDate, 'years');
            birthDate.add(years, 'years');
            const months = currentDate.diff(birthDate, 'months');
            birthDate.add(months, 'months');
            const days = currentDate.diff(birthDate, 'days');

            return `${years} th ${months} bln ${days} hr`;
        },

        getData(regDesId) {
			this.$store.dispatch('registration/getById', regDesId).then(data => {
                this.regData = data[0]
            })
        },

    },
   
    created () {
        if (this.$route.params.id) {
            this.reg_des_id = this.$route.params.id
            this.getData(this.$route.params.id)
            this.getTransfusion()
        }
    }
}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>