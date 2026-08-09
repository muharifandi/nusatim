# Skenario Test — Support Tickets

Sumber: `tests/Feature/Api/SupportTicketApiTest.php` (4 test, semua hijau).

Lihat penjelasan fitur & contoh payload di [../11-support-tickets.md](../11-support-tickets.md).

| # | Skenario | Kondisi Awal | Aksi | Hasil yang Diharapkan |
|---|---|---|---|---|
| 1 | Tiket baru berhasil dibuat dengan status default | — | `POST /support-tickets` dengan `subject`+`description` | `201`. `data.status = "open"` (otomatis, tidak bisa diatur dari request). Tersimpan di database dengan `partner_id` yang benar. |
| 2 | List cuma menampilkan tiket milik sendiri | 1 tiket milik partner login, 1 milik partner lain | `GET /support-tickets` | `200`. `data` berisi **1** item. |
| 3 | Tiket yang sudah diselesaikan menampilkan catatan penyelesaian | Tiket dibuat lalu diselesaikan admin (`resolve()`) dengan catatan tertentu | `GET /support-tickets/{id}` | `200`. `data.status = "resolved"`, `data.resolution_note` berisi catatan admin. |
| 4 | Lihat tiket milik partner lain → 404 | Tiket dibuat untuk partner lain | `GET /support-tickets/{id}` pakai token partner login | `404`. |

## Catatan khusus untuk implementasi Android

- Tidak ada endpoint update/delete tiket — begitu dibuat, layar detail tiket cukup menampilkan status dan (kalau ada) `resolution_note`, tanpa aksi lanjutan apa pun dari sisi partner.
- Skenario #3: `resolution_note` cuma terisi setelah admin menyelesaikan tiket — tampilkan section "Jawaban/Solusi" secara kondisional (sembunyikan kalau `null`), bukan selalu ditampilkan kosong.
- Tidak ada sistem balas-membalas (chat/thread) dalam satu tiket — kalau partner ingin menambahkan info setelah tiket dibuat, satu-satunya jalan saat ini adalah membuat tiket baru. Sampaikan ini ke tim produk kalau UI perlu menyesuaikan ekspektasi (misal beri disclaimer di layar buat-tiket).
