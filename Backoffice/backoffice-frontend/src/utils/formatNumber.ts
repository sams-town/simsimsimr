export function FormatNumber(number : any) {
    if (number == null || number == null) {
        return '0';
    }else{
        const result = number.toLocaleString('id-ID',{
            // minimumFractionDigits: 2,
            // maximumFractionDigits: 2,
        });
        return  result
    }
}

/**
 * Rounds to a whole rupiah, then formats: 4379999.64 -> "4.380.000".
 *
 * Display only -- stored and posted amounts keep their 2 decimals. Round at the point of
 * display and nowhere earlier: totals must still be summed from the exact values and only
 * then passed here, otherwise the rounding error compounds across every line.
 *
 * Consequence to be aware of: because each line and the total are rounded independently
 * from exact values, adding up a printed column by hand can differ from the printed total
 * by a rupiah or two. That is intended -- the total stays as close as possible to the books.
 *
 * Rounds half away from zero (2,5 -> 3; -2,5 -> -3), matching the accounting convention and
 * the backend's Math.Round(..., MidpointRounding.AwayFromZero) used in the Excel exports.
 * Plain Math.round would send -2,5 to -2 instead.
 */
export function FormatNumberRounded(number: any) {
    if (number == null) return '0';

    const value = typeof number === 'number' ? number : Number(number);
    if (!isFinite(value)) return '0';

    const rounded = Math.sign(value) * Math.round(Math.abs(value));

    // Guard against "-0" rendering for tiny negative values.
    return (rounded === 0 ? 0 : rounded).toLocaleString('id-ID', {
        maximumFractionDigits: 0,
    });
}
