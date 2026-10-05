import { getAuthenticated } from '@/utils/auth';
import { toastMessage } from '@/utils/toastUtils';
import { useState } from 'react';

interface DownloadParams {
  [key: string]: string | number | undefined | null;
}

interface DownloadFileArgs {
  endpoint: string;
  baseFilename: string;
  params?: DownloadParams;
}

export const useDownloader = () => {
  const [isLoading, setIsLoading] = useState(false);

  const downloadFile = async ({ endpoint, baseFilename, params = {} }: DownloadFileArgs) => {
    setIsLoading(true);
    try {
      const cleanedParams: Record<string, string> = {};
      for (const key in params) {
        const value = params[key];
        if (value !== null && value !== undefined) {
          cleanedParams[key] = String(value);
        }
      }

      const API_URL = import.meta.env.VITE_HOST;
      const queryString = new URLSearchParams(params as Record<string, string>).toString();
      const url = `${API_URL}${endpoint}?${queryString}`;

      const token = getAuthenticated();
      if (!token) {
        toastMessage('Authentication token not found. Please log in again.', 'error');
        return;
      }

      const response = await fetch(url, {
        method: 'GET',
        headers: {
          accept: '*/*',
          Authorization: `Bearer ${token}`,
        },
      });

      if (!response.ok) {
        if (response.status === 401) {
          toastMessage('Your session has expired. Please log in again.', 'error');
        } else {
          const errorText = await response.text();
          throw new Error(errorText || `HTTP error! Status: ${response.status}`);
        }
        return;
      }

      const blob = await response.blob();
      if (blob.size < 10) {
        throw new Error('The downloaded file appears to be empty or invalid.');
      }

      let finalFilename = baseFilename;

      const searchTerm = cleanedParams.keyword || cleanedParams.search;
      if (searchTerm) {
        const parts = baseFilename.split('.');
        const ext = parts.pop();
        const name = parts.join('.');
        finalFilename = `${name}_${searchTerm}.${ext}`;
      }

      if (cleanedParams.coaCode) {
        const parts = finalFilename.split('.');
        const ext = parts.pop();
        const name = parts.join('.');
        finalFilename = `${name}_${cleanedParams.coaCode}.${ext}`;
      }

      if (cleanedParams.startDate && cleanedParams.endDate) {
        const startDate = cleanedParams.startDate.split('T')[0].replace(/-/g, '');
        const endDate = cleanedParams.endDate.split('T')[0].replace(/-/g, '');
        const parts = finalFilename.split('.');
        const ext = parts.pop();
        const name = parts.join('.');
        finalFilename = `${name}_startDate-${startDate}_endDate-${endDate}.${ext}`;
      }

      const downloadUrl = window.URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.style.display = 'none';
      a.href = downloadUrl;
      a.download = finalFilename;

      document.body.appendChild(a);
      a.click();

      window.URL.revokeObjectURL(downloadUrl);
      document.body.removeChild(a);

      toastMessage('File downloaded successfully!', 'success');
    } catch (error: any) {
      console.error('Download failed:', error);
      toastMessage(`Download failed: ${error.message}`, 'error');
    } finally {
      setIsLoading(false);
    }
  };

  return { downloadFile, isLoading };
};
