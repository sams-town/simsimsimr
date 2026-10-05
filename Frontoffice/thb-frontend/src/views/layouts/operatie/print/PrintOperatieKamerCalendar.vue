<template>
    <div v-if="calendarReady">
        <b-container fluid="xl" class="main-content" ref="printArea">
            <div class="row align-items-center pb-20" style="border-bottom: 3px solid black;">
                <div class="col-2 text-center">
                    <img src="@/@core/assets/images/logo-thb-small.png" width="60%" alt="rs-logo">
                </div>
                <div class="col text-center">
                    <p class="text-bold-18">RS Taman Harapan Baru</p>
                    <p>Jl. Kaliabang Tengah No. 2 RT.004 RW.023, Bekasi, Kota Bekasi</p>
                    <p>Info: +62 778 4161212 / 0853 7111 1212</p>
                </div>
                <div class="col-2 text-center mr-16">
                    <!-- <img src="@/@core/assets/images/lafki-logo.png" width="50%" alt="lafki-logo"> -->
                    <!-- <img src="@/@core/assets/images/apexa.png" width="50%" alt="lafki-logo"> -->
                </div>
            </div>
            <div class="canvas-wrapper">
                <div id="canvas" ref="canvas" class="mt-24 print-calendar-compact" style="position: relative;">
                    <div class="text-center font-weight-bold mb-1" style="font-size: 1.2rem;">
                        Jadwal Operasi {{ formattedInitialDate }}
                    </div>
                    <div class="d-flex justify-content-end align-items-center">
                        <b-button
                            ref="downloadBtn"
                            @click="downloadCalendarImage"
                            size="sm"
                            class="px-32 mx-2 download-hide"
                            variant="info"
                        ><b-icon icon="download" class="mr-1" />  Download Gambar</b-button>
                    </div>
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
                    <EventCalendar style="display: none;"
                        ref="eventCalendar"
                        :events="calendarEvents"
                        :initialView="'resourceTimeGridDay'"
                        :initialDate="initialDate"
                        :hideNavigation="true"
                        :fontSize="'11px'"
                        :allDaySlot="false"
                        :displayEventTime="true"
                        :slotMinTime="dynamicSlotMinTime"
                        :slotMaxTime="dynamicSlotMaxTime"
                        @dates-set="onCalendarDatesSet"
                    />
                </div>
            </div>
        </b-container>
    </div>
</template>

<script>
import EventCalendar from '@/@core/components/calendar/EventCalendar.vue';
import html2canvas from 'html2canvas';
import moment from 'moment';

export default {
    name: 'OperatieKamer',
    components: {
        EventCalendar
    },
    data() {
        return {
            calendarEvents: [],
            calendarReady: false,
            initialDate: this.$route.query.date || moment().format('YYYY-MM-DD'),
            moment
        }
    },
    computed: {
        formattedInitialDate() {
            return moment(this.initialDate).locale('id').format('dddd, D MMMM YYYY');
        },
        dynamicSlotMinTime() {
            if (!this.calendarEvents.length) return '08:00:00'; // fallback
            const min = this.calendarEvents.reduce((acc, ev) => {
            const d = new Date(ev.start);
            return d < acc ? d : acc;
            }, new Date(this.calendarEvents[0].start));
            return min.toTimeString().slice(0, 8);
        },
        dynamicSlotMaxTime() {
            if (!this.calendarEvents.length) return '20:00:00'; // fallback
            const max = this.calendarEvents.reduce((acc, ev) => {
            const d = new Date(ev.end || ev.start);
            return d > acc ? d : acc;
            }, new Date(this.calendarEvents[0].end || this.calendarEvents[0].start));
            // Add 1 hour for better visibility if needed:
            max.setMinutes(max.getMinutes() + 60);
            return max.toTimeString().slice(0, 8);
        },
    },
    methods: {
        formatTime(dateStr) {
            if (!dateStr) return '';
            const d = new Date(dateStr);
            if (isNaN(d.getTime())) return '';
            const pad = n => n.toString().padStart(2, '0');
            return `${pad(d.getHours())}:${pad(d.getMinutes())}`;
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

                if (Array.isArray(item.icd10)) {
                    item.icd10_names = item.icd10.map(icd => icd.icd10_name);
                } else {
                    item.icd10_names = [];
                }
            });
            this.calendarEvents = this.mapOperatingListToCalendarEvents(response.rows);
            this.$nextTick(() => {
                this.calendarReady = true;
            });

            });
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

                const icd10Names = Array.isArray(item.icd10_names) && item.icd10_names.length
                    ? item.icd10_names.join('; ')
                    : '';

                const age = item.birth_date ? this.getAge(item.birth_date) : null;

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

                const formatTime = dateStr => {
                    if (!dateStr) return '';
                    const d = new Date(dateStr);
                    if (isNaN(d.getTime())) return '';
                    const pad = n => n.toString().padStart(2, '0');
                    return `${pad(d.getHours())}:${pad(d.getMinutes())}`;
                };

                // Build concise event title (type is now color-coded, not in title)
                const label =
                `<b>[${item.alias_name}] ${item.patient_name}</b> ` +
                (item.birth_date ? `(${age})` : '') +
                (item.therapy_plan ? `<br>Tindakan: ${item.therapy_plan}` : '') +
                (icd10Names ? `<br>Diagnosa: ${icd10Names}` : '') +
                (operatorNames ? `<br>Operator: ${operatorNames}` : '') +
                (anesthesiologistNames ? `<br>Anestesi: ${anesthesiologistNames}` : '') +
                (start ? `<br>Jam Mulai: ${formatTime(start)}` : '');



                // Build and return the event object for FullCalendar
                return {
                    // title: label,
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
                        icd10_names: icd10Names,
                        age: age,
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
        onCalendarDatesSet(arg) {
            // fetch by the visible range (not just the month)
            // this.getCalendarOperatingList(arg.start, arg.end);
            if (arg.view.type !== 'timeGridDay') {
                this.getCalendarOperatingList(arg.start, arg.end);
            }
        },
        printCanvas() {
            const canvas = this.$refs.canvas;
            const originalContents = document.body.innerHTML;
            this.$nextTick(() => {
                setTimeout(() => {
                    document.body.innerHTML = canvas.innerHTML;
                    window.print();
                    document.body.innerHTML = originalContents;
                    location.reload();
                }, 300); // You can increase this if needed (e.g. 500)
            });
        },
        closeWindow(){
            window.close()
        },
        downloadCalendarImage() {
            const btn = this.$refs.downloadBtn?.$el || this.$refs.downloadBtn;
            if (btn) btn.style.display = 'none';
            const printElem = this.$refs.printArea;
            html2canvas(printElem, { useCORS: true, backgroundColor: '#fff' }).then(canvas => {
                const link = document.createElement('a');
                link.download = 'calendar.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
                if (btn) btn.style.display = '';
            });
        },
    },
    created() {
        // console.log('initialDate:', this.initialDate); // <-- log heres
        const now = new Date();
        const start = new Date(now.getFullYear(), now.getMonth(), 1);
        const end = new Date(now.getFullYear(), now.getMonth() + 1, 1); // exclusive
        // console.log('Fetching calendar events from:', start, 'to:', end);
        this.getCalendarOperatingList(this.initialDate, this.initialDate);
    }
}
</script>
<style>
.print-calendar-compact .fc-timegrid-slot,
.print-calendar-compact .fc-timegrid-axis,
.print-calendar-compact .fc-timegrid-col {
  min-height: 60px !important;
  height: 60px !important;
  line-height: 1.5 !important;
  padding: 0 !important;
}
@media print {
  .download-hide {
    display: none !important;
  }
}
</style>