<!-- EventCalendar.vue -->
<template>
    <!-- <FullCalendar ref="fullCalendar" :options="calendarOptions" /> -->
      <div :style="{ '--fc-hour-font-size': fontSize }">
          <FullCalendar ref="fullCalendar" :options="calendarOptions" />
      </div>
  </template>
  <script>
  import FullCalendar from '@fullcalendar/vue'
  import dayGridPlugin from '@fullcalendar/daygrid'
  import timeGridPlugin from '@fullcalendar/timegrid'
  import resourceTimeGridPlugin from '@fullcalendar/resource-timegrid'
  import tippy from 'tippy.js'
  import 'tippy.js/dist/tippy.css'
//   import adaptivePlugin from '@fullcalendar/adaptive'
  
  export default {
    name: 'EventCalendar',
    components: { FullCalendar },
    data() {
      return {
        expandedEvents: {},
      }
    },
    watch: {
      events: {
          handler(events) {
              // Build the map whenever events change
              const map = {};
              events.forEach(ev => {
                  const start = new Date(ev.start);
                  const end = ev.end ? new Date(ev.end) : start;
                  let d = new Date(start);
                  while (d <= end) {
                      const key = `${d.getFullYear()}-${(d.getMonth()+1).toString().padStart(2, '0')}-${d.getDate().toString().padStart(2, '0')}`;
                      if (!map[key]) map[key] = [];
                      map[key].push(ev);
                      d.setDate(d.getDate() + 1);
                  }
              });
              this.eventsByDate = map;
          },
          immediate: true,
          deep: true
      }
      },
    props: {
        events: Array,
        initialView: {
            type: String,
            default: 'dayGridMonth'
        },
        initialDate: {
            type: String,
            default: ''
        },
        initialView: {
            type: String,
            default: 'dayGridMonth'
        },
        headerToolbar: {
            type: Object,
            default: null
        },
        height: {
            type: [String, Number],
            default: 'auto'
        },
        hideNavigation: {
            type: Boolean,
            default: false
        },
        fontSize: {
            type: String,
            default: '14px'
        },
        useCustomEventContent: {
            type: Boolean,
            default: true
        },
        displayEventTime: {
            type: Boolean,
            default: false
        },
        allDaySlot: {
            type: Boolean,
            default: true
        },
        slotMinTime: {
            type: String,
            default: '00:00:00'
        },
        slotMaxTime: {
            type: String,
            default: '24:00:00'
        },
    },
    computed: {
      calendarOptions() {
          const options = {
              plugins: [dayGridPlugin, timeGridPlugin, resourceTimeGridPlugin],
            //   plugins: [dayGridPlugin, timeGridPlugin, resourceTimeGridPlugin, adaptivePlugin],
              allDaySlot: this.allDaySlot,
              slotMinTime: this.slotMinTime,
              slotMaxTime: this.slotMaxTime,
              eventContent: this.useCustomEventContent
                   ? function(arg) {
                  // Only show label in week and day views
                  const viewType = arg.view.type;
                  if (viewType === 'timeGridWeek' || viewType === 'resourceTimeGridDay' || viewType === 'timeGridDay') {
                  const div = document.createElement('div');
                  div.innerHTML = arg.event.extendedProps.eventLabel || '';
                  return { domNodes: [div] };
                  }
                  // For month view, show nothing (or just a dot)
                  return { domNodes: [] };
              } : undefined,
              initialView: this.initialView,
              // initialDate: this.initialDate,
            headerToolbar: this.headerToolbar || {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,resourceTimeGridDay'
            },
            displayEventTime: this.displayEventTime,
            views: {
                resourceTimeGridDay: {
                    type: 'resourceTimeGrid',
                    duration: { days: 1 },
                    buttonText: 'day'
                }
            },
              resources: [
                  { id: 1, title: 'OK 1' },
                  { id: 2, title: 'OK 2' },
                  { id: 3, title: 'OK 3' },
                  { id: 4, title: 'OK 4' }, 
              ],
              events: this.events.map(ev => ({
                  ...ev,
                  resourceId: ev.extendedProps.room_number 
              })),
              weekends: true,
              // events: this.events,
              headerToolbar: {
                  left: 'prev,next today',
                  center: 'title',
                  right: 'dayGridMonth,timeGridWeek,resourceTimeGridDay'
              },
              eventTimeFormat: { // 24-hour format, no AM/PM
                  hour: '2-digit',
                  minute: '2-digit',
                  hour12: false
              },
              eventClick: (info) => {
                  this.$emit('event-info', info.event.extendedProps);
                  // this.$emit('goto-day', info.event.start);
  
              },
              // eventDisplay: 'block',
              eventMouseEnter: (info) => {
                  const alias = info.event.extendedProps.alias_name || '';
                  const name = info.event.extendedProps.patient_name || '';
                  const therapyPlan = info.event.extendedProps.therapy_plan || '';
                  const type = info.event.extendedProps.operation_type || '';
                  const birthDate = info.event.extendedProps.birth_date || '';
                  const start = info.event.start ? new Date(info.event.start) : null;
                  const end = info.event.end ? new Date(info.event.end) : null;
                  const age = this.getAge(birthDate);
                  const operatorNames = info.event.extendedProps.operator_names || '';
                  const anesthesiologistNames = info.event.extendedProps.anesthesiologist_names || '';
                  const monthNames = [
                      'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                      'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                  ];
                  const startStr = start
                  ? `${start.getHours().toString().padStart(2, '0')}:${start.getMinutes().toString().padStart(2, '0')}`
                  : '-';
  
                  const endStr = end
                  ? `${end.getHours().toString().padStart(2, '0')}:${end.getMinutes().toString().padStart(2, '0')}`
                  : '';
  
                  const tooltipContent = `
                      <div>
                      <div><strong>[${alias}] ${name}</strong></div>
                      <div style="font-style: italic;">${age}</div>
                      ${therapyPlan ? `<div>Tindakan: ${therapyPlan}</div>` : ''}
                      ${operatorNames ? `<div>Operator: ${operatorNames}</div>` : ''}
                      ${anesthesiologistNames ? `<div>Anestesi: ${anesthesiologistNames}</div>` : ''}
                      <div>${startStr} ${endStr ? '- ' + endStr : ''}</div>
                      <div style="margin-top:4px; color: #888; font-style: italic;">${type}</div>
                      </div>
                  `;
                  // Create a tippy tooltip on hover
                  tippy(info.el, {
                      content: tooltipContent,
                      allowHTML: true,
                      placement: 'top',
                      theme: 'light-border',
                      trigger: 'manual',
                  }).show();
                  info.el._tippy && info.el._tippy.show();
              },
              eventMouseLeave: function(info) {
                  // Hide and destroy the tooltip when mouse leaves
                  if (info.el._tippy) {
                      info.el._tippy.hide();
                      info.el._tippy.destroy();
                  }
              },
              datesSet: (arg) => {
                  this.$emit('dates-set', arg);
              },
              dayCellContent: (arg) => {
                  if (!arg.view || !arg.view.type.startsWith('dayGrid')) {
                      return;
                  }
                  return this.buildOperasiLabel(arg.date, { showDateNumber: true });
              },
              dayHeaderContent: (arg) => {
                  // If in dayGridMonth (monthly view), show default day name
                  if (arg.view && arg.view.type === 'dayGridMonth') {
                      return arg.text;
                  }
                  // Otherwise, show the day name and your custom label
                  const customLabel = this.buildOperasiLabel(arg.date, { showDateNumber: false });
                  // If buildOperasiLabel returns an object with .html, combine with day name
                  if (customLabel && customLabel.html) {
                      return {
                      html: `
                          <div style="display: flex; flex-direction: column; align-items: flex-start;">
                          <span style="font-weight: bold; font-size: 1.1em; margin-bottom: 2px;">${arg.text}</span>
                          ${customLabel.html}
                          </div>
                      `
                      };
                  }
                  // Fallback
                  return arg.text;
              },
          };
          if (this.initialDate) {
              options.initialDate = this.initialDate;
          }
          if (this.height) {
              options.height = this.height;
          }
          if (this.hideNavigation) {
              options.headerToolbar = this.hideNavigation;
          }
          return options;
      }
    },
    methods: {
      gotoDay(date) {
          // Access FullCalendar's API and change view
          this.$refs.fullCalendar.getApi().changeView('resourceTimeGridDay', date);
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
          if (years > 0) result += `${years} th `;
          if (months > 0) result += `${months} bln `;
          if (days > 0) result += `${days} hr`;
          return result.trim() || '0 hr';
      },
      buildOperasiLabel(cellDate, opts = { showDateNumber: true }) {
          const key = `${cellDate.getFullYear()}-${(cellDate.getMonth()+1).toString().padStart(2, '0')}-${cellDate.getDate().toString().padStart(2, '0')}`;
          const eventsForDay = this.eventsByDate[key] || [];
          if (eventsForDay.length === 0) {
              return { html: `<div class="fc-daygrid-day-number">${cellDate.getDate()}</div>` };
          }
  
          // Count by type
          const typeCounts = {};
          eventsForDay.forEach(ev => {
          const type = ev.extendedProps?.operation_type || 'LAINNYA';
          typeCounts[type] = (typeCounts[type] || 0) + 1;
          });
  
          // Build label
          let label = `<div class="fc-label-heading">${eventsForDay.length} OPERASI</div>`;
          const typeColor = {
          'CITO': '#ff4d4f',
          'ELECTIVE': '#1890ff',
          'ODC': '#52c41a',
          };
          Object.entries(typeCounts).forEach(([type, count]) => {
          const color = typeColor[type] || '#888';
          label += `
              <div class="fc-label-row">
              <span class="fc-label-dot" style="background:${color}"></span>
              ${count} ${type}
              </div>
          `;
          });
  
          return {
              html: `
                  <div class="fc-custom-daycell">
                  ${opts.showDateNumber ? `<div class="fc-daygrid-day-number fc-custom-day-number">${cellDate.getDate()}</div>` : ''}
                  <div class="fc-custom-label fc-label-clickable fc-label-card" data-date="${cellDate.toISOString()}" style="cursor:pointer;" title="Lihat detail hari ini">
                      ${label}
                  </div>
                  </div>
              `
          };
      },
    },
      mounted() {
          // Event delegation: listen for clicks on the calendar root
          this.$nextTick(() => {
              const calendarRoot = this.$el;
              calendarRoot.addEventListener('click', (e) => {
                  // console.log('click on calendar root', e);
                  const label = e.target.closest('.fc-label-clickable');
                  if (label) {
                      // console.log('label clicked', label);
                      const dateStr = label.getAttribute('data-date');
                      if (dateStr) {
                          // console.log('goto day', dateStr);
                          // Emit the event with the JS Date object
                          this.$emit('goto-day', new Date(dateStr));
                      }
                  }
              });
          });
      },
  }
  </script>
  
  <style>
  .fc-timegrid-slot-label, /* main time labels */
  .fc-timegrid-axis-cushion, /* inner span */
  .fc-timegrid-axis {
    font-size: var(--fc-hour-font-size, 11px) !important;
  }
  .fc-col-header-cell{
      font-size: var(--fc-hour-font-size, 11px) !important;
  }
  /* .fc-event-title, 
  .fc-event-title-container, 
  .fc-custom-label, 
  .fc-label-card, 
  .fc-label-heading, 
  .fc-label-row {
    font-size: var(--fc-event-font-size, 14px) !important;
  } */
  .fc-custom-daycell {
      /* max-height: 110px; */
      /* overflow-y: auto; */
      display: flex;
      flex-direction: column;
      position: relative;
      width: 100%;
      /* height: 100%; */
      /* border: 1px solid blue; */
  }
  .fc-daygrid-day-number{
      width: 100%;
  }
  .fc-custom-day-number {
      align-self: flex-end;
      color: #212529;
      pointer-events: none;
  }
  .fc-custom-label {
      /* border: 1px solid black; */
      align-self: stretch;
      font-size: 13px;
      font-weight: normal;
      text-align: left;
      word-break: break-word;
      margin-top: 4px;
      width: 100%;
      display: block;
  }
  .fc-label-card {
    background: #fff;
    border: 1.5px solid #939393;
    border-radius: 4px;
    box-shadow: 0 2px 8px rgba(60, 60, 60, 0.06);
    padding: 10px 8px 8px 8px;
    margin-top: 6px;
    margin-bottom: 4px;
    transition: box-shadow 0.2s, border-color 0.2s;
    color: #232526;
  }
  .fc-timegrid-event-harness{
      font-size: 13px;
  }
  
  .fc-label-card:hover {
    box-shadow: 0 4px 16px rgba(60, 60, 60, 0.12);
    border-color: #bdbdbd;
    background: #fafbfc;
  }
  .fc-label-heading {
    font-size: 1.2em;
    font-weight: bold;
    margin-bottom: 6px;
    letter-spacing: 0.5px;
    text-shadow: none;
    color: #232526;
  }
  .fc-label-row {
    display: flex;
    align-items: center;
    font-size: 12px;
    margin-bottom: 2px;
    color: #555;
  }
  .fc-label-dot {
    display: inline-block;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    margin-right: 6px;
    margin-left: 2px;
    border: 1.5px solid #e0e0e0;
    box-shadow: 0 0 2px #eee;
  }
  /* Allow event titles to wrap and show multiple lines */
  .fc-event-title, .fc-event-title-container {
      /* color: #0A0A0A !important; */
      white-space: normal !important;
      overflow: visible !important;
      text-overflow: unset !important;
      /* display: block !important; */
      max-height: none !important;
      height: auto !important;
      line-height: 1.2 !important;
      word-break: break-word !important;
  }
  .fc-daygrid-event {
      /* max-height: 70px;
      overflow-y: scroll;  */
      min-height: 24px;
  }
  .fc-daygrid-day-events{
      max-height: 100px;
      overflow-y: auto;
  }
  /* Black text for month (dayGrid) view */
  .fc-daygrid-event,
  .fc-daygrid-event .fc-event-title,
  .fc-daygrid-event .fc-event-time,
  .fc-daygrid-event-dot {
    color: #3e3b3b !important;
  }
  
  /* White text for timeGrid (week/day) view */
  .fc-timegrid-event,
  .fc-timegrid-event .fc-event-title,
  .fc-timegrid-event .fc-event-time {
    color: #fff !important;
  }
  
  .fc-daygrid-day-frame {
    padding: 0 !important;
  }
  
  .fc-daygrid-day-frame {
      margin: 0 !important;
      max-height: 180px;  
      overflow-y: auto; 
  }
  </style>