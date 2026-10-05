<template>
    <div class="col px-0">
        <!-- Heading-Devider -->
        <div class="card border-0">
            <div class="card-header rounded bg-indigo">
                <span class="fw-bold text-white">Data Pasien</span>
            </div>
            <!-- RADIO TAB-->
            <div class="card-body">
                <div class="col px-0">
                    <div class="d-flex">
                        <b-button variant="indigo-100" @click="selectFor('new')" ref="pasBaru" class="col mr-2 highlight">Pasien
                            Baru</b-button>
                        <b-button variant="indigo-100" @click="selectFor('baby')" ref="pasBayi" class="col ml-2">Pasien Bayi</b-button>
                    </div>
                </div>
                <!-- FORM PASIEN BARU-->
                <div>
                    <b-form>
                        <!-- BLOK 1 -->
                        <div class="col px-0 mt-3 c-form">
                            <div class="row mx-0">
                                <div class="col-lg-4 d-flex align-items-stretch px-0 px-lg-3">
                                    <div class="p-2 user-inform d-flex justify-content-start align-items-center w-100">
                                        <div class="row mx-0">
                                            <img src="../../../../@core/assets/images/pendaftaran/user.png" alt="img user"
                                                class="img-user">
                                            <div class="px-3 d-flex flex-column gap-1h">
                                                <p class="user-data">No RM:</p>
                                                <p class="user-data">Terdaftar Sejak:</p>
                                                <div class="d-inline-block pointer p-1">
                                                    <input type="file" id="upload" class="d-none">
                                                    <label class="user-data pointer" for="upload"><i><u>Add
                                                                Photo</u></i></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col px-0 mt-lg-12 mt-lg-0">
                                    <div class="row mx-0">
                                        <div class="col-12 col-lg px-0 px-lg-3 mt-1">
                                            <label>Nama Pasien *</label>
                                            <div class="c-d-flex-center flex-row">
                                                <b-form-select :size="selecOption" :disabled="patient_type != 'new'"
                                                    class="col-3 px-2 d-flex align-items-stretch mr-2">
                                                    <b-form-select-option selected value="TN">TN</b-form-select-option>
                                                    <b-form-select-option value="NY">NY</b-form-select-option>
                                                </b-form-select>
                                                <b-form-input type="text" :disabled="patient_type != 'new'"
                                                    class="col"></b-form-input>
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg px-0 px-lg-3 mt-1">
                                            <label>Nomor Identitas *</label>
                                            <b-form-input :disabled="patient_type != 'new'" type="number"></b-form-input>
                                        </div>
                                    </div>
                                    <div class="row mx-0 mt-0 mt-lg-10">
                                        <div class="col-12 col-lg px-0 px-lg-3 mt-1">
                                            <label>Tempat, Tanggal Lahir *</label>
                                            <div class="row mx-0">
                                                <b-form-input type="text" class="col-12 col-lg mr-lg-2" placeholder="Tempat" ></b-form-input>
                                                <b-form-input type="date" class="col-12 col-lg mt-8 mt-lg-0"></b-form-input>
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg px-0 px-lg-3 mt-1">
                                            <label>Jenis Kelamin *</label>
                                            <b-form-select v-model="selected" :options="options"></b-form-select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- BLOK 2 -->
                        <div class="col px-0 c-form">
                            <div class="row mx-0 mt-0 mt-lg-10">
                                <div class="col-12 col-lg px-0 px-lg-3 mt-1">
                                    <label>Agama *</label>
                                    <b-form-select v-model="selected" :options="options"></b-form-select>
                                </div>
                                <div class="col-12 col-lg px-0 px-lg-3 mt-1">
                                    <label>Perkawinan *</label>
                                    <b-form-select v-model="selected" :options="options"></b-form-select>
                                </div>
                                <div class="col-12 col-lg px-0 px-lg-3 mt-1">
                                    <label>Kewarganegaraan *</label>
                                    <b-form-select v-model="selected" :options="options"></b-form-select>
                                </div>
                            </div>
                            <div class="row mx-0 mt-0 mt-lg-10">
                                <div class="col-12 col-lg px-0 px-lg-3 mt-1">
                                    <label>No Telp/No HP *</label>
                                    <b-form-input type="number" placeholder="0123456789" :disabled="patient_type != 'new'"></b-form-input>
                                </div>
                                <div class="col-12 col-lg px-0 px-lg-3 mt-1">
                                    <label for="idNum">Email</label>
                                    <b-form-input id="idNum" type="email" placeholder="email"></b-form-input>
                                </div>
                                <div class="col-12 col-lg px-0 px-lg-3 mt-1">
                                    <label for="blood">Golongan Darah</label>
                                    <b-form-select v-model="selected" :options="options" id="blood"></b-form-select>
                                </div>
                            </div>
                        </div>
                        <!-- BLOK 3 -->
                        <div class="col px-0 c-form">
                            <div class="row mx-0 mt-0 mt-lg-10">
                                <div class="col-12 col-lg px-0 px-lg-3 mt-1">
                                    <label for="edu">Pendidikan</label>
                                    <b-form-select v-model="selected" :options="options" id="edu"></b-form-select>
                                </div>
                                <div class="col-12 col-lg px-0 px-lg-3 mt-1">
                                    <label for="work">Pekerjaan</label>
                                    <b-form-select v-model="selected" :options="options" id="work"></b-form-select>
                                </div>
                            </div>
                            <div class="row mx-0 mt-0 mt-lg-10">
                                <b-form-group class="col-12 col-lg px-0 px-lg-3 mt-1">
                                    <label for="father">Nama Ayah</label>
                                    <b-form-input id="father" type="text"></b-form-input>
                                </b-form-group>
                                <b-form-group class="col-12 col-lg px-0 px-lg-3 mt-1">
                                    <label for="mother">Nama Ibu</label>
                                    <b-form-input id="mother" type="text"></b-form-input>
                                </b-form-group>
                            </div>
                        </div>
                        <!-- BLOK 4 -->
                        <div class="col px-0 c-form">
                            <div class="row mx-0 mt-0 mt-lg-10">
                                <div class="col-12 col-lg px-0 px-lg-3 mt-1">
                                    <label>Alamat *</label>
                                    <div class="row mx-0">
                                        <b-form-input type="text" placeholder="Nama jalan" :disabled="patient_type != 'new'"
                                            class="col-12 col-lg mr-0 mr-lg-2"></b-form-input>
                                        <div class="col-12 col-lg-2 px-0">
                                            <div class="row mx-0 mt-8 mt-lg-0">
                                                <b-form-input type="number" :disabled="patient_type != 'new'" placeholder="RT" class="col mr-2"></b-form-input>
                                                <b-form-input type="number" :disabled="patient_type != 'new'" placeholder="RW" class="col"></b-form-input>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mx-0 mt-0 mt-lg-10">
                                <div class="col-12 col-lg px-0 px-lg-3 mt-1">
                                    <label>Kelurahan *</label>
                                    <b-form-select v-model="selected" :options="options" :disabled="patient_type != 'new'"></b-form-select>
                                </div>
                                <div class="col-12 col-lg px-0 px-lg-3 mt-1">
                                    <label>Kecamatan *</label>
                                    <b-form-select v-model="selected" :options="options" :disabled="patient_type != 'new'"></b-form-select>
                                </div>
                                <div class="col-12 col-lg px-0 px-lg-3 mt-1">
                                    <label>Kota/Kabupaten *</label>
                                    <b-form-select v-model="selected" :options="options" :disabled="patient_type != 'new'"></b-form-select>
                                </div>
                            </div>
                            <div class="row mx-0 mt-0 mt-lg-10">
                                <div class="col-12 col-lg px-0 px-lg-3 mt-1">
                                    <label>Provinsi *</label>
                                    <b-form-select v-model="selected" :options="options" :disabled="patient_type != 'new'"></b-form-select>
                                </div>
                                <div class="col-12 col-lg px-0 px-lg-3 mt-1">
                                    <label>Kode Pos</label>
                                    <b-form-input type="text" placeholder="Masukkan kode pos" :disabled="patient_type != 'new'"></b-form-input>
                                </div>
                            </div>
                        </div>
                        <!-- UPLOAD FILES -->
                        <div class="col px-0 mt-2">
                            <div class="col-lg-6">
                                <label class="mb-1 c-form-label">Dokumen Pasien</label>
                                <div v-if="fileSim" class="upload-box d-flex flex-row justify-content-between">
                                    <input type="file" ref="file" id="upload_sim" class="d-none">
                                    <div class="row mx-0 grouping-file d-flex align-items-center gap-5">
                                        <label for="upload_sim" class="d-inline-block pointer">Upload File</label>
                                        <p>Tipe Dokumen : SIM</p>
                                    </div>
                                    <div @click="removeFileList" class="d-flex align-items-center pointer">
                                        <img src="../../../../@core/assets/icons/close.svg" width="18px" alt="remove"
                                            class="remove">
                                    </div>
                                </div>
                                <div class="available-files">
                                    <div @click="expandSelectedFiles"
                                        class="row mx-0 dropdown-files pointer d-flex justify-content-between align-items-center">
                                        <p>Pilih Tipe Dokumen</p>
                                        <img src="../../../../@core/assets/icons/drop.svg" alt="">
                                    </div>
                                    <div class="" v-if="expandFiles">
                                        <div class="file-list" v-if="fileSim == false">
                                            <div
                                                class="row mx-0 bg-light p-2 d-flex justify-content-between align-items-center">
                                                <label class="">Surat Izin Mengemudi</label>
                                                <b-button @click="selectFileList" class="bg-purple">Pilih</b-button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </b-form>
                </div>
            </div>
        </div>
    </div>
</template>
<script>

export default {
    data() {
        return {
            patient_type: 'new',
            fileSim: true,
            expandFiles: false,
            screenWidth: 0,
            // option
            selected: null,
            options: [
                { value: null, text: '-- Pilih --' },
                { value: 'a', text: 'This is option 1' },
                { value: 'b', text: 'This is option 2' },
                { value: 'c', text: 'This is option 3' },
                { value: 'd', text: 'This is option 4' }
            ],
        }
    },
    methods: {
        selectFor(patient) {
            this.patient_type = patient;
            if (this.patient_type == 'new') {
                this.$refs.pasBaru.classList.add('highlight');
                this.$refs.pasBayi.classList.remove('highlight');
            } else {
                this.$refs.pasBayi.classList.add('highlight');
                this.$refs.pasBaru.classList.remove('highlight');
            }
        },
        removeFileList() {
            this.fileSim = !this.fileSim;
        },
        selectFileList() {
            this.fileSim = !this.fileSim
            this.expandFiles = false
        },
        expandSelectedFiles() {
            this.expandFiles = !this.expandFiles
        }
    },
    computed: {
        selecOption() {
            if (this.screenWidth < 1300) {
                return 'sm'
            } else {
                return 'md'
            }
        },
        inputSize() {
            if (this.screenWidth < 768) {
                return 'sm'
            } else if (this.screenWidth >= 768 && this.screenWidth < 1300) {
                return 'md'
            } else {
                return 'lg'
            }
        }
    },
    mounted() {
        this.screenWidth = window.innerWidth
        window.addEventListener('resize', () => {
            this.screenWidth = window.innerWidth
        })
    }
}
</script>
<style scoped>
@import '../../../../@core/css/utility.css';
@import '../../../../@core/css/_layouts/registration.css';

.user-inform {
  background: #dcf3f3;
  border: 1px dashed #680cff;
  border-radius: 10px;
}
</style>