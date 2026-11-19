import { createSlice, PayloadAction } from '@reduxjs/toolkit';
import { setProfile, setToken, removeUserInfo, getProfile, getAuthenticated } from '@/utils/auth';

// Role Access types for RBAC
export interface RolePageAccess {
    pageCode: string;
    action: string;
    roleID: number;
}

export interface UserRole {
    roleID: number;
    roleName: string;
    accessList?: RolePageAccess[];
}

interface AuthState {
    user: UserProfile | null;
    isAuth: boolean;
    token: string | null;
}

interface UserProfile {
    token: string;
    displayName: string;
    email: string;
    profilePicture?: string;
    role?: UserRole;
}

interface UpdateProfilePayload {
    displayName: string;
    email: string;
    profilePicture?: string;
}

/**
 * Normalisasi role dari berbagai bentuk response menjadi SATU bentuk internal:
 * { roleID, roleName, accessList: RolePageAccess[] }.
 *
 * Backend (AuthenticateResponse.role) mengirim `roleID` + `accessList` (kadang null).
 * Beberapa endpoint lain memakai `roleId`/`role_id`. Semua varian dibaca di sini supaya
 * komponen tidak perlu menebak nama properti dan tidak crash saat `accessList` null.
 */
export const normalizeRole = (rawRole: any): UserRole | undefined => {
    if (!rawRole || typeof rawRole !== 'object') return undefined;

    const rawRoleId = rawRole.roleID ?? rawRole.roleId ?? rawRole.role_id;
    const roleID = rawRoleId === null || rawRoleId === undefined || rawRoleId === '' ? NaN : Number(rawRoleId);
    if (Number.isNaN(roleID)) return undefined;

    const rawAccess = rawRole.accessList ?? rawRole.accesslist ?? rawRole.access ?? [];
    const accessList: RolePageAccess[] = Array.isArray(rawAccess)
        ? rawAccess
              .filter((a: any) => a && typeof a === 'object' && a.pageCode)
              .map((a: any) => ({
                  pageCode: String(a.pageCode),
                  action: typeof a.action === 'string' ? a.action : '',
                  roleID: Number(a.roleID ?? a.roleId ?? a.role_id ?? roleID),
              }))
        : [];

    return {
        roleID,
        roleName: String(rawRole.roleName ?? rawRole.role_name ?? ''),
        accessList,
    };
};

/**
 * Normalisasi profil user (hasil login ATAU data lama di localStorage).
 * - Membaca `role` di level atas; kalau tidak ada, bangun dari `roleId`/`roleName`
 *   (bentuk GET /auth/profile) sehingga sesi lama tetap terbaca.
 * - `accessList` selalu array (bukan undefined) => menu/permission tidak pernah crash.
 */
export const normalizeUserProfile = (raw: any): any => {
    if (!raw || typeof raw !== 'object') return null;

    let role = normalizeRole(raw.role);
    if (!role) {
        const roleId = raw.roleID ?? raw.roleId ?? raw.role_id;
        if (roleId !== null && roleId !== undefined && roleId !== '') {
            role = normalizeRole({ roleID: roleId, roleName: raw.roleName ?? raw.role_name ?? '' });
        }
    }

    return { ...raw, role };
};

// Helper function to check access
export const checkAccess = (user: UserProfile | null, pageCode: string, action?: string): boolean => {
    const list = user?.role?.accessList;
    // Fallback: permission kosong/hilang tidak boleh melempar exception.
    if (!Array.isArray(list) || list.length === 0) return false;

    const access = list.find((a) => a && a.pageCode === pageCode);
    if (!access) return false;

    if (action) {
        const actions = String(access.action ?? '')
            .split(',')
            .map((a) => a.trim());
        return actions.includes(action);
    }

    return true;
};

const storedUser = normalizeUserProfile(getProfile());
export const authSlice = createSlice({
    name: 'auth',
    initialState: {
        user: storedUser || null,
        isAuth: !!storedUser && !!storedUser?.token,
        token: getAuthenticated() ?? storedUser?.token ?? null,
    } as AuthState,
    reducers: {
        setUser: (state, action: PayloadAction<UserProfile>) => {
            const profile = normalizeUserProfile(action.payload);
            if (!profile) return;
            setProfile(profile);
            if (profile.token) setToken(profile.token);
            state.user = profile;
            state.isAuth = true;
            state.token = profile.token ?? state.token;
        },
        updateUserProfile: (state, action: PayloadAction<UpdateProfilePayload>) => {
            if (state.user) {
                state.user.displayName = action.payload.displayName;
                state.user.email = action.payload.email;
                state.user.profilePicture = action.payload.profilePicture;
                // Also update localStorage
                setProfile(state.user);
            }
        },
        logOut: (state) => {
            removeUserInfo();
            state.user = null;
            state.isAuth = false;
            state.token = null;
        },
    },
});

export const { setUser, updateUserProfile, logOut } = authSlice.actions;
export default authSlice.reducer;
