import { Navigate } from 'react-router-dom';
import { useSelector } from 'react-redux';
import { checkAccess } from '@/store/api/auth/authSlice';

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
 * Usage:
 * <ProtectedRoute pageCode="MasterUser" action="Read">
 *   <UserListPage />
 * </ProtectedRoute>
 */
const ProtectedRoute = ({
    children,
    pageCode,
    action,
    fallbackPath = '/'
}: ProtectedRouteProps) => {
    const user = useSelector((state: any) => state.auth.user);
    const isAuthenticated = useSelector((state: any) => state.auth.isAuth);

    // If not authenticated, redirect to login
    if (!isAuthenticated) {
        return <Navigate to="/login" replace />;
    }

    // Check if user has access to this page
    const hasPageAccess = checkAccess(user, pageCode, action);
    const roleID = user?.role?.roleID;

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
