<template>
    <div>
        <Layout
            :layout_breadcrumb_items="[
                { text: 'Home', link: '/' },
                { text: 'Gizi' },
            ]"
        >
            <!-- <TopNutritionMenu selectedMenu="menu1" /> -->
            <section class="card">
                <div class="card-header font-weight-bold bg-primary-200">Filter Data</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-3">
                            <label class="fs-13 mb-0">Nama Pasien</label>
                            <b-form-input v-model="params.patient_name"/>
                        </div>
                        <div class="col-lg-3 mt-10 mt-lg-0">
                            <label class="fs-13 mb-0">No RM</label>
                            <b-form-input v-model="params.norm"/>
                        </div>
                        <div class="col-lg-3 mt-10 mt-lg-0">
                            <label class="fs-13 mb-0">Diet</label>
                            <v-select v-model="params.diet_id" :options="optionsDiet" :reduce="item => item.value" label="title" placeholder="-- Diet --" />
                        </div>
                        <div class="col-lg-3 mt-10 mt-lg-0">
                            <label class="fs-13 mb-0">Waktu</label>
                            <v-select v-model="params.time_type_id" :options="optionsTime" :reduce="item => item.value" label="title" placeholder="-- pilih --" />
                        </div>
                        <div class="col-lg-3 mt-10">
                            <label class="fs-13 mb-0">Tanggal mulai</label>
                            <b-form-input type="date" v-model="params.date_start" />
                        </div>
                        <div class="col-lg-3 mt-10">
                            <label class="fs-13 mb-0">Tanggal akhir</label>
                            <b-form-input type="date" v-model="params.date_end" />
                        </div>
                        <div class="col-lg-3 mt-10">
                            <label class="fs-13 mb-0">Bangsal</label>
                            <v-select v-model="params.type_room_id" :options="TypeRoomOptions" :reduce="(TypeRoomOptions) => TypeRoomOptions.value" label="title" placeholder="pilih bangsal"></v-select>
                        </div>
                        <div class="col-lg-3 mt-10">
                            <label class="fs-13 mb-0">Ruangan</label>
                            <v-select v-model="params.room_id" :options="OptionsRoom" :reduce="(OptionsRoom) => OptionsRoom.value" label="title" placeholder="pilih ruangan"></v-select>
                        </div>
                    </div>
                    <div class="d-flex mt-40">
                        <b-button @click="getNutritionList" variant="primary" :disabled="isDisabledFilter" class="mr-2">
                            <feather-icon icon="SearchIcon" /> Filter
                        </b-button>
                        <b-button @click="filterReset" variant="outline-primary" :disabled="isDisabledFilter" class="mr-2">
                            <feather-icon icon="XIcon" /> Clear Filter
                        </b-button>
                        <b-button @click="printAllEticket" :disabled="disabled_print_all" variant="outline-w3green" class="mr-2">
                            <feather-icon icon="PrinterIcon" /> Cetak Semua
                        </b-button>
                        <b-button @click="downloadReports" :disabled="disabled_export" variant="outline-w3green" class="mr-2">
                            <feather-icon icon="DownloadIcon" /> Download Laporan
                        </b-button>
                    </div>
                </div>
            </section>
            <section class="mt-24">
                <div class="d-flex justify-content-between align-items-center">
                    <b-form-group label-cols="6" label-align="left" label-size="sm" label-for="sortBySelect" class="text-nowrap mb-md-0">
                        <span class="text-regular-14">Tampilkan</span>
                        <b-form-select v-model="perPage" size="sm" :options="pageOptions" class="mx-1"/>
                        <span class="text-regular-14">Entri</span>
                    </b-form-group>
                    <div>
                        <b-button
                            @click="approveSelectedNutrition"
                            :disabled="selectedIds.length === 0 || bulkApproveRunning"
                            variant="primary"
                            size="sm"
                        >
                            <feather-icon icon="CheckIcon" class="mr-1" /> Konfirmasi Terpilih ({{ selectedIds.length }})
                        </b-button>
                    </div>
                </div>
                <b-table bordered fixed small head-variant="light" :items="nutrition_list" :fields="nutrition_fields" thead-tr-class="fs-13" tbody-tr-class="fs-12" class="mt-12 text-center">
                    <template #head(select)>
                        <b-form-checkbox
                            v-model="isAllSelected"
                            :disabled="selectableItems.length === 0"
                        />
                    </template>
                    <template #cell(select)="row">
                        <b-form-checkbox
                            v-if="row.item.status == 0"
                            :checked="isRowSelected(row.item.id)"
                            @change="toggleRowSelect(row.item)"
                        />
                    </template>
                    <template #cell(action)="row">
                        <div class="d-flex">
                            <div>
                                <b-button @click="printOneEticket(row.item)" :disabled="disabled_one_print" size="sm" variant="w3green" class="mr-1">
                                    <feather-icon icon="PrinterIcon" />
                                </b-button>
                            </div>
                            <template v-if="row.item.status == 0">
                                <div>
                                    <b-button @click="approveNutrition(row.item)" size="sm" variant="orange-500" class="mr-1">
                                        <feather-icon icon="CheckIcon" />
                                    </b-button>
                                </div>
                                <div>
                                    <b-button @click="onEditNutrition(row.item)" size="sm" variant="warning-500" class="mr-1">
                                        <feather-icon icon="EditIcon" />
                                    </b-button>
                                </div>
                                <div>
                                    <b-button @click="removeNutrition(row.item)" size="sm" variant="danger-500" class="mr-1">
                                        <feather-icon icon="TrashIcon" />
                                    </b-button>
                                </div>
                            </template>
                        </div>
                    </template>
                    <template #cell(status)="row">{{ row.value == 1 ? 'TELAH DIKIRIM' : '-' }}</template>
                    <template #cell(patient)="row">
                        <div class="text-left">
                            <div class="font-weight-bold">{{ row.item.norm }} | {{ row.item.alias_name }}. {{ row.item.patient_name }}</div>
                            <div>{{ localDate(row.item.birth_date) }} ({{ parsedAge(row.item.birth_date) }}) | {{ row.item.room_name }}</div>
                        </div>
                    </template>
                    <template #cell(diet)="row">
                        <div>{{ localDate(row.item.date) }}, {{ getTimeName(row.item.time_type_id) }}</div>
                        <div>{{ row.item.diet_name }}</div>
                    </template>
                    <template #cell(employee_approved_name)="row">{{ row.value ? row.value : '-' }}</template>
                    <template #cell(icd10)="row">
                        <div v-if="row.value.length > 0">
                            {{ truncatedText(`${row.value[0].icd10_code} ${row.value[0].icd10_name}`, 50)}}
                            <feather-icon :id="`pop-trgt-${row.item.id}`" icon="InfoIcon" size="12" color="grey" class="pointer" />
                            <b-popover :target="`pop-trgt-${row.item.id}`" triggers="hover" placement="right">
                                <template #title>
                                    <div class="fs-10 text-uppercase font-weight-bold">{{ row.item.alias_name }} {{ row.item.patient_name }}</div>
                                </template>
                                <div class="fs-11">
                                    <li v-for="unit in row.value">{{ unit.icd10_code }} {{ unit.icd10_name }}</li>
                                </div>
                            </b-popover>
                        </div>
                        <div v-else>-</div>
                    </template>
                </b-table>

                <div class="col px-0 mt-20">
                    <div class="d-flex">
                        <span class="text-regular-14">Menampilkan {{ firstRows }} hingga <template v-if="lastRows < totalRows">{{ lastRows }}</template> <template v-else>{{ totalRows }}</template> dari {{ totalRows }} entri</span>
                        <div class="ml-auto">
                            <b-pagination
                                v-model="currentPage"
                                :total-rows="totalRows"
                                :per-page="perPage"
                                align="fill" size="sm" class="my-0">
                            </b-pagination>
                        </div>
                    </div>
                </div>
            </section>

            <b-modal v-model="active.modal_edit" title="Edit Order" no-close-on-backdrop>
                <div class="row">
                    <div class="col-lg-4">
                        <label class="c-form-label">Tanggal</label>
                        <b-form-input type="date" v-model="form_nutrition.date" />
                    </div>
                    <div class="col-lg-4">
                        <label class="c-form-label">Waktu</label>
                        <v-select v-model="form_nutrition.time_type_id" :options="optionsTime" :reduce="item => item.value" label="title" placeholder="-- pilih --" />
                    </div>
                    <div class="col-lg-4">
                        <label class="c-form-label">Diet</label>
                        <v-select v-model="form_nutrition.diet_id" :options="optionsDiet" :reduce="item => item.value" label="title" placeholder="-- pilih --" />
                    </div>
                    <div class="col-12 mt-10">
                        <label class="c-form-label">Catatan</label>
                        <b-form-textarea rows="4" v-model="form_nutrition.notes" autocomplete="off" />
                    </div>
                </div>

                <template #modal-footer>
                    <div class="d-flex justify-content-end w-100">
                        <b-button @click="validationForm" :disabled="active.disabled_main" variant="primary-500">Simpan</b-button>
                    </div>
                </template>
            </b-modal>
        </Layout>
    </div>
</template>

<script>
// import TopNutritionMenu from '@/@core/components/top-menu/TopNutritionMenu.vue';
import dateMixin from '@/mixins/dateMixin';
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue';
import axios from 'axios';
import ExcelJS from 'exceljs';
import moment from 'moment';
import { mapState } from 'vuex';

export default {
    mixins: [dateMixin],
    data() {
        return {
            showFilter: false,
            nutrition_list: [],
            disabled_one_print: false,
            disabled_print_all: false,
            disabled_export: false,

            // --- fitur select all / konfirmasi terpilih ---
            selectedIds: [],
            bulkApproveRunning: false,

            pageOptions: [20, 50, 100],
            currentPage:1,
            perPage: 20,
            firstRows: 1,
            lastRows: 20,
            isDisabledFilter: false,
            totalRows: 0,
            params: {
                offset: 0,
                limit: 20,
                patient_name: '',
                norm: '',
                diet_id: '',
                time_type_id: '',
                type_room_id: '',
                room_id: '',
                date_start: moment(new Date()).format('YYYY-MM-DD'),
                date_end: moment(new Date()).format('YYYY-MM-DD'),
            },

            active: {
                modal_edit: false,
                disabled_main: false,
            },

            nutrition_fields: [
                { key: 'select', label: '', thStyle: {width: '40px'} },
                { key: 'action', label: 'Aksi', thStyle: {width: '150px'} },
                { key: 'status', label: 'Status', thStyle: { width: '70px' } },
                { key: 'patient', label: 'Identitas Pasien', thStyle: { width: '300px' } },
                { key: 'diet', label: 'Diet', thStyle: {width: '150px'} },
                { key: 'notes', label: 'Catatan' },
                { key: 'icd10', label: 'Diagnosa' },
                { key: 'employee_nurse_name', label: 'Diorder oleh' },
                // { key: 'room_name', label: 'Ruangan' },
                // { key: 'date', label: 'Tanggal Order' },
                // { key: 'employee_approved_name', label: 'Disetujui oleh' },
            ],

            optionsTime: [
                { value : 1, title: 'Pagi' },
                { value : 2, title: 'Siang' },
                { value : 3, title: 'Sore' },
            ],
            // selected: []
            form_nutrition: {
                id: '',
                registration_destination_id: '',
                health_facility_item_id: '',
                employee_nurse_id: '',
                room_id: '',
                diet_id: '',
                date: moment(new Date()).format('YYYY-MM-DD'),
                delivery_time: '',
                time_arr: [],
                time_type_id: 0,
                notes: '',
                status: 0,
            },
        }
    },

    computed: {
        ...mapState({
            optionsDiet: state => state.nutrition.optionsDiet,
            TypeRoomOptions: state => state.helper.options.type_room,
            OptionsRoom: state => state.room.options.room,
        }),

        // Baris yang boleh dipilih untuk bulk-approve: hanya yang statusnya
        // masih 0 (belum dikonfirmasi). Baris yang sudah "TELAH DIKIRIM"
        // tidak ikut dihitung / tidak bisa dicentang.
        selectableItems() {
            return this.nutrition_list.filter(item => item.status == 0)
        },

        // Checkbox "select all" di header tabel.
        isAllSelected: {
            get() {
                return this.selectableItems.length > 0
                    && this.selectedIds.length === this.selectableItems.length
            },
            set(value) {
                if (value) {
                    this.selectedIds = this.selectableItems.map(item => item.id)
                } else {
                    this.selectedIds = []
                }
            }
        },
    },

    watch: {
        currentPage: {
            handler: function(value) {
                this.firstRows = this.perPage*(value-1)+1
                this.lastRows = this.perPage*(value)+1
                this.params.offset = this.perPage*(value-1)
                this.params.limit = this.perPage
                this.getNutritionList()
            }
        },
        perPage: {
            handler: function(value) {
                this.params.limit = this.perPage
                this.getNutritionList()
            }
        },
        'params.type_room_id'(value) {
            this.params.room_id = ''
            if (value) {
                return this.$store.dispatch('room/getParamRoom', value)
            } else {
                this.$store.dispatch('room/getParamRoom', '')
            }
        },
    },

    methods: {
        truncatedText(textData, maxChar) {
            const safeText = textData != null ? String(textData) : ""
            let result = safeText.length > maxChar ? safeText.slice(0, maxChar)+'...' : safeText
            return result
        },

        // --- helper untuk checkbox per-baris ---
        isRowSelected(id) {
            return this.selectedIds.includes(id)
        },
        toggleRowSelect(item) {
            const idx = this.selectedIds.indexOf(item.id)
            if (idx === -1) {
                this.selectedIds.push(item.id)
            } else {
                this.selectedIds.splice(idx, 1)
            }
        },

        // --- bulk approve untuk semua baris yang dicentang ---
        approveSelectedNutrition() {
            const selectedItems = this.nutrition_list.filter(item => this.selectedIds.includes(item.id))

            if (selectedItems.length === 0) {
                return
            }

            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin konfirmasi ${selectedItems.length} order terpilih?`, {
                    title: 'Konfirmasi', size: 'sm', okVariant: 'primary', okTitle: 'Ya, Konfirmasi', cancelTitle: 'Batal', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
                })
                .then(value => {
                    if (value === true) {
                        this.bulkApproveRunning = true

                        const requests = selectedItems.map(obj => {
                            const formData = {
                                id: obj?.id,
                                registration_destination_id: obj?.registration_destination_id,
                                health_facility_item_id: obj?.health_facility_item_id,
                                employee_nurse_id: obj?.employee_nurse_id,
                                room_id: obj?.room_id,
                                diet_id: obj?.diet_id,
                                date: obj?.date,
                                time_type_id: obj?.time_type_id,
                                delivery_time: moment(new Date()).format('HH:mm'),
                                notes: obj?.notes,
                                status: 1
                            }
                            return this.$store.dispatch('nutrition/postPutNutrition', formData)
                        })

                        Promise.all(requests)
                            .then(() => {
                                this.notification('Success', 'CheckIcon', 'success', 'You have successfully')
                                this.selectedIds = []
                                this.getNutritionList()
                            })
                            .catch((error) => {
                                this.notification('Failed', 'CloseIcon', 'danger', 'You have unsuccessful')
                                console.error('Error while bulk approve nutrition at: ', error)
                            })
                            .finally(() => {
                                this.bulkApproveRunning = false
                            })
                    }
                })
        },

        async downloadReports() {
            try {
                this.disabled_export = true
                const { offset, limit, ...rest } = this.params
                let temp_param = {...rest}

                await this.$store.dispatch('nutrition/getListNutrition', temp_param)
                .then(async response => {
                    // console.log(response.rows, 'response')
                    let merged = []
                    if(response?.rows.length > 0){
                        let patientsMap = {}

                        for (let i = 0; i < response.rows.length; i++) {
                            const item = response.rows[i]

                            const key = `${item.registration_destination_id}_${item.date}`

                            if (!patientsMap[key]) {
                                // clone patient object tanpa diet_name/time_type_id
                                const newPatient = { ...item, times: [] }
                                delete newPatient.time_type_id
                                delete newPatient.diet_name

                                patientsMap[key] = newPatient
                                merged.push(patientsMap[key])
                            }

                            let times = patientsMap[key].times

                            // cek apakah sudah ada entry dengan time_type_id yg sama
                            const existingIndex = times.findIndex(t => t.time_type_id === item.time_type_id)

                            if (existingIndex === -1) {
                                // belum ada, langsung push
                                times.push({
                                    id: item.id,
                                    time_type_id: item.time_type_id,
                                    diet_name: item.diet_name,
                                    date_updated: item.date_updated
                                })
                            } else {
                                // sudah ada bandingkan date_updated
                                if (new Date(item.date_updated) > new Date(times[existingIndex].date_updated)) {
                                    // replace dengan data yang lebih baru
                                    times[existingIndex] = {
                                        id: item.id,
                                        time_type_id: item.time_type_id,
                                        diet_name: item.diet_name,
                                        date_updated: item.date_updated
                                    }
                                }
                            }
                        }
                    }

                    await this.exportToExcel(merged || [], temp_param)
                }, error => {
                    console.error('Error while GET nutrition/getListNutrition at: ', error);
                })
            } catch (error) {
                console.error('error at: ', error);
            } finally {
                this.disabled_export = false
            }
        },

        async exportToExcel(arrayData, filterData) {
            const workbook = new ExcelJS.Workbook();
            const worksheet = workbook.addWorksheet("Laporan Diet");
            const date_range = filterData.date_start == filterData.date_end ? filterData.date_start : `${filterData.date_start} sampai ${filterData.date_end}`

            worksheet.mergeCells('A1:K1')
            const titleCell = worksheet.getCell('A1')
            titleCell.value = "DAFTAR DIET MAKAN PASIEN RAWAT INAP"

            worksheet.mergeCells('A2:K2')
            const subTitleCell = worksheet.getCell('A2')
            subTitleCell.value = "RUMAH SAKIT Taman Harapan Baru"

            worksheet.mergeCells('A3:K3')
            const periodCell = worksheet.getCell('A3')
            periodCell.value = date_range

            const cells = [titleCell, subTitleCell, periodCell];
            cells.forEach((cell) => {
                cell.font = { bold: true };
                cell.alignment = { horizontal: 'center' };
            });

            worksheet.mergeCells('A5:A6'); worksheet.getCell('A5').value = "TANGGAL";
            worksheet.mergeCells('B5:B6'); worksheet.getCell('B5').value = "NO";
            worksheet.mergeCells('C5:C6'); worksheet.getCell('C5').value = "NAMA";
            worksheet.mergeCells('D5:D6'); worksheet.getCell('D5').value = "TGL LAHIR";
            worksheet.mergeCells('E5:E6'); worksheet.getCell('E5').value = "USIA";
            worksheet.mergeCells('F5:F6'); worksheet.getCell('F5').value = "DIAGNOSA";

            worksheet.mergeCells('G5:I5'); worksheet.getCell('G5').value = "DIET PASIEN";
            worksheet.getCell('G6').value = "PAGI";
            worksheet.getCell('H6').value = "SIANG";
            worksheet.getCell('I6').value = "SORE";

            worksheet.mergeCells('J5:J6'); worksheet.getCell('J5').value = "RUANGAN";
            worksheet.mergeCells('K5:K6'); worksheet.getCell('K5').value = "KETERANGAN";

            // Style header
            for (let row = 5; row <= 6; row++) {
                for (let col = 1; col <= 11; col++) {
                    const cell = worksheet.getCell(row, col);
                    cell.font = { bold: true };
                    cell.alignment = { vertical: 'middle', horizontal: 'center', wrapText: true };
                    cell.border = {
                        top: { style: 'thin' },
                        left: { style: 'thin' },
                        bottom: { style: 'thin' },
                        right: { style: 'thin' }
                    };
                }
            }

            // const headers = [
            //     "NO",
            //     "ALIAS",
            //     "NAMA PASIEN",
            //     "TGL LAHIR",
            //     "USIA",
            //     "TGL DIET",
            //     "WAKTU DIET",
            //     "DIET",
            //     "RUANGAN",
            //     "DIAGNOSA",
            //     "KETERANGAN"
            // ]

            // // Masukin header dari kolom A sampe K
            // headers.forEach((header, index) => {
            //     const cell = worksheet.getCell(5, index + 1); // Baris 5, Kolom A-K
            //     cell.value = header;
            //     cell.font = { bold: true };
            //     cell.border = {
            //         top: { style: 'thin' },
            //         left: { style: 'thin' },
            //         bottom: { style: 'thin' },
            //         right: { style: 'thin' }
            //     };
            // });

            // Ngatur lebar kolom
            worksheet.columns = [
                { width: 15 }, // A Tanggal
                { width: 5 },  // B No
                { width: 35 }, // C Nama
                { width: 15 }, // D Tgl Lahir
                { width: 18 },  // E Usia
                { width: 30 }, // F Diagnosa
                { width: 25 }, // G Pagi
                { width: 25 }, // H Siang
                { width: 25 }, // I Sore
                { width: 15 }, // J Ruangan
                { width: 30 }, // K Keterangan
            ];

            // Isi data ke worksheet
            arrayData.forEach((item, index) => {
                const row = 7 + index; // Mulai dari baris 7
                // Mapping untuk diet
                const dietMap = {
                    1: "", // pagi
                    2: "", // siang
                    3: ""  // sore
                };
                if (item.times && Array.isArray(item.times)) {
                    item.times.forEach(d => {
                        dietMap[d.time_type_id] = d.diet_name;
                    });
                }
                worksheet.getCell(`A${row}`).value = item.date
                worksheet.getCell(`B${row}`).value = index + 1
                worksheet.getCell(`C${row}`).value = `${item.alias_name} ${item.patient_name}`
                worksheet.getCell(`D${row}`).value = item.birth_date
                worksheet.getCell(`E${row}`).value = this.parsedAge(item.birth_date)
                worksheet.getCell(`F${row}`).value = this.getStringICD(item.icd10)
                worksheet.getCell(`G${row}`).value = dietMap[1]
                worksheet.getCell(`H${row}`).value = dietMap[2]
                worksheet.getCell(`I${row}`).value = dietMap[3]
                worksheet.getCell(`J${row}`).value = item.room_name
                worksheet.getCell(`K${row}`).value = item.notes
            });

            // Nambahin border ke seluruh cell yang diperluin
            for (let row = 0; row <= arrayData.length; row++) {
                for (let col = 1; col <= 11; col++) { // 1-11 malsudnya kolom A-K
                    const cell = worksheet.getCell(row+6, col); //row+6 karena mulainya dari row 6
                    // cell.alignment = { wrapText: true };
                    if(col == 1 || col == 2 || col == 4){
                        cell.alignment = { vertical: 'middle', horizontal: 'center', wrapText: true };
                    }
                    cell.border = {
                        top: { style: 'thin' },
                        left: { style: 'thin' },
                        bottom: { style: 'thin' },
                        right: { style: 'thin' }
                    };
                }
            }

            // Download file
            const buffer = await workbook.xlsx.writeBuffer();
            const blob = new Blob([buffer], {
                type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = `Laporan_diet_${date_range}.xlsx`;
            link.click();
        },

        getStringICD(arrayICD) {
            let temp = []
            for (let i = 0; i < arrayICD.length; i++) {
                const el = arrayICD[i];
                temp.push(`${el.icd10_code} ${el.icd10_name}`)
            }
            return temp.join('\n')
        },

        onEditNutrition(obj) {
            this.resetForm()
            this.active.modal_edit = true

            this.form_nutrition.id = obj?.id
            this.form_nutrition.registration_destination_id = obj?.registration_destination_id
            this.form_nutrition.health_facility_item_id = obj?.health_facility_item_id
            this.form_nutrition.employee_nurse_id = obj?.employee_nurse_id
            this.form_nutrition.room_id = obj?.room_id
            this.form_nutrition.diet_id = obj?.diet_id
            this.form_nutrition.date = obj?.date
            this.form_nutrition.time_type_id = obj?.time_type_id
            this.form_nutrition.notes = obj?.notes
            this.form_nutrition.status = obj?.status
            this.form_nutrition.delivery_time = obj?.delivery_time || ''
        },

        resetForm() {
            this.form_nutrition.id = ''
            this.form_nutrition.registration_destination_id = ''
            this.form_nutrition.health_facility_item_id = ''
            this.form_nutrition.employee_nurse_id = ''
            this.form_nutrition.room_id = ''
            this.form_nutrition.diet_id = ''
            this.form_nutrition.date = moment(new Date()).format('YYYY-MM-DD')
            this.form_nutrition.time_type_id = ''
            this.form_nutrition.time_arr = []
            this.form_nutrition.notes = ''
            this.form_nutrition.status = 0
            this.form_nutrition.delivery_time = ''
        },

        validationForm() {
            try {
                this.active.disabled_main = true
                this.$store.dispatch('nutrition/postPutNutrition', this.form_nutrition)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.active.modal_edit = false
                        this.active.modal_edit = false
                        this.resetForm()
                        this.getNutritionList()
                    }
                )
                .catch(({error, response}) => {
                    this.notification('Failed','CloseIcon','danger',`${error}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)
            } finally{
                this.active.disabled_main = false
            }
        },

        removeNutrition(obj) {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus ${obj.patient_name} (${obj.room_name}) tanggal ${obj.date} ${this.getTimeName(obj.time_type_id)}?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger', okTitle: 'Ya, Hapus', cancelTitle: 'Batal', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    this.$store.dispatch('nutrition/deleteNutrition', obj.id)
                    .then(
                        response => {
                            if (response.status === 200 || response.status === 201) {
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.getNutritionList()
                            } else {
                                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                            }
                        },
                        error => {
                            this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                            console.error('Error While Delete Using nutrition/deleteNutritionl at: ', error)
                        }
                    )
                    .catch(({error, response}) => {
                        this.notification('Failed','CloseIcon','danger',`${error}`)
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
        getNutritionList() {
            this.isDisabledFilter = true
            // reset seleksi setiap kali data di-refresh (filter/ganti halaman/dsb)
            this.selectedIds = []
            try {
                this.$store.dispatch('nutrition/getListNutrition', this.params).then((response) => {
                    this.nutrition_list = response.rows
                    this.totalRows = response.iTotal
                }, error => {
                    console.error('Error while GET nutrition/getListNutrition at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET nutrition/getListNutrition at: ', error);
            } finally {
                this.isDisabledFilter = false
            }
        },

        filterReset () {
            this.params.offset = 0
            this.params.limit = this.perPage
            this.params.patient_name = ''
            this.params.norm = ''
            this.params.date_start = moment(new Date()).format('YYYY-MM-DD')
            this.params.date_end = moment(new Date()).format('YYYY-MM-DD')
            this.params.diet_id = ''
            this.params.type_room_id = ''
            this.params.room_id = ''
            this.params.time_type_id = ''
            this.getNutritionList()
        },

        approveNutrition(obj) {
            let formData = {
                id: obj?.id,
                registration_destination_id: obj?.registration_destination_id,
                health_facility_item_id: obj?.health_facility_item_id,
                employee_nurse_id: obj?.employee_nurse_id,
                room_id: obj?.room_id,
                diet_id: obj?.diet_id,
                date: obj?.date,
                time_type_id: obj?.time_type_id,
                delivery_time: moment(new Date()).format('HH:mm'),
                notes: obj?.notes,
                time_type_id: obj?.time_type_id,
                status: 1
            }
            this.$bvModal
            .msgBoxConfirm(`Apakah anda ingin konfirmasi ${obj.patient_name} (${obj.room_name}) tanggal ${obj.date} ${this.getTimeName(obj.time_type_id)}?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'primary', okTitle: 'Ya, Konfirmasi', cancelTitle: 'Batal', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    try {
                        this.$store.dispatch('nutrition/postPutNutrition', formData)
                        .then(
                            res => {
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.getNutritionList()
                            }
                        )
                        .catch(({error, response}) => {
                            this.notification('Failed','CloseIcon','danger',`${error}`)
                        })
                    } catch (error) {
                        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                        console.error('Error catch at: ', error)
                    }
                }
            })
        },

        getTimeName(time_id) {
            if (time_id == 1) return 'Pagi'
            if (time_id == 2) return 'Siang'
            if (time_id == 3) return 'Sore'
            return '-'
        },

        printOneEticket(obj) {
            this.disabled_one_print = true

            let temp_arry = []
            let print_norm = obj?.norm || '-'
            let print_name = `${obj?.alias_name || '-'} ${this.truncatedText(obj?.patient_name || '-', 22)}`
            let print_birthdate = obj?.birth_date || '-'
            let print_diet = obj?.diet_name || '-'
            let print_time = this.getTimeName(obj?.time_type_id).toUpperCase() || '-'
            let print_room = obj?.room_name || '-'
            let print_stamp = moment(new Date()).format('YYYY-MM-DD')
            // let print_hfi = obj?.health_facility_item_name || '-'
            // let print_date = obj?.date || '-'

            const version = `
                ^XA

                ^CF0,20
                ^FO120,15^FDUNIT GIZI RS Taman Harapan Baru Bekasi^FS

                ^CF0,16
                ^FO107,40^FDJl. Kaliabang Tengah No. 2 RT.004 RW.023, Bekasi, Kota Bekasi^FS
                ^FO0,65^GB1300,3,2^FS

                ^CF0,20
                ^FO150,80^FDETICKET MAKAN PASIEN ${print_time}^FS
                ^CF0,16
                ^FO230,102^FD${print_stamp}^FS

                ^CF0,20
                ^FO10,137^FDNo RM^FS
                ^FO140,137^FD:^FS
                ^FO155,137^FD${print_norm}^FS

                ^CF0,20
                ^FO10,164^FDNama^FS
                ^FO140,164^FD:^FS
                ^FO155,164^FD${print_name}^FS

                ^CF0,20
                ^FO10,191^FDTanggal Lahir^FS
                ^FO140,191^FD:^FS
                ^FO155,191^FD${print_birthdate}^FS

                ^CF0,20
                ^FO10,218^FDRuangan^FS
                ^FO140,218^FD:^FS
                ^FO155,218^FD${print_room}^FS

                ^CF0,20
                ^FO10,245^FDDiet^FS
                ^FO140,245^FD:^FS
                ^FO155,245^FD${print_diet}^FS

                ^CF0,19
                ^FO450,137^FD[  ] Ahli Gizi^FS
                ^FO450,164^FD[  ] Perawat^FS

                ^CF0,17
                ^FO90,290^FDSebaiknya dikonsumsi dalam 1 jam setelah pemberian^FS

                ^XZ
            `

            temp_arry.push(version)

            const headers = {
                "Accept": "application/pdf",
                "Content-Type": "application/x-www-form-urlencoded",
            }
            axios.post("http://api.labelary.com/v1/printers/8dpmm/labels/2.7559069999999997x1.5748039999999999/", temp_arry, {
                responseType: "arraybuffer",
                headers,
            })
            .then((res) => {
                const blob = new Blob([res.data], { type: "application/pdf" });
                const url = window.URL.createObjectURL(blob);

                window.open(url, "_blank");
                this.disabled_one_print = false
            })
            .catch((error) => {
                console.error("Error:", error);
                this.disabled_one_print = false
            });
        },

        printAllEticket() {
            this.disabled_print_all = true
            let temp_arry = []

            for (let i = 0; i < this.nutrition_list.length; i++) {
                const el = this.nutrition_list[i];

                let print_norm = el?.norm || '-'
                let print_name = `${el?.alias_name || '-'} ${this.truncatedText(el?.patient_name || '-', 22)}`
                let print_birthdate = el?.birth_date || '-'
                let print_diet = el?.diet_name || '-'
                let print_time = this.getTimeName(el?.time_type_id).toUpperCase() || '-'
                let print_room = el?.room_name || '-'
                let print_stamp = moment(new Date()).format('YYYY-MM-DD')
                // let print_hfi = el?.health_facility_item_name || '-'
                // let print_date = el?.date || '-'

                const version = `
                    ^XA

                    ^CF0,20
                    ^FO120,15^FDUNIT GIZI RS Taman Harapan Baru Bekasi^FS

                    ^CF0,16
                    ^FO107,40^FDJl. Kaliabang Tengah No. 2 RT.004 RW.023, Bekasi, Kota Bekasi^FS
                    ^FO0,65^GB1300,3,2^FS

                    ^CF0,20
                    ^FO150,80^FDETICKET MAKAN PASIEN ${print_time}^FS
                    ^CF0,16
                    ^FO230,102^FD${print_stamp}^FS

                    ^CF0,20
                    ^FO10,137^FDNo RM^FS
                    ^FO140,137^FD:^FS
                    ^FO155,137^FD${print_norm}^FS

                    ^CF0,20
                    ^FO10,164^FDNama^FS
                    ^FO140,164^FD:^FS
                    ^FO155,164^FD${print_name}^FS

                    ^CF0,20
                    ^FO10,191^FDTanggal Lahir^FS
                    ^FO140,191^FD:^FS
                    ^FO155,191^FD${print_birthdate}^FS

                    ^CF0,20
                    ^FO10,218^FDRuangan^FS
                    ^FO140,218^FD:^FS
                    ^FO155,218^FD${print_room}^FS

                    ^CF0,20
                    ^FO10,245^FDDiet^FS
                    ^FO140,245^FD:^FS
                    ^FO155,245^FD${print_diet}^FS

                    ^CF0,19
                    ^FO450,137^FD[  ] Ahli Gizi^FS
                    ^FO450,164^FD[  ] Perawat^FS

                    ^CF0,17
                    ^FO90,290^FDSebaiknya dikonsumsi dalam 1 jam setelah pemberian^FS

                    ^XZ
                `;
                temp_arry.push(version)
            }
            const headers = {
                "Accept": "application/pdf",
                "Content-Type": "application/x-www-form-urlencoded",
            }
            axios.post("http://api.labelary.com/v1/printers/8dpmm/labels/2.7559069999999997x1.5748039999999999/", temp_arry, {
                responseType: "arraybuffer",
                headers,
            })
            .then((res) => {
                // console.log(res);
                const blob = new Blob([res.data], { type: "application/pdf" });
                const url = window.URL.createObjectURL(blob);

                window.open(url, "_blank");
                this.disabled_print_all = false
            })
            .catch((error) => {
                console.error("Error:", error);
                this.disabled_print_all = false
            });
        },
    },

    created() {
        this.$store.dispatch('nutrition/getParamDiet')
        this.$store.dispatch('helper/getParam', 'type_room')
        this.getNutritionList()
    },
}
</script>