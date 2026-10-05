<template>
    <div>
        <b-container fluid="xl" class="main-content">
            <div class="d-flex" ref="buttons">
                <b-button size="sm" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button @click="printCanvas" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
            </div>
            <div class="canvas-wrapper">
                <section id="canvas" ref="canvas" class="mt-16 p-40" style="position: relative;">
                    <div class="text-center text-uppercase text-bold-18">Kartu Pasien</div>
                    <div class="mt-24">
                        <div class="text-bold-16">{{ cardData.name }}</div>
                        <div class="text-bold-16">{{ ParseBirthDate }}</div>
                        <div class="col-3 px-0 mt-16">
                            <vue-barcode :value="cardData.norm" class="w-100"></vue-barcode>
                        </div>
                    </div>
                </section>
            </div>
        </b-container>
    </div>
</template>
<script>
import moment from 'moment'
export default {
    data() {
        return {
            cardData: {},
            show: false
        }
    },
    mounted() {
        this.getData(this.$route.params.norm)
    },
    methods: {
        getData (norm) {
			this.$store.dispatch('patient/getByNorm', norm).then(data => {
                this.cardData = data[0]
                console.log(this.cardData);
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
        }
    },
    computed: {
        ParseBirthDate() {
            return moment(this.cardData.birth_date).format('DD-MM-YYYY');
        },
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
