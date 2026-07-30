# TODO — Portal Partner / Sales Partner Management

Breakdown kerja untuk pengembangan **Portal Partner/Affiliator** + modul tambahan di **Admin existing**. Sumber: spec "Final Rekapan Fitur – Portal Partner/Sales Partner Management" (lihat ringkasan modul di bagian paling bawah file ini).

Status semua item: belum dikerjakan (`[ ]`). Centang (`[x]`) begitu selesai. Urutan fase disusun berdasarkan dependency teknis (fondasi dulu, baru fitur yang bergantung padanya) — bukan urutan penomoran di spec asli.

## Progress

| Fase | Status | Tanggal |
|---|---|---|
| Fase 0 — Keputusan Arsitektur | ✅ Selesai (bagian teknis) | 2026-07-29 |
| Fase 1 — Registrasi & Autentikasi Partner | ✅ Selesai | 2026-07-29 |
| Fase 2 — Dashboard Partner | ✅ Selesai | 2026-07-30 |
| Fase 3 — Lead & Opportunity Management | ✅ Selesai | 2026-07-29 |
| Fase 4 — Customer Management | ✅ Selesai | 2026-07-29 |
| Fase 6 — Sales Pipeline (Kanban) | ✅ Selesai | 2026-07-29 |
| Fase 7 — Project Board (Available Project) | ✅ Selesai | 2026-07-29 |
| Fase 8 — Project Management | ✅ Selesai | 2026-07-30 |
| Fase 9 — Commission Management (sisi Partner) | ✅ Selesai | 2026-07-29 |
| Fase 10 — Withdrawal (Partner) | ✅ Selesai | 2026-07-29 |
| Fase 11 — Withdrawal History | ✅ Selesai (dikerjakan bareng Fase 10) | 2026-07-29 |
| Fase 16 (Admin) — Project Board Management | ✅ Selesai (dikerjakan bareng Fase 7, bukan cuma preview) | 2026-07-29 |
| Fase 17 (Admin) — Lead Monitoring | ✅ Selesai | 2026-07-30 |
| Fase 18 (Admin) — Commission Scheme Management | ✅ Selesai (dikerjakan bareng Fase 9) | 2026-07-29 |
| Fase 19 (Admin) — Commission Management (sisi Admin) | ✅ Selesai (dikerjakan bareng Fase 9) | 2026-07-29 |
| Fase 20 (Admin) — Withdrawal Management | ✅ Selesai (dikerjakan bareng Fase 10) | 2026-07-29 |
| Fase 12 — Marketing Center | ✅ Selesai | 2026-07-29 |
| Fase 21 (Admin) — Marketing Material | ✅ Selesai (dikerjakan bareng Fase 12) | 2026-07-29 |
| Fase 13 — Notification Center | ✅ Selesai | 2026-07-30 |
| Fase 14 — Profile Partner | ✅ Selesai | 2026-07-30 |
| Fase 15 (Admin) — Partner Management | ✅ Selesai | 2026-07-30 |
| Fase 22 (Admin) — Reports | ✅ Selesai | 2026-07-30 |
| Fase 23 (Admin) — Partner Settings | ✅ Selesai | 2026-07-30 |
| Fase 5 — Sales Workspace | ✅ Selesai (dikerjakan bareng Fase 8) | 2026-07-30 |
| Fase 24 (Admin) — RBAC (Role & Permission) | ✅ Selesai | 2026-07-30 |
| Fase 25 (Admin) — Workflow Assignment | ✅ Selesai | 2026-07-30 |
| Fase 26 (Admin+Partner) — Support Ticket | ✅ Selesai | 2026-07-30 |
| Fase 27 (Admin) — Audit Log | 🔧 Sedang dikerjakan | 2026-07-30 |

**Final Review klien (2026-07-30 sore)** menetapkan beberapa keputusan bisnis final yang merevisi/menyelesaikan item-item yang sebelumnya masih asumsi/placeholder — lihat bagian **"Final Business Decisions (Client Sign-off)"** di bawah untuk detail lengkap. Ringkasnya: Workflow Approval (Fase 23) direvisi total jadi RBAC sungguhan (Fase 24+25, sedang dikerjakan), Partner Level (yang sempat ditambahkan sebagai bagian Commission Scheme di hari yang sama) di-revert jadi murni atribut informational, Produk/Export/dll dikonfirmasi final tanpa perubahan kode, dan 2 modul baru ditambahkan ke scope (Support Ticket, Audit Log).

### Sisa pekerjaan (ringkasan cepat)

**Sedang dikerjakan** (per Final Review 2026-07-30): Fase 24 (RBAC), Fase 25 (Workflow Assignment), Fase 26 (Support Ticket, modul baru), Fase 27 (Audit Log, modul baru). Plus revert kecil di Fase 15/Commission Scheme untuk Partner Level.

**Sudah "selesai" tapi sengaja belum 100% lengkap:**
- Fase 9 (Commission — Recurring Percentage): **dikonfirmasi final sebagai future enhancement**, bukan MVP (butuh Invoice Management + Payment Tracking + Scheduler/Recurring Engine terpisah)
- Fase 23 (Notifikasi): kanal default sudah ada, "template pesan" (isi teks tiap notifikasi diedit admin) belum dibangun

**Pertanyaan bisnis yang masih terbuka:** tidak ada lagi — semua yang tersisa di versi sebelumnya (role approval, Produk, format export) sudah dijawab final lewat Final Review 2026-07-30.

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

### Sudah diselesaikan sambil jalan (dulu tercatat "genuinely open", sekarang sudah dibangun)

- [x] **Audit trail untuk data uang** — tabel `commission_status_histories` (Fase 9) dan `withdrawal_status_histories` (Fase 10), keduanya terisi otomatis lewat model event tiap kali status berubah, bukan sekadar kolom `status` yang di-update in-place.
- [x] **Precision angka uang** — semua kolom nominal (`commissions.amount`, `withdrawals.amount`, `customers.project_value`, `partner_projects.budget`, dst) pakai `decimal`, tidak ada yang `float`/`double`.
- [x] ~~**Definisi "Level Partner"** (2026-07-30) — 4 tier tetap Bronze/Silver/Gold/Platinum, pengaruhnya ke rate komisi (Commission Scheme)~~ **DIREVISI ulang 2026-07-30 sore** (final review klien) — lihat "Final Business Decisions" di bawah: Level Partner **BUKAN** bagian dari Commission Scheme, murni atribut bisnis informational (badge/loyalty/reward/prioritas project/klasifikasi/dashboard). Tier tetap (`Partner::LEVELS`) dan UI "Ubah Level" (Fase 15) tetap ada, tapi cakupan "Per Level" di Commission Scheme (sempat ditambahkan sore harinya) di-revert.
- [x] **Definisi role approval** (2026-07-30) — RESOLVED lewat "Final Business Decisions": RBAC (Role/Permission per modul + assign User ke Role + assign approver per workflow). Lihat Fase 24 & 25 di bawah.
- [x] **Definisi "Produk"** (2026-07-30) — RESOLVED: dikonfirmasi final tetap pakai tabel `services`, tidak ada tabel `products` terpisah. Lihat "Final Business Decisions".
- [x] **Format export laporan** (2026-07-30) — RESOLVED: dikonfirmasi final CSV untuk fase awal. Lihat "Final Business Decisions".

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

## Fase 2 — Dashboard Partner ✅ (selesai 2026-07-30)

Penundaan sebelumnya sudah tidak berlaku — semua tabel sumbernya (Lead/Customer/Project/Commission/Withdrawal) sudah ada sejak Fase 3/4/7/9/10. Dikerjakan sekarang, menggantikan placeholder `Filament\Pages\Dashboard` bawaan yang dipasang sementara di Fase 1.

- [x] Query/summary: Total Lead, Total Opportunity, Total Customer, Total Project — widget `PartnerActivityStats`. "Opportunity" diinterpretasikan sebagai lead berstatus `opportunity`/`proposal`/`negotiation` (sudah lewat tahap awal, belum closing) — tidak didefinisikan eksplisit di spec.
- [x] Query/summary: Project Available (jumlah project yang bisa diklaim) — global (semua partner), bukan cuma milik partner yang login, karena itu yang bisa diklaim siapa saja.
- [x] Query/summary: Follow Up Hari Ini, Meeting Hari Ini (dari data reminder di Fase 3)
- [x] Query/summary: Total Nilai Project, Total Komisi, Komisi Pending, Komisi Ready Withdrawal, Total Withdrawal — widget `PartnerFinanceStats`
- [x] Target Penjualan (input target oleh admin per partner/periode, ditampilkan progress-nya) — tabel baru `partner_sales_targets` (satu target per partner per bulan), admin input lewat `/admin/partner-sales-targets`, partner lihat progress-nya di dashboard
- [x] Grafik Pipeline (jumlah lead per tahapan pipeline) — `PartnerPipelineChart` (doughnut)
- [x] Grafik Closing (tren closing per periode) — `PartnerClosingChart` (line, 12 bulan terakhir)
- [x] Grafik Komisi (tren komisi per periode) — `PartnerCommissionChart` (line, 12 bulan terakhir)

Diverifikasi lewat `tests/Feature/PartnerDashboardTest.php` (5 test): angka stats benar dan ter-scope ke partner yang login (data partner lain tidak ikut terhitung), reminder hari ini terpisah follow-up/meeting dengan benar, progress target penjualan menghitung persentase benar dan menampilkan pesan yang tepat kalau belum diset, render halaman dashboard partner & halaman admin sales target.

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

## Fase 5 — Sales Workspace ✅ (selesai 2026-07-30, dikerjakan bareng Fase 8)

Diimplementasikan sebagai **perluasan `infolist()` di `CustomerResource` (Fase 4) yang sudah ada** — bukan halaman/resource baru — karena "satu halaman gabungan per customer" secara harfiah sudah ada (`ViewCustomer`). Semua panel bawah ini adalah Section baru yang ditambahkan di situ:

- [x] Panel Informasi Customer — section existing (Fase 4), tidak diubah
- [x] Panel Nilai Project & Status Pembayaran — section existing (Fase 4), tidak diubah
- [x] Panel Status Project — baru, dari `partnerProject.status`/`progress`, hanya tampil kalau ada `partnerProject` terkait
- [x] Panel Timeline — section "Riwayat Aktivitas" existing (Fase 4), gabungan penuh, tidak diubah
- [x] Panel Aktivitas — baru, `Customer::getSystemActivitiesAttribute()`, subset timeline yang sama (filter tipe `created`/`status_change`/`document`)
- [x] Panel Catatan — baru, `Customer::getNotesAttribute()`, subset timeline yang sama (filter tipe `note`)
- [x] Panel Follow Up — baru, `Customer::getFollowUpsAttribute()`, dari `lead.reminders` (kosong kalau customer tidak berasal dari Lead)
- [x] Panel Meeting — baru, `Customer::getMeetingsAttribute()`, sama seperti Follow Up beda filter tipe
- [x] Panel Proposal — baru, `Customer::getProposalDocumentsAttribute()`. **Interpretasi**: tidak ada entitas "Proposal" terpisah di sistem ini, jadi diinterpretasikan sebagai dokumen yang sudah diupload ke Lead asal (`lead.documents`, Fase 3) — dicatat jelas di kode sebagai interpretasi, bukan didefinisikan eksplisit di spec
- [x] Panel Status Komisi — baru, dari `Customer::commission` (relasi sudah ada sejak Fase 9)

Timeline/Aktivitas/Catatan secara teknis dari satu sumber data yang sama (`Customer::activityTimeline()`), cuma beda filter — bukan 3 tabel terpisah.

Diverifikasi lewat `tests/Feature/ProjectManagementTest.php` (lihat detail test di Fase 8 di bawah, satu file test dipakai untuk kedua fase karena keduanya diimplementasikan di file yang sama).

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

## Fase 8 — Project Management ✅ (selesai 2026-07-30)

Diimplementasikan sebagai **penambahan kolom & action ke `CustomerResource` (partner) yang sudah ada** — `Customer` sudah jadi satu-satunya sumber "deal tertutup" sejak Fase 9 (baik dari Lead-Won maupun dari PartnerProject-Assigned), jadi tidak perlu resource/listing baru.

- [x] Listing project yang sudah terjual — kolom baru di tabel `CustomerResource`: Nama Project, Status Project (badge), Progress (`%`), semua dari relasi `partnerProject` yang sudah ada sejak Fase 9. Placeholder `-` untuk Customer yang berasal dari Lead-Won murni (tidak ada `partnerProject` terkait)
- [x] Tampilkan: Nama Project, Customer, Produk, Nilai Project, Status Pembayaran (sudah ada dari Fase 4), Status Project, Progress (baru, lihat di atas)
- [x] Update progress project — **dibuka untuk keduanya sebagai default sementara**, keputusan resmi siapa yang berwenang masih menunggu Fase 0:
  - Partner: action baru `updateProgress` di tabel `CustomerResource` (partner), cuma muncul kalau ada `partnerProject` terkait, form 1 field numeric 0–100
  - Admin: field `progress` baru ditambahkan ke form `PartnerProjectResource` (Fase 16, sudah full-CRUD)

Diverifikasi lewat `tests/Feature/ProjectManagementTest.php` (7 test): Customer dari `partner_project_id` menampilkan Nama Project/Status Project/Progress dengan benar; Customer dari Lead-Won murni tidak punya `partnerProject`; update progress via `PartnerProject::update()` tercermin di Customer; admin bisa update `progress` lewat form edit `PartnerProjectResource`; panel Follow Up/Meeting/Proposal/Aktivitas/Catatan/Status Komisi (Fase 5) menampilkan data yang benar dan terpisah sesuai filter masing-masing; Customer tanpa Lead punya array kosong untuk Follow Up/Meeting/Proposal; render halaman index & view `CustomerResource` untuk kedua kasus (dengan & tanpa `partnerProject`).

Full regression: 89/90 test lulus (1 kegagalan adalah `ExampleTest` yang sudah gagal sebelum sesi ini, tidak terkait — tidak pakai `RefreshDatabase`, bukan regresi dari perubahan ini).

---

## Fase 9 — Commission Management (sisi Partner) ✅ (selesai 2026-07-29)

- [x] Migrasi tabel `commission_schemes` (lihat 3 jenis skema di bawah)
- [x] Migrasi tabel `commissions` (per project/invoice, relasi ke scheme yang dipakai + histori status)
- [x] Listing komisi: Project, Produk, Skema Komisi, Nilai Project, Nilai Invoice, Persentase, Nominal Komisi, Status — `/partner/commissions`
- [x] State machine status komisi: `Pending` → `Waiting Client Payment` → `Approved` → `Paid` / `Rejected`

### Implementasi 3 jenis skema komisi

- [x] **Percentage** — komisi = persentase × nilai project (sekali hitung, saat closing). Contoh: 10% × Rp100.000.000.
- [x] **Recurring Percentage** — ⚠️ **tipe skema-nya sudah bisa dipilih & disimpan**, tapi mesin hitung ulang otomatis tiap ada pembayaran baru **belum dibangun** — sistem ini belum punya tabel invoice/payment sama sekali. Generate untuk skema ini sekarang cuma menghasilkan satu baris komisi awal (diperlakukan seperti Percentage biasa). **Dikonfirmasi final oleh klien (2026-07-30, lihat "Final Business Decisions" di bawah): ini memang bukan bagian dari MVP**, melainkan future enhancement yang butuh 3 modul tambahan (Invoice Management, Payment Tracking, Scheduler/Recurring Engine) — bukan lagi item "belum sempat dikerjakan", tapi item yang sengaja di luar scope saat ini.
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

## Fase 15 (Admin) — Partner Management ✅ (selesai 2026-07-30)

- [x] Approval Registrasi partner baru (lihat dokumen KTP/NPWP yang diupload, approve/reject) — `/admin/partners`, dikerjakan bareng Fase 1
- [x] Suspend Partner — action baru di tabel `PartnerResource` (cuma muncul untuk partner `approved`), alasan opsional disimpan ke kolom `rejection_reason` yang sudah ada (di-reuse sebagai "alasan status non-aktif" generik, bukan kolom baru — partner tidak pernah rejected dan suspended sekaligus jadi tidak ambigu). Partner yang disuspend otomatis terblokir dari portal lewat `EnsurePartnerApproved` yang sudah ada (sama seperti pending/rejected), diarahkan ke halaman status dengan pesan "Akun Disuspend" + alasan
- [x] Aktifkan kembali Partner — action `reactivate` (cuma muncul untuk partner `suspended`), status kembali `approved`, `rejection_reason` dikosongkan lagi
- [x] Reset Password partner (dari sisi admin) — admin memicu `Password::broker('partners')->sendResetLink()`, reuse persis mekanisme "Lupa Password" partner yang sudah ada dari Fase 1 (bukan admin melihat/set password baru secara langsung, lebih aman)
- [x] Kelola Level Partner (2026-07-30) — level pindah dari free-text jadi 4 tier tetap (`Partner::LEVELS`: Bronze/Silver/Gold/Platinum, lihat keputusan penuh di Fase 0), dikelola lewat action baru `updateLevel` (dropdown, bukan field pasif di form View yang sebelumnya tidak benar-benar bisa diedit). Level ini juga jadi cakupan baru di Commission Scheme (lihat Fase 18) — partner level lebih tinggi bisa dapat rate default lebih baik.

Diverifikasi lewat `tests/Feature/PartnerManagementTest.php` (6 test): suspend partner `approved` menyimpan alasan, partner `suspended` diblokir dari portal dan melihat pesan+alasan di halaman status, reaktivasi mengembalikan status & mengosongkan alasan, action suspend/reaktivasi cuma tampil sesuai status yang relevan, reset password mengirim notifikasi `ResetPassword` + menyimpan token ke `partner_password_reset_tokens`, action reset password tidak tampil untuk partner yang masih `pending_review`. Level Partner + pengaruhnya ke Commission Scheme diverifikasi terpisah lewat `tests/Feature/PartnerLevelTest.php` (5 test): admin bisa ubah level, resolusi skema komisi memprioritaskan Level di antara Partner dan Produk, skema per-level cuma cocok untuk partner dengan level persis sama, komisi yang di-generate memakai rate dari skema level, admin bisa buat skema baru dengan cakupan Level lewat form.

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

## Fase 17 (Admin) — Lead Monitoring ✅ (selesai 2026-07-30)

- [x] Halaman monitoring seluruh lead semua partner (read access lintas partner, admin only) — `/admin/leads`
- [x] Transfer Ownership lead (pindah kepemilikan dari satu partner ke partner lain)
- [x] Validasi Lead (admin verifikasi lead valid/tidak)
- [x] Anti Duplicate — upgrade dari exact-match jadi fuzzy-matching (`Lead::findPotentialDuplicates()`): nomor telepon dinormalisasi dulu (buang semua karakter selain digit, prefix `+62`/`62` disamakan jadi `0`) supaya `+62 812-3456-7890` dan `081234567890` dikenali sebagai nomor yang sama; email dibandingkan case-insensitive; nama dibandingkan pakai `similar_text()` (ambang 85%) untuk menangkap typo kecil di nama lead/perusahaan. Tetap perbandingan in-memory (bukan query fuzzy di level DB) karena skala data di sini kecil (small business, bukan call center).

Diverifikasi lewat `tests/Feature/LeadDuplicateDetectionTest.php` (5 test): nomor telepon format beda terdeteksi sama, email beda kapital terdeteksi sama, nama dengan typo kecil terdeteksi mirip (nama yang benar-benar beda tidak ikut ke-flag), lead tanpa kemiripan sama sekali tidak menghasilkan duplikat, halaman View Lead admin menampilkan daftar duplikat yang benar.

---

## Fase 18 (Admin) — Commission Scheme Management ✅ (selesai 2026-07-29, dikerjakan bareng Fase 9)

- [x] Form buat skema komisi baru, pilih salah satu dari 3 jenis (Percentage / Recurring Percentage / Flat Commission) — `/admin/commission-schemes`
- [x] Pengaturan cakupan skema: Per Produk
- [x] Pengaturan cakupan skema: Per Partner
- [x] Pengaturan cakupan skema: Per Level Partner (baru, 2026-07-30 — lihat Fase 15)
- [x] Pengaturan cakupan skema: Per Project
- [x] Masa Berlaku skema (tanggal mulai/berakhir)
- [x] Input Persentase (untuk skema Percentage/Recurring Percentage)
- [x] Input Nominal Flat (untuk skema Flat Commission)

Catatan: tiap skema dianggap pakai maksimal SATU dimensi cakupan (bukan gabungan) — form kasih helper text "isi salah satu saja". Urutan prioritas kalau lebih dari satu skema bisa cocok untuk satu Customer: **Project spesifik → Partner spesifik → Level Partner → Produk spesifik → Default (Fase 23) → skema global** (semua cakupan kosong). Ini asumsi, tidak dijelaskan eksplisit di spec asli. Kolom `level` ditambahkan 2026-07-30 lewat migration terpisah (`add_level_to_commission_schemes_table`) begitu "Level Partner" (Fase 15) akhirnya didefinisikan.

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

## Fase 23 (Admin) — Partner Settings ✅ (selesai 2026-07-30)

- [x] Pengaturan Minimum Withdrawal (global, dipakai Fase 20) — dibangun di Fase 10
- [x] Pengaturan Commission Scheme Default (skema fallback kalau produk/partner/project tidak punya skema khusus) — `default_commission_scheme_id`, prioritas: Project → Partner → Produk → **Default di sini** → skema tanpa cakupan (fallback lama dari Fase 9, tetap jalan berdampingan, tidak diganti)
- [x] Pengaturan Project Claim Rule (mis. berapa lama klaim harus diproses, berapa project maksimal diklaim bersamaan) — **keduanya benar-benar ditegakkan**, bukan cuma field tersimpan: `max_concurrent_claimed_projects` dicek di `PartnerProject::claim()` (`ValidationException` kalau sudah di batas), `claim_processing_hours` ditegakkan lewat command terjadwal baru `projects:expire-stale-claims` (`->hourly()`, otomatis reject klaim yang lewat batas waktu)
- [x] Pengaturan Partner Agreement (teks perjanjian kemitraan yang ditampilkan di Fase 1 registrasi — editable tanpa ubah kode) — dibangun di Fase 1
- [x] ~~Pengaturan Workflow Approval (siapa approve apa, sesuai keputusan di Fase 0) — placeholder teks bebas~~ **DIREVISI 2026-07-30**: keputusan final klien adalah RBAC sungguhan, bukan catatan teks. Field `approval_workflow_notes` dihapus, digantikan sepenuhnya oleh **Fase 24 (RBAC) + Fase 25 (Workflow Assignment)** di bawah — lihat "Final Business Decisions".
- [x] Pengaturan Notifikasi (kanal default, template pesan) — **"kanal default" dibangun** (`default_email_notifications_enabled`, dipakai `Register::handleRegistration()` untuk partner baru). ⚠️ **"Template pesan" (isi teks tiap jenis email/notifikasi editable admin) SENGAJA TIDAK dibangun** — perlu sistem templating dinamis menggantikan Blade view yang di-hardcode di `resources/views/emails/*.blade.php`, itu perubahan besar terpisah, bukan bagian dari fase ini.

**Ini menandai semua fase di `todo_partnert.md` selesai dikerjakan pada saat ditulis (2026-07-29/30)** — Fase 2 (Dashboard), Fase 5 (Sales Workspace), dan Fase 8 (Project Management) waktu itu masih sengaja ditunda, tapi ketiganya sudah diselesaikan juga per 2026-07-30 (lihat catatan masing-masing fase di atas dan ringkasan "Sisa Pekerjaan" di bagian Progress).

Diverifikasi lewat `tests/Feature/PartnerSettingsExtendedTest.php` (7 test): default skema eksplisit menang lawan fallback lama tapi kalah lawan skema lebih spesifik, klaim ditolak begitu di batas concurrent (dan tidak terpengaruh kalau setting kosong), command expire-stale-claims cuma memproses yang benar-benar lewat batas waktu, registrasi baru memakai kanal notifikasi default dari setting.

---

## Final Business Decisions (Client Sign-off, 2026-07-30)

Dokumen "Final Review" dari klien menetapkan keputusan-keputusan berikut sebagai **final, tidak perlu klarifikasi lagi**:

- **Role menggunakan RBAC** — bukan lagi placeholder teks. Lihat Fase 24 (Role & Permission) dan Fase 25 (Workflow Assignment) di bawah.
- **Workflow Approval menggunakan Assignment** — approver per workflow ditentukan lewat Role, bukan hardcode "siapa saja yang login boleh approve". Lihat Fase 25.
- **Produk menggunakan tabel `services`** — final, tidak akan ada tabel `products` terpisah. Sudah konsisten dipakai di semua modul (Lead, Customer, Project, Commission Scheme, Project Board) sejak awal, tidak ada perubahan kode.
- **Export menggunakan CSV pada fase awal** — final, sudah diimplementasikan di Fase 22.
- **Lead hanya dimiliki satu Partner** — sudah begini sejak Fase 3 (`leads.partner_id`, bukan many-to-many), tidak ada perubahan kode.
- **Project Claim mengikuti alur**: Available → Pending Approval → Assigned → In Progress → Closed — sudah sesuai state machine `PartnerProject` sejak Fase 7 (ditambah `draft` sebelum `Available` dan `Cancelled` sebagai off-ramp, detail implementasi yang tidak bertentangan dengan alur inti ini).
- **Commission dihitung berdasarkan pembayaran (invoice/payment) yang telah diterima** — ini **konfirmasi arah jangka panjang**, bukan MVP sekarang. MVP saat ini masih pakai `project_value` sebagai proksi nilai yang "diterima" (dihitung sekali saat closing). Perhitungan berbasis invoice/payment sungguhan adalah bagian dari Recurring Commission (Fase 9) yang dikonfirmasi sebagai future enhancement, lihat catatan di Fase 9.
- **Partner menggunakan Role bawaan "Partner"**, tidak bisa mengelola Role maupun Permission — sudah benar secara arsitektur sejak awal (guard `partner` terpisah total dari RBAC yang dibangun di Fase 24, yang cuma berlaku untuk guard `web`/staff admin). Tidak dibangun tabel roles/permissions terpisah untuk guard `partner` karena semua partner memang selalu punya akses yang identik hari ini — tidak ada kebutuhan pembedaan hak akses antar partner yang diminta.
- **Partner Level bukan bagian dari Commission Scheme** — direvisi dari keputusan sore harinya (Level sempat ditambahkan sebagai salah satu cakupan resolusi skema komisi), di-revert total. Level murni atribut informational: badge, loyalty program, reward, bonus, prioritas mendapat project, klasifikasi partner, dashboard & reporting. Yang benar-benar diimplementasikan hari ini: tier tetap (`Partner::LEVELS`) + badge di tabel `PartnerResource` (dashboard/reporting) + UI "Ubah Level". Loyalty/reward/bonus/prioritas project **belum ada logic aktifnya** (tidak diminta dibangun sekarang, cuma disebut sebagai contoh kegunaan level ke depannya) — dicatat di sini supaya tidak dianggap sudah selesai.
- **Audit Log** ditambahkan sebagai modul baru (disarankan, bukan wajib) — beda dari Status History (`commission_status_histories`/`withdrawal_status_histories`) yang sudah ada: Audit Log generik lintas model, mencatat siapa/kapan/apa yang berubah. Lihat Fase 27.

---

## Fase 24 (Admin) — RBAC (Role & Permission) ✅ (selesai 2026-07-30)

Menggantikan placeholder "Workflow Approval" di Fase 23 sepenuhnya. Pakai `spatie/laravel-permission`, guard `web` (staff/admin) saja — Partner tetap guard terpisah tanpa RBAC sendiri (lihat "Final Business Decisions").

- [x] Admin bisa membuat Role — `RoleResource` baru (`/admin/roles`)
- [x] Admin bisa mengatur Permission per Modul — 15 modul (Partner, Lead, Project Board, Commission Scheme, Commission, Withdrawal, Marketing Material, Sales Target, Reports, Partner Settings, Support Ticket, Role, User, Workflow Assignment, Audit Log), masing-masing dengan permission minimum: View/Create/Update/Delete/Approve/Reject/Assign/Export — di-seed otomatis lewat migration `seed_rbac_modules_and_super_admin_role` (bukan seeder terpisah, supaya jalan otomatis di `RefreshDatabase` test juga), dipilih lewat `CheckboxList` di form Role
- [x] Admin bisa assign User (staff) ke Role — `UserResource` baru (`/admin/users`, belum pernah ada UI kelola staff user sebelumnya)
- [x] Modul-modul existing (Partner, Lead, Project Board, Commission Scheme, Commission, Withdrawal, Marketing Material, Sales Target, Partner Settings, Reports) digating oleh permission ini di CRUD standarnya — lewat trait kecil `App\Filament\Concerns\AuthorizesModule` (dipasang di 8 Resource) + `canAccess()` manual di 2 Page (Reports, Partner Settings)

**Batasan lingkup**: RBAC ini cuma berlaku untuk resource-resource **Portal Partner/Sales Partner Management** (sesuai lingkup dokumen ini), tidak termasuk 12 resource CMS situs profile yang sudah ada duluan (Client/Faq/Menu/Page/Post/PricingPlan/Project/Promotion/Service/TeamMember/Testimonial/ContactMessage) — itu di luar lingkup modul Partner Program.

**Pencegahan regresi/lockout**: role `Super Admin` (semua permission) di-seed otomatis dan di-assign ke semua `users` yang sudah ada lewat migration data-seed yang sama — aman untuk data produksi yang sudah berjalan. `UserFactory` juga di-update supaya tiap User baru dari factory otomatis dapat role `Super Admin`, supaya ~30 test existing yang pakai `User::factory()->create()` tidak mendadak kehilangan akses begitu gating aktif.

Diverifikasi lewat `tests/Feature/RoleManagementTest.php` (6 test): migration seed menghasilkan 15×8=120 permission + role Super Admin dengan semua permission, User dari factory otomatis Super Admin dan bisa akses semua resource yang di-gate, admin bisa buat Role dengan permission tertentu, admin bisa assign User ke Role, User tanpa permission diblokir (403) dari resource manapun, User dengan permission `lead.view` saja bisa akses Lead tapi diblokir dari Partner.

---

## Fase 25 (Admin) — Workflow Assignment ✅ (selesai 2026-07-30)

Melengkapi Fase 24: menentukan **siapa** (Role mana) yang jadi approver di tiap workflow, bukan cuma **bisa apa** (permission).

- [x] Tabel `workflow_assignments` — 1 row per workflow (`WorkflowAssignmentResource`, `/admin/workflow-assignments`, cuma bisa edit `role_id`, tidak bisa create/delete karena 6 row-nya fixed & di-seed sekali lewat migration), admin pilih Role approver (boleh dikosongkan = siapa saja yang punya permission `approve` di modul itu boleh approve)
- [x] Workflow: Registrasi Partner → `PartnerResource::approve/reject`
- [x] Workflow: Project Claim → `PartnerProjectResource::approveClaim/rejectClaim`
- [x] Workflow: Project Approval → `PartnerProjectResource::publish` (draft → available)
- [x] Workflow: Commission Approval → `CommissionResource::approve/reject`
- [x] Workflow: Withdrawal Approval → `WithdrawalResource::approve/reject`
- [x] Workflow: Support Ticket → `SupportTicketResource::resolve/close` (dikaitkan di sini, diimplementasikan penuh di Fase 26)

Kelima action approve/reject/publish existing ditambah 2 syarat sekaligus: permission modul terkait (`{modul}.approve`/`.reject`, dari Fase 24) DAN `WorkflowAssignment::userIsAuthorizedFor()` (kalau workflow itu di-assign ke Role tertentu, user juga harus punya Role itu — kalau tidak di-assign, permission saja cukup).

Field `approval_workflow_notes` (placeholder lama di Partner Settings, Fase 23) dihapus total (kolom + form field) — sepenuhnya digantikan Fase 24+25 ini.

Diverifikasi lewat `tests/Feature/WorkflowAssignmentTest.php` (5 test): migration seed menghasilkan 6 row workflow tanpa assignment, workflow tanpa assignment bisa diapprove siapa saja yang punya permission modul itu, workflow yang sudah di-assign ke Role tertentu memblokir user yang punya permission tapi bukan Role itu (dan mengizinkan yang punya Role-nya), admin bisa ubah Role approver lewat `WorkflowAssignmentResource`, kolom `approval_workflow_notes` sudah tidak ada lagi di `partner_settings`.

---

## Fase 26 (Admin+Partner) — Support Ticket ✅ (selesai 2026-07-30)

**Modul baru yang tidak pernah ada di spec/todo manapun sebelumnya** — ditambahkan karena "Final Review" klien menyebutnya sebagai salah satu dari 6 workflow yang butuh Assignment. Dibangun sebagai modul minimal (tanpa kategori/prioritas/SLA) supaya Assignment-nya benar-benar teruji end-to-end, bukan cuma kerangka kosong.

- [x] Partner bisa buat tiket (subjek + deskripsi) — `/partner/support-tickets/create`
- [x] Partner bisa lihat status tiket miliknya sendiri (tidak bisa lihat tiket partner lain) — scoped lewat `getEloquentQuery()`, pola sama seperti `LeadResource`/`CustomerResource`. Tidak bisa edit/hapus setelah dibuat.
- [x] Admin bisa lihat semua tiket lintas partner — `/admin/support-tickets`
- [x] Admin bisa assign tiket ke staff user (pakai Workflow Assignment dari Fase 25) — status otomatis jadi `in_progress`
- [x] Admin bisa resolve (dengan catatan penyelesaian) / close tiket — state machine `open → in_progress → resolved → closed`

Kedua action `resolve`/`close` (dan `assign`) digating permission modul `support_ticket` (Fase 24) + `WorkflowAssignment::userIsAuthorizedFor()` (Fase 25), persis pola yang sama dengan 5 workflow existing.

Diverifikasi lewat `tests/Feature/SupportTicketTest.php` (5 test): partner bisa buat tiket, partner cuma lihat tiket sendiri (tidak bisa lihat tiket partner lain), admin lihat tiket lintas partner, admin bisa assign+resolve+close berurutan, action resolve digating benar oleh permission + Workflow Assignment (user dengan permission tapi bukan Role yang di-assign diblokir).

---

## Fase 27 (Admin) — Audit Log

Disarankan klien, beda dari Status History yang sudah ada (Fase 9/10) — Status History cuma mencatat histori status; Audit Log mencatat **siapa** melakukan **perubahan apa** pada **kapan**, lintas model.

- [ ] Tabel `audit_logs` generik (model manapun, aktor manapun — staff `User` atau `Partner`)
- [ ] Terpasang otomatis (lewat model event, bukan instrumentasi manual per-action) di: Partner, Lead, Customer, Project Board, Commission, Withdrawal, Support Ticket, Role & Permission, Workflow Assignment
- [ ] Halaman admin read-only untuk melihat/filter Audit Log (per model, per user, per aksi, per tanggal)

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
| 21 | RBAC (Role & Permission) | ❌ | ✅ |
| 22 | Workflow Assignment | ❌ | ✅ |
| 23 | Support Ticket | ✅ | ✅ |
| 24 | Audit Log | ❌ | ✅ |

> Modul 21-24 ditambahkan 2026-07-30 dari "Final Review" klien — tidak ada di spec asli (lihat "Final Business Decisions" di atas).

---

## Catatan Penting

> Spec asli yang diberikan berhenti tepat di judul "Catatan Penting" tanpa isi — poin-poin di bawah ini murni catatan teknis tambahan dari hasil review breakdown, **bukan** dari spec asli.

- **Keamanan dokumen KYC (KTP/NPWP/bukti transfer)** adalah data pribadi sensitif — wajib disimpan di disk privat dengan akses terautentikasi per-pemilik, bukan folder publik seperti disk `media` yang dipakai situs profile saat ini.
- **Ketelitian angka komisi**: skema Recurring Percentage butuh mekanisme yang jelas untuk tahu "pembayaran keberapa" sudah dihitung, supaya komisi tidak double-hitung atau ke-skip kalau job/cron sempat gagal jalan. Relevan lagi begitu Recurring Commission (future enhancement) mulai dikerjakan.
- ~~**Klarifikasi masih dibutuhkan**~~ — **semua terjawab final** lewat "Final Business Decisions" (2026-07-30): definisi Level Partner, siapa berwenang approve (RBAC), format export laporan.
