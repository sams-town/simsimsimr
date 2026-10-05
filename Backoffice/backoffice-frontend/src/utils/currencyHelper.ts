export const pembilang = (num: number): string => {
    if (num === 0) return 'Nol Rupiah';

    const units = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan'];
    const teens = ['Sepuluh', 'Sebelas', 'Dua Belas', 'Tiga Belas', 'Empat Belas', 'Lima Belas', 'Enam Belas', 'Tujuh Belas', 'Delapan Belas', 'Sembilan Belas'];
    const tens = ['', '', 'Dua Puluh', 'Tiga Puluh', 'Empat Puluh', 'Lima Puluh', 'Enam Puluh', 'Tujuh Puluh', 'Delapan Puluh', 'Sembilan Puluh'];
    const scales = ['', 'Ribu', 'Juta', 'Miliar', 'Triliun'];

    // helper untuk 3 digit
    const convertChunk = (n: number, scale: number): string => {
        let str = '';

        if (n >= 100) {
            const hundreds = Math.floor(n / 100);
            str += (hundreds === 1 ? 'Seratus' : units[hundreds] + ' Ratus') + ' ';
            n %= 100;
        }

        if (n >= 20) {
            str += tens[Math.floor(n / 10)] + ' ';
            n %= 10;
        }

        if (n >= 10) {
            str += teens[n - 10] + ' ';
            n = 0;
        }

        if (n > 0) {
            // khusus "Seribu" bukan "Satu Ribu"
            if (n === 1 && scale === 1) {
                str += 'Se';
            } else {
                str += units[n] + ' ';
            }
        }

        return str.trim() + (scale > 0 ? ' ' + scales[scale] : '');
    };

    let numStr = num.toString();
    let scale = 0;
    let result = '';

    while (numStr.length > 0) {
        const chunk = parseInt(numStr.slice(-3), 10);
        numStr = numStr.slice(0, -3);

        if (chunk > 0) {
            result = convertChunk(chunk, scale) + ' ' + result;
        }
        scale++;
    }

    return result.trim() + ' Rupiah';
};