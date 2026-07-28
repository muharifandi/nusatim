# TODO — Portal Partner / Sales Partner Management

Breakdown kerja untuk pengembangan **Portal Partner/Affiliator** + modul tambahan di **Admin existing**. Sumber: spec "Final Rekapan Fitur – Portal Partner/Sales Partner Management" (lihat ringkasan modul di bagian paling bawah file ini).

Status semua item: belum dikerjakan (`[ ]`). Centang (`[x]`) begitu selesai. Urutan fase disusun berdasarkan dependency teknis (fondasi dulu, baru fitur yang bergantung padanya) — bukan urutan penomoran di spec asli.

---

## Fase 0 — Keputusan Arsitektur (wajib selesai sebelum mulai coding)

Ini bukan fitur, tapi keputusan desain yang akan menentukan struktur seluruh modul di bawahnya. Jangan mulai Fase 1 sebelum poin-poin ini diputuskan.

- [ ] **Model akses**: Portal partner pakai guard/auth terpisah (`partner`) di project Laravel yang sama, atau aplikasi terpisah? (Rekomendasi: guard terpisah dalam satu project — reuse database, tapi partner tidak boleh bisa login ke `/admin` dan sebaliknya admin tidak login lewat portal partner)
- [ ] **Panel admin untuk modul baru**: apakah 8 modul admin baru (Partner Management, Project Board Management, dst) masuk ke Filament panel admin yang sudah ada (`/admin`), atau bikin panel Filament kedua khusus? (Rekomendasi: tetap satu panel admin, tambah grup navigasi baru "Partner Program")
- [ ] **Penamaan tabel database**: prefix/namespace supaya tidak bentrok dengan tabel situs profile yang sudah ada (mis. `partners`, `partner_leads`, `partner_projects`, dst — cek dulu tidak ada nama tabel yang sudah dipakai)
- [ ] **Penyimpanan dokumen sensitif (KTP, NPWP, bukti transfer)**: **jangan** pakai disk `media` yang sudah ada (itu `public_path()`, semua orang bisa akses langsung by URL). Perlu disk baru yang **tidak publicly accessible** — file di-serve lewat route terautentikasi yang mengecek pemilik dokumen sebelum stream file-nya.
- [ ] **Audit trail untuk data uang**: komisi & withdrawal tidak boleh sekadar kolom `status` yang di-update in-place tanpa jejak. Rencanakan tabel histori/log perubahan status (siapa approve, kapan, alasan reject, dll) sejak awal — bukan ditambah belakangan.
- [ ] **Precision angka uang**: pastikan semua kolom nominal pakai `decimal`, bukan `float`/`double` (mengulang standar yang sudah dipakai di `pricing_plans.price`).
- [ ] **Definisi "Level Partner"**: spec menyebut "Level Partner" di modul admin tapi tidak dijelaskan levelnya apa saja atau pengaruhnya ke apa (komisi berbeda? akses fitur berbeda?) — perlu klarifikasi dari pemberi spec sebelum dikerjakan.
- [ ] **Definisi role approval**: siapa yang approve apa? (registrasi partner, claim project, komisi, withdrawal — apakah semua admin bisa approve semua, atau ada pemisahan role/permission?)

---

## Fase 1 — Registrasi & Autentikasi Partner

- [ ] Migrasi tabel `partners` (data akun: nama, email, password, status registrasi, level, dst)
- [ ] Migrasi tabel dokumen partner (foto profil, KTP, NPWP — path ke disk privat, bukan disk publik)
- [ ] Migrasi tabel rekening bank partner (nama bank, no rekening, atas nama)
- [ ] Halaman Registrasi akun (form publik, tanpa login)
- [ ] Halaman Login partner
- [ ] Fitur Lupa Password (reset via email)
- [ ] Upload Foto Profil
- [ ] Upload KTP
- [ ] Upload NPWP (opsional, boleh dikosongkan)
- [ ] Input Data Rekening
- [ ] Halaman/modal Persetujuan Perjanjian Kemitraan (checkbox wajib centang sebelum submit registrasi)
- [ ] State machine status registrasi: `Draft` → `Pending Review` → `Approved` / `Rejected`
- [ ] Notifikasi email ke calon partner saat status berubah (approved/rejected)
- [ ] Halaman "menunggu approval" yang ditampilkan ke partner selama status masih `Pending Review`

---

## Fase 2 — Dashboard Partner

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

## Fase 3 — Lead & Opportunity Management

- [ ] Migrasi tabel `leads` (relasi ke partner pemilik)
- [ ] Tambah Lead (form)
- [ ] Edit Lead
- [ ] Halaman Detail Lead
- [ ] Upload Dokumen di Lead (relasi dokumen ke lead, disk terpisah dari dokumen KYC partner)
- [ ] Timeline aktivitas per Lead (log otomatis setiap ada perubahan/aktivitas)
- [ ] Catatan Internal (notes bebas, tidak terlihat customer)
- [ ] Reminder Follow Up (dengan tanggal/waktu, muncul di dashboard & notifikasi)
- [ ] Reminder Meeting (dengan tanggal/waktu, muncul di dashboard & notifikasi)
- [ ] State machine status lead: `New` → `Contacted` → `Qualified` → `Opportunity` → `Proposal` → `Negotiation` → `Won` / `Lost`
- [ ] Saat status jadi `Won`: trigger otomatis pembuatan record Customer (Fase 4)

---

## Fase 4 — Customer Management

- [ ] Migrasi tabel `customers` (partner pemilik, sumber dari lead yang closing)
- [ ] Halaman Profil Customer
- [ ] Data PIC customer
- [ ] Data Kontak
- [ ] Data Produk yang dibeli
- [ ] Nilai Project per customer
- [ ] Status Pembayaran
- [ ] Riwayat Aktivitas customer (gabungan timeline dari lead asal + aktivitas setelah jadi customer)

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

## Fase 6 — Sales Pipeline (Kanban)

- [ ] Tampilan Kanban board dengan kolom sesuai status lead (`New` s.d. `Lost`, sama seperti Fase 3)
- [ ] Drag & drop card lead antar kolom → update status
- [ ] Filter pipeline (per periode, per produk, dst — sesuaikan kebutuhan)

---

## Fase 7 — Project Board (Available Project)

- [ ] Migrasi tabel `partner_projects` (project yang dibuka admin, bisa diklaim partner)
- [ ] Listing project: Nama, Produk, Budget, Lokasi, Deadline, Tingkat Kesulitan, Nilai Komisi, Status
- [ ] Halaman Detail Project
- [ ] Fitur Claim Project (partner mengajukan klaim)
- [ ] Fitur Batalkan Claim (hanya selama status masih `Pending Approval`, belum diproses admin)
- [ ] State machine status: `Available` → `Pending Approval` → `Assigned` → `In Progress` → `Closed` / `Cancelled`
- [ ] Notifikasi ke partner saat klaim disetujui/ditolak admin

---

## Fase 8 — Project Management

- [ ] Listing project yang sudah terjual (hasil dari Fase 7 yang `Assigned`/`In Progress`, atau dari closing Lead di Fase 3)
- [ ] Tampilkan: Nama Project, Customer, Produk, Nilai Project, Status Pembayaran, Status Project, Progress
- [ ] Update progress project (oleh partner atau admin — putuskan di Fase 0)

---

## Fase 9 — Commission Management (sisi Partner)

- [ ] Migrasi tabel `commission_schemes` (lihat 3 jenis skema di bawah)
- [ ] Migrasi tabel `commissions` (per project/invoice, relasi ke scheme yang dipakai + histori status)
- [ ] Listing komisi: Project, Produk, Skema Komisi, Nilai Project, Nilai Invoice, Persentase, Nominal Komisi, Status
- [ ] State machine status komisi: `Pending` → `Waiting Client Payment` → `Approved` → `Paid` / `Rejected`

### Implementasi 3 jenis skema komisi

- [ ] **Percentage** — komisi = persentase × nilai project (sekali hitung, saat closing). Contoh: 10% × Rp100.000.000.
- [ ] **Recurring Percentage** — komisi dihitung ulang **setiap** client melakukan pembayaran/tagihan berikutnya (bukan sekali di awal). Butuh trigger/job yang jalan tiap ada pembayaran baru tercatat. Contoh: client bayar Rp5.000.000/bulan, komisi 10% → partner dapat Rp500.000 setiap pembayaran.
- [ ] **Flat Commission** — nominal tetap per unit penjualan, tidak bergantung nilai project. Contoh: setiap penjualan Produk A → komisi Rp2.000.000 (tetap, walau harga produk A berbeda-beda).

> Ketiga skema ini harus bisa hidup berdampingan — satu produk/partner/project bisa punya skema berbeda dari produk/partner/project lain (lihat Fase "Commission Scheme Management" di sisi Admin).

---

## Fase 10 — Withdrawal (Partner)

- [ ] Hitung & tampilkan saldo tersedia (komisi berstatus `Approved`/ready withdrawal)
- [ ] Ambil aturan Minimum Withdrawal dari pengaturan admin (Fase Partner Settings)
- [ ] Form Ajukan Withdrawal
- [ ] Pilih rekening (dari data rekening yang sudah diinput saat registrasi/profile)
- [ ] Upload Foto KTP — **wajib di setiap pengajuan penarikan** (bukan cukup sekali saat registrasi — ini beda dari KTP registrasi, tegaskan validasinya di kode)
- [ ] Catatan Penarikan (opsional, alasan/keterangan dari partner)
- [ ] State machine status: `Pending` → `Approved` → `Paid` / `Rejected`

---

## Fase 11 — Withdrawal History

- [ ] Listing riwayat withdrawal: Nominal, Rekening, Foto KTP (link lihat, bukan publik), Bukti Transfer (dari admin), Tanggal, Status

---

## Fase 12 — Marketing Center

- [ ] Migrasi tabel `marketing_materials` (kategori: brosur, company profile, price list, proposal, logo, banner, video, template WA, template email, FAQ, selling point)
- [ ] Halaman listing materi per kategori, bisa didownload partner
- [ ] (Sisi admin ada di Fase "Marketing Material" di bawah — upload-nya dari sana)

---

## Fase 13 — Notification Center

- [ ] Infrastruktur notifikasi in-app (tabel `notifications` atau pakai fitur notification bawaan Laravel)
- [ ] Trigger notifikasi: Lead Update
- [ ] Trigger notifikasi: Project Baru (tersedia untuk diklaim)
- [ ] Trigger notifikasi: Project Assignment (klaim disetujui)
- [ ] Trigger notifikasi: Reminder Follow Up
- [ ] Trigger notifikasi: Reminder Meeting
- [ ] Trigger notifikasi: Komisi Masuk
- [ ] Trigger notifikasi: Withdrawal Approved
- [ ] Trigger notifikasi: Pengumuman (broadcast dari admin ke semua/sebagian partner)

---

## Fase 14 — Profile Partner

- [ ] Edit Biodata
- [ ] Ganti Foto
- [ ] Ganti Password
- [ ] Edit Data Rekening
- [ ] Update KTP
- [ ] Update NPWP
- [ ] Preferensi Notifikasi (partner pilih notifikasi mana yang mau diterima, mis. lewat email vs in-app saja)

---

## Fase 15 (Admin) — Partner Management

- [ ] Approval Registrasi partner baru (lihat dokumen KTP/NPWP yang diupload, approve/reject)
- [ ] Suspend Partner
- [ ] Aktifkan kembali Partner
- [ ] Reset Password partner (dari sisi admin)
- [ ] Kelola Level Partner (menunggu klarifikasi dari Fase 0)

---

## Fase 16 (Admin) — Project Board Management

- [ ] CRUD Project (buat/edit/hapus project yang akan dibuka ke partner)
- [ ] Publish Project (ubah status jadi `Available`, baru muncul di Project Board partner)
- [ ] Assign Partner langsung (tanpa lewat mekanisme klaim, untuk kasus tertentu)
- [ ] Approve Claim (menyetujui klaim yang diajukan partner di Fase 7)
- [ ] Close Project

---

## Fase 17 (Admin) — Lead Monitoring

- [ ] Halaman monitoring seluruh lead semua partner (read access lintas partner, admin only)
- [ ] Transfer Ownership lead (pindah kepemilikan dari satu partner ke partner lain)
- [ ] Validasi Lead (admin verifikasi lead valid/tidak)
- [ ] Anti Duplicate — deteksi lead dengan kontak/data yang sama sudah pernah diinput (oleh partner lain atau partner yang sama)

---

## Fase 18 (Admin) — Commission Scheme Management

- [ ] Form buat skema komisi baru, pilih salah satu dari 3 jenis (Percentage / Recurring Percentage / Flat Commission)
- [ ] Pengaturan cakupan skema: Per Produk
- [ ] Pengaturan cakupan skema: Per Partner
- [ ] Pengaturan cakupan skema: Per Project
- [ ] Masa Berlaku skema (tanggal mulai/berakhir)
- [ ] Input Persentase (untuk skema Percentage/Recurring Percentage)
- [ ] Input Nominal Flat (untuk skema Flat Commission)

---

## Fase 19 (Admin) — Commission Management (sisi Admin)

- [ ] Generate Komisi (proses hitung komisi dari project/invoice yang closing, sesuai skema aktif)
- [ ] Approval Komisi
- [ ] Reject Komisi (dengan alasan, tercatat di histori)
- [ ] Bonus Komisi (komisi tambahan di luar skema normal, manual oleh admin)
- [ ] Riwayat Komisi (log lengkap perubahan status per komisi)

---

## Fase 20 (Admin) — Withdrawal Management

- [ ] Pengaturan Minimum Withdrawal (dipakai Fase 10 di sisi partner)
- [ ] Approval Withdrawal
- [ ] Verifikasi Foto KTP yang diupload partner saat pengajuan (tampilkan preview, bukan sekadar centang)
- [ ] Upload Bukti Transfer (setelah withdrawal benar-benar ditransfer ke partner)
- [ ] Reject Withdrawal (dengan alasan)
- [ ] Riwayat Withdrawal (semua partner, admin view)

---

## Fase 21 (Admin) — Marketing Material

- [ ] Upload Brosur
- [ ] Upload Proposal
- [ ] Upload Video
- [ ] Upload Banner
- [ ] Upload Template (WhatsApp & Email)

> Satu form/resource admin untuk kelola seluruh isi Marketing Center (Fase 12), dikelompokkan per kategori materi.

---

## Fase 22 (Admin) — Reports

- [ ] Laporan Partner (jumlah, status, performa ringkas)
- [ ] Laporan Lead (jumlah per status, per partner, per periode)
- [ ] Laporan Project (jumlah, status, nilai)
- [ ] Laporan Closing (tren closing per periode)
- [ ] Laporan Komisi (total, per status, per partner)
- [ ] Laporan Withdrawal (total, per status, per partner)
- [ ] Laporan Performa Partner (ranking/perbandingan antar partner)
- [ ] Laporan Nilai Penjualan (total omzet dari seluruh partner)
- [ ] Fitur export laporan (PDF/Excel — konfirmasi format yang dibutuhkan)

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
