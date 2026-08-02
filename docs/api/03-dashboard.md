# Dashboard

Satu endpoint yang menggabungkan semua ringkasan yang tampil di halaman Dashboard panel web partner: statistik aktivitas, statistik keuangan, breakdown pipeline per status, dan tren 12 bulan terakhir.

## Cara kerja

- Ini **satu payload gabungan**, dirancang supaya aplikasi mobile cukup 1 kali request untuk render seluruh layar dashboard (bukannya harus memanggil banyak endpoint kecil terpisah).
- Semua angka dihitung dari query yang **persis sama** dengan yang dipakai widget dashboard di panel web — jadi angka yang ditampilkan di app mobile akan selalu match dengan yang ditampilkan di browser, real-time (tidak ada cache terpisah).
- Butuh partner berstatus `approved`.

---

## `GET /dashboard`

**Request**: tanpa body/parameter.

**Response sukses — `200 OK`**

```json
{
  "activity": {
    "total_leads": 24,
    "total_opportunities": 5,
    "total_customers": 12,
    "total_projects": 3,
    "available_projects": 8,
    "follow_ups_today": 2,
    "meetings_today": 1
  },
  "finance": {
    "total_project_value": 45000000,
    "total_commission": 4500000,
    "pending_commission": 800000,
    "available_balance": 2200000,
    "total_withdrawn": 1500000,
    "sales_target": {
      "target_amount": 50000000,
      "achieved_amount": 45000000,
      "achieved_percentage": 90.0
    }
  },
  "pipeline": {
    "new": 6,
    "contacted": 4,
    "qualified": 3,
    "opportunity": 5,
    "proposal": 2,
    "negotiation": 1,
    "won": 12,
    "lost": 3
  },
  "closing_trend": {
    "labels": ["Sep 2025", "Okt 2025", "Nov 2025", "Des 2025", "Jan 2026", "Feb 2026", "Mar 2026", "Apr 2026", "Mei 2026", "Jun 2026", "Jul 2026", "Agu 2026"],
    "data": [1, 0, 2, 3, 1, 0, 2, 1, 0, 1, 1, 0]
  },
  "commission_trend": {
    "labels": ["Sep 2025", "Okt 2025", "Nov 2025", "Des 2025", "Jan 2026", "Feb 2026", "Mar 2026", "Apr 2026", "Mei 2026", "Jun 2026", "Jul 2026", "Agu 2026"],
    "data": [500000, 0, 900000, 1200000, 400000, 0, 700000, 300000, 0, 400000, 100000, 0]
  }
}
```

Penjelasan tiap bagian:

| Bagian | Penjelasan |
|---|---|
| `activity.total_opportunities` | Jumlah lead dengan status `opportunity`, `proposal`, atau `negotiation` — bukan angka mentah "opportunity" saja |
| `activity.available_projects` | Jumlah project yang berstatus `available` **secara global** (bukan cuma milik partner ini) — angka yang sama untuk semua partner |
| `activity.follow_ups_today` / `meetings_today` | Reminder lead milik partner ini yang jatuh tempo hari ini dan belum ditandai selesai |
| `finance.available_balance` | Saldo yang bisa ditarik lewat modul Withdrawal (jumlah komisi berstatus `approved`) |
| `finance.sales_target` | `null` kalau admin belum mengatur target penjualan bulan berjalan untuk partner ini — tangani null di UI (misal sembunyikan progress bar target) |
| `pipeline` | Objek dengan 8 key tetap (`new`, `contacted`, `qualified`, `opportunity`, `proposal`, `negotiation`, `won`, `lost`), masing-masing jumlah lead di status itu — cocok langsung untuk render pie/bar chart pipeline |
| `closing_trend` / `commission_trend` | Selalu 12 elemen (`labels` dan `data` sejajar index-nya), 12 bulan terakhir dari bulan berjalan mundur ke belakang — cocok langsung untuk render line chart |

`labels` di `closing_trend`/`commission_trend` sudah dalam Bahasa Indonesia terformat (`"Jul 2026"`) — kalau aplikasi butuh format lain untuk lokalisasi, ambil datanya dari index array (index 11 = bulan berjalan, index 0 = 11 bulan lalu) dan format ulang di sisi client.
