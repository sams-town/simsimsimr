import { useEffect, useState } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { useTranslation } from 'react-i18next';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '@/store/themeConfigSlice';
import { DataTable } from 'mantine-datatable';
import Flatpickr from 'react-flatpickr';
import 'flatpickr/dist/flatpickr.css';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Database, Calendar, Download, Search, Filter, Loader2, Info } from 'lucide-react';
import IconCalendar from '@/components/Icon/IconCalendar';
import { FormatNumber } from '@/utils/formatNumber';
import { toastMessage } from '@/utils/toastUtils';
import { getAuthenticated } from '@/utils/auth';

interface KhanzaJournalItem {
  tanggal: string;
  kodeAkun: string;
  noJurnal: string;
  noBukti: string;
  namaAkun: string;
  keterangan: string;
  debet: number;
  kredit: number;
  caraBayar: string;
  namaPoli: string;
}

interface KhanzaJournalResponse {
  data: KhanzaJournalItem[];
  totalData: number;
}

const JournalKhanza = () => {
  const dispatch = useDispatch();
  const { t } = useTranslation();
  const isRtl = useSelector((state: any) => state.themeConfig.rtlClass) === 'rtl';

  // State
  const [page, setPage] = useState<number>(1);
  const PAGE_SIZES: number[] = [10, 20, 50, 100];
  const [pageSize, setPageSize] = useState<number>(PAGE_SIZES[1]);
  const [startDate, setStartDate] = useState<string | null>(null);
  const [endDate, setEndDate] = useState<string | null>(null);
  const [isLoading, setIsLoading] = useState(false);
  const [isExporting, setIsExporting] = useState(false);
  const [data, setData] = useState<KhanzaJournalItem[]>([]);
  const [totalRecords, setTotalRecords] = useState(0);
  const [hasSearched, setHasSearched] = useState(false);

  const toUtcMidnight = (date: Date) => {
    return new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate(), 0, 0, 0));
  };

  useEffect(() => {
    dispatch(setPageTitle(t('khanza_journal_page.title')));
    dispatch(setTitle(t('khanza_journal_page.title')));
    dispatch(setBreadcrumbTitle([t('menu.dashboard'), t('menu.journal'), t('menu.khanza_downloader')]));
  }, [dispatch, t]);

  const fetchData = async () => {
    if (!startDate || !endDate) {
      toastMessage(t('khanza_journal_page.toast.select_period'), 'error');
      return;
    }

    setIsLoading(true);
    setHasSearched(true);

    try {
      const token = getAuthenticated();
      const response = await fetch(
        `${import.meta.env.VITE_HOST}/Journal/khanza-sync?startDate=${startDate}&endDate=${endDate}&page=${page}&pageSize=${pageSize}`,
        {
          headers: {
            'Authorization': `Bearer ${token}`,
          }
        }
      );

      if (!response.ok) {
        const errorData = await response.json();
        throw new Error(errorData.message || 'Failed to fetch data');
      }

      const result = await response.json();
      setData(result.data?.data || []);
      setTotalRecords(result.data?.totalData || 0);
    } catch (error: any) {
      toastMessage(error.message || t('khanza_journal_page.toast.fetch_failed'), 'error');
      setData([]);
      setTotalRecords(0);
    } finally {
      setIsLoading(false);
    }
  };

  const handleExport = async () => {
    if (!startDate || !endDate) {
      toastMessage(t('khanza_journal_page.toast.select_period'), 'error');
      return;
    }

    setIsExporting(true);

    try {
      const token = getAuthenticated();
      const response = await fetch(
        `${import.meta.env.VITE_HOST}/Journal/khanza-sync/export?startDate=${startDate}&endDate=${endDate}`,
        {
          headers: {
            'Authorization': `Bearer ${token}`,
          }
        }
      );

      if (!response.ok) {
        const errorData = await response.json();
        throw new Error(errorData.message || 'Failed to export data');
      }

      const blob = await response.blob();
      const url = window.URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.href = url;
      const startDateStr = startDate?.split('T')[0].replace(/-/g, '');
      const endDateStr = endDate?.split('T')[0].replace(/-/g, '');
      link.setAttribute('download', `JurnalKhanza_${startDateStr}_${endDateStr}.xlsx`);
      document.body.appendChild(link);
      link.click();
      link.remove();
      window.URL.revokeObjectURL(url);
      toastMessage(t('khanza_journal_page.export.success'), 'success');
    } catch (error: any) {
      toastMessage(error.message || t('khanza_journal_page.export.failed'), 'error');
    } finally {
      setIsExporting(false);
    }
  };

  const handleApplyFilter = () => {
    setPage(1);
    fetchData();
  };

  useEffect(() => {
    if (hasSearched && startDate && endDate) {
      fetchData();
    }
  }, [page, pageSize]);

  return (
    <div className="space-y-6">
      <Card>
        <CardHeader className="border-b border-gray-100 pb-4">
          <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
            {/* Left side - Title */}
            <div className="flex items-center gap-4">
              <div className="flex items-center gap-3">
                <div className="p-2 rounded-lg bg-primary/10">
                  <Database className="h-5 w-5 text-primary" />
                </div>
                <div>
                  <CardTitle className="text-lg">{t('khanza_journal_page.title')}</CardTitle>
                  <p className="text-sm text-gray-500 mt-0.5">
                    {t('khanza_journal_page.subtitle')}
                  </p>
                </div>
              </div>
            </div>

            {/* Right side - Filters & Actions */}
            <div className="flex flex-col md:flex-row items-end md:items-center gap-3 ml-auto w-full md:w-auto">
              {/* Date Filter */}
              <div className="relative w-[280px]">
                <Flatpickr
                  options={{
                    mode: 'range',
                    dateFormat: 'd/m/Y',
                    enableTime: false,
                    position: isRtl ? 'auto right' : 'auto left',
                  }}
                  className="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 ltr:pl-9 rtl:pr-9"
                  onChange={(dates: Date[]) => {
                    if (dates.length === 2) {
                      setStartDate(toUtcMidnight(dates[0]).toISOString());
                      setEndDate(toUtcMidnight(dates[1]).toISOString());
                    }
                  }}
                  placeholder={t('khanza_journal_page.filter.date_range')}
                />
                <div className="absolute inset-y-0 ltr:left-3 rtl:right-3 flex items-center pointer-events-none text-gray-400">
                  <IconCalendar className="w-4 h-4" />
                </div>
              </div>

              {/* Apply Filter Button */}
              <Button
                onClick={handleApplyFilter}
                disabled={isLoading || !startDate || !endDate}
                className="gap-2"
              >
                {isLoading ? (
                  <Loader2 className="h-4 w-4 animate-spin" />
                ) : (
                  <Filter className="h-4 w-4" />
                )}
                <span>{isLoading ? t('khanza_journal_page.filter.showing') : t('khanza_journal_page.filter.show')}</span>
              </Button>

              {/* Export Button */}
              <Button
                variant="outline"
                className="gap-2"
                onClick={handleExport}
                disabled={isExporting || !startDate || !endDate}
              >
                {isExporting ? (
                  <Loader2 className="h-4 w-4 animate-spin" />
                ) : (
                  <Download className="h-4 w-4" />
                )}
                <span className="hidden sm:inline">{isExporting ? t('khanza_journal_page.export.exporting') : t('khanza_journal_page.export.button')}</span>
              </Button>
            </div>
          </div>
        </CardHeader>

        <CardContent className="pt-6">
          {!hasSearched ? (
            <div className="bg-blue-50 border border-blue-100 rounded-xl p-6 flex items-start gap-3">
              <Info className="h-5 w-5 text-blue-500 mt-0.5 shrink-0" />
              <div>
                <p className="text-sm text-blue-800 font-medium">{t('khanza_journal_page.info.title')}</p>
                <p className="text-sm text-blue-600 mt-1">
                  {t('khanza_journal_page.info.desc')}
                </p>
              </div>
            </div>
          ) : (
            <div className="datatables z-0 rounded-lg border border-gray-100 overflow-hidden">
              <DataTable
                highlightOnHover
                className="whitespace-nowrap table-hover"
                records={data}
                columns={[
                  {
                    accessor: 'tanggal',
                    title: t('khanza_journal_page.table.date'),
                    titleClassName: 'font-bold text-white bg-primary',
                    render: (row) => (
                      <span className="text-gray-600">{row.tanggal || '-'}</span>
                    )
                  },
                  {
                    accessor: 'noJurnal',
                    title: t('khanza_journal_page.table.journal_no'),
                    titleClassName: 'font-bold text-white bg-primary',
                    render: (row) => (
                      <span className="font-semibold text-primary">{row.noJurnal || '-'}</span>
                    )
                  },
                  {
                    accessor: 'kodeAkun',
                    title: t('khanza_journal_page.table.account_code'),
                    titleClassName: 'font-bold text-white bg-primary',
                    render: (row) => (
                      <span className="font-mono text-gray-700">{row.kodeAkun || '-'}</span>
                    )
                  },
                  {
                    accessor: 'namaAkun',
                    title: t('khanza_journal_page.table.account_name'),
                    titleClassName: 'font-bold text-white bg-primary',
                    render: (row) => (
                      <div className="max-w-[200px] truncate" title={row.namaAkun}>
                        {row.namaAkun || '-'}
                      </div>
                    )
                  },
                  {
                    accessor: 'debet',
                    title: t('khanza_journal_page.table.debit'),
                    textAlignment: 'right',
                    titleClassName: 'font-bold text-white bg-primary text-right',
                    render: (row) => (
                      <div className="font-mono font-bold text-gray-900 text-right">
                        {row.debet > 0 ? FormatNumber(row.debet) : '-'}
                      </div>
                    ),
                  },
                  {
                    accessor: 'kredit',
                    title: t('khanza_journal_page.table.credit'),
                    textAlignment: 'right',
                    titleClassName: 'font-bold text-white bg-primary text-right',
                    render: (row) => (
                      <div className="font-mono font-bold text-gray-900 text-right">
                        {row.kredit > 0 ? FormatNumber(row.kredit) : '-'}
                      </div>
                    ),
                  },
                  {
                    accessor: 'caraBayar',
                    title: t('khanza_journal_page.table.payment_method'),
                    titleClassName: 'font-bold text-white bg-primary',
                    render: (row) => (
                      <span className={`px-2 py-1 rounded-full text-xs font-medium ${row.caraBayar ? 'bg-blue-100 text-blue-700' : 'text-gray-400'}`}>
                        {row.caraBayar || '-'}
                      </span>
                    )
                  },
                  {
                    accessor: 'namaPoli',
                    title: t('khanza_journal_page.table.poli'),
                    titleClassName: 'font-bold text-white bg-primary',
                    render: (row) => (
                      <span className="text-gray-600">{row.namaPoli || '-'}</span>
                    )
                  },
                ]}
                totalRecords={totalRecords}
                recordsPerPage={pageSize}
                page={page}
                onPageChange={(p) => setPage(p)}
                recordsPerPageOptions={PAGE_SIZES}
                onRecordsPerPageChange={setPageSize}
                minHeight={200}
                paginationText={({ from, to, totalRecords }) => t('common.pagination_text', { from, to, totalRecords })}
                fetching={isLoading}
                loaderVariant="dots"
                loaderColor="#4361ee"
                noRecordsText={t('khanza_journal_page.table.no_data')}
                styles={{
                  root: {
                    border: 'none',
                    borderRadius: '0.75rem',
                    overflow: 'hidden',
                  },
                  pagination: {
                    borderTop: '1px solid #f1f5f9',
                    margin: '1rem',
                  },
                }}
              />
            </div>
          )}
        </CardContent>
      </Card>

      {/* Info Note */}
      <div className="bg-amber-50 border border-amber-100 rounded-lg p-4 text-sm text-amber-800">
        <h4 className="font-semibold mb-1 flex items-center"><Database className="h-4 w-4 mr-2" /> {t('khanza_journal_page.note.title')}</h4>
        <p>{t('khanza_journal_page.note.desc')}</p>
      </div>
    </div>
  );
};

export default JournalKhanza;
