# Skenario Test — Auth

Sumber: `tests/Feature/Api/AuthApiTest.php` (12 test, semua hijau).

Lihat penjelasan fitur & contoh payload di [../01-auth.md](../01-auth.md).

| # | Skenario | Kondisi Awal | Aksi | Hasil yang Diharapkan |
|---|---|---|---|---|
| 1 | Register berhasil dengan dokumen lengkap | — | `POST /auth/register` dengan semua field + `profile_photo`, `ktp`, `npwp` | `201`. `data.status` = `pending_review`. File tersimpan di disk `partner_documents`. Email `PartnerRegistrationReceived` terkirim ke partner. |
| 2 | Register gagal tanpa dokumen wajib | — | `POST /auth/register` tanpa `profile_photo` dan `ktp` | `422`. `errors` berisi key `profile_photo` dan `ktp`. |
| 3 | Register gagal tanpa persetujuan perjanjian | — | `POST /auth/register` lengkap tapi tanpa `agreement_accepted` | `422`. `errors.agreement_accepted` ada. |
| 4 | Login berhasil, dapat token | Partner ada dengan password `password123` | `POST /auth/login` dengan email+password benar | `200`. Response punya key `token` (tidak kosong) dan `partner`. Baris baru muncul di tabel `personal_access_tokens` untuk partner ini. |
| 5 | Login gagal — password salah | Partner ada dengan password `password123` | `POST /auth/login` dengan password salah | `422`. `errors.email` berisi pesan "Email atau password salah." (bukan `errors.password` — pesan sengaja tidak membedakan email-tidak-ada vs password-salah). |
| 6 | Forgot-password mengirim email untuk partner terdaftar | Partner ada dengan email `budi@example.com` | `POST /auth/forgot-password` dengan email itu | `200`. Email `PartnerPasswordResetRequested` terkirim ke partner tersebut, berisi kode (token) yang tidak kosong. |
| 7 | Forgot-password untuk email tak terdaftar tetap `200` dan tidak mengirim apa pun | — | `POST /auth/forgot-password` dengan email yang tidak ada di database | `200`, pesan sama persis dengan skenario #6 (generik). **Tidak ada** email terkirim sama sekali. |
| 8 | Reset password dengan kode valid berhasil | Partner ada, punya token API aktif, kode reset dibuat via `Password::broker('partners')->createToken()` | `POST /auth/reset-password` dengan email+kode+password baru | `200`. Password ter-update (verifiable via `Hash::check`). **Semua** token API partner ini terhapus dari `personal_access_tokens`. Login ulang dengan password baru berhasil (`200`). |
| 9 | Reset password gagal dengan kode salah | Partner ada dengan password lama | `POST /auth/reset-password` dengan `token` asal-asalan | `422`. `errors.email` berisi pesan kode tidak valid. Password partner **tidak berubah** (masih bisa di-`Hash::check` dengan password lama). |
| 10 | Logout mencabut token yang dipakai | Partner login, punya token aktif | `POST /auth/logout` pakai token itu | `200`. Baris token di `personal_access_tokens` terhapus. **Request susulan** pakai token yang sama ke endpoint mana pun → `401`. |
| 11 | `GET /auth/me` mengembalikan partner yang benar | Partner login, punya token | `GET /auth/me` pakai token itu | `200`. `data.id` dan `data.name` sesuai partner pemilik token. |
| 12 | Endpoint terproteksi menolak tanpa token | — | `GET /auth/me` **tanpa** header `Authorization` | `401`. *(Ini baseline untuk SEMUA endpoint lain di seluruh API — tidak diulang per modul, lihat [00-overview.md](00-overview.md))* |

## Catatan khusus untuk implementasi Android

- Skenario #5 (pesan error login) sengaja generik — **jangan** tampilkan pesan berbeda untuk "email tidak terdaftar" vs "password salah" di UI aplikasi (potensi celah keamanan kalau dibedakan, karena orang jadi bisa menebak email mana yang terdaftar). Skenario #6/#7 (forgot-password) mengikuti prinsip yang sama — respons selalu identik.
- Skenario #8: karena reset password mencabut **semua** token, kalau partner login di banyak device lalu reset password dari salah satu, device lain harus menangani `401` pada request berikutnya sebagai "sesi habis, minta login ulang" — sama seperti penanganan logout di skenario #10.
- Kode reset dari email berlaku 60 menit dan sekali pakai — kalau aplikasi menampilkan countdown, jangan hardcode angka lain di client, ambil dari salinan dokumentasi di [../01-auth.md](../01-auth.md) kalau nilainya berubah di masa depan.
- Skenario #10: setelah logout sukses, aplikasi **wajib** menghapus token dari local storage sebelum request berikutnya — kalau tidak, request berikutnya akan dapat 401 dan harus ditangani sebagai "sesi habis, minta login ulang", bukan crash.
- Register (skenario #1) **tidak** menguji auto-login — pastikan alur UI aplikasi mengarahkan ke layar Login setelah register berhasil, bukan langsung masuk ke Dashboard.
