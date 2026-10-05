<template>
    <div>
        <div class="d-flex">
			<div class="col-lg-10 mx-auto">
				<div class="card">
					<div class="card-body bg-c-info d-flex">
						<div class="col-12 mx-auto px-0 px-lg-3">
							<div class="bg-white rounded">
								<div style="overflow: hidden; background-color: #eaebef;">
									<div id="mata-screen" class="mata-input"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- /end:card -->
			</div>
		</div>
		<!-- /mata -->
    </div>
</template>

<script>
import $ from 'jquery'
import '@/views/pages/canvasing/jsCanvas/szmatacanvas.js'
export default {
    data() {
        return {
            mataCanvas: null,
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
            const drawing = this.mataCanvas.szMataCanvas('exportData');
            this.$emit('save', drawing);
        }
    },
    watch: {
        drawing(val) {
            if (!val || !this.mataCanvas) return;

            this.$nextTick(() => {
                this.mataCanvas.szMataCanvas(
                    'importData',
                    JSON.parse(val)
                );
            });
        }
    },
    mounted() {
        // Canvas mata
		this.mataCanvas = $('#mata-screen.mata-input').szMataCanvas({
			responsive: true,
			canDraw: false,
		});

		if (this.drawing) {
			setTimeout(() => {
				this.mataCanvas.szMataCanvas(
					'importData',
					JSON.parse(this.drawing)
				);
			}, 200);
		}
		const $mata = this.mataCanvas;

		$('#mata-pointer-size-1').click(function (e) {
			e.preventDefault();
			$mata.szMataCanvas('setPointSize', 2);
		});

		$('#mata-pointer-size-2').click(function () {
			$mata.szMataCanvas('setPointSize', 5);
		});

		$('#mata-pointer-size-3').click(function () {
			$mata.szMataCanvas('setPointSize', 10);
		});

		$('#mata-pointer-size-4').click(function () {
			$mata.szMataCanvas('setPointSize', 15);
		});

		$('#mata-pointer-size-5').click(function () {
			$mata.szMataCanvas('setPointSize', 20);
		});

		$('#mata-editor-brush').click(function () {
			$mata.szMataCanvas('setMode', 'brush');
		});

		$('#mata-editor-text').click(function () {
			$mata.szMataCanvas('setMode', 'text');
		});

		$('#mata-editor-eraser').click(function () {
			$mata.szMataCanvas('setMode', 'eraser');
		});

		$('.mata-color-picker').click(function (e) {
			e.preventDefault();
			var color = $(this).find('.color-picker').css('background-color');
			$mata.szMataCanvas('setColor', color);
			$('#canvas-screen-current-color').css('background-color', color);
		});

		$('#mata-editor-clear').click(function () {
			$mata.szMataCanvas('clearCanvas');
			confirm('Hapus semua gambar mata?');
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