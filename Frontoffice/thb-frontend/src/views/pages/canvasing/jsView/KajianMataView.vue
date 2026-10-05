<template>
	<div>
		<!-- START: Anterior -->
		<div class="d-flex">
			<div class="card col-12 col-lg-10 mt-5 mx-lg-auto px-0">
				<div class="card-header bg-primary text-white">Anterior</div>
				<div class="card-body bg-c-info d-flex px-0 px-lg-3">
					<div class="col-12 mx-auto">
						<div class="bg-white rounded">
							<div class="panel-body">
								<div id="anterior-eye" class="anterior-eye-input" style="width: 100%;"></div>
							</div>
						</div>
						<!-- START: input & buttons -->
						<div class="col mt-3">
							<p class="mb-0">Ukuran pena / penghapus / text:</p>
							<div class="panel-body row mx-0">
								<div class="col-12 col-lg-4">
									<div class="slider-container">
										<input type="range" id="anterior-eye-slider" min="2" max="50" step="2" value="2"
											class="form-range col px-0" style="outline-color:red" />
										<p class="mb-0 slider-value"><strong>2</strong></p>
									</div>
								</div>
								<div class="col-12 col-lg">
									<div class="d-flex flex-wrap">
										<div class="d-flex align-items-center">
											<b-button pill id="anterior-eye-brush" class="bg-purple">Pena</b-button>
										</div>
										<div class="d-flex align-items-center">
											<b-button pill id="anterior-eye-text" class="bg-info">Text</b-button>
										</div>
										<div>
											<span id="anterior-eye-current-color"
												style="border: 1px solid black; padding: 0 8px; margin: 4px; background-color: black; min-width: 10px; width: 10px; height: 8px"></span>
											<b-dropdown id="dropdown-dropup" dropup text="Warna" variant="white">
												<div class="d-flex">
													<div>
														<b-dropdown-item href="#"
															class="anterior-eye-color-picker text-dark text-decoration-none">
															<span class="color-picker"
																style="border: 1px solid black; padding: 0 8px; margin: 4px; background-color: red; min-width: 10px; width: 10px; height: 8px"></span>
															Merah
														</b-dropdown-item>
														<b-dropdown-item href="#"
															class="anterior-eye-color-picker text-dark text-decoration-none">
															<span class="color-picker"
																style="border: 1px solid black; padding: 0 8px; margin: 4px; background-color: pink; min-width: 10px; width: 10px; height: 8px"></span>
															Merah Muda
														</b-dropdown-item>
														<b-dropdown-item href="#"
															class="anterior-eye-color-picker text-dark text-decoration-none">
															<span class="color-picker"
																style="border: 1px solid black; padding: 0 8px; margin: 4px; background-color: orange; min-width: 10px; width: 10px; height: 8px"></span>
															Orange
														</b-dropdown-item>
														<b-dropdown-item href="#"
															class="anterior-eye-color-picker text-dark text-decoration-none">
															<span class="color-picker"
																style="border: 1px solid black; padding: 0 8px; margin: 4px; background-color: yellow; min-width: 10px; width: 10px; height: 8px"></span>
															Kuning
														</b-dropdown-item>
														<b-dropdown-item href="#"
															class="anterior-eye-color-picker text-dark text-decoration-none">
															<span class="color-picker"
																style="border: 1px solid black; padding: 0 8px; margin: 4px; background-color: lime; min-width: 10px; width: 10px; height: 8px"></span>
															Lime
														</b-dropdown-item>
														<b-dropdown-item href="#"
															class="anterior-eye-color-picker text-dark text-decoration-none">
															<span class="color-picker"
																style="border: 1px solid black; padding: 0 8px; margin: 4px; background-color: green; min-width: 10px; width: 10px; height: 8px"></span>
															Hijau
														</b-dropdown-item>
														<b-dropdown-item href="#"
															class="anterior-eye-color-picker text-dark text-decoration-none">
															<span class="color-picker"
																style="border: 1px solid black; padding: 0 8px; margin: 4px; background-color: cyan; min-width: 10px; width: 10px; height: 8px"></span>
															Cyan
														</b-dropdown-item>
													</div>
													<div>
														<b-dropdown-item href="#"
															class="anterior-eye-color-picker text-dark text-decoration-none">
															<span class="color-picker"
																style="border: 1px solid black; padding: 0 8px; margin: 4px; background-color: blue; min-width: 10px; width: 10px; height: 8px"></span>
															Biru
														</b-dropdown-item>
														<b-dropdown-item href="#"
															class="anterior-eye-color-picker text-dark text-decoration-none">
															<span class="color-picker"
																style="border: 1px solid black; padding: 0 8px; margin: 4px; background-color: purple; min-width: 10px; width: 10px; height: 8px"></span>
															Ungu
														</b-dropdown-item>
														<b-dropdown-item href="#"
															class="anterior-eye-color-picker text-dark text-decoration-none">
															<span class="color-picker"
																style="border: 1px solid black; padding: 0 8px; margin: 4px; background-color: magenta; min-width: 10px; width: 10px; height: 8px;"></span>
															Magenta
														</b-dropdown-item>
														<b-dropdown-item href="#"
															class="anterior-eye-color-picker text-dark text-decoration-none">
															<span class="color-picker"
																style="border: 1px solid black; padding: 0 8px; margin: 4px; background-color: brown; min-width: 10px; width: 10px; height: 8px"></span>
															Cokelat
														</b-dropdown-item>
														<b-dropdown-item href="#"
															class="anterior-eye-color-picker text-dark text-decoration-none">
															<span class="color-picker"
																style="border: 1px solid black; padding: 0 8px; margin: 4px; background-color: grey; min-width: 10px; width: 10px; height: 8px"></span>
															Abu-Abu
														</b-dropdown-item>
														<b-dropdown-item href="#"
															class="anterior-eye-color-picker text-dark text-decoration-none">
															<span class="color-picker"
																style="border: 1px solid black; padding: 0 8px; margin: 4px; background-color: black; min-width: 10px; width: 10px; height: 8px"></span>
															Hitam
														</b-dropdown-item>
													</div>
												</div>
											</b-dropdown>
										</div>
										<div class="d-flex align-items-center">
											<b-button pill id="anterior-eye-eraser" class="bg-warning">Hapus</b-button>
										</div>
										<div class="d-flex align-items-center">
											<b-button pill id="anterior-eye-clear" class="bg-danger">
												<img src="@/@core/assets/icons/canvas/trash.svg" alt="trash-icon"
													style="max-width: 12px;">Hapus Semua
											</b-button>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!-- END: input & buttons -->
					</div>
				</div>
			</div>
		</div>
		<!-- END: anterior eye -->
		<!-- START: posterior -->
		<div class="d-flex">
			<div class="card col-12 col-lg-10 mt-5 mx-lg-auto px-0">
				<div class="card-header bg-primary text-white">Posterior</div>
				<div class="card-body bg-c-info d-flex px-0 px-lg-3">
					<div class="col-12 mx-auto">
						<div class="bg-white rounded">
							<div class="panel-body">
								<div id="posterior-eye" class="posterior-eye-input" style="width: 100%;"></div>
							</div>
						</div>
						<!-- START: input & buttons -->
						<div class="col mt-3">
							<p class="mb-0">Ukuran pena / penghapus / text:</p>
							<div class="panel-body row mx-0">
								<div class="col-12 col-lg-4">
									<div class="slider-container">
										<input type="range" id="posterior-eye-slider" min="2" max="50" step="2" value="2"
											class="form-range col px-0" style="outline-color:red" />
										<p class="mb-0 slider-value"><strong>2</strong></p>
									</div>
								</div>
								<div class="col">
									<div class="d-flex flex-wrap">
										<div class="d-flex align-items-center">
											<b-button pill id="posterior-eye-brush" class="bg-purple">Pena</b-button>
										</div>
										<div class="d-flex align-items-center">
											<b-button pill id="posterior-eye-text" class="bg-info">Text</b-button>
										</div>
										<div>
											<span id="posterior-eye-current-color"
												style="border: 1px solid black; padding: 0 8px; margin: 4px; background-color: black; min-width: 10px; width: 10px; height: 8px"></span>
											<b-dropdown id="dropdown-dropup" dropup text="Warna" variant="white">
												<div class="d-flex">
													<div>
														<b-dropdown-item href="#"
															class="posterior-eye-color-picker text-dark text-decoration-none">
															<span class="color-picker"
																style="border: 1px solid black; padding: 0 8px; margin: 4px; background-color: red; min-width: 10px; width: 10px; height: 8px"></span>
															Merah
														</b-dropdown-item>
														<b-dropdown-item href="#"
															class="posterior-eye-color-picker text-dark text-decoration-none">
															<span class="color-picker"
																style="border: 1px solid black; padding: 0 8px; margin: 4px; background-color: pink; min-width: 10px; width: 10px; height: 8px"></span>
															Merah Muda
														</b-dropdown-item>
														<b-dropdown-item href="#"
															class="posterior-eye-color-picker text-dark text-decoration-none">
															<span class="color-picker"
																style="border: 1px solid black; padding: 0 8px; margin: 4px; background-color: orange; min-width: 10px; width: 10px; height: 8px"></span>
															Orange
														</b-dropdown-item>
														<b-dropdown-item href="#"
															class="posterior-eye-color-picker text-dark text-decoration-none">
															<span class="color-picker"
																style="border: 1px solid black; padding: 0 8px; margin: 4px; background-color: yellow; min-width: 10px; width: 10px; height: 8px"></span>
															Kuning
														</b-dropdown-item>
														<b-dropdown-item href="#"
															class="posterior-eye-color-picker text-dark text-decoration-none">
															<span class="color-picker"
																style="border: 1px solid black; padding: 0 8px; margin: 4px; background-color: lime; min-width: 10px; width: 10px; height: 8px"></span>
															Lime
														</b-dropdown-item>
														<b-dropdown-item href="#"
															class="posterior-eye-color-picker text-dark text-decoration-none">
															<span class="color-picker"
																style="border: 1px solid black; padding: 0 8px; margin: 4px; background-color: green; min-width: 10px; width: 10px; height: 8px"></span>
															Hijau
														</b-dropdown-item>
														<b-dropdown-item href="#"
															class="posterior-eye-color-picker text-dark text-decoration-none">
															<span class="color-picker"
																style="border: 1px solid black; padding: 0 8px; margin: 4px; background-color: cyan; min-width: 10px; width: 10px; height: 8px"></span>
															Cyan
														</b-dropdown-item>
													</div>
													<div>
														<b-dropdown-item href="#"
															class="posterior-eye-color-picker text-dark text-decoration-none">
															<span class="color-picker"
																style="border: 1px solid black; padding: 0 8px; margin: 4px; background-color: blue; min-width: 10px; width: 10px; height: 8px"></span>
															Biru
														</b-dropdown-item>
														<b-dropdown-item href="#"
															class="posterior-eye-color-picker text-dark text-decoration-none">
															<span class="color-picker"
																style="border: 1px solid black; padding: 0 8px; margin: 4px; background-color: purple; min-width: 10px; width: 10px; height: 8px"></span>
															Ungu
														</b-dropdown-item>
														<b-dropdown-item href="#"
															class="posterior-eye-color-picker text-dark text-decoration-none">
															<span class="color-picker"
																style="border: 1px solid black; padding: 0 8px; margin: 4px; background-color: magenta; min-width: 10px; width: 10px; height: 8px;"></span>
															Magenta
														</b-dropdown-item>
														<b-dropdown-item href="#"
															class="posterior-eye-color-picker text-dark text-decoration-none">
															<span class="color-picker"
																style="border: 1px solid black; padding: 0 8px; margin: 4px; background-color: brown; min-width: 10px; width: 10px; height: 8px"></span>
															Cokelat
														</b-dropdown-item>
														<b-dropdown-item href="#"
															class="posterior-eye-color-picker text-dark text-decoration-none">
															<span class="color-picker"
																style="border: 1px solid black; padding: 0 8px; margin: 4px; background-color: grey; min-width: 10px; width: 10px; height: 8px"></span>
															Abu-Abu
														</b-dropdown-item>
														<b-dropdown-item href="#"
															class="posterior-eye-color-picker text-dark text-decoration-none">
															<span class="color-picker"
																style="border: 1px solid black; padding: 0 8px; margin: 4px; background-color: black; min-width: 10px; width: 10px; height: 8px"></span>
															Hitam
														</b-dropdown-item>
													</div>
												</div>
											</b-dropdown>
										</div>
										<div class="d-flex align-items-center">
											<b-button pill id="posterior-eye-eraser" class="bg-warning">Hapus</b-button>
										</div>
										<div class="d-flex align-items-center">
											<b-button pill id="posterior-eye-clear" class="bg-danger">
												<img src="@/@core/assets/icons/canvas/trash.svg" alt="trash-icon"
													style="max-width: 12px;">Hapus Semua
											</b-button>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!-- END: input & buttons -->
					</div>
				</div>
			</div>
		</div>
		<!-- END: posterior eye -->
		<!-- START: Oftalmologis -->
		<!-- <div class="d-flex">
			<div class="card col-12 col-lg-10 mt-5 mx-lg-auto px-0">
				<div class="card-header bg-primary text-white">Oftalmologis</div>
				<div class="card-body bg-c-info d-flex">
					<div class="col-12 mx-auto">
						<div class="bg-white rounded">
							<div class="panel-body">
								<div id="oftalmologis" class="oftalmologis-input" style="width: 100%;"></div>
							</div>
						</div>
						<div class="mt-3 panel-body">
							<div class="row mx-0">
								<a href="#" class="mx-1 col btn btn-light oftalmologis-arrow-direction oftalmologis-arrow"
									arrow-direction="NW">
									<ul class="py-2"></ul>
								</a>
								<a href="#" class="mx-1 col btn btn-light oftalmologis-arrow-direction oftalmologis-arrow"
									arrow-direction="N">
									<ul class="py-2"></ul>
								</a>
								<a href="#" class="mx-1 col btn btn-light oftalmologis-arrow-direction oftalmologis-arrow"
									arrow-direction="NE">
									<ul class="py-2"></ul>
								</a>
							</div>
							<div class="row mx-0 mt-2">
								<a href="#" class="mx-1 col btn btn-light oftalmologis-arrow-direction oftalmologis-arrow"
									arrow-direction="W">
									<ul class="py-2"></ul>
								</a>
								<div class="btn-group dropup col oftalmologis-arrow mx-1 bg-white rounded d-flex">
									<a class="btn btn-default dropdown-toggle my-auto" data-toggle="dropdown"
										aria-haspopup="true" aria-expanded="false"><small>Gerak</small></a>
									<ul class="dropdown-menu col-12">
										<li>
											<a class="text-decoration-none text-dark oftalmologis-arrow oftalmologis-arrow-mode"
												arrow-mode="full" href="#">
												<span class="color-picker"
													style="padding: 0 8px; margin: 4px; min-width: 10px; width: 10px; height: 8px"></span>
												Tidak ada hambatan gerak
											</a>
										</li>
										<li><a class="text-decoration-none text-dark oftalmologis-arrow oftalmologis-arrow-mode"
												arrow-mode="partial" href="#"><span class="color-picker"
													style="padding: 0 8px; margin: 4px; min-width: 10px; width: 10px; height: 8px"></span>
												Terdapat hambatan gerak</a></li>
										<li><a class="text-decoration-none text-dark oftalmologis-arrow oftalmologis-arrow-mode"
												arrow-mode="none" href="#"><span class="color-picker"
													style="padding: 0 8px; margin: 4px; min-width: 10px; width: 10px; height: 8px"></span>
												Tidak dapat bergerak</a></li>
									</ul>
								</div>
								<a href="#" class="mx-1 col btn btn-light oftalmologis-arrow-direction oftalmologis-arrow"
									arrow-direction="E">
									<ul class="py-2"></ul>
								</a>
							</div>
							<div class="row mx-0 mt-2">
								<a href="#" class="mx-1 col btn btn-light oftalmologis-arrow-direction oftalmologis-arrow"
									arrow-direction="SW">
									<ul class="py-2"></ul>
								</a>
								<a href="#" class="mx-1 col btn btn-light oftalmologis-arrow-direction oftalmologis-arrow"
									arrow-direction="S">
									<ul class="py-2"></ul>
								</a>
								<a href="#" class="mx-1 col btn btn-light oftalmologis-arrow-direction oftalmologis-arrow"
									arrow-direction="SE">
									<ul class="py-2"></ul>
								</a>
							</div>
						</div>
						<div class="col mt-3">
							<p class="mb-0">Ukuran pena / penghapus / text:</p>
							<div class="panel-body row mx-0">
								<div class="col-12 col-lg-4">
									<div class="slider-container">
										<input type="range" id="oftalmologis-slider" min="2" max="50" step="2" value="2"
											class="form-range col px-0" style="outline-color:red" />
										<p class="mb-0 slider-value"><strong>2</strong></p>
									</div>
								</div>
								<div class="col">
									<div class="d-flex flex-wrap">
										<div class="d-flex align-items-center">
											<b-button pill id="oftalmologis-brush" class="bg-purple">Pena</b-button>
										</div>
										<div class="d-flex align-items-center">
											<b-button pill id="oftalmologis-text" class="bg-info">Text</b-button>
										</div>
										<div>
											<span id="oftalmologis-current-color"
												style="border: 1px solid black; padding: 0 8px; margin: 4px; background-color: black; min-width: 10px; width: 10px; height: 8px"></span>
											<b-dropdown id="dropdown-dropup" dropup text="Warna" variant="white">
												<div class="d-flex">
													<div>
														<b-dropdown-item href="#"
															class="oftalmologis-color-picker text-dark text-decoration-none">
															<span class="color-picker"
																style="border: 1px solid black; padding: 0 8px; margin: 4px; background-color: red; min-width: 10px; width: 10px; height: 8px"></span>
															Merah
														</b-dropdown-item>
														<b-dropdown-item href="#"
															class="oftalmologis-color-picker text-dark text-decoration-none">
															<span class="color-picker"
																style="border: 1px solid black; padding: 0 8px; margin: 4px; background-color: pink; min-width: 10px; width: 10px; height: 8px"></span>
															Merah Muda
														</b-dropdown-item>
														<b-dropdown-item href="#"
															class="oftalmologis-color-picker text-dark text-decoration-none">
															<span class="color-picker"
																style="border: 1px solid black; padding: 0 8px; margin: 4px; background-color: orange; min-width: 10px; width: 10px; height: 8px"></span>
															Orange
														</b-dropdown-item>
														<b-dropdown-item href="#"
															class="oftalmologis-color-picker text-dark text-decoration-none">
															<span class="color-picker"
																style="border: 1px solid black; padding: 0 8px; margin: 4px; background-color: yellow; min-width: 10px; width: 10px; height: 8px"></span>
															Kuning
														</b-dropdown-item>
														<b-dropdown-item href="#"
															class="oftalmologis-color-picker text-dark text-decoration-none">
															<span class="color-picker"
																style="border: 1px solid black; padding: 0 8px; margin: 4px; background-color: lime; min-width: 10px; width: 10px; height: 8px"></span>
															Lime
														</b-dropdown-item>
														<b-dropdown-item href="#"
															class="oftalmologis-color-picker text-dark text-decoration-none">
															<span class="color-picker"
																style="border: 1px solid black; padding: 0 8px; margin: 4px; background-color: green; min-width: 10px; width: 10px; height: 8px"></span>
															Hijau
														</b-dropdown-item>
														<b-dropdown-item href="#"
															class="oftalmologis-color-picker text-dark text-decoration-none">
															<span class="color-picker"
																style="border: 1px solid black; padding: 0 8px; margin: 4px; background-color: cyan; min-width: 10px; width: 10px; height: 8px"></span>
															Cyan
														</b-dropdown-item>
													</div>
													<div>
														<b-dropdown-item href="#"
															class="oftalmologis-color-picker text-dark text-decoration-none">
															<span class="color-picker"
																style="border: 1px solid black; padding: 0 8px; margin: 4px; background-color: blue; min-width: 10px; width: 10px; height: 8px"></span>
															Biru
														</b-dropdown-item>
														<b-dropdown-item href="#"
															class="oftalmologis-color-picker text-dark text-decoration-none">
															<span class="color-picker"
																style="border: 1px solid black; padding: 0 8px; margin: 4px; background-color: purple; min-width: 10px; width: 10px; height: 8px"></span>
															Ungu
														</b-dropdown-item>
														<b-dropdown-item href="#"
															class="oftalmologis-color-picker text-dark text-decoration-none">
															<span class="color-picker"
																style="border: 1px solid black; padding: 0 8px; margin: 4px; background-color: magenta; min-width: 10px; width: 10px; height: 8px;"></span>
															Magenta
														</b-dropdown-item>
														<b-dropdown-item href="#"
															class="oftalmologis-color-picker text-dark text-decoration-none">
															<span class="color-picker"
																style="border: 1px solid black; padding: 0 8px; margin: 4px; background-color: brown; min-width: 10px; width: 10px; height: 8px"></span>
															Cokelat
														</b-dropdown-item>
														<b-dropdown-item href="#"
															class="oftalmologis-color-picker text-dark text-decoration-none">
															<span class="color-picker"
																style="border: 1px solid black; padding: 0 8px; margin: 4px; background-color: grey; min-width: 10px; width: 10px; height: 8px"></span>
															Abu-Abu
														</b-dropdown-item>
														<b-dropdown-item href="#"
															class="oftalmologis-color-picker text-dark text-decoration-none">
															<span class="color-picker"
																style="border: 1px solid black; padding: 0 8px; margin: 4px; background-color: black; min-width: 10px; width: 10px; height: 8px"></span>
															Hitam
														</b-dropdown-item>
													</div>
												</div>
											</b-dropdown>
										</div>
										<div class="d-flex align-items-center">
											<b-button pill id="oftalmologis-eraser" class="bg-warning">Hapus</b-button>
										</div>
										<div class="d-flex align-items-center">
											<b-button pill id="oftalmologis-clear" class="bg-danger">
												<img src="@/@core/assets/icons/canvas/trash.svg" alt="trash-icon"
													style="max-width: 12px;">Hapus Semua
											</b-button>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div> -->
	</div>
</template>

<script>
import $ from 'jquery'
import '@/views/pages/canvasing/jsCanvas/szkajanteriorcanvas.js'
import '@/views/pages/canvasing/jsCanvas/szkajposteriorcanvas.js'
import '@/views/pages/canvasing/jsCanvas/szkajoftalmologiscanvas.js'
import '@/views/pages/canvasing/jsCanvas/szcursor.js'
export default {
	mounted() {

		// POSTERIOR
		var $posteriorEye = $('#posterior-eye.posterior-eye-input').szKajPosteriorCanvas({
			responsive: true,
			canDraw: true,
		});

		/* Slider */
		$('#posterior-eye-slider').change(function () {
			var pointSize = $(this).val();
			var valueLabel = $(this).parents('.slider-container').find('.slider-value strong').html(pointSize);

			$posteriorEye.szKajPosteriorCanvas('setPointSize', pointSize);
		});

		/* Brush */
		$('#posterior-eye-brush').click(function () {
			$posteriorEye.szKajPosteriorCanvas('setMode', 'brush');
		});


		/* Text */
		$('#posterior-eye-text').click(function () {
			$posteriorEye.szKajPosteriorCanvas('setMode', 'text');
		});

		/* Eraser */
		$('#posterior-eye-eraser').click(function () {
			$posteriorEye.szKajPosteriorCanvas('setMode', 'eraser');
		});

		/* Color Picker */
		$('.posterior-eye-color-picker').click(function (e) {
			e.preventDefault();
			var color = $(this).find('.color-picker').css('background-color');
			$posteriorEye.szKajPosteriorCanvas('setColor', color);
			$('#posterior-eye-current-color').css('background-color', color);
		});

		/* Clear */
		$('#posterior-eye-clear').click(function () {
			$posteriorEye.szKajPosteriorCanvas('clearCanvas');
			confirm('Hapus semua gambar Posterior?');
			return false;
		});

		// ANTERIOR
		var $anteriorEye = $('#anterior-eye.anterior-eye-input').szKajAnteriorCanvas({
			responsive: true,
			canDraw: true,
		});

		/* Slider */
		$('#anterior-eye-slider').change(function () {
			var pointSize = $(this).val();
			var valueLabel = $(this).parents('.slider-container').find('.slider-value strong').html(pointSize);

			$anteriorEye.szKajAnteriorCanvas('setPointSize', pointSize);
		});
		/* Brush */
		$('#anterior-eye-brush').click(function () {
			$anteriorEye.szKajAnteriorCanvas('setMode', 'brush');
		});

		/* Text */
		$('#anterior-eye-text').click(function () {
			$anteriorEye.szKajAnteriorCanvas('setMode', 'text');
		});

		/* Eraser */
		$('#anterior-eye-eraser').click(function () {
			console.log('clicked');
			$anteriorEye.szKajAnteriorCanvas('setMode', 'eraser');
		});

		/* Color Picker */
		$('.anterior-eye-color-picker').click(function (e) {
			e.preventDefault();
			var color = $(this).find('.color-picker').css('background-color');
			$anteriorEye.szKajAnteriorCanvas('setColor', color);
			$('#anterior-eye-current-color').css('background-color', color);
		});

		/* Clear */
		$('#anterior-eye-clear').click(function () {
			$anteriorEye.szKajAnteriorCanvas('clearCanvas');
			confirm('Hapus semua gambar Anterior?');
			return false;
		});

	},
	// OFTALMOLOGIS
	// computed(){
	// 	$(document).ready(function () {
	// 		$('input[type=range]').val('2');

	// 		setArrowDirection('full');
	// 		setCurrentArrowMode('full');

	// 		$('.oftalmologis-arrow-mode').each(function () {
	// 			var self = $(this);
	// 			var mode = $(this).attr('arrow-mode');
	// 			var thumbnail = new szCursor();
	// 			thumbnail.getArrowCursorImage(mode, 16, 0.1, 'E', function (base64Image) {
	// 				var bgImg = 'url(' + base64Image + ')';

	// 				self.css('background-image', bgImg);
	// 				self.css('background-position', '10px 0px');
	// 				self.css('background-repeat', 'no-repeat');
	// 				self.css('display', 'inline-block');

	// 				self.find('.arrow-image').css('background-image', bgImg);
	// 				self.find('.arrow-image').css('background-position', '10px 0px 0px 0px');
	// 				self.find('.arrow-image').css('background-repeat', 'no-repeat');
	// 				self.find('.arrow-image').css('display', 'inline');
	// 				self.find('.arrow-image').css('position', 'relative');
	// 				self.find('.arrow-image').css('top', '7px');
	// 				self.find('.arrow-image').css('margin-left', '10px');
	// 				self.find('.arrow-image').css('vertical-align', 'top');
	// 			});
	// 		});

	// 		toggle_resep_kacamata(false);
	// 	});

	// 	function setArrowDirection(mode) {
	// 		$('.oftalmologis-arrow-direction').each(function () {
	// 			var self = $(this);
	// 			var direction = $(this).attr('arrow-direction');
	// 			var thumbnail = new szCursor();
	// 			thumbnail.getArrowCursorImage(mode, 24, 0.2, direction, function (base64Image) {
	// 				var bgImg = 'url(' + base64Image + ')';

	// 				self.css('background-image', bgImg);
	// 				self.css('background-position', 'center');
	// 				self.css('background-repeat', 'no-repeat');
	// 				self.css('display', 'inline-block');

	// 				self.find('.arrow-image').css('display', 'inline');
	// 				self.find('.arrow-image').css('position', 'relative');
	// 				self.find('.arrow-image').css('top', '7px');
	// 				self.find('.arrow-image').css('margin-left', '10px');
	// 				self.find('.arrow-image').css('vertical-align', 'top');
	// 			});
	// 		});
	// 	}

	// 	function setCurrentArrowMode(mode) {
	// 		var self = $('.oftalmologis-current-mode');
	// 		var direction = self.attr('arrow-direction');
	// 		var thumbnail = new szCursor();
	// 		thumbnail.getArrowCursorImage(mode, 24, 0.2, 'E', function (base64Image) {
	// 			var bgImg = 'url(' + base64Image + ')';

	// 			self.css('background-image', bgImg);
	// 			self.css('background-position', '0px 0px; width: 24px; height: 24px');
	// 			self.css('background-repeat', 'no-repeat');
	// 			self.css('display', 'inline-block');

	// 			self.find('.arrow-image').css('display', 'inline');
	// 			self.find('.arrow-image').css('position', 'relative');
	// 			self.find('.arrow-image').css('top', '7px');
	// 			self.find('.arrow-image').css('margin-left', '10px');
	// 			self.find('.arrow-image').css('vertical-align', 'top');
	// 		});
	// 	}

	// 	var $oftalmologis = $('#oftalmologis.oftalmologis-input').szKajOftalmologisCanvas({
	// 		responsive: true,
	// 		canDraw: true,
	// 	});

	// 	$('#oftalmologis-slider').change(function () {
	// 		var pointSize = $(this).val();
	// 		var valueLabel = $(this).parents('.slider-container').find('.slider-value strong').html(pointSize);

	// 		$oftalmologis.szKajOftalmologisCanvas('setPointSize', pointSize);
	// 	});

	// 	$('#oftalmologis-brush').click(function () {
	// 		$oftalmologis.szKajOftalmologisCanvas('setMode', 'brush');
	// 	});


	// 	$('#oftalmologis-text').click(function () {
	// 		$oftalmologis.szKajOftalmologisCanvas('setMode', 'text');
	// 	});

	// 	$('#oftalmologis-eraser').click(function () {
	// 		$oftalmologis.szKajOftalmologisCanvas('setMode', 'eraser');
	// 	});

	// 	$('.oftalmologis-color-picker').click(function (e) {
	// 		e.preventDefault();
	// 		var color = $(this).find('.color-picker').css('background-color');
	// 		$oftalmologis.szKajOftalmologisCanvas('setColor', color);
	// 		$('#oftalmologis-current-color').css('background-color', color);
	// 	});


	// 	$('#oftalmologis-clear').click(function () {
	// 		$oftalmologis.szKajOftalmologisCanvas('clearCanvas');
	// 		confirm('Hapus semua gambar Oftalmologis?');
	// 		return false;
	// 	});

		/* Oftalmologis Specific Arrow Functions */
	// 	$('.oftalmologis-arrow-direction').click(function (e) {
	// 		e.preventDefault();
	// 		var arrowDirection = $(this).attr('arrow-direction');
	// 		$oftalmologis.szKajOftalmologisCanvas('setArrowDirection', arrowDirection);
	// 	});

	// 	$('.oftalmologis-arrow').click(function (e) {
	// 		e.preventDefault();
	// 		$oftalmologis.szKajOftalmologisCanvas('setMode', 'arrow');
	// 	});

	// 	$('.oftalmologis-arrow-mode').click(function (e) {
	// 		e.preventDefault();
	// 		var arrowMode = $(this).attr('arrow-mode');
	// 		$oftalmologis.szKajOftalmologisCanvas('setArrowMode', arrowMode);
	// 		setArrowDirection(arrowMode);
	// 	});

	// 	/* Toggle */
	// 	$('[name="sts_resep_kacamata"]').on('change', function () {
	// 		if ($(this).prop('checked') == true) {
	// 			toggle_resep_kacamata(true);
	// 		} else {
	// 			toggle_resep_kacamata(false);
	// 		}
	// 	});

	// 	function toggle_resep_kacamata(status) {
	// 		if (status === true) {
	// 			$('#panel-resep-kacamata').find('.panel-body').show();
	// 		} else {
	// 			$('#panel-resep-kacamata').find('.panel-body').hide();
	// 		}
	// 	}
	// }
}
</script>

<style scoped>
button.btn {
	line-height: 100%;
	margin: 0;
}

.bg-c-info {
	background-color: #BBE2FC !important;
}

canvas {
	border: 0;
}

input[type=range]::-webkit-slider-runnable-track {
	background: #000;
	border-radius: 10px;
}

input[type=range]::-webkit-slider-thumb {
	background: #fff;
}

.bg-primary {
	background-color: #0099ff !important;
}
</style>