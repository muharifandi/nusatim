# Skenario Test — Commissions

Sumber: `tests/Feature/Api/CommissionApiTest.php` (4 test, semua hijau).

Lihat penjelasan fitur & contoh payload di [../08-commissions.md](../08-commissions.md).

| # | Skenario | Kondisi Awal | Aksi | Hasil yang Diharapkan |
|---|---|---|---|---|
| 1 | List cuma menampilkan komisi milik sendiri | 1 komisi milik partner login, 1 milik partner lain | `GET /commissions` | `200`. `data` berisi **1** item, dengan `amount` yang benar milik partner login. |
| 2 | Filter status bekerja | 1 komisi `pending`, 1 komisi `approved` (dan `is_bonus: true`) | `GET /commissions?status=approved` | `200`. `data` berisi **1** item, `status = "approved"`. |
| 3 | Detail komisi menyertakan nama customer | Komisi terkait dengan customer "Customer Detail", status `paid` | `GET /commissions/{id}` | `200`. `data.customer_name = "Customer Detail"`, `data.status = "paid"`. |
| 4 | Lihat komisi milik partner lain → 404 | Komisi dibuat untuk partner lain | `GET /commissions/{id}` pakai token partner login | `404`. |

## Catatan khusus untuk implementasi Android

- Tidak ada satu pun endpoint create/update/delete di modul ini — pastikan tidak ada tombol aksi apa pun di layar Commissions selain "lihat detail". Ini murni layar riwayat.
- Skenario #2: kalau UI punya filter/tab per status (Pending / Approved / Paid / dst), gunakan query param `status` — jangan filter di sisi klien dari hasil `GET /commissions` tanpa filter (boros bandwidth kalau riwayat komisi partner sudah banyak, karena endpoint ini dipaginasi).
