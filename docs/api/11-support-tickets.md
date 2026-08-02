# Support Tickets

Tiket bantuan yang partner ajukan ke tim support/admin — mirip fitur "Hubungi Kami" tapi tercatat dan bisa dilacak statusnya.

## Cara kerja

- Partner cuma bisa **membuat** dan **melihat** tiketnya sendiri. **Tidak ada endpoint update/delete** — sekali dibuat, penyelesaian tiket sepenuhnya di tangan admin (assign ke staff, resolve, close) lewat panel web.
- Alur status:

  ```
  open → in_progress → resolved
                            │
                            └─ (admin bisa reopen) → open
  atau langsung → closed
  ```

- Begitu admin menyelesaikan tiket, field `resolution_note` akan terisi — cek field ini untuk tahu jawaban/solusi dari admin.
- Tidak ada mekanisme percakapan bolak-balik (chat) di dalam satu tiket — ini murni form-dan-jawaban sekali jalan, bukan sistem tiket berbasis thread.

---

## `GET /support-tickets`

Riwayat tiket milik partner yang login, dipaginasi.

**Response — `200 OK`**

```json
{
  "data": [
    {
      "id": 8,
      "subject": "Tidak bisa upload dokumen KTP",
      "description": "Setiap saya coba upload foto KTP, selalu muncul error.",
      "status": "resolved",
      "resolution_note": "Sudah diperbaiki di versi terbaru aplikasi, silakan update dan coba lagi.",
      "created_at": "2026-07-30T08:00:00.000000Z"
    }
  ]
}
```

---

## `POST /support-tickets`

**Request**:

```json
{
  "subject": "Tidak bisa upload dokumen KTP",
  "description": "Setiap saya coba upload foto KTP, selalu muncul error."
}
```

Kedua field wajib. `status` otomatis `"open"`, tidak bisa diatur dari aplikasi.

**Response sukses — `201 Created`**: bentuk sama seperti satu item di atas.

**Response gagal — `422`**

```json
{
  "message": "The subject field is required. (and 1 more error)",
  "errors": {
    "subject": ["The subject field is required."],
    "description": ["The description field is required."]
  }
}
```

---

## `GET /support-tickets/{supportTicket}`

Detail satu tiket. Bentuk sama seperti satu item di atas. `404` kalau tidak ada / bukan milik partner yang login.
