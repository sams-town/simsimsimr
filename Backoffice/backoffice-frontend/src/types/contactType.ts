export interface ContactType {
  contactId: number;
  contactName: string;
  phone: string;
  address: string;
  email: string;
  contactType: string;
  refCode: string;
  dueDate: number;
  status: string;
  provinceId: number;
  provinceDetail: any;
  cityId: number;
  cityDetail: any;
  npwp: string;
  pph21: boolean;
  doctorShare?: number;
  accountDetail: AccountDetail[];
  contactCategory: ContactCategory;
  contactCategoryId: number;
}

export interface AccountDetail {
  accountId: number;
  accountName: string;
  bankName: string;
  bankCode: string;
  bankDetail: BankDetail;
  accountNo: string;
  contactId: number;
}

export interface BankDetail {
  bankCode: string;
  bankName: string;
}

export interface ContactCategory {
  contactCategoryId: number;
  contactCategoryName: string;
  branchId: number;
}
