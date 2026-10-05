<template>
  <BarChartGenerator
    ref="barChartRef"
    :chart-options="chartOptions"
    :chart-data="chartData"
    :chart-id="chartId"
    :dataset-id-key="datasetIdKey"
    :plugins="plugins"
    :css-classes="cssClasses"
    style="height: 300px;"
    :width="width"
  />
</template>

<script>
import { Bar as BarChartGenerator } from 'vue-chartjs/legacy'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale
} from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

export default {
  name: 'BarChartKontraksi',
  components: { BarChartGenerator },
  props: {
    partographId: String,
    datetimeStart: String,    
    chartId: {
      type: String,
      default: 'bar-chart'
    },
    datasetIdKey: {
      type: String,
      default: 'label'
    },
    width: {
      type: Number,
      default: 400
    },
    cssClasses: {
      default: '',
      type: String
    },
    styles: {
      type: Object,
      default: () => {}
    },
    plugins: {
      type: Array,
      default: () => []
    }
    // width: { type: [Number, String], default: 600 },
    // height: { type: [Number, String], default: 300 },
    // labels: { type: Array, default: () => [] },
    // data: { type: Array, default: () => [] }
  },
  data() {
    return {
      chartData: {
        labels: [
                    "08:00", "08:30", "09:00", "09:30",
                    "10:00", "10:30", "11:00", "11:30",
                    "12:00", "12:30", "13:00", "13:30",
                    "14:00", "14:30", "15:00", "15:30"
                ],
        datasets: [
          {
            label: 'Kontraksi',
            backgroundColor: (context) => {
              const durations = context.dataset.durations || [];
              const duration = durations[context.dataIndex] || 0;
              if (duration < 20) return '#fff'; // empty (white)
              if (duration <= 40) {
                const ctx = context.chart.ctx;
                const patternCanvas = document.createElement('canvas');
                patternCanvas.width = patternCanvas.height = 8;
                const pctx = patternCanvas.getContext('2d');
                pctx.strokeStyle = '#4F8EF7';
                pctx.lineWidth = 2;
                pctx.beginPath();
                pctx.moveTo(0, 8);
                pctx.lineTo(8, 0);
                pctx.stroke();
                return ctx.createPattern(patternCanvas, 'repeat');
              }
              return '#4F8EF7'; // solid
            },
            borderColor: '#1B4F72',
            borderWidth: 1,
            data: [/* ... */]
          }
        ]
      },
      chartOptions: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: (context) => {
                 const kontraksi = context.parsed.y;
                 const duration = (this._alignedDuration && this._alignedDuration[context.dataIndex]) || '-';
                 return [
                     `Kontraksi: ${kontraksi}`,
                     `Durasi: ${duration} detik`
                 ];
              }
            }
          }
        },
        scales: {
          y: {
            // beginAtZero: true,
            min: 0,
            max: 5,
            ticks: {
              maxTicksLimit: 6
            },
          },
        }
      }
    }
  },
  computed: {
    computedChartData() {
      return {
        labels: this.chartData.labels,
        datasets: [
          {
            ...this.chartData.datasets[0],
            data: this.chartData.datasets[0].data,
            durations: this.chartData.datasets[0].durations,
          }
        ]
        
      }
    }
  },
  methods: {
    handleBarClick(event) {
      const chart = this.$refs.barChartRef?.$data?._chart || this.$refs.barChartRef?.chart;
      if (!chart) return;
      const points = chart.getElementsAtEventForMode(event, 'nearest', { intersect: true }, true);
      if (points.length) {
        const idx = points[0].index;
        const label = this.chartData.labels[idx];
        const contraction = this.chartData.datasets[0].data[idx];
        const duration = this.chartData.datasets[0].durations ? this.chartData.datasets[0].durations[idx] : null;
        this.$emit('bar-clicked', { label, contraction, duration });
      }
    },
    updateChart() {
      if (this.$refs.barChartRef && this.$refs.barChartRef.renderChart) {
        // console.log('Updating chart with data:', this.computedChartData);
        this.$refs.barChartRef.renderChart(this.computedChartData, this.chartOptions);
      }
    },
    async getLabels(){
       // Calculate max_time: datetimeStart + 16 hours
      const start = new Date(this.datetimeStart)
      const max = new Date(start.getTime() + 16 * 60 * 60 * 1000)
      const pad = n => n.toString().padStart(2, '0')
      const max_time = `${max.getFullYear()}-${pad(max.getMonth() + 1)}-${pad(max.getDate())} ${pad(max.getHours())}:${pad(max.getMinutes())}:00`
      // console.log('max_time: ', max_time)
      try {
        const labels = await this.$store.dispatch('partograph/fetchHours', {
          partographId: this.partographId,
          addition_time: 30,
          max_time
        })
        // console.log('labels: ', labels)
        const formattedLabels = []
        for (let label of labels.data) {
          const timePart = label.split(' ')[1]?.slice(0,5)
          formattedLabels.push(timePart)
        }
        this.chartData.labels = formattedLabels
        this.$emit('labels-updated', formattedLabels) 
        // console.log(this.chartData.labels)

      } catch (e) {
        // handle error
      }
    },
    async getChartData(){
      // console.log('getChartData called')
      try {
        const data = await this.$store.dispatch('partograph/getPartographDetail', {
          partographId: this.partographId
        })

        const allContractions = []
        // const allContractionDurations = []
        for (let item of data.data) {
            // console.log('item: ', item)
          if (Array.isArray(item.contraction)) {
            allContractions.push(...item.contraction)
          };
        }
        // console.log('allContractions: ', allContractions)

        const contractionMap = {}
        for (let obj of allContractions) {
          const time = obj.observation_hours?.slice(0,5);
          contractionMap[time] = obj.contraction;
        }

        const alignedContraction = this.chartData.labels.map(label =>
            contractionMap[label] !== undefined ? contractionMap[label] : null
            );

        const durationMap = {};
        for (let obj of allContractions) {
        const time = obj.observation_hours?.slice(0, 5);
        durationMap[time] = obj.duration;
        }
        const alignedDuration = this.chartData.labels.map(label =>
        durationMap[label] !== undefined ? durationMap[label] : null
        );
        this._alignedDuration = alignedDuration; // Store for tooltip access
        // console.log('alignedDuration: ', alignedDuration)
        // console.log('alignedContraction: ', alignedContraction)
        this.chartData.datasets[0].data = alignedContraction;
        this.chartData.datasets[0].durations = alignedDuration;

      } catch (e) {
        // handle error
      }
    },
  },
  mounted() {
    this.updateChart();
    this.$nextTick(() => {
      const canvas = this.$el.querySelector('canvas');
      if (canvas) {
        canvas.addEventListener('click', this.handleBarClick);
      }
    });
  },
  watch: {
    computedChartData: {
      handler() {
        this.updateChart();
      },
      deep: true
    },
    "partographId": {
      immediate: true,
      handler() {
         if (this.partographId && this.datetimeStart) {
          this.getLabels()
          this.getChartData()
        }
      }
    },
    datetimeStart(newVal) {
      if (this.partographId && newVal) {
        this.getLabels()
      }
    }
  }
//   watch: {
//     labels(newLabels) {
//       this.chartData.labels = newLabels;
//     },
//     data(newData) {
//       this.chartData.datasets[0].data = newData;
//     }
//   }
}
</script>