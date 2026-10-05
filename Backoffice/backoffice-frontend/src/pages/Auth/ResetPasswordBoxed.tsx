import { useNavigate, Link, useSearchParams } from 'react-router-dom';
import { useDispatch } from 'react-redux';
import { useEffect } from 'react';
import { setPageTitle } from '../../store/themeConfigSlice';
import { useTranslation } from 'react-i18next';
import { useResetPasswordMutation } from "@/store/api/auth/authApiSlice";
import { useForm, SubmitHandler } from 'react-hook-form';
import { yupResolver } from '@hookform/resolvers/yup';
import * as yup from 'yup';
import { ResetPasswordRequest } from '@/store/api/auth/authApiSlice';
import { Lock, ArrowLeft, Building2, TrendingUp, Wallet, Shield } from 'lucide-react';

// shadcn/ui components
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader } from '@/components/ui/card';

const ResetPasswordBoxed = () => {
    const [resetPassword, { error, isLoading, isSuccess }] = useResetPasswordMutation();
    const dispatch = useDispatch();
    const { t } = useTranslation();
    const [searchParams] = useSearchParams();
    const token = searchParams.get('token');

    const schema = yup.object({
        newPassword: yup.string().min(6, t('validation.password_min')).required(t('validation.required')),
        confirmPassword: yup.string()
            .oneOf([yup.ref('newPassword')], t('validation.password_mismatch'))
            .required(t('validation.required')),
    }).required();

    useEffect(() => {
        dispatch(setPageTitle("Reset Password"));
    });

    const { register, formState: { errors }, handleSubmit } = useForm<Omit<ResetPasswordRequest, 'token'>>({
        resolver: yupResolver(schema), mode: "all",
    });

    const submitForm: SubmitHandler<Omit<ResetPasswordRequest, 'token'>> = async (data) => {
        if (!token) return;
        await resetPassword({ ...data, token });
    };

    const getErrorMessage = (err: any) => {
        if (err && 'data' in err && typeof err.data === 'string') return err.data;
        if (err && 'data' in err && err.data.message) return err.data.message;
        return t('messages.save_error');
    };

    if (!token) {
        return (
            <div className="min-h-screen flex items-center justify-center bg-gray-50">
                <Card className="w-full max-w-md p-6 text-center">
                    <h2 className="text-xl font-bold text-red-600 mb-2">Token Invalid</h2>
                    <p className="text-gray-600 mb-4">Link reset password tidak valid atau sudah kadaluarsa.</p>
                    <Button asChild>
                        <Link to="/login">Kembali ke Login</Link>
                    </Button>
                </Card>
            </div>
        );
    }

    return (
        <div className="min-h-screen flex">
            {/* LEFT SIDE - Illustration & Branding */}
            <div className="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-primary via-primary to-[#4a7bd4] relative overflow-hidden">
                <div className="absolute top-20 left-20 w-72 h-72 bg-white/10 rounded-full blur-3xl"></div>
                <div className="absolute bottom-20 right-20 w-96 h-96 bg-white/5 rounded-full blur-3xl"></div>
                <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-white/10 rounded-full blur-2xl"></div>

                <div className="relative z-10 flex flex-col items-center justify-center w-full px-12">
                    <div className="mb-8">
                        <img
                            src="/assets/images/grahakara_on black.png"
                            alt="Logo"
                            className="w-48 h-auto drop-shadow-2xl"
                        />
                    </div>
                    <div className="mb-8">
                        <img
                            src="/assets/images/hospital_finance_illustration.png"
                            alt="Hospital Finance"
                            className="w-80 h-auto rounded-2xl shadow-2xl"
                        />
                    </div>
                    <div className="grid grid-cols-2 gap-4 mt-6">
                        <div className="flex items-center gap-3 text-white/90">
                            <div className="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                                <Building2 className="w-5 h-5" />
                            </div>
                            <span className="text-sm font-medium">{t('auth.features.integrated_accounting')}</span>
                        </div>
                        <div className="flex items-center gap-3 text-white/90">
                            <div className="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                                <TrendingUp className="w-5 h-5" />
                            </div>
                            <span className="text-sm font-medium">{t('auth.features.realtime_report')}</span>
                        </div>
                        <div className="flex items-center gap-3 text-white/90">
                            <div className="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                                <Wallet className="w-5 h-5" />
                            </div>
                            <span className="text-sm font-medium">{t('auth.features.cash_flow')}</span>
                        </div>
                        <div className="flex items-center gap-3 text-white/90">
                            <div className="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                                <Shield className="w-5 h-5" />
                            </div>
                            <span className="text-sm font-medium">{t('auth.features.security')}</span>
                        </div>
                    </div>
                </div>
            </div>

            {/* RIGHT SIDE */}
            <div className="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12 bg-gradient-to-br from-gray-50 to-white">
                <div className="w-full max-w-md">

                    <div className="lg:hidden flex justify-center mb-8">
                        <div className="p-4 bg-primary rounded-2xl shadow-lg shadow-primary/30">
                            <img
                                src="/assets/images/logo-thb-transparent.png"
                                alt="Logo"
                                className="w-12 h-12"
                            />
                        </div>
                    </div>

                    <Card className="border-0 shadow-xl shadow-gray-200/50">
                        <CardHeader className="text-center pb-2 pt-8">
                            <h1 className="text-3xl font-bold text-gray-900 mb-2">
                                Atur Ulang Kata Sandi
                            </h1>
                            <p className="text-gray-500 text-sm">
                                Masukkan kata sandi baru Anda
                            </p>
                        </CardHeader>

                        <CardContent className="pt-6 pb-8 px-8">
                            {isSuccess ? (
                                <div className="text-center space-y-4">
                                    <div className="p-4 bg-green-50 text-green-700 rounded-lg">
                                        Kata sandi Anda telah berhasil diubah.
                                    </div>
                                    <Button asChild className="w-full">
                                        <Link to="/login">Login Sekarang</Link>
                                    </Button>
                                </div>
                            ) : (
                                <form className="space-y-5" onSubmit={handleSubmit(submitForm)}>
                                    <div className="space-y-2">
                                        <Label htmlFor="newPassword" className="text-gray-700">
                                            Kata Sandi Baru
                                        </Label>
                                        <div className="relative">
                                            <Lock className="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" />
                                            <Input
                                                id="newPassword"
                                                type="password"
                                                {...register('newPassword')}
                                                placeholder="******"
                                                className="pl-10 h-12 border-gray-200 focus:border-primary"
                                            />
                                        </div>
                                        {errors.newPassword && (
                                            <span className="text-sm text-danger">{errors.newPassword.message}</span>
                                        )}
                                    </div>

                                    <div className="space-y-2">
                                        <Label htmlFor="confirmPassword" className="text-gray-700">
                                            Konfirmasi Kata Sandi
                                        </Label>
                                        <div className="relative">
                                            <Lock className="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" />
                                            <Input
                                                id="confirmPassword"
                                                type="password"
                                                {...register('confirmPassword')}
                                                placeholder="******"
                                                className="pl-10 h-12 border-gray-200 focus:border-primary"
                                            />
                                        </div>
                                        {errors.confirmPassword && (
                                            <span className="text-sm text-danger">{errors.confirmPassword.message}</span>
                                        )}
                                    </div>

                                    <Button
                                        disabled={isLoading}
                                        type="submit"
                                        className="w-full h-12 text-base font-semibold mt-6"
                                        loading={isLoading}
                                    >
                                        Ubah Kata Sandi
                                    </Button>

                                    {error && (
                                        <div className="p-3 bg-danger/10 border border-danger/20 rounded-lg">
                                            <span className="text-sm text-danger">{getErrorMessage(error)}</span>
                                        </div>
                                    )}
                                </form>
                            )}
                            {/* Footer */}
                            <div className="mt-8 pt-6 border-t border-gray-100 text-center">
                                <p className="text-xs text-gray-400">
                                    {t('auth.system_name')}
                                </p>
                                <p className="text-xs text-gray-400 mt-1">
                                    {t('auth.copyright')}
                                </p>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    );
};

export default ResetPasswordBoxed;
