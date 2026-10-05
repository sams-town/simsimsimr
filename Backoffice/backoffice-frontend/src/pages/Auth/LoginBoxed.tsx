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
import { setProfile, setToken } from "@/utils/auth";
import { LoginRequest } from '@/types/apiType';
import { FetchBaseQueryError } from '@reduxjs/toolkit/dist/query/fetchBaseQuery';
import { SerializedError } from '@reduxjs/toolkit';
import { Mail, Lock, Building2, TrendingUp, Wallet, Shield } from 'lucide-react';

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
    });

    const { register, formState: { errors }, handleSubmit } = useForm<LoginRequest>({
        resolver: yupResolver(schema), mode: "all",
    });

    const navigate = useNavigate();
    const submitForm: SubmitHandler<LoginRequest> = async (request: LoginRequest) => {
        const response = await login(request);
        if ('data' in response) {
            const userData = response.data.data;

            // Save to localStorage DIRECTLY before redirect
            // This ensures data is saved synchronously before page reload
            setProfile(userData);
            setToken(userData.token);

            // Also dispatch to Redux (for current session)
            dispatch(setUser(userData));

            // Determine redirect based on role
            // Staff (roleID: 4) goes to staff dashboard
            const isStaff = userData.role?.roleID === 4;
            const isPettyCashRole = userData.role?.roleID === 5;

            let redirectUrl = '/';
            if (isStaff) {
                redirectUrl = '/staff-dashboard';
            } else if (isPettyCashRole) {
                redirectUrl = '/petty-cash/dashboard';
            }

            // Redirect with full page reload
            window.location.href = redirectUrl;
        } else {
            console.error(response.error);
        }
    };

    const getErrorMessage = (error: FetchBaseQueryError | SerializedError) => {
        if ('status' in error) {
            const fetchError = error as FetchBaseQueryError;
            if ('data' in fetchError) {
                return (fetchError.data as any).message;
            }
            return fetchError.status;
        }
        return error.message;
    };

    return (
        <div className="min-h-screen flex">
            {/* LEFT SIDE - Illustration & Branding */}
            <div className="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-primary via-primary to-[#4a7bd4] relative overflow-hidden">
                {/* Decorative circles */}
                <div className="absolute top-20 left-20 w-72 h-72 bg-white/10 rounded-full blur-3xl"></div>
                <div className="absolute bottom-20 right-20 w-96 h-96 bg-white/5 rounded-full blur-3xl"></div>
                <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-white/10 rounded-full blur-2xl"></div>

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
            <div className="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12 bg-gradient-to-br from-gray-50 to-white">
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
                                    loading={isLoading}
                                >
                                    {t('auth.sign_in')}
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
