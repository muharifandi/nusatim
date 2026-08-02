# Layar — Dashboard (Beranda)

API terkait: [docs/api/03-dashboard.md](../api/03-dashboard.md) — satu panggilan `GET /dashboard` mengisi seluruh layar ini.

## Beranda

**Layout** (scrollable, dari atas ke bawah):

1. **Top App Bar**: sapaan "Halo, {nama partner}" di kiri, ikon lonceng notifikasi di kanan (dengan badge titik merah kalau `unread_count > 0`, lihat [12-notifications.md](12-notifications.md)).
2. **Kartu ringkasan keuangan** (paling menonjol — nilai yang paling ingin dilihat partner setiap buka aplikasi): "Saldo Tersedia" (`finance.available_balance`) besar, dengan tombol kecil "Tarik Saldo" langsung ke Form Ajukan Withdrawal ([09-withdrawals.md](09-withdrawals.md)).
3. **Grid statistik aktivitas** (2 kolom × beberapa baris, tiap sel kartu kecil dengan ikon + angka + label): Total Lead, Total Opportunity, Total Customer, Total Project, Follow Up Hari Ini, Meeting Hari Ini. Tap kartu "Follow Up Hari Ini"/"Meeting Hari Ini" → deep link ke Lead List terfilter reminder hari ini (butuh penyesuaian query di sisi klien, karena API dashboard tidak mengembalikan daftar leadnya, cuma angkanya — lihat [docs/api/03-dashboard.md](../api/03-dashboard.md)).
4. **Kartu target penjualan bulan ini** (`finance.sales_target`) — progress bar linear dengan label "Rp{achieved_amount} dari Rp{target_amount} ({achieved_percentage}%)". **Sembunyikan seluruh kartu ini kalau `sales_target` bernilai `null`** (admin belum set target).
5. **Chart pipeline** — donut/pie chart dari objek `pipeline` (8 status), dengan legenda warna sesuai badge status yang sama dipakai di seluruh aplikasi (lihat [00-overview.md](00-overview.md)).
6. **Chart tren closing** — line chart 12 bulan dari `closing_trend`.
7. **Chart tren komisi** — line chart 12 bulan dari `commission_trend`, format sumbu-Y sebagai Rupiah ringkas (`1jt`, `2.5jt`, dst untuk menghemat ruang, bukan `Rp1.000.000` penuh di sumbu chart).
8. **Kartu keuangan sekunder** (baris kecil, kurang menonjol dari #2): Total Komisi, Komisi Pending, Total Withdrawal — cuma informasi tambahan.

**State**:
- Loading pertama: skeleton menyerupai seluruh layout di atas (bukan spinner tunggal — layar ini padat konten, skeleton section-per-section terasa jauh lebih responsif).
- Pull-to-refresh: memuat ulang seluruh `GET /dashboard`.
- Error: kartu error di bagian atas dengan tombol "Coba Lagi", tanpa menghilangkan Bottom Navigation (partner tetap bisa pindah ke tab lain meski Dashboard gagal muat).

**Catatan desain**: karena ini satu response gabungan besar, hindari re-fetch seluruh dashboard hanya karena partner berpindah tab lalu kembali — cache response selama sesi (invalidasi saat pull-to-refresh manual, atau otomatis tiap beberapa menit / saat ada aksi yang jelas-jelas mengubah datanya seperti submit withdrawal).
