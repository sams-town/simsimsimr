<template>
    <div>
        <div class="text-semi-18 card-header rounded bg-neutral-200 border-bottom-0" style="margin-left:-16px; margin-right:-16px;">Pengkajian Awal SOAP</div>
        <div class="mt-20">
            <label class="c-form-label">Subjective (S)</label>
            <b-textarea v-model="form.s" rows="5"></b-textarea>
        </div>
        <div class="mt-10">
            <label class="c-form-label">Objective (O)</label>
            <b-textarea v-model="form.o" rows="5"></b-textarea>
        </div>
        <div class="mt-10">
            <label class="c-form-label">Assessment (A)</label>
            <b-textarea v-model="form.a" rows="5"></b-textarea>
        </div>
        <div class="mt-10">
            <label class="c-form-label">Plan (P)</label>
            <b-textarea v-model="form.p" rows="5"></b-textarea>
        </div>
    </div>
</template>
<script>
import { mapState } from 'vuex'
import vSelect from 'vue-select'
import ModernLabel from '@/@core/components/label/ModernLabel.vue';

export default {
    name: 'nurse_SOAP',
    components: { vSelect, ModernLabel },
    data() {
        return {
            // # TYPE_NURSE_ID X = SOAP
            form: {
                nurse_id: '',
                s: '',
                o: '',
                a: '',
                p: '',
            },
            props: {
                trigger_clear_subnurse: {},
            },
        }
    },
    watch: {
        trigger_clear_subnurse(value){
            console.log('run by soap at: ', value)
            this.resetForm()
        },
    },
    methods: {
        resetForm(){
            this.form.s = ''
            this.form.o = ''
            this.form.a = ''
            this.form.p = ''
        },

    }
}
</script>
<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
</style>