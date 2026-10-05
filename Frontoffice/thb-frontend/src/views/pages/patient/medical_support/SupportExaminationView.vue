<template>
    <div>
        <b-modal v-model="active.modal_main" size="lg" hide-footer no-close-on-backdrop>
            <template #modal-header="{ close }">
                <div class="fs-13 font-weight-bold text-uppercase">
                    PENUNJANG LAINNYA
                </div>
                <feather-icon icon="XIcon" @click="close()" size="18" class="pointer" />
            </template>
            <div>
                <div class="row">
                    <div class="col-lg-3">
                        <label class="fs-13 mb-0">Pemeriksaan <span class="text-danger">*</span></label>
                        <v-select v-model="form_examination.category_examination_id" :options="OptionsExamination" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                    </div>
                    <div class="col-lg-3">
                        <label class="fs-13 mb-0">Dokter <span class="text-danger">*</span></label>
                        <v-select v-model="form_examination.doctor_id" :options="doctor_list" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                    </div>
                    <div class="col-lg-3 mt-12 mt-lg-0">
                        <label class="fs-13 mb-0">Berkas</label>
                        <b-form-file v-model="form_examination.file" accept="image/pdf/*" size="sm"/>
                    </div>
                    <div class="col-lg-3 mt-12 mt-lg-0">
                        <label class="fs-13 mb-0">Tanggal Pemeriksaan<span class="text-danger">*</span></label>
                        <b-form-input v-model="form_examination.examination_date" type="date" size="sm" autocomplete="off" />
                    </div>
                </div>
                <div class="row mt-12">
                    <div class="col-lg-6">
                        <label class="fs-13 mb-0">Expertise</label>
                        <textarea v-model.lazy="form_examination.expertise" rows="3" class="form-control form-control-sm" placeholder="Masukkan expertise" autocomplete="off" />
                    </div>
                </div>
                <div class="d-flex mt-20">
                    <b-button @click="validationExamination" :disabled="active.disabled_main" variant="primary" size="sm" class="px-20">Tambah</b-button>
                </div>
            </div>

            <div class="mt-32">
                <b-table-simple bordered responsive style="font-size: 13px">
                  <b-thead>
                    <b-tr>
                      <b-th style="width: 100px;">Pemeriksaan</b-th>
                      <b-th>Detail</b-th>
                    </b-tr>
                  </b-thead>
                  <b-tbody>
                    <template v-for="(item, index) in examination_list">
                      <b-tr>
                        <b-td>
                          <div>{{ item.category_examination_name }}</div>
                        </b-td>
                        <b-td>
                            <div>
                                <div v-for="(file, imgIndex) in item.data.files" :key="imgIndex" class="mb-3">
                                  <div class="position-relative border p-2 rounded" style="background-color: #f8f9fa;">
                                    <!-- Action buttons: absolute top right -->
                                    <div
                                      class="d-flex flex-row align-items-center"
                                      style="position: absolute; top: 12px; right: 12px; z-index: 2;"
                                    >
                                        <b-button
                                            v-if="!editFileState[file.file_code]?.editing"
                                            @click="enableEditFile(file,item)"
                                            v-b-popover.hover.top="'Edit'"
                                            size="sm"
                                            variant="warning"
                                            class="mr-2"
                                        >
                                            <feather-icon icon="Edit2Icon" />
                                        </b-button>

                                        <div v-else>
                                            <b-button
                                                @click="cancelEditFile(file.file_code)"
                                                v-b-popover.hover.top="'Batal'"
                                                size="sm"
                                                variant="secondary"
                                                class="mr-2"
                                            >
                                                <feather-icon icon="XIcon" />
                                            </b-button>
                                            <b-button
                                                @click="saveEditFile(file, item)"
                                                v-b-popover.hover.top="'Simpan'"
                                                size="sm"
                                                variant="success"
                                                class="mr-2"
                                            >
                                                <feather-icon icon="CheckIcon" />
                                            </b-button>
                                        </div>
                                      <b-button
                                        @click="removeExamination({ file_code: file.file_code, examination_id: item.id })"
                                        v-b-popover.hover.top="'Hapus'"
                                        size="sm"
                                        variant="danger-700"
                                        class="mr-2"
                                      >
                                        <feather-icon icon="TrashIcon" />
                                      </b-button>
                                      <template v-if="isPdfFile(file.filename)">
                                        <!-- <a :href="urlAsset+file.url" download>Download</a> -->
                                         <b-button
                                            :href="getFullAssetUrl(file.url)"
                                            download
                                            size="sm"
                                            variant="primary"
                                            class="mr-2"
                                            v-b-popover.hover.top="'Download PDF'"
                                            >
                                            <feather-icon icon="DownloadIcon" />
                                        </b-button>
                                      </template>
                                    </div>
                                    <!-- Detail Column: Tanggal, Gambar, Expertise -->
                                    <div class="d-flex flex-column flex-grow-1" style="min-width:0;">
                                        <div class="pt-2 pl-2">
                                        <strong>Tanggal:</strong>
                                        <template v-if="editFileState[file.file_code]?.editing">
                                            <b-form-input
                                            v-model="editFileState[file.file_code].examination_date"
                                            type="date"
                                            size="sm"
                                            style="max-width: 160px; display: inline-block;"
                                            />
                                        </template>
                                        <template v-else>
                                            {{ formatDate(file.examination_date) }}
                                        </template>
                                    </div>
                                    <div class="pl-2"><strong>Gambar:</strong></div>
                                    <!-- <b-button
                                    variant="warning"
                                    size="sm"
                                    style=""
                                    @click.stop="openAddImageModal(item.id, file.file_code)"
                                    v-b-popover.hover.top="'Ganti Gambar/Berkas'"
                                    >
                                    <feather-icon icon="Edit2Icon" class="mr-1" />
                                    Ganti Gambar
                                    </b-button> -->
                                    <div class="my-2">
                                      <div
                                        style="width:600px; height:300px; background:#f8f9fa; border:1px solid #eee; border-radius:8px; display:flex; align-items:center; justify-content:center; overflow:hidden; margin:0 auto;"
                                      >
                                        <template v-if="!file.url">
                                            <div style="display:flex; flex-direction: column;">
                                                <div style="text-align:center; color:#888; width:100%;">
                                                    <feather-icon icon="ImageOffIcon" size="32" class="mb-2"/>
                                                    <div>Tidak ada gambar/berkas</div>
                                                </div>
                                                <div style="width:100%; text-align:center; margin-top:8px;">
                                                    <b-button   
                                                        variant="primary"
                                                        size="sm"
                                                        @click="openAddImageModal(item.id, file.file_code)"
                                                        v-b-popover.hover.top="'Tambah Gambar/Berkas'"
                                                    >
                                                        <feather-icon icon="PlusIcon" />
                                                    </b-button>
                                                </div>
                                            </div>
                                        </template>
                                        <template v-else-if="isPdfFile(file.filename)">
                                          <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; position:relative;">
                                            <!-- Action buttons: top right, only in edit mode -->
                                            <div
                                              v-if="editFileState[file.file_code]?.editing"
                                              class="image-action-overlay"
                                              style="position:absolute; top:12px; right:12px; display:flex; gap:8px; z-index:20;"
                                            >
                                              <b-button
                                                variant="light"
                                                size="sm"
                                                class="rounded-circle shadow"
                                                style="width:40px; height:40px; display:flex; align-items:center; justify-content:center; padding:0;"
                                                @click.stop="openAddImageModal(item.id, file.file_code)"
                                                v-b-popover.hover.top="'Ubah PDF'"
                                              >
                                                <feather-icon icon="Edit2Icon" size="20" style="color:#333;" />
                                              </b-button>
                                              <b-button
                                                variant="light"
                                                size="sm"
                                                class="rounded-circle shadow"
                                                style="width:40px; height:40px; display:flex; align-items:center; justify-content:center; padding:0;"
                                                @click.stop="removeImage({ file_code: file.file_code, examination_id: item.id })"
                                                v-b-popover.hover.top="'Hapus PDF'"
                                              >
                                                <feather-icon icon="TrashIcon" size="20" style="color:#d9534f;" />
                                              </b-button>
                                            </div>
                                            <img
                                              v-if="pdfThumbnails[imageVariable || imgIndex]"
                                              :src="pdfThumbnails[imageVariable || imgIndex]"
                                              style="max-width:100%; max-height:100%;"
                                              alt="PDF Preview"
                                            />
                                            <div v-else>
                                              <b-spinner small type="grow" />
                                              <span class="ml-2">Loading PDF preview...</span>
                                              <span v-if="generatePdfThumbnail(file, imgIndex)" style="display:none"></span>
                                            </div>
                                          </div>
                                        </template>
                                        <template v-else>
                                          <div
                                            class="image-hover-container"
                                            @click="expandImage(file.url)"
                                            style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; cursor:pointer; position:relative;"
                                          >
                                            <div
                                              v-if="editFileState[file.file_code]?.editing"
                                              class="image-action-overlay"
                                              style="position:absolute; top:12px; right:12px; display:flex; gap:8px; z-index:20;"
                                            >
                                              <b-button
                                                variant="light"
                                                size="sm"
                                                class="rounded-circle shadow"
                                                style="width:40px; height:40px; display:flex; align-items:center; justify-content:center; padding:0;"
                                                @click.stop="openAddImageModal(item.id, file.file_code)"
                                                v-b-popover.hover.top="'Ubah Gambar'"
                                              >
                                                <feather-icon icon="Edit2Icon" size="20" style="color:#333;" />
                                              </b-button>
                                              <b-button
                                                variant="light"
                                                size="sm"
                                                class="rounded-circle shadow"
                                                style="width:40px; height:40px; display:flex; align-items:center; justify-content:center; padding:0;"
                                                @click.stop="removeImage({ file_code: file.file_code, examination_id: item.id })"
                                                v-b-popover.hover.top="'Hapus Gambar'"
                                              >
                                                <feather-icon icon="TrashIcon" size="20" style="color:#d9534f;" />
                                              </b-button>
                                            </div>
                                            <b-img
                                              :src="getCacheBustedUrl(urlbe+file.url)" @error="onImageError"
                                              style="max-width:100%; max-height:100%; width:auto; height:auto; object-fit:contain; display:block;"
                                              rounded
                                            />
                                            <div class="image-hover-overlay"
                                              style="position:absolute; top:0; left:0; width:100%; height:100%; display:flex; align-items:center; justify-content:center; background:rgba(0,0,0,0.28); opacity:0; transition:opacity 0.2s; border: 1px solid rgba(0,0,0,0.1);">
                                              <div style="background:rgba(0,0,0,0.7); border-radius:50%; padding:12px; display:flex; align-items:center; justify-content:center;">
                                                <feather-icon icon="SearchIcon" size="36" style="color:white; filter:drop-shadow(0 2px 6px rgba(0,0,0,0.3));"/>
                                              </div>
                                            </div>
                                          </div>
                                        </template>
                                      </div>
                                    </div>
                                    <div class="pl-2 pb-2">
                                        <strong>Dokter: </strong>
                                        <template v-if="editFileState[file.file_code]?.editing">
                                            <v-select v-model="editFileState[file.file_code].doctor_id" :options="doctor_list" :reduce="item => item.value" label="title" placeholder="pilih" class="select-size-sm" />
                                        </template>
                                        <strong>Expertise: </strong>
                                        <template v-if="editFileState[file.file_code]?.editing">
                                            <textarea v-model.lazy="editFileState[file.file_code].expertise" rows="3" class="form-control form-control-sm" placeholder="Masukkan expertise" autocomplete="off" />
                                        </template>
                                        <template v-else>
                                            <span>{{ file.expertise }}</span>
                                        </template>
                                    </div>
                                    </div>
                                  </div>
                                  <hr v-if="imgIndex < item.data.files.length - 1" class="my-3" style="border-top:1px solid #ccc; width:100%; margin-left:0; margin-right:0;" />
                                </div>
                            </div>
                        </b-td>
                      </b-tr>
                    </template>
                  </b-tbody>
                </b-table-simple>
            </div>
        </b-modal>
    </div>
</template>
<script>
import * as pdfjsLib from 'pdfjs-dist/build/pdf'
import workerSrc from 'pdfjs-dist/build/pdf.worker.min.js'
pdfjsLib.GlobalWorkerOptions.workerSrc = workerSrc
import { mapState } from 'vuex'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import moment from 'moment'
import 'moment/locale/id'
// import pdf from 'vue-pdf'

// import * as pdfjsLib from 'pdfjs-dist/build/pdf'
// import workerSrc from 'pdfjs-dist/build/pdf.worker.min.js'
// pdfjsLib.GlobalWorkerOptions.workerSrc = workerSrc

export default {
    data() {
        return {
            // pdfImages: {},
            supportsPDF: false,
            urlbe:process.env.VUE_APP_IMAGE,
            urlbe_:process.env.VUE_APP_IMAGE_,
            urlAsset:process.env.VUE_APP_ASSETS_BE,
            form_examination: {
                id: '',
                file: null,
                examination_date: moment(new Date()).format('YYYY-MM-DD'),
                category_examination_id: '',
                soap_id: '',
                doctor_id: '',
                expertise: '',
            },
            doctor_list: [],
            examination_list: [],
            editFileState: {}, // { [file_code]: { editing: true, expertise: '', examination_date: '' } }
            active: {
                modal_main: false,
                disabled_main: false,
            },
            pdfThumbnails: {},
            imageVariable: '',

        }
    },

    computed: {
        ...mapState({ 
            OptionsExamination: state => state.soap.options.examination_category,
        })
    },

    methods: {
        getAllDoctorList() {
            try {                
                this.$store.dispatch('employee/getDoctorList', { is_specialist: 'all' }).then((response) => {
                    this.doctor_list = response
                }, error => {
                    console.error('Error while GET employee/getDoctorList general at: ', error);
                })
            } catch (error) {
                console.error('Error catch while GET employee/getDoctorList general at: ', error);
            }
        },
        isPdfFile(filename) {
            if (!filename || typeof filename !== 'string') return false;
            const last4Chars = filename.slice(-4).toLowerCase();
            return last4Chars === '.pdf';
        },
        formatDate(date) {
            if (!date) return '-'
            return moment(date).locale('id').format('D MMMM YYYY')
        },
        getFullAssetUrl(path) {
            if (!path) return '';
            // console.log('path: ', path);
            if (path.startsWith('/')) path = path.substring(1);
            if (path.startsWith('assets/')) path = path.substring(7);
            let base = this.urlAsset;
            if (!base.endsWith('/')) base += '/';
            return base + path;
        },
        async generatePdfThumbnail(file, imgIndex) {
            try {
                if (!file.base64) return;
        
                // Convert base64 data URI to binary
                const pdfAsArray = this.convertDataURIToBinary(file.base64);
        
                // Load PDF
                const loadingTask = pdfjsLib.getDocument(pdfAsArray);
                const pdf = await loadingTask.promise;
                const page = await pdf.getPage(1);
        
                // Prepare canvas
                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');
                const viewport = page.getViewport({ scale: 1 });
        
                canvas.width = viewport.width;
                canvas.height = viewport.height;
        
                // Render PDF page into canvas
                await page.render({ canvasContext: context, viewport }).promise;
        
                // Save as base64 image
                const thumbnail = canvas.toDataURL();
                // this.$set(this.pdfThumbnails, file.file_code || imgIndex, thumbnail);
                this.$set(this.pdfThumbnails, file.base64 || imgIndex, thumbnail);

            } catch (error) {
                console.error('Error rendering PDF:', error);
            }
        },
        convertDataURIToBinary(dataURI) {
            const base64Index = dataURI.indexOf(';base64,') + ';base64,'.length;
            const base64 = dataURI.substring(base64Index);
            const raw = atob(base64);
            const rawLength = raw.length;
            const array = new Uint8Array(new ArrayBuffer(rawLength));
            for (let i = 0; i < rawLength; i++) {
                array[i] = raw.charCodeAt(i);
            }
            return array;
        },
        onImageError() {
            this.urlbe = process.env.VUE_APP_IMAGE_
        },
        notification (a,b,c,d) {
            this.$toast({
                component: ToastificationContent,
                employee: 'top-right',
                props: { title: a, icon: b, variant: c, text: d}
            })
        },

        parentTrigger(message) {
            // console.log(message);

            this.getOptionExamination(message.examData?.health_facility_item_id)
            this.openModalExamination(message.examData?.id)
            this.getAllDoctorList()
        }, 

        openModalExamination(soap_id){
            this.active.modal_main = true
            this.form_examination.soap_id = soap_id 
            this.getExamination(this.form_examination.soap_id)
        },

        getExamination(soap_id){
            console.log('getExamination soap_id: ', soap_id);
            this.$store.dispatch('soap/getExamination', {soap_id: soap_id}).then(response => {
                response.forEach(exam => {
                    if (exam.data && Array.isArray(exam.data.files)) {
                        exam.data.files.forEach((file, imgIndex) => {
                            if (file.base64) {
                                this.imageVariable = file.base64;
                                if (this.isPdfFile(file.filename)) {
                                    this.generatePdfThumbnail(file, imgIndex);
                                }
                            }
                        });
                    }
                });
                this.examination_list = response;
                // console.log('this.examination_list: ', this.examination_list);
            });
        },

        async validationExamination(){
            if (this.form_examination.examination_date && this.form_examination.category_examination_id) {
                try {
                    this.active.disabled_main = true
                    await this.$store.dispatch('soap/postPutExamination', this.form_examination)
                    .then(
                        res => {
                            this.notification('Success','CheckIcon','success','Hasil pemeriksaan berhasil ditambah')
                            this.getExamination(this.form_examination.soap_id)
                            this.resetExaminationForm()
                        }
                    )
                    .catch(({message, debug, response}) => {
                        this.notification(`Failed ${debug}`,'CloseIcon','danger',`${message}`)
                    })
                } catch (error) {
                    this.notification('Failed','CloseIcon','danger','Hasil pemeriksaan gagal ditambah')
                    console.error('Error catch at: ', error)                
                } finally {
                    this.active.disabled_main = false
                }  
            } else {
                this.notification('Warning','CloseIcon','warning','Form wajib diisi semua')
            }
        },

        enableEditFile(file,data) {
            console.log(file)
            this.$set(this.editFileState, file.file_code, {
                editing: true,
                expertise: file.expertise || '',
                doctor_id: data.doctor_id || '',
                examination_date: file.examination_date ? file.examination_date.slice(0, 10) : '', // YYYY-MM-DD
            });
        },

        saveEditFile(file, item) {
            const state = this.editFileState[file.file_code];
            if (!state) return;
            const params = {
                file_code: file.file_code,
                expertise: state.expertise,
                doctor_id: state.doctor_id,
                examination_date: state.examination_date,
            };
            const id = item.id;
            // console.log('params: ', params);
            // console.log('id: ', id);
            this.$store.dispatch('soap/updateExaminationFile', { id: id, params: params })
                .then(() => {
                this.notification('Success', 'CheckIcon', 'success', 'Berhasil mengubah data');
                this.getExamination(this.form_examination.soap_id);
                this.$delete(this.editFileState, file.file_code);
                })
                .catch(() => {
                this.notification('Gagal', 'CloseIcon', 'danger', 'Gagal mengubah data');
                });
        },
        
        cancelEditFile(fileCode) {
            this.$delete(this.editFileState, fileCode);
        },

        openAddImageModal(examId, fileCode) {
            const item = this.examination_list.find(exam => exam.id === examId);
            if (!item) {
                this.notification('Gagal', 'CloseIcon', 'danger', 'Data pemeriksaan tidak ditemukan');
                return;
            }
            // Create a hidden file input and trigger click
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/*,application/pdf';
            input.onchange = async (event) => {
            const file = event.target.files[0];
            if (file) {
                await this.addExaminationImage(file, item, fileCode);
            }
            };
            input.click();
        },

        async addExaminationImage(file, item, fileCode) {
            // console.log('addExaminationImage file: ', file);
            const id = item.id;
            // console.log('id: ', id);
            // console.log('file: ', file);
            // console.log('fileCode: ', fileCode);
            try {
                await this.$store.dispatch('soap/updateExaminationImage', {
                    id,
                    params: {
                        file,
                        file_code: fileCode
                    }
                });
                this.notification('Success', 'CheckIcon', 'success', 'Gambar berhasil ditambahkan');
                this.getExamination(this.form_examination.soap_id);
                // this.$delete(this.pdfThumbnails, fileCode);

            } catch (error) {
                this.notification('Gagal', 'CloseIcon', 'danger', 'Gagal menambah gambar');
            }
        },

        removeExamination (obj) {
            this.$bvModal
                .msgBoxConfirm(`Apakah anda yakin ingin menghapus hasil pemeriksaan ini?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger', okTitle: 'Ya, Hapus', cancelTitle: 'Batal', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    this.$store.dispatch('soap/removeExamination', obj)
                    .then(response => {
                        if (response.status === 200 || response.status === 201) {
                            this.getExamination(this.form_examination.soap_id)
                            this.notification('Success','CheckIcon','success','Hasil pemeriksaan berhasil dihapus')
                        } else {
                            this.notification('Failed','CloseIcon','danger','Hasil pemeriksaan gagal dihapus')
                        }
                    })
                }
            })
        },

        removeImage ({ file_code, examination_id }) {
            // console.log('removeImage file_code: ', file_code);
            // console.log('removeImage examination_id: ', examination_id);
            this.$bvModal
                .msgBoxConfirm(`Apakah anda yakin ingin menghapus gambar ini?`, {
                title: 'Konfirmasi', size: 'sm', okVariant: 'danger', okTitle: 'Ya, Hapus', cancelTitle: 'Batal', cancelVariant: 'outline-secondary', hideHeaderClose: false, centered: true,
            })
            .then(value => {
                if (value === true){
                    this.$store.dispatch('soap/removeImage', { file_code, examination_id })
                    .then(response => {
                        if (response.status === 200 || response.status === 201) {
                            this.getExamination(this.form_examination.soap_id)
                            this.notification('Success','CheckIcon','success','Gambar berhasil dihapus')
                        } else {
                            this.notification('Failed','CloseIcon','danger','Gambar gagal dihapus')
                        }
                    })
                }
            })
        },

        expandImage(url){
            const param = this.urlbe + url;
            const newWindow = window.open('', '_blank');
            
            newWindow.document.write(`
                <html>
                    <head>
                        <title>Image Preview</title>
                        <style>
                            body, html {
                                margin: 0;
                                padding: 0;
                                height: 100%;
                                width: 100%;
                            }
                            img {
                                width: 100%;
                                height: auto;
                            }
                        </style>
                    </head>
                    <body>
                        <img src="${param}" alt="Image Preview">
                    </body>
                </html>
            `);
            newWindow.document.close();
        },

        resetExaminationForm(){
            this.form_examination.id = ''
            this.form_examination.file = null
            this.form_examination.examination_date = moment(new Date()).format('YYYY-MM-DD')
            this.form_examination.category_examination_id = ''
            this.form_examination.expertise = ''
            this.form_examination.doctor_id = ''
        },

        getOptionExamination(hfii) {
            this.$store.dispatch('soap/getExaminationCategory')
            // this.$store.dispatch('soap/getExaminationCategory', { health_facility_item_id: hfii })
        },

        getCacheBustedUrl(url) {
            // console.log('getCacheBustedUrl url: ', url);
            if (!url) return '';
            if (url.startsWith('data:')) return url;
            const separator = url.includes('?') ? '&' : '?';
            return url + separator + 't=' + Date.now();
        },
    },
    mounted() {
        this.supportsPDF = !!document.createElement('iframe').src;
    }
}
</script>
<style scoped>
    .image-hover-container:hover .image-hover-overlay {
        opacity: 1 !important;
    }
</style>