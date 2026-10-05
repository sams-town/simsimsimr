<template>
    <div>
        <div class="d-flex">
			<div class="col-lg-10 mx-auto">
				<div class="card">
					<div class="card-header bg-primary text-white text-capitalize">mata</div>
					<div class="card-body bg-c-info d-flex">
						<div class="col-12 mx-auto px-0 px-lg-3">
							<div class="bg-white rounded">
								<div style="overflow: hidden; background-color: #eaebef;">
									<div id="mata-screen" class="mata-input"></div>
								</div>
							</div>
							<div class="mt-3 bg-white">
								<div class="col p-2">
									<div class="row mx-0">
										<a class="btn col d-flex justify-content-center mata-color-picker"
											data-toggle="pill">
											<div class="rounded-circle box my-auto color-picker"
												style="background-color: #0F0E14;"></div>
										</a>
										<a class="btn col d-flex justify-content-center mata-color-picker"
											data-toggle="pill">
											<div class="rounded-circle box color-picker" style="background-color: #EB5757;">
											</div>
										</a>
										<a class="btn col d-flex justify-content-center mata-color-picker"
											data-toggle="pill">
											<div class="rounded-circle box color-picker" style="background-color: #EC98B5;">
											</div>
										</a>
										<a class="btn col d-flex justify-content-center mata-color-picker"
											data-toggle="pill">
											<div class="rounded-circle box color-picker" style="background-color: #F2994A;">
											</div>
										</a>
										<a class="btn col d-flex justify-content-center mata-color-picker"
											data-toggle="pill">
											<div class="rounded-circle box color-picker" style="background-color: #6FCF97;">
											</div>
										</a>
										<a class="btn col d-flex justify-content-center mata-color-picker"
											data-toggle="pill">
											<div class="rounded-circle box color-picker" style="background-color: #27AE60;">
											</div>
										</a>
										<a class="btn col d-flex justify-content-center mata-color-picker"
											data-toggle="pill">
											<div class="rounded-circle box color-picker" style="background-color: #219653;">
											</div>
										</a>
										<a class="btn col d-flex justify-content-center mata-color-picker"
											data-toggle="pill">
											<div class="rounded-circle box color-picker" style="background-color: #00EDF5;">
											</div>
										</a>
										<a class="btn col d-flex justify-content-center mata-color-picker"
											data-toggle="pill">
											<div class="rounded-circle box color-picker" style="background-color: #8901EB;">
											</div>
										</a>
										<a class="btn col d-flex justify-content-center mata-color-picker"
											data-toggle="pill">
											<div class="rounded-circle box color-picker" style="background-color: #840083;">
											</div>
										</a>
										<a class="btn col d-flex justify-content-center mata-color-picker"
											data-toggle="pill">
											<div class="rounded-circle box color-picker" style="background-color: #FA01F8;">
											</div>
										</a>
										<a class="btn col d-flex justify-content-center mata-color-picker"
											data-toggle="pill">
											<div class="rounded-circle box color-picker" style="background-color: #990104;">
											</div>
										</a>
										<a class="btn col d-flex justify-content-center mata-color-picker"
											data-toggle="pill">
											<div class="rounded-circle box color-picker" style="background-color: #737278;">
											</div>
										</a>
									</div>
								</div>
								<div class="row mx-0 p-2">
									<div class="col-12 col-lg-6 px-0">
										<div class="row mx-0">
											<a class="btn d-flex justify-content-center" id="mata-pointer-size-1">
												<img src="@/@core/assets/icons/canvas/brush.svg" width="20px" alt="brush">
												<p class="mb-0">1</p>
											</a>
											<a class="btn d-flex justify-content-center" id="mata-pointer-size-2">
												<img src="@/@core/assets/icons/canvas/brush.svg" width="20px" alt="brush">
												<p class="mb-0">2</p>
											</a>
											<a class="btn d-flex justify-content-center" id="mata-pointer-size-3">
												<img src="@/@core/assets/icons/canvas/brush.svg" width="20px" alt="brush">
												<p class="mb-0">3</p>
											</a>
											<a class="btn d-flex justify-content-center" id="mata-pointer-size-4">
												<img src="@/@core/assets/icons/canvas/brush.svg" width="20px" alt="brush">
												<p class="mb-0">4</p>
											</a>
											<a class=" btn d-flex justify-content-center" id="mata-pointer-size-5">
												<img src="@/@core/assets/icons/canvas/brush.svg" width="20px" alt="brush">
												<p class="mb-0">5</p>
											</a>
										</div>
									</div>
									<div class="col-12 col-lg-4 mt-3 mt-lg-0 px-0">
										<div class="d-flex">
											<div class="col-lg px-0 justify-content-lg-center d-flex">
												<a class="px-3 btn" id="mata-editor-brush" data-toggle="pill"><img
														src="@/@core/assets/icons/canvas/pencil.svg" alt="Pencil"></a>
												<a class="btn px-3 btn" id="mata-editor-text" data-toggle="pill"><img
														src="@/@core/assets/icons/canvas/text.svg" alt="Text"></a>
												<a class="btn px-3 btn" id="mata-editor-eraser" data-toggle="pill"><img
														src="@/@core/assets/icons/canvas/eraser.svg" alt="Eraser"></a>
											</div>
										</div>
									</div>
									<div class="col d-flex justify-content-end">
										<b-button
											variant="success"
											class="mr-2"
											@click="saveCanvas"
										>
											<b-icon icon="check-circle"></b-icon>
											Simpan
										</b-button>

										<b-button
											variant="danger"
											id="mata-editor-clear"
										>
											<img src="@/@core/assets/icons/canvas/trash.svg" width="20px"
												alt="trash-icon">
											Reset
										</b-button>
									</div>
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
			canDraw: true,
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