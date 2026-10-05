<template>
    <div>
        <b-container fluid="xl" class="main-content">
            <div class="d-flex">
                <b-button @click="closeWindow" size="sm" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button @click="printCanvas" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
            </div>
            <div class="canvas-wrapper mt-32">
                <div id="canvas" ref="canvas" class="mt-16" style="position: relative;">
                    <div class="row align-items-center pb-24 fs-14" style="border-bottom: 2px solid black;">
                        <div class="col-2 text-center">
                            <img src="../../../../../@core/assets/images/logo-thb-small.png" width="46%" alt="rs-logo">
                        </div>
                        <div class="col text-center">
                            <p class="text-bold-18">RS Taman Harapan Baru</p>
                            <p>Jl. Kaliabang Tengah No. 2 RT.004 RW.023, Bekasi, Kota Bekasi</p>
                            <p>Info: +62 778 4161212 / 0853 7111 1212</p>

                        </div>
                        <div class="col-2 text-center">
                            <!-- <img src="../../../../../@core/assets/images/lafki-logo.png" width="62%" alt="lafki-logo"> -->
                        </div>
                    </div>

                    <div class="fs-13 mt-24" style="line-height: 120%;">
                        <div class="row">
                            <div class="col-6">
                                <table>
                                    <tr>
                                        <td class="align-top" style="width: 120px;">Nama Pasien</td>
                                        <td class="text-center" style="width: 20px;">:</td>
                                        <td>{{ regData.alias_name }}. {{ regData.name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="align-top">Tanggal Lahir</td>
                                        <td class="text-center">:</td>
                                        <td>{{ regData.birth_date }} ({{ parsedAge(regData.birth_date) }})</td>
                                    </tr>
                                    <tr>
                                        <td class="align-top">No. Rekam Medis</td>
                                        <td class="text-center">:</td>
                                        <td>{{regData.norm}}</td>
                                    </tr>
                                    <tr>
                                        <td class="align-top">Jenis Kelamin</td>
                                        <td class="text-center">:</td>
                                        <td>{{ regData.sex_name }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-6">
                                <table>
                                    <tr>
                                        <td class="align-top" style="width: 120px;">PPA</td>
                                        <td class="text-center" style="width: 20px;">:</td>
                                        <td>{{ sbar_data.user_added_name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="align-top">Jabatan</td>
                                        <td class="text-center">:</td>
                                        <td>{{ sbar_data.user_added_role }}</td>
                                    </tr>
                                    <tr>
                                        <td class="align-top">Ditambahkan pada</td>
                                        <td class="text-center">:</td>
                                        <td>{{localDateTime(sbar_data.date_added)}}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="mt-24">
                            <section>
                                <ModernLabel variant="brown" label="VERIFIKASI"/>
                                <div class="mt-8">
                                    <div class="font-weight-bold">Nama Dokter yang Dikonsulkan:</div>
                                    <div class="mt-12">{{ sbar_data?.doctor_in_charge_name_sbar || '-' }}</div>
                                </div>
                                <div class="mt-12">
                                    <div class="font-weight-bold">Konsultasi Via:</div>
                                    <div class="d-flex mt-12 text-regular-16">
                                        <div class="d-flex pr-3">
                                            <b-icon :icon="getRadioDoctorSbarIcon('1')" :variant="getRadioDoctorSbarIconVariant('1')" class="mt-1 mr-8"/>
                                            <div>
                                                <div class="lh-110">WA (whatsapp)</div>
                                            </div>
                                        </div>
                                        <div class="d-flex pr-3">
                                            <b-icon :icon="getRadioDoctorSbarIcon('2')" :variant="getRadioDoctorSbarIconVariant('2')" class="mt-1 mr-8"/>
                                            <div>
                                                <div class="lh-110">Telefon</div>
                                            </div>
                                        </div>
                                        <div class="d-flex pr-3">
                                                <b-icon :icon="getRadioDoctorSbarIcon('3')" :variant="getRadioDoctorSbarIconVariant('3')" class="mt-1 mr-8" />
                                                <div>
                                                    <div class="lh-110">Lainnya: <span v-if="sbar_data.consultation_via_type_id == '3'">{{ sbar_data.consultation_via_anything_else }}</span></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            </section>
                            <section class="mt-24">
                                <ModernLabel variant="brown" label="SBAR"/>
                                <div class="mt-8">
                                    <div class="font-weight-bold">SBAR:</div>
                                    <div class="mt-12" v-html="parsedBody(sbar_data?.sbar || '-')"></div>
                                </div>
                                <div class="mt-8">
                                    <div class="font-weight-bold">Advice:</div>
                                    <div class="mt-12" v-html="parsedBody(sbar_data?.advice || '-')"></div>
                                </div>
                            </section>
                            <template v-if="sbar_data.user_added_group_id == doctor_group_id">
                                <section class="mt-24">
                                    <ModernLabel variant="brown" label="LABORATORIUM"/>
                                    <div class="mt-8">
                                        <template v-if="sbar_data?.laboratory_request?.length > 0">
                                            <template v-for="(il, i) in sbar_data.laboratory_request">
                                                <div class="mb-12">
                                                    <div class="font-weight-bold">{{localDateTime(il.date_request)}}</div>
                                                    <li v-for="(list, j) in il.laboratory_request_items">{{ list.costing_name }} </li>
                                                </div>
                                            </template>
                                        </template>
                                        <div v-else>-</div>
                                    </div>
                                </section>

                                <section class="mt-24">
                                    <ModernLabel variant="brown" label="RADIOLOGI"/>
                                    <div class="mt-8">
                                        <template v-if="sbar_data?.radiology_request?.length > 0">
                                            <template v-for="(ir, i) in sbar_data.radiology_request">
                                                <div class="mb-12">
                                                    <div class="font-weight-bold">{{localDateTime(ir.date_request)}}</div>
                                                    <li v-for="(list, j) in ir.radiology_request_items">{{ list.radiology_package_name }} </li>
                                                </div>
                                            </template>
                                        </template>
                                        <div v-else>-</div>
                                    </div>
                                </section>

                                <section class="mt-24">
                                    <ModernLabel variant="brown" label="FISIOTERAPI"/>
                                    <div class="mt-8">
                                        <template v-if="sbar_data?.physiotherapy_request?.length > 0">
                                            <template v-for="(phy, i) in sbar_data.physiotherapy_request">
                                                <div class="mb-12">
                                                    <div class="font-weight-bold">{{localDateTime(phy.date_request)}}</div>
                                                    <li v-for="(list, j) in phy.physiotherapy_request_items">{{ list.physiotherapy_package_name }} </li>
                                                </div>
                                            </template>
                                        </template>
                                        <div v-else>-</div>
                                    </div>
                                </section>

                                <section class="mt-24">
                                    <ModernLabel variant="brown" label="RESEP"/>
                                    <div class="mt-8">
                                        <div>
                                            <template v-if="sbar_data?.prescription?.length > 0">
                                                <div v-for="(ipre, i) in sbar_data.prescription">
                                                    <div class="font-weight-bold">{{localDateTime(ipre.date_added)}} ({{ isHomeRecipe(ipre) ? 'Resep Pulang' : 'Kardeks' }})</div>
                                                    <div v-for="(list, idx_p) in ipre.form_prescription_parent" class="mt-1">
                                                        <template v-if="isHomeRecipe(ipre)">
                                                            <div :class="getPrescParentVariant(list.id)" class="px-12 py-1">
                                                                <div class="font-weight-bold">{{ getPrescParentName(list.id) }}</div>
                                                                <div v-if="list.id != 1" class="fs-12 text-italic" style="font-weight: 400">
                                                                    <span>Jumlah: {{ list.value | number}}, </span>
                                                                    <span>{{ list.used }}</span>
                                                                </div>
                                                            </div>
                                                            <div v-if="ipre?.form_prescription" class="mt-1">
                                                                <li v-for="unit in ipre.form_prescription" v-if="list.id == unit.prescription_item_type_id">
                                                                    {{ unit?.pharmacy_catalog_name }}, jml: {{ unit?.qty | number}}
                                                                    <span v-if="unit?.prescription_item_type_id == 1">, {{ unit?.used }} {{ unit?.desc }}</span>
                                                                </li>
                                                            </div>
                                                        </template>
                                                        <template v-else>
                                                            <div :class="getPrescParentVariant(list.id)" class="px-12 py-1">
                                                                <div class="font-weight-bold">
                                                                    {{ getPrescParentName(list.id) }}
                                                                    <b-badge v-if="list.id != 1 && list?.status" :variant="getKardexItemStatusVariant(list?.status)" class="text-uppercase mr-1">{{ getKardexItemStatusName(list?.status) }}</b-badge>
                                                                </div>
                                                                <div v-if="list.id != 1" class="fs-12 text-italic" style="font-weight: 400">
                                                                    <span>Kemasan: {{ getParentPackageName(list?.package) }}, </span>
                                                                    <span>
                                                                        {{ list?.value | number }}
                                                                        TIAP {{ getDynamicOptName(list?.signatura_times_id, signature_time)}}
                                                                        {{ getDynamicOptName(list?.administer_id, catalog_instruction) }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div v-if="ipre?.form_prescription" class="mt-1">
                                                                <li v-for="unit in ipre.form_prescription" v-if="list.id == unit.prescription_item_type_id">
                                                                    <b-badge v-if="unit?.status" :variant="getKardexItemStatusVariant(unit?.status)" class="text-uppercase mr-1">{{ getKardexItemStatusName(unit?.status) }}</b-badge>
                                                                    {{ unit?.pharmacy_catalog_name }}, {{ unit?.dose }} {{ getDynamicOptName(unit?.dose_unit_id, signature_units) }}
                                                                    <span v-if="unit?.prescription_item_type_id == 1">
                                                                        TIAP {{ getDynamicOptName(unit?.signatura_times_id, signature_time)}}
                                                                        {{ getDynamicOptName(unit?.administer_id, catalog_instruction) }}
                                                                    </span>
                                                                </li>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </div>
                                            </template>
                                            <div v-else>-</div>
                                        </div>
                                    </div>
                                    <div class="mt-12">
                                        <div class="font-weight-bold">NON-FARMAKOLOGI:</div>
                                        <div>
                                            <li v-for="inf in sbar_data?.kardex_non_pharmacological">
                                                <div v-html="parsedBody(inf.name || '-')"></div>
                                            </li>
                                            <div v-if="sbar_data?.plan" v-html="parsedBody(sbar_data?.plan || '-')"></div>
                                        </div>
                                    </div>
                                    <div v-if="sbar_data?.pharmacological_plan" class="mt-12">
                                        <div class="font-weight-bold">FARMAKOLOGI:</div>
                                        <div v-html="parsedBody(sbar_data?.pharmacological_plan || '-')"></div>
                                    </div>
                                </section>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </b-container>
    </div>
</template>
<script>
// import moment from 'moment'
import dateMixin from "@/mixins/dateMixin";
import optionMixin from '@/mixins/optionMixin';

export default {
    mixins: [dateMixin, optionMixin],

    data() {
        return {
            admin_group_id: 'ddad321c-a9d8-430c-95f0-de38d20834a1',
            doctor_group_id: 'efb3fdff-fbe3-4954-a391-3729e544b62a',
            nurse_group_id: '29644626-5b54-4d54-ab69-22146a631277',
            sbar_data: {},
            regData: {},
            signature_time: [],
            catalog_instruction: [],
            signature_units: [],
            // today: moment(new Date()).locale('id').format('LL'),
            // Profesional Pemberi Asuhan (PPA)
        }
    },

    methods: {
        async getMySbar(sbar_id){
            try {
                this.$store.dispatch('sbar/getBySBARId', sbar_id).then(async response => {
                    let res_data = response[0] || null
                    console.log(res_data, 'sbar_data')
                    this.sbar_data = res_data

                    await this.getData(this.sbar_data.registration_destination_id)
                }, error => {
                    console.error('Error while GET sbar/getBySBARId at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET sbar/getBySBARId at: ', error);
            }
        },

        async getData(reg_des_id) {
            this.$store.dispatch('registration/getById', reg_des_id).then(response => {
                this.regData = response[0]
                // console.log(this.regData);
            })
        },

        getRadioDoctorSbarIcon(value) {
            if (this.sbar_data && this.sbar_data.consultation_via_type_id) {
                return this.sbar_data.consultation_via_type_id == value ? "check-circle-fill" : "circle-fill";
            } else {
                return "circle-fill";
            }
        },
        getRadioDoctorSbarIconVariant(value) {
            if (this.sbar_data && this.sbar_data.consultation_via_type_id) {
                return this.sbar_data.consultation_via_type_id == value ? "success" : "neutral-200";
            } else {
                return "neutral-200";
            }
        },

        isHomeRecipe(obj) {
            if (obj?.type_prescription_id == 2) return true
            return false
        },

        async getOptSignTime() {
            try {
                await this.$store.dispatch('kardex/getOptSignaturaTime')
                .then(response => {
                    this.signature_time = response
                })
                .catch(({message, debug, response}) => {
                    console.error('Error at: ', response);
                })
            } catch (error) {
                console.error('Error catch at: ', error)
            }
        },

        async getOptCatInstruction() {
            try {
                await this.$store.dispatch('catalog/getOptCatalogInstruction')
                .then(response => {
                    this.catalog_instruction = response
                })
                .catch(({message, debug, response}) => {
                    console.error('Error at: ', response);
                })
            } catch (error) {
                console.error('Error catch at: ', error)
            }
        },

        async getSignaturaUnits() {
            try {
                await this.$store.dispatch('kardex/getOptUnit', {})
                .then(
                    async response => {
                        this.signature_units = response
                    }
                )
                .catch(({message, debug, response}) => {
                    console.error('Error GET kardex/getOptUnit at: ', response);
                })
            } catch (error) {
                console.error('Error catch while GET kardex/getOptUnit at: ', error);
            }
        },

        handleKeyPress(event) {
            if (event.ctrlKey && event.key === 'p') {
                event.preventDefault();
                this.printCanvas();
            }
        },

        closeWindow() {
            window.close()
        },

        printCanvas() {
            const canvas = this.$refs.canvas;
            const originalContents = document.body.innerHTML;
            document.body.innerHTML = canvas.innerHTML;
            window.print();
            document.body.innerHTML = originalContents;
            location.reload()
        },

        parsedBody(text){
            return String(text || '-').replace(/\n/g, "<br>");
        },
    },

    mounted() {
        window.addEventListener('keydown', this.handleKeyPress);
    },

    beforeDestroy() {
        window.removeEventListener('keydown', this.handleKeyPress);
    },

    created() {
        if (this.$route.params.sbar_id) {
            this.getMySbar(this.$route.params.sbar_id)
        }
        this.getOptSignTime()
        this.getOptCatInstruction()
        this.getSignaturaUnits()
    },
};

</script>
<style scope>

@media print {
    @page {
        size: A4;
        margin: 16mm;
    }
    td {
        padding: 2px 0;
        border: 0;
    }
}

td {
    padding: 2px 0;
}

p {
    margin: 0;
    padding: 0;
}

.main-content {
    margin-top: 32px;
    padding-bottom: 32px;
}

.canvas-wrapper {
    display: flex;
    justify-content: center;
    padding-bottom: 40px;
}
</style>
