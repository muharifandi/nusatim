# TODO — Portal Partner / Sales Partner Management

Breakdown kerja untuk pengembangan **Portal Partner/Affiliator** + modul tambahan di **Admin existing**. Sumber: spec "Final Rekapan Fitur – Portal Partner/Sales Partner Management" (lihat ringkasan modul di bagian paling bawah file ini).

Status semua item: belum dikerjakan (`[ ]`). Centang (`[x]`) begitu selesai. Urutan fase disusun berdasarkan dependency teknis (fondasi dulu, baru fitur yang bergantung padanya) — bukan urutan penomoran di spec asli.

## Progress

| Fase | Status | Tanggal |
|---|---|---|
| Fase 0 — Keputusan Arsitektur | ✅ Selesai (bagian teknis) | 2026-07-29 |
| Fase 1 — Registrasi & Autentikasi Partner | ✅ Selesai | 2026-07-29 |
| Fase 2 — Dashboard Partner | ⏸️ Ditunda (perlu data Fase 3/4/8/9) | — |
| Fase 3 — Lead & Opportunity Management | ✅ Selesai | 2026-07-29 |
| Fase 4 — Customer Management | ✅ Selesai | 2026-07-29 |
| Fase 6 — Sales Pipeline (Kanban) | ✅ Selesai | 2026-07-29 |
| Fase 7 — Project Board (Available Project) | ✅ Selesai | 2026-07-29 |
| Fase 8 — Project Management | ⏸️ Ditunda (2 sumber data sudah nyambung sejak Fase 9, tinggal tunggu data lebih banyak) | — |
| Fase 9 — Commission Management (sisi Partner) | ✅ Selesai | 2026-07-29 |
| Fase 10 — Withdrawal (Partner) | ✅ Selesai | 2026-07-29 |
| Fase 11 — Withdrawal History | ✅ Selesai (dikerjakan bareng Fase 10) | 2026-07-29 |
| Fase 16 (Admin) — Project Board Management | ✅ Selesai (dikerjakan bareng Fase 7, bukan cuma preview) | 2026-07-29 |
| Fase 17 (Admin) — Lead Monitoring | ⚠️ Preview minimal (dikerjakan bareng Fase 3/4) | 2026-07-29 |
| Fase 18 (Admin) — Commission Scheme Management | ✅ Selesai (dikerjakan bareng Fase 9) | 2026-07-29 |
| Fase 19 (Admin) — Commission Management (sisi Admin) | ✅ Selesai (dikerjakan bareng Fase 9) | 2026-07-29 |
| Fase 20 (Admin) — Withdrawal Management | ✅ Selesai (dikerjakan bareng Fase 10) | 2026-07-29 |
| Fase 12 — Marketing Center | ✅ Selesai | 2026-07-29 |
| Fase 21 (Admin) — Marketing Material | ✅ Selesai (dikerjakan bareng Fase 12) | 2026-07-29 |
| Fase 13 — Notification Center | ✅ Selesai | 2026-07-30 |
| Fase 14 — Profile Partner | ✅ Selesai | 2026-07-30 |
| Fase 15 (Admin) — Partner Management | ⚠️ Preview minimal (dikerjakan bareng Fase 1) | 2026-07-29 |
| Fase 22 (Admin) — Reports | ✅ Selesai | 2026-07-30 |
| Fase 5, 23 lainnya | belum dikerjakan | — |

**Semua modul partner-facing selesai** kecuali Fase 2/5/8 yang sengaja ditunda. Sisa pekerjaan: Fase 23 (satu-satunya fase admin yang belum disentuh sama sekali).

**Berikutnya**: Fase 23 (Partner Settings, versi lengkap) — sebagian besar field-nya (Commission Scheme Default, Project Claim Rule, Notifikasi) bisa dibangun sekarang. "Pengaturan Workflow Approval" tetap nyangkut ke keputusan bisnis yang belum dijawab sejak Fase 0 (siapa approve apa) — akan dilewati/dibuat placeholder, bukan menunda seluruh fase. Fase 5 (Sales Workspace) masih sengaja dilewati (murni agregat dari data Fase 3/4/7/8/9/10).

---

## Fase 0 — Keputusan Arsitektur (wajib selesai sebelum mulai coding)

Ini bukan fitur, tapi keputusan desain yang akan menentukan struktur seluruh modul di bawahnya. Jangan mulai Fase 1 sebelum poin-poin ini diputuskan.

### Sudah diverifikasi langsung ke database & kode existing (bukan asumsi)

- [x] **Cek collision nama tabel** — sudah dibandingkan ke 25 tabel yang ada sekarang (`cache`, `clients`, `contact_messages`, `faqs`, `menus`, `menu_items`, `newsletter_subscribers`, `pages`, `page_views`, `posts`, `pricing_plans`, `projects`, `promotions`, `services`, `site_settings`, `team_members`, `testimonials`, `users`, dst — lihat `DATABASE.md`). **Tidak ada collision** untuk nama tabel yang diusulkan di fase-fase bawah (`partners`, `leads`, `customers`, `partner_projects`, `commission_schemes`, `commissions`, `marketing_materials`), **kecuali** dua titik mirip berikut yang perlu perhatian ekstra, bukan sekadar beda nama tabel:
  - ⚠️ **`clients` (existing) vs `customers` (baru, Fase 4) — konsepnya BEDA, jangan disatukan.** Tabel `clients` yang sudah ada itu logo perusahaan partner/klien untuk carousel kepercayaan di homepage situs profile (marketing) — bukan data penjualan. Tabel `customers` baru adalah customer hasil closing lead oleh sales partner. Nama mirip tapi domain beda total; pertahankan sebagai 2 tabel terpisah seperti sudah direncanakan di fase-fase bawah, tapi beri komentar jelas di migration supaya developer berikutnya tidak bingung/menggabungkan keduanya.
  - ⚠️ **`services` (existing) kemungkinan besar = "Produk" yang disebut berulang di spec** (di Lead, Customer, Commission Scheme, dst — spec tidak pernah mendefinisikan apa itu "Produk"). Tabel `services` sekarang isinya katalog layanan Nusatim sendiri (Web Marketing, Development, Creative Layout, dll — 6 baris saat ini). Kemungkinan besar partner MENJUAL layanan yang sama ini, bukan katalog produk terpisah. **Rekomendasi teknis: reuse `services`, tambah relasi `service_id` di tabel yang butuh "Produk"** (leads, customers, partner_projects, commission_schemes) — jangan bikin tabel `products` baru kecuali dikonfirmasi memang beda dari layanan yang sudah ada. **Ini masih perlu dikonfirmasi ke pemberi spec**, tapi jangan mulai Fase 3 sebelum diputuskan karena field "Produk" muncul di hampir semua modul.
- [x] **Cek trait `DeletesOldFiles` (dipakai 7 model existing untuk auto-hapus file lama saat upload diganti)** — trait ini **hardcode ke `Storage::disk('media')`**, tidak menerima parameter disk. Karena dokumen KYC partner (KTP/NPWP/bukti transfer) wajib di disk privat (lihat poin di bawah), trait ini **tidak bisa dipakai langsung** untuk model partner — perlu diparameterkan (terima nama disk) atau dibuat trait sejenis khusus disk privat sebelum dipakai di Fase 1/10/20.
- [x] **Cek trait `RedirectsToResourceIndex`** (dibuat untuk redirect Create/Edit Filament kembali ke halaman list) — trait ini generic, tidak terikat resource tertentu. **Bisa langsung dipakai apa adanya** di semua Create/Edit page resource admin baru di modul partner (Fase 15–21), tanpa modifikasi.
- [x] **Cek pola listing aktif/urutan** — 7 model existing (`Service`, `Project`, `TeamMember`, `PricingPlan`, `Faq`, `Testimonial`, `Client`) konsisten pakai kolom `is_active` + `order` + method `scopeActive()`. Ikuti pola yang sama untuk tabel baru yang butuh publish/urutan tampil, misalnya `commission_schemes` dan `marketing_materials`.
- [x] **Cek pola singleton settings** — `SiteSetting::current()` + `ManageSiteSettings` (satu Filament Page custom dengan form + `save()`, bukan Resource CRUD) adalah pola yang sudah dipakai untuk pengaturan situs profile. **Fase 23 "Partner Settings" harus ikut pola ini persis** (satu halaman pengaturan global, bukan resource list/create/edit biasa), karena sifatnya sama-sama singleton (satu set pengaturan berlaku untuk semua partner, bukan banyak baris data).
- [x] **Cek pola resource sederhana (`ManageRecords`, satu halaman list + modal CRUD, dipakai `NewsletterSubscriberResource`)** — cocok dipakai untuk resource dengan sedikit field dan tidak butuh halaman detail sendiri (kandidat: `marketing_materials`). Resource dengan banyak field/relasi (Partner, Lead, Project) tetap pakai pola List/Create/Edit penuh seperti resource lain yang sudah ada.

### Sudah diputuskan & diimplementasikan (bersama Fase 1)

- [x] **Model akses**: guard `partner` + provider `partners` + model `Partner` terpisah total dari `User`/`web` (satu database yang sama). Sudah diverifikasi test: akun partner tidak bisa masuk `/admin`, akun `User` staff tidak bisa masuk `/partner`.
- [x] **Panel admin untuk modul baru**: tetap satu panel admin (`/admin`), grup navigasi baru **"Partner Program"** (bukan panel Filament terpisah). Portal partner sendiri jadi panel Filament KEDUA (`/partner`, guard `partner`) — beda dari panel admin.
- [x] **Penyimpanan dokumen sensitif**: disk privat baru `partner_documents` (`storage_path('app/partner-documents')`, tanpa `url` publik), file di-serve lewat `PartnerDocumentController` (route `partner.documents.show`) yang mengecek: partner hanya boleh lihat dokumennya sendiri, admin (`web` guard) boleh lihat semua. Diverifikasi test.

### Masih genuinely open — butuh jawaban dari pemberi spec, bukan keputusan teknis

- [ ] **Audit trail untuk data uang**: komisi & withdrawal tidak boleh sekadar kolom `status` yang di-update in-place tanpa jejak. Rencanakan tabel histori/log perubahan status (siapa approve, kapan, alasan reject, dll) sejak awal — bukan ditambah belakangan. Belum relevan di Fase 1 (tidak ada data uang di sini), tapi wajib diputuskan sebelum Fase 9/10/19/20.
- [ ] **Precision angka uang**: pastikan semua kolom nominal pakai `decimal`, bukan `float`/`double` (mengulang standar yang sudah dipakai di `pricing_plans.price`). Belum relevan di Fase 1.
- [ ] **Definisi "Level Partner"**: spec menyebut "Level Partner" di modul admin tapi tidak dijelaskan levelnya apa saja atau pengaruhnya ke apa (komisi berbeda? akses fitur berbeda?) — perlu klarifikasi dari pemberi spec sebelum dikerjakan. Kolom `level` sudah ada di tabel `partners` (nullable, diedit bebas dari admin) sebagai placeholder sampai definisinya jelas.
- [ ] **Definisi role approval**: siapa yang approve apa? (registrasi partner, claim project, komisi, withdrawal — apakah semua admin bisa approve semua, atau ada pemisahan role/permission?) Fase 1 sementara pakai "siapa saja yang login ke `/admin` boleh approve/reject partner" — belum ada pemisahan role karena `spatie/laravel-permission` belum terpasang.
- [ ] **Definisi "Produk"**: konfirmasi apakah benar reuse tabel `services` existing (lihat poin verifikasi di atas) atau memang perlu katalog produk baru yang terpisah dari layanan situs profile. Belum relevan di Fase 1, wajib diputuskan sebelum Fase 3.

---

## Fase 1 — Registrasi & Autentikasi Partner ✅ (selesai 2026-07-29)

- [x] Migrasi tabel `partners` (data akun: nama, email, password, status registrasi, level, dst)
- [x] ~~Migrasi tabel dokumen partner~~ — digabung jadi kolom langsung di `partners` (`profile_photo_path`, `ktp_path`, `npwp_path`, disk privat `partner_documents`), bukan tabel terpisah. Lihat keputusan di Fase 0.
- [x] ~~Migrasi tabel rekening bank partner~~ — digabung jadi kolom langsung di `partners` (`bank_name`, `bank_account_number`, `bank_account_holder`). Lihat keputusan di Fase 0.
- [x] Halaman Registrasi akun (form publik, tanpa login) — panel Filament kedua (`/partner/register`), wizard 4 langkah: Akun → Dokumen → Rekening Bank → Perjanjian
- [x] Halaman Login partner (`/partner/login`, guard `partner` terpisah total dari `/admin`)
- [x] Fitur Lupa Password (reset via email) — bawaan Filament `passwordReset()`, broker `partners` sendiri
- [x] Upload Foto Profil
- [x] Upload KTP
- [x] Upload NPWP (opsional, boleh dikosongkan)
- [x] Input Data Rekening
- [x] Halaman/modal Persetujuan Perjanjian Kemitraan (checkbox wajib centang, teksnya diambil dari `PartnerSetting::current()` — editable admin di `/admin/manage-partner-settings`)
- [x] State machine status registrasi: `Pending Review` → `Approved` / `Rejected` (`Draft` diperlakukan sebagai progres wizard di client, bukan row DB tersendiri — lihat keputusan Fase 0)
- [x] Notifikasi email ke calon partner saat status berubah (approved/rejected) — 3 Mailable (`PartnerRegistrationReceived/Approved/Rejected`)
- [x] Halaman "menunggu approval" yang ditampilkan ke partner selama status masih `Pending Review` (`PartnerStatus` page, juga menampilkan alasan kalau `Rejected`)

Dikerjakan bersama fondasi teknis Fase 0 (guard `partner`, panel Filament kedua, disk privat `partner_documents`, trait `DeletesOldFiles` digeneralisasi, `PartnerSetting` singleton, `PartnerResource` admin minimal untuk approve/reject). Diverifikasi lewat `tests/Feature/PartnerRegistrationTest.php` (7 test: wizard registrasi, isolasi guard dua arah, gating status, kepemilikan dokumen privat) — sempat menemukan bug nyata (infinite redirect loop karena panel partner belum punya halaman navigable sebelum `Filament\Pages\Dashboard` didaftarkan), sudah diperbaiki.

---

## Fase 2 — Dashboard Partner ⏸️ (sengaja ditunda)

**Belum dikerjakan, bukan lupa.** Semua isi fase ini adalah angka ringkasan/grafik dari tabel yang saat Fase 1 selesai belum ada sama sekali (`leads`, `customers`, `partner_projects`, `commissions`). Membangunnya lebih dulu cuma menghasilkan halaman kosong yang harus dibongkar ulang begitu data aslinya ada. Placeholder `Filament\Pages\Dashboard` bawaan (dipasang di Fase 1 supaya panel partner tidak infinite-redirect) dipakai sementara. Item "Total Lead"/"Total Customer" di bawah ini sudah bisa dikerjakan sekarang setelah Fase 3+4 selesai (datanya sudah ada) — sisanya (Project/Komisi/Withdrawal) menunggu fase terkait.

- [ ] Query/summary: Total Lead, Total Opportunity, Total Customer, Total Project
- [ ] Query/summary: Project Available (jumlah project yang bisa diklaim)
- [ ] Query/summary: Follow Up Hari Ini, Meeting Hari Ini (dari data reminder di Fase 3)
- [ ] Query/summary: Total Nilai Project
- [ ] Query/summary: Total Komisi, Komisi Pending, Komisi Ready Withdrawal, Total Withdrawal
- [ ] Target Penjualan (input target oleh admin per partner/periode, ditampilkan progress-nya)
- [ ] Grafik Pipeline (jumlah lead per tahapan pipeline)
- [ ] Grafik Closing (tren closing per periode)
- [ ] Grafik Komisi (tren komisi per periode)

> Bisa reuse pola chart yang sudah ada di dashboard admin situs profile ini (`TrafficChart` widget) sebagai referensi teknis — tapi ini widget/halaman terpisah, bukan bagian dari dashboard admin existing.

---

## Fase 3 — Lead & Opportunity Management ✅ (selesai 2026-07-29)

- [x] Migrasi tabel `leads` (relasi ke partner pemilik)
- [x] Tambah Lead (form) — `/partner/leads/create`
- [x] Edit Lead
- [x] Halaman Detail Lead (`ViewLead` — pola 4-halaman List/Create/Edit/View, pertama di project ini)
- [x] Upload Dokumen di Lead (tabel `lead_documents`, disk privat `lead_documents` — terpisah dari `partner_documents`, di-serve `LeadDocumentController` dengan cek kepemilikan)
- [x] Timeline aktivitas per Lead (tabel `lead_activities`, auto-log lewat model event `created`/`status_change`)
- [x] Catatan Internal (`lead_activities` type=`note`, ditambah lewat action "Tambah Catatan" di relation manager Timeline)
- [x] Reminder Follow Up (tabel `lead_reminders` type=`follow_up`)
- [x] Reminder Meeting (`lead_reminders` type=`meeting`)
- [x] State machine status lead: `new → contacted → qualified → opportunity → proposal → negotiation → won / lost`
- [x] Saat status jadi `Won`: trigger otomatis pembuatan record Customer (Fase 4) — ada di model event, jadi tidak bisa dilewati lewat jalur manapun (bukan cuma lewat tombol "Tandai Won")

Belum ada di scope ini: reminder belum benar-benar "muncul di dashboard & notifikasi" (menunggu Fase 2 Dashboard + Fase 13 Notification Center — datanya sudah siap dipakai begitu fase itu dikerjakan).

---

## Fase 4 — Customer Management ✅ (selesai 2026-07-29)

- [x] Migrasi tabel `customers` (partner pemilik, sumber dari lead yang closing)
- [x] Halaman Profil Customer (`ViewCustomer`)
- [x] Data PIC customer
- [x] Data Kontak — **diinterpretasikan sebagai orang yang sama dengan PIC** (`pic_name`/`pic_phone`/`pic_email`), bukan dua entitas kontak terpisah. Perlu dikonfirmasi kalau ternyata dimaksudkan beda.
- [x] Data Produk yang dibeli (`service_id`, reuse tabel `services` — sama seperti di Lead)
- [x] Nilai Project per customer
- [x] Status Pembayaran (`unpaid`/`partial`/`paid`)
- [x] Riwayat Aktivitas customer (gabungan timeline dari lead asal + aktivitas setelah jadi customer) — `Customer::activityTimeline()`, ditampilkan lewat Infolist `RepeatableEntry` (bukan RelationManager karena sumbernya gabungan 2 relasi, bukan 1 relasi tunggal)

Dikerjakan bersama Fase 17 (Admin) versi minimal (Lead Monitoring: validasi + transfer ownership + peringatan duplikat sederhana berdasarkan phone/email yang sama) supaya kedua fase ini bisa diuji end-to-end dari sisi admin juga — sama seperti pola preview Fase 15 saat Fase 1. Anti-duplicate masih versi sederhana (tampilkan daftar lead lain dengan kontak sama), belum fuzzy-matching penuh.

Diverifikasi lewat `tests/Feature/LeadManagementTest.php` (7 test: create lead + auto-log timeline, Won→Customer idempotent, Lost tidak membuat Customer, partner tidak bisa lihat/edit lead partner lain, kepemilikan dokumen privat, render halaman View Lead & Customer end-to-end, admin validate+transfer ownership).

---

## Fase 5 — Sales Workspace

- [ ] Rancang satu halaman gabungan (workspace) per customer/project yang menampilkan semua modul sekaligus tanpa pindah halaman:
  - [ ] Panel Informasi Customer
  - [ ] Panel Timeline
  - [ ] Panel Aktivitas
  - [ ] Panel Follow Up
  - [ ] Panel Meeting
  - [ ] Panel Proposal
  - [ ] Panel Catatan
  - [ ] Panel Nilai Project
  - [ ] Panel Status Project
  - [ ] Panel Status Pembayaran
  - [ ] Panel Status Komisi

> Ini murni UI/UX — datanya semua sudah ada dari Fase 3, 4, 8, 9. Kerjakan setelah fase-fase itu selesai, supaya tidak membangun data ulang.

---

## Fase 6 — Sales Pipeline (Kanban) ✅ (selesai 2026-07-29)

- [x] Tampilan Kanban board dengan kolom sesuai status lead (`New` s.d. `Lost`, sama seperti Fase 3) — halaman baru `/partner/pipeline`
- [x] Drag & drop card lead antar kolom → update status — native HTML5 Drag and Drop API (tidak nambah dependency baru), status di-update lewat `Lead::update()` biasa jadi otomatis kena hook logging timeline + auto-Customer kalau di-drop ke kolom Won (reuse persis logic dari Fase 3, bukan logic baru)
- [x] Filter pipeline (per periode, per produk, dst) — filter Produk (dropdown `services`) dan rentang tanggal (dari/sampai), berdasar `created_at`

Server-side re-cek kepemilikan lead di `moveLead()` (bukan cuma UI yang disembunyikan) — partner tidak bisa pindahkan lead milik partner lain walau tahu ID-nya, diverifikasi test.

**Bug ditemukan & diperbaiki saat mengerjakan fase ini**: form/tabel Produk di `LeadResource`, `CustomerResource` (Fase 3/4, sudah live sebelumnya) salah pakai `service.name`/`pluck('name','id')` — kolom asli di tabel `services` adalah `title`, bukan `name`. Akibatnya dropdown & kolom "Produk" tampil kosong walau `service_id` tersimpan benar di database. Sudah diperbaiki di 3 file itu.

Diverifikasi lewat `tests/Feature/SalesPipelineTest.php` (5 test: drag ke Won → status+timeline+Customer, status tidak valid ditolak, partner tidak bisa pindahkan lead partner lain, filter produk mengurangi hasil, halaman render semua 8 kolom).

---

## Fase 7 — Project Board (Available Project) ✅ (selesai 2026-07-29)

- [x] Migrasi tabel `partner_projects` (project yang dibuka admin, bisa diklaim partner)
- [x] Listing project: Nama, Produk, Budget, Lokasi, Deadline, Tingkat Kesulitan, Nilai Komisi, Status — `/partner/partner-projects`
- [x] Halaman Detail Project
- [x] Fitur Claim Project (partner mengajukan klaim) — pakai conditional update (bukan cek-lalu-simpan) supaya dua partner yang klaim bersamaan tidak bisa dua-duanya berhasil
- [x] Fitur Batalkan Claim (hanya selama status masih `Pending Approval`, belum diproses admin)
- [x] State machine status: `draft → available → pending_approval → assigned → in_progress → closed / cancelled` (tambah `draft` di depan `available`, lihat Fase 16 di bawah)
- [x] Notifikasi ke partner saat klaim disetujui/ditolak admin — `PartnerProjectClaimApproved`/`Rejected` (pola sama seperti mail Fase 1)

Catatan: "Nilai Komisi" (`commission_value`) di sini murni angka referensi/preview untuk partner yang browsing board — bukan hasil hitungan skema komisi sungguhan (Commission Scheme belum dibangun, itu Fase 9/18/19).

Diverifikasi lewat `tests/Feature/PartnerProjectTest.php` (8 test): board partner cuma nampilin project available + milik sendiri, race condition klaim, cancel claim, approve/reject claim + email, admin assign langsung, render halaman partner & admin.

**Bug produksi ditemukan & diperbaiki saat mengerjakan fase ini**: model `User` (staff/admin) ternyata tidak pernah implement `FilamentUser`. Efeknya, Filament fallback ke aturan "boleh akses `/admin` HANYA kalau `APP_ENV=local`" — di server manapun selain lokal (staging, production, `testing`) admin **tidak akan pernah bisa login ke `/admin` sama sekali**, tanpa pesan error yang jelas (403 kosong). Ketahuan justru karena test Fase 7 ini yang pertama kali benar-benar hit route admin lewat HTTP asli dengan user login (test fase-fase sebelumnya semua lewat `Livewire::test()` yang tidak kena middleware ini). Sudah diperbaiki: `User` implement `FilamentUser::canAccessPanel()` return `true` (menyamai perilaku yang memang sudah berjalan di lokal — belum ada pemisahan role/permission).

**Update dari Fase 9**: `partner_projects` yang `Assigned` sekarang otomatis bikin/hubungkan `Customer` (kolom baru `customers.partner_project_id`) — lihat catatan lengkap di Fase 9. Ini relevan untuk Fase 8 di bawah karena dua sumber datanya sekarang sudah tersambung.

---

## Fase 8 — Project Management ⏸️ (sengaja ditunda)

**Belum dikerjakan, bukan lupa** — roll-up view dari data project/customer. **Update dari Fase 9**: alasan awal fase ini ditunda (2 sumber data terpisah tanpa hubungan) **sudah tidak berlaku lagi** — sejak Fase 9, `PartnerProject` yang `Assigned` otomatis bikin/hubungkan `Customer` (`customers.partner_project_id`), jadi sekarang tinggal query `Customer` (opsional join ke `partnerProject` untuk data status/progress project-nya) sebagai satu sumber. Tetap ditunda bukan karena masalah arsitektur lagi, tapi supaya ada data asli lebih banyak dulu (dari Fase 3/4/7) sebelum bangun UI ringkasannya. Kolom `progress` sudah disiapkan di tabel `partner_projects` (default 0) supaya tidak perlu migration tambahan nanti.

- [ ] Listing project yang sudah terjual (hasil dari Fase 7 yang `Assigned`/`In Progress`, atau dari closing Lead di Fase 3)
- [ ] Tampilkan: Nama Project, Customer, Produk, Nilai Project, Status Pembayaran, Status Project, Progress
- [ ] Update progress project (oleh partner atau admin — masih belum diputuskan, lihat Fase 0)

---

## Fase 9 — Commission Management (sisi Partner) ✅ (selesai 2026-07-29)

- [x] Migrasi tabel `commission_schemes` (lihat 3 jenis skema di bawah)
- [x] Migrasi tabel `commissions` (per project/invoice, relasi ke scheme yang dipakai + histori status)
- [x] Listing komisi: Project, Produk, Skema Komisi, Nilai Project, Nilai Invoice, Persentase, Nominal Komisi, Status — `/partner/commissions`
- [x] State machine status komisi: `Pending` → `Waiting Client Payment` → `Approved` → `Paid` / `Rejected`

### Implementasi 3 jenis skema komisi

- [x] **Percentage** — komisi = persentase × nilai project (sekali hitung, saat closing). Contoh: 10% × Rp100.000.000.
- [x] **Recurring Percentage** — ⚠️ **tipe skema-nya sudah bisa dipilih & disimpan**, tapi mesin hitung ulang otomatis tiap ada pembayaran baru **belum dibangun** — sistem ini belum punya tabel invoice/payment sama sekali. Generate untuk skema ini sekarang cuma menghasilkan satu baris komisi awal (diperlakukan seperti Percentage biasa). Butuh fase baru "Invoice/Payment Tracking" + keputusan produk sebelum benar-benar berfungsi sesuai deskripsi asli.
- [x] **Flat Commission** — nominal tetap per unit penjualan, tidak bergantung nilai project. Contoh: setiap penjualan Produk A → komisi Rp2.000.000 (tetap, walau harga produk A berbeda-beda).

> Ketiga skema ini harus bisa hidup berdampingan — satu produk/partner/project bisa punya skema berbeda dari produk/partner/project lain (lihat Fase "Commission Scheme Management" di sisi Admin).

**Perbaikan arsitektur yang dilakukan sekalian di fase ini**: sebelum ini, sistem punya 2 sumber "deal tertutup" yang terpisah dan tidak terhubung — `Customer` (dari Lead Won, Fase 3/4) dan `PartnerProject` yang `Assigned` (dari klaim Project Board, Fase 7). Komisi butuh satu sumber pasti untuk dihitung, jadi sekarang `PartnerProject::approveClaim()` dan `assignDirectly()` (assign langsung admin) otomatis membuat/menghubungkan `Customer` juga — persis pola `Lead::markWon()`. `customers` dapat kolom baru `partner_project_id` (nullable, unik). Setelah ini, **`Customer` adalah satu-satunya sumber "deal tertutup"** di seluruh sistem, apapun asalnya — ini juga menyelesaikan sebagian gap yang bikin Fase 8 ditunda.

**Audit trail untuk data uang** (di-flag sejak Fase 0, baru relevan sekarang): tabel `commission_status_histories` terpisah, terisi otomatis lewat model event tiap kali status komisi berubah — bukan sekadar kolom `status` yang di-update begitu saja.

Diverifikasi lewat `tests/Feature/CommissionManagementTest.php` (8 test): auto-create Customer dari PartnerProject (idempotent), prioritas resolusi skema (Project > Partner > Produk > Global), hitung nominal Percentage & Flat benar + idempotent, audit trail status berjalan, reject tercatat alasannya, scoping partner, admin bonus komisi tanpa scheme, render halaman partner & admin.

**Bug test-writing ditemukan & diperbaiki saat menulis test fase ini** (bukan bug aplikasi): `actingAs($user)` tanpa guard eksplisit setelah `actingAs($partner, 'partner')` di test method yang sama ikut ke-set ke guard `partner` juga — ternyata `Illuminate\Auth\AuthManager::shouldUse(null)` fallback ke `config('auth.defaults.guard')` yang sudah "tertular" ke `partner` dari pemanggilan sebelumnya. Perbaikan: selalu isi guard eksplisit (`actingAs($admin, 'web')`) di test manapun yang mencampur kedua guard.

---

## Fase 10 — Withdrawal (Partner) ✅ (selesai 2026-07-29)

- [x] Hitung & tampilkan saldo tersedia (komisi berstatus `Approved`/ready withdrawal) — `Partner::availableBalance()`, divalidasi ulang server-side saat submit (bukan cuma ditampilkan)
- [x] Ambil aturan Minimum Withdrawal dari pengaturan admin (Fase Partner Settings) — field `minimum_withdrawal` ditambahkan ke `partner_settings` (potongan minimal dari Fase 23, bukan seluruh modul)
- [x] Form Ajukan Withdrawal — `/partner/withdrawals/create`
- [x] Pilih rekening (dari data rekening yang sudah diinput saat registrasi/profile) — partner cuma punya 1 rekening (kolom langsung di `partners`), jadi otomatis di-snapshot saat submit, bukan pilihan aktif
- [x] Upload Foto KTP — **wajib di setiap pengajuan penarikan**, kolom `withdrawals.ktp_path` terpisah dari `partners.ktp_path` registrasi
- [x] Catatan Penarikan (opsional, alasan/keterangan dari partner)
- [x] State machine status: `Pending` → `Approved` → `Paid` / `Rejected`

**Audit trail** (di-flag sejak Fase 0): tabel `withdrawal_status_histories` terpisah, sama pola dengan `commission_status_histories` di Fase 9.

**Kapan komisi ditandai `paid`**: bukan saat withdrawal `approved`, tapi saat benar-benar `paid` (admin upload bukti transfer) — komisi `approved` milik partner dikonsumsi FIFO (dari yang paling lama) sampai menutupi nominal withdrawal. Komisi tidak dibagi sebagian (kalau baris terakhir kelebihan, tetap ditandai paid utuh) — penyederhanaan yang didokumentasikan, bukan sistem alokasi presisi.

---

## Fase 11 — Withdrawal History ✅ (selesai 2026-07-29, dikerjakan bareng Fase 10)

- [x] Listing riwayat withdrawal: Nominal, Rekening, Foto KTP (link lihat, bukan publik), Bukti Transfer (dari admin), Tanggal, Status — sudah ada di `/partner/withdrawals` (List) + halaman detail, tidak perlu halaman terpisah karena datanya sama dengan Fase 10

Diverifikasi lewat `tests/Feature/WithdrawalTest.php` (7 test): submit melebihi saldo ditolak, submit di bawah minimum ditolak, snapshot rekening tidak berubah walau partner ganti rekening setelahnya, approve tercatat di audit trail, markPaid konsumsi FIFO komisi dengan benar, partner tidak bisa akses dokumen withdrawal partner lain, render halaman partner & admin.

---

## Fase 12 — Marketing Center ✅ (selesai 2026-07-29)

- [x] Migrasi tabel `marketing_materials` (kategori: brosur, company profile, price list, proposal, logo, banner, video, template WA, template email, FAQ, selling point)
- [x] Halaman listing materi per kategori, bisa didownload partner — `/partner/marketing-materials`, dikelompokkan per kategori (`defaultGroup`), hanya materi `is_active`
- [x] (Sisi admin ada di Fase "Marketing Material" di bawah — upload-nya dari sana)

Kategori file (brosur/company profile/price list/proposal/logo/banner/video) disimpan di disk publik `media` (sama seperti upload admin lainnya — bukan dokumen rahasia, memang untuk dibagikan partner ke calon customer). Kategori teks (template WhatsApp/email, FAQ, selling point) disimpan sebagai rich text (`content`) yang partner bisa langsung copy, bukan file.

Diverifikasi lewat `tests/Feature/MarketingCenterTest.php` (5 test): partner cuma lihat materi aktif, materi file punya link download yang benar, materi teks menampilkan isi lengkap, admin bisa create, render halaman partner & admin.

---

## Fase 13 — Notification Center ✅ (selesai 2026-07-30)

- [x] Infrastruktur notifikasi in-app (tabel `notifications` atau pakai fitur notification bawaan Laravel) — pakai tabel `notifications` standar Laravel + fitur `->databaseNotifications()` bawaan Filament (bell icon di topbar `/partner`), bukan bikin sistem sendiri
- [x] Trigger notifikasi: Lead Update — `Lead` model event, saat status berubah
- [x] Trigger notifikasi: Project Baru (tersedia untuk diklaim) — `PartnerProject::publish()` (method baru, menggantikan `update()` inline di admin resource), broadcast ke semua partner `approved`
- [x] Trigger notifikasi: Project Assignment (klaim disetujui) — `PartnerProject::approveClaim()`, berdampingan dengan email yang sudah ada (bukan menggantikan)
- [x] Trigger notifikasi: Reminder Follow Up
- [x] Trigger notifikasi: Reminder Meeting — keduanya lewat command baru `reminders:notify-due` (scheduled `everyFiveMinutes()`, pola sama persis `pageviews:resolve-countries`), kolom baru `lead_reminders.notified_at` supaya tidak dikirim dobel
- [x] Trigger notifikasi: Komisi Masuk — `Commission::generateForCustomer()`
- [x] Trigger notifikasi: Withdrawal Approved — `Withdrawal::approve()`
- [x] Trigger notifikasi: Pengumuman (broadcast dari admin ke semua/sebagian partner) — halaman baru `/admin/send-announcement` (pilih Semua Partner approved atau partner tertentu)

Semua notifikasi pakai `Filament\Notifications\Notification::make()->sendToDatabase($partner)` (API resmi Filament untuk database notifications), bukan bikin 7 class `Illuminate\Notifications\Notification` custom terpisah.

Diverifikasi lewat `tests/Feature/NotificationCenterTest.php` (10 test): tiap 5 trigger event-based mengirim notifikasi ke partner yang benar, `publish()` broadcast ke SEMUA partner approved (bukan cuma satu), `approveClaim()` tetap kirim email DAN notifikasi (tidak saling menggantikan), command reminder tidak mengirim dobel untuk reminder yang sama, reminder yang belum jatuh tempo tidak ikut ke-notify, broadcast admin ke "semua" vs "partner tertentu" keduanya benar, render halaman admin.

---

## Fase 14 — Profile Partner ✅ (selesai 2026-07-30)

- [x] Edit Biodata — `/partner/profile`, otomatis muncul di dropdown user-menu lewat fitur bawaan Filament `Panel::profile()`
- [x] Ganti Foto
- [x] Ganti Password
- [x] Edit Data Rekening
- [x] Update KTP
- [x] Update NPWP
- [x] Preferensi Notifikasi (partner pilih notifikasi mana yang mau diterima, mis. lewat email vs in-app saja) — **diinterpretasikan**: notifikasi in-app (Fase 13) selalu aktif (itu Notification Center itu sendiri, tidak masuk akal dimatikan), field `email_notifications_enabled` cuma menggerbang EMAIL TAMBAHAN yang sudah dikirim berdampingan dengan notifikasi in-app (`PartnerProject::approveClaim()`/`rejectClaim()`). Email akun inti (registrasi diterima/disetujui/ditolak, Fase 1) tidak ikut digerbang — itu komunikasi yang harus selalu sampai apapun preferensinya.

Ganti Foto/Update KTP/Update NPWP otomatis membersihkan file lama dari disk (reuse trait `DeletesOldFiles` yang sudah ada sejak Fase 1, tidak ada kode baru untuk itu).

**Bonus perbaikan kecil**: `PartnerProject::rejectClaim()` sebelumnya cuma kirim email, tidak ada notifikasi in-app (beda dari `approveClaim()` yang sudah dapat keduanya sejak Fase 13). Ditambahkan sekalian di sini supaya partner yang mematikan email tidak sama sekali tidak tahu klaimnya ditolak.

Diverifikasi lewat `tests/Feature/PartnerProfileTest.php` (5 test): update biodata & rekening tersimpan, ganti KTP menghapus file lama, ganti password bisa dipakai login, mematikan preferensi email menghentikan email tapi notifikasi in-app tetap jalan, render halaman.

---

## Fase 15 (Admin) — Partner Management ⚠️ (preview minimal, 2026-07-29)

- [x] Approval Registrasi partner baru (lihat dokumen KTP/NPWP yang diupload, approve/reject) — `/admin/partners`, dikerjakan bareng Fase 1
- [ ] Suspend Partner
- [ ] Aktifkan kembali Partner
- [ ] Reset Password partner (dari sisi admin)
- [ ] Kelola Level Partner (menunggu klarifikasi dari Fase 0) — kolom `level` sudah ada, cuma text bebas belum ada UI "kelola" sungguhan

---

## Fase 16 (Admin) — Project Board Management ✅ (selesai 2026-07-29, dikerjakan bareng Fase 7)

- [x] CRUD Project (buat/edit/hapus project yang akan dibuka ke partner) — `/admin/partner-projects`, project baru mulai berstatus `draft` (belum tampil ke partner)
- [x] Publish Project (ubah status jadi `Available`, baru muncul di Project Board partner)
- [x] Assign Partner langsung (tanpa lewat mekanisme klaim, untuk kasus tertentu)
- [x] Approve Claim (menyetujui klaim yang diajukan partner di Fase 7)
- [x] Reject Claim (bonus, tidak eksplisit di daftar tapi perlu — tanpa ini klaim yang ditolak macet permanen di `pending_approval`)
- [x] Close Project

Dikerjakan bareng Fase 7 (bukan sekadar preview minimal seperti Fase 15/17) karena tanpa sisi admin ini, Fase 7 sama sekali tidak bisa diuji — partner tidak akan pernah melihat project apapun kalau tidak ada yang di-publish.

---

## Fase 17 (Admin) — Lead Monitoring ⚠️ (preview minimal, 2026-07-29)

- [x] Halaman monitoring seluruh lead semua partner (read access lintas partner, admin only) — `/admin/leads`
- [x] Transfer Ownership lead (pindah kepemilikan dari satu partner ke partner lain)
- [x] Validasi Lead (admin verifikasi lead valid/tidak)
- [ ] Anti Duplicate — **versi sederhana sudah ada** (banner daftar lead lain dengan phone/email sama, di modal View), **belum** fuzzy-matching penuh (typo, format nomor beda, dst). Perbaiki di sini kalau versi sekarang kurang sensitif.

---

## Fase 18 (Admin) — Commission Scheme Management ✅ (selesai 2026-07-29, dikerjakan bareng Fase 9)

- [x] Form buat skema komisi baru, pilih salah satu dari 3 jenis (Percentage / Recurring Percentage / Flat Commission) — `/admin/commission-schemes`
- [x] Pengaturan cakupan skema: Per Produk
- [x] Pengaturan cakupan skema: Per Partner
- [x] Pengaturan cakupan skema: Per Project
- [x] Masa Berlaku skema (tanggal mulai/berakhir)
- [x] Input Persentase (untuk skema Percentage/Recurring Percentage)
- [x] Input Nominal Flat (untuk skema Flat Commission)

Catatan: tiap skema dianggap pakai maksimal SATU dimensi cakupan (bukan gabungan) — form kasih helper text "isi salah satu saja". Urutan prioritas kalau lebih dari satu skema bisa cocok untuk satu Customer: **Project spesifik → Partner spesifik → Produk spesifik → skema global** (semua cakupan kosong). Ini asumsi, tidak dijelaskan eksplisit di spec asli.

---

## Fase 19 (Admin) — Commission Management (sisi Admin) ✅ (selesai 2026-07-29, dikerjakan bareng Fase 9)

- [x] Generate Komisi (proses hitung komisi dari project/invoice yang closing, sesuai skema aktif) — `/admin/commissions`, action "Generate Komisi" pilih Customer yang belum punya komisi
- [x] Approval Komisi
- [x] Reject Komisi (dengan alasan, tercatat di histori)
- [x] Bonus Komisi (komisi tambahan di luar skema normal, manual oleh admin) — pilih partner + customer (opsional) + nominal + catatan, tanpa lewat scheme matching
- [x] Riwayat Komisi (log lengkap perubahan status per komisi) — tabel `commission_status_histories`, terisi otomatis lewat model event

---

## Fase 20 (Admin) — Withdrawal Management ✅ (selesai 2026-07-29, dikerjakan bareng Fase 10)

- [x] Pengaturan Minimum Withdrawal (dipakai Fase 10 di sisi partner) — `/admin/manage-partner-settings`
- [x] Approval Withdrawal
- [x] Verifikasi Foto KTP yang diupload partner saat pengajuan (tampilkan preview, bukan sekadar centang) — link "Lihat" langsung buka file lewat `WithdrawalDocumentController`
- [x] Upload Bukti Transfer (setelah withdrawal benar-benar ditransfer ke partner) — action "Mark Paid"
- [x] Reject Withdrawal (dengan alasan)
- [x] Riwayat Withdrawal (semua partner, admin view) — `/admin/withdrawals`

---

## Fase 21 (Admin) — Marketing Material ✅ (selesai 2026-07-29, dikerjakan bareng Fase 12)

- [x] Upload Brosur
- [x] Upload Proposal
- [x] Upload Video
- [x] Upload Banner
- [x] Upload Template (WhatsApp & Email)

> Satu form/resource admin untuk kelola seluruh isi Marketing Center (Fase 12), dikelompokkan per kategori materi.

Sesuai catatan di atas, `/admin/marketing-materials` menangani **semua 11 kategori** dari Fase 12 (bukan cuma 5 yang disebut eksplisit di daftar checklist ini) — company profile, price list, logo, FAQ, dan selling point ikut ditangani lewat resource yang sama, form-nya menyesuaikan (Upload untuk kategori file, Rich Text untuk kategori teks).

---

## Fase 22 (Admin) — Reports ✅ (selesai 2026-07-30)

- [x] Laporan Partner (jumlah, status, performa ringkas) — `/admin/reports`
- [x] Laporan Lead (jumlah per status, per partner, per periode)
- [x] Laporan Project (jumlah, status, nilai)
- [x] Laporan Closing (tren closing per periode)
- [x] Laporan Komisi (total, per status, per partner)
- [x] Laporan Withdrawal (total, per status, per partner)
- [x] Laporan Performa Partner (ranking/perbandingan antar partner)
- [x] Laporan Nilai Penjualan (total omzet dari seluruh partner)
- [x] Fitur export laporan (PDF/Excel — konfirmasi format yang dibutuhkan) — **diputuskan pakai CSV** sebagai default (universal, tidak nambah dependency baru seperti `dompdf`/`maatwebsite/excel` yang belum terpasang). Kalau nanti benar-benar butuh PDF/Excel asli, tinggal ganti format export di atas fondasi query yang sama (`app/Services/ReportService.php`).

Satu halaman gabungan (bukan 8 halaman terpisah) dengan filter tanggal global (dari/sampai), tiap laporan punya tombol Export CSV sendiri (bukan satu file gabungan — struktur kolom tiap laporan beda-beda). Logic query dipusatkan di `ReportService` supaya angka yang tampil di halaman dan yang di-export selalu sama persis (dipakai berdua, bukan dua implementasi terpisah yang bisa drift).

Diverifikasi lewat `tests/Feature/ReportsTest.php` (7 test): agregasi Laporan Partner benar, filter tanggal benar-benar mengecualikan data di luar rentang, breakdown Komisi/Withdrawal per status & per partner benar, ranking Performa Partner terurut descending, export CSV menghasilkan file dengan header kolom benar, nama laporan tidak dikenal ditolak (404), render halaman.

---

## Fase 23 (Admin) — Partner Settings

- [ ] Pengaturan Minimum Withdrawal (global, dipakai Fase 20)
- [ ] Pengaturan Commission Scheme Default (skema fallback kalau produk/partner/project tidak punya skema khusus)
- [ ] Pengaturan Project Claim Rule (mis. berapa lama klaim harus diproses, berapa project maksimal diklaim bersamaan)
- [ ] Pengaturan Partner Agreement (teks perjanjian kemitraan yang ditampilkan di Fase 1 registrasi — editable tanpa ubah kode)
- [ ] Pengaturan Workflow Approval (siapa approve apa, sesuai keputusan di Fase 0)
- [ ] Pengaturan Notifikasi (kanal default, template pesan)

---

## Ringkasan Modul (dari spec asli)

| No | Modul | Partner | Admin |
|----|-------|:---:|:---:|
| 1 | Registrasi & Verifikasi | ✅ | ✅ |
| 2 | Dashboard | ✅ | ✅ |
| 3 | Lead & Opportunity | ✅ | ✅ |
| 4 | Customer Management | ✅ | ✅ |
| 5 | Sales Workspace | ✅ | ❌ |
| 6 | Sales Pipeline | ✅ | ✅ |
| 7 | Project Board | ✅ | ✅ |
| 8 | Project Management | ✅ | ✅ |
| 9 | Commission Management | ✅ | ✅ |
| 10 | Withdrawal | ✅ | ✅ |
| 11 | Withdrawal History | ✅ | ✅ |
| 12 | Marketing Center | ✅ | ✅ |
| 13 | Notification Center | ✅ | ✅ |
| 14 | Profile | ✅ | ❌ |
| 15 | Partner Management | ❌ | ✅ |
| 16 | Lead Monitoring | ❌ | ✅ |
| 17 | Commission Scheme Management | ❌ | ✅ |
| 18 | Withdrawal Management | ❌ | ✅ |
| 19 | Reports | ❌ | ✅ |
| 20 | Partner Settings | ❌ | ✅ |

---

## Catatan Penting

> Spec asli yang diberikan berhenti tepat di judul "Catatan Penting" tanpa isi — poin-poin di bawah ini murni catatan teknis tambahan dari hasil review breakdown, **bukan** dari spec asli. Perlu dikonfirmasi ke pemberi spec, bukan dianggap final.

- **Keamanan dokumen KYC (KTP/NPWP/bukti transfer)** adalah data pribadi sensitif — wajib disimpan di disk privat dengan akses terautentikasi per-pemilik, bukan folder publik seperti disk `media` yang dipakai situs profile saat ini.
- **Ketelitian angka komisi**: skema Recurring Percentage butuh mekanisme yang jelas untuk tahu "pembayaran keberapa" sudah dihitung, supaya komisi tidak double-hitung atau ke-skip kalau job/cron sempat gagal jalan.
- **Klarifikasi masih dibutuhkan** (jangan mulai kerja di area ini sebelum dijawab): definisi "Level Partner" dan pengaruhnya, siapa berwenang approve di tiap tahap (registrasi/klaim/komisi/withdrawal), dan format export laporan yang diharapkan.
