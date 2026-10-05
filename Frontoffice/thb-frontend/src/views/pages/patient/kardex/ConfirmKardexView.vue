<template>
    <div>
        <!-- <ApexaSpinner /> -->        
        <b-modal ref="confirm_kardex" size="lg" scrollable no-close-on-backdrop no-close-on-esc>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    KONFIRMASI RESEP KONSULTASI
                    <span class="text-primary">{{ regData.alias_name }}. {{ regData.name }} | {{ regData.norm }} | {{localDate(regData.birth_date)}} ({{ parsedAge(regData.birth_date) }}) | {{ regData.health_facility_item_name }}</span>
                </div>
                <feather-icon v-if="active.section == 'first'" icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>
            
            <ApexaSpinner :visible="active.loading" />
            <section v-if="!active.loading && active.section == 'first'">
                <ModernLabel :label="`Ditambahkan pada ${localDateTime(kardex_data.date_added)}`" variant="w3green" />

                <b-table-simple responsive small class="fs-12 mt-10 bg-white">
                    <b-tr>
                        <b-th v-if="userLogin == 'dpjp'" style="width: 70px;">Disetujui</b-th>
                        <b-th>Nama</b-th>
                        <b-th style="width: 70px;">Kemasan</b-th>
                        <b-th>Signatura</b-th>
                        <b-th>Administer</b-th>
                    </b-tr>
                    <template v-for="(category, catIndex) in kardex_data.kardex_parent" v-if="hasItemsInCategory(kardex_data.kardex_item, category.id)">
                        <b-tr>
                            <b-th v-if="userLogin == 'dpjp'" :class="getPrescParentVariant(category.id)"></b-th>
                            <b-th colspan="4" :class="getPrescParentVariant(category.id)">
                                <div class="text-uppercase ">{{ getPrescParentName(category.id) }}</div>
                                <div v-if="category.id != 1" class="fs-12 text-italic" style="font-weight: 400">  
                                    <span>Kemasan: {{ getParentPackageName(category?.package) }}, </span>
                                    <span>
                                        {{ category.dose }} 
                                        {{ getDynamicOptName(category.dose_unit_id, optSignUnits)}} / 
                                        TIAP {{ getDynamicOptName(category.signatura_times_id, optSignTime)}}
                                        {{ getDynamicOptName(category.administer_id, optAdminister) }}
                                        <span v-if="category.administer_id == other_administer">: {{ category.desc }}</span>
                                    </span>
                                </div>
                            </b-th>
                        </b-tr>
                        <template v-for="(item, itemIndex) in kardex_data.kardex_item">
                            <!-- <b-tr :key="itemIndex" v-if="item.prescription_item_type_id == category.id" :class="{'bg-orange-400': item?.is_annulment == 1, 'bg-warning-400': item?.is_change == 1}"> -->
                            <b-tr :key="itemIndex" v-if="item.prescription_item_type_id == category.id" :class="{'bg-warning-400': item?.is_change == 1}">
                                <b-td v-if="userLogin == 'dpjp'">
                                    <div class="text-center">
                                        <b-form-checkbox
                                            v-if="item.is_change == 1"
                                            v-model="item.is_confirm"
                                            :value="true"
                                            :unchecked-value="false"
                                            >
                                            <!-- {{ item.is_confirm }} -->
                                        </b-form-checkbox>
                                    </div>
                                </b-td>
                                <b-td>
                                    <div>{{ item.pharmacy_catalog_name }}</div>
                                    <div class="d-flex">
                                        <div>
                                            <b-badge v-if="item?.status" :variant="getKardexItemStatusVariant(item?.status)" class="text-uppercase mr-1">{{ getKardexItemStatusName(item?.status) }}</b-badge>
                                        </div>
                                        <small class="font-italic" :class="{'text-neutral-500': !item.is_change == 1}">
                                            <div>ditambahkan pada {{ localDateTime(item.catalog_date) }} oleh {{ item.doctor_added_name || '-' }}</div>
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

                    <div class="pl-16">
                        <li v-for="unit in kardex_data?.kardex_non_pharmacological">
                            <span>{{ unit.name }} </span>
                            <small class="fs-11 font-italic text-neutral-500">(ditambahkan oleh {{ unit.doctor_added_name || '-' }})</small>
                        </li>
                    </div>
                </div>
                <!-- <pre>{{ kardex_data }}</pre> -->
            </section>

            <section v-if="!active.loading && active.section == 'second'" class="row">
                <div class="col-lg-4">
                    <label class="fs-13 mb-0">Dokter yang dikonsulkan</label>
                    <v-select v-model="form_sbar.doctor_in_charge_id_sbar" :options="optAllDoctor" :reduce="doctor => doctor.value" label="title" placeholder="pilih" class="select-size-sm" />
                </div>
                <div class="col-lg-3">
                    <label class="fs-13 mb-0">Konsultasi via</label>
                    <v-select v-model="form_sbar.consultation_via_type_id" :options="optConsultVia" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                </div>
                <div v-if="form_sbar.consultation_via_type_id == 3" class="col-lg-3">
                    <label class="fs-13 mb-0">Konsultasi Lainnya</label>
                    <b-form-input v-model="form_sbar.consultation_via_anything_else" type="text" size="sm" autocomplete="off" />
                </div>
                <div class="col-12 mt-8">
                    <label class="fs-13 mb-0">SBAR</label>
                    <textarea v-model.lazy="form_sbar.sbar" rows="3" class="form-control form-control-sm" autocomplete="off" />
                </div>
                <div class="col-12 mt-8">
                    <label class="fs-13 mb-0">Advice</label>
                    <textarea v-model.lazy="form_sbar.advice" rows="3" class="form-control form-control-sm" autocomplete="off" />
                </div>                        
            </section>

            <section v-if="!active.loading && active.section == 'third'">
                <div class="row" style="padding-left: 0; padding-right: 0;">
                    <div class="col-lg-3 pr-0">
                        <label class="fs-13 mb-0">Sediaan</label>
                        <v-select v-model="kxStock" @input="openPreParent($event)" :options="optParentStock" :reduce="item => item" label="title" class="select-size-sm" placeholder="sediaan"></v-select>
                    </div>
                    <div class="col-lg">
                        <label class="fs-13 mb-0">Tambah Katalog</label>
                        <v-select v-model="kxCatalog" @input="openPreItem($event)" @search="onSearchCatalog" :options="OptionsCatalog" :reduce="item => item" :filterable="false" label="title" class="select-size-sm" placeholder="pilih obat"></v-select>
                    </div>
                </div>

                <b-table-simple fixed small class="mt-12">
                    <b-thead class="text-center fs-12">
                        <b-tr>
                            <th class="align-middle fs-10" style="width: 40px;">AKSI</th>
                            <th class="align-middle" style="width: 220px;">NAMA OBAT</th>
                            <th class="align-middle fs-10" style="width: 100px;">DITAMBAHKAN OLEH</th>
                            <th class="align-middle" style="width: 135px;">STATUS</th>
                            <th class="align-middle" style="width: 80px;">DOSIS</th>
                            <th class="align-middle fs-10" style="width: 100px;">SATUAN DOSIS / KEMASAN</th>
                            <th class="align-middle" style="width: 150px;">WAKTU</th>
                            <th class="align-middle" style="width: 220px;">ADMINISTER</th>
                        </b-tr>
                    </b-thead>
                    <template v-for="(item_p, index_p) in input_prescription.form_prescription_parent">
                        <b-tr class="fs-11" :class="getPrescParentVariant(item_p.id)">
                            <b-td :colspan="item_p.id == 1 ? 8 : 2" class="font-weight-bold align-middle" :class="item_p.id == 1? 'py-12' : ''">{{ getPrescParentName(item_p.id || '-') }}</b-td>
                            <template v-if="item_p.id != 1">
                                <td></td>
                                <td>
                                    <v-select v-model="item_p.status" :options="optKardexItemStatus" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                                </td>
                                <b-td>
                                    <b-form-input v-model="item_p.value" disabled type="number" min="1" size="sm" autocomplete="off" placeholder="jumlah"/>
                                </b-td>
                                <b-td>
                                    <v-select v-model="item_p.package" :options="optParentPackage" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />                    
                                </b-td>
                                <b-td>
                                    <b-input-group size="sm" prepend="TIAP">                                
                                        <b-form-select v-model="item_p.signatura_times_id" :options="optSignTime" text-field="title" size="sm" />
                                    </b-input-group>
                                </b-td>
                                <b-td>
                                    <v-select @input="updateAdminister(index_p, $event, 'parent')" v-model="item_p.administer_id" :options="optAdminister" :reduce="item => item.value" label="title" placeholder="Administer" class="select-size-sm"/>
                                    <b-form-input v-if="item_p.administer_id == other_administer" v-model="item_p.desc" size="sm" autocomplete="off" class="mt-1"/>
                                </b-td>
                            </template>
                        </b-tr>
                        <template v-for="(item, index) in input_prescription.form_prescription" v-if="item_p.id == item.prescription_type_id">
                            <tr class="cs-table fs-11" :class="{'bg-warning-300': item.is_exist == 1 && item.is_change == 1}">
                                <td class="text-center">
                                    <template v-if="item.is_exist == 0">
                                        <b-button @click="deletePRES(index)" variant="danger-600" size="sm">
                                            <feather-icon icon="TrashIcon" />
                                        </b-button>
                                    </template>
                                    <template v-if="item.is_exist == 1">
                                        <feather-icon icon="InfoIcon" :id="`popover-target-${index}`" color="grey" class="pointer"/>                                                            
                                        <b-popover :target="`popover-target-${index}`" triggers="hover" placement="right">
                                            <template #title>
                                                <div class="fs-10 text-uppercase font-weight-bold">{{ item.name }}</div>
                                            </template>
                                            <div class="fst-italic fs-10">
                                                <li>ditambahkan oleh {{ item.doctor_added_name }}</li>
                                                <li>digunakan selama {{ differenceDate(item.exist_status == 4 ? item.stop_date : getCurrentFullDate(), item.catalog_date) }}</li>
                                            </div>
                                        </b-popover>
                                    </template>
                                </td>
                                <td>{{ item.name }}</td>
                                <td class="text-center">{{ item.doctor_nickname }}</td>
                                <td>
                                    <v-select v-if="item.prescription_type_id == 1" v-model="item.status" :options="optKardexItemStatus" :reduce="item => item.value" :disabled="kxDisabledCriteria(item.is_exist, item.doctor_added)" label="title" placeholder="pilih" class="select-size-sm" />
                                </td>
                                <td>
                                    <b-form-input v-model="item.dose" :disabled="kxDisabledCriteria(item.is_exist, item.doctor_added)" type="number" min="1" size="sm" autocomplete="off" placeholder="dosis" class="kx-input"/>
                                </td>
                                <b-td>
                                    <v-select v-model="item.dose_unit_id" :options="item.dose_unit_options" :reduce="unit => unit.value" :disabled="kxDisabledCriteria(item.is_exist, item.doctor_added)" label="title" placeholder="pilih" class="select-size-sm"
                                    />
                                </b-td>
                                <b-td>
                                    <b-input-group v-if="item.prescription_type_id == 1" size="sm" prepend="TIAP">                                
                                        <b-form-select v-model="item.signatura_times_id" :options="optSignTime" text-field="title" size="sm"/>
                                    </b-input-group>
                                </b-td>
                                <td>
                                    <template v-if="item.prescription_type_id == 1">
                                        <v-select @input="updateAdminister(index, $event, null)" v-model="item.administer_id" :options="optAdminister" :reduce="item => item.value" :disabled="kxDisabledCriteria(item.is_exist, item.doctor_added)" label="title" placeholder="pilih" class="select-size-sm"/>
                                        <b-form-input v-if="item.administer_id == other_administer" v-model="item.desc" :disabled="kxDisabledCriteria(item.is_exist, item.doctor_added)" size="sm" autocomplete="off" class="mt-1"/>
                                    </template>
                                </td>
                            </tr>
                        </template>
                    </template>
                </b-table-simple>
                <!-- <pre>{{ input_prescription }}</pre> -->

                <section class="mt-24">
                    <ModernLabel label="Non Farmakologi" />
                    <template v-if="input_prescription.non_pharmacological?.length > 0">
                        <b-table small bordered :fields="nonPharmaFields" :items="input_prescription.non_pharmacological" class="mt-8 fs-13 text-center">
                            <template #cell(action)="row">
                                <b-button variant="danger-600" size="sm" @click="removeNonPharma(row.index)">
                                    <feather-icon icon="TrashIcon" />
                                </b-button>
                            </template>
                            <template #cell(no)="row">{{ row.index+1 }}</template>
                            <template #cell(name)="row">
                                <div v-html="parsedBody(row.item.name)" class="text-left"></div>
                            </template>
                        </b-table>
                    </template>
                    <div class="mt-12">
                        <textarea v-model.lazy="kxNonPharma" class="form-control form-control-sm" autocomplete="off" />
                        <b-button @click="addNonPharma" size="sm" variant="outline-primary" class="mt-1">Tambah</b-button>
                    </div>
                    <!-- <pre>{{input_prescription.non_pharmacological}}</pre> -->
                </section>
            </section>

            <template #modal-footer>
                <!-- <template> -->
                <template v-if="kardex_data.suggestion_status == 0">
                    <!-- <template v-if="userData.position.id == general_dr_position"> -->
                    <div v-if="userLogin == 'general_doctor'" class="position-relative w-100 m-0">
                        <div class="w-100 text-center">
                            <i class="text-muted fs-12">{{ getActiveProgress() }}</i>
                        </div>

                        <div v-if="active.section == 'first'" class="d-flex justify-content-between w-100 position-absolute" style="top: 0;">
                            <div></div>
                            <b-button @click="takeKardex(true)" :disabled="active.disabled.kardex" size="sm" variant="primary">
                                <span>AMBIL KARDEK</span>
                                <feather-icon icon="ArrowRightIcon" class="ml-2"/>
                            </b-button> 
                        </div>

                        <div v-if="active.section == 'second'" class="d-flex justify-content-end w-100 position-absolute" style="top: 0;">
                            <!-- <b-button @click="onlyChangeSection('first')" :disabled="active.disabled.kardex" size="sm" variant="outline-neutral-500">
                                <feather-icon icon="ArrowLeftIcon" class="mr-1"/>
                                <span>KEMBALI</span>
                            </b-button>  -->
                            <b-button @click="saveSbar()" :disabled="active.disabled.kardex" size="sm" variant="primary">
                                <span>SIMPAN & LANJUTKAN</span>
                                <feather-icon icon="ArrowRightIcon" class="ml-2"/>
                            </b-button> 
                        </div>

                        <div v-if="active.section == 'third'" class="d-flex justify-content-between w-100 position-absolute" style="top: 0;">
                            <b-button @click="onlyChangeSection('second')" :disabled="active.disabled.kardex" size="sm" variant="outline-neutral-500">
                                <feather-icon icon="ArrowLeftIcon" class="mr-1"/>
                                <span>KEMBALI</span>
                            </b-button> 
                            <b-button @click="validateNow()" :disabled="active.disabled.kardex" size="sm" variant="orange-500">
                                <feather-icon icon="SaveIcon" class="mr-2" />
                                <span>SELESAI & VALIDASI</span>
                            </b-button> 
                        </div>
                    </div>

                    <!-- pake regData, bukan kardex_data karena nanti kl ganti DPJP bakalan DPJP baru yang approve/reject -->
                    <!-- <template v-if="regData.doctor_in_charge_id == userData.employee_id"> -->
                    <template v-if="userLogin == 'dpjp'">
                        <div class="d-flex justify-content-end w-100 m-0">
                            <b-button @click="approveOrRejectKardex()" :disabled="active.disabled.kardex" size="sm" variant="primary">
                                <feather-icon icon="SaveIcon" class="mr-1" />
                                <span>KONFIRMASI</span>
                            </b-button>
                        </div>
                        <!-- <div class="d-flex justify-content-end w-100 m-0">
                            <b-button @click="approveOrRejectKardex(0)" :disabled="active.disabled.kardex" size="sm" variant="outline-danger" class="mr-8">
                                <span>TOLAK</span>
                            </b-button>
                            <b-button @click="approveOrRejectKardex(1)" :disabled="active.disabled.kardex" size="sm" variant="primary">
                                <feather-icon icon="SaveIcon" class="mr-1" />
                                <span>KONFIRMASI</span>
                            </b-button>
                        </div> -->
                    </template>
                </template>
                <div v-else></div>
            </template>
        </b-modal>

        <b-modal v-model="active.modal.presc_parent" size="sm" no-close-on-esc no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA AKAN MENAMBAHKAN SEDIAAN
                    <span class="text-primary">{{ temp_presc_parent.parent_name }}</span>
                </div>
                <feather-icon icon="XIcon" @click="closePreParent()" size="18" class="pointer" />
            </template>
            <section>
                <div>
                    <label class="fs-13 mb-0">Kemasan</label>
                    <v-select v-model="temp_presc_parent.package" :options="optParentPackage" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />                    
                </div>
                <div class="mt-10">
                    <label class="fs-13 mb-0">Waktu konsumsi / pemakaian</label>
                    <b-input-group size="sm" prepend="TIAP">                                
                        <b-form-select v-model="temp_presc_parent.signatura_time" :options="optSignTime" text-field="title" size="sm" />
                    </b-input-group>
                </div>
                <div class="mt-10">
                    <label class="fs-13 mb-0">Administer</label>
                    <v-select v-model="temp_presc_parent.administer" :options="optAdminister" :reduce="item => item.value" label="title" placeholder="Administer" class="select-size-sm"/>
                </div>
                <div v-if="temp_presc_parent.administer == other_administer" class="mt-10">
                    <label class="fs-13 mb-0">Administer Lainnya</label>
                    <b-form-input v-model="temp_presc_parent.desc" size="sm" autocomplete="off"/>                    
                </div>
            </section>
            <template #modal-footer>
                <div class="d-flex justify-content-end w-100">
                    <b-button @click="addParent" size="sm" variant="primary">TAMBAHKAN</b-button>
                </div>
            </template>
        </b-modal>

        <b-modal v-model="active.modal.presc_items" size="sm" no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA AKAN MENAMBAHKAN 
                    <span class="text-primary">{{ temp_presc_items.parent_name }} - {{ temp_presc_items.catalog_name }}</span>
                </div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>

            <section>
                <div>
                    <label class="fs-13 mb-0">Signatura</label>
                    <div class="row">
                        <div class="col-lg-4 pr-lg-0">
                            <b-form-input v-model="temp_presc_items.signatura_dose" type="number" size="sm" autocomplete="off" placeholder="dosis"/>
                        </div>
                        <v-select v-model="temp_presc_items.signatura_unit" :options="optItemSignUnits" :reduce="item => item.value" label="title" placeholder="satuan dosis" class="select-size-sm col-lg-4 mt-8 mt-lg-0"/>
                        <b-input-group v-if="temp_presc_items.parent_id == 1" size="sm" prepend="TIAP" class="col-lg-4 pl-lg-0 mt-8 mt-lg-0">                                
                            <b-form-select v-model="temp_presc_items.signatura_time" :options="optSignTime" text-field="title" size="sm" />
                        </b-input-group>
                    </div>
                </div>
                <template v-if="temp_presc_items.parent_id == 1">
                    <div class="mt-10">
                        <label class="fs-13 mb-0">Administer</label>
                        <v-select v-model="temp_presc_items.administer" :options="optAdminister" :reduce="item => item.value" label="title" placeholder="Administer" class="select-size-sm"/>
                    </div>
                    <div v-if="temp_presc_items.administer == other_administer" class="mt-10">
                        <label class="fs-13 mb-0">Administer Lainnya</label>
                        <b-form-input v-model="temp_presc_items.desc" size="sm" autocomplete="off"/>                    
                    </div>
                </template>
            </section>
            <template #modal-footer>
                <div class="d-flex justify-content-end w-100 m-0">
                    <b-button @click="addPrescItem" size="sm" variant="primary">TAMBAHKAN</b-button>
                </div>
            </template>
        </b-modal>

        <b-modal ref="confirmKardexModal" size="ms" no-close-on-backdrop no-close-on-esc>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase text-center w-100">KONFIRMASI PERUBAHAN PADA KARDEKS AKTIF</div>
                <feather-icon icon="XIcon" @click="closePrescModal()" size="18" class="pointer" />
            </template>
            <div class="fs-13">
                <p>{{ getNarativeKrxchanges }}</p>

                <template v-if="kardexChanges?.form_prescription?.length > 0 || kardexChanges?.form_prescription_parent?.length > 0">
                    <b-table-simple small fixed bordered class="fs-11 text-center">
                        <b-tr>
                            <b-th style="width: 40px">NO</b-th>
                            <b-th style="width: 250px" class="text-left">NAMA OBAT</b-th>
                            <b-th>KEMASAN / SATUAN</b-th>
                            <b-th>DOSIS</b-th>
                            <b-th>WAKTU</b-th>
                            <b-th>ADMINISTER</b-th>
                        </b-tr>
                        
                        <template v-if="kardexChanges?.form_prescription?.length > 0">
                            <b-tr v-for="(item, index) in kardexChanges.form_prescription">
                                <b-td>{{ index+1 }}</b-td>
                                <b-td class="text-left">
                                    <div>{{ item.name }}</div>
                                    <b-badge v-if="item.prescription_type_id == 1" :variant="getKardexItemStatusVariant(item.status)" class="text-uppercase">{{ getKardexItemStatusName(item.status) }}</b-badge>
                                </b-td>
                                <b-td>{{ item.package_name }}</b-td>
                                <b-td>{{ item.dose }} {{ getDynamicOptName(item.dose_unit_id, optSignUnits)}}</b-td>
                                <b-td>{{ item.prescription_type_id == 1 ? `TIAP ${getDynamicOptName(item.signatura_times_id, optSignTime)}` : '-' }}</b-td>
                                <b-td>
                                    <template v-if="item.prescription_type_id == 1">
                                        <span>{{ getDynamicOptName(item.administer_id, optAdminister) }}</span>
                                        <span v-if="item.administer_id == other_administer">: {{ item.desc }}</span>                                        
                                    </template>
                                    <div v-else>-</div>
                                </b-td>
                            </b-tr>
                        </template>

                        <template v-if="kardexChanges?.form_prescription_parent?.length > 0">
                            <b-tr v-for="(item, index) in kardexChanges.form_prescription_parent">
                                <b-td>{{ kardexChanges?.form_prescription?.length ? kardexChanges?.form_prescription?.length+index+1 : index+1 }}</b-td>
                                <b-td class="text-left">
                                    <div>{{ getPrescParentName(item.id || '-') }}</div>
                                    <b-badge :variant="getKardexItemStatusVariant(item.status)" class="text-uppercase">{{ getKardexItemStatusName(item.status) }}</b-badge>
                                </b-td>
                                <b-td>{{ getParentPackageName(item.package) }}</b-td>
                                <b-td>{{ item.value }}</b-td>
                                <b-td>TIAP {{ getDynamicOptName(item.signatura_times_id, optSignTime) }}</b-td>
                                <b-td>
                                    <span>{{ getDynamicOptName(item.administer_id, optAdminister) }}</span>
                                    <span v-if="item.administer_id == other_administer">: {{ item.desc }}</span>
                                </b-td>
                            </b-tr>
                        </template>
                    </b-table-simple>
                </template>

                <div v-if="kardexChanges?.non_pharmacological?.length > 0">                    
                    <p class="mb-0 font-weight-bold">NON-FARMAKOLOGI: </p>
                    <div class="pl-16">
                        <li v-for="item in kardexChanges.non_pharmacological">
                            <span>{{ item.name }} </span>
                            <small class="fs-11 font-italic text-neutral-500">(ditambahkan oleh {{ item.doctor_added_name || '-' }})</small>
                        </li>
                    </div>
                </div>
                <!-- <pre>{{ kardexChanges }}</pre> -->
            </div>
            <template #modal-footer>
                <div class="d-flex justify-content-center w-100">
                    <b-button @click="closePrescModal()" size="sm" variant="outline-danger-500" class="px-32">Batal</b-button>
                    <b-button @click="proceedToKardex(active.destination, active.trigger_from)" :disabled="active.disabled.changes_presc" size="sm" variant="w3green" class="ml-1">Simpan & lanjutkan</b-button>
                </div>
            </template>
        </b-modal>
    </div>
</template>

<script>
import { mapState } from 'vuex'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import dateMixin from "@/mixins/dateMixin";
import optionMixin from "@/mixins/optionMixin";

export default {
    mixins: [dateMixin, optionMixin],
    data() {
        return {
            userData: JSON.parse(localStorage.getItem('userData')),
            general_dr_position: 'd93bd445-df1e-451a-bf95-5fc10546c133',
            other_administer: 'ea3c2573-b9f5-428d-ada4-0ce6ea67c72f',
            // admin_group_id: 'ddad321c-a9d8-430c-95f0-de38d20834a1',

            kardex_data: {},
            temp_notification: {},
            regData: {},
            active: {
                disabled: {
                    kardex: false,
                },
                modal: {
                    presc_parent: false,
                    presc_items: false,
                },
                section: '',
                loading: false,
            },
            optSignUnits: [],
            optSignTime: [],
            optAdminister: [],
            optAllDoctor: [],
            userLogin: '',
            form_sbar: {
                id: '',
                registration_destination_id: '',
                doctor_in_charge_id: '',
                doctor_in_charge_id_sbar: '',
                consultation_via_type_id: '',
                consultation_via_anything_else: '',
                sbar: '',
                advice: ''
            },
            kxStock: { value: 1, title: 'OBAT SATUAN' },            
            kxCatalog: '',
            kxNonPharma: '',

            OptionsCatalog: [],
            catalog_list: [],
            original_prescription: {},
            input_prescription: {
                id: '',
                doctor_in_charge_id: '',
                registration_destination_id: '',
                health_facility_item_id: '',
                form_prescription_parent: [{
                    id: 1,
                }],
                form_prescription: [],
                non_pharmacological: [],
                soap_id: '',
                cppt_id: '',
                sbar_id: '',
            },
            combine_empty_found: [],
            kardexChanges: [],
            temp_presc_parent: {
                parent_id: '',
                parent_name: '',

                package: '',
                signatura_time: '',
                administer: '',
                desc: ''
            },
            temp_presc_items: {
                parent_id: '',
                parent_name: '',
                catalog_name: '',
                data_catalog: {},

                signatura_dose: '',
                signatura_unit: '',
                signatura_time: '',
                administer: '',
                desc: ''
            },
            other_administer: 'ea3c2573-b9f5-428d-ada4-0ce6ea67c72f',
            // optSignTime: [],
            optSignDose: [],
            // optSignUnits: [],
            // optAdminister: [],
            optItemSignUnits: [],
            nonPharmaFields: [
                { key: 'action', label: 'AKSI', thStyle: { width: '50px' } },
                { key: 'no', label: 'NO', thStyle: { width: '40px' } },
                { key: 'name', label: 'NON FARMAKOLOGI', thClass: 'text-left' },
                { key: 'doctor_added_name', label: 'DITAMBAHKAN OLEH', thStyle: { width: '200px' } },
            ],
        }
    },

    computed: {
        ...mapState({ 
            optConsultVia: state => state.frontoptions.doctor.sbar.consultation_via,
            optParentStock: state => state.frontoptions.doctor.assessment.prescription.parent_stock,
            optKardexItemStatus: state => state.frontoptions.doctor.assessment.prescription.kardex_item_status,
            optParentPackage: state => state.frontoptions.doctor.assessment.prescription.parent_package,
        }),

        getNarativeKrxchanges() {
            const p = this.kardexChanges?.form_prescription?.length > 0 || 
                    this.kardexChanges?.form_prescription_parent?.length > 0;
            const n = this.kardexChanges?.non_pharmacological?.length > 0;
            
            const types = [];
            if (p) types.push('Resep');
            if (n) types.push('Non-farmakologi');
            
            return types.length ? `${types.join(' dan ')} telah diubah, harap periksa kembali sebelum disimpan.` : '';
        },
    },

    methods: {
        async parentTrigger(obj) {
            this.active.loading = true
            this.$refs.confirm_kardex.show()

            this.active.section = 'first'
            this.kardex_data = await this.arrangeData(obj.kardex)
            this.temp_notification = obj.notification
            // console.log('this.kardex_data: ', this.kardex_data)

            await this.getPatientDetail(obj?.kardex?.registration_destination_id)
            await this.getOptSignTime()
            await this.getOptCatInstruction()
            await this.getSignaturaUnits()
            
            this.userLogin = await this.setUserEnterKardex()
            if (this.userLogin == 'general_doctor') {
                await this.getAllDoctorList()
                await this.getPrescCatalog()
                
                // await this.getMySbar('de453ffa-3fad-4c71-85c7-2d7868501ef2')
                this.form_sbar.doctor_in_charge_id = this.regData.doctor_in_charge_id
                this.form_sbar.registration_destination_id = this.regData.registration_destination_id
                await this.getActiveKardex(this.kardex_data.registration_destination_id)
            }
            
            this.active.loading = false
        },

        async arrangeData(obj_kardex) {
            const { kardex_item, ...rest } = obj_kardex

            kardex_item.sort((a, b) => 
                b.is_change - a.is_change || 
                a.doctor_added_name.localeCompare(b.doctor_added_name) || 
                a.pharmacy_catalog_name.localeCompare(b.pharmacy_catalog_name)
            )
            // sort 3 level, urutannya dari atas kebawah
            // sort biasa yang pake '-', rekomendasi buat number
            // kl bentuknya alphabet, pake localeCompare

            const injectedConfirm = kardex_item.map(item => {
                return item.is_change == 1 
                    ? { ...item, is_confirm: true }
                    : { ...item }
            })
            
            const final = { 
                kardex_item: [...injectedConfirm], 
                ...rest
            }
            return final
        },

        async setUserEnterKardex() {
            // if (this.userData.employee_id == this.regData.doctor_in_charge_id || this.userData.user_group.id == this.admin_group_id) return 'dpjp'
            if (this.userData.employee_id == this.regData.doctor_in_charge_id) return 'dpjp'
            if (this.userData.position.id == this.general_dr_position) return 'general_doctor'
            return ''
        },

        async getPatientDetail(reg_des_id) {
            const response = await this.$store.dispatch('registration/getById', reg_des_id)
            this.regData = response?.length > 0 ? response[0] : []               
        },

        async takeKardex(param) {
            this.active.disabled.kardex = true
            try {
                const response = await this.$store.dispatch('kardex/putTakeOrder', {kardex_id: this.kardex_data.id})
                if (param == true) {
                    this.notification('Success','CheckIcon','success','Berhasil mengambil order kardeks')
                    this.active.section = 'second'
                    this.$emit('refresh')
                }
            } catch (error) {
                const { message, debug, response } = error
                this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                console.error('Error catch while GET kardex/putTakeOrder at: ', error);
            } finally {
                this.active.disabled.kardex = false
            }
        },

        async approveOrRejectKardex() {
            await this.takeKardex(false)
            this.active.disabled.kardex = true

            const kardex_obj = this.kardex_data
            let final_obj = {
                kardex_id: kardex_obj.id,
                is_approve: 1,
                reject_items: [],
            }

            const changes = kardex_obj?.kardex_item?.filter(item => item.is_change == 1) || []
            const rejected = changes.filter(item => item.is_confirm == false) || []

            final_obj.is_approve = changes.length == rejected.length ? 0 : 1
            final_obj.reject_items = rejected.map(item => {
                return {
                    id: item.id
                }
            })

            try {
                const response = await this.$store.dispatch('kardex/putApproveKardex', final_obj)
                this.notification('Success','CheckIcon','success',`Berhasil konfirmasi order kardeks`)
                this.$refs.confirm_kardex.hide()
                this.$emit('refresh')
                //ga perlu di read lagi krn udah auto ke read ama BE, jadi langsung trigger refresh notif aja
                // this.$emit('toggleRead', this.temp_notification); 
            } catch (error) {
                const { message, debug, response } = error
                this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                console.error('Error catch while GET kardex/putApproveKardex at: ', error);
            } finally {
                this.active.disabled.kardex = false
            }
        },

        getActiveProgress() {
            const mapping = {
                first: '1',
                second: '2',
                third: '3',
            }
            return `progres ${mapping[this.active.section]}/${Object.keys(mapping).length}` 
        },

        onlyChangeSection(destination) {            
            this.active.section = destination
        }, 

        async saveSbar() {
            this.active.disabled.kardex = true
            // this.active.section = 'third'
            try {
                if (this.form_sbar.doctor_in_charge_id_sbar) {
                    const response = await this.$store.dispatch('sbar/putSaveFull', this.form_sbar )
                    this.notification('Success','CheckIcon','success','Berhasil menyimpan SBAR')
                    this.active.section = 'third'
                    await this.getMySbar(response?.data?.id)
                } else {
                    alert('Dokter yang dikonsulkan wajib diisi')
                }
            } catch (error) {
                const { message, debug, response } = error
                this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                console.error('error at saveSbar: ', error)
            } finally {
                this.active.disabled.kardex = false               
            }
        },

        async getMySbar(sbar_id) {
            try {
                const response = await this.$store.dispatch('sbar/getBySBARId', sbar_id);
                if (response?.length > 0) {
                    const obj_sbar = {...response[0]}
                    this.form_sbar.id = obj_sbar.id
                    this.form_sbar.registration_destination_id = obj_sbar.registration_destination_id
                    this.form_sbar.doctor_in_charge_id = obj_sbar.doctor_in_charge_id
                    this.form_sbar.doctor_in_charge_id_sbar = obj_sbar.doctor_in_charge_id_sbar
                    this.form_sbar.consultation_via_type_id = obj_sbar.consultation_via_type_id
                    this.form_sbar.consultation_via_anything_else = obj_sbar.consultation_via_anything_else
                    this.form_sbar.sbar = obj_sbar.sbar
                    this.form_sbar.advice = obj_sbar.advice
                }
            } catch (error) {
                const { message, debug, response } = error
                console.error('Error details getMySbar:', { message, debug, response })
            }
        },

        async validateNow() {
            this.$bvModal
            .msgBoxConfirm(`Apakah anda ingin menyelesaikan SBAR dan memvalidasinya? (SBAR yang sudah di validasi tidak dapat di-edit)`, {
                title: 'Konfirmasi',
                size: 'sm',
                okVariant: 'orange-500',
                okTitle: 'Ya, Validasi',
                cancelTitle: 'Batal',
                cancelVariant: 'danger',
                hideHeaderClose: false,
                centered: true,
            })
            .then(async value => {
                if (value === true) {
                    this.active.disabled.kardex = true
                    let inp_items = this.input_prescription?.form_prescription || []
                    let inp_parent = this.input_prescription?.form_prescription_parent || [] 

                    // if (inp_parent?.length > 0 && inp_items?.length > 0 && this.userLogin == 'general_doctor') {
                    if (inp_parent?.length > 0 && inp_items?.length > 0) {
                        await this.saveKardex()
                    } else {
                        alert('Resep tidak ditemukan')
                        this.active.disabled.kardex = false
                    }
                }                            
            });
        },

        async saveKardex() {
            try {
                let inp_items = this.input_prescription?.form_prescription || []
                let inp_parent = this.input_prescription?.form_prescription_parent || [] 

                if (inp_parent?.length > 0 && inp_items?.length > 0) {
                    if(this.emptyChecking({
                        parents: inp_parent,
                        items: inp_items
                    })) {
                        const message = "SILAKAN LENGKAPI:\n\n" + this.combine_empty_found.map(item => `- ${item}`).join('\n');
                        alert(message);
                        this.active.disabled.kardex = false
                    } else {
                        for (let i = 0; i < inp_parent.length; i++) {
                            const el = inp_parent[i];
                            if (el.is_exist == 0 && el.id != 1 && el.status == 1) {
                                this.input_prescription.form_prescription_parent[i].status = 2
                            }    
                        }
                        
                        let changes = this.kardexSpesificChanges() 
                        if (changes) {
                            this.kardexChanges = changes
                            this.$refs.confirmKardexModal.show()
                        } else {
                            await this.proceedToKardex()
                        }
                    }
                }  
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at saveKardex: ', error)                
            }
        },

        emptyChecking(obj) {
            const naming = {
                package: "Kemasan/satuan",
                // package_id: "Kemasan/satuan",
                value: "Jumlah",
                dose: "Dosis",
                dose_unit_id: "Satuan Dosis",
                signatura_times_id: "Waktu",
                administer_id: "Administer",
                status: "Status"
            };

            const parent_properties = ['status', 'value', 'package', 'signatura_times_id', 'administer_id']
            const item_unit_properties = ['status', 'dose', 'dose_unit_id', 'signatura_times_id', 'administer_id']
            const item_conc_properties = ['dose', 'dose_unit_id']
            
            let parent_criteria = []
            let parent_part = []
            let item_part = []

            let null_parents = []
            let null_items = []

            let data_items = obj.items || []
            let data_parents = []
            parent_criteria = [...new Set(data_items.map(item => item.prescription_type_id))] // buat dapetin parent mana aja yang punya items
            let compounded_medicine = obj.parents.filter(item => item.id != 1) // ambil yang racikan aja, krn parent obat satuan ga ada inputan dosis dll

            // tambahin `criteria` buat nentuin selanjutnya perlu di cek atau gak 
            for (let idx_c = 0; idx_c < compounded_medicine.length; idx_c++) {
                const el = compounded_medicine[idx_c];
                data_parents.push({
                    ...el,
                    criteria: parent_criteria.includes(el.id)
                })
            }
            
            if (data_parents) {
                for (let idx_p = 0; idx_p < data_parents.length; idx_p++) {
                    parent_part = []
                    const el = data_parents[idx_p]

                    if(el.criteria === true) {
                        for (let i = 0; i < parent_properties.length; i++) {
                            const pp = parent_properties[i];
                            const value = el[pp];
                            const isInvalid = pp === 'value'
                                ? !value || value <= 0 // null, undefined, '', atau <= 0
                                : !value || value == 0 // null, undefined, '', atau == 0
                            
                            if (isInvalid) {
                                parent_part.push(naming[pp]);
                                // console.log('empty property found: ', pp, value);
                            }
                        }

                        // console.log('All empty property found: ', parent_part);
                        if (parent_part.length > 0) {
                            let narrative_parent = `${this.getPrescParentName(el.id)}: ${parent_part.join(', ')}`
                            null_parents.push(narrative_parent)                   
                        }
                    }
                }
                // console.log('null_parents: ', null_parents);
            }

            if (data_items) {
                for (let idx_i = 0; idx_i < data_items.length; idx_i++) {
                    item_part = []
                    const el = data_items[idx_i]
                    let properties = el.prescription_type_id == 1 ? item_unit_properties : item_conc_properties

                    for (let i = 0; i < properties.length; i++) {
                        const ip = properties[i];
                        const value = el[ip];
                        const isInvalid = ip === 'dose'
                            ? !value || value <= 0 // null, undefined, '', atau <= 0
                            : !value || value == 0 // null, undefined, '', atau == 0
                        
                        if (isInvalid) {
                            item_part.push(naming[ip]);
                            // console.log('empty property found: ', ip, value);
                        }
                    }

                    // console.log('All empty property found: ', parent_part);
                    if (item_part.length > 0) {
                        let narrative_item = `${el.name}: ${item_part.join(', ')}`
                        null_items.push(narrative_item)                   
                    }
                }
                // console.log('null_items: ', null_items);
            }
            
            this.combine_empty_found = [...null_parents, ...null_items]
            return this.combine_empty_found.length > 0 ? true : false
        },

        kardexSpesificChanges() {
            const formData = { ...this.input_prescription }
            const originalData = { ...this.original_prescription }

            // ngehapus properti yang gak perlu dibandingin
            const excludedProps = ['id', 'doctor_in_charge_id', 'registration_destination_id', 'health_facility_item_id', 'soap_id', 'cppt_id', 'sbar_id']
            excludedProps.forEach(prop => {
                delete formData[prop]
                delete originalData[prop]
            });

            // Fungsi buat dapetin object lengkap yang berubah
            const getChangedObjects = (before, after) => {
                // Handle array
                if (Array.isArray(before) && Array.isArray(after)) {
                    const changedItems = [];
                    
                    // Cek item yang diubah atau ditambahin
                    after.forEach((afterItem, index) => {
                        if (index < before.length) {
                            // Item udah ada di before, cek apakah berubah
                            const isChanged = isDifferent(before[index], afterItem);
                            if (isChanged) {
                                changedItems.push(afterItem); // Return object lengkap dari after (formData)
                            }
                        } else {
                            // Item baru yang ditambahkan di after
                            changedItems.push(afterItem);
                        }
                    });
                    
                    // Cek item yang dihapus dari before
                    // tp ini harusnya ga akan terjadi karena yg is_exist == 1 itu ga bisa dihapus
                    // jadi kl ada Null nih belom ke handle dari FE, next dah
                    if (before.length > after.length) {
                        for (let i = after.length; i < before.length; i++) {
                            changedItems.push(null); // sementara, kl ada item yang dihapus, push Null dulu
                        }
                    }
                    
                    return changedItems.length > 0 ? changedItems : null;
                }

                // Handle object - return object lengkap dari after kalo ada perubahan
                if (typeof before === 'object' && before !== null && after !== null) {
                    const isChanged = isDifferent(before, after);
                    return isChanged ? after : null;
                }

                // Handle primitive values - return value dari after kalo berbeda
                return String(before ?? '') !== String(after ?? '') ? after : null;
            };

            // Fungsi deep comparison
            const isDifferent = (a, b) => {
                if (typeof a !== typeof b) return true;

                if (Array.isArray(a) && Array.isArray(b)) {
                    if (a.length !== b.length) return true;
                    return a.some((item, index) => isDifferent(item, b[index]))
                }

                if (typeof a === 'object' && a !== null && b !== null) {
                    const keysA = Object.keys(a)
                    const keysB = Object.keys(b)
                    if (keysA.length !== keysB.length) return true
                    return keysA.some(key => isDifferent(a[key], b[key]))
                }

                return String(a ?? '') !== String(b ?? '')
            };

            // Cari perubahan
            const changes = {};
            Object.keys(formData).forEach(key => {
                if (isDifferent(originalData[key], formData[key])) {
                    const changedObjects = getChangedObjects(originalData[key], formData[key]);
                    
                    changes[key] = changedObjects
                    // changes[key] = {
                    //     before: originalData[key],
                    //     after: formData[key],
                    //     changes: changedObjects // Object lengkap dari formData yang berubah
                    // };
                }
            });

            // console.log("Perubahan kardeks berubah:", changes);
            return Object.keys(changes).length > 0 ? changes : false;
        },

        async proceedToKardex() {
            this.active.disabled.changes_presc = true
            this.input_prescription.doctor_in_charge_id ||= this.regData.doctor_in_charge_id;
            this.input_prescription.registration_destination_id ||= this.regData.registration_destination_id
            this.input_prescription.health_facility_item_id = this.regData.health_facility_item_id
            this.input_prescription.sbar_id ||= this.form_sbar.id
            this.input_prescription.cppt_id ||= this.kardex_data.cppt_id

            try {
                const res = await this.$store.dispatch('kardex/putKardex', this.input_prescription);
                await this.orderKardex(res.data.id)
            } catch (error) {
                const { message, debug, response } = error;
                this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
            } finally {
                this.active.disabled.kardex = false
                this.active.disabled.changes_presc = false
            }
        },

        async orderKardex(kardex_id) {
            try {
                let form_order = {
                    kardex_id: kardex_id
                }
                const res = await this.$store.dispatch('kardex/putOrderKardex', form_order);
                await this.getActiveKardex()
                this.$refs.confirmKardexModal.hide()

                await this.validateSBAR()
            } catch (error) {
                const { message, debug, response } = error;
                this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
            }
        },

        async validateSBAR() {
            try {
                // this.active.disabled.kardex = true
                let payload = {
                    validation_status: 1, 
                    sbar_id: this.form_sbar.id
                }

                const res = await this.$store.dispatch('sbar/validateSBAR', payload);
                this.notification('Success','CheckIcon','success','Berhasil Melakukan Validasi SBAR')
                this.$refs.confirm_kardex.hide()
                this.active.section = ''
                
                // await this.getMySbar(res?.data?.id)
                // this.active.modal.main = false
            } catch (error) {
                const { message, debug, response } = error
                this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
            } finally {
                this.active.disabled.kardex = false
                // this.active.modal.main = false
            }
        },

        kxDisabledCriteria(is_exist, user_added) {
            if (this.userLogin == 'general_doctor' || is_exist == 0) return false
            return this.userData.employee_id !== user_added;
        },

        async getActiveKardex(reg_des_id) {
            const param = { 
                registration_destination_id: reg_des_id,
                is_active: 1
            }
            try {
                const response = await this.$store.dispatch('kardex/getKardexList', param);
                let active_kardex = response.rows || []
                await this.fillKardex(active_kardex[0])
            } catch (error) {
                console.error('Error getActiveKardex at: ', error);
            }
        },

        async fillKardex(obj) {
            // console.log('obj: ', obj);
            if (obj) {
                this.input_prescription.id = obj.id
                this.input_prescription.doctor_in_charge_id = obj.doctor_in_charge_id
                this.input_prescription.registration_destination_id = obj.registration_destination_id
                this.input_prescription.cppt_id = obj.cppt_id
                this.input_prescription.health_facility_item_id = this.kardex_data.health_facility_item_id
                this.input_prescription.soap_id = null
                this.input_prescription.cppt_id = this.kardex_data.cppt_id
                this.input_prescription.sbar_id = this.form_sbar.id

                let var_presc = obj.kardex_item || []
                let var_parent = obj.kardex_parent || []
                let var_non = obj.kardex_non_pharmacological || []
                
                if (var_non.length > 0) {
                    const mappedNon = var_non.map(item => {
                        return {
                            id: item.non_pharmacological_id,
                            name: item.name,
                            doctor_added: item.doctor_added ?? '',
                            doctor_added_name: item.doctor_added_name ?? '-',
                        };
                    });
                    this.input_prescription.non_pharmacological = mappedNon;
                } else {
                    this.input_prescription.non_pharmacological = []
                }
                
                if (var_parent.length > 0) {
                    const mappedPrescParent = var_parent.map(item => {
                        if (item.id != 1) {
                            return {
                                id: item.id,
                                status: item?.status || 4,
                                package: Number(item.package),
                                value: Number(item.value ?? 0),
                                signatura_times_id: item.signatura_times_id ?? '',
                                administer_id: item.administer_id ?? '',
                                desc: item.administer_id == this.other_administer ? item?.desc : '',
                                is_exist: 1,
                            };
                        } else {
                            return {
                                id: item.id,
                                is_exist: 1,
                            };
                        }
                    });         
                    mappedPrescParent.sort((a, b) => a.id - b.id);                
                    this.input_prescription.form_prescription_parent = mappedPrescParent;
                } else {
                    this.input_prescription.form_prescription_parent = [{
                        id: 1,
                    }]
                }

                if (var_presc) {
                    const mappedPresc = var_presc.map(item => {
                        let temp_sign = []
                        if (item?.dose_unit_options.length > 0) {
                            const mappedSign = item.dose_unit_options.map(item => {
                                return {
                                    value: item.id,
                                    title: item.name, 
                                };
                            });                
                            temp_sign = mappedSign
                        }

                        return {
                            pharmacy_catalog_id: item.pharmacy_catalog_id, 
                            package_id: item.package_id,
                            prescription_type_id: item.prescription_item_type_id,
                            dose: item.dose,
                            dose_unit_id: item.dose_unit_id,
                            signatura_times_id: item.signatura_times_id,
                            administer_id: item.administer_id,
                            desc: item.administer_id == this.other_administer ? item?.desc : '',
                            sort: item.sort, 
                            status: item.status,

                            is_exist: 1,
                            name: item.pharmacy_catalog_name,
                            package_name: item.package_name,                            
                            catalog_date: item.catalog_date,
                            stop_date: item.stop_date,
                            is_change: item.is_change,
                            exist_status: item.status,
                            doctor_added: item.doctor_added,
                            doctor_added_name: item.doctor_added_name,
                            doctor_nickname: item?.doctor_nickname || '-',
                            id: item.id,
                            dose_unit_options: temp_sign,
                        };
                    });
                    // mappedPresc.sort((a, b) => a.sort - b.sort);                
                    this.input_prescription.form_prescription = mappedPresc;                                  
                } else {
                    this.input_prescription.form_prescription = [{
                        id: 1,
                    }]                                
                }
                await this.cloneAllState()
            }
            // console.log('this.input_prescription: ', this.input_prescription)
        },

        async resetKardex() {
            this.input_prescription.id = ''
            this.input_prescription.doctor_in_charge_id = ''
            this.input_prescription.registration_destination_id = ''
            this.input_prescription.health_facility_item_id = ''
            this.input_prescription.form_prescription_parent = [{
                id: 1,
            }]
            this.input_prescription.form_prescription = []
            this.input_prescription.non_pharmacological = []
            this.input_prescription.soap_id = ''
            this.input_prescription.cppt_id = ''
            this.input_prescription.sbar_id = ''
        },

        // async takeKardex(param) {
        //     try {
        //         await this.$store.dispatch('kardex/putTakeOrder', {kardex_id: this.kardex_data.id})
        //         .then(
        //             async response => {
        //                 if (param == true) {
        //                     this.notification('Success','CheckIcon','success','Berhasil mengambil order kardeks')
        //                     this.$refs.confirm_kardex.hide()
        //                     this.$emit('refresh')                            
        //                     // this.$emit('toggleRead', this.temp_notification);
        //                     setTimeout(() => {
        //                         const url = this.$router.resolve({ path: '/patient/doctor/'+ this.regData.registration_destination_id }).href;
        //                         window.open(url, '_blank');
        //                     }, 750);
        //                 }
        //             }
        //         )
        //     } catch (error) {
        //         console.error('Error catch while GET kardex/putTakeOrder at: ', error);
        //     }
        // },

        // async approveOrRejectKardex(status) {
        //     await this.takeKardex(false)
        //     try {                
        //         await this.$store.dispatch('kardex/putApproveKardex', {
        //             kardex_id: this.kardex_data.id,
        //             is_approve: status
        //         })
        //         .then(
        //             response => {
        //                 this.notification('Success','CheckIcon','success',`Berhasil ${status == 1 ? 'konfirmasi' : 'menolak'} order kardeks`)
        //                 this.$refs.confirm_kardex.hide()
        //                 this.$emit('refresh')
                        
        //                 //ga perlu di read lagi krn udah auto ke read ama BE, jadi langsung trigger refresh notif aja
        //                 // this.$emit('toggleRead', this.temp_notification); 
        //             }
        //         )
        //     } catch (error) {
        //         console.error('Error catch while GET kardex/putApproveKardex at: ', error);
        //     }
        // },

        onSearchCatalog(search, loading) {
            if (search.length >= 3) {
                loading(true)
                const searchTerm = search.toLowerCase();
                this.OptionsCatalog = this.catalog_list.filter(item => {
                    return item?.data_catalog?.composition.toLowerCase().includes(searchTerm) ||
                    item?.title.toLowerCase().includes(searchTerm)
                });
                loading(false)
            }
        },

        async cloneAllState() {          
            this.original_prescription = JSON.parse(JSON.stringify(this.input_prescription))
        },

        openPreParent(event) {
            this.prescription_type = event
            let index = this.input_prescription.form_prescription_parent.findIndex((item) => item.id == event.value);
            if (index !== -1) return;
            // if (index !== -1) return console.log("Object already exists");

            // kl bukan obat satuan, alias racik
            if (event.value != 1) {
                this.temp_presc_parent.parent_id = event.value
                this.temp_presc_parent.parent_name = event.title
                this.temp_presc_parent.package = ''
                this.temp_presc_parent.signatura_time = ''
                this.temp_presc_parent.administer = ''
                this.temp_presc_parent.desc = ''
                this.active.modal.presc_parent = true
                return
            }

            // obat satuan
            this.input_prescription.form_prescription_parent.push({
                id: event.value,
                is_exist: 0,
            })
        },

        closePreParent() {
            this.active.modal.presc_parent = false
            this.kxStock = ''
            this.kxCatalog = ''
        },

        addParent() {
            this.input_prescription.form_prescription_parent.push(
                {
                    id: this.temp_presc_parent.parent_id,
                    status: 3,
                    value: 1,
                    package: this.temp_presc_parent.package,
                    signatura_times_id: this.temp_presc_parent.signatura_time,
                    administer_id: this.temp_presc_parent.administer,
                    desc: this.temp_presc_parent.desc,
                    is_exist: 0,
                }
            ) 
            this.active.modal.presc_parent = false
        },

        async openPreItem(event) {
            this.kxCatalog = ''
            if (this.kxStock == '' || this.kxStock == null) return alert("Silakan Pilih Sediaan Terlebih Dahulu")

            this.temp_presc_items.parent_id = this.kxStock?.value
            this.temp_presc_items.parent_name = this.kxStock?.title
            this.temp_presc_items.catalog_name = event?.data_catalog?.name
            this.temp_presc_items.data_catalog = event?.data_catalog
            this.temp_presc_items.signatura_dose = event?.data_catalog?.signatura_dose
            this.temp_presc_items.signatura_unit = event?.data_catalog?.signatura_unit_id
            this.temp_presc_items.signatura_time = event?.data_catalog?.signatura_times_id
            this.temp_presc_items.administer = event?.data_catalog?.instruction_id || ''
            this.temp_presc_items.desc = ''
            await this.getItemSignaturaUnits(event?.data_catalog?.package_id)

            this.active.modal.presc_items = true
        },

        addPrescItem() {            
            this.input_prescription.form_prescription.push({ 
                pharmacy_catalog_id: this.temp_presc_items?.data_catalog?.id, 
                package_id: this.temp_presc_items?.data_catalog?.package_id,
                prescription_type_id: this.temp_presc_items.parent_id,
                dose: this.temp_presc_items.signatura_dose,
                dose_unit_id: this.temp_presc_items.signatura_unit,
                signatura_times_id: this.temp_presc_items.signatura_time,
                administer_id: this.temp_presc_items.administer,
                desc: this.temp_presc_items.desc,
                sort: this.input_prescription.form_prescription.length, 
                status: this.temp_presc_items.parent_id == 1 ? 2 : null,

                is_exist: 0,
                name: this.temp_presc_items?.data_catalog?.name,
                package_name: this.temp_presc_items?.data_catalog?.package_name,
                catalog_date: '',
                stop_date: '',
                is_change: '',
                exist_status: '',
                doctor_added: this.userData.employee_id,
                doctor_added_name: this.userData.employee_name,
                doctor_nickname: this.userData.employee_name,
                id: '',
                dose_unit_options: this.optItemSignUnits
            })
            this.active.modal.presc_items = false
        },

        deletePRES (index) {
            this.input_prescription.form_prescription.splice(index, 1)
        },

        closePrescModal() {
            this.active.disabled.kardex = false
            this.$refs.confirmKardexModal.hide()                            
        },

        updateAdminister(index, value, target) {
            const arrayKey = target != null ? `form_prescription_${target}` : 'form_prescription'
            const targetArray = this.input_prescription[arrayKey]
            if(value != this.other_administer) targetArray[index].desc = ''
        },

        addNonPharma() {
            if (this.kxNonPharma) {
                this.input_prescription.non_pharmacological.push({
                    id: '',
                    name: this.kxNonPharma,
                    doctor_added: this.userData.employee_id,
                    doctor_added_name: this.userData.employee_name,
                })
                this.kxNonPharma = ''
            }
        },

        removeNonPharma(index) {
            this.input_prescription.non_pharmacological.splice(index, 1)  
        },

        async getAllDoctorList() {
            try {
                const response = await this.$store.dispatch('employee/getDoctorList', { is_specialist: 'all' })
                this.optAllDoctor = response
                // console.log('this.optAllDoctor: ', this.optAllDoctor)
            } catch (error) {
                console.error('error at getAllDoctorList: ', error)
            }
        },

        async getPrescCatalog() {
            try {                
                const response = await this.$store.dispatch('catalog/getOptionCatalog')
                this.catalog_list = response
            } catch (error) {
                console.error('Error getPrescCatalog at: ', error);
            }
        },

        async getItemSignaturaUnits(package_id) {
            this.optItemSignUnits = []
            let param = {
                package_id: package_id
            }
            try {                
                await this.$store.dispatch('kardex/getOptUnit', param)
                .then(
                    async response => {
                        this.optItemSignUnits = response
                    }
                )
                .catch(({message, debug, response}) => {
                    console.error('Error GET kardex/getOptUnit at: ', response);
                })
            } catch (error) {
                console.error('Error catch while GET kardex/getOptUnit at: ', error);
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

        hasItemsInCategory(items, categoryId) {
            if (items) {
                return items.some(item => item.prescription_item_type_id === categoryId);
            }
        },

        parsedBody(text){
            return String(text || '').replace(/\n/g, "<br>");
        },

        notification(a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                employee: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },

        handleBeforeUnload(event) {
            if (this.active.section == 'second' || this.active.section == 'third') {
                event.preventDefault()
                // event.returnValue = "Are you sure to leave site?" //blm tau kenapa pesan nya gak masuk
            }
        },
    },
    
    mounted() {
        window.addEventListener('beforeunload', this.handleBeforeUnload);
    },

    beforeDestroy() {
        window.removeEventListener('beforeunload', this.handleBeforeUnload);
    },
}
</script>