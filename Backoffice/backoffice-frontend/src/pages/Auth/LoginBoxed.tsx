import { useNavigate, Link } from 'react-router-dom';
import { useDispatch } from 'react-redux';
import { useEffect } from 'react';
import { setPageTitle } from '../../store/themeConfigSlice';
import { useTranslation } from 'react-i18next';
import { useLoginMutation } from "@/store/api/auth/authApiSlice";
import { useForm, SubmitHandler } from 'react-hook-form';
import { yupResolver } from '@hookform/resolvers/yup';
import * as yup from 'yup';
import { setUser } from "@/store/api/auth/authSlice";
import { LoginRequest } from '@/types/apiType';
import { Building2, TrendingUp, Wallet, Shield, Mail, Lock } from 'lucide-react';

// shadcn/ui components
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader } from '@/components/ui/card';

const LoginBoxed = () => {
    const [login, { error, isLoading }] = useLoginMutation();
    const dispatch = useDispatch();

    const { t } = useTranslation();

    const schema = yup.object({
        email: yup.string().email(t('validation.email_invalid')).required(t('validation.required')),
        password: yup.string().required(t('validation.required')),
    }).required();

    useEffect(() => {
        dispatch(setPageTitle(t('auth.title')));
    }, [dispatch, t]);

    const { register, formState: { errors }, handleSubmit } = useForm<LoginRequest>({
        resolver: yupResolver(schema), mode: "all",
    });

    const navigate = useNavigate();

    const getErrorMessage = (err: any) => {
        try {
            if (!err) return "Terjadi kesalahan pada server.";

            // Jika error berupa string langsung
            if (typeof err === 'string') return err;

            // Jika error memiliki struktur RTK Query
            if (err && typeof err === 'object') {
                const data = (err as any).data;

                // 400 dari ASP.NET Core: {"errors":{"Email":["..."],"Password":["..."]}}
                // Tampilkan apa adanya agar penyebab (field salah / body bukan JSON) langsung terlihat.
                if (data && typeof data === 'object' && data.errors) {
                    const fieldErrors = Object.entries(data.errors as Record<string, unknown>)
                        .map(([field, msgs]) => `${field}: ${Array.isArray(msgs) ? msgs.join(', ') : String(msgs)}`)
                        .join(' | ');
                    if (fieldErrors) {
                        return `Validasi gagal (HTTP ${(err as any).status ?? 400}) — ${fieldErrors}`;
                    }
                }
                if (data && typeof data === 'object' && 'message' in data) {
                    return String((data as any).message);
                }
                if (data && typeof data === 'string' && data.trim()) {
                    // Respons HTML dari nginx (mis. 405 Not Allowed saat URL terkirim relatif)
                    const plain = data.replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').trim();
                    return `HTTP ${(err as any).status}: ${plain.slice(0, 160)}`;
                }
                if ('status' in err && err.status === 404) {
                    return "Endpoint login (Auth/login) tidak ditemukan di server. Periksa VITE_HOST / base URL API.";
                }
                if ('status' in err && err.status === 405) {
                    return "HTTP 405: request login dikirim ke web server frontend, bukan ke API. Periksa base URL (harus https://api.rsthb.id).";
                }
                if ('status' in err && err.status === 401) {
                    return "Email atau password salah.";
                }
                if ('status' in err && err.status === 'FETCH_ERROR') {
                    return "Tidak dapat menghubungi API. Periksa koneksi / CORS ke https://api.rsthb.id.";
                }
                if ('error' in err && err.error && typeof err.error === 'string') {
                    return err.error;
                }
            }

            return "Gagal melakukan login. Periksa kembali email/password Anda.";
        } catch (e) {
            return "Gagal melakukan login. Periksa kembali email/password Anda.";
        }
    };

    const submitForm: SubmitHandler<LoginRequest> = async (request: LoginRequest) => {
        // Kirim HANYA field yang dikenal backend (AuthenticateRequest: Email, Password).
        // Tanpa langkah ini, field tambahan dari form dapat membuat model binding gagal.
        const payload: LoginRequest = {
            email: String(request?.email ?? '').trim(),
            password: String(request?.password ?? ''),
        };

        try {
            const response = await login(payload);
            if ('data' in response) {
                // Backend membungkus: { message, data: AuthenticateResponse }
                const userData = (response.data as any)?.data ?? response.data;

                // setUser() menyimpan token + profile (single JSON.stringify) ke localStorage
                // SEKALIGUS mengisi Redux dengan role/accessList yang sudah dinormalisasi,
                // sehingga DefaultLayout tidak sempat melempar user ke /login setelah redirect.
                dispatch(setUser(userData));

                // Role bisa berupa roleID (login) maupun roleId (profile) — normalisasi di authSlice.
                const roleID = Number(userData?.role?.roleID ?? userData?.role?.roleId ?? userData?.roleId);

                let redirectUrl = '/';
                if (roleID === 4) {
                    redirectUrl = '/staff-dashboard';
                } else if (roleID === 5) {
                    redirectUrl = '/petty-cash/dashboard';
                }

                window.location.href = redirectUrl;
            } else {
                // Tangkap request/response persis untuk diagnosis HTTP 400.
                console.error('[login] gagal', {
                    status: (response.error as any)?.status,
                    data: (response.error as any)?.data,
                });
            }
        } catch (err) {
            console.error('[login] exception', err);
        }
    };

    return (
        <div className="min-h-screen flex">
            {/* LEFT SIDE - Illustration & Branding */}
            <div className="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-primary via-primary to-[#4a7bd4] relative overflow-hidden">
              {/* Decorative circles */}
                <div className="absolute top-20 left-20 w-72 h-72 bg-white/10 rounded-full blur-3xl"></div>
                <div className="absolute bottom-20 right-20 w-96 h-96 bg-white/5 rounded-full blur-3xl"></div>
                <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>

                <div className="relative z-10 flex flex-col items-center justify-center w-full px-12">
                    {/* Logo */}
                    <div className="mb-8">
                        <img
                            src="/assets/images/grahakara_on black.png"
                            alt="Logo"
                            className="w-48 h-auto drop-shadow-2xl"
                        />
                    </div>

                    {/* Illustration */}
                    <div className="mb-8">
                        <img
                            src="/assets/images/hospital_finance_illustration.png"
                            alt="Hospital Finance"
                            className="w-80 h-auto rounded-2xl shadow-2xl"
                        />
                    </div>

                    {/* Feature highlights */}
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

            {/* RIGHT SIDE - Login Form */}
            <div className="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12 bg-gradient-to-br from-gray-50">
                <div className="w-full max-w-md">
                    {/* Mobile logo */}
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
                                {t('auth.welcome_title')}
                            </h1>
                            <p className="text-gray-500 text-sm">
                                {t('auth.welcome_desc')}
                            </p>
                        </CardHeader>

                        <CardContent className="pt-6 pb-8 px-8">
                            <form className="space-y-5" onSubmit={handleSubmit(submitForm)}>
                                <div className="space-y-2">
                                    <Label htmlFor="Email" className="text-gray-700">
                                        {t('auth.email')}
                                    </Label>
                                    <div className="relative">
                                        <Mail className="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" />
                                        <Input
                                            id="Email"
                                            type="email"
                                            {...register('email')}
                                            placeholder={t('auth.email_placeholder')}
                                            className="pl-10 h-12 border-gray-200 focus:border-primary"
                                        />
                                    </div>
                                    {errors.email && (
                                        <span className="text-sm text-danger">{errors.email.message}</span>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="Password" className="text-gray-700">
                                        {t('auth.password')}
                                    </Label>
                                    <div className="relative">
                                        <Lock className="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" />
                                        <Input
                                            id="Password"
                                            type="password"
                                            {...register('password')}
                                            placeholder={t('auth.password_placeholder')}
                                            className="pl-10 h-12 border-gray-200 focus:border-primary"
                                        />
                                    </div>
                                    {errors.password && (
                                        <span className="text-sm text-danger">{errors.password.message}</span>
                                    )}
                                    <div className="flex justify-end mt-2">
                                        <Link to="/auth/forgot-password" className="text-sm text-primary hover:underline">
                                            {t('auth.forgot_password')}
                                        </Link>
                                    </div>
                                </div>

                                <Button
                                    disabled={isLoading}
                                    type="submit"
                                    className="w-full h-12 text-base font-semibold mt-6"
                                >
                                    {isLoading ? "Loading..." : t('auth.sign_in')}
                                </Button>

                                {error && (
                                    <div className="p-3 bg-danger/10 border border-danger/20 rounded-lg">
                                        <span className="text-sm text-danger">{getErrorMessage(error)}</span>
                                    </div>
                                )}
                            </form>

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

export default LoginBoxed;
