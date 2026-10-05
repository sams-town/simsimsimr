<template>
    <div>
        <b-container fluid="xl" class="main-content">
            <div class="d-flex">
                <b-button size="sm" @click="handleBack" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button @click="printCanvas" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
            </div>
            <div class="canvas-wrapper">
                <div id="canvas" ref="canvas" class="mt-16" style="position: relative;">
                    <div class="canvas-header d-flex px-3 mt-16">
                        <div class="ml-3">
                            <p class="text-regular-12">Jl.Raya Jakarta 11520</p>
                            <p class="text-regular-12">Telp: (021) 12345678</p>
                            <p class="text-regular-12">Email: info@gmail.com</p>
                            <p class="text-regular-12">Website: www.website.com</p>
                        </div>
                    </div><hr>
                    <div class="mt-40">
                        <div class="text-center">
                            <p class="text-bold-16">FORM VERIFIKASI RESEP OBAT</p>
                        </div><br>
                        <div class="row mx-0">
                            <div class="col-8">
                                <table class="text-regular-14">
                                    <tr>
                                        <td class="first-cell">Nama Pasien</td>
                                        <td class="px-3">:</td>
                                        <td>{{ this.data.patient_name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">TTL</td>
                                        <td class="px-3">:</td>
                                        <td>{{ this.data.patient_birth_place }} - {{ this.data.patient_birth_date }}</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">No RM</td>
                                        <td class="px-3">:</td>
                                        <td>{{ this.data.norm }}</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Ruang Rawat</td>
                                        <td class="px-3">:</td>
                                        <td>{{ this.data.health_facility_item_name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Nama Dokter</td>
                                        <td class="px-3">:</td>
                                        <td>{{ this.data.doctor_in_charge_name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">TB/TT</td>
                                        <td class="px-3">:</td>
                                        <td>{{ this.data.notes }}</td>
                                    </tr>
                                    <tr>
                                        <td class="first-cell">Apoteker</td>
                                        <td class="px-3">:</td>
                                        <td>Administration</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="px-3 mt-32">
                        <b-table-simple small class="text-regular-14">
                            <thead class="text-center text-bold-14">
                                <tr>
                                    <th>No</th>
                                    <th>Jenis Pengecekan</th>
                                    <th>Ya</th>
                                    <th>Tidak</th>
                                    <th>Keterangan/Tindak Lanjut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="item" v-for="(item, index) in this.verification">
                                    <td>
                                        {{ item.value }}
                                    </td>
                                    <td>
                                        {{ item.title }}
                                    </td>
                                    <td>
                                        <feather-icon :icon="item.yn === 1 ? 'CheckIcon' : ''" size="18" class="mb-1" />
                                    </td>
                                    <td>
                                        <feather-icon :icon="item.yn === 0 ? 'CheckIcon' : ''" size="18" class="mb-1" />
                                    </td>
                                    <td>
                                        {{ item.notes }}
                                    </td>
                                </tr>
                            </tbody>
                        </b-table-simple>
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
            data:{},
            verification: JSON.parse(localStorage.getItem('verification'))
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
        handleBack() {
            this.$router.go(-1);
        },
        getData (id) {
			this.$store.dispatch('prescription/getById', this.$route.params.id).then(data => {
                this.data = data[0]
            })
        }
    },
    mounted () {
        if (this.$route.params.id) {
            this.id = this.$route.params.id
            this.getData()
        }
    }
};

</script>
<style>
@import '@core/css/main/main.min.css';

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
</style>
