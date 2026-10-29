import { LoginRequest } from '@/types/apiType';
import { apiSlice } from '../apiSlice';
import { API_BASE_URL } from '../apiSlice';

/**
 * URL login absolut. Backend .NET memakai route [Route("[controller]")] + [HttpPost("login")]
 * pada AuthController => POST {baseUrl}/Auth/login (case-sensitive, huruf A besar).
 *
 * Dibuat absolut secara eksplisit agar request TIDAK PERNAH dikirim relatif ke
 * https://backoffice.rsthb.id/Auth/login (nginx frontend -> 405 Not Allowed).
 */
export const LOGIN_URL = `${API_BASE_URL.replace(/\/+$/, '')}/Auth/login`;

/** Pastikan body hanya berisi field yang dikenal backend (AuthenticateRequest: Email, Password). */
const toLoginBody = (data: LoginRequest) => ({
  email: data?.email ?? '',
  password: data?.password ?? '',
});

export interface ProfileResponse {
  userId: number;
  displayName: string;
  email: string;
  phone?: string;
  profilePicture?: string;
  roleId?: number;
  roleName?: string;
}

export interface UpdateProfileRequest {
  displayName: string;
  email: string;
  phone?: string;
  profilePicture?: string;
}

export interface ChangePasswordRequest {
  currentPassword: string;
  newPassword: string;
  confirmPassword: string;
}

export interface ForgotPasswordRequest {
  email: string;
}

export interface ResetPasswordRequest {
  token: string;
  newPassword: string;
  confirmPassword: string;
}

export const authApi = apiSlice.injectEndpoints({
  endpoints: (builder) => ({
    login: builder.mutation<{ message: string; data: any }, LoginRequest>({
      query: (data: LoginRequest) => ({
        // URL absolut: https://api.rsthb.id/Auth/login
        url: LOGIN_URL,
        method: 'POST',
        // Body plain object -> RTK Query menserialisasi dengan JSON.stringify
        // dan otomatis menyetel Content-Type: application/json.
        body: toLoginBody(data),
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
        },
      }),
    }),
    getProfile: builder.query<ProfileResponse, void>({
      query: () => 'auth/profile',
      transformResponse: (response: { data: ProfileResponse }) => response.data,
    }),
    updateProfile: builder.mutation<void, UpdateProfileRequest>({
      query: (data) => ({
        url: 'auth/profile',
        method: 'PUT',
        body: data,
      }),
    }),
    changePassword: builder.mutation<void, ChangePasswordRequest>({
      query: (data) => ({
        url: 'auth/password',
        method: 'PUT',
        body: data,
      }),
    }),
    uploadProfilePhoto: builder.mutation<{ url: string }, FormData>({
      query: (formData) => ({
        url: 'auth/upload-photo',
        method: 'POST',
        body: formData,
      }),
      transformResponse: (response: { data: { url: string } }) => response.data,
    }),
    deleteProfilePhoto: builder.mutation<void, void>({
      query: () => ({
        url: 'auth/photo',
        method: 'DELETE',
      }),
    }),
    forgotPassword: builder.mutation<void, ForgotPasswordRequest>({
      query: (data) => ({
        url: 'auth/forgot-password',
        method: 'POST',
        body: data,
      }),
    }),
    resetPassword: builder.mutation<void, ResetPasswordRequest>({
      query: (data) => ({
        url: 'auth/reset-password',
        method: 'POST',
        body: data,
      }),
    }),
  }),
});

export const {
  useLoginMutation,
  useGetProfileQuery,
  useUpdateProfileMutation,
  useChangePasswordMutation,
  useUploadProfilePhotoMutation,
  useDeleteProfilePhotoMutation,
  useForgotPasswordMutation,
  useResetPasswordMutation,
} = authApi;
