<template>
    <div>
        <div class="col label-head">
            <span class="px-16 text-medium-24 text-neutral-500">DATA PERMINTAAN LABORATORIUM PASIEN</span>
        </div>
        <section class="col mt-32">
            <b-table-simple responsive class="text-medium-14">
                <b-tbody>
                    <b-tr>
                        <b-td style="width: 15%;">No. Reg / RM</b-td>
                        <b-td style="width: 2%;">:</b-td>
                        <b-td style="width: 38%;" class="font-weight-bold">{{regData.code}} / {{regData.norm}}</b-td>
                        <b-td style="width: 15%;">Unit Pengirim</b-td>
                        <b-td style="width: 2%;">:</b-td>
                        <b-td style="width: 38%;" class="font-weight-bold">{{regData.health_facility_item_name}}</b-td>
                    </b-tr>
                    <b-tr>
                        <b-td style="width: 15%;">NIK</b-td>
                        <b-td style="width: 2%;">:</b-td>
                        <b-td style="width: 38%;" class="font-weight-bold">{{regData.idcard}}</b-td>
                        <b-td style="width: 15%;">No. Order</b-td>
                        <b-td style="width: 2%;">:</b-td>
                        <b-td style="width: 38%;" class="font-weight-bold">{{orderData.no_request}}</b-td>
                    </b-tr>
                    <b-tr>
                        <b-td style="width: 15%;">Nama Pasien</b-td>
                        <b-td style="width: 2%;">:</b-td>
                        <b-td style="width: 38%;" class="font-weight-bold">{{regData.name}}</b-td>
                        <b-td style="width: 15%;">Tgl. Permintaan</b-td>
                        <b-td style="width: 2%;">:</b-td>
                        <b-td style="width: 38%;" class="font-weight-bold">{{orderData.date_request}}</b-td>
                    </b-tr>
                    <b-tr>
                        <b-td style="width: 15%;">Tempat, Tanggal Lahir</b-td>
                        <b-td style="width: 2%;">:</b-td>
                        <b-td style="width: 38%;" class="font-weight-bold">{{regData.birth_place}}, {{regData.birth_date}}</b-td>
                        <b-td style="width: 15%;">Dokter Pengirim</b-td>
                        <b-td style="width: 2%;">:</b-td>
                        <b-td style="width: 38%;" class="font-weight-bold">{{regData.doctor_in_charge_name}}</b-td>
                    </b-tr>
                    <b-tr>
                        <b-td style="width: 15%;">Kelamin</b-td>
                        <b-td style="width: 2%;">:</b-td>
                        <b-td style="width: 38%;" class="font-weight-bold">{{regData.sex_name}}</b-td>
                        <b-td style="width: 10%;">Usia Pasien</b-td>
                        <b-td style="width: 2%;">:</b-td>
                        <b-td style="width: 38%;" class="font-weight-bold">{{parsed_age(regData.birth_date)}}</b-td>
                    </b-tr>
                    <b-tr>
                        <b-td style="width: 15%;">No Telepon</b-td>
                        <b-td style="width: 2%;">:</b-td>
                        <b-td style="width: 38%;" class="font-weight-bold">{{regData.phone}}</b-td>
                        <b-td style="width: 10%;">Diagnosa</b-td>
                        <b-td style="width: 2%;">:</b-td>
                        <b-td style="width: 38%;" class="font-weight-bold">{{ main_diagnose }}</b-td>
                    </b-tr>
                    <b-tr>
                        <b-td style="width: 15%;">Pendidikan</b-td>
                        <b-td style="width: 2%;">:</b-td>
                        <b-td style="width: 38%;" class="font-weight-bold">{{ getlastEdu(regData.last_edu_id) }}</b-td>
                        <b-td style="width: 10%;">Pekerjaan</b-td>
                        <b-td style="width: 2%;">:</b-td>
                        <b-td style="width: 38%;" class="font-weight-bold">{{ getProffesion(regData.profession_id) }}</b-td>
                    </b-tr>
                    <b-tr>
                        <b-td style="width: 15%;">Alamat</b-td>
                        <b-td style="width: 2%;">:</b-td>
                        <b-td colspan="4" class="font-weight-bold">{{regData.address}}</b-td>
                    </b-tr>
                </b-tbody>
            </b-table-simple>
        </section>
        <section class="col mt-40">
            <b-table bordered responsive hover small :fields="laboratory_action.field" :items="orderData.laboratory_request_item" head-variant="light" tbody-tr-class="text-medium-14" class="mt-12 text-center">
                <template #cell(costing_name)="data">
                    <div style="min-width: 140px;">
                        <span>{{ data.value }}</span>
                    </div>
                </template>

                <template #cell(var_1)="data">
                    <div style="min-width: 140px;">
                        <span>{{ data.value | currency }}</span>
                    </div>
                </template>

                <template #cell(status)="row">
                    <div style="min-width: 140px;">
                        <template v-if="row.value == 1">
                            Permintaan Baru
                        </template>
                        <template v-if="row.value == 2 || row.value == 3">
                            <router-link :to="{ path: '/laboratory/list/process/result/'+ orderData.id+'/'+row.item.id}">
                                <b-button size="sm" variant="orange-400" class="text-white">
                                    <feather-icon icon="PrintIcon" /> Cetak Hasil
                                </b-button>
                            </router-link>
                        </template>
                    </div>
                </template>

                <template #cell(bill_status)="data">
                    <div style="min-width: 140px;">
                        <b-badge :variant="data.value === 'Sudah Melunasi Pembayaran' ? 'success-500' : 'warning-500'" class="p-1">
                            <feather-icon icon="CheckSquareIcon" /> {{ data.value }}
                        </b-badge>
                    </div>
                </template>
            </b-table>
        </section>
        <!-- <section class="col mt-24" v-if="orderData.status == '2' || orderData.status == '3'">
            <div class="mt-24">
                <div>
                    <label class="c-form-label">Kesimpulan:</label>
                    <b-textarea v-model="form.conclusion" :disabled="orderData.status == 3 ? true : false" rows="2"></b-textarea>
                </div>
                <div class="mt-12">
                    <label class="c-form-label">Catatan :</label>
                    <b-textarea v-model="form.notes" :disabled="orderData.status == 3 ? true : false" rows="2"></b-textarea>
                </div>
                <div class="mt-12">
                    <label class="c-form-label">Saran :</label>
                    <b-textarea v-model="form.advice" :disabled="orderData.status == 3 ? true : false" rows="2"></b-textarea>
                </div>
                <div class="mt-12">
                    <label class="c-form-label">Pemeriksa Laboratorium:</label>
                    <v-select v-model="form.user_lab_id" aria-readonly="true" :options="OptionsLabor" :disabled="orderData.status == 3 ? true : false" :reduce="(OptionsLabor) => OptionsLabor" label="title" placeholder="-- Dokter --"></v-select>
                </div>
                <div class="mt-12">
                    <label class="c-form-label">Verifikator:</label>
                    <v-select v-model="form.doctor_lab_id" aria-readonly="true" :options="OptionsDoctor" :disabled="orderData.status == 3 ? true : false" :reduce="(OptionsDoctor) => OptionsDoctor" label="title" placeholder="-- Dokter --"></v-select>
                </div>
            </div>
        </section> -->
        <section class="col mt-24">
            <div class="row mx-0">
                <!-- <router-link :to="{ path: '/patient/laboratory/'+ regData.registration_destination_id}" class="rounded-pill" >
                    <b-button size="sm" variant="outline-secondary" class="mr-2">
                        <feather-icon icon="FileTextIcon" /> Tambah Tindakan
                    </b-button>
                </router-link> -->
                
                <template v-if="orderData.is_lis == '1'">
                    <b-button variant="primary-600" @click="isRefcontrol(orderData.id)" size="sm" class="text-white mr-2">
                        <feather-icon icon="EyeIcon" class="mr-1" />
                        Lihat Hasil
                    </b-button>
                </template>

                <template v-if="orderData.is_lis == '0'">
                    <b-button size="sm" :disabled="isDisabled" @click.prevent="validationFormLis(id)" variant="primary-400" class="text-white mr-2">
                        <feather-icon icon="SendIcon" /> Send LIS
                    </b-button>
                </template>

                <template v-if="orderData.status == '1'">
                    <b-button size="sm" :disabled="isDisabled"  @click.prevent="validationForm(id)" variant="orange-400" class="text-white">
                        <feather-icon icon="SaveIcon" /> Proses
                    </b-button>
                </template>

                <template v-if="orderData.status == '3'">
                    <router-link :to="{ path: '/laboratory/print/'+ id}">
                        <b-button size="sm" variant="outline-secondary" class="mr-2">
                            <feather-icon icon="PrinterIcon" /> Cetak
                        </b-button>
                    </router-link>
                </template>

                <!-- <template v-if="orderData.status == '2'">
                    <b-button size="sm" :disabled="isDisabled"  @click.prevent="validationFormVerifikasi(id)" variant="orange-400" class="text-white mr-2">
                        <feather-icon icon="SaveIcon" /> Verifikasi
                    </b-button>
                </template> -->
                <!-- <router-link :to="{ name: 'LaboratoryListProcessOrderView' }">
                    <b-button size="sm" variant="outline-secondary">
                        <feather-icon icon="PrinterIcon" /> Cetak Order
                    </b-button>
                </router-link> -->
            </div>
        </section>
        
        <b-modal v-model="active.refcontrol" scrollable size="lg" title="HASIL PEMERIKSAAN" hide-footer>
            <div class="row">
                <div class="col-12 col-lg-12 mt-1">
                    <b-table bordered responsive small head-variant="light" :items="refer_result.result_detail" thead-class="fs-14" tbody-tr-class="fs-12" class="text-center">
                    </b-table>
                </div>
            </div>
        </b-modal>
    </div>
</template>

<script>
import { mapState } from 'vuex';
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import moment from 'moment'
// import io from 'socket.io-client';

export default {
    components: { ToastificationContent },
    data() {
        return {
            // socket : io(process.env.VUE_APP_SOCKET_IO, { transports: ["websocket", "polling", "flashsocket"],withCredentials: true }),
            imageview:'',
            active:{refcontrol:false},
            laboratory_action: {
                field: [
                    { key: 'no', label: 'NO' },
                    { key: 'date_request', label: 'TGL. PERMINTAAN' },
                    { key: 'costing_name', label: 'TINDAKAN LABORATORIUM' },
                    { key: 'var_1', label: 'HARGA TINDAKAN' },
                    { key: 'bill_status', label: 'STATUS BILLING' },
                    { key: 'status', label: 'ACTION' },
                ],
            },
            // form:{
            //     id:'',
            //     user_lab_id:'',
            //     doctor_lab_id:'',
            //     conclusion:'',
            //     notes:'',
            //     advice:''
            // },
            orderData:{},
            id:'',
            isDisabled: false,
            regData: {},
            refer_result: {},
            main_diagnose: '',
        }
    },
    filters:{
        currency(value) {
            return new Intl.NumberFormat("en-US", { style: "currency", currency: "IDR" }).format(value)
        }
    },
    computed: {
        ...mapState({
        OptionsDoctor: state => state.employee.options.employee,
        OptionsLabor: state => state.employee.options.labor,
        })
    },
    methods: {
        isRefcontrol(data) {
            this.imageview = data
            this.active.refcontrol = true
            this.$store.dispatch('laboratory/getResultById', this.imageview)
                .then(response => {
                    if (response?.length > 0) {
                        this.refer_result = response[0]
                        console.log('this.refer_result');

                        // nge bunyiin nya disini
                    }
                }
            )
        },
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
			this.$store.dispatch('laboratory/getByIdOrder', id).then(data => {
                this.orderData = data[0]
                this.regData = data[0].regData
                console.log('this.orderData: ', this.orderData);
                // console.log('this.regData', this.regData);
                // this.form.conclusion = this.orderData.conclusion
                // this.form.notes = this.orderData.notes
                // this.form.advice = this.orderData.advice
                // this.form.user_lab_id = {value:this.orderData.user_lab_id,title:this.orderData.user_lab_name}
                // this.form.doctor_lab_id = {value:this.orderData.doctor_lab_id,title:this.orderData.doctor_lab_name}

                this.$store.dispatch('soap/getByNorm', this.regData.norm).then((response) => {
                    let main_icd10 = ''
                    const init = response[0]
                    if (init.icd10 && init.icd10.length > 0) {
                        const code = init.icd10[0].code
                        const name = init.icd10[0].name

                        main_icd10 = code + name
                    }
                    this.main_diagnose = main_icd10
                })
            })
        },
        validationFormLis (id) {
            this.isDisabled = true
            const formData = { id: id }
            this.$store.dispatch('laboratory/formPostPutProcessLis', formData)
            .then(res => {
                this.notification('Success','CheckIcon','success','You have successfully')
                this.getData(this.$route.params.id)
                this.isDisabled = false
            }, 
            err => {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                this.isDisabled = false
            })
        },
        
        // sendNextStep(date_request, queue) {
        //     let params = {
        //         date: moment(date_request).format('YYYY-MM-DD'),
        //         queue: queue
        //     }

        //     try {
        //         this.$store.dispatch('queue/postPutQueueNextLab', params)
        //         .then(
        //             res => {
        //                 this.socket.emit('SEND_LAB', params);
        //             } 
        //         )
        //         .catch(({error, response}) => {
        //             this.notification('Failed','CloseIcon','danger',`${error}`)
        //         })
        //     } catch (error) {
        //         this.notification('Failed','CloseIcon','danger','You have unsuccessful')
        //         console.error('Error catch at: ', error)                
        //     }
        // },

        validationForm (id) {
            this.isDisabled = true
            const formData = { id: id }
            this.$store.dispatch('laboratory/formPostPutProcess', formData)
            .then(res => {
                this.notification('Success','CheckIcon','success','You have successfully')
                // this.sendNextStep(this.orderData.date_request, this.orderData.queue)
                this.getData(this.$route.params.id)
                this.isDisabled = false
            }, 
            err => {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                this.isDisabled = false
            })
        },
        // validationFormVerifikasi (id) {
        //     this.isDisabled = true
        //     const formData = this.form
        //     this.$store.dispatch('laboratory/formPostPutVerify', formData)
        //     .then(res => {
        //         this.notification('Success','CheckIcon','success','You have successfully')
        //         // this.sendNextStep(this.orderData.date_request, this.orderData.queue)
        //         this.getData(this.$route.params.id)
        //         this.isDisabled = false
        //     }, 
        //     err => {
        //         this.notification('Failed','CloseIcon','danger','You have unsuccessful')
        //         this.isDisabled = false
        //     })
        // },
        parsed_age(input_date) {
            const birthDate = moment(input_date, 'YYYY-MM-DD');
            const currentDate = moment();

            const years = currentDate.diff(birthDate, 'years');
            birthDate.add(years, 'years');
            const months = currentDate.diff(birthDate, 'months');
            birthDate.add(months, 'months');
            const days = currentDate.diff(birthDate, 'days');

            return `${years} th ${months} bln ${days} hr`;
        },
        getProffesion(code) {
            const proffesions = {
                '1': 'Pegawai Negeri',
                '2': 'Pegawai Swasta',
                '3': 'Pekerja Lepas',
                '4': 'Pelajar',
                '5': 'Mahasiswa',
                '6': 'Ibu Rumah Tangga',
                '7': 'TNI / POLRI',
                '8': 'Pensiunan',
                '9': 'Tidak Bekerja',
                '10': 'Lain Lain',
                '11': 'BUMN',
                '12': 'Buruh',
            };
            return proffesions[code] || '-';
        },
        getlastEdu(code) {
            const proffesions = {
                '1': 'Sekolah Dasar',
                '2': 'Sekolah Menengah Pertama',
                '3': 'Sekolah Menengah Atas',
                '4': 'Diploma 1',
                '5': 'Diploma 3',
                '6': 'Sarjana',
                '7': 'Magister',
                '8': 'Doktor',
                '9': 'Spesialist',
                '10': 'Sastra 4',
            };
            return proffesions[code] || '-';
        },

    },
    mounted () {
        if (this.$route.params.id) {
            this.id = this.$route.params.id
            this.getData(this.$route.params.id)
            // this.show = true
            // this.form.id = this.$route.params.id
        }
        this.$store.dispatch('employee/getParamSpecialistLab')
        this.$store.dispatch('employee/getParamLabor')
    }
}
</script>
<style scoped>
.label-head::before {
    content: '';
    border-left: 6px solid #648DE1;
}
</style>
