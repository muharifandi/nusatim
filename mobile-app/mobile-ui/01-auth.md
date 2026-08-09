# Layar — Auth

API terkait: [docs/api/01-auth.md](../api/01-auth.md)

## Splash

**Tujuan**: cek token tersimpan, arahkan ke tujuan yang benar tanpa mengharuskan partner login ulang setiap buka aplikasi.

**Layout**: logo Nusatim di tengah, tanpa interaksi. Layar transisi, maksimal ~1 detik ditambah waktu panggilan `GET /auth/me`.

**Logika**:
1. Tidak ada token tersimpan → **Login**.
2. Ada token → panggil `GET /auth/me`.
   - `200` & `status: approved` → **Shell Utama** (tab Beranda).
   - `200` & `status` lain → **Layar Status Approval**.
   - `401` (token sudah tidak valid/dicabut) → hapus token lokal → **Login**.

## Login

**Layout** (dari atas ke bawah):
- Logo/branding.
- Input `email` (keyboard type email).
- Input `password` (dengan toggle show/hide, ikon mata).
- Tombol "Masuk" (`FilledButton`, penuh lebar) — disabled sampai kedua field terisi.
- Teks tautan "Belum punya akun? Daftar sekarang" → **Register**.

**State**:
- Loading: tombol berubah jadi `CircularProgressIndicator` kecil di dalam tombol (bukan overlay layar penuh), input di-disable sementara.
- Gagal (`422`): tampilkan pesan error generik di bawah field password (lihat catatan keamanan di [docs/api/testing/01-auth.md](../api/testing/01-auth.md) — jangan bedakan "email tidak ada" vs "password salah").

**Aksi**: submit sukses → simpan `token` di `EncryptedSharedPreferences` → cek `partner.status` dari response → **Shell Utama** kalau `approved`, **Layar Status Approval** kalau bukan.

## Register (wizard 4 langkah)

Meski API register cuma satu request (lihat [docs/api/01-auth.md](../api/01-auth.md)), tetap sajikan sebagai **wizard bertahap di UI** — form sepanjang ini kalau ditampilkan sekaligus akan terasa berat dan menakutkan, empat langkah kecil terasa lebih ringan diselesaikan. Semua data ditahan di state lokal (belum dikirim ke server) sampai langkah terakhir.

**Struktur**: `LinearProgressIndicator` atau step indicator titik-titik di atas (4 langkah), tombol "Lanjut"/"Kembali" di bawah tiap langkah, tombol "Daftar" (submit sungguhan) cuma muncul di langkah terakhir.

| Langkah | Field | Validasi di sisi klien sebelum "Lanjut" |
|---|---|---|
| 1. Akun | Nama, Email, Password, Konfirmasi Password | Email format valid, password ≥ 8 karakter, konfirmasi cocok |
| 2. Dokumen | Foto Profil (kamera/galeri), Foto KTP (kamera/galeri), Foto NPWP (opsional) | Foto Profil & KTP wajib dipilih; kompres gambar sebelum upload (target di bawah beberapa MB, batas server 4MB per file) |
| 3. Rekening Bank | Nama Bank, Nomor Rekening, Atas Nama | Semua wajib diisi |
| 4. Persetujuan | Tampilkan teks perjanjian kemitraan (scrollable card), checkbox "Saya sudah membaca dan menyetujui" | Tombol "Daftar" disabled sampai checkbox dicentang |

**Komponen upload foto** (dipakai di Langkah 2, dan dipakai ulang di [02-profile.md](02-profile.md)): thumbnail preview persegi dengan overlay ikon kamera, tap untuk buka `BottomSheet` pilihan "Ambil Foto" / "Pilih dari Galeri".

**Submit** (di langkah 4): kirim semua data terkumpul sebagai satu `multipart/form-data` ke `POST /auth/register`.

**State**:
- Loading: full-screen loading overlay (upload multipart bisa makan waktu, terutama koneksi lambat) dengan progress upload kalau memungkinkan.
- Sukses (`201`): navigasi ke **Login** dengan snackbar "Registrasi berhasil, silakan login" — **jangan** auto-login (lihat catatan di [docs/api/testing/01-auth.md](../api/testing/01-auth.md)).
- Gagal (`422`): kembali otomatis ke langkah yang field-nya error, tampilkan pesan di field terkait.

## Layar Status Approval

Ditampilkan untuk partner berstatus bukan `approved`. **Bukan bagian dari Shell Utama** (tanpa Bottom Navigation) — cuma dua hal yang bisa dilakukan partner di sini: lihat status, atau kelola profil sendiri.

**Layout**, berbeda tergantung `status`:

| Status | Ikon/warna | Judul | Isi |
|---|---|---|---|
| `pending_review` | Jam pasir, netral | "Menunggu Persetujuan" | "Pendaftaran Anda sedang ditinjau admin. Anda akan bisa mengakses seluruh fitur setelah disetujui." |
| `rejected` | Silang, merah | "Pendaftaran Ditolak" | Tampilkan `rejection_reason` dari response apa adanya |
| `suspended` | Larangan, merah | "Akun Ditangguhkan" | "Akun Anda ditangguhkan admin. Hubungi support untuk info lebih lanjut." |

**Aksi yang tersedia**:
- Tombol "Lihat/Edit Profil" → buka layar Edit Profil (lihat [02-profile.md](02-profile.md)) langsung, tanpa Bottom Navigation di sekitarnya (mis. sebagai layar penuh dengan tombol kembali ke Status Approval).
- Tombol "Tarik untuk Perbarui Status" atau ikon refresh di app bar — panggil ulang `GET /auth/me`, kalau status sudah berubah jadi `approved`, otomatis pindah ke Shell Utama.
- Tombol "Keluar" (logout).

**Catatan penting**: layar ini juga jadi tujuan otomatis kalau, di titik mana pun selagi partner sedang memakai Shell Utama, sebuah panggilan API modul bisnis mengembalikan `403` dengan body approval-gate (lihat [docs/api/00-overview.md](../api/00-overview.md)) — misal partner di-suspend admin di tengah sesi aktif. Tangani ini secara terpusat (satu interceptor HTTP), bukan ditangani manual di tiap layar.
