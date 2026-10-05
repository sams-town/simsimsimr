<template>
	<div>
		<b-button @click="showModal = !showModal" pill variant="outline-indigo" size="sm" class="px-3">Pemeriksaan Fisik (Canvassing)</b-button>
		<b-modal v-model="showModal" size="xl" scrollable hide-footer title="Pemeriksaan Fisik (Canvassing)" title-class="text-bold-18">
			<div>
				<div class="col px-0">
					<div class="row mx-0">
						<!-- here -->
						<b-button pill :variant="activeTab === 'abdomen' ? 'outline-indigo' : 'outline-neutral-500 border-0'" @click="goToTab('abdomen')" class="m-1">Kajian Abdomen</b-button>

						<b-button pill :variant="activeTab === 'anus' ? 'outline-indigo' : 'outline-neutral-500 border-0'" @click="goToTab('anus')" class="m-1">Kajian Anus</b-button>
						<b-button pill :variant="activeTab === 'dada' ? 'outline-indigo' : 'outline-neutral-500 border-0'"  @click="goToTab('dada')" class="m-1">Kajian Dada</b-button>
						<b-button pill :variant="activeTab === 'ekstrimitas' ? 'outline-indigo' : 'outline-neutral-500 border-0'"  @click="goToTab('ekstrimitas')" class="m-1">Kajian Ekstrimitas</b-button>
						<b-button pill :variant="activeTab === 'hidung' ? 'outline-indigo' : 'outline-neutral-500 border-0'"  @click="goToTab('hidung')" class="m-1">Kajian Hidung</b-button>
						<b-button pill :variant="activeTab === 'jantung' ? 'outline-indigo' : 'outline-neutral-500 border-0'"  @click="goToTab('jantung')" class="m-1">Kajian Jantung</b-button>
						<b-button pill :variant="activeTab === 'kepala' ? 'outline-indigo' : 'outline-neutral-500 border-0'"  @click="goToTab('kepala')" class="m-1">Kajian Kepala</b-button>
						<b-button pill :variant="activeTab === 'leher' ? 'outline-indigo' : 'outline-neutral-500 border-0'"  @click="goToTab('leher')" class="m-1">Kajian Leher</b-button>
						<b-button pill :variant="activeTab === 'mata' ? 'outline-indigo' : 'outline-neutral-500 border-0'"  @click="goToTab('mata')" class="m-1">Kajian Mata</b-button>
						<b-button pill :variant="activeTab === 'mulut' ? 'outline-indigo' : 'outline-neutral-500 border-0'"  @click="goToTab('mulut')" class="m-1">Kajian Mulut</b-button>
						<b-button pill :variant="activeTab === 'muskuloskeletal' ? 'outline-indigo' : 'outline-neutral-500 border-0'"  @click="goToTab('muskuloskeletal')" class="m-1">Kajian Muskuloskeletal</b-button>
						<b-button pill :variant="activeTab === 'paru' ? 'outline-indigo' : 'outline-neutral-500 border-0'"  @click="goToTab('paru')" class="m-1">Kajian Paru-paru</b-button>
						<b-button pill :variant="activeTab === 'rongga' ? 'outline-indigo' : 'outline-neutral-500 border-0'"  @click="goToTab('rongga')" class="m-1">Kajian Rongga Mulut</b-button>
						<b-button pill :variant="activeTab === 'telinga' ? 'outline-indigo' : 'outline-neutral-500 border-0'"  @click="goToTab('telinga')" class="m-1">Kajian Telinga</b-button>
						<b-button pill :variant="activeTab === 'tenggorokan' ? 'outline-indigo' : 'outline-neutral-500 border-0'"  @click="goToTab('tenggorokan')" class="m-1">Kajian Tenggorokan</b-button>
						<b-button pill :variant="activeTab === 'urogenital' ? 'outline-indigo' : 'outline-neutral-500 border-0'"  @click="goToTab('urogenital')" class="m-1">Kajian Urogenital</b-button>

						<!-- <b-button pill :variant="activeTab === 'kajianMata' ? 'outline-indigo' : 'outline-neutral-500 border-0'"  @click="goToTab('kajianMata')" class="m-1">Kajian Mata</b-button>
						<b-button pill :variant="activeTab === 'kajianGigi' ? 'outline-indigo' : 'outline-neutral-500 border-0'"  @click="goToTab('kajianGigi')" class="m-1">Kajian Gigi</b-button> -->
					</div>
				</div>
				<section v-if="activeTab === 'abdomen'" class="d-flex flex-column mt-32">
					<ModernLabel label="Pemeriksaan Abdomen" />
					<div class="mt-20">
						<div v-if="editable.abdomen == true" class="w-100">
							<AbdomenView />
							<!-- Abdomen-PR -->
						</div>
						<div v-else class="w-100 mx-auto">
							<div class="card">
								<div class="card-header bg-primary-500 text-semi-18 text-white">Abdomen</div>
								<div class="card-body d-flex bg-white">
									<div class="mx-auto col-lg-8">
										<img src="@/@core/assets/images/canvas/Abdomen (jenis kelamin Laki-laki).jpg" alt="" class="w-100" style="aspect-ratio: 16/9; object-fit: cover;">
										<b-button @click="editable.abdomen = !editable.abdomen" size="sm" variant="primary-200" class="w-100">
											<b-icon icon="pencil-square" scale="0.7" />
											<span class="text-bold-16">Start Drawing</span></b-button>
									</div>
								</div>
							</div>
						</div>
						<div class="mt-20">
							<label class="c-form-label">Pemeriksaan Fisik (O)</label>
							<b-textarea rows="3" placeholder="Tulis hasil pemeriksaan fisik"></b-textarea>
						</div>
					</div>
				</section>
				<section v-else-if="activeTab === 'anus'" class="d-flex flex-column mt-32">
					<ModernLabel label="Pemeriksaan Anus" />
					<div class="mt-20">
						<div v-if="editable.anus == true" class="w-100">
							<AnusView />
						</div>
						<div v-else class="w-100 mx-auto">
							<div class="card">
								<div class="card-header bg-primary-500 text-semi-18 text-white">Anus</div>
								<div class="card-body d-flex bg-white">
									<div class="mx-auto col-lg-8">
										<img src="@/@core/assets/images/canvas/Anus.jpg" alt="" class="w-100" style="aspect-ratio: 16/9; object-fit: cover;">
										<b-button @click="editable.anus = !editable.anus" size="sm" variant="primary-200" class="w-100">
											<b-icon icon="pencil-square" scale="0.7" />
											<span class="text-bold-16">Start Drawing</span>
										</b-button>
									</div>
								</div>
							</div>
						</div>
						<div class="mt-20">
							<label class="c-form-label">Pemeriksaan Fisik (O)</label>
							<b-textarea rows="3" placeholder="Tulis hasil pemeriksaan fisik"></b-textarea>
						</div>
					</div>
				</section>
				<section v-else-if="activeTab === 'dada'" class="d-flex flex-column mt-32">
					<div ref="Dada Lk">
						<ModernLabel label="Pemeriksaan Dada Laki-laki" />
						<div class="mt-20">
							<div v-if="editable.dadaLaki == true" class="w-100">
								<DadaLakiView />
							</div>
							<div v-else class="w-100 mx-auto">
								<div class="card">
									<div class="card-header bg-primary-500 text-semi-18 text-white">Dada Laki-laki</div>
									<div class="card-body d-flex bg-white">
										<div class="mx-auto col-lg-8">
											<img src="@/@core/assets/images/canvas/Dada-laki.jpg" alt="" class="w-100" style="aspect-ratio: 16/9; object-fit: cover;">
											<b-button @click="editable.dadaLaki = !editable.dadaLaki" size="sm" variant="primary-200" class="w-100">
												<b-icon icon="pencil-square" scale="0.7" />
												<span class="text-bold-16">Start Drawing</span>
											</b-button>
										</div>
									</div>
								</div>
							</div>
							<div class="mt-20">
								<label class="c-form-label">Pemeriksaan Fisik (O)</label>
								<b-textarea rows="3" placeholder="Tulis hasil pemeriksaan fisik"></b-textarea>
							</div>
						</div>
					</div>
					<div ref="Dada Pr" class="mt-72">
						<ModernLabel label="Pemeriksaan Dada Perempuan" />
						<div class="mt-20">
							<div v-if="editable.dadaPr == true" class="w-100">
								<DadaPrView />
							</div>
							<div v-else class="w-100 mx-auto">
								<div class="card">
									<div class="card-header bg-primary-500 text-semi-18 text-white">Dada Perempuan</div>
									<div class="card-body d-flex bg-white">
										<div class="mx-auto col-lg-8">
											<img src="@/@core/assets/images/canvas/Dada-pr.jpg" alt="" class="w-100" style="aspect-ratio: 16/9; object-fit: cover;">
											<b-button @click="editable.dadaPr = !editable.dadaPr" size="sm" variant="primary-200" class="w-100">
												<b-icon icon="pencil-square" scale="0.7" />
												<span class="text-bold-16">Start Drawing</span>
											</b-button>
										</div>
									</div>
								</div>
							</div>
							<div class="mt-20">
								<label class="c-form-label">Pemeriksaan Fisik (O)</label>
								<b-textarea rows="3" placeholder="Tulis hasil pemeriksaan fisik"></b-textarea>
							</div>
						</div>
					</div>
				</section>
				<section v-else-if="activeTab === 'ekstrimitas'" class="d-flex flex-column mt-32">
					<div ref="Dada Lk">
						<ModernLabel label="Ekstremitas Atas" />
						<div class="mt-20">
							<div v-if="editable.ekstrimAtas == true" class="w-100">
								<EkstrimAtasView />
							</div>
							<div v-else class="w-100 mx-auto">
								<div class="card">
									<div class="card-header bg-primary-500 text-semi-18 text-white">Ekstremitas Atas</div>
									<div class="card-body d-flex bg-white">
										<div class="mx-auto col-lg-8">
											<img src="@/@core/assets/images/canvas/Ekstremitas Atas.jpg" alt="" class="w-100" style="aspect-ratio: 16/9; object-fit: cover;">
											<b-button @click="editable.ekstrimAtas = !editable.ekstrimAtas" size="sm" variant="primary-200" class="w-100">
												<b-icon icon="pencil-square" scale="0.7" />
												<span class="text-bold-16">Start Drawing</span>
											</b-button>
										</div>
									</div>
								</div>
							</div>
							<div class="mt-20">
								<label class="c-form-label">Pemeriksaan Fisik (O)</label>
								<b-textarea rows="3" placeholder="Tulis hasil pemeriksaan fisik"></b-textarea>
							</div>
						</div>
					</div>
					<div ref="Dada Pr" class="mt-72">
						<ModernLabel label="Ekstremitas Bawah" />
						<div class="mt-20">
							<div v-if="editable.ekstrimBawah == true" class="w-100">
								<EkstrimBawahView />
							</div>
							<div v-else class="w-100 mx-auto">
								<div class="card">
									<div class="card-header bg-primary-500 text-semi-18 text-white">Ekstremitas Bawah</div>
									<div class="card-body d-flex bg-white">
										<div class="mx-auto col-lg-8">
											<img src="@/@core/assets/images/canvas/Ekstremitas Bawah.jpg" alt="" class="w-100" style="aspect-ratio: 16/9; object-fit: cover;">
											<b-button @click="editable.ekstrimBawah = !editable.ekstrimBawah" size="sm" variant="primary-200" class="w-100">
												<b-icon icon="pencil-square" scale="0.7" />
												<span class="text-bold-16">Start Drawing</span>
											</b-button>
										</div>
									</div>
								</div>
							</div>
							<div class="mt-20">
								<label class="c-form-label">Pemeriksaan Fisik (O)</label>
								<b-textarea rows="3" placeholder="Tulis hasil pemeriksaan fisik"></b-textarea>
							</div>
						</div>
					</div>
				</section>
				<section v-else-if="activeTab === 'hidung'" class="d-flex flex-column mt-32">
					<ModernLabel label="Pemeriksaan Hidung" />
					<div class="mt-20">
						<div v-if="editable.hidung == true" class="w-100">
							<HidungView />
						</div>
						<div v-else class="w-100 mx-auto">
							<div class="card">
								<div class="card-header bg-primary-500 text-semi-18 text-white">Hidung</div>
								<div class="card-body d-flex bg-white">
									<div class="mx-auto col-lg-8">
										<img src="@/@core/assets/images/canvas/Hidung.jpg" alt="" class="w-100" style="aspect-ratio: 16/9; object-fit: cover;">
										<b-button @click="editable.hidung = !editable.hidung" size="sm" variant="primary-200" class="w-100">
											<b-icon icon="pencil-square" scale="0.7" />
											<span class="text-bold-16">Start Drawing</span>
										</b-button>
									</div>
								</div>
							</div>
						</div>
						<div class="mt-20">
							<label class="c-form-label">Pemeriksaan Fisik (O)</label>
							<b-textarea rows="3" placeholder="Tulis hasil pemeriksaan fisik"></b-textarea>
						</div>
					</div>
				</section>
				<section v-else-if="activeTab === 'jantung'" class="d-flex flex-column mt-32">
					<ModernLabel label="Pemeriksaan Jantung" />
					<div class="mt-20">
						<div v-if="editable.jantung == true" class="w-100">
							<JantungView />
						</div>
						<div v-else class="w-100 mx-auto">
							<div class="card">
								<div class="card-header bg-primary-500 text-semi-18 text-white">Jantung</div>
								<div class="card-body d-flex bg-white">
									<div class="mx-auto col-lg-8">
										<img src="@/@core/assets/images/canvas/Jantung.jpg" alt="" class="w-100" style="aspect-ratio: 16/9; object-fit: cover;">
										<b-button @click="editable.jantung = !editable.jantung" size="sm" variant="primary-200" class="w-100">
											<b-icon icon="pencil-square" scale="0.7" />
											<span class="text-bold-16">Start Drawing</span>
										</b-button>
									</div>
								</div>
							</div>
						</div>
						<div class="mt-20">
							<label class="c-form-label">Pemeriksaan Fisik (O)</label>
							<b-textarea rows="3" placeholder="Tulis hasil pemeriksaan fisik"></b-textarea>
						</div>
					</div>
				</section>
				<section v-else-if="activeTab === 'kepala'" class="d-flex flex-column mt-32">
					<ModernLabel label="Pemeriksaan Kepala" />
					<div class="mt-20">
						<div v-if="editable.kepala == true" class="w-100">
							<KepalaView />
						</div>
						<div v-else class="w-100 mx-auto">
							<div class="card">
								<div class="card-header bg-primary-500 text-semi-18 text-white">Kepala</div>
								<div class="card-body d-flex bg-white">
									<div class="mx-auto col-lg-8">
										<img src="@/@core/assets/images/canvas/Kepala.jpg" alt="" class="w-100" style="aspect-ratio: 16/9; object-fit: cover;">
										<b-button @click="editable.kepala = !editable.kepala" size="sm" variant="primary-200" class="w-100">
											<b-icon icon="pencil-square" scale="0.7" />
											<span class="text-bold-16">Start Drawing</span>
										</b-button>
									</div>
								</div>
							</div>
						</div>
						<div class="mt-20">
							<label class="c-form-label">Pemeriksaan Fisik (O)</label>
							<b-textarea rows="3" placeholder="Tulis hasil pemeriksaan fisik"></b-textarea>
						</div>
					</div>
				</section>
				<section v-else-if="activeTab === 'leher'" class="d-flex flex-column mt-32">
					<div ref="Leher">
						<ModernLabel label="Leher" />
						<div class="mt-20">
							<div v-if="editable.leher == true" class="w-100">
								<LeherView />
							</div>
							<div v-else class="w-100 mx-auto">
								<div class="card">
									<div class="card-header bg-primary-500 text-semi-18 text-white">Leher</div>
									<div class="card-body d-flex bg-white">
										<div class="mx-auto col-lg-8">
											<img src="@/@core/assets/images/canvas/Leher.jpg" alt="" class="w-100" style="aspect-ratio: 16/9; object-fit: cover;">
											<b-button @click="editable.leher = !editable.leher" size="sm" variant="primary-200" class="w-100">
												<b-icon icon="pencil-square" scale="0.7" />
												<span class="text-bold-16">Start Drawing</span>
											</b-button>
										</div>
									</div>
								</div>
							</div>
							<div class="mt-20">
								<label class="c-form-label">Pemeriksaan Fisik (O)</label>
								<b-textarea rows="3" placeholder="Tulis hasil pemeriksaan fisik"></b-textarea>
							</div>
						</div>
					</div>
					<div ref="Leher-depan" class="mt-72">
						<ModernLabel label="Leher Depan" />
						<div class="mt-20">
							<div v-if="editable.leherDepan == true" class="w-100">
								<LeherDepanView />
							</div>
							<div v-else class="w-100 mx-auto">
								<div class="card">
									<div class="card-header bg-primary-500 text-semi-18 text-white">Leher Depan</div>
									<div class="card-body d-flex bg-white">
										<div class="mx-auto col-lg-8">
											<img src="@/@core/assets/images/canvas/Leher Depan.jpg" alt="" class="w-100" style="aspect-ratio: 16/9; object-fit: cover;">
											<b-button @click="editable.leherDepan = !editable.leherDepan" size="sm" variant="primary-200" class="w-100">
												<b-icon icon="pencil-square" scale="0.7" />
												<span class="text-bold-16">Start Drawing</span>
											</b-button>
										</div>
									</div>
								</div>
							</div>
							<div class="mt-20">
								<label class="c-form-label">Pemeriksaan Fisik (O)</label>
								<b-textarea rows="3" placeholder="Tulis hasil pemeriksaan fisik"></b-textarea>
							</div>
						</div>
					</div>
				</section>
				<section v-else-if="activeTab === 'mata'" class="d-flex flex-column mt-32">
					<ModernLabel label="Pemeriksaan Mata" />
					<div class="mt-20">
						<div v-if="editable.mata == true" class="w-100">
							<MataView />
						</div>
						<div v-else class="w-100 mx-auto">
							<div class="card">
								<div class="card-header bg-primary-500 text-semi-18 text-white">Mata</div>
								<div class="card-body d-flex bg-white">
									<div class="mx-auto col-lg-8">
										<img src="@/@core/assets/images/canvas/Mata.jpg" alt="" class="w-100" style="aspect-ratio: 16/9; object-fit: cover;">
										<b-button @click="editable.mata = !editable.mata" size="sm" variant="primary-200" class="w-100">
											<b-icon icon="pencil-square" scale="0.7" />
											<span class="text-bold-16">Start Drawing</span>
										</b-button>
									</div>
								</div>
							</div>
						</div>
						<div class="mt-20">
							<label class="c-form-label">Pemeriksaan Fisik (O)</label>
							<b-textarea rows="3" placeholder="Tulis hasil pemeriksaan fisik"></b-textarea>
						</div>
					</div>
				</section>
				<section v-else-if="activeTab === 'mulut'" class="d-flex flex-column mt-32">
					<ModernLabel label="Pemeriksaan Mulut" />
					<div class="mt-20">
						<div v-if="editable.mulut == true" class="w-100">
							<MulutView />
						</div>
						<div v-else class="w-100 mx-auto">
							<div class="card">
								<div class="card-header bg-primary-500 text-semi-18 text-white">Mata</div>
								<div class="card-body d-flex bg-white">
									<div class="mx-auto col-lg-8">
										<img src="@/@core/assets/images/canvas/Mulut.jpg" alt="" class="w-100" style="aspect-ratio: 16/9; object-fit: cover;">
										<b-button @click="editable.mulut = !editable.mulut" size="sm" variant="primary-200" class="w-100">
											<b-icon icon="pencil-square" scale="0.7" />
											<span class="text-bold-16">Start Drawing</span>
										</b-button>
									</div>
								</div>
							</div>
						</div>
						<div class="mt-20">
							<label class="c-form-label">Pemeriksaan Fisik (O)</label>
							<b-textarea rows="3" placeholder="Tulis hasil pemeriksaan fisik"></b-textarea>
						</div>
					</div>
				</section>
				<section v-else-if="activeTab === 'muskuloskeletal'" class="d-flex flex-column mt-32">
					<div ref="Muskuloskeletal Bayi">
						<ModernLabel label="Pemerikasaan Muskuloskeletal Bayi" />
						<div class="mt-20">
							<div v-if="editable.musBayi == true" class="w-100">
								<MuskuloskeletalBayiView />
							</div>
							<div v-else class="w-100 mx-auto">
								<div class="card">
									<div class="card-header bg-primary-500 text-semi-18 text-white">Muskuloskeletal Bayi</div>
									<div class="card-body d-flex bg-white">
										<div class="mx-auto col-lg-8">
											<img src="@/@core/assets/images/canvas/Muskuloskeletal-bayi.jpg" alt="" class="w-100" style="aspect-ratio: 16/9; object-fit: cover;">
											<b-button @click="editable.musBayi = !editable.musBayi" size="sm" variant="primary-200" class="w-100">
												<b-icon icon="pencil-square" scale="0.7" />
												<span class="text-bold-16">Start Drawing</span>
											</b-button>
										</div>
									</div>
								</div>
							</div>
							<div class="mt-20">
								<label class="c-form-label">Pemeriksaan Fisik (O)</label>
								<b-textarea rows="3" placeholder="Tulis hasil pemeriksaan fisik"></b-textarea>
							</div>
						</div>
					</div>
					<div ref="Muskuloskeletal Laki" class="mt-72">
						<ModernLabel label="Pemerikasaan Muskuloskeletal Laki-laki" />
						<div class="mt-20">
							<div v-if="editable.musLaki == true" class="w-100">
								<MuskuloskeletalLakiView />
							</div>
							<div v-else class="w-100 mx-auto">
								<div class="card">
									<div class="card-header bg-primary-500 text-semi-18 text-white">Muskuloskeletal Laki-laki</div>
									<div class="card-body d-flex bg-white">
										<div class="mx-auto col-lg-8">
											<img src="@/@core/assets/images/canvas/Muskuloskeletal-laki.jpg" alt="" class="w-100" style="aspect-ratio: 16/9; object-fit: cover;">
											<b-button @click="editable.musLaki = !editable.musLaki" size="sm" variant="primary-200" class="w-100">
												<b-icon icon="pencil-square" scale="0.7" />
												<span class="text-bold-16">Start Drawing</span>
											</b-button>
										</div>
									</div>
								</div>
							</div>
							<div class="mt-20">
								<label class="c-form-label">Pemeriksaan Fisik (O)</label>
								<b-textarea rows="3" placeholder="Tulis hasil pemeriksaan fisik"></b-textarea>
							</div>
						</div>
					</div>
					<div ref="Muskuloskeletal pr" class="mt-72">
						<ModernLabel label="Pemerikasaan Muskuloskeletal Perempuan" />
						<div class="mt-20">
							<div v-if="editable.musPr == true" class="w-100">
								<MuskuloskeletalPrView />
							</div>
							<div v-else class="w-100 mx-auto">
								<div class="card">
									<div class="card-header bg-primary-500 text-semi-18 text-white">Muskuloskeletal Perempuan</div>
									<div class="card-body d-flex bg-white">
										<div class="mx-auto col-lg-8">
											<img src="@/@core/assets/images/canvas/Muskuloskeletal-pr.jpg" alt="" class="w-100" style="aspect-ratio: 16/9; object-fit: cover;">
											<b-button @click="editable.musPr = !editable.musPr" size="sm" variant="primary-200" class="w-100">
												<b-icon icon="pencil-square" scale="0.7" />
												<span class="text-bold-16">Start Drawing</span>
											</b-button>
										</div>
									</div>
								</div>
							</div>
							<div class="mt-20">
								<label class="c-form-label">Pemeriksaan Fisik (O)</label>
								<b-textarea rows="3" placeholder="Tulis hasil pemeriksaan fisik"></b-textarea>
							</div>
						</div>
					</div>
				</section>
				<section v-else-if="activeTab === 'paru'" class="d-flex flex-column mt-32">
					<ModernLabel label="Pemeriksaan paru" />
					<div class="mt-20">
						<div v-if="editable.paru == true" class="w-100">
							<ParuView />
						</div>
						<div v-else class="w-100 mx-auto">
							<div class="card">
								<div class="card-header bg-primary-500 text-semi-18 text-white">Paru-Paru</div>
								<div class="card-body d-flex bg-white">
									<div class="mx-auto col-lg-8">
										<img src="@/@core/assets/images/canvas/Paru-Paru.jpg" alt="" class="w-100" style="aspect-ratio: 16/9; object-fit: cover;">
										<b-button @click="editable.paru = !editable.paru" size="sm" variant="primary-200" class="w-100">
											<b-icon icon="pencil-square" scale="0.7" />
											<span class="text-bold-16">Start Drawing</span>
										</b-button>
									</div>
								</div>
							</div>
						</div>
						<div class="mt-20">
							<label class="c-form-label">Pemeriksaan Fisik (O)</label>
							<b-textarea rows="3" placeholder="Tulis hasil pemeriksaan fisik"></b-textarea>
						</div>
					</div>
				</section>
				<section v-else-if="activeTab === 'rongga'" class="d-flex flex-column mt-32">
					<ModernLabel label="Pemeriksaan rongga mulut" />
					<div class="mt-20">
						<div v-if="editable.rongga == true" class="w-100">
							<RonggaMulutView />
						</div>
						<div v-else class="w-100 mx-auto">
							<div class="card">
								<div class="card-header bg-primary-500 text-semi-18 text-white">Rongga Mulut</div>
								<div class="card-body d-flex bg-white">
									<div class="mx-auto col-lg-8">
										<img src="@/@core/assets/images/canvas/Rongga Mulut.jpg" alt="" class="w-100" style="aspect-ratio: 16/9; object-fit: cover;">
										<b-button @click="editable.rongga = !editable.rongga" size="sm" variant="primary-200" class="w-100">
											<b-icon icon="pencil-square" scale="0.7" />
											<span class="text-bold-16">Start Drawing</span>
										</b-button>
									</div>
								</div>
							</div>
						</div>
						<div class="mt-20">
							<label class="c-form-label">Pemeriksaan Fisik (O)</label>
							<b-textarea rows="3" placeholder="Tulis hasil pemeriksaan fisik"></b-textarea>
						</div>
					</div>
				</section>
				<section v-else-if="activeTab === 'telinga'" class="d-flex flex-column mt-32">
					<div ref="Telinga-kanan">
						<ModernLabel label="Pemerikasan Telinga Kanan" />
						<div class="mt-20">
							<div v-if="editable.telKanan == true" class="w-100">
								<TelingaKananView />
							</div>
							<div v-else class="w-100 mx-auto">
								<div class="card">
									<div class="card-header bg-primary-500 text-semi-18 text-white">Telinga Kanan</div>
									<div class="card-body d-flex bg-white">
										<div class="mx-auto col-lg-8">
											<img src="@/@core/assets/images/canvas/Telinga-kanan.jpg" alt="" class="w-100" style="aspect-ratio: 16/9; object-fit: cover;">
											<b-button @click="editable.telKanan = !editable.telKanan" size="sm" variant="primary-200" class="w-100">
												<b-icon icon="pencil-square" scale="0.7" />
												<span class="text-bold-16">Start Drawing</span>
											</b-button>
										</div>
									</div>
								</div>
							</div>
							<div class="mt-20">
								<label class="c-form-label">Pemeriksaan Fisik (O)</label>
								<b-textarea rows="3" placeholder="Tulis hasil pemeriksaan fisik"></b-textarea>
							</div>
						</div>
					</div>
					<div ref="Telinga-kiri" class="mt-72">
						<ModernLabel label="Pemerikasaan Telinga Kiri" />
						<div class="mt-20">
							<div v-if="editable.telKiri == true" class="w-100">
								<TelingaKiriView />
							</div>
							<div v-else class="w-100 mx-auto">
								<div class="card">
									<div class="card-header bg-primary-500 text-semi-18 text-white">Telinga Kiri</div>
									<div class="card-body d-flex bg-white">
										<div class="mx-auto col-lg-8">
											<img src="@/@core/assets/images/canvas/Telinga-kiri.jpg" alt="" class="w-100" style="aspect-ratio: 16/9; object-fit: cover;">
											<b-button @click="editable.telKiri = !editable.telKiri" size="sm" variant="primary-200" class="w-100">
												<b-icon icon="pencil-square" scale="0.7" />
												<span class="text-bold-16">Start Drawing</span>
											</b-button>
										</div>
									</div>
								</div>
							</div>
							<div class="mt-20">
								<label class="c-form-label">Pemeriksaan Fisik (O)</label>
								<b-textarea rows="3" placeholder="Tulis hasil pemeriksaan fisik"></b-textarea>
							</div>
						</div>
					</div>
				</section>
				<section v-else-if="activeTab === 'tenggorokan'" class="d-flex flex-column mt-32">
					<ModernLabel label="Pemeriksaan Tenggorokan" />
					<div class="mt-20">
						<div v-if="editable.tenggorokan == true" class="w-100">
							<TenggorokanView />
						</div>
						<div v-else class="w-100 mx-auto">
							<div class="card">
								<div class="card-header bg-primary-500 text-semi-18 text-white">Tenggorokan</div>
								<div class="card-body d-flex bg-white">
									<div class="mx-auto col-lg-8">
										<img src="@/@core/assets/images/canvas/Tenggorokan.jpg" alt="" class="w-100" style="aspect-ratio: 16/9; object-fit: cover;">
										<b-button @click="editable.tenggorokan = !editable.tenggorokan" size="sm" variant="primary-200" class="w-100">
											<b-icon icon="pencil-square" scale="0.7" />
											<span class="text-bold-16">Start Drawing</span>
										</b-button>
									</div>
								</div>
							</div>
						</div>
						<div class="mt-20">
							<label class="c-form-label">Pemeriksaan Fisik (O)</label>
							<b-textarea rows="3" placeholder="Tulis hasil pemeriksaan fisik"></b-textarea>
						</div>
					</div>
				</section>
				<section v-else-if="activeTab === 'urogenital'" class="d-flex flex-column mt-32">
					<div ref="Urogenital-laki">
						<ModernLabel label="Pemerikasan Urogenital Laki-laki" />
						<div class="mt-20">
							<div v-if="editable.uroLaki == true" class="w-100">
								<UrogenitalLakiView />
							</div>
							<div v-else class="w-100 mx-auto">
								<div class="card">
									<div class="card-header bg-primary-500 text-semi-18 text-white">Urogenital Laki-laki</div>
									<div class="card-body d-flex bg-white">
										<div class="mx-auto col-lg-8">
											<img src="@/@core/assets/images/canvas/Urogenital-laki.jpg" alt="" class="w-100" style="aspect-ratio: 16/9; object-fit: cover;">
											<b-button @click="editable.uroLaki = !editable.uroLaki" size="sm" variant="primary-200" class="w-100">
												<b-icon icon="pencil-square" scale="0.7" />
												<span class="text-bold-16">Start Drawing</span>
											</b-button>
										</div>
									</div>
								</div>
							</div>
							<div class="mt-20">
								<label class="c-form-label">Pemeriksaan Fisik (O)</label>
								<b-textarea rows="3" placeholder="Tulis hasil pemeriksaan fisik"></b-textarea>
							</div>
						</div>
					</div>
					<div ref="urogenital-pr" class="mt-72">
						<ModernLabel label="Pemerikasaan Urogenital Perempuan" />
						<div class="mt-20">
							<div v-if="editable.uroPr == true" class="w-100">
								<UrogenitalPrView />
							</div>
							<div v-else class="w-100 mx-auto">
								<div class="card">
									<div class="card-header bg-primary-500 text-semi-18 text-white">Urogenital Perempuan</div>
									<div class="card-body d-flex bg-white">
										<div class="mx-auto col-lg-8">
											<img src="@/@core/assets/images/canvas/Urogenital-pr.jpg" alt="" class="w-100" style="aspect-ratio: 16/9; object-fit: cover;">
											<b-button @click="editable.uroPr = !editable.uroPr" size="sm" variant="primary-200" class="w-100">
												<b-icon icon="pencil-square" scale="0.7" />
												<span class="text-bold-16">Start Drawing</span>
											</b-button>
										</div>
									</div>
								</div>
							</div>
							<div class="mt-20">
								<label class="c-form-label">Pemeriksaan Fisik (O)</label>
								<b-textarea rows="3" placeholder="Tulis hasil pemeriksaan fisik"></b-textarea>
							</div>
						</div>
					</div>
				</section>

				<!-- <KajianMataView v-if="activeTab === 'kajianMata'" />
				<KajianGigiView v-if="activeTab === 'kajianGigi'" /> -->
			</div>
			<div class="d-flex justify-content-lg-between mt-40">
				<b-button pill variant="outline-neutral-500" class="col col-lg-1">Tutup</b-button>
				<b-button pill variant="indigo" class="col col-lg-2">
					<i><img src="@/@core/assets/icons/nurse/save.svg" alt="save" class="mr-1"></i>
					<span>Simpan</span>
				</b-button>
			</div>
		</b-modal>
	</div>
</template>
<script>
import AbdomenView from './jsView/AbdomenView.vue'
import AnusView from './jsView/AnusView.vue'
import DadaLakiView from './jsView/DadaLakiView.vue'
import DadaPrView from './jsView/DadaPrView.vue'
import EkstrimAtasView from './jsView/EkstrimAtasView.vue'
import EkstrimBawahView from './jsView/EkstrimBawahView.vue'
import HidungView from './jsView/HidungView.vue'
import JantungView from './jsView/JantungView.vue'
import KepalaView from './jsView/KepalaView.vue'
import LeherView from './jsView/LeherView.vue'
import LeherDepanView from './jsView/LeherDepanView.vue'
import MataView from './jsView/MataView.vue'
import MulutView from './jsView/MulutView.vue'
import MuskuloskeletalBayiView from './jsView/MuskuloskeletalBayiView.vue'
import MuskuloskeletalLakiView from './jsView/MuskuloskeletalLakiView.vue'
import MuskuloskeletalPrView from './jsView/MuskuloskeletalPrView.vue'
import ParuView from './jsView/ParuView.vue'
import RonggaMulutView from './jsView/RonggaMulutView.vue'
import TelingaKananView from './jsView/TelingaKananView.vue'
import TelingaKiriView from './jsView/TelingaKiriView.vue'
import TenggorokanView from './jsView/TenggorokanView.vue'
import UrogenitalLakiView from './jsView/UrogenitalLakiView.vue'
import UrogenitalPrView from './jsView/UrogenitalPrView.vue'

import ModernLabel from '@/@core/components/label/ModernLabel.vue'
// import KajianMataView from './jsView/KajianMataView.vue'
// import KajianGigiView from './jsView/KajianGigiView.vue'

export default {
	components: {
		ModernLabel, HidungView, AbdomenView, ParuView, AnusView, DadaLakiView, DadaPrView, EkstrimAtasView, EkstrimBawahView, JantungView, KepalaView, LeherView, LeherDepanView, MataView, MulutView, MuskuloskeletalBayiView, MuskuloskeletalLakiView, MuskuloskeletalPrView, RonggaMulutView, TelingaKananView, TelingaKiriView, TenggorokanView, UrogenitalLakiView, UrogenitalPrView, //KajianMataView, KajianGigiView
	},
	methods: {
		goToTab(tab) {
			this.activeTab = tab;
			// loop through each key in editable and set its value to false
			for (let key in this.editable) {
				this.editable[key] = false;
			}
			console.log(this.activeTab)
		}
	},
	data() {
		return {
			activeTab: 'abdomen',
			showModal: false,
			editable: {
				abdomen: false,
				anus: false,
				dadaLaki: false,
				dadaPr: false,
				ekstrimAtas: false,
				ekstrimBawah: false,
				hidung: false,
				jantung: false,
				kepala: false,
				leher: false,
				leherDepan: false,
				mata: false,
				mulut: false,
				musBayi: false,
				musLaki: false,
				musPr: false,
				paru: false,
				rongga: false,
				telKanan: false,
				telKiri: false,
				tenggorokan: false,
				uroLaki: false,
				uroPr: false,
				// kajianMata: false,
				// kajianGigi: false,
			},
		}
	},
}

</script>
