<template>
    <div>
        <Layout
            :layout_breadcrumb_items="[
                { text: 'Template Dokter' }
            ]"
        >
            <section class="">
                <section>
                    <b-button @click="openPrescModal" variant="primary" size="sm" class="mr-2">
                        <feather-icon icon="PlusCircleIcon" class="mr-1" />
                        <span class="fs-12">TEMPLATE RESEP</span>
                    </b-button>
                    <b-button @click="openAnamModal" variant="primary" size="sm" class="mr-2">
                        <feather-icon icon="PlusCircleIcon" class="mr-1" />
                        <span class="fs-12">TEMPLATE ANAMNESA</span>
                    </b-button>
                    <!-- <b-button @click="openEducationResumeModal" variant="primary" size="sm" class="mr-2">
                        <feather-icon icon="PlusCircleIcon" class="mr-1" />
                        <span class="fs-12">TEMPLATE RESUME EDUKASI SELAMA DI RUMAH</span>
                    </b-button> -->
                </section>

                <section class="d-flex align-items-center mt-12 bg-info-100 p-12">
                    <div class="fs-14 font-weight-bold mr-12">FILTER:</div>
                    <b-form-checkbox size="sm" :value="true" :unchecked-value="false" class="mr-2" v-model="active.showingList.anamnesa">
                        <span class="fs-12">TEMPLATE ANAMNESA</span>
                    </b-form-checkbox>
                    <b-form-checkbox size="sm" :value="true" :unchecked-value="false" class="mr-2" v-model="active.showingList.prescription">
                        <span class="fs-12">TEMPLATE RESEP</span>
                    </b-form-checkbox>
                    <!-- <b-form-checkbox size="sm" :value="true" :unchecked-value="false" class="mr-2" v-model="active.showingList.education_resume">
                        <span class="fs-12">TEMPLATE RESUME EDUKASI SELAMA DI RUMAH</span>
                    </b-form-checkbox> -->
                </section>

                <section class="mt-20" v-if="active.showingList.anamnesa">
                    <ModernLabel label="Template Anamnesa" />
                    <b-table bordered fixed small head-variant="light" :items="anam_template_list" :fields="anamTable" thead-tr-class="fs-13" tbody-tr-class="fs-12" class="mt-12 text-center">
                        <template #cell(no)="row">{{ row.index+1 }}</template>
                        <template #cell(action)="row">
                            <div>
                                <b-button @click="onEditAnam(row.item)" size="sm" variant="warning-500" class="mr-1">
                                    <feather-icon icon="EditIcon" />
                                </b-button>
                                <b-button @click="removeAnam(row.item)" size="sm" variant="danger-700">
                                    <feather-icon icon="TrashIcon" />
                                </b-button>
                            </div>
                        </template>
                        <template #cell(anamesa_type_id)="row">
                            <div>{{ getAnamnesaName(row.item.anamesa_type_id) }}</div>
                            <template v-if="row.item.anamesa_type_id == 2">
                                <div>
                                    <span>{{ getFamilyName(row.item.family_relation_id) }} - {{ row.item.family_name || '-' }}</span>
                                </div>
                            </template>
                        </template>
                    </b-table>
                </section>

                <!-- <section class="mt-20" v-if="active.showingList.education_resume">
                    <ModernLabel label="Template Resume Edukasi Selama di Rumah" />
                    <b-table bordered fixed small head-variant="light" :items="education_resume_template_list" :fields="educationResumeTable" thead-tr-class="fs-13" tbody-tr-class="fs-12" class="mt-12 text-center">
                        <template #cell(no)="row">{{ row.index+1 }}</template>
                        <template #cell(action)="row">
                            <div>
                                <b-button @click="onEditEducationResume(row.item)" size="sm" variant="warning-500" class="mr-1">
                                    <feather-icon icon="EditIcon" />
                                </b-button>
                                <b-button @click="removeEducationResume(row.item)" size="sm" variant="danger-700">
                                    <feather-icon icon="TrashIcon" />
                                </b-button>
                            </div>
                        </template>
                    </b-table>
                </section> -->

                <section class="mt-20" v-if="active.showingList.prescription">
                    <ModernLabel label="Template Resep" />
                    <b-table bordered responsive small head-variant="light" :items="presc_template_list" :fields="prescTable" thead-tr-class="fs-13" tbody-tr-class="fs-12" class="mt-12 text-center">
                        <template #cell(no)="row">{{ row.index+1 }}</template>
                        <template #cell(action)="row">
                            <div>
                                <b-button @click="onEditPresc(row.item)" size="sm" variant="warning-500" class="mr-1">
                                    <feather-icon icon="EditIcon" />
                                </b-button>
                                <b-button @click="removePresc(row.item)" size="sm" variant="danger-700">
                                    <feather-icon icon="TrashIcon" />
                                </b-button>
                            </div>
                        </template>
                        <template #cell(form_prescription)="row">
                            <div class="fs-16 font-weight-bold text-uppercase">{{ row.item.name }}</div>
                            <div v-for="(category, index) in row.item.form_prescription_parent" :key="index" class="mt-10" v-if="hasItemsInCategory(row.value, category.id)">
                                <b-table-simple fixed small>
                                    <b-thead class="fs-13">
                                        <b-tr>
                                            <b-th colspan="4" :class="getParentVariant(category.id)">
                                                <div class="text-uppercase ">{{ getParentName(category.id) }}</div>
                                                <div v-if="category.id != 1" class="fs-12 text-italic" style="font-weight: 400">  
                                                    <span>Jumlah: {{ category.value }}, </span>
                                                    <span>Kemasan: {{ getParentPackageName(category?.package) }}, </span>
                                                    <span>Aturan pakai: {{ category.used }}</span>
                                                </div>
                                            </b-th>
                                        </b-tr>
                                        <b-tr>
                                            <b-th>Nama</b-th>
                                            <b-th>Jumlah</b-th>
                                            <b-th>Aturan Pakai</b-th>
                                            <b-th>Keterangan</b-th>
                                        </b-tr>
                                    </b-thead>
                                    <b-tbody class="fs-12">
                                        <template v-for="(item, itemIndex) in row.value">
                                            <b-tr :key="itemIndex" v-if="item.prescription_item_type_id == category.id">
                                                <b-td>{{ item.pharmacy_catalog_name }}</b-td>
                                                <b-td>{{ item.qty }} {{ item.package_name }}</b-td>
                                                <b-td>{{ item.used || '-' }}</b-td>
                                                <b-td>{{ item.desc }}</b-td>
                                            </b-tr>
                                        </template>
                                    </b-tbody>
                                </b-table-simple>
                            </div>
                        </template>
                    </b-table>
                </section>

                <b-modal v-model="active.modal.anamnesa" size="md" no-close-on-backdrop>
                    <template #modal-header="{ close }">
                        <div class="fs-13 font-weight-bold text-uppercase">
                            ANDA SEDANG MENGISI TEMPLATE ANAMNESA
                        </div>
                        <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
                    </template>

                    <section class="row">
                        <div class="col-12">
                            <label class="fs-13 mb-0">Nama Template Anamnesa</label>
                            <b-form-input v-model="form_anamnesa.name" type="text" size="sm" autocomplete="off" />
                        </div>
                        <div class="col-lg-4">
                            <label class="fs-13 mb-0">Anamnesa</label>
                            <v-select class="select-size-sm" v-model="form_anamnesa.anamesa_type_id" :options="optAnamnesaType" :reduce="item => item.value" label="title" placeholder="pilih anamnesa" />
                        </div>
                        <template v-if="form_anamnesa.anamesa_type_id == 2">
                            <div class="col-lg-4 mt-8 mt-lg-0">
                                <label class="fs-13 mb-0">Nama Keluarga</label>
                                <b-form-input type="text" size="sm" v-model="form_anamnesa.family_name" autocomplete="off" />
                            </div>
                            <div class="col-lg-4 mt-8 mt-lg-0">
                                <label class="fs-13 mb-0">Hubungan Keluarga</label>
                                <v-select v-model="form_anamnesa.family_relation_id" class="select-size-sm" :options="optFamilyRelation" :reduce="item => item.value" label="title" placeholder="pilih" />
                            </div>
                        </template>
                        <div class="col-12 mt-8">
                            <label class="fs-13 mb-0">Keluhan Utama</label>
                            <b-form-input size="sm" v-model="form_anamnesa.main_complaint" autocomplete="off" />
                        </div>
                        <div class="col-12 mt-8">
                            <label class="fs-13 mb-0">Riwayat Penyakit Sekarang</label>
                            <textarea v-model.lazy="form_anamnesa.history_disease_now" rows="3" class="form-control form-control-sm" autocomplete="off" />
                        </div>
                        <div class="col-12 mt-8">
                            <label class="fs-13 mb-0">Riwayat Penyakit Dahulu</label>
                            <textarea v-model.lazy="form_anamnesa.history_disease_past" rows="3" class="form-control form-control-sm" autocomplete="off" />
                        </div>
                        <div class="col-12 mt-8">
                            <label class="fs-13 mb-0">Riwayat Obat</label>
                            <textarea v-model.lazy="form_anamnesa.history_drugs" rows="3" class="form-control form-control-sm" autocomplete="off" />
                        </div>
                    </section>

                    <template #modal-footer>
                        <div class="w-100 d-flex justify-content-end m-0">
                            <b-button @click="validationAnamTemplate" size="sm" :disabled="active.disabled.anamnesa" variant="primary">SIMPAN ANAMNESA</b-button>
                        </div>
                    </template>
                </b-modal>

                <b-modal v-model="active.modal.prescription" size="lg" no-close-on-backdrop>
                    <template #modal-header="{ close }">
                        <div class="fs-13 font-weight-bold text-uppercase">
                            ANDA SEDANG MENGISI TEMPLATE RESEP
                        </div>
                        <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
                    </template>
                    <div>
                        <label class="fs-13 mb-0">Nama Template Resep</label>
                        <b-form-input v-model="input_presc.name" autocomplete="off" size="sm" />                    
                    </div>
                    <div class="row mt-12">
                        <div class="col-lg-3">
                            <label class="fs-13 mb-0">Sediaan</label>
                            <v-select v-model="inp_stock" @input="openPreParent($event)" :options="optParentStock" :reduce="item => item" label="title" class="select-size-sm" placeholder="sediaan"></v-select>
                        </div>
                        <div class="col-lg">
                            <label class="fs-13 mb-0">Tambah Obat</label>
                            <v-select v-model="inp_catalog" @input="openPreItem($event)" @search="onSearchCatalog" :options="optionsCatalog" :reduce="item => item" :filterable="false" label="title" class="select-size-sm" placeholder="pilih obat"></v-select>
                        </div>
                    </div>
                    <b-table-simple fixed small class="mt-12">
                        <b-thead class="text-center fs-12">
                            <b-tr>
                                <th style="width: 60px;">AKSI</th>
                                <th style="width: 270px;">NAMA OBAT</th>
                                <th class="align-middle">
                                    SIGNATURA & ATURAN PAKAI
                                    <feather-icon id="`pop-trgt-prs-sgntr`" icon="InfoIcon" size="12" color="blue" style="cursor: pointer;" />
                                    <b-popover target="`pop-trgt-prs-sgntr`" triggers="hover" placement="bottom">
                                        <template #title>
                                            <div class="fs-10 text-uppercase font-weight-bold">Pengisian Kolom Signatura & Aturan Pakai</div>
                                        </template>
                                        <PrescInstruction ins_section="signatura" />
                                    </b-popover>
                                </th>
                                <th style="width: 100px;" class="align-middle">JUMLAH</th>
                                <th class="align-middle">KEMASAN / SATUAN</th>
                                <th class="align-middle">
                                    KETERANGAN
                                    <feather-icon id="`pop-trgt-prs-nts`" icon="InfoIcon" size="12" color="blue" style="cursor: pointer;" />
                                    <b-popover target="`pop-trgt-prs-nts`" triggers="hover" placement="bottom">
                                        <template #title>
                                            <div class="fs-10 text-uppercase font-weight-bold">Pengisian Kolom Keterangan</div>
                                        </template>
                                        <PrescInstruction ins_section="notes" />                                                    
                                    </b-popover>
                                </th>
                            </b-tr>
                        </b-thead>
                        <template  v-for="(item_p, index_p) in input_presc.form_prescription_parent">
                            <b-tr class="fs-11" :class="getParentVariant(item_p.id)">
                                <b-td colspan="2" class="font-weight-bold align-middle" :class="item_p.id == 1? 'py-12' : ''">{{ item_p.prescription }}</b-td>
                                <template v-if="item_p.id != 1">
                                    <td>
                                        <b-form-input size="sm" v-model="item_p.used" type="text" />
                                    </td>
                                    <td>
                                        <b-form-input size="sm" v-model="item_p.value" type="number" min="1" placeholder="jumlah" />
                                    </td>
                                    <b-td>
                                        <v-select v-model="item_p.package" :options="optParentPackage" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />                    
                                    </b-td>
                                    <b-td></b-td>
                                </template>
                                <template v-else>
                                    <b-td colspan="4"></b-td>
                                </template>
                            </b-tr>
                            <tr class="item fs-11" v-for="(item, index) in input_presc.form_prescription" v-if="item_p.id == item.prescription_type_id">
                                <td class="text-center">
                                    <b-button @click="deleteItemPresc(index)" variant="danger-600" size="sm" class="mr-2">
                                        <feather-icon icon="TrashIcon" />
                                    </b-button>
                                </td>
                                <td>{{ item.name }}</td>
                                <td class="align-middle">
                                    <b-form-input v-model="item.used" size="sm" type="text" v-if="item.prescription_type_id == 1"/>
                                    <div v-else class="text-center">-</div>
                                </td>
                                <td>
                                    <b-form-input size="sm" type="number" min="1" v-model="item.value_package" />
                                </td>
                                <td>
                                    <v-select v-model="item.package_id" :options="PackageOptions" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                                </td>
                                <td class="align-middle">
                                    <b-form-input v-model="item.desc" size="sm" type="text" />
                                </td>
                            </tr>
                        </template>
                    </b-table-simple>
                    <template #modal-footer>
                        <div class="w-100 d-flex justify-content-end">
                            <b-button @click="validationPrescTemplate" size="sm" :disabled="active.disabled.prescription" variant="primary">SIMPAN RESEP</b-button>
                        </div>
                    </template>
                </b-modal>

                <b-modal v-model="active.modal.presc_mdl_parent" size="sm" no-close-on-esc no-close-on-backdrop>
                    <template #modal-header="{ close }">
                        <div class="fs-13 font-weight-bold text-uppercase">
                            ANDA AKAN MENAMBAHKAN SEDIAAN
                            <span class="text-primary">{{ temp_presc_parent.parent_name }}</span>
                        </div>
                        <feather-icon icon="XIcon" @click="closePreParent()" size="18" class="pointer" />
                        <!-- <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" /> -->
                    </template>
                    <section>
                        <div>
                            <label class="fs-13 mb-0">Signatura</label>
                            <v-select v-model="temp_presc_parent.signatura" :options="OptionsSignatura" :reduce="item => item.value" label="title" placeholder="signatura" class="select-size-sm"/>
                        </div>
                        <div v-if="temp_presc_parent.signatura == 'other'" class="mt-10">
                            <label class="fs-13 mb-0">Signatura lain</label>
                            <b-form-input v-model="temp_presc_parent.other_signatura" size="sm" autocomplete="off"/>
                        </div>
                        <div class="mt-10">
                            <label class="fs-13 mb-0">Waktu Penggunaan</label>
                            <v-select v-model="temp_presc_parent.consume_time" :options="OptionsConsumeTime" :reduce="item => item.value" label="title" placeholder="waktu penggunaan" class="select-size-sm"/>
                        </div>
                        <div class="mt-10">
                            <label class="fs-13 mb-0">Aturan Pakai</label>
                            <v-select v-model="temp_presc_parent.utilization" :options="OptionsUtilization" :reduce="item => item.value" label="title" placeholder="aturan pakai" class="select-size-sm"/>
                        </div>
                        <div class="mt-10">
                            <label class="fs-13 mb-0">Kemasan</label>
                            <v-select v-model="temp_presc_parent.package" :options="optParentPackage" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />                    
                        </div>
                    </section>
                    <template #modal-footer>
                        <div class="d-flex justify-content-end w-100">
                            <b-button @click="addParent" size="sm" variant="primary">TAMBAHKAN</b-button>
                        </div>
                    </template>
                </b-modal>

                <b-modal v-model="active.modal.presc_mdl_items" size="sm" no-close-on-backdrop>
                    <template #modal-header="{ close }">
                        <div class="fs-13 font-weight-bold text-uppercase">
                            ANDA AKAN MENAMBAHKAN OBAT
                            <span class="text-primary">{{ temp_presc_items.parent_name }}-{{ temp_presc_items.catalog_name }}</span>
                        </div>
                        <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
                    </template>

                    <section>
                        <div>
                            <label class="fs-13 mb-0">Signatura</label>
                            <v-select v-model="temp_presc_items.signatura" :options="OptionsSignatura" :reduce="item => item.value" label="title" placeholder="signatura" class="select-size-sm"/>
                        </div>
                        <div v-if="temp_presc_items.signatura == 'other'" class="mt-10">
                            <label class="fs-13 mb-0">Signatura lain</label>
                            <b-form-input v-model="temp_presc_items.other_signatura" size="sm" autocomplete="off"/>
                        </div>
                        <div class="mt-10">
                            <label class="fs-13 mb-0">Waktu Penggunaan</label>
                            <v-select v-model="temp_presc_items.consume_time" :options="OptionsConsumeTime" :reduce="item => item.value" label="title" placeholder="waktu penggunaan" class="select-size-sm"/>
                        </div>
                        <div class="mt-10">
                            <label class="fs-13 mb-0">Aturan Pakai</label>
                            <v-select v-model="temp_presc_items.utilization" :options="OptionsUtilization" :reduce="item => item.value" label="title" placeholder="aturan pakai" class="select-size-sm"/>
                        </div>
                    </section>
                    <template #modal-footer>
                        <div class="d-flex justify-content-end w-100">
                            <b-button @click="addPrescItem" size="sm" variant="primary">TAMBAHKAN</b-button>
                        </div>
                    </template>
                </b-modal>

                <b-modal v-model="active.modal.education_resume" size="md" no-close-on-backdrop>
                    <template #modal-header="{ close }">
                        <div class="fs-13 font-weight-bold text-uppercase">
                            ANDA SEDANG MENGISI TEMPLATE RESUME EDUKASI SELAMA DI RUMAH
                        </div>
                        <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
                    </template>

                    <section class="row">
                        <div class="col-12">
                            <label class="fs-13 mb-0">Nama Template Resume Edukasi Selama di Rumah</label>
                            <b-form-input v-model="form_education_resume.name" type="text" size="sm" autocomplete="off" />
                        </div>
                        <div class="col-12 mt-8">
                            <label class="fs-13 mb-0">EDUKASI SELAMA DI RUMAH</label>
                            <textarea v-model.lazy="form_education_resume.education_while_at_home" rows="3" class="form-control form-control-sm" autocomplete="off" />
                        </div>
                    </section>

                    <template #modal-footer>
                        <div class="w-100 d-flex justify-content-end m-0">
                            <b-button @click="validationEducationResumeTemplate" size="sm" :disabled="active.disabled.education_resume" variant="primary">SIMPAN RESUME EDUKASI SELAMA DI RUMAH</b-button>
                        </div>
                    </template>
                </b-modal>
                <!-- dibuat grouping barangkali tar ada template laen -->
            </section>
        </Layout>
    </div>
</template>

<script>
import { mapState } from 'vuex'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import optionMixin from '@/mixins/optionMixin'

export default {
    mixins: [optionMixin],
    data() {
        return {
            catalog_list: [],
            presc_template_list: [],
            active: {
                modal: {
                    anamnesa: false,
                    prescription: false,
                    presc_mdl_parent: false,
                    presc_mdl_items: false,
                    education_resume: false,
                },
                disabled: {
                    prescription: false,
                    anamnesa: false,
                    education_resume: false,
                },
                showingList: {
                    anamnesa: true,
                    prescription: true,
                    education_resume: true,
                }
            },

            prescTable: [
                { key: 'no', label: 'No', thStyle: { width: '45px' } },
                { key: 'action', label: 'Aksi', thStyle: { width: '100px' } },
                { key: 'form_prescription', label: 'Template Resep', },
            ],

            input_presc: {
                id: '',
                name: '',
                form_prescription_parent: [],
                form_prescription: []
            },
            
            optionsCatalog: [],
            inp_catalog: '',
            inp_stock: '',
            active_stock: {},

            temp_presc_parent: {
                parent_id: '',
                parent_name: '',
                signatura: '',
                other_signatura: '',
                consume_time: '',
                utilization: '',
                package: '',
            },
            temp_presc_items: {
                parent_id: '',
                parent_name: '',
                catalog_name: '',
                signatura: '',
                other_signatura: '',
                consume_time: '',
                utilization: '',
                data_catalog: {},
            },

            anam_template_list: [],
            form_anamnesa: {
                id: '',
                name: '',
                anamesa_type_id: 1,
                family_name: '',
                family_relation_id: '',
                main_complaint: '',
                history_disease_now: '',
                history_disease_past: '',
                history_drugs: '',
            },

            anamTable: [
                { key: 'no', label: 'No', thStyle: { width: '45px' } },
                { key: 'action', label: 'Aksi', thStyle: { width: '100px' } },
                { key: 'name', label: 'Nama Template', },
                { key: 'anamesa_type_id', label: 'Anamnesa & Hub. Keluarga', },
                { key: 'main_complaint', label: 'Keluhan Utama', },
                { key: 'history_disease_now', label: 'Riwayat Penyakit Sekarang', },
                { key: 'history_disease_past', label: 'Riwayat Penyakit Dahulu', },
                { key: 'history_drugs', label: 'Riwayat Obat', },
            ],

            education_resume_template_list: [],
            form_education_resume: {
                id: '',
                name: '',
                education_while_at_home: '',
            },

            educationResumeTable: [
                { key: 'no', label: 'No', thStyle: { width: '45px' } },
                { key: 'action', label: 'Aksi', thStyle: { width: '100px' } },
                { key: 'name', label: 'Nama Template', },
                { key: 'education_while_at_home', label: 'Edukasi Selama Di Rumah', },
            ],
        }
    },

    computed: {
        ...mapState({ 
            optAnamnesaType: state => state.frontoptions.nurse.assessment.anamnesa.anamnesa_type,
            optFamilyRelation: state => state.frontoptions.nurse.assessment.anamnesa.family_relation,

            PackageOptions: state => state.packages.options.packages,            
            OptionsUtilization: state => state.frontoptions.doctor.assessment.prescription.utilization,
            OptionsConsumeTime: state => state.frontoptions.doctor.assessment.prescription.consume_time,
            OptionsSignatura: state => state.frontoptions.doctor.assessment.prescription.signatura,
            optParentPackage: state => state.frontoptions.doctor.assessment.prescription.parent_package,
            optParentStock: state => state.frontoptions.doctor.assessment.prescription.parent_stock,
        }),
    },

    watch: {
        "form_anamnesa.anamesa_type_id"(newValue) {
            if (newValue == "1") {
                this.form_anamnesa.family_relation_id = ''
                this.form_anamnesa.family_name = ''
            }
        },
    },

    methods: {
        async openAnamModal() {
            await this.resetAnamForm()
            this.active.modal.anamnesa = true
        },

        async resetAnamForm() {
            this.form_anamnesa.id = ''
            this.form_anamnesa.name = ''
            this.form_anamnesa.anamesa_type_id = 1
            this.form_anamnesa.family_name = ''
            this.form_anamnesa.family_relation_id = ''
            this.form_anamnesa.main_complaint = ''
            this.form_anamnesa.history_disease_now = ''
            this.form_anamnesa.history_disease_past = ''
            this.form_anamnesa.history_drugs = ''
        },

        async validationAnamTemplate() {
            try {
                this.active.disabled.anamnesa = true
                await this.$store.dispatch('templating/postPutAnamTemplate', this.form_anamnesa)
                .then(
                    async res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active.modal.anamnesa = false
                        await this.resetAnamForm()
                        this.getAnamTemplate()
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
            } finally {
                this.active.disabled.anamnesa = false
            }
        },

        getAnamTemplate() {
            try {                
                this.$store.dispatch('templating/getAnamTemplate').then((response) => {
                    this.anam_template_list = response
                    // console.log('this.anam_template_list', this.anam_template_list);
                }, error => {
                    console.error('Error while GET templating/getAnamTemplate at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET templating/getAnamTemplate at: ', error);
            }
        },

        async onEditAnam(obj) {
            await this.resetAnamForm()
            this.active.modal.anamnesa = true

            this.form_anamnesa.id = obj?.id || ''
            this.form_anamnesa.name = obj?.name || ''
            this.form_anamnesa.anamesa_type_id = obj?.anamesa_type_id || 1
            this.form_anamnesa.family_name = obj?.family_name || ''
            this.form_anamnesa.family_relation_id = obj?.family_relation_id || ''
            this.form_anamnesa.main_complaint = obj?.main_complaint || ''
            this.form_anamnesa.history_disease_now = obj?.history_disease_now || ''
            this.form_anamnesa.history_disease_past = obj?.history_disease_past || ''
            this.form_anamnesa.history_drugs = obj?.history_drugs || ''
        },

        removeAnam(obj) {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus template anamnesa ${obj.name}?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger', okTitle: 'Ya, Hapus', cancelTitle: 'Batal', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    this.$store.dispatch('templating/deleteAnamTemplate', obj.id)
                    .then(
                        res => {
                            this.notification('Success','CheckIcon','success','You have successfully')
                            this.getAnamTemplate()
                        }
                    )
                    .catch(({message, debug, response}) => {
                        this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                    })
                }
            })
        },

        getPrescTemplate() {
            try {                
                this.$store.dispatch('templating/getPrescTemplate').then((response) => {
                    this.presc_template_list = response
                    // console.log('this.presc_template_list', this.presc_template_list);
                }, error => {
                    console.error('Error while GET templating/getPrescTemplate at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET templating/getPrescTemplate at: ', error);
            }
        },

        async openPrescModal() {
            await this.resetPrescForm()
            this.active.modal.prescription = true
        },

        async resetPrescForm() {
            this.input_presc.id = ''
            this.input_presc.name = ''
            this.input_presc.form_prescription = []
            this.input_presc.form_prescription_parent = []

            this.inp_catalog = ''
            this.inp_stock = ''
            this.active_stock = {}
        },

        openPreParent(event) {
            this.active_stock = event
            let index = this.input_presc.form_prescription_parent.findIndex((item) => item.id == event.value);

            if (index !== -1) return console.log("Object already exists");

            if (event.value != 1) {
                this.temp_presc_parent.parent_id = event.value
                this.temp_presc_parent.parent_name = event.title
                this.temp_presc_parent.signatura = ''
                this.temp_presc_parent.other_signatura = ''
                this.temp_presc_parent.consume_time = ''
                this.temp_presc_parent.utilization = ''
                this.temp_presc_parent.package = ''
                this.active.modal.presc_mdl_parent = true
                return
            }

            this.input_presc.form_prescription_parent.push({
                id: event.value,
                prescription: event.title,
                used: '',
                value: event.value == 1 ? 0 : 1,
                package: '',
            })
        },

        closePreParent() {
            this.active.modal.presc_mdl_parent = false
            this.inp_catalog = ''
            this.inp_stock = ''
            this.active_stock = {}
        },

        addParent() {
            this.input_presc.form_prescription_parent.push(
                {
                    id: this.temp_presc_parent.parent_id,
                    prescription: this.temp_presc_parent.parent_name,
                    used: `${this.temp_presc_parent.signatura == 'other' ? this.temp_presc_parent.other_signatura : this.temp_presc_parent.signatura} ${this.temp_presc_parent.consume_time} ${this.temp_presc_parent.utilization}`,
                    value: 1,
                    package: this.temp_presc_parent.package,
                }
            ) 
            this.active.modal.presc_mdl_parent = false
        },

        onSearchCatalog(search, loading) {
            if (search.length >= 3) {
                loading(true)
                const searchTerm = search.toLowerCase();
                this.optionsCatalog = this.catalog_list.filter(item => {
                    return item?.data_catalog?.composition.toLowerCase().includes(searchTerm) ||
                    item?.title.toLowerCase().includes(searchTerm)
                });
                loading(false)
            }
        },

        openPreItem (event) {
            this.inp_catalog = ''
            if (this.inp_stock === '') return alert("Silakan Pilih Sediaan Terlebih Dahulu")

            if (this.inp_stock.value != 1) {
                this.input_presc.form_prescription.push({ 
                    code: event?.data_catalog?.code,
                    name: event?.data_catalog?.name,
                    pharmacy_catalog_id: event?.data_catalog?.id, 
                    package_id: event?.data_catalog?.package_id,
                    dose: event?.data_catalog?.dose,
                    sum_dose: event?.data_catalog?.dose+' '+event?.data_catalog?.dose_name, 
                    package_name: event?.data_catalog?.package_name,
                    prescription_type_id: this.inp_stock.value,
                    prescription_type_name: this.inp_stock.title,
                    value_doctor: 0,
                    value_package: 1,
                    used: '',
                    desc: '',
                    sort: this.input_presc.form_prescription.length, 
                })
                return
            }

            this.temp_presc_items.parent_id = ''
            this.temp_presc_items.parent_name = ''
            this.temp_presc_items.catalog_name = ''
            this.temp_presc_items.signatura = ''
            this.temp_presc_items.other_signatura = ''
            this.temp_presc_items.consume_time = ''
            this.temp_presc_items.utilization = ''
            this.temp_presc_items.data_catalog = {}

            let temp_pharmacy_catalog_id = event?.data_catalog?.id
            try {                
                this.$store.dispatch('catalog/getCatalogReference', temp_pharmacy_catalog_id).then((response) => {
                    if (response?.length > 0) {
                        this.temp_presc_items.parent_id = this.inp_stock.value
                        this.temp_presc_items.parent_name = this.inp_stock.title
                        this.temp_presc_items.data_catalog = event?.data_catalog
                        this.temp_presc_items.catalog_name = event?.data_catalog?.name
                        this.temp_presc_items.consume_time = this.getConsumeTime({
                            morning: response[0]?.morning,
                            afternoon: response[0]?.afternoon,
                            evening: response[0]?.evening,
                        })

                        let res_signatura = response[0]?.signatura
                        if (res_signatura) {
                            const is_inOption = this.OptionsSignatura.find((item) => item.value == res_signatura);
                            this.temp_presc_items.signatura = is_inOption ? res_signatura : 'other'
                            this.temp_presc_items.other_signatura = is_inOption ? '' : res_signatura
                        } else {
                            this.temp_presc_items.signatura = ''
                        }
                    } else {
                        this.temp_presc_items.parent_id = this.inp_stock.value
                        this.temp_presc_items.parent_name = this.inp_stock.title
                        this.temp_presc_items.data_catalog = event?.data_catalog
                        this.temp_presc_items.catalog_name = event?.data_catalog?.name
                    }
                    
                    this.active.modal.presc_mdl_items = true
                    // console.log('masuk sini this.temp_presc_items: ', this.temp_presc_items);
                }, error => {
                    console.error('Error while GET catalog/getCatalogReference at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET catalog/getCatalogReference at: ', error);
            }
        },

        addPrescItem() {
            this.input_presc.form_prescription.push({ 
                code: this.temp_presc_items?.data_catalog?.code,
                name: this.temp_presc_items?.data_catalog?.name,
                pharmacy_catalog_id: this.temp_presc_items?.data_catalog?.id, 
                package_id: this.temp_presc_items?.data_catalog?.package_id,
                dose: this.temp_presc_items?.data_catalog?.dose,
                sum_dose: this.temp_presc_items?.data_catalog?.dose+' '+this.temp_presc_items?.data_catalog?.dose_name, 
                package_name: this.temp_presc_items?.data_catalog?.package_name,
                prescription_type_id: this.temp_presc_items.parent_id,
                prescription_type_name: this.temp_presc_items.parent_name,
                value_doctor: 0,
                value_package: 1,
                used: `${this.temp_presc_items.signatura == 'other' ? this.temp_presc_items.other_signatura : this.temp_presc_items.signatura}`,
                desc: `${this.temp_presc_items.consume_time} ${this.temp_presc_items.utilization}`,
                sort: this.input_presc.form_prescription.length, 
            })
            this.active.modal.presc_mdl_items = false

            // console.log('this.input_presc.form_prescription', this.input_presc.form_prescription);
        },

        deleteItemPresc (index) {
            this.input_presc.form_prescription.splice(index, 1)
        },

        async validationPrescTemplate() {
            try {
                this.active.disabled.prescription = true
                await this.$store.dispatch('templating/postPutPrescTemplate', this.input_presc)
                .then(
                    async res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active.modal.prescription = false
                        await this.resetPrescForm()
                        this.getPrescTemplate()
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
            } finally {
                this.active.disabled.prescription = false
            }
        },
        
        async onEditPresc(obj) {
            await this.resetPrescForm()
            this.active.modal.prescription = true
            this.input_presc.id = obj.id
            this.input_presc.name = obj.name

            if (obj.form_prescription_parent) {
                const mappedPrescParent = obj.form_prescription_parent.map(item => {
                    let name = item.id ? this.getParentName(item.id) : ''

                    return {
                        id: item.id,
                        used: item.used,
                        value: item.value,
                        prescription: name,
                        package: item.package,
                    };
                });                
                this.input_presc.form_prescription_parent = mappedPrescParent;
            } else {
                this.input_presc.form_prescription_parent = []
            }

            if (obj.form_prescription) {
                const mappedPresc = obj.form_prescription.map(item => {
                    let name = item.id ? this.getParentName(item.id) : ''

                    return {
                        pharmacy_catalog_id: item.pharmacy_catalog_id, 
                        prescription_type_id: item.prescription_item_type_id,
                        package_id: item.package_id,
                        package_name: item.package_name,
                        dose: item.dose,
                        sum_dose: `${item.dose} ${item.dose_name || ''}`, 
                        value_package: item.qty,
                        sort: item.sort, 
                        used: item.used,
                        desc: item.desc,

                        code: item.pharmacy_catalog_code,
                        name: item.pharmacy_catalog_name,
                        value_doctor: item.qty_doctor || 0,
                        prescription_type_name: name,
                    };
                });      
                this.input_presc.form_prescription = mappedPresc;                                  
            } else {
                this.input_presc.form_prescription = [];                                  
            }
        },

        removePresc(obj) {
            // console.log('remove clicked', obj);

            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus template resep ${obj.name}?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger', okTitle: 'Ya, Hapus', cancelTitle: 'Batal', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    this.$store.dispatch('templating/deletePrescTemplate', obj.id)
                    .then(
                        res => {
                            this.notification('Success','CheckIcon','success','You have successfully')
                            this.getPrescTemplate()
                        }
                    )
                    .catch(({message, debug, response}) => {
                        this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                    })
                }
            })
        },

        hasItemsInCategory(items, categoryId) {
            if (items) {
                return items.some(item => item.prescription_item_type_id === categoryId);
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

        getConsumeTime(obj) {
            if (obj.morning === 1 && obj.afternoon === 1 && obj.evening == 0) return '';

            const times = [
                obj.morning ? 'Pagi' : '',
                obj.afternoon ? 'Siang' : '',
                obj.evening ? 'Malam' : ''
            ].filter(Boolean); 

            return times.length ? times.join('-') : '';
        },

        getPrescCatalog() {
            try {                
                this.$store.dispatch('catalog/getOptionCatalog').then((response) => {
                    this.catalog_list = response
                }, error => {
                    console.error('Error while GET catalog/getOptionCatalog at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET catalog/getOptionCatalog at: ', error);
            }
        },

        openEducationResumeModal() {
            this.resetEducationResumeForm()
            this.active.modal.education_resume = true
        },

        resetEducationResumeForm() {
            this.form_education_resume.id = ''
            this.form_education_resume.name = ''
            this.form_education_resume.education_while_at_home = ''
        },

        validationEducationResumeTemplate() {
            // console.log('save education resume:', this.form_education_resume);

            try {
                this.active.disabled.education_resume = true
                this.$store.dispatch('templating/postPutEducationResumeTemplate', this.form_education_resume)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active.modal.education_resume = false
                        this.resetEducationResumeForm()
                        this.getEducationResumeTemplate()
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
            } finally {
                this.active.disabled.education_resume = false
            }
        },

        getEducationResumeTemplate() {
            try {                
                this.$store.dispatch('templating/getEducationResumeTemplate').then((response) => {
                    this.education_resume_template_list = response
                    // console.log('this.education_resume_template_list', this.education_resume_template_list);
                }, error => {
                    console.error('Error while GET templating/getEducationResumeTemplate at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET templating/getEducationResumeTemplate at: ', error);
            }
        },

        onEditEducationResume(obj) {
            this.resetEducationResumeForm()
            this.active.modal.education_resume = true

            this.form_education_resume.id = obj?.id || ''
            this.form_education_resume.name = obj?.name || ''
            this.form_education_resume.education_while_at_home = obj?.education_while_at_home || ''
        },

        removeEducationResume(obj) {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus template Resume Edukasi selama di Rumah ${obj.name}?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger', okTitle: 'Ya, Hapus', cancelTitle: 'Batal', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    this.$store.dispatch('templating/deleteEducationResumeTemplate', obj.id)
                    .then(
                        res => {
                            this.notification('Success','CheckIcon','success','You have successfully')
                            this.getEducationResumeTemplate()
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
                employee: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },
    },

    created() {
        this.$store.dispatch('packages/getParam')
        this.getPrescCatalog()
        this.getPrescTemplate()
        this.getAnamTemplate()
        this.getEducationResumeTemplate()
    },
}
</script>