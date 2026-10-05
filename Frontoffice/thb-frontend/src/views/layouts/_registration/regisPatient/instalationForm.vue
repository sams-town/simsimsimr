<template>
    <div>
        <div class="col px-0 mt-3">
            <p class="c-text-12-14 fw-600">Tindakan Penunjang Laboratorium</p>
            <div class="row mx-0">
                <div class="c-form col-12 col-md-6 px-0 pr-lg-1">
                    <label for="rujukan">Dokter Pengirim *</label>
                    <b-form-select v-model="selected" :size="inputSize" :options="options" id="rujukan"
                        class="c-input"></b-form-select>
                    <small class="c-f-desc fw-400">Boleh diisi lebih dari satu</small>
                </div>
                <div class="col-12 col-md-6 px-0 pl-lg-1">
                    <div class="c-form col px-0">
                        <label for="room">Tindakan *</label>
                        <b-form-select v-model="selected" :size="inputSize" :options="options" id="room"
                            class="c-input"></b-form-select>
                    </div>
                    <b-form-radio-group class="row mx-0 px-1">
                        <b-form-radio size="sm" class="c-orange d-flex align-items-center c-text-12-14-on mr-2">Show All</b-form-radio>
                        <b-form-radio size="sm" class="c-orange d-flex align-items-center c-text-12-14-on">Show Filter</b-form-radio>
                    </b-form-radio-group>
                </div>
            </div>
        </div>
        <div class="col px-0 mt-3">
            <p class="c-text-12-14 fw-600">Tindakan Penunjang Radiologi</p>
            <div class="row mx-0">
                <div class="c-form col-12 col-md-6 px-0 pr-lg-1">
                    <label for="rujukan">Dokter Pengirim *</label>
                    <b-form-select v-model="selected" :size="inputSize" :options="options" id="rujukan"
                        class="c-input"></b-form-select>
                    <small class="c-f-desc fw-400">Boleh diisi lebih dari satu</small>
                </div>
                <div class="col-12 col-md-6 px-0 pl-lg-1">
                    <div class="c-form col px-0">
                        <label for="room">Tindakan *</label>
                        <b-form-select v-model="selected" :size="inputSize" :options="options" id="room"
                            class="c-input"></b-form-select>
                    </div>
                    <b-form-radio-group class="row mx-0 px-1">
                        <b-form-radio size="sm" class="c-orange d-flex align-items-center c-text-12-14-on mr-2">Show All</b-form-radio>
                        <b-form-radio size="sm" class="c-orange d-flex align-items-center c-text-12-14-on">Show Filter</b-form-radio>
                    </b-form-radio-group>
                </div>
            </div>
        </div>
        <div class="col px-0 mt-3">
            <p class="c-text-12-14 fw-600">Tindakan Penunjang MCU</p>
            <div class="row mx-0">
                <div class="c-form col-12 col-md-6 px-0 pr-lg-1">
                    <label for="rujukan">Dokter Pengirim *</label>
                    <b-form-select v-model="selected" :size="inputSize" :options="options" id="rujukan"
                        class="c-input"></b-form-select>
                    <small class="c-f-desc fw-400">Boleh diisi lebih dari satu</small>
                </div>
                <div class="col-12 col-md-6 px-0 pl-lg-1">
                    <div class="c-form col px-0">
                        <label for="room">Tindakan *</label>
                        <b-form-select v-model="selected" :size="inputSize" :options="options" id="room"
                            class="c-input"></b-form-select>
                    </div>
                    <b-form-radio-group class="row mx-0 px-1">
                        <b-form-radio size="sm" class="c-orange d-flex align-items-center c-text-12-14-on mr-2">Show All</b-form-radio>
                        <b-form-radio size="sm" class="c-orange d-flex align-items-center c-text-12-14-on">Show Filter</b-form-radio>
                    </b-form-radio-group>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            screenWidth: 0,
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
    computed: {
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

</style>