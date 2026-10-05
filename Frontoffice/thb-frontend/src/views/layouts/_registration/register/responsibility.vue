<template>
    <div class="col px-0">
        <div class="col head-devider mb-4">
            <p>Data Penanggung Jawab</p>
        </div>
        <div class="col px-0">
            <div class="col px-0">
                <form action="index.html">
                    <div class="sec-row">
                        <b-form-group class="c-form col sec-form">
                            <label for="responsible">Nama Penanggung Jawab *</label>
                            <b-form-input id="responsible" type="text" class="c-input"></b-form-input>
                        </b-form-group>
                        <b-form-group class="c-form col sec-form">
                            <label for="pNumb">No Telp/HP</label>
                            <b-form-input id="pNumb" type="number" class="c-input" min="0"></b-form-input>
                        </b-form-group>
                        <div class="c-form col sec-form">
                            <label for="family">Hubungan Keluarga</label>
                            <b-form-select v-model="selected" :size="inputSize" :options="options" id="family"
                                class="c-input"></b-form-select>
                        </div>
                    </div>
                    <div class="sec-row">
                        <b-form-group class="c-form col col-lg-4 sec-form">
                            <label for="addres">Alamat</label>
                            <b-form-input id="addres" type="text" class="c-input"></b-form-input>
                        </b-form-group>
                        <b-form-group class="c-form col col-lg-4 sec-form">
                            <label for="idNum">No Identitas</label>
                            <b-form-input id="idNum" type="number" class="c-input" min="0"></b-form-input>
                        </b-form-group>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
<script>

export default {
    data() {
        return {
            // option
            selected: null,
            options: [
                { value: null, text: '-- Pilih --' },
                { value: 'a', text: 'This is option 1' },
                { value: 'b', text: 'This is option 2' },
                { value: 'c', text: 'This is option 3' },
                { value: 'd', text: 'This is option 4' }
            ],
            screenWidth: 0
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
@import '../../../../@core/css/_layouts/registration.css';

</style>