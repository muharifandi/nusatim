# Skenario Test — Leads (Lead & Opportunity)

Sumber: `tests/Feature/Api/LeadApiTest.php` (11 test), `LeadReminderApiTest.php` (3 test), `LeadDocumentApiTest.php` (2 test) — total 16 test, semua hijau.

Lihat penjelasan fitur & contoh payload di [../04-leads.md](../04-leads.md).

## Lead (CRUD, status, timeline)

| # | Skenario | Kondisi Awal | Aksi | Hasil yang Diharapkan |
|---|---|---|---|---|
| 1 | Partner belum `approved` diblokir | Partner `pending_review` | `GET /leads` | `403`. |
| 2 | List cuma menampilkan lead milik sendiri | 1 lead milik partner login, 1 lead milik partner lain | `GET /leads` | `200`. `data` berisi **1** item, cuma lead milik sendiri. |
| 3 | Filter status & pencarian nama bekerja | 1 lead "Budi Santoso" status `new`, 1 lead "Ani Wijaya" status `won` | `GET /leads?status=won` lalu `GET /leads?search=Budi` | Filter status → 1 hasil ("Ani Wijaya"). Filter search → 1 hasil ("Budi Santoso"). |
| 4 | Lead baru berhasil dibuat dengan status default | — | `POST /leads` dengan `name`+`phone` (tanpa `status`) | `201`. `data.status = "new"` (default otomatis, bukan `null`). Tersimpan di database dengan `partner_id` yang benar. |
| 5 | Lihat lead milik partner lain → 404 | Lead dibuat untuk partner B | `GET /leads/{id}` pakai token partner A | `404` (bukan 403 — lihat pola umum di [00-overview.md](00-overview.md)). |
| 6 | Update lead berhasil | Lead ada, milik partner login | `PUT /leads/{id}` dengan `name` baru | `200`. `data.name` sudah berubah. |
| 7 | Ubah status ke `won` otomatis membuat Customer | Lead ada, `estimated_value = 1.000.000` | `PATCH /leads/{id}/status` dengan `status: won` | `200`. `data.status = "won"`. Baris baru muncul di tabel `customers` dengan `lead_id` yang sesuai. |
| 8 | Status tidak valid ditolak | Lead ada | `PATCH /leads/{id}/status` dengan `status: "not-a-real-status"` | `422`. |
| 9 | Hapus lead berhasil | Lead ada, milik partner login | `DELETE /leads/{id}` | `204` (body kosong). Baris di database benar-benar terhapus. |
| 10 | Lead baru otomatis punya entri timeline "created" | Lead baru dibuat | `GET /leads/{id}/activities` | `200`. Ada entri dengan `type: "created"` — **tanpa aplikasi memanggil endpoint apa pun untuk membuatnya**. |
| 11 | Partner bisa tambah catatan manual | Lead ada | `POST /leads/{id}/activities` dengan `body` | `201`. `data.type = "note"`. Tersimpan di tabel `lead_activities`. |

## Reminder (Follow Up & Meeting)

| # | Skenario | Kondisi Awal | Aksi | Hasil yang Diharapkan |
|---|---|---|---|---|
| 12 | Alur CRUD lengkap: create → list → update → delete | Lead ada | `POST` reminder → `GET` list → `PUT` update `note` → `DELETE` | Create `201`. List `200` dengan 1 item. Update `200`, `note` berubah. Delete `204`, baris hilang dari database. |
| 13 | Endpoint "complete" mengisi `completed_at` | Reminder ada, `completed_at` masih `null` | `PATCH /leads/{lead}/reminders/{id}/complete` (tanpa body) | `200`. `data.completed_at` terisi (bukan `null`). |
| 14 | Reminder di lead milik orang lain tidak bisa diakses | Lead milik partner lain | `GET .../reminders` dan `POST .../reminders` pakai token partner login | Keduanya `404`. |

## Dokumen / Proposal

| # | Skenario | Kondisi Awal | Aksi | Hasil yang Diharapkan |
|---|---|---|---|---|
| 15 | Alur lengkap: upload → list → download → delete | Lead ada | `POST` file → `GET` list → `GET .../download` → `DELETE` | Upload `201`, `data.original_name` sesuai yang dikirim. List `200` dengan 1 item. Download `200` (file stream). Delete `204`, baris hilang dari database. |
| 16 | Dokumen di lead milik orang lain tidak bisa diakses | Lead milik partner lain | `GET .../documents` pakai token partner login | `404`. |

## Catatan khusus untuk implementasi Android

- Skenario #7 adalah yang paling penting dipahami tim: **jangan** buat tombol/alur terpisah "Convert to Customer" di aplikasi — cukup ubah status lead jadi `won` lewat UI yang sama dengan perubahan status lainnya (mis. drag-and-drop di Pipeline), Customer akan muncul otomatis. Setelah aksi ini sukses, pertimbangkan langsung mengarahkan user ke detail Customer yang baru terbentuk (butuh query tambahan ke `GET /customers` untuk mencari customer dengan `lead_id` ini, karena response `PATCH .../status` tidak mengembalikan ID customer secara langsung).
- Skenario #10: karena entri timeline "created"/"status_change" dibuat server tanpa aplikasi memintanya, layar timeline harus di-refresh (bukan cuma append lokal) setiap kali balik dari layar edit lead — jangan asumsikan hanya entri "note" manual yang mungkin bertambah.
- Skenario #6 vs #7: `PUT /leads/{id}` **juga bisa** mengubah status (termasuk ke `won`, dengan efek samping sama seperti `PATCH .../status`) — kalau form edit lead di aplikasi punya field status, tidak perlu memanggil dua endpoint berbeda, cukup satu `PUT` saja sudah cukup.
