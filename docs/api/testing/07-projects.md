# Skenario Test — Projects

Sumber: `tests/Feature/Api/ProjectApiTest.php` (6 test, semua hijau).

Lihat penjelasan fitur & contoh payload di [../07-projects.md](../07-projects.md).

| # | Skenario | Kondisi Awal | Aksi | Hasil yang Diharapkan |
|---|---|---|---|---|
| 1 | Papan menampilkan available + milik sendiri, bukan milik orang lain | 1 project `available`, 1 project `pending_approval` milik partner login, 1 project `assigned` milik partner lain | `GET /projects` | `200`. Daftar nama project berisi "Available Project" dan "My Claimed Project", **tidak** berisi "Someone Elses Project". |
| 2 | Klaim project available berhasil | Project berstatus `available` | `POST /projects/{id}/claim` | `200`. `data.status = "pending_approval"`. `partner_id` project di database berubah jadi partner yang login. |
| 3 | Project yang sudah diklaim orang lain jadi tak terlihat | Project sudah diklaim Partner A (`claim()` dipanggil langsung, bukan lewat API) | `POST /projects/{id}/claim` pakai token Partner B | `404` (bukan 409 — begitu klaim A committed, project tidak lagi cocok kriteria "available atau milik B", jadi hilang dari jangkauan B sebelum sempat 409. Lihat penjelasan lengkap di [../07-projects.md](../07-projects.md)). `partner_id` project **tetap** milik Partner A. |
| 4 | Klaim ditolak kalau sudah mencapai batas maksimal | Admin set `max_concurrent_claimed_projects = 1`, partner sudah punya 1 project berstatus `pending_approval` | `POST /projects/{project-baru}/claim` | `422`. |
| 5 | Batalkan klaim sendiri yang masih pending berhasil | Project sudah diklaim partner login, status masih `pending_approval` | `POST /projects/{id}/cancel-claim` | `200`. `data.status` kembali jadi `"available"`. `partner_id` project jadi `null`. |
| 6 | Tidak bisa batalkan klaim milik orang lain / yang sudah diproses | Project `pending_approval` tapi milik partner **lain** | `POST /projects/{id}/cancel-claim` pakai token partner login | `404`. |

## Catatan khusus untuk implementasi Android

- Skenario #3 adalah kasus **race condition** paling penting untuk ditangani dengan baik di UI: kalau tombol "Klaim" ditekan tapi hasilnya `404` (bukan `200`), jangan tampilkan pesan error generik — tampilkan pesan spesifik seperti *"Project ini sudah tidak tersedia, mungkin baru saja diklaim partner lain"*, lalu otomatis refresh daftar project. Perhatikan: kode status yang dikembalikan API di kasus ini adalah **404**, meskipun secara konsep terasa seperti "409 conflict" — implementasi Android harus menangani `404` di endpoint klaim sebagai kasus khusus ini, bukan sebagai "project tidak ditemukan" generik.
- Skenario #4: pesan error dari server (`"Sudah mencapai batas maksimal N project..."`) sudah menyebutkan angka batas sebenarnya — tampilkan pesan ini apa adanya ke pengguna, jangan hardcode angka batas di aplikasi karena admin bisa mengubahnya kapan saja.
- Skenario #5: setelah batal klaim sukses, project itu **langsung available lagi untuk partner lain** — kalau aplikasi menampilkan konfirmasi "Klaim Dibatalkan", jangan biarkan pengguna mengira project itu masih "disimpan" untuknya.
