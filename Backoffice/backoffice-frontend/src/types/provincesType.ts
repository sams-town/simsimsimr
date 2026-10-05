export interface ProvincesType {
  provinceId: number;
  provinceName: string;
}

export interface ProvinceOptionType {
  label: string;
  value: string;
  desc: string;
}

export interface CityType {
  cityId: number;
  cityName: string;
  provinceId: number;
}

export interface CityOptionType {
  label: string;
  value: string;
  desc: string;
}
