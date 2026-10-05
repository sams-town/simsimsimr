export type costCenterType = {
    costcenterId: number;
    costCenterCode: string;
    costCenterName: string;
    departmentId: number;
    branchId: number;
};

export type costCenterResponseType = {
    data: costCenterType[];
    totalData: number;
};
