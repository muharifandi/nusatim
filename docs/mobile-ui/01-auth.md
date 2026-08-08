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

Teks tautan tambahan di bawah tombol "Masuk": "Lupa password?" → **Lupa Password**.

## Lupa Password (2 langkah)

Alur dua layar berurutan, mengikuti dua endpoint `forgot-password` + `reset-password` di [docs/api/01-auth.md](../api/01-auth.md). Tidak ada deep link email → app di versi awal ini — partner menyalin kode dari email lalu mengetiknya manual di layar kedua, jadi desain layar 2 harus membuat proses tempel-kode terasa cepat (field kode besar, auto-focus, dukung paste).

**Layar 1 — Minta Kode**
- Input `email` (prefill dari field email di Login kalau sudah pernah diisi).
- Tombol "Kirim Kode Reset" (`FilledButton`, disabled sampai email terisi & format valid).
- Teks bantu di bawah judul: "Masukkan email akun Anda, kami akan mengirimkan kode untuk reset password."

**State**:
- Loading: tombol jadi `CircularProgressIndicator` kecil, sama seperti Login.
- Sukses (`200` — selalu sukses secara UI terlepas email terdaftar atau tidak, lihat catatan keamanan di [docs/api/testing/01-auth.md](../api/testing/01-auth.md)): navigasi ke **Layar 2 — Masukkan Kode**, bawa `email` yang barusan diisi. Tampilkan snackbar/banner "Jika email terdaftar, kode telah dikirim. Cek email Anda."
- Gagal (`422`, terlalu sering mengirim ulang): tampilkan pesan error dari `errors.email` di bawah field, jangan navigasi.

**Layar 2 — Masukkan Kode & Password Baru**
- Banner info kecil: "Kode dikirim ke `{email}`" (email non-editable, tampil apa adanya dari layar 1) + tautan kecil "Kirim ulang" (kembali memanggil `forgot-password` dengan email yang sama, throttle sisi UI ±30 detik agar tidak memicu `422` server berulang-ulang).
- Input `token` (multiline/monospace, mendukung tempel dari clipboard — kode cukup panjang, jangan batasi jadi kotak-kotak OTP 6 digit ala SMS).
- Input `password` baru (toggle show/hide).
- Input `password_confirmation` (toggle show/hide).
- Tombol "Reset Password" (`FilledButton`, penuh lebar).

**State**:
- Loading: sama pola tombol seperti layar lain.
- Sukses (`200`): navigasi ke **Login** dengan snackbar "Password berhasil direset, silakan login dengan password baru." Kosongkan field password yang sempat diisi di layar mana pun (jangan simpan di state setelah ini).
- Gagal (`422`, kode salah/kedaluwarsa): tampilkan pesan error di bawah field `token`, biarkan partner mencoba lagi atau kembali ke Layar 1 untuk minta kode baru.

**Catatan penting**: reset password mencabut **semua** token API partner ini, termasuk yang sedang aktif di device lain (lihat [docs/api/01-auth.md](../api/01-auth.md)) — device lain akan mendapat `401` pada request berikutnya dan harus ditangani lewat interceptor HTTP terpusat yang sama seperti kasus logout/suspend (lihat catatan di bagian **Layar Status Approval** di bawah).

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
