# Customers (Customer Management)

Customer adalah lead yang sudah "closing" — record ini **tidak pernah dibuat manual**, selalu muncul otomatis begitu sebuah lead diubah statusnya jadi `won` (lihat [04-leads.md](04-leads.md)), atau begitu klaim project disetujui admin (lihat [07-projects.md](07-projects.md)).

## Cara kerja

- **Tidak ada endpoint `POST /customers`** — memang disengaja, sama seperti panel web (tombol "Buat Customer" memang tidak ada di sana). Kalau aplikasi butuh membuat customer baru, arahnya selalu lewat: buat lead → ubah status jadi `won`.
- Detail customer (`GET /customers/{customer}`) mengembalikan **data gabungan** dari beberapa sumber — customer itu sendiri, lead asalnya (kalau ada), project terkait (kalau berasal dari klaim project), dan komisi terkait (kalau sudah digenerate). Semua digabung supaya aplikasi tidak perlu memanggil banyak endpoint terpisah untuk satu layar detail customer.
- `update_progress` cuma berlaku kalau customer punya `project` terkait (customer yang berasal dari lead biasa, bukan dari klaim project, tidak punya apa pun untuk di-update progress-nya).

---

## `GET /customers`

Daftar customer milik partner yang login, dipaginasi (lihat format pagination di [00-overview.md](00-overview.md)). Query param `page`/`per_page` didukung.

Item di list index **tidak** menyertakan `follow_ups`/`meetings`/`timeline`/`proposal_documents` (field-field berat itu cuma dimuat di endpoint detail) — struktur ringkas:

```json
{
  "data": [
    {
      "id": 201,
      "name": "Budi Santoso",
      "pic_name": null,
      "pic_phone": null,
      "pic_email": null,
      "service_id": 3,
      "service_name": null,
      "project_value": 15000000,
      "payment_status": "unpaid",
      "project": null,
      "commission": null,
      "follow_ups": [],
      "meetings": [],
      "proposal_documents": [],
      "timeline": [],
      "system_activities": [],
      "notes": [],
      "created_at": "2026-07-22T10:15:00.000000Z"
    }
  ]
}
```

> Catatan: field turunan (`follow_ups` dst) tetap muncul di response index, tapi isinya bergantung data — untuk performa terbaik, ambil detail lewat `GET /customers/{customer}` saat pengguna benar-benar membuka layar detail.

---

## `GET /customers/{customer}`

Detail lengkap satu customer.

**Response — `200 OK`**

```json
{
  "data": {
    "id": 201,
    "name": "Budi Santoso",
    "pic_name": "Budi",
    "pic_phone": "081234567890",
    "pic_email": "budi@perusahaan.com",
    "service_id": 3,
    "service_name": "Website Company Profile",
    "project_value": 15000000,
    "payment_status": "partial",
    "project": {
      "id": 44,
      "name": "Website Company Profile PT Maju",
      "status": "in_progress",
      "progress": 60
    },
    "commission": {
      "id": 77,
      "status": "approved",
      "amount": 1500000
    },
    "follow_ups": [
      { "remind_at": "2026-07-25T09:00:00.000000Z", "note": "Follow up pertama", "completed_at": "2026-07-25T09:30:00.000000Z" }
    ],
    "meetings": [
      { "remind_at": "2026-07-28T13:00:00.000000Z", "note": "Presentasi proposal", "completed_at": null }
    ],
    "proposal_documents": [
      { "name": "Proposal Website.pdf", "url": "https://.../api/v1/leads/101/documents/9/download" }
    ],
    "timeline": [
      { "type": "created", "body": "Lead dibuat.", "created_at": "2026-07-20T08:00:00.000000Z" },
      { "type": "status_change", "body": "Status berubah dari new ke won.", "created_at": "2026-07-22T10:15:00.000000Z" }
    ],
    "system_activities": [
      { "type": "created", "body": "Lead dibuat.", "created_at": "2026-07-20T08:00:00.000000Z" }
    ],
    "notes": [
      { "body": "Klien minta revisi warna logo.", "created_at": "2026-07-21T14:00:00.000000Z" }
    ],
    "created_at": "2026-07-22T10:15:00.000000Z"
  }
}
```

Penjelasan field turunan:

| Field | Isi |
|---|---|
| `project` | `null` kalau customer ini murni dari lead biasa (bukan dari klaim project) |
| `commission` | `null` selama komisi belum digenerate server untuk customer ini |
| `follow_ups` / `meetings` | Diambil dari reminder lead asal (lihat [04-leads.md](04-leads.md)) — kosong kalau customer tidak berasal dari lead (murni dari klaim project) |
| `proposal_documents` | Dokumen yang diupload ke lead asal. `url` di sini butuh header `Authorization` untuk diakses, sama seperti endpoint download dokumen lead |
| `timeline` | Gabungan kronologis semua aktivitas lead asal + aktivitas setelah jadi customer |
| `system_activities` | Subset `timeline` — cuma event otomatis (`created`, `status_change`, `document`), tanpa catatan manual |
| `notes` | Subset `timeline` — cuma catatan manual (`type: note`) |

---

## `PUT /customers/{customer}`

Update data customer (bukan data lead asalnya). Semua field opsional.

**Request**:

```json
{
  "pic_name": "Budi",
  "pic_phone": "081234567890",
  "pic_email": "budi@perusahaan.com",
  "payment_status": "paid"
}
```

`payment_status` salah satu dari `unpaid`, `partial`, `paid`.

**Response — `200 OK`**: data customer terbaru (bentuk sama seperti `GET /customers/{customer}`).

---

## `PATCH /customers/{customer}/progress`

Update progress pengerjaan project terkait customer (0–100). **Hanya bisa dipakai kalau customer punya `project` terkait** (lihat field `project` di atas) — kalau `project` bernilai `null`, endpoint ini akan menolak.

**Request**:

```json
{ "progress": 75 }
```

**Response sukses — `200 OK`**: data customer terbaru, `project.progress` sudah berubah.

**Response gagal — `422`** (customer tidak punya project terkait, atau nilai di luar 0–100)

```json
{
  "message": "The progress field must be between 0 and 100. (and 1 more error)",
  "errors": { "progress": ["Customer ini tidak punya project terkait."] }
}
```
