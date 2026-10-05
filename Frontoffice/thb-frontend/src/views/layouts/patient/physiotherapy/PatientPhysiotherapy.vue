<template>
    <div>
        <patientMenu selectedMenu="menu20" />
        <div class="label-head my-16">
            <span class="px-16 text-bold-24 text-neutral-700">Riwayat Pemeriksaan Fisioterapi</span>
        </div>
        <PatientBanner />
        <div class="card-body mt-32 shadow-sm">
            <div class="row">
                <div class="col-12 col-lg-6">
                    <!-- <table>
                        <tr>
                            <th class="text-regular-14">Perawat</th>
                            <td class="text-regular-14 px-12 py-1">:</td>
                            <td class="text-medium-14">Maria Nanay</td>
                        </tr>
                        <tr>
                            <th class="text-regular-14">Waktu</th>
                            <td class="text-regular-14 px-12 py-1">:</td>
                            <td class="text-medium-14">11 Oktober 2022 - 17:03:47</td>
                        </tr>
                    </table> -->
                </div>
                <div class="col-12 col-lg-6 mt-16 mt-lg-0">
                    <div class="d-flex justify-content-lg-end">
                        <b-button @click="modal.show = !modal.show" pill variant="info-600" size="sm"
                            class="px-16 ml-lg-2 mt-8 mt-lg-0">
                            <b-icon icon="plus" /> <span>Order Pemeriksaan Fisioterapi</span>
                        </b-button>
                    </div>
                </div>
            </div>
        </div>
        <template v-for="(itemsResult,i) in PhysiotherapyOrder">
            <div class="card-body mt-32 border rounded-lg">
                <p class="text-regular-14">Rujukan Fisioterapi, Dokter : <span class="font-weight-bold"> {{itemsResult.doctor_in_charge_name}} </span></p>
                <p class="text-regular-14">Waktu Order : <span class="font-weight-bold">{{itemsResult.date_request}}</span></p>
                <div class="row mx-0">
                    <!-- <router-link :to="{ name: 'physiotherapyListView' }">
                        <b-button size="sm" variant="outline-success-600" class="px-16 mr-2">
                            <feather-icon icon="ArrowLeftIcon" /> Ke Modul Fisioterapi
                        </b-button>
                    </router-link> -->
                    <!-- <router-link :to="{ name: 'PatientPhysiotherapyOrderCertificateView' }">
                        <b-button size="sm" variant="success-600" class="px-16 mr-2">
                            <feather-icon icon="PrinterIcon" /> Print Order
                        </b-button>
                    </router-link> -->
                    <!-- <b-button @click="modal.show = !modal.show" size="sm" variant="outline-success-600" class="px-16">
                        <feather-icon icon="PlusIcon" /> Tambah
                    </b-button> -->
                </div>
                <div class="mt-16 mt-lg-24">
                    <b-table small responsive bordered head-variant="light" :items="itemsResult.physiotherapy_request_item"
                        :fields="physiotherapy_table.fields" tbody-tr-class="text-medium-14" thead-class="text-bold-14"
                        class="text-center">
                        <template #thead-top="data">
                            <b-tr>
                                <b-th></b-th>
                                <b-th></b-th>
                                <b-th></b-th>
                                <b-th colspan="2">Tanggal & Jam Input</b-th>
                                <b-th></b-th>
                            </b-tr>
                        </template>
                        <!-- <template #cell(no)="row">
                            <div style="min-width: 500px;">
                                <span>{{ row.item.value }}</span>
                            </div>
                        </template> -->
                        <template #cell(inspection_detail)="data">
                            <div style="min-width: 500px;">
                                <span>{{ data.value }}</span>
                            </div>
                        </template>
                        <template #cell(status)="data">
                            <div style="min-width: 140px;">
                                <span :class="data.value === '2' ? 'text-success-400' : 'text-danger-600'">{{ data.value == '2' ? 'Sudah Melakukan Kunjungan ke Fisioterapi' : 'Belum Melakukan Kunjungan ke Fisioterapi' }}</span>
                            </div>
                        </template>
                        <template #cell(date_request)="data">
                            {{ data.value ? data.value : itemsResult.date_request }}
                        </template>
                        <template #cell(inspection_result)="data">
                            <div style="min-width: 140px;">
                                <span>{{ data.value }}</span>
                            </div>
                        </template>
                        <template #cell(action)="data">
                            <div style="min-width: 200px;">
                                <!-- <b-button @click="modal.edit = !modal.edit" size="sm" variant="warning-500" class="mr-2">
                                    <feather-icon icon="EditIcon" />
                                </b-button>
                                <b-button @click="modal.delete = !modal.delete" size="sm" variant="danger-600" class="mr-2">
                                    <feather-icon icon="TrashIcon" />
                                </b-button> -->
                                <b-button @click="modal.result = !modal.result" size="sm" variant="info-700">
                                    Lihat Hasil <feather-icon icon="SearchIcon" />
                                </b-button>
                            </div>
                        </template>
                    </b-table>
                </div>
            </div>
        </template>

        <b-modal v-model="modal.show" scrollable size="xl" title="Order Fisioterapi" hide-footer>
            <div>
                <label class="c-form-label">Dokter Perujuk</label>
                <v-select v-model="form_soap.soap_id" :options="OptionsSoapPlanDoctor" :reduce="(OptionsSoapPlanDoctor) => OptionsSoapPlanDoctor.value" label="title" placeholder="-- Dokter --"></v-select>
            </div>
            <div class="card mt-20">
                <div class="card-header">Fisioterapi</div>
                <div class="card-body">
                    <b-form-input type="text" v-model="searchphy" placeholder="Pencarian"></b-form-input>
                    <div class="col-lg-12 mt-3">
                        <h5>Checked Items:</h5>
                        <b-form-checkbox-group v-model="form_soap.physiotherapy_request_items">
                            <b-form-checkbox
                            v-for="(item, index) in form_soap.physiotherapy_request_items"
                            :key="index"
                            :value="item"
                            >
                            {{ item.name }}
                            </b-form-checkbox>
                        </b-form-checkbox-group>
                    </div>
                    <div class="col mt-12 py-2 bg-neutral-100" style="height: 300px; overflow-y: scroll;">
                        <b-form-checkbox-group size="sm" stacked>
                            <div class="row">
                                <template v-for="il in filteredListPhy">
                                    <div class="col-lg-4">
                                        <b-form-checkbox class="my-2" v-model="form_soap.physiotherapy_request_items" :value="{'service_class_id':'9b702245-79c5-4beb-8218-53afb08647aa','physiotherapy_package_id':il.id, name: il.name}">{{ il.name }} 
                                            <!-- Kelas 2 -->
                                            <!-- <br> Tarif: {{ il.costing_services_class.var_1 | currency }} -->
                                        </b-form-checkbox>
                                    </div>
                                </template>
                            </div>
                        </b-form-checkbox-group>
                    </div>
                </div>
            </div>
            <div class="row mx-0 mt-32">
                <b-button @click="modal.show = false" variant="outline-secondary" class="mr-2 ml-auto">Tutup</b-button>
                <b-button :disabled="isDisabled"  @click.prevent="validationForm" variant="indigo"><feather-icon icon="SaveIcon" class="mr-1" />Simpan</b-button>
            </div>
        </b-modal>
        <b-modal v-model="modal.result" scrollable size="xl" title="Hasil" hide-footer>
            <div class="d-flex justify-content-end">
                <div>
                    <router-link :to="{ name: 'PatientPhysiotherapyInfoCertificateView' }">
                        <b-button size="sm" variant="success-600" class="px-16 mr-2">
                            <feather-icon icon="PrinterIcon" /> Print Surat Keterangan
                        </b-button>
                    </router-link>
                </div>
            </div>
            <div class="mt-20">
                <p class="text-center text-bold-16">HASIL FISIOTERAPI</p>
                <div class="row mt-28">
                    <div class="col-lg-5">
                        <table class="text-regular-14">
                            <tr>
                                <td class="first-cell">No. Reg / RM</td>
                                <td class="px-3">:</td>
                                <td>{{ this.regData.code }} / {{ this.regData.norm }}</td>
                            </tr>
                            <tr>
                                <td class="first-cell">NIK</td>
                                <td class="px-3">:</td>
                                <td>{{ this.regData.citizen_id }}</td>
                            </tr>
                            <tr>
                                <td class="first-cell">Nama Pasien</td>
                                <td class="px-3">:</td>
                                <td><span>({{this.regData.alias_name}}) </span>
                                    <span>{{this.regData.name}}</span></td>
                            </tr>
                            <tr>
                                <td class="first-cell">Alamat</td>
                                <td class="px-3">:</td>
                                <td>{{ this.regData.address }}</td>
                            </tr>
                            <tr>
                                <td class="first-cell">Instalasi/Ruangan</td>
                                <td class="px-3">:</td>
                                <td>{{this.regData.health_facility_item_name}}</td>
                            </tr>
                            <tr>
                                <td class="first-cell">Tgl. Order</td>
                                <td class="px-3">:</td>
                                <td>26-10-2022 16:38:08</td>
                            </tr>
                            <tr>
                                <td class="first-cell">Tgl. Hasil</td>
                                <td class="px-3">:</td>
                                <td>30-01-2023 10:26:45</td>
                            </tr>
                            <tr>
                                <td class="first-cell">Tgl. Cetak</td>
                                <td class="px-3">:</td>
                                <td>04-05-2023 13:37:59</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-lg-5">
                        <table class="text-regular-14">
                            <tr>
                                <td class="first-cell">No. Fisioterapi</td>
                                <td class="px-3">:</td>
                                <td>202301300002</td>
                            </tr>
                            <tr>
                                <td class="first-cell">Tanggal Lahir</td>
                                <td class="px-3">:</td>
                                <td>03-02-2021</td>
                            </tr>
                            <tr>
                                <td class="first-cell">Umur</td>
                                <td class="px-3">:</td>
                                <td>2 Tahun 3 Bulan 1 Hari</td>
                            </tr>
                            <tr>
                                <td class="first-cell">Kelamin</td>
                                <td class="px-3">:</td>
                                <td>LAKI - LAKI</td>
                            </tr>
                            <tr>
                                <td class="first-cell"> Dokter Pengirim</td>
                                <td class="px-3">:</td>
                                <td>simrs, Adm</td>
                            </tr>
                            <tr>
                                <td class="first-cell">Dokter Fisioterapi</td>
                                <td class="px-3">:</td>
                                <td>dr. Cici Julia Sri Dewi, Sp.PK</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <!-- /info-table -->
                <div class="mt-24">
                    <!-- <b-table-simple fixed responsive hover bordered>
                        <b-thead class="text-uppercase text-center">
                            <b-tr>
                                <b-th>Jenis Pemeriksaan</b-th>
                                <b-th>Hasil</b-th>
                                <b-th>Nilai</b-th>
                                <b-th>Rujukan</b-th>
                            </b-tr>
                        </b-thead>
                        <b-tbody>
                            <b-tr>
                                <b-td colspan="4" class="py-2">DARAH RUTIN LAKI-LAKI</b-td>
                            </b-tr>
                            <b-tr>
                                <b-td class="py-2">- HEMOGLOBIN (HB)</b-td>
                                <b-td>12</b-td>
                                <b-td>11.7-15.5</b-td>
                                <b-td>g/dL</b-td>
                            </b-tr>
                            <b-tr>
                                <b-td class="py-2">- HEMATOKRIT (HT)</b-td>
                                <b-td>36</b-td>
                                <b-td>35.0-47.0</b-td>
                                <b-td>%</b-td>
                            </b-tr>
                            <b-tr>
                                <b-td class="py-2">- LEUKOSIT (L)</b-td>
                                <b-td>6</b-td>
                                <b-td>3.6-11.0</b-td>
                                <b-td>10^3/?L</b-td>
                            </b-tr>
                            <b-tr>
                                <b-td class="py-2">- TROMBOSIT (TR)</b-td>
                                <b-td>155</b-td>
                                <b-td>150-440</b-td>
                                <b-td>10^3/?L</b-td>
                            </b-tr>
                            <b-tr>
                                <b-td class="py-2">- ERITROSIT</b-td>
                                <b-td>4</b-td>
                                <b-td>3.8-5.2</b-td>
                                <b-td>10^6/?L</b-td>
                            </b-tr>
                            <b-tr>
                                <b-td class="py-2">- MCV</b-td>
                                <b-td>85</b-td>
                                <b-td>80-100</b-td>
                                <b-td>fl</b-td>
                            </b-tr>
                            <b-tr>
                                <b-td class="py-2">- MCH</b-td>
                                <b-td>30</b-td>
                                <b-td>26-34</b-td>
                                <b-td>pg</b-td>
                            </b-tr>
                            <b-tr>
                                <b-td class="py-2">- MCHC</b-td>
                                <b-td>35</b-td>
                                <b-td>32-36</b-td>
                                <b-td>g/dL</b-td>
                            </b-tr>
                        </b-tbody>
                    </b-table-simple> -->
                </div>
                <!-- /table -->
                <div class="mt-32">
                    <p class="text-regular-14 mt-16">Kesimpulan: </p>
                    <p class="text-regular-14 mt-16">Catatan: </p>
                    <p class="text-regular-14 mt-16">Saran: </p>
                </div>
            </div>
        </b-modal>
        <b-modal v-model="modal.edit" scrollable size="md" title="Ubah Rincian" hide-footer>
            <div>
                <label class="c-form-label">Tindakan Fisioterapi</label>
                <b-form-select></b-form-select>
            </div>
            <b-button @click="modal.edit = false" variant="indigo" class="mt-24 float-right">Simpan</b-button>
        </b-modal>
        <b-modal v-model="modal.delete" scrollable size="md" title="Konfirmasi Hapus" hide-footer>
            <p class="text-regular-16">Apakah Anda yakin ingin menghapus data?</p>
            <div class="d-flex">
                <b-button @click="modal.delete = false" size="sm" variant="outline-secondary"
                    class="mt-24 mr-2 ml-auto">Close</b-button>
                <b-button @click="modal.delete = false" size="sm" variant="danger-600" class="mt-24">Ya, Hapus</b-button>
            </div>
        </b-modal>
    </div>
</template>
<script>
import { mapState } from 'vuex'
import patientMenu from '@/@core/components/tab-menu/patientMenu.vue';
import administration from '@/views/layouts/patient/doctor/administration.vue';
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import vSelect from 'vue-select'

export default {
    name: 'Fisioterapi',
    components: {
        patientMenu, administration, vSelect
    },
    data() {
        return {
            searchphy: '',
            modal: {
                show: false,
                result: false,
                edit: false,
                delete: false,
            },
            physiotherapy_table: {
                fields: [
                    { key: 'no', label: 'No' },
                    { key: 'costing_name', label: 'Rincian Pemeriksaan Fisioterapi' },
                    { key: 'status', label: 'Status Kunjungan ke Fisioterapi' },
                    { key: 'inspection_result', label: 'Hasil Pemeriksaan' },
                    { key: 'date_request', label: 'Order' },
                    { key: 'action', label: 'Aksi' },
                ],
                // items: [
                //     {
                //         no: '1',
                //         inspection_detail: 'Hematologi Lengkap',
                //         visit_status: 'Sudah Melakukan Kunjungan ke Fisioterapi',
                //         inspection_result: '11 Jul 2024 12:00:00',
                //         request: '11 Jul 2024 12:00:00'
                //     }
                // ]
            },
            isDisabled: false,
            form_soap: {
                id:'',
                view:'add',
                soap_id:'',
            },
            userData: JSON.parse(localStorage.getItem('userData')),
            regData: {},
            PhysiotherapyOrder:[],
            paramsInsuranceServiceClassLab:{}
        }
    },
    computed: {
        ...mapState({ 
            itemsPhysiotherapy: state => state.physiotherapy.package.items,
            OptionsSoapPlanDoctor: state => state.soap.options.plandoctor
        }),
        filteredListPhy() {
            return this.itemsPhysiotherapy.filter((phy) => {
                const computedPhyObj = { ...phy}
                return Object.keys(computedPhyObj)
                    .some(key => ('' + computedPhyObj[key]).toLowerCase().includes(this.searchphy.toLowerCase()))
            })
        },
    },
    methods: {
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                employee: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },
        refresh () {
            router.go(router.currentRoute)
        },
        getData (id) {
			this.$store.dispatch('registration/getById', id).then(data => {
                this.regData = data[0]
                this.paramsInsuranceServiceClassLab = {
                    type_costing_id:4,
                    // company_id:this.regData.registration_insurance[0].company_id,
                    // service_class_id:this.regData.registration_insurance[0].service_class_id
                }
                this.getDataRequest(data[0].norm)
                this.$store.dispatch('physiotherapy/getPackageAll', this.paramsInsuranceServiceClassLab)
            })
        },
        validationForm () {
            this.isDisabled = true
            // this.$refs.simpleRules.validate()
            // .then(success => {
            //     if (success) {
                const formData = this.form_soap
                this.$store.dispatch('physiotherapy/formPostPut', formData)
                .then(res => {
                    this.modal.show = false
                    this.notification('Success','CheckIcon','success','You have successfully')
                    this.getDataRequest(this.regData.norm)
                    this.isDisabled = false
                }, 
                err => {
                    this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                    this.isDisabled = false
                })
                // } else {
                // this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                // }
            // })
        },
        async getDataRequest(id) {
            try {
                const response = await this.$store.dispatch('physiotherapy/getOrderByNorm', id)
                console.log(response)
                if (response.rows.length > 0) {
                    this.PhysiotherapyOrder = response.rows
                }
            } catch (error) {
                this.notification('Failed', 'CloseIcon', 'danger', 'Something Wrong!');
            }
        }
    },
    mounted () {
        if (this.$route.params.id) {
            // this.show = true
            this.id = this.$route.params.id
            this.getData(this.$route.params.id)
            this.$store.dispatch('soap/getSoapDoctorById', this.$route.params.id)
        }
    },
}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>
<style scoped>
@import '../../../../@core/css/utility.css';

.label-head::before {
    content: '';
    border-left: 6px solid #648DE1;
}

.c-img {
    aspect-ratio: 1/1;
    object-fit: cover;
    max-width: 150px;
    max-height: 150px;
    border: 10px solid white;
}

li {
    list-style-type: none;
}

li a {
    color: #78838C;
}

li a:hover {
    text-decoration: none;
    color: #101725;
    background-color: rgb(245, 239, 254);
    cursor: pointer;
}

li a.active {
    background-color: rgba(132, 66, 243, 0.05);
    color: indigo;
    border: 1px solid indigo;
    color: #1B1B1B;
}

li a.active:hover {
    border: 1px solid indigo;
    background-color: rgb(245, 239, 254);
    color: indigo;
    cursor: pointer;
}

.first-cell {
    min-width: 120px;
}

td {
    padding-top: 2px;
    padding-bottom: 2px;
}</style>