import { useEffect, useState } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '../../../store/themeConfigSlice';
import { DataTable } from 'mantine-datatable';
import Flatpickr from 'react-flatpickr';
import 'flatpickr/dist/flatpickr.css';
import SelectSearch from 'react-select';
import { useTranslation } from 'react-i18next';
import {
  useGetInvoiceExchangeSupplierOptionQuery,
  useGetInvoiceExchangeTransactionQuery,
  useLazyGetInvoiceExchangeTransactionDetailQuery,
  useUpdateInvoiceExchangeStatusMutation,
  useExportInvoiceExchangeMutation,
} from '@/store/api/invoiceExchange/invoiceExchangeApiSlice';
import { FormatNumberRounded } from '@/utils/formatNumber';
import { FormatDate } from '@/utils/formatDate';
import AnimateHeight from 'react-animate-height';
import { toastMessage } from '@/utils/toastUtils';
import ConfirmationModal from '@/components/ConfirmationModal';
import { Card, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import TimelineButton from '@/components/ActivityTimeline/TimelineButton';
import { AUDIT_MODULE } from '@/components/ActivityTimeline/modules';
import IconCalendar from '@/components/Icon/IconCalendar';
import {
  ChevronDown,
  Download,
  Loader2,
  FileText,
  CheckCircle2,
  Clock,
  Printer,
  Search
} from 'lucide-react';
import { createPortal } from 'react-dom';
import { ResiTukarFaktur, ResiTukarFakturData } from './ResiTukarFaktur';

interface InvoiceExchangeData {
  invoiceExchangeId?: string | number;
  id?: string | number;
  noPo: string;
  tanggalPenerimaan: string;
  tanggalPo: string;
  noFaktur: string;
  namaSupplier: string;
  jumlahTransaksi: number;
  namaPerusahaan: string;
  dueDate: string;
  exchangeDate: string;
  batchNumber: string;
  // Status pembayaran faktur. manualPaid = true berarti lunas lewat "Mark as Paid"
  // di halaman Purchase Order (tanpa transaksi bank & tanpa jurnal).
  paymentStatus: string;
  manualPaid: boolean;
  manualPaidNote: string;
}

interface SupplierGroupData {
  invoiceExchangeId: number | string;
  supplierName: string;
  totalRow: number;
  totalTransaction: number;
  status?: string;
}

const InvoiceExchange = () => {
  const dispatch = useDispatch();
  const isRtl = useSelector((state: any) => state.themeConfig.rtlClass) === 'rtl';
  const { t } = useTranslation();

  const [exportInvoiceExchange, { isLoading: isExporting }] = useExportInvoiceExchangeMutation();

  useEffect(() => {
    dispatch(setPageTitle(t('account_payable.tukar_faktur.title')));
    dispatch(setTitle(t('account_payable.tukar_faktur.title')));
    dispatch(setBreadcrumbTitle([t('menu.dashboard'), t('menu.account_payable'), t('account_payable.tukar_faktur.title')]));
  }, [dispatch, t]);

  // Filter states
  const [selectedCompany, setSelectedCompany] = useState<any>(null);
  const [searchCompany, setSearchCompany] = useState<string>('');
  const [startDate, setStartDate] = useState<Date | null>(null);
  const [endDate, setEndDate] = useState<Date | null>(null);
  const [status, setStatus] = useState<string>('not sent');
  const [supplierType, setSupplierType] = useState<string>('');


  const [showUpdateModal, setShowUpdateModal] = useState(false);
  const [pendingUpdateData, setPendingUpdateData] = useState<InvoiceExchangeData[]>([]);

  // Table states - per accordion page tracking
  const PAGE_SIZES: number[] = [10, 20, 30, 50];
  const [pageSize, setPageSize] = useState<number>(PAGE_SIZES[0]);
  const [detailPageMap, setDetailPageMap] = useState<Record<string, number>>({});

  // Helper functions for per-accordion pagination
  const getDetailPage = (key: string) => detailPageMap[key] || 1;
  const setDetailPage = (key: string, newPage: number) => {
    setDetailPageMap(prev => ({ ...prev, [key]: newPage }));
    // Clear cached data for this accordion so it refetches
    setDetailDataMap(prev => {
      const next = { ...prev };
      delete next[key];
      return next;
    });
  };

  // Accordion states
  const [accordionPage, setAccordionPage] = useState<number>(1);
  const ACCORDION_PAGE_SIZES: number[] = [5, 10, 15, 20];
  const [accordionPageSize, setAccordionPageSize] = useState<number>(ACCORDION_PAGE_SIZES[0]);
  const [expandedAccordions, setExpandedAccordions] = useState<Set<string>>(new Set());

  const [detailDataMap, setDetailDataMap] = useState<Record<string, InvoiceExchangeData[]>>({});
  const [detailTotalData, setDetailTotalData] = useState<Record<string, number>>({});
  const [detailLoadingMap, setDetailLoadingMap] = useState<Record<string, boolean>>({});
  const [selectedRows, setSelectedRows] = useState<Set<string>>(new Set());
  const [selectedRowDataMap, setSelectedRowDataMap] = useState<Record<string, InvoiceExchangeData>>({});
  const [selectedDueDateMap, setSelectedDueDateMap] = useState<Record<string, Date | null>>({});
  const [selectedDaysMap, setSelectedDaysMap] = useState<Record<string, string>>({});
  const [selectedExchangeDateMap, setSelectedExchangeDateMap] = useState<Record<string, Date | null>>({});
  const [pendingDueDate, setPendingDueDate] = useState<Date | null>(null);
  const [pendingExchangeDate, setPendingExchangeDate] = useState<Date | null>(null);
  const [detailKeywordMap, setDetailKeywordMap] = useState<Record<string, string>>({});
  const [debouncedKeywordMap, setDebouncedKeywordMap] = useState<Record<string, string>>({});

  useEffect(() => {
    const timer = setTimeout(() => setDebouncedKeywordMap(detailKeywordMap), 500);
    return () => clearTimeout(timer);
  }, [detailKeywordMap]);

  // Print state
  const [printDataList, setPrintDataList] = useState<ResiTukarFakturData[]>([]);

  // Clean up print portal after printing
  useEffect(() => {
    const handleAfterPrint = () => setPrintDataList([]);
    window.addEventListener('afterprint', handleAfterPrint);
    return () => window.removeEventListener('afterprint', handleAfterPrint);
  }, []);

  const handlePrintSelectedResi = (supplierName: string, groupKey: string) => {
    const detailData = detailDataMap[groupKey] || [];
    // Get selected items from persistent data map (survives pagination)
    const selectedItems = Object.entries(selectedRowDataMap)
      .filter(([id]) => selectedRows.has(id))
      .map(([, data]) => data)
      .filter(item => item.namaSupplier === supplierName);
    // If nothing checked, print all items on current page
    const itemsToPrint = selectedItems.length > 0 ? selectedItems : detailData;

    if (itemsToPrint.length === 0) return;

    const uniqueBatches = [...new Set(itemsToPrint.map(item => item.batchNumber).filter(Boolean))];

    const resiData: ResiTukarFakturData = {
      supplierName: supplierName,
      batchNumber: uniqueBatches.join(', '),
      exchangeDate: new Date().toISOString(),
      items: itemsToPrint.map((item) => ({
        invoiceExchangeId: Number(item.invoiceExchangeId || item.id || 0),
        poNumber: item.noPo || '',
        invoiceNumber: item.noFaktur || '',
        receiptDate: item.tanggalPenerimaan || '',
        dueDate: item.dueDate || '',
        totalTransaction: item.jumlahTransaksi || 0,
      })),
      totalAmount: itemsToPrint.reduce((sum, item) => sum + (item.jumlahTransaksi || 0), 0),
      printedDate: new Date().toISOString(),
      printedBy: '',
    };

    setPrintDataList([resiData]);
    setTimeout(() => {
      window.print();
    }, 500);
  };

  // API calls
  const { data: supplierList, isLoading: isLoadingSupplier } = useGetInvoiceExchangeSupplierOptionQuery(
    { keyword: searchCompany || '', page: 1, pageSize: 20 }, { skip: false }
  );

  const getSupplierOptions = () => {
    if (!supplierList) return [];
    let items: any[] = Array.isArray(supplierList) ? supplierList :
      supplierList.data?.data || supplierList.data || [];
    return items.map((s: any) => ({
      label: s.label || s.supplierName || s.name || '',
      value: s.value || s.supplierId || s.id || '',
    })).filter((i: any) => i.label && i.value);
  };

  const companyOptions = [{ label: t('account_payable.tukar_faktur.all_suppliers'), value: '' }, ...getSupplierOptions()];

  const formatDateForAPI = (date: Date | null): string | null => {
    if (!date) return null;
    return new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate(), 0, 0, 0)).toISOString();
  };

  const supplierName = selectedCompany?.value ? selectedCompany.label : null;
  const formattedStartDate = formatDateForAPI(startDate);
  const formattedEndDate = formatDateForAPI(endDate);

  const { data: transactionList, refetch: refetchTransaction, isLoading: isLoadingTransaction, error: transactionError } =
    useGetInvoiceExchangeTransactionQuery({
      ...(supplierName && { supplierName }),
      ...(supplierType && { supplierType }),
      ...(formattedStartDate && { startDate: formattedStartDate }),
      ...(formattedEndDate && { endDate: formattedEndDate }),
      ...(status && status !== '' && { status: status }),
      page: accordionPage, pageSize: accordionPageSize,
    });

  const [updateInvoiceExchangeStatus, { isLoading: isUpdatingStatus }] = useUpdateInvoiceExchangeStatusMutation();
  const [fetchTransactionDetail] = useLazyGetInvoiceExchangeTransactionDetailQuery();

  // Extract supplier groups
  const supplierGroups: SupplierGroupData[] = (() => {
    if (!transactionList) return [];
    let items = transactionList.data?.data || transactionList.data || transactionList || [];
    if (!Array.isArray(items)) items = [];
    return items.map((item: any) => ({
      invoiceExchangeId: item.invoiceExchangeId || item.id || '',
      supplierName: item.supplierName || '',
      totalRow: item.totalRow || 0,
      totalTransaction: item.totalTransaction || 0,
      status: item.status || '',
    })).filter((i: SupplierGroupData) => i.supplierName && i.invoiceExchangeId);
  })();

  // Clear detail cache and selections when filters or page size change
  useEffect(() => {
    setDetailDataMap({});
    setDetailTotalData({});
    setDetailPageMap({});
    setSelectedRows(new Set());
    setSelectedRowDataMap({});
    setSelectedDueDateMap({});
    setSelectedDaysMap({});
    setPrintDataList([]);
  }, [pageSize, formattedStartDate, formattedEndDate, status, supplierType]);

  // Clear detail cache when debounced keyword changes
  useEffect(() => {
    const keys = Object.keys(debouncedKeywordMap);
    if (keys.length === 0) return;
    setDetailDataMap(prev => {
      const next = { ...prev };
      keys.forEach(k => delete next[k]);
      return next;
    });
    setDetailLoadingMap(prev => {
      const next = { ...prev };
      keys.forEach(k => delete next[k]);
      return next;
    });
    // Reset to page 1 for affected keys
    setDetailPageMap(prev => {
      const next = { ...prev };
      keys.forEach(k => { next[k] = 1; });
      return next;
    });
  }, [debouncedKeywordMap]);

  // Fetch detail for expanded accordions
  useEffect(() => {
    if (supplierGroups.length === 0) return;

    const fetchDetail = async (key: string, name: string, stat: string) => {
      console.log("fetchDetail", key, name, stat);
      console.log("fetchedKeysRef", detailLoadingMap[key], detailDataMap[key]);
      if (detailLoadingMap[key] || detailDataMap[key]) return;
      setDetailLoadingMap((p) => ({ ...p, [key]: true }));

      try {
        const currentPage = getDetailPage(key);
        const keywordVal = debouncedKeywordMap[key] || '';
        const params: Record<string, string> = { page: String(currentPage), pageSize: String(pageSize) };
        if (keywordVal) params.keyword = keywordVal;
        if (name) params.supplierName = name;
        if (stat) params.status = stat;
        if (supplierType) params.supplierType = supplierType;
        if (formattedStartDate) params.startDate = formattedStartDate;
        if (formattedEndDate) params.endDate = formattedEndDate;
        // Order by batch number descending for exchanged status
        if (stat && stat.toLowerCase() !== 'not sent') {
          params.orderBy = 'batchnumber';
          params.orderType = 'desc';
        }

        const result = await fetchTransactionDetail(params).unwrap();
        console.log("result", result);

        let items = result.data?.data || result.data || result || [];
        if (!Array.isArray(items)) items = [];

        const parseExchangeDate = (item: any): string => {
          if (item.exchangeDate) return item.exchangeDate;
          // Fallback: parse from batchNumber format yyyy/MM/dd/seq
          if (item.batchNumber) {
            const parts = item.batchNumber.split('/');
            if (parts.length >= 3) {
              return `${parts[0]}-${parts[1]}-${parts[2]}T00:00:00Z`;
            }
          }
          return '';
        };

        const mapped = items.map((i: any) => ({
          invoiceExchangeId: i.invoiceExchangeId || i.id || '',
          id: i.id || i.invoiceExchangeId || '',
          noPo: i.noPo || i.poNumber || i.purchaseOrderNo || '',
          tanggalPenerimaan: i.tanggalPenerimaan || i.receiptDate || '',
          tanggalPo: i.tanggalPo || i.poDate || '',
          noFaktur: i.noFaktur || i.invoiceNumber || '',
          namaSupplier: i.namaSupplier || i.supplierName || name,
          jumlahTransaksi: i.jumlahTransaksi || i.amount || i.totalTransaction || 0,
          namaPerusahaan: i.namaPerusahaan || i.companyName || name,
          dueDate: i.dueDate || '',
          exchangeDate: parseExchangeDate(i),
          batchNumber: i.batchNumber || '',
          paymentStatus: i.paymentStatus || 'Unpaid',
          manualPaid: Boolean(i.manualPaid),
          manualPaidNote: i.manualPaidNote || '',
        }));

        setDetailDataMap((p) => ({ ...p, [key]: mapped }));
        setDetailTotalData((p) => ({ ...p, [key]: result.data?.totalData || result.totalData || 0 }));
      } catch (error) {
        console.error("fetchDetail error", error);
        setDetailDataMap((p) => ({ ...p, [key]: [] }));
        setDetailTotalData((p) => ({ ...p, [key]: 0 }));
      } finally {
        setDetailLoadingMap((p) => ({ ...p, [key]: false }));
      }
    };

    expandedAccordions.forEach((key) => {
      const group = supplierGroups.find((g) => String(g.invoiceExchangeId) === key);
      console.log("group", group);
      if (group) fetchDetail(key, group.supplierName, group.status || '');
    });
  }, [expandedAccordions, detailPageMap, pageSize, formattedStartDate, formattedEndDate, status, supplierGroups.length, debouncedKeywordMap]);

  const toggleAccordion = (id: string | number) => {
    const key = String(id);
    setExpandedAccordions((prev) => {
      const next = new Set(prev);
      next.has(key) ? next.delete(key) : next.add(key);
      return next;
    });
  };



  const handleExport = async () => {
    // if (!isFilterApplied) { toastMessage('Terapkan filter terlebih dahulu', 'error'); return; }
    try {
      const params: Record<string, string> = { type: 'xlsx' };
      if (supplierName) params.supplierName = supplierName;
      if (supplierType) params.supplierType = supplierType;
      if (formattedStartDate) params.startDate = formattedStartDate;
      if (formattedEndDate) params.endDate = formattedEndDate;
      if (status) params.status = status;

      const blob = await exportInvoiceExchange(params).unwrap();
      const url = window.URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.href = url;
      link.setAttribute('download', `tukar_faktur_${new Date().toISOString().split('T')[0]}.xlsx`);
      document.body.appendChild(link);
      link.click();
      link.remove();
      window.URL.revokeObjectURL(url);
    } catch (error: any) {
      toastMessage(error?.data?.message || 'Gagal export data', 'error');
    }
  };

  const getRowId = (row: InvoiceExchangeData, index: number): string =>
    String(row.invoiceExchangeId || row.id || `row-${index}`);

  const handleRowSelect = (rowId: string, checked: boolean, rowData?: InvoiceExchangeData, groupKey?: string) => {
    setSelectedRows((prev) => {
      const next = new Set(prev);
      checked ? next.add(rowId) : next.delete(rowId);
      return next;
    });

    // Store/remove row data for cross-page selection
    if (checked && rowData) {
      setSelectedRowDataMap(prev => ({ ...prev, [rowId]: rowData }));
    } else if (!checked) {
      setSelectedRowDataMap(prev => {
        const next = { ...prev };
        delete next[rowId];
        return next;
      });
    }

    // Set default due date if it's the first one being selected in this group
    if (checked && rowData?.dueDate && groupKey) {
      setSelectedDueDateMap(prev => {
        if (prev[groupKey]) return prev; // Already has a date
        return { ...prev, [groupKey]: new Date(rowData.dueDate) };
      });
    }
  };

  const handleSelectAll = (checked: boolean, data: InvoiceExchangeData[], groupKey: string) => {
    const rowIds = data.map((r, i) => getRowId(r, i));

    setSelectedRows((prev) => {
      const next = new Set(prev);
      if (checked) {
        rowIds.forEach(id => next.add(id));
      } else {
        rowIds.forEach(id => next.delete(id));
      }
      return next;
    });

    // Store/remove all row data for cross-page selection
    if (checked) {
      setSelectedRowDataMap(prev => {
        const next = { ...prev };
        data.forEach((r, i) => { next[getRowId(r, i)] = r; });
        return next;
      });
    } else {
      setSelectedRowDataMap(prev => {
        const next = { ...prev };
        rowIds.forEach(id => delete next[id]);
        return next;
      });
    }

    if (checked && data.length > 0 && groupKey) {
      const firstWithDueDate = data.find(r => r.dueDate);
      if (firstWithDueDate) {
        setSelectedDueDateMap(prev => ({ ...prev, [groupKey]: new Date(firstWithDueDate.dueDate) }));
      }
    }
  };

  const handleUpdate = async (displayData: InvoiceExchangeData[]) => {
    if (selectedRows.size === 0) { toastMessage('Pilih minimal satu item', 'error'); return; }

    const ids = displayData
      .filter((r, i) => selectedRows.has(getRowId(r, i)) && (r.invoiceExchangeId || r.id))
      .map((r) => String(r.invoiceExchangeId || r.id));

    if (ids.length === 0) { toastMessage('Tidak ada ID valid', 'error'); return; }

    try {
      const payload: any = { id: ids.join(',') };
      if (pendingDueDate) {
        payload.dueDate = new Date(Date.UTC(pendingDueDate.getFullYear(), pendingDueDate.getMonth(), pendingDueDate.getDate())).toISOString();
      }
      if (pendingExchangeDate) {
        payload.exchangeDate = new Date(Date.UTC(pendingExchangeDate.getFullYear(), pendingExchangeDate.getMonth(), pendingExchangeDate.getDate())).toISOString();
      }
      await updateInvoiceExchangeStatus(payload).unwrap();
      setSelectedRows(new Set());
      setSelectedRowDataMap({});
      // Collapse all expanded accordions after successful update (since items are now Sent)
      setExpandedAccordions(new Set());
      // Clear caches
      setDetailDataMap({});
      setDetailTotalData({});
      setDetailLoadingMap({});
      setDetailPageMap({});
      // Refetch transaction list to get updated data
      refetchTransaction();
      toastMessage('Status berhasil diupdate', 'success');
      setShowUpdateModal(false);
      setPendingUpdateData([]);
    } catch (error: any) {
      toastMessage(error?.data?.message || 'Gagal update status', 'error');
    }
  };

  const totalAccordionRecords = transactionList?.data?.totalData || supplierGroups.length;

  return (
    <div className="space-y-6">
      {/* Filter Card */}
      <Card>
        <CardHeader className="border-b border-gray-100 pb-4">
          <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
            {/* Left side - Title */}
            <div className="flex items-center gap-3">
              <div className="p-2 rounded-lg bg-primary/10">
                <FileText className="h-5 w-5 text-primary" />
              </div>
              <div>
                <CardTitle className="text-lg">{t('account_payable.tukar_faktur.title')}</CardTitle>
                <p className="text-sm text-gray-500 mt-0.5">
                  {t('account_payable.tukar_faktur.subtitle')}
                </p>
              </div>
            </div>

            {/* Right side - Filters */}
            <div className="flex flex-col md:flex-row items-end md:items-center gap-3 ml-auto w-full md:w-auto">
              {/* Status */}
              <div className="w-[160px]">
                <select
                  className="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                  value={status}
                  onChange={(e) => setStatus(e.target.value)}
                >
                  <option value="not sent">{t('account_payable.tukar_faktur.filter.not_sent')}</option>
                  <option value="Sent">{t('account_payable.tukar_faktur.filter.sent')}</option>
                </select>
              </div>

              {/* Supplier Type */}
              <div className="w-[150px]">
                <select
                  className="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                  value={supplierType}
                  onChange={(e) => setSupplierType(e.target.value)}
                >
                  <option value="">Semua Tipe</option>
                  <option value="Medis">Medis</option>
                  <option value="Umum">Umum</option>
                  <option value="Dapur">Dapur</option>
                </select>
              </div>

              {/* Supplier */}
              <div className="w-[200px]">
                <SelectSearch
                  placeholder={t('account_payable.tukar_faktur.select_supplier')}
                  options={companyOptions}
                  isLoading={isLoadingSupplier}
                  onInputChange={(v) => setSearchCompany(v)}
                  onChange={(opt: any) => setSelectedCompany(opt)}
                  value={selectedCompany}
                  isClearable
                  styles={{
                    control: (base) => ({
                      ...base,
                      minHeight: '40px',
                      borderColor: '#e2e8f0',
                      '&:hover': { borderColor: '#cbd5e1' },
                    }),
                    menuPortal: (base) => ({ ...base, zIndex: 9999 }),
                  }}
                  menuPortalTarget={document.body}
                />
              </div>

              {/* Date Range */}
              <div className="relative w-[220px]">
                <Flatpickr
                  options={{ mode: 'range', dateFormat: 'd/m/Y', position: isRtl ? 'auto right' : 'auto left' }}
                  className="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ltr:pl-9 rtl:pr-9"
                  onChange={(dates: Date[]) => {
                    setStartDate(dates.length > 0 ? dates[0] : null);
                    setEndDate(dates.length > 1 ? dates[1] : null);
                  }}
                  placeholder={t('account_payable.tukar_faktur.select_period')}
                />
                <div className="absolute inset-y-0 ltr:left-3 rtl:right-3 flex items-center pointer-events-none text-gray-400">
                  <IconCalendar className="w-4 h-4" />
                </div>
              </div>

              <div className="flex items-center gap-2">
                <Button
                  variant="outline"
                  className="gap-2"
                  onClick={handleExport}
                  disabled={isExporting}
                >
                  {isExporting ? <Loader2 className="h-4 w-4 animate-spin" /> : <Download className="h-4 w-4" />}
                  <span className="hidden sm:inline">{isExporting ? t('account_payable.tukar_faktur.exporting') : t('account_payable.tukar_faktur.export')}</span>
                </Button>
              </div>
            </div>
          </div>
        </CardHeader>
      </Card>

      {/* Data Card */}
      <Card>
        {transactionError ? (
          <div className="flex flex-col items-center justify-center py-16">
            <p className="text-red-500 font-medium mb-2">{t('account_payable.tukar_faktur.error_loading')}</p>
            <button onClick={() => refetchTransaction()} className="btn btn-primary mt-2">{t('account_payable.tukar_faktur.retry')}</button>
          </div>
        ) : isLoadingTransaction ? (
          <div className="flex flex-col items-center justify-center py-16">
            <Loader2 className="w-10 h-10 text-primary animate-spin mb-4" />
            <p className="text-gray-500">{t('account_payable.tukar_faktur.loading')}</p>
          </div>
        ) : supplierGroups.length === 0 ? (
          <div className="flex flex-col items-center justify-center py-16">
            <FileText className="w-12 h-12 text-gray-300 mb-4" />
            <p className="text-gray-500">{t('account_payable.tukar_faktur.no_data')}</p>
          </div>
        ) : (
          <div className="divide-y divide-gray-200 dark:divide-gray-700">
            {supplierGroups.map((supplier) => {
              const key = String(supplier.invoiceExchangeId);
              const isExpanded = expandedAccordions.has(key);
              const isNotSent = supplier.status?.toLowerCase() === 'not sent';
              const detailData = isExpanded ? (detailDataMap[key] || []) : [];
              const isLoadingDetail = detailLoadingMap[key];
              const totalRecords = detailTotalData[key] || detailData.length;

              return (
                <div key={key}>
                  {/* Accordion Header */}
                  <button
                    type="button"
                    className="w-full flex items-center justify-between p-4 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
                    onClick={() => toggleAccordion(supplier.invoiceExchangeId)}
                  >
                    <div className="flex items-center gap-4">
                      <ChevronDown className={`w-5 h-5 text-gray-400 transition-transform ${isExpanded ? 'rotate-180' : ''}`} />
                      <div className="text-left">
                        <h3 className="font-semibold text-gray-800 dark:text-gray-100">{supplier.supplierName}</h3>
                        <p className="text-sm text-gray-500">{supplier.totalRow} {t('account_payable.tukar_faktur.transactions')}</p>
                      </div>
                    </div>
                    <div className="flex items-center gap-4">
                      <div className="text-right">
                        <p className="text-sm text-gray-500">{t('account_payable.tukar_faktur.total')}</p>
                        <p className="font-semibold font-mono">{FormatNumberRounded(supplier.totalTransaction)}</p>
                      </div>
                      <span className={`px-3 py-1 rounded-full text-xs font-medium ${isNotSent
                        ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400'
                        : 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                        }`}>
                        {isNotSent ? (
                          <span className="flex items-center gap-1"><Clock className="w-3 h-3" /> {t('account_payable.tukar_faktur.filter.not_sent')}</span>
                        ) : (
                          <span className="flex items-center gap-1"><CheckCircle2 className="w-3 h-3" /> {t('account_payable.tukar_faktur.filter.sent')}</span>
                        )}
                      </span>
                    </div>
                  </button>

                  {/* Accordion Content */}
                  <AnimateHeight duration={300} height={isExpanded ? 'auto' : 0}>
                    <div className="p-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-700">
                      {isNotSent && detailData.length > 0 && (() => {
                        const selectedInGroup = detailData.filter((r, i) => selectedRows.has(getRowId(r, i)));
                        const selectedCountInGroup = selectedInGroup.length;

                        return (
                          <div className="flex items-center justify-between gap-4 mb-4">
                            {/* Keyword Filter */}
                            <div className="relative w-[220px]">
                              <input
                                type="text"
                                className="flex h-9 w-full items-center rounded-md border border-input bg-white pl-9 pr-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                                placeholder="No Invoice / No PO"
                                value={detailKeywordMap[key] || ''}
                                onChange={(e) => {
                                  const val = e.target.value;
                                  setDetailKeywordMap(prev => ({ ...prev, [key]: val }));
                                }}
                              />
                              <div className="absolute inset-y-0 left-3 flex items-center pointer-events-none text-gray-400">
                                <Search className="w-4 h-4" />
                              </div>
                            </div>
                            <div className="flex items-center gap-4">
                            {selectedCountInGroup > 0 && (
                              <div className="flex items-center gap-4">
                                <div className="flex items-center gap-2">
                                  <span className="text-sm font-medium text-gray-700">Tgl Tukar Faktur :</span>
                                  <div className="w-[160px]">
                                    <Flatpickr
                                      options={{ dateFormat: 'd/m/Y', position: isRtl ? 'auto right' : 'auto left' }}
                                      className="form-input text-sm"
                                      value={selectedExchangeDateMap[key] || ''}
                                      onChange={(dates: Date[]) => {
                                        if (dates.length > 0) {
                                          const exchangeDate = dates[0];
                                          setSelectedExchangeDateMap(prev => ({ ...prev, [key]: exchangeDate }));

                                          // Auto set due date = exchange date + 30 days
                                          const newDueDate = new Date(exchangeDate);
                                          newDueDate.setDate(newDueDate.getDate() + 30);
                                          setSelectedDueDateMap(prev => ({ ...prev, [key]: newDueDate }));
                                          setSelectedDaysMap(prev => ({ ...prev, [key]: '30' }));
                                        }
                                      }}
                                      placeholder="Tgl Tukar Faktur"
                                    />
                                  </div>
                                </div>
                                <div className="flex items-center gap-2">
                                  <span className="text-sm font-medium text-gray-700">{t('account_payable.tukar_faktur.due_date_label')} :</span>
                                  <div className="w-[160px]">
                                    <Flatpickr
                                      options={{ dateFormat: 'd/m/Y', position: isRtl ? 'auto right' : 'auto left' }}
                                      className="form-input text-sm"
                                      value={selectedDueDateMap[key] || ''}
                                      onChange={(dates: Date[]) => {
                                        if (dates.length > 0) {
                                          const date = dates[0];
                                          setSelectedDueDateMap(prev => ({ ...prev, [key]: date }));

                                          // Sync Days from exchange date
                                          const baseDate = selectedExchangeDateMap[key] ? new Date(selectedExchangeDateMap[key]!) : new Date();
                                          baseDate.setHours(0, 0, 0, 0);
                                          const target = new Date(date);
                                          target.setHours(0, 0, 0, 0);
                                          const diffTime = target.getTime() - baseDate.getTime();
                                          const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                                          setSelectedDaysMap(prev => ({ ...prev, [key]: diffDays.toString() }));
                                        }
                                      }}
                                      placeholder={t('account_payable.tukar_faktur.due_date_label')}
                                    />
                                  </div>
                                </div>
                                <div className="flex items-center gap-1.5">
                                  <input
                                    type="number"
                                    className="form-input text-sm w-[70px] px-2 text-center"
                                    value={selectedDaysMap[key] || ''}
                                    onChange={(e) => {
                                      const val = e.target.value;
                                      setSelectedDaysMap(prev => ({ ...prev, [key]: val }));
                                      const days = parseInt(val);
                                      if (!isNaN(days)) {
                                        const baseDate = selectedExchangeDateMap[key] ? new Date(selectedExchangeDateMap[key]!) : new Date();
                                        baseDate.setHours(0, 0, 0, 0);
                                        const newDate = new Date(baseDate);
                                        newDate.setDate(newDate.getDate() + days);
                                        setSelectedDueDateMap(prev => ({ ...prev, [key]: newDate }));
                                      } else if (val === '') {
                                        setSelectedDueDateMap(prev => ({ ...prev, [key]: null }));
                                      }
                                    }}
                                    placeholder="0"
                                    min="0"
                                  />
                                  <span className="text-sm font-medium text-gray-700">{t('account_payable.tukar_faktur.days_label')}</span>
                                </div>
                              </div>
                            )}
                            <button
                              className="btn btn-primary btn-sm gap-2"
                              onClick={() => {
                                setPendingUpdateData(selectedInGroup);
                                setPendingDueDate(selectedDueDateMap[key] || null);
                                setPendingExchangeDate(selectedExchangeDateMap[key] || null);
                                setShowUpdateModal(true);
                              }}
                              disabled={selectedCountInGroup === 0 || isUpdatingStatus}
                            >
                              <CheckCircle2 className="w-4 h-4" />
                              {isUpdatingStatus ? t('account_payable.tukar_faktur.processing') : `${t('account_payable.tukar_faktur.mark_exchanged')} (${selectedCountInGroup})`}
                            </button>
                            </div>
                          </div>
                        );
                      })()}

                      {!isNotSent && detailData.length > 0 && (() => {
                        // Count selections across all pages for this supplier
                        const selectedCountInGroup = Object.entries(selectedRowDataMap)
                          .filter(([id, data]) => selectedRows.has(id) && data.namaSupplier === supplier.supplierName)
                          .length;

                        return (
                          <div className="flex items-center justify-between gap-2 mb-4">
                            {/* Keyword Filter */}
                            <div className="relative w-[220px]">
                              <input
                                type="text"
                                className="flex h-9 w-full items-center rounded-md border border-input bg-white pl-9 pr-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                                placeholder="No Invoice / No PO"
                                value={detailKeywordMap[key] || ''}
                                onChange={(e) => {
                                  const val = e.target.value;
                                  setDetailKeywordMap(prev => ({ ...prev, [key]: val }));
                                }}
                              />
                              <div className="absolute inset-y-0 left-3 flex items-center pointer-events-none text-gray-400">
                                <Search className="w-4 h-4" />
                              </div>
                            </div>
                            <Button
                              size="sm"
                              variant="outline"
                              className="gap-1.5"
                              onClick={() => handlePrintSelectedResi(supplier.supplierName, key)}
                            >
                              <Printer className="w-3.5 h-3.5" />
                              {selectedCountInGroup > 0
                                ? `${t('account_payable.tukar_faktur.print_resi')} (${selectedCountInGroup})`
                                : t('account_payable.tukar_faktur.print_resi')
                              }
                            </Button>
                          </div>
                        );
                      })()}

                      <div className="datatables">
                        {isLoadingDetail ? (
                          <div className="flex items-center justify-center py-8">
                            <Loader2 className="w-6 h-6 text-primary animate-spin" />
                          </div>
                        ) : (
                          <DataTable
                            highlightOnHover
                            className="whitespace-nowrap table-hover cursor-pointer"
                            records={detailData}
                            onRowClick={(record: InvoiceExchangeData, recordIndex: number) => {
                              const rowId = getRowId(record, recordIndex);
                              handleRowSelect(rowId, !selectedRows.has(rowId), record, key);
                            }}
                            columns={[
                              {
                                accessor: 'checkbox',
                                title: (
                                  <input
                                    type="checkbox"
                                    className="form-checkbox"
                                    checked={detailData.length > 0 && detailData.every((r, i) => selectedRows.has(getRowId(r, i)))}
                                    onChange={(e) => handleSelectAll(e.target.checked, detailData, key)}
                                    onClick={(e) => e.stopPropagation()}
                                  />
                                ),
                                render: (row: InvoiceExchangeData, index: number) => (
                                  <input
                                    type="checkbox"
                                    className="form-checkbox"
                                    checked={selectedRows.has(getRowId(row, index))}
                                    onChange={(e) => handleRowSelect(getRowId(row, index), e.target.checked, row, key)}
                                    onClick={(e) => e.stopPropagation()}
                                  />
                                ),
                                width: 50,
                              },
                              { accessor: 'noPo', title: t('account_payable.tukar_faktur.table.po_no'), sortable: true },
                              { accessor: 'tanggalPenerimaan', title: t('account_payable.tukar_faktur.table.receipt_date'), render: (r: InvoiceExchangeData) => r.tanggalPenerimaan ? FormatDate(r.tanggalPenerimaan) : '-' },
                              { accessor: 'tanggalPo', title: t('account_payable.tukar_faktur.table.po_date'), render: (r: InvoiceExchangeData) => r.tanggalPo ? FormatDate(r.tanggalPo) : '-' },
                              { accessor: 'noFaktur', title: t('account_payable.tukar_faktur.table.invoice_no') },
                              ...(!isNotSent ? [{
                                accessor: 'exchangeDate',
                                title: t('account_payable.tukar_faktur.table.exchange_date'),
                                render: (r: InvoiceExchangeData) => (
                                  <div>
                                    <div>{r.exchangeDate ? FormatDate(r.exchangeDate) : '-'}</div>
                                    {r.batchNumber && <div className="text-xs text-gray-500">{r.batchNumber}</div>}
                                  </div>
                                ),
                              }] : []),
                              {
                                accessor: 'jumlahTransaksi',
                                title: t('account_payable.tukar_faktur.table.amount'),
                                textAlignment: 'right' as const,
                                cellsStyle: () => ({ textAlign: 'right' }),
                                render: (r: InvoiceExchangeData) => <span className="font-mono block text-right">{FormatNumberRounded(r.jumlahTransaksi)}</span>
                              },
                              {
                                accessor: 'paymentStatus',
                                title: t('account_payable.tukar_faktur.table.payment_status', 'Status Bayar'),
                                textAlignment: 'center' as const,
                                cellsStyle: () => ({ textAlign: 'center' }),
                                render: (r: InvoiceExchangeData) => {
                                  const isPaid = r.paymentStatus === 'Paid';
                                  const badgeClass = !isPaid
                                    ? (r.paymentStatus === 'Partial' ? 'bg-info/10 text-info' : 'bg-warning/10 text-warning')
                                    : r.manualPaid
                                      ? 'bg-info/10 text-info'
                                      : 'bg-success/10 text-success';
                                  const label = isPaid && r.manualPaid
                                    ? t('account_payable.tukar_faktur.paid_manual', 'Paid (Manual)')
                                    : (r.paymentStatus || 'Unpaid');

                                  return (
                                    <span
                                      className={`px-2 py-1 rounded-full text-xs font-semibold ${badgeClass}`}
                                      title={r.manualPaid ? r.manualPaidNote || undefined : undefined}
                                    >
                                      {label}
                                    </span>
                                  );
                                },
                              },
                              {
                                accessor: 'riwayat',
                                title: '',
                                width: 50,
                                render: (r: InvoiceExchangeData) => (
                                  <TimelineButton
                                    module={AUDIT_MODULE.INVOICE_EXCHANGE}
                                    refId={r.invoiceExchangeId ?? r.id}
                                    title={r.noFaktur}
                                  />
                                ),
                              },
                            ]}
                            totalRecords={totalRecords}
                            recordsPerPage={pageSize}
                            page={getDetailPage(key)}
                            onPageChange={(newPage) => setDetailPage(key, newPage)}
                            recordsPerPageOptions={PAGE_SIZES}
                            onRecordsPerPageChange={(newSize) => {
                              setPageSize(newSize);
                              setDetailDataMap({});
                              setDetailPageMap({});
                            }}
                            minHeight={150}
                            noRecordsText={t('account_payable.tukar_faktur.no_detail')}
                            paginationText={({ from, to, totalRecords }) =>
                              `Showing  ${from} to ${to} of ${totalRecords} entries`
                            }
                            paginationSize="xs"
                            styles={{
                              root: { border: 'none', borderRadius: '0.5rem', overflow: 'hidden' },
                              header: { backgroundColor: '#4361ee', color: '#fff', fontWeight: 'bold', textTransform: 'uppercase', fontSize: '0.75rem' },
                              pagination: { fontSize: '0.75rem', padding: '0.5rem' },
                            }}
                          />
                        )}
                      </div>
                    </div>
                  </AnimateHeight>
                </div>
              );
            })}
          </div>
        )}

        {/* Pagination */}
        {!isLoadingTransaction && supplierGroups.length > 0 && totalAccordionRecords > accordionPageSize && (() => {
          const totalPages = Math.ceil(totalAccordionRecords / accordionPageSize);
          const getPageNumbers = () => {
            const pages: (number | string)[] = [];
            const showEllipsis = totalPages > 7;
            if (!showEllipsis) {
              for (let i = 1; i <= totalPages; i++) pages.push(i);
            } else {
              if (accordionPage <= 4) {
                for (let i = 1; i <= 5; i++) pages.push(i);
                pages.push('...');
                pages.push(totalPages);
              } else if (accordionPage >= totalPages - 3) {
                pages.push(1);
                pages.push('...');
                for (let i = totalPages - 4; i <= totalPages; i++) pages.push(i);
              } else {
                pages.push(1);
                pages.push('...');
                for (let i = accordionPage - 1; i <= accordionPage + 1; i++) pages.push(i);
                pages.push('...');
                pages.push(totalPages);
              }
            }
            return pages;
          };
          return (
            <div style={{
              display: 'flex',
              justifyContent: 'space-between',
              alignItems: 'center',
              padding: '0.75rem 1rem',
              borderTop: '1px solid #e9ecef',
              backgroundColor: '#fff',
              fontSize: '14px'
            }}>
              <div style={{ display: 'flex', alignItems: 'center', gap: '0.5rem' }}>
                <span style={{ color: '#868e96' }}>
                  Showing  {(accordionPage - 1) * accordionPageSize + 1} to {Math.min(accordionPage * accordionPageSize, totalAccordionRecords)} of {totalAccordionRecords} entries
                </span>
              </div>
              <div style={{ display: 'flex', alignItems: 'center', gap: '0.75rem' }}>
                <select
                  style={{
                    height: '32px',
                    padding: '0 0.75rem',
                    border: '1px solid #dee2e6',
                    borderRadius: '4px',
                    fontSize: '14px',
                    backgroundColor: '#fff',
                    cursor: 'pointer'
                  }}
                  value={accordionPageSize}
                  onChange={(e) => { setAccordionPageSize(Number(e.target.value)); setAccordionPage(1); }}
                >
                  {ACCORDION_PAGE_SIZES.map(s => <option key={s} value={s}>{s}</option>)}
                </select>
                <div style={{ display: 'flex', alignItems: 'center', gap: '8px' }}>
                  {/* Prev Arrow */}
                  <button
                    disabled={accordionPage === 1}
                    onClick={() => setAccordionPage(p => Math.max(1, p - 1))}
                    style={{
                      width: '32px',
                      height: '32px',
                      display: 'flex',
                      alignItems: 'center',
                      justifyContent: 'center',
                      border: 'none',
                      borderRadius: '4px',
                      backgroundColor: 'transparent',
                      color: accordionPage === 1 ? '#ced4da' : '#495057',
                      cursor: accordionPage === 1 ? 'not-allowed' : 'pointer',
                      fontSize: '18px'
                    }}
                  >
                    ‹
                  </button>
                  {/* Page Numbers */}
                  {getPageNumbers().map((p, i) => (
                    p === '...' ? (
                      <span key={`ellipsis-${i}`} style={{ color: '#868e96', padding: '0 4px' }}>…</span>
                    ) : (
                      <button
                        key={p}
                        onClick={() => setAccordionPage(Number(p))}
                        style={{
                          width: '32px',
                          height: '32px',
                          display: 'flex',
                          alignItems: 'center',
                          justifyContent: 'center',
                          border: 'none',
                          borderRadius: '50%',
                          backgroundColor: accordionPage === p ? '#4361ee' : 'transparent',
                          color: accordionPage === p ? '#fff' : '#495057',
                          cursor: 'pointer',
                          fontSize: '14px',
                          fontWeight: accordionPage === p ? 600 : 400,
                          transition: 'all 0.15s ease'
                        }}
                      >
                        {p}
                      </button>
                    )
                  ))}
                  {/* Next Arrow */}
                  <button
                    disabled={accordionPage >= totalPages}
                    onClick={() => setAccordionPage(p => Math.min(totalPages, p + 1))}
                    style={{
                      width: '32px',
                      height: '32px',
                      display: 'flex',
                      alignItems: 'center',
                      justifyContent: 'center',
                      border: 'none',
                      borderRadius: '4px',
                      backgroundColor: 'transparent',
                      color: accordionPage >= totalPages ? '#ced4da' : '#495057',
                      cursor: accordionPage >= totalPages ? 'not-allowed' : 'pointer',
                      fontSize: '18px'
                    }}
                  >
                    ›
                  </button>
                </div>
              </div>
            </div>
          );
        })()}
      </Card>

      <ConfirmationModal
        confirmText={t('common.confirm')}
        message={`${t('account_payable.tukar_faktur.modal.confirm_message', {
          date: pendingDueDate ? FormatDate(pendingDueDate.toISOString()) : '-'
        })}${pendingExchangeDate ? `\nTanggal Tukar Faktur: ${FormatDate(pendingExchangeDate.toISOString())}` : ''}`}
        isOpen={showUpdateModal}
        onClose={() => { if (!isUpdatingStatus) { setShowUpdateModal(false); setPendingUpdateData([]); } }}
        onConfirm={() => handleUpdate(pendingUpdateData)}
      />

      {/* Print Portal */}
      {printDataList.length > 0 && createPortal(
        <div id="printable-resi-multi-container">
          <style>
            {`
            @media print {
              body > *:not(#printable-resi-multi-container) { display: none !important; }
              #printable-resi-multi-container { display: block !important; position: absolute; left: 0; top: 0; width: 100%; height: 100%; background: white; z-index: 9999; }
              @page { size: auto; margin: 0; }
              .resi-page-break { page-break-after: always; }
              .resi-page-break:last-child { page-break-after: avoid; }
            }
            @media screen {
              #printable-resi-multi-container { display: none; }
            }
          `}
          </style>
          {printDataList.map((resi, idx) => (
            <div key={idx} className={idx < printDataList.length - 1 ? 'resi-page-break' : ''}>
              <ResiTukarFaktur data={resi} />
            </div>
          ))}
        </div>,
        document.body
      )}
    </div>
  );
};

export default InvoiceExchange;
