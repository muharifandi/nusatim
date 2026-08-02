# Layar — Support Tickets

API terkait: [docs/api/11-support-tickets.md](../api/11-support-tickets.md)

Diakses lewat tab **Profil** → "Support Ticket".

## Daftar Tiket

**Layout**: `LazyColumn`, tiap item (`Card`): subjek, badge status (Open/In Progress/Resolved/Closed), tanggal dibuat, cuplikan 1 baris deskripsi. `FloatingActionButton` "+" → **Form Buat Tiket**.

**State kosong**: "Belum ada tiket bantuan." + tombol "Buat Tiket" (sama aksi dengan FAB).

## Form Buat Tiket

**Layout**: form sederhana — Subjek (single line), Deskripsi (multiline, beberapa baris terlihat sekaligus). Tombol "Kirim" full-width.

**Sukses (`201`)**: snackbar konfirmasi, kembali ke Daftar Tiket, item baru muncul di atas dengan status "Open".

## Detail Tiket

**Layout**: kartu subjek + badge status di atas, kartu deskripsi (isi laporan awal partner). **Kalau `resolution_note` tidak `null`**, tampilkan kartu terpisah "Jawaban Admin" (visual dibedakan, mis. latar `secondaryContainer`) berisi catatan penyelesaian — sembunyikan sepenuhnya kartu ini kalau belum ada isinya (lihat [docs/api/testing/11-support-tickets.md](../api/testing/11-support-tickets.md)).

Tidak ada aksi apa pun di layar ini (tidak bisa edit/hapus/balas) — murni tampilan status. Pertimbangkan menampilkan catatan kecil di bawah: "Butuh info tambahan? Buat tiket baru." mengarah ke Form Buat Tiket, karena modul ini tidak mendukung percakapan lanjutan dalam satu tiket (lihat [docs/api/11-support-tickets.md](../api/11-support-tickets.md)).
