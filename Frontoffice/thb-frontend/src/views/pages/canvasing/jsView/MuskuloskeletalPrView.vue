<template>
	<div>
		<div class="card">
			<div class="card-header bg-primary text-white text-capitalize">muskuloskeletal Perempuan</div>
			<div class="card-body bg-c-info d-flex">
				<div class="col-12 mx-auto px-0 px-lg-3">
					<div class="bg-white rounded">
						<div style="overflow: hidden; background-color: #eaebef;">
							<div id="musPr-screen" class="musPr-input"></div>
						</div>
					</div>
					<div class="mt-3 bg-white">
						<div class="col p-2">
							<div class="row mx-0">
								<a class="btn col d-flex justify-content-center musPr-color-picker" data-toggle="pill">
									<div class="rounded-circle box my-auto color-picker" style="background-color: #0F0E14;">
									</div>
								</a>
								<a class="btn col d-flex justify-content-center musPr-color-picker" data-toggle="pill">
									<div class="rounded-circle box color-picker" style="background-color: #EB5757;">
									</div>
								</a>
								<a class="btn col d-flex justify-content-center musPr-color-picker" data-toggle="pill">
									<div class="rounded-circle box color-picker" style="background-color: #EC98B5;">
									</div>
								</a>
								<a class="btn col d-flex justify-content-center musPr-color-picker" data-toggle="pill">
									<div class="rounded-circle box color-picker" style="background-color: #F2994A;">
									</div>
								</a>
								<a class="btn col d-flex justify-content-center musPr-color-picker" data-toggle="pill">
									<div class="rounded-circle box color-picker" style="background-color: #6FCF97;">
									</div>
								</a>
								<a class="btn col d-flex justify-content-center musPr-color-picker" data-toggle="pill">
									<div class="rounded-circle box color-picker" style="background-color: #27AE60;">
									</div>
								</a>
								<a class="btn col d-flex justify-content-center musPr-color-picker" data-toggle="pill">
									<div class="rounded-circle box color-picker" style="background-color: #219653;">
									</div>
								</a>
								<a class="btn col d-flex justify-content-center musPr-color-picker" data-toggle="pill">
									<div class="rounded-circle box color-picker" style="background-color: #00EDF5;">
									</div>
								</a>
								<a class="btn col d-flex justify-content-center musPr-color-picker" data-toggle="pill">
									<div class="rounded-circle box color-picker" style="background-color: #8901EB;">
									</div>
								</a>
								<a class="btn col d-flex justify-content-center musPr-color-picker" data-toggle="pill">
									<div class="rounded-circle box color-picker" style="background-color: #840083;">
									</div>
								</a>
								<a class="btn col d-flex justify-content-center musPr-color-picker" data-toggle="pill">
									<div class="rounded-circle box color-picker" style="background-color: #FA01F8;">
									</div>
								</a>
								<a class="btn col d-flex justify-content-center musPr-color-picker" data-toggle="pill">
									<div class="rounded-circle box color-picker" style="background-color: #990104;">
									</div>
								</a>
								<a class="btn col d-flex justify-content-center musPr-color-picker" data-toggle="pill">
									<div class="rounded-circle box color-picker" style="background-color: #737278;">
									</div>
								</a>
							</div>
						</div>
						<div class="row mx-0 p-2">
							<div class="col-12 col-lg-6 px-0">
								<div class="row mx-0">
									<a class="btn d-flex justify-content-center" id="musPr-pointer-size-1">
										<img src="@/@core/assets/icons/canvas/brush.svg" width="20px" alt="brush">
										<p class="mb-0">1</p>
									</a>
									<a class="btn d-flex justify-content-center" id="musPr-pointer-size-2">
										<img src="@/@core/assets/icons/canvas/brush.svg" width="20px" alt="brush">
										<p class="mb-0">2</p>
									</a>
									<a class="btn d-flex justify-content-center" id="musPr-pointer-size-3">
										<img src="@/@core/assets/icons/canvas/brush.svg" width="20px" alt="brush">
										<p class="mb-0">3</p>
									</a>
									<a class="btn d-flex justify-content-center" id="musPr-pointer-size-4">
										<img src="@/@core/assets/icons/canvas/brush.svg" width="20px" alt="brush">
										<p class="mb-0">4</p>
									</a>
									<a class=" btn d-flex justify-content-center" id="musPr-pointer-size-5">
										<img src="@/@core/assets/icons/canvas/brush.svg" width="20px" alt="brush">
										<p class="mb-0">5</p>
									</a>
								</div>
							</div>
							<div class="col-12 col-lg-4 mt-3 mt-lg-0 px-0">
								<div class="d-flex">
									<div class="col-lg px-0 justify-content-lg-center d-flex">
										<a class="px-3 btn" id="musPr-editor-brush" data-toggle="pill"><img
												src="@/@core/assets/icons/canvas/pencil.svg" alt="Pencil"></a>
										<a class="btn px-3 btn" id="musPr-editor-text" data-toggle="pill"><img
												src="@/@core/assets/icons/canvas/text.svg" alt="Text"></a>
										<a class="btn px-3 btn" id="musPr-editor-eraser" data-toggle="pill"><img
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
									id="musPr-editor-clear"
								>
									<img src="@/@core/assets/icons/canvas/trash.svg" width="20px">
									Reset
								</b-button>

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import $ from 'jquery'
import '@/views/pages/canvasing/jsCanvas/szmuskuloskeletalprcanvas.js'
export default {
	data() {
        return {
            musCanvas: null,
            isLoading:false
        }
    },
	props:{
		drawing:{
			type:String,
			default:null
		}
	},
	methods: {
		saveCanvas(){
			const drawing = this.musCanvas.szMuskuloskeletalPrCanvas('exportData');
			this.$emit('save', drawing);
		}
	},
	watch: {
		drawing(val) {
			if (!val || !this.musCanvas) return;

			this.$nextTick(() => {
				this.musCanvas.szMuskuloskeletalPrCanvas(
					'importData',
					JSON.parse(val)
				);
			});
		}
	},
	mounted() {
		// Canvas musPr
		this.musCanvas = $('#musPr-screen.musPr-input')
		.szMuskuloskeletalPrCanvas({
			responsive: true,
			canDraw: true,
		});

		if (this.drawing) {
			setTimeout(() => {
				this.musCanvas.szMuskuloskeletalPrCanvas(
					'importData',
					JSON.parse(this.drawing)
				);
			}, 200);
		}
		const $musPr = this.musCanvas;
		$('#musPr-pointer-size-1').click(function (e) {
			e.preventDefault();
			$musPr.szMuskuloskeletalPrCanvas('setPointSize', 2);
		});

		$('#musPr-pointer-size-2').click(function () {
			$musPr.szMuskuloskeletalPrCanvas('setPointSize', 5);
		});

		$('#musPr-pointer-size-3').click(function () {
			$musPr.szMuskuloskeletalPrCanvas('setPointSize', 10);
		});

		$('#musPr-pointer-size-4').click(function () {
			$musPr.szMuskuloskeletalPrCanvas('setPointSize', 15);
		});

		$('#musPr-pointer-size-5').click(function () {
			$musPr.szMuskuloskeletalPrCanvas('setPointSize', 20);
		});

		$('#musPr-editor-brush').click(function () {
			$musPr.szMuskuloskeletalPrCanvas('setMode', 'brush');
		});

		$('#musPr-editor-text').click(function () {
			$musPr.szMuskuloskeletalPrCanvas('setMode', 'text');
		});

		$('#musPr-editor-eraser').click(function () {
			$musPr.szMuskuloskeletalPrCanvas('setMode', 'eraser');
		});

		$('.musPr-color-picker').click(function (e) {
			e.preventDefault();
			var color = $(this).find('.color-picker').css('background-color');
			$musPr.szMuskuloskeletalPrCanvas('setColor', color);
			$('#canvas-screen-current-color').css('background-color', color);
		});

		$('#musPr-editor-clear').click(function () {
			$musPr.szMuskuloskeletalPrCanvas('clearCanvas');
			confirm('Hapus semua gambar Muskuloskeletal Perempuan?');
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