<template>
    <section class="card mt-16 shadow-sm">
        <div class="card-header p-12 bg-white">
            <ModernLabel variant="w3green" :label="getSoapLabel(result)" :small="`Ditambahkan pada ${localFullDay(result?.date_added)} Oleh ${result?.user_added_name}`" />
            <section class="mt-8">
                <b-button v-b-toggle="'accordion-'+result?.id" size="sm" variant="w3green" class="mr-1">
                    <feather-icon icon="EyeIcon" size="12" class="mr-1" />
                    <span class="fs-12">LIHAT CPPT DISINI</span>
                </b-button>
                <template v-if="userData.user_group.id == jkn_group_id || userData.user_group.id == admin_group_id">
                    <router-link :to="`/patient/cppt/print/${result.id}`" target="_blank">
                        <b-button size="sm" variant="outline-success-500" class="mr-1" v-b-tooltip.hover.top="'Cetak CPPT'">
                            <feather-icon icon="PrinterIcon" size="12" class="mr-1"/>
                            <span class="fs-12">CPPT</span>
                        </b-button>
                    </router-link>
                </template>
            </section>
        </div>

        <b-collapse :id="`accordion-`+result?.id" :visible="visible" >
            <div class="card-body py-12">
                <div class="row">
                    <template v-if="result?.user_added_role == 'Gizi'">
                        <div class="col-12 px-8 fs-12">
                            <div class="fs-14 font-weight-bold bg-w3green py-1 px-8 text-white">GIZI</div>
                            <div class="row">
                                <div class="col-lg-4 mt-8">
                                    <div class="font-weight-bold text-w3green">ASESMEN:</div>
                                    <div v-html="parsedBody(result?.nutrition_assessment || '-')"></div>
                                </div>
                                <div class="col-lg-4 mt-8">
                                    <div class="font-weight-bold text-w3green">DIAGNOSIS:</div>
                                    <div v-html="parsedBody(result?.nutrition_diagnosis || '-')"></div>
                                </div>
                                <div class="col-lg-4 mt-8">
                                    <div class="font-weight-bold text-w3green">INTERVENSI:</div>
                                    <div v-html="parsedBody(result?.nutrition_intervention || '-')"></div>
                                </div>
                                <div class="col-lg-4 mt-8 mt-lg-12">
                                    <div class="font-weight-bold text-w3green">MONITORING & EVALUASI:</div>
                                    <div v-html="parsedBody(result?.nutrition_monitoring_evaluation || '-')"></div>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template v-else>
                        <div class="col-lg-4 px-8 fs-12">
                            <div class="fs-14 font-weight-bold bg-w3green py-1 px-8 text-white">SUBJECTIVE</div>
                            <section class="mt-8">
                                <template v-if="result?.user_added_role != 'Super User' && result?.user_added_role != 'Doctor'">
                                    <div class="font-weight-bold text-w3green">ASESMEN SUBJECTIVE:</div>
                                    <div v-html="parsedBody(result?.subjective || '-')" class="fs-12"></div>
                                    <div class="font-weight-bold text-w3green mt-8">ASESMEN OBJECTIVE:</div>
                                    <div v-html="parsedBody(result?.nursing_objective || '-')" class="fs-12"></div>
                                </template>
                                <template v-else>
                                    <div v-html="parsedBody(result?.subjective || '-')" class="fs-12"></div>
                                </template>
                            </section>
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
                                                    {{ result?.systolic_blood_pressure || '-' }}
                                                    <span class="fs-9">mmHg</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Dia</td>
                                                <td class="px-1">:</td>
                                                <td>
                                                    {{ result?.diastolic_blood_pressure || '-' }}
                                                    <span class="fs-9">mmHg</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>HR</td>
                                                <td class="px-1">:</td>
                                                <td>
                                                    {{ result?.pulse_frequency || '-' }}
                                                    <span class="fs-9">x/mnt</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>RR</td>
                                                <td class="px-1">:</td>
                                                <td>
                                                    {{ result?.breath_frequency || '-' }}
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
                                                    {{ result?.oximetry_frequency || '-' }}
                                                    <span class="fs-9">%</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>T</td>
                                                <td class="px-1">:</td>
                                                <td>
                                                    {{ result?.temperature || '-' }}
                                                    <span class="fs-9">ᵒC</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>O2</td>
                                                <td class="px-1">:</td>
                                                <td>{{ getOxygenPoint(result?.oxygen_type_id || '-') }}</td>
                                            </tr>
                                            <tr>
                                                <td>AVPU</td>
                                                <td class="px-1">:</td>
                                                <td>{{ getAwarePoint(result?.aware) }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-4 px-0">
                                        <table>
                                            <tr>
                                                <td>GCS</td>
                                                <td class="px-1">:</td>
                                                <td>{{ getGcsScore({eye: result?.eye_score, verbal: result?.verbal_score, motor: result?.motor_score}) }}</td>
                                            </tr>
                                            <tr>
                                                <td>Eye</td>
                                                <td class="px-1">:</td>
                                                <td>{{ getGcsEyePoint(result?.eye_score || '-') }}</td>
                                            </tr>
                                            <tr>
                                                <td>Vrb</td>
                                                <td class="px-1">:</td>
                                                <td>{{ getGcsVerbalPoint(result?.verbal_score || '-') }}</td>
                                            </tr>
                                            <tr>
                                                <td>Mtr</td>
                                                <td class="px-1">:</td>
                                                <td>{{ getGcsMotoricPoint(result?.motor_score || '-') }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <table class="mt-8">
                                    <tr>
                                        <td>Warna kulit</td>
                                        <td class="px-1">:</td>
                                        <td> {{ getColorSkinName(result?.skin_color || '-') }}</td>
                                    </tr>
                                    <tr>
                                        <td>Perilaku</td>
                                        <td class="px-1">:</td>
                                        <td>{{ getBehaviorName(result?.behavior || '-') }}</td>
                                    </tr>
                                </table>
                            </section>
                            <section v-if="meaningful?.length > 0" class="mt-12">
                                <div class="font-weight-bold text-w3green">ST.GENERALIS BERMAKNA:</div>
                                <table>
                                    <template v-for="item in meaningful">
                                        <tr>
                                            <td>{{ item.key }}</td>
                                            <td class="px-1">:</td>
                                            <td>{{ item.value }}</td>
                                        </tr>
                                    </template>
                                </table>
                            </section>
                        </div>
                        <template v-if="result?.user_added_role == 'Super User' || result?.user_added_role == 'Doctor'">
                            <div class="col-lg-4 px-2 fs-12 mt-10 mt-lg-0">
                                <div class="fs-14 font-weight-bold bg-w3green py-1 px-8 text-white">ASSESSMENT</div>
                                <section class="mt-8">
                                    <div class="font-weight-bold text-w3green">DIAGNOSIS:</div>
                                    <template v-if="result?.icd10?.length > 0">
                                        <li v-for="(list, index) in result.icd10">{{ list.icd10_code }} {{ list.icd10_name }}</li>
                                    </template>
                                    <div v-else class="text-italic">-</div>
                                </section>
                                <section class="mt-12">
                                    <div class="font-weight-bold text-w3green">CATATAN ICD10:</div>
                                    <div v-html="parsedBody(result?.icd10_notes || '-')"></div>
                                </section>
                                <section class="mt-12">
                                    <div class="font-weight-bold text-w3green">NON-ICD10:</div>
                                    <template v-if="result?.non_icd10?.length > 0">
                                        <li v-for="(list, index) in result.non_icd10">{{ list }}</li>
                                    </template>
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
                                            <template v-if="result?.transaction_doctor?.length > 0">
                                                <li v-for="list in result.transaction_doctor">{{ list.costing_name }}</li>
                                            </template>
                                            <div v-else>-</div>
                                        </section>
                                        <section class="mt-12">
                                            <div class="font-weight-bold text-w3green">ICD9CM:</div>
                                            <div>
                                                <div v-if="result?.icd9cm?.length > 0">
                                                    <li v-for="list in result.icd9cm">{{ list.icd9cm_code }} {{ list.icd9cm_name }}</li>
                                                </div>
                                                <div v-else>-</div>
                                            </div>
                                            <div v-html="parsedBody(result?.therapy_plan || '-')" class="mt-1"></div>
                                        </section>
                                    </div>
                                    <div class="col-lg-4">
                                        <section class="mt-8">
                                            <div class="font-weight-bold text-w3green">LABORATORIUM:</div>
                                            <template v-if="result?.laboratory_request?.length > 0">
                                                <template v-for="(il, i) in result.laboratory_request">
                                                    <div class="mb-12">
                                                        <div class="font-weight-bold">{{localDateTime(il.date_request)}}</div>
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
                                                </template>
                                            </template>
                                            <div v-else>-</div>
                                        </section>
                                        <section class="mt-12">
                                            <div class="font-weight-bold text-w3green">RADIOLOGI:</div>
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
                                            <div v-else class="font-weight-bold">-</div>
                                        </section>
                                        <section class="mt-12">
                                            <div class="font-weight-bold text-w3green">FISIOTERAPI:</div>
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
                                        </section>
                                    </div>
                                    <div class="col-lg-4">
                                        <section class="mt-8">
                                            <div class="font-weight-bold text-w3green">RESEP:</div>
                                            <pre>{{  }}</pre>
                                            <template v-if="result?.prescription?.length > 0">
                                                <div v-for="(ipre, i) in result.prescription" class="mt-8">
                                                    <div class="font-weight-bold">
                                                        <template>
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
                                        </section>
                                        <section class="mt-8">
                                            <div class="font-weight-bold text-w3green">NON-FARMAKOLOGI:</div>
                                            <li v-for="inf in result?.kardex_non_pharmacological">
                                                <div v-html="parsedBody(inf.name || '-')"></div>
                                            </li>
                                            <div v-if="result?.plan" v-html="parsedBody(result?.plan || '-')"></div>
                                        </section>
                                        <section class="mt-8">
                                            <div class="font-weight-bold text-w3green">CATATAN PEMBERIAN OBAT (FARMAKOLOGI):</div>
                                            <li v-for="inf in result?.kardex_pharmacological_note">
                                                <div v-html="parsedBody(inf.note || '-')"></div>
                                            </li>
                                            <div v-if="result?.pharmacological_plan" v-html="parsedBody(result?.pharmacological_plan || '-')"></div>
                                        </section>
                                    </div>

                                </div>
                            </div>
                        </template>
                        <template v-else-if="result?.user_added_role != 'Super User' || result?.user_added_role != 'Doctor'">
                            <div class="col-lg-4 px-2 fs-12 mt-10 mt-lg-0">
                                <div class="fs-14 font-weight-bold bg-w3green py-1 px-8 text-white">ASSESSMENT</div>
                                <section class="mt-8">
                                    <div class="font-weight-bold text-w3green">DIAGNOSIS:</div>
                                    <div v-html="parsedBody(result?.nursing_diagnose || '-')" class="fs-12"></div>
                                </section>
                            </div>
                            <div class="col-lg-4 px-2 fs-12 mt-10">
                                <div class="fs-14 font-weight-bold bg-w3green py-1 px-8 text-white">PLAN</div>
                                <div v-html="parsedBody(result?.nursing_plan || '-')" class="fs-12"></div>
                            </div>
                        </template>
                    </template>
                </div>
                <!-- <div class="row">
                    <div class="col-lg-4 px-8">
                        <div class="fs-14 font-weight-bold bg-w3green py-1 px-8 text-white">SUBJECTIVE</div>
                        <div class="fs-12 mt-8">{{ result?.subjective }}</div>
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
                                                {{ result?.systolic_blood_pressure || '-' }}
                                                <span class="fs-9">mmHg</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Dia</td>
                                            <td class="px-1">:</td>
                                            <td>
                                                {{ result?.diastolic_blood_pressure || '-' }}
                                                <span class="fs-9">mmHg</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>HR</td>
                                            <td class="px-1">:</td>
                                            <td>
                                                {{ result?.pulse_frequency || '-' }}
                                                <span class="fs-9">x/mnt</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>RR</td>
                                            <td class="px-1">:</td>
                                            <td>
                                                {{ result?.breath_frequency || '-' }}
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
                                                {{ result?.oximetry_frequency || '-' }}
                                                <span class="fs-9">%</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>T</td>
                                            <td class="px-1">:</td>
                                            <td>
                                                {{ result?.temperature || '-' }}
                                                <span class="fs-9">ᵒC</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>O2</td>
                                            <td class="px-1">:</td>
                                            <td>{{ getOxygenPoint(result?.oxygen_type_id || '-') }}</td>
                                        </tr>
                                        <tr>
                                            <td>AVPU</td>
                                            <td class="px-1">:</td>
                                            <td>{{ getAwarePoint(result?.aware) }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-4 px-0">
                                    <table>
                                        <tr>
                                            <td>GCS</td>
                                            <td class="px-1">:</td>
                                            <td>{{ getGcsScore({eye: result?.eye_score, verbal: result?.verbal_score, motor: result?.motor_score}) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Eye</td>
                                            <td class="px-1">:</td>
                                            <td>{{ getGcsEyePoint(result?.eye_score || '-') }}</td>
                                        </tr>
                                        <tr>
                                            <td>Vrb</td>
                                            <td class="px-1">:</td>
                                            <td>{{ getGcsVerbalPoint(result?.verbal_score || '-') }}</td>
                                        </tr>
                                        <tr>
                                            <td>Mtr</td>
                                            <td class="px-1">:</td>
                                            <td>{{ getGcsMotoricPoint(result?.motor_score || '-') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </section>
                        <section v-if="meaningful?.length > 0" class="mt-12">
                            <div class="font-weight-bold text-w3green">ST.GENERALIS BERMAKNA:</div>
                            <table>
                                <template v-for="item in meaningful">
                                    <tr>
                                        <td>{{ item.key }}</td>
                                        <td class="px-1">:</td>
                                        <td>{{ item.value }}</td>
                                    </tr>
                                </template>
                            </table>
                        </section>
                    </div>
                    <template v-if="result?.user_added_role == 'Super User' || result?.user_added_role == 'Doctor'">
                        <div class="col-lg-4 px-2 fs-12 mt-10 mt-lg-0">
                            <div class="fs-14 font-weight-bold bg-w3green py-1 px-8 text-white">ASSESSMENT</div>
                            <section class="mt-8">
                                <div class="font-weight-bold text-w3green">DIAGNOSIS:</div>
                                <template v-if="result?.icd10?.length > 0">
                                    <li v-for="(list, index) in result.icd10">{{ list.icd10_code }} {{ list.icd10_name }}</li>
                                </template>
                                <div v-else class="text-italic">-</div>
                            </section>
                            <section class="mt-12">
                                <div class="font-weight-bold text-w3green">CATATAN ICD10:</div>
                                <div>{{ result?.icd10_notes || '-'}}</div>
                            </section>
                            <section class="mt-12">
                                <div class="font-weight-bold text-w3green">NON-ICD10:</div>
                                <template v-if="result?.non_icd10?.length > 0">
                                    <li v-for="(list, index) in result.non_icd10">{{ list }}</li>
                                </template>
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
                                        <template v-if="result?.transaction_doctor?.length > 0">
                                            <li v-for="list in result.transaction_doctor">{{ list.costing_name }}</li>
                                        </template>
                                        <div v-else>-</div>
                                    </section>
                                    <section class="mt-12">
                                        <div class="font-weight-bold text-w3green">ICD9CM:</div>
                                        <div>
                                            <div v-if="result?.icd9cm?.length > 0">
                                                <li v-for="list in result.icd9cm">{{ list.icd9cm_code }} {{ list.icd9cm_name }}</li>
                                            </div>
                                            <div v-else>-</div>
                                        </div>
                                        <div class="mt-1">{{ result?.therapy_plan || '-' }}</div>
                                    </section>
                                </div>
                                <div class="col-lg-4">
                                    <section class="mt-8">
                                        <div class="font-weight-bold text-w3green">LABORATORIUM:</div>
                                        <template v-if="result?.laboratory_request?.length > 0">
                                            <template v-for="(il, i) in result.laboratory_request">
                                                <div class="mb-12">
                                                    <div class="font-weight-bold">{{localDateTime(il.date_request)}}</div>
                                                    <li v-for="(list, j) in il.laboratory_request_items">
                                                        <template v-if="list.result?.length > 0">
                                                            <feather-icon icon="EyeIcon" @click="triggerLabResult({ alias_name: regData.alias_name, name: regData.name, result: list.result, costing_name: list.laboratory_package_name, laboratory_request_id: list.laboratory_request_id })" color="blue" v-b-tooltip.hover.left="`Lihat hasil ${list.laboratory_package_name}`"/>
                                                        </template>
                                                        <template v-else>
                                                            <img src="./../../../../@core/assets/icons/hourglass.svg" alt="" width="14px" v-b-tooltip.hover.left="`${list.laboratory_package_name} telah di order, belum terbit hasil`">
                                                        </template>
                                                        {{ list.laboratory_package_name }}
                                                    </li>
                                                </div>
                                            </template>
                                        </template>
                                        <div v-else>-</div>
                                    </section>
                                    <section class="mt-12">
                                        <div class="font-weight-bold text-w3green">RADIOLOGI:</div>
                                        <template v-if="result?.radiology_request?.length > 0">
                                            <template v-for="(ir, i) in result.radiology_request">
                                                <div class="mb-12">
                                                    <div class="font-weight-bold">{{localDateTime(ir.date_request)}}</div>
                                                    <li v-for="(list, j) in ir.radiology_request_items">
                                                        <template v-if="list.url.length > 0">
                                                            <feather-icon icon="EyeIcon" @click="triggerRadResult({ alias_name: regData.alias_name, name: regData.name, url: list.url, costing_name: list.radiology_package_name })" color="blue" v-b-tooltip.hover.left="`Lihat hasil ${list.radiology_package_name}`"/>
                                                        </template>
                                                        <template v-else>
                                                            <img src="./../../../../@core/assets/icons/hourglass.svg" alt="" width="14px" v-b-tooltip.hover.left="`${list.radiology_package_name} telah di order, belum terbit hasil`">
                                                        </template>
                                                        {{ list.radiology_package_name }}
                                                    </li>
                                                </div>
                                            </template>
                                        </template>
                                        <div v-else class="font-weight-bold">-</div>
                                    </section>
                                    <section class="mt-12">
                                        <div class="font-weight-bold text-w3green">FISIOTERAPI:</div>
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
                                    </section>
                                </div>
                                <div class="col-lg-4">
                                    <section class="mt-8">
                                        <div class="font-weight-bold text-w3green">RESEP:</div>
                                        <template v-if="result?.prescription?.length > 0">
                                            <template v-for="(ipre, i) in result.prescription">
                                                <div class="font-weight-bold">
                                                    <template>
                                                        <feather-icon v-if="ipre?.flag == 1" icon="CheckSquareIcon" color="green" v-b-tooltip.hover.left="`Diproses oleh farmasi`"/>
                                                        <img v-else-if="ipre?.flag == 0" src="./../../../../@core/assets/icons/hourglass.svg" alt="" width="14px" v-b-tooltip.hover.left="`Berhasil diorder`">
                                                    </template>
                                                    {{localDateTime(ipre.date_added)}}</div>
                                                <div v-for="(list, idx_p) in ipre.form_prescription_parent" :class="idx_p > 0 ? 'mt-12' : ''">
                                                    <div :class="getParentVariant(list.id)" class="px-12 py-1">
                                                        <div class="font-weight-bold">{{ getParentName(list.id) }}</div>
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
                                            </template>
                                        </template>
                                        <div v-else>-</div>
                                    </section>
                                    <section class="mt-8">
                                        <div class="font-weight-bold text-w3green">NON-FARMAKOLOGI:</div>
                                        <div>{{ result?.plan || '-' }}</div>
                                    </section>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template v-else>
                        <div class="col-lg-4 px-2 fs-12 mt-10 mt-lg-0">
                            <div class="fs-14 font-weight-bold bg-w3green py-1 px-8 text-white">ASSESSMENT</div>
                            <section class="mt-8">
                                <div class="font-weight-bold text-w3green">DIAGNOSIS:</div>
                                <div class="fs-12 mt-8">{{ result?.nursing_diagnose || '-' }}</div>
                            </section>
                        </div>
                        <div class="col-lg-4 px-2 fs-12 mt-10">
                            <div class="fs-14 font-weight-bold bg-w3green py-1 px-8 text-white">PLAN</div>
                            <div class="fs-12 mt-8">{{ result?.nursing_plan || '-'}}</div>
                        </div>
                    </template>
                </div> -->
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
            admin_group_id: 'ddad321c-a9d8-430c-95f0-de38d20834a1',
            jkn_group_id: 'a4c04d8d-12d8-4c54-afca-69de6bd5f62d',
            hfi_inpatient: '40f06534-07ec-4221-ad9d-dea08196079f',
            hfi_outpatient: '3cb626f5-9148-4fb1-8663-3d7f028b01a6',
            hfi_emergency: '444b3134-5b75-4b67-af72-a558d46716d7',
            meaningful: [],
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
            ]
        }
    },

    watch: {
        'result': function(val){
            if (val) {
                let all_key = this.generalis_key

                let temp_array = []
                for (let i = 0; i < all_key.length; i++) {
                    const el = all_key[i];
                    if (obj[el.key] && obj[el.key] != el.default ) {
                        const func = this[el.name]
                        const result = func(obj[el.key])
                        temp_array.push({
                            key: el.label,
                            value: result
                        });
                    }
                }

                this.meaningful = temp_array
            }
        },
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
            return `CPPT ${result?.group_name} - ${result?.health_facility_item_name}`
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