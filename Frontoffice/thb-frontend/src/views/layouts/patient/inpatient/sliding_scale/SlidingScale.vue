<template>
    <div>
        <patientMenu selectedMenu="menu22" />
        <section class="card mt-32">
            <div class="card-header bg-indigo-200">
                <span class="text-bold-18">Data Dasar</span>
            </div>
            <div class="card-body">
                <div class="row" style="font-size: 14px">
                    <div class="col-lg-4">
                        <div class="font-weight-bold">Nama</div>
                        <div>{{ regData.name }} ({{ parsed_age(regData.birth_date) }})</div>
                        <div>{{ regData.room_name }} ({{ regData.room_name }})</div>
                    </div>
                    <div class="col-lg-4">
                        <div class="font-weight-bold">Tanggal Lahir</div>
                        <div>{{ localDate(regData.birth_date) }}</div>
                    </div>
                    <div class="col-lg-4">
                        <div class="font-weight-bold">No RM</div>
                        <div>{{ regData.norm }}</div>
                    </div>
                </div>
            </div>
        </section>
        <section class="mt-32">
            <div class="d-flex justify-content-end">
                <b-button @click="openModalSliding" size="sm" variant="primary">
                    <feather-icon icon="PlusIcon" class="mr-1"/> Tambah
                </b-button>
            </div>
            <b-table-simple small bordered responsive class="mt-24">
                <b-thead class="text-bold-14 text-center">
                    <b-tr>
                        <b-th rowspan="2" style="min-width: 100px;">Aksi</b-th>
                        <b-th rowspan="2" style="min-width: 100px;">Tangal</b-th>
                        <b-th rowspan="2" style="min-width: 150px;">Keterangan</b-th>
                        <b-th colspan="24">Waktu / Jam</b-th>
                    </b-tr>
                    <b-tr>
                        <template v-for="i in 24">
                            <b-th style="vertical-align: middle; min-width: 70px;">{{ i-1 }}</b-th>
                        </template>
                    </b-tr>
                </b-thead>
                <b-tbody class="text-regular-12">
                    <template v-if="sliding_list && sliding_list.length > 0">
                        <template v-for="(item, index_i) in sliding_list">
                            <b-tr>
                                <b-td rowspan="4">
                                    <b-button @click="openModalItem(item.id)" size="sm" variant="w3green">
                                        <feather-icon icon="PlusIcon" class="mr-1"/> Item
                                    </b-button>
                                </b-td>
                                <b-td rowspan="4">{{ item.monitoring_date }}</b-td>
                                <b-td>GDS (Hiperglikemi)</b-td>
                                <template v-for="j in 24">
                                    <b-td class="text-center">
                                        <template v-if="item.sliding_scale_insulin_items && item.sliding_scale_insulin_items.length > 0">
                                            <template v-for="(list, index_j) in item.sliding_scale_insulin_items">
                                                <template v-if="getSelectedTime(j, list.time)">{{ list.gds_hiperglikemia }}</template>
                                            </template>
                                        </template>
                                    </b-td>
                                </template>
                            </b-tr>
                            <template v-for="m in 3">
                                <b-tr>
                                    <b-td>
                                        <template v-if="m == 1">Novorapid</template>
                                        <template v-if="m == 2">GDS (Hipoglikemi)</template>
                                        <template v-if="m == 3">Novorapid</template>
                                    </b-td>
                                    <template v-for="n in 24">
                                        <b-td class="text-center">
                                            <template v-if="item.sliding_scale_insulin_items && item.sliding_scale_insulin_items.length > 0">
                                                <template v-for="(list, index_j) in item.sliding_scale_insulin_items">
                                                    <template v-if="m == 1">
                                                        <span v-if="getSelectedTime(n, list.time)">{{ list.novorapid_hiperglikemia }}</span>
                                                    </template>
                                                    <template v-if="m == 2">
                                                        <span v-if="getSelectedTime(n, list.time)">{{ list.gds_hipoglikemia }}</span>
                                                    </template>
                                                    <template v-if="m == 3">
                                                        <span v-if="getSelectedTime(n, list.time)">{{ list.novorapid_hipoglikemia }}</span>
                                                    </template>
                                                </template>
                                            </template>
                                        </b-td>
                                    </template>
                                </b-tr>
                            </template>
                        </template>
                    </template>
                </b-tbody>
            </b-table-simple>
        </section>
        <b-modal v-model="active.modal_sliding" title="Tambah Tanggal" no-close-on-backdrop>
            <div>
                <label class="c-form-label">Tanggal Monitoring</label>
                <b-form-input v-model="form_sliding.monitoring_date" type="date" />
            </div>
            <template #modal-footer>
                <div class="d-flex justify-content-end">
                    <b-button @click="validationSliding" :disabled="active.disabled_sliding" size="sm" variant="primary">
                        <feather-icon icon="SaveIcon" /> Simpan
                    </b-button>
                </div>
            </template>
        </b-modal>
        <b-modal v-model="active.modal_item" title="Tambah Item" no-close-on-backdrop>
            <div>
                <label class="c-form-label">Jam Monitoring</label>
                <b-form-input v-model="form_item.time" type="time" />
            </div>
            <div class="mt-10">
                <label class="c-form-label">Hiperglikemia</label>
                <b-form-input v-model="form_item.gds_hiperglikemia" type="number" />
            </div>
            <div class="mt-10">
                <label class="c-form-label">Hipoglikemia</label>
                <b-form-input v-model="form_item.gds_hipoglikemia" type="number" />
            </div>
            <template #modal-footer>
                <div class="d-flex justify-content-end">
                    <b-button @click="validationItem" :disabled="active.disabled_item" size="sm" variant="primary">
                        <feather-icon icon="SaveIcon" /> Simpan
                    </b-button>
                </div>
            </template>
        </b-modal>
    </div>
</template>
<script>
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import patientMenu from '@/@core/components/tab-menu/patientMenu.vue';
import ModernLabel from '@/@core/components/label/ModernLabel.vue';
import vSelect from 'vue-select'
import moment from 'moment'

export default {
    name: 'FluidBalance',
    components: {
        patientMenu, ModernLabel, vSelect
    },
    data() {
        return {
            userData: JSON.parse(localStorage.getItem('userData')),
            reg_des_id: '',
            regData: {},
            active: {
                modal_sliding: false,
                modal_item: false,
                disabled_sliding: false,
                disabled_item: false,
            },
            params: {
                registration_destination_id: ''
                // offset: 0,
                // limit: 20,
            },
            sliding_list: [],
            form_sliding: {
                id: '',
                registration_destination_id: '',
                employee_nurse_id: '',
                monitoring_date: moment(new Date()).format('YYYY-MM-DD'),
            },

            form_item: {
                sliding_scale_insulin_id: '',
                time: moment(new Date()).format('HH:mm'),
                gds_hiperglikemia: '',
                gds_hipoglikemia: '',
            }
        }
    },

    methods: {
        openModalSliding() {
            this.active.modal_sliding = true
            this.resetFormSliding()
        },

        resetFormSliding() {
            this.form_sliding.id = '',
            this.form_sliding.registration_destination_id = '',
            this.form_sliding.employee_nurse_id = '',
            this.form_sliding.monitoring_date = moment(new Date()).format('YYYY-MM-DD')
        },

        validationSliding() {
            this.form_sliding.registration_destination_id = this.reg_des_id
            this.form_sliding.employee_nurse_id = this.userData.employee_id

            if (this.form_sliding.monitoring_date) {
                try {
                    this.active.disabled_sliding = true
                    this.$store.dispatch('slidingscale/postPutSlidingScale', this.form_sliding)
                    .then(
                        res => {
                            this.notification('Success','CheckIcon','success','You have successfully')
                            this.active.modal_sliding = false
                            this.active.disabled_sliding = false
                            this.resetFormSliding()
                            this.getSlidingList()
                        } 
                    )
                    .catch(({error, response}) => {
                        this.notification('Failed','CloseIcon','danger',`${error}`)
                        this.active.disabled_sliding = false
                    })
                } catch (error) {
                    this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                    console.error('Error catch at: ', error)                
                    this.active.disabled_sliding = false
                }
            } else {
                this.notification('Warning','CloseIcon','warning','Tanggal Monitoring Wajib Diisi')
            }
        },

        getSlidingList() {
            this.params.registration_destination_id = this.reg_des_id

            try {                
                this.$store.dispatch('slidingscale/getListSlidingScale', this.params).then((response) => {
                    this.sliding_list = response.rows
                    // this.totalRows = response.iTotal
                }, error => {
                    console.error('Error while GET slidingscale/getListSlidingScale at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET slidingscale/getListSlidingScale at: ', error);
            }
        },

        openModalItem(sliding_id) {
            this.resetFormItem()
            this.form_item.sliding_scale_insulin_id = sliding_id
            this.active.modal_item = true
        },

        resetFormItem() {
            this.form_item.sliding_scale_insulin_id = ''
            this.form_item.time = moment(new Date()).format('HH:mm')
            this.form_item.gds_hiperglikemia = ''
            this.form_item.gds_hipoglikemia = ''
        },

        validationItem() {
            if (this.form_item.time) {
                try {
                    this.active.disabled_item = true
                    this.$store.dispatch('slidingscale/postPutSlidingItem', this.form_item)
                    .then(
                        res => {
                            this.notification('Success','CheckIcon','success','You have successfully')
                            this.active.modal_item = false
                            this.active.disabled_item = false
                            this.resetFormItem()
                            this.getSlidingList()
                        } 
                    )
                    .catch(({error, response}) => {
                        this.notification('Failed','CloseIcon','danger',`${error}`)
                        this.active.disabled_item = false
                    })
                } catch (error) {
                    this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                    console.error('Error catch at: ', error)                
                    this.active.disabled_item = false
                }
            } else {
                this.notification('Warning','CloseIcon','warning','Jam Monitoring Wajib Diisi')
            }
        },

        getSelectedTime(n, time) {
            let column_number = n-1
            let split_time = time.slice(0, 2) 

            if (column_number == split_time) {
                return true
            }
            return false
        },

        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                position: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },

        localDate(input_date) {
            if (!input_date || input_date === "0000-00-00") {
                return "";
            }

            const parsed = moment(input_date);
            if (!parsed.isValid()) {
                return "";
            }

            return moment(input_date).locale("id").format('DD MMMM YYYY')
        },

        parsed_age(input_date) {
            if (!input_date || input_date === "0000-00-00") {
                return "";
            }

            const parsed = moment(input_date);
            if (!parsed.isValid()) {
                return "";
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

        getData(regDesId) {
			this.$store.dispatch('registration/getById', regDesId).then(data => {
                this.regData = data[0]
            })
        },

    },
   
    created () {
        if (this.$route.params.id) {
            this.reg_des_id = this.$route.params.id
            this.getData(this.$route.params.id)
            this.getSlidingList()
        }
    }
}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>