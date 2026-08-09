# Leads (Lead & Opportunity)

Modul CRM inti: mencatat calon customer (lead), menggerakkannya lewat tahap penjualan (status), menjadwalkan follow up/meeting, mengunggah dokumen/proposal, dan mencatat timeline aktivitas.

## Cara kerja — konsep penting sebelum mulai

**Status lead** adalah salah satu dari 8 nilai tetap, urut dari awal ke akhir funnel:

```
new → contacted → qualified → opportunity → proposal → negotiation → won / lost
```

Aplikasi bebas memindahkan lead ke status mana pun kapan saja (tidak dipaksa berurutan) lewat `PATCH /leads/{lead}/status` atau `PUT /leads/{lead}`.

**Yang terjadi otomatis di server saat status berubah** (tidak perlu dan tidak bisa dipicu manual dari aplikasi):
- Setiap perubahan status tercatat sebagai entri baru di timeline aktivitas lead (`GET /leads/{lead}/activities`).
- Partner otomatis dapat notifikasi in-app (lihat [12-notifications.md](12-notifications.md)).
- **Begitu status jadi `won`, server otomatis membuat record Customer** dari data lead itu (nama, produk, estimasi nilai jadi nilai project). Setelah ini, lead yang bersangkutan akan punya customer terkait, bisa dilihat lewat modul [Customers](06-customers.md). Ini kenapa tidak ada endpoint terpisah "convert lead to customer" — cukup ubah status jadi `won`.
- Begitu status jadi `won`, komisi untuk partner biasanya akan digenerate oleh sistem (dicek lewat modul [Commissions](08-commissions.md)) — bukan sesuatu yang dipicu API ini, murni efek samping server.

**Duplikasi lead tidak dicek otomatis oleh API ini** — kalau partner input nomor telepon yang sama dua kali, keduanya akan tersimpan sebagai lead terpisah (sama seperti perilaku panel web saat ini).

---

## `GET /leads`

Daftar lead milik partner yang login, dipaginasi.

**Query params** (semua opsional):

| Param | Keterangan |
|---|---|
| `status` | Filter status persis, contoh `?status=won` |
| `service_id` | Filter produk/layanan |
| `search` | Cari di `name`, `phone`, `email` (substring match) |
| `page`, `per_page` | Pagination |

**Response — `200 OK`**

```json
{
  "data": [
    {
      "id": 101,
      "name": "Budi Santoso",
      "phone": "081234567890",
      "email": "budi@example.com",
      "service_id": 3,
      "service_name": "Website Company Profile",
      "estimated_value": 15000000,
      "status": "opportunity",
      "created_at": "2026-07-20T08:00:00.000000Z",
      "updated_at": "2026-07-22T10:15:00.000000Z"
    }
  ],
  "links": { "...": "lihat 00-overview.md" },
  "meta": { "...": "lihat 00-overview.md" }
}
```

`service_name` cuma terisi saat detail lead dimuat lewat `GET /leads/{lead}` (yang meng-*eager-load* relasi produk) — di list index, field ini akan `null` untuk menghemat query. Kalau butuh nama produk di daftar juga, minta backend menambahkannya, atau simpan daftar produk terpisah di aplikasi.

---

## `POST /leads`

**Request** — JSON:

```json
{
  "name": "Budi Santoso",
  "phone": "081234567890",
  "email": "budi@example.com",
  "service_id": 3,
  "estimated_value": 15000000
}
```

Cuma `name` dan `phone` yang wajib. `status` boleh dikirim manual (nilai valid = salah satu dari 8 status di atas), tapi kalau tidak dikirim otomatis jadi `"new"`.

**Response sukses — `201 Created`**: bentuk sama seperti satu item di `GET /leads`.

**Response gagal — `422`**

```json
{
  "message": "The name field is required. (and 1 more error)",
  "errors": {
    "name": ["The name field is required."],
    "phone": ["The phone field is required."]
  }
}
```

---

## `GET /leads/{lead}`

Detail satu lead (termasuk `service_name`). **404** kalau ID tidak ada, atau ada tapi bukan milik partner yang login.

---

## `PUT /leads/{lead}`

Update field lead, termasuk `status` kalau mau (semua field opsional — partial update). Perilaku sama seperti form edit lead di panel web.

**Request** contoh (update sebagian):

```json
{
  "estimated_value": 18000000,
  "status": "proposal"
}
```

**Response — `200 OK`**: data lead terbaru.

---

## `PATCH /leads/{lead}/status`

Endpoint ringan khusus ganti status saja — cocok dipakai untuk interaksi cepat seperti drag-and-drop di papan Pipeline (lihat [05-pipeline.md](05-pipeline.md)) tanpa perlu mengirim seluruh field lead.

**Request**:

```json
{ "status": "negotiation" }
```

**Response sukses — `200 OK`**: data lead terbaru.

**Response gagal — `422`** (status tidak dikenal):

```json
{
  "message": "The selected status is invalid.",
  "errors": { "status": ["The selected status is invalid."] }
}
```

---

## `DELETE /leads/{lead}`

Hapus lead. **`204 No Content`** (body kosong) kalau berhasil, **`404`** kalau bukan milik sendiri/tidak ada.

---

## Reminder (Follow Up & Meeting)

Setiap lead bisa punya banyak reminder bertipe `follow_up` atau `meeting`. Ini yang muncul di `activity.follow_ups_today`/`meetings_today` pada [Dashboard](03-dashboard.md), dan yang jadi dasar field `follow_ups`/`meetings` pada [Customer](06-customers.md) setelah lead jadi `won`.

### `GET /leads/{lead}/reminders`

Response:

```json
{
  "data": [
    {
      "id": 55,
      "lead_id": 101,
      "type": "follow_up",
      "remind_at": "2026-08-05T09:00:00.000000Z",
      "note": "Telepon untuk konfirmasi budget.",
      "completed_at": null
    }
  ]
}
```

### `POST /leads/{lead}/reminders`

**Request**:

```json
{
  "type": "meeting",
  "remind_at": "2026-08-10T13:00:00",
  "note": "Presentasi proposal ke tim procurement."
}
```

`type` wajib salah satu dari `follow_up`/`meeting`. `remind_at` wajib, format tanggal apa pun yang bisa di-parse (disarankan ISO 8601). `note` opsional.

**Response — `201 Created`**: bentuk sama seperti satu item di atas.

### `PUT /leads/{lead}/reminders/{reminder}`

Update sebagian field (semua opsional) — termasuk cara paling umum untuk mengubah `note`/`remind_at`.

### `PATCH /leads/{lead}/reminders/{reminder}/complete`

Tandai reminder sudah ditindaklanjuti — cara tercepat untuk ini, tanpa perlu kirim body apa pun.

**Request**: tanpa body.

**Response — `200 OK`**: reminder dengan `completed_at` terisi timestamp sekarang.

### `DELETE /leads/{lead}/reminders/{reminder}`

`204 No Content` kalau berhasil.

---

## Dokumen / Proposal Lead

Upload berkas terkait lead (proposal, brosur yang dikirim ke calon customer, dsb). File tersimpan di storage privat — hanya bisa diunduh lewat endpoint API sendiri.

### `GET /leads/{lead}/documents`

```json
{
  "data": [
    {
      "id": 9,
      "lead_id": 101,
      "original_name": "Proposal Website.pdf",
      "download_url": "https://.../api/v1/leads/101/documents/9/download",
      "created_at": "2026-07-21T11:00:00.000000Z"
    }
  ]
}
```

### `POST /leads/{lead}/documents`

**Request** — `multipart/form-data`:

| Field | Wajib | Keterangan |
|---|---|---|
| `file` | ✓ | Maks. 10MB, tipe file bebas |
| `original_name` | — | Nama tampilan; kalau kosong, dipakai nama file asli dari perangkat |

**Response — `201 Created`**: bentuk sama seperti satu item di atas.

### `GET /leads/{lead}/documents/{document}/download`

Stream/unduh file. Butuh header `Authorization` (bukan link publik). Response berupa file mentah dengan `Content-Type` sesuai jenis file.

### `DELETE /leads/{lead}/documents/{document}`

`204 No Content` kalau berhasil.

---

## Timeline & Catatan (Activities)

Riwayat kronologis suatu lead — **sebagian besar entrinya dibuat otomatis oleh server** (saat lead dibuat, saat status berubah), aplikasi cuma bisa menambah **catatan manual** (`type: "note"`), tidak bisa membuat entri jenis lain secara langsung.

### `GET /leads/{lead}/activities`

```json
{
  "data": [
    {
      "id": 301,
      "lead_id": 101,
      "type": "status_change",
      "body": "Status berubah dari new ke contacted.",
      "created_at": "2026-07-22T10:15:00.000000Z"
    },
    {
      "id": 300,
      "lead_id": 101,
      "type": "created",
      "body": "Lead dibuat.",
      "created_at": "2026-07-20T08:00:00.000000Z"
    }
  ]
}
```

Nilai `type` yang mungkin muncul: `created`, `status_change`, `document` (otomatis, bukan dari aplikasi), `note` (satu-satunya yang bisa dibuat lewat `POST` di bawah). Diurutkan terbaru dulu.

### `POST /leads/{lead}/activities`

**Request**:

```json
{ "body": "Sudah dihubungi via WhatsApp, menunggu balasan." }
```

**Response — `201 Created`**: entri baru dengan `type: "note"`.
