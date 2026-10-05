import { useEffect, useState, useRef } from 'react';
import { useTranslation } from 'react-i18next';
import { useDispatch } from 'react-redux';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Camera, Key, Loader2, User, Trash2 } from 'lucide-react';
import { toastMessage } from '@/utils/toastUtils';
import {
    useGetProfileQuery,
    useUpdateProfileMutation,
    useChangePasswordMutation,
    useUploadProfilePhotoMutation,
    useDeleteProfilePhotoMutation
} from '@/store/api/auth/authApiSlice';
import { updateUserProfile } from '@/store/api/auth/authSlice';

const ProfileTab = () => {
    const { t } = useTranslation();
    const dispatch = useDispatch();
    const [isSaving, setIsSaving] = useState(false);
    const [isChangingPassword, setIsChangingPassword] = useState(false);
    const [isUploading, setIsUploading] = useState(false);
    const [isDeleting, setIsDeleting] = useState(false);
    const fileInputRef = useRef<HTMLInputElement>(null);

    const { data: profileData, isLoading: isLoadingProfile, refetch } = useGetProfileQuery();
    const [updateProfile] = useUpdateProfileMutation();
    const [changePassword] = useChangePasswordMutation();
    const [uploadProfilePhoto] = useUploadProfilePhotoMutation();
    const [deleteProfilePhoto] = useDeleteProfilePhotoMutation();

    // Profile form state
    const [profile, setProfile] = useState({
        displayName: '',
        email: '',
        phone: '',
        profilePicture: '',
    });

    // Password form state
    const [passwordForm, setPasswordForm] = useState({
        currentPassword: '',
        newPassword: '',
        confirmPassword: '',
    });

    useEffect(() => {
        if (profileData) {
            setProfile({
                displayName: profileData.displayName || '',
                email: profileData.email || '',
                phone: profileData.phone || '',
                profilePicture: profileData.profilePicture || '',
            });
        }
    }, [profileData]);

    const handleProfileChange = (field: string, value: string) => {
        setProfile((prev) => ({ ...prev, [field]: value }));
    };

    const handlePasswordChange = (field: string, value: string) => {
        setPasswordForm((prev) => ({ ...prev, [field]: value }));
    };

    const handlePhotoClick = () => {
        fileInputRef.current?.click();
    };

    const handlePhotoUpload = async (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0];
        if (!file) return;

        // Validate file
        if (file.size > 2 * 1024 * 1024) {
            toastMessage(t('validation.file_size_max'), 'error');
            return;
        }

        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            toastMessage(t('validation.file_type_image'), 'error');
            return;
        }

        setIsUploading(true);
        try {
            const formData = new FormData();
            formData.append('file', file);

            const result = await uploadProfilePhoto(formData).unwrap();
            setProfile((prev) => ({ ...prev, profilePicture: result.url }));
            toastMessage(t('messages.upload_photo_success'), 'success');
            refetch();
        } catch (error: any) {
            toastMessage(error?.data?.message || t('messages.upload_photo_error'), 'error');
        } finally {
            setIsUploading(false);
        }
    };

    const handleSaveProfile = async () => {
        setIsSaving(true);
        try {
            await updateProfile({
                displayName: profile.displayName,
                email: profile.email,
                phone: profile.phone,
                profilePicture: profile.profilePicture,
            }).unwrap();

            // Update Redux store - this will instantly update header
            dispatch(updateUserProfile({
                displayName: profile.displayName,
                email: profile.email,
                profilePicture: profile.profilePicture,
            }));

            toastMessage(t('messages.save_success'), 'success');
            refetch();
        } catch (error: any) {
            toastMessage(error?.data?.message || t('messages.update_error'), 'error');
        } finally {
            setIsSaving(false);
        }
    };

    const handleChangePassword = async () => {
        if (passwordForm.newPassword !== passwordForm.confirmPassword) {
            toastMessage(t('messages.password_mismatch'), 'error');
            return;
        }

        if (passwordForm.newPassword.length < 6) {
            toastMessage(t('messages.password_min_length'), 'error');
            return;
        }

        setIsChangingPassword(true);
        try {
            await changePassword({
                currentPassword: passwordForm.currentPassword,
                newPassword: passwordForm.newPassword,
                confirmPassword: passwordForm.confirmPassword,
            }).unwrap();
            toastMessage(t('messages.password_change_success'), 'success');
            setPasswordForm({ currentPassword: '', newPassword: '', confirmPassword: '' });
        } catch (error: any) {
            toastMessage(error?.data?.message || t('messages.password_change_error'), 'error');
        } finally {
            setIsChangingPassword(false);
        }
    };

    const getProfileImageUrl = () => {
        return profile.profilePicture || null;
    };

    if (isLoadingProfile) {
        return (
            <div className="flex items-center justify-center h-48">
                <Loader2 className="h-8 w-8 animate-spin text-primary" />
            </div>
        );
    }

    return (
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {/* Profile Info Card */}
            <Card className="border border-gray-100 shadow-sm rounded-xl">
                <CardHeader className="border-b border-gray-100 bg-gray-50/50">
                    <CardTitle className="text-lg font-semibold flex items-center gap-2">
                        <User className="h-5 w-5 text-primary" />
                        {t('profile_settings.title')}
                    </CardTitle>
                </CardHeader>
                <CardContent className="p-6 space-y-5">
                    {/* Profile Picture */}
                    <div className="flex items-center gap-4">
                        <div className="relative">
                            <div className="w-20 h-20 rounded-full bg-gradient-to-br from-primary/20 to-primary/40 flex items-center justify-center overflow-hidden">
                                {profile.profilePicture ? (
                                    <img
                                        src={getProfileImageUrl() || ''}
                                        alt="Profile"
                                        className="w-full h-full object-cover"
                                    />
                                ) : (
                                    <User className="h-10 w-10 text-primary" />
                                )}
                            </div>
                            <button
                                onClick={handlePhotoClick}
                                disabled={isUploading}
                                className="absolute bottom-0 right-0 w-7 h-7 bg-primary rounded-full flex items-center justify-center shadow-lg hover:bg-primary/90 transition-colors disabled:opacity-50"
                                title={t('profile_settings.upload_photo')}
                            >
                                {isUploading ? (
                                    <Loader2 className="h-3.5 w-3.5 text-white animate-spin" />
                                ) : (
                                    <Camera className="h-3.5 w-3.5 text-white" />
                                )}
                            </button>
                            <input
                                ref={fileInputRef}
                                type="file"
                                accept="image/jpeg,image/png,image/gif,image/webp"
                                className="hidden"
                                onChange={handlePhotoUpload}
                            />
                        </div>
                        <div className="flex-1">
                            <p className="font-medium text-gray-800">{t('profile_settings.profile_picture')}</p>
                            <p className="text-sm text-gray-500 mb-2">{t('profile_settings.photo_guideline')}</p>
                            {profile.profilePicture && (
                                <button
                                    onClick={async () => {
                                        setIsDeleting(true);
                                        try {
                                            await deleteProfilePhoto().unwrap();
                                            setProfile((prev) => ({ ...prev, profilePicture: '' }));
                                            dispatch(updateUserProfile({
                                                displayName: profile.displayName,
                                                email: profile.email,
                                                profilePicture: undefined,
                                            }));
                                            toastMessage(t('messages.delete_photo_success'), 'success');
                                            refetch();
                                        } catch (error: any) {
                                            toastMessage(error?.data?.message || t('messages.delete_photo_error'), 'error');
                                        } finally {
                                            setIsDeleting(false);
                                        }
                                    }}
                                    disabled={isDeleting}
                                    className="text-xs text-red-500 hover:text-red-600 flex items-center gap-1 disabled:opacity-50"
                                >
                                    {isDeleting ? (
                                        <Loader2 className="h-3 w-3 animate-spin" />
                                    ) : (
                                        <Trash2 className="h-3 w-3" />
                                    )}
                                    {t('profile_settings.delete_photo')}
                                </button>
                            )}
                        </div>
                    </div>

                    {/* Display Name */}
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1.5">
                            {t('profile_settings.display_name')}
                        </label>
                        <input
                            type="text"
                            className="form-input w-full rounded-lg border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary"
                            placeholder={t('profile_settings.enter_display_name')}
                            value={profile.displayName}
                            onChange={(e) => handleProfileChange('displayName', e.target.value)}
                        />
                    </div>

                    {/* Email */}
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1.5">
                            {t('profile_settings.email')}
                        </label>
                        <input
                            type="email"
                            className="form-input w-full rounded-lg border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary"
                            placeholder={t('profile_settings.enter_email')}
                            value={profile.email}
                            onChange={(e) => handleProfileChange('email', e.target.value)}
                        />
                    </div>

                    {/* Phone */}
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1.5">
                            {t('profile_settings.phone')}
                        </label>
                        <input
                            type="tel"
                            className="form-input w-full rounded-lg border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary"
                            placeholder={t('profile_settings.enter_phone')}
                            value={profile.phone}
                            onChange={(e) => handleProfileChange('phone', e.target.value)}
                        />
                    </div>

                    <Button
                        onClick={handleSaveProfile}
                        disabled={isSaving}
                        className="w-full bg-primary hover:bg-primary/90"
                    >
                        {isSaving ? (
                            <>
                                <Loader2 className="h-4 w-4 mr-2 animate-spin" />
                                {t('profile_settings.saving')}
                            </>
                        ) : (
                            t('profile_settings.save_profile')
                        )}
                    </Button>
                </CardContent>
            </Card>

            {/* Change Password Card */}
            <Card className="border border-gray-100 shadow-sm rounded-xl">
                <CardHeader className="border-b border-gray-100 bg-gray-50/50">
                    <CardTitle className="text-lg font-semibold flex items-center gap-2">
                        <Key className="h-5 w-5 text-orange-500" />
                        {t('profile_settings.change_password')}
                    </CardTitle>
                </CardHeader>
                <CardContent className="p-6 space-y-5">
                    {/* Current Password */}
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1.5">
                            {t('profile_settings.current_password')}
                        </label>
                        <input
                            type="password"
                            className="form-input w-full rounded-lg border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary"
                            placeholder={t('profile_settings.enter_current_password')}
                            value={passwordForm.currentPassword}
                            onChange={(e) => handlePasswordChange('currentPassword', e.target.value)}
                        />
                    </div>

                    {/* New Password */}
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1.5">
                            {t('profile_settings.new_password')}
                        </label>
                        <input
                            type="password"
                            className="form-input w-full rounded-lg border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary"
                            placeholder={t('validation.password_min')}
                            value={passwordForm.newPassword}
                            onChange={(e) => handlePasswordChange('newPassword', e.target.value)}
                        />
                    </div>

                    {/* Confirm Password */}
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1.5">
                            {t('profile_settings.confirm_password')}
                        </label>
                        <input
                            type="password"
                            className="form-input w-full rounded-lg border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary"
                            placeholder={t('profile_settings.enter_confirm_password')}
                            value={passwordForm.confirmPassword}
                            onChange={(e) => handlePasswordChange('confirmPassword', e.target.value)}
                        />
                    </div>

                    <Button
                        onClick={handleChangePassword}
                        disabled={isChangingPassword || !passwordForm.currentPassword || !passwordForm.newPassword}
                        variant="outline"
                        className="w-full border-orange-200 text-orange-600 hover:bg-orange-50"
                    >
                        {isChangingPassword ? (
                            <>
                                <Loader2 className="h-4 w-4 mr-2 animate-spin" />
                                {t('profile_settings.saving')}
                            </>
                        ) : (
                            t('profile_settings.change_password')
                        )}
                    </Button>
                </CardContent>
            </Card>
        </div>
    );
};

export default ProfileTab;
