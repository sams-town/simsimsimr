import { useEffect, useState } from 'react';
import { useDispatch } from 'react-redux';
import { useTranslation } from 'react-i18next';
import { useNavigate, useParams } from 'react-router-dom';
import { setBreadcrumbTitle, setPageTitle, setTitle } from '@/store/themeConfigSlice';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { useGetPettyCashRequestByIdQuery } from '@/store/api/pettyCash/pettyCashApiSlice';
import { FormatNumber } from '@/utils/formatNumber';
import { FormatDate } from '@/utils/formatDate';
import { ArrowLeft, FileText, Printer, CheckCircle, XCircle, Clock } from 'lucide-react';
import { Loader2 } from 'lucide-react';

const PettyCashRequestDetail = () => {
    const dispatch = useDispatch();
    const navigate = useNavigate();
    const { t } = useTranslation();
    const { id } = useParams();

    useEffect(() => {
        dispatch(setPageTitle('Detail Permintaan'));
        dispatch(setTitle('Detail Permintaan'));
        dispatch(setBreadcrumbTitle(['Dashboard', t('sidebar.petty_cash', 'Petty Cash'), 'Permintaan', 'Detail']));
    }, [dispatch, t]);

    const { data: requestRes, isLoading } = useGetPettyCashRequestByIdQuery(id);
    const request = requestRes?.data;

    if (isLoading) {
        return (
            <div className="flex items-center justify-center h-64">
                <Loader2 className="h-8 w-8 animate-spin text-primary" />
            </div>
        );
    }

    if (!request) {
        return (
            <div className="p-5 text-center">
                <div className="text-red-500 mb-2">Permintaan tidak ditemukan.</div>
                <Button onClick={() => navigate('/petty-cash/request')}>Kembali</Button>
            </div>
        );
    }

    const getStatusBadge = (status: string) => {
        const config: any = {
            Draft: { bg: 'bg-gray-100', text: 'text-gray-700', icon: Clock, label: 'Draft' },
            Submitted: { bg: 'bg-blue-100', text: 'text-blue-700', icon: Clock, label: 'Diajukan' },
            Approved: { bg: 'bg-green-100', text: 'text-green-700', icon: CheckCircle, label: 'Disetujui' },
            Rejected: { bg: 'bg-red-100', text: 'text-red-700', icon: XCircle, label: 'Ditolak' },
            Settled: { bg: 'bg-purple-100', text: 'text-purple-700', icon: CheckCircle, label: 'Selesai' },
            Posted: { bg: 'bg-teal-100', text: 'text-teal-700', icon: CheckCircle, label: 'Posted' },
        };
        const c = config[status] || config.Draft;
        const Icon = c.icon;

        return (
            <span className={`flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium ${c.bg} ${c.text}`}>
                <Icon className="w-4 h-4" />
                {c.label}
            </span>
        );
    };

    return (
        <div className="space-y-6">
            {/* Header */}
            <div className="flex items-center justify-between">
                <Button
                    variant="outline"
                    className="gap-2"
                    onClick={() => navigate('/petty-cash/request')}
                >
                    <ArrowLeft className="w-4 h-4" />
                    {t('common.back') || 'Kembali'}
                </Button>
                <div className="flex gap-2">
                    {(request.status === 'Approved' || request.status === 'Posted' || request.status === 'Settled') && (
                        <Button
                            variant="outline"
                            className="gap-2"
                            onClick={() => window.open(`/petty-cash/request/${id}/receipt`, '_blank')}
                        >
                            <Printer className="w-4 h-4" />
                            Cetak Bukti Pengeluaran
                        </Button>
                    )}
                    {request.status === 'Draft' && (
                        <Button onClick={() => navigate(`/petty-cash/request/edit/${id}`)}>
                            Edit Permintaan
                        </Button>
                    )}
                </div>
            </div>

            {/* Main Content */}
            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {/* Left Column: Details */}
                <div className="lg:col-span-2 space-y-6">
                    <Card>
                        <CardHeader className="border-b">
                            <div className="flex justify-between items-start">
                                <div>
                                    <CardTitle className="text-lg flex items-center gap-2">
                                        <FileText className="w-5 h-5 text-primary" />
                                        Informasi Permintaan
                                    </CardTitle>
                                    <p className="text-sm text-gray-500 mt-1">No. {request.requestNo}</p>
                                </div>
                                {getStatusBadge(request.status)}
                            </div>
                        </CardHeader>
                        <CardContent className="p-6">
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <p className="text-sm text-gray-500 mb-1">Tanggal Permintaan</p>
                                    <p className="font-semibold">{FormatDate(request.requestDate)}</p>
                                </div>
                                <div>
                                    <p className="text-sm text-gray-500 mb-1">Nama Pemohon</p>
                                    <p className="font-semibold">{request.requesterName}</p>
                                </div>
                                <div>
                                    <p className="text-sm text-gray-500 mb-1">Unit</p>
                                    <p className="font-semibold">{request.unitName}</p>
                                    <p className="text-xs text-gray-400">{request.unitCode}</p>
                                </div>
                                <div>
                                    <p className="text-sm text-gray-500 mb-1">Akun Biaya</p>
                                    <p className="font-semibold">{request.expenseCoaName || request.expenseCoaCode}</p>
                                    <p className="text-xs text-gray-400">{request.expenseCoaCode}</p>
                                </div>
                                <div className="md:col-span-2">
                                    <p className="text-sm text-gray-500 mb-1">Keperluan</p>
                                    <p className="font-medium bg-gray-50 p-3 rounded-md border border-gray-100">
                                        {request.purpose}
                                    </p>
                                </div>
                                {request.notes && (
                                    <div className="md:col-span-2">
                                        <p className="text-sm text-gray-500 mb-1">Catatan</p>
                                        <p className="text-sm">{request.notes}</p>
                                    </div>
                                )}
                            </div>
                        </CardContent>
                    </Card>

                    {/* Transaction / Journal Info if any */}
                    {(request.transactionId || request.journalNo) && (
                        <Card>
                            <CardHeader className="border-b bg-gray-50/50">
                                <CardTitle className="text-base">Informasi Jurnal</CardTitle>
                            </CardHeader>
                            <CardContent className="p-6">
                                <div className="grid grid-cols-2 gap-4">
                                    <div>
                                        <p className="text-sm text-gray-500">No. Jurnal</p>
                                        <p className="font-mono font-medium">{request.journalNo || '-'}</p>
                                    </div>
                                    <div>
                                        <p className="text-sm text-gray-500">Transaction ID</p>
                                        <p className="font-mono text-xs text-gray-600 truncate" title={request.transactionId}>
                                            {request.transactionId || '-'}
                                        </p>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    )}
                </div>

                {/* Right Column: Amount & Status Log */}
                <div className="space-y-6">
                    <Card className="bg-primary/5 border-primary/20">
                        <CardContent className="p-6">
                            <p className="text-sm text-gray-600 mb-1">Jumlah Permintaan</p>
                            <p className="text-3xl font-bold text-primary font-mono">
                                Rp {FormatNumber(request.amount)}
                            </p>

                            {request.approvedAmount && request.approvedAmount !== request.amount && (
                                <div className="mt-4 pt-4 border-t border-primary/10">
                                    <p className="text-sm text-gray-600 mb-1">Disetujui Sebesar</p>
                                    <p className="text-xl font-bold text-green-600 font-mono">
                                        Rp {FormatNumber(request.approvedAmount)}
                                    </p>
                                </div>
                            )}
                        </CardContent>
                    </Card>

                    {request.rejectedReason && (
                        <Card className="bg-red-50 border-red-200">
                            <CardContent className="p-6">
                                <p className="text-sm font-semibold text-red-700 mb-2">Alasan Penolakan:</p>
                                <p className="text-red-600 text-sm">{request.rejectedReason}</p>
                            </CardContent>
                        </Card>
                    )}
                </div>
            </div>
        </div>
    );
};

export default PettyCashRequestDetail;
