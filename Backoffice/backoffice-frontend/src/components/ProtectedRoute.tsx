import { useEffect, useState } from 'react';
import { Navigate, useLocation } from 'react-router-dom';
import { useSelector } from 'react-redux';
import { checkAccess } from '@/store/api/auth/authSlice';
import { useGetProfileQuery } from '@/store/api/auth/authApiSlice';
import { getAuthenticated, getProfile } from '@/utils/auth';

interface ProtectedRouteProps {
    children: React.ReactNode;
    pageCode: string;
    action?: string;
    fallbackPath?: string;
}

/**
 * ProtectedRoute Component
 *
 * Protects routes based on user role and page access permissions.
 * If user doesn't have access, redirects to fallback path (default: /).
 *
 * Catatan penting:
 * - Keputusan redirect ditunda sampai profile selesai dimuat, supaya user yang BARU login
 *   (token sudah ada, tapi state Redux belum lengkap) tidak terlempar kembali ke /login.
 * - Frontend hanya menyembunyikan menu; otorisasi tetap ditegakkan backend ([Authorize(Page,Action)]).
 *
 * Usage:
 * <ProtectedRoute pageCode="MasterUser" action="Read">
 *   <UserListPage />
 * </ProtectedRoute>
 */
const ProtectedRoute = ({ children, pageCode, action, fallbackPath = '/' }: ProtectedRouteProps) => {
    const user = useSelector((state: any) => state.auth.user);
    const isAuthenticated = useSelector((state: any) => state.auth.isAuth);
    const location = useLocation();
    const [booted, setBooted] = useState(false);

    // Sesi dianggap ada jika token tersimpan, walau state Redux belum terisi.
    const hasToken = !!getAuthenticated();
    const storedUser = !user ? getProfile() : null;

    // Lengkapi state (role/permission) dari backend bila sesi ada tapi profile belum lengkap.
    const needsProfileRefresh = hasToken && !user?.role?.roleID;
    const { data: profile, isLoading: isProfileLoading } = useGetProfileQuery(undefined, {
        skip: !needsProfileRefresh,
    });

    useEffect(() => {
        // Tunggu satu putaran data: hindari redirect ke /login saat halaman di-refresh.
        setBooted(true);
    }, []);

    if (!hasToken && (!isAuthenticated || !user)) {
        return <Navigate to="/login" replace state={{ from: location.pathname }} />;
    }

    if (needsProfileRefresh && (isProfileLoading || !booted)) {
        return (
            <div className="flex items-center justify-center h-96 text-gray-500 text-sm" role="status">
                Memuat sesi…
            </div>
        );
    }

    const effectiveUser = user ?? storedUser ?? (profile ? { ...profile, role: { roleID: profile.roleId, roleName: profile.roleName, accessList: [] } } : null);
    const rawRoleID = effectiveUser?.role?.roleID ?? effectiveUser?.roleId;
    const roleID = rawRoleID === undefined || rawRoleID === null ? undefined : Number(rawRoleID);

    const hasPageAccess = checkAccess(effectiveUser, pageCode, action);

    // Master Data pages: only Admin (1) and Manager (2)
    if (pageCode.startsWith('Master') && roleID !== 1 && roleID !== 2) {
        return <Navigate to={fallbackPath} replace />;
    }

    // Report pages: exclude Staff (4) and Petty Cash (5)
    if (pageCode === 'Report' && (roleID === 4 || roleID === 5)) {
        return <Navigate to={fallbackPath} replace />;
    }

    if (!hasPageAccess) {
        // User doesn't have access, redirect to fallback
        return <Navigate to={fallbackPath} replace />;
    }

    return <>{children}</>;
};

export default ProtectedRoute;
