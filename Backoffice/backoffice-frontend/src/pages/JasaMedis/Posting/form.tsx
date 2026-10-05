import { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import { useDispatch, useSelector } from 'react-redux';
import { setPageTitle, setTitle, setBreadcrumbTitle } from '@/store/themeConfigSlice';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import SelectSearch from 'react-select';
import CreatableSelect from 'react-select/creatable';
import Flatpickr from 'react-flatpickr';
import 'flatpickr/dist/flatpickr.css';
import {
    useGetDoctorOptionsQuery,
    useGetPatientOptionsQuery,
    useGetPayerOptionsQuery,
    useGetVisitNoOptionsQuery,
    useGetSepNoOptionsQuery,
    useCreateManualMedicalFeeMutation,
} from '@/store/api/medicalFee/medicalFeeApiSlice';
import { toastMessage } from '@/utils/toastUtils';
import ConfirmationModal from '@/components/ConfirmationModal';
import { FilePlus, Save, ArrowLeft, Loader2 } from 'lucide-react';

const selectStyles = {
    control: (base: any, state: any) => ({
        ...base,
        borderColor: state.isFocused ? '#4361ee' : '#e2e8f0',
        boxShadow: state.isFocused ? '0 0 0 2px rgba(67, 97, 238, 0.2)' : 'none',
        paddingTop: '2px',
        paddingBottom: '2px',
        '&:hover': { borderColor: '#cbd5e1' },
    }),
    menuPortal: (base: any) => ({ ...base, zIndex: 9999 }),
};

const selectErrorStyles = {
    ...selectStyles,
    control: (base: any, state: any) => ({
        ...selectStyles.control(base, state),
        borderColor: state.isFocused ? '#4361ee' : '#ef4444',
    }),
};

const ManualMedicalFeeForm = () => {
    const navigate = useNavigate();
    const dispatch = useDispatch();
    const { t } = useTranslation();
    const isRtl = useSelector((state: any) => state.themeConfig.rtlClass) === 'rtl';

    useEffect(() => {
        dispatch(setPageTitle('Input Jasa Medis Manual'));
        dispatch(setTitle('Input Jasa Medis Manual'));
        dispatch(setBreadcrumbTitle(['Dashboard', 'Jasa Medis', 'Posting', 'Input Manual']));
    }, [dispatch]);

    // Server-side search states
    const [doctorSearch, setDoctorSearch] = useState('');
    const [patientSearch, setPatientSearch] = useState('');
    const [payerSearch, setPayerSearch] = useState('');

    // API lookups - all server-side
    const { data: doctorOptions, isLoading: isLoadingDoctor } = useGetDoctorOptionsQuery(doctorSearch);
    const { data: patientOptions, isLoading: isLoadingPatient } = useGetPatientOptionsQuery(patientSearch);
    const { data: payerOptions, isLoading: isLoadingPayer } = useGetPayerOptionsQuery(payerSearch);
    const [createManual, { isLoading: isSubmitting }] = useCreateManualMedicalFeeMutation();

    // Form state
    const [form, setForm] = useState({
        doctor: null as any,
        patient: null as any,
        visitNo: '',
        sepNo: '',
        procedureName: '',
        procedureDate: null as Date | null,
        medicalProcedure: null as any,
        payer: null as any,
        totalFee: '',
    });

    const [errors, setErrors] = useState<Record<string, string>>({});
    const [showConfirmModal, setShowConfirmModal] = useState(false);

    // Visit No & SEP No lookup - only fetch when patient is selected
    const selectedMedicalRecordNo = form.patient?.desc || '';
    const { data: visitNoOptions, isLoading: isLoadingVisitNo } = useGetVisitNoOptionsQuery(
        selectedMedicalRecordNo,
        { skip: !selectedMedicalRecordNo }
    );
    const { data: sepNoOptions, isLoading: isLoadingSepNo } = useGetSepNoOptionsQuery(
        selectedMedicalRecordNo,
        { skip: !selectedMedicalRecordNo }
    );

    const medicalProcedureOptions = [
        { value: 'Rawat Jalan', label: 'Rawat Jalan' },
        { value: 'Rawat Inap', label: 'Rawat Inap' },
    ];

    // When patient selected, reset visitNo
    const handlePatientChange = (val: any) => {
        setForm(prev => ({
            ...prev,
            patient: val,
            visitNo: '', // reset when patient changes
            sepNo: '',
        }));
        if (val) {
            setErrors(prev => ({ ...prev, patient: '' }));
        }
    };

    const validate = (): boolean => {
        const errs: Record<string, string> = {};
        if (!form.doctor) errs.doctor = 'Pilih dokter';
        if (!form.patient) errs.patient = 'Pilih pasien';
        if (!form.procedureName.trim()) errs.procedureName = 'Nama tindakan wajib diisi';
        if (!form.procedureDate) errs.procedureDate = 'Tanggal tindakan wajib diisi';
        if (!form.medicalProcedure) errs.medicalProcedure = 'Pilih jenis pelayanan';
        if (!form.visitNo.trim()) errs.visitNo = 'No Registrasi wajib diisi';
        if (!form.payer) errs.payer = 'Pilih cara bayar / asuransi';
        const feeNum = parseInt(form.totalFee.replace(/[^\d]/g, ''), 10) || 0;
        if (!form.totalFee || feeNum <= 0) errs.totalFee = 'Nominal harus lebih dari 0';
        setErrors(errs);
        return Object.keys(errs).length === 0;
    };

    const handleSubmitClick = () => {
        if (!validate()) return;
        setShowConfirmModal(true);
    };

    const handleConfirmSubmit = async () => {
        try {
            const feeNum = parseInt(form.totalFee.replace(/[^\d]/g, ''), 10) || 0;
            await createManual({
                doctorId: 0,
                doctorCode: form.doctor.desc || form.doctor.value,
                doctorName: form.doctor.label?.replace(/^[^\-]+ - /, '') || form.doctor.label,
                patientName: form.patient?.name || form.patient?.label?.replace(/^[^\-]+ - /, '') || null,
                medicalRecordNo: form.patient?.desc || null,
                sepNo: form.sepNo || null,
                visitNo: form.visitNo || null,
                procedureName: form.procedureName,
                procedureDate: form.procedureDate
                    ? new Date(Date.UTC(form.procedureDate.getFullYear(), form.procedureDate.getMonth(), form.procedureDate.getDate())).toISOString()
                    : null,
                medicalProcedure: form.medicalProcedure?.value || null,
                payerCode: form.payer?.desc || form.payer?.value || null,
                payerName: form.payer?.label?.replace(/^[^\-]+ - /, '') || form.payer?.label || null,
                totalFee: feeNum,
            }).unwrap();

            toastMessage('Data jasa medis berhasil disimpan', 'success');
            setShowConfirmModal(false);
            navigate('/jasa-medis/posting');
        } catch (error: any) {
            toastMessage(error?.data?.message || 'Gagal menyimpan data', 'error');
            setShowConfirmModal(false);
        }
    };

    const formatNumber = (value: string) => {
        const num = value.replace(/[^\d]/g, '');
        return num ? new Intl.NumberFormat('id-ID').format(parseInt(num)) : '';
    };

    return (
        <div className="flex flex-col gap-6 max-w-5xl mx-auto">
            {/* Header */}
            <div className="flex items-center justify-between">
                <div>
                    <h1 className="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Input Jasa Medis Manual</h1>
                    <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">Tambah data tindakan dokter yang tidak terinput otomatis dari SIMRS</p>
                </div>
                <Button
                    type="button"
                    variant="outline"
                    onClick={() => navigate('/jasa-medis/posting')}
                    className="gap-2 shadow-sm bg-white dark:bg-black"
                    size="sm"
                >
                    <ArrowLeft className="h-4 w-4" />
                    Kembali
                </Button>
            </div>

            {/* Section 1: Informasi Dokter & Pasien */}
            <Card className="shadow-sm border border-gray-200 dark:border-gray-800">
                <CardHeader className="bg-gray-50/50 dark:bg-[#1b2e4b] border-b border-gray-100 dark:border-gray-700 px-6 py-4">
                    <CardTitle className="text-base font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                        <span className="bg-primary/10 text-primary w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold">1</span>
                        Informasi Dokter & Pasien
                    </CardTitle>
                </CardHeader>
                <CardContent className="p-6">
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {/* Dokter */}
                        <div className="space-y-2 md:col-span-2">
                            <Label className="text-gray-700 dark:text-gray-300 font-medium">Dokter <span className="text-red-500">*</span></Label>
                            <SelectSearch
                                options={doctorOptions?.data || []}
                                value={form.doctor}
                                onInputChange={(val) => setDoctorSearch(val)}
                                onChange={(val: any) => {
                                    setForm({ ...form, doctor: val });
                                    if (val) setErrors(prev => ({ ...prev, doctor: '' }));
                                }}
                                isClearable
                                isLoading={isLoadingDoctor}
                                placeholder="Ketik nama atau kode dokter..."
                                noOptionsMessage={() => doctorSearch.length < 1 ? 'Ketik untuk mencari...' : 'Tidak ditemukan'}
                                menuPortalTarget={document.body}
                                styles={errors.doctor ? selectErrorStyles : selectStyles}
                            />
                            {errors.doctor && <p className="text-xs text-red-500 flex items-center gap-1"><span className="w-1 h-1 bg-red-500 rounded-full inline-block" />{errors.doctor}</p>}
                        </div>

                        {/* Pasien */}
                        <div className="space-y-2">
                            <Label className="text-gray-700 dark:text-gray-300 font-medium">Pasien <span className="text-red-500">*</span></Label>
                            <SelectSearch
                                options={patientOptions?.data || []}
                                value={form.patient}
                                onInputChange={(val) => setPatientSearch(val)}
                                onChange={handlePatientChange}
                                isClearable
                                isLoading={isLoadingPatient}
                                placeholder="Ketik nama atau no RM..."
                                noOptionsMessage={() => patientSearch.length < 1 ? 'Ketik untuk mencari...' : 'Tidak ditemukan'}
                                menuPortalTarget={document.body}
                                styles={errors.patient ? selectErrorStyles : selectStyles}
                            />
                            {errors.patient && <p className="text-xs text-red-500 flex items-center gap-1"><span className="w-1 h-1 bg-red-500 rounded-full inline-block" />{errors.patient}</p>}
                        </div>

                        {/* No Registrasi / Visit No */}
                        <div className="space-y-2">
                            <Label className="text-gray-700 dark:text-gray-300 font-medium">No Registrasi <span className="text-red-500">*</span></Label>
                            <CreatableSelect
                                options={visitNoOptions?.data || []}
                                value={form.visitNo ? { value: form.visitNo, label: form.visitNo } : null}
                                onChange={(val: any) => {
                                    const v = val?.value || '';
                                    setForm(prev => ({ ...prev, visitNo: v }));
                                    if (v.trim()) setErrors(prev => ({ ...prev, visitNo: '' }));
                                }}
                                onCreateOption={(inputValue) => {
                                    setForm(prev => ({ ...prev, visitNo: inputValue }));
                                    if (inputValue.trim()) setErrors(prev => ({ ...prev, visitNo: '' }));
                                }}
                                isClearable
                                isDisabled={!form.patient}
                                isLoading={isLoadingVisitNo}
                                placeholder={form.patient ? 'Pilih atau ketik no registrasi...' : 'Pilih pasien terlebih dahulu'}
                                noOptionsMessage={() => 'Ketik untuk input manual'}
                                formatCreateLabel={(inputValue) => `Input: "${inputValue}"`}
                                menuPortalTarget={document.body}
                                styles={errors.visitNo ? selectErrorStyles : selectStyles}
                            />
                            {errors.visitNo && <p className="text-xs text-red-500 flex items-center gap-1"><span className="w-1 h-1 bg-red-500 rounded-full inline-block" />{errors.visitNo}</p>}
                        </div>

                        {/* No SEP */}
                        <div className="space-y-2">
                            <Label className="text-gray-700 dark:text-gray-300 font-medium">No SEP <span className="text-gray-400 font-normal text-xs ml-1">(opsional)</span></Label>
                            <CreatableSelect
                                options={sepNoOptions?.data || []}
                                value={form.sepNo ? { value: form.sepNo, label: form.sepNo } : null}
                                onChange={(val: any) => setForm(prev => ({ ...prev, sepNo: val?.value || '' }))}
                                onCreateOption={(inputValue) => setForm(prev => ({ ...prev, sepNo: inputValue }))}
                                isClearable
                                isDisabled={!form.patient}
                                isLoading={isLoadingSepNo}
                                placeholder={form.patient ? 'Pilih atau ketik no SEP...' : 'Pilih pasien terlebih dahulu'}
                                noOptionsMessage={() => 'Ketik untuk input manual'}
                                formatCreateLabel={(inputValue) => `Input: "${inputValue}"`}
                                menuPortalTarget={document.body}
                                styles={selectStyles}
                            />
                        </div>
                    </div>
                </CardContent>
            </Card>

            {/* Section 2: Detail Tindakan */}
            <Card className="shadow-sm border border-gray-200 dark:border-gray-800">
                <CardHeader className="bg-gray-50/50 dark:bg-[#1b2e4b] border-b border-gray-100 dark:border-gray-700 px-6 py-4">
                    <CardTitle className="text-base font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                        <span className="bg-primary/10 text-primary w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold">2</span>
                        Detail Tindakan
                    </CardTitle>
                </CardHeader>
                <CardContent className="p-6">
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {/* Nama Tindakan */}
                        <div className="space-y-2 md:col-span-2">
                            <Label className="text-gray-700 dark:text-gray-300 font-medium">Nama Tindakan <span className="text-red-500">*</span></Label>
                            <Input
                                placeholder="Contoh: Konsultasi Spesialis, Operasi Minor, Visite, dll"
                                value={form.procedureName}
                                onChange={(e) => {
                                    setForm({ ...form, procedureName: e.target.value });
                                    if (e.target.value.trim()) setErrors(prev => ({ ...prev, procedureName: '' }));
                                }}
                                className={`transition-all ${errors.procedureName ? 'border-red-500 focus:ring-red-200' : 'focus:ring-primary/20'}`}
                            />
                            {errors.procedureName && <p className="text-xs text-red-500 flex items-center gap-1"><span className="w-1 h-1 bg-red-500 rounded-full inline-block" />{errors.procedureName}</p>}
                        </div>

                        {/* Tanggal Tindakan */}
                        <div className="space-y-2">
                            <Label className="text-gray-700 dark:text-gray-300 font-medium">Tanggal Tindakan <span className="text-red-500">*</span></Label>
                            <div className="relative">
                                <Flatpickr
                                    options={{
                                        dateFormat: 'd F Y',
                                        position: isRtl ? 'auto right' : 'auto left',
                                    }}
                                    className={`form-input w-full pl-10 transition-all ${errors.procedureDate ? 'border-red-500 focus:ring-red-200' : 'focus:ring-primary/20'}`}
                                    value={form.procedureDate || undefined}
                                    onChange={(dates: Date[]) => {
                                        if (dates[0]) {
                                            setForm({ ...form, procedureDate: dates[0] });
                                            setErrors(prev => ({ ...prev, procedureDate: '' }));
                                        }
                                    }}
                                    placeholder="Pilih tanggal tindakan"
                                />
                                <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                    <i className="calendar-icon">📅</i>
                                </div>
                            </div>
                            {errors.procedureDate && <p className="text-xs text-red-500 flex items-center gap-1"><span className="w-1 h-1 bg-red-500 rounded-full inline-block" />{errors.procedureDate}</p>}
                        </div>

                        {/* Jenis Pelayanan */}
                        <div className="space-y-2">
                            <Label className="text-gray-700 dark:text-gray-300 font-medium">Jenis Pelayanan <span className="text-red-500">*</span></Label>
                            <SelectSearch
                                options={medicalProcedureOptions}
                                value={form.medicalProcedure}
                                onChange={(val: any) => {
                                    setForm({ ...form, medicalProcedure: val });
                                    if (val) setErrors(prev => ({ ...prev, medicalProcedure: '' }));
                                }}
                                isClearable
                                placeholder="Rawat Jalan / Rawat Inap"
                                menuPortalTarget={document.body}
                                styles={errors.medicalProcedure ? selectErrorStyles : selectStyles}
                            />
                            {errors.medicalProcedure && <p className="text-xs text-red-500 flex items-center gap-1"><span className="w-1 h-1 bg-red-500 rounded-full inline-block" />{errors.medicalProcedure}</p>}
                        </div>

                        {/* Cara Bayar / Payer (Asuransi) */}
                        <div className="space-y-2 md:col-span-2">
                            <Label className="text-gray-700 dark:text-gray-300 font-medium">Cara Bayar / Asuransi <span className="text-red-500">*</span></Label>
                            <SelectSearch
                                options={payerOptions?.data || []}
                                value={form.payer}
                                onInputChange={(val) => setPayerSearch(val)}
                                onChange={(val: any) => {
                                    setForm({ ...form, payer: val });
                                    if (val) setErrors(prev => ({ ...prev, payer: '' }));
                                }}
                                isClearable
                                isLoading={isLoadingPayer}
                                placeholder="Ketik nama asuransi / cara bayar..."
                                noOptionsMessage={() => payerSearch.length < 1 ? 'Ketik untuk mencari...' : 'Tidak ditemukan'}
                                menuPortalTarget={document.body}
                                styles={errors.payer ? selectErrorStyles : selectStyles}
                            />
                            {errors.payer && <p className="text-xs text-red-500 flex items-center gap-1"><span className="w-1 h-1 bg-red-500 rounded-full inline-block" />{errors.payer}</p>}
                        </div>
                    </div>
                </CardContent>
            </Card>

            {/* Section 3: Nominal */}
            <Card className="shadow-sm border border-gray-200 dark:border-gray-800">
                <CardHeader className="bg-gray-50/50 dark:bg-[#1b2e4b] border-b border-gray-100 dark:border-gray-700 px-6 py-4">
                    <CardTitle className="text-base font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                        <span className="bg-primary/10 text-primary w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold">3</span>
                        Nominal Jasa Medis
                    </CardTitle>
                </CardHeader>
                <CardContent className="p-6">
                    <div className="max-w-md">
                        <div className="space-y-2">
                            <Label className="text-gray-700 dark:text-gray-300 font-medium">Total Fee (Rp) <span className="text-red-500">*</span></Label>
                            <div className="relative">
                                <span className="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 font-medium">Rp</span>
                                <Input
                                    placeholder="0"
                                    value={form.totalFee}
                                    onChange={(e) => {
                                        const formatted = formatNumber(e.target.value);
                                        setForm({ ...form, totalFee: formatted });
                                        if (formatted) setErrors(prev => ({ ...prev, totalFee: '' }));
                                    }}
                                    className={`pl-8 text-right font-mono text-lg transition-all ${errors.totalFee ? 'border-red-500 focus:ring-red-200' : 'focus:ring-primary/20'}`}
                                />
                            </div>
                            {errors.totalFee && <p className="text-xs text-red-500 flex items-center gap-1"><span className="w-1 h-1 bg-red-500 rounded-full inline-block" />{errors.totalFee}</p>}
                        </div>
                    </div>
                </CardContent>
            </Card>

            {/* Action Buttons */}
            <div className="flex justify-end gap-3">
                <Button
                    type="button"
                    variant="outline"
                    onClick={() => navigate('/jasa-medis/posting')}
                    className="shadow-sm"
                >
                    Batal
                </Button>
                <Button
                    type="button"
                    className="gap-2 shadow-sm"
                    onClick={handleSubmitClick}
                >
                    <Save className="w-4 h-4" />
                    Simpan Data
                </Button>
            </div>

            <ConfirmationModal
                isOpen={showConfirmModal}
                onClose={() => !isSubmitting && setShowConfirmModal(false)}
                onConfirm={handleConfirmSubmit}
                title="Konfirmasi Simpan"
                message="Apakah Anda yakin ingin menyimpan data jasa medis ini?"
                confirmText="Ya, Simpan"
                cancelText="Batal"
                loading={isSubmitting}
                variant="primary"
                keepOpenOnConfirm
            />
        </div>
    );
};

export default ManualMedicalFeeForm;
