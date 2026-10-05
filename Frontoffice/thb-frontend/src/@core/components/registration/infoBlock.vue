<template>
    <div>
        <div class="row mx-0">
            <div class="col-12 col-lg px-0 mr-lg-2 left-block rad-2 d-flex">
                <div class="col d-flex">
                    <div v-if="activeBlock == 3" class="col px-0 c-d-flex-center c-text-12 br-1">Nama Asuransi</div>
                    <div v-if="activeBlock == 3" class="col px-0 c-d-flex-center c-text-12 br-1">Cara Bayar</div>
                    <div v-if="activeBlock == 2" class="col px-0 c-d-flex-center c-text-12 br-1">Cara Bayar</div>
                    <div class="col px-0 c-d-flex-center c-text-12">Kelas Layanan</div>
                </div>
            </div>
            <!-- <div class="col px-0"> -->
            <div class="row mx-0">
                <b-button class="col c-btn-xs-ico c-btn-blue-fill">
                    <b-icon icon="check" class="c-text-12"></b-icon>
                    <span>Pilih</span>
                </b-button>
                <b-button class="col c-btn-xs-ico c-btn-red-fill">
                    <b-icon icon="trash-fill" class="c-text-12"></b-icon>
                    <span>Hapus</span>
                </b-button>
                <b-button class="col c-btn-xs-ico c-btn-green-fill">
                    <b-icon icon="pencil-square" class="c-text-12"></b-icon>
                    <span>Edit</span>
                </b-button>
            </div>
            <!-- </div> -->
        </div>
        <div class="row mx-0 d-flex align-items-center mt-2">
            <span class="ml-lg-auto mr-2 c-text-12">Tambah Cara Bayar: </span>
            <p class="mb-0 c-text-12">
                <b-badge pill variant="light" class="ml-1 rad-5 text-capitalize pointer mb-0">
                    <b-icon icon="plus" scale="1.2"></b-icon>Perusahaan
                </b-badge>
                <b-badge pill variant="light" class="ml-1 rad-5 text-capitalize pointer mb-0">
                    <b-icon icon="plus" scale="1.2"></b-icon>Asuransi
                </b-badge>
                <b-badge pill variant="light" class="ml-1 rad-5 text-capitalize pointer mb-0">
                    <b-icon icon="plus" scale="1.2"></b-icon>Pribadi
                </b-badge>
                <b-badge pill variant="light" class="ml-1 rad-5 text-capitalize pointer mb-0">
                    <b-icon icon="plus" scale="1.2"></b-icon>Jaminan
                </b-badge>
            </p>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        activeBlock: {
            type: Number,
            default: 2
        }
    },
}
</script>

<style>
@import '../../css/utility.css';

.left-block {
    background-color: #fff;
    filter: drop-shadow(1px 1px 2px rgba(164, 159, 155, 0.4));
}

.br-1 {
    border-right: 1px solid #e0e0e0;
}

.bl-1 {
    border-right: 1px solid #e0e0e0;
}

.c-text-12 {
    font-size: 10px;
}

@media (min-width: 1400px) {
    .c-text-12 {
        font-size: 12px;
    }
}

@media (max-width: 576px) {
    .left-block {
        padding: 8px 0;
    }
}
</style>