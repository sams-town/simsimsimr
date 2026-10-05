<template>
    <div class="d-flex w-100 justify-content-center">
        <div class="">
            <div class="d-flex justify-content-end py-20" ref="buttons">
                <b-button @click="closeWindow" size="sm" variant="danger-400" class="px-32">Tutup</b-button>
                <b-button @click="printCanvas" size="sm" variant="success-500" class="px-32 ml-2">Print</b-button>
            </div>
            <section id="canvas" ref="canvas" class="p-20">
                <div class="d-flex align-items-center justify-content-between">
                    <img src="../../../@core/assets/images/logo-thb.jpg" width="100px" alt="logo-thb">
                    <div class="col text-uppercase text-center font-weight-bold" style="line-height: 120%;">
                        <div style="font-size: 18px;">Kartu alur kunjungan pasien</div>
                        <div style="font-size: 13px;">{{ regData.health_facility_item_name }}</div>
                    </div>
                    <div class="d-flex">
                        <!-- <img src="../../../@core/assets/images/lafki-logo.png" width="40px" alt="logo-lafki"> -->
                        <!-- <img src="../../../@core/assets/images/apexa.png" width="80px" alt="logo-apexa" class="ml-2"> -->
                    </div>
                </div>
                <div class="row mt-24">
                    <div class="col-7">
                        <div class="d-flex align-items-center text-bold-12">
                            <div class="queue-box mr-2">NO ANTRIAN : {{ regData.code_queue }} - {{ regData.queue }}</div>
                            <!-- <div v-if="regData.code_booking" class="queue-box">KODE BOOKING: {{ regData.code_booking }}</div> -->
                        </div>
                        <div class="row mt-16">
                            <div class="col-12">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="text-bold-10">NAMA PASIEN</div>
                                        <div class="text-regular-10">{{ regData.name }}</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-bold-10">JENIS KELAMIN / USIA</div>
                                        <div class="text-regular-10">{{ regData.sex_name }},  {{ parsed_age(regData.birth_date) }}</div>
                                    </div>
                                </div>
                                <div class="row mt-12">
                                    <div class="col-6">
                                        <div class="text-bold-10">NO RM</div>
                                        <div class="text-regular-10">{{ regData.norm }}</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-bold-10">TTL</div>
                                        <div class="text-regular-10">{{ regData.birth_place ? regData.birth_place + ', ' : '' }} {{ regData.birth_date }}</div>
                                    </div>
                                </div>
                                <div class="row mt-12">
                                    <div class="col-6">
                                        <div class="text-bold-10">ALAMAT</div>
                                        <div class="text-regular-10">{{ regData.address }}</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-bold-10">DOKTER</div>
                                        <div class="text-regular-10">{{ regData.doctor_in_charge_name }}</div>
                                    </div>
                                </div>
                                <div class="row mt-12">
                                    <div class="col-6">
                                        <div class="text-bold-10">JENIS PEMBAYARAN</div>
                                        <div class="text-regular-10" :class="regData.is_cob == 1 ? 'd-flex align-items-end' : ''">
                                            <b-badge v-if="regData.is_cob == 1" class="rounded text-neutral-500 text-uppercase border border-warning-500 px-10 py-1 mr-1" variant="warning-200">COB</b-badge>
                                            <span>{{ regData.insurance_name}} {{ regData.company_name ? '| ' + regData.company_name : '' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-bold-10">ESTIMASI PELAYANAN</div>
                                        <div class="text-regular-10">-</div>
                                    </div>
                                </div>
                                <div class="row mt-12">
                                    <div class="col-6">
                                        <div class="text-bold-10">NO ASURANSI</div>
                                        <div class="text-regular-10">{{ regData.number_insurance ? regData.number_insurance : '-' }}</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-bold-10">NO SEP</div>
                                        <div class="text-regular-10">{{ regData.sep }}</div>
                                    </div>
                                </div>
                                <div class="row mt-12">
                                    <div class="col-6">
                                        <div class="text-bold-10">KODE PENDAFTARAN</div>
                                        <div class="text-regular-10">{{ regData.code }}</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-bold-10">TGL. PENDAFTARAN</div>
                                        <div class="text-regular-10">{{ regData.register_date }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-5 py-16 bg-neutral-100">
                        <div class="text-bold-16">TUJUAN SELANJUTNYA</div>
                        <div class="row mt-16">
                            <div v-for="i in 2" class="col-6">
                                <div class="text-semi-12">{{ i == 1 ? 'TUJUAN' : 'TANGGAL' }}</div>
                                <table>
                                    <tr v-for="n in 5">
                                        <td class="text-medium-14 pt-16">{{ n }}</td>
                                        <td class="table-box p-16"></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>
<script>
import moment from 'moment';
export default {
    data() {
        return {
            userData: JSON.parse(localStorage.getItem('userData')),
            regData: {
                health_facility_item_name: '',
                code_queue: '',
                queue: '',
                code_booking: '',
                name: '',
                sex_name: '',
                birth_date: '',
                birth_place: '',
                norm: '',
                address: '',
                doctor_in_charge_name: '',
                is_cob: '',
                insurance_name: '',
                company_name: '',
                number_insurance: '',
                sep: '',
                code: '',
                register_date: '',
            },
        }
    },

    methods: {
        parsed_age(input_date) {
            if (!input_date || input_date === "0000-00-00") {
                return "-";
            }

            const parsed = moment(input_date);
            if (!parsed.isValid()) {
                return "-";
            }

            const birthDate = moment(input_date, 'YYYY-MM-DD');
            const currentDate = moment();

            const years = currentDate.diff(birthDate, 'years');
            birthDate.add(years, 'years');
            const months = currentDate.diff(birthDate, 'months');
            birthDate.add(months, 'months');
            const days = currentDate.diff(birthDate, 'days');

            return `${years} th ${months} bln ${days} hr`;
        },
        getData(id) {
			this.$store.dispatch('registration/getById', id).then(response => {
                let data = response[0]

                this.regData.health_facility_item_name = data.health_facility_item_name ? data.health_facility_item_name : '-'
                this.regData.code_queue = data.code_queue ? data.code_queue : '-'
                this.regData.queue = data.queue ? data.queue : '-'
                this.regData.code_booking = data.code_booking ? data.code_booking : '-'
                this.regData.name = data.name ? data.name : '-'
                this.regData.sex_name = data.sex_name ? data.sex_name : '-'
                this.regData.birth_date = data.birth_date ? data.birth_date : '-'
                this.regData.birth_place = data.birth_place ? data.birth_place : '-'
                this.regData.norm = data.norm ? data.norm : '-'
                this.regData.address = data.address ? data.address : '-'
                this.regData.doctor_in_charge_name = data.doctor_in_charge_name ? data.doctor_in_charge_name : '-'
                this.regData.code = data.code ? data.code : '-'
                this.regData.register_date = data.register_date ? data.register_date : '-'

                let insurance = data.registration_insurance
                if(insurance.length > 1) {
                    for (let index = 0; index < insurance.length; index++) {
                        let element = insurance[index]

                        if (element.is_primary == 1) {
                            this.regData.is_cob = element.is_cob ? element.is_cob : '-'
                            this.regData.insurance_name = element.insurance_name ? element.insurance_name : '-'
                            this.regData.company_name = element.company_name ? element.company_name : '-'
                            this.regData.number_insurance = element.number_insurance ? element.number_insurance : '-'
                            this.regData.sep = element.sep ? element.sep : '-'
                        }
                    }
                } else {
                    this.regData.is_cob = insurance[0].is_cob ? insurance[0].is_cob : '-'
                    this.regData.insurance_name = insurance[0].insurance_name ? insurance[0].insurance_name : '-'
                    this.regData.company_name = insurance[0].company_name ? insurance[0].company_name : '-'
                    this.regData.number_insurance = insurance[0].number_insurance ? insurance[0].number_insurance : '-'
                    this.regData.sep = insurance[0].sep ? insurance[0].sep : '-'
                }
            })
        },

        printCanvas() {
            const canvasElement = document.querySelector('#canvas');
            const buttons = this.$refs.buttons;
            const originalContent = document.body.innerHTML;

            canvasElement.style.transform = 'rotate(-90deg) scale(2)';
            canvasElement.style.transformOrigin = 'center';
            canvasElement.style.position = 'absolute';
            canvasElement.style.top = '50%';
            canvasElement.style.left = '50%';
            canvasElement.style.width = 'auto';
            canvasElement.style.height = 'auto';
            canvasElement.style.marginTop = `-${canvasElement.offsetHeight / 2}px`;
            canvasElement.style.marginLeft = `-${canvasElement.offsetWidth / 2}px`;

            buttons.classList.add('d-none');

            document.body.innerHTML = canvasElement.outerHTML;

            window.print();

            document.body.innerHTML = originalContent;

            this.$nextTick(() => {
                buttons.classList.remove('d-none');
                canvasElement.style.transform = '';
                canvasElement.style.position = '';
                canvasElement.style.marginTop = '';
                canvasElement.style.marginLeft = '';
            });

            location.reload()
        },

        handleKeyPress(event) {
            if (event.ctrlKey && event.key === 'p') {
                event.preventDefault();
                this.printCanvas();
            }
        },

        closeWindow() {
            window.close()
        }
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

<style scoped>
@import './../../../@core/css/main/main.min.css';

td {
    padding: 2px 0;
    vertical-align: text-top;
}

#canvas {
    width: 220mm !important;
    height: 110mm !important;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    background-color: white;
    display: block;
}

.queue-box {
    display: flex;
    justify-content: center;
    align-items: center;
    border: 3px solid black;
    padding: 4px 8px;
}

.table-box {
    border-bottom: 2px solid black;
    width:100%;
}

</style>
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap');
</style>