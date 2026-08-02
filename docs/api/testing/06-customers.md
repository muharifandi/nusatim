# Skenario Test — Customers

Sumber: `tests/Feature/Api/CustomerApiTest.php` (6 test, semua hijau).

Lihat penjelasan fitur & contoh payload di [../06-customers.md](../06-customers.md).

| # | Skenario | Kondisi Awal | Aksi | Hasil yang Diharapkan |
|---|---|---|---|---|
| 1 | List cuma menampilkan customer milik sendiri | 1 customer milik partner login, 1 milik partner lain | `GET /customers` | `200`. `data` berisi **1** item. |
| 2 | Lihat customer milik partner lain → 404 | Customer dibuat untuk partner lain | `GET /customers/{id}` pakai token partner login | `404`. |
| 3 | Detail customer menggabungkan data project + komisi + turunan lead | Lead dibuat lalu diubah jadi `won` (Customer auto-terbentuk), customer punya `partner_project_id` terkait, 1 reminder `follow_up`, 1 dokumen lead, 1 komisi `approved` | `GET /customers/{id}` | `200`. `data.project.name` & `.progress` sesuai project. `data.commission.status = "approved"`, `.amount` sesuai. `data.follow_ups` berisi 1 item. `data.proposal_documents` berisi 1 item, `url`-nya persis sama dengan URL download dokumen lead terkait. |
| 4 | Update field customer berhasil | Customer ada | `PUT /customers/{id}` dengan `name` baru + `payment_status: paid` | `200`. Kedua field berubah di response. |
| 5 | Update progress ditolak kalau customer tidak punya project | Customer ada, `partner_project_id` kosong (`null`) | `PATCH /customers/{id}/progress` dengan `progress: 50` | `422`. |
| 6 | Update progress berhasil dan mengubah project terkait | Customer punya `partner_project_id` terkait, progress awal `20` | `PATCH /customers/{id}/progress` dengan `progress: 75` | `200`. `data.project.progress = 75`. Baris `partner_projects` di database ikut berubah jadi `75` (bukan cuma di response). |

## Catatan khusus untuk implementasi Android

- Skenario #5 penting untuk UI: **sembunyikan tombol/slider "Update Progress" di layar detail customer kalau `data.project` bernilai `null`** — jangan andalkan response 422 sebagai satu-satunya penjagaan, karena itu pengalaman pengguna yang buruk (tombol ada tapi selalu gagal).
- Skenario #3: `data.proposal_documents[].url` butuh header `Authorization` yang sama untuk diakses (bukan link publik) — perlakukan sama seperti dokumen KTP/NPWP di [Profile](../02-profile.md), jangan buka lewat `Intent.ACTION_VIEW` browser eksternal tanpa menyertakan token.
- Tidak ada skenario `POST /customers` di daftar ini **karena memang tidak ada endpoint-nya** — pastikan aplikasi tidak menyediakan tombol "Tambah Customer" sama sekali, arahkan selalu lewat alur Lead → status `won` (lihat [04-leads.md](04-leads.md)) atau Project → klaim disetujui (lihat [07-projects.md](07-projects.md)).
