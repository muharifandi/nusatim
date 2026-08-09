# Skenario Test — Dashboard

Sumber: `tests/Feature/Api/DashboardApiTest.php` (6 test, semua hijau).

Lihat penjelasan fitur & contoh payload di [../03-dashboard.md](../03-dashboard.md).

| # | Skenario | Kondisi Awal | Aksi | Hasil yang Diharapkan |
|---|---|---|---|---|
| 1 | Partner belum `approved` diblokir | Partner berstatus `pending_review` | `GET /dashboard` | `403`. `status` di body = `pending_review`. |
| 2 | Angka aktivitas & keuangan cuma menghitung data milik partner sendiri | 2 lead milik partner (1 `opportunity`), 1 lead milik partner lain, 1 customer, 2 project (1 available, 1 punya partner), 2 komisi (`pending` & `approved`), 1 withdrawal `paid` | `GET /dashboard` | `200`. `activity.total_leads = 2` (bukan 3 — punya partner lain tidak ikut terhitung). `activity.total_opportunities = 1`. `finance.total_commission = 500000` (jumlah kedua komisi). `finance.pending_commission = 300000`. `finance.available_balance = 200000` (cuma yang `approved`). `finance.total_withdrawn = 150000`. |
| 3 | Reminder hari ini dipisah per tipe | 1 reminder `follow_up` hari ini, 1 reminder `meeting` hari ini, 1 reminder `meeting` besok | `GET /dashboard` | `activity.follow_ups_today = 1`, `activity.meetings_today = 1` (yang besok tidak terhitung). |
| 4 | Persentase target penjualan dihitung dari total nilai project | Customer dengan `project_value = 2.500.000`, target bulan ini `10.000.000` | `GET /dashboard` | `finance.sales_target.achieved_percentage = 25`. |
| 5 | Breakdown pipeline menghitung lead per status | 2 lead `new`, 1 lead `won` (milik partner ini) | `GET /dashboard` | `pipeline.new = 2`, `pipeline.won = 1`, `pipeline.lost = 0`. |
| 6 | Tren 12 bulan selalu lengkap 12 elemen | 1 customer + 1 komisi `approved` senilai `400.000` dibuat bulan ini | `GET /dashboard` | `closing_trend.labels`/`data` masing-masing berjumlah **12**. Total seluruh `closing_trend.data` = 1. Total seluruh `commission_trend.data` = 400000. |

## Catatan khusus untuk implementasi Android

- Skenario #4: kalau partner belum punya target bulan ini (tidak diset admin), `finance.sales_target` seluruhnya `null` — pastikan UI progress bar target menangani kasus ini (sembunyikan section, jangan crash saat parsing).
- Skenario #6: karena `closing_trend`/`commission_trend` **selalu** 12 elemen (bahkan kalau datanya 0 semua), aman untuk langsung dipetakan ke chart library tanpa perlu cek panjang array dulu — tapi tetap tangani kasus semua nilai 0 di sisi visual chart (jangan biarkan sumbu Y kosong/aneh).
