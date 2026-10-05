export interface OptionType {
  label?: string;
  value?: string;
  desc?: string;
  level?: string;
}

export type OptionDataType = {
  optionId?: number;
  optionName?: string;
  optionValue?: string;
  optionType?: string;
  optionLabel?: string;
  optionDesc?: string;
};

export type OptionDataUpdateType = {
  optionId?: number | null;
  optionName: string;
  optionValue: string;
  optionType?: string;
  optionLabel?: string;
  optionDesc?: string;
  branchId?: number;
};
