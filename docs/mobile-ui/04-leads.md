# Layar — Leads (Lead & Opportunity)

API terkait: [docs/api/04-leads.md](../api/04-leads.md)

Bagian dari tab **Penjualan** (sub-tab "Lead", bersebelahan dengan sub-tab "Pipeline" — lihat [00-overview.md](00-overview.md)).

## Daftar Lead

**Layout**:
- `TopAppBar` dengan search bar yang bisa di-expand (ikon kaca pembesar → jadi input `search`), dan ikon filter di kanan.
- Chip filter horizontal-scroll di bawah app bar: satu chip per status (8 status) + chip "Semua" — tap untuk filter via `?status=`.
- `LazyColumn` daftar lead, tiap item (`Card`): nama, telepon, badge status di kanan atas, nilai estimasi (kalau ada) di kanan bawah, nama produk kecil di bawah nama.
- `FloatingActionButton` "+" pojok kanan bawah → **Form Tambah Lead**.

**State kosong**: "Belum ada lead. Mulai catat calon customer pertama Anda." + tombol "Tambah Lead" (memicu aksi sama seperti FAB).

## Form Tambah / Edit Lead

Satu layout dipakai untuk create dan edit (bedanya cuma judul app bar dan endpoint tujuan: `POST /leads` vs `PUT /leads/{id}`).

**Layout**: form vertikal — Nama, Telepon (keyboard numerik), Email (opsional), Dropdown Produk/Layanan, Nilai Estimasi (opsional, keyboard numerik dengan format ribuan otomatis saat mengetik). **Field status tidak ditampilkan di form ini** — status baru selalu `new` otomatis (lihat [docs/api/testing/04-leads.md](../api/testing/04-leads.md)), perubahan status dilakukan lewat Pipeline atau tombol khusus di Detail Lead, bukan lewat form edit biasa (memisahkan concern: form ini untuk data, bukan alur kerja).

Tombol "Simpan" di app bar.

## Detail Lead

**Layout**: `TopAppBar` dengan nama lead sebagai judul, ikon Edit dan ikon Hapus (menu `⋮`) di kanan. Di bawah app bar, `TabRow` dengan 4 tab:

### Tab "Info"
- Kartu ringkasan: semua field lead (telepon, email, produk, estimasi nilai), badge status besar di atas.
- **Kalau `status == "won"`**: tombol "Lihat Detail Customer" menonjol — arahkan ke Detail Customer terkait (butuh dicari lewat `GET /customers` dengan `lead_id` ini di sisi klien, karena API tidak mengembalikan ID customer langsung dari respons ubah status — lihat catatan di [docs/api/testing/04-leads.md](../api/testing/04-leads.md)).
- Tombol besar "Ubah Status" → `BottomSheet` daftar 8 status (radio button, status saat ini ditandai) → konfirmasi → `PATCH /leads/{id}/status`. **Kalau memilih `won`**, tampilkan dialog konfirmasi tambahan menjelaskan "Ini akan otomatis membuat data Customer" sebelum submit — efek sampingnya besar dan permanen, partner perlu tahu.

### Tab "Reminder"
- Daftar reminder (follow up & meeting dijadikan satu daftar, dibedakan lewat ikon: telepon untuk follow_up, kalender untuk meeting), diurutkan berdasarkan tanggal.
- Tiap item: tanggal/waktu, catatan singkat, checkbox/ikon untuk tandai selesai (tap langsung memicu `PATCH .../complete` tanpa perlu buka detail — micro-interaction cepat).
- Item yang sudah lewat tanggal dan belum selesai ditandai visual berbeda (misal teks merah/oranye) — pengingat halus tanpa notifikasi push.
- `FloatingActionButton` "+" → `BottomSheet` form Tambah Reminder (Dropdown tipe Follow Up/Meeting, `DateTimePicker`, catatan opsional).
- Swipe-to-delete atau menu `⋮` per item untuk hapus.

### Tab "Dokumen"
- Grid/daftar dokumen terupload, tiap item: ikon jenis file (PDF/gambar), nama dokumen, tanggal upload.
- Tap item → unduh/buka dokumen (stream lewat `download_url` dengan header Authorization, lihat [00-overview.md](00-overview.md)) — untuk PDF gunakan viewer bawaan/Intent ke aplikasi PDF, untuk gambar buka `Dialog` preview penuh layar.
- `FloatingActionButton` "+" → pilih file dari penyimpanan device / kamera → dialog input nama dokumen (opsional) → upload.
- Menu `⋮` per item → Hapus (dengan konfirmasi).

### Tab "Timeline"
- Daftar kronologis (terbaru di atas) — **read-only** untuk sebagian besar entri (`created`, `status_change`, `document` muncul otomatis dari server, tidak bisa dihapus/diedit).
- Tiap entri: ikon sesuai tipe, teks `body`, timestamp relatif ("2 jam lalu").
- `FloatingActionButton` "+" → **satu-satunya** aksi tulis di tab ini → dialog input teks singkat → `POST /leads/{id}/activities` (catatan manual, `type: note`).

## Interaksi lintas-tab

- Setelah **Ubah Status** (tab Info) sukses, tab Timeline harus di-refresh (bukan cuma cache lokal) karena server otomatis menambah entri baru di sana — lihat skenario di [docs/api/testing/04-leads.md](../api/testing/04-leads.md).
- Badge status di app bar/tab Info harus konsisten dengan badge yang sama dipakai di Daftar Lead dan Pipeline (satu komponen `StatusBadge` dipakai ulang, jangan implementasi warna terpisah tiap layar).
