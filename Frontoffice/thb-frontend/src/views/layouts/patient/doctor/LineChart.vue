<template>
  <LineChartGenerator
    ref="chartInstance"
    :chart-options="chartOptions"
    :chart-data="chartData"
    :chart-id="chartId"
    :dataset-id-key="datasetIdKey"
    :plugins="plugins"
    style="height: 100%; width: 100%;"
    :width="width"
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
import { watch } from 'vue'

ChartJS.register(Title, Tooltip, Legend, LineElement, CategoryScale, LinearScale, PointElement)
export default {
  name: 'LineChart',
  components: {
    LineChartGenerator
  },
  props: {
    chartData: {
        type: Object,
        required: true
      },  
    chartId: {
      type: String,
      default: 'line-chart'
    },
    datasetIdKey: {
      type: String,
      default: 'label'
    },
    width: {
      type: Number,
      default: 400
    }
  },
  data() {
    return {
      chartOptions: {
        scales: {
            y: {
              min: 80,
              max: 200,
              ticks: {
                  // stepSize: 50,
                  maxTicksLimit: 14
              }
            }
        },
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          },
        }
      },
      plugins: []
    }
  },
  mounted() {
    this.tryAttachCanvasClick();
  },
  methods: {
    tryAttachCanvasClick(retries = 10) {
      this.$nextTick(() => {
        const canvas = this.$refs.chartInstance?.$el?.querySelector('canvas');
        if (canvas) {
          canvas.addEventListener('click', this.handleChartClick);
        } else if (retries > 0) {
          setTimeout(() => this.tryAttachCanvasClick(retries - 1), 100);
        }
      });
    },
    handleChartClick(event) {
      const chart = this.$refs.chartInstance.chart || this.$refs.chartInstance.$data?._chart || this.$refs.chartInstance.$chart;
      if (!chart) return;
      const points = chart.getElementsAtEventForMode(
        event,
        'nearest',
        { intersect: true },
        true
      );
      if (points.length) {
        const point = points[0];
        const index = point.index;
        const label = this.chartData.labels[index];
        const value = this.chartData.datasets[0].data[index];
        if (value !== null && value !== undefined) {
          this.$emit('point-clicked', { index, label, value });
        }
      }
    }
  },
  watch: {
    chartData: {
      handler() {
        this.tryAttachCanvasClick();
      },
      deep: true,
      immediate: true
    }
  },
  beforeDestroy() {
    const canvas = this.$refs.chartInstance?.$el?.querySelector('canvas');
    if (canvas) {
      canvas.removeEventListener('click', this.handleChartClick);
    }
  },
}
</script>