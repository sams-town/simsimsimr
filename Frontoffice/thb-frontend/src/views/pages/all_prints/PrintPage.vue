<template>
    <div class="wrapper">
        <div class="no-print my-8">
            <b-button @click="handlePrint" variant="w3green" size="sm">Cetak Label</b-button>
        </div>
        <LabelPrint :rows="rows" />
    </div>
</template>

<script>
import LabelPrint from '@/@core/components/label-sticker/LabelPrint.vue'
import moment from 'moment'

export default {
    components: {
        LabelPrint
    },

    data() {
        return {
            regData: null,
            rows: [
                // {
                //     print_qr: '596ebc84-574d-4370-bb50-2198cb15657d:1',
                //     first_split_name: 'ADI WAHYU SETIAWAN DIMANA TANAH LANGIT',
                //     norm: '000123',
                //     birth_date: '01-01-2000',
                //     age: '25 th',
                //     sex: 'L'
                // },
                // {
                //     print_qr: 'QR002',
                //     first_split_name: 'BUDI',
                //     norm: '000124',
                //     birth_date: '02-02-1999',
                //     age: '26 th',
                //     sex: 'P'
                // }
            ]
        };
    },

    methods: {
        handlePrint() {
            window.print();
        },

        async getData(reg_des_id) {
            try {
                const response = await this.$store.dispatch('registration/getById', reg_des_id)
                this.regData = response[0]
                this.rows.push({
                    print_qr: `${this.regData.registration_id}:1`,
                    first_split_name: `${this.regData.alias_name} ${this.regData.name}`,
                    norm: this.regData.norm,
                    birth_date: this.parsed_date(this.regData.birth_date),
                    age: this.parsedAge(this.regData.birth_date),
                    sex: this.regData.sex_name,
                    qr_url: this.regData.bracelet_url
                })
            } catch (error) {
                console.error('Error while GET data at:', error)
            }
        },

        parsedAge(date) {
            if (!date || date === "0000-00-00") {
                return "-";
            }

            const parsed = moment(date);
            if (!parsed.isValid()) {
                return "";
            }

            const birthDate = moment(date, 'YYYY-MM-DD');
            const currentDate = moment();

            const years = currentDate.diff(birthDate, 'years');
            birthDate.add(years, 'years');

            return `${years} th`;
        },

        parsed_date(date) {
            if (!date || date === "0000-00-00") {
                return "-";
            }

            const parsed = moment(date);
            if (!parsed.isValid()) {
                return "";
            }

            return parsed.format("DD-MM-YYYY");
        },
    },

    created() {
        this.getData(this.$route.params.id)
    },
};
</script>

<style scoped>
.wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
}

@media print {
    .no-print {
        display: none !important;
    }

    .wrapper {
        align-items: start;
    }
}
</style>
