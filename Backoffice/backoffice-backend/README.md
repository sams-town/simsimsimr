# Backoffice Backend

ASP.NET Core 8 Web API — database utama PostgreSQL, dengan koneksi ke database Khanza (MySQL).

## Konfigurasi

Semua kredensial di `appsettings*.json` berisi nilai **dummy** (`CHANGE_ME`, `example.com`) dan wajib diganti sebelum aplikasi dijalankan.

| Key | Keterangan |
|---|---|
| `ConnectionStrings:MyConnection` | PostgreSQL database backoffice |
| `ConnectionStrings:ZeemarConnection` | MySQL database Khanza |
| `AppSettings:Secret` | Secret signing JWT — string acak, minimal 32 karakter |
| `AppSettings:BackendUrl` | URL publik API ini |
| `AppSettings:FrontendUrl` | URL publik frontend (dipakai untuk link reset password) |

File yang dipakai ditentukan oleh `ASPNETCORE_ENVIRONMENT`: `Local`, `Development`, atau `Production` (default `appsettings.json`).

## Menjalankan

```bash
dotnet restore
dotnet run --launch-profile http   # http://localhost:5245
```

## Docker

```bash
docker build -f Dockerfile -t rssm-backoffice-backend-dev .        # ASPNETCORE_ENVIRONMENT=Development
docker build -f Dockerfile.Prod -t rssm-backoffice-backend-prod .  # ASPNETCORE_ENVIRONMENT=Production
```

## Struktur database

- `database/`, `SQL/` — script schema, seed, dan perbaikan data
- `Migrations/` — script migrasi SQL berurutan per tanggal
- `migration-worker/` — worker Python untuk menjalankan job migrasi Khanza (lihat README di folder tersebut)
