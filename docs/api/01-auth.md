# Auth

Registrasi, login, dan logout partner. Ini satu-satunya grup yang punya endpoint publik (register & login) — sisanya butuh token.

## Cara kerja

- **Register** menerima semua data sekaligus dalam satu request (akun, dokumen KYC, rekening bank, persetujuan perjanjian) — beda dari panel web yang membaginya jadi wizard 4 langkah, tapi hasil akhirnya sama: partner baru langsung berstatus `pending_review`, menunggu admin approve. Email konfirmasi otomatis terkirim ke partner.
- **Login** memverifikasi email+password, lalu menerbitkan token baru. Token lama (dari login sebelumnya, device lain) tetap berlaku — tidak ada logout paksa di device lain.
- **Logout** cuma mencabut token yang sedang dipakai request itu sendiri. Kalau partner login di 2 device, logout di device A tidak memengaruhi sesi di device B.
- Tidak ada endpoint "refresh token" — token Sanctum tidak kedaluwarsa otomatis, jadi tidak perlu mekanisme refresh.

---

## `POST /auth/register`

Publik, tidak butuh token.

**Request** — `multipart/form-data` (wajib multipart karena ada file):

| Field | Tipe | Wajib | Keterangan |
|---|---|---|---|
| `name` | string | ✓ | |
| `email` | string | ✓ | Harus unik |
| `password` | string | ✓ | Min. mengikuti aturan default Laravel (min 8 karakter) |
| `password_confirmation` | string | ✓ | Harus sama dengan `password` |
| `profile_photo` | file (image) | ✓ | Maks. 4MB |
| `ktp` | file (image) | ✓ | Maks. 4MB |
| `npwp` | file (image) | — | Maks. 4MB, opsional |
| `bank_name` | string | ✓ | |
| `bank_account_number` | string | ✓ | |
| `bank_account_holder` | string | ✓ | |
| `agreement_accepted` | boolean | ✓ | Harus `true` — mewakili checkbox "saya setuju perjanjian kemitraan" di panel web |

**Response sukses — `201 Created`**

```json
{
  "data": {
    "id": 3,
    "name": "Budi Santoso",
    "email": "budi@example.com",
    "status": "pending_review",
    "level": null,
    "rejection_reason": null,
    "bank_name": "BCA",
    "bank_account_number": "1234567890",
    "bank_account_holder": "Budi Santoso",
    "email_notifications_enabled": true,
    "agreement_accepted_at": "2026-08-01T20:20:35.000000Z",
    "profile_photo_url": "https://.../api/v1/profile/documents/photo",
    "ktp_url": "https://.../api/v1/profile/documents/ktp",
    "npwp_url": null,
    "created_at": "2026-08-01T20:20:36.000000Z"
  }
}
```

Catatan: register **tidak** langsung mengembalikan token — partner harus login setelah register, sama seperti alur di panel web (registrasi ≠ auto-login).

**Response gagal — `422 Unprocessable Content`** (contoh: dokumen tidak dikirim)

```json
{
  "message": "The profile photo field is required. (and 1 more error)",
  "errors": {
    "profile_photo": ["The profile photo field is required."],
    "ktp": ["The ktp field is required."]
  }
}
```

---

## `POST /auth/login`

Publik, tidak butuh token.

**Request** — JSON:

```json
{
  "email": "budi@example.com",
  "password": "password123",
  "device_name": "Samsung Galaxy S23"
}
```

`device_name` opsional (default `"mobile"` kalau tidak dikirim) — dipakai sebagai label token di sisi server, tidak memengaruhi perilaku. Berguna kalau nanti perlu fitur "kelola perangkat login" di masa depan.

**Response sukses — `200 OK`**

```json
{
  "token": "1|mudG6Kbap1iLFdgtJeWVQos3dxzheFHRAR8or7dV242e3e90",
  "partner": {
    "id": 3,
    "name": "Budi Santoso",
    "email": "budi@example.com",
    "status": "approved",
    "...": "field sama seperti response register"
  }
}
```

Simpan `token` untuk dipakai di header `Authorization: Bearer <token>` pada semua request berikutnya. **Cek `partner.status`** — kalau bukan `"approved"`, tampilkan layar status sebelum membuka modul bisnis (lihat [00-overview.md](00-overview.md)).

**Response gagal — `422 Unprocessable Content`** (email tidak terdaftar ATAU password salah — pesannya sengaja tidak membedakan keduanya, demi keamanan)

```json
{
  "message": "The email field is required. (and 1 more error)",
  "errors": {
    "email": ["Email atau password salah."]
  }
}
```

---

## `POST /auth/logout`

Butuh token. Tidak butuh partner `approved` (partner pending pun boleh logout).

**Request**: tanpa body.

**Response sukses — `200 OK`**

```json
{
  "message": "Berhasil logout."
}
```

Setelah ini, token yang tadi dipakai langsung tidak valid — request berikutnya dengan token yang sama akan dapat `401 Unauthorized`. Hapus token dari penyimpanan lokal device setelah menerima response ini.

---

## `GET /auth/me`

Butuh token. Tidak butuh partner `approved`.

Mengembalikan data partner yang sedang login — berguna untuk cek ulang status approval saat aplikasi dibuka lagi (misal setelah token disimpan dari sesi sebelumnya), tanpa perlu login ulang.

**Response sukses — `200 OK`**

```json
{
  "data": {
    "id": 3,
    "name": "Budi Santoso",
    "email": "budi@example.com",
    "status": "approved",
    "...": "field sama seperti response register/login"
  }
}
```
