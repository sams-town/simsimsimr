<template>
  <LineChartGenerator
    ref="chartRef"
    :chart-options="chartOptions"
    :chart-data="chartData"
    :plugins="plugins"
    style="height: 300px; width: 100%;"
  />
</template>

<script>
import { Line as LineChartGenerator } from 'vue-chartjs/legacy'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  LineElement,
  CategoryScale,
  LinearScale,
  PointElement
} from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, LineElement, CategoryScale, LinearScale, PointElement)

export default {
  name: 'LineChartTensiNadi',
  components: {
    LineChartGenerator
  },
  props: {
    chartData: { type: Object, required: true }
  },
  data() {
    return {
      chartOptions: {
        scales: {
            y: {
              min: 60,
              max: 180,
              ticks: {
                  // stepSize: 50,
                  maxTicksLimit: 13
              }
            }
        },
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          },
          tooltip: {
            callbacks: {
              label: (context) => {
                const datasets = context.chart.data.datasets;
                const idx = context.dataIndex;
                const systolic = datasets[0].data[idx] ?? '-';
                const diastolic = datasets[1].data[idx] ?? '-';
                const pulse = datasets[2].data[idx] ?? '-';
                return [
                  `Sistolik: ${systolic}`,
                  `Diastolik: ${diastolic}`,
                  `Nadi: ${pulse} bpm`
                ];
              }
            }
          },
        }
      },
      plugins: [
        {
          id: 'verticalConnectorLines',
          afterDatasetsDraw: (chart) => {
            const { ctx, data, scales } = chart;
            if (!data || !data.datasets || data.datasets.length < 3) return;

            const dataset0 = data.datasets[0].data; // Sistolik
            const dataset1 = data.datasets[1].data; // Diastolik
            const dataset2 = data.datasets[2].data; // Nadi

            data.labels.forEach((label, i) => {
              const x = scales.x.getPixelForValue(label);

              const y0 = scales.y.getPixelForValue(dataset0[i]);
              const y1 = scales.y.getPixelForValue(dataset1[i]);
              const y2 = scales.y.getPixelForValue(dataset2[i]);

              const ys = [y0, y1, y2].filter(y => typeof y === 'number' && !isNaN(y));
              if (ys.length < 2) return;
              const minY = Math.min(...ys);
              const maxY = Math.max(...ys);

              ctx.save();
              ctx.beginPath();
              ctx.strokeStyle = 'rgba(136,136,136,0.35)';
              ctx.lineWidth = 2;
              ctx.moveTo(x, minY);
              ctx.lineTo(x, maxY);
              ctx.stroke();
              ctx.restore();
            });
          }
        }
      ]
    }
  },

  mounted() {
    this.tryAttachCanvasClick();
  },

  beforeDestroy() {
    const chartComponent = this.$refs.chartRef;
    const chart = chartComponent && chartComponent.$data?._chart;
    if (chart && chart.canvas) {
      chart.canvas.removeEventListener('click', this.handleChartClick);
    }
  },

  methods: {
    tryAttachCanvasClick(retries = 10) {
      this.$nextTick(() => {
        const canvas = this.$refs.chartRef?.$el?.querySelector('canvas');
        if (canvas) {
          canvas.addEventListener('click', this.handleChartClick);
        } else if (retries > 0) {
          setTimeout(() => this.tryAttachCanvasClick(retries - 1), 100);
        }
      });
    },

    handleChartClick(event) {
      const chartComponent = this.$refs.chartRef;
      const chart = chartComponent && chartComponent.$data?._chart;
      if (!chart) return;
      const points = chart.getElementsAtEventForMode(event, 'nearest', { intersect: true }, true);
      if (points.length) {
        const idx = points[0].index;
        const label = this.chartData.labels[idx];
        const systolic = this.chartData.datasets[0].data[idx];
        const diastolic = this.chartData.datasets[1].data[idx];
        const pulse = this.chartData.datasets[2].data[idx];
        this.$emit('point-clicked', { label, systolic, diastolic, pulse });
      }
    },
  },


  watch: {
    chartData: {
      handler() {
        this.tryAttachCanvasClick();
      },
      deep: true,
      immediate: true
    }
  }
}
</script>