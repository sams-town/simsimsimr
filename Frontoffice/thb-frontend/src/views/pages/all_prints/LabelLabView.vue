<template>
    <div>
        <b-container fluid="xl" class="main-content">
            <div class="d-flex" ref="buttons">
                <b-button size="sm" class="px-32" variant="danger-400">Tutup</b-button>
                <b-button @click="newPrintCanvas" :disabled="disabled_print" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
            </div>
            <div class="canvas-wrapper">
                <section id="canvas" ref="canvas" class="mt-16 p-20" style="position: relative;">
                    <table>
                        <tbody class="text-uppercase font-weight-bold" style="font-size: 12px;">
                            <tr>
                                <b-td colspan="2" class="p-0">NO.RM</b-td>
                                <b-td colspan="1" class="px-1 py-0" style="width: 15px;">:</b-td>
                                <b-td colspan="2" class="p-0">{{ regData.norm }}</b-td>

                                <b-td colspan="2" class="p-0 pl-3">TGL LAHIR</b-td>
                                <b-td colspan="1" class="px-1 py-0" style="width: 15px;">:</b-td>
                                <b-td colspan="2" class="p-0">{{ parsed_date(regData.birth_date) }}</b-td>
                            </tr>
                            <tr>
                                <b-td colspan="2" class="p-0">NAMA</b-td>
                                <b-td colspan="1" class="px-1 py-0" style="width: 15px;">:</b-td>
                                <b-td colspan="6" class="p-0">
                                    {{ regData.alias_name ? regData.alias_name : '-' }} {{ first_split }}
                                    ({{ parsedAge(regData.birth_date) }})
                                </b-td>
                            </tr>
                            <tr v-if="second_split">
                                <b-td colspan="3" class="p-0"></b-td>
                                <b-td colspan="6" class="p-0">{{ second_split }}</b-td>
                            </tr>
                            <tr>
                                <b-td colspan="2" class="p-0">NIK</b-td>
                                <b-td colspan="1" class="px-1 py-0" style="width: 15px;">:</b-td>
                                <b-td colspan="6" class="p-0">{{ regData.idcard ? regData.idcard : '-' }}</b-td>
                            </tr>
                            <tr>
                                <b-td colspan="2" class="p-0">JNS PERIKSA</b-td>
                                <b-td colspan="1" class="px-1 py-0" style="width: 15px;">:</b-td>
                                <b-td colspan="6" class="p-0">{{ examTypeText }}</b-td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="barcode-wrapper mt-1">
                        <svg ref="barcodeSvg"></svg>
                    </div>
                </section>
            </div>
        </b-container>
    </div>
</template>
<script>
import moment from 'moment'
import axios from 'axios'

export default {
    data() {
        return {
            regData: {},
            orderData: {},
            disabled_print: false,
            first_split: '',
            second_split: '',
        }
    },
    computed: {
        examTypeText() {
            const items = this.orderData && this.orderData.laboratory_request_item
            if (!items || !items.length) return '-'
            return items.map(item => item.costing_name).join(', ').toUpperCase()
        }
    },
    methods: {
        handleKeyPress(event) {
            if (event.ctrlKey && event.key === 'p') {
                event.preventDefault();
                this.newPrintCanvas();
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
        parsedAge(date) {
            if (!date || date === "0000-00-00") {
                return "-";
            }

            const parsed = moment(date);
            if (!parsed.isValid()) {
                return "";
            }

            const birthDate = moment(date, 'YYYY-MM-DD');
            const currentDate = moment();

            const years = currentDate.diff(birthDate, 'years');
            birthDate.add(years, 'years');

            return `${years} th`;
        },
        parsed_date(date) {
            if (!date || date === "0000-00-00") {
                return "-";
            }

            const parsed = moment(date);
            if (!parsed.isValid()) {
                return "";
            }

            return parsed.format("DD-MM-YYYY");
        },
        newPrintCanvas() {
            this.disabled_print = true
            let temp_arry = []

            let temp_alias = this.regData.alias_name ? this.regData.alias_name.toUpperCase() : ''
            let temp_name = this.regData.name ? this.regData.name.toUpperCase() : ''
            let combine_name = temp_alias + " " + temp_name
            let first_split_name = combine_name.slice(0, 28);
            let second_split_name = combine_name.slice(28);

            let norm = this.regData.norm ? this.regData.norm : ''
            let nik = this.regData.idcard ? this.regData.idcard : '-'
            let birth_date = this.parsed_date(this.regData.birth_date)
            let age = this.parsedAge(this.regData.birth_date)

            // ambil costing_name dari laboratory_request_item, gabung kalau lebih dari 1
            let exam_type = this.orderData.laboratory_request_item && this.orderData.laboratory_request_item.length
                ? this.orderData.laboratory_request_item.map(item => item.costing_name).join(', ').toUpperCase()
                : '-'

            // value yang di-encode ke barcode, mis. kode registrasi seperti 2026/05/20/000197
            let barcode_value = this.orderData.no_request

            const version_1 = `
                ^XA
                ^CF0,16

                ^FO10,5^FDNo.RM : ${norm}^FS
                ^FO230,5^FDTgl.lahir : ${birth_date}^FS

                ^FO10,25^FDNama : ${first_split_name} (${age})^FS

                ^FO10,45^FDNIK : ${nik}^FS
                ^FO230,45^FD${exam_type}^FS

                ^FO10,68
                ^BY1.3,2,45
                ^BCN,45,Y,N,N
                ^FD${barcode_value}^FS

                ^XZ
            `;

            const version_2 = `
                ^XA
                ^CF0,15

                ^FO10,3^FDNo.RM : ${norm}^FS
                ^FO230,3^FDTgl.lahir : ${birth_date}^FS

                ^FO10,20^FDNama : ${first_split_name}^FS
                ^FO10,37^FD${second_split_name} (${age})^FS

                ^FO10,55^FDNIK : ${nik}^FS
                ^FO230,55^FD${exam_type}^FS

                ^FO10,75
                ^BY1.2,2,42
                ^BCN,42,Y,N,N
                ^FD${barcode_value}^FS

                ^XZ
            `;

            temp_arry.push(second_split_name == "" ? version_1 : version_2)

            const headers = { 
                "Accept": "application/pdf",
                "Content-Type": "application/x-www-form-urlencoded",
            }
            axios.post("http://api.labelary.com/v1/printers/8dpmm/labels/2.1653555x0.7874019999999999/", temp_arry, {
                responseType: "arraybuffer",
                headers,
            })
            .then((res) => {
                console.log(res);
                const blob = new Blob([res.data], { type: "application/pdf" });
                const url = window.URL.createObjectURL(blob);
                
                window.open(url, "_blank");
                this.disabled_print = false
            })
            .catch((error) => {
                console.error("Error:", error);
                this.disabled_print = false
            });
        },

        getData (id) {
            try {
                this.$store.dispatch('laboratory/getByIdOrder', id).then(data => {
                    this.orderData = data[0]
                    this.regData = data[0].regData
                    this.first_split = this.regData.name ? this.regData.name.slice(0, 25) : '-'
                    this.second_split = this.regData.name ? this.regData.name.slice(25) : '-'
                })
                
            } catch (error) {
                console.error('Error while GET data at:', error)
            }
        },
    },
    created() {
        this.getData(this.$route.params.id)
    },
    mounted() {
        window.addEventListener('keydown', this.handleKeyPress);
    },
    beforeDestroy() {
        window.removeEventListener('keydown', this.handleKeyPress);
    },

};

</script>
<style>
@import './../../../@core/css/main/main.min.css';

@media print {
    @page {
        size: 55mm 20mm;
        padding-top: 10px;
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
}

#canvas {
    width: 21.2cm !important;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
}

.barcode-wrapper {
    text-align: center;
}

</style>