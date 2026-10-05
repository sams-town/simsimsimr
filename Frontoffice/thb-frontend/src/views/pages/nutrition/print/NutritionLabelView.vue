<template>
    <div>
        <b-container fluid="xl" class="main-content">
            <div class="d-flex">
                <b-button size="sm" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button @click="printCanvas" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
            </div>
            <div class="canvas-wrapper">
                <section id="canvas" ref="canvas" class="mt-16 p-40" style="position: relative;">
                    <table>
                        <tbody class="text-regular-14">
                            <tr>
                                <b-td>30-01-2023 /</b-td>
                                <b-td class="text-right font-weight-bold" style="text-decoration: underline;">00013607 </b-td>
                            </tr>
                            <tr class="font-weight-bold">
                                <b-td colspan="2">WAHYU EKO YULIANTO [Tn] [L]</b-td>
                            </tr>
                            <tr class="font-weight-bold">
                                <b-td colspan="2">dr. Vicky Vendy</b-td>
                            </tr>
                            <tr>
                                <b-td colspan="2">ALAMANDA 317 / Pribadi</b-td>
                            </tr>
                            <tr class="text-regular-12">
                                <b-td colspan="2">Dob: 04-07-1992/30 th 10 bln 7 hr Tlp.081319620151</b-td>
                            </tr>
                            <tr class="text-regular-12">
                                <b-td colspan="2">DUSUN II RT 8 RW 2 KENCANA MULIA RAMBANG MUARA ENIM SUMATE</b-td>
                            </tr>
                        </tbody>
                    </table>
                </section>
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
            // Call the print method of the current window
            window.print();
            // Restore the original contents of the body
            document.body.innerHTML = originalContents;
        }
    }
};

</script>
<style>
@import './../../../../@core/css/main/main.min.css';

/* table,
th,
td {
    border: 1px solid black;
} */
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
    /* height: 29.7cm !important; */
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
}

.canvas-header {
    width: 100%;
}

.canvas-header img:first-child {
    width: 80px;
}

.canvas-header img:last-child {
    width: 100%;
    height: 100%;
    display: block;
    margin-top: -30px;
}

.w-40 {
    width: 40%;
}

.canvas-footer {
    position: absolute;
    bottom: 0;
    /* right: 0; */
}
</style>
