<template>
	<div>
		<div class="card">
			<div class="card-body bg-c-info d-flex">
				<div class="col-12 mx-auto px-0 px-lg-3">
					<div class="bg-white rounded">
						<div style="overflow: hidden; background-color: #eaebef;">
							<div id="musLaki-screen" class="musLaki-input"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import $ from 'jquery'
import '@/views/pages/canvasing/jsCanvas/szmuskuloskeletallakicanvas.js'
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
			const drawing = this.musCanvas.szMuskuloskeletalLakiCanvas('exportData');
			this.$emit('save', drawing);
		}
	},
	watch: {
		drawing(val) {
			if (!val || !this.musCanvas) return;

			this.$nextTick(() => {
				this.musCanvas.szMuskuloskeletalLakiCanvas(
					'importData',
					JSON.parse(val)
				);
			});
		}
	},
	mounted() {
		// Canvas musLaki
		this.musCanvas = $('#musLaki-screen.musLaki-input')
		.szMuskuloskeletalLakiCanvas({
			responsive: true,
			canDraw: false,
		});

		if (this.drawing) {
			setTimeout(() => {
				this.musCanvas.szMuskuloskeletalLakiCanvas(
					'importData',
					JSON.parse(this.drawing)
				);
			}, 200);
		}
		const $musLaki = this.musCanvas;
		
		$('#musLaki-pointer-size-1').click(function (e) {
			e.preventDefault();
			$musLaki.szMuskuloskeletalLakiCanvas('setPointSize', 2);
		});

		$('#musLaki-pointer-size-2').click(function () {
			$musLaki.szMuskuloskeletalLakiCanvas('setPointSize', 5);
		});

		$('#musLaki-pointer-size-3').click(function () {
			$musLaki.szMuskuloskeletalLakiCanvas('setPointSize', 10);
		});

		$('#musLaki-pointer-size-4').click(function () {
			$musLaki.szMuskuloskeletalLakiCanvas('setPointSize', 15);
		});

		$('#musLaki-pointer-size-5').click(function () {
			$musLaki.szMuskuloskeletalLakiCanvas('setPointSize', 20);
		});

		$('#musLaki-editor-brush').click(function () {
			$musLaki.szMuskuloskeletalLakiCanvas('setMode', 'brush');
		});

		$('#musLaki-editor-text').click(function () {
			$musLaki.szMuskuloskeletalLakiCanvas('setMode', 'text');
		});

		$('#musLaki-editor-eraser').click(function () {
			$musLaki.szMuskuloskeletalLakiCanvas('setMode', 'eraser');
		});

		$('.musLaki-color-picker').click(function (e) {
			e.preventDefault();
			var color = $(this).find('.color-picker').css('background-color');
			$musLaki.szMuskuloskeletalLakiCanvas('setColor', color);
			$('#canvas-screen-current-color').css('background-color', color);
		});

		$('#musLaki-editor-clear').click(function () {
			$musLaki.szMuskuloskeletalLakiCanvas('clearCanvas');
			confirm('Hapus semua gambar Muskuloskeletal Laki-laki?');
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