<template>
    <div>
        <patientMenu selectedMenu="menu14" />
        <div class="label-head my-16 my-lg-24">
            <span class="px-16 text-bold-24 text-neutral-700">Riwayat MCU Pasien</span>
        </div>
        <!-- ////////  -->
        <div class="card-body shadow-sm rounded">
            <div class="row mx-0">
                <div class="col-12 col-lg">
                    <div class="row">
                        <div class="col-12 col-lg-3">
                            <img src="@/@core/assets/images/nurse/img-user.jpg" alt="your-photo"
                                class="rounded-circle shadow-sm c-img">
                        </div>
                        <div class="col-12 col-lg">
                            <span class="text-bold-18 text-indigo">
                                <img src="@/@core/assets/icons/nurse/user.svg" alt="user-icon">00003686
                            </span>
                            <p class="mb-0 mt-12 text-semi-24 text-neutral-800">
                                <span>(Nn) </span>
                                <span>Alambana Tampubolon</span>
                            </p>
                            <div class="row mx-0 mt-16">
                                <b-badge class="rounded text-neutral-500 text-uppercase border border-info-500 px-10 py-1 "
                                    variant="info-200">Pasien Baru</b-badge>
                                <b-badge
                                    class="rounded text-neutral-500 text-uppercase border border-success-500 px-10 py-1 mx-lg-12 "
                                    variant="success-200">Instalasi Rawat Jalan</b-badge>
                                <b-badge
                                    class="rounded text-neutral-500 text-uppercase border border-warning-500 px-10 py-1 "
                                    variant="warning-200">Poli Umum</b-badge>
                            </div>
                            <p class="text-neutral-800 mt-8 mt-lg-16">
                                <span class="text-regular-14 mr-2">Status:</span>
                                <span class="text-bold-14">Dalam Penanganan Medis</span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4 mt-16 mt-lg-0">
                    <div class="d-flex flex-column align-items-lg-end h-100">
                        <div class="row mx-0">
                            <b-button pill variant="outline-indigo" size="sm" class="mr-2">
                                <b-icon icon="arrow-left" /> <span>Kembali</span>
                            </b-button>
                            <administration />
                        </div>
                        <div class="mt-2 mt-lg-auto">
                            <b-button pill variant="outline-indigo" size="sm">
                                <b-icon icon="arrow-clockwise" /> <span>Refresh</span>
                            </b-button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-24">
            <b-button @click="open_modal.mcu = !open_modal.mcu" size="sm" pill variant="indigo-500">
                <feather-icon icon="PlusIcon" /> Upload Form
            </b-button>
            <b-modal v-model="open_modal.mcu" title="Tambah Rincian" hide-footer>
                <div>
                    <div>
                        <label class="c-form-label">Tanggal</label>
                        <b-form-datepicker></b-form-datepicker>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">Pilih</label>
                        <b-form-select></b-form-select>
                    </div>
                    <div class="mt-12">
                        <label class="c-form-label">File</label>
                        <b-form-file></b-form-file>
                    </div>
                    <div class="d-flex justify-content-end mt-24">
                        <b-button variant="indigo-500" class="px-3"><feather-icon icon="SaveIcon" /> Save</b-button>
                    </div>
                </div>
            </b-modal>
        </div>
    </div>
</template>
<script>
import patientMenu from '@/@core/components/tab-menu/patientMenu.vue';
import administration from '@/views/layouts/patient/doctor/administration.vue';

export default {
    name: 'DoctorStudies',
    components: {
        patientMenu, administration,
    },
    data() {
        return {
            open_modal: {
                mcu: false,
            },
        }
    },
}
</script>
<style scoped>
@import '../../../../@core/css/utility.css';

.label-head::before {
    content: '';
    border-left: 6px solid #648DE1;
}

.c-img {
    aspect-ratio: 1/1;
    object-fit: cover;
    max-width: 150px;
    max-height: 150px;
    border: 10px solid white;
}
</style>