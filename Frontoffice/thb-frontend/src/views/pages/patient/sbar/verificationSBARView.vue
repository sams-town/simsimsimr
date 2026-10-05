<template>
    <div>
        <b-modal @close="close()" v-model="active.modal.main" scrollable size="lg" no-close-on-esc no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-15 font-weight-bold text-uppercase text-center w-100">{{ getHeaderTitle(sbardDatum) }} - {{ regData.alias_name }}.{{ regData.name }} - {{ regData.norm }}</div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
                <!-- <div v-if="sbardDatum?.verification_time" class="fs-15 font-weight-bold text-uppercase text-center w-100">SBAR TELAH DIVERIFIKASI</div>
                <div v-else-if="sbardDatum.decline_reason != null"class="fs-15 font-weight-bold text-uppercase text-center w-100">SBAR DITOLAK</div>
                <div v-else class="fs-15 font-weight-bold text-uppercase text-center w-100">VERIFIKASI SBAR</div> -->
            </template>
            <section>
                <div class="row">
                    <div class="col-lg-4">
                        <div class="fs-14 font-weight-bold bg-sBrown py-1 px-8 text-white">SBAR</div>
                        <div class="fs-12 mt-8">SBAR = {{ sbardDatum?.sbar }}</div>
                        <div class="fs-12 mt-8">Advice = {{ sbardDatum?.advice }}</div>
                    </div>

                    <template v-if="sbardDatum?.user_added_group_id == doctor_group_id">                    
                        <div class="col-lg-4 mt-12 mt-lg-0">
                            <div class="fs-14 font-weight-bold bg-sBrown py-1 px-8 text-white">LABORATORIUM</div>
                            <template v-if="sbardDatum?.laboratory_request?.length > 0">
                                <div v-for="(il, i) in sbardDatum.laboratory_request" class="fs-12 mt-8">
                                    <div class="font-weight-bold">{{localDateTime(il.date_request)}}</div>
                                    <li v-for="(list, j) in il.laboratory_request_items">
                                        <template v-if="list.result?.length > 0">
                                            <feather-icon icon="EyeIcon" @click="triggerLabResult({ alias_name: regData.alias_name, name: regData.name, result: list.result, url: list.url,costing_name: list.costing_name, laboratory_request_id: list.laboratory_request_id })" color="blue" v-b-tooltip.hover.left="`Lihat hasil ${list.costing_name}`"/>
                                        </template>
                                        <template v-else>
                                            <img src="./../../../../@core/assets/icons/hourglass.svg" alt="" width="14px" v-b-tooltip.hover.left="`${list.costing_name} telah di order, belum terbit hasil`">
                                        </template>
                                        {{ list.costing_name }} 
                                    </li>
                                </div>
                            </template>
                            <div v-else>-</div>
                        </div>

                        <div class="col-lg-4 mt-12 mt-lg-0">
                            <div class="fs-14 font-weight-bold bg-sBrown py-1 px-8 text-white">RADIOLOGI</div>
                            <template v-if="sbardDatum?.radiology_request?.length > 0">
                                <div v-for="(ir, i) in sbardDatum.radiology_request" class="fs-12 mt-10">
                                    <div class="font-weight-bold">{{localDateTime(ir.date_request)}}</div>
                                    <li v-for="(list, j) in ir.radiology_request_items">
                                        <template v-if="list?.url?.length > 0">
                                            <feather-icon icon="EyeIcon" class="pointer" @click="triggerRadResult({ alias_name: regData.alias_name, name: regData.name, url: list.url, costing_name: list.radiology_package_name })" color="blue" v-b-tooltip.hover.left="`Lihat hasil ${list.radiology_package_name}`"/>
                                        </template>
                                        <template v-else>
                                            <img src="./../../../../@core/assets/icons/hourglass.svg" alt="" class="pointer" width="14px" v-b-tooltip.hover.left="`${list.radiology_package_name} telah di order, belum terbit hasil`">
                                        </template>
                                        {{ list.radiology_package_name }} 
                                    </li>
                                </div>
                            </template>
                            <div v-else>-</div>
                        </div>

                        <div class="col-lg-4 mt-12">
                            <div class="fs-14 font-weight-bold bg-sBrown py-1 px-8 text-white">FISISOTERAPI</div>
                            <template v-if="sbardDatum?.physiotherapy_request?.length > 0">
                                <div v-for="(ir, i) in sbardDatum.physiotherapy_request" class="fs-12 mt-10">
                                    <div class="font-weight-bold">{{localDateTime(ir.date_request)}}</div>
                                    <li v-for="(list, j) in ir.physiotherapy_request_items">{{ list.physiotherapy_package_name }} </li>
                                </div>
                            </template>
                            <div v-else>-</div>
                        </div>

                        <div class="col-lg-4 mt-12">
                            <div class="fs-14 font-weight-bold bg-sBrown py-1 px-8 text-white">RESEP KARDEKS</div>
                            <template v-if="kardex_recipe?.length > 0">
                                <div v-for="(ipre, presc_i) in kardex_recipe" class="mt-8">
                                    <div class="fs-12 font-weight-bold">
                                        <template>
                                            <feather-icon v-if="ipre?.flag == 1" icon="CheckSquareIcon" color="green" v-b-tooltip.hover.left="`Diproses oleh farmasi`"/>
                                            <img v-else-if="ipre?.flag == 0" src="./../../../../@core/assets/icons/hourglass.svg" alt="" width="14px" v-b-tooltip.hover.left="`Berhasil diorder`">
                                        </template>
                                        {{localDateTime(ipre.date_added)}}
                                    </div>
                                    <div class="fs-12" v-for="(list, idx_p) in ipre.form_prescription_parent" :class="idx_p > 0 ? 'mt-12' : ''">
                                        <div :class="getPrescParentVariant(list.id)" class="px-12 py-1">
                                            <div class="font-weight-bold">
                                                {{ getPrescParentName(list.id) }}
                                                <b-badge v-if="list.id != 1 && list?.status" :variant="getKardexItemStatusVariant(list?.status)" class="text-uppercase mr-1">{{ getKardexItemStatusName(list?.status) }}</b-badge>                                                
                                            </div>
                                            <div v-if="list.id != 1" class="fs-12 text-italic" style="font-weight: 400">  
                                                <span>Kemasan: {{ getParentPackageName(list?.package) }}, </span>
                                                <span>
                                                    {{ list?.value | number }}
                                                    TIAP {{ getDynamicOptName(list?.signatura_times_id, signature_time)}} 
                                                    {{ getDynamicOptName(list?.administer_id, catalog_instruction) }}
                                                    <span v-if="category.administer_id == other_administer">: {{ category.desc }}</span>
                                                </span>
                                            </div>
                                        </div>
                                        <div v-if="ipre?.form_prescription" class="mt-1">
                                            <li v-for="unit in ipre.form_prescription" v-if="list.id == unit.prescription_item_type_id">
                                                <b-badge v-if="unit?.status" :variant="getKardexItemStatusVariant(unit?.status)" class="text-uppercase mr-1">{{ getKardexItemStatusName(unit?.status) }}</b-badge>                                                
                                                {{ unit?.pharmacy_catalog_name }}, {{ unit?.dose }} {{ getDynamicOptName(unit?.dose_unit_id, signature_units) }}
                                                <span v-if="unit?.prescription_item_type_id == 1">
                                                    TIAP {{ getDynamicOptName(unit?.signatura_times_id, signature_time)}} 
                                                    {{ getDynamicOptName(unit?.administer_id, catalog_instruction) }}
                                                    <span v-if="unit.administer_id == other_administer">: {{ unit.desc }}</span>
                                                </span>
                                            </li>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <div v-else>-</div>
                        </div>
                        
                        <div class="col-lg-4 mt-12">
                            <div class="fs-14 font-weight-bold bg-sBrown py-1 px-8 text-white">RESEP PULANG</div>
                            <template v-if="home_recipe?.length > 0">
                                <div v-for="(ipre, presc_i) in home_recipe" class="mt-8">
                                    <div class="fs-12 font-weight-bold">
                                        <template>
                                            <feather-icon v-if="ipre?.flag == 1" icon="CheckSquareIcon" color="green" v-b-tooltip.hover.left="`Diproses oleh farmasi`"/>
                                            <img v-else-if="ipre?.flag == 0" src="./../../../../@core/assets/icons/hourglass.svg" alt="" width="14px" v-b-tooltip.hover.left="`Berhasil diorder`">
                                        </template>
                                        {{localDateTime(ipre.date_added)}}
                                    </div>
                                    <div class="fs-12" v-for="(list, idx_p) in ipre.form_prescription_parent" :class="idx_p > 0 ? 'mt-12' : ''">
                                        <div :class="getPrescParentVariant(list.id)" class="px-12 py-1">
                                            <div class="font-weight-bold">{{ getPrescParentName(list.id) }}</div>
                                            <div v-if="list.id != 1" class="fs-12 text-italic" style="font-weight: 400">  
                                                <span>Jumlah: {{ list.value | number}}, </span>
                                                <span>Aturan: {{ list.used }}</span>
                                            </div>
                                        </div>
                                        <div v-if="ipre?.form_prescription" class="mt-1">
                                            <li v-for="unit in ipre.form_prescription" v-if="list.id == unit.prescription_item_type_id">
                                                {{ unit?.pharmacy_catalog_name }}, jml: {{ unit?.qty | number}}
                                                <span v-if="unit?.prescription_item_type_id == 1">, aturan: {{ unit?.used }}</span>
                                            </li>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <div v-else>-</div>
                        </div>

                        <div class="col-12 mt-12">
                            <div class="fs-14 font-weight-bold bg-sBrown py-1 px-8 text-white">VERIFIKASI</div>
                            <div class="row mt-2">
                                <div class="col-lg-6 border-right">
                                    <div class="p-12">
                                        <div class="fs-12 font-weight-bold mb-1">Nama Pelapor</div>
                                        <div class="fs-12 mb-2">{{ sbardDatum?.user_added_name || '-' }} ({{ sbardDatum?.user_added_role }})</div>
        
                                        <div class="fs-12 font-weight-bold mb-1">Waktu Menulis SBAR</div>
                                        <div class="fs-12 mb-2">{{ sbardDatum?.date_added ? localDateTime(sbardDatum.date_added) : '-' }}</div>
        
                                        <div class="fs-12 font-weight-bold mb-1">Tanda Tangan Pelapor</div>
                                        <div class="fs-12">
                                            <img :src="urlbe+sbardDatum?.user_added_ttd" alt="Tanda Tangan" height="80" v-if="sbardDatum?.user_added_ttd"/>
                                            <div v-else>-</div>
                                        </div>
                                    </div>
                                </div>
        
                                <div class="col-lg-6">
                                    <div class="p-12">
                                        <div class="fs-12 font-weight-bold mb-1">Nama Dokter yang Dikonsulkan</div>
                                        <div class="fs-12 mb-2">{{ sbardDatum?.doctor_in_charge_name_sbar || '-' }}</div>
        
                                        <div class="fs-12 font-weight-bold mb-1">
                                            <b-icon
                                            icon="check-circle-fill"
                                            variant="success"
                                            font-scale="1"
                                            v-if="sbardDatum?.verification_time"
                                            ></b-icon>
                                            Waktu Verifikasi
                                        </div>
                                        <div class="fs-12 mb-2">{{ sbardDatum?.verification_time ? localDateTime(sbardDatum?.verification_time) : '-' }}</div>
        
                                        <div class="fs-12 font-weight-bold mb-1">Tanda Tangan Dokter yang Dikonsulkan</div>
                                        <div class="fs-12">
                                            <img :src="urlbe+sbardDatum?.doctor_in_charge_ttd_sbar" alt="Tanda Tangan" height="80" v-if="sbardDatum?.doctor_in_charge_ttd && sbardDatum?.verification_time"/>
                                            <div v-else>-</div>
                                        </div>
                                    </div>
                                </div>
        
                                <div class="col-lg-12">
                                    <div class="p-12">
                                        <div v-if="sbardDatum.decline_reason != null" class="mt-1">
                                            <div class="fs-12 font-weight-bold mb-1">
                                                <b-icon
                                                icon="x-circle-fill"
                                                variant="danger"
                                                font-scale="1"
                                                ></b-icon>
                                                Alasan Penolakan:
                                            </div>
                                            <p class="fs-12 mb-2 text-danger">{{ sbardDatum.decline_reason }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <b-modal
                    id="rejection-reason-modal"
                    v-model="reasonModal"
                    title="Alasan Penolakan"
                    size="md"
                    ok-title="Kirim Penolakan"
                    cancel-title="Batal"
                    ok-variant="danger"
                    @ok="submitRejection"
                >
                    <b-form-group label="Tuliskan alasan penolakan SBAR ini:">
                        <b-form-textarea
                        v-model="rejectionReason"
                        placeholder="Contoh: Data tidak lengkap atau tidak relevan..."
                        rows="4"
                        max-rows="8"
                        ></b-form-textarea>
                    </b-form-group>
                </b-modal>
            </section>
            <template #modal-footer>
                <b-button :disabled="active.disabled" v-show="sbardDatum.verification_time === null && !sbardDatum.decline_reason" size="sm" @click="unverifySBAR" variant="danger" class="m-0 px-24 mr-2" >TOLAK VERIFIKASI</b-button>
                <b-button :disabled="active.disabled" v-show="sbardDatum.verification_time === null && !sbardDatum.decline_reason" size="sm" @click="verifySBAR" variant="w3green" class="m-0 px-24" >VERIFIKASI</b-button>
            </template>
        </b-modal>
        
        <LabResultModalView ref="labModalResult"/>
        <RadResultModalView ref="radModalResult"/>
    </div>
</template>

<script>
import { mapState } from 'vuex'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import dateMixin from "@/mixins/dateMixin";
import optionMixin from '@/mixins/optionMixin';
import LabResultModalView from '@/views/pages/patient/medical_support/LabResultModalView.vue';
import RadResultModalView from '@/views/pages/patient/medical_support/RadResultModalView.vue';
import io from 'socket.io-client';

export default {
    components: { ToastificationContent, LabResultModalView, RadResultModalView },
    mixins: [dateMixin, optionMixin],
    data() {
        return {
            userData: JSON.parse(localStorage.getItem('userData')),
            regData:{},
            active:{
                modal:{
                    main: false
                },
                disabled:false,
            },
            socket: 
                io(process.env.VUE_APP_SOCKET_IO_NOTIF, {
                path: '/socket.io',
                transports: ['websocket', 'polling', 'flashsocket'],
                withCredentials: true}),
            urlbe: process.env.VUE_APP_IMAGE,
            notifData: {},
            sbardDatum: {},
            reasonModal: false,
            rejectionReason: '',
            
            home_recipe: [],
            kardex_recipe: [],
            signature_time: [],
            catalog_instruction: [],
            signature_units: [],
            other_administer: 'ea3c2573-b9f5-428d-ada4-0ce6ea67c72f',
            doctor_group_id: 'efb3fdff-fbe3-4954-a391-3729e544b62a',
        }
    },
    computed: {
        ...mapState({ 
        }),
    },
    filters:{
        number(value) {
            return Number(value)
        },
    },
    methods: {
        getHeaderTitle(obj) {
            if (obj?.verification_time) return  'SBAR TELAH DIVERIFIKASI'
            if (obj?.decline_reason != null) return  'SBAR DITOLAK'
            return 'VERIFIKASI SBAR'
        },

        getData (regDesId) {
            this.$store.dispatch('registration/getById', regDesId).then(data => {
                this.regData = data[0]
                // console.log('regdata', this.regData)
            })
        },

        triggerLabResult(message) {
            this.$refs.labModalResult.seeLaboratoryResult(message);
        },

        triggerRadResult(message) {
            this.$refs.radModalResult.seeRadiologyResult(message);
        },

        async parentTrigger(message) {
            if(message){
                this.notifData = message
            }
            await this.openMainModal()
        },

        async openMainModal() {
            await this.getOptSignTime()
            await this.getOptCatInstruction()
            await this.getSignaturaUnits()

            this.active.modal.main = true
            this.getMySBAR()
        },

        getMySBAR() {
            if(!this.notifData?.data?.sbar_id) return
            let sbar_id = this.notifData?.data?.sbar_id
            try {                
                this.$store.dispatch('sbar/getBySBARId', sbar_id).then((response) => {
                    if (response?.length > 0) {
                        this.sbardDatum = response[0]
                        this.getData(this.sbardDatum.registration_destination_id)
                        
                        this.home_recipe = this.sbardDatum.prescription.filter(item => item?.type_prescription_id == 2) || []
                        this.kardex_recipe = this.sbardDatum.prescription.filter(item => item.type_prescription_id == undefined || item?.type_prescription_id != 2) || []
                    }
                }, error => {
                    console.error('Error while GET sbar/getBySBARId at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET sbar/getBySBARId at: ', error);
            }
        },

        verifySBAR(){
            try {
                this.active.disabled = true
                let sbar_id = this.notifData?.data?.sbar_id
                let payload = {verification_status:1, sbar_id:sbar_id}
                this.$store.dispatch('sbar/verifySBAR', payload)
                .then(
                    res => {
                        // Toggle the isRead status when the item is submitted
                        this.$emit('toggleRead', this.notifData);
                        this.socket.emit('SEND_SBAR', {
                            type: 'REFRESH_DATA',
                            doctor_in_charge_id: this.sbardDatum?.doctor_in_charge_id
                        })
                        this.notification('Success','CheckIcon','success','Berhasil validasi SBAR')
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','Gagal Valdiasi SBAR')
                console.error('Error catch at: ', error)                
            }finally{
                this.active.disabled = false
                this.active.modal.main = false
            }
        },

        unverifySBAR() {
            this.reasonModal = true;
        },

        submitRejection() {
            if (!this.rejectionReason.trim()) {
                this.notification('Gagal', 'AlertTriangleIcon', 'danger', 'Silakan tulis alasan penolakan.');
                return false;
            }

            try {
                this.active.disabled = true;
                const sbar_id = this.notifData?.data?.sbar_id;
                const payload = {
                    sbar_id,
                    decline_reason: this.rejectionReason,
                };
                this.$store.dispatch('sbar/declineSBAR', payload)
                    .then(() => {
                        // Toggle the isRead status when the item is declined
                        this.$emit('toggleRead', this.notifData);
                        this.socket.emit('SEND_SBAR', {
                            type: 'REFRESH_DATA',
                            doctor_in_charge_id: this.sbardDatum?.doctor_in_charge_id
                        })
                        this.notification('Success', 'CheckIcon', 'success', 'SBAR berhasil ditolak');
                    })
                    .catch(({ message, debug }) => {
                        this.notification(`Failed ${debug}`, 'CloseIcon', 'danger', `${message}`);
                    });
            } catch (error) {
                this.notification('Failed', 'CloseIcon', 'danger', 'Terjadi kesalahan saat menolak SBAR');
                console.error('Error in submitRejection:', error);
            } finally {
                this.active.disabled = false;
                this.reasonModal = false;
                this.active.modal.main = false;
                this.rejectionReason = '';
            }
        },

        notification(a, b, c, d) {
            this.$toast({
                component: ToastificationContent,
                position: 'top-right',
                props: { title: a, icon: b, variant: c, text: d }
            })
        },

        close() {
            this.active.modal.main = false
            this.$emit('closeModal', 'closedmodal')
        },

        async getOptSignTime() {
            try {
                await this.$store.dispatch('kardex/getOptSignaturaTime')
                .then(response => {
                    this.signature_time = response
                })
                .catch(({message, debug, response}) => {
                    console.error('Error at: ', response);
                })
            } catch (error) {
                console.error('Error catch at: ', error)
            }
        },

        async getOptCatInstruction() {
            try {
                await this.$store.dispatch('catalog/getOptCatalogInstruction')
                .then(response => {
                    this.catalog_instruction = response
                })
                .catch(({message, debug, response}) => {
                    console.error('Error at: ', response);
                })
            } catch (error) {
                console.error('Error catch at: ', error)
            }
        },

        async getSignaturaUnits() {
            try {                
                await this.$store.dispatch('kardex/getOptUnit', {})
                .then(
                    async response => {
                        this.signature_units = response
                    }
                )
                .catch(({message, debug, response}) => {
                    console.error('Error GET kardex/getOptUnit at: ', response);
                })
            } catch (error) {
                console.error('Error catch while GET kardex/getOptUnit at: ', error);
            }
        },
    },
}
</script>