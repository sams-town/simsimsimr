<template>
    <div>
        <b-container fluid="xl" class="main-content">
            <div class="d-flex">
                <b-button size="sm" @click="handleBack" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button @click="printCanvas" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
            </div>
            <div class="canvas-wrapper">
                <div id="canvas" ref="canvas" class="mt-16" style="position: relative;">
                    <!-- <div class="col">
                        <div ref="header" class="d-flex pb-16" >
                            <div class="d-flex align-items-center justify-content-center">
                                <img src="../../../../../@core/assets/images/logo-thb-small.png" width="80px" alt="rs-logo"> <br>
                                <div class="text-regular-12 pl-16">
                                    <p class="text-bold-18">RS Taman Harapan Baru</p>
                                    <p>Jl. Kaliabang Tengah No. 2 RT.004 RW.023, Bekasi, Kota Bekasi</p>
                                    <p>Info: +62 778 4161212 / 0853 7111 1212</p>

                                </div>
                            </div>
                        </div>
                    </div> -->

                    <div class="ql-editor">
                        <!-- Header logos -->
                        <div class="row align-items-center mb-4">
                            <div class="col-2">
                            <img src="../../../../../@core/assets/images/logo-thb.jpg" width="200%" alt="rs-logo">
                            </div>
                            <div class="col text-center text-bold-28"></div>
                            <div class="col-2">
                            <img src="../../../../../@core/assets/images/lafki-logo.png" width="85%" alt="lafki-logo" class="float-right">
                            </div>
                            <div class="col-2">
                            <img src="../../../../../@core/assets/images/apexa.png" width="80%" alt="-logo" class="float-right">
                            </div>
                        </div>
                        <div v-html="content"></div>
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
            content: "",
            signatureImage: ""
        }
    },
    methods: {
        printCanvas() {
            const canvas = this.$refs.canvas;
            const originalContents = document.body.innerHTML;
            document.body.innerHTML = canvas.innerHTML;
            window.print();
            document.body.innerHTML = originalContents;
        },
        async getData(id) {
            try {
                const response = await this.$store.dispatch("letter/getById", id);
                const data = response[0]
                console.log("response", response, data);
                this.content = data?.content;
                this.signatureImage = data?.signature_image ? data.signature_image : "Placeholder signature";
            } catch (error) {
                console.log(error)
            }
        },
        handleBack() {
            this.$router.go(-1);
        }
    },
    mounted() {
        if (this.$route.params.id) {
            this.id = this.$route.params.id
            this.getData(this.$route.params.id)
            console.log(this.$route.params.id)
        }
    }
};

</script>
<style>
@import './../../../../../@core/css/main/main.min.css';

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
    /* width: 210mm; */
    width: 21.2cm !important;
    /* height: 29.7cm !important; */
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    padding: 20px 0;
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

.ql-editor {
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
    line-height: 1.42;
    height: none;
    outline: none;
    overflow-y: auto;
    padding: 12px 15px;
    -o-tab-size: 4;
    tab-size: 4;
    -moz-tab-size: 4;
    text-align: left;
    white-space: pre-wrap;
    word-wrap: break-word;
}
</style>
