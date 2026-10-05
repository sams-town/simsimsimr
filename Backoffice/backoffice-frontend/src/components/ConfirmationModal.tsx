import { Dialog, Transition } from '@headlessui/react';
import { Fragment } from 'react';
import IconX from './Icon/IconX';

interface ConfirmationModalProps {
    isOpen: boolean;
    onClose: () => void;
    onConfirm: () => void;
    title?: string;
    message?: React.ReactNode | string;
    confirmText?: string;
    cancelText?: string;
    loading?: boolean;
    variant?: 'primary' | 'danger' | 'success';
    customContent?: React.ReactNode;
    hideConfirm?: boolean;
    keepOpenOnConfirm?: boolean;
}

export default function ConfirmationModal({
    isOpen,
    onClose,
    onConfirm,
    title = 'Confirmation',
    message = 'Are you sure?',
    confirmText = 'Confirm',
    cancelText = 'Cancel',
    loading = false,
    variant = 'primary',
    customContent,
    hideConfirm = false,
    keepOpenOnConfirm = false,
}: ConfirmationModalProps) {
    return (
        <Transition appear show={isOpen} as={Fragment}>
            <Dialog as="div" open={isOpen} onClose={() => !loading && onClose()}>
                <Transition.Child
                    as={Fragment}
                    enter="ease-out duration-300"
                    enterFrom="opacity-0"
                    enterTo="opacity-100"
                    leave="ease-in duration-200"
                    leaveFrom="opacity-100"
                    leaveTo="opacity-0"
                >
                    <div className="fixed inset-0" />
                </Transition.Child>

                <div className="fixed inset-0 bg-[black]/60 z-[999] overflow-y-auto">
                    <div className="flex items-center justify-center min-h-screen px-4">
                        <Dialog.Panel className="panel border-0 p-0 rounded-lg overflow-hidden w-full max-w-lg my-8 text-black dark:text-white-dark animate__animated animate__fadeIn">
                            {/* Header */}
                            <div className="flex bg-[#fbfbfb] dark:bg-[#121c2c] items-center justify-between px-5 py-3">
                                <h5 className="font-bold text-lg">{title}</h5>
                                <button
                                    onClick={onClose}
                                    type="button"
                                    className="text-white-dark hover:text-dark disabled:opacity-30 disabled:cursor-not-allowed"
                                    disabled={loading}
                                >
                                    <IconX />
                                </button>
                            </div>

                            {/* Body */}
                            <div className="p-5">
                                {customContent ? customContent : <p>{message}</p>}
                                <div className="flex justify-end items-center mt-8">
                                    <button
                                        onClick={onClose}
                                        type="button"
                                        className="btn btn-outline-dark"
                                        disabled={loading}
                                    >
                                        {cancelText}
                                    </button>
                                    {!hideConfirm && (
                                        <button
                                            onClick={() => {
                                                if (!keepOpenOnConfirm) {
                                                    onClose();
                                                }
                                                onConfirm();
                                            }}
                                            type="button"
                                            className={`btn btn-outline-${variant} ltr:ml-4 rtl:mr-4`}
                                            disabled={loading}
                                        >
                                            {loading ? 'Loading...' : confirmText}
                                        </button>
                                    )}
                                </div>
                            </div>

                        </Dialog.Panel>
                    </div>
                </div>
            </Dialog>
        </Transition>
    );
}

