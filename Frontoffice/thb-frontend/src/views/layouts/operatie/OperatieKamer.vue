<template>
    <div>
        <div class="text-center mb-1 mt-5">
            <h2 class="mb-0">Kalender Operasi</h2>
            <div class="text-muted mb-5" style="font-size: 15px;">
                Berikut adalah kalender operasi sesuai schedule yang telah terkonfirmasi
            </div>
        </div>
        <div class="calendar-header d-flex justify-content-between align-items-center mb-2" style="max-width:1000px; margin:0 auto;">
            <div></div> <!-- Empty div to push legend to the right -->
            <div class="calendar-legend">
                <span style="background:#ff4d4f;display:inline-block;width:16px;height:16px;border-radius:3px;margin-right:4px;"></span> CITO
                <span style="background:#1890ff;display:inline-block;width:16px;height:16px;border-radius:3px;margin:0 4px 0 16px;"></span> ELECTIVE
                <span style="background:#52c41a;display:inline-block;width:16px;height:16px;border-radius:3px;margin:0 4px 0 16px;"></span> ODC
            </div>
        </div>
        <div class="mb-5" style="max-width:1000px; margin: 0 auto;">
            <!-- <EventCalendar :events="calendarEvents" @event-info="onModalInfo" @dates-set="onCalendarDatesSet"/> -->
            <EventCalendar
              ref="eventCalendar"
              :events="calendarEvents"
              @goto-day="onGotoDay"
              @event-info="onModalInfo"
              @dates-set="onCalendarDatesSet"
            />
            <!-- <b-button
                v-if="currentCalendarView === 'resourceTimeGridDay'"
                :to="`/operatie-kamer/print-calendar`"   
                target="_blank"
                size="sm" variant="w3green">
                    <b-icon icon="printer" class="mr-1" /> Print Kalender Operasi
            </b-button> -->
            <b-button
                v-if="currentCalendarView === 'resourceTimeGridDay'"
                @click="openPrintPage"
                size="sm"
                variant="w3green">
                <b-icon icon="printer" class="mr-1" /> Print Kalender Operasi
            </b-button>
        </div>
        <div class="card">
            <div class="row">
                <div class="card-body mb-5">
                    <div v-if="currentCalendarView === 'resourceTimeGridDay'" class="table-responsive mt-5">
                        <table class="table table-bordered table-sm">
                            <thead class="thead-light">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th style="width:140px">JAM MULAI</th>
                                    <th class="text-center">NORM</th>
                                    <th class="text-center">NAMA PASIEN</th>
                                    <th class="text-center">UMUR</th>
                                    <th class="text-center">DIAGNOSA</th>
                                    <th class="text-center">TINDAKAN</th>
                                    <th class="text-center">OPERATOR</th>
                                    <th class="text-center">ANESTESI</th>
                                    <th class="text-center">JAMINAN</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(row, idx) in calendarEvents" :key="row.extendedProps.id">
                                    <!-- editable time -->
                                    <td class="time-cell align-middle">
                                        {{ idx + 1 }}
                                    </td>

                                    <!-- Column -->
                                    <td class="align-middle">
                                        {{ formatTime(row.start) }}
                                    </td>

                                    <!-- NORM -->
                                    <td class="align-middle">
                                        {{ row.extendedProps.norm }}
                                    </td>

                                    <!-- NAMA PASIEN -->
                                    <td class="align-middle">
                                        {{ row.extendedProps.alias_name }}. {{ row.extendedProps.patient_name }}
                                    </td>

                                    <!-- UMUR -->
                                    <td class="align-middle">
                                        {{ getAge(row.extendedProps.birth_date) }}
                                    </td>

                                    <!-- DIAGNOSA -->
                                    <td class="align-middle">
                                        <div v-for="(row,idx) in row.extendedProps.icd10">
                                            {{ row.icd10_name }}
                                        </div>
                                    </td>

                                    <!-- TINDAKAN -->
                                    <td class="align-middle">
                                        {{ row.extendedProps.therapy_plan }}
                                    </td>

                                    <td class="align-middle">
                                        <div v-for="(row,idx) in row.extendedProps.operator_name">
                                            {{ row }}
                                        </div>
                                    </td>

                                    <td class="align-middle">
                                        <div v-for="(row,idx) in row.extendedProps.anesthesiologist_name">
                                            {{ row }}
                                        </div>
                                    </td>

                                    <td class="align-middle">
                                        {{ row.extendedProps.company_name }} Kelas {{ row.extendedProps.service_class_name }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header text-bold-18 bg-primary-200">List Pasien</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <label class="c-form-label">Tanggal Rencana Operasi</label>
                        <div class="row justify-content-center align-items-center position-relative">
                            <div class="col-6">
                                <b-form-input type="date" v-model="params.date_start"></b-form-input>
                            </div>
                            <span class="position-absolute text-center">-</span>
                            <div class="col-6">
                                <b-form-input type="date" v-model="params.date_end"></b-form-input>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <label class="c-form-label">Nama Pasien</label>
                        <b-form-input v-model="params.name"/>
                    </div>
                    <div class="col-3">
                        <label class="c-form-label">No RM</label>
                        <b-form-input v-model="params.norm"/>
                    </div>
                </div>
                <div class="d-flex mt-40">
                    <b-button variant="primary" @click="filterData" :disabled="isDisabledFilter" class="mr-2">
                        <feather-icon icon="SearchIcon" /> Filter
                    </b-button>
                    <b-button @click="filterReset" :disabled="isDisabledFilter" variant="outline-primary" class="mr-2">
                        <feather-icon icon="XIcon" /> Clear Filter
                    </b-button>
                </div>
            </div>
        </div>

        <section class="mt-32">
            <div class="row mx-0">
                <b-form-group label-cols="6" label-align="left" label-size="sm" label-for="sortBySelect" class="text-nowrap mb-md-0 mr-1">
                    <span class="text-regular-14">Tampilkan</span>
                    <b-form-select id="per-page-select" v-model="perPage" size="sm" :options="pageOptions"/>
                    <span class="text-regular-14">Entri</span>
                </b-form-group>
            </div>
            <b-table
              hover
              bordered
              responsive
              :fields="OKfields"
              :items="operating_list"
              thead-class="text-bold-14"
              tbody-tr-class="text-regular-12"
              class="mt-12 text-center"
            >
              <!-- Nama Pasien -->
              <template #cell(name)="row">
                [{{ row.item.alias_name }}] {{ row.item.patient_name }}
              </template>
            
              <!-- Jenis Operasi -->
              <template #cell(operation_type_id)="row">
                <div class="w-100 text-center">
                  <span v-if="row.value == 1">CITO</span>
                  <span v-else-if="row.value == 2">ELECTIVE</span>
                  <span v-else-if="row.value == 3">One Day Care (ODC)</span>
                  <span v-else>-</span>
                </div>
              </template>
            
              <!-- Ruang Operasi -->
              <template #cell(room)="row">
                <template v-if="row.item.room_name || row.item.bed_name">
                  {{ row.item.room_name ? row.item.room_name : '-' }}<template v-if="row.item.bed_name">, Bed {{ row.item.bed_name }}</template>
                </template>
                <div v-else>-</div>
              </template>
            
              <!-- Operator -->
              <template #cell(operators)="row">
                <div v-if="Array.isArray(row.value)">
                  <div v-for="(item, index) in row.value" :key="index">
                    {{ getRoleName(item.operator_role_type_id) }}: {{ item.operator_name }}
                  </div>
                </div>
                <span v-else>-</span>
              </template>
            
              <!-- Durasi -->
              <template #cell(duration)="row">
                <span>{{ row.value ? row.value : '-' }}</span>
              </template>
            
              <!-- Tindakan -->
              <template #cell(therapy_plan)="row">
                <span>{{ row.value ? row.value : '-' }}</span>
              </template>
            
              <!-- Rencana Operasi -->
              <template #cell(operation_approved_date)="row">
                <div>{{ row.value ? row.value : '-' }}</div>
              </template>
            
              <!-- Disetujui Oleh -->
              <template #cell(approved)="row">
                <div>{{ row.item.employee_approved_name ? row.item.employee_approved_name : '-' }}</div>
                <div>{{ row.item.employee_approved_name ? row.item.employee_approved_date : '' }}</div>
              </template>
            
              <!-- Action Buttons -->
              <template #cell(action)="row">
                <div class="d-flex">
                  <b-button
                    @click="onModalInfo(row.item)"
                    v-b-popover.hover.top="'Informasi Operasi'"
                    size="sm"
                    variant="w3green"
                    class="mr-2"
                  >
                    <feather-icon icon="SearchIcon" />
                  </b-button>
                  <template v-if="row.item.status == 1">
                    <b-button
                      @click="onConfirmModal(row.item)"
                      v-b-popover.hover.top="'Konfirmasi Order'"
                      size="sm"
                      variant="orange-500"
                      class="mr-2"
                    >
                      <feather-icon icon="CheckSquareIcon" />
                    </b-button>
                    <b-button
                      @click="cancelOrder(row.item)"
                      size="sm"
                      variant="danger"
                      v-b-popover.hover.top="'Batalkan Operasi'"
                    >
                      <feather-icon icon="TrashIcon" />
                    </b-button>
                  </template>
                  <template v-if="row.item.status == 2 || row.item.status == 3">
                    <b-button
                        size="sm"
                        variant="warning"
                        v-b-popover.hover.top="'Edit Operasi'"
                        class="mr-2"
                        @click="openEditModal(row.item)"
                        >
                        <feather-icon icon="Edit2Icon" />
                    </b-button>
                    <router-link :to="{ path: '/operatie-kamer/' + row.item.id }" target="_blank">
                      <b-button
                        v-b-popover.hover.top="'Tindakan'"
                        size="sm"
                        variant="primary"
                        class="mr-2"
                      >
                        <feather-icon icon="AirplayIcon" />
                      </b-button>
                    </router-link>
                  </template>
                </div>
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
                            align="fill" size="sm" class="my-0"></b-pagination>
                    </div>
                </div>
            </div>
        </section>

        <b-modal scrollable v-model="active.modal_info" title="Informasi Operasi" hide-footer>
            <b-table-simple responsive class="w-100" style="font-size: 13px;">
                <b-tr>
                    <b-th class="align-top" style="width: 170px">Nama</b-th>
                    <b-td class="align-top text-center">:</b-td>
                    <b-td class="align-top">{{ information.patient_name ? information.patient_name : '-' }}</b-td>
                </b-tr>
                <b-tr>
                    <b-th class="align-top">No RM</b-th>
                    <b-td class="align-top text-center">:</b-td>
                    <b-td class="align-top">{{ information.norm ? information.norm : '-' }}</b-td>
                </b-tr>
                <b-tr>
                    <b-th class="align-top">Tanggl Lahir</b-th>
                    <b-td class="align-top text-center">:</b-td>
                    <b-td class="align-top">{{ localDate(information.birth_date ? information.birth_date : 0) }} ({{ parsed_age(information.birth_date ? information.birth_date : 0) }})</b-td>
                </b-tr>
                <b-tr>
                    <b-th class="align-top">Operator & Anestesi</b-th>
                    <b-td class="align-top text-center">:</b-td>
                    <b-td class="align-top">
                        <template v-if="information && information.operators">
                            <li v-for="(item, index) in information.operators" :key="index">{{ item.operator_name }}</li>
                        </template>
                        <div v-else>-</div>
                    </b-td>
                </b-tr>
                <b-tr>
                    <b-th class="align-top">Tindakan</b-th>
                    <b-td class="align-top text-center">:</b-td>
                    <b-td class="align-top">{{ information.therapy_plan ? information.therapy_plan : '-' }}</b-td>
                </b-tr>
                <b-tr>
                    <b-th class="align-top">Diagnosa ICD10</b-th>
                    <b-td class="align-top text-center">:</b-td>
                    <b-td class="align-top">
                        <template v-if="information && information.icd10">
                            <li v-for="(item, index) in information.icd10" :key="index">{{ item.icd10_code }} {{ item.icd10_name }}</li>
                        </template>
                        <div v-else>-</div>
                    </b-td>
                </b-tr>
                <b-tr>
                    <b-th class="align-top">Tindakan ICD9CM</b-th>
                    <b-td class="align-top text-center">:</b-td>
                    <b-td class="align-top">
                        <template v-if="information && information.icd9cm">
                            <li v-for="(item, index) in information.icd9cm" :key="index">{{ item.icd9cm_code }} {{ item.icd9cm_name }}</li>
                        </template>
                        <div v-else>-</div>
                    </b-td>
                </b-tr>
                <b-tr>
                    <b-th class="align-top">Tanggal Operasi (Terkonfirmasi)</b-th>
                    <b-td class="align-top text-center">:</b-td>
                    <b-td class="align-top">{{ information.operation_approved_date ? information.operation_approved_date : '-' }}</b-td>
                </b-tr>
                <b-tr>
                    <b-th class="align-top">Ruang Operasi</b-th>
                    <b-td class="align-top text-center">:</b-td>
                    <b-td class="align-top">{{ information.room_number ? `OK - ${information.room_number}` : '-' }}</b-td>
                </b-tr>
            </b-table-simple>
            <div class="w-100 d-flex justify-content-end">
                <b-button variant="secondary" @click="active.modal_info = false"> Tutup</b-button>
                <b-button
                size="sm"
                variant="primary"
                class="ml-2"
                @click="openTindakan(information.id)" 
                >
                <feather-icon icon="AirplayIcon" />
                Buka Tindakan
                </b-button>
                
            </div>

        </b-modal>
        <b-modal v-model="active.modal_confirm" title="Konfirmasi Order" no-close-on-backdrop>
            <div class="row">
                <div class="col-lg-4">
                    <label class="c-form-label">Tipe Ruangan</label>
                    <v-select v-model="form_confirm.type_room_id" :options="TypeRoomOptions" :reduce="(TypeRoomOptions) => TypeRoomOptions.value" label="title" placeholder="-- Pilih Tipe Ruangan --" disabled></v-select>
                </div>

                <div class="col-lg-4 mt-12 mt-lg-0">
                    <label class="c-form-label">Ruangan</label>
                    <v-select v-model="form_confirm.room_id" :options="OptionsRoom" :reduce="(OptionsRoom) => OptionsRoom.value"
                        label="title" placeholder="-- Ruangan --"></v-select>
                </div>

                <div class="col-lg-4 mt-12 mt-lg-0">
                    <label class="c-form-label">Bed</label>
                    <v-select v-model="form_confirm.bed_id" :options="OptionsBed" :reduce="(OptionsBed) => OptionsBed.value"
                        label="title" placeholder="-- Bed --"></v-select>
                </div>
                <div class="col-lg-4 mt-12">
                    <label class="c-form-label">Tanggal Operasi</label>                        
                    <b-form-input v-model="form_confirm.operation_approved_date" type="datetime-local" class="form-control" />
                </div>
                <div class="col-lg-4 mt-12">
                    <label class="c-form-label">Dokter Anestesi</label>
                    <v-select v-model="form_confirm.main_anesthesia" :options="anesthesiologist_list" :reduce="anesthesiologist_list => anesthesiologist_list.value" label="title" placeholder="-- Dokter Anestesi --"></v-select>
                </div>
            </div>
            <template #modal-footer>
                <div class="w-100 d-flex justify-content-end">
                    <b-button @click="validationConfirmOrder" :disabled="disabled.confirm" variant="primary" class="px-3">
                        <feather-icon icon="SaveIcon" /> Simpan
                    </b-button>
                </div>
            </template>
        </b-modal>
        <b-modal
          v-model="editModal.show"
          title="Edit Data Operasi"
          ok-title="Simpan"
          cancel-title="Batal"
          @ok="saveEditModal"
        >
          <b-form-group label="Kode Pendaftaran">
            <b-form-input :value="editModal.item?.no_order" disabled />
          </b-form-group>
          <b-form-group label="Nama">
            <b-form-input :value="editModal.item ? `[${editModal.item.alias_name}] ${editModal.item.patient_name}` : ''" disabled />
          </b-form-group>
          <b-form-group label="Tanggal Operasi (Terkonfirmasi)">
            <b-form-input v-model="editModal.form.operation_approved_date" type="datetime-local" />
          </b-form-group>
          <b-form-group label="Ruang Operasi">
            <v-select v-model="editModal.form.room_id" :options="OptionsRoom" :reduce="(OptionsRoom) => OptionsRoom.value"
                        label="title" placeholder="-- Ruangan --"></v-select>
          </b-form-group>
          <b-form-group label="Dokter Anestesi">
            <v-select
              v-model="editModal.form.anesthesiologist_id"
              :options="anesthesiologist_list"
              :reduce="opt => opt.value"
              label="title"
              placeholder="-- Dokter Anestesi --"
            />
          </b-form-group>
          <b-form-group label="Durasi (menit)">
            <b-form-input v-model="editModal.form.duration" type="number" min="1" />
          </b-form-group>
          <b-form-group label="Tindakan">
            <b-form-input v-model="editModal.form.therapy_plan" />
          </b-form-group>
        </b-modal>
    </div>
</template>
<script>
import { mapState } from 'vuex'
import ModernLabel from '@/@core/components/label/ModernLabel.vue';
import vSelect from 'vue-select'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import moment from 'moment'
import EventCalendar from '@/@core/components/calendar/EventCalendar.vue'

export default {
    name: 'OperatieKamer',
    components: {
        ModernLabel, vSelect, EventCalendar
    },
    data() {
        return {
            calendarEvents: [],
            currentCalendarView: 'dayGridMonth',
            selectedCalendarDate: moment().format('YYYY-MM-DD'),        
            userData: JSON.parse(localStorage.getItem('userData')),
            active: {
                modal_info: false,
                modal_confirm: false,
            },
            disabled: {
                confirm: false,
            },
            regData: {},
            doctor_list: [],
            operating_list: [],
            pageOptions: [20, 50, 100],
            currentPage:1,
            perPage: 20,
            totalRows: 0,
            firstRows: 1,
            lastRows: 20,
            params: {
                limit: 20,
                offset: 0,
                norm: '',
                name: '',
                date_start: moment().format('YYYY-MM-DD'),
                date_end: moment().add(7, 'days').format('YYYY-MM-DD'),
            },
            isDisabledFilter: false,
            anesthesiologist:'', 
            operator_name: [],
            anesthesiologist_name: [],
            editModal: {
              show: false,
              item: null,
              form: {
                operation_approved_date: '',
                room_id: '',
                anesthesiologist_id: '',
                duration: '',
                therapy_plan: '',
              }
            },

            // First Look
            OKfields: [
                { key: 'no_order', label: 'Kode Pendaftaran', thStyle: { minWidth: '140px' } },
                { key: 'name', label: 'Nama', thStyle: { minWidth: '140px' } },
                { key: 'norm', label: 'No RM', thStyle: { minWidth: '100px' } },
                { key: 'health_facility_item_name', label: 'Ruangan', thStyle: { minWidth: '100px' } },
                { key: 'operation_type_id', label: 'Jenis Operasi', thStyle: { minWidth: '140px' } },
                { key: 'date_added', label: 'Tanggal Input', thStyle: { minWidth: '140px' } },
                { key: 'operation_date', label: 'Tanggal Rencana Operasi', thStyle: { minWidth: '140px' } },
                { key: 'operation_approved_date', label: 'Tanggal Operasi (Terkonfirmasi)', thStyle: { minWidth: '140px' } },
                { key: 'room', label: 'Ruang Operasi', thStyle: { minWidth: '140px' } },
                { key: 'operators', label: 'Operator', thStyle: { minWidth: '260px' } },
                { key: 'duration', label: 'Durasi (menit)', thStyle: { minWidth: '140px' } },
                { key: 'therapy_plan', label: 'Tindakan', thStyle: { minWidth: '140px' } },
                { key: 'approved', label: 'Disetujui Oleh', thStyle: { minWidth: '140px' } },
                { key: 'action', label: 'Action', thStyle: { minWidth: '140px' } },
            ],
            form_confirm: {
                order_id: '',
                main_anesthesia: '',
                bed_id: '',
                status: '',
                operation_approved_date: '',
                type_room_id: 29,
                room_id: '',
            },
            information: {},
        }
    },
    computed: {
        ...mapState({ 
            TypeRoomOptions: state => state.helper.options.type_room,
            OptionsRoom: state => state.room.options.room,
            OptionsBed: state => state.room.options.bed,         
        }),
        anesthesiologist_list() {
            const anesthesiologists = this.doctor_list.filter(item => {
                const cleanName = item?.title?.replace(/\s+/g, ' ').replace(/\s*,\s*/g, ',').trim(); 
                if(cleanName){
                    const parts = cleanName.split(',Sp.');
                    if (parts.length < 2) return false;

                    const specialization = parts[1].trim().toLowerCase();
                    return specialization.includes('an');
                }
            });

            return anesthesiologists
        },
    },
    watch : {
        // 'OptionsPharmacyDepo' : function(val){
        //     console.log('val: ', val)
        // },
         // Section Tab Detail Operasi
        // 'form_confirm.type_room_id'(value) {
        //     this.form_confirm.room_id = ''   
        //     this.form_confirm.bed_id = ''   
            
        //     if (value) {
        //         return this.$store.dispatch('room/getParamRoom', value)
        //     }
        // },
        'form_confirm.room_id'(value) {
            this.form_confirm.bed_id = ''   

            if (value) {
                return this.$store.dispatch('room/getParamBed', value)
            }
        },
        currentPage: {
            handler: function(value) {
                this.firstRows = this.perPage*(value-1)+1
                this.lastRows = this.perPage*(value)+1
                this.params.offset = this.perPage*(value-1)
                this.params.limit = this.perPage
                this.getOperatingList()
            }
        },
        perPage: {
            handler: function(value) {
                this.params.limit = this.perPage
                this.getOperatingList()
            }
        },
    },
    methods: {
        // GENERAL
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                employee: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },
        openTindakan(id) {
            if (id) window.open(`/operatie-kamer/${id}`, '_blank');
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
        },
        localDate(input_date) {
            return moment(input_date).locale("id").format('DD MMMM YYYY')
        },
        getData () {
            this.$store.dispatch('employee/getDoctorOptions', '').then((response) => {
                this.doctor_list = response
            })
        },

        getOperatingList (){
            try {
                this.$store.dispatch('operatie/getAllList', this.params).then(response => {
                    this.operating_list = response.rows
                    this.getData()
                    // console.log('Operating List: ', this.operating_list);
                    this.totalRows = response.iTotal
                    this.isDisabledFilter = false
                    // console.log('Calendar Events: ', this.calendarEvents);
                })
            } catch (error) {
                console.log('Error while GET DATA at: ', error);
            }
        },

        getCalendarOperatingList(start, end) {
            const params = {
            date_type: 'approved_date', // <-- change this if needed
            date_start: moment(start).format('YYYY-MM-DD'),
            date_end: moment(end).subtract(1, 'days').format('YYYY-MM-DD'), // end is exclusive
            limit: 1000,
            offset: 0,
            };

            this.$store.dispatch('operatie/getAllList', params).then(response => {
            response.rows.forEach(item => {
                if (Array.isArray(item.operators)) {
                    item.operator_name = item.operators
                        .filter(op => this.getRoleName(op.operator_role_type_id) === 'Operator')
                        .map(op => op.operator_name);
                    item.anesthesiologist_name = item.operators
                        .filter(op => this.getRoleName(op.operator_role_type_id) === 'Anastesi')
                        .map(op => op.operator_name);
                } else {
                    item.operator_name = [];
                    item.anesthesiologist_name = [];
                }
            });
            this.calendarEvents = this.mapOperatingListToCalendarEvents(response.rows.sort((a, b) => new Date(a.operation_approved_date) - new Date(b.operation_approved_date)));
            });
        },


        onCalendarDatesSet(arg) {
            console.log('onCalendarDatesSet called with:', arg);
            this.currentCalendarView = arg.view.type;
            console.log('Current Calendar View:', this.currentCalendarView);
            this.selectedCalendarDate = moment(arg.start).format('YYYY-MM-DD');
            console.log('Selected Calendar Date:', this.selectedCalendarDate);
            // fetch by the visible range (not just the month)
            // this.getCalendarOperatingList(arg.start, arg.end);
            if (arg.view.type !== 'timeGridDay') {
                this.getCalendarOperatingList(arg.start, arg.end);
            }
        },

        onGotoDay(date) {
            // console.log('onGotoDay called with date:', date);
            this.$refs.eventCalendar.gotoDay(date);
        },

        mapOperatingListToCalendarEvents(list) {
            list.sort((a, b) => new Date(a.operation_approved_date) - new Date(b.operation_approved_date))
            const colorMap = {
                CITO: '#ff4d4f',      // Red for CITO
                ELECTIVE: '#1890ff',  // Blue for ELECTIVE
                ODC: '#52c41a',       // Green for One Day Care
                '-': '#888'           // Default/gray
            };
        
            return list.map(item => {
                // Determine operation type label
                const typeLabel = item.operation_type_id === 1
                    ? 'CITO'
                    : item.operation_type_id === 2
                        ? 'ELECTIVE'
                        : item.operation_type_id === 3
                            ? 'ODC'
                            : '-';
                // Join all operator names, if any, for display
                const operatorNames = Array.isArray(item.operator_name) && item.operator_name.length
                    ? item.operator_name.join(', ')
                    : '';

                const anesthesiologistNames = Array.isArray(item.anesthesiologist_name) && item.anesthesiologist_name.length
                    ? item.anesthesiologist_name.join(', ')
                    : '';

                const age = item.birth_date ? this.getAge(item.birth_date) : null;

                // Build concise event title (type is now color-coded, not in title)
                const label =
                `<b>[${item.alias_name}] ${item.patient_name}</b> ` +
                (item.birth_date ? `(${age})` : '') +
                (item.therapy_plan ? `<br>Tindakan: ${item.therapy_plan}` : '') +
                (operatorNames ? `<br>Operator: ${operatorNames}` : '');

                // Parse duration in minutes
                let durationMinutes = null;
                if (item.duration) {
                    if (typeof item.duration === 'string') {
                        const match = item.duration.match(/\d+/);
                        durationMinutes = match ? parseInt(match[0], 10) : null;
                    } else if (typeof item.duration === 'number') {
                        durationMinutes = item.duration;
                    }
                }
        
                // Format start as ISO string
                let start = item.operation_approved_date
                    ? item.operation_approved_date.replace(' ', 'T')
                    : null;
        
                // Calculate end as ISO string
                let end = null;
                if (start && durationMinutes) {
                    const startDate = new Date(start);
                    if (!isNaN(startDate.getTime())) {
                        const endDate = new Date(startDate.getTime() + durationMinutes * 60000);
                        const pad = n => n.toString().padStart(2, '0');
                        end = `${endDate.getFullYear()}-${pad(endDate.getMonth() + 1)}-${pad(endDate.getDate())}T${pad(endDate.getHours())}:${pad(endDate.getMinutes())}:${pad(endDate.getSeconds())}`;
                    }
                }
        
                // Build and return the event object for FullCalendar
                return {
                    title: '',
                    start: start,
                    end: end,
                    resourceId: item.room_number, 
                    backgroundColor: colorMap[typeLabel],
                    borderColor: colorMap[typeLabel],
                    extendedProps: {
                        ...item,
                        eventLabel: label,
                        operator_names: operatorNames,
                        anesthesiologist_names: anesthesiologistNames,
                        birth_date: item.birth_date,
                        therapy_plan: item.therapy_plan,
                        operation_type: typeLabel,
                        patient_name: item.patient_name,
                        alias_name: item.alias_name,
                        // add more if needed
                    }
                };
            });
        },
        
        openEditModal(item) {
            this.editModal.item = item;
            this.editModal.form = {
            operation_approved_date: item.operation_approved_date || '',
            room_id: item.room_id || '',
            anesthesiologist_id: (item.operators || []).find(op => this.getRoleName(op.operator_role_type_id) === 'Anastesi')?.operator_id || '',
            duration: item.duration || '',
            therapy_plan: item.therapy_plan || '',
            };
            this.editModal.show = true;
        },
       saveEditModal() {
            const item = this.editModal.item;
            const form = this.editModal.form;

            const payload = {
                id: item.id,
                // registration_destination_id: item.registration_destination_id,
                // soap_id: item.soap_id,
                main_anesthesia_id: form.anesthesiologist_id, // to add 
                operation_approved_date: form.operation_approved_date, //to add
                therapy_plan: form.therapy_plan,
                duration: form.duration,
                room_id: form.room_id, // to add
                main_operator_id: item.main_operator_id,
                operation_date: item.operation_date,
                infectious_type_id: item.infectious_type_id,
                operation_type_id: item.operation_type_id,
                accomodation_arry: item.accomodation_arry,
                icd9cm: item.icd9cm,
            };

            this.$store.dispatch('operatie/postPutOrder', payload).then(() => {
                this.editModal.show = false;
                this.getOperatingList();
            });
        },

        filterData () {
            this.show = true
            
            try {
                this.isDisabledFilter = true
                this.getOperatingList()
            } catch (error) {
                console.error('Error at: ', error);
                this.isDisabledFilter = false
                
            }
        },

        filterReset () {
            this.params.limit = 20,
            this.params.offset = 0,
            this.params.norm = '',
            this.params.name = '',
            this.params.date_start = moment().format('YYYY-MM-DD'),
            this.params.date_end = moment().add(7, 'days').format('YYYY-MM-DD'),            
            this.getOperatingList()
        },
    
        onConfirmModal(obj) {
            this.active.modal_confirm = true
            this.resetConfirmForm()

            this.form_confirm.order_id = obj.id
            this.form_confirm.operation_approved_date = obj.operation_date
            const anesthesiologists = obj.operators.filter(item => {
                const cleanName = item?.operator_name?.replace(/\s+/g, ' ').replace(/\s*,\s*/g, ',').trim(); 
                if(cleanName){
                    const parts = cleanName.split(',Sp.');
                    if (parts.length < 2) return false;

                    const specialization = parts[1].trim().toLowerCase();
                    return specialization.includes('an');
                }
            });
            this.form_confirm.main_anesthesia = anesthesiologists[0]?.operator_id        
        },

        validationConfirmOrder() {
            this.disabled.confirm = true
            this.form_confirm.status = 2

            try {
                this.$store.dispatch('operatie/putConfirmOrder', this.form_confirm).then((response) => {
                    this.notification('Success','CheckIcon','success','You have successfully')
                    this.active.modal_confirm = false
                    this.getOperatingList()
                    this.resetConfirmForm()
                    this.disabled.confirm = false
                })
                
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                this.disabled.confirm = false
                console.log('Error at: ', error);
            }
        },

        resetConfirmForm(){
            this.form_confirm.order_id = ''
            this.form_confirm.bed_id = ''
            this.form_confirm.status = ''
            this.form_confirm.operation_approved_date = ''
            this.form_confirm.type_room_id = 29
            this.form_confirm.room_id = ''
        },
        cancelOrder(obj){
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin membatalkan ${obj.no_order} ?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'primary', okTitle: 'Yes', cancelTitle: 'No', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    try {
                        this.$store.dispatch('operatie/delRemoveOrder', obj.id) .then(response => {
                            if (response.status === 200 || response.status === 201) {
                                this.notification('Success','CheckIcon','success','You have successfully')
                                this.getOperatingList()
                            } else {
                                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                            }
                        }) 
                        
                    } catch (error) {
                        this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                        console.log('Error at: ', error);
                    }
                }
            })
        },
        onModalInfo(obj) {
            this.information = ''
            this.active.modal_info = true
            this.information = { ...obj }
        },
        getRoleName(item){
            if(item == 1){
               return 'Operator'
            }
            if(item == 2){
                return 'Co-operator'
            }
            if(item == 3){
                return 'Instrumen'
            }
            if(item == 4){
                return 'Circular'
            }
            if(item == 5){
                return 'Anastesi'
            }
        },
        getAge(birthDateStr) {
            if (!birthDateStr) return '-';
            const birthDate = new Date(birthDateStr);
            const today = new Date();
        
            let years = today.getFullYear() - birthDate.getFullYear();
            let months = today.getMonth() - birthDate.getMonth();
            let days = today.getDate() - birthDate.getDate();
        
            if (days < 0) {
                months--;
                // Get days in previous month
                const prevMonth = new Date(today.getFullYear(), today.getMonth(), 0);
                days += prevMonth.getDate();
            }
            if (months < 0) {
                years--;
                months += 12;
            }
        
            let result = '';
            if (years > 0) result += `${years} th`;
            return result;
        },
        formatTime(dateStr) {
            if (!dateStr) return '';
            const d = new Date(dateStr);
            if (isNaN(d.getTime())) return '';
            const pad = n => n.toString().padStart(2, '0');
            return `${pad(d.getHours())}:${pad(d.getMinutes())}`;
        },
        openPrintPage() {
            this.$router.push({
            name: 'PrintOperatieKamerCalendar',
            query: { date: this.selectedCalendarDate }
            });
        }

    },
    created() {
        const now = new Date();
        const start = new Date(now.getFullYear(), now.getMonth(), 1);
        const end = new Date(now.getFullYear(), now.getMonth() + 1, 1); // exclusive
        this.getCalendarOperatingList(start, end);
        this.getOperatingList()
        this.$store.dispatch('helper/getParam', 'type_room')
        this.$store.dispatch('room/getParamRoom', 29) //default OK
        
    },

}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
.calendar-legend {
  font-size: 14px;
  font-weight: 500;
  text-align: right;
}
</style>