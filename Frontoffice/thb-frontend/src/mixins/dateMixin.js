// src/mixins/dateMixin.js
import moment from "moment";

export default {
    methods: {
        getCurrentFullDate() {
            return moment().format('YYYY-MM-DD HH:mm:ss');
        },

        differenceDate(now_date, past_date) {
            if (!now_date || now_date === "0000-00-00") {
                return '-'
            }
            const parsed_1 = moment(now_date);
            if (!parsed_1.isValid()) {
                return '-'
            }
            let date1 = moment(now_date, 'YYYY-MM-DD HH:mm:ss')

            if (!past_date || past_date === "0000-00-00") {
                return '-'
            }
            const parsed_2 = moment(past_date);
            if (!parsed_2.isValid()) {
                return '-'
            }
            let date2 = moment(past_date, 'YYYY-MM-DD HH:mm:ss')
            
            let diffInDays = date1.diff(date2, 'days')
            date2.add(diffInDays, 'days');
            var diffInHours = date1.diff(date2, 'hours');
            date2.add(diffInHours, 'hours');
            var diffInMinutes = date1.diff(date2, 'minutes');
            return `${diffInDays} hari ${diffInHours} jam ${diffInMinutes} mnt`
        },

        timeFromNow(input_date) {
            if (!input_date || input_date === "0000-00-00") {
                return false
            }

            const parsed = moment(input_date);
            if (!parsed.isValid()) {
                return false
            }

            return moment(input_date).locale("id").fromNow()
        },

        pewsCriteria(input_date) {
            if (!input_date || input_date === "0000-00-00") {
                return false
            }

            const parsed = moment(input_date);
            if (!parsed.isValid()) {
                return false
            }

            const birthDate = moment(input_date, 'YYYY-MM-DD');
            const now = moment();
            const years = now.diff(birthDate, 'years');

            return years <= 18
        },

        localDate(input_date) {
            if (!input_date || input_date === "0000-00-00") {
                return "-";
            }

            const parsed = moment(input_date);
            if (!parsed.isValid()) {
                return "-";
            }

            return moment(input_date).locale("id").format("DD MMM YYYY");
        },

        localFullDate(input_date) {
            if (!input_date || input_date === "0000-00-00") {
                return "-";
            }

            const parsed = moment(input_date);
            if (!parsed.isValid()) {
                return "-";
            }

            return moment(input_date).locale("id").format("DD MMMM YYYY");
        },

        localDateTime(input_date) {
            if (!input_date || input_date === "0000-00-00") {
                return "-";
            }

            const parsed = moment(input_date);
            if (!parsed.isValid()) {
                return "-";
            }

            return moment(input_date).locale("id").format("DD MMM YYYY HH:mm:ss");
        },

        localFullDay(input_date) {
            if (!input_date || input_date === "0000-00-00") {
                return "-";
            }

            const parsed = moment(input_date);
            if (!parsed.isValid()) {
                return "-";
            }

            return moment(input_date).locale("id").format("dddd, Do MMMM YYYY HH:mm:ss");
        },

        dateTimeFullNumber(input_date) {
            if (!input_date || input_date === "0000-00-00") {
                return "-";
            }

            const parsed = moment(input_date);
            if (!parsed.isValid()) {
                return "-";
            }

            return moment(input_date).format("DD-MM-YYYY HH:mm:ss");
        },

        onlyHourMinute(input_time) {
            const dummy = moment(new Date()).format('YYYY-MM-DD')
            const temp = `${dummy} ${input_time}`
            if (!input_time || input_time === "00:00:00") {
                return "-";
            }

            const parsed = moment(temp);
            if (!parsed.isValid()) {
                return "-";
            }

            return moment(temp).format('HH:mm');
        },
        
        parsedAge(input_date) {
            if (!input_date || input_date === "0000-00-00") {
                return "-";
            }
    
            const parsed = moment(input_date);
            if (!parsed.isValid()) {
                return "-";
            }
    
            const birthDate = moment(input_date, 'YYYY-MM-DD');
            const currentDate = moment();
    
            const years = currentDate.diff(birthDate, 'years');
            birthDate.add(years, 'years');
            const months = currentDate.diff(birthDate, 'months');
            birthDate.add(months, 'months');
            const days = currentDate.diff(birthDate, 'days');
    
            return `${years} th ${months} bln ${days} hr`;
        },
    },

};
