<template>
    <div class="d-flex flex-column align-items-center">
        <b-table bordered responsive hover small head-variant="light" :fields="PromoDetailTable.fields"
            :items="PromoDetailTable.items" thead-class="text-bold-14" tbody-tr-class="text-regular-12"
            class="mt-12 text-center">
        </b-table>
        <b-button variant="success-500">
            <feather-icon icon="PrinterIcon" /> Print
        </b-button>
    </div>
</template>
<script>
export default {
    name: 'RatesPromoView',
    data() {
        return {
            PromoDetailTable : {
                fields: [
                    { key: 'voucher_code', label: 'Kode Voucher' },
                    { key: 'promo_name', label: 'Nama Promo' },
                    { key: 'register_code', label: 'Kode Pendaftaran' },
                    { key: 'user', label: 'User Use' },
                    { key: 'date_use', label: 'Date Use' },
                    { key: 'user_added', label: 'User Added' },
                    { key: 'status', label: 'Status' },
                ],
                items: [
                    {
                        voucher_code: 'VCH-20210520-00070',
                        promo_name: 'VOUCHER GRATIS',
                        register_code: '',
                        user: '',
                        date_use: '2021-06-13 17:04:34',
                        user_added: '',
                        status: 'Not Use',
                    },
                    {
                        voucher_code: 'VCH-20210520-00070',
                        promo_name: 'VOUCHER GRATIS',
                        register_code: '',
                        user: '',
                        date_use: '2021-06-13 17:04:34',
                        user_added: '',
                        status: 'Not Use',
                    },
                    {
                        voucher_code: 'VCH-20210520-00070',
                        promo_name: 'VOUCHER GRATIS',
                        register_code: '',
                        user: '',
                        date_use: '2021-06-13 17:04:34',
                        user_added: '',
                        status: 'Not Use',
                    },
                ]
            }
        }
    },

}
</script>