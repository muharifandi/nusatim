# Layar — Withdrawals

API terkait: [docs/api/09-withdrawals.md](../api/09-withdrawals.md)

Bagian dari tab **Keuangan** (sub-tab "Withdrawal").

## Daftar Withdrawal

**Layout**:
- Kartu ringkasan saldo di paling atas (sama data dengan kartu di Dashboard, dari `GET /withdrawals/balance`): "Saldo Tersedia" besar + "Minimum Penarikan: Rp{x}" teks kecil di bawahnya.
- `LazyColumn` riwayat pengajuan, tiap item: nominal, badge status (Pending/Disetujui/Ditolak/Dibayar), tanggal pengajuan.
- `FloatingActionButton` "+" → **Form Ajukan Withdrawal**. **Nonaktifkan/sembunyikan FAB** kalau `available_balance < minimum_withdrawal` (cegah partner submit yang pasti gagal — validasi UX proaktif, bukan cuma mengandalkan server menolak).

## Form Ajukan Withdrawal

**Layout**:
- Kartu info di atas: "Saldo Tersedia: Rp{x}" (read-only, dari data yang sama seperti daftar).
- Input Nominal (keyboard numerik, format ribuan otomatis) — tampilkan helper text real-time di bawah field: kalau nominal > saldo tersedia atau < minimum, tampilkan peringatan (warna oranye) **sebelum** submit, tombol "Ajukan" disabled sampai nominal valid.
- Upload Foto KTP (komponen sama seperti di Register/Profil KYC — thumbnail + tap untuk kamera/galeri). **Wajib.**
- Catatan (opsional, multiline).
- Tombol "Ajukan" full-width di bawah.

**Catatan penting**: form ini **tidak** punya field rekening bank — data rekening otomatis diambil dari profil partner saat submit (lihat [docs/api/09-withdrawals.md](../api/09-withdrawals.md)). Tampilkan info rekening tujuan (dari data profil, sudah dimuat di aplikasi) sebagai teks konfirmasi read-only di form ("Dana akan ditransfer ke: BCA - 1234567890 a.n. Budi Santoso") dengan tautan kecil "Ganti rekening" yang membuka [Edit Profil](02-profile.md) — supaya partner tidak bingung kenapa tidak ada field itu di form ini.

**State gagal (`422`)**: tampilkan pesan error server apa adanya di bawah field Nominal (sudah termasuk nominal Rupiah terformat, lihat [docs/api/testing/09-withdrawals.md](../api/testing/09-withdrawals.md)).

**Sukses (`201`)**: snackbar konfirmasi, kembali ke Daftar Withdrawal, item baru muncul di atas dengan status "Pending".

## Detail Withdrawal

**Layout**: kartu ringkasan (nominal besar, badge status), detail rekening yang di-snapshot saat pengajuan (bank/nomor rekening/atas nama — bisa beda dari data profil saat ini kalau partner sudah gonta-ganti rekening setelahnya), catatan (kalau diisi).

- Kartu "Dokumen": thumbnail Foto KTP (selalu ada), thumbnail "Bukti Transfer" **cuma muncul kalau `proof_of_transfer_url` tidak `null`** (baru terisi setelah admin menandai `paid`) — sebelum itu tampilkan placeholder "Belum ada bukti transfer".
- **Kalau `status: rejected`**: kartu merah muda berisi `rejection_reason`.

Tidak ada aksi edit/batalkan di layar ini — sekali diajukan, sepenuhnya di tangan admin (lihat [docs/api/09-withdrawals.md](../api/09-withdrawals.md)).
