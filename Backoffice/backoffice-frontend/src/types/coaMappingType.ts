export interface CoaMappingType {
    id?: number;
    coaSource: string;
    coaSourceName?: string;
    coaDest: string;
    coaDestName?: string;
}

export interface CoaMappingData {
    data: CoaMappingType[];
    totalData: number;
}
