# Layar — Commissions

API terkait: [docs/api/08-commissions.md](../api/08-commissions.md)

Bagian dari tab **Keuangan** (sub-tab "Komisi"). **Seluruhnya read-only** — tidak ada FAB, tidak ada aksi tulis apa pun di modul ini.

## Daftar Komisi

**Layout**:
- Chip filter horizontal-scroll di atas: "Semua", "Pending", "Menunggu Pembayaran Klien", "Disetujui", "Ditolak", "Dibayar" — memetakan ke `?status=`.
- `LazyColumn`, tiap item (`Card`): nama customer, nama produk, nominal komisi (besar, tabular figures), badge status, ikon kecil khusus kalau `is_bonus: true` (mis. ikon bintang dengan label "Bonus").

**State kosong**: "Belum ada komisi." — tanpa CTA (memang tidak ada aksi yang bisa dilakukan partner untuk "membuat" komisi).

## Detail Komisi

**Layout**: kartu ringkasan besar di atas (nominal, badge status), lalu daftar detail (nilai project, nilai invoice, persentase, tipe perhitungan, tanggal dibuat). **Kalau `status: rejected`**, tampilkan kartu khusus warna merah muda berisi `rejection_reason`.

Tidak ada tombol aksi di layar ini sama sekali (Edit/Hapus/dsb tidak ada) — cuma tombol kembali di app bar dan (opsional) tombol "Lihat Customer Terkait" kalau ingin memudahkan navigasi ke [Detail Customer](06-customers.md) terkait (butuh field tambahan atau pencarian sisi klien, karena response komisi cuma punya `customer_name`, bukan `customer_id` yang bisa langsung dipakai — cek dulu ke tim backend kalau navigasi ini dianggap penting).
