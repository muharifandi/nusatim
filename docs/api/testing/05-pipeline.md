# Skenario Test — Pipeline

Sumber: `tests/Feature/Api/LeadPipelineApiTest.php` (3 test, semua hijau).

Lihat penjelasan fitur & contoh payload di [../05-pipeline.md](../05-pipeline.md).

| # | Skenario | Kondisi Awal | Aksi | Hasil yang Diharapkan |
|---|---|---|---|---|
| 1 | Lead dikelompokkan per status, cuma milik sendiri | 2 lead `new` + 1 lead `won` milik partner login, 1 lead `new` milik partner lain | `GET /pipeline` | `200`. Key `new` berisi **2** item (bukan 3). Key `won` berisi **1** item. Key `lost` berisi array kosong `[]`. |
| 2 | Filter tanggal bekerja | 1 lead dibuat 10 hari lalu, 1 lead dibuat hari ini | `GET /pipeline?date_from=<kemarin>` | `200`. Key `new` cuma berisi **1** item (yang dibuat hari ini). |
| 3 | Pindah kolom (drag-and-drop) lewat endpoint status lead | Lead berstatus `new` | `PATCH /leads/{id}/status` dengan `status: contacted`, lalu `GET /pipeline` lagi | Update status → `200`, `data.status = "contacted"`. Papan setelah refresh: key `contacted` berisi 1 item, key `new` berisi 0. |

## Catatan khusus untuk implementasi Android

- Skenario #3 adalah pola drag-and-drop yang benar: **tidak ada** endpoint `POST /pipeline/move` — aplikasi memanggil `PATCH /leads/{lead}/status` (sama seperti modul [Leads](../04-leads.md)), lalu **refresh ulang papan** (`GET /pipeline`) atau update state lokal secara optimis (pindahkan card ke kolom baru di UI duluan, lalu sinkronkan/rollback kalau request gagal).
- Kolom kosong tetap muncul sebagai key dengan array kosong (`[]`), **bukan** key yang hilang dari response — aman untuk selalu me-render 8 kolom tanpa perlu pengecekan `containsKey` di sisi Android.
