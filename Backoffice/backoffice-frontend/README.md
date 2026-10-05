# Backoffice Frontend

React 18 + Vite + TypeScript + Tailwind CSS.

## Konfigurasi

URL backend diatur lewat `VITE_HOST`. Nilai bawaan adalah **dummy** (`example.com`) dan perlu diganti:

| File | Dipakai oleh |
|---|---|
| `.env` | `npm run dev` lokal |
| `.env.Dev` | `Dockerfile` (image dev) |
| `.env.Prod` | `Dockerfile.Prod` (image production, di-serve nginx) |

## Menjalankan

```bash
npm install --force
npm run dev      # http://localhost:3000
npm run build    # output ke dist/
```

## Docker

```bash
docker build -f Dockerfile -t rssm-backoffice-frontend-dev .
docker build -f Dockerfile.Prod -t rssm-backoffice-frontend-prod .
```
