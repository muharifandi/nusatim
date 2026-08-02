# Skenario Test — Profile

Sumber: `tests/Feature/Api/ProfileApiTest.php` (7 test, semua hijau).

Lihat penjelasan fitur & contoh payload di [../02-profile.md](../02-profile.md).

| # | Skenario | Kondisi Awal | Aksi | Hasil yang Diharapkan |
|---|---|---|---|---|
| 1 | Partner `pending_review` tetap bisa lihat & update profil sendiri | Partner berstatus `pending_review` | `GET /profile`, lalu `PUT /profile` ganti `name` | Keduanya `200` (bukan 403 — beda dari modul bisnis lain). Nama di database benar-benar berubah. |
| 2 | Update email ke email yang sudah dipakai partner lain gagal | Partner A pakai `taken@example.com`, Partner B login | `PUT /profile` dengan `email: taken@example.com` (sebagai partner B) | `422`. `errors.email` ada. |
| 3 | Ganti foto profil menghapus file lama | Partner sudah punya `profile_photo_path` lama (file benar-benar ada di disk) | `POST /profile/photo` dengan file baru | `200`. `profile_photo_path` di database berubah ke path baru. File path **lama** sudah tidak ada di disk (`Storage::assertMissing`), file **baru** ada (`assertExists`). |
| 4 | Partner bisa stream dokumen sendiri | Partner punya `ktp_path` yang filenya ada di disk | `GET /profile/documents/ktp` | `200`, body berupa file. |
| 5 | Tipe dokumen tidak dikenal ditolak | Partner login | `GET /profile/documents/passport` (tipe yang tidak ada dalam daftar `photo`/`ktp`/`npwp`) | `404`. |
| 6 | Ganti password gagal — password lama salah | Partner punya password `old-password` | `PUT /profile/password` dengan `current_password: wrong-password` | `422`. `errors.current_password` ada. |
| 7 | Ganti password berhasil, password baru langsung bisa dipakai login | Partner punya password `old-password` | `PUT /profile/password` dengan `current_password` benar + `password` baru, lalu `POST /auth/login` pakai password baru | Ganti password → `200`. Login berikutnya dengan password baru → `200` juga. |

## Catatan khusus untuk implementasi Android

- Skenario #1 penting untuk alur onboarding: setelah register, aplikasi harus tetap bisa membuka layar Profile (untuk perbaiki data kalau ada yang salah) **sebelum** partner disetujui admin — jangan blokir seluruh navigasi hanya karena status bukan `approved`, cuma modul bisnis yang perlu diblokir.
- Skenario #3: setelah ganti foto/KTP/NPWP berhasil, **jangan** cache gambar lama di `ImageView` berdasarkan URL — meskipun URL endpoint-nya sama (`GET /profile/documents/ktp`), isi file di baliknya sudah berbeda. Pertimbangkan invalidasi cache Coil/Glide (mis. tambahkan query param cache-buster atau paksa reload) setelah upload sukses.
