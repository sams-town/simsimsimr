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

// Helper function to check access
export const checkAccess = (user: UserProfile | null, pageCode: string, action?: string): boolean => {
    if (!user?.role?.accessList) return false;

    const access = user.role.accessList.find(a => a.pageCode === pageCode);
    if (!access) return false;

    if (action) {
        const actions = access.action.split(',').map(a => a.trim());
        return actions.includes(action);
    }

    return true;
};

const storedUser = getProfile();
export const authSlice = createSlice({
    name: 'auth',
    initialState: {
        user: storedUser || null,
        isAuth: !!storedUser,
        token: getAuthenticated(),
    } as AuthState,
    reducers: {
        setUser: (state, action: PayloadAction<UserProfile>) => {
            setProfile(action.payload);
            setToken(action.payload.token);
            state.user = action.payload;
            state.isAuth = true;
            state.token = action.payload.token;
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

