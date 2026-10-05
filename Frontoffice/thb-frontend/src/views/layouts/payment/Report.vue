    <template>
    <div>
        <TopPaymentMenu selectedMenu="menu5" class="mt-2" />
        <div class="mt-32">
            <div class="card">
                <div @click="showFilter = !showFilter" class="card-header d-flex justify-content-between bg-primary-200">
                    <span class="text-bold-18">Filter Data</span>
                    <b-icon v-if="showFilter" icon="chevron-up" />
                    <b-icon v-else icon="chevron-down" />
                </div>
                <div v-if="showFilter" class="card-body">
                    <div class="row">
                        <div class="col-lg-6 px-lg-5 c-form">
                            <!-- <div class="mt-10 mt-lg-0">
                                <label>Nomor Pendaftaran</label>
                                <b-form-input type="number"></b-form-input>
                            </div>
                            <div class="mt-10">
                                <label>No RM</label>
                                <b-form-input type="number"></b-form-input>
                            </div>
                            <div class="mt-10">
                                <label>Instalasi</label>
                                <b-form-select></b-form-select>
                            </div> -->
                            <div class="mt-10">
                                <label>Jenis Laporan</label>
                                <b-form-select v-model="params.daily_all">
                                    <option value="how_to_pay">Cara Pembayaran</option>
                                    <option value="health_facility_item">Poli/Ruangan</option>
                                    <option value="doctor">Dokter DPJP</option>
                                </b-form-select>
                            </div>
                            <div class="mt-10">
                                <label class="c-form-label">Nama Penjamin </label>
                                <v-select v-model="params.company_id" :options="Options3rdCompany" :reduce="Options3rdCompany => Options3rdCompany" label="title" placeholder="-- Nama Penjamin --"></v-select>
                            </div>
                        </div>
                        <div class="col-lg-6 px-lg-5 c-form">
                            <div class="mt-10">
                                <label>Tanggal Mulai</label>
                                <div class="d-flex justify-content-between">
                                    <div class="row align-items-end">
                                        <div class="col">
                                            <b-form-input v-model="params.date_start" type="date" />
                                        </div>
                                        <span class="px-3">-</span> 
                                        <div class="col">
                                            <b-form-input v-model="params.time_start" type="time" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-10">
                                <label>Tanggal Akhir</label>
                                <div class="d-flex justify-content-between">
                                    <div class="row align-items-end">
                                        <div class="col">
                                            <b-form-input v-model="params.date_end" type="date" />
                                        </div>
                                        <span class="px-3">-</span>
                                        <div class="col">
                                            <b-form-input v-model="params.time_end" type="time" />
                                        </div>
                                    </div>
                                </div>
                                <!-- <div class="row mx-0">
                                    <div class="col d-flex align-items-center" >
                                        <b-form-datepicker v-model="params.date_start"></b-form-datepicker>
                                        <b-form-timepicker
                                            v-model="params.time_start"
                                         
                                        ></b-form-timepicker>
                                        <span class="px-3">-</span>
                                        <b-form-datepicker v-model="params.date_end" class="col"></b-form-datepicker>
                                        <b-form-timepicker
                                            v-model="params.time_end"
                                            
                                        ></b-form-timepicker>
                                    </div>
                                </div> -->
                            </div>
                            <!-- <div class="mt-10 mt-lg-0">
                                <label>Dokter</label>
                                <b-form-select></b-form-select>
                            </div>
                            <div class="mt-10">
                                <label>Cara Masuk</label>
                                <b-form-select></b-form-select>
                            </div>
                            <div class="mt-10">
                                <label>Jaminan</label>
                                <b-form-select></b-form-select>
                            </div>
                            <div class="mt-10">
                                <label>Petugas</label>
                                <b-form-select></b-form-select>
                            </div> -->
                        </div>
                    </div>
                    <div class="d-flex justify-content-lg-end mt-40">
                        <div class="col-lg-2">
                            <div class="row">
                                <!-- <div class="col-6 col-lg px-1 my-1">
                                    <b-button pill class="btn-block" variant="outline-primary">
                                        Search <b-icon icon="search" scale="0.7" />
                                    </b-button>
                                </div>
                                <div class="col-6 col-lg px-1 my-1">
                                    <b-button pill class="btn-block" variant="outline-primary">Cancel</b-button>
                                </div> -->
                                <div class="col-2 col-lg px-1 my-1">
                                    <b-button pill class="btn-block" @click="filterReport" variant="outline-primary">Search</b-button>
                                </div>
                                <!-- <div class="col-2 col-lg px-1 my-1">
                                    <b-button pill class="btn-block" @click="filterExport" variant="outline-primary">Export Excel</b-button>
                                </div> -->
                                <!-- <router-link :to="{ name: 'PaymentReportExportView' }">
                                    <div class="col-6 col-lg px-1 my-1">
                                        <b-button pill class="btn-block" variant="primary">Cetak Laporan</b-button>
                                    </div>
                                </router-link> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-32 mt-lg-40" v-if="hasReport">
                <div class="d-flex justify-content-between mb-2">
                    <p class="text-bold-24">Laporan Data</p>
                    <b-button v-if="reportData.length > 0" pill @click="filterExport" variant="outline-primary">
                        <b-icon icon="download" scale="0.7" /> Export Excel
                    </b-button>
                </div>
                <b-table bordered responsive hover small head-variant="light" :fields="report.field" :items="reportData"
                    class="mt-12 text-center"
                >
                </b-table>
            </div>
            <div class="mt-32 mt-lg-40" style="display:none">
                <p class="text-bold-24">List Data</p>
                <div class="row mx-0 mt-8">
                    <span class="text-regular-14">Tampilkan</span>
                    <select id="per-page-select" v-model="patient.perPage"
                        class="px-8 border-left-0 border-right-0 border-top-0 mx-2">
                        <option value="5" selected>5</option>
                        <option value="10" selected>10</option>
                        <option value="15" selected>15</option>
                    </select>
                    <span class="text-regular-14">Entri</span>
                </div>
                <b-table bordered responsive hover small head-variant="light" :fields="patient.field" :items="patient.item"
                    :current-page="patient.currentPage" @filtered="patientFiltered" :per-page="patient.perPage"
                    class="mt-12 text-center">
                    <template #cell(code)="data">
                        <div style="min-width: 140px;">
                            <span class="text-medium-14">{{ data.value }}</span>
                        </div>
                    </template>
                    <template #cell(guarantee)="data">
                        <div style="min-width: 140px;">
                            <span class="text-medium-14">{{ data.value }}</span>
                        </div>
                    </template>
                    <template #cell(entry)="data">
                        <div style="min-width: 140px;">
                            <span class="text-medium-14">{{ data.value }}</span>
                        </div>
                    </template>
                    <template #cell(no_rm)="data">
                        <div style="min-width: 140px;">
                            <span class="text-medium-14">{{ data.value }}</span>
                        </div>
                    </template>
                    <template #cell(patient_name)="data">
                        <div style="min-width: 140px;">
                            <span class="text-medium-14">{{ data.value }}</span>
                        </div>
                    </template>
                    <template #cell(instalation)="data">
                        <div style="min-width: 140px;">
                            <span class="text-medium-14">{{ data.value }}</span>
                        </div>
                    </template>
                    <template #cell(poli)="data">
                        <div style="min-width: 140px;">
                            <span class="text-medium-14">{{ data.value }}</span>
                        </div>
                    </template>
                    <template #cell(doctor_name)="data">
                        <div style="min-width: 140px;">
                            <span class="text-medium-14">{{ data.value }}</span>
                        </div>
                    </template>
                    <template #cell(invoice_code)="data">
                        <div style="min-width: 140px;">
                            <span class="text-medium-14">{{ data.value }}</span>
                        </div>
                    </template>
                    <template #cell(invoice_date)="data">
                        <div style="min-width: 140px;">
                            <span class="text-medium-14">{{ data.value }}</span>
                        </div>
                    </template>
                    <template #cell(bill_cashier)="data">
                        <div style="min-width: 140px;">
                            <span class="text-medium-14">{{ data.value }}</span>
                        </div>
                    </template>
                    <template #cell(cashier_payment)="data">
                        <div style="min-width: 140px;">
                            <span class="text-medium-14">{{ data.value }}</span>
                        </div>
                    </template>
                    <template #cell(total_bill)="data">
                        <div style="min-width: 140px;">
                            <span class="text-medium-14">{{ data.value }}</span>
                        </div>
                    </template>
                    <template #cell(payment)="data">
                        <div style="min-width: 140px;">
                            <span class="text-medium-14">{{ data.value }}</span>
                        </div>
                    </template>

                </b-table>
                <div class="col px-0 mt-20">
                    <div class="d-flex">
                        <span class="text-regular-14">Menampilkan 1 hingga 10 dari 200 entri</span>
                        <div class="ml-auto">
                            <b-pagination v-model="patient.currentPage" :total-rows="patient.totalRows"
                                :per-page="patient.perPage" :current-page="patient.currentPage" align="fill" size="sm"
                                class="my-0"></b-pagination>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import TopPaymentMenu from '@/@core/components/top-menu/TopPaymentMenu.vue';
import moment from 'moment'
import ExcelJS from 'exceljs'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import { mapState } from 'vuex'

export default {
    name: 'PatientPayment',
    components: {
        TopPaymentMenu,
    },
    data() {
        return {
            showFilter: true,
            hasReport: false,
            params:{
                daily_all:'how_to_pay',
                date_start:moment(new Date()).format('YYYY-MM-DD'),
                time_start: moment(new Date()).format('HH:mm'),
                date_end:moment(new Date()).format('YYYY-MM-DD'),
                time_end: moment(new Date()).format('HH:mm'),
                company_id: null,
            },
            patient: {
                field: [
                    { key: 'code', label: 'Kode Pendaftaran' },
                    { key: 'guarantee', label: 'Jaminan' },
                    { key: 'entry', label: 'Cara Masuk' },
                    { key: 'no_rm', label: 'No RM' },
                    { key: 'patient_name', label: 'Nama Pasien [A/n]' },
                    { key: 'instalation', label: 'Instalasi' },
                    { key: 'poli', label: 'Poli/Ruangan' },
                    { key: 'doctor_name', label: 'Nama Dokter' },
                    { key: 'invoice_code', label: 'No Inovoice' },
                    { key: 'invoice_date', label: 'Tanggal Inovoice' },
                    { key: 'bill_cashier', label: 'Kasir Tagihan' },
                    { key: 'payment cashier', label: 'Kasir Pelunasan' },
                    { key: 'total_bill', label: 'Total Tagihan' },
                    { key: 'payment', label: 'Pelunasan' },
                ],
                item: [
                    {
                        code: '-',
                        guarantee: '-',
                        entry: '-',
                        no_rm: '-',
                        patient_name: '-',
                        instalation: '-',
                        poli: '-',
                        doctor_name: '',
                        invoice_code: '-',
                        invoice_date: '-',
                        bill_cashier: '-',
                        payment_cashier: '-',
                        total_bill: '-',
                        payment: '-',
                    },
                    {
                        code: '-',
                        guarantee: '-',
                        entry: '-',
                        no_rm: '-',
                        patient_name: '-',
                        instalation: '-',
                        poli: '-',
                        doctor_name: '',
                        invoice_code: '-',
                        invoice_date: '-',
                        bill_cashier: '-',
                        payment_cashier: '-',
                        total_bill: '-',
                        payment: '-',
                    },
                    {
                        code: '-',
                        guarantee: '-',
                        entry: '-',
                        no_rm: '-',
                        patient_name: '-',
                        instalation: '-',
                        poli: '-',
                        doctor_name: '',
                        invoice_code: '-',
                        invoice_date: '-',
                        bill_cashier: '-',
                        payment_cashier: '-',
                        total_bill: '-',
                        payment: '-',
                    },
                    {
                        code: '-',
                        guarantee: '-',
                        entry: '-',
                        no_rm: '-',
                        patient_name: '-',
                        instalation: '-',
                        poli: '-',
                        doctor_name: '',
                        invoice_code: '-',
                        invoice_date: '-',
                        bill_cashier: '-',
                        payment_cashier: '-',
                        total_bill: '-',
                        payment: '-',
                    },
                    {
                        code: '-',
                        guarantee: '-',
                        entry: '-',
                        no_rm: '-',
                        patient_name: '-',
                        instalation: '-',
                        poli: '-',
                        doctor_name: '',
                        invoice_code: '-',
                        invoice_date: '-',
                        bill_cashier: '-',
                        payment_cashier: '-',
                        total_bill: '-',
                        payment: '-',
                    },
                    {
                        code: '-',
                        guarantee: '-',
                        entry: '-',
                        no_rm: '-',
                        patient_name: '-',
                        instalation: '-',
                        poli: '-',
                        doctor_name: '',
                        invoice_code: '-',
                        invoice_date: '-',
                        bill_cashier: '-',
                        payment_cashier: '-',
                        total_bill: '-',
                        payment: '-',
                    },
                    

                ],
                currentPage: 1,
                totalRows: 1,
                perPage: 5,
            },
            report:{
                field: [
                    { key: 'tanggal', label: 'Tanggal' },
                    { key: 'code', label: 'No. Invoice' },
                    { key: 'norm', label: 'No. RM' },
                    { key: 'nama_pasien', label: 'Nama Pasien' },
                    { key: 'cara_bayar_penjamin', label: 'Penjamin' },
                    { key: 'poli', label: 'Unit' },
                    { key: 'dokter_instalasi', label: 'Keterangan' },
                    { key: 'billing', label: 'Billing' },
                    { key: 'cara_bayar', label: 'Cara Bayar' },
                    { key: 'nominal_bayar', label: 'Nominal Bayar' },
                    { key: 'cashier_name', label: 'Petugas Kasir' },
                ],
            },
            reportData:[],
            groupReportHeader:[],
        }
    },
    computed: {
        ...mapState({ 
            Options3rdCompany: state => state.helper.options.third_party_company,
        }),
    },
    mounted() {
        this.$store.dispatch('helper/getParamThirdPartyCompany', 'third_party_company')
        this.patient.totalRows = this.patient.item.length
    },
    methods: {
        async filterReport () {
            if (!this.params.date_start || !this.params.date_end) {
                this.notification('Warning','CloseIcon','warning','Mohon untuk mengisi Tanggal terlebih dahulu')
                return
            }

            if (!this.params.time_start || !this.params.time_end) {
                this.notification('Warning','CloseIcon','warning','Mohon untuk mengisi Waktu Tanggal terlebih dahulu')
                return
            }

            let params = {
                daily_all: this.params.daily_all,
                date_start: this.params.date_start,
                date_end: this.params.date_end,
            }

            if (this.params.time_start) {
                params.date_start += ` ${this.params.time_start}`
            }

            if (this.params.time_end) {
                params.date_end += ` ${this.params.time_end}`
            }

            if (this.params.company_id) {
                params.company_id = this.params.company_id.value
            }
         
            try {
                const response = await this.$store.dispatch('payment/getReports', params)
                console.log(response, 'responseExport')
                this.reportData = response.data
                this.hasReport = true
                if(this.reportData.length > 0 && Array.isArray(this.reportData)){
                    this.mappingArray(this.reportData)
                }
            } catch (error) {
                console.error('Error fetching report:', error);
                this.notification('Warning','CloseIcon','warning','Gagal memuat laporan. Silakan periksa koneksi atau coba lagi nanti.')
            }
        },
        async filterExport () {
            this.exportToExcel()
            // if (this.params.daily_all === 'how_to_pay') {
            //     window.open(process.env.VUE_APP_ROOT_API+`report/cashier/daily_all?by=${this.params.daily_all}&date_start=${this.params.date_start}&date_end=${this.params.date_end}`)
            // } else if (this.params.daily_all === 'doctor') {
            //     window.open(process.env.VUE_APP_ROOT_API+`report/cashier/daily_all?by=${this.params.daily_all}&date_start=${this.params.date_start}&date_end=${this.params.date_end}`)
            // } else if (this.params.daily_all === 'health_facility_item') {
            //     window.open(process.env.VUE_APP_ROOT_API+`report/cashier/daily_all?by=${this.params.daily_all}&date_start=${this.params.date_start}&date_end=${this.params.date_end}`)
            // }
        },
        mappingArray(reportData) {
            if (reportData && Array.isArray(reportData)) {
                const newReportData = reportData.map(item => (
                    {
                        ...item,
                        dokter_instalasi: `${item.nama_dokter}`,
                        cara_bayar_penjamin: item.insurance_name
                            ? `${item.how_to_pay} - ${item.insurance_name}`
                            : item.how_to_pay
                    
                    }
                ))
                
                this.reportData = newReportData
                
                for (let index = 0; index < reportData.length; index++) {
                    const element = reportData[index];

                    this.groupReportHeader.push({cara_bayar:element.cara_bayar})
                }
                
                const groupReportHeader = this.groupReportHeader.filter((item, index, self) =>
                    index === self.findIndex(t => JSON.stringify(t) === JSON.stringify(item))
                );
                this.groupReportHeader = groupReportHeader
                this.groupReportHeader.sort((a, b) => new Date(a.value) - new Date(b.value))
            }
        },
        async exportToExcel() {
            const workbook = new ExcelJS.Workbook();
            const worksheet = workbook.addWorksheet("Report Cashier");
            const date_range = this.params.date_start == this.params.date_end ? this.params.date_start : `${this.params.date_start}_${this.params.date_end}`

            // ----------- DEFINE COLUMNS ----------
            worksheet.columns = [
                { key: 'tanggal', width: 15 }, 
                { key: 'code', width: 40 },
                { key: 'norm', width: 5 },  
                { key: 'nama_pasien', width: 5 },     
                { key: 'cara_bayar_penjamin', width: 12 },
                { key: 'dokter_instalasi', width: 8 },
                { key: 'billing', width: 15 },
                { key: 'cara_bayar', width: 5 },      
                { key: 'nominal_bayar', width: 12 },
                { key: 'cashier_name', width: 5 },     
                { key: 'poli', width: 18 },                     
            ];

            const headerRow = worksheet.addRow([
                'Tanggal',
                'No. Invoice',
                'No. RM',
                'Nama Pasien',
                'Penjamin',
                'Unit',
                'Keterangan',
                'Billing',
                'Cara Bayar',
                'Nominal Bayar',
                'Petugas Kasir',
            ]);

            // Style headers
            headerRow.eachCell((cell) => {
                cell.font = { bold: true };
                cell.alignment = { horizontal: 'center', vertical: 'middle' };
                cell.fill = {
                    type: 'pattern',
                    pattern: 'solid',
                    fgColor: { argb: 'FFFF00' }, // light green
                };
                cell.border = {
                    top: { style: 'thin' },
                    left: { style: 'thin' },
                    bottom: { style: 'thin' },
                    right: { style: 'thin' },
                };
            });

            // ----------- HELPER: Apply border to every row -----------
            const applyBorderToRow = (row) => {
                row.eachCell((cell) => {
                    cell.border = {
                        top: { style: 'thin' },
                        left: { style: 'thin' },
                        bottom: { style: 'thin' },
                        right: { style: 'thin' }
                    };
                });
            };

            if (this.groupReportHeader.length > 0) {
                this.groupReportHeader.forEach(typec => {
                    let sectionRow = worksheet.addRow([]);
                    worksheet.mergeCells(`A${sectionRow.number}:J${sectionRow.number}`);
                    worksheet.getCell(`A${sectionRow.number}`).value = typec.cara_bayar;
                    worksheet.getCell(`A${sectionRow.number}`).font = { bold: true };
                    worksheet.getCell(`A${sectionRow.number}`).alignment = { horizontal: 'left' }
                    
                    applyBorderToRow(sectionRow)

                    this.reportData.filter(item=> item.cara_bayar == typec.cara_bayar).forEach(item => {
                        let row = worksheet.addRow([
                            item.tanggal,
                            item.code,
                            item.norm,
                            item.nama_pasien,
                            item.cara_bayar_penjamin,
                            item.poli,
                            item.dokter_instalasi,
                            item.billing,
                            item.cara_bayar,
                            item.nominal_bayar,
                            item.cashier_name,
                        ]);
                        applyBorderToRow(row);
                    });
                });
            }

            // ----------- DOWNLOAD ----------
            const buffer = await workbook.xlsx.writeBuffer();
            const blob = new Blob([buffer], { 
                type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' 
            });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = `Report_Cashier_${date_range}.xlsx`;
            link.click();
        },
        patientFiltered(patientItems) {
            this.patient.totalRows = patientItems.length
            this.patient.currentPage = 1
        },
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                employee: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },
    },
}
</script>
<style scoped>
/* @import '../../../@core/css/main/main.min.css'; */
.card-header:hover {
    cursor: pointer;
}
</style>
