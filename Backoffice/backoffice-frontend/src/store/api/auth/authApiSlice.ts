import { LoginRequest } from '@/types/apiType';
import { apiSlice } from '../apiSlice';

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
    login: builder.mutation({
      query: (data: LoginRequest) => ({
        url: 'v1/authentication/auth/signin',
        method: 'POST',
        body: data,
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
