<template>
    <div>
        <div class="col label-head">
            <span class="px-16 text-medium-24 text-neutral-500">Hasil Pemeriksaan Laboratorium</span>
        </div>
        <section class="col mt-32">
            <b-table-simple responsive class="text-medium-14">
                <b-tbody>
                    <b-tr>
                        <b-td style="width: 10%;">No. Reg / RM</b-td>
                        <b-td style="width: 2%;">:</b-td>
                        <b-td style="width: 38%;" class="font-weight-bold">{{regData.code}} / {{regData.norm}}</b-td>
                        <b-td style="width: 10%;">Unit</b-td>
                        <b-td style="width: 2%;">:</b-td>
                        <b-td style="width: 38%;" class="font-weight-bold">LABORATORIUM</b-td>
                    </b-tr>
                    <b-tr>
                        <b-td style="width: 10%;">Nama Pasien</b-td>
                        <b-td style="width: 2%;">:</b-td>
                        <b-td style="width: 38%;" class="font-weight-bold">{{regData.name}}</b-td>
                        <b-td style="width: 10%;">No. Order</b-td>
                        <b-td style="width: 2%;">:</b-td>
                        <b-td style="width: 38%;" class="font-weight-bold">{{orderData.no_request}}</b-td>
                    </b-tr>
                    <b-tr>
                        <b-td style="width: 10%;">Tanggal Lahir</b-td>
                        <b-td style="width: 2%;">:</b-td>
                        <b-td style="width: 38%;" class="font-weight-bold">{{regData.birth_date}}</b-td>
                        <b-td style="width: 10%;">Tgl. Permintaan</b-td>
                        <b-td style="width: 2%;">:</b-td>
                        <b-td style="width: 38%;" class="font-weight-bold">{{orderData.date_request}}</b-td>
                    </b-tr>
                    <b-tr>
                        <b-td style="width: 10%;">Kelamin</b-td>
                        <b-td style="width: 2%;">:</b-td>
                        <b-td style="width: 38%;" class="font-weight-bold">{{regData.sex_name}}</b-td>
                        <b-td style="width: 10%;">Dokter Pengirim</b-td>
                        <b-td style="width: 2%;">:</b-td>
                        <b-td style="width: 38%;" class="font-weight-bold">{{orderData.employee_doctor_name}}</b-td>
                    </b-tr>
                </b-tbody>
            </b-table-simple>
        </section>
        <section class="col mt-40">
            <p class="text-bold-14 text-center">HASIL PEMERIKSAAN LABORATORIUM</p>
            <div class="mt-16">
                <b-table-simple fixed bordered responsive>
                    <b-thead class="text-bold-14 text-center">
                        <b-tr>
                            <b-th>PARAMETER</b-th>
                            <b-th>HASIL</b-th>
                            <b-th>NILAI RUJUKAN</b-th>
                            <b-th>SATUAN</b-th>
                        </b-tr>
                    </b-thead>
                    <b-tbody class="text-regular-14">
                        <b-tr>
                            <b-td colspan="4" class="text-bold-14">DARAH LENGKAP PEREMPUAN</b-td>
                        </b-tr>
                        <b-tr>
                            <b-td>- HEMOGLOBIN (HB)</b-td>
                            <b-td><b-form-input></b-form-input></b-td>
                            <b-td><b-textarea rows="4"></b-textarea></b-td>
                            <b-td><b-form-input></b-form-input></b-td>
                        </b-tr>
                        <b-tr>
                            <b-td>- HEMATOKRIT (HT)</b-td>
                            <b-td><b-form-input></b-form-input></b-td>
                            <b-td><b-textarea rows="4"></b-textarea></b-td>
                            <b-td><b-form-input></b-form-input></b-td>
                        </b-tr>
                        <b-tr>
                            <b-td colspan="4" class="text-bold-14">HITUNG JENIS</b-td>
                        </b-tr>
                        <b-tr>
                            <b-td>- BASOFIL</b-td>
                            <b-td><b-form-input></b-form-input></b-td>
                            <b-td><b-textarea rows="4"></b-textarea></b-td>
                            <b-td><b-form-input></b-form-input></b-td>
                        </b-tr>
                    </b-tbody>
                </b-table-simple>
            </div>
            <!-- <div class="mt-24">
                <div>
                    <label class="c-form-label">Kesimpulan:</label>
                    <b-textarea rows="4"></b-textarea>
                </div>
                <div class="mt-12">
                    <label class="c-form-label">Catatan :</label>
                    <b-textarea rows="4"></b-textarea>
                </div>
                <div class="mt-12">
                    <label class="c-form-label">Saran :</label>
                    <b-textarea rows="4"></b-textarea>
                </div>
            </div> -->
            <div class="row mt-24 mx-0">
                <b-button size="sm" variant="primary-500" class="mr-2">
                    <feather-icon icon="SaveIcon" /> Simpan
                </b-button>
                <router-link :to="{ path: '/laboratory/list/process/result/print/'+ orderData.id}">
                    <b-button size="sm" variant="outline-secondary" class="mr-2">
                        <feather-icon icon="PrinterIcon" /> Cetak
                    </b-button>
                </router-link>
                <!-- <b-button size="sm" variant="outline-secondary">
                    <feather-icon icon="PrinterIcon" /> Cetak (Non Kop)
                </b-button> -->
            </div>
        </section>
    </div>
</template>
<script>
export default {
    data() {
        return {
            laboratory_action: {
                field: [
                    { key: 'print_all', label: 'PRINT ALL' },
                    { key: 'costing_id', label: 'TGL. PERMINTAAN' },
                    { key: 'costing_name', label: 'TINDAKAN LABORATORIUM' },
                    { key: 'var_1', label: 'HARGA TINDAKAN' },
                    { key: 'action', label: 'TINDAKAN' },
                    { key: 'bill_status', label: 'STATUS BILLING' },
                ],
                // item: [
                //     {
                //         request_date: '24-03-2023 11:04:05',
                //         costing_name: 'Hematologi Lengkap',
                //         var_1: '104.500,00',
                //         bill_status: 'Sudah Melunasi Pembayaran',
                //     },
                // ],
            },
            orderData:{},
            regData:{}
        }
    },
    filters:{
        currency(value) {
            return new Intl.NumberFormat("en-US", { style: "currency", currency: "IDR" }).format(value)
        }
    },
    methods: {
        refresh () {
            router.go(router.currentRoute)
        },
        getData (id) {
			this.$store.dispatch('laboratory/getByIdOrder', id).then(data => {
                this.orderData = data[0]
                this.regData = data[0].regData
            })
        },
        async getDataRequest(id) {
            try {
                const response = await this.$store.dispatch('laboratory/getResultById', id)
                console.log(response)
                // if (response.rows.length > 0) {
                //     this.LaboratoryOrder = response.rows
                // }
            } catch (error) {
                this.notification('Failed', 'CloseIcon', 'danger', 'Something Wrong!');
            }
        }
    },
    mounted () {
        if (this.$route.params.id) {
            // this.show = true
            this.id = this.$route.params.id
            this.getData(this.$route.params.id)
            this.getDataRequest(this.$route.params.item)
        }
    }
}
</script>
<style scoped>
.label-head::before {
    content: '';
    border-left: 6px solid #648DE1;
}
</style>