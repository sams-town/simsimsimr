<template>
    <b-modal ref="radModalResult" scrollable size="lg" hide-footer>
        <template #modal-header="{ close }">
            <div class="d-flex justify-content-between w-100">
                <div class="text-uppercase font-weight-bold fs-13 pr-16">
                    Hasil Radiologi  
                    <span class="text-primary-500">{{ temp_rad_result.alias_name }}. {{ temp_rad_result.name }} - {{ temp_rad_result.costing_name }}</span>
                </div>
                <feather-icon icon="XIcon" @click="close()" class="pointer"/>
            </div>
        </template>
        <b-table :fields="radiologyResultField" :items="temp_rad_result.arryData" bordered responsive small thead-tr-class="fs-14" tbody-tr-class="fs-13">
            <template #cell(no)="row">
                <div class="text-center">{{ row.index+1 }}</div>
            </template>
            <template #cell(expertise)="row">
                <table class="w-100 custom-border">
                    <tr>
                        <td style="width: 130px;">Pemeriksa Radiologi</td>
                        <td class="align-top text-center" style="width: 20px;">:</td>
                        <td>{{ row.item.user_lab_name }}</td>
                    </tr>
                    <tr>
                        <td>Verifikator</td>
                        <td class="align-top text-center">:</td>
                        <td>{{ row.item.doctor_lab_name }}</td>
                    </tr>
                    <tr>
                        <td>Tanggal Order</td>
                        <td class="align-top text-center">:</td>
                        <td>{{ localDate(row.item?.date_request || '') }}</td>
                    </tr>
                    <tr>
                        <td>Tanggal Diproses</td>
                        <td class="align-top text-center">:</td>
                        <td>{{ localDate(row.item?.date_process || '') }}</td>
                    </tr>
                    <tr>
                        <td>Expertise</td>
                        <td class="align-top text-center">:</td>
                        <td><span v-html="parsedBody(row.item.conclusion)"></span></td>
                    </tr>
                    <tr>
                        <td>Kesan</td>
                        <td class="align-top text-center">:</td>
                        <td><span v-html="parsedBody(row.item.advice)"></span></td>
                    </tr>
                </table>
            </template>
            <template #cell(url)="data">
                <b-img rounded :src="urlbe+data.value" style="width: 100%;" />
            </template>
            <template #cell(action)="row">
                <div class="d-flex">
                    <b-button 
                        @click="blankToPrint({
                            radiology_request_id: row.item.radiology_request_id,
                            radiology_request_item_id: row.item.radiology_request_item_id,
                            image_id: row.item.id
                        })"
                        v-b-popover.hover.top="'Cetak'" 
                        size="sm"
                        variant="w3green"
                        class="mr-1"
                    >
                        <feather-icon icon="PrinterIcon" />
                    </b-button>
                    <b-button @click="expandImage(row.item.url)" v-b-popover.hover.top="'Expand Gambar'" size="sm" variant="primary">
                        <feather-icon icon="ExternalLinkIcon" />
                    </b-button>
                </div>
            </template>
        </b-table>
    </b-modal>
</template>

<script>
import dateMixin from "@/mixins/dateMixin";

export default {
    mixins: [dateMixin],
    data() {
        return {
            urlbe:process.env.VUE_APP_IMAGE,
            radiologyResultField: [
                { key: 'no', label: 'No', thStyle: { width: '40px', textAlign: 'center' }  },
                { key: 'expertise', label: 'Expertise', thStyle:'width: 400px' },
                { key: 'url', label: 'Gambar' },
                { key: 'action', label: 'Aksi', thStyle: { width: '100px' }  },
            ],
            temp_rad_result: {
                arryData: '',
                costing_name: '',
            },
        }
    },

    methods: {
        seeRadiologyResult(obj){
            this.temp_rad_result.alias_name = obj.alias_name
            this.temp_rad_result.name = obj.name
            this.temp_rad_result.arryData = obj.url
            this.temp_rad_result.costing_name = obj.costing_name
            this.$refs.radModalResult.show()
        },
        blankToPrint(obj){
            const url = this.$router.resolve({ path: '/radiology/list/process/print-expertise/'+ obj.radiology_request_id + '/' + obj.radiology_request_item_id + '/' + obj.image_id }).href;
            window.open(url, '_blank');
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

        parsedBody(text){
            return text ? text.replace(/\n/g,"<br>") : ''
        },
    },
}
</script>
<style scoped>
table.custom-border tr td {
    border: 0px;
    padding: 2px;
}
</style>