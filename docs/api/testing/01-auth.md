# Skenario Test — Auth

Sumber: `tests/Feature/Api/AuthApiTest.php` (8 test, semua hijau).

Lihat penjelasan fitur & contoh payload di [../01-auth.md](../01-auth.md).

| # | Skenario | Kondisi Awal | Aksi | Hasil yang Diharapkan |
|---|---|---|---|---|
| 1 | Register berhasil dengan dokumen lengkap | — | `POST /auth/register` dengan semua field + `profile_photo`, `ktp`, `npwp` | `201`. `data.status` = `pending_review`. File tersimpan di disk `partner_documents`. Email `PartnerRegistrationReceived` terkirim ke partner. |
| 2 | Register gagal tanpa dokumen wajib | — | `POST /auth/register` tanpa `profile_photo` dan `ktp` | `422`. `errors` berisi key `profile_photo` dan `ktp`. |
| 3 | Register gagal tanpa persetujuan perjanjian | — | `POST /auth/register` lengkap tapi tanpa `agreement_accepted` | `422`. `errors.agreement_accepted` ada. |
| 4 | Login berhasil, dapat token | Partner ada dengan password `password123` | `POST /auth/login` dengan email+password benar | `200`. Response punya key `token` (tidak kosong) dan `partner`. Baris baru muncul di tabel `personal_access_tokens` untuk partner ini. |
| 5 | Login gagal — password salah | Partner ada dengan password `password123` | `POST /auth/login` dengan password salah | `422`. `errors.email` berisi pesan "Email atau password salah." (bukan `errors.password` — pesan sengaja tidak membedakan email-tidak-ada vs password-salah). |
| 6 | Logout mencabut token yang dipakai | Partner login, punya token aktif | `POST /auth/logout` pakai token itu | `200`. Baris token di `personal_access_tokens` terhapus. **Request susulan** pakai token yang sama ke endpoint mana pun → `401`. |
| 7 | `GET /auth/me` mengembalikan partner yang benar | Partner login, punya token | `GET /auth/me` pakai token itu | `200`. `data.id` dan `data.name` sesuai partner pemilik token. |
| 8 | Endpoint terproteksi menolak tanpa token | — | `GET /auth/me` **tanpa** header `Authorization` | `401`. *(Ini baseline untuk SEMUA endpoint lain di seluruh API — tidak diulang per modul, lihat [00-overview.md](00-overview.md))* |

## Catatan khusus untuk implementasi Android

- Skenario #5 (pesan error login) sengaja generik — **jangan** tampilkan pesan berbeda untuk "email tidak terdaftar" vs "password salah" di UI aplikasi (potensi celah keamanan kalau dibedakan, karena orang jadi bisa menebak email mana yang terdaftar).
- Skenario #6: setelah logout sukses, aplikasi **wajib** menghapus token dari local storage sebelum request berikutnya — kalau tidak, request berikutnya akan dapat 401 dan harus ditangani sebagai "sesi habis, minta login ulang", bukan crash.
- Register (skenario #1) **tidak** menguji auto-login — pastikan alur UI aplikasi mengarahkan ke layar Login setelah register berhasil, bukan langsung masuk ke Dashboard.
