# Profile

Data akun partner yang sedang login: biodata, rekening bank, dokumen KYC (foto profil/KTP/NPWP), password, preferensi notifikasi email.

## Cara kerja

- Semua endpoint di grup ini **butuh token**, tapi **tidak butuh status `approved`** — partner yang masih `pending_review`/`rejected`/`suspended` tetap boleh lihat & edit profilnya sendiri, karena mereka perlu bisa memperbaiki data (misal foto KTP buram) sambil menunggu/setelah ditolak.
- Ganti foto profil/KTP/NPWP masing-masing endpoint terpisah (bukan digabung ke `PUT /profile`) karena upload file perlu `multipart/form-data`, sedangkan update biodata pakai JSON biasa.
- **File lama otomatis terhapus dari server** begitu file baru berhasil diupload — tidak perlu (dan tidak bisa) menghapus manual.
- Foto profil/KTP/NPWP adalah dokumen privat — field `*_url` di response selalu mengarah ke endpoint API sendiri (`GET /profile/documents/{type}`), bukan link publik. Endpoint itu tetap butuh header `Authorization` untuk diakses (lihat bagian bawah halaman ini).

---

## `GET /profile`

Ambil data profil partner yang sedang login. Response sama persis dengan `data` pada response login (lihat [01-auth.md](01-auth.md)).

---

## `PUT /profile`

Update biodata, rekening bank, dan preferensi notifikasi. **Semua field opsional** — kirim cuma field yang berubah (partial update).

**Request** — JSON:

```json
{
  "name": "Budi Santoso",
  "email": "budi.baru@example.com",
  "bank_name": "Mandiri",
  "bank_account_number": "9876543210",
  "bank_account_holder": "Budi Santoso",
  "email_notifications_enabled": false
}
```

**Response sukses — `200 OK`**: sama seperti `GET /profile`, dengan data terbaru.

**Response gagal — `422`** (contoh: email sudah dipakai partner lain)

```json
{
  "message": "The email has already been taken.",
  "errors": { "email": ["The email has already been taken."] }
}
```

---

## `POST /profile/photo`, `POST /profile/ktp`, `POST /profile/npwp`

Ketiganya berpola sama — cuma field & path berbeda:

| Endpoint | Field wajib |
|---|---|
| `POST /profile/photo` | `profile_photo` (file image, maks. 4MB) |
| `POST /profile/ktp` | `ktp` (file image, maks. 4MB) |
| `POST /profile/npwp` | `npwp` (file image, maks. 4MB) |

**Request** — `multipart/form-data`, satu field file sesuai tabel di atas.

**Response sukses — `200 OK`**: data profil terbaru (field `*_url` terkait berubah karena path filenya berganti).

---

## `PUT /profile/password`

**Request** — JSON:

```json
{
  "current_password": "password-lama",
  "password": "password-baru123",
  "password_confirmation": "password-baru123"
}
```

**Response sukses — `200 OK`**

```json
{ "message": "Password berhasil diubah." }
```

**Response gagal — `422`** (password lama salah)

```json
{
  "message": "The current password field is required. (and 1 more error)",
  "errors": { "current_password": ["The current password is incorrect."] }
}
```

Setelah ganti password, token yang sedang dipakai **tetap valid** (tidak perlu login ulang) — hanya password untuk login berikutnya yang berubah.

---

## `GET /profile/documents/{type}`

Stream/unduh dokumen KYC milik sendiri. `{type}` salah satu dari `photo`, `ktp`, `npwp`.

Ini endpoint yang dituju oleh `profile_photo_url`/`ktp_url`/`npwp_url` pada response profil — jangan konstruksi URL ini manual, selalu pakai nilai yang dikembalikan server (path storage internal bisa berubah sewaktu-waktu).

**Request**: tanpa body, header `Authorization` wajib (endpoint ini **tidak** bisa dibuka langsung di browser tanpa token, beda dari link Marketing Material).

**Response sukses — `200 OK`**: file mentah (`Content-Type` sesuai jenis file, misal `image/jpeg`) — load langsung ke `ImageView`/`Coil`/`Glide` dengan header Authorization disertakan di request gambar.

**Response gagal — `404 Not Found`**: tipe tidak dikenal, atau partner belum pernah upload dokumen jenis itu (misal NPWP memang opsional saat register).
