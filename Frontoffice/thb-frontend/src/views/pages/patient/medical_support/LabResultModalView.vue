<template>
    <b-modal ref="labModalResult" scrollable size="md" hide-footer>
        <template #modal-header="{ close }">
            <div class="d-flex justify-content-between w-100">
                <div class="text-uppercase font-weight-bold fs-13 pr-16">
                    Hasil Laboratorium  
                    <span class="text-primary-500">{{ temp_lab_result.alias_name }}. {{ temp_lab_result.name }} - {{ temp_lab_result.costing_name }}</span>
                </div>
                <feather-icon icon="XIcon" @click="close()" class="pointer"/>
            </div>
        </template>
        <b-button @click="printLaboratoryResult(temp_lab_result.laboratory_request_id)" variant="w3green" size="sm">
            <feather-icon icon="PrinterIcon" /> Cetak
        </b-button>
        <b-table :fields="laboratoryResultField" :items="temp_lab_result.arryData" bordered responsive small thead-tr-class="fs-14" tbody-tr-class="fs-13" class="mt-12">
            <template #cell(no)="row">
                <div class="text-center">{{ row.index+1 }}</div>
            </template>
        </b-table>
        <template v-if="temp_lab_result.url">
            <img :src="thumbnail" v-if="thumbnail" />
            <div id="pdf-canvas"></div>
        </template>
    </b-modal>
</template>

<script>
import * as pdfjsLib from 'pdfjs-dist/build/pdf'
import workerSrc from 'pdfjs-dist/build/pdf.worker.min.js'
export default {
    data() {
        return {
            urlbe: process.env.VUE_APP_IMAGE,
            pdfAsDataUri:'',
            input_pdf: null,
            thumbnail:null,

            laboratoryResultField: [
                { key: 'no', label: 'No', thStyle: { width: '40px', textAlign: 'center' }  },
                { key: 'Parameter', label: 'Parameter' },
                { key: 'hasil', label: 'Hasil' },
                { key: 'n_rujukan', label: 'Nilai Rujukan' },
                { key: 'satuan', label: 'Satuan' },
                { key: 'metoda', label: 'Metoda' },
            ],

            temp_lab_result: {
                alias_name: '',
                name: '',
                arryData: '',
                costing_name: '',
                url: '',
                laboratory_request_id: ''
            }
        }
    },

    methods: {
        seeLaboratoryResult(obj) {
            console.log(obj)
            this.temp_lab_result.alias_name = obj.alias_name
            this.temp_lab_result.name = obj.name
            this.temp_lab_result.arryData = obj.result
            this.temp_lab_result.costing_name = obj.costing_name
            this.temp_lab_result.laboratory_request_id = obj.laboratory_request_id
            this.temp_lab_result.url = obj.url
            if (obj.url && obj.url != "" || obj.url != null) {
                this.generateThumbnail(obj.url)
            }
            this.$refs.labModalResult.show()
        },
        printLaboratoryResult(id) {
            const url = this.$router.resolve({ path: '/laboratory/print/'+ id }).href;
            window.open(url, '_blank');
        },
        convertDataURIToBinary (dataURI) {
            var BASE64_MARKER = ';base64,';
            var base64Index = dataURI.indexOf(BASE64_MARKER) + BASE64_MARKER.length;
            var base64 = dataURI.substring(base64Index);
            var raw = window.atob(base64);
            var rawLength = raw.length;
            var array = new Uint8Array(new ArrayBuffer(rawLength));

            for(var i = 0; i < rawLength; i++) {
                array[i] = raw.charCodeAt(i);
            }
            return array;
        },
        async generateThumbnail(data) {
            try {
                const response = await this.$store.dispatch('soap/pdf_to_base64_lab', data)
                this.pdfAsDataUri = response.base64
                // var pdfAsArray = this.convertDataURIToBinary(this.pdfAsDataUri);

                // const loadingTask =  pdfjsLib.getDocument(pdfAsArray)

                // const pdf = await loadingTask.promise
                // const page = await pdf.getPage(1)

                // const scale = 1
                // const viewport = page.getViewport({ scale })

                // const canvas = document.getElementById('pdf-canvas')
                // const context = canvas.getContext('2d')

                // canvas.width = viewport.width
                // canvas.height = viewport.height

                // await page.render({ canvasContext: context, viewport }).promise

                // this.thumbnail = canvas.toDataURL()
                // console.log('testste'+this.thumbnail)
                
                var pdfAsArray = this.convertDataURIToBinary(this.pdfAsDataUri);
                const loadingTask = pdfjsLib.getDocument(pdfAsArray)
                const pdf = await loadingTask.promise

                // Loop through all pages
                for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
                    const page = await pdf.getPage(pageNum)
                    const scale = 1
                    const viewport = page.getViewport({ scale })

                    // Create a new canvas for each page
                    const canvas = document.createElement('canvas')
                    canvas.id = `pdf-canvas-${pageNum}`
                    const context = canvas.getContext('2d')

                    canvas.width = viewport.width
                    canvas.height = viewport.height

                    await page.render({ canvasContext: context, viewport }).promise

                    // Append canvas to container
                    document.getElementById('pdf-canvas').appendChild(canvas)
                }
            } catch (error) {
                console.error('Error rendering PDF:', error)
            }
        }
    },
}
</script>