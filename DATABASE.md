# Nusatim — Database Schema Reference

Rekap seluruh tabel yang ada di database `nusatim` (MySQL/MariaDB) saat ini, per tabel — struktur kolom, tipe data, dan jumlah baris. Disusun dari `information_schema` per 28 Juli 2026.

**Ringkasan:** 25 tabel total · 17 tabel konten aplikasi · 92 baris konten (di luar log) · 8.030 baris log `page_views`.

Legenda kolom **Key**: `PK` = primary key, `UNIQUE` = nilai wajib berbeda per baris, `INDEX` = kolom terindeks (termasuk foreign key) untuk mempercepat query filter/join.

---

## Pengaturan & Halaman

### `site_settings`
Singleton (selalu 1 baris) — identitas perusahaan, logo, sosial media, SEO default, Google Analytics, mode Coming Soon, dan CTA navigasi. Diedit lewat halaman Site Settings. **1 baris.**

| Kolom | Tipe | Null | Key |
|---|---|---|---|
| id | bigint unsigned | — | PK |
| company_name | varchar(255) | — | |
| legal_name | varchar(255) | optional | |
| tagline | varchar(255) | optional | |
| email | varchar(255) | optional | |
| phone | varchar(255) | optional | |
| address | varchar(255) | optional | |
| logo_light | varchar(255) | optional | |
| logo_dark | varchar(255) | optional | |
| logo_mobile | varchar(255) | optional | |
| logo_footer | varchar(255) | optional | |
| favicon | varchar(255) | optional | |
| preloader_logo | varchar(255) | optional | logo animasi loading |
| facebook_url | varchar(255) | optional | |
| twitter_url | varchar(255) | optional | |
| instagram_url | varchar(255) | optional | |
| linkedin_url | varchar(255) | optional | |
| youtube_url | varchar(255) | optional | |
| google_maps_embed_url | text | optional | diperlebar dari varchar(255) |
| default_meta_title | varchar(255) | optional | |
| default_meta_description | varchar(500) | optional | |
| default_meta_keywords | varchar(500) | optional | |
| default_og_image | varchar(255) | optional | |
| enable_image_skeleton | tinyint(1) | — | |
| coming_soon_enabled | tinyint(1) | — | |
| google_analytics_id | varchar(255) | optional | |
| nav_cta_text | varchar(255) | optional | |
| services_explore_heading | varchar(255) | optional | |
| services_explore_image | varchar(255) | optional | |
| created_at / updated_at | timestamp | optional | |

### `pages`
Konten fleksibel per halaman (home, about, contact, coming-soon, dll) — teks section disimpan bebas di kolom JSON `content`, diakses lewat key (mis. `hero_title`). **10 baris.**

| Kolom | Tipe | Null | Key |
|---|---|---|---|
| id | bigint unsigned | — | PK |
| slug | varchar(255) | — | UNIQUE |
| name | varchar(255) | — | |
| meta_title | varchar(255) | optional | |
| meta_description | varchar(500) | optional | |
| meta_keywords | varchar(500) | optional | |
| og_image | varchar(255) | optional | |
| content | longtext (json) | optional | key-value bebas per halaman |
| created_at / updated_at | timestamp | optional | |

### `menus`
Wadah menu navigasi — 2 baris: `header` dan `footer`. **2 baris.**

| Kolom | Tipe | Null | Key |
|---|---|---|---|
| id | bigint unsigned | — | PK |
| name | varchar(255) | — | |
| slug | varchar(255) | — | UNIQUE |
| created_at / updated_at | timestamp | optional | |

### `menu_items`
Item navigasi, mendukung dropdown & mega menu bertingkat lewat `parent_id` (self-referencing, sampai 2 level). **27 baris.**

| Kolom | Tipe | Null | Key |
|---|---|---|---|
| id | bigint unsigned | — | PK |
| menu_id | bigint unsigned | — | INDEX → menus.id |
| parent_id | bigint unsigned | optional | INDEX → menu_items.id (self) |
| label | varchar(255) | — | |
| url | varchar(255) | optional | |
| type | enum(link, dropdown, mega_menu) | — | |
| icon | varchar(255) | optional | |
| image | varchar(255) | optional | |
| target | varchar(255) | — | |
| order | int unsigned | — | |
| is_active | tinyint(1) | — | |
| created_at / updated_at | timestamp | optional | |

### `promotions`
Popup promosi di homepage, dengan jadwal aktif (`starts_at`/`ends_at`) opsional. **1 baris.**

| Kolom | Tipe | Null | Key |
|---|---|---|---|
| id | bigint unsigned | — | PK |
| title | varchar(255) | optional | |
| image | varchar(255) | — | |
| link_url | varchar(255) | optional | |
| is_active | tinyint(1) | — | |
| starts_at | timestamp | optional | |
| ends_at | timestamp | optional | |
| created_at / updated_at | timestamp | optional | |

---

## Konten Bisnis

### `posts`
Artikel blog. `views_count` naik otomatis tiap dibuka (dipakai di kolom "Total Baca" admin); `tags` disimpan JSON. **21 baris.**

| Kolom | Tipe | Null | Key |
|---|---|---|---|
| id | bigint unsigned | — | PK |
| title | varchar(255) | — | |
| slug | varchar(255) | — | UNIQUE |
| excerpt | varchar(500) | optional | |
| category | varchar(255) | optional | INDEX |
| tags | longtext (json) | optional | |
| content | longtext | optional | |
| featured_image | varchar(255) | optional | |
| author_name | varchar(255) | optional | |
| user_id | bigint unsigned | optional | INDEX → users.id |
| published_at | timestamp | optional | |
| is_published | tinyint(1) | — | INDEX |
| is_featured | tinyint(1) | — | INDEX |
| views_count | int unsigned | — | |
| meta_title | varchar(255) | optional | |
| meta_description | varchar(500) | optional | |
| meta_keywords | varchar(500) | optional | |
| og_image | varchar(255) | optional | |
| created_at / updated_at | timestamp | optional | |

### `services`
Layanan yang ditawarkan. `features` (JSON, ditambahkan terbaru) menyimpan sampai 4 badge fitur per layanan — bisa berbeda tiap service, diedit lewat Repeater di admin. **6 baris.**

| Kolom | Tipe | Null | Key |
|---|---|---|---|
| id | bigint unsigned | — | PK |
| title | varchar(255) | — | |
| slug | varchar(255) | — | UNIQUE |
| icon | varchar(255) | optional | |
| image | varchar(255) | optional | |
| short_description | varchar(500) | optional | |
| content | longtext | optional | |
| features | longtext (json) | optional | array {icon, title, color} |
| order | int unsigned | — | |
| is_active | tinyint(1) | — | |
| meta_title | varchar(255) | optional | |
| meta_description | varchar(500) | optional | |
| og_image | varchar(255) | optional | |
| created_at / updated_at | timestamp | optional | |

### `projects`
Portofolio/karya untuk halaman Portfolio. **3 baris.**

| Kolom | Tipe | Null | Key |
|---|---|---|---|
| id | bigint unsigned | — | PK |
| title | varchar(255) | — | |
| slug | varchar(255) | — | UNIQUE |
| category | varchar(255) | optional | |
| image | varchar(255) | — | |
| description | longtext | optional | |
| order | int unsigned | — | |
| is_active | tinyint(1) | — | |
| meta_title | varchar(255) | optional | |
| meta_description | varchar(500) | optional | |
| og_image | varchar(255) | optional | |
| created_at / updated_at | timestamp | optional | |

### `pricing_plans`
Paket harga. `features` berisi daftar poin JSON; `is_highlighted` menandai paket yang ditonjolkan. **3 baris.**

| Kolom | Tipe | Null | Key |
|---|---|---|---|
| id | bigint unsigned | — | PK |
| name | varchar(255) | — | |
| price | decimal(12,2) | — | |
| currency | varchar(10) | — | |
| period | varchar(255) | optional | |
| features | longtext (json) | optional | |
| cta_text | varchar(255) | — | |
| cta_url | varchar(255) | optional | |
| is_highlighted | tinyint(1) | — | |
| highlight_color | varchar(255) | optional | |
| order | int unsigned | — | |
| is_active | tinyint(1) | — | |
| created_at / updated_at | timestamp | optional | |

### `team_members`
Anggota tim untuk halaman Team, dengan tautan sosial media perorangan. **3 baris.**

| Kolom | Tipe | Null | Key |
|---|---|---|---|
| id | bigint unsigned | — | PK |
| name | varchar(255) | — | |
| position | varchar(255) | optional | |
| photo | varchar(255) | optional | |
| bio | longtext | optional | |
| facebook_url | varchar(255) | optional | |
| twitter_url | varchar(255) | optional | |
| linkedin_url | varchar(255) | optional | |
| instagram_url | varchar(255) | optional | |
| order | int unsigned | — | |
| is_active | tinyint(1) | — | |
| created_at / updated_at | timestamp | optional | |

### `testimonials`
Testimoni klien dengan rating bintang, tampil di carousel homepage. **1 baris.**

| Kolom | Tipe | Null | Key |
|---|---|---|---|
| id | bigint unsigned | — | PK |
| name | varchar(255) | — | |
| position | varchar(255) | optional | |
| photo | varchar(255) | optional | |
| quote | longtext | — | |
| rating | tinyint unsigned | — | |
| order | int unsigned | — | |
| is_active | tinyint(1) | — | |
| created_at / updated_at | timestamp | optional | |

### `faqs`
Pertanyaan umum untuk halaman FAQ (juga dipakai untuk structured data FAQPage). **3 baris.**

| Kolom | Tipe | Null | Key |
|---|---|---|---|
| id | bigint unsigned | — | PK |
| question | varchar(255) | — | |
| answer | longtext | — | |
| order | int unsigned | — | |
| is_active | tinyint(1) | — | |
| created_at / updated_at | timestamp | optional | |

### `clients`
Logo klien/partner — dirender di carousel brand yang tampil di hampir semua halaman. **6 baris.**

| Kolom | Tipe | Null | Key |
|---|---|---|---|
| id | bigint unsigned | — | PK |
| name | varchar(255) | — | |
| logo | varchar(255) | — | |
| website_url | varchar(255) | optional | |
| order | int unsigned | — | |
| is_active | tinyint(1) | — | |
| created_at / updated_at | timestamp | optional | |

---

## Komunikasi & Traffic

### `contact_messages`
Pesan masuk dari form Contact. Auto-reply email dikirim ke pengirim saat baris ini dibuat. **3 baris.**

| Kolom | Tipe | Null | Key |
|---|---|---|---|
| id | bigint unsigned | — | PK |
| name | varchar(255) | — | |
| email | varchar(255) | — | |
| phone | varchar(255) | optional | |
| subject | varchar(255) | optional | |
| message | longtext | — | |
| is_read | tinyint(1) | — | |
| created_at / updated_at | timestamp | optional | |

### `newsletter_subscribers`
Email pendaftar. Saat ini terisi lewat popup "Notify Us" di halaman Coming Soon; `source` mencatat asalnya untuk kalau ada titik pendaftaran lain nanti. **1 baris.**

| Kolom | Tipe | Null | Key |
|---|---|---|---|
| id | bigint unsigned | — | PK |
| email | varchar(255) | — | UNIQUE |
| source | varchar(255) | optional | mis. "coming-soon" |
| created_at / updated_at | timestamp | optional | |

### `page_views`
Log satu baris per kunjungan halaman nyata — menyalakan chart Traffic Pengunjung dan Peta Negara di dashboard. Resolusi negara dari IP berjalan async lewat scheduled job, bukan saat request masuk. **8.030 baris.**

| Kolom | Tipe | Null | Key |
|---|---|---|---|
| id | bigint unsigned | — | PK |
| path | varchar(255) | — | INDEX |
| url | varchar(255) | — | |
| ip_address | varchar(45) | optional | INDEX |
| country_code | varchar(2) | optional | INDEX |
| country_name | varchar(255) | optional | |
| referrer | varchar(255) | optional | |
| user_agent | varchar(255) | optional | |
| post_id | bigint unsigned | optional | INDEX → posts.id |
| viewed_at | timestamp | — | INDEX |

---

## Akses

### `users`
Akun admin. Password di-hash (bcrypt), tidak pernah disimpan/ditampilkan dalam bentuk asli. **1 baris.**

| Kolom | Tipe | Null | Key |
|---|---|---|---|
| id | bigint unsigned | — | PK |
| name | varchar(255) | — | |
| email | varchar(255) | — | UNIQUE |
| email_verified_at | timestamp | optional | |
| password | varchar(255) | — | hashed |
| remember_token | varchar(100) | optional | |
| created_at / updated_at | timestamp | optional | |

---

## Internal Laravel

Tabel bawaan framework — bukan konten aplikasi, tidak diedit lewat admin, dan tidak butuh rincian kolom (sudah standar Laravel). Disebut ringkas di sini demi kelengkapan rekap.

| Tabel | Fungsi |
|---|---|
| sessions | Sesi login pengguna aktif |
| cache | Cache aplikasi (key/value) |
| cache_locks | Lock internal untuk cache atomik |
| jobs | Antrian background job (mis. kirim email) |
| job_batches | Pengelompokan batch job |
| failed_jobs | Job yang gagal dieksekusi |
| password_reset_tokens | Token sekali-pakai untuk reset password |
| migrations | Riwayat migrasi skema yang sudah dijalankan |

---

*Sumber: `information_schema.COLUMNS` pada database `nusatim` (MySQL/MariaDB), digabung dengan hitungan baris langsung per tabel.*
