export type BpjsCostType = {
    costId: number;
    procedure: string;
    cost: number;
    branchId: number;
};

export type BpjsCostResponseType = {
    data: BpjsCostType[];
    totalData: number;
};
