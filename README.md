# Reasuransi Report

Sistem Generator Laporan Reasuransi — aplikasi web lokal berbasis **Laravel + React (Inertia.js) + Tailwind CSS + MySQL** untuk mengelola data produksi premi dan klaim, lalu menghasilkan satu file Excel dengan **tiga worksheet** yang rapi sesuai struktur laporan.

## Teknologi

| Komponen | Pilihan |
|---|---|
| Backend | Laravel 13 + Eloquent ORM + Form Request |
| Frontend | React via Inertia.js |
| Styling | Tailwind CSS v4 |
| Database | MySQL |
| Excel | maatwebsite/excel (PhpSpreadsheet) |
| Build tool | Vite |

## Fitur

- CRUD data produksi & premi (polis) dengan pencarian dan pagination
- CRUD data klaim reasuransi dengan pilihan polis dan filter status
- Halaman ringkasan keuangan dengan perhitungan server-side
- Generate & download workbook Excel **3 worksheet**:
  1. **Borderaux Premi** — Laporan Produksi & Premi Reasuransi
  2. **Borderaux Klaim** — Laporan Klaim Reasuransi
  3. **Ringkasan Keuangan** — Ringkasan akun keuangan dengan formula lintas-sheet
- Validasi input lengkap di sisi server
- Format angka Rupiah, bahasa Indonesia, zona waktu Asia/Jakarta

## Prasyarat

- [Laragon](https://laragon.org/) lengkap (Apache/Nginx + PHP `8.3+` + MySQL)
- Composer
- Node.js + npm
- Browser Chrome/Chromium terbaru

Periksa versi yang tersedia:

```sh
php -v
composer -V
node -v
npm -v
```

## Instalasi di Laragon

1. **Clone/masukkan project** ke folder `C:\laragon\www\reasuransi-report`.

2. **Buat database** kosong di MySQL melalui HeidiSQL / phpMyAdmin:
   ```
   Nama database: reasuransi-report
   ```

3. **Konfigurasi `.env`** (salin dari `.env.example` jika belum ada):
   ```ini
   APP_NAME="Reasuransi Report"
   APP_URL=http://localhost:8000

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=reasuransi-report
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Install dependencies & jalankan migrasi + seed:**
   ```sh
   composer install
   npm install
   php artisan key:generate
   php artisan migrate:fresh --seed
   npm run build
   ```

5. **Jalankan aplikasi:**
   ```sh
   php artisan serve
   ```
   Buka `http://localhost:8000` di browser.

   Untuk pengembangan frontend (hot reload):
   ```sh
   npm run dev
   ```

## Menghasilkan Laporan Excel

- Klik tombol **"Generate Excel"** di sidebar, Dashboard, atau halaman Ringkasan.
- File diunduh dengan nama: `laporan_reasuransi_YYYY-MM-DD_HHmmss.xlsx`
- Workbook berisi tepat tiga worksheet sesuai urutan:
  `Borderaux Premi` → `Borderaux Klaim` → `Ringkasan Keuangan`.

Workbook contoh hasil dari seed data tersedia di `examples/laporan_reasuransi_contoh.xlsx`.

## Perhitungan Ringkasan

```
total_premi_gross  = SUM(reinsurance_premium)
komisi_reasuransi  = total_premi_gross * commission_rate (default 10%)
premi_netto        = total_premi_gross - komisi_reasuransi
total_recovery     = SUM(reinsurance_recovery)
saldo_akhir        = premi_netto - total_recovery
```

Seluruh perhitungan berada di `App\Services\FinancialSummaryService` (sumber kebenaran tunggal) dan direplikasi sebagai formula Excel di worksheet Ringkasan Keuangan.

## Menjalankan Test

```sh
php artisan test
```

Cakupan test: CRUD produksi & klaim, validasi batas, perhitungan ringkasan, dan ekspor workbook (struktur sheet, formula, nama file).

## Struktur Direktori Utama

```
app/
├── Exports/                 # Export workbook multi-sheet
│   ├── ReinsuranceWorkbookExport.php
│   └── Sheets/              # 3 class sheet + trait styling
├── Http/
│   ├── Controllers/         # Dashboard, Production, Claim, Summary, Report
│   └── Requests/            # Form Request validasi
├── Models/                  # ReinsuranceProduction, ReinsuranceClaim, ReportSetting
└── Services/                # FinancialSummaryService, ReinsuranceReportService
resources/
└── js/
    ├── Layouts/AppLayout.jsx
    ├── Pages/               # Dashboard, Productions, Claims, Summary
    └── Components/          # DataTable, CurrencyInput, ConfirmDialog, FlashMessage, Pagination
```

## Route Minimum

| Method | Route | Deskripsi |
|---|---|---|
| GET | `/` | Dashboard |
| RESOURCE | `/productions` | CRUD produksi & premi |
| RESOURCE | `/claims` | CRUD klaim |
| GET | `/summary` | Preview ringkasan |
| GET | `/reports/reinsurance.xlsx` | Generate & download workbook |