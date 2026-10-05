// DateRangePicker.tsx
import React, { useState } from 'react';
import 'flatpickr/dist/flatpickr.css';
import Flatpickr from 'react-flatpickr';
import { Indonesian } from 'flatpickr/dist/l10n/id.js';
import { format, isValid, startOfMonth } from 'date-fns';

interface DateRangePickerProps {
    onFilter: ({ startDate, endDate }: { startDate: Date | null; endDate: Date | null }) => void;
}

const DateRangePicker: React.FC<DateRangePickerProps> = ({ onFilter }) => {
    const today = new Date();
    const startOfCurrentMonth = startOfMonth(today);
    const [dates, setDates] = useState<[Date, Date]>([startOfCurrentMonth, today]);
    const [isOpen, setIsOpen] = useState(false);

    const handleFilter = (reset: boolean = false) => {
        if (reset) {
            setDates([startOfCurrentMonth, today]);
            onFilter({ startDate: startOfCurrentMonth, endDate: today });
        } else {
            const [start, end] = dates;
            onFilter({ startDate: start, endDate: end });
        }
        setIsOpen(false);
    };

    const formattedDates = isValid(dates[0]) && isValid(dates[1])
        ? `${format(dates[0], 'dd MMM yyyy')} - ${format(dates[1], 'dd MMM yyyy')}`
        : 'Invalid date range';

    return (
        <div className="relative">
            <div className="flex items-center border rounded-lg px-4 py-2 cursor-pointer" onClick={() => setIsOpen(!isOpen)}>
                <svg className="w-4 h-4 text-gray-500 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                </svg>
                <span>{formattedDates}</span>
            </div>
            {isOpen && (
                <div className="absolute top-full left-0 mt-2 w-full bg-white border rounded-lg shadow-lg z-10">
                    <Flatpickr
                        value={dates}
                        onChange={(selectedDates: Date[]) => {
                            if (selectedDates.length === 2 && isValid(selectedDates[0]) && isValid(selectedDates[1])) {
                                setDates(selectedDates as [Date, Date]);
                            }
                        }}
                        options={{
                            mode: "range",
                            dateFormat: "d M Y",
                            locale: Indonesian,
                            inline: true,
                            showMonths: 2
                        }}
                        className="w-full"
                    />
                    <div className="flex justify-between items-center mt-4 p-4">
                        <button className="text-red-500" onClick={() => handleFilter(true)}>Batal</button>
                        <button className="bg-green-500 text-white px-4 py-2 rounded-lg" onClick={() => handleFilter(false)}>Proses</button>
                    </div>
                </div>
            )}
        </div>
    );
};

export default DateRangePicker;
