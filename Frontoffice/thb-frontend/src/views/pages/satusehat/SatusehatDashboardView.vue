<template>
    <div>
        <Layout
            :layout_breadcrumb_items="[
                { text: 'Home', link: '/' },
                { text: 'SATUSEHAT' },
                { text: 'DASHBOARD' },
                // { text: this.form_transaction.no_order }
            ]"
            >
            <section class="">
                <TopSatusehatMenu selectedMenu="menu1" class="mt-2" />
                </section>
        </Layout>
    </div>
</template>
<script>
import TopSatusehatMenu from '@/@core/components/top-menu/TopSatusehatMenu.vue';
import moment from 'moment'
import { mapState } from 'vuex'
import vSelect from 'vue-select'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
export default {
    name: 'SatusehatView',
    components: {
        TopSatusehatMenu, vSelect, ToastificationContent
    },
    data() {
        return {
        }
    }
}
</script>