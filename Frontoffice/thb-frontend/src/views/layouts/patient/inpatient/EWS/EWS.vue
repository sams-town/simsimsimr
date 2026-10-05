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

        <div class="mt-32">
            <div class="d-flex justify-content-between align-items-center">
                <b-form-group label-cols="6" label-align="left" label-size="sm" label-for="sortBySelect" class="text-nowrap mb-md-0">
                    <span class="text-regular-14">Tampilkan</span>
                    <b-form-select v-model="perPage" size="sm" :options="pageOptions" class="mx-1"/>
                    <span class="text-regular-14">Entri</span>
                </b-form-group>
                <b-button @click="openModalEws" size="sm" variant="primary">
                    <feather-icon icon="PlusIcon" class="mr-1"/> Tambah
                </b-button>
            </div>

            <b-table :fields="MainFields" :items="ews_list" bordered responsive class="mt-8" style="font-size: 13px;">
                <template #cell(action)="row">
                    <div class="d-flex">
                        <b-button @click="onEditEws(row.item)" size="sm" variant="warning-500" class="mr-1">
                            <feather-icon icon="EditIcon"/>
                        </b-button>
                        <b-button @click="removeEws(row.item)" size="sm" variant="danger-500" class="mr-1">
                            <feather-icon icon="TrashIcon" />
                        </b-button>
                        <b-button @click="openModalItem(row.item.id)" size="sm" variant="outline-w3green" class="mr-1">
                            <feather-icon icon="PlusIcon" class="mr-1" />Item
                        </b-button>
                        <template v-if="row.item.early_warning_score_item.length > 0">
                            <b-button size="sm" @click="row.toggleDetails" variant="outline-primary-500">
                                {{ row.detailsShowing ? 'Hide' : 'Show'}}
                            </b-button>
                        </template>
                    </div>
                </template>
                <template #row-details="row">
                    <b-table :fields="ItemFields" :items="row.item.early_warning_score_item" small responsive >
                        <template #cell(action)="data">
                            <div class="d-flex">
                                <b-button @click="onEditItem(data.item)" size="sm" variant="warning-500" class="mr-1">
                                    <feather-icon icon="EditIcon"/>
                                </b-button>
                                <b-button @click="removeItemEws(data.item, row.item.observation_date)" size="sm" variant="danger-500">
                                    <feather-icon icon="TrashIcon" />
                                </b-button>
                            </div>
                        </template>
                    </b-table>
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
        </div>

        <ModernLabel label="Monitoring EWS" class="mt-32 mb-8" />
        <div class="text-center" style="overflow-x: scroll; font-size: 12px;">
            <div class="d-flex flex-nowrap">
                <b-table-simple bordered small class="text-uppercase">
                    <b-tr>
                        <b-th rowspan="2">EWS</b-th>
                        <b-th>Tanggal</b-th>
                    </b-tr>
                    <b-tr>
                        <b-th>Jam</b-th>
                    </b-tr>
                    <b-tr>
                        <b-th class="align-middle" style="height: 50px;">Tingkat Kesadaran</b-th>
                        <b-th class="align-middle" style="height: 50px;">A/V/P/U</b-th>
                    </b-tr>
                    <b-tr>
                        <th :rowspan="arry_blood_pressure.length + 1">Tekanan darah (sistolik)</th>
                        <th>&ge; 230</th>
                    </b-tr>
                    <template v-for="(number, index) in arry_blood_pressure">
                        <b-tr>
                            <b-th>{{ index+1 == arry_blood_pressure.length ? `&le; ${number}` : number }}</b-th>
                        </b-tr>
                    </template>
                    <b-tr>
                        <th :rowspan="arry_pulse.length + 1">Nadi</th>
                        <th>&ge; 140</th>
                    </b-tr>
                    <template v-for="(number, index) in arry_pulse">
                        <b-tr>
                            <b-th>{{ index+1 == arry_pulse.length ? `&lt; ${number}` : number }}</b-th>
                        </b-tr>
                    </template>
                    <b-tr>
                        <th :rowspan="arry_breath.length + 1">Pernafasan</th>
                        <th>&ge; 25</th>
                    </b-tr>
                    <template v-for="(number, index) in arry_breath">
                        <b-tr>
                            <b-th>{{ index + 1 === arry_breath.length ? `&le; ${number}` : `${arry_breath[index + 1] + 1}-${number}` }}</b-th>
                        </b-tr>
                    </template>
                    <b-tr>
                        <th :rowspan="arry_temperature.length + 1">Suhu</th>
                        <th>&ge; 39.1ᵒ</th>
                    </b-tr>
                    <template v-for="(number, index) in arry_temperature">
                        <b-tr>
                            <b-th>{{ index + 1 === arry_temperature.length ? `&lt; ${number}ᵒ` : `${number}ᵒ` }}</b-th>
                        </b-tr>
                    </template>
                    <b-tr>
                        <th :rowspan="arry_spo2.length + 1">SpO2</th>
                        <th>&ge; 96</th>
                    </b-tr>
                    <template v-for="(number, index) in arry_spo2">
                        <b-tr>
                            <b-th>{{ index + 1 === arry_spo2.length ? `&le; ${number}` : `${arry_spo2[index + 1] + 1}-${number}` }}</b-th>
                        </b-tr>
                    </template>
                    <b-tr>
                        <th rowspan="2">Oksigenasi</th>
                        <th>Ya</th>
                    </b-tr>
                    <tr>
                        <th>Tidak</th>
                    </tr>
                    <b-tr>
                        <th colspan="2">Total EWS</th>
                    </b-tr>
                    <!-- <b-tr>
                        <th colspan="2">Skala jatuh</th>
                    </b-tr>
                    <b-tr>
                        <th colspan="2">Skala Nyeri</th>
                    </b-tr>
                    <b-tr>
                        <th colspan="2">Intake</th>
                    </b-tr>
                    <b-tr>
                        <th colspan="2">Output</th>
                    </b-tr>
                    <b-tr>
                        <th colspan="2">Diit</th>
                    </b-tr> -->

                </b-table-simple>
                <template v-for="(item, index_i) in ews_list">
                    <template v-for="(list, index_j) in item.early_warning_score_item">
                        <b-table-simple bordered small>
                            <b-tr >
                                <td>{{ item.observation_date }}</td>
                            </b-tr>
                            <b-tr >
                                <td>{{ list.observation_time }}</td>
                            </b-tr>
                            <b-tr>
                                <td class="align-middle" style="height: 50px;">{{ list.aware != 0 ? `3` : `0` }} - {{ getAwareName(list.aware) }}</td>
                            </b-tr>
    
                            <!-- param BP >=230 = range >=230 -->
                            <tr>
                                <td class="bg-danger-200">{{ list.blood_pressure >= arry_blood_pressure[0] ? `3 (${list.blood_pressure})` : '-' }}</td>
                            </tr>
                            <!-- param BP 220 = range 229-0 -->
                            <template v-for="(number, index_j) in arry_blood_pressure">                                
                                <tr>
                                    <td :class="getBloodVariant(index_j)">{{ getMidBlood(list.blood_pressure, index_j, arry_blood_pressure[index_j], arry_blood_pressure[index_j+1]) }}</td>
                                </tr>
                            </template>
                            
                            <!-- param P >=140 = range >=140 -->
                            <tr>
                                <td class="bg-danger-200">{{ list.pulse >= arry_pulse[0] ? `3 (${list.pulse})` : '-' }}</td>
                            </tr>
                            <!-- param P 131 = range 139-0 -->
                            <template v-for="(number, index_j) in arry_pulse">
                                <tr>
                                    <td :class="getPulseVariant(index_j)">{{ getMidPulse(list.pulse, index_j, arry_pulse[index_j], arry_pulse[index_j+1]) }}</td>
                                </tr>
                            </template>
    
                            <!-- param B >=25 = range >=25 -->
                            <tr>
                                <td class="bg-danger-200">{{ list.breathing >= 25 ? `3 (${list.breathing})` : '-' }}</td>
                            </tr>
                            <!-- param B 21-24 = range 24-0 -->
                            <template v-for="(number, index_j) in arry_breath">                            
                                <tr>
                                    <td :class="getBreathVariant(index_j)">{{ getMidBreath(list.breathing, index_j, arry_breath[index_j], arry_breath[index_j+1]) }}</td>
                                </tr>    
                            </template>
    
                            <!-- param T <=39.1 = range 39.1-38.2 -->
                            <tr>
                                <td class="bg-warning-100">{{ list.temperature >= arry_temperature[0] ? `2 (${list.temperature})` : '-' }}</td>
                            </tr>
                            <!-- param B 21-24 = range 24-0 -->
                            <template v-for="(number, index_j) in arry_temperature">                            
                                <tr>
                                    <td :class="getTemperatureVariant(index_j)">{{ getMidTemperature(list.temperature, index_j, arry_temperature[index_j], arry_temperature[index_j+1]) }}</td>
                                </tr>
                            </template>
    
                            <!-- param S <=96 = range 96 -->
                            <tr>
                                <td class="bg-white">{{ list.oxygen_saturation >= 96 ? `0 (${list.oxygen_saturation})` : '-' }}</td>
                            </tr>
                            <!-- param S 94-95 = range 95-0 -->
                            <template v-for="(number, index_j) in arry_spo2">                            
                                <tr>
                                    <td :class="getSpo2Variant(index_j)">{{ getMidSpo2(list.oxygen_saturation, index_j, arry_spo2[index_j], arry_spo2[index_j+1]) }}</td>
                                </tr>
                            </template>
    
                            <!-- <tr>
                                <td :class="list.oxygen == 0 ? `bg-warning-100` : 'bg-white'">{{ list.oxygen == 2 ? `0` : `2` }}</td>
                            </tr> -->
                            
                            <tr>
                                <td class="bg-warning-100">{{ list.oxygen != 2 ? `2` : `-` }}</td>
                            </tr>
                            <tr>
                                <td class="bg-white">{{ list.oxygen == 2 ? `0` : `-` }}</td>
                            </tr>
                            <tr>
                                <td class="bg-white">{{ list.total ? list.total : '0' }}</td>
                            </tr>
                            <!-- <tr>
                                <td class="bg-white">{{ list.falling_scale ? list.falling_scale : '-' }}</td>
                            </tr>
                            <tr>
                                <td class="bg-white">{{ list.pain_scale ? list.pain_scale : '-' }}</td>
                            </tr>
                            <tr>
                                <td class="bg-white">{{ list.intake ? list.intake : '-' }}</td>
                            </tr>
                            <tr>
                                <td class="bg-white">{{ list.output ? list.output : '-' }}</td>
                            </tr>
                            <tr>
                                <td class="bg-white">{{ list.diit ? list.diit : '-' }}</td>
                            </tr> -->
                        </b-table-simple>
                    </template>
                </template>
            </div>

        </div>

        <b-modal v-model="active.modal_main" title="Tambah/edit Tanggal Observasi" no-close-on-backdrop>
            <div class="col">
                <label class="c-form-label">Tangal Observasi</label>
                <b-form-input v-model="form_ews.observation_date" type="date" />
            </div>
            <template #modal-footer>
                <div class="d-flex justify-content-end">
                    <b-button @click="validationEws" :disabled="active.disabled_main" variant="primary">Simpan</b-button>
                </div>
            </template>
        </b-modal>
        <b-modal v-model="active.modal_item" size="lg" title="Tambah/edit Observasi" no-close-on-backdrop>
            <div class="row">
                <div class="col-lg-4">
                    <label class="c-form-label">Jam Observasi</label>
                    <b-form-input v-model="form_item.observation_time" type="time" />
                </div>
                <div class="col-lg-4 mt-10 mt-lg-0">
                    <label class="c-form-label">Kesadaran</label>
                    <v-select v-model="form_item.aware" :options="OptionsAware" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                    <!-- <v-select v-model="form_item.aware" :options="OptionsResponse" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select> -->                    
                </div>
                <div class="col-lg-4 mt-10 mt-lg-0">
                    <label class="c-form-label">Tekanan Darah (systolic)</label>
                    <b-input-group append="mmHg">
                        <b-form-input type="number" autocomplete="off" v-model="form_item.blood_pressure" />
                    </b-input-group>
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
                        <b-form-input type="number" autocomplete="off" v-model="form_item.breathing" />
                    </b-input-group>
                </div>
                <div class="col-lg-4 mt-10">
                    <label class="c-form-label">Suhu</label>
                    <b-input-group append="ᵒC">
                        <b-form-input type="number" autocomplete="off" v-model="form_item.temperature" />
                    </b-input-group>
                </div>
                <div class="col-lg-4 mt-10">
                    <label class="c-form-label">SPO2</label>
                    <b-input-group append="%">
                        <b-form-input type="number" autocomplete="off" v-model="form_item.oxygen_saturation"></b-form-input>
                    </b-input-group>
                </div>
                <div class="col-lg-4 mt-10">
                    <label class="c-form-label">Oksigenasi</label>
                    <v-select v-model="form_item.oxygen" :options="OptionsOxygen" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select>
                    <!-- <v-select v-model="form_item.oxygen" :options="OptionsBeforeOxygen" :reduce="item => item.value" label="title" placeholder="-- Pilih --"></v-select> -->
                </div>
                <!-- <div class="col-lg-4 mt-10">
                    <label class="c-form-label">Skala Jatuh</label>
                    <b-form-input v-model="form_item.falling_scale" type="number" />
                </div>
                <div class="col-lg-4 mt-10">
                    <label class="c-form-label">Skala Nyeri</label>
                    <b-form-input v-model="form_item.pain_scale" type="number" />
                </div>
                <div class="col-lg-4 mt-10">
                    <label class="c-form-label">Intake</label>
                    <b-form-input v-model="form_item.intake" type="number" />
                </div>
                <div class="col-lg-4 mt-10">
                    <label class="c-form-label">Output</label>
                    <b-form-input v-model="form_item.output" type="number" />
                </div>
                <div class="col-lg-4 mt-10">
                    <label class="c-form-label">Diit</label>
                    <b-form-input v-model="form_item.diit" type="number"/>
                </div> -->
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
import { mapState } from 'vuex'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import patientMenu from '@/@core/components/tab-menu/patientMenu.vue';
import ModernLabel from '@/@core/components/label/ModernLabel.vue';
import vSelect from 'vue-select'
import moment from 'moment'
import optionMixin from '@/mixins/optionMixin';

export default {
    name: 'EWS',
    mixins: [optionMixin],
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
            arry_blood_pressure: [ 220, 211, 201, 191, 181, 171, 161, 151, 141, 131, 121, 111, 101, 91, 81, 71, 61, 51],
            arry_pulse: [ 131, 121, 111, 101, 91, 81, 71, 61, 51, 41, 40 ],
            arry_breath: [ 24, 20, 11, 8 ],
            arry_temperature: [ 38.1, 37.1, 36.1, 35.1, 35 ],
            arry_spo2: [ 95, 93, 91 ],
            params: {
                limit: 20,
                offset: 0,
                registration_destination_id: ''
            },
            pageOptions: [20, 50, 100],
            currentPage:1,
            perPage: 20,
            firstRows: 1,
            lastRows: 20,
            isDisabledFilter: false,
            totalRows: 0,

            // OptionsResponse: [
            //     { value: 'V', title: 'Voice (Suara)' },
            //     { value: 'P', title: 'Pain (Kesakitan)' },
            //     { value: 'U', title: 'Unresponsive (Kurang memberi respon)' },
            // ],

            // OptionsBeforeOxygen: [
            //     { value: 0, title: 'Tidak' },
            //     { value: 2, title: 'Ya' },
            // ],

            ews_list: [],
            MainFields: [
                { key: 'observation_date', label: 'Tanggal Monitoring' },
                { key: 'action', label: 'Aksi', thStyle: 'width: 250px' },
            ],
            form_ews: {
                id: '',
                registration_destination_id: '',
                observation_date: moment(new Date()).format('YYYY-MM-DD'),
            },

            ItemFields: [
                { key: 'observation_time', label: 'Waktu' },
                { key: 'employee_nurse_name', label: 'Petugas' },
                { key: 'action', label: 'Aksi', thStyle: 'width: 235px' },
            ],
            form_item: {
                id: '',
                early_warning_score_id: '',
                employee_nurse_id: '',
                observation_time: moment(new Date()).format('HH:mm'),
                aware: '',
                blood_pressure: '',
                pulse: '',
                breathing: '',
                temperature: '',
                oxygen_saturation: '',
                oxygen: '',
                // falling_scale: '',
                // pain_scale: '',
                // intake: '',
                // output: '',
                // diit: '',
            },
        }
    },

    computed: {
        ...mapState({ 
            OptionsOxygen: state => state.soap.vitals.OptionsOxygen,
            OptionsAware: state => state.soap.vitals.OptionsAware,
        }),
    },

    watch: {
        currentPage: {
            handler: function(value) {
                this.firstRows = this.perPage*(value-1)+1
                this.lastRows = this.perPage*(value)+1
                this.params.offset = this.perPage*(value-1)
                this.params.limit = this.perPage
                this.getEws()
            }
        },
        perPage: {
            handler: function(value) {
                this.params.limit = this.perPage
                this.getEws()
            }
        },
    },

    methods: {
        getBloodVariant(index) {
            if (index == 0 || index >= 14) {
                return 'bg-danger-200'
            } else if (index >= 1 && index <= 11) {
                return 'bg-white'
            } else if (index == 12) {
                return 'bg-info-100'
            } else if (index == 13) {
                return 'bg-warning-100'
            }
        },

        getMidBlood(point, index, own_param, next_param) {
            let is_danger = false
            let is_white = false
            let is_info = false
            let is_warning = false

            if (index == 0 || index >= 14) {
                is_danger = true
            } else if (index >= 1 && index <= 11) {
                is_white = true
            } else if (index == 12) {
                is_info = true
            } else if (index == 13) {
                is_warning = true
            }

            if (next_param != undefined) {
                if (point >= next_param && point < own_param && is_danger) {
                    return `3 (${point})`
                } else if (point >= next_param && point < own_param && is_warning) {
                    return `2 (${point})`
                } else if (point >= next_param && point < own_param && is_info) {
                    return `1 (${point})`
                } else if (point >= next_param && point < own_param && is_white) {
                    return `0 (${point})`
                }
                return '-'
            } else {
                if (point < own_param) {
                    return `3 (${point})`
                }
                return '-'
            }
        },
        
        getPulseVariant(index) {
            if (index == 0 || index == 10) {
                return 'bg-danger-200'
            } else if (index >= 1 && index <= 2) {
                return 'bg-warning-100'
            } else if (index >= 3 && index <= 4) {
                return 'bg-info-100'
            } else if (index >= 5 && index <= 9) {
                return 'bg-white'
            }
        },
        
        getMidPulse(point, index, own_param, next_param) {
            let is_danger = false
            let is_white = false
            let is_info = false
            let is_warning = false

            if (index == 0 || index == 10) {
                is_danger = true
            } else if (index >= 1 && index <= 2) {
                is_warning = true
            } else if (index >= 3 && index <= 4) {
                is_info = true
            } else if (index >= 5 && index <= 9) {
                is_white = true
            }

            if (next_param != undefined) {
                if (point >= next_param && point < own_param && is_danger) {
                    return `3 (${point})`
                } else if (point >= next_param && point < own_param && is_warning) {
                    return `2 (${point})`
                } else if (point >= next_param && point < own_param && is_info) {
                    return `1 (${point})`
                } else if (point >= next_param && point < own_param && is_white) {
                    return `0 (${point})`
                }
                return '-'
            } else {
                if (point < own_param) {
                    return `3 (${point})`
                }
                return '-'
            }
        },
        
        getBreathVariant(index) {
            if (index == 0) {
                return 'bg-warning-100'
            } else if (index == 1) {
                return 'bg-white'
            } else if (index == 2) {
                return 'bg-info-100'
            } else if (index == 3) {
                return 'bg-danger-200'
            }
        },
        
        getMidBreath(point, index, own_param, next_param) {
            let is_danger = false
            let is_white = false
            let is_info = false
            let is_warning = false

            if (index == 0) {
                is_warning = true
            } else if (index == 1) {
                is_white = true
            } else if (index == 2) {
                is_info = true
            } else if (index == 3) {
                is_danger = true
            }

            if (next_param != undefined) {
                if (point > next_param && point <= own_param && is_danger) {
                    return `3 (${point})`
                } else if (point > next_param && point <= own_param && is_warning) {
                    return `2 (${point})`
                } else if (point > next_param && point <= own_param && is_info) {
                    return `1 (${point})`
                } else if (point > next_param && point <= own_param && is_white) {
                    return `0 (${point})`
                }
                return '-'
            } else {
                if (point <= own_param) {
                    return `3 (${point})`
                }
                return '-'
            }
        },
                
        getTemperatureVariant(index) {
            if (index == 0) {
                return 'bg-info-100'
            } else if (index >= 1 && index <= 3) {
                return 'bg-white'
            } else if (index == 4) {
                return 'bg-danger-200'
            }
        },
        
        getMidTemperature(point, index, own_param, next_param) {
            let is_danger = false
            let is_white = false
            let is_info = false
            let is_warning = false

            if (index == 0) {
                is_info = true
            } else if (index >= 1 && index <= 3) {
                is_white = true
            } else if (index == 4) {
                is_danger = true
            }

            if (next_param != undefined) {
                if (point >= next_param && point < own_param && is_info) {
                    return `1 (${point})`
                } else if (point >= next_param && point < own_param && is_white) {
                    return `0 (${point})`
                } else if (point >= next_param && point < own_param && is_danger) {
                    return `3 (${point})`
                }
                return '-'
            } else {
                if (point < own_param) {
                    return `3 (${point})`
                }
                return '-'
            }
        },

        getSpo2Variant(index) {
            if (index == 0) {
                return 'bg-info-100'
            } else if (index == 1) {
                return 'bg-warning-100'
            } else if (index == 2) {
                return 'bg-danger-200'
            }
        },
        
        getMidSpo2(point, index, own_param, next_param) {
            let is_danger = false
            let is_white = false
            let is_info = false
            let is_warning = false

            if (index == 0) {
                is_info = true
            } else if (index == 1) {
                is_warning = true
            } else if (index == 2) {
                is_danger = true
            }

            if (next_param != undefined) {
                if (point > next_param && point <= own_param && is_info) {
                    return `1 (${point})`
                } else if (point > next_param && point <= own_param && is_warning) {
                    return `2 (${point})`
                } else if (point > next_param && point <= own_param && is_danger) {
                    return `3 (${point})`
                }
                return '-'
            } else {
                if (point <= own_param) {
                    return `3 (${point})`
                }
                return '-'
            }
        },
        
        openModalEws() {
            this.clearFormEws()
            this.active.modal_main = true
        },

        clearFormEws() {
            this.form_ews.id = ''
            this.form_ews.registration_destination_id = ''
            this.form_ews.observation_date = moment(new Date()).format('YYYY-MM-DD')
        },

        validationEws() {
            if (this.form_ews.id == '') {
                this.form_ews.registration_destination_id = this.reg_des_id
            }

            try {
                this.active.disabled_main = true
                this.$store.dispatch('ews/postPutEws', this.form_ews)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active.modal_main = false
                        this.active.disabled_main = false
                        this.clearFormEws()
                        this.getEws()
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                    this.active.disabled_main = false
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
                this.active.disabled_main = false
            }
        },

        getEws() {
            this.params.registration_destination_id = this.reg_des_id
            try {                
                this.$store.dispatch('ews/getListEws', this.params).then((response) => {
                    this.ews_list = response.rows
                    console.log('this.ews_list: ', this.ews_list);
                    this.totalRows = response.iTotal
                }, error => {
                    console.error('Error while GET ews/getListEws at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET ews/getListEws at: ', error);
            }
        },

        onEditEws(obj) {
            this.openModalEws()
            
            this.form_ews.id = obj.id
            this.form_ews.registration_destination_id = obj.registration_destination_id
            this.form_ews.observation_date = obj.observation_date
        },

        removeEws(obj) {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus EWS tanggal ${obj.observation_date}?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger', okTitle: 'Ya, Hapus', cancelTitle: 'Batal', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    this.$store.dispatch('ews/deleteEws', obj.id)
                    .then(
                        res => {
                            this.notification('Success','CheckIcon','success','You have successfully')
                            this.getEws()
                        }
                    )
                    .catch(({message, debug, response}) => {
                        this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                    })
                }
            })
        },

        openModalItem(ews_id) {
            this.clearFormItem()
            this.active.modal_item = true
            this.form_item.early_warning_score_id = ews_id
        },

        clearFormItem() {
            this.form_item.id = ''
            this.form_item.early_warning_score_id = ''
            this.form_item.employee_nurse_id = ''
            this.form_item.observation_time = moment(new Date()).format('HH:mm')
            this.form_item.aware = ''
            this.form_item.blood_pressure = ''
            this.form_item.pulse = ''
            this.form_item.breathing = ''
            this.form_item.temperature = ''
            this.form_item.oxygen_saturation = ''
            this.form_item.oxygen = ''
            // this.form_item.falling_scale = ''
            // this.form_item.pain_scale = ''
            // this.form_item.intake = ''
            // this.form_item.output = ''
            // this.form_item.diit = ''
        },

        validationItem() {
            if (this.form_item.id == '') {
                this.form_item.employee_nurse_id = this.userData.employee_id
            }

            try {
                this.active.disabled_item = true
                this.$store.dispatch('ews/postPutItemEws', this.form_item)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active.modal_item = false
                        this.active.disabled_item = false
                        this.clearFormItem()
                        this.getEws()
                    } 
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                    this.active.disabled_item = false
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
                this.active.disabled_item = false
            }
        },

        onEditItem(obj) {
            this.openModalItem()
            
            this.form_item.id = obj.id
            this.form_item.early_warning_score_id = obj.early_warning_score_id
            this.form_item.employee_nurse_id = obj.employee_nurse_id
            this.form_item.observation_time = obj.observation_time
            this.form_item.aware = obj.aware
            this.form_item.blood_pressure = obj.blood_pressure
            this.form_item.pulse = obj.pulse
            this.form_item.breathing = obj.breathing
            this.form_item.temperature = obj.temperature
            this.form_item.oxygen_saturation = obj.oxygen_saturation
            this.form_item.oxygen = obj.oxygen
            // this.form_item.falling_scale = obj.falling_scale
            // this.form_item.pain_scale = obj.pain_scale
            // this.form_item.intake = obj.intake
            // this.form_item.output = obj.output
            // this.form_item.diit = obj.diit
        },

        removeItemEws(obj, date) {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus monitoring jam ${obj.observation_time} pada tanggal ${date}?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger', okTitle: 'Ya, Hapus', cancelTitle: 'Batal', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    this.$store.dispatch('ews/deleteItemEws', obj.id)
                    .then(
                        res => {
                            this.notification('Success','CheckIcon','success','You have successfully')
                            this.getEws()
                        }
                    )
                    .catch(({message, debug, response}) => {
                        this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
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
            this.getEws()
        }
    }
}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>