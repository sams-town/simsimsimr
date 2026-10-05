<template>
    <div>
        <b-modal v-model="active.history_modal" scrollable size="md" hide-footer>
            <template #modal-header="{ close }">
                <div>
                    <div class="fs-13 font-weight-bold text-uppercase">
                        RIWAYAT PENUNJANG
                        <span class="text-primary">{{ temp_header.alias_name }}. {{ temp_header.name }} | {{localDate(temp_header.birth_date)}} ({{ parsedAge(temp_header.birth_date) }}) | {{ temp_header.norm }}</span>
                    </div>
                    <div class="d-flex">
                        <b-form-checkbox size="sm" :value="true" :unchecked-value="false" class="mr-2" v-model="active.laboratory_status">
                            <span class="fs-12">LABORATORIUM</span>
                        </b-form-checkbox>
                        <b-form-checkbox size="sm" :value="true" :unchecked-value="false" class="mr-2" v-model="active.radiology_status">
                            <span class="fs-12">RADIOLOGI</span>
                        </b-form-checkbox>
                        <b-form-checkbox size="sm" :value="true" :unchecked-value="false" class="mr-2" v-model="active.physiotherapy_status">
                            <span class="fs-12">FISIOTERAPI</span>
                        </b-form-checkbox>
                        <b-form-checkbox size="sm" :value="true" :unchecked-value="false" class="mr-2" v-model="active.examination_status">
                            <span class="fs-12">PENUNJANG LAINNYA</span>
                        </b-form-checkbox>
                    </div>
                </div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>

            <section v-if="active.laboratory_status">
                <ModernLabel label="Laboratorium" />
                <template v-if="history?.laboratory?.length > 0">
                    <template v-for="item in history?.laboratory">
                        <div class="card mt-12">
                            <div class="card-body p-10">
                                <table class="fs-13">
                                    <tr>
                                        <td style="width: 100px;">Dokter</td>
                                        <td style="width: 20px;">:</td>
                                        <td>{{ item.doctor_in_charge_name }}</td>
                                    </tr>
                                    <tr>
                                        <td>Waktu Order</td>
                                        <td>:</td>
                                        <td>{{ localDateTime(item.date_request) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Instalasi</td>
                                        <td>:</td>
                                        <td>{{ item.health_facility_name }} - {{ item.health_facility_item_name }}</td>
                                    </tr>
                                </table>
                                <b-table :fields="supportFields" :items="item.laboratory_request_items" bordered small head-variant="light" class="mt-12 mb-0" thead-tr-class="fs-13" tbody-tr-class="fs-12">
                                    <template #cell(no)="row">
                                        <div class="text-center">{{ row.index+1 }}</div>
                                    </template>
                                    <template #cell(costing_name)="row">
                                        <div>{{ row.item.laboratory_package_name }}</div>
                                    </template>
                                    <template #cell(status)="row">
                                        <div class="text-center">
                                            <template v-if="row.item.result?.length > 0">
                                                <!-- <b-button @click="seeLaboratoryResult({ result: row.item.result, costing_name: row.item.laboratory_package_name, laboratory_request_id: row.item.laboratory_request_id})" variant="w3green" size="sm"> -->
                                                <b-button @click="triggerLabResult({ alias_name: temp_header.alias_name, name: temp_header.name, result: row.item.result, url: row.item.url, costing_name: row.item.laboratory_package_name, laboratory_request_id: row.item.laboratory_request_id})" variant="w3green" size="sm">
                                                    <span style="line-height: 100%;">Lihat Hasil</span>
                                                </b-button>
                                            </template>
                                        </div>
                                    </template>
                                </b-table>
                            </div>
                        </div>
                    </template>
                    <template v-if="lab_load.limit_display < lab_load.count">
                        <div class="d-flex justify-content-center align-items-center">
                            <div @click="loadMoreLaboratory" class="font-weight-bold pointer fs-13 text-center py-1 load-text">
                                <a class="mr-2">Load more</a>
                                <feather-icon icon="ArrowDownIcon" size="14"/>
                            </div>
                        </div>
                    </template>
                </template>
                <div v-else class="text-center text-italic fs-13">Tidak ada order laboratorium yang dibuat pada pasien ini</div>
            </section>
            
            <section :class="active.laboratory_status == true ? 'mt-32' : 'mt-0'" v-if="active.radiology_status">
                <ModernLabel label="Radiologi" />
                <template v-if="history?.radiology?.length > 0">
                    <template v-for="item in history?.radiology">
                        <div class="card mt-12">
                            <div class="card-body p-10">
                                <table class="fs-13">
                                    <tr>
                                        <td style="width: 100px;">Dokter</td>
                                        <td style="width: 20px;">:</td>
                                        <td>{{ item.doctor_in_charge_name }}</td>
                                    </tr>
                                    <tr>
                                        <td>Waktu Order</td>
                                        <td>:</td>
                                        <td>{{ localDateTime(item.date_request) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Instalasi</td>
                                        <td>:</td>
                                        <td>{{ item.health_facility_name }} - {{ item.health_facility_item_name }}</td>
                                    </tr>
                                </table>
                                <b-table :fields="supportFields" :items="item.radiology_request_items" bordered small head-variant="light" class="mt-12 mb-0" thead-tr-class="fs-13" tbody-tr-class="fs-12">
                                    <template #cell(no)="row">
                                        <div class="text-center">{{ row.index+1 }}</div>
                                    </template>
                                    <template #cell(costing_name)="row">
                                        <div>{{ row.item.radiology_package_name }}</div>
                                    </template>
                                    <template #cell(status)="row">
                                        <div class="text-center">
                                            <span v-if="row.item?.url?.length == 0">Belum terbit hasil</span>
                                            <!-- <template v-else-if="row.item.url.length == 2 || row.item.url.length == 3"> -->
                                            <template v-else-if="row.item?.url?.length > 0">
                                                <!-- <b-button @click="seeRadiologyResult({ url: row.item.url, costing_name: row.item.radiology_package_name})" variant="w3green" size="sm"> -->
                                                <b-button @click="triggerRadResult({ alias_name: temp_header.alias_name, name: temp_header.name, url: row.item.url, costing_name: row.item.radiology_package_name})" variant="w3green" size="sm">
                                                    <span style="line-height: 100%;">Lihat Hasil</span>
                                                </b-button>
                                            </template>
                                        </div>
                                    </template>
                                </b-table>
                            </div>
                        </div>
                    </template>
                    <template v-if="rad_load.limit_display < rad_load.count">
                        <div class="d-flex justify-content-center align-items-center">
                            <div @click="loadMoreRadiology" class="font-weight-bold pointer fs-13 text-center py-1 load-text">
                                <a class="mr-2">Load more</a>
                                <feather-icon icon="ArrowDownIcon" size="14"/>
                            </div>
                        </div>
                    </template>
                </template>
                <div v-else class="text-center text-italic fs-13">Tidak ada order radiologi yang dibuat pada pasien ini</div>
            </section>
            
            <section :class="active.laboratory_status == true || active.radiology_status == true ? 'mt-32' : 'mt-0'" v-if="active.physiotherapy_status">
                <ModernLabel label="Fisioterapi"/>
                <template v-if="history?.physiotherapy?.length > 0">
                    <template v-for="item in history?.physiotherapy">
                        <div class="card mt-12">
                            <div class="card-body p-10">
                                <table class="fs-13">
                                    <tr>
                                        <td style="width: 100px;">Dokter</td>
                                        <td style="width: 20px;">:</td>
                                        <td>{{ item.doctor_in_charge_name }}</td>
                                    </tr>
                                    <tr>
                                        <td>Waktu Order</td>
                                        <td>:</td>
                                        <td>{{ localDateTime(item.date_request) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Instalasi</td>
                                        <td>:</td>
                                        <td>{{ item.health_facility_name }} - {{ item.health_facility_item_name }}</td>
                                    </tr>
                                </table>
                                <b-table :fields="physiotherapyFields" :items="item.physiotherapy_request_items" bordered small head-variant="light" class="mt-12 mb-0" thead-tr-class="fs-13" tbody-tr-class="fs-12">
                                    <template #cell(no)="row">
                                        <div class="text-center">{{ row.index+1 }}</div>
                                    </template>
                                </b-table>
                            </div>
                        </div>
                    </template>
                    <template v-if="phy_load.limit_display < phy_load.count">
                        <div class="d-flex justify-content-center align-items-center">
                            <div @click="loadMorePhysiotherapy" class="font-weight-bold pointer fs-13 text-center py-1 load-text">
                                <a class="mr-2">Load more</a>
                                <feather-icon icon="ArrowDownIcon" size="14"/>
                            </div>
                        </div>
                    </template>
                </template>
                <div v-else class="text-center text-italic fs-13">Tidak ada order fisioterapi yang dibuat pada pasien ini</div>
            </section>

            <section :class="active.laboratory_status == true || active.radiology_status == true || active.physiotherapy_status == true ? 'mt-32' : 'mt-0'" v-if="active.examination_status">
                <ModernLabel label="Penunjang Lainnya"/>
                <template v-if="examination_list?.length > 0">
                    <template v-for="(item, index) in examination_list">
                        <div class="card mt-12">
                            <div class="card-header bg-ax-primary-400 text-white">
                                <div class="fs-14 text-uppercase" style="font-weight: 600;">{{ item.category_examination_name }}</div>
                            </div>
                            <div class="card-body p-0">
                                <template v-if="item.data?.files?.length > 0">
                                    <b-table :fields="examFields" :items="item.data.files" bordered small head-variant="light" class="mb-0" thead-tr-class="fs-13" tbody-tr-class="fs-12">
                                        <template #cell(no)="row">
                                            <div class="text-center">{{ row.index+1 }}</div>
                                        </template>
                                        <template #cell(url)="row">
                                            <div class="mb-2 fs-13">
                                                <b>Expertise : </b>
                                                <span>{{ row.item.expertise || '-' }}</span>
                                            </div>
                                            <template v-if="isPdfFile(row.item.filename)">                                                
                                                <div v-if="!supportsPDF">
                                                    <p>Browser tidak mendukung preview PDF</p>
                                                </div>
                                                <iframe
                                                    v-else
                                                    :src="urlAsset+row.value"
                                                    @error="onImageError"
                                                    width="100%"
                                                    height="600px"
                                                    style="border: 1px solid #eee; border-radius: 4px;"
                                                ></iframe>
                                            </template>
                                            <b-img v-else @error="onImageError" rounded :src="urlbe+row.value" style="width: 100%;" />
                                        </template>
                                        <template #cell(examination_date)="row">
                                            <div class="text-center">{{ row.value }}</div>
                                            <div class="d-flex justify-content-center mt-8">
                                                <template v-if="isPdfFile(row.item.filename)">
                                                    <a :href="urlbe+row.item.url" download>Download</a>
                                                </template>
                                                <template v-else>
                                                    <b-button @click="expandImage(row.item.url)" v-b-popover.hover.top="'Expand Gambar'" size="sm" variant="primary">
                                                        <feather-icon icon="ExternalLinkIcon" />
                                                    </b-button>
                                                </template>
                                            </div>
                                        </template>
                                    </b-table>  
                                </template>
                            </div>
                        </div>
                    </template>
                </template>
                <div v-else class="text-center text-italic fs-13">Tidak ada riwayat penunjang lainnya yang ditambahkan pada pasien ini</div>
            </section>
        </b-modal>

        <LabResultModalView ref="labModalResult"/>
        <RadResultModalView ref="radModalResult"/>
    </div>
</template>
<script>
import dateMixin from "@/mixins/dateMixin";
import LabResultModalView from "./LabResultModalView.vue";
import RadResultModalView from "./RadResultModalView.vue";

export default {
    components: {LabResultModalView, RadResultModalView},
    mixins: [dateMixin],
    data() {
        return {
            urlbe:process.env.VUE_APP_IMAGE,
            urlbe_:process.env.VUE_APP_IMAGE_,
            urlAsset:process.env.VUE_APP_ASSETS_BE,
            supportsPDF: false,

            supportFields: [
                { key: 'no', label: 'No', thStyle: { width: '40px', textAlign: 'center' } },
                { key: 'status', label: 'Hasil', thStyle: { width: '130px', textAlign: 'center' } },
                { key: 'costing_name', label: 'Rincian Pemeriksaan' },
            ],
            physiotherapyFields: [
                { key: 'no', label: 'No', thStyle: { width: '40px', textAlign: 'center' } },
                { key: 'physiotherapy_package_name', label: 'Rincian Pemeriksaan' },
            ],
            examination_list: [],
            examFields: [
                { key: 'no', label: 'No', thStyle: { width: '40px', textAlign: 'center' } },
                { key: 'url', label: 'Pemeriksaan' },
                { key: 'examination_date', label: 'Tanggal' , thStyle: { width: '150px', textAlign: 'center' }},
            ],
            active: {
                history_modal: false,
                laboratory_status: true,
                radiology_status: true,
                physiotherapy_status: true,
                examination_status: true,
            },
            temp_header: {
                name: '',
                alias_name: '',
                birth_date: '',
                norm: '',
            },

            history: {
                laboratory: [],
                radiology: [],
                physiotherapy: [],
            },

            lab_load: {
                count: 0,
                limit_display: 3,
                add_display: 3,
            },

            rad_load: {
                count: 0,
                limit_display: 3,
                add_display: 3,
            },

            phy_load: {
                count: 0,
                limit_display: 3,
                add_display: 3,
            },
        }
    },

    methods: {
        onImageError() {
            this.urlbe = process.env.VUE_APP_IMAGE_
        },
        isPdfFile(filename) {
            if (!filename || typeof filename !== 'string') return false;
            const last4Chars = filename.slice(-4).toLowerCase();
            return last4Chars === '.pdf';
        },
        parentTrigger(message) {
            this.temp_header.alias_name = message.alias_name
            this.temp_header.name = message.name
            this.temp_header.birth_date = message.birth_date
            this.temp_header.norm = message.norm

            // history
            this.history.laboratory = []
            this.lab_load.limit_display = 3
            this.getLaboratoryHistory(message.norm)

            this.history.radiology = []
            this.rad_load.limit_display = 3
            this.getRadiologyHistory(message.norm)

            this.history.physiotherapy = []
            this.phy_load.limit_display = 3
            this.getPhysiotherapyHistory(message.norm)

            this.getExamination(message.norm)

            this.openHistoryModal()
            // console.log('message: ', message);
        },
        openHistoryModal() {
            this.active.history_modal = true
        },

        async getLaboratoryHistory(norm) {
            const param = { 
                norm: norm,
                limit: this.lab_load.limit_display
            }
            try {
                await this.$store.dispatch('laboratory/getLaboratoryHistoryV2', param)
                .then(response => {
                    this.history.laboratory = response.rows
                    this.lab_load.count = response.iTotal                    
                })
                .catch(({message, debug, response}) => {
                    console.error('Error GET laboratory/getLaboratoryHistoryV2 at: ', response);
                })
            } catch (error) {
                console.error('Error catch while GET laboratory/getLaboratoryHistoryV2 at: ', error)
            }
        },

        loadMoreLaboratory() {
            this.lab_load.limit_display += this.lab_load.add_display
            this.getLaboratoryHistory(this.temp_header.norm)
        },

        async getRadiologyHistory(norm) {
            const param = { 
                norm: norm,
                limit: this.rad_load.limit_display
            }
            try {
                await this.$store.dispatch('radiology/getRadiologyHistoryV2', param)
                .then(response => {
                    this.history.radiology = response.rows
                    this.rad_load.count = response.iTotal                    
                })
                .catch(({message, debug, response}) => {
                    console.error('Error GET radiology/getRadiologyHistoryV2 at: ', response);
                })
            } catch (error) {
                console.error('Error catch while GET radiology/getRadiologyHistoryV2 at: ', error)
            }
        },

        loadMoreRadiology() {
            this.rad_load.limit_display += this.rad_load.add_display
            this.getRadiologyHistory(this.temp_header.norm)
        },

        async getPhysiotherapyHistory(norm) {
            const param = { 
                norm: norm,
                limit: this.phy_load.limit_display
            }
            try {
                await this.$store.dispatch('physiotherapy/getPhysiotherapyHistoryV2', param)
                .then(response => {
                    this.history.physiotherapy = response.rows
                    this.phy_load.count = response.iTotal                    
                })
                .catch(({message, debug, response}) => {
                    console.error('Error GET physiotherapy/getPhysiotherapyHistoryV2 at: ', response);
                })
            } catch (error) {
                console.error('Error catch while GET physiotherapy/getPhysiotherapyHistoryV2 at: ', error)
            }
        },

        loadMorePhysiotherapy() {
            this.phy_load.limit_display += this.phy_load.add_display
            this.getPhysiotherapyHistory(this.temp_header.norm)
        },

        getExamination(norm){
            try {
                this.$store.dispatch('soap/getExamination', {norm: norm}).then(response => {
                    this.examination_list = response
                    // console.log('this.examination_list:', this.examination_list);
                })
            } catch (error) {
                console.error('Error catch while GET soap/getExamination at: ', error)
            }
        },
        expandImage(url){
            const param = this.urlbe + url;
            const newWindow = window.open('', '_blank');
            
            newWindow.document.write(`
                <html>
                    <head>
                        <title>Image Preview</title>
                        <style>
                            body, html {
                                margin: 0;
                                padding: 0;
                                height: 100%;
                                width: 100%;
                            }
                            img {
                                width: 100%;
                                height: auto;
                            }
                        </style>
                    </head>
                    <body>
                        <img src="${param}" alt="Image Preview">
                    </body>
                </html>
            `);
            newWindow.document.close();
        },
        
        triggerRadResult(message) {
            this.$refs.radModalResult.seeRadiologyResult(message);
        },

        triggerLabResult(message) {
            this.$refs.labModalResult.seeLaboratoryResult(message);
        },

    },
    mounted() {
        this.supportsPDF = !!document.createElement('iframe').src;
    }
}
</script>

<style scoped>    
    .load-text{
        color: blue;
    }
    
    .load-text:hover{
        color: darkblue;
        text-decoration: underline;
        background-color: antiquewhite;
        width: 100%;
    }
</style>