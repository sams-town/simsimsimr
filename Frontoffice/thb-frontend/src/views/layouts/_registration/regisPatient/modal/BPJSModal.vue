<template>
    <div>
        <b-button @click="onSelectType()" size="sm" variant="pink-500" class="text-white"><b-icon icon="type-bold" scale="0.8"></b-icon> BPJS</b-button>
        <b-modal v-model="open_modal.first_select_type" @close="onSelectType()" title="BPJS" hide-footer>
            <div class="row">
                <div class="col-lg-6">
                    <div @click="onBPJSReference()" class="card bg-success-300 pointer">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <feather-icon icon="SearchIcon" />
                                <span class="text-bold-18">Rujukan BPJS</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mt-16 mt-lg-0">
                    <div @click="onJKNQueue()" class="card bg-orange-300 pointer">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <feather-icon icon="SearchIcon" />
                                <span class="text-bold-18">Antrean JKN</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </b-modal>
        
        <b-modal size="xl" v-model="open_modal.second_bpjs_reference" @close="onBPJSReference()" title="Rujukan BPJS" hide-footer>
            <section>
                <label class="c-form-label">Pencarian Berdasarkan</label>
                <b-input-group>
                    <b-input-group-prepend>
                        <b-form-select v-model="filter_bpjs_form.selected_option" :options="bpjs_options"></b-form-select>
                    </b-input-group-prepend>
                    <b-form-input v-model="filter_bpjs_form.search"></b-form-input>
                    <b-input-group-append>
                        <b-button>
                            <feather-icon icon="SearchIcon" />
                        </b-button>
                    </b-input-group-append>
                </b-input-group>
            </section>
            <section class="mt-24">
                <div class="card">
                    <div class="card-body">
                        <div class="label-head">
                            <span class="px-16 text-bold-24 text-neutral-700 text-capitalize">Mapping Tarif Perusahaan</span>
                        </div>
                        <div class="mt-16">
                            <b-table bordered responsive small head-variant="light" :fields="ReferenceTable.fields" :items="BpjsReferencefiltered" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="mt-12 text-center"></b-table>
                        </div>
                    </div>
                </div>
                <div class="card mt-16">
                    <div class="card-body">
                        <div class="label-head">
                            <span class="px-16 text-bold-24 text-neutral-700 text-capitalize">Kesamaan Nomor kartu</span>
                        </div>
                        <div class="mt-16">
                            <b-table bordered responsive small head-variant="light" :fields="CardsTable.fields" :items="BpjsCardfiltered" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="mt-12 text-center">
                                <template #cell(action)>
                                    <div>
                                        <b-button variant="info-600" size="sm" @click="getPatientOnReg()">
                                            <feather-icon icon="PaperclipIcon" /> Ambil
                                        </b-button>
                                    </div>
                                </template>
                            </b-table>
                        </div>
                    </div>
                </div>
            </section>
        </b-modal>

        <b-modal size="lg" v-model="open_modal.second_jkn_queue" @close="onJKNQueue()" title="Antrean JKN" hide-footer>
            <section>
                <label class="c-form-label">Pencarian Berdasarkan</label>
                <b-input-group>
                    <b-input-group-prepend>
                        <b-form-select v-model="filter_jkn_form.selected_option" :options="jkn_options"></b-form-select>
                    </b-input-group-prepend>
                    <b-form-input v-model="filter_jkn_form.search"></b-form-input>
                    <b-input-group-append>
                        <b-button>
                            <feather-icon icon="SearchIcon" />
                        </b-button>
                    </b-input-group-append>
                </b-input-group>
            </section>
            <section class="mt-24">
                <div class="card">
                    <div class="card-body">
                        <div class="label-head">
                            <span class="px-16 text-bold-24 text-neutral-700 text-capitalize">Data Antrean</span>
                        </div>
                        <div class="mt-16">
                            <b-table bordered responsive small head-variant="light" :fields="JknQueueTable.fields" :items="JknQueuefiltered" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="mt-12 text-center">
                                <template #cell(booking_code)="data">
                                    <div>
                                        <b-button @click="onJknConfirmation()" size="sm" variant="outline-primary-700">{{ data.value }}</b-button>
                                    </div>
                                </template>
                            </b-table>
                        </div>
                    </div>
                </div>
                <div class="card mt-16">
                    <div class="card-body">
                        <div class="label-head">
                            <span class="px-16 text-bold-24 text-neutral-700 text-capitalize">Data Peserta Rujukan</span>
                        </div>
                        <div class="mt-16">
                            <b-table bordered responsive small head-variant="light" :fields="JknReferenceTable.fields" :items="JknReferenceFiltered" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="mt-12 text-center"></b-table>
                        </div>
                    </div>
                </div>
                <div class="card mt-16">
                    <div class="card-body">
                        <div class="label-head">
                            <span class="px-16 text-bold-24 text-neutral-700 text-capitalize">Kesamaan Nomor kartu</span>
                        </div>
                        <div class="mt-16">
                            <b-table bordered responsive small head-variant="light" :fields="JknCardsTable.fields" :items="JknCardsFiltered" thead-class="text-bold-14" tbody-tr-class="text-regular-12" class="mt-12 text-center">
                                <template #cell(action)>
                                    <div>
                                        <b-button variant="info-600" size="sm" @click="getPatientOnReg()">
                                            <feather-icon icon="PaperclipIcon" /> Ambil
                                        </b-button>
                                    </div>
                                </template>
                            </b-table>
                        </div>
                    </div>
                </div>
            </section>
        </b-modal>

    </div>
</template>

<script>
export default {
    data() {
        return {
            open_modal: {
                first_select_type: false,
                second_bpjs_reference: false,
                second_jkn_queue: false
            },
            bpjs_options: [
                { value: null, text: 'Please select an option' },
                { value: 0, text: 'No Rujukan / No Kartu' },
            ],
            jkn_options: [
                { value: null, text: 'Please select an option' },
                { value: 0, text: 'No Booking' },
            ],
            filter_bpjs_form: {
                selected_option: 0,
                search: '',
            },
            filter_jkn_form: {
                selected_option: 0,
                search: '',
                selected_booking_code: '',
            },
            ReferenceTable: {
                fields: [
                    { key: 'no', label: 'NO' },
                    { key: 'reference_number', label: 'No Rujukan' },
                    { key: 'reference_date', label: 'Tgl Rujukan' },
                    { key: 'card_number', label: 'No Kartu' },
                    { key: 'card_status', label: 'Status Kartu' },
                    { key: 'name', label: 'Nama' },
                    { key: 'ppk', label: 'PPK Perujuk' },
                    { key: 'specialist', label: 'Sub/Spesialis' },
                ],
                items: [
                    {
                        no: '1',
                        reference_number: '100708010623P000012',
                        reference_date: '2023-06-05',
                        card_number: '0003502569857',
                        card_status: 'AKTIF',
                        name: 'BINTANG',
                        ppk: 'BOJONG',
                        specialist: 'PENYAKIT DALAM',
                    },
                ],
            },
            CardsTable: {
                fields: [
                    { key: 'norm', label: 'No RM' },
                    { key: 'name', label: 'Nama' },
                    { key: 'birth_date', label: 'Tgl Lahir' },
                    { key: 'identity_number', label: 'NIK' },
                    { key: 'card_number', label: 'No Kartu' },
                    { key: 'sex_name', label: 'Kelamin' },
                    { key: 'action', label: 'Aksi' },
                ],
                items: [
                    {
                        norm: '00610992',
                        name: 'BINTANG',
                        birth_date: '1989-11-15',
                        identity_number: '3604026111870001',
                        card_number: '0003502569857',
                        sex_name: 'P',
                    },
                ],
            },
            JknQueueTable: {
                fields: [
                    { key: 'no', label: 'No' },
                    { key: 'booking_code', label: 'Kode Booking' },
                    { key: 'queue_number', label: 'No Antrean' },
                    { key: 'card_number', label: 'No Kartu' },
                    { key: 'queue_type', label: 'Jenis Antrean' },
                    { key: 'control_date', label: 'Tanggal Periksa' },
                    { key: 'poli_name', label: 'Nama Poli' },
                ],
                items: [
                    {
                        no: '1',
                        booking_code: 'BRP02070001',
                        queue_number: 'ARP0001',
                        card_number: '0003502569857',
                        queue_type: 'Pendaftaran',
                        control_date: '2023-06-05',
                        poli_name: 'PENYAKIT DALAM',
                    },
                ],
            },
            JknReferenceTable: {
                fields: [
                    { key: 'no', label: 'NO' },
                    { key: 'reference_number', label: 'No Rujukan' },
                    { key: 'reference_date', label: 'Tgl Rujukan' },
                    { key: 'card_number', label: 'No Kartu' },
                    { key: 'card_status', label: 'Status Kartu' },
                    { key: 'name', label: 'Nama' },
                    { key: 'ppk', label: 'PPK Perujuk' },
                    { key: 'specialist', label: 'Sub/Spesialis' },
                ],
                items: [
                    {
                        booking_code: 'BRP02070001',
                        no: '1',
                        reference_number: '100708010623P000012',
                        reference_date: '2023-06-05',
                        card_number: '0003502569857',
                        card_status: 'AKTIF',
                        name: 'BINTANG',
                        ppk: 'BOJONG',
                        specialist: 'PENYAKIT DALAM',
                    },
                ],
            },
            JknCardsTable: {
                fields: [
                    { key: 'norm', label: 'No RM' },
                    { key: 'name', label: 'Nama' },
                    { key: 'birth_date', label: 'Tgl Lahir' },
                    { key: 'identity_number', label: 'NIK' },
                    { key: 'card_number', label: 'No Kartu' },
                    { key: 'sex_name', label: 'Kelamin' },
                    { key: 'action', label: 'Aksi' },
                ],
                items: [
                    {
                        booking_code: 'BRP02070001',
                        norm: '00610992',
                        name: 'BINTANG',
                        birth_date: '1989-11-15',
                        identity_number: '3604026111870001',
                        card_number: '0003502569857',
                        sex_name: 'P',
                    },
                ],
            },

        }
    },
    methods: {
        onSelectType() {
            this.open_modal.first_select_type = !this.open_modal.first_select_type
        },
        onBPJSReference() {
            this.open_modal.second_bpjs_reference = !this.open_modal.second_bpjs_reference
        },
        onJKNQueue() {
            this.open_modal.second_jkn_queue = !this.open_modal.second_jkn_queue
        },
        getPatientOnReg() {
            this.open_modal.first_select_type = false
            this.open_modal.second_bpjs_reference = false
            this.open_modal.second_jkn_queue = false
        },
        onJknConfirmation() {
            const confirmation = window.confirm('Memilih Nomor Booking' + this.JknQueueTable.items[0].booking_code);
            if (confirmation) {
                this.onSelectedJKNID(this.JknQueueTable.items[0].booking_code);
                console.log('Tombol "OK" pada alert telah ditekan.');
            } else {
                console.log('Tombol "Cancel" pada alert telah ditekan.');
            }
        },
        onSelectedJKNID(booking_code) {
            return this.filter_jkn_form.selected_booking_code = booking_code;
        },
    },
    computed: {
        BpjsReferencefiltered() {
            const searchTerm = this.filter_bpjs_form.search.toLowerCase().trim()
            if (!searchTerm) {
                return null
            }
            return this.ReferenceTable.items.filter(item => {
                return item.reference_number.toLowerCase().includes(searchTerm) || item.card_number.toLowerCase().includes(searchTerm)
            })
        },
        BpjsCardfiltered() {
            const searchTerm = this.filter_bpjs_form.search.toLowerCase().trim()
            if (!searchTerm) {
                return null
            }
            return this.CardsTable.items.filter(item => {
                return item.card_number.toLowerCase().includes(searchTerm)
            })
        },
        JknQueuefiltered() {
            const searchTerm = this.filter_jkn_form.search.toLowerCase().trim()
            if (!searchTerm) {
                return null
            }
            return this.JknQueueTable.items.filter(item => {
                return item.booking_code.toLowerCase().includes(searchTerm)
            })
        },
        JknReferenceFiltered() {
            const filtered = this.filter_jkn_form.selected_booking_code.toLowerCase().trim()
            if (!filtered) {
                return null
            }
            return this.JknReferenceTable.items.filter(item => {
                return item.booking_code.toLowerCase().includes(filtered)
            })
        },
        JknCardsFiltered() {
            const filtered = this.filter_jkn_form.selected_booking_code.toLowerCase().trim()
            if (!filtered) {
                return null
            }
            return this.JknCardsTable.items.filter(item => {
                return item.booking_code.toLowerCase().includes(filtered)
            })
        },
    },
}
</script>
<style>
.label-head::before {
    content: '';
    border-left: 6px solid #648DE1;
}
</style>