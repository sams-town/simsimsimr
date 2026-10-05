<template>
    <div>
        <b-container fluid="xl" class="main-content">
            <div class="d-flex justify-content-between">
                <div class="d-block">
                    <div v-for="(item, index) in myArray" :key="index" id="canvas" ref="canvases" style="display: block; width: 100mm; overflow: hidden; font-weight: 700;">
                        <div>
                            <div class="d-flex align-items-center">
                                <div>
                                    <img src="../../../../../@core/assets/images/logo-thb-small.png" width="35px" alt="logo-icon-only">
                                </div>
                                <div class="d-flex flex-column align-items-center text-center pl-2">
                                    <div style="font-size: 16px;">RS Taman Harapan Baru Bekasi</div>
                                    <div style="font-size: 12px;">Jl. Kaliabang Tengah No. 2 RT.004 RW.023, Bekasi, Kota Bekasi</div>
                                </div>
                            </div>
                            <div class="">
                                <hr class="" style="margin: 4px 0; border: 1.5px solid black;">
                            </div>
                            <div class="text-center" style="font-size: 16px;">INSTALASI FARMASI {{ parsed_date(data.date_added) }}</div>
                            <div style="font-size: 14px;">{{ data.patient_name }} ({{ data.norm }}) {{  parsed_birth_date(data.patient_birth_date)  }}</div>
                            <div style="font-size: 18px;">{{ item.prescription_item_type_id == 1 ? item.pharmacy_catalog_name : item.prescription_item_type_name }}</div>
                            <div class="row">
                                <div class="col-9">
                                    <div style="font-size: 10px;">Exp: {{ item.prescription_item_type_id == 1 ? parsed_date(item.expired) : "" }}</div>
                                    <div style="font-size: 16px;">Pemakaian:
                                        <span v-if="item.prescription_item_type_id == 1">{{ truncatedText(item.used) }} {{ truncatedText(item.desc) }}</span>
                                        <span v-else>{{ truncatedText(item.prescription_item_type_used) }}</span>
                                    </div>
                                    <!-- <div style="font-size: 16px;">Pemakaian: {{ truncatedText(item.prescription_item_type_id == 1 ? item.used : item.prescription_item_type_used) }} {{ truncatedText(item.prescription_item_type_id == 1 ? item.desc : '') }}</div> -->
                                </div>
                                <div class="col-3 text-right" style="font-size: 14px;">
                                    <!-- <span v-if="item.prescription_item_type_id == 1">{{ (item.qty-(item.is_retur_full === 1 && item.pharmacy_catalog_id_retur || item.pharmacy_catalog_id_ref ? 0 : item.qty_retur)) }} {{ item.package_name }}</span> -->
                                    <span v-if="item.prescription_item_type_id == 1">{{ (item.qty-(item.is_retur_full === 1 ? item.qty_retur : 0)) }} {{ item.package_name }}</span>
                                    <span v-else>{{ item.prescription_item_type_val ? item.prescription_item_type_val : '' }}</span>
                                </div>
                            </div>
                            <div class="text-center" style="font-size: 13px;">
                                <div>SEBELUM MAKAN / SETELAH MAKAN</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="not_print" ref="buttons">
                    <div>
                        <b-table-simple table-variant="white" bordered>
                            <b-thead>
                                <b-tr >
                                    <b-td>No</b-td>
                                    <b-td>Nama Obat</b-td>
                                    <b-td>
                                        <b-form-checkbox v-model="toAll" :value="true" class="m-0" />
                                    </b-td>
                                </b-tr>
                            </b-thead>
                            <b-tbody>
                                <b-tr v-for="(item, index) in data.form_prescription" :key="index">
                                    <b-td>{{ index + 1 }}</b-td>
                                    <b-td>{{ item.prescription_item_type_id == 1 ? item.pharmacy_catalog_name : item.prescription_item_type_name }}</b-td>
                                    <b-td class="text-center">
                                        <b-form-checkbox-group v-model="selectedArray" size="xl" class="pr-0">
                                            <b-form-checkbox :value="index" class="m-0" />
                                        </b-form-checkbox-group>
                                    </b-td>
                                </b-tr>
                            </b-tbody>
                        </b-table-simple>

                        <!-- DONT DELETE CODE BELOW - IT'S BACKUP IF MAIN FUNCTION GOT ERROR OR SERCIVE GOT MAINTENANCE  -->
                        <!-- <b-table-simple table-variant="white" bordered>
                            <b-thead>
                                <b-tr >
                                    <b-td>No</b-td>
                                    <b-td>Nama Obat</b-td>
                                    <b-td>Aksi</b-td>
                                </b-tr>
                            </b-thead>
                            <b-tbody>
                                <b-tr v-for="(item, index) in data.form_prescription" :key="index">
                                    <b-td>{{ index + 1 }}</b-td>
                                    <b-td>{{ item.pharmacy_catalog_name }}</b-td>
                                    <b-td>
                                        <b-button @click="printSelected(index)" size="sm" variant="success-500">Print</b-button>
                                    </b-td>
                                </b-tr>
                            </b-tbody>
                        </b-table-simple> -->
                    </div>

                    <div class="d-flex mt-24">
                        <div>
                            <b-button size="sm" @click="closeWindow" class="px-32" variant="danger-400">Tutup</b-button>
                        </div>
                        <div class="w-100 ml-2">
                            <b-button @click="printCondition" :disabled="disabled_print_all" size="sm" class="btn-block px-32" variant="success-500">Cetak</b-button>
                        </div>
                    </div>
                </div>
            </div>
        </b-container>
    </div>
</template>
<script>
import optionMixin from "@/mixins/optionMixin";
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue';
import axios from 'axios';
import moment from 'moment';

export default {
    mixins: [optionMixin],

    data() {
        return {
            data:{},
            verification: JSON.parse(localStorage.getItem('verification')),
            disabled_print_all: false,
            myArray: [],
            selectedArray: [],
            toAll: true,
            optSignTime: [],
            optAdminister: [],
            optSignUnits: [],
            other_administer: 'ea3c2573-b9f5-428d-ada4-0ce6ea67c72f',
            isHomeRecipe: false,
        }
    },
    watch: {
        selectedArray(val) {
            // console.log(this.selectedArray);

            if (val.length === this.myArray.length) {
                this.toAll = true;
                this.disabled_print_all = false;
            } else if (val.length > 0) {
                this.toAll = false;
                this.disabled_print_all = false;
            } else {
                this.toAll = false;
                this.disabled_print_all = true;
            }
        },
        toAll(val) {
            if (val) {
                this.checkedAll();
            }
        }
    },
    methods: {
        notification(a, b, c, d) {
            this.$toast({
                component: ToastificationContent,
                position: 'top-right',
                props: { title: a, icon: b, variant: c, text: d }
            })
        },
        parsed_date(date) {
            if (!date || date === "0000-00-00") {
                return "";
            }

            const parsed = moment(date);
            if (!parsed.isValid()) {
                return "";
            }

            return parsed.format("DD-MM-YYYY");
        },
        parsed_birth_date(date) {
            return moment(date).format('DD/MM/YYYY');
        },
        printCondition() {
            if (this.selectedArray.length == 0) {
                this.notification('Warning','CloseIcon','warning','Silakan pilih obat yang akan dicetak')
            } else {
                this.myArray = this.myArray.filter((item, index) =>
                    this.selectedArray.includes(index)
                );
                setTimeout(() => {
                    this.isHomeRecipe ?  this.newPrintCanvas() : this.printHomeRecipe()
                }, 500);
            }
        },

        newPrintCanvas() {
            this.disabled_print_all = true
            let temp_arry = []

            for (let i = 0; i < this.myArray.length; i++) {
                const el = this.myArray[i];

                // let qty_retur = el.qty_retur != null || el.qty_retur != '' ? el.qty_retur : 0
                // let qty = el.qty != null || el.qty != '' ? el.qty : 0

                let print_date = this.parsed_date(this.data.date_added)
                let print_patient_info = this.data.patient_name + " (" + this.data.norm + ")"

                let qty_retur = el.qty_retur ? el.qty_retur : 0
                let qty = el.qty ? el.qty : 0
                let count_qty = qty-qty_retur
                let print_count = ""

                let first_catalog_name = ""
                let second_catalog_name = ""
                let print_expired = ""

                let dose = el.dose
                let dose_unit = this.getDynamicOptName(el.dose_unit_id, this.optSignUnits)
                let sign_times = this.getDynamicOptName(el.signatura_times_id, this.optSignTime)
                let print_signatura = `${dose} ${dose_unit} / ${sign_times}`

                // let desc = this.truncatedText(el.desc)
                let desc = el.desc
                let print_administer = el.administer_id == this.other_administer ? desc : this.getDynamicOptName(el.administer_id, this.optAdminister)

                if (el.prescription_item_type_id == 1) {
                    first_catalog_name = el.pharmacy_catalog_name.slice(0, 34);
                    second_catalog_name = el.pharmacy_catalog_name.slice(34);
                    print_expired = this.parsed_date(el.expired)
                    print_count = count_qty + " " + el.package_name
                } else {
                    first_catalog_name = el.prescription_item_type_name
                    second_catalog_name = ""
                    print_expired = ""
                    print_count = ""
                }

                const version_1 = `
                    ^XA

                    ^CF0,20
                    ^FO170,15^FDRS Taman Harapan Baru Bekasi^FS
                    ^CF0,16
                    ^FO124,43^FDJl. Kaliabang Tengah No. 2 RT.004 RW.023, Bekasi, Kota Bekasi^FS
                    ^FO0,70^GB1300,3,2^FS

                    ^CF0,20
                    ^FO145,85^FDINSTALASI FARMASI ${print_date}^FS

                    ^CF0,20
                    ^FO10,130^FD${print_patient_info}^FS

                    ^CF0,28
                    ^FO10,165^FD${first_catalog_name}^FS

                    ^CF0,20
                    ^FO10,210^FDExp : ${print_expired}^FS
                    ^FO430,210^FD${print_count}^FS

                    ^CF0,22
                    ^FO10,255^FD${print_signatura}, ${print_administer}^FS

                    ^XZ
                `;

                const version_2 = `
                    ^XA

                    ^CF0,20
                    ^FO170,15^FDRS Taman Harapan Baru Bekasi^FS
                    ^CF0,16
                    ^FO124,43^FDJl. Kaliabang Tengah No. 2 RT.004 RW.023, Bekasi, Kota Bekasi^FS
                    ^FO0,70^GB1300,3,2^FS

                    ^CF0,20
                    ^FO145,85^FDINSTALASI FARMASI ${print_date}^FS

                    ^CF0,20
                    ^FO10,130^FD${print_patient_info}^FS

                    ^CF0,28
                    ^FO10,163^FD${first_catalog_name}^FS
                    ^FO10,193^FD${second_catalog_name}^FS

                    ^CF0,20
                    ^FO10,232^FDExp : ${print_expired}^FS
                    ^FO430,232^FD${print_count}^FS

                    ^CF0,22
                    ^FO10,268^FD${print_signatura}, ${print_administer}^FS

                    ^XZ
                `;

                temp_arry.push(second_catalog_name == "" ? version_1 : version_2)
            }
            const headers = {
                "Accept": "application/pdf",
                "Content-Type": "application/x-www-form-urlencoded",
            }
            axios.post("http://api.labelary.com/v1/printers/8dpmm/labels/2.7559069999999997x1.5748039999999999/", temp_arry, {
                responseType: "arraybuffer",
                headers,
            })
            .then((res) => {
                // console.log(res);
                const blob = new Blob([res.data], { type: "application/pdf" });
                const url = window.URL.createObjectURL(blob);

                window.open(url, "_blank");
                this.disabled_print_all = false
            })
            .catch((error) => {
                console.error("Error:", error);
                this.disabled_print_all = false
            });
            this.myArray = this.data.form_prescription
        },

        printHomeRecipe() {
            this.disabled_print_all = true
            let temp_arry = []

            for (let i = 0; i < this.myArray.length; i++) {
                const el = this.myArray[i];

                // let qty_retur = el.qty_retur != null || el.qty_retur != '' ? el.qty_retur : 0
                // let qty = el.qty != null || el.qty != '' ? el.qty : 0

                let print_date = this.parsed_date(this.data.date_added)
                let print_patient_info = this.data.patient_name + " (" + this.data.norm + ")"

                let qty_retur = el.qty_retur ? el.qty_retur : 0
                let qty = el.qty ? el.qty : 0
                let count_qty = qty-qty_retur
                let print_count = ""

                let first_catalog_name = ""
                let second_catalog_name = ""
                let print_used = ""
                let print_expired = ""
                let print_desc = ""

                if (el.prescription_item_type_id == 1) {
                    first_catalog_name = el.pharmacy_catalog_name.slice(0, 34);
                    second_catalog_name = el.pharmacy_catalog_name.slice(34);
                    print_expired = this.parsed_date(el.expired)
                    print_count = count_qty + " " + el.package_name
                    print_used = this.truncatedText(el.used)
                    print_desc = this.truncatedText(el.desc)
                } else {
                    first_catalog_name = el.prescription_item_type_name
                    second_catalog_name = ""
                    print_expired = ""
                    print_count = ""
                    print_used = this.truncatedText(el.prescription_item_type_used)
                    print_desc = ""
                }

                const version_1 = `
                    ^XA

                    ^CF0,20
                    ^FO170,15^FDRS Taman Harapan Baru Bekasi^FS
                    ^CF0,16
                    ^FO124,43^FDJl. Kaliabang Tengah No. 2 RT.004 RW.023, Bekasi, Kota Bekasi^FS
                    ^FO0,70^GB1300,3,2^FS

                    ^CF0,20
                    ^FO145,85^FDINSTALASI FARMASI ${print_date}^FS

                    ^CF0,20
                    ^FO10,130^FD${print_patient_info}^FS

                    ^CF0,28
                    ^FO10,165^FD${first_catalog_name}^FS

                    ^CF0,20
                    ^FO10,210^FDExp : ${print_expired}^FS
                    ^FO430,210^FD${print_count}^FS
                    ^CF0,22
                    ^FO10,245^FDPemakaian: ${print_used} ${print_desc}^FS

                    ^CF0,18

                    ^XZ
                `;
                    // ^FO160,290^FDSEBELUM MAKAN / SETELAH MAKAN^FS

                const version_2 = `
                    ^XA

                    ^CF0,20
                    ^FO170,15^FDRS Taman Harapan Baru Bekasi^FS
                    ^CF0,16
                    ^FO124,43^FDJl. Kaliabang Tengah No. 2 RT.004 RW.023, Bekasi, Kota Bekasi^FS
                    ^FO0,70^GB1300,3,2^FS

                    ^CF0,20
                    ^FO145,85^FDINSTALASI FARMASI ${print_date}^FS

                    ^CF0,20
                    ^FO10,130^FD${print_patient_info}^FS

                    ^CF0,28
                    ^FO10,163^FD${first_catalog_name}^FS
                    ^FO10,193^FD${second_catalog_name}^FS

                    ^CF0,20
                    ^FO10,232^FDExp : ${print_expired}^FS
                    ^FO430,232^FD${print_count}^FS
                    ^CF0,22
                    ^FO10,258^FDPemakaian: ${print_used} ${print_desc}^FS

                    ^CF0,18

                    ^XZ
                `;

                temp_arry.push(second_catalog_name == "" ? version_1 : version_2)
            }
            const headers = {
                "Accept": "application/pdf",
                "Content-Type": "application/x-www-form-urlencoded",
            }
            axios.post("http://api.labelary.com/v1/printers/8dpmm/labels/2.7559069999999997x1.5748039999999999/", temp_arry, {
                responseType: "arraybuffer",
                headers,
            })
            .then((res) => {
                // console.log(res);
                const blob = new Blob([res.data], { type: "application/pdf" });
                const url = window.URL.createObjectURL(blob);

                window.open(url, "_blank");
                this.disabled_print_all = false
            })
            .catch((error) => {
                console.error("Error:", error);
                this.disabled_print_all = false
            });
            this.myArray = this.data.form_prescription
        },


        // DONT DELETE CODE BELOW - IT'S BACKUP IF MAIN FUNCTION GOT ERROR OR SERCIVE GOT MAINTENANCE

        // newPrintCanvas() {
        //     const canvases = this.$refs.canvases;
        //     const originalContents = document.body.innerHTML;
        //     const buttons = this.$refs.buttons;

        //     buttons.classList.add('d-none');

        //     Array.from(canvases).forEach((canvas, index) => {
        //         document.body.innerHTML = canvas.outerHTML;
        //         window.print();
        //     });

        //     document.body.innerHTML = originalContents;
        //     buttons.classList.remove('d-none');
        //     location.reload()
        // },

        // printSelected(param_index) {
        //     const canvases = this.$refs.canvases;
        //     const originalContents = document.body.innerHTML;
        //     const buttons = this.$refs.buttons;

        //     buttons.classList.add('d-none');
        //     Array.from(canvases).forEach((canvas, index) => {
        //         if (param_index == index) {
        //             document.body.innerHTML = canvas.outerHTML;
        //             window.print();
        //         }
        //     });

        //     document.body.innerHTML = originalContents;
        //     buttons.classList.remove('d-none');
        //     location.reload()
        // },

        closeWindow() {
            window.close()
        },
        checkedAll() {
            this.$nextTick(() => {
                this.selectedArray = Array.from({ length: this.myArray.length }, (_, i) => i)
            });
        },
        getData() {
			this.$store.dispatch('prescription/getConcoct', this.$route.params.id).then(response => {
                this.data = response[0]
                this.myArray = response[0].form_prescription
                this.isHomeRecipe = this.data.type_prescription_id == 4

                this.checkedAll()
            })
        },
        truncatedText(textData) {
            const safeText = textData != null ? String(textData) : "";
            return safeText.slice(0, 30);
        },
        parsedBody(text){
            return String(text || '').replace(/\n/g, "<br>");
        },

        async getOptSignTime() {
            try {
                await this.$store.dispatch('kardex/getOptSignaturaTime')
                .then(response => {
                    this.optSignTime = response
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
                    this.optAdminister = response
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
                        this.optSignUnits = response
                    }
                )
                .catch(({message, debug, response}) => {
                    console.error('Error GET kardex/getOptUnit at: ', response);
                })
            } catch (error) {
                console.error('Error catch while GET kardex/getOptUnit at: ', error);
            }
        },
    },
    created () {
        if (this.$route.params.id) {
            this.id = this.$route.params.id
            this.getData()
        }

        this.getOptSignTime()
        this.getOptCatInstruction()
        this.getSignaturaUnits()
    }
};

</script>
<style>
@import '@core/css/main/main.min.css';

@media print {
    @page {
        size: 70mm 40mm;
        padding: 5px;
    }
    #canvas {
        display: block;
        size: 70mm 40mm;
        overflow: hidden;
    }
    .not_print{
        display: none;
    }
    .main-content {
        margin-top: 0px;
        padding-bottom: 0px;
    }
}

p {
    margin: 0;
    padding: 0;
}

.main-content {
    margin-top: 32px;
    padding-bottom: 32px;
}

/* #canvas {
    overflow: visible;
    width: 120mm !important;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
} */


</style>
