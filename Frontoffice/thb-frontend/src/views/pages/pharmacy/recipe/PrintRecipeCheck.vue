<template>
    <div>
        <b-container fluid="xl" class="main-content">
            <div class="d-flex">
                <b-button @click="closeWindow" size="sm" class="px-32 ml-auto" variant="danger-400">Tutup</b-button>
                <b-button @click="printCanvas" size="sm" class="px-32 mx-2" variant="success-500">Print</b-button>
            </div>
            <div class="canvas-wrapper">
                <div id="canvas" ref="canvas" class="mt-16" style="position: relative;">
                    <div class="row">
                        <section ref="left-section" class="col-7">
                            <div ref="left-header" class="d-flex justify-content-between pb-16" style="border-bottom: 3px solid black;">
                                <div class="d-flex align-items-center justify-content-center">
                                    <img src="../../../../@core/assets/images/logo-thb-small.png" width="80px" alt="rs-logo"> <br>
                                    <div class="text-regular-12 pl-16">
                                        <p class="text-bold-18">RS Taman Harapan Baru</p>
                                        <p>Jl. Kaliabang Tengah No. 2 RT.004 RW.023, Bekasi, Kota Bekasi</p>
                                        <p>Info: +62 778 4161212 / 0853 7111 1212</p>

                                    </div>
                                </div>
                                <img src="../../../../@core/assets/images/lafki-logo.png" width="80px" class="text-center" alt="rs-logo"> <br>
                            </div>
                            <div class="row mt-16 text-regular-12">
                                <div class="col-6">
                                    <table class="mt-16">
                                        <tr>
                                            <td class="align-top col-3" style="white-space: nowrap;">Klinik / Ruang</td>
                                            <td class="align-top col-1 text-right">:</td>
                                            <td>{{ data.health_facility_item_name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="align-top col-3">Nama Dokter</td>
                                            <td class="align-top col-1 text-right">:</td>
                                            <td>{{ data.doctor_in_charge_name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="align-top col-3">SIP Dokter</td>
                                            <td class="align-top col-1 text-right">:</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td class="align-top col-3">No. Telp</td>
                                            <td class="align-top col-1 text-right">:</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td class="align-top col-3">Obat Kronis</td>
                                            <td class="align-top col-1 text-right">:</td>
                                            <td><v-select v-model="is_chronic" readonly :options="ChronicOptions" :reduce="ChronicOptions => ChronicOptions.value" label="label" placeholder="Obat Kronis"></v-select></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-6">
                                    <table class="mt-16">
                                        <tr>
                                            <td class="align-top col-3">Tanggal</td>
                                            <td class="align-top col-1 text-right">:</td>
                                            <td>{{ data.date_added }}</td>
                                        </tr>
                                        <tr>
                                            <td class="align-top col-3" style="white-space: nowrap;">TTD Dokter</td>
                                            <td class="align-top col-1 text-right">:</td>
                                            <td class="p-32" style="border: 1.5px solid black;"></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <hr>

                            <div class="row text-regular-12">
                                <div class="col-6">
                                    <table>
                                        <tr>
                                            <td class="align-top col-3" style="white-space: nowrap;">Nama Pasien</td>
                                            <td class="align-top col-1 text-right">:</td>
                                            <td>{{ data.patient_name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="align-top col-3">Tgl. Lahir</td>
                                            <td class="align-top col-1 text-right">:</td>
                                            <td>{{ data.patient_birth_date }}</td>
                                        </tr>
                                        <tr>
                                            <td class="align-top col-3">No RM</td>
                                            <td class="align-top col-1 text-right">:</td>
                                            <td>{{ data.norm }}</td>
                                        </tr>
                                        <tr>
                                            <td class="align-top col-3">No. Telepon</td>
                                            <td class="align-top col-1 text-right">:</td>
                                            <td>{{ data.phone }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-6">
                                    <table>
                                        <tr>
                                            <td class="align-top col-3" style="white-space: nowrap;">Jenis Kelamin</td>
                                            <td class="align-top col-1 text-right">:</td>
                                            <td>{{ data.sex === 1 ? 'Laki - Laki' : 'Perempuan' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="align-top col-3">SEP</td>
                                            <td class="align-top col-1 text-right">:</td>
                                            <td>{{ data.sep }}</td>
                                        </tr>
                                        <tr>
                                            <td class="align-top col-3">No. Kartu</td>
                                            <td class="align-top col-1 text-right">:</td>
                                            <td>{{ data.number_insurance }}</td>
                                        </tr>
                                        <tr>
                                            <td class="align-top col-3">Alamat</td>
                                            <td class="align-top col-1 text-right">:</td>
                                            <td>{{ data.address }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="mt-24">
                                <b-table-simple class="text-regular-12">
                                    <b-tbody>
                                        <b-tr v-for="(item,index) in data.form_prescription" :key="index">
                                            <b-td>
                                                <div class="d-flex">
                                                    <div class="mr-8">R/</div>
                                                    <div>{{ item.pharmacy_catalog_name }} <br> Sediaan : {{item.pharmacy_catalog_dose}} {{item.pharmacy_catalog_dose_unit_name}}, Kemasan : {{ item.pharmacy_catalog_packaging_value }} {{ item.pharmacy_catalog_packaging_unit_name }}, Dosis Dokter : {{ item.dose }} {{ item.dose_unit_name }} TIAP {{ item.signatura_times_name }}, {{ item.administer_name }} | Jumlah : {{ (item.qty-(item.is_retur_full === 1 && item.pharmacy_catalog_id_retur || item.pharmacy_catalog_id_ref ? 0 : item.qty_retur)) }} {{ item.package_name }} </div>
                                                </div>
                                                <div>{{ item.used }}</div>
                                            </b-td>
                                        </b-tr>
                                    </b-tbody>
                                </b-table-simple>
                            </div>
                        </section>

                        <section ref="right-section" class="col-5">
                            <div class="d-flex justify-content-center align-items-center w-100 p-8 text-bold-24" style="border: 3px solid black; height: 60px;">{{ data.company_name }}</div>

                            <div class="mt-8">
                                <table class="mt-16 text-regular-12">
                                    <tr>
                                        <td class="col-3" style="vertical-align: top;">Petugas</td>
                                        <td class="col-1 text-right">:</td>
                                        <td>{{ userData.employee_name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="col-3" style="vertical-align: top; white-space: nowrap;">Tgl / Jam</td>
                                        <td class="col-1 text-right">:</td>
                                        <td>{{ dateNow }}</td>
                                    </tr>
                                </table>
                            </div>

                            <div class="mt-16">
                                <div class="font-weight-bold">TELAAH RESEP</div>
                                <b-table bordered small :fields="RecipeTable.fields" :items="RecipeTable.items" style="font-size: 14px;">
                                    <template #cell(criteria)="row">
                                        <div :class="row.item.criteria == 'SYARAT ADMINISTRASI' || row.item.criteria == 'ASPEK FARMASTETIK' || row.item.criteria == 'ASPEK KLINIS' ? 'font-weight-bold' : ''">
                                            {{ row.item.criteria }}
                                        </div>
                                    </template>
                                    <template #cell(yes)="row">
                                        <div class="text-center" style="width: 45px;">
                                            <b-icon v-if="row.item.yes === true"icon="check-lg"></b-icon>
                                        </div>
                                    </template>
                                    <template #cell(no)="row">
                                        <div class="text-center" style="width: 45px;">
                                            <b-icon v-if="row.item.no === true"icon="check-lg"></b-icon>
                                        </div>
                                    </template>
                                </b-table>
                            </div>

                            <div class="mt-16">
                                <div class="font-weight-bold">TELAAH OBAT</div>
                                <b-table bordered small :fields="DrugTable.fields" :items="DrugTable.items" style="font-size: 14px;">
                                    <template #cell(criteria)="row">
                                        <div :class="row.item.criteria == 'SYARAT ADMINISTRASI' ? 'font-weight-bold' : ''">
                                            {{ row.item.criteria }}
                                        </div>
                                    </template>
                                    <template #cell(yes)="row">
                                        <div class="text-center" style="width: 45px;">
                                            <b-icon v-if="row.item.yes === true"icon="check-lg"></b-icon>
                                        </div>
                                    </template>
                                    <template #cell(no)="row">
                                        <div class="text-center" style="width: 45px;">
                                            <b-icon v-if="row.item.no === true"icon="check-lg"></b-icon>
                                        </div>
                                    </template>
                                </b-table>
                            </div>

                            <div class="mt-16">
                                <b-table bordered small :fields="SignTable.fields" :items="SignTable.items" style="font-size: 14px;">
                                    <template #cell(criteria)="row">
                                        <div :class="row.item.criteria == 'SYARAT ADMINISTRASI' ? 'font-weight-bold' : ''">
                                            {{ row.item.criteria }}
                                        </div>
                                    </template>
                                    <template #cell(sign)="row">
                                        <div style="width: 100px;">

                                        </div>
                                    </template>
                                </b-table>
                            </div>
                            <div class="mt-34 row text-regular-12 text-center">
                                <div class="col-6 d-flex flex-column align-items-center">
                                    <div>Tanda Tangan Petugas</div>
                                    <div class="d-flex align-items-center justify-content-center" style="width: 100%; height: 120px; border: 1px solid black;">
                                        <b-img v-if="data.sign_officer" fluid :src="urlbe+data.sign_officer" style="max-height: 118px;" />
                                    </div>
                                    <div>({{ userData.employee_name }})</div>
                                </div>
 
                                <div class="col-6 d-flex flex-column align-items-center">
                                    <div>Tanda Tangan Penerima</div>
                                    <div v-if="useSavedSignature" style="width: 100%;">
                                        <VueSignaturePad id="signature" width="100%" height="120px" ref="signaturePad" style="border: 1px solid black;"
                                        :options="{onBegin: () => {$refs.signaturePad.resizeCanvas()}}" />
                                        <div class="mt-4">
                                            <b-button size="sm" variant="secondary" class="mr-1" @click="clearSignature"> Hapus </b-button>
                                            <b-button size="sm" variant="success" @click="saveSignature"> Simpan </b-button>
                                        </div>
                                    </div>
                                    <div v-else class="d-flex align-items-center justify-content-center" style="width: 100%; height: 120px; border: 1px solid black;">
                                        <b-img v-if="data.sign_patient" fluid :src="urlbe+data.sign_patient" style="max-height: 118px;" />
                                    </div>
                                    <div>({{ data.patient_name }})</div>
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
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue';
import moment from 'moment';
import vSelect from 'vue-select';
import { mapState } from 'vuex';

export default {
    components: {
        vSelect
    },
    data() {
        return {
            useSavedSignature: true,
            urlbe:process.env.VUE_APP_IMAGE,
            userData: JSON.parse(localStorage.getItem('userData')),
            // dateNow:moment(new Date()).format('DD-MM-YYYY HH:MM'),
            dateNow: null,
            is_chronic:1,
            data:{},
            RecipeTable: {
                fields: [
                    { key: 'criteria', label: 'Kriteria Pengkajian' },
                    { key: 'yes', label: 'Ya', thClass: 'text-center' },
                    { key: 'no', label: 'Tidak', thClass: 'text-center' },
                ],
                items: [
                    { criteria: 'SYARAT ADMINISTRASI', yes: '', no: '' },
                    { criteria: 'SEP', yes: true, no: '' },
                    { criteria: 'Identitas Pasien (Nama, Domisili, Tgl Lahir)', yes: true, no: '' },
                    { criteria: 'Paraf Dokter', yes: true, no: '' },
                    { criteria: 'ASPEK FARMASTETIK', yes: '', no: '' },
                    { criteria: 'Nama, Bentuk, Kekuatan', yes: true, no: '' },
                    { criteria: 'Jumlah Obat', yes: true, no: '' },
                    { criteria: 'Signa / Aturan Pakai', yes: true, no: '' },
                    { criteria: 'ASPEK KLINIS', yes: '', no: '' },
                    { criteria: 'Tepat Obat', yes: true, no: '' },
                    { criteria: 'Tepat Indikasi', yes: true, no: '' },
                    { criteria: 'Tepat Dosis', yes: true, no: '' },
                    { criteria: 'Tepat Rute', yes: true, no: '' },
                    { criteria: 'Tepat Waktu', yes: true, no: '' },
                    { criteria: 'Tidak Duplikasi Terapi', yes: true, no: '' },
                    { criteria: 'Tidak Ada Alergi obat & ROTD', yes: true, no: '' },
                    { criteria: 'Tidak Ada interaksi Obat', yes: true, no: '' },
                    { criteria: 'Tidak Ada Kontra Indikasi', yes: true, no: '' },
                ]
            },
            DrugTable: {
                fields: [
                    { key: 'criteria', label: 'Kriteria Pengkajian' },
                    { key: 'yes', label: 'Ya', thClass: 'text-center' },
                    { key: 'no', label: 'Tidak', thClass: 'text-center' },
                ],
                items: [
                    { criteria: 'SYARAT ADMINISTRASI', yes: '', no: '' },
                    { criteria: 'Nama Pasien', yes: true, no: '' },
                    { criteria: 'Nama Obat', yes: true, no: '' },
                    { criteria: 'Dosis, Bentuk, Kekuatan Sediaan', yes: true, no: '' },
                    { criteria: 'Tepat ED', yes: true, no: '' },
                    { criteria: 'Rute Pemberian', yes: true, no: '' },
                    { criteria: 'Waktu Frekuensi Aturan Pakai', yes: true, no: '' },
                ]
            },
            ChronicOptions: [
                { value: 1, label: "Non Kronis" },
                { value: 2, label: "Obat Kronis" },
            ],
            SignTable: {
                fields: [
                    { key: 'criteria', label: 'Kriteria' },
                    { key: 'sign', label: 'Paraf' },
                ],
                items: [
                    { criteria: 'Penerimaan', sign: '' },
                    { criteria: 'Penyiapan Obat Jadi', sign: '' },
                    { criteria: 'Penyiapan Racikan', sign: '' },
                    { criteria: 'Penyerahan', sign: '' },
                ]
            },
            // patient: {},
        }
    },
    mounted() {
        window.addEventListener('keydown', this.handleKeyPress);
        this.dateNow = this.getFormattedDateTime()
        if (this.$route.params.id) {
            this.id = this.$route.params.id
            this.getData(this.$route.params.id)
        }
    },
    beforeDestroy() {
        window.removeEventListener('keydown', this.handleKeyPress);
    },
    methods: {
        clearSignature() {
            this.$refs.signaturePad.clearSignature();
        },

        saveSignature() {
            const { isEmpty: isEmpty1, data: data1 } = this.$refs.signaturePad.saveSignature();

            console.log('Pad 1:', isEmpty1, data1);
            this.disabled_btn = true
            this.validationSignature(data1)
            this.$nextTick(() => {
                setTimeout(() => {
                    const modalContent = document.querySelector('.ql-editor');
                    if (modalContent) {
                        modalContent.scrollTop = modalContent.scrollHeight;
                    }
                }, 400);
            });
        },

        validationSignature(data1) {
            let obj = {
                is_chronic: this.is_chronic,
                sign_patient: data1,
                extension: 'image/png',
                id: this.id
            }

            try {

                this.$store.dispatch('prescription/formPostPutTransactionDone', obj)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.clearSignature()
                        location.reload();
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)
            } finally {
                this.disabled_btn = false
            }
        },
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                employee: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },
        handleKeyPress(event) {
            if (event.ctrlKey && event.key === 'p') {
                event.preventDefault();
                this.printCanvas();
            }
        },
        printCanvas() {
            this.useSavedSignature = false

            setTimeout(() => {
                const canvas = this.$refs.canvas;
                const originalContents = document.body.innerHTML;
                document.body.innerHTML = canvas.innerHTML;
                window.print();
                document.body.innerHTML = originalContents;
                location.reload()

            }, 400);
        },
        closeWindow(){
            window.close()
        },
        handleBack() {
            this.$router.go(-1);
        },
        getData (id) {
			this.$store.dispatch('prescription/getById', id).then(data => {
                this.data = data[0]
                this.is_chronic = data[0].is_chronic
                // console.log('Data: ', this.data);

                // this.$store.dispatch('registration/getById', this.data.registration_destination_id).then(data => {
                //     this.patient = data[0]
                //     console.log('patient', this.patient);
                // })
            })
        },
        localDate(input_date) {
            return moment(input_date).locale("id").format('DD MMMM YYYY')
        },
        localDateTime(input) {
            return moment(input).locale("id").format('DD MMMM YYYY HH:MM')
        },
        getFormattedDateTime() {
            const now = new Date();

            const monthNames = [
                "Januari", "Februari", "Maret", "April", "Mei", "Juni",
                "Juli", "Agustus", "September", "Oktober", "November", "Desember"
            ];

            const day = now.getDate();
            const month = monthNames[now.getMonth()];
            const year = now.getFullYear();

            const hours = now.getHours().toString().padStart(2, '0');
            const minutes = now.getMinutes().toString().padStart(2, '0');

            return `${day} ${month} ${year} ${hours}:${minutes}`;
        }
    },
};

</script>
<style>
@import './../../../../@core/css/main/main.min.css';

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

/* .canvas-wrapper {
    display: flex;
    justify-content: center;
    padding-bottom: 40px;
} */
/* #canvas {
    width: 21.2cm !important;
    height: 100% !important;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    padding: 40px;
} */

.with-line {
    text-decoration: underline;
    text-decoration-thickness: 2px;
    text-underline-offset: 8px;
}

.w-40 {
    width: 40%;
}

.canvas-footer {
    position: absolute;
    bottom: 0;
}
</style>

<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>