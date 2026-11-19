export const baseAuthName: string = import.meta.env.VITE_BASE_APP_NAME as string;

const TOKEN_KEY = `${baseAuthName}-token`;
const USER_KEY = `${baseAuthName}-user`;

export const removeUserInfo = (): void => {
  localStorage.removeItem(TOKEN_KEY);
  localStorage.removeItem(USER_KEY);
};

export const setToken = (token: string): void => {
  if (!token) return;
  // Simpan string apa adanya. JANGAN JSON.stringify(token) — token yang di-stringify
  // akan terkirim sebagai `Bearer "eyJ..."` (tanda kutip ikut) dan backend menjawab 401.
  localStorage.setItem(TOKEN_KEY, typeof token === 'string' ? token : String(token));
};

export const setProfile = (profile: Record<string, any>): void => {
  // Simpan sekali saja (string JSON). JANGAN JSON.stringify dua kali:
  // getProfile() hanya melakukan satu JSON.parse, sehingga string yang di-stringify
  // dua kali akan terbaca sebagai string, bukan object.
  localStorage.setItem(USER_KEY, JSON.stringify(profile));
};

/** Baca token; toleran terhadap data lama yang terlanjur di-stringify. */
export const getAuthenticated = (): string | null => {
  const raw = localStorage.getItem(TOKEN_KEY);
  if (!raw) return null;
  let token = raw;
  if (token.startsWith('"')) {
    // Data lama: token tersimpan sebagai JSON string (double stringify).
    try {
      const parsed = JSON.parse(token);
      if (typeof parsed === 'string') token = parsed;
    } catch {
      token = raw.replace(/^"|"$/g, '');
    }
  }
  // Buang prefix "Bearer " ganda bila pernah tersimpan.
  token = token.replace(/^Bearer\s+/i, '').trim();
  return token || null;
};

export const getProfile = (): Record<string, any> | null => {
  const profileData = localStorage.getItem(USER_KEY);
  if (!profileData) return null;
  try {
    let parsed: any = JSON.parse(profileData);
    // Kompatibilitas data lama yang ter-stringify dua kali (string berisi JSON).
    if (typeof parsed === 'string') {
      try {
        parsed = JSON.parse(parsed);
      } catch {
        return null;
      }
    }
    return parsed && typeof parsed === 'object' ? parsed : null;
  } catch {
    // Data korup jangan sampai melempar exception saat inisialisasi Redux
    // (menyebabkan layar putih tepat setelah login berhasil).
    localStorage.removeItem(USER_KEY);
    return null;
  }
};
