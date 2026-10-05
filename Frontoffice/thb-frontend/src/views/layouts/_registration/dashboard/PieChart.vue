<template>
    <Pie
      :chart-options="chartOptions"
      :chart-data="chartData"
      :chart-id="chartId"
      :dataset-id-key="datasetIdKey"
      :plugins="plugins"
      :css-classes="cssClasses"
      :styles="styles"
      :width="width"
      :height="height"
    />
  </template>
  
  <script>
  import { Pie } from 'vue-chartjs/legacy'
  
  import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    ArcElement,
    CategoryScale
  } from 'chart.js'
  
  ChartJS.register(Title, Tooltip, Legend, ArcElement, CategoryScale)
  
  export default {
    name: 'PieChart',
    components: {
      Pie
    },
    props: {
      chartId: {
        type: String,
        default: 'pie-chart'
      },
      datasetIdKey: {
        type: String,
        default: 'label'
      },
      width: {
        type: Number,
        default: 150
      },
      height: {
        type: Number,
        default: 150
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
          labels: ['Pasien Jaminan', 'Pasien Tunai'],
          datasets: [
            {
              backgroundColor: ['#F765A3', '#A155B9'],
              data: [25, 75]
            }
          ]
        },
        chartOptions: {
          responsive: true,
          maintainAspectRatio: false,
          plugins:{
            legend:{
              position: 'bottom',
              align: 'center',
              labels: {
                font:{
                  size: 10
                }
              }
            }
          }
        }
      }
    }
  }
  </script>
  