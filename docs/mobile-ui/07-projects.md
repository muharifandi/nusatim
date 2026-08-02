# Layar — Projects (Project Board)

API terkait: [docs/api/07-projects.md](../api/07-projects.md)

Tab **Proyek** di Bottom Navigation.

## Papan Project

**Layout**:
- `TabRow` atas dengan 2 sub-tab: **Tersedia** vs **Punya Saya** — meski API-nya satu endpoint (`GET /projects`, sudah menggabungkan available + milik sendiri, lihat [docs/api/07-projects.md](../api/07-projects.md)), pisahkan di UI berdasarkan field `is_mine` supaya partner tidak perlu memilah manual di satu daftar campur aduk.
- `LazyColumn`, tiap item (`Card`): nama project, badge status, nilai budget, estimasi komisi, lokasi, deadline (format tanggal relatif kalau mendekati, mis. "3 hari lagi").

**Sub-tab "Tersedia"**: cuma tampilkan item dengan `status: available`. Tiap kartu punya tombol "Klaim" langsung di kartu (tanpa harus masuk detail dulu) — mempercepat aksi paling umum di tab ini, karena project available sering diperebutkan (lihat catatan race condition di bawah).

**Sub-tab "Punya Saya"**: semua project dengan `is_mine: true`, badge status menunjukkan tahap (`pending_approval`/`assigned`/`in_progress`/`closed`/`cancelled`). Tap → **Detail Project**.

## Detail Project

**Layout**: kartu info lengkap (deskripsi, budget, lokasi, deadline, tingkat kesulitan, estimasi komisi), badge status besar di atas.

**Aksi berdasarkan status** (tombol besar di bawah, cuma satu yang tampil sesuai kondisi):

| `status` | `is_mine` | Tombol |
|---|---|---|
| `available` | — | "Klaim Project Ini" |
| `pending_approval` | `true` | "Batalkan Klaim" |
| `assigned` / `in_progress` | `true` | "Lihat Customer Terkait" → **Detail Customer** ([06-customers.md](06-customers.md)) |
| `closed` / `cancelled` | `true` | *(tidak ada tombol aksi — riwayat saja)* |

### Alur "Klaim"
1. Tap "Klaim Project Ini" → `AlertDialog` konfirmasi singkat (project ini bisa direbut partner lain, jelaskan konsekuensinya) → `POST /projects/{id}/claim`.
2. **Respons `200`**: snackbar sukses "Berhasil diklaim, menunggu persetujuan admin", status di layar berubah, tombol berganti jadi "Batalkan Klaim".
3. **Respons `404`** (kalah race — sudah diklaim partner lain barusan, lihat [docs/api/testing/07-projects.md](../api/testing/07-projects.md)): **jangan** tampilkan sebagai error generik. Tampilkan dialog/snackbar spesifik: *"Project ini baru saja diklaim partner lain."*, lalu otomatis kembali ke Papan Project dengan daftar ter-refresh (project itu akan hilang dari sub-tab "Tersedia").
4. **Respons `422`** (melebihi batas klaim bersamaan): tampilkan pesan error dari server apa adanya (sudah menyebutkan angka batasnya) di dialog, jangan biarkan partner mencoba klaim lain sampai batasnya longgar (mis. project lain di-`cancel-claim` atau disetujui/selesai admin).

### Alur "Batalkan Klaim"
`AlertDialog` konfirmasi → `POST /projects/{id}/cancel-claim` → sukses: status kembali `available`, tombol berubah lagi jadi "Klaim Project Ini" kalau partner masih di layar yang sama (edge case, tapi mungkin terjadi kalau partner scroll balik).
