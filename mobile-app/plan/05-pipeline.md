# Pipeline

Satu endpoint untuk render papan kanban sales pipeline — lead yang sudah dikelompokkan per status, siap dipakai langsung untuk tampilan drag-and-drop ala Trello.

## Cara kerja

- Ini murni **tampilan alternatif** dari data yang sama dengan modul [Leads](04-leads.md) — tidak ada data atau aturan bisnis baru di sini.
- Untuk **memindahkan** lead antar kolom (drag-and-drop), panggil `PATCH /leads/{lead}/status` (lihat [04-leads.md](04-leads.md)) dengan status kolom tujuan, lalu refresh papan (atau update state lokal secara optimis lalu sinkron ulang).
- **Beda dari `GET /leads` biasa**: response bukan pagination flat, tapi objek dengan 8 key tetap (satu per status), masing-masing berisi array lead **tanpa pagination** — cocok untuk render semua kolom sekaligus, tapi kalau salah satu partner punya ratusan lead di satu status, pertimbangkan tetap pakai `GET /leads?status=...` (yang dipaginasi) untuk kolom itu.

---

## `GET /pipeline`

**Query params** (semua opsional, filter tanggal berdasarkan `created_at` lead):

| Param | Keterangan |
|---|---|
| `service_id` | Filter produk/layanan |
| `date_from` | Format `YYYY-MM-DD` |
| `date_to` | Format `YYYY-MM-DD` |

**Response — `200 OK`**

```json
{
  "new": [
    {
      "id": 105,
      "name": "Citra Dewi",
      "phone": "081298765432",
      "email": null,
      "service_id": 1,
      "service_name": null,
      "estimated_value": null,
      "status": "new",
      "created_at": "2026-08-01T09:00:00.000000Z",
      "updated_at": "2026-08-01T09:00:00.000000Z"
    }
  ],
  "contacted": [],
  "qualified": [],
  "opportunity": [
    { "...": "objek Lead, sama seperti di GET /leads" }
  ],
  "proposal": [],
  "negotiation": [],
  "won": [
    { "...": "objek Lead" }
  ],
  "lost": []
}
```

Urutan key selalu mengikuti urutan funnel (`new` → `lost`) meskipun sebagian kolom kosong (`[]`) — aman untuk langsung dipetakan ke 8 kolom UI tanpa perlu pengecekan tambahan. Di dalam tiap kolom, lead diurutkan terbaru dulu.
