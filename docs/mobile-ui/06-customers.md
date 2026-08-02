# Layar — Customers

API terkait: [docs/api/06-customers.md](../api/06-customers.md)

Bagian dari tab **Penjualan** (sub-tab "Customer"). **Tidak ada tombol "Tambah Customer" di mana pun** — customer selalu muncul otomatis dari Lead yang `won` atau Project yang klaimnya disetujui (lihat [04-leads.md](04-leads.md), [07-projects.md](07-projects.md)).

## Daftar Customer

**Layout**: `LazyColumn`, tiap item (`Card`): nama customer, nama produk, nilai project, badge `payment_status` (Belum Bayar/Sebagian/Lunas — warna semantik sesuai [00-overview.md](00-overview.md)), indikator kecil progress project (bar tipis) kalau `project` tidak `null`.

**State kosong**: "Belum ada customer. Customer akan muncul otomatis begitu ada lead yang berhasil closing." — kalimat ini penting supaya partner tidak bingung mencari tombol tambah yang memang tidak ada.

## Detail Customer

**Layout**: `TopAppBar` dengan nama customer, `TabRow` 4 tab.

### Tab "Info"
- Kartu data utama: PIC (nama/telepon/email), produk, nilai project, `payment_status` (dengan tombol edit kecil untuk field-field ini → dialog/form inline → `PUT /customers/{id}`).
- **Kartu Project** (cuma muncul kalau `project` tidak `null`): nama project, badge status project, progress bar besar dengan tombol "Update Progress" → dialog slider 0-100 → `PATCH /customers/{id}/progress`.
- **Kartu Komisi** (cuma muncul kalau `commission` tidak `null`): status komisi + nominal, tombol "Lihat Detail" → **Detail Komisi** ([08-commissions.md](08-commissions.md)).

### Tab "Timeline"
- Sama persis pola tampilan dengan tab Timeline di Detail Lead ([04-leads.md](04-leads.md)) — daftar kronologis dari field `timeline`. **Read-only sepenuhnya di sini, tidak ada FAB tambah catatan** (beda dari Lead) — kalau ingin menambah catatan, dilakukan lewat Lead asalnya, bukan dari customer (API tidak menyediakan endpoint tambah activity di modul Customer).

### Tab "Follow Up & Meeting"
- Dua daftar terpisah (atau segmented sub-toggle) dari field `follow_ups` dan `meetings` — **read-only**, ini riwayat reminder dari lead asal, dikelola lewat tab Reminder di Detail Lead, bukan dari sini.

### Tab "Proposal"
- Daftar dari field `proposal_documents` — tiap item tap untuk buka/unduh (sama pola viewer dokumen seperti tab Dokumen di Detail Lead). **Read-only**, upload dokumen baru dilakukan lewat Detail Lead.

**Catatan desain**: tiga dari empat tab di layar ini murni read-only (Timeline, Follow Up/Meeting, Proposal) — pertimbangkan banner kecil non-intrusif di bagian atas ketiga tab itu: "Data ini berasal dari Lead asal" dengan tautan pintas ke Detail Lead terkait, supaya partner tidak bingung kenapa tidak ada tombol edit di sana.
