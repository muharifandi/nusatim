# Skenario Test — Withdrawals

Sumber: `tests/Feature/Api/WithdrawalApiTest.php` (7 test, semua hijau).

Lihat penjelasan fitur & contoh payload di [../09-withdrawals.md](../09-withdrawals.md).

| # | Skenario | Kondisi Awal | Aksi | Hasil yang Diharapkan |
|---|---|---|---|---|
| 1 | Endpoint saldo melaporkan angka yang benar | 1 komisi `approved` senilai `500.000`, admin set minimum penarikan `100.000` | `GET /withdrawals/balance` | `200`. `available_balance = 500000`, `minimum_withdrawal = 100000`. |
| 2 | Pengajuan valid berhasil, data rekening ikut ter-snapshot | Partner punya rekening BCA/123456/Budi tersimpan di profil, komisi `approved` `1.000.000` | `POST /withdrawals` dengan `amount: 500000` + file `ktp` | `201`. `data.amount = 500000`, `data.bank_name = "BCA"` (ikut tersalin dari profil otomatis), `data.status = "pending"`. |
| 3 | Pengajuan melebihi saldo ditolak | Komisi `approved` cuma `300.000` | `POST /withdrawals` dengan `amount: 1000000` | `422`. `errors.amount` ada. |
| 4 | Pengajuan di bawah minimum ditolak | Komisi `approved` `500.000`, minimum diset `200.000` | `POST /withdrawals` dengan `amount: 100000` | `422`. `errors.amount` ada. |
| 5 | List cuma menampilkan withdrawal milik sendiri | 1 withdrawal milik partner login (`100.000`), 1 milik partner lain (`200.000`) | `GET /withdrawals` | `200`. `data` berisi **1** item, `amount = 100000`. |
| 6 | Partner bisa stream dokumen KTP withdrawal sendiri | Withdrawal ada, `ktp_path` filenya ada di disk | `GET /withdrawals/{id}/documents/ktp` | `200`, body berupa file. |
| 7 | Tidak bisa akses dokumen withdrawal milik orang lain | Withdrawal milik partner lain | `GET /withdrawals/{id}/documents/ktp` pakai token partner login | `404`. |

## Catatan khusus untuk implementasi Android

- Skenario #1 wajib dipanggil **sebelum** menampilkan form pengajuan withdrawal — tampilkan `available_balance` dan `minimum_withdrawal` langsung di form supaya partner tahu batasannya sebelum submit, jangan biarkan mereka baru tahu lewat pesan error setelah submit.
- Skenario #3 dan #4: pesan error dari server (lihat contoh lengkap di [../09-withdrawals.md](../09-withdrawals.md)) sudah berisi nominal Rupiah terformat siap-tampil — bisa langsung dipakai sebagai teks error di form tanpa perlu diformat ulang.
- Skenario #2: **tidak ada field rekening bank di request** `POST /withdrawals` — kalau partner mau menarik ke rekening berbeda dari yang tersimpan di profil, mereka harus update dulu lewat `PUT /profile` (lihat [02-profile.md](02-profile.md)) **sebelum** membuka form withdrawal. Pertimbangkan menampilkan info rekening tujuan (dari data profil) di form withdrawal sebagai konfirmasi, dengan tautan cepat ke halaman edit profil kalau perlu diganti.
- Tidak ada endpoint untuk membatalkan/mengedit withdrawal yang sudah diajukan — sekali submit, sembunyikan semua aksi selain "lihat detail" di layar itu.
