<template>
    <section class="card mt-16 shadow-sm">
        <div class="card-header p-12 bg-white">
            <ModernLabel variant="brown" :label="getSoapLabel(result)" :small="`Ditambahkan pada ${localFullDay(result?.date_added)} Oleh ${result?.user_added_name}`" />
            <section class="mt-8">
                <b-button v-b-toggle="'accordion-'+result?.id" size="sm" variant="sBrown" class="mr-1">
                    <feather-icon icon="EyeIcon" size="12" class="mr-1" /> 
                    <span class="fs-12">LIHAT SBAR DISINI</span>
                </b-button>
                <template v-if="userData.user_group.id == jkn_group_id || userData.user_group.id == admin_group_id">
                    <router-link :to="`/patient/sbar/print/${result.id}`" target="_blank">
                        <b-button size="sm" variant="outline-success-500" class="mr-1" v-b-tooltip.hover.top="'Cetak SBAR'">
                            <feather-icon icon="PrinterIcon" size="12" class="mr-1"/>
                            <span class="fs-12">SBAR</span>                                                 
                        </b-button>
                    </router-link>
                </template>
            </section>
        </div>

        <b-collapse :id="`accordion-`+result?.id" :visible="visible" >
            <div class="card-body py-12">
                <div class="row">
                    <div class="col-lg-4 px-8 fs-12">
                        <div class="fs-14 font-weight-bold bg-sBrown py-1 px-8 text-white">SBAR</div>
                        <section class="mt-8">
                            <div class="font-weight-bold text-sBrown">SBAR:</div>
                            <div v-html="parsedBody(result?.sbar || '-')"></div>                                                
                        </section>
                        <section class="mt-8">
                            <div class="font-weight-bold text-sBrown">ADVICE:</div>
                            <div v-html="parsedBody(result?.advice || '-')"></div>                                                
                        </section>
                    </div>

                    <template v-if="result?.user_added_group_id == doctor_group_id || result?.user_added_group_id == admin_group_id">
                        <div class="col-lg-4 px-8 fs-12">
                            <div class="fs-14 font-weight-bold bg-sBrown py-1 px-8 text-white">LABORATORIUM</div>
                            <div class="mt-8">
                                <template v-if="result?.laboratory_request?.length > 0">
                                    <div v-for="(il, i) in result.laboratory_request" class="mb-12">
                                        <div class="font-weight-bold">{{localDateTime(il.date_request)}}</div>
                                        <li v-for="(list, j) in il.laboratory_request_items">
                                            <template v-if="list.result?.length > 0">
                                                <feather-icon icon="EyeIcon" @click="triggerLabResult({ alias_name: patient.alias_name, name: patient.name, result: list.result, costing_name: list.laboratory_package_name, laboratory_request_id: list.laboratory_request_id })" color="blue" v-b-tooltip.hover.left="`Lihat hasil ${list.laboratory_package_name}`"/>
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
                        </div>

                        <div class="col-lg-4 px-8 fs-12">
                            <div class="fs-14 font-weight-bold bg-sBrown py-1 px-8 text-white">RADIOLOGI</div>
                            <div class="mt-8">
                                <template v-if="result?.radiology_request?.length > 0">
                                    <template v-for="(ir, i) in result.radiology_request">
                                        <div class="mb-12">
                                            <div class="font-weight-bold">{{localDateTime(ir.date_request)}}</div>
                                            <li v-for="(list, j) in ir.radiology_request_items">
                                                <template v-if="list.url.length > 0">
                                                    <feather-icon icon="EyeIcon" @click="triggerRadResult({ alias_name: patient.alias_name, name: patient.name, url: list.url, costing_name: list.radiology_package_name })" color="blue" v-b-tooltip.hover.left="`Lihat hasil ${list.radiology_package_name}`"/>
                                                </template>
                                                <template v-else>
                                                    <img src="./../../../../@core/assets/icons/hourglass.svg" alt="" width="14px" v-b-tooltip.hover.left="`${list.radiology_package_name} telah di order, belum terbit hasil`">
                                                </template>
                                                {{ list.radiology_package_name }} 
                                            </li>
                                        </div>
                                    </template>
                                </template>
                                <div v-else>-</div>
                            </div>
                        </div>

                        <div class="col-lg-4 px-8 mt-12 fs-12">
                            <div class="fs-14 font-weight-bold bg-sBrown py-1 px-8 text-white">FISIOTERAPI</div>
                            <div class="mt-8">
                                <template v-if="result?.physiotherapy_request?.length > 0">
                                    <template v-for="(phy, i) in result.physiotherapy_request">
                                        <div class="mb-12">
                                            <div class="font-weight-bold">{{localDateTime(phy.date_request)}}</div>
                                            <li v-for="(list, j) in phy.physiotherapy_request_items">
                                                {{ list.physiotherapy_package_name }} 
                                            </li>
                                        </div>
                                    </template>
                                </template>
                                <div v-else class="font-weight-bold">-</div>
                            </div>
                        </div>

                        <div class="col-lg-8 px-8 mt-12 fs-12">
                            <div class="fs-14 font-weight-bold bg-sBrown py-1 px-8 text-white">RESEP</div>
                            <div class="mt-8">
                                <template v-if="result?.prescription?.length > 0">
                                    <div v-for="(ipre, i) in result.prescription" class="mt-8">
                                        <div class="font-weight-bold">
                                            <template>
                                                <feather-icon v-if="ipre?.flag == 1" icon="CheckSquareIcon" color="green" v-b-tooltip.hover.left="`Diproses oleh farmasi`"/>
                                                <img v-else-if="ipre?.flag == 0" src="./../../../../@core/assets/icons/hourglass.svg" alt="" width="14px" v-b-tooltip.hover.left="`Berhasil diorder`">
                                            </template>
                                            {{localDateTime(ipre.date_added)}} ({{ isHomeRecipe(ipre) ? 'Resep Pulang' : 'Kardeks' }})
                                        </div>
                                        <div v-if="ipre?.cppt_doctor_added_name" class="fs-11 font-italic">Resep konsultasi dari cppt {{ ipre?.cppt_doctor_added_name }}</div>
                                        
                                        <div v-for="(list, idx_p) in ipre.form_prescription_parent" class="mt-1">
                                            <template v-if="isHomeRecipe(ipre)">
                                                <div :class="getPrescParentVariant(list.id)" class="px-12 py-1">
                                                    <div class="font-weight-bold">{{ getPrescParentName(list.id) }}</div>
                                                    <div v-if="list.id != 1" class="fs-12 text-italic" style="font-weight: 400">  
                                                        <span>Jumlah: {{ list.value | number}}, </span>
                                                        <span>{{ list.used }}</span>
                                                    </div>
                                                </div>
                                                <div v-if="ipre?.form_prescription" class="mt-1">
                                                    <li v-for="unit in ipre.form_prescription" v-if="list.id == unit.prescription_item_type_id">
                                                        {{ unit?.pharmacy_catalog_name }}, jml: {{ unit?.qty | number}}
                                                        <span v-if="unit?.prescription_item_type_id == 1">, {{ unit?.used }} {{ unit?.desc }}</span>
                                                    </li>
                                                </div>
                                            </template>
                                            <template v-else>
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
                                                        </span>
                                                    </li>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                                <div v-else>-</div>
                            </div>
                            <div class="mt-12">
                                <div class="font-weight-bold">NON-FARMAKOLOGI:</div>
                                <div>
                                    <li v-for="inf in result?.kardex_non_pharmacological">
                                        <div v-html="parsedBody(inf.name || '-')"></div>
                                    </li>
                                    <div v-if="result?.plan" v-html="parsedBody(result?.plan || '-')"></div>
                                </div>
                            </div>
                            <div v-if="result?.pharmacological_plan" class="mt-12">
                                <div class="font-weight-bold">FARMAKOLOGI:</div>
                                <div v-html="parsedBody(result?.pharmacological_plan || '-')"></div>
                            </div>
                        </div>
                    </template>
                    <div class="col-12 px-8 mt-12 fs-12">
                        <div class="fs-14 font-weight-bold bg-sBrown py-1 px-8 text-white">VERIFIKASI</div>
                        <div class="row mt-8">
                            <div class="col-lg-6 border-right">
                                <div>
                                    <div class="font-weight-bold">Nama Pelapor</div>
                                    <div>{{ result?.user_added_name || '-' }} ({{ result?.user_added_role }})</div>
                                </div>

                                <div class="mt-8">
                                    <div class=" font-weight-bold">Waktu Menulis SBAR</div>
                                    <div>{{ localDateTime(result?.date_added) }}</div>
                                </div>

                                <div class="mt-8">
                                    <div class="font-weight-bold">Tanda Tangan Pelapor</div>
                                    <div class="">
                                        <img :src="urlbe+result?.user_added_ttd" alt="Tanda Tangan" height="80" v-if="result?.user_added_ttd"/>
                                        <div v-else>-</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div>
                                    <div class="font-weight-bold">Nama Dokter yang Dikonsulkan</div>
                                    <div>{{ result?.doctor_in_charge_name_sbar || '-' }}</div>
                                </div>

                                <div class="mt-8">
                                    <div>
                                        <b-icon icon="check-circle-fill" variant="success" font-scale="1" v-if="result?.verification_time" />
                                        <span class="font-weight-bold">Waktu Verifikasi</span>
                                    </div>
                                    <div>{{ localDateTime(result?.verification_time) }}</div>
                                </div>

                                <div class="mt-8">
                                    <div class="font-weight-bold">Tanda Tangan Dokter yang Dikonsulkan</div>
                                    <div class="">
                                        <img :src="urlbe+result?.doctor_in_charge_ttd_sbar" alt="Tanda Tangan" height="80" v-if="result?.doctor_in_charge_ttd && result?.verification_time"/>
                                        <div v-else>-</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 mt-8">
                                <div v-if="result?.decline_reason != null">
                                    <div>
                                        <b-icon icon="x-circle-fill" variant="danger" font-scale="1" />
                                        <span class="font-weight-bold">Alasan Penolakan:</span>
                                    </div>
                                    <p class="text-danger">{{ result.decline_reason }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </b-collapse>

        <LabResultModalView ref="labModalResult"/>
        <RadResultModalView ref="radModalResult"/>
    </section>
</template>

<script>
import optionMixin from '@/mixins/optionMixin';
import dateMixin from "@/mixins/dateMixin";
import RadResultModalView from '../medical_support/RadResultModalView.vue';
import LabResultModalView from '../medical_support/LabResultModalView.vue';

export default {
    mixins: [optionMixin, dateMixin],
    components: {
        RadResultModalView,
        LabResultModalView,
    },
    data() {
        return {
            userData: JSON.parse(localStorage.getItem('userData')),
            urlbe:process.env.VUE_APP_IMAGE,
            admin_group_id: 'ddad321c-a9d8-430c-95f0-de38d20834a1',
            jkn_group_id: 'a4c04d8d-12d8-4c54-afca-69de6bd5f62d',
            hfi_inpatient: '40f06534-07ec-4221-ad9d-dea08196079f',
            hfi_outpatient: '3cb626f5-9148-4fb1-8663-3d7f028b01a6',
            hfi_emergency: '444b3134-5b75-4b67-af72-a558d46716d7',
            admin_group_id: 'ddad321c-a9d8-430c-95f0-de38d20834a1',
            doctor_group_id: 'efb3fdff-fbe3-4954-a391-3729e544b62a',
        }
    },
    props: {
        result: {
            type: Object,
        },
        regData: {
            type: Object,
        },
        signature_time: {
            type: Array,
            default: () => [
                { value: '-', title: '-' },
            ]
        },
        catalog_instruction: {
            type: Array,
            default: () => [
                { value: '-', title: '-' },
            ]
        },
        signature_units: {
            type: Array,
            default: () => [
                { value: '-', title: '-' },
            ]
        },
        visible: {
            type: Boolean,
            default: false,
        },
    },

    filters:{
        number(value) {
            return Number(value)
        },
    },

    methods: {
        isHomeRecipe(obj) {
            if (obj?.type_prescription_id == 2) return true
            return false                    
        },

        getSoapLabel(result) {
            // console.log(result, 'result getsoaplabel')
            return `SBAR`
        },

        getParentVariant(parent_id){
            if (parent_id == 1) return 'bg-primary-100'
            if (parent_id == 2) return 'bg-success-100'
            if (parent_id == 3) return 'bg-orange-100'
            if (parent_id == 4) return 'bg-danger-100'
            if (parent_id == 5) return 'bg-pink-100'
            return 'bg-white'
        },

        getParentName(parent_id){
            if (parent_id == 1) return 'OBAT SATUAN'
            if (parent_id == 2) return 'RACIK 1'
            if (parent_id == 3) return 'RACIK 2'
            if (parent_id == 4) return 'RACIK 3'
            if (parent_id == 5) return 'RACIK 4'
            return '-'
        },

        getMeaningVariant(value) {
            if (value && value != 1) return 'text-danger-500'
            return ''
        },

        triggerRadResult(message) {
            this.$refs.radModalResult.seeRadiologyResult(message);
        },

        triggerLabResult(message) {
            this.$refs.labModalResult.seeLaboratoryResult(message);
        },

        parsedBody(text){
            return text ? text.replace(/\n/g,"<br>") : ''
        },   
    }
}
</script>