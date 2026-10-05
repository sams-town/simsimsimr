<template>
    <div>
        <b-modal v-model="active.main_modal" scrollable size="xl" hide-footer>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    KARDEKS
                    <span class="text-primary">{{ temp_header.alias_name }}. {{ temp_header.name }} | {{localDate(temp_header.birth_date)}} ({{ parsedAge(temp_header.birth_date) }}) | {{ temp_header.norm }}</span>
                </div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>
            <section>
                <b-card no-body>
                    <b-tabs card nav-wrapper-class="bg-primary pt-0 pl-8" active-nav-item-class="bg-orange-400">
                        <b-tab no-body active>
                            <template #title>
                                <span class="fs-14 font-weight-bold text-white">RIWAYAT KARDEKS</span>
                            </template>
                            <div class="p-16">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="card">
                                            <div class="card-header bg-primary text-white fs-14 font-weight-bold px-12 py-8">PLANNING AKTIF</div>
                                            <div class="card-body p-0">
                                                <template v-if="active_kardex?.length > 0">
                                                    <div v-for="(list, listIndex) in active_kardex">
                                                        <div class="p-12" :class="{'bg-diagonal': list.pharmacy_prescription_id != null}">
                                                            <ModernLabel :label="`Ditambahkan pada ${localDateTime(list.date_added)}`" />
        
                                                            <b-table-simple responsive small class="fs-12 mt-10 bg-white">
                                                                <b-tr>
                                                                    <b-th>Nama</b-th>
                                                                    <b-th style="width: 70px;">Kemasan</b-th>
                                                                    <b-th>Signatura</b-th>
                                                                    <b-th>Administer</b-th>
                                                                </b-tr>
                                                                <template v-for="(category, catIndex) in list.kardex_parent" v-if="hasItemsInCategory(list.kardex_item, category.id)">
                                                                    <b-tr>
                                                                        <b-th colspan="5" :class="getPrescParentVariant(category.id)">
                                                                            <div class="text-uppercase ">{{ getPrescParentName(category.id) }}</div>
                                                                            <div v-if="category.id != 1" class="fs-12 text-italic" style="font-weight: 400">  
                                                                                <span>Kemasan: {{ getParentPackageName(category?.package) }}, </span>
                                                                                <span>
                                                                                    {{ category?.value ?? '-' }}
                                                                                    TIAP {{ getDynamicOptName(category?.signatura_times_id, optSignTime)}}
                                                                                    {{ getDynamicOptName(category?.administer_id, optAdminister) }}
                                                                                    <span v-if="category.administer_id == other_administer">: {{ category.desc }}</span>
                                                                                </span>
                                                                            </div>
                                                                        </b-th>
                                                                    </b-tr>
                                                                    <template v-for="(item, itemIndex) in list.kardex_item">
                                                                        <b-tr :key="itemIndex" v-if="item.prescription_item_type_id == category.id" :class="getKardexItemVariant(item, list.suggestion_status, list.is_approve)">
                                                                            <b-td>
                                                                                <div :class="{'font-weight-bold': item.pharmacy_catalog_ref_name}">{{ item.pharmacy_catalog_name }}</div>
                                                                                <div v-if="item.pharmacy_catalog_ref_name" class=" d-flex fs-10">
                                                                                    <feather-icon icon="CornerLeftUpIcon" class="mr-1"/>
                                                                                    <div>
                                                                                        <div>{{item.pharmacy_catalog_ref_name}}</div>
                                                                                        <div class="font-italic">
                                                                                            <span style="font-weight: 600;">Alasan subtitusi: </span>
                                                                                            {{item.notes}}
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="d-flex">
                                                                                    <div>
                                                                                        <b-badge v-if="item?.status" :variant="getKardexItemStatusVariant(item?.status)" class="text-uppercase mr-1">{{ getKardexItemStatusName(item?.status) }}</b-badge>
                                                                                    </div>
                                                                                    <small class="font-italic" :class="{'text-neutral-500': !item.is_change == 1}">
                                                                                        <div>ditambahkan pada {{ localDateTime(item.catalog_date) }} oleh {{ item.doctor_added_name || '-'}}</div>
                                                                                        <div v-if="item.status == 4">stop pada {{ localDateTime(item.stop_date) }} (digunakan selama {{ differenceDate(item.stop_date, item.catalog_date) }})</div>
                                                                                    </small>
                                                                                </div>
                                                                            </b-td>
                                                                            <b-td>{{ item.package_name }}</b-td>
                                                                            <b-td>
                                                                                {{ item.dose }} 
                                                                                {{ getDynamicOptName(item.dose_unit_id, optSignUnits)}} / 
                                                                                {{ getDynamicOptName(item.signatura_times_id, optSignTime)}}
                                                                            </b-td>
                                                                            <b-td>
                                                                                <span>{{ getDynamicOptName(item.administer_id, optAdminister) }}</span>
                                                                                <span v-if="item.administer_id == other_administer">: {{ item.desc }}</span>
                                                                            </b-td>
                                                                        </b-tr>
                                                                    </template>
                                                                </template>
                                                            </b-table-simple>
        
                                                            <div class="fs-14 bg-white p-12">
                                                                <b class="text-uppercase">Non-Farmakologi : </b> 
                                                                <li v-for="unit in list?.kardex_non_pharmacological">
                                                                    <span>{{ unit.name }} </span>
                                                                    <small class="fs-11 font-italic text-neutral-500">(ditambahkan oleh {{ unit.doctor_added_name || '-' }})</small>
                                                                </li>
                                                            </div>
                                                        </div>
                                                        <hr class="m-0">
                                                    </div>
                                                </template>
                                                <div v-else class="p-16 text-center">
                                                    <i>Tidak ada resep kardeks aktif pada perawatan ini</i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mt-16 mt-lg-0">
                                        <div class="card">
                                            <div class="card-header bg-w3green text-white fs-14 font-weight-bold px-12 py-8">RESEP SEBELUMNYA SELAMA PERAWATAN</div>
                                            <div class="card-body p-0">
                                                <template v-if="deactive_kardex?.length > 0">
                                                    <div v-for="(list, listIndex) in deactive_kardex">
                                                        <div class="p-12" :class="{'bg-diagonal': list.pharmacy_prescription_id != null}">
                                                            <ModernLabel :label="`Ditambahkan pada ${localDateTime(list.date_added)}`" variant="w3green" />
        
                                                            <b-table-simple responsive small class="fs-12 mt-10 bg-white">
                                                                <b-tr>
                                                                    <b-th>Nama</b-th>
                                                                    <b-th style="width: 70px;">Kemasan</b-th>
                                                                    <b-th>Signatura</b-th>
                                                                    <b-th>Administer</b-th>
                                                                </b-tr>
                                                                <template v-for="(category, catIndex) in list.kardex_parent" v-if="hasItemsInCategory(list.kardex_item, category.id)">
                                                                    <b-tr>
                                                                        <b-th colspan="5" :class="getPrescParentVariant(category.id)">
                                                                            <div class="text-uppercase ">{{ getPrescParentName(category.id) }}</div>
                                                                            <div v-if="category.id != 1" class="fs-12 text-italic" style="font-weight: 400">  
                                                                                <span>Kemasan: {{ getParentPackageName(category?.package) }}, </span>
                                                                                <span>
                                                                                    {{ category?.value ?? '-' }}
                                                                                    TIAP {{ getDynamicOptName(category?.signatura_times_id, optSignTime)}}
                                                                                    {{ getDynamicOptName(category?.administer_id, optAdminister) }}
                                                                                    <span v-if="category.administer_id == other_administer">: {{ category.desc }}</span>
                                                                                </span>
                                                                            </div>
                                                                        </b-th>
                                                                    </b-tr>
                                                                    <template v-for="(item, itemIndex) in list.kardex_item">
                                                                        <b-tr :key="itemIndex" v-if="item.prescription_item_type_id == category.id" :class="{'bg-warning-400': item?.is_change == 1}">
                                                                            <b-td>
                                                                                <div :class="{'font-weight-bold': item.pharmacy_catalog_ref_name}">{{ item.pharmacy_catalog_name }}</div>
                                                                                <div v-if="item.pharmacy_catalog_ref_name" class=" d-flex fs-10">
                                                                                    <feather-icon icon="CornerLeftUpIcon" class="mr-1"/>
                                                                                    <div>
                                                                                        <div>{{item.pharmacy_catalog_ref_name}}</div>
                                                                                        <div class="font-italic">
                                                                                            <span style="font-weight: 600;">Alasan subtitusi: </span>
                                                                                            {{item.notes}}
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="d-flex">
                                                                                    <div>
                                                                                        <b-badge v-if="item?.status" :variant="getKardexItemStatusVariant(item?.status)" class="text-uppercase mr-1">{{ getKardexItemStatusName(item?.status) }}</b-badge>
                                                                                    </div>
                                                                                    <small class="font-italic" :class="{'text-neutral-500': !item.is_change == 1}">
                                                                                        <div>ditambahkan pada {{ localDateTime(item.catalog_date) }} oleh {{ item.doctor_added_name || '-'}}</div>
                                                                                        <div v-if="item.status == 4">stop pada {{ localDateTime(item.stop_date) }} ({{ differenceDate(item.stop_date, item.catalog_date) }})</div>
                                                                                    </small>
                                                                                </div>
                                                                            </b-td>
                                                                            <b-td>{{ item.package_name }}</b-td>
                                                                            <b-td>
                                                                                {{ item.dose }} 
                                                                                {{ getDynamicOptName(item.dose_unit_id, optSignUnits)}} / 
                                                                                {{ getDynamicOptName(item.signatura_times_id, optSignTime)}}
                                                                            </b-td>
                                                                            <b-td>
                                                                                <span>{{ getDynamicOptName(item.administer_id, optAdminister) }}</span>
                                                                                <span v-if="item.administer_id == other_administer">: {{ item.desc }}</span>
                                                                            </b-td>
                                                                        </b-tr>
                                                                    </template>
                                                                </template>
                                                            </b-table-simple>
        
                                                            <div class="fs-14 bg-white p-12">
                                                                <b class="text-uppercase">Non-Farmakologi : </b> 
                                                                <li v-for="unit in list?.kardex_non_pharmacological">
                                                                    <span>{{ unit.name }} </span>
                                                                    <small class="fs-11 font-italic text-neutral-500">(ditambahkan oleh {{ unit.doctor_added_name || '-' }})</small>
                                                                </li>
                                                            </div>
                                                        </div>
                                                        <hr class="m-0">
                                                    </div>
                                                </template>
                                                <div v-else class="p-16 text-center">
                                                    <i>Tidak ada riwayat resep kardeks sebelumnya yang tercatat pada perawatan ini</i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </b-tab>

                        <b-tab no-body>
                            <template #title>
                                <span class="fs-14 font-weight-bold text-white">STOK KARDEKS</span>
                            </template>
                            <div class="card-body px-16 py-8">
                                <template v-if="kardex_stock?.length > 0">
                                    <b-table-simple small fixed bordered class="text-center">
                                        <b-tr class="fs-14">
                                            <b-th style="width: 50px">NO</b-th>
                                            <b-th style="width: 400px" class="text-left">NAMA OBAT</b-th>
                                            <b-th>DOSIS</b-th>
                                            <b-th>STOCK</b-th>
                                            <b-th>JUMLAH FISIK</b-th>
                                        </b-tr>
                                        <template v-for="(item, index) in kardex_stock">
                                            <template v-if="item.kardex_concoction.length > 0">
                                                <b-tr class="fs-12 bg-warning-200">
                                                    <b-td :rowspan="item.kardex_concoction.length+1">{{index+1}}</b-td>
                                                    <b-td class="text-left">
                                                        <div>{{ item.catalog_name }}</div>
                                                        <b-badge :variant="getKardexItemStatusVariant(item?.status)" class="text-uppercase mr-1">{{ getKardexItemStatusName(item?.status) }}</b-badge>
                                                    </b-td>
                                                    <b-td>{{ item.dose }} {{ item.dose_unit_name }} TIAP {{ item.signatura_times_name }}</b-td>
                                                    <b-td>{{ item.current_amount }} {{ item.current_amount_unit_name }}</b-td>
                                                    <b-td>{{ item.current_physical_quantity }} {{ item.package_name }}</b-td>
                                                </b-tr>
                                                <b-tr v-for="list in item.kardex_concoction" class="fs-12" >
                                                    <b-td class="text-left">
                                                        {{ list.pharmacy_catalog_name }}
                                                        <feather-icon icon="InfoIcon" :id="`popover-conc-${list.id}`" :color="list.is_incomplete_master == 1 ? 'red' : 'grey'" class="pointer"/>
                                                        <b-popover :target="`popover-conc-${list.id}`" triggers="hover" placement="right">
                                                            <template #title>
                                                                <div class="fs-10 text-uppercase font-weight-bold">{{ list.catalog_name }}</div>
                                                            </template>
                                                            <div class="fst-italic fs-10">
                                                                <li>Kekuatan: {{ list.catalog_dose }} {{ list.catalog_dose_unit_name}}</li>
                                                                <li>Kemasan: {{ list.catalog_packaging_value }} {{ list.catalog_packaging_unit_name}}</li>
                                                            </div>
                                                        </b-popover>
                                                    </b-td>
                                                    <b-td>{{ list.dose }} {{ list.dose_unit_name }}</b-td>
                                                    <b-td>-</b-td>
                                                    <b-td>-</b-td>
                                                </b-tr>
                                            </template>
                                            <b-tr v-else class="fs-12">
                                                <b-td>{{index+1}}</b-td>
                                                <b-td class="text-left">
                                                    <div>
                                                        {{ item.catalog_name }}
                                                        <feather-icon icon="InfoIcon" :id="`popover-stock-${item.id}`" :color="item.is_incomplete_master == 1 ? 'red' : 'grey'" class="pointer"/>                                                            
                                                    </div>
                                                    <b-badge :variant="getKardexItemStatusVariant(item?.status)" class="text-uppercase mr-1">{{ getKardexItemStatusName(item?.status) }}</b-badge>               
                                                    <b-popover :target="`popover-stock-${item.id}`" triggers="hover" placement="right">
                                                        <template #title>
                                                            <div class="fs-10 text-uppercase font-weight-bold">{{ item.catalog_name }}</div>
                                                        </template>
                                                        <div class="fst-italic fs-10">
                                                            <li>Kekuatan: {{ item.catalog_dose }} {{ item.catalog_dose_unit_name}}</li>
                                                            <li>Kemasan: {{ item.catalog_packaging_value }} {{ item.catalog_packaging_unit_name}}</li>
                                                        </div>
                                                    </b-popover>
                                                </b-td>
                                                <b-td>{{ item.dose }} {{ item.dose_unit_name }} TIAP {{ item.signatura_times_name }}</b-td>
                                                <b-td>{{ item.current_amount }} {{ item.current_amount_unit_name }}</b-td>
                                                <b-td>{{ item.current_physical_quantity }} {{ item.package_name }}</b-td>
                                            </b-tr>
                                        </template>
                                    </b-table-simple>
                                </template>
                                <div v-else class="p-16 fs-14 text-center d-flex justify-content-center align-items-center h-100">
                                    <i>Tidak ada stok kardeks yang tercatat pada perawatan ini</i>
                                </div>
                                <!-- <pre>{{ kardex_stock }}</pre> -->
                            </div>
                        </b-tab>

                        <b-tab no-body>
                            <template #title>
                                <span class="fs-14 font-weight-bold text-white">RIWAYAT ORDER</span>
                            </template>
                            <div class="card-body p-0">
                                <template v-if="kardex_do?.length > 0">
                                    <div v-for="(list, listIndex) in kardex_do">
                                        <div class="px-16 py-8">
                                            <ModernLabel :label="`${list.no_order}`" :small="`Ditambahkan pada ${localDateTime(list.date_added)}`" variant="w3green" />
                                            <b-table-simple responsive small class="fs-12 mt-10 bg-white mb-0">
                                                <b-tr>
                                                    <b-th>NAMA</b-th>
                                                    <b-th style="width: 80px;">JUMLAH</b-th>
                                                    <b-th style="width: 140px;">SIGNATURA</b-th>
                                                    <b-th style="width: 220px;">ADMINISTER</b-th>
                                                </b-tr>
                                                <template v-for="(category, catIndex) in list.form_prescription_parent" v-if="hasItemsInCategory(list.form_prescription, category.id)">
                                                    <b-tr>
                                                        <b-th colspan="5" :class="getPrescParentVariant(category.id)">
                                                            <div class="text-uppercase ">{{ getPrescParentName(category.id) }}</div>
                                                            <div v-if="category.id != 1" class="fs-12 text-italic" style="font-weight: 400">  
                                                                <span>Kemasan: {{ getParentPackageName(category?.package) }}, </span>
                                                                <span>
                                                                    {{ category?.value ?? '-' }}
                                                                    TIAP {{ getDynamicOptName(category?.signatura_times_id, optSignTime)}}
                                                                    {{ getDynamicOptName(category?.administer_id, optAdminister) }}
                                                                    <span v-if="category.administer_id == other_administer">: {{ category.desc }}</span>
                                                                </span>
                                                            </div>
                                                        </b-th>
                                                    </b-tr>
                                                    <template v-for="(item, itemIndex) in list.form_prescription">
                                                        <b-tr :key="itemIndex" v-if="item.prescription_item_type_id == category.id">
                                                            <b-td>{{ item.pharmacy_catalog_name }}</b-td>
                                                            <b-td>{{ item.qty }} {{ item.package_name }}</b-td>
                                                            <b-td>
                                                                {{ item.dose }} 
                                                                {{ getDynamicOptName(item.dose_unit_id, optSignUnits)}} / 
                                                                {{ getDynamicOptName(item.signatura_times_id, optSignTime)}}
                                                            </b-td>
                                                            <b-td>
                                                                <span>{{ getDynamicOptName(item.administer_id, optAdminister) }}</span>
                                                                <span v-if="item.administer_id == other_administer">: {{ item.desc }}</span>
                                                            </b-td>
                                                        </b-tr>
                                                    </template>
                                                </template>
                                            </b-table-simple>
                                        </div>
                                        <hr class="m-0">
                                    </div>
                                </template>
                                <div v-else class="p-16 fs-14 text-center d-flex justify-content-center align-items-center h-100">
                                    <i>Tidak ada riwayat order sebelumnya yang tercatat pada perawatan ini</i>
                                </div>
                                <!-- <pre>{{ kardex_do }}</pre> -->
                            </div>
                        </b-tab>
                    </b-tabs>
                </b-card>                
            </section>
        </b-modal>
    </div>
</template>

<script>
// import { mapState } from 'vuex'
import dateMixin from "@/mixins/dateMixin";
import optionMixin from "@/mixins/optionMixin";

export default {
    mixins: [dateMixin, optionMixin],

    data() {
        return {
            regData: {},
            active_kardex: [],
            deactive_kardex: [],

            active: {
                main_modal: false,
            },

            temp_header: {
                name: '',
                alias_name: '',
                birth_date: '',
                norm: ''
            },
            optSignTime: [],
            optAdminister: [],
            optSignUnits: [],
            other_administer: 'ea3c2573-b9f5-428d-ada4-0ce6ea67c72f',
            kardex_stock: [],
            kardex_do: [],
        }
    },

    // computed: {
    //     ...mapState({ 
    //         optKardexItemStatus: state => state.frontoptions.doctor.assessment.prescription.kardex_item_status,
    //     }),
    // },

    methods: {
        getKardexItemVariant(obj, suggestion_status, is_approve) {
            if ((obj?.is_suggestion == 1 && suggestion_status == 2 && is_approve == 0) || (obj.is_reject == 1 && is_approve == 1)) return 'bg-ax-danger-300 text-white' 
            if (obj?.is_change == 1) return 'bg-warning-400' 
        },

        async parentTrigger(message) {
            // console.log('message: ', message);
            this.getOptSignTime()
            this.getOptCatInstruction()
            this.getSignaturaUnits()
            
            this.regData = message?.regData
            await this.getKardex(this.regData.registration_destination_id)
            await this.getKardexStock(this.regData.registration_destination_id)
            await this.getKardexDo(this.regData.registration_destination_id)
            
            this.temp_header.name = this.regData.name
            this.temp_header.alias_name = this.regData.alias_name
            this.temp_header.birth_date = this.regData.birth_date
            this.temp_header.norm = this.regData.norm
            
            this.active.main_modal = true
        },

        async getKardex(regDesId) {
            const param = { 
                registration_destination_id: regDesId,
            }
            try {                
                await this.$store.dispatch('kardex/getKardexList', param)
                .then(
                    response => {
                        const temp = response.rows || []
                        // console.log('temp: ', temp);
                        if (temp) {
                            this.active_kardex = temp.filter(item => item.is_active == 1)
                            this.deactive_kardex = temp.filter(item => item.is_active == 0)
                            // console.log(`${this.active_kardex.length}`, this.active_kardex);
                            // console.log(`${this.deactive_kardex.length}`, this.deactive_kardex);
                        }
                    }
                )
                .catch(({message, debug, response}) => {
                    console.error('Error GET kardex/getKardexList at: ', response);
                })
            } catch (error) {
                console.error('Error catch while GET kardex/getKardexList at: ', error);
            }
        },

        hasItemsInCategory(items, categoryId) {
            if (items) {
                return items.some(item => item.prescription_item_type_id === categoryId);
            }
        },

        async getOptSignTime() {
            try {
                await this.$store.dispatch('kardex/getOptSignaturaTime')
                .then(response => {
                    this.optSignTime = response
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
                    this.optAdminister = response
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
                        this.optSignUnits = response
                    }
                )
                .catch(({message, debug, response}) => {
                    console.error('Error GET kardex/getOptUnit at: ', response);
                })
            } catch (error) {
                console.error('Error catch while GET kardex/getOptUnit at: ', error);
            }
        },

        async getKardexStock(reg_des_id) {
            const param = { 
                registration_destination_id: reg_des_id,
            }
            try {                
                await this.$store.dispatch('kardex/getKardexStock', param)
                .then(
                    async response => {
                        this.kardex_stock = response?.sort((a, b) => {
                            if (a.status !== b.status) {
                                return a.status - b.status
                            }
                            return a.catalog_name.toLowerCase().localeCompare(b.catalog_name.toLowerCase())
                        })
                    }
                )
                .catch(({message, debug, response}) => {
                    console.error('Error GET kardex/getKardexStock at: ', response);
                })
            } catch (error) {
                console.error('Error catch while GET kardex/getKardexStock at: ', error);
            }
        },

        async getKardexDo(reg_des_id) {
            const param = { 
                registration_destination_id: reg_des_id,
                list_type: 'kardex'
            }
            try {                
                await this.$store.dispatch('prescription/getPrescriptionListV2', param)
                .then(
                    async response => {
                        this.kardex_do = response?.rows || []
                        // console.log('this.kardex_do: ', this.kardex_do);
                    }
                )
                .catch(({message, debug, response}) => {
                    console.error('Error GET prescription/getPrescriptionListV2 at: ', response);
                })
            } catch (error) {
                console.error('Error catch while GET prescription/getPrescriptionListV2 at: ', error);
            }
        },
    },
}
</script>

<style scoped>
    .bg-diagonal {
        background-color: #ffffff;
        opacity: 1;
        background-size: 8px 8px;
        background-image: repeating-linear-gradient(45deg, #d4d4ff 0, #d4d4ff 0.8px, #ffffff 0, #ffffff 50%);
    }
</style>