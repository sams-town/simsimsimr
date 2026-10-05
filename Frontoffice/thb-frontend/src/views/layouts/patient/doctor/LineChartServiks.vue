<template>
  <LineChartGenerator
    ref="chartInstance"
    :chart-options="chartOptions"
    :chart-data="chartData"
    :plugins="plugins"
    style="height: 100%; width: 100%;"
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

// Add this plugin definition in your <script> section, outside export default
const waspadaLinePlugin = {
  id: 'waspadaLine',
  afterDraw(chart) {
    const { ctx, chartArea, scales } = chart;
    if (!scales.x || !scales.y) return;

    // First line: from (1,4) to (6,10)
    const x1 = scales.x.getPixelForValue(chart.data.labels[0]);
    const y1 = scales.y.getPixelForValue(4);
    const x2 = scales.x.getPixelForValue(chart.data.labels[12]);
    const y2 = scales.y.getPixelForValue(10);

    // Draw the black line
    ctx.save();
    ctx.beginPath();
    ctx.moveTo(x1, y1);
    ctx.lineTo(x2, y2);
    ctx.strokeStyle = 'black';
    ctx.lineWidth = 2;
    ctx.stroke();
    ctx.restore();

    // Draw the "WASPADA" text above the line
    ctx.save();
    ctx.font = '24px Arial';
    ctx.fillStyle = 'black';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'bottom';
    // Place text above the midpoint of the line
    const midX = (x1 + x2) / 2;
    const midY = (y1 + y2) / 2 - 20;
    ctx.fillText('WASPADA', midX, midY);
    ctx.restore();

    // Second line: from (8,4) to (20,10)
    const x3 = scales.x.getPixelForValue(chart.data.labels[8]);
    const y3 = scales.y.getPixelForValue(4);
    const x4 = scales.x.getPixelForValue(chart.data.labels[20]);
    const y4 = scales.y.getPixelForValue(10);

    ctx.save();
    ctx.beginPath();
    ctx.moveTo(x3, y3);
    ctx.lineTo(x4, y4);
    ctx.strokeStyle = 'black';
    ctx.lineWidth = 2;
    ctx.stroke();
    ctx.restore();

    // "BERTINDAK" above the second line
    ctx.save();
    ctx.font = '24px Arial';
    ctx.fillStyle = 'black';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'bottom';
    const midX2 = (x3 + x4) / 2;
    const midY2 = (y3 + y4) / 2 - 20;
    ctx.fillText('BERTINDAK', midX2, midY2);
    ctx.restore();

  }
};

export default {
    name: 'LineChartServiks',
    components: {
        LineChartGenerator
    },
    props: {
        chartData: { type: Object, required: true },
        
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
            default: () => [waspadaLinePlugin]
        }
    },
    data(){
        return {
            chartOptions: {
            scales: {
                y: {
                min: 0,
                max: 10,
                ticks: {
                    // stepSize: 50,
                    maxTicksLimit: 20
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
                    const cervical_dilation = datasets[0].data[idx] ?? '-';
                    const head_descent = datasets[1].data[idx] ?? '-';
                    return [
                    `Pembukaan Serviks: ${cervical_dilation} cm`,
                    `Penurunan Kepala: ${head_descent} cm`
                    ];
                }
                }
            }
            }
        },
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
                // console.log('Canvas element:', canvas);
                canvas.addEventListener('click', this.handleChartClick);
                } else if (retries > 0) {
                setTimeout(() => this.tryAttachCanvasClick(retries - 1), 100);
                } else {
                console.warn('Canvas element not found after retries');
                }
            });
        },
        handleChartClick(event) {
            console.log('Chart clicked:', event);
            const chart = this.$refs.chartInstance.chart || this.$refs.chartInstance.$data?._chart || this.$refs.chartInstance.$chart;
            // console.log('Chart instance:', chart); // <-- Add this
            if (!chart) {
                // console.log('Chart instance not found');
                return;
            }
            const points = chart.getElementsAtEventForMode(
            event,
            'nearest',
            { intersect: true },
            true
            );
            // console.log('Points:', points); // <-- Add this
            if (points.length) {
                const point = points[0];
                const index = point.index;
                const label = this.chartData.labels[index];
                const cervicalDilation = this.chartData.datasets[0].data[index];
                const headDescent = this.chartData.datasets[1]?.data[index];
                // console.log('Point clicked:', { index, label, value }); // <-- Add this
                this.$emit('point-clicked', {
                    index,
                    label,
                    cervicalDilation,
                    headDescent
                });
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
    }

}

</script>