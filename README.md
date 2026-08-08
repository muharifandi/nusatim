# Nusatim — Company Profile, Partner Portal & API (Laravel + Filament)

Aplikasi PT. Nusantara Teknologi Inovasi Mandiri (Nusatim), dibangun dengan **Laravel 10** + **Filament v3**. Terdiri dari tiga bagian:

1. **Situs company profile publik** — desain diadaptasi dari template statis "DigeCo" (CodeCanyon), seluruh konten (halaman, blog, layanan, portofolio, dll) dikelola lewat panel admin.
2. **Panel admin** (`/admin`) — kelola konten situs, RBAC (role & permission), dan **Portal Partner** (lead, customer, project, komisi, withdrawal, dst — lihat `todo_partnert.md` untuk breakdown lengkap tiap modul).
3. **Panel Partner** (`/partner`, versi web) + **REST API** (`/api/v1/...`, versi mobile) — dua jalur akses berbeda ke data & fitur yang **sama persis** untuk partner (lead, pipeline, customer, project, komisi, withdrawal, marketing center, support ticket, notifikasi). Lihat [Partner Portal & REST API](#partner-portal--rest-api-mobile) di bawah.

> **Catatan versi PHP/Laravel:** branch ini (`php-8.1-laravel-10`) sengaja diturunkan dari Laravel 13/PHP 8.3 ke **Laravel 10/PHP 8.1** untuk kompatibilitas shared hosting yang PHP-nya belum sampai 8.3. Kalau hosting kamu sudah PHP 8.3+, cek branch lain di repo ini untuk versi Laravel yang lebih baru.

---

## Daftar Isi

- [Spesifikasi Minimum Server](#spesifikasi-minimum-server)
- [Instalasi (Development)](#instalasi-development)
- [Konfigurasi](#konfigurasi)
- [Partner Portal & REST API (Mobile)](#partner-portal--rest-api-mobile)
- [Deploy ke Production](#deploy-ke-production)
- [Catatan Arsitektur](#catatan-arsitektur)
- [Troubleshooting](#troubleshooting)

---

## Spesifikasi Minimum Server

| Kebutuhan | Minimum | Catatan |
|---|---|---|
| PHP | **8.1** | Framework: Laravel 10.x. Dikembangkan &amp; diuji lokal di PHP 8.4 dengan `composer.json` `config.platform.php` di-set ke `8.1.31` supaya dependency yang ter-resolve benar-benar kompatibel PHP 8.1, bukan cuma kompatibel interpreter lokal (lihat [Catatan Arsitektur](#catatan-arsitektur)) |
| Database | MySQL 8.0+ atau MariaDB 10.3+ | |
| Composer | 2.x | |
| Web server | Apache (dengan `mod_rewrite`) — standar di hosting cPanel/LiteSpeed | Panduan di bawah ditulis untuk **shared hosting (cPanel)**; lihat catatan VPS/Nginx di bagian [Deploy](#deploy-ke-production) kalau perlu |
| Cron | Wajib | Untuk resolusi negara pengunjung di dashboard **dan** 2 scheduled command Portal Partner (lihat [Scheduler](#4-scheduler-wajib-untuk-peta-pengunjung--reminder-partner)) |
| Node.js / npm | **Tidak wajib** | Lihat catatan di bawah |

**Ekstensi PHP wajib:** `openssl`, `pdo_mysql`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`, `curl`, `gd`, `intl`, `zip` — semua ini sudah termasuk di instalasi PHP standar cPanel/LiteSpeed maupun `apt install php-*` di Ubuntu/Debian.

**Soal Node.js/npm:** proyek ini **tidak butuh proses build frontend**. Seluruh CSS/JS tampilan situs (folder `public/assets/` dan `public/dependencies/`) adalah file statis yang sudah jadi, langsung dipakai lewat `asset()` — bukan lewat Vite. Konfigurasi Vite yang ada di `package.json`/`vite.config.js` adalah sisa bawaan `laravel new` yang tidak pernah dipakai (cek: hanya `resources/views/welcome.blade.php`, halaman default yang tidak dipakai, yang mereferensikan `@vite`). Jadi **`npm install` dan `npm run build` boleh dilewati sepenuhnya**, baik saat instalasi maupun deploy.

---

## Instalasi (Development)

```bash
# 1. Masuk ke folder project
cd nusatim-laravel

# 2. Install dependency PHP
composer install

# 3. Siapkan file environment
cp .env.example .env
php artisan key:generate

# 4. Buat database kosong (MySQL/MariaDB), lalu isi kredensialnya di .env:
#    DB_CONNECTION=mysql
#    DB_HOST=127.0.0.1
#    DB_PORT=3306
#    DB_DATABASE=nusatim
#    DB_USERNAME=root
#    DB_PASSWORD=

# 5. Jalankan migrasi + seeder (membuat akun admin, data contoh, dsb)
php artisan migrate --seed

# 6. Pastikan folder upload bisa ditulis (lihat catatan disk "media" di bawah)
chmod -R 775 public/media

# 7. Generate dokumentasi Swagger untuk REST API (WAJIB - hasil generate-nya
#    ada di storage/api-docs/, sengaja di-gitignore, tidak ikut ter-clone)
php artisan l5-swagger:generate

# 8. Jalankan server development
php artisan serve
```

Buka `http://127.0.0.1:8000` untuk situs publik. Tiga panel/dokumentasi lain yang aktif:

| URL | Isi |
|---|---|
| `/admin` | Panel admin — konten situs, RBAC, Portal Partner (kelola dari sisi admin) |
| `/partner` | Panel Partner versi web — lead, pipeline, customer, project, komisi, dst (partner login sendiri, butuh approval admin dulu) |
| `/api/documentation` | Swagger UI — dokumentasi interaktif REST API (54 endpoint, untuk aplikasi mobile) |

**Login admin default (dibuat oleh seeder):**
- Email: `admin@nusatim.com`
- Password: `password`

> ⚠️ **Ganti password ini sebelum situs diakses publik.** Login ke `/admin`, buka menu profil di pojok kanan atas untuk mengubahnya, atau jalankan `php artisan tinker` dan update lewat model `User`.

Tidak perlu `php artisan storage:link` untuk disk `media` — lihat [Catatan Arsitektur](#disk-media-kustom-bukan-storagelink). Symlink `storage:link` standar **tetap dijalankan otomatis** kalau ada langkah lain yang membutuhkannya (disk `public` bawaan Laravel), aman dijalankan manual (`php artisan storage:link`) kalau ragu — tidak mengganggu disk `media`.

---

## Konfigurasi

### 1. Environment (`.env`)

Variabel yang benar-benar dipakai proyek ini, di luar bawaan Laravel:

| Variabel | Wajib? | Keterangan |
|---|---|---|
| `APP_URL` | Ya | Harus URL asli situs (dipakai untuk generate link, sitemap.xml, dsb) |
| `DB_*` | Ya | Kredensial database |
| `MAIL_MAILER` dan variabel `MAIL_*` lain | Ya untuk production | Default `log` (email hanya ditulis ke `storage/logs/laravel.log`, tidak benar-benar terkirim) — **wajib diganti ke SMTP asli** (Gmail, Mailgun, SES, dst) agar auto-reply form kontak & popup "Notify Us" benar-benar mengirim email |
| `GOOGLE_MAPS_API_KEY` | Tidak | Sudah tidak dipakai aktif — peta di halaman Contact sekarang pakai iframe embed biasa (diatur dari Site Settings), tidak butuh API key. Boleh dikosongkan. |

Sisanya (`SESSION_*`, `CACHE_STORE`, dst) boleh dibiarkan default.

> ⚠️ **`QUEUE_CONNECTION` — beda dari klaim versi lama README ini.** Auto-reply email situs publik memang dikirim sinkron (tidak butuh queue), **tapi notifikasi in-app Portal Partner (lonceng notifikasi di panel `/partner` maupun endpoint `GET /api/v1/notifications`) dikirim lewat class Filament yang otomatis di-*queue*** (`ShouldQueue`). Kalau `QUEUE_CONNECTION=database` (default `.env.example`) dan **tidak ada proses `php artisan queue:work` yang berjalan**, notifikasi akan tersangkut selamanya di tabel `jobs` dan **tidak pernah muncul** ke partner — ditemukan langsung lewat pengujian, bukan cuma dugaan. Ada dua opsi:
> - **Set `QUEUE_CONNECTION=sync`** di `.env` (paling praktis untuk shared hosting yang tidak bisa menjalankan proses background permanen) — notifikasi diproses langsung saat itu juga, tanpa perlu worker terpisah.
> - Atau jalankan `php artisan queue:work` sebagai proses background (butuh VPS/Supervisor, tidak realistis di kebanyakan shared hosting) kalau `QUEUE_CONNECTION=database` tetap dipertahankan.

### 2. Pengaturan yang TIDAK ada di `.env` (diatur dari admin)

Sengaja dibuat begini supaya pemilik situs bisa mengubahnya sendiri tanpa akses server:

- **Site Settings** (`/admin/manage-site-settings`): nama perusahaan, logo (light/dark/mobile/footer/favicon/preloader), sosial media, Google Maps embed URL, SEO default, **Google Analytics Measurement ID**, toggle **Image Loading Placeholder**, dan toggle **Mode Coming Soon**.
- **Pages** (`/admin/pages`): teks tiap section halaman (home, about, contact, coming-soon, dst) lewat editor key-value.
- **Menus** (`/admin/menus`): struktur navigasi header/footer, termasuk dropdown & mega menu.

### 3. Disk upload (`media`)

File yang diupload lewat admin (logo, gambar blog, foto tim, dst) disimpan langsung ke `public/media/uploads/` (lihat `config/filesystems.php`, disk `media`) — **bukan** `storage/app/public` + symlink standar Laravel. Konsekuensinya:

- Folder `public/media/` (dan subfoldernya) harus **writable** oleh user web server (`www-data`, `nobody`, atau sesuai konfigurasi hosting).
- Jangan jalankan `php artisan storage:link` — tidak diperlukan dan tidak berpengaruh ke upload.

### 4. Scheduler (wajib untuk Peta Pengunjung & reminder Partner)

Ada 3 command terjadwal (didefinisikan di `app/Console/Kernel.php` — bukan `routes/console.php`, lihat [Catatan Arsitektur](#catatan-arsitektur) soal kenapa berbeda dari Laravel versi terbaru):

```php
// app/Console/Kernel.php
$schedule->command('pageviews:resolve-countries --limit=100')->everyFiveMinutes(); // dashboard admin: peta negara pengunjung
$schedule->command('reminders:notify-due')->everyFiveMinutes();                    // Portal Partner: notifikasi reminder follow-up/meeting jatuh tempo
$schedule->command('projects:expire-stale-claims')->hourly();                      // Portal Partner: auto-reject klaim project yang kelamaan menggantung
```

Agar semuanya berjalan, tambahkan **satu baris cron** di server (`crontab -e`):

```
* * * * * cd /path/ke/project && php artisan schedule:run >> /dev/null 2>&1
```

Tanpa cron ini: tracking pengunjung tetap jalan tapi kolom negara di dashboard tetap kosong, reminder partner tidak pernah mengirim notifikasi tepat waktu, dan klaim project yang terbengkalai tidak pernah otomatis kembali `available` untuk partner lain.

---

## Partner Portal & REST API (Mobile)

Selain situs publik, aplikasi ini punya sistem lengkap untuk **partner** (reseller/sales) mengelola lead sampai withdrawal komisi — bisa diakses lewat **dua jalur berbeda ke data yang sama**:

| Jalur | Untuk siapa | Auth |
|---|---|---|
| Panel `/partner` (Filament, web) | Partner yang pakai browser | Session (guard `partner`) |
| REST API `/api/v1/...` | Aplikasi mobile Android/iOS | Token Bearer (Sanctum, guard `api`) |

Kedua jalur ini **membungkus logic bisnis yang sama persis** (model, aturan, efek samping) — bukan dua sistem terpisah yang kebetulan mirip.

### Modul yang tersedia (11 modul, 54 endpoint REST)

Auth · Profile · Dashboard · Lead & Opportunity · Sales Pipeline · Customer Management · Project Board · Commission Management · Withdrawal · Marketing Center · Support Ticket · Notification Center. Breakdown lengkap tiap modul (termasuk modul admin-only seperti RBAC, Workflow Assignment, Audit Log) ada di `todo_partnert.md`.

### Registrasi & approval partner

Partner mendaftar sendiri (lewat panel `/partner/register` atau `POST /api/v1/auth/register`), status awal `pending_review` — **tidak bisa akses modul bisnis apa pun** sampai admin approve lewat `/admin/partners`. Partner status lain: `approved` (akses penuh), `rejected`, `suspended`.

### Dokumentasi REST API — untuk developer aplikasi mobile

| Dokumen | Isi |
|---|---|
| [`docs/api/00-overview.md`](docs/api/00-overview.md) | Base URL, alur autentikasi token, status approval partner, format response/error umum — **baca ini duluan** |
| `docs/api/01-auth.md` s.d. `12-notifications.md` | Satu file per modul: penjelasan cara kerja + contoh payload request/response nyata |
| [`docs/api/testing/00-overview.md`](docs/api/testing/00-overview.md) | Skenario test per modul (65 skenario), diambil langsung dari test PHPUnit yang sudah hijau — checklist QA / referensi edge case |
| [`docs/mobile-ui/00-overview.md`](docs/mobile-ui/00-overview.md) | Spesifikasi layar aplikasi Android (Material 3): layout, komponen, state, navigasi, per modul |
| `/api/documentation` (server berjalan) | Swagger UI interaktif — coba langsung tiap endpoint dari browser |

### Yang perlu diperhatikan soal Sanctum

- Guard token untuk API bernama **`api`** (di `config/auth.php`), **bukan** guard bawaan Sanctum yang literal bernama `sanctum` — route API pakai middleware `auth:api`, bukan `auth:sanctum`. Ini disengaja (lihat komentar di `config/auth.php`), jangan diubah tanpa paham alasannya.
- Token tidak kedaluwarsa otomatis (`config/sanctum.php` → `'expiration' => null`), dan login dari banyak device sekaligus diizinkan (token lama tidak dicabut saat login baru).
- Middleware `partner.approved.api` (alias di `app/Http/Kernel.php`) menggating semua modul bisnis di belakang status `approved` — mengembalikan `403` JSON, bukan redirect (beda dari panel `/partner` yang redirect ke halaman status).

---

## Deploy ke Production

Panduan ini fokus ke **shared hosting berbasis cPanel** (Niagahoster, DomaiNesia, Rumahweb, Qwords, Hostinger, dst — mayoritas hosting Indonesia). Ada catatan VPS/Nginx di bagian paling bawah kalau suatu saat pindah ke sana.

### 1. Pilih versi PHP & aktifkan ekstensi

Di cPanel: **Select PHP Version** (kadang bernama **MultiPHP Manager**) → pilih domain → set ke **PHP 8.1 atau lebih baru**. Buka tab **Extensions**, pastikan aktif: `curl`, `fileinfo`, `gd`, `intl`, `mbstring`, `bcmath`, `xml`, `zip`, `pdo_mysql` — biasanya sudah aktif secara default.

### 2. Buat database

Di cPanel: **MySQL® Databases** → buat database baru dan user baru, lalu assign user ke database dengan **ALL PRIVILEGES**. Catat nama-namanya — cPanel otomatis menambah prefix akun, jadi hasilnya biasanya seperti `namauser_nusatim` (database) dan `namauser_admin` (user), bukan `nusatim`/`root` seperti di lokal.

### 3. Upload project

Document root domain harus mengarah ke folder **`public/`** project ini, bukan root project — ini bagian yang paling sering bikin bingung di shared hosting. Ada dua kondisi:

**A. Kalau cPanel mengizinkan ganti Document Root** (cek menu **Domains**, ada kolom "Document Root" yang bisa diklik ubah) — ini cara paling bersih:
1. Upload seluruh folder project ke luar `public_html`, misalnya ke `~/nusatim-laravel`.
2. Di menu **Domains**, ubah Document Root domain ke `nusatim-laravel/public`.
3. Selesai — struktur project persis sama seperti di lokal.

**B. Kalau Document Root terkunci ke `public_html`** (banyak paket shared hosting basic begini):
1. Upload seluruh project ke folder di luar `public_html`, misalnya `~/app-nusatim`.
2. Pindahkan **isi** folder `~/app-nusatim/public/` (semua file & folder di dalamnya) ke `public_html/`.
3. Edit `public_html/index.php`, ganti dua baris `require` supaya menunjuk ke lokasi project yang sebenarnya:
   ```php
   require __DIR__.'/../app-nusatim/vendor/autoload.php';
   // ...
   $app = require_once __DIR__.'/../app-nusatim/bootstrap/app.php';
   ```
4. Sesuaikan path di atas dengan struktur folder di akun hosting kamu (lihat lewat File Manager).

> Jangan upload folder `vendor/` dan `node_modules/` manual satu-satu lewat File Manager — terlalu banyak file kecil, sering timeout/gagal. Kompres jadi `.zip` dulu, upload, lalu extract lewat File Manager cPanel (atau lewat Terminal kalau tersedia, lihat langkah 4).

### 4. Install dependency & jalankan migrasi

Cek dulu apakah paket hosting kamu punya **Terminal** (menu di cPanel) atau akses **SSH**. Kalau ada:

```bash
cd ~/nusatim-laravel   # atau ~/app-nusatim, sesuai lokasi upload
composer install --optimize-autoloader --no-dev
cp .env.example .env   # lalu isi sesuai langkah 5
php artisan key:generate
php artisan migrate --seed
php artisan l5-swagger:generate   # wajib, hasil generate-nya di-gitignore
```

Kalau **tidak ada** Terminal/SSH sama sekali (beberapa paket paling murah membatasi ini): jalankan `composer install`, `php artisan migrate --seed`, dan `php artisan l5-swagger:generate` di komputer sendiri dulu (pointing ke database lokal), lalu upload seluruh project **termasuk folder `vendor/` dan `storage/api-docs/`** lewat File Manager/FTP, dan import database lokal ke database hosting lewat **phpMyAdmin** (menu cPanel). Migrasi berikutnya (kalau ada update kode) perlu diulang dengan cara yang sama.

### 5. Environment (`.env`)

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-asli-anda.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=namauser_nusatim
DB_USERNAME=namauser_admin
DB_PASSWORD=...

MAIL_MAILER=smtp
MAIL_HOST=...
MAIL_USERNAME=...
MAIL_PASSWORD=...

# Wajib di shared hosting - lihat catatan QUEUE_CONNECTION di bagian
# Konfigurasi di atas (tanpa ini, notifikasi in-app Partner Portal
# tersangkut di tabel `jobs` dan tidak pernah muncul ke partner)
QUEUE_CONNECTION=sync
```

Lalu (kalau ada Terminal/SSH):

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

> Kalau nanti mengubah `.env`, wajib `php artisan config:clear` lalu `config:cache` ulang — Laravel tidak otomatis membaca `.env` selagi config sudah di-cache. Tanpa Terminal, cukup jangan jalankan langkah cache ini sama sekali (Laravel tetap berjalan normal, hanya sedikit lebih lambat).

### 6. Permission folder

Lewat File Manager cPanel atau Terminal:

```bash
chmod -R 755 storage bootstrap/cache public/media
```

Shared hosting biasanya menjalankan semua proses sebagai user cPanel kamu sendiri (bukan `www-data` seperti VPS), jadi **tidak perlu** `chown` — file yang kamu upload sudah otomatis milikmu.

### 7. SSL

cPanel → **SSL/TLS Status**, jalankan **AutoSSL** (gratis, biasanya sudah otomatis aktif). Pastikan `APP_URL` di `.env` memakai `https://` setelah SSL aktif.

### 8. Cron job (wajib)

cPanel → **Cron Jobs** → tambah baru:
- **Interval:** setiap menit (`* * * * *`, atau pilih "Once Per Minute" kalau tersedia)
- **Command:**
  ```
  cd /home/namauser/nusatim-laravel && php artisan schedule:run >> /dev/null 2>&1
  ```
  Sesuaikan path dengan lokasi project (lihat langkah 3). Kalau ada beberapa versi PHP di server, cPanel biasanya menyediakan dropdown/binary khusus (mis. `/opt/cpanel/ea-php83/root/usr/bin/php`) — pakai itu sebagai ganti `php` supaya pasti memakai versi yang benar.

Ini yang menjalankan resolusi negara pengunjung untuk widget Peta di dashboard — lihat [Scheduler](#4-scheduler-wajib-untuk-peta-pengunjung--reminder-partner).

### 9. Setelah live

- [ ] Ganti password admin default
- [ ] Isi semua field di Site Settings (logo, kontak, sosial media, Google Analytics ID, Google Maps embed URL)
- [ ] Kirim email tes lewat form Contact untuk memastikan SMTP benar-benar terkirim (bukan cuma masuk log)
- [ ] Cek `https://domain-anda.com/sitemap.xml` bisa diakses (untuk submit ke Google Search Console)
- [ ] Pastikan Mode Coming Soon **mati** kalau situs sudah siap dilihat publik
- [ ] Cek `https://domain-anda.com/api/documentation` (Swagger UI) bisa dibuka — kalau blank/500, `l5-swagger:generate` belum sempat jalan
- [ ] Daftar 1 partner tes lewat `/partner/register`, approve dari `/admin/partners`, pastikan notifikasi in-app-nya benar-benar muncul (bukan cuma tersangkut di tabel `jobs` — lihat catatan `QUEUE_CONNECTION`)

### Catatan kalau pakai VPS (bukan shared hosting)

`.htaccess` di `public/` sudah menangani proteksi folder untuk Apache — tidak perlu diubah, cukup pastikan `mod_rewrite` dan `AllowOverride All` aktif. Kalau web server-nya **Nginx** (tidak membaca `.htaccess`, perlu konfigurasi setara):

```nginx
server {
    listen 80;
    server_name domain-asli-anda.com;
    root /path/ke/project/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

`try_files $uri $uri/ /index.php?$query_string;` otomatis meneruskan folder tanpa file ke Laravel (beda dari default Nginx), jadi custom 404 tetap berfungsi tanpa konfigurasi tambahan. Di VPS, permission folder pakai `chown -R www-data:www-data storage bootstrap/cache public/media` (sesuaikan user web server-nya) karena prosesnya berjalan sebagai user server, bukan user kamu sendiri.

---

## Catatan Arsitektur

Beberapa keputusan desain yang penting diketahui sebelum mengubah kode:

- **Disk `media` kustom (bukan `storage:link`).** Lihat [bagian Disk upload](#3-disk-upload-media) di atas.
- **`server.php` di root project.** File ini meng-override router bawaan `php artisan serve` — versi asli Laravel memakai `file_exists()` untuk memutuskan apakah suatu request diteruskan ke Laravel, dan `file_exists()` bernilai benar untuk folder maupun file. Akibatnya folder seperti `/media` ditangani langsung oleh server bawaan PHP (pesan "Not Found" polos) alih-alih oleh Laravel. Versi kustom ini pakai `is_file()` supaya perilakunya konsisten dengan Apache/Nginx di production. **Hanya berlaku untuk `php artisan serve`** — tidak berpengaruh ke deployment Apache/Nginx yang punya konfigurasi sendiri (lihat bagian Deploy).
- **Tidak ada job yang di-*queue*.** `QUEUE_CONNECTION` ada di `.env` untuk kompatibilitas bawaan Laravel, tapi saat ini tidak ada satupun proses yang benar-benar butuh worker antrian berjalan.
- **View composer global (`AppServiceProvider`) di-memoize per-request.** Data situs (menu, promosi aktif, site settings) diambil sekali per request lewat properti instance provider, bukan query berulang — jangan hapus logic memoisasi ini tanpa memahami alasannya (komentar lengkap ada di file tersebut), karena tanpanya jumlah query per halaman bisa naik drastis (pernah terukur 174 query per load sebelum diperbaiki).
- **`app/Http/Kernel.php`, `app/Console/Kernel.php`, `app/Exceptions/Handler.php` ada lagi — ini disengaja, bukan salah upgrade.** Branch ini pakai Laravel 10, yang masih memakai struktur bootstrap klasik (Kernel/Handler + `app/Providers/RouteServiceProvider.php`), berbeda dari Laravel 11+ yang meringkasnya jadi satu `bootstrap/app.php` bergaya closure. Kalau melihat referensi/tutorial Laravel 11+ yang bilang "cukup edit `bootstrap/app.php`", itu **tidak berlaku** di branch ini — edit langsung ke file Kernel/Handler/Provider yang sesuai.
- **`config.platform.php` di `composer.json`.** Di-set ke `8.1.31` supaya `composer update`/`composer install` selalu memilih versi dependency yang kompatibel PHP 8.1 sungguhan, terlepas dari versi PHP CLI yang dipakai menjalankan Composer di komputer developer. Kalau versi PHP lokal developer lebih tinggi dari target hosting, tanpa override ini Composer bisa diam-diam meng-upgrade dependency ke versi yang cuma jalan di PHP lokal tapi crash di hosting — jangan dihapus.
- **`Sanctum::ignoreMigrations()` di `AppServiceProvider::register()`.** Migration tabel `personal_access_tokens` sudah di-vendor-publish & di-commit sendiri (`database/migrations/2026_08_01_..._create_personal_access_tokens_table.php`). Tanpa `ignoreMigrations()`, package Sanctum juga otomatis me-load migration bawaannya sendiri untuk tabel yang sama → bentrok "table already exists" setiap `migrate`/test.
- **Guard token API bernama `api`, bukan `sanctum`.** Lihat penjelasan lengkap di [Partner Portal & REST API](#partner-portal--rest-api-mobile) di atas — ini bukan typo.

---

## Troubleshooting

**Auto-reply email / notifikasi contact form tidak sampai ke inbox.**
Cek `MAIL_MAILER` di `.env` — kalau masih `log`, email hanya ditulis ke `storage/logs/laravel.log`, tidak benar-benar dikirim. Ganti ke SMTP asli.

**Gambar hasil upload di admin muncul broken/404.**
Pastikan folder `public/media/` writable oleh user web server. Jangan jalankan `storage:link` — disk upload di proyek ini tidak memakainya.

**Kolom negara di widget Peta Pengunjung selalu kosong.**
Cron `php artisan schedule:run` belum terpasang di server. Lihat [Scheduler](#4-scheduler-wajib-untuk-peta-pengunjung--reminder-partner).

**Perubahan di `.env` tidak berpengaruh setelah deploy.**
Config kemungkinan sudah di-cache. Jalankan `php artisan config:clear` lalu `php artisan config:cache` ulang.

**Mengakses folder seperti `/assets/` menampilkan halaman aneh, bukan 404 custom.**
Pastikan `.htaccess` bawaan `public/` ikut ter-upload dan tidak diubah (kadang tersembunyi di File Manager — aktifkan "Show Hidden Files"). Kalau pakai VPS dengan Nginx, lihat catatan konfigurasi `try_files` di bagian [Deploy](#catatan-kalau-pakai-vps-bukan-shared-hosting).

**Situs tampil error 500 blank / "Internal Server Error" setelah upload ke hosting.**
Hampir selalu karena salah satu dari: `APP_KEY` belum di-generate (jalankan `php artisan key:generate` lewat Terminal, atau salin `APP_KEY` dari `.env` lokal yang sudah pernah jalan), folder `storage/` atau `bootstrap/cache/` belum writable, atau versi PHP di hosting masih di bawah 8.1. Cek `storage/logs/laravel.log` untuk pesan error detailnya.

**Tidak ada menu Terminal/SSH di cPanel, jadi tidak bisa jalankan `artisan`.**
Lihat opsi B di [langkah upload](#4-install-dependency--jalankan-migrasi) — siapkan semuanya (composer install, migrate, key:generate, l5-swagger:generate) di komputer sendiri, lalu upload hasilnya termasuk folder `vendor/` dan `storage/api-docs/`, dan import database lewat phpMyAdmin.

**Notifikasi in-app partner (lonceng di panel `/partner`, atau `GET /api/v1/notifications`) tidak pernah muncul walau event-nya sudah terjadi (lead status berubah, klaim disetujui, dst).**
Notifikasi Filament otomatis di-*queue*, bukan dikirim langsung. Kalau `QUEUE_CONNECTION=database` dan tidak ada `php artisan queue:work` yang berjalan, notifikasi cuma tersimpan sebagai baris di tabel `jobs`, tidak pernah "diproses" jadi notifikasi sungguhan. Cek: `php artisan tinker` → `DB::table('jobs')->count()` — kalau angkanya terus naik, ganti `QUEUE_CONNECTION=sync` di `.env` (lihat [Konfigurasi](#konfigurasi)) lalu `php artisan config:clear`.

**Swagger UI (`/api/documentation`) blank putih atau error 500.**
Paling sering karena `php artisan l5-swagger:generate` belum pernah dijalankan di server itu (`storage/api-docs/` sengaja di-gitignore, tidak ikut ter-upload/ter-clone) — jalankan sekali. Kalau sudah dijalankan dan masih error, cek `storage/logs/laravel.log`: kemungkinan ada view Blade lama yang ter-publish dari versi package lain (`resources/views/vendor/l5-swagger/`) yang tidak cocok dengan versi `darkaonline/l5-swagger` yang terpasang sekarang — hapus folder itu kalau ada dan tidak sedang dikustomisasi, biarkan fallback ke view bawaan package.
