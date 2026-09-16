<div align="center">

██████╗ ███████╗ █████╗ ███████╗██╗   ██╗██████╗  █████╗ ███╗   ██╗███████╗██╗
██╔══██╗██╔════╝██╔══██╗██╔════╝██║   ██║██╔══██╗██╔══██╗████╗  ██║██╔════╝██║
██████╔╝█████╗  ███████║███████╗██║   ██║██████╔╝███████║██╔██╗ ██║███████╗██║
██╔══██╗██╔══╝  ██╔══██║╚════██║██║   ██║██╔══██╗██╔══██║██║╚██╗██║╚════██║██║
██║  ██║███████╗██║  ██║███████║╚██████╔╝██║  ██║██║  ██║██║ ╚████║███████║██║
╚═╝  ╚═╝╚══════╝╚═╝  ╚═╝╚══════╝ ╚═════╝ ╚═╝  ╚═╝╚═╝  ╚═╝╚═╝  ╚═══╝╚══════╝╚═╝

Reasuransi Report

Sistem Generator Laporan Reasuransi Berbasis Web

Kelola data produksi premi dan klaim, hitung ringkasan keuangan, lalu hasilkan satu workbook Excel profesional dengan tiga worksheet dalam sekali klik.













</div>

Tentang Project

Reasuransi Report adalah aplikasi web lokal untuk mengelola data produksi dan premi reasuransi, mencatat klaim, menghitung ringkasan akun keuangan, serta menghasilkan laporan Excel secara otomatis.

Fokus utama project ini adalah menghasilkan file .xlsx yang rapi, akurat, mudah dibaca, dan memiliki tepat tiga worksheet:

Borderaux Premi — Laporan Produksi dan Premi Reasuransi.

Borderaux Klaim — Laporan Klaim Reasuransi.

Ringkasan Keuangan — Ringkasan akun keuangan dengan formula lintas-sheet.

[!IMPORTANT]
Antarmuka web dibuat sederhana dan fungsional. Prioritas utama project adalah kualitas data, perhitungan, formula, dan tampilan workbook Excel.

System Overview

flowchart LR
    A[Input Data Premi] --> D[(MySQL)]
    B[Input Data Klaim] --> D
    D --> E[Laravel Services]
    E --> F[Financial Summary]
    E --> G[Excel Generator]
    G --> H[Workbook 3 Sheets]

┌────────────────────── REASURANSI REPORT ──────────────────────┐
│                                                               │
│  [ PRODUKSI & PREMI ]    [ KLAIM ]    [ RINGKASAN ]          │
│            │                 │              │                  │
│            └─────────────────┴──────────────┘                  │
│                              │                                │
│                        [ MySQL Database ]                      │
│                              │                                │
│                  [ Generate Excel Workbook ]                  │
│                              │                                │
│   Borderaux Premi → Borderaux Klaim → Ringkasan Keuangan     │
│                                                               │
└───────────────────────────────────────────────────────────────┘

Teknologi

Layer

Teknologi

Fungsi

Backend

Laravel 13

Routing, controller, validasi, service, dan export

ORM

Eloquent ORM

Pengelolaan dan relasi data MySQL

Frontend

React + Inertia.js

Antarmuka tanpa API terpisah

Styling

Tailwind CSS v4

Layout dan komponen responsif

Database

MySQL

Penyimpanan data produksi, klaim, dan pengaturan

Excel Engine

maatwebsite/excel

Export workbook multi-sheet

Spreadsheet Core

PhpSpreadsheet

Styling, formula, format angka, dan print settings

Build Tool

Vite

Development server dan production build

Local Environment

Laragon

PHP, MySQL, web server, dan virtual host lokal

Fitur Utama

Produksi dan Premi

CRUD data polis dan premi reasuransi.

Pencarian berdasarkan nomor polis atau nama tertanggung.

Pagination data.

Validasi uang pertanggungan, retention, dan ceded.

Jenis reasuransi: Surplus, Quota Share, Fac/Surplus, Facultative, dan Other.

Klaim Reasuransi

CRUD data klaim yang terhubung ke polis.

Pencarian berdasarkan nomor klaim atau nomor polis.

Filter berdasarkan status klaim.

Validasi nilai klaim dan recovery.

Perlindungan relasi agar polis yang masih memiliki klaim tidak terhapus.

Ringkasan Keuangan

Total premi reasuransi gross.

Komisi reasuransi dengan nilai default 10%.

Premi reasuransi netto.

Total recovery klaim.

Saldo netto setelah klaim.

Satu sumber perhitungan melalui FinancialSummaryService.

Excel Generator

Menghasilkan satu file .xlsx.

Tepat tiga worksheet dengan urutan tetap.

Formula total dan formula lintas-sheet.

Format Rupiah, tanggal Indonesia, border, header, freeze pane, dan auto filter.

Lebar kolom, alignment, wrap text, serta print settings yang sudah diatur.

Nama file otomatis menggunakan tanggal dan waktu.

Struktur Workbook

Sheet 1 — Borderaux Premi

Kolom

Data

A

No Polis

B

Nama Tertanggung

C

Tanggal Lahir

D

Uang Pertanggungan atau UP Utama

E

Sendiri atau Retention

F

UP Direasuransikan atau Ceded

G

Jenis Reasuransi

H

Premi Reasuransi

Sheet 2 — Borderaux Klaim

Kolom

Data

A

No Klaim

B

No Polis

C

Nama Tertanggung

D

Penyebab Meninggal atau Klaim

E

Tanggal Lahir

F

Total Nilai Klaim

G

Uang Pertanggungan atau UP Utama

H

Porsi Klaim Reasuransi atau Recovery

I

UP Direasuransikan atau Ceded

J

Status Klaim

Sheet 3 — Ringkasan Keuangan

Metrik

Perhitungan

Premi Reasuransi Gross

Total seluruh premi reasuransi

Komisi Reasuransi

Premi gross × commission rate

Premi Reasuransi Netto

Premi gross − komisi reasuransi

Recovery Klaim

Total seluruh recovery klaim

Saldo Netto Setelah Klaim

Premi netto − recovery klaim

Prasyarat

Pastikan perangkat telah memiliki:

Laragon dengan PHP 8.3+ dan MySQL.

Composer.

Node.js dan npm.

Git.

Chrome atau browser Chromium terbaru.

Periksa seluruh runtime:

php -v
composer -V
node -v
npm -v
git --version

Instalasi di Laragon

1. Letakkan project

Clone atau pindahkan project ke:

C:\laragon\www\reasuransi-report

Jika menggunakan Git:

cd C:\laragon\www
git clone <repository-url> reasuransi-report
cd reasuransi-report

2. Instal dependency backend

composer install

Jika koneksi download ZIP bermasalah, gunakan:

composer install --prefer-source

3. Instal dependency frontend

npm install

4. Siapkan environment

Command Prompt:

copy .env.example .env

Git Bash atau terminal Unix:

cp .env.example .env

Generate application key:

php artisan key:generate

5. Buat database

Buat database baru melalui HeidiSQL atau phpMyAdmin:

CREATE DATABASE reasuransi_report
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

[!WARNING]
Nama folder project menggunakan tanda hubung: reasuransi-report. Nama database menggunakan underscore: reasuransi_report. Jangan menuliskan nama database dengan tanda hubung tanpa penanganan khusus SQL.

6. Konfigurasi .env

APP_NAME="Reasuransi Report"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://reasuransi-report.test

APP_LOCALE=id
APP_FALLBACK_LOCALE=id
APP_TIMEZONE=Asia/Jakarta

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=reasuransi_report
DB_USERNAME=root
DB_PASSWORD=

7. Jalankan migration dan seeder

php artisan optimize:clear
php artisan migrate:fresh --seed

[!CAUTION]
migrate:fresh menghapus seluruh tabel pada database yang dipilih. Gunakan hanya untuk instalasi baru atau database development yang tidak berisi data penting.

8. Build frontend

Untuk development dengan hot reload:

npm run dev

Untuk production build:

npm run build

9. Jalankan aplikasi

Menggunakan Laravel development server:

php artisan serve

Buka:

http://127.0.0.1:8000

Atau gunakan virtual host otomatis Laragon:

http://reasuransi-report.test

Quick Start

cd C:\laragon\www\reasuransi-report
composer install
npm install
copy .env.example .env
php artisan key:generate
php artisan optimize:clear
php artisan migrate:fresh --seed
npm run build
php artisan serve

Generate Laporan Excel

Jalankan aplikasi.

Isi atau periksa data pada menu Produksi & Premi.

Isi atau periksa data pada menu Klaim Reasuransi.

Buka halaman Ringkasan Keuangan untuk memeriksa hasil perhitungan.

Klik tombol Generate Excel pada sidebar, Dashboard, atau halaman Ringkasan.

Browser akan mengunduh file:

laporan_reasuransi_YYYY-MM-DD_HHmmss.xlsx

Urutan worksheet:

[1] Borderaux Premi
        ↓
[2] Borderaux Klaim
        ↓
[3] Ringkasan Keuangan

Contoh workbook dari seed data tersedia di:

examples/laporan_reasuransi_contoh.xlsx

Formula Keuangan

$totalPremiGross = $productions->sum('reinsurance_premium');
$komisiReasuransi = $totalPremiGross * $commissionRate;
$premiNetto = $totalPremiGross - $komisiReasuransi;
$totalRecovery = $claims->sum('reinsurance_recovery');
$saldoAkhir = $premiNetto - $totalRecovery;

PREMI GROSS
    │
    ├── dikurangi KOMISI REASURANSI
    │
    ▼
PREMI NETTO
    │
    ├── dikurangi TOTAL RECOVERY KLAIM
    │
    ▼
SALDO NETTO SETELAH KLAIM

Seluruh perhitungan server-side berada di:

app/Services/FinancialSummaryService.php

Formula yang setara diterapkan kembali pada worksheet Ringkasan Keuangan agar hasil workbook dapat ditelusuri dan dihitung oleh Excel.

Database Schema

erDiagram
    REINSURANCE_PRODUCTIONS ||--o{ REINSURANCE_CLAIMS : memiliki
    REINSURANCE_PRODUCTIONS {
        bigint id PK
        varchar policy_number UK
        varchar insured_name
        date birth_date
        decimal sum_insured
        decimal retention
        decimal ceded_amount
        varchar reinsurance_type
        decimal reinsurance_premium
    }
    REINSURANCE_CLAIMS {
        bigint id PK
        varchar claim_number UK
        bigint production_id FK
        text claim_cause
        decimal total_claim_value
        decimal reinsurance_recovery
        varchar claim_status
    }
    REPORT_SETTINGS {
        bigint id PK
        decimal commission_rate
        varchar company_name
        varchar report_title
    }

Aturan integritas utama

policy_number wajib unik.

claim_number wajib unik.

retention + ceded_amount harus sama dengan sum_insured.

ceded_amount tidak boleh melebihi sum_insured.

reinsurance_recovery tidak boleh melebihi total_claim_value.

Polis yang masih memiliki klaim tidak dapat dihapus.

Struktur Direktori

reasuransi-report/
├── app/
│   ├── Exports/
│   │   ├── ReinsuranceWorkbookExport.php
│   │   └── Sheets/
│   │       ├── ProductionPremiumSheet.php
│   │       ├── ReinsuranceClaimSheet.php
│   │       ├── FinancialSummarySheet.php
│   │       └── Concerns/
│   ├── Http/
│   │   ├── Controllers/
│   │   └── Requests/
│   ├── Models/
│   └── Services/
│       ├── FinancialSummaryService.php
│       └── ReinsuranceReportService.php
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── examples/
│   └── laporan_reasuransi_contoh.xlsx
├── resources/
│   └── js/
│       ├── Components/
│       ├── Layouts/
│       └── Pages/
│           ├── Dashboard.jsx
│           ├── Productions/
│           ├── Claims/
│           └── Summary/
├── routes/
│   └── web.php
├── tests/
│   ├── Feature/
│   └── Unit/
├── .env.example
├── composer.json
├── package.json
└── README.md

Route Minimum

Method

Endpoint

Name

Deskripsi

GET

/

dashboard

Dashboard dan metrik utama

RESOURCE

/productions

productions.*

CRUD produksi dan premi

RESOURCE

/claims

claims.*

CRUD klaim reasuransi

GET

/summary

summary.index

Preview ringkasan keuangan

GET

/reports/reinsurance.xlsx

reports.reinsurance

Generate dan download workbook

Periksa route aktif:

php artisan route:list

Menjalankan Test

Jalankan seluruh automated test:

php artisan test

Jalankan kelompok test tertentu:

php artisan test --filter=Production
php artisan test --filter=Claim
php artisan test --filter=FinancialSummary
php artisan test --filter=Export

Cakupan pengujian minimum:

CRUD produksi dan premi.

CRUD klaim serta relasi polis.

Validasi nomor unik dan batas nilai.

Perhitungan ringkasan keuangan.

Response download Excel.

Nama dan urutan tiga worksheet.

Formula, nama file, serta cell penting pada workbook.

Production build frontend.

Verifikasi frontend:

npm run build

Troubleshooting

<details>
<summary><strong>Composer gagal menulis ZIP ke vendor/composer</strong></summary>

Tutup VS Code dan terminal lain, jalankan Laragon sebagai Administrator, lalu gunakan:

composer clear-cache
composer config --global process-timeout 2000
composer install --prefer-source

Pastikan antivirus tidak mengunci folder project dan Git dapat dijalankan dengan git --version.

</details>

<details>
<summary><strong>Database connection refused</strong></summary>

Pastikan MySQL aktif di Laragon dan konfigurasi .env benar:

DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=reasuransi_report
DB_USERNAME=root
DB_PASSWORD=

Kemudian jalankan:

php artisan optimize:clear

</details>

<details>
<summary><strong>Vite manifest not found</strong></summary>

Jalankan salah satu perintah berikut:

npm run dev

atau:

npm run build

</details>

<details>
<summary><strong>Class export atau package Excel tidak ditemukan</strong></summary>

composer install
composer dump-autoload
php artisan optimize:clear

</details>

<details>
<summary><strong>Perubahan .env tidak terbaca</strong></summary>

php artisan optimize:clear

</details>

Checklist Development

Project Laravel dapat dijalankan di Laragon.

Database reasuransi_report terhubung.

Migration dan seeder berhasil.

CRUD produksi dan premi berfungsi.

CRUD klaim berfungsi.

Validasi backend berjalan.

Ringkasan keuangan akurat.

Export menghasilkan satu file .xlsx.

Workbook berisi tepat tiga worksheet.

Formula dan total Excel benar.

Tampilan workbook rapi dan siap diperiksa.

php artisan test lulus.

npm run build berhasil.

Security Notes

Jangan commit file .env.

Jangan menyimpan password database di source code.

Gunakan Form Request untuk validasi server-side.

Gunakan CSRF protection bawaan Laravel.

Gunakan Eloquent atau query binding untuk mencegah SQL injection.

Nonaktifkan APP_DEBUG pada environment production.

Hindari memasukkan data pribadi asli ke seed dan repository publik.

APP_ENV=production
APP_DEBUG=false

Definition of Done

Project dinyatakan selesai apabila:

Aplikasi dapat dipasang dari README tanpa langkah yang hilang.

Seluruh halaman dan operasi CRUD berjalan.

Ringkasan web cocok dengan data database.

Tombol Generate Excel mengunduh workbook valid.

Workbook memiliki tiga worksheet sesuai urutan.

Tampilan Excel memiliki judul, header, border, format Rupiah, format tanggal, total, freeze pane, dan print settings yang benar.

Formula ringkasan cocok dengan hasil FinancialSummaryService.

Seluruh test lulus dan frontend berhasil di-build.

Project Identity

Atribut

Nilai

Nama aplikasi

Reasuransi Report

Folder project

reasuransi-report

Database MySQL

reasuransi_report

Bahasa

Indonesia

Zona waktu

Asia/Jakarta

Mata uang

Rupiah

Output

Excel .xlsx dengan tiga worksheet

<div align="center">

SYSTEM STATUS  : READY FOR DEVELOPMENT
DATABASE       : reasuransi_report
EXPORT ENGINE  : MULTI-SHEET XLSX
PRIMARY TARGET : CLEAN DATA • VALID FORMULA • PROFESSIONAL WORKBOOK

Built with Laravel, React, Tailwind CSS, MySQL, and a suspicious amount of coffee.

</div>
