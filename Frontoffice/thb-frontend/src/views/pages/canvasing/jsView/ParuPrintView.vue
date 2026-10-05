<template>
    <div>
        <div class="card">
            <div class="card-header bg-primary text-white">Paru-paru</div>
            <div class="card-body bg-c-info d-flex">
                <div class="col-12 mx-auto px-0 px-lg-3">
                    <div class="bg-white rounded">
                        <div style="overflow: hidden; background-color: #eaebef;">
                            <div id="canvas-screen" class="canvas-input"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import $ from 'jquery'
import '@/views/pages/canvasing/jsCanvas/szparucanvas.js'
export default {
    data() {
        return {
            paruCanvas: null,
            isLoading: false
        }
    },
    props: {
        drawing: {
            type: String,
            default: null
        }
    },
    methods: {
        saveCanvas() {
            const drawing = this.paruCanvas.szParuCanvas('exportData');
            this.$emit('save', drawing);
        }
    },
    watch: {
        drawing(val) {
            if (!val || !this.paruCanvas) return;

            this.$nextTick(() => {
                this.paruCanvas.szParuCanvas(
                    'importData',
                    JSON.parse(val)
                );
            });
        }
    },
    mounted() {
        // Canvas Paru
        this.paruCanvas = $('#canvas-screen.canvas-input')
        .szParuCanvas({
            responsive: true,
            canDraw: false,
        });

        if (this.drawing) {
            setTimeout(() => {
                this.paruCanvas.szParuCanvas(
                    'importData',
                    JSON.parse(this.drawing)
                );
            }, 200);
        }
        const $canvas = this.paruCanvas;

        $('#pointer-size-1').click(function (e) {
            e.preventDefault();
            $canvas.szParuCanvas('setPointSize', 2);
        });

        $('#pointer-size-2').click(function () {
            $canvas.szParuCanvas('setPointSize', 5);
        });

        $('#pointer-size-3').click(function () {
            $canvas.szParuCanvas('setPointSize', 10);
        });

        $('#pointer-size-4').click(function () {
            $canvas.szParuCanvas('setPointSize', 15);
        });

        $('#pointer-size-5').click(function () {
            $canvas.szParuCanvas('setPointSize', 20);
        });

        $('#editor-brush').click(function () {
            $canvas.szParuCanvas('setMode', 'brush');
        });

        $('#editor-text').click(function () {
            $canvas.szParuCanvas('setMode', 'text');
        });

        $('#editor-eraser').click(function () {
            $canvas.szParuCanvas('setMode', 'eraser');
        });

        $('.editor-color-picker').click(function (e) {
            e.preventDefault();
            var color = $(this).find('.color-picker').css('background-color');
            $canvas.szParuCanvas('setColor', color);
            $('#canvas-screen-current-color').css('background-color', color);
        });

        $('#editor-clear').click(function () {
            $canvas.szParuCanvas('clearCanvas');
            confirm('Hapus semua gambar Paru-paru?');
            return false;
        });
    },
}
</script>

<style scoped>
canvas {
    border: 0;
    overflow: hidden;
    width: 80%;
}

a.btn {
    background-color: whitesmoke;
    color: black;
}

a.btn:hover {
    background-color: #efdffc;
    color: #0F0E14;
}

a.btn:active {
    background-color: #d3a7ff;
    color: #0F0E14;
}

.box {
    width: 25px;
    height: 25px;
    aspect-ratio: 1/1;
}
</style>