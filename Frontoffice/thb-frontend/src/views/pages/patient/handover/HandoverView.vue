<template>
    <div>
        <b-modal v-model="active.main_modal" scrollable size="xl">
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    HANDOVER PASIEN
                    <span class="text-primary">{{ temp_header.alias_name }}. {{ temp_header.name }} | {{localDate(temp_header.birth_date)}} ({{ parsedAge(temp_header.birth_date) }}) | {{ temp_header.norm }}</span>
                </div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>
             <ModernLabel class="mb-1" :label="`CCPT ditambahkan pada: ${localDateTime(latestCppt?.date_added || '--')}`" variant="w3green" />
            <div class="row p-2">
                <div class="col-6 row">
                    <template v-if="latestCppt?.user_added_role == 'Gizi'">
                        <div class="col-12 px-8 fs-12">
                            <div class="fs-14 font-weight-bold bg-w3green py-1 px-8 text-white">GIZI</div>
                            <div class="row">
                                <div class="col-lg-4 mt-8">
                                    <div class="font-weight-bold text-w3green">ASESMEN:</div>
                                    <div v-html="parsedBody(latestCppt?.nutrition_assessment || '-')"></div>
                                </div>
                                <div class="col-lg-4 mt-8">
                                    <div class="font-weight-bold text-w3green">DIAGNOSIS:</div>
                                    <div v-html="parsedBody(latestCppt?.nutrition_diagnosis || '-')"></div>
                                </div>
                                <div class="col-lg-4 mt-8">
                                    <div class="font-weight-bold text-w3green">INTERVENSI:</div>
                                    <div v-html="parsedBody(latestCppt?.nutrition_intervention || '-')"></div>
                                </div>
                                <div class="col-lg-4 mt-8 mt-lg-12">
                                    <div class="font-weight-bold text-w3green">MONITORING & EVALUASI:</div>
                                    <div v-html="parsedBody(latestCppt?.nutrition_monitoring_evaluation || '-')"></div>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template v-else>
                        <div class="col-lg-4 px-8 fs-12">
                            <div>
                                <div class="fs-14 font-weight-bold bg-w3green py-1 px-8 text-white">SUBJECTIVE</div>
                                <section class="mt-8">
                                    <template v-if="latestCppt?.user_added_role != 'Super User' && latestCppt?.user_added_role != 'Doctor'">
                                        <div class="font-weight-bold text-w3green">ASESMEN SUBJECTIVE:</div>
                                        <div v-html="parsedBody(latestCppt?.subjective || '-')" class="fs-12"></div>
                                        <div class="font-weight-bold text-w3green mt-8">ASESMEN OBJECTIVE:</div>
                                        <div v-html="parsedBody(latestCppt?.nursing_objective || '-')" class="fs-12"></div>
                                    </template>
                                    <template v-else>
                                        <div v-html="parsedBody(latestCppt?.subjective || '-')" class="fs-12"></div>
                                    </template>
                                </section>
                            </div>
                            <div v-if="latestCppt?.user_added_role == 'Super User' || latestCppt?.user_added_role == 'Doctor'" class="mt-16">
                                <div class="fs-14 font-weight-bold bg-w3green py-1 px-8 text-white">ST GENERALIS</div>
                                <section class="mt-8">
                                    <div class="font-weight-bold text-w3green">STATUS LOKALIS:</div>
                                    <div v-html="parsedBody(latestCppt?.localist_status || '-')" class="fs-12"></div>
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
                                                    {{ latestCppt?.systolic_blood_pressure || '-' }}
                                                    <span class="fs-9">mmHg</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Dia</td>
                                                <td class="px-1">:</td>
                                                <td>
                                                    {{ latestCppt?.diastolic_blood_pressure || '-' }}
                                                    <span class="fs-9">mmHg</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>HR</td>
                                                <td class="px-1">:</td>
                                                <td>
                                                    {{ latestCppt?.pulse_frequency || '-' }}
                                                    <span class="fs-9">x/mnt</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>RR</td>
                                                <td class="px-1">:</td>
                                                <td>
                                                    {{ latestCppt?.breath_frequency || '-' }}
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
                                                    {{ latestCppt?.oximetry_frequency || '-' }}
                                                    <span class="fs-9">%</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>T</td>
                                                <td class="px-1">:</td>
                                                <td>
                                                    {{ latestCppt?.temperature || '-' }}
                                                    <span class="fs-9">ᵒC</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>O2</td>
                                                <td class="px-1">:</td>
                                                <td>{{ getOxygenPoint(latestCppt?.oxygen_type_id || '-') }}</td>
                                            </tr>
                                            <tr>
                                                <td>AVPU</td>
                                                <td class="px-1">:</td>
                                                <td>{{ getAwarePoint(latestCppt?.aware) }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-4 px-0">
                                        <table>
                                            <tr>
                                                <td>GCS</td>
                                                <td class="px-1">:</td>
                                                <td>{{ getGcsScore({eye: latestCppt?.eye_score, verbal: latestCppt?.verbal_score, motor: latestCppt?.motor_score}) }}</td>
                                            </tr>
                                            <tr>
                                                <td>Eye</td>
                                                <td class="px-1">:</td>
                                                <td>{{ getGcsEyePoint(latestCppt?.eye_score || '-') }}</td>
                                            </tr>
                                            <tr>
                                                <td>Vrb</td>
                                                <td class="px-1">:</td>
                                                <td>{{ getGcsVerbalPoint(latestCppt?.verbal_score || '-') }}</td>
                                            </tr>
                                            <tr>
                                                <td>Mtr</td>
                                                <td class="px-1">:</td>
                                                <td>{{ getGcsMotoricPoint(latestCppt?.motor_score || '-') }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <table class="mt-8">
                                    <tr>
                                        <td>Warna kulit</td>
                                        <td class="px-1">:</td>
                                        <td> {{ getColorSkinName(latestCppt?.skin_color || '-') }}</td>
                                    </tr>
                                    <tr>
                                        <td>Perilaku</td>
                                        <td class="px-1">:</td>
                                        <td>{{ getBehaviorName(latestCppt?.behavior || '-') }}</td>
                                    </tr>
                                </table>
                            </section>
                            <section v-if="latestCppt?.meaningful?.length > 0" class="mt-12">
                                <div class="font-weight-bold text-w3green">ST.GENERALIS BERMAKNA:</div>
                                <table>
                                    <template v-for="item in latestCppt.meaningful">
                                        <tr>
                                            <td>{{ item.key }}</td>
                                            <td class="px-1">:</td>
                                            <td>{{ item.value }}</td>
                                        </tr>
                                    </template>
                                </table>
                            </section>
                        </div>
                        <template v-if="latestCppt?.user_added_role == 'Super User' || latestCppt?.user_added_role == 'Doctor'">
                            <div class="col-lg-4 px-2 fs-12 mt-10 mt-lg-0">
                                <div class="fs-14 font-weight-bold bg-w3green py-1 px-8 text-white">ASSESSMENT</div>
                                <section class="mt-8">
                                    <div class="font-weight-bold text-w3green">DIAGNOSIS:</div>
                                    <div v-if="latestCppt?.icd10?.length > 0" class="pl-16">
                                        <li v-for="(list, index) in latestCppt.icd10">{{ list.icd10_code }} {{ list.icd10_name }}</li>
                                    </div>
                                    <div v-else class="text-italic">-</div>
                                </section>
                                <section class="mt-12">
                                    <div class="font-weight-bold text-w3green">CATATAN ICD10:</div>
                                    <div v-html="parsedBody(latestCppt?.icd10_notes || '-')"></div>
                                </section>
                                <section class="mt-12">
                                    <div class="font-weight-bold text-w3green">NON-ICD10:</div>
                                    <div v-if="latestCppt?.non_icd10?.length > 0" class="pl-16">
                                        <li v-for="(list, index) in latestCppt.non_icd10">{{ list }}</li>
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
                                            <div v-if="latestCppt?.transaction_doctor?.length > 0" class="pl-16">
                                                <li v-for="list in latestCppt.transaction_doctor">{{ list.costing_name }}</li>
                                            </div>
                                            <div v-else>-</div>
                                        </section>
                                        <section class="mt-12">
                                            <div class="font-weight-bold text-w3green">ICD9CM:</div>
                                            <div>
                                                <div v-if="latestCppt?.icd9cm?.length > 0" class="pl-16">
                                                    <li v-for="list in latestCppt.icd9cm">{{ list.icd9cm_code }} {{ list.icd9cm_name }}</li>
                                                </div>
                                                <div v-else>-</div>
                                            </div>
                                            <div v-html="parsedBody(latestCppt?.therapy_plan || '-')" class="mt-1"></div>
                                        </section>
                                    </div>
                                    <div class="col-lg-4">
                                        <section class="mt-8">
                                            <div class="font-weight-bold text-w3green">LABORATORIUM:</div>
                                            <template v-if="latestCppt?.laboratory_request?.length > 0">
                                                <template v-for="(il, i) in latestCppt.laboratory_request">
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
                                            <template v-if="latestCppt?.radiology_request?.length > 0">
                                                <template v-for="(ir, i) in latestCppt.radiology_request">
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
                                            <template v-if="latestCppt?.physiotherapy_request?.length > 0">
                                                <template v-for="(phy, i) in latestCppt.physiotherapy_request">
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
                                        <template v-if="latestCppt?.waiting_for_approval" >
                                            <div class="h-100 rounded mt-1" style="border: 1px solid #03a972;">
                                                <div class="p-1 font-italic fs-12 text-center bg-w3green text-white rounded-top">Tata laksana ini menunggu persetujuan DPJP</div>
                                                <section class="p-1">
                                                    <div class="font-weight-bold text-w3green">RESEP:</div>
                                                    <!-- <div class="font-weight-bold">
                                                        {{localDateTime(latestCppt?.waiting_for_approval?.date_added)}} (Kardeks)
                                                    </div> -->
                                                    <div v-for="(list, idx_p) in latestCppt?.waiting_for_approval?.form_prescription_parent" class="mt-1">
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
                                                        <div v-if="latestCppt?.waiting_for_approval?.form_prescription" class="mt-1 pl-16">
                                                            <li v-for="unit in latestCppt.waiting_for_approval.form_prescription" v-if="list.id == unit.prescription_item_type_id">
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
                                                <section class="mt-8 p-1" v-if="latestCppt?.waiting_for_approval?.kardex_non_pharmacological">
                                                    <div class="font-weight-bold text-w3green">NON-FARMAKOLOGI:</div>
                                                    <div class="pl-16">
                                                        <li v-for="inf in latestCppt?.waiting_for_approval?.kardex_non_pharmacological">
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

                                            <section class="mt-8" v-if="latestCppt?.plan">
                                                <div class="font-weight-bold text-w3green">NON-FARMAKOLOGI:</div>
                                                <div v-html="parsedBody(latestCppt?.plan || '-')"></div>
                                            </section>
                                        </template>

                                        <div v-else>
                                            <section class="mt-8">
                                                <div class="font-weight-bold text-w3green">RESEP:</div>
                                                <template v-if="latestCppt?.prescription?.length > 0">
                                                    <div v-for="(ipre, i) in latestCppt.prescription" class="mt-8">
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
                                                        <li v-for="inf in latestCppt?.kardex_non_pharmacological">
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
                                                    <div v-if="latestCppt?.plan" v-html="parsedBody(latestCppt?.plan || '-')"></div>
                                                </template>
                                            </section>
                                        </div>

                                        <section class="mt-8" v-if="latestCppt?.pharmacological_plan">
                                            <div class="font-weight-bold text-w3green">FARMAKOLOGI:</div>
                                            <div v-html="parsedBody(latestCppt?.pharmacological_plan || '-')"></div>
                                        </section>
                                    </div>

                                </div>
                            </div>
                        </template>
                        <!-- <template v-else-if="latestCppt?.user_added_role == 'Nurse'"> -->
                        <template v-else-if="latestCppt?.user_added_role != 'Super User' || latestCppt?.user_added_role != 'Doctor'">
                            <div class="col-lg-4 px-2 fs-12 mt-10 mt-lg-0">
                                <div class="fs-14 font-weight-bold bg-w3green py-1 px-8 text-white">ASSESSMENT</div>
                                <section class="mt-8">
                                    <div class="font-weight-bold text-w3green">DIAGNOSIS:</div>
                                    <div v-html="parsedBody(latestCppt?.nursing_diagnose || '-')" class="fs-12"></div>
                                </section>
                            </div>
                            <div class="col-lg-4 px-2 fs-12 mt-10">
                                <div class="fs-14 font-weight-bold bg-w3green py-1 px-8 text-white">PLAN</div>
                                <div v-html="parsedBody(latestCppt?.nursing_plan || '-')" class="fs-12"></div>
                            </div>
                        </template>
                    </template>
                </div>
                <div class="col-6">
                    <div class="fs-14 font-weight-bold bg-primary py-1 px-8 text-white mb-8">FORM HANDOVER</div>
                    <div class="row">
                        <div class="col-2 mt-8">
                                <label class="fs-13 mb-0">Shift Masuk</label>
                                <v-select v-model="form_handover.shift_in" :options="optShift" :reduce="item => item.value" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                            </div>
                            <div class="col-4 mt-8">
                                <label class="fs-13 mb-0"> Nama Perawat</label>
                                <v-select v-model="form_handover.nurse_in_id" :options="nurseList" :reduce="item => item.value" label="title" class="select-size-sm" placeholder="Pilih Perawat"></v-select>
                        </div>
                        <div class="col-2 mt-8">
                            <label class="fs-13 mb-0">Shift Keluar</label>
                            <v-select v-model="form_handover.shift_out" :options="optShift" :reduce="item => item.value" label="title" placeholder="-- Pilih --" class="select-size-sm" />
                        </div>
                        <div class="col-4 mt-8">
                            <label class="fs-13 mb-0"> Nama Perawat</label>
                            <v-select v-model="form_handover.nurse_out_id" :options="nurseList" :reduce="item => item.value" label="title" class="select-size-sm" placeholder="Pilih Perawat"></v-select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 mt-8">
                            <label class="fs-13 mb-0">Situation</label>
                            <textarea v-model.lazy="form_handover.situation" rows="3" class="form-control form-control-sm" />
                        </div>
                        <div class="col-6 mt-8">
                            <label class="fs-13 mb-0">Background</label>
                            <textarea v-model.lazy="form_handover.background" rows="3" class="form-control form-control-sm" />
                        </div>
                        <div class="col-6 mt-8">
                            <label class="fs-13 mb-0">Assessment</label>
                            <textarea v-model.lazy="form_handover.assessment" rows="3" class="form-control form-control-sm" />
                        </div>
                        <div class="col-6 mt-8">
                            <label class="fs-13 mb-0">Recommendation</label>
                            <textarea v-model.lazy="form_handover.recommendation" rows="3" class="form-control form-control-sm" />
                        </div>
                        <div class="col-6 mt-8">
                            <label class="fs-13 mb-0">Tindakan &amp; Medikasi</label>
                            <textarea v-model.lazy="form_handover.action" rows="3" class="form-control form-control-sm" />
                        </div>
                    </div>
                </div>
            </div>
            <template #modal-footer>
                <div class="d-flex justify-content-end">
                    <b-button variant="primary" size="sm" class="btn-block" @click="submitHandover" :disabled="active.disabled">
                        <feather-icon icon="SaveIcon" class="mr-50" />
                        Simpan Handover
                    </b-button>
                </div>
            </template>
        </b-modal>
    </div>
</template>

<script>
import dateMixin from "@/mixins/dateMixin";
import optionMixin from "@/mixins/optionMixin";

export default {
    mixins: [dateMixin, optionMixin],
    props: {
      cppt_list: {
          type: Array,
          default: () => []
      },
    },
    data() {
      return {
        active: {
            main_modal: false,
            disabled: false,
        },
        nurseList: [],
        temp_header: {
            name: '',
            alias_name: '',
            birth_date: '',
            norm: ''
        },
        form_handover: {
            situation: '',
            recommendation: '',
            background: '',
            assessment: '',
            action: '',
            shift_in: '',
            shift_out: '',
            nurse_in_id: '',
            nurse_out_id: '',
        },
        optShift: [
            { title: 'Pagi', value: 'pagi' },
            { title: 'Siang', value: 'siang' },
            { title: 'Malam', value: 'malam' },
        ],
        optNurses: [
            { title: 'Pagi', value: 'pagi' },
            { title: 'Siang', value: 'siang' },
            { title: 'Malam', value: 'malam' },
        ],
      }
    },
    computed: {
      latestCppt() {
        return this.cppt_list?.[0] || null
      }
    },
    methods: {
        async parentTrigger(message) {
            // console.log('message: ', message);
            this.regData = message?.regData

            this.temp_header.name = this.regData.name
            this.temp_header.alias_name = this.regData.alias_name
            this.temp_header.birth_date = this.regData.birth_date
            this.temp_header.norm = this.regData.norm

            this.active.main_modal = true
        },
        parsedBody(text){
            return String(text || '').replace(/\n/g, "<br>");
        },
        async submitHandover() {
            this.active.disabled = true

            try {
                await this.$store.dispatch('nurse/xxx', this.form_handover)
                .then(res => {
                    this.notification('Success', 'CheckIcon', 'success', `Handover pasien ${this.temp_header.alias_name}. ${this.temp_header.name} berhasil`)
                    this.resetHandoverForm()
                })
                .catch(({ message, debug }) => {
                    this.notification(`Failed ${debug}`, 'CloseIcon', 'danger', `${message}`)
                })
            } catch (error) {
                console.error('Error catch while submit handover at: ', error);
                this.active.disabled = false
            }
        },
        resetHandoverForm() {
            this.form_handover = {
                situation: '',
                recommendation: '',
                background: '',
                assessment: '',
                action: '',
                shift_in: '',
                shift_out: '',
                nurse_in_id: '',
                nurse_out_id: '',
            }
        },
        getAllNurseList() {
            try {
                this.$store.dispatch('employee/getNurseList').then((response) => {
                    this.nurseList = response
                }, error => {
                    console.error('Error while GET employee/getNurseList general at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET employee/getNurseList general at: ', error);
            }
        },

    },
    created() {
        this.getAllNurseList()
    },
}
</script>