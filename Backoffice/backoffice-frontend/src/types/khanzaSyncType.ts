export type KhanzaSyncModuleCode = 'ap' | 'ar' | 'po' | 'mf';

export type KhanzaSyncLockState = 'editable' | 'locked' | 'all';

export type KhanzaSyncFieldType = 'Text' | 'Number' | 'Integer' | 'Date' | 'DateTime';

export interface KhanzaSyncFieldMeta {
  column: string;
  label: string;
  type: KhanzaSyncFieldType;
}

export interface KhanzaSyncModule {
  code: KhanzaSyncModuleCode;
  label: string;
  keyLabel: string;
  lockRule: string;
  fields: KhanzaSyncFieldMeta[];
}

export interface KhanzaSyncListItem {
  key: string;
  title: string;
  subtitle: string | null;
  info: string | null;
  amount: number | null;
  date: string | null;
  editable: boolean;
  lockReason: string | null;
  rowCount: number;
  valuesDiffer: boolean;
}

export interface KhanzaSyncCompareField {
  column: string;
  label: string;
  type: KhanzaSyncFieldType;
  appValue: string | null;
  khanzaValue: string | null;
  equal: boolean;
}

export interface KhanzaSyncCompareRecord {
  key: string;
  title: string;
  subtitle: string | null;
  editable: boolean;
  lockReason: string | null;
  foundInApp: boolean;
  foundInKhanza: boolean;
  note: string | null;
  rowCount: number;
  appValuesDiffer: boolean;
  changedCount: number;
  fields: KhanzaSyncCompareField[];
}

export interface KhanzaSyncApplyItem {
  key: string;
  fields: string[];
  /** Nilai aplikasi yang dilihat user saat membandingkan; server menolak bila sudah berubah. */
  expectedAppValues: Record<string, string | null>;
}

export interface KhanzaSyncApplyResult {
  key: string;
  success: boolean;
  message: string;
  updatedRows: number;
  appliedFields: string[];
}
