export type DepartmentType = {
    departmentId: number;
    departmentName: string;
    branchId: number;
};

export type DepartmentResponseType = {
    data: DepartmentType[];
    totalData: number;
};
