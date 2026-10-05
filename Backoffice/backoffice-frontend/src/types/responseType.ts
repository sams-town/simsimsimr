export type ResponseType<T = any, E = any> = {
  [x: string]: any;
  data: T;
  message?: string;
  error?: E;
};

export type ResponseDataType<T = any> = {
  map: any;
  length: number;
  data: T;
  totalCount?: number;
  totalData?: number;
};
