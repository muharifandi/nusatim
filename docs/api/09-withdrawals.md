# Withdrawals

Penarikan komisi yang sudah `approved` (lihat [08-commissions.md](08-commissions.md)) menjadi transfer bank nyata.

## Cara kerja

- **Selalu cek saldo dulu** lewat `GET /withdrawals/balance` sebelum menampilkan form pengajuan — validasi saldo cukup/minimum penarikan tetap dijalankan ulang di server saat submit, tapi menampilkan info ini di awal mencegah partner mengisi form yang pasti gagal.
- Data rekening bank (nama bank, nomor rekening, atas nama) **diambil otomatis dari profil partner** saat pengajuan dibuat (di-*snapshot*, bukan direferensikan live) — aplikasi tidak perlu (dan tidak bisa) mengirim data rekening saat submit withdrawal. Kalau partner mau ganti rekening tujuan, update dulu lewat `PUT /profile` (lihat [02-profile.md](02-profile.md)) **sebelum** submit withdrawal.
- Alur status:

  ```
  pending → approved → paid
      │
      └─ rejected (lihat rejection_reason)
  ```

  Semua transisi status (approve/reject/mark-paid) dilakukan admin di panel web — **tidak ada endpoint di modul ini untuk mengubah status**, partner cuma bisa melihat progress.
- Setelah diajukan, withdrawal **tidak bisa diedit atau dibatalkan** lewat API ini (sama seperti panel web — begitu submit, sepenuhnya di tangan admin).

---

## `GET /withdrawals/balance`

Cek dulu sebelum menampilkan form pengajuan.

**Response — `200 OK`**

```json
{
  "available_balance": 2200000,
  "minimum_withdrawal": 100000
}
```

`minimum_withdrawal` bisa `0` kalau admin tidak mengatur batas minimum.

---

## `GET /withdrawals`

Riwayat pengajuan withdrawal, dipaginasi.

**Response — `200 OK`**

```json
{
  "data": [
    {
      "id": 12,
      "amount": 1500000,
      "bank_name": "BCA",
      "bank_account_number": "1234567890",
      "bank_account_holder": "Budi Santoso",
      "note": "Butuh untuk operasional bulan ini",
      "status": "approved",
      "rejection_reason": null,
      "ktp_url": "https://.../api/v1/withdrawals/12/documents/ktp",
      "proof_of_transfer_url": null,
      "created_at": "2026-07-25T10:00:00.000000Z"
    }
  ]
}
```

`proof_of_transfer_url` (bukti transfer, diunggah admin) baru terisi setelah status jadi `paid` — sebelum itu selalu `null`.

---

## `POST /withdrawals`

**Request** — `multipart/form-data`:

| Field | Wajib | Keterangan |
|---|---|---|
| `amount` | ✓ | Angka, harus ≤ `available_balance` dan ≥ `minimum_withdrawal` |
| `ktp` | ✓ | File image, maks. 4MB |
| `note` | — | Catatan opsional |

**Response sukses — `201 Created`**: bentuk sama seperti satu item di atas, `status: "pending"`.

**Response gagal — `422`** (saldo tidak cukup)

```json
{
  "message": "Saldo tidak cukup. Saldo tersedia: Rp2.200.000",
  "errors": { "amount": ["Saldo tidak cukup. Saldo tersedia: Rp2.200.000"] }
}
```

**Response gagal — `422`** (di bawah minimum)

```json
{
  "message": "Minimum penarikan adalah Rp100.000",
  "errors": { "amount": ["Minimum penarikan adalah Rp100.000"] }
}
```

Kedua pesan itu sudah dalam format siap-tampil (termasuk nominal Rupiah terformat) — bisa langsung ditampilkan sebagai pesan error tanpa perlu diformat ulang, meski tetap disarankan cek field `errors.amount` untuk menampilkannya tepat di bawah input jumlah.

---

## `GET /withdrawals/{withdrawal}`

Detail satu withdrawal. Bentuk sama seperti satu item di atas. `404` kalau bukan milik partner yang login.

---

## `GET /withdrawals/{withdrawal}/documents/{type}`

Stream/unduh dokumen terkait withdrawal. `{type}` salah satu dari `ktp` atau `proof` (bukti transfer). Sama seperti dokumen privat lainnya, butuh header `Authorization` — jangan buka link ini tanpa token.

Gunakan URL dari field `ktp_url`/`proof_of_transfer_url` pada response withdrawal, jangan konstruksi manual.

**Response gagal — `404`**: tipe tidak dikenal, dokumen belum ada (misal `proof` sebelum status `paid`), atau withdrawal bukan milik partner yang login.
