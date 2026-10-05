<template>
    <div>
        <patientMenu selectedMenu="menu18" />
        <div class="label-head my-16">
            <span class="px-16 text-bold-24 text-neutral-700">Riwayat Pemeriksaan Radiologi</span>
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
                            <td class="text-medium-14">11 Jul 2024 - 12:03:47</td>
                        </tr>
                    </table> -->
                </div>
                <div class="col-12 col-lg-6 mt-16 mt-lg-0">
                    <div class="d-flex justify-content-lg-end">
                        <b-button @click="modal.show = !modal.show" pill variant="info-600" size="sm"
                            class="px-16 ml-lg-2 mt-8 mt-lg-0">
                            <b-icon icon="plus" /> <span>Order Pemeriksaan</span>
                        </b-button>
                    </div>
                </div>
            </div>
        </div>
        <template v-for="(itemsResult,i) in RadiologyOrder">
            <div class="card-body mt-32 border rounded-lg">
                <p class="text-regular-14">Rujukan Radiologi, Dokter : <span class="font-weight-bold"> {{itemsResult.doctor_in_charge_name}} </span></p>
                <p class="text-regular-14">Waktu Order : <span class="font-weight-bold">{{itemsResult.date_request}}</span></p>
                <div class="row mx-0 mt-28">
                    <router-link :to="{ name: 'RadiologyListView' }">
                        <b-button size="sm" variant="outline-success-600" class="px-16 mr-2">
                            <feather-icon icon="ArrowLeftIcon" /> Ke Modul Radiologi
                        </b-button>
                    </router-link>
                    <router-link :to="{ name: 'PatientRadiologyOrderCertificateView' }">
                        <b-button size="sm" variant="success-600" class="px-16 mr-2">
                            <feather-icon icon="PrinterIcon" /> Print Permintaan
                        </b-button>
                    </router-link>
                    <b-button @click="modal.show = !modal.show" size="sm" variant="outline-success-600" class="px-16">
                        <feather-icon icon="PlusIcon" /> Tambah
                    </b-button>
                </div>
                <div class="mt-16 mt-lg-24">
                    <b-table small responsive bordered head-variant="light" :items="itemsResult.radiology_request_item"
                        :fields="radiology_table.fields" tbody-tr-class="text-medium-14" thead-class="text-bold-14"
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
                        <template #cell(inspection_detail)="data">
                            <div style="min-width: 500px;">
                                <span>{{ data.value }}</span>
                            </div>
                        </template>
                        <template #cell(status)="data">
                            <div style="min-width: 140px;">
                                <span :class="data.value == '2' ? 'text-success-400' : 'text-danger-600'">{{ data.value == '2' ? 'Sudah Melakukan Kunjungan ke Radiologi' : 'Belum Melakukan Kunjungan ke Radiologi' }}</span>
                            </div>
                        </template>
                        <template #cell(inspection_result)="data">
                            <div style="min-width: 140px;">
                                <span>{{ data.value }}</span>
                            </div>
                        </template>
                        <template #cell(action)="row">
                            <div style="min-width: 200px;">
                                <b-button @click="modal.edit = !modal.edit" size="sm" variant="warning-500" class="mr-2">
                                    <feather-icon icon="EditIcon" />
                                </b-button>
                                <b-button @click="modal.delete = !modal.delete" size="sm" variant="danger-600" class="mr-2">
                                    <feather-icon icon="TrashIcon" />
                                </b-button>
                                <b-button variant="primary-600" @click="isRefcontrol(urlbe,row.item.url)" size="sm" class="mr-2">Lihat Hasil <feather-icon icon="SearchIcon" /></b-button>
                            </div>
                        </template>
                    </b-table>
                </div>
            </div>
        </template>
        <b-modal v-model="active.refcontrol" scrollable size="xl" @close="isRefcontrol()" title="Hasil Radiologi" hide-footer>
            <div class="row">
                <div class="col-12 col-lg-12 mt-1">
                    <template v-for="(img,index) in imageview">
                        <b-img rounded :src="urlbe+img.url"/>
                    </template>
                </div>
            </div>
        </b-modal>

        

        <b-modal v-model="modal.show" scrollable size="xl" title="Order Radiologi" hide-footer>
            <div class="row">
                <div class="col-lg-4">
                    <label class="c-form-label">Tanggal Permintaan</label>
                    <b-form-input v-model="form_soap.date_request" type="datetime-local" />
                </div>
            </div>
            <div class="mt-12">
                <b-form-input type="text" v-model="searchrad" placeholder="Pencarian"></b-form-input>
                <div class="col-lg-12 mt-3">
                    <h5>Checked Items:</h5>
                    <b-form-checkbox-group v-model="form_soap.radiology_request_items">
                        <b-form-checkbox
                        v-for="(item, index) in form_soap.radiology_request_items"
                        :key="index"
                        :value="item"
                        >
                        {{ item.costing_name }}
                        </b-form-checkbox>
                    </b-form-checkbox-group>
                </div>
                <div class="col mt-12 py-2 bg-neutral-100" style="height: 400px; overflow-y: scroll;">
                    <div class="row">
                        <b-form-checkbox-group size="sm" stacked>
                            <b-form-checkbox-group class="col-lg-12" v-model="form_soap.radiology_request_items">
                                <div v-for="(item, index) in filteredListRad" :key="index" :class="index != 0 ? 'mt-32' : 'mt-0'">
                                    <div class="font-weight-bold text-uppercase">{{ item.category_name }}</div>
                                    <div class="row mx-0"> 
                                        <b-form-checkbox v-for="(list, packIndex) in item.category_items" v-bind:key="packIndex" :value="{'service_class_id': list.costing_services_class.service_class_id ,'radiology_package_id':list.id, 'costing_name':list.costing_name}" class="col-lg-5">{{list.costing_name }}</b-form-checkbox>
                                    </div>
                                </div>
                            </b-form-checkbox-group>
                        </b-form-checkbox-group>
                    </div>
                </div>
            </div>
            <div class="row mx-0 mt-32">
                <b-button @click="modal.show = false" variant="outline-secondary" class="mr-2 ml-auto">Tutup</b-button>
                <b-button :disabled="isDisabled"  @click.prevent="validationForm" variant="indigo"><feather-icon icon="SaveIcon" class="mr-1" />Simpan</b-button>
            </div>
        </b-modal>
        <b-modal v-model="modal.result" scrollable size="xl" title="View Hasil" hide-footer>
            <div>
                <p class="text-center text-bold-16">HASIL RADIOLOGI</p>
                <div class="row mt-28">
                    <div class="col-lg-5">
                        <table class="text-regular-14">
                            <tr>
                                <td class="first-cell">No. Reg / RM</td>
                                <td class="px-3">:</td>
                                <td>{{ this.regData.code }} / {{ this.regData.norm }}</td>
                            </tr>
                            <tr>
                                <td class="first-cell">Nama Pasien</td>
                                <td class="px-3">:</td>
                                <td>
                                <span>({{this.regData.alias_name}}) </span>
                                <span>{{this.regData.name}}</span></td>
                            </tr>
                            <tr>
                                <td class="first-cell">Tgl. Permintaan</td>
                                <td class="px-3">:</td>
                                <td>11-07-2024 12:38:08</td>
                            </tr>
                            <tr>
                                <td class="first-cell">Tgl. Hasil</td>
                                <td class="px-3">:</td>
                                <td>11-07-2024 12:26:45</td>
                            </tr>
                            <tr>
                                <td class="first-cell">Alamat</td>
                                <td class="px-3">:</td>
                                <td></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-lg-5">
                        <table class="text-regular-14">
                            <tr>
                                <td class="first-cell">No. Radiologi</td>
                                <td class="px-3">:</td>
                                <td>202401300002</td>
                            </tr>
                            <tr>
                                <td class="first-cell">Tanggal Lahir</td>
                                <td class="px-3">:</td>
                                <td>{{ this.regData.birth_date }}</td>
                            </tr>
                            <tr>
                                <td class="first-cell">Umur</td>
                                <td class="px-3">:</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="first-cell">Kelamin</td>
                                <td class="px-3">:</td>
                                <td>{{ this.regData.sex_name }}</td>
                            </tr>
                            <tr>
                                <td class="first-cell">	Dokter Pengirim</td>
                                <td class="px-3">:</td>
                                <td>{{ this.regData.doctor_in_charge_name }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <!-- /info-table -->
                <div class="mt-32">
                    <p class="text-regular-14">Yth {{ this.regData.doctor_in_charge_name }}</p>
                    <p class="text-bold-14 mt-16">Kesan : </p>
                </div>
                <div class="mt-32">
                    <p class="text-regular-14">Btk</p>
                    <p class="text-regular-14">Salam Sejawat</p>
                    <!-- img -->
                    <img src="@/@core/assets/images/nurse/vector-sign.png" width="80px" alt="your-sign" class="my-2">
                    <p class="text-regular-14 mt-16">dr. Anis,Sp.Rad </p>
                </div>
            </div>
        </b-modal>
        <b-modal v-model="modal.edit" scrollable size="md" title="Ubah Rincian" hide-footer>
            <div>
                <label class="c-form-label">Tindakan Radiologi</label>
                <b-form-select></b-form-select>
            </div>
            <b-button @click="modal.edit = false" variant="indigo" class="mt-24 float-right">Simpan</b-button>
        </b-modal>
        <b-modal v-model="modal.delete" scrollable size="md" title="Konfirmasi Hapus" hide-footer>
            <p class="text-regular-16">Apakah Anda yakin ingin menghapus data?</p>
            <div class="d-flex">
                <b-button @click="modal.delete = false" size="sm" variant="outline-secondary" class="mt-24 mr-2 ml-auto">Close</b-button>
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
    name: 'Radiologi',
    components: {
        patientMenu, administration,vSelect
    },
    data() {
        return {
            searchrad: '',
            imageview:'',
            active:{refcontrol:false},
            urlbe:process.env.VUE_APP_IMAGE,
            itemsRadiology:[],
            modal: {
                show: false,
                result: false,
                edit: false,
                delete: false,
            },
            radiology_table: {
                fields: [
                    { key: 'no', label: 'No' },
                    { key: 'costing_name', label: 'Rincian Pemeriksaan Radiologi' },
                    { key: 'status', label: 'Status Kunjungan ke Radiologi' },
                    { key: 'date_process', label: 'Hasil Pemeriksaan' },
                    { key: 'date_request', label: 'Order' },
                    { key: 'action', label: 'Aksi' },
                ]
            },
            isDisabled: false,
            form_soap: {
                id:'',
                view:'add',
                soap_id:'',
                doctor_in_charge_id:'',
                registration_destination_id:'',
            },
            userData: JSON.parse(localStorage.getItem('userData')),
            regData: {},
            RadiologyOrder:[]
        }
    },
    computed: {
        ...mapState({ 
            OptionsSoapPlanDoctor: state => state.soap.options.plandoctor
        }),
        filteredListRad() {
            return this.itemsRadiology.map((rad) => {
                const filteredCategoryItems = rad?.category_items?.filter((item) =>
                    item.costing_name.toLowerCase().includes(this.searchrad.toLowerCase())
                );

                return {
                    ...rad,
                    category_items: filteredCategoryItems,
                };
            }).filter(rad => rad?.category_items?.length > 0); 
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
        isRefcontrol(url,data) {
            this.imageview = ''
            this.imageview = data
            this.active.refcontrol = !this.active.refcontrol
        },
        refresh () {
            router.go(router.currentRoute)
        },
        getData (id) {
			this.$store.dispatch('registration/getById', id).then(data => {
                this.regData = data[0]
                this.paramsInsuranceServiceClassRad = {
                    type_costing_id:5,
                    // company_id:this.regData.registration_insurance[0].company_id,
                    // service_class_id:this.regData.registration_insurance[0].service_class_id
                }
                this.getDataRequest(data[0].norm)
                this.$store.dispatch('radiology/getPackageAll', this.paramsInsuranceServiceClassRad)
                this.$store.dispatch('soap/getByRegId', this.$route.params.id).then((res) => {
                    console.log(res[0].id)
                    console.log(res[0].doctor_in_charge_id)
                    console.log(res[0].register_destination_id)
                    this.form_soap.soap_id = res[0].id
                    this.form_soap.doctor_in_charge_id =  res[0].doctor_in_charge_id
                    this.form_soap.register_destination_id = res[0].register_destination_id  
                })
                this.$store.dispatch('radiology/getRadiologyPackageV2').then((response) => {
                    this.itemsRadiology = response
                })
            })
        },
        validationForm () {
            this.isDisabled = true
            // this.$refs.simpleRules.validate()
            // .then(success => {
            //     if (success) {
                const formData = this.form_soap
                this.$store.dispatch('radiology/formPostPut', formData)
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
                const response = await this.$store.dispatch('radiology/getOrderByNorm', id)
                // console.log(response)
                if (response.rows.length > 0) {
                    this.RadiologyOrder = response.rows
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
    }
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
.first-cell{
    min-width: 120px;
}
td{
    padding-top: 2px;
    padding-bottom: 2px;
}
</style>