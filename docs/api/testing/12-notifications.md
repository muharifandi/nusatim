# Skenario Test — Notifications

Sumber: `tests/Feature/Api/NotificationApiTest.php` (5 test, semua hijau).

Lihat penjelasan fitur & contoh payload di [../12-notifications.md](../12-notifications.md).

| # | Skenario | Kondisi Awal | Aksi | Hasil yang Diharapkan |
|---|---|---|---|---|
| 1 | List cuma menampilkan notifikasi milik sendiri | 1 notifikasi dikirim ke partner login, 1 dikirim ke partner lain | `GET /notifications` | `200`. `data` berisi **1** item, `title` sesuai notifikasi milik sendiri. |
| 2 | Jumlah belum-dibaca akurat | 2 notifikasi dikirim ke partner, keduanya belum dibaca | `GET /notifications/unread-count` | `200`. `unread_count = 2`. |
| 3 | Tandai satu notifikasi dibaca | 1 notifikasi terkirim, belum dibaca | `PATCH /notifications/{id}/read` (tanpa body) | `200`. `data.read_at` terisi (bukan `null`). `unread_count` partner ini jadi `0`. |
| 4 | Tandai semua sekaligus | 2 notifikasi terkirim, keduanya belum dibaca | `PATCH /notifications/read-all` | `200`. Setelah ini, `unread_count` partner jadi `0` untuk semua notifikasi. |
| 5 | Tidak bisa menandai notifikasi milik orang lain | Notifikasi dikirim ke partner lain | `PATCH /notifications/{id}/read` pakai token partner login, dengan `id` notifikasi milik partner lain | `404`. |

## Catatan khusus untuk implementasi Android

- Skenario #2 adalah endpoint yang paling sering dipanggil di antara semua endpoint Notifications — dirancang ringan (cuma angka) supaya aman dipanggil berkala (tiap app resume/foreground) untuk badge ikon lonceng, tanpa perlu menarik seluruh daftar notifikasi.
- Skenario #3: pertimbangkan memanggil endpoint ini otomatis saat partner **membuka** detail satu notifikasi dari daftar (bukan cuma lewat tombol eksplisit "tandai dibaca") — mengikuti pola UX umum aplikasi notifikasi.
- **Tidak ada push notification (FCM) bawaan** di API ini — kelima skenario di atas semuanya berbasis *pull* (aplikasi yang minta data ke server), bukan *push* (server yang mengirim ke device). Kalau produk butuh notifikasi real-time muncul meski app tertutup, itu perlu jalur terpisah (integrasi FCM sendiri) di luar cakupan API ini — lihat catatan yang sama di [../12-notifications.md](../12-notifications.md).
