# Spesifikasi UI/UX Aplikasi Mobile Partner Portal

Spesifikasi layar untuk aplikasi Android partner, dipetakan 1:1 ke modul API yang sudah ada di [`../api/`](../api/00-overview.md). Penomoran file di folder ini sama dengan `docs/api/` — `04-leads.md` di sini adalah spesifikasi layar untuk `docs/api/04-leads.md`.

Ini **spesifikasi fungsional & struktural** (layout, komponen, state, navigasi), bukan mockup visual pixel-perfect — cukup detail untuk tim desain membuat Figma dan tim Android mulai membangun, tanpa mengunci keputusan visual kecil (spacing persis, ukuran font persis) yang lebih baik diputuskan di tools desain.

## Sistem Desain

**Material 3 (Material You)** — pilihan paling wajar untuk aplikasi Android modern per 2026: dynamic color, komponen sudah teruji aksesibilitas, dan tooling resminya (Jetpack Compose Material3) matang.

### Warna

Warna aksen mengikuti **warna Partner Portal versi web yang sudah ada** (`#a6541a`, terracotta hangat) — supaya partner merasakan brand yang konsisten saat berpindah dari browser ke aplikasi, bukan dua identitas berbeda. Generate tonal palette Material 3 dari seed color ini (13 tone per role: primary/secondary/tertiary/neutral/error), bukan pilih warna manual per komponen.

| Role | Sumber | Dipakai untuk |
|---|---|---|
| Primary | `#a6541a` (seed) | Tombol utama, FAB, tab aktif, ikon terpilih |
| Secondary | Diturunkan otomatis dari seed | Chip filter, badge sekunder |
| Tertiary | Diturunkan otomatis dari seed | Aksen dekoratif, highlight chart |
| Error | Merah standar Material 3 | Validasi gagal, aksi destruktif |

**Warna semantik status** (dipakai badge status lead/project/commission/withdrawal, dst) **terpisah dari warna aksen brand** — jangan pakai `primary` untuk arti "sukses", karena itu bikin ambigu:

| Semantik | Contoh pemakaian |
|---|---|
| Netral/abu | `new`, `pending`, `open`, `draft` |
| Kuning/amber | `pending_approval`, `waiting_client_payment`, `in_progress` |
| Biru/info | `assigned`, `contacted`, `approved` (komisi/klaim, bukan "selesai") |
| Hijau/sukses | `won`, `approved` (partner), `paid`, `closed`, `resolved` |
| Merah/error | `lost`, `rejected`, `cancelled`, `suspended` |

**Wajib dukung Dark Mode** — semua token warna didefinisikan sebagai pasangan light/dark (Material 3 `ColorScheme` sudah menyediakan pola ini secara bawaan), bukan warna hardcode.

### Tipografi

Pakai **Material 3 type scale** langsung (Display/Headline/Title/Body/Label, masing-masing Large/Medium/Small) — jangan definisikan skala custom dari nol. Font: font sistem Android default (Roboto Flex / sesuai device) sudah cukup profesional untuk aplikasi B2B seperti ini; kalau tim desain ingin identitas lebih kuat, satu display font custom untuk judul (Headline/Title) saja sudah cukup, body text tetap font sistem untuk keterbacaan angka/tabel.

### Bentuk & Elevasi

- Card: rounded corner besar (`16dp`), elevasi rendah (`1-2dp`) — hindari shadow tebal, sesuai tren Material 3 yang lebih flat dengan pembeda warna (`surfaceContainer`) daripada bayangan.
- Tombol utama: rounded penuh (pill shape) untuk `FilledButton`/FAB, sesuai default Material 3.
- Badge status: chip kecil rounded penuh, warna sesuai tabel semantik di atas.

### Angka & Uang

Semua nominal Rupiah diformat di sisi aplikasi (API selalu mengirim angka mentah, lihat [docs/api/00-overview.md](../api/00-overview.md)) — format `Rp1.234.567` (titik pemisah ribuan, tanpa desimal untuk Rupiah). Pakai font tabular (`fontFeatureSettings = "tnum"` atau setara) di layar yang menampilkan banyak angka sejajar (daftar komisi, withdrawal) supaya digit rata secara visual.

## Arsitektur Navigasi

### Alur sebelum masuk aplikasi utama (di luar Bottom Navigation)

```
Splash → [ada token tersimpan & valid?]
           ├─ Ya, status "approved"        → Shell Utama (Beranda)
           ├─ Ya, status bukan "approved"  → Layar Status Approval
           └─ Tidak ada token / kadaluarsa → Login
                                               ├─ Login berhasil → (cek status, lanjut seperti di atas)
                                               └─ "Belum punya akun?" → Register (wizard 4 langkah)
                                                                          → selesai → kembali ke Login
```

### Shell utama — Bottom Navigation (5 destinasi)

Konten dipilih supaya masing-masing tab berdiri sebagai satu domain kerja yang jelas — bukan sekadar sesuai urutan modul API. Modul yang secara konsep berdekatan (Lead → Pipeline → Customer, atau Commission → Withdrawal) digabung dalam satu tab lewat **tab sekunder di dalam layar** (segmented control / `TabRow` di bagian atas), bukan dipisah jadi 8-9 item bottom nav yang tidak akan muat dan sulit dipakai satu tangan.

| # | Tab | Ikon (Material Symbols) | Isi |
|---|---|---|---|
| 1 | **Beranda** | `home` | Dashboard (lihat [03-dashboard.md](03-dashboard.md)) |
| 2 | **Penjualan** | `trending_up` | Sub-tab: *Pipeline* · *Lead* · *Customer* (lihat [04-leads.md](04-leads.md), [05-pipeline.md](05-pipeline.md), [06-customers.md](06-customers.md)) |
| 3 | **Proyek** | `work` | Project Board, sub-tab: *Tersedia* · *Punya Saya* (lihat [07-projects.md](07-projects.md)) |
| 4 | **Keuangan** | `payments` | Sub-tab: *Komisi* · *Withdrawal* (lihat [08-commissions.md](08-commissions.md), [09-withdrawals.md](09-withdrawals.md)) |
| 5 | **Profil** | `person` | Menu profil + jalan masuk ke Marketing Center, Support Ticket (lihat [02-profile.md](02-profile.md), [10-marketing-materials.md](10-marketing-materials.md), [11-support-tickets.md](11-support-tickets.md)) |

**Notifikasi tidak jadi tab bottom nav** — diakses lewat ikon lonceng di `TopAppBar`, muncul persisten di tab Beranda (dan idealnya di semua layar level-atas), dengan badge titik merah kalau `unread_count > 0` (lihat [12-notifications.md](12-notifications.md)). Ini pola standar di hampir semua aplikasi modern (notifikasi bukan "tujuan navigasi utama", tapi interupsi sewaktu-waktu).

### Peta layar per tab (ringkas — detail penuh di file masing-masing)

```
Beranda
└─ (tap kartu statistik) → deep link ke Lead List / Customer List / dst terfilter

Penjualan
├─ Pipeline (papan kanban)
│   └─ tap kartu lead → Detail Lead
├─ Lead (daftar + filter/search)
│   ├─ tap item → Detail Lead
│   └─ FAB "+" → Form Tambah Lead
│       Detail Lead (tab: Info · Reminder · Dokumen · Timeline)
│       ├─ Info: tombol Edit → Form Edit Lead
│       ├─ Reminder: FAB → Bottom sheet Tambah Reminder
│       ├─ Dokumen: FAB → upload dokumen
│       └─ jika status "won" → tombol "Lihat Customer" → Detail Customer
└─ Customer (daftar)
    └─ tap item → Detail Customer (tab: Info · Timeline · Follow Up/Meeting · Proposal)

Proyek
├─ Tersedia → tap item → Detail Project → tombol "Klaim"
└─ Punya Saya → tap item → Detail Project → tombol "Batalkan Klaim" (kalau masih pending_approval)

Keuangan
├─ Komisi (daftar + filter chip status) → tap item → Detail Komisi
└─ Withdrawal (daftar) → tap item → Detail Withdrawal
    └─ FAB "+" → Form Ajukan Withdrawal

Profil
├─ Edit Profil (biodata, rekening)
├─ Dokumen KYC (lihat/ganti foto, KTP, NPWP)
├─ Ganti Password
├─ Marketing Center → daftar per kategori → Detail Materi
├─ Support Ticket → daftar → Detail Tiket, FAB → Form Buat Tiket
└─ Keluar (logout, dengan dialog konfirmasi)
```

## Pola & Komponen Bersama

Dipakai berulang di banyak layar — didefinisikan sekali di sini supaya konsisten, tidak diulang di tiap file modul.

| Pola | Kapan dipakai | Spesifikasi |
|---|---|---|
| **Skeleton loading** | Setiap layar yang menunggu response API pertama kali | Bentuk placeholder abu-abu menyerupai layout asli (bukan spinner polos di tengah layar) — standar UX modern, mengurangi persepsi waktu tunggu |
| **Pull-to-refresh** | Semua layar daftar (list) | `SwipeRefresh` standar Material 3 di atas `LazyColumn` |
| **Infinite scroll / pagination** | Semua endpoint yang dipaginasi (lihat [docs/api/00-overview.md](../api/00-overview.md)) | Load halaman berikutnya otomatis saat scroll mendekati akhir daftar, indikator loading kecil di baris terakhir |
| **Empty state** | Daftar kosong (belum ada data) | Ilustrasi/ikon besar + judul singkat + kalimat penjelas + CTA kalau relevan (mis. "Belum ada lead. Tambah lead pertama Anda.") — jangan cuma layar putih kosong |
| **Error state** | Request gagal (500, tidak ada koneksi) | Ikon + pesan + tombol "Coba Lagi", bedakan pesan untuk "tidak ada koneksi internet" vs "server bermasalah" |
| **Empty state karena approval** | Modul bisnis diakses partner belum `approved` (403, lihat [docs/api/00-overview.md](../api/00-overview.md)) | **Bukan** error biasa — redirect penuh ke Layar Status Approval, jangan tampilkan sebagai "gagal muat data" |
| **Snackbar** | Konfirmasi aksi berhasil/gagal yang tidak butuh layar penuh (submit form, hapus item) | Snackbar bawah layar, auto-hilang, warna sesuai semantik (hijau sukses/merah gagal) |
| **Dialog konfirmasi** | Aksi destruktif atau tidak bisa dibatalkan (hapus lead/dokumen/reminder, klaim project, logout) | `AlertDialog` Material 3 — jelaskan konsekuensi di body teks, bukan cuma "Yakin?" |
| **Badge status** | Menampilkan field `status` dari API manapun | Chip kecil rounded penuh, warna sesuai tabel semantik di atas, label dalam Bahasa Indonesia (map dari value API, jangan tampilkan raw value seperti `pending_approval` mentah-mentah) |
| **Dokumen privat (viewer)** | Menampilkan gambar dari endpoint yang butuh `Authorization` header (lihat [docs/api/00-overview.md](../api/00-overview.md)) | Loader gambar (Coil/Glide) dikonfigurasi mengirim header Authorization di tiap request gambar — **jangan** cuma tempel token di URL sebagai query param |
| **Format nominal** | Semua field uang | Lihat bagian "Angka & Uang" di atas |
| **Pesan validasi (422)** | Semua form | Tampilkan pesan per field tepat di bawah input yang error (dari `errors.<field>`), bukan cuma toast generik |
