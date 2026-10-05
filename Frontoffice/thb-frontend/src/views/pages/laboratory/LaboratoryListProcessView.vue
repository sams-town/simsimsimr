<template>
    <div>
        <Layout
            :layout_breadcrumb_items="[
                { text: 'Home', link: '/' },
                { text: 'Laboratorium', link: '/laboratory/list' },
                { text: `${regData.name}` }
            ]"
        >
        <section class="">
            <ModernLabel label="DATA PERMINTAAN LABORATORIUM PASIEN" />
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
                        <tr>
                            <td class="align-top">Alamat</td>
                            <td class="align-top text-center">:</td>
                            <td class="align-top">{{ regData.address }}</td>
                        </tr>
                        <tr>
                            <td class="align-top">Pendidikan</td>
                            <td class="align-top text-center">:</td>
                            <td class="align-top">{{ getlastEdu(regData.last_edu_id) }}</td>
                        </tr>
                        <tr>
                            <td class="align-top">Pekerjaan</td>
                            <td class="align-top text-center">:</td>
                            <td class="align-top">{{ getProffesion(regData.profession_id) }}</td>
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
                            <td class="align-top">Usia Pasien</td>
                            <td class="align-top text-center">:</td>
                            <td class="align-top">{{ parsed_age(regData.birth_date) }}</td>
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

            <b-table bordered responsive small :fields="laboratoryFields" :items="orderData.laboratory_request_item" head-variant="light" tbody-tr-class="text-medium-14" class="mt-32 text-center">
                <template #head(select)>
                    <b-form-checkbox v-model="selectAll" @change="toggleSelectAll"></b-form-checkbox>
                </template>

                <template #cell(select)="row">
                    <b-form-checkbox
                        :value="row.item.id"
                        v-model="selectedItems"
                    ></b-form-checkbox>
                </template>

                <template #cell(costing_name)="data">{{ data.value }}</template>
                <template #cell(var_1)="data">{{ data.value | currency }}</template>
                <template #cell(date_sampling)="row"> 
                    <template v-if="row.item.date_sampling">
                        {{ row.item.date_sampling }}
                        <b-button @click="isRefSampling(row.item)" variant="warning-600" size="sm" class="text-white mr-2">
                            <feather-icon icon="EditIcon" size="16"/>
                        </b-button>
                    </template>
                    <template v-else>
                        <b-button @click="isRefSampling(row.item)" variant="primary-600" size="sm" class="text-white mr-2">
                            <feather-icon icon="EyeIcon" size="16" class="mr-1" />
                            <span class="fs-14">Input</span>
                        </b-button>
                    </template>
                </template>

                <template #cell(status)="row">
                    <template v-if="row.value == 1"></template>
                    <template v-if="row.value == 2 || row.value == 3">
                        <router-link :to="{ path: '/laboratory/list/process/result/'+ orderData.id+'/'+row.item.id}">
                            <b-button size="sm" variant="orange-400" class="text-white mr-2">
                                <feather-icon icon="PrintIcon" /> Cetak Hasil
                            </b-button>
                        </router-link>
                    </template>
                    <div class="d-flex" style="padding: 0 -12px;">
                        <!-- <div class="col-9 pl-0">
                            <b-form-file v-model="input_pdf" accept="pdf/*" multiple @change="uploadPdf($event, row.item.id)" autocomplete="off"></b-form-file>
                        </div> -->
                        <router-link :to="{ path: '/laboratory/list/process/result/'+ orderData.id+'/'+row.item.id}">
                            <b-button size="sm" class="mr-1" variant="primary-600">Input Hasil</b-button>
                        </router-link>
                        <template v-if="row.item.url">
                            <b-button @click="isRefcontrolExternal(row.item.url)" class="mr-1" size="sm" variant="primary-600">Lihat Hasil</b-button>
                        </template>
                        <b-button @click="removeLaboratoryProcess(row.item)" 
                            size="sm" 
                            variant="danger-500" 
                            class="text-white mr-1"
                            :disabled="isDisabled"
                        >
                            Hapus Penunjang
                        </b-button>
                    </div>
                </template>

                <!-- bill_status dari mana? -->
                <!-- <template #cell(bill_status)="data">
                    <b-badge :variant="data.value === 'Sudah Melunasi Pembayaran' ? 'success-500' : 'warning-500'" class="p-1">
                        <feather-icon icon="CheckSquareIcon" /> {{ data.value }}
                    </b-badge>
                </template> -->
            </b-table>

            <div class="d-flex mt-24">                    
                <template v-if="orderData.is_lis == 1">
                    <b-button @click="isRefcontrol" variant="primary-600" class="text-white mr-2">
                        <feather-icon icon="EyeIcon" size="16" class="mr-1" />
                        <span class="fs-14">LIHAT HASIL</span>
                    </b-button>
                </template>

                <template v-if="orderData.is_lis == 0">
                    <!-- <b-button :disabled="isDisabled" @click.prevent="validationLis" variant="primary-500" class="mr-2">
                        <feather-icon icon="SendIcon" size="16" class="mr-1"/>
                        <span class="fs-14">SEND LIS</span>
                    </b-button> -->
                </template>

                <template v-if="orderData.status == 1">
                    <b-button :disabled="isDisabled" @click.prevent="proceedOrder" variant="orange-400" class="text-white mr-2">
                        <feather-icon icon="SaveIcon" size="16" class="mr-1"/>
                        <span class="fs-14">PROSES</span>
                    </b-button>
                </template>

                <template v-if="orderData.status == 3">
                    <router-link :to="{ path: '/laboratory/print/'+ id}">
                        <b-button variant="outline-secondary">
                            <feather-icon icon="PrinterIcon" size="16" class="mr-1 ml-1"/>
                            <span class="fs-14">CETAK</span>
                        </b-button>
                    </router-link>
                </template>

                <!-- <b-button @click="triggerToNotification" variant="outline-secondary">
                    <span class="fs-14">COBAIN NOTIF</span>
                </b-button> -->
            </div>
            
            <b-modal v-model="active.refsampling" scrollable size="sm" no-close-on-backdrop no-close-on-esc>
                <template #modal-header="{ close }">
                    <div class="d-flex justify-content-between w-100">
                        <div class="text-uppercase font-weight-bold fs-13 pr-16">
                            Waktu Terima Sampling 
                            <span class="text-primary">{{ regData.alias_name }}. {{ regData.name }} | {{ regData.norm }} | {{ localDate(regData.birth_date) }} ({{ parsedAge(regData.birth_date) }})</span>
                        </div>
                        <feather-icon icon="XIcon" @click="close()" class="pointer"/>
                    </div>
                </template>
                <div>
                    <div class="col-lg-6">
                        <label class="fs-13 mb-0">Waktu Terima Sampling</label>
                        <b-form-input v-model="date_sampling" type="datetime-local" size="sm" />
                    </div>
                </div>
                <template #modal-footer>
                    <b-button @click="validationSampling" :disabled="isDisabledSampling" variant="primary" size="sm" class="px-32">Update</b-button>
                    <div class="w-100 d-flex justify-content-end m-0">
                    </div>
                </template>
            </b-modal>
            
            <b-modal v-model="active.refcontrol" scrollable size="lg" hide-footer>
                <template #modal-header="{ close }">
                    <div class="d-flex justify-content-between w-100">
                        <div class="text-uppercase font-weight-bold fs-13 pr-16">
                            HASIL PEMERIKSAAN 
                            <span class="text-primary">{{ regData.alias_name }}. {{ regData.name }} | {{ regData.norm }} | {{ localDate(regData.birth_date) }} ({{ parsedAge(regData.birth_date) }})</span>
                        </div>
                        <feather-icon icon="XIcon" @click="close()" class="pointer"/>
                    </div>
                </template>
                <b-table bordered responsive small head-variant="light" :items="refer_result" thead-class="fs-14" tbody-tr-class="fs-13" class="text-center mb-0"></b-table>
            </b-modal>

            <b-modal v-model="active.refcontrolexternal" scrollable size="xl" title="Hasil Laboratorium" hide-footer>
                <div class="row">
                    <div class="col-12 mt-1">
                        <img style="width:900px" :src="thumbnail" v-if="thumbnail" />
                        <div id="pdf-canvas"></div>
                    </div>
                </div>
            </b-modal>
        </section>
        </Layout>
    </div>
</template>

<script>
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import dateMixin from "@/mixins/dateMixin";
import optionMixin from '@/mixins/optionMixin';
import io from 'socket.io-client';
import moment from 'moment'
import * as pdfjsLib from 'pdfjs-dist/build/pdf'
import workerSrc from 'pdfjs-dist/build/pdf.worker.min.js'

export default {
    mixins: [dateMixin, optionMixin],
    components: { ToastificationContent },
    data() {
        return {
            notif_socket: io(process.env.VUE_APP_SOCKET_IO_NOTIF, { path: '/socket.io', transports: ['websocket', 'polling', 'flashsocket'], withCredentials: true}),
            // socket : io(process.env.VUE_APP_SOCKET_IO, { transports: ["websocket", "polling", "flashsocket"],withCredentials: true }),
            urlbe:process.env.VUE_APP_IMAGE,
            hfi_inpatient: '40f06534-07ec-4221-ad9d-dea08196079f',
            hfi_outpatient: '3cb626f5-9148-4fb1-8663-3d7f028b01a6',
            hfi_emergency: '444b3134-5b75-4b67-af72-a558d46716d7',
            pdfAsDataUri:'',
            input_pdf: null,
            thumbnail:null,
            date_sampling:'',
            active: { refcontrol: false ,refsampling: false ,refcontrolexternal: false }, 
            laboratoryFields: [
                { key: 'select', label: '', thStyle: { width: '40px' } },
                { key: 'no', label: 'NO' },
                { key: 'date_request', label: 'WAKTU PERMINTAAN' },
                { key: 'date_sampling', label: 'WAKTU TERIMA SAMPLING' },
                // { key: 'date_result', label: 'WAKTU HASIL' },
                { key: 'costing_name', label: 'TINDAKAN LABORATORIUM', thStyle: { minWidth: '140px' } },
                { key: 'var_1', label: 'HARGA TINDAKAN', thStyle: { minWidth: '140px' } },
                { key: 'status', label: 'AKSI', thStyle: { minWidth: '140px' } },
                // { key: 'bill_status', label: 'STATUS BILLING', thStyle: { minWidth: '140px' } },
            ],
            result:{
                file: null
            },
            orderData:{},
            id:'',
            item_id:'',
            isDisabled: false,
            isDisabledSampling: false,
            regData: {},
            refer_result: [],
            main_diagnose: '',
            selectAll: false,
            selectedItems: [],
        }
    },

    watch: {
        selectedItems(newVal) {
            this.selectAll = newVal.length === this.orderData.laboratory_request_item.length;
        }
    },

    filters:{
        currency(value) {
            return new Intl.NumberFormat("en-US", { style: "currency", currency: "IDR" }).format(value)
        }
    },

    methods: {
        async validationSampling() {
            try {
                this.$store.dispatch('laboratory/sendValidationSampling', { date_sampling:this.date_sampling, id:this.item_id})
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.id = this.$route.params.id
                        this.getData(this.$route.params.id)
                        this.active.refsampling = false
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
        toggleSelectAll() {
           if (this.selectAll) {
                this.selectedItems = this.orderData.laboratory_request_item.map(item => item.id);
            } else {
                this.selectedItems = [];
            }
        },
        isRefcontrolExternal(data) {
            this.generateThumbnail(data)
            this.active.refcontrolexternal = true
        },
        convertDataURIToBinary (dataURI) {
            var BASE64_MARKER = ';base64,';
            var base64Index = dataURI.indexOf(BASE64_MARKER) + BASE64_MARKER.length;
            var base64 = dataURI.substring(base64Index);
            var raw = window.atob(base64);
            var rawLength = raw.length;
            var array = new Uint8Array(new ArrayBuffer(rawLength));

            for(var i = 0; i < rawLength; i++) {
                array[i] = raw.charCodeAt(i);
            }
            return array;
        },
        async generateThumbnail(data) {
            try {
                const response = await this.$store.dispatch('soap/pdf_to_base64_lab', data)
                this.pdfAsDataUri = response.base64
                // var pdfAsArray = this.convertDataURIToBinary(this.pdfAsDataUri);

                // const loadingTask =  pdfjsLib.getDocument(pdfAsArray)

                // const pdf = await loadingTask.promise
                // const page = await pdf.getPage(1)

                // const scale = 1
                // const viewport = page.getViewport({ scale })

                // const canvas = document.getElementById('pdf-canvas')
                // const context = canvas.getContext('2d')

                // canvas.width = viewport.width
                // canvas.height = viewport.height

                // await page.render({ canvasContext: context, viewport }).promise

                // this.thumbnail = canvas.toDataURL()
                // console.log('testste'+this.thumbnail)
                
                var pdfAsArray = this.convertDataURIToBinary(this.pdfAsDataUri);
                const loadingTask = pdfjsLib.getDocument(pdfAsArray)
                const pdf = await loadingTask.promise

                // Loop through all pages
                for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
                    const page = await pdf.getPage(pageNum)
                    const scale = 1
                    const viewport = page.getViewport({ scale })

                    // Create a new canvas for each page
                    const canvas = document.createElement('canvas')
                    canvas.id = `pdf-canvas-${pageNum}`
                    const context = canvas.getContext('2d')

                    canvas.width = viewport.width
                    canvas.height = viewport.height

                    await page.render({ canvasContext: context, viewport }).promise

                    // Append canvas to container
                    document.getElementById('pdf-canvas').appendChild(canvas)
                }
            } catch (error) {
                console.error('Error rendering PDF:', error)
            }
        },
        // upload pdf utama
        uploadPdf(e, id) {
            for (let i = 0; i < e.target.files.length; i++) {
                const file = e.target.files[i]
                const reader = new FileReader()
                reader.onload = (filez) => {
                    this.result.file = filez.target.result.replace("data:"+ file.type +";base64,", '');
                    this.$store.dispatch('laboratory/sendFile', { id:id, result: this.result.file, name: file.name, extension: file.type })
                    .then(data => {
                        this.getData(this.$route.params.id)
                        this.input_pdf = null
                    })
                }
                reader.readAsDataURL(file)
            }
        },

        getProffesion(code) {
            const proffesions = {
                '1': 'Pegawai Negeri',
                '2': 'Pegawai Swasta',
                '3': 'Pekerja Lepas',
                '4': 'Pelajar',
                '5': 'Mahasiswa',
                '6': 'Ibu Rumah Tangga',
                '7': 'TNI / POLRI',
                '8': 'Pensiunan',
                '9': 'Tidak Bekerja',
                '10': 'Lain Lain',
                '11': 'BUMN',
                '12': 'Buruh',
            };
            return proffesions[code] || '-';
        },
        getlastEdu(code) {
            const proffesions = {
                '1': 'Sekolah Dasar',
                '2': 'Sekolah Menengah Pertama',
                '3': 'Sekolah Menengah Atas',
                '4': 'Diploma 1',
                '5': 'Diploma 3',
                '6': 'Sarjana',
                '7': 'Magister',
                '8': 'Doktor',
                '9': 'Spesialist',
                '10': 'Sastra 4',
            };
            return proffesions[code] || '-';
        },
        parsed_age(input_date) {
            const birthDate = moment(input_date, 'YYYY-MM-DD');
            const currentDate = moment();

            const years = currentDate.diff(birthDate, 'years');
            birthDate.add(years, 'years');
            const months = currentDate.diff(birthDate, 'months');
            birthDate.add(months, 'months');
            const days = currentDate.diff(birthDate, 'days');

            return `${years} th ${months} bln ${days} hr`;
        },
        isRefcontrol() {
            if (this.refer_result?.length == 0 && this.orderData.status == 2) {
                this.triggerToNotification()
                // kl ke trigger tp ga ngaruh apapun, berarti udah pernah di trigger sebelumnya
            }
            
            // dari BE status 2 ini bakalan langsung keubah jadi 3 kl hit endpoint ini
            this.$store.dispatch('laboratory/getResultById', this.orderData.id)
                .then(response => {
                    if (response?.length > 0) {
                        this.refer_result = response[0]?.result_detail || []
                        // console.log('this.refer_result: ', this.refer_result);                        
                    }
                }
            )
            this.active.refcontrol = true
        },

        isRefSampling(obj) {
            this.item_id = obj.id
            this.date_sampling = obj.date_sampling
            this.active.refsampling = true
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
                                type: 'LAB_RESULT',
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
        
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                employee: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },
        
        getData(id) {
			this.$store.dispatch('laboratory/getByIdOrder', id).then(data => {
                this.orderData = data[0]
                this.regData = data[0].regData
                // console.log('this.orderData: ', this.orderData);

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

        async validationLis() {
            try {
                this.isDisabled = true
                await this.$store.dispatch('laboratory/formPostPutProcessLis', { id: this.id })
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        this.getData(this.$route.params.id)
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

        async proceedOrder() {
            try {
                this.isDisabled = true
                await this.$store.dispatch('laboratory/formPostPutProcess', { id: this.id })
                .then(
                    res => {
                        this.notification('Success','CheckIcon','success','You have successfully')
                        // this.sendNextStep(this.orderData.date_request, this.orderData.queue)
                        this.getData(this.$route.params.id)
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
        
        // sendNextStep(date_request, queue) {
        //     let params = {
        //         date: moment(date_request).format('YYYY-MM-DD'),
        //         queue: queue
        //     }

        //     try {
        //         this.$store.dispatch('queue/postPutQueueNextLab', params)
        //         .then(
        //             res => {
        //                 this.socket.emit('SEND_LAB', params);
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
        removeLaboratoryProcess(obj){
            // console.log(obj, 'removeLaboratoryProcess')
            this.$bvModal
                .msgBoxConfirm(`Apakah anda ingin menghapus ${obj.costing_name} ?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'primary', okTitle: 'Ya', cancelTitle: 'Tidak', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true) {
                    this.isDisabled = true
                    this.$store.dispatch('laboratory/removeItemRequest', obj.id)
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
    },

    created() {
        if (this.$route.params.id) {
            this.id = this.$route.params.id
            this.getData(this.$route.params.id)
        }
    }
}
</script>
