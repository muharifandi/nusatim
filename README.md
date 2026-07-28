# Nusatim — Company Profile & Blog (Laravel + Filament)

Website company profile PT. Nusantara Teknologi Inovasi Mandiri (Nusatim), dibangun dengan **Laravel 13** + **Filament v3** sebagai admin panel. Desain diadaptasi dari template statis "DigeCo" (CodeCanyon), seluruh konten (halaman, blog, layanan, portofolio, dll) dikelola lewat panel admin — tidak perlu edit kode untuk mengubah isi situs.

---

## Daftar Isi

- [Spesifikasi Minimum Server](#spesifikasi-minimum-server)
- [Instalasi (Development)](#instalasi-development)
- [Konfigurasi](#konfigurasi)
- [Deploy ke Production](#deploy-ke-production)
- [Catatan Arsitektur](#catatan-arsitektur)
- [Troubleshooting](#troubleshooting)

---

## Spesifikasi Minimum Server

| Kebutuhan | Minimum | Catatan |
|---|---|---|
| PHP | 8.3 | Dikembangkan &amp; diuji di PHP 8.4 |
| Database | MySQL 8.0+ atau MariaDB 10.3+ | |
| Composer | 2.x | |
| Web server | Apache (dengan `mod_rewrite`) — standar di hosting cPanel/LiteSpeed | Panduan di bawah ditulis untuk **shared hosting (cPanel)**; lihat catatan VPS/Nginx di bagian [Deploy](#deploy-ke-production) kalau perlu |
| Cron | Wajib | Untuk resolusi negara pengunjung di dashboard (lihat [Scheduler](#4-scheduler-wajib-untuk-peta-pengunjung)) |
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

# 7. Jalankan server development
php artisan serve
```

Buka `http://127.0.0.1:8000` untuk situs publik, dan `http://127.0.0.1:8000/admin` untuk panel admin.

**Login admin default (dibuat oleh seeder):**
- Email: `admin@nusatim.com`
- Password: `password`

> ⚠️ **Ganti password ini sebelum situs diakses publik.** Login ke `/admin`, buka menu profil di pojok kanan atas untuk mengubahnya, atau jalankan `php artisan tinker` dan update lewat model `User`.

Tidak perlu `php artisan storage:link` — lihat [Catatan Arsitektur](#disk-media-kustom-bukan-storagelink).

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

Sisanya (`SESSION_*`, `CACHE_STORE`, `QUEUE_CONNECTION`, dst) boleh dibiarkan default. **Tidak ada job yang di-*queue*** di aplikasi ini (auto-reply email dikirim langsung/sinkron), jadi `php artisan queue:work` **tidak wajib** dijalankan — aman diabaikan kecuali suatu saat ditambahkan fitur baru yang butuh antrian.

### 2. Pengaturan yang TIDAK ada di `.env` (diatur dari admin)

Sengaja dibuat begini supaya pemilik situs bisa mengubahnya sendiri tanpa akses server:

- **Site Settings** (`/admin/manage-site-settings`): nama perusahaan, logo (light/dark/mobile/footer/favicon/preloader), sosial media, Google Maps embed URL, SEO default, **Google Analytics Measurement ID**, toggle **Image Loading Placeholder**, dan toggle **Mode Coming Soon**.
- **Pages** (`/admin/pages`): teks tiap section halaman (home, about, contact, coming-soon, dst) lewat editor key-value.
- **Menus** (`/admin/menus`): struktur navigasi header/footer, termasuk dropdown & mega menu.

### 3. Disk upload (`media`)

File yang diupload lewat admin (logo, gambar blog, foto tim, dst) disimpan langsung ke `public/media/uploads/` (lihat `config/filesystems.php`, disk `media`) — **bukan** `storage/app/public` + symlink standar Laravel. Konsekuensinya:

- Folder `public/media/` (dan subfoldernya) harus **writable** oleh user web server (`www-data`, `nobody`, atau sesuai konfigurasi hosting).
- Jangan jalankan `php artisan storage:link` — tidak diperlukan dan tidak berpengaruh ke upload.

### 4. Scheduler (wajib untuk Peta Pengunjung)

Dashboard admin punya widget peta dunia yang memetakan pengunjung berdasarkan negara. Resolusi IP → negara **tidak** dilakukan saat pengunjung mengakses halaman (supaya tidak memperlambat website), melainkan lewat command terjadwal:

```php
// routes/console.php
Schedule::command('pageviews:resolve-countries --limit=100')->everyFiveMinutes();
```

Agar ini berjalan, tambahkan **satu baris cron** di server (`crontab -e`):

```
* * * * * cd /path/ke/project && php artisan schedule:run >> /dev/null 2>&1
```

Tanpa cron ini, tracking pengunjung tetap jalan (halaman tetap tercatat), tapi kolom negara di dashboard akan tetap kosong.

---

## Deploy ke Production

Panduan ini fokus ke **shared hosting berbasis cPanel** (Niagahoster, DomaiNesia, Rumahweb, Qwords, Hostinger, dst — mayoritas hosting Indonesia). Ada catatan VPS/Nginx di bagian paling bawah kalau suatu saat pindah ke sana.

### 1. Pilih versi PHP & aktifkan ekstensi

Di cPanel: **Select PHP Version** (kadang bernama **MultiPHP Manager**) → pilih domain → set ke **PHP 8.3 atau lebih baru**. Buka tab **Extensions**, pastikan aktif: `curl`, `fileinfo`, `gd`, `intl`, `mbstring`, `bcmath`, `xml`, `zip`, `pdo_mysql` — biasanya sudah aktif secara default.

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
```

Kalau **tidak ada** Terminal/SSH sama sekali (beberapa paket paling murah membatasi ini): jalankan `composer install` dan `php artisan migrate --seed` di komputer sendiri dulu (pointing ke database lokal), lalu upload seluruh project **termasuk folder `vendor/`** lewat File Manager/FTP, dan import database lokal ke database hosting lewat **phpMyAdmin** (menu cPanel). Migrasi berikutnya (kalau ada update kode) perlu diulang dengan cara yang sama.

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

Ini yang menjalankan resolusi negara pengunjung untuk widget Peta di dashboard — lihat [Scheduler](#4-scheduler-wajib-untuk-peta-pengunjung).

### 9. Setelah live

- [ ] Ganti password admin default
- [ ] Isi semua field di Site Settings (logo, kontak, sosial media, Google Analytics ID, Google Maps embed URL)
- [ ] Kirim email tes lewat form Contact untuk memastikan SMTP benar-benar terkirim (bukan cuma masuk log)
- [ ] Cek `https://domain-anda.com/sitemap.xml` bisa diakses (untuk submit ke Google Search Console)
- [ ] Pastikan Mode Coming Soon **mati** kalau situs sudah siap dilihat publik

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
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
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

---

## Troubleshooting

**Auto-reply email / notifikasi contact form tidak sampai ke inbox.**
Cek `MAIL_MAILER` di `.env` — kalau masih `log`, email hanya ditulis ke `storage/logs/laravel.log`, tidak benar-benar dikirim. Ganti ke SMTP asli.

**Gambar hasil upload di admin muncul broken/404.**
Pastikan folder `public/media/` writable oleh user web server. Jangan jalankan `storage:link` — disk upload di proyek ini tidak memakainya.

**Kolom negara di widget Peta Pengunjung selalu kosong.**
Cron `php artisan schedule:run` belum terpasang di server. Lihat [Scheduler](#4-scheduler-wajib-untuk-peta-pengunjung).

**Perubahan di `.env` tidak berpengaruh setelah deploy.**
Config kemungkinan sudah di-cache. Jalankan `php artisan config:clear` lalu `php artisan config:cache` ulang.

**Mengakses folder seperti `/assets/` menampilkan halaman aneh, bukan 404 custom.**
Pastikan `.htaccess` bawaan `public/` ikut ter-upload dan tidak diubah (kadang tersembunyi di File Manager — aktifkan "Show Hidden Files"). Kalau pakai VPS dengan Nginx, lihat catatan konfigurasi `try_files` di bagian [Deploy](#catatan-kalau-pakai-vps-bukan-shared-hosting).

**Situs tampil error 500 blank / "Internal Server Error" setelah upload ke hosting.**
Hampir selalu karena salah satu dari: `APP_KEY` belum di-generate (jalankan `php artisan key:generate` lewat Terminal, atau salin `APP_KEY` dari `.env` lokal yang sudah pernah jalan), folder `storage/` atau `bootstrap/cache/` belum writable, atau versi PHP di hosting masih di bawah 8.3. Cek `storage/logs/laravel.log` untuk pesan error detailnya.

**Tidak ada menu Terminal/SSH di cPanel, jadi tidak bisa jalankan `artisan`.**
Lihat opsi B di [langkah upload](#4-install-dependency--jalankan-migrasi) — siapkan semuanya (composer install, migrate, key:generate) di komputer sendiri, lalu upload hasilnya termasuk folder `vendor/` dan import database lewat phpMyAdmin.
