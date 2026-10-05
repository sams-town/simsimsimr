<template>
    <div>
        <b-modal v-model="active_act.modal_booking" scrollable size="lg">
            <template #modal-header="{ close }">
                <div class="d-flex justify-content-between w-100">
                    <div class="text-uppercase font-weight-bold fs-13 pr-16">
                        Anda akan order booking penunjang untuk 
                        <span class="text-primary-500">{{ regData.alias_name }} {{ regData.name }} | {{ regData.norm }} | {{localDate(regData.birth_date)}} ({{ parsedAge(regData.birth_date) }})</span>
                    </div>
                    <feather-icon icon="XIcon" @click="close()" class="pointer"/>
                </div>
            </template>

            <div class="col-lg-3 px-0">
                <label class="fs-13 mb-0">Tanggal Pemeriksaan</label>
                <!-- <b-form-input v-model="form_booking.date_request" :disabled="active.disabled_booking_date" size="sm" type="date" /> -->
                <b-form-input v-model="form_booking.date_request" size="sm" type="date" />
            </div>

            <b-card no-body class="mt-10">
                <b-tabs card nav-wrapper-class="bg-primary pt-0 pl-8" active-nav-item-class="bg-orange-400">
                    <b-tab active>
                        <template #title>
                            <span class="fs-14 font-weight-bold text-white">LABORATORIUM</span>
                        </template>
                                                        
                        <section :class="active.booking_view != 'ADD' && active.editAccess.laboratory == false ? 'bg-warning-100 pb-20': ''">
                            <div v-if="active.booking_view != 'ADD' && active.editAccess.laboratory == false" class="p-12 bg-warning mb-16">
                                <div class="d-flex align-items-center">
                                    <feather-icon icon="AlertTriangleIcon" />
                                    <div class="ml-8 fs-13 font-italic">Form penunjang laboratorium tidak dapat diubah karena telah diproses oleh laboratorium</div>
                                </div>
                            </div>

                            <div :class="active.booking_view != 'ADD' && active.editAccess.laboratory == false ? 'mt-12': ''">
                                <div class="w-100">
                                    <div class="fs-14 font-weight-bold">PEMERIKSAAN TERPILIH:</div>
                                    <b-form-checkbox-group size="sm" v-model="form_booking.laboratory_request_items">
                                        <b-form-checkbox
                                        v-for="(item, index) in form_booking.laboratory_request_items"
                                        :key="index"
                                        :value="item"
                                        :disabled="active.booking_view != 'ADD' && active.editAccess.laboratory == false"
                                        >
                                        {{ item.costing_name }}
                                        </b-form-checkbox>
                                    </b-form-checkbox-group>
                                </div>
                                <b-form-input v-model="searchlab" :disabled="active.booking_view != 'ADD' && active.editAccess.laboratory == false" placeholder="Pencarian" size="sm" class="mt-8" />
                                <div class="mt-8 py-2 bg-neutral-100" style="height: 350px; overflow-y: scroll;">
                                    <b-form-checkbox-group size="sm" class="col-lg-12" v-model="form_booking.laboratory_request_items" :disabled="active.booking_view != 'ADD' && active.editAccess.laboratory == false">
                                        <div v-for="(item, index) in filteredListLab" :key="index" :class="index != 0 ? 'mt-10' : 'mt-0'">
                                            <div class="fs-14 font-weight-bold text-uppercase">{{ item.category_name }}</div>
                                            <div class="row mx-0"> 
                                                <b-form-checkbox v-for="(list, packIndex) in item.category_items" v-bind:key="packIndex" :value="{'service_class_id': list.costing_services_class.service_class_id ,'laboratory_package_id':list.id, 'costing_name':list.costing_name}" class="mr-0 col-lg-4">{{list.costing_name }}</b-form-checkbox>
                                            </div>
                                        </div>
                                    </b-form-checkbox-group>
                                </div>
                            </div>
                        </section>
                    </b-tab>

                    <b-tab>
                        <template #title>
                            <span class="fs-14 font-weight-bold text-white">RADIOLOGI</span>
                        </template>
                        <section :class="active.booking_view != 'ADD' && active.editAccess.radiology == false ? 'bg-warning-100 pb-20': ''">
                            <div v-if="active.booking_view != 'ADD' && active.editAccess.radiology == false" class="p-12 bg-warning mb-16">
                                <div class="d-flex align-items-center">
                                    <feather-icon icon="AlertTriangleIcon" />
                                    <div class="ml-3 fs-13 font-italic">Form penunjang radiologi tidak dapat diubah karena telah diproses oleh radiologi</div>
                                </div>
                            </div>
                            <div :class="active.booking_view != 'ADD' && active.editAccess.radiology == false ? 'mt-12': ''">
                                <div class="w-100">
                                    <div class="fs-14 font-weight-bold">PEMERIKSAAN TERPILIH:</div>
                                    <b-form-checkbox-group size="sm" v-model="form_booking.radiology_request_items">
                                        <b-form-checkbox
                                        v-for="(item, index) in form_booking.radiology_request_items"
                                        :key="index"
                                        :value="item"
                                        :disabled="active.booking_view != 'ADD' && active.editAccess.radiology == false"
                                        >
                                        {{ item.costing_name }}
                                        </b-form-checkbox>
                                    </b-form-checkbox-group>
                                </div>
                                <b-form-input type="text" v-model="searchrad" :disabled="active.booking_view != 'ADD' && active.editAccess.radiology == false" placeholder="Pencarian" size="sm" class="mt-8" />
                                <div class="mt-8 py-2 bg-neutral-100" style="height: 350px; overflow-y: scroll;">
                                    <b-form-checkbox-group size="sm" class="col-lg-12" v-model="form_booking.radiology_request_items" :disabled="active.booking_view != 'ADD' && active.editAccess.radiology == false">
                                        <div v-for="(item, index) in filteredListRad" :key="index" :class="index != 0 ? 'mt-10' : 'mt-0'">
                                            <div class="fs-14 font-weight-bold text-uppercase">{{ item.category_name }}</div>
                                            <div class="row mx-0"> 
                                                <b-form-checkbox v-for="(list, packIndex) in item.category_items" v-bind:key="packIndex" :value="{'service_class_id': list.costing_services_class.service_class_id ,'radiology_package_id':list.id, 'costing_name':list.costing_name}" class="mr-0 col-lg-4">{{list.costing_name }}</b-form-checkbox>
                                            </div>
                                        </div>
                                    </b-form-checkbox-group>
                                </div>
                            </div>
                        </section>
                    </b-tab>

                    <b-tab>
                        <template #title>
                            <span class="fs-14 font-weight-bold text-white">FISIOTERAPI</span>
                        </template>
                        <div class="w-100">
                            <div class="fs-14 font-weight-bold">PEMERIKSAAN TERPILIH:</div>
                            <b-form-checkbox-group size="sm" v-model="form_booking.physiotherapy_request_items">
                                <b-form-checkbox
                                v-for="(item, index) in form_booking.physiotherapy_request_items"
                                :key="index"
                                :value="item"
                                >
                                {{ item.costing_name }}
                                </b-form-checkbox>
                            </b-form-checkbox-group>
                        </div>
                        <b-form-input type="text" v-model="searchphy" placeholder="Pencarian" size="sm" class="mt-8" />
                        <div class="mt-8 py-2 bg-neutral-100" style="height: 350px; overflow-y: scroll;">
                            <b-form-checkbox-group size="sm" class="col-lg-12" v-model="form_booking.physiotherapy_request_items">
                                <b-form-checkbox v-for="(ip, index) in filteredListPhy" v-bind:key="index" :value="{'service_class_id': ip.service_class_id,'physiotherapy_package_id':ip.id, 'costing_name': ip.costing_name}" class="mr-0 col-lg-4">{{ip.costing_name }}</b-form-checkbox>
                            </b-form-checkbox-group>
                        </div>
                    </b-tab>
                </b-tabs>
            </b-card>

            <!-- <template #modal-footer v-if="active.editAccess.laboratory == true || active.editAccess.radiology == true"> -->
            <template #modal-footer>
                <div class="d-flex justify-content-end m-0">
                    <b-button :disabled="isDisable" @click="validationBooking()" size="sm" variant="primary" class="px-24">ORDER SEKARANG</b-button>
                </div>
            </template>
        </b-modal>
    </div>
</template>
<script>
// import { mapState } from 'vuex'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import moment from 'moment'
import dateMixin from '@/mixins/dateMixin'

export default {
    mixins: [dateMixin],
    data() {
        return {
            beforeEditDate: null,
            supportPackage: {
                lab_package_list: [],
                rad_package_list: [],                
                phy_package_list: [],                
            },
            active: {
                booking_view: 'ADD',
                editAccess: {
                    laboratory: true,
                    radiology: true,
                    // physiotherapy: true,
                },
                disabled_booking_date: false,
            },
            urlbe:process.env.VUE_APP_IMAGE,
            active_act: {
                modal_booking: false,
            },
            regData: {},
            searchlab: '',
            searchrad: '',
            searchphy: '',
            form_booking: {
                id: '',
                registration_destination_id: '',
                doctor_in_charge_id: '',
                icd10: [],
                date_request: moment(new Date()).format('YYYY-MM-DD'),
                laboratory_request_items: [],
                radiology_request_items: [],
                physiotherapy_request_items: [],
            },
            isDisable: false,

        }
    },

    computed: {
        filteredListLab() {
            return this.supportPackage.lab_package_list.map((lab) => {
                const filteredCategoryItems = lab.category_items.filter((item) =>
                    item.costing_name.toLowerCase().includes(this.searchlab.toLowerCase())
                );

                return {
                    ...lab,
                    category_items: filteredCategoryItems,
                };
            }).filter(lab => lab.category_items.length > 0); 
        },

        filteredListRad() {
            return this.supportPackage?.rad_package_list.map((rad) => {
                const filteredCategoryItems = rad?.category_items?.filter((item) =>
                    item.costing_name.toLowerCase().includes(this.searchrad.toLowerCase())
                );

                return {
                    ...rad,
                    category_items: filteredCategoryItems,
                };
            }).filter(rad => rad?.category_items?.length > 0); 
        },

        filteredListPhy() {
            return this.supportPackage.phy_package_list.filter((phy) => {
                const computedPhyObj = { ...phy}
                return Object.keys(computedPhyObj)
                    .some(key => ('' + computedPhyObj[key]).toLowerCase().includes(this.searchphy.toLowerCase()))
            })
        },

    },

    methods: {
        getAssessment() {
            let message = 'initial dulu'
            this.$emit("refreshAssessment", message);
        },
        cancelTrigger(message) {
            if (message.gate == 'laboratory_gate') {
                this.cancelLaboratoryRequest(message.request, message.norm)
            } else if (message.gate == 'radiology_gate') {
                this.cancelRadiologyRequest(message.request, message.norm)
            }
        },
        cancelRadiologyRequest(obj, norm){
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin membatalkan booking radiologi ${obj.no_request} ?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'primary', okTitle: 'Yes', cancelTitle: 'No', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    this.$store.dispatch('radiology/removeRequest', obj.id)
                    .then(
                        response => {
                            if (response.status === 200 || response.status === 201) {
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.getAssessment()
                                // this.$store.dispatch('soap/getByRM', norm)
                            } else {
                                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                            }
                        },
                        error => {
                            this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                        }
                    ) 
                }
            })
        },
        cancelLaboratoryRequest(obj, norm){
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus ${obj.no_request} ?`, {
                title: 'Please Confirm', size: 'sm', okVariant: 'primary', okTitle: 'Yes', cancelTitle: 'No', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true) {
                    this.$store.dispatch('laboratory/removeRequest', obj.id)
                    .then(
                        response => {
                            if (response.status === 200 || response.status === 201) {
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.getAssessment()
                                // this.$store.dispatch('soap/getByRM', norm)

                            } else {
                                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                            }
                        },
                        error => {
                            this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                        }
                    ) 
                }
            })
        },
        parentTrigger(message) {
            // console.log(message);
            this.openModalBooking(message.view, message.data)
            this.supportPackage.lab_package_list = message.lab_list
            this.supportPackage.rad_package_list = message.rad_list
            this.supportPackage.phy_package_list = message.phy_list

            this.regData = message.regData
            this.form_booking.registration_destination_id = this.regData.registration_destination_id
            this.form_booking.doctor_in_charge_id = this.regData.doctor_in_charge_id
        },

        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                employee: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },

        openModalBooking(view, data){
            this.resetBooking()
            this.active_act.modal_booking = true
            this.active.booking_view = view
            // console.log('data booking: ', data);

            if (this.active.booking_view == 'ADD') {
                this.form_booking.date_request = moment(new Date()).format('YYYY-MM-DD')
                this.form_booking.id = ''
                this.active.editAccess.laboratory = true
                this.active.editAccess.radiology = true
                this.active.disabled_booking_date = false
                
            } else {
                if (data != null) {
                    this.beforeEditDate = moment(data.laboratory_request.date_request).format('YYYY-MM-DD')
                    // console.log('data: ', data);
                    // Necessary fill data
                    if (data.laboratory_request && data.radiology_request && data.physiotherapy_request) {
                        this.form_booking.date_request = moment(data.laboratory_request.date_request).format('YYYY-MM-DD')
                        this.active.disabled_booking_date = true
                    } else if (data.laboratory_request) {
                        this.form_booking.date_request = moment(data.laboratory_request.date_request).format('YYYY-MM-DD')
                        this.active.disabled_booking_date = true
                    } else if (data.radiology_request) {
                        this.form_booking.date_request = moment(data.radiology_request.date_request).format('YYYY-MM-DD')
                        this.active.disabled_booking_date = true
                    } else if (data.physiotherapy_request) {
                        this.form_booking.date_request = moment(data.physiotherapy_request.date_request).format('YYYY-MM-DD')
                        this.active.disabled_booking_date = true
                    } else {
                        this.form_booking.date_request = moment(new Date()).format('YYYY-MM-DD')
                        this.active.disabled_booking_date = false
                    }

                    this.form_booking.id = data.id
                    
                    const init_lab = data?.laboratory_request
                    const first_lab_item = init_lab[0]?.laboratory_request_items
                    if (init_lab?.length > 0 && first_lab_item?.length > 0) {
                        const mappedLab = first_lab_item.map(item => {
                            return {
                                costing_name: item.costing_name,
                                laboratory_package_id: item.laboratory_package_id,
                                service_class_id: item.service_class_id
                            };
                        });                
                        this.form_booking.laboratory_request_items = mappedLab;
                    } else {
                        this.form_booking.laboratory_request_items = []
                    }
                    
                    const init_rad = data.radiology_request
                    const first_rad_item = init_rad[0]?.radiology_request_items
                    if (init_rad?.length > 0 && first_rad_item?.length > 0) {
                        const mappedRad = first_rad_item.map(item => {
                            return {
                                costing_name: item.costing_name,
                                radiology_package_id: item.radiology_package_id,
                                service_class_id: item.service_class_id
                            };
                        });                
                        this.form_booking.radiology_request_items = mappedRad;
                    } else {
                        this.form_booking.radiology_request_items = []
                    }

                    const init_phy = data?.physiotherapy_request || []
                    const first_phy_item = init_phy[0]?.physiotherapy_request_items || []
                    if (init_phy?.length > 0 && first_phy_item?.length > 0) {
                        const mappedRad = first_phy_item.map(item => {
                            return {
                                costing_name: item.costing_name,
                                physiotherapy_package_id: item.physiotherapy_package_id,
                                service_class_id: item.service_class_id
                            };
                        });                
                        this.form_booking.physiotherapy_request_items = mappedRad;
                    } else {
                        this.form_booking.physiotherapy_request_items = []
                    }

                    // handling if request is processed or not
                    if (data.laboratory_request && data.laboratory_request.status) {
                        if (data.laboratory_request.status == 2 || data.laboratory_request.status == 3 ) {
                            this.active.editAccess.laboratory = false
                        } else {
                            this.active.editAccess.laboratory = true
                        }
                    }

                    if (data.radiology_request && data.radiology_request.status) {
                        if (data.radiology_request.status == 2 || data.radiology_request.status == 3 ) {
                            this.active.editAccess.radiology = false
                        } else {
                            this.active.editAccess.radiology = true
                        }
                    }

                }
            }
        },

        validationBooking(){
            if (this.active.booking_view == 'ADD') {
                this.proceedBooking()
            } else if (this.active.booking_view == 'EDIT'){
                if (this.form_booking.date_request != this.beforeEditDate) {
                    this.$bvModal
                        .msgBoxConfirm(`Apakah anda yakin mengganti tanggal dari ${this.beforeEditDate} ke ${this.form_booking.date_request}?`, {
                        title: 'Konfirmasi', size: 'sm', okVariant: 'primary', okTitle: 'Ya, Lanjutkan', cancelTitle: 'Batal', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
                    })
                    .then(value => {
                        if (value === true){
                            this.proceedBooking()
                        }
                    })
                } else {
                    this.proceedBooking()
                }
            }
        },

        async proceedBooking() {
            try {
                this.isDisable = true
                await this.$store.dispatch('soap/postPutFullBook', this.form_booking)
                .then(
                    res => {
                        this.active_act.modal_booking = false
                        this.resetBooking()
                        this.isDisable = false
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.getAssessment()
                        // this.socket.emit('SEND_LAB', []);
                        // this.socket.emit('SEND_RAD', []);
                    } 
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error at: ', error);
            } finally {
                this.isDisable = false
            }

            // this.isDisable = true
            // try {
            //     this.$store.dispatch('soap/postPutFullBook', this.form_booking).then(res => {
            //         this.active_act.modal_booking = false
            //         this.resetBooking()
            //         this.isDisable = false
            //         this.notification('Success','CheckIcon','success','You have successfully')
            //         // this.socket.emit('SEND_LAB', []);
            //         // this.socket.emit('SEND_RAD', []);
            //         this.getAssessment()
            //         // this.$store.dispatch('soap/getByRM', this.regData.norm)
            //     })
                
            // } catch (error) {
            //     this.notification('Failed','CloseIcon','danger','You have unsuccessful')
            //     this.isDisable = false
            //     console.error('ERR at: ', error);
            // }
        },

        resetBooking() {
            this.form_booking.date_request = ''
            this.form_booking.laboratory_request_items = []
            this.form_booking.radiology_request_items = []
            this.form_booking.physiotherapy_request_items = []
        },
    },
}
</script>