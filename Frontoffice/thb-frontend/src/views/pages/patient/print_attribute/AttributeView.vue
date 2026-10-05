<template>
    <div>
        <b-container fuid="xl" class="main-content">
            <div class="d-flex">
                <b-button size="sm" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button @click="printCanvas" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
            </div>
            <div class="canvas-wrapper">
                <div id="canvas" ref="canvas" class="mt-16" style="position: relative;">
                    <div class="canvas-body p-20">
                        <div id="bracelet">
                            <div class="col-10">
                                <p class="text-danger-700 mb-0 text-bold-24">Gelang Pasien</p>
                                <div class="card border-neutral-900 mt-8">
                                    <div class="card-body px-16 py-8">
                                        <div class="row">
                                            <div class="col-4">
                                                <p class="mb-0 text-bold-14 text-uppercase">Aditya Feri K <span>(L)</span></p>
                                                <p class="mb-0 text-bold-14 text-uppercase">Nomor RM : <span>00013596</span></p>
                                                <p class="mb-0 text-bold-14">08-05-1989 <span>33 Th 11 bln 27 hari</span></p>
                                            </div>
                                            <div class="col-3 d-flex align-items-center">
                                                <img src="@/@core/assets/images/nurse/vector-barcode-sm.png" class="w-100" alt="barcode">
                                            </div>
                                        </div>
                                        <p class="mb-0 mt-32 text-bold-14 text-uppercase">RS Zeemarr</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row mx-0 mt-40">
                                <div class="col-5">
                                    <p class="text-danger-700 mb-0 text-bold-24">Kartu Pasien</p>
                                    <div class="card border-neutral-900 rounded-cs mt-8">
                                        <div class="card-body">
                                            <div class="col-6 px-0 mt-40">
                                                <p class="mb-0 text-bold-16 text-uppercase">Aditya Feri K <span>TN</span> </p>
                                                <p class="mb-0 text-bold-16 text-uppercase">00013596</p>
                                                <img src="@/@core/assets/images/nurse/vector-barcode-sm.png" class="w-100 mt-16" alt="barcode">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-7">
                                    <p class="text-danger-700 mb-0 text-bold-24">Label RM</p>
                                    <div id="row-1">
                                        <p class="text-danger-900 mb-0 text-bold-16">x</p>
                                        <div class="row">
                                            <div class="col-6 px-20">
                                                <p class="mb-0 text-bold-16 text-uppercase">Renaldi</p>
                                                <div class="d-flex">
                                                    <p class="mb-0 text-semi-14 text-uppercase">04-07-2022</p>
                                                    <p class="ml-auto mb-0 text-semi-14 text-uppercase">L / <span>20 Th</span></p>
                                                </div>
                                                <p class="mb-0 text-bold-24 text-uppercase">MRN# 00-00-00-21</p>
                                                <p class="mb-0 text-bold-14 text-uppercase">REG# 2304080007</p>
                                                <img src="@/@core/assets/images/nurse/vector-barcode-xs.png" class="w-100 mt-8" alt="barcode">
                                            </div>
                                            <div class="col-6 px-20">
                                                <p class="mb-0 text-bold-16 text-uppercase">Renaldi</p>
                                                <div class="d-flex">
                                                    <p class="mb-0 text-semi-14 text-uppercase">04-07-2022</p>
                                                    <p class="ml-auto mb-0 text-semi-14 text-uppercase">L / <span>20 Th</span></p>
                                                </div>
                                                <p class="mb-0 text-bold-24 text-uppercase">MRN# 00-00-00-21</p>
                                                <p class="mb-0 text-bold-14 text-uppercase">REG# 2304080007</p>
                                                <img src="@/@core/assets/images/nurse/vector-barcode-xs.png" class="w-100 mt-8" alt="barcode">
                                            </div>
                                        </div>
                                    </div>
                                    <div id="row-2" class="mt-20">
                                        <p class="text-danger-900 mb-0 text-bold-16">x</p>
                                        <div class="row">
                                            <div class="col-6 px-20">
                                                <p class="mb-0 text-bold-16 text-uppercase">Renaldi</p>
                                                <div class="d-flex">
                                                    <p class="mb-0 text-semi-14 text-uppercase">04-07-2022</p>
                                                    <p class="ml-auto mb-0 text-semi-14 text-uppercase">L / <span>20 Th</span></p>
                                                </div>
                                                <p class="mb-0 text-bold-24 text-uppercase">MRN# 00-00-00-21</p>
                                                <p class="mb-0 text-bold-14 text-uppercase">REG# 2304080007</p>
                                                <img src="@/@core/assets/images/nurse/vector-barcode-xs.png" class="w-100 mt-8" alt="barcode">
                                            </div>
                                            <div class="col-6 px-20">
                                                <p class="mb-0 text-bold-16 text-uppercase">Renaldi</p>
                                                <div class="d-flex">
                                                    <p class="mb-0 text-semi-14 text-uppercase">04-07-2022</p>
                                                    <p class="ml-auto mb-0 text-semi-14 text-uppercase">L / <span>20 Th</span></p>
                                                </div>
                                                <p class="mb-0 text-bold-24 text-uppercase">MRN# 00-00-00-21</p>
                                                <p class="mb-0 text-bold-14 text-uppercase">REG# 2304080007</p>
                                                <img src="@/@core/assets/images/nurse/vector-barcode-xs.png" class="w-100 mt-8" alt="barcode">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </b-container>
    </div>
</template>
<script>
export default {
    methods: {
        printCanvas() {
            const canvas = this.$refs.canvas;
            // Save the current contents of the body to a variable
            const originalContents = document.body.innerHTML;
            // Set the body contents to the contents of the "canvas" div
            document.body.innerHTML = canvas.innerHTML;
            // Add CSS rule to change the orientation to landscape and set page size to A4
            const style = document.createElement('style');
            style.innerHTML = `
                @page {
                    size: landscape;
                    margin: 0;
                }
            `;
            document.head.appendChild(style);
            // Call the print method of the current window
            window.print();
            // Remove the added CSS rule from the document's style
            document.head.removeChild(style);
            // Restore the original contents of the body
            document.body.innerHTML = originalContents;
        }
    }

};

</script>
<style>
@import './../../../../@core/css/main/main.min.css';

.main-content {
    margin-top: 32px;
    padding-bottom: 32px;
}

.canvas-wrapper {
    display: flex;
    justify-content: center;
}

#canvas {
    width: 297mm;
    height: 215mm;
    overflow: hidden;
    /* background-color: rgb(251, 252, 217); */
}
.rounded-cs{
    border-radius: 32px;
}
</style>
