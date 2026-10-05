<template>
    <div>
        <Layout
            :layout_breadcrumb_items="[
                { text: 'Home', link: '/' },
                { text: 'Laboratorium' }
            ]"
        >
            <div class="col label-head">
                <span class="px-16 text-medium-24 text-neutral-500">Hasil Pemeriksaan Laboratorium</span>
            </div>
            <div class="row fs-14 mt-16">
                <div class="col-lg-6">
                    <table class="table table-md mb-0" style="border: 1px solid #dee2e6;">
                        <tr>
                            <td class="align-top" style="width: 150px;">Nama Pasien</td>
                            <td class="align-top text-center" style="width: 20px;">:</td>
                            <td class="align-top">{{ regData.alias_name }}. {{ regData.name }} ({{ getSexCode(regData.sex) }})</td>
                        </tr>
                        <tr>
                            <td class="align-top">Tanggal Lahir</td>
                            <td class="align-top text-center">:</td>
                            <td class="align-top">{{ regData.birth_date }} ({{ parsedAge(regData.birth_date) }})</td>
                        </tr>
                        <tr>
                            <td class="align-top">No. RM / NIK</td>
                            <td class="align-top text-center">:</td>
                            <td class="align-top">{{ regData.norm }} / {{ regData.idcard }}</td>
                        </tr>
                        <tr>
                            <td class="align-top">No. Telepon</td>
                            <td class="align-top text-center">:</td>
                            <td class="align-top">{{ regData.phone }}</td>
                        </tr>
                        <tr>
                            <td class="align-top">Alamat</td>
                            <td class="align-top text-center">:</td>
                            <td class="align-top">{{ regData.address }}</td>
                        </tr>
                        <tr>
                            <td class="align-top">Pendidikan</td>
                            <td class="align-top text-center">:</td>
                            <td class="align-top">{{ getlastEdu(regData.last_edu_id) }}</td>
                        </tr>
                        <tr>
                            <td class="align-top">Pekerjaan</td>
                            <td class="align-top text-center">:</td>
                            <td class="align-top">{{ getProffesion(regData.profession_id) }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-lg-6">
                    <table class="table table-md mb-0" style="border: 1px solid #dee2e6;">
                        <tr>
                            <td class="align-top" style="width: 150px;">No. Permintaan</td>
                            <td class="align-top text-center" style="width: 20px;">:</td>
                            <td class="align-top">{{ orderData.no_request }}</td>
                        </tr>
                        <tr>
                            <td class="align-top">Tgl. Permintaan</td>
                            <td class="align-top text-center">:</td>
                            <td class="align-top">{{ localFullDay(orderData.date_request) }}</td>
                        </tr>
                        <tr>
                            <td class="align-top">Pengirim</td>
                            <td class="align-top text-center">:</td>
                            <td class="align-top">{{regData.health_facility_item_name}} - {{ regData.doctor_in_charge_name }}</td>
                        </tr>
                        <tr>
                            <td class="align-top">Usia Pasien</td>
                            <td class="align-top text-center">:</td>
                            <td class="align-top">{{ parsed_age(regData.birth_date) }}</td>
                        </tr>
                        <tr>
                            <td class="align-top">Diagnosa</td>
                            <td class="align-top text-center">:</td>
                            <td class="align-top">
                                <!-- <li v-for="item in main_diagnose">{{ item.title }}</li> -->
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <section class="col mt-40">
                <p class="text-bold-14 text-center">HASIL PEMERIKSAAN LABORATORIUM</p>
                <div class="mt-16">
                    <b-table-simple fixed bordered responsive>
                        <b-thead class="text-bold-14 text-center">
                            <b-tr>
                                <b-th>PARAMETER</b-th>
                                <b-th>HASIL</b-th>
                                <b-th>NILAI RUJUKAN</b-th>
                                <b-th>SATUAN</b-th>
                            </b-tr>
                        </b-thead>
                        <b-tbody class="text-regular-14">
                            <template v-for="(item, index) in LaboratoryOrder.collation">
                                <b-tr>
                                    <b-td colspan="4" class="text-bold-14">{{ item.collation_name }}</b-td>
                                </b-tr>
                                <b-tr v-for="(item_parameter, index) in item.parameter" :key="'news-' + index +'-'+ item_parameter.id">
                                    <b-td>{{ item_parameter.name }}</b-td>
                                    <b-td><b-form-input v-model="item_parameter.result" min="1" size="sm" autocomplete="off"/></b-td>
                                    <b-td><b-form-input v-model="item_parameter.value" disabled min="1" size="sm" autocomplete="off"/></b-td>
                                    <b-td><b-form-input v-model="item_parameter.unit" disabled min="1" size="sm" autocomplete="off"/></b-td>
                                </b-tr>
                            </template>
                        </b-tbody>
                    </b-table-simple>
                </div>
                <!-- <div class="mt-24">
                    <div>
                        <label class="c-form-label">Kesimpulan:</label>
                        <b-textarea rows="4"></b-textarea>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Catatan :</label>
                        <b-textarea rows="4"></b-textarea>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Saran :</label>
                        <b-textarea rows="4"></b-textarea>
                    </div>
                </div> -->
                <div class="row mt-24 mx-0">
                    <b-button size="sm" @click="saveValidation" variant="primary-500" class="mr-2">
                        <feather-icon icon="SaveIcon" /> Simpan
                    </b-button>
                    <!-- <b-button size="sm" variant="outline-secondary">
                        <feather-icon icon="PrinterIcon" /> Cetak (Non Kop)
                    </b-button> -->
                </div>
            </section>
        </Layout>
    </div>
</template>
<script>
import dateMixin from "@/mixins/dateMixin";
import optionMixin from '@/mixins/optionMixin';
import moment from 'moment'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import router from '@/router/index.js'
export default {
    mixins: [dateMixin, optionMixin],
    components: { ToastificationContent },
    data() {
        return {
            laboratory_action: {
                field: [
                    { key: 'print_all', label: 'PRINT ALL' },
                    { key: 'costing_id', label: 'TGL. PERMINTAAN' },
                    { key: 'costing_name', label: 'TINDAKAN LABORATORIUM' },
                    { key: 'var_1', label: 'HARGA TINDAKAN' },
                    { key: 'action', label: 'TINDAKAN' },
                    { key: 'bill_status', label: 'STATUS BILLING' },
                ],
                // item: [
                //     {
                //         request_date: '24-03-2023 11:04:05',
                //         costing_name: 'Hematologi Lengkap',
                //         var_1: '104.500,00',
                //         bill_status: 'Sudah Melunasi Pembayaran',
                //     },
                // ],
            },
            results:[],
            LaboratoryOrder:[],
            orderData:{},
            regData:{}
        }
    },
    filters:{
        currency(value) {
            return new Intl.NumberFormat("en-US", { style: "currency", currency: "IDR" }).format(value)
        }
    },
    methods: {
        async saveValidation() {
            try {
                this.$store.dispatch('laboratory/sendValidationResult', { data:this.LaboratoryOrder.collation, id:this.$route.params.item, laboratory_request_id:this.id})
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.getDataRequest(this.$route.params.item)
                        router.push({ name: 'LaboratoryListProcessView', params: { id: this.id }  })
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
            } finally {
                this.isDisabled = false
            }   
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
            })
        },
        async getDataRequest(id) {
            try {
                const response = await this.$store.dispatch('laboratory/getResultInputById', id)
                console.log(response)
                this.LaboratoryOrder = response
            } catch (error) {
                this.notification('Failed', 'CloseIcon', 'danger', 'Something Wrong!');
            }
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
        parsed_age(input_date) {
            const birthDate = moment(input_date, 'YYYY-MM-DD');
            const currentDate = moment();

            const years = currentDate.diff(birthDate, 'years');
            birthDate.add(years, 'years');
            const months = currentDate.diff(birthDate, 'months');
            birthDate.add(months, 'months');
            const days = currentDate.diff(birthDate, 'days');

            return `${years} th ${months} bln ${days} hr`;
        }
    },
    mounted () {
        if (this.$route.params.id) {
            // this.show = true
            this.id = this.$route.params.id
            this.getData(this.$route.params.id)
            this.getDataRequest(this.$route.params.item)
        }
    }
}
</script>
<style scoped>
.label-head::before {
    content: '';
    border-left: 6px solid #648DE1;
}
</style>