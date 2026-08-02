# Layar — Profile

API terkait: [docs/api/02-profile.md](../api/02-profile.md)

## Tab Profil (halaman utama tab ke-5 di Bottom Navigation)

**Layout**:
- Header: foto profil bulat besar (dari `profile_photo_url`) + nama + email, latar `primaryContainer`.
- Badge status approval kecil di bawah nama (`Disetujui` hijau / dsb — sama palet semantik seperti [00-overview.md](00-overview.md)).
- Daftar menu (`ListItem` Material 3, ikon di kiri, chevron di kanan):
  - "Edit Profil" (ikon `edit`)
  - "Dokumen KYC" (ikon `badge`)
  - "Ganti Password" (ikon `lock`)
  - "Rekening Bank" — bisa digabung ke "Edit Profil" (lihat di bawah) alih-alih menu terpisah
  - *(divider)*
  - "Marketing Center" (ikon `campaign`) → [10-marketing-materials.md](10-marketing-materials.md)
  - "Support Ticket" (ikon `support_agent`) → [11-support-tickets.md](11-support-tickets.md)
  - *(divider)*
  - "Keluar" (ikon `logout`, warna teks merah/error)

## Edit Profil

**Layout**: form satu layar, dibagi jadi beberapa `Section` dengan judul kecil:
- **Biodata**: Nama, Email.
- **Rekening Bank**: Nama Bank, Nomor Rekening, Atas Nama.
- **Preferensi**: Switch "Notifikasi lewat email juga" (`email_notifications_enabled`).

Tombol "Simpan" di app bar (ikon centang) atau tombol penuh di bawah — kirim cuma field yang berubah lewat `PUT /profile` (partial update, sesuai API).

**State gagal**: email sudah dipakai partner lain → pesan error di bawah field email (lihat skenario di [docs/api/testing/02-profile.md](../api/testing/02-profile.md)).

## Dokumen KYC

**Layout**: tiga kartu berurutan, satu per dokumen (Foto Profil, KTP, NPWP) — tiap kartu:
- Thumbnail dokumen saat ini (dimuat dari `*_url` dengan header Authorization, lihat pola di [00-overview.md](00-overview.md)). Kalau `null` (khusus NPWP, opsional), tampilkan placeholder "Belum diupload".
- Tombol "Ganti Foto" di tiap kartu → buka `BottomSheet` kamera/galeri yang sama seperti di Register → submit ke endpoint terkait (`POST /profile/photo`, `/ktp`, atau `/npwp`).

**State sukses**: thumbnail di kartu langsung ter-update dengan gambar baru (invalidasi cache gambar, lihat catatan di [docs/api/testing/02-profile.md](../api/testing/02-profile.md)), snackbar "Foto berhasil diperbarui".

## Ganti Password

**Layout**: form sederhana tiga field — Password Saat Ini, Password Baru, Konfirmasi Password Baru (semua dengan toggle show/hide). Tombol "Simpan" full-width di bawah.

**State gagal**: password lama salah → pesan error di bawah field "Password Saat Ini" (bukan toast generik, lihat skenario terkait). Sukses → snackbar konfirmasi, kembali ke tab Profil (token tetap berlaku, tidak perlu login ulang).

## Logout

**Aksi**: tap "Keluar" → `AlertDialog` konfirmasi ("Yakin ingin keluar?") → kalau ya, panggil `POST /auth/logout`, hapus token dari penyimpanan lokal terlepas dari sukses/gagalnya panggilan API itu (kalau device offline saat logout, tetap hapus token lokal supaya partner tidak terjebak), navigasi ke **Login**.
