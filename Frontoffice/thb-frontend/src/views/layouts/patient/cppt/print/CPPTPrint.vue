<template>
    <div>
        <b-container fluid="xl" class="main-content">
            <div class="d-flex">
                <b-button @click="closeWindow" size="sm" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button @click="printCanvas" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
            </div>
            <div class="canvas-wrapper mt-32">
                <div id="canvas" ref="canvas" class="mt-16" style="position: relative;">
                    <div class="row align-items-center pb-24 fs-14" style="border-bottom: 2px solid black;">
                        <div class="col-2 text-center">
                            <img src="../../../../../@core/assets/images/logo-thb-small.png" width="46%" alt="rs-logo">
                        </div>
                        <div class="col text-center">
                            <p class="text-bold-18">RS Taman Harapan Baru</p>
                            <p>Jl. Kaliabang Tengah No. 2 RT.004 RW.023, Bekasi, Kota Bekasi</p>
                            <p>Info: +62 778 4161212 / 0853 7111 1212</p>
                        </div>
                        <div class="col-2 text-center">
                            <!-- <img src="../../../../../@core/assets/images/lafki-logo.png" width="62%" alt="lafki-logo"> -->
                        </div>
                    </div>

                    <div class="fs-13 mt-24" style="line-height: 120%;">
                        <div class="row">
                            <div class="col-6">
                                <table>
                                    <tr>
                                        <td class="align-top" style="width: 120px;">Nama Pasien</td>
                                        <td class="text-center" style="width: 20px;">:</td>
                                        <td>{{ regData.alias_name }}. {{ regData.name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="align-top">Tanggal Lahir</td>
                                        <td class="text-center">:</td>
                                        <td>{{ regData.birth_date }} ({{ parsedAge(regData.birth_date) }})</td>
                                    </tr>
                                    <tr>
                                        <td class="align-top">No. Rekam Medis</td>
                                        <td class="text-center">:</td>
                                        <td>{{regData.norm}}</td>
                                    </tr>
                                    <tr>
                                        <td class="align-top">Jenis Kelamin</td>
                                        <td class="text-center">:</td>
                                        <td>{{ regData.sex_name }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-6">
                                <table>
                                    <tr>
                                        <td class="align-top" style="width: 120px;">PPA</td>
                                        <td class="text-center" style="width: 20px;">:</td>
                                        <td>{{ cppt_data.user_added_name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="align-top">Jabatan</td>
                                        <td class="text-center">:</td>
                                        <td>{{ cppt_data.user_added_role }}</td>
                                    </tr>
                                    <tr>
                                        <td class="align-top">Ditambahkan pada</td>
                                        <td class="text-center">:</td>
                                        <td>{{localDateTime(cppt_data.date_added)}}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="mt-24">
                            <template v-if="cppt_data.group_name == 'Doctor' || cppt_data.group_name == 'Super User'">
                                <section>
                                    <ModernLabel variant="w3green" label="SUBJECTIVE"/>
                                    <div class="mt-8">
                                        <div class="font-weight-bold">SUBJECTIVE:</div>
                                        <div v-html="parsedBody(cppt_data?.subjective || '-')"></div>
                                    </div>
                                </section>

                                <section class="mt-24">
                                    <ModernLabel variant="w3green" label="OBJECTIVE"/>
                                    <div class="mt-8">
                                        <div>
                                            <div class="font-weight-bold">TANDA VITAL & ANTROPOMETRI:</div>
                                            <div class="row">
                                                <div class="col-3">
                                                    <table>
                                                        <tr>
                                                            <td>Sistolik</td>
                                                            <td class="px-8">:</td>
                                                            <td>{{ cppt_data?.systolic_blood_pressure || '-' }} mmHg</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Diastolik</td>
                                                            <td class="px-8">:</td>
                                                            <td>{{ cppt_data?.diastolic_blood_pressure || '-' }} mmHg</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Frekuensi Nadi</td>
                                                            <td class="px-8">:</td>
                                                            <td>{{ cppt_data?.pulse_frequency || '-' }} x/mnt</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Frekuensi Nafas</td>
                                                            <td class="px-8">:</td>
                                                            <td>{{ cppt_data?.breath_frequency || '-' }} x/mnt</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Saturasi Oksigen</td>
                                                            <td class="px-8">:</td>
                                                            <td>{{ cppt_data?.oximetry_frequency || '-' }} %</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <div class="col-3">
                                                    <table>
                                                        <tr>
                                                            <td>Suhu</td>
                                                            <td class="px-8">:</td>
                                                            <td>{{ cppt_data?.temperature || '-' }} ᵒC</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Berat Badan</td>
                                                            <td class="px-8">:</td>
                                                            <td>{{ cppt_data?.weight || '-' }} kg</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Tinggi Badan</td>
                                                            <td class="px-8">:</td>
                                                            <td>{{ cppt_data?.height || '-' }} cm</td>
                                                        </tr>
                                                        <tr>
                                                            <td>IMT</td>
                                                            <td class="px-8">:</td>
                                                            <td>{{ cppt_data?.imt || '-' }} kg/m²</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="align-top">Oksigenasi</td>
                                                            <td class="align-top px-8">:</td>
                                                            <td>{{ getOxygenName(cppt_data?.oxygen_type_id || '-') }}</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <div class="col-5">
                                                    <table>
                                                        <tr>
                                                            <td class="align-top" style="width: 80px;">Kesadaran</td>
                                                            <td class="align-top px-8">:</td>
                                                            <td>{{ getAwareName(cppt_data?.aware) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="align-top">GCS Mata</td>
                                                            <td class="align-top px-8">:</td>
                                                            <td>{{ getGcsEyeName(cppt_data?.eye_score || '-') }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="align-top">GCS Verbal</td>
                                                            <td class="align-top px-8">:</td>
                                                            <td>{{ getGcsVerbalName(cppt_data?.verbal_score || '-') }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="align-top">GCS Motorik</td>
                                                            <td class="align-top px-8">:</td>
                                                            <td>{{ getGcsMotoricName(cppt_data?.motor_score || '-') }}</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div v-if="meaningful?.length > 0" class="mt-12">
                                            <div class="font-weight-bold">STATUS GENERALIS BERMAKNA:</div>
                                            <table>
                                                <template v-for="item in meaningful">
                                                    <tr>
                                                        <td>{{ item.key }}</td>
                                                        <td class="px-1">:</td>
                                                        <td>{{ item.value }}</td>
                                                    </tr>
                                                </template>
                                            </table>
                                        </div>
                                        <div class="mt-12">
                                            <div class="font-weight-bold">GENITALIA:</div>
                                            <div v-html="parsedBody(cppt_data?.genitalia || '-')"></div>
                                        </div>
                                        <div class="mt-12">
                                            <div class="font-weight-bold">STATUS LOKALIS:</div>
                                            <div v-html="parsedBody(cppt_data?.localist_status || '-')"></div>
                                        </div>
                                        <div class="mt-12">
                                            <div class="font-weight-bold">STATUS NEUROLOGIS:</div>
                                            <div v-html="parsedBody(cppt_data?.neurologist_status || '-')"></div>
                                        </div>
                                        <div class="mt-12">
                                            <div class="font-weight-bold">STATUS OFTALMOLOGIS:</div>
                                            <div v-html="parsedBody(cppt_data?.ophthal_status || '-')"></div>
                                        </div>
                                    </div>
                                </section>

                                <section class="mt-24">
                                    <ModernLabel variant="w3green" label="ASSESSMENT"/>
                                    <div class="mt-8">
                                        <div>
                                            <div class="font-weight-bold">DIAGNOSIS (ICD10):</div>
                                            <div>
                                                <div v-if="cppt_data?.icd10?.length > 0" class="pl-16">
                                                    <li v-for="list in cppt_data.icd10">{{ list.icd10_code }} {{ list.icd10_name }}</li>
                                                </div>
                                                <div v-else class="text-italic">-</div>
                                            </div>
                                        </div>
                                        <div class="mt-12">
                                            <div class="font-weight-bold">CATATAN ICD10:</div>
                                            <div v-html="parsedBody(cppt_data?.icd10_notes || '-')"></div>
                                        </div>
                                        <div class="mt-12">
                                            <div class="font-weight-bold">NON-ICD10:</div>
                                            <div>
                                                <div v-if="cppt_data?.non_icd10?.length > 0" class="pl-16">
                                                    <li v-for="list in cppt_data.non_icd10">{{ list }}</li>
                                                </div>
                                                <div v-else class="text-italic">-</div>
                                            </div>
                                        </div>
                                    </div>
                                </section>

                                <section class="mt-24">
                                    <ModernLabel variant="w3green" label="PLAN"/>
                                    <div class="mt-8">
                                        <div>
                                            <div class="font-weight-bold">PROSEDUR:</div>
                                            <div>
                                                <div v-if="cppt_data?.transaction_doctor?.length > 0" class="pl-16">
                                                    <li v-for="list in cppt_data.transaction_doctor">{{ list.costing_name }}</li>
                                                </div>
                                                <div v-else>-</div>
                                            </div>
                                        </div>
                                        <div class="mt-12">
                                            <div class="font-weight-bold">ICD9CM:</div>
                                            <div>
                                                <div v-if="cppt_data?.icd9cm?.length > 0" class="pl-16">
                                                    <li v-for="list in cppt_data.icd9cm">{{ list.icd9cm_code }} {{ list.icd9cm_name }}</li>
                                                </div>
                                                <div v-else>-</div>
                                            </div>
                                        </div>
                                        <div class="mt-12">
                                            <div class="font-weight-bold">CATATAN ICD9CM:</div>
                                            <div v-html="parsedBody(cppt_data?.therapy_plan || '-')"></div>
                                        </div>
                                        <div class="mt-12">
                                            <div class="font-weight-bold">LABORATORIUM:</div>
                                            <div>
                                                <template v-if="cppt_data?.laboratory_request?.length > 0">
                                                    <template v-for="(il, i) in cppt_data.laboratory_request">
                                                        <div class="mb-12">
                                                            <div class="font-weight-bold">{{localDateTime(il.date_request)}}</div>
                                                            <div class="pl-16">
                                                                <li v-for="(list, j) in il.laboratory_request_items">{{ list.laboratory_package_name }} </li>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </template>
                                                <div v-else>-</div>
                                            </div>
                                        </div>
                                        <div class="mt-12">
                                            <div class="font-weight-bold">RADIOLOGI:</div>
                                            <div>
                                                <template v-if="cppt_data?.radiology_request?.length > 0">
                                                    <template v-for="(ir, i) in cppt_data.radiology_request">
                                                        <div class="mb-12">
                                                            <div class="font-weight-bold">{{localDateTime(ir.date_request)}}</div>
                                                            <div class="pl-16">
                                                                <li v-for="(list, j) in ir.radiology_request_items">{{ list.radiology_package_name }} </li>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </template>
                                                <div v-else>-</div>
                                            </div>
                                        </div>
                                        <div class="mt-12">
                                            <div class="font-weight-bold">FISIOTERAPI:</div>
                                            <div>
                                                <template v-if="cppt_data?.physiotherapy_request?.length > 0">
                                                    <template v-for="(phy, i) in cppt_data.physiotherapy_request">
                                                        <div class="mb-12">
                                                            <div class="font-weight-bold">{{localDateTime(phy.date_request)}}</div>
                                                            <div class="pl-16">
                                                                <li v-for="(list, j) in phy.physiotherapy_request_items">{{ list.physiotherapy_package_name }} </li>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </template>
                                                <div v-else>-</div>
                                            </div>
                                        </div>
                                        <div class="mt-12">
                                            <div class="font-weight-bold">RESEP:</div>
                                            <div>
                                                <template v-if="cppt_data?.prescription?.length > 0">
                                                    <div v-for="(ipre, i) in cppt_data.prescription">
                                                        <div class="font-weight-bold">{{localDateTime(ipre.date_added)}} ({{ isHomeRecipe(ipre) ? 'Resep Pulang' : 'Kardeks' }})</div>
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
                                        </div>
                                        <div class="mt-12">
                                            <div class="font-weight-bold">NON-FARMAKOLOGI:</div>
                                            <div>
                                                <div class="pl-16">
                                                    <li v-for="inf in cppt_data?.kardex_non_pharmacological">
                                                        <div v-html="parsedBody(inf.name || '-')"></div>
                                                    </li>
                                                </div>
                                                <div v-if="cppt_data?.plan" v-html="parsedBody(cppt_data?.plan || '-')"></div>
                                            </div>
                                        </div>
                                        <div v-if="cppt_data?.pharmacological_plan" class="mt-12">
                                            <div class="font-weight-bold">FARMAKOLOGI:</div>
                                            <div v-html="parsedBody(cppt_data?.pharmacological_plan || '-')"></div>
                                        </div>

                                    </div>
                                </section>
                            </template>

                            <template v-if="cppt_data.group_name == 'Nurse'">
                                <section>
                                    <ModernLabel variant="w3green" label="SUBJECTIVE"/>
                                    <div class="mt-8">
                                        <div>
                                            <div class="font-weight-bold">ASESMEN SUBJECTIVE:</div>
                                            <div v-html="parsedBody(cppt_data?.subjective || '-')"></div>
                                        </div>
                                        <div class="mt-12">
                                            <div class="font-weight-bold">ASESMEN OBJECTIVE:</div>
                                            <div v-html="parsedBody(cppt_data?.nursing_objective || '-')"></div>
                                        </div>
                                    </div>
                                </section>

                                <section class="mt-24">
                                    <ModernLabel variant="w3green" label="OBJECTIVE"/>
                                    <div class="mt-8">
                                        <div>
                                            <div class="font-weight-bold">TANDA VITAL & ANTROPOMETRI:</div>
                                            <div class="row">
                                                <div class="col-3">
                                                    <table>
                                                        <tr>
                                                            <td>Sistolik</td>
                                                            <td class="px-8">:</td>
                                                            <td>{{ cppt_data?.systolic_blood_pressure || '-' }} mmHg</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Diastolik</td>
                                                            <td class="px-8">:</td>
                                                            <td>{{ cppt_data?.diastolic_blood_pressure || '-' }} mmHg</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Frekuensi Nadi</td>
                                                            <td class="px-8">:</td>
                                                            <td>{{ cppt_data?.pulse_frequency || '-' }} x/mnt</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Frekuensi Nafas</td>
                                                            <td class="px-8">:</td>
                                                            <td>{{ cppt_data?.breath_frequency || '-' }} x/mnt</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Saturasi Oksigen</td>
                                                            <td class="px-8">:</td>
                                                            <td>{{ cppt_data?.oximetry_frequency || '-' }} %</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <div class="col-3">
                                                    <table>
                                                        <tr>
                                                            <td>Suhu</td>
                                                            <td class="px-8">:</td>
                                                            <td>{{ cppt_data?.temperature || '-' }} ᵒC</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Berat Badan</td>
                                                            <td class="px-8">:</td>
                                                            <td>{{ cppt_data?.weight || '-' }} kg</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Tinggi Badan</td>
                                                            <td class="px-8">:</td>
                                                            <td>{{ cppt_data?.height || '-' }} cm</td>
                                                        </tr>
                                                        <tr>
                                                            <td>IMT</td>
                                                            <td class="px-8">:</td>
                                                            <td>{{ cppt_data?.imt || '-' }} kg/m²</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="align-top">Oksigenasi</td>
                                                            <td class="align-top px-8">:</td>
                                                            <td>{{ getOxygenName(cppt_data?.oxygen_type_id || '-') }}</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <div class="col-5">
                                                    <table>
                                                        <tr>
                                                            <td class="align-top" style="width: 80px;">Kesadaran</td>
                                                            <td class="align-top px-8">:</td>
                                                            <td>{{ getAwareName(cppt_data?.aware) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="align-top">GCS Mata</td>
                                                            <td class="align-top px-8">:</td>
                                                            <td>{{ getGcsEyeName(cppt_data?.eye_score || '-') }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="align-top">GCS Verbal</td>
                                                            <td class="align-top px-8">:</td>
                                                            <td>{{ getGcsVerbalName(cppt_data?.verbal_score || '-') }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="align-top">GCS Motorik</td>
                                                            <td class="align-top px-8">:</td>
                                                            <td>{{ getGcsMotoricName(cppt_data?.motor_score || '-') }}</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div v-if="meaningful?.length > 0" class="mt-12">
                                            <div class="font-weight-bold">STATUS GENERALIS BERMAKNA:</div>
                                            <table>
                                                <template v-for="item in meaningful">
                                                    <tr>
                                                        <td>{{ item.key }}</td>
                                                        <td class="px-1">:</td>
                                                        <td>{{ item.value }}</td>
                                                    </tr>
                                                </template>
                                            </table>
                                        </div>
                                    </div>
                                </section>

                                <section class="mt-24">
                                    <ModernLabel variant="w3green" label="ASSESSMENT"/>
                                    <div class="mt-8">
                                        <div class="font-weight-bold">DIAGNOSIS:</div>
                                        <div v-html="parsedBody(cppt_data?.nursing_diagnose || '-')"></div>
                                    </div>
                                </section>

                                <section class="mt-24">
                                    <ModernLabel variant="w3green" label="PLAN"/>
                                    <div class="mt-8">
                                        <div class="font-weight-bold">PLAN:</div>
                                        <div v-html="parsedBody(cppt_data?.nursing_plan || '-')"></div>
                                    </div>
                                </section>
                            </template>

                            <template v-if="cppt_data.group_name == 'Gizi'">
                                <section>
                                    <ModernLabel variant="w3green" label="GIZI"/>
                                    <div class="mt-8">
                                        <div>
                                            <div class="font-weight-bold">ASESMEN:</div>
                                            <div v-html="parsedBody(cppt_data?.nutrition_assessment || '-')"></div>
                                        </div>
                                        <div class="mt-12">
                                            <div class="font-weight-bold">DIAGNOSIS:</div>
                                            <div v-html="parsedBody(cppt_data?.nutrition_diagnosis || '-')"></div>
                                        </div>
                                        <div class="mt-12">
                                            <div class="font-weight-bold">INTERVENSI:</div>
                                            <div v-html="parsedBody(cppt_data?.nutrition_intervention || '-')"></div>
                                        </div>
                                        <div class="mt-12">
                                            <div class="font-weight-bold">MONITORING & EVALUASI:</div>
                                            <div v-html="parsedBody(cppt_data?.nutrition_monitoring_evaluation || '-')"></div>
                                        </div>
                                    </div>
                                </section>
                            </template>
                        </div>
                    </div>

                </div>
            </div>
        </b-container>
    </div>
</template>
<script>
// import moment from 'moment'
import dateMixin from "@/mixins/dateMixin";
import optionMixin from '@/mixins/optionMixin';

export default {
    mixins: [dateMixin, optionMixin],

    data() {
        return {
            cppt_data: {},
            regData: {},
            signature_time: [],
            catalog_instruction: [],
            signature_units: [],
            // today: moment(new Date()).locale('id').format('LL'),
            // Profesional Pemberi Asuhan (PPA)

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
            ],
        }
    },

    methods: {
        async getMyCppt(cppt_id){
            try {
                this.$store.dispatch('cppt/getByCpptId', cppt_id).then(async (response) => {
                    if (response?.length > 0) {
                        let res_data = response[0] || null
                        this.cppt_data = res_data
                        // console.log('this.cppt_data',this.cppt_data)

                        let all_key = this.generalis_key
                        let temp_array = []

                        for (let i = 0; i < all_key.length; i++) {
                            const el = all_key[i];
                            if (res_data[el.key] && res_data[el.key] != el.default ) {
                                const func = this[el.name]
                                const result = func(res_data[el.key])
                                temp_array.push({
                                    key: el.label,
                                    value: result
                                });
                            }
                        }
                        this.meaningful = temp_array

                        await this.getData(this.cppt_data.registration_destination_id)
                    }
                }, error => {
                    console.error('Error while GET cppt/getByCpptId at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET cppt/getByCpptId at: ', error);
            }
        },

        async getData(reg_des_id) {
            this.$store.dispatch('registration/getById', reg_des_id).then(response => {
                this.regData = response[0]
                // console.log(this.regData);
            })
        },

        isHomeRecipe(obj) {
            if (obj?.type_prescription_id == 2) return true
            return false
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

        handleKeyPress(event) {
            if (event.ctrlKey && event.key === 'p') {
                event.preventDefault();
                this.printCanvas();
            }
        },

        closeWindow() {
            window.close()
        },

        printCanvas() {
            const canvas = this.$refs.canvas;
            const originalContents = document.body.innerHTML;
            document.body.innerHTML = canvas.innerHTML;
            window.print();
            document.body.innerHTML = originalContents;
            location.reload()
        },

        parsedBody(text){
            return String(text || '-').replace(/\n/g, "<br>")
        },

        // async printCanvas() {
        //     await this.$nextTick()

        //     const canvas = this.$refs.canvas
        //     const originalBody = document.body.innerHTML

        //     // Convert images ke base64 (tidak perlu load ulang)
        //     await this.convertImagesToDataURL(canvas)

        //     document.body.innerHTML = canvas.innerHTML
        //     window.print()
        //     document.body.innerHTML = originalBody
        //     location.reload()
        // },

        // async convertImagesToDataURL(container) {
        //     const images = Array.from(container.getElementsByTagName('img'))

        //     await Promise.all(images.map(async (img) => {
        //         if (img.src.startsWith('data:')) return

        //         try {
        //             const response = await fetch(img.src)
        //             const blob = await response.blob()
        //             const dataURL = await this.blobToDataURL(blob)
        //             img.src = dataURL
        //         } catch (error) {
        //             console.warn('Failed to convert image:', img.src)
        //         }
        //     }))
        // },

        // blobToDataURL(blob) {
        //     return new Promise((resolve) => {
        //         const reader = new FileReader()
        //         reader.onload = () => resolve(reader.result)
        //         reader.readAsDataURL(blob)
        //     })
        // },
    },

    mounted() {
        window.addEventListener('keydown', this.handleKeyPress);
    },

    beforeDestroy() {
        window.removeEventListener('keydown', this.handleKeyPress);
    },

    created() {
        if (this.$route.params.cppt_id) {
            this.getMyCppt(this.$route.params.cppt_id)
        }
        this.getOptSignTime()
        this.getOptCatInstruction()
        this.getSignaturaUnits()
    },
};

</script>
<style scope>

@media print {
    @page {
        size: A4;
        margin: 16mm;
    }
    td {
        padding: 2px 0;
        border: 0;
    }
}

td {
    padding: 2px 0;
}

p {
    margin: 0;
    padding: 0;
}

.main-content {
    margin-top: 32px;
    padding-bottom: 32px;
}

.canvas-wrapper {
    display: flex;
    justify-content: center;
    padding-bottom: 40px;
}

/* li {
    position: relative;
    padding-left: 1.5rem;
    margin-bottom: 0.25rem;
    display: block;
}

li::before {
    content: "•";
    position: absolute;
    left: 0.5rem;
    color: #000;
} */
</style>