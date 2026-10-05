<template>
    <div>
        <b-container fluid="xl" class="main-content">
            <div class="d-flex" ref="buttons">
                <b-button size="sm" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button @click="printCanvas" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
            </div>
            <div class="canvas-wrapper">
                <section id="canvas" ref="canvas" class="mt-16 p-40" style="position: relative;">
                    <section class="d-flex flex-column justify-content-center text-regular-14 text-center mt-40">
                        <p class="text-uppercase">{{ regData.health_facility_name }}</p>
                        <p>Tiket Antrian</p>
                        <p>RS Taman Harapan Baru Bekasi</p>
                    </section>
                    <h3 class="text-center"><b>A-1</b></h3>
                    <p class="text-regular-14 text-center">( {{ dateNow }} )</p>
                </section>
            </div>
        </b-container>
    </div>
</template>
<script>
export default {
    data() {
        return {
            userData: JSON.parse(localStorage.getItem('userData')),
            regData: {},
            show: false
        }
    },
    mounted() {
        this.getData(this.$route.params.id)
    },
    methods: {
        getData (id) {
			this.$store.dispatch('registration/getById', id).then(data => {
                this.regData = data[0]
                // console.log(this.regData);
                // this.show = false
            })
        },
        printCanvas() {
            const buttons = this.$refs.buttons;
            buttons.classList.add('d-none');
            buttons.classList.remove('d-flex');
            window.print();
            buttons.classList.remove('d-none');
            buttons.classList.add('d-flex');
        },
    },
    computed: {
        dateNow() {
            const date = new Date();
            const year = date.getFullYear();
            const month = date.getMonth() + 1;
            const day = date.getDate();
            const hour = date.getHours();
            const minute = date.getMinutes();
            const second = date.getSeconds();
            return `${day}-${month}-${year} ${hour}:${minute}:${second}`;
        }
    }
};
</script>
<style>
@import './../../../@core/css/main/main.min.css';

/* table,
th,
td {
    border: 1px solid black;
} */
td {
    padding: 2px 0;
    vertical-align: text-top;
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
