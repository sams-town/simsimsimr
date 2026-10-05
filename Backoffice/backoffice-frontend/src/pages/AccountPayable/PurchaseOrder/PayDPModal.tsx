import { Fragment, useState, useEffect } from 'react';
import { Dialog, Transition } from '@headlessui/react';
import { useTranslation } from 'react-i18next';
import { useAddDownPaymentMutation } from '@/store/api/purchaseOrder/purchaseOrderApiSlice';
import { useGetOptionBankQuery } from '@/store/api/bank/bankApiSlice';
import SelectSearch from 'react-select';
import Flatpickr from 'react-flatpickr';
import 'flatpickr/dist/flatpickr.css';
import { FormatNumberRounded } from '@/utils/formatNumber';
import { FormatDate } from '@/utils/formatDate';
import { toastMessage } from '@/utils/toastUtils';
import { Button } from '@/components/ui/button';
import { X, Calendar } from 'lucide-react';

interface PayDPModalProps {
    isOpen: boolean;
    onClose: () => void;
    poData: any;
    refetch?: () => void;
}

const PayDPModal = ({ isOpen, onClose, poData, refetch }: PayDPModalProps) => {
    const { t } = useTranslation();

    const [amount, setAmount] = useState<string>('');
    const [note, setNote] = useState<string>('');
    const [date, setDate] = useState<Date>(new Date());
    const [selectedBank, setSelectedBank] = useState<any>(null);

    const [addDownPayment, { isLoading }] = useAddDownPaymentMutation();
    const { data: bankList, isLoading: isLoadingBank } = useGetOptionBankQuery(
        { search: '', page: -1, pageSize: -1 }, { skip: !isOpen }
    );

    useEffect(() => {
        if (isOpen) {
            setAmount('');
            setNote('');
            setDate(new Date());
            setSelectedBank(null);
        }
    }, [isOpen]);

    const getBankOptions = () => {
        if (!bankList) return [];
        let items: any[] = Array.isArray(bankList) ? bankList : bankList.data || [];
        return items.map((b: any) => ({
            label: b.label || b.bankName || '',
            value: b.value || b.coaCode || b.desc || '',
            desc: b.desc || b.coaCode || '',
            coaName: b.label || b.bankName || '' // Store name explicitly
        })).filter((i: any) => i.label && i.value);
    };

    const bankOptions = getBankOptions();

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();

        if (!amount || Number(amount) <= 0) {
            toastMessage(t('account_payable.purchase_order.pay_dp_modal.validation.amount_positive'), 'error');
            return;
        }

        if (!selectedBank) {
            toastMessage(t('account_payable.purchase_order.pay_dp_modal.validation.select_bank'), 'error');
            return;
        }

        try {
            await addDownPayment({
                orderNumber: poData?.orderNumber,
                amount: Number(amount),
                date: new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate())).toISOString(),
                note: note,
                coaCode: selectedBank.value,
                coaName: selectedBank.coaName
            }).unwrap();

            toastMessage(t('account_payable.purchase_order.pay_dp_modal.validation.success'), 'success');
            if (refetch) refetch();
            onClose();
        } catch (error: any) {
            toastMessage(error?.data?.message || t('account_payable.purchase_order.pay_dp_modal.validation.error'), 'error');
        }
    };

    // Parse History
    const history = poData?.downPaymentHistory ? JSON.parse(poData.downPaymentHistory) : [];

    return (
        <Transition appear show={isOpen} as={Fragment}>
            <Dialog as="div" className="relative z-50" onClose={onClose}>
                <Transition.Child
                    as={Fragment}
                    enter="ease-out duration-300"
                    enterFrom="opacity-0"
                    enterTo="opacity-100"
                    leave="ease-in duration-200"
                    leaveFrom="opacity-100"
                    leaveTo="opacity-0"
                >
                    <div className="fixed inset-0 bg-black bg-opacity-25" />
                </Transition.Child>

                <div className="fixed inset-0 overflow-y-auto">
                    <div className="flex min-h-full items-center justify-center p-4 text-center">
                        <Transition.Child
                            as={Fragment}
                            enter="ease-out duration-300"
                            enterFrom="opacity-0 scale-95"
                            enterTo="opacity-100 scale-100"
                            leave="ease-in duration-200"
                            leaveFrom="opacity-100 scale-100"
                            leaveTo="opacity-0 scale-95"
                        >
                            <Dialog.Panel className="w-full max-w-2xl transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all">
                                <div className="flex justify-between items-center mb-4 border-b pb-4">
                                    <Dialog.Title as="h3" className="text-lg font-medium leading-6 text-gray-900">
                                        {t('account_payable.purchase_order.pay_dp_modal.title')} - {poData?.orderNumber}
                                    </Dialog.Title>
                                    <button onClick={onClose} className="text-gray-400 hover:text-gray-500">
                                        <X className="w-5 h-5" />
                                    </button>
                                </div>

                                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    {/* Left: Form */}
                                    <form onSubmit={handleSubmit} className="space-y-4">
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700 mb-1">{t('account_payable.purchase_order.pay_dp_modal.date')}</label>
                                            <div className="relative">
                                                <Flatpickr
                                                    value={date}
                                                    options={{
                                                        dateFormat: 'd/m/Y',
                                                        enableTime: false,
                                                    }}
                                                    className="form-input w-full pl-9"
                                                    onChange={([d]) => setDate(d)}
                                                />
                                                <div className="absolute inset-y-0 left-3 flex items-center pointer-events-none text-gray-400">
                                                    <Calendar className="w-4 h-4" />
                                                </div>
                                            </div>
                                        </div>

                                        <div>
                                            <label className="block text-sm font-medium text-gray-700 mb-1">{t('account_payable.purchase_order.pay_dp_modal.bank')}</label>
                                            <SelectSearch
                                                className="basic-single"
                                                classNamePrefix="select"
                                                placeholder={t('account_payable.purchase_order.pay_dp_modal.select_bank')}
                                                options={bankOptions}
                                                isLoading={isLoadingBank}
                                                onChange={(opt: any) => setSelectedBank(opt)}
                                                value={selectedBank}
                                            />
                                        </div>

                                        <div>
                                            <label className="block text-sm font-medium text-gray-700 mb-1">{t('account_payable.purchase_order.pay_dp_modal.amount')}</label>
                                            <input
                                                type="number"
                                                className="form-input w-full"
                                                value={amount}
                                                onChange={(e) => setAmount(e.target.value)}
                                                placeholder="0"
                                                min="0"
                                            />
                                        </div>

                                        <div>
                                            <label className="block text-sm font-medium text-gray-700 mb-1">{t('account_payable.purchase_order.pay_dp_modal.note')}</label>
                                            <textarea
                                                className="form-textarea w-full"
                                                rows={3}
                                                value={note}
                                                onChange={(e) => setNote(e.target.value)}
                                                placeholder={t('account_payable.purchase_order.pay_dp_modal.note_placeholder')}
                                            />
                                        </div>

                                        <div className="pt-4 flex justify-end gap-2">
                                            <Button type="button" variant="outline" onClick={onClose}>
                                                {t('account_payable.purchase_order.pay_dp_modal.cancel')}
                                            </Button>
                                            <Button type="submit" disabled={isLoading}>
                                                {isLoading ? t('account_payable.purchase_order.pay_dp_modal.saving') : t('account_payable.purchase_order.pay_dp_modal.save')}
                                            </Button>
                                        </div>
                                    </form>

                                    {/* Right: Summary & History */}
                                    <div className="bg-gray-50 p-4 rounded-lg space-y-4">
                                        <div>
                                            <h4 className="font-semibold text-gray-900 mb-2">{t('account_payable.purchase_order.pay_dp_modal.info_po')}</h4>
                                            <div className="grid grid-cols-2 gap-2 text-sm">
                                                <span className="text-gray-500">{t('account_payable.purchase_order.table.supplier')}:</span>
                                                <span className="font-medium text-right">{poData?.supplierName}</span>
                                                <span className="text-gray-500">{t('account_payable.purchase_order.table.total')}:</span>
                                                <span className="font-medium text-right">{FormatNumberRounded(poData?.grandTotal || poData?.total)}</span>
                                                <span className="text-gray-500">{t('account_payable.purchase_order.table.total_dp')}:</span>
                                                <span className="font-bold text-success text-right">{FormatNumberRounded(poData?.downPayment)}</span>
                                            </div>
                                        </div>

                                        <div>
                                            <h4 className="font-semibold text-gray-900 mb-2">{t('account_payable.purchase_order.pay_dp_modal.history_dp')}</h4>
                                            {history.length > 0 ? (
                                                <div className="overflow-auto max-h-[200px]">
                                                    <table className="min-w-full text-xs">
                                                        <thead>
                                                            <tr className="border-b bg-gray-100">
                                                                <th className="py-1 text-left">{t('account_payable.purchase_order.pay_dp_modal.date')}</th>
                                                                <th className="py-1 text-right">{t('account_payable.purchase_order.pay_dp_modal.amount')}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            {history.map((item: any, idx: number) => (
                                                                <tr key={idx} className="border-b last:border-0 border-gray-100">
                                                                    <td className="py-1">{FormatDate(item.Date)}</td>
                                                                    <td className="py-1 text-right font-medium">{FormatNumberRounded(item.Amount)}</td>
                                                                </tr>
                                                            ))}
                                                        </tbody>
                                                    </table>
                                                </div>
                                            ) : (
                                                <p className="text-sm text-gray-500 italic text-center py-4">{t('account_payable.purchase_order.pay_dp_modal.no_history')}</p>
                                            )}
                                        </div>
                                    </div>
                                </div>
                            </Dialog.Panel>
                        </Transition.Child>
                    </div>
                </div>
            </Dialog>
        </Transition>
    );
};

export default PayDPModal;
