<template>
    <div>
        <div class="row mt-12">
            <div class="col-lg-3 pr-0">
                <b-tabs small content-class="mt-3 fs-12" nav-wrapper-class="fs-13" nav-class="custom-tab-style" class="h-100">
                    <b-tab title="SEMUA" active>
                        <div
                            :style="{
                                'min-height': '350px',
                                'max-height': scrollMaxHeight,
                                'overflow-y': 'scroll'
                            }">
                            <template v-if="cppt_list?.length > 0">
                                <template v-for="(item, index) in cppt_list">
                                    <div @click="isLoading == false ? showThisCppt(item.id) : false" class="card pointer cppt-card" :class="item.id == selected?.id ? 'bg-success-100 border-w3green border border-4' : 'bg-white'" :style="index > 0 ? { 'margin-top': '10px' } : ''">
                                        <div class="card-body p-12">
                                            <div>
                                                <div class="text-uppercase fs-14" style="font-weight: 700;">CPPT {{ item.group_name }}</div>
                                                <div class="">
                                                    <div>{{ item.user_added_name }} ({{ item.user_added_role }})</div>
                                                    <div>{{ localDateTime(item.date_added) }}</div>
                                                    <div>{{ item.health_facility_item_name }}</div>
                                                </div>
                                            </div>
                                            <div class="mt-8">
                                                <template v-if="availableToEdit(item.user_added)">
                                                    <b-button @click="removeCppt(item)" size="sm" variant="danger-500" class="mr-1" v-b-tooltip.hover.top="'Hapus CPPT'">
                                                        <feather-icon icon="TrashIcon" size="12" />
                                                    </b-button>
                                                    <b-button
                                                        @click="triggerCpptModal({
                                                            registration_destination_id: item.registration_destination_id,
                                                            cppt_id: item.id,
                                                        })"
                                                        size="sm"
                                                        variant="warning-400"
                                                        class="mr-1"
                                                        v-b-tooltip.hover.top="'Edit CPPT'"
                                                    >
                                                        <feather-icon icon="EditIcon" size="12" />
                                                    </b-button>
                                                </template>
                                                <!-- <template v-if="userData.user_group.id != doctor_role_id"> -->
                                                <template v-if="userData.user_group.id == jkn_group_id || userData.user_group.id == admin_group_id">
                                                    <router-link :to="`/patient/cppt/print/${item.id}`" target="_blank">
                                                        <b-button size="sm" variant="w3green" class="mr-1" v-b-tooltip.hover.top="'Cetak CPPT'">
                                                            <feather-icon icon="PrinterIcon" size="12" />
                                                        </b-button>
                                                    </router-link>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </template>
                            <div class="text-center fs-11" v-else>
                                <i>Tidak ditemukan CPPT yang telah dibuat</i>
                            </div>
                        </div>
                    </b-tab>
                    <b-tab title="DOKTER">
                        <div
                            :style="{
                                'min-height': '350px',
                                'max-height': scrollMaxHeight,
                                'overflow-y': 'scroll'
                            }">
                            <template v-if="doctor_cppt?.length > 0">
                                <template v-for="(item, index) in doctor_cppt">
                                    <div @click="isLoading == false ? showThisCppt(item.id) : false" class="card pointer cppt-card" :class="item.id == selected?.id ? 'bg-success-100 border-w3green border border-4' : 'bg-white'" :style="index > 0 ? { 'margin-top': '10px' } : ''">
                                        <div class="card-body p-12">
                                            <div>
                                                <div class="text-uppercase fs-14" style="font-weight: 700;">CPPT {{ item.group_name }}</div>
                                                <div class="">
                                                    <div>{{ item.user_added_name }} ({{ item.user_added_role }})</div>
                                                    <div>{{ localDateTime(item.date_added) }}</div>
                                                    <div>{{ item.health_facility_item_name }}</div>
                                                </div>
                                            </div>
                                            <div class="mt-8">
                                                <template v-if="availableToEdit(item.user_added)">
                                                    <b-button @click="removeCppt(item)" size="sm" variant="danger-500" class="mr-1" v-b-tooltip.hover.top="'Hapus CPPT'">
                                                        <feather-icon icon="TrashIcon" size="12" />
                                                    </b-button>
                                                    <b-button
                                                        @click="triggerCpptModal({
                                                            registration_destination_id: item.registration_destination_id,
                                                            cppt_id: item.id,
                                                        })"
                                                        size="sm"
                                                        variant="warning-400"
                                                        class="mr-1"
                                                        v-b-tooltip.hover.top="'Edit CPPT'"
                                                    >
                                                        <feather-icon icon="EditIcon" size="12" />
                                                    </b-button>
                                                </template>
                                                <template v-if="userData.user_group.id == jkn_group_id || userData.user_group.id == admin_group_id">
                                                    <router-link :to="`/patient/cppt/print/${item.id}`" target="_blank">
                                                        <b-button size="sm" variant="w3green" class="mr-1" v-b-tooltip.hover.top="'Cetak CPPT'">
                                                            <feather-icon icon="PrinterIcon" size="12" />
                                                        </b-button>
                                                    </router-link>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </template>
                            <div class="text-center fs-11" v-else>
                                <i>Tidak ditemukan CPPT yang dibuat oleh dokter</i>
                            </div>
                        </div>
                    </b-tab>
                    <b-tab title="PERAWAT">
                        <div
                            :style="{
                                'min-height': '350px',
                                'max-height': scrollMaxHeight,
                                'overflow-y': 'scroll'
                            }">
                            <template v-if="nurse_cppt?.length > 0">
                                <template v-for="(item, index) in nurse_cppt">
                                    <div @click="isLoading == false ? showThisCppt(item.id) : false" class="card pointer cppt-card" :class="item.id == selected?.id ? 'bg-success-100 border-w3green border border-4' : 'bg-white'" :style="index > 0 ? { 'margin-top': '10px' } : ''">
                                        <div class="card-body p-12">
                                            <div>
                                                <div class="text-uppercase fs-14" style="font-weight: 700;">CPPT {{ item.group_name }}</div>
                                                <div class="">
                                                    <div>{{ item.user_added_name }} ({{ item.user_added_role }})</div>
                                                    <div>{{ localDateTime(item.date_added) }}</div>
                                                    <div>{{ item.health_facility_item_name }}</div>
                                                </div>
                                            </div>
                                            <div class="mt-8">
                                                <template v-if="availableToEdit(item.user_added)">
                                                    <b-button @click="removeCppt(item)" size="sm" variant="danger-500" class="mr-1" v-b-tooltip.hover.top="'Hapus CPPT'">
                                                        <feather-icon icon="TrashIcon" size="12" />
                                                    </b-button>
                                                    <b-button
                                                        @click="triggerCpptModal({
                                                            registration_destination_id: item.registration_destination_id,
                                                            cppt_id: item.id,
                                                        })"
                                                        size="sm"
                                                        variant="warning-400"
                                                        class="mr-1"
                                                        v-b-tooltip.hover.top="'Edit CPPT'"
                                                    >
                                                        <feather-icon icon="EditIcon" size="12" />
                                                    </b-button>
                                                </template>
                                                <template v-if="userData.user_group.id == jkn_group_id || userData.user_group.id == admin_group_id">
                                                    <router-link :to="`/patient/cppt/print/${item.id}`" target="_blank">
                                                        <b-button size="sm" variant="w3green" class="mr-1" v-b-tooltip.hover.top="'Cetak CPPT'">
                                                            <feather-icon icon="PrinterIcon" size="12" />
                                                        </b-button>
                                                    </router-link>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </template>
                            <div class="text-center fs-11" v-else>
                                <i>Tidak ditemukan CPPT yang dibuat oleh perawat</i>
                            </div>
                        </div>
                    </b-tab>
                    <b-tab title="LAINNYA">
                        <div
                            :style="{
                                'min-height': '350px',
                                'max-height': scrollMaxHeight,
                                'overflow-y': 'scroll'
                            }">
                            <template v-if="others_cppt?.length > 0">
                                <template v-for="(item, index) in others_cppt">
                                    <div @click="isLoading == false ? showThisCppt(item.id) : false" class="card pointer cppt-card" :class="item.id == selected?.id ? 'bg-success-100 border-w3green border border-4' : 'bg-white'" :style="index > 0 ? { 'margin-top': '10px' } : ''">
                                        <div class="card-body p-12">
                                            <div>
                                                <div class="text-uppercase fs-14" style="font-weight: 700;">CPPT {{ item.group_name }}</div>
                                                <div class="">
                                                    <div>{{ item.user_added_name }} ({{ item.user_added_role }})</div>
                                                    <div>{{ localDateTime(item.date_added) }}</div>
                                                    <div>{{ item.health_facility_item_name }}</div>
                                                </div>
                                            </div>
                                            <div class="mt-8">
                                                <template v-if="availableToEdit(item.user_added)">
                                                    <b-button @click="removeCppt(item)" size="sm" variant="danger-500" class="mr-1" v-b-tooltip.hover.top="'Hapus CPPT'">
                                                        <feather-icon icon="TrashIcon" size="12" />
                                                    </b-button>
                                                    <b-button
                                                        @click="triggerCpptModal({
                                                            registration_destination_id: item.registration_destination_id,
                                                            cppt_id: item.id,
                                                        })"
                                                        size="sm"
                                                        variant="warning-400"
                                                        class="mr-1"
                                                        v-b-tooltip.hover.top="'Edit CPPT'"
                                                    >
                                                        <feather-icon icon="EditIcon" size="12" />
                                                    </b-button>
                                                </template>
                                                <template v-if="userData.user_group.id == jkn_group_id || userData.user_group.id == admin_group_id">
                                                    <router-link :to="`/patient/cppt/print/${item.id}`" target="_blank">
                                                        <b-button size="sm" variant="w3green" class="mr-1" v-b-tooltip.hover.top="'Cetak CPPT'">
                                                            <feather-icon icon="PrinterIcon" size="12" />
                                                        </b-button>
                                                    </router-link>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </template>
                            <div class="text-center fs-11" v-else>
                                <i>Tidak ditemukan CPPT yang dibuat oleh tenaga non-medis</i>
                            </div>
                        </div>
                    </b-tab>
                </b-tabs>
            </div>
            <div class="col-lg-9">
                <template v-if="selected != ''">
                    <ApexaSpinner :visible="isLoading" />
                    <template v-if="!isLoading">
                        <div class="card h-100" id="cppt-content-selected">
                            <div class="card-body py-12 h-100">
                                <div class="row mb-2">
                                    <div class="col-lg-8 mb-2">
                                        <div class="font-weight-bold text-w3green fs-14">
                                            Tanggal CPPT:

                                        </div>

                                        <div class="text-muted mr-2" v-if="!isEditingDate">
                                            {{ localDateTime(selected?.date_added) }}
                                            <b-link
                                                v-if="!isEditingDate"
                                                class="ml-1 small"
                                                @click="startEditDate"
                                            >
                                                (Ubah)
                                            </b-link>
                                        </div>

                                        <div v-else class="d-flex align-items-center">
                                            <b-form-input
                                                type="datetime-local"
                                                v-model="editedDate"
                                                size="sm"
                                                class="mr-2"
                                                style="max-width: 200px;"
                                            />
                                            <b-button
                                                variant="w3green"
                                                size="sm"
                                                class="mr-1"
                                                @click="submitDateChange"
                                                :disabled="isSavingDate"
                                            >
                                                Simpan
                                            </b-button>
                                            <b-button
                                                variant="outline-secondary"
                                                size="sm"
                                                @click="cancelEditDate"
                                            >
                                                Batal
                                            </b-button>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <template v-if="selected?.user_added_role == 'Gizi'">
                                        <div class="col-12 px-8 fs-12">
                                            <div class="fs-14 font-weight-bold bg-w3green py-1 px-8 text-white">GIZI</div>
                                            <div class="row">
                                                <div class="col-lg-4 mt-8">
                                                    <div class="font-weight-bold text-w3green">ASESMEN:</div>
                                                    <div v-html="parsedBody(selected?.nutrition_assessment || '-')"></div>
                                                </div>
                                                <div class="col-lg-4 mt-8">
                                                    <div class="font-weight-bold text-w3green">DIAGNOSIS:</div>
                                                    <div v-html="parsedBody(selected?.nutrition_diagnosis || '-')"></div>
                                                </div>
                                                <div class="col-lg-4 mt-8">
                                                    <div class="font-weight-bold text-w3green">INTERVENSI:</div>
                                                    <div v-html="parsedBody(selected?.nutrition_intervention || '-')"></div>
                                                </div>
                                                <div class="col-lg-4 mt-8 mt-lg-12">
                                                    <div class="font-weight-bold text-w3green">MONITORING & EVALUASI:</div>
                                                    <div v-html="parsedBody(selected?.nutrition_monitoring_evaluation || '-')"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                    <!-- <template v-if="selected?.user_added_role == 'Super User' || selected?.user_added_role == 'Doctor' || selected?.user_added_role == 'Nurse'"> -->
                                    <template v-else>
                                        <div class="col-lg-4 px-8 fs-12">
                                            <div>
                                                <div class="fs-14 font-weight-bold bg-w3green py-1 px-8 text-white">SUBJECTIVE</div>
                                                <section class="mt-8">
                                                    <template v-if="selected?.user_added_role != 'Super User' && selected?.user_added_role != 'Doctor'">
                                                        <div class="font-weight-bold text-w3green">ASESMEN SUBJECTIVE:</div>
                                                        <div v-html="parsedBody(selected?.subjective || '-')" class="fs-12"></div>
                                                        <div class="font-weight-bold text-w3green mt-8">ASESMEN OBJECTIVE:</div>
                                                        <div v-html="parsedBody(selected?.nursing_objective || '-')" class="fs-12"></div>
                                                    </template>
                                                    <template v-else>
                                                        <div v-html="parsedBody(selected?.subjective || '-')" class="fs-12"></div>
                                                    </template>
                                                </section>
                                            </div>
                                            <div v-if="selected?.user_added_role == 'Super User' || selected?.user_added_role == 'Doctor'" class="mt-16">
                                                <div class="fs-14 font-weight-bold bg-w3green py-1 px-8 text-white">ST GENERALIS</div>
                                                <section class="mt-8">
                                                    <div class="font-weight-bold text-w3green">STATUS LOKALIS:</div>
                                                    <div v-html="parsedBody(selected?.localist_status || '-')" class="fs-12"></div>
                                                </section>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 px-2 fs-12">
                                            <div class="fs-14 font-weight-bold bg-w3green py-1 px-8 text-white">OBJECTIVE</div>
                                            <section class="mt-8 ">
                                                <div class="font-weight-bold text-w3green">TTV:</div>
                                                <div class="row mx-0">
                                                    <div class="col-4 px-0">
                                                        <table>
                                                            <tr>
                                                                <td>Sys</td>
                                                                <td class="px-1">:</td>
                                                                <td>
                                                                    {{ selected?.systolic_blood_pressure || '-' }}
                                                                    <span class="fs-9">mmHg</span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Dia</td>
                                                                <td class="px-1">:</td>
                                                                <td>
                                                                    {{ selected?.diastolic_blood_pressure || '-' }}
                                                                    <span class="fs-9">mmHg</span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>HR</td>
                                                                <td class="px-1">:</td>
                                                                <td>
                                                                    {{ selected?.pulse_frequency || '-' }}
                                                                    <span class="fs-9">x/mnt</span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>RR</td>
                                                                <td class="px-1">:</td>
                                                                <td>
                                                                    {{ selected?.breath_frequency || '-' }}
                                                                    <span class="fs-9">x/mnt</span>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                    <div class="col-4 px-0">
                                                        <table>
                                                            <tr>
                                                                <td>SpO2</td>
                                                                <td class="px-1">:</td>
                                                                <td>
                                                                    {{ selected?.oximetry_frequency || '-' }}
                                                                    <span class="fs-9">%</span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>T</td>
                                                                <td class="px-1">:</td>
                                                                <td>
                                                                    {{ selected?.temperature || '-' }}
                                                                    <span class="fs-9">ᵒC</span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>O2</td>
                                                                <td class="px-1">:</td>
                                                                <td>{{ getOxygenPoint(selected?.oxygen_type_id || '-') }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>AVPU</td>
                                                                <td class="px-1">:</td>
                                                                <td>{{ getAwarePoint(selected?.aware) }}</td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                    <div class="col-4 px-0">
                                                        <table>
                                                            <tr>
                                                                <td>GCS</td>
                                                                <td class="px-1">:</td>
                                                                <td>{{ getGcsScore({eye: selected?.eye_score, verbal: selected?.verbal_score, motor: selected?.motor_score}) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Eye</td>
                                                                <td class="px-1">:</td>
                                                                <td>{{ getGcsEyePoint(selected?.eye_score || '-') }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Vrb</td>
                                                                <td class="px-1">:</td>
                                                                <td>{{ getGcsVerbalPoint(selected?.verbal_score || '-') }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Mtr</td>
                                                                <td class="px-1">:</td>
                                                                <td>{{ getGcsMotoricPoint(selected?.motor_score || '-') }}</td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                                <table class="mt-8">
                                                    <tr>
                                                        <td>Warna kulit</td>
                                                        <td class="px-1">:</td>
                                                        <td> {{ getColorSkinName(selected?.skin_color || '-') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Perilaku</td>
                                                        <td class="px-1">:</td>
                                                        <td>{{ getBehaviorName(selected?.behavior || '-') }}</td>
                                                    </tr>
                                                </table>
                                            </section>
                                            <section v-if="selected?.meaningful?.length > 0" class="mt-12">
                                                <div class="font-weight-bold text-w3green">ST.GENERALIS BERMAKNA:</div>
                                                <table>
                                                    <template v-for="item in selected.meaningful">
                                                        <tr>
                                                            <td>{{ item.key }}</td>
                                                            <td class="px-1">:</td>
                                                            <td>{{ item.value }}</td>
                                                        </tr>
                                                    </template>
                                                </table>
                                            </section>
                                        </div>
                                        <template v-if="selected?.user_added_role == 'Super User' || selected?.user_added_role == 'Doctor'">
                                            <div class="col-lg-4 px-2 fs-12 mt-10 mt-lg-0">
                                                <div class="fs-14 font-weight-bold bg-w3green py-1 px-8 text-white">ASSESSMENT</div>
                                                <section class="mt-8">
                                                    <div class="font-weight-bold text-w3green">DIAGNOSIS:</div>
                                                    <div v-if="selected?.icd10?.length > 0" class="pl-16">
                                                        <li v-for="(list, index) in selected.icd10">{{ list.icd10_code }} {{ list.icd10_name }}</li>
                                                    </div>
                                                    <div v-else class="text-italic">-</div>
                                                </section>
                                                <section class="mt-12">
                                                    <div class="font-weight-bold text-w3green">CATATAN ICD10:</div>
                                                    <div v-html="parsedBody(selected?.icd10_notes || '-')"></div>
                                                </section>
                                                <section class="mt-12">
                                                    <div class="font-weight-bold text-w3green">NON-ICD10:</div>
                                                    <div v-if="selected?.non_icd10?.length > 0" class="pl-16">
                                                        <li v-for="(list, index) in selected.non_icd10">{{ list }}</li>
                                                    </div>
                                                    <div v-else class="text-italic">-</div>
                                                </section>
                                            </div>
                                            <div class="col-lg-12 px-8 mt-10">
                                                <div class="col-12 px-0">
                                                    <div class="fs-14 font-weight-bold bg-w3green py-1 px-8 text-white">PLAN</div>
                                                </div>
                                                <div class="row fs-12">
                                                    <div class="col-lg-4">
                                                        <section class="mt-8">
                                                            <div class="font-weight-bold text-w3green">PROSEDUR:</div>
                                                            <div v-if="selected?.transaction_doctor?.length > 0" class="pl-16">
                                                                <li v-for="list in selected.transaction_doctor">{{ list.costing_name }}</li>
                                                            </div>
                                                            <div v-else>-</div>
                                                        </section>
                                                        <section class="mt-12">
                                                            <div class="font-weight-bold text-w3green">ICD9CM:</div>
                                                            <div>
                                                                <div v-if="selected?.icd9cm?.length > 0" class="pl-16">
                                                                    <li v-for="list in selected.icd9cm">{{ list.icd9cm_code }} {{ list.icd9cm_name }}</li>
                                                                </div>
                                                                <div v-else>-</div>
                                                            </div>
                                                            <div v-html="parsedBody(selected?.therapy_plan || '-')" class="mt-1"></div>
                                                        </section>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <section class="mt-8">
                                                            <div class="font-weight-bold text-w3green">LABORATORIUM:</div>
                                                            <template v-if="selected?.laboratory_request?.length > 0">
                                                                <template v-for="(il, i) in selected.laboratory_request">
                                                                    <div class="mb-12">
                                                                        <!-- <div class="font-weight-bold">{{il.no_request}} [{{ il.queue }}]</div> -->
                                                                        <div class="font-weight-bold">{{localDateTime(il.date_request)}}</div>
                                                                        <div class="pl-16">
                                                                            <li v-for="(list, j) in il.laboratory_request_items">
                                                                                <template v-if="list.result?.length > 0">
                                                                                    <feather-icon icon="EyeIcon" @click="triggerLabResult({ alias_name: patient.alias_name, name: patient.name, result: list.result, costing_name: list.laboratory_package_name, laboratory_request_id: list.laboratory_request_id })" color="blue" v-b-tooltip.hover.left="`Lihat hasil ${list.laboratory_package_name}`"/>
                                                                                </template>
                                                                                <template v-else>
                                                                                    <img src="./../../../../@core/assets/icons/hourglass.svg" alt="" width="14px" v-b-tooltip.hover.left="`${list.laboratory_package_name} telah di order, belum terbit hasil`">
                                                                                </template>
                                                                                {{ list.laboratory_package_name }}
                                                                            </li>
                                                                        </div>
                                                                    </div>
                                                                </template>
                                                            </template>
                                                            <div v-else>-</div>
                                                        </section>
                                                        <section class="mt-12">
                                                            <div class="font-weight-bold text-w3green">RADIOLOGI:</div>
                                                            <template v-if="selected?.radiology_request?.length > 0">
                                                                <template v-for="(ir, i) in selected.radiology_request">
                                                                    <div class="mb-12">
                                                                        <!-- <div class="font-weight-bold">{{ir.no_request}} [{{ ir.queue }}]</div> -->
                                                                        <div class="font-weight-bold">{{localDateTime(ir.date_request)}}</div>
                                                                        <div class="pl-16">
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
                                                                    </div>
                                                                </template>
                                                            </template>
                                                            <div v-else class="font-weight-bold">-</div>
                                                        </section>
                                                        <section class="mt-12">
                                                            <div class="font-weight-bold text-w3green">FISIOTERAPI:</div>
                                                            <template v-if="selected?.physiotherapy_request?.length > 0">
                                                                <template v-for="(phy, i) in selected.physiotherapy_request">
                                                                    <div class="mb-12">
                                                                        <!-- <div class="font-weight-bold">{{phy.no_request}} [{{ phy.queue }}]</div> -->
                                                                        <div class="font-weight-bold">{{localDateTime(phy.date_request)}}</div>
                                                                        <div class="pl-16">
                                                                            <li v-for="(list, j) in phy.physiotherapy_request_items">
                                                                                {{ list.physiotherapy_package_name }}
                                                                            </li>
                                                                        </div>
                                                                    </div>
                                                                </template>
                                                            </template>
                                                            <div v-else class="font-weight-bold">-</div>
                                                        </section>
                                                    </div>
                                                    <div class="col-lg-4 pl-8">
                                                        <!-- seharusnya di sini ada 2 atau 3 elemen pembungkus buat ngondisiin waiting_for_approval -->
                                                        <template v-if="selected?.waiting_for_approval" >
                                                            <div class="h-100 rounded mt-1" style="border: 1px solid #03a972;">
                                                                <div class="p-1 font-italic fs-12 text-center bg-w3green text-white rounded-top">Tata laksana ini menunggu persetujuan DPJP</div>
                                                                <section class="p-1">
                                                                    <div class="font-weight-bold text-w3green">RESEP:</div>
                                                                    <!-- <div class="font-weight-bold">
                                                                        {{localDateTime(selected?.waiting_for_approval?.date_added)}} (Kardeks)
                                                                    </div> -->
                                                                    <div v-for="(list, idx_p) in selected?.waiting_for_approval?.form_prescription_parent" class="mt-1">
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
                                                                        <div v-if="selected?.waiting_for_approval?.form_prescription" class="mt-1 pl-16">
                                                                            <li v-for="unit in selected.waiting_for_approval.form_prescription" v-if="list.id == unit.prescription_item_type_id">
                                                                                <b-badge v-if="unit?.status" :variant="getKardexItemStatusVariant(unit?.status)" class="text-uppercase mr-1">{{ getKardexItemStatusName(unit?.status) }}</b-badge>
                                                                                {{ unit?.pharmacy_catalog_name }}, {{ unit?.dose }} {{ getDynamicOptName(unit?.dose_unit_id, signature_units) }}
                                                                                <span v-if="unit?.prescription_item_type_id == 1">
                                                                                    TIAP {{ getDynamicOptName(unit?.signatura_times_id, signature_time)}}
                                                                                    {{ getDynamicOptName(unit?.administer_id, catalog_instruction) }}
                                                                                </span>
                                                                            </li>
                                                                        </div>
                                                                    </div>
                                                                </section>
                                                                <section class="mt-8 p-1" v-if="selected?.waiting_for_approval?.kardex_non_pharmacological">
                                                                    <div class="font-weight-bold text-w3green">NON-FARMAKOLOGI:</div>
                                                                    <div class="pl-16">
                                                                        <li v-for="inf in selected?.waiting_for_approval?.kardex_non_pharmacological">
                                                                            <feather-icon
                                                                                :icon="isWaitingApproval(inf) ? 'AlertCircleIcon' : 'UserCheckIcon'"
                                                                                :color="isWaitingApproval(inf) ? 'orange' : 'green'"
                                                                                v-b-tooltip.hover.left="isWaitingApproval(inf) ? `Tata laksana ini menunggu persetujuan DPJP` : `Disetujui DPJP`"
                                                                                size="12"
                                                                                class="mr-1"
                                                                            />
                                                                            <span v-html="parsedBody(inf.name || '-')"></span>
                                                                        </li>
                                                                    </div>
                                                                </section>
                                                            </div>

                                                            <section class="mt-8" v-if="selected?.plan">
                                                                <div class="font-weight-bold text-w3green">NON-FARMAKOLOGI:</div>
                                                                <div v-html="parsedBody(selected?.plan || '-')"></div>
                                                            </section>
                                                        </template>

                                                        <div v-else>
                                                            <section class="mt-8">
                                                                <div class="font-weight-bold text-w3green">RESEP:</div>
                                                                <template v-if="selected?.prescription?.length > 0">
                                                                    <div v-for="(ipre, i) in selected.prescription" class="mt-8">
                                                                        <div class="font-weight-bold">
                                                                            <feather-icon
                                                                                v-if="isWaitingApproval({ is_suggestion: ipre.is_suggestion, is_approve: ipre.is_approve })"
                                                                                icon="AlertCircleIcon"
                                                                                color="orange"
                                                                                v-b-tooltip.hover.left="`Tata laksana ini menunggu persetujuan DPJP`"
                                                                                size="12"
                                                                                class="mr-1"
                                                                            />
                                                                            <template v-else>
                                                                                <feather-icon v-if="ipre?.flag == 1" icon="CheckSquareIcon" color="green" v-b-tooltip.hover.left="`Diproses oleh farmasi`"/>
                                                                                <img v-else-if="ipre?.flag == 0" src="./../../../../@core/assets/icons/hourglass.svg" alt="" width="14px" v-b-tooltip.hover.left="`Berhasil diorder`">
                                                                            </template>
                                                                            {{localDateTime(ipre.date_added)}} ({{ isHomeRecipe(ipre) ? 'Resep Pulang' : 'Kardeks' }})
                                                                        </div>
                                                                        <div v-for="(list, idx_p) in ipre.form_prescription_parent" class="mt-1">
                                                                            <template v-if="isHomeRecipe(ipre)">
                                                                                <div :class="getPrescParentVariant(list.id)" class="px-12 py-1">
                                                                                    <div class="font-weight-bold">{{ getPrescParentName(list.id) }}</div>
                                                                                    <div v-if="list.id != 1" class="fs-12 text-italic" style="font-weight: 400">
                                                                                        <span>Jumlah: {{ list.value | number}}, </span>
                                                                                        <span>{{ list.used }}</span>
                                                                                    </div>
                                                                                </div>
                                                                                <div v-if="ipre?.form_prescription" class="mt-1 pl-16">
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
                                                                                <div v-if="ipre?.form_prescription" class="mt-1 pl-16">
                                                                                    <li v-for="unit in ipre.form_prescription" v-if="list.id == unit.prescription_item_type_id" :class="getKardexItemVariant(unit, ipre.suggestion_status, ipre.is_approve)">
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
                                                            </section>
                                                            <section class="mt-8">
                                                                <div class="font-weight-bold text-w3green">NON-FARMAKOLOGI:</div>
                                                                <template>
                                                                    <div class="pl-16">
                                                                        <li v-for="inf in selected?.kardex_non_pharmacological">
                                                                            <feather-icon
                                                                                :icon="isWaitingApproval(inf) ? 'AlertCircleIcon' : 'UserCheckIcon'"
                                                                                :color="isWaitingApproval(inf) ? 'orange' : 'green'"
                                                                                v-b-tooltip.hover.left="isWaitingApproval(inf) ? `Tata laksana ini menunggu persetujuan DPJP` : `Disetujui DPJP`"
                                                                                size="12"
                                                                                class="mr-1"
                                                                            />
                                                                            <span v-html="parsedBody(inf.name || '-')"></span>
                                                                        </li>
                                                                    </div>
                                                                    <div v-if="selected?.plan" v-html="parsedBody(selected?.plan || '-')"></div>
                                                                </template>
                                                            </section>
                                                        </div>

                                                        <section class="mt-8" v-if="selected?.pharmacological_plan">
                                                            <div class="font-weight-bold text-w3green">FARMAKOLOGI:</div>
                                                            <div v-html="parsedBody(selected?.pharmacological_plan || '-')"></div>
                                                        </section>
                                                    </div>

                                                </div>
                                            </div>
                                        </template>
                                        <!-- <template v-else-if="selected?.user_added_role == 'Nurse'"> -->
                                        <template v-else-if="selected?.user_added_role != 'Super User' || selected?.user_added_role != 'Doctor'">
                                            <div class="col-lg-4 px-2 fs-12 mt-10 mt-lg-0">
                                                <div class="fs-14 font-weight-bold bg-w3green py-1 px-8 text-white">ASSESSMENT</div>
                                                <section class="mt-8">
                                                    <div class="font-weight-bold text-w3green">DIAGNOSIS:</div>
                                                    <div v-html="parsedBody(selected?.nursing_diagnose || '-')" class="fs-12"></div>
                                                </section>
                                            </div>
                                            <div class="col-lg-4 px-2 fs-12 mt-10">
                                                <div class="fs-14 font-weight-bold bg-w3green py-1 px-8 text-white">PLAN</div>
                                                <div v-html="parsedBody(selected?.nursing_plan || '-')" class="fs-12"></div>
                                            </div>
                                        </template>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </template>
                <div v-else class="card d-flex justify-content-center align-items-center h-100">
                    <ApexaSpinner :visible="isLoading" />
                    <template v-if="!isLoading">
                        <i class="fs-12">Silakan pilih CPPT yang ingin dilihat dari daftar di samping</i>
                    </template>
                </div>
            </div>
        </div>

        <LabResultModalView ref="labModalResult"/>
        <RadResultModalView ref="radModalResult"/>
        <AddingCpptView ref="childCpptRef" @refreshAssessment="forWardAssesment" @prescChildTemplate="forWardPrescTemplate"/>
    </div>
</template>

<script>
import moment from "moment"
import optionMixin from '@/mixins/optionMixin';
import dateMixin from '@/mixins/dateMixin';
import RadResultModalView from '../medical_support/RadResultModalView.vue';
import LabResultModalView from '../medical_support/LabResultModalView.vue';
import AddingCpptView from '@/views/pages/patient/cppt/AddingCpptView.vue';
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'

export default {
    mixins: [optionMixin, dateMixin],
    components: {
        RadResultModalView,
        LabResultModalView,
        AddingCpptView
    },

    data() {
        return {
            contentHeight: 350,
            userData: JSON.parse(localStorage.getItem('userData')),
            admin_id: 'd77142c4-4777-47d8-bd0c-fd7dfd134af4',
            doctor_role_id: 'efb3fdff-fbe3-4954-a391-3729e544b62a',
            nurse_role_id: '29644626-5b54-4d54-ab69-22146a631277',
            admin_group_id: 'ddad321c-a9d8-430c-95f0-de38d20834a1',
            jkn_group_id: 'a4c04d8d-12d8-4c54-afca-69de6bd5f62d',
            selected: '',
            isEditingDate: false,
            isSavingDate: false,
            editedDate: null,
            generalis_key: [
                { default: 1, label: 'Kepala', key: "head", name: 'getHead' },
                { default: 1, label: 'Warna Rambut', key: "head_hair_color", name: 'getHairColor' },
                { default: 1, label: 'Kondisi Rambut', key: "head_hair_condition", name: 'getHairCondition' },
                { default: 1, label: 'Conjunctiva', key: "eye_conjunctive", name: 'getEyeConjunctive' },
                { default: 1, label: 'Sklera', key: "eye_sclera", name: 'getEyeSclera' },
                { default: 1, label: 'Pupil', key: "eye_pupil", name: 'getEyePupil' },
                { default: 1, label: 'Reflek Cahaya', key: "eye_light_reflex", name: 'getEyeReflect' },
                { default: 1, label: 'Mukosa', key: "mouth_mucosa", name: 'getMouthMucosa' },
                { default: 1, label: 'Perubahan Warna', key: "mouth_discoloration", name: 'getMouthDiscoloration' },
                { default: 1, label: 'Lidah', key: "mouth_tongue", name: 'getMouthTongue' },
                { default: 1, label: 'Karies', key: "mouth_caries", name: 'getMouthCaries' },
                { default: 1, label: 'KGB', key: "neck_kgb", name: 'getNeckKGB' },
                { default: 1, label: 'Posisi Trakea', key: "neck_trachea_position", name: 'getNeckTracheaPosition' },
                { default: 1, label: 'Massa', key: "neck_massa", name: 'getNeckMassa' },
                { default: 1, label: 'JVP', key: "neck_jvp", name: 'getNeckJVP' },
                { default: 1, label: 'Inspeksi Dada', key: "thorax_insp_chest", name: 'getInspectionChest' },
                { default: 1, label: 'Retraksi', key: "thorax_insp_retraction", name: 'getInspectionRetractiont' },
                { default: 1, label: 'Palpasi Dada', key: "thorax_palp_chest", name: 'getPalpationChest' },
                { default: 1, label: 'Fremitus', key: "thorax_palp_fremitus", name: 'getPalpationFremitus' },
                { default: 1, label: 'Krepitasi', key: "thorax_palp_crepitation", name: 'getPalpationKrepitasi' },
                { default: 1, label: 'Pulmo Vasikuler', key: "thorax_ausk_pul_vasiculer", name: 'getAuskultationPulmoVasikuler' },
                { default: 1, label: 'Pulmo Rhonki', key: "thorax_ausk_pul_rhonki", name: 'getAuskultationPulmoRhonki' },
                { default: 1, label: 'Pulmo Wheezing', key: "thorax_ausk_pul_wheezing", name: 'getAuskultationPulmoWheezing' },
                { default: 1, label: 'Bunyi Jantung', key: "thorax_ausk_cor_heart_sound", name: 'getAuskultationCORBJ' },
                { default: 1, label: 'Murmur', key: "thorax_ausk_cor_murmur", name: 'getAuskultationCORMurmur' },
                { default: 1, label: 'Gallop', key: "thorax_ausk_cor_gallop", name: 'getAuskultationCORGallop' },
                { default: 1, label: 'Supel', key: "abdomen_supel", name: 'getAbdomenSupel' },
                { default: 1, label: 'Bising Usus', key: "abdomen_bowel_sounds", name: 'getAbdomenBising' },
                { default: 1, label: 'Nyeri Tekan', key: "abdomen_tenderness", name: 'getAbdomenPress' },
                { default: 1, label: 'Hepar & Lien', key: "abdomen_hepar_lien", name: 'getAbdomenHepar' },
                { default: 1, label: 'Akral', key: "extremity_acral", name: 'getEkstremitasAkral' },
                { default: 1, label: 'extremity_crt', key: "extremity_crt", name: 'getEkstremitasCRT' },
                { default: 1, label: 'extremity_edema', key: "extremity_edema", name: 'getEkstremitasEdema' },
            ],
            isLoading: false,
        }
    },

    props: {
        patient: {
            type: Object,
        },
        cppt_list: {
            type: Array,
        },
        catalog_list: {
            type: Array,
        },
        stock_list: {
            type: Array,
        },
        presc_template: {
            type: Array,
        },
        anamn_template: {
            type: Array,
        },
        signature_time: {
            type: Array,
        },
        signature_dose: {
            type: Array,
        },
        signature_units: {
            type: Array,
        },
        catalog_instruction: {
            type: Array,
        },
    },

    watch: {
        'cppt_list': async function(val){
            if (val) {
                let actvcpt;
                try {
                    actvcpt = JSON.parse(localStorage.getItem('actvcpt'));
                } catch (e) {
                    console.error('Error parsing actvcpt:', e);
                    actvcpt = null;
                }

                const foundItem = this.cppt_list.find(item => item.id == actvcpt);
                // foundItem ? this.showThisCppt(foundItem) : this.selected = '';
                if (foundItem) {
                    await this.showThisCppt(foundItem.id)
                } else {
                    this.selected = ''
                    this.meaningful = []
                }
            }
        },

        'selected'() {
            this.$nextTick(this.updateMaxHeight);
        }
    },

    filters:{
        number(value) {
            return Number(value)
        },
    },

    methods: {
        startEditDate() {
            this.editedDate = this.selected?.date_added
                ? moment(this.selected.date_added).format('YYYY-MM-DD HH:mm:ss')
                : moment().format('YYYY-MM-DD');
            this.isEditingDate = true;
        },
        cancelEditDate() {
            this.isEditingDate = false;
            this.editedDate = null;
        },
        async submitDateChange() {
            if (!this.editedDate) return;
            this.isSavingDate = true;
            try {
                const payload = {
                    cppt_id: this.selected.id,
                    updated_at: moment(this.editedDate).format('YYYY-MM-DD HH:mm:ss'),
                };

                const res = await this.$store.dispatch('cppt/editCPPTdate', {
                    cppt_id: this.selected.id,
                    updated_at: moment(this.editedDate, 'YYYY-MM-DDTHH:mm').format('YYYY-MM-DD HH:mm:ss')
                });

                if (res?.status == 200) {
                    this.selected.updated_at = payload.updated_at;
                    this.isEditingDate = false;
                    this.notification('Success','CheckIcon','success','Tanggal berhasil diubah')
                } else {
                    this.notification('Error','ExclamationIcon','danger',' Gagal mengubah tanggal')
                }
            } catch (err) {
                console.error(err);
                this.notification('Error','ExclamationIcon','danger','Terjadi kesalahan saat mengubah tanggal')
            } finally {
                this.isSavingDate = false;
            }
        },
        getKardexItemVariant(obj, suggestion_status, is_approve) {
            // console.log('masukL ', {obj, suggestion_status, is_approve})
            if ((obj?.is_suggestion == 1 && suggestion_status == 2 && is_approve == 0) || (obj.is_reject == 1 && is_approve == 1)) return 'bg-ax-danger-300 text-white'
            return ''
        },

        isWaitingApproval(obj) {
            if (obj.is_suggestion == 1 && obj.is_approve == 0) return true
            return false
        },

        isHomeRecipe(obj) {
            if (obj?.type_prescription_id == 2) return true
            return false
        },

        availableToEdit(user_added){
            if (this.patient.soap_registration_destination_id != this.patient.registration_destination_id) return false
            if (this.userData.employee_id == this.admin_id) return true
            if (user_added == this.userData.user_id) return true
            return false
        },

        async showThisCppt(cppt_id) {
            localStorage.setItem(`actvcpt`, JSON.stringify(cppt_id))
            try {
                this.isLoading = true
                await this.$store.dispatch('cppt/getByCpptId', cppt_id)
                .then(async response => {
                    let res_data = response[0] || null
                    const temp_meaningful = await this.manageMeaningfulGeneralis(res_data) // nentuin St.generalis yang bermakna aja
                    const temp_waiting = await this.manageWaitingApprove(res_data) // ngolah data biar dapetin tata laksana yang belom di acc

                    this.selected = {
                        ...res_data,
                        meaningful: temp_meaningful,
                        waiting_for_approval: temp_waiting
                    }
                    // console.log('this.selected: ', this.selected);
                    // this.selected = {...res_data} // barangkali mau balikin
                }, error => {
                    console.error('Error while GET cppt/getByCpptId at: ', error);
                })
            } catch (error) {
                console.error('Error catch at: ', error)
            } finally {
                this.isLoading = false
            }
        },

        async manageMeaningfulGeneralis(obj) {
            let all_key = this.generalis_key
            let temp_arry = []

            for (let i = 0; i < all_key.length; i++) {
                const el = all_key[i];
                if (obj[el.key] && obj[el.key] != el.default ) {
                    const func = this[el.name]
                    const result = func(obj[el.key])
                    temp_arry.push({
                        key: el.label,
                        value: result
                    });
                }
            }
            return temp_arry
        },

        async manageWaitingApprove(obj) {
            const user_group_name = obj?.user_added_role.toLowerCase()
            const temp_presc = obj?.prescription || []
            if (user_group_name != 'doctor' || temp_presc?.length != 1) return null

            const selected_presc = temp_presc[0]
            if (this.isHomeRecipe(selected_presc)) return null

            const presc_item = selected_presc?.form_prescription
            if (!this.isWaitingApproval(selected_presc) || presc_item?.length == 0) return null

            const kardex_id = presc_item[0].kardex_id
            const presc_parent = selected_presc?.form_prescription_parent
            const non_pharma_same_kardex = obj?.kardex_non_pharmacological?.filter(item => item.kardex_id == kardex_id) || []
            const groupingObj = {
                date_added: temp_presc.user_added,
                form_prescription: [...presc_item],
                form_prescription_parent: [...presc_parent],
                kardex_non_pharmacological: [...non_pharma_same_kardex],
            }
            return groupingObj
        },

        triggerRadResult(message) {
            this.$refs.radModalResult.seeRadiologyResult(message);
        },

        triggerLabResult(message) {
            this.$refs.labModalResult.seeLaboratoryResult(message);
        },

        triggerCpptModal(obj) {
            const message = {
                gate: 'edit_gate',
                registration_destination_id: obj.registration_destination_id,
                cppt_id: obj.cppt_id,
                regData: '',
                costing_list: '',
                lab_list: '',
                rad_list: '',
                phy_list: '',
                catalog_list: this.catalog_list,
                stock_list: this.stock_list,
                presc_template: this.presc_template,
                anamn_template: this.anamn_template,
                signature_time: this.signature_time,
                signature_dose: this.signature_dose,
                signature_units: this.signature_units,
                catalog_instruction: this.catalog_instruction,
                triggerFrom: null
            }
            this.$refs.childCpptRef.parentTrigger(message);
        },

        removeCppt(obj) {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus CPPT ${obj.user_added_name} ${obj.date_added}?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger', okTitle: 'Ya, Hapus', cancelTitle: 'Batal', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    this.$store.dispatch('cppt/removeCPPT', obj.id)
                    .then(
                        res => {
                            this.notification('Success','CheckIcon','success','You have successfully')
                            this.forWardAssesment()
                        }
                    )
                    .catch(({message, debug, response}) => {
                        this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                    })
                }
            })
        },

        forWardAssesment() {
            let message = 'initial dulu'
            this.$emit("refreshAssessment", message);
        },

        forWardPrescTemplate(data) {
            // console.log('masuk ke forward presc: ', data);
            this.$emit("prescChildTemplate", data);
        },

        updateMaxHeight() {
            // console.log('hit di awal');
            const contentEl = document.getElementById('cppt-content-selected');
            if (contentEl) {
                this.contentHeight = contentEl.clientHeight - 45;
                // console.log('masuk ke blok if:', this.contentHeight);
            }
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

        notification(a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                employee: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },

        parsedBody(text){
            return String(text || '').replace(/\n/g, "<br>");
        },
    },

    computed: {
        doctor_cppt(){
            if(this.cppt_list.length > 0) return this.cppt_list.filter(group => group.group_id == this.doctor_role_id)
            return []
        },

        nurse_cppt(){
            if(this.cppt_list.length > 0) return this.cppt_list.filter(group => group.group_id == this.nurse_role_id)
            return []
        },

        others_cppt(){
            if(this.cppt_list.length > 0) return this.cppt_list.filter(group => group.group_id != this.doctor_role_id && group.group_id != this.nurse_role_id)
            return []
        },

        scrollMaxHeight() {
            return `${this.contentHeight}px`;
        },
    },

    mounted() {
        this.updateMaxHeight();
        window.addEventListener('resize', this.updateMaxHeight);
    },

    beforeDestroy() {
        window.removeEventListener('resize', this.updateMaxHeight);
    },
}
</script>

<style scoped>
    ::v-deep(.custom-tab-style .nav-link) {
        padding: 8px 10px 6px !important;
    }

    table.custom-border tr td {
        border: 0px;
        padding: 2px;
    }

    .cppt-card:hover {
        border: 1px solid #03a972;
    }
</style>