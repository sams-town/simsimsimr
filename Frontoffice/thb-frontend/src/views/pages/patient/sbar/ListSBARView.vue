<template>
    <div>
        <div class="row mt-12">
            <div class="col-lg-3 pr-0">
                <b-tabs v-model="activeTab" @input="onTabChange" small content-class="mt-3 text-regular-12" nav-wrapper-class="fs-13"
                    nav-class="custom-tab-style">
                    <b-tab title="SEMUA" active>
                        <div 
                            :style="{
                                'min-height': '350px',
                                'max-height': scrollMaxHeight,
                                'overflow-y': 'auto',
                                'overflow-x': 'hidden',
                                'position': 'relative'
                            }"
                        >
                            <div v-for="(item, index) in sbar_list"
                                @click="handleItemClick(index, 'view', item, 'semua')"
                                class="card pointer sbar-card"
                                :class="{ 
                                    'highlighted': highlightedCardId.semua === item.id,
                                    'bg-white': highlightedCardId.semua !== item.id,
                                    'mt-10' : index >= 0,
                                }" 
                                :key="'all-' + index"
                                :ref="el => itemRefsAll[index] = el"
                            >   
                                <div v-if="item.decline_reason != null" class="ribbon ribbon-reject" v-b-tooltip.hover :title="item.decline_reason">Ditolak</div>
                                <div v-else-if="item.decline_reason == null && item.verification_time" class="ribbon ribbon-approve" v-b-tooltip.hover title="Terverifikasi">Terverifikasi</div>
                                
                                <div class="card-body p-12">
                                    <div class="text-uppercase fs-14" style="font-weight: 700;">SBAR {{ item.group_name }}</div>
                                    <div>{{ item.user_added_name }} ({{ item.user_added_role }})</div>
                                    <div>{{ item.date_added }}</div>
                                    <div>{{ item.health_facility_item_name }}</div>
                                    <div class="mt-8"> 
                                        <template v-if="availableToEdit(item)">
                                            <b-button @click="deleteSBAR({item})" size="sm" variant="danger-500" class="mr-1" v-b-tooltip.hover.top="'Hapus SBAR'">
                                                <feather-icon icon="TrashIcon" size="12" />                                                 
                                            </b-button>
                                            <b-button 
                                                @click="handleItemClick(index, 'edit', 
                                                {
                                                    registration_destination_id: item.registration_destination_id,
                                                    sbar_id: item.id,
                                                },'semua')"
                                                size="sm" 
                                                variant="warning-400" 
                                                class="mr-1"
                                                v-b-tooltip.hover.top="'Edit SBAR'"
                                            >
                                                <feather-icon icon="EditIcon" size="12" />                                                 
                                            </b-button>
                                        </template>
                                        <template v-if="userData.user_group.id == jkn_group_id || userData.user_group.id == admin_group_id">
                                            <router-link :to="`/patient/sbar/print/${item.id}`" target="_blank">
                                                <b-button size="sm" variant="w3green" class="mr-1" v-b-tooltip.hover.top="'Cetak SBAR'">
                                                    <feather-icon icon="PrinterIcon" size="12" />                                                 
                                                </b-button>
                                            </router-link>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </b-tab>
                    <b-tab title="DOKTER">
                        <div 
                            :style="{
                                'min-height': '350px',
                                'max-height': scrollMaxHeight,
                                'overflow-y': 'auto',
                                'overflow-x': 'hidden',
                                'position': 'relative'
                            }"
                        >
                            <div v-for="(item, index) in doctor_sbar_list"
                                @click="handleItemClick(index, 'view', item, 'dokter')"
                                class="card pointer sbar-card"
                                :class="{ 
                                    'highlighted': highlightedCardId.dokter === item.id,
                                    'bg-white': highlightedCardId.dokter !== item.id,
                                    'mt-10' : index > 0 
                                }"  
                                :key="'doctor-' + index"
                                :ref="el => itemRefsDoctor[index] = el"
                            > 
                                <div v-if="item.decline_reason != null" class="ribbon ribbon-reject" v-b-tooltip.hover :title="item.decline_reason">Ditolak</div>
                                <div v-else-if="item.decline_reason == null && item.verification_time" class="ribbon ribbon-approve" v-b-tooltip.hover title="Terverifikasi">Terverifikasi</div>
                                <div class="card-body p-12">
                                    <div class="text-uppercase fs-14" style="font-weight: 700;">SBAR {{ item.group_name }}</div>
                                    <div>{{ item.user_added_name }} ({{ item.user_added_role }})</div>
                                    <div>{{ item.date_added }}</div>
                                    <div>{{ item.health_facility_item_name }}</div>
                                    <div class="mt-8"> 
                                        <template v-if="availableToEdit(item)">
                                            <b-button @click="deleteSBAR({item})" size="sm" variant="danger-500" class="mr-1" v-b-tooltip.hover.top="'Hapus SBAR'">
                                                <feather-icon icon="TrashIcon" size="12" />                                                 
                                            </b-button>
                                            <b-button 
                                                @click="handleItemClick(index, 'edit', 
                                                {
                                                    registration_destination_id: item.registration_destination_id,
                                                    sbar_id: item.id,
                                                },'dokter')"
                                                size="sm" 
                                                variant="warning-400" 
                                                class="mr-1"
                                                v-b-tooltip.hover.top="'Edit SBAR'"
                                            >
                                                <feather-icon icon="EditIcon" size="12" />                                                 
                                            </b-button>
                                        </template>
                                        <template v-if="userData.user_group.id == jkn_group_id || userData.user_group.id == admin_group_id">
                                            <router-link :to="`/patient/sbar/print/${item.id}`" target="_blank">
                                                <b-button size="sm" variant="w3green" class="mr-1" v-b-tooltip.hover.top="'Cetak SBAR'">
                                                    <feather-icon icon="PrinterIcon" size="12" />                                                 
                                                </b-button>
                                            </router-link>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </b-tab>
                    <b-tab title="PERAWAT">
                        <div 
                            :style="{
                                'min-height': '350px',
                                'max-height': scrollMaxHeight,
                                'overflow-y': 'auto',
                                'overflow-x': 'hidden',
                                'position': 'relative'
                            }"
                        >
                            <div v-for="(item, index) in nurse_sbar_list"
                                @click="handleItemClick(index, 'view', item, 'perawat')"
                                class="card pointer sbar-card"
                                :class="{
                                    'highlighted': highlightedCardId.perawat === item.id,
                                    'bg-white': highlightedCardId.perawat !== item.id,
                                    'mt-10' : index > 0 
                                }"  
                                :key="'nurse-' + index"
                                :ref="el => itemRefsNurse[index] = el"
                            >
                                <div v-if="item.decline_reason != null" class="ribbon ribbon-reject" v-b-tooltip.hover :title="item.decline_reason">Ditolak</div>
                                <div v-else-if="item.decline_reason == null && item.verification_time" class="ribbon ribbon-approve" v-b-tooltip.hover title="Terverifikasi">Terverifikasi</div>
                                <div class="card-body p-12">
                                    <div class="text-uppercase fs-14" style="font-weight: 700;">SBAR {{ item.group_name }}</div>
                                    <div>{{ item.user_added_name }} ({{ item.user_added_role }})</div>
                                    <div>{{ item.date_added }}</div>
                                    <div>{{ item.health_facility_item_name }}</div>
                                    <div class="mt-8"> 
                                        <template v-if="availableToEdit(item)">
                                            <b-button @click="deleteSBAR({item})" size="sm" variant="danger-500" class="mr-1" v-b-tooltip.hover.top="'Hapus SBAR'">
                                                <feather-icon icon="TrashIcon" size="12" />                                                 
                                            </b-button>
                                            <b-button 
                                                @click="handleItemClick(index, 'edit', 
                                                {
                                                    registration_destination_id: item.registration_destination_id,
                                                    sbar_id: item.id,
                                                },'perawat')"
                                                size="sm" 
                                                variant="warning-400" 
                                                class="mr-1"
                                                v-b-tooltip.hover.top="'Edit SBAR'"
                                            >
                                                <feather-icon icon="EditIcon" size="12" />                                                 
                                            </b-button>
                                        </template>
                                        <template v-if="userData.user_group.id == jkn_group_id || userData.user_group.id == admin_group_id">
                                            <router-link :to="`/patient/sbar/print/${item.id}`" target="_blank">
                                                <b-button size="sm" variant="w3green" class="mr-1" v-b-tooltip.hover.top="'Cetak SBAR'">
                                                    <feather-icon icon="PrinterIcon" size="12" />                                                 
                                                </b-button>
                                            </router-link>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </b-tab>
                    <b-tab title="LAINNYA">
                        <div 
                            :style="{
                                'min-height': '350px',
                                'max-height': scrollMaxHeight,
                                'overflow-y': 'auto',
                                'overflow-x': 'hidden',
                                'position': 'relative'
                            }"
                        >
                            <div v-for="(item, index) in others_sbar_list"
                                @click="handleItemClick(index, 'view', item, 'lainnya')"
                                class="card pointer sbar-card"
                                :class="{
                                    'highlighted': highlightedCardId.lainnya === item.id,
                                    'bg-white': highlightedCardId.lainnya !== item.id,
                                    'mt-10' : index > 0 
                                }"   
                                :key="'others-' + index"
                                :ref="el => itemRefsOthers[index] = el"
                            >
                                <div v-if="item.decline_reason != null" class="ribbon ribbon-reject" v-b-tooltip.hover :title="item.decline_reason">Ditolak</div>
                                <div v-else-if="item.decline_reason == null && item.verification_time" class="ribbon ribbon-approve" v-b-tooltip.hover title="Terverifikasi">Terverifikasi</div>
                                <div class="card-body p-12">
                                    <div class="text-uppercase fs-14" style="font-weight: 700;">SBAR {{ item.group_name }}</div>
                                    <div>{{ item.user_added_name }} ({{ item.user_added_role }})</div>
                                    <div>{{ item.date_added }}</div>
                                    <div>{{ item.health_facility_item_name }}</div>
                                    <div class="mt-8"> 
                                        <template v-if="availableToEdit(item)" class="card-footer">
                                            <b-button @click="deleteSBAR({item})" size="sm" variant="danger-500" class="mr-1" v-b-tooltip.hover.top="'Hapus SBAR'">
                                                <feather-icon icon="TrashIcon" size="12" />                                                 
                                            </b-button>
                                            <b-button 
                                                @click="handleItemClick(index, 'edit', 
                                                {
                                                    registration_destination_id: item.registration_destination_id,
                                                    sbar_id: item.id,
                                                },'lainnya')"
                                                size="sm" 
                                                variant="warning-400" 
                                                class="mr-1"
                                                v-b-tooltip.hover.top="'Edit SBAR'"
                                            >
                                                <feather-icon icon="EditIcon" size="12" />                                                 
                                            </b-button>
                                        </template>
                                        <template v-if="userData.user_group.id == jkn_group_id || userData.user_group.id == admin_group_id">
                                            <router-link :to="`/patient/sbar/print/${item.id}`" target="_blank">
                                                <b-button size="sm" variant="w3green" class="mr-1" v-b-tooltip.hover.top="'Cetak SBAR'">
                                                    <feather-icon icon="PrinterIcon" size="12" />                                                 
                                                </b-button>
                                            </router-link>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </b-tab>
                </b-tabs>
            </div>
            <div class="col-lg-9">
                <template v-if="selected != ''">
                    <ApexaSpinner :visible="isLoading" />
                    <template v-if="!isLoading">
                        <div class="card" id="sbar-content-selected">
                            <div class="card-body fs-12 py-12">
                                <!-- <div class="row d-flex align-items-stretch"> -->
                                <div class="row">
                                    <div class="col-lg-4 px-8">
                                        <div class="fs-14 font-weight-bold bg-sBrown py-1 px-8 text-white">SBAR</div>
                                        <section class="mt-8">
                                            <div class="font-weight-bold text-sBrown">SBAR:</div>
                                            <div v-html="parsedBody(selected?.sbar || '-')"></div>                                                
                                        </section>
                                        <section class="mt-8">
                                            <div class="font-weight-bold text-sBrown">ADVICE:</div>
                                            <div v-html="parsedBody(selected?.advice || '-')"></div>                                                
                                        </section>
                                    </div>

                                    <template v-if="selected?.user_added_group_id == doctor_group_id || selected?.user_added_group_id == admin_group_id">
                                        <div class="col-lg-4 px-8">
                                            <div class="fs-14 font-weight-bold bg-sBrown py-1 px-8 text-white">LABORATORIUM</div>
                                            <div class="mt-8">
                                                <template v-if="selected?.laboratory_request?.length > 0">
                                                    <div v-for="(il, i) in selected.laboratory_request" class="mb-12">
                                                        <div class="font-weight-bold">{{localDateTime(il.date_request)}}</div>
                                                        <li v-for="(list, j) in il.laboratory_request_items">
                                                            <template v-if="list.result?.length > 0">
                                                                <feather-icon icon="EyeIcon" @click="triggerLabResult({ alias_name: patient.alias_name, name: patient.name, result: list.result, costing_name: list.costing_name, laboratory_request_id: list.laboratory_request_id })" color="blue" v-b-tooltip.hover.left="`Lihat hasil ${list.costing_name}`"/>
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

                                        <div class="col-lg-4 px-8">
                                            <div class="fs-14 font-weight-bold bg-sBrown py-1 px-8 text-white">RADIOLOGI</div>
                                            <div class="mt-8">
                                                <template v-if="selected?.radiology_request?.length > 0">
                                                    <template v-for="(ir, i) in selected.radiology_request">
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

                                        <div class="col-lg-4 px-8 mt-12">
                                            <div class="fs-14 font-weight-bold bg-sBrown py-1 px-8 text-white">FISIOTERAPI</div>
                                            <div class="mt-8">
                                                <template v-if="selected?.physiotherapy_request?.length > 0">
                                                    <template v-for="(phy, i) in selected.physiotherapy_request">
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
        
                                        <div class="col-lg-8 px-8 mt-12">
                                            <div class="fs-14 font-weight-bold bg-sBrown py-1 px-8 text-white">RESEP</div>
                                            <div class="mt-8">
                                                <template v-if="selected?.prescription?.length > 0">
                                                    <div v-for="(ipre, i) in selected.prescription" class="mt-8">
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
                                                    <li v-for="inf in selected?.kardex_non_pharmacological">
                                                        <div v-html="parsedBody(inf.name || '-')"></div>
                                                    </li>
                                                    <div v-if="selected?.plan" v-html="parsedBody(selected?.plan || '-')"></div>
                                                </div>
                                            </div>
                                            <div v-if="selected?.pharmacological_plan" class="mt-12">
                                                <div class="font-weight-bold">FARMAKOLOGI:</div>
                                                <div v-html="parsedBody(selected?.pharmacological_plan || '-')"></div>
                                            </div>
                                        </div>
                                    </template>

                                    <div class="col-12 px-8 mt-12">
                                        <div class="fs-14 font-weight-bold bg-sBrown py-1 px-8 text-white">VERIFIKASI</div>
                                        <div class="row mt-8">
                                            <div class="col-lg-6 border-right">
                                                <div>
                                                    <div class="font-weight-bold">Nama Pelapor</div>
                                                    <div>{{ selected?.user_added_name || '-' }} ({{ selected?.user_added_role }})</div>
                                                </div>

                                                <div class="mt-8">
                                                    <div class=" font-weight-bold">Waktu Menulis SBAR</div>
                                                    <div>{{ localDateTime(selected?.date_added) }}</div>
                                                </div>

                                                <div class="mt-8">
                                                    <div class="font-weight-bold">Tanda Tangan Pelapor</div>
                                                    <div class="">
                                                        <img :src="urlbe+selected?.user_added_ttd" alt="Tanda Tangan" height="80" v-if="selected?.user_added_ttd"/>
                                                        <div v-else>-</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div>
                                                    <div class="font-weight-bold">Nama Dokter yang Dikonsulkan</div>
                                                    <div>{{ selected?.doctor_in_charge_name_sbar || '-' }}</div>
                                                </div>

                                                <div class="mt-8">
                                                    <div>
                                                        <b-icon icon="check-circle-fill" variant="success" font-scale="1" v-if="selected?.verification_time" />
                                                        <span class="font-weight-bold">Waktu Verifikasi</span>
                                                    </div>
                                                    <div>{{ localDateTime(selected?.verification_time) }}</div>
                                                </div>

                                                <div class="mt-8">
                                                    <div class="font-weight-bold">Tanda Tangan Dokter yang Dikonsulkan</div>
                                                    <div class="">
                                                        <img :src="urlbe+selected?.doctor_in_charge_ttd_sbar" alt="Tanda Tangan" height="80" v-if="selected?.doctor_in_charge_ttd && selected?.verification_time"/>
                                                        <div v-else>-</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-12 mt-8">
                                                <div v-if="selected.decline_reason != null">
                                                    <div>
                                                        <b-icon icon="x-circle-fill" variant="danger" font-scale="1" />
                                                        <span class="font-weight-bold">Alasan Penolakan:</span>
                                                    </div>
                                                    <p class="text-danger">{{ selected.decline_reason }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>    
                    </template>
                </template>
                <div v-else class="card d-flex justify-content-center align-items-center h-100">
                    <ApexaSpinner :visible="isLoading" />
                    <template v-if="!isLoading">
                        <i class="fs-12">Silakan pilih SBAR yang ingin dilihat dari daftar di samping</i>
                    </template>
                </div>
            </div>
        </div>
        <LabResultModalView ref="labModalResult"/>
        <RadResultModalView ref="radModalResult"/>
        <AddingSBARView ref="childSbarRef" @refreshAssessment="getAssessment" @hidesbarcontent="hideSBARContents"/>
    </div>
</template>

<script>
    import dateMixin from "@/mixins/dateMixin";
    import optionMixin from '@/mixins/optionMixin';
    import LabResultModalView from '../medical_support/LabResultModalView.vue';
    import RadResultModalView from '../medical_support/RadResultModalView.vue';
    import AddingSBARView from '@/views/pages/patient/sbar/AddingSbarView.vue';
    import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'

    export default {
        mixins: [optionMixin, dateMixin],
        components: {
            LabResultModalView, RadResultModalView, AddingSBARView
        },

        data() {
            return {
                userData: JSON.parse(localStorage.getItem('userData')),
                admin_id: 'd77142c4-4777-47d8-bd0c-fd7dfd134af4',
                doctor_group_id: 'efb3fdff-fbe3-4954-a391-3729e544b62a',
                nurse_group_id: '29644626-5b54-4d54-ab69-22146a631277',
                admin_group_id: 'ddad321c-a9d8-430c-95f0-de38d20834a1',
                jkn_group_id: 'a4c04d8d-12d8-4c54-afca-69de6bd5f62d',
                contentHeight:350,
                selected: '',
                highlightedIndexAll: null,
                highlightedIndexDoctor: null,
                highlightedIndexNurse: null,
                highlightedIndexOthers: null,
                itemRefsAll: [],
                itemRefsDoctor: [],
                itemRefsNurse: [],
                itemRefsOthers: [],
                urlbe:process.env.VUE_APP_IMAGE,

                activeTab: 0,
                TABS: {
                    SEMUA: 'semua',
                    DOKTER: 'dokter',
                    PERAWAT: 'perawat',
                    LAINNYA: 'lainnya'
                },
                highlightedCardId: {
                    semua: null,
                    dokter: null,
                    perawat: null,
                    lainnya: null,
                },

                isLoading: false,
            }
        },

        props: {
            patient: {
                type: Object,
            },
            sbar_list: {
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
            // 'sbar_list': function(val){
            //     if (val) {
            //         // this.hideSBARContents()
            //     }
            // },
            'selected'() {
                this.$nextTick(this.updateMaxHeight);
            }
        },

        computed:{
            doctor_sbar_list() {
                if(this.sbar_list.length > 0) return this.sbar_list.filter(group => group.group_id == this.doctor_group_id)
                return []
            },
            nurse_sbar_list() {
                if(this.sbar_list.length > 0) return this.sbar_list.filter(group => group.group_id == this.nurse_group_id)
                return []                    
            },
            others_sbar_list() {
                if(this.sbar_list.length > 0) return this.sbar_list.filter(group => group.group_id != this.doctor_group_id && group.group_id != this.nurse_group_id)
                return []
            },
            scrollMaxHeight() {
                return `${this.contentHeight}px`;
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

            availableToEdit(obj){
                if (this.patient.soap_registration_destination_id != this.patient.registration_destination_id) return false
                if (obj.decline_reason == null && obj.validation_time == null && obj.verification_time == null){
                    if (this.userData.employee_id == this.admin_id) return true
                    if (this.userData.user_id == obj.user_added) return true
                }
                return false
            },

            handleItemClick(index, action, item='', listType) {
                if (item && item.id) {
                    this.highlightedCardId[this.TABS[listType.toUpperCase()]] = item.id;
                }

                if (action == 'edit') {
                    this.triggerSBARModal(item)
                } else {
                    if (this.isLoading == false) this.showThisSBAR(item.id)                    
                }
                // Scroll ke item yang diklik
                this.$nextTick(() => {
                    const refsMap = {
                        semua: this.itemRefsAll,
                        dokter: this.itemRefsDoctor,
                        perawat: this.itemRefsNurse,
                        lainnya: this.itemRefsOthers,
                    };

                    const el = refsMap[listType]?.[index];
                    // const el = this.itemRefs[index];
                    if (el) {
                        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                });
            },
            onTabChange(newTabKey) {
                this.activeTab = newTabKey;
                this.hideSBARContents()
            },

            async showThisSBAR(sbar_id) {
                try {
                    this.isLoading = true
                    await this.$store.dispatch('sbar/getBySBARId', sbar_id).then(response => {
                        this.selected = response[0] || null                    
                    }, error => {
                        console.error('Error while GET sbar/getBySBARId at: ', error);
                    })
                } catch (error) {
                    console.error('Error catch at: ', error)
                } finally {
                    this.isLoading = false                    
                }
                
            },

            hideSBARContents() {
                this.selected = ''
                Object.keys(this.highlightedCardId).forEach(key => {
                    this.highlightedCardId[key] = null;
                });
                // this.highlightedCardId = null;
            },

            deleteSBAR(obj) {
                this.$bvModal
                    .msgBoxConfirm(`Apakah anda ingin menghapus SBAR - ${obj.item.user_added_name || ''} ${obj.item.user_added_role || ''}
                - ${obj.item.date_added || ''}
                - ${obj.item.health_facility_item_name || ''} ?`, {
                    title: 'Konfirmasi', size: 'sm', okVariant: 'danger', okTitle: 'Ya, Hapus', cancelTitle: 'Batal', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
                })
                .then(value => {
                    if (value === true) {
                        this.$store.dispatch('sbar/removeSBAR', obj.item.id)
                        .then(
                            response => {
                                this.getAssessment()
                                this.hideSBARContents()
                                this.notification('Success', 'CheckIcon', 'success', 'SBAR Berhasil di Hapus')
                            },
                            error => {
                                this.notification('Failed', 'CloseIcon', 'danger', 'SBAR Gagal di Hapus')
                                console.error('Error While Delete Using sbar/removeSBAR at: ', error)
                            }
                        )
                        .catch(({message, debug, response}) => {
                            this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                        })
                    }
                })
            },

            getAssessment() {
                let message = 'initial dulu'
                this.$emit("refreshAssessment", message);
            },

            triggerRadResult(message) {
                this.$refs.radModalResult.seeRadiologyResult(message);
            },

            triggerLabResult(message) {
                this.$refs.labModalResult.seeLaboratoryResult(message);
            },

            triggerSBARModal(obj) {
                const message = {
                    gate: 'edit_gate',
                    registration_destination_id: obj.registration_destination_id,
                    sbar_id: obj.sbar_id,
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
                }
                this.$refs.childSbarRef.parentTrigger(message);
            },

            updateMaxHeight() {
                const contentEl = document.getElementById('sbar-content-selected');
                if (contentEl) {
                    this.contentHeight = contentEl.clientHeight - 45;
                }
            },
            notification(a, b, c, d) {
                this.$toast({
                    component: ToastificationContent,
                    position: 'top-right',
                    props: { title: a, icon: b, variant: c, text: d }
                })
            },

            parsedBody(text){
                return String(text || '').replace(/\n/g, "<br>");
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

    .highlighted {
        background-color: #ede0d4;
        border: 1px solid #a58e7c;
        box-shadow: 0 0 5px rgba(165, 142, 124, 0.5);
        transition: all 0.2s ease;
        border-radius: 6px;
    }

    .sbar-card{
        position: relative;
        overflow: visible !important;
    }

    .sbar-card:hover {
        border: 1px solid #a58e7c;
    }

    .ribbon {
        font-size: 0.75rem;
        font-weight: bold;
        color: #fff;
    }
    .ribbon-reject {
        --f: .5em; /* control the folded part */
        
        position: absolute;
        top: 0;
        right: 0;
        line-height: 24px;
        padding-inline: 1lh;
        padding-bottom: var(--f);
        border-image: conic-gradient(#0008 0 0) 51%/var(--f);
        clip-path: polygon(
            100% calc(100% - var(--f)),100% 100%,calc(100% - var(--f)) calc(100% - var(--f)),var(--f) calc(100% - var(--f)), 0 100%,0 calc(100% - var(--f)),999px calc(100% - var(--f) - 999px),calc(100% - 999px) calc(100% - var(--f) - 999px));
        transform: translate(calc((1 - cos(45deg))*100%), -100%) rotate(45deg);
        transform-origin: 0% 100%;
        background-color: #dc3545; /* the main color  */
    }

    .ribbon-approve {
        --f: .5em; /* control the folded part */
        
        position: absolute;
        top: 0;
        right: 0;
        line-height: 24px;
        padding-inline: 1lh;
        padding-bottom: var(--f);
        border-image: conic-gradient(#0008 0 0) 51%/var(--f);
        clip-path: polygon(
            100% calc(100% - var(--f)),100% 100%,calc(100% - var(--f)) calc(100% - var(--f)),var(--f) calc(100% - var(--f)), 0 100%,0 calc(100% - var(--f)),999px calc(100% - var(--f) - 999px),calc(100% - 999px) calc(100% - var(--f) - 999px));
        transform: translate(calc((1 - cos(45deg))*100%), -100%) rotate(45deg);
        transform-origin: 0% 100%;
        background-color: #03a972; /* the main color  */
    }
</style>