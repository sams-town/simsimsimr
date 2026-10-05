export interface JournalEntryDetail {
    journalId?: number;
    coaCode?: string;
    description?: string;
    amount?: number;
    position?: 'Debit' | 'Credit';
    isPremier?: boolean;
}

export interface GeneralJournalType {
    transactionId?: string;
    transactionDate: string;
    coaCode?: string;
    description?: string;
    transactionNo?: string;
    transactionType?: string;
    transactionRef?: string;
    contactId?: number;
    details: JournalEntryDetail[];
}

export interface GeneralJournalUpdateType extends GeneralJournalType {
    transactionId: string;
}
