<template>
    <div>
        <div class="mt-2">
            <b-form>
                <div v-if="activePhase == 1" class="c-form">
                    <div class="col px-0 mt-1 mt-lg-10">
                        <label>Pilih Perusahaan *</label>
                        <b-form-select v-model="selected" :options="options"></b-form-select>
                    </div>
                    <div class="col px-0 mt-1 mt-lg-10">
                        <label>Pilih Cara Bayar *</label>
                        <b-form-select v-model="selected" :options="options"></b-form-select>
                    </div>
                    <div class="row no-gutters mt-10 mt-lg-20">
                        <b-button pill size="sm" variant="outline-indigo" class="px-3 ml-auto">Batal</b-button>
                        <b-button pill size="sm" variant="pink-500" @click="goToStep(2)" class="text-white px-3 ml-2">Simpan</b-button>
                    </div>
                </div>
                <div v-if="activePhase == 2">
                    <infoBlock class="mt-3" />
                </div>
            </b-form>
        </div>
    </div>

</template>

<script>
import infoBlock from '@core/components/registration/infoBlock.vue';

export default {
    components: {infoBlock },
    data() {
        return {
            activePhase: 1,
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
        goToStep(step) {
            this.activePhase = step;
        }
    }
}
</script>