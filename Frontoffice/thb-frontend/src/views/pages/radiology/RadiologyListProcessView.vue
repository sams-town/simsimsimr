<template>
    <div>
        <Layout
            :layout_breadcrumb_items="[
                { text: 'Home', link: '/' },
                { text: 'Radiologi', link: '/radiology/list' },
                { text: `${regData.name}` }
            ]"
        >
        <section class="">
            <ModernLabel label="DATA PERMINTAAN RADIOLOGI PASIEN" />
            <div class="row fs-14 mt-16">
                <div class="col-lg-6">
                    <table class="table table-md mb-0" style="border: 1px solid #dee2e6;">
                        <tr>
                            <td class="align-top" style="width: 150px;">Nama Pasien</td>
                            <td class="align-top text-center" style="width: 20px;">:</td>
                            <td class="align-top">{{ regData.alias_name }}. {{ regData.name }} ({{ getSexCode(regData.sex) }})</td>
                        </tr>
                        <tr>
                            <td class="align-top">Tanggal Lahir</td>
                            <td class="align-top text-center">:</td>
                            <td class="align-top">{{ regData.birth_date }} ({{ parsedAge(regData.birth_date) }})</td>
                        </tr>
                        <tr>
                            <td class="align-top">No. RM / NIK</td>
                            <td class="align-top text-center">:</td>
                            <td class="align-top">{{ regData.norm }} / {{ regData.idcard }}</td>
                        </tr>
                        <tr>
                            <td class="align-top">No. Telepon</td>
                            <td class="align-top text-center">:</td>
                            <td class="align-top">{{ regData.phone }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-lg-6">
                    <table class="table table-md mb-0" style="border: 1px solid #dee2e6;">
                        <tr>
                            <td class="align-top" style="width: 150px;">No. Permintaan</td>
                            <td class="align-top text-center" style="width: 20px;">:</td>
                            <td class="align-top">{{ orderData.no_request }}</td>
                        </tr>
                        <tr>
                            <td class="align-top">Tgl. Permintaan</td>
                            <td class="align-top text-center">:</td>
                            <td class="align-top">{{ localFullDay(orderData.date_request) }}</td>
                        </tr>
                        <tr>
                            <td class="align-top">Pengirim</td>
                            <td class="align-top text-center">:</td>
                            <td class="align-top">{{regData.health_facility_item_name}} - {{ regData.doctor_in_charge_name }}</td>
                        </tr>
                        <tr>
                            <td class="align-top">Diagnosa</td>
                            <td class="align-top text-center">:</td>
                            <td class="align-top">
                                <li v-for="item in main_diagnose">{{ item.title }}</li>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="fs-13 mt-20">
                <div class="font-weight-bold">CATATAN KLINIS: </div>
                <div v-html="parsedBody(orderData?.clinical_indications || '-')"></div>
            </div>

            <b-table-simple small responsive bordered class="mt-10 w-100 text-center">
                <b-thead>
                    <b-tr>
                        <b-th style="width: 70px;">NO</b-th>
                        <b-th style="min-width: 140px;">TGL. PERMINTAAN</b-th>
                        <b-th style="min-width: 140px;">TINDAKAN RADIOLOGI</b-th>
                        <b-th style="min-width: 140px;">HARGA TINDAKAN</b-th>
                        <b-th style="min-width: 140px;">AKSI</b-th>
                    </b-tr>
                </b-thead>
                <b-tbody class="fs-14">
                    <template v-for="(item,index) in orderData.radiology_request_item">
                        <b-tr>
                            <b-td :rowspan="item.url.length != 0 ? 2 : 1"> {{ item.no }} </b-td>
                            <b-td>{{ item.date_request }}</b-td>    
                            <b-td>{{ item.costing_name }}</b-td>
                            <b-td>{{ item.var_1 | currency }}</b-td>
                            <b-td>
                                <template v-if="item.status == 1">Permintaan Baru</template>
                                <template v-else-if="item.status == 2">
                                    <div class="d-flex" style="padding: 0 -12px;">
                                        <div class="col-7 pl-0">
                                            <b-form-file v-model="input_image" accept="image/*" multiple @change="uploadImage($event, item.id)" autocomplete="off"></b-form-file>
                                        </div>
                                        <template v-if="item.url.length != 0">
                                            <b-button @click="isRefcontrol(item.url, null)" size="sm" variant="primary-600">Lihat Semua Hasil</b-button>
                                        </template>
                                    </div>
                                </template>
                                <b-button @click="removeRadiologyProcess(item)" 
                                    size="sm" 
                                    variant="danger-500" 
                                    class="text-white ml-2"
                                    :disabled="isDisabled"
                                >
                                    Hapus Penunjang
                                </b-button>
                            </b-td>
                        </b-tr>
                        <b-tr v-if="item.url.length != 0">
                            <b-td colspan="5">
                                <b-table-simple bordered responsive class="w-100 text-left">
                                    <b-thead>
                                        <b-tr>
                                            <b-th style="width: 300px;">Gambar</b-th>
                                            <b-th>Catatan</b-th>
                                            <b-th style="width: 180px;">Aksi</b-th>
                                        </b-tr>
                                    </b-thead>
                                    <b-tbody>
                                        <b-tr v-for="(list, index) in item.url" :key="index">
                                            <b-td>
                                                <b-img rounded :src="urlbe+list.url" style="width: 100%;" />
                                                <div>{{ list.name ? list.name : `Image-${index+1}` }}</div>
                                            </b-td>
                                            <b-td>
                                                <table class="w-100 custom-border" >
                                                    <tr>
                                                        <td style="width: 150px;">Nama Gambar</td>
                                                        <td class="align-top text-center" style="width: 30px;">:</td>
                                                        <td>{{ list?.name || `Image-${index+1}` }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Pemeriksa Radiologi</td>
                                                        <td class="align-top text-center">:</td>
                                                        <td>{{ list?.user_lab_name || '-' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Verifikator</td>
                                                        <td class="align-top text-center">:</td>
                                                        <td>{{ list?.doctor_lab_name || '-' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Tanggal Order</td>
                                                        <td class="align-top text-center">:</td>
                                                        <td>{{ localFullDate(list?.date_request || '-') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Tanggal Diproses</td>
                                                        <td class="align-top text-center">:</td>
                                                        <td>{{ localFullDate(list?.date_process || '-') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Tanggal Hasil</td>
                                                        <td class="align-top text-center">:</td>
                                                        <td>{{ localFullDate(list?.date_result || '-') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Expertise</td>
                                                        <td class="align-top text-center">:</td>
                                                        <td><span v-html="parsedBody(list?.conclusion || '-')"></span></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Kesan</td>
                                                        <td class="align-top text-center">:</td>
                                                        <td><span v-html="parsedBody(list?.advice || '-')"></span></td>
                                                    </tr>
                                                </table>
                                            </b-td>
                                            <b-td>
                                                <b-button @click="isRefcontrol(item.url, index)" size="sm" variant="primary-600" class="mb-1 mr-1">Lihat</b-button>
                                                <template v-if="orderData.status == '2' || orderData.status == '3'">
                                                    <b-button @click="onNotesModal(list)" variant="primary" size="sm" class="mr-1 mb-1">Isi Expertise</b-button>
                                                </template>
                                                <template v-if="list.status == 2">
                                                    <router-link :to="{ path: '/radiology/list/process/print-expertise/'+ id + '/' + list.radiology_request_item_id + '/' + list.id }" target="_blank">
                                                        <b-button variant="success-500" size="sm" class="mr-1 mb-1">Cetak</b-button>
                                                    </router-link>
                                                </template>
                                                <b-button @click="deleteImage(list)" variant="danger-500" size="sm" class="mb-1">Hapus</b-button>
                                            </b-td>
                                        </b-tr>
                                    </b-tbody>
                                </b-table-simple>
                            </b-td>
                        </b-tr>
                    </template>
                </b-tbody>
            </b-table-simple>

            <section class="d-flex mt-12">
                <template v-if="orderData.status == '1'">
                    <b-button :disabled="isDisabled" @click.prevent="proceedOrder" size="sm" variant="orange-400" class="text-white">
                        <feather-icon icon="SaveIcon" size="17" class="mr-1"/> PROSES
                    </b-button>
                </template>

                <template v-if="orderData.status == '3'">
                    <router-link :to="{ path: '/radiology/print/'+ id}">
                        <b-button size="sm" variant="outline-secondary" class="mr-2">
                            <feather-icon icon="PrinterIcon" class="mr-1"/> CETAK
                        </b-button>
                    </router-link>
                </template>
            </section>

            <div class="mt-32">                
                <!-- <ModernLabel :label="`ASESMEN PASIEN ${regData.alias_name}.${regData.name} di ${regData.health_facility_item_name}`" class="mb-8"/> -->
                <ModernLabel label="ASESMEN DOKTER" variant="w3green" class="mb-8"/>
                <template v-if="soap_list?.length > 0">
                    <template v-for="item in soap_list">
                        <LastDoctorAssessment 
                            :result="item" 
                            :regData="{
                                name: regData.name,
                                alias_name: regData.alias_name,
                            }" 
                        />
                    </template>
                </template>
            </div>
        </section>
        </Layout>

        <b-modal v-model="active.refcontrol" scrollable size="xl" title="Hasil Radiologi" hide-footer>
            <div class="row">
                <div class="col-12 mt-1">
                    <template v-for="(img,index) in imageview">
                        <b-img rounded :src="urlbe+img.url" style="width: 100%;"/>
                    </template>
                </div>
            </div>
        </b-modal>

        <b-modal v-model="active.notes_modal" scrollable size="md" title="Isi Expertise">
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    ANDA SEDANG MENGISI EXPERTISE
                    <span class="text-primary"> {{ regData.alias_name }}. {{ regData.name }} | {{ regData.norm }} | {{localDate(regData.birth_date)}} ({{ parsedAge(regData.birth_date) }})</span>
                </div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>
            <div>
                <div>
                    <label class="fs-13 mb-0">Ganti Gambar</label>
                    <b-form-file accept="image/*" @change="inputImageAtModal" autocomplete="off" size="sm"></b-form-file>
                </div>
                <div class="mt-8">
                    <label class="fs-13 mb-0">Pemeriksa Radiologi:</label>
                    <v-select v-model="form.user_lab_id" :options="OptionsDoctor" :reduce="item => item.value" label="title" class="select-size-sm" placeholder="-- Dokter --" />
                </div>
                <div class="mt-8">
                    <label class="fs-13 mb-0">Verifikator:</label>
                    <v-select v-model="form.doctor_lab_id" :options="OptionsDoctor" :reduce="item => item.value" label="title" class="select-size-sm" placeholder="-- Dokter --" />
                </div>
                <div class="mt-8">
                    <label class="fs-13 mb-0">Tanggal Diproses</label>
                    <b-form-input v-model="form.date_process" type="date" size="sm" />
                </div>
                <div class="mt-8">
                    <label class="fs-13 mb-0">Tanggal Hasil</label>
                    <b-form-input v-model="form.date_result" type="date" size="sm" />
                </div>
                <div class="mt-8">
                    <label class="fs-13 mb-0">Expertise:</label>
                    <textarea v-model.lazy="form.conclusion" class="form-control form-control-sm" autocomplete="off" />                    
                </div>
                <div class="mt-8">
                    <label class="fs-13 mb-0">Kesan :</label>
                    <textarea v-model.lazy="form.advice" class="form-control form-control-sm" autocomplete="off" />                    
                </div>
            </div>
            <template #modal-footer>
                <div class="d-flex justify-content-end w-100 m-0">
                    <b-button size="sm" @click.prevent="validationFormVerifikasi()" variant="orange-400" class="text-white">
                        <feather-icon icon="SaveIcon" class="mr-1"/> VERIFIKASI
                    </b-button>
                </div>
            </template>
        </b-modal>
    </div>
</template>

<script>
import { mapState } from 'vuex';
import moment from 'moment'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import dateMixin from "@/mixins/dateMixin";
import optionMixin from '@/mixins/optionMixin';
import LastDoctorAssessment from '@/views/pages/patient/detail/LastDoctorAssessment.vue'
import io from 'socket.io-client';

export default {
    mixins: [dateMixin, optionMixin],
    components: { ToastificationContent, LastDoctorAssessment },
    data() {
        return {
            // socket : io(process.env.VUE_APP_SOCKET_IO, { transports: ["websocket", "polling", "flashsocket"],withCredentials: true }),
            notif_socket: io(process.env.VUE_APP_SOCKET_IO_NOTIF, { path: '/socket.io', transports: ['websocket', 'polling', 'flashsocket'], withCredentials: true}),

            hfi_inpatient: '40f06534-07ec-4221-ad9d-dea08196079f',
            hfi_outpatient: '3cb626f5-9148-4fb1-8663-3d7f028b01a6',
            hfi_emergency: '444b3134-5b75-4b67-af72-a558d46716d7',
            
            radiologyResultField: [
                { key: 'no', label: 'No', thStyle:'width: 50px' },
                { key: 'expertise', label: 'Expertise', thStyle:'width: 500px' },
                { key: 'url', label: 'Gambar' },
            ],
            input_image: null,
            imageview:[],
            active:{
                refcontrol:false,
                notes_modal:false,
            },
            urlbe:process.env.VUE_APP_IMAGE,
            radiology_action: {
                field: [
                    { key: 'no', label: 'NO' },
                    { key: 'date_request', label: 'TGL. PERMINTAAN' },
                    { key: 'costing_name', label: 'TINDAKAN RADIOLOGI' },
                    { key: 'var_1', label: 'HARGA TINDAKAN' },
                    { key: 'status', label: 'ACTION' },
                    { key: 'bill_status', label: 'STATUS BILLING' },
                ],
            },
            form:{
                id:'',
                result: '',
                extension: '',
                status: 2,
                date_process: moment(new Date()).format('YYYY-MM-DD'),
                date_result: moment(new Date()).format('YYYY-MM-DD'),
                doctor_lab_id: '',
                user_lab_id:'',
                conclusion:'',
                notes:'',
                advice:'',
            },
            result:{
                // file_url: '',
                // file_set: 'none',
                file: null,
            },
            imageview:{},
            orderData:{},
            id:'',
            isDisabled:false,
            regData:{},
            main_diagnose: [],
            soap_list: []
        }
    },
    filters:{
        currency(value) {
            return new Intl.NumberFormat("en-US", { style: "currency", currency: "IDR" }).format(value)
        }
    },
    computed: {
        ...mapState({
            OptionsDoctor: state => state.employee.options.employee,
        }),
    },
    methods: {
        // nge proses-order
        proceedOrder() {
            this.isDisabled = true
            this.$store.dispatch('radiology/formPostPutProcess', { id: this.id })
            .then(res => {
                this.notification('Success','CheckIcon','success','You have successfully')
                this.getData(this.$route.params.id)
                this.isDisabled = false
                // this.sendNextStep(this.orderData.date_request, this.orderData.queue)
            }, 
            err => {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                this.isDisabled = false
            })
        },

        // upload image utama
        uploadImage(e, id) {
            for (let i = 0; i < e.target.files.length; i++) {
                const file = e.target.files[i]
                const reader = new FileReader()
                reader.onload = (filez) => {
                    this.result.file = filez.target.result.replace("data:"+ file.type +";base64,", '');
                    this.$store.dispatch('radiology/sendFile', { id:id, result: this.result.file, name: file.name, extension: file.type })
                    .then(data => {
                        this.getData(this.$route.params.id)
                        this.input_image = null
                    })
                }
                reader.readAsDataURL(file)
            }
        },

        // lihat image nya
        isRefcontrol(data, index) {
            this.imageview = []
            if (index == null) {
                this.imageview = data
            } else {
                this.imageview.push(data[index])
                // console.log('this.imageview', this.imageview);
            }
            this.active.refcontrol = true
        },

        // buka modal isi expertise
        onNotesModal(obj) {
            // console.log('obj: ', obj);
            this.form.id = obj.id
            this.form.doctor_lab_id = obj.doctor_lab_id
            this.form.user_lab_id = obj.user_lab_id
            this.form.conclusion = obj.conclusion
            this.form.advice = obj.advice

            this.active.notes_modal = true
        },

        // upload image buat ganti
        inputImageAtModal (e) {
            // console.log(e)
            for (let i = 0; i < e.target.files.length; i++) {
                const file = e.target.files[i]
                const reader = new FileReader()
                reader.onload = (filez) => {
                    this.form.result = filez.target.result.replace("data:"+ file.type +";base64,", '');
                    this.form.extension = file.type
                }
                reader.readAsDataURL(file)
            }
        },

        // simpan expertise
        async validationFormVerifikasi() {
            try {
                this.isDisabled = true
                await this.$store.dispatch('radiology/formPutRadiologyImage', this.form)
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.getData(this.$route.params.id)
                        this.isDisabled = false
                        
                        this.active.notes_modal = false
                        this.form.id = ''
                        this.form.result = ''
                        this.form.extension = ''
                        this.form.status = 2
                        this.form.date_process = moment(new Date()).format('YYYY-MM-DD')
                        this.form.date_result = moment(new Date()).format('YYYY-MM-DD')
                        this.form.doctor_lab_id =''
                        this.form.user_lab_id =''
                        this.form.conclusion =''
                        this.form.advice = ''

                        this.triggerToNotification()
                        // this.sendNextStep(this.orderData.date_request, this.orderData.queue)
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)                
            } finally {
                this.isDisabled = false
            }   
        },

        triggerToNotification() {
            let params = {
                health_facility_item_id: this.regData.health_facility_item_id
            }

            try {
                this.$store.dispatch('position/getPosition', params)
                .then(
                    async res => {
                        if (res?.length > 0) {
                            this.notif_socket.emit('SEND_SBAR', {
                                type: 'RAD_RESULT',
                                position_id: res[0].id,
                            })
                        }
                    }
                )
                .catch(({message, debug, response}) => {
                    this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                })
            } catch (error) {
                this.notification('Failed','CloseIcon','danger','You have unsuccessful')
                console.error('Error catch at: ', error)    
            }
        },

        // hapus image + expertise nya
        deleteImage(obj) {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus ${obj.name} ?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger', okTitle: 'Ya, Hapus', cancelTitle: 'Batal', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    this.$store.dispatch('radiology/removeImage', obj.id)
                    .then(
                        res => {
                            this.notification('Success','CheckIcon','success','You have successfully')
                            this.getData(this.$route.params.id)
                        }
                    )
                    .catch(({message, debug, response}) => {
                        this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                    })
                }
            })
        },

        getData(id) {
			this.$store.dispatch('radiology/getByIdOrder', id).then(data => {
                this.orderData = data[0]
                this.regData = data[0].regData
                // console.log('this.orderData', this.orderData);

                this.form.conclusion = this.orderData.conclusion
                this.form.notes = this.orderData.notes
                this.form.advice = this.orderData.advice

                this.getDiagnoseFromSoap()
            })
        },

        getDiagnoseFromSoap() {
            const params = {
                registration_destination_id: this.regData.registration_destination_id
            }

            if (this.regData.health_facility_id === this.hfi_outpatient) {
                params.is_booking = 0
            } else if (this.regData.health_facility_id === this.hfi_inpatient) {
                params.is_initial_inpatient = 1
            }

            try {                
                this.$store.dispatch('soap/getDetail', params).then((response) => {
                    let rows = response
                    let last_asse = rows.length > 0 ? rows[0] : null
                    this.soap_list = response || []

                    if (last_asse?.icd10) {
                        const mappedIcd10 = last_asse.icd10.map(item => {
                            return {
                                icd10_id: item.icd10_id,
                                value: item.icd10_id,
                                title: `${item.code} ${item.name}`, 
                                action: "action"
                            };
                        });                
                        this.main_diagnose = mappedIcd10;
                    } else {
                        this.main_diagnose = []
                    }

                }, error => {
                    console.error('Error while GET soap/getDetail at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET soap/getDetail at: ', error);
            }
        },

        // sendNextStep(date_request, queue) {
        //     let params = {
        //         date: moment(date_request).format('YYYY-MM-DD'),
        //         queue: queue
        //     }

        //     try {
        //         this.$store.dispatch('queue/postPutQueueNextRad', params)
        //         .then(
        //             res => {
        //                 this.socket.emit('SEND_RAD', params);
        //             } 
        //         )
        //         .catch(({error, response}) => {
        //             this.notification('Failed','CloseIcon','danger',`${error}`)
        //         })
        //     } catch (error) {
        //         this.notification('Failed','CloseIcon','danger','You have unsuccessful')
        //         console.error('Error catch at: ', error)                
        //     }
        // },

        removeRadiologyProcess(obj){
            // console.log(obj, 'removeRadiologyProcess')
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus ${obj.costing_name} ?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'primary', okTitle: 'Ya', cancelTitle: 'Tidak', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true) {
                    this.isDisabled = true
                    this.$store.dispatch('radiology/removeItemRequest', obj.id)
                    .then(
                        response => {
                            if (response.status === 200 || response.status === 201) {
                                this.notification('Success','CheckIcon','success','Berhasil Menghapus Item Penunjang')
                                this.getData(this.$route.params.id)

                            } else {
                                this.notification('Failed','CloseIcon','danger','Gagal Menghapus Item Penunjang')
                            }
                        }
                    )
                    .catch(({message, debug, response}) => {
                        this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                    }) 
                    .finally(() => {
                        this.isDisabled = false
                    }) 
                }
            })
        },

        parsedBody(text){
            return String(text || '').replace(/\n/g, "<br>");
        },

        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                employee: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },
    },

    created () {
        if (this.$route.params.id) {
            this.id = this.$route.params.id
            this.getData(this.$route.params.id)
        }
        this.$store.dispatch('employee/getParamSpecialistRad')
    }
}
</script>

<style scoped>
    table.custom-border tr td {
        border: 0px;
        padding: 4px;
    }
</style>
