<template>
    <div>
        <b-modal v-model="active.modal_main" no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ORDER BHP
                    <span class="text-primary">{{ regData.alias_name }}. {{ regData.name }} | {{localDate(regData.birth_date)}} ({{ parsedAge(regData.birth_date) }}) | {{ regData.norm }}</span>
                </div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>
            <section>
                <template>
                <!-- <template v-if="intensiveCriteria()"> -->
                    <div class="row mb-8">
                        <div class="col-lg-4">
                            <label class="fs-13 mb-0">Tujuan Order</label>
                            <v-select v-model="form_bhp.pharmacy_depo_id" :options="optDepo" :reduce="item => item.value" label="name" class="select-size-sm" placeholder="depo" />
                        </div>
                    </div>
                </template>

                <div>
                    <label class="fs-13 mb-0">Tambah Katalog</label>
                    <v-select v-model="inputCatalog" @input="addBhpItem($event)" :options="OptionsCatalog" :reduce="item => item" label="title" placeholder="pilih katalog" class="select-size-sm" />
                </div>

                <template v-if="form_bhp.form_prescription?.length > 0">
                    <b-table bordered fixed small :items="form_bhp.form_prescription" :fields="bhpFields" thead-tr-class="fs-13" tbody-tr-class="fs-12" class="mt-12 text-center">
                        <template #cell(action)="row">
                            <b-button @click="removeItem(row.index)" variant="danger-600" size="sm">
                                <feather-icon icon="TrashIcon" />
                            </b-button>
                        </template>
                        <template #cell(name)="row">
                            <div class="text-left">{{ row.value }}</div>
                        </template>
                        <template #cell(value_package)="row">
                            <b-form-input type="number" v-model="row.item.value_package" min="1" size="sm" />
                        </template>
                        <template #cell(package_id)="row">
                            <v-select v-model="row.item.package_id" :options="OptionsPackage" :reduce="item => item.value" label="title" placeholder="kemasan" class="select-size-sm" />
                        </template>
                    </b-table>
                </template>
            </section>
            <template #modal-footer>
                <div class="d-flex justify-content-end w-100 m-0">
                    <b-button @click="validationBhp" :disabled="active.disabled_main" size="sm" variant="primary" class="px-16">SIMPAN</b-button>
                </div>
            </template>
        </b-modal>
    </div>
</template>
<script>
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import dateMixin from "@/mixins/dateMixin";
import { mapState } from 'vuex'

export default {
    mixins: [dateMixin],
    data() {
        return {
            hfi_inpatient: '40f06534-07ec-4221-ad9d-dea08196079f',
            hfi_outpatient: '3cb626f5-9148-4fb1-8663-3d7f028b01a6',
            hfi_emergency: '444b3134-5b75-4b67-af72-a558d46716d7',

            hfii_hcu: '1843843c-af62-4452-b19f-beddd1acd0ec',
            hfii_picu: 'e66e6ed4-4ef4-4375-a6d0-0445e3ec3039',
            hfii_iccu: '7f2a9f80-c30d-4cbd-9f0d-364cda2ec84c',
            hfii_icu: '6c7d7e53-ff24-4246-85b2-07ea77ac801f',
            hfii_icu_isolation: 'ff1f252e-daab-4bdb-b308-e9f4cb11a47d',
            hfii_nicu: '5515af72-6dcb-415d-89e4-2433e7b0149d',
            hfii_nicu_isolation: 'ed40f235-1c7b-4954-aebf-b3dc70a674b7',

            depo_emergency: '4105d8bc-5769-463e-bc67-aae2fc5adf10',
            depo_central: '297c44f0-12ef-40e6-b9a7-c831065b3372',
            depo_ok: '822d71e8-7a2b-4fc6-a372-40a6b458d8c3',
            optDepo: [],
            
            regData: {},
            OptionsCatalog: [],
            OptionsPackage: [],
            form_bhp: {
                id: '',
                soap_id: '',
                pharmacy_depo_id: '',
                soap_p_id: '',
                registration_destination_id: '',
                doctor_in_charge_id: '',
                form_prescription_parent: [{
                    id: 1,
                    prescription: 'OBAT SATUAN',
                    used: '',
                    value: 0,
                    package: null
                }],
                form_prescription: [],

                // ????
                type_prescription_id: 3,
                norm: '',
                health_facility_item_id: '',
            },
            active: {
                modal_main: false,
                disabled_main: false,
            },
            inputCatalog: '',
            bhpFields: [
                { key: 'action', label: 'Aksi', thStyle: {width: '50px'} },
                { key: 'name', label: 'BHP', thStyle: {width: '400px', textAlign: 'left'} },
                { key: 'value_package', label: 'Jumlah' },
                { key: 'package_id', label: 'Kamasan' },
            ]
        }
    },

    // computed: {
    //     ...mapState({ 
    //         optDepo: state => state.frontoptions.general.bhp.depo,
    //     }),
    // },

    methods: {
        getDepoBHP(hfi) {
            try {                
                this.$store.dispatch('depo/getDepoBHP',hfi)
                .then(
                    response => {
                        this.form_bhp.pharmacy_depo_id = {id:response[0].id,name:response[0].name} 
                        this.optDepo = response
                    }
                )
                .catch(({message, debug, response}) => {
                    console.error('Error GET depo/getDepoBHP at: ', response);
                })
            } catch (error) {
                console.error('Error catch while GET depo/getDepoBHP at: ', error);
            }
        },

        intensiveCriteria() {
            return false
            // if(
            //     this.regData.health_facility_id == this.hfi_inpatient && (
            //         this.regData.health_facility_item_id == this.hfii_hcu 
            //         || this.regData.health_facility_item_id == this.hfii_picu 
            //         || this.regData.health_facility_item_id == this.hfii_iccu
            //         || this.regData.health_facility_item_id == this.hfii_icu
            //         || this.regData.health_facility_item_id == this.hfii_icu_isolation
            //         || this.regData.health_facility_item_id == this.hfii_nicu
            //         || this.regData.health_facility_item_id == this.hfii_nicu_isolation
            //     )) return true
            // return false
        },

        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                employee: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },

        parentTrigger(message) {
            this.regData = message.regData
            this.OptionsCatalog = message.optCatalog 
            this.OptionsPackage = message.optPackage 
            // console.log('message', message);
            
            if (message.gate == 'add_gate') {
                // this.form_bhp.pharmacy_depo_id = this.regData.health_facility_id == this.hfi_emergency ? this.depo_emergency : this.depo_central
                // this.form_bhp.pharmacy_depo_id = this.getDepoByCriteria()
                this.getDepoBHP(this.regData.health_facility_item_id)
                this.openMainModal()
            } else if (message.gate == 'edit_gate') {
                this.onEditBhp(message.bhpData)
            }
        }, 

        getDepoByCriteria() {
            if (this.intensiveCriteria() == true) {
                return this.depo_ok
            } else if (this.regData.health_facility_id == this.hfi_emergency) {
                return this.depo_emergency
            } else {
                return this.depo_central
            }
        },

        openMainModal() {
            this.resetForm()
            this.active.modal_main = true
        },

        addBhpItem(event) {
            this.inputCatalog = ''
            this.form_bhp.form_prescription.push({ 
                pharmacy_catalog_id: event.data_catalog.id, 
                prescription_type_id: 1,
                package_id: event.data_catalog.package_id,
                package_name: event.data_catalog.package_name,
                dose: event.data_catalog.dose,
                sum_dose: `${event.data_catalog.dose} ${event.data_catalog.dose_name}`, 
                value_package: 1,
                sort: this.form_bhp.form_prescription.length, 
                used: '',
                desc: '',

                code: event.data_catalog.code,
                name: event.data_catalog.name,
                value_doctor: 0,
                prescription_type_name: 'BHP SATUAN',
            })
        },

        removeItem(index){
            this.form_bhp.form_prescription.splice(index, 1)
        },

        onEditBhp(obj) {
            console.log(obj);
            this.openMainModal()

            this.form_bhp.id = obj?.id || ''
            this.form_bhp.soap_id = obj?.soap_id || ''
            this.form_bhp.pharmacy_depo_id = obj?.pharmacy_depo_id || this.depo_central
            this.form_bhp.soap_p_id = obj?.soap_p_id || ''
            this.form_bhp.registration_destination_id = obj?.registration_destination_id || ''
            this.form_bhp.doctor_in_charge_id = obj?.doctor_in_charge_id || ''

            if (obj?.form_prescription_parent) {
                const mappedPrescParent = obj.form_prescription_parent.map(item => {
                    return {
                        id: item.id,
                        used: item.used,
                        value: item.value,
                        prescription: 'BHP SATUAN',
                        package: item.package
                    };
                });                
                this.form_bhp.form_prescription_parent = mappedPrescParent;
            } else {
                this.form_bhp.form_prescription_parent = []
            }

            if (obj.form_prescription) {
                const mappedPresc = obj.form_prescription.map(item => {
                    return {
                        pharmacy_catalog_id: item.pharmacy_catalog_id, 
                        prescription_type_id: item.prescription_item_type_id,
                        package_id: item.package_id,
                        package_name: item.package_name,
                        dose: item.dose,
                        sum_dose: `${item.dose} ${item.dose_name}`, 
                        value_package: item.qty,
                        sort: item.sort, 
                        used: item.used,
                        desc: item.desc,

                        code: item.pharmacy_catalog_code,
                        name: item.pharmacy_catalog_name,
                        value_doctor: item.qty_doctor,
                        prescription_type_name: 'BHP SATUAN',
                    };
                });      
                this.form_bhp.form_prescription = mappedPresc;                                  
            } else {
                this.form_bhp.form_prescription = [];                                  
            }

            this.form_bhp.type_prescription_id = obj?.type_prescription_id || 3
            this.form_bhp.norm = obj?.norm || ''
            this.form_bhp.health_facility_item_id = obj?.health_facility_item_id || ''
        },

        async validationBhp() {
            this.form_bhp.norm ||= this.regData.norm
            this.form_bhp.health_facility_item_id ||= this.regData.health_facility_item_id
            this.form_bhp.registration_destination_id ||= this.regData.registration_destination_id
            this.form_bhp.doctor_in_charge_id ||= this.regData.doctor_in_charge_id
            this.form_bhp.pharmacy_depo_id ||= this.getDepoByCriteria()

            let inp_presc = this.form_bhp?.form_prescription 
            let item_null_val = []

            if (inp_presc?.length > 0) {
                for (let idx_i = 0; idx_i < inp_presc.length; idx_i++) {
                    if (inp_presc[idx_i].value_package == "" || inp_presc[idx_i].value_package <= 0) {
                        item_null_val.push(inp_presc[idx_i].name)
                    }
                }

                if (item_null_val.length > 0) return alert(`Jumlah obat pada katalog ${item_null_val} minimal diisi 1`)

                try {
                    this.active.disabled_main = true
                    await this.$store.dispatch('soap/postPutPrescription', this.form_bhp)
                    .then(
                        async res => {
                            this.notification('Success','CheckIcon','success','You have successfully')
                            this.active.modal_main = false
                            this.resetForm()
                            this.triggerToParent()
                        }
                    )
                    .catch(({message, debug, response}) => {
                        this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                    })
                } catch (error) {
                    this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                    console.error('Error catch at: ', error)                
                } finally {
                    this.active.disabled_main = false
                }  
            }
        },

        resetForm() {
            this.form_bhp.id = ''
            this.form_bhp.soap_id = ''
            this.form_bhp.soap_p_id = ''
            this.form_bhp.registration_destination_id = ''
            this.form_bhp.doctor_in_charge_id = ''
            this.form_bhp.form_prescription = []
            this.form_bhp.norm = ''
            this.form_bhp.health_facility_item_id = ''
        },

        triggerToParent() {
            const data = { message: "barangkali tar mau kirim data" };
            this.$emit("childBhpTrigger", data);
        },
    },
}
</script>