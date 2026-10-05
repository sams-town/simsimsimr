<template>
    <div>
        <b-container fluid="xl" class="main-content">
            <div class="d-flex">
                <b-button @click="closeWindow" size="sm" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button @click="printCanvas" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
            </div>
            <div class="">
                <div id="canvas" ref="canvas" class="mt-16" style="position: relative;">
                    <div class="text-center text-bold-18">
                        <div>LOW STOCK GUDANG FARMASI</div>
                        <div>INSTALASI FARMASI</div>
                        <div>TGL: 27 JUNI 2024</div>
                    </div>
                    <div class="mt-32">
                        <section class="mt-24">
                            <b-table :fields="fieldsTable" :items="items" small bordered class="text-center" style="font-size: 14px;">
                                <template #cell(goods_name)="data">
                                    <div class="text-left">{{ data.value }}</div>
                                </template>
                                <template #cell(price)="data">
                                    <div class="text-right">{{ data.value }}</div>
                                </template>
                            </b-table>
                        </section>
                        <section class="col mt-64">
                            <div class="d-flex justify-content-between w-100 mt-20">
                                <div class="col-3 text-center">
                                    <div>Bekasi, 22 Juni 2024</div>
                                    <div>Apoteker Penanggung Jawab</div>
                                    <div class="mt-64">( Nama Lengkap )</div>
                                </div>
                                <div class="col-3 text-center">
                                    <div>Menyetujui</div>
                                    <div>Direktur R. Taman Harapan Baru</div>
                                    <div class="mt-64">( Nama Lengkap )</div>
                                </div>
                                <div class="col-3 text-center">
                                    <div class="invisible">Lorem Ipsum</div>
                                    <div>Mengetahui</div>
                                    <div class="mt-64">( Nama Lengkap )</div>
                                </div>
                                <div class="col-3 text-center">
                                    <div class="invisible">Lorem Ipsum</div>
                                    <div>Diperiksa Oleh</div>
                                    <div class="mt-64">( Nama Lengkap )</div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </b-container>
    </div>
</template>
<script>
export default {
    data() {
        return {
            fieldsTable: [
                { key:'no', label:'NO' },
                { key:'goods_name', label:'NAMA BARANG', thStyle:'width:35%' },
                { key:'small_unit', label:'STOCK SATUAN TERKECIL', thStyle:'width:13%' },
                { key:'monthly_average', label:'RATA-RATA 1 BULAN (PCS)', thStyle:'width:13%' },
                { key:'submission', label:'PENGAJUAN DALAM PCS', thStyle:'width:13%' },
                { key:'price', label:'HARGA/PCS' },
                { key:'discount', label:'DISC' },
                { key:'pbf', label:'PBF' },
            ],
            items: [
                { no: '1', goods_name: 'Acran Injeksi (Ranitidin inj 50 mg/2 ml )', small_unit: '0', monthly_average: '86 AMP', submission: '105', price: 'Rp 22,450', discount: '15', pbf: 'BSP'  },
                { no: '2', goods_name: 'ACYCLOVIR 400 MG TAB', small_unit: '0', monthly_average: '173 TAB', submission: '300', price: 'Rp 631', discount: '22.5', pbf: 'BSP'  },
                { no: '3', goods_name: 'Amoxsan injeksi (Amoxicillin inj 1 g )', small_unit: '10 AMP (1 BOX)', monthly_average: '37 AMP', submission: '50', price: 'Rp 23,000', discount: '22.5', pbf: 'BSP'  },
            ]
        }
    },
    methods: {
        printCanvas() {
            const canvas = this.$refs.canvas;
            const originalContents = document.body.innerHTML;
            document.body.innerHTML = canvas.innerHTML;
            window.print();
            document.body.innerHTML = originalContents;
            location.reload()
        },
        closeWindow(){
            window.close()
        },
    }
};

</script>
<style>
@import '../../../../../@core/css/main/main.min.css';

@media print {
    @page {
        size: A4;
        margin: 16mm;
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


/* #canvas {
    width: 30cm !important;
    width: 21.2cm !important;
    height: 29.7cm !important;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
} */

.w-40 {
    width: 40%;
}

</style>
