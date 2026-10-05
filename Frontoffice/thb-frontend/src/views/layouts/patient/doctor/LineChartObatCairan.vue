<template>
  <LineChartGenerator
    :chart-options="chartOptions"
    :chart-data="chartData"
    :chart-id="chartId"
    :dataset-id-key="datasetIdKey"
    :plugins="plugins"
    :css-classes="cssClasses"
    style="height: 150px;"
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

ChartJS.register(Title, Tooltip, Legend, LineElement, CategoryScale, LinearScale, PointElement)
export default {
  name: 'LineChartObatCairan',
  components: {
    LineChartGenerator
  },
  props: {
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
  },
  data() {
    return {
      chartData: {
        labels:  [
          "10:00",
          "10:30",
          "11:00",
          "11:30",
          "12:00",
          "12:30",
          "13:00",
          "13:30",
          "14:00",
          "14:30",
          "15:00",
          "15:30",
          "16:00",
          "16:30",
          "17:00",
          "17:30",
          "18:00",
          "18:30",
          "19:00",
          "19:30",
          "20:00",
          "20:30",
          "21:00",
          "21:30",
          "22:00",
          "22:30",
          "23:00",
          "23:30",
          "00:00"
        ],
        datasets: [
          {
            label: 'Jumlah resep',
            backgroundColor: '#FBA2F7',
            borderColor: "rgb(255, 99, 132)",
            data: [],
            trendlineLinear: {
              colorMin: "gray",
              colorMax: "gray",
              lineStyle: "solid",
              width: 2,
              projection: false
            }
          }
        ]
      },
      chartOptions: {
        scales: {
            y: {
              min: 1,
              max: 2,
              ticks: {
                  // stepSize: 50,
                  maxTicksLimit: 2
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
      }
    }
  }
}
</script>