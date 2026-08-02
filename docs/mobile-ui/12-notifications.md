# Layar — Notifications

API terkait: [docs/api/12-notifications.md](../api/12-notifications.md)

Diakses lewat ikon lonceng di `TopAppBar` (lihat pola navigasi di [00-overview.md](00-overview.md)) — **bukan** tab Bottom Navigation tersendiri.

## Badge Lonceng

Di setiap layar yang menampilkan ikon lonceng (minimal Beranda): panggil `GET /notifications/unread-count` secara berkala — saat aplikasi dibuka/resume dari background, dan setelah menutup layar Daftar Notifikasi. Titik merah kecil muncul di sudut ikon kalau `unread_count > 0`, tanpa perlu menampilkan angka pastinya (badge titik lebih umum dipakai daripada angka untuk notifikasi ringan seperti ini).

## Daftar Notifikasi

**Layout**:
- `TopAppBar`: judul "Notifikasi", tombol teks "Tandai semua dibaca" di kanan (`PATCH /notifications/read-all`) — sembunyikan/nonaktifkan tombol ini kalau `unread_count` sudah 0.
- `LazyColumn`, tiap item (`ListItem`): titik indikator kecil di kiri kalau `read_at == null` (belum dibaca — beri latar sedikit berbeda juga, mis. `surfaceContainerHigh`, supaya kontras jelas dari yang sudah dibaca tanpa mengandalkan titik saja), judul (`title`), cuplikan `body` kalau ada, waktu relatif ("2 jam lalu") di kanan.
- Pagination standar (infinite scroll, lihat [00-overview.md](00-overview.md)), default 20 per halaman sesuai API.

**Interaksi tap item**:
1. Tap notifikasi → panggil `PATCH /notifications/{id}/read` (kalau belum dibaca) di background, ubah tampilan item jadi "sudah dibaca" secara optimis di UI.
2. **Navigasi kontekstual tidak didukung otomatis oleh API** (`title`/`body` cuma teks, tidak ada field seperti `deep_link` atau `related_id`) — untuk versi awal, tap notifikasi cukup menandainya sudah dibaca tanpa berpindah layar. Kalau produk menginginkan tap-to-navigate (mis. notifikasi "Lead X: status jadi won" langsung membuka Detail Lead X), itu butuh perubahan di API (menambah field referensi entitas terkait) — catat sebagai kebutuhan API tambahan untuk tim backend, jangan coba tebak-tebak parsing dari teks `title` di sisi aplikasi (rapuh, gampang salah kalau format pesan berubah).

**State kosong**: "Belum ada notifikasi."
