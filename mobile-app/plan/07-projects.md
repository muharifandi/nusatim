# Projects (Project Board)

Papan project yang bisa "diambil" (diklaim) partner — beda dari Lead (yang partner cari sendiri), Project dibuat admin dan tersedia untuk semua partner rebutan sampai ada yang berhasil klaim.

## Cara kerja

### Alur status project

```
available → (klaim) → pending_approval → (admin setujui) → assigned → in_progress → closed
                            │
                            └─ (admin tolak, atau partner batalkan) → available lagi
```

- `available`: siapa saja boleh klaim.
- `pending_approval`: sudah diklaim salah satu partner, menunggu admin approve/reject. **Aplikasi mobile tidak bisa approve/reject** — itu murni wewenang admin di panel web.
- `assigned`/`in_progress`: klaim disetujui, project resmi jadi milik partner itu. Begitu ini terjadi, server otomatis membuat record [Customer](06-customers.md) dari project ini (progress project bisa di-update lewat `PATCH /customers/{customer}/progress`, **bukan** lewat endpoint di modul ini).
- `closed`/`cancelled`: selesai/dibatalkan admin.

### Siapa bisa lihat project apa

Endpoint `GET /projects` dan `GET /projects/{project}` menampilkan gabungan dari dua hal:
1. **Semua** project berstatus `available` (bisa dilihat semua partner, siapa saja boleh coba klaim), DAN
2. Project **apa pun** (status apa pun) yang **pernah diklaim partner yang login** — supaya partner bisa memantau riwayat project miliknya sendiri (termasuk yang sudah `closed`).

Konsekuensinya: begitu project X diklaim partner lain (bukan yang login), project itu **hilang** dari daftar `GET /projects` milik partner yang login (karena sudah bukan `available` lagi dan bukan miliknya) — bukan bug, memang begitu perilakunya, sama seperti papan project di panel web.

### Race condition saat klaim

Kalau dua partner menekan tombol "Klaim" di project yang sama nyaris bersamaan, server menjamin **cuma satu yang berhasil** lewat operasi database atomik. Partner yang kalah race akan dapat response gagal (lihat di bawah) — tampilkan pesan yang jelas, jangan retry otomatis.

Ada juga **batas jumlah project yang boleh diklaim bersamaan per partner** (diatur admin, bisa berubah-ubah) — kalau partner sudah mencapai batas itu, klaim project baru akan ditolak dengan pesan spesifik.

---

## `GET /projects`

Dipaginasi. Tidak ada filter query param di endpoint ini (beda dari Leads).

**Response — `200 OK`**

```json
{
  "data": [
    {
      "id": 44,
      "name": "Website Company Profile PT Maju",
      "description": "Pembuatan website 5 halaman + hosting 1 tahun.",
      "service_id": 3,
      "service_name": "Website Company Profile",
      "budget": 20000000,
      "location": "Jakarta",
      "deadline": "2026-09-30",
      "difficulty": "medium",
      "commission_value": 2000000,
      "status": "available",
      "progress": null,
      "is_mine": false,
      "claimed_at": null
    }
  ]
}
```

`is_mine`: `true` kalau `partner_id` project ini sama dengan partner yang login — pakai field ini untuk menentukan tombol mana yang ditampilkan (lihat tabel di bawah), jangan bandingkan manual dengan ID partner.

| `status` | `is_mine` | Tombol yang masuk akal ditampilkan |
|---|---|---|
| `available` | `false` | "Klaim Project" |
| `pending_approval` | `true` | "Batalkan Klaim" |
| `pending_approval` | `false` | *(seharusnya tidak pernah muncul — lihat catatan di atas)* |
| `assigned` / `in_progress` | `true` | Tautan ke detail customer terkait (lihat [06-customers.md](06-customers.md)) |
| `closed` / `cancelled` | `true` | Tidak ada aksi, tampilan riwayat saja |

---

## `GET /projects/{project}`

Detail satu project. `404` kalau tidak ada, atau ada tapi tidak `available` **dan** bukan milik partner yang login (lihat aturan visibilitas di atas).

---

## `POST /projects/{project}/claim`

**Request**: tanpa body.

**Response sukses — `200 OK`**: data project, `status` sudah jadi `"pending_approval"`, `is_mine: true`.

**Response gagal — `409 Conflict`** (kalah race, partner lain sudah lebih dulu klaim)

```json
{ "message": "Project ini baru saja diklaim partner lain." }
```

Tindak lanjut yang disarankan: refresh daftar project (project ini akan hilang dari list karena sudah bukan `available` dan bukan milik partner yang login).

**Response gagal — `422 Unprocessable Content`** (sudah mencapai batas klaim bersamaan)

```json
{
  "message": "Sudah mencapai batas maksimal 3 project yang diklaim bersamaan.",
  "errors": { "project": ["Sudah mencapai batas maksimal 3 project yang diklaim bersamaan."] }
}
```

Angka batasnya (`3` di contoh ini) diatur admin dan bisa berbeda-beda — selalu baca dari pesan error, jangan di-hardcode di aplikasi.

**Response gagal — `404 Not Found`**: project tidak ada, atau sudah tidak `available` lagi (misal sudah diklaim partner lain sebelum aplikasi sempat refresh — lihat penjelasan visibilitas di atas, ini beda kasus dari 409).

---

## `POST /projects/{project}/cancel-claim`

Cuma bisa dipakai untuk project **milik sendiri** yang masih berstatus `pending_approval` (belum diproses admin). Kalau klaim sudah disetujui/ditolak admin, endpoint ini tidak lagi berlaku.

**Request**: tanpa body.

**Response sukses — `200 OK`**: data project, `status` kembali jadi `"available"`, `is_mine: false`.

**Response gagal — `404 Not Found`**: project bukan milik partner yang login, atau statusnya bukan `pending_approval`.
