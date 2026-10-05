<template>
	<div>
		<div class="card">
			<div class="card-header bg-primary text-white">Odontogram</div>
			<div class="card-body bg-c-info d-flex">
				<div class="col-12 mx-auto px-0 px-lg-3">
					<div class="bg-white rounded">
						<div style="overflow: hidden; background-color: #eaebef;">
							<div ref="odontogramCanvas" class="odontogram-input"></div>
						</div>
					</div>

					<div class="mt-3 bg-white">
						<table class="table table-bordered table-sm mb-0 odontogram-table">
							<tbody>
								<tr v-for="(row, rowIdx) in tableRows" :key="rowIdx">
									<td v-for="cell in row" :key="cell">
										<strong>{{ toothLabel(cell) }}</strong> : {{ toothDescription(cell) }}
									</td>
								</tr>
							</tbody>
						</table>

						<div class="row mx-0 p-2">
							<div class="col d-flex justify-content-end">
								<b-button
									variant="success"
									class="mr-2"
									@click="saveOdontogram"
								>
									<b-icon icon="check-circle"></b-icon>
									Simpan
								</b-button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Modal kondisi gigi -->
		<b-modal ref="toothModal" :title="modalTitle" hide-footer>
			<div class="row">
				<div class="col-sm-6">
					<div
						class="checkbox odontogram-checkbox"
						v-for="item in conditionsCol1"
						:key="item.value"
						v-show="!isHiddenCondition(item)"
					>
						<label>
							<input type="checkbox" v-model="currentConditions" :value="item.value">
							{{ item.label }}
						</label>
					</div>
				</div>
				<div class="col-sm-6">
					<div
						class="checkbox odontogram-checkbox"
						v-for="item in conditionsCol2"
						:key="item.value"
					>
						<label>
							<input type="checkbox" v-model="currentConditions" :value="item.value">
							{{ item.label }}
						</label>
					</div>
				</div>
			</div>
			<div class="modal-footer px-0 pb-0">
				<b-button variant="primary" @click="saveToothCondition">
					Simpan
				</b-button>
			</div>
		</b-modal>
	</div>
</template>

<script>
import $ from 'jquery'
import '@/views/pages/canvasing/jsCanvas/sztoothcanvas.js'

export default {
	data() {
		return {
			toothCanvas: null,
			description: {},

			currentToothId: null,
			currentQuadran: null,
			currentConditions: [],

			// Tabel 4 kolom (kuadran 1/2/3/4) x 8 baris (posisi gigi 8 s/d 1),
			// mengikuti tata letak visual odontogram: 18-28-38-48, 17-27-37-47, dst.
			tableRows: [
				['t18', 't28', 't38', 't48'],
				['t17', 't27', 't37', 't47'],
				['t16', 't26', 't36', 't46'],
				['t15,t55', 't25,t65', 't35,t75', 't45,t85'],
				['t14,t54', 't24,t64', 't34,t74', 't44,t84'],
				['t13,t53', 't23,t63', 't33,t73', 't43,t83'],
				['t12,t52', 't22,t62', 't32,t72', 't42,t82'],
				['t11,t51', 't21,t61', 't31,t71', 't41,t81'],
			],

			conditionsCol1: [
				{ value: 'karies_oklusal', label: 'Karies Oklusal' },
				{ value: 'karies_distal', label: 'Karies Distal' },
				{ value: 'karies_mesial', label: 'Karies Mesial' },
				{ value: 'karies_bukal', label: 'Karies Bukal' },
				{ value: 'karies_palatal', label: 'Karies Palatal', hideFor: ['3', '4', '7', '8'] },
				{ value: 'karies_lingual', label: 'Karies Lingual', hideFor: ['1', '2', '5', '6'] },
				{ value: 'karies_servikal', label: 'Karies Servikal' },
				{ value: 'karies_sekunder', label: 'Karies Sekunder' },
				{ value: 'migrasi_bukal', label: 'Migrasi Bukal' },
				{ value: 'migrasi_palatal', label: 'Migrasi Palatal' },
				{ value: 'migrasi_mesial', label: 'Migrasi Mesial' },
				{ value: 'migrasi_distal', label: 'Migrasi Distal' },
				{ value: 'gigi_goyang_1', label: 'Gigi Goyang / Mobilitas gigi 1°' },
				{ value: 'gigi_goyang_2', label: 'Gigi Goyang / Mobilitas gigi 2°' },
				{ value: 'gigi_goyang_3', label: 'Gigi Goyang / Mobilitas gigi 3°' },
				{ value: 'gigi_goyang_4', label: 'Gigi Goyang / Mobilitas gigi 4°' },
			],
			conditionsCol2: [
				{ value: 'rot_cw', label: 'Rotasi (kanan)' },
				{ value: 'rot_ccw', label: 'Rotasi (kiri)' },
				{ value: 'sisa_akar', label: 'Sisa Akar' },
				{ value: 'impaksi', label: 'Impaksi' },
				{ value: 'edentulous_ridge', label: 'Edentulous Ridge' },
				{ value: 'gigi_tiruan_sebagian', label: 'Gigi Tiruan Sebagian' },
				{ value: 'gigi_tiruan_cekat', label: 'Gigi Tiruan Cekat' },
				{ value: 'crowded', label: 'Crowded' },
				{ value: 'diasterna', label: 'diasterna' },
				{ value: 'gangren_pulpa', label: 'Gangren Pulpa' },
				{ value: 'tumpatan', label: 'Tumpatan / Crown / Inlay / Onlay' },
				{ value: 'persistensi', label: 'Persistensi' },
			],
		}
	},
	props: {
		toothConditions: {
			type: String,
			default: null
		}
	},
	computed: {
		modalTitle() {
			if (!this.currentToothId) return 'Gigi'
			return 'Gigi ' + this.currentToothId.substr(1, 3)
		}
	},
	methods: {
		saveOdontogram() {
			const conditions = this.toothCanvas.szToothCanvas('getToothConditions')
			this.$emit('save', conditions)
		},
		saveToothCondition() {
			if (!this.currentToothId) return

			const toothCondition = {}
			toothCondition[this.currentToothId] = [...this.currentConditions]

			this.toothCanvas.szToothCanvas('setToothConditions', toothCondition)
			this.refreshDescriptions()

			this.$refs.toothModal.hide()
		},
		refreshDescriptions() {
			this.description = this.toothCanvas.szToothCanvas('getToothDescriptions')
		},
		toothDescription(id) {
			return id
				.split(',')
				.map((key) => this.description[key])
				.filter((val) => !!val)
				.join(', ')
		},
		toothLabel(id) {
			return id
				.split(',')
				.map((key) => key.replace('t', ''))
				.join(' / ')
		},
		isHiddenCondition(item) {
			return !!(item.hideFor && this.currentQuadran && item.hideFor.includes(this.currentQuadran))
		}
	},
	watch: {
		toothConditions(val) {
			if (!val || !this.toothCanvas) return

			let parsed
			try {
				parsed = JSON.parse(val)
			} catch (e) {
				console.warn('Invalid toothConditions JSON:', e)
				return
			}
			
			this.$nextTick(() => {
				this.toothCanvas.szToothCanvas('setToothConditions', val)
				this.refreshDescriptions()
			})
		}
	},
	mounted() {
		// Canvas odontogram
		let initialConditions
		try {
			initialConditions = this.toothConditions ? JSON.parse(this.toothConditions) : undefined
		} catch (e) {
			console.warn('Invalid toothConditions JSON:', e)
			initialConditions = undefined
		}

		this.toothCanvas = $(this.$refs.odontogramCanvas).szToothCanvas({
			responsive: true,
			toothConditions: initialConditions,
		})

		this.refreshDescriptions()

		const self = this
		this.toothCanvas.on('click', function (e) {
			const toothID = $(this).szToothCanvas('getNearestTooth', e.offsetX, e.offsetY)
			if (!toothID) return

			const toothCondition = $(this).szToothCanvas('getToothConditions')
			const quadran = toothID.substr(1, 1)

			self.currentToothId = toothID
			self.currentQuadran = quadran
			self.currentConditions = toothCondition[toothID] ? [...toothCondition[toothID]] : []

			self.$refs.toothModal.show()
		})
	},
}
</script>

<style scoped>
canvas {
	border: 0;
	overflow: hidden;
	width: 100%;
}

.odontogram-checkbox label {
	font-weight: normal;
}

.odontogram-table {
	table-layout: fixed;
	width: 100%;
}

.odontogram-table td {
	vertical-align: top;
	word-break: break-word;
}
</style>