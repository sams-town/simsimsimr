export type VoucherType = 'PENERIMAAN' | 'PENGELUARAN';

export interface BankFormMakerConfig {
    voucherType: VoucherType;
    pageTitle: string;
    subtitle: string;
    docTitle: string;          // printed document heading
    basePath: string;          // list route
    breadcrumbParent: string;
    accent: string;            // screen accent color
    printColor: string;        // printed Bukti border/text color
    receivedLabel: string;     // "Diterima Oleh :" / "Dibayar Kepada :"
    numberPrefix: string;      // voucher number prefix (info only)
    createLabel: string;       // "Buat" button label

    // Form (input) labels
    formTitle: string;         // "Form Maker Pengeluaran Baru"
    formSubtitle: string;
    dateLabel: string;         // "Tanggal Pengeluaran"
    personLabel: string;       // "Nama Penerima" / "Nama Menyerahkan"
    accountLabel: string;      // "Akun Bayar (Kas/Bank)" / "Akun Tujuan (Kas/Bank)"
    amountLabel: string;       // "Jumlah Yang Dikeluarkan" / "Jumlah Yang Diserahkan"
    showTax: boolean;          // show Diskon + PPh23 (Pengeluaran only)
    showNotes: boolean;        // show "Catatan (Opsional)" input
    showRecipientAccount: boolean; // show "Nama/No Rekening Penerima" inputs (Pengeluaran only)
}

export const BANK_FORM_MAKER_CONFIG: Record<VoucherType, BankFormMakerConfig> = {
    PENERIMAAN: {
        voucherType: 'PENERIMAAN',
        pageTitle: 'Form Maker Penerimaan',
        subtitle: 'Buat Bukti Penerimaan',
        docTitle: 'BUKTI PENERIMAAN',
        basePath: '/daftar-penerimaan/form-maker',
        breadcrumbParent: 'Penerimaan',
        accent: '#0E9F6E',
        printColor: '#003399', // biru (seperti bukti petty cash lebih bayar)
        receivedLabel: 'Diterima Dari :',
        numberPrefix: 'BPN',
        createLabel: 'Buat Maker Penerimaan',
        formTitle: 'Form Maker Penerimaan Baru',
        formSubtitle: 'Isi formulir untuk membuat bukti penerimaan baru',
        dateLabel: 'Tanggal Penerimaan',
        personLabel: 'Nama Menyerahkan',
        accountLabel: 'Akun Tujuan (Kas/Bank)',
        amountLabel: 'Jumlah Yang Diserahkan',
        showTax: false,
        showNotes: true,
        showRecipientAccount: false,
    },
    PENGELUARAN: {
        voucherType: 'PENGELUARAN',
        pageTitle: 'Form Maker Pengeluaran',
        subtitle: 'Buat Bukti Pengeluaran',
        docTitle: 'BUKTI PENGELUARAN',
        basePath: '/daftar-pengeluaran/form-maker',
        breadcrumbParent: 'Pengeluaran',
        accent: '#E8861A',
        printColor: '#8B1A1A', // maroon (seperti bukti pengeluaran AP)
        receivedLabel: 'Dibayar Kepada :',
        numberPrefix: 'BPG',
        createLabel: 'Buat Maker Pengeluaran',
        formTitle: 'Form Maker Pengeluaran Baru',
        formSubtitle: 'Isi formulir untuk membuat bukti pengeluaran baru',
        dateLabel: 'Tanggal Pengeluaran',
        personLabel: 'Nama Penerima',
        accountLabel: 'Akun Bayar (Kas/Bank)',
        amountLabel: 'Jumlah Yang Dikeluarkan',
        showTax: true,
        showNotes: false,
        showRecipientAccount: true,
    },
};
