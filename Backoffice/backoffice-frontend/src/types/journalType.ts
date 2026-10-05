export interface journalType {
  balance?: number | null;
  journalId?: number; // Menambahkan journalId
  journalDesc?: string; // Menambahkan journalDesc
  transactionId?: string;
  journalRef?: string | null; // Menambahkan journalRef
  coaCode?: string;
  coaName?: string;
  coaParentCode?: string;
  coaParentName?: string;
  debitAmount?: number; // Menambahkan debitAmount
  creditAmount?: number; // Menambahkan creditAmount
  source?: string; // Menambahkan source
  branchId?: number;
  createdBy?: number;
  createdDate?: string;
  vourcherId?: string | null; // Menambahkan vourcherId
  status?: string;
  isPremier?: boolean; // Menambahkan isPremier
  updatedBy?: number | null;
  updatedDate?: string | null;
  reconciledDate?: string | null; // Menambahkan reconciledDate
  reconciledBy?: any | null; // Menambahkan reconciledBy
  transactionDetail?: {
    transactionNo: string;
    transactionType: string;
    transactionDate?: string;
    journalNo?: string;
    contactDetail: any | null; // Sesuaikan tipe data detail kontak sesuai kebutuhan
  } | null; // Menambahkan transactionDetail
  transaction?: {
    transactionNo: string;
    transactionId: string;
    journalNo: string;
    contactId: number;
    status: string;
  }
}

export interface JournalLedgerType {
  coaId: number;
  coaCode: string;
  coaName: string;
  openingBalance: number;
  closingBalance: number;
  totalTransactions: number;
  transactions: TransactionsType[];
}

export interface TransactionsType {
  date: string;
  transactionNo: string;
  journalNo?: string;
  journalRef?: string;
  transactionId: string;
  description: string;
  debit: number;
  credit: number;
  runningBalance: number;
}
